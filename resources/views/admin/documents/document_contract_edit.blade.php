@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="container-fluid">

        @if(isset($document) && $document != null)
        <div class="col-md-12 doc-title mt-4 pb-4" id="docPageTitle">
            <h3>{{ $document->title ?? '' }}</h3>
        </div>
        @endif

        <div class="nk-block-head doc-outer-div">
            <div class="nk-block-head-content wrapper contractEditor-confiBtn">

                <div class="tab" id="mainTabBar">
                    @if(isset($document) && $document != null)
                        <a href="{{ route('admin.dashboard.edit_documents', ['slug' => $document->slug]) }}" target="_blank" class="btn tab_btn" id="tab-frontpage">Frontpage</a>
                    @else
                        <a href="{{ route('admin.dashboard.documents') }}" class="btn tab_btn" id="tab-frontpage">Document</a>
                    @endif

                    @if(isset($document) && $document != null)
                        <a href="{{ url('admin-dashboard/document-questions/?id=' . $document->id) }}" class="btn tab_btn" target="_blank" id="tab-questions">Document Questions</a>
                    @else
                        <a href="javascript:void(0);" class="btn tab_btn" id="tab-questions">Document Questions</a>
                    @endif

                    @if(isset($document) && $document != null)
                        <a href="{{ url('admin-dashboard/document-right-content/?id=' . $document->id) }}" class="btn tab_btn" target="_blank" id="tab-text">Document Text</a>
                    @else
                        <a href="javascript:void(0);" class="btn tab_btn" id="tab-text">Document Text</a>
                    @endif

                    @if(isset($document) && $document != null)
                        <a href="javascript:void(0);" class="btn tab_btn active" id="tab-contract">Contract Editor</a>
                    @else
                        <a href="javascript:void(0);" class="btn tab_btn active" style="opacity:.5;cursor:not-allowed;">Contract Editor</a>
                    @endif
                </div>

                    <div class="containSaveAndUpdate">

                    {{-- AI Edit Button --}}
                <button type="button" onclick="ceOpenAiEditModal()" id="ceAiEditBtn"
                    style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:6px;padding:8px 10px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#374151;transition:all .15s;"
                    onmouseover="this.style.background='#e85d2f';this.style.borderColor='#e85d2f';this.style.color='#fff'"
                    onmouseout="this.style.background='#f3f4f6';this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9"/>
                        <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
                        <path d="M15 5l3 3"/>
                    </svg>
                    Edit with AI
                </button>

                        <div class="ce-savebar">
                        <div style="position:relative;display:inline-block;">
                            <button type="button" id="ceConfigGearBtn" onclick="ceToggleConfigDropdown()"
                                title="Configuration"
                                style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:6px;padding:8px 10px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .15s;"
                                onmouseover="this.style.background='#e85d2f';this.style.borderColor='#e85d2f';this.querySelector('svg').setAttribute('stroke','#fff')"
                                onmouseout="this.style.background='#f3f4f6';this.style.borderColor='#e5e7eb';this.querySelector('svg').setAttribute('stroke','#6b7280')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                                </svg>
                            </button>

                            <div id="ceConfigDropdown" style="display:none;position:absolute;top:calc(100% + 6px);right:0;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 28px rgba(0,0,0,.12);width:340px;z-index:99999;padding:18px 18px 14px;">
                                <div style="font-size:13px;font-weight:700;color:#1a1a2e;margin-bottom:14px;display:flex;align-items:center;gap:6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#e85d2f" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                                    Configuration
                                </div>

                                <div style="padding-top:12px;border-top:1px solid #f3f4f6;margin-top:2px;">
                                    <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Parties Template</div>
                                    <select id="ceConfigPartiesTemplate" class="form-select form-select-sm" onchange="ceConfigChanged()">
                                        <option value="">— Select Template —</option>
                                        <option value="1-1">1 Party A — 1 Party B (1-1)</option>
                                        <option value="2-2">2 Party A — 2 Party B (2-2)</option>
                                        <option value="1-2">1 Party A — 2 Party B (1-2)</option>
                                        <option value="2-1">2 Party A — 1 Party B (2-1)</option>
                                    </select>
                                </div>

                                <div style="margin-bottom:14px;">
                                    <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Party Names</div>
                                    <div style="display:flex;gap:8px;">
                                        <div style="flex:1;">
                                            <label style="font-size:11px;color:#6b7280;display:block;margin-bottom:3px;">Party A</label>
                                            <input type="text" id="ceConfigPartyA" value="Party A" class="form-control form-control-sm" placeholder="e.g. Landlord" oninput="ceConfigChanged()">
                                        </div>
                                        <div style="flex:1;">
                                            <label style="font-size:11px;color:#6b7280;display:block;margin-bottom:3px;">Party B</label>
                                            <input type="text" id="ceConfigPartyB" value="Party B" class="form-control form-control-sm" placeholder="e.g. Tenant" oninput="ceConfigChanged()">
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-bottom:14px;padding-top:12px;border-top:1px solid #f3f4f6;">
                                    <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Standard Clause Grouping</div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                        <input type="checkbox" id="ceConfigNeverHide" onchange="ceToggleHideThreshold()" style="accent-color:#e85d2f;">
                                        <label for="ceConfigNeverHide" style="font-size:12px;color:#374151;cursor:pointer;margin:0;">Never hide / group Standard Clauses</label>
                                    </div>
                                    <div id="ceHideThresholdWrap" style="display:flex;align-items:center;gap:8px;">
                                        <label style="font-size:12px;color:#374151;white-space:nowrap;">Group clauses with more than</label>
                                        <select id="ceConfigHideThreshold" onchange="ceConfigChanged()" class="form-select form-select-sm" style="width:70px;">
                                            @for($i=1;$i<=100;$i++)
                                                <option value="{{ $i }}" {{ $i==3?'selected':'' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <label style="font-size:12px;color:#374151;">items</label>
                                    </div>
                                </div>

                                <div style="padding-top:12px;border-top:1px solid #f3f4f6;">
                                    <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Clause Numbering</div>
                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                        <div>
                                            <label style="font-size:11px;color:#6b7280;display:block;margin-bottom:3px;">Sections</label>
                                            <select id="ceConfigNumSections" class="form-select form-select-sm" onchange="ceConfigChanged()">
                                                <option value="1.">1., 2., 3.</option>
                                                <option value="I.">I., II., III.</option>
                                                <option value="A.">A., B., C.</option>
                                                <option value="none">None</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="font-size:11px;color:#6b7280;display:block;margin-bottom:3px;">Subsections</label>
                                            <select id="ceConfigNumSubsections" class="form-select form-select-sm" onchange="ceConfigChanged()">
                                                <option value="1.1">1.1, 1.2, 1.3</option>
                                                <option value="a.">a., b., c.</option>
                                                <option value="none">None</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="font-size:11px;color:#6b7280;display:block;margin-bottom:3px;">Clauses</label>
                                            <select id="ceConfigNumClauses" class="form-select form-select-sm" onchange="ceConfigChanged()">
                                                <option value="(a)">(a), (b), (c)</option>
                                                <option value="(1)">(1), (2), (3)</option>
                                                <option value="none">None</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="font-size:11px;color:#6b7280;display:block;margin-bottom:3px;">Sub-clauses</label>
                                            <select id="ceConfigNumSubclauses" class="form-select form-select-sm" onchange="ceConfigChanged()">
                                                <option value="(i)">(i), (ii), (iii)</option>
                                                <option value="(1)">(1), (2), (3)</option>
                                                <option value="none">None</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div style="display:flex;justify-content:flex-end;margin-top:14px;padding-top:10px;border-top:1px solid #f3f4f6;">
                                    <button type="button" onclick="ceSaveConfig()" class="ce-btn-save" style="padding:6px 16px;font-size:12px;">
                                        Apply
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="ceSaveAll()" id="ceSaveBtn" class="ce-btn-save">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            {{-- Save All --}}
                        </button>
                    </div>
                    <span id="ceAutoSaveStatus" style="font-size:12px;color:#6b7280;"></span>
            </div>
        </div>

        <div class="row main_section mt-4">
         @if(isset($document) && $document != null)
         <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
         <link rel="stylesheet" href="{{asset('assets/admin/css/document-contract-editor/document-contract-edit.css')}}">

                <div id="contractEditorPanel">

                    <div id="ceToast" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.45);z-index:999999;align-items:center;justify-content:center;">
                        <div style="background:#fff;border-radius:16px;padding:40px 36px 32px;width:340px;max-width:92vw;text-align:center;box-shadow:0 16px 48px rgba(0,0,0,0.18);animation:ceFadeIn .25s ease;">
                            <div id="ceToastIconWrap" style="width:72px;height:72px;border-radius:50%;border:3px solid #2dd4a7;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                                <svg id="ceToastIconSvg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#2dd4a7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                            <p id="ceToastTitle" style="font-size:22px;font-weight:700;color:#1a1a2e;margin:0 0 10px;">Success!</p>
                            <p id="ceToastMsg" style="font-size:14px;color:#6b7280;margin:0 0 26px;line-height:1.6;"></p>
                            <button type="button" onclick="ceCloseToast()"
                                id="ceToastOkBtn"
                                style="background:#2dd4a7;color:#fff;border:none;border-radius:8px;padding:11px 40px;font-size:15px;font-weight:600;cursor:pointer;transition:background .15s;">
                                OK
                            </button>
                        </div>
                    </div>

                    <div id="ceLoadingState" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 0;gap:14px;">
                        <div style="width:32px;height:32px;border:3px solid #e5e7eb;border-top-color:#e85d2f;border-radius:50%;animation:ceSpin 1s linear infinite;"></div>
                        <p style="color:#9ca3af;margin:0;font-size:13px;">Loading contract data&hellip;</p>
                    </div>

                    <div id="ceErrorState" style="display:none;flex-direction:column;align-items:center;justify-content:center;padding:80px 0;gap:12px;">
                        <div style="font-size:38px;">&#9888;</div>
                        <p id="ceErrorMsg" style="color:#dc2626;margin:0;font-size:13px;font-weight:500;"></p>
                        <button type="button" onclick="ceLoadData()" class="ce-btn">Retry</button>
                    </div>

                    <div id="ceEditorMain" style="display:none;">
                        <div class="ce-wrap">

                            <div class="ce-left">
                                <div class="ce-left-head">
                                    <div class="ce-panel-title">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e85d2f" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/>
                                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                                        </svg>
                                        Questionnaire
                                        <span id="ceQuestionCount" style="background:#e85d2f;color:#fff;border-radius:10px;padding:1px 8px;font-size:11px;margin-left:2px;">
                                            {{ $questions->count() }}
                                        </span>
                                    </div>

                                    <div style="display:flex;gap:8px;align-items:center;">
                                            <input type="checkbox" id="ceSelectAllCheckbox" title="Select all" onchange="ceToggleSelectAll(this)"
                                                style="width:16px;height:16px;accent-color:#e85d2f;cursor:pointer;flex-shrink:0;">
                                            <input type="text" id="ceSearchQuestions" placeholder="Search questions…" oninput="ceFilterQuestions(this.value)"
                                                class="form-control form-control-sm" style="flex:1;">
                                            <button type="button" onclick="ceAddNewQuestion()" class="ce-btn sm">+ Add</button>
                                        </div>
                                </div>
                                <div id="ceBulkBar">
                                        <span id="ceBulkCount">0 selected</span>
                                        <button type="button" class="ce-bulk-btn" onclick="ceBulkCopy()">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                            Copy
                                        </button>
                                        <button type="button" class="ce-bulk-btn" onclick="ceBulkDelete()">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                            Delete
                                        </button>
                                        <button type="button" class="ce-bulk-close" onclick="ceClearSelection()" title="Clear selection">&times;</button>
                                    </div>
                                <div id="ceQuestionsList" class="ce-qlist">
                                </div>
                            </div>

                            <div class="ce-right">
                                <div class="ce-right-head">
                                    <div class="ce-panel-title" style="margin:0;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e85d2f" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                        </svg>
                                        Contract Preview
                                        <span id="ceSectionCount" style="background:#e85d2f;color:#fff;border-radius:10px;padding:1px 8px;font-size:11px;margin-left:2px;">
                                            {{ $documentRight->count() }}
                                        </span>
                                    </div>
                              <div style="display:flex;gap:8px;align-items:center;width:100%;">
                                        <input type="checkbox" id="ceSelectAllSections" title="Select all sections" onchange="ceToggleSelectAllSections(this)"
                                            style="width:16px;height:16px;accent-color:#e85d2f;cursor:pointer;flex-shrink:0;">
                                        <input type="text" id="ceSearchSections" placeholder="Search sections…" oninput="ceFilterSectionsText(this.value)"
                                            class="form-control form-control-sm" style="flex:1;">
                                        <button type="button" onclick="ceAddNewSection()" class="ce-btn sm">+ Add</button>
                                    </div>
                                </div>
                                <div id="ceSBulkBar">
                                    <span id="ceSBulkCount">0 selected</span>
                                    <button type="button" class="ce-bulk-btn" onclick="ceBulkCopyS()">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                        Copy
                                    </button>
                                    <button type="button" class="ce-bulk-btn" onclick="ceBulkDeleteS()">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                        Delete
                                    </button>
                                    <button type="button" class="ce-bulk-close" onclick="ceClearSectionSelection()" title="Clear selection">&times;</button>
                                </div>
                                <div id="ceContractPreview" class="ce-preview"></div>         
                                </div>

                        </div>
                    </div>

                    <div id="ceQPreviewPopup" style="
                        display:none;
                        position:fixed;
                        z-index:999998;
                        background:#fff;
                        border:1px solid #e0e0e0;
                        border-radius:10px;
                        box-shadow:0 8px 28px rgba(0,0,0,.14);
                        padding:1px 18px 14px;
                        min-width:260px;
                        max-width:310px;
                        pointer-events:none;                     
                    ">
                        <div id="ceQPreviewTypeBadge" style="margin-bottom:8px;"></div>
                        <div id="ceQPreviewLabel" style="font-size:13px;font-weight:600;color:#1f2937;margin-bottom:11px;line-height:1.4;"></div>
                        <div id="ceQPreviewField"></div>
                        <div id="ceQPreviewInfo" style="display:none;margin-top:px;font-size:11px;color:#9ca3af;line-height:1.5;display:flex;align-items:flex-start;gap:5px;">
                            <svg width="11" height="11" style="flex-shrink:0;margin-top:1px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            <span id="ceQPreviewInfoText"></span>
                        </div>
                    </div>
                </div>

               
                <div id="ceQuestionModal" class="ce-modal">
                    <div class="ce-mbox" style="width:700px;">
                        <div class="ce-mhead">
                                <div style="display:flex;align-items:center;gap:10px;flex:1;">
                                    <span id="ceQModalQidBadge" style="display:none;font-family:monospace;font-size:12px;background:#fff4f0;color:#e85d2f;padding:3px 10px;border-radius:4px;"></span>
                                    <div class="inner-side-icon-wrap">
                                        <div class="edit-icn">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        </div>
                                        <div class="drop-dwn">
                                    <select id="ceQModalType" active onchange="ceQModalTypeChange()"
                                        style="font-size:14px;font-weight:600;appearance:none;color:#526484;background:transparent;border:0;padding:0 5px;cursor:pointer;outline:none;min-width:104px;"
                                        onfocus="this.style.borderColor='#e85d2f'" onblur="this.style.borderColor='#e5e7eb'">
                                        <option value="textbox">Text Box</option>
                                        <option value="textarea">Text Area</option>
                                        <option value="radio-button">Radio Button</option>
                                        <option value="dropdown">Dropdown</option>
                                        <option value="checkbox">Checkbox</option>
                                        <option value="date">Date</option>
                                        <option value="number">Number</option>
                                        <option value="dropdown-link">Dropdown Link</option>
                                    </select>
                                    </div>
                                    {{-- <i class="fa-solid fa-pen-to-square"></i> --}}                                    
                                    <div class="svg-wrp">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="pointer-events:none;color:#9ca3af;"><polyline points="6 9 12 15 18 9"/></svg>
                                        </div>
                                </div>
                                </div>
                            <button type="button" class="ce-mclose" onclick="closeCeQuestionModal()">&times;</button>
                        </div>

                        <div class="ce-mbody">
                            <input type="hidden" id="ceQModalId">
                            <div class="ce-fg">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
                                <label class="ce-flabel" style="margin-bottom:0;">QUESTION LABEL </label>
                            </div>

                            <div class="QtextAddButton" id="ceQModalLabelWrap">
                                <textarea id="ceQModalLabel" class="form-control" rows="1"
                                placeholder="e.g. What is the tenant's full name?">
                                </textarea>

                                <button type="button" onclick="ceAddConditionFromModal()" class="ce-svg-btn"
                                style="width:26px;height:26px;border:none;border-radius:50%;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:rgb(60, 77, 98);flex-shrink:0;transition:background .12s;"
                                title="Add condition row">
                            <i class="fa-solid fa-plus"></i>
                            </button>
                            </div>
                        </div>

                        <div id="ceQModalCondRows" style="margin-bottom:8px;"></div>
                                    <div id="ceQModalPlaceholderWrap" class="ce-fg" style="display:none;">
                                        <label class="ce-flabel" id="ceQModalPlaceholderLabel">TEXT BOX PLACEHOLDER</label>
                                        <input type="text" id="ceQModalPlaceholder" class="form-control"
                                            placeholder="e.g. Enter your full name…">
                                    </div>

                                    <div id="ceQModalDropdownLinkWrap" style="display:none;">
                                        <div class="ce-fg">
                                            <label class="ce-flabel">SAME CONTRACT LINK LABEL</label>
                                            <input type="text" id="ceQModalSameContractLink" class="form-control"
                                                placeholder="e.g. Same contract link label">
                                        </div>
                                        <div class="ce-fg">
                                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                                                <label class="ce-flabel" style="margin:0;">DIFFERENT CONTRACT LINK</label>
                                                <button type="button" onclick="ceAddDropdownLinkRow()"
                                                    style="width:28px;height:28px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:50%;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:#374151;"
                                                    onmouseover="this.style.background='#e85d2f';this.style.color='#fff';this.style.borderColor='#e85d2f'"
                                                    onmouseout="this.style.background='#f3f4f6';this.style.color='#374151';this.style.borderColor='#e5e7eb'">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                                </button>
                                            </div>
                                            <div id="ceQModalDropdownLinkRows"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 ce-fg goto_wid_inner">
                                            <label class="ce-flabel">GO TO</label>
                                            <select id="ceQModalGoTo" class="form-select">
                                                <option value="">— None (next) —</option>
                                            </select>
                                        </div>                                
                                    </div>

                            <div id="ceQModalOptionsWrap" style="display:none;" class="ce-fg">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                                    <label class="ce-flabel" style="margin:0;" id="ceQModalOptionsLabel">Add Radio Option</label>
                                 
                                </div>
                                <div id="ceQModalOptionsList" style="margin-bottom:8px;"></div>
                            </div>
                       
                         <div class="ce-fg" id="ceQModalCondGoToWrap">
                            {{-- <button type="button" onclick="ceAddQModalOption()"
                                        style="width:28px;height:28px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:50%;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:#374151;flex-shrink:0;" --}}
                                        <button type="button" id="ceQModalAddOptionBtn" onclick="ceAddQModalOption()"
                                        style="width:28px;height:28px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:50%;cursor:pointer;display:none;align-items:center;justify-content:center;color:#374151;flex-shrink:0;"
                                        onmouseover="this.style.background='#e85d2f';this.style.color='#fff';this.style.borderColor='#e85d2f'"
                                        onmouseout="this.style.background='#f3f4f6';this.style.color='#374151';this.style.borderColor='#e5e7eb'">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    </button>
                                    <div id="ceCondGroupsContainer"></div>
                                    <button type="button" class="btn btn-sm btn-primary add_btn20084 grey-btn " onclick="ceAddCondGroup()"
                                        style="background:#f3f4f6;border:1.5px dashed #d1d5db;border-radius:130px;width:100%;padding:9px;font-size:12px;font-weight:600;color:rgb(107, 114, 128);cursor:pointer;transition:all .12s;display:flex;align-items:center;justify-content:center;gap:6px;"
                                        onmouseover="this.style.borderColor='#e85d2f';this.style.color='#e85d2f'"
                                        onmouseout="this.style.borderColor='#d1d5db';this.style.color='#6b7280'">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        Condition
                                    </button>
                                </div>


                            <div class="ce-fg">
                                <label class="ce-flabel">HELP TEXT</label>
                                <textarea id="ceQModalInfo" class="form-control" rows="2"
                                          placeholder="Optional guidance shown to the user…"></textarea>
                            </div>
                        </div>
                         
                        <div class="ce-mfoot">
                            <button type="button" onclick="closeCeQuestionModal()" class="btn btn-sm btn-light"
                                    style="border:1px solid #d1d5db;">Cancel</button>
                            <button type="button" onclick="ceSaveQuestion()" class="ce-btn-save">Save Question</button>
                        </div>
                    </div>
                </div>

                <div id="ceSectionModal" class="ce-modal">
                    <div class="ce-mbox wide" style="width:680px;">
                        <div class="ce-mhead">
                    <div style="display:flex;align-items:center;gap:10px;flex:1;">
                        <p class="ce-mtitle" id="ceSModalTitle" style="  margin: 0; display: inline-block;font-family: monospace;font-size: 12px;background: rgb(255, 244, 240);padding: 3px 3px;border-radius: 4px; font-weight:500;"></p>
                        <div class="inner-side-icon-wrap">
                             <i class="fa-solid fa-pen-to-square"></i>
                                <select id="ceSModalType" onchange="ceSModalTypeChange()"
                                    style="font-size:14px;font-weight:600;appearance:none;color:#526484;background:transparent;border:0;padding:0 5px;cursor:pointer;outline:none;min-width:104px;"
                                    onfocus="this.style.borderColor='#e85d2f'" onblur="this.style.borderColor='#e5e7eb'">
                                    <option value="content_title">Title</option>
                                    <option value="content_heading">Headline</option>
                                    <option value="content">Content</option>
                                    <option value="signature_field">Signature</option>
                                    <option value="standard-clauses">Standard Clauses</option>
                                </select>
                                {{-- <i class="fa-solid fa-pen-to-square"></i> --}}                               
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="position:absolute;right:6px;top:50%;transform:translateY(-50%);pointer-events:none;color:#9ca3af;"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                    </div>
                                <button type="button" class="ce-mclose" onclick="closeCeSectionModal()">&times;</button>
                            </div>
                        <div class="ce-mbody">
                            <input type="hidden" id="ceSModalId">
                            <input type="hidden" id="ceSModalSectionKey" value="">

                            <div class="row">
                                <div class="col-md-4 ce-fg iner-ce-fg">
                                    <select id="ceSModalAlign" class="form-select" style="height:34px;">
                                        <option value="left">Left</option>
                                        <option value="center">Center</option>
                                        <option value="right">Right</option>
                                        <option value="justify">Justify</option>
                                    </select>
                                </div>
                                {{-- <div class="col-md-4 ce-fg blur-side-check">
                                     <input type="checkbox" id="ceSModalBlurSelect" class="form-check-input" value="1">
                                     <label class="ce-flabel">BLUR</label>
                                </div> --}}
                            </div>

                            {{-- <div class="ce-fg">
                                <label class="ce-flabel">TEXT</label>
                                <textarea id="ceSModalContent" rows="10" class="form-control"
                                          placeholder="Enter HTML content here. Example: &lt;p&gt;This agreement is made between &lt;strong&gt;{QID1}&lt;/strong&gt; and the landlord.&lt;/p&gt;"></textarea>
                            </div> --}}
                            <div class="ce-fg">
                                <label class="ce-flabel">TEXT</label>
                                <textarea id="ceSModalContent" rows="10" class="form-control"
                                        placeholder="Enter HTML content here. Example: &lt;p&gt;This agreement is made between &lt;strong&gt;{QID1}&lt;/strong&gt; and the landlord.&lt;/p&gt;"
                                        style="display:none;"></textarea>
                                <div id="ceSModalContentEditor"
                                    contenteditable="true"
                                    class="form-control ce-rich-editor"
                                    style="min-height:160px;max-height:340px;overflow-y:auto;white-space:pre-wrap;word-break:break-word;font-size:13px;line-height:1.6;font-family:inherit;padding:8px 12px;cursor:text;"
                                    data-placeholder="Enter HTML content here. Example: &lt;p&gt;This agreement is made between &lt;strong&gt;{QID1}&lt;/strong&gt; and the landlord.&lt;/p&gt;">
                                </div>
                            </div>
                        </div>
                        <div class="contract-blur-button">
                             <div class="col-md-4 ce-fg blur-side-check">
                                     <input type="checkbox" id="ceSModalBlurSelect" class="form-check-input" value="1">
                                     <label class="ce-flabel">BLUR</label>
                                </div>
                            </div>

                      
                       <div id="ceSModalCondWrap" style="display:none;" class="ce-fg">
                        <div id="ceSCondGroupsContainer"></div>
                        <div style="display:flex;justify-content:flex-end;">
                        <button type="button" id="ceAddSectionCondBtn" onclick="ceAddSectionCondGroup()" class="contract-condition-btn"
                            style="background:#f3f4f6;border:1.5px dashed #d1d5db;border-radius:130px;width:auto;padding:9px 20px;font-size:12px;font-weight:600;color:rgb(107, 114, 128);cursor:pointer;transition:all .12s;display:flex;align-items:center;justify-content:center;gap:6px;"
                            onmouseover="this.style.borderColor='#e85d2f';this.style.color='#e85d2f'"
                            onmouseout="this.style.borderColor='#d1d5db';this.style.color='#6b7280'">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Condition
                        </button>
                        </div> 
                    </div>
                        <div class="ce-mfoot">
                            <button type="button" onclick="closeCeSectionModal()" class="btn btn-sm btn-light"
                                    style="border:1px solid #d1d5db;">Cancel</button>
                            <button type="button" onclick="ceSaveSection()" class="ce-btn-save">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                                    <polyline points="17 21 17 13 7 13 7 21"/>
                                    <polyline points="7 3 7 8 15 8"/>
                                </svg>
                                Save Section
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Standard Clause Selection Modal --}}
                <div id="ceStdClauseModal" class="ce-modal">
                    <div class="ce-mbox" style="width:620px;">
                        <div class="ce-mhead">
                            <p class="ce-mtitle" style="margin:0;">Select Standard Clauses</p>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#6b7280;cursor:pointer;margin:0;white-space:nowrap;">
                                    <input type="checkbox" id="ceStdClauseSelectAll"
                                        onchange="ceToggleAllStdClauses(this)"
                                        style="width:14px;height:14px;accent-color:#e85d2f;cursor:pointer;">
                                    Select all
                                </label>
                                <button type="button" class="ce-mclose" onclick="ceCloseStdClauseModal()">&times;</button>
                            </div>
                        </div>
                        <div class="ce-mbody">
                            <input type="text" id="ceStdClauseSearch" class="form-control form-control-sm" placeholder="Search clauses…" oninput="ceFilterStdClauses(this.value)" style="margin-bottom:12px;">
                            <div id="ceStdClauseList" style="max-height:380px;overflow-y:auto;min-height:380px;">
                                <div style="text-align:center;color:#d1d5db;padding:40px;font-size:13px;">
                                    <div style="width:24px;height:24px;border:2px solid #e5e7eb;border-top-color:#e85d2f;border-radius:50%;animation:ceSpin 1s linear infinite;margin:0 auto 10px;"></div>
                                    Loading standard clauses…
                                </div>
                            </div>
                        </div>
                        <div class="ce-mfoot">
                            <button type="button" onclick="ceCloseStdClauseModal()" class="btn btn-sm btn-light" style="border:1px solid #d1d5db;">Cancel</button>
                            <button type="button" onclick="cePasteSelectedStdClause()" class="ce-btn-save">Paste Clause</button>
                        </div>
                    </div>
                </div>

                {{-- ── Standard Clause Sub-view --}}
                <div id="ceStdClauseSubView" style="position:fixed;">
                    <div class="ce-subview-head">
                        <button type="button" class="ce-subview-back" onclick="ceCloseStdClauseSubView()">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                            Back
                        </button>
                        <div style="flex:1;">
                            <p id="ceSubViewTitle" style="font-size:16px;font-weight:700;color:#1a1a2e;margin:0;"></p>
                            <p id="ceSubViewMeta" style="font-size:11px;color:#9ca3af;margin:0;"></p>
                        </div>
                        <button type="button" onclick="ceSubViewInsert()" class="ce-btn-save" style="padding:7px 18px;font-size:12px;">Insert Here</button>
                    </div>
                    <div class="ce-subview-body" id="ceSubViewBody"></div>
                </div>

                {{-- AI Edit Modal --}}
                <div id="ceAiEditModal" class="ce-modal">
                    <div class="ce-mbox" style="width:640px;">
                        <div class="ce-mhead">
                            <div style="display:flex;align-items:center;gap:10px;flex:1;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#e85d2f" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                <span style="font-size:14px;font-weight:700;color:#1a1a2e;">Edit Contract with AI</span>
                            </div>
                            <button type="button" class="ce-mclose" onclick="ceCloseAiEditModal()">&times;</button>
                        </div>
                        <div class="ce-mbody">
                            {{-- <div style="background:#fff4f0;border:1px solid #fdd0bb;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:12px;color:#7c3d1a;line-height:1.6;">
                                <strong>How it works:</strong> Describe the changes you want — AI will update the contract questions and text based on your instructions. Existing data will be preserved unless you ask to change it.
                            </div> --}}

                            <div class="ce-fg">
                                <label class="ce-flabel">SCOPE</label>
                                <div style="display:flex;gap:8px;margin-bottom:12px;">
                                    <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#374151;cursor:pointer;padding:7px 14px;border:1.5px solid #e5e7eb;border-radius:6px;flex:1;transition:all .12s;" id="ceAiScopeAllLabel">
                                        <input type="radio" name="ceAiScope" value="all" id="ceAiScopeAll" checked onchange="ceAiScopeChange()" style="accent-color:#e85d2f;">
                                        <span><strong>Entire contract</strong> — questions &amp; text</span>
                                    </label>
                                    <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#374151;cursor:pointer;padding:7px 14px;border:1.5px solid #e5e7eb;border-radius:6px;flex:1;transition:all .12s;" id="ceAiScopeQLabel">
                                        <input type="radio" name="ceAiScope" value="questions" id="ceAiScopeQ" onchange="ceAiScopeChange()" style="accent-color:#e85d2f;">
                                        <span><strong>Questions only</strong></span>
                                    </label>
                                    <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#374151;cursor:pointer;padding:7px 14px;border:1.5px solid #e5e7eb;border-radius:6px;flex:1;transition:all .12s;" id="ceAiScopeSLabel">
                                        <input type="radio" name="ceAiScope" value="sections" id="ceAiScopeS" onchange="ceAiScopeChange()" style="accent-color:#e85d2f;">
                                        <span><strong>Contract text only</strong></span>
                                    </label>
                                </div>
                            </div>

                            <div class="ce-fg">
                                <label class="ce-flabel">DESCRIBE YOUR CHANGES</label>
                                <textarea id="ceAiEditPrompt" class="form-control" rows="5"
                                    placeholder="e.g. Add a question asking for the tenant's monthly income. Update the payment clause to include a late fee of 5% after 7 days. Remove any mention of pets policy."></textarea>
                            </div>

                            <div id="ceAiEditProgress" style="display:none;margin-top:12px;">
                                <div style="display:flex;align-items:center;gap:10px;padding:12px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb;">
                                    <div style="width:18px;height:18px;border:2px solid #e5e7eb;border-top-color:#e85d2f;border-radius:50%;animation:ceSpin 1s linear infinite;flex-shrink:0;"></div>
                                    <span id="ceAiEditProgressText" style="font-size:12px;color:#6b7280;">Sending request to AI...</span>
                                </div>
                            </div>

                            <div id="ceAiEditResult" style="display:none;margin-top:12px;">
                                <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">AI Summary of Changes</div>
                                <div id="ceAiEditSummary" style="font-size:12px;color:#374151;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px;line-height:1.6;max-height:160px;overflow-y:auto;"></div>
                                <div style="margin-top:10px;display:flex;gap:8px;justify-content:flex-end;">
                                    <button type="button" onclick="ceAiEditReject()" class="btn btn-sm btn-light" style="border:1px solid #d1d5db;font-size:12px;">Discard changes</button>
                                    <button type="button" onclick="ceAiEditAccept()" class="ce-btn-save" style="font-size:12px;">Apply changes</button>
                                </div>
                            </div>
                        </div>

                        <div class="ce-mfoot" id="ceAiEditFooter">
                            <button type="button" onclick="ceCloseAiEditModal()" class="btn btn-sm btn-light" style="border:1px solid #d1d5db;">Cancel</button>
                            <button type="button" onclick="ceRunAiEdit()" id="ceAiEditRunBtn" class="ce-btn-save">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                Apply AI Edit
                            </button>
                        </div>
                    </div>
                </div>


