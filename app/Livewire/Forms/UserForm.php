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
    public $photo = null;

    public function rules()
    {
        $requiredOrNullable = $this->group === 'user' ? 'required' : 'nullable';
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($this->user)
            ],
            'nip' => [$requiredOrNullable, 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user)
            ],
            'phone' => ['required',  'string', 'min:5', 'max:255'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
            'gender' => [$requiredOrNullable, 'in:male,female'],
            'city' => [$requiredOrNullable, 'string', 'max:255'],
            'address' => [$requiredOrNullable, 'string', 'max:255'],
            'group' => ['nullable', 'string', 'max:255', Rule::in(User::$groups)],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'education_id' => ['nullable', 'exists:educations,id'],
            'job_title_id' => ['nullable', 'exists:job_titles,id'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ];
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->nip = $user->nip;
        $this->email = $user->email;
        $this->phone = $user->phone;
        if ($this->isAllowed()) {
            $this->password = $user->raw_password;
        }
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
        if (!$this->isAllowed()) {
            return abort(403);
        }
        $this->validate();
        /** @var User $user */
        $user = User::create([
            ...$this->all(),
            'password' => Hash::make($this->password ?? 'password'),
            'raw_password' => $this->password ?? 'password',
        ]);
        if (isset($this->photo)) $user->updateProfilePhoto($this->photo);
        $this->reset();
    }

    public function update()
    {
        if (!$this->isAllowed()) {
            return abort(403);
        }
        $this->validate();
        $this->user->update([
            ...$this->all(),
            'password' => $this->password ? Hash::make($this->password) : $this->user?->password,
            'raw_password' => $this->password ?? $this->user?->raw_password,
        ]);
        if (isset($this->photo)) $this->user->updateProfilePhoto($this->photo);
        $this->reset();
    }

    public function deleteProfilePhoto()
    {
        if (!$this->isAllowed()) {
            return abort(403);
        }
        return $this->user->deleteProfilePhoto();
    }

    public function delete()
    {
        if (!$this->isAllowed()) {
            return abort(403);
        }
        $this->user->delete();
        $this->deleteProfilePhoto();
        $this->reset();
    }

    private function isAllowed()
    {
        if ($this->group === 'user') {
            return Auth::user()?->isAdmin;
        }
        return Auth::user()?->isSuperadmin || (Auth::user()?->isAdmin && Auth::user()?->id === $this->user?->id);
    }
}
