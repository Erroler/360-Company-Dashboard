<?php

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

Route::middleware('guest')->group(function() {
    Route::get('/', 'AuthController@login')->name('login');
    Route::post('/login', 'AuthController@authenticate')->name('login.submit');
});

Route::middleware('auth')->group(function() {
    // Overview route.
    Route::get('/overview/{period?}', 'OverviewController@index')->where('period', '[0-4]')->name('overview');

    // Finances section routes.
    Route::prefix('finances')->name('finances.')->group(function() {
        Route::get('/{period?}', 'FinancesController@index')->where('period', '[0-4]')->name('index');
    });

    // Sales section routes.
    Route::prefix('sales')->name('sales.')->group(function() {
        Route::get('/{period?}', 'SalesController@index')->where('period', '[0-4]')->name('index');
        Route::get('/search', 'SalesController@search')->name('search');
        Route::get('/search/api', 'SearchController@sales');
    });
    
    // Sales section routes.
    Route::prefix('purchases')->name('purchases.')->group(function() {
        Route::get('/{period?}', 'PurchasesController@index')->where('period', '[0-4]')->name('index');
        Route::get('/search', 'PurchasesController@search')->name('search');
        Route::get('/search/api', 'SearchController@purchases');
    });
    
    // Inventory section routes.
    Route::prefix('inventory')->name('inventory.')->group(function() {
        Route::get('/{period?}', 'InventoryController@index')->where('period', '[0-4]')->name('index');
        Route::get('/search', 'InventoryController@search')->name('search');
        Route::get('/search/api', 'SearchController@inventory');
        Route::get('/product/{itemKey}', 'InventoryController@product')->name('product');
    });

    Route::prefix('entity')->name('entity.')->group(function() {
        Route::get('/consumer/{nif}', 'EntitiesController@consumer')->name('consumer');
        Route::get('/supplier/{nif}', 'EntitiesController@supplier')->name('supplier');
    });

    

    // // Clients section routes.
    // Route::prefix('clients')->name('clients.')->group(function() {
    //     Route::get('/', 'ClientsController@index')->name('index');
    // });

    // About company section routes.
    Route::prefix('about')->name('about.')->group(function() {
        Route::get('/', 'AboutController@index')->name('index');
        Route::get('/trial_balance_sheet', 'AboutController@trialBalanceSheet')->name('trial_balance_sheet');
        Route::get('/profit_loss_statement', 'AboutController@profitLossStatement')->name('profit_loss_statement');
        Route::get('/balance_sheet', 'AboutController@balanceSheet')->name('balance_sheet');
    });

    // Logout route.
    Route::get('/logout', 'AuthController@logout')->name('logout');

    Route::get('/cc', function () {
        \Cache::forget('saft_data');
        \Cache::forget('inventory');
        \Cache::forget('clients');
        \Cache::forget('suppliers');
        \Cache::forget('sales');
        \Cache::forget('purchases');
        \Cache::forget('assortments');
        \Cache::forget('dt_inventory');
        // ola
    })->name('cc');

});
   