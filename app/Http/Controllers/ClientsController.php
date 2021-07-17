<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use DB;
class ClientsController extends Controller
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
        ->join('clients','clients.user_id','=','users.id')
            ->select('users.id','clients.id as client_id', 'users.username','users.level', 'clients.phone', 'users.email','clients.fullname', 'users.status', 'clients.created_at')
            ->where('clients.user_id', $userid)
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        //
    }
}
