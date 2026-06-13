<div>
  <h3 class="col-span-2 mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
    Pengajuan Izin
  </h3>

  <div class="mb-4 flex items-center gap-3">
    <x-label for="filter" value="Filter"></x-label>
    <x-select id="filter" wire:model.live="filter" class="w-48">
      <option value="pending">Menunggu ({{ $pendingCount }})</option>
      <option value="approved">Disetujui</option>
      <option value="rejected">Ditolak</option>
      <option value="all">Semua</option>
    </x-select>
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
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Aksi</th>
        </tr>
      </thead>
      @forelse ($requests as $req)
        <tbody>
          <tr wire:key="{{ $req->id }}" class="group">
            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
              {{ $req->user->name }}
              <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $req->user->nip }}</span>
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
            <td class="px-4 py-3 text-sm text-nowrap">
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
                    <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $req->rejection_reason }}</span>
                  @endif
                  @break
              @endswitch
            </td>
            <td class="px-4 py-3 text-nowrap">
              <div class="flex items-center gap-1">
                <x-button wire:click="viewDetail({{ $req->id }})" class="!px-3 !py-1">
                  Detail
                </x-button>
                @if ($req->status === 'pending')
                  <x-button wire:click="confirmApprove({{ $req->id }})"
                    class="!px-3 !py-1 bg-green-600 hover:bg-green-500 active:bg-green-700 focus:ring-green-500">
                    Setujui
                  </x-button>
                  <x-danger-button wire:click="confirmReject({{ $req->id }})" class="!px-3 !py-1">
                    Tolak
                  </x-danger-button>
                @endif
              </div>
            </td>
          </tr>
          @if ($rejectingId === $req->id)
            <tr wire:key="reject-{{ $req->id }}">
              <td colspan="6" class="px-4 py-3 bg-gray-50 dark:bg-gray-900">
                <div class="flex items-center gap-2">
                  <x-input type="text" placeholder="Alasan penolakan..." wire:model="rejectReason"
                    class="flex-1" />
                  <x-danger-button wire:click="reject">
                    Konfirmasi
                  </x-danger-button>
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
        </tbody>
      @empty
        <tbody>
          <tr>
            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
              Tidak ada data.
            </td>
          </tr>
        </tbody>
      @endforelse
    </table>
  </div>

  <div class="mt-3">
    {{ $requests->links() }}
  </div>

  <x-modal wire:model="showDetailModal">
    @if ($detailRequest)
      <div class="px-6 py-4">
        <h3 class="mb-4 text-lg font-semibold dark:text-white">Detail Pengajuan Izin</h3>

        <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
          <div>
            <x-label value="Karyawan" />
            <p class="font-medium text-gray-900 dark:text-white">{{ $detailRequest->user->name }}</p>
          </div>
          <div>
            <x-label value="Tipe" />
            <p>{{ $detailRequest->type === 'excused' ? 'Izin' : 'Sakit' }}</p>
          </div>
          <div>
            <x-label value="Tanggal" />
            <p>{{ $detailRequest->from_date->format('d/m/Y') }}
              @if ($detailRequest->to_date != $detailRequest->from_date)
                - {{ $detailRequest->to_date->format('d/m/Y') }}
              @endif
            </p>
          </div>
          <div>
            <x-label value="Keterangan" />
            <p>{{ $detailRequest->note }}</p>
          </div>
          <div>
            <x-label value="Status" />
            @switch($detailRequest->status)
              @case('pending')
                <p class="text-amber-600 dark:text-amber-400">Menunggu</p>
                @break
              @case('approved')
                <p class="text-emerald-600 dark:text-emerald-400">Disetujui</p>
                @break
              @case('rejected')
                <p class="text-red-600 dark:text-red-400">Ditolak</p>
                @if ($detailRequest->rejection_reason)
                  <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Alasan: {{ $detailRequest->rejection_reason }}</p>
                @endif
                @break
            @endswitch
          </div>
          @if ($detailRequest->attachment)
            <div>
              <x-label value="Lampiran" />
              @php
                $ext = pathinfo($detailRequest->attachment, PATHINFO_EXTENSION);
                $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
              @endphp
              @if ($isImage)
                <img src="{{ Storage::url($detailRequest->attachment) }}" alt="Lampiran"
                  class="mt-1 max-h-48 rounded object-contain">
              @else
                <a href="{{ Storage::url($detailRequest->attachment) }}" target="_blank"
                  class="text-blue-500 hover:underline">Lihat lampiran</a>
              @endif
            </div>
          @endif
          <div>
            <x-label value="Diajukan" />
            <p>{{ $detailRequest->created_at->format('d/m/Y H:i') }}</p>
          </div>
          @if ($detailRequest->reviewer)
            <div>
              <x-label value="Diperiksa oleh" />
              <p>{{ $detailRequest->reviewer->name }} ({{ $detailRequest->reviewed_at?->format('d/m/Y H:i') }})</p>
            </div>
          @endif
        </div>
      </div>
    @endif
    <x-slot name="footer">
      <x-secondary-button wire:click="$set('showDetailModal', false)">Tutup</x-secondary-button>
    </x-slot>
  </x-modal>

  <x-confirmation-modal wire:model="confirmingApproval">
    <x-slot name="title">
      Konfirmasi Persetujuan
    </x-slot>
    <x-slot name="content">
      Setujui pengajuan izin dari <strong>{{ $approvingName }}</strong>?
    </x-slot>
    <x-slot name="footer">
      <x-secondary-button wire:click="$set('confirmingApproval', false)" wire:loading.attr="disabled">
        Batal
      </x-secondary-button>
      <x-button wire:click="executeApprove" class="ml-2 bg-green-600 hover:bg-green-500" wire:loading.attr="disabled">
        Setujui
      </x-button>
    </x-slot>
  </x-confirmation-modal>
</div>
