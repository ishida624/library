<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\BorrowLog;
use App\User;
use App\Book;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $token = $request->header('userToken');
        $UserData = User::where('remember_token', $token)->first();
        $Lv = $UserData->Lv;
        if ($Lv < 3) {
            $Log = $UserData->BorrowLog->sortbydesc('borrow_time');
            return $Log;
        } else {
            $Log = BorrowLog::all()->sortbydesc('borrow_time');
            return $Log; //
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
    public function borrow(Request $request)
    {
        #book status : true  書還在  ，false 書被借
        #give back  :  true 已還書 ，false 書未還
        $token = $request->header('userToken');
        $UserData = User::where('remember_token', $token)->first();
        $NoGiveBack = $UserData->BorrowLog->where('give_back', '0')->count();
        // dd($NoGiveBack);
        $UserId = $UserData->id;
        $Lv = $UserData->Lv;
        $BookId = $request->id;

        if ($NoGiveBack >= 3 && $Lv == 1) {
            return response()->json(['message' => 'bad request', 'reason' => 'only borrow 3 books'], 400);
        }
        if ($NoGiveBack >= 5 && $Lv == 2) {
            return response()->json(['message' => 'bad request', 'reason' => 'only borrow 5 books'], 400);
        }

        $BookData = Book::where('id', $BookId)->first();
        $Bookname = $BookData->bookname;
        $BookStatus = $BookData->status;
        // dd($Bookname);
        if (isset($BookData) && $BookStatus == 1) {
            BorrowLog::create(['user_id' => $UserId, 'bookname' => $Bookname, 'book_id' => $BookId, 'give_back' => false]);
            $BookData->update(['status' => false]);
            return response()->json(['message' => 'borrow successfully'], 200);
        } else {
            return response()->json(['message' => 'bad request', 'reason' => 'book not found or it is borrowed'], 400);
        }
    }
    public function returnBook(Request $request)
    {
        $token = $request->header('userToken');
        $UserData = User::where('remember_token', $token)->first();
        $BookId = $request->id;
        $BookData = $UserData->BorrowLog->where('book_id', $BookId)->sortbydesc('borrow_time')->first();
        $GiveBack = $BookData->give_back;
        // dd($BookData);
        if (isset($BookData) && $GiveBack == 0) {
            $BookData->update(['give_back' => true, 'give_back_time' => date('Y-m-d H:i:s')]);
            Book::find($BookId)->update(['status' => true]);
            return response()->json(['message' => 'give back successfully'], 200);
        } else {
            return response()->json(['message' => 'bad request', 'reason' => 'book id false'], 400);
        }
    }
}
