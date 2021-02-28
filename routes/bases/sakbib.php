<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Additional routes for the base "Sakbib"
|--------------------------------------------------------------------------
|
| This is the place to register additional routes for this base beyond the
| standard routes defined by the `mapDynamicRoutes` method in
| `RouteServiceProvider`.
|
| The routes are loaded by `RouteServiceProvider` within a group that
| sets middleware and namespace for this base.
|
| Tip: To list all routes, use the `route:list` artisan command:
|
|     ./dev.sh artisan route:list
*/

Route::get('creator/{id}', 'CreatorController@show');
Route::get('category/{id}', 'CategoryController@show');

Route::middleware('can:sakbib')
    ->group(function () {
        // Place any routes that should only be available to authorized users here
        Route::post('record/{record}/publish', 'Controller@publish');
        Route::post('record/{record}/unpublish', 'Controller@unpublish');

        Route::get('creator/{id}/edit', 'CreatorController@edit');
        Route::put('creator/{id}', 'CreatorController@update');
        Route::get('creator/{id}/delete', 'CreatorController@delete');
        Route::delete('creator/{id}', 'CreatorController@destroy');

        Route::get('category/{id}/edit', 'CategoryController@edit');
        Route::put('category/{id}', 'CategoryController@update');
        Route::get('category/{id}/delete', 'CategoryController@delete');
        Route::delete('category/{id}', 'CategoryController@destroy');

    });
