<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'book';
    // public $incrementing = 'false';
    public $primaryKey = 'id';
    // public $timestamps = false;
    protected $fillable = [
        'bookname', 'Borrower', 'status',
    ];
    public function BorrowLog()
    {
        return $this->hasMany('App\BorrowLog');
    }
}
