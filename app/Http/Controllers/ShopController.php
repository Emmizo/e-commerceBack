<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Shopitem;
use Illuminate\Http\Request;
use Validator;
use DB;
class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $viewShop=DB::table('shops')->orderBy('shops.updated_at','DESC')
        ->get();
        $count=$viewShop->count();
        return response()->json(['Message'=>'Success','Data'=>$viewShop,'Returned_Data'=>$count,'Status'=>200],200);
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
         $validator = Validator::make($request->all(), [
            'shop_name' => 'required',
            'location'=>'required',
            'phone' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 404,
                'data' => 'Validation Error.',
                'message' => $validator->errors(),
            ];
            return response()->json($response, 404);
        }
        $create=Shop::create([
            'shop_name'=>$request->shop_name,
            'location'=>$request->location,
            'phone'=>$request->phone
        ]);
        return response()->json(['Message'=>'New Shop Added Well','Status'=>200],200);
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function show(Shop $shop)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function edit(Shop $shop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shop $shop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shop $shop)
    {
        //
    }
    public function joinShopItems(Request $request){
         $validator = Validator::make($request->all(), [
            'shop_id' => 'required|integer',
            'item_id'=>'required|integer',
        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 404,
                'data' => 'Validation Error.',
                'Message' => $validator->errors(),
            ];
            return response()->json($response, 404);
        }
        $join=Shopitem::create([
        'shop_id'=>$request->shop_id,
        'item_id'=>$request->item_id,
        ]);
        return response()->json(['Message'=>'Shop with item joined well','Status'=>200],200);
       

    }
    public function singleShop(Request $request){
         $validator = Validator::make($request->all(), [
            'shop_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 404,
                'data' => 'Validation Error.',
                'Message' => $validator->errors(),
            ];
            return response()->json($response, 404);
        }
        $shop=$request->shop_id;
        $display=DB::table('shops')
        ->join('shopitems','shopitems.shop_id','=','shops.id')
        ->join('items','shopitems.item_id','=','items.id')
        ->select('items.id as item_id','shopitems.id as shopitem_id','items.item_name','items.price','items.discount','items.image')
        ->where('shopitems.shop_id',$shop)
        ->get();
        $count=$display->count();
        return response()->json(['Message'=>'Success','Data'=>$display,'Returned_Data'=>$count,'Status'=>200],200);

    }
}
