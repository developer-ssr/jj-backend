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
        $http = Http::get($this->act_api . "survey_id=242&id={$request->id}");
        $data = [
            't3' => json_decode($http->body(), true)
        ];
        
        $http = Http::get($this->act_api . "survey_id=243&id={$request->id}");
        $data['t4'] = json_decode($http->body(), true);

        $http = Http::get($this->act_api . "survey_id=244&id={$request->id}");
        $data['t5'] = json_decode($http->body(), true);

        $http = Http::get($this->act_api . "survey_id=245&id={$request->id}");
        $data['t8'] = json_decode($http->body(), true);

        $http = Http::get($this->act_api . "survey_id=246&id={$request->id}");
        $data['t9'] = json_decode($http->body(), true);

        $http = Http::get($this->act_api . "survey_id=247&id={$request->id}");
        $data['t10'] = json_decode($http->body(), true);

        $data['t6'] = $request->c1;
        $data['t7'] = $request->c2;

        $record = Record::create([
            'participant_id' => $request->id,
            'country' => 'us',
            'ip' => $request->ip(),
            'data' => $data,
            'meta' => [
                'url' => $request->url(),
                'query' => $request->all(),
                'office' => $request->b2_3
            ],
            'created_at' => now()->addDays(5),
            'updated_at' => now()->addDays(5)
        ]);
        return redirect('https://fluent.splitsecondsurveys.co.uk/engine/complete/V3n?' . http_build_query($request->all()));
    }
}
