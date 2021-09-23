<?php

use App\Http\Controllers\RecordController;
use App\Http\Controllers\TestController;
use Illuminate\Foundation\Application;
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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::get('/test', [TestController::class, 'index']);
Route::get('/complete', [RecordController::class, 'complete']);

Route::get('test2', function() {
    $string = '{"segments":[{"from":"2021-09-1 10:08:01","to":"2021-09-17 10:47:08"},{"from":"2021-09-17 10:08:01","to":"2021-09-24 10:47:08"},{"from":"2021-10-1 10:08:01","to":"2021-10-17 10:47:08"}],"legends":[{"name":"t3","primes":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]}]}';
    $filter = json_decode($string, true);
    dd($filter);
});
