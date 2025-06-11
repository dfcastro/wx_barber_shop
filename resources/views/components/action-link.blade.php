@props(['href'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'font-semibold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200']) }}>
    {{ $slot }}
</a>