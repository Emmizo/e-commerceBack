<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Validator;
use JD\Cloudder\Facades\Cloudder;
use DB;
class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
          $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 400,
                'data' => 'Validation Error.',
                'message' => $validator->errors(),
            ];
            return response()->json($response, 400);
        }
        $res =array();
        $subcat_id=$request->id;
        $viewItem=DB::table('subcategories')
        ->select('subcategories.id','subcategories.cat_name')
        ->where('id',$subcat_id)
        ->get();
        foreach ( $viewItem as $result) {
           $id=$result->id;
        $view=DB::table('items')
        ->where('items.subcat_id', $id)->orderBy('items.created_at','DESC')
        ->get();
            $res['id']=$result->id;
            $res['cat_name']=$result->cat_name;
            $res['Data']=$view;
        }
        $count=$view->count();
        return response()->json(['Message'=>'Success','Data'=>$res,'Returned_Data'=>$count,'Status'=>200],200);
        //
    }

public function makeitem(Request $request){
        $validator = Validator::make($request->all(), [
            'itemid'=>'required|integer',
            'status' => 'required|integer',

        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 404,
                'data' => 'Validation Error.',
                'Message' => $validator->errors(),
            ];
            return response()->json($response, 404);
        }
        $status = $request->status;
        $id=$request->itemid;
       
        $perm = DB::table('items')->where('items.id', '=', $id)->update(array('items.status' => $status,'items.updated_at'=>DB::raw('NOW()')));
        if ($status==1) {
            return response()->json(['Message' => 'Available', 'Status' => 200], 200);
        }elseif ($status==0) {
             return response()->json(['Message' => 'Not Available', 'Status' => 200], 200);
        }

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
            'image' => 'required|image',
            'item_name' => 'required',
            'subcat_id' => 'required|integer',
            'description'=>'required',


        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 404,
                'data' => 'Validation Error.',
                'message' => $validator->errors(),
            ];
            return response()->json($response, 404);
        }

        $image = $request->file('image');

        $name = $request->file('image')->getClientOriginalName();

        $image_name = $request->file('image')->getRealPath();

        Cloudder::upload($image_name, null);

        list($width, $height) = getimagesize($image_name);

        $image_url = Cloudder::show(Cloudder::getPublicId(), ["width" => $width, "height" => $height]);
        //save to uploads directory
        $image->move(public_path("public/images"), $name);
        $count = array();


                $employee = Item::create([
                    'image' => $image_url,
                    'item_name' => $request->item_name,
                    'subcat_id' => $request->subcat_id,
                    'price' => $request->price,
                    'description'=>$request->description,
                ]);
                 return response()->json(['Message' => 'new item added', 'Status' => 200], 200);
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        $allItem=DB::table('items')
        ->join('subcategories','subcategories.id','items.subcat_id')
        ->join('categories','categories.id','subcategories.cat_id')
        ->select('items.id','items.item_name','subcategories.id as cat_id','items.image','items.description','items.price','items.discount','categories.categories_name','items.created_at')
        ->orderBy('items.created_at','DESC')->get();
        $count=$allItem->count();
        return response()->json(['products'=>$allItem,'Returned_Data'=>$count,'Status'=>200],200);
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        //
    }
}
