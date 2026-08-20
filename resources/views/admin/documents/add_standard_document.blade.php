@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="container-fluid">

        <div class="nk-block-head doc-outer-div">
            <div class="nk-block-head-content wrapper">
                <div class="tab" id="mainTabBar">
                    <a href="javascript:void(0);" class="btn tab_btn active" id="tabConfiguration"
                       onclick="switchTab('configuration')">Configuration</a>
                    @if(isset($document) && $document != null)
                        <a href="javascript:void(0);" class="btn tab_btn" id="tabContractEditor"
                           onclick="switchTab('contractEditor')">Contract Editor</a>
                    @else
                        <a href="javascript:void(0);" class="btn tab_btn"
                           style="opacity:.5;cursor:not-allowed;" title="Save document first">Contract Editor</a>
                    @endif
                </div>
            </div>
        </div>

        <div id="panelConfiguration">
            <form action="{{ route('admin.document.add_standard_document') }}" id="documentForm"
                  method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="slug" id="slug" value="{{ $document->slug ?? '' }}">
                <input type="hidden" name="id"   id="id"   value="{{ $document->id   ?? '' }}">

                <div class="row main_section_div">
                    <div class="col col-md-8 doc-left-content">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <div class="col-md-12 doc-title">
                                    <div class="form-group">
                                        <label class="form-label mb-2" for="title"><b><h4>Title</h4></b></label>
                                        <input type="text" class="form-control form-control-lg" id="title"
                                               name="title" placeholder="Add title"
                                               value="{{ $document->title ?? '' }}">
                                        <span class="error text-danger" id="titleError"></span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <h5 class="mb-2">Description</h5>
                                    <textarea class="form-control" id="description"
                                              name="description">{{ $document->description ?? '' }}</textarea>
                                    <span class="error text-danger" id="descriptionError"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col col-md-4 doc-right-content">
                        <div class="card card-bordered card-preview mb-3">
                            <div class="card-inner">
                                @if(isset($document) && $document != null)
                                    <button class="btn btn-sm btn-primary submitDocument w-100"
                                            type="submit">Update</button>
                                @else
                                    <button class="btn btn-sm btn-primary submitDocument w-100"
                                            type="submit">Save</button>
                                @endif
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <h6 class="mb-3" style="font-size:13px;font-weight:700;color:#1a1a2e;display:flex;align-items:center;gap:6px;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                         stroke="#e85d2f" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                    Clause Versions
                                </h6>

                                <div class="mb-3">
                                    <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:6px;">
                                        Default (All States)
                                    </label>
                                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;display:flex;align-items:center;justify-content:space-between;">
                                        <span style="font-size:12px;color:#374151;font-weight:500;">
                                            @if(isset($document) && $document != null)
                                                {{ $document->title }}
                                            @else
                                                <em style="color:#9ca3af;">Save document first</em>
                                            @endif
                                        </span>
                                        @if(isset($document) && $document != null)
                                            <a href="javascript:void(0);" onclick="switchTab('contractEditor')"
                                               style="font-size:11px;color:#e85d2f;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-width="2.5">
                                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                @php
                                    $usStates = [
                                        'Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut',
                                        'Delaware','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa',
                                        'Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan',
                                        'Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada',
                                        'New Hampshire','New Jersey','New Mexico','New York','North Carolina',
                                        'North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island',
                                        'South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont',
                                        'Virginia','Washington','West Virginia','Wisconsin','Wyoming'
                                    ];
                                    $stateVersions = isset($document) ? ($document->stateVersions ?? collect()) : collect();
                                @endphp

                                <div id="stateVersionsContainer">
                                    <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:6px;">
                                        State-Specific Versions
                                    </label>

                                    @if(isset($document) && $document != null && $stateVersions->count() > 0)
                                        @foreach($stateVersions as $sv)
                                            <div class="state-version-row"
                                                 style="background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;margin-bottom:8px;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                                <div style="flex:1;">
                                                    <span style="font-size:11px;font-weight:700;color:#e85d2f;font-family:monospace;background:#fff4f0;padding:2px 7px;border-radius:4px;">
                                                        {{ is_array($sv->states) ? implode(', ', $sv->states) : ($sv->states ?? '') }}
                                                    </span>
                                                </div>
                                                <div style="display:flex;align-items:center;gap:6px;">
                                                    <button type="button"
                                                            onclick="editStates({{ $sv->id }}, '{{ is_array($sv->states) ? implode(',', $sv->states) : ($sv->states ?? '') }}')"
                                                            style="background:#f0f0f0;border:none;padding:2px 6px;font-size:11px;border-radius:4px;cursor:pointer;color:#FD5602;">
                                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                                                             stroke="currentColor" stroke-width="2.5">
                                                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                        </svg>
                                                        Edit State
                                                    </button>
                                                    <button type="button"
                                                            onclick="removeStateVersion(this, {{ $sv->id }})"
                                                            style="background:none;border:none;cursor:pointer;color:#9ca3af;display:inline-flex;align-items:center;"
                                                            onmouseover="this.style.color='#dc2626'"
                                                            onmouseout="this.style.color='#9ca3af'">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                             stroke="currentColor" stroke-width="2.5">
                                                            <polyline points="3 6 5 6 21 6"/>
                                                            <path d="M19 6l-1 14H6L5 6"/>
                                                            <path d="M10 11v6M14 11v6"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    <div id="dynamicStateVersionRows"></div>

                                    @if(isset($document) && $document != null)
                                        <button type="button" onclick="openAddStateVersionModal()"
                                                style="width:100%;background:#f9fafb;border:1.5px dashed #d1d5db;border-radius:8px;padding:9px;font-size:12px;font-weight:600;color:#6b7280;cursor:pointer;transition:all .12s;display:flex;align-items:center;justify-content:center;gap:6px;margin-top:4px;"
                                                onmouseover="this.style.borderColor='#e85d2f';this.style.color='#e85d2f'"
                                                onmouseout="this.style.borderColor='#d1d5db';this.style.color='#6b7280'">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2.5">
                                                <line x1="12" y1="5" x2="12" y2="19"/>
                                                <line x1="5" y1="12" x2="19" y2="12"/>
                                            </svg>
                                            Add State Version
                                        </button>
                                    @else
                                        <div style="font-size:11px;color:#9ca3af;text-align:center;padding:8px 0;font-style:italic;">
                                            Save the document first to add state versions
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        @if(isset($document) && $document != null)
        <div id="panelContractEditor" style="display:none;">

            <div class="nk-block-head doc-outer-div" style="margin-top:0;">
                <div class="nk-block-head-content wrapper contractEditor-confiBtn">

                    <div id="sceVersionBar" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:8px 0;">
                        <span style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap;">Editing version:</span>

                        <button type="button"
                                id="sceVerBtn_default"
                                onclick="sceSwitchVersion({{ $document->id }}, 'default')"
                                style="font-size:12px;font-weight:600;padding:5px 14px;border-radius:20px;cursor:pointer;transition:all .15s;border:1.5px solid #e85d2f;background:#e85d2f;color:#fff;">
                            Default
                        </button>

                        @foreach($stateVersions as $sv)
                            <button type="button"
                                    id="sceVerBtn_{{ $sv->id }}"
                                    onclick="sceSwitchVersion({{ $sv->id }}, '{{ is_array($sv->states) ? implode(', ', $sv->states) : ($sv->states ?? '') }}')"
                                    style="font-size:12px;font-weight:600;padding:5px 14px;border-radius:20px;cursor:pointer;transition:all .15s;border:1.5px solid #e5e7eb;background:#fff;color:#374151;">
                                {{ is_array($sv->states) ? implode(', ', $sv->states) : ($sv->states ?? '') }}
                            </button>
                        @endforeach

                        <div id="sceVersionBarDynamic" style="display:contents;"></div>
                    </div>

                    <div class="containSaveAndUpdate" style="margin-left:auto;">
                        <div class="ce-savebar">
                            <button type="button" onclick="sceSaveAll()" id="sceSaveBtn" class="ce-btn-save">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5">
                                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                                    <polyline points="17 21 17 13 7 13 7 21"/>
                                    <polyline points="7 3 7 8 15 8"/>
                                </svg>
                                Save All
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row main_section mt-2">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
                <link rel="stylesheet" href="{{ asset('assets/admin/css/document-contract-editor/document-contract-edit.css') }}">

                <div id="sceEditorPanel">

                    <div id="sceToast" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.45);z-index:999999;align-items:center;justify-content:center;">
                        <div style="background:#fff;border-radius:16px;padding:40px 36px 32px;width:340px;max-width:92vw;text-align:center;box-shadow:0 16px 48px rgba(0,0,0,0.18);animation:ceFadeIn .25s ease;">
                            <div id="sceToastIconWrap" style="width:72px;height:72px;border-radius:50%;border:3px solid #2dd4a7;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                                <svg id="sceToastIconSvg" width="34" height="34" viewBox="0 0 24 24" fill="none"
                                     stroke="#2dd4a7" stroke-width="2.5" stroke-linecap="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                            <p id="sceToastTitle" style="font-size:22px;font-weight:700;color:#1a1a2e;margin:0 0 10px;">Success!</p>
                            <p id="sceToastMsg"   style="font-size:14px;color:#6b7280;margin:0 0 26px;line-height:1.6;"></p>
                            <button type="button" onclick="sceCloseToast()"
                                    style="background:#2dd4a7;color:#fff;border:none;border-radius:8px;padding:11px 40px;font-size:15px;font-weight:600;cursor:pointer;">OK</button>
                        </div>
                    </div>

                    <div id="sceLoadingState" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 0;gap:14px;">
                        <div style="width:32px;height:32px;border:3px solid #e5e7eb;border-top-color:#e85d2f;border-radius:50%;animation:ceSpin 1s linear infinite;"></div>
                        <p style="color:#9ca3af;margin:0;font-size:13px;">Loading&hellip;</p>
                    </div>

                    <div id="sceErrorState" style="display:none;flex-direction:column;align-items:center;justify-content:center;padding:80px 0;gap:12px;">
                        <div style="font-size:38px;">&#9888;</div>
                        <p id="sceErrorMsg" style="color:#dc2626;margin:0;font-size:13px;font-weight:500;"></p>
                        <button type="button" onclick="sceLoadData()" class="ce-btn">Retry</button>
                    </div>

                    <div id="sceEditorMain" style="display:none;">
                        <div class="ce-wrap">

                            <div class="ce-left" id="sce-left-panel">
                                <div class="ce-left-head">
                                    <div class="ce-panel-title">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                             stroke="#e85d2f" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/>
                                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                                        </svg>
                                        Question
                                        <span id="sceQuestionCount"
                                              style="background:#e85d2f;color:#fff;border-radius:10px;padding:1px 8px;font-size:11px;margin-left:2px;">0</span>
                                    </div>
                                    <div style="display:flex;gap:8px;align-items:center;">
                                        <input type="checkbox" id="sceSelectAllCb" title="Select all"
                                               onchange="sceToggleSelectAll(this)"
                                               style="width:16px;height:16px;accent-color:#e85d2f;cursor:pointer;">
                                        <input type="text" id="sceSearchQ" placeholder="Search questions…"
                                               oninput="sceFilterQ(this.value)"
                                               class="form-control form-control-sm" style="flex:1;">
                                        <button type="button" onclick="sceAddNewQ()" class="ce-btn sm">+ Add</button>
                                    </div>
                                </div>

                                <div id="sceBulkBar">
                                    <button type="button" class="ce-bulk-btn" onclick="sceBulkCopy()">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2.5">
                                            <rect x="9" y="9" width="13" height="13" rx="2"/>
                                            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                                        </svg>
                                        Copy
                                    </button>
                                    <button type="button" class="ce-bulk-btn" onclick="sceBulkDelete()">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2.5">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v6M14 11v6"/>
                                        </svg>
                                        Delete
                                    </button>
                                </div>

                                <div id="sceQuestionsList" class="ce-qlist"></div>
                            </div>

                            <div class="ce-right" id="sce-right-panel">
                                <div class="ce-right-head">
                                    <div class="ce-panel-title" style="margin:0;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                             stroke="#e85d2f" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                        </svg>
                                        Contract Text
                                        <span id="sceSectionCount"
                                              style="background:#e85d2f;color:#fff;border-radius:10px;padding:1px 8px;font-size:11px;margin-left:2px;">0</span>
                                    </div>
                                    <div style="display:flex;gap:8px;align-items:center;width:100%;">
                                        <input type="checkbox" id="sceSelectAllSectionsCb" title="Select all sections"
                                               onchange="sceToggleSelectAllSections(this)"
                                               style="width:16px;height:16px;accent-color:#e85d2f;cursor:pointer;flex-shrink:0;">
                                        <input type="text" id="sceSearchS" placeholder="Search sections…"
                                               oninput="sceFilterS(this.value)"
                                               class="form-control form-control-sm" style="flex:1;">
                                        <button type="button" onclick="sceAddNewS()" class="ce-btn sm">+ Add</button>
                                    </div>
                                </div>

                                <div id="sceSBulkBar">
                                    <button type="button" class="ce-bulk-btn" onclick="sceBulkCopyS()">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2.5">
                                            <rect x="9" y="9" width="13" height="13" rx="2"/>
                                            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                                        </svg>
                                        Copy
                                    </button>
                                    <button type="button" class="ce-bulk-btn" onclick="sceBulkDeleteS()">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2.5">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v6M14 11v6"/>
                                        </svg>
                                        Delete
                                    </button>
                                </div>

                                <div id="sceContractPreview" class="ce-preview"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Question Modal ── --}}
                <div id="sceQuestionModal" class="ce-modal">
                    <div class="ce-mbox" style="width:700px;">
                        <div class="ce-mhead">
                            <div style="display:flex;align-items:center;gap:10px;flex:1;">
                                <span id="sceQModalBadge"
                                    style="display:none;font-family:monospace;font-size:12px;background:#fff4f0;color:#e85d2f;padding:3px 10px;border-radius:4px;"></span>
                                <div class="inner-side-icon-wrap">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <select id="sceQModalType" onchange="sceQTypeChange()"
                                            style="font-size:14px;font-weight:600;appearance:none;color:#526484;background:transparent;border:0;padding:0 5px;cursor:pointer;outline:none;min-width:90px;">
                                        <option value="textbox">Text Box</option>
                                        <option value="textarea">Text Area</option>
                                        <option value="radio-button">Radio Button</option>
                                        <option value="dropdown">Dropdown</option>
                                        <option value="checkbox">Checkbox</option>
                                        <option value="date">Date</option>
                                        <option value="number">Number</option>
                                    </select>                                        
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5"
                                        style="pointer-events:none;color:#9ca3af;">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </div>
                            </div>
                            <button type="button" class="ce-mclose" onclick="sceCloseQModal()">&times;</button>
                        </div>
                        <div class="ce-mbody">
                            <input type="hidden" id="sceQModalId">
                
                            <div class="ce-fg">
                                <label class="ce-flabel">Question Label</label>
                                <div class="QtextAddButton" id="sceQModalLabelWrap">
                                    <textarea id="sceQModalLabel" class="form-control" rows="2"
                                            placeholder="e.g. What is the tenant's full name?"></textarea>
                                    <button type="button" onclick="sceAddConditionFromModal()" class="ce-svg-btn"
                                        style="width:26px;height:26px;border:none;border-radius:50%;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:rgb(60,77,98);flex-shrink:0;transition:background .12s;"
                                        title="Add show-if condition row">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="sceQModalCondRows" style="margin-bottom:8px;"></div>
                
                            <div id="sceQModalPlaceholderWrap" class="ce-fg" style="display:none;">
                                <label class="ce-flabel" id="sceQModalPlaceholderLabel">Placeholder</label>
                                <input type="text" id="sceQModalPlaceholder" class="form-control"
                                    placeholder="e.g. Enter your full name…">
                            </div>
                
                            <div class="row">
                                <div class="col-md-4 ce-fg goto_wid_inner">
                                    <label class="ce-flabel">Go To</label>
                                    <select id="sceQModalGoTo" class="form-select">
                                        <option value="">— None (next) —</option>
                                    </select>
                                </div>
                            </div>
                
                            <div id="sceQModalOptionsWrap" class="ce-fg" style="display:none;">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                                    <label class="ce-flabel" id="sceQModalOptionsLabel" style="margin:0;">Options</label>
                                </div>
                                <div id="sceQModalOptionsList" style="margin-bottom:8px;"></div>
                            </div>
                
                            <div class="ce-fg" id="sceQModalCondGoToWrap">
                                <div id="sceCondGroupsContainer"></div>
                                <button type="button" onclick="sceAddCondGroup()"
                                    style="background:#f3f4f6;border-radius:130px;width:100%;padding:9px;font-size:12px;font-weight:600;color:rgb(107,114,128);cursor:pointer;transition:all .12s;display:flex;align-items:center;justify-content:center;gap:6px; width:fit-content; margin-left:auto; padding: 7px 18px; border:none;"
                                    onmouseover="this.style.borderColor='#e85d2f';this.style.color='#e85d2f'"
                                    onmouseout="this.style.borderColor='#d1d5db';this.style.color='#6b7280'">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Condition
                                </button>
                            </div>
                
                            <div class="ce-fg">
                                <label class="ce-flabel">Help Text</label>
                                <textarea id="sceQModalInfo" class="form-control" rows="2"
                                        placeholder="Optional guidance shown to the user…"></textarea>
                            </div>
                        </div>
                        <div class="ce-mfoot">
                            <button type="button" onclick="sceCloseQModal()"
                                    class="btn btn-sm btn-light" style="border:1px solid #d1d5db;">Cancel</button>
                            <button type="button" onclick="sceSaveQuestion()" class="ce-btn-save">Save Question</button>
                        </div>
                    </div>
                </div>

                <div id="sceSectionModal" class="ce-modal">
                    <div class="ce-mbox wide" style="width:680px;">
                        <div class="ce-mhead">
                            <div style="display:flex;align-items:center;gap:10px;flex:1;">
                                <p class="ce-mtitle" id="sceSModalTitle"
                                   style="margin:0;display:inline-block;font-family:monospace;font-size:12px;background:#fff4f0;padding:3px;border-radius:4px;font-weight:500;"></p>
                                <div class="inner-side-icon-wrap">
                                <i class="fa-solid fa-pen-to-square"></i>
                                    <select id="sceSModalType" onchange="sceSTypeChange()"
                                            style="font-size:14px;font-weight:600;appearance:none;color:#526484;background:transparent;border:0;padding:0 5px;cursor:pointer;outline:none;min-width:75px;">
                                        <option value="content_heading">Headline</option>
                                        <option value="content">Content</option>
                                        <option value="signature_field">Signature</option>
                                    </select>
                                    
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                         style="pointer-events:none;color:#9ca3af;">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </div>
                            </div>
                            <button type="button" class="ce-mclose" onclick="sceCloseSModal()">&times;</button>
                        </div>
                        <div class="ce-mbody">
                            <input type="hidden" id="sceSModalId">
                            <div class="row">
                                <div class="col-md-4 ce-fg">
                                    <select id="sceSModalAlign" class="form-select">
                                        <option value="left">Left</option>
                                        <option value="center">Center</option>
                                        <option value="right">Right</option>
                                        <option value="justify">Justify</option>
                                    </select>
                                </div>
                            </div>
                            <div class="ce-fg">
                                <label class="ce-flabel">Text</label>
                                <textarea id="sceSModalContent" rows="10" class="form-control"
                                          placeholder="Enter content. Use {QID123} to reference questions."></textarea>
                            </div>
                            <div class="contract-blur-button">
                                <div class="col-md-4 ce-fg blur-side-check" style="display: flex;gap: 8px;margin-bottom: 22px;justify-content: end; margin-top: 10px;">
                                    <input type="checkbox" id="sceSModalBlur" class="form-check-input" value="1">
                                    <label class="ce-flabel">Blur</label>
                                </div>
                            </div>
                 
                            <div id="sceSModalCondWrap" class="ce-fg" style="display:none;">
                                <div id="sceSCondGroupsContainer"></div>
                                <div style="display:flex;justify-content:flex-end;">
                                    <button type="button" id="sceAddSectionCondBtn" onclick="sceAddSectionCondGroup()"
                                        style="background:#f3f4f6;border:none;border-radius:130px;padding:7px 18px;font-size:12px;font-weight:600;color:rgb(107,114,128);cursor:pointer;transition:all .12s;display:flex;align-items:center;justify-content:center;gap:6px;"
                                        onmouseover="this.style.color='#e85d2f'"
                                        onmouseout="this.style.color='#6b7280'">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        Condition
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="ce-mfoot">
                            <button type="button" onclick="sceCloseSModal()"
                                    class="btn btn-sm btn-light" style="border:1px solid #d1d5db;">Cancel</button>
                            <button type="button" onclick="sceSaveSection()" class="ce-btn-save">
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

                <div id="sceQidAuto"
                     style="display:none;position:fixed;z-index:999999;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.10);width:380px;overflow:hidden;">
                    <div style="padding:8px 10px;border-bottom:1px solid #f3f4f6;background:#fafafa;">
                        <input type="text" id="sceQidAutoSearch"
                               placeholder="Search by ID or label…"
                               oninput="sceQidAutoFilter(this.value)"
                               style="width:100%;padding:5px 9px;border:1px solid #e5e7eb;border-radius:5px;font-size:12px;outline:none;box-sizing:border-box;color:#374151;font-family:inherit;">
                    </div>
                    <div id="sceQidAutoList" style="max-height:240px;overflow-y:auto;"></div>
                </div>

            </div>
        </div>
        @endif

    </div>
