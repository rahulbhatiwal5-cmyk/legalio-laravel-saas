let currentStep = 1
let qCurrentStep = 1,
    qTotalSteps = 0
let cCurrentStep = 1,
    cTotalSteps = 0
let questionnaireOutputs = []
let contractOutputs = []
let questionnaireQuestions = []
let contractText = ""
let documentName = ""
let parsedContractData = null
let contractTextItems = []
let documentId = null
let editors = {}
let documentAlreadySaved = false;

let _s4QClipboard   = null;
let _s4SClipboard   = null;
let _s4QSelected    = new Set();
let _s4SSelected    = new Set();
let _s4PreviewTimer = null;


const qidPlaceholderDescriptionMap = {};

//  for Step 6
let selectedFieldIds = null;
let output = '';
let shortDescriptionEditor;
let longDescriptionEditor;
let imageDescriptionEditor;
let faqAnswerEditor;
let record_id = null;
let questionSortable = null
let contractSortable = null
let stateClauseData;

let selectedStateClauses = []
let selectedState = null;
let editingQuestionIndex = null
let editingContractIndex = null

let partiesTemplatesData = [];
let selectedPartiesTemplate = null;

let qidAnswers = {}
let renderedQidInputs = new Set()

const CLAUSE_BATCH_SIZE   = 1000;
const CLAUSE_MAX_PARALLEL = 4; 

let autofillInProgress = false;


let allStandardClausesData = [];        
let aiProcessedClauses = [];           
let standardClausesPanelOpen = false;
let standardClausesScanned = false;
let standardClausesCurrentFilter = 'all';
let standardClausesCurrentSearch = '';
let selectedStandardClauseIds = []; 

/* -------- UTILITY FUNCTIONS -------- */
function csrf() {
    return document.querySelector('meta[name="csrf-token"]').content
}

function buildQIDPlaceholderMapping() {
    const mapping = {};
    questionnaireQuestions.forEach(q => {
        const questionText = q.text.toLowerCase();

        // Extract all placeholders from contract text
        const allPlaceholders = new Set();
        //  Store description context for each placeholder
        const placeholderDescriptions = {};

        contractTextItems.forEach(item => {
            const matches = item.text.match(/\[([A-Z_]+)\]/g);

            if (matches) {
                matches.forEach(match => {
                    const placeholder = match.replace(/[\[\]]/g, '');
                    allPlaceholders.add(placeholder);

                    if (!placeholderDescriptions[placeholder]) {
                        placeholderDescriptions[placeholder] = {
                            fullText: item.text,
                            sectionName: item.section_name,
                            stateClauseTitle: item.state_clause_title,
                            tid: item.tid
                        };
                    }
                });
            }
        });

        // FULLY DYNAMIC MATCHING - NO STATIC KEYWORDS
        allPlaceholders.forEach(placeholder => {
            const placeholderKey = `[${placeholder}]`;
            const placeholderWords = placeholder.toLowerCase().split('_').filter(w => w.length > 0);

            // Calculate match score based on word overlap
            let matchScore = 0;
            let exactMatches = 0;
            let partialMatches = 0;
            const matchedWords = [];

            placeholderWords.forEach(placeholderWord => {
                // Skip common filler words that don't add meaning
                if (isFillerWord(placeholderWord)) {
                    return;
                }

                // Check for exact word match
                const regex = new RegExp(`\\b${escapeRegExp(placeholderWord)}\\b`, 'i');
                if (regex.test(questionText)) {
                    exactMatches++;
                    matchScore += 3;
                    matchedWords.push(placeholderWord);
                }
                // Check for partial match (contains)
                else if (questionText.includes(placeholderWord)) {
                    partialMatches++;
                    matchScore += 1;
                    matchedWords.push(`~${placeholderWord}`);
                }

                // DYNAMIC SEMANTIC SIMILARITY
                const semanticScore = calculateSemanticSimilarity(placeholderWord, questionText);

                if (semanticScore > 0) {
                    matchScore += semanticScore;
                }
            });

            // Calculate match percentage
            const totalWords = placeholderWords.filter(w => !isFillerWord(w)).length;
            const matchPercentage = totalWords > 0 ? (exactMatches + (partialMatches * 0.5)) / totalWords : 0;

            // Only map if we have a good match
            if (matchPercentage >= 0.4 || exactMatches >= 2 || matchScore >= 5) {
                if (!mapping[placeholderKey] || mapping[placeholderKey].score < matchScore) {
                    mapping[placeholderKey] = {
                        qid: q.qid,
                        score: matchScore,
                        matchPercentage: matchPercentage,
                        exactMatches: exactMatches,
                        questionText: q.text,
                        matchedWords: matchedWords,
                        description: placeholderDescriptions[placeholder]
                    };
                }
            }
        });
    });

    //  STORE IN GLOBAL MAPPING WITH DESCRIPTIONS
    Object.entries(mapping).forEach(([placeholder, data]) => {
        qidPlaceholderDescriptionMap[data.qid] = {
            placeholder: placeholder,
            description: data.description,
            questionText: data.questionText,
            score: data.score
        };
    });
    // Convert to simple qid mapping
    const finalMapping = {};
    Object.entries(mapping).forEach(([placeholder, data]) => {
        finalMapping[placeholder] = data.qid;
    });
    return finalMapping;
}

//  CHECK IF WORD IS A COMMON FILLER WORD
function isFillerWord(word) {
    const fillers = ['the', 'a', 'an', 'of', 'for', 'to', 'in', 'on', 'at', 'is', 'are', 'was', 'were', 'be', 'been', 'being'];
    return fillers.includes(word.toLowerCase());
}

//  ESCAPE REGEX SPECIAL CHARACTERS
function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

//  CALCULATE SEMANTIC SIMILARITY DYNAMICALLY (NO STATIC SYNONYMS)
function calculateSemanticSimilarity(word, questionText) {
    let score = 0;

    // 1. Check for common morphological variations
    const variants = generateMorphologicalVariants(word);
    variants.forEach(variant => {
        if (questionText.includes(variant) && variant !== word) {
            score += 0.5;
        }
    });

    // 2. Check for character similarity (Levenshtein-based)
    const questionWords = questionText.split(/\s+/);
    questionWords.forEach(qWord => {
        const cleanQWord = qWord.replace(/[^a-z0-9]/gi, '').toLowerCase();
        if (cleanQWord.length >= 3) {
            const similarity = calculateStringSimilarity(word, cleanQWord);
            if (similarity > 0.7) { // 70% similar
                score += similarity;
            }
        }
    });

    // 3. Check for substring relationships
    questionWords.forEach(qWord => {
        const cleanQWord = qWord.replace(/[^a-z0-9]/gi, '').toLowerCase();
        if (cleanQWord.length >= 4 && word.length >= 4) {
            // Check if one word contains the other
            if (cleanQWord.includes(word) || word.includes(cleanQWord)) {
                score += 0.3;
            }
        }
    });

    return score;
}

//  GENERATE MORPHOLOGICAL VARIANTS DYNAMICALLY
function generateMorphologicalVariants(word) {
    const variants = new Set();
    variants.add(word);

    const lowerWord = word.toLowerCase();

    // Pluralization rules
    if (lowerWord.endsWith('s')) {
        variants.add(lowerWord.slice(0, -1)); // names → name
        if (lowerWord.endsWith('es')) {
            variants.add(lowerWord.slice(0, -2)); // addresses → address
        }
    } else {
        variants.add(lowerWord + 's'); // name → names
        if (lowerWord.endsWith('y') && !['a', 'e', 'i', 'o', 'u'].includes(lowerWord[lowerWord.length - 2])) {
            variants.add(lowerWord.slice(0, -1) + 'ies'); // party → parties
        } else if (lowerWord.endsWith('s') || lowerWord.endsWith('x') || lowerWord.endsWith('z') ||
            lowerWord.endsWith('ch') || lowerWord.endsWith('sh')) {
            variants.add(lowerWord + 'es'); // address → addresses
        }
    }

    // Verb forms (progressive/continuous)
    if (lowerWord.endsWith('ing')) {
        variants.add(lowerWord.slice(0, -3)); // signing → sign
        variants.add(lowerWord.slice(0, -3) + 'e'); // signing → signe
    } else {
        variants.add(lowerWord + 'ing'); // sign → signing
        if (lowerWord.endsWith('e')) {
            variants.add(lowerWord.slice(0, -1) + 'ing'); // lease → leasing
        }
    }

    // Past tense
    if (lowerWord.endsWith('ed')) {
        variants.add(lowerWord.slice(0, -2)); // leased → lease
        variants.add(lowerWord.slice(0, -1)); // signed → signe
    } else {
        variants.add(lowerWord + 'ed'); // lease → leased
        if (lowerWord.endsWith('e')) {
            variants.add(lowerWord + 'd'); // lease → leased
        }
    }

    // Common suffixes
    const suffixes = ['er', 'or', 'ee', 'ist', 'ian', 'ment', 'tion', 'sion', 'ness', 'ity', 'ty'];
    suffixes.forEach(suffix => {
        if (lowerWord.endsWith(suffix)) {
            variants.add(lowerWord.slice(0, -suffix.length));
        }
    });

    // Remove very short variants (likely noise)
    return Array.from(variants).filter(v => v.length >= 2);
}

//  CALCULATE STRING SIMILARITY (LEVENSHTEIN DISTANCE)
function calculateStringSimilarity(str1, str2) {
    const s1 = str1.toLowerCase();
    const s2 = str2.toLowerCase();

    if (s1 === s2) return 1.0;
    if (s1.length === 0 || s2.length === 0) return 0.0;

    // Use Levenshtein distance
    const matrix = [];

    // Initialize matrix
    for (let i = 0; i <= s2.length; i++) {
        matrix[i] = [i];
    }
    for (let j = 0; j <= s1.length; j++) {
        matrix[0][j] = j;
    }

    // Fill matrix
    for (let i = 1; i <= s2.length; i++) {
        for (let j = 1; j <= s1.length; j++) {
            if (s2.charAt(i - 1) === s1.charAt(j - 1)) {
                matrix[i][j] = matrix[i - 1][j - 1];
            } else {
                matrix[i][j] = Math.min(
                    matrix[i - 1][j - 1] + 1, 
                    matrix[i][j - 1] + 1,     
                    matrix[i - 1][j] + 1      
                );
            }
        }
    }

    const distance = matrix[s2.length][s1.length];
    const maxLength = Math.max(s1.length, s2.length);

    return 1 - (distance / maxLength);
}

function replacePlaceholdersWithQIDs(text, qidMapping, clause) {
    let updatedText = text;
    const warnings = [];

    //  Only match square bracket placeholders, ignore parentheses and quotes
    const placeholderPattern = /\[([A-Z_]+)\]/g;
    const foundPlaceholders = new Set();
    let match;

    // Find all placeholders in text (ONLY square brackets)
    while ((match = placeholderPattern.exec(text)) !== null) {
        foundPlaceholders.add(match[0]);
    }

    // Process each placeholder
    foundPlaceholders.forEach(placeholder => {
        const qid = qidMapping[placeholder];

        if (qid) {
            // Direct mapping found - replace with {QID} format
            updatedText = updatedText.replace(
                new RegExp(escapeRegExp(placeholder), 'g'),
                `{${qid}}`
            );
        } else {
            // Try fuzzy matching
            const fuzzyResult = findDynamicFuzzyMatch(placeholder, questionnaireQuestions);

            if (fuzzyResult && fuzzyResult.confidence >= 0.6) {
                // Fuzzy match - replace with {QID} format
                updatedText = updatedText.replace(
                    new RegExp(escapeRegExp(placeholder), 'g'),
                    `{${fuzzyResult.qid}}`
                );
                warnings.push(
                    `${placeholder} fuzzy-matched to ${fuzzyResult.qid} (${Math.round(fuzzyResult.confidence * 100)}% confidence)`
                );
            } else {
                // No match found - replace with blank
                updatedText = updatedText.replace(
                    new RegExp(escapeRegExp(placeholder), 'g'),
                    '__________'
                );
                warnings.push(`${placeholder} not found - replaced with blank`);
            }
        }
    });

    return { updatedText, warnings };
}


function showLoading(elementId) {
    const el = document.getElementById(elementId)
    if (el) {
        el.innerHTML =
            '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Generating with AI...</p></div>'
    }
}

function hideLoading(elementId) {
    const el = document.getElementById(elementId)
    if (el) el.innerHTML = ""
}

function escapeHtml(text) {
    if (text === null || text === undefined) return ''
    const div = document.createElement("div")
    div.textContent = String(text)
    return div.innerHTML
}

function escapeAttribute(text) {
    if (text === null || text === undefined) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function getValidQuestionIds() {
    return questionnaireQuestions.map(q => q.qid)
}

function getNextQuestionId(currentIndex) {
    if (currentIndex < questionnaireQuestions.length - 1) {
        return questionnaireQuestions[currentIndex + 1].qid
    }
    return 'END'
}

/*  SCROLL POSITION MANAGEMENT  */
function saveScrollPosition(containerId) {
    const container = document.getElementById(containerId)
    if (container) {
        return container.scrollTop
    }
    return 0
}

function restoreScrollPosition(containerId, scrollTop) {
    const container = document.getElementById(containerId)
    if (container) {
        setTimeout(() => {
            container.scrollTop = scrollTop
        }, 10)
    }
}

let isEditMode = false
function goToStep(step) {
    if (step > currentStep) {
        if (currentStep === 1 && qTotalSteps === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Step',
                text: 'Please complete questionnaire generation first',
                confirmButtonColor: '#FF6B35',
                confirmButtonText: 'OK'
            });
            return;
        }
        //  Validate contract completion
        if (currentStep === 3 && cTotalSteps === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Step',
                text: 'Please complete contract generation first',
                confirmButtonColor: '#FF6B35',
                confirmButtonText: 'OK'
            });
            return;
        }
        if (step === 6) {
            const docId = documentId || window.documentId || document.getElementById('document_id')?.value;
            if (!docId || docId === '0' || docId === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Document Not Saved',
                    text: 'Please save the document in Step 5 before proceeding to FrontPage',
                    confirmButtonColor: '#FF6B35',
                    confirmButtonText: 'OK'
                });
                return;
            }
            if (docId && !documentId) {
                documentId = parseInt(docId);
                window.documentId = documentId;
            }
        }
    }
    currentStep = step
    document.querySelectorAll(".step-content").forEach((s) => s.classList.remove("active"))
    document.querySelectorAll(".step").forEach((s) => s.classList.remove("active"))
    document.getElementById(`step-${step}`).classList.add("active")
    document.querySelector(`[data-step="${step}"]`).classList.add("active")

    if (step === 2) {
        editingQuestionIndex = null
        renderQuestionEditor()
    } else if (step === 3) {
        const nameInput = document.getElementById('contractName');
        if (nameInput && nameInput.value.trim()) {
            documentName = nameInput.value.trim();
            window.documentName = documentName;
        }
        const previewInput = document.getElementById('contractNamePreview');
        if (previewInput && documentName) {
            previewInput.value = documentName;
        }
    } else if (step === 4) {
        editingContractIndex = null
        parseContractTextItems()
        renderContractEditor()
    } else if (step === 5) {
        if (documentAlreadySaved) setTimeout(_lockSaveButton, 50);
        loadFinalDocument()
    } else if (step === 6) {
        initializeStep6WithDocumentData()
        setTimeout(injectAutofillButtons, 500)

    }
}

async function initializeStep6WithDocumentData() {
    if (!documentId) {
        return
    }

    try {
        const hiddenIdField = document.getElementById('document_id')
        if (hiddenIdField) {
            hiddenIdField.value = documentId
        }

        await initializeCKEditorsInStep6()

    setTimeout(injectAutofillButtons, 300)
    } catch (error) {
    }
}

function goBack() {
    if (currentStep > 1) {
        goToStep(currentStep - 1)
    }
}

//  Initialize CKEditor instances for Step 6
async function initializeCKEditorsInStep6() {
    const textareas = document.querySelectorAll('#finalReviewContainer textarea.form-control')


    for (const textarea of textareas) {
        const editorId = textarea.id

        // Skip if already initialized
        if (editors[editorId]) {
            continue
        }

        // Skip file inputs and hidden inputs
        if (textarea.type === 'file' || textarea.type === 'hidden') {
            continue
        }

        try {
            const editor = await ClassicEditor.create(textarea, {
                toolbar: {
                    items: [
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        'link',
                        '|',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'undo',
                        'redo'
                    ]
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                }
            })

            editors[editorId] = editor
        } catch (error) {
            console.error(` CKEditor initialization error for ${editorId}:`, error)
        }
    }
}

//  Fetch categories via API and populate select
async function loadCategories(selectedIds = []) {
    try {
        const response = await fetch('/admin-dashboard/api/categories', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        const result = await response.json()
        if (!result.success) {
            throw new Error('Failed to load categories')
        }

        const select = document.querySelector('#category_id')
        if (!select) {
            return
        }

        //  Clear existing options
        select.innerHTML = ''

        //  Populate options
        result.data.forEach(category => {

            const option = document.createElement('option')
            option.value = category.id
            option.textContent = category.name

            if (selectedIds.includes(category.id)) {
                option.selected = true
            }

            select.appendChild(option)
        })
        // if ($(select).hasClass('js-select2')) {
        //     $(select).select2({
        //         placeholder: "Select categories"
        //     })
        // }

    } catch (error) {
        console.error(' Failed to load categories:', error)
    }
}

//  Finalize contract - final submission
async function finalizeContract() {
    const result = await Swal.fire({
        title: 'Finalize Document?',
        text: 'This will mark the document as complete and publish it.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Finalize',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#FF6B35'
    })

    if (!result.isConfirmed) {
        return
    }

    const btn = event.target
    const originalText = btn.innerHTML
    btn.disabled = true
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Finalizing...'

    try {
        const form = document.querySelector('#step6MainForm')
        if (!form) {
            throw new Error('Form not found')
        }

        const formData = new FormData(form)

        //  Update CKEditor data
        for (const [id, editor] of Object.entries(editors)) {
            formData.set(id, editor.getData())
        }

        //  Mark as published
        formData.set('published', '1')
        formData.set('id', documentId)

        const response = await fetch('/admin-dashboard/update-document', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf()
            },
            body: formData
        })

        const data = await response.json()

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Document finalized and published successfully!',
                confirmButtonColor: '#FF6B35'
            })

            // Redirect to documents list
            window.location.href = '/admin-dashboard/documents'
        } else {
            throw new Error(data.message || 'Failed to finalize document')
        }

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
        })
    } finally {
        btn.disabled = false
        btn.innerHTML = originalText
    }
}

//  Generate SVG image for Step 6
async function generateSVG() {
    const inputs = document.querySelectorAll('.image_name_input')
    const imageNames = Array.from(inputs).map(input => input.value.trim())

    //  Validate at least one field is filled
    if (imageNames.every(name => !name)) {
        Swal.fire({
            icon: 'warning',
            title: 'Input Required',
            text: 'Please fill at least one image name field.',
            confirmButtonColor: '#FF6B35',
            confirmButtonText: 'OK'
        })
        return
    }

    try {
        const response = await fetch('/admin-dashboard/update-document-image', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf()
            },
            body: JSON.stringify({
                id: documentId,
                image_name: imageNames
            })
        })

        const data = await response.json()

        if (data.success) {
            //  Update image preview with cache bust
            const imgElements = document.querySelectorAll('[id*="document_image"]')
            imgElements.forEach(img => {
                if (img && data.image_url) {
                    img.src = data.image_url + '?t=' + Date.now()
                    img.style.display = 'block'
                    img.style.width = '50%'
                    img.style.height = 'auto'
                }
            })

            //  Close modal properly
            const modalElement = document.getElementById('generateSVG')
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement)
                if (modal) {
                    modal.hide()
                }
            }

            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Document image updated successfully',
                timer: 2000,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#FF6B35'
            })

        } else {
            throw new Error(data.message || 'Failed to generate SVG')
        }

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to generate SVG: ' + error.message,
            confirmButtonColor: '#FF6B35',
            confirmButtonText: 'OK',
            allowOutsideClick: false
        })
    }
}

async function setFieldIdAndOpenModal(field_ids, recordId, document_id) {
    selectedFieldIds = Array.isArray(field_ids) ? field_ids : [field_ids]
    documentId = document_id
    record_id = recordId

    const titleInput = document.getElementById('title')
    const title = titleInput?.value?.trim()

    if (!title) {
        Swal.fire({
            icon: 'warning',
            title: 'Title Required',
            text: 'Please fill in the Document Title before using AI Autofill.',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                titleInput?.focus()
            }
        })
        return
    }

    window.dispatchEvent(new CustomEvent('openAiModal', {
        detail: {
            title: 'AI Modal',
            id: recordId,
            document_id: document_id,
            field_ids: selectedFieldIds
        }
    }))
}


function getOutput() {
    let output = document.getElementById('ai-output').innerHTML
    const decoded = cleanAndParseOutput(output)

    selectedFieldIds.forEach((fieldId) => {
        const targetEl = document.getElementById(fieldId)

        if (!targetEl) {
            return
        }

        if (editors[fieldId]) {
            if (fieldId === 'short_description') {
                editors[fieldId].setData(decoded?.short_description || output)
            } else if (fieldId === 'long_description') {
                editors[fieldId].setData(decoded?.long_description || output)
            } else if (fieldId.includes('img_description')) {
                editors[fieldId].setData(decoded?.short_description || output)
            } else if (fieldId.includes('answer')) {
                editors[fieldId].setData(decoded?.faq_answer || output)
            }
        }
        //  Handle regular input fields
        else if (targetEl.tagName === 'INPUT' || targetEl.tagName === 'TEXTAREA') {
            if (fieldId === 'meta_title') {
                targetEl.value = decoded?.meta_title || output
            } else if (fieldId === 'meta_description') {
                targetEl.value = decoded?.meta_description || output
            } else if (fieldId === 'primary_keywords') {
                targetEl.value = decoded?.primary_keyword || output
            } else if (fieldId === 'secondary_keywords') {
                targetEl.value = decoded?.secondary_keyword || output
            } else if (fieldId.includes('img_heading') || fieldId.includes('question')) {
                targetEl.value = decoded?.title || decoded?.faq_question || output
            } else {
                targetEl.value = output
            }
        }
    })
}


function cleanAndParseOutput(rawOutput) {
    let output = rawOutput.trim()
    output = output.replace(/^(json|```json|```)\s*/i, '')
    output = output.replace(/```$/, '')

    try {
        return JSON.parse(output)
    } catch (e) {
        return null
    }
}

async function manualSaveDocument(event) {
    if (event) event.preventDefault()
    const saveBtn = event.currentTarget
    const originalText = saveBtn.innerHTML
    saveBtn.disabled = true
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...'

    try {
        const form = document.getElementById('step6MainForm')
        if (!form) throw new Error('Form not found')

        const formData = new FormData(form)

        if (!documentId) {
            throw new Error('Document ID is missing. Please complete previous steps first.')
        }
        formData.set('id', documentId)

        let slugValue = document.getElementById('document_slug_hidden')?.value || ''
        if (!slugValue) {
            const editablePostName = document.getElementById('editable_post_name')?.textContent?.trim()
            if (editablePostName) {
                slugValue = editablePostName
            }
        }

        if (slugValue) {
            formData.set('slug', slugValue)
        }
        if (typeof editors !== 'undefined') {
            for (const [id, editor] of Object.entries(editors)) {
                try {
                    formData.set(id, editor.getData())
                } catch (e) {
                    console.warn(`Could not get data from editor ${id}:`, e)
                }
            }
        }

        const priceField = document.getElementById('doc_price')
        if (priceField && priceField.value) {
            formData.set('doc_price', priceField.value)
        }

        formData.delete('faq_title[]')
        formData.delete('faq_answer[]')

        const faqTitles = document.querySelectorAll('input[name="faq_title[]"]')
        const faqAnswers = document.querySelectorAll('textarea[name="faq_answer[]"]')

        faqTitles.forEach((input, index) => {
            if (input.value.trim()) {
                formData.append('faq_title[]', input.value)
            }
        })

        faqAnswers.forEach((textarea, index) => {
            if (textarea.value.trim()) {
                formData.append('faq_answer[]', textarea.value)
            }
        })

        const primaryKeywords = document.getElementById('primary_keywords')
        const secondaryKeywords = document.getElementById('secondary_keywords')

        if (primaryKeywords && primaryKeywords.value) {
            formData.set('primary_keywords', primaryKeywords.value)
        }
        if (secondaryKeywords && secondaryKeywords.value) {
            formData.set('secondary_keywords', secondaryKeywords.value)
        }

        console.log(' Submitting form data:', {
            id: documentId,
            slug: slugValue,
            hasTitle: !!formData.get('title'),
            hasPrice: !!formData.get('doc_price'),
            faqCount: faqTitles.length,
            hasPrimaryKeywords: !!formData.get('primary_keywords'),
            hasSecondaryKeywords: !!formData.get('secondary_keywords')
        })

        const response = await fetch('/admin-dashboard/api/update-document', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })

        const contentType = response.headers.get('content-type')
        if (!contentType || !contentType.includes('application/json')) {
            const htmlText = await response.text()
            throw new Error('Server returned an error page. Please check your data and try again.')
        }

        const data = await response.json()

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Save failed')
        }

        if (data.document_id && !documentId) {
            documentId = data.document_id
            window.documentId = documentId

            const hiddenIdField = document.getElementById('document_id')
            if (hiddenIdField) {
                hiddenIdField.value = documentId
            }
        }
        Swal.fire({
            icon: 'success',
            title: 'Saved!',
            text: 'Document updated successfully',
            timer: 2000,
            showConfirmButton: false
        })
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Save Failed',
            text: error.message || 'An unexpected error occurred',
            confirmButtonColor: '#FF6B35'
        })
    } finally {
        saveBtn.disabled = false
        saveBtn.innerHTML = originalText
    }
}

function togglePriceEdit() {
    const input = document.getElementById('doc_price');
    const btn = document.getElementById('editPriceBtn');
    const isReadonly = input.hasAttribute('readonly');

    if (isReadonly) {
        input.removeAttribute('readonly');
        input.focus();
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-primary');
        btn.title = 'Lock price';
        btn.innerHTML = '<i class="fa-solid fa-lock-open"></i>';
    } else {
        input.setAttribute('readonly', true);
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-secondary');
        btn.title = 'Edit price';
        btn.innerHTML = '<i class="fa-solid fa-pen-to-square" style ></i>';
    }
}


async function initializeStep6WithDocumentData() {
    if (!documentId) {
        return
    }

    try {
        const hiddenIdField = document.getElementById('document_id')
        if (hiddenIdField) {
            hiddenIdField.value = documentId
        }
        const response = await fetch(`/admin-dashboard/get-document/${documentId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrf(),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        if (response.ok) {
            const data = await response.json()

            if (data.success && data.document) {
                const doc = data.document
                if (doc.document_image) {
                    const imgElements = document.querySelectorAll('[id*="document_image"]')
                    imgElements.forEach(img => {
                        img.src = doc.document_image + '?t=' + Date.now()
                        img.style.display = 'block'
                        img.style.width = '50%'
                        img.style.height = 'auto'
                    })
                }
                const editablePostName = document.getElementById('editable_post_name')
                const hiddenSlugField = document.getElementById('document_slug_hidden')

                if (editablePostName && doc.slug) {
                    editablePostName.textContent = doc.slug
                }
                if (hiddenSlugField && doc.slug) {
                    hiddenSlugField.value = doc.slug
                }

                const titleField = document.getElementById('title')
                if (titleField && doc.title) {
                    titleField.value = doc.title
                }
                if (doc.meta_title) {
                    const metaTitleField = document.getElementById('meta_title')
                    if (metaTitleField) metaTitleField.value = doc.meta_title
                }

                if (doc.meta_description) {
                    const metaDescField = document.getElementById('meta_description')
                    if (metaDescField) metaDescField.value = doc.meta_description
                }
                if (doc.primary_keywords) {
                    const primaryField = document.getElementById('primary_keywords')
                    if (primaryField) primaryField.value = doc.primary_keywords
                }
                if (doc.secondary_keywords) {
                    const secondaryField = document.getElementById('secondary_keywords')
                    if (secondaryField) secondaryField.value = doc.secondary_keywords
                }
            }
        }
        await loadCategories()
        await initializeCKEditorsInStep6()
    } catch (error) {
        console.error(' Error initializing Step 6:', error)
    }
}

function triggerStep6AIAutofill(field) {

    showSaveIndicator('saving')

    fetch('/admin-dashboard/all-prompt', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf()
        },
        body: JSON.stringify({
            document_id: documentId,
            field: field
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (field === 'short_description') {
                    const elem = document.getElementById('short_description')
                    if (elem) elem.value = data.content
                } else if (field === 'meta') {
                    const titleElem = document.getElementById('meta_title')
                    const descElem = document.getElementById('meta_description')
                    if (titleElem) titleElem.value = data.meta_title
                    if (descElem) descElem.value = data.meta_description
                } else if (field === 'keywords') {
                    const primaryElem = document.getElementById('primary_keywords')
                    const secondaryElem = document.getElementById('secondary_keywords')
                    if (primaryElem) primaryElem.value = data.primary_keyword
                    if (secondaryElem) secondaryElem.value = data.secondary_keyword
                }

            } else {
                showSaveIndicator('error')
                alert(' AI autofill failed: ' + data.message)
            }
        })
        .catch(error => {
            showSaveIndicator('error')
        })
}

function addStep6ArticleSection() {
    const container = document.getElementById('articleSectionsContainer')
    if (!container) return

    const index = container.children.length

    const newSection = `
        <div class="card card-bordered card-preview mt-3" id="article_section_${index}">
            <div class="card-inner position-relative">
                <button type="button" class="btn btn-sm btn-danger remove-section-btn" 
                        onclick="removeStep6ArticleSection(${index})">
                    <i class="fas fa-times"></i>
                </button>
                <h5>Article Section ${index + 1}</h5>
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="article_title[]" class="form-control" />
                </div>
                <div class="form-group mt-2">
                    <label>Content</label>
                    <textarea name="article_content[]" class="form-control" rows="4"></textarea>
                </div>
            </div>
        </div>
    `

    container.insertAdjacentHTML('beforeend', newSection)
}

function removeStep6ArticleSection(index) {
    Swal.fire({
        title: 'Remove Section?',
        text: 'Are you sure you want to remove this section?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const section = document.getElementById(`article_section_${index}`);
            if (section) {
                section.remove();
            }
        }
    });
}

function addStep6FaqSection() {
    const container = document.getElementById('faqSectionsContainer')
    if (!container) return

    const index = container.children.length + 1

    const newFaq = `
        <div class="faq-item card card-bordered" id="faq_section_${index}">
            <div class="card-inner">

                <!--  FAQ Header -->
                <div class="faq-header d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle text-warning"></i>
                        FAQ #${index}
                    </h6>

                    <button type="button"
                            class="btn btn-sm btn-icon btn-outline-danger"
                            onclick="removeStep6FaqSection(${index})"
                            title="Remove FAQ">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!--  Question -->
                <div class="form-group mb-3">
                    <label class="form-label">Question</label>
                    <input type="text"
                           name="faq_title[]"
                           class="form-control"
                           placeholder="Enter FAQ question">
                </div>

                <!--  Answer -->
                <div class="form-group">
                    <label class="form-label">Answer</label>
                    <textarea name="faq_answer[]"
                              class="form-control"
                              rows="4"
                              placeholder="Enter detailed answer"></textarea>
                </div>

            </div>
        </div>
    `
    container.insertAdjacentHTML('beforeend', newFaq)

    document
        .getElementById(`faq_section_${index}`)
        .scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function removeStep6FaqSection(index) {
    Swal.fire({
        title: 'Remove FAQ?',
        text: 'Are you sure you want to remove this FAQ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const section = document.getElementById(`faq_section_${index}`);
            if (section) {
                section.remove();
            }
        }
    });
}

function toggleStep6Publish() {
    const checkbox = document.getElementById('publish1')
    const hiddenField = document.getElementById('published')

    if (checkbox && hiddenField) {
        const isPublished = checkbox.checked
        hiddenField.value = isPublished ? '1' : '0'

        const viewPageLink = document.querySelector('.view_page')
        if (viewPageLink) {
            if (isPublished && documentId) {
                viewPageLink.href = `/document/${document.getElementById('document_slug_hidden')?.value || ''}`
                viewPageLink.target = '_blank'
                viewPageLink.onclick = null
            } else {
                viewPageLink.href = 'javascript:void(0);'
                viewPageLink.removeAttribute('target')
                viewPageLink.onclick = function () { isNotView(); return false; }
            }
        }
    }
}

function isNotView() {
    Swal.fire({
        icon: 'warning',
        title: 'Document Not Published',
        text: 'This document must be published before it can be viewed. Please check the "Published" toggle and save the document.',
        confirmButtonText: 'OK',
        confirmButtonColor: '#FF6B35'
    });
}

function editStep6Slug() {
    const permalinkDisplay = document.getElementById('permalink_display')
    const permalinkEdit = document.getElementById('permalink_edit')
    const slugInput = document.getElementById('slug_input')
    const editablePostName = document.getElementById('editable_post_name')

    if (!permalinkDisplay || !permalinkEdit || !slugInput || !editablePostName) {
        return
    }

    permalinkDisplay.style.display = 'none'
    permalinkEdit.style.display = 'inline-block'

    const currentSlug = editablePostName.textContent.trim()
    slugInput.value = currentSlug

    slugInput.focus()
    slugInput.select()
}

function saveStep6Slug() {
    const slugInput = document.getElementById('slug_input')
    const slugError = document.getElementById('slug_error')

    if (!slugInput) {
        return
    }

    const newSlug = slugInput.value.trim()

    if (!newSlug) {
        slugError.textContent = 'Slug cannot be empty'
        return
    }

    const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/
    if (!slugRegex.test(newSlug)) {
        slugError.textContent = 'Slug can only contain lowercase letters, numbers, and hyphens'
        return
    }

    slugError.textContent = ''

    slugInput.disabled = true

    fetch('/admin-dashboard/update-document-slug', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf()
        },
        body: JSON.stringify({
            document_id: documentId,
            slug: newSlug
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const editablePostName = document.getElementById('editable_post_name')
                const hiddenSlugField = document.getElementById('document_slug_hidden')

                if (editablePostName) {
                    editablePostName.textContent = newSlug
                }

                if (hiddenSlugField) {
                    hiddenSlugField.value = newSlug
                }

                slugInput.value = newSlug

                cancelStep6Slug()

                Swal.fire({
                    icon: 'success',
                    title: 'Slug Updated',
                    text: 'Permalink has been updated successfully',
                    timer: 2000,
                    showConfirmButton: false
                })
            } else {
                slugError.textContent = data.message || 'Failed to update slug'
                slugInput.disabled = false
            }
        })
        .catch(error => {
            slugError.textContent = 'Failed to update slug. Please try again.'
            slugInput.disabled = false
        })
}


function cancelStep6Slug() {
    const permalinkDisplay = document.getElementById('permalink_display');
    const permalinkEdit = document.getElementById('permalink_edit');
    const slugError = document.getElementById('slug_error');

    if (permalinkDisplay) {
        permalinkDisplay.style.display = 'inline';
    }

    if (permalinkEdit) {
        permalinkEdit.style.display = 'none';
    }

    if (slugError) {
        slugError.textContent = '';
    }
}

window.getDocumentId = () => documentId
function goBack() {
    if (currentStep > 1) {
        goToStep(currentStep - 1)
    }
}

async function submitQuestionnaireStep() {
    if (qCurrentStep === 1) {
        documentName = document.getElementById("contractName").value.trim()
        window.documentName = documentName;
        qTotalSteps = 1;

        if (!documentName) {
            showValidationError("contractName", "contractNameError", "Please enter document name");
            return;
        } else {
            clearValidationError("contractName", "contractNameError");
        }

        updateStepIndicator("qStepIndicator", qCurrentStep, qTotalSteps)
        document.getElementById("qStepIndicator").style.display = "block"
    }

    const promptEl = document.getElementById(`qPrompt${qCurrentStep}`);
    const prompt = promptEl ? promptEl.dataset.prompt.trim() : '';

    if (!prompt) {
        Swal.fire({
            icon: 'error',
            title: 'Prompt missing',
            text: `No prompt found for step ${qCurrentStep}`,
            confirmButtonColor: '#FF6B35'
        });
        return;
    } else {
        clearValidationError("qPrompt", "qPromptError");
    }

    showLoading("qStepOutput")

    try {
        const res = await fetch("/admin-dashboard/document-generator/questionnaire-step", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrf(),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                step: qCurrentStep,
                document_name: documentName,
                prompt: prompt,
                previous_outputs: questionnaireOutputs.filter(output => output !== null),
            }),
        })

        const contentType = res.headers.get('content-type') || ''
        if (!contentType.includes('application/json')) {
            _renderQuestionnaireErrorBanner(
                qCurrentStep,
                'Server Error',
                `Server returned an unexpected response (HTTP ${res.status}).`,
            )
            return
        }

        const data = await res.json()

        if (!data.success) {
            _renderQuestionnaireErrorBanner(
                qCurrentStep,
                'Generation Failed',
                data.message || 'AI failed to generate questionnaire for this step.',
            )
            return
        }

        const output = data.output || ''

        if (!output || output.trim().length < 10) {
            _renderQuestionnaireErrorBanner(
                qCurrentStep,
                'Empty Response',
                'AI returned an empty response. Please try again.',
                ['No content received from AI']
            )
            return
        }

        questionnaireOutputs[qCurrentStep - 1] = {
            step: qCurrentStep,
            prompt: prompt,
            output: data.output,
            timestamp: new Date().toISOString(),
        }

        displayQuestionnaireOutput(data.output, qCurrentStep)

        // CHANGED: Since always 1 step, always show proceed button
        updateQuestionnaireControls()

    } catch (error) {
        _renderQuestionnaireErrorBanner(
            qCurrentStep,
            'Network or Parse Error',
            error.message || 'An unexpected error occurred.',
        )
    }
}


// VALIDATE QUESTIONNAIRE OUTPUT
function _validateQuestionnaireOutput(output) {
    const result = { isValid: false, issues: [] }

    if (!output || output.trim().length < 10) {
        result.issues.push('Output is empty or too short')
        return result
    }
    result.isValid = true
    return result
}

function _objectHasQuestions(obj) {
    if (!obj || typeof obj !== 'object') return false

    if (Array.isArray(obj)) {
        return obj.some(item =>
            item && typeof item === 'object' &&
            (item.label || item.text || item.question || item.Question)
        )
    }

    for (const key in obj) {
        const val = obj[key]
        if (val && typeof val === 'object') {
            if (val.label || val.text || val.question || val.Question) return true
            if (Array.isArray(val) && val.length > 0) {
                if (_objectHasQuestions(val)) return true
            }
            if (key.toLowerCase().includes('question') || key.toLowerCase().includes('questionnaire')) return true
        }
    }

    return false
}

