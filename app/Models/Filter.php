<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filter extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array'
    ];

    protected $with = ['office'];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
