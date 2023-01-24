<?php

namespace App\Exports;

use App\Models\Service;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CatalogServiceExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $services;
    public function __construct($services){
        $this->services = $services;
    }
       public function view(): View
    {
        $services = $this->services;
        return view('exports.service', compact('services'));
    }
}
