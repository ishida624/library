<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BorrowLog extends Model
{
    protected $table = 'borrow_log';
    // public $incrementing = 'false';
    public $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'bookname', 'book_id', 'status', 'user_id', 'give_back', 'give_back_time'
    ];
}
