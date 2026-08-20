@php
    $isActive = !empty($value);
    $inputClass = 'form-control' . ($isActive ? ' active' : '');
@endphp


<div class="input-box active">
    @if(!empty($label))
        <label class="form-label" for="{{ $id }}">{{ $label }}</label>
    @endif

    @if($type === 'textarea')
        <textarea
            name="{{ $name }}"
            id="{{ $id }}"
            rows="3"
            {{ $attributes->merge(['class' => $inputClass]) }}
            onfocus="this.parentNode.classList.add('active')"
            onblur="if(!this.value) this.parentNode.classList.remove('active')"
        >{{ $value }}</textarea>

    @elseif($type === 'file')
        <input
            type="file"
            name="{{ $name }}"
            id="{{ $id }}"
            {{ $attributes->merge(['class' => $inputClass]) }}
            onfocus="this.parentNode.classList.add('active')"
            onblur="if(!this.value) this.parentNode.classList.remove('active')"
        />

    @elseif($type === 'select' && isset($questions))

       <select
            class="form-select js-select2 {{ $class ?? '' }}"
            data-search="on"
            name="condition_question_id-{{ $name ?? '' }}"
            id="condition_question_id-{{ $id ?? '' }}"
            onfocus="this.parentNode.classList.add('active')"
            onblur="if(!this.value) this.parentNode.classList.remove('active')"
        >
        @if(isset($questions) && $questions != null)
        @foreach($questions as $question)
        <option value="{{ $question->getName() }}"
            {{ isset($qu_conditions->conditional_question_id) && $qu_conditions->conditional_question_id == $question->getName() ? 'selected' : '' }}>
            {{ $question->getName() }}
       </option>
        @endforeach
        @endif
        </select>
 @elseif($type === 'select_conditions' && isset($qu_conditions))
        <select
            class="form-select js-select2 {{ $class ?? '' }}"
            name="conditions-{{ $qu_conditions->id ?? '' }}[]"
            id="conditions-{{ $qu_conditions->id ?? '' }}"
            onfocus="this.parentNode.classList.add('active')"
            onblur="if(!this.value) this.parentNode.classList.remove('active')"
        >
            <option value="is_equal_to" @selected($qu_conditions->conditional_check == '1')>is equal to</option>
            <option value="is_greater_than" @selected($qu_conditions->conditional_check == '2')>is greater than</option>
            <option value="is_less_than" @selected($qu_conditions->conditional_check == '3')>is less than</option>
            <option value="not_equal_to" @selected($qu_conditions->conditional_check == '4')>not equal to</option>
        </select>

 @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            {{ $attributes->merge(['class' => $inputClass]) }}
            onfocus="this.parentNode.classList.add('active')"
            onblur="if(!this.value) this.parentNode.classList.remove('active')"
        />
    @endif
</div>
