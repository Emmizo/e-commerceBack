<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Validator;
use DB;
class ForgotPasswordController extends Controller
{
     public function forgot(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            $response = [
                'Status' => 404,
                'data' => 'Validation Error.',
                'message' => $validator->errors(),
            ];
            return response()->json($response, 404);
        }
    $credentials = request()->validate(['email' => 'required|email']);
    $email=DB::table('users')->where('email',$credentials)->where('status',1)
    ->get();
    if($email->count()>0){
        Password::sendResetLink($credentials);

        return response()->json(["Message" => 'Reset password link sent on your email id.', 'status' => 200], 200);
    }else{
        return response()->json(["Message" => 'This email not available in system', 'status' => 200], 200);
    }
    }
    //
    public function reset()
    {
        $credentials = request()->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $reset_password_status = Password::reset($credentials, function ($user, $password) {
            $user->password = bcrypt($password);
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(["Message" => "Invalid token provided"], 400);
        }

        return response()->json(["Message" => "Password has been successfully changed"]);
    }
    //
}
