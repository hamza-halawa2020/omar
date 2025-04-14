{{--<a--}}
{{--        {{ $attributes->merge(['class' => 'font-medium text-blue-600 dark:text-blue-500 hover:underline cursor-pointer']) }}--}}
{{-->--}}
{{--    {{ $slot }}--}}
{{--</a>--}}

<a
    {{ $attributes->merge(['class' => 'fw-medium text-primary link-underline link-underline-opacity-0 link-underline-opacity-100-hover cursor-pointer']) }}
>
    {{ $slot }}
</a>
