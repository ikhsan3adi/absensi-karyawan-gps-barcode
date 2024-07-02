<?php

namespace App\Http\Controllers\Admin;

use App\BarcodeGenerator;
use App\Http\Controllers\Controller;
use App\Models\Barcode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarcodeController extends Controller
{
    protected $rules = [
        'name' => ['required', 'string', 'max:255'],
        'value' => ['required', 'string', 'max:255', 'unique:barcodes'],
        'lat' => ['required', 'numeric'],
        'lng' => ['required', 'numeric'],
        'radius' => ['required', 'numeric'],
    ];

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
        $request->validate($this->rules);
        try {
            Barcode::create([
                'name' => $request->name,
                'value' => $request->value,
                'latitude' => doubleval($request->lat),
                'longitude' => doubleval($request->lng),
                'radius' => $request->radius,
            ]);
            return redirect()->route('admin.barcodes')->with('flash.banner', __('Created successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('flash.banner', $th->getMessage())
                ->with('flash.bannerStyle', 'danger');
        }
    }

    public function edit(Barcode $barcode)
    {
        return view('admin.barcodes.edit', ['barcode' => $barcode]);
    }

    public function update(Request $request, Barcode $barcode)
    {
        $request->validate(array_merge($this->rules, [
            'value' => ['required', 'string', 'max:255', Rule::unique('barcodes')->ignore($barcode->id)],
        ]));
        try {
            $barcode->update([
                'name' => $request->name,
                'value' => $request->value,
                'latitude' => doubleval($request->lat),
                'longitude' => doubleval($request->lng),
                'radius' => $request->radius,
            ]);
            return redirect()->route('admin.barcodes')->with('flash.banner', __('Updated successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('flash.banner', $th->getMessage())
                ->with('flash.bannerStyle', 'danger');
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
            return redirect()->back()
                ->with('flash.banner', 'Barcode ' . __('Not Found'))
                ->with('flash.bannerStyle', 'danger');
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
