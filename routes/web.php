<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\IdentifyingInformation;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\NewEntryController;
use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\IDPresentedController;
use App\Http\Controllers\ClientRecordsController;
use App\Http\Controllers\ClientCategoryController;
use App\Http\Controllers\ClientRecordsGenIntakesController;
use App\Http\Controllers\ClientRecordsSwitchController;
use App\Http\Controllers\IdentifyingInformationController;
use App\Models\CurrencyRateModel;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/{pathMatch}', function () {
//     return view('app');
// })->where('pathMatch','.*');


Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => Inertia::render('Auth/Login'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return Inertia::render('Home',[
        'title' => 'Home'
    ]);
    })->name('home');
      
});



// Route::middleware(['auth', RoleMiddleware::class.':2'])->group(function () {
Route::get('/new-profile', function () {
    return Inertia::render('Admin/NewProfile/Match/Match'); 
})->name('new-profile');


Route::get('/result', function () {
    return Inertia::render('Admin/NewProfile/Match/Result'); 
})->name('result');

Route::get('/new-client', function () {
    return Inertia::render('Admin/NewProfile/Main'); 
})->name('new-client');


Route::get('/overview', function () {
    return Inertia::render('Admin/Overview/Overview',[
        'title' => 'Overview'
    ]); 
})->name('overview');


// Route::get('/data-settings', function () {
//     return Inertia::render('Superadmin/DataSettings/Main',[
//         'title' => 'Settings'
//     ]); 
// })->name('data-settings');



    Route::get('client-category/', [ClientCategoryController::class, 'index'])->name('client-category.index');
    Route::post('client-category/', [ClientCategoryController::class, 'store'])->name('client-category.store');
    Route::put('client-category/{id}', [ClientCategoryController::class, 'update'])->name('client-category.update');
    Route::get('client-category/categories', [ClientCategoryController::class, 'getCategories'])->name('client-category.categories');



    Route::get('client-idpresented/', [IDPresentedController::class, 'index'])->name('client-idpresented.index');
    Route::post('client-idpresented/', [IDPresentedController::class, 'store'])->name('client-idpresented.store');
    Route::put('client-idpresented/{id}', [IDPresentedController::class, 'update'])->name('client-idpresented.update');
     Route::get('client-idpresented/idpresented', [IDPresentedController::class, 'getIDPresented'])->name('client-category.idpresented');
     




   
    Route::get('client-type/', [ClientTypeController::class, 'index'])->name('client-type.index');
    Route::post('client-type/', [ClientTypeController::class, 'store'])->name('client-type.store');
    Route::put('client-type/{id}', [ClientTypeController::class, 'update'])->name('client-type.update');
    Route::get('client-type/clienttype', [ClientTypeController::class, 'getClienttype'])->name('client-type.get-all');

Route::get('/client-records',[ClientRecordsController::class,'index'])->name('client-records.index');
Route::get('/client-recordsGenIntake',[ClientRecordsGenIntakesController::class,'index'])->name('client-recordsGenIntake.index');


Route::post('/validate-step/{step}', [IdentifyingInformationController::class, 'validateStep'])
    ->name('validate-step');

Route::post('/deportees-submission', [IdentifyingInformationController::class, 'store'])
    ->name('deportees.store');

Route::get('/client/{id}', [IdentifyingInformationController::class, 'show'])
    ->name('client.show');


    Route::get('/client/prefill', [IdentifyingInformationController::class, 'prefill'])
    ->name('client.prefill');
    
    Route::get('/currency/rate', function () {
    return CurrencyRateModel::where('from_currency', 'MYR')
        ->where('to_currency', 'PHP')
        ->latest('rate_date')
        ->first();
});

    // });



