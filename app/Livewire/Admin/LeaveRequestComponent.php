<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;
use Livewire\WithPagination;

class LeaveRequestComponent extends Component
{
    use InteractsWithBanner, WithPagination;

    public string $filter = 'pending';
    public ?string $rejectReason = null;
    public ?int $rejectingId = null;
    public bool $showDetailModal = false;
    public ?int $selectedRequestId = null;
    public bool $confirmingApproval = false;
    public ?int $approvingId = null;
    public string $approvingName = '';

    public function viewDetail($id)
    {
        $this->selectedRequestId = $id;
        $this->showDetailModal = true;
    }

    public function confirmApprove($id)
    {
        $leaveRequest = LeaveRequest::with('user')->findOrFail($id);
        $this->approvingId = $id;
        $this->approvingName = $leaveRequest->user->name;
        $this->confirmingApproval = true;
    }

    public function executeApprove()
    {
        $leaveRequest = LeaveRequest::with('user')->findOrFail($this->approvingId);
        $leaveRequest->update([
            'status' => 'approved',
            'reviewed_by' => Auth::user()->id,
            'reviewed_at' => now(),
        ]);

        Carbon::parse($leaveRequest->from_date)
            ->range(Carbon::parse($leaveRequest->to_date))
            ->forEach(function (Carbon $date) use ($leaveRequest) {
                Attendance::updateOrCreate(
                    [
                        'user_id' => $leaveRequest->user_id,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'status' => $leaveRequest->type,
                        'note' => $leaveRequest->note,
                        'attachment' => $leaveRequest->attachment,
                        'latitude' => $leaveRequest->latitude,
                        'longitude' => $leaveRequest->longitude,
                    ]
                );
            });

        $this->confirmingApproval = false;
        $this->approvingId = null;
        $this->approvingName = '';
        $this->banner('Pengajuan izin telah disetujui.');
    }

    public function confirmReject($id)
    {
        $this->rejectingId = $id;
        $this->rejectReason = null;
    }

    public function reject()
    {
        $this->validate(['rejectReason' => 'required|string|max:255']);

        LeaveRequest::where('id', $this->rejectingId)->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $this->rejectReason,
        ]);

        $this->rejectingId = null;
        $this->rejectReason = null;
        $this->banner('Pengajuan izin telah ditolak.');
    }

    public function cancelReject()
    {
        $this->rejectingId = null;
        $this->rejectReason = null;
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $requests = LeaveRequest::with('user')
            ->when($this->filter !== 'all', fn ($q) => $q->where('status', $this->filter))
            ->latest()
            ->paginate(20);

        $detailRequest = $this->showDetailModal && $this->selectedRequestId
            ? LeaveRequest::with(['user', 'reviewer'])->find($this->selectedRequestId)
            : null;

        return view('livewire.admin.leave-requests', [
            'requests' => $requests,
            'pendingCount' => LeaveRequest::where('status', 'pending')->count(),
            'detailRequest' => $detailRequest,
        ]);
    }
}
