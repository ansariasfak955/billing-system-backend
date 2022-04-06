<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientSpecialPrice;
use Illuminate\Http\Request;

class ClientSpecialPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client_special_price =  new ClientSpecialPrice;

        $client_sp_table = 'company_'.$request->company_id.'_client_special_prices';
        ClientSpecialPrice::setGlobalTable($client_sp_table);
        ClientSpecialPrice::create([
            'client_id' => $request->client_id,
            'product_id' => $request->product_id,
            'purchase_price' => $request->purchase_price,
            'sales_price' => $request->sales_price,
            'purchase_margin' => $request->purchase_margin,
            'sales_margin' => $request->sales_margin,
            'discount' => $request->discount,
            'special_price' => $request->special_price,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClientSpecialPrice  $clientSpecialPrice
     * @return \Illuminate\Http\Response
     */
    public function show(ClientSpecialPrice $clientSpecialPrice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClientSpecialPrice  $clientSpecialPrice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClientSpecialPrice $clientSpecialPrice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClientSpecialPrice  $clientSpecialPrice
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClientSpecialPrice $clientSpecialPrice)
    {
        //
    }
}
