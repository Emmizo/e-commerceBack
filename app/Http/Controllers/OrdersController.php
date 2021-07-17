<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Validator;
use DB;
use JWTAuth;
use Carbon\Carbon;
use Mail;
class OrdersController extends Controller
{
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user=$this->user = JWTAuth::parseToken()->authenticate();
        $userid=$user->id;
        $myOrder=DB::table('orders')->where('client_id',$userid)
        ->orderBy('updated_at','DESC')
        ->get();
        $count=$myOrder->count();
        return response()->json(['Message'=>'Success','Data'=>$myOrder,'Returned_Data'=>$count,'Status'=>200],200);
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
              'comments'=>'required',
            'cartItems'=>'required',
            'address'=>'required',
            'total'=>'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 404,
                'data' => 'Validation Error.',
                'Message' => $validator->errors(),
            ];
            return response()->json($response, 404);
        }
    $user=$this->user = JWTAuth::parseToken()->authenticate();
        $userid=$user->id;
         $datamine = [
            'subject'=>'Order Received',
            'email' => $request->email,
            'address'=>$request->address,
            'cartItems'=>json_encode($request->cartItems),
            'total'=>$request->total,
            'comments'=>$request->comments,
        ];
        Mail::send('emails.order', $datamine, function($message) use ($datamine) {
          $message->to($datamine['email'])
          ->subject($datamine['subject']);
        });
        
    $data=Order::create([
        'client_id'=>$userid,
        'address'=>$request->address,
        'cartItems'=>json_encode($request->cartItems),
        'total'=>$request->total,
        'comments'=>$request->comments,
    ]);

        return response()->json(['Message'=>'Thank you to make Order','Data'=>$data,'Status'=>200],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
      $show=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->select('orders.id','clients.fullname','clients.phone','orders.cartitems','orders.total','orders.comments','orders.address','orders.quantity','orders.status','orders.created_at','orders.updated_at')
      ->where('orders.status',1)
      ->whereDay('orders.updated_at', Carbon::now()->day)
      ->get();
      $count=$show->count();
      return response()->json(['Message'=>'Success','Data'=>$show,'Returned_Data'=>$count,'Status'=>200],200);

        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {

        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
    public function approve(Request $request){
        $validator = Validator::make($request->all(), [
            'orderid'=>'required|integer',
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
        // $user=$this->user = JWTAuth::parseToken()->authenticate();
        // $userid=$user->id;
        $status = $request->status;
        $id=$request->orderid;
        $staff_id=$request->staff_id;
        $perm = DB::table('orders')->where('orders.id', '=', $id)->update(array('orders.status' => $status,'orders.staff_id'=>$staff_id,'orders.updated_at'=>DB::raw('NOW()')));
        if ($status==3) {
            return response()->json(['Message' => 'Paid', 'Status' => 200], 200);
        }elseif ($status==0) {
             return response()->json(['Message' => 'Cancelled', 'Status' => 200], 200);
        }elseif($status==2){
            return response()->json(['Message' => 'In progress', 'Status' => 200], 200);
        }

    }
    public function myWorker(Request $request){
         $validator = Validator::make($request->all(), [
            'staff_id'=>'required|integer',
           

        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 404,
                'data' => 'Validation Error.',
                'Message' => $validator->errors(),
            ];
            return response()->json($response, 404);
        }
    $staffid=$request->staff_id;
    $myWork=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->select('orders.id','clients.fullname','clients.phone','orders.cartitems','orders.quantity','orders.total','orders.status','orders.created_at','orders.updated_at')
      ->where('orders.staff_id',$staffid)
      ->where('orders.status',3)
      
      ->whereDay('orders.updated_at', Carbon::now()->day)
      ->get();
      $count=$myWork->count();
      return response()->json(['Message'=>'Success','Data'=>$myWork,'Returned_Data'=>$count,'Status'=>200],200);
    }

    public function myWorkerWeekly(){
    $user=$this->user = JWTAuth::parseToken()->authenticate();
    $userid=$user->id;
    $myWork=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->select('orders.id','clients.fullname','clients.phone','orders.cartitems','orders.quantity','orders.amount','orders.status','orders.created_at','orders.updated_at')
      ->where('orders.user_id',$userid)
      ->where('orders.status',2)
      ->OrWhere('orders.status',0)
      ->whereBetween('orders.updated_at', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
      ->get();
      $count=$myWork->count();
      return response()->json(['Message'=>'Success','Data'=>$myWork,'Returned_Data'=>$count,'Status'=>200],200);
    }
public function adminWeekly(){
    $myWork=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->join('staff','staff.id','=','orders.staff_id')
      ->select('orders.id','clients.fullname','clients.phone','staff.fullname as received_by','orders.cartitems','orders.total','orders.status','orders.created_at','orders.updated_at')
      ->where('orders.status',3)
      ->whereBetween('orders.updated_at', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
      ->get();
      $count=$myWork->count();
      return response()->json(['Message'=>'Success','Data'=>$myWork,'Returned_Data'=>$count,'Status'=>200],200);
    }

public function adminMonthly(){
    $myWork=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->join('staff','staff.id','=','orders.staff_id')
      ->select('orders.id','clients.fullname','clients.phone','staff.fullname as received_by','orders.cartitems','orders.total','orders.status','orders.created_at','orders.updated_at')
       ->where('orders.status',3)
      ->whereMonth('orders.updated_at', Carbon::now()->month)
      ->get();

      $count=$myWork->count();
      return response()->json(['Message'=>'Success','Data'=>$myWork,'Returned_Data'=>$count,'Status'=>200],200);
    }
    public function adminYear(){
    $myWork=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->select('orders.id','clients.fullname','clients.phone','orders.cartitems','orders.quantity','orders.status','orders.created_at','orders.updated_at')
      ->whereYear('orders.updated_at', Carbon::now()->year)
      ->get();
      $count=$myWork->count();
      return response()->json(['Message'=>'Success','Data'=>$myWork,'Returned_Data'=>$count,'Status'=>200],200);
    }

    public function  allActivity(Request $request){
         $validator = Validator::make($request->all(), [
            'staffid'=>'required|integer',

        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 404,
                'data' => 'Validation Error.',
                'Message' => $validator->errors(),
            ];
            return response()->json($response, 404);
        }
    $staffid=$request->staffid;
   $all=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->select('orders.id','clients.fullname','clients.phone','orders.item_id','orders.quantity','orders.amount','orders.status','orders.created_at','orders.updated_at')
      ->where('orders.user_id',$staffid)
      ->where('orders.status',2)
      ->OrWhere('orders.status',0)

      ->get();
      $count=$all->count();
      return response()->json(['Message'=>'Success','Data'=>$all,'Returned_Data'=>$count,'Status'=>200],200);

    }
public function cancelOrder(Request $request){
    $validator = Validator::make($request->all(), [
            'orderid'=>'required|integer',

        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 404,
                'data' => 'Validation Error.',
                'Message' => $validator->errors(),
            ];
            return response()->json($response, 404);
        }
        $id = $request->orderid;
        $perm = DB::table('orders')->where('orders.id', '=', $id)->update(array('orders.status' => 0));
        return response()->json(['Message' => 'Concelled', 'Status' => 200], 200);
}
public function cancelled()
{
  $myWork=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->select('orders.id','clients.fullname','clients.phone','orders.cartitems','orders.quantity','orders.status','orders.created_at','orders.updated_at')
      ->where('orders.status',0)
      ->whereMonth('orders.updated_at', Carbon::now()->month)
      ->get();
      $count=$myWork->count();
      return response()->json(['Message'=>'Success','Data'=>$myWork,'Returned_Data'=>$count,'Status'=>200],200);
}

/**cancelled this week*/
public function cancelledWeek()
{
  $myWork=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->select('orders.id','clients.fullname','clients.phone','orders.cartitems','orders.quantity','orders.status','orders.created_at','orders.updated_at')
      ->where('orders.status',0)
      ->whereBetween('orders.updated_at', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
      ->get();
      $count=$myWork->count();
      return response()->json(['Message'=>'Success','Data'=>$myWork,'Returned_Data'=>$count,'Status'=>200],200);
}
/**cancelled */
public function cancelledToday()
{
  $myWork=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->select('orders.id','clients.fullname','clients.phone','orders.cartitems','orders.quantity','orders.status','orders.created_at','orders.updated_at')
      ->where('orders.status',0)
      ->whereDay('orders.updated_at', Carbon::now()->day)
      ->get();
      $count=$myWork->count();
      return response()->json(['Message'=>'Success','Data'=>$myWork,'Returned_Data'=>$count,'Status'=>200],200);
}
/**pending*/
public function pending()
{
  $myWork=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->select('orders.id','clients.fullname','clients.phone','orders.cartitems','orders.total','orders.comments','orders.address','orders.quantity','orders.status','orders.created_at','orders.updated_at')
       ->where('orders.status','1')
       ->orWhere('orders.status','2')
      ->orderBy('orders.created_at','DESC')
      ->get();
      $count=$myWork->count();
      return response()->json(['Message'=>'Success','Data'=>$myWork,'Returned_Data'=>$count,'Status'=>200],200);
}
/**paid today*/
public function paidToday()
{
  $myWork=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->join('staff','staff.id','=','orders.staff_id')
      ->select('orders.id','clients.fullname','clients.phone','staff.fullname as received_by','orders.cartitems','orders.total','orders.status','orders.created_at','orders.updated_at')
      ->where('orders.status',3)
      ->whereDay('orders.updated_at', Carbon::now()->day)
      ->get();
      $count=$myWork->count();
      return response()->json(['Message'=>'Success','Data'=>$myWork,'Returned_Data'=>$count,'Status'=>200],200);
}
/**paid weekly*/
public function paidWeekly()
{
  $myWork=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->select('orders.id','clients.fullname','clients.phone','orders.cartitems','orders.quantity','orders.status','orders.created_at','orders.updated_at')
      ->where('orders.status',3)
       ->whereBetween('orders.updated_at', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
      ->get();
      $count=$myWork->count();
      return response()->json(['Message'=>'Success','Data'=>$myWork,'Returned_Data'=>$count,'Status'=>200],200);
}

/**paid Monthly*/
public function paidMonthly()
{
  $myWork=DB::table('orders')
     ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->join('staff','staff.id','=','orders.staff_id')
      ->select('orders.id','clients.fullname','clients.phone','staff.fullname as received_by','orders.cartitems','orders.total','orders.status','orders.created_at','orders.updated_at')
      ->where('orders.status',3)
      ->whereMonth('orders.updated_at', Carbon::now()->month)
      ->get();
      $count=$myWork->count();
      return response()->json(['Message'=>'Success','Data'=>$myWork,'Returned_Data'=>$count,'Status'=>200],200);
}

/**paid Year*/
public function paidYear()
{
  $myWork=DB::table('orders')
     ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->join('staff','staff.id','=','orders.staff_id')
      ->select('orders.id','clients.fullname','clients.phone','staff.fullname as received_by','orders.cartitems','orders.total','orders.status','orders.created_at','orders.updated_at')
      ->get();
      $count=$myWork->count();
      return response()->json(['Message'=>'Success','Data'=>$myWork,'Returned_Data'=>$count,'Status'=>200],200);
}

 public function allOrderPerYear(){
        $allCustomers=DB::table('orders')
        ->select(DB::raw('count(orders.id) as data'), DB::raw('MONTHNAME(orders.updated_at) month'),'orders.status')
        ->groupBy('month','status')
        ->where('orders.status',3)
        ->get();
    $count=$allCustomers->count();
     return response()->json(['Message' => 'Success','Data'=>$allCustomers,'Returned_Data'=>$count, 'Status' => 200], 200);
    }

    public function allOrderPerYearCancelled(){
        $allCustomers=DB::table('orders')
        ->select(DB::raw('count(orders.id) as data1'), DB::raw('MONTHNAME(orders.updated_at) month'),'orders.status')
        ->groupBy('month','status')
        ->where('orders.status',0)
        ->get();
    $count=$allCustomers->count();
     return response()->json(['Message' => 'Success','Data'=>$allCustomers,'Returned_Data'=>$count, 'Status' => 200], 200);
    }
     public function allOrderPerWeek(){
        $allCustomers=DB::table('orders')
        ->select(DB::raw('count(orders.id) as data'), DB::raw('DAYNAME(orders.updated_at) day'),'orders.status')
        ->groupBy('day','status')
        ->where('orders.status',3)
        ->whereBetween('orders.updated_at', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
        ->get();
    $count=$allCustomers->count();
     return response()->json(['Message' => 'Success','Data'=>$allCustomers,'Returned_Data'=>$count, 'Status' => 200], 200);
    }
     public function allOrderPerWeekCancelled(){
        $allCustomers=DB::table('orders')
        ->select(DB::raw('count(orders.id) as data1'), DB::raw('DAYNAME(orders.updated_at) day'),'orders.status')
        ->orderBy('day','ASC')
        ->groupBy('day','status')
        ->where('orders.status',0)
        ->whereBetween('orders.updated_at', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
        ->get();
    $count=$allCustomers->count();
     return response()->json(['Message' => 'Success','Data'=>$allCustomers,'Returned_Data'=>$count, 'Status' => 200], 200);
    }
}
