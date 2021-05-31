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

// Route::get('/', 'UserAuthController@userlogin');
// Route::get('/userLoginSuccess', 'UserAuthController@userloginSuccess');
// Route::get('/userLogout', 'UserAuthController@userLogout');
// Route::get('/userRegister/{user?}', 'UserAuthController@userlogin');
// Route::post('/userRegister', 'UserAuthController@userRegister');
// Route::post('/userLogin', 'UserAuthController@userInputLogin');
// Route::post('/fbLogin', 'UserAuthController@fbLogin');

//L5 架構 Route
Route::get('/', 'FbDatasController@userlogin');
Route::get('/userLoginSuccess', 'FbDatasController@userloginSuccess');
Route::get('/userLogout', 'FbDatasController@userLogout');
Route::get('/userRegister/{user?}', 'FbDatasController@userlogin');
Route::post('/userRegister', 'FbDatasController@userRegister');
Route::post('/userLogin', 'FbDatasController@userInputLogin');
Route::post('/fbLogin', 'FbDatasController@fbLogin');

//使用者
Route::group(['prefix' => 'user'],function(){
    //使用者驗證
    Route::group(['prefix' => 'auth'], function(){
        //Facebook登入         
        Route::get('/facebook-sign-in', 'UserAuthController@facebookSignInProcess');
        //Facebook登入重新導向授權資料處理 
        Route::get('/facebook-sign-in-callback', 'UserAuthController@facebookSignInCallbackProcess');
    });
}); 
