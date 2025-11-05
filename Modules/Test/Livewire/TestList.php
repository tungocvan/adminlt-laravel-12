<?php

namespace Modules\Test\Livewire;

use Livewire\Component;

class TestList extends Component
{
    public function render()
    {
        return view('Test::livewire.test-list');
    }
}