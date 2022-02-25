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
        $all = json_decode($request->all) ?? false;
        $file_name = $request->file_name ?? $chart->title."_".$summary;
        if ($all) {
            if ($summary == 'table_summary' || $summary == 'table_respondent') {
                $legends = ["T2","T3","T4","T5","T6","T7","T8","T9","T10","T11","T12"];
            }else {
                $legends = collect($chart->series)->pluck('name')->toArray();
            }
        }else {
            if ($summary == 'table_summary' || $summary == 'table_respondent') {
                $legends = [];
                $_lg = json_decode($request->legends)[0];
                if (count(explode("_", $_lg)) > 1) {
                    foreach (json_decode($request->legends) as $legend) {
                        $tmp = Str::of($legend)->explode('_');
                        $prime = $tmp[1];
                        if (!isset($legends[$tmp[0]])) {
                            $legends[$tmp[0]] = [];
                            $legends[$tmp[0]][] = $prime;
                        }else {
                            $legends[$tmp[0]][] = $prime;
                        }
                    }
                } else {
                    $legends = json_decode($request->legends);
                    $all = true;
                }
            }else {
                $legends = json_decode($request->legends);
            }
        }

        if ($summary == 'summary') {
            $data = $this->exportSummary($chart, $legends);
            $headers = ['Dimension','','','Question Text','','','Answer Value'];
            $data = collect($data)->prepend($headers)->toArray(); 
        }elseif($summary == 'respondent') {
            $data = $this->exportRespondent($chart, $legends);
            $headers = ['Dimension','','','Question Text','','','Answer Value'];
            $data = collect($data)->prepend($headers)->toArray(); 
        }elseif($summary == 'table_summary') {
            $data = $this->exportTable($chart, $legends, $summary, $all);
        }elseif($summary == 'table_respondent') {
            $data = $this->exportTable($chart, $legends, $summary, $all);
        }elseif($summary == 'tracker_kpi') {
            $data = $this->exportKPI($chart, $legends, $summary, $all);
        } else {
            $tmp_data = $this->exportTracker($chart, $legends);
            $headers = collect(['Respondent ID','Country','Name','Email Address'])->merge($tmp_data['headers'])->toArray();
            $data = collect($tmp_data['results'])->prepend($headers)->toArray(); 
        }
        
        return Excel::download(CsvExport::new($data), $file_name.".xlsx");
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

    public function exportTable($chart, $legends, $summary, $all) {
        $tmp_results = [];
        $headers = [];
        if ($summary == 'table_summary') {
            foreach ($legends as $t_key => $legend) {
                $record_ids = collect([]);
                if ($all == false) {
                    $items = [];
                    $Tn_point = $t_key;
                    $t = Str::lower($t_key);//t3
                    foreach ($legend as $tmp_prime) {
                        $items[] = $tmp_prime;
                    }
                }else {
                    $Tn_point = $legend;
                    $t = Str::lower($legend);//t3
                    $items = Chart::items($t);
                }
                $recorded = false;
                foreach ($items as $i_key => $prime_or_description) {
                    if ($all == false) {
                        $prime = $prime_or_description;
                        $item = $t_key.'_'.$prime; //T3_1
                    }else {
                        $prime = $i_key + 1;
                        $item = $legend.'_'.$prime; //T3_1
                    }
                    
                    $series = collect($chart->series)->firstWhere('name', $item);
                    if ($series == null) {
                        continue;
                    }
                    $tmp_data = [];
                    while ($recorded == false) {
                        foreach ($series['data'] as $data) { //for getting all completes
                            if (count($tmp_data) < count($data)) {
                                $tmp_data = $data; //find proper data
                            }
                            $record_ids = $record_ids->merge($data['record_ids']);
                        }
                        while (count($tmp_data['targets']) < 5) {//assign spacing
                            $tmp_data['targets'][] = '';
                        }
                        //for headers
                        $tmp_data['targets'][] = $tmp_data['percentage']['green']['label'];
                        if (isset($tmp_data['percentage']['orange'])) {
                            $tmp_data['targets'][] = $tmp_data['percentage']['orange']['label'];
                        }
                        if (isset($tmp_data['percentage']['red'])) {
                            $tmp_data['targets'][] = $tmp_data['percentage']['red']['label'];
                        }
                        $headers[$t] = collect([$tmp_data['dimension'], $Tn_point, $Tn_point, $tmp_data['question']]);
                        $headers[$t] = $headers[$t]->merge($tmp_data['targets'])->toArray();//merge T2B headers
                        $records = Record::whereIn('id', $record_ids->unique()->toArray())->get();
                        $recorded = true;
                    }
                    
                    $data = $this->getData($records, $t, $prime, $tmp_data, 'respondent', true);//summary in table
                    $score = $this->getScore($records, $t, $prime);
                    $data[] = $score['percentage']['green']['value'];
                    if (isset($score['percentage']['orange'])) {
                        $data[] = $score['percentage']['orange']['value'];
                    }
                    if (isset($score['percentage']['red'])) {
                        $data[] = $score['percentage']['red']['value'];
                    }
                    $tmp_results[$t][] = $data;
                }
            }

            $results = [];
            $header_keys = collect($headers)->keys()->toArray();
            natsort($header_keys);//sort list
            foreach ($header_keys as $ts) {
                $results[] = $headers[$ts];//assign header
                foreach ($tmp_results[$ts] as $value) {
                    $results[] = $value;
                }
                $results[] = ["",""]; //apply spacing below
            }
        }else { //table_respondent
            $ts = [];
            $scores = [];
            $sample_size = [];
            foreach ($legends as $t_key => $legend) {
                $record_ids = collect([]);
                if ($all == false) {
                    $Tn_point = $t_key;
                    $items = [];
                    foreach ($legend as $tmp_prime) {
                        $items[] = $tmp_prime;
                    }
                    $t = Str::lower($t_key);//t3
                }else {
                    $Tn_point = $t_key;
                    $t = Str::lower($legend);//t3
                    $items = Chart::items($t);
                }
                foreach ($items as $i_key => $prime_or_description) {
                    if ($all == false) {
                        $prime = $prime_or_description;
                        $item = $t_key.'_'.$prime; //T3_1
                    }else {
                        $prime = $i_key + 1;
                        $item = $legend.'_'.$prime; //T3_1
                    }
                    $series = collect($chart->series)->firstWhere('name', $item);
                    if ($series == null) {
                        continue;
                    }
                    $ts[] = $item;
                    if ($i_key == 0) {
                        foreach ($series['data'] as $data) { //for getting all completes
                            $record_ids = $record_ids->merge($data['record_ids']);
                        }
                        $records = Record::whereIn('id', $record_ids->unique()->toArray())->get();
                    }
                    $sample_size[$item] = count($records);
                    $scores[$item] = $this->getScore($records, $t, $prime);
                }
            }
            $tmp_data = $this->exportTracker($chart, $ts);
            $headers = collect(['Respondent ID','Country','Name','Email Address'])->merge($tmp_data['headers'])->toArray();
            $tmp_results = $tmp_data['results'];
            $tmp_results[] = ["",""]; // add space
            ksort($ts, 4);
            $tmps = [
                "Sample size" => [],
                "Items" => [],
                "T2B" => [],
                "MB" => [],
                "B2B" => []
            ];
            
            $colours = [
                "sample" => "Sample size",
                "items" => "Items",
                "green" => "T2B",
                "orange" => "MB",
                "red" => "B2B"
            ];
            foreach (generator($colours) as $color => $tmp) {
                $tmps[$tmp] = ["","","",$tmp];
                foreach (generator($ts) as $key => $t_item) {
                    if ($tmp == 'Items') {
                        $tmps[$tmp][] = $scores[$t_item]['prime'];
                    }elseif($tmp == 'Sample size') {
                        $tmps['Sample size'][] = $sample_size[$t_item];
                    } else {
                        if (isset($scores[$t_item]['percentage'][$color]['value'])) {
                            $tmps[$tmp][] = $scores[$t_item]['percentage'][$color]['value'];
                        } else {
                            $tmps[$tmp][] = '-';
                        }
                    }
                }
            }
            
            $tmp_results[] = $tmps["Sample size"];
            $tmp_results[] = $tmps["Items"];
            $tmp_results[] = $tmps["T2B"];
            $tmp_results[] = $tmps["MB"];
            $tmp_results[] = $tmps["B2B"];

            $results = collect($tmp_results)->prepend($headers)->toArray(); 
        }
        
        return $results;
    }

    public function exportKPI($chart, $legends, $summary, $all) {
        

        $questions = [
            [
                'label' => 'Satisfaction w/ ordering',
                'variables' => ['T3_19']
            ],
            [
                'label' => 'Satisfaction w/ fitting software',
                'variables' => ['T3_4']
            ],
            [
                'label' => 'Satisfaction w/ cust. service',
                'variables' => ['T3_1']
            ],
            [
                'label' => '%ECPs discussing MM as a treatment option with all patients',
                'variables' => ['T7_4','T7_5']
            ],
            [
                'label' => '%ECPs discuss LT health risks w/ parents',
                'variables' => ['T9_1']
            ],
            [
                'label' => '%ECPs who value CSC support',
                'variables' => ['T4_1']
            ],
            [
                'label' => '%ECPs who would recommend Abiliti to their patients/parents',
                'variables' => ['T5_4']
            ],
            [
                'label' => '% ECPs satisfied with SeeAbiliti for their patients',
                'variables' => ['T3_6']
            ]
        ];

        $tmp_results = [];
        $headers = ['KPIs', $chart->name];
        $scores = [];
        foreach ($questions as $key => $question) {
            $tmp_data = [$question['label']];
            foreach ($question['variables'] as $variable) {
                $series = collect($chart->series)->firstWhere('name', $variable);
                $record_ids = collect([]);
                foreach ($series['data'] as $data) { //for getting all completes
                    if (count($tmp_data) < count($data)) {
                        $tmp_data = $data; //find proper data
                    }
                    $record_ids = $record_ids->merge($data['record_ids']);
                }
                $ts = Str::of($variable)->explode('_');
                $t = Str::lower($ts[0]);//t3
                $prime = $ts[1] ?? null;
                $records = Record::whereIn('id', $record_ids->unique()->toArray())->get();
                $scores[$key] = $this->getKPIData($records, $t, $prime, $tmp_data, $key);
                $tmp_data[] = $scores[$key]['percent'];
            }
            $tmp_result[] = $tmp_data;
        }

        $results = collect($tmp_results)->prepend($headers)->toArray(); 
        return $results;
    }

    public function getData($records, $t, $prime, $data, $summary, $table = false) {
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
                    } else {
                        $tmp_data = null;
                    }                    
                    break;
            }

            if ($tmp_data != null) {
                $data_count = count($tmp_data['data']);
                foreach ($tmp_data['data'] as $t_key => $tmp) {
                    if (!isset($tmp_result[4+$t_key])) {
                        $tmp_result[3] = $tmp_data['equivalent'];//prime
                        $tmp_result[4+$t_key] = 0;//initialize
                    }
                    if ($tmp['selected']) {
                        if ($summary == 'summary') {
                            if ($t == 't4' || $t == 't9') {
                                $tmp_result[4+$t_key] += 1;//increment selected
                            }else {
                                $tmp_result[4+$t_key] += $tmp['value'];
                            }
                            $total += $tmp['value']; //total using value
                        }else {
                            if ($t == 't2') {
                                $tmp_result[4+$t_key] += $tmp['value'];
                                $total += $tmp['value']; //total using value
                            }else {
                                $tmp_result[4+$t_key] += 1;//increment selected
                            }
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
                if ($t == 't2') {
                    $segment1 = round($total / $max_point);
                    $max_point = 100 * $max_point;
                }else {
                    $segment1 = round(($total / $max_point) * 100); //segment 1
                }
                
            }
            if ($t == 't4' || $t == 't9') {
                for ($i=0; $i < $data_count; $i++) { //loop and change value to percentage
                    $tmp_result[4+$i] = round(($tmp_result[4+$i] / $max_point) * 100);
                }
            }
            
        }else {
            $total = count($records);
            if ($t == 't2') {
                $tmp_result[4] = round($tmp_result[4] / $total);
            }
        }
        if ($table == false) {
            $tmp_result[5+$data_count] = $total; //total
            $tmp_result[] = $max_point ?? '';
            $tmp_result[] = $segment1 ?? '';
        }
        
        /* $tmp_result[6+$data_count] = $max_point ?? '';
        $tmp_result[7+$data_count] = $segment1 ?? ''; */
        

        return $tmp_result->toArray();
    }

    public function getKPIData($records, $t, $prime, $data, $key) {
        $counts = 0;
        foreach ($records as $record) {
            switch ($key) {
                case 3: //T7
                    $tmp_data = $record->data[$t];
                    break;
                default:
                    if (isset($record->data[$t]['responses'])) {
                        $tmp_data = collect($record->data[$t]['responses'][0]['primes'])->firstWhere('index', $prime);
                    } else {
                        $tmp_data = null;
                    }  
                    break;
            }
            if ($tmp_data != null) {
                switch ($t) {
                    case 't3':
                        $evaluate = [5];
                        break;
                    case 't4':
                    case 't9':
                        $evaluate = [2];
                        break;
                    case 't5':
                        $evaluate = [4,5];
                        break;
                    default: //t7
                        $evaluate = [4,5];
                        break;
                }
                foreach ($evaluate as $value) {
                    if ($t == 't7') {
                        if ($tmp_data == $value) {
                            $counts++;
                            break;
                        }
                    }else {
                        $data = collect($tmp_data['data'])->firstWhere('value', $value);
                        if ($data == null) {
                            dd($tmp_data['data']);
                        }
                        
                        if ($data['selected']) {
                            $counts++;
                            break;
                        }
                    }
                    
                }
                
            }
        }
        $count_records = count($records);
        if ($count_records > 0) {
            $percent = round(($counts/ $count_records) * 100);
        }else {
            $percent = 0;
        }
        
        return ['records' => $records, 'percent' => $percent];
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
                    $headers[$legend] = 'b3_2';
                    break;
                case 'T2_3':
                    $headers[$legend] = 'b3_3';
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
                        if ($responses != null) {
                            foreach ($responses['data'] as $reskey => $resdata) {
                                if ($resdata['selected']) {
                                    $val = $reskey + 1;
                                    break 1;
                                }
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

    public function getScore($records, $legend, $prime)
    {
        $max_value = 0;
        $points = 0;
        $percentage = [
            'green' => [
                'colour' => 'green',
                'value' => 0,
                'count' => 0,
                'name' => 'Green Box %',
                'label' => 'T2B',//T2B
                'active' => true
            ],
            'orange' => [
                'colour' => 'orange',
                'value' => 0,
                'count' => 0,
                'name' => 'Amber Box %',
                'label' => 'MB',//MB
                'active' => false
            ],
            'red' => [
                'colour' => 'red',
                'value' => 0,
                'count' => 0,
                'name' => 'Red Box %',
                'label' => 'B2B',//B2B
                'active' => false
            ],
        ];
        if ($legend == 't6') {
            unset($percentage['red']);
            unset($percentage['orange']);
            $colour = 'green';
            /* if ($prime == 1) {
                unset($percentage['orange']);
                unset($percentage['green']);
                $colour = 'red';
            }else if ($prime == 2) {
                unset($percentage['red']);
                unset($percentage['green']);
                $colour = 'orange';
            }else {
                unset($percentage['red']);
                unset($percentage['orange']);
                $colour = 'green';
            } */
            
        } elseif ($legend == 't7') {
            unset($percentage['red']);
            unset($percentage['orange']);
            $colour = 'green';
            /* if ($prime <= 2) {
                unset($percentage['orange']);
                unset($percentage['green']);
                $colour = 'red';
            }else if ($prime == 3) {
                unset($percentage['red']);
                unset($percentage['green']);
                $colour = 'orange';
            }else {
                unset($percentage['red']);
                unset($percentage['orange']);
                $colour = 'green';
            } */
        } elseif ($legend == 't2'|| $legend == 't11' || $legend == 't12') {
            unset($percentage['red']);
            unset($percentage['orange']);
            $colour = 'green';
        } 
        $tcount = count($records);
        $tmp_data = [];
        foreach ($records as $record) {
            switch ($legend) {
                case 't2':
                case 't6':
                case 't7':
                case 't11':
                case 't12':
                    $tmp_data = Chart::getExpData($legend, $record, $prime);
                    break;
                default:
                    if (isset($record->data[$legend]['responses'])) {
                        $tmp_data = collect($record->data[$legend]['responses'][0]['primes'])->firstWhere('index', $prime);
                    }else {
                        $tmp_data = null;
                    }                    
                    break;
            }

            if ($tmp_data != null) {
                if ($max_value == 0) {
                    if (count($tmp_data['data']) > 2) {
                        $max_value = $tcount * count($tmp_data['data']);
                    }else {
                        $max_value = $tcount;
                    }
                }
                foreach ($tmp_data['data'] as $t_key => $tmp) {
                    /* if (!isset($points[$t_key])) {
                        $points[$t_key] = 0;
                    } */
                    if ($tmp['selected']) {
                        // $points+=($t_key + 1);
                        $points+=$tmp['value'];
                        switch ($legend) {
                            case 't2':
                                $percentage[$colour]['count'] += $tmp['value'];
                            break;
                            case 't3':
                            case 't5':
                            case 't8':
                                if ($t_key <= 1) {
                                    $percentage['red']['count'] += 1;
                                }elseif ($t_key == 2) {
                                    $percentage['orange']['count'] += 1;
                                }else  {
                                    $percentage['green']['count'] += 1;
                                }
                                break;
                            case 't4':
                            case 't9':
                                if ($prime == 19) {
                                    if ($t_key == 0) {
                                        $percentage['green']['count'] += 1; //change 1/14/2021 NO
                                    }else  {
                                        $percentage['red']['count'] += 1; //change 1/14/2021 YES
                                    }
                                }else {
                                    if ($t_key == 0) {
                                        $percentage['red']['count'] += 1; //NO
                                    }else  {
                                        $percentage['green']['count'] += 1;//YES
                                    }
                                }
                                
                                unset($percentage['orange']);
                                break;
                            case 't10':
                                if ($t_key == 0) {
                                    $percentage['red']['count'] += 1;
                                }elseif ($t_key == 3) {
                                    $percentage['green']['count'] += 1;
                                }else  {
                                    $percentage['orange']['count'] += 1;
                                }
                                break;
                            default:
                                # t2 t6 t7 t11 t12
                                $percentage[$colour]['count'] += 1;
                                break;
                        }
                    }
                }
            }
            
        }
        
        if ($tcount > 0) {
            foreach ($percentage as $key =>  $percent) {
                if ($legend == 't2') {
                    $percentage[$key]['value'] = round($percent['count'] / $tcount);
                }else {
                    $percentage[$key]['value'] = round(($percent['count'] / $tcount) * 100);
                }
                // $percent['value'] = ceil($percent['count'] / $tcount);
                /* if ($percentage[$key]['value'] > $this->tops['colours'][$key]) {
                    $this->tops['colours'][$key] = $percentage[$key]['value'];
                } */
            }
        }
        
        $score = $max_value > 0 ? (($points/$max_value) * 100) : null;
        $question = Chart::getQuestion($legend);
        $equivalent = Chart::items($legend, $prime); //$tmp_data['prime'] ?? null;
        if ($legend == 't2' || $legend == 't6' || $legend == 't7' || $legend == 't11' || $legend == 't12') {
            $targets = [''];
        }else {
            $targets = $question['choices'];
        }
        
        return [
            'gscore' => round($score),
            'prime' => $legend == 't5' ? ($tmp_data['equivalent'] ?? '').' '.$equivalent : $equivalent,
            'percentage' => $percentage,
            'question' => $question['question'],
            'dimension' => $question['dimension'],
            'targets' => $targets
        ];
    }    
}
