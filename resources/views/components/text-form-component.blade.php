@props([
    'label',
    'varname',
    'placeholder',
    'classVar'=>null
])


<div class="mb-4">
    <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="{{$varname}}">
        {{ $label }}
    </label>
    <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has($varname)? 'is-invalid' : '' }}" id="{{$varname}}" name="{{$varname}}" placeholder="{{$placeholder}}">{{isset($classVar) ? old($varname, $classVar) : old($varname)}}</textarea>
    @if($errors->has($varname))
    <div class="error-fb">
        <sub>This field cannot be empty.</sub>
    </div>
    @endif
</div>
