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

/************************* Admin Panel routes ********************/
 
  Route::get('/', 'Auth\LoginController@showLoginForm')->name('login');
  Route::get('admin','Admin\HomeController@index')->name('home');
  Route::get('sharedRoute/{id}','ShareRouteController@index')->name('sharedRoute');
  Route::get('unblockUser','ShareRouteController@unblockUser')->name('unblockUser');


  Route::post('getRoutes','ShareRouteController@getRoutes')->name('getRoutes');
  

  Route::get('admin/dashboard','Admin\HomeController@index')->name('dashboard');

   //offer routes

  Route::get('/offers/{type}','Admin\OfferCodeController@index')->name('offers');
  Route::post('/offers/{type}','Admin\OfferCodeController@index')->name('offers');
  Route::get('/offers','Admin\OfferCodeController@index')->name('offers');
  Route::get('/editOffers/{type}','Admin\OfferCodeController@editOffers')->name('editOffers');
  
  Route::get('/addOffer','Admin\OfferCodeController@addOffer')->name('addOffer');
  Route::post('/saveOffer','Admin\OfferCodeController@saveOffer')->name('saveOffer'); 
  Route::get('/editOffer','Admin\OfferCodeController@editOffer')->name('editOffer');
  Route::put('/set-Offerstatus','Admin\OfferCodeController@setStatus')->name('set-Offerstatus');
  Route::get('/addInviteCode','Admin\OfferCodeController@addInviteCode')->name('addInviteCode');
  Route::get('/driverInvitation','Admin\OfferCodeController@driverInvitation')->name('driverInvitation');
  Route::post('/driverInvitation','Admin\OfferCodeController@driverInvitation')->name('driverInvitation');
  Route::get('/driver_invitationDetail/{type}','Admin\OfferCodeController@driver_invitationDetail')->name('driver_invitationDetail');
  Route::get('/sendPromocode/{promo_id}','Admin\OfferCodeController@sendPromocode')->name('sendPromocode');
  Route::post('/sendPromocode/{promo_id}','Admin\OfferCodeController@sendPromocode')->name('sendPromocode');
  
  
  Route::put('/changeInvitecodeSetting','Admin\OfferCodeController@changeInvitecodeSetting')->name('changeInvitecodeSetting');
  Route::post('/saveInvitecode','Admin\OfferCodeController@saveInvitecode')->name('saveInvitecode');
  
  Route::put('/send-Promocode','Admin\OfferCodeController@send_Promocode')->name('send-Promocode');
  //Route::post('/searchOffer','Admin\OfferCodeController@searchOffer')->name('searchOffer');
  


  Route::get('/change-password','Admin\HomeController@changePassword')->name('change-password');
  Route::put('/update-password','Admin\HomeController@updatePassword')->name('update-password');

  Route::get('/dashboard','Admin\DashboardController@index')->name('update-password');

  Auth::routes();

  Route::group(['middleware' => 'auth'], function () {

  Route::name('notification/')->group(function () {
      Route::get('/send-notification','Admin\NotificationController@create')->name('create');
      Route::post('/save-notification','Admin\NotificationController@store')->name('store');
      Route::get('/getUsers','Admin\NotificationController@getUsers')->name('getUsers');

      Route::get('/sendotp','Admin\NotificationController@sendotp')->name('sendotp');
      Route::post('/sendsms','Admin\NotificationController@sendsms')->name('sendsms');
      Route::post('/send_sms','Admin\NotificationController@send_sms')->name('send_sms');
  });

   Route::name('sms/')->group(function () {
      Route::get('/send-sms','Admin\SmsController@create')->name('create');
      Route::post('/save-sms','Admin\SmsController@store')->name('store');
      Route::get('/get-users','Admin\SmsController@getUsers')->name('getUsers');

  });

   Route::name('support/')->group(function () {
    Route::get('/banners','Admin\SupportController@banners')->name('banners');
    Route::post('/saveBanner','Admin\SupportController@saveBanner')->name('saveBanner');
    Route::get('/supports','Admin\SupportController@index')->name('supports');
    Route::get('/search-support','Admin\SupportController@search')->name('search');
    Route::get('/add-support','Admin\SupportController@create')->name('create');
    Route::post('/save-support','Admin\SupportController@store')->name('store');
    Route::get('/edit-support','Admin\SupportController@edit')->name('edit');
    Route::get('/show-support','Admin\SupportController@show')->name('show');
    Route::put('/update-support','Admin\SupportController@update')->name('update');
    Route::get('/delete-support','Admin\SupportController@destroy')->name('destroy');
    Route::put('/user-support','Admin\SupportController@setStatus')->name('set-status');
  });

  Route::name('profile/')->group(function () {
    Route::get('/profile','Admin\ProfileController@show')->name('show');
    Route::get('/edit-profile','Admin\ProfileController@edit')->name('edit');
    Route::put('/update-profile','Admin\ProfileController@update')->name('update');
  });


    Route::name('user/')->group(function () {
    Route::get('/users','Admin\UserController@index')->name('users');
    Route::post('/search-user','Admin\UserController@search')->name('search');
    Route::get('/add-user','Admin\UserController@create')->name('create');
    Route::post('/save-user','Admin\UserController@store')->name('store');
    Route::get('/edit-user','Admin\UserController@edit')->name('edit');
    Route::get('/show-user','Admin\UserController@show')->name('show');
    Route::put('/update-user','Admin\UserController@update')->name('update');
    Route::get('/delete-user','Admin\UserController@destroy')->name('destroy');
    Route::put('/user-status','Admin\UserController@setStatus')->name('set-status');
  });

  Route::name('driver/')->group(function () {
    Route::get('/drivers','Admin\DriverController@index')->name('drivers');
    Route::post('/search-driver','Admin\DriverController@search')->name('search');
    Route::get('/add-driver','Admin\DriverController@create')->name('create');
    Route::post('/save-driver','Admin\DriverController@store')->name('store');
    Route::get('/edit-driver','Admin\DriverController@edit')->name('edit');
    Route::get('/show-driver','Admin\DriverController@show')->name('show');
    Route::put('/update-driver','Admin\DriverController@update')->name('update');
    Route::get('/delete-driver','Admin\DriverController@destroy')->name('destroy');
    Route::get('/add-documents','Admin\DriverController@storeDocuments')->name('store-documents');
    Route::get('/edit-documents','Admin\DriverController@editDocuments')->name('edit-documents');
    Route::post('/upload-documents','Admin\DriverController@uploadDocuments')->name('upload-documents');
    Route::put('/update-documents','Admin\DriverController@updateDocuments')->name('update-documents');
    Route::get('/verification-status','Admin\DriverController@setVerificationStatus')->name('verification-status');
    Route::put('/approved','Admin\DriverController@setApprovedStatus')->name('approved');
    Route::put('/decline-driver','Admin\DriverController@declineDriver')->name('decline-driver');
    Route::put('/driver-status','Admin\DriverController@setStatus')->name('set-status');
    Route::get('/tracking','Admin\DriverController@realtimeTracking')->name('tracking');

    Route::get( '/downloadDoc/{filename}', 'Admin\DriverController@downloadDoc')->name('downloadDoc');
    
  });

  Route::name('category/')->group(function () {
    Route::get('/categories','Admin\CategoryController@index')->name('categories');
    Route::get('/search-category','Admin\CategoryController@search')->name('search');
    Route::get('/add-category','Admin\CategoryController@create')->name('create');
    Route::post('/save-category','Admin\CategoryController@store')->name('store');
    Route::get('/edit-category','Admin\CategoryController@edit')->name('edit');
    Route::get('/show-category','Admin\CategoryController@show')->name('show');
    Route::put('/update-category','Admin\CategoryController@update')->name('update');
    Route::get('/delete-category','Admin\CategoryController@destroy')->name('destroy');
    Route::put('/category-status','Admin\CategoryController@setStatus')->name('set-status');
  
  });

  Route::name('role/')->group(function () {
    Route::get('/roles','Admin\RoleController@index')->name('roles');
    Route::get('/search-role','Admin\RoleController@search')->name('search');
    Route::get('/add-role','Admin\RoleController@create')->name('create');
    Route::post('/save-role','Admin\RoleController@store')->name('store');
    Route::get('/edit-role','Admin\RoleController@edit')->name('edit');
    Route::get('/show-role','Admin\RoleController@show')->name('show');
    Route::put('/update-role','Admin\RoleController@update')->name('update');
    Route::get('/delete-role','Admin\RoleController@destroy')->name('destroy');
    Route::put('/role-status','Admin\RoleController@setStatus')->name('set-status');
   
  });

  Route::name('permission/')->group(function () {
    Route::get('/permissions','Admin\PermissionController@index')->name('permissions');
    Route::get('/search-permission','Admin\PermissionController@search')->name('search');
    Route::get('/add-permission','Admin\PermissionController@create')->name('create');
    Route::post('/save-permission','Admin\PermissionController@store')->name('store');
    Route::get('/edit-permission','Admin\PermissionController@edit')->name('edit');
    Route::get('/show-permission','Admin\PermissionController@show')->name('show');
    Route::put('/update-permission','Admin\PermissionController@update')->name('update');
    Route::get('/delete-permission','Admin\PermissionController@destroy')->name('destroy');
    Route::put('/permission-status','Admin\PermissionController@setStatus')->name('set-status');
  });


  Route::name('vehicle/')->group(function () {
    Route::get('/vehicles','Admin\VehicleController@index')->name('vehicles');
    Route::get('/search-vehicle','Admin\VehicleController@search')->name('search');
    Route::get('/add-vehicle','Admin\VehicleController@create')->name('create');
    Route::post('/save-vehicle','Admin\VehicleController@store')->name('store');
    Route::get('/edit-vehicle','Admin\VehicleController@edit')->name('edit');
    Route::get('/show-vehicle','Admin\VehicleController@show')->name('show');
    Route::put('/update-vehicle','Admin\VehicleController@update')->name('update');
    Route::get('/delete-vehicle','Admin\VehicleController@destroy')->name('destroy');
    Route::put('/vehicle-status','Admin\VehicleController@setStatus')->name('set-status');
  });

  Route::name('plan/')->group(function () {
    Route::get('/plans','Admin\PlanController@index')->name('plans');
    Route::get('/search-plan','Admin\PlanController@search')->name('search');
    Route::get('/add-plan','Admin\PlanController@create')->name('create');
    Route::post('/save-plan','Admin\PlanController@store')->name('store');
    Route::get('/edit-plan','Admin\PlanController@edit')->name('edit');
    Route::get('/show-plan','Admin\PlanController@show')->name('show');
    Route::put('/update-plan','Admin\PlanController@update')->name('update');
    Route::get('/delete-plan','Admin\PlanController@destroy')->name('destroy');
    Route::put('/plan-status','Admin\PlanController@setStatus')->name('set-status');
  });


  Route::name('ajax/')->group(function () {
    Route::get('/states','Admin\HomeController@getStates')->name('getStates');
    Route::get('/cities','Admin\HomeController@getCities')->name('getCities');
    Route::get('/getCharges','Admin\HomeController@getCharges')->name('getCharges');
  });

  Route::name('rider/')->group(function () {
    Route::get('/riders','Admin\RiderController@index')->name('riders');
    Route::get('/search-rider','Admin\RiderController@search')->name('search');
    Route::get('/show-rider','Admin\RiderController@show')->name('show');
    Route::put('/rider-status','Admin\RiderController@setStatus')->name('set-status');
    Route::get('/delete-rider','Admin\RiderController@destroy')->name('destroy');
  });

  Route::name('booking/')->group(function () {
    Route::get('/bookings','Admin\BookingController@index')->name('bookings');
    Route::get('/search-booking','Admin\BookingController@search')->name('search');
    Route::get('/invoice','Admin\BookingController@invoice')->name('invoice');
    Route::get('/delete-booking','Admin\BookingController@destroy')->name('destroy');

    Route::get('/create-booking','Admin\BookingController@create')->name('create');
    Route::post('/store-booking','Admin\BookingController@store')->name('store');
    Route::get('/load-drivers','Admin\BookingController@loadDriver')->name('loadDriver');


    });

   Route::name('setting/')->group(function () {
        Route::get('/settings','Admin\ApplicationController@index')->name('settings');

        Route::put('/update','Admin\ApplicationController@update')->name('update');

        Route::get('/list','Admin\ApplicationController@list')->name('list');
        Route::get('/drawShape','Admin\ApplicationController@drawShape')->name('drawShape');
 
        Route::post('/updateCity','Admin\ApplicationController@updateCity')->name('updateCity');

        Route::post('/deletecity','Admin\ApplicationController@deletecity')->name('deletecity');
        
    });

  Route::name('report/')->group(function () {
    Route::get('/reports','Admin\ReportController@index')->name('reports'); 
    Route::get('/search-report','Admin\ReportController@search')->name('search');  
  }); 

  Route::name('payment/')->group(function () {
    Route::get('/payments','Admin\PaymentController@index')->name('payments'); 
    Route::get('/search-payment','Admin\PaymentController@search')->name('search');  
  }); 

});
    

