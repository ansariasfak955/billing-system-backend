<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Service;
use App\Models\ClientAsset;
use App\Models\ClientAttachment;
use App\Models\ClientSpecialPrice;
use Illuminate\Http\Request;
use Validator;

class ImportExportController extends Controller
{
    public function getColumns($company_id,$type){
        // return $type;
        $columns = [];
        if($type == "client" || $type == "potential_client"){
            $getClientColumn = new Client;
            $columns = $getClientColumn->getTableColumns();
        }elseif($type == "suppliers"){
            $getSupplierColumn = new Supplier;
            $columns = $getSupplierColumn->getTableColumns();
        }elseif($type == "products"){
            $getProductColumn = new Product;
            $columns = $getProductColumn->getTableColumns();
        }elseif($type == "service"){
            $getServiceColumn = new Service;
            $columns = $getServiceColumn->getTableColumns();
        }elseif($type == "assets"){
            $getAssetsColumn = new ClientAsset;
            $columns = $getAssetsColumn->getTableColumns();
        }
        return response()->json([
            'status' => true,
            'columns' => $columns
        ]);
    }
}
