{{--<a--}}
{{--        {{ $attributes->merge(['class' => 'btn btn-primary text-sm btn-sm px-3 py-3 rounded-lg flex items-center gap-2']) }}--}}
{{-->--}}
{{--    {{ $slot }}--}}
{{--</a>--}}

<a
    {{ $attributes->merge(['class' => 'btn btn-primary d-flex align-items-center gap-2 px-3 py-2 rounded']) }}
>
    {{ $slot }}
</a>
