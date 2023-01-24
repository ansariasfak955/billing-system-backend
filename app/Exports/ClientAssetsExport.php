<?php

namespace App\Exports;

use App\Models\ClientAsset;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ClientAssetsExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $clientAssets;
    public function __construct($clientAssets){
        $this->clientAssets = $clientAssets;
    }
       public function view(): View
    {
        $clientAssets = $this->clientAssets;
        return view('exports.clientAssets', compact('clientAssets'));
    }
}
