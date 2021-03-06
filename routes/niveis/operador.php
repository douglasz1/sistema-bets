<?php

Route::group(['prefix' => 'cashier', 'as' => 'cashier.'], function () {
    Route::get('/', 'CashierController@index')->name('index');
    Route::get('report', 'CashierController@report')->name('report');
});

Route::group(['prefix' => 'bolao/acompanhamento', 'as' => 'bolao.acompanhamento.'], function () {
    Route::get('/', 'AcompanhamentoController@index')->name('index');
    Route::get('relatorio', 'AcompanhamentoController@relatorio')->name('relatorio');
});

Route::group(['prefix' => 'financial', 'as' => 'financial.'], function () {
    Route::get('/', 'FinancialController@index')->name('index');
    Route::get('report', 'FinancialController@report')->name('report');
});

Route::group(['prefix' => 'bets', 'as' => 'bets.'], function () {
    Route::get('summary/{id}', 'BetsController@summary')->name('summary');
    Route::post('cancel/{id}', 'BetsController@cancel')->name('cancel');
});

Route::group(['prefix' => 'clients', 'as' => 'clients.'], function () {
    Route::get('/', 'ClientsController@index')->name('index');
    Route::post('store', 'ClientsController@store')->name('store');
    Route::post('destroy/{id}', 'ClientsController@destroy')->name('destroy');
    Route::get('list', 'ClientsController@list')->name('all');
});
