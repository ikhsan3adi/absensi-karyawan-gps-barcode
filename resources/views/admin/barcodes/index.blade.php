<x-app-layout>
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
      .leaflet-container {
        isolation: isolate;
      }
    </style>
  @endpushOnce

  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
      {{ __('Barcode') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
        @livewire('barcode-component')
      </div>
    </div>
  </div>

  @pushOnce('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
      integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
      window.addEventListener("load", function() {
        window.initializeMap({
          onUpdate: (lat, lng) => {
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
          }
        });
      });
    </script>
  @endPushOnce
</x-app-layout>
