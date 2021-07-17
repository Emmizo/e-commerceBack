<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Category;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use DB;
use Mail;
use App\Mail\NotifyMail;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $loginAfterSignUp = true;
    //sign up
    public function signUp(Request $request)
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

        if($level==1){
            $data = [
                'subject'=>'e-Share',
                'fullname'=>$request->fullname,
                'email' => $request->email,
                'username' => $request->username,
                'password'=>$request->password,
              ];
              Mail::send('emails.profile', $data, function($message) use ($data) {
                $message->to($data['email'])
                ->subject($data['subject']);
              });
            $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'level' => $request->level,
        ]);
        Client::create([
            'user_id' => $user->id,
            'fullname' => $request->fullname,
            'phone' => $request->phone,
        ]);
            $token = JWTAuth::fromUser($user);
            return response()->json([
            'Message'=>'Success',
            'Data' => $user,
            'Token'=>$token,
            'Status' => 200

        ], 200);
        }elseif($level==2){
            $data = [
                'subject'=>'e-Share',
                'fullname'=>$request->fullname,
                'email' => $request->email,
                'username' => $request->username,
                'password'=>$request->password,
              ];
              Mail::send('emails.profile', $data, function($message) use ($data) {
                $message->to($data['email'])
                ->subject($data['subject']);
              });
           $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'level' => $request->level,
        ]);


        Staff::create([
            'user_id' => $user->id,
            'fullname' => $request->fullname,
            'phone' => $request->phone,
        ]);
            $token = JWTAuth::fromUser($user);
            return response()->json([
            'Message'=>'Success',
            'Data' => $user,
            'Token'=>$token,
            'Status' => 200

        ], 200);
        }elseif($level==3){
            $data = [
                'subject'=>'e-Share',
                'fullname'=>$request->fullname,
                'email' => $request->email,
                'username' => $request->username,
                'password'=>$request->password,
              ];
              Mail::send('emails.profile', $data, function($message) use ($data) {
                $message->to($data['email'])
                ->subject($data['subject']);
              });
           $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'level' => $request->level,
        ]);


        Staff::create([
            'user_id' => $user->id,
            'fullname' => $request->fullname,
            'phone' => $request->phone,
        ]);
            $token = JWTAuth::fromUser($user);
            return response()->json([
            'Message'=>'Success',
            'Data' => $user,
            'Token'=>$token,
            'Status' => 200

        ], 200);
        }
    }
    //login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [

                'Data' => 'Validation Error.',
                'message' => $validator->errors(),
                'Status' => 400,
            ];
            return response()->json($response, 400);
        }
        $login_type = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)
        ? 'email'
        : 'username';
        $request->merge([
            $login_type => $request->input('login'),
        ]);
        $input = $request->only($login_type, 'password');
        $jwt_token = null;
        $log = $request->get($login_type);
        try {
            $users = User::where('username', '=', $log)->orwhere('email', '=', $log)->where('status', '=', 0)->get();
            if (!$jwt_token = JWTAuth::attempt($input)) {
                return response()->json([
                    'Message' => 'Invalid Credential',
                    'Status' => 401,
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['Error' => 'could_not_create_token', 'Status' => 400], 400);

        }

        $user = array();
        $usr = array();
        foreach ($users as $us) {
            $usr['message'] = 'Success';
            $usr['id'] = $us->id;
            $usr['username'] = $us->username;
            $usr['email'] = $us->email;
            $usr['level'] = $us->level;
            $usr['token'] = $jwt_token;
            array_push($user, $usr);
        }
        $count = $users->count();
        if ($count > 0) {
            return response()->json($user);
        } else {
            return response()->json(['Message' => 'Your acount blocked', 'Status' => 401], 401);
        }
    }
    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'null',
        ]);
        if ($validator->fails()) {
            $response = [
                'message' => $validator->errors(),
                'Data' => 'Validation Error.',
                'Status' => 400,
            ];
            return response()->json($response, 400);
        }
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([

                'message' => 'User logged out successfully',
                'Status' => 200,
            ], 200);
        } catch (JWTException $exception) {
            return response()->json([

                'Message' => 'Sorry, the user cannot be logged out',
                'Status' => 400,
            ], 400);
        }
    }
 public function viewCategory(){
      $cat = Category::all();
        $count = $cat->count();
        return response()->json(['Message' => 'Success', 'Data' => $cat, 'Returned_Data' => $count, 'Status' => 200], 200);
 }
 public function allOrderInGeneral(){
    $general=DB::table('orders')
      ->join('users','users.id','=','orders.client_id')
      ->join('clients','clients.user_id','=','users.id')
      ->select('orders.id','clients.fullname','clients.phone','orders.item_id','orders.quantity','orders.amount','orders.status','orders.created_at','orders.updated_at')
      ->orderBy('orders.created_at','DESC')
      ->get();
      $count=$general->count();
      return response()->json(['Message'=>'Success','Data'=>$general,'Returned_Data'=>$count,'Status'=>200],200);
}

}
