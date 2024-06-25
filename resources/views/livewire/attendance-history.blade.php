<div>
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce
  <h3 class="col-span-2 mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
    Data Absensi
  </h3>
  <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
    <x-label for="month_filter" value="Bulan"></x-label>
    <x-input type="month" name="month_filter" id="month_filter" wire:model.live="month" />
  </div>
  <h5 class="mt-3 text-sm">Klik pada tanggal untuk melihat detail</h5>
  <div class="mt-4 flex w-full flex-col gap-3 lg:flex-row">
    <div class="grid w-96 grid-cols-7 overflow-x-scroll dark:text-white lg:w-[36rem]">
      @foreach (['M', 'S', 'S', 'R', 'K', 'J', 'S'] as $day)
        <div
          class="{{ $day === 'M' ? 'text-red-500' : '' }} {{ $day === 'J' ? 'text-green-600 dark:text-green-500' : '' }} flex h-10 items-center justify-center border border-gray-300 text-center dark:border-gray-600">
          {{ $day }}
        </div>
      @endforeach
      @if ($start->dayOfWeek !== 0)
        @foreach (range(1, $start->dayOfWeek) as $i)
          <div class="h-14 border border-gray-300 bg-gray-100 dark:border-gray-600 dark:bg-gray-700">
          </div>
        @endforeach
      @endif
      @php
        $presentCount = 0;
        $lateCount = 0;
        $excusedCount = 0;
        $sickCount = 0;
        $absentCount = 0;
      @endphp
      @foreach ($dates as $date)
        @php
          $isWeekend = $date->isWeekend();
          $attendance = $attendances->firstWhere(fn($v, $k) => $v['date'] === $date->format('Y-m-d'));
          $status = ($attendance ?? [
              'status' => $isWeekend || !$date->isPast() ? '-' : 'absent',
          ])['status'];

          switch ($status) {
              case 'present':
                  $shortStatus = 'H';
                  $bgColor =
                      'bg-green-200 dark:bg-green-800 hover:bg-green-300 dark:hover:bg-green-700 border border-green-600';
                  $presentCount++;
                  break;
              case 'late':
                  $shortStatus = 'T';
                  $bgColor =
                      'bg-amber-200 dark:bg-amber-800 hover:bg-amber-300 dark:hover:bg-amber-700 border border-amber-600';
                  $lateCount++;
                  break;
              case 'excused':
                  $shortStatus = 'I';
                  $bgColor =
                      'bg-blue-200 dark:bg-blue-800 hover:bg-blue-300 dark:hover:bg-blue-700 border border-blue-600';
                  $excusedCount++;
                  break;
              case 'sick':
                  $shortStatus = 'S';
                  $bgColor =
                      'bg-purple-200 dark:bg-purple-950 hover:bg-purple-100 dark:hover:bg-purple-700 border border-purple-600';
                  $sickCount++;
                  break;
              case 'absent':
                  $shortStatus = 'A';
                  $bgColor =
                      'bg-red-200 dark:bg-red-950 text-red-500 dark:text-red-200 border border-red-300 dark:border-red-700';
                  $absentCount++;
                  break;
              default:
                  $shortStatus = '-';
                  $bgColor =
                      'bg-slate-200 text-slate-600 dark:text-slate-200 dark:bg-slate-800 border border-gray-400 dark:border-gray-700';
                  break;
          }
        @endphp
        @if ($attendance && ($attendance['attachment'] || $attendance['note'] || $attendance['coordinates']))
          <button class="{{ $bgColor }} h-14 w-full py-1 text-center" wire:click="show({{ $attendance['id'] }})"
            onclick="setLocation({{ $attendance['coordinates']['lat'] ?? 0 }}, {{ $attendance['coordinates']['lng'] ?? 0 }})">
            <span
              class="{{ $date->isSunday() ? 'text-red-500' : '' }} {{ $date->isFriday() ? 'text-green-600 dark:text-green-500' : '' }}">
              {{ $date->format('d') }}
            </span>
            <br>
            {{ $shortStatus }}
          </button>
        @else
          <div class="{{ $bgColor }} h-14 py-1 text-center">
            <span
              class="{{ $date->isSunday() ? 'text-red-500' : '' }} {{ $date->isFriday() ? 'text-green-600 dark:text-green-500' : '' }}">
              {{ $date->format('d') }}
            </span>
            <br>
            {{ $shortStatus }}
          </div>
        @endif
      @endforeach
      @if ($end->dayOfWeek !== 6)
        @foreach (range(5, $end->dayOfWeek) as $i)
          <div class="h-14 border border-gray-300 bg-gray-100 dark:border-gray-600 dark:bg-gray-700"></div>
        @endforeach
      @endif
    </div>
    <div class="grid h-fit w-full grid-cols-2 gap-3 md:grid-cols-4">
      <div
        class="flex items-center justify-between rounded-md bg-green-200 px-4 py-2 text-gray-800 dark:bg-green-900 dark:text-white dark:shadow-gray-700">
        <div>
          <h4 class="text-lg font-semibold md:text-xl">Hadir: {{ $presentCount + $lateCount }}</h4>
          Terlambat: {{ $lateCount }}
        </div>
      </div>
      <div
        class="flex items-center justify-between rounded-md bg-blue-200 px-4 py-2 text-gray-800 dark:bg-blue-900 dark:text-white dark:shadow-gray-700">
        <div>
          <h4 class="text-lg font-semibold md:text-xl">Izin: {{ $excusedCount }}</h4>
        </div>
      </div>
      <div
        class="flex items-center justify-between rounded-md bg-purple-200 px-4 py-2 text-gray-800 dark:bg-purple-900 dark:text-white dark:shadow-gray-700">
        <div>
          <h4 class="text-lg font-semibold md:text-xl">Sakit: {{ $sickCount }}</h4>
        </div>
      </div>
      <div
        class="flex items-center justify-between rounded-md bg-red-200 px-4 py-2 text-gray-800 dark:bg-red-900 dark:text-white dark:shadow-gray-700">
        <div>
          <h4 class="text-lg font-semibold md:text-xl">Absen: {{ $absentCount }}</h4>
        </div>
      </div>
    </div>
  </div>

  <x-modal wire:model="showDetail" onclose="removeMap()">
    <div class="px-6 py-4">
      @if ($currentAttendance)
        <h3 class="mb-3 text-xl font-semibold">{{ $currentAttendance['name'] }}</h3>
        <div class="mb-3 w-full">
          <x-label for="nip" value="{{ __('NIP') }}"></x-label>
          <x-input type="text" class="w-full" id="nip" disabled
            value="{{ $currentAttendance['nip'] }}"></x-input>
        </div>
        <div class="mb-3 flex w-full gap-3">
          <div class="w-full">
            <x-label for="date" value="{{ __('Date') }}"></x-label>
            <x-input type="text" class="w-full" id="date" disabled
              value="{{ $currentAttendance['date'] }}"></x-input>
          </div>
          <div class="w-full">
            <x-label for="status" value="{{ __('Status') }}"></x-label>
            <x-input type="text" class="w-full" id="status" disabled
              value="{{ __($currentAttendance['status']) }}"></x-input>
          </div>
        </div>
        <div class="flex flex-col gap-3">
          @if ($currentAttendance['attachment'])
            <x-label for="attachment" value="{{ __('Attachment') }}"></x-label>
            <img src="{{ $currentAttendance['attachment'] }}" alt="Attachment"
              class="max-h-64 object-contain md:max-h-96">
          @endif
          @if ($currentAttendance['note'])
            <x-label for="note" value="Keterangan"></x-label>
            <x-textarea type="text" id="note" disabled value="{{ $currentAttendance['note'] }}"></x-textarea>
          @endif
          @if (
              $currentAttendance['coordinates'] &&
                  $currentAttendance['coordinates']['lat'] &&
                  $currentAttendance['coordinates']['lng']
          )
            <x-label for="map" value="Koordinat Lokasi Absen"></x-label>
            <p>{{ $currentAttendance['coordinates']['lat'] }}, {{ $currentAttendance['coordinates']['lng'] }}</p>
            <div class="my-2 h-52 w-full md:h-64" id="map"></div>
          @endif
          @if ($currentAttendance['time_in'] || $currentAttendance['time_out'])
            <div class="grid grid-cols-2 gap-3">
              <x-label for="time_in" value="Waktu Masuk"></x-label>
              <x-label for="time_out" value="Waktu Keluar"></x-label>
              <x-input type="text" id="time_in" disabled
                value="{{ $currentAttendance['time_in'] ?? '-' }}"></x-input>
              <x-input type="text" id="time_out" disabled
                value="{{ $currentAttendance['time_out'] ?? '-' }}"></x-input>
            </div>
          @endif

          @if ($currentAttendance['barcode'] ?? false)
            <x-label for="barcode" value="Barcode"></x-label>
            <x-input type="text" id="barcode" disabled
              value="{{ $currentAttendance['barcode']['name'] }}"></x-input>
          @endif
        </div>
      @endif
    </div>
  </x-modal>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script>
    let map = null;

    function setLocation(lat, lng) {
      removeMap();
      setTimeout(() => {
        map = L.map('map').setView([Number(lat), Number(lng)], 19);
        L.marker([Number(lat), Number(lng)]).addTo(map);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 21,
        }).addTo(map);
      }, 500);
    }

    function removeMap() {
      if (map !== null) map.remove();
      map = null;
    }
  </script>
</div>
