<?php

namespace App\Livewire;

use Livewire\Component;

class BarcodeValueInputComponent extends Component
{
    public $value = null;

    public function rendering()
    {
        if (old('value')) {
            $this->value = old('value');
        }
    }

    public function render()
    {
        return view('livewire.barcode-value-input');
    }

    public function generate()
    {
        $this->value = fake()->ean13();
    }
}
