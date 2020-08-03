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
    public function __construct()
    {
        $this->middleware('administrator')->except('index', 'borrow', 'returnBook');
    }
    public function index(Request $request)
    {
        $UserData = $request->input('UserData');
        // $token = $request->header('userToken');
        // $UserData = User::where('remember_token', $token)->first();
        $Lv = $UserData->Lv;
        // dd($Log);
        if ($Lv < 3) {
            $Log = $UserData->BorrowLog->sortbydesc('borrow_time');
            return $Log;
        } else {
            $Log = BorrowLog::all()->sortbydesc('borrow_time');
            return $Log;
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
    public function UserBorrowLog($UserId)
    {
        $BorrowLog = User::find($UserId);
        if (isset($BorrowLog)) {
            // $BorrowLog->BorrowLog->sortbydesc('borrow_time');
            $BorrowLog->BorrowLog->where('borrow_log')->flatten();
            // dd($BorrowLog);
            return $BorrowLog;
        } else {
            return response()->json(['message' => 'bad request', 'reason' => 'User id ls false'], 400);
        }
    }
    public function BookBorrowLog($BookId)
    {
        $BorrowLog = Book::find($BookId);
        if (isset($BorrowLog)) {
            $BorrowLog->BorrowLog->sortbydesc('borrow_time');
            return $BorrowLog;
        } else {
            return response()->json(['message' => 'bad request', 'reason' => 'Book id ls false'], 400);
        }
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
        $UserData = $request->input('UserData');
        $Lv = $UserData->Lv;
        #若是管理者身份 可輸入UserId幫使用者借書
        if ($Lv == 3 && isset($request->UserId)) {
            $UserId = $request->UserId;
            $UserData = User::find($UserId);
            if (!$UserData) {
                return response()->json(['message' => 'bad request', 'reason' => 'User id ls false'], 400);
            }
        }
        $BorrowTime = $UserData->BorrowLog->where('give_back', '0')->sortby('borrow_time')->first();
        $NoGiveBack = $UserData->BorrowLog->where('give_back', '0')->count();
        $UserId = $UserData->id;
        $Borrower = $UserData->name;

        $BookId = $request->BookId;
        $BookData = Book::where('id', $BookId)->first();
        if (!$BookData) {
            return response()->json(['message' => 'bad request', 'reason' => 'Book id ls false'], 400);
        }
        $Bookname = $BookData->bookname;
        $BookStatus = $BookData->status;
        #Lv1可借3本，Lv2能借5本
        if ($NoGiveBack >= 3 && $Lv == 1) {
            return response()->json(['message' => 'bad request', 'reason' => 'only borrow 3 books'], 400);
        }
        if ($NoGiveBack >= 5 && $Lv == 2) {
            return response()->json(['message' => 'bad request', 'reason' => 'only borrow 5 books'], 400);
        }
        #Lv1可借一週，Lv2能借兩週，新書能借5天（新增的一個月內為新書）
        if (isset($BorrowTime->borrow_time)) {
            $BorrowTime = $BorrowTime->borrow_time;
            if ($Lv == 1) {
                $OutTime = date('Y-m-d H:i:s', strtotime('+1 week', strtotime($BorrowTime)));
            }
            if ($Lv == 2) {
                $OutTime = date('Y-m-d H:i:s', strtotime('+2 week', strtotime($BorrowTime)));
            }
            if (date('Y-m-d H:i:s') < $BookData->created_at->addMonth()->toDateString()) {
                $OutTime = date('Y-m-d H:i:s', strtotime('+5 day', strtotime($BorrowTime)));
            }
            if ($OutTime < date('Y-m-d  H:i:s')) {
                return response()->json(['message' => 'bad request', 'reason' => 'you have same book not give back over time'], 400);
            }
        }

        #判斷書有沒有被借走
        if (isset($BookData) && $BookStatus == 1) {
            BorrowLog::create(['user_id' => $UserId, 'bookname' => $Bookname, 'book_id' => $BookId, 'give_back' => false]);
            $BookData->update(['Borrower' => $Borrower, 'status' => false]);
            return response()->json(['message' => 'borrow successfully'], 200);
        } else {
            return response()->json(['message' => 'bad request', 'reason' => 'book not found or already borrowed'], 400);
        }
    }
    public function returnBook(Request $request)
    {
        // $token = $request->header('userToken');
        // $UserData = User::where('remember_token', $token)->first();
        $UserData = $request->input('UserData');
        $Lv = $UserData->Lv;
        #若是管理者身份 可輸入UserId幫使用者借書
        if ($Lv == 3 && isset($request->UserId)) {
            $UserId = $request->UserId;
            $UserData = User::find($UserId);
        }
        if (!$UserData) {
            return response()->json(['message' => 'bad request', 'reason' => 'User id false'], 400);
        }
        $BookId = $request->BookId;
        $BorrowLog = $UserData->BorrowLog->where('book_id', $BookId)->sortbydesc('borrow_time')->first();
        // dd($BorrowLog);
        if (!$BorrowLog) {
            return response()->json(['message' => 'bad request', 'reason' => 'book id false'], 400);
        }
        $GiveBack = $BorrowLog->give_back;
        if ($GiveBack == 0) {
            $BorrowLog->update(['give_back' => true, 'give_back_time' => date('Y-m-d H:i:s')]);
            Book::find($BookId)->update(['Borrower' => null, 'status' => true]);
            return response()->json(['message' => 'give back successfully'], 200);
        } else {
            return response()->json(['message' => 'bad request', 'reason' => 'book id false'], 400);
        }
    }
}
