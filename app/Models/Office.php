<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function getLinkAttribute()
    {
        
    }

    public function emails()
    {
        return $this->hasMany(Email::class, 'email', 'email');
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }
}
