@props(['name' => '', 'value' => ''])
<x-forms.labels.basic>
    {{ $slot }}
</x-forms.labels.basic>
<x-forms.inputs.basic :name="$name" {{ $attributes }} :value="$value" />