function _renderQuestionnaireErrorBanner(step, title, message, issues = []) {
    const outputDiv = document.getElementById('qStepOutput')
    if (!outputDiv) return

    const issueRows = issues.length > 0
        ? issues.map(i => `
            <li style="margin: 4px 0; padding: 5px 0; border-bottom: 0.5px solid var(--color-border-tertiary);
                font-size: 13px; color: var(--color-text-danger); list-style: disc;">${escapeHtml(i)}</li>
        `).join('')
        : ''

    const infoNote = message || 'An error occurred while generating this step.'

    outputDiv.innerHTML = `
        <div style="border: 1.5px solid var(--color-border-danger); border-radius: var(--border-radius-lg); background: var(--color-background-danger); padding: 18px 20px; margin: 8px 0;">
            <!-- Header -->
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" style="flex-shrink:0;">
                    <circle cx="10" cy="10" r="9" stroke="#E24B4A" stroke-width="1.5"/>
                    <path d="M10 6v5M10 14h.01" stroke="#E24B4A" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span style="font-weight:500; font-size:15px; color:var(--color-text-danger);">
                    Step ${step}: ${escapeHtml(title)}
                </span>
                <span style="margin-left:auto; font-size:11px; color:var(--color-text-tertiary);">
                    Step ${step} of ${qTotalSteps}
                </span>
            </div>

            ${issueRows ? `
                <div style="margin-bottom: 14px;">
                    <div style="font-size: 12px; font-weight: 500; color: var(--color-text-secondary); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Issues detected
                    </div>
                    <ul style="margin: 0; padding-left: 20px;">
                        ${issueRows}
                    </ul>
                </div>
            ` : ''}

            <!-- Info note -->
            <div style="
                background: var(--color-background-primary);
                border-radius: var(--border-radius-md);
                border: 0.5px solid var(--color-border-tertiary);
                padding: 10px 14px;
                font-size: 12px;
                color: var(--color-text-secondary);
                margin-bottom: 16px;
                line-height: 1.6;
            ">
                ${escapeHtml(infoNote)}
            </div>
        </div>
    `
}

// RESET questionnaire step
function resetAndClearQuestionnaireStep(stepNumber) {
    Swal.fire({
        title:             `Reset Step ${stepNumber}?`,
        text:              'This clears the output so you can regenerate from scratch.',
        icon:              'warning',
        showCancelButton:  true,
        confirmButtonColor:'#E24B4A',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, reset it'
    }).then(result => {
        if (!result.isConfirmed) return

        questionnaireOutputs[stepNumber - 1] = null

        const outputDiv = document.getElementById('qStepOutput')
        const actionDiv = document.getElementById('qNextAction')
        if (outputDiv) outputDiv.innerHTML = ''
        if (actionDiv) actionDiv.innerHTML = ''

        updateQuestionnaireControls()

        Swal.fire({
            icon:              'success',
            title:             'Step Reset',
            text:              `Step ${stepNumber} has been cleared. Click Generate to try again.`,
            timer:             2000,
            showConfirmButton: false,
        })
    })
}

function displayQuestionnaireOutput(output, step) {
    const outputDiv = document.getElementById("qStepOutput")
    if (!outputDiv) return

    const looksLikeRawJson = output.trim().startsWith('{') || output.trim().startsWith('[')
    const isTruncated = looksLikeRawJson && !output.trim().endsWith('}') && !output.trim().endsWith(']')

    if (isTruncated) {
        _renderQuestionnaireErrorBanner(
            step,
            'Truncated or Incomplete Response',
            'The AI response appears to be cut off. Click on Reset and try again.',
            ['Response ended mid-JSON (incomplete output)']
        )
        return
    }
    outputDiv.innerHTML = `
        <div class="alert alert-success" style="display:none;">
            <h5>Questionnaire – Step ${step} Output</h5>
            <div class="output-content">${formatAIOutput(output)}</div>
        </div>
    `

    updateQuestionnaireControls()
}

async function improveQuestionnaireWithAI() {
    const feedbackText = document.getElementById('qAiFeedbackText')?.value?.trim()
    if (!feedbackText) {
        Swal.fire({ icon: 'warning', title: 'Feedback Required', text: 'Please enter your feedback before sending.', confirmButtonColor: '#FF6B35' })
        return
    }

    const statusEl = document.getElementById('qAiFeedbackStatus')
    if (statusEl) statusEl.textContent = 'Sending to AI...'

    const currentOutput = questionnaireOutputs[qCurrentStep - 1]
    if (!currentOutput) return

    const promptEl = document.getElementById(`qPrompt${qCurrentStep}`)
    const originalPrompt = promptEl ? promptEl.dataset.prompt.trim() : ''

    const improvePrompt = `${originalPrompt}\n\nPREVIOUS OUTPUT:\n${currentOutput.output}\n\nUSER FEEDBACK (apply these changes):\n${feedbackText}\n\nPlease regenerate the questionnaire incorporating the feedback above. Keep what was good, fix what was requested.`

    showLoading("qStepOutput")

    try {
        const res = await fetch("/admin-dashboard/document-generator/questionnaire-step", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrf(),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                step: qCurrentStep,
                document_name: documentName,
                prompt: improvePrompt,
                previous_outputs: [],
            }),
        })

        const data = await res.json()

        if (!data.success || !data.output || data.output.trim().length < 10) {
            _renderQuestionnaireErrorBanner(qCurrentStep, 'AI Improvement Failed', data.message || 'AI could not improve the questionnaire. Try again.')
            return
        }

        questionnaireOutputs[qCurrentStep - 1] = {
            step: qCurrentStep,
            prompt: improvePrompt,
            output: data.output,
            timestamp: new Date().toISOString(),
        }

        displayQuestionnaireOutput(data.output, qCurrentStep)
        updateQuestionnaireControls()

    } catch (error) {
        _renderQuestionnaireErrorBanner(qCurrentStep, 'Network Error', error.message || 'An unexpected error occurred.')
    }
}

function updateQuestionnaireControls() {
    const submitBtn = document.getElementById("qSubmitBtn")
    const backBtn = document.getElementById("qBackBtn")
    const nextAction = document.getElementById("qNextAction")
    backBtn.classList.add("d-none")

    const hasCurrentOutput = questionnaireOutputs[qCurrentStep - 1]

    if (hasCurrentOutput) {
        submitBtn.classList.add("d-none")
        nextAction.innerHTML = `<button class="btn btn-success" onclick="parseAllQuestions(); goToStep(2);">
            <i class="fas fa-check"></i> Proceed to Edit Questions
        </button>`
    } else {
        submitBtn.classList.remove("d-none")
        submitBtn.innerHTML = `<i class="fas fa-paper-plane"></i> Start Generation`
        nextAction.innerHTML = ""
    }
}

function moveToNextQuestionnaireStep() {
    qCurrentStep++
    // document.getElementById("qPrompt").placeholder = `Enter prompt for step ${qCurrentStep}...`
    document.getElementById("qStepOutput").innerHTML = ""
    updateStepIndicator("qStepIndicator", qCurrentStep, qTotalSteps)
    updateQuestionnaireControls()
}

function goPrevQuestionnaireStep() {
    if (qCurrentStep <= 1) return

    qCurrentStep--

    const prev = questionnaireOutputs[qCurrentStep - 1]
    if (prev) {
        // document.getElementById("qPrompt").value = prev.prompt
        displayQuestionnaireOutput(prev.output, qCurrentStep)
    } else {
        // document.getElementById("qPrompt").value = ""
        document.getElementById("qStepOutput").innerHTML = ""
    }

    updateStepIndicator("qStepIndicator", qCurrentStep, qTotalSteps)
    updateQuestionnaireControls()
    // document.getElementById("qPrompt").placeholder = `Enter prompt for step ${qCurrentStep}...`
}

function formatAIOutput(text) {
    return text
        .split("\n")
        .map((line) => line.trim())
        .filter((line) => line.length > 0)
        .map((line) => `<p>${escapeHtml(line)}</p>`)
        .join("")
}

function parseAllQuestions() {
    questionnaireQuestions = []
    let questionId = 1
    const validOutputs = questionnaireOutputs.filter(output => output !== null);

    validOutputs.forEach((output, outputIndex) => {
        try {
            const jsonOutput = JSON.parse(output.output)

            if (typeof jsonOutput === "object") {
                parseNestedQuestions(jsonOutput)
            }
        } catch (e) {
            const lines = output.output
                .split("\n")
                .map((line) => line.trim())
                .filter((line) => line.length > 5)

            lines.forEach((line, lineIndex) => {
                if (line.match(/^[{}\[\],:"]/)) {
                    return
                }

                if (line.startsWith('{') && line.endsWith('}')) {
                    try {
                        const lineObj = JSON.parse(line)
                        parseQuestionObject(lineObj, `QID${questionId++}`, questionId)
                        return
                    } catch (lineError) {
                        // Continue with text parsing
                    }
                }

                // Extract question text
                const cleanText = line
                    .replace(/^\d+[).\-:\s]*/g, "")
                    .replace(/^["'`]|["'`]$/g, "")
                    .replace(/^[Qq]uestion\s*\d*[:\-]?\s*/i, "")
                    .trim()

                if (cleanText.length > 0 && !cleanText.match(/^[{}[\],:"]/)) {
                    const newQid = `QID${questionId++}`

                    questionnaireQuestions.push({
                        id: questionId - 1,
                        qid: newQid,
                        text: cleanText,
                        type: "text",
                        options: [],
                        required: true,
                        goto: "",
                        userinfo: "",
                        section_name: `Section ${output.step}`,
                    })
                }
            })
        }
    })

    assignAutoGotoFlow()
    fixInvalidGotoReferences()

    //  Check for gaps in QIDs
    const qidNumbers = questionnaireQuestions.map(q => parseInt(q.qid.replace('QID', '')))
    const minQid = Math.min(...qidNumbers)
    const maxQid = Math.max(...qidNumbers)

    if (maxQid - minQid + 1 !== questionnaireQuestions.length) {
        console.warn(` Warning: QID gap detected! Expected ${maxQid - minQid + 1} questions but found ${questionnaireQuestions.length}`)
    }
}

function fixInvalidGotoReferences() {
    const validQids = new Set(questionnaireQuestions.map(q => q.qid))
    validQids.add('END')
    validQids.add('')

    let fixedCount = 0

    questionnaireQuestions.forEach((q, index) => {
        if (q.goto === null || q.goto === undefined) {
            q.goto = ""
        } else {
            q.goto = String(q.goto)
        }

        if (q.goto && !validQids.has(q.goto)) {
            if (index < questionnaireQuestions.length - 1) {
                q.goto = questionnaireQuestions[index + 1].qid
            } else {
                q.goto = "END"
            }
            fixedCount++
        }
    })

    if (fixedCount > 0) {
        console.log(` Fixed ${fixedCount} invalid goto references`)
    }
}

function parseNestedQuestions(obj, parentKey = '') {
    for (const key in obj) {
        const value = obj[key]

        if (value && typeof value === "object") {
            // Check if this is a question object
            if (value.label || value.text || value.question || value.Question) {
                const questionText = value.label || value.text || value.question || value.Question
                const newQid = `QID${questionnaireQuestions.length + 1}`

                parseQuestionObject(value, newQid, questionnaireQuestions.length + 1)
            }
            // Check for common question container keys
            else if (key.toLowerCase().includes('question') ||
                key.toLowerCase().includes('questionnaire') ||
                key === 'items' ||
                key === 'fields') {
                parseNestedQuestions(value, key)
            }
            // Recursively parse nested objects
            else if (!Array.isArray(value)) {
                parseNestedQuestions(value, key)
            }
            // Handle arrays
            else if (Array.isArray(value)) {
                value.forEach((item, index) => {
                    if (item && typeof item === 'object') {
                        parseNestedQuestions(item, `${key}[${index}]`)
                    }
                })
            }
        }
    }
}

function parseQuestionObject(q, qid, id) {
    if (!q) return

    // Extract question text from various possible fields
    const questionText = q.label || q.text || q.question || q.Question || q.title || q.prompt

    if (!questionText || questionText.trim().length === 0) {
        return
    }

    let questionType = "text"
    let options = []

    // Determine question type
    if (q.TYPE || q.type) {
        const typeValue = (q.TYPE || q.type).toString().toUpperCase()
        const typeMap = {
            TEXTFIELD: "text",
            TEXT: "text",
            TEXTAREA: "textarea",
            NUMBERFIELD: "number",
            NUMBER: "number",
            DATEFIELD: "date",
            DATE: "date",
            RADIOBUTTON: "radio",
            RADIO: "radio",
            CHECKBOX: "checkbox",
            DROPDOWN: "select",
            SELECT: "select",
        }
        questionType = typeMap[typeValue] || "text"
    }

    // Parse options
    if (q.options && Array.isArray(q.options) && q.options.length > 0) {
        options = q.options
            .map((opt) => {
                if (typeof opt === "string") {
                    return { label: opt, value: opt }
                }
                return {
                    label: opt.option_label || opt.label || opt.value || opt.option_value || "",
                    value: opt.option_value || opt.value || opt.option_label || opt.label || "",
                }
            })
            .filter((opt) => opt.label && opt.label.length > 0)
    }

    const generatedQid = `QID${questionnaireQuestions.length + 1}`

    questionnaireQuestions.push({
        id: questionnaireQuestions.length + 1,
        qid: generatedQid,
        text: questionText.trim(),
        type: questionType,
        options: options,
        required: q.required !== undefined ? q.required : true,
        goto: "",
        userinfo: q.userinfo || q.help || q.description || "",
        section_name: q.section_name || q.section || q.category || "",
    })
}

function updateStepIndicator(elementId, current, total) {
    const el = document.getElementById(elementId)
    if (el) {
        el.innerHTML = `Step ${current} of ${total}`
    }
}

function assignAutoGotoFlow() {
    questionnaireQuestions.forEach((q, index) => {
        if (!q.goto || q.goto === '') {
            if (index < questionnaireQuestions.length - 1) {
                q.goto = questionnaireQuestions[index + 1].qid
            } else {
                q.goto = 'END'
            }
        }
    })
}


function resetQuestionnaireGeneration() {
    Swal.fire({
        title: 'Reset Current Step?',
        text: `Are you sure you want to reset Step ${qCurrentStep}? This will clear the generated questions from this step only.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, reset it!'
    }).then((result) => {
        if (result.isConfirmed) {
            //  Clear only current step's output
            if (qCurrentStep > 0 && Array.isArray(questionnaireOutputs)) {
                const index = qCurrentStep - 1;
                if (index < questionnaireOutputs.length) {
                    //  Store the step number before clearing
                    const currentStepNum = qCurrentStep;

                    //  Clear the output for this step
                    questionnaireOutputs[index] = null;
                }
            }

            //  Remove questions that were generated from this specific step
            if (qCurrentStep > 0) {
                // Track which questions were from this step by their section_name
                const stepSectionName = `Section ${qCurrentStep}`;

                //  Remove questions from this step only
                const questionsBeforeReset = questionnaireQuestions.length;
                questionnaireQuestions = questionnaireQuestions.filter(q =>
                    q.section_name !== stepSectionName
                );

                const questionsRemoved = questionsBeforeReset - questionnaireQuestions.length;

                //  Fix goto references after removing questions
                if (questionsRemoved > 0) {
                    fixInvalidGotoReferences();
                }
            }

            //  Clear UI elements for current step only
            const promptEl = document.getElementById("qPrompt");
            const outputEl = document.getElementById("qStepOutput");
            const actionEl = document.getElementById("qNextAction");

            if (promptEl) promptEl.value = "";
            if (outputEl) outputEl.innerHTML = "";
            if (actionEl) actionEl.innerHTML = "";

            //  Update controls to reflect current state
            updateQuestionnaireControls();

            //  Show success message with details
            Swal.fire({
                icon: 'success',
                title: 'Step Reset!',
                text: `Step ${qCurrentStep} has been reset. You can now regenerate this step.`,
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

/* -------- STEP 2: COMPACT QUESTION EDITOR WITH DRAG & DROP -------- */
// function renderQuestionEditor() {
//     const container = document.getElementById("questionEditor")

//     if (questionnaireQuestions.length === 0) {
//         container.innerHTML = `<div class="alert alert-warning">
//             No questions generated yet. Please go back and complete the questionnaire generation.
//         </div>`
//         return
//     }

//     container.innerHTML = `<div class="editor-layout" style="display:grid; grid-template-columns:1fr 1fr 340px; gap:16px; height:calc(100vh - 260px); min-height:550px;">
//         <div class="list-panel" style="display:flex; flex-direction:column; height:100%; overflow:hidden; background:white; border-radius:8px; border:1px solid #dee2e6;">
//             <div class="panel-header">
//                 <h6><i class="fas fa-list"></i> Questions (${questionnaireQuestions.length})</h6>
//                 <button class="icon-btn success" onclick="addNewQuestion()" title="Add Question">
//                     <i class="fas fa-plus"></i>
//                 </button>
//             </div>
//             <div class="cards-container" id="questionSortableList" style="flex:1; overflow-y:auto; padding:10px;"></div>
//         </div>
//         <div class="edit-panel" id="questionEditPanel" style="background:white; border-radius:8px; border:1px solid #dee2e6; overflow-y:auto;">
//             <div class="empty-state">
//                 <i class="fas fa-hand-pointer"></i>
//                 <p>Click on a question to edit</p>
//             </div>
//         </div>
//         <div style="display:flex; flex-direction:column; background:white; border-radius:8px; border:1px solid #FF6B35; overflow:hidden; height:100%;">
//             <div style="padding:14px 16px; background:#FF6B35; color:white; font-weight:600; font-size:14px; display:flex; align-items:center; gap:8px; flex-shrink:0;">
//                 <i></i> AI Feedback
//             </div>
//             <div style="padding:14px; flex:1; display:flex; flex-direction:column; gap:12px; overflow-y:auto;">
//                 <p style="font-size:12px; color:#6c757d; margin:0;">
//                     Describe changes you want AI to make to the questionnaire. AI will regenerate the questions based on your feedback.
//                 </p>
//                 <textarea id="qAiFeedbackText" style="flex:1; min-height:220px; padding:10px; border:1px solid #dee2e6; border-radius:6px; font-size:13px; resize:vertical; outline:none; width:100%; box-sizing:border-box;" placeholder="e.g. 'Add more questions about payment terms and late fees'&#10;'Remove questions about personal address'&#10;'Make the questions more specific to residential rental'"></textarea>
//                 <div>
//                     <button onclick="improveQuestionsWithAI()" id="qAiImproveBtn" style="width:100%; padding:10px; background:#FF6B35; color:white; border:none; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
//                         <i class="fas fa-paper-plane"></i> Send to AI
//                     </button>
//                 </div>
//                 <div id="qAiFeedbackStatus" style="font-size:12px; color:#6c757d; min-height:16px;"></div>
//                 <div style="border-top:1px solid #f1f3f5; padding-top:12px;">
//                     <p style="font-size:11px; color:#adb5bd; margin:0;">
//                         <i class="fas fa-info-circle"></i> You can also edit any question directly by clicking it in the list on the left.
//                     </p>
//                 </div>
//             </div>
//         </div>
//     </div>`

//     // container.innerHTML = `<div class="editor-layout" style="display:grid; grid-template-columns:1fr 340px; gap:16px; height:calc(100vh - 260px); min-height:550px;">
//     //     <div class="list-panel" style="display:flex; flex-direction:column; height:100%; overflow:hidden; background:white; border-radius:8px; border:1px solid #dee2e6;">
//     //         <div class="panel-header">
//     //             <h6><i class="fas fa-list"></i> Questions (${questionnaireQuestions.length})</h6>
//     //             <button class="icon-btn success" onclick="addNewQuestion()" title="Add Question">
//     //                 <i class="fas fa-plus"></i>
//     //             </button>
//     //         </div>
//     //         <div class="cards-container" id="questionSortableList" style="flex:1; overflow-y:auto; padding:10px;"></div>
//     //     </div>
//     //     <div style="display:flex; flex-direction:column; background:white; border-radius:8px; border:1px solid #FF6B35; overflow:hidden; height:100%;">
//     //         <div style="padding:14px 16px; background:#FF6B35; color:white; font-weight:600; font-size:14px; display:flex; align-items:center; gap:8px; flex-shrink:0;">
//     //             <i></i> AI Feedback
//     //         </div>
//     //         <div style="padding:14px; flex:1; display:flex; flex-direction:column; gap:12px; overflow-y:auto;">
//     //             <p style="font-size:12px; color:#6c757d; margin:0;">
//     //                 Describe changes you want AI to make to the questionnaire. AI will regenerate the questions based on your feedback.
//     //             </p>
//     //             <textarea id="qAiFeedbackText" style="flex:1; min-height:120px; padding:10px; border:1px solid #dee2e6; border-radius:6px; font-size:13px; resize:vertical; outline:none; width:100%; box-sizing:border-box;" placeholder="e.g. 'Add more questions about payment terms and late fees'&#10;'Remove questions about personal address'&#10;'Make the questions more specific to residential rental'"></textarea>
//     //             <div>
//     //                 <button onclick="improveQuestionsWithAI()" id="qAiImproveBtn" style="width:100%; padding:10px; background:#FF6B35; color:white; border:none; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
//     //                     <i class="fas fa-paper-plane"></i> Send to AI
//     //                 </button>
//     //             </div>
//     //             <div id="qAiFeedbackStatus" style="font-size:12px; color:#6c757d; min-height:16px;"></div>
//     //             <div style="border-top:1px solid #f1f3f5; padding-top:12px;">
//     //                 <p style="font-size:11px; color:#adb5bd; margin:0;">
//     //                     <i class="fas fa-info-circle"></i> You can also edit any question directly by clicking it in the list on the left.
//     //                 </p>
//     //             </div>
//     //         </div>
//     //     </div>
//     // </div>`

//     const listEl = document.getElementById("questionSortableList")

//     questionnaireQuestions.forEach((q, i) => {
//         listEl.innerHTML += renderQuestionCard(q, i)
//     })

//     initQuestionSortable()
// }

function renderQuestionEditor() {
    const container = document.getElementById("questionEditor")

    if (questionnaireQuestions.length === 0) {
        container.innerHTML = `<div class="alert alert-warning">
            No questions generated yet. Please go back and complete the questionnaire generation.
        </div>`
        return
    }

    container.innerHTML = `<div class="editor-layout" style="display:grid; grid-template-columns:repeat(2, 1fr); gap:16px; height:calc(100vh - 260px); min-height:550px;">
        <div class="list-panel" style="display:flex; flex-direction:column; height:100%; overflow:hidden; background:white; border-radius:8px; border:1px solid #dee2e6;">
            <div class="panel-header">
                <h6><i class="fas fa-list"></i> Questions (${questionnaireQuestions.length})</h6>
                <button class="icon-btn success" onclick="addNewQuestion()" title="Add Question">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="cards-container" id="questionSortableList" style="flex:1; overflow-y:auto; padding:10px;"></div>
        </div>
        <div style="display:flex; flex-direction:column; background:white; border-radius:8px; border:1px solid #FF6B35; overflow:hidden; height:100%; max-height:550px;">
            <div style="padding:14px 16px; background:#FF6B35; color:white; font-weight:600; font-size:14px; display:flex; align-items:center; gap:8px; flex-shrink:0;">
                <i></i> AI Feedback
            </div>
            <div style="padding:14px; flex:1; display:flex; flex-direction:column; gap:12px; overflow-y:auto;">
                <p style="font-size:12px; color:#6c757d; margin:0;">
                    Describe changes you want AI to make to the questionnaire. AI will regenerate the questions based on your feedback.
                </p>
                <textarea id="qAiFeedbackText" style="flex:1; min-height:220px; padding:10px; border:1px solid #dee2e6; border-radius:6px; font-size:13px; resize:vertical; outline:none; width:100%; box-sizing:border-box;" placeholder="e.g. 'Add more questions about payment terms and late fees'&#10;'Remove questions about personal address'&#10;'Make the questions more specific to residential rental'"></textarea>
                <div>
                    <button onclick="improveQuestionsWithAI()" id="qAiImproveBtn" style="width:100%; padding:10px; background:#FF6B35; color:white; border:none; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
                        <i class="fas fa-paper-plane"></i> Send to AI
                    </button>
                </div>
                <div id="qAiFeedbackStatus" style="font-size:12px; color:#6c757d; min-height:16px;"></div>
                <div style="border-top:1px solid #f1f3f5; padding-top:12px;">
                    <p style="font-size:11px; color:#adb5bd; margin:0;">
                        <i class="fas fa-info-circle"></i> Click the edit icon on any question card to edit it directly.
                    </p>
                </div>
            </div>
        </div>
    </div>`

    const listEl = document.getElementById("questionSortableList")

    questionnaireQuestions.forEach((q, i) => {
        listEl.innerHTML += renderQuestionCard(q, i)
    })

    initQuestionSortable()
}

async function improveQuestionsWithAI() {
    const feedbackEl = document.getElementById('qAiFeedbackText')
    const feedbackText = feedbackEl?.value?.trim()

    if (!feedbackText) {
        Swal.fire({
            icon: 'warning',
            title: 'Feedback Required',
            text: 'Please describe what changes you want AI to make.',
            confirmButtonColor: '#FF6B35'
        })
        return
    }

    const btn = document.getElementById('qAiImproveBtn')
    const statusEl = document.getElementById('qAiFeedbackStatus')
    if (btn) {
        btn.disabled = true
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> AI is working...'
    }
    if (statusEl) statusEl.textContent = 'Sending to AI, please wait...'

    const currentQuestionsText = questionnaireQuestions.map((q, i) =>
        `${i + 1}. [${q.qid}] (${q.type}) ${q.text}` +
        (q.options && q.options.length ? '\n   Options: ' + q.options.map(o => o.label || o).join(', ') : '')
    ).join('\n')

    const promptEl = document.getElementById(`qPrompt1`)
    const originalPrompt = promptEl ? promptEl.dataset.prompt.trim() : ''

    const improvePrompt = `${originalPrompt}

CURRENT QUESTIONNAIRE (${questionnaireQuestions.length} questions):
${currentQuestionsText}

USER FEEDBACK - Apply these changes to the questionnaire:
${feedbackText}

Please regenerate the complete improved questionnaire incorporating the feedback. Keep what was good, apply the requested changes. Return in the same JSON format as before.`

    try {
        const res = await fetch("/admin-dashboard/document-generator/questionnaire-step", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrf(),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                step: 1,
                document_name: documentName,
                prompt: improvePrompt,
                previous_outputs: [],
            }),
        })

        const data = await res.json()

        if (!data.success || !data.output || data.output.trim().length < 10) {
            if (statusEl) statusEl.textContent = ' AI failed. Please try again.'
            Swal.fire({
                icon: 'error',
                title: 'AI Improvement Failed',
                text: data.message || 'Could not improve the questionnaire. Please try again.',
                confirmButtonColor: '#FF6B35'
            })
            return
        }

        // Store updated output
        questionnaireOutputs[0] = {
            step: 1,
            prompt: improvePrompt,
            output: data.output,
            timestamp: new Date().toISOString(),
        }

        // Re-parse questions from new output
        questionnaireQuestions = []
        parseAllQuestions()
        fixInvalidGotoReferences()

        // Re-render the editor — this recreates the textarea with same ID
        editingQuestionIndex = null
        renderQuestionEditor()

        // After re-render, clear the new textarea and show success
        const newFeedbackEl = document.getElementById('qAiFeedbackText')
        if (newFeedbackEl) newFeedbackEl.value = ''

        const newStatusEl = document.getElementById('qAiFeedbackStatus')
        if (newStatusEl) newStatusEl.textContent = ` Done! ${questionnaireQuestions.length} questions updated.`

    } catch (error) {
        if (statusEl) statusEl.textContent = 'Error: ' + error.message
        Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: error.message || 'An unexpected error occurred.',
            confirmButtonColor: '#FF6B35'
        })
    } finally {
        const newBtn = document.getElementById('qAiImproveBtn')
        if (newBtn) {
            newBtn.disabled = false
            newBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send to AI'
        }
    }
}

function renderQuestionCard(q, index) {
    const questionText = q.text || q.label || "Question"
    const truncatedText = questionText.length > 40 ? questionText.substring(0, 40) + '...' : questionText
    const qid = q.qid || `QID${index + 1}`

    return `<div class="item-card" data-index="${index}" id="question-card-${index}">
        <div class="card-header">
            <div class="drag-handle-compact">
                <i class="fas fa-grip-vertical"></i>
            </div>
            <span class="id-badge">${escapeHtml(qid)}</span>
            <div class="card-actions">
                <button class="icon-btn" onclick="event.stopPropagation(); openEditQuestionModal(${index})" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="icon-btn" onclick="event.stopPropagation(); duplicateQuestion(${index})" title="Duplicate">
                    <i class="fas fa-copy"></i>
                </button>
                <button class="icon-btn danger" onclick="event.stopPropagation(); deleteQuestion(${index})" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <p class="item-preview">${escapeHtml(truncatedText)}</p>
            <div class="item-meta">
                <span class="type-badge ${q.type}">${q.type || 'text'}</span>
                ${q.required ? '<span class="required-badge">Required</span>' : ''}
                ${q.goto ? '<span class="goto-badge">→ ' + escapeHtml(q.goto) + '</span>' : ''}
            </div>
        </div>
    </div>`
}

function openEditQuestionModal(index) {
    editingQuestionIndex = index
    const q = questionnaireQuestions[index]

    const existingModal = document.getElementById('editQuestionModal')
    if (existingModal) existingModal.remove()

    const gotoValue = (q.goto === null || q.goto === undefined) ? "" : String(q.goto).trim()

    let gotoOptionsHtml = `<option value=""${gotoValue === "" ? " selected" : ""}>-- Auto (Next Question) --</option>`
    questionnaireQuestions.forEach((question, i) => {
        if (i > index) {
            const qidStr = String(question.qid)
            gotoOptionsHtml += `<option value="${escapeHtml(qidStr)}"${gotoValue === qidStr ? " selected" : ""}>${escapeHtml(qidStr)}</option>`
        }
    })
    gotoOptionsHtml += `<option value="END"${gotoValue === "END" ? " selected" : ""}>END (Finish)</option>`

    const questionType = q.type || "text"
    const questionOptions = Array.isArray(q.options) ? q.options : []

    const modalHTML = `
        <div class="modal fade" id="editQuestionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Question &nbsp;<span class="badge bg-primary">${escapeHtml(q.qid)}</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label form-label-sm">Question ID</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">QID</span>
                                    <input type="text" class="form-control" id="modal-q-qid"
                                        value="${escapeHtml(q.qid.replace('QID', ''))}"
                                        oninput="this.value = this.value.replace(/[^0-9]/g,'')">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label form-label-sm">Section</label>
                                <input type="text" class="form-control form-control-sm" id="modal-q-section"
                                    value="${escapeHtml(q.section_name || '')}">
                            </div>
                            <div class="col-12">
                                <label class="form-label form-label-sm">Question Text</label>
                                <textarea class="form-control form-control-sm" id="modal-q-text" rows="2">${escapeHtml(q.text)}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label form-label-sm">Help Text</label>
                                <textarea class="form-control form-control-sm" id="modal-q-userinfo" rows="2">${escapeHtml(q.userinfo || '')}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm">Type</label>
                                <select class="form-select form-select-sm" id="modal-q-type">
                                    <option value="text"${questionType === "text" ? " selected" : ""}>Text</option>
                                    <option value="textarea"${questionType === "textarea" ? " selected" : ""}>Textarea</option>
                                    <option value="radio"${questionType === "radio" ? " selected" : ""}>Radio</option>
                                    <option value="checkbox"${questionType === "checkbox" ? " selected" : ""}>Checkbox</option>
                                    <option value="select"${questionType === "select" ? " selected" : ""}>Dropdown</option>
                                    <option value="date"${questionType === "date" ? " selected" : ""}>Date</option>
                                    <option value="number"${questionType === "number" ? " selected" : ""}>Number</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm">Required</label>
                                <select class="form-select form-select-sm" id="modal-q-required">
                                    <option value="true"${q.required ? " selected" : ""}>Yes</option>
                                    <option value="false"${!q.required ? " selected" : ""}>No</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-sm">Go To</label>
                                <select class="form-select form-select-sm" id="modal-q-goto">
                                    ${gotoOptionsHtml}
                                </select>
                            </div>
                            ${["radio", "checkbox", "select"].includes(questionType) ? `
                            <div class="col-12">
                                <label class="form-label form-label-sm"><i class="fas fa-list-ul"></i> Options</label>
                                <div id="modal-q-options-list">
                                    ${questionOptions.map((opt, optIdx) => `
                                        <div class="d-flex gap-2 mb-2 align-items-center" id="modal-opt-row-${optIdx}">
                                            <input type="text" class="form-control form-control-sm" placeholder="Label"
                                                value="${escapeHtml(opt.label || opt)}"
                                                onchange="updateQuestionOption(${index}, ${optIdx}, 'label', this.value)">
                                            <input type="text" class="form-control form-control-sm" placeholder="Value"
                                                value="${escapeHtml(opt.value || opt)}"
                                                onchange="updateQuestionOption(${index}, ${optIdx}, 'value', this.value)">
                                            <button class="icon-btn danger flex-shrink-0" onclick="deleteQuestionOption(${index}, ${optIdx}); openEditQuestionModal(${index})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    `).join('')}
                                </div>
                                <button class="btn btn-sm btn-outline-success mt-1" onclick="addQuestionOption(${index}); openEditQuestionModal(${index})">
                                    <i class="fas fa-plus"></i> Add Option
                                </button>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="saveQuestionModalBtn" style="background:#FF6B35; border-color:#FF6B35;">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `

    document.body.insertAdjacentHTML('beforeend', modalHTML)

    const modalEl = document.getElementById('editQuestionModal')

    document.getElementById('saveQuestionModalBtn').addEventListener('click', () => {
        const qidRaw = document.getElementById('modal-q-qid').value.trim()
        const newQid = qidRaw ? 'QID' + qidRaw : q.qid
        const newText = document.getElementById('modal-q-text').value.trim()
        const newSection = document.getElementById('modal-q-section').value.trim()
        const newUserinfo = document.getElementById('modal-q-userinfo').value.trim()
        const newType = document.getElementById('modal-q-type').value
        const newRequired = document.getElementById('modal-q-required').value === 'true'
        const newGoto = document.getElementById('modal-q-goto').value

        if (!newText) {
            alert('Question text is required.')
            return
        }

        questionnaireQuestions[index].qid = newQid
        questionnaireQuestions[index].text = newText
        questionnaireQuestions[index].section_name = newSection
        questionnaireQuestions[index].userinfo = newUserinfo
        questionnaireQuestions[index].type = newType
        questionnaireQuestions[index].required = newRequired
        questionnaireQuestions[index].goto = newGoto

        fixInvalidGotoReferences()

        const cardEl = document.getElementById(`question-card-${index}`)
        if (cardEl) {
            cardEl.outerHTML = renderQuestionCard(questionnaireQuestions[index], index)
        }

        bootstrap.Modal.getInstance(modalEl).hide()
    })

    modalEl.addEventListener('hidden.bs.modal', () => modalEl.remove())

    const modalInstance = new bootstrap.Modal(modalEl)
    modalInstance.show()

    setTimeout(() => {
        const gotoSelect = document.getElementById('modal-q-goto')
        if (gotoSelect && gotoValue) gotoSelect.value = gotoValue
    }, 50)
}

function selectQuestionForEdit(index) {
    editingQuestionIndex = index

    document.querySelectorAll('#questionSortableList .item-card').forEach((card, i) => {
        card.classList.toggle('active', i === index)
    })

    const q = questionnaireQuestions[index]
    const panel = document.getElementById("questionEditPanel")

    const gotoValue = (q.goto === null || q.goto === undefined) ? "" : String(q.goto).trim()

    let gotoOptionsHtml = `<option value=""${gotoValue === "" ? " selected" : ""}>-- Auto (Next Question) --</option>`

    questionnaireQuestions.forEach((question, i) => {
        if (i > index) {
            const qidStr = String(question.qid)
            const isSelected = (gotoValue === qidStr)
            gotoOptionsHtml += `<option value="${escapeHtml(qidStr)}"${isSelected ? " selected" : ""}>${escapeHtml(qidStr)}</option>`
        }
    })

    const endSelected = (gotoValue === "END")
    gotoOptionsHtml += `<option value="END"${endSelected ? " selected" : ""}>END (Finish)</option>`

    const questionType = q.type || "text"
    const questionOptions = Array.isArray(q.options) ? q.options : []

    panel.innerHTML = `
        <div class="edit-panel-content">
            <div class="edit-panel-header">
                <h6><i class="fas fa-edit"></i> Edit Question</h6>
                <span class="badge bg-primary">${escapeHtml(q.qid)}</span>
            </div>

            <div class="edit-form">
                <div class="form-row">
                    <div class="form-group half">
                        <label>Question ID</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">QID</span>
                            <input type="text" 
                                class="form-control form-control-sm" 
                                value="${escapeHtml(q.qid.replace('QID', ''))}"
                                oninput="this.value = this.value.replace(/[^0-9]/g,'')"
                                onchange="updateQuestion(${index}, 'qid', this.value)"
                            >
                        </div>
                    </div>
                    <div class="form-group half">
                        <label>Section</label>
                        <input type="text" class="form-control form-control-sm" value="${escapeHtml(q.section_name || '')}" onchange="updateQuestion(${index}, 'section_name', this.value)">
                    </div>
                </div>

                <div class="form-group">
                    <label>Question Text</label>
                    <textarea class="form-control form-control-sm" rows="2"
                        onchange="updateQuestion(${index}, 'text', this.value)">${escapeHtml(q.text)}</textarea>
                </div>

                <div class="form-group">
                    <label>Help Text</label>
                    <textarea class="form-control form-control-sm" rows="2"
                        onchange="updateQuestion(${index}, 'userinfo', this.value)">${escapeHtml(q.userinfo || '')}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group third">
                        <label>Type</label>
                        <select class="form-select form-select-sm" onchange="updateQuestion(${index}, 'type', this.value)">
                            <option value="text"${questionType === "text" ? " selected" : ""}>Text</option>
                            <option value="textarea"${questionType === "textarea" ? " selected" : ""}>Textarea</option>
                            <option value="radio"${questionType === "radio" ? " selected" : ""}>Radio</option>
                            <option value="checkbox"${questionType === "checkbox" ? " selected" : ""}>Checkbox</option>
                            <option value="select"${questionType === "select" ? " selected" : ""}>Dropdown</option>
                            <option value="date"${questionType === "date" ? " selected" : ""}>Date</option>
                            <option value="number"${questionType === "number" ? " selected" : ""}>Number</option>
                        </select>
                    </div>
                    <div class="form-group third">
                        <label>Required</label>
                        <select class="form-select form-select-sm" onchange="updateQuestion(${index}, 'required', this.value === 'true')">
                            <option value="true"${q.required ? " selected" : ""}>Yes</option>
                            <option value="false"${!q.required ? " selected" : ""}>No</option>
                        </select>
                    </div>
                    <div class="form-group third">
                        <label>Go To</label>
                        <select class="form-select form-select-sm" id="goto-select-${index}" onchange="updateQuestion(${index}, 'goto', this.value)">
                            ${gotoOptionsHtml}
                        </select>
                    </div>
                </div>

                ${["radio", "checkbox", "select"].includes(questionType) ? `
                    <div class="form-group options-section">
                        <label><i class="fas fa-list-ul"></i> Options</label>
                        <div class="options-list" id="options-list-${index}">
                            ${questionOptions.map((opt, optIdx) => `
                                <div class="option-row">
                                    <input type="text" class="form-control form-control-sm" placeholder="Label" 
                                        value="${escapeHtml(opt.label || opt)}"
                                        onchange="updateQuestionOption(${index}, ${optIdx}, 'label', this.value)">
                                    <input type="text" class="form-control form-control-sm" placeholder="Value" 
                                        value="${escapeHtml(opt.value || opt)}"
                                        onchange="updateQuestionOption(${index}, ${optIdx}, 'value', this.value)">
                                    <button class="icon-btn danger" onclick="deleteQuestionOption(${index}, ${optIdx})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            `).join('')}
                        </div>
                        <button class="btn btn-sm btn-outline-success" onclick="addQuestionOption(${index})">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>
                ` : ''}
            </div>
        </div>
    `

    setTimeout(() => {
        const selectEl = document.getElementById(`goto-select-${index}`)
        if (selectEl && gotoValue) {
            selectEl.value = gotoValue
        }
    }, 10)
}

function initQuestionSortable() {
    const el = document.getElementById("questionSortableList")
    if (!el) return

    if (questionSortable) {
        questionSortable.destroy()
    }

    questionSortable = new Sortable(el, {
        animation: 150,
        handle: ".drag-handle-compact",
        ghostClass: "sortable-ghost",
        chosenClass: "sortable-chosen",
        dragClass: "sortable-drag",
        onEnd: function (evt) {
            const oldIndex = evt.oldIndex
            const newIndex = evt.newIndex

            if (oldIndex !== newIndex) {
                // Save scroll position
                const scrollPos = saveScrollPosition('questionSortableList')

                const movedItem = questionnaireQuestions.splice(oldIndex, 1)[0]
                questionnaireQuestions.splice(newIndex, 0, movedItem)

                fixInvalidGotoReferences()

                if (editingQuestionIndex !== null) {
                    if (editingQuestionIndex === oldIndex) {
                        editingQuestionIndex = newIndex
                    } else if (oldIndex < editingQuestionIndex && newIndex >= editingQuestionIndex) {
                        editingQuestionIndex--
                    } else if (oldIndex > editingQuestionIndex && newIndex <= editingQuestionIndex) {
                        editingQuestionIndex++
                    }
                }

                // Update cards without full re-render
                updateQuestionCardsOrder()

                // Restore scroll position
                restoreScrollPosition('questionSortableList', scrollPos)

                if (editingQuestionIndex !== null) {
                    selectQuestionForEdit(editingQuestionIndex)
                }
            }
        },
    })
}

function updateQuestionCardsOrder() {
    const listEl = document.getElementById("questionSortableList")
    if (!listEl) return

    const cards = listEl.querySelectorAll('.item-card')
    cards.forEach((card, index) => {
        card.setAttribute('data-index', index)
        card.setAttribute('id', `question-card-${index}`)

        const editBtn = card.querySelector('.icon-btn:first-child')
        const duplicateBtn = card.querySelectorAll('.icon-btn')[1]
        const deleteBtn = card.querySelector('.icon-btn.danger')
        if (editBtn) editBtn.setAttribute('onclick', `event.stopPropagation(); openEditQuestionModal(${index})`)
        if (duplicateBtn) duplicateBtn.setAttribute('onclick', `event.stopPropagation(); duplicateQuestion(${index})`)
        if (deleteBtn) deleteBtn.setAttribute('onclick', `event.stopPropagation(); deleteQuestion(${index})`)

        const q = questionnaireQuestions[index]
        const idBadge = card.querySelector('.id-badge')
        if (idBadge) idBadge.textContent = q.qid

        const preview = card.querySelector('.item-preview')
        if (preview) {
            const truncatedText = q.text.length > 40 ? q.text.substring(0, 40) + '...' : q.text
            preview.textContent = truncatedText
        }
    })
}

function updateQuestion(index, field, value) {
    if (questionnaireQuestions[index]) {

        if (field === 'qid') {
            value = value.replace(/\D/g, '')
            value = value ? 'QID' + value : '';
        }

        if (field === 'goto') {
            value = (value === null || value === undefined) ? "" : String(value).trim()
        }

        questionnaireQuestions[index][field] = value
        const cardEl = document.getElementById(`question-card-${index}`)
        if (cardEl) {
            const q = questionnaireQuestions[index]
            cardEl.outerHTML = renderQuestionCard(q, index)
        }

        if (field === "type") {
            selectQuestionForEdit(index)
        }
    }
}

function updateQuestionOption(qIndex, optIndex, field, value) {
    if (questionnaireQuestions[qIndex] && questionnaireQuestions[qIndex].options[optIndex]) {
        if (typeof questionnaireQuestions[qIndex].options[optIndex] === "string") {
            questionnaireQuestions[qIndex].options[optIndex] = { label: value, value: value }
        } else {
            questionnaireQuestions[qIndex].options[optIndex][field] = value
        }
    }
}

function addQuestionOption(qIndex) {
    if (questionnaireQuestions[qIndex]) {
        if (!questionnaireQuestions[qIndex].options) {
            questionnaireQuestions[qIndex].options = []
        }
        questionnaireQuestions[qIndex].options.push({ label: "New Option", value: "new_option" })
        selectQuestionForEdit(qIndex)
    }
}

function deleteQuestionOption(qIndex, optIndex) {
    Swal.fire({
        title: 'Delete Option?',
        text: 'Are you sure you want to delete this option?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            questionnaireQuestions[qIndex].options.splice(optIndex, 1);
            selectQuestionForEdit(qIndex);
        }
    });
}

function deleteQuestion(index) {
    Swal.fire({
        title: 'Delete Question?',
        text: 'Are you sure you want to delete this question?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            questionnaireQuestions.splice(index, 1);
            editingQuestionIndex = null;
            fixInvalidGotoReferences();
            renderQuestionEditor();
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'Question has been deleted.',
                timer: 1500,
                showConfirmButton: false,

            });
        }
    });
}

