<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('administrator')->except('index', 'login');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $token = $request->header('userToken');
        // $UserData = User::where('remember_token', $token)->first();
        $UserData = $request->input('UserData');
        // $UserLv = $UserData->Lv;
        $Lv = $UserData->Lv;
        $id = $UserData->id;
        // dd($id);
        if ($Lv === 3) {
            $users = User::all();
            return $users;
        } else {
            $user = User::find($id);
            return $user;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $token = $request->header('userToken');
        // $UserLv = User::where('remember_token', $token)->first()->Lv;
        // $UserData = $request->input('UserData');
        // $UserLv = $UserData->Lv;
        // if ($UserLv === 3) {
        $Lv = $request->Lv;
        $username = $request->username;
        $password = $request->password;
        $hash = password_hash($password, PASSWORD_DEFAULT);
        User::create(['name' => $username, 'password' => $hash, 'Lv' => $Lv, 'remember_token' => 'new user']);
        return response()->json(['message' => 'create succesfully'], 201);
        // } else {
        //     return response()->json(['message' => 'Unauthorized', 'reason' => 'Permission denied'], 403);
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return $user;
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
        // dd($request->username, $id);
        // $token = $request->header('userToken');
        // $UserLv = User::where('remember_token', $token)->first()->Lv;
        // $UserData = $request->input('UserData');
        // $UserLv = $UserData->Lv;
        // if ($UserLv === 3) {
        if (isset($request->username)) {
            $username = $request->username;
            $update = User::find($id)->update(['name' => $username]);
        }
        if (isset($request->password)) {
            $password = $request->password;
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $update = User::find($id)->update(['password' => $hash]);
        }
        if (isset($request->Lv)) {
            $Lv = $request->Lv;
            $update = User::find($id)->update(['Lv' => $Lv]);
        }
        return response()->json(['message' => 'update successfully'], 200);
        // } else {
        //     return response()->json(['message' => 'Unauthorized', 'reason' => 'Permission denied'], 403);
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $token = $request->header('userToken');
        // $UserLv = User::where('remember_token', $token)->first()->Lv;
        // $UserData = $request->input('UserData');
        // $UserLv = $UserData->Lv;
        // if ($UserLv === 3) {
        User::find($id)->delete();
        return response()->json(['message' => 'delete successfully'], 200);
        // } else {
        //     return response()->json(['message' => 'Unauthorized', 'reason' => 'Permission denied'], 403);
        // }
    }
    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;
        $UserData = User::where('name', $username)->first();
        if (isset($UserData)) {
            // dd('hello');
            $dbPassword = $UserData->password;
            if (password_verify($password, $dbPassword)) {
                do {                                #避免token重複
                    // $token = rand(1, 3);
                    $token = Str::random(15);
                    $tokenCheck = User::where('remember_token', $token)->first();
                    if (isset($tokenCheck)) {
                        $sameToken = true;
                    } else {
                        $sameToken = false;
                    }
                } while ($sameToken);
                User::where('name', $username)->update(['remember_token' => $token]);
                return response()->json(['message' => 'login successfully'], 200)->header('userToken', $token);
            } else {
                return response()->json(['message' => 'bad request', 'reason' => 'Username or password false'], 400);
            }
        } else {
            return response()->json(['message' => 'bad request', 'reason' => 'Username or password false'], 400);
        }
    }
}
