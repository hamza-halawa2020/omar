{{--<button {{ $attributes->merge(['class' => "btn btn-primary text-sm btn-sm px-3 py-3 rounded-lg flex items-center gap-2"]) }}>--}}
{{--    {{ $slot }}--}}
{{--</button>--}}

<button {{ $attributes->merge(['class' => 'btn btn-primary d-flex align-items-center gap-2 px-6 py-2 rounded fs-6']) }}>
    {{ $slot }}
</button>
