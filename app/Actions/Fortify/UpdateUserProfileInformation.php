<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'phone' => ['required', 'string', 'max:64', Rule::unique('users')->ignore($user->id)],
            'gender' => ['required', 'string', 'in:male,female'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'education_id' => ['nullable', 'exists:educations,id'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'job_title_id' => ['nullable', 'exists:job_titles,id'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        $input = array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $input);

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'nip' => $input['nip'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'gender' => $input['gender'],
                'address' => $input['address'],
                'city' => $input['city'],
                'birth_date' => $input['birth_date'],
                'birth_place' => $input['birth_place'],
                'education_id' => $input['education_id'],
                'division_id' => $input['division_id'],
                'job_title_id' => $input['job_title_id'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'nip' => $input['nip'],
            'email' => $input['email'],
            'email_verified_at' => null,
            'phone' => $input['phone'],
            'gender' => $input['gender'],
            'address' => $input['address'],
            'city' => $input['city'],
            'birth_date' => $input['birth_date'],
            'birth_place' => $input['birth_place'],
            'education_id' => $input['education_id'],
            'division_id' => $input['division_id'],
            'job_title_id' => $input['job_title_id'],
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
