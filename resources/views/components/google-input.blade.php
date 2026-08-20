@props([
    'type' => 'text',
    'name',
    'label' => '',
    'value' => '',
    'attributes' => [],
    'class' => '', // allow dynamic class
])

@php
    $hasError = $errors->has($name);
    $inputClasses = 'input-1';
    if ($hasError) $inputClasses .= ' error';

    $wrapperClasses = 'input-box';
    if (!empty(old($name, $value))) $wrapperClasses .= ' active';
    if ($hasError) $wrapperClasses .= ' error';
@endphp

<div class="{{ $wrapperClasses }}">
    <label class="input-label" for="{{ $id }}">{{ $label }}</label>

    @if ($type === 'select')
        <select
            name="{{ $name }}"
            id="{{ $id }}"
            {{ $attributes->merge([
                'class' => $inputClasses,
                'onfocus' => "this.parentNode.classList.add('active')",
                'onblur' => "if(!this.value) this.parentNode.classList.remove('active')",
            ]) }}
        >
            <option value=""></option>
            @foreach($options as $key => $option)
                <option value="{{ $key }}" @selected(old($name, $value) == $key)>{{ $option }}</option>
            @endforeach
        </select>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ old($name, $value) }}"
            {{ $attributes->merge([
                'class' => $inputClasses,
                'onfocus' => "this.parentNode.classList.add('active')",
                'onblur' => "if(!this.value) this.parentNode.classList.remove('active')",
                'autocomplete' => $type === 'password' ? 'new-password' : 'off',
            ]) }}
        />
    @endif


<!-- @error($name)
    <span class="text-danger">{{ $message }}</span>
@enderror -->

</div>
    
