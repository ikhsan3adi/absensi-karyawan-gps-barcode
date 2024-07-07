<nav x-data="{ open: false }" class="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800">
  <!-- Primary Navigation Menu -->
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex h-16 justify-between">
      <div class="flex">
        <!-- Logo -->
        <div class="flex shrink-0 items-center">
          <a href="{{ Auth::user()->isAdmin ? route('admin.dashboard') : route('home') }}">
            <x-application-mark class="block h-9 w-auto" />
          </a>
        </div>

        <!-- Navigation Links -->
        <div class="hidden space-x-2 sm:-my-px sm:ms-6 sm:flex md:ms-10 md:space-x-5 lg:space-x-8">
          @if (Auth::user()->isAdmin)
            <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
              {{ __('Dashboard') }}
            </x-nav-link>
            <x-nav-link href="{{ route('admin.barcodes') }}" :active="request()->routeIs('admin.barcodes')">
              {{ __('Barcode') }}
            </x-nav-link>
            <x-nav-link class="hidden md:inline-flex" href="{{ route('admin.attendances') }}" :active="request()->routeIs('admin.attendances')">
              {{ __('Attendance') }}
            </x-nav-link>
            <x-nav-link class="hidden md:inline-flex" href="{{ route('admin.employees') }}" :active="request()->routeIs('admin.employees')">
              {{ __('Employee') }}
            </x-nav-link>
            <x-nav-dropdown :active="request()->routeIs('admin.masters.*')" triggerClasses="text-nowrap">
              <x-slot name="trigger">
                {{ __('Master Data') }}
                <x-heroicon-o-chevron-down class="ms-2 h-5 w-5 text-gray-400" />
              </x-slot>
              <x-slot name="content">
                <x-dropdown-link class="md:hidden" href="{{ route('admin.attendances') }}" :active="request()->routeIs('admin.attendances')">
                  {{ __('Attendance') }}
                </x-dropdown-link>
                <x-dropdown-link class="md:hidden" href="{{ route('admin.employees') }}" :active="request()->routeIs('admin.employees')">
                  {{ __('Employee') }}
                </x-dropdown-link>
                <x-dropdown-link href="{{ route('admin.masters.division') }}" :active="request()->routeIs('admin.masters.division')">
                  {{ __('Division') }}
                </x-dropdown-link>
                <x-dropdown-link href="{{ route('admin.masters.job-title') }}" :active="request()->routeIs('admin.masters.job-title')">
                  {{ __('Job Title') }}
                </x-dropdown-link>
                <x-dropdown-link href="{{ route('admin.masters.education') }}" :active="request()->routeIs('admin.masters.education')">
                  {{ __('Education') }}
                </x-dropdown-link>
                <x-dropdown-link href="{{ route('admin.masters.shift') }}" :active="request()->routeIs('admin.masters.shift')">
                  {{ __('Shift') }}
                </x-dropdown-link>
                <hr>
                <x-dropdown-link href="{{ route('admin.masters.admin') }}" :active="request()->routeIs('admin.masters.admin')">
                  {{ __('Admin') }}
                </x-dropdown-link>
              </x-slot>
            </x-nav-dropdown>
            <x-nav-dropdown :active="request()->routeIs('admin.import-export.*')" triggerClasses="text-nowrap">
              <x-slot name="trigger">
                {{ __('Import & Export') }}
                <x-heroicon-o-chevron-down class="ms-2 h-5 w-5 text-gray-400" />
              </x-slot>
              <x-slot name="content">
                <x-dropdown-link href="{{ route('admin.import-export.users') }}" :active="request()->routeIs('admin.import-export.users')">
                  {{ __('Employee') }}/{{ __('Admin') }}
                </x-dropdown-link>
                <x-dropdown-link href="{{ route('admin.import-export.attendances') }}" :active="request()->routeIs('admin.import-export.attendances')">
                  {{ __('Attendance') }}
                </x-dropdown-link>
              </x-slot>
            </x-nav-dropdown>
          @else
            <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
              {{ __('Home') }}
            </x-nav-link>
          @endif
        </div>
      </div>

      <div class="flex gap-2">
        <div class="hidden sm:ms-6 sm:flex sm:items-center">
          <x-theme-toggle />

          <!-- Settings Dropdown -->
          <div class="relative ms-3">
            <x-dropdown align="right" width="48">
              <x-slot name="trigger">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                  <button
                    class="flex rounded-full border-2 border-transparent text-sm transition focus:border-gray-300 focus:outline-none">
                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                      alt="{{ Auth::user()->name }}" />
                  </button>
                @else
                  <span class="inline-flex rounded-md">
                    <button type="button"
                      class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:bg-gray-50 focus:outline-none active:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:bg-gray-700 dark:active:bg-gray-700">
                      {{ Auth::user()->name }}

                      <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                      </svg>
                    </button>
                  </span>
                @endif
              </x-slot>

              <x-slot name="content">
                <!-- Account Management -->
                <div class="block px-4 py-2 text-xs text-gray-400">
                  {{ __('Manage Account') }}
                </div>

                <x-dropdown-link href="{{ route('profile.show') }}">
                  {{ __('Profile') }}
                </x-dropdown-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                  <x-dropdown-link href="{{ route('api-tokens.index') }}">
                    {{ __('API Tokens') }}
                  </x-dropdown-link>
                @endif

                <div class="border-t border-gray-200 dark:border-gray-600"></div>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                  @csrf

                  <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                    {{ __('Log Out') }}
                  </x-dropdown-link>
                </form>
              </x-slot>
            </x-dropdown>
          </div>
        </div>

        <x-theme-toggle class="sm:hidden" />

        <!-- Hamburger -->
        <div class="-me-2 flex items-center sm:hidden">
          <button @click="open = ! open"
            class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400 dark:focus:bg-gray-900 dark:focus:text-gray-400">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
              <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Responsive Navigation Menu -->
  <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
    <div class="space-y-1 pb-3 pt-2">
      @if (Auth::user()->isAdmin)
        <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
          {{ __('Dashboard') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('admin.barcodes') }}" :active="request()->routeIs('admin.barcodes')">
          {{ __('Barcode') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('admin.attendances') }}" :active="request()->routeIs('admin.attendances')">
          {{ __('Attendance') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('admin.employees') }}" :active="request()->routeIs('admin.employees')">
          {{ __('Employee') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('admin.masters.division') }}" :active="request()->routeIs('admin.masters.division')">
          {{ __('Division') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('admin.masters.job-title') }}" :active="request()->routeIs('admin.masters.job-title')">
          {{ __('Job Title') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('admin.masters.education') }}" :active="request()->routeIs('admin.masters.education')">
          {{ __('Education') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('admin.masters.shift') }}" :active="request()->routeIs('admin.masters.shift')">
          {{ __('Shift') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('admin.masters.admin') }}" :active="request()->routeIs('admin.masters.admin')">
          {{ __('Admin Management') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('admin.import-export.users') }}" :active="request()->routeIs('admin.import-export')">
          Import & Export Karyawan/Admin
        </x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('admin.import-export.attendances') }}" :active="request()->routeIs('admin.import-export')">
          Import & Export Absensi
        </x-responsive-nav-link>
      @else
        <x-responsive-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
          {{ __('Home') }}
        </x-responsive-nav-link>
      @endif
    </div>

    <!-- Responsive Settings Options -->
    <div class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-600">
      <div class="flex items-center px-4">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
          <div class="me-3 shrink-0">
            <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
              alt="{{ Auth::user()->name }}" />
          </div>
        @endif

        <div>
          <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
          <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
        </div>
      </div>

      <div class="mt-3 space-y-1">
        <!-- Account Management -->
        <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
          {{ __('Profile') }}
        </x-responsive-nav-link>

        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
          <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
            {{ __('API Tokens') }}
          </x-responsive-nav-link>
        @endif

        <!-- Authentication -->
        <form method="POST" action="{{ route('logout') }}" x-data>
          @csrf

          <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
            {{ __('Log Out') }}
          </x-responsive-nav-link>
        </form>
      </div>
    </div>
  </div>
</nav>
