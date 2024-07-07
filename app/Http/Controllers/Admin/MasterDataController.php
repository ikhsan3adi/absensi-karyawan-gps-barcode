<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class MasterDataController extends Controller
{
    public function division()
    {
        return view('admin.master-data.division');
    }

    public function jobTitle()
    {
        return view('admin.master-data.job-title');
    }

    public function education()
    {
        return view('admin.master-data.education');
    }

    public function shift()
    {
        return view('admin.master-data.shift');
    }

    public function admin()
    {
        return view('admin.master-data.admin');
    }
}