<div id="ceQidAutocomplete" style="
    display:none;
    position:fixed;
    z-index:999999;
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:8px;
    box-shadow:0 4px 16px rgba(0,0,0,.10);
    width:380px;
    overflow:hidden;
    
">
    <div style="padding:8px 10px;border-bottom:1px solid #f3f4f6;background:#fafafa;">
        <input type="text" id="ceQidAutoSearch"
            placeholder="Search by ID or label…"
            style="width:100%;padding:5px 9px;border:1px solid #e5e7eb;border-radius:5px;font-size:12px;outline:none;box-sizing:border-box;color:#374151;background:#fff;font-family:inherit;"
            onfocus="this.style.borderColor='#9ca3af'" onblur="this.style.borderColor='#e5e7eb'"
            oninput="ceQidAutoFilter(this.value)">
    </div>
    <div id="ceQidAutoList" style="max-height:240px;overflow-y:auto;"></div>
</div>

             <script>
             (function () {
    'use strict';

    var CE = {
        documentId         : {{ $document->id }},
        csrfToken          : '{{ csrf_token() }}',
        questions          : [],
        sections           : [],
        sectionsFull       : [],
        deletedQuestionIds : [],
        deletedSectionIds  : [],
        editingQuestionIdx : null,
        editingSectionIdx  : null,
        _pasteTargetSectionIdx : null,
        _stdClauseInsertAfterIdx : null,
        _selectedStdClause     : null,
        _allStdClauses         : [],
        config : {
                partyA          : 'Party A',
                partyB          : 'Party B',
                neverHide       : false,
                hideThreshold   : 3,
                numSections     : '1.',
                numSubsections  : '1.1',
                numClauses      : '(a)',
                numSubclauses   : '(i)',
                partiesTemplate : '',
            },
    };
    window.__CE = CE;

    window.ceToggleConfigDropdown = function () {
        var d = document.getElementById('ceConfigDropdown');
        d.style.display = d.style.display === 'none' ? 'block' : 'none';
    };

    window.ceToggleHideThreshold = function () {
        var neverHide = document.getElementById('ceConfigNeverHide').checked;
        document.getElementById('ceHideThresholdWrap').style.display = neverHide ? 'none' : 'flex';
    };

    window.ceConfigChanged = function () {};

window.ceSaveConfig = function () {
    CE.config.partyA           = document.getElementById('ceConfigPartyA').value.trim()  || 'Party A';
    CE.config.partyB           = document.getElementById('ceConfigPartyB').value.trim()  || 'Party B';
    CE.config.neverHide        = document.getElementById('ceConfigNeverHide').checked;
    CE.config.hideThreshold    = parseInt(document.getElementById('ceConfigHideThreshold').value) || 3;
    CE.config.numSections      = document.getElementById('ceConfigNumSections').value;
    CE.config.numSubsections   = document.getElementById('ceConfigNumSubsections').value;
    CE.config.numClauses       = document.getElementById('ceConfigNumClauses').value;
    CE.config.numSubclauses    = document.getElementById('ceConfigNumSubclauses').value;
    CE.config.partiesTemplate  = document.getElementById('ceConfigPartiesTemplate').value;

    document.getElementById('ceConfigDropdown').style.display = 'none';
    ceRenderPreview();
    ceSetStatus('Configuration applied');
    setTimeout(function(){ ceSetStatus(''); }, 2000);
};

    CE._selectedIndices = new Set();

window.ceToggleSelect = function(checkbox, ri) {
    if (checkbox.checked) {
        CE._selectedIndices.add(ri);
        var card = document.getElementById('ce-qcard-' + ri);
        if (card) card.classList.add('ce-selected');
    } else {
        CE._selectedIndices.delete(ri);
        var card = document.getElementById('ce-qcard-' + ri);
        if (card) card.classList.remove('ce-selected');
    }
    ceUpdateBulkBar();
};

function ceUpdateBulkBar() {
    var bar = document.getElementById('ceBulkBar');
    var count = CE._selectedIndices.size;
    if (count > 0) {
        bar.classList.add('open');
        document.getElementById('ceBulkCount').textContent = count + ' selected';
    } else {
        bar.classList.remove('open');
    }
}

window.ceClearSelection = function() {
    CE._selectedIndices.clear();
    document.querySelectorAll('.ce-qcard-checkbox').forEach(function(cb) {
        cb.checked = false;
    });
    document.querySelectorAll('.ce-qcard.ce-selected').forEach(function(card) {
        card.classList.remove('ce-selected');
    });
    var master = document.getElementById('ceSelectAllCheckbox');
    if (master) master.checked = false;
    ceUpdateBulkBar();
};

window.ceBulkCopy = function() {
    if (!CE._selectedIndices.size) return;
    var indices = Array.from(CE._selectedIndices).sort(function(a,b){return a-b;});
    CE._bulkClipboard = indices.map(function(ri) {
        return JSON.parse(JSON.stringify(CE.questions[ri]));
    });
    ceSetStatus(indices.length + ' question(s) copied');
    setTimeout(function(){ ceSetStatus(''); }, 2000);
    ceClearSelection();
};

window.ceBulkDelete = function() {
    if (!CE._selectedIndices.size) return;
    var count = CE._selectedIndices.size;
    if (!confirm('Delete ' + count + ' selected question(s)?')) return;
    var indices = Array.from(CE._selectedIndices).sort(function(a,b){return b-a;}); // descending
    indices.forEach(function(ri) {
        var q = CE.questions[ri];
        if (q && !q.isNew) CE.deletedQuestionIds.push(q.id);
        CE.questions.splice(ri, 1);
    });
    CE._selectedIndices.clear();
    ceUpdateBulkBar();
    ceRenderQ();
    ceRenderPreview();
    ceSetStatus('Unsaved changes');
};

CE._selectedSectionIndices = new Set();

window.ceToggleSectionSelect = function(checkbox, idx) {
    if (checkbox.checked) {
        CE._selectedSectionIndices.add(idx);
        var block = document.getElementById('ce-sblock-' + idx);
        if (block) block.classList.add('ce-selected');
    } else {
        CE._selectedSectionIndices.delete(idx);
        var block = document.getElementById('ce-sblock-' + idx);
        if (block) block.classList.remove('ce-selected');
    }
    ceUpdateSBulkBar();
};

function ceUpdateSBulkBar() {
    var bar = document.getElementById('ceSBulkBar');
    var count = CE._selectedSectionIndices.size;
    if (count > 0) {
        bar.classList.add('open');
        document.getElementById('ceSBulkCount').textContent = count + ' selected';
    } else {
        bar.classList.remove('open');
    }
}

window.ceClearSectionSelection = function() {
    CE._selectedSectionIndices.clear();
    document.querySelectorAll('.ce-sblock-checkbox').forEach(function(cb) {
        cb.checked = false;
    });
    document.querySelectorAll('.ce-sblock.ce-selected').forEach(function(block) {
        block.classList.remove('ce-selected');
    });
    var master = document.getElementById('ceSelectAllSections');
    if (master) master.checked = false;
    ceUpdateSBulkBar();
};

window.ceToggleSelectAllSections = function(masterCb) {
    var checkboxes = document.querySelectorAll('.ce-sblock-checkbox');
    CE._selectedSectionIndices.clear();
    checkboxes.forEach(function(cb) {
        cb.checked = masterCb.checked;
        var idx = parseInt(cb.getAttribute('data-idx'));
        var block = document.getElementById('ce-sblock-' + idx);
        if (masterCb.checked) {
            CE._selectedSectionIndices.add(idx);
            if (block) block.classList.add('ce-selected');
        } else {
            if (block) block.classList.remove('ce-selected');
        }
    });
    ceUpdateSBulkBar();
};

window.ceBulkCopyS = function() {
    if (!CE._selectedSectionIndices.size) return;
    var indices = Array.from(CE._selectedSectionIndices).sort(function(a,b){return a-b;});
    CE._bulkSClipboard = indices.map(function(idx) {
        return JSON.parse(JSON.stringify(CE.sections[idx]));
    });
    ceSetStatus(indices.length + ' section(s) copied');
    setTimeout(function(){ ceSetStatus(''); }, 2000);
    ceClearSectionSelection();
};

window.ceBulkDeleteS = function() {
    if (!CE._selectedSectionIndices.size) return;
    var count = CE._selectedSectionIndices.size;
    if (!confirm('Delete ' + count + ' selected section(s)?')) return;
    var indices = Array.from(CE._selectedSectionIndices).sort(function(a,b){return b-a;});
    indices.forEach(function(idx) {
        var s = CE.sections[idx];
        if (s && !s.isNew) CE.deletedSectionIds.push(s.id);
        CE.sections.splice(idx, 1);
    });
    CE.sectionsFull = CE.sections.slice();
    CE._selectedSectionIndices.clear();
    ceUpdateSBulkBar();
    ceRenderPreview();
    ceSetStatus('Unsaved changes');
};

    document.addEventListener('click', function (e) {
        var dropdown = document.getElementById('ceConfigDropdown');
        var gearBtn  = document.getElementById('ceConfigGearBtn');
        if (dropdown && !dropdown.contains(e.target) && !gearBtn.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    /*  PASTE DROPDOWN */
    window.ceShowPasteDropdown = function (btn, sectionIdx) {
        CE._pasteTargetSectionIdx = sectionIdx;

        var drop = document.getElementById('cePasteDropdown');
        var rect = btn.getBoundingClientRect();

        var top  = rect.bottom + 4;
        var left = rect.left;

        var dropW = 210;
        if (left + dropW > window.innerWidth - 8) {
            left = rect.right - dropW;
        }
        var dropH = 84;
        if (top + dropH > window.innerHeight - 8) {
            top = rect.top - dropH - 4;
        }

        drop.style.top  = top  + 'px';
        drop.style.left = left + 'px';
        drop.classList.add('open');

        setTimeout(function () {
            document.addEventListener('click', cePasteDropdownOutside, { once: true });
        }, 10);
    };

    function cePasteDropdownOutside (e) {
        var drop = document.getElementById('cePasteDropdown');
        if (drop && !drop.contains(e.target)) {
            drop.classList.remove('open');
        }
    }

    window.ceDoPasteFromClipboard = function () {
        document.getElementById('cePasteDropdown').classList.remove('open');
        var idx = CE._pasteTargetSectionIdx;
        if (idx === null || idx === undefined) return;
        cePasteS(idx);
    };

window.ceOpenStdClauseModal = function () {
    document.getElementById('cePasteDropdown').classList.remove('open');

    if (CE._stdClauseInsertAfterIdx === null || CE._stdClauseInsertAfterIdx === undefined) {
        CE._stdClauseInsertAfterIdx = CE.sections.length - 1;
    }

    CE._allStdClauses = [];
    CE._stdClausePage = 1;
    CE._stdClauseTotalPages = 1;
    CE._stdClauseTotalCount = 0;
    CE._stdClausePerPage = 20;

    document.getElementById('ceStdClauseList').innerHTML =
        '<div style="text-align:center;color:#d1d5db;padding:40px;font-size:13px;">'
        + '<div style="width:24px;height:24px;border:2px solid #e5e7eb;border-top-color:#e85d2f;border-radius:50%;animation:ceSpin 1s linear infinite;margin:0 auto 10px;"></div>'
        + 'Loading standard clauses…'
        + '</div>';

    ceLoadStdClausesPage(1);
    document.getElementById('ceStdClauseModal').classList.add('open');
};

window.ceRenderStdClausePagination = function () {
    var existing = document.getElementById('ceStdClausePagination');
    if (existing) existing.remove();

    var total = CE._stdClauseTotalPages || 1;
    var current = CE._stdClausePage || 1;

    var container = document.createElement('div');
    container.id = 'ceStdClausePagination';
    container.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:10px 2px 2px;border-top:1px solid #f3f4f6;margin-top:8px;flex-wrap:wrap;gap:6px;';

    var info = document.createElement('span');
    info.style.cssText = 'font-size:11px;color:#9ca3af;';
    info.textContent = 'Page ' + current + ' of ' + total + ' (' + (CE._stdClauseTotalCount || 0) + ' clauses)';

    var btns = document.createElement('div');
    btns.style.cssText = 'display:flex;gap:4px;align-items:center;';

    var btnStyle = 'padding:4px 10px;font-size:12px;border-radius:5px;border:1px solid #e5e7eb;cursor:pointer;background:#fff;color:#374151;transition:all .12s;';
    var btnActiveStyle = 'padding:4px 10px;font-size:12px;border-radius:5px;border:1px solid #e85d2f;cursor:default;background:#e85d2f;color:#fff;font-weight:700;';
    var btnDisabledStyle = 'padding:4px 10px;font-size:12px;border-radius:5px;border:none;cursor:not-allowed;background:transparent;color:#d1d5db;';
    var ellipsisStyle = 'padding:4px 6px;font-size:12px;color:#9ca3af;background:transparent;border:none;cursor:default;';

    function makePageBtn(pageNum) {
        var pb = document.createElement('button');
        pb.type = 'button';
        pb.textContent = pageNum;
        if (pageNum === current) {
            pb.style.cssText = btnActiveStyle;
            pb.disabled = true;
        } else {
            pb.style.cssText = btnStyle;
            pb.onmouseover = function(){ this.style.background='#f3f4f6'; };
            pb.onmouseout  = function(){ this.style.background='#fff'; };
            pb.onclick = (function(p){ return function(){ ceLoadStdClausesPage(p); }; })(pageNum);
        }
        return pb;
    }

    function makeEllipsis() {
        var span = document.createElement('span');
        span.style.cssText = ellipsisStyle;
        span.textContent = '…';
        return span;
    }

    var pagesToShow = [];

    if (total <= 5) {
        for (var i = 1; i <= total; i++) pagesToShow.push(i);
    } else {
        pagesToShow.push(1);

        if (current > 3) {
            pagesToShow.push('...');
        }

        var rangeStart = Math.max(2, current - 1);
        var rangeEnd   = Math.min(total - 1, current + 1);

        for (var p = rangeStart; p <= rangeEnd; p++) {
            pagesToShow.push(p);
        }

        if (current < total - 2) {
            pagesToShow.push('...');
        }

        if (total > 1) pagesToShow.push(total);
    }

    pagesToShow.forEach(function(item) {
        if (item === '...') {
            btns.appendChild(makeEllipsis());
        } else {
            btns.appendChild(makePageBtn(item));
        }
    });

    var next = document.createElement('button');
    next.type = 'button';
    next.innerHTML = '&raquo;';
    if (current >= total) {
        next.style.cssText = btnDisabledStyle;
        next.disabled = true;
    } else {
        next.style.cssText = btnStyle;
        next.onmouseover = function(){ this.style.background='#f3f4f6'; };
        next.onmouseout  = function(){ this.style.background='#fff'; };
        next.onclick = function(){ ceLoadStdClausesPage(current + 1); };
    }
    btns.appendChild(next);

    container.appendChild(info);
    container.appendChild(btns);

    var listEl = document.getElementById('ceStdClauseList');
    if (listEl && listEl.parentNode) {
        listEl.parentNode.insertBefore(container, listEl.nextSibling);
    }
};

window.ceLoadStdClausesPage = function (page) {
    CE._stdClausePage = page;
    var search = document.getElementById('ceStdClauseSearch') ? document.getElementById('ceStdClauseSearch').value.trim() : '';

    document.getElementById('ceStdClauseList').innerHTML =
        '<div style="text-align:center;color:#d1d5db;padding:40px;font-size:13px;">'
        + '<div style="width:24px;height:24px;border:2px solid #e5e7eb;border-top-color:#e85d2f;border-radius:50%;animation:ceSpin 1s linear infinite;margin:0 auto 10px;"></div>'
        + 'Loading page ' + page + '…'
        + '</div>';

    var url = '/admin-dashboard/api/standard-documents?page=' + page + '&per_page=' + CE._stdClausePerPage;
    if (search) url += '&search=' + encodeURIComponent(search);

    fetch(url, {
        headers: { 
            'X-Requested-With': 'XMLHttpRequest', 
            'Accept': 'application/json' 
            }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        CE._allStdClauses      = data.documents || [];
        CE._stdClauseTotalPages = data.total_pages || 1;
        CE._stdClauseTotalCount = data.total || 0;
        CE._stdClausePerPage    = data.per_page || 10;
        ceRenderStdClauseList(CE._allStdClauses);
        ceRenderStdClausePagination();
    })
    .catch(function() {
        document.getElementById('ceStdClauseList').innerHTML =
            '<div style="text-align:center;color:#dc2626;padding:30px;font-size:13px;">Failed to load standard clauses.</div>';
    });
};

window.ceFilterStdClauses = function (v) {
    if (window._ceStdClauseSearchTimer) clearTimeout(window._ceStdClauseSearchTimer);
    window._ceStdClauseSearchTimer = setTimeout(function() {
        ceLoadStdClausesPage(1);
    }, 350);
};

window.ceToggleAllStdClauses = function(masterCb) {
    document.querySelectorAll('#ceStdClauseList .ce-std-clause-cb').forEach(function(cb) {
        cb.checked = masterCb.checked;
        var card = cb.closest('.ce-clause-card');
        if (card) {
            if (masterCb.checked) {
                card.classList.add('selected');
                card.style.background  = '#fff4f0';
                card.style.borderColor = '#e85d2f';
            } else {
                card.classList.remove('selected');
                card.style.background  = '#fff';
                card.style.borderColor = '#e5e7eb';
            }
        }
    });
};

   window.ceCloseStdClauseModal = function() {
    document.getElementById('ceStdClauseModal').classList.remove('open');
    CE._selectedStdClause = null;
    document.querySelectorAll('#ceStdClauseList .ce-std-clause-cb').forEach(function(cb) {
        cb.checked = false;
        var card = cb.closest('.ce-clause-card');
        if (card) {
            card.classList.remove('selected');
            card.style.background   = '#fff';
            card.style.borderColor  = '#e5e7eb';
        }
    });
};

   function ceRenderStdClauseList(clauses) {
    var list = document.getElementById('ceStdClauseList');
    if (!clauses.length) {
        list.innerHTML = '<div style="text-align:center;color:#d1d5db;padding:30px;font-size:13px;">No standard clauses available.</div>';
        return;
    }
    list.innerHTML = clauses.map(function(c) {
        var qCount = c.questions_count || 0;
        var sCount = c.sections_count  || 0;
        var typeBadge = c.type
            ? '<span style="font-size:10px;font-weight:700;text-transform:uppercase;background:#f3f4f6;color:#6b7280;border-radius:4px;padding:1px 6px;margin-left:6px;">' + esc(c.type) + '</span>'
            : '';
        return '<div class="ce-clause-card" data-id="' + c.id + '"'
            + ' onclick="ceToggleStdClauseCheckbox(' + c.id + ', this)"'
            + ' style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:6px;transition:background .12s,border-color .12s;"'
            + ' onmouseover="if(!this.classList.contains(\'selected\'))this.style.background=\'#fafafa\'"'
            + ' onmouseout="if(!this.classList.contains(\'selected\'))this.style.background=\'#fff\'">'
            + '<input type="checkbox" class="ce-std-clause-cb" data-id="' + c.id + '"'
            + ' onclick="event.stopPropagation();ceToggleStdClauseCheckbox(' + c.id + ', this.closest(\'.ce-clause-card\'))"'
            + ' style="margin-top:2px;width:15px;height:15px;accent-color:#e85d2f;flex-shrink:0;cursor:pointer;">'
            + '<div style="flex:1;min-width:0;">'
            + '<div style="font-size:13px;font-weight:600;color:#1f2937;">' + esc(c.title || c.name || '') + typeBadge + '</div>'
            + '<div style="font-size:11px;color:#9ca3af;margin-top:3px;">'
            + qCount + ' question(s) &middot; ' + sCount + ' section(s)'
            + (qCount + sCount > 3 ? ' &middot; <span style="color:#e85d2f;font-weight:600;">Will be grouped</span>' : '')
            + ' &nbsp;<button type="button" onclick="event.stopPropagation();ceOpenStdClauseSubView(' + c.id + ')"'
            + ' style="background:none;border:none;cursor:pointer;color:#6b7280;font-size:10px;text-decoration:underline;padding:0;">Preview</button>'
            + '</div>'
            + '</div>'
            + '</div>';
    }).join('');
}

window.ceToggleStdClauseCheckbox = function(id, cardEl) {
    var cb = cardEl.querySelector('.ce-std-clause-cb');
    if (!cb) return;
    cb.checked = !cb.checked;
    if (cb.checked) {
        cardEl.classList.add('selected');
        cardEl.style.background = '#fff4f0';
        cardEl.style.borderColor = '#e85d2f';
    } else {
        cardEl.classList.remove('selected');
        cardEl.style.background = '#fff';
        cardEl.style.borderColor = '#e5e7eb';
    }
};

    window.ceFilterStdClauses = function (v) {
        var lc = v.toLowerCase();
        var filtered = CE._allStdClauses.filter(function (c) {
            return (c.title || c.name || '').toLowerCase().includes(lc);
        });
        ceRenderStdClauseList(filtered);
    };

    window.ceSelectStdClause = function (id, el) {
        document.querySelectorAll('#ceStdClauseList .ce-clause-card').forEach(function (card) {
            card.classList.remove('selected');
        });
        if (el) el.classList.add('selected');
        // CE._selectedStdClause = CE._allStdClauses.find(function (c) { return c.id === id; }) || { id: id };
        CE._selectedStdClause = CE._allStdClauses.find(function (c) { return c.id === id; }) || { id: id, title: '', name: '' };

    };

    /* full-screen preview of a standard clause */
    window.ceOpenStdClauseSubView = function (id) {
        var clause = CE._allStdClauses.find(function (c) { return c.id === id; });
        if (!clause) return;

        document.getElementById('ceSubViewTitle').textContent = clause.title || clause.name || '';
        document.getElementById('ceSubViewMeta').textContent  =
            (clause.questions_count || 0) + ' question(s) · ' + (clause.sections_count || 0) + ' section(s)';

        var body = document.getElementById('ceSubViewBody');
        body.innerHTML = '<div style="text-align:center;padding:40px;"><div style="width:24px;height:24px;border:2px solid #e5e7eb;border-top-color:#e85d2f;border-radius:50%;animation:ceSpin 1s linear infinite;margin:0 auto;"></div></div>';

        fetch('/admin-dashboard/api/standard-document-detail/' + id, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var sections  = data.sections  || [];
            var questions = data.questions || [];
            var html = '';
            if (sections.length) {
                html += '<h4 style="font-size:14px;font-weight:700;color:#1a1a2e;margin-bottom:12px;">Contract Text</h4>';
                sections.forEach(function (s) {
                    html += '<div style="background:#fff;border:1px solid #ebebeb;border-radius:6px;padding:10px 14px;margin-bottom:8px;">'
                        + '<span style="font-size:10px;font-weight:700;color:#e85d2f;font-family:monospace;background:#fff4f0;padding:2px 6px;border-radius:3px;margin-right:6px;">' + esc(s.type || '') + '</span>'
                        + '<div style="font-size:12px;color:#374151;margin-top:6px;">' + (s.content || '<em style="color:#d1d5db;">Empty</em>') + '</div>'
                        + '</div>';
                });
            }
            if (questions.length) {
                html += '<h4 style="font-size:14px;font-weight:700;color:#1a1a2e;margin:16px 0 12px;">Questions</h4>';
                questions.forEach(function (q) {
                    html += '<div style="background:#fff;border:1px solid #ebebeb;border-radius:6px;padding:10px 14px;margin-bottom:8px;">'
                        + '<span style="font-size:10px;font-weight:700;color:#e85d2f;font-family:monospace;">Q' + q.id + '</span>'
                        + '<div style="font-size:12px;color:#374151;margin-top:4px;">' + esc(q.label || '') + '</div>'
                        + '</div>';
                });
            }
            if (!html) html = '<div style="text-align:center;color:#d1d5db;padding:40px;">No content available.</div>';
            body.innerHTML = html;
        })
        .catch(function () {
            body.innerHTML = '<div style="text-align:center;color:#dc2626;padding:30px;">Failed to load details.</div>';
        });

        CE._subViewClauseId = id;
        document.getElementById('ceStdClauseSubView').classList.add('open');
    };

    window.ceCloseStdClauseSubView = function () {
        document.getElementById('ceStdClauseSubView').classList.remove('open');
    };

    window.ceSubViewInsert = function () {
        var id = CE._subViewClauseId;
        CE._selectedStdClause = CE._allStdClauses.find(function (c) { return c.id === id; }) || { id: id, title: '', name: '' };
        ceCloseStdClauseSubView();
        cePasteSelectedStdClause();
    };

    /* Paste Selected Standard Clause */
   window.cePasteSelectedStdClause = function() {
    var checkedIds = [];
    document.querySelectorAll('#ceStdClauseList .ce-std-clause-cb:checked').forEach(function(cb) {
        checkedIds.push(parseInt(cb.getAttribute('data-id')));
    });

    if (!checkedIds.length) {
        alert('Please select at least one standard clause.');
        return;
    }

    ceCloseStdClauseModal();

    var insertAfter      = CE._stdClauseInsertAfterIdx !== null ? CE._stdClauseInsertAfterIdx : CE.sections.length - 1;
    var currentInsertPos = insertAfter;

    var fetchNext = function(i) {
        if (i >= checkedIds.length) {
            CE.sectionsFull = CE.sections.slice();
            ceRenderQ();
            ceRenderPreview();
            ceSetStatus(checkedIds.length + ' standard clause(s) inserted');
            setTimeout(function() { ceSetStatus(''); }, 2500);
            return;
        }

        var clauseId   = checkedIds[i];
        var clauseMeta = CE._allStdClauses.find(function(c) { return c.id === clauseId; }) || { id: clauseId, title: '', name: '' };

        fetch('/admin-dashboard/api/standard-document-detail/' + clauseId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var clauseSections  = data.sections  || [];
            var clauseQuestions = data.questions || [];
            var clauseTitle     = clauseMeta.title || clauseMeta.name || (data.clause && data.clause.title) || 'Standard Clause';
            var totalItems      = clauseSections.length + clauseQuestions.length;
            var shouldGroup     = !CE.config.neverHide && totalItems > CE.config.hideThreshold;

            if (shouldGroup) {
                CE.sections.splice(currentInsertPos + 1, 0, {
                    id                  : 'new_' + Date.now() + '_g' + i,
                    type                : 'content',
                    content             : '<!-- STD_CLAUSE_GROUP:' + clauseId + ':' + esc(clauseTitle) + ' -->',
                    section_key         : 'std_clause_' + clauseId,
                    section_name        : clauseTitle,
                    text_align          : 'left',
                    secure_blur_content : 0,
                    isNew               : true,
                    isStdClauseGroup    : true,
                    stdClauseId         : clauseId,
                    stdClauseTitle      : clauseTitle,   // ← ensure title stored here
                    stdClauseSections   : clauseSections,
                    stdClauseQuestions  : clauseQuestions,
                    stdClauseTotalItems : totalItems,
                });
                currentInsertPos += 1;
            } else {
                clauseSections.forEach(function(s, si) {
                    CE.sections.splice(currentInsertPos + 1 + si, 0, {
                        id                  : 'new_' + Date.now() + '_s' + i + '_' + si,
                        type                : s.type || 'content',
                        content             : s.content || '',
                        section_key         : s.section_key || '',
                        section_name        : clauseTitle,
                        text_align          : s.text_align || 'left',
                        secure_blur_content : s.secure_blur_content || 0,
                        isNew               : true,
                    });
                });
                currentInsertPos += clauseSections.length || 1;
            }

            if (clauseQuestions.length > 0) {
                // find the nearest single-path question ABOVE current questions
                var connectBeforeIdx = _findSinglePathQuestion(CE.questions.length - 1);
                clauseQuestions.forEach(function(cq, qi) {
                    CE.questions.splice(connectBeforeIdx + 1 + qi, 0, {
                        id          : 'new_' + Date.now() + '_q' + i + '_' + qi,
                        type        : cq.type || 'textbox',
                        label       : cq.label || '',
                        info        : cq.info  || '',
                        placeholder : cq.placeholder || '',
                        required    : 1,
                        section     : clauseTitle,
                        goTo        : null,
                        usedIn      : 0,
                        options     : cq.options || [],
                        conditions  : [],
                        condGoTo    : [],
                        isNew       : true,
                    });
                });
            }

            fetchNext(i + 1);
        })
        .catch(function(err) {
            ceShowToast('Failed to load "' + esc(clauseMeta.title || String(clauseId)) + '": ' + err.message, true);
            fetchNext(i + 1);
        });
    };

    fetchNext(0);
};

   function _findSinglePathQuestion(maxIdx) {
    for (var i = Math.min(maxIdx, CE.questions.length - 1); i >= 0; i--) {
        var q = CE.questions[i];
        if (!q) continue;
        var hasCondGoTo = q.condGoTo && q.condGoTo.length > 0;
        var hasGoTo = q.goTo && q.goTo !== null;
        if (!hasCondGoTo) {
            if (!hasGoTo) return i;
            var nextQ = CE.questions[i + 1];
            if (nextQ && String(nextQ.id) === String(q.goTo)) return i;
        }
    }
    return CE.questions.length - 1;
}

    var _romanNumerals = ['i','ii','iii','iv','v','vi','vii','viii','ix','x',
        'xi','xii','xiii','xiv','xv','xvi','xvii','xviii','xix','xx'];

    function _ceGetNumber (style, n) {
        if (style === 'none') return '';
        n = n || 1;
        if (style === '1.')     return n + '.';
        if (style === '1.1')    return n + '.';  
        if (style === 'I.')     { var r = _romanNumerals[n-1]; return r ? r.toUpperCase() + '.' : n + '.'; }
        if (style === 'A.')     return String.fromCharCode(64 + n) + '.';
        if (style === '(a)')    return '(' + String.fromCharCode(96 + n) + ')';
        if (style === '(1)')    return '(' + n + ')';
        if (style === 'a.')     return String.fromCharCode(96 + n) + '.';
        if (style === '(i)')    { var rv = _romanNumerals[n-1]; return '(' + (rv || n) + ')'; }
        return '';
    }

    function setDisplay(id, v) {
        var el = document.getElementById(id);
        if (el) el.style.display = v;
    }

    function ceLoading() {
        setDisplay('ceLoadingState', 'flex');
        setDisplay('ceEditorMain',   'none');
        setDisplay('ceErrorState',   'none');
    }
    function ceEditor() {
        setDisplay('ceLoadingState', 'none');
        setDisplay('ceEditorMain',   'block');
        setDisplay('ceErrorState',   'none');
    }
    function ceError(msg) {
        setDisplay('ceLoadingState', 'none');
        setDisplay('ceEditorMain',   'none');
        setDisplay('ceErrorState',   'flex');
        document.getElementById('ceErrorMsg').textContent = msg;
    }

    function esc(s) {
        return String(s || '')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;')
            .replace(/'/g,'&#39;');
    }

    function ceSetStatus(m) {
        document.getElementById('ceAutoSaveStatus').textContent = m;
    }

    window.ceCloseToast = function () {
        var toast = document.getElementById('ceToast');
        if (toast) toast.style.display = 'none';
    };

    window.ceShowToast = function (msg, isError) {
        var toast    = document.getElementById('ceToast');
        var iconWrap = document.getElementById('ceToastIconWrap');
        var iconSvg  = document.getElementById('ceToastIconSvg');
        var title    = document.getElementById('ceToastTitle');
        var okBtn    = document.getElementById('ceToastOkBtn');

        document.getElementById('ceToastMsg').textContent = msg;

        if (isError) {
            iconWrap.style.borderColor  = '#ef4444';
            iconSvg.setAttribute('stroke', '#ef4444');
            iconSvg.innerHTML           = '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>';
            title.textContent           = 'Error!';
            title.style.color           = '#ef4444';
            okBtn.style.background      = '#ef4444';
        } else {
            iconWrap.style.borderColor  = '#2dd4a7';
            iconSvg.setAttribute('stroke', '#2dd4a7');
            iconSvg.innerHTML           = '<polyline points="20 6 9 17 4 12"/>';
            title.textContent           = 'Success!';
            title.style.color           = '#1a1a2e';
            okBtn.style.background      = '#2dd4a7';
        }

        toast.style.display = 'flex';
    };

    window.ceLoadData = function () {
    ceLoading();
    Promise.all([
        fetch('/admin-dashboard/api/ce-questions/' + CE.documentId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(r => r.json()),
        fetch('/admin-dashboard/api/ce-sections/' + CE.documentId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(r => r.json()),
    ]).then(function (res) {
        console.log('Questions raw:', JSON.stringify(res[0].questions ? res[0].questions.slice(0,2) : 'EMPTY'));
    console.log('Questions count:', res[0].questions ? res[0].questions.length : 0);
        if (!res[0].success || !res[1].success) {
            ceError('Server error loading data. Check API routes.');
            return;
        }

        CE.questions = (res[0].questions || []).map(function (q) {
            return {
                id          : q.id,
                type        : q.type || 'textbox',
                label       : (q.questionData && q.questionData.question_label)       || '',
                info        : (q.questionData && q.questionData.question_info_text)   || '',
                placeholder : (q.questionData && q.questionData.text_box_placeholder) || '',
                required    : q.required || 0,
                section     : q.section || '',
                goTo        : q.go_to || null,


condGoTo : (function() {
    var rawList = q.condGoTo || q.cond_go_to || q.cond_go_tos || [];
    if (!Array.isArray(rawList)) return [];
    return rawList
        .filter(function(cg) {
            var g = cg.goto || cg.go_to || cg.goto_step || cg.next_question_id || cg.destination;
            return g !== null && g !== undefined && String(g) !== '';
        })
        .map(function (cg) {
            var gotoVal = cg.goto || cg.go_to || cg.goto_step || cg.next_question_id || cg.destination || '';
            if (gotoVal !== null && gotoVal !== undefined) gotoVal = String(gotoVal);
            var condList = cg.conditions || cg.condition_list || [];
            if (!Array.isArray(condList)) condList = [];
            return {
                goto : gotoVal,
                conditions : condList.map(function (c) {
                    return {
                        qid   : c.qid !== undefined ? String(c.qid) : (c.question_id ? String(c.question_id) : ''),
                        type  : c.type || c.condition_type || 'is_equal_to',
                        value : c.value !== undefined ? String(c.value) : (c.condition_value || ''),
                    };
                }),
            };
        });
}()),

conditions : (function() {
    var rawConds = q.conditions || q.show_conditions || [];
    if (!Array.isArray(rawConds)) return [];
    return rawConds.map(function (c) {
        return {
            label : c.label || '',
            qid   : c.qid !== undefined ? String(c.qid) : (c.question_id ? String(c.question_id) : ''),
            value : c.value !== undefined ? String(c.value) : (c.condition_value || ''),
        };
    });
}()),
                usedIn      : q.used_in_count || 0,
                options     : (q.options || []).map(o => ({
                    id    : o.id,
                    label : o.option_label,
                    value : o.option_value,
                })),
                isNew: false,
            };
        });

        CE.sections = (res[1].sections || []).map(function (s) {
            return {
                id                  : s.id,
                type                : s.type || 'content',
                content             : s.content || '',
                section_key         : s.section_key  || '',
                section_name        : s.section_name || '',
                text_align          : s.text_align || 'left',
                secure_blur_content : s.secure_blur_content || 0,
                conditions          : (s.conditions || []).map(function(c) {
                    return {
                        qid   : c.qid   || '',
                        type  : c.type  || 'is_equal_to',
                        value : c.value || '',
                    };
                }),
                isNew               : false,
            };
        });
        CE.sectionsFull       = CE.sections.slice();
        CE.deletedQuestionIds = [];
        CE.deletedSectionIds  = [];

        ceRenderQ();
        ceRenderPreview();
        window.__ceDataSnapshot = { questions: CE.questions, sections: CE.sections };
        ceEditor();

    }).catch(function (err) {
        ceError('Could not load data: ' + err.message);
    });
};


CE._selectedIndices = new Set();
var bar = document.getElementById('ceBulkBar');
if (bar) bar.classList.remove('open');
function ceRenderQ(filter) {
    var list = document.getElementById('ceQuestionsList');
    document.getElementById('ceQuestionCount').textContent = CE.questions.length;
    var lc    = (filter || '').toLowerCase();
    var items = CE.questions.filter(function (q) {
        return !lc || q.label.toLowerCase().includes(lc);
    });

    if (!items.length) {
        list.innerHTML = '<div style="text-align:center;color:#d1d5db;padding:40px 14px;font-size:12px;">'
            + (filter ? 'No matching questions.' : 'No questions yet.<br>Click <b>+ Add</b> to create one.')
            + '</div>';
        return;
    }

    list.innerHTML = items.map(function (q) {
        var ri      = CE.questions.indexOf(q);
        var usedCls = q.usedIn > 0 ? 'used' : '';
        var usedTxt = q.usedIn > 0 ? '&#128279; Used in ' + q.usedIn + ' place(s)' : '';
        var typeLabel = (q.type || 'textbox').replace(/-/g,' ').replace(/\b\w/g, function(c){ return c.toUpperCase(); });

        var goTosHtml = '';

        if (q.goTo) {
            var nextQ = CE.questions[CE.questions.indexOf(q) + 1];
            var isNext = nextQ && String(nextQ.id) === String(q.goTo);
            var dest = q.goTo === 'END'
                ? 'Checkout'
                : (isNext ? 'NEXT' : 'Q' + q.goTo);
            var destClick = q.goTo === 'END'
                ? ''
                : ' onclick="ceScrollToQuestion(\'' + q.goTo + '\')"';
            goTosHtml += '<div style="margin-bottom:3px;">'
            + '<span style="display:inline-flex;align-items:center;gap:1px;background:#e6f4ea;color:#1e8e3e;border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700;cursor:pointer;"'
            + destClick + '>'
            + '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>'
            + ' ' + esc(dest)
            + '</span>'
            + '</div>';
        }

       if (q.condGoTo && q.condGoTo.length > 0) {
            q.condGoTo.forEach(function(cg) {
                   if (!cg.goto && cg.goto !== 0) return;
                // var destLabel = cg.goto === 'END' ? 'Checkout' : 'Q' + cg.goto;
                // var destClick = cg.goto === 'END' ? '' : ' onclick="ceScrollToQuestion(\'' + cg.goto + '\')"';

                var _cgNextQ = CE.questions[CE.questions.indexOf(q) + 1];
                var _cgIsNext = _cgNextQ && String(_cgNextQ.id) === String(cg.goto);
                var destLabel = cg.goto === 'END' ? 'Checkout' : (_cgIsNext ? 'NEXT' : 'Q' + cg.goto);
                var destClick = cg.goto === 'END' ? '' : ' onclick="ceScrollToQuestion(\'' + cg.goto + '\')"';
                
                var condTexts = (cg.conditions || []).map(function(c) {
                    var qRef = (String(c.qid) === String(q.id)) ? 'answer' : 'Q' + c.qid;
                    var op = (c.type || 'is_equal_to');
                    if (op === 'is_equal_to' || op === '') op = '=';
                    else if (op === 'is_not_equal_to') op = '!=';
                    else if (op === 'is_greater_than') op = '>';
                    else if (op === 'is_less_than') op = '<';
                    return qRef + ' ' + op + ' ' + esc(c.value);
                }).join(' AND ');

                var isSimpleRadio = (q.type === 'radio-button' || q.type === 'radio' || q.type === 'dropdown' || q.type === 'select')
                    && (cg.conditions || []).length === 1
                    && String(cg.conditions[0].qid) === String(q.id)
                    && (cg.conditions[0].type === 'is_equal_to' || cg.conditions[0].type === '');

                var condBadge = condTexts
                    ? ' <span style="font-size:10px;color:#6b7280;background:#f3f4f6;border-radius:4px;padding:1px 6px;font-family:monospace;">' + condTexts + '</span>'
                    : '';

                if (isSimpleRadio) {
                    goTosHtml += '<div style="margin-bottom:3px;display:flex;align-items:center;gap:5px;padding-left:4px;">'
                        + '<span style="font-size:11px;color:#4b5563;">' + esc(cg.conditions[0].value) + '</span>'
                        + '<span style="display:inline-flex;align-items:center;gap:1px;background:#e6f4ea;color:#1e8e3e;border-radius:5px;padding:0px 8px;font-size:10px;font-weight:700;cursor:pointer;"'
                        + destClick + '>'
                        + '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>'
                        + ' ' + esc(destLabel)
                        + '</span>'
                        + '</div>';
                } else {
                    goTosHtml += '<div style="margin-bottom:3px;display:flex;align-items:center;gap:5px;flex-wrap:wrap;">'
                        + '<span style="display:inline-flex;align-items:center;gap:4px;background:#e6f4ea;color:#1e8e3e;border-radius:5px;padding:0px 8px;font-size:10px;font-weight:700;cursor:pointer;"'
                        + destClick + '>'
                        + '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>'
                        + ' ' + esc(destLabel)
                        + '</span>'
                        + condBadge
                        + '</div>';
                }
            });
        }

        var showIfHtml = '';
        if (q.conditions && q.conditions.length > 0) {
            var condParts = q.conditions.map(function(c) {
                var qRef = '<span style="cursor:pointer;font-weight:700;color:#e85d2f;" onclick="ceScrollToQuestion(\'' + c.qid + '\')">Q' + c.qid + '</span>';
                return qRef + ' = <span style="font-weight:600;color:#374151;">' + esc(c.value) + '</span>';
            }).join(' <span style="color:#9ca3af;font-size:10px;">AND</span> ');
            showIfHtml = '<div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">'
                + '<span style="display:inline-flex;align-items:center;gap:4px;background:#fef9c3;color:#854d0e;border:1px solid #fde68a;border-radius:5px;padding:2px 8px;font-size:10px;font-weight:700;white-space:nowrap;">'
                + '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
                + 'Show if</span>'
                + '<span style="font-size:11px;color:#374151;">' + condParts + '</span>'
                + '</div>';
        }

        var bottomHtml = '';
        if (goTosHtml || showIfHtml) {
            var allCondRows = [];
            if (goTosHtml) {
                // Split goTosHtml into individual row divs to count them
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = goTosHtml;
                var rowCount = tempDiv.children.length;
                allCondRows = rowCount;
            }

            var COLLAPSE_THRESHOLD = 2;
            var needsCollapse = allCondRows > COLLAPSE_THRESHOLD;
            var uniqueId = 'ce-cond-collapse-' + ri;

            if (needsCollapse) {
                // Split goTosHtml rows: first 5 visible, rest hidden
                var tempDiv2 = document.createElement('div');
                tempDiv2.innerHTML = goTosHtml;
                var visibleRows = '';
                var hiddenRows = '';
                Array.from(tempDiv2.children).forEach(function(child, idx) {
                    if (idx < COLLAPSE_THRESHOLD) {
                        visibleRows += child.outerHTML;
                    } else {
                        hiddenRows += child.outerHTML;
                    }
                });

                var hiddenCount = allCondRows - COLLAPSE_THRESHOLD;

                bottomHtml = '<div style="margin-top:8px;padding-top:8px;border-top:1.5px solid #e5e7eb;display:flex;flex-direction:column;gap:4px;">'
                    + visibleRows
                    + '<div id="' + uniqueId + '-extra" style="display:none;flex-direction:column;gap:4px;">'
                    + hiddenRows
                    + '</div>'
                    + (showIfHtml ? showIfHtml : '')
                    + '<button type="button" id="' + uniqueId + '-btn" onclick="ceCEToggleCondCollapse(\'' + uniqueId + '\', ' + hiddenCount + ', this)" '
                    + 'style="align-self:flex-start;display:inline-flex;align-items:center;gap:4px;background:none;border:none;cursor:pointer;font-size:11px;color:#6b7280;padding:2px 0;margin-top:2px;font-family:inherit;transition:color .15s;" '
                    + 'onmouseover="this.style.color=\'#e85d2f\'" onmouseout="this.style.color=\'#6b7280\'">'
                    + '<svg id="' + uniqueId + '-arrow" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="transition:transform .2s;"><polyline points="6 9 12 15 18 9"/></svg>'
                    + '<span id="' + uniqueId + '-label">View all ' + allCondRows + ' conditions</span>'
                    + '</button>'
                    + '</div>';
            } else {
                var parts = [];
                if (goTosHtml) parts.push(goTosHtml);
                if (showIfHtml) parts.push(showIfHtml);
                bottomHtml = '<div style="margin-top:8px;padding-top:8px;border-top:1.5px solid #e5e7eb;display:flex;flex-direction:column;gap:4px;">'
                    + parts.join('')
                    + '</div>';
            }
        }

                return '<div class="ce-qcard" id="ce-qcard-' + ri + '">'
        + '<div class="ce-qcard-top">'
        +   '<div style="display:flex;align-items:center;gap:6px;">'
        +     '<input type="checkbox" class="ce-qcard-checkbox" data-ri="' + ri + '" onchange="ceToggleSelect(this,' + ri + ')" onclick="event.stopPropagation();">'
        +     '<span class="ce-qid" style="cursor:pointer;" title="Click to find in contract" onclick="event.stopPropagation();ceScrollToUsages(' + q.id + ', this)">Q' + q.id + '</span>'
        +   '</div>'
        +   '<div class="ce-qcard-actions">'
        +     '<button type="button" class="ce-icon-btn copy-btn" onclick="ceCopyQ(' + ri + ')" title="Copy question text">'
        +       '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>'
        +     '</button>'
        +     '<button type="button" class="ce-icon-btn" onclick="cePasteQ(' + ri + ')" title="Paste copied question after this">'
        +       '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>'
        +     '</button>'
        +     '<button type="button" class="ce-icon-btn" onclick="ceEditQ(' + ri + ')" title="Edit"><i class="fa fa-pencil"></i></button>'
        +     '<button type="button" class="ce-icon-btn del" onclick="ceDelQ(' + ri + ')" title="Delete"><i class="fa fa-trash"></i></button>'
        +     '<button type="button" class="ce-icon-btn add-btn" onclick="ceInsertQAfter(' + ri + ')" title="Add question after">'
        +       '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>'
        +     '</button>'
        +     '<span class="ce-drag-handle ce-qcard-drag" title="Drag to reorder">'
        +       '<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="5" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="5" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="15" cy="19" r="1.5"/></svg>'
        +     '</span>'
        +   '</div>'
        + '</div>'
        + (q.label ? '<div class="ce-qlabel">' + esc(q.label) + (q.required ? ' <span class="ce-qreq"></span>' : '') + '</div>' : '')
        + '<div class="ce-qfield-preview" style="padding:6px 10px 8px;">' + ceRenderInlineField(q, ri) + bottomHtml + '</div>'
        + '</div>';
            }).join('');
        if (CE._highlightedQid !== null) {
            var hIdx = -1;
            for (var hi = 0; hi < CE.questions.length; hi++) {
                if (String(CE.questions[hi].id) === String(CE._highlightedQid)) { hIdx = hi; break; }
            }
            if (hIdx >= 0) {
                var hCards = list.querySelectorAll('.ce-qcard');
                if (hCards[hIdx]) {
                    hCards[hIdx].style.transition  = 'box-shadow .2s, border-color .2s';
                    hCards[hIdx].style.boxShadow   = '0 0 0 3px #e85d2f55';
                    hCards[hIdx].style.borderColor = '#e85d2f';
                }
            }
        }
            ceInitSortable();
        }

window.ceCEToggleCondCollapse = function(uid, hiddenCount, btn) {
    var extra  = document.getElementById(uid + '-extra');
    var arrow  = document.getElementById(uid + '-arrow');
    var label  = document.getElementById(uid + '-label');
    if (!extra) return;

    var isOpen = extra.style.display === 'flex';
    if (isOpen) {
        extra.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
        label.textContent = 'View all ' + (hiddenCount + 2) + ' conditions';
    } else {
        extra.style.display = 'flex';
        extra.style.flexDirection = 'column';
        extra.style.gap = '4px';
        arrow.style.transform = 'rotate(180deg)';
        label.textContent = 'Show less';
    }
};

    function ceRenderPreview(filterType) {
    CE._selectedSectionIndices = new Set();
    var sbar = document.getElementById('ceSBulkBar');
    if (sbar) sbar.classList.remove('open');
    var smast = document.getElementById('ceSelectAllSections');
    if (smast) smast.checked = false;


        var searchEl = document.getElementById('ceSearchSections');
        if (searchEl && !searchEl.value) {
            CE.sections = CE.sectionsFull.slice();
        }

    var el  = document.getElementById('ceContractPreview');
    var src = filterType
            ? CE.sections.filter(function (s) { return s.type === filterType; })
            : CE.sections;

        document.getElementById('ceSectionCount').textContent = CE.sections.length;

        if (!src.length) {
            el.innerHTML = '<div style="text-align:center;color:#d1d5db;padding:60px 20px;font-size:13px;">'
                + (filterType ? 'No sections of this type.' : 'No contract sections yet.<br>Click <b>+ Add</b> to start building.')
                + '</div>';
            return;
        }

        var sectionCounter    = 0;
        var subsectionCounter = {};

        el.innerHTML = src.map(function (s) {
            var idx = CE.sections.indexOf(s);

            if (s.isStdClauseGroup) {
                var totalItems = s.stdClauseTotalItems || 0;
                return '<div class="ce-sgroup-block ce-sblock" data-idx="' + idx + '">'
                    + '<div class="ce-sgroup-header" onclick="ceToggleGroupBlock(this)">'
                    +   '<div class="ce-sgroup-title">'
                    +     '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>'
                    +     esc(s.stdClauseTitle || s.section_name || 'Standard Clause')
                    +     '<span class="ce-sgroup-badge">' + totalItems + ' items</span>'
                    +   '</div>'
                    +   '<div style="display:flex;align-items:center;gap:6px;">'
                    +     '<button type="button" class="ce-icon-btn" onclick="event.stopPropagation();ceExpandGroupBlock(' + idx + ')" title="Expand / Edit in sub-view" style="background:#e85d2f;color:#fff;">'
                    +       '<svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>'
                    +     '</button>'
                    +     '<button type="button" class="ce-icon-btn del" onclick="event.stopPropagation();ceDelS(' + idx + ')" title="Delete group"><i class="fa fa-trash"></i></button>'
                    +     '<span class="ce-sdrag-handle ce-sblock-drag" title="Drag to reorder">'
                    +       '<svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="5" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="5" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="15" cy="19" r="1.5"/></svg>'
                    +     '</span>'
                    +   '</div>'
                    + '</div>'
                    + '</div>';
            }

            var tid      = s.isNew ? 'T (new)' : 'T' + s.id;
            var rendered = ceReplacePlaceholders(s.content || '');

            var numPrefix = '';
            if (s.type === 'content_title') {
                sectionCounter = 0;
                subsectionCounter = {};
            } else if (s.type === 'content_heading') {
                sectionCounter++;
                subsectionCounter[sectionCounter] = 0;
                var n = _ceGetNumber(CE.config.numSections, sectionCounter);
                numPrefix = n ? '<span style="font-size:12px;font-weight:700;color:#9ca3af;margin-right:4px;"></span>' : '';
            } else if (s.type === 'content') {
                var parentSec = sectionCounter || 1;
                subsectionCounter[parentSec] = (subsectionCounter[parentSec] || 0) + 1;
                var subN = subsectionCounter[parentSec];
                var subNum = CE.config.numSubsections === '1.1'
                    ? parentSec + '.' + subN
                    : _ceGetNumber(CE.config.numSubsections, subN);
                numPrefix = subNum ? '<span style="font-size:11px;font-weight:600;color:#b0b7c3;margin-right:0px;"></span>' : '';
            }

            var inner = '';
            if (s.type === 'content_title') {
                inner = '<div class="ce-stitle" style="text-align:' + esc(s.text_align) + ';max-height:220px;overflow-y:auto;font-size:18px;font-weight:800;color:#1a1a2e;letter-spacing:.2px;">' + rendered + '</div>';
            } else if (s.type === 'content_heading') {
                inner = '<div class="ce-sheading" style="text-align:' + esc(s.text_align) + ';max-height:220px;overflow-y:auto;">' + numPrefix + rendered + '</div>';
            } else if (s.type === 'signature_field') {
                inner = '<div class="ce-sig">'
                    + '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5">'
                    + '<path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>'
                    + ' Signature field</div>';
            } else {
                var blurStyle = s.secure_blur_content ? 'filter:blur(4px);user-select:none;' : '';
                inner = '<div class="ce-scontent" style="text-align:' + esc(s.text_align) + ';' + blurStyle + 'max-height:220px;overflow-y:auto;">'
                    + numPrefix + rendered + '</div>';
            }

           return '<div class="ce-sblock" id="ce-sblock-' + idx + '" style="padding-left:6px;">'
    +   '<div class="ce-stid">'
    +   '<div style="display:flex;align-items:center;gap:6px;">'
    +     '<input type="checkbox" class="ce-sblock-checkbox" data-idx="' + idx + '" onchange="ceToggleSectionSelect(this,' + idx + ')" onclick="event.stopPropagation();">'
    +     '<span style="color:#e85d2f;font-weight:700;background:#fff4f0; padding:2px 6px;">' + esc(tid) + '</span>'
    +   '</div>'
    +  '<div class="align-contract-btn">'
    +   '<button type="button" class="ce-icon-btn copy-btn" onclick="ceCopyS(' + idx + ')" title="Copy section content">'
    +     '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>'
    +   '</button>'

    +   '<button type="button" class="ce-icon-btn" id="ce-paste-btn-' + idx + '" onclick="ceShowPasteDropdown(this,' + idx + ')" title="Paste options">'
    +     '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>'
    +   '</button>'

    +   '<button type="button" class="ce-icon-btn" onclick="ceEditS(' + idx + ')" title="Edit"><i class="fa fa-pencil"></i></button>'

    +   '<button type="button" class="ce-icon-btn del" onclick="ceDelS(' + idx + ')" title="Delete"><i class="fa fa-trash"></i></button>'

   +   '<button type="button" class="ce-icon-btn add-btn" onclick="ceInsertSAfter(' + idx + ')" title="Add section after">'
+     '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>'
+   '</button>'

    +   '<span class="ce-sdrag-handle ce-sblock-drag" title="Drag to reorder">'
    +     '<svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="5" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="5" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="15" cy="19" r="1.5"/></svg>'
    +   '</span>'
    + '</div>'
    +  '</div>'
    + '<hr>'
    + inner

    + '<hr class="ce-sdivider">'
    + (function() {
        if (!s.conditions || !s.conditions.length) return '';
        var condParts = s.conditions.map(function(c) {
            var op = (c.type || 'is_equal_to');
            if (op === 'is_equal_to'     || op === '') op = '=';
            else if (op === 'is_not_equal_to') op = '!=';
            else if (op === 'is_greater_than') op = '>';
            else if (op === 'is_less_than')    op = '<';
            var destQ = CE.questions.find(function(qq){ return String(qq.id) === String(c.qid); });
            var qLabel = destQ ? (destQ.label ? destQ.label.substring(0,20) + (destQ.label.length > 20 ? '…' : '') : 'Q' + c.qid) : 'Q' + c.qid;
            return '<span style="display:inline-flex;align-items:center;gap:4px;background:#e6f4ea;color:#1e8e3e;border-radius:5px;padding:2px 7px;font-size:10px;font-weight:700;cursor:pointer;" onclick="ceScrollToQuestion(\'' + c.qid + '\')">'
                + '<svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>'
                + ' Q' + esc(String(c.qid))
                + '</span>'
                + '<span style="font-size:10px;color:#6b7280;font-family:monospace;background:#f3f4f6;border-radius:4px;padding:1px 5px;">' + op + ' ' + esc(c.value) + '</span>';
        }).join('<span style="font-size:9px;color:#9ca3af;font-weight:700;padding:0 2px;">AND</span>');

        return '<div style="border-top:1.5px solid #e5e7eb;margin-top:6px;padding:6px 8px 4px;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">'
            // + '<span style="display:inline-flex;align-items:center;gap:3px;background:#fef9c3;color:#854d0e;border:1px solid #fde68a;border-radius:5px;padding:2px 7px;font-size:10px;font-weight:700;white-space:nowrap;">'
            // + '<svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
            // + ' Show if</span>'
            + condParts
            + '</div>';
    }())
    + '</div>';
        }).join('');
            ceInitSectionSortable();
    }

    window.ceToggleGroupBlock = function (header) {
        var body = header.nextElementSibling;
        if (body && body.classList.contains('ce-sgroup-body')) {
            body.classList.toggle('open');
        }
    };

    window.ceExpandGroupBlock = function (idx) {
        var s = CE.sections[idx];
        if (!s || !s.isStdClauseGroup) return;
        CE._subViewClauseId = s.stdClauseId;

        document.getElementById('ceSubViewTitle').textContent = s.stdClauseTitle || 'Standard Clause';
        document.getElementById('ceSubViewMeta').textContent  =
            (s.stdClauseSections  ? s.stdClauseSections.length  : 0) + ' section(s) · ' +
            (s.stdClauseQuestions ? s.stdClauseQuestions.length : 0) + ' question(s)';

        var body = document.getElementById('ceSubViewBody');
        body.innerHTML = '';

        if (s.stdClauseSections && s.stdClauseSections.length) {
            body.innerHTML += '<h4 style="font-size:14px;font-weight:700;color:#1a1a2e;margin-bottom:12px;">Contract Text</h4>';
            s.stdClauseSections.forEach(function (sec) {
                var rendered = ceReplacePlaceholders(sec.content || '');
                body.innerHTML += '<div style="background:#fff;border:1px solid #ebebeb;border-radius:6px;padding:10px 14px;margin-bottom:8px;">'
                    + '<span style="font-size:10px;font-weight:700;color:#e85d2f;font-family:monospace;background:#fff4f0;padding:2px 6px;border-radius:3px;margin-right:6px;">' + esc(sec.type || '') + '</span>'
                    + '<div style="font-size:12px;color:#374151;margin-top:6px;">' + rendered + '</div>'
                    + '</div>';
            });
        }

        if (s.stdClauseQuestions && s.stdClauseQuestions.length) {
            body.innerHTML += '<h4 style="font-size:14px;font-weight:700;color:#1a1a2e;margin:16px 0 12px;">Questions</h4>';
            s.stdClauseQuestions.forEach(function (q) {
                body.innerHTML += '<div style="background:#fff;border:1px solid #ebebeb;border-radius:6px;padding:10px 14px;margin-bottom:8px;">'
                    + '<span style="font-size:10px;font-weight:700;color:#e85d2f;font-family:monospace;">Q' + q.id + '</span>'
                    + '<div style="font-size:12px;color:#374151;margin-top:4px;">' + esc(q.label || '') + '</div>'
                    + '</div>';
            });
        }

        document.getElementById('ceStdClauseSubView').classList.add('open');
    };


   window.ceSectionTypeChange = function (idx, newType) {
    var s = CE.sections[idx];
    if (!s) return;
    s.type = newType;
    ceRenderPreview();
    ceSetStatus('Unsaved changes');
};

window.ceSModalTypeChange = function () {
    var t = document.getElementById('ceSModalType').value;
    var wrap = document.getElementById('ceSModalCondWrap');
    if (wrap) wrap.style.display = (t === 'content') ? 'block' : 'none';

    var contentEditor = document.getElementById('ceSModalContentEditor');
    var contentFg = contentEditor ? contentEditor.closest('.ce-fg') : null;
    var alignRow  = document.querySelector('#ceSectionModal .iner-ce-fg')
                  ? document.querySelector('#ceSectionModal .iner-ce-fg').closest('.row') : null;
    var blurBtn   = document.querySelector('.contract-blur-button');
    var stdClausesWrap = document.getElementById('ceSModalStdClausesWrap');

    if (!stdClausesWrap) {
        stdClausesWrap = document.createElement('div');
        stdClausesWrap.id = 'ceSModalStdClausesWrap';
        stdClausesWrap.style.cssText = 'display:none;padding:10px 20px 8px;';
        stdClausesWrap.innerHTML =
            '<label class="ce-flabel" style="margin-bottom:6px;"></label>'
          + '<div style="display:flex;gap:8px;align-items:center;margin-bottom:8px;">'
          + '  <input type="text" id="ceSModalStdSearch" class="form-control form-control-sm" '
          + '    placeholder="Search clauses…" oninput="ceSModalFilterStdClauses(this.value)" style="flex:1;">'
          + '  <label style="display:flex;align-items:center;gap:5px;font-size:12px;color:#374151;cursor:pointer;white-space:nowrap;">'
          + '    <input type="checkbox" id="ceSModalStdSelectAll" onchange="ceSModalToggleAll(this)" style="width:14px;height:14px;accent-color:#e85d2f;">'
          + '    Select all'
          + '  </label>'
          + '</div>'
          + '<div id="ceSModalStdList" style="height:300px; max-height:300px;overflow-y:auto;border:1px solid #e5e7eb;border-radius:8px;padding:6px;">'
          + '  <div style="text-align:center;color:#9ca3af;padding:20px;font-size:12px;">Loading…</div>'
          + '</div>'
          + '<div style="display:flex;justify-content:flex-end;margin-top:8px;">'
        //   + '  <button type="button" id="ceSModalInsertBtn" onclick="ceSModalInsertSelected()" disabled '
        //   + '    style="background:#e85d2f;color:#fff;border:none;border-radius:6px;padding:7px 18px;font-size:12px;font-weight:600;cursor:not-allowed;opacity:.5;transition:all .12s;">'
        //   + '    Insert'
        //   + '  </button>'
          + '</div>';

        if (blurBtn && blurBtn.parentNode) {
            blurBtn.parentNode.insertBefore(stdClausesWrap, blurBtn);
        } else {
            var mbody = document.querySelector('#ceSectionModal .ce-mbody');
            if (mbody) mbody.appendChild(stdClausesWrap);
        }
    }

    var alignFg = document.querySelector('#ceSectionModal .iner-ce-fg');
    var alignRow = alignFg ? alignFg.closest('.row') : null;
    
    if (t === 'standard-clauses') {
        if (contentFg)  contentFg.style.display  = 'none';
        if (alignRow)   alignRow.style.display   = 'none';
        if (alignFg)    alignFg.style.display    = 'none';
        if (blurBtn)    blurBtn.style.display     = 'none';
        stdClausesWrap.style.display = 'block';
        window._ceSModalSelectedIds = new Set();
        ceSModalLoadStdClauses();
    } else {
        if (contentFg)  contentFg.style.display  = '';
        if (alignRow)   alignRow.style.display   = '';
        if (alignFg)    alignFg.style.display    = '';
        if (blurBtn)    blurBtn.style.display     = '';
        stdClausesWrap.style.display = 'none';
    }
};

// Select all on current page
window.ceSModalToggleAll = function(cb) {
    var ids = window._ceSModalSelectedIds = window._ceSModalSelectedIds || new Set();
    document.querySelectorAll('#ceSModalStdList .ce-smodal-std-item').forEach(function(item) {
        var id = parseInt(item.getAttribute('data-id'));
        if (cb.checked) {
            ids.add(id);
            item.classList.add('selected');
            item.style.background  = '#fff4f0';
            item.style.borderColor = '#e85d2f';
            var check = item.querySelector('.ce-smodal-std-cb');
            if (check) check.checked = true;
            if (!item.querySelector('svg')) {
                item.insertAdjacentHTML('beforeend', '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#e85d2f" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>');
            }
        } else {
            ids.delete(id);
            item.classList.remove('selected');
            item.style.background  = '#fff';
            item.style.borderColor = '#e5e7eb';
            var check = item.querySelector('.ce-smodal-std-cb');
            if (check) check.checked = false;
            var tick = item.querySelector('svg:last-child');
            if (tick) tick.remove();
        }
    });
    _ceSModalUpdateSelectBtn();
};

// Insert selected — reuses existing paste logic
window.ceSModalInsertSelected = function() {
    var ids = window._ceSModalSelectedIds || new Set();
    if (!ids.size) return;

    // Close section modal
    closeCeSectionModal();

    // Set insert position
    CE._stdClauseInsertAfterIdx = CE.editingSectionIdx !== null
        ? CE.editingSectionIdx
        : CE.sections.length - 1;

    var checkedIds = Array.from(ids);
    window._ceSModalSelectedIds = new Set();

    var allMeta = window._ceSModalAllOnPage || [];
    var currentInsertPos = CE._stdClauseInsertAfterIdx;

    var fetchNext = function(i) {
        if (i >= checkedIds.length) {
            CE.sectionsFull = CE.sections.slice();
            ceRenderQ();
            ceRenderPreview();
            ceSetStatus(checkedIds.length + ' standard clause(s) inserted');
            setTimeout(function() { ceSetStatus(''); }, 2500);
            return;
        }

        var clauseId = checkedIds[i];
        var clauseMeta = allMeta.find(function(c) { return c.id === clauseId; }) || { id: clauseId, title: '', name: '' };

        fetch('/admin-dashboard/api/standard-document-detail/' + clauseId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var clauseSections  = data.sections  || [];
            var clauseQuestions = data.questions || [];
            var clauseTitle     = clauseMeta.title || clauseMeta.name || (data.clause && data.clause.title) || 'Standard Clause';
            var totalItems      = clauseSections.length + clauseQuestions.length;
            var shouldGroup     = !CE.config.neverHide && totalItems > CE.config.hideThreshold;

            if (shouldGroup) {
                CE.sections.splice(currentInsertPos + 1, 0, {
                    id: 'new_' + Date.now() + '_g' + i, type: 'content',
                    content: '<!-- STD_CLAUSE_GROUP:' + clauseId + ':' + esc(clauseTitle) + ' -->',
                    section_key: 'std_clause_' + clauseId, section_name: clauseTitle,
                    text_align: 'left', secure_blur_content: 0, isNew: true,
                    isStdClauseGroup: true, stdClauseId: clauseId, stdClauseTitle: clauseTitle,
                    stdClauseSections: clauseSections, stdClauseQuestions: clauseQuestions,
                    stdClauseTotalItems: totalItems,
                });
                currentInsertPos += 1;
            } else {
                clauseSections.forEach(function(s, si) {
                    CE.sections.splice(currentInsertPos + 1 + si, 0, {
                        id: 'new_' + Date.now() + '_s' + i + '_' + si,
                        type: s.type || 'content', content: s.content || '',
                        section_key: s.section_key || '', section_name: clauseTitle,
                        text_align: s.text_align || 'left', secure_blur_content: s.secure_blur_content || 0,
                        isNew: true,
                    });
                });
                currentInsertPos += clauseSections.length || 1;
            }

            if (clauseQuestions.length > 0) {
                clauseQuestions.forEach(function(cq, qi) {
                    CE.questions.push({
                        id: 'new_' + Date.now() + '_q' + i + '_' + qi,
                        type: cq.type || 'textbox', label: cq.label || '', info: cq.info || '',
                        placeholder: cq.placeholder || '', required: 1, section: clauseTitle,
                        goTo: null, usedIn: 0, options: cq.options || [],
                        conditions: [], condGoTo: [], isNew: true,
                    });
                });
            }
            fetchNext(i + 1);
        })
        .catch(function(err) {
            ceShowToast('Failed: ' + err.message, true);
            fetchNext(i + 1);
        });
    };

    fetchNext(0);
};

// Replace ceSModalLoadStdClauses
window.ceSModalLoadStdClauses = function() {
    window._ceSModalPage = 1;
    window._ceSModalPerPage = 10;
    window._ceSModalSearch = '';
    window._ceSModalSelectedIds = new Set();
    _ceSModalFetchPage(1);
};

function _ceSModalFetchPage(page) {
    var listEl = document.getElementById('ceSModalStdList');
    if (!listEl) return;

    listEl.innerHTML = '<div style="text-align:center;color:#9ca3af;padding:20px;font-size:12px;">'
        + '<div style="width:20px;height:20px;border:2px solid #e5e7eb;border-top-color:#e85d2f;border-radius:50%;animation:ceSpin 1s linear infinite;margin:0 auto 8px;"></div>Loading…</div>';

    var url = '/admin-dashboard/api/standard-documents?page=' + page + '&per_page=' + window._ceSModalPerPage;
    if (window._ceSModalSearch) url += '&search=' + encodeURIComponent(window._ceSModalSearch);

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        window._ceSModalPage = page;
        window._ceSModalTotalPages = data.total_pages || 1;
        window._ceSModalTotal = data.total || 0;
        window._ceSModalAllOnPage = data.documents || [];
        ceSModalRenderStdList(window._ceSModalAllOnPage);
        _ceSModalRenderPagination();
    })
    .catch(function() {
        if (listEl) listEl.innerHTML = '<div style="text-align:center;color:#dc2626;padding:20px;font-size:12px;">Failed to load.</div>';
    });
}

// toggle function
window.ceSModalToggleItem = function(id, el) {
    if (!window._ceSModalSelectedIds) window._ceSModalSelectedIds = new Set();
    var ids = window._ceSModalSelectedIds;
    var tick = el.querySelector('.ce-smodal-tick');
    if (ids.has(id)) {
        ids.delete(id);
        el.classList.remove('selected');
        el.style.background   = '#fff';
        el.style.borderColor  = '#e5e7eb';
        var check = el.querySelector('.ce-smodal-std-cb');
        if (check) check.checked = false;
        if (tick) tick.remove();
    } else {
        ids.add(id);
        el.classList.add('selected');
        el.style.background  = '#fff4f0';
        el.style.borderColor = '#e85d2f';
        var check = el.querySelector('.ce-smodal-std-cb');
        if (check) check.checked = true;
        if (!tick) {
            el.insertAdjacentHTML('beforeend', '<svg class="ce-smodal-tick" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#e85d2f" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>');
        }
    }
    _ceSModalUpdateSelectBtn();
};

function _ceSModalUpdateSelectBtn() {
    var btn = document.getElementById('ceSModalInsertBtn');
    var count = (window._ceSModalSelectedIds || new Set()).size;
    if (btn) {
        // btn.disabled = count === 0;
        // btn.textContent = count > 0 ? 'Insert (' + count + ')' : 'Insert';
        // btn.style.opacity = count > 0 ? '1' : '0.5';
        // btn.style.cursor  = count > 0 ? 'pointer' : 'not-allowed';
    }
}

function _ceSModalRenderPagination() {
    var existing = document.getElementById('ceSModalStdPagination');
    if (existing) existing.remove();

    var total   = window._ceSModalTotalPages || 1;
    var current = window._ceSModalPage || 1;
    var totalCount = window._ceSModalTotal || 0;

    var wrap = document.createElement('div');
    wrap.id = 'ceSModalStdPagination';
    wrap.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:8px 0 2px;flex-wrap:wrap;gap:4px;';

    var info = document.createElement('span');
    info.style.cssText = 'font-size:11px;color:#9ca3af;';
    info.textContent = 'Page ' + current + ' of ' + total + ' (' + totalCount + ' clauses)';

    var btns = document.createElement('div');
    btns.style.cssText = 'display:flex;gap:3px;align-items:center;';

    var btnBase = 'padding:3px 8px;font-size:11px;border-radius:4px;cursor:pointer;transition:all .12s;';

    var prev = document.createElement('button');
    prev.type = 'button'; prev.innerHTML = '&laquo;';
    prev.style.cssText = btnBase + 'border:1px solid #e5e7eb;background:#fff;color:' + (current <= 1 ? '#d1d5db' : '#374151') + ';';
    prev.disabled = current <= 1;
    prev.onclick = function() { _ceSModalFetchPage(current - 1); };
    btns.appendChild(prev);

    var pages = [];
    if (total <= 5) {
        for (var i = 1; i <= total; i++) pages.push(i);
    } else {
        pages.push(1);
        if (current > 3) pages.push('…');
        for (var p = Math.max(2, current - 1); p <= Math.min(total - 1, current + 1); p++) pages.push(p);
        if (current < total - 2) pages.push('…');
        if (total > 1) pages.push(total);
    }

    pages.forEach(function(pg) {
        if (pg === '…') {
            var span = document.createElement('span');
            span.style.cssText = 'padding:3px 5px;font-size:11px;color:#9ca3af;';
            span.textContent = '…';
            btns.appendChild(span);
        } else {
            var pb = document.createElement('button');
            pb.type = 'button'; pb.textContent = pg;
            var isActive = pg === current;
            pb.style.cssText = btnBase + (isActive
                ? 'border:1px solid #e85d2f;background:#e85d2f;color:#fff;font-weight:700;cursor:default;'
                : 'border:1px solid #e5e7eb;background:#fff;color:#374151;');
            pb.disabled = isActive;
            if (!isActive) {
                pb.onclick = (function(p){ return function(){ _ceSModalFetchPage(p); }; })(pg);
                pb.onmouseover = function(){ this.style.background='#f3f4f6'; };
                pb.onmouseout  = function(){ this.style.background='#fff'; };
            }
            btns.appendChild(pb);
        }
    });

    var next = document.createElement('button');
    next.type = 'button'; next.innerHTML = '&raquo;';
    next.style.cssText = btnBase + 'border:1px solid #e5e7eb;background:#fff;color:' + (current >= total ? '#d1d5db' : '#374151') + ';';
    next.disabled = current >= total;
    next.onclick = function() { _ceSModalFetchPage(current + 1); };
    btns.appendChild(next);

    wrap.appendChild(info);
    wrap.appendChild(btns);

    var stdWrap = document.getElementById('ceSModalStdClausesWrap');
    if (stdWrap) stdWrap.appendChild(wrap);
}

window.ceSModalFilterStdClauses = function(v) {
    var lc = v.toLowerCase();
    var filtered = (window._ceSModalStdAll || []).filter(function(c) {
        return (c.title || c.name || '').toLowerCase().includes(lc);
    });
    ceSModalRenderStdList(filtered);
};

// Replace ceSModalRenderStdList
window.ceSModalRenderStdList = function(clauses) {
    var listEl = document.getElementById('ceSModalStdList');
    if (!listEl) return;

    if (!clauses.length) {
        listEl.innerHTML = '<div style="text-align:center;color:#9ca3af;padding:20px;font-size:12px;">No clauses found.</div>';
        return;
    }

    if (!window._ceSModalSelectedIds) window._ceSModalSelectedIds = new Set();
    var selectedIds = window._ceSModalSelectedIds;

    listEl.innerHTML = clauses.map(function(c) {
        var isSelected = selectedIds.has(c.id);
        return '<div class="ce-smodal-std-item" data-id="' + c.id + '" '
            + 'onclick="ceSModalToggleItem(' + c.id + ', this)" '
            + 'style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:6px;cursor:pointer;margin-bottom:4px;'
            + 'background:' + (isSelected ? '#fff4f0' : '#fff') + ';border:1.5px solid ' + (isSelected ? '#e85d2f' : '#e5e7eb') + ';transition:all .12s;"'
            + 'onmouseover="if(!this.classList.contains(\'selected\'))this.style.background=\'#fafafa\'"'
            + 'onmouseout="if(!this.classList.contains(\'selected\'))this.style.background=\'' + (isSelected ? '#fff4f0' : '#fff') + '\'">'
            + '<input type="checkbox" class="ce-smodal-std-cb" data-id="' + c.id + '" '
            + (isSelected ? 'checked' : '') + ' '
            + 'onclick="event.stopPropagation();ceSModalToggleItem(' + c.id + ', this.closest(\'.ce-smodal-std-item\'))" '
            + 'style="width:15px;height:15px;accent-color:#e85d2f;flex-shrink:0;cursor:pointer;">'
            + '<div style="flex:1;min-width:0;">'
            + '<div style="font-size:12px;font-weight:600;color:#1f2937;">' + esc(c.title || c.name || '') + '</div>'
            + '<div style="font-size:11px;color:#9ca3af;margin-top:2px;">'
            + (c.questions_count || 0) + ' question(s) · ' + (c.sections_count || 0) + ' section(s)'
            + '</div>'
            + '</div>'
            + (isSelected ? '<svg class="ce-smodal-tick" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#e85d2f" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>' : '')
            + '</div>';
    }).join('');

    _ceSModalUpdateSelectBtn();
};

window.ceSModalSelectStd = function(id, el) {
    window._ceSModalSelectedStdId = id;

    // Update visual selection
    document.querySelectorAll('.ce-smodal-std-item').forEach(function(item) {
        item.classList.remove('selected');
        item.style.background   = '#fff';
        item.style.borderColor  = 'transparent';
        item.querySelector('svg') && item.querySelector('svg:last-child') && item.querySelector('svg:last-child').remove();
    });

    el.classList.add('selected');
    el.style.background  = '#fff4f0';
    el.style.borderColor = '#e85d2f';
    if (!el.querySelector('svg')) {
        el.insertAdjacentHTML('beforeend', '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#e85d2f" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>');
    }
};

window.ceAddSectionCondGroup = function () {
    var container = document.getElementById('ceSCondGroupsContainer');
    if (!container) return;
    document.getElementById('ceAddSectionCondBtn').style.display = 'none';
    var gi = container.children.length;

    var iStyle = 'padding:7px 9px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:12px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;width:100%;';
    var iFocus = 'onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';

    var grp = document.createElement('div');
    grp.id = 'ceSCondGroup_' + gi;
    grp.style.cssText = 'border:1px solid #e5e7eb;border-radius:8px;padding:14px 14px 10px;background:#f9fafb;margin-bottom:12px;';

    grp.innerHTML =
        '<div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Conditions</div>'
        + '<div class="ce-scond-rows-' + gi + '"></div>'
        + '<div style="display:flex;justify-content:flex-end;margin-top:6px;">'
        // +   '<button type="button" onclick="this.closest(\'[id^=ceSCondGroup_]\').remove()" '
        + '<button type="button" onclick="this.closest(\'[id^=ceSCondGroup_]\').remove();if(!document.getElementById(\'ceSCondGroupsContainer\').children.length){document.getElementById(\'ceAddSectionCondBtn\').style.display=\'\'}" '
        +   'style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:11px;display:flex;align-items:center;gap:4px;" '
        +   'onmouseover="this.style.color=\'#dc2626\'" onmouseout="this.style.color=\'#9ca3af\'">'
        +   '<i class="fa fa-trash" style="font-size:10px;"></i> Remove Condition'
        +   '</button>'
        + '</div>';

    container.appendChild(grp);
    ceAddSectionCondRow(gi);
};

window.ceAddSectionCondRow = function (gi) {
    var rowsEl = document.querySelector('.ce-scond-rows-' + gi);
    if (!rowsEl) return;

    var iStyle = 'padding:7px 9px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:12px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;width:100%;';
    var iFocus = 'onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';

    var qidOpts = '<option value="">Question ID</option>';
    CE.questions.forEach(function(qq) {
        qidOpts += '<option value="' + esc(qq.id) + '">' + esc(qq.id) + (qq.label ? ' — ' + qq.label.substring(0,25) : '') + '</option>';
    });

    var condOpts = '<option value="">Select</option>'
        + '<option value="is_equal_to">is equal to</option>'
        + '<option value="is_not_equal_to">is not equal to</option>'
        + '<option value="is_less_than">is less than</option>'
        + '<option value="is_greater_than">is greater than</option>';

    var row = document.createElement('div');
    row.setAttribute('data-scond-row', rowsEl.children.length);
    row.style.cssText = 'display:flex;gap:6px;align-items:flex-end;margin-bottom:8px;';

    row.innerHTML =
        '<div style="flex:1.2;position:relative;padding-top:10px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#f9fafb;padding:0 3px;font-weight:600;z-index:1;">Question ID</span>'
        + '<select class="ce-scond-qid" style="' + iStyle + 'cursor:pointer;" ' + iFocus + '>' + qidOpts + '</select>'
        + '</div>'
        + '<div style="flex:1.4;position:relative;padding-top:10px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#f9fafb;padding:0 3px;font-weight:600;z-index:1;">Condition</span>'
        + '<select class="ce-scond-type" style="' + iStyle + 'cursor:pointer;" ' + iFocus + '>' + condOpts + '</select>'
        + '</div>'
        + '<div style="flex:1;position:relative;padding-top:10px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#f9fafb;padding:0 3px;font-weight:600;z-index:1;"> Question Value</span>'
        + '<input type="text" class="ce-scond-val" style="' + iStyle + '" ' + iFocus + ' placeholder="">'
        + '</div>'
        + '<button type="button" onclick="this.closest(\'[data-scond-row]\').remove()" '
        + 'style="flex-shrink:0;width:26px;height:26px;background:#80808036;border:1px solid #e2e8f0;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;margin-bottom:1px;" '
        + 'onmouseover="this.style.background=\'#80808036\';this.style.color=\'#94a3b8\';this.style.borderColor=\'80808036\'" '
        + 'onmouseout="this.style.background=\'80808036\';this.style.color=\'#94a3b8\';this.style.borderColor=\'80808036\'">'
        + '<i class="fa fa-trash" style="font-size:8px;"></i>'
        + '</button>'
        + '<button type="button" onclick="ceAddSectionCondRow(' + gi + ')" class="ce-scond-add-btn" '
        + 'style="flex-shrink:0;width:26px;height:26px;background:#80808036;border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:rgb(60, 77, 98);margin-bottom:1px;" '
        + 'onmouseover="this.style.background=\'#80808036\'" onmouseout="this.style.background=\'#80808036\'">'
        + '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>'
        + '</button>';

    if (rowsEl.children.length > 0) {
        var prevRow = rowsEl.children[rowsEl.children.length - 1];
        var prevPlus = prevRow ? prevRow.querySelector('.ce-scond-add-btn') : null;
        if (prevPlus) prevPlus.style.display = 'none';
    }

    rowsEl.appendChild(row);
};

function ceReplacePlaceholders(html) {
    if (!html) return '<em style="color:#d1d5db;">Empty section</em>';
    return html.replace(/\{(\w+)\}/g, function (_, token) {
        var numId = token.replace(/^QID/i, '').replace(/\D/g, '');
        if (!numId) return '{' + token + '}';
        var q   = CE.questions.find(function (q) { return String(q.id) === numId; });
        var qid = q ? q.id : numId;
        return '<span class="ce-var ce-var-qid"'
            + ' onclick="ceScrollToQuestion(\'' + qid + '\')"'
            + ' title="' + (q ? esc(q.label) : 'Q' + qid) + '"'
            + ' style="display:inline-flex;align-items:center;gap:3px;cursor:pointer;background:#fff4f0;color:#e85d2f;border-radius:4px;padding:1px 6px;font-size:11px;font-weight:700;font-family:monospace;"'
            + ' onmouseover="this.style.background=\'#fdd0bb\'"'
            + ' onmouseout="this.style.background=\'#fff4f0\'">'
            + 'Q' + esc(String(qid))
            + '</span>';
    });
}

   function ceRenderInlineField(q, ri) {
    var optionGoTos = {};
    if (q.condGoTo && q.condGoTo.length > 0) {
        q.condGoTo.forEach(function(cg) {
            if (cg.conditions && cg.conditions.length === 1) {
                var c = cg.conditions[0];
                if (String(c.qid) === String(q.id) && (c.type === 'is_equal_to' || c.type === '')) {
                    optionGoTos[c.value] = cg.goto;
                }
            }
        });
    }

    var fStyle = 'width:100%;padding:6px 9px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:11.5px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;';
    var fFocus = 'onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';
    var ph     = q.placeholder || '';

    var typeLabel = (q.type || 'textbox').replace(/-/g,' ').replace(/\b\w/g, function(c){ return c.toUpperCase(); });

    var prefix = '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">'
        + '<div style="font-size:10px;font-weight:600;color:#9ca3af;font-family:monospace;">' + esc(typeLabel) + '</div>'
        + '<button type="button" class="ce-icon-btn new-QuestionMark_des" onmouseenter="ceShowQPreview(event,' + ri + ')" onmouseleave="ceHideQPreview()">'
        + '<svg width="12" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">'
        + '<circle cx="12" cy="12" r="10"/>'
        + '<path d="M9.5 9a2.5 2.5 0 1 1 4.5 1.5c-.7 1-2 1.5-2 3"/>'
        + '<circle cx="12" cy="17.5" r="0.8" fill="currentColor" stroke="none"/>'
        + '</svg>'
        + '</button>'
        + '</div>';

    switch (q.type) {
        case 'dropdown':
        case 'select':
            return prefix + '<select style="' + fStyle + 'cursor:pointer;" ' + fFocus + '>'
                + '<option value="" style="color:#9ca3af;">— Select an option —</option>'
                + (q.options || []).map(function(o) {
                    var val = o.value || o.label;
                    var gotoLabel = '';
                    if (optionGoTos[val]) {
                        var dest = optionGoTos[val] === 'END' ? 'Checkout' : 'Q' + optionGoTos[val];
                        gotoLabel = ' \u2192 ' + dest;
                    }
                    return '<option value="' + esc(val) + '">' + esc(o.label) + gotoLabel + '</option>';
                }).join('')
                + '</select>';

        case 'radio-button':
        case 'radio':
            if (!(q.options || []).length) {
                return prefix + '<span style="font-size:11px;color:#d1d5db;font-style:italic;">No options defined</span>';
            }
            return prefix + '<div style="display:flex;flex-direction:column;gap:5px;">'
                + (q.options || []).map(function(o) {
                    var val = o.value || o.label;
                    var gotoBadge = '';
                    if (optionGoTos[val]) {
                        var dest = optionGoTos[val] === 'END' ? 'Checkout' : 'Q' + optionGoTos[val];
                        var clickHandler = optionGoTos[val] === 'END'
                            ? ''
                            : ' onclick="event.preventDefault();ceScrollToQuestion(\'' + optionGoTos[val] + '\')"';
                        gotoBadge = '<span style="background:#e6f4ea;color:#1e8e3e;font-size:10px;padding:2px 4px;border-radius:3px;margin-left:6px;cursor:pointer;"'
                            + clickHandler + '>\u2192 ' + dest + '</span>';
                    }
                    return '<label style="display:flex;align-items:center;gap:6px;font-size:11.5px;color:#374151;cursor:pointer;">'
                        + '<input type="radio" name="ceq_radio_' + q.id + '" style="accent-color:#e85d2f;width:13px;height:13px;flex-shrink:0;cursor:pointer;">'
                        + esc(o.label)
                        + gotoBadge
                        + '</label>';
                }).join('')
                + '</div>';

        case 'checkbox':
            if (!(q.options || []).length) {
                return prefix + '<label style="display:flex;align-items:center;gap:6px;font-size:11.5px;color:#374151;cursor:pointer;">'
                    + '<input type="checkbox" style="accent-color:#e85d2f;width:13px;height:13px;cursor:pointer;">'
                    + esc(q.label || 'Checkbox')
                    + '</label>';
            }
            return prefix + '<div style="display:flex;flex-direction:column;gap:5px;">'
                + (q.options || []).map(function(o) {
                    return '<label style="display:flex;align-items:center;gap:6px;font-size:11.5px;color:#374151;cursor:pointer;">'
                        + '<input type="checkbox" style="accent-color:#e85d2f;width:13px;height:13px;flex-shrink:0;cursor:pointer;">'
                        + esc(o.label)
                        + '</label>';
                }).join('')
                + '</div>';

        case 'date':
            return prefix + '<input type="date" style="' + fStyle + '" ' + fFocus + '>';

        case 'textarea':
            return prefix + '<textarea rows="2" placeholder="' + esc(ph || 'Long answer text…') + '" style="' + fStyle + 'resize:vertical;" ' + fFocus + '></textarea>';

        case 'number':
            return prefix + '<input type="number" placeholder="' + esc(ph || '0') + '" style="' + fStyle + '" ' + fFocus + '>';

        // case 'email':
        //     return prefix + '<input type="email" placeholder="' + esc(ph || 'you@example.com') + '" style="' + fStyle + '" ' + fFocus + '>';

        // case 'phone':
        //     return prefix + '<input type="tel" placeholder="' + esc(ph || '+1 (555) 000-0000') + '" style="' + fStyle + '" ' + fFocus + '>';

case 'dropdown-link':
    return prefix + '<div style="font-size:11px;color:#6b7280;padding:4px 0;">'
        + '<div style="margin-bottom:4px;"><span style="font-weight:600;">Same link:</span> '
        + esc(q.sameContractLink || '—') + '</div>'
        + '<div style="font-weight:600;margin-bottom:3px;">Different links:</div>'
        + (q.dropdownLinks && q.dropdownLinks.length
            ? q.dropdownLinks.map(function(dl) {
                return '<div style="display:flex;gap:6px;margin-bottom:2px;">'
                    + '<span style="background:#f3f4f6;border-radius:3px;padding:1px 6px;font-size:10px;">' + esc(dl.label) + '</span>'
                    + '<span style="color:#9ca3af;font-size:10px;">' + esc(dl.link || '—') + '</span>'
                    + '</div>';
              }).join('')
            : '<span style="color:#d1d5db;font-style:italic;">No links defined</span>')
        + '</div>';

        default:
            return prefix + '<input type="text" placeholder="' + esc(ph || 'Short answer text…') + '" style="' + fStyle + '" ' + fFocus + '>';
    }
}

CE._highlightedQid = null;
CE._highlightTimer = null;
window.ceScrollToQuestion = function (qid) {
    var idx = -1;
    for (var i = 0; i < CE.questions.length; i++) {
        if (String(CE.questions[i].id) === String(qid)) { idx = i; break; }
    }
    if (idx === -1) return;

    var list = document.getElementById('ceQuestionsList');
    var card = document.getElementById('ce-qcard-' + idx);
    if (!card) return;

    list.querySelectorAll('.ce-qcard').forEach(function(c) {
        if (c._ceHighlightTimer) { clearTimeout(c._ceHighlightTimer); c._ceHighlightTimer = null; }
        if (c._ceScrollListener) { list.removeEventListener('scroll', c._ceScrollListener); c._ceScrollListener = null; }
        c.style.transition  = '';
        c.style.boxShadow   = '';
        c.style.borderColor = '';
    });

    card.style.transition  = 'box-shadow .2s, border-color .2s';
    card.style.boxShadow   = '0 0 0 3px #e85d2f55';
    card.style.borderColor = '#e85d2f';

    card.scrollIntoView({ behavior: 'smooth', block: 'center' });

    var _removeHighlight = function () {
        card.style.boxShadow   = '';
        card.style.borderColor = '';
        card._ceHighlightTimer = null;
        if (card._ceScrollListener) {
            list.removeEventListener('scroll', card._ceScrollListener);
            card._ceScrollListener = null;
        }
    };

    if (card._ceHighlightTimer) clearTimeout(card._ceHighlightTimer);
    card._ceHighlightTimer = setTimeout(_removeHighlight, 30000);
    card._ceScrollListener = function () { _removeHighlight(); };
    setTimeout(function () {
        list.addEventListener('scroll', card._ceScrollListener, { once: true });
    }, 800);
};

    window.ceFilterQuestions = function (v) { ceRenderQ(v); };

    window.ceFilterSectionsText = function(v) {
    var lc = v.toLowerCase();
    if (!lc) {
        CE.sections = CE.sectionsFull.slice();
    } else {
        CE.sections = CE.sectionsFull.filter(function(s) {
            return (s.content || '').toLowerCase().includes(lc)
                || (s.section_name || '').toLowerCase().includes(lc)
                || (s.section_key || '').toLowerCase().includes(lc)
                || (s.stdClauseTitle || '').toLowerCase().includes(lc);
        });
    }
    ceRenderPreview();
};

    window.ceFilterSections  = function (t) { ceRenderPreview(t || null); };

   window.ceShowQPreview = function (e, idx) {
    var q = CE.questions[idx];
    if (!q) return;

    var pop      = document.getElementById('ceQPreviewPopup');
    var badgeEl  = document.getElementById('ceQPreviewTypeBadge');
    var labelEl  = document.getElementById('ceQPreviewLabel');
    var fieldEl  = document.getElementById('ceQPreviewField');
    var infoWrap = document.getElementById('ceQPreviewInfo');
    var infoTxt  = document.getElementById('ceQPreviewInfoText');

    if (q.info) {
        infoTxt.textContent    = q.info;
        infoWrap.style.display = 'flex';
    } else {
        infoWrap.style.display = 'none';
    }

    pop.style.display = 'block';
    var rect = e.currentTarget.getBoundingClientRect();
    var popW = pop.offsetWidth  || 300;
    var popH = pop.offsetHeight || 180;
    var left = rect.right + 10;
    var top  = rect.top   - 10;
    if (left + popW > window.innerWidth  - 12) left = rect.left - popW - 10;
    if (top  + popH > window.innerHeight - 12) top  = window.innerHeight - popH - 12;
    if (top < 8) top = 8;
    pop.style.left = left + 'px';
    pop.style.top  = top  + 'px';
};
    
    window.ceHideQPreview = function () {
        document.getElementById('ceQPreviewPopup').style.display = 'none';
    };

window.ceCopyQ = function (ri) {
    var q = CE.questions[ri];
    if (!q) return;
    CE._clipboard = JSON.parse(JSON.stringify(q));
    var text = q.label || '';
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            ceSetStatus('Copied to clipboard');
            setTimeout(function() { ceSetStatus(''); }, 2000);
        });
    } else {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity  = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        ceSetStatus('Copied');
        setTimeout(function() { ceSetStatus(''); }, 2000);
    }
};

