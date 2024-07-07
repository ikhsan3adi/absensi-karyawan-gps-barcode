<?php

namespace App\Livewire\Admin\MasterData;

use App\Livewire\Forms\UserForm;
use App\Models\User;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Admin extends Component
{
    use WithPagination, InteractsWithBanner, WithFileUploads;

    public UserForm $form;
    public $groups = [];
    public $deleteName = null;
    public $creating = false;
    public $editing = false;
    public $confirmingDeletion = false;
    public $selectedId = null;
    public $showDetail = null;

    public function __construct()
    {
        $this->groups = User::$groups;
    }

    public function show($id)
    {
        $this->form->setUser(User::find($id));
        $this->showDetail = true;
    }

    public function showCreating()
    {
        $this->form->resetErrorBag();
        $this->form->reset();
        $this->creating = true;
        $this->form->password = 'admin';
    }

    public function create()
    {
        $this->form->store();
        $this->creating = false;
        $this->banner(__('Created successfully.'));
    }

    public function edit($id)
    {
        $this->form->resetErrorBag();
        $this->form->reset();
        $this->editing = true;
        /** @var User $user */
        $user = User::find($id);
        $this->form->setUser($user);
    }

    public function update()
    {
        $this->form->update();
        $this->editing = false;
        $this->banner(__('Updated successfully.'));
    }

    public function deleteProfilePhoto()
    {
        $this->form->deleteProfilePhoto();
    }

    public function confirmDeletion($id, $name)
    {
        $this->deleteName = $name;
        $this->confirmingDeletion = true;
        $this->selectedId = $id;
    }

    public function delete()
    {
        $user = User::find($this->selectedId);
        $this->form->setUser($user)->delete();
        $this->confirmingDeletion = false;
        $this->banner(__('Deleted successfully.'));
    }

    public function render()
    {
        $users = User::where('group', '!=', 'user')->orderBy('group', 'desc')->paginate(20);
        return view('livewire.admin.master-data.admin', ['users' => $users]);
    }
}
