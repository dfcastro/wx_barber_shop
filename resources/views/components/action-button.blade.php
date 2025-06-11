@props(['color' => 'indigo'])

@php
    $colorClasses = match ($color) {
        'red'    => 'text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200',
        'green'  => 'text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200',
        'blue'   => 'text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200',
        'purple' => 'text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-200',
        default  => 'text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200',
    };
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => 'font-semibold ' . $colorClasses]) }}>
    {{ $slot }}
</button>