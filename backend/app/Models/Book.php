<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'publisher',
        'published_year',
        'edition',
        'description',
        'total_copies',
        'available_copies',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'published_year'   => 'integer',
        'total_copies'     => 'integer',
        'available_copies' => 'integer',
    ];
}
