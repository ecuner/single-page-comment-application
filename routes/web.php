<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'App\Http\Controllers\CommentController@index');
Route::get('/comments', 'App\Http\Controllers\CommentController@getJson')->name('get_comments_json');
Route::post('/store', 'App\Http\Controllers\CommentController@store')->name('post_comment');
