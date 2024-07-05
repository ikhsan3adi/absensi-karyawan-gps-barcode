<div>
  <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:gap-6">
    @if ($mode != 'import')
      <div>
        <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
          Ekspor Data Karyawan
        </h3>
        <div class="flex flex-col items-center justify-stretch gap-4">
          @if ($mode == 'export')
            <x-secondary-button wire:click="preview" class="w-full justify-center">
              {{ __('Cancel') }}
            </x-secondary-button>
          @else
            <x-secondary-button wire:click="preview" class="w-full justify-center">
              {{ __('Preview') }}
            </x-secondary-button>
          @endif
          <x-button wire:click="export" class="w-full justify-center">
            {{ $mode == 'export' ? __('Confirm & Export') : __('Export') }}
          </x-button>
        </div>
      </div>
    @endif
    @if ($mode != 'export')
      <div>
        <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
          Impor Data Karyawan
        </h3>
        <form x-data="{ file: null }" method="post" wire:submit.prevent="import" enctype="multipart/form-data">
          @csrf
          <div class="mb-4 flex items-center gap-3">
            <x-secondary-button class="me-2" type="button" x-on:click.prevent="$refs.file.click()"
              x-text="file ? 'Ganti File' : 'Pilih File dan Pratinjau'">
              Pilih File
            </x-secondary-button>
            <x-secondary-button class="me-2" type="button" x-show="file"
              x-on:click.prevent="$refs.file.files[0] = null; file = null; $wire.$set('file', null)">
              Hapus File
            </x-secondary-button>
            <h5 class="text-sm dark:text-gray-200" x-text="file ? file.name : 'File Belum Dipilih'"></h5>
            <x-input type="file" class="hidden" name="file" x-ref="file"
              x-on:change="file = $refs.file.files[0]" wire:model.live="file" />
          </div>
          <div class="flex items-center justify-stretch">
            <x-danger-button class="w-full"
              x-text="file ? '{{ __('Confirm & Import') }} ' + file.name : '{{ __('Import') }}'">
            </x-danger-button>
          </div>
        </form>
      </div>
    @endif
  </div>
  @if ($mode && $previewing)
    <h3 class="mt-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
      {{ __('Preview') . ' ' . $mode }}
    </h3>
    <div class="mt-4 w-full overflow-x-scroll text-sm">
      @php
        $trClass = 'divide-x divide-gray-200 dark:divide-gray-700';
        $thClass = 'px-4 py-3 text-left font-semibold dark:text-white';
        $tdClass = 'px-4 py-4 text-sm font-medium text-gray-900 dark:text-white';
      @endphp
      <table class="w-full divide-y divide-gray-200 border dark:divide-gray-700 dark:border-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900">
          <tr class="{{ $trClass }}">
            <th scope="col" class="px-2 py-3 text-left font-semibold dark:text-white">
              No
            </th>
            <th scope="col" class="{{ $thClass }}">
              NIP
            </th>
            <th scope="col" class="{{ $thClass }}">
              Name
            </th>
            <th scope="col" class="{{ $thClass }}">
              Email
            </th>
            <th scope="col" class="{{ $thClass }}">
              Phone
            </th>
            <th scope="col" class="{{ $thClass }}">
              Gender
            </th>
            <th scope="col" class="{{ $thClass }}">
              Birth Date
            </th>
            <th scope="col" class="{{ $thClass }}">
              Birth Place
            </th>
            <th scope="col" class="{{ $thClass }}">
              Address
            </th>
            <th scope="col" class="{{ $thClass }}">
              City
            </th>
            <th scope="col" class="{{ $thClass }}">
              Education
            </th>
            <th scope="col" class="{{ $thClass }}">
              Division
            </th>
            <th scope="col" class="{{ $thClass }}">
              Job Title
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
          @foreach ($users as $user)
            <tr class="{{ $trClass }}">
              <td class="px-2 py-4 text-center text-sm font-medium text-gray-900 dark:text-white">
                {{ $loop->iteration }}
              </td>
              <td class="{{ $tdClass }}">
                {{ $user->nip }}
              </td>
              <td class="{{ $tdClass }}">
                {{ $user->name }}
              </td>
              <td class="{{ $tdClass }}">
                {{ $user->email }}
              </td>
              <td class="{{ $tdClass }}">
                <div class="w-32">{{ $user->phone }}</div>
              </td>
              <td class="{{ $tdClass }}">
                {{ $user->gender }}
              </td>
              <td class="{{ $tdClass }} text-nowrap">
                {{ $user->birth_date?->format('Y-m-d') }}
              </td>
              <td class="{{ $tdClass }}">
                {{ Str::limit($user->birth_place, 20, '...') }}
              </td>
              <td class="{{ $tdClass }}">
                <div class="w-48">{{ Str::limit($user->address, 90, '...') }}</div>
              </td>
              <td class="{{ $tdClass }}">{{ $user->city }}</td>
              <td class="{{ $tdClass }} text-nowrap">
                {{ $user->education?->name }}
              </td>
              <td class="{{ $tdClass }} text-nowrap">
                {{ $user->division?->name }}
              </td>
              <td class="{{ $tdClass }} text-nowrap">
                {{ $user->jobTitle?->name }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
