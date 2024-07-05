<?php

namespace App\Imports;

use App\Models\Division;
use App\Models\Education;
use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
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
        $division_id = Division::where('name', $row['division'])->first()?->id
            ?? Division::create(['name' => $row['division']])?->id;
        $job_title_id = JobTitle::where('name', $row['job_title'])->first()?->id
            ?? JobTitle::create(['name' => $row['job_title']])?->id;
        $education_id = Education::where('name', $row['education'])->first()?->id
            ?? Education::create(['name' => $row['education']])?->id;
        $user = (new User)->forceFill([
            'id' => isset($row['id']) ? $row['id'] : null,
            'nip' => $row['nip'],
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'gender' => $row['gender'],
            'birth_date' => $row['birth_date'],
            'birth_place' => $row['birth_place'],
            'address' => $row['address'],
            'city' => $row['city'],
            'education_id' => $education_id,
            'division_id' => $division_id,
            'job_title_id' => $job_title_id,
            'password' => Hash::make($row['password']),
            'raw_password' => $row['password'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ]);
        if ($this->save) {
            $user->save();
        }
        return $user;
    }

    public function rules(): array
    {
        return [
            'nip' => ['required', 'string', Rule::unique('users', 'nip')],
            'name' => ['required', 'string'],
            'email' => ['required', 'string', Rule::unique('users', 'email')],
            'gender' => ['required', 'string'],
            // 'education' => ['nullable', 'exists:educations,name'],
            // 'division' => ['nullable', 'exists:divisions,name'],
            // 'job_title' => ['nullable', 'exists:job_titles,name'],
            'password' => ['required', 'string'],
        ];
    }

    public function onFailure(Failure ...$failures)
    {
    }
}