window.cePasteQ = function (ri) {
    if (CE._bulkClipboard && CE._bulkClipboard.length) {
        var insertAt = ri + 1;
        CE._bulkClipboard.forEach(function(q, i) {
            var copy    = JSON.parse(JSON.stringify(q));
            // copy.id     = 'new_' + Date.now() + '_' + i;
            // copy.isNew  = true;
            copy.usedIn = 0;
            CE.questions.splice(insertAt + i, 0, copy);
        });
        ceRenderQ();
        ceSetStatus('Unsaved changes');
        return;
    }
    if (!CE._clipboard) {
        ceSetStatus('Nothing copied yet');
        return;
    }
    var copy    = JSON.parse(JSON.stringify(CE._clipboard));
    copy.usedIn = 0;
    CE.questions.splice(ri + 1, 0, copy);
    ceRenderQ();
    ceSetStatus('Unsaved changes');
};

window.cePasteS = function (idx) {
    if (CE._bulkSClipboard && CE._bulkSClipboard.length) {
        CE._bulkSClipboard.forEach(function(s, i) {
            var copy = JSON.parse(JSON.stringify(s));
            CE.sections.splice(idx + 1 + i, 0, copy);
        });
        CE.sectionsFull = CE.sections.slice();
        ceRenderPreview();
        ceSetStatus('Unsaved changes');
        return;
    }
    if (!CE._sclipboard) { ceSetStatus('Nothing copied yet'); return; }
    var copy = JSON.parse(JSON.stringify(CE._sclipboard));
    CE.sections.splice(idx + 1, 0, copy);
    CE.sectionsFull = CE.sections.slice();
    ceRenderPreview();
    ceSetStatus('Unsaved changes');
};