//Customert API Route


Route::post('/applyOfferCode','Api\ApiCustomerController@applyOfferCode')->name('applyOfferCode');

Route::post('/getMyoffers','Api\ApiCustomerController@getMyoffers')->name('getMyoffers');

Route::post('/shareUrl','Api\ApiCustomerController@shareUrl')->name('shareUrl');

Route::post('/deleteEmergencyContact','Api\ApiCustomerController@deleteEmergencyContact')->name('deleteEmergencyContact');

Route::post('/updateEmergencyContact','Api\ApiCustomerController@updateEmergencyContact')->name('updateEmergencyContact');

Route::post('/getEmergencyContact','Api\ApiCustomerController@getEmergencyContact')->name('getEmergencyContact');

Route::post('/addEmergencyContact','Api\ApiCustomerController@addEmergencyContact')->name('addEmergencyContact');

Route::post('/sentDriverLatLong','Api\ApiCustomerController@sentDriverLatLong')->name('sentDriverLatLong');

Route::post('/nearestDrivers','Api\ApiCustomerController@nearestDrivers')->name('nearestDrivers');


Route::post('/registerCustomer','Api\ApiCustomerController@registerCustomer')->name('registerCustomer');
//Route::post('/forgot_Password','Api\ApiCustomerController@forgot_Password')->name('forgot_Password');


