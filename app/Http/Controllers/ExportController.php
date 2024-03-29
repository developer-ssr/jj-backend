<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\Chart;
use App\Models\Office;
use App\Models\Email;
use App\Exports\CsvExport;
use App\Models\Filter;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Http;
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

    public function downloadBaseline(Request $request, $summary)
    {
        /* if ($request->all) {
            $country = $request->country ?? 'all';
        }else {
            $country = $request->country ?? 'all';
        } */

        $country = $request->country ?? "all";

        switch ($country) {
            case "us":
                $codes = collect([
                    840 => ["country" => "USA", "survey_code" => "4fa"],
                ]);
                $survey_codes = ["4fa"];
                break;
            case "sg":
                $codes = collect([
                    702 => ["country" => "Singapore", "survey_code" => "XWp"],
                ]);
                $survey_codes = ["XWp"];
                break;
            case "hk":
                $codes = collect([
                    344 => ["country" => "Hongkong", "survey_code" => "4xS"],
                ]);
                $survey_codes = ["4xS"];
                break;
            case "ca":
                $codes = collect([
                    124 => ["country" => "Canada", "survey_code" => "5Ph"],
                ]);
                $survey_codes = ["5Ph"];
                break;
            default:
                $codes = collect([
                    840 => ["country" => "USA", "survey_code" => "4fa"],
                    702 => ["country" => "Singapore", "survey_code" => "XWp"],
                    344 => ["country" => "Hongkong", "survey_code" => "4xS"],
                    124 => ["country" => "Canada", "survey_code" => "5Ph"],
                ]);
                $survey_codes = ["4fa", "XWp", "5Ph", "4xS"];
                break;
        }

        if (isset($request->classifications)) {
            $classifications = json_decode($request->classifications);
            $all_offices = Office::whereIn("classification", $classifications)
                ->whereIn("code", $codes->keys())
                ->get();
        } else {
            $all_offices = Office::whereIn("code", $codes->keys())->get();
        }

        $filter_emails = $all_offices
            ->pluck("email")
            ->map(function ($item, $key) {
                return Str::lower($item);
            })
            ->toArray();

        $survey_codes = ["4fa", "XWp", "5Ph", "4xS"];
        $country_records = [];
        foreach ($codes as $code) {
            $response = Http::get(
                "https://fluent2.splitsecondsurveys.co.uk/api/records",
                [
                    "survey_codes" => [$code["survey_code"]],
                ]
            );
            //get records per country
            $country_records[$code["country"]] = collect(
                json_decode($response->body(), true)
            )->filter(function ($record) use ($filter_emails) {
                return in_array(
                    Str::lower(
                        $record["url_data"]["a2_2"] ??
                            ($record["url_data"]["c2_2"] ??
                                $record["url_data"]["h2_2"])
                    ),
                    $filter_emails
                );
            });
        }

        $records_data = [];
        $q_keys = baselineQuestions();
        foreach ($country_records as $country => $records) {
            foreach ($records as $record) {
                //data per respondent
                $results = $this->getBaselinedata($country, $record);
                $records_data[] = $results["data"];
                $records_url_data[] = $results["url_data"];
            }
        }
        if ($summary == "respondent") {
            $headers = collect([
                "Respondent ID",
                "Country",
                "Name",
                "Email Address",
                "Date Finished",
            ])->merge($results["headers"]);
            $data = collect($records_data)
                ->prepend($headers)
                ->toArray();
            $data[] = [" "];
            $data[] = ["Answer Keys", "Questions", "", "Value", "Description"];
            //Add question keys
            foreach (generator($q_keys) as $key => $question) {
                $data[] = [$key, $question["Question"]];
                foreach ($question["choices"]["rows"] as $row_key => $row) {
                    $rval = $row_key + 1;
                    if ($row_key == 0) {
                        //add column keys
                        $tmp_col = [$key . "." . $rval, "", "", $rval, $row];
                        foreach (
                            $question["choices"]["columns"]
                            as $col_key => $col
                        ) {
                            $tmp_col[] =
                                $key .
                                "." .
                                $rval .
                                "." .
                                ($col_key + 1) .
                                " " .
                                $col;
                        }
                        $data[] = $tmp_col;
                    } else {
                        $data[] = [$key . "." . $rval, "", "", $rval, $row];
                    }
                }
            }
        } else {
            $data = [];
            $records = collect($records_url_data);
            $summary_keys = summaryKeys();
            foreach (generator($summary_keys) as $key => $q_summary) {
                $question = $q_keys[$key];
                $tmp_data = [$key, $key, $question["Question"]];
                foreach ($question["choices"]["columns"] as $col_key => $col) {
                    $tmp_data[] = $col;
                }
                $data[] = $tmp_data;
                $col_count = count($question["choices"]["columns"]);
                $row_count = count($question["choices"]["rows"]);
                $r = 0;
                do {
                    $r++;
                    $col_val = [
                        $key . "." . $r,
                        $r,
                        $question["choices"]["rows"][$r - 1] ?? "Answer",
                    ];
                    $c = 0;
                    do {
                        $c++;
                        $col_val[] = baselineSummary(
                            $records,
                            $key,
                            $r,
                            $c,
                            $q_summary
                        );
                    } while ($c < $col_count);
                    $data[] = $col_val;
                } while ($r < $row_count);
            }
            $data[] = ["Sample Size", $records->count()];
        }

        $filename = $request->title;
        return Excel::download(CsvExport::new($data), $filename . ".xlsx");
    }

    public function getBaselinedata($country, $record)
    {
        $ts = baselineVariables();
        $headers = [];
        $url_data = [];
        $name =
            $record["url_data"]["a2_1"] ??
            ($record["url_data"]["c2_1"] ?? $record["url_data"]["h2_1"]);
        $email =
            $record["url_data"]["a2_2"] ??
            ($record["url_data"]["c2_2"] ?? $record["url_data"]["h2_2"]);
        $finished = $record["updated_at"];
        $data = [$record["url_data"]["id"], $country, $name, $email, $finished]; //initialize data with id, name and email

        //'convert data to Tracker format here
        foreach ($ts as $t => $variables) {
            switch ($t) {
                case "T2":
                    for ($i = 1; $i <= 3; $i++) {
                        $header = "{$t}_{$i}";
                        $headers[] = $header;
                        $val = baselineVal(
                            $record["url_data"],
                            $variables[$country],
                            $variables["Q_num"] . "_" . $i
                        );
                        $url_data[$header] = $val;
                        $data[] = $val;
                    }
                    break;
                case "T12":
                    for ($i = 1; $i <= 7; $i++) {
                        $header = "{$t}_{$i}";
                        $headers[] = $header;
                        $val = baselineVal(
                            $record["url_data"],
                            $variables[$country],
                            $variables["Q_num"] . "_" . $i
                        );
                        $url_data[$header] = $val;
                        $data[] = $val;
                    }
                    break;
                case "T16":
                    for ($i = 1; $i <= 4; $i++) {
                        $header = "{$t}_{$i}";
                        $headers[] = $header;
                        $val = baselineVal(
                            $record["url_data"],
                            $variables[$country],
                            $variables["Q_num"] . "_" . $i
                        );
                        $url_data[$header] = $val;
                        $data[] = $val;
                    }
                    break;
                case "T18":
                case "T26":
                    for ($i = 1; $i <= 8; $i++) {
                        $header = "{$t}_{$i}";
                        $headers[] = $header;
                        $val = baselineVal(
                            $record["url_data"],
                            $variables[$country],
                            $variables["Q_num"] . "_" . $i
                        );
                        $url_data[$header] = $val;
                        $data[] = $val;
                    }
                    break;
                case "T27":
                    for ($i = 1; $i <= 6; $i++) {
                        $header = "{$t}_{$i}";
                        $headers[] = $header;
                        $val = baselineVal(
                            $record["url_data"],
                            $variables[$country],
                            $variables["Q_num"] . "_" . $i
                        );
                        $url_data[$header] = $val;
                        $data[] = $val;
                    }
                    break;
                case "T34":
                    for ($i = 1; $i <= 7; $i++) {
                        $header = "{$t}_{$i}";
                        $headers[] = $header;
                        $val = baselineVal(
                            $record["url_data"],
                            $variables[$country],
                            $variables["Q_num"] . "_" . $i
                        );
                        $url_data[$header] = $val;
                        $data[] = $val;
                    }
                    break;
                case "T35":
                    for ($i = 1; $i <= 13; $i++) {
                        $header = "{$t}_{$i}";
                        $headers[] = $header;
                        $val = baselineVal(
                            $record["url_data"],
                            $variables[$country],
                            $variables["Q_num"] . "_" . $i
                        );
                        $url_data[$header] = $val;
                        $data[] = $val;
                    }
                    break;
                case "T36": //for matrix multiple
                case "T38":
                    for ($a = 1; $a <= $variables["Q_num"]; $a++) {
                        for ($i = 1; $i <= $variables["Q_limit"]; $i++) {
                            $header = "{$t}_{$a}_{$i}";
                            $headers[] = $header;
                            $val = baselineVal(
                                $record["url_data"],
                                $variables[$country],
                                "_" . $a . "_" . $i
                            );
                            $url_data[$header] = $val;
                            $data[] = $val;
                        }
                    }
                    break;
                case "T37": //single select
                case "T40":
                case "T42":
                case "T43":
                case "T44":
                    for ($i = 1; $i <= $variables["Q_limit"]; $i++) {
                        $header = "{$t}_{$i}";
                        $headers[] = $header;
                        $val = baselineVal(
                            $record["url_data"],
                            $variables[$country],
                            $variables["Q_num"] . "_" . $i
                        );
                        $url_data[$header] = $val;
                        $data[] = $val;
                    }
                    break;
                case "T39": //single select inverse
                    if ($country == "Hongkong") {
                        //hk not inverted
                        for ($i = 1; $i <= $variables["Q_limit"]; $i++) {
                            $header = "{$t}_{$i}";
                            $headers[] = $header;
                            $val = baselineVal(
                                $record["url_data"],
                                $variables[$country],
                                $variables["Q_num"] . "_" . $i
                            );
                            $url_data[$header] = $val;
                            $data[] = $val;
                        }
                    } else {
                        for ($i = 1; $i <= $variables["Q_limit"]; $i++) {
                            $header = "{$t}_{$i}";
                            $val = baselineValInvert(
                                $record["url_data"],
                                $variables[$country],
                                $variables["Q_num"] . "_" . $i,
                                $variables["Q_not"],
                                5
                            );
                            $url_data[$header] = $val;
                            $headers[] = $header;
                            $data[] = $val;
                        }
                    }

                    break;
                case "T41":
                    if ($country == "Hongkong" || $country == "Canada") {
                        //hk and canada not inverted
                        for ($i = 1; $i <= $variables["Q_limit"]; $i++) {
                            $header = "{$t}_{$i}";
                            $val = baselineVal(
                                $record["url_data"],
                                $variables[$country],
                                $variables["Q_num"] . "_" . $i
                            );
                            $url_data[$header] = $val;
                            $headers[] = $header;
                            $data[] = $val;
                        }
                    } else {
                        for ($i = 1; $i <= $variables["Q_limit"]; $i++) {
                            $header = "{$t}_{$i}";
                            $val = baselineValInvert(
                                $record["url_data"],
                                $variables[$country],
                                $variables["Q_num"] . "_" . $i,
                                $variables["Q_not"],
                                5
                            );
                            $url_data[$header] = $val;
                            $headers[] = $header;
                            $data[] = $val;
                        }
                    }

                    break;
                default:
                    $header = $t;
                    $val = baselineVal(
                        $record["url_data"],
                        $variables[$country],
                        $variables["Q_num"]
                    );
                    $url_data[$header] = $val;
                    $headers[] = $header;
                    $data[] = $val;
                    break;
            }
        }
        return [
            "data" => $data,
            "headers" => $headers,
            "url_data" => $url_data,
        ];
    }

    public function downloadOffice(Request $request, $ecp)
    {
        $all = json_decode($request->all) ?? false;

        if ($all) {
            $classifications = ["Leader", "Believer", "Emerger"];
        } else {
            $classifications = json_decode($request->classifications);
        }
        $country = $request->country ?? "all";
        switch ($country) {
            case "us":
                $codes = collect([
                    840 => "USA",
                ]);
                break;
            case "sg":
                $codes = collect([
                    702 => "Singapore",
                ]);
                break;
            case "hk":
                $codes = collect([
                    344 => "Hongkong",
                ]);
                break;
            case "ca":
                $codes = collect([
                    124 => "Canada",
                ]);
                break;
            default:
                $codes = collect([
                    840 => "USA",
                    702 => "Singapore",
                    344 => "Hongkong",
                    124 => "Canada",
                ]);
                break;
        }
        $all_offices = Office::with("links")
            ->whereIn("classification", $classifications)
            ->whereIn("code", $codes->keys())
            ->get();
        $offices = $all_offices->filter(function ($values) {
            $taken = collect($values->links)
                ->filter(fn($v) => $v["taken"] === "YES")
                ->count();
            return $taken > 0;
        });
        if ($ecp == "tracker") {
            $office_ids = $offices->pluck("id")->toArray();
            $all_filters = Filter::whereIn("office_id", $office_ids)
                ->orderBy("id", "asc")
                ->get();
            $filter_ids = $all_filters
                ->groupBy("office_id")
                ->map(function ($item, $key) {
                    return collect($item)->last()->id;
                });
            $charts = Chart::whereIn("filter_id", $filter_ids->toArray())
                ->whereNotNull("country")
                ->get();
            $kpi_data = $this->exportKPI($charts, $request->title);
            $data = $kpi_data["results"];
            $data[] = ["Sample Size", $kpi_data["sample_size"]];
        } else {
            $filter_emails = $all_offices
                ->pluck("email")
                ->map(function ($item, $key) {
                    return Str::lower($item);
                })
                ->toArray();
            //EcpBaselineController->classifications()
            $response = Http::get(
                "https://fluent2.splitsecondsurveys.co.uk/custom/jnj/baseline/classifications",
                [
                    "filter_emails" => $filter_emails,
                    "title" => $request->title,
                ]
            );
            $data = json_decode($response->body(), true);
        }

        $filename = $request->title;
        return Excel::download(CsvExport::new($data), $filename . ".xlsx");
    }

    public function download(Request $request, $id, $summary)
    {
        $chart = Chart::find($id);
        $all = json_decode($request->all) ?? false;
        $file_name = $request->file_name ?? $chart->title . "_" . $summary;
        if ($all) {
            if ($summary == "table_summary" || $summary == "table_respondent") {
                $legends = [
                    "T2",
                    "T3",
                    "T4",
                    "T5",
                    "T6",
                    "T7",
                    "T8",
                    "T9",
                    "T10",
                    "T11",
                    "T12",
                ];
            } else {
                $legends = collect($chart->series)
                    ->pluck("name")
                    ->toArray();
            }
        } else {
            if ($summary == "table_summary" || $summary == "table_respondent") {
                $legends = [];
                $_lg = json_decode($request->legends)[0];
                if (count(explode("_", $_lg)) > 1) {
                    foreach (json_decode($request->legends) as $legend) {
                        $tmp = Str::of($legend)->explode("_");
                        if ($tmp[0] == "T4" && $tmp[1] == 21) {
                            $prime = 19;
                        } else {
                            $prime = $tmp[1];
                        }

                        if (!isset($legends[$tmp[0]])) {
                            $legends[$tmp[0]] = [];
                            $legends[$tmp[0]][] = $prime;
                        } else {
                            $legends[$tmp[0]][] = $prime;
                        }
                    }
                } else {
                    $legends = json_decode($request->legends);
                    $all = true;
                }
            } else {
                $legends = json_decode($request->legends);
            }
        }

        if ($summary == "summary") {
            $data = $this->exportSummary($chart, $legends);
            $headers = [
                "Dimension",
                "",
                "",
                "Question Text",
                "",
                "",
                "Answer Value",
            ];
            $data = collect($data)
                ->prepend($headers)
                ->toArray();
        } elseif ($summary == "respondent") {
            $data = $this->exportRespondent($chart, $legends);
            $headers = [
                "Dimension",
                "",
                "",
                "Question Text",
                "",
                "",
                "Answer Value",
            ];
            $data = collect($data)
                ->prepend($headers)
                ->toArray();
        } elseif ($summary == "table_summary") {
            $data = $this->exportTable($chart, $legends, $summary, $all);
        } elseif ($summary == "table_respondent") {
            $data = $this->exportTable($chart, $legends, $summary, $all);
        } elseif ($summary == "tracker_kpi") {
            $kpi_data = $this->exportKPI([$chart], $chart->title);
            $data = $kpi_data["results"];
            $data[] = ["Sample Size", $kpi_data["sample_size"]];
        } /* elseif($summary == 'classification_kpi') {
            $data = $this->exportKPI($chart, $chart->title);
        } */ else {
            $tmp_data = $this->exportTracker($chart, $legends);
            $headers = collect([
                "Respondent ID",
                "Date Completed",
                "Country",
                "Name",
                "Email Address",
            ])
                ->merge($tmp_data["headers"])
                ->toArray();
            $data = collect($tmp_data["results"])
                ->prepend($headers)
                ->toArray();
        }

        return Excel::download(CsvExport::new($data), $file_name . ".xlsx");
    }

    public function exportSummary($chart, $legends)
    {
        $tmp_results = [];
        $headers = [];
        foreach ($legends as $legend) {
            $tmp = Str::of($legend)->explode("_");
            $t = Str::lower($tmp[0]); //t3
            if ($tmp[0] == "T4" && $tmp[1] == 21) {
                $prime = 19;
            } else {
                $prime = $tmp[1];
            }
            $record_ids = collect([]);
            $series = collect($chart->series)->firstWhere("name", $legend);
            $tmp_data = [];
            foreach ($series["data"] as $data) {
                $headers[$t] = collect([
                    $data["dimension"],
                    $tmp[0],
                    $tmp[0],
                    $data["question"],
                ]);
                if (count($tmp_data) < count($data)) {
                    $tmp_data = $data; //find proper data
                }
                $record_ids = $record_ids->merge($data["record_ids"]);
            }
            $records = Record::whereIn(
                "id",
                $record_ids->unique()->toArray()
            )->get();
            $tmp_results[$t][] = $this->getData(
                $records,
                $t,
                $prime,
                $tmp_data,
                "summary"
            );
            while (count($tmp_data["targets"]) < 5) {
                //assign spacing
                $tmp_data["targets"][] = "";
            }
            $tmp_data["targets"][] = "TOTAL Points";
            $tmp_data["targets"][] = "Max Points (max value * total completes)";
            $tmp_data["targets"][] = "Invite 1";
            $headers[$t] = $headers[$t]
                ->merge($tmp_data["targets"] ?? [])
                ->toArray();
        }
        $results = [];
        $header_keys = collect($headers)
            ->keys()
            ->toArray();
        natsort($header_keys); //sort list
        foreach ($header_keys as $ts) {
            $results[] = $headers[$ts]; //assign header
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
            $tmp = Str::of($legend)->explode("_");
            $t = Str::lower($tmp[0]); //t3
            if ($tmp[0] == "T4" && $tmp[1] == 21) {
                $prime = 19;
            } else {
                $prime = $tmp[1];
            }
            $record_ids = collect([]);
            $series = collect($chart->series)->firstWhere("name", $legend);
            $tmp_data = [];
            foreach ($series["data"] as $data) {
                $headers[$t] = collect([
                    $data["dimension"],
                    $tmp[0],
                    $tmp[0],
                    $data["question"],
                ]);
                if (count($tmp_data) < count($data)) {
                    $tmp_data = $data; //find proper data
                }
                $record_ids = $record_ids->merge($data["record_ids"]);
            }
            $records = Record::whereIn(
                "id",
                $record_ids->unique()->toArray()
            )->get();
            $tmp_results[$t][] = $this->getData(
                $records,
                $t,
                $prime,
                $tmp_data,
                "respondent"
            );
            while (count($tmp_data["targets"]) < 5) {
                //assign spacing
                $tmp_data["targets"][] = "";
            }
            $tmp_data["targets"][] = "TOTAL Completes";
            $headers[$t] = $headers[$t]
                ->merge($tmp_data["targets"] ?? [])
                ->toArray();
        }
        $results = [];
        $header_keys = collect($headers)
            ->keys()
            ->toArray();
        natsort($header_keys); //sort list
        foreach ($header_keys as $ts) {
            $results[] = $headers[$ts]; //assign header
            foreach ($tmp_results[$ts] as $value) {
                $results[] = $value;
            }
        }
        return $results;
    }

    public function exportTable($chart, $legends, $summary, $all)
    {
        $tmp_results = [];
        $headers = [];
        if ($summary == "table_summary") {
            foreach ($legends as $t_key => $legend) {
                $record_ids = collect([]);
                if ($all == false) {
                    $items = [];
                    $Tn_point = $t_key;
                    $t = Str::lower($t_key); //t3
                    foreach ($legend as $tmp_prime) {
                        $items[] = $tmp_prime;
                    }
                } else {
                    $Tn_point = $legend;
                    $t = Str::lower($legend); //t3
                    $items = Chart::items($t);
                }
                $recorded = false;
                foreach ($items as $i_key => $prime_or_description) {
                    if ($all == false) {
                        $prime = $prime_or_description;
                        $item = $t_key . "_" . $prime; //T3_1
                    } else {
                        $prime = $i_key + 1;
                        $item = $legend . "_" . $prime; //T3_1
                    }
                    if ($item == "T4_19") {
                        $item = "T4_21";
                    }
                    $series = collect($chart->series)->firstWhere(
                        "name",
                        $item
                    );
                    if ($series == null) {
                        continue;
                    }
                    $tmp_data = [];
                    while ($recorded == false) {
                        foreach ($series["data"] as $data) {
                            //for getting all completes
                            if (count($tmp_data) < count($data)) {
                                $tmp_data = $data; //find proper data
                            }
                            $record_ids = $record_ids->merge(
                                $data["record_ids"]
                            );
                        }
                        while (count($tmp_data["targets"]) < 5) {
                            //assign spacing
                            $tmp_data["targets"][] = "";
                        }
                        //for headers
                        $tmp_data["targets"][] =
                            $tmp_data["percentage"]["green"]["label"];
                        if (isset($tmp_data["percentage"]["orange"])) {
                            $tmp_data["targets"][] =
                                $tmp_data["percentage"]["orange"]["label"];
                        }
                        if (isset($tmp_data["percentage"]["red"])) {
                            $tmp_data["targets"][] =
                                $tmp_data["percentage"]["red"]["label"];
                        }
                        $headers[$t] = collect([
                            $tmp_data["dimension"],
                            $Tn_point,
                            $Tn_point,
                            $tmp_data["question"],
                        ]);
                        $headers[$t] = $headers[$t]
                            ->merge($tmp_data["targets"])
                            ->toArray(); //merge T2B headers
                        $records = Record::whereIn(
                            "id",
                            $record_ids->unique()->toArray()
                        )->get();
                        $recorded = true;
                    }

                    $data = $this->getData(
                        $records,
                        $t,
                        $prime,
                        $tmp_data,
                        "respondent",
                        true
                    ); //summary in table
                    $score = $this->getScore($records, $t, $prime);
                    $data[] = $score["percentage"]["green"]["value"];
                    if (isset($score["percentage"]["orange"])) {
                        $data[] = $score["percentage"]["orange"]["value"];
                    }
                    if (isset($score["percentage"]["red"])) {
                        $data[] = $score["percentage"]["red"]["value"];
                    }
                    $tmp_results[$t][] = $data;
                }
            }

            $results = [];
            $header_keys = collect($headers)
                ->keys()
                ->toArray();
            natsort($header_keys); //sort list
            foreach ($header_keys as $ts) {
                $results[] = $headers[$ts]; //assign header
                foreach ($tmp_results[$ts] as $value) {
                    $results[] = $value;
                }
                $results[] = ["", ""]; //apply spacing below
            }
        } else {
            //table_respondent
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
                    $t = Str::lower($t_key); //t3
                } else {
                    $Tn_point = $t_key;
                    $t = Str::lower($legend); //t3
                    $items = Chart::items($t);
                }
                foreach ($items as $i_key => $prime_or_description) {
                    if ($all == false) {
                        $prime = $prime_or_description;
                        $item = $t_key . "_" . $prime; //T3_1
                    } else {
                        $prime = $i_key + 1;
                        $item = $legend . "_" . $prime; //T3_1
                    }
                    if ($item == "T4_19") {
                        $item = "T4_21";
                    }
                    $series = collect($chart->series)->firstWhere(
                        "name",
                        $item
                    );
                    if ($series == null) {
                        continue;
                    }
                    $ts[] = $item;
                    if ($i_key == 0) {
                        foreach ($series["data"] as $data) {
                            //for getting all completes
                            $record_ids = $record_ids->merge(
                                $data["record_ids"]
                            );
                        }
                        $records = Record::whereIn(
                            "id",
                            $record_ids->unique()->toArray()
                        )->get();
                    }
                    $sample_size[$item] = count($records);
                    $scores[$item] = $this->getScore($records, $t, $prime);
                }
            }
            $tmp_data = $this->exportTracker($chart, $ts);
            $headers = collect([
                "Respondent ID",
                "Date Completed",
                "Country",
                "Name",
                "Email Address",
            ])
                ->merge($tmp_data["headers"])
                ->toArray();
            $tmp_results = $tmp_data["results"];
            $tmp_results[] = ["", ""]; // add space
            ksort($ts, 4);
            $tmps = [
                "Sample size" => [],
                "Items" => [],
                "T2B" => [],
                "MB" => [],
                "B2B" => [],
            ];

            $colours = [
                "sample" => "Sample size",
                "items" => "Items",
                "green" => "T2B",
                "orange" => "MB",
                "red" => "B2B",
            ];
            foreach (generator($colours) as $color => $tmp) {
                $tmps[$tmp] = ["", "", "", $tmp];
                foreach (generator($ts) as $key => $t_item) {
                    if ($tmp == "Items") {
                        $tmps[$tmp][] = $scores[$t_item]["prime"];
                    } elseif ($tmp == "Sample size") {
                        $tmps["Sample size"][] = $sample_size[$t_item];
                    } else {
                        if (
                            isset(
                                $scores[$t_item]["percentage"][$color]["value"]
                            )
                        ) {
                            $tmps[$tmp][] =
                                $scores[$t_item]["percentage"][$color]["value"];
                        } else {
                            $tmps[$tmp][] = "-";
                        }
                    }
                }
            }

            $tmp_results[] = $tmps["Sample size"];
            $tmp_results[] = $tmps["Items"];
            $tmp_results[] = $tmps["T2B"];
            $tmp_results[] = $tmps["MB"];
            $tmp_results[] = $tmps["B2B"];

            $results = collect($tmp_results)
                ->prepend($headers)
                ->toArray();
        }

        return $results;
    }

    public function exportKPI($charts, $title)
    {
        // dd($charts);
        $questions = [
            [
                "label" => "Satisfaction w/ ordering",
                "variables" => ["T3_19"],
            ],
            [
                "label" => "Satisfaction w/ fitting software",
                "variables" => ["T3_4"],
            ],
            [
                "label" => "Satisfaction w/ cust. service",
                "variables" => ["T3_1"],
            ],
            [
                "label" =>
                    "%ECPs discussing MM as a treatment option with all patients",
                "variables" => ["T7_4"],
            ],
            [
                "label" => "%ECPs discuss LT health risks w/ parents",
                "variables" => ["T9_1"],
            ],
            [
                "label" => "%ECPs who value CSC support",
                "variables" => ["T4_1"],
            ],
            [
                "label" =>
                    "%ECPs who would recommend Abiliti to their patients/parents",
                "variables" => ["T5_4"],
            ],
            [
                "label" =>
                    "% ECPs satisfied with SeeAbiliti for their patients",
                "variables" => ["T3_6"],
            ],
        ];

        $tmp_results = [];
        $headers = ["KPIs", $title];
        $scores = [];
        $total_records = collect([]);
        foreach ($questions as $key => $question) {
            $tmp_result = [$question["label"]];
            foreach ($question["variables"] as $variable) {
                $record_ids = collect([]);
                foreach ($charts as $chart) {
                    $series = collect($chart->series)->firstWhere(
                        "name",
                        $variable
                    );
                    if ($series != null) {
                        foreach ($series["data"] as $data) {
                            //for getting all completes
                            if (isset($data["record_ids"])) {
                                $record_ids = $record_ids->merge(
                                    $data["record_ids"]
                                );
                                $total_records = $total_records->merge(
                                    $data["record_ids"]
                                );
                            }
                        }
                    }
                }
                $ts = Str::of($variable)->explode("_");
                $t = Str::lower($ts[0]); //t3
                $prime = $ts[1] ?? null;
                $records = Record::whereIn(
                    "id",
                    $record_ids->unique()->toArray()
                )->get();
                $scores[$key] = $this->getKPIData($records, $t, $prime, $key);
                $tmp_result[] = $scores[$key]["percent"];
            }
            $tmp_results[] = $tmp_result;
        }
        // $tmp_results[] = ['Sample Size', count($records)];
        $results = collect($tmp_results)
            ->prepend($headers)
            ->toArray();
        $sample_size = $total_records->unique()->count();
        return ["results" => $results, "sample_size" => $sample_size];
    }

    public function getData(
        $records,
        $t,
        $prime,
        $data,
        $summary,
        $table = false
    ) {
        $tmp_data = [];
        if ($t == "t4" && $prime == 19) {
            $prime_num = 21;
        } else {
            $prime_num = $prime;
        }
        $tmp_result = collect([
            $data["dimension"] ?? "",
            Str::upper($t),
            $prime_num,
            $data["question"] ?? "",
        ]);
        $data_count = 0;
        $total = 0;
        $count = count($records);

        $a = 0;
        do {
            if (isset($records[$a])) {
                $record = $records[$a];
                switch ($t) {
                    case "t2":
                    case "t6":
                    case "t7":
                    case "t11":
                    case "t12":
                        $tmp_data = Chart::getExpData($t, $record, $prime);
                        break;
                    default:
                        if (isset($record->data[$t]["responses"])) {
                            $tmp_data = collect(
                                $record->data[$t]["responses"][0]["primes"]
                            )->firstWhere("index", $prime);
                        } else {
                            $tmp_data = null;
                        }
                        break;
                }
            } else {
                $tmp_data = null;
            }

            if ($tmp_data != null) {
                $data_count = count($tmp_data["data"]);
                foreach ($tmp_data["data"] as $t_key => $tmp) {
                    if (!isset($tmp_result[4 + $t_key])) {
                        $tmp_result[3] = $tmp_data["equivalent"]; //prime
                        $tmp_result[4 + $t_key] = 0; //initialize
                    }
                    if ($tmp["selected"]) {
                        if ($summary == "summary") {
                            if ($t == "t4" || $t == "t9") {
                                $tmp_result[4 + $t_key] += 1; //increment selected
                            } else {
                                $tmp_result[4 + $t_key] += $tmp["value"];
                            }
                            $total += $tmp["value"]; //total using value
                        } else {
                            if ($t == "t2") {
                                $tmp_result[4 + $t_key] += $tmp["value"];
                                $total += $tmp["value"]; //total using value
                            } else {
                                $tmp_result[4 + $t_key] += 1; //increment selected
                            }
                        }
                    }
                }
            } else {
                $equivalent = Chart::items($t, $prime);
                $tmp_result[3] = $equivalent;
                $questions = Chart::getQuestion($t);
                foreach ($questions["choices"] as $c_key => $choice) {
                    if (!isset($tmp_result[4 + $c_key])) {
                        $tmp_result[4 + $c_key] = 0;
                    }
                }
                $data_count = count($questions["choices"]);
            }
            $a++;
        } while ($a < $count);

        $i = 0;
        while (count($tmp_result) < 9) {
            $i++;
            $tmp_result[5 + $data_count + $i] = ""; //assign spacing
        }

        if ($summary == "summary") {
            if ($data_count > 2) {
                $max_point = $count * $data_count; //max point
            } else {
                $max_point = $count; //max point
            }
            if ($max_point == 0) {
                $segment1 = 0;
            } else {
                if ($t == "t2") {
                    $segment1 = round($total / $max_point);
                    $max_point = 100 * $max_point;
                } else {
                    $segment1 = round(($total / $max_point) * 100); //segment 1
                }
            }
            if ($t == "t4" || $t == "t9") {
                for ($i = 0; $i < $data_count; $i++) {
                    //loop and change value to percentage
                    $tmp_result[4 + $i] = round(
                        ($tmp_result[4 + $i] / $max_point) * 100
                    );
                }
            }
        } else {
            $total = $count;
            if ($t == "t2" && $count > 0) {
                $tmp_result[4] = round($tmp_result[4] / $total);
            } else {
                $tmp_result[4] = 0;
            }
        }
        if ($table == false) {
            $tmp_result[5 + $data_count] = $total; //total
            $tmp_result[] = $max_point ?? "";
            $tmp_result[] = $segment1 ?? "";
        }

        /* $tmp_result[6+$data_count] = $max_point ?? '';
         $tmp_result[7+$data_count] = $segment1 ?? ''; */

        return $tmp_result->toArray();
    }

    public function getKPIData($records, $t, $prime, $key)
    {
        $counts = 0;
        $count_records = count($records);
        foreach ($records as $record) {
            switch ($key) {
                case 3: //T7
                    $tmp_data = $record->data[$t];
                    break;
                default:
                    if (isset($record->data[$t]["responses"])) {
                        $tmp_data = collect(
                            $record->data[$t]["responses"][0]["primes"]
                        )->firstWhere("index", $prime);
                    } else {
                        $tmp_data = null;
                    }
                    break;
            }
            if ($tmp_data != null) {
                switch ($t) {
                    case "t3":
                        $evaluate = [4, 5];
                        break;
                    case "t4":
                    case "t9":
                        $evaluate = [1];
                        break;
                    case "t5":
                        $evaluate = [4, 5];
                        break;
                    default:
                        //t7
                        $evaluate = [4, 5];
                        break;
                }
                foreach ($evaluate as $value) {
                    if ($t == "t7") {
                        if ($tmp_data == $value) {
                            $counts++;
                            break;
                        }
                    } else {
                        $data = collect($tmp_data["data"])->firstWhere(
                            "value",
                            $value
                        );
                        if ($data["selected"]) {
                            $counts++;
                            break;
                        }
                    }
                }
            }
        }

        if ($count_records > 0) {
            $percent = round(($counts / $count_records) * 100);
        } else {
            $percent = 0;
        }

        return ["percent" => $percent];
    }

    public function exportTracker($chart, $legends)
    {
        $results = [];
        $headers = [];
        $record_ids = collect([]);

        /* $headers['T2_1'] = 'b3_1';
        $headers['T2_2'] = 'b3_2';
        $headers['T2_3'] = 'b3_3'; */

        ksort($legends, 4); // 4 = SORT_NATURAL

        foreach ($legends as $legend) {
            $series = collect($chart->series)->firstWhere("name", $legend);
            foreach ($series["data"] as $data) {
                //loop for segment
                $record_ids = $record_ids->merge($data["record_ids"]);
            }
            switch ($legend) {
                case "T2_1":
                    $headers[$legend] = "b3_1";
                    break;
                case "T2_2":
                    $headers[$legend] = "b3_2";
                    break;
                case "T2_3":
                    $headers[$legend] = "b3_3";
                    break;
                case "T2_4":
                    $headers[$legend] = "b3_4";
                    break;
                case "T11_1":
                    $headers[$legend] = "d1";
                    break;
                case "T12_1":
                    $headers[$legend] = "d2";
                    break;
                default:
                    $headers[$legend] = "-";
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

        $records = Record::whereIn(
            "id",
            $record_ids->unique()->toArray()
        )->get();
        foreach ($records as $record) {
            $tmp = [
                $record->participant_id,
                $record->updated_at->toDateTimeString(),
                Chart::getCountry($record->country),
                $record->meta["query"]["b2_1"] ?? "-",
                $record->meta["query"]["b2_2"] ?? "-",
            ];
            foreach ($headers as $hkey => $header) {
                $ts = Str::of($hkey)->explode("_");
                $t = Str::lower($ts[0]); //t3
                if ($ts[0] == "T4" && $ts[1] == 21) {
                    $prime = 19;
                } else {
                    $prime = $ts[1] ?? null;
                }

                switch ($t) {
                    case "t3":
                    case "t4":
                    case "t5":
                    case "t8":
                    case "t9":
                    case "t10":
                        if ($record->data[$t]) {
                            $responses = collect(
                                $record->data[$t]["responses"][0]["primes"]
                            )->firstWhere("index", $prime);
                            $val = "-";
                            if ($responses != null) {
                                foreach (
                                    $responses["data"]
                                    as $reskey => $resdata
                                ) {
                                    if ($resdata["selected"]) {
                                        $val = $reskey + 1;
                                        break;
                                    }
                                }
                            }
                        }
                        break;
                    case "t6":
                    case "t7":
                        if ($prime == $record->data[$t]) {
                            $val = 1;
                        } else {
                            $val = 0;
                        }
                        break;
                    case "t11":
                    case "t12":
                    case "t2":
                        if (!isset($record->meta["query"][$header])) {
                            $val = "-";
                        } else {
                            $val = $record->meta["query"][$header];
                        }
                        break;
                    default:
                        //f
                        /* if ($record->meta['query'][$header] == $prime) {
                            $val = 1;
                        }else {
                            $val = 0;
                        } */
                        break;
                }
                $tmp[] = $val ?? "-";
            }
            $results[] = $tmp;
        }
        return ["results" => $results, "headers" => collect($headers)->keys()];
    }

    public function getScore($records, $legend, $prime)
    {
        $max_value = 0;
        $points = 0;
        $percentage = [
            "green" => [
                "colour" => "green",
                "value" => 0,
                "count" => 0,
                "name" => "Green Box %",
                "label" => "T2B", //T2B
                "active" => true,
            ],
            "orange" => [
                "colour" => "orange",
                "value" => 0,
                "count" => 0,
                "name" => "Amber Box %",
                "label" => "MB", //MB
                "active" => false,
            ],
            "red" => [
                "colour" => "red",
                "value" => 0,
                "count" => 0,
                "name" => "Red Box %",
                "label" => "B2B", //B2B
                "active" => false,
            ],
        ];
        if ($legend == "t6") {
            unset($percentage["red"]);
            unset($percentage["orange"]);
            $colour = "green";
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
        } elseif ($legend == "t7") {
            unset($percentage["red"]);
            unset($percentage["orange"]);
            $colour = "green";
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
        } elseif ($legend == "t2" || $legend == "t11" || $legend == "t12") {
            unset($percentage["red"]);
            unset($percentage["orange"]);
            $colour = "green";
        } elseif ($legend == "t4" || $legend == "t9") {
            unset($percentage["orange"]);
        }
        $tcount = count($records);
        $tmp_data = [];
        foreach ($records as $record) {
            switch ($legend) {
                case "t2":
                case "t6":
                case "t7":
                case "t11":
                case "t12":
                    $tmp_data = Chart::getExpData($legend, $record, $prime);
                    break;
                default:
                    if (isset($record->data[$legend]["responses"])) {
                        $tmp_data = collect(
                            $record->data[$legend]["responses"][0]["primes"]
                        )->firstWhere("index", $prime);
                    } else {
                        $tmp_data = null;
                    }
                    break;
            }

            if ($tmp_data != null) {
                if ($max_value == 0) {
                    if (count($tmp_data["data"]) > 2) {
                        $max_value = $tcount * count($tmp_data["data"]);
                    } else {
                        $max_value = $tcount;
                    }
                }
                foreach ($tmp_data["data"] as $t_key => $tmp) {
                    /* if (!isset($points[$t_key])) {
                        $points[$t_key] = 0;
                    } */
                    if ($tmp["selected"]) {
                        // $points+=($t_key + 1);
                        $points += $tmp["value"];
                        switch ($legend) {
                            case "t2":
                                $percentage[$colour]["count"] += $tmp["value"];
                                break;
                            case "t3":
                            case "t5":
                            case "t8":
                                if ($t_key <= 1) {
                                    $percentage["red"]["count"] += 1;
                                } elseif ($t_key == 2) {
                                    $percentage["orange"]["count"] += 1;
                                } else {
                                    $percentage["green"]["count"] += 1;
                                }
                                break;
                            case "t4":
                            case "t9":
                                if ($prime == 19) {
                                    if ($t_key == 0) {
                                        $percentage["green"]["count"] += 1; //change 1/14/2021 NO
                                    } else {
                                        $percentage["red"]["count"] += 1; //change 1/14/2021 YES
                                    }
                                } else {
                                    if ($t_key == 0) {
                                        $percentage["red"]["count"] += 1; //NO
                                    } else {
                                        $percentage["green"]["count"] += 1; //YES
                                    }
                                }

                                /* unset($percentage['orange']); */
                                break;
                            case "t10":
                                if ($tmp["value"] == 1 || $tmp["value"] == 2) {
                                    $percentage["red"]["count"] += 1;
                                } elseif ($tmp["value"] == 4) {
                                    $percentage["green"]["count"] += 1;
                                } elseif ($tmp["value"] == 3) {
                                    $percentage["orange"]["count"] += 1;
                                }
                                /* if ($t_key == 0 && $t_key == 1) {
                                    $percentage['red']['count'] += 1;
                                }elseif ($t_key == 3) {
                                    $percentage['green']['count'] += 1;
                                }else  {
                                    $percentage['orange']['count'] += 1;
                                } */
                                break;
                            default:
                                # t2 t6 t7 t11 t12
                                $percentage[$colour]["count"] += 1;
                                break;
                        }
                    }
                }
            }
        }
        $true_count =
            ($percentage["red"]["count"] ?? 0) +
            ($percentage["green"]["count"] ?? 0) +
            ($percentage["orange"]["count"] ?? 0);
        if ($tcount > 0) {
            foreach ($percentage as $key => $percent) {
                if ($legend == "t2") {
                    $percentage[$key]["value"] = round(
                        $percent["count"] / $tcount
                    );
                } else {
                    $percentage[$key]["value"] =
                        $true_count === 0
                            ? 0
                            : round(($percent["count"] / $true_count) * 100);
                }
                // $percent['value'] = ceil($percent['count'] / $tcount);
                /* if ($percentage[$key]['value'] > $this->tops['colours'][$key]) {
                    $this->tops['colours'][$key] = $percentage[$key]['value'];
                } */
            }
        }
        // dd($percentage);
        $score = $max_value > 0 ? ($points / $max_value) * 100 : null;
        $question = Chart::getQuestion($legend);
        $equivalent = Chart::items($legend, $prime); //$tmp_data['prime'] ?? null;
        if (
            $legend == "t2" ||
            $legend == "t6" ||
            $legend == "t7" ||
            $legend == "t11" ||
            $legend == "t12"
        ) {
            $targets = [""];
        } else {
            $targets = $question["choices"];
        }

        return [
            "gscore" => round($score),
            // 'prime' => $legend == 't5' ? ($tmp_data['equivalent'] ?? '').' '.$equivalent : $equivalent,
            "prime" => $equivalent,
            "percentage" => $percentage,
            "question" => $question["question"],
            "dimension" => $question["dimension"],
            "targets" => $targets,
        ];
    }
}
