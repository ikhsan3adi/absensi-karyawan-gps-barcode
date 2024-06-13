<?php

namespace App\Livewire;

use App\Models\JobTitle;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;

class JobTitleComponent extends Component
{
    use InteractsWithBanner;

    public $name;
    public $deleteName = null;
    public $creating = false;
    public $editing = false;
    public $confirmingDeletion = false;
    public $selectedId = null;

    protected $rules = [
        'name' => ['required', 'string', 'max:255', 'unique:job_titles'],
    ];

    public function create()
    {
        $this->validate();
        JobTitle::create(['name' => $this->name]);
        $this->creating = false;
        $this->name = null;
        $this->banner(__('Created successfully.'));
    }

    public function edit($id)
    {
        $this->editing = true;
        $jobTitle = JobTitle::find($id);
        $this->name = $jobTitle->name;
        $this->selectedId = $id;
    }

    public function update()
    {
        $this->validate();
        $jobTitle = JobTitle::find($this->selectedId);
        $jobTitle->update(['name' => $this->name]);
        $this->editing = false;
        $this->selectedId = null;
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
        $jobTitle = JobTitle::find($this->selectedId);
        $jobTitle->delete();
        $this->confirmingDeletion = false;
        $this->selectedId = null;
        $this->deleteName = null;
        $this->banner(__('Deleted successfully.'));
    }

    public function render()
    {
        $jobTitles = JobTitle::all();
        return view('livewire.job-title', ['jobTitles' => $jobTitles]);
    }
}
