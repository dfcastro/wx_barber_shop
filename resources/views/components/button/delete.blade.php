<button {{ $attributes->merge(['type' => 'button', 'class' => 'font-medium text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 ml-2']) }}>
    {{ $slot->isEmpty() ? 'Deletar' : $slot }}
</button>