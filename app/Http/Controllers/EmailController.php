<?php

namespace App\Http\Controllers;

use App\Models\Email;

use App\Mail\JnJMail;
use App\Mail\NotifyEcp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmailController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required',
            //'file' => 'nullable|file|mimes:pdf,PDF',
            'message' => 'nullable|string',
            'subject' => 'required'
        ]);
        
        $filename = null;
        $path = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $fileHash = $file->hashName();
            $rand_string = Str::random(10);
            $path = $rand_string . "/{$fileHash}";
            $file->store($rand_string);
        }
        if (isset($request->id)) {
            $email = Email::find($request->id);
            if ($request->hasFile('file')) {
                $email->update([
                    'path' => $path,
                    'file' => $filename
                ]);
            } else {
                if ($request->remove_attachment ?? false) {
                    $email->update([
                        'path' => null,
                        'file' => null
                    ]);
                }
            }
            
            $email->update([
                'email' => $request->email,
                'message' => $request->message,
                'subject' => $request->subject,
                'status' => 'sent',
                'meta' => [
                    'cc' => $request->csr_email ?? null,
                    'bc' => $request->client_email ?? null
                ]
            ]);
        }else {
            $email = Email::create([
                'email' => $request->email,
                'message' => $request->message,
                'user_id' => $request->user()->id,
                'path' =>  $path,
                'file' => $filename,
                'subject' => $request->subject,
                'meta' => [
                    'cc' => $request->csr_email ?? null,
                    'bc' => $request->client_email ?? null
                ]
            ]);
        }
        Mail::to($request->email)->send(new NotifyEcp($email));
        Mail::to("jnj@splitsecondresearch.co.uk")->send(new NotifyEcp($email));
        Mail::to("cris.tarpin@splitsecondsoftware.com")->send(new NotifyEcp($email));
        if ($request->csr_email != 'null') {
            Mail::to($request->csr_email)->send(new NotifyEcp($email));
        }
        
        if ($request->client_email != "") {
            Mail::to($request->client_email)->send(new NotifyEcp($email));
        }
            
        
        return response()->json($email);
    }

    public function save(Request $request)
    {
        $request->validate([
            'email' => 'required',
            //'file' => 'nullable|file|mimes:pdf,PDF',
            'message' => 'nullable|string',
            'subject' => 'required'
        ]);
        $filename = null;
        $path = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $fileHash = $file->hashName();
            $rand_string = Str::random(10);
            $path = $rand_string . "/{$fileHash}";
            $file->store($rand_string);
        }
        if (isset($request->id)) {
            $email = Email::find($request->id);
            $email->update([
                'email' => $request->email,
                'message' => $request->message,
                'user_id' => $request->user()->id,
                'path' =>  $path,
                'file' => $filename,
                'subject' => $request->subject,
                'status' => 'unsent',
                'meta' => [
                    'cc' => $request->csr_email ?? null,
                    'bc' => $request->client_email ?? null
                ]
            ]);
        }else {
            $email = Email::create([
                'email' => $request->email,
                'message' => $request->message,
                'user_id' => $request->user()->id,
                'path' =>  $path,
                'file' => $filename,
                'subject' => $request->subject,
                'status' => 'unsent',
                'meta' => [
                    'cc' => $request->csr_email ?? null,
                    'bc' => $request->client_email ?? null
                ]
            ]);
        }
        
        //Mail::to($request->email)->send(new NotifyEcp($email));

        return response()->json($email);
    }

    public function drafts(Request $request)
    {
        $drafts = Email::where(['user_id' => $request->user()->id, 'status' => 'unsent'])->get();

        return response()->json($drafts);
    }
    
    public function sents(Request $request)
    {
        $sents = Email::where(['user_id' => $request->user()->id, 'status' => 'sent'])->get();

        return response()->json($sents);
    }
}
