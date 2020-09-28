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

Route::get('/', function () {
    return view('index');
});
Route::get('/uses', function () {
    return view('uses');
});
Route::get('/sponsor-us', function () {
    return view('sponsor-us');
});
Route::get('/event-template', function () {
    return view('event-template');
});

/* Business-IT Competitions */
Route::get('/bcase', function () {
    return view('bcase');
});
Route::get('/moapps', function () {
    return view('moapps');
});

/* Mini E-Sports Competitions */
Route::get('/ml', function () {
    return view('ml');
});
Route::get('/pubg', function () {
    return view('pubg');
});
Route::get('/valorant', function () {
    return view('valorant');
});

/* Webinars */
Route::get('/webinar-bchain', function () {
    return view('webinar-bchain');
});
Route::get('/webinar-covid', function () {
    return view('webinar-covid');
});
Route::get('/webinar-digital', function () {
    return view('webinar-digital');
});
Route::get('/webinar-mobile', function () {
    return view('webinar-mobile');
});

Route::get('/regist-new', function () {
    return view('registration.main');
});

Route::get('/contact', function () {
    return view('contact');
});
Route::get('/regist-webinar', function () {
    return view('regist-webinar');
});
Route::get('/regist-competition', function () {
    return view('regist-competition');
});
Route::view('userview', "registration");
Route::post('postcontroller', 'PostController@formSubmit');


// Login / User Dashboard
Route::resource('/login', 'TicketStatusController');
Route::get('/logout', 'TicketStatusController@logout');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('dashboard.home');
