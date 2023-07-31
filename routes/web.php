<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CompanyActivityController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyGuideController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//Home
Route::get('home', HomeController::class)->name('home');

Route::middleware('auth')->group(function () {

    //Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Companies
    Route::resource('companies', CompanyController::class)
        ->middleware('isAdmin')
        ->except('show');

    // Companies Users -  companies/{company}/users/{user
    Route::resource('companies.users', CompanyUserController::class)
        ->except('show');

    // Companies Guides - companies/{company}/guides
    Route::resource('companies.guides', CompanyGuideController::class)
        ->except('show');

    // Company activities - companies/{company}/activities/{activity}
    Route::resource('companies.activities', CompanyActivityController::class);

    // Show Activity
    Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activity.show');

});

require __DIR__ . '/auth.php';
