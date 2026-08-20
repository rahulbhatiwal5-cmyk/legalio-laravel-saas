<select name="{{ $name }}" id="{{ $id }}"  class="form-control form-control-lg" style="appearance: auto;" >
    @foreach ($option as $key=>$value )
    <option value="{{ $key }}" {{ $selected == $key ?'selected' : '' }}>{{ $value }}</option>
    @endforeach
</select>
