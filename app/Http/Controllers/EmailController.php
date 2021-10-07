<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmailController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'attachment' => 'file|mimes:pdf,PDF',
            'message' => 'nullable|string'
        ]);
        $filename = null;
        $path = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = $file->getClientOriginalName();
            $fileHash = $file->hashName();
            $path = Str::random(10) . "/{$fileHash}";
            $file->store($path);
        }
        $email = Email::create([
            'email' => $request->email,
            'message' => $request->message,
            'user_id' => $request->user()->id,
            'path' =>  $path,
            'file' => $filename
        ]);
        return response()->json($email);
    }
}
