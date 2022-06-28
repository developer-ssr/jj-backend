<?php

use App\Http\Controllers\RecordController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\Custom\UpdateController;
use App\Models\Email;
use App\Models\Office;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return Inertia::render('Welcome', [
    //     'canLogin' => Route::has('login'),
    //     'canRegister' => Route::has('register'),
    //     'laravelVersion' => Application::VERSION,
    //     'phpVersion' => PHP_VERSION,
    // ]);
    return redirect('https://jnj.splitsecondsurveys.co.uk/dashboard');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

// Route::get('/test', [TestController::class, 'index']);
Route::get('/complete', [RecordController::class, 'complete']);

Route::get('test2', function() {
    // $string = '{"segments":[{"from":"2021-09-1 10:08:01","to":"2021-09-17 10:47:08"},{"from":"2021-09-17 10:08:01","to":"2021-09-24 10:47:08"},{"from":"2021-10-1 10:08:01","to":"2021-10-17 10:47:08"}],"legends":[{"name":"t3","primes":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]}]}';
    // $filter = json_decode($string, true);
    // //$date = Carbon::parse("2021-09-23 08:53:10")->format("dmy-Hi");
    // $date = Carbon::parse("2021-09-23 08:53:10")->format("d F Y");
    // // dd($date);
    // $offices = Office::with('links')->get()->toArray();
    //     $offices = collect($offices)->map(function($values) {
    //         $email = Email::where('email', $values['email'])->orderBy('created_at', 'desc')->first();
    //         $values['emails'] = $email;
    //         $taken = collect($values['links'])->filter(fn($v) => $v['taken'] == 'YES')->count();
    //         $values['links'] = $taken . '/' . count($values['links']);
    //         return $values;
    //     })->toArray();
    //     dd($offices);
});

/* Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('charts')->group(function () {
        Route::get('/download/{id}/{summary}', [ExportController::class, 'download']);
    });
}); */
Route::prefix('charts')->group(function () {
    Route::get('/download/{id}/{summary}', [ExportController::class, 'download']);
});

Route::prefix('offices')->group(function () {
    Route::get('/download', [OfficeController::class, 'download'])->middleware('auth:sanctum');
    // Route::get('/download', [OfficeController::class, 'download']);
    Route::get('/download_office/{ecp}', [ExportController::class, 'downloadOffice']);
});

Route::prefix('baseline')->group(function () {
    Route::get('/download/{summary}', [ExportController::class, 'downloadBaseline']);
});

Route::prefix('custom')->group(function () {
    Route::prefix('update')->group(function () {
        Route::get('/updatephase2null', [UpdateController::class, 'updatePhase2Null']);
    });
});
