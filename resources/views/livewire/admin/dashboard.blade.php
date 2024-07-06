@php
  $date = Carbon\Carbon::now();
@endphp
<div>
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce
  <div class="flex flex-col justify-between sm:flex-row">
    <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
      Absensi Hari Ini
    </h3>
    <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
      Jumlah Karyawan: {{ $employeesCount }}
    </h3>
  </div>
  <div class="mb-4 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
    <div class="rounded-md bg-green-200 px-8 py-4 text-gray-800 dark:bg-green-900 dark:text-white dark:shadow-gray-700">
      <span class="text-2xl font-semibold md:text-3xl">Hadir: {{ $presentCount }}</span><br>
      <span>Terlambat: {{ $lateCount }}</span>
    </div>
    <div class="rounded-md bg-blue-200 px-8 py-4 text-gray-800 dark:bg-blue-900 dark:text-white dark:shadow-gray-700">
      <span class="text-2xl font-semibold md:text-3xl">Izin: {{ $excusedCount }}</span><br>
      <span>Izin/Cuti</span>
    </div>
    <div
      class="rounded-md bg-purple-200 px-8 py-4 text-gray-800 dark:bg-purple-900 dark:text-white dark:shadow-gray-700">
      <span class="text-2xl font-semibold md:text-3xl">Sakit: {{ $sickCount }}</span>
    </div>
    <div class="rounded-md bg-red-200 px-8 py-4 text-gray-800 dark:bg-red-900 dark:text-white dark:shadow-gray-700">
      <span class="text-2xl font-semibold md:text-3xl">Tidak Hadir: {{ $absentCount }}</span><br>
      <span>Tidak/Belum Hadir</span>
    </div>
  </div>

  <div class="mb-4 overflow-x-scroll">
    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-900">
        <tr>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('Name') }}
          </th>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('NIP') }}
          </th>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('Division') }}
          </th>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('Job Title') }}
          </th>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('Shift') }}
          </th>
          <th scope="col"
            class="text-nowrap border border-gray-300 px-1 py-3 text-center text-xs font-medium text-gray-500 dark:border-gray-600 dark:text-gray-300">
            Status
          </th>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('Time In') }}
          </th>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('Time Out') }}
          </th>
          <th scope="col" class="relative">
            <span class="sr-only">Actions</span>
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
        @php
          $class = 'px-4 py-3 text-sm font-medium text-gray-900 dark:text-white';
        @endphp
        @foreach ($employees as $employee)
          @php
            $attendance = $employee->attendance;
            $timeIn = $attendance ? $attendance?->time_in?->format('H:i:s') : null;
            $timeOut = $attendance ? $attendance?->time_out?->format('H:i:s') : null;
            $isWeekend = $date->isWeekend();
            $status = ($attendance ?? [
                'status' => $isWeekend || !$date->isPast() ? '-' : 'absent',
            ])['status'];
            switch ($status) {
                case 'present':
                    $shortStatus = 'H';
                    $bgColor =
                        'bg-green-200 dark:bg-green-800 hover:bg-green-300 dark:hover:bg-green-700 border border-green-300 dark:border-green-600';
                    break;
                case 'late':
                    $shortStatus = 'T';
                    $bgColor =
                        'bg-amber-200 dark:bg-amber-800 hover:bg-amber-300 dark:hover:bg-amber-700 border border-amber-300 dark:border-amber-600';
                    break;
                case 'excused':
                    $shortStatus = 'I';
                    $bgColor =
                        'bg-blue-200 dark:bg-blue-800 hover:bg-blue-300 dark:hover:bg-blue-700 border border-blue-300 dark:border-blue-600';
                    break;
                case 'sick':
                    $shortStatus = 'S';
                    $bgColor = 'hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600';
                    break;
                case 'absent':
                    $shortStatus = 'A';
                    $bgColor =
                        'bg-red-200 dark:bg-red-800 hover:bg-red-300 dark:hover:bg-red-700 border border-red-300 dark:border-red-600';
                    break;
                default:
                    $shortStatus = '-';
                    $bgColor = 'hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600';
                    break;
            }
          @endphp
          <tr wire:key="{{ $employee->id }}" class="group">
            {{-- Detail karyawan --}}
            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              {{ $employee->name }}
            </td>
            <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              {{ $employee->nip }}
            </td>
            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              {{ $employee->division?->name ?? '-' }}
            </td>
            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              {{ $employee->jobTitle?->name ?? '-' }}
            </td>
            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              {{ $attendance->shift?->name ?? '-' }}
            </td>

            {{-- Absensi --}}
            <td
              class="{{ $bgColor }} text-nowrap px-1 py-3 text-center text-sm font-medium text-gray-900 dark:text-white">
              {{ __($status) }}
            </td>

            {{-- Waktu masuk/keluar --}}
            <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              {{ $timeIn ?? '-' }}
            </td>
            <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              {{ $timeOut ?? '-' }}
            </td>

            {{-- Action --}}
            <td
              class="cursor-pointer text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:text-white dark:group-hover:bg-gray-700">
              <div class="flex items-center justify-center gap-3">
                @if ($attendance && ($attendance->attachment || $attendance->note || $attendance->lat_lng))
                  <x-button type="button" wire:click="show({{ $attendance->id }})"
                    onclick="setLocation({{ $attendance->latitude ?? 0 }}, {{ $attendance->longitude ?? 0 }})">
                    {{ __('Detail') }}
                  </x-button>
                @else
                  -
                @endif
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  {{ $employees->links() }}

  <x-attendance-detail-modal :current-attendance="$currentAttendance" />
  @stack('attendance-detail-scripts')
</div>
