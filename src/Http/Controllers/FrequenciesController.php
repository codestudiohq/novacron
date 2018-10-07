<?php

namespace Studio\Novacron\Http\Controllers;

use Studio\Totem\Frequency;

class FrequenciesController
{
    public function index()
    {
        return Frequency::all();
    }
}