Route::post('/verifyOTP','Api\ApiCustomerController@verifyOTP')->name('verifyOTP');


Route::post('/updateProfile','Api\ApiCustomerController@updateProfile')->name('updateProfile');
Route::post('/resentOTP','Api\ApiCustomerController@resentOTP')->name('resentOTP');

Route::post('/imageUpdate','Api\ApiCustomerController@imageUpdate')->name('imageUpdate');

Route::post('/getCustomerInfo','Api\ApiCustomerController@getCustomerInfo')->name('getCustomerInfo');
Route::post('/nearestDriversLatLong','Api\ApiCustomerController@nearestDriversLatLong')->name('nearestDriversLatLong');
Route::post('/rateDriver','Api\ApiCustomerController@rateDriver')->name('rateDriver');

Route::post('/rideHistory','Api\ApiCustomerController@rideHistory')->name('rateDriver'); 

Route::get('/faq','Api\ApiStaticPages@faq')->name('faq');
Route::post('/faq','Api\ApiStaticPages@faq')->name('faq');

Route::get('/terms','Api\ApiStaticPages@terms')->name('terms');

Route::get('/pages','Api\ApiStaticPages@pages')->name('pages');
Route::get('/private_policy','Api\ApiStaticPages@private_policy')->name('private_policy');