window.ceInsertQAfter = function (ri) {
    var newQ = {
        id          : 'new_' + Date.now(),
        type        : 'textbox',
        label       : '',
        info        : '',
        placeholder : '',
        required    : 1,
        section     : '',
        goTo        : null,
        usedIn      : 0,
        options     : [],
        conditions  : [],
        condGoTo    : [],
        condGoToStep: null,
        isNew       : true,
    };
    CE.questions.splice(ri + 1, 0, newQ);
    ceRenderQ();
    ceSetStatus('Unsaved changes');
    setTimeout(function() { ceEditQ(ri + 1); }, 50);
};

function ceInitSortable() {
    var list = document.getElementById('ceQuestionsList');
    if (!list || !window.Sortable) return;

    if (window._ceSortableInstance) {
        window._ceSortableInstance.destroy();
    }

    window._ceSortableInstance = new Sortable(list, {
        animation    : 150,
        handle       : '.ce-qcard-drag',
        ghostClass   : 'sortable-ghost',
        chosenClass  : 'sortable-chosen',
        dragClass    : 'sortable-drag',
        onEnd: function (evt) {
            var oldIdx = evt.oldIndex;
            var newIdx = evt.newIndex;
            if (oldIdx === newIdx) return;

            var moved = CE.questions.splice(oldIdx, 1)[0];
            CE.questions.splice(newIdx, 0, moved);

            var scrollTop = list.scrollTop;
            ceRenderQ();
            list.scrollTop = scrollTop;

            ceSetStatus('Unsaved changes');
        },
    });
}

