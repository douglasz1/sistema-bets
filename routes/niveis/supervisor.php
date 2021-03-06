<?php

Route::group(['prefix' => 'managers', 'as' => 'managers.'], function () {
    Route::get('/', 'ManagersController@index')->name('index');
    Route::get('all', 'ManagersController@all')->name('all');
    Route::get('create', 'ManagersController@create')->name('create');
    Route::post('store', 'ManagersController@store')->name('store');
    Route::get('edit/{id}', 'ManagersController@edit')->name('edit');
    Route::post('update/{id}', 'ManagersController@update')->name('update');
    Route::get('status/{id}', 'ManagersController@changeStatus')->name('status');
    Route::get('pluck', 'ManagersController@pluck')->name('pluck');
});

Route::group(['prefix' => 'sellers', 'as' => 'sellers.'], function () {
    Route::get('/', 'SellersController@index')->name('index');
    Route::get('all', 'SellersController@all')->name('all');
    Route::get('create', 'SellersController@create')->name('create');
    Route::post('store', 'SellersController@store')->name('store');
    Route::get('edit/{id}', 'SellersController@edit')->name('edit');
    Route::post('update/{id}', 'SellersController@update')->name('update');
    Route::get('status/{id}', 'SellersController@changeStatus')->name('status');
    Route::get('pluck', 'SellersController@pluck')->name('pluck');
});

Route::group(['prefix' => 'cashier', 'as' => 'cashier.'], function () {
    Route::get('index', 'CashierController@index')->name('index');
    Route::get('report', 'CashierController@report')->name('report');
    Route::post('search', 'CashierController@search')->name('search');
});

Route::group(['prefix' => 'financial', 'as' => 'financial.'], function () {
    Route::get('index', 'FinancialController@index')->name('index');
    Route::get('report', 'FinancialController@report')->name('report');
});

Route::group(['prefix' => 'bets', 'as' => 'bets.'], function () {
    Route::get('summary/{id}', 'BetsController@summary')->name('summary');
    Route::post('cancel/{id}', 'BetsController@cancel')->name('cancel');
});

Route::group(['prefix' => 'tips', 'as' => 'tips.'], function () {
    Route::post('cancel/{id}', 'TipsController@cancel')->name('cancel');
});

Route::group(['prefix' => 'companies', 'as' => 'companies.'], function () {
    Route::get('pluck', 'CompaniesController@pluck')->name('pluck');
});

Route::group(['prefix' => 'balance', 'as' => 'balance.'], function () {
    Route::get('index', 'BalanceController@index')->name('index');
    Route::get('sellers', 'BalanceController@sellers')->name('sellers');
    Route::post('send', 'BalanceController@send')->name('send');
    Route::post('reset', 'BalanceController@reset')->name('reset');
    Route::post('reset-all', 'BalanceController@resetAll')->name('resetAll');
    Route::post('zero-all', 'BalanceController@zeroToAll')->name('zeroToAll');
});

Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
    Route::group(['prefix' => 'analitico', 'as' => 'analitico.'], function () {
        Route::get('/', 'AnaliticoController@index')->name('index');
        Route::get('report', 'AnaliticoController@report')->name('report');
        Route::get('summary/{id}', 'AnaliticoController@summary')->name('summary');
    });
});

Route::group(['prefix' => 'bolao/acompanhamento', 'as' => 'bolao.acompanhamento.'], function () {
    Route::get('/', 'AcompanhamentoController@index')->name('index');
    Route::get('relatorio', 'AcompanhamentoController@relatorio')->name('relatorio');
    Route::post('busca', 'AcompanhamentoController@busca')->name('busca');
    Route::put('cancelar/{id}', 'AcompanhamentoController@cancelar')->name('cancelar');
});