Route::get('/contact','Api\ApiCustomerController@contact')->name('contact');

Route::post('/customerStatus','Api\ApiCustomerController@customerStatus')->name('customerStatus');
Route::get('/vehicleTypes','Api\ApiCustomerController@vehicleTypes')->name('vehicleTypes');

Route::post('/isCustomerOnRide','Api\ApiCustomerController@isCustomerOnRide')->name('isCustomerOnRide');
Route::post('/noPaymentRequired','Api\ApiDriverController@noPaymentRequired')->name('noPaymentRequired');


//Booking Apis 

 
 Route::get('/fcmNotificationTest','Api\ApiBookingController@fcmNotificationTest')->name('fcmNotificationTest');

 Route::get('/testios','Api\ApiBookingController@iosNotification')->name('testios');
 Route::get('/testsocket','Api\ApiBookingController@testsocket')->name('testsocket');
 

Route::post('/getRideEstimate','Api\ApiBookingController@getRideEstimate')->name('getRideEstimate');
Route::post('/checkCustomerOfferExist','Api\ApiBookingController@checkCustomerOfferExist')->name('checkCustomerOfferExist');

Route::post('/richedOnPickup','Api\ApiBookingController@richedOnPickup')->name('richedOnPickup');


Route::post('/createBooking','Api\ApiBookingController@createBooking')->name('createBooking');

