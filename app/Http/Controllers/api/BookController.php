<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BookRequest;
use App\Book;

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
    public function store(BookRequest $request)
    {
        $bookname = $request->bookname;
        Book::create(['bookname' => $bookname, 'status' => true]);
        return response()->json(['message' => 'create successfully'], 201);
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
        $Book = Book::find($id);
        if (isset($Book)) {
            return $Book;
        } else {
            return response()->json(['message' => 'bad request', 'reason' => 'book id ls false'], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(BookRequest $request, $id)
    {
        $update = $request->bookname;
        $Book = Book::find($id);
        if (isset($Book)) {
            $Book->update(['bookname' => $update]);
            return response()->json(['message' => 'update successfully'], 200);
        } else {
            return response()->json(['message' => 'bad request', 'reason' => 'book id ls false'], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $token = $request->header('userToken');
        // $UserData = $request->input('UserData');
        // $UserLv = $UserData->Lv;
        // $UserLv = User::where('remember_token', $token)->first()->Lv;
        // if ($UserLv === 3) {
        $Book = Book::find($id);
        if (isset($Book)) {
            $Book->delete();
            return response()->json(['message' => 'delete successfully'], 200);
        } else {
            return response()->json(['message' => 'bad request', 'reason' => 'book id ls false'], 400);
        }
        // } else {
        //     return response()->json(['message' => 'Unauthorized', 'reason' => 'Permission denied'], 403);
        // }
    }
}
