<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Book;
// use App\BorrowLog;
use App\User;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('administrator')->except('index', 'show');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $index = Book::all();
        return $index;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $token = $request->header('userToken');
        // $UserLv = User::where('remember_token', $token)->first()->Lv;
        // $UserData = $request->input('UserData');
        // $UserLv = $UserData->Lv;
        // if ($UserLv === 3) {
        $bookname = $request->bookname;
        Book::create(['bookname' => $bookname, 'status' => true]);
        return response()->json(['message' => 'create successfully'], 201);
        // } else {
        //     return response()->json(['message' => 'Unauthorized', 'reason' => 'Permission denied'], 403);
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::find($id);
        return $book;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $token = $request->header('userToken');
        // $UserLv = User::where('remember_token', $token)->first()->Lv;
        // $UserData = $request->input('UserData');
        // $UserLv = $UserData->Lv;
        // if ($UserLv === 3) {
        $update = $request->bookname;
        Book::find($id)->update(['bookname' => $update]);
        return response()->json(['message' => 'update successfully'], 200);
        // } else {
        //     return response()->json(['message' => 'Unauthorized', 'reason' => 'Permission denied'], 403);
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // $token = $request->header('userToken');
        // $UserData = $request->input('UserData');
        // $UserLv = $UserData->Lv;
        // $UserLv = User::where('remember_token', $token)->first()->Lv;
        // if ($UserLv === 3) {
        $delete = Book::find($id)->delete();
        return response()->json(['message' => 'delete successfully'], 200);
        // } else {
        //     return response()->json(['message' => 'Unauthorized', 'reason' => 'Permission denied'], 403);
        // }
    }
}
