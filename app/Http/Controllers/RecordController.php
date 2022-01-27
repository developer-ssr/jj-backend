<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecordController extends Controller
{
    protected $act_api = 'https://ast.splitsecondsurveys.co.uk/api/record/?';

    public function complete(Request $request)
    {

        $country_t5 = [
            'us' => '244',
            'sg' => '981',
            'hk' => '1092',
            'ca' => '983'
        ];
        $country_t3 = [
            'us' => '242',
            'sg' => '242',
            'hk' => '1087',
            'ca' => '242'
        ];
        $country_t4 = [
            'us' => '243',
            'sg' => '243',
            'hk' => '1088',
            'ca' => '243'
        ];
        $country_t8 = [
            'us' => '245',
            'sg' => '245',
            'hk' => '1089',
            'ca' => '245'
        ];
        $country_t9 = [
            'us' => '246',
            'sg' => '246',
            'hk' => '1090',
            'ca' => '246'
        ];
        $country_t10 = [
            'us' => '247',
            'sg' => '247',
            'hk' => '1091',
            'ca' => '247'
        ];
        $country_link = [
            'us' => 'V3n',
            'sg' => 'V3n',
            'hk' => '5vw',
            'ca' => 'V3n'
        ];

        $country = $request->country ?? 'us';

        $http = Http::get($this->act_api . "survey_id=" . $country_t3[$country] . "&id={$request->id}");
        $data = [
            't3' => json_decode($http->body(), true)
        ];
        
        $http = Http::get($this->act_api . "survey_id=" . $country_t4[$country] . "&id={$request->id}");
        $data['t4'] = json_decode($http->body(), true);

        $http = Http::get($this->act_api . "survey_id=" . $country_t5[$country] . "&id={$request->id}");
        $data['t5'] = json_decode($http->body(), true);

        $http = Http::get($this->act_api . "survey_id=" . $country_t8[$country] . "&id={$request->id}");
        $data['t8'] = json_decode($http->body(), true);

        $http = Http::get($this->act_api . "survey_id=" . $country_t9[$country] . "&id={$request->id}");
        $data['t9'] = json_decode($http->body(), true);

        $http = Http::get($this->act_api . "survey_id=" . $country_t10[$country] . "&id={$request->id}");
        $data['t10'] = json_decode($http->body(), true);

        $data['t6'] = $request->c1;
        $data['t7'] = $request->c2;

        $record = Record::create([
            'participant_id' => $request->id,
            'country' => strtolower(explode("-", $request->id)[0]),
            'ip' => $request->ip(),
            'data' => $data,
            'meta' => [
                'url' => $request->url(),
                'query' => $request->all(),
                'office' => $request->b2_3
            ],
            // 'created_at' => now()->addMonth(),
            // 'updated_at' => now()->addMonth()
        ]);
        return redirect("https://fluent.splitsecondsurveys.co.uk/engine/complete/" . $country_link[$country] . "?" . http_build_query($request->all()));
    }
}
