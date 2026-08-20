@extends('users_layout.master') @section('content')
<style>
    .answered_spns {
        background: #002655;
        color: white;
    }

    .right-question-box {
        background: white;
        overflow-y: scroll;
        border-radius: 10px;
        height: 70vh;
    }

    .answered_spns {
        border: 1px solid #002655;
        min-width: 60px;
        display: inline-block;
        min-height: 18px;
        text-align: center;
    }
    .blur_content {
        filter: blur(5px);
        pointer-events: none; 
    }

</style>
<section class="questions_page_main_div">
    <!-- This is the main container for the question and the form  -->
    <div id="main-question-form-controller" class="p-5 card"
        style="display: flex !important ; flex-direction: row !important ; gap: 10px;">
        <!-- here we show all the steps of the questions, this is the section where we show all the questions  -->
        <div class="left-box left-question-box questions-div">
            @foreach($questions as $index => $question)
                <div class="question-div step step-{{ $question->id }} mb-4 p-4" que_id="{{ $question->id }}"
                    id="{{ $question->qid }}" is_condition="{{ $question->is_condition }}"
                    swtchtyp="{{ $question->condition_type }}">
                    <p class="que_heading lbl-{{ $question->id }}"
                        json-data="{{  $question->conditions && count($question->conditions) > 0 ? json_encode($question->conditions) : NULL  }}">
                        {{ $question->questionData->question_label ?? '' }}</p>
                        @php 
                            $question_type = $question->type;
                            $next_qid = NULL;
                        @endphp 

                        @if($question_type == "textbox")
                            @php 
                                $next_qid = $question->questionData->next_question_id ?? '';
                            @endphp 
                            <input type="text" target-id="qidtarget-{{ $question->id }}"
                                onkeyup="storeAnswers(this, '{{ $question->id }}','textbox')" />
                        @elseif($question_type == "dropdown")
                            @php 
                                $next_qid = $question->options->first()->next_question_id ?? '';
                            @endphp 
                            <select onchange="updateNextButton(this); storeAnswers(this, '{{ $question->id }}','dropdown') " target-id=".qidtarget-{{ $question->id }}">
                                @foreach($question->options as $option)
                                    <option my_ref_nxt=".nxt_btn_{{ $question->id }}" que_id="{{ $option->next_question_id }}"
                                        value="{{ $option->option_value }}"> {{ $option->option_label }} </option>
                                @endforeach
                            </select>
                        @elseif($question_type == "radio")
                            @php 
                                $next_qid = $question->options->first()->next_question_id ?? '';
                            @endphp 
                            @foreach($question->options as $option)
                                <input type="radio" name="question_{{ $question->id }}" target-id=".qidtarget-{{ $question->id }}"
                                    onchange="updateNextButtonR(this); storeAnswers(this, '{{ $question->id }}','radio')" my_ref_nxt=".nxt_btn_{{ $question->id }}"
                                    que_id="{{ $option->next_question_id }}" value="{{ $option->option_value }}" />
                                <label>{{ $option->option_label }}</label>
                            @endforeach
                        @elseif($question_type == "datefield")
                            @php 
                                $next_qid = $question->questionData->next_question_id ?? '';
                            @endphp 
                            <!-- <input type="date" /> -->
                            <input type="date" target-id="qidtarget-{{ $question->id }}"
                                onchange="storeAnswers(this, '{{ $question->id }}','textbox')" />
                        @endif

                    <div class="navigation-btns mt-4">
                        @if($index != 0)
                            <button type="button" class="pre_btn_{{ $question->id }} nxt" que_id=""
                                onclick="go_pre_step(this)">Previous</button>
                        @endif
                        <button type="button" class="nxt_btn_{{ $question->id }} pre " que_id="{{ $next_qid ?? '' }}"
                            my_ref="{{ $question->id }}" onclick="go_next_step(this)">Next</button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- This is the box where we show the steps or the form -->
        <div class="right-box right-question-box form-div card ">
            @foreach($documentContents as $content)
                <div id="right_content_div_{{ $content->id }}" class="{{ $content->content_class }} right-sec-div mb-2 {{ $content->blur_content ? 'blur_content' : '' }}  {{ $content->is_condition ? 'd-none' : NULL }}" conditional_section="{{ $content->is_condition ? 'true' : NULL }}">
                    {!! $content->content !!}
                </div>
            @endforeach

        </div>
    </div>



</section>

<!-- JS for getting the questions  -->
<!-- <script src="{{ asset('assets/js/questions.js') }}"></script> -->

