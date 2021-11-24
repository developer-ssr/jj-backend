<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\Chart;
use App\Exports\CsvExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function download(Request $request, $id, $summary) 
    {
        $chart = Chart::find($id);
        $all = json_decode($request->all);
        if ($all) {
            $legends = collect($chart->series)->pluck('name')->toArray();
        }else {
            $legends = json_decode($request->legends);
        }
        
        if ($summary == 'summary') {
            $data = $this->exportSummary($chart, $legends);
        }else {
            $data = $this->exportRespondent($chart, $legends);
        }
        $headers = ['Dimension','','','Question Text','','','Answer Value'];
        $data = collect($data)->prepend($headers)->toArray(); 

        return Excel::download(CsvExport::new($data), "download.xlsx");
    }

    public function exportSummary($chart, $legends) 
    {
        $results = [];
        foreach ($legends as $legend) {
            $tmp_data = collect($chart->series)->firstWhere('name', $legend);
            unset($tmp_data['data'][count($tmp_data['data']) - 1]['percentage']);
            $results[] = Arr::flatten($tmp_data['data'][count($tmp_data['data']) - 1]);
        }
        return $results;
    }

    public function exportRespondent($chart, $legends) 
    {
        $tmp_results = [];
        $headers = [];
        foreach ($legends as $legend) {
            $tmp = Str::of($legend)->explode('_');
            $t = Str::lower($tmp[0]);
            $prime = $tmp[1];
            $record_ids = collect([]);
            $serires = collect($chart->series)->firstWhere('name', $legend);
            $tmp_data = [];
            foreach ($serires['data'] as $data) {
                $headers[$t] = collect([$data['dimension'], $tmp[0], $tmp[0], $data['question']]);
                if (count($tmp_data) < count($data)) {
                    $tmp_data = $data;
                }
                $record_ids = $record_ids->merge($data['record_ids']);
            }
            $records = Record::whereIn('id', $record_ids->unique()->toArray())->get();
            $tmp_results[$t][] = $this->getData($records, $t, $prime, $tmp_data);
            while (count($tmp_data['targets']) < 5) {
                $tmp_data['targets'][] = '';
            }
            $tmp_data['targets'][] = 'TOTAL Completes';       
            $headers[$t] = $headers[$t]->merge($tmp_data['targets'] ?? [])->toArray();
            
        }
        $results = [];
        $header_keys = collect($headers)->keys()->toArray();
        natsort($header_keys);
        foreach ($header_keys as $ts) {
            $results[] = $headers[$ts];
            foreach ($tmp_results[$ts] as $value) {
                $results[] = $value;
            }
        }
        return $results;
    }
    public function getData($records, $t, $prime, $data) {
        $tmp_data = [];
        $tmp_result = collect([$data['dimension'] ?? '', Str::upper($t), $prime, $data['question'] ?? '']);
        $data_count = 0;
        foreach ($records as $record) {
            if (isset($record->data[$t]['responses'])) {
                $tmp_data = collect($record->data[$t]['responses'][0]['primes'])->firstWhere('index', $prime);
                $data_count = count($tmp_data);
            } else {
                $tmp_data = null;
            }

            if ($tmp_data != null) {
                foreach ($tmp_data['data'] as $t_key => $tmp) {
                    if (!isset($tmp_result[4+$t_key])) {
                        $tmp_result[3] = $tmp_data['equivalent'];
                        $tmp_result[4+$t_key] = 0;
                    }
                    if ($tmp['selected']) {
                        $tmp_result[4+$t_key] += 1;
                    }
                }
            }
        }
        
        $i = 0;
        while (count($tmp_result) < 9) {
            $i++;
            $tmp_result[5+$data_count+$i] = '';
        }
        $tmp_result[5+$data_count] = count($records); //total
        return $tmp_result->toArray();
    }
}
