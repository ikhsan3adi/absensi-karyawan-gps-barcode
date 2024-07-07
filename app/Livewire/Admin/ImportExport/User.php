<?php

namespace App\Livewire\Admin\ImportExport;

use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User as UserModel;
use Livewire\WithFileUploads;

class User extends Component
{
    use InteractsWithBanner, WithFileUploads;

    public bool $previewing = false;
    public ?string $mode = null;
    public $groups = ['user'];
    public $file = null;

    protected $rules = [
        'file' => 'required|mimes:csv,xls,xlsx,ods'
    ];

    public function preview()
    {
        $this->previewing = !$this->previewing;
        $this->mode = $this->previewing ? 'export' : null;
    }

    public function updated()
    {
        $this->validateGroups();
    }

    public function render()
    {
        $users = null;
        if ($this->file) {
            $this->mode = 'import';
            $this->previewing = true;
            $userImport = new UsersImport(save: false);
            $users = Excel::toCollection($userImport, $this->file)
                ->first()
                ->map(function (\Illuminate\Support\Collection $v) use ($userImport) {
                    return $userImport->model($v->toArray());
                });
        } else if ($this->previewing && $this->mode == 'export') {
            $users = empty($this->groups) ?
                new \Illuminate\Support\Collection :
                UserModel::whereIn('group', $this->groups)->get();
        } else {
            $this->previewing = false;
            $this->mode = null;
        }
        return view('livewire.admin.import-export.user', [
            'users' => $users
        ]);
    }

    public function import()
    {
        if (Auth::user()->isNotAdmin) {
            abort(403);
        }
        try {
            $this->validate();

            Excel::import(new UsersImport, $this->file);

            $this->banner(__('Success'));
            $this->reset();
        } catch (\Throwable $th) {
            $this->dangerBanner($th->getMessage());
        }
    }

    public function export()
    {
        if (Auth::user()->isNotAdmin) {
            abort(403);
        }
        $this->validateGroups();
        return Excel::download(
            new UsersExport($this->groups),
            'users.xlsx'
        );
    }

    private function validateGroups()
    {
        $this->validate([
            'groups.*' => ['string', 'in:user,admin,superadmin'],
            'groups' => ['required', 'array']
        ]);
    }
}
