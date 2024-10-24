<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class Index extends Component
{

    public $count = 1;

    public function increment()
    {
        $this->count++;
    }

    public function render()
    {
        return view('livewire.chat.index');
    }
}
