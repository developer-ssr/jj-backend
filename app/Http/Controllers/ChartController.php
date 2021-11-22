<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\Office;
use App\Models\Chart;
use App\Models\Record;
use App\Models\Link;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ChartController extends Controller
{
    private $tops;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $office = Office::find($request->office_id);
        if ($request->has('filter_ids')) {
            $filters = Filter::whereIn('id', json_decode($request->filter_ids, true))->get();
            Cache::put($office->id, $filters->pluck('id')->toArray());
        } else {
            if (Cache::has($office->id)) {
                $filters = Filter::where('office_id', $request->office_id)->whereIn('id', Cache::get($office->id))->get();
            } else {
                $filters = Filter::where('office_id', $request->office_id)->orderBy('id', 'desc')->limit(1)->get();
            }
            
        }
        if ($filters->isEmpty()) {
            return response('No data', 500);
        }
        
        $links = Link::where('office_id', $office->id)->get();
        // $filters = Filter::all();
        $data = [];
        
        foreach ($filters as $key => $filter) {
            $qry = Chart::where('filter_id',$filter->id);
            if(!$qry->exists()){
                $chart = Chart::create([
                    'title' => $office->name,
                    'filter_id' => $filter->id
                ]);
            }else {
                $chart = $qry->first();
            }

            // if ($request->update_series == true) {
                $series = [];
                // $records = [];//Record::all();
                $categories = [];
                foreach ($filter->data['legends'] as $legend) {
                    foreach ($legend['primes'] as $prime) {
                        $code = Str::of($legend['name'].'_'. $prime)->ucfirst();
                        $series_data = [];
                        $records = [];
                        $segment = 0;
                        $this->tops = [
                            "highest" => [
                                "value" => 0,
                                "colour" => 'green'
                            ]
                        ];
                        if ($office->type == 'country') {
                            foreach ($filter->data['segments'] as $s_key => $segments):
                                if (!isset($records[$s_key])) {
                                    //$records[$s_key] = Record::whereBetween('created_at', [date($segments['from']), date($segments['to'])])->get();
                                    $records[$s_key] = Record::whereDate('created_at', ">=", date($segments['from']))
                                                                ->whereDate('created_at', "<=", date($segments['to']))
                                                                ->get();
                                }
                                $tcount = count($records[$s_key]);
                                $date = Carbon::parse($segments['from'])->format('d M Y');
                                if (!isset($categories[$s_key])) {
                                    $categories[$s_key] = $date;//'Segment '.($segment + 1);
                                }
                                $score = $this->getScore($records[$s_key], $legend['name'], $prime);
                                
                                $series_data[] = [
                                    'question' => $score['question'],
                                    'code' => $code,
                                    'prime' => $score['prime'],
                                    'segment' => ($s_key + 1),
                                    'date' => $date,
                                    'tcount' => $tcount,
                                    'gscore' => $score['gscore'],
                                    'percentage' => $score['percentage'],
                                    'record_ids' => $records[$s_key]->pluck('id'),
                                    'dimension' => $score['dimension'],
                                    'targets' => $score['targets']
                                ];
                                $segment++;
                            endforeach;
                        }else {
                            foreach ($links as $s_key => $link) {
                                $records[$s_key] = Record::where('participant_id', $link->link_id)->get();  
                                $tcount = count($records[$s_key]);
                                if ($tcount > 0) {
                                    if (!isset($categories[$segment])) {
                                        $categories[$segment] = $records[$s_key][0]->created_at->format("d M Y");//'Segment '.($segment + 1);
                                    }
                                    $score = $this->getScore($records[$s_key], $legend['name'], $prime);
                                    $date = $link->created_at->format("d M Y");
                                    $series_data[] = [
                                        'question' => $score['question'],
                                        'code' => $code,
                                        'prime' => $score['prime'],
                                        'segment' => ($segment + 1),
                                        'date' => $date,
                                        'tcount' => $tcount,
                                        'gscore' => $score['gscore'],
                                        'percentage' => $score['percentage'],
                                        'record_ids' => $records[$s_key]->pluck('id'),
                                        'dimension' => $score['dimension'],
                                        'targets' => $score['targets']
                                    ];
                                    $segment++;
                                }
                            }
                        }
                        
                        foreach ($score['percentage'] as $colour =>  $percent) {
                            if ($percent['value'] >= $this->tops['highest']['value']) {
                                $this->tops['highest']['value'] = $percent['value'];
                                $this->tops['highest']['colour'] = $colour;
                            }
                        }
                        /* foreach ($this->tops['colours'] as $colour => $top) {
                            if ($top > $this->tops['highest']['value']) {
                                $this->tops['highest']['value'] = $top;
                                $this->tops['highest']['colour'] = $colour;
                            }
                        } */
                        $this->tops['segment'] = last($series_data);
                        $series[] = [
                            'name' => $code,
                            'question' => 'How likely would you be to recommend the following to your patients and their parents?',
                            'data' => $series_data,
                            'tops' => $this->tops
                        ];
                    }
                }
                $chart->update([
                    'series' => $series,
                    'categories' => $categories,
                    'title' => $office->name,
                    'office_type' => $office->type
                ]);
            // }

            $data[] = $chart->only(['id', 'title', 'series', 'categories', 'office_type']);
        }
        return response()->json($data[0]);
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

    public function checkChartData($chart)
    {
        if ($chart->series == null) {
            $this->processkChartData($chart);
        }
    }

    public function processkChartData($chart)
    {
        $records = Record::all();
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
                'name' => 'Green Box %'
            ],
            'orange' => [
                'colour' => 'orange',
                'value' => 0,
                'count' => 0,
                'name' => 'Amber Box %'
            ],
            'red' => [
                'colour' => 'red',
                'value' => 0,
                'count' => 0,
                'name' => 'Red Box %'
            ],
        ];
        $tcount = count($records);
        $tmp_data = [];
        foreach ($records as $record) {
            if (isset($record->data[$legend]['responses'])) {
                $tmp_data = collect($record->data[$legend]['responses'][0]['primes'])->firstWhere('index', $prime);
            }else {
                $tmp_data = null;
            }
            
            if ($tmp_data != null) {
                if ($max_value == 0) {
                    $max_value = $tcount * count($tmp_data['data']);
                }
                foreach ($tmp_data['data'] as $t_key => $tmp) {
                    /* if (!isset($points[$t_key])) {
                        $points[$t_key] = 0;
                    } */
                    if ($tmp['selected']) {
                        // $points+=($t_key + 1);
                        $points+=$tmp['index'];
                        switch ($legend) {
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
                                if ($t_key == 0) {
                                    $percentage['red']['count'] += 1;
                                }else  {
                                    $percentage['green']['count'] += 1;
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
                                # code...
                                break;
                        }
                    }
                }
            }
        }
        
        if ($tcount > 0) {
            foreach ($percentage as $key =>  $percent) {
                $percentage[$key]['value'] = ceil(($percent['count'] / $tcount) * 100);
                // $percent['value'] = ceil($percent['count'] / $tcount);
                /* if ($percentage[$key]['value'] > $this->tops['colours'][$key]) {
                    $this->tops['colours'][$key] = $percentage[$key]['value'];
                } */
            }
        }
        
        $score = $max_value > 0 ? (($points/$max_value) * 100) : null;
        $question = $this->getQuestion($legend);
        return [
            'gscore' => ceil($score),
            'prime' => $tmp_data['prime'] ?? null,
            'percentage' => $percentage,
            'question' => $question['question'],
            'dimension' => $question['dimension'],
            'targets' => $tmp_data != null ? collect($tmp_data['data'])->pluck('equivalent'): []
        ];
    }    

    public function getQuestion($legend) 
    {
        switch ($legend) {
            case 't3':
                $dimension = 'Satisfaction';
                $question = 'Please indicate your satisfaction level with Abilit in the following areas:';
                break;
            case 't4':
                $dimension = 'Value';
                $question = 'Please indicate which of the following areas from Abiliti are effective and providing value to your myopia management practice.';
                break;
            case 't5':
                $dimension = 'Brand';
                $question = 'How likely would you be to recommend the following to your patients and their parents?';
                break;
            case 't8':
                $dimension = 'HC2 Behavior freq.';
                $question = 'In general, how frequently are you able to do the following with your myopia patients and/or their parents? ';
                break;
            case 't9':
                $dimension = 'HC2 Content';
                $question = 'As part of the parent conversation, please indicate if you include the following information or language in your discussion:';
                break;
            case 't10':
                $dimension = 'Tool use/reco';
                $question = 'Please indicate how often you use/recommend the following tools from Abiliti™:';
                break;
            default:
                $dimension = 'Dimension';
                $question = 'How likely would you be to recommend the following to your patients and their parents?';
                break;
        }
        return ['question' => $question, 'dimension' => $dimension];
    }
}
