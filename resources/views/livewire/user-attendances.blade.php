<div>
  <div class="mb-4 flex items-center gap-5 md:gap-8">
    <h3 class="text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200 md:mb-0">
      Data Absensi
    </h3>
    <x-input type="month" name="month_filter" id="month_filter" wire:model.live="month" />
  </div>
  <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-900">
      <tr>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          {{ __('Name') }}
        </th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          {{ __('Date') }}
        </th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          {{ __('Status') }}
        </th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          {{ __('Time In') }}
        </th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          {{ __('Time Out') }}
        </th>
        <th scope="col" class="relative px-6 py-3">
          <span class="sr-only">Actions</span>
        </th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
      @php
        $class = 'cursor-pointer group-hover:bg-gray-100 dark:group-hover:bg-gray-700';
      @endphp
      @foreach ($employees as $employee)
        @php
          $wireClick = "wire:click=show('$employee->id')";
          $attendance = $employee->attendance;
        @endphp
        <tr wire:key="{{ $employee->id }}" class="group">
          <td class="{{ $class }} px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"
            {{ $wireClick }}>
            {{ $employee->name }}
          </td>
          <td class="{{ $class }} px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"
            {{ $wireClick }}>
            {{ $attendance?->date?->format('d F Y') }}
          </td>
          <td class="{{ $class }} px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"
            {{ $wireClick }}>
            @switch($attendance?->status)
              @case('present')
                <x-badge type="success">
                  {{ __($attendance?->status) }}
                </x-badge>
              @break

              @case('late')
                <x-badge type="warning">
                  {{ __($attendance?->status) }}
                </x-badge>
              @break

              @case('excused')
                <x-badge type="info">
                  {{ __($attendance?->status) }}
                </x-badge>
              @break

              @case('sick')
                <x-badge type="warning">
                  {{ __($attendance?->status) }}
                </x-badge>
              @break

              @case('absent')
                <x-badge type="danger">
                  {{ __($attendance?->status) }}
                </x-badge>
              @break

              @default
                <x-badge type="disabled">
                  {{ __($attendance?->status) }}
                </x-badge>
            @endswitch
          </td>
          <td class="{{ $class }} px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"
            {{ $wireClick }}>
            {{ $attendance?->time_in?->format('H:i:s') ?? '-' }}
          </td>
          <td class="{{ $class }} px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"
            {{ $wireClick }}>
            {{ $attendance?->time_in?->format('H:i:s') ?? '-' }}
          </td>
          <td class="relative flex justify-end gap-2 px-6 py-4">
            <x-button wire:click="edit('{{ $attendance?->id }}')">
              Edit
            </x-button>
            {{-- <x-danger-button wire:click="confirmDeletion('{{ $attendance?->id }}', '{{ $attendance?->name }}')">
              Delete
            </x-danger-button> --}}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mt-3">
    {{ $employees->links() }}
  </div>
</div>
