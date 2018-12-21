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

Route::get('/', function () {
    return view('auth/login');
});



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('account', 'AccountController');
Route::resource('template', 'TemplateController');
Route::resource('asin', 'AsinController');
Route::resource('user', 'UserController');
Route::get('/total', 'UserController@total')->name('total');
Route::get('/etotal', 'UserController@etotal')->name('etotal');
Route::get('/tran', 'TranController@index')->name('tran');
Route::get('/price', 'PriceController@index')->name('price');
Route::Post('/price/get', 'PriceController@get')->name('getPrice');
Route::Post('/price/getStockAge', 'PriceController@getStockAge')->name('getStockAge');
Route::resource('rule', 'RuleController');
Route::resource('auto', 'AutoController');
Route::get('/profile', 'UserController@profile')->name('profile');
Route::Post('/profile', 'UserController@profile')->name('profileUpdate');
Route::resource('inbox', 'InboxController');
Route::get('/inbox/filter/{type}', 'InboxController@index')->name('filterInbox');
Route::Post('/inbox/get', 'InboxController@get')->name('getInbox');
Route::Post('/asin/get', 'AsinController@get')->name('getAsin');
Route::Post('/inbox/change', 'InboxController@change')->name('changeInbox');
Route::resource('send', 'SendController');
Route::Post('/send/get', 'SendController@get')->name('getSendbox');
Route::get('/send/deletefile/{filename}', 'SendController@deletefile')->name('deleteFile');
Route::resource('review', 'ReviewController');
Route::resource('star', 'StarController');
Route::resource('phone', 'PhoneController');
Route::Post('/phone/get', 'PhoneController@get')->name('getPhone');
Route::Post('/star/get', 'StarController@get')->name('getStar');
Route::Post('/review/get', 'ReviewController@get')->name('getReview');
Route::Post('/review/upload', 'ReviewController@upload')->name('uploadReview');
Route::get('/template/ajax/get', 'TemplateController@get')->name('getTemplate');
Route::Post('/saporder/get', 'InboxController@getrfcorder')->name('getRfcOrder');
Route::get('/invoice/get/{id}', 'InboxController@getpdfinvoice')->name('getPdfInvoice');
Route::resource('qa', 'QaiController');
Route::Post('/qa/get', 'QaiController@get')->name('getQa');
Route::get('/laravel-u-editor-server/server', 'CustomuploadController@server')->name('upload');
Route::resource('question', 'QaController');
Route::resource('group', 'GroupController');
Route::resource('sellertab', 'SellertabController');
Route::resource('rs', 'RsController');
Route::resource('seller', 'SellerController');
Route::get('/seller/{asin}/{marketplaceid}', 'SellerController@show')->name('viewAsin');
Route::Post('/ratingdetails', 'SellerController@getrating')->name('getRating');
Route::resource('exception', 'ExceptionController');
Route::Post('/exception/get', 'ExceptionController@get')->name('getException');
Route::Post('/exception/getorder', 'ExceptionController@getrfcorder')->name('getExceptionOrder');
Route::Post('/exception/getrepeatorder', 'ExceptionController@getRepeatOrder')->name('getRepeatOrder');
Route::get('/exceptionexport', 'ExceptionController@export')->name('exportException');
Route::get('/reviewexport', 'ReviewController@export')->name('exportReview');
Route::get('/asinexport', 'AsinController@export')->name('exportAsin');
Route::get('/fees', 'FeesController@index')->name('fees');
Route::Post('/fees/getads', 'FeesController@getads')->name('getads');
Route::Post('/fees/getcpc', 'FeesController@getcpc')->name('getcpc');
Route::Post('/fees/getdeal', 'FeesController@getdeal')->name('getdeal');
Route::Post('/fees/getcoupon', 'FeesController@getcoupon')->name('getcoupon');
Route::Post('/fees/getservice', 'FeesController@getservice')->name('getservice');
Route::resource('mcforder', 'McforderController');
Route::resource('autoprice', 'AutopriceController');
Route::Post('/mcforder/get', 'McforderController@get')->name('getMcforder');
Route::resource('rsgproducts', 'RsgproductsController');
Route::Post('/rsgproducts/get', 'RsgproductsController@get')->name('getrsgproducts');
Route::resource('rsgrequests', 'RsgrequestsController');
Route::Post('/rsgrequests/get', 'RsgrequestsController@get')->name('getrsgrequests');