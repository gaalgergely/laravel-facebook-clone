<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->group(function () {

   Route::apiResources([
       'posts' => 'PostController',
       'users' => 'UserController',
   ]);

});