window.ceCopyS = function (idx) {
    var s = CE.sections[idx];
    if (!s) return;
    CE._sclipboard = JSON.parse(JSON.stringify(s));
    var text = s.content || '';
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            ceSetStatus('Section content copied');
            setTimeout(function() { ceSetStatus(''); }, 2000);
        });
    } else {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.cssText = 'position:fixed;opacity:0;';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        ceSetStatus('Copied');
        setTimeout(function() { ceSetStatus(''); }, 2000);
    }
};

window.ceInsertSAfter = function (idx) {
    CE._stdClauseInsertAfterIdx = idx; 
    var newS = {
        id                  : 'new_' + Date.now(),
        type                : 'content',
        content             : '',
        section_key         : '',
        section_name        : '',
        text_align          : 'left',
        secure_blur_content : 0,
        isNew               : true,
    };
    CE.sections.splice(idx + 1, 0, newS);
    CE.sectionsFull = CE.sections.slice();
    ceRenderPreview();
    ceSetStatus('Unsaved changes');
    setTimeout(function() { ceEditS(idx + 1); }, 50);
};

function ceInitSectionSortable() {
    var list = document.getElementById('ceContractPreview');
    if (!list || !window.Sortable) return;

    if (window._ceSectionSortableInstance) {
        window._ceSectionSortableInstance.destroy();
    }

    window._ceSectionSortableInstance = new Sortable(list, {
        animation   : 150,
        handle      : '.ce-sblock-drag',
        ghostClass  : 'sortable-ghost',
        chosenClass : 'sortable-chosen',
        onEnd: function (evt) {
            var oldIdx = evt.oldIndex;
            var newIdx = evt.newIndex;
            if (oldIdx === newIdx) return;

            var moved = CE.sections.splice(oldIdx, 1)[0];
            CE.sections.splice(newIdx, 0, moved);
            CE.sectionsFull = CE.sections.slice();

            var scrollTop = list.scrollTop;
            ceRenderPreview();
            list.scrollTop = scrollTop;

            ceSetStatus('Unsaved changes');
        },
    });
}

window.ceQModalType = function(ri, newType) {
    var q = CE.questions[ri];
    if (!q) return;
    q.type = newType;
    ceRenderQ();
    ceRenderPreview();
    ceSetStatus('Unsaved changes');
};

    window.ceAddNewQuestion = function () {
    CE.editingQuestionIdx = null;
    document.getElementById('ceQModalId').value              = '';
    document.getElementById('ceQModalLabel').value           = '';
    document.getElementById('ceQModalPlaceholder').value     = '';
    document.getElementById('ceQModalSameContractLink').value = '';
    document.getElementById('ceQModalDropdownLinkRows').innerHTML = '';
    var wrap = document.getElementById('ceQModalLabelWrap');
    if (wrap) wrap.style.display = '';
    document.getElementById('ceQModalInfo').value            = '';
    document.getElementById('ceQModalType').value            = 'textbox';
    document.getElementById('ceQModalOptionsList').innerHTML = '';
    var badge = document.getElementById('ceQModalQidBadge');
    badge.style.display = 'none';
    badge.textContent   = '';
    document.getElementById('ceQModalType').style.display = 'inline-block';
    cePopGoToDropdown(null);
    ceQModalTypeChange();
    document.getElementById('ceQModalCondRows').innerHTML = '';
    document.getElementById('ceCondGroupsContainer').innerHTML = '';
    document.getElementById('ceQuestionModal').classList.add('open');
};

