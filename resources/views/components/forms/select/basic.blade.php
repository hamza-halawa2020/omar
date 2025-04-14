@props(['name' => ''])

<select
    name="{{ $name }}"
    {{ $attributes->merge(['class' => 'form-select' . ($errors->has($name) ? ' is-invalid' : '')]) }}
>
    {{ $slot }}
</select>

@if($errors->has($name))
    <div class="text-sm text-danger mt-1">
        {{ $errors->first($name) }}
    </div>
@endif

