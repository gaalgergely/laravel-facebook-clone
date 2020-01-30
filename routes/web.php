<?php

Auth::routes();

Route::get('{any}', 'AppController@index')
    ->where('any', '.*')
    ->middleware('auth')
    ->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
