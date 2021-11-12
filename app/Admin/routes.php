<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {
    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('manager/users', UserController::class);
    $router->resource('manager/entries', EntryController::class);
    $router->resource('manager/accounts', AccountController::class);
    $router->resource('manager/information', InformationController::class);
    $router->resource('manager/reservations', ReservationSettingController::class);
});