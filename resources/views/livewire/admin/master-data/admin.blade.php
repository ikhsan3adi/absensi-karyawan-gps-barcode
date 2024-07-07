<div>
  <div class="mb-4 flex-col items-center gap-5 sm:flex-row md:flex md:justify-between lg:mr-4">
    <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200 md:mb-0">
      Data Admin
    </h3>
    @if (Auth::user()->isSuperadmin)
      <x-button wire:click="showCreating">
        <x-heroicon-o-plus class="mr-2 h-4 w-4" /> Tambah Admin
      </x-button>
    @endif
  </div>
  <div class="overflow-x-scroll">
    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-900">
        <tr>
          <th scope="col" class="relative px-2 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300">
            No.
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('Name') }}
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('Email') }}
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('Group') }}
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('Phone Number') }}
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
        @foreach ($users as $user)
          @php
            $wireClick = "wire:click=show('$user->id')";
          @endphp
          <tr wire:key="{{ $user->id }}" class="group">
            <td class="{{ $class }} p-2 text-center text-sm font-medium text-gray-900 dark:text-white"
              {{ $wireClick }}>
              {{ $loop->iteration }}
            </td>
            <td class="{{ $class }} px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"
              {{ $wireClick }}>
              {{ $user->name }}
            </td>
            <td class="{{ $class }} px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"
              {{ $wireClick }}>
              {{ $user->email }}
            </td>
            <td class="{{ $class }} px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"
              {{ $wireClick }}>
              {{ $user->group }}
            </td>
            <td class="{{ $class }} px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"
              {{ $wireClick }}>
              {{ $user->phone }}
            </td>
            <td class="relative flex justify-center gap-2 px-6 py-4">
              @if (Auth::user()->isSuperadmin || Auth::user()->id == $user->id)
                <x-button wire:click="edit('{{ $user->id }}')">
                  Edit
                </x-button>
                @if (Auth::user()->isSuperadmin && $user->isUser)
                  <x-danger-button wire:click="confirmDeletion('{{ $user->id }}', '{{ $user->name }}')">
                    Delete
                  </x-danger-button>
                @endif
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="mt-3">
    {{ $users->links() }}
  </div>

  <x-confirmation-modal wire:model="confirmingDeletion">
    <x-slot name="title">
      Hapus Admin
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
      Admin Baru
    </x-slot>

    <form wire:submit="create">
      <x-slot name="content">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
          <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
            <!-- Profile Photo File Input -->
            <input type="file" id="photo" class="hidden" wire:model.live="form.photo" x-ref="photo"
              x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

            <x-label for="photo" value="{{ __('Photo') }}" />

            <!-- Current Profile Photo -->
            <div class="mt-2 h-20 w-20 rounded-full outline outline-gray-400" x-show="! photoPreview">
            </div>

            <!-- New Profile Photo Preview -->
            <div class="mt-2" x-show="photoPreview" style="display: none;">
              <span class="block h-20 w-20 rounded-full bg-cover bg-center bg-no-repeat"
                x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
              </span>
            </div>

            <x-secondary-button class="me-2 mt-2" type="button" x-on:click.prevent="$refs.photo.click()">
              {{ __('Select A New Photo') }}
            </x-secondary-button>

            @if ($form->user?->profile_photo_path)
              <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                {{ __('Remove Photo') }}
              </x-secondary-button>
            @endif

            @error('form.photo')
              <x-input-error for="form.photo" message="{{ $message }}" class="mt-2" />
            @enderror
          </div>
        @endif
        <div class="mt-4">
          <x-label for="name">Nama Admin</x-label>
          <x-input id="name" class="mt-1 block w-full" type="text" wire:model="form.name" />
          @error('form.name')
            <x-input-error for="form.name" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:gap-3">
          <div class="w-full">
            <x-label for="email">{{ __('Email') }}</x-label>
            <x-input id="email" class="mt-1 block w-full" type="email" wire:model="form.email"
              placeholder="example@example.com" required />
            @error('form.email')
              <x-input-error for="form.email" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
          <div class="w-full">
            <x-label for="nip">NIP</x-label>
            <x-input id="nip" class="mt-1 block w-full" type="text" wire:model="form.nip"
              placeholder="12345678" required />
            @error('form.nip')
              <x-input-error for="form.nip" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
        </div>
        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:gap-3">
          <div class="w-full">
            <x-label for="password">{{ __('Password') }}</x-label>
            <x-input id="password" class="mt-1 block w-full" type="password" wire:model="form.password"
              placeholder="New Password" required />
            <p class="text-sm dark:text-gray-400">Default password: <b>admin</b></p>
            @error('form.password')
              <x-input-error for="form.password" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
          <div class="w-full">
            <x-label for="form.group" value="{{ __('Group') }}" />
            <x-select id="form.group" class="mt-1 block w-full" wire:model="form.group" required>
              @foreach ($groups as $group)
                @if ($group != 'user')
                  <option value="{{ $group }}" {{ $group == $form->group ? 'selected' : '' }}>
                    {{ $group }}
                  </option>
                @endif
              @endforeach
            </x-select>
            @error('form.group')
              <x-input-error for="form.group" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
        </div>
        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:gap-3">
          <div class="w-full">
            <x-label for="phone">{{ __('Phone') }}</x-label>
            <x-input id="phone" class="mt-1 block w-full" type="number" wire:model="form.phone"
              placeholder="+628123456789" />
            @error('form.phone')
              <x-input-error for="form.phone" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
        </div>
        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:gap-3">
          <div class="w-full">
            <x-label for="city">{{ __('City') }}</x-label>
            <x-input id="city" class="mt-1 block w-full" type="text" wire:model="form.city"
              placeholder="Domisili" />
            @error('form.city')
              <x-input-error for="form.city" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
          <div class="w-full">
            <x-label for="address">{{ __('Address') }}</x-label>
            <x-input id="address" class="mt-1 block w-full" type="text" wire:model="form.address"
              placeholder="Jl. Jend. Sudirman" />
            @error('form.address')
              <x-input-error for="form.address" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
        </div>
        <div class="mt-4">
          <x-label for="form.division_id" value="{{ __('Division') }}" />
          <x-select id="form.division_id" class="mt-1 block w-full" wire:model="form.division_id">
            <option value="">{{ __('Select Division') }}</option>
            @foreach (App\Models\Division::all() as $division)
              <option value="{{ $division->id }}" {{ $division->id == $form->division_id ? 'selected' : '' }}>
                {{ $division->name }}
              </option>
            @endforeach
          </x-select>
          @error('form.division_id')
            <x-input-error for="form.division_id" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
        <div class="mt-4">
          <x-label for="form.job_title_id" value="{{ __('Job Title') }}" />
          <x-select id="form.job_title_id" class="mt-1 block w-full" wire:model="form.job_title_id">
            <option value="">{{ __('Select Job Title') }}</option>
            @foreach (App\Models\JobTitle::all() as $jobTitle)
              <option value="{{ $jobTitle->id }}" {{ $jobTitle->id == $form->job_title_id ? 'selected' : '' }}>
                {{ $jobTitle->name }}
              </option>
            @endforeach
          </x-select>
          @error('form.job_title_id')
            <x-input-error for="form.job_title_id" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('creating')" wire:loading.attr="disabled">
          {{ __('Cancel') }}
        </x-secondary-button>

        <x-button class="ml-2" wire:click="create" wire:loading.attr="disabled" wire:target="form.photo">
          {{ __('Confirm') }}
        </x-button>
      </x-slot>
    </form>
  </x-dialog-modal>

  <x-dialog-modal wire:model="editing">
    <x-slot name="title">
      Edit Admin
    </x-slot>

    <form wire:submit.prevent="update" id="user-edit">
      <x-slot name="content">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
          <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
            <!-- Profile Photo File Input -->
            <input type="file" id="photo" class="hidden" wire:model.live="form.photo" x-ref="photo"
              x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

            <x-label for="photo" value="{{ __('Photo') }}" />

            <!-- Current Profile Photo -->
            <div class="mt-2" x-show="! photoPreview">
              <img src="{{ $form->user?->profile_photo_url }}" alt="{{ $form->user?->name }}"
                class="h-20 w-20 rounded-full object-cover">
            </div>

            <!-- New Profile Photo Preview -->
            <div class="mt-2" x-show="photoPreview" style="display: none;">
              <span class="block h-20 w-20 rounded-full bg-cover bg-center bg-no-repeat"
                x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
              </span>
            </div>

            <x-secondary-button class="me-2 mt-2" type="button" x-on:click.prevent="$refs.photo.click()">
              {{ __('Select A New Photo') }}
            </x-secondary-button>

            @if ($form->user?->profile_photo_path)
              <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                {{ __('Remove Photo') }}
              </x-secondary-button>
            @endif

            @error('form.photo')
              <x-input-error for="form.photo" message="{{ $message }}" class="mt-2" />
            @enderror
          </div>
        @endif
        <div class="mt-4">
          <x-label for="name">Nama Admin</x-label>
          <x-input id="name" class="mt-1 block w-full" type="text" wire:model="form.name" />
          @error('form.name')
            <x-input-error for="form.name" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:gap-3">
          <div class="w-full">
            <x-label for="email">{{ __('Email') }}</x-label>
            <x-input id="email" class="mt-1 block w-full" type="email" wire:model="form.email"
              placeholder="example@example.com" required />
            @error('form.email')
              <x-input-error for="form.email" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
          <div class="w-full">
            <x-label for="nip">NIP</x-label>
            <x-input id="nip" class="mt-1 block w-full" type="text" wire:model="form.nip"
              placeholder="12345678" required />
            @error('form.nip')
              <x-input-error for="form.nip" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
        </div>
        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:gap-3">
          <div class="w-full">
            <x-label for="password">{{ __('Password') }}</x-label>
            <x-input id="password" class="mt-1 block w-full" type="password" wire:model="form.password"
              placeholder="New Password" />
            @error('form.password')
              <x-input-error for="form.password" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
        </div>
        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:gap-3">
          <div class="w-full">
            <x-label for="phone">{{ __('Phone') }}</x-label>
            <x-input id="phone" class="mt-1 block w-full" type="text" wire:model="form.phone"
              placeholder="+628123456789" />
            @error('form.phone')
              <x-input-error for="form.phone" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
        </div>
        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:gap-3">
          <div class="w-full">
            <x-label for="city">{{ __('City') }}</x-label>
            <x-input id="city" class="mt-1 block w-full" type="text" wire:model="form.city"
              placeholder="Domisili" />
            @error('form.city')
              <x-input-error for="form.city" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
          <div class="w-full">
            <x-label for="address">{{ __('Address') }}</x-label>
            <x-input id="address" class="mt-1 block w-full" type="text" wire:model="form.address"
              placeholder="Jl. Jend. Sudirman" />
            @error('form.address')
              <x-input-error for="form.address" class="mt-2" message="{{ $message }}" />
            @enderror
          </div>
        </div>
        <div class="mt-4">
          <x-label for="form.division_id" value="{{ __('Division') }}" />
          <x-select id="form.division_id" class="mt-1 block w-full" wire:model="form.division_id">
            <option value="">{{ __('Select Division') }}</option>
            @foreach (App\Models\Division::all() as $division)
              <option value="{{ $division->id }}" {{ $division->id == $form->division_id ? 'selected' : '' }}>
                {{ $division->name }}
              </option>
            @endforeach
          </x-select>
          @error('form.division_id')
            <x-input-error for="form.division_id" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
        <div class="mt-4">
          <x-label for="form.job_title_id" value="{{ __('Job Title') }}" />
          <x-select id="form.job_title_id" class="mt-1 block w-full" wire:model="form.job_title_id">
            <option value="">{{ __('Select Job Title') }}</option>
            @foreach (App\Models\JobTitle::all() as $jobTitle)
              <option value="{{ $jobTitle->id }}" {{ $jobTitle->id == $form->job_title_id ? 'selected' : '' }}>
                {{ $jobTitle->name }}
              </option>
            @endforeach
          </x-select>
          @error('form.job_title_id')
            <x-input-error for="form.job_title_id" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('editing')" wire:loading.attr="disabled">
          {{ __('Cancel') }}
        </x-secondary-button>

        <x-button class="ml-2" wire:click="update" wire:loading.attr="disabled" wire:target="form.photo">
          {{ __('Confirm') }}
        </x-button>
      </x-slot>
    </form>
  </x-dialog-modal>

  <x-modal wire:model="showDetail">
    @if ($form->user)
      @php
        $division = $form->user->division ? json_decode($form->user->division)->name : '-';
        $jobTitle = $form->user->jobTitle ? json_decode($form->user->jobTitle)->name : '-';
        $education = $form->user->education ? json_decode($form->user->education)->name : '-';
      @endphp
      <div class="px-6 py-4">
        <div class="my-4 flex items-center justify-center">
          <img class="h-32 w-32 rounded-full object-cover" src="{{ $form->user->profile_photo_url }}"
            alt="{{ $form->user->name }}" title="{{ $form->user->name }}" />
        </div>

        <div class="text-center text-lg font-medium text-gray-900 dark:text-gray-100">
          {{ $form->user->name }}
        </div>

        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
          <div class="mt-4">
            <x-label for="nip" value="NIP" />
            <p>{{ $form->user->nip }}</p>
          </div>
          <div class="mt-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <p>{{ $form->user->email }}</p>
          </div>
          <div class="mt-4">
            <x-label for="phone" value="{{ __('Phone') }}" />
            <p>{{ $form->user->phone }}</p>
          </div>
          <div class="mt-4">
            <x-label for="group" value="{{ __('Group') }}" />
            <p>{{ __($form->user->group) }}</p>
          </div>
          <div class="mt-4">
            <x-label for="birth_date" value="{{ __('Birth Date') }}" />
            @if ($form->user->birth_date)
              <p>{{ \Illuminate\Support\Carbon::parse($form->user->birth_date)->format('D d M Y') }}</p>
            @else
              <p>-</p>
            @endif
          </div>
          <div class="mt-4">
            <x-label for="birth_place" value="{{ __('Birth Place') }}" />
            <p>{{ $form->user->birth_place ?? '-' }}</p>
          </div>
          <div class="mt-4">
            <x-label for="address" value="{{ __('Address') }}" />
            @if (empty($form->user->address))
              <p>-</p>
            @else
              <p>{{ $form->user->address }}</p>
            @endif
          </div>
          <div class="mt-4">
            <x-label for="city" value="{{ __('City') }}" />
            @if (empty($form->user->city))
              <p>-</p>
            @else
              <p>{{ $form->user->city }}</p>
            @endif
          </div>
          <div class="mt-4">
            <x-label for="job_title_id" value="{{ __('Job Title') }}" />
            <p>{{ $jobTitle }}</p>
          </div>
          <div class="mt-4">
            <x-label for="division_id" value="{{ __('Division') }}" />
            <p>{{ $division }}</p>
          </div>
          {{-- <div class="mt-4">
            <x-label for="education_id" value="{{ __('Last Education') }}" />
            <p>{{ $education }}</p>
          </div> --}}
        </div>
      </div>
    @endif
  </x-modal>
</div>
