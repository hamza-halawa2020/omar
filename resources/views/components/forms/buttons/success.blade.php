{{--<button--}}
{{--    {{ $attributes->merge(['class' => 'px-4 py-2 text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50 rounded-lg']) }}--}}
{{-->--}}
{{--    {{ $slot }}--}}
{{--</button>--}}

<button
    {{ $attributes->merge(['class' => 'btn text-white bg-success border-0 px-4 py-2 rounded']) }}
>
    {{ $slot }}
</button>
