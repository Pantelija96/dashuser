<?php

use App\Http\Controllers\BackendController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [FrontendController::class, 'loginPage']);
Route::post('/login', [BackendController::class, 'login'])->name('login');
Route::get('/logout',[BackendController::class, 'logout'])->name('logout');

Route::get('/test', [FrontendController::class, 'test']);

Route::group(['middleware' => ['auth:web']], function (){
    Route::get('/home/{homeType?}', [FrontendController::class, 'home']);
    Route::get('/search/{homeType?}/', [FrontendController::class, 'search'])->name('search');
});

Route::prefix('/insert')->group(function (){
    Route::get('/token', [BackendController::class, 'getToken']);

    Route::controller(BackendController::class)->group(function(){
        Route::post('/lokacijaapp', 'dodajLokacijuApp');
        Route::post('/nazivservisa', 'dodajNazivServisa');
        Route::post('/partner', 'dodajPartnera');
        Route::post('/tehnologije', 'dodajTehnologije');
        Route::post('/tipservisa', 'dodajTipServisa');
        Route::post('/tipugovora', 'dodajTipUgovora');
        Route::post('/vrstasenzora', 'dodajVrstuSenzora');
    });

    Route::controller(FrontendController::class)->group(function (){
        Route::get('/lokacijaapp/{id?}', 'dodajLokacijuApp');
        Route::get('/nazivservisa/{id?}', 'dodajNazivServisa');
        Route::get('/partner/{id?}', 'dodajPartnera');
        Route::get('/tehnologije/{id?}', 'dodajTehnologije');
        Route::get('/tipservisa/{id?}', 'dodajTipServisa');
        Route::get('/tipugovora/{id?}', 'dodajTipUgovora');
        Route::get('/vrstasenzora/{id?}', 'dodajVrstuSenzora');
    });
});

Route::prefix('/edit')->group(function (){
    Route::controller(BackendController::class)->group(function(){
        Route::post('/lokacijaapp', 'editLokacijuApp');
        Route::post('/nazivservisa', 'editNazivServisa');
        Route::post('/partner', 'editPartnera');
        Route::post('/tehnologije', 'editTehnologije');
        Route::post('/tipservisa', 'editTipServisa');
        Route::post('/tipugovora', 'editTipUgovora');
        Route::post('/vrstasenzora', 'editVrstuSenzora');
    });
});
