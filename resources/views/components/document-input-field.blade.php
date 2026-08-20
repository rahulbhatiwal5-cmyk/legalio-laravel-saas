@php
    $classes = 'input-box';
    if ($alwaysActive) $classes .= ' active';
    if ($error) $classes .= ' error';
 
@endphp


<div class="{{ $classes }}">
    @if($label)
        <label class="form-label" for="{{ $id }}">{{ $label }}</label>
    @endif

    @if($type === 'select')
        <select class="form-select {{ $class }}"
                name="{{ $name }}"
                id="{{ $id }}"
                onfocus="this.parentNode.classList.add('active')"
                onblur="if(!this.value) this.parentNode.classList.remove('active')">
                <option value="0">Checkout</option>
            @foreach($options as $key => $option)
                <option value="{{ $key }}" @selected(old($name, $value) == $key)>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    @elseif($type === 'question_select')
        <select class="form-select {{ $class }}"
                name="{{ $name }}"
                id="{{ $id }}"
                onfocus="this.parentNode.classList.add('active')"
                onblur="if(!this.value) this.parentNode.classList.remove('active')">
            @foreach($options as $key => $option)
                <option value="{{ $key }}" @selected(old($name, $value) == $key)>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    @elseif($type === 'condition_select')
        <select class="form-select {{ $class }}"
                name="{{ $name }}"
                id="{{ $id }}"
                onfocus="this.parentNode.classList.add('active')"
                onblur="if(!this.value) this.parentNode.classList.remove('active')">
                <option value="" disabled {{ empty(old($name, $value)) ? 'selected' : '' }}>Select</option>
            @foreach($options as $key => $option)
                <option value="{{ $key }}" @selected(old($name, $value) == $key)>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    @elseif($type === 'textarea')
        <textarea
            class="{{ $class }}"
            id="{{ $id }}"
            name="{{ $name }}"
            rows="3"
            onfocus="this.parentNode.classList.add('active')"
            onblur="if(!this.value) this.parentNode.classList.remove('active')"
            >{{ old($name, $value) }}</textarea>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            class="{{ $class }}"
            id="{{ $id }}"
            value="{{ old($name, $value) }}"
            onfocus="this.parentNode.classList.add('active')"
            onblur="if(!this.value) this.parentNode.classList.remove('active')"
            
        />
    @endif

    @error($name)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>