//  select all questions button
    window.ceToggleSelectAll = function(masterCb) {
    var checkboxes = document.querySelectorAll('.ce-qcard-checkbox');
    CE._selectedIndices.clear();
    checkboxes.forEach(function(cb) {
        cb.checked = masterCb.checked;
        var ri = parseInt(cb.getAttribute('data-ri'));
        var card = document.getElementById('ce-qcard-' + ri);
        if (masterCb.checked) {
            CE._selectedIndices.add(ri);
            if (card) card.classList.add('ce-selected');
        } else {
            if (card) card.classList.remove('ce-selected');
        }
    });
    ceUpdateBulkBar();
};

    window.ceEditQ = function (idx) {
        var q = CE.questions[idx];
        if (!q) return;
        CE.editingQuestionIdx = idx;
        document.getElementById('ceQModalId').value              = q.id;
        document.getElementById('ceQModalLabel').value           = q.label   || '';

         var wrap = document.getElementById('ceQModalLabelWrap');
        if (wrap) wrap.style.display = '';

        document.getElementById('ceQModalInfo').value            = q.info    || '';
        document.getElementById('ceQModalType').value            = q.type    || 'textbox' ;
        document.getElementById('ceQModalOptionsList').innerHTML = '';
        document.getElementById('ceQModalPlaceholder').value      = q.placeholder || '';
        document.getElementById('ceQModalSameContractLink').value = q.sameContractLink || '';
        document.getElementById('ceQModalDropdownLinkRows').innerHTML = '';
        (q.dropdownLinks || []).forEach(function(dl) {
            ceAddDropdownLinkRow(dl.label, dl.link);
        });
        if (q.type === 'dropdown-link' && !(q.dropdownLinks || []).length) {
            ceAddDropdownLinkRow();
        }
        // (q.options || []).forEach(function (o) { ceAppendOpt(o.label, o.value); });
        (q.options || []).forEach(function (o) {
        var gotoVal = '';
        (q.condGoTo || []).forEach(function(cg) {
            if (cg.conditions && cg.conditions.length === 1) {
                var c = cg.conditions[0];
            if ((c.type === 'is_equal_to' || c.type === '') && c.value === (o.value || o.label)) {
                gotoVal = cg.goto || '';
            }
        }
    });
    ceAppendOpt(o.label, o.value, gotoVal);
});

        var badge = document.getElementById('ceQModalQidBadge');
        if (!q.isNew) {
            badge.textContent   = 'Q' + q.id;
            badge.style.display = 'inline-block';
            document.getElementById('ceQModalType').style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }

        cePopGoToDropdown(q.goTo || null);
        ceQModalTypeChange();
        cePopGoToDropdown(q.goTo || null, q.condGoToStep || null);

      document.getElementById('ceQModalCondRows').innerHTML = '';
        document.getElementById('ceCondGroupsContainer').innerHTML = '';
        var qType = q.type || 'textbox';
        var isOptType = (qType === 'radio-button' || qType === 'dropdown' || qType === 'select');
        (q.condGoTo || []).forEach(function(grp) {
            // Skip simple single-condition option goTos for radio/dropdown
            // These are already rendered via ceAppendOpt, not as condition groups
            if (isOptType
                && grp.conditions
                && grp.conditions.length === 1
                && String(grp.conditions[0].qid) === String(q.id)
                && (grp.conditions[0].type === 'is_equal_to' || grp.conditions[0].type === '')) {
                return; // skip — already shown in options list
            }
            ceAddCondGroup();
            var lastGi = document.querySelectorAll('#ceCondGroupsContainer [id^=ceCondGroup_]').length - 1;
            var grpEl = document.getElementById('ceCondGroup_' + lastGi);
            if (!grpEl) return;
            var rowsEl = grpEl.querySelector('.ce-cond-rows-' + lastGi);
            if (rowsEl) rowsEl.innerHTML = '';
            var conditions = grp.conditions || [grp];
            conditions.forEach(function() { ceAddSubCond(lastGi); });
            setTimeout(function() {
                var qids  = grpEl.querySelectorAll('.ce-sub-qid');
                var conds = grpEl.querySelectorAll('.ce-sub-cond');
                var vals  = grpEl.querySelectorAll('.ce-sub-val');
                conditions.forEach(function(c, ci) {
                    if (qids[ci])  qids[ci].value  = c.qid   || '';
                    if (conds[ci]) conds[ci].value = c.type  || '';
                    if (vals[ci])  vals[ci].value  = c.value || '';
                });
                var gotoSel = grpEl.querySelector('.ce-cond-goto-sel');
                if (gotoSel) gotoSel.value = grp.goto || '';
            }, 0);
        });
        (q.conditions || []).forEach(function() { ceAddConditionFromModal(); });
        setTimeout(function() {
            var rows = document.querySelectorAll('#ceQModalCondRows [id^=ceModalCondRow_]');
            (q.conditions || []).forEach(function(cond, ci) {
                if (!rows[ci]) return;
                var lbl = rows[ci].querySelector('.ce-modal-cond-label');
                var qid = rows[ci].querySelector('.ce-modal-cond-qid');
                var val = rows[ci].querySelector('.ce-modal-cond-value');
                if (lbl) lbl.value = cond.label || '';
                if (qid) qid.value = cond.qid  || '';
                if (val) val.value = cond.value || '';
            });
        }, 0);
        document.getElementById('ceQuestionModal').classList.add('open');
    };
               (function(){
                var _usageScrollState = {};

                window.ceScrollToUsages = function(qid, badgeEl) {
                    var preview = document.getElementById('ceContractPreview');
                    if (!preview) return;

                    var allBlocks = preview.querySelectorAll('.ce-sblock');
                    var matches = [];
                    allBlocks.forEach(function(block) {
                        var tokens = block.querySelectorAll('.ce-var.ce-var-qid');
                        var found = false;
                        tokens.forEach(function(tok) {
                            if (!found && tok.onclick && tok.getAttribute('onclick') &&
                                tok.getAttribute('onclick').includes("'" + qid + "'")) {
                                found = true;
                            }
                        });
                        if (!found) {
                            var idx = parseInt(block.getAttribute('id').replace('ce-sblock-',''));
                            var s = window.__CE && window.__CE.sections[idx];
                            if (s && s.content && s.content.includes('{QID' + qid + '}')) {
                                found = true;
                            }
                        }
                        if (found) matches.push(block);
                    });

                    if (!matches.length) {
                        ceSetStatus('Q' + qid + ' not used in any section');
                        setTimeout(function(){ ceSetStatus(''); }, 2000);
                        return;
                    }

                    var state = _usageScrollState[qid] || { idx: 0 };
                    var target = matches[state.idx % matches.length];

                    // Clear all previous highlights
                    preview.querySelectorAll('.ce-sblock.ce-usage-highlight').forEach(function(b){
                        b.classList.remove('ce-usage-highlight');
                        // Clean up any lingering timers/listeners attached to previously highlighted blocks
                        if (b._ceUsageTimer) {
                            clearTimeout(b._ceUsageTimer);
                            b._ceUsageTimer = null;
                        }
                        if (b._ceUsageScrollHandler) {
                            preview.removeEventListener('scroll', b._ceUsageScrollHandler);
                            b._ceUsageScrollHandler = null;
                        }
                    });

                    target.classList.add('ce-usage-highlight');
                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    var current = (state.idx % matches.length) + 1;
                    ceSetStatus('Q' + qid + ' — usage ' + current + ' of ' + matches.length);
                    setTimeout(function(){ ceSetStatus(''); }, 2500);

                    state.idx = (state.idx + 1) % matches.length;
                    _usageScrollState[qid] = state;

                    // Auto-clear after 30 seconds
                    target._ceUsageTimer = setTimeout(function() {
                        target.classList.remove('ce-usage-highlight');
                        target._ceUsageTimer = null;
                        if (target._ceUsageScrollHandler) {
                            preview.removeEventListener('scroll', target._ceUsageScrollHandler);
                            target._ceUsageScrollHandler = null;
                        }
                    }, 20000);

                target._ceUsageScrollHandler = function() {
                        target.classList.remove('ce-usage-highlight');
                        if (target._ceUsageTimer) {
                            clearTimeout(target._ceUsageTimer);
                            target._ceUsageTimer = null;
                        }
                        preview.removeEventListener('scroll', target._ceUsageScrollHandler);
                        target._ceUsageScrollHandler = null;
                    };
                    setTimeout(function() {
                        if (target._ceUsageScrollHandler) {
                            preview.addEventListener('scroll', target._ceUsageScrollHandler, { once: true });
                        }
                    }, 1000);
                
                };
            }());

    window.ceDelQ = function (idx) {
        if (!confirm('Delete this question? It will also be removed from contract sections that reference it.')) return;
        var q = CE.questions[idx];
        if (q && !q.isNew) CE.deletedQuestionIds.push(q.id);
        CE.questions.splice(idx, 1);
        ceRenderQ();
        ceRenderPreview();
        ceSetStatus('Unsaved changes');
    };

    window.ceQModalTypeChange = function () {
    var t = document.getElementById('ceQModalType').value;

    var isOptType = (t === 'dropdown' || t === 'radio-button');
    document.getElementById('ceQModalOptionsWrap').style.display = isOptType ? 'block' : 'none';
    
    // var addOptBtn = document.getElementById('ceQModalAddOptionBtn');
    // if (addOptBtn) addOptBtn.style.display = isOptType ? 'inline-flex' : 'none';
    var addOptBtn = document.getElementById('ceQModalAddOptionBtn');
    if (addOptBtn) {
        if (isOptType) {
            var optList = document.getElementById('ceQModalOptionsList');
            var hasOptions = optList && optList.children.length > 0;
            addOptBtn.style.display = hasOptions ? 'none' : 'inline-flex';
        } else {
            addOptBtn.style.display = 'none';
        }
    }
    
    var lbl = document.getElementById('ceQModalOptionsLabel');
    if (lbl) lbl.textContent = (t === 'dropdown') ? 'Add Dropdown Option' : 'Add Radio Option';
    
    var goToWrap = document.querySelector('.goto_wid_inner');
    if (goToWrap) goToWrap.style.display = isOptType ? 'none' : '';

    // Placeholder field
    var phWrap  = document.getElementById('ceQModalPlaceholderWrap');
    var phLabel = document.getElementById('ceQModalPlaceholderLabel');
    var showPh  = (t === 'textbox' || t === 'textarea' || t === 'number' || t === 'number-field'
                || t === 'price-box' || t === 'percentage-box' || t === 'email' || t === 'phone');
    if (phWrap) {
        phWrap.style.display = showPh ? 'block' : 'none';
        if (phLabel) {
            var labelMap = {
                'textbox'        : 'TEXT BOX PLACEHOLDER',
                'textarea'       : 'TEXT AREA PLACEHOLDER',
                'number'         : 'NUMBER FIELD PLACEHOLDER',
                'number-field'   : 'NUMBER FIELD PLACEHOLDER',
                'price-box'      : 'PRICE BOX PLACEHOLDER',
                'percentage-box' : 'PERCENTAGE BOX PLACEHOLDER',
                'email'          : 'EMAIL PLACEHOLDER',
                'phone'          : 'PHONE PLACEHOLDER',
            };
            phLabel.textContent = labelMap[t] || 'PLACEHOLDER';
        }
    }

    // Dropdown link fields
    var dlWrap = document.getElementById('ceQModalDropdownLinkWrap');
    if (dlWrap) dlWrap.style.display = (t === 'dropdown-link') ? 'block' : 'none';
};


window.ceAddDropdownLinkRow = function (label, link) {
    var container = document.getElementById('ceQModalDropdownLinkRows');
    if (!container) return;

    var iStyle = 'padding:7px 9px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:12px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;width:100%;';
    var iFocus = 'onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';

    var row = document.createElement('div');
    row.style.cssText = 'display:flex;gap:8px;align-items:flex-end;margin-bottom:10px;';
    row.innerHTML =
        '<div style="flex:1;position:relative;padding-top:12px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Label</span>'
        + '<input type="text" class="ce-dl-label" style="' + iStyle + '" ' + iFocus + ' placeholder="Label" value="' + esc(label || '') + '">'
        + '</div>'
        + '<div style="flex:1;position:relative;padding-top:12px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Contract Link</span>'
        + '<input type="text" class="ce-dl-link" style="' + iStyle + '" ' + iFocus + ' placeholder="Contract link URL" value="' + esc(link || '') + '">'
        + '</div>'
        + '<button type="button" onclick="this.closest(\'div[style*=margin-bottom]\').remove()"'
        + ' style="flex-shrink:0;width:28px;height:28px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;margin-bottom:1px;"'
        + ' onmouseover="this.style.background=\'#fde8e8\';this.style.color=\'#dc2626\'" onmouseout="this.style.background=\'#f1f5f9\';this.style.color=\'#94a3b8\'">'
        // + '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>'
          + '<i class="fa fa-trash" style="font-size:10px;"></i>'
        + '</button>'
        + '<button type="button" onclick="ceAddDropdownLinkRow()"'
        + ' style="flex-shrink:0;width:28px;height:28px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#374151;margin-bottom:1px;"'
        + ' onmouseover="this.style.background=\'#e85d2f\';this.style.color=\'#fff\'" onmouseout="this.style.background=\'#f3f4f6\';this.style.color=\'#374151\'">'
        + '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>'
        + '</button>';

    container.appendChild(row);
};

window.ceAddConditionFromModal = function () {
    var container = document.getElementById('ceQModalCondRows');
    if (!container) return;

    var wrap = document.getElementById('ceQModalLabelWrap');
    if (wrap) wrap.style.display = 'none';

    var ci     = container.children.length;
    var iStyle = 'padding:7px 9px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:12px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;width:100%;';
    var iFocus = 'onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';

    var qidOpts = '<option value="">— QID —</option>';
    CE.questions.forEach(function(qq) {
        qidOpts += '<option value="' + esc(qq.id) + '">QID' + esc(qq.id) + '</option>';
    });

    var row = document.createElement('div');
    row.style.cssText = 'display:flex;gap:5px;align-items:flex-end;margin-bottom:10px;padding-top:10px;';
    row.id = 'ceModalCondRow_' + ci;

    var initLabel = '';
    if (ci === 0) {
        var mainLbl = document.getElementById('ceQModalLabel');
        if (mainLbl) initLabel = mainLbl.value.trim();
    }

    row.innerHTML =
        '<div style="flex:2;position:relative;padding-top:10px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Question Label</span>'
        + '<input type="text" class="ce-modal-cond-label" style="' + iStyle + '" ' + iFocus + ' placeholder="Label" value="' + esc(initLabel) + '">'
        + '</div>'
        + '<div style="flex:1.2;position:relative;padding-top:10px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Question ID</span>'
        + '<select class="ce-modal-cond-qid" style="' + iStyle + 'cursor:pointer;" ' + iFocus + '>' + qidOpts + '</select>'
        + '</div>'
        + '<div style="flex:1.2;position:relative;padding-top:10px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Value</span>'
        + '<input type="text" class="ce-modal-cond-value" style="' + iStyle + '" ' + iFocus + ' placeholder="Value">'
        + '</div>'
        + '<button type="button" onclick="ceRemoveConditionFromModal(this)"'
        + ' style="flex-shrink:0;width:28px;height:28px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:5px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;"'
        + ' onmouseover="this.style.background=\'#fde8e8\';this.style.color=\'#dc2626\'" onmouseout="this.style.background=\'#f1f5f9\';this.style.color=\'#94a3b8\'">'
        + '<i class="fa fa-trash" style="font-size:11px;"></i>'
        + '</button>'
        + '<button type="button" onclick="ceAddConditionFromModal()" class="ce-add-cond-btn"'
        + ' style="flex-shrink:0;width:28px;height:28px;background:#e85d2f;border:none;border-radius:5px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#fff;"'
        + ' onmouseover="this.style.background=\'#c94d23\'" onmouseout="this.style.background=\'#e85d2f\'">'
        + '<i class="fa fa-plus" style="font-size:11px;"></i>'
        + '</button>';

    if (ci > 0) {
        var prev = container.children[ci - 1];
        var prevPlus = prev ? prev.querySelector('.ce-add-cond-btn') : null;
        if (prevPlus) prevPlus.style.display = 'none';
    }

    container.appendChild(row);
};
window.ceRemoveConditionFromModal = function(btn) {
    var row = btn.closest('[id^=ceModalCondRow_]');
    var lblInput = row ? row.querySelector('.ce-modal-cond-label') : null;
    var lastVal = lblInput ? lblInput.value.trim() : '';

    if (row) row.remove();
    
    var container = document.getElementById('ceQModalCondRows');
    if (!container.children.length) {
        var wrap = document.getElementById('ceQModalLabelWrap');
        if (wrap) wrap.style.display = '';
        var mainLbl = document.getElementById('ceQModalLabel');
        if (mainLbl && lastVal) {
            mainLbl.value = lastVal;
        }
    } else {
        var lastRow = container.lastElementChild;
        if (lastRow) {
            var plusBtn = lastRow.querySelector('.ce-add-cond-btn');
            if (plusBtn) plusBtn.style.display = 'flex';
        }
    }
};


    // window.ceAddQModalOption = function () { ceAppendOpt('', ''); };
    window.ceAddQModalOption = function () { 
        ceAppendOpt('', '', ''); 
        };


window.ceAddCondGroup = function () {
    var container = document.getElementById('ceCondGroupsContainer');
    var gi = container.children.length;

    var iStyle = 'padding:7px 9px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:12px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;width:100%;';
    var iFocus = 'onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';

    var grp = document.createElement('div');
    grp.id = 'ceCondGroup_' + gi;
    grp.style.cssText = 'border:1px solid #e5e7eb;border-radius:8px;padding:14px 14px 10px;background:#f9fafb;margin-bottom:12px;';

    grp.innerHTML =
        '<div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Conditions</div>'
        + '<div class="ce-cond-rows-' + gi + '"></div>'
        + '<div style="display:flex;align-items:center;gap:8px;margin:8px 0;">'
        // +   '<button type="button" onclick="ceAddSubCond(' + gi + ')" '
        // +   'style="width:26px;height:26px;background:#e85d2f;border:none;border-radius:50%;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0;"'
        // +   ' onmouseover="this.style.background=\'#c94d23\'" onmouseout="this.style.background=\'#e85d2f\'">'
        // +   '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>'
        // +   '</button>'
        + '</div>'
        + '<div style="position:relative;padding-top:12px;margin-bottom:6px;">'
        +   '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#f9fafb;padding:0 3px;font-weight:600;z-index:1;">Conditional Go to Step</span>'
        +   '<select class="ce-cond-goto-sel" style="' + iStyle + '" ' + iFocus + '>'
        +     '<option value="">— None —</option>'
        +   '</select>'
        + '</div>'
        + '<div style="display:flex;justify-content:flex-end;margin-top:6px;">'
        +   '<button type="button" onclick="this.closest(\'[id^=ceCondGroup_]\').remove()" '
        +   'style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:11px;display:flex;align-items:center;gap:4px;" '
        +   'onmouseover="this.style.color=\'#dc2626\'" onmouseout="this.style.color=\'#9ca3af\'">'
        +   '<i class="fa fa-trash" style="font-size:10px;"></i> Remove Condition'
        +   '</button>'
        + '</div>';

    container.appendChild(grp);

    var sel = grp.querySelector('.ce-cond-goto-sel');
    CE.questions.forEach(function(q) {
        var o = document.createElement('option');
        o.value = q.id;
        o.textContent = 'QID' + q.id + (q.label ? ' — ' + q.label.substring(0,30) : '');
        sel.appendChild(o);
    });
    var endO = document.createElement('option');
    endO.value = 'END'; endO.textContent = 'Checkout';
    sel.appendChild(endO);

    ceAddSubCond(gi);
};
             
window.ceAddSubCond = function (gi) {
    var rowsEl = document.querySelector('.ce-cond-rows-' + gi);
    if (!rowsEl) return;

    var iStyle = 'padding:7px 9px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:12px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;width:100%;';
    var iFocus = 'onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';

    var qidOpts = '<option value="">Question ID</option>';
    CE.questions.forEach(function(qq) {
        qidOpts += '<option value="' + esc(qq.id) + '">' + esc(qq.id) + '</option>';
    });

    var condOpts = '<option value="">Condition</option>'
        + '<option value="is_equal_to">is equal to</option>'
        + '<option value="is_not_equal_to">is not equal to</option>'
        + '<option value="is_less_than">is less than</option>'
        + '<option value="is_greater_than">is greater than</option>';

    var row = document.createElement('div');
    row.style.cssText = 'display:flex;gap:6px;align-items:center;margin-bottom:8px;';
    row.innerHTML =
        '<div style="flex:1.2;position:relative;padding-top:10px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Question ID</span>'
        + '<select class="ce-sub-qid" style="' + iStyle + 'cursor:pointer;" ' + iFocus + '>' + qidOpts + '</select>'
        + '</div>'
        + '<div style="flex:1.4;position:relative;padding-top:10px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Condition</span>'
        + '<select class="ce-sub-cond" style="' + iStyle + 'cursor:pointer;" ' + iFocus + '>' + condOpts + '</select>'
        + '</div>'
        + '<div style="flex:1;position:relative;padding-top:10px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Value</span>'
        + '<input type="text" class="ce-sub-val" style="' + iStyle + '" ' + iFocus + ' placeholder="Value">'
        + '</div>'
        + '<button type="button" onclick="this.closest(\'div[style*=margin-bottom]\').remove()" '
        + 'style="flex-shrink:0;width:26px;height:26px;margin-top:10px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;">'
        + '<i class="fa fa-trash" style="font-size:10px;"></i>'
        + '</button>'
        + '<button type="button" onclick="ceAddSubCond(' + gi + ')" '
        + 'style="flex-shrink:0;width:26px;height:26px;margin-top:10px;background:##80808036;border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:rgb(60, 77, 98);"    >'
        + '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>'
        + '</button>';

    rowsEl.appendChild(row);
};

function ceAppendOpt(label, value, gotoVal) {
    var ol = document.getElementById('ceQModalOptionsList');
    var d  = document.createElement('div');
    d.style.cssText = 'display:flex;gap:8px;margin-bottom:10px;align-items:flex-end;';

    var gotoOpts = '';
    CE.questions.forEach(function(q) {
        gotoOpts += '<option value="' + esc(q.id) + '"' + (String(gotoVal) === String(q.id) ? ' selected' : '') + '>'
            + 'Q' + esc(String(q.id)) + (q.label ? ' \u2014 ' + q.label.substring(0,22) : '') + '</option>';
    });
    gotoOpts += '<option value="END"' + (!gotoVal || gotoVal === 'END' ? ' selected' : '') + '>Checkout</option>';

    var iStyle = 'padding:8px 10px;border:1.5px solid #e5e7eb;border-radius:6px;font-size:12px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;width:100%;';
    var iFocus = 'onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';

    d.innerHTML =
        '<div style="flex:2;position:relative;padding-top:12px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Label</span>'
        + '<input type="text" placeholder="" value="' + esc(label) + '" class="ce-opt-label" style="' + iStyle + '" ' + iFocus + '>'
        + '</div>'
        + '<div style="flex:1.5;position:relative;padding-top:12px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Value</span>'
        + '<input type="text" placeholder="" value="' + esc(value) + '" class="ce-opt-value" style="' + iStyle + '" ' + iFocus + '>'
        + '</div>'
        + '<div style="flex:2;position:relative;padding-top:12px;">'
        + '<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Go to Step</span>'
        + '<select class="ce-opt-goto" style="' + iStyle + 'cursor:pointer;" title="Go to step" ' + iFocus + '>'
        + gotoOpts
        + '</select>'
        + '</div>'
        // + '<button type="button" onclick="this.closest(\'div[style*=margin-bottom]\').remove()"'
        // + ' style="flex-shrink:0;width:28px;height:28px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;margin-bottom:1px;"'
        // + ' onmouseover="this.style.background=\'#fde8e8\';this.style.color=\'#dc2626\';this.style.borderColor=\'#fca5a5\'" onmouseout="this.style.background=\'#f1f5f9\';this.style.color=\'#94a3b8\';this.style.borderColor=\'#e2e8f0\'">'
        // + '<i class="fa fa-trash" style="font-size:10px;"></i>'
        // + '</button>';
        + '<button type="button" onclick="this.closest(\'div[style*=margin-bottom]\').remove();(function(){var ol=document.getElementById(\'ceQModalOptionsList\');var btn=document.getElementById(\'ceQModalAddOptionBtn\');if(btn)btn.style.display=ol&&ol.children.length===0?\'inline-flex\':\'none\';})()"'
        + ' style="flex-shrink:0;width:28px;height:28px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;margin-bottom:1px;"'
        + ' onmouseover="this.style.background=\'#fde8e8\';this.style.color=\'#dc2626\';this.style.borderColor=\'#fca5a5\'" onmouseout="this.style.background=\'#f1f5f9\';this.style.color=\'#94a3b8\';this.style.borderColor=\'#e2e8f0\'">'
        + '<i class="fa fa-trash" style="font-size:10px;"></i>'
        + '</button>'
        // + '<button type="button" onclick="this.closest(\'div[style*=margin-bottom]\').remove()"'
        // + ' style="flex-shrink:0;width:28px;height:28px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;margin-bottom:1px;"'
        // + ' onmouseover="this.style.background=\'#fde8e8\';this.style.color=\'#dc2626\';this.style.borderColor=\'#fca5a5\'" onmouseout="this.style.background=\'#f1f5f9\';this.style.color=\'#94a3b8\';this.style.borderColor=\'#e2e8f0\'">'
        // + '<i class="fa fa-trash" style="font-size:10px;"></i>' 
        // + '</button>'
        + '<button type="button" onclick="ceAddQModalOption()"'
        + ' style="flex-shrink:0;width:28px;height:28px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#374151;margin-bottom:1px;"'
        + ' onmouseover="this.style.background=\'#e85d2f\';this.style.color=\'#fff\';this.style.borderColor=\'#e85d2f\'" onmouseout="this.style.background=\'#f3f4f6\';this.style.color=\'#374151\';this.style.borderColor=\'#e5e7eb\'">'
        + '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>'
        + '</button>';

      ol.appendChild(d);

    var addOptBtn = document.getElementById('ceQModalAddOptionBtn');
    if (addOptBtn) addOptBtn.style.display = 'none';
}

   function cePopGoToDropdown(selected, condGoToSelected) {
    var sel = document.getElementById('ceQModalGoTo');
    sel.innerHTML = '<option value="">— None (next) —</option>';
    CE.questions.forEach(function (q) {
        var opt         = document.createElement('option');
        opt.value       = q.id;
        opt.textContent = 'QID' + q.id + (q.label ? ' — ' + q.label.substring(0, 40) : '');
        if (String(q.id) === String(selected)) opt.selected = true;
        sel.appendChild(opt); 
    });
    var endOpt         = document.createElement('option');
    endOpt.value       = 'END';
    endOpt.textContent = 'END (Checkout)';
    if (String(selected) === 'END') endOpt.selected = true;
    sel.appendChild(endOpt);

    var condSel = document.getElementById('ceQModalCondGoTo');
    if (condSel) {
        condSel.innerHTML = '<option value="">— None —</option>';
        CE.questions.forEach(function (q) {
            var opt         = document.createElement('option');
            opt.value       = q.id;
            opt.textContent = 'QID' + q.id + (q.label ? ' — ' + q.label.substring(0, 35) : '');
            if (String(q.id) === String(condGoToSelected)) opt.selected = true;
            condSel.appendChild(opt);
        });
        var endOpt2         = document.createElement('option');
        endOpt2.value       = 'END';
        endOpt2.textContent = 'Checkout';
        if (String(condGoToSelected) === 'END') endOpt2.selected = true;
        condSel.appendChild(endOpt2);
    }
}

