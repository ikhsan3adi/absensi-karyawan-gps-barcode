<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.employees.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }
}
