<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\Office;
use App\Models\Record;
use Illuminate\Http\Request;
use App\Exports\CsvExport;
use Carbon\Carbon;
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
        $results = [
            ['Name', 'Email', 'Country', 'Invite Sent', 'Taken Test']
        ];
        $code = [
            840 => "USA",
            702 => 'Singapore',
            344 => 'Hongkong',
            124 => 'Canada'
        ];
        foreach ($offices as $office) {
            if ($office['type'] === 'office') {
                $results[] = [$office['name'], $office['email'], $code[$office['code']], !is_null($office['emails']) ? Carbon::parse($office['emails']['created_at'])->toDateTimeString() : null, explode('/', $office['links'])[0] == '1' ? 'Yes': 'No'];
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
            'client_email' => 'nullable'
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
            'client_email' => $request->email ?? null
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
            'client_email' => 'nullable'
            
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
            'client_email' => $request->email ?? null
        ]);
        return response()->json($office);
    }

    public function destroy(Office $office)
    {
        $office->delete();
        return response('ok');
    }
}
