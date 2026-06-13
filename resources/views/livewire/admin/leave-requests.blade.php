<div>
  <h3 class="col-span-2 mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
    Pengajuan Izin
  </h3>

  <div class="mb-4 flex gap-2">
    <x-secondary-button wire:click="$set('filter', 'pending')"
      class="{{ $filter === 'pending' ? 'bg-blue-500 text-white' : '' }}">
      Menunggu ({{ LeaveRequest::where('status', 'pending')->count() }})
    </x-secondary-button>
    <x-secondary-button wire:click="$set('filter', 'approved')"
      class="{{ $filter === 'approved' ? 'bg-green-500 text-white' : '' }}">
      Disetujui
    </x-secondary-button>
    <x-secondary-button wire:click="$set('filter', 'rejected')"
      class="{{ $filter === 'rejected' ? 'bg-red-500 text-white' : '' }}">
      Ditolak
    </x-secondary-button>
    <x-secondary-button wire:click="$set('filter', 'all')"
      class="{{ $filter === 'all' ? 'bg-gray-500 text-white' : '' }}">
      Semua
    </x-secondary-button>
  </div>

  <div class="overflow-x-scroll">
    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-900">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Karyawan</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Tipe</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Tanggal</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Keterangan</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Status</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Diajukan</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
        @forelse ($requests as $req)
          <tr wire:key="{{ $req->id }}" class="group">
            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
              {{ $req->user->name }}
              <span class="block text-xs text-gray-500">{{ $req->user->nip }}</span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
              {{ $req->type === 'excused' ? 'Izin' : 'Sakit' }}
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-nowrap">
              {{ $req->from_date->format('d/m/Y') }}
              @if ($req->to_date != $req->from_date)
                - {{ $req->to_date->format('d/m/Y') }}
              @endif
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white max-w-xs truncate">
              {{ $req->note }}
            </td>
            <td class="px-4 py-3 text-sm">
              @switch($req->status)
                @case('pending')
                  <span class="text-yellow-500">Menunggu</span>
                  @break
                @case('approved')
                  <span class="text-green-500">Disetujui</span>
                  @break
                @case('rejected')
                  <span class="text-red-500">Ditolak</span>
                  @if ($req->rejection_reason)
                    <span class="block text-xs text-gray-500">{{ $req->rejection_reason }}</span>
                  @endif
                  @break
              @endswitch
            </td>
            <td class="px-4 py-3 text-sm text-gray-500">
              {{ $req->created_at->format('d/m/Y H:i') }}
            </td>
            <td class="px-4 py-3 text-sm">
              @if ($req->status === 'pending')
                <x-button wire:click="approve({{ $req->id }})" class="bg-green-500 hover:bg-green-600">
                  Setujui
                </x-button>
                <x-secondary-button wire:click="confirmReject({{ $req->id }})" class="ml-1">
                  Tolak
                </x-secondary-button>
              @elseif ($req->status === 'rejected' && $req->rejection_reason)
                <span class="text-xs text-gray-500">{{ $req->rejection_reason }}</span>
              @elseif ($req->reviewer)
                <span class="text-xs text-gray-500">oleh {{ $req->reviewer->name }}</span>
              @endif
            </td>
          </tr>
          @if ($rejectingId === $req->id)
            <tr wire:key="reject-{{ $req->id }}">
              <td colspan="7" class="px-4 py-3 bg-gray-50 dark:bg-gray-900">
                <div class="flex items-center gap-2">
                  <x-input type="text" placeholder="Alasan penolakan..." wire:model="rejectReason"
                    class="flex-1" />
                  <x-button wire:click="reject" class="bg-red-500 hover:bg-red-600">
                    Konfirmasi Tolak
                  </x-button>
                  <x-secondary-button wire:click="cancelReject">
                    Batal
                  </x-secondary-button>
                </div>
                @error('rejectReason')
                  <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
              </td>
            </tr>
          @endif
        @empty
          <tr>
            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
              Tidak ada data.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $requests->links() }}
  </div>
</div>
