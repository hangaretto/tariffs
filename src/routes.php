<?php

Route::group(['prefix' => 'api/v1/magnetar/tariffs'], function () {

    Route::post('/callbacks/paymentAviso', 'Magnetar\Tariffs\Controllers\CallbackController@paymentAviso');
    Route::post('/callbacks/checkOrder', 'Magnetar\Tariffs\Controllers\CallbackController@checkOrder');

    Route::get('/users/{id}/pay', function ($id) {
        return view('magnetar_tariffs::yandex', ['id' => $id]);
    });

});

Route::group(['prefix' => 'api/v1/magnetar/tariffs', 'middleware' => [config('tariffs.middleware.auth')]], function () {

//    Route::get('users/{id}', 'Magnetar\Tariffs\Controllers\PaymentController@allInfo');
    Route::get('/', 'Magnetar\Tariffs\Controllers\PaymentController@allInfo');

    Route::group(['prefix' => 'object_types'], function () {

        Route::group(['middleware' => [config('tariffs.middleware.super_admin')]], function () {

            Route::post('/', 'Magnetar\Tariffs\Controllers\ObjectTypeCrudController@process');
            Route::put('/{id}', 'Magnetar\Tariffs\Controllers\ObjectTypeCrudController@process')->where('id', '[0-9]+');
            Route::delete('/{id}', 'Magnetar\Tariffs\Controllers\ObjectTypeCrudController@destroy')->where('id', '[0-9]+');

        });

        Route::get('/', 'Magnetar\Tariffs\Controllers\ObjectTypeCrudController@index');
        Route::get('/{id}', 'Magnetar\Tariffs\Controllers\ObjectTypeCrudController@show')->where('id', '[0-9]+');

    });

    Route::group(['prefix' => 'cards'], function () {

        Route::group(['middleware' => [config('tariffs.middleware.super_admin')]], function () {

            Route::post('/', 'Magnetar\Tariffs\Controllers\CardCrudController@process');
            Route::put('/{id}', 'Magnetar\Tariffs\Controllers\CardCrudController@process')->where('id', '[0-9]+');
            Route::delete('/{id}', 'Magnetar\Tariffs\Controllers\CardCrudController@destroy')->where('id', '[0-9]+');

        });

        Route::get('/', 'Magnetar\Tariffs\Controllers\CardCrudController@index');
        Route::get('/{id}', 'Magnetar\Tariffs\Controllers\CardCrudController@show')->where('id', '[0-9]+');

    });

    Route::group(['prefix' => 'modules'], function () {

        Route::group(['middleware' => [config('tariffs.middleware.super_admin')]], function () {

            Route::post('/', 'Magnetar\Tariffs\Controllers\ModuleCrudController@process');
            Route::put('/{id}', 'Magnetar\Tariffs\Controllers\ModuleCrudController@process')->where('id', '[0-9]+');
            Route::delete('/{id}', 'Magnetar\Tariffs\Controllers\ModuleCrudController@destroy')->where('id', '[0-9]+');

        });

        Route::get('/', 'Magnetar\Tariffs\Controllers\ModuleCrudController@index');
        Route::get('/{id}', 'Magnetar\Tariffs\Controllers\ModuleCrudController@show')->where('id', '[0-9]+');

        Route::put('/{id}/buy', 'Magnetar\Tariffs\Controllers\PaymentController@buyModule')->where('id', '[0-9]+');

    });

    Route::group(['prefix' => '{type}'], function () {

        Route::group(['middleware' => [config('tariffs.middleware.super_admin')]], function () {

            Route::post('/', 'Magnetar\Tariffs\Controllers\ObjectCrudController@process');
            Route::put('/{id}', 'Magnetar\Tariffs\Controllers\ObjectCrudController@process')->where('id', '[0-9]+');
            Route::delete('/{id}', 'Magnetar\Tariffs\Controllers\ObjectCrudController@destroy')->where('id', '[0-9]+');

        });

        Route::get('/', 'Magnetar\Tariffs\Controllers\ObjectCrudController@index');
        Route::get('/{id}', 'Magnetar\Tariffs\Controllers\ObjectCrudController@show')->where('id', '[0-9]+');
        Route::get('/{id}/test', 'Magnetar\Tariffs\Controllers\PaymentController@decreaseTest')->where('id', '[0-9]+'); // test

        Route::put('/{id}/buy', 'Magnetar\Tariffs\Controllers\PaymentController@buyObject')->where('id', '[0-9]+');

    });

});
