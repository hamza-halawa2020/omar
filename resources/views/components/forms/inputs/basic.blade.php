@props(['name' => '', 'value' => ''])

<input
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'form-control']) }}
>

@error($name)
<p class="text-sm text-danger mt-1">{{ $message }}</p>
@enderror