<script>
    let attemptedAnswers = {};
    let currentQuestion = 1;
    $(".question-div").hide();
    $(".step-1").show();
    function go_next_step(e) {
        // This is the next question ID 
        var next_step_id = $(e).attr("que_id");
        // Update the current question ID to the global counter 
        currentQuestion = next_step_id;
        // This is the current div id 
        var my_ref = $(e).attr("my_ref");
        // This is the next Main Div 
        var next_step_div = `.step-${next_step_id}`;

        /**
         * Before going to the next questions check all the conditions attached to the current question 
           This parameter makes sure that the condtion is attached to the next question   
           if it is 1 condition is attached else not 
        */
        var is_condition = $(next_step_div).attr('is_condition');

        if (is_condition != undefined && is_condition == 1) {
            var lbl = `.lbl-${next_step_id}`;
            // It tells which condition is set Go to step or the Lable condition 
            var conditionType = $(next_step_div).attr('swtchtyp')
            var conditions = $(lbl).attr('json-data');  // Get the conditions data from the Label 
            if (conditions != undefined) {
                conditions = JSON.parse(conditions)
                conditions.forEach(function (condition) {
                    // first check the Condition Type
                    var condition_type = condition.condition_type
                    var conditional_que_div = `.step-${condition.conditional_question_id}`; // this is the div for which we need to check the conditon 
                    var value = $(conditional_que_div).attr('attempted')  // we get the value attempted for the conditional question 
                    var conditionType = $(next_step_div).attr('swtchtyp') 

                    if(condition_type=="content_condition"){
                        var rightContentDiv=`#right_content_div_${condition.document_right_content_id}`;
                        if( value ==condition.conditional_question_value ){
                            $(rightContentDiv).removeClass('d-none');
                        }
                        
                    }else {
 // 1 is for the Lable condition 
                    if (conditionType == 1) {
     // update the lable of the current question 
                        if (value == condition.conditional_question_value) {
                            var changed_label = condition.question_label
                            $(lbl).text(changed_label)
                        }
                    } else if (conditionType == 2) {
                        // 2 is for the step condition 
                        if (value == condition.conditional_question_value) {
                            // Change the buuton value of this 
                            var current_question_next_btn = `.nxt_btn_${next_step_id}`
                            $(current_question_next_btn).attr('que_id', condition.go_to_step);
                            $(`.step-${next_step_id}`).attr('onchange', false);

                        }
                        console.log(condition, " This is the step 2 condition man sop we ")
                    } else if(conditionType == 1){
                        // both the conditons are active label and go to step 
                        window.reload();
                    }
                    }
                   

                });

            }

        }
        // get the current div previsous button refrence
        var pre_btn = `.pre_btn_${next_step_id}`;
        $(pre_btn).attr("que_id", my_ref);
        $(".question-div").hide();
        $(next_step_div).show();
    }

    function go_pre_step(e) {
        $(".question-div").hide();
        var next_step_id = $(e).attr("que_id");
        var next_step_div = `.step-${next_step_id}`;
        $(next_step_div).show();
    }

    function updateNextButton(selectElement) {

        // Get the main div 
        var shouldStepChange = $(`.step-${currentQuestion}`).attr('onchange');
        if (shouldStepChange != undefined && shouldStepChange == "false") {
            return;
        }
        // Get the currently selected option
        const selectedOption = selectElement.options[selectElement.selectedIndex];

        // Retrieve the attributes from the selected option
        const myNextBtn = selectedOption.getAttribute("my_ref_nxt");
        const queId = selectedOption.getAttribute("que_id");
        const selectedValue = selectedOption.value;
        console.log( selectedValue, " TJos os tje selected value ")
        console.log(" This is the ID : ", `.step-${currentQuestion}`)
        $(`.step-${currentQuestion}`).attr('attempted', selectedValue); 
        const targetEle = $(selectElement).attr('target-id');
        $(targetEle).text(selectedValue)
        $(myNextBtn).attr("que_id", queId);
    }

    // This is for the Radio Buttons 
    function updateNextButtonR(radioElement) {
        // Get the attributes from the selected radio button
        const myNextBtn = radioElement.getAttribute("my_ref_nxt");
        const queId = radioElement.getAttribute("que_id");
        const selectedValue = radioElement.value;
        const targetEle = $(radioElement).attr('target-id');
        $(targetEle).text(selectedValue)
        $(myNextBtn).attr("que_id", queId);
    }


    function storeAnswers(e, que_id = undefined, qtype = undefined) {
        if (qtype === "textbox") {
            // Get the div and inject the attempted value, filling the object
            var qid = `que${que_id}`;
            var main_q_div = `.step-${que_id}`;
            var right_part_target = `.qidtarget-${que_id}`;
            // Define an object to store the question ID and answer
            var obj = {
                "qid": qid,
                "ans": $(e).val() // Fixed syntax error here
            };
            $(right_part_target).text(obj.ans)
            // Store the object in attemptedAnswers using qid as the key
            attemptedAnswers[qid] = obj;

            smoothScrollToTarget(right_part_target, '.right-question-box');


            // Update the main question div with the attempted answer attribute
            $(main_q_div).attr('attempted', attemptedAnswers[qid].ans); // Fixed attribute reference here
        } else if (qtype === "datefield") {

        }else if( qtype === "dropdown" ||   qtype === "radio" ){
            var qid = `que${que_id}`;
            var main_q_div = `.step-${que_id}`;
            var right_part_target = `.qidtarget-${que_id}`;
            // Define an object to store the question ID and answer
            var obj = {
                "qid": qid,
                "ans": $(e).val() // Fixed syntax error here
            };
            $(right_part_target).text(obj.ans)
            // Store the object in attemptedAnswers using qid as the key
            attemptedAnswers[qid] = obj;

            smoothScrollToTarget(right_part_target, '.right-question-box');


            // Update the main question div with the attempted answer attribute
            $(main_q_div).attr('attempted', attemptedAnswers[qid].ans); // Fixed attribute reference here
        }
    }


    function smoothScrollToTarget(targetElement, container, offset = 35) {
        var container = $(container);
        var targetOffset = $(targetElement).offset().top - container.offset().top + container.scrollTop() - offset;
        var currentScroll = container.scrollTop();
        var distance = targetOffset - currentScroll;
        var duration = 800; // Total duration in ms
        var start = null;

        // Smooth scrolling using requestAnimationFrame
        function smoothStep(timestamp) {
            if (!start) start = timestamp;
            var progress = timestamp - start;

            // Calculate current position with ease-in function
            var scrollPosition = easeInQuad(progress, currentScroll, distance, duration);

            container.scrollTop(scrollPosition);

            if (progress < duration) {
                window.requestAnimationFrame(smoothStep);
            }
        }

        window.requestAnimationFrame(smoothStep);

        // Ease-in quadratic function
        function easeInQuad(time, start, distance, duration) {
            time /= duration;
            return distance * time * time + start;
        }
    }




</script>

@endsection