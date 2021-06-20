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