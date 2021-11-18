<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
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
    return (new App\Http\Controllers\PagesController())->show('home');
//     return view('static.index');
});
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');
// Route::get('/uses', function () {
//     return view('static.uses');
// });
Route::get('/sponsor-us', function () {
    return (new App\Http\Controllers\PagesController())->show('sponsor-us');
});
Route::get('/media-partner-proposal', function () {
    return (new App\Http\Controllers\PagesController())->show('media-partner-proposal');
});
Route::get('/event-template', function () {
    return view('static.event-template');
});
Route::get('/contact', function () {
    return (new App\Http\Controllers\PagesController())->show('contact');
});
Route::get('/twibbon', function () {
    return redirect('/info/twibbon-guidelines');
});
Route::resource('info', 'PagesController');
Route::view('userview', "registration");
Route::post('postcontroller', 'PostController@formSubmit');


// Login / User Dashboard
// Route::resource('/login', 'TicketStatusController');
Route::get('/logout', 'TicketStatusController@logout');

Auth::routes(['verify' => true]);

Route::post('/changeaccountdetails', 'UserSettingsController@updateContacts');

// Get user details (for registration)
Route::post('/getuserdetails', 'UserSettingsController@getUserDetails');

// Handle registration
Route::get('/register/{id}', 'UserSettingsController@registrationRedirectHandler');
Route::post('/registerevent', 'UserSettingsController@registerToEvent');

// Handle payments
Route::get('/pay/{paymentcode}', 'UserSettingsController@paymentIndex');
Route::post('/pay/{paymentcode}', 'UserSettingsController@paymentHandler');

// Handle User download file
Route::get('/user/downloadFile/cp/{teamid}', 'UserSettingsController@downloadFileCompetition');
Route::get('/user/downloadFile/{type}/{paymentcode}/{fileid}', 'UserSettingsController@downloadFileUser');

// Handle competition
Route::get('/cp/{teamid}', 'UserSettingsController@competitionIndex');
Route::post('/cp/{teamid}', 'UserSettingsController@competitionHandler');

// Handle attendance
Route::get('/attendance/{eventId}/{id}', 'NewAttendanceController@index');
Route::post('/attendance/{eventId}/{id}', 'NewAttendanceController@store');

// Handle attendance
//Route::get('/attendance/{eventId}', ['uses' =>'NewAttendanceController@index']);
//Route::post('/attendance/{eventId}', ['uses' =>'NewAttendanceController@store']);
// Route::get('/webinarTest', function () {
//     $payload = [
//         'attendance_id' => "1",
//         'registration_id' => "2",
//         'event_id' => "3",
//         'account_id' => "4",
//         'account_name' => "5",
//         'attendance_type' => "6",
//         'attendance_timestamp' => "7",
//         'event_name' => "Lorem Ipsum",
//         'url_link' => "https://gojek.com"
//     ];
//     return view('static.webinar-end', $payload);
// });

// User Dashboard
Route::get('/profile', function () {
    return redirect('/home');
});

Route::get('/home', 'HomeController@index')->name('dashboard.home');

// Administration Panel
Route::get('/admin', function () {
    return redirect('/home');
});
// Route::get('/admin/{path}', 'AdminController@index');
Route::get('/admin/downloadFile/{type}/{file_id}', 'AdminController@downloadFromFileId');
Route::get('/admin/events', 'AdminController@getEventsList');
Route::get('/admin/event/{event_id}', 'AdminController@getEventParticipants');
Route::post('/admin/event/{event_id}', 'AdminController@postEventParticipants');
Route::get('/admin/users', 'AdminController@getAllUsers');
Route::post('/admin/users', 'AdminController@postAllUsers');
Route::get('/admin/sendemail/{registration_id}', 'AdminController@sendZoomEmail');
