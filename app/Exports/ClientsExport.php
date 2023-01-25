<?php

namespace App\Exports;

use App\Models\Client;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ClientsExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $clients;
    public function __construct($clients){
        $this->clients = $clients;
    }
       public function view(): View
    {
        $clients = $this->clients;
        return view('exports.clients', compact('clients'));
    }
}
