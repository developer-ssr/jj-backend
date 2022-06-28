<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecordController extends Controller
{
    protected $act_api = 'https://ast.splitsecondsurveys.co.uk/api/v1/record/?';

    public function index(Request $request)
    {
        return response()->json(Record::all());
    }
    public function complete(Request $request)
    {

        $lang = $request->lang ?? 'en';
        $country = $request->country ?? 'us';

        switch ($country) {
            case 'us':
            case 'sg':
            case 'ca':
                if ($request->MyopiaLenses == 2) { //night
                    $country_t3 = [
                        'us' => [
                            'en' => '1266'
                        ],
                        'sg' => [
                            'en' => '1282'
                        ],
                        'ca' => [
                            'en' => '1274'
                        ]
                    ];
                    $country_t4 = [
                        'us' => [
                            'en' => '1261'
                        ],
                        'sg' => [
                            'en' => '1277'
                        ],
                        'ca' => [
                            'en' => '1269'
                        ]
                    ];
                }elseif ($request->MyopiaLenses == 1) { //day
                    $country_t3 = [
                        'us' => [
                            'en' => '1260'
                        ],
                        'sg' => [
                            'en' => '1276'
                        ],
                        'ca' => [
                            'en' => '1268'
                        ]
                    ];
                    $country_t4 = [
                        'us' => [
                            'en' => '1267'
                        ],
                        'sg' => [
                            'en' => '1283'
                        ],
                        'ca' => [
                            'en' => '1275'
                        ]
                    ];
                }elseif ($request->MyopiaLenses == 3) { //both
                    $country_t3 = [
                        'us' => [
                            'en' => '1308'
                        ],
                        'sg' => [
                            'en' => '1313'
                        ],
                        'ca' => [
                            'en' => '1311'
                        ]
                    ];
                    $country_t4 = [
                        'us' => [
                            'en' => '1310'
                        ],
                        'sg' => [
                            'en' => '1314'
                        ],
                        'ca' => [
                            'en' => '1312'
                        ]
                    ];
                }else {
                    dd("Something went wrong Country");
                }
            break;
            case 'hk':
                if ($request->MyopiaLenses == 3 || $request->MyopiaLenses == 4) { //night
                    $country_t3 = [
                        'hk' => [
                            'en' => '1291',
                            'cn' => '1299'
                        ]
                    ];
                    $country_t4 = [
                        'hk' => [
                            'en' => '1286',
                            'cn' => '1294'
                        ]
                    ];
                } elseif ($request->MyopiaLenses == 1 || $request->MyopiaLenses == 2) { //day
                    $country_t3 = [
                        'hk' => [
                            'en' => '1285',
                            'cn' => '1293'
                        ]
                    ];
                    $country_t4 = [
                        'hk' => [
                            'en' => '1292',
                            'cn' => '1300'
                        ]
                    ];
                } elseif ($request->MyopiaLenses == 5 || $request->MyopiaLenses == 6) { //both
                    $country_t3 = [
                        'hk' => [
                            'en' => '1315',
                            'cn' => '1317'
                        ]
                    ];
                    $country_t4 = [
                        'hk' => [
                            'en' => '1316',
                            'cn' => '1318'
                        ]
                    ];
                } else {
                    dd("Something went wrong HK");
                }
            break;
            default:
                dd("Something went wrong");
            break;
        }

        $country_t5 = [
            'us' => [
                'en' => '1262'
            ],
            'sg' => [
                'en' => '1278'
            ],
            'hk' => [
                'en' => '1287',
                'cn' => '1295'
            ],
            'ca' => [
                'en' => '1270'
            ]
        ];

        $country_t8 = [
            'us' => [
                'en' => '1263'
            ],
            'sg' => [
                'en' => '1279'
            ],
            'hk' => [
                'en' => '1288',
                'cn' => '1296'
            ],
            'ca' => [
                'en' => '1271'
            ]
        ];
        $country_t9 = [
            'us' => [
                'en' => '1264'
            ],
            'sg' => [
                'en' => '1280'
            ],
            'hk' => [
                'en' => '1289',
                'cn' => '1297'
            ],
            'ca' => [
                'en' => '1272'
            ]
        ];
        $country_t10 = [
            'us' => [
                'en' => '1265'
            ],
            'sg' => [
                'en' => '1281'
            ],
            'hk' => [
                'en' => '1290',
                'cn' => '1298'
            ],
            'ca' => [
                'en' => '1273'
            ]
        ];

        $country_link = [
            'us' => 'nax',
            'sg' => 'jqF',
            'hk' => '5vw',
            'ca' => 'sqV'
        ];

        $http = Http::get($this->act_api . "survey_id=" . $country_t3[$country][$lang] . "&id={$request->id}");
        $data = [
            't3' => json_decode($http->body(), true) ?? null
        ];

        $http = Http::get($this->act_api . "survey_id=" . $country_t4[$country][$lang] . "&id={$request->id}");
        $data['t4'] = json_decode($http->body(), true) ?? null;

        $http = Http::get($this->act_api . "survey_id=" . $country_t5[$country][$lang] . "&id={$request->id}");
        $data['t5'] = json_decode($http->body(), true);

        $http = Http::get($this->act_api . "survey_id=" . $country_t8[$country][$lang] . "&id={$request->id}");
        $data['t8'] = json_decode($http->body(), true);

        $http = Http::get($this->act_api . "survey_id=" . $country_t9[$country][$lang] . "&id={$request->id}");
        $data['t9'] = json_decode($http->body(), true);

        $http = Http::get($this->act_api . "survey_id=" . $country_t10[$country][$lang] . "&id={$request->id}");
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

    public function destroy(Record $record)
    {
        $record->delete();
        return response('ok');
    }
}
