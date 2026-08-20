@extends('users_layout.custom_header')
@section('title', $document->title ?? 'Legalio')
@section('content')

    <style>
       .input-error {
    border: 1.5px solid #f28b82 !important;
    outline: none;
}

.error-msg {
    color: #d93025;
    font-size: 12px;
    margin-top: 4px;
    display: block;
}



        .question-div {
            display: none;
        }

        .question-div.active {
            display: block;
        }

        .hide {
            display: none;
        }

        .active_sec {
            animation: colorHighlight 4s forwards;
            /* 4s animation, forwards keeps final state */
            transition: all 0.3s ease-in-out;
            /* Smooth transitions for other properties */
        }

        .answered_spns.active {
            background: #002655;
            color: #fff;
        }

        @media only screen and (max-width: 991px) {
            #ui-datepicker-div {
                z-index: 10 !important;
            }
        }

        @media only screen and (max-width: 575px) {
            .shaking-div:has(.question-div.active) {
                padding: 30px 20px 15px 20px !important;
            }

            #ui-datepicker-div {
                max-width: 220px;
                font-size: 14px;
            }

            .save_document_button {
                top: -22px;
                height: 20px;
                width: 20px;
            }
        }

        /* 19 march */
        #main-question-form-controller .right-box.right-question-box {
            font-family: "Inter", sans-serif;
        }

        /* #main-question-form-controller .right-box.right-question-box .contract-preview,
        #main-question-form-controller .right-box.right-question-box .contract-preview * {
            user-select: none !important;
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            cursor: default !important;
        } 
         .state-compliance-badge {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        } 

         #protected-preview-panel,
        #protected-preview-panel *,
        .r_div,
        .right-sec-div,
        .answered_spns,
        .contract-priveiw-side {
            user-select: none !important;
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            -khtml-user-select: none !important;
            cursor: default !important;
        } */

    </style>


    <section class="privacy-sec questions_page_main_div contract_page_main_div">
        @if ($questions->isNotEmpty() || $documentContents->isNotEmpty())
            <div class="container">
                <div class="contract-header">
                    <div class="row document_align">
                        <div class="col-md-8">
                            <div class="contract_heading_div">
                                <h1>{{ $document->title ?? '' }}</h1>

                                @php
                                    $avgRating = $document->getavgRating();
                                    $allDocumentRating = \App\Models\Document::getAllDocumentAvgRating();
                                    // dd($allDocumentRating);

                                    // dd($avgRating);
                                @endphp

                                @if($avgRating !== false)
                                    <x-rating-review-component :showReviews="$showReviews" :document="$document"
                                        :rating="$avgRating" ratingClass="cont_rate" :ratingText="$data['rating_text'] ?? '' "
                                        :reviewHaceText="$data['review_modal_hace_text'] ?? '' " />
                                @else
                                    <x-rating-review-component :showReviews="$showReviews" :document="$document"
                                        :rating="$allDocumentRating" ratingClass="cont_rate" :ratingText="$data['rating_text'] ?? '' " :reviewHaceText="$data['review_modal_hace_text'] ?? '' " :showDescription="true" />
                                @endif



                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="contract-progress">
                                <div class="progressLabel">
                                    <span class="progressCount">0%</span>
                                    <input type="hidden" id="percent_count" value="0">
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                                        aria-valuemin="0" aria-valuemax="100" aria-label="progress-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="valid_in_check tick_img">
                            <img src="{{ asset('assets/img/org_tick.svg') }}" alt="">
                            {{-- Validez en todo México --}}
                            Customized to your U.S. state
                        </div>
                    </div>
                </div>
            </div>
            <!-- This is the main container for the question and the form  -->

            <div class="main_questn">
                <div class="container">
                    <div id="main-question-form-controller" class="row outer_main">
                        <div class="info-div hide">
                            <div class="info-content">
                                <span class="info-close">x</span>
                                <span class="infoqu-txt"></span>
                            </div>
                        </div>
                        <div class="left-box left-question-box col-md-4">
                            <div class="left_heding">
                                <h3 class="contarct_top_left_heading">
                                    {{-- {{ $data['contract_heading'] ?? 'Introduce los datos aquí:' }} --}}
                                    {{ $data['contract_heading'] ?? 'Enter the information here ' }}:
                                </h3>
                            </div>
                            <form id="contractForm">
                                <input type="hidden" id="document_id" name="document_id" value="{{ $id ?? '' }}">
                                <input type="hidden" id="total_step" value="{{ $total_questions ?? '' }}">
                                <input type="hidden" id="all_attempted" value="0">
                                <input type="hidden" id="user_id" value="{{ Auth::user()->id ?? '' }}">
                                <!-- <input type="hidden" id="is_login" value="{{ $is_login ?? '' }}"> -->
                                <input type="hidden" id="is_login" value="{{ auth()->check() ? '1' : '0' }}">
                                <input type="hidden" id="type" value="{{ $_GET['type'] ?? '' }}">
                                <input type="hidden" id="order_id" value="{{ $_GET['order_id'] ?? '' }}">
                                <input type="hidden" id="has_subscription" value="{{ $_GET['has_subscription'] ?? '' }}">
                                <input type="hidden" id="has_credits" value="{{ $_GET['has_credits'] ?? '' }}">
                                <input type="hidden" id="edit_count" value="{{ $_GET['edit_count'] ?? '' }}">

                                @php
                                    $count = 1;
                                    $num = 1;   
                                    $total_steps = count($questions);
                                @endphp
                                {{-- @dd($questions->toArray()) --}}
                                @foreach ($questions as $index => $question)
                                    <div class="shaking-div">
                                        <div class="question-div step{{ $count ?? '' }} step-{{ $question->id }}"
                                            que_label="{{ $question->questionData->question_label ?? '' }}"
                                            que_id="{{ $question->id ?? '' }}" data-type="{{ $question->type ?? '' }}"
                                            is_condition="{{ $question->is_condition ?? '' }}"
                                            swtchtyp="{{ $question->condition_type ?? '' }}" data-count="{{ $count ?? '' }}"
                                            is_last="{{ $loop->last ? 'true' : '' }}">
                                            @if (!empty($question->questionData->question_info_text) && $question->questionData->question_info_text !== 'null')
                                                <div class="save_document_button" id="info_{{ $question->id ?? '' }}"
                                                    onclick="getQuestionInfo(`{{ $question->id ?? '' }}`, `{{ $question->questionData->question_info_text ?? '' }}`)">
                                                    <span class="infoimg"><img src="{{ asset('assets/img/contract_info.svg') }}"></span>
                                                </div>
                                            @endif

                                            <label class="que_heading lbl-{{ $question->id }}">
                                                @if ($question->is_condition == 1)
                                                                            <?php
                                                    $labelCondition = App\Models\QuestionCondition::where('question_id', $question->id)->where('condition_type', 'question_label_condition')->first();

                                                    echo $labelCondition?->question_label ?? $question->questionData->question_label;
                                                                                                                                                                                                                                                                                                    ?>
                                                @else
                                                    {{ $question->questionData->question_label ?? '' }}
                                                @endif

                                            </label>

                                            <br>
                                            @php
                                                $question_type = $question->type;
                                                $next_qid = null;  
                                            @endphp
                                            @if($question_type == 'textbox' || $question_type == 'text')
                                                @php
                                                    $next_qid = $question->questionData->next_question_id ?? '';
                                                @endphp
                                                <input type="text" target-id="qidtarget-{{ $question->id ?? '' }}"
                                                    id="{{ $question->id ?? '' }}" name="{{ $question->id ?? '' }}"
                                                    onkeyup="storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}', '{{ $next_qid ?? '' }}')"
                                                    {{--
                                                    onchange="storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}', '{{ $next_qid ?? '' }}')"
                                                    --}} placeholder="{{ $question->questionData->text_box_placeholder ?? '' }}"
                                                    data-placeholdertext="__________" />
                                            @elseif($question_type == 'textarea')
                                                @php
                                                    $next_qid = $question->questionData->next_question_id ?? '';
                                                @endphp
                                                <textarea class="contract_textarea" target-id="qidtarget-{{ $question->id ?? '' }}"
                                                    id="{{ $question->id ?? '' }}" name="{{ $question->id ?? '' }}"
                                                    onkeyup="storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}', '{{ $next_qid ?? '' }}')"
                                                    {{--
                                                    onchange="storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}', '{{ $next_qid ?? '' }}')"
                                                    --}} placeholder="{{ $question->questionData->text_box_placeholder ?? '' }}"
                                                    data-placeholdertext="__________"></textarea>
                                            @elseif($question_type == 'dropdown')
                                                @php
                                                    $next_qid = $question->options->first()->next_question_id ?? '';
                                                @endphp
                                                <div class="custom-select-wrapper">
                                                    <select
                                                        onchange="updateNextButton(this, '{{ $question->id ?? '' }}'); storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}') "
                                                        target-id="qidtarget-{{ $question->id ?? '' }}" id="{{ $question->id ?? '' }}"
                                                        name="{{ $question->id ?? '' }}">
                                                        @foreach ($question->options as $option)
                                                            <option my_ref_nxt=".nxt_btn_{{ $question->id ?? '' }}"
                                                                que_id="{{ $option->next_question_id ?? '' }}"
                                                                value="{{ $option->option_value ?? '' }}" {{ $loop->first ? 'selected' : '' }}>
                                                                {{ $option->option_label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @elseif($question_type == 'radio-button' || $question_type == 'radio')
                                                @php
                                                    $next_qid = $question->options->first()->next_question_id ?? '';
                                                @endphp
                                                @foreach ($question->options as $option)
                                                    <div class="radio_div">
                                                        <label>
                                                            <input type="radio" name="question_{{ $question->id ?? '' }}"
                                                                target-id="qidtarget-{{ $question->id ?? '' }}"
                                                                id="radio_{{ $question->id ?? '' }}{{ $num++ ?? '' }}"
                                                                onchange="updateNextButtonR(this); storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}')"
                                                                my_ref_nxt=".nxt_btn_{{ $question->id ?? '' }}"
                                                                que_id="{{ $option->next_question_id ?? '' }}"
                                                                value="{{ $option->option_value ?? '' }}" {{ $loop->first ? 'checked' : '' }} />
                                                            {{ $option->option_label }}
                                                        </label>
                                                    </div>

                                                @endforeach
                                            @elseif($question_type == 'date-field' || $question_type == 'date')
                                                @php
                                                    $next_qid = $question->questionData->next_question_id ?? '';
                                                @endphp
                                                <div class="date-container">

                                                    <input type="text" class="contract_date"
                                                        target-id="qidtarget-{{ $question->id ?? '' }}" id="{{ $question->id ?? '' }}"
                                                        name="{{ $question->id ?? '' }}"
                                                        onchange="storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}')"
                                                        placeholder="dd/mm/aaaa" autocomplete="off" />
                                                    <img src="{{ asset('assets/images/icon-calendar.svg') }}" alt="calender"
                                                        class="custom-icon">
                                                </div>
                                            @elseif($question_type == 'pricebox')
                                                @php
                                                    $next_qid = $question->questionData->next_question_id ?? '';
                                                @endphp
                                                <input type="text" target-id="qidtarget-{{ $question->id ?? '' }}"
                                                    id="{{ $question->id ?? '' }}" name="{{ $question->id ?? '' }}"
                                                    onkeyup="storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}')"
                                                    {{--
                                                    onchange="storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}', '{{ $next_qid ?? '' }}')"
                                                    --}} placeholder="{{ $question->questionData->text_box_placeholder ?? '' }}"
                                                    data-placeholdertext="__________" />
                                            @elseif($question_type == 'number-field' || $question_type == 'number')
                                                @php
                                                    $next_qid = $question->questionData->next_question_id ?? '';
                                                @endphp
                                                <input type="text" target-id="qidtarget-{{ $question->id ?? '' }}"
                                                    id="{{ $question->id ?? '' }}" name="{{ $question->id ?? '' }}" {{--
                                                    onchange="storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}', '{{ $next_qid ?? '' }}')"
                                                    --}}
                                                    onkeyup="storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}')"
                                                    placeholder="{{ $question->questionData->text_box_placeholder ?? '' }}"
                                                    data-placeholdertext="__________" />
                                            @elseif($question_type == 'percentage-box')
                                                @php
                                                    $next_qid = $question->questionData->next_question_id ?? '';
                                                @endphp
                                                <input type="text" target-id="qidtarget-{{ $question->id ?? '' }}"
                                                    id="{{ $question->id ?? '' }}" name="{{ $question->id ?? '' }}"
                                                    onkeyup="storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}')"
                                                    {{--
                                                    onchange="storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}', '{{ $next_qid ?? '' }}')"
                                                    --}} placeholder="{{ $question->questionData->text_box_placeholder ?? '' }}"
                                                    data-placeholdertext="__________" />
                                            @elseif($question_type == 'dropdown-link' || $question_type == 'select')
                                                @php
                                                    $next_qid = $question->questionData->next_question_id ?? '';
                                                @endphp
                                                <div class="custom-select-wrapper">
                                                    <select
                                                        onchange="updateDropdownLInk(this, '{{ $question->id ?? '' }}'); storeAnswers(this, '{{ $question->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}') "
                                                        target-id="qidtarget-{{ $question->id ?? '' }}" id="{{ $question->id }}"
                                                        name="{{ $question->id ?? '' }}">
                                                        <option value="{{ $question->questionData->same_contract_link_label ?? '' }}"
                                                            selected>
                                                            {{ $question->questionData->same_contract_link_label ?? '' }}
                                                        </option>
                                                        @foreach ($question->options as $option)
                                                            <option my_ref_nxt=".nxt_btn_{{ $question->id ?? '' }}"
                                                                que_id="{{ $question->questionData->next_question_id ?? '' }}"
                                                                value="{{ $option->contract_link ?? '' }}">
                                                                {{ $option->option_label ?? '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            <div class="navigation-btns mt-4">
                                                <div class="contract_btns">
                                                    @if ($index != 0)
                                                        <div class="nav_prev">
                                                            <button type="button" class="pre_btn_{{ $question->id }} pre" que_id=""
                                                                my_ref="{{ $question->id }}" onclick="go_pre_step(this)">
                                                                {{-- Anterior --}}
                                                                Previous
                                                            </button>
                                                        </div>
                                                        <div class="save_document_btn">
                                                            @if (Auth::check())
                                                                <a class="add_on_btn guardar_btn" href="{{ url()->full() }}"><img
                                                                        src="{{ asset('assets/img/save_btn.svg') }}"></a>
                                                            @else
                                                                <a class="add_on_btn guardar_btn go_to_login"
                                                                    href="javascript:void(0);"><img
                                                                        src="{{ asset('assets/img/save_btn.svg') }}"></a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    <div class="nav_next">
                                                        <button type="button" class="nxt_btn_{{ $question->id ?? '' }} nxt"
                                                            que_id="{{ $next_qid ?? '' }}" data-next_step="{{ $next_qid ?? '' }}"
                                                            data-condition_step="{{ $question->questionData->conditional_go_to_step ?? '' }}"
                                                            my_ref="{{ $question->id ?? '' }}"
                                                            onclick="go_next_step(this, '{{ $question_type ?? '' }}')"
                                                            data-condition="{{ $question->conditions && count($question->conditions) > 0 ? json_encode($question->conditions) : null }}">
                                                            {{-- Siguiente --}}
                                                            Next    
                                                        </button>
                                                        <button type="button" class="last_step_btn nxt" style="display:none;"
                                                            onclick="go_to_checkout_page()">
                                                            {{-- Generar --}}
                                                            Generate
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php $count++; @endphp
                                @endforeach
                                <div class="prev-next-icons hide">
                                    <div class="left-vector-btn">
                                        <img src="{{ asset('assets/img/angle-left-1.svg') }}">
                                    </div>
                                    <div class="up-vector-btn">
                                        <img src="{{ asset('assets/img/angle-up.svg') }}">
                                    </div>
                                </div>
                            </form>

                                    {{-- <div class="state-compliance-badge">
                                            <img src="{{ asset('storage/USA_map_img/USA.svg') }}" style="width:25%; margin-top:10px;">
                                            <div class="badge-text" style="font-size: 15px;">
                                                We prepare your document in full compliance with the laws of the U.S. and the State of <Strong id="selected-state-label"></Strong>.
                                            </div>
                                    </div> --}}
                        </div>
                     
                        {{-- <div class="right-box right-question-box form-div card col-md-8 letest-inner-scl"> --}}
                        <div class="right-box right-question-box form-div card col-md-8 letest-inner-scl" id="protected-preview-panel">
                        <h6 id='contract-inner-preview'>Preview</h6>
                         <div class="contract-priveiw-side">
                            <div class="target-box hide">
                                <span class="pop-span">
                                    <div class="left-vector-btn">
                                        <img src="{{ asset('assets/img/angle-left-1.svg') }}">
                                    </div>
                                    Por favor, completa los datos en el formulario ubicado a la izquierda.
                                </span>
                            </div>
                            {{-- @foreach ($documentContents as $content) --}}
                            @foreach ($documentContents as $key => $content)
                                    @php
                                        if (strpos($content->content, '{QID') !== false) {
                                            \Log::info(' Content has QID placeholder: ' . $content->id);
                                        }
                                    @endphp
                                    @if ($content->secure_blur_content == 1)

                                        @if(auth()->check() && auth()->user()->is_admin)

                                            {{-- italic content for admin user --}}
                                            <div id="right_content_div_{{ $content->id ?? '' }}"
                                                style="text-align:{{ $content->text_align ?? '' }}; font-style: italic;"
                                                class="r_div right-sec-div mb-2 qwe"
                                                conditional_section="{{ $content->is_condition ? 'true' : null }}"
                                                data-conditions="{{ $content->conditions && count($content->conditions) > 0 ? json_encode($content->conditions) : null }}">
                                                @if ($content->type == 'content_heading')
                                                    <p
                                                        style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400; font-style: italic;">
                                                        {{-- {!! $content->content !!} --}}
                                                        {!! $adminDocumentContents[$key]->content !!}
                                                    </p>
                                                @elseif($content->type == 'signature_field')
                                                    @if ($content->signature_field != 0)
                                                        <span class="sign_field">__________________</span>
                                                        <p
                                                            style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400; font-style: italic;">
                                                            {{-- {!! $content->content !!} --}}
                                                            {!! $adminDocumentContents[$key]->content !!}
                                                        </p>
                                                        <p
                                                            style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400; font-style: italic;">
                                                            {{-- {!! $content->content2 !!} --}}
                                                            {!! $adminDocumentContents[$key]->content2 !!}
                                                        </p>
                                                        <p
                                                            style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400; font-style: italic;">
                                                            {{-- {!! $content->content3 !!} --}}
                                                            {!! $adminDocumentContents[$key]->content3 !!}
                                                        </p>
                                                    @endif
                                                @else
                                                    {{-- <span style="font-style: italic;">{!! $content->content !!}</span> --}}
                                                    <span style="font-style: italic;">{!! $adminDocumentContents[$key]->content !!}</span>
                                                @endif
                                            </div>

                                        @else
                                            {{-- blur content for normal user --}}
                                            <div id="right_content_div_{{ $content->id ?? '' }}"
                                                style="text-align:{{ $content->text_align ?? '' }}"
                                                class="r_div right-sec-div secure_content secure_blur_sec mb-2"
                                                conditional_section="{{ $content->is_condition ? 'true' : null }}"
                                                data-conditions="{{ $content->conditions && count($content->conditions) > 0 ? json_encode($content->conditions) : null }}">
                                                @if ($content->type == 'content_heading')
                                                    <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                                        {!! $content->content !!}
                                                    </p>
                                                @elseif($content->type == 'signature_field')
                                                    @if ($content->signature_field != 0)
                                                        <span class="sign_field">__________________</span>
                                                        <p class="blur-text-spn"
                                                            style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                                            {!! $content->content !!}
                                                        </p>
                                                        <p class="blur-text-spn"
                                                            style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                                            {!! $content->content2 !!}
                                                        </p>
                                                        <p class="blur-text-spn"
                                                            style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                                            {!! $content->content3 !!}
                                                        </p>
                                                    @endif
                                                @else
                                                    <span class="blur-text-spn">{!! $content->content !!}</span>
                                                @endif
                                                <span class="text-hover">El texto borroso se hace visibe al descargar el
                                                    documento.</span>
                                            </div>
                                        @endif


                                    @elseif($content->is_condition == 0)
                                            <span style="text-align:{{ $content->text_align ?? '' }}" class="r_div">
                                                {{-- @elseif($content->is_condition == 0)
                                                <div style="text-align:{{ $content->text_align ?? '' }}" class="r_div"> --}}
                                                    @if ($content->type == 'content_heading')
                                                        <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                                            {!! $content->content !!}
                                                        </p>
                                                    @elseif($content->type == 'signature_field')
                                                        @if ($content->signature_field != 0)
                                                            <span class="sign_field">__________________</span>
                                                            <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                                                {!! $content->content !!}
                                                            </p>
                                                            <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                                                {!! $content->content2 !!}
                                                            </p>
                                                            <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                                                {!! $content->content3 !!}
                                                            </p>
                                                        @endif
                                                    @else
                                                        {!! $content->content !!}
                                                    @endif
                                            </span>
                                            {{--
                                        </div> --}}
                                    @else
                                    <div id="right_content_div_{{ $content->id ?? '' }}" style="text-align:{{ $content->text_align ?? '' }}"
                                        class="r_div right-sec-div mb-2" conditional_section="{{ $content->is_condition ? 'true' : null }}"
                                        data-conditions="{{ $content->conditions && count($content->conditions) > 0 ? json_encode($content->conditions) : null }}">
                                        @if ($content->type == 'content_heading')
                                            <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                                {!! $content->content !!}
                                            </p>
                                        @elseif($content->type == 'signature_field')
                                            @if ($content->signature_field != 0)
                                                <span class="sign_field">__________________</span>
                                                <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                                    {!! $content->content !!}
                                                </p>
                                                <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                                    {!! $content->content2 !!}
                                                </p>
                                                <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                                    {!! $content->content3 !!}
                                                </p>
                                            @endif
                                        @else
                                            {!! $content->content !!}
                                        @endif
                                    </div>
                                @endif
                            @endforeach

                            </div>
                        </div>
                        
                    </div>
                 </div>
            </div>
        @else
            <div class="container">This Document is empty</div>
        @endif
    </section>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-ui-datepicker-with-i18n@1.10.4/ui/i18n/jquery.ui.datepicker-es.js">
    </script>


    <script>

        let total_steps = parseInt($('#total_step').val()) || 1;
        let total_attempted = 0;

        let lastprogress = 0;

        

        $(document).ready(function () {
            $(".step1").addClass('active');

            //  Prevent all copy/paste/cut operations on contract content
            $('.right-question-box, .right-box, .r_div, .right-sec-div').on('cut copy paste', function (e) {
                e.preventDefault();
                return false;
            });

            //  Disable right-click context menu on contract content
            $('.right-question-box, .right-box').on('contextmenu', function (e) {
                e.preventDefault();
                return false;
            });

            //  Prevent drag selection
            $('.right-question-box, .right-box').on('selectstart dragstart', function (e) {
                e.preventDefault();
                return false;
            });

            $('form#contractForm select').each(function () {
                var id = $(this).attr('id');
                if (id != null && id != '' && id != undefined) {
                    var targetvalue = $('#' + id).val();
                    if (targetvalue == '' || targetvalue == null || targetvalue == undefined) {
                        var defaultvalue = $('#' + id).data('placeholdertext');
                        if (defaultvalue == '' || defaultvalue == null || defaultvalue == undefined) {
                            targetvalue = '_________';
                        } else {
                            targetvalue = defaultvalue;
                        }
                    }
                    $(".qidtarget-" + id).html(targetvalue);
                    $(".qidtarget-" + id).each(function () {
                        $(this).html(targetvalue);
                    });
                }
            });

            $('.go_to_login').click(function (e) {
                e.preventDefault();
                let currentUrl = encodeURIComponent(window.location.href);
                let loginUrl = "{{ route('login.user') }}" + "?redirecturl=" + currentUrl;
                location.href = loginUrl;
            });

            replaceQIDPlaceholders();
            showLastAttemptedValues();
            updateStateClauseDescriptions();
            rightSecConditions();
            alphabetList();
            updateUserIdInLocalStorage();
        })

            function updateUserIdInLocalStorage() {
            let user_id = "{{ Auth::user()->id ?? '' }}";
            let localStorageData = JSON.parse(localStorage.getItem('Localstorage')) || {};

            Object.keys(localStorageData).forEach(document_id => {
                if (localStorageData[document_id]) {
                    localStorageData[document_id].user_id = user_id;
                }
            });

            localStorage.setItem('Localstorage', JSON.stringify(localStorageData));
        }

        $(document).ready(function () {
            let document_id = $('#document_id').val();
            let is_login = $('#is_login').val() === '1';
            let localStorageData = JSON.parse(localStorage.getItem('Localstorage')) || {};
            let attemptedQuestions = (localStorageData[document_id]?.attempted_question) || [];

            if (attemptedQuestions.length > 0 && is_login) {
                storeAttemptedQuestions(attemptedQuestions);
            }
        });


        function storeAttemptedQuestions(questions) {
            const user_id = $('#user_id').val();
            const document_id = $('#document_id').val();

            $.ajax({
                url: "{{ url('/save/steps') }}",
                method: "POST",
                data: {
                    user_id: user_id,
                    document_id: document_id,
                    attempted_questions: questions,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function (response) {
                    return response;
                },
                error: function (xhr) {
                    console.error("Error saving steps:", xhr.responseText);
                }
            });
        }

        function alphabetList() {
            let alphabet = {
                1: "a",
                2: "b",
                3: "c",
                4: "d",
                5: "e",
                6: "f",
                7: "g",
                8: "h",
                9: "i",
                10: "j",
                11: "k",
                12: "l",
                13: "m",
                14: "n",
                15: "o",
                16: "p",
                17: "q",
                18: "r",
                19: "s",
                20: "t",
                21: "u",
                22: "v",
                23: "w",
                24: "x",
                25: "y",
                26: "z"
            };

            if ($('.abclist').length) {
                for (var li = 1; li <= 10; li++) {
                    if ($('.abclist' + li).length) {
                        var num = 1;
                        $('.abclist' + li).each(function () {
                            if ($(this).closest('.right-sec-div').length > 0 && !$(this).closest('.right-sec-div')
                                .hasClass('d-none')) {
                                $(this).html(alphabet[num]);
                                num++;
                            } else if ($(this).closest('.right-sec-div').length > 0 && $(this).closest(
                                '.right-sec-div').hasClass('d-none')) {

                            } else if (!$(this).closest('div').hasClass('d-none')) {
                                $(this).html(alphabet[num]);
                                num++;
                            } else {
                                $(this).html(alphabet[num]);
                                num++;
                            }

                            if (num == 26) {
                                num = 1;
                            }
                        });
                    }
                }
            }
        }

        let isScrolling = false;

        function rightSecConditions() {
            $('.right-sec-div').each(function () {
                if ($(this).data('conditions') != null && $(this).data('conditions') != '' && $(this).data(
                    'conditions') != undefined) {
                    var conditions = $(this).data('conditions');
                    var is_elem_show = true;

                    $.each(conditions, function (key, val) {
                        var queId = val.conditional_question_id;
                        var queValue = val.conditional_question_value;
                        var conditionalCheck = val.conditional_check;

                        if ($('#' + queId).length) {
                            if (conditionalCheck == '1') {
                                if ($('#' + queId).val() == queValue && is_elem_show == true) {
                                    is_elem_show = true;
                                } else {
                                    is_elem_show = false;
                                }
                            } else if (conditionalCheck == '2') {
                                if ($('#' + queId).val() > queValue && is_elem_show == true) {
                                    is_elem_show = true;
                                } else {
                                    is_elem_show = false;
                                }
                            } else if (conditionalCheck == '3') {
                                if ($('#' + queId).val() < queValue && is_elem_show == true) {
                                    is_elem_show = true;
                                } else {
                                    is_elem_show = false;
                                }
                            } else if (conditionalCheck == '4') {
                                if ($('#' + queId).val() != queValue && is_elem_show == true) {
                                    is_elem_show = true;
                                } else {
                                    is_elem_show = false;
                                }
                            }
                        } else if ($('input[type="radio"][name="question_' + queId + '"]').length) {
                            if (conditionalCheck == '1') {
                                if ($('input[type="radio"][name="question_' + queId + '"]:checked').val() ==
                                    queValue && is_elem_show == true) {
                                    is_elem_show = true;
                                } else {
                                    is_elem_show = false;
                                }
                            } else if (conditionalCheck == '2') {
                                if ($('input[type="radio"][name="question_' + queId + '"]:checked').val() >
                                    queValue && is_elem_show == true) {
                                    is_elem_show = true;
                                } else {
                                    is_elem_show = false;
                                }
                            } else if (conditionalCheck == '3') {
                                if ($('input[type="radio"][name="question_' + queId + '"]:checked').val() <
                                    queValue && is_elem_show == true) {
                                    is_elem_show = true;
                                } else {
                                    is_elem_show = false;
                                }
                            } else if (conditionalCheck == '4') {
                                if ($('input[type="radio"][name="question_' + queId + '"]:checked').val() !=
                                    queValue && is_elem_show == true) {
                                    is_elem_show = true;
                                } else {
                                    is_elem_show = false;
                                }
                            }
                        }
                    });

                    if (is_elem_show == true) {
                        $(this).removeClass('d-none');
                        $(this).addClass('active_sec');

                        if (!isScrolling) {
                            // $(this).find("span:first.answered_spns").addClass('active');
                            smoothScrollToTarget(this, '.right-question-box');
                        }
                    } else {
                        $(this).addClass('d-none');
                        $(this).removeClass('active_sec');
                    }
                }
            })
        }

        function replaceQIDPlaceholdersOLD() {
            $('.r_div, .right-sec-div, .right-box').find('*').addBack().contents().filter(function () {
                return this.nodeType === 3; 
            }).each(function () {
                const parent = this.parentNode;
                const text = this.nodeValue;

                //  Match {QID29}, {WQID29}, {W_QID29}, {29}, #29#
                if (!/{[W_]*QID\d+}|{\d+}|#\d+#/i.test(text)) return;

                const html = text.replace(
                    /\{(?:[W_]*QID)?(\d+)\}|#(\d+)#/gi,
                    function (match, g1, g2) {
                        const qid = g1 || g2;
                        const currentVal = $('#' + qid).val() || '__________';
                        return `<span class="answered_spns qidtarget-${qid}">${currentVal}</span>`;
                    }
                );

                if (html !== text) {
                    const wrapper = document.createElement('span');
                    wrapper.innerHTML = html;
                    parent.replaceChild(wrapper, this);
                }
            });

            // Also fix any remaining literal text in innerHTML (for dynamically rendered content)
            $('.r_div, .right-sec-div').each(function () {
                let html = $(this).html();
                const updated = html.replace(
                    /\{(?:[W_]*QID)?(\d+)\}/gi,
                    function (match, qid) {
                        const currentVal = $('#' + qid).val() || '__________';
                        return `<span class="answered_spns qidtarget-${qid}">${currentVal}</span>`;
                    }
                );
                if (updated !== html) {
                    $(this).html(updated);
                }
            });
        }

        function replaceQIDPlaceholders() {
    $('.r_div, .right-sec-div').each(function () {
        let html = $(this).html();

        if (!/{[W_]*QID\d+}/i.test(html) && !/\{(\d+)\}/.test(html)) return;

        const updated = html.replace(
            /\{(?:[W_]*QID)?(\d+)\}/gi,
            function (match, qid) {
                if (html.indexOf('qidtarget-' + qid + '"') !== -1) return match;
                const currentVal = $('#' + qid).val() || '__________';
                return `<span class="answered_spns qidtarget-${qid}">${currentVal}</span>`;
            }
        );

        if (updated !== html) {
            $(this).html(updated);
        }
    });
}

        function replaceQIDPlaceholdersOLD() {
            $('.r_div, .right-sec-div').each(function () {
                var content = $(this).html();
                var regex = /\{QID(\d+)\}/g;
                var matches = content.match(regex);

                if (matches) {
                    matches.forEach(function (match) {
                        var qid = match.replace(/\{QID|\}/g, '');
                        var value = $('#' + qid).val() || '__________';

                        // Replace in content
                        content = content.replace(match, '<span class="qidtarget-' + qid + ' answered_spns">' + value + '</span>');
                    });

                    $(this).html(content);
                }
            });
        }

        // Function to update the progress bar based on total attempted steps
        function updateProgressBar() {
            // console.log(total_attempted);
            var percent = (total_attempted / total_steps) * 100;
            // console.log(percent);
            var value = parseInt(percent);
            $('#percent_count').val(value);
            $('.progressCount').text(value + "%");
            $('.progress-bar').css("width", value + "%");
        }

        function progressBarCount(id, next_id, is_last = false) {
            var id = parseInt(id);
            var next_id = parseInt(next_id);
            var current_step = $('.step-' + id);
            var total_hidden_steps = 0;
            var back_step = 0;

            if (id > next_id) {
                for (let i = id - 1; i >= next_id; i--) {
                    back_step++;
                }

                total_attempted -= back_step;
                if (total_attempted < 0) {
                    total_attempted = Math.abs(total_attempted);
                }

            } else {
                for (let i = id + 1; i < next_id; i++) {
                    if ($('.step-' + i).hasClass('hide')) {
                        total_hidden_steps++;
                    }
                }

                total_attempted = parseInt(total_attempted);
                total_attempted += total_hidden_steps;

                if ($(current_step).hasClass('done')) {
                    total_attempted++;
                }
            }

            // console.log("Total attempted steps:", total_attempted);

            if (is_last) {
                $('#percent_count').val(100);
                $('.progressCount').text("100%");
                $('.progress-bar').css("width", "100%");
            } else {
                updateProgressBar();
            }

            $('#all_attempted').val(total_attempted);
        }

        function reverseProgressCount(id, next_id) {
            var id = parseInt(id);
            var next_id = parseInt(next_id);
            // console.log(id, next_id);
            var current_step = $('.step-' + id);
            var total_hidden_steps = 0;
            var back_step = 0;

            if (id < next_id) {
                for (let i = id + 1; i <= next_id; i++) {
                    // console.log(i);
                    back_step++;
                }

                total_attempted -= back_step;
                if (total_attempted < 0) {
                    total_attempted = Math.abs(total_attempted);
                }

            } else {
                for (let i = parseInt(id) - 1; i >= next_id; i--) {
                    if ($('.step-' + i).hasClass('hide')) {
                        total_hidden_steps++;
                    }
                }
                total_attempted -= total_hidden_steps;
                total_attempted--;
            }

            // console.log("Total reverse steps:", total_attempted);

            if (total_attempted >= 0) {
                updateProgressBar();
                $('#all_attempted').val(total_attempted);
            }
        }

            function validateCurrentStep(my_ref, questionType) {
                $('.step-' + my_ref + ' input, .step-' + my_ref + ' textarea, .step-' + my_ref + ' select')
                    .removeClass('input-error');
                $('.step-' + my_ref + ' .error-msg').remove();

                let isValid = true;

                if (questionType === 'textbox' || questionType === 'text') {
                    let $input = $('#' + my_ref);
                    let val = $input.val().trim();
                    let placeholder = $input.attr('placeholder') || '';

                    // Check if placeholder suggests email
                    if (placeholder.toLowerCase().includes('email')) {
                        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (val !== '' && !emailRegex.test(val)) {
                            $input.addClass('input-error');
                            $input.after('<span class="error-msg">Please enter a valid email address.</span>');
                            isValid = false;
                        }
                    }

                    // Check if placeholder suggests phone
                    else if (placeholder.toLowerCase().includes('phone') || placeholder.toLowerCase().includes('mobile')) {
                        let phoneRegex = /^[0-9\+\-\(\)\s]{7,15}$/;
                        if (val !== '' && !phoneRegex.test(val)) {
                            $input.addClass('input-error');
                            $input.after('<span class="error-msg">Please enter a valid phone number.</span>');
                            isValid = false;
                        }
                    }

                } else if (questionType === 'number-field' || questionType === 'number') {
                    let $input = $('#' + my_ref);
                    let val = $input.val().trim();
                    if (val !== '' && isNaN(val)) {
                        $input.addClass('input-error');
                        $input.after('<span class="error-msg">Please enter a valid number.</span>');
                        isValid = false;
                    }

                } else if (questionType === 'pricebox') {
                    let $input = $('#' + my_ref);
                    let val = $input.val().trim().replace(/[$,]/g, '');
                    if (val !== '' && isNaN(val)) {
                        $input.addClass('input-error');
                        $input.after('<span class="error-msg">Please enter a valid price.</span>');
                        isValid = false;
                    }

                } else if (questionType === 'percentage-box') {
                    let $input = $('#' + my_ref);
                    let val = $input.val().trim();
                    if (val !== '' && (isNaN(val) || parseFloat(val) < 0 || parseFloat(val) > 100)) {
                        $input.addClass('input-error');
                        $input.after('<span class="error-msg">Please enter a percentage between 0 and 100.</span>');
                        isValid = false;
                    }

                } else if (questionType === 'date-field' || questionType === 'date') {
                    let $input = $('.step-' + my_ref + ' .contract_date');
                    let val = $input.val().trim();
                    let dateRegex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
                    if (val !== '' && !dateRegex.test(val)) {
                        $input.addClass('input-error');
                        $input.after('<span class="error-msg">Please enter a valid date (dd/mm/yyyy).</span>');
                        isValid = false;
                    }
                }

                return isValid;
            }

        function go_next_step(e, questionType) {
            $(e).prop("disabled", true);

              var my_ref_check = $(e).attr("my_ref");
                if (!validateCurrentStep(my_ref_check, questionType)) {
                    $(e).prop("disabled", false);
                    return;
                }

            var conditions = $(e).attr("data-condition");
            var next_step_id = $(e).attr("que_id");
            var labelElement = $(`.lbl-${next_step_id}`);

            var my_ref = $(e).attr("my_ref");
            var is_last = !next_step_id || next_step_id === '';
            var target = `.qidtarget-${next_step_id}`;
            var currentStepElement = $('.step-' + my_ref);
            var isLastAttribute = currentStepElement.attr('is_last');
            var is_last = (isLastAttribute === 'true');

            var needsFallback = (!next_step_id || next_step_id === '' || next_step_id === 'undefined');

            if (!is_last && needsFallback) {
                var allQuestionDivs = $('.question-div');
                var currentIndex = allQuestionDivs.index(currentStepElement);

                if (currentIndex >= 0 && currentIndex < allQuestionDivs.length - 1) {
                    var nextQuestionDiv = allQuestionDivs.eq(currentIndex + 1);
                    var foundNextId = nextQuestionDiv.attr('que_id');

                    if (foundNextId && foundNextId !== '' && foundNextId !== 'undefined') {
                        next_step_id = foundNextId;
                        $(e).attr("que_id", next_step_id);
                        $(e).attr("data-next_step", next_step_id);
                    } else {
                        is_last = true;
                    }
                } else if (currentIndex === allQuestionDivs.length - 1) {
                    is_last = true;
                } else {
                    console.log(" ERROR: Could not find current step in question collection! currentIndex:", currentIndex);
                    //  Don't set is_last = true here, try to proceed anyway
                }
            }

            console.log(" After fallback - next_step_id:", next_step_id, "| is_last:", is_last);

            var target = `.qidtarget-${next_step_id}`;
            var conditional_step = $(e).attr("data-condition_step");
            var next_step = $(e).attr("data-next_step");

            var labelElement = $(`.lbl-${next_step_id}`);
            if (labelElement.length) {
                let labelText = labelElement.text().trim();
                if (labelText === 'No label found') {
                    $(`#info_${next_step_id}`).hide();
                } else {
                    $(`#info_${next_step_id}`).show();
                }
            }

            // Process dropdown conditions
            if (questionType == 'dropdown') {
                if (conditions != null && conditions != '' && conditions != undefined) {
                    conditions = JSON.parse(conditions);
                    if (next_step_id != undefined && next_step_id != null && next_step_id != '') {
                        $.each(conditions, function (key, val) {
                            var condition_type = val.condition_type;
                            var queId = val.conditional_question_id;
                            var queValue = val.conditional_question_value;
                            var conditionalCheck = val.conditional_check;
                            var queLabel = val.question_label;
                            var sub_goToStep = val.go_to_step;

                            if (condition_type == 'go_to_step_condition') {
                                if (conditionalCheck == 1 || conditionalCheck == 2 || conditionalCheck == 3 || conditionalCheck == 4) {
                                    if ($('#' + queId).val() == queValue) {
                                        $(e).attr("que_id", conditional_step);
                                        next_step = conditional_step;
                                    } else {
                                        next_step_id = next_step;
                                        $(e).attr("que_id", next_step);
                                    }
                                }
                            }

                            if (condition_type == "another_go_to_step_condition") {
                                var subconditions = val.subconditions;
                                $.each(subconditions, function (sub_key, sub_val) {
                                    var sub_queId = sub_val.conditional_question_id;
                                    var sub_queValue = sub_val.conditional_question_value;
                                    var sub_conditionalCheck = sub_val.conditional_check;

                                    if (sub_conditionalCheck == 1 || sub_conditionalCheck == 2 || sub_conditionalCheck == 3 || sub_conditionalCheck == 4) {
                                        if ($('#' + sub_queId).val() == sub_queValue) {
                                            $(e).attr("que_id", sub_goToStep);
                                            next_step = sub_goToStep;
                                        } else {
                                            next_step_id = next_step;
                                            $(e).attr("que_id", next_step);
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            }

            var conditiontype = $('.step-' + next_step_id).attr("swtchtyp");
            var questionLabel = $('.step-' + next_step_id).attr("que_label");
            var next_conditions = $('.nxt_btn_' + next_step_id).attr("data-condition");
            var conditional_next_step = $('.nxt_btn_' + next_step_id).attr("data-condition_step");
            var nextStep = $('.nxt_btn_' + next_step_id).attr("data-next_step");

            if (next_conditions != null && next_conditions != '' && next_conditions != undefined) {
                next_conditions = JSON.parse(next_conditions);
                $.each(next_conditions, function (key, val) {
                    var condition_type = val.condition_type;
                    var queId = val.conditional_question_id;
                    var queValue = val.conditional_question_value;
                    var conditionalCheck = val.conditional_check;
                    var queLabel = val.question_label;
                    var sub_goToStep = val.go_to_step;

                    if (conditiontype == "1") {
                        if ($('#' + queId).val() == queValue) {
                            $(".lbl-" + next_step_id).text(queLabel);
                        }
                    } else if (conditiontype == "3") {
                        if (condition_type == "question_label_condition") {
                            if ($('#' + queId).val() == queValue) {
                                $(".lbl-" + next_step_id).text(queLabel);
                            }
                        } else if (condition_type == 'go_to_step_condition') {
                            if ($('#' + queId).val() == queValue) {
                                $(".nxt_btn_" + next_step_id).attr('que_id', conditional_next_step);
                            } else {
                                $(".nxt_btn_" + next_step_id).attr('que_id', nextStep);
                            }
                        } else if (condition_type == "another_go_to_step_condition") {
                            var subconditions = val.subconditions;
                            $.each(subconditions, function (sub_key, sub_val) {
                                var sub_queId = sub_val.conditional_question_id;
                                var sub_queValue = sub_val.conditional_question_value;
                                var sub_conditionalCheck = sub_val.conditional_check;

                                if ($('#' + sub_queId).val() == sub_queValue) {
                                    $(".nxt_btn_" + next_step_id).attr('que_id', sub_goToStep);
                                } else {
                                    $(".nxt_btn_" + next_step_id).attr('que_id', nextStep);
                                }
                            });
                        }
                    }
                });
            }

            if ($(".lbl-" + next_step_id).text().trim() === "") {
                $(".lbl-" + next_step_id).text("No label found");
            }

            console.log(" FINAL STATE before setTimeout:");
            console.log("   is_last:", is_last);
            console.log("   next_step_id:", next_step_id);
            console.log("   my_ref:", my_ref);

            setTimeout(function () {
                console.log(" Inside setTimeout - is_last:", is_last);

                if (is_last) {
                    console.log(" SHOWING GENERATE BUTTON (is_last = true)");
                    $('.last_step_btn').show();
                    $('.nxt_btn_' + my_ref).hide();

                    progressBarCount(my_ref, null, true);
                    next_step_id = 'last_step';
                    $('.step-' + my_ref).attr('is_last', 'true');

                    updateUrl(next_step_id);

                } else {
                    console.log(" NAVIGATING TO NEXT STEP:", next_step_id);

                    for (let i = parseInt(my_ref) + 1; i < parseInt(next_step_id); i++) {
                        if (!$('.step-' + i).hasClass('active')) {
                            $('.step-' + i).addClass('hide');
                        }
                    }

                    var pre_btn = `.pre_btn_${next_step_id}`;
                    $(pre_btn).attr("que_id", my_ref);

                    if (my_ref != null && my_ref != '' && my_ref != undefined) {
                        var current_step = `.step-${my_ref}`;
                        if ($(current_step).hasClass('active')) {
                            $(current_step).removeClass('active');
                            $(current_step).addClass('done');
                        }
                    }

                    var next_step_div = `.step-${next_step_id}`;
                    console.log(" Activating next_step_div:", next_step_div);
                    console.log("   Element exists:", $(next_step_div).length > 0);

                    if ($(next_step_div).hasClass('hide')) {
                        $(next_step_div).addClass('active');
                        $(next_step_div).removeClass('hide');
                    } else {
                        $(next_step_div).addClass('active');
                    }

                    $(target).each(function () {
                        let hasUnderline = $(this).css("text-decoration").includes("underline");

                        $(this).css({
                            "color": "white",
                            "background-color": "#002655",
                            "padding": "0px",
                            "border-radius": "0px"
                        });

                        if (hasUnderline) {
                            $(this).css("text-decoration", "underline");
                        }
                    });

                    progressBarCount(my_ref, next_step_id);
                    updateUrl(next_step_id);
                }

                setLocalstorage(my_ref, next_step_id, questionType);
                $(e).prop("disabled", false);
            }, 500);
        }

        function go_pre_step(e) {
            var current_step_id = $(e).attr('my_ref');
            var current_step = `.step-${current_step_id}`;

            if ($(current_step).hasClass('active')) {
                $(current_step).removeClass('active');
            }
            var prev_step_id = $(e).attr("que_id");
            // console.log(prev_step_id);
            var prev_step_div = `.step-${prev_step_id}`;
            var target = `.qidtarget-${current_step_id}`;

            $(target).css({
                "color": "#002655",
                "background-color": "#fff",
            });


            if ($(prev_step_div).hasClass('done') || $(prev_step_div).hasClass('hide')) {
                $(prev_step_div).removeClass('done hide').addClass('active');
            } else {
                $(prev_step_div).addClass('active');
            }

            var last_step_btn = $(current_step).find('.last_step_btn');
            if (last_step_btn.length) {
                $('.last_step_btn').hide();
                $('.nxt_btn_' + current_step_id).show();
            }

            let key = 'Localstorage';
            reverseProgressCount(current_step_id, prev_step_id);
            popLocalstorageValue(current_step_id, key);

            updateUrl(prev_step_id);
        }

        function updateNextButton(selectElement, id) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const myNextBtn = selectedOption.getAttribute("my_ref_nxt");
            const queId = selectedOption.getAttribute("que_id");
            const selectedValue = selectedOption.value;
            $(`.step-${id}`).attr('attempted', selectedValue);
            const targetEle = $(selectElement).attr('target-id');
            $(targetEle).text(selectedValue);
            $(myNextBtn).attr("que_id", queId);
        }

        function updateNextButtonR(radioElement) {
            const myNextBtn = radioElement.getAttribute("my_ref_nxt");
            const queId = radioElement.getAttribute("que_id");
            const selectedValue = radioElement.value;
            const targetEle = $(radioElement).attr('target-id');
            $(targetEle).text(selectedValue)
            $(myNextBtn).attr("que_id", queId);
        }

        function updateDropdownLInk(selectLink, id) {
            const selectedOption = selectLink.options[selectLink.selectedIndex];
            const myNextBtn = selectedOption.getAttribute("my_ref_nxt");
            const queId = selectedOption.getAttribute("que_id");
            const selectedValue = selectedOption.value;
            $(`.step-${id}`).attr('attempted', selectedValue);
            const targetEle = $(selectLink).attr('target-id');
            $(targetEle).text(selectedValue)
            $(myNextBtn).attr("que_id", queId);
        }

        function saveContractContent() {
            let finalHtml = $('.right-box').children().map(function () {
                let $element = $(this);

                if ($element.hasClass('right-sec-div') && $element.hasClass('active_sec') && $element.hasClass(
                    'secure_content')) {
                    return null;
                }

                if ($element.hasClass('right-sec-div') && $element.hasClass('active_sec')) {
                    return $element.prop('outerHTML');
                }

                if ($element.is('span') && $element.find('.answered_spns').text().trim() !== "_______") {
                    return $element.prop('outerHTML');
                }
            }).get().join("");

            if (finalHtml !== null && finalHtml !== undefined && finalHtml !== '') {
                let user_id = "{{ Auth()->user()->id ?? '' }}";
                let document_id = $('#document_id').val();

                var data = {
                    html: finalHtml,
                    user_id: user_id,
                    document_id: document_id,
                    _token: "{{ csrf_token() }}"
                }

                $.ajax({
                    url: "{{ url('/save/contract/content') }}",
                    type: "post",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if (response.code == "200") {
                            console.log(response);
                        }
                    }
                })
            }
        }

        function updateContractContent() {
            let finalHtml = $('.right-box').children().map(function () {
                let $element = $(this);

                if ($element.hasClass('right-sec-div') && $element.hasClass('active_sec') && $element.hasClass('secure_content')) {
                    return null;
                }

                if ($element.hasClass('right-sec-div') && $element.hasClass('active_sec')) {
                    return $element.prop('outerHTML');
                }

                if ($element.is('span') && $element.find('.answered_spns').text().trim() !== "_______") {
                    return $element.prop('outerHTML');
                }
            }).get().join("");

            if (finalHtml && finalHtml.trim() !== '') {
                let user_id = "{{ Auth()->user()->id ?? '' }}";
                let document_id = $('#document_id').val();
                let type = $('#type').val();
                let order_id = $('#order_id').val();
                let has_subscription = $('#has_subscription').val();
                let has_credits = $('#has_credits').val();
                let edit_count = parseInt($('#edit_count').val() || 0);

                console.log('edit_count:', edit_count);

                let data = {
                    html: finalHtml,
                    user_id: user_id,
                    document_id: document_id,
                    type: type,
                    order_id: order_id,
                    has_subscription: has_subscription,
                    has_credits: has_credits,
                    edit_count: edit_count,
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    url: "{{ url('/update/contract/content') }}",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if (response.code === 200 && response.status === "save") {
                            console.log("Contract saved successfully.");
                            toastr.success("Contract saved successfully!");
                        }
                        else if (response.code === 200 && response.status === "update") {
                            console.log("Contract updated successfully.");
                            toastr.success("Contract updated successfully!");
                        }
                        else if (response.code === 302 && response.status === "redirect_checkout") {
                            // Redirect if user already edited once
                            toastr.info(response.message || "Redirecting to checkout...");
                            setTimeout(() => {
                                window.location.href = response.redirect_url;
                            }, 500);
                        }
                        else if (response.code === 400) {
                            toastr.error(response.message || "Something went wrong!");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", error);
                        toastr.error("An unexpected error occurred!");
                    }
                });
            }
        }

        function storeAnswers(e, question_id, qtype, next_id) {
            let attempted_value = $(e).val();
            let localStorageData = JSON.parse(localStorage.getItem('Localstorage')) || {};
            let document_id = $('#document_id').val();
            let attemptedQuestions = localStorageData[document_id]?.attempted_question || [];
            let questionIndex = attemptedQuestions.findIndex(item => item.question_id === question_id);

            if (questionIndex !== -1) {
                attemptedQuestions[questionIndex].attempted_answer = attempted_value;
                attemptedQuestions[questionIndex].attempted_value = attempted_value;
                localStorageData[document_id].attempted_question = attemptedQuestions;
                localStorage.setItem('Localstorage', JSON.stringify(localStorageData));
            }

            let right_part_target = `.qidtarget-${question_id}`;

            if (qtype === "textbox" || qtype === "text" || qtype === "textarea" ||
                qtype === "pricebox" || qtype === "percentage-box" || qtype === "number-field" || qtype === "number") {

                $(`.step-${question_id}`).attr('attempted', attempted_value);

                if (attempted_value.length === 0) {
                    $(right_part_target).text("__________");
                } else {
                    $(right_part_target).text(attempted_value).css({
                        "color": "white",
                        "background-color": "#002655",
                        "text-decoration": "none",
                    });
                }

                //  Re-scan the right panel for any un-converted QID placeholders after user types
                _rescanUnconvertedPlaceholders();

                setTimeout(function () {
                    rightSecConditions();
                    updateStateClauseDescriptions();
                }, 50);

                smoothScrollToTarget(right_part_target, '.right-question-box');
                alphabetList();
                questionCondition();

            } else if (qtype === "radio-button" || qtype === "radio") {
                $(`.step-${question_id}`).attr('attempted', attempted_value);
                $(right_part_target).text(attempted_value).css({
                    "color": "white",
                    "background-color": "#002655",
                });

            } else if (qtype === "dropdown" || qtype === "select") {
                let selectoption = $(`#${question_id} option:selected`).text();
                $(`.step-${question_id}`).attr('attempted', selectoption);
                $(right_part_target).text(selectoption).css({
                    "color": "white",
                    "background-color": "#002655",
                });


            var questionLabel = $(`.step-${question_id} .que_heading`).text().toLowerCase();
                if (questionLabel.includes('governing') || questionLabel.includes('state')) {
                    $('#selected-state-label').text(selectoption);
                }

                $(right_part_target).attr('data-value', attempted_value);

                if (questionIndex !== -1) {
                    attemptedQuestions[questionIndex].attempted_answer = selectoption;
                    attemptedQuestions[questionIndex].attempted_value = attempted_value;
                    localStorage.setItem('Localstorage', JSON.stringify(localStorageData));
                }

                setTimeout(function () {
                    rightSecConditions();
                }, 50);

            } else if (qtype === "dropdown-link") {
                let selectoption = $(`#${question_id} option:selected`).text();
                $(`.step-${question_id}`).attr('attempted', selectoption);
                $(right_part_target).text(selectoption).css({
                    "color": "white",
                    "background-color": "#002655",
                });

                if (questionIndex !== -1) {
                    attemptedQuestions[questionIndex].attempted_answer = selectoption;
                    attemptedQuestions[questionIndex].attempted_value = attempted_value;
                    localStorage.setItem('Localstorage', JSON.stringify(localStorageData));
                }

            } else if (qtype === "date-field" || qtype === "date") {
                let [day, month, year] = attempted_value.split("/");
                let date = new Date(`${year}-${month}-${day}`);
                let options = { day: "2-digit", month: "long", year: "numeric" };
                let formattedDate = new Intl.DateTimeFormat("en-US", options).format(date);

                $(`.step-${question_id}`).attr('attempted', formattedDate);
                $(right_part_target).text(formattedDate).css({
                    "color": "white",
                    "background-color": "#002655",
                });

                if (questionIndex !== -1) {
                    attemptedQuestions[questionIndex].attempted_answer = formattedDate;
                    attemptedQuestions[questionIndex].attempted_value = formattedDate;
                    localStorage.setItem('Localstorage', JSON.stringify(localStorageData));
                }
            }

            smoothScrollToTarget(right_part_target, '.right-question-box');
            rightSecConditions();
            alphabetList();
            questionCondition();
        }

        function _rescanUnconvertedPlaceholders() {
            $('.r_div, .right-sec-div').each(function () {
                let html = $(this).html();

                if (!/{[W_]*QID\d+}/i.test(html)) return;

                const updated = html.replace(
                    /\{(?:[W_]*QID)?(\d+)\}/gi,
                    function (match, qid) {
                        if (html.indexOf('qidtarget-' + qid + '"') !== -1) return match;
                        const currentVal = $('#' + qid).val() || '__________';
                        return `<span class="answered_spns qidtarget-${qid}">${currentVal}</span>`;
                    }
                );

                if (updated !== html) {
                    $(this).html(updated);
                }
            });
        }

        function storeAnswersOLD(e, question_id = undefined, qtype = undefined, next_id = undefined) {
            let attempted_value = $(e).val();
            let localStorageData = JSON.parse(localStorage.getItem('Localstorage')) || {};
            let document_id = $('#document_id').val();
            let attemptedQuestions = localStorageData[document_id]?.attempted_question || [];
            let questionIndex = attemptedQuestions.findIndex(item => item.question_id === question_id);

            if (questionIndex !== -1) {
                attemptedQuestions[questionIndex].attempted_answer = attempted_value;
                attemptedQuestions[questionIndex].attempted_value = attempted_value;
                localStorageData[document_id].attempted_question = attemptedQuestions;
                localStorage.setItem('Localstorage', JSON.stringify(localStorageData));
            }

            let right_part_target = `.qidtarget-${question_id}`;

            if (qtype === "textbox" || qtype === "text" || qtype === "textarea" || qtype === "pricebox" || qtype === "percentage-box" || qtype === "number-field" || qtype === "number") {

                $(`.step-${question_id}`).attr('attempted', attempted_value);

                if (attempted_value.length == 0) {
                    $(right_part_target).text("__________").each(function () {
                        $(this).css({
                            "color": "white",
                            "background-color": "#002655",
                            "padding": "0px",
                            "border-radius": "0px",
                        });
                    });
                } else {
                    $(right_part_target).text(attempted_value).css({
                        "color": "white",
                        "background-color": "#002655",
                        "text-decoration": "none",
                    });
                }

                //  Add this at the end before the last calls
                setTimeout(function () {
                    rightSecConditions();
                    updateStateClauseDescriptions();
                }, 50);

                smoothScrollToTarget(right_part_target, '.right-question-box');
                alphabetList();
                questionCondition();


            } else if (qtype === "radio-button" || qtype === "radio") {
                $(`.step-${question_id}`).attr('attempted', attempted_value);
                $(right_part_target).text(attempted_value).css({
                    "color": "white",
                    "background-color": "#002655",
                });


            } else if (qtype === "dropdown" || qtype === "select") {
                // let selectoption = $(`.step-${question_id}`).find('select option:selected').text();
                let selectoption = $(`#${question_id} option:selected`).text();
                $(`.step-${question_id}`).attr('attempted', selectoption);
                $(right_part_target).text(selectoption).css({
                    "color": "white",
                    "background-color": "#002655",
                });

                $(right_part_target).attr('data-value', attempted_value);


                if (questionIndex !== -1) {
                    attemptedQuestions[questionIndex].attempted_answer = selectoption;
                    attemptedQuestions[questionIndex].attempted_value = attempted_value;
                    localStorage.setItem('Localstorage', JSON.stringify(localStorageData));
                }

                setTimeout(function () {
                    rightSecConditions();
                }, 50);

            } else if (qtype === "dropdown-link") {
                // let selectoption = $(`.step-${question_id}`).find('select option:selected').text();
                let selectoption = $(`#${question_id} option:selected`).text();

                $(`.step-${question_id}`).attr('attempted', selectoption);
                $(right_part_target).text(selectoption).css({
                    "color": "white",
                    "background-color": "#002655",
                });

                if (questionIndex !== -1) {
                    attemptedQuestions[questionIndex].attempted_answer = selectoption;
                    attemptedQuestions[questionIndex].attempted_value = attempted_value;
                    localStorage.setItem('Localstorage', JSON.stringify(localStorageData));
                }

            } else if (qtype === "date-field" || qtype === "date") {
                let [day, month, year] = attempted_value.split("/");
                let date = new Date(`${year}-${month}-${day}`);
                let options = {
                    day: "2-digit",
                    month: "long",
                    year: "numeric"
                };
                let formattedDate = new Intl.DateTimeFormat("en-US", options).format(date);

                $(`.step-${question_id}`).attr('attempted', formattedDate);
                $(right_part_target).text(formattedDate).css({
                    "color": "white",
                    "background-color": "#002655",
                });

                if (questionIndex !== -1) {
                    attemptedQuestions[questionIndex].attempted_answer = formattedDate;
                    attemptedQuestions[questionIndex].attempted_value = formattedDate;
                    localStorage.setItem('Localstorage', JSON.stringify(localStorageData));
                }
            }

            smoothScrollToTarget(right_part_target, '.right-question-box');
            rightSecConditions();
            alphabetList();
            questionCondition();
        }

        function updateStateClauseDescriptions() {
            // Find all state clause sections
            $('.right-sec-div[data-conditions]').each(function () {
                var $section = $(this);
                var conditions = $section.data('conditions');

                if (!conditions) return;

                // Check if this is a state clause section
                var isStateClause = false;
                if (Array.isArray(conditions)) {
                    conditions.forEach(function (condition) {
                        if (condition.section_name && condition.section_name.includes('Requirements')) {
                            isStateClause = true;
                        }
                    });
                }

                if (isStateClause) {
                    // Update QID placeholders within state clause descriptions
                    var content = $section.html();
                    var regex = /\{QID(\d+)\}/g;

                    content = content.replace(regex, function (match, qid) {
                        var value = $('#' + qid).val() || '__________';
                        return '<span class="qidtarget-' + qid + ' answered_spns">' + value + '</span>';
                    });

                    $section.html(content);
                }
            });
        }

        function setLocalstorage(que_id, next_id, qtype) {
            var document_id = $('#document_id').val();
            var user_id = $('#user_id').val();
            var attemptedAnswer = $('.step-' + que_id).attr('attempted');
            var attemptedAnswerValue = '';

            if (qtype === 'textbox' || qtype === 'text') {
                attemptedAnswerValue = $('#' + que_id).val();
            } else if (qtype === 'textarea') {
                attemptedAnswerValue = $('#' + que_id).val();
            } else if (qtype === 'dropdown' || qtype === 'select') {
                attemptedAnswerValue = $('#' + que_id).val();

            } else if (qtype === 'radio-button' || qtype === 'radio') {
                attemptedAnswerValue = $('input[name="question_' + que_id + '"]:checked').val();
            } else if (qtype === 'date-field' || qtype === 'date') {
                attemptedAnswerValue = $('#' + que_id).val();
            } else if (qtype === 'pricebox') {
                attemptedAnswerValue = $('#' + que_id).val();
            } else if (qtype === 'number-field' || qtype === 'number') {
                attemptedAnswerValue = $('#' + que_id).val();
            } else if (qtype === 'percentage-box') {
                attemptedAnswerValue = $('#' + que_id).val();
            } else if (qtype === 'dropdown-link') {
                attemptedAnswerValue = $('#' + que_id).val();
            } else {
                attemptedAnswerValue = $('#' + que_id).val();
            }
            var prevId = $('.pre_btn_' + que_id).attr('que_id');
            var pre_btn_id = $('.pre_btn_' + next_id).attr('que_id');
            var next_btn_id = $('.nxt_btn_' + next_id).attr('que_id');
            var nextQuestionType = $('.step-' + next_id).attr('data-type');
            var nextAttemptedAnswer = '';
            var nextAttemptedAnswerValue = '';

            var now = new Date();
            var formattedTime = now.getTime();
            var expiryTime = now.getTime() + 2 * 60 * 1000;
            var progressValue = $('#percent_count').val();
            var totalSteps = $('#total_step').val();
            var attemptedSteps = $('#all_attempted').val();
            var is_last = $('.step-' + que_id).attr('is_last');
            var current_label = $(".lbl-" + que_id).text();
            var next_label = $(".lbl-" + next_id).text();

            var firstObj = {
                question_id: que_id,
                type: qtype,
                attempted_answer: attemptedAnswer,
                attempted_value: attemptedAnswerValue,
                previous_id: prevId,
                next_id: next_id,
                progress: 0,
                total_steps: totalSteps,
                attempted_step: 0,
                label: current_label,
            };

            var newObj = {
                question_id: next_id,
                type: nextQuestionType,
                attempted_answer: nextAttemptedAnswer,
                attempted_value: nextAttemptedAnswerValue,
                previous_id: pre_btn_id,
                next_id: next_btn_id,
                progress: progressValue,
                total_steps: totalSteps,
                attempted_step: attemptedSteps,
                label: next_label,
            };

            let localStorageData = JSON.parse(localStorage.getItem('Localstorage')) || {};
            if (!localStorageData[document_id]) {
                localStorageData[document_id] = {
                    user_id: user_id,
                    attempted_question: []
                };
            }

            if (!localStorageData[document_id].user_id) {
                localStorageData[document_id].user_id = user_id;
            }

            let attemptedQuestions = localStorageData[document_id].attempted_question;

            if (!Array.isArray(attemptedQuestions)) {
                attemptedQuestions = [];
            }

            // Save firstObj if not already saved
            let firstObjIndex = attemptedQuestions.findIndex(obj => obj.question_id === firstObj.question_id);
            if (firstObjIndex === -1) {
                attemptedQuestions.push(firstObj);
                console.log("Stored first step:", firstObj);
            }

            if (is_last == "true") {
                var type = $('#type').val();
                // console.log(type, 'type');
                if (type == 'edit' || type == 'full') {
                    updateContractContent();
                } else {
                    saveContractContent();
                }

            } else {
                let newObjIndex = attemptedQuestions.findIndex(obj => obj.question_id === newObj.question_id);

                if (newObjIndex !== -1) {
                    attemptedQuestions[newObjIndex].attempted_answer = newObj.attempted_answer || null;
                } else {
                    attemptedQuestions.push(newObj);
                }

                console.log("Stored step:", newObj);
            }

            localStorageData[document_id].attempted_question = attemptedQuestions;
            localStorage.setItem('Localstorage', JSON.stringify(localStorageData));
        }

        function popLocalstorageValue(que_id, key) {
            let document_id = $('#document_id').val();
            let localStorageData = JSON.parse(localStorage.getItem(key));
            if (!localStorageData || !localStorageData[document_id]?.attempted_question) {
                return null;
            }

            let attemptedQuestions = localStorageData[document_id]?.attempted_question || [];
            let questionIndex = attemptedQuestions.findIndex(item => item.question_id === que_id);

            if (questionIndex !== -1) {
                let poppedQuestion = attemptedQuestions.splice(questionIndex, 1)[0];
                localStorageData.attempted_question = attemptedQuestions;
                localStorage.setItem(key, JSON.stringify(localStorageData));
                return poppedQuestion;
            } else {
                return null;
            }
        }

        function getLocalstorage(key) {
            let document_id = $('#document_id').val();
            let localStorageData = JSON.parse(localStorage.getItem(key));
            if (!localStorageData || !localStorageData[document_id]?.attempted_question) {
                return null;
            }

            let attemptedQuestions = localStorageData[document_id]?.attempted_question || [];

            return attemptedQuestions.length > 0 ? attemptedQuestions : null;
        }

        function showLastAttemptedValues() {
            let attemptedQuestions = getLocalstorage('Localstorage');
            // console.log(attemptedQuestions);

            // return;

            if (!attemptedQuestions) {
                return;
            }

            let lastAttempted = attemptedQuestions[attemptedQuestions.length - 1];

            if (lastAttempted) {

                let step_id = lastAttempted.question_id;
                // console.log("step_id",step_id);

                let next_id = lastAttempted.next_id;
                let prev_id = lastAttempted.previous_id;
                let value = lastAttempted.attempted_answer;
                let current_document_id = $("#document_id").val();
                let pre_btn = `.pre_btn_${step_id}`;

                $(pre_btn).attr("que_id", prev_id);
                if (next_id == 'last_step') {
                    $('.nxt_btn_' + step_id).hide();
                    $('.last_step_btn').show();
                }

                let current_step = $(".step-" + step_id);
                let first_step = $(".step1");
                $(".question-div").addClass('hide').removeClass('active done');

                if (step_id === "1") {
                    first_step.addClass('active').removeClass('hide');
                } else {
                    first_step.removeClass('active').addClass('hide');
                    current_step.addClass('active').removeClass('hide done');
                }

                // console.log('Current Step for last saved is', current_step);
                $('#' + step_id).val();
                $(current_step).attr('attempted', value);

                lastProgress = lastAttempted.progress || 0;
                total_steps = lastAttempted.total_steps || 1;
                total_attempted = lastAttempted.attempted_step || 0;

                $('#total_step').val(total_steps);
                $('#all_attempted').val(total_attempted);
                $('#percent_count').val(lastProgress);
                $('.progressCount').text(lastProgress + "%");
                $('.progress-bar').css("width", lastProgress + "%");

                updateUrl(step_id);

                // Hide steps marked as hidden in progressBarCount logic
                attemptedQuestions.forEach(data => {
                    let ques_id = data.question_id;
                    let quesDiv = $('.step-' + ques_id);

                    if (quesDiv.length) {
                        if (ques_id == step_id) {
                            quesDiv.removeClass('hide').addClass('active');
                        } else {
                            quesDiv.addClass('hide').removeClass('active done');
                        }
                    }
                });
            }

            attemptedQuestions.forEach(data => {
                let ques_id = data.question_id;
                let prev_id = data.previous_id;

                let next_id = data.next_id;
                let type = data.type;
                let value = data.attempted_answer;
                let label_value = data.label;

                let prev_btn = $('.pre_btn_' + ques_id);
                let next_btn = $('.nxt_btn_' + ques_id);

                $(prev_btn).attr('que_id', prev_id);
                $(next_btn).attr('que_id', next_id);

                let quesDiv = $('.step-' + ques_id);

                if (!quesDiv.hasClass('active')) {
                    quesDiv.addClass('done');
                }

                if (quesDiv.length) {
                    quesDiv.attr('attempted', value);

                    if (type === 'textbox' || type === 'text' || type === 'textarea' || type === 'pricebox' || type ===
                        'number-field' || type === 'percentage-box') {
                        if (value) {
                            $('#' + ques_id).val(value);
                            $('.qidtarget-' + ques_id).text(value);
                        }
                        $('.lbl-' + ques_id).text(label_value);

                    } else if (type === 'dropdown') {
                        if (value) {
                            $('#' + ques_id).val(data.attempted_value);
                            $('.qidtarget-' + ques_id).text(value);
                        }
                        $('.lbl-' + ques_id).text(label_value);

                    } else if (type === 'dropdown-link') {
                        if (value) {
                            $('#' + ques_id).val(value);
                            $('.qidtarget-' + ques_id).text(value);
                        }
                        $('.lbl-' + ques_id).text(label_value);
                    }
                    else if (type === 'radio-button' || type === 'radio') {
                        if (value) {
                            $('input[name="question_' + ques_id + '"]').each(function () {
                                if ($(this).val() == value) {
                                    $(this).prop('checked', true);
                                }
                            });
                        }

                        $('.lbl-' + ques_id).text(label_value);
                    } else if (type === 'date-field' || type === 'date') {
                        if (value) {
                            const originalDate = value;
                            // console.log(originalDate);
                            if (originalDate) {
                                // const date = new Date(originalDate);
                                // date.setDate(date.getDate() - 8);
                                // const formattedDate = date.toISOString().split('T')[0];
                                $('#' + ques_id).val(originalDate);
                                $('.qidtarget-' + ques_id).text(value);
                            }
                        }
                        $('.lbl-' + ques_id).text(label_value);
                    }
                }
            });
            replaceQIDPlaceholders();
            updateStateClauseDescriptions();
            rightSecConditions();
        }

        function smoothScrollToTarget(targetElement, container, offset = 0) {
            var $container = $(container);
            var $target = $(targetElement);

            if (!$container.length || !$target.length) {
                return;
            }

            var containerHeight = $container.height();
            var containerScrollHeight = $container[0].scrollHeight;
            var containerScrollTop = $container.scrollTop();
            var targetOffsetTop = $target.offset().top - $container.offset().top + containerScrollTop;
            var targetScrollPosition = targetOffsetTop - offset;

            if (targetScrollPosition < 0) targetScrollPosition = 0;
            if (targetScrollPosition > containerScrollHeight - containerHeight) {
                targetScrollPosition = containerScrollHeight - containerHeight;
            }

            var startScroll = containerScrollTop;
            var distance = targetScrollPosition - startScroll;
            var duration = 800;
            let startTime = null;

            isScrolling = true;

            function smoothStep(timestamp) {
                if (!startTime) startTime = timestamp;
                const progress = timestamp - startTime;
                const currentScrollPosition = easeInOutQuad(progress, startScroll, distance, duration);

                $container.scrollTop(currentScrollPosition);

                if (progress < duration) {
                    window.requestAnimationFrame(smoothStep);
                } else {
                    isScrolling = false; 
                }
            }

            window.requestAnimationFrame(smoothStep);

            function easeInOutQuad(time, start, distance, duration) {
                time /= duration / 2;
                if (time < 1) return (distance / 2) * time * time + start;
                time--;
                return (-distance / 2) * (time * (time - 2) - 1) + start;
            }
        }



        // update the current url for every step //
        function updateUrl(step) {
            const url = new URL(window.location.href);
            // url.searchParams.set('step', step);
            url.searchParams.set('s', step);

            if (typeof (history.pushState) !== "undefined") {
                const obj = {
                    Title: 'title',
                    Url: url.toString()
                };
                history.pushState(obj, obj.Title, obj.Url);
            } else {
                alert("Browser does not support HTML5.");
            }
        }

        // function go_to_checkout_page() {
        //     let type = $('#type').val();
        //     let edit_count = parseInt($('#edit_count').val() || 0);
        //     let document_id = $('#document_id').val();

        //     @if(auth()->check())
        //         if (type === 'edit' && edit_count === 0 || type === 'full') {
        //             // First-time edit allowed — refresh to continue
        //             window.location.reload();
        //         } else {
        //             // Redirect to checkout after first edit
        //             location.href = "{{ url('/checkout') }}";
        //         }
        //     @else
        //         let redirectUrl = encodeURIComponent("{{ url('/checkout') }}");
        //         window.location.href = "{{ route('register') }}?redirecturl=" + redirectUrl;
        //     @endif
        //                         }

        function go_to_checkout_page() {
            let type = $('#type').val();
            let edit_count = parseInt($('#edit_count').val() || 0);
            let document_id = $('#document_id').val();
            let user_id = $('#user_id').val();

            let finalHtml = $('.contract-priveiw-side').html();

            console.log('go_to_checkout_page called');
            console.log('finalHtml length:', finalHtml ? finalHtml.trim().length : 0);
            console.log('type:', type, '| edit_count:', edit_count);

            @if(auth()->check())
                if (type === 'edit' && edit_count === 0 || type === 'full') {
                    if (!finalHtml || finalHtml.trim() === '') {
                        window.location.reload();
                        return;
                    }
                    let order_id = $('#order_id').val();
                    let has_subscription = $('#has_subscription').val();
                    let has_credits = $('#has_credits').val();
                    $.ajax({
                        url: "{{ url('/update/contract/content') }}",
                        type: "POST",
                        data: {
                            html: finalHtml,
                            user_id: user_id,
                            document_id: document_id,
                            type: type,
                            order_id: order_id,
                            has_subscription: has_subscription,
                            has_credits: has_credits,
                            edit_count: edit_count,
                            _token: "{{ csrf_token() }}"
                        },
                        dataType: "json",
                        success: function (response) {
                            console.log('update response:', response);
                            window.location.reload();
                        },
                        error: function (xhr) {
                            console.error('update error:', xhr.responseText);
                            window.location.reload();
                        }
                    });
                } else {
                    if (!finalHtml || finalHtml.trim() === '') {
                        location.href = "{{ url('/pricing') }}?document_id=" + document_id;
                        return;
                    }
                    $.ajax({
                        url: "{{ url('/save/contract/content') }}",
                        type: "POST",
                        data: {
                            html: finalHtml,
                            user_id: user_id,
                            document_id: document_id,
                            _token: "{{ csrf_token() }}"
                        },
                        dataType: "json",
                        success: function (response) {
                            console.log('save response:', response);
                            location.href = "{{ url('/pricing') }}?document_id=" + document_id;
                        },
                        error: function (xhr) {
                            console.error('save error:', xhr.responseText);
                            location.href = "{{ url('/pricing') }}?document_id=" + document_id;
                        }
                    });
                }
                @else
                    if (!finalHtml || finalHtml.trim() === '') {
                        let redirectUrl = encodeURIComponent("{{ url('/pricing') }}?document_id=" + document_id);
                        window.location.href = "{{ route('register') }}?redirecturl=" + redirectUrl;
                        return;
                    }
                    $.ajax({
                        url: "{{ url('/save/contract/content') }}",
                        type: "POST",
                        data: {
                            html: finalHtml,
                            user_id: user_id,
                            document_id: document_id,
                            _token: "{{ csrf_token() }}"
                        },
                        dataType: "json",
                        complete: function (response) {
                            let redirectUrl = encodeURIComponent("{{ url('/pricing') }}?document_id=" + document_id);
                            window.location.href = "{{ route('register') }}?redirecturl=" + redirectUrl;
                        }
                    });
                @endif
        }

        // function go_to_checkout_page() {
        //     saveContractContent()
        //     let type = $('#type').val();
        //     let edit_count = parseInt($('#edit_count').val() || 0);
        //     let document_id = $('#document_id').val();

        //     @if(auth()->check())
        //         if (type === 'edit' && edit_count === 0 || type === 'full') {
        //             window.location.reload();
        //         } else {
        //             location.href = "{{ url('/pricing') }}?document_id=" + document_id;
        //         }
        //     @else
                
        //         let redirectUrl = encodeURIComponent("{{ url('/pricing') }}?document_id=" + document_id);
        //         window.location.href = "{{ route('register') }}?redirecturl=" + redirectUrl;
        //     @endif
        // }

        

        $('.answered_spns').click(function (e) {
            var rightBox = $('.right-question-box');
            var boxTop = rightBox.offset().top;
            var scrollTop = rightBox.scrollTop();
            var relativeClick = (e.pageY - boxTop) + scrollTop - 40;

            $('.target-box').css({
                'top': relativeClick + 'px',
                'position': 'absolute'
            }).removeClass('hide');

            $('.prev-next-icons').removeClass('hide');
            $('.question-div').addClass('horizontal-shake');

            setTimeout(function () {
                $('.target-box').addClass('hide');
                $('.prev-next-icons').addClass('hide');
                $('.question-div').removeClass('horizontal-shake');
            }, 2000);
        });

        function getQuestionInfo(id, info) {
            event.stopPropagation();
            let infoDiv = $('.info-div');
            let infoTxt = $('.infoqu-txt');

            // console.log(id, info);

            if (infoDiv.hasClass('hide')) {
                infoTxt.text(info);
                infoDiv.removeClass('hide');
            } else {
                infoDiv.addClass('hide');
            }
        }

        $(document).on('click', '.infoimg', function (event) {
            event.stopPropagation();

            let que_id = $(this).closest('.save_document_button').attr('id');
            let info_text = $(this).closest('.save_document_button').attr('onclick').match(/`(.*?)`/g)[1]?.replace(
                /`/g, '') || '';

            getQuestionInfo(que_id, info_text);
        });

        $(document).on('click', function (event) {
            if (!$(event.target).closest('.info-div').length && !$(event.target).is('.infoimg')) {
                $('.info-div').addClass('hide');
            }
        });

        $(document).on('click', '.info-div', function (event) {
            event.stopPropagation();
        });

        $(document).on('click', '.info-close', function () {
            $('.info-div').addClass('hide');
        });
    </script>

    <script>
        $('.chkbox').change(function () {
            $(".chkbox").prop('checked', false);
            $(this).prop('checked', true);
            val = $(this).val();
            $('.star').removeClass('rating-color');

            for (x = val; x > 0; x--) {
                $(`i[rate="${x}"]`).addClass('rating-color');
            }
        });

        $(document).ready(function () {
            if (typeof $.ui !== "undefined" && typeof $.ui.datepicker !== "undefined") {

                $.datepicker.regional["en-US"] = {
                    closeText: "Close",
                    prevText: "Prev",
                    nextText: "Next",
                    currentText: "Today",
                    monthNames: [
                        "January", "February", "March", "April", "May", "June",
                        "July", "August", "September", "October", "November", "December"
                    ],
                    monthNamesShort: [
                        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                    ],
                    dayNames: [
                        "Sunday", "Monday", "Tuesday", "Wednesday",
                        "Thursday", "Friday", "Saturday"
                    ],
                    dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                    dayNamesMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
                    weekHeader: "Wk",
                    dateFormat: "dd/mm/yy",
                    firstDay: 0,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: ""
                };

                $.datepicker.setDefaults($.datepicker.regional["en-US"]);

                $(".datepicker").datepicker();

            } else {
                console.error("jQuery UI is NOT loaded");
                return;
            }

            $(".contract_date").datepicker({
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true,
                yearRange: "1925:2125"
            });

            $(".contract_date").on("focus", function () {
                $(this).datepicker("show");
            });

            $(".custom-icon").on("click", function () {
                $(this).prev(".contract_date").datepicker("show");
            });
        });


        $(document).ready(function () {
            $('.secure_content .blur-text-spn').on('cut copy paste', function (e) {
                e.preventDefault();
            });

            $('.secure_content').mousedown(function (e) {
                if (e.button == 2) {
                    e.preventDefault();

                }
            });

        });
    </script>

    <script>
        function isClose() {
            $('#all_rating').show();
            $('#all_rev').show();
            $('.modal-rgt').show();
            $('.form-scroll-wrap').css({
                display: "none"
            });
            $('.name_fields').hide();
            $('.form-scroll-wrap').removeClass('form-scroll-wrap-show');
        }

        $(document).ready(function () {
            if ($('#exampleModalCenter').css('display') == 'none') {
                isClose();
            }

            $('.ad_rvw').click(function () {
                let is_login = "{{ auth()->check() ?? '' }}";

                $('.modal-rgt').hide();
                $('#all_rev').hide();

                if (!is_login) {
                    $('#all_rating').show();
                } else {
                    $('#all_rating').hide();
                }

                $('.form-scroll-wrap').css({
                    display: "block"
                }).addClass('form-scroll-wrap-show');
            });

            $('#cancel_btn').click(function (e) {
                e.preventDefault();
                isClose();
            });

            $('.append_name_fields').click(function () {
                $('.name_fields').toggle();
            });

            $('#exampleModalCenter').on('hidden.bs.modal', function () {
                isClose();
            });
        });


    </script>

    <script>

    //  State label updater 
        $(document).ready(function () {
            $(document).on('input change', '.question-div input, .question-div textarea, .question-div select', function () {
    $(this).removeClass('input-error');
    $(this).siblings('.error-msg').remove();
});

            function updateStateLabelFromDropdown($select) {
                var text = $select.find('option:selected').text().trim();
                if (text) {
                    $('#selected-state-label').text(text);
                }
            }

        $('select').each(function () {
            var questionLabel = $(this).closest('.question-div').find('.que_heading').text().toLowerCase();
            if (questionLabel.includes('governing') || questionLabel.includes('state')) {
                var defaultText = $(this).find('option:selected').text().trim();
                if (defaultText) $('#selected-state-label').text(defaultText);
            }
        });

            $(document).on('change', 'select', function () {
                var questionLabel = $(this).closest('.question-div').find('.que_heading').text().toLowerCase();
                if (questionLabel.includes('governing') || questionLabel.includes('state')) {
                    updateStateLabelFromDropdown($(this));
                }
            });
        });

                $(document).ready(function () {
                    $('.question-div').each(function (index) {
                        var $this = $(this);
                    });

                    $('.nxt').not('.last_step_btn').each(function (index) {
                        var $this = $(this);
                        console.log("Next Button " + (index + 1) + ":");
                        console.log("  que_id:", $this.attr('que_id'));
                        console.log("  data-next_step:", $this.attr('data-next_step'));
                        console.log("  my_ref:", $this.attr('my_ref'));
                    });
                });


                function questionCondition() {
                    $('.question-div').each(function () {
                        if ($(this).attr('is_condition') == '1') {
                            var conditiontype = $(this).attr('swtchtyp');
                            var que_id = $(this).attr('que_id');

                            if (conditiontype) {
                                console.log('Processing condition for question:', que_id);
                            }
                        }
                    });
                }
            </script>

            
            <script>
                document.addEventListener('keydown', function (e) {
                    if (e.ctrlKey && (e.key === 'a' || e.key === 'c' || e.key === 'u' || e.key === 's')) {
                        e.preventDefault();
                        return false;
                    }

                    if ((e.ctrlKey && e.shiftKey && e.key === 'I') ||
                        e.key === 'F12' ||
                        (e.ctrlKey && e.shiftKey && e.key === 'J')) {
                        e.preventDefault();
                        return false;
                    }

                    if (e.ctrlKey && e.key === 'p') {
                        e.preventDefault();
                        return false;
                    }
                }, false);

                $(document).ready(function () {
                    document.addEventListener('copy', function (e) {
                        var selection = window.getSelection();
                        var selectedText = selection.toString();

                        var isProtected = false;
                        var container = selection.anchorNode;

                        while (container) {
                            if ($(container).hasClass('right-question-box') ||
                                $(container).hasClass('right-box') ||
                                $(container).hasClass('r_div') ||
                                $(container).hasClass('right-sec-div')) {
                                isProtected = true;
                                break;
                            }
                            container = container.parentNode;
                        }

                        if (isProtected) {
                            e.preventDefault();
                            e.clipboardData.setData('text/plain', '');
                            return false;
                        }
                    });
                });
            </script>

    <script>
            (function lockContent() {

                var SELECTORS = '#protected-preview-panel, .right-box, .right-question-box, ' +
                                '.r_div, .right-sec-div, .answered_spns, .contract-priveiw-side';

                $(document).on('cut copy paste', SELECTORS, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                });

                document.addEventListener('copy', function (e) {
                    var node = window.getSelection() && window.getSelection().anchorNode;
                    while (node) {
                        if (node.nodeType === 1) {
                            var $node = $(node);
                            if ($node.closest('#protected-preview-panel, .right-box, .right-question-box').length) {
                                e.preventDefault();
                                if (e.clipboardData) e.clipboardData.setData('text/plain', '');
                                return false;
                            }
                        }
                        node = node.parentNode;
                    }
                }, true);

                $(document).on('contextmenu', SELECTORS, function (e) {
                    e.preventDefault();
                    return false;
                });

                $(document).on('selectstart dragstart', SELECTORS, function (e) {
                    e.preventDefault();
                    return false;
                });

                $(document).on('mousedown', SELECTORS, function (e) {
                    if (e.detail > 1) {
                        e.preventDefault();
                        return false;
                    }
                });

                $(document).on('mousedown', SELECTORS, function (e) {
                    if (e.button === 2) {
                        e.preventDefault();
                        return false;
                    }
                });

                document.addEventListener('keydown', function (e) {
                    var ctrl = e.ctrlKey || e.metaKey;

                    if (ctrl && ['a','c','u','s','p'].indexOf(e.key.toLowerCase()) !== -1) {
                        if (!$(e.target).closest('.left-question-box').length) {
                            e.preventDefault();
                            return false;
                        }
                    }

                    if (e.key === 'F12' ||
                        (ctrl && e.shiftKey && ['i','j','c'].indexOf(e.key.toLowerCase()) !== -1) ||
                        (ctrl && e.shiftKey && e.key === 'I')) {
                        e.preventDefault();
                        return false;
                    }
                }, true);

                document.addEventListener('keyup', function (e) {
                    if (e.key === 'PrintScreen') {
                        navigator.clipboard && navigator.clipboard.writeText('');
                    }
                });

                document.addEventListener('visibilitychange', function () {
                    if (!document.hidden) {
                        try {
                            navigator.clipboard && navigator.clipboard.writeText('');
                        } catch (_) {}
                    }
                });

            })();
</script>

@endsection