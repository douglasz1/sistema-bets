<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return redirect()->route('web.index');
});

Route::get('download', 'HomeController@download')->name('download');
Route::get('regras', 'Web\HomeController@rules')->name('regras');

Auth::routes();
Route::get('logout', ['uses' => 'Auth\LoginController@logout']);

Route::post('configs/simulator', 'ConfigsController@simulator');

Route::group(['prefix' => 'web', 'as' => 'web.', 'namespace' => 'Web'], function () {
    Route::get('/', 'HomeController@index')->name('index');

    Route::group(['prefix' => 'leagues', 'as' => 'leagues.'], function () {
        Route::post('/', 'LeaguesController@leaguesHaveMatches')->name('leaguesHaveMatches');
        Route::post('matches', 'LeaguesController@leaguesWithMatches')->name('leaguesWithMatches');
        Route::post('search', 'LeaguesController@search')->name('search');
    });

    Route::group(['prefix' => 'quotations', 'as' => 'quotations.'], function () {
        Route::post('match/{id}', 'QuotationsController@quotations')->name('match');
    });

    Route::group(['prefix' => 'bets', 'as' => 'bets.'], function () {
        Route::post('store', 'BetsController@store')->name('store');
        Route::get('printing/{id}', 'BetsController@printing')->name('printing');
        Route::get('pdf/{id}', 'BetsController@pdf')->name('pdf');
    });
});

Route::group(['middleware' => ['auth', 'actives']], function () {
    Route::get('home', 'HomeController@index')->name('index');
    Route::get('rules', 'HomeController@rules')->name('rules');
    // Route::get('download', 'HomeController@download')->name('download');
    Route::get('balance', 'HomeController@balance')->name('balance');
    Route::get('results', 'ResultsController@index')->name('results');

    Route::get('alterar-senha', 'HomeController@alterarSenha')->name('alterarSenha');
    Route::post('atualizar-senha', 'HomeController@atualizarSenha')->name('atualizarSenha');

    Route::group(['prefix' => 'leagues', 'as' => 'leagues.'], function () {
        Route::get('/', 'LeaguesController@leaguesHaveMatches')->name('leaguesHaveMatches');
        Route::get('matches', 'LeaguesController@leaguesWithMatches')->name('leaguesWithMatches');
        Route::post('search', 'LeaguesController@search')->name('search');
        Route::get('toprint', 'LeaguesController@printQuotations')->name('toprint');
        Route::post('toprint', 'LeaguesController@printQuotationsPost');
    });

    Route::group(['prefix' => 'matches', 'as' => 'matches.'], function () {
        Route::post('/{id}/quotations/json', 'MatchesController@quotationsToJson')->name('quotationsToJson');
    });

    Route::group(['prefix' => 'bets', 'as' => 'bets.'], function () {
        Route::post('store', 'BetsController@store')->name('store');
        Route::get('printing/{id}', 'BetsController@printing')->name('printing');
        Route::get('validate', 'BetsController@validateBet')->name('validate');
        Route::post('search', 'BetsController@searchCode')->name('search.code');
        Route::post('validate', 'BetsController@validateSave')->name('validate.save');
        Route::get('reprints', 'BetsController@reprints')->name('reprints');
        Route::post('reprints', 'BetsController@getReprints')->name('reprints.post');
        Route::put('cancel/{id}', 'BetsController@cancel')->name('cancel');
    });

    Route::group(['prefix' => 'live', 'as' => 'live.'], function () {
        Route::get('/', 'LiveBetsController@index')->name('index');
        Route::get('matches', 'LiveBetsController@matches')->name('matches');
        Route::get('quotations/{id}', 'LiveBetsController@quotations')->name('quotations');
        Route::post('store', 'LiveBetsController@store')->name('store');
    });

    Route::group(['prefix' => 'bolao', 'as' => 'bolao.', 'namespace' => 'API\V2\Bolao'], function () {
        Route::get('/', 'SimuladorController@index')->name('index');
        Route::get('jogar/{id}', 'SimuladorController@jogar')->name('jogar');
        Route::post('salvar', 'SimuladorController@salvar')->name('salvar');
        Route::get('bilhete/{id}', 'SimuladorController@bilhete')->name('bilhete');
        Route::get('segundaVia', 'SimuladorController@segundaVia')->name('segundaVia');
        Route::post('segundaVia', 'SimuladorController@buscarSegundaVia')->name('segundaVia.post');
        Route::put('cancelar/{id}', 'SimuladorController@cancelar')->name('cancelar');
        Route::get('resultado/{id}', 'SimuladorController@resultado')->name('resultado');
    });

    Route::group(['prefix' => 'admin/bolao', 'as' => 'admin.bolao.', 'namespace' => 'API\V2\Bolao', 'middleware' => ['auth', 'check-role:admin']], function () {
        Route::get('/', 'HomeController@index')->name('index');
        Route::get('criar', 'HomeController@criar')->name('criar');
        Route::post('salvar', 'HomeController@salvar')->name('salvar');
        Route::get('detalhes/{id}', 'HomeController@detalhes')->name('detalhes');
        Route::get('apostas/{id}', 'HomeController@apostas')->name('apostas');
        Route::get('placares/{id}', 'HomeController@placares')->name('placares');
        Route::post('placares/{id}', 'HomeController@salvarPlacares')->name('placares.salvar');
        Route::get('vencedor/{id}', 'HomeController@vencedor')->name('vencedor');

        Route::group(['prefix' => 'acompanhamento', 'as' => 'acompanhamento.'], function () {
            Route::get('/', 'AcompanhamentoController@index')->name('index');
            Route::get('relatorio', 'AcompanhamentoController@relatorio')->name('relatorio');
            Route::post('busca', 'AcompanhamentoController@busca')->name('busca');
            Route::put('cancelar/{id}', 'AcompanhamentoController@cancelar')->name('cancelar');
        });
    });

});

Route::group(['middleware' => ['auth', 'check-role:technical'], 'namespace' => 'Technical', 'prefix' => 'technical', 'as' => 'technical.'], function () {

    Route::group(['prefix' => 'companies', 'as' => 'companies.'], function () {
        Route::get('/', 'CompaniesController@index')->name('index');
        Route::get('pluck', 'CompaniesController@pluck')->name('pluck');
        Route::get('quotation-up', 'CompaniesController@quotationsUp')->name('quotation-up');
        Route::get('quotation-down', 'CompaniesController@quotationsDown')->name('quotation-down');
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

    Route::group(['prefix' => 'bets', 'as' => 'bets.'], function () {
        Route::get('summary/{id}', 'BetsController@summary')->name('summary');
        Route::get('cancel/{id}', 'BetsController@cancel')->name('cancel');
    });

    Route::group(['prefix' => 'tips', 'as' => 'tips.'], function () {
        Route::get('cancel/{id}', 'TipsController@cancel')->name('cancel');
    });

    Route::group(['prefix' => 'results', 'as' => 'results.'], function () {
        Route::get('/', 'ResultsController@index')->name('index');
        Route::post('store', 'ResultsController@store')->name('store');
        Route::get('edit/{id}', 'ResultsController@edit')->name('edit');
        Route::post('update/{id}', 'ResultsController@update')->name('update');
        Route::get('cancel/{id}', 'ResultsController@cancel')->name('cancel');
    });

    Route::group(['prefix' => 'matches', 'as' => 'matches.'], function () {
        Route::get('/', 'MatchesController@index')->name('index');
        Route::get('edit/{id}', 'MatchesController@edit')->name('edit');
        Route::post('update/{id}', 'MatchesController@update')->name('update');
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

    Route::group(['prefix' => 'bolao', 'as' => 'bolao.'], function () {
        Route::get('/', 'BolaoController@index')->name('index');
        Route::get('criar', 'BolaoController@criar')->name('criar');
        Route::post('salvar', 'BolaoController@salvar')->name('salvar');
        Route::get('detalhes/{id}', 'BolaoController@detalhes')->name('detalhes');
        Route::get('apostas/{id}', 'BolaoController@apostas')->name('apostas');
        Route::get('placares/{id}', 'BolaoController@placares')->name('placares');
        Route::post('placares/{id}', 'BolaoController@salvarPlacares')->name('placares.salvar');
        Route::get('vencedor/{id}', 'BolaoController@vencedor')->name('vencedor');

        Route::group(['prefix' => 'acompanhamento', 'as' => 'acompanhamento.'], function () {
            Route::get('/', 'AcompanhamentoBolaoController@index')->name('index');
            Route::get('relatorio', 'AcompanhamentoBolaoController@relatorio')->name('relatorio');
            Route::post('busca', 'AcompanhamentoBolaoController@busca')->name('busca');
            Route::put('cancelar/{id}', 'AcompanhamentoBolaoController@cancelar')->name('cancelar');
        });
    });
});

Route::get('pdf/{id}', 'Web\BetsController@gerarPDF');
Route::get('imagem/{id}', 'Web\BetsController@gerarImagem');
