<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Books extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name', 
        'publisher',
        'isbn', 
        'category',
        'sub_category', 
        'description',
        'pages', 
        'image',
        'added_by'
    ];   
    public function bookLoan()
    {
        return $this->hasMany(BookLoan::class, 'book_id');
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, BookLoan::class, 'book_id', 'id', 'id', 'user_id');
    }
}