function duplicateQuestion(index) {
    const original = questionnaireQuestions[index]
    const duplicate = JSON.parse(JSON.stringify(original))
    duplicate.id = Date.now()
    duplicate.qid = `QID${questionnaireQuestions.length + 1}`
    duplicate.goto = ""
    questionnaireQuestions.splice(index + 1, 0, duplicate)
    fixInvalidGotoReferences()
    renderQuestionEditor()
    selectQuestionForEdit(index + 1)
}

function addNewQuestion() {
    const newQid = `QID${questionnaireQuestions.length + 1}`
    questionnaireQuestions.push({
        id: Date.now(),
        qid: newQid,
        text: "New Question",
        type: "text",
        options: [],
        required: true,
        goto: "",
        userinfo: "",
        section_name: "",
    })
    renderQuestionEditor()
    selectQuestionForEdit(questionnaireQuestions.length - 1)
}

/*  STEP 3: CONTRACT GENERATION  */
// async function submitContractStep() {
//     if (cCurrentStep === 1) {
//         // CHANGED: Always force 1 step for contract
//         cTotalSteps = 1;

//         updateStepIndicator("cStepIndicator", cCurrentStep, cTotalSteps)
//         document.getElementById("cStepIndicator").style.display = "block"
//     }

//     const promptEl = document.getElementById(`cPrompt${cCurrentStep}`);
//     const prompt = promptEl ? promptEl.dataset.prompt.trim() : '';

//     if (!prompt) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Prompt missing',
//             text: `No prompt found for step ${cCurrentStep}`,
//             confirmButtonColor: '#FF6B35'
//         });
//         return;
//     } else {
//         clearValidationError("cPrompt", "cPromptError");
//     }

//     showLoading("cStepOutput")

//     try {
//         const res = await fetch("/admin-dashboard/document-generator/contract-step", {
//             method: "POST",
//             headers: {
//                 "X-CSRF-TOKEN": csrf(),
//                 "Content-Type": "application/json",
//             },
//             body: JSON.stringify({
//                 step: cCurrentStep,
//                 document_name: documentName,
//                 prompt: prompt,
//                 questionnaire: questionnaireQuestions,
//                 previous_outputs: contractOutputs.filter(output => output !== null),
//             }),
//         })

//         const data = await res.json()

//         if (!data.success) {
//             throw new Error(data.message || "Failed to generate contract")
//         }

//         contractOutputs[cCurrentStep - 1] = {
//             step: cCurrentStep,
//             prompt: prompt,
//             output: data.output,
//             timestamp: new Date().toISOString(),
//         }

//         // CHANGED: Always treat as last step (single step), parse and show as final contract view
//         contractText = contractOutputs
//             .filter(o => o !== null)
//             .map((o) => o.output)
//             .join("\n\n")

//         parseContractTextItems()

//         // CHANGED: Show as final-contract preview instead of hiding
//         displayContractAsFinalPreview(data.output, cCurrentStep)

//         updateContractControls()

//     } catch (error) {
//         hideLoading("cStepOutput")
//         Swal.fire({
//             icon: 'error',
//             title: 'Error',
//             text: error.message,
//             confirmButtonColor: '#FF6B35',
//             confirmButtonText: 'OK'
//         });
//     }
// }

async function submitContractStep() {
    const step1Input = document.getElementById('contractName');
    if (step1Input && step1Input.value.trim()) {
        documentName = step1Input.value.trim();
        window.documentName = documentName;
    }

    if (!documentName || !documentName.trim()) {
        documentName = document.getElementById('contractNamePreview')?.value?.trim()
            || window.documentName
            || '';
    }

    if (!documentName || !documentName.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Document Name Missing',
            text: 'Please enter a document name in Step 1 before generating the contract.',
            confirmButtonColor: '#FF6B35',
            confirmButtonText: 'OK'
        });
        return;
    }
    window.documentName = documentName;
    if (cCurrentStep === 1) {
        cTotalSteps = 1;
        updateStepIndicator("cStepIndicator", cCurrentStep, cTotalSteps);
        document.getElementById("cStepIndicator").style.display = "block";
    }

    const promptEl = document.getElementById(`cPrompt${cCurrentStep}`);
    const prompt = promptEl ? promptEl.dataset.prompt.trim() : '';

    if (!prompt) {
        Swal.fire({
            icon: 'error',
            title: 'Prompt missing',
            text: `No prompt found for step ${cCurrentStep}`,
            confirmButtonColor: '#FF6B35'
        });
        return;
    } else {
        clearValidationError("cPrompt", "cPromptError");
    }

    showLoading("cStepOutput");

    try {
        const res = await fetch("/admin-dashboard/document-generator/contract-step", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrf(),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                step: cCurrentStep,
                document_name: documentName,   // always uses the validated name
                prompt: prompt,
                questionnaire: questionnaireQuestions,
                previous_outputs: contractOutputs.filter(output => output !== null),
            }),
        });

        const data = await res.json();

        if (!data.success) {
            throw new Error(data.message || "Failed to generate contract");
        }

        contractOutputs[cCurrentStep - 1] = {
            step: cCurrentStep,
            prompt: prompt,
            output: data.output,
            timestamp: new Date().toISOString(),
        };

        contractText = contractOutputs
            .filter(o => o !== null)
            .map((o) => o.output)
            .join("\n\n");

        parseContractTextItems();
        displayContractAsFinalPreview(data.output, cCurrentStep);
        updateContractControls();

    } catch (error) {
        hideLoading("cStepOutput");
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message,
            confirmButtonColor: '#FF6B35',
            confirmButtonText: 'OK'
        });
    }
}

function displayContractAsFinalPreview(output, step) {
    const outputDiv = document.getElementById("cStepOutput")
    if (!outputDiv) return

    const trimmed = output.trim()
    const looksLikeJson = trimmed.startsWith('{') || trimmed.startsWith('[')

    if (looksLikeJson) {
        const isTruncated = !trimmed.endsWith('}') && !trimmed.endsWith(']')
        if (isTruncated) {
            _renderContractErrorBanner(step, 'Truncated or Incomplete Response',
                'The AI response was cut off mid-JSON. Click Reset and try again.',
                ['Response ended mid-JSON (incomplete output)'])
            updateContractControls()
            return
        }
        try {
            JSON.parse(trimmed)
        } catch (e) {
            _renderContractErrorBanner(step, 'Invalid JSON Response',
                'The AI returned malformed JSON. Please click Reset and regenerate.',
                ['JSON syntax error: ' + e.message.substring(0, 80)])
            updateContractControls()
            return
        }
    }

    outputDiv.innerHTML = `
            <div>
                <div style="font-weight:600; color:#15803d; font-size:14px;"></div>
            </div>
    `

    updateContractControls()
}

function focusContractPreviewQuestion(qid) {
    const el = document.getElementById(`cpreview-q-${qid}`)
    if (el) {
        el.style.borderLeftColor = '#FF6B35'
        el.style.background = '#fff3e0'
        el.scrollIntoView({ behavior: 'smooth', block: 'center' })
        setTimeout(() => {
            el.style.borderLeftColor = '#dee2e6'
            el.style.background = '#f8f9fa'
        }, 2000)
    }
}

function displayContractOutput(output, step) {
    displayContractAsFinalPreview(output, step)
}

function _renderContractErrorBanner(step, title, message, issues = []) {
    const outputDiv = document.getElementById('cStepOutput')
    if (!outputDiv) return

    const issueRows = issues.length > 0
        ? issues.map(i => `
            <li style="
                margin: 4px 0;
                padding: 5px 0;
                border-bottom: 0.5px solid var(--color-border-tertiary);
                font-size: 13px;
                color: var(--color-text-danger);
                list-style: disc;
            ">${escapeHtml(i)}</li>
        `).join('')
        : ''

    const infoNote = message || 'An error occurred while generating this step.'

    outputDiv.innerHTML = `
        <div style="
            border: 1.5px solid var(--color-border-danger);
            border-radius: var(--border-radius-lg);
            background: var(--color-background-danger);
            padding: 18px 20px;
            margin: 8px 0;
        ">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" style="flex-shrink:0;">
                    <circle cx="10" cy="10" r="9" stroke="#E24B4A" stroke-width="1.5"/>
                    <path d="M10 6v5M10 14h.01" stroke="#E24B4A" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span style="font-weight:500; font-size:15px; color:var(--color-text-danger);">
                    Step ${step}: ${escapeHtml(title)}
                </span>
                <span style="margin-left:auto; font-size:11px; color:var(--color-text-tertiary);">
                    Step ${step} of ${cTotalSteps}
                </span>
            </div>

            ${issueRows ? `
                <div style="margin-bottom: 14px;">
                    <div style="font-size: 12px; font-weight: 500; color: var(--color-text-secondary); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Issues detected
                    </div>
                    <ul style="margin: 0; padding-left: 20px;">
                        ${issueRows}
                    </ul>
                </div>
            ` : ''}

            <div style="
                background: var(--color-background-primary);
                border-radius: var(--border-radius-md);
                border: 0.5px solid var(--color-border-tertiary);
                padding: 10px 14px;
                font-size: 12px;
                color: var(--color-text-secondary);
                margin-bottom: 16px;
                line-height: 1.6;
            ">
                ${escapeHtml(infoNote)}
            </div>
        </div>
    `
}

function updateContractControls() {
    const submitBtn = document.getElementById("cSubmitBtn")
    const backBtn = document.getElementById("cBackBtn")
    const nextAction = document.getElementById("cNextAction")

    // CHANGED: No back button needed for single step
    backBtn.classList.add("d-none")

    const hasCurrentOutput = contractOutputs[cCurrentStep - 1]

    if (hasCurrentOutput) {
        submitBtn.classList.add("d-none")
        nextAction.innerHTML = `<button class="btn btn-success" onclick="contractText = contractOutputs.filter(o=>o).map(o => o.output).join('\\n\\n'); goToStep(4);">
            <i class="fas fa-check"></i> Proceed to Edit Contract
        </button>`
    } else {
        submitBtn.classList.remove("d-none")
        submitBtn.innerHTML = `<i class="fas fa-paper-plane"></i> Generate Contract`
        nextAction.innerHTML = ""
    }
}

function moveToNextContractStep() {
    cCurrentStep++
    // document.getElementById("cPrompt").placeholder = `Enter prompt for step ${cCurrentStep}...`
    document.getElementById("cStepOutput").innerHTML = ""
    updateStepIndicator("cStepIndicator", cCurrentStep, cTotalSteps)
    updateContractControls()
}

function goPrevContractStep() {
    if (cCurrentStep <= 1) return

    cCurrentStep--

    const prev = contractOutputs[cCurrentStep - 1]
    if (prev) {
        // document.getElementById("cPrompt").value = prev.prompt
        displayContractOutput(prev.output, cCurrentStep)
    } else {
        // document.getElementById("cPrompt").value = ""
        document.getElementById("cStepOutput").innerHTML = ""
    }

    updateStepIndicator("cStepIndicator", cCurrentStep, cTotalSteps)
    updateContractControls()
    // document.getElementById("cPrompt").placeholder = `Enter prompt for step ${cCurrentStep}...`
}

function resetContractGeneration() {
    Swal.fire({
        title: 'Reset Current Step?',
        text: `Are you sure you want to reset Step ${cCurrentStep}? This will clear the contract sections generated in this step only.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, reset it!'
    }).then((result) => {
        if (result.isConfirmed) {
            if (cCurrentStep > 0 && Array.isArray(contractOutputs)) {
                const index = cCurrentStep - 1;
                if (index < contractOutputs.length) {
                    const outputText = contractOutputs[index]?.output || '';
                    const tidsInThisStep = extractTIDsFromOutput(outputText);

                    //  Remove contract items that were created in this step
                    if (tidsInThisStep.length > 0) {
                        const itemsBeforeReset = contractTextItems.length;
                        contractTextItems = contractTextItems.filter(item =>
                            !tidsInThisStep.includes(item.tid)
                        );

                        const itemsRemoved = itemsBeforeReset - contractTextItems.length;

                        //  Rebuild contract data after removal
                        if (itemsRemoved > 0) {
                            rebuildParsedContractData();
                        }
                    }

                    //  Clear the output for this step
                    contractOutputs[index] = null;
                }
            }

            //  Clear UI elements for current step only
            // const promptEl = document.getElementById("cPrompt");
            const outputEl = document.getElementById("cStepOutput");
            const actionEl = document.getElementById("cNextAction");

            // if (promptEl) promptEl.value = "";
            if (outputEl) outputEl.innerHTML = "";
            if (actionEl) actionEl.innerHTML = "";

            updateContractControls();

            Swal.fire({
                icon: 'success',
                title: 'Step Reset!',
                text: `Step ${cCurrentStep} has been reset. You can now regenerate this step.`,
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

function extractTIDsFromOutput(outputText) {
    const tids = [];
    const tidPattern = /"(TID\d+)":/g;
    let match;

    while ((match = tidPattern.exec(outputText)) !== null) {
        tids.push(match[1]);
    }

    return tids;
}

/*  STEP 4: CONTRACT EDITOR */
function parseContractTextItems() {
    contractTextItems = []
    parsedContractData = {}

    //  Filter out null outputs and combine text
    const validOutputs = contractOutputs.filter(o => o !== null && o !== undefined);
    if (validOutputs.length === 0) {
        return;
    }
    const combinedText = validOutputs.map(o => o.output).join('\n\n').trim()

    if (!combinedText) {
        return
    }

    const completeJsonObjects = extractCompleteJsonObjects(combinedText)

    completeJsonObjects.forEach((jsonObj, idx) => {
        processContractJsonObject(jsonObj, idx)
    })

    const foundTidsAfterStrategy1 = new Set(contractTextItems.map(item => item.tid))

    const extractedTids = extractTidBlocksWithRegex(combinedText)

    extractedTids.forEach(tidData => {
        if (!foundTidsAfterStrategy1.has(tidData.tid)) {
            contractTextItems.push(tidData)
        }
    })

    const allFoundTids = new Set(contractTextItems.map(item => item.tid))
    const lineExtractedTids = extractTidsLineByLine(combinedText, allFoundTids)

    lineExtractedTids.forEach(tidData => {
        if (!allFoundTids.has(tidData.tid)) {
            contractTextItems.push(tidData)
            allFoundTids.add(tidData.tid)
        }
    })

    contractTextItems.sort((a, b) => {
        const numA = parseInt(a.tid.replace(/\D/g, '')) || 0
        const numB = parseInt(b.tid.replace(/\D/g, '')) || 0
        return numA - numB
    })

    rebuildParsedContractData()

    console.log(' Parsed contract items:', {
        total: contractTextItems.length,
        firstTID: contractTextItems[0]?.tid,
        lastTID: contractTextItems[contractTextItems.length - 1]?.tid,
        sections: [...new Set(contractTextItems.map(i => i.section_name))]
    });
}

function extractCompleteJsonObjects(text) {
    const objects = []
    let searchStart = 0

    while (searchStart < text.length) {
        const startMatch = text.substring(searchStart).match(/{\s*"[A-Za-z_]+"\s*:\s*{/)
        if (!startMatch) break

        const startIdx = searchStart + startMatch.index

        let braceCount = 0
        let inString = false
        let escapeNext = false
        let endIdx = -1

        for (let j = startIdx; j < text.length; j++) {
            const char = text[j]

            if (escapeNext) {
                escapeNext = false
                continue
            }

            if (char === '\\' && inString) {
                escapeNext = true
                continue
            }

            if (char === '"' && !escapeNext) {
                inString = !inString
                continue
            }

            if (inString) continue

            if (char === '{') {
                braceCount++
            } else if (char === '}') {
                braceCount--
                if (braceCount === 0) {
                    endIdx = j
                    break
                }
            }
        }

        if (endIdx === -1) {
            searchStart = startIdx + 1
            continue
        }

        const jsonStr = text.substring(startIdx, endIdx + 1)

        try {
            const parsed = JSON.parse(jsonStr)

            let hasContractText = false
            for (const key in parsed) {
                if (parsed[key] && typeof parsed[key] === 'object' && parsed[key].Contract_Text) {
                    hasContractText = true
                    break
                }
            }

            if (hasContractText) {
                objects.push(parsed)
            }
        } catch (e) {
            console.warn(`JSON parse failed at position ${startIdx}`)
        }

        searchStart = endIdx + 1
    }

    return objects
}

function processContractJsonObject(jsonObj, idx) {
    for (const sectionKey in jsonObj) {
        const section = jsonObj[sectionKey]

        if (!section || typeof section !== 'object') continue

        if (!parsedContractData[sectionKey]) {
            parsedContractData[sectionKey] = {
                section_id: section.section_id || null,
                Contract_Text: {}
            }
        }

        if (section.Contract_Text && typeof section.Contract_Text === 'object') {
            for (const tid in section.Contract_Text) {
                const item = section.Contract_Text[tid]

                if (!item || typeof item !== 'object') continue

                parsedContractData[sectionKey].Contract_Text[tid] = item

                contractTextItems.push({
                    tid: tid,
                    section_key: sectionKey,
                    section_id: section.section_id || null,
                    section_name: item.section_name || sectionKey,
                    type: item.TYPE || "CONTENT",
                    text: item.TEXT || "",
                    align_text: item.ALIGN_TEXT || "left",
                    blur_content: item.BLUR_CONTENT || false,
                    conditions: Array.isArray(item.CONDITIONS) ? item.CONDITIONS : [],
                })
            }
        }
    }
}

function extractTidBlocksWithRegex(text) {
    const results = []
    const tidStartPattern = /"(TID\d+)":\s*{/g
    let match

    while ((match = tidStartPattern.exec(text)) !== null) {
        const tid = match[1]
        const startPos = match.index + match[0].length - 1

        let braceCount = 1
        let inString = false
        let escapeNext = false
        let endPos = -1

        for (let i = startPos + 1; i < text.length && i < startPos + 5000; i++) {
            const char = text[i]

            if (escapeNext) {
                escapeNext = false
                continue
            }

            if (char === '\\' && inString) {
                escapeNext = true
                continue
            }

            if (char === '"') {
                inString = !inString
                continue
            }

            if (inString) continue

            if (char === '{') {
                braceCount++
            } else if (char === '}') {
                braceCount--
                if (braceCount === 0) {
                    endPos = i
                    break
                }
            }
        }

        if (endPos === -1) {
            const partialContent = text.substring(startPos, Math.min(startPos + 2000, text.length))
            const tidData = parsePartialTidContent(tid, partialContent)
            if (tidData) {
                results.push(tidData)
            }
            continue
        }

        const tidContent = text.substring(startPos, endPos + 1)

        try {
            const parsed = JSON.parse(tidContent)
            results.push({
                tid: tid,
                section_key: parsed.section_name || "Unknown",
                section_id: null,
                section_name: parsed.section_name || "Unknown",
                type: parsed.TYPE || "CONTENT",
                text: parsed.TEXT || "",
                align_text: parsed.ALIGN_TEXT || "left",
                blur_content: parsed.BLUR_CONTENT || false,
                conditions: Array.isArray(parsed.CONDITIONS) ? parsed.CONDITIONS : [],
            })
        } catch (e) {
            const tidData = parsePartialTidContent(tid, tidContent)
            if (tidData) {
                results.push(tidData)
            }
        }
    }

    return results
}

function parsePartialTidContent(tid, content) {
    try {
        const sectionNameMatch = content.match(/"section_name":\s*"([^"]*)"/)
        const typeMatch = content.match(/"TYPE":\s*"([^"]*)"/)
        const textMatch = content.match(/"TEXT":\s*"((?:[^"\\]|\\.)*)"/s)
        const alignMatch = content.match(/"ALIGN_TEXT":\s*"([^"]*)"/)
        const blurMatch = content.match(/"BLUR_CONTENT":\s*(true|false)/)

        let conditions = []
        const conditionsMatch = content.match(/"CONDITIONS":\s*\[([\s\S]*?)\]/m)
        if (conditionsMatch) {
            try {
                conditions = JSON.parse('[' + conditionsMatch[1] + ']')
            } catch (e) {
                // Ignore
            }
        }

        if (textMatch || typeMatch || sectionNameMatch) {
            let textContent = ""
            if (textMatch) {
                textContent = textMatch[1]
                    .replace(/\\"/g, '"')
                    .replace(/\\n/g, '\n')
                    .replace(/\\r/g, '')
                    .replace(/\\t/g, '\t')
                    .replace(/\\\\/g, '\\')
            }

            return {
                tid: tid,
                section_key: sectionNameMatch ? sectionNameMatch[1] : "Unknown",
                section_id: null,
                section_name: sectionNameMatch ? sectionNameMatch[1] : "Unknown",
                type: typeMatch ? typeMatch[1] : "CONTENT",
                text: textContent,
                align_text: alignMatch ? alignMatch[1] : "left",
                blur_content: blurMatch ? blurMatch[1] === "true" : false,
                conditions: conditions,
            }
        }
    } catch (e) {
        console.warn(`Failed to parse partial content for ${tid}`)
    }

    return null
}

function extractTidsLineByLine(text, existingTids) {
    const results = []
    const segments = text.split(/(?="TID\d+":\s*{)/)

    for (const segment of segments) {
        const tidMatch = segment.match(/^"(TID\d+)":\s*{/)
        if (!tidMatch) continue

        const tid = tidMatch[1]
        if (existingTids.has(tid)) continue

        const tidData = parsePartialTidContent(tid, segment)
        if (tidData) {
            results.push(tidData)
        }
    }

    return results
}

function rebuildParsedContractData() {
    parsedContractData = {}

    contractTextItems.forEach((item) => {
        const sectionKey = item.section_key || "Main"

        if (!parsedContractData[sectionKey]) {
            parsedContractData[sectionKey] = {
                section_id: item.section_id || null,
                Contract_Text: {},
            }
        }

        const textItem = {
            section_name: item.section_name,
            TYPE: item.type,
            TEXT: item.text,
            ALIGN_TEXT: item.align_text,
            BLUR_CONTENT: item.blur_content,
        }

        if (item.conditions && item.conditions.length > 0) {
            textItem.CONDITIONS = item.conditions
        }

        parsedContractData[sectionKey].Contract_Text[item.tid] = textItem
    })

    contractText = JSON.stringify(parsedContractData, null, 2)
}

function renderContractEditor() {
    const container = document.getElementById("contractEditorContainer");
    if (!container) return;
 
    if (contractTextItems.length === 0) {
        container.innerHTML = `<div class="alert alert-warning">
            <strong>No contract data found.</strong>
            <p>Please complete the contract generation step, or add items manually.</p>
            <button class="btn btn-success btn-sm mt-2" onclick="addNewContractTextItem()">
                <i class="fas fa-plus"></i> Add First Section
            </button>
        </div>`;
        return;
    }
 
    container.innerHTML = `
    <style>
    /* ── CE-style layout ── */
    #s4Wrap { display:grid; grid-template-columns:370px 1fr; gap:0; height:calc(100vh - 320px); min-height:560px; background:var(--color-background-secondary); border:1px solid var(--color-border-tertiary); border-radius:10px; overflow:hidden; }
    #s4Left,#s4Right { display:flex; flex-direction:column; height:100%; overflow:hidden; }
    #s4Left  { border-right:1px solid var(--color-border-tertiary); }
    #s4Right { }
 
    /* heads */
    .s4-head { padding:12px 14px; border-bottom:1px solid var(--color-border-tertiary); background:var(--color-background-primary); flex-shrink:0; display:flex; flex-direction:column; gap:8px; }
    .s4-head-row { display:flex; align-items:center; gap:8px; }
    .s4-panel-title { font-size:13px; font-weight:500; color:var(--color-text-primary); display:flex; align-items:center; gap:6px; }
    .s4-count-badge { background:#e85d2f; color:#fff; border-radius:10px; padding:1px 7px; font-size:11px; }
 
    /* bulk bar */
    .s4-bulk-bar { display:none;   background: #1d4ed8;color: #fff;padding: 8px 12px;align-items: center;gap: 10px;border-radius: 6px;margin: 10px 24px 8px;font-size: 12px;font-weight: 600;animation: ceFadeIn .15s ease;}
    .s4-bulk-bar.open { display:flex; }
    .s4-bulk-btn {background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);  color: #fff; border-radius: 5px;padding: 4px 12px; font-size: 12px; font-weight: 600;  cursor: pointer;  display: inline-flex;   align-items: center;   gap: 5px;
    transition: background .12s; }
    .s4-bulk-btn:hover {  background: rgba(255,255,255,0.28)}  
    .s4-bulk-close { margin-left:auto; background:none; border:none; cursor:pointer; font-size:16px; color:#9ca3af; line-height:1; }
 
    /* scrollable lists */
    .s4-list { flex:1; overflow-y:auto; overflow-x:hidden; padding:24px; min-height:0; }
 
    /* question cards */
    .s4-qcard { background:var(--color-background-primary); border:1px solid var(--color-border-tertiary); border-radius:8px; margin-bottom:8px; cursor:default; transition:border-color .12s, box-shadow .12s; }
    .s4-qcard:hover { border-color:#e85d2f; }
    .s4-qcard.s4-selected { border-color:#e85d2f !important; box-shadow:0 0 0 2px #e85d2f30; }
    .s4-qcard-top { display:flex; align-items:center; gap:6px; padding:8px 10px 4px; }
    .s4-qid-badge { font-size:10px; font-weight:700; color:#e85d2f; background:#fff4f0; padding:2px 6px; border-radius:4px; font-family:monospace; cursor:pointer; }
    .s4-qcard-actions { display:flex; gap:4px; align-items:center; margin-left:auto; }
    .s4-qcard-label { font-size:12px; color:var(--color-text-primary); padding:0 10px 4px; line-height:1.4; }
    .s4-qfield-preview { padding:4px 10px 8px; }
    .s4-icon-btn { width:24px; height:24px; background:var(--color-background-secondary); border:1px solid var(--color-border-tertiary); border-radius:5px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; color:var(--color-text-secondary); font-size:11px; transition:background .1s, color .1s; flex-shrink:0; }
    .s4-icon-btn:hover { background:#e85d2f; color:#fff; border-color:#e85d2f; }
    .s4-icon-btn.s4-del:hover { background:#dc2626; border-color:#dc2626; }
    .s4-drag-handle { cursor:grab; color:var(--color-text-tertiary); font-size:10px; padding:0 2px; }
    .s4-drag-handle:active { cursor:grabbing; }
 
    /* section blocks */
    .s4-sblock { background:var(--color-background-primary); border:1px solid var(--color-border-tertiary); border-radius:8px; margin-bottom:8px; transition:border-color .12s; }
    .s4-sblock:hover { border-color:#e85d2f33; }
    .s4-sblock.s4-selected { border-color: #e85d2f !important; box-shadow: 0 0 0 2px #e85d2f33;}
    .s4-sblock-top { display:flex; align-items:center; gap:6px; padding:8px 10px 4px; }
    .s4-tid-badge { font-size:10px; font-weight:700; color:#e85d2f; background:#fff4f0; padding:2px 6px; border-radius:4px; font-family:monospace; }
    .s4-sblock-type { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--color-text-tertiary); margin-left:2px; }
    .s4-sblock-actions { display:flex; gap:4px; align-items:center; margin-left:auto; }
    .s4-sblock-content { padding:4px 10px 8px; font-size:13px; color:#374151; max-height:120px; overflow-y:auto; line-height:1.5; }
    .s4-sblock-divider { margin:0 10px; border:none; border-top:1px solid var(--color-border-tertiary); }
    .s4-cond-row { display:flex; align-items:center; gap:6px; padding:4px 10px 6px; flex-wrap:wrap; }
    .s4-cond-badge { display:inline-flex; align-items:center; gap:3px; background:#e6f4ea; color:#1e8e3e; border-radius:5px; padding:2px 7px; font-size:10px; font-weight:700; cursor:pointer; }
    .s4-qvar { display:inline-flex; align-items:center; gap:2px; background:#fff4f0; color:#e85d2f; border-radius:4px; padding:1px 5px; font-size:11px; font-weight:700; font-family:monospace; cursor:pointer; }
    .s4-qvar:hover { background:#fdd0bb; }
 
    /* modal */
    .s4-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:99999; align-items:center; justify-content:center; }
    .s4-modal.open { display:flex; }
    .s4-mbox { background:var(--color-background-primary); border-radius:12px; width:640px; max-width:96vw; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 16px 48px rgba(0,0,0,.18); }
    .s4-mhead { display:flex; align-items:center; justify-content:space-between; padding:14px 18px; border-bottom:1px solid var(--color-border-tertiary); gap:10px; flex-shrink:0; }
    .s4-mtitle { font-size:14px; font-weight:500; color:var(--color-text-primary); }
    .s4-mclose { background:none; border:none; cursor:pointer; font-size:20px; color:var(--color-text-tertiary); line-height:1; }
    .s4-mbody { padding:16px 18px; overflow-y:auto; flex:1; display:flex; flex-direction:column; gap:12px; }
    .s4-mfoot { padding:12px 18px; border-top:1px solid var(--color-border-tertiary); display:flex; justify-content:flex-end; gap:8px; flex-shrink:0; }
    .s4-fg { display:flex; flex-direction:column; gap:4px; }
    .s4-flabel { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--color-text-tertiary); }
    .s4-btn-save { background:#e85d2f; color:#fff; border:none; border-radius:7px; padding:8px 20px; font-size:13px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:background .12s; }
    .s4-btn-save:hover { background:#c94d23; }
    .s4-btn-save:disabled { opacity:.5; cursor:not-allowed; }
 
    /* question-preview popup */
     #s4QPreview { display:none; position:fixed; z-index:999999; background-color:#fff; border:1px solidrgb(185, 185, 185); border-radius:10px; box-shadow:0 6px 24px rgba(232,93,47,.12); padding:12px 16px; min-width:260px; max-width:300px; pointer-events:none; }
 
    /* inline field preview */
    .s4-field-inp { width:100%; padding:5px 8px; border:1.5px solid var(--color-border-tertiary); border-radius:5px; font-size:11.5px; color:var(--color-text-primary); background:var(--color-background-primary); outline:none; font-family:inherit; box-sizing:border-box; }
    .s4-radio-grp, .s4-check-grp { display:flex; flex-direction:column; gap:4px; }
    .s4-radio-lbl, .s4-check-lbl { display:flex; align-items:center; gap:6px; font-size:11.5px; color:var(--color-text-primary); cursor:pointer; }
 
    /* type label */
    .s4-type-lbl { font-size:10px; font-weight:600; color:var(--color-text-tertiary); font-family:monospace; margin-bottom:3px; }
 
    /* section content rendered HTML */
    .s4-rendered h3,.s4-rendered h4 { font-size:12px; font-weight:500; margin:0 0 4px; }
    .s4-rendered p { margin:0 0 3px; }
 
    /* goto badge */
    .s4-goto-badge { display:inline-flex; align-items:center; gap:3px; background:#e6f4ea; color:#1e8e3e; border-radius:5px; padding:2px 7px; font-size:10px; font-weight:700; cursor:pointer; }
 
    /* usage highlight */
    .s4-sblock.s4-usage-hl { border-color:#e85d2f; box-shadow:0 0 0 3px #e85d2f33; }
 
    @media (max-width:900px) {
        #s4Wrap { grid-template-columns:1fr; height:auto; }
        #s4Left { border-right:none; border-bottom:1px solid var(--color-border-tertiary); max-height:380px; }
    }
    </style>
 
    <!-- question-preview popup -->
    <div id="s4QPreview">
        <div id="s4QPreviewType" style="margin-bottom:6px;"></div>
        <div id="s4QPreviewLabel" style="font-size:13px;font-weight:500;color:var(--color-text-primary);margin-bottom:10px;line-height:1.4;"></div>
        <div id="s4QPreviewField"></div>
        <div id="s4QPreviewInfo" style="display:none;margin-top:8px;font-size:11px;color:var(--color-text-tertiary);line-height:1.5;"></div>
    </div>
 
    <!-- main grid -->
    <div id="s4Wrap">
        <!-- LEFT: questions -->
        <div id="s4Left">
            <div class="s4-head">
                <div class="s4-head-row">
                    <span class="s4-panel-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#e85d2f" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Questionnaire
                        <span class="s4-count-badge" id="s4QCount">${questionnaireQuestions.length}</span>
                    </span>
                    <div style="margin-left:auto;display:flex;gap:6px;align-items:center;">
            
                    </div>
                </div>
                <div class="s4-head-row">
                <input type="checkbox" id="s4QSelectAll" title="Select all" onchange="s4ToggleSelectAllQ(this)" style="width:14px;height:14px;accent-color:#e85d2f;cursor:pointer;">
                <input type="text" class="form-control form-control-sm" placeholder="Search questions…" oninput="s4FilterQ(this.value)" style="flex:1;">
                <button class="s4-btn-save" style="padding:4px 10px;font-size:11px;" onclick="s4AddNewQuestion()">+ Add</button>
                    </div>
            </div>
            <div class="s4-bulk-bar" id="s4QBulkBar">
                <span id="s4QBulkCount">0 selected</span>
                <button class="s4-bulk-btn" onclick="s4BulkCopyQ()">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg> Copy
                </button>
                <button class="s4-bulk-btn" onclick="s4BulkDeleteQ()">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg> Delete
                </button>
                <button class="s4-bulk-close" onclick="s4ClearQSelection()">&times;</button>
            </div>
            <div class="s4-list" id="s4QuestionList"></div>
        </div>
 
        <!-- RIGHT: contract sections -->
        <div id="s4Right">
            <div class="s4-head">
                <div class="s4-head-row">
                    <span class="s4-panel-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#e85d2f" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Contract Sections
                        <span class="s4-count-badge" id="s4SCount">${contractTextItems.length}</span>
                    </span>
                    <div style="margin-left:auto;display:flex;gap:6px;align-items:center;">
                    </div>
                </div>
                <div class="s4-head-row">
                                        <input type="checkbox" id="s4SSelectAll" title="Select all sections" onchange="s4ToggleSelectAllS(this)" style="width:14px;height:14px;accent-color:#e85d2f;cursor:pointer;">

                    <input type="text" class="form-control form-control-sm" placeholder="Search sections…" oninput="s4FilterS(this.value)" style="flex:1;">
                                        <button class="s4-btn-save" style="padding:4px 10px;font-size:11px;" onclick="s4AddNewSection()">+ Add</button>

                    </div>
            </div>
            <div class="s4-bulk-bar" id="s4SBulkBar">
                <span id="s4SSBulkCount">0 selected</span>
                <button class="s4-bulk-btn" onclick="s4BulkCopyS()">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg> Copy
                </button>
                <button class="s4-bulk-btn" onclick="s4BulkDeleteS()">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg> Delete
                </button>
                <button class="s4-bulk-close" onclick="s4ClearSSelection()">&times;</button>
            </div>
            <div class="s4-list" id="s4SectionList"></div>
        </div>
    </div>
 
    <!-- QUESTION EDIT MODAL -->
    <div class="s4-modal" id="s4QModal">
        <div class="s4-mbox">
            <div class="s4-mhead">
                <div style="display:flex;align-items:center;gap:10px;flex:1;">
                    <span id="s4QModalQidBadge" style="display:none;font-family:monospace;font-size:12px;background:#fff4f0;color:#e85d2f;padding:2px 8px;border-radius:4px;font-weight:700;"></span>
                    <select id="s4QModalType" onchange="s4QModalTypeChange()" style="font-size:14px;font-weight:500;appearance:none;color:var(--color-text-secondary);background:transparent;border:0;cursor:pointer;outline:none;min-width:110px;padding:0 4px;">
                        <option value="text">Text Box</option>
                        <option value="textarea">Text Area</option>
                        <option value="radio">Radio Button</option>
                        <option value="select">Dropdown</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="date">Date</option>
                        <option value="number">Number</option>
                    </select>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color:var(--color-text-tertiary);flex-shrink:0;"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <button class="s4-mclose" onclick="s4CloseQModal()">&times;</button>
            </div>
              <div class="s4-mbody">
                <input type="hidden" id="s4QModalIdx">
                <div class="s4-fg">
                    <label class="s4-flabel">Question Label</label>
                    <textarea id="s4QModalLabel" class="form-control" rows="2" placeholder="e.g. What is the tenant's full name?"></textarea>
                </div>
                <div class="s4-fg" id="s4QModalPhWrap">
                    <label class="s4-flabel">Placeholder</label>
                    <input type="text" id="s4QModalPh" class="form-control" placeholder="Short answer text…">
                </div>
                <div class="s4-fg">
                    <label class="s4-flabel">Help Text</label>
                    <textarea id="s4QModalInfo" class="form-control" rows="2" placeholder="Optional guidance…"></textarea>
                </div>
                <div class="s4-fg" id="s4QModalOptsWrap" style="display:none;">
                    <label class="s4-flabel">Options</label>
                    <div id="s4QModalOptsList"></div>
                    <button type="button" onclick="s4AddQOpt()" style="margin-top:6px;font-size:11px;padding:4px 10px;background:#e6f4ea;border:1px solid #a7d7b7;border-radius:5px;cursor:pointer;color:#1e8e3e;font-weight:600;">+ Add Option</button>
                </div>
            </div>
            <div class="s4-mfoot">
                <button type="button" class="btn btn-sm btn-light" onclick="s4CloseQModal()" style="border:1px solid var(--color-border-tertiary);">Cancel</button>
                <button type="button" class="s4-btn-save" onclick="s4SaveQuestion()">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save Question
                </button>
            </div>
        </div>
    </div>
 
    <!-- SECTION EDIT MODAL -->
    <div class="s4-modal" id="s4SModal">
        <div class="s4-mbox">
            <div class="s4-mhead">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span id="s4SModalTidBadge" style="font-family:monospace;font-size:12px;background:#fff4f0;color:#e85d2f;padding:2px 8px;border-radius:4px;font-weight:700;"></span>
                    
                </div>
                <select id="s4SModalType" class="form-select form-select-sm" style="width:auto;font-size:14px;font-weight:500; min-width:110px; border:none;">
                        <option value="CONTENT">Content</option>
                        <option value="HEADLINE">Headline</option>
                        <option value="SIGNATURE">Signature</option>
                        <option value="LIST">List</option>
                        <option value="TABLE">Table</option>
                    </select>
                <button class="s4-mclose" onclick="s4CloseSModal()">&times;</button>
            </div>
            <div class="s4-mbody">
                <input type="hidden" id="s4SModalIdx">
                <div class="row g-2">
                    <div class="col-md-6 s4-fg">
                        <label class="s4-flabel">Section Name</label>
                        <input type="text" id="s4SModalName" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6 s4-fg">
                        <label class="s4-flabel">Alignment</label>
                        <select id="s4SModalAlign" class="form-select form-select-sm">
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                            <option value="justify">Justify</option>
                        </select>
                    </div>
                </div>
                <div class="s4-fg">
                    <label class="s4-flabel">Text Content</label>
                    <div id="s4SModalEditor" contenteditable="true" class="form-control" style="min-height:160px;max-height:320px;overflow-y:auto;font-size:13px;line-height:1.6;white-space:pre-wrap;word-break:break-word;" data-placeholder="Enter contract text here…"></div>
                    <textarea id="s4SModalContent" style="display:none;"></textarea>
                </div>
                <div class="s4-fg">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" id="s4SModalBlur" style="accent-color:#e85d2f;">
                        <label for="s4SModalBlur" class="s4-flabel" style="margin:0;cursor:pointer;">Blur Content</label>
                    </div>
                </div>
            </div>
            <div class="s4-mfoot">
                <button type="button" class="btn btn-sm btn-light" onclick="s4CloseSModal()" style="border:1px solid var(--color-border-tertiary);">Cancel</button>
                <button type="button" class="s4-btn-save" onclick="s4SaveSection()">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save Section
                </button>
            </div>
        </div>
    </div>`;
 
    s4RenderQuestions();
    s4RenderSections();
    s4InitQSortable();
    s4InitSSortable();
 
    ['s4QModal','s4SModal'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
    });
}

function s4Esc(s) {
    const d = document.createElement('div'); d.textContent = String(s||''); return d.innerHTML;
}
 
function s4ReplacePlaceholders(html) {
    if (!html) return '<em style="color:var(--color-text-tertiary);">Empty section</em>';
    return html.replace(/\{(QID\d+)\}/g, (_, token) => {
        const numId = token.replace(/^QID/i,'');
        const q = questionnaireQuestions.find(q => String(q.qid) === 'QID'+numId || String(q.id) === numId);
        const qid = q ? q.qid : token;
        return `<span class="s4-qvar" onclick="s4ScrollToQ('${qid}')" title="${q ? s4Esc(q.text||q.label||qid) : qid}">${qid}</span>`;
    });
}
 
function s4UpdateCounts() {
    const qc = document.getElementById('s4QCount');
    const sc = document.getElementById('s4SCount');
    if (qc) qc.textContent = questionnaireQuestions.length;
    if (sc) sc.textContent = contractTextItems.length;
}


function s4RenderQuestions(filter) {
    const list = document.getElementById('s4QuestionList');
    if (!list) return;
    s4UpdateCounts();
 
    const lc = (filter||'').toLowerCase();
    const items = questionnaireQuestions.filter(q =>
        !lc || (q.text||q.label||'').toLowerCase().includes(lc)
    );
 
    if (!items.length) {
        list.innerHTML = `<div style="text-align:center;color:var(--color-text-tertiary);padding:40px 14px;font-size:12px;">${filter ? 'No matching questions.' : 'No questions yet. Click <b>+ Add</b>.'}</div>`;
        return;
    }
 
    list.innerHTML = items.map(q => {
        const ri = questionnaireQuestions.indexOf(q);
        return s4RenderQCard(q, ri);
    }).join('');
 
    s4InitQSortable();
}
 
function s4RenderQCard(q, ri) {
    const label = q.text || q.label || '';
    const type = q.type || 'text';
    const typeLabel = type.replace(/-/g,' ').replace(/\b\w/g,c=>c.toUpperCase());
    const isSel = _s4QSelected.has(ri);
 
    // let gotoHtml = '';
    // if (q.goto && q.goto !== '') {
    //     const dest = q.goto === 'END' ? 'END' : q.goto;
    //     gotoHtml = `<div style="margin-top:4px; margin-top: 8px;
    //     padding-top: 8px;
    //     border-top: 1.5px solid #e5e7eb;
    //     display: flex;
    //     flex-direction: column;
    //     gap: 4px;">
    //     <span class="s4-goto-badge" onclick="s4ScrollToQ('${s4Esc(dest)}')">→ ${s4Esc(dest)}</span>
    //     </div>`;
    // }
    
 
    const fieldHtml = s4RenderInlineField(q, ri);
 
    return `<div class="s4-qcard${isSel?' s4-selected':''}" id="s4-qcard-${ri}">
        <div class="s4-qcard-top">
            <input type="checkbox" class="s4-qcb" data-ri="${ri}" ${isSel?'checked':''} onchange="s4ToggleQSelect(this,${ri})" onclick="event.stopPropagation();" style="width:13px;height:13px;accent-color:#e85d2f;cursor:pointer;flex-shrink:0;">
            <span class="s4-qid-badge" onclick="event.stopPropagation();s4ScrollToUsages('${s4Esc(q.qid||'QID'+(ri+1))}')">${s4Esc(q.qid||'QID'+(ri+1))}</span>
            <div class="s4-qcard-actions">
                <button class="s4-icon-btn" onclick="event.stopPropagation();s4CopyQ(${ri})" title="Copy"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg></button>
                <button class="s4-icon-btn" onclick="event.stopPropagation();s4PasteQ(${ri})" title="Paste after"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/></svg></button>
                <button class="s4-icon-btn" onclick="event.stopPropagation();s4EditQ(${ri})" title="Edit"><i class="fa fa-pencil" style="font-size:10px;"></i></button>
                <button class="s4-icon-btn s4-del" onclick="event.stopPropagation();s4DelQ(${ri})" title="Delete"><i class="fa fa-trash" style="font-size:10px;"></i></button>
                <button class="s4-icon-btn" onclick="event.stopPropagation();s4InsertQAfter(${ri})" title="Insert after" style="background:#e6f4ea;border-color:#a7d7b7;color:#1e8e3e;"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button>
                <span class="s4-drag-handle s4-qcard-drag" title="Drag"><svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="5" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="5" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="15" cy="19" r="1.5"/></svg></span>
            </div>
        </div>
        ${label ? `<div class="s4-qcard-label">${s4Esc(label)}${q.required ? ' <span style="color:#dc2626;font-size:11px;"></span>':''}</div>` : ''}
        <div class="s4-qfield-preview">
            <div class="s4-type-lbl">${typeLabel}</div>
            ${fieldHtml}
        </div>
    </div>`;
}
 
function s4RenderInlineField(q, ri) {
    const ph = q.userinfo || '';
    const opts = Array.isArray(q.options) ? q.options : [];
    const fStyle = 'width:100%;padding:5px 8px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:11.5px;color:var(--color-text-primary);background:var(--color-background-primary);outline:none;font-family:inherit;box-sizing:border-box; max-height:31px;';
 
    const infoBtn = `
    <button class="s4-icon-btn" style="width:20px;height:20px;margin-left:auto;background:#fff4f0; border-color:#e85d2f;  color:#6b7280;" onmouseenter="this.style.background='#FFF';this.style.color='#FD5602';this.style.borderColor='#FFF';s4ShowPreview(event,${ri})" onmouseleave="this.style.background='#fff4f0';this.style.color='#e85d2f';this.style.borderColor='#e85d2f';s4HidePreview()"><svg width="12" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></button>`; 
    switch (q.type) {
        case 'radio': case 'radio-button':
            if (!opts.length) return `<div style="display:flex;justify-content:flex-end;">${infoBtn}</div><span style="font-size:11px;color:var(--color-text-tertiary);font-style:italic;">No options</span>`;
            return `<div style="display:flex;justify-content:flex-end;">${infoBtn}</div><div class="s4-radio-grp">${opts.map(o=>`<label class="s4-radio-lbl"><input type="radio" name="s4q${ri}" style="accent-color:#e85d2f;width:12px;height:12px;">${s4Esc(o.label||o)}</label>`).join('')}</div>`;
        case 'select': case 'dropdown':
            return `<div style="display:flex;justify-content:flex-end;">${infoBtn}</div><select style="${fStyle}cursor:pointer;"><option>— Select —</option>${opts.map(o=>`<option>${s4Esc(o.label||o)}</option>`).join('')}</select>`;
        case 'checkbox':
            if (!opts.length) return `<div style="display:flex;justify-content:flex-end;">${infoBtn}</div><label class="s4-check-lbl"><input type="checkbox" style="accent-color:#e85d2f;width:12px;height:12px;"> ${s4Esc(q.text||'Checkbox')}</label>`;
            return `<div style="display:flex;justify-content:flex-end;">${infoBtn}</div><div class="s4-check-grp">${opts.map(o=>`<label class="s4-check-lbl"><input type="checkbox" style="accent-color:#e85d2f;width:12px;height:12px;">${s4Esc(o.label||o)}</label>`).join('')}</div>`;
        case 'date':
            return `<div style="display:flex;justify-content:flex-end;">${infoBtn}</div><input type="date" style="${fStyle}">`;
        case 'number':
            return `<div style="display:flex;justify-content:flex-end;">${infoBtn}</div><input type="number" placeholder="${s4Esc(ph||'0')}" style="${fStyle}">`;
        case 'textarea':
            return `<div style="display:flex;justify-content:flex-end;">${infoBtn}</div><textarea rows="2" style="${fStyle}resize:vertical;"></textarea>`;
        default:
            return `<div style="display:flex;justify-content:flex-end;">${infoBtn}</div><input type="text" style="${fStyle}">`;
    }
}
 
/* ── question preview popup ── */
// function s4ShowPreview(e, ri) {
//     const q = questionnaireQuestions[ri];
//     if (!q) return;
//     const pop = document.getElementById('s4QPreview');
//     const typeLabel = (q.type||'text').replace(/-/g,' ').replace(/\b\w/g,c=>c.toUpperCase());
//     document.getElementById('s4QPreviewType').innerHTML = `<span style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--color-text-tertiary);background-color:#fff;">${typeLabel}</span>`;
//     document.getElementById('s4QPreviewLabel').textContent = q.text || q.label || '';
//     document.getElementById('s4QPreviewField').textContent = q.userinfo ? '' : '';
//     const infoEl = document.getElementById('s4QPreviewInfo');
//     if (q.userinfo) { infoEl.style.display='flex'; infoEl.textContent = q.userinfo; }
//     else { infoEl.style.display='none'; }
//     pop.style.display = 'block';
//     const rect = e.currentTarget.getBoundingClientRect();
//     const pw = 280, ph2 = 160;
//     let left = rect.right + 8, top = rect.top - 10;
//     if (left + pw > window.innerWidth - 8) left = rect.left - pw - 8;
//     if (top + ph2 > window.innerHeight - 8) top = window.innerHeight - ph2 - 8;
//     if (top < 8) top = 8;
//     pop.style.left = left + 'px';
//     pop.style.top  = top  + 'px';
// }

function s4ShowPreview(e, ri) {
    const q = questionnaireQuestions[ri];
    if (!q) return;
    const pop = document.getElementById('s4QPreview');
    const typeLabel = (q.type||'text').replace(/-/g,' ').replace(/\b\w/g,c=>c.toUpperCase());

    // document.getElementById('s4QPreviewType').innerHTML = `
    //     <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;
    //         background:#fff4f0;color:#e85d2f;border:1px solid #e85d2f;
    //         border-radius:4px;padding:2px 8px;display:inline-block;">
    //         ${typeLabel}
    //     </span>`;

    // document.getElementById('s4QPreviewLabel').innerHTML = `
    //     <div style="font-size:13px;font-weight:600;color:#1f2937;margin:8px 0 4px;line-height:1.5;">
    //         ${s4Esc(q.text || q.label || '')}
    //         ${q.required ? '<span style="color:#e85d2f;margin-left:2px;">*</span>' : ''}
    //     </div>`;

    const infoEl = document.getElementById('s4QPreviewInfo');
    if (q.userinfo && q.userinfo.trim()) {
        infoEl.style.display = 'flex';
        infoEl.style.alignItems = 'flex-start';
        infoEl.style.gap = '5px';
        infoEl.innerHTML = `
            <span style="font-size:11px;color:#6b7280;line-height:1.5;">${s4Esc(q.userinfo)}</span>`;
    } else {
        infoEl.style.display = 'none';
        infoEl.innerHTML = '';
    }

    // QID badge at bottom
    // document.getElementById('s4QPreviewField').innerHTML = `
    //     <div style="margin-top:8px;padding-top:8px;border-top:1px solid #f3f4f6;
    //         display:flex;align-items:center;gap:6px;">
    //         <span style="font-size:10px;font-weight:700;font-family:monospace;
    //             background:#fff4f0;color:#e85d2f;border:1px solidrgb(97, 95, 95);
    //             border-radius:4px;padding:1px 6px;">${s4Esc(q.qid||'')}</span>
    //         ${q.section_name ? `<span style="font-size:10px;color:#9ca3af;">${s4Esc(q.section_name)}</span>` : ''}
    //     </div>`;

    // pop.style.cssText += 'border-left:3px solid #e85d2f;';
    pop.style.display = 'block';

    const rect = e.currentTarget.getBoundingClientRect();
    const pw = 290, ph2 = 180;
    let left = rect.right + 10, top = rect.top - 10;
    if (left + pw > window.innerWidth - 8) left = rect.left - pw - 10;
    if (top + ph2 > window.innerHeight - 8) top = window.innerHeight - ph2 - 8;
    if (top < 8) top = 8;
    pop.style.left = left + 'px';
    pop.style.top  = top  + 'px';
}


function s4HidePreview() { document.getElementById('s4QPreview').style.display = 'none'; }
 
/* ── scroll to a question by qid ── */
function s4ScrollToQ(qid) {
    const idx = questionnaireQuestions.findIndex(q => q.qid === qid || String(q.id) === String(qid));
    if (idx < 0) return;
    const card = document.getElementById(`s4-qcard-${idx}`);
    if (!card) return;
    card.style.transition = 'box-shadow .2s, border-color .2s';
    card.style.boxShadow  = '0 0 0 3px #e85d2f55';
    card.style.borderColor = '#e85d2f';
    card.scrollIntoView({ behavior:'smooth', block:'center' });
    setTimeout(() => { card.style.boxShadow=''; card.style.borderColor=''; }, 3000);
}
 
/* ── scroll section list to usages of a qid ── */
let _s4UsageScrollState = {};
function s4ScrollToUsages(qid) {
    const list = document.getElementById('s4SectionList');
    if (!list) return;
    const blocks = Array.from(list.querySelectorAll('.s4-sblock')).filter(b => {
        const idx = parseInt(b.getAttribute('data-idx'));
        const item = contractTextItems[idx];
        return item && item.text && item.text.includes(`{${qid}}`);
    });
    if (!blocks.length) return;
    const state = _s4UsageScrollState[qid] || { idx:0 };
    const target = blocks[state.idx % blocks.length];
    list.querySelectorAll('.s4-sblock.s4-usage-hl').forEach(b => b.classList.remove('s4-usage-hl'));
    target.classList.add('s4-usage-hl');
    target.scrollIntoView({ behavior:'smooth', block:'center' });
    state.idx = (state.idx + 1) % blocks.length;
    _s4UsageScrollState[qid] = state;
    setTimeout(() => target.classList.remove('s4-usage-hl'), 4000);
}
 
/* ── filter ── */
function s4FilterQ(v) { s4RenderQuestions(v); }
function s4FilterS(v) {
    const list = document.getElementById('s4SectionList');
    if (!list) return;
    const lc = v.toLowerCase();
    list.querySelectorAll('.s4-sblock').forEach(b => {
        const idx = parseInt(b.getAttribute('data-idx'));
        const item = contractTextItems[idx];
        const hay = ((item.text||'')+(item.section_name||'')+(item.tid||'')).toLowerCase();
        b.style.display = !lc || hay.includes(lc) ? '' : 'none';
    });
}
 
/* ── select / bulk ── */
function s4ToggleQSelect(cb, ri) {
    if (cb.checked) _s4QSelected.add(ri); else _s4QSelected.delete(ri);
    const card = document.getElementById(`s4-qcard-${ri}`);
    if (card) card.classList.toggle('s4-selected', cb.checked);
    s4UpdateQBulkBar();
}
function s4ToggleSelectAllQ(masterCb) {
    _s4QSelected.clear();
    document.querySelectorAll('.s4-qcb').forEach(cb => {
        cb.checked = masterCb.checked;
        const ri = parseInt(cb.getAttribute('data-ri'));
        if (masterCb.checked) _s4QSelected.add(ri);
        const card = document.getElementById(`s4-qcard-${ri}`);
        if (card) card.classList.toggle('s4-selected', masterCb.checked);
    });
    s4UpdateQBulkBar();
}
function s4UpdateQBulkBar() {
    const bar = document.getElementById('s4QBulkBar');
    const cnt = _s4QSelected.size;
    if (bar) { bar.classList.toggle('open', cnt > 0); }
    const el = document.getElementById('s4QBulkCount');
    if (el) el.textContent = cnt + ' selected';
}
function s4ClearQSelection() {
    _s4QSelected.clear();
    document.querySelectorAll('.s4-qcb').forEach(cb => cb.checked = false);
    document.querySelectorAll('.s4-qcard.s4-selected').forEach(c => c.classList.remove('s4-selected'));
    const master = document.getElementById('s4QSelectAll');
    if (master) master.checked = false;
    s4UpdateQBulkBar();
}
function s4BulkCopyQ() {
    if (!_s4QSelected.size) return;
    const indices = Array.from(_s4QSelected).sort((a,b)=>a-b);
    _s4QClipboard = { bulk: indices.map(i => JSON.parse(JSON.stringify(questionnaireQuestions[i]))) };
    s4ClearQSelection();
}
function s4BulkDeleteQ() {
    if (!_s4QSelected.size || !confirm(`Delete ${_s4QSelected.size} question(s)?`)) return;
    Array.from(_s4QSelected).sort((a,b)=>b-a).forEach(i => questionnaireQuestions.splice(i,1));
    fixInvalidGotoReferences();
    _s4QSelected.clear();
    s4UpdateQBulkBar();
    s4RenderQuestions();
    s4RenderSections();
}
 
/* section select / bulk */
function s4ToggleSSelect(cb, idx) {
    if (cb.checked) _s4SSelected.add(idx); else _s4SSelected.delete(idx);
    const b = document.getElementById(`s4-sblock-${idx}`);
    if (b) b.classList.toggle('s4-selected', cb.checked);
    s4UpdateSBulkBar();
}
function s4ToggleSelectAllS(masterCb) {
    _s4SSelected.clear();
    document.querySelectorAll('.s4-scb').forEach(cb => {
        cb.checked = masterCb.checked;
        const idx = parseInt(cb.getAttribute('data-idx'));
        if (masterCb.checked) _s4SSelected.add(idx);
        const b = document.getElementById(`s4-sblock-${idx}`);
        if (b) b.classList.toggle('s4-selected', masterCb.checked);
    });
    s4UpdateSBulkBar();
}
function s4UpdateSBulkBar() {
    const bar = document.getElementById('s4SBulkBar');
    const cnt = _s4SSelected.size;
    if (bar) bar.classList.toggle('open', cnt > 0);
    const el = document.getElementById('s4SSBulkCount');
    if (el) el.textContent = cnt + ' selected';
}
function s4ClearSSelection() {
    _s4SSelected.clear();
    document.querySelectorAll('.s4-scb').forEach(cb => cb.checked = false);
    document.querySelectorAll('.s4-sblock.s4-selected').forEach(b => b.classList.remove('s4-selected'));
    const master = document.getElementById('s4SSelectAll');
    if (master) master.checked = false;
    s4UpdateSBulkBar();
}
function s4BulkCopyS() {
    if (!_s4SSelected.size) return;
    const indices = Array.from(_s4SSelected).sort((a,b)=>a-b);
    _s4SClipboard = { bulk: indices.map(i => JSON.parse(JSON.stringify(contractTextItems[i]))) };
    s4ClearSSelection();
}
function s4BulkDeleteS() {
    if (!_s4SSelected.size || !confirm(`Delete ${_s4SSelected.size} section(s)?`)) return;
    Array.from(_s4SSelected).sort((a,b)=>b-a).forEach(i => contractTextItems.splice(i,1));
    saveContractChanges();
    _s4SSelected.clear();
    s4UpdateSBulkBar();
    s4RenderSections();
}
 
/* ── copy / paste questions ── */
function s4CopyQ(ri) {
    _s4QClipboard = { single: JSON.parse(JSON.stringify(questionnaireQuestions[ri])) };
}
function s4PasteQ(ri) {
    if (!_s4QClipboard) return;
    const items = _s4QClipboard.bulk || [_s4QClipboard.single];
    items.forEach((q, i) => {
        const copy = JSON.parse(JSON.stringify(q));
        copy.qid = `QID${questionnaireQuestions.length + 1}`;
        copy.goto = '';
        questionnaireQuestions.splice(ri + 1 + i, 0, copy);
    });
    fixInvalidGotoReferences();
    s4RenderQuestions();
    s4RenderSections();
}
function s4InsertQAfter(ri) {
    questionnaireQuestions.splice(ri + 1, 0, {
        id: Date.now(), qid: `QID${questionnaireQuestions.length+1}`,
        text:'New Question', type:'text', options:[], required:true, goto:'', userinfo:'', section_name:''
    });
    fixInvalidGotoReferences();
    s4RenderQuestions();
    setTimeout(() => s4EditQ(ri+1), 60);
}
 
/* ── CRUD: questions ── */
function s4DelQ(ri) {
    if (!confirm('Delete this question?')) return;
    questionnaireQuestions.splice(ri, 1);
    fixInvalidGotoReferences();
    s4RenderQuestions();
    s4RenderSections();
}
function s4AddNewQuestion() {
    editingQuestionIndex = null;
    s4OpenQModal(null);
}
function s4EditQ(ri) {
    editingQuestionIndex = ri;
    s4OpenQModal(questionnaireQuestions[ri]);
}
function s4OpenQModal(q) {
    const badge = document.getElementById('s4QModalQidBadge');
    if (q) {
        badge.textContent = q.qid; badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
    document.getElementById('s4QModalType').value  = (q && q.type) || 'text';
    document.getElementById('s4QModalLabel').value = (q && (q.text || q.label)) || '';
    document.getElementById('s4QModalPh').value    = (q && q.userinfo) || '';
    document.getElementById('s4QModalInfo').value  = (q && q.userinfo) || '';

    const ol = document.getElementById('s4QModalOptsList');
    if (ol) {
        ol.innerHTML = '';
        if (q && Array.isArray(q.options)) {
            q.options.forEach((o) => s4AppendQOptRow(o.label || o, o.value || o));
        }
    }

    s4QModalTypeChange();
    document.getElementById('s4QModal').classList.add('open');
}
 
function s4QModalTypeChange() {
    const t = document.getElementById('s4QModalType').value;
    const hasOpts = ['radio', 'radio-button', 'select', 'checkbox'].includes(t);
    const hasPh = ['text', 'textarea', 'number'].includes(t);
    const phWrap = document.getElementById('s4QModalPhWrap');
    const optsWrap = document.getElementById('s4QModalOptsWrap');
    if (phWrap) phWrap.style.display = hasPh ? '' : 'none';
    if (optsWrap) optsWrap.style.display = hasOpts ? '' : 'none';
}
 
function s4AppendQOptRow(label, value) {
    const ol = document.getElementById('s4QModalOptsList');
    const d = document.createElement('div');
    d.style.cssText = 'display:flex;gap:6px;margin-bottom:6px;align-items:center;';
    const iStyle = 'padding:5px 8px;border:1.5px solid var(--color-border-tertiary);border-radius:5px;font-size:12px;color:var(--color-text-primary);background:var(--color-background-primary);outline:none;font-family:inherit;box-sizing:border-box;flex:1; border:1px solid #e5e7eb;';
    d.innerHTML = `<input type="text" placeholder="Label" value="${s4Esc(label||'')}" class="s4opt-label" style="${iStyle}">
        <input type="text" placeholder="Value" value="${s4Esc(value||'')}" class="s4opt-value" style="${iStyle}">
        <button type="button" onclick="this.closest('div').remove()" style="flex-shrink:0;width:24px;height:24px;background:var(--color-background-secondary);border:1px solid var(--color-border-tertiary);border-radius:5px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--color-text-tertiary);" onmouseover="this.style.background='#fde8e8';this.style.color='#dc2626'" onmouseout="this.style.background='';this.style.color=''"><i class="fa fa-trash" style="font-size:9px;"></i></button>`;
    ol.appendChild(d);
}
function s4AddQOpt() {
    const ol = document.getElementById('s4QModalOptsList');
    if (ol) s4AppendQOptRow('', '');
} 
function s4CloseQModal() { document.getElementById('s4QModal').classList.remove('open'); }
 
function s4SaveQuestion() {
    const label = document.getElementById('s4QModalLabel').value.trim();
    if (!label) { alert('Question label is required.'); return; }

    const opts = [];
    const optsList = document.getElementById('s4QModalOptsList');
    if (optsList) {
        optsList.querySelectorAll('div').forEach(row => {
            const l = row.querySelector('.s4opt-label');
            const v = row.querySelector('.s4opt-value');
            if (l && l.value.trim()) opts.push({ label: l.value.trim(), value: (v && v.value.trim()) || l.value.trim() });
        });
    }

    const qObj = {
        type     : document.getElementById('s4QModalType').value,
        text     : label,
        label    : label,
        userinfo : document.getElementById('s4QModalInfo').value.trim(),
        options  : opts,
        section_name: ''
    };

    if (editingQuestionIndex === null) {
        qObj.id  = Date.now();
        qObj.qid = `QID${questionnaireQuestions.length + 1}`;
        questionnaireQuestions.push(qObj);
    } else {
        const ex = questionnaireQuestions[editingQuestionIndex];
        qObj.id  = ex.id;
        qObj.qid = ex.qid;
        Object.assign(ex, qObj);
    }

    fixInvalidGotoReferences();
    s4CloseQModal();
    s4RenderQuestions();
    s4RenderSections();
}
 
/* ── sortable questions ── */
let _s4QSortable = null;
function s4InitQSortable() {
    const el = document.getElementById('s4QuestionList');
    if (!el || !window.Sortable) return;
    if (_s4QSortable) _s4QSortable.destroy();
    _s4QSortable = new Sortable(el, {
        animation:150, handle:'.s4-qcard-drag', ghostClass:'sortable-ghost', chosenClass:'sortable-chosen',
        onEnd(evt) {
            if (evt.oldIndex === evt.newIndex) return;
            const moved = questionnaireQuestions.splice(evt.oldIndex, 1)[0];
            questionnaireQuestions.splice(evt.newIndex, 0, moved);
            fixInvalidGotoReferences();
            const st = el.scrollTop;
            s4RenderQuestions();
            el.scrollTop = st;
        }
    });
}

function s4RenderSections() {
    const list = document.getElementById('s4SectionList');
    if (!list) return;
    s4UpdateCounts();
 
    if (!contractTextItems.length) {
        list.innerHTML = `<div style="text-align:center;color:var(--color-text-tertiary);padding:40px 14px;font-size:12px;">No sections yet. Click <b>+ Add</b>.</div>`;
        return;
    }
 
    list.innerHTML = contractTextItems.map((item, idx) => s4RenderSBlock(item, idx)).join('');
    s4InitSSortable();
}
 
function s4RenderSBlock(item, idx) {
    const isSel = _s4SSelected.has(idx);
    const rendered = s4ReplacePlaceholders(item.text || '');
    const blurStyle = item.blur_content ? 'filter:blur(4px);user-select:none;' : '';
    const alignStyle = item.align_text || 'left';
    const hasConds = item.conditions && item.conditions.length > 0;
 
    let condHtml = '';
    if (hasConds) {
        const parts = item.conditions.map(c => {
            const q = questionnaireQuestions.find(q => q.qid === c.question_id);
            return `<span class="s4-cond-badge" onclick="s4ScrollToQ('${s4Esc(c.question_id||'')}')">${s4Esc(c.question_id||'?')}</span><span style="font-size:10px;color:var(--color-text-tertiary);background:var(--color-background-secondary);border-radius:4px;padding:1px 5px;">${s4Esc(c.conditions||'=')} ${s4Esc(c.question_value||'')}</span>`;
        }).join('<span style="font-size:9px;color:var(--color-text-tertiary);font-weight:700;padding:0 2px;">AND</span>');
        condHtml = `<div class="s4-cond-row">${parts}</div>`;
    }
 
    return `<div class="s4-sblock${isSel?' s4-selected':''}" id="s4-sblock-${idx}" data-idx="${idx}">
        <div class="s4-sblock-top">
            <input type="checkbox" class="s4-scb" data-idx="${idx}" ${isSel?'checked':''} onchange="s4ToggleSSelect(this,${idx})" onclick="event.stopPropagation();" style="width:13px;height:13px;accent-color:#e85d2f;cursor:pointer;flex-shrink:0;">
            <span class="s4-tid-badge">${s4Esc(item.tid)}</span>
            <div class="s4-sblock-actions">
                <button class="s4-icon-btn" onclick="event.stopPropagation();s4CopyS(${idx})" title="Copy"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg></button>
                <button class="s4-icon-btn" onclick="event.stopPropagation();s4PasteS(${idx})" title="Paste after"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/></svg></button>
                <button class="s4-icon-btn" onclick="event.stopPropagation();s4EditS(${idx})" title="Edit"><i class="fa fa-pencil" style="font-size:10px;"></i></button>
                <button class="s4-icon-btn s4-del" onclick="event.stopPropagation();s4DelS(${idx})" title="Delete"><i class="fa fa-trash" style="font-size:10px;"></i></button>
                <button class="s4-icon-btn" onclick="event.stopPropagation();s4InsertSAfter(${idx})" title="Insert section after" style="background:#e6f4ea;border-color:#a7d7b7;color:#1e8e3e;"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button>
                <span class="s4-drag-handle s4-sblock-drag" title="Drag"><svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="5" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="5" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="15" cy="19" r="1.5"/></svg></span>
            </div>
        </div>
        <hr class="s4-sblock-divider">
        <div class="s4-sblock-content s4-rendered" style="text-align:${alignStyle};${blurStyle}">${rendered}</div>
        ${hasConds ? '<hr class="s4-sblock-divider">'+condHtml : ''}
    </div>`;
}
 
function s4CopyS(idx) {
    _s4SClipboard = { single: JSON.parse(JSON.stringify(contractTextItems[idx])) };
}
function s4PasteS(idx) {
    if (!_s4SClipboard) return;
    const items2 = _s4SClipboard.bulk || [_s4SClipboard.single];
    const maxTid = contractTextItems.length ? Math.max(...contractTextItems.map(i=>parseInt(i.tid.replace(/\D/g,''))||0)) : 0;
    items2.forEach((item, i) => {
        const copy = JSON.parse(JSON.stringify(item));
        copy.tid = `TID${maxTid + i + 1}`;
        contractTextItems.splice(idx + 1 + i, 0, copy);
    });
    saveContractChanges();
    s4RenderSections();
}
function s4InsertSAfter(idx) {
    const maxTid = contractTextItems.length ? Math.max(...contractTextItems.map(i=>parseInt(i.tid.replace(/\D/g,''))||0)) : 0;
    const newS = { tid:`TID${maxTid+1}`, section_key:'New_Section', section_id:null, section_name:'New Section', type:'CONTENT', text:'', align_text:'left', blur_content:false, conditions:[] };
    contractTextItems.splice(idx+1, 0, newS);
    saveContractChanges();
    s4RenderSections();
    setTimeout(() => s4EditS(idx+1), 60);
}
 
/* ── CRUD: sections ── */
function s4DelS(idx) {
    if (!confirm('Delete this section?')) return;
    contractTextItems.splice(idx, 1);
    saveContractChanges();
    s4RenderSections();
}
function s4AddNewSection() {
    editingContractIndex = null;
    s4OpenSModal(null);
}
function s4EditS(idx) {
    editingContractIndex = idx;
    s4OpenSModal(contractTextItems[idx]);
}
function s4OpenSModal(item) {
    const badge = document.getElementById('s4SModalTidBadge');
    badge.textContent = item ? item.tid : '(new)';
    document.getElementById('s4SModalType').value  = (item&&item.type) || 'CONTENT';
    document.getElementById('s4SModalName').value  = (item&&item.section_name) || '';
    document.getElementById('s4SModalAlign').value = (item&&item.align_text) || 'left';
    document.getElementById('s4SModalBlur').checked = !!(item&&item.blur_content);
    document.getElementById('s4SModalIdx').value = editingContractIndex !== null ? editingContractIndex : '';
 
    /* rich editor value */
    const editor = document.getElementById('s4SModalEditor');
    const raw = (item && item.text) || '';
    /* convert {QIDx} tokens to styled badges */
    const withBadges = raw.replace(/\{(QID\d+)\}/g, (_, token) => {
        const numId = token.replace(/^QID/i,'');
        const q = questionnaireQuestions.find(q => q.qid === token || String(q.id)===numId);
        const lbl = q ? (q.text||q.label||token).substring(0,20) : token;
        return `<span class="s4-qvar" data-qid="${token}" contenteditable="false" title="${s4Esc(lbl)}">${token}</span>`;
    });
    editor.innerHTML = withBadges;
 
    /* QID picker */
    // const picker = document.getElementById('s4QidPicker');
    // picker.innerHTML = questionnaireQuestions.map(q =>
    //     `<button type="button" onclick="s4InsertQidIntoEditor('${s4Esc(q.qid)}')" style="font-size:10px;background:#fff4f0;border:1px solid #fca5a5;border-radius:4px;padding:2px 7px;cursor:pointer;color:#e85d2f;font-weight:700;font-family:monospace;" title="${s4Esc(q.text||q.label||q.qid)}">${s4Esc(q.qid)}</button>`
    // ).join('');
 
    document.getElementById('s4SModal').classList.add('open');
    setTimeout(() => editor.focus(), 100);
}
 
function s4InsertQidIntoEditor(qid) {
    const editor = document.getElementById('s4SModalEditor');
    editor.focus();
    const badge = document.createElement('span');
    badge.className = 's4-qvar';
    badge.setAttribute('data-qid', qid);
    badge.setAttribute('contenteditable','false');
    badge.textContent = qid;
    const sel = window.getSelection();
    if (sel && sel.rangeCount) {
        const range = sel.getRangeAt(0);
        range.deleteContents();
        range.insertNode(badge);
        range.setStartAfter(badge);
        range.collapse(true);
        sel.removeAllRanges();
        sel.addRange(range);
    } else {
        editor.appendChild(badge);
    }
}
 
function s4GetEditorContent() {
    const editor = document.getElementById('s4SModalEditor');
    const clone = editor.cloneNode(true);
    clone.querySelectorAll('.s4-qvar[data-qid]').forEach(b => {
        const qid = b.getAttribute('data-qid');
        const text = document.createTextNode(`{${qid}}`);
        b.parentNode.replaceChild(text, b);
    });
    return clone.innerHTML;
}
 
function s4CloseSModal() { document.getElementById('s4SModal').classList.remove('open'); }
 
function s4SaveSection() {
    const idx = document.getElementById('s4SModalIdx').value;
    const content = s4GetEditorContent();
    const sObj = {
        section_name : document.getElementById('s4SModalName').value.trim() || 'Section',
        section_key  : (document.getElementById('s4SModalName').value.trim()||'Section').replace(/\s+/g,'_'),
        type         : document.getElementById('s4SModalType').value,
        text         : content,
        align_text   : document.getElementById('s4SModalAlign').value,
        blur_content : document.getElementById('s4SModalBlur').checked,
        conditions   : []
    };
 
    if (idx === '' || editingContractIndex === null) {
        const maxTid = contractTextItems.length ? Math.max(...contractTextItems.map(i=>parseInt(i.tid.replace(/\D/g,''))||0)) : 0;
        sObj.tid = `TID${maxTid+1}`;
        sObj.section_id = null;
        contractTextItems.push(sObj);
    } else {
        const ex = contractTextItems[parseInt(idx)];
        sObj.tid = ex.tid;
        sObj.section_id = ex.section_id;
        Object.assign(ex, sObj);
    }
 
    saveContractChanges();
    s4CloseSModal();
    s4RenderSections();
}
 
/* ── sortable sections ── */
let _s4SSortable = null;
function s4InitSSortable() {
    const el = document.getElementById('s4SectionList');
    if (!el || !window.Sortable) return;
    if (_s4SSortable) _s4SSortable.destroy();
    _s4SSortable = new Sortable(el, {
        animation:150, handle:'.s4-sblock-drag', ghostClass:'sortable-ghost', chosenClass:'sortable-chosen',
        onEnd(evt) {
            if (evt.oldIndex === evt.newIndex) return;
            const moved = contractTextItems.splice(evt.oldIndex, 1)[0];
            contractTextItems.splice(evt.newIndex, 0, moved);
            saveContractChanges();
            const st = el.scrollTop;
            s4RenderSections();
            el.scrollTop = st;
        }
    });
}

function renderContractCard(item, index) {
    const textPreview = item.text.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim()
    const truncatedText = textPreview.length > 35 ? textPreview.substring(0, 35) + '...' : textPreview
    const isActive = editingContractIndex === index
    const hasConditions = item.conditions && item.conditions.length > 0

    return `<div class="item-card ${isActive ? 'active' : ''}" data-index="${index}" id="contract-card-${index}" onclick="selectContractItemForEdit(${index})">
        <div class="card-header">
            <div class="drag-handle-compact">
                <i class="fas fa-grip-vertical"></i>
            </div>
            <span class="id-badge">${escapeHtml(item.tid)}</span>
            <div class="card-actions">
                <button class="icon-btn" onclick="event.stopPropagation(); duplicateContractItem(${index})" title="Duplicate">
                    <i class="fas fa-copy"></i>
                </button>
                <button class="icon-btn danger" onclick="event.stopPropagation(); deleteContractItem(${index})" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="section-name">${escapeHtml(item.section_name)}</div>
            <p class="item-preview">${escapeHtml(truncatedText) || '<em>Empty</em>'}</p>
            <div class="item-meta">
                <span class="type-badge ${item.type.toLowerCase()}">${item.type}</span>
                ${hasConditions ? '<span class="condition-badge" title="Has conditions"><i class="fas fa-filter"></i> ' + item.conditions.length + '</span>' : ''}
            </div>
        </div>
    </div>`
}

function selectContractItemForEdit(index) {
    editingContractIndex = index

    document.querySelectorAll('#contractSortableList .item-card').forEach((card, i) => {
        card.classList.toggle('active', i === index)
    })

    const item = contractTextItems[index]
    const panel = document.getElementById("contractEditPanel")

    panel.innerHTML = `
        <div class="edit-panel-content">
            <div class="edit-panel-header">
                <h6><i class="fas fa-edit"></i> Edit Section</h6>
                <span class="badge bg-primary">${escapeHtml(item.tid)}</span>
            </div>

            <div class="edit-form">
                <div class="form-row">
                    <div class="form-group half">
                    <label>Text ID (TID)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">TID</span>
                            <input type="text" 
                                class="form-control form-control-sm" 
                                value="${escapeHtml(item.tid.replace('TID', ''))}"
                                oninput="this.value = this.value.replace(/[^0-9]/g,'')"
                                onchange="updateContractItem(${index}, 'tid', this.value)"
                            >
                        </div>
                    </div>
                    <div class="form-group half">
                        <label>Section Key</label>
                        <input type="text" class="form-control form-control-sm" value="${escapeHtml(item.section_key)}"
                            onchange="updateContractItem(${index}, 'section_key', this.value)">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label>Section Name</label>
                        <input type="text" class="form-control form-control-sm" 
                            value="${escapeHtml(item.section_name)}"
                            onchange="updateContractItem(${index}, 'section_name', this.value)">
                    </div>
                    <div class="form-group half">
                        <label>Type</label>
                        <select class="form-select form-select-sm" onchange="updateContractItem(${index}, 'type', this.value)">
                            <option value="HEADLINE"${item.type === "HEADLINE" ? " selected" : ""}>Headline</option>
                            <option value="CONTENT"${item.type === "CONTENT" ? " selected" : ""}>Content</option>
                            <option value="LIST"${item.type === "LIST" ? " selected" : ""}>List</option>
                            <option value="TABLE"${item.type === "TABLE" ? " selected" : ""}>Table</option>
                            <option value="SIGNATURE"${item.type === "SIGNATURE" ? " selected" : ""}>Signature</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Text Content <small class="text-muted">(HTML allowed, use {QID123} for variables)</small></label>
                    <textarea class="form-control form-control-sm contract-text-editor" rows="6"
                        id="contractTextArea-${index}"
                        onchange="updateContractItem(${index}, 'text', this.value)">${escapeHtml(item.text)}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label>Text Alignment</label>
                        <select class="form-select form-select-sm" onchange="updateContractItem(${index}, 'align_text', this.value)">
                            <option value="left"${item.align_text === "left" ? " selected" : ""}>Left</option>
                            <option value="center"${item.align_text === "center" ? " selected" : ""}>Center</option>
                            <option value="right"${item.align_text === "right" ? " selected" : ""}>Right</option>
                            <option value="justify"${item.align_text === "justify" ? " selected" : ""}>Justify</option>
                        </select>
                    </div>
                    <div class="form-group half">
                        <label>Blur Content</label>
                        <select class="form-select form-select-sm" onchange="updateContractItem(${index}, 'blur_content', this.value === 'true')">
                            <option value="false"${!item.blur_content ? " selected" : ""}>No</option>
                            <option value="true"${item.blur_content ? " selected" : ""}>Yes</option>
                        </select>
                    </div>
                </div>

                <div class="form-group conditions-section">
                    <label>
                        <i class="fas fa-filter"></i> Conditions 
                        <small class="text-muted">(Show only when conditions are met)</small>
                    </label>
                    <div class="conditions-list" id="conditions-list-${index}">
                        ${renderConditionsList(item.conditions, index)}
                    </div>
                    <button class="btn btn-sm btn-outline-success" onclick="addContractCondition(${index})">
                        <i class="fas fa-plus"></i> Add Condition
                    </button>
                </div>

                <div class="preview-section">
                    <label><i class="fas fa-eye"></i> Preview</label>
                    <div class="preview-box" style="text-align: ${item.align_text}; ${item.blur_content ? 'filter: blur(4px);' : ''}">
                        ${item.text || '<em class="text-muted">No content</em>'}
                    </div>
                </div>
            </div>
        </div>
    `
}

function renderConditionsList(conditions, itemIndex) {
    if (!conditions || conditions.length === 0) {
        return '<p class="text-muted small mb-2">No conditions. This section will always display.</p>'
    }

    return conditions.map((cond, condIndex) => {
        const qidValue = cond.question_id || ""
        const conditionType = cond.conditions || "is equal to"
        const qValue = cond.question_value || ""

        return `
            <div class="condition-row">
                <select class="form-select form-select-sm" onchange="updateContractCondition(${itemIndex}, ${condIndex}, 'question_id', this.value)">
                    <option value="">Select QID</option>
                    ${questionnaireQuestions.map(q => `
                        <option value="${escapeHtml(q.qid)}"${qidValue === q.qid ? " selected" : ""}>${escapeHtml(q.qid)}</option>
                    `).join('')}
                </select>
                <select class="form-select form-select-sm" onchange="updateContractCondition(${itemIndex}, ${condIndex}, 'conditions', this.value)">
                    <option value="is equal to"${conditionType === "is equal to" ? " selected" : ""}>equals</option>
                    <option value="is not equal to"${conditionType === "is not equal to" ? " selected" : ""}>not equals</option>
                    <option value="contains"${conditionType === "contains" ? " selected" : ""}>contains</option>
                    <option value="is empty"${conditionType === "is empty" ? " selected" : ""}>is empty</option>
                    <option value="is not empty"${conditionType === "is not empty" ? " selected" : ""}>is not empty</option>
                </select>
                <input type="text" class="form-control form-control-sm" placeholder="Value" 
                    value="${escapeHtml(qValue)}"
                    onchange="updateContractCondition(${itemIndex}, ${condIndex}, 'question_value', this.value)">
                <button class="icon-btn danger" onclick="deleteContractCondition(${itemIndex}, ${condIndex})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `
    }).join('')
}

function initContractSortable() {
    const el = document.getElementById("contractSortableList")
    if (!el) return

    if (contractSortable) {
        contractSortable.destroy()
    }

    contractSortable = new Sortable(el, {
        animation: 150,
        handle: ".drag-handle-compact",
        ghostClass: "sortable-ghost",
        chosenClass: "sortable-chosen",
        dragClass: "sortable-drag",
        onEnd: function (evt) {
            const oldIndex = evt.oldIndex
            const newIndex = evt.newIndex

            if (oldIndex !== newIndex) {
                // Save scroll position
                const scrollPos = saveScrollPosition('contractSortableList')

                const movedItem = contractTextItems.splice(oldIndex, 1)[0]
                contractTextItems.splice(newIndex, 0, movedItem)

                if (editingContractIndex !== null) {
                    if (editingContractIndex === oldIndex) {
                        editingContractIndex = newIndex
                    } else if (oldIndex < editingContractIndex && newIndex >= editingContractIndex) {
                        editingContractIndex--
                    } else if (oldIndex > editingContractIndex && newIndex <= editingContractIndex) {
                        editingContractIndex++
                    }
                }

                saveContractChanges()

                // Update cards without full re-render
                updateContractCardsOrder()

                // Restore scroll position
                restoreScrollPosition('contractSortableList', scrollPos)

                if (editingContractIndex !== null) {
                    selectContractItemForEdit(editingContractIndex)
                }
            }
        },
    })
}

function updateContractCardsOrder() {
    const listEl = document.getElementById("contractSortableList")
    if (!listEl) return

    const cards = listEl.querySelectorAll('.item-card')
    cards.forEach((card, index) => {
        card.setAttribute('data-index', index)
        card.setAttribute('id', `contract-card-${index}`)
        card.setAttribute('onclick', `selectContractItemForEdit(${index})`)

        const duplicateBtn = card.querySelector('.icon-btn:not(.danger)')
        const deleteBtn = card.querySelector('.icon-btn.danger')
        if (duplicateBtn) {
            duplicateBtn.setAttribute('onclick', `event.stopPropagation(); duplicateContractItem(${index})`)
        }
        if (deleteBtn) {
            deleteBtn.setAttribute('onclick', `event.stopPropagation(); deleteContractItem(${index})`)
        }

        const item = contractTextItems[index]
        const idBadge = card.querySelector('.id-badge')
        if (idBadge) idBadge.textContent = item.tid

        const sectionName = card.querySelector('.section-name')
        if (sectionName) sectionName.textContent = item.section_name
    })
}

function updateContractItem(index, field, value) {
    if (contractTextItems[index]) {

        if (field === 'tid') {
            value = value.replace(/\D/g, '')
            value = value ? 'TID' + value : '';
        }

        contractTextItems[index][field] = value
        saveContractChanges()

        const cardEl = document.getElementById(`contract-card-${index}`)
        if (cardEl) {
            cardEl.outerHTML = renderContractCard(contractTextItems[index], index)
        }

        if (field === 'text' || field === 'align_text' || field === 'blur_content') {
            const previewBox = document.querySelector('.preview-box')
            if (previewBox) {
                previewBox.style.textAlign = contractTextItems[index].align_text
                previewBox.style.filter = contractTextItems[index].blur_content ? 'blur(4px)' : 'none'
                previewBox.innerHTML = contractTextItems[index].text || '<em class="text-muted">No content</em>'
            }
        }
    }
}

function updateContractCondition(itemIndex, condIndex, field, value) {
    if (contractTextItems[itemIndex] && contractTextItems[itemIndex].conditions[condIndex]) {
        contractTextItems[itemIndex].conditions[condIndex][field] = value
        saveContractChanges()

        const cardEl = document.getElementById(`contract-card-${itemIndex}`)
        if (cardEl) {
            cardEl.outerHTML = renderContractCard(contractTextItems[itemIndex], itemIndex)
        }
    }
}

function addContractCondition(itemIndex) {
    if (contractTextItems[itemIndex]) {
        if (!contractTextItems[itemIndex].conditions) {
            contractTextItems[itemIndex].conditions = []
        }
        contractTextItems[itemIndex].conditions.push({
            question_id: "",
            question_value: "",
            conditions: "is equal to",
        })
        saveContractChanges()
        selectContractItemForEdit(itemIndex)
    }
}

function deleteContractCondition(itemIndex, condIndex) {
    Swal.fire({
        title: 'Delete Condition?',
        text: 'Are you sure you want to delete this condition?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            contractTextItems[itemIndex].conditions.splice(condIndex, 1);
            saveContractChanges();
            selectContractItemForEdit(itemIndex);
        }
    });
}

function deleteContractItem(index) {
    Swal.fire({
        title: 'Delete Section?',
        text: 'Are you sure you want to delete this section?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            contractTextItems.splice(index, 1);
            editingContractIndex = null;
            saveContractChanges();
            renderContractEditor();
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'Section has been deleted.',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

function duplicateContractItem(index) {
    const original = contractTextItems[index]
    const duplicate = JSON.parse(JSON.stringify(original))

    const maxTid = Math.max(...contractTextItems.map(item => {
        const num = parseInt(item.tid.replace(/\D/g, '')) || 0
        return num
    }), 0)
    duplicate.tid = `TID${maxTid + 1}`

    contractTextItems.splice(index + 1, 0, duplicate)
    saveContractChanges()
    renderContractEditor()
    selectContractItemForEdit(index + 1)
}

async function toggleAIStandardClausesPanel() {
    const panel   = document.getElementById('aiClausesPanel');
    const btn     = document.getElementById('defineStandardClausesBtn');
    const chevron = document.getElementById('aiClausesChevron');
 
    standardClausesPanelOpen = !standardClausesPanelOpen;
    panel.style.display = standardClausesPanelOpen ? 'block' : 'none';
    btn.classList.toggle('active', standardClausesPanelOpen);
    if (chevron) chevron.style.transform = standardClausesPanelOpen ? 'rotate(180deg)' : 'rotate(0deg)';
 
    if (standardClausesPanelOpen && !standardClausesScanned) {
        await fetchAndAnalyzeClauses();
    } else if (standardClausesPanelOpen && standardClausesScanned && aiProcessedClauses.length > 0) {
        // Re-opened after scan — just re-render without re-fetching
        const scanEl  = document.getElementById('aiClausesScanningState');
        const readyEl = document.getElementById('aiClausesReadyState');
        const emptyEl = document.getElementById('aiClausesEmptyState');
        if (scanEl)  scanEl.style.display  = 'none';
        if (emptyEl) emptyEl.style.display = 'none';
        if (readyEl) readyEl.style.display = 'block';
        renderAIClausesList();
        updateAIClausesCount();
    }
}

async function fetchAndAnalyzeClauses() {
    const contractName = document.getElementById('contractName')?.value?.trim() || '';

    const scanningEl = document.getElementById('aiClausesScanningState');
    const readyEl    = document.getElementById('aiClausesReadyState');
    const emptyEl    = document.getElementById('aiClausesEmptyState');
    if (scanningEl) scanningEl.style.display = 'block';
    if (readyEl)    readyEl.style.display    = 'none';
    if (emptyEl)    emptyEl.style.display    = 'none';

    const fill = document.getElementById('aiClausesProgressFill');
    if (fill) fill.style.width = '0%';
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress = Math.min(progress + Math.random() * 8, 85);
        if (fill) fill.style.width = progress + '%';
    }, 400);

    try {
        const response = await fetch('/admin-dashboard/standard/section/documents/api', {
            headers: { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (!data.success || !data.data || data.data.length === 0) {
            clearInterval(progressInterval);
            if (fill) fill.style.width = '100%';
            if (scanningEl) scanningEl.style.display = 'none';
            if (emptyEl) {
                emptyEl.style.display = 'block';
                emptyEl.innerHTML = '<i class="fas fa-info-circle"></i> No standard clauses are available yet.';
            }
            return;
        }

        allStandardClausesData = data.data;
        let matchedIds = new Set();

        if (contractName) {
            const BATCH_SIZE = 50;
            const batches = [];
            for (let i = 0; i < allStandardClausesData.length; i += BATCH_SIZE) {
                batches.push(allStandardClausesData.slice(i, i + BATCH_SIZE));
            }

            for (let b = 0; b < batches.length; b++) {
                const batch = batches[b];
                const batchProgress = 10 + ((b / batches.length) * 70);
                if (fill) fill.style.width = batchProgress + '%';

                try {
                    //  Only send clause_list + contract_name — prompt is built in PHP
                    const clauseList = batch
                        .map(c => `ID:${c.id} | "${c.title}" | ${(c.description || '').substring(0, 60)}`)
                        .join('\n');

                    const aiResp = await fetch('/admin-dashboard/api/ai-autofill', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf(),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            field_type: 'clause_analysis',
                            //  No prompt — PHP builds it from context
                            context: { contract_name: contractName, clause_list: clauseList }
                        })
                    });

                    const aiData = await aiResp.json();
                    if (aiData.success && aiData.content) {
                        const clean  = aiData.content.replace(/```json|```/g, '').trim();
                        const parsed = JSON.parse(clean);
                        if (Array.isArray(parsed)) {
                            parsed.forEach(id => matchedIds.add(String(id)));
                        }
                    }
                } catch (batchErr) {
                    console.warn(`Batch ${b + 1} failed — skipping`);
                    //  Do NOT add batch as fallback (that caused 600+ selections)
                }
            }
        } else {
            allStandardClausesData.forEach(c => matchedIds.add(String(c.id)));
        }

        console.log(` AI matched ${matchedIds.size} clauses out of ${allStandardClausesData.length}`);

        aiProcessedClauses = allStandardClausesData.map(c => ({
            ...c,
            aiInclude: matchedIds.has(String(c.id)),
            aiReason: !matchedIds.has(String(c.id)) ? `Not specific to "${contractName}"` : null
        }));

        clearInterval(progressInterval);
        if (fill) fill.style.width = '100%';

        setTimeout(() => {
            if (scanningEl) scanningEl.style.display = 'none';
            if (emptyEl)    emptyEl.style.display    = 'none';
            if (readyEl)    readyEl.style.display    = 'block';
            standardClausesScanned = true;
            renderAIClausesList();
            updateAIClausesCount();
        }, 350);

    } catch (err) {
        clearInterval(progressInterval);
        console.error('fetchAndAnalyzeClauses error:', err);
        if (scanningEl) scanningEl.style.display = 'none';
        if (emptyEl) {
            emptyEl.style.display = 'block';
            emptyEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Failed to load clauses. Please try again.';
        }
    }
}
 
function showAIClausesEmpty(message) {
    const scanningEl = document.getElementById('aiClausesScanningState');
    const readyEl    = document.getElementById('aiClausesReadyState');
    const emptyEl    = document.getElementById('aiClausesEmptyState');
    if (scanningEl) scanningEl.style.display = 'none';
    if (readyEl)    readyEl.style.display    = 'none';
    if (emptyEl) {
        emptyEl.style.display = 'block';
        if (message) emptyEl.innerHTML = message;
    }
}

function _chunkArray(arr, size) {
    const chunks = [];
    for (let i = 0; i < arr.length; i += size) {
        chunks.push(arr.slice(i, i + size));
    }
    return chunks;
}

async function _pooled(tasks, asyncFn, concurrency) {
    const results = new Array(tasks.length);
    let next = 0;
 
    async function worker() {
        while (next < tasks.length) {
            const idx = next++;
            results[idx] = await asyncFn(tasks[idx], idx);
        }
    }
 
    const workers = Array.from({ length: Math.min(concurrency, tasks.length) }, worker);
    await Promise.all(workers);
    return results;
}
 
 
function _setProgress(pct) {
    const fill = document.getElementById('aiClausesProgressFill');
    if (fill) fill.style.width = Math.min(pct, 100) + '%';
}
 
function _setScanLabel(text) {
    const el = document.getElementById('aiClausesScanLabel');
    if (el) el.textContent = text;
}
  
/** Send one batch of clauses to the backend and return an array of matched IDs (strings).  **/

async function _matchBatch(batch, contractName) {
    try {
        const clauseList = batch
            .map(c => `ID:${c.id} | "${c.title}" | ${(c.description || '').substring(0, 80)}`)
            .join('\n');
 
        const resp = await fetch('/admin-dashboard/api/ai-autofill', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                field_type: 'clause_analysis',
                context: { contract_name: contractName, clause_list: clauseList },
            }),
        });
 
        const data = await resp.json();
        if (!data.success || !data.content) return [];
 
        const clean  = data.content.replace(/```json|```/g, '').trim();
        const parsed = JSON.parse(clean);
        return Array.isArray(parsed) ? parsed.map(String) : [];
    } catch {
        return [];
    }
}
 
async function _generateClausesForContract(contractName) {
    try {
        const resp = await fetch('/admin-dashboard/api/ai-autofill', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                field_type: 'generate_clauses',
                context: { contract_name: contractName },
            }),
        });
 
        const data = await resp.json();
        if (!data.success || !data.content) return [];
 
        const clean  = data.content.replace(/```json|```/g, '').trim();
        const parsed = JSON.parse(clean);
        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
}
 

async function fetchAndAnalyzeClauses() {
    const contractName = document.getElementById('contractName')?.value?.trim() || '';
 
    const scanningEl = document.getElementById('aiClausesScanningState');
    const readyEl    = document.getElementById('aiClausesReadyState');
    const emptyEl    = document.getElementById('aiClausesEmptyState');
    if (scanningEl) scanningEl.style.display = 'block';
    if (readyEl)    readyEl.style.display    = 'none';
    if (emptyEl)    emptyEl.style.display    = 'none';
    _setProgress(0);
 
    try {
        _setScanLabel('Loading clause library…');
        const response = await fetch('/admin-dashboard/standard/section/documents/api', {
            headers: {
                'X-CSRF-TOKEN': csrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });
        const data = await response.json();
 
        if (!data.success || !Array.isArray(data.data) || data.data.length === 0) {
            if (scanningEl) scanningEl.style.display = 'none';
            if (emptyEl) {
                emptyEl.style.display = 'block';
                emptyEl.innerHTML = '<i class="fas fa-info-circle"></i> No standard clauses available yet.';
            }
            return;
        }
 
        allStandardClausesData = data.data;
        _setProgress(8);
 
        const batches    = _chunkArray(allStandardClausesData, CLAUSE_BATCH_SIZE);
        const matchedIds = new Set();
        let   completed  = 0;
 
        _setScanLabel(`Matching clauses — 0 / ${batches.length} batches done…`);
 
        await _pooled(batches, async (batch, batchIdx) => {
            const ids = await _matchBatch(batch, contractName);
            ids.forEach(id => matchedIds.add(id));
            completed++;
            const pct = 8 + Math.round((completed / batches.length) * 78);
            _setProgress(pct);
            _setScanLabel(
                `Matching clauses — ${completed} / ${batches.length} batch${batches.length > 1 ? 'es' : ''} done…`
            );
        }, CLAUSE_MAX_PARALLEL);
 
        _setProgress(88);
 
        //  AI-generate clauses if nothing matched 
        let generatedClauses = [];
 
        if (matchedIds.size === 0 && contractName) {
            _setScanLabel(`No matches found — generating clauses for "${contractName}"…`);
            generatedClauses = await _generateClausesForContract(contractName);
 
            generatedClauses = generatedClauses.map((c, i) => ({
                ...c,
                id: `gen_${i}`,
                clause_type: 'national',
                isGenerated: true,
            }));
        }
 
        _setProgress(96);
 
        const libraryProcessed = allStandardClausesData.map(c => ({
            ...c,
            aiInclude: matchedIds.has(String(c.id)),
            aiReason:  !matchedIds.has(String(c.id)) ? `Not specific to "${contractName}"` : null,
            isGenerated: false,
        }));
 
        const generatedProcessed = generatedClauses.map(c => ({
            ...c,
            aiInclude: true,
            aiReason:  null,
        }));
 
        aiProcessedClauses = [...libraryProcessed, ...generatedProcessed];
 
        _setProgress(100);
        _setScanLabel('Done');
 
        setTimeout(() => {
            if (scanningEl) scanningEl.style.display = 'none';
            if (emptyEl)    emptyEl.style.display    = 'none';
            if (readyEl)    readyEl.style.display     = 'block';
            standardClausesScanned = true;
            renderAIClausesList();
            updateAIClausesCount();
        }, 300);
 
    } catch (err) {
        console.error('fetchAndAnalyzeClauses error:', err);
        if (scanningEl) scanningEl.style.display = 'none';
        if (emptyEl) {
            emptyEl.style.display = 'block';
            emptyEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Failed to load clauses. Please try again.';
        }
    }
}
 
 
function renderAIClausesList() {
    const listEl  = document.getElementById('aiClausesList');
    const emptyEl = document.getElementById('aiClausesEmptyState');
    if (!listEl) return;
 
    const q = standardClausesCurrentSearch.toLowerCase();
 
    const filtered = aiProcessedClauses.filter(c => {
        const matchType   = standardClausesCurrentFilter === 'all' || c.clause_type === standardClausesCurrentFilter;
        const matchSearch = !q || c.title.toLowerCase().includes(q) || (c.description || '').toLowerCase().includes(q);
        return matchType && matchSearch;
    });
 
    if (filtered.length === 0) {
        listEl.innerHTML = '';
        if (emptyEl) {
            emptyEl.style.display = 'block';
            emptyEl.innerHTML = '<i class="fas fa-search"></i> No clauses match your search.';
        }
        return;
    }
 
    if (emptyEl) emptyEl.style.display = 'none';
 
    const sorted = [...filtered].sort((a, b) => {
        if (a.aiInclude === b.aiInclude) return 0;
        return a.aiInclude ? -1 : 1;
    });
 
    listEl.innerHTML = sorted.map(c => {
        const checked        = c.aiInclude ? 'checked' : '';
        const dimClass       = !c.aiInclude ? 'ai-clause-dim' : '';
        const typeLabel      = c.clause_type === 'national' ? 'National' : 'State-specific';
        const typeBadgeClass = c.clause_type === 'national' ? 'badge-national' : 'badge-state';
 
        let aiStatusBadge;
        if (c.isGenerated) {
            aiStatusBadge = `<span class="ai-clause-badge ai-generated">✦ AI Generated</span>`;
        } else if (c.aiInclude) {
            aiStatusBadge = `<span class="ai-clause-badge ai-included">AI Matched</span>`;
        } else {
            aiStatusBadge = `<span class="ai-clause-badge ai-skipped">Not matched</span>`;
        }
 
        const reasonHtml = (!c.aiInclude && !c.isGenerated && c.aiReason)
            ? `<div class="ai-clause-reason"><i class="fas fa-info-circle"></i> ${escapeHtml(c.aiReason)}</div>`
            : '';
        const descHtml = c.description
            ? `<div class="ai-clause-desc">${escapeHtml(c.description.substring(0, 120))}${c.description.length > 120 ? '…' : ''}</div>`
            : '';
 
        return `<div class="ai-clause-item ${dimClass}" id="aiClauseItem_${c.id}" onclick="toggleAIClauseCheck('${c.id}')">
            <input type="checkbox" class="ai-clause-cb" id="aiCb_${c.id}" ${checked}
                onclick="event.stopPropagation(); toggleAIClauseCheck('${c.id}')">
            <div class="ai-clause-body">
                <div class="ai-clause-name">${escapeHtml(c.title)}</div>
                ${descHtml}
                ${reasonHtml}
            </div>
        </div>`;
    }).join('');
}
 
 
function toggleAIClauseCheck(id) {
    const strId = String(id);
    const clause = aiProcessedClauses.find(c => String(c.id) === strId);
    if (!clause) return;
    clause.aiInclude = !clause.aiInclude;
    const cb   = document.getElementById(`aiCb_${strId}`);
    const item = document.getElementById(`aiClauseItem_${strId}`);
    if (cb)   cb.checked = clause.aiInclude;
    if (item) item.classList.toggle('ai-clause-dim', !clause.aiInclude);
    updateAIClausesCount();
}
 

 
function applyAISelectedClauses() {
    const selected = aiProcessedClauses.filter(c => c.aiInclude);
    selectedStandardClauseIds = selected
        .filter(c => !c.isGenerated)
        .map(c => String(c.id));
 
    // inject AI-generated clauses directly into contractTextItems
    selected.filter(c => c.isGenerated).forEach(c => {
        if (contractTextItems.some(item => item.generated_clause_title === c.title)) return;
 
        const maxTid = contractTextItems.length
            ? Math.max(...contractTextItems.map(item => parseInt(item.tid?.replace(/\D/g, '')) || 0))
            : 0;
 
        let insertIndex = contractTextItems.length;
        const distinctSections = [...new Set(contractTextItems.map(i => i.section_name).filter(Boolean))];
        if (distinctSections.length >= 2) {
            const lastSection = distinctSections[distinctSections.length - 1];
            const idx = contractTextItems.findIndex(i => i.section_name === lastSection);
            if (idx !== -1) insertIndex = idx;
        }
 
        let fullText = `<h4>${escapeHtml(c.title)}</h4>`;
        if (c.description && c.description.trim()) {
            fullText += `\n<p>${escapeHtml(c.description)}</p>`;
        }
 
        contractTextItems.splice(insertIndex, 0, {
            tid: `TID${maxTid + 1}`,
            section_key: 'Standard_Clauses',
            section_id: null,
            section_name: 'Standard Clauses',
            type: 'CONTENT',
            text: fullText,
            align_text: 'left',
            blur_content: false,
            conditions: [],
            generated_clause_title: c.title,
        });
    });
 
    // sync legacy checkbox UI
    selectedStandardClauseIds.forEach(id => {
        const cb = document.getElementById(`sc_${id}`);
        if (cb) cb.checked = true;
    });
 
    const applyBtn = document.getElementById('aiClausesApplyBtn');
    if (applyBtn) {
        const orig = applyBtn.innerHTML;
        applyBtn.innerHTML = '<i class="fas fa-check"></i> Applied!';
        applyBtn.style.background = '#2d6a4f';
        setTimeout(() => { applyBtn.innerHTML = orig; applyBtn.style.background = ''; }, 2500);
    }
 
    updateStep1ApplyBar();

    const qActionButtons = document.getElementById('qActionButtons');
    if (qActionButtons) qActionButtons.style.display = 'flex';
 
    const genCount = selected.filter(c => c.isGenerated).length;
    const libCount = selected.filter(c => !c.isGenerated).length;
    const detail   = [
        libCount  ? `${libCount} matched from library` : '',
        genCount  ? `${genCount} AI-generated` : '',
    ].filter(Boolean).join(' · ');
 
    Swal.fire({
        icon: 'success',
        title: 'Clauses Staged!',
        text: `${selected.length} clause(s) will be inserted into the contract. ${detail}`,
        timer: 3000,
        showConfirmButton: false,
    });
}
 
function updateAIClausesCount() {
    const checked = aiProcessedClauses.filter(c => c.aiInclude).length;
    const total = aiProcessedClauses.length;
    const el = document.getElementById('aiClausesSelectedCount');
    const checkedEl = document.getElementById('aiClausesCheckedNum');
    const totalEl = document.getElementById('aiClausesTotalNum');
    const applyBtn = document.getElementById('aiClausesApplyBtn');
    if (el) el.textContent = `${checked} selected`;
    if (checkedEl) checkedEl.textContent = checked;
    if (totalEl) totalEl.textContent = total;
    if (applyBtn) applyBtn.disabled = checked === 0;
}
 
function aiClausesSelectAll(state) {
    aiProcessedClauses.forEach(c => c.aiInclude = state);
    renderAIClausesList();
    updateAIClausesCount();
}
 
function aiClausesSearch(query) {
    standardClausesCurrentSearch = query;
    renderAIClausesList();
}
 
function toggleStateClausesPanel() {
    stateClausesPanelOpen = !stateClausesPanelOpen;
    const panel   = document.getElementById('stateClausesPanel');
    const btn     = document.getElementById('defineStateClausesBtn');
    const chevron = document.getElementById('stateClausesChevron');
    panel.style.display = stateClausesPanelOpen ? 'block' : 'none';
    btn.classList.toggle('active', stateClausesPanelOpen);
    if (chevron) chevron.style.transform = stateClausesPanelOpen ? 'rotate(180deg)' : 'rotate(0deg)';
}
 

async function onStateClauseStateChange(state) {
    stateClausesCurrentState = state;
    stateClausesSearch_q = '';
    allStateClausesData = [];
    stateClausesFiltered = [];
 
    // Reset search box
    const searchInput = document.getElementById('stateClausesSearchInput');
    if (searchInput) searchInput.value = '';
 
    _scHide(['stateClausesSearchBar', 'stateClausesInfoBar', 'stateClausesEmpty', 'stateClausesApplyBar']);
    _scEmpty('stateClausesList');
    document.getElementById('stateClausesCount').style.display = 'none';
 
    if (!state) return;
 
    // Show loading
    const loadingEl = document.getElementById('stateClausesLoading');
    const loadStateEl = document.getElementById('stateClausesLoadingState');
    if (loadingEl) loadingEl.style.display = 'block';
    if (loadStateEl) loadStateEl.textContent = state;
 
    try {
        const response = await fetch(
            `/admin-dashboard/state/clauses/api?state=${encodeURIComponent(state)}`,
            { headers: { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } }
        );
        const data = await response.json();
 
        if (!data.success || !Array.isArray(data.data) || data.data.length === 0) {
            if (loadingEl) loadingEl.style.display = 'none';
            _scShow('stateClausesEmpty');
            return;
        }
 
        allStateClausesData = data.data;
        stateClausesFiltered = [...allStateClausesData];
 
        // Update count badge in state picker
        const countEl = document.getElementById('stateClausesCount');
        if (countEl) {
            countEl.textContent = `${allStateClausesData.length} clause${allStateClausesData.length !== 1 ? 's' : ''} available`;
            countEl.style.display = '';
        }
 
        if (loadingEl) loadingEl.style.display = 'none';
        _scShow(['stateClausesSearchBar', 'stateClausesInfoBar', 'stateClausesApplyBar']);
        renderStateClausesList();
        _scUpdateCount();
 
    } catch (err) {
        console.error('State clauses fetch error:', err);
        if (loadingEl) loadingEl.style.display = 'none';
        _scShow('stateClausesEmpty');
        document.getElementById('stateClausesEmpty').innerHTML =
            '<i class="fas fa-exclamation-triangle"></i>Failed to load clauses. Please try again.';
    }
}
 
/** Render the current filtered list into #stateClausesList */
function renderStateClausesList() {
    const listEl = document.getElementById('stateClausesList');
    if (!listEl) return;
 
    if (stateClausesFiltered.length === 0) {
        listEl.innerHTML = '';
        _scShow('stateClausesEmpty');
        document.getElementById('stateClausesEmpty').style.display = 'block';
        document.getElementById('stateClausesEmpty').innerHTML =
            '<i class="fas fa-search"></i>No clauses match your search.';
        return;
    }
 
    document.getElementById('stateClausesEmpty').style.display = 'none';
 
    listEl.innerHTML = stateClausesFiltered.map(c => {
        const checked    = selectedStateClauseIds.has(String(c.id)) ? 'checked' : '';
        const hasQ       = c.questions && (Array.isArray(c.questions) ? c.questions.length > 0 : true);
        const desc       = c.description ? `<div class="sc-clause-desc">${escapeHtml(c.description)}</div>` : '';
        const qBadge     = hasQ ? `<span class="sc-has-questions-badge"><i class="fas fa-list-ul"></i> adds questions</span>` : '';
        const stateBadge = `<span class="sc-clause-state-badge">${escapeHtml(c.state || stateClausesCurrentState)}</span>`;
 
        return `<div class="sc-clause-item"
                     id="scItem_${c.id}"
                     onclick="toggleStateClauseCheck(${c.id})">
            <input type="checkbox"
                   class="sc-clause-cb"
                   id="scCb_${c.id}"
                   ${checked}
                   onclick="event.stopPropagation(); toggleStateClauseCheck(${c.id})">
            <div class="sc-clause-body">
                <div class="sc-clause-name">${escapeHtml(c.title)}</div>
                ${desc}
                <div class="sc-clause-meta">${stateBadge}${qBadge}</div>
            </div>
        </div>`;
    }).join('');
}
 
/** Toggle a single state clause checkbox */
function toggleStateClauseCheck(id) {
    const strId = String(id);
    if (selectedStateClauseIds.has(strId)) {
        selectedStateClauseIds.delete(strId);
    } else {
        selectedStateClauseIds.add(strId);
    }
    const cb   = document.getElementById(`scCb_${id}`);
    if (cb) cb.checked = selectedStateClauseIds.has(strId);
    _scUpdateCount();
}
 
/** Select / deselect all visible (filtered) clauses */
function stateClausesSelectAll(state) {
    stateClausesFiltered.forEach(c => {
        if (state) selectedStateClauseIds.add(String(c.id));
        else selectedStateClauseIds.delete(String(c.id));
        const cb = document.getElementById(`scCb_${c.id}`);
        if (cb) cb.checked = state;
    });
    _scUpdateCount();
}
 
/** Filter the list by search query */
function stateClausesSearch(query) {
    stateClausesSearch_q = query.toLowerCase();
    stateClausesFiltered = allStateClausesData.filter(c =>
        c.title.toLowerCase().includes(stateClausesSearch_q) ||
        (c.description || '').toLowerCase().includes(stateClausesSearch_q)
    );
    renderStateClausesList();
}
 
/** Update the count badge, button label, and parent trigger badge */
function _scUpdateCount() {
    const count   = selectedStateClauseIds.size;
    const numEl   = document.getElementById('stateClausesCheckedNum');
    const applyBtn = document.getElementById('stateClausesApplyBtn');
    const badge    = document.getElementById('stateClausesBadge');
    const trigBtn  = document.getElementById('defineStateClausesBtn');
 
    if (numEl) numEl.textContent = count;
    if (applyBtn) applyBtn.disabled = count === 0;
    if (badge) badge.textContent = count;
    if (trigBtn) trigBtn.classList.toggle('has-selection', count > 0);
}
 
/** Show one or multiple elements by id */
function _scShow(ids) {
    (Array.isArray(ids) ? ids : [ids]).forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = '';
    });
}
 
/** Hide one or multiple elements by id */
function _scHide(ids) {
    (Array.isArray(ids) ? ids : [ids]).forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
}
 
/** Clear inner HTML of an element */
function _scEmpty(id) {
    const el = document.getElementById(id);
    if (el) el.innerHTML = '';
}
 
/**
 * Stage selected state clauses into selectedStateClauses[]
 * (same array used by Steps 4/5 to inject into the contract).
 */
function applyStateClausesToContract() {
    if (selectedStateClauseIds.size === 0) return;
 
    const toStage = allStateClausesData.filter(c => selectedStateClauseIds.has(String(c.id)));
    let added = 0;
 
    toStage.forEach(clause => {
        // Avoid duplicates
        if (selectedStateClauses.some(sc => String(sc.id) === String(clause.id))) return;
 
        let questions = [];
        try {
            if (clause.questions) {
                const raw = typeof clause.questions === 'string'
                    ? JSON.parse(clause.questions)
                    : clause.questions;
                questions = Array.isArray(raw) ? raw.map(q => {
                    if (typeof q === 'string') return q;
                    return q.question || q.text || q.label || '';
                }).filter(Boolean) : [];
            }
        } catch (e) { questions = []; }
 
        selectedStateClauses.push({
            id: clause.id,
            state: clause.state || stateClausesCurrentState,
            title: clause.title,
            description: clause.description || '',
            text: clause.text || '',
            questions,
            has_questions: questions.length > 0
        });
        added++;
    });
 
    const applyBtn = document.getElementById('stateClausesApplyBtn');
    if (applyBtn) {
        const orig = applyBtn.innerHTML;
        applyBtn.innerHTML = '<i class="fas fa-check"></i> Staged!';
        applyBtn.style.background = '#2d6a4f';
        setTimeout(() => { applyBtn.innerHTML = orig; applyBtn.style.background = ''; }, 2500);
    }
 
    Swal.fire({
        icon: 'success',
        title: 'State Clauses Staged!',
        text: `${added} clause(s) from ${stateClausesCurrentState} will be added to the contract in Step 5.`,
        timer: 2800,
        showConfirmButton: false
    });
}

function loadStateClausesForStep5() {
    if (!selectedStateClauses || selectedStateClauses.length === 0) return;
 
    selectedStateClauses.forEach(clause => {
        const placeholderMap = addQuestionsFromStateClause(clause);
 
        addTextToContractFromStateClause(clause);
    });
 
    highlightAppliedStateClausesInStep1();
}
 

function highlightAppliedStateClausesInStep1() {
    selectedStateClauses.forEach(sc => {
        const cb   = document.getElementById(`scCb_${sc.id}`);
        const item = document.getElementById(`scItem_${sc.id}`);
        if (cb) cb.checked = true;
        if (item) item.style.borderLeft = '3px solid #4361ee';
        selectedStateClauseIds.add(String(sc.id));
    });
    _scUpdateCount();
}
 

 
async function loadStandardClausesForStep1(state = null) {
    // This is now only called if the legacy section is present. The new AI flow replaces it visually.
}
 
function searchStandardClauses(query) {
    aiClausesSearch(query);
}
 
// function filterStandardClauses(filterValue) {
//     aiClausesFilterType(filterValue);
// }
 
function toggleStandardClause(checkbox) {
    const id = String(checkbox.value);
    if (checkbox.checked) { if (!selectedStandardClauseIds.includes(id)) selectedStandardClauseIds.push(id); }
    else selectedStandardClauseIds = selectedStandardClauseIds.filter(x => x !== id);
    updateStep1ApplyBar();
}
 
function updateStep1ApplyBar() {
    const bar = document.getElementById('standardClausesApplyBar');
    const countEl = document.getElementById('applyStandardClausesCount');
    if (!bar) return;
    const count = selectedStandardClauseIds.length;
    bar.style.display = count > 0 ? '' : 'none';
    if (countEl) countEl.textContent = count;
}
 
async function applySelectedStandardClauses() {
    if (!selectedStandardClauseIds.length) return;
    try {
        const url = '/admin-dashboard/standard/section/documents/api';
        const response = await fetch(url, { headers: { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
        const data = await response.json();
        if (!data.success) return;
        const selectedClauses = data.data.filter(c => selectedStandardClauseIds.includes(String(c.id)));
        selectedClauses.forEach(clause => {
            if (contractTextItems.some(item => item.standard_clause_id == clause.id)) return;
            const maxTid = contractTextItems.length ? Math.max(...contractTextItems.map(item => parseInt(item.tid?.replace(/\D/g, '')) || 0)) : 0;
            let insertIndex = contractTextItems.length;
            const distinctSections = [...new Set(contractTextItems.map(i => i.section_name).filter(Boolean))];
            if (distinctSections.length >= 2) { const idx = contractTextItems.findIndex(i => i.section_name === distinctSections[distinctSections.length - 1]); if (idx !== -1) insertIndex = idx; }
            let fullText = `<h4>${escapeHtml(clause.title)}</h4>`;
            if (clause.description && clause.description.trim()) fullText += `\n<p>${clause.description}</p>`;
            contractTextItems.splice(insertIndex, 0, { tid: `TID${maxTid + 1}`, section_key: 'Standard_Clauses', section_id: null, section_name: 'Standard Clauses', type: 'CONTENT', text: fullText, align_text: 'left', blur_content: false, conditions: [], standard_clause_id: clause.id, standard_clause_title: clause.title });
        });
        saveContractChanges();
    } catch (error) { console.error('Failed to apply standard clauses:', error); throw error; }
}
 
async function applyStandardClausesNow() {
    // Delegate to the new AI flow if available
    if (aiProcessedClauses.length > 0) { applyAISelectedClauses(); return; }
    const btn = document.getElementById('applyStandardClausesBtn');
    const originalHTML = btn?.innerHTML;
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...'; }
    try {
        await applySelectedStandardClauses();
        updateStep1ApplyBar();
        Swal.fire({ icon: 'success', title: 'Clauses Staged!', text: `${selectedStandardClauseIds.length} clause(s) will be inserted.`, timer: 2800, showConfirmButton: false });
    } catch (error) { Swal.fire({ icon: 'error', title: 'Error', text: error.message }); }
    finally { if (btn) { btn.disabled = false; btn.innerHTML = originalHTML; } }
}
 
function applySelectedStandardClausesSync() {
    if (!selectedStandardClauseIds || selectedStandardClauseIds.length === 0) return;

    //  Build a lookup map from aiProcessedClauses (the actual data source)
    const clauseDataMap = {};
    aiProcessedClauses.forEach(c => {
        clauseDataMap[String(c.id)] = c;
    });

    //  Fallback: also check allStandardClausesData if aiProcessedClauses is empty
    if (Object.keys(clauseDataMap).length === 0) {
        allStandardClausesData.forEach(c => {
            clauseDataMap[String(c.id)] = c;
        });
    }

    selectedStandardClauseIds.forEach(id => {
        // Skip if already added
        if (contractTextItems.some(item => item.standard_clause_id == id)) return;

        const clause = clauseDataMap[String(id)];
        if (!clause) {
            console.warn(` Standard clause ID ${id} not found in data — skipping`);
            return;
        }

        const title       = clause.title || '';
        const description = clause.description || '';

        const maxTid = contractTextItems.length
            ? Math.max(...contractTextItems.map(item => parseInt(item.tid?.replace(/\D/g, '')) || 0))
            : 0;

        // Insert before last section (signatures)
        let insertIndex = contractTextItems.length;
        const distinctSections = [...new Set(contractTextItems.map(i => i.section_name).filter(Boolean))];
        if (distinctSections.length >= 2) {
            const lastSection = distinctSections[distinctSections.length - 1];
            const idx = contractTextItems.findIndex(i => i.section_name === lastSection);
            if (idx !== -1) insertIndex = idx;
        }

        let fullText = `<h4>${escapeHtml(title)}</h4>`;
        if (description && description.trim()) {
            fullText += `\n<p>${escapeHtml(description)}</p>`;
        }

        contractTextItems.splice(insertIndex, 0, {
            tid: `TID${maxTid + 1}`,
            section_key: 'Standard_Clauses',
            section_id: null,
            section_name: 'Standard Clauses',
            type: 'CONTENT',
            text: fullText,
            align_text: 'left',
            blur_content: false,
            conditions: [],
            standard_clause_id: id,
            standard_clause_title: title
        });

        console.log(` Injected standard clause: "${title}" at index ${insertIndex}`);
    });
}

function addNewContractTextItem() {
    const maxTid = contractTextItems.length > 0
        ? Math.max(...contractTextItems.map(item => {
            const num = parseInt(item.tid.replace(/\D/g, '')) || 0
            return num
        }))
        : 0

    const newTid = `TID${maxTid + 1}`

    contractTextItems.push({
        tid: newTid,
        section_key: "New_Section",
        section_id: null,
        section_name: "New_Section",
        type: "CONTENT",
        text: "",
        align_text: "left",
        blur_content: false,
        conditions: [],
    })
    saveContractChanges()
    renderContractEditor()
    selectContractItemForEdit(contractTextItems.length - 1)
}

function saveContractChanges() {
    rebuildParsedContractData()
}

/* -------- STEP 5: FINAL DOCUMENT EDITOR (THREE-COLUMN LAYOUT) -------- */
function renderFinalEditor() {
    const stepContainer = document.getElementById('step-5')
    const cardBody = stepContainer.querySelector('.card-body')
    const sectionNames = getUniqueSectionNames()
    const qidUsageMap = buildQidUsageMap()

    cardBody.innerHTML = `
        <style>
            /* ===== GENERAL LAYOUT ===== */
            * {
                box-sizing: border-box;
            }

            .final-editor-wrapper {
                background: #f8f9fa;
                padding: 20px 0;
                overflow-x: hidden;
            }
            
            .final-editor-header {
                background: white;
                padding: 20px 30px;
                margin-bottom: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .final-editor-header h4 {
                margin: 0 0 8px 0;
                color: #2c3e50;
                font-size: 24px;
                font-weight: 600;
            }
            
            .final-editor-header p {
                margin: 0;
                color: #6c757d;
                font-size: 14px;
            }
            
            /* ===== THREE COLUMN LAYOUT ===== */
            .final-editor-layout {
                display: grid;
                grid-template-columns: 320px 1fr ;
                gap: 20px;
                margin-bottom: 20px;
                height: calc(100vh - 180px);
                max-height: 800px;
                min-height: 600px;
            }
            
            /* ===== LEFT PANEL - QUESTIONS & STATE CLAUSES ===== */
            .final-questions-panel {
                background: white;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                display: flex;
                flex-direction: column;
                height: 100%;
                position: sticky;
                top: 20px;
                overflow: hidden;
                min-height: 0;
            }
            
            .final-panel-header {
                padding: 16px 20px;
                border-bottom: 2px solid #e9ecef;
                font-weight: 600;
                color: #2c3e50;
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 16px;
                flex-shrink: 0;
                background: white;
                z-index: 10;
            }
            
            .final-panel-header i {
                color: #FF6B35;
                font-size: 18px;
            }
            
            /* Questions List - Fixed height, scrollable */
            .final-questions-list {
                flex: 1;
                min-height: 0;
                overflow-y: auto;
                overflow-x: hidden;
                padding: 15px;
                background: white;
            }
            
            .final-question-item {
                margin-bottom: 16px;
                padding: 12px;
                background: #f8f9fa;
                border-radius: 6px;
                border-left: 3px solid #FF6B35;
                transition: all 0.2s;
            }
            
            .final-question-item:hover {
                background: #e9ecef;
                transform: translateX(2px);
            }
            
            .final-question-label {
                font-size: 12px;
                color: #495057;
                margin-bottom: 8px;
                font-weight: 500;
                display: flex;
                align-items: start;
                gap: 8px;
                line-height: 1.4;
            }
            
            .final-question-label i {
                color: #FF6B35;
                margin-top: 2px;
                flex-shrink: 0;
            }
            
            .final-question-input {
                width: 100%;
                padding: 8px 10px;
                border: 1px solid #ced4da;
                border-radius: 4px;
                font-size: 12px;
                transition: border-color 0.2s;
            }
            
            .final-question-input:focus {
                outline: none;
                border-color: #FF6B35;
                box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
            }
            
            .question-usage-badge {
                display: inline-block;
                background: #FF6B35;
                color: white;
                font-size: 10px;
                padding: 2px 6px;
                border-radius: 10px;
                margin-top: 5px;
                font-weight: 500;
            }
          

            /* State Clauses Content Wrapper */
            #stateClausesContent {
                flex: 1;
                display: flex;
                flex-direction: column;
                min-height: 0;
                overflow: hidden;
            }

            .state-group-header {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 6px 10px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-radius: 4px;
                margin-bottom: 8px;
                font-size: 12px;
                font-weight: 600;
            }

            .state-group-header i {
                color: white;
            }

            .state-clause-item {
                background: white;
                border: 1px solid #dee2e6;
                border-radius: 6px;
                padding: 10px;
                margin-bottom: 8px;
                transition: all 0.2s;
            }

            .state-clause-item:hover {
                border-color: #ffc107;
                box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
                transform: translateX(2px);
            }

            .state-clause-item-header {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 6px;
            }

            .state-clause-checkbox {
                width: 14px;
                height: 14px;
                cursor: pointer;
                flex-shrink: 0;
            }

            .state-clause-title {
                font-size: 11px;
                font-weight: 600;
                color: #2c3e50;
                flex: 1;
                margin: 0;
                cursor: pointer;
                line-height: 1.3;
            }

            .state-clause-badge {
                font-size: 9px;
                background: #17a2b8;
                color: white;
                padding: 2px 5px;
                border-radius: 3px;
                flex-shrink: 0;
            }

            .state-clause-description {
                font-size: 10px;
                color: #6c757d;
                margin-left: 22px;
                margin-bottom: 4px;
                line-height: 1.3;
            }

            .state-clause-text-preview {
                margin: 6px 0 6px 22px;
                padding: 6px;
                background: #f8f9fa;
                border-left: 3px solid #17a2b8;
                font-size: 10px;
            }

            .state-clause-text-preview .text-content {
                margin-top: 4px;
                color: #495057;
                line-height: 1.4;
            }

            .state-clause-questions-preview {
                margin: 6px 0 6px 22px;
                padding: 6px;
                background: #e7f3ff;
                border-left: 3px solid #007bff;
                font-size: 10px;
            }

            .state-clause-questions-preview ul {
                margin: 4px 0 0 0;
                padding-left: 16px;
            }

            .state-clause-questions-preview li {
                margin: 2px 0;
                font-size: 10px;
                color: #495057;
            }

            .state-clause-meta {
                margin-top: 6px;
                padding-top: 6px;
                border-top: 1px solid #dee2e6;
                margin-left: 22px;
            }

            .badge-sm {
                font-size: 9px;
                padding: 2px 6px;
                margin-left: 4px;
            }
            
            /* ===== MIDDLE PANEL - CONTRACT ===== */
            .final-contract-panel {
                background: white;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                display: flex;
                flex-direction: column;
                height: 100%;
                position: sticky;
                top: 20px;
                overflow: hidden;
            }
            
            .final-contract-content {
                padding: 30px 40px;
                overflow-y: auto;
                overflow-x: hidden;
                flex: 1;
                line-height: 1.8;
                color: #2c3e50;
                min-height: 0;
            }
            
            .contract-section {
                margin-bottom: 30px;
                scroll-margin-top: 20px;
            }
            
            .contract-section-title {
                font-size: 18px;
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 2px solid #FF6B35;
            }
            
            .contract-section-content {
                font-size: 14px;
                color: #495057;
                white-space: pre-wrap;
                line-height: 1.6;
            }
            
            .contract-placeholder {
                display: inline-block;
                background: #fff3cd;
                color: #856404;
                padding: 2px 8px;
                border-radius: 3px;
                font-weight: 500;
                border: 1px dashed #ffc107;
                cursor: pointer;
                transition: all 0.2s;
            }
            
            .contract-placeholder:hover {
                background: #ffc107;
                color: white;
            }
            
            /* ===== RIGHT PANEL - NAVIGATION ===== */
            .final-nav-panel {
                background: white;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                height: 100%;
                position: sticky;
                top: 20px;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }
            
            .final-nav-list {
                padding: 10px;
                flex: 1;
                overflow-y: auto;
                overflow-x: hidden;
            }
            
            .final-nav-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 12px;
                color: #495057;
                text-decoration: none;
                border-radius: 6px;
                font-size: 12px;
                margin-bottom: 5px;
                transition: all 0.2s;
                border-left: 3px solid transparent;
                cursor: pointer;
                line-height: 1.4;
            }
            
            .final-nav-item:hover {
                background: #f8f9fa;
                color: #FF6B35;
                border-left-color: #FF6B35;
                transform: translateX(3px);
            }
            
            .final-nav-item.active {
                background: #fff3e0;
                color: #FF6B35;
                border-left-color: #FF6B35;
                font-weight: 600;
            }
            
            .final-nav-item i {
                font-size: 9px;
                color: #adb5bd;
                flex-shrink: 0;
            }
            
            /* ===== ACTION BUTTONS ===== */
            .final-actions {
                background: white;
                padding: 20px 30px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                display: flex;
                gap: 15px;
                align-items: center;
                flex-wrap: wrap;
            }
            
            .btn-success {
                background: #FF6B35;
                border: none;
                padding: 12px 30px;
                font-size: 16px;
                font-weight: 600;
                border-radius: 6px;
                transition: all 0.2s;
                color: white;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }
            
            .btn-success:hover {
                background: #e55a2b;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
            }
            
            .btn-secondary {
                background: #6c757d;
                border: none;
                padding: 12px 24px;
                color: white;
                border-radius: 6px;
                font-size: 14px;
                transition: all 0.2s;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }
            
            .btn-secondary:hover {
                background: #5a6268;
                transform: translateY(-2px);
            }

            .btn-primary {
                background: #007bff;
                border: none;
                padding: 12px 24px;
                color: white;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                transition: all 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
            }

            .btn-primary:hover {
                background: #0056b3;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
            }
            
            /* ===== RESPONSIVE DESIGN ===== */
            @media (max-width: 1400px) {
                .final-editor-layout {
                    grid-template-columns: 300px 1fr;
                }
            }

            @media (max-width: 1200px) {
                .final-editor-layout {
                    grid-template-columns: 280px 1fr ;
                }
            }
            
            @media (max-width: 992px) {
                .final-editor-layout {
                    grid-template-columns: 1fr;
                    height: auto;
                    max-height: none;
                }
                
                .final-questions-panel,
                .final-nav-panel,
                .final-contract-panel {
                    position: static;
                    height: auto;
                }

                .final-questions-list {
                    max-height: 300px;
                }

            }
        </style>
        
        <div class="final-editor-wrapper">
            <div class="final-editor-header">
                <h4><i class="fas fa-file-signature"></i> ${escapeHtml(documentName)}</h4>
                <p class="text-muted">Review and complete the questionnaire to generate your finalized contract</p>
            </div>
            
            <div class="final-editor-layout">
                <!-- Left Panel: Questions + State Clauses -->
                <div class="final-questions-panel" id="finalQuestionsPanel">
                    <!-- Questions Header -->
                    <div class="final-panel-header">
                        <i class="fas fa-question-circle"></i>
                        <span>Questionnaire (${questionnaireQuestions.length})</span>
                    </div>
                    
                    <!-- Questions List - Scrollable -->
                    <div class="final-questions-list" id="finalQuestionsList">
                        ${renderFinalQuestionsPanel(qidUsageMap)}
                    </div>
                    
                </div>
                
                <!-- Middle Panel: Contract Preview -->
                <div class="final-contract-panel" id="finalContractPanel">
                    <div class="final-panel-header">
                        <i class="fas fa-file-contract"></i>
                        <span>Contract Preview</span>
                    </div>
                    <div class="final-contract-content" id="finalContractContent">
                        ${renderFinalContractPreview(qidUsageMap)}
                    </div>
                </div>
            </div>
            
            <div class="final-actions">
                <button class="btn btn-success btn-lg" id="document_id" onclick="saveDocumentToDatabase()">
                    <i class="fas fa-save"></i> Save to Database
                </button>
                <button class="btn btn-primary" onclick="goToStep(6)">
                    <i class="fas fa-edit"></i> Go to Advanced Editor
                </button>
                <button class="btn btn-secondary" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </div>
        </div>
    `

    setupScrollSpy()
    loadStateClausesForStep5()
    if (documentAlreadySaved) _lockSaveButton();

}

function _lockSaveButton(btn) {
    if (!btn) {
        btn = document.querySelector('#step-5 .btn-success[onclick*="saveDocumentToDatabase"]');
    }
    if (!btn) return;
 
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-check-circle"></i> Saved';
    btn.style.background  = '#6c757d';
    btn.style.cursor      = 'not-allowed';
    btn.style.opacity     = '0.75';
    btn.title             = 'Document already saved. Edit in Step 6.';
}

function findDynamicFuzzyMatch(placeholder, questions) {
    const cleanPlaceholder = placeholder.replace(/[\[\]]/g, '').toLowerCase();
    const words = cleanPlaceholder.split('_').filter(w => w.length > 0);

    let bestMatch = null;
    let highestScore = 0;

    questions.forEach(question => {
        const questionText = question.text.toLowerCase();
        let score = 0;

        // Calculate match score
        words.forEach(word => {
            if (questionText.includes(word)) {
                score += word.length / cleanPlaceholder.length;
            }
        });

        // Boost score if all words are present
        if (words.every(word => questionText.includes(word))) {
            score += 0.3;
        }

        if (score > highestScore) {
            highestScore = score;
            bestMatch = {
                qid: question.qid,
                confidence: Math.min(score, 1.0),
                text: question.text
            };
        }
    });

    return bestMatch;
}

function stripPTags(html) {
    if (!html || typeof html !== 'string') return '';
    return html
        .replace(/<\/p>\s*<p>/gi, '\n\n') // preserve paragraph breaks
        .replace(/<p[^>]*>/gi, '')         //  CHANGED: remove opening <p> with attributes
        .replace(/<\/p>/gi, '')            //  CHANGED: remove closing </p>
        .trim();
}


function addTextToContractFromStateClause(clause) {
    if (!clause || typeof clause !== 'object') return;
    if (typeof clause.text !== 'string' || clause.text.trim() === '') return;

    let processedText = String(clause.text ?? '').trim();

    // Check for duplicates
    const existingItem = contractTextItems.find(item =>
        item.state_clause_id == clause.id ||
        (item.text && clause.title && item.text.includes(clause.title))
    );
    if (existingItem) return;

    // --- 2. QID Matching Logic ---
    const placeholderPattern = /\[([A-Z_]+)\]/g;
    const foundPlaceholders = [...processedText.matchAll(placeholderPattern)];
    const itemQIDMappings = [];

    foundPlaceholders.forEach(match => {
        const fullPlaceholder = match[0];
        const placeholderKey = match[1];

        const matchedQuestion = questionnaireQuestions.find(q => {
            const qPlaceholder = (q.placeholder || '').toUpperCase();
            const qText = (q.text || '').toUpperCase().trim();
            const target = placeholderKey.toUpperCase().trim();
            const targetAsText = target.replace(/_/g, ' ');

            if (qPlaceholder === target) return true;
            if (qText === targetAsText) return true;

            return false;
        }) || questionnaireQuestions.find(q => {
            const qText = (q.text || '').toUpperCase();
            const targetAsText = placeholderKey.toUpperCase().replace(/_/g, ' ');
            return qText.includes(targetAsText) && targetAsText.length > 4;
        });

        if (matchedQuestion) {
            const qid = matchedQuestion.qid;
            itemQIDMappings.push({
                placeholder: fullPlaceholder,
                qid: qid,
                originalText: match.input.substring(Math.max(0, match.index - 50), Math.min(match.input.length, match.index + 50))
            });
            processedText = processedText.split(fullPlaceholder).join(`{${matchedQuestion.qid}}`);
        } else {
            processedText = processedText.split(fullPlaceholder).join('_____________');
        }
    });

    let processedDescription = '';
    if (typeof clause.description === 'string' && clause.description.trim() !== '') {
        processedDescription = String(clause.description ?? '').trim();
        const descMatches = [...processedDescription.matchAll(placeholderPattern)];

        descMatches.forEach(match => {
            const fullPlaceholder = match[0];
            const placeholderKey = match[1];

            const matchedQuestion = questionnaireQuestions.find(q => {
                const qPlaceholder = (q.placeholder || '').toUpperCase();
                const qText = (q.text || '').toUpperCase().trim();
                const target = placeholderKey.toUpperCase().trim();
                const targetAsText = target.replace(/_/g, ' ');

                return (qPlaceholder === target) || (qText === targetAsText);
            });

            if (matchedQuestion) {
                processedDescription = processedDescription.split(fullPlaceholder).join(`{${matchedQuestion.qid}}`);
            } else {
                processedDescription = processedDescription.split(fullPlaceholder).join('_____________');
            }
        });
    }

    // FIXED POSITIONING LOGIC 2ND LAST
    let insertIndex = contractTextItems.length; // Default to end

    if (contractTextItems.length > 0) {
        const distinctSections = [];
        const seenSections = new Set();

        contractTextItems.forEach(item => {
            if (item.section_name && !seenSections.has(item.section_name)) {
                seenSections.add(item.section_name);
                distinctSections.push(item.section_name);
            }
        });

        // If there are at least 2 sections, insert before the LAST section
        if (distinctSections.length >= 2) {
            const lastSectionName = distinctSections[distinctSections.length - 1];

            //  Find the FIRST item of the last section
            const startOfLastSectionIndex = contractTextItems.findIndex(item =>
                item.section_name === lastSectionName
            );

            if (startOfLastSectionIndex !== -1) {
                insertIndex = startOfLastSectionIndex;
            }
        }
        //  If only 1 section exists, append to end
        else {
            insertIndex = contractTextItems.length;
        }
    }

    //  Create New Item
    const maxTid = contractTextItems.length
        ? Math.max(...contractTextItems.map(item => parseInt(item.tid?.replace(/\D/g, '')) || 0))
        : 0;

    const newTid = `TID${maxTid + 1}`;

    let completeText = '';
    if (clause.title) completeText += `<h3>${clause.title}</h3>\n`;
    if (processedDescription) completeText += `<p>${processedDescription}</p>\n`;

    const paragraphs = processedText.split('\n\n').filter(p => p.trim());
    paragraphs.forEach(para => {
        completeText += `<p>${para.trim()}</p>\n`;
    });

    const newItem = {
        tid: newTid,
        section_key: `${clause.state}_Requirements`,
        section_id: null,
        section_name: `${clause.state} Requirements`,
        type: 'CONTENT',
        text: completeText,
        align_text: 'left',
        blur_content: false,
        conditions: [],
        state_clause_id: clause.id,
        state_clause_title: clause.title,
        state_clause_description: clause.description,
        state: clause.state,
        qidMappings: itemQIDMappings
    };

    contractTextItems.splice(insertIndex, 0, newItem);
}


// //  HELPER: Escape special regex characters
function extractQuestionDetails(questionItem) {
    const defaults = {
        text: '',
        placeholder: '',
        type: 'text',
        options: []
    };

    if (typeof questionItem === 'string') {
        return { ...defaults, text: questionItem };
    }

    if (typeof questionItem === 'object' && questionItem !== null) {
        return {
            text: questionItem.question || questionItem.text || questionItem.label || '',
            placeholder: questionItem.placeholder || '',
            type: questionItem.type || 'text',
            options: questionItem.options || []
        };
    }

    return defaults;
}

function addQuestionsFromStateClause(clause) {
    if (!clause.questions || clause.questions.length === 0) {
        return {};
    }

    const placeholderToQidMap = {};

    // Get next available QID number
    const existingQidNumbers = questionnaireQuestions.map(q =>
        parseInt(q.qid.replace('QID', '')) || 0
    );
    let nextQidNumber = existingQidNumbers.length > 0
        ? Math.max(...existingQidNumbers) + 1
        : 1;

    // Normalize questions array
    const questionsArray = Array.isArray(clause.questions)
        ? clause.questions
        : (typeof clause.questions === 'string' ? JSON.parse(clause.questions) : []);

    questionsArray.forEach((questionItem, index) => {
        // Extract question details
        const questionDetails = extractQuestionDetails(questionItem);

        if (!questionDetails.text || questionDetails.text.trim() === '') {
            return;
        }

        // Check if question already exists
        const existingQuestion = findExistingQuestion(questionDetails.text);

        if (existingQuestion) {
            // Map existing question's QID to placeholder
            if (questionDetails.placeholder) {
                const placeholderKey = `[${questionDetails.placeholder}]`;
                placeholderToQidMap[placeholderKey] = existingQuestion.qid;
            }
            return;
        }

        // Create new question with sequential QID
        const newQid = `QID${nextQidNumber}`;
        const newQuestion = {
            id: questionnaireQuestions.length + 1,
            qid: newQid,
            text: questionDetails.text,
            type: questionDetails.type,
            options: questionDetails.options || [],
            required: true,
            goto: '',
            userinfo: `Required for ${clause.state} - ${clause.title}`,
            section_name: `${clause.state} Requirements`,
            state_clause_id: clause.id,
            state_clause_title: clause.title,
            placeholder: questionDetails.placeholder
        };

        questionnaireQuestions.push(newQuestion);

        // Map placeholder to new QID
        if (questionDetails.placeholder) {
            const placeholderKey = `[${questionDetails.placeholder}]`;
            placeholderToQidMap[placeholderKey] = newQid;
        }

        nextQidNumber++;
    });

    // Update goto flow
    if (Object.keys(placeholderToQidMap).length > 0) {
        assignAutoGotoFlow();
        fixInvalidGotoReferences();
    }

    return placeholderToQidMap;
}

function findExistingQuestion(questionText) {
    const normalizedText = questionText.toLowerCase().trim();
    return questionnaireQuestions.find(q =>
        q.text.toLowerCase().trim() === normalizedText
    ) || null;
}

function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// Toggle State Clause Selection in Step 5
function toggleStateClauseInStep5(checkbox) {
    const clauseId = checkbox.value
    const state = checkbox.dataset.state
    const title = checkbox.dataset.title
    const description = checkbox.dataset.description
    const text = checkbox.dataset.text
    // Parse questions from data attribute properly
    let questions = []
    try {
        const questionsData = checkbox.dataset.questions
        if (questionsData) {

            questions = JSON.parse(questionsData)

            // FIX: Ensure questions are strings
            questions = questions.map(q => {
                if (typeof q === 'string') return q
                if (typeof q === 'object' && q !== null) {
                    return q.question || q.text || q.label || '[Invalid Question]'
                }
                return '[Invalid Question]'
            }).filter(q => q !== '[Invalid Question]')
        }
    } catch (e) {
        questions = []
    }

    const hasQuestions = questions.length > 0

    if (checkbox.checked) {
        if (!selectedStateClauses.some(sc => sc.id == clauseId)) {
            selectedStateClauses.push({
                id: clauseId,
                state: state,
                title: title,
                description: description,
                text: text,
                questions: questions,
                has_questions: hasQuestions
            })
        }
    } else {
        selectedStateClauses = selectedStateClauses.filter(sc => sc.id != clauseId)
    }
    // Update UI
    const countText = document.getElementById('selectedClausesCountText')
    const applyBtn = document.getElementById('applyStateClausesBtn')

    if (countText) {
        countText.textContent = `${selectedStateClauses.length} selected`
    }

    if (applyBtn) {
        applyBtn.disabled = selectedStateClauses.length === 0
        applyBtn.innerHTML = `
            <i class="fas fa-sync"></i>
            Apply Selected Clauses (${selectedStateClauses.length})
        `
    }
}

// Get all state clauses currently applied to the document
function getAppliedStateClauses() {
    const appliedIds = new Set()

    questionnaireQuestions.forEach(q => {
        if (q.state_clause_id) {
            appliedIds.add(q.state_clause_id)
        }
    })

    contractTextItems.forEach(item => {
        if (item.state_clause_id) {
            appliedIds.add(item.state_clause_id)
        }
    })

    return Array.from(appliedIds)
}


function highlightAppliedStateClauses() {
    const appliedIds = getAppliedStateClauses()

    appliedIds.forEach(id => {
        const checkbox = document.getElementById(`clause_${id}`)
        if (checkbox) {
            checkbox.checked = true
            checkbox.parentElement.parentElement.classList.add('applied')
        }
    })
}

function getSelectedStateFromQuestions() {
    // Check qidAnswers for state-related questions
    for (const qid in qidAnswers) {
        const question = questionnaireQuestions.find(q => q.qid === qid)

        if (question) {
            const questionText = question.text.toLowerCase()
            const sectionName = (question.section_name || '').toLowerCase()

            // Check if this question is about state/province
            if (questionText.includes('state') ||
                questionText.includes('province') ||
                sectionName.includes('state') ||
                sectionName.includes('jurisdiction') ||
                qid === 'QID1') {

                const answer = qidAnswers[qid]
                if (answer && answer.trim() !== '') {
                    selectedState = answer.trim()
                    return answer.trim()
                }
            }
        }
    }

    // Strategy 2: Check first question (commonly used for state)
    if (questionnaireQuestions.length > 0) {
        const firstQuestion = questionnaireQuestions[0]
        const firstAnswer = qidAnswers[firstQuestion.qid]

        if (firstAnswer && firstAnswer.trim() !== '') {
            const questionText = firstQuestion.text.toLowerCase()

            // If first question mentions state, use its answer
            if (questionText.includes('state') || questionText.includes('province')) {
                selectedState = firstAnswer.trim()
                return firstAnswer.trim()
            }
        }
    }

    // Strategy 3: Check selectedState global variable
    if (selectedState) {
        return selectedState
    }
    return null
}

//  NEW: Get unique section names from contract items
function getUniqueSectionNames() {
    const seen = new Set()
    const names = []

    contractTextItems.forEach(item => {
        const sectionName = item.section_name || 'Main'

        if (!seen.has(sectionName)) {
            seen.add(sectionName)
            names.push(sectionName)
        }
    })

    return names.length > 0 ? names : ['Main']
}

// Helper function to render contract preview
function renderFinalContractPreview(qidUsageMap) {
    const sections = getContractSections()
    return sections.map(section => `
        <div class="contract-section" id="section-${escapeHtml(section.name.replace(/\s+/g, '-').toLowerCase())}">
            <h3 class="contract-section-title">${escapeHtml(section.name)}</h3>
            <div class="contract-section-content">
                ${processContractText(section.content, qidUsageMap)}
            </div>
        </div>
    `).join('')
}

// Helper function to process contract text and replace placeholders
function processContractText(text, qidUsageMap) {
    return escapeHtml(text).replace(/\{\{Q(\d+)\}\}/g, (match, qid) => {
        const question = questionnaireQuestions.find(q => q.id === qid)
        const answer = question?.answer || ''
        return answer
            ? `<span class="contract-placeholder" onclick="focusQuestion('${qid}')">${escapeHtml(answer)}</span>`
            : `<span class="contract-placeholder" onclick="focusQuestion('${qid}')">[Not Answered]</span>`
    })
}

// Scroll to section when navigation item is clicked
function scrollToSection(sectionName) {
    const sectionId = 'section-' + sectionName.replace(/\s+/g, '-').toLowerCase()
    const element = document.getElementById(sectionId)
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
}

// Focus on question when placeholder is clicked
function focusQuestion(qid) {
    const input = document.getElementById('q_' + qid)
    if (input) {
        input.focus()
        input.scrollIntoView({ behavior: 'smooth', block: 'center' })
    }
}

// Update question answer
function updateQuestionAnswer(qid, value) {
    const question = questionnaireQuestions.find(q => q.id === qid)
    if (question) {
        question.answer = value
        // Refresh contract preview
        const qidUsageMap = buildQidUsageMap()
        document.getElementById('finalContractContent').innerHTML = renderFinalContractPreview(qidUsageMap)
    }
}

// Setup scroll spy for navigation
function setupScrollSpy() {
    const contractContent = document.getElementById('finalContractContent')
    const navItems = document.querySelectorAll('.final-nav-item')

    contractContent.addEventListener('scroll', () => {
        const sections = document.querySelectorAll('.contract-section')
        let currentSection = ''

        sections.forEach(section => {
            const rect = section.getBoundingClientRect()
            if (rect.top <= 100 && rect.bottom >= 100) {
                currentSection = section.id
            }
        })

        navItems.forEach(item => {
            item.classList.remove('active')
            const sectionName = item.textContent.trim()
            const sectionId = 'section-' + sectionName.replace(/\s+/g, '-').toLowerCase()
            if (sectionId === currentSection) {
                item.classList.add('active')
            }
        })
    })
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div')
    div.textContent = text
    return div.innerHTML
}

function getUniqueSectionNames() {
    const seen = new Set()
    const names = []

    contractTextItems.forEach(item => {
        if (item.section_name && !seen.has(item.section_name)) {
            seen.add(item.section_name)
            names.push(item.section_name)
        }
    })

    return names
}

function buildQidUsageMap() {
    const map = {}

    // Find which QIDs are used in which contract sections
    contractTextItems.forEach((item, index) => {
        const qidPattern = /\{(QID\d+)\}/g
        let match

        while ((match = qidPattern.exec(item.text)) !== null) {
            const qid = match[1]
            if (!map[qid]) {
                map[qid] = {
                    firstOccurrenceIndex: index,
                    occurrences: []
                }
            }
            map[qid].occurrences.push({
                index: index,
                tid: item.tid,
                section_name: item.section_name
            })
        }
    })

    return map
}

function renderFinalQuestionsPanel(qidUsageMap) {
    if (questionnaireQuestions.length === 0) {
        return '<div class="alert alert-warning">No questions available</div>'
    }

    // Group questions by section
    const groupedQuestions = {}
    questionnaireQuestions.forEach(q => {
        const section = q.section_name || 'General'
        if (!groupedQuestions[section]) {
            groupedQuestions[section] = []
        }
        groupedQuestions[section].push(q)
    })

    let html = ''

    for (const section in groupedQuestions) {
        html += `
            <div class="final-question-section">
                <div class="final-question-section-header">
                    <i class="fas fa-folder"></i> ${escapeHtml(section)}
                </div>
                ${groupedQuestions[section].map(q => renderFinalQuestionItem(q, qidUsageMap)).join('')}
            </div>
        `
    }

    return html
}

function renderFinalQuestionItem(q, qidUsageMap) {
    const usage = qidUsageMap[q.qid]
    const usageInfo = usage ? `Used in ${usage.occurrences.length} place(s)` : 'Not used in contract'
    const currentValue = qidAnswers[q.qid] || ''

    return `
        <div class="final-question-item" id="question-${q.qid}">
            <div class="final-question-header">
                <span class="final-qid-badge">${escapeHtml(q.qid)}</span>
                <button class="icon-btn" onclick="editQuestionInline('${q.qid}')" title="Edit Question">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
            <div class="final-question-text" id="question-text-${q.qid}">
                ${escapeHtml(q.text)}
                ${q.required ? '<span class="text-danger">*</span>' : ''}
            </div>
            ${q.userinfo ? `<div class="final-question-help">${escapeHtml(q.userinfo)}</div>` : ''}
            <div class="final-question-input">
                ${renderQuestionInput(q, currentValue)}
            </div>
            <div class="final-question-usage">
                <small class="text-muted">
                    <i class="fas fa-link"></i> ${usageInfo}
                </small>
            </div>
        </div>
    `
}

function renderQuestionInput(q, value) {
    const qid = q.qid

    switch (q.type) {
        case 'textarea':
            return `<textarea class="form-control" rows="3" placeholder="Enter your answer..."
           data-qid="${qid}" 
                oninput="updateQidAnswer('${qid}', this.value)">${escapeHtml(value)}</textarea>`

        case 'number':
            return `<input type="number" class="form-control" placeholder="Enter number..."
                value="${escapeHtml(value)}" oninput="updateQidAnswer('${qid}', this.value)">`

        case 'date':
            return `<input type="date" class="form-control" 
                value="${escapeHtml(value)}" oninput="updateQidAnswer('${qid}', this.value)">`

        case 'radio':
            if (!q.options || q.options.length === 0) {
                return `<input type="text" class="form-control" placeholder="Enter your answer..."
                    value="${escapeHtml(value)}" oninput="updateQidAnswer('${qid}', this.value)">`
            }
            return `<div class="final-radio-group">
                ${q.options.map(opt => {
                const optLabel = opt.label || opt
                const optValue = opt.value || opt
                const checked = value === optValue ? 'checked' : ''
                return `
                        <label class="final-radio-label">
                            <input type="radio" name="radio-${qid}" value="${escapeHtml(optValue)}" ${checked}
                                onchange="updateQidAnswer('${qid}', this.value)">
                            ${escapeHtml(optLabel)}
                        </label>
                    `
            }).join('')}
            </div>`

        case 'checkbox':
            if (!q.options || q.options.length === 0) {
                return `<input type="text" class="form-control" placeholder="Enter your answer..."
                    value="${escapeHtml(value)}" oninput="updateQidAnswer('${qid}', this.value)">`
            }
            const checkedValues = value ? value.split(',').map(v => v.trim()) : []
            return `<div class="final-checkbox-group">
                ${q.options.map(opt => {
                const optLabel = opt.label || opt
                const optValue = opt.value || opt
                const checked = checkedValues.includes(optValue) ? 'checked' : ''
                return `
                        <label class="final-checkbox-label">
                            <input type="checkbox" value="${escapeHtml(optValue)}" ${checked}
                                onchange="updateCheckboxAnswer('${qid}', this)">
                            ${escapeHtml(optLabel)}
                        </label>
                    `
            }).join('')}
            </div>`

        case 'select':
            if (!q.options || q.options.length === 0) {
                return `<input type="text" class="form-control" placeholder="Enter your answer..."
                    value="${escapeHtml(value)}" oninput="updateQidAnswer('${qid}', this.value)">`
            }
            return `<select class="form-select" onchange="updateQidAnswer('${qid}', this.value)">
                <option value="">-- Select --</option>
                ${q.options.map(opt => {
                const optLabel = opt.label || opt
                const optValue = opt.value || opt
                const selected = value === optValue ? 'selected' : ''
                return `<option value="${escapeHtml(optValue)}" ${selected}>${escapeHtml(optLabel)}</option>`
            }).join('')}
            </select>`

        default: // text
            return `<input type="text" class="form-control" placeholder="Enter your answer..."
             data-qid="${qid}" 
        value="${escapeHtml(value)}" 
                value="${escapeHtml(value)}" oninput="updateQidAnswer('${qid}', this.value)">`
    }
}


function updateQidAnswer(qid, value) {
    qidAnswers[qid] = value
    updateContractPreviewWithAnswers()

    const allInputs = document.querySelectorAll(`[data-qid="${qid}"]`);
    allInputs.forEach(input => {
        if (input.value !== value) {
            input.value = value;
        }
    });
}

function updateCheckboxAnswer(qid, checkbox) {
    const container = checkbox.closest('.final-checkbox-group');
    const checked = Array.from(container.querySelectorAll('input:checked')).map(cb => cb.value);
    const value = checked.join(', ');
    updateQidAnswer(qid, value);
}


function updateContractPreviewWithAnswers() {
    const contractContent = document.getElementById('finalContractContent')
    if (contractContent) {
        const qidUsageMap = buildQidUsageMap()
        contractContent.innerHTML = renderFinalContractPreview(qidUsageMap)
    }
}

function renderFinalContractPreview(qidUsageMap) {
    if (contractTextItems.length === 0) {
        return '<div class="alert alert-warning">No contract content available</div>'
    }

    // Group by section
    const groupedItems = {}
    contractTextItems.forEach((item, index) => {
        const section = item.section_name || 'Main'
        if (!groupedItems[section]) {
            groupedItems[section] = []
        }
        groupedItems[section].push({ ...item, originalIndex: index })
    })

    let html = ''
    const renderedQidsInSection = new Set()

    for (const section in groupedItems) {
        const sectionId = section.replace(/\s+/g, '-').replace(/[^a-zA-Z0-9-]/g, '')

        html += `
            <div class="final-contract-section" id="section-${sectionId}" data-section="${escapeHtml(section)}">
                <div class="final-contract-section-header">
                    <h5>${escapeHtml(section)}</h5>
                    <button class="icon-btn" onclick="editSectionInline('${escapeHtml(section.replace(/'/g, "\\'"))}')" title="Edit Section">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
                <div class="final-contract-section-content">
        `

        groupedItems[section].forEach(item => {
            html += renderFinalContractItem(item, qidUsageMap, renderedQidsInSection)
        })

        html += `
                </div>
            </div>
        `
    }

    return html
}


function renderFinalContractItem(wrappedItem, qidUsageMap, renderedQidsInSection) {
    const item = (wrappedItem && wrappedItem.item !== undefined) ? wrappedItem.item : wrappedItem;
    const originalIndex = (wrappedItem && wrappedItem.originalIndex !== undefined) ? wrappedItem.originalIndex : 0;

    let text = item.text ?? ''; //  Use raw HTML, not escapeHtml()

    const qidPattern = /\{(QID\d+)\}/g
    let match
    const qidsInText = []

    while ((match = qidPattern.exec(text)) !== null) {
        qidsInText.push(match[1])
    }

    let processedText = text.replace(/\{(QID\d+)\}/g, (fullMatch, qid) => {
        const answer = qidAnswers[qid] || ''
        const question = questionnaireQuestions.find(q => q.qid === qid)
        const questionText = question ? question.text : qid

        if (renderedQidsInSection.has(qid)) {
            if (answer) {
                return `<span class="qid-value filled">${escapeHtml(answer)}</span>`
            }
            return `<span class="qid-reference" title="See ${qid} above">[${qid}]</span>`
        } else {
            renderedQidsInSection.add(qid)

            if (answer) {
                return `<span class="qid-value filled" title="${escapeHtml(questionText)}">${escapeHtml(answer)}</span>`
            }
            return `<span class="qid-placeholder" onclick="focusQuestion('${qid}')" title="Click to fill: ${escapeHtml(questionText)}">[${qid}: ${escapeHtml(questionText.substring(0, 30))}...]</span>`
        }
    })

    const alignStyle = item.align_text || 'left'
    const blurStyle = item.blur_content ? 'filter: blur(4px);' : ''

    let typeClass = ''
    if (item.type === 'HEADLINE') typeClass = 'contract-headline'
    else if (item.type === 'LIST') typeClass = 'contract-list'
    else if (item.type === 'SIGNATURE') typeClass = 'contract-signature'

    let conditionsHtml = ''
    if (item.conditions && item.conditions.length > 0) {
        const condText = item.conditions.map(c =>
            `${c.question_id} ${c.conditions} "${c.question_value}"`
        ).join(' AND ')
        conditionsHtml = `<div class="contract-condition-note"><i class="fas fa-filter"></i> ${condText}</div>`
    }

    return `
        <div class="final-contract-item ${typeClass}" id="contract-${item.tid ?? ''}" style="text-align: ${alignStyle}; ${blurStyle}">
            <div class="contract-item-header">
                <span class="tid-badge">${escapeHtml(item.tid ?? '')}</span>
               
                <button class="icon-btn small" onclick="editContractItemInline(${originalIndex})" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
            ${conditionsHtml}
            <div class="contract-item-text">${processedText}</div>
        </div>
    `
}



function scrollToSection(sectionName) {
    const sectionId = sectionName.replace(/\s+/g, '-').replace(/[^a-zA-Z0-9-]/g, '')
    const element = document.getElementById(`section-${sectionId}`)

    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' })

        // Highlight the nav item
        document.querySelectorAll('.final-nav-item').forEach(item => {
            item.classList.remove('active')
        })

        const navItem = document.querySelector(`.final-nav-item[onclick*="${sectionName.replace(/'/g, "\\'")}"]`)
        if (navItem) {
            navItem.classList.add('active')
        }
    }
}

function focusQuestion(qid) {
    const questionElement = document.getElementById(`question-${qid}`)
    if (questionElement) {
        const questionsPanel = document.getElementById('finalQuestionsPanel')
        if (questionsPanel) {
            questionsPanel.scrollTo({
                top: questionElement.offsetTop - 100,
                behavior: 'smooth'
            })
        }

        // Highlight and focus the input
        questionElement.classList.add('highlight')
        setTimeout(() => {
            questionElement.classList.remove('highlight')
        }, 2000)

        const input = questionElement.querySelector('input, textarea, select')
        if (input) {
            setTimeout(() => input.focus(), 300)
        }
    }
}

function setupScrollSpy() {
    const contractPanel = document.getElementById('finalContractPanel')
    if (!contractPanel) return

    const contentDiv = contractPanel.querySelector('.final-contract-content')
    if (!contentDiv) return

    contentDiv.addEventListener('scroll', () => {
        const sections = contentDiv.querySelectorAll('.final-contract-section')
        let currentSection = null

        sections.forEach(section => {
            const rect = section.getBoundingClientRect()
            const panelRect = contentDiv.getBoundingClientRect()

            if (rect.top <= panelRect.top + 100) {
                currentSection = section.getAttribute('data-section')
            }
        })

        if (currentSection) {
            document.querySelectorAll('.final-nav-item').forEach(item => {
                item.classList.remove('active')
                if (item.textContent.trim() === currentSection) {
                    item.classList.add('active')
                }
            })
        }
    })
}

// Inline editing functions
function editQuestionInline(qid) {
    const question = questionnaireQuestions.find(q => q.qid === qid)
    if (!question) return

    const textEl = document.getElementById(`question-text-${qid}`)
    if (!textEl) return

    const currentText = question.text

    textEl.innerHTML = `
        <div class="inline-edit-container">
            <textarea class="form-control" id="inline-edit-${qid}" rows="2">${escapeHtml(currentText)}</textarea>
            <div class="inline-edit-actions">
                <button class="btn btn-sm btn-success" onclick="saveQuestionInlineEdit('${qid}')">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-sm btn-secondary" onclick="cancelQuestionInlineEdit('${qid}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `

    document.getElementById(`inline-edit-${qid}`).focus()
}

function saveQuestionInlineEdit(qid) {
    const textarea = document.getElementById(`inline-edit-${qid}`)
    if (!textarea) return

    const newText = textarea.value.trim()
    const questionIndex = questionnaireQuestions.findIndex(q => q.qid === qid)

    if (questionIndex !== -1) {
        questionnaireQuestions[questionIndex].text = newText
    }

    // Re-render
    renderFinalEditor()
}

function cancelQuestionInlineEdit(qid) {
    renderFinalEditor()
}


let currentEditingContractIndex = null;
let editContractModalInstance = null;
function editContractItemInline(index) {
    const item = contractTextItems[index];
    if (!item) return;

    currentEditingContractIndex = index;

    const existingModal = document.getElementById('editContractModal');
    if (existingModal) {
        existingModal.remove();
    }

    const modalHTML = `
        <div class="modal fade" id="editContractModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit ${escapeHtml(item.tid)}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Text ID (TID)</label>
                            <input type="text" class="form-control" id="modal-tid" value="${escapeHtml(item.tid)}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section Key</label>
                            <input type="text" class="form-control" id="modal-section-key" value="${escapeHtml(item.section_key)}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section Name</label>
                            <input type="text" class="form-control" id="modal-section-name" value="${escapeHtml(item.section_name)}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" id="modal-type">
                                <option value="HEADLINE"${item.type === 'HEADLINE' ? ' selected' : ''}>Headline</option>
                                <option value="CONTENT"${item.type === 'CONTENT' ? ' selected' : ''}>Content</option>
                                <option value="LIST"${item.type === 'LIST' ? ' selected' : ''}>List</option>
                                <option value="TABLE"${item.type === 'TABLE' ? ' selected' : ''}>Table</option>
                                <option value="SIGNATURE"${item.type === 'SIGNATURE' ? ' selected' : ''}>Signature</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Text Content <small class="text-muted">(HTML allowed, use {QID123} for variables)</small></label>
                            <textarea class="form-control" id="modal-text" rows="8">${escapeHtml(item.text)}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Text Alignment</label>
                                <select class="form-select" id="modal-align">
                                    <option value="left"${item.align_text === 'left' ? ' selected' : ''}>Left</option>
                                    <option value="center"${item.align_text === 'center' ? ' selected' : ''}>Center</option>
                                    <option value="right"${item.align_text === 'right' ? ' selected' : ''}>Right</option>
                                    <option value="justify"${item.align_text === 'justify' ? ' selected' : ''}>Justify</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Blur Content</label>
                                <select class="form-select" id="modal-blur">
                                    <option value="false"${!item.blur_content ? ' selected' : ''}>No</option>
                                    <option value="true"${item.blur_content ? ' selected' : ''}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="saveContractEditBtn">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modalElement = document.getElementById('editContractModal');
    document.getElementById('saveContractEditBtn').addEventListener('click', saveContractInlineEdit);
    editContractModalInstance = new bootstrap.Modal(modalElement);
    editContractModalInstance.show();

    //  Clean up modal from DOM when hidden
    modalElement.addEventListener('hidden.bs.modal', function () {
        modalElement.remove();
        editContractModalInstance = null;
    });
}

function closeInlineEditModal() {
    const modal = document.querySelector('.inline-edit-modal')
    if (modal) {
        modal.remove()
    }
}

function saveContractInlineEdit() {
    if (currentEditingContractIndex === null) return;

    const index = currentEditingContractIndex;

    //  Get values from modal
    const sectionKey = document.getElementById('modal-section-key').value.trim();
    const sectionName = document.getElementById('modal-section-name').value.trim();
    const type = document.getElementById('modal-type').value;
    const text = document.getElementById('modal-text').value;
    const alignText = document.getElementById('modal-align').value;
    const blurContent = document.getElementById('modal-blur').value === 'true';

    if (!sectionName) {
        alert('Section name is required');
        return;
    }

    if (contractTextItems[index]) {
        // Update all fields
        contractTextItems[index].section_key = sectionKey;
        contractTextItems[index].section_name = sectionName;
        contractTextItems[index].type = type;
        contractTextItems[index].text = text;
        contractTextItems[index].align_text = alignText;
        contractTextItems[index].blur_content = blurContent;

        saveContractChanges();
    }

    if (editContractModalInstance) {
        editContractModalInstance.hide();
    }
    currentEditingContractIndex = null;
    if (editingContractIndex !== null && editingContractIndex === index) {
        selectContractItemForEdit(index);
    }

    const cardEl = document.getElementById(`contract-card-${index}`);
    if (cardEl) {
        cardEl.outerHTML = renderContractCard(contractTextItems[index], index);
    }
}

function editSectionInline(sectionName) {
    const newName = prompt('Enter new section name:', sectionName)
    if (newName && newName !== sectionName) {
        contractTextItems.forEach(item => {
            if (item.section_name === sectionName) {
                item.section_name = newName
            }
        })
        saveContractChanges()
        renderFinalEditor()
    }
}
async function saveDocumentToDatabase() {

    if (documentAlreadySaved) {
        Swal.fire({
            icon: 'info',
            title: 'Already Saved',
            text: 'This document has already been saved. Use the Advanced Editor in Step 6 to make changes.',
            confirmButtonColor: '#FF6B35',
        });
        return;
    }

    if (!documentName || !documentName.trim()) {
        Swal.fire({ icon: 'warning', title: 'Document Name Required', text: 'Please enter a document name first' });
        return;
    }

    if (questionnaireQuestions.length === 0) {
        Swal.fire({ icon: 'warning', title: 'No Questions', text: 'Please generate questions first' });
        return;
    }

    const saveBtn = document.querySelector('#step-5 .btn-success');
    if (!saveBtn) return;

    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    try {
        let regularQuestions = [...questionnaireQuestions];

        // new sequential QID
        let qidCounter = 1;
        const qidRemap = {};

        regularQuestions.forEach((q) => {
            const oldQid = q.qid;
            const newQid = `QID${qidCounter}`;
            qidRemap[oldQid] = newQid;
            q.qid = newQid;
            qidCounter++;
        });

        // Update goto references
        regularQuestions.forEach((q) => {
            if (q.goto && q.goto !== 'END' && qidRemap[q.goto]) {
                q.goto = qidRemap[q.goto];
            }
        });

        // Fix sequential goto chain
        regularQuestions.forEach((q, index) => {
            if (index < regularQuestions.length - 1) {
                q.goto = regularQuestions[index + 1].qid;
            } else {
                q.goto = 'END';
            }
        });

        questionnaireQuestions = regularQuestions;

        // Update contract text items with remapped QIDs
        contractTextItems.forEach(item => {
            if (!item.text) return;
            let updatedText = item.text;
            Object.entries(qidRemap).forEach(([oldQid, newQid]) => {
                const pattern = new RegExp(`\\{${escapeRegExp(oldQid)}\\}`, 'g');
                updatedText = updatedText.replace(pattern, `{${newQid}}`);
            });
            item.text = updatedText;
        });

        //  Build request body
        const requestBody = {
            document_name: documentName.trim(),
            questions: questionnaireQuestions,
            contract_sections: contractTextItems,
            article_sections: collectArticleSections(), 
            faqs: collectFaqSections(),  
            parties_type: getSelectedPartiesType(),
            party_labels: collectPartyLabels()
        };

        if (documentId) {
            requestBody.document_id = documentId;
        }

        console.log(' Saving document:', {
            name: documentName,
            questions: questionnaireQuestions.length,
            sections: contractTextItems.length,
            firstQID: questionnaireQuestions[0]?.qid,
            lastQID: questionnaireQuestions[questionnaireQuestions.length - 1]?.qid,
            hasDocId: !!documentId
        });

        const response = await fetch("/admin-dashboard/add-document-generator/beta", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf(),
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
            },
            body: JSON.stringify(requestBody)
        });

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server error. Please check your data and try again.');
        }

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Failed to save document');
        }

        if (data.document_id) {
            documentId = parseInt(data.document_id);
            window.documentId = documentId;
            const hiddenIdField = document.getElementById('document_id');
            if (hiddenIdField) hiddenIdField.value = documentId;
            console.log(' Document ID stored:', documentId);
        }

        documentAlreadySaved = true;
        _lockSaveButton(saveBtn);

        await Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Document saved successfully!',
            showConfirmButton: true,
            confirmButtonText: 'OK',
            confirmButtonColor: '#FF6B35'
        });

        goToStep(6);

    } catch (error) {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;

        Swal.fire({
            icon: 'error',
            title: 'Save Failed',
            html: `<p><strong>Error:</strong></p>
                   <p style="color: #dc3545; font-family: monospace; font-size: 12px;">${error.message}</p>`,
            confirmButtonText: 'OK'
        });
    } 
    // finally {
    //     saveBtn.disabled = false;
    //     saveBtn.innerHTML = originalText;
    // }
}

function collectFaqSections() {
    const faqs = [];

    const faqItems = document.querySelectorAll('#faqSectionsContainer .faq-item');

    if (faqItems.length > 0) {
        faqItems.forEach(item => {
            const questionEl = item.querySelector('[name="faq_title[]"], [name="new_question[]"], input[name*="faq"]');
            const answerEl = item.querySelector('[name="faq_answer[]"], [name="new_answer[]"], textarea[name*="faq"]');
            const question = questionEl?.value?.trim() || '';
            const answer = answerEl?.value?.trim() || '';
            if (question || answer) faqs.push({ question, answer });
        });
    } else {
        const questions = document.querySelectorAll('[name="new_question[]"], [name="faq_title[]"]');
        const answers = document.querySelectorAll('[name="new_answer[]"], [name="faq_answer[]"]');
        questions.forEach((q, i) => {
            const question = q.value.trim();
            const answer = answers[i]?.value?.trim() || '';
            if (question || answer) faqs.push({ question, answer });
        });
    }

    console.log('collectFaqSections:', faqs);
    return faqs;
}

function collectArticleSections() {
    const sections = [];
    const items = document.querySelectorAll('#articleSectionsContainer .article-item, #articleSectionsContainer > div');

    if (items.length > 0) {
        items.forEach(item => {
            const titleEl = item.querySelector('[name="article_title[]"], input[name*="article_title"]');
            const contentEl = item.querySelector('[name="article_content[]"], textarea[name*="article_content"]');
            const title = titleEl?.value?.trim() || '';
            const content = contentEl?.value?.trim() || '';
            if (title || content) sections.push({ title, content });
        });
    } else {
        // Fallback: direct name selectors
        const titleInputs = document.querySelectorAll('[name="article_title[]"]');
        const contentInputs = document.querySelectorAll('[name="article_content[]"]');
        titleInputs.forEach((titleEl, index) => {
            const title = titleEl.value.trim();
            const content = contentInputs[index]?.value?.trim() || '';
            if (title || content) sections.push({ title, content });
        });
    }

    console.log(' collectArticleSections:', sections);
    return sections;
}


// Helper function to show validation error
function showValidationError(fieldId, errorId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(errorId);

    if (field) {
        field.classList.add('is-invalid');
        field.focus();
    }

    if (errorDiv) {
        errorDiv.textContent = message;
    }
}

// Helper function to clear validation error
function clearValidationError(fieldId, errorId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(errorId);

    if (field) {
        field.classList.remove('is-invalid');
    }

    if (errorDiv) {
        errorDiv.textContent = '';
    }
}

function validateStep6Form() {
    let isValid = true;

    const titleField = document.getElementById('title');
    if (titleField && !titleField.value.trim()) {
        showValidationError('title', 'titleError', 'This field is required');
        isValid = false;
    }

    const shortDescField = document.getElementById('short_description');
    if (shortDescField && !shortDescField.value.trim()) {
        showValidationError('short_description', 'shortDescriptionError', 'This field is required');
        isValid = false;
    }

    const metaTitleField = document.getElementById('meta_title');
    if (metaTitleField && !metaTitleField.value.trim()) {
        showValidationError('meta_title', 'metaTitleError', 'This field is required');
        isValid = false;
    }

    const metaDescField = document.getElementById('meta_description');
    if (metaDescField && !metaDescField.value.trim()) {
        showValidationError('meta_description', 'metaDescriptionError', 'This field is required');
        isValid = false;
    }

    const primaryKeywordsField = document.getElementById('primary_keywords');
    if (primaryKeywordsField && !primaryKeywordsField.value.trim()) {
        showValidationError('primary_keywords', 'primaryKeywordsError', 'This field is required');
        isValid = false;
    }

    const categorySelect = document.getElementById('category_id');
    if (categorySelect && categorySelect.selectedOptions.length === 0) {
        const errorDiv = document.getElementById('categoryError');
        if (errorDiv) errorDiv.textContent = 'This field is required';
        isValid = false;
    }

    return isValid;
}
const validationStyles = `
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545 !important;
    }
    
    .form-control.is-invalid:focus,
    .form-select.is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 12px;
        margin-top: 4px;
    }
`;

document.addEventListener("DOMContentLoaded", function () {
    const contractInput = document.getElementById("contractName");
    const contractPreview = document.getElementById("contractNamePreview");
    if (contractInput && contractPreview) {
        contractPreview.value = contractInput.value || "Loan Agreement";
        contractInput.addEventListener("input", function () { contractPreview.value = this.value || "Loan Agreement"; });
    }
    loadPartiesTemplates();
});

//  Filter displayed clauses by type without re-fetching from server
function filterStandardClauses(filterValue) {
    const searchQuery = document.getElementById('clauseSearchInput')?.value || '';
    searchStandardClauses(searchQuery);

    const listEl = document.getElementById('standardClausesList');
    if (!listEl) return;

    const allItems = listEl.querySelectorAll('.standard-clause-item');

    if (!allItems.length) return;

    let visibleCount = 0;

    allItems.forEach(item => {
        const clauseType = item.getAttribute('data-clause-type');

        if (filterValue === 'all') {
            item.style.display = '';
            visibleCount++;
        } else if (filterValue === 'national' && clauseType === 'national') {
            item.style.display = '';
            visibleCount++;
        } else if (filterValue === 'state_specific' && clauseType === 'state_specific') {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    //  Show empty message if nothing matches the filter
    const emptyEl = document.getElementById('standardClausesEmpty');
    if (emptyEl) {
        if (visibleCount === 0) {
            emptyEl.style.display = '';
            emptyEl.innerHTML = `<i class="fas fa-info-circle"></i> No <strong>${filterValue.replace('_', '-')}</strong> clauses available.`;
        } else {
            emptyEl.style.display = 'none';
        }
    }
}

// PArties Section
function onPartiesTypeChange(select) {
    const container = document.getElementById('partyLabelsContainer');
    if (!container) return;
    container.innerHTML = '';

    const selectedOption = select.options[select.selectedIndex];
    if (!selectedOption || !selectedOption.value) {
        selectedPartiesTemplate = null;
        return;
    }

    const templateId = selectedOption.getAttribute('data-id');
    selectedPartiesTemplate = partiesTemplatesData.find(
        t => String(t.id) === String(templateId)
    ) || null;

    const partyACount = parseInt(selectedOption.getAttribute('data-a') || '1');
    const partyBCount = parseInt(selectedOption.getAttribute('data-b') || '1');

    function createPartyInputs(side) {
        const singularHint = side === 'a'
            ? 'e.g. Owner, Client, Buyer, Tenant'
            : 'e.g. Guardian, Provider, Seller, Landlord';
        const pluralHint = side === 'a'
            ? 'e.g. Owners, Clients, Buyers, Tenants'
            : 'e.g. Guardians, Providers, Sellers, Landlords';

        const wrapper = document.createElement('div');
        wrapper.className = 'party-label-group mb-3 p-3 border rounded bg-white';
        wrapper.innerHTML = `
            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label form-label-sm">
                        Singular <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control form-control-sm party-label-input"
                           name="party_labels[${side}][1][singular]"
                           placeholder="${escapeHtml(singularHint)}"
                           required />
                </div>
                <div class="col-6">
                    <label class="form-label form-label-sm">
                        Plural <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control form-control-sm party-label-input"
                           name="party_labels[${side}][1][plural]"
                           placeholder="${escapeHtml(pluralHint)}"
                           required />
                </div>
            </div>
        `;
        return wrapper;
    }

    const card = document.createElement('div');
    card.className = 'card card-body bg-light mb-3';
    card.innerHTML = `
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fas fa-users"></i>
            <h6 class="mb-0">Define Party Role Labels</h6>
            <span class="badge bg-secondary ms-auto">
                ${partyACount} Side-A &nbsp;·&nbsp; ${partyBCount} Side-B
            </span>
        </div>
        <p class="text-muted small mb-3">
            <i class="fas fa-info-circle"></i> 
            Enter custom labels for each party (e.g., Owner/Owners, Guardian/Guardians, Trustee/Trustees)
        </p>
        <div class="row" id="partyLabelRows"></div>
    `;
    container.appendChild(card);

    const rowDiv = card.querySelector('#partyLabelRows');

    const colA = document.createElement('div');
    colA.className = 'col-md-6';
    colA.innerHTML = `<h6 class="fw-bold mb-2" style="font-size:13px;">
        <i class="fas fa-circle-half-stroke"></i> Side A
    </h6>`;
    colA.appendChild(createPartyInputs('a'));
    rowDiv.appendChild(colA);

    const colB = document.createElement('div');
    colB.className = 'col-md-6';
    colB.innerHTML = `<h6 class="fw-bold mb-2" style="font-size:13px;">
        <i class="fas fa-circle-half-stroke fa-flip-horizontal"></i> Side B
    </h6>`;
    colB.appendChild(createPartyInputs('b'));
    rowDiv.appendChild(colB);

    if (selectedPartiesTemplate) {
        const info = document.createElement('div');
        info.className = 'alert alert-info mt-3 mb-0 py-2';
        info.style.fontSize = '12px';
        info.innerHTML = `
            <i class="fas fa-info-circle"></i>
            <strong>${escapeHtml(selectedPartiesTemplate.name)}</strong>
            &nbsp;·&nbsp; ${partyACount + partyBCount} parties total
            &nbsp;·&nbsp; Parties section + signature blocks will use your custom labels
        `;
        container.appendChild(info);
    }
}

function getSelectedPartiesType() {
    const select = document.getElementById('partiesTypeSelect');
    return (select && select.value) ? select.value : null;
}

function buildPartyReplacementMap() {
    const map = {};
    const labels = collectPartyLabels();
    if (!labels) return map;

    ['a', 'b'].forEach(side => {
        if (!labels[side]) return;
        Object.entries(labels[side]).forEach(([idx, forms]) => {
            const S = side.toUpperCase();
            if (forms.singular) map[`[${S}${idx}_SINGULAR]`] = forms.singular;
            if (forms.plural) map[`[${S}${idx}_PLURAL]`] = forms.plural;
        });
    });

    return map;
}

function applyPartyReplacements(text) {
    if (!text) return text;
    const map = buildPartyReplacementMap();
    let result = text;
    Object.entries(map).forEach(([placeholder, value]) => {
        result = result.split(placeholder).join(value);
    });
    return result;
}

// added validation before injection
function injectPartiesTemplateIntoContract() {
    if (!selectedPartiesTemplate) return;

    const tpl = selectedPartiesTemplate;
    const partyACount = tpl.party_a_count || 1;
    const partyBCount = tpl.party_b_count || 1;

    const labels = collectPartyLabels();
    if (!labels) {
        console.warn(' Party labels not filled - skipping injection');
        return;
    }

    // Clean up ALL previously injected items (parties section + signature)
    contractTextItems = contractTextItems.filter(
        item => !item.from_parties_section && !item.from_signature_inject
    );

    // Clean up previously injected party questions
    questionnaireQuestions = questionnaireQuestions.filter(q => !q.from_parties_template);

    // Inject in order: questions → parties section text → signature
    injectPartyQuestionsFromTemplate(tpl);
    injectPartiesSectionText(tpl, partyACount, partyBCount);
    injectSignatureSection(partyACount, partyBCount);

    console.log(` injectPartiesTemplateIntoContract complete:`, {
        template: tpl.name,
        partyACount,
        partyBCount,
        labels,       //  Log the custom labels being used
        totalQuestions: questionnaireQuestions.length,
        totalItems: contractTextItems.length,
    });
}

function injectPartyQuestionsFromTemplate(tpl) {
    if (!tpl.questions || !Array.isArray(tpl.questions) || tpl.questions.length === 0) return;

    //  Remove previously injected party questions cleanly before re-injecting
    questionnaireQuestions = questionnaireQuestions.filter(q => !q.from_parties_template);

    const labels = collectPartyLabels() || {};

    function resolvePartyTokens(text) {
        if (!text) return text;
        return text.replace(/\[([AB])(\d+)_(SINGULAR|PLURAL)\]/gi, (match, side, idx, form) => {
            const label = labels[side.toLowerCase()]?.[idx]?.[form.toLowerCase()];
            return label || match;
        });
    }

    const existingPlaceholders = new Set(
        questionnaireQuestions
            .filter(q => !q.from_parties_template)
            .map(q => q.placeholder || '')
            .filter(Boolean)
    );

    const newQuestions = [];
    tpl.questions.forEach((qDef, idx) => {
        const placeholder = qDef.placeholder || '';
        if (placeholder && existingPlaceholders.has(placeholder)) return;

        const rawText = qDef.question || qDef.text || `Party question ${idx + 1}`;
        const resolvedText = resolvePartyTokens(rawText);

        //  Temp QID uses high offset to avoid colliding with regular QIDs
        const tempQid = `QID_PARTY_${idx}`;

        newQuestions.push({
            id: Date.now() + idx,
            qid: tempQid,
            text: resolvedText,
            type: qDef.type || 'text',
            options: qDef.options || [],
            required: true,
            goto: '',
            userinfo: `Required for ${tpl.name} — parties information`,
            section_name: 'Parties Information',
            placeholder: placeholder,
            from_parties_template: true,
        });
    });

    if (newQuestions.length === 0) return;

    // APPEND party questions at the END instead of prepending
    questionnaireQuestions.push(...newQuestions);

    // Party questions get the last QIDs naturally
    questionnaireQuestions.forEach((q, index) => {
        q.qid = `QID${index + 1}`;
    });

    fixInvalidGotoReferences();

    console.log(` Injected ${newQuestions.length} party question(s) at END, QIDs renumbered from QID1`);
}

function injectSignatureSection(partyACount, partyBCount) {
    const labels = collectPartyLabels() || {};
    const tpl = selectedPartiesTemplate;

    contractTextItems = contractTextItems.filter(item => !item.from_signature_inject);

    let signatureText = '';

    if (tpl && tpl.signature_section_text && tpl.signature_section_text.trim()) {
        signatureText = applyPartyReplacements(tpl.signature_section_text);
        signatureText = signatureText.replace(/\[([A-Z0-9_]+)\]/g, (match, key) => {
            const q = questionnaireQuestions.find(q => q.placeholder === key);
            return q ? `{${q.qid}}` : match;
        });
    } else {
        signatureText = '<p><strong>IN WITNESS WHEREOF</strong>, the parties have executed this Agreement as of the date first written above.</p>\n';

        function buildDynamicSignatureBlock(side, partyNumber) {
            const sideKey = side.toLowerCase();
            const singularLabel = labels[sideKey]?.['1']?.singular || '';
            const pluralLabel = labels[sideKey]?.['1']?.plural || '';

            if (!singularLabel) {
                console.warn(`Missing labels for signature block Side ${side.toUpperCase()} Party ${partyNumber}`);
                return '';
            }

            const partyQuestions = questionnaireQuestions.filter(q =>
                q.from_parties_template &&
                q.section_name === 'Parties Information' &&
                (q.placeholder || '').toUpperCase().startsWith(`${side.toUpperCase()}${partyNumber}`)
            );

            const nameQuestion = partyQuestions.find(q =>
                (q.placeholder || '').toUpperCase() === `${side.toUpperCase()}${partyNumber}_NAME` ||
                (q.placeholder || '').toUpperCase().endsWith('_NAME')
            );

            const detailQuestions = partyQuestions.filter(q => q !== nameQuestion);
            const nameRef = nameQuestion ? `{${nameQuestion.qid}}` : '__________';

            const totalForSide = side.toLowerCase() === 'a' ? partyACount : partyBCount;
            const displayLabel = totalForSide > 1
                ? `${singularLabel.toUpperCase()} ${partyNumber}`
                : singularLabel.toUpperCase();

            let block = `\n<p><strong>${escapeHtml(displayLabel)}:</strong></p>\n`;
            block += `<p>Full Legal Name: ${nameRef}</p>\n`;

            detailQuestions.forEach(dq => {
                const fieldLabel = dq.text
                    .replace(/^(full legal name|name)\s+(of|for)\s+/i, '')
                    .replace(/of\s+(Service Provider|Client|Party)/i, '')
                    .trim();
                block += `<p>${escapeHtml(fieldLabel)}: {${dq.qid}}</p>\n`;
            });

            block += `<p>Signature: ___________________________&nbsp;&nbsp;&nbsp;&nbsp; Date: ___________________________</p>\n`;
            block += `<p>&nbsp;</p>\n`;

            return block;
        }

        for (let i = 1; i <= partyACount; i++) {
            signatureText += buildDynamicSignatureBlock('a', i);
        }
        for (let i = 1; i <= partyBCount; i++) {
            signatureText += buildDynamicSignatureBlock('b', i);
        }
    }

    const maxTid = contractTextItems.length > 0
        ? Math.max(...contractTextItems.map(item => parseInt(item.tid?.replace(/\D/g, '')) || 0))
        : 0;

    const wrappedSignature = `<p><strong>SIGNATURES</strong></p>\n${signatureText}`;

    contractTextItems.push({
        tid: `TID${maxTid + 1}`,
        section_key: 'Signatures',
        section_id: null,
        section_name: 'Signatures',
        type: 'SIGNATURE',
        text: wrappedSignature,
        align_text: 'left',
        blur_content: false,
        conditions: [],
        from_signature_inject: true,
    });
}

const _originalLoadFinalDocument = typeof loadFinalDocument === 'function' ? loadFinalDocument : null;


function loadFinalDocument() {
    if (!documentName || !documentName.trim()) {
        documentName = document.getElementById('contractName')?.value?.trim()
            || document.getElementById('contractNamePreview')?.value?.trim()
            || '';
    }

    if (!documentName || !documentName.trim()) {
        documentName = 'Untitled Document';
    }

    const labels = collectPartyLabels();
    if (selectedPartiesTemplate && !labels) {
        Swal.fire({
            icon: 'warning',
            title: 'Party Labels Required',
            text: 'Please fill in all singular and plural labels for each party before proceeding.',
            confirmButtonColor: '#FF6B35'
        });
        return;
    }

    injectPartiesTemplateIntoContract();
    applySelectedStandardClausesSync();
    saveContractChanges();
    renderedQidInputs = new Set();

    questionnaireQuestions.forEach(q => {
        if (!qidAnswers[q.qid]) {
            qidAnswers[q.qid] = '';
        }
    });

    renderFinalEditor();

    const partiesItem   = contractTextItems.find(i => i.from_parties_section);
    const signatureItem = contractTextItems.find(i => i.from_signature_inject);
    const stdClauses    = contractTextItems.filter(i => i.standard_clause_id);

    console.log(' loadFinalDocument complete:', {
        documentName,  
        template: selectedPartiesTemplate?.name || 'none',
        partiesInjected: !!partiesItem,
        signatureInjected: !!signatureItem,
        standardClausesInjected: stdClauses.length,
        totalItems: contractTextItems.length
    });
}

function applySelectedStandardClausesSync() {
    if (!selectedStandardClauseIds || selectedStandardClauseIds.length === 0) return;

    selectedStandardClauseIds.forEach(id => {
        if (contractTextItems.some(item => item.standard_clause_id == id)) return;
        const checkbox = document.getElementById(`sc_${id}`);
        if (!checkbox) return;

        const title = checkbox.getAttribute('data-title') || '';
        const description = checkbox.getAttribute('data-description') || '';
        const clauseType = checkbox.getAttribute('data-clause-type') || '';
        const states = JSON.parse(checkbox.getAttribute('data-states') || '[]');

        const maxTid = contractTextItems.length
            ? Math.max(...contractTextItems.map(item => parseInt(item.tid?.replace(/\D/g, '')) || 0))
            : 0;

        //  Insert before last section (signatures), same positioning logic
        let insertIndex = contractTextItems.length;
        const distinctSections = [...new Set(contractTextItems.map(i => i.section_name).filter(Boolean))];
        if (distinctSections.length >= 2) {
            const lastSection = distinctSections[distinctSections.length - 1];
            const idx = contractTextItems.findIndex(i => i.section_name === lastSection);
            if (idx !== -1) insertIndex = idx;
        }
        let fullText = `<h4>${escapeHtml(title)}</h4>`;
        if (description && description.trim()) {
            fullText += `\n<p>${escapeHtml(description)}</p>`;
        }

        contractTextItems.splice(insertIndex, 0, {
            tid: `TID${maxTid + 1}`,
            section_key: 'Standard_Clauses',
            section_id: null,
            section_name: 'Standard Clauses',
            type: 'CONTENT',
            text: fullText,
            align_text: 'left',
            blur_content: false,
            conditions: [],
            standard_clause_id: id,
            standard_clause_title: title
        });
    });
}


async function loadPartiesTemplates() {
    try {
        const response = await fetch('/admin-dashboard/document-generator/parties-templates', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf()
            }
        });

        if (!response.ok) throw new Error('Failed to fetch parties templates');

        const data = await response.json();
        if (!data.success) throw new Error(data.message || 'Error loading templates');

        partiesTemplatesData = data.data || [];

        const select = document.getElementById('partiesTypeSelect');
        if (!select) return;

        select.innerHTML = '<option value="">— Select Parties Type —</option>';

        partiesTemplatesData.forEach(tpl => {
            const option = document.createElement('option');
            option.value = tpl.parties_type;
            option.textContent = `${tpl.parties_type} — ${tpl.name}`;
            option.setAttribute('data-a', tpl.party_a_count);
            option.setAttribute('data-b', tpl.party_b_count);
            option.setAttribute('data-id', tpl.id);
            select.appendChild(option);
        });

    } catch (error) {
        console.warn('loadPartiesTemplates error:', error.message);
    }
}

// Standard Section Code
// let selectedStandardClauseIds = [];
async function loadStandardClausesForStep1(state = null) {
    const loadingEl = document.getElementById('standardClausesLoading');
    const listEl = document.getElementById('standardClausesList');
    const emptyEl = document.getElementById('standardClausesEmpty');

    if (!loadingEl) return;

    loadingEl.style.display = '';
    listEl.style.display = 'none';
    emptyEl.style.display = 'none';

    try {
        let url = '/admin-dashboard/standard/section/documents/api';
        if (state) url += `?state=${encodeURIComponent(state)}`;

        const response = await fetch(url, {
            headers: {
                'X-CSRF-TOKEN': csrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        loadingEl.style.display = 'none';

        if (!data.success || !data.data.length) {
            emptyEl.style.display = '';
            return;
        }
        listEl.innerHTML = data.data.map(clause => `
            <div class="standard-clause-item mb-2" data-clause-type="${clause.clause_type}">
                <div class="card border-0 shadow-sm clause-card"
                     style="border-radius:12px; transition:all .2s ease;">
        
                    <div class="card-body p-2">
        
                        <div class="d-flex justify-content-between align-items-start">
        
                            <!-- Left Side -->
                            <div class="d-flex align-items-start gap-2" style="flex:1;">
        
                                <input 
                                    type="checkbox" 
                                    class="form-check-input mt-1 standard-clause-checkbox"
                                    id="sc_${clause.id}"
                                    value="${clause.id}"
                                    data-title="${escapeAttribute(clause.title)}"
                                    data-description="${escapeAttribute(clause.description || '')}"
                                    data-clause-type="${clause.clause_type}"
                                    data-states='${JSON.stringify(clause.states || [])}'
                                    onchange="toggleStandardClause(this)"
                                >
        
                                <label for="sc_${clause.id}" 
                                       class="mb-0 w-100" 
                                       style="cursor:pointer;">
        
                                    <div class="fw-semibold text-dark"
                                         style="font-size:14px; line-height:1.3;">
                                        ${escapeHtml(clause.title)}
                                    </div>
                                </label>
                            </div>
        
                            <!-- Right Side -->
                            <div class="text-end ms-2" style="min-width:130px;">
        
                                ${clause.clause_type === 'national'
                ? `
                                        <span class="badge bg-primary px-3 py-1"
                                              style="font-size:10px; letter-spacing:.3px;">
                                            National
                                        </span>
                                      `
                : `
                                        <span class="badge bg-warning text-dark px-3 py-1"
                                              style="font-size:10px; letter-spacing:.3px;">
                                            State-Specific
                                        </span>
        
                                        ${(clause.states && clause.states.length) ? `
                                            <div class="mt-2 d-flex flex-wrap justify-content-end gap-1">
                                                ${clause.states.map(s => `
                                                    <span class="badge bg-light text-dark border"
                                                          style="font-size:9px;">
                                                        ${s}
                                                    </span>
                                                `).join('')}
                                            </div>
                                        ` : ''}
                                      `
            }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        listEl.style.display = '';

        const searchBox = document.getElementById('standardClausesSearch');
        if (searchBox) searchBox.style.display = '';

        // Restore checked state for already-selected clauses + refresh bar
        selectedStandardClauseIds.forEach(id => {
            const cb = document.getElementById(`sc_${id}`);
            if (cb) cb.checked = true;
        });

        updateStep1ApplyBar();

    } catch (error) {
        loadingEl.style.display = 'none';
        emptyEl.style.display = '';
        console.error('Failed to load standard clauses:', error);
    }

}

// Search standard clauses by title
function searchStandardClauses(query) {
    const listEl = document.getElementById('standardClausesList');
    if (!listEl) return;

    const items = listEl.querySelectorAll('.standard-clause-item');
    const normalizedQuery = query.toLowerCase().trim();
    const filterValue = document.getElementById('clauseTypeFilter')?.value || 'all';
    let visibleCount = 0;

    items.forEach(item => {
        const titleEl = item.querySelector('label .fw-semibold');
        const title = titleEl ? titleEl.textContent.toLowerCase() : '';
        const clauseType = item.getAttribute('data-clause-type');

        const matchesSearch = !normalizedQuery || title.includes(normalizedQuery);
        const matchesFilter = filterValue === 'all' || clauseType === filterValue;

        if (matchesSearch && matchesFilter) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    const emptyEl = document.getElementById('standardClausesEmpty');
    if (emptyEl) {
        emptyEl.style.display = visibleCount === 0 ? '' : 'none';
        if (visibleCount === 0) {
            emptyEl.innerHTML = `<i class="fas fa-search"></i> No clauses match "<strong>${escapeHtml(query)}</strong>"`;
        }
    }
}

function toggleStandardClause(checkbox) {
    const id = String(checkbox.value);
    if (checkbox.checked) {
        if (!selectedStandardClauseIds.includes(id)) {
            selectedStandardClauseIds.push(id);
        }
    } else {
        selectedStandardClauseIds = selectedStandardClauseIds.filter(x => x !== id);
    }
    updateStep1ApplyBar();
}

// Apply selected standard clauses into contract items
async function applySelectedStandardClauses() {
    if (!selectedStandardClauseIds.length) return;

    try {
        const url = '/admin-dashboard/standard/section/documents/api';
        const response = await fetch(url, {
            headers: {
                'X-CSRF-TOKEN': csrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (!data.success) return;

        const selectedClauses = data.data.filter(c =>
            selectedStandardClauseIds.includes(String(c.id))
        );

        selectedClauses.forEach(clause => {
            // Avoid duplicates
            if (contractTextItems.some(item => item.standard_clause_id == clause.id)) return;

            const maxTid = contractTextItems.length
                ? Math.max(...contractTextItems.map(item =>
                    parseInt(item.tid?.replace(/\D/g, '')) || 0))
                : 0;

            let insertIndex = contractTextItems.length;
            const distinctSections = [new Set(contractTextItems.map(i => i.section_name).filter(Boolean))];
            if (distinctSections.length >= 2) {
                const lastSection = distinctSections[distinctSections.length - 1];
                const idx = contractTextItems.findIndex(i => i.section_name === lastSection);
                if (idx !== -1) insertIndex = idx;
            }

            // Build full text with title + description so it renders in contract preview
            let fullText = `<h4>${escapeHtml(clause.title)}</h4>`;
            if (clause.description && clause.description.trim()) {
                fullText += `\n<p>${clause.description}</p>`;
            }

            contractTextItems.splice(insertIndex, 0, {
                tid: `TID${maxTid + 1}`,
                section_key: 'Standard_Clauses',
                section_id: null,
                section_name: 'Standard Clauses',
                type: 'CONTENT',
                text: fullText,
                align_text: 'left',
                blur_content: false,
                conditions: [],
                standard_clause_id: clause.id,
                standard_clause_title: clause.title
            });
        });

        saveContractChanges();

    } catch (error) {
        console.error('Failed to apply standard clauses:', error);
        throw error;
    }
}

//   Module-level function to show/hide the apply bar in Step 1
function updateStep1ApplyBar() {
    const bar = document.getElementById('standardClausesApplyBar');
    const countEl = document.getElementById('applyStandardClausesCount');
    const infoEl = document.getElementById('applyStandardClausesInfo');

    if (!bar) return;

    const count = selectedStandardClauseIds.length;
    bar.style.display = count > 0 ? '' : 'none';
    if (countEl) countEl.textContent = count;
    if (infoEl) infoEl.textContent = count > 0
        ? `Will be inserted before the last section`
        : '';
}

//  fetches full description and updates contractTextItems with real content
async function applyStandardClausesNow() {
    const btn = document.getElementById('applyStandardClausesBtn');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';

    try {
        await applySelectedStandardClauses();
        updateStep1ApplyBar();

        if (currentStep === 4) {
            renderContractEditor();
        }

        Swal.fire({
            icon: 'success',
            title: 'Clauses Staged!',
            text: `${selectedStandardClauseIds.length} standard clause(s) will be inserted into the contract when you reach Step 5 or save to database.`,
            timer: 2800,
            showConfirmButton: false
        });
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'Error', text: error.message });
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}

function injectPartiesSectionText(tpl, partyACount, partyBCount) {
    const labels = collectPartyLabels() || {};

    contractTextItems = contractTextItems.filter(item => !item.from_parties_section);

    function resolveToken(side, idx, form) {
        return labels[side.toLowerCase()]?.[idx]?.[form] || '';
    }

    let processedText = '';

    const rawPartiesText = tpl.parties_section_text;
    const partiesTextArray = Array.isArray(rawPartiesText)
        ? rawPartiesText
        : (rawPartiesText && typeof rawPartiesText === 'string'
            ? (() => { try { return JSON.parse(rawPartiesText); } catch(e) { return null; } })()
            : null);

    const partiesTextString = partiesTextArray && partiesTextArray.length > 0
        ? partiesTextArray.map(block => block.text || block.content || '').filter(Boolean).join('\n\n')
        : (typeof rawPartiesText === 'string' ? rawPartiesText.trim() : '');

    if (partiesTextString && partiesTextString.trim()) {
        let rawText = partiesTextString;
        rawText = applyPartyReplacements(rawText);
        rawText = rawText.replace(/\[([A-Z0-9_]+)\]/g, (match, key) => {
            const q = questionnaireQuestions.find(q => q.placeholder === key);
            return q ? `{${q.qid}}` : '__________';
        });

        const lines = rawText.split(/\n+/).map(l => l.trim()).filter(Boolean);
        processedText = lines.map(line => `<p style="margin:0 0 8px 0;">${line}</p>`).join('\n');

    } else {
        const parts = [];

        const sideALabel = resolveToken('a', 1, 'singular') || 'Party A';
        parts.push(`<p style="margin:0 0 6px 0;"><strong>${escapeHtml(sideALabel)}:</strong></p>`);

        for (let i = 1; i <= partyACount; i++) {
            const singular = resolveToken('a', i, 'singular');
            const plural = resolveToken('a', i, 'plural');
            if (!singular) continue;

            const nameQ = questionnaireQuestions
                .slice().reverse()
                .find(q => q.from_parties_template &&
                    (q.placeholder || '').toUpperCase().startsWith(`A${i}`));

            const nameRef = nameQ ? `{${nameQ.qid}}` : '__________';

            const addrQ = questionnaireQuestions
                .slice().reverse()
                .find(q => q.from_parties_template &&
                    (q.placeholder || '').toUpperCase().includes(`A${i}_ADDRESS`));
            const addrRef = addrQ ? `{${addrQ.qid}}` : null;

            let line = `${nameRef}`;
            if (addrRef) line += `, with its registered address at ${addrRef}`;
            line += ` (hereinafter referred to as the "<strong>${escapeHtml(singular)}</strong>").`;

            parts.push(`<p style="margin:0 0 8px 0;">${line}</p>`);
        }

        if (partyACount > 1) {
            const pluralA = resolveToken('a', 1, 'plural');
            parts.push(`<p style="margin:0 0 12px 0;">(collectively referred to as the "<strong>${escapeHtml(pluralA)}</strong>").</p>`);
        }

        const sideBLabel = resolveToken('b', 1, 'singular') || 'Party B';
        parts.push(`<p style="margin:0 0 6px 0;"><strong>${escapeHtml(sideBLabel)}${partyBCount > 1 ? 's' : ''}:</strong></p>`);

        for (let i = 1; i <= partyBCount; i++) {
            const singular = resolveToken('b', i, 'singular');
            const plural = resolveToken('b', i, 'plural');
            if (!singular) continue;

            const nameQ = questionnaireQuestions
                .slice().reverse()
                .find(q => q.from_parties_template &&
                    (q.placeholder || '').toUpperCase().startsWith(`B${i}`));

            const nameRef = nameQ ? `{${nameQ.qid}}` : '__________';

            const addrQ = questionnaireQuestions
                .slice().reverse()
                .find(q => q.from_parties_template &&
                    (q.placeholder || '').toUpperCase().includes(`B${i}_ADDRESS`));
            const addrRef = addrQ ? `{${addrQ.qid}}` : null;

            let line = `${nameRef}`;
            if (addrRef) line += `, residing at ${addrRef}`;
            line += ` (hereinafter referred to as the "<strong>${escapeHtml(singular)}</strong>").`;

            parts.push(`<p style="margin:0 0 8px 0;">${line}</p>`);
        }

        if (partyBCount > 1) {
            const pluralB = resolveToken('b', 1, 'plural');
            parts.push(`<p style="margin:0 0 12px 0;">(collectively referred to as the "<strong>${escapeHtml(pluralB)}</strong>").</p>`);
        }

        processedText = parts.join('\n');
    }

    let insertIndex = 0;
    const firstHeadlineIndex = contractTextItems.findIndex(item => item.type === 'HEADLINE');
    if (firstHeadlineIndex !== -1) insertIndex = firstHeadlineIndex + 1;

    const maxTid = contractTextItems.length > 0
        ? Math.max(...contractTextItems.map(item => parseInt(item.tid?.replace(/\D/g, '')) || 0))
        : 0;

    contractTextItems.splice(insertIndex, 0, {
        tid: `TID${maxTid + 1}`,
        section_key: 'Parties',
        section_id: null,
        section_name: 'Parties',
        type: 'CONTENT',
        text: processedText,
        align_text: 'left',
        blur_content: false,
        conditions: [],
        from_parties_section: true,
    });
}

function injectPartiesSectionText(tpl, partyACount, partyBCount) {
    const labels = collectPartyLabels() || {};

    contractTextItems = contractTextItems.filter(item => !item.from_parties_section);

    function resolveToken(side, idx, form) {
        return labels[side.toLowerCase()]?.[idx]?.[form] || '';
    }

    let processedText = '';

    const rawPartiesText = tpl.parties_section_text;
    let partiesTextString = '';

    if (Array.isArray(rawPartiesText) && rawPartiesText.length > 0) {
        partiesTextString = rawPartiesText
            .map(block => block.text || block.content || '')
            .filter(Boolean)
            .join('\n\n');

    } else if (typeof rawPartiesText === 'string' && rawPartiesText.trim()) {
        try {
            const parsed = JSON.parse(rawPartiesText);
            if (Array.isArray(parsed) && parsed.length > 0) {
                partiesTextString = parsed
                    .map(block => (typeof block === 'object' ? block.text || block.content || '' : block))
                    .filter(Boolean)
                    .join('\n\n');
            } else {
                partiesTextString = rawPartiesText.trim();
            }
        } catch (e) {
            partiesTextString = rawPartiesText.trim();
        }

    } else if (rawPartiesText && typeof rawPartiesText === 'object' && !Array.isArray(rawPartiesText)) {
        partiesTextString = rawPartiesText.text || rawPartiesText.content || '';
    }

    if (partiesTextString && partiesTextString.trim()) {
        let rawText = partiesTextString;
        rawText = applyPartyReplacements(rawText);
        rawText = rawText.replace(/\[([A-Z0-9_]+)\]/g, (match, key) => {
            const q = questionnaireQuestions.find(q => q.placeholder === key);
            return q ? `{${q.qid}}` : '__________';
        });

        const lines = rawText.split(/\n+/).map(l => l.trim()).filter(Boolean);
        processedText = lines.map(line => `<p style="margin:0 0 8px 0;">${line}</p>`).join('\n');

    } else {
        const parts = [];

        const sideALabel = resolveToken('a', 1, 'singular') || 'Party A';
        parts.push(`<p style="margin:0 0 6px 0;"><strong>${escapeHtml(sideALabel)}:</strong></p>`);

        for (let i = 1; i <= partyACount; i++) {
            const singular = resolveToken('a', i, 'singular');
            const plural   = resolveToken('a', i, 'plural');
            if (!singular) continue;

            const nameQ = questionnaireQuestions.slice().reverse()
                .find(q => q.from_parties_template &&
                    (q.placeholder || '').toUpperCase().startsWith(`A${i}`));
            const nameRef = nameQ ? `{${nameQ.qid}}` : '__________';

            const addrQ = questionnaireQuestions.slice().reverse()
                .find(q => q.from_parties_template &&
                    (q.placeholder || '').toUpperCase().includes(`A${i}_ADDRESS`));
            const addrRef = addrQ ? `{${addrQ.qid}}` : null;

            let line = nameRef;
            if (addrRef) line += `, with its registered address at ${addrRef}`;
            line += ` (hereinafter referred to as the "<strong>${escapeHtml(singular)}</strong>").`;
            parts.push(`<p style="margin:0 0 8px 0;">${line}</p>`);
        }

        if (partyACount > 1) {
            const pluralA = resolveToken('a', 1, 'plural');
            parts.push(`<p style="margin:0 0 12px 0;">(collectively referred to as the "<strong>${escapeHtml(pluralA)}</strong>").</p>`);
        }

        const sideBLabel = resolveToken('b', 1, 'singular') || 'Party B';
        parts.push(`<p style="margin:0 0 6px 0;"><strong>${escapeHtml(sideBLabel)}${partyBCount > 1 ? 's' : ''}:</strong></p>`);

        for (let i = 1; i <= partyBCount; i++) {
            const singular = resolveToken('b', i, 'singular');
            const plural   = resolveToken('b', i, 'plural');
            if (!singular) continue;

            const nameQ = questionnaireQuestions.slice().reverse()
                .find(q => q.from_parties_template &&
                    (q.placeholder || '').toUpperCase().startsWith(`B${i}`));
            const nameRef = nameQ ? `{${nameQ.qid}}` : '__________';

            const addrQ = questionnaireQuestions.slice().reverse()
                .find(q => q.from_parties_template &&
                    (q.placeholder || '').toUpperCase().includes(`B${i}_ADDRESS`));
            const addrRef = addrQ ? `{${addrQ.qid}}` : null;

            let line = nameRef;
            if (addrRef) line += `, residing at ${addrRef}`;
            line += ` (hereinafter referred to as the "<strong>${escapeHtml(singular)}</strong>").`;
            parts.push(`<p style="margin:0 0 8px 0;">${line}</p>`);
        }

        if (partyBCount > 1) {
            const pluralB = resolveToken('b', 1, 'plural');
            parts.push(`<p style="margin:0 0 12px 0;">(collectively referred to as the "<strong>${escapeHtml(pluralB)}</strong>").</p>`);
        }

        processedText = parts.join('\n');
    }

    let insertIndex = 0;
    const firstHeadlineIndex = contractTextItems.findIndex(item => item.type === 'HEADLINE');
    if (firstHeadlineIndex !== -1) insertIndex = firstHeadlineIndex + 1;

    const maxTid = contractTextItems.length > 0
        ? Math.max(...contractTextItems.map(item => parseInt(item.tid?.replace(/\D/g, '')) || 0))
        : 0;

    contractTextItems.splice(insertIndex, 0, {
        tid: `TID${maxTid + 1}`,
        section_key: 'Parties',
        section_id: null,
        section_name: 'Parties',
        type: 'CONTENT',
        text: processedText,
        align_text: 'left',
        blur_content: false,
        conditions: [],
        from_parties_section: true,
    });
}

function collectPartyLabels() {
    const labels = { a: {}, b: {} };
    let hasLabels = false;
    let hasEmptyRequired = false; 

    document.querySelectorAll('[name^="party_labels"]').forEach(input => {
        const match = input.name.match(/party_labels\[([ab])\]\[(\d+)\]\[(\w+)\]/);
        if (!match) return;
        const [, side, index, form] = match;
        if (!labels[side][index]) labels[side][index] = {};

        const value = input.value.trim();
        labels[side][index][form] = value;

        if (value) {
            hasLabels = true;
        } else if (input.hasAttribute('required')) {
            hasEmptyRequired = true;
        }
    });

    if (!hasLabels || hasEmptyRequired) {
        return null;
    }

    return labels;
}

function getDocumentContext() {
    const title      = document.getElementById('title')?.value?.trim() || '';
    const docName    = window.documentName || title || 'Untitled Document';
    const questions  = (typeof questionnaireQuestions !== 'undefined') ? questionnaireQuestions : [];
    const sections   = (typeof contractTextItems !== 'undefined') ? contractTextItems : [];

    return {
        document_name: docName,
        title: title,
        questions_count: questions.length,
        sections_count: sections.length,
        section_names: [...new Set(sections.map(s => s.section_name).filter(Boolean))].slice(0, 10),
        question_samples: questions.slice(0, 5).map(q => q.text),
    };
}

async function callAIAutofill(fieldType, extraContext = {}) {
    const ctx = { ...getDocumentContext(), ...extraContext };

    const response = await fetch('/admin-dashboard/api/ai-autofill', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf(),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ field_type: fieldType, context: ctx })
    });

    if (!response.ok) {
        const err = await response.json().catch(() => ({}));
        throw new Error(err.message || `HTTP ${response.status}`);
    }

    const data = await response.json();
    if (!data.success) throw new Error(data.message || 'AI request failed');
    return data.content;
}


function setAutofillBtnLoading(btn, loading) {
    if (!btn) return;
    if (loading) {
        btn.dataset.originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
        btn.classList.add('ai-btn-loading');
    } else {
        btn.innerHTML = btn.dataset.originalHtml || '<i class="fas fa-magic"></i>';
        btn.disabled = false;
        btn.classList.remove('ai-btn-loading');
    }
}

async function autofillField(fieldId, fieldType, btnEl, extraContext = {}) {
    if (autofillInProgress) return;
    setAutofillBtnLoading(btnEl, true);
    showStep6AutofillToast(`Generating ${fieldType.replace(/_/g, ' ')}…`);

    try {
        const content = await callAIAutofill(fieldType, extraContext);

        const el = document.getElementById(fieldId);
        if (!el) throw new Error(`Element #${fieldId} not found`);

        // Handle CKEditor instances
        if (typeof editors !== 'undefined' && editors[fieldId]) {
            editors[fieldId].setData(content);
        } else {
            el.value = content;
            el.dispatchEvent(new Event('input', { bubbles: true }));
        }

        el.classList.add('ai-filled-flash');
        setTimeout(() => el.classList.remove('ai-filled-flash'), 1200);
        showStep6AutofillToast(`✓ ${fieldType.replace(/_/g, ' ')} filled!`, 'success');
    } catch (err) {
        showStep6AutofillToast(`Error: ${err.message}`, 'error');
    } finally {
        setAutofillBtnLoading(btnEl, false);
    }
}

/* Fill ALL fields with a single API call */
async function autofillAllFields() {
    if (autofillInProgress) return;
    autofillInProgress = true;

    const masterBtn = document.getElementById('ai-autofill-all-btn');
    if (masterBtn) {
        masterBtn.dataset.originalHtml = masterBtn.innerHTML;
        masterBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating All Fields…';
        masterBtn.disabled = true;
    }

    showStep6AutofillToast('AI is filling all fields…');

    try {
        const rawContent = await callAIAutofill('fill_all');

        // Parse JSON response
        let parsed;
        try {
            const clean = rawContent.replace(/```json|```/g, '').trim();
            parsed = JSON.parse(clean);
        } catch {
            throw new Error('AI returned invalid JSON. Please try again.');
        }

        const fieldMap = {
            // title:               'title',
            short_description:   'short_description',
            meta_title:          'meta_title',
            meta_description:    'meta_description',
            primary_keywords:    'primary_keywords',
            secondary_keywords:  'secondary_keywords',
        };

        let filled = 0;
        for (const [key, elId] of Object.entries(fieldMap)) {
            if (!parsed[key]) continue;
            const el = document.getElementById(elId);
            if (!el) continue;

            if (typeof editors !== 'undefined' && editors[elId]) {
                editors[elId].setData(parsed[key]);
            } else {
                el.value = parsed[key];
                el.dispatchEvent(new Event('input', { bubbles: true }));
            }

            el.classList.add('ai-filled-flash');
            setTimeout(() => el.classList.remove('ai-filled-flash'), 1400);
            filled++;
        }

        showStep6AutofillToast(`✓ ${filled} fields filled successfully!`, 'success');
    } catch (err) {
        showStep6AutofillToast(`Error: ${err.message}`, 'error');
    } finally {
        autofillInProgress = false;
        if (masterBtn) {
            masterBtn.innerHTML = masterBtn.dataset.originalHtml || 'AI Autofill All Fields';
            masterBtn.disabled = false;
        }
    }
}

async function autofillArticleSection(index, btnEl) {
    setAutofillBtnLoading(btnEl, true);
    const titleEl   = document.querySelector(`input[name="article_title[]"]:nth-of-type(${index + 1})`);
    const contentEl = document.querySelectorAll('textarea[name="article_content[]"]')[index];
    const existingTitle = titleEl?.value?.trim() || '';

    try {
        showStep6AutofillToast('Generating article section…');

        if (!existingTitle) {
            const titleContent = await callAIAutofill('article_title', {
                sectionHint: `section ${index + 1}`,
            });
            if (titleEl) {
                titleEl.value = titleContent;
                titleEl.classList.add('ai-filled-flash');
                setTimeout(() => titleEl.classList.remove('ai-filled-flash'), 1200);
            }
        }

        const finalTitle = titleEl?.value?.trim() || '';
        const articleContent = await callAIAutofill('article_content', {
            articleTitle: finalTitle,
        });

        if (contentEl) {
            const editorKey = contentEl.id;
            if (editorKey && typeof editors !== 'undefined' && editors[editorKey]) {
                editors[editorKey].setData(articleContent);
            } else {
                contentEl.value = articleContent;
            }
            contentEl.classList.add('ai-filled-flash');
            setTimeout(() => contentEl.classList.remove('ai-filled-flash'), 1200);
        }

        showStep6AutofillToast('✓ Article section filled!', 'success');
    } catch (err) {
        showStep6AutofillToast(`Error: ${err.message}`, 'error');
    } finally {
        setAutofillBtnLoading(btnEl, false);
    }
}

async function autofillFaqItem(index, btnEl) {
    setAutofillBtnLoading(btnEl, true);
    const faqItems      = document.querySelectorAll('#faqSectionsContainer .faq-item');
    const faqItem       = faqItems[index];
    if (!faqItem) { setAutofillBtnLoading(btnEl, false); return; }

    const questionEl    = faqItem.querySelector('input[name="faq_title[]"]');
    const answerEl      = faqItem.querySelector('textarea[name="faq_answer[]"]');
    const existingQ     = questionEl?.value?.trim() || '';

    try {
        showStep6AutofillToast('Generating FAQ…');

        let finalQuestion = existingQ;
        if (!existingQ) {
            finalQuestion = await callAIAutofill('faq_question', { faqIndex: index + 1 });
            if (questionEl) {
                questionEl.value = finalQuestion;
                questionEl.classList.add('ai-filled-flash');
                setTimeout(() => questionEl.classList.remove('ai-filled-flash'), 1200);
            }
        }

        const answer = await callAIAutofill('faq_answer', { question: finalQuestion });
        if (answerEl) {
            answerEl.value = answer;
            answerEl.classList.add('ai-filled-flash');
            setTimeout(() => answerEl.classList.remove('ai-filled-flash'), 1200);
        }

        showStep6AutofillToast('✓ FAQ filled!', 'success');
    } catch (err) {
        showStep6AutofillToast(`Error: ${err.message}`, 'error');
    } finally {
        setAutofillBtnLoading(btnEl, false);
    }
}

let toastTimeout;
function showStep6AutofillToast(message, type = 'info') {
    let toast = document.getElementById('ai-autofill-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'ai-autofill-toast';
        document.body.appendChild(toast);
    }

    const colors = {
        info:    '#2c3782',
        success: '#10b981',
        error:   '#ef4444',
    };

    toast.style.cssText = `
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: ${colors[type] || colors.info};
        color: white;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        z-index: 99999;
        box-shadow: 0 4px 16px rgba(0,0,0,0.18);
        transition: opacity 0.3s;
        opacity: 1;
        max-width: 320px;
        pointer-events: none;
    `;
    toast.textContent = message;
    clearTimeout(toastTimeout);
    if (type !== 'info') {
        toastTimeout = setTimeout(() => { toast.style.opacity = '0'; }, 3000);
    }
}

function injectAutofillButtons() {
    injectMasterAutofillBar();
    // injectFieldButton('title',                   'title',               'title');
    injectFieldButton('short_description',       'short_description',   'short_description');
    injectFieldButton('meta_title',              'meta_title',          'meta_title');
    injectFieldButton('meta_description',        'meta_description',    'meta_description');
    injectFieldButton('primary_keywords',  'primary_keywords',  'primary_keywords');
    injectFieldButton('secondary_keywords','secondary_keywords', 'secondary_keywords');
    injectAutofillStyles();
}

function injectMasterAutofillBar() {
    if (document.getElementById('ai-autofill-master-bar')) return;

    const target = document.getElementById('finalReviewContainer');
    if (!target) return;

    const bar = document.createElement('div');
    bar.id = 'ai-autofill-master-bar';
    bar.innerHTML = `
        <div class="ai-master-bar-inner">
            <button id="ai-autofill-all-btn" class="ai-fill-all-btn" onclick="autofillAllFields()">
                </i> AI Autofill
            </button>
        </div>
    `;
    target.insertAdjacentElement('beforebegin', bar);
}

/* Inject a button next to a specific field */
function injectFieldButton(fieldId, labelFieldId, fieldType) {
    const el = document.getElementById(fieldId);
    if (!el || document.getElementById(`ai-btn-${fieldId}`)) return;

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.id = `ai-btn-${fieldId}`;
    btn.className = 'ai-field-btn';
    btn.title = `AI Autofill: ${fieldType.replace(/_/g, ' ')}`;
    btn.innerHTML = 'AI Autofill';
    btn.onclick = () => autofillField(fieldId, fieldType, btn);

    const wrapper = el.parentElement;
    if (!wrapper) return;

    el.insertAdjacentElement('afterend', btn);
}

// const _origAddArticle = window.addStep6ArticleSection;
// window.addStep6ArticleSection = function () {
//     if (typeof _origAddArticle === 'function') _origAddArticle();
//     setTimeout(() => {
//         const items = document.querySelectorAll('#articleSectionsContainer > div');
//         const idx = items.length - 1;
//         if (idx < 0) return;
//         const item = items[idx];
//         if (!item.querySelector('.ai-article-btn')) {
//             const btn = document.createElement('button');
//             btn.type = 'button';
//             btn.className = 'ai-field-btn ai-article-btn mt-2';
//             btn.innerHTML = 'AI Autofill Section';
//             btn.onclick = () => autofillArticleSection(idx, btn);
//             item.querySelector('.card-inner')?.appendChild(btn);
//         }
//     }, 50);
// };

// const _origAddFaq = window.addStep6FaqSection;
// window.addStep6FaqSection = function () {
//     if (typeof _origAddFaq === 'function') _origAddFaq();
//     setTimeout(() => {
//         const items = document.querySelectorAll('#faqSectionsContainer .faq-item');
//         const idx = items.length - 1;
//         if (idx < 0) return;
//         const item = items[idx];
//         if (!item.querySelector('.ai-faq-btn')) {
//             const btn = document.createElement('button');
//             btn.type = 'button';
//             btn.className = 'ai-field-btn ai-faq-btn mt-2';
//             btn.innerHTML = '<i></i> AI Autofill FAQ';
//             btn.onclick = () => autofillFaqItem(idx, btn);
//             item.querySelector('.card-inner')?.appendChild(btn);
//         }
//     }, 50);
// };

document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'addArticleSectionBtn' || 
        (e.target && e.target.closest && e.target.closest('[onclick*="addStep6ArticleSection"]'))) {
        setTimeout(() => {
            const items = document.querySelectorAll('#articleSectionsContainer > div');
            const idx = items.length - 1;
            if (idx < 0) return;
            const item = items[idx];
            if (!item.querySelector('.ai-article-btn')) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'ai-field-btn ai-article-btn mt-2';
                btn.innerHTML = 'AI Autofill Section';
                btn.onclick = () => autofillArticleSection(idx, btn);
                item.querySelector('.card-inner')?.appendChild(btn);
            }
        }, 100);
    }
    if (e.target && e.target.closest && e.target.closest('[onclick*="addStep6FaqSection"]')) {
        setTimeout(() => {
            const items = document.querySelectorAll('#faqSectionsContainer .faq-item');
            const idx = items.length - 1;
            if (idx < 0) return;
            const item = items[idx];
            if (!item.querySelector('.ai-faq-btn')) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'ai-field-btn ai-faq-btn mt-2';
                btn.innerHTML = 'AI Autofill FAQ';
                btn.onclick = () => autofillFaqItem(idx, btn);
                item.querySelector('.card-inner')?.appendChild(btn);
            }
        }, 100);
    }
});

function injectAutofillStyles() {
    if (document.getElementById('ai-autofill-styles')) return;
    const style = document.createElement('style');
    style.id = 'ai-autofill-styles';
    style.textContent = `
        #ai-autofill-master-bar {
            margin-bottom: 14px;
        }
        .ai-master-bar-inner {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .ai-master-label {
            font-size: 12px;
            font-weight: 700;
            color: #2c3782;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .ai-master-label .fa-magic {
            font-size: 11px;
        }
        .ai-master-hint {
            font-size: 11.5px;
            color: #888;
            flex: 1;
        }
        .ai-fill-all-btn {
            background: #2c3782;
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            transition: background 0.15s;
            flex-shrink: 0;
            margin-left: 85%;
        }
        .ai-fill-all-btn:hover:not(:disabled) {
            background:rgb(62, 73, 148);
        }
        .ai-fill-all-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .ai-fill-all-btn .fa-magic,
        .ai-fill-all-btn .fa-spinner {
            font-size: 10px;
        }

        .ai-field-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 5px;
            padding: 3px 10px;
            background: #fff;
            color: #2c3782;
            border: 1px solid #c7d2fe;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
            width: 36%;
        }
        .ai-field-btn:hover:not(:disabled) {
            background: #eef0ff;
            border-color: #2c3782;
        }
        .ai-field-btn:disabled,
        .ai-btn-loading {
            opacity: 0.55;
            cursor: not-allowed;
            size: 15px;
            padding: 8px;
        }
        .ai-field-btn .fa-magic,
        .ai-field-btn .fa-spinner { font-size: 9px; }

        @keyframes aiFillFlash {
            0%   { outline: 2px solid transparent; }
            25%  { outline: 2px solid #2c3782; }
            100% { outline: 2px solid transparent; }
        }
        .ai-filled-flash {
            animation: aiFillFlash 1s ease-out forwards;
            border-radius: 4px;
        }
    `;
    document.head.appendChild(style);
}

function resetAIClausesPanel() {
    standardClausesScanned = false;
    aiProcessedClauses     = [];
    allStandardClausesData = [];
 
    const panel = document.getElementById('aiClausesPanel');
    if (panel) panel.style.display = 'none';
    standardClausesPanelOpen = false;
 
    const btn     = document.getElementById('defineStandardClausesBtn');
    const chevron = document.getElementById('aiClausesChevron');
    if (btn)     btn.classList.remove('active');
    if (chevron) chevron.style.transform = 'rotate(0deg)';
 
    const scanningEl = document.getElementById('aiClausesScanningState');
    const readyEl    = document.getElementById('aiClausesReadyState');
    const emptyEl    = document.getElementById('aiClausesEmptyState');
    if (scanningEl) scanningEl.style.display = 'block';
    if (readyEl)    readyEl.style.display    = 'none';
    if (emptyEl)    emptyEl.style.display    = 'none';
 
    const fill = document.getElementById('aiClausesProgressFill');
    if (fill) fill.style.width = '0%';
}
 
document.addEventListener("DOMContentLoaded", function () {
    const contractInput = document.getElementById("contractName");
    const contractPreview = document.getElementById("contractNamePreview");
    if (contractInput && contractPreview) {
        contractPreview.value = contractInput.value || "Loan Agreement";
        contractInput.addEventListener("input", function () {
            contractPreview.value = this.value || "Loan Agreement";
            const subText = document.getElementById('aiScanSubText');
            if (subText) {
                subText.textContent = this.value.trim()
                    ? `Analysing clauses for "${this.value.trim()}"…`
                    : 'Matching standard clauses to your contract type';
            }
            if (standardClausesScanned) {
                resetAIClausesPanel();
            }
        });
    }
    loadPartiesTemplates();
});