<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityRegisterController;
use App\Http\Controllers\CompanyActivityController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyGuideController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\GuideActivityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyActivityController;
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

Route::get('/user', function () {
    $user = \App\Models\User::create([
        'name' => 'emre',
        'email' => 'emre@gmail.com',
        'password' => \Illuminate\Support\Facades\Hash::make('123'),
        'role_id' => 1
    ]);
    if ($user){
        dd('ok');
    }
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//Home
Route::get('home', HomeController::class)->name('home');

// Activity show
Route::get('/activities/{activity}', [ActivityController::class, 'show'])
    ->name('activity.show');

// ActivityRegister
Route::post('/activities/{activity}/register', [ActivityRegisterController::class, 'store'])
    ->name('activities.register');

Route::middleware('auth')->group(function () {

    //Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/activities', [MyActivityController::class, 'show'])->name('my-activity.show');

    // Guide Activities
    Route::get('/guides/activities', [GuideActivityController::class, 'show'])
        ->name('guide-activity.show');

    // Cancel Activity
    Route::delete('/activities/{activity}', [MyActivityController::class, 'destroy'])
        ->name('my-activity.destroy');

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
