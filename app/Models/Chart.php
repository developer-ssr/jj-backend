<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'series' => 'array',
        'categories' => 'array'
    ];

    public static function getExpData($legend, $value, $prime) 
    {
        $choices = [];
        switch ($legend) {
            case 't6':
                $choices = [
                    'I DO NOT USE the HealthCaringTM Conversations ISIGHT Model',
                    'NO, I DO NOT agree with HealthCaringTM Conversations ISIGHT Model',
                    'YES, I agree with HealthCaringTM Conversations ISIGHT Model'
                ];
                break;
            case 't7':
                $choices = [
                    'None',
                    'About 25%',
                    'About 50%',
                    'About 75%',
                    'Virtually all of my patients'
                ];
                break;
            default:
                break;
        }
        $index = $prime - 1;
        $tmp_data = [
            'index' => $prime,
            'prime' => $choices[$index],
            'equivalent' => $choices[$index],
            'data' => [
                [
                    "target" => '',
                    "equivalent" => '',
                    "index" => $prime,
                    "value" => 1,//$prime,
                    "selected" => $value == $prime ? true: false
                ]
            ],
        ]; 
        return $tmp_data;
    }
}
