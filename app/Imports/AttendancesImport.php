<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Shift;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class AttendancesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    public function __construct(public bool $save = true)
    {
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        [$lat, $lng] = [null, null];
        if (isset($row['coordinates'])) {
            [$lat, $lng] = explode(',', $row['coordinates']);
        }
        $shift_id = Shift::where('name', $row['shift'])->first()?->id ?? $row['shift_id'];

        $attendance = (new Attendance)->forceFill([
            'user_id' => $row['user_id'],
            'barcode_id' => $row['barcode_id'],
            'date' => $row['date'],
            'time_in' => $row['time_in'],
            'time_out' => $row['time_out'],
            'shift_id' => $shift_id,
            'latitude' => doubleval($lat),
            'longitude' => doubleval($lng),
            'status' => $this->getStatus($row['status']) ?? $row['raw_status'],
            'note' => $row['note'],
            'attachment' => $row['attachment'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ]);
        if ($this->save) {
            $attendance->save();
        }
        return $attendance;
    }

    private function getStatus($status)
    {
        switch (Str::lower($status)) {
            case 'hadir':
                return 'present';
            case 'terlambat':
                return 'late';
            case 'izin':
                return 'excused';
            case 'sakit':
                return 'sick';
            case 'tidak hadir':
                return 'absent';
            default:
                return null;
        }
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'date' => 'required',
            'status' => 'required',
            // 'shift' => 'nullable|exists:shifts,name',
            // 'barcode_id' => 'nullable|exists:barcodes,id',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
    }
}
