<?php

Route::group(['prefix' => 'cashier', 'as' => 'cashier.'], function () {
    Route::get('summary', 'CashierController@summary')->name('summary');
    Route::get('summary/report', 'CashierController@summaryReport')->name('summary.report');
    Route::get('monitoring', 'CashierController@monitoring')->name('monitoring');
    Route::get('monitoring/report', 'CashierController@monitoringReport')->name('monitoring.report');
    Route::get('/', 'AcompanhamentoController@index')->name('index');
    Route::get('relatorio', 'AcompanhamentoController@relatorio')->name('relatorio');
});

Route::group(['prefix' => 'bolao/acompanhamento', 'as' => 'bolao.acompanhamento.'], function () {
    Route::get('/', 'AcompanhamentoController@index')->name('index');
    Route::get('geral', 'AcompanhamentoController@geral')->name('geral');
    Route::get('pessoal', 'AcompanhamentoController@pessoal')->name('pessoal');
    Route::get('relatorio', 'AcompanhamentoController@relatorio')->name('relatorio');
});

Route::group(['prefix' => 'financial', 'as' => 'financial.'], function () {
    Route::get('summary', 'FinancialController@summary')->name('summary');
    Route::get('summary/report', 'FinancialController@summaryReport')->name('summary.report');
    Route::get('general', 'FinancialController@general')->name('general');
    Route::get('general/report', 'FinancialController@generalReport')->name('general.report');
});

Route::group(['prefix' => 'sellers', 'as' => 'sellers.'], function () {
    Route::get('/', 'SellersController@index')->name('index');
    Route::get('all', 'SellersController@all')->name('all');
    Route::get('pluck', 'SellersController@pluck')->name('pluck');
    Route::get('status/{id}', 'SellersController@changeStatus')->name('status');
});

Route::group(['prefix' => 'balance', 'as' => 'balance.'], function () {
    Route::get('index', 'BalanceController@index')->name('index');
    Route::get('sellers', 'BalanceController@sellers')->name('sellers');
    Route::post('send', 'BalanceController@send')->name('send');
    Route::post('reset', 'BalanceController@reset')->name('reset');
    Route::post('reset-all', 'BalanceController@resetAll')->name('resetAll');
    Route::post('zero-all', 'BalanceController@zeroToAll')->name('zeroToAll');
});

Route::group(['prefix' => 'payments', 'as' => 'payments.'], function () {
    Route::get('index', 'PaymentsController@index')->name('index');
    Route::get('sellers', 'PaymentsController@sellers')->name('sellers');
    Route::post('send', 'PaymentsController@send')->name('send');
});
