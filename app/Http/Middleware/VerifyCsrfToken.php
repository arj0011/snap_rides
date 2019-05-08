<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */ 
    protected $except = [ 'registerCustomer','verifyOTP','updateProfile','nearestDrivers','resentOTP','imageUpdate','getCustomerInfo','driverRegistration','vehicleRegistration','verifyDriverOTP','documnetRegistration','getSubscriptionPlan','driverLogin','nearestDriversLatLong','validateAppVersion','getSubscriptionPlan','getRideEstimate','driverImageUpdate' , 'approvalStatus' , 'vehicleCategoies','documnetVerification','buySubscriptionPlan','checkPayment','createBooking','updateDriverStatus','cancelBooking','getDriverLatLong','completeBooking','rateDriver', 'bookingDetails','driverLogout','updateBookingStatus','rideHistory','getDriverDocuments','getPlanDetail','getDriverProfile','sentDriverLatLong' , 'updateDriverProfile','buyDemoPlan','driverRideHistory' ,'customerStatus','orderResponse','placeOrder','getDiscountRange','updateDiscount','varifyBooking','addEmergencyContact','getEmergencyContact','updateEmergencyContact','deleteEmergencyContact','richedOnPickup','getBookingInvoice','shareUrl','getRoutes','updatePerKmCharge','updateCity','deletecity','driverForgotPassword','customerForgotPassword','driverResetPassword','customerResetPassword','getInvteCode','getMyoffers','applyOfferCode','checkCustomerOfferExist','faq','validateAppVersion_customer','prepareCheckout','checkPaymentStatus','createScheduleBookings','getScheduleBookings','scheduleBookingDetailCM','getScheduleBookingsDriver','scheduleBookingDetailDriver','outForCustomerPickup','cancelScheduleBookingByDriver','cancelScheduleBookingByCM','sendPickupRequestToDriver','prepareCheckoutNew','checkPaymentStatusNew','noPaymentRequired','isCustomerOnRide'
    ];  
}
