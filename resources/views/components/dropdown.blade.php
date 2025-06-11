@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-gray-700'])

@php
switch ($align) {
    case 'left':
        $alignmentClasses = 'origin-top-left left-0';
        break;
    case 'top':
        $alignmentClasses = 'origin-top';
        break;
    case 'right':
    default:
        $alignmentClasses = 'origin-top-right right-0';
        break;
}

switch ($width) {
    case '48':
        $width = 'w-48';
        break;
}
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    {{-- O botão que abre o menu precisa da referência x-ref="trigger" --}}
    <div @click="open = ! open" x-ref="trigger">
        {{ $trigger }}
    </div>

    {{-- O menu "teletransportado" que se ancora ao trigger --}}
    @teleport('body')
        <div x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg"
                style="display: none;"
                @click="open = false"
                x-anchor.bottom-end.offset.5="$refs.trigger">
            <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
                {{ $content }}
            </div>
        </div>
    @endteleport
</div>