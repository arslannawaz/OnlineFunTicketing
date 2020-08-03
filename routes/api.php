<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


//auth
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('user-login', 'AuthController@login');
    Route::post('user-register', 'AuthController@register');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('getuser', 'AuthController@user');
    });
});

//admin routes
Route::group([
    'middleware' => ['auth:api','admin:api']
], function() {
    //events
    Route::get('/pendingevents','EventController@pendingEvents');
    Route::put('/approveevents/{id}','EventController@approveEvents');
    Route::get('/approvedevents','EventController@approvedEvents');
    Route::resource('/events','EventController');
    //event categories
    Route::resource('/eventcategories','EventCategoryController');
    //event screentype
    Route::resource('/screentypes','ScreenTypeController');
    //role
    Route::resource('/role','RoleController');
    //commercial user
    Route::resource('/commercialuser','CommercialUserController');
    //partner with us
    Route::get('/getpartnerwithus','PartnerWithUsController@index');
    Route::get('/findpartnerwithus/{id}','PartnerWithUsController@show');
    //reviews
    Route::get('/pendingreviews','UserReviewController@pendingReviews');
    Route::put('/approvereview/{id}','UserReviewController@approveReview');
    Route::delete('/deletereview/{id}','UserReviewController@deleteByAdmin');
    //ticketbooking
    Route::resource('/ticketbooking','TicketBookingController');
    //refreshment
    Route::get('/pendingrefreshments','RefreshmentController@pendingRefreshment');
    Route::put('/approverefreshments/{id}','RefreshmentController@approveRefreshment');
    Route::delete('/deleterefreshment/{id}','RefreshmentController@deleteRefreshment');
    Route::get('getallrefreshments','RefreshmentController@getAllRefreshments');
    Route::get('/findrefreshment/{id}','RefreshmentController@findRefreshmentById');
    //playmovieonrequest
    Route::get('getallrequest','PlayOnRequestController@getAllRequest');
    //Reporting
    Route::post('getreportbyevent','ReportingController@reportByEvent');
    Route::post('getreportbyeventcat','ReportingController@reportByEventCat');
    Route::post('getreportbyeventanddate','ReportingController@reportByEventAndDate');
    Route::post('getreportbyalleventanddate','ReportingController@reportByAllEventAndDate');
    Route::post('getreportbyeventandmonth','ReportingController@reportByEventAndMonth');
    Route::post('getreportbyalleventandmonth','ReportingController@reportByAllEventAndMonth');

});

//commercial user routes
Route::group([
    'middleware' => ['auth:api','commercialuser:api']
], function() {
    //commerical event
    Route::resource('/commercialevents','CommercialEventController');
    Route::get('/mypendingevents','CommercialEventController@myPendingEvents');
    Route::get('/myapprovedevents','CommercialEventController@myApprovedEvents');
    //event screentype
    Route::get('/getscreentypes','ScreenTypeController@getScreenType');
    //discount and deals
    Route::resource('/discount','DiscountController');
    //ticketbooking
    Route::post('getbookingbyevent','TicketBookingController@getBookingByEvent');
    Route::delete('deletebooking/{id}','TicketBookingController@deleteBooking');
    Route::get('findcommercialbooking/{id}','TicketBookingController@findCommercialBooking');
    Route::post('confirmticket','TicketBookingController@confirmTicket');
    //refreshment
    Route::resource('/refreshments','RefreshmentController');
    //reporting
    Route::post('getmyreportbyevent','ReportingController@myReportByEvent');
    Route::post('getmyreportbyeventcat','ReportingController@myReportByEventCat');
    Route::post('getmyreportbyeventdate','ReportingController@myReportByEventDate');
    Route::post('getmyreportbyalleventdate','ReportingController@myReportByAllEventDate');
    Route::post('getmyreportbyeventmonth','ReportingController@myReportByEventMonth');
    Route::post('getmyreportbyalleventmonth','ReportingController@myReportByAllEventMonth');

});

//user routes
Route::group([
    'middleware' => ['auth:api','user:api']
], function() {
    //ticketbooking
    Route::post('makebooking','TicketBookingController@makeBooking');
    Route::get('mybooking','TicketBookingController@myBooking');
    Route::post('getbookedseats','TicketBookingController@getBookedSeats');
    Route::get('finduserbooking/{id}','TicketBookingController@findMyBooking');
    //review
    Route::resource('userreview','UserReviewController');
    //refreshments
    Route::get('/getrefreshmentbycommercialuser/{id}','RefreshmentController@getAllRefereshmentsByCommercialUser');
    //playonrequest
    Route::post('submitplayonrequest','PlayOnRequestController@submitRequest');
    Route::get('getmyrequest','PlayOnRequestController@getMyRequest');
    //Ewallet
    Route::get('viewmyewallet','EwalletController@viewMyEwallet');


});


//public routes

//submit partner with us form
Route::post('/partnerwithus','PartnerWithUsController@submitForm');
//select event
Route::post('/selectevents/','EventController@selectEvent');
//event by category
Route::post('/geteventbycategory/','EventController@getEventByCategory');
//location by category
Route::post('/getlocationbycategory/','EventController@getAllLocations');
//event detail
Route::get('/getpubliceventbyid/{id}','EventController@getPublicEventByID');
//all event category
Route::get('/getpubliceventcategories','EventCategoryController@getPublicEventCategories');
//reviews
Route::get('reviewsbyevent/{id}','UserReviewController@publicReviewsByEvent');
//discount
Route::get('discountbyevent/{id}','DiscountController@getDiscountByEvent');






