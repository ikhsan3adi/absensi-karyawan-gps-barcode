<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
      {{ __('Import & Export') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
        <div class="p-6 lg:p-8">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
            <div>
              <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Impor Data Karyawan
              </h3>
              <form x-data="{ file: null }" action="{{ route('admin.users.import') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="flex gap-3 items-center mb-4">
                  <x-secondary-button class="me-2 mt-2" type="button" x-on:click.prevent="$refs.file.click()" x-text="file ? 'Ganti File' : 'Pilih File'">
                    Pilih File
                  </x-secondary-button>
                  <h5 class="mt-3 text-sm dark:text-gray-200" x-text="file ? file.name : 'File Belum Dipilih'"></h5>
                  <x-input type="file" class="hidden" name="file" x-ref="file" x-on:change="file = $refs.file.files[0]" />
                </div>
                <div class="flex items-center justify-stretch">
                  <x-danger-button class="w-full" x-text="file ? '{{ __('Import') }} ' + file.name : '{{ __('Import') }}'">
                  </x-danger-button>
                </div>
              </form>
              <hr class="my-4">
              <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Ekspor Data Karyawan
              </h3>
              <div class="flex items-center justify-stretch">
                <x-button href="{{ route('admin.users.export') }}" class="w-full justify-center">{{__('Export')}}</x-button>
              </div>
            </div>
            <hr class="my-4 lg:hidden border-dashed border-gray-500 dark:border-white">
            <div>
              <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Impor Data Absensi
              </h3>
              <form x-data="{ file: null }" action="{{ route('admin.attendances.import') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="flex gap-3 items-center mb-4">
                  <x-secondary-button class="me-2 mt-2" type="button" x-on:click.prevent="$refs.file.click()" x-text="file ? 'Ganti File' : 'Pilih File'">
                    Pilih File
                  </x-secondary-button>
                  <h5 class="mt-3 text-sm dark:text-gray-200" x-text="file ? file.name : 'File Belum Dipilih'"></h5>
                  <x-input type="file" class="hidden" name="file" x-ref="file" x-on:change="file = $refs.file.files[0]" />
                </div>
                <div class="flex items-center justify-stretch">
                  <x-danger-button class="w-full" x-text="file ? '{{ __('Import') }} ' + file.name : '{{ __('Import') }}'">
                  </x-danger-button>
                </div>
              </form>
              <hr class="my-4">
              <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Ekspor Data Absensi
              </h3>
              <form action="{{ route('admin.attendances.export') }}" method="get">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center mb-4">
                  <x-label for="year" value="Per Tahun"></x-label>
                  <x-input type="number" min="1970" max="2099" value="{{ date('Y') }}" name="year" id="year" />
                </div>
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center mb-4">
                  <x-label for="month" value="Per Bulan"></x-label>
                  <x-input type="month" name="month" id="month" />
                </div>
                <x-select id="division" name="division" class="mb-4">
                  <option value="">{{ __('Select Division') }}</option>
                  @foreach (App\Models\Division::all() as $division)
                  <option value="{{ $division->id }}">
                    {{ $division->name }}
                  </option>
                  @endforeach
                </x-select>
                <x-select id="jobTitle" name="job_title" class="mb-4">
                  <option value="">{{ __('Select Job Title') }}</option>
                  @foreach (App\Models\JobTitle::all() as $jobTitle)
                  <option value="{{ $jobTitle->id }}">
                    {{ $jobTitle->name }}
                  </option>
                  @endforeach
                </x-select>
                <x-select id="education" name="education" class="mb-4">
                  <option value="">{{ __('Select Education') }}</option>
                  @foreach (App\Models\Education::all() as $education)
                  <option value="{{ $education->id }}">
                    {{ $education->name }}
                  </option>
                  @endforeach
                </x-select>
                <div class="flex items-center justify-stretch">
                  <x-button class="w-full justify-center">
                    {{__('Export')}}
                  </x-button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>