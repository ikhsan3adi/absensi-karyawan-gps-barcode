<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class UsersExport implements FromView
{
    private $group = 'user';

    public function setGroup(string $group)
    {
        $this->group = $group;
    }

    public function view(): View
    {
        return view('admin.import-export.export-users', [
            'users' => User::where('group', $this->group)->get(),
        ]);
    }
}
