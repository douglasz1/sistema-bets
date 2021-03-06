<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('perfil', 'API\LoginController@perfil')->middleware('api-checar:seller,manager');
Route::post('login', 'API\LoginController@login');

Route::middleware('api-checar:seller,manager')
    ->prefix('simulador')
    ->namespace('API\V2\Simulador')
    ->group(function () {
        Route::get('partidas', 'PartidasController@partidas');
        Route::get('cotacoes/{eventoID}', 'PartidasController@cotacoes');
    });

Route::get('/bet/{id}', 'BetsController@printToJson');
Route::get('/app/bet/{id}', 'BetsController@jsonToApp');
Route::get('/app/league/{id}/{userId}', 'LeaguesController@jsonToApp');

Route::get('bolao/bilhete/{id}', 'API\V2\Bolao\SimuladorController@bilheteJson');

Route::prefix('casa-apostas')->name('casa-apostas')->group(function () {
    Route::post('bilhete/salvar', 'API\CasaDasApostas\BilheteController@salvar');
    Route::get('bilhete/{id}', 'API\CasaDasApostas\BilheteController@index');
    Route::get('partidas', 'API\CasaDasApostas\PartidasController@index');
    Route::get('partidas/{id}', 'API\CasaDasApostas\PartidasController@abrir');
    Route::get('campeonatos', 'API\CasaDasApostas\LigasController@index');
    Route::get('campeonatos/{id}', 'API\CasaDasApostas\LigasController@abrir');
});

Route::get('partidas', 'API\V2\Simulador\PartidasController@partidas')->name('partidas');
