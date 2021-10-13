<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use function PHPSTORM_META\map;

class Link extends Model
{
    use HasFactory, SoftDeletes ;
    
    protected $guarded = [];

    protected $appends = [
        'link',
        'created',
        'taken',
        'link_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $timestamps = false;

    protected $with = ['office'];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function getLinkAttribute()
    {
        return "https://fluent.splitsecondsurveys.co.uk/engine/entry/V3n/?id=" . $this->link_id;
    }

    public function getLinkIdAttribute()
    {
        
        $codes = [
            840 => 'US',
            702 => 'SG',
            124 => 'CA',
            344 => 'HK'
        ];
        return $codes[$this->country_code] . "-" . $this->created_at->format("dmy-Hi") . "-" . $this->uid;
    }

    public function getCreatedAttribute()
    {
        return $this->created_at->format('M d, Y h:i:s A');
    }

    public function getTakenAttribute()
    {
        return Record::where('participant_id', $this->link_id)->exists() ? 'YES' : 'NO';
    }
}
