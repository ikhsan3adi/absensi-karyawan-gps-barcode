<div class="w-full">
  @php
    use Illuminate\Support\Carbon;
  @endphp
  <script src="{{ url('/assets/js/html5-qrcode.min.js') }}"></script>

  <div class="flex flex-col gap-4 md:flex-row">
    <div class="flex flex-col gap-4">
      <div>
        {{-- <x-label for="shift" value="{{ $shift_id ? __('Shift') : __('Select Shift') }}" /> --}}
        <x-select id="shift" class="mt-1 block w-full" wire:model="shift_id" disabled="{{ $shift_id ? true : false }}">
          <option value="">{{ __('Select Shift') }}</option>
          @foreach (App\Models\Shift::all() as $shift)
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
    <div class="w-full">
      <h4 id="scanner-error" class="mb-3 text-lg font-semibold text-red-500 dark:text-red-400 sm:text-xl" wire:ignore>
      </h4>
      <h4 id="scanner-result" class="mb-3 hidden text-lg font-semibold text-green-500 dark:text-green-400 sm:text-xl">
        {{ $successMsg }}
      </h4>
      <h4 id="latlng" class="mb-3 text-lg font-semibold text-gray-600 dark:text-gray-100 sm:text-xl">
        {{ __('Date') . ': ' . now()->format('d/m/Y') }}<br>
        {{ __('Your location') . ': ' . (is_null($currentLiveCoords) ? '-, -' : $currentLiveCoords[0] . ', ' . $currentLiveCoords[1]) }}
      </h4>
      <div class="grid grid-cols-2 gap-3 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3">
        <div
          class="{{ $attendance?->status == 'late' ? 'bg-red-500' : 'bg-blue-500' }} flex items-center justify-between rounded-md px-4 py-2 text-white dark:shadow-gray-700">
          <div>
            <h4 class="text-lg font-semibold md:text-xl">Absen Masuk</h4>
            <div class="flex flex-col sm:flex-row">
              <span>{{ $attendance?->time_in ? Carbon::parse($attendance?->time_in)->format('H:i:s') : 'Belum Absen' }}</span>
              @if ($attendance?->status == 'late')
                <span class="mx-1 hidden sm:inline-block">|</span>
              @endif
              <span>{{ $attendance?->status == 'late' ? 'Terlambat: Ya' : '' }}</span>
            </div>
          </div>
          <x-heroicon-o-arrows-pointing-in class="h-5 w-5"></x-heroicon-o-arrows-pointing-in>
        </div>
        <div
          class="flex items-center justify-between rounded-md bg-orange-500 px-4 py-2 text-white dark:shadow-gray-700">
          <div>
            <h4 class="text-lg font-semibold md:text-xl">Absen Keluar</h4>
            {{ $attendance?->time_out ? Carbon::parse($attendance?->time_out)->format('H:i:s') : 'Belum Absen' }}
          </div>
          <x-heroicon-o-arrows-pointing-out class="h-5 w-5"></x-heroicon-o-arrows-pointing-out>
        </div>
        <div
          class="col-span-2 flex items-center justify-between rounded-md bg-purple-500 px-4 py-2 text-white dark:shadow-gray-700 md:col-span-1 lg:col-span-2 xl:col-span-1">
          <div>
            <h4 class="text-lg font-semibold md:text-xl">Koordinat Absen</h4>
            {{ is_null($attendance?->coordinates) ? 'Belum Absen' : $attendance?->lat_lng['lat'] . ', ' . $attendance?->lat_lng['lng'] }}
          </div>
          <x-heroicon-o-map-pin class="h-6 w-6"></x-heroicon-o-map-pin>
        </div>
      </div>

      <hr class="my-4">

      <div class="grid grid-cols-2 gap-3 md:grid-cols-2 lg:grid-cols-3" wire:ignore>
        <a href="{{ route('apply-leave') }}" target="_blank">
          <div
            class="flex flex-col-reverse items-center justify-center gap-2 rounded-md bg-amber-500 px-4 py-2 text-center font-medium text-white shadow-md shadow-gray-400 transition duration-100 hover:bg-amber-600 dark:shadow-gray-700 md:flex-row md:gap-3">
            Ajukan Izin
            <x-heroicon-o-envelope-open class="h-6 w-6 text-white"></x-heroicon-o-envelope-open>
          </div>
        </a>
        <a href="{{ route('attendance-history') }}" target="_blank">
          <div
            class="flex flex-col-reverse items-center justify-center gap-2 rounded-md bg-blue-500 px-4 py-2 text-center font-medium text-white shadow-md shadow-gray-400 hover:bg-blue-600 dark:shadow-gray-700 md:flex-row md:gap-3">
            Riwayat Absen
            <x-heroicon-o-clock class="h-6 w-6 text-white"></x-heroicon-o-clock>
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
        navigator.geolocation.watchPosition((position) => {
          console.log(position);
          $wire.$set('currentLiveCoords', [position.coords.latitude, position.coords.longitude]);
        });
      } else {
        document.querySelector('#scanner-error').innerHTML = "Gagal mendeteksi lokasi";
      }
    }

    const scanner = new Html5QrcodeScanner(
      'scanner', {
        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
        fps: 15,
        aspectRatio: 1,
        qrbox: {
          width: 280,
          height: 280
        },
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
      }
    );

    async function onScanSuccess(decodedText, decodedResult) {
      console.log(`Code matched = ${decodedText}`, decodedResult);

      if (scanner.getState() === Html5QrcodeScannerState.SCANNING) {
        scanner.pause(true);
      }

      await scanner.clear();
      if (!(await checkTime())) {
        scanner.render(onScanSuccess);
        return;
      }

      const result = await $wire.scan(decodedText);

      if (result === true) {
        return onAttendanceSuccess();
      } else if (typeof result === 'string') {
        errorMsg.innerHTML = result;
      }

      scanner.render(onScanSuccess);
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
        scanner.render(onScanSuccess);
        isRendered = true;
      }
    }, 1000);
    shift.addEventListener('change', () => {
      if (!isRendered) {
        scanner.render(onScanSuccess);
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
  </script>
@endscript
