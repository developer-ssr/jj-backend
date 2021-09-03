<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{

    private $source = "https://eaat2.splitsecondsurveys.co.uk/api/record/";

    public function index()
    {
        $request = [
            'id' => 'Joeanna1'
        ];
        $codes = [
            'us' => [
                't2' => 'iM3viwJnSo',
                't3' => '2y9ChfQwm1',
                't4' => '23oaNTciU1',
                't7' => 'MEqJVmfUuv',
                't8' => 'P7q91E4oL8',
                't9' => 'PA3fuH8vBF'
            ]
        ];
        $data = [];
        foreach ($codes['us'] as $key => $code) {
            $data['data'][$key] = $this->getFastData($code, $request['id']);
        }
        dd($data);
    }

    private function getDataSummary($data)
    {
        //dump($data['data']);
        // $targets = collect(collect($data['data'])->first()['targets'])->pluck('target_value')->mapWithKeys(function($item) {
        //     return [$item  => 0];
        // })->toArray();
        $responses = collect($data['data'])->sortBy('prime_id')->map(function($response) {
            return [
                'id' => $response['prime_id'],
                'name' => $response['prime_name'],
                'choice' => $response['choice']['target_value']
            ];
        })->values()->toArray();
        $targets = collect(collect($data['data'])->first()['targets'])->pluck('target_value')->toArray();
        return [
            'targets' => $targets,
            'responses' => $responses
        ];
    }

    private function getFastData($code, $id)
    {
        $http = Http::get($this->source . $code . "/{$id}");
        $data = json_decode($http->body(), true);
        return $this->getDataSummary($data);
    }
}
