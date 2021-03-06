<?php

Route::post('rules/update/{id}', 'CompaniesController@rulesUpdate')->name('rules.update');

Route::group(['prefix' => 'jogos-manuais', 'as' => 'jogos-manuais.'], function () {
    Route::any('/', 'JogosManuaisController@index')->name('index');
    Route::post('salvar-liga', 'JogosManuaisController@salvarLiga')->name('salvar-liga');
    Route::post('salvar-evento', 'JogosManuaisController@salvarEvento')->name('salvar-evento');
});

Route::get('goals', 'GoalsController@index')->name('goals.index');
Route::post('goals/all', 'GoalsController@all')->name('goals.all');

Route::group(['prefix' => 'auditoria', 'as' => 'auditoria.'], function () {
    Route::get('/', 'AuditoriaController@index')->name('index');
    Route::get('listar', 'AuditoriaController@listar')->name('listar');
});

Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
    Route::get('/', 'RolesController@index')->name('index');
    Route::get('edit/{id}', 'RolesController@edit')->name('edit');
    Route::post('update/{id}', 'RolesController@update')->name('update');
});

Route::group(['prefix' => 'technical', 'as' => 'technical.'], function () {
    Route::get('/', 'TechnicalController@index')->name('index');
    Route::get('all', 'TechnicalController@all')->name('all');
    Route::get('create', 'TechnicalController@create')->name('create');
    Route::post('store', 'TechnicalController@store')->name('store');
    Route::get('edit/{id}', 'TechnicalController@edit')->name('edit');
    Route::post('update/{id}', 'TechnicalController@update')->name('update');
    Route::get('status/{id}', 'TechnicalController@changeStatus')->name('status');
});

Route::group(['prefix' => 'supervisors', 'as' => 'supervisors.'], function () {
    Route::get('/', 'SupervisorsController@index')->name('index');
    Route::get('all', 'SupervisorsController@all')->name('all');
    Route::get('create', 'SupervisorsController@create')->name('create');
    Route::post('store', 'SupervisorsController@store')->name('store');
    Route::get('edit/{id}', 'SupervisorsController@edit')->name('edit');
    Route::post('update/{id}', 'SupervisorsController@update')->name('update');
    Route::get('status/{id}', 'SupervisorsController@changeStatus')->name('status');
    Route::get('pluck', 'SupervisorsController@pluck')->name('pluck');
});

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
    Route::delete('/{id}', 'SellersController@delete')->name('delete');
});

Route::group(['prefix' => 'quotations', 'as' => 'quotations.'], function () {
    Route::get('/', 'QuotationsController@index')->name('index');
    Route::get('matches', 'QuotationsController@matches')->name('matches');
    Route::get('edit/{id}', 'QuotationsController@edit')->name('edit');
    Route::post('update', 'QuotationsController@update')->name('update');
    Route::get('remove/{id}', 'QuotationsController@remove')->name('remove');

    Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
        Route::get('/', 'QuotationCategoriesController@index')->name('index');
        Route::get('create', 'QuotationCategoriesController@create')->name('create');
        Route::post('store', 'QuotationCategoriesController@store')->name('store');
        Route::get('edit/{id}', 'QuotationCategoriesController@edit')->name('edit');
        Route::post('update/{id}', 'QuotationCategoriesController@update')->name('update');
        Route::get('status/{id}', 'QuotationCategoriesController@changeStatus')->name('change.status');
    });
});

Route::group(['prefix' => 'cashier', 'as' => 'cashier.'], function () {
    Route::get('/', 'CashierController@index')->name('index');
    Route::get('report', 'CashierController@report')->name('report');
    Route::post('search', 'CashierController@search')->name('search');
});

Route::group(['prefix' => 'financial', 'as' => 'financial.'], function () {
    Route::get('/', 'FinancialController@index')->name('index');
    Route::get('report', 'FinancialController@report')->name('report');
});

Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
    Route::group(['prefix' => 'analitico', 'as' => 'analitico.'], function () {
        Route::get('/', 'AnaliticoController@index')->name('index');
        Route::get('report', 'AnaliticoController@report')->name('report');
        Route::get('summary/{id}', 'AnaliticoController@summary')->name('summary');
    });
});

Route::group(['prefix' => 'bets', 'as' => 'bets.'], function () {
    Route::get('summary/{id}', 'BetsController@summary')->name('summary');
    Route::post('cancel/{id}', 'BetsController@cancel')->name('cancel');
    Route::get('remove/{id}', 'BetsController@remove')->name('remove');
    Route::post('update/{id}', 'BetsController@update')->name('update');
});

Route::group(['prefix' => 'tips', 'as' => 'tips.'], function () {
    Route::post('cancel/{id}', 'TipsController@cancel')->name('cancel');
    Route::post('update/{id}', 'TipsController@update')->name('update');
});

