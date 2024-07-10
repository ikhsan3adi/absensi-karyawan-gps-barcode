<div class="w-full">
  @php
    use Illuminate\Support\Carbon;
  @endphp
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce
  @pushOnce('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
      integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
      let currentMap = document.getElementById('currentMap');
      let map = document.getElementById('map');

      setTimeout(() => {
        toggleMap();
        toggleCurrentMap();
      }, 1000);

      function toggleCurrentMap() {
        const mapIsVisible = currentMap.style.display === "none";
        currentMap.style.display = mapIsVisible ? "block" : "none";
        document.querySelector('#toggleCurrentMap').innerHTML = mapIsVisible ?
          `<x-heroicon-s-chevron-up class="mr-2 h-5 w-5" />` :
          `<x-heroicon-s-chevron-down class="mr-2 h-5 w-5" />`;
      }

      function toggleMap() {
        const mapIsVisible = map.style.display === "none";
        map.style.display = mapIsVisible ? "block" : "none";
      }
    </script>
  @endpushOnce

  @if (!$isAbsence)
    <script src="{{ url('/assets/js/html5-qrcode.min.js') }}"></script>
  @endif

  <div class="flex flex-col gap-4 md:flex-row">
    @if (!$isAbsence)
      <div class="flex flex-col gap-4">
        <div>
          <x-select id="shift" class="mt-1 block w-full" wire:model="shift_id" disabled="{{ !is_null($attendance) }}">
            <option value="">{{ __('Select Shift') }}</option>
            @foreach ($shifts as $shift)
              <option value="{{ $shift->id }}" {{ $shift->id == $shift_id ? 'selected' : '' }}>
                {{ $shift->name . ' | ' . $shift->start_time . ' - ' . $shift->end_time }}
              </option>
            @endforeach
          </x-select>
          @error('shift_id')
            <x-input-error for="shift" class="mt-2" message={{ $message }} />
          @enderror
        </div>
        <div class="flex justify-center outline outline-gray-100 dark:outline-slate-700" wire:ignore>
          <div id="scanner" class="min-h-72 sm:min-h-96 w-72 rounded-sm outline-dashed outline-slate-500 sm:w-96">
          </div>
        </div>
      </div>
    @endif
    <div class="w-full">
      <h4 id="scanner-error" class="mb-3 text-lg font-semibold text-red-500 dark:text-red-400 sm:text-xl" wire:ignore>
      </h4>
      <h4 id="scanner-result" class="mb-3 hidden text-lg font-semibold text-green-500 dark:text-green-400 sm:text-xl">
        {{ $successMsg }}
      </h4>
      <h4 id="latlng" class="mb-3 text-lg font-semibold text-gray-600 dark:text-gray-100 sm:text-xl">
        {{ __('Date') . ': ' . now()->format('d/m/Y') }}<br>

        @if (!is_null($currentLiveCoords))
          <div class="flex justify-between">
            <a href="{{ \App\Helpers::getGoogleMapsUrl($currentLiveCoords[0], $currentLiveCoords[1]) }}" target="_blank"
              class="underline hover:text-blue-400">
              {{ __('Your location') . ': ' . $currentLiveCoords[0] . ', ' . $currentLiveCoords[1] }}
            </a>
            <button class="text-nowrap h-6" onclick="toggleCurrentMap()" id="toggleCurrentMap">
              <x-heroicon-s-chevron-down class="mr-2 h-5 w-5" />
            </button>
          </div>
        @else
          {{ __('Your location') . ': -, -' }}
        @endif
        <div class="my-6 h-72 w-full md:h-96" id="currentMap" wire:ignore></div>
      </h4>
      <div class="grid grid-cols-2 gap-3 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3">
        <div
          class="{{ $attendance?->status == 'late' ? 'bg-red-200 dark:bg-red-900' : 'bg-blue-200 dark:bg-blue-900' }} flex items-center justify-between rounded-md px-4 py-2 text-gray-800 dark:text-white dark:shadow-gray-700">
          <div>
            <h4 class="text-lg font-semibold md:text-xl">Absen Masuk</h4>
            <div class="flex flex-col sm:flex-row">
              <span>
                @if ($isAbsence)
                  {{ __($attendance?->status) ?? '-' }}
                @else
                  {{ $attendance?->time_in ? Carbon::parse($attendance?->time_in)->format('H:i:s') : 'Belum Absen' }}
                @endif
              </span>
              @if ($attendance?->status == 'late')
                <span class="mx-1 hidden sm:inline-block">|</span>
              @endif
              <span>{{ $attendance?->status == 'late' ? 'Terlambat: Ya' : '' }}</span>
            </div>
          </div>
          <x-heroicon-o-arrows-pointing-in class="h-5 w-5" />
        </div>
        <div
          class="flex items-center justify-between rounded-md bg-orange-200 px-4 py-2 text-gray-800 dark:bg-orange-900 dark:text-white dark:shadow-gray-700">
          <div>
            <h4 class="text-lg font-semibold md:text-xl">Absen Keluar</h4>
            @if ($isAbsence)
              {{ __($attendance?->status) ?? '-' }}
            @else
              {{ $attendance?->time_out ? Carbon::parse($attendance?->time_out)->format('H:i:s') : 'Belum Absen' }}
            @endif
          </div>
          <x-heroicon-o-arrows-pointing-out class="h-5 w-5" />
        </div>
        <button
          class="col-span-2 flex items-center justify-between rounded-md bg-purple-200 px-4 py-2 text-gray-800 dark:bg-purple-900 dark:text-white dark:shadow-gray-700 md:col-span-1 lg:col-span-2 xl:col-span-1"
          {{ is_null($attendance?->lat_lng) ? 'disabled' : 'onclick=toggleMap()' }} id="toggleMap">
          <div>
            <h4 class="text-lg font-semibold md:text-xl">Koordinat Absen</h4>
            @if (is_null($attendance?->lat_lng))
              Belum Absen
            @else
              <a href="{{ \App\Helpers::getGoogleMapsUrl($attendance?->latitude, $attendance?->longitude) }}"
                target="_blank" class="underline hover:text-blue-400">
                {{ $attendance?->latitude . ', ' . $attendance?->longitude }}
              </a>
            @endif
          </div>
          <x-heroicon-o-map-pin class="h-6 w-6" />
        </button>
      </div>

      <div class="my-6 h-52 w-full md:h-64" id="map" wire:ignore></div>

      <hr class="my-4">

      <div class="grid grid-cols-2 gap-3 md:grid-cols-2 lg:grid-cols-3" wire:ignore>
        <a href="{{ route('apply-leave') }}">
          <div
            class="flex flex-col-reverse items-center justify-center gap-2 rounded-md bg-amber-500 px-4 py-2 text-center font-medium text-white shadow-md shadow-gray-400 transition duration-100 hover:bg-amber-600 dark:shadow-gray-700 md:flex-row md:gap-3">
            Ajukan Izin
            <x-heroicon-o-envelope-open class="h-6 w-6 text-white" />
          </div>
        </a>
        <a href="{{ route('attendance-history') }}">
          <div
            class="flex flex-col-reverse items-center justify-center gap-2 rounded-md bg-blue-500 px-4 py-2 text-center font-medium text-white shadow-md shadow-gray-400 hover:bg-blue-600 dark:shadow-gray-700 md:flex-row md:gap-3">
            Riwayat Absen
            <x-heroicon-o-clock class="h-6 w-6 text-white" />
          </div>
        </a>
      </div>
    </div>
  </div>
</div>

@script
  <script>
    const errorMsg = document.querySelector('#scanner-error');
    getLocation();

    async function getLocation() {
      if (navigator.geolocation) {
        const map = L.map('currentMap');
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 21,
        }).addTo(map);
        navigator.geolocation.watchPosition((position) => {
          console.log(position);
          $wire.$set('currentLiveCoords', [position.coords.latitude, position.coords.longitude]);
          map.setView([
            Number(position.coords.latitude),
            Number(position.coords.longitude),
          ], 13);
          L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
        }, (err) => {
          console.error(`ERROR(${err.code}): ${err.message}`);
          alert('{{ __('Please enable your location') }}');
        });
      } else {
        document.querySelector('#scanner-error').innerHTML = "Gagal mendeteksi lokasi";
      }
    }

    if (!$wire.isAbsence) {
      const scanner = new Html5Qrcode('scanner');

      const config = {
        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
        fps: 15,
        aspectRatio: 1,
        qrbox: {
          width: 280,
          height: 280
        },
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
      };

      async function startScanning() {
        if (scanner.getState() === Html5QrcodeScannerState.PAUSED) {
          return scanner.resume();
        }
        await scanner.start({
            facingMode: "environment"
          },
          config,
          onScanSuccess,
        );
      }

      async function onScanSuccess(decodedText, decodedResult) {
        console.log(`Code matched = ${decodedText}`, decodedResult);

        if (scanner.getState() === Html5QrcodeScannerState.SCANNING) {
          scanner.pause(true);
        }

        if (!(await checkTime())) {
          await startScanning();
          return;
        }

        const result = await $wire.scan(decodedText);

        if (result === true) {
          return onAttendanceSuccess();
        } else if (typeof result === 'string') {
          errorMsg.innerHTML = result;
        }

        setTimeout(async () => {
          await startScanning();
        }, 500);
      }

      async function checkTime() {
        const attendance = await $wire.getAttendance();

        if (attendance) {
          const timeIn = new Date(attendance.time_in).valueOf();
          const diff = (Date.now() - timeIn) / (1000 * 3600);
          const minAttendanceTime = 1;
          console.log(`Difference = ${diff}`);
          if (diff <= minAttendanceTime) {
            const timeIn = new Date(attendance.time_in).toLocaleTimeString([], {
              hour: 'numeric',
              minute: 'numeric',
              second: 'numeric',
              hour12: false,
            });
            const confirmation = confirm(
              `Anda baru saja absen pada ${timeIn}, apakah ingin melanjutkan untuk absen keluar?`
            );
            return confirmation;
          }
        }
        return true;
      }

      function onAttendanceSuccess() {
        scanner.stop();
        errorMsg.innerHTML = '';
        document.querySelector('#scanner-result').classList.remove('hidden');
      }

      const observer = new MutationObserver((mutationList, observer) => {
        const classes = ['text-white', 'bg-blue-500', 'dark:bg-blue-400', 'rounded-md', 'px-3', 'py-1'];
        for (const mutation of mutationList) {
          if (mutation.type === 'childList') {
            const startBtn = document.querySelector('#html5-qrcode-button-camera-start');
            const stopBtn = document.querySelector('#html5-qrcode-button-camera-stop');
            const fileBtn = document.querySelector('#html5-qrcode-button-file-selection');
            const permissionBtn = document.querySelector('#html5-qrcode-button-camera-permission');

            if (startBtn) {
              startBtn.classList.add(...classes);
              stopBtn.classList.add(...classes, 'bg-red-500');
              fileBtn.classList.add(...classes);
            }

            if (permissionBtn)
              permissionBtn.classList.add(...classes);
          }
        }
      });

      observer.observe(document.querySelector('#scanner'), {
        childList: true,
        subtree: true,
      });

      const shift = document.querySelector('#shift');
      const msg = 'Pilih shift terlebih dahulu';
      let isRendered = false;
      setTimeout(() => {
        if (!shift.value) {
          errorMsg.innerHTML = msg;
        } else {
          startScanning();
          isRendered = true;
        }
      }, 1000);
      shift.addEventListener('change', () => {
        if (!isRendered) {
          startScanning();
          isRendered = true;
          errorMsg.innerHTML = '';
        }
        if (!shift.value) {
          scanner.pause(true);
          errorMsg.innerHTML = msg;
        } else if (scanner.getState() === Html5QrcodeScannerState.PAUSED) {
          scanner.resume();
          errorMsg.innerHTML = '';
        }
      });

      const map = L.map('map').setView([
        Number({{ $attendance?->latitude }}),
        Number({{ $attendance?->longitude }}),
      ], 13);
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 21,
      }).addTo(map);
      L.marker([
        Number({{ $attendance?->latitude }}),
        Number({{ $attendance?->longitude }}),
      ]).addTo(map);
    }
  </script>
@endscript
