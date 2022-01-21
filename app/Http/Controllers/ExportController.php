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
            $headers = ['Dimension','','','Question Text','','','Answer Value'];
            $data = collect($data)->prepend($headers)->toArray(); 
        }elseif($summary == 'respondent') {
            $data = $this->exportRespondent($chart, $legends);
            $headers = ['Dimension','','','Question Text','','','Answer Value'];
            $data = collect($data)->prepend($headers)->toArray(); 
        } else {
            $tmp_data = $this->exportTracker($chart, $legends);
            $headers = collect(['Respondent ID','Country','Name','Email Address'])->merge($tmp_data['headers'])->toArray();;
            $data = collect($tmp_data['results'])->prepend($headers)->toArray(); 
        }
        
        return Excel::download(CsvExport::new($data), $chart->title."_".$summary.".xlsx");
    }

    public function exportSummary($chart, $legends) 
    {
        $tmp_results = [];
        $headers = [];
        foreach ($legends as $legend) {
            $tmp = Str::of($legend)->explode('_');
            $t = Str::lower($tmp[0]);//t3
            $prime = $tmp[1];
            $record_ids = collect([]);
            $series = collect($chart->series)->firstWhere('name', $legend);
            $tmp_data = [];
            foreach ($series['data'] as $data) {
                $headers[$t] = collect([$data['dimension'], $tmp[0], $tmp[0], $data['question']]);
                if (count($tmp_data) < count($data)) {
                    $tmp_data = $data; //find proper data
                }
                $record_ids = $record_ids->merge($data['record_ids']);
            }
            $records = Record::whereIn('id', $record_ids->unique()->toArray())->get();
            $tmp_results[$t][] = $this->getData($records, $t, $prime, $tmp_data, 'summary');
            while (count($tmp_data['targets']) < 5) {//assign spacing
                $tmp_data['targets'][] = '';
            }
            $tmp_data['targets'][] = 'TOTAL Points';   
            $tmp_data['targets'][] = 'Max Points (max value * total completes)';    
            $tmp_data['targets'][] = 'Invite 1';
            $headers[$t] = $headers[$t]->merge($tmp_data['targets'] ?? [])->toArray();
            
        }
        $results = [];
        $header_keys = collect($headers)->keys()->toArray();
        natsort($header_keys);//sort list
        foreach ($header_keys as $ts) {
            $results[] = $headers[$ts];//assign header
            foreach ($tmp_results[$ts] as $value) {
                $results[] = $value;
            }
        }
        return $results;
    }

    public function exportRespondent($chart, $legends) 
    {
        $tmp_results = [];
        $headers = [];
        foreach ($legends as $legend) {
            $tmp = Str::of($legend)->explode('_');
            $t = Str::lower($tmp[0]);//t3
            $prime = $tmp[1];
            $record_ids = collect([]);
            $series = collect($chart->series)->firstWhere('name', $legend);
            $tmp_data = [];
            foreach ($series['data'] as $data) {
                $headers[$t] = collect([$data['dimension'], $tmp[0], $tmp[0], $data['question']]);
                if (count($tmp_data) < count($data)) {
                    $tmp_data = $data; //find proper data
                }
                $record_ids = $record_ids->merge($data['record_ids']);
            }
            $records = Record::whereIn('id', $record_ids->unique()->toArray())->get();
            $tmp_results[$t][] = $this->getData($records, $t, $prime, $tmp_data, 'respondent');
            while (count($tmp_data['targets']) < 5) {//assign spacing
                $tmp_data['targets'][] = '';
            }
            $tmp_data['targets'][] = 'TOTAL Completes';       
            $headers[$t] = $headers[$t]->merge($tmp_data['targets'] ?? [])->toArray();
            
        }
        $results = [];
        $header_keys = collect($headers)->keys()->toArray();
        natsort($header_keys);//sort list
        foreach ($header_keys as $ts) {
            $results[] = $headers[$ts];//assign header
            foreach ($tmp_results[$ts] as $value) {
                $results[] = $value;
            }
        }
        return $results;
    }
    public function getData($records, $t, $prime, $data, $summary) {
        $tmp_data = [];
        $tmp_result = collect([$data['dimension'] ?? '', Str::upper($t), $prime, $data['question'] ?? '']);
        $data_count = 0;
        $total = 0;
        foreach ($records as $record) {
            switch ($t) {
                case 't2':
                case 't6':
                case 't7':
                case 't11':
                case 't12':
                    $tmp_data = Chart::getExpData($t, $record, $prime);
                    break;
                default:
                    if (isset($record->data[$t]['responses'])) {
                        $tmp_data = collect($record->data[$t]['responses'][0]['primes'])->firstWhere('index', $prime);
                        $data_count = count($tmp_data['data']);
                    } else {
                        $tmp_data = null;
                    }                    
                    break;
            }

            if ($tmp_data != null) {
                foreach ($tmp_data['data'] as $t_key => $tmp) {
                    if (!isset($tmp_result[4+$t_key])) {
                        $tmp_result[3] = $tmp_data['equivalent'];//prime
                        $tmp_result[4+$t_key] = 0;//initialize
                    }
                    if ($tmp['selected']) {
                        if ($summary == 'summary') {
                            $tmp_result[4+$t_key] += $tmp['value'];
                            // $tmp_result[4+$t_key] += ($t_key + 1);
                            $total += $tmp['value']; //total using value
                            // $total += ($t_key + 1); //total using key
                        }else {
                            $tmp_result[4+$t_key] += 1;//increment selected
                        }
                        
                    }
                }
            }
        }
        
        $i = 0;
        while (count($tmp_result) < 9) {
            $i++;
            $tmp_result[5+$data_count+$i] = ''; //assign spacing
        }
        
        if ($summary == 'summary') {
            if ($data_count > 2) {
                $max_point = count($records) * $data_count; //max point
            }else {
                $max_point = count($records); //max point
            }
            if ($max_point == 0) {
                $segment1 = 0;
            }else {
                $segment1 = ceil(($total / $max_point) * 100); //segment 1
            }
            
        }else {
            $total = count($records);
        }
        $tmp_result[5+$data_count] = $total; //total
        $tmp_result[] = $max_point ?? '';
        $tmp_result[] = $segment1 ?? '';
        /* $tmp_result[6+$data_count] = $max_point ?? '';
        $tmp_result[7+$data_count] = $segment1 ?? ''; */
        

        return $tmp_result->toArray();
    }

    public function exportTracker($chart, $legends) 
    {
        $results = [];
        $headers = [];
        $record_ids = collect([]);

        /* $headers['T2_1'] = 'b3_1';
        $headers['T2_2'] = 'b3_2';
        $headers['T2_3'] = 'b3_3'; */

        ksort($legends, 4);// 4 = SORT_NATURAL

        foreach ($legends as $legend) {
            $series = collect($chart->series)->firstWhere('name', $legend);
            foreach ($series['data'] as $data) { //loop for segment
                $record_ids = $record_ids->merge($data['record_ids']);
            }    
            switch ($legend) {
                case 'T2_1':
                    $headers[$legend] = 'b3_1';
                    break;
                case 'T2_2':
                    $headers[$legend] = 'b3_1';
                    break;
                case 'T2_3':
                    $headers[$legend] = 'b3_1';
                    break;
                case 'T11_1':
                    $headers[$legend] = 'd1';
                    break;
                case 'T12_1':
                    $headers[$legend] = 'd2';
                    break;
                default:
                    $headers[$legend] = '-';
                    break;
            }
            
        }
        
        /* $headers['T11_1'] = 'd1';
        $headers['T12_1'] = 'd2';
        $headers['F1_1'] = 'a1';
        $headers['F1_2'] = 'a1';
        $headers['F1_3'] = 'a1';
        $headers['F1_4'] = 'a1';
        $headers['F1_5'] = 'a1';
        $headers['F2_1'] = 'a2';
        $headers['F2_2'] = 'a2';
        $headers['F2_3'] = 'a2';
        $headers['F2_4'] = 'a2';
        $headers['F2_5'] = 'a2'; */

        $records = Record::whereIn('id', $record_ids->unique()->toArray())->get();
        foreach ($records as $record) {
            $tmp = [
                $record->participant_id,
                Chart::getCountry($record->country),
                $record->meta['query']['b2_1'] ?? '-',
                $record->meta['query']['b2_2'] ?? '-'
            ];
            foreach ($headers as $hkey => $header) {
                $ts = Str::of($hkey)->explode('_');
                $t = Str::lower($ts[0]);//t3
                $prime = $ts[1] ?? null;
                switch ($t) {
                    case 't3':
                    case 't4':
                    case 't5':
                    case 't8':
                    case 't9':
                    case 't10':
                        $responses = collect($record->data[$t]['responses'][0]['primes'])->firstWhere('index', $prime);
                        foreach ($responses['data'] as $reskey => $resdata) {
                            if ($resdata['selected']) {
                                $val = $reskey + 1;
                                break 1;
                            }
                        }
                        break;
                    case 't6':
                    case 't7':
                        if ($prime == $record->data[$t]) {
                            $val = 1;
                        }else {
                            $val = 0;
                        }
                        break;
                    case 't11':
                    case 't12':
                    case 't2':
                        $val = $record->meta['query'][$header];
                        break;
                    default: //f
                        /* if ($record->meta['query'][$header] == $prime) {
                            $val = 1;
                        }else {
                            $val = 0;
                        } */
                        break;
                }
                $tmp[] = $val ?? '-';
            }
            $results[] = $tmp;
        }
        return ['results' => $results, 'headers' => collect($headers)->keys()];
    }
}
