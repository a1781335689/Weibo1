<?php
// 页面布局
Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');
// 用户信息
Route::get('signup', 'UsersController@create')->name('signup');
Route::resource('users', 'UsersController');
// 会话（登录登出）功能
Route::get('login', 'SessionsController@create')->name('login');
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsController@destroy')->name('logout');
// 邮件功能
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

// 忘记密码
Route::get('password/reset',  'PasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email',  'PasswordController@sendResetLinkEmail')->name('password.email');

Route::get('password/reset/{token}',  'PasswordController@showResetForm')->name('password.reset');
Route::post('password/reset',  'PasswordController@reset')->name('password.update');

Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);

// 粉丝关系
Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');

Route::post('/users/followers/{user}', 'FollowersController@store')->name('followers.store');
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');