<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\Office;
use App\Models\Record;
use Illuminate\Http\Request;
use App\Exports\CsvExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class OfficeController extends Controller
{
    public function addresses()
    {
        return Record::all()->pluck('meta')->map(fn ($v) => $v['office'])->unique()->toArray();
    }

    public function download(Request $request)
    {
        if ($request->user()->type === "user") {
            $offices = Office::where('id', $request->user()->office_id)->get();
        } else if ($request->user()->type === "admin") {
            $offices = Office::with('links')->get()->toArray();
            $offices = collect($offices)->map(function($values) {
                $email = Email::where('email', $values['email'])->orderBy('created_at', 'desc')->where('status', 'sent')->pluck('created_at')->toArray();
                $values['emails'] = $email;
                $taken = collect($values['links'])->filter(fn($v) => $v['taken'] === 'YES')->count();
                $values['links2'] = $taken . '/' . count($values['links']);
                return $values;
            });
        } else if ($request->user()->type === 'group_user') {
            $offices = Office::whereIn('id', $request->user()->office_ids)->with('links')->get()->toArray();
            $offices = collect($offices)->map(function($values) {
                $email = Email::where('email', $values['email'])->where('status', 'sent')->orderBy('created_at', 'asc')->get();
                $values['emails'] = $email;
                $taken = collect($values['links'])->filter(fn($v) => $v['taken'] === 'YES')->count();
                $values['links'] = $taken . '/' . count($values['links']);
                return $values;
            });
        }
        $results = [
            // ['Name', 'Email', 'Country', 'Invite Sent', 'Test Taken', 'CSR Name', 'CSR Email', 'Client Name', 'Client Email', 'Classification', 'Baseline 1 Status', 'Baseline 1 Completed', 'Baseline 2 Status', 'Baseline 2 Completed', 'Invite 1', 'Invite 2', 'Invite 3', 'Invite 4']
            ['Name', 'Email', 'Country', 'Test Taken', 'CSR Name', 'CSR Email', 'Client Name', 'Client Email', 'Classification', 'Baseline 1 Status', 'Baseline 1 Completed', 'Baseline 2 Status', 'Baseline 2 Completed', 'Invite 1', 'Invite 2', 'Invite 3', 'Invite 4', 'Email dates']
        ];
        $code = [
            840 => "USA",
            702 => 'Singapore',
            344 => 'Hongkong',
            124 => 'Canada'
        ];
        $code2 = [
            840 => ['4fa', 'v8h'],
            702 => ['XWp', 'N8N'],
            344 => ['4xS', '8eC'],
            124 => ['5Ph', 'Mam']
        ];
        foreach ($offices as $office) {
            if ($office['type'] === 'office') {
                $base1_query = [
                    'code' => $code2[$office['code']][0],
                    'value' => $office['email']
                ];
                $base2_query = [
                    'code' => $code2[$office['code']][1],
                    'value' => $office['email']
                ];
                $_base1 = Http::get('https://fluent.splitsecondsurveys.co.uk/api/ecp/row?' . http_build_query($base1_query));
                $_base2 = Http::get('https://fluent.splitsecondsurveys.co.uk/api/ecp/row?' . http_build_query($base2_query));
                $base1 = json_decode($_base1->body(), true);
                $base2 = json_decode($_base2->body(), true);

                $results[] = collect([
                    $office['name'], 
                    $office['email'], 
                    $code[$office['code']], 
                    // !is_null($office['emails']) ? Carbon::parse($office['emails']['created_at'])->toDateTimeString() : null,
                    explode('/', $office['links2'])[0] == '1' ? 'Yes': 'No', 
                    $office['csr_name'], 
                    $office['csr_email'], 
                    $office['client_name'], 
                    $office['client_email'], 
                    $office['classification'],
                    empty($base1) ? null : 'Completed',
                    empty($base1) ? null : Carbon::parse($base1['updated_at'])->toDateTimeString(),
                    empty($base2) ? null : 'Completed',
                    empty($base2) ? null : Carbon::parse($base2['updated_at'])->toDateTimeString(),
                    empty($office['links'][0]['record']) ? null : Carbon::parse($office['links'][0]['record']['updated_at'])->toDateTimeString(),
                    empty($office['links'][1]['record']) ? null : Carbon::parse($office['links'][1]['record']['updated_at'])->toDateTimeString(),
                    empty($office['links'][2]['record']) ? null : Carbon::parse($office['links'][2]['record']['updated_at'])->toDateTimeString(),
                    empty($office['links'][3]['record']) ? null : Carbon::parse($office['links'][3]['record']['updated_at'])->toDateTimeString(),
                    
                ])->merge(collect($office['emails'])->map(fn($e) => Carbon::parse($e)->toDateTimeString())->toArray())->toArray();
            }
                
        }
        return Excel::download(CsvExport::new($results), "Offices.xlsx");
    }

    public function index(Request $request)
    {
        if ($request->user()->type === "user") {
            $offices = Office::where('id', $request->user()->office_id)->get();
        } else if ($request->user()->type === "admin") {
            $offices = Office::with('links')->get()->toArray();
            $offices = collect($offices)->map(function($values) {
                $email = Email::where('email', $values['email'])->orderBy('created_at', 'desc')->first();
                $values['emails'] = $email;
                $taken = collect($values['links'])->filter(fn($v) => $v['taken'] === 'YES')->count();
                $values['links'] = $taken . '/' . count($values['links']);
                return $values;
            });
        } else if ($request->user()->type === 'group_user') {
            $offices = Office::whereIn('id', $request->user()->office_ids)->with('links')->get()->toArray();
            $offices = collect($offices)->map(function($values) {
                $email = Email::where('email', $values['email'])->orderBy('created_at', 'desc')->first();
                $values['emails'] = $email;
                $taken = collect($values['links'])->filter(fn($v) => $v['taken'] === 'YES')->count();
                $values['links'] = $taken . '/' . count($values['links']);
                return $values;
            });
        }
        
        return response()->json($offices);
    }

    public function show(Office $office)
    {
        return response()->json($office);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable',
            'type' => 'required',
            'code' => 'required',
            'csr_name' => 'nullable',
            'csr_email' => 'nullable',
            'client_name' => 'nullable',
            'client_email' => 'nullable',
            'classification' => 'nullable'
        ]);
        $office = Office::create([
            'name' => $request->name,
            'email' => $request->email,
            'user_id' => $request->user()->id,
            'type' => strtolower($request->type),
            'code' => $request->code,
            'sequence' => $request->sequence,
            'csr_name' => $request->csr_name ?? null,
            'csr_email' => $request->csr_email ?? null,
            'client_name' => $request->client_name ?? null,
            'client_email' => $request->client_email ?? null,
            'classification' => $request->classification ?? null
        ]);
        return response()->json($office);
    }

    public function update(Request $request, Office $office)
    {
        $request->validate([
            'name' => 'required',
            //'address' => 'required',
            'type' => 'required',
            'code' => 'required',
            'email' => 'nullable',
            'csr_name' => 'nullable',
            'csr_email' => 'nullable',
            'client_name' => 'nullable',
            'client_email' => 'nullable',
            'classification' => 'nullable'
            
        ]);
        $office->update([
            'name' => $request->name,
            //'address' => $request->address,
            'type' => $request->type,
            'email' => $request->email,
            'code' => $request->code,
            'sequence' => $request->sequence,
            'csr_name' => $request->csr_name ?? null,
            'csr_email' => $request->csr_email ?? null,
            'client_name' => $request->client_name ?? null,
            'client_email' => $request->client_email ?? null,
            'classification' => $request->classification ?? null
        ]);
        return response()->json($office);
    }

    public function destroy(Office $office)
    {
        $office->delete();
        return response('ok');
    }
}
