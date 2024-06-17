<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserForm extends Form
{
    public ?User $user = null;

    public $name = '';
    public $nip = '';
    public $email = '';
    public $phone = '';
    public $password = null;
    public $gender = null;
    public $city = '';
    public $address = '';
    public $group = 'user';
    public $birth_date = null;
    public $birth_place = '';
    public $division_id = null;
    public $education_id = null;
    public $job_title_id = null;

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($this->user)
            ],
            'nip' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user)
            ],
            'phone' => ['required',  'numeric', 'min:5', 'max:99999999999999999'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'group' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'education_id' => ['nullable', 'exists:educations,id'],
            'job_title_id' => ['nullable', 'exists:job_titles,id'],
        ];
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->nip = $user->nip;
        $this->email = $user->email;
        $this->phone = $user->phone;
        // $this->password = $user->password;
        $this->gender = $user->gender;
        $this->city = $user->city;
        $this->address = $user->address;
        $this->group = $user->group;
        $this->birth_date = $user->birth_date
            ? \Illuminate\Support\Carbon::parse($user->birth_date)->format('Y-m-d')
            : null;
        $this->birth_place = $user->birth_place;
        $this->division_id = $user->division_id;
        $this->education_id = $user->education_id;
        $this->job_title_id = $user->job_title_id;
        return $this;
    }

    public function store()
    {
        if (Auth::user()->group != 'admin') {
            return abort(403);
        }
        $this->validate();
        User::create([
            ...$this->all(),
            'password' => Hash::make($this->password ?? 'password')
        ]);
        $this->reset();
    }

    public function update()
    {
        if (Auth::user()->group != 'admin') {
            return abort(403);
        }
        $this->validate();
        $this->user->update([
            ...$this->all(),
            'password' => Hash::make($this->password ?? 'password')
        ]);
        $this->reset();
    }

    public function delete()
    {
        if (Auth::user()->group != 'admin') {
            return abort(403);
        }
        $this->user->delete();
        $this->reset();
    }
}
