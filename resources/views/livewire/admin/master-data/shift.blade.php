<div>
  <div class="mb-4 flex-col items-center gap-5 sm:flex-row md:flex md:justify-between lg:mr-4">
    <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200 md:mb-0">
      Data Shift
    </h3>
    <x-button wire:click="showCreating">
      <x-heroicon-o-plus class="mr-2 h-4 w-4" /> Tambah Shift
    </x-button>
  </div>
  <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-900">
      <tr>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          Shift
        </th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          {{ __('Time Start') }}
        </th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          {{ __('Time End') }}
        </th>
        <th scope="col" class="relative px-6 py-3">
          <span class="sr-only">Actions</span>
        </th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
      @foreach ($shifts as $shift)
        <tr>
          <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
            {{ $shift->name }}
          </td>
          <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
            {{ $shift->start_time }}
          </td>
          <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
            {{ $shift->end_time ?? '-' }}
          </td>
          <td class="relative flex justify-end gap-2 px-6 py-4">
            <x-button wire:click="edit({{ $shift->id }})">
              Edit
            </x-button>
            <x-danger-button wire:click="confirmDeletion({{ $shift->id }}, '{{ $shift->name }}')">
              Delete
            </x-danger-button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <x-confirmation-modal wire:model="confirmingDeletion">
    <x-slot name="title">
      Hapus Shift
    </x-slot>

    <x-slot name="content">
      Apakah Anda yakin ingin menghapus <b>{{ $deleteName }}</b>?
    </x-slot>

    <x-slot name="footer">
      <x-secondary-button wire:click="$toggle('confirmingDeletion')" wire:loading.attr="disabled">
        {{ __('Cancel') }}
      </x-secondary-button>

      <x-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">
        {{ __('Confirm') }}
      </x-danger-button>
    </x-slot>
  </x-confirmation-modal>

  <x-dialog-modal wire:model="creating">
    <x-slot name="title">
      Shift Baru
    </x-slot>

    <form wire:submit="create">
      <x-slot name="content">
        <div>
          <x-label for="name">Nama Shift</x-label>
          <x-input id="name" class="mt-1 block w-full" type="text" wire:model="form.name" />
          @error('form.name')
            <x-input-error for="form.name" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:gap-3">
          <div class="w-full">
            <x-label for="start_time">{{ __('Time Start') }}</x-label>
            <x-input id="start_time" class="mt-1 block w-full" type="time" wire:model="form.start_time" required />
            @error('form.start_time')
              <x-input-error for="form.start_time" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
          <div class="w-full">
            <x-label for="end_time">{{ __('Time End') }}</x-label>
            <x-input id="end_time" class="mt-1 block w-full" type="time" wire:model="form.end_time" />
            @error('form.end_time')
              <x-input-error for="form.end_time" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
        </div>
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('creating')" wire:loading.attr="disabled">
          {{ __('Cancel') }}
        </x-secondary-button>

        <x-button class="ml-2" wire:click="create" wire:loading.attr="disabled">
          {{ __('Confirm') }}
        </x-button>
      </x-slot>
    </form>
  </x-dialog-modal>

  <x-dialog-modal wire:model="editing">
    <x-slot name="title">
      Edit Shift
    </x-slot>

    <form wire:submit.prevent="update" id="shift-edit">
      <x-slot name="content">
        <div>
          <x-label for="name">Nama Shift</x-label>
          <x-input id="name" class="mt-1 block w-full" type="text" wire:model="form.name" />
          @error('form.name')
            <x-input-error for="form.name" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:gap-3">
          <div class="w-full">
            <x-label for="start_time">{{ __('Time Start') }}</x-label>
            <x-input id="start_time" class="mt-1 block w-full" type="time" wire:model="form.start_time" required />
            @error('form.start_time')
              <x-input-error for="form.start_time" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
          <div class="w-full">
            <x-label for="end_time">{{ __('Time End') }}</x-label>
            <x-input id="end_time" class="mt-1 block w-full" type="time" wire:model="form.end_time" />
            @error('form.end_time')
              <x-input-error for="form.end_time" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
        </div>
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('editing')" wire:loading.attr="disabled">
          {{ __('Cancel') }}
        </x-secondary-button>

        <x-button class="ml-2" wire:click="update" wire:loading.attr="disabled">
          {{ __('Confirm') }}
        </x-button>
      </x-slot>
    </form>
  </x-dialog-modal>
</div>
