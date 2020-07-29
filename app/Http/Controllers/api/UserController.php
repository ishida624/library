<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UpdateRequest;
// use Illuminate\Http\Response;
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

    public function store(UserRequest $request)
    {
        $Lv = $request->Lv;
        $username = $request->username;
        $password = $request->password;
        $SameUsername = User::where('name', $username)->first();
        if (isset($SameUsername)) {
            return response()->json(['message' => 'bad request', 'reason' => 'this name was used'], 403);
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        User::create(['name' => $username, 'password' => $hash, 'Lv' => $Lv, 'remember_token' => 'new user']);
        return response()->json(['message' => 'create succesfully'], 201);
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
        if (isset($user)) {
            return $user;
        } else {
            return response()->json(['message' => 'bad request', 'reason' => 'User id ls false'], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        // dd($request->username, $id);
        // $token = $request->header('userToken');
        // $UserLv = User::where('remember_token', $token)->first()->Lv;
        // $UserData = $request->input('UserData');
        // $UserLv = $UserData->Lv;
        // if ($UserLv === 3) {
        $user = User::find($id);
        if (isset($user)) {
            if (isset($request->username)) {
                $username = $request->username;
                $user->update(['name' => $username]);
            }
            if (isset($request->password)) {
                $password = $request->password;
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $user->update(['password' => $hash]);
            }
            if (isset($request->Lv)) {
                $Lv = $request->Lv;
                $user->update(['Lv' => $Lv]);
            }
        } else {
            return response()->json(['message' => 'bad request', 'reason' => 'User id ls false'], 400);
        }
        return response()->json(['message' => 'update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = User::find($id);
        if (isset($delete)) {
            $delete->delete();
            return response()->json(['message' => 'delete successfully'], 200);
        } else {
            return response()->json(['message' => 'bad request', 'reason' => 'User id ls false'], 400);
        }
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
                do {                                #隨機生成token，do while回圈避免token重複
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
