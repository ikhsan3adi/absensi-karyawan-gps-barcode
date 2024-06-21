@php
  use Illuminate\Support\Carbon;
  $m = Carbon::parse($month);
  $minWeek = $m->startOfMonth()->format('Y') . '-W' . $m->startOfMonth()->format('W');
  $maxWeek = $m->endOfMonth()->format('Y') . '-W' . $m->endOfMonth()->format('W');
@endphp
<div>
  <div class="mb-4 grid grid-cols-2 flex-wrap items-center gap-5 md:gap-8 lg:flex">
    <h3 class="col-span-2 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200 md:mb-0">
      Data Absensi
    </h3>
    <x-input type="month" name="month_filter" id="month_filter" wire:model.live="month" />
    <x-input type="week" name="week_filter" id="week_filter" wire:model.live="week" min="{{ $minWeek }}"
      max="{{ $maxWeek }}" />
    <x-input type="date" name="day_filter" id="day_filter" wire:model.live="date" />
    <x-select id="division" wire:model.live="division">
      <option value="">{{ __('Select Division') }}</option>
      @foreach (App\Models\Division::all() as $_division)
        <option value="{{ $_division->id }}" {{ $_division->id == $division ? 'selected' : '' }}>
          {{ $_division->name }}
        </option>
      @endforeach
    </x-select>
    <x-select id="jobTitle" wire:model.live="jobTitle">
      <option value="">{{ __('Select Job Title') }}</option>
      @foreach (App\Models\JobTitle::all() as $_jobTitle)
        <option value="{{ $_jobTitle->id }}" {{ $_jobTitle->id == $jobTitle ? 'selected' : '' }}>
          {{ $_jobTitle->name }}
        </option>
      @endforeach
    </x-select>
    <div class="col-span-2 flex items-center gap-2 lg:w-96">
      <x-input type="text" class="w-full" name="search" id="seacrh" wire:model="search"
        placeholder="{{ __('Search') }}" />
      <x-button type="button" wire:click="$refresh" wire:loading.attr="disabled">{{ __('Search') }}</x-button>
      @if ($search)
        <x-secondary-button type="button" wire:click="$set('search', '')" wire:loading.attr="disabled">
          {{ __('Reset') }}
        </x-secondary-button>
      @endif
    </div>
  </div>
  <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-900">
      <tr>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          {{ __('Name') }}
        </th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          {{ __('NIP') }}
        </th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          {{ __('Division') }}
        </th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          {{ __('Job Title') }}
        </th>
        <th scope="col"
          class="bg-green-100 px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:bg-green-900 dark:text-gray-300">
          {{ __('present') }}
        </th>
        <th scope="col"
          class="bg-amber-100 px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:bg-amber-900 dark:text-gray-300">
          {{ __('late') }}
        </th>
        <th scope="col"
          class="bg-blue-100 px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:bg-blue-900 dark:text-gray-300">
          {{ __('excused') }}
        </th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">
          {{ __('sick') }}
        </th>
        <th scope="col"
          class="bg-red-100 px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:bg-red-900 dark:text-gray-300">
          {{ __('absent') }}
        </th>
        <th scope="col" class="relative">
          <span class="sr-only">Actions</span>
        </th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
      @php
        $class = 'cursor-pointer px-6 py-4 text-sm font-medium text-gray-900 dark:text-white';
      @endphp
      @foreach ($employees as $employee)
        @php
          $wireClick = "wire:click=show('$employee->id')";
          $attendance = $employee->attendance;
        @endphp
        <tr wire:key="{{ $employee->id }}" class="group">
          <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700" {{ $wireClick }}>
            {{ $employee->name }}
          </td>
          <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700" {{ $wireClick }}>
            {{ $employee->nip }}
          </td>
          <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700"
            {{ $wireClick }}>
            {{ $employee->division?->name ?? '-' }}
          </td>
          <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700" {{ $wireClick }}>
            {{ $employee->jobTitle?->name ?? '-' }}
          </td>
          <td
            class="{{ $class }} bg-green-200 text-gray-900 group-hover:bg-green-300 dark:bg-green-800 dark:group-hover:bg-green-700"
            {{ $wireClick }}>
            {{ $employee->present }}
          </td>
          <td
            class="{{ $class }} bg-amber-200 text-gray-900 group-hover:bg-amber-300 dark:bg-amber-800 dark:group-hover:bg-amber-700"
            {{ $wireClick }}>
            {{ $employee->late }}
          </td>
          <td
            class="{{ $class }} bg-blue-200 text-gray-900 group-hover:bg-blue-300 dark:bg-blue-800 dark:group-hover:bg-blue-700"
            {{ $wireClick }}>
            {{ $employee->excused }}
          </td>
          <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700" {{ $wireClick }}>
            {{ $employee->sick }}
          </td>
          <td
            class="{{ $class }} bg-red-200 text-gray-900 group-hover:bg-red-300 dark:bg-red-800 dark:group-hover:bg-red-700"
            {{ $wireClick }}>
            {{ $employee->absent }}
          </td>
          <td class="px-2">
            <x-button>
              {{ __('Detail') }}
            </x-button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mt-3">
    {{ $employees->links() }}
  </div>
</div>
