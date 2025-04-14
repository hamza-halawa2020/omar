{{--<button--}}
{{--    {{ $attributes->merge(['class' => 'px-3 py-1.5 text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50 rounded-lg text-sm']) }}--}}
{{-->--}}
{{--    {{ $slot }}--}}
{{--</button>--}}

<button
    {{ $attributes->merge(['class' => 'btn btn-success btn-sm text-white px-3 py-1 rounded']) }}
>
    {{ $slot }}
</button>
