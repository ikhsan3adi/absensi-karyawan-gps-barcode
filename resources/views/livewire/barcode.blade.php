 <div class="p-6 lg:p-8">
   <x-button class="mb-4 mr-2" href="{{ route('admin.barcodes.create') }}">
     Buat Barcode Baru
   </x-button>
   <x-secondary-button class="mb-4">
     <a href="{{ route('admin.barcodes.downloadall') }}">Download Semua</a>
   </x-secondary-button>
   <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
     @foreach ($barcodes as $barcode)
       <div
         class="pointer-events-none flex flex-col rounded-lg bg-white p-4 shadow hover:bg-gray-100 dark:bg-gray-800 dark:shadow-gray-600 hover:dark:bg-gray-700">

         <div class="pointer-events-auto mt-4 flex items-center justify-center gap-2">
           <x-secondary-button href="{{ route('admin.barcodes.download', $barcode->id) }}">
             Download
           </x-secondary-button>
           <x-button href="{{ route('admin.barcodes.edit', $barcode->id) }}">
             Edit
           </x-button>
           <x-danger-button wire:click="confirmDeletion({{ $barcode->id }}, '{{ $barcode->name }}')">
             Delete
           </x-danger-button>
         </div>
         <a href="{{ route('admin.barcodes.show', $barcode->id) }}" class="pointer-events-auto">
           <div class="container flex items-center justify-center p-4">
             <div class="children:dark:text-gray-100 text-center dark:bg-gray-300">
               {{-- <img src="{{ base64_decode($barcode->barcode) }}" alt=""> --}}
               {{-- {!! $barcode->barcode !!} --}}
               {{-- <img src="{{ $barcode->barcode }}" alt="QR Code" /> --}}
             </div>
           </div>
         </a>
         <a href="{{ route('admin.barcodes.show', $barcode->id) }}" class="pointer-events-auto">
           <h3 class="mb-3 text-center text-lg font-semibold leading-tight text-gray-800 dark:text-white">
             {{ $barcode->name }}
           </h3>
         </a>
         <ul class="list-disc pl-4 dark:text-gray-400">
           <li> {{ __('Attendance Time Limit') }}: {{ $barcode->time_limit }}</li>
           {{-- <li> {{ __('Time In Valid From') }}: {{ $barcode->time_in_valid_from }}</li> --}}
           {{-- <li> {{ __('Time In Valid Until') }}: {{ $barcode->time_in_valid_until }}</li>
           <li> {{ __('Time Out Valid From') }}: {{ $barcode->time_out_valid_from }}</li>
           <li> {{ __('Time Out Valid Until') }}: {{ $barcode->time_out_valid_until }}</li> --}}
           <li> {{ __('Coords') . ': ' . $barcode->lat_lng['lat'] . ', ' . $barcode->latLng['lng'] }}</li>
           <li> {{ __('Radius (meter)') }}: {{ $barcode->radius }}</li>
         </ul>
       </div>
     @endforeach
   </div>

   <x-confirmation-modal wire:model="confirmingDeletion">
     <x-slot name="title">
       Hapus Barcode
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
 </div>