Route::get('/fcmNotification','Api\ApiCustomerController@fcmNotification')->name('fcmNotification');
Route::post('/varifyBooking','Api\ApiBookingController@varifyBooking')->name('varifyBooking');
 


/*Route::get('/getStatus','Api\ApiCustomerController@getStatus')->name('getStatus');
*/
Route::post('/cancelBooking','Api\ApiBookingController@cancelBooking')->name('cancelBooking');

Route::post('/completeBooking','Api\ApiBookingController@completeBooking')->name('completeBooking');

Route::post('/bookingDetails','Api\ApiBookingController@bookingDetails')->name('bookingDetails');

Route::post('/getBookingInvoice','Api\ApiBookingController@bookingInvoice')->name('getBookingInvoice');



Route::post('/updateBookingStatus','Api\ApiBookingController@updateBookingStatus')->name('updateBookingStatus');

Route::post('/createScheduleBookings','Api\ApiBookingController@createScheduleBookings')->name('createScheduleBookings');

Route::post('/getScheduleBookings','Api\ApiBookingController@getScheduleBookings')->name('getScheduleBookings');

Route::post('/scheduleBookingDetailCM','Api\ApiBookingController@scheduleBookingDetailCM')->name('scheduleBookingDetailCM');


Route::post('/getScheduleBookingsDriver','Api\ApiDriverController@getScheduleBookingsDriver')->name('getScheduleBookingsDriver');

Route::post('/scheduleBookingDetailDriver','Api\ApiDriverController@scheduleBookingDetailDriver')->name('scheduleBookingDetailDriver');

Route::post('/outForCustomerPickup','Api\ApiDriverController@outForCustomerPickup')->name('outForCustomerPickup');

Route::post('/cancelScheduleBookingByDriver','Api\ApiDriverController@cancelScheduleBookingByDriver')->name('cancelScheduleBookingByDriver');
Route::post('/cancelScheduleBookingByCM','Api\ApiCustomerController@cancelScheduleBookingByCM')->name('cancelScheduleBookingByCM');

Route::post('/sendPickupRequestToDriver','Api\ApiCustomerController@sendPickupRequestToDriver')->name('sendPickupRequestToDriver');


//Payment
Route::post('/prepareCheckout','Api\ApiBookingController@prepareCheckout')->name('prepareCheckout');
Route::post('/checkPaymentStatus','Api\ApiBookingController@checkPaymentStatus')->name('checkPaymentStatus');

Route::post('/prepareCheckoutNew','Api\ApiBookingController@prepareCheckoutNew')->name('prepareCheckoutNew');
Route::post('/checkPaymentStatusNew','Api\ApiBookingController@checkPaymentStatusNew')->name('checkPaymentStatusNew');


 
//driver api


 Route::post('/getInvteCode','Api\ApiDriverController@getInvteCode')->name('getInvteCode');

 
 Route::post('/updatePerKmCharge','Api\ApiDriverController@updatePerKmCharge')->name('updatePerKmCharge');

