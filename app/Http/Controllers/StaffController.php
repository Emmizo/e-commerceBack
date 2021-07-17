<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Staff;
use App\Models\Client;
use Illuminate\Http\Request;
use DB;
use JWTAuth;
use Hash;
use Validator;
class StaffController extends Controller
{
     protected $user;

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
        $view = DB::table('users')
        ->join('staff','staff.user_id','=','users.id')
            ->select('users.id','staff.id as staff_id', 'users.username', 'staff.phone', 'users.email','staff.fullname', 'users.status','users.level','staff.created_at','staff.updated_at')
            ->where('staff.user_id',$userid)
            ->get();
        $count = $view->count();
        return response()->json(['Message' => 'Success', 'Data' => $view, 'retured_data' => $count, 'Status' => 200], 200);
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
            'username' => 'required|unique:users',
            'email' => 'required|unique:users',
            'password' => 'required',
            'level' => 'required',
            'confirmPassword' => 'required|same:password',
            'phone' => 'required',
            'fullname'=>'required',

        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 400,
                'data' => 'Validation Error.',
                'message' => $validator->errors(),
            ];
            return response()->json($response, 400);
        }
        $level=$request->level;
         $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'level' => $request->level,
        ]);
        if($level==1){
           
       Client::create([
            'user_id' => $user->id,
            'fullname' => $request->fullname,
            'phone' => $request->phone,
        ]); 
           
            return response()->json(['Message' => 'you add client', 'Status' => 200], 200);
        }
        if($level==2){
           
       $staff=new Staff();
       $staff->fullname=$request->fullname;
       $staff->phone=$request->phone;
       $staff->staff_id=$user->id;
       if ($this->user->staffs()->save($staff)) {
            return response()->json(['Message' => 'Success New staff added', 'Status' => 200], 200);
        }
           
        }
        elseif($level==3){
           
       $staff=new Staff();
       $staff->fullname=$request->fullname;
       $staff->phone=$request->phone;
       $staff->staff_id=$user->id;
       if ($this->user->staffs()->save($staff)) {
            return response()->json(['Message' => 'Success New Super User added', 'Status' => 200], 200);
        }
        }
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function allstaff(){
        $showme=DB::table('staff')
        ->join('users','users.id','staff.user_id')
        ->select('users.id as userid','staff.id as staffid','users.level','staff.fullname','staff.phone','users.email','users.status')
        ->get();
        $count=$showme->count();
        return response()->json(['Message'=>'Success','Data'=>$showme,'Returned_Data'=>$count,'Status'=>200],200);
    }
     public function changePassword(Request $request)
    {

        if (!(Hash::check($request->get('current_password'), JWTAuth::user()->password))) {
            // The passwords matches
            return response()->json(["Message" => "Your current password does not matches with the password you provided. Please try again.", "Status" => 400], 400);
        }

        if (strcmp($request->get('current_password'), $request->get('new_password')) == 0) {
            //Current password and new password are same
            return response()->json(['Message' => "New Password cannot be same as your current password. Please choose a different password.", "Status" => 400], 400);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required',
            'confirmPassword' => 'required|same:new_password',
        ]);
        if ($validator->fails()) {
            $response = [

                'data' => 'Validation Error.',
                'message' => $validator->errors(),
                'Status' => 401,
            ];
            return response()->json($response, 401);
        }
        //Change Password
        $user = JWTAuth::user();
        $user->password = bcrypt($request->get('new_password'));
        $user->save();

        return response()->json(["Message" => "success,Password changed successfully !", "Status" => 200], 200);

    }
}
