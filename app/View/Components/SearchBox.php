<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SearchBox extends Component
{
    public $id;
    public $placeholder;

    public function __construct($id = 'search-box', $placeholder = 'Search...')
    {
        $this->id = $id;
        $this->placeholder = $placeholder;
    }

    public function render()
    {
        return view('components.search-box');
    }
}