window.ceSaveQuestion = function () {

    var labelWrap = document.getElementById('ceQModalLabelWrap');
    var label     = document.getElementById('ceQModalLabel').value.trim();

    var conditions = [];
    document.querySelectorAll('#ceQModalCondRows [id^=ceModalCondRow_]').forEach(function(row) {
        var lbl = row.querySelector('.ce-modal-cond-label');
        var qid = row.querySelector('.ce-modal-cond-qid');
        var val = row.querySelector('.ce-modal-cond-value');
        if (lbl || qid) {
            conditions.push({
                label : lbl ? lbl.value.trim() : '',
                qid   : qid ? qid.value        : '',
                value : val ? val.value.trim()  : '',
            });
        }
    });

    // If label wrap is hidden, take label from first condition row
    if (labelWrap && labelWrap.style.display === 'none' && conditions.length > 0) {
        label = conditions[0].label || label;
    }

    if (!label) { alert('Question label is required.'); return; }

    var placeholderEl = document.getElementById('ceQModalPlaceholder');
    var placeholder   = placeholderEl ? placeholderEl.value.trim() : '';

    var sameContractLinkEl = document.getElementById('ceQModalSameContractLink');
    var sameContractLink   = sameContractLinkEl ? sameContractLinkEl.value.trim() : '';

    var dropdownLinks = [];
    document.querySelectorAll('#ceQModalDropdownLinkRows > div').forEach(function(row) {
        var l = row.querySelector('.ce-dl-label');
        var k = row.querySelector('.ce-dl-link');
        if (l && l.value.trim()) {
            dropdownLinks.push({
                label : l.value.trim(),
                link  : k ? k.value.trim() : '',
            });
        }
    });

    var options = [];
    document.querySelectorAll('#ceQModalOptionsList > div').forEach(function(row) {
        var l = row.querySelector('.ce-opt-label');
        var v = row.querySelector('.ce-opt-value');
        if (l && l.value.trim()) {
            options.push({
                label : l.value.trim(),
                value : v ? (v.value.trim() || l.value.trim()) : l.value.trim(),
            });
        }
    });

    var condGoTo = (function () {
        var groups = [];
        var t = document.getElementById('ceQModalType').value;

        if (t === 'radio-button' || t === 'dropdown' || t === 'select') {
            document.querySelectorAll('#ceQModalOptionsList > div').forEach(function(row) {
                var lbl     = row.querySelector('.ce-opt-label');
                var val     = row.querySelector('.ce-opt-value');
                var gotoSel = row.querySelector('.ce-opt-goto');
                var optVal  = (val && val.value.trim()) ? val.value.trim() : (lbl ? lbl.value.trim() : '');
                var gotoVal = gotoSel ? gotoSel.value : '';
                if (optVal && gotoVal) {
                    var editingQ = CE.editingQuestionIdx !== null ? CE.questions[CE.editingQuestionIdx] : null;
                    var qid      = editingQ ? editingQ.id : ('new_' + Date.now());
                    groups.push({
                        conditions : [{ qid: qid, type: 'is_equal_to', value: optVal }],
                        goto       : gotoVal,
                    });
                }
            });
        }

        document.querySelectorAll('#ceCondGroupsContainer [id^=ceCondGroup_]').forEach(function(grp) {
            var rows = [];
            grp.querySelectorAll('.ce-sub-qid').forEach(function(qidEl, i) {
                var condEl = grp.querySelectorAll('.ce-sub-cond')[i];
                var valEl  = grp.querySelectorAll('.ce-sub-val')[i];
                rows.push({
                    qid   : qidEl  ? qidEl.value        : '',
                    type  : condEl ? condEl.value       : '',
                    value : valEl  ? valEl.value.trim() : '',
                });
            });
            var gotoSel = grp.querySelector('.ce-cond-goto-sel');
            groups.push({
                conditions : rows,
                goto       : gotoSel ? gotoSel.value : '',
            });
        });

        return groups;
    })();

    var qObj = {
        type             : document.getElementById('ceQModalType').value,
        label            : label,
        placeholder      : placeholder,
        sameContractLink : sameContractLink,
        dropdownLinks    : dropdownLinks,
        section          : '',
        info             : document.getElementById('ceQModalInfo').value.trim(),
        goTo             : document.getElementById('ceQModalGoTo').value || null,
        options          : options,
        conditions       : conditions,
        condGoTo         : condGoTo,
        condGoToStep     : null,
        usedIn           : 0,
    };

    if (CE.editingQuestionIdx === null) {
        qObj.id    = 'new_' + Date.now();
        qObj.isNew = true;
        CE.questions.push(qObj);
    } else {
        var ex      = CE.questions[CE.editingQuestionIdx];
        qObj.usedIn = ex.usedIn;
        qObj.isNew  = ex.isNew;
        if (!ex.isNew) qObj.id = ex.id;
        Object.assign(ex, qObj);
    }

    closeCeQuestionModal();
    ceRenderQ();
    ceRenderPreview();
    ceSetStatus('Unsaved changes');
};
//     window.ceAddNewSection = function () {
//     CE.editingSectionIdx = null;
//     CE._stdClauseInsertAfterIdx = CE.sections.length - 1; 
//     document.getElementById('ceSModalTitle').textContent = '';
//     document.getElementById('ceSModalType').value = 'content';
//     document.getElementById('ceSModalId').value              = '';
//     document.getElementById('ceSModalSectionKey').value      = '';
//     document.getElementById('ceSModalContent').value         = '';
//     if (window._ceEditorSetValue) window._ceEditorSetValue('');
//     document.getElementById('ceSModalAlign').value           = 'left';
//     document.getElementById('ceSModalBlurSelect').checked    = false;
//     document.getElementById('ceSCondGroupsContainer').innerHTML = '';
//     document.getElementById('ceSModalCondWrap').style.display = 'block';
//     cePopQBadges();
//     document.getElementById('ceSectionModal').classList.add('open');
// };

//     window.ceEditS = function (idx) {
//         var s = CE.sections[idx];
//         if (!s) return;
//         CE.editingSectionIdx = idx;
//         document.getElementById('ceSModalTitle').textContent = s.isNew ? '' : 'T' + s.id;
//         document.getElementById('ceSModalType').value = s.type || 'content';
//         document.getElementById('ceSModalId').value              = s.id || '';
//         document.getElementById('ceSModalSectionKey').value      = s.section_key || '';
//         document.getElementById('ceSModalContent').value         = s.content      || '';
//         if (window._ceEditorSetValue) window._ceEditorSetValue(s.content || '');
//         document.getElementById('ceSModalAlign').value           = s.text_align   || 'left';
//         document.getElementById('ceSModalBlurSelect').checked    = !!s.secure_blur_content;
//         document.getElementById('ceSCondGroupsContainer').innerHTML = '';
//         document.getElementById('ceSModalCondWrap').style.display = (s.type === 'content') ? 'block' : 'none';
//             if (s.conditions && s.conditions.length > 0) {
//                 ceAddSectionCondGroup();
//                 var gi = document.querySelectorAll('#ceSCondGroupsContainer [id^=ceSCondGroup_]').length - 1;
//                 var grpEl = document.getElementById('ceSCondGroup_' + gi);
//                 if (grpEl) {
//                     var rowsEl = grpEl.querySelector('.ce-scond-rows-' + gi);
//                     if (rowsEl) rowsEl.innerHTML = '';
//                     s.conditions.forEach(function(c, ci) {
//                         ceAddSectionCondRow(gi);
//                     });
//                     setTimeout(function() {
//                         var rows = document.querySelectorAll('#ceSCondGroup_' + gi + ' [data-scond-row]');
//                         s.conditions.forEach(function(c, ci) {
//                             if (!rows[ci]) return;
//                             var qid  = rows[ci].querySelector('.ce-scond-qid');
//                             var type = rows[ci].querySelector('.ce-scond-type');
//                             var val  = rows[ci].querySelector('.ce-scond-val');
//                             if (qid)  qid.value  = c.qid   || '';
//                             if (type) type.value = c.type   || '';
//                             if (val)  val.value  = c.value  || '';
//                             var plusBtn = rows[ci].querySelector('.ce-scond-add-btn');
//                             if (plusBtn && ci < s.conditions.length - 1) plusBtn.style.display = 'none';
//                         });
//                     }, 0);
//                 }
//             }
//         cePopQBadges();
//         document.getElementById('ceSectionModal').classList.add('open');
//     };

window.ceAddNewSection = function () {
    CE.editingSectionIdx = null;
    CE._stdClauseInsertAfterIdx = CE.sections.length - 1;
    document.getElementById('ceSModalTitle').textContent     = '';
    document.getElementById('ceSModalType').value            = 'content';
    document.getElementById('ceSModalId').value              = '';
    document.getElementById('ceSModalSectionKey').value      = '';
    document.getElementById('ceSModalContent').value         = '';
    if (window._ceEditorSetValue) window._ceEditorSetValue('');
    document.getElementById('ceSModalAlign').value           = 'left';
    document.getElementById('ceSModalBlurSelect').checked    = false;
    document.getElementById('ceSCondGroupsContainer').innerHTML = '';
    document.getElementById('ceSModalCondWrap').style.display = 'block';
    window._ceSModalSelectedStdId = null;
    // Reset std clauses wrap visibility
    var stdWrap = document.getElementById('ceSModalStdClausesWrap');
    if (stdWrap) stdWrap.style.display = 'none';
    // Ensure content editor visible
    var contentEditor = document.getElementById('ceSModalContentEditor');
    var contentFg = contentEditor ? contentEditor.closest('.ce-fg') : null;
    if (contentFg) contentFg.style.display = '';
    var alignRow = document.querySelector('#ceSectionModal .iner-ce-fg')
                 ? document.querySelector('#ceSectionModal .iner-ce-fg').closest('.row') : null;
    if (alignRow) alignRow.style.display = '';
    var blurBtn = document.querySelector('.contract-blur-button');
    if (blurBtn) blurBtn.style.display = '';
    cePopQBadges();
    document.getElementById('ceSectionModal').classList.add('open');
};

window.ceEditS = function (idx) {
    var s = CE.sections[idx];
    if (!s) return;
    CE.editingSectionIdx = idx;
    document.getElementById('ceSModalTitle').textContent     = s.isNew ? '' : 'T' + s.id;
    document.getElementById('ceSModalType').value            = s.type || 'content';
    document.getElementById('ceSModalId').value              = s.id || '';
    document.getElementById('ceSModalSectionKey').value      = s.section_key || '';
    document.getElementById('ceSModalContent').value         = s.content || '';
    if (window._ceEditorSetValue) window._ceEditorSetValue(s.content || '');
    document.getElementById('ceSModalAlign').value           = s.text_align || 'left';
    document.getElementById('ceSModalBlurSelect').checked    = !!s.secure_blur_content;
    document.getElementById('ceSCondGroupsContainer').innerHTML = '';
    window._ceSModalSelectedStdId = null;

    // Reset std clauses wrap visibility  
    var stdWrap = document.getElementById('ceSModalStdClausesWrap');
    if (stdWrap) stdWrap.style.display = 'none';
    var contentEditor = document.getElementById('ceSModalContentEditor');
    var contentFg = contentEditor ? contentEditor.closest('.ce-fg') : null;
    if (contentFg) contentFg.style.display = '';
    var alignRow = document.querySelector('#ceSectionModal .iner-ce-fg')
                 ? document.querySelector('#ceSectionModal .iner-ce-fg').closest('.row') : null;
    if (alignRow) alignRow.style.display = '';
    var blurBtn = document.querySelector('.contract-blur-button');
    if (blurBtn) blurBtn.style.display = '';

    document.getElementById('ceSModalCondWrap').style.display = (s.type === 'content') ? 'block' : 'none';

    if (s.conditions && s.conditions.length > 0) {
        ceAddSectionCondGroup();
        var gi = document.querySelectorAll('#ceSCondGroupsContainer [id^=ceSCondGroup_]').length - 1;
        var grpEl = document.getElementById('ceSCondGroup_' + gi);
        if (grpEl) {
            var rowsEl = grpEl.querySelector('.ce-scond-rows-' + gi);
            if (rowsEl) rowsEl.innerHTML = '';
            s.conditions.forEach(function(c, ci) {
                ceAddSectionCondRow(gi);
            });
            setTimeout(function() {
                var rows = document.querySelectorAll('#ceSCondGroup_' + gi + ' [data-scond-row]');
                s.conditions.forEach(function(c, ci) {
                    if (!rows[ci]) return;
                    var qid  = rows[ci].querySelector('.ce-scond-qid');
                    var type = rows[ci].querySelector('.ce-scond-type');
                    var val  = rows[ci].querySelector('.ce-scond-val');
                    if (qid)  qid.value  = c.qid   || '';
                    if (type) type.value = c.type   || '';
                    if (val)  val.value  = c.value  || '';
                    var plusBtn = rows[ci].querySelector('.ce-scond-add-btn');
                    if (plusBtn && ci < s.conditions.length - 1) plusBtn.style.display = 'none';
                });
            }, 0);
        }
    }
    cePopQBadges();
    document.getElementById('ceSectionModal').classList.add('open');
        if (s.type === 'standard-clauses') {
        ceSModalTypeChange();
    }
};

    function cePopQBadges() {
        var c = document.getElementById('ceSModalQidList');
        if (!c) return;
        if (!CE.questions.length) {
            c.innerHTML = '<span style="font-size:11px;color:#9ca3af;">No questions yet — add questions first.</span>';
        }
    }

    window.ceInsertQid = function (id) {
    var editor = document.getElementById('ceSModalContentEditor');
    if (editor && document.getElementById('ceSectionModal').classList.contains('open')) {
        editor.focus();
        var badge = document.createElement('span');
        badge.className = 'ce-qid-badge';
        badge.setAttribute('data-qid', id);
        badge.setAttribute('contenteditable', 'true');
        var q = window.__CE && window.__CE.questions
            ? window.__CE.questions.find(function(qq){ return String(qq.id) === String(id); })
            : null;
        badge.title       = q ? (q.label || ('Q' + id)) : ('Q' + id);
        badge.textContent = 'Q' + id;
        var sel = window.getSelection();
        if (sel && sel.rangeCount) {
            var range = sel.getRangeAt(0);
            range.deleteContents();
            range.insertNode(badge);
            range.setStartAfter(badge);
            range.collapse(true);
            sel.removeAllRanges();
            sel.addRange(range);
        } else {
            editor.appendChild(badge);
        }
        return;
    }
    // fallback original textarea
    var ta  = document.getElementById('ceSModalContent');
    var pos = ta.selectionStart;
    var ph  = '{QID' + id + '}';
    ta.value = ta.value.substring(0, pos) + ph + ta.value.substring(ta.selectionEnd);
    ta.selectionStart = ta.selectionEnd = pos + ph.length;
    ta.focus();
};

    window.ceDelS = function (idx) {
        if (!confirm('Delete this contract section?')) return;
        var s = CE.sections[idx];
        if (s && !s.isNew) CE.deletedSectionIds.push(s.id);
        CE.sections.splice(idx, 1);
        CE.sectionsFull = CE.sections.slice();
        ceRenderPreview();
        ceSetStatus('Unsaved changes');
    };

    window.ceMoveS = function (idx, dir) {
        var ni = idx + dir;
        if (ni < 0 || ni >= CE.sections.length) return;
        var tmp          = CE.sections[idx];
        CE.sections[idx] = CE.sections[ni];
        CE.sections[ni]  = tmp;
        CE.sectionsFull  = CE.sections.slice();
        ceRenderPreview();
        ceSetStatus('Unsaved changes');
    };

    window.ceSaveSection = function () {
        var type    = document.getElementById('ceSModalType').value;
        if (type === 'standard-clauses') {
        var ids = window._ceSModalSelectedIds || new Set();
        if (!ids.size) { alert('Please select at least one standard clause.'); return; }
        ceSModalInsertSelected();
        return;
}

        if (window._ceEditorSyncToTextarea) window._ceEditorSyncToTextarea();
        var content = document.getElementById('ceSModalContent').value;
        if (!content.trim() && type !== 'signature_field') {
            alert('Content is required.');
            return;
        }

        var sConditions = [];
        document.querySelectorAll('#ceSCondGroupsContainer [data-scond-row]').forEach(function(row) {
            var qid  = row.querySelector('.ce-scond-qid');
            var typ  = row.querySelector('.ce-scond-type');
            var val  = row.querySelector('.ce-scond-val');
            if (qid && qid.value) sConditions.push({
        qid   : qid.value,
        type  : typ ? typ.value : 'is_equal_to',
        value : val ? val.value.trim() : ''
    });
});

            var sObj = {
                type                : type,
                content             : content,
                section_key         : document.getElementById('ceSModalSectionKey').value.trim(),
                section_name     : ' ',
                text_align          : document.getElementById('ceSModalAlign').value,
                secure_blur_content : document.getElementById('ceSModalBlurSelect').checked ? 1 : 0,
                conditions          : sConditions,
            };

        if (CE.editingSectionIdx === null) {
            sObj.id    = 'new_' + Date.now();
            sObj.isNew = true;
            CE.sections.push(sObj);
        } else {
            var ex = CE.sections[CE.editingSectionIdx];
            sObj.isNew = ex.isNew;
            if (!ex.isNew) sObj.id = ex.id;
            Object.assign(ex, sObj);
        }
        CE.sectionsFull = CE.sections.slice();
        closeCeSectionModal();
        ceRenderPreview();
        ceSetStatus('Unsaved changes');
    };

window.ceSaveAll = function () {
    var btn = document.getElementById('ceSaveBtn');
    btn.disabled    = true;
    btn.textContent = 'Saving…';

    var expandedSections = [];
    CE.sections.forEach(function(s) {
        if (s.isStdClauseGroup) {
            if (s.stdClauseSections && s.stdClauseSections.length) {
                s.stdClauseSections.forEach(function(gs) {
                    expandedSections.push({
                        id                  : null,
                        type                : gs.type || 'content',
                        content             : gs.content || '',
                        section_key         : gs.section_key || s.section_key || '',
                        section_name        : s.stdClauseTitle || s.section_name || '',
                        text_align          : gs.text_align || 'left',
                        secure_blur_content : gs.secure_blur_content || 0,
                        order_id            : expandedSections.length + 1,
                        conditions          : [],
                    });
                });
            } else {
                expandedSections.push({
                    id                  : s.isNew ? null : s.id,
                    type                : 'content',
                    content             : s.stdClauseTitle || s.section_name || '',
                    section_key         : s.section_key || '',
                    section_name        : s.stdClauseTitle || s.section_name || '',
                    text_align          : 'left',
                    secure_blur_content : 0,
                    order_id            : expandedSections.length + 1,
                    conditions          : [],
                });
            }
        } else {
            expandedSections.push({
                id                  : s.isNew ? null : s.id,
                type                : s.type,
                content             : s.content,
                section_key         : s.section_key,
                section_name        : s.section_name,
                text_align          : s.text_align,
                secure_blur_content : s.secure_blur_content,
                order_id            : expandedSections.length + 1,
                conditions          : (s.conditions || []).map(function(c) {
                    return {
                        qid   : c.qid   ? String(c.qid)   : '',
                        type  : c.type  || 'is_equal_to',
                        value : c.value || '',
                    };
                }),
            });
        }
    });

    var payload = {
        _token             : CE.csrfToken,
        document_id        : CE.documentId,
        delete_question_ids: CE.deletedQuestionIds,
        delete_section_ids : CE.deletedSectionIds,
        questions: CE.questions.map(function (q, i) {
            return {
                id           : q.isNew ? null : q.id,
                type         : q.type || 'textbox',
                label        : q.label,
                placeholder  : q.placeholder  || '',
                info         : q.info         || '',
                required     : q.required     || 0,
                order_id     : i + 1,
                options      : (q.options || []).map(function(o) {
                    return {
                        id    : o.id    || null,
                        label : o.label || '',
                        value : o.value || o.label || '',
                    };
                }),
                section_name : q.section || '',
                go_to        : q.goTo    || null,
                // ── FIX: send conditions (show-if logic) correctly ──
                conditions   : (q.conditions || []).map(function(c) {
                    return {
                        label : c.label || '',
                        qid   : c.qid   ? String(c.qid)   : '',
                        value : c.value || '',
                    };
                }),
                // ── FIX: filter uses null/undefined check, not falsy ──
                cond_go_to   : (q.condGoTo || [])
                    .filter(function(cg) {
                        return cg.goto !== null && cg.goto !== undefined && cg.goto !== '';
                    })
                    .map(function(cg) {
                        return {
                            goto       : String(cg.goto),
                            conditions : (cg.conditions || []).map(function(c) {
                                return {
                                    qid   : c.qid   ? String(c.qid)   : '',
                                    type  : c.type  || 'is_equal_to',
                                    value : c.value || '',
                                };
                            }),
                        };
                    }),
            };
        }),
        sections: expandedSections,
    };

    fetch('/admin-dashboard/api/ce-save', {
        method  : 'POST',
        headers : {
            'Content-Type' : 'application/json',
            'X-CSRF-TOKEN' : CE.csrfToken,
            'Accept'       : 'application/json',
        },
        body: JSON.stringify(payload),
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (!data.success) throw new Error(data.message || 'Save failed');
        ceSetStatus('Saved');
        ceShowToast('Contract saved successfully.', false);
        return window.ceLoadData();
    })
    .catch(function (err) {
        ceShowToast('Save failed: ' + err.message, true);
    })
    .finally(function () {
        btn.disabled  = false;
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">'
            + '<path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>'
            + '<polyline points="17 21 17 13 7 13 7 21"/>'
            + '<polyline points="7 3 7 8 15 8"/></svg>';
    });
};

    window.closeCeQuestionModal = function () {
        document.getElementById('ceQuestionModal').classList.remove('open');
        document.getElementById('ceAiEditModal').classList.remove('open');

    };
    window.closeCeSectionModal = function () {
        document.getElementById('ceSectionModal').classList.remove('open');
    };

    ['ceQuestionModal', 'ceSectionModal', 'ceStdClauseModal'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('open');
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        document.getElementById('ceQuestionModal').classList.remove('open');
        document.getElementById('ceAiEditModal').classList.remove('open');
        document.getElementById('ceSectionModal').classList.remove('open');
        document.getElementById('ceStdClauseModal').classList.remove('open');
        document.getElementById('ceStdClauseSubView').classList.remove('open');
        document.getElementById('ceConfigDropdown').style.display = 'none';
    });

    document.addEventListener('DOMContentLoaded', function () { ceLoadData(); });
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        ceLoadData();
    }


    var _ceAiEditPendingQuestions = null;
    var _ceAiEditPendingSections  = null;
    var _ceAiEditAbortCtrl        = null;

    window.ceOpenAiEditModal = function () {
        document.getElementById('ceAiEditPrompt').value             = '';
        document.getElementById('ceAiEditProgress').style.display   = 'none';
        document.getElementById('ceAiEditResult').style.display     = 'none';
        document.getElementById('ceAiEditFooter').style.display     = 'flex';
        document.getElementById('ceAiEditRunBtn').disabled          = false;
        document.getElementById('ceAiScopeAll').checked             = true;
        ceAiScopeChange();
        _ceAiEditPendingQuestions = null;
        _ceAiEditPendingSections  = null;
        if (_ceAiEditAbortCtrl) { _ceAiEditAbortCtrl.abort(); _ceAiEditAbortCtrl = null; }
        document.getElementById('ceAiEditModal').classList.add('open');
    };

    window.ceCloseAiEditModal = function () {
        if (_ceAiEditAbortCtrl) { _ceAiEditAbortCtrl.abort(); _ceAiEditAbortCtrl = null; }
        document.getElementById('ceAiEditModal').classList.remove('open');
    };

    window.ceAiScopeChange = function () {
        ['ceAiScopeAllLabel','ceAiScopeQLabel','ceAiScopeSLabel'].forEach(function(id) {
            var el = document.getElementById(id);
            if (!el) return;
            var radio = el.querySelector('input[type=radio]');
            el.style.borderColor = (radio && radio.checked) ? '#e85d2f' : '#e5e7eb';
            el.style.background  = (radio && radio.checked) ? '#fff4f0' : '#fff';
        });
    };

    window.ceRunAiEdit = function () {
        var prompt = document.getElementById('ceAiEditPrompt').value.trim();
        if (!prompt) { alert('Please describe the changes you want.'); return; }

        var scope    = document.querySelector('input[name="ceAiScope"]:checked');
        var scopeVal = scope ? scope.value : 'all';

        if (_ceAiEditAbortCtrl) _ceAiEditAbortCtrl.abort();
        _ceAiEditAbortCtrl = new AbortController();

        document.getElementById('ceAiEditRunBtn').disabled          = true;
        document.getElementById('ceAiEditProgress').style.display   = 'flex';
        document.getElementById('ceAiEditResult').style.display     = 'none';
        document.getElementById('ceAiEditFooter').style.display     = 'flex';

        var progressEl = document.getElementById('ceAiEditProgressText');
        progressEl.textContent = 'Sending to AI…';

        var qSummary = CE.questions.map(function(q) {
            return {
                id      : q.id,
                type    : q.type,
                label   : (q.label || '').substring(0, 120),
                options : (q.options || []).slice(0, 10).map(function(o){
                    return { label: o.label, value: o.value };
                }),
                goTo    : q.goTo || null,
            };
        });

        var sSummary = CE.sections.map(function(s) {
            return {
                id      : s.id,
                type    : s.type,
                content : s.content || '',
            };
        });

        var timeoutId = setTimeout(function() {
            if (_ceAiEditAbortCtrl) {
                _ceAiEditAbortCtrl.abort();
                _ceAiEditAbortCtrl = null;
            }
            document.getElementById('ceAiEditProgress').style.display = 'none';
            document.getElementById('ceAiEditRunBtn').disabled         = false;
            ceShowToast('Request timed out. Try a simpler instruction or smaller scope.', true);
        }, 120000);

        progressEl.textContent = 'AI is processing…';

        fetch('/admin-dashboard/api/ce-ai-edit', {
            method  : 'POST',
            headers : {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : CE.csrfToken,
                'Accept'       : 'application/json',
            },
            signal : _ceAiEditAbortCtrl.signal,
            body   : JSON.stringify({
                document_id : CE.documentId,
                prompt      : prompt,
                scope       : scopeVal,
                questions   : qSummary,
                sections    : sSummary,
            }),
        })
        .then(function(r) {
            clearTimeout(timeoutId);
            if (!r.ok) {
                return r.json().then(function(d) {
                    throw new Error(d.message || ('Server error ' + r.status));
                });
            }
            return r.json();
        })
        .then(function(data) {
            if (!data.success) throw new Error(data.message || 'AI edit failed');

            // _ceAiEditPendingQuestions = data.questions;
            // _ceAiEditPendingSections  = data.sections;
            _ceAiEditPendingQuestions = Array.isArray(data.questions) ? data.questions : [];
            _ceAiEditPendingSections  = Array.isArray(data.sections)  ? data.sections  : [];

            document.getElementById('ceAiEditSummary').textContent   = data.summary || 'Changes processed.';
            document.getElementById('ceAiEditProgress').style.display = 'none';
            document.getElementById('ceAiEditResult').style.display   = 'block';
            document.getElementById('ceAiEditFooter').style.display   = 'none';
        })
        .catch(function(err) {
            clearTimeout(timeoutId);
            if (err.name === 'AbortError') return;
            document.getElementById('ceAiEditProgress').style.display = 'none';
            document.getElementById('ceAiEditRunBtn').disabled         = false;
            ceShowToast('AI edit failed: ' + err.message, true);
        })
        .finally(function() {
            _ceAiEditAbortCtrl = null;
        });
    };

   window.ceAiEditAccept = function () {
    if (!_ceAiEditPendingQuestions || !_ceAiEditPendingSections) return;

    // ── MERGE questions ──
    _ceAiEditPendingQuestions.forEach(function(q) {
        var existingIdx = CE.questions.findIndex(function(eq) {
            return String(eq.id) === String(q.id);
        });
        var isUnchanged = (!q.label || q.label === '[unchanged]');

        if (existingIdx >= 0) {
            if (!isUnchanged) {
                var ex = CE.questions[existingIdx];
                if (q.label && q.label !== '[unchanged]') ex.label = q.label;
                if (q.placeholder !== undefined) ex.placeholder = q.placeholder;
                if (q.info !== undefined) ex.info = q.info;
                if (q.type && q.type !== '[unchanged]') ex.type = q.type;
                if (q.goTo !== undefined) ex.goTo = q.goTo;
                if (q.options && q.options.length > 0) {
                    ex.options = q.options.map(function(o) {
                        return { label: o.label || '', value: o.value || o.label || '' };
                    });
                }
            }
        } else if (!isUnchanged && (q.id === null || q.id === undefined)) {
            CE.questions.push({
                id          : 'new_' + Date.now() + '_' + Math.random(),
                type        : q.type || 'textbox',
                label       : q.label || '',
                placeholder : q.placeholder || '',
                info        : q.info || '',
                goTo        : q.goTo || null,
                options     : (q.options || []).map(function(o) {
                    return { label: o.label || '', value: o.value || o.label || '' };
                }),
                conditions  : [],
                condGoTo    : [],
                usedIn      : 0,
                required    : 1,
                section     : '',
                isNew       : true,
            });
        }
    });

    _ceAiEditPendingSections.forEach(function(s) {
        var existingIdx = CE.sections.findIndex(function(es) {
            return String(es.id) === String(s.id);
        });

        var isUnchanged = (!s.content || s.content === '[unchanged]');

        if (existingIdx >= 0) {
            var ex = CE.sections[existingIdx];
            var originalLen = (ex.content || '').length;
            var newLen      = (s.content || '').length;

            var likelyTruncated = !isUnchanged && newLen < originalLen * 0.7 && newLen < originalLen - 30;

            if (isUnchanged || likelyTruncated) {
                if (s.type && s.type !== '[unchanged]') ex.type = s.type;
                if (s.text_align !== undefined && s.text_align !== ex.text_align) ex.text_align = s.text_align;
            } else {
                if (s.content !== undefined && s.content !== '[unchanged]') ex.content = s.content;
                if (s.type && s.type !== '[unchanged]') ex.type = s.type;
                if (s.text_align !== undefined) ex.text_align = s.text_align;
                if (s.secure_blur_content !== undefined) ex.secure_blur_content = s.secure_blur_content;
            }
        } else if (!isUnchanged && (s.id === null || s.id === undefined)) {
            CE.sections.push({
                id                  : 'new_' + Date.now() + '_' + Math.random(),
                type                : s.type    || 'content',
                content             : s.content || '',
                text_align          : s.text_align || 'left',
                secure_blur_content : s.secure_blur_content || 0,
                section_key         : '',
                section_name        : '',
                conditions          : [],
                isNew               : true,
            });
        }
    });

    CE.sectionsFull = CE.sections.slice();
    _ceAiEditPendingQuestions = null;
    _ceAiEditPendingSections  = null;

    ceCloseAiEditModal();
    ceRenderQ();
    ceRenderPreview();
    ceSetStatus('AI changes applied — remember to save');
    setTimeout(function() { ceSetStatus(''); }, 3000);
};

    window.ceAiEditReject = function () {
        _ceAiEditPendingQuestions = null;
        _ceAiEditPendingSections  = null;
        document.getElementById('ceAiEditResult').style.display   = 'none';
        document.getElementById('ceAiEditProgress').style.display = 'none';
        document.getElementById('ceAiEditFooter').style.display   = 'flex';
        document.getElementById('ceAiEditRunBtn').disabled        = false;
        document.getElementById('ceAiEditPrompt').value           = '';
    };

    document.getElementById('ceAiEditModal').addEventListener('click', function(e) {
        if (e.target === this) ceCloseAiEditModal();
    });

}());

