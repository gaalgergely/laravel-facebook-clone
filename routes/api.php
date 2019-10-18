<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->group(function () {

   Route::get('/user', function (Request $request) {
       return $request->user();
   });

   Route::get('/posts', 'PostController@index');
   Route::post('/posts', 'PostController@store');

});
