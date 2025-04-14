<textarea
    {{
    $attributes->merge([
        'class' => 'form-control',
        'rows' => '4'
    ])
    }}
>{{ $slot }}</textarea>

{{--<textarea--}}
{{--    {{ $attributes->merge([--}}
{{--        'class' => 'form-control',--}}
{{--        'rows' => '4'--}}
{{--    ]) }}--}}
{{-->--}}
{{--    {{ $slot }}--}}
{{--</textarea>--}}
