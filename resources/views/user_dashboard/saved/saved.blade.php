@extends('user_dashboard_layout.master')
<style>
.tit-des-renm-btn {
    border-radius: 5px;
    padding: 9px 11px;
    border: none;
    background-color: #002655;
    color: white;
    font-size: 15px;
    font-weight: 500;
    margin-top: 7px;
}
.tit-des-renm-btn:hover{
        
    background-color: #fd5602;
}
</style>
@section('content')
    <div class="uer_nm">
        <h1>
            {{-- Guardados --}}
            Drafts 
        </h1>
    </div>
    <div class="scroll_div">
        <div class="crt_main" id="append_document">
            @if(isset($documents) && $documents->isNotEmpty())
                @foreach($documents as $document)
                @php
                    $firstQuestion = $firstQuestionsPerDocument[$document->id] ?? null;
                    $lastQuestion = $lastQuestionsPerDocument[$document->id] ?? null;

                    $firstUrl = $firstQuestion
                        ? url('contracts/' . $document->slug) . '?s=' . $firstQuestion->question_id
                        : url('contracts/' . $document->slug);

                    $lastUrl = $lastQuestion
                        ? url('contracts/' . $document->slug) . '?s=' . $lastQuestion->question_id
                        : url('contracts/' . $document->slug);
                @endphp

                <div class="cart_dv">
                    <div class="crt_lft">
                        <div class="cart_img">
                            <img src="{{ $document->document_image ?? '' }}" class="img-fluid">
                        </div>
                        <div class="cart_text">
                            <h4>{{ $document->title ?? '' }}</h4>
                            <p><?php 
                            $short = Str::limit($document->short_description, 100, '...');
                                print_r($short);
                            ?></p>
                        </div>
                    </div>
                    <div class="crt_ryt">
                        <div class="datt_text">
                            {{-- <p class="dt_text">Última edición</p> --}}
                            <p class="dt_text">Last updated</p>
                            <p>{{ \Carbon\Carbon::parse($document->created_at)->format('d/m/Y') ?? '' }}</p>
                        </div>
                        <div class="edt_lnk">
                            <!-- <a onclick="modifiedStep(event, 'last','{{ $lastUrl ?? '' }}')"><span><i class="fa-solid fa-pen"></i></span></a> -->
                            <a data-bs-toggle="modal" data-bs-target="#editarModal{{ $document->id ?? '' }}"><span><i class="fa-solid fa-pen"></i></span></a>
                        </div>
                        <div class="shr_dt dot">
                            <span class="elps_icn"><i class="fa-solid fa-ellipsis-vertical"></i></span>
                            <div class="dropdown-menu_review">
                                <div class="user_name">
                                    {{-- <p class="text-center">Administrar</p> --}}
                                    <p class="text-center">Manage</p>
                                </div>
                                <div class="dropdown-main">
                                    <div class="dash-icon">
                                        <!-- <a class="dropdown-item" onclick="modifiedStep(event, 'last', '{{ $lastUrl ?? '' }}');"><i class="fa-solid fa-pen"></i>Editar</a>   -->
                                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editarModal{{ $document->id ?? '' }}"><i class="fa-solid fa-file-arrow-down"></i>Edit</a>
                                        {{-- <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editarModal{{ $document->id ?? '' }}"><i class="fa-solid fa-file-arrow-down"></i>Editar</a> --}}
                                    </div>
                                    <div class="dash-icon">
                                        {{-- <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#comprarModal{{ $document->id ?? '' }}"><i class="fa-solid fa-file-arrow-down"></i>Comprar</a> --}}
                                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#comprarModal{{ $document->id ?? '' }}"><i class="fa-solid fa-file-arrow-down"></i>Download</a>
                                    </div>
                                    <div class="dash-icon">
                                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#renameModal{{ $document->id }}"><i class="fa-solid fa-pen-to-square"></i>Rename</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade editar-modal" id="editarModal{{ $document->id ?? '' }}" tabindex="-1" aria-labelledby="savedDocumentLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="savedDocumentLabel"></h5>
                                <div class="close-btn-wrp">
                                    <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="modal-body">
                                {{-- <p>¿Desde qué punto te gustaría ver el documento?</p> --}}
                                <p> Where would you like to start?</p>
                                <div class="login_button">
                                    {{-- <a id="from_first_step_{{ $document->id ?? '' }}" onclick="modifiedStep(event, 'first', '{{ $firstUrl ?? '' }}');">Desde la primera hoja</a> --}}
                                    {{-- <a id="from_last_step_{{ $document->id ?? '' }}" onclick="modifiedStep(event, 'last', '{{ $lastUrl ?? '' }}');">Desde el último cambio</a> --}}
                                    <a id="from_first_step_{{ $document->id ?? '' }}" onclick="modifiedStep(event, 'first', '{{ $firstUrl ?? '' }}');">Start from first page</a>
                                    <a id="from_last_step_{{ $document->id ?? '' }}" onclick="modifiedStep(event, 'last', '{{ $lastUrl ?? '' }}');">Jump to last change</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade comprar-modal" id="comprarModal{{ $document->id ?? '' }}" tabindex="-1" aria-labelledby="savedDocumentLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="savedDocumentLabel"></h5>
                                <div class="close-btn-wrp">
                                    <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </div>
                            <div class="modal-body">
                                {{-- <p>¿Desde qué punto te gustaría ver el documento?</p> --}}
                                <p> Where would you like to start?</p>
                                <div class="login_button">
                                    <a id="from_first_step_{{ $document->id ?? '' }}" onstyclick="modifiedStep(event, 'first', '{{ $firstUrl ?? '' }}');">Start from first page</a>
                                    <a id="from_last_step_{{ $document->id ?? '' }}" onclick="modifiedStep(event, 'last', '{{ $lastUrl ?? '' }}');">Jump to last change</a>
                                    {{-- <a id="from_first_step_{{ $document->id ?? '' }}" onstyclick="modifiedStep(event, 'first', '{{ $firstUrl ?? '' }}');">Desde la primera hoja</a>
                                    <a id="from_last_step_{{ $document->id ?? '' }}" onclick="modifiedStep(event, 'last', '{{ $lastUrl ?? '' }}');">Desde el último cambio</a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="renameModal{{ $document->id }}" tabindex="-1" aria-labelledby="renameModalLabel{{ $document->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="renameModalLabel{{ $document->id }}">Rename Draft</h5>
                                <div class="close-btn-wrp">
                                    <button type="button" class="close btn-close x-draft-button" data-bs-dismiss="modal" aria-label="Close">
                                    {{-- <i class="fa-solid fa-xmark"></i> --}}</button>
                                </div>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('user.saved.rename', $document->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="mb-3">
                                        <label for="rename_title_{{ $document->id }}" class="form-label">Contract name</label>
                                        <input type="text" class="form-control" id="rename_title_{{ $document->id }}" name="title" value="{{ strip_tags($document->title) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="rename_desc_{{ $document->id }}" class="form-label">Description</label>
                                        <textarea class="form-control" id="rename_desc_{{ $document->id }}" name="short_description" rows="3" placeholder="Add a short description...">{{ strip_tags($document->short_description) }}</textarea>
                                    </div>
                                    <div class="login_button">
                                        <button type="submit" class="tit-des-renm-btn">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            No saved documents found
        @endif
    </div>
</div>

<script>
    let attemptedQuestions = @json($attemptedQuestions);
    let firstQuestions = @json($firstQuestionsPerDocument);

    let savedIdToDocMap = @json($savedDocumentIDMap);
    let user_id = "{{ Auth::user()->id ?? '' }}";

    // $(document).ready(function(){
    //     setLocalstorage(attemptedQuestions, savedIdToDocMap, user_id);
    // });


    function modifiedStep(e,status,url){
        e.preventDefault();
        if(status == 'first'){
            setLocalstorage(firstQuestions, savedIdToDocMap, user_id);
            window.location.href = url;
        }else if(status == 'last'){
            setLocalstorage(attemptedQuestions, savedIdToDocMap, user_id);
            window.location.href = url;
        }
    }

    function setLocalstorage(attemptedQuestions, savedIdToDocMap, user_id) {
        let localStorageData = JSON.parse(localStorage.getItem('Localstorage')) || {};
        let docIdsToReset = new Set();

        $.each(attemptedQuestions, function(key, value) {
            let document_id = savedIdToDocMap[value.saved_id];
            if (document_id) {
                docIdsToReset.add(document_id);
            }
        });

        docIdsToReset.forEach(docId => {
            delete localStorageData[docId];
        });

        $.each(attemptedQuestions, function(key, value) {
            let document_id = savedIdToDocMap[value.saved_id];
            if (!document_id) return;

            let savedValue = {
                question_id: value.question_id,
                type: value.question_type,
                attempted_answer: value.answer,
                attempted_value: value.attempted_value,
                previous_id: value.prev_id,
                next_id: value.next_id,
                progress: value.progress,
                total_steps: value.total_steps,
                attempted_step: value.attempted_steps,
                label: value.question_label,
            };

            if (!localStorageData[document_id]) {
                localStorageData[document_id] = {
                    user_id: user_id,
                    attempted_question: []
                };
            }

            localStorageData[document_id].attempted_question.push(savedValue);
        });

        localStorage.setItem('Localstorage', JSON.stringify(localStorageData));
        console.log("Updated localStorage:", localStorageData);
    }

</script>


<script>
    // for three dots //
    // $(document).ready(function () {
    //     $(document).on("click", ".elps_icn", function (event) {
    //         event.stopPropagation(); // Prevents click event from bubbling up

    //         // Close all other dropdowns
    //         $(".dropdown-menu_review").not($(this).next()).removeClass("show");

    //         // Toggle only the corresponding dropdown
    //         $(this).next(".dropdown-menu_review").toggleClass("show");
    //     });

    //     // Close the dropdown if clicking outside
    //     $(document).on("click", function (event) {
    //         if (!$(event.target).closest(".dropdown-menu_review, .elps_icn").length) {
    //             $(".dropdown-menu_review").removeClass("show");
    //         }
    //     });
    // });


    $(document).ready(function () {
        // When clicking the 3 dots (elps_icn)
        $(document).on("click", ".elps_icn", function (event) {
            event.stopPropagation(); // Prevent bubbling

            // Close other dropdowns
            $(".dropdown-menu_review").not($(this).next()).removeClass("show");

            // Toggle only this one
            $(this).next(".dropdown-menu_review").toggleClass("show");
        });

        // Close dropdowns when clicking outside
        $(document).on("click", function (event) {
            if (!$(event.target).closest(".dropdown-menu_review, .elps_icn").length) {
                $(".dropdown-menu_review").removeClass("show");
            }
        });


        $(document).on("mouseleave", ".shr_dt", function () {
            $(this).find(".dropdown-menu_review").removeClass("show");
        });
    });



</script>

@endsection