</div>

<div id="stateVersionModal"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:99999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;padding:28px 24px 22px;width:460px;max-width:94vw;box-shadow:0 16px 48px rgba(0,0,0,.18);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
            <span style="font-size:15px;font-weight:700;color:#1a1a2e;">Add State-Specific Version</span>
            <button type="button" onclick="closeAddStateVersionModal()"
                    style="background:none;border:none;cursor:pointer;font-size:22px;color:#9ca3af;line-height:1;">&times;</button>
        </div>
        <p style="font-size:12px;color:#6b7280;margin-bottom:14px;line-height:1.5;">
            Select the state for which this clause will have a different version. One state per version.
        </p>
        <input type="text" id="stateSearch" placeholder="Search states…"
               oninput="filterStatesModal(this.value)"
               style="width:100%;padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none;margin-bottom:10px;box-sizing:border-box;"
               onfocus="this.style.borderColor='#e85d2f'" onblur="this.style.borderColor='#e5e7eb'">
        <div id="stateRadioList"
             style="max-height:240px;overflow-y:auto;display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:16px;">
            @foreach($usStates as $state)
                <label class="state-modal-item"
                       style="display:flex;align-items:center;gap:6px;font-size:12px;color:#374151;cursor:pointer;padding:5px 8px;border:1px solid #f3f4f6;border-radius:6px;transition:background .1s;"
                       onmouseover="this.style.background='#fff4f0'" onmouseout="this.style.background=''">
                    {{-- CHANGED: radio instead of checkbox, name="state_version_select" ensures only one --}}
                    <input type="radio" class="state-version-rb" name="state_version_select" value="{{ $state }}"
                           style="width:13px;height:13px;accent-color:#e85d2f;cursor:pointer;flex-shrink:0;">
                    {{ $state }}
                </label>
            @endforeach
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;">
            <button type="button" onclick="closeAddStateVersionModal()"
                    class="btn btn-sm btn-light" style="border:1px solid #d1d5db;font-size:12px;">Cancel</button>
            <button type="button" id="modalActionBtn" onclick="confirmAddStateVersion()"
                    style="background:#e85d2f;color:#fff;border:none;border-radius:6px;padding:8px 20px;font-size:12px;font-weight:600;cursor:pointer;">
                Create Version
            </button>
        </div>
    </div>
</div>

<script>
$('#title').on('keyup', function () {
    const name = $(this).val();
    $('#slug').val(name.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,''));
});

$(document).ready(function () {
    $('.submitDocument').click(function (e) {
        e.preventDefault();
        let hasError = false;
        $('#titleError').text('').hide();
        $('#descriptionError').text('').hide();
        if (!$('#title').val()) { $('#titleError').text('Title is required').show(); hasError = true; }
        if (!$('#description').val()) { $('#descriptionError').text('Description is required').show(); hasError = true; }
        if (!hasError) $('#documentForm').submit();
    });
});


function switchTab(tab) {
    if (tab === 'configuration') {
        document.getElementById('panelConfiguration').style.display = '';
        var ep = document.getElementById('panelContractEditor');
        if (ep) ep.style.display = 'none';
        document.getElementById('tabConfiguration').classList.add('active');
        var et = document.getElementById('tabContractEditor');
        if (et) et.classList.remove('active');
    } else {
        document.getElementById('panelConfiguration').style.display = 'none';
        var ep = document.getElementById('panelContractEditor');
        if (ep) { ep.style.display = ''; sceLoadData(); }
        document.getElementById('tabConfiguration').classList.remove('active');
        var et = document.getElementById('tabContractEditor');
        if (et) et.classList.add('active');
    }
}

function switchEditorTab(tab) {
    ['both','questions','contract'].forEach(function(t){
        document.getElementById('edTab-'+t).classList.remove('active');
    });
    document.getElementById('edTab-'+tab).classList.add('active');

    var lp = document.getElementById('sce-left-panel');
    var rp = document.getElementById('sce-right-panel');
    if (!lp || !rp) return;
    if (tab === 'both')      { lp.style.display = ''; rp.style.display = ''; }
    else if (tab === 'questions') { lp.style.display = ''; rp.style.display = 'none'; }
    else                     { lp.style.display = 'none'; rp.style.display = ''; }
}


function openAddStateVersionModal() {
    document.getElementById('stateVersionModal').style.display = 'flex';
    document.getElementById('stateSearch').value = '';
    filterStatesModal('');
    document.querySelectorAll('.state-version-rb').forEach(rb => rb.checked = false);
    var btn = document.getElementById('modalActionBtn');
    btn.textContent = 'Create Version';
    btn.onclick = confirmAddStateVersion;
}
function closeAddStateVersionModal() {
    document.getElementById('stateVersionModal').style.display = 'none';
}
function filterStatesModal(v) {
    var lc = v.toLowerCase();
    document.querySelectorAll('.state-modal-item').forEach(function(item){
        item.style.display = item.textContent.toLowerCase().includes(lc) ? '' : 'none';
    });
}
document.getElementById('stateVersionModal').addEventListener('click', function(e){
    if (e.target === this) closeAddStateVersionModal();
});

function confirmAddStateVersion() {
    var selectedRb = document.querySelector('.state-version-rb:checked');
    if (!selectedRb) { alert('Please select a state.'); return; }
    var selected = [selectedRb.value];

    closeAddStateVersionModal();
    var documentId = {{ $document->id ?? 'null' }};
    if (!documentId) return;
    fetch('/admin-dashboard/standard/section/document/add-state-version', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ parent_id: documentId, states: selected, title: '{{ addslashes($document->title ?? '') }}' })
    })
    .then(r => r.json())
    .then(function(data) {
        if (!data.success) { alert(data.message || 'Failed.'); return; }
        var stateLabel = selectedRb.value;
        var newDocId   = data.document_id;
        var newSlug    = data.slug ?? '';

        /* Add version button to the contract editor version bar */
        var verBar = document.getElementById('sceVersionBarDynamic');
        if (verBar) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.id   = 'sceVerBtn_' + newDocId;
            btn.textContent = stateLabel;
            btn.style.cssText = 'font-size:12px;font-weight:600;padding:5px 14px;border-radius:20px;cursor:pointer;transition:all .15s;border:1.5px solid #e5e7eb;background:#fff;color:#374151;';
            btn.onclick = function(){ sceSwitchVersion(newDocId, stateLabel); };
            verBar.appendChild(btn);
        }

        var row = document.createElement('div');
        row.className = 'state-version-row';
        row.style.cssText = 'background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;margin-bottom:8px;display:flex;align-items:center;justify-content:space-between;gap:8px;';
        row.setAttribute('data-doc-id', newDocId);
        row.innerHTML =
            '<div style="flex:1;"><span style="font-size:11px;font-weight:700;color:#e85d2f;font-family:monospace;background:#fff4f0;padding:2px 7px;border-radius:4px;">'+stateLabel+'</span></div>'
            +'<div style="display:flex;align-items:center;gap:6px;">'
            +(newSlug ? '<a href="/admin-dashboard/standard/section/document/edit/'+newSlug+'" style="font-size:11px;color:#374151;text-decoration:none;">Config</a>' : '')
            +'<button type="button" onclick="editStates('+newDocId+',\''+stateLabel+'\')" style="background:#f0f0f0;border:none;padding:2px 6px;font-size:11px;border-radius:4px;cursor:pointer;color:#FD5602;">'
            +'<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit State</button>'
            +'<button type="button" onclick="removeStateVersion(this,'+newDocId+')" style="background:none;border:none;cursor:pointer;color:#9ca3af;display:inline-flex;align-items:center;" onmouseover="this.style.color=\'#dc2626\'" onmouseout="this.style.color=\'#9ca3af\'">'
            +'<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg></button>'
            +'</div>';
        document.getElementById('dynamicStateVersionRows').appendChild(row);
    })
    .catch(() => alert('Something went wrong.'));
}

function removeStateVersion(btn, docId) {
    if (!confirm('Delete this state-specific version? This cannot be undone.')) return;
    fetch('/admin-dashboard/standard/section/document/delete-state-version/' + docId, {
        method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(function(data) {
        if (!data.success) { alert(data.message || 'Failed.'); return; }
        var row = btn.closest('.state-version-row');
        if (row) row.remove();
        var vbtn = document.getElementById('sceVerBtn_' + docId);
        if (vbtn) vbtn.remove();
        if (window.__SCE && window.__SCE.documentId === docId) {
sceSwitchVersion({{ $document->id ?? 'null' }}, 'default');
        }
    })
    .catch(() => alert('Something went wrong.'));
}

function editStates(docId, currentState) {
    openAddStateVersionModal();
    document.querySelectorAll('.state-version-rb').forEach(function(rb){
        rb.checked = (rb.value === currentState);
    });
    var btn = document.getElementById('modalActionBtn');
    btn.textContent = 'Update State';
    btn.onclick = function() { updateStateVersion(docId); };
}

function updateStateVersion(docId) {
    var selectedRb = document.querySelector('.state-version-rb:checked');
    if (!selectedRb) { alert('Please select a state.'); return; }
    fetch('/admin-dashboard/standard/section/document/update-state-version/' + docId, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ states: [selectedRb.value] })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            var vbtn = document.getElementById('sceVerBtn_' + docId);
            if (vbtn) vbtn.textContent = selectedRb.value;
            alert('State updated successfully.');
            location.reload();
        } else {
            alert(data.message || 'Failed.');
        }
    })
    .catch(() => alert('Something went wrong.'));
}


@if(isset($document) && $document != null)
(function () {
    'use strict';

    var SCE = {
        documentId          : {{ $document->id }},
        csrfToken           : '{{ csrf_token() }}',
        questions           : [],
        sections            : [],
        sectionsFull        : [],
        deletedQuestionIds  : [],
        deletedSectionIds   : [],
        editingQIdx         : null,
        editingSIdx         : null,
        _selQ               : new Set(),
        _selS               : new Set(),
        _clipQ              : null,
        _clipS              : null,
        _bulkClipQ          : null,
        _bulkClipS          : null,
        _loaded             : false,
    };
    window.__SCE = SCE;

    window.sceSwitchVersion = function(docId, label) {
        if (SCE.documentId === docId) return;

        document.querySelectorAll('[id^=sceVerBtn_]').forEach(function(b){
            b.style.background = '#fff';
            b.style.color      = '#374151';
            b.style.borderColor = '#e5e7eb';
        });
        var activeBtn = document.getElementById('sceVerBtn_' + (docId === {{ $document->id }} ? 'default' : docId));
        if (activeBtn) {
            activeBtn.style.background   = '#e85d2f';
            activeBtn.style.color        = '#fff';
            activeBtn.style.borderColor  = '#e85d2f';
        }

        SCE.documentId         = docId;
        SCE.questions          = [];
        SCE.sections           = [];
        SCE.sectionsFull       = [];
        SCE.deletedQuestionIds = [];
        SCE.deletedSectionIds  = [];
        SCE._selQ              = new Set();
        SCE._selS              = new Set();
        SCE._loaded            = false;

        document.getElementById('sceEditorMain').style.display   = 'none';
        document.getElementById('sceLoadingState').style.display = 'flex';
        document.getElementById('sceErrorState').style.display   = 'none';

        sceLoadData();
    };

    function esc(s) {
        return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }
    function setStatus(m) { var el=document.getElementById('sceAutoSaveStatus'); if(el) el.textContent = m; }
    function show(id) { var e=document.getElementById(id); if(e) e.style.display=''; }
    function hide(id) { var e=document.getElementById(id); if(e) e.style.display='none'; }

    window.sceCloseToast = function() { hide('sceToast'); };
    window.sceShowToast  = function(msg, isError) {
        var toast = document.getElementById('sceToast');
        var iw    = document.getElementById('sceToastIconWrap');
        var is_   = document.getElementById('sceToastIconSvg');
        var tit   = document.getElementById('sceToastTitle');
        var okBtn = toast.querySelector('button');
        document.getElementById('sceToastMsg').textContent = msg;
        if (isError) {
            iw.style.borderColor = '#ef4444'; is_.setAttribute('stroke','#ef4444');
            is_.innerHTML = '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>';
            tit.textContent = 'Error!'; tit.style.color='#ef4444'; okBtn.style.background='#ef4444';
        } else {
            iw.style.borderColor = '#2dd4a7'; is_.setAttribute('stroke','#2dd4a7');
            is_.innerHTML = '<polyline points="20 6 9 17 4 12"/>';
            tit.textContent = 'Success!'; tit.style.color='#1a1a2e'; okBtn.style.background='#2dd4a7';
        }
        toast.style.display = 'flex';
    };

    window.sceLoadData = function() {
        if (SCE._loaded) return;
        document.getElementById('sceLoadingState').style.display = 'flex';
        document.getElementById('sceEditorMain').style.display   = 'none';
        document.getElementById('sceErrorState').style.display   = 'none';

        Promise.all([
            fetch('/admin-dashboard/api/sce-questions/' + SCE.documentId, {
                headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
            }).then(r => r.json()),
            fetch('/admin-dashboard/api/sce-sections/' + SCE.documentId, {
                headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
            }).then(r => r.json()),
        ]).then(function(res) {
            if (!res[0].success || !res[1].success) {
                showError('Server error loading data.');
                return;
            }
            SCE.questions = (res[0].questions || []).map(function(q) {
                return {
                    id          : q.id,
                    type        : q.type || 'textbox',
                    label       : (q.questionData && q.questionData.question_label)       || '',
                    info        : (q.questionData && q.questionData.question_info_text)   || '',
                    placeholder : (q.questionData && q.questionData.text_box_placeholder) || '',
                    goTo        : q.go_to || null,
                    options     : (q.options || []).map(o => ({id:o.id, label:o.option_label, value:o.option_value})),
                    condGoTo : (function(){
                        var rawList = q.condGoTo || q.cond_go_to || [];
                        if (!Array.isArray(rawList)) return [];
                        return rawList
                            .filter(function(cg){ var g=cg.goto||cg.go_to||''; return g!==null&&g!==undefined&&String(g)!==''; })
                            .map(function(cg){
                                return {
                                    goto       : String(cg.goto||cg.go_to||''),
                                    conditions : (cg.conditions||[]).map(function(c){
                                        return { qid:String(c.qid||c.question_id||''), type:c.type||'is_equal_to', value:String(c.value||c.condition_value||'') };
                                    }),
                                };
                            });
                    }()),
                    conditions : (function(){
                        var rawConds = q.conditions||q.show_conditions||[];
                        if (!Array.isArray(rawConds)) return [];
                        return rawConds.map(function(c){
                            return { label:c.label||'', qid:String(c.qid||c.question_id||''), value:String(c.value||c.condition_value||'') };
                        });
                    }()),
                    isNew : false,
                };
            });

            SCE.sections = (res[1].sections || []).map(function(s) {
                return {
                    id                  : s.id,
                    type                : s.type || 'content',
                    content             : s.content || '',
                    text_align          : s.text_align || 'left',
                    secure_blur_content : s.secure_blur_content || 0,
                    conditions          : (function(){
                        var rawConds = s.conditions || [];
                        if (!Array.isArray(rawConds)) return [];
                        return rawConds.map(function(c){
                            return { qid:String(c.qid||c.question_id||''), type:c.type||'is_equal_to', value:String(c.value||c.condition_value||'') };
                        });
                    }()),
                    isNew               : false,
                };
            });
            SCE.sectionsFull = SCE.sections.slice();
            SCE._loaded = true;
            document.getElementById('sceLoadingState').style.display = 'none';
            document.getElementById('sceEditorMain').style.display   = 'block';
            renderQ();
            renderS();
        }).catch(function(err) {
            document.getElementById('sceLoadingState').style.display = 'none';
            document.getElementById('sceErrorState').style.display   = 'flex';
            document.getElementById('sceErrorMsg').textContent        = 'Could not load: ' + err.message;
        });
    };

    function renderQ(filter) {
        var list = document.getElementById('sceQuestionsList');
        document.getElementById('sceQuestionCount').textContent = SCE.questions.length;
        var lc = (filter || '').toLowerCase();
        var items = SCE.questions.filter(function(q){ return !lc || (q.label||'').toLowerCase().includes(lc); });

        if (!items.length) {
            list.innerHTML = '<div style="text-align:center;color:#d1d5db;padding:40px 14px;font-size:12px;">'+(filter?'No matching questions.':'No questions yet.<br>Click <b>+ Add</b> to create one.')+'</div>';
            initQSortable();
            return;
        }

        list.innerHTML = items.map(function(q) {
            var ri = SCE.questions.indexOf(q);
            var iStyle = 'width:100%;padding:6px 9px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:11.5px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;';
            var typeLabel = (q.type||'textbox').replace(/-/g,' ').replace(/\b\w/g,c=>c.toUpperCase());
            var prefix = '<div style="font-size:10px;font-weight:600;color:#9ca3af;font-family:monospace;margin-bottom:4px;">'+esc(typeLabel)+'</div>';

            var optionGoTos = {};
            if (q.condGoTo && q.condGoTo.length) {
                q.condGoTo.forEach(function(cg){
                    if (cg.conditions && cg.conditions.length===1) {
                        var c = cg.conditions[0];
                        if (String(c.qid)===String(q.id) && (c.type==='is_equal_to'||c.type===''))
                            optionGoTos[c.value] = cg.goto;
                    }
                });
            }

            var fieldHtml = '';
            if (q.type==='radio-button') {
                if (!(q.options||[]).length) {
                    fieldHtml = prefix+'<span style="font-size:11px;color:#d1d5db;font-style:italic;">No options defined</span>';
                } else {
                    fieldHtml = prefix+'<div style="display:flex;flex-direction:column;gap:5px;">'
                        +(q.options||[]).map(function(o){
                            var val = o.value||o.label;
                            var gotoBadge = '';
                            if (optionGoTos[val]) {
                                var dest = optionGoTos[val]==='END'?'Checkout':'Q'+optionGoTos[val];
                                gotoBadge = '<span style="background:#e6f4ea;color:#1e8e3e;font-size:10px;padding:1px 5px;border-radius:3px;margin-left:6px;">→ '+dest+'</span>';
                            }
                            return '<label style="display:flex;align-items:center;gap:6px;font-size:11.5px;color:#374151;cursor:pointer;">'
                                +'<input type="radio" name="sceq_radio_'+q.id+'" style="accent-color:#e85d2f;width:13px;height:13px;flex-shrink:0;">'
                                +esc(o.label)+gotoBadge+'</label>';
                        }).join('')+'</div>';
                }
            } else if (q.type==='dropdown') {
                fieldHtml = prefix+'<select style="'+iStyle+'cursor:pointer;">'
                    +'<option style="color:#9ca3af;">— Select an option —</option>'
                    +(q.options||[]).map(function(o){
                        var val = o.value||o.label;
                        var gotoLabel = optionGoTos[val] ? ' → '+(optionGoTos[val]==='END'?'Checkout':'Q'+optionGoTos[val]) : '';
                        return '<option value="'+esc(val)+'">'+esc(o.label)+gotoLabel+'</option>';
                    }).join('')+'</select>';
            } else if (q.type==='checkbox') {
                fieldHtml = prefix+'<label style="display:flex;align-items:center;gap:6px;font-size:11.5px;color:#374151;cursor:pointer;"><input type="checkbox" style="accent-color:#e85d2f;width:13px;height:13px;">'+esc(q.label||'Checkbox')+'</label>';
            } else if (q.type==='date') {
                fieldHtml = prefix+'<input type="date" style="'+iStyle+'">';
            } else if (q.type==='textarea') {
                fieldHtml = prefix+'<textarea rows="2" style="'+iStyle+'resize:vertical;" placeholder="'+esc(q.placeholder||'Long answer…')+'"></textarea>';
            } else if (q.type==='number') {
                fieldHtml = prefix+'<input type="number" style="'+iStyle+'" placeholder="'+esc(q.placeholder||'0')+'">';
            } else {
                fieldHtml = prefix+'<input type="text" style="'+iStyle+'" placeholder="'+esc(q.placeholder||'Short answer…')+'">';
            }

            var goTosHtml = '';
            if (q.goTo) {
                var dest = q.goTo==='END'?'Checkout':'Q'+q.goTo;
                goTosHtml += '<div style="margin-bottom:3px;"><span style="display:inline-flex;align-items:center;gap:1px;background:#e6f4ea;color:#1e8e3e;border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700;">→ '+esc(dest)+'</span></div>';
            }
            if (q.condGoTo && q.condGoTo.length) {
                q.condGoTo.forEach(function(cg){
                    if (!cg.goto && cg.goto!==0) return;
                    var destLabel = cg.goto==='END'?'Checkout':'Q'+cg.goto;
                    var isSimpleRadio = (q.type==='radio-button'||q.type==='dropdown')
                        && (cg.conditions||[]).length===1
                        && String(cg.conditions[0].qid)===String(q.id)
                        && (cg.conditions[0].type==='is_equal_to'||cg.conditions[0].type==='');
                    if (isSimpleRadio) return;
                    var condTexts = (cg.conditions||[]).map(function(c){
                        var op = c.type||'is_equal_to';
                        if(op==='is_equal_to'||op==='') op='=';
                        else if(op==='is_not_equal_to') op='!=';
                        else if(op==='is_greater_than') op='>';
                        else if(op==='is_less_than') op='<';
                        return 'Q'+c.qid+' '+op+' '+esc(c.value);
                    }).join(' AND ');
                    goTosHtml += '<div style="margin-bottom:3px;display:flex;align-items:center;gap:5px;flex-wrap:wrap;">'
                        +'<span style="display:inline-flex;align-items:center;gap:1px;background:#e6f4ea;color:#1e8e3e;border-radius:5px;padding:2px 8px;font-size:10px;font-weight:700;">→ '+esc(destLabel)+'</span>'
                        +(condTexts?'<span style="font-size:10px;color:#6b7280;background:#f3f4f6;border-radius:4px;padding:1px 6px;font-family:monospace;">'+condTexts+'</span>':'')
                        +'</div>';
                });
            }

            var showIfHtml = '';
            if (q.conditions && q.conditions.length) {
                var condParts = q.conditions.map(function(c){
                    return 'Q'+c.qid+' = <b>'+esc(c.value)+'</b>';
                }).join(' AND ');
                showIfHtml = '<div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">'
                    +'<span style="display:inline-flex;align-items:center;gap:4px;background:#fef9c3;color:#854d0e;border:1px solid #fde68a;border-radius:5px;padding:2px 8px;font-size:10px;font-weight:700;">Show if</span>'
                    +'<span style="font-size:11px;color:#374151;">'+condParts+'</span>'
                    +'</div>';
            }

            var bottomHtml = (goTosHtml||showIfHtml)
                ? '<div style="margin-top:8px;padding-top:8px;border-top:1.5px solid #e5e7eb;display:flex;flex-direction:column;gap:4px;">'+goTosHtml+showIfHtml+'</div>'
                : '';

            var isSel = SCE._selQ.has(ri);
            return '<div class="ce-qcard'+(isSel?' ce-selected':'')+'" id="sce-qcard-'+ri+'">'
                +'<div class="ce-qcard-top">'
                +'<div style="display:flex;align-items:center;gap:6px;">'
                +'<input type="checkbox" class="ce-qcard-checkbox" data-ri="'+ri+'" '+(isSel?'checked':'')+' onchange="sceTogSelQ(this,'+ri+')" onclick="event.stopPropagation();">'
                +'<span class="ce-qid">Q'+(q.isNew?'new':q.id)+'</span>'
                +'</div>'
                +'<div class="ce-qcard-actions">'
                +'<button type="button" class="ce-icon-btn copy-btn" onclick="sceCopyQ('+ri+')" title="Copy"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg></button>'
                +'<button type="button" class="ce-icon-btn" onclick="scePasteQ('+ri+')" title="Paste after"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/></svg></button>'
                +'<button type="button" class="ce-icon-btn" onclick="sceEditQ('+ri+')" title="Edit"><i class="fa fa-pencil"></i></button>'
                +'<button type="button" class="ce-icon-btn del" onclick="sceDelQ('+ri+')" title="Delete"><i class="fa fa-trash"></i></button>'
                +'<button type="button" class="ce-icon-btn add-btn" onclick="sceInsertQAfter('+ri+')" title="Add after"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button>'
                +'<span class="ce-drag-handle ce-qcard-drag" title="Drag"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="5" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="5" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="15" cy="19" r="1.5"/></svg></span>'
                +'</div></div>'
                +(q.label?'<div class="ce-qlabel">'+esc(q.label)+'</div>':'')
                +'<div class="ce-qfield-preview" style="padding:6px 10px 8px;">'+fieldHtml+bottomHtml+'</div>'
                +'</div>';
        }).join('');
        initQSortable();
    }

    function renderS(filter) {
        var el = document.getElementById('sceContractPreview');
        document.getElementById('sceSectionCount').textContent = SCE.sections.length;
        var lc = (filter||'').toLowerCase();
        var src = lc ? SCE.sectionsFull.filter(s=>(s.content||'').toLowerCase().includes(lc)||(s.type||'').toLowerCase().includes(lc)) : SCE.sections;

        if (!src.length) {
            el.innerHTML = '<div style="text-align:center;color:#d1d5db;padding:60px 20px;font-size:13px;">No contract sections yet.<br>Click <b>+ Add</b> to start building.</div>';
            initSSortable();
            return;
        }
        el.innerHTML = src.map(function(s) {
            var idx = SCE.sections.indexOf(s);
            var tid = s.isNew ? 'T(new)' : 'T'+s.id;
            var rendered = replacePlaceholders(s.content||'');
            var inner = '';
            if (s.type==='content_heading') {
                inner = '<div class="ce-sheading" style="text-align:'+esc(s.text_align)+';max-height:220px;overflow-y:auto;">'+rendered+'</div>';
            } else if (s.type==='signature_field') {
                inner = '<div class="ce-sig"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> Signature field</div>';
            } else {
                var blurStyle = s.secure_blur_content ? 'filter:blur(4px);user-select:none;' : '';
                inner = '<div class="ce-scontent" style="text-align:'+esc(s.text_align)+';'+blurStyle+'max-height:220px;overflow-x:auto;">'+rendered+'</div>';
            }

            var condBar = '';
            if (s.conditions && s.conditions.length) {
                var condParts = s.conditions.map(function(c){
                    var op = c.type||'is_equal_to';
                    if(op==='is_equal_to'||op==='') op='=';
                    else if(op==='is_not_equal_to') op='!=';
                    else if(op==='is_greater_than') op='>';
                    else if(op==='is_less_than') op='<';
                    return '<span style="display:inline-flex;align-items:center;gap:4px;background:#e6f4ea;color:#1e8e3e;border-radius:5px;padding:2px 7px;font-size:10px;font-weight:700;">Q'+esc(String(c.qid))+'</span>'
                        +'<span style="font-size:10px;color:#6b7280;font-family:monospace;background:#f3f4f6;border-radius:4px;padding:1px 5px;">'+op+' '+esc(c.value)+'</span>';
                }).join('<span style="font-size:9px;color:#9ca3af;font-weight:700;padding:0 2px;">AND</span>');
                condBar = '<div style="border-top:1.5px solid #e5e7eb;margin-top:6px;padding:6px 8px 4px;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">'
                    +'<span style="display:inline-flex;align-items:center;gap:3px;background:#fef9c3;color:#854d0e;border:1px solid #fde68a;border-radius:5px;padding:2px 8px;font-size:10px;font-weight:700;white-space:nowrap;">Show if</span>'
                    +condParts+'</div>';
            }

            var isSel = SCE._selS.has(idx);
            return '<div class="ce-sblock'+(isSel?' ce-selected':'')+'" id="sce-sblock-'+idx+'" style="padding-left:6px;">'
                +'<div class="ce-stid">'
                +'<div style="display:flex;align-items:center;gap:6px;">'
                +'<input type="checkbox" class="ce-sblock-checkbox" data-idx="'+idx+'" '+(isSel?'checked':'')+' onchange="sceTogSelS(this,'+idx+')" onclick="event.stopPropagation();">'
                +'<span style="color:#e85d2f;font-weight:700;background:#fff4f0;padding:2px 6px;">'+esc(tid)+'</span>'
                +'</div>'
                +'<div class="align-contract-btn">'
                +'<button type="button" class="ce-icon-btn copy-btn" onclick="sceCopyS('+idx+')" title="Copy"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg></button>'
                +'<button type="button" class="ce-icon-btn" onclick="scePasteS('+idx+')" title="Paste after"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/></svg></button>'
                +'<button type="button" class="ce-icon-btn" onclick="sceEditS('+idx+')" title="Edit"><i class="fa fa-pencil"></i></button>'
                +'<button type="button" class="ce-icon-btn del" onclick="sceDelS('+idx+')" title="Delete"><i class="fa fa-trash"></i></button>'
                +'<button type="button" class="ce-icon-btn add-btn" onclick="sceInsertSAfter('+idx+')" title="Add after"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button>'
                +'<span class="ce-sdrag-handle ce-sblock-drag" title="Drag"><svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="5" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="5" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="15" cy="19" r="1.5"/></svg></span>'
                +'</div></div><hr>'+inner+'<hr class="ce-sdivider">'
                + condBar
                +'</div>';
        }).join('');
        initSSortable();
    }

    function replacePlaceholders(html) {
        if (!html) return '<em style="color:#d1d5db;">Empty section</em>';
        return html.replace(/\{(\w+)\}/g, function(_, token) {
            var numId = token.replace(/\D/g,'');
            var q = SCE.questions.find(q => String(q.id) === numId);
            var lbl = q ? q.label : token;
            var qid = q ? q.id : numId;
            return '<span style="display:inline-flex;align-items:center;gap:3px;cursor:pointer;background:#fff4f0;color:#e85d2f;border-radius:4px;padding:1px 6px;font-size:11px;font-weight:700;font-family:monospace;">Q'+esc(String(qid))+'</span>';
        });
    }

    function initQSortable() {
        var list = document.getElementById('sceQuestionsList');
        if (!list || !window.Sortable) return;
        if (window._sceSortQ) window._sceSortQ.destroy();
        window._sceSortQ = new Sortable(list, {
            animation:150, handle:'.ce-qcard-drag', ghostClass:'sortable-ghost',
            onEnd: function(evt) {
                if (evt.oldIndex === evt.newIndex) return;
                var m = SCE.questions.splice(evt.oldIndex,1)[0];
                SCE.questions.splice(evt.newIndex,0,m);
                renderQ(); setStatus('Unsaved changes');
            }
        });
    }
    function initSSortable() {
        var el = document.getElementById('sceContractPreview');
        if (!el || !window.Sortable) return;
        if (window._sceSortS) window._sceSortS.destroy();
        window._sceSortS = new Sortable(el, {
            animation:150, handle:'.ce-sblock-drag', ghostClass:'sortable-ghost',
            onEnd: function(evt) {
                if (evt.oldIndex === evt.newIndex) return;
                var m = SCE.sections.splice(evt.oldIndex,1)[0];
                SCE.sections.splice(evt.newIndex,0,m);
                SCE.sectionsFull = SCE.sections.slice();
                renderS(); setStatus('Unsaved changes');
            }
        });
    }

    window.sceTogSelQ = function(cb,ri) {
        if(cb.checked){SCE._selQ.add(ri);var c=document.getElementById('sce-qcard-'+ri);if(c)c.classList.add('ce-selected');}
        else{SCE._selQ.delete(ri);var c=document.getElementById('sce-qcard-'+ri);if(c)c.classList.remove('ce-selected');}
        updateBulkQ();
    };
    function updateBulkQ() {
        var bar=document.getElementById('sceBulkBar');
        var n=SCE._selQ.size;
        if(n>0){bar.classList.add('open');}
        else bar.classList.remove('open');
    }

    window.sceClearSel = function() {
        SCE._selQ.clear();
        document.querySelectorAll('.ce-qcard-checkbox').forEach(cb=>cb.checked=false);
        document.querySelectorAll('.ce-qcard.ce-selected').forEach(c=>c.classList.remove('ce-selected'));
        var m=document.getElementById('sceSelectAllCb');if(m)m.checked=false;
        updateBulkQ();
    };
    window.sceToggleSelectAll = function(masterCb) {
        var cbs=document.querySelectorAll('.ce-qcard-checkbox');
        SCE._selQ.clear();
        cbs.forEach(function(cb){
            cb.checked=masterCb.checked;
            var ri=parseInt(cb.getAttribute('data-ri'));
            var card=document.getElementById('sce-qcard-'+ri);
            if(masterCb.checked){SCE._selQ.add(ri);if(card)card.classList.add('ce-selected');}
            else if(card)card.classList.remove('ce-selected');
        });
        updateBulkQ();
    };
    window.sceBulkCopy = function() {
        if(!SCE._selQ.size) return;
        SCE._bulkClipQ = Array.from(SCE._selQ).sort((a,b)=>a-b).map(ri=>JSON.parse(JSON.stringify(SCE.questions[ri])));
        setStatus(SCE._selQ.size+' question(s) copied');
        setTimeout(()=>setStatus(''),2000);
        sceClearSel();
    };
    window.sceBulkDelete = function() {
        if(!SCE._selQ.size) return;
        if(!confirm('Delete '+SCE._selQ.size+' selected question(s)?')) return;
        Array.from(SCE._selQ).sort((a,b)=>b-a).forEach(function(ri){
            var q=SCE.questions[ri];
            if(q&&!q.isNew) SCE.deletedQuestionIds.push(q.id);
            SCE.questions.splice(ri,1);
        });
        SCE._selQ.clear(); updateBulkQ(); renderQ(); renderS(); setStatus('Unsaved changes');
    };

    window.sceTogSelS = function(cb,idx) {
        if(cb.checked){SCE._selS.add(idx);var c=document.getElementById('sce-sblock-'+idx);if(c)c.classList.add('ce-selected');}
        else{SCE._selS.delete(idx);var c=document.getElementById('sce-sblock-'+idx);if(c)c.classList.remove('ce-selected');}
        updateBulkS();
    };
    function updateBulkS(){
        var bar=document.getElementById('sceSBulkBar');
        var n=SCE._selS.size;
        if(n>0){bar.classList.add('open');}
        else bar.classList.remove('open');
    }
    window.sceClearSelS = function(){
        SCE._selS.clear();
        document.querySelectorAll('.ce-sblock-checkbox').forEach(cb=>cb.checked=false);
        document.querySelectorAll('.ce-sblock.ce-selected').forEach(c=>c.classList.remove('ce-selected'));
        var m=document.getElementById('sceSelectAllSectionsCb');if(m)m.checked=false;
        updateBulkS();
    };
    window.sceToggleSelectAllSections = function(masterCb){
        var cbs=document.querySelectorAll('.ce-sblock-checkbox');
        SCE._selS.clear();
        cbs.forEach(function(cb){
            cb.checked=masterCb.checked;
            var idx=parseInt(cb.getAttribute('data-idx'));
            var b=document.getElementById('sce-sblock-'+idx);
            if(masterCb.checked){SCE._selS.add(idx);if(b)b.classList.add('ce-selected');}
            else if(b)b.classList.remove('ce-selected');
        });
        updateBulkS();
    };
    window.sceBulkCopyS = function(){
        if(!SCE._selS.size) return;
        SCE._bulkClipS = Array.from(SCE._selS).sort((a,b)=>a-b).map(idx=>JSON.parse(JSON.stringify(SCE.sections[idx])));
        setStatus(SCE._selS.size+' section(s) copied');
        setTimeout(()=>setStatus(''),2000); sceClearSelS();
    };
    window.sceBulkDeleteS = function(){
        if(!SCE._selS.size) return;
        if(!confirm('Delete '+SCE._selS.size+' selected section(s)?')) return;
        Array.from(SCE._selS).sort((a,b)=>b-a).forEach(function(idx){
            var s=SCE.sections[idx];
            if(s&&!s.isNew) SCE.deletedSectionIds.push(s.id);
            SCE.sections.splice(idx,1);
        });
        SCE.sectionsFull=SCE.sections.slice();
        SCE._selS.clear(); updateBulkS(); renderS(); setStatus('Unsaved changes');
    };

    window.sceCopyQ = function(ri){ var q=SCE.questions[ri]; if(!q)return; SCE._clipQ=JSON.parse(JSON.stringify(q)); setStatus('Copied'); setTimeout(()=>setStatus(''),1500); };
    window.scePasteQ = function(ri){
        var src = SCE._bulkClipQ && SCE._bulkClipQ.length ? SCE._bulkClipQ : (SCE._clipQ ? [SCE._clipQ] : null);
        if(!src){setStatus('Nothing copied');return;}
        src.forEach(function(q,i){ var c=JSON.parse(JSON.stringify(q)); c.isNew=true; c.id='new_'+Date.now()+'_'+i; SCE.questions.splice(ri+1+i,0,c); });
        renderQ(); setStatus('Unsaved changes');
    };
    window.sceCopyS = function(idx){ var s=SCE.sections[idx]; if(!s)return; SCE._clipS=JSON.parse(JSON.stringify(s)); setStatus('Copied'); setTimeout(()=>setStatus(''),1500); };
    window.scePasteS = function(idx){
        var src = SCE._bulkClipS && SCE._bulkClipS.length ? SCE._bulkClipS : (SCE._clipS ? [SCE._clipS] : null);
        if(!src){setStatus('Nothing copied');return;}
        src.forEach(function(s,i){ var c=JSON.parse(JSON.stringify(s)); c.isNew=true; c.id='new_'+Date.now()+'_'+i; SCE.sections.splice(idx+1+i,0,c); });
        SCE.sectionsFull=SCE.sections.slice(); renderS(); setStatus('Unsaved changes');
    };

    window.sceAddNewQ = function(){ SCE.editingQIdx=null; openQModal(null); };
    window.sceInsertQAfter = function(ri){
        var nq={id:'new_'+Date.now(),type:'textbox',label:'',info:'',placeholder:'',goTo:null,options:[],isNew:true};
        SCE.questions.splice(ri+1,0,nq); renderQ(); setStatus('Unsaved changes');
        setTimeout(()=>sceEditQ(ri+1),50);
    };
    window.sceAddNewS = function(){ SCE.editingSIdx=null; openSModal(null); };
    window.sceInsertSAfter = function(idx){
        var ns={id:'new_'+Date.now(),type:'content',content:'',text_align:'left',secure_blur_content:0,isNew:true};
        SCE.sections.splice(idx+1,0,ns); SCE.sectionsFull=SCE.sections.slice(); renderS(); setStatus('Unsaved changes');
        setTimeout(()=>sceEditS(idx+1),50);
    };

    window.sceEditQ = function(ri){ var q=SCE.questions[ri]; if(!q)return; SCE.editingQIdx=ri; openQModal(q); };
    window.sceEditS = function(idx){ var s=SCE.sections[idx]; if(!s)return; SCE.editingSIdx=idx; openSModal(s); };

    window.sceDelQ = function(ri){
        if(!confirm('Delete this question?')) return;
        var q=SCE.questions[ri];
        if(q&&!q.isNew) SCE.deletedQuestionIds.push(q.id);
        SCE.questions.splice(ri,1); renderQ(); renderS(); setStatus('Unsaved changes');
    };
    window.sceDelS = function(idx){
        if(!confirm('Delete this section?')) return;
        var s=SCE.sections[idx];
        if(s&&!s.isNew) SCE.deletedSectionIds.push(s.id);
        SCE.sections.splice(idx,1); SCE.sectionsFull=SCE.sections.slice(); renderS(); setStatus('Unsaved changes');
    };

    window.sceFilterQ = function(v){ renderQ(v); };
    window.sceFilterS = function(v){ renderS(v); };

    function openQModal(q) {
        document.getElementById('sceQModalId').value          = q ? q.id : '';
        document.getElementById('sceQModalLabel').value       = q ? (q.label||'') : '';
        document.getElementById('sceQModalInfo').value        = q ? (q.info||'') : '';
        document.getElementById('sceQModalPlaceholder').value = q ? (q.placeholder||'') : '';
        document.getElementById('sceQModalType').value        = q ? (q.type||'textbox') : 'textbox';
        document.getElementById('sceQModalOptionsList').innerHTML  = '';
        document.getElementById('sceQModalCondRows').innerHTML     = '';
        document.getElementById('sceCondGroupsContainer').innerHTML = '';

        var badge = document.getElementById('sceQModalBadge');
        if (q && !q.isNew) { badge.textContent='Q'+q.id; badge.style.display='inline-block'; }
        else badge.style.display = 'none';

        var labelWrap = document.getElementById('sceQModalLabelWrap');
        if (labelWrap) labelWrap.style.display = '';

        if (q) {
            (q.options||[]).forEach(function(o){
                var gotoVal = '';
                (q.condGoTo||[]).forEach(function(cg){
                    if (cg.conditions && cg.conditions.length===1) {
                        var c = cg.conditions[0];
                        if ((c.type==='is_equal_to'||c.type==='') && c.value===(o.value||o.label))
                            gotoVal = cg.goto||'';
                    }
                });
                sceAppendOpt(o.label, o.value||o.label, gotoVal);
            });
        }

        popGoTo(q ? q.goTo : null);
        sceQTypeChange();

        if (q) {
            var nonOptionCondGoTo = (q.condGoTo||[]).filter(function(cg){
                if (!cg.conditions || cg.conditions.length!==1) return true;
                var c = cg.conditions[0];
                return !(String(c.qid)===String(q.id) && (c.type==='is_equal_to'||c.type===''));
            });
            nonOptionCondGoTo.forEach(function(grp){
                sceAddCondGroup();
                var lastGi = document.querySelectorAll('#sceCondGroupsContainer [id^=sceCondGroup_]').length - 1;
                var grpEl  = document.getElementById('sceCondGroup_'+lastGi);
                if (!grpEl) return;
                var rowsEl = grpEl.querySelector('.sce-cond-rows-'+lastGi);
                if (rowsEl) rowsEl.innerHTML = '';
                var conditions = grp.conditions || [];
                conditions.forEach(function(){ sceAddSubCond(lastGi); });
                setTimeout(function(){
                    var qids  = grpEl.querySelectorAll('.sce-sub-qid');
                    var conds = grpEl.querySelectorAll('.sce-sub-cond');
                    var vals  = grpEl.querySelectorAll('.sce-sub-val');
                    conditions.forEach(function(c,ci){
                        if (qids[ci])  qids[ci].value  = c.qid   || '';
                        if (conds[ci]) conds[ci].value = c.type  || '';
                        if (vals[ci])  vals[ci].value  = c.value || '';
                    });
                    var gotoSel = grpEl.querySelector('.sce-cond-goto-sel');
                    if (gotoSel) gotoSel.value = grp.goto || '';
                }, 0);
            });
        }

        if (q) {
            (q.conditions||[]).forEach(function(){ sceAddConditionFromModal(); });
            setTimeout(function(){
                var rows = document.querySelectorAll('#sceQModalCondRows [id^=sceModalCondRow_]');
                (q.conditions||[]).forEach(function(cond,ci){
                    if (!rows[ci]) return;
                    var lbl = rows[ci].querySelector('.sce-modal-cond-label');
                    var qid = rows[ci].querySelector('.sce-modal-cond-qid');
                    var val = rows[ci].querySelector('.sce-modal-cond-value');
                    if (lbl) lbl.value = cond.label||'';
                    if (qid) qid.value = cond.qid  ||'';
                    if (val) val.value = cond.value ||'';
                });
            }, 0);
        }

        document.getElementById('sceQuestionModal').classList.add('open');
    }

    window.sceAddConditionFromModal = function(){
        var container = document.getElementById('sceQModalCondRows'); if(!container)return;
        var wrap = document.getElementById('sceQModalLabelWrap'); if(wrap)wrap.style.display='none';
        var ci = container.children.length;
        var iStyle='padding:7px 9px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:12px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;width:100%;';
        var iFocus='onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';
        var qidOpts='<option value="">— QID —</option>';
        SCE.questions.forEach(function(qq){ qidOpts+='<option value="'+esc(qq.id)+'">QID'+esc(qq.id)+'</option>'; });
        var row=document.createElement('div');
        row.style.cssText='display:flex;gap:5px;align-items:flex-end;margin-bottom:10px;padding-top:10px;';
        row.id='sceModalCondRow_'+ci;
        var initLabel='';
        if(ci===0){ var ml=document.getElementById('sceQModalLabel'); if(ml)initLabel=ml.value.trim(); }
        row.innerHTML=
            '<div style="flex:2;position:relative;padding-top:10px;">'
            +'<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Question Label</span>'
            +'<input type="text" class="sce-modal-cond-label" style="'+iStyle+'" '+iFocus+' placeholder="Label" value="'+esc(initLabel)+'"></div>'
            +'<div style="flex:1.2;position:relative;padding-top:10px;">'
            +'<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Question ID</span>'
            +'<select class="sce-modal-cond-qid" style="'+iStyle+'cursor:pointer;" '+iFocus+'>'+qidOpts+'</select></div>'
            +'<div style="flex:1.2;position:relative;padding-top:10px;">'
            +'<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Value</span>'
            +'<input type="text" class="sce-modal-cond-value" style="'+iStyle+'" '+iFocus+' placeholder="Value"></div>'
            +'<button type="button" onclick="sceRemoveConditionFromModal(this)"'
            +' style="flex-shrink:0;width:28px;height:28px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:5px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;"'
            +' onmouseover="this.style.background=\'#fde8e8\';this.style.color=\'#dc2626\'" onmouseout="this.style.background=\'#f1f5f9\';this.style.color=\'#94a3b8\'"><i class="fa fa-trash" style="font-size:11px;"></i></button>'
            +'<button type="button" onclick="sceAddConditionFromModal()" class="sce-add-cond-btn"'
            +' style="flex-shrink:0;width:28px;height:28px;background:#e85d2f;border:none;border-radius:5px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#fff;"'
            +' onmouseover="this.style.background=\'#c94d23\'" onmouseout="this.style.background=\'#e85d2f\'"><i class="fa fa-plus" style="font-size:11px;"></i></button>';
        if(ci>0){ var prev=container.children[ci-1]; var pp=prev?prev.querySelector('.sce-add-cond-btn'):null; if(pp)pp.style.display='none'; }
        container.appendChild(row);
    };

    window.sceRemoveConditionFromModal = function(btn){
        var row=btn.closest('[id^=sceModalCondRow_]');
        var lblInput=row?row.querySelector('.sce-modal-cond-label'):null;
        var lastVal=lblInput?lblInput.value.trim():'';
        if(row)row.remove();
        var container=document.getElementById('sceQModalCondRows');
        if(!container.children.length){
            var wrap=document.getElementById('sceQModalLabelWrap'); if(wrap)wrap.style.display='';
            var ml=document.getElementById('sceQModalLabel'); if(ml&&lastVal)ml.value=lastVal;
        } else {
            var last=container.lastElementChild;
            if(last){ var pp=last.querySelector('.sce-add-cond-btn'); if(pp)pp.style.display='flex'; }
        }
    };

    window.sceAddCondGroup = function(){
        var container=document.getElementById('sceCondGroupsContainer');
        var gi=container.children.length;
        var iStyle='padding:7px 9px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:12px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;width:100%;';
        var iFocus='onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';
        var grp=document.createElement('div');
        grp.id='sceCondGroup_'+gi;
        grp.style.cssText='border:1px solid #e5e7eb;border-radius:8px;padding:14px 14px 10px;background:#f9fafb;margin-bottom:12px;';
        grp.innerHTML=
            '<div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Conditions</div>'
            +'<div class="sce-cond-rows-'+gi+'"></div>'
            +'<div style="position:relative;padding-top:12px;margin-bottom:6px;">'
            +'<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#f9fafb;padding:0 3px;font-weight:600;z-index:1;">Conditional Go to Step</span>'
            +'<select class="sce-cond-goto-sel" style="'+iStyle+'" '+iFocus+'><option value="">— None —</option></select></div>'
            +'<div style="display:flex;justify-content:flex-end;margin-top:6px;">'
            +'<button type="button" onclick="this.closest(\'[id^=sceCondGroup_]\').remove()"'
            +' style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:11px;display:flex;align-items:center;gap:4px;"'
            +' onmouseover="this.style.color=\'#dc2626\'" onmouseout="this.style.color=\'#9ca3af\'">'
            +'<i class="fa fa-trash" style="font-size:10px;"></i> Remove Condition</button></div>';
        container.appendChild(grp);
        var sel=grp.querySelector('.sce-cond-goto-sel');
        SCE.questions.forEach(function(q){
            var o=document.createElement('option'); o.value=q.id;
            o.textContent='QID'+q.id+(q.label?' — '+q.label.substring(0,30):''); sel.appendChild(o);
        });
        var endO=document.createElement('option'); endO.value='END'; endO.textContent='Checkout'; sel.appendChild(endO);
        sceAddSubCond(gi);
    };

    window.sceAddSubCond = function(gi){
        var rowsEl=document.querySelector('.sce-cond-rows-'+gi); if(!rowsEl)return;
        var iStyle='padding:7px 9px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:12px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;width:100%;';
        var iFocus='onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';
        var qidOpts='<option value="">Question ID</option>';
        SCE.questions.forEach(function(qq){ qidOpts+='<option value="'+esc(qq.id)+'">'+esc(qq.id)+'</option>'; });
        var condOpts='<option value="">Condition</option>'
            +'<option value="is_equal_to">is equal to</option>'
            +'<option value="is_not_equal_to">is not equal to</option>'
            +'<option value="is_less_than">is less than</option>'
            +'<option value="is_greater_than">is greater than</option>';
        var row=document.createElement('div');
        row.style.cssText='display:flex;gap:6px;align-items:center;margin-bottom:8px;';
        row.innerHTML=
            '<div style="flex:1.2;position:relative;padding-top:10px;">'
            +'<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Question ID</span>'
            +'<select class="sce-sub-qid" style="'+iStyle+'cursor:pointer;" '+iFocus+'>'+qidOpts+'</select></div>'
            +'<div style="flex:1.4;position:relative;padding-top:10px;">'
            +'<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Condition</span>'
            +'<select class="sce-sub-cond" style="'+iStyle+'cursor:pointer;" '+iFocus+'>'+condOpts+'</select></div>'
            +'<div style="flex:1;position:relative;padding-top:10px;">'
            +'<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Value</span>'
            +'<input type="text" class="sce-sub-val" style="'+iStyle+'" '+iFocus+' placeholder="Value"></div>'
            +'<button type="button" onclick="this.closest(\'div[style*=margin-bottom]\').remove()"'
            +' style="flex-shrink:0;width:26px;height:26px;margin-top:10px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;">'
            +'<i class="fa fa-trash" style="font-size:10px;"></i></button>'
            +'<button type="button" onclick="sceAddSubCond('+gi+')"'
            +' style="flex-shrink:0;width:26px;height:26px;margin-top:10px;background:none;border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:rgb(60,77,98);">'
            +'<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button>';
        rowsEl.appendChild(row);
    };

    window.sceCloseQModal = function(){ document.getElementById('sceQuestionModal').classList.remove('open'); };

    window.sceQTypeChange = function(){
        var t = document.getElementById('sceQModalType').value;
        var isOpt = (t==='radio-button'||t==='dropdown');
        document.getElementById('sceQModalOptionsWrap').style.display = isOpt ? 'block' : 'none';
        var goToWrap = document.querySelector('.goto_wid_inner');
        if (goToWrap) goToWrap.style.display = isOpt ? 'none' : '';
        var phWrap = document.getElementById('sceQModalPlaceholderWrap');
        var showPh = (t==='textbox'||t==='textarea'||t==='number');
        if (phWrap) phWrap.style.display = showPh ? 'block' : 'none';
    };

    function popGoTo(selected) {
        var sel=document.getElementById('sceQModalGoTo');
        sel.innerHTML='<option value="">— None (next) —</option>';
        SCE.questions.forEach(function(q){
            var o=document.createElement('option');
            o.value=q.id; o.textContent='QID'+q.id+(q.label?' — '+q.label.substring(0,35):'');
            if(String(q.id)===String(selected)) o.selected=true;
            sel.appendChild(o);
        });
        var e=document.createElement('option'); e.value='END'; e.textContent='END (Checkout)';
        if(String(selected)==='END') e.selected=true;
        sel.appendChild(e);
    }

    window.sceAppendOpt = function(label, value, gotoVal){
        var ol = document.getElementById('sceQModalOptionsList');
        var iStyle = 'padding:8px 10px;border:1.5px solid #e5e7eb;border-radius:6px;font-size:12px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;width:100%;';
        var iFocus = 'onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';
        var gotoOpts = '';
        SCE.questions.forEach(function(q){
            gotoOpts += '<option value="'+esc(q.id)+'"'+(String(gotoVal)===String(q.id)?' selected':'')+'>Q'+esc(String(q.id))+(q.label?' — '+q.label.substring(0,22):'')+'</option>';
        });
        gotoOpts += '<option value="END"'+((!gotoVal||gotoVal==='')?' selected':'')+'>Checkout</option>';
        var d = document.createElement('div');
        d.style.cssText = 'display:flex;gap:8px;margin-bottom:10px;align-items:flex-end;';
        d.innerHTML =
            '<div style="flex:2;position:relative;padding-top:12px;">'
            +'<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Label</span>'
            +'<input type="text" class="sce-opt-label" value="'+esc(label||'')+'" style="'+iStyle+'" '+iFocus+' placeholder=""></div>'
            +'<div style="flex:1.5;position:relative;padding-top:12px;">'
            +'<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Value</span>'
            +'<input type="text" class="sce-opt-value" value="'+esc(value||'')+'" style="'+iStyle+'" '+iFocus+' placeholder=""></div>'
            +'<div style="flex:2;position:relative;padding-top:12px;">'
            +'<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#fff;padding:0 3px;font-weight:600;z-index:1;">Go to Step</span>'
            +'<select class="sce-opt-goto" style="'+iStyle+'cursor:pointer;" '+iFocus+'>'+gotoOpts+'</select></div>'
            +'<button type="button" onclick="this.closest(\'div[style*=margin-bottom]\').remove()"'
            +' style="flex-shrink:0;width:28px;height:28px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;margin-bottom:1px;"'
            +' onmouseover="this.style.background=\'#fde8e8\';this.style.color=\'#dc2626\'" onmouseout="this.style.background=\'#f1f5f9\';this.style.color=\'#94a3b8\'">'
            +'<i class="fa fa-trash" style="font-size:10px;"></i></button>'
            +'<button type="button" onclick="sceAppendOpt(\'\',\'\',\'\')"'
            +' style="flex-shrink:0;width:28px;height:28px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#374151;margin-bottom:1px;"'
            +' onmouseover="this.style.background=\'#e85d2f\';this.style.color=\'#fff\';this.style.borderColor=\'#e85d2f\'" onmouseout="this.style.background=\'#f3f4f6\';this.style.color=\'#374151\';this.style.borderColor=\'#e5e7eb\'">'
            +'<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button>';
        ol.appendChild(d);
    };

    window.sceSaveQuestion = function(){
        var labelWrap = document.getElementById('sceQModalLabelWrap');
        var label     = document.getElementById('sceQModalLabel').value.trim();

        var conditions = [];
        document.querySelectorAll('#sceQModalCondRows [id^=sceModalCondRow_]').forEach(function(row){
            var lbl = row.querySelector('.sce-modal-cond-label');
            var qid = row.querySelector('.sce-modal-cond-qid');
            var val = row.querySelector('.sce-modal-cond-value');
            if (lbl||qid) conditions.push({label:lbl?lbl.value.trim():'',qid:qid?qid.value:'',value:val?val.value.trim():''});
        });
        if (labelWrap && labelWrap.style.display==='none' && conditions.length)
            label = conditions[0].label || label;
        if (!label){ alert('Question label is required.'); return; }

        var options = [];
        document.querySelectorAll('#sceQModalOptionsList > div').forEach(function(row){
            var l = row.querySelector('.sce-opt-label');
            var v = row.querySelector('.sce-opt-value');
            if (l && l.value.trim()) options.push({label:l.value.trim(), value:(v&&v.value.trim())||l.value.trim()});
        });

        var condGoTo = (function(){
            var groups = [];
            var t = document.getElementById('sceQModalType').value;
            if (t==='radio-button'||t==='dropdown') {
                document.querySelectorAll('#sceQModalOptionsList > div').forEach(function(row){
                    var lbl     = row.querySelector('.sce-opt-label');
                    var val     = row.querySelector('.sce-opt-value');
                    var gotoSel = row.querySelector('.sce-opt-goto');
                    var optVal  = (val&&val.value.trim()) ? val.value.trim() : (lbl?lbl.value.trim():'');
                    var gotoVal = gotoSel ? gotoSel.value : '';
                    if (optVal && gotoVal) {
                        var editingQ = SCE.editingQIdx!==null ? SCE.questions[SCE.editingQIdx] : null;
                        var qid      = editingQ ? editingQ.id : ('new_'+Date.now());
                        groups.push({ conditions:[{qid:qid,type:'is_equal_to',value:optVal}], goto:gotoVal });
                    }
                });
            }
            document.querySelectorAll('#sceCondGroupsContainer [id^=sceCondGroup_]').forEach(function(grp){
                var rows = [];
                grp.querySelectorAll('.sce-sub-qid').forEach(function(qidEl,i){
                    var condEl = grp.querySelectorAll('.sce-sub-cond')[i];
                    var valEl  = grp.querySelectorAll('.sce-sub-val')[i];
                    rows.push({qid:qidEl?qidEl.value:'',type:condEl?condEl.value:'',value:valEl?valEl.value.trim():''});
                });
                var gotoSel = grp.querySelector('.sce-cond-goto-sel');
                groups.push({conditions:rows, goto:gotoSel?gotoSel.value:''});
            });
            return groups;
        })();

        var qObj = {
            type        : document.getElementById('sceQModalType').value,
            label       : label,
            placeholder : document.getElementById('sceQModalPlaceholder').value.trim(),
            info        : document.getElementById('sceQModalInfo').value.trim(),
            goTo        : document.getElementById('sceQModalGoTo').value || null,
            options     : options,
            conditions  : conditions,
            condGoTo    : condGoTo,
        };
        if (SCE.editingQIdx===null){ qObj.id='new_'+Date.now(); qObj.isNew=true; SCE.questions.push(qObj); }
        else { var ex=SCE.questions[SCE.editingQIdx]; qObj.isNew=ex.isNew; if(!ex.isNew)qObj.id=ex.id; Object.assign(ex,qObj); }
        sceCloseQModal(); renderQ(); renderS(); setStatus('Unsaved changes');
    };

    function openSModal(s) {
        document.getElementById('sceSModalId').value      = s ? s.id : '';
        document.getElementById('sceSModalType').value    = s ? (s.type||'content') : 'content';
        document.getElementById('sceSModalContent').value = s ? (s.content||'') : '';
        document.getElementById('sceSModalAlign').value   = s ? (s.text_align||'left') : 'left';
        document.getElementById('sceSModalBlur').checked  = s ? !!s.secure_blur_content : false;
        document.getElementById('sceSModalTitle').textContent = (s&&!s.isNew) ? 'T'+s.id : '';

        document.getElementById('sceSCondGroupsContainer').innerHTML = '';
        document.getElementById('sceAddSectionCondBtn').style.display = '';
        sceSTypeChange();

        if (s && s.conditions && s.conditions.length) {
            sceAddSectionCondGroup();
            var gi = document.querySelectorAll('#sceSCondGroupsContainer [id^=sceSCondGroup_]').length - 1;
            var grpEl = document.getElementById('sceSCondGroup_'+gi);
            if (grpEl) {
                var rowsEl = grpEl.querySelector('.sce-scond-rows-'+gi);
                if (rowsEl) rowsEl.innerHTML = '';
                s.conditions.forEach(function(){ sceAddSectionCondRow(gi); });
                setTimeout(function(){
                    var rows = document.querySelectorAll('#sceSCondGroup_'+gi+' [data-scond-row]');
                    s.conditions.forEach(function(c, ci){
                        if (!rows[ci]) return;
                        var qid  = rows[ci].querySelector('.sce-scond-qid');
                        var type = rows[ci].querySelector('.sce-scond-type');
                        var val  = rows[ci].querySelector('.sce-scond-val');
                        if (qid)  qid.value  = c.qid   || '';
                        if (type) type.value = c.type  || '';
                        if (val)  val.value  = c.value || '';
                    });
                }, 0);
            }
        }

        document.getElementById('sceSectionModal').classList.add('open');
    }

    window.sceCloseSModal = function(){ document.getElementById('sceSectionModal').classList.remove('open'); };

    window.sceSTypeChange = function(){
        var t = document.getElementById('sceSModalType').value;
        var wrap = document.getElementById('sceSModalCondWrap');
        if (wrap) wrap.style.display = (t === 'content') ? 'block' : 'none';
    };

    window.sceSaveSection = function(){
        var type    = document.getElementById('sceSModalType').value;
        var content = document.getElementById('sceSModalContent').value;
        if (!content.trim() && type !== 'signature_field') { alert('Content is required.'); return; }

        var sConditions = [];
        document.querySelectorAll('#sceSCondGroupsContainer [data-scond-row]').forEach(function(row){
            var qid  = row.querySelector('.sce-scond-qid');
            var typ  = row.querySelector('.sce-scond-type');
            var val  = row.querySelector('.sce-scond-val');
            if (qid && qid.value) sConditions.push({
                qid   : qid.value,
                type  : typ ? typ.value : 'is_equal_to',
                value : val ? val.value.trim() : ''
            });
        });

        var sObj = {
            type                : type,
            content             : content,
            text_align          : document.getElementById('sceSModalAlign').value,
            secure_blur_content : document.getElementById('sceSModalBlur').checked ? 1 : 0,
            conditions          : sConditions,
        };
        if (SCE.editingSIdx === null) { sObj.id='new_'+Date.now(); sObj.isNew=true; SCE.sections.push(sObj); }
        else { var ex=SCE.sections[SCE.editingSIdx]; sObj.isNew=ex.isNew; if(!ex.isNew)sObj.id=ex.id; Object.assign(ex,sObj); }
        SCE.sectionsFull = SCE.sections.slice();
        sceCloseSModal(); renderS(); setStatus('Unsaved changes');
    };

    window.sceAddSectionCondGroup = function(){
        var container = document.getElementById('sceSCondGroupsContainer'); if(!container)return;
        document.getElementById('sceAddSectionCondBtn').style.display = 'none';
        var gi = container.children.length;
        var iStyle='padding:7px 9px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:12px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;width:100%;';
        var iFocus='onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';
        var grp = document.createElement('div');
        grp.id = 'sceSCondGroup_'+gi;
        grp.style.cssText = 'border:1px solid #e5e7eb;border-radius:8px;padding:14px 14px 10px;background:#f9fafb;margin-bottom:12px;';
        grp.innerHTML =
            '<div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Show If Conditions</div>'
            +'<div class="sce-scond-rows-'+gi+'"></div>'
            +'<div style="display:flex;justify-content:flex-end;margin-top:6px;">'
            +'<button type="button" onclick="this.closest(\'[id^=sceSCondGroup_]\').remove();if(!document.getElementById(\'sceSCondGroupsContainer\').children.length){document.getElementById(\'sceAddSectionCondBtn\').style.display=\'\'}" '
            +'style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:11px;display:flex;align-items:center;gap:4px;" '
            +'onmouseover="this.style.color=\'#dc2626\'" onmouseout="this.style.color=\'#9ca3af\'">'
            +'<i class="fa fa-trash" style="font-size:10px;"></i> Remove Condition</button></div>';
        container.appendChild(grp);
        sceAddSectionCondRow(gi);
    };

    window.sceAddSectionCondRow = function(gi){
        var rowsEl = document.querySelector('.sce-scond-rows-'+gi); if(!rowsEl)return;
        var iStyle='padding:7px 9px;border:1.5px solid #e5e7eb;border-radius:5px;font-size:12px;color:#374151;background:#fff;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s;width:100%;';
        var iFocus='onfocus="this.style.borderColor=\'#e85d2f\'" onblur="this.style.borderColor=\'#e5e7eb\'"';
        var qidOpts='<option value="">Question ID</option>';
        SCE.questions.forEach(function(qq){ qidOpts+='<option value="'+esc(qq.id)+'">'+esc(qq.id)+(qq.label?' — '+qq.label.substring(0,25):'')+'</option>'; });
        var condOpts='<option value="">Select</option>'
            +'<option value="is_equal_to">is equal to</option>'
            +'<option value="is_not_equal_to">is not equal to</option>'
            +'<option value="is_less_than">is less than</option>'
            +'<option value="is_greater_than">is greater than</option>';
        var row = document.createElement('div');
        row.setAttribute('data-scond-row', rowsEl.children.length);
        row.style.cssText = 'display:flex;gap:6px;align-items:flex-end;margin-bottom:8px;';
        row.innerHTML =
            '<div style="flex:1.2;position:relative;padding-top:10px;">'
            +'<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#f9fafb;padding:0 3px;font-weight:600;z-index:1;">Question ID</span>'
            +'<select class="sce-scond-qid" style="'+iStyle+'cursor:pointer;" '+iFocus+'>'+qidOpts+'</select></div>'
            +'<div style="flex:1.4;position:relative;padding-top:10px;">'
            +'<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#f9fafb;padding:0 3px;font-weight:600;z-index:1;">Condition</span>'
            +'<select class="sce-scond-type" style="'+iStyle+'cursor:pointer;" '+iFocus+'>'+condOpts+'</select></div>'
            +'<div style="flex:1;position:relative;padding-top:10px;">'
            +'<span style="position:absolute;top:2px;left:8px;font-size:10px;color:#9ca3af;background:#f9fafb;padding:0 3px;font-weight:600;z-index:1;">Value</span>'
            +'<input type="text" class="sce-scond-val" style="'+iStyle+'" '+iFocus+' placeholder=""></div>'
            +'<button type="button" onclick="this.closest(\'[data-scond-row]\').remove()"'
            +' style="flex-shrink:0;width:26px;height:26px;background:#80808036;border:1px solid #e2e8f0;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;margin-bottom:1px;">'
            +'<i class="fa fa-trash" style="font-size:8px;"></i></button>'
            +'<button type="button" onclick="sceAddSectionCondRow('+gi+')" class="sce-scond-add-btn"'
            +' style="flex-shrink:0;width:26px;height:26px;background:#80808036;border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:rgb(60,77,98);margin-bottom:1px;">'
            +'<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button>';
        if (rowsEl.children.length > 0) {
            var prev = rowsEl.children[rowsEl.children.length-1];
            var pp = prev ? prev.querySelector('.sce-scond-add-btn') : null;
            if (pp) pp.style.display = 'none';
        }
        rowsEl.appendChild(row);
    };

    window.sceSaveAll = function(){
        var btn=document.getElementById('sceSaveBtn');
        btn.disabled=true; btn.textContent='Saving…';

        var payload={
            _token             : SCE.csrfToken,
            document_id        : SCE.documentId,
            delete_question_ids: SCE.deletedQuestionIds,
            delete_section_ids : SCE.deletedSectionIds,
            questions: SCE.questions.map(function(q,i){
                return {
                    id          : q.isNew ? null : q.id,
                    type        : q.type  || 'textbox',
                    label       : q.label,
                    placeholder : q.placeholder || '',
                    info        : q.info        || '',
                    order_id    : i + 1,
                    goTo        : q.goTo || null,
                    options     : (q.options||[]).map(o=>({id:o.id||null,label:o.label,value:o.value||o.label})),
                    conditions  : (q.conditions||[]).map(function(c){
                        return {label:c.label||'',qid:c.qid?String(c.qid):'',value:c.value||''};
                    }),
                    cond_go_to  : (q.condGoTo||[])
                        .filter(function(cg){ return cg.goto!==null&&cg.goto!==undefined&&cg.goto!==''; })
                        .map(function(cg){
                            return {
                                goto       : String(cg.goto),
                                conditions : (cg.conditions||[]).map(function(c){
                                    return {qid:c.qid?String(c.qid):'',type:c.type||'is_equal_to',value:c.value||''};
                                }),
                            };
                        }),
                };
            }),
            sections: SCE.sections.map(function(s,i){
                return {
                    id                  : s.isNew ? null : s.id,
                    type                : s.type,
                    content             : s.content,
                    text_align          : s.text_align,
                    secure_blur_content : s.secure_blur_content,
                    order_id            : i + 1,
                    conditions          : (s.conditions||[]).map(function(c){
                        return { qid:c.qid?String(c.qid):'', type:c.type||'is_equal_to', value:c.value||'' };
                    }),
                };
            }),
        };

        fetch('/admin-dashboard/api/sce-save', {
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':SCE.csrfToken,
                'Accept':'application/json'
                },
            body:JSON.stringify(payload),
        })
        .then(r=>r.json())
        .then(function(data){
            if(!data.success) throw new Error(data.message||'Save failed');
            sceShowToast('Contract saved successfully.',false);
            SCE.deletedQuestionIds=[]; SCE.deletedSectionIds=[]; SCE._loaded=false;
            sceLoadData();
        })
        .catch(function(err){ sceShowToast('Save failed: '+err.message,true); })
        .finally(function(){
            btn.disabled=false;
            btn.innerHTML='<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Save All';
        });
    };

    ['sceQuestionModal','sceSectionModal'].forEach(function(id){
        var el=document.getElementById(id);
        if(el) el.addEventListener('click',function(e){ if(e.target===this)this.classList.remove('open'); });
    });
    document.addEventListener('keydown',function(e){
        if(e.key!=='Escape') return;
        document.getElementById('sceQuestionModal').classList.remove('open');
        document.getElementById('sceSectionModal').classList.remove('open');
    });

    (function(){
        var _open=false, _start=-1;

        window.sceQidAutoFilter = function(v){
            var lc=v.toLowerCase().replace(/^qid/i,'').replace(/^\{/,'');
            var list=document.getElementById('sceQidAutoList');
            var items=SCE.questions.filter(q=>!lc||String(q.id).includes(lc)||(q.label||'').toLowerCase().includes(lc));
            if(!items.length){list.innerHTML='<div style="padding:10px 12px;font-size:12px;color:#9ca3af;font-style:italic;">No questions found</div>';return;}
            list.innerHTML=items.map(function(q){
                var lbl=(q.label||'').substring(0,55); var sid=String(q.id);
                return '<div onclick="sceQidAutoSelect(\''+sid+'\')" style="padding:7px 12px;cursor:pointer;display:flex;align-items:center;gap:10px;border-bottom:1px solid #f3f4f6;background:#fff;transition:background .1s;" onmouseover="this.style.background=\'#f9fafb\'" onmouseout="this.style.background=\'#fff\'">'
                    +'<span style="font-size:11px;font-weight:600;color:#6b7280;font-family:monospace;white-space:nowrap;">QID'+esc(sid)+'</span>'
                    +'<span style="font-size:11px;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">— '+esc(lbl)+'</span>'
                    +'</div>';
            }).join('');
        };
        window.sceQidAutoSelect = function(qid){
            var ta=document.getElementById('sceSModalContent'); if(!ta)return;
            var token='{'+qid+'}'; var val=ta.value;
            if(_start>=0){ ta.value=val.substring(0,_start)+token+val.substring(ta.selectionStart); }
            else { var p=ta.selectionStart; ta.value=val.substring(0,p)+token+val.substring(p); }
            document.getElementById('sceQidAuto').style.display='none'; _open=false; _start=-1; ta.focus();
        };
        setTimeout(function(){
            var ta=document.getElementById('sceSModalContent'); if(!ta)return;
            ta.addEventListener('input',function(){
                var pos=ta.selectionStart,val=ta.value,bracePos=-1;
                for(var i=pos-1;i>=0;i--){ if(val[i]==='{'){bracePos=i;break;} if(val[i]==='}'||val[i]==='\n')break; }
                if(bracePos>=0){
                    _start=bracePos;
                    var pop=document.getElementById('sceQidAuto');
                    var rect=ta.getBoundingClientRect();
                    pop.style.cssText='display:block;position:fixed;top:'+(rect.bottom+4)+'px;left:'+Math.max(8,rect.left)+'px;z-index:999999;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.10);width:380px;overflow:hidden;';
                    document.getElementById('sceQidAutoSearch').value=val.substring(bracePos+1,pos);
                    sceQidAutoFilter(val.substring(bracePos+1,pos)); _open=true;
                } else if(_open){ document.getElementById('sceQidAuto').style.display='none';_open=false;_start=-1; }
            });
        },300);
        document.addEventListener('click',function(e){
            var pop=document.getElementById('sceQidAuto');
            if(!pop||pop.style.display==='none') return;
            if(!pop.contains(e.target)&&e.target.id!=='sceSModalContent') pop.style.display='none';
        });
    }());

}());
@endif
</script>

@endsection