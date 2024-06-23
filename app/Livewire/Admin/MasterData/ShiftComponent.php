<?php

namespace App\Livewire\Admin\MasterData;

use App\Livewire\Forms\ShiftForm;
use App\Models\Shift;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;

class ShiftComponent extends Component
{
    use InteractsWithBanner;

    public ShiftForm $form;
    public $deleteName = null;
    public $creating = false;
    public $editing = false;
    public $confirmingDeletion = false;
    public $selectedId = null;

    public function showCreating()
    {
        $this->form->resetErrorBag();
        $this->form->reset();
        $this->creating = true;
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
        $this->editing = true;
        /** @var Shift $shift */
        $shift = Shift::find($id);
        $this->form->setShift($shift);
    }

    public function update()
    {
        $this->form->update();
        $this->editing = false;
        $this->banner(__('Updated successfully.'));
    }

    public function confirmDeletion($id, $name)
    {
        $this->deleteName = $name;
        $this->confirmingDeletion = true;
        $this->selectedId = $id;
    }

    public function delete()
    {
        $shift = Shift::find($this->selectedId);
        $this->form->setShift($shift)->delete();
        $this->confirmingDeletion = false;
        $this->banner(__('Deleted successfully.'));
    }

    public function render()
    {
        $shifts = Shift::all();
        return view('livewire.admin.master-data.shift', ['shifts' => $shifts]);
    }
}