Route::post('/updateDiscount','Api\ApiDriverController@updateDiscount')->name('updateDiscount');

Route::post('/getDiscountRange','Api\ApiDriverController@getDiscountRange')->name('getDiscountRange');
 
Route::post('/getPlanDetail','Api\ApiDriverController@getPlanDetail')->name('getPlanDetail');
 
Route::post('/getDriverProfile','Api\ApiDriverController@getDriverProfile')->name('getDriverProfile');

Route::post('/updateDriverProfile','Api\ApiDriverController@updateDriverProfile')->name('updateDriverProfile');
 


Route::post('/getDriverDocuments','Api\ApiDriverController@getDriverDocuments')->name('getDriverDocuments');



Route::post('/driverLogout','Api\ApiDriverController@driverLogout')->name('driverLogout');

Route::post('/validateAppVersion','Api\ApiDriverController@validateAppVersion')->name('validateAppVersion');
Route::post('/validateAppVersion_customer','Api\ApiCustomerController@validateAppVersion_customer')->name('validateAppVersion_customer');



Route::post('/driverRegistration','Api\ApiDriverController@driverRegistration')->name('driverRegistration');

Route::post('/driverImageUpdate','Api\ApiDriverController@imageUpdate')->name('driverImageUpdate');

Route::post('/vehicleRegistration','Api\ApiDriverController@vehicleRegistration')->name('vehicleRegistration');

Route::post('/verifyDriverOTP','Api\ApiDriverController@verifyOTP')->name('verifyDriverOTP');
Route::post('/documnetRegistration','Api\ApiDriverController@documnetRegistration')->name('documnetRegistration');

Route::post('/getSubscriptionPlan','Api\ApiDriverController@getSubscriptionPlan')->name('getSubscriptionPlan');

Route::post('/buySubscriptionPlan','Api\ApiDriverController@buySubscriptionPlan')->name('buySubscriptionPlan');

Route::post('/buyDemoPlan','Api\ApiDriverController@buyDemoPlan')->name('buyDemoPlan');

Route::post('/driverLogin','Api\ApiDriverController@driverLogin')->name('driverLogin');

Route::post('/approvalStatus','Api\ApiDriverController@approvalStatus')->name('approvalStatus');

Route::post('/vehicleCategoies','Api\ApiDriverController@vehicleCategoies')->name('vehicleCategoies');

Route::post('/checkPayment','Api\ApiDriverController@checkPayment')->name('checkPayment');
 
Route::post('/documnetVerification','Api\ApiDriverController@documnetVerification')->name('documnetVerification');

Route::post('/updateDriverStatus','Api\ApiDriverController@updateDriverStatus')->name('updateDriverStatus');

Route::post('/getDriverLatLong','Api\ApiDriverController@getDriverLatLong')->name('getDriverLatLong');

Route::post('/driverRideHistory','Api\ApiDriverController@driverRideHistory')->name('driverRideHistory');
 

Route::get('/placeOrder','Api\ApiDriverController@placeOrder')->name('placeOrder');
Route::post('/placeOrder','Api\ApiDriverController@placeOrder')->name('placeOrder');

Route::post('/orderResponse','Api\ApiDriverController@orderResponse')->name('orderResponse');
Route::get('/orderResponse','Api\ApiDriverController@orderResponse')->name('orderResponse');
 
Route::post('/driverForgotPassword/','Api\ApiDriverController@driverForgotPassword');
Route::post('/customerForgotPassword/','Api\ApiCustomerController@customerForgotPassword');

Route::post('/driverResetPassword/','Api\ApiDriverController@driverResetPassword');
Route::post('/customerResetPassword/','Api\ApiBookingController@test_ios');

Route::get('mail/send', 'MailController@send');
Auth::routes();