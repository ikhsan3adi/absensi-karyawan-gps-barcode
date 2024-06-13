<?php

use App\Models\User;
use Illuminate\Support\Carbon;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;

test('current profile information is available', function () {
    $this->actingAs($user = User::factory()->create());

    $component = Livewire::test(UpdateProfileInformationForm::class);

    expect($component->state['name'])->toEqual($user->name);
    expect($component->state['email'])->toEqual($user->email);
});

test('profile information can be updated', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(UpdateProfileInformationForm::class)
        ->set('state', [
            'name' => 'Test Name',
            'nip' => '123',
            'email' => 'test@example.com',
            'phone' => '123',
            'gender' => 'female',
            'address' => 'abc',
            'city' => 'abc',
            'birth_date' => '2024-01-01',
            'birth_place' => 'abc',
            'education_id' => null,
            'division_id' => null,
            'job_title_id' => null,
        ])->call('updateProfileInformation');

    expect($user->fresh())
        ->name->toEqual('Test Name')
        ->email->toEqual('test@example.com')
        ->gender->toEqual('female')
        ->phone->toEqual('123')
        ->nip->toEqual('123')
        ->address->toEqual('abc')
        ->city->toEqual('abc')
        ->birth_date->toEqual(Carbon::parse('2024-01-01'))
        ->birth_place->toEqual('abc')
        ->education_id->toEqual(null)
        ->division_id->toEqual(null)
        ->job_title_id->toEqual(null);
});