Route::group(['prefix' => 'cancelar-palpites', 'as' => 'cancelar-palpites.'], function () {
    Route::get('/', 'CancelarPalpitesController@index')->name('index');
    Route::get('buscar', 'CancelarPalpitesController@buscar')->name('buscar');
    Route::get('cancelados', 'CancelarPalpitesController@cancelados')->name('cancelados');
    Route::post('cancelar', 'CancelarPalpitesController@cancelar')->name('cancelar');
});

Route::group(['prefix' => 'results', 'as' => 'results.'], function () {
    Route::get('/', 'ResultsController@index')->name('index');
    Route::get('salvar', 'ResultsController@salvar')->name('salvar');
    Route::post('store', 'ResultsController@store')->name('store');
    Route::get('edit/{id}', 'ResultsController@edit')->name('edit');
    Route::post('update/{id}', 'ResultsController@update')->name('update');
    Route::get('cancel/{id}', 'ResultsController@cancel')->name('cancel');
});

Route::group(['prefix' => 'matches', 'as' => 'matches.'], function () {
    Route::get('/', 'MatchesController@index')->name('index');
    Route::get('edit/{id}', 'MatchesController@edit')->name('edit');
    Route::post('update/{id}', 'MatchesController@update')->name('update');
    Route::get('remove/{id}', 'MatchesController@remove')->name('remove');
    Route::get('all', 'MatchesController@all')->name('all');
    Route::get('status/{id}', 'MatchesController@changeStatus')->name('status');
});

Route::group(['prefix' => 'leagues', 'as' => 'leagues.'], function () {
    Route::get('/', 'LeaguesController@index')->name('index');
    Route::get('all', 'LeaguesController@all')->name('all');
    Route::get('pluck-by-matches', 'LeaguesController@pluckByMatches')->name('pluckByMatches');
    Route::get('edit/{id}', 'LeaguesController@edit')->name('edit');
    Route::post('update/{id}', 'LeaguesController@update')->name('update');
    Route::get('status/{id}', 'LeaguesController@changeStatus')->name('status');
});

Route::group(['prefix' => 'companies', 'as' => 'companies.'], function () {
    Route::get('/', 'CompaniesController@index')->name('index');
    Route::get('create', 'CompaniesController@create')->name('create');
    Route::post('store', 'CompaniesController@store')->name('store');
    Route::get('edit/{id}', 'CompaniesController@edit')->name('edit');
    Route::post('update/{id}', 'CompaniesController@update')->name('update');
    Route::get('supervisors/{id}', 'CompaniesController@supervisors')->name('supervisors');
    Route::get('get-supervisors/{id}', 'CompaniesController@getSupervisors');
    Route::post('save-supervisors/{id}', 'CompaniesController@saveSupervisors');
    Route::get('pluck', 'CompaniesController@pluck')->name('pluck');
    Route::get('quotation-up', 'CompaniesController@quotationsUp')->name('quotation-up');
    Route::get('quotation-down', 'CompaniesController@quotationsDown')->name('quotation-down');
});

Route::group(['prefix' => 'balance', 'as' => 'balance.'], function () {
    Route::get('index', 'BalanceController@index')->name('index');
    Route::get('sellers', 'BalanceController@sellers')->name('sellers');
    Route::post('send', 'BalanceController@send')->name('send');
    Route::post('reset', 'BalanceController@reset')->name('reset');
    Route::post('reset-all', 'BalanceController@resetAll')->name('resetAll');
    Route::post('zero-all', 'BalanceController@zeroToAll')->name('zeroToAll');
});

Route::group(['prefix' => 'expenses', 'as' => 'expenses.'], function () {
    Route::get('index', 'ExpensesController@index')->name('index');
    Route::get('sellers', 'ExpensesController@sellers')->name('sellers');
    Route::post('send', 'ExpensesController@send')->name('send');
});

Route::group(['prefix' => 'payments', 'as' => 'payments.'], function () {
    Route::get('index', 'PaymentsController@index')->name('index');
    Route::get('sellers', 'PaymentsController@sellers')->name('sellers');
    Route::post('send', 'PaymentsController@send')->name('send');
});

Route::group(['prefix' => 'sports', 'as' => 'sports.'], function () {
    Route::get('/', 'SportsController@index')->name('index');
    Route::get('edit/{id}', 'SportsController@edit')->name('edit');
    Route::post('update/{id}', 'SportsController@update')->name('update');
    Route::get('remove/{id}', 'SportsController@remove')->name('remove');
    Route::get('all', 'SportsController@all')->name('all');
    Route::get('status/{id}', 'SportsController@changeStatus')->name('status');
});
