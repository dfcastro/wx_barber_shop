{{-- resources/views/admin/appointments/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Agendamentos') }}
        </h2>
    </x-slot>
    <div>
        @livewire('admin.appointments.appointment-list')
    </div>
</x-app-layout>