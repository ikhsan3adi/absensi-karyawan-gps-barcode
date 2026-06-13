<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
      Riwayat Pengajuan Izin
    </h2>
  </x-slot>

  <div x-data="{ open: false, selectedId: null, selectedData: {} }"
    @detail-leave.window="selectedId = $event.detail; open = true"
    class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
        <div class="p-6 lg:p-8">
          <div class="mb-4 flex items-center gap-3">
            <x-label for="filter" value="Filter Status"></x-label>
            <select id="filter" onchange="window.location.href = this.value"
              class="rounded-md border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
              <option value="{{ route('leave-history', ['filter' => 'all']) }}" {{ $currentFilter === 'all' ? 'selected' : '' }}>Semua</option>
              <option value="{{ route('leave-history', ['filter' => 'pending']) }}" {{ $currentFilter === 'pending' ? 'selected' : '' }}>Menunggu ({{ $pendingCount }})</option>
              <option value="{{ route('leave-history', ['filter' => 'approved']) }}" {{ $currentFilter === 'approved' ? 'selected' : '' }}>Disetujui</option>
              <option value="{{ route('leave-history', ['filter' => 'rejected']) }}" {{ $currentFilter === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
          </div>
          <div class="mb-4">
            <a href="{{ route('apply-leave') }}">
              <x-button>
                <x-heroicon-o-plus class="mr-2 h-4 w-4" />
                Ajukan Izin Baru
              </x-button>
            </a>
          </div>
          <div class="overflow-x-scroll">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Tipe</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Tanggal</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Keterangan</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Status</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Detail</th>
                </tr>
              </thead>
              @forelse ($leaveRequests as $req)
                <tbody>
                  <tr class="group">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                      {{ $req->type === 'excused' ? 'Izin' : 'Sakit' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-nowrap">
                      {{ $req->from_date->format('d/m/Y') }}
                      @if ($req->to_date != $req->from_date)
                        - {{ $req->to_date->format('d/m/Y') }}
                      @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                      {{ $req->note }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      @switch($req->status)
                        @case('pending')
                          <span class="font-medium text-amber-600 dark:text-amber-400">Menunggu</span>
                          @break
                        @case('approved')
                          <span class="font-medium text-emerald-600 dark:text-emerald-400">Disetujui</span>
                          @break
                        @case('rejected')
                          <span class="font-medium text-red-600 dark:text-red-400">Ditolak</span>
                          @break
                      @endswitch
                    </td>
                    <td class="px-4 py-3 text-sm">
                      <x-button @click="$dispatch('detail-leave', {{ $req->id }})">Lihat</x-button>
                    </td>
                  </tr>
                </tbody>
              @empty
                <tbody>
                  <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                      Belum ada pengajuan izin.
                    </td>
                  </tr>
                </tbody>
              @endforelse
            </table>
          </div>
          <div class="mt-3">
            {{ $leaveRequests->links() }}
          </div>
        </div>
      </div>
    </div>

    <div x-show="open"
      class="fixed inset-0 z-50 flex items-center justify-center"
      @keydown.escape.window="open = false">
      <div class="fixed inset-0 bg-gray-900/50" @click="open = false"></div>
      <div class="relative z-50 w-full max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Detail Pengajuan</h3>

        @foreach ($leaveRequests as $req)
          <div x-show="selectedId === {{ $req->id }}">
            <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
              <div>
                <span class="font-medium text-gray-900 dark:text-white">Tipe:</span>
                {{ $req->type === 'excused' ? 'Izin' : 'Sakit' }}
              </div>
              <div>
                <span class="font-medium text-gray-900 dark:text-white">Tanggal:</span>
                {{ $req->from_date->format('d/m/Y') }}
                @if ($req->to_date != $req->from_date)
                  - {{ $req->to_date->format('d/m/Y') }}
                @endif
              </div>
              <div>
                <span class="font-medium text-gray-900 dark:text-white">Keterangan:</span>
                <p class="mt-1">{{ $req->note }}</p>
              </div>
              <div>
                <span class="font-medium text-gray-900 dark:text-white">Status:</span>
                @switch($req->status)
                  @case('pending')
                    <span class="text-amber-600 dark:text-amber-400">Menunggu</span>
                    @break
                  @case('approved')
                    <span class="text-emerald-600 dark:text-emerald-400">Disetujui</span>
                    @break
                  @case('rejected')
                    <span class="text-red-600 dark:text-red-400">Ditolak</span>
                    @if ($req->rejection_reason)
                      <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Alasan: {{ $req->rejection_reason }}</p>
                    @endif
                    @break
                @endswitch
              </div>
              @if ($req->attachment)
                <div>
                  <span class="font-medium text-gray-900 dark:text-white">Lampiran:</span>
                  @php
                    $ext = pathinfo($req->attachment, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                  @endphp
                  @if ($isImage)
                    <div class="mt-1">
                      <img src="{{ Storage::url($req->attachment) }}" alt="Lampiran"
                        class="max-h-48 rounded object-contain">
                    </div>
                  @else
                    <a href="{{ Storage::url($req->attachment) }}" target="_blank"
                      class="ml-1 text-blue-500 hover:underline">Lihat</a>
                  @endif
                </div>
              @endif
              <div>
                <span class="font-medium text-gray-900 dark:text-white">Diajukan:</span>
                {{ $req->created_at->format('d/m/Y H:i') }}
              </div>
            </div>
          </div>
        @endforeach

        <div class="mt-6 flex justify-end">
          <button @click="open = false"
            class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
