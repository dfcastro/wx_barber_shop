@props(['href'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'font-medium text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200']) }}>
    {{ $slot->isEmpty() ? 'Editar' : $slot }}
</a>