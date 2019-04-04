<?php

use Illuminate\Support\Str;
use hlaCk\ezCP\Events\Routing;
use hlaCk\ezCP\Events\RoutingAdmin;
use hlaCk\ezCP\Events\RoutingAdminAfter;
use hlaCk\ezCP\Events\RoutingAfter;
use hlaCk\ezCP\Facades\ezCP;

/*
|--------------------------------------------------------------------------
| ezCP Routes
|--------------------------------------------------------------------------
|
| This file is where you may override any of the routes that are included
| with ezCP.
|
*/

Route::group(['as' => 'ezcp.'], function () {
    event(new Routing());

    $namespacePrefix = '\\'.config('ezcp.controllers.namespace').'\\';

    Route::get('login', ['uses' => $namespacePrefix.'ezCPAuthController@login',     'as' => 'login']);
    Route::post('login', ['uses' => $namespacePrefix.'ezCPAuthController@postLogin', 'as' => 'postlogin']);

    Route::group(['middleware' => 'admin.user'], function () use ($namespacePrefix) {
        event(new RoutingAdmin());

        // Main Admin and Logout Route
        Route::get('/', ['uses' => $namespacePrefix.'ezCPController@index',   'as' => 'dashboard']);
        Route::post('logout', ['uses' => $namespacePrefix.'ezCPController@logout',  'as' => 'logout']);
        Route::post('upload', ['uses' => $namespacePrefix.'ezCPController@upload',  'as' => 'upload']);

        Route::get('profile', ['uses' => $namespacePrefix.'ezCPUserController@profile', 'as' => 'profile']);

        try {
            foreach (ezCP::model('DataType')::all() as $dataType) {
                $breadController = $dataType->controller
                                 ? Str::start($dataType->controller, '\\')
                                 : $namespacePrefix.'ezCPBaseController';

                Route::get($dataType->slug.'/order', $breadController.'@order')->name($dataType->slug.'.order');
                Route::post($dataType->slug.'/action', $breadController.'@action')->name($dataType->slug.'.action');
                Route::post($dataType->slug.'/order', $breadController.'@update_order')->name($dataType->slug.'.order');
                Route::get($dataType->slug.'/{id}/restore', $breadController.'@restore')->name($dataType->slug.'.restore');
                Route::get($dataType->slug.'/relation', $breadController.'@relation')->name($dataType->slug.'.relation');
                Route::resource($dataType->slug, $breadController);
            }
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException("Custom routes hasn't been configured because: ".$e->getMessage(), 1);
        } catch (\Exception $e) {
            // do nothing, might just be because table not yet migrated.
        }

        // Role Routes
        Route::resource('roles', $namespacePrefix.'ezCPRoleController');

        // Menu Routes
        Route::group([
            'as'     => 'menus.',
            'prefix' => 'menus/{menu}',
        ], function () use ($namespacePrefix) {
            Route::get('builder', ['uses' => $namespacePrefix.'ezCPMenuController@builder',    'as' => 'builder']);
            Route::post('order', ['uses' => $namespacePrefix.'ezCPMenuController@order_item', 'as' => 'order']);

            Route::group([
                'as'     => 'item.',
                'prefix' => 'item',
            ], function () use ($namespacePrefix) {
                Route::delete('{id}', ['uses' => $namespacePrefix.'ezCPMenuController@delete_menu', 'as' => 'destroy']);
                Route::post('/', ['uses' => $namespacePrefix.'ezCPMenuController@add_item',    'as' => 'add']);
                Route::put('/', ['uses' => $namespacePrefix.'ezCPMenuController@update_item', 'as' => 'update']);
            });
        });

        // Settings
        Route::group([
            'as'     => 'settings.',
            'prefix' => 'settings',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'ezCPSettingsController@index',        'as' => 'index']);
            Route::post('/', ['uses' => $namespacePrefix.'ezCPSettingsController@store',        'as' => 'store']);
            Route::put('/', ['uses' => $namespacePrefix.'ezCPSettingsController@update',       'as' => 'update']);
            Route::delete('{id}', ['uses' => $namespacePrefix.'ezCPSettingsController@delete',       'as' => 'delete']);
            Route::get('{id}/move_up', ['uses' => $namespacePrefix.'ezCPSettingsController@move_up',      'as' => 'move_up']);
            Route::get('{id}/move_down', ['uses' => $namespacePrefix.'ezCPSettingsController@move_down',    'as' => 'move_down']);
            Route::put('{id}/delete_value', ['uses' => $namespacePrefix.'ezCPSettingsController@delete_value', 'as' => 'delete_value']);
        });

        // Admin Media
        Route::group([
            'as'     => 'media.',
            'prefix' => 'media',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'ezCPMediaController@index',              'as' => 'index']);
            Route::post('files', ['uses' => $namespacePrefix.'ezCPMediaController@files',              'as' => 'files']);
            Route::post('new_folder', ['uses' => $namespacePrefix.'ezCPMediaController@new_folder',         'as' => 'new_folder']);
            Route::post('delete_file_folder', ['uses' => $namespacePrefix.'ezCPMediaController@delete', 'as' => 'delete']);
            Route::post('move_file', ['uses' => $namespacePrefix.'ezCPMediaController@move',          'as' => 'move']);
            Route::post('rename_file', ['uses' => $namespacePrefix.'ezCPMediaController@rename',        'as' => 'rename']);
            Route::post('upload', ['uses' => $namespacePrefix.'ezCPMediaController@upload',             'as' => 'upload']);
            Route::post('remove', ['uses' => $namespacePrefix.'ezCPMediaController@remove',             'as' => 'remove']);
            Route::post('crop', ['uses' => $namespacePrefix.'ezCPMediaController@crop',             'as' => 'crop']);
        });

        // BREAD Routes
        Route::group([
            'as'     => 'bread.',
            'prefix' => 'bread',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'ezCPBreadController@index',              'as' => 'index']);
            Route::get('{table}/create', ['uses' => $namespacePrefix.'ezCPBreadController@create',     'as' => 'create']);
            Route::post('/', ['uses' => $namespacePrefix.'ezCPBreadController@store',   'as' => 'store']);
            Route::get('{table}/edit', ['uses' => $namespacePrefix.'ezCPBreadController@edit', 'as' => 'edit']);
            Route::put('{id}', ['uses' => $namespacePrefix.'ezCPBreadController@update',  'as' => 'update']);
            Route::delete('{id}', ['uses' => $namespacePrefix.'ezCPBreadController@destroy',  'as' => 'delete']);
            Route::post('relationship', ['uses' => $namespacePrefix.'ezCPBreadController@addRelationship',  'as' => 'relationship']);
            Route::get('delete_relationship/{id}', ['uses' => $namespacePrefix.'ezCPBreadController@deleteRelationship',  'as' => 'delete_relationship']);
        });

        // Database Routes
        Route::resource('database', $namespacePrefix.'ezCPDatabaseController');

        // Compass Routes
        Route::group([
            'as'     => 'compass.',
            'prefix' => 'compass',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'ezCPCompassController@index',  'as' => 'index']);
            Route::post('/', ['uses' => $namespacePrefix.'ezCPCompassController@index',  'as' => 'post']);
        });

        event(new RoutingAdminAfter());
    });

    //Asset Routes
    Route::get('assets', ['uses' => $namespacePrefix.'ezCPController@assets', 'as' => 'assets']);

    event(new RoutingAfter());
});