(function () {
    var _qidAutoOpen   = false;
    var _qidAutoStart  = -1; 
    var _qidAutoActive = 0; 

   function ceQidAutoShow(ta) {
    var pop  = document.getElementById('ceQidAutocomplete');
    var rect = ta.getBoundingClientRect();

    var popHeight = 280;
    var spaceBelow = window.innerHeight - rect.bottom - 8;
    var topPos = spaceBelow >= popHeight ? rect.bottom + 4 : rect.top - popHeight + 100;

    pop.style.position = 'fixed';
    // pop.style.left    = Math.max(8, rect.left) + 'px';
    pop.style.top     = topPos + 'px';
    pop.style.width   = Math.max(320, rect.width) + 'px';
    pop.style.display = 'block';
    _qidAutoOpen   = true;
    _qidAutoActive = 0;

    var searchEl = document.getElementById('ceQidAutoSearch');
    if (searchEl) searchEl.value = '';
    
    ceQidAutoFilter('');

    setTimeout(function () {
        var s = document.getElementById('ceQidAutoSearch');
        if (s) s.focus();
    }, 30);
}

    window.ceQidAutoHide = function (restoreFocus) {
    document.getElementById('ceQidAutocomplete').style.display = 'none';
    _qidAutoOpen  = false;
    _qidAutoStart = -1;
    if (restoreFocus) {
        var ta = document.getElementById('ceSModalContent');
        if (ta) ta.focus();
    }
};

   window.ceQidAutoFilter = function (v) {
    var lc   = v.toLowerCase().replace(/^qid/i, '').replace(/^\{/,'');
    var list = document.getElementById('ceQidAutoList');

    var questions = (window.__CE && window.__CE.questions) ? window.__CE.questions : [];

    if (!questions.length) {
        list.innerHTML = '<div style="padding:10px 12px;font-size:12px;color:#9ca3af;font-style:italic;">No questions loaded yet.</div>';
        return;
    }

    var items = questions.filter(function (q) {
        return !lc
            || String(q.id).includes(lc)
            || (q.label || '').toLowerCase().includes(lc);
    });

    if (!items.length) {
        list.innerHTML = '<div style="padding:10px 12px;font-size:12px;color:#9ca3af;font-style:italic;">No questions found</div>';
        return;
    }

    list.innerHTML = items.map(function (q, i) {
        var rawLabel = q.label || '';
        var labelPreview = rawLabel.substring(0, 55) + (rawLabel.length > 55 ? '…' : '');
        var safeLabel = labelPreview.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        var safeId    = String(q.id).replace(/"/g,'&quot;');
        return '<div class="ce-qid-auto-item" data-qid="' + safeId + '" data-idx="' + i + '"'
            + ' onclick="ceQidAutoSelect(\'' + safeId + '\')"'
            + ' style="padding:7px 12px;cursor:pointer;display:flex;align-items:center;gap:10px;border-bottom:1px solid #f3f4f6;background:#fff;transition:background .1s;"'
            + ' onmouseover="this.style.background=\'#f9fafb\'" onmouseout="this.style.background=\'#fff\'">'
            + '<span style="font-size:11px;font-weight:600;color:#6b7280;font-family:monospace;white-space:nowrap;flex-shrink:0;">QID' + safeId + '</span>'
            + '<span style="font-size:11px;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;">— ' + safeLabel + '</span>'
            + '</div>';
    }).join('');
};
window.ceQidAutoSelect = function (qid) {
    var editor = document.getElementById('ceSModalContentEditor');
    var modal  = document.getElementById('ceSectionModal');
    var isRichEditor = modal && modal.classList.contains('open') && editor;

    if (isRichEditor) {
        var node     = window._qidAutoAnchorNode;
        var startPos = window._qidAutoStart;
        var endPos   = window._qidAutoAnchorOffset;

        // Build badge
        var badge = document.createElement('span');
        badge.className = 'ce-qid-badge';
        badge.setAttribute('data-qid', qid);
        badge.setAttribute('contenteditable', 'true');
        var q = window.__CE && window.__CE.questions
            ? window.__CE.questions.find(function(qq){ return String(qq.id) === String(qid); })
            : null;
        badge.title       = q ? (q.label || ('Q' + qid)) : ('Q' + qid);
        badge.textContent = 'Q' + qid;

        if (node && node.nodeType === Node.TEXT_NODE && node.parentNode) {
            // text before { and text after cursor
            var before = node.textContent.substring(0, startPos);
            var after  = node.textContent.substring(endPos);

            node.textContent = before;
            var afterNode = document.createTextNode(after || '\u200B'); // zero-width space keeps cursor placeable
            node.parentNode.insertBefore(badge, node.nextSibling);
            node.parentNode.insertBefore(afterNode, badge.nextSibling);

            // Move cursor right after badge
            var newRange = document.createRange();
            newRange.setStart(afterNode, 0);
            newRange.collapse(true);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(newRange);
        } else {
            // Fallback: just append badge to editor
            editor.appendChild(badge);
            editor.appendChild(document.createTextNode('\u200B'));
        }

        // Clear stored anchor
        window._qidAutoAnchorNode   = null;
        window._qidAutoAnchorOffset = 0;
        window._qidAutoStart        = -1;
        _qidAutoStart               = -1;

        ceQidAutoHide(false);
        editor.focus();
        return;
    }

    // Original textarea fallback
    var ta = document.getElementById('ceSModalContent');
    if (!ta) return;
    var val   = ta.value;
    var token = '{' + qid + '}';
    if (_qidAutoStart >= 0) {
        var cursorPos = ta.selectionStart;
        var before = val.substring(0, _qidAutoStart);
        var after  = val.substring(cursorPos);
        ta.value   = before + token + after;
        var newPos = _qidAutoStart + token.length;
        ta.selectionStart = ta.selectionEnd = newPos;
    } else {
        var pos  = ta.selectionStart;
        ta.value = val.substring(0, pos) + token + val.substring(pos);
        ta.selectionStart = ta.selectionEnd = pos + token.length;
    }
    ceQidAutoHide(false);
    ta.focus();
};

    document.addEventListener('DOMContentLoaded', function () {
        ceQidAutoAttach();
    });
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(ceQidAutoAttach, 200);
    }

    function ceQidAutoAttach() {
        var ta = document.getElementById('ceSModalContent');
        if (!ta) return;

        ta.addEventListener('keydown', function (e) {
            if (!_qidAutoOpen) return;
            var list  = document.getElementById('ceQidAutoList');
            var items = list.querySelectorAll('.ce-qid-auto-item');
            if (e.key === 'Escape') { e.preventDefault(); ceQidAutoHide(true); }
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                _qidAutoActive = Math.min(_qidAutoActive + 1, items.length - 1);
                ceQidAutoHighlight(items);
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                _qidAutoActive = Math.max(_qidAutoActive - 1, 0);
                ceQidAutoHighlight(items);
            }
            if (e.key === 'Enter' && items[_qidAutoActive]) {
                e.preventDefault();
                ceQidAutoSelect(items[_qidAutoActive].getAttribute('data-qid'));
            }
        });

        ta.addEventListener('input', function (e) {
            var pos = ta.selectionStart;
            var val = ta.value;
            var bracePos = -1;
            for (var i = pos - 1; i >= 0; i--) {
                if (val[i] === '{') { bracePos = i; break; }
                if (val[i] === '}' || val[i] === '\n') break;
            }
            if (bracePos >= 0) {
                _qidAutoStart = bracePos;
                var typed = val.substring(bracePos + 1, pos);
                ceQidAutoShow(ta);
                document.getElementById('ceQidAutoSearch').value = typed;
                ceQidAutoFilter(typed);
            } else if (_qidAutoOpen) {
                ceQidAutoHide();
            }
        });
    }

        function ceQidAutoHighlight(items) {
            items.forEach(function (item, i) {
                item.style.background = i === _qidAutoActive ? '#fff4f0' : '#fff';
            });
            if (items[_qidAutoActive]) {
                items[_qidAutoActive].scrollIntoView({ block: 'nearest' });
            }
        }

            document.addEventListener('click', function (e) {
            var pop = document.getElementById('ceQidAutocomplete');
            if (!pop || pop.style.display === 'none') return;
            if (pop.contains(e.target)) return;
            if (e.target.id === 'ceSModalContent') return;
            var modal = document.getElementById('ceSectionModal');
            var restoreFocus = modal ? !modal.contains(e.target) : false;
            ceQidAutoHide(restoreFocus);
            });

            document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && _qidAutoOpen) ceQidAutoHide(true);
        });


(function() {
    function ceEditorGetRawValue() {
        var editor = document.getElementById('ceSModalContentEditor');
        if (!editor) return '';
        var clone = editor.cloneNode(true);
        // Replace badge spans with {QID} tokens
        clone.querySelectorAll('.ce-qid-badge').forEach(function(badge) {
            var qid = badge.getAttribute('data-qid');
            var text = document.createTextNode('{' + qid + '}');
            badge.parentNode.replaceChild(text, badge);
        });
        return clone.innerHTML;
    }                                       

    function ceEditorSetValue(html) {
        var editor = document.getElementById('ceSModalContentEditor');
        if (!editor) return;
        if (!html) { editor.innerHTML = ''; return; }
        var replaced = html.replace(/\{(QID)?(\d+)\}/gi, function(match, prefix, numId) {
            var q = window.__CE && window.__CE.questions
                ? window.__CE.questions.find(function(qq) { return String(qq.id) === numId; })
                : null;
            var label = q ? (q.label || '').substring(0, 30) : '';
            return '<span class="ce-qid-badge" data-qid="' + numId + '" contenteditable="true" title="' + (label || ('Q' + numId)) + '">Q' + numId + '</span>';
        });
        editor.innerHTML = replaced;
    }

    function ceEditorSyncToTextarea() {
        var ta = document.getElementById('ceSModalContent');
        if (ta) ta.value = ceEditorGetRawValue();
    }

    window._ceEditorSetValue    = ceEditorSetValue;
    window._ceEditorGetRawValue = ceEditorGetRawValue;
    window._ceEditorSyncToTextarea = ceEditorSyncToTextarea;

    document.addEventListener('DOMContentLoaded', function() {
        attachRichEditor();
    });
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(attachRichEditor, 300);
    }

    // function attachRichEditor() {
    //     var editor = document.getElementById('ceSModalContentEditor');
    //     if (!editor) return;

    //     editor.addEventListener('input', function() {
    //         var sel = window.getSelection();
    //         if (!sel || !sel.rangeCount) return;
    //         var range = sel.getRangeAt(0);
    //         var node = range.startContainer;
    //         if (node.nodeType !== Node.TEXT_NODE) return;
    //         var text = node.textContent;
    //         var pos = range.startOffset;

    //         var bracePos = -1;
    //         for (var i = pos - 1; i >= 0; i--) {
    //             if (text[i] === '{') { bracePos = i; break; }
    //             if (text[i] === '}' || text[i] === '\n') break;
    //         }

    //         if (bracePos >= 0) {
    //             var typed = text.substring(bracePos + 1, pos);
    //             _qidAutoStart = bracePos;
    //             window._qidAutoStart = bracePos;          
    //             window._qidAutoAnchorNode = node;         
    //             window._qidAutoAnchorOffset = pos;       
    //             ceQidAutoShowForEditor(editor);
    //             var searchEl = document.getElementById('ceQidAutoSearch');
    //             if (searchEl) { searchEl.value = typed; ceQidAutoFilter(typed); }
    //         } else if (typeof _qidAutoOpen !== 'undefined' && _qidAutoOpen) {
    //             ceQidAutoHide();
    //         }
    //     });

    //     editor.addEventListener('paste', function(e) {
    //         e.preventDefault();
    //         var text = (e.clipboardData || window.clipboardData).getData('text/plain');
    //         var html = (e.clipboardData || window.clipboardData).getData('text/html') || text;
    //         document.execCommand('insertText', false, text);
    //     });
    // }

    function attachRichEditor() {
    var editor = document.getElementById('ceSModalContentEditor');
    if (!editor) return;

    editor.addEventListener('click', function(e) {
        var badge = e.target.closest('.ce-qid-badge');
        if (!badge) return;
        e.stopPropagation();

        var currentQid = badge.getAttribute('data-qid');

        var existingPicker = document.getElementById('ceEditorInlineQidPicker');
        if (existingPicker) existingPicker.remove();

        var picker = document.createElement('div');
        picker.id = 'ceEditorInlineQidPicker';
        picker.style.cssText = 'position:fixed;z-index:9999999;background:#fff;border:1px solid #e5e7eb;border-radius:8px;'
            + 'box-shadow:0 4px 16px rgba(0,0,0,.14);width:340px;overflow:hidden;';

        picker.innerHTML = '<div style="padding:8px 10px;border-bottom:1px solid #f3f4f6;background:#fafafa;">'
            + '<input type="text" id="ceEditorQidPickerSearch" placeholder="Search by ID or label…" '
            + 'style="width:100%;padding:5px 9px;border:1px solid #e5e7eb;border-radius:5px;font-size:12px;outline:none;'
            + 'box-sizing:border-box;color:#374151;background:#fff;font-family:inherit;">'
            + '</div>'
            + '<div id="ceEditorQidPickerList" style="max-height:220px;overflow-y:auto;"></div>';

        document.body.appendChild(picker);

        var rect = badge.getBoundingClientRect();
        var top  = rect.bottom + 4;
        var left = rect.left;
        if (left + 340 > window.innerWidth - 8) left = window.innerWidth - 348;
        if (top + 260 > window.innerHeight - 8) top = rect.top - 264;
        picker.style.top  = Math.max(8, top)  + 'px';
        picker.style.left = Math.max(8, left) + 'px';

        function renderPickerList(filter) {
            var lc = (filter || '').toLowerCase().replace(/^qid/i, '');
            var listEl = document.getElementById('ceEditorQidPickerList');
            if (!listEl) return;
            var questions = (window.__CE && window.__CE.questions) ? window.__CE.questions : [];
            var items = questions.filter(function(q) {
                return !lc || String(q.id).includes(lc) || (q.label || '').toLowerCase().includes(lc);
            });
            if (!items.length) {
                listEl.innerHTML = '<div style="padding:10px 12px;font-size:12px;color:#9ca3af;font-style:italic;">No questions found</div>';
                return;
            }
            listEl.innerHTML = items.map(function(q) {
                var isCurrent = String(q.id) === String(currentQid);
                var labelPreview = (q.label || '').substring(0, 45) + ((q.label || '').length > 45 ? '…' : '');
                var safeLabel = labelPreview.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
                return '<div style="padding:7px 12px;cursor:pointer;display:flex;align-items:center;gap:10px;'
                    + 'border-bottom:1px solid #f3f4f6;background:' + (isCurrent ? '#fff4f0' : '#fff') + ';"'
                    + ' onmouseover="this.style.background=\'#f9fafb\'" onmouseout="this.style.background=\'' + (isCurrent ? '#fff4f0' : '#fff') + '\'"'
                    + ' onclick="ceEditorBadgeReplace(this, \'' + badge.getAttribute('data-qid') + '\', \'' + q.id + '\')">'
                    + '<span style="font-size:11px;font-weight:600;color:' + (isCurrent ? '#e85d2f' : '#6b7280') + ';font-family:monospace;white-space:nowrap;flex-shrink:0;">QID' + q.id + '</span>'
                    + '<span style="font-size:11px;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;">— ' + safeLabel + '</span>'
                    + (isCurrent ? '<span style="font-size:10px;color:#e85d2f;font-weight:700;flex-shrink:0;">current</span>' : '')
                    + '</div>';
            }).join('');
        }

        renderPickerList('');

        var searchEl = document.getElementById('ceEditorQidPickerSearch');
        if (searchEl) {
            searchEl.addEventListener('input', function() { renderPickerList(this.value); });
            setTimeout(function() { searchEl.focus(); }, 30);
        }

        window._ceEditorPickerTargetBadge = badge;

        function closePicker(ev) {
            if (ev && picker.contains(ev.target)) return;
            picker.remove();
            document.removeEventListener('mousedown', closePicker);
            document.removeEventListener('keydown', escClosePicker);
            window._ceEditorPickerTargetBadge = null;
        }
        function escClosePicker(ev) {
            if (ev.key === 'Escape') closePicker();
        }
        setTimeout(function() {
            document.addEventListener('mousedown', closePicker);
            document.addEventListener('keydown', escClosePicker);
        }, 10);
    });

    editor.addEventListener('input', function() {
        var sel = window.getSelection();
        if (!sel || !sel.rangeCount) return;
        var range = sel.getRangeAt(0);
        var node = range.startContainer;
        if (node.nodeType !== Node.TEXT_NODE) return;
        var text = node.textContent;
        var pos = range.startOffset;

        var bracePos = -1;
        for (var i = pos - 1; i >= 0; i--) {
            if (text[i] === '{') { bracePos = i; break; }
            if (text[i] === '}' || text[i] === '\n') break;
        }

        if (bracePos >= 0) {
            var typed = text.substring(bracePos + 1, pos);
            _qidAutoStart = bracePos;
            window._qidAutoStart = bracePos;
            window._qidAutoAnchorNode = node;
            window._qidAutoAnchorOffset = pos;
            ceQidAutoShowForEditor(editor);
            var searchEl = document.getElementById('ceQidAutoSearch');
            if (searchEl) { searchEl.value = typed; ceQidAutoFilter(typed); }
        } else if (typeof _qidAutoOpen !== 'undefined' && _qidAutoOpen) {
            ceQidAutoHide();
        }
    });

    editor.addEventListener('paste', function(e) {
        e.preventDefault();
        var text = (e.clipboardData || window.clipboardData).getData('text/plain');
        document.execCommand('insertText', false, text);
    });
}

window.ceEditorBadgeReplace = function(rowEl, oldQid, newQid) {
    var picker = document.getElementById('ceEditorInlineQidPicker');
    if (picker) picker.remove();

    if (String(oldQid) === String(newQid)) return;

    var badge = window._ceEditorPickerTargetBadge;
    window._ceEditorPickerTargetBadge = null;
    if (!badge || !badge.parentNode) return;

    var q = window.__CE && window.__CE.questions
        ? window.__CE.questions.find(function(qq){ return String(qq.id) === String(newQid); })
        : null;

    badge.setAttribute('data-qid', newQid);
    badge.title       = q ? (q.label || ('Q' + newQid)) : ('Q' + newQid);
    badge.textContent = 'Q' + newQid;
};

    function ceQidAutoShowForEditor(editor) {
        var pop  = document.getElementById('ceQidAutocomplete');
        var rect = editor.getBoundingClientRect();
        var sel  = window.getSelection();
        if (sel && sel.rangeCount) {
            var r    = sel.getRangeAt(0).getBoundingClientRect();
            var top  = r.bottom + 4;
            var left = r.left;
            if (top + 280 > window.innerHeight - 8) top = r.top - 280;
            if (left + 380 > window.innerWidth - 8) left = window.innerWidth - 390;
            pop.style.top  = Math.max(8, top)  + 'px';
            pop.style.left = Math.max(8, left) + 'px';
        } else {
            pop.style.top  = (rect.bottom + 4) + 'px';
            pop.style.left = rect.left + 'px';
        }
        pop.style.position = 'fixed';
        pop.style.width    = '380px';
        pop.style.display  = 'block';
        window._qidAutoOpen   = true;
        window._qidAutoActive = 0;
        var searchEl = document.getElementById('ceQidAutoSearch');
        if (searchEl) { searchEl.value = ''; ceQidAutoFilter(''); }
        setTimeout(function() { var s = document.getElementById('ceQidAutoSearch'); if (s) s.focus(); }, 30);
    }
}());

        }());
             </script>
                @endif
        </div>
        </div>
    </div>
</div>

<div id="cePasteDropdown" class="ce-paste-dropdown">
    <button type="button" class="ce-paste-opt" onclick="ceDoPasteFromClipboard()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
        Paste from clipboard
    </button>
    <button type="button" class="ce-paste-opt" onclick="ceOpenStdClauseModal()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
        Paste standard clause
    </button>
</div>
@endsection