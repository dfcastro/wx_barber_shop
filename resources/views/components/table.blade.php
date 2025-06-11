<div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200 dark:divide-gray-700']) }}>
        {{ $slot }}
    </table>
</div>