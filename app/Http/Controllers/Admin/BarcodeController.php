<?php

namespace App\Http\Controllers\Admin;

use App\BarcodeGenerator;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Barcode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.barcodes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function create()
    {
        return view('admin.barcodes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:255', 'unique:barcodes'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'radius' => ['required', 'numeric'],
            'time_limit' => ['required'],
            // 'time_in_valid_from' => ['date_format:H:i', 'before:time_in_valid_until'],
            // 'time_in_valid_until' => ['date_format:H:i', 'after:time_in_valid_from'],
            // 'time_out_valid_from' => ['date_format:H:i', 'before:time_out_valid_until'],
            // 'time_out_valid_until' => ['date_format:H:i', 'after:time_out_valid_from'],
        ]);
        try {
            Barcode::create([
                'name' => $request->name,
                'value' => $request->value,
                'coordinates' => Helpers::createPointQuery($request->lat, $request->lng),
                'radius' => $request->radius,
                'time_limit' => $request->time_limit
                // 'time_in_valid_from' => $request->time_in_valid_from,
                // 'time_in_valid_until' => $request->time_in_valid_until,
                // 'time_out_valid_from' => $request->time_out_valid_from,
                // 'time_out_valid_until' => $request->time_out_valid_until,
            ]);
            return redirect()->route('admin.barcodes')->with('success', __('Created successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors($th->getMessage());
        }
    }

    public function edit(Barcode $barcode)
    {
        return view('admin.barcodes.edit', ['barcode' => $barcode]);
    }

    public function update(Request $request, Barcode $barcode)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:255', Rule::unique('barcodes')->ignore($barcode->id)],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'radius' => ['required', 'numeric'],
            'time_limit' => ['required'],
            // 'time_in_valid_from' => ['date_format:H:i', 'before:time_in_valid_until'],
            // 'time_in_valid_until' => ['date_format:H:i', 'after:time_in_valid_from'],
            // 'time_out_valid_from' => ['date_format:H:i', 'before:time_out_valid_until'],
            // 'time_out_valid_until' => ['date_format:H:i', 'after:time_out_valid_from'],
        ]);
        try {
            $barcode->update([
                'name' => $request->name,
                'value' => $request->value,
                'coordinates' => Helpers::createPointQuery($request->lat, $request->lng),
                'radius' => $request->radius,
                'time_limit' => $request->time_limit
                // 'time_in_valid_from' => $request->time_in_valid_from,
                // 'time_in_valid_until' => $request->time_in_valid_until,
                // 'time_out_valid_from' => $request->time_out_valid_from,
                // 'time_out_valid_until' => $request->time_out_valid_until,
            ]);
            return redirect()->route('admin.barcodes')->with('success', __('Updated successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors($th->getMessage());
        }
    }


    public function download($barcodeId)
    {
        $barcode = Barcode::find($barcodeId);
        $barcodeFile = (new BarcodeGenerator(width: 1280, height: 1280))->generateQrCode($barcode->value);
        return response($barcodeFile)->withHeaders([
            'Content-Type' => 'aplication/octet-stream',
            'Content-Disposition' => 'attachment; filename=' . ($barcode->name ?? $barcode->value) . '.png',
        ]);
    }

    public function downloadAll()
    {
        $barcodes = Barcode::all();
        if ($barcodes->isEmpty()) {
            return redirect()->back()->withErrors('Barcodes not found');
        }
        $zipFile = (new BarcodeGenerator(width: 1280, height: 1280))->generateQrCodesZip(
            $barcodes->mapWithKeys(fn ($barcode) => [$barcode->name => $barcode->value])->toArray()
        );

        return response(file_get_contents($zipFile))->withHeaders([
            'Content-Type' => 'aplication/octet-stream',
            'Content-Disposition' => 'attachment; filename=barcodes.zip',
        ]);
    }
}
