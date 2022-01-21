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

    public static function getExpData($legend, $record, $prime) 
    {
        $choices = [];
        switch ($legend) {
            case 't2':
                $choices = [
                    'No treatment recommended',
                    'Refractive only treatment: you fit only with single vision solutions (glasses or contact lenses)',
                    'Myopia management treatment: you fit with myopia management treatments (Ortho-K, multifocal soft contacts or glasses, myopia control soft contacts or glasses, atropine)'
                ];
                $value = $record->meta['query']['b3_'.$prime];
                break;
            case 't6':
                $choices = [
                    'I DO NOT USE the HealthCaringTM Conversations ISIGHT Model',
                    'NO, I DO NOT agree with HealthCaringTM Conversations ISIGHT Model',
                    'YES, I agree with HealthCaringTM Conversations ISIGHT Model'
                ];
                $value = $record->data[$legend];
                break;
            case 't7':
                $choices = [
                    'None',
                    'About 25%',
                    'About 50%',
                    'About 75%',
                    'Virtually all of my patients'
                ];
                $value = $record->data[$legend];
                break;
            case 't11':
                $choices = [
                    'Please indicate what you like about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:'
                ];
                $value = 1;//$record->meta['query']['d1'];
                break;
            case 't12':
                $choices = [
                    'Please indicate what you dislike about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:'
                ];
                $value = 1;//$record->meta['query']['d2'];
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

    public static function getCountry($code) {
        switch ($code) {
            case 'us':
                $country = 'USA';
                break;
            case 'sg':
                $country = 'Singapore';
                break;
            case 'hk':
                $country = 'Hongkong';
                break;
            case 'ca':
                $country = 'Canada';
                break;
            default:
                $country = '';
                break;
        }
        return $country;
    }
}
