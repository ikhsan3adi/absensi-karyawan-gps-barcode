<?php

namespace App\Livewire\Forms;

use App\Models\Shift;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ShiftForm extends Form
{
    public ?Shift $shift;

    public $name = '';
    public $start_time = null;
    public $end_time = null;

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shifts')->ignore($this->shift)
            ],
            'start_time' => ['required'],
            'end_time' => ['nullable'],
        ];
    }

    public function setShift(Shift $shift)
    {
        $this->shift = $shift;
        $this->name = $shift->name;
        $this->start_time = $shift->start_time;
        $this->end_time = $shift->end_time;
        return $this;
    }

    public function store()
    {
        $this->validate();
        Shift::create($this->all());
        $this->reset();
    }

    public function update()
    {
        $this->validate();
        $this->shift->update($this->all());
        $this->reset();
    }

    public function delete()
    {
        $this->shift->delete();
        $this->reset();
    }
}
