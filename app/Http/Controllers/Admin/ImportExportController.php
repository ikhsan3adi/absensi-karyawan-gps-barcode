<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ImportExportController extends Controller
{
    public function users()
    {
        return view('admin.import-export.users');
    }

    public function attendances()
    {
        return view('admin.import-export.attendances');
    }
}
