<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Customer;
use DB;
use App\Http\Controllers\Controller;
use App\VehicleCategory;
use App\Booking;
use App\Driver;
use App\Vehicle;
use App\Setting;
use App\Category;
use Config;
use App\DriverLatLong;
use App\Payment;
//use App\CancelBooking;
use App\CancelCount;
use App\BookingCountDiscount;
use App\OfferUsedByCustomers;
use Illuminate\Support\Facades\Validator;

class ApiBookingController extends Controller {

    public function createBooking(Request $request)
    {
        $security_token = $request->header('stoken');
        $obj = (object) [];
        if ($security_token == null || $security_token == '') {
            $status = false;
            $data = array("success" => false, "message" => "Security token can not be blank", "data" => $obj);
            return $data;
            exit;
        } else if ($security_token != '987654321') {
            $data = array("success" => false, "message" => "Please Add Correct Security Token", "data" => $obj);
            return $data;
            exit;
        }

        $environment = "adhoc";
        $environment = $request->header('environment'); // type of server using for iosnotification
        if ($environment == null || $environment == '') {
            $environment = "adhoc";
        }

        $customer_id = $request->input('customer_id');
        if ($customer_id == null || $customer_id == '') {
            $status = false;
            $data = array("success" => false, "message" => "Customer can not be blank", "data" => $obj);
            return $data;
            exit;
        }

        //check CM is active
        $customer_status = DB::table('customers')->where("status","=","1")->where("id","=",$customer_id)->first();
        if(empty($customer_status->name)){
             $status = false;
             $data = array("success" => false, "message" => "Your profile has been dactivated please contact to admin.", "data" => $obj);
             return $data;
             exit;
        }

        $vehicle_type = $request->input('vehicle_type');
        if ($vehicle_type == null || $vehicle_type == '') {
            $status = false;
            $data = array("success" => false, "message" => "Vehicle type can not be blank", "data" => $obj);
            return $data;
            exit;
        }    

        $pickup_lat = $request->input('pickup_lat');
        if ($pickup_lat == null || $pickup_lat == '') {
            $status = false;
            $data = array("success" => false, "message" => "Pickup latitude can not be blank", "data" => $obj);
            return $data;
            exit;
        }
        $pickup_long = $request->input('pickup_long');
        if ($pickup_long == null || $pickup_long == '') {
            $status = false;
            $data = array("success" => false, "message" => "Pickup longitude can not be blank", "data" => $obj);
            return $data;
            exit;
        }
        
        $drop_lat = $request->input('drop_lat');
        $drop_long = $request->input('drop_long');

        //  0 => normail booking, 1 => offer booking
        $booking_type = $request->input('booking_type');
        if ($booking_type == null || $booking_type == '') {
            $booking_type = 0; 
        }
        $offer_code = $request->input('offer_code');
        if ($offer_code == null || $offer_code == '') {
            $offer_code = '';
        }

        $pickup_addrees = $request->input('pickup_addrees');
        $destination_address = $request->input('destination_address');

        if ($destination_address == null || $destination_address == '') {
            $destination_address = "NA";
        }
        date_default_timezone_set ("Africa/Johannesburg") ;
        $booking_time = date('Y-m-d G:i:s');
        $pickup_lat_long = $pickup_lat . ',' . $pickup_long;

        if ($drop_long != null || $drop_long != '') {

            $destinations_lat_long = $drop_lat . ',' . $drop_long;
            $api = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$pickup_lat_long&destinations=$destinations_lat_long&key=AIzaSyBnTKkK26b0bwrCOU8XMoqzpUMVrHnf554";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $api);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($result);

            $duration = 0;
            $distance = 0;
            if (isset($data->rows[0]->elements[0]->distance->value)) {
                /* $distance = $data->rows[0]->elements[0]->distance->text; */
                $distance = ($data->rows[0]->elements[0]->distance->value) / 1000;
            } else {
                $distance = 0;
            }

            if (isset($data->rows[0]->elements[0]->duration->value)) {
                $duration = ($data->rows[0]->elements[0]->duration->value) / 60;
            } else {
                $duration = 0;
            }
        } else {
            $distance = 0;
            $duration = 0;
            $drop_lat = 0;
            $drop_long = 0;
        }

        $otp = $this->random_string();

        $booking = new Booking;
        $booking->customer_id = $customer_id;

        $booking->pickup_lat = $pickup_lat;
        $booking->pickup_long = $pickup_long;
        $booking->drop_lat = $drop_lat;
        $booking->drop_long = $drop_long;

        $booking->booking_time = $booking_time;
        $booking->pickup_addrees = $pickup_addrees;

        $booking->destination_address = $destination_address;
        $booking->booking_status = 'pending';
        $booking->otp = $otp;
        $booking->estimated_distance = $distance;
        $booking->estimated_time = $duration;
        $booking->offer_code = $offer_code;
        $booking->booking_type = $booking_type;
        $booking->save();
        $bookingId = $booking->id;

        //send OTP to CM
        $customer_det = Customer::where('id', $customer_id)->get();
        $mobilearray = array($customer_det[0]->mobile);
        $msg = "Your otp is " . $otp;
        $res = commonSms($mobilearray, $msg);

        

        $sql = "SELECT
                drivers.id as driver_id,drivers.device_token,drivers.type,
                111.045 * DEGREES(
                ACOS(
                COS(RADIANS($pickup_lat)) * COS(
                RADIANS(driver_latlong.latitude)
                ) * COS(
                RADIANS($pickup_long) - RADIANS(driver_latlong.longitude)
                ) + SIN(RADIANS($pickup_lat)) * SIN(
                RADIANS(driver_latlong.latitude)
                )
                )
                ) AS distance_in_km
                FROM driver_latlong
                INNER JOIN drivers ON driver_latlong.driver_id = drivers.id
                LEFT JOIN vehicles on  vehicles.driver_id=drivers.id 
                LEFT JOIN vehicle_category ON vehicles.vehicle_category = vehicle_category.id
                WHERE drivers.is_active = '1' AND drivers.deleted_at IS NULL AND
                drivers.is_online = 1 AND drivers.device_token IS NOT NULL AND 
                drivers.is_receive_notifi = 0 AND vehicles.vehicle_category = $vehicle_type AND
                driver_latlong.booking_id = 0 AND 
                driver_latlong.id IN (
                SELECT MAX(id) FROM driver_latlong GROUP BY driver_latlong.id) AND
                drivers.id NOT IN(
                SELECT driver_id FROM booking WHERE booking_status = 'in_progress')
                GROUP BY drivers.id
                HAVING distance_in_km <= 5
                ORDER BY distance_in_km ASC";
        
        $results = DB::select( DB::raw($sql));                   
        
        $timeout = 30;
        $setting = Setting::where([['key','driver_request_timeout'],['is_active',1]])->first(); 
        if(!empty($setting)){
            $timeout = $setting->value;
        }

        if(!empty($results)){

            $msg = array(
                    'body' => 'Upcoming Trip Request',
                    'title' => 'Trip Notification',
                    'icon' => 'myicon', /* Default Icon */
                    'sound' => 'mySound'/* Default sound */
            );
            foreach ($results as $driver) {
                $token = '';
                if ($driver->type == "1"){
                   $token = $driver->device_token;
                   $fields = array(
                        'to'=>$token,
                        'notification' => $msg,
                        'data' => array('bookingid' => $bookingId, 'type' => '1', 'message' => 'booking_request'),
                        'android' => array('ttl' => (String)$timeout),
                        'time_to_live' => (int)$timeout
                    );
                   $this->fcmNotification($msg, $fields, 'driverapp');
                }else{
                   $token = $driver->device_token;      
                   $fields = array(
                        'to'=>$token,
                        'notification' => $msg,
                        'data' => array('bookingid' => $bookingId, 'type' => '1', 'message' => 'booking_request'),
                        'android' => array('ttl' => (String)$timeout),
                        'time_to_live' => (int)$timeout
                    );
                   $this->iosNotification($msg, $fields, 'driverapp', $environment);
                }

                //set is_receive_notification 1 to stop notification until accept/reject/timeout
                DB::table('drivers')->where('id',$driver->driver_id)->update(['is_receive_notifi'=>1]);

            }
            
        }
        
        $current_time = date('Y-m-d H:i:s');
        $stop = strtotime($current_time) + $timeout;
        
        $dbstatus = 'pending';    
        while ($dbstatus == 'pending') {
            $new = strtotime(date('Y-m-d H:i:s'));
            if ($new >= $stop) {
                $req = array('booking_id' => $bookingId, 'canceled_by' => 'auto', 'stoken' => '987654321');
                // $status = "canceled";
                $status = "rejected";
                $cancel_time = date('Y-m-d H:i:s');
                $res = Booking::where('id', $bookingId)
                        ->update(['booking_status' => $status, 'cancel_time' => $cancel_time, 'canceled_by' => "auto"]);
                $data = array("success" => false, "message" => "Drivers not responding for booking ", "data" => array());

                //After timeout if no driver response then set is_receive_notification to 0 so that drivers can get notification again.
                if(!empty($results)){
                    foreach ($results as $driver){
                        DB::table('drivers')->where('id',$driver->driver_id)
                        ->update(['is_receive_notifi'=>0]);        
                    }
                }
                
                return $data;
                exit;
            }
            sleep(5);
            $dbstatus = $this->getStatus($bookingId);
        }

        //After accept/reject status set is_receive_notification to 0 so that drivers can get notification again.
        if(!empty($results)){
            foreach ($results as $driver){
                DB::table('drivers')->where('id',$driver->driver_id)
                ->update(['is_receive_notifi'=>0]);        
            }
        }

            $array = array(
                'otp' => $otp,
                'booking_id' => $bookingId,
            );

            $driver_id = '';
            $reg = $vehicle_img = $vehicle_cat = $basefare = $per_km_charge = '';
            $name = $mobile = $email = $image = ''; 

            $driverData = DB::table('booking')->select('driver_id')->where('id',$bookingId)->first();

            //if(isset($driverData->driver_id) && $driverData->driver_id != ''){
                $driver_id = $driverData->driver_id;
                if($driver_id != ''){
                    
                    $driver_data = DB::table('drivers')
                               ->select('drivers.name', 'drivers.mobile', 'drivers.email', 'drivers.profile_image','vehicle_category.name as vehicle_cat','vehicle_category.basefare', 'vehicle_category.per_km_charges','vehicles.registration_number as reg', 'vehicles.vehicle_image')
                               ->leftJoin('vehicles', 'drivers.id', '=', 'vehicles.driver_id')
                               ->leftJoin('vehicle_category', 'vehicles.vehicle_category', '=', 'vehicle_category.id')
                               ->where('drivers.id',$driver_id)
                               ->first();           
                    if(isset($driver_data->reg)) {
                        $reg = $driver_data->reg;
                    }
                    
                    $destinationPath = 'Admin/profileImage';
                    $image = url('/') . '/' . $destinationPath . '/' . 'noimage.png';
                    if (isset($driver_data->profile_image) && ($driver_data->profile_image != '')) {
                        if (file_exists($destinationPath . '/' . $driver_data->profile_image)) {
                            $image = url('/') . '/' . $destinationPath . '/' . $driver_data->profile_image;
                        }
                    }

                    $vehiclePath = 'Admin/vehicleImage';
                    $vehicle_img = url('/') . '/' . $vehiclePath . '/' . 'noimage.png';
                    if (isset($driver_data->vehicle_image) && ($driver_data->vehicle_image != '')){
                        if(file_exists($vehiclePath . '/' . $driver_data->vehicle_image)) {
                            $vehicle_img = url('/') . '/' . $vehiclePath . '/' . $driver_data->vehicle_image;
                        }
                    }

                    if (isset($driver_data->vehicle_cat) && $driver_data->vehicle_cat != null) {
                        $vehicle_cat = $driver_data->vehicle_cat;
                    }

                    if (isset($driver_data->basefare) && $driver_data->basefare != null) {
                        $basefare = $driver_data->basefare;
                    } 
                    if (isset($driver_data->per_km_charges) && $driver_data->per_km_charges != null) {
                        $per_km_charge = $driver_data->per_km_charges;
                    }
                    if (isset($driver_data->name)) {
                        $name = $driver_data->name;
                    } 
                    if (isset($driver_data->mobile)) {
                        $mobile = $driver_data->mobile;
                    }
                    if (isset($driver_data->email)) {
                        $email = $driver_data->email;
                    }

                }   
            //}
            
            $array['driver_id'] = $driver_id;
            $array['name'] = $name;
            $array['mobile'] = $mobile;
            $array['email'] = $email;
            $array['image'] = $image;
            $array['vehicle_cat'] = $vehicle_cat;
            $array['basefare'] = $basefare;
            $array['per_km_charge'] = $per_km_charge;
            $array['booking_status'] = $dbstatus;
            $array['vehicle_img'] = $vehicle_img;
            $array['reg'] = $reg;

            // if ($dbstatus == 'canceled') {
            if ($dbstatus == 'rejected') {
                $data = array("success" => false, "message" => "Your booking status is " . $dbstatus, "data" => $array);
            } else {
                $data = array("success" => true, "message" => "Your booking status is " . $dbstatus, "data" => $array);
            }

            return $data;
    }    


    public function bookingDetails(Request $request) {
        $booking_id = $request->input('booking_id');
        if ($booking_id == null || $booking_id == '') {
            $data = array("success" => false, "message" => "Booking Id can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $booking = Booking::where('id', $booking_id)->get();
        if (!isset($booking[0])) {
            $data = array("success" => false, "message" => "Booking Id not matched");
            print_r(json_encode($data));
            exit;
        }

        $customer_id = $booking[0]->customer_id;
        $customer = Customer::where('id', $customer_id)->get();
        $data = array();

        if (isset($booking[0])) {
            if (isset($customer[0]->name)) {
                $customer_name = $customer[0]->name;
            } else {
                $customer_name = " ";
            }

            if (isset($customer[0]->mobile)) {
                $mobile = $customer[0]->mobile;
            } else {
                $mobile = " ";
            }

            $basepath = url('/');
            $destinationPath = 'Admin/customerimg';
            $image = $basepath . '/' . $destinationPath . '/' . 'noimage.png';
            if (isset($customer[0]->image)) {
                $path = $basepath . '/' . $destinationPath . '/' . $customer[0]->image;
                // && (file_exists($path))
                if ($customer[0]->image != '') {
                    $image = $path;
                }
            }

            $data = array('pickup_addrees' => $booking[0]->pickup_addrees,
                'destination_address' => $booking[0]->destination_address,
                'estimated_distance' => number_format($booking[0]->estimated_distance,1),
                'distance_unit' => 'km',
                'estimated_time' => $booking[0]->estimated_time,
                'time_unit' => 'min',
                'pickup_lat' => $booking[0]->pickup_lat,
                'pickup_long' => $booking[0]->pickup_long,
                'drop_lat' => $booking[0]->drop_lat,
                'drop_long' => $booking[0]->drop_long,
                'customer_name' => $customer_name,
                'mobile' => $mobile,
                'image' => $image,
                'booking_status' => $booking[0]->booking_status,
                'customer_id' => $customer_id,
                'booking_id' => $booking_id,
                'booking_type' => $booking[0]->booking_type,
                'is_reached'   =>$booking[0]->is_reached,
                'isSchedule'=>$booking[0]->schedule_booking
            );

            if($booking[0]->schedule_booking == 1){
                $datetime = date('D d M Y g:i A',strtotime($booking[0]->booking_time));
                $data['booking_time'] = $datetime;
            }

            $res = array("success" => true, "message" => "Trip Request", "data" => $data);
            return $res;
            exit;
        } else {
            $res = array("success" => false, "message" => "Booking Id Not found", "data" => array());
            return $res;

            exit;
        }
    }

    function getStatus($bookingId = '') {
        $res = Booking::select('booking_status')->where('id', $bookingId)->get();
        if (isset($res[0]->booking_status)) {
            return $res[0]->booking_status;
        } else {
            return 'no booking found';
        }
    }

    public function random_string($length = 4) {
        $str = "";
        $characters = range('1', '9');
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public function cancelBooking(Request $request) {

        $security_token = $request->header('stoken');
        if ($security_token == null || $security_token == '') {
            $data = array("success" => false, "message" => "Security token can not be blank");
            print_r(json_encode($data));
            exit;
        } else if ($security_token != '987654321') {
            $data = array("success" => false, "message" => "Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }

        $environment = "adhoc";
        $environment = $request->header('environment'); // type of server using for iosnotification
        if ($environment == null || $environment == '') {
            $environment = "adhoc";
        }

        $booking_id = $request->input('booking_id');
        if ($booking_id == null || $booking_id == '') {
            $data = array("success" => false, "message" => "Booking Id can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $canceled_by = $request->input('canceled_by');

        if ($canceled_by == null || $canceled_by == '') {
            $data = array("success" => false, "message" => "Canceled by name can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $status = "canceled";
        $cancel_time = date('Y-m-d H:i:s');

        $booking = Booking::select('start_time')->where('id', $booking_id)->first();
        if ($booking->start_time != '0000-00-00 00:00:00') {
            $data = array("success" => false, "message" => "You cant cancel Onride Booking");
            print_r(json_encode($data));
            exit;
        }

        $res = Booking::where('id', $booking_id)
                ->update(['booking_status' => $status, 'cancel_time' => $cancel_time, 'canceled_by' => $canceled_by]);
        if ($res == 1) {
            $data = array("success" => true, "message" => "Booking Canceled");
        } else {
            $data = array("success" => false, "message" => "Booking Id not matched");
        }

        $booking = Booking::where('id', $booking_id)->first();
        /* $driver_id = $booking->driver_id;
          $customer_id = $booking->customer_id;
          $booking_time =$booking->booking_time; */

        $driver_id = $booking['driver_id'];
        $customer_id = $booking['customer_id'];
        $booking_time = $booking['booking_time'];


        if (strtolower($canceled_by) == 'driver') {


            $customer = Customer::select('device_token', 'type')->where('id', $customer_id)->first();

            $token = $customer->device_token;
            $device_type = $customer->type;
            $name = $customer->name;


            $to = "customerapp";
            $message = "Booking cancelled by driver";
            $type = 3;

            $user_type = "driver";
        } else if (strtolower($canceled_by) == 'customer') {
            $driver = Driver::select('device_token', 'type')->where('id', $driver_id)->first();
            //get driver token
            $token = $driver->device_token;
            $device_type = $driver->type;
            $name = $driver->name;
            $to = "driverapp";
            $type = 4;
            $message = "Booking cancelled by customer";
            $user_type = "customer";
        }
        if ($user_type == 'driver') {
            $cancelcount = CancelCount::where('driver_id', $driver_id)->where('user_type', 'driver')->first();
        } else if ($user_type == 'customer') {
            $cancelcount = CancelCount::where('customer_id', $customer_id)->where('user_type', 'customer')->first();
        }

        
        if (!empty($cancelcount->cancel_count) && ($cancelcount->cancel_count>0)) {
            $count = $cancelcount->cancel_count + 1;
        } else {
            $count = 1;
        }

        if ($count >= 3) {
            $block_time = date('Y-m-d H:i:s');
        } else {
            $block_time = null;
        }

        if (!empty($cancelcount->cancel_count) && ($cancelcount->cancel_count>0)) {
            $id = $cancelcount->id;
            $obj = CancelCount::find($id);
            $obj->cancel_count = $count;
            $obj->block_time = $block_time;
            $obj->save();
            if ($count >= 3) {
                if ($user_type == 'driver') { //block driver
                    Driver::where('id', $driver_id)->update(['is_blocked' => 1]);
                } else if ($user_type == 'customer') { //block customer
                    Driver::where('id', $driver_id)->update(['is_blocked' => 1]);
                }
            }
        } else {
            $obj = new CancelCount;
            $obj->user_type = $user_type;
            if ($user_type == 'driver') {
                $obj->driver_id = $driver_id;
                $obj->customer_id = 0;
                $obj->cancel_count = $count;
                $obj->block_time = $block_time;
                $obj->save();
            } else if ($user_type == 'customer') {
                $obj->driver_id = 0;
                $obj->customer_id = $customer_id;
                $obj->cancel_count = $count;
                $obj->block_time = $block_time;
                $obj->save();
            }
        }



        //send notification 
        $msg = array
            (
            'body' => $message,
            'title' => 'Trip Cancel Notification'
        );
        $fields = array
            (
            'to' => $token,
            'notification' => $msg,
            'data' => array('bookingid' => $booking_id, 'type' => $type, 'message' => $message, 'booking_time' => $booking_time)
        );

        //echo $token;

        if ($device_type == "1") {
            $this->fcmNotification($msg, $fields, $to);
        } else {

            $this->iosNotification($msg, $fields, $to, $environment);
        }
        //end notification

        return $data;

        //need to add notification when driver or customer cancel booking
        exit;
    }

    //accept booking
    public function updateBookingStatus(Request $request) {
        $security_token = $request->header('stoken');
        if ($security_token == null || $security_token == '') {
            $data = array("success" => false, "message" => "Security token can not be blank");
            print_r(json_encode($data));
            exit;
        } else if ($security_token != '987654321') {
            $data = array("success" => false, "message" => "Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }

        $environment = "adhoc";
        $environment = $request->header('environment'); // type of server using for iosnotification
        if ($environment == null || $environment == '') {
            $environment = "adhoc";
        }

        $booking_id = $request->input('booking_id');
        if ($booking_id == null || $booking_id == '') {
            $data = array("success" => false, "message" => "Booking Id can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $driver_id = $request->input('driver_id');
        if ($driver_id == null || $driver_id == '') {
            $data = array("success" => false, "message" => "Driver Id can not be blank");
            print_r(json_encode($data));
            exit;
        }
        
        $driver_check = DB::table('drivers')->where('id' , '=' , $driver_id)->first();
          if(($driver_check->is_active=="0" || $driver_check->deleted_at!="")){
            $data =array('success'=>false ,'message'=>"Your profile is dactivated please contact to admin." );    
            print_r(json_encode($data));
            exit;
        } 

        $booking_status = $request->input('status');
        if ($booking_status == null || $booking_status == '') {
            $data = array("success" => false, "message" => "Booking status can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $remark = $request->input('remark');
        if ($remark != null || $remark != '') {
            $remark = $remark;
        } else {
            $remark = "";
        }

        $cancel_time = "";
        $start_time = "";
        $completed_time = "";
        $time = date('Y-m-d H:i:s');
        $dataArr = array('booking_id' => $booking_id, 'timestamp' => $time);

        $customer = DB::table('booking')->join('customers', 'booking.customer_id', '=', 'customers.id')->select('customers.device_token', 'customers.type','booking.schedule_booking')->where('booking.id', $booking_id)->first();
        $token = $customer->device_token;
        $device_type = $customer->type;
        $schedule_booking = $customer->schedule_booking;
        
        if ($booking_status == '0') {
            
            //Booking Rejected by driver
            /*
            $status = "canceled";
            Booking::where('id', $booking_id)
                    ->update(['booking_status' => $status, 'cancel_time' => $time, 'canceled_by' => "driver"]);

            //code to manage cancel trp by driver

            $driver_last_three_booking = Booking::select('booking_status')->where('driver_id', $driver_id)->where('canceled_by', 'driver')->orderBy('id', 'desc')->limit(3);
            $mark_driver_cancel = 1;
            foreach ($driver_last_three_booking as $key => $value) {
                $value->booking_status != 'Canceled';
                $mark_driver_cancel = 0;
            }


            $cancelcount = CancelCount::where('driver_id', $driver_id)->where('user_type', 'driver')->first();
           
            if (!empty($cancelcount->cancel_count) && ($cancelcount->cancel_count>0)) {
                $count = $cancelcount->cancel_count + 1;
            } else {
                $count = 1;
            }
            if ($count >= 3) {
                $block_time = date('Y-m-d H:i:s');
                Driver::where('id', $driver_id)->update(['is_blocked' => 1]);
            } else {
                $block_time = null;
            }
           if (!empty($cancelcount->cancel_count) && ($cancelcount->cancel_count>0)){
                $id = $cancelcount->id;
                $obj = CancelCount::find($id);
                $obj->cancel_count = $count;
                $obj->block_time = $block_time;
                $obj->save();
            } else {
                $obj = new CancelCount;
                $obj->user_type = "driver";
                $obj->driver_id = $driver_id;
                $obj->customer_id = 0;
                $obj->cancel_count = $count;
                $obj->block_time = $block_time;
                $obj->save();
            }
            */

            $data = array("success" => false, "message" => "Booking Cancelled By Driver", "data" => $dataArr);
            $title = "Trip Rejected";
            $message = "Trip Rejected By Driver";
            $type = "3";

            $msg = array('body' => $message, 'title' => $title);
            $fields = array
                (
                'to' => $token,
                'notification' => $msg,
                'data' => array('bookingid' => $booking_id, 'type' => $type, 'message' => "Rejected")
            );
        } else if ($booking_status == '1') {
            //when booking accepted   

            $type = "2";
            if($schedule_booking == 1){
                $status = "accept";    
            }else{
                $status = "in_progress";    
            } 
            
            $status_of_booking = Booking::where('id', $booking_id)->where('booking_status', '<>', 'pending')->get();
            if (isset($status_of_booking[0])) {
                $status = $status_of_booking[0]->booking_status;
                $data = array("success" => false, "message" => "Booking Already " . $status);
                print_r(json_encode($data));
                exit;
            }

            $vehicledata= Vehicle::select('id')->where('driver_id',$driver_id)->first();
            $vehicle_id = $vehicledata->id;

            Booking::where('id', $booking_id)
                    ->update(['booking_status' => $status, 'accept_time' => $time, 'driver_id' => $driver_id,'vehicle_id' => $vehicle_id]);
            $data = array("success" => true, "message" => "Booking Accepted By Driver", "data" => $dataArr);
            $title = "Trip Accepted";
            $message = "Trip Accepted By Driver";
            $msg = array('body' => $message, 'title' => $title);
            $fields = array
                (
                'to' => $token,
                'notification' => $msg,
                'data' => array('bookingid' => $booking_id, 'type' => $type, 'message' => "Accepted")
            );
        }


        //Set is_receive_notification 0 so that driver can receive notifi. again

        if($driver_id != ''){
            DB::table('drivers')->where('id',$driver_id)->update(['is_receive_notifi'=>0]);
        }


        //notification will send to customer when ride reject or accept
        if($booking_status == "1"){
            if ($device_type == "1") {
                $this->fcmNotification($msg, $fields, 'customerapp');
            } else {
               
                $this->iosNotification($msg, $fields, 'customerapp', $environment);
            }    
        }
        
        return $data;
        exit;
    }

    //check customer offer

    public function checkCustomerOfferExist(Request $request) {
        $customer_id = $request->customer_id;
        $offer_code = $request->offer_code;
        $security_token = $request->header('stoken');
        if ($security_token == null || $security_token == '') {
            $data = array("status" => false, "message" => "Security token can not be blank");
            return $data;
            exit;
        } else if ($security_token != '987654321') {
            $data = array("status" => false, "message" => "Please Add Correct Security Token");
            return $data;
            exit;
        }

        if ($customer_id == null || $customer_id == '') {
            $data = array("status" => false, "message" => "Customer Id can not be blank");
            return $data;
            exit;
        }

        if ($offer_code == null || $offer_code == '') {
            $data = array("status" => false, "message" => "Offer code can not be blank");
            return $data;
            exit;
        }
        $offer_exist = OfferUsedByCustomers::where('offer_code', '=', $offer_code)
                        ->where("customer_id", "=", $customer_id)
                        ->whereNull('deleted_at')
                        ->get();

        if (!empty($offer_exist[0]->offer_code)) {
            $today = date("Y-m-d h:i:s");
            $offerdays = DB::table('offer_codes')->select('plan_extends_for_days', 'amount', "used_limit",'percent')->where('offer_code', $offer_code)->where('start_date', '<=', $today)->where('end_date', '>=', $today)->get();
            if (!empty($offerdays[0]->amount) || !empty($offerdays[0]->percent)) {
                if ($offer_exist[0]->no_of_time_used <= $offerdays[0]->used_limit) {
                    $data = array("status" => true, "message" => "Offer is available for you.", "data" => array());
                    return $data;
                    exit;
                } else {
                    $data = array("status" => false, "message" => "you have exeeded the maximun limit for this user.", "data" => array());
                    return $data;
                    exit;
                }
            } else {
                $data = array("status" => false, "message" => "Offer code is not available.", "data" => array());
                return $data;
                exit;
            }
        } else {
            $data = array("status" => false, "message" => "Offer code is not available for you.", "data" => array());
            return $data;
            exit;
        }
    }

    //booking will completed by driver
    public function completeBooking(Request $request) {
        try{


        $security_token = $request->header('stoken');
        if ($security_token == null || $security_token == '') {
            $data = array("success" => false, "message" => "Security token can not be blank");
            return $data;
            exit;
        } else if ($security_token != '987654321') {
            $data = array("success" => false, "message" => "Please Add Correct Security Token");
            return $data;
            exit;
        }
       
        $environment = "adhoc";
        $environment = $request->header('environment'); // type of server using for iosnotification
        if ($environment == null || $environment == '') {
            $environment = "adhoc";
        }

        $booking_id = $request->input('booking_id');

        if ($booking_id == null || $booking_id == '') {
            $data = array("success" => false, "message" => "Booking Id can not be blank");
            return $data;
            exit;
        }


        $address = $request->input('address');
        if ($address == null || $address == '') {
            $address = " ";
        }

        $lat = $request->input('lat');
        if ($lat == null || $lat == '') {
            $data = array("success" => false, "message" => "Latitude can not be blank");
            return $data;
            exit;
            $lat = "";
        }

        $long = $request->input('long');
        if ($long == null || $long == '') {
            $data = array("success" => false, "message" => "Longitude can not be blank");
            return $data;
            exit;
            $long = "";
        }

        // $wait_time = $request->input('wait_time');
        // if ($wait_time == null || $wait_time == '') {
        //     $data = array("success" => false, "message" => "Waiting Time can not be Null");
        //     return $data;
        //     exit;
        //     $long = " ";
        // }
        
        //calculate actual distance
        $booking_cordinate = DriverLatLong::where('booking_id', $booking_id)->orderBy('created_at', 'asc')->select('id','latitude','longitude')->get();
        if(!empty($booking_cordinate) && count($booking_cordinate)>1){
            $old_lat =  $booking_cordinate[0]->latitude;
            $old_long =  $booking_cordinate[0]->longitude;
            $distance =0;
            for($i=0;$i<count($booking_cordinate);$i++){
                //distance($lat1, $lon1, $lat2, $lon2, $unit)
                $new_lat  =   $booking_cordinate[$i]->latitude;
                $new_long =   $booking_cordinate[$i]->longitude;
                $distance+=$this->distance($old_lat, $old_long, $new_lat, $new_long, "K");
                $old_lat =  $booking_cordinate[$i]->latitude;
                $old_long =  $booking_cordinate[$i]->longitude;
            } 
        } 
        else{
            $distance = 0;   
        }
        
        $distance =  round($distance,3);
        
        
        // get destination address on basis of lat long
        $google_api = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $long . "&sensor=true&key=AIzaSyCNHQsjbS828-SDaE2k-PCTzy0SDZr2O3k 	";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $google_api);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        $res_api = json_decode($result);
        $status = $res_api->status;
        if ($status == "OK") {
            $address_api = $res_api->results[0]->formatted_address;
        } else {
            $address_api = "";
        }

        $bookingData = Booking::select('offer_code', 'driver_id', 'customer_id', 'vehicle_id', 'pickup_lat', 'pickup_long', 'drop_lat', 'drop_long', 'discount_amount')->where('id', '=', $booking_id)->groupBy('customer_id')->first();
        
        if (!empty($bookingData['destination_address'])) {
            $address = $bookingData['destination_address'];
        } else {
            $address = $address_api;
        }

        $customer_id = $bookingData['customer_id'];
        $driver_id = $bookingData['driver_id'];
        $discount_amount = $bookingData['discount_amount'];
        $offer_code = $bookingData['offer_code'];


        $customerData = Customer::select('id', 'device_token', 'type')->where('id', '=', $customer_id)->first();

        $vehicleData = Vehicle::select('vehicle_category')->where('driver_id', '=', $driver_id)->first();

        $categoryData = Category::select('per_km_charges')->where('id', '=', $vehicleData['vehicle_category'])->first();


        $status = "completed";
        $complete_time = date('Y-m-d H:i:s');
        
        if (isset($categoryData['per_km_charges']) && $categoryData['per_km_charges'] != 0) {
            $per_km_charges =  $categoryData['per_km_charges'];
        } else {
            $per_km_charges = 14.49;
        }

 
        // if (isset($categoryData['basefare']) && $categoryData['basefare'] != 0) {
        //     $basefare = $categoryData['basefare'];
        // } else {
        //     $basefare = 20;
        // }
        

        $settings = Setting::get()->keyBy('key');
        // if(isset($settings['wait_charge_permin']->value) && ($settings['wait_charge_permin']->value!='') ){
        //     $per_min_charges = $settings['wait_charge_permin']->value;
        // }else{
        //     $per_min_charges = 2 ;
        // }

        if(isset($settings['base_km']->value) && ($settings['base_km']->value!='') ){
            $base_km = $settings['base_km']->value;
        }else{
            $base_km = 1 ;
        }
         
        
        // $time_min = 1;
        
        // $time_charges = 0;
       
        // $time = explode(':', $wait_time);
        // $min=0;

        // if(isset($time[0])){
        //     $min=$time[0];
        // }
        //  if(isset($time[1])){
        //     $min=$min+($time[1]/60);
        // }
        // $waiting_time =ceil($min);
        // $time_charges = $waiting_time * $per_min_charges;
        
 
        if ($distance <= $base_km) {
            // $distance_charges = $basefare;
            $actual_charges = $per_km_charges;
        } else {
            // $a= $distance - $base_km;         
            // $distance_charges = ($a * $per_km_charges) + $basefare;
            $actual_charges = ($distance * $per_km_charges);
        }
     
        // $actual_charges = $distance_charges + $time_charges;


        //CODE PASTE HERE
        $offer_days = 0;
        $offer_amount = 0;

        if(!empty($offer_code)){
                
            $today = date('Y-m-d');
            $offerdays = DB::table('offer_codes')
                ->select('plan_extends_for_days', 'amount','offerType','percentType','percent','used_limit')
                ->where('offer_code', $offer_code)
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->get();
            
            if (count($offerdays) > 0) {

                $offerdays = $offerdays[0];
                $offer_days = $offerdays->plan_extends_for_days;
            
                if($offerdays->offerType == 'FIXED'){
                    $offer_amount = $offerdays->amount;    
                }else{
                    if($offerdays->percentType == 'FLAT'){
                        $offer_amount = ($actual_charges * $offerdays->percent) / 100;
                    }else{
                        if($actual_charges >= $offerdays->amount){
                            $offer_amount = ($actual_charges * $offerdays->percent) / 100;
                        }
                    }
                }

                //update offer used by customer
                $already = db::table('offer_used_by_customers')
                            ->where('offer_code', $offer_code)
                            ->where('customer_id', $customer_id)
                            ->whereNull('deleted_at')
                            ->get();

                if (count($already) > 0) {
                    $usedId = $already[0]->id;
                    $already_used = $already[0]->no_of_time_used;

                    $obj = OfferUsedByCustomers::find($usedId);
                    $obj->no_of_time_used = $already_used + 1;
                    $obj->save();
                } else {
                    $obj = new OfferUsedByCustomers;
                    $obj->no_of_time_used = 1;
                    $obj->customer_id = $customer_id;
                    $obj->offer_code = $offer_code;
                    $obj->save();
                }

                //check if  used_limit == no_used _limit then soft delete
                $usedOffer = db::table('offer_used_by_customers')
                            ->select('id','no_of_time_used')
                            ->where('offer_code', $offer_code)
                            ->where('customer_id', $customer_id)
                            ->whereNull('deleted_at')
                            ->first();
  
                if($usedOffer){
                    if($offerdays->used_limit == $usedOffer->no_of_time_used){
                        DB::table('offer_used_by_customers')->where('id',$usedOffer->id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
                    }
                }
            }
        }
        

        $driverData = Driver::select('id', 'discount', 'allow_discount', 'device_token', 'type', 'total_offer_days', 'available_offer_days', 'referral_code')->where('id', '=', $driver_id)->first();

        $discount = $discount_amount + $offer_amount;


        /* $offer_days = 0;*/

        $final_amount = $actual_charges - $discount;

       
        if ($final_amount <= 0) {
            $final_amount = 0;
        } 
        
        $status = "payment_pending";
        $complete_time = date('Y-m-d H:i:s');
        $arr = array(
            'booking_status' => $status,
            'actual_distance' => $distance,
            'per_km_charge' => $per_km_charges,
            // 'basefare' => $basefare,
            'total_amount' => $actual_charges,
            'discount_amount' => $discount,
            'final_amount' => $final_amount,
            'destination_address' => $address,
            'drop_lat' => $lat,
            'drop_long' => $long,
            // 'waiting_charges' => $time_charges,
            // 'wait_time'      => $waiting_time,
            // 'wait_time_format'=>$wait_time
        );

        $res = Booking::where('id', $booking_id)->update($arr);
        
        if ($res == 1) {
            
            $driver_latlong = DriverLatLong::where('booking_id', $booking_id)->get();

            $single = DriverLatLong::where('booking_id', $booking_id)->orderBy('id', 'desc')->first();
            if (isset($single->latitude)) {
                $latitudes = $single->latitude;
            } else {
                $latitudes = '';
            }
            if (isset($single->longitude)) {
                $longitudes = $single->longitude;
            } else {
                $longitudes = '';
            }
            
           

            if (count($driver_latlong) > 0) {
                $booking_latlong = json_encode($driver_latlong);
                //print_r($booking_latlong);
                DriverLatLong::where('booking_id', $booking_id)->delete();
                $latlong = new DriverLatLong;
                $latlong->booking_id = $booking_id;
                $latlong->driver_id = $driver_id;
                $latlong->booking_latlong = $booking_latlong;
                $latlong->booking_id = $booking_id;
                $latlong->latitude = $latitudes;
                $latlong->longitude = $longitudes;
                $latlong->save();
            }
            
            $arr = unserialize(str_replace(array('NAN;','INF;'),'0;',serialize($arr)));
            $data = array("success" => true, "message" => "Booking Successfully completed", "data" => $arr);
            
            //notification to driver and customer when booking completed
            $to_driver = $driverData['device_token'];
            $to_customer = $customerData['device_token'];
            $driver_deviceType = $driverData['type'];
            $customer_deviceType = $customerData['type'];
            

            //die('111');
            $msg = array
                (
                'body' => 'Trip Completed',
                'title' => 'Your Trip Completed',
                'icon' => 'myicon', /* Default Icon */
                'sound' => 'mySound'/* Default sound */
            );

            $driver_fields = array
                (
                'to' => $to_driver,
                'data' => $msg,
                'data' => array('bookingid' => $booking_id, 'type' => '7', 'message' => "Ride Completed")
            );
            $customer_fields = array
                (
                'to' => $to_customer,
                'data' => $msg,
                'data' => array('bookingid' => $booking_id,"driver_id"=>$driver_id, 'type' => '7', 'message' => "Completed")
            );

            if ($customer_deviceType == "1") {
                $this->fcmNotification($msg, $customer_fields, 'customerapp');
                //echo $customer_deviceType; die();
            } else {
                $this->iosNotification($msg, $customer_fields, 'customerapp', $environment);
            }

            if ($driver_deviceType == "1") {
                $this->fcmNotification($msg, $driver_fields, 'driverapp');
            } else {
                $this->iosNotification($msg, $driver_fields, 'driverapp', $environment);
            }

        } else {
            $data = array("success" => false, "message" => "Booking Id not matched", "data" => array());
        }

        return $data;
        exit;
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }
    
    //function to calculate distance
      function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
        return $miles;
        }
    }
    //

    public function test_ios() {
        $customer_fields = array
            (
            'to' => 'dP7dhIos1H0:APA91bGlSRfPEnRCby5Q6SOvAlsxv04REv2Lm9KHsubqQ1QrsEnJBvJdVg8zoi9WlFS-0aS8HKQzRC63B7CD77KckwVSAQ-JjUJ62c46mPLbmmUELuLF0_YYR0CjXAL4PTfnTUINDXjp',
            'data' => "this is fortesting",
            'data' => array('bookingid' => '22', 'type' => '7', 'message' => "Completed")
        );

        //$this->iosNotification_test("Hello sumit",$customer_fields,'driverapp');
        $this->fcmNotification($msg, $fields, 'driverapp');
    }

    public function iosNotification($msg, $fields, $app, $environment) {
        
        //$fields['to']='ac2447286e2aa2f1f2e1d98d3d5dffb00b2630a3417221609cc8aaf3f86c91a4';
        $deviceToken = $fields['to'];
        // Put your private key's passphrase here:
        $passphrase = '';

        $message = $msg;
        // Put your alert message here:
        //$message = 'tttttMy first push notification Amit! This is holi offer this is holi offer This is holi offer this is holi offer This is holi offer this is holi offer This is holi offer this is holi offer';
        //echo $path = base_url().'uploads/Woosah_Apns.pem';die;
       
        if ($environment == 'dev') {
            $url = 'ssl://gateway.sandbox.push.apple.com:2195';
            $customer = 'Admin/CertificatesDevelopment.pem';
            $driver = 'Admin/CertificatesDriverDevelopment.pem';
        } else { // production 
            $url = 'ssl://gateway.push.apple.com:2195';
            $customer = 'Admin/CertificatesDevelopment.pem';
            $driver = 'Admin/CertificatesDriverDevelopment.pem';
        }



        if ($app == "customerapp") {
            // $path = 'Admin/CertificatesDevelopment.pem';
            //$path = 'Admin/CertificatesCustomerProduction.pem';
            $path = $customer;
        } elseif ($app == "driverapp") {
            //$path = 'Admin/CertificatesDriverDevelopment.pem';
            // $path = 'Admin/CertificatesDriverProduction.pem';
            $path = $driver;
        }
        
        
        

        //echo IOS_PEM.'CertificatesProduction.pem';
        //echo $base_url.'/pushnotiication_PEmfile/Certificates.pem';die;

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $path);
        //stream_context_set_option($ctx, 'ssl', '', $passphrase);
        stream_context_set_option($ctx, 'ssl', 'passphrase', '');
        // Open a connection to the APNS server


        $fp = stream_socket_client($url, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        //ssl://gateway.sandbox.push.apple.com:2195
        // $url = "ssl://gateway.push.apple.com:2195";
        // $fp = stream_socket_client($url, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

        //echo 'Connected to APNS' . PHP_EOL;
        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'badge' => 1, 'sound' => 'default', "fields" => $fields
        );
        //apns-expiration to set exipiration
        // Encode the payload as JSON
        $payload = json_encode($body);
        // Build the binary notification
        //$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        fclose($fp);
     //print_r($result);
        
    }

    public function testsocket() {

        $host = 'http://dev.tekzee.in/snap_rides';
        $ports = array(21, 2195, 80, 2196, 110, 443, 3306);
        foreach ($ports as $port) {
            $connection = @fsockopen($host, $port);
            if (is_resource($connection)) {
                echo '<h2>' . $host . ':' . $port . ' ' . '(' . getservbyport($port, 'tcp') . ') is open.</h2>' . "\n";
                fclose($connection);
            } else {
                echo '<h2>' . $host . ':' . $port . ' is not responding.</h2>' . "\n";
            }
        }
    }

    public function fcmNotificationTest() {
        define('API_ACCESS_KEY', 'AAAAuIvKrEM:APA91bEix38_XugJjJfB3HregtLFzg0xhMJ_0-PMvIzTca7SrdNFs2eBcKYAdq0lY0oiDKmW0ccDoMCsp-nNhkVT90kwJMpAxs2kCmjlcZebEK4VhQK9Qi1-aqIJcxpAc9dcIEebamsEZG5rUHdW1LIk-MDcBF5Xqg');
        $tk = "e2xxvcK4z0Y:APA91bHleGa03OlEiCY2Omu6LP7zqU8AJW9O4MIWzLbhRMtpR3q7Y4BCPIkowPhS3ybW2tfOaqpVwFwdh_KMmhXbZ94DmA43vVJ10RorRFpymDIcV-e_BR4fu1ylXcH5yg4kxgoFI2Wx";
        $msg = array
            (
            'body' => 'Upcoming Trip Request',
            'title' => 'Trip Notification',
            'icon' => 'myicon', /* Default Icon */
            'sound' => 'mySound'/* Default sound */
        );
        $fields = array
            (
            'to' => $tk,
            'notification' => $msg,
            'data' => array('bookingid' => 1, 'type' => 9, 'message' => 'jjjjj'),
            'android' => array('ttl' => '30'),
            'time_to_live' => 30
        );

        $headers = array
            (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        #Send Reponse To FireBase Server    

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        print_r($result);
        curl_close($ch);
    }

    public function fcmNotification($msg, $fields, $to) {
        #API access key from Google API's Console
        /*  define( 'API_ACCESS_KEY', 'YOUR-SERVER-API-ACCESS-KEY-GOES-HERE' ); */
        //$fields["timeToLive"]=60;

        if ($to == 'driverapp') {
            //define('API_ACCESS_KEY','');
            // $key = "AAAAuIvKrEM:APA91bEix38_XugJjJfB3HregtLFzg0xhMJ_0-PMvIzTca7SrdNFs2eBcKYAdq0lY0oiDKmW0ccDoMCsp-nNhkVT90kwJMpAxs2kCmjlcZebEK4VhQK9Qi1-aqIJcxpAc9dcIEebamsEZG5rUHdW1LIk-MDcBF5Xqg";
            
            $key = "AAAA9ySehbk:APA91bFNiawcfu4KAL81fn8_836pEJ0RVvtIvaUwISStvM0lSDX7HS8__KKgI4Dnr_SbAArC1SSHAVhqjhAmsriCA3r94Wl1mc7IQw-gu9bDFdInk7_FfoQ0Oh4YVCxbw30oYgY3_HSM";

        } else if ($to == 'customerapp') {
            //customer app
            // $key = "AAAAk5ejpl4:APA91bHrLr9l6rKfceoEK_lECAh3rkyQhFN9MthlN2u7EissI5UIt3UuWD3wf6bXyJBmt6OUfE32vTr67NT_nZmVXsKPDiEinXessPsLdM6W3mYj6aMi6yCj7aRjBbXuQLGfHkEdQX6iVKGUA5Xb5cZtRLOrBQhIlA";

            $key = "AAAAji3qdWk:APA91bGq1dWOjVHLiDZt9JXOorasxGtuAKyT49yjyHc0ShlNuptQ7KUNuf4k15dtWPg_ePXvgNCJdPGL6j7owl3qKROehPzbSXkInpGS_bTnNOMGy4yJYaH4jjQNgINgr9BJnnT7c8Uk";



        }
        //define('API_ACCESS_KEY',$key);  
        $headers = array
            (
            'Authorization: key=' . $key,
            'Content-Type: application/json'
        );
        #Send Reponse To FireBase Server    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        #Echo Result Of FireBase Server
    }

    public function getRideEstimate(Request $request) {
        $security_token = $request->header('stoken');
        if ($security_token == null || $security_token == '') {
            $status = false;
            $data = array("success" => false, "message" => "Security token can not be blank");
            print_r(json_encode($data));
            exit;
        } else if ($security_token != '987654321') {
            $data = array("success" => false, "message" => "Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }

        $pickup_lat = $request->input('pickup_lat');
        if ($pickup_lat == null || $pickup_lat == '') {
            $status = false;
            $data = array("success" => false, "message" => "Pickup latitude can not be blank");
            print_r(json_encode($data));
            exit;
        }
        $pickup_long = $request->input('pickup_long');
        if ($pickup_long == null || $pickup_long == '') {
            $status = false;
            $data = array("success" => false, "message" => "Pickup longitude can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $drop_lat = $request->input('drop_lat');
        if ($drop_lat == null || $drop_lat == '') {
            $status = false;
            $data = array("success" => false, "message" => "Drop latitude can not be blank");
            print_r(json_encode($data));
            exit;
        }
        $drop_long = $request->input('drop_long');
        if ($drop_long == null || $drop_long == '') {
            $status = false;
            $data = array("success" => false, "message" => "Drop longitude can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $vehicle_type = $request->input('vehicle_type');
        if ($vehicle_type == null || $vehicle_type == '') {
            $status = false;
            $data = array("success" => false, "message" => "vehicle type can not be blank");
            print_r(json_encode($data));
            exit;
        }


        $cat = VehicleCategory::where('id', $vehicle_type)->first();

        // if (isset($cat->basefare)) {
        //     $basefare = $cat->basefare;
        // } else {
        //     $basefare = 20;
        // }

        if (isset($cat->per_km_charges)) {
            $per_km_chargs = $cat->per_km_charges;
        } else {
            $per_km_chargs = 14.49;
        }

        /* if($driver_id!=null ||  $driver_id!=''){
          $vehicle = Vehicle::select('vehicle_category','per_km_charge')->where('driver_id',$driver_id)->first();
          if(isset($vehicle->per_km_charge)){
          $per_km_chargs = $vehicle->per_km_charge;
          }else{
          $per_km_chargs = 20;
          }
          $catId = $vehicle->vehicle_category;
          $vehicle_cat = VehicleCategory::select('basefare')->where('id',$catId)->first();
          if(isset($vehicle_cat->basefare)){
          $basefare=$vehicle_cat->basefare;
          }else{
          $basefare = 50;
          }
          }else{
          $per_km_chargs = 20;
          $basefare = 50;
          } */


        $pick = $pickup_lat . ',' . $pickup_long;
        $drop = $drop_lat . ',' . $drop_long;

        $api = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$pick&destinations=$drop&key=AIzaSyBnTKkK26b0bwrCOU8XMoqzpUMVrHnf554";

        //die();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($result);
        //return $result;
        if (isset($data->rows[0]->elements[0]) && isset($data->rows[0]->elements[0]->distance)) {
            $distance = $data->rows[0]->elements[0]->distance;
        } else {
            $distance = 0;
        }

        if (isset($data->rows[0]->elements[0]) && isset($data->rows[0]->elements[0]->duration)) {
            $duration = $data->rows[0]->elements[0]->duration;
        } else {
            $duration = 0;
        }

        if (isset($duration->value)) {
            $time_min = round(($duration->value) / 60);
        } else {
            $time_min = 0;
        }

        if (isset($distance->value)) {
            $km = round((($distance->value) / 1000), 1);
        } else {
            $km = 0;
        }

        // $time_charges = 0;

        $settings = Setting::get()->keyBy('key');
        // if(isset($settings['wait_charge_permin']->value) && ($settings['wait_charge_permin']->value!='') ){
        //     $per_min_charges = $settings['wait_charge_permin']->value;
        // }else{
        //     $per_min_charges = 2 ;
        // }

        if(isset($settings['base_km']->value) && ($settings['base_km']->value!='') ){
            $base_km = $settings['base_km']->value;
        }else{
            $base_km = 1 ;
        }
        
        //$time_charges = $time_min*$per_min_charges;
        // $time_charges = 0;
        //km charges calculation
        if ($km <= $base_km) {
            // $distance_charges = $basefare;
            $total_charges = $per_km_chargs;
        } else {
            // $distance_charges = (($km - $base_km) * $per_km_chargs) + $basefare;
            $total_charges = $km * $per_km_chargs;
        }
        // $total_charges = $distance_charges + $time_charges;

        //end here
        //+- 20 amount
        $next = $total_charges + 20;
        $charges = number_format($total_charges) . '-' . number_format($next);

        //
        $data = array('estimated_charges' => $charges, 'estimated_time' => $time_min, 'distance' => $km);

        $res = array('success' => true, 'message' => 'Ride Estimates', 'data' => $data);
        return $res;
        //$km =$distance->text;
        //$data->rows[0]->elements[0]->duration;
    }

    public function varifyBooking(Request $request) {
        $security_token = $request->header('stoken');
        if ($security_token == null || $security_token == '') {
            $status = false;
            $data = array("success" => false, "message" => "Security token can not be blank");
            print_r(json_encode($data));
            exit;
        } else if ($security_token != '987654321') {
            $data = array("success" => false, "message" => "Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }

        $environment = "adhoc";
        $environment = $request->header('environment'); // type of server using for iosnotification
        if ($environment == null || $environment == '') {
            $environment = "adhoc";
        }

        $booking_id = $request->input('booking_id');
        if ($booking_id == null || $booking_id == '') {
            $status = false;
            $data = array("success" => false, "message" => "Booking Id can not be blank");
            print_r(json_encode($data));
            exit;
        }
        $otp = $request->input('otp');
        if ($otp == null || $otp == '') {
            $status = false;
            $data = array("success" => false, "message" => "OTP can not be blank");
            print_r(json_encode($data));
            exit;
        }
        $type = $request->input('type');
        if ($type == null || $type == '') {

            $data = array("success" => false, "message" => "Type can not be blank");
            print_r(json_encode($data));
            exit;
        }


        if ($otp != "2580") {
            $booking = Booking::select('id', 'drop_lat', 'drop_long', 'customer_id', 'booking_time')->where('id', $booking_id)->where('otp', $otp)->get();
        } else {
            $booking = Booking::select('id', 'drop_lat', 'drop_long', 'customer_id', 'booking_time')->where('id', $booking_id)->get();
        }

        if (count($booking) > 0) {
            $booking = $booking[0];

            $start_time = date('Y-m-d H:i:s');
            $res = Booking::where('id', $booking_id)->update(['booking_status' => 'in_progress', 'start_time' => $start_time]);
            //notification to driver that ur booking 
            $customer_id = $booking['customer_id'];
            $booking_time = $booking['booking_time'];

            $customerData = Customer::select('device_token', 'type')->where('id', $customer_id)->first();

            $token = $customerData['device_token'];
            $device_token = $customerData['device_token'];


            $msg = array('body' => "Ride Started.", 'title' => "Your Booking Started");

            $msgios = array('body' => "Ride Started.", 'title' => "Your Booking Started", 'data' => array('bookingid' => $booking_id, 'type' => '6', 'message' => 'Started', 'booking_time' => $booking_time));

            $msg1 = array
                (
                'body' => 'Ride Started.',
                'title' => 'Your Booking Started',
                'icon' => 'myicon', /* Default Icon */
                'sound' => 'mySound'/* Default sound */
            );





            $fields = array
                (
                'to' => $token,
                'notification' => $msg,
                'data' => array('bookingid' => $booking_id, 'type' => '6', 'message' => 'Started', 'booking_time' => $booking_time)
            );
            //print_r(json_encode($fields)); //die();
            //echo $device_token;die();

            if ($customerData->type == "1") {
                $this->fcmNotification($msg, $fields, 'customerapp');
            } else {
                $this->iosNotification($msg1, $fields, 'customerapp', $environment);
            }
            //$this->ios($msgios,$token);
            //

            $data = array("success" => true, "message" => "OTP Verified", "data" => $booking);
            print_r(json_encode($data));
        } else {
            $data = array("success" => false, "message" => "Incorrect OTP ");
            print_r(json_encode($data));
        }
    }

    public function testios() {
        $body['aps'] = array(
            'alert' => "helllo",
            'badge' => 1, 'sound' => 'default'
        );
        //'data'=>array('bookingid'=>'1','type'=>'6','message'=>'Started','booking_time'=>'1')
        $token = '517b39f1a75669c21f44bf99875e15f38a8b474c179f1c8b469a750948041d38';
        //$this->iosNotification($body,$token);
        $this->iosNotification();
    }

    public function richedOnPickup(Request $request) {

        $security_token = $request->header('stoken');
        if ($security_token == null || $security_token == '') {
            $status = false;
            $data = array("success" => false, "message" => "Security token can not be blank");
            print_r(json_encode($data));
            exit;
        } else if ($security_token != '987654321') {
            $data = array("success" => false, "message" => "Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }

        $customer_id = $request->input('customer_id');
        if ($customer_id == null || $customer_id == '') {
            $status = false;
            $data = array("success" => false, "message" => "Customer Id can not be blank");
            print_r(json_encode($data));
            exit;
        }
        $booking_id = $request->input('booking_id');
        if ($booking_id == null || $booking_id == '') {
            $status = false;
            $data = array("success" => false, "message" => "Booking Id can not be blank");
            print_r(json_encode($data));
            exit;
        }
        $booking_update = Booking::where('id', $booking_id)->update(array("is_reached"=>"1"));
        $booking = Booking::where('id', $booking_id)->first();
        $booking_time = $booking['booking_time'];

        $data_cust = Customer::select('device_token','type')->where('id', $customer_id)->first();
        $token = $data_cust['device_token'];
        
        $msg = array
            (
            'body' => 'Driver reached On Pickup',
            'title' => 'Driver reached'
        );
        $fields = array
            (
            'to' => $token,
            'notification' => $msg,
            'data' => array('type' => '5', 'message' => "Reached", "booking_time" => $booking_time,'bookingid'=>$booking_id)
        );
        //echo $token;
        // echo $data_cust['type']; die();
        if($data_cust['type']==1){
            $this->fcmNotification($msg, $fields, 'customerapp');
        }
        else{
            
          //$environment = $request->header('environment'); 
            $environment = 'adhoc'; 
            //$environment = 'dev';
          $this->iosNotification($msg, $fields, 'customerapp', $environment);  
        }
        $res = array('success' => true, 'message' => 'Driver Riched', 'data' => array());
        return $res;
    }

    public function bookingInvoice(Request $request) {

        $booking_id = $request->input('booking_id');
        if ($booking_id == null || $booking_id == '') {
            $status = false;
            $data = array("success" => false, "message" => "Booking Id can not be blank");
            print_r(json_encode($data));
            exit;
        }



        $invoice = DB::table('booking')
                        ->join('customers', 'booking.customer_id', '=', 'customers.id')
                        ->join('drivers', 'booking.driver_id', '=', 'drivers.id')
                        ->leftJoin('driver_rating', 'drivers.id', '=', 'driver_rating.driver_id')
                        ->leftJoin('vehicles', 'vehicles.driver_id', '=', 'drivers.id')
                        ->leftJoin('vehicle_category', 'vehicle_category.id', '=', 'vehicles.vehicle_category')
                        ->select(DB::raw('avg(driver_rating.rating) as rating'), 'booking.booking_status', 'booking.pickup_addrees', 'booking.destination_address', 'booking.actual_distance', 'booking.discount_amount', 'booking.tax_amount', 'booking.total_amount', 'booking.final_amount', 'booking.per_km_charge', 'vehicle_category.basefare', 'customers.image', 'customers.name', 'drivers.name as driver_name','drivers.id as driver_id', 'drivers.profile_image as driver_image', 'booking.start_time', 'booking.completed_time', 'vehicle_name', 'registration_number', 'vehicle_category.name as vehicle_category', 'vehicle_category.image as category_image', 'booking.wait_time','booking.wait_time_format')
                        ->where('booking.id', $booking_id)->get();
        
        $actual_time = 0;
        if ($invoice[0]->booking_status == 'completed') {
            $actual_time = strtotime($invoice[0]->completed_time) - strtotime($invoice[0]->start_time);
            if ($actual_time != 0) {
                $actual_time = round($actual_time / 60);
            }
        } else {
            $actual_time = 0;
        }

        $basepath = url('/');
        $destinationPath = 'Admin/customerimg';
        $image = $basepath . '/' . $destinationPath . '/' . 'noimage.png';
        if (isset($invoice[0]->image)) {
            $path = $basepath . '/' . $destinationPath . '/' . $invoice[0]->image;
            //&& (file_exists($path))
            if ($invoice[0]->image != '') {
                $image = $path;
            }
        }

        $driverImgPath = 'Admin/profileImage';
        $driver_image = $basepath . '/' . $driverImgPath . '/' . 'noimage.png';

        if (isset($invoice[0]->driver_image)) {
            $path = $basepath . '/' . $driverImgPath . '/' . $invoice[0]->driver_image;

            if ($invoice[0]->driver_image != '') {
                $driver_image = $path;
            }
        }


        #########################
        $categoryImagePath = "Admin/categoryImage";

        $categoryImage = $basepath . '/' . $categoryImagePath . '/' . 'noimage.png';

        if (isset($invoice[0]->category_image)) {
            $path = $basepath . '/' . $categoryImagePath . '/' . $invoice[0]->category_image;

            if ($invoice[0]->category_image != '') {
                $category_image = $path;
            }
        }

        if ($invoice[0]->rating == null) {
            $invoice[0]->rating = 0;
        }

        $final_amount = $invoice[0]->final_amount;
        

        $settings = Setting::get()->keyBy('key');

        // if(isset($settings['wait_charge_permin']->value) && ($settings['wait_charge_permin']->value!='') ){
        //     $per_min_charges = $settings['wait_charge_permin']->value;
        // }else{
        //     $per_min_charges = 2 ;
        // }


        $data = (object) [];
        if (isset($invoice[0])) {
            
            // if(isset($invoice[0]->wait_time) && $invoice[0]->wait_time!=null){
            //     $waiting_charges = ($invoice[0]->wait_time*$per_min_charges);
            // }else{
            //     $waiting_charges = 0;
            // }
            // $waiting_charges =$waiting_charges. ' R';
            
            // $final_amount = ($final_amount == 0) ? $invoice[0]->basefare : $final_amount;
            $final_amount = ($final_amount == 0) ? $invoice[0]->per_km_charge : $final_amount;


            $data = (object) array(
                    'booking_status'=>$invoice[0]->booking_status,
                    'pickup_addrees'=>$invoice[0]->pickup_addrees,
                    'destination_address'=>$invoice[0]->destination_address,
                    'total_amount'=>$invoice[0]->total_amount,
                    'final_amount'=>$final_amount,
                    'customer_name'=>$invoice[0]->name,
                    'customer_image'=>$image,
                    'driver_image'=>$driver_image,
                    'driver_name'=>$invoice[0]->driver_name,
                    'driver_rating'=>$invoice[0]->rating,
                    'time'=>$actual_time.' min',
                    'vehicle_name'=>$invoice[0]->vehicle_name,
                    'registration_number'=>$invoice[0]->registration_number,
                    'vehicle_category'=>$invoice[0]->vehicle_category,
                    'category_image'=>$category_image,
                    // 'wait_time' =>$invoice[0]->wait_time_format,
                    // 'waiting_charges' =>$waiting_charges,
                    'currency_symbol'=>'R',
                    'driver_id'=>$invoice[0]->driver_id

                    );
            if(isset( $invoice[0]->per_km_charge)){
                $per_km_charge= $invoice[0]->per_km_charge;
            }else{
                $per_km_charge= 0;
            }
            
            
            if(isset($settings['base_km']->value) && ($settings['base_km']->value!='') ){
                $base_km = $settings['base_km']->value;
             }else{
                $base_km = 1 ;
            }
            
            $billingdetails = array(
                // array('key' => 'Base Fare', 'value' => "R ".$invoice[0]->basefare),        
                array('key' => 'Base Fare', 'value' => "R ".$per_km_charge),        
                array('key' => 'Distance', 'value' => $invoice[0]->actual_distance . ' km'),
            );
            $data->billing_details = $billingdetails;
        }
        $res = array('success' => true, 'message' => 'Booking Invoice', 'data' => $data);
        return $res;
    }
/////////////////////////////////////SCHEDULE BOOKING START///////////////////////////////////////
    
    
    //Schedule booking list for CM
    public function getScheduleBookings(Request $request)
    {
        try{
            $security_token = $request->header('stoken');
            if ($security_token == null || $security_token == '') {
                $status = false;
                $data = array("success" => false, "message" => "Security token can not be blank", "data" => $obj);
                return $data;
                exit;
            } else if ($security_token != '987654321') {
                $data = array("success" => false, "message" => "Please Add Correct Security Token", "data" => $obj);
                return $data;
                exit;
            }
            
            $obj = (object) [];

            
            $input=$request->all();

            $validator=Validator::make($input,[
                       'customer_id' =>'required'
                       ]);
            if($validator->fails()){
               $data = array("success" => false, "message" => $validator->errors()->first(), "data" => $obj);
               return $data;exit;
            }
            $customer_id = $input['customer_id'];
            $filter = isset($input['filter']) ? $input['filter'] : '';
                
            $rides = DB::table('booking')

            ->select('booking.id as booking_id','booking.booking_status','pickup_addrees','destination_address','start_time','booking_time')
            ->where('booking.customer_id',$customer_id)
            ->whereIn('booking_status', ['completed','accept','canceled'])
            ->where('schedule_booking',1)
            ->where(function($query) use ($filter) {
                if (!empty($filter)) {
                    $query -> where('booking_status' , '=' , $filter);
                }
            })
            ->orderBy('booking.id', 'desc')
            ->get();   

            if(count($rides)>0){
                
                foreach ($rides as $key => $value) {
                    
                    $booking_time=date('D d M Y g:i A',strtotime($value->booking_time));
                    $value->booking_time  = $booking_time;
                   
                    $time = $value->start_time;
           
                    if($value->booking_status=="in_progress"){

                        if($time!='0000-00-00 00:00:00'){
                             $value->booking_status='onride';
                        }else{
                             $value->booking_status='accepted';
                        }
                    }
                    
                    if($value->booking_status=="canceled"){
                       $value->booking_status = "cancelled"; 
                    }
                    unset($value->start_time);
                }
                $data = array("success" => true, "message" => "Schedule booking data", "data" => $rides);
                return $data;
            }else{
                $data = array("success" => false, "message" => "No schedule booking found", "data" => $obj);
                return $data;
            }
        }catch(Excption $e){
            $data = array("success" => false, "message" => "Something went wrong", "data" => $obj);
                return $data;            
        }
                    
    }

    //get Schedule booking detail for CM
    public function scheduleBookingDetailCM(Request $request)
    {   
        try{
            $security_token = $request->header('stoken');
            if ($security_token == null || $security_token == '') {
                $status = false;
                $data = array("success" => false, "message" => "Security token can not be blank", "data" => $obj);
                return $data;
                exit;
            } else if ($security_token != '987654321') {
                $data = array("success" => false, "message" => "Please Add Correct Security Token", "data" => $obj);
                return $data;
                exit;
            }
            
            $obj = (object) [];

            
            $input=$request->all();

            $validator=Validator::make($input,[
                       'booking_id' =>'required'
                       ]);
            if($validator->fails()){
               $data = array("success" => false, "message" => $validator->errors()->first(), "data" => $obj);
               return $data;exit;
            }
            $booking_id = $input['booking_id'];
            
            $onride = DB::table('booking')
                                ->where('booking.id',$booking_id)
                                ->where('booking_status', 'in_progress')
                                ->where('schedule_booking',1)
                                ->first();
            if(!empty($onride)){
                $data = array("success" => false, "message" => "Booking already in progress", "data" => $obj);
                return $data;
                exit;
            }                    



            $ride = DB::table('booking')
            ->join('drivers', 'booking.driver_id', '=', 'drivers.id') 
            ->leftjoin('vehicles','vehicles.driver_id','=','drivers.id')
           
            ->leftjoin('vehicle_category','vehicles.vehicle_category','=','vehicle_category.id')
            ->leftjoin('customers','booking.customer_id','=','customers.id')
            ->select('booking.id as booking_id','booking.driver_id','booking.booking_status','customers.name as customer_name','drivers.name as driver_name','final_amount','pickup_addrees','destination_address','pickup_lat','pickup_long','drop_lat','drop_long','booking_time','completed_time','start_time', 'customers.image as customer_image','customers.mobile','drivers.mobile as driver_mobile','drivers.profile_image as driver_image', 'vehicle_name','vehicle_category.name as vehicle_category','registration_number','vehicles.vehicle_image','booking.otp')
            ->where('booking.id',$booking_id)
            ->whereIn('booking_status', ['completed','accept','canceled'])
            ->where('schedule_booking',1)
            ->first();    
              
            if(count($ride)>0){
                
                // foreach ($rides as $value) {
                    $booking_time=date('D d M Y g:i A',strtotime($ride->booking_time));
                    $ride->booking_time  = $booking_time;
                    if($ride->completed_time==null){
                         $ride->completed_time ='0000-00-00 00:00:00';
                    }
                    $destinationPath =  url('/').'/Admin/customerimg/';
                    $image=$destinationPath."noimage.png";
                    if($ride->customer_image!=''){
                        $image=$destinationPath.$ride->customer_image;
                    }
                    $ride->customer_image = $image;

                    $destinationPath =  url('/').'/Admin/profileImage/';
                    $image=$destinationPath."noimage.png";
                    if($ride->driver_image!=''){
                            $image=$destinationPath.$ride->driver_image;
                    }
                    $ride->driver_image = $image;



                    $destinationPath =  url('/').'/Admin/vehicleImage/';
                    $image=$destinationPath."noimage.png";
                    if($ride->vehicle_image!=''){
                        $image=$destinationPath.$ride->vehicle_image;
                    }
                    $ride->vehicle_image = $image;
                    
                    if($ride->vehicle_category==null){
                        $ride->vehicle_category ="No category Selected";
                    }else{
                        $ride->vehicle_category = $ride->vehicle_category;
                    }
                    
                    $time = $ride->start_time;
           
                    if($ride->booking_status=="in_progress"){

                        if($time!='0000-00-00 00:00:00'){
                             $ride->booking_status='onride';
                        }else{
                             $ride->booking_status='accepted';
                        }
                    }
                    
                    if($ride->booking_status=="canceled"){
                       $ride->booking_status = "cancelled"; 
                    }

                    //calculate estimated fare
                    $estcharge = $this->calculateEstFare($ride);
                    $ride->estimate_charges = $estcharge;
                // }
                $data = array("success" => true, "message" => "Schedule booking data", "data" => $ride);
                return $data;
            }else{
                $data = array("success" => false, "message" => "No schedule booking found", "data" => $obj);
                return $data;
            }
        }catch(Excption $e){
            $data = array("success" => false, "message" => "Something went wrong", "data" => $obj);
                return $data;            
        }
                    
    }


    public function calculateEstFare($ride)
    {
        $driver_id = $ride->driver_id;
        $pickup_lat = $ride->pickup_lat;
        $pickup_long = $ride->pickup_long;
        $drop_lat = $ride->drop_lat;
        $drop_long = $ride->drop_long;
        $vehicle = Vehicle::where('driver_id',$driver_id)->select('vehicle_category')->first();

        $vehicle_type = 1;
        if($vehicle){
            $vehicle_type = $vehicle->vehicle_category;
        }
        
        $cat = VehicleCategory::where('id', $vehicle_type)->first();

        // if (isset($cat->basefare)) {
        //     $basefare = $cat->basefare;
        // } else {
        //     $basefare = 20;
        // }

        if (isset($cat->per_km_charges)) {
            $per_km_chargs = $cat->per_km_charges;
        } else {
            $per_km_chargs = 14.49;
        }

        $pick = $pickup_lat . ',' . $pickup_long;
        $drop = $drop_lat . ',' . $drop_long;

        $api = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$pick&destinations=$drop&key=AIzaSyBnTKkK26b0bwrCOU8XMoqzpUMVrHnf554";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($result);
        //return $result;
        if (isset($data->rows[0]->elements[0]) && isset($data->rows[0]->elements[0]->distance)) {
            $distance = $data->rows[0]->elements[0]->distance;
        } else {
            $distance = 0;
        }

        if (isset($data->rows[0]->elements[0]) && isset($data->rows[0]->elements[0]->duration)) {
            $duration = $data->rows[0]->elements[0]->duration;
        } else {
            $duration = 0;
        }

        if (isset($duration->value)) {
            $time_min = round(($duration->value) / 60);
        } else {
            $time_min = 0;
        }

        if (isset($distance->value)) {
            $km = round((($distance->value) / 1000), 1);
        } else {
            $km = 0;
        }

        // $time_charges = 0;

        $settings = Setting::get()->keyBy('key');
        // if(isset($settings['wait_charge_permin']->value) && ($settings['wait_charge_permin']->value!='') ){
        //     $per_min_charges = $settings['wait_charge_permin']->value;
        // }else{
        //     $per_min_charges = 2 ;
        // }

        if(isset($settings['base_km']->value) && ($settings['base_km']->value!='') ){
            $base_km = $settings['base_km']->value;
        }else{
            $base_km = 1 ;
        }

        //$time_charges = $time_min*$per_min_charges;
        // $time_charges = 0;
        //km charges calculation
        if ($km <= $base_km) {
            // $distance_charges = $basefare;
            $total_charges = $per_km_chargs;
        } else {
            // $distance_charges = (($km - $base_km) * $per_km_chargs) + $basefare;
            $total_charges = ($km * $per_km_chargs);
        }
        // $total_charges = $distance_charges + $time_charges;

        //end here
        //+- 20 amount
        $next = $total_charges + 20;
        $charges = number_format($total_charges) . '-' . number_format($next);

        return $charges; 
    }


    public function createScheduleBookings(Request $request)
    {
        $security_token = $request->header('stoken');
        $obj = (object) [];
        if ($security_token == null || $security_token == '') {
            $status = false;
            $data = array("success" => false, "message" => "Security token can not be blank", "data" => $obj);
            return $data;
            exit;
        } else if ($security_token != '987654321') {
            $data = array("success" => false, "message" => "Please Add Correct Security Token", "data" => $obj);
            return $data;
            exit;
        }

        $environment = "adhoc";
        $environment = $request->header('environment'); // type of server using for iosnotification
        if ($environment == null || $environment == '') {
            $environment = "adhoc";
        }

        $input=$request->all();

        $validator=Validator::make($input,[
                   'customer_id' =>'required',
                   'vehicle_type' =>'required',
                   'pickup_lat' =>'required',
                   'pickup_long' =>'required',
                   'booking_date'=>'required',
                   'booking_time'=>'required'

                   ]);
        if($validator->fails()){
           $data = array("success" => false, "message" => $validator->errors()->first(), "data" => $obj);
           return $data;exit;
        }
        $customer_id = $request->input('customer_id');
        $vehicle_type = $request->input('vehicle_type');
        $pickup_lat = $request->input('pickup_lat');
        $pickup_long = $request->input('pickup_long');
        $drop_lat = $request->input('drop_lat');
        $drop_long = $request->input('drop_long');
        $pickup_addrees = $request->input('pickup_addrees');
        $destination_address = $request->input('destination_address');
        $booking_date = $request->input('booking_date');
        $booking_time = $request->input('booking_time');


        //check CM is active
        $customer_status = DB::table('customers')->where("status","=","1")->where("id","=",$customer_id)->first();
        if(empty($customer_status->name)){
             $status = false;
             $data = array("success" => false, "message" => "Your profile has been dactivated please contact to admin.", "data" => $obj);
             return $data;
             exit;
        }

        //  0 => normail booking, 1 => offer booking
        $booking_type = $request->input('booking_type');
        if ($booking_type == null || $booking_type == '') {
            $booking_type = 0; 
        }

        $offer_code = $request->input('offer_code');
        if ($offer_code == null || $offer_code == '') {
            $offer_code = '';
        }

        if ($destination_address == null || $destination_address == '') {
            $destination_address = "NA";
        }
        
        date_default_timezone_set ("Africa/Johannesburg") ;
        if(empty($booking_time)){
            $booking_datetime = date('Y-m-d G:i:s');         
        }else{
            $date = date('Y-m-d',strtotime($booking_date));
            $time = date('G:i:s',strtotime($booking_time));
            $datetime = $date.''.$time;
            $booking_datetime = date('Y-m-d G:i:s',strtotime($datetime));
        }
        
        $pickup_lat_long = $pickup_lat . ',' . $pickup_long;

        if ($drop_long != null || $drop_long != '') {

            $destinations_lat_long = $drop_lat . ',' . $drop_long;
            $api = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$pickup_lat_long&destinations=$destinations_lat_long&key=AIzaSyBnTKkK26b0bwrCOU8XMoqzpUMVrHnf554";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $api);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($result);

            $duration = 0;
            $distance = 0;
            if (isset($data->rows[0]->elements[0]->distance->value)) {
                
                $distance = ($data->rows[0]->elements[0]->distance->value) / 1000;
            } else {
                $distance = 0;
            }

            if (isset($data->rows[0]->elements[0]->duration->value)) {
                $duration = ($data->rows[0]->elements[0]->duration->value) / 60;
            } else {
                $duration = 0;
            }
        } else {
            $distance = 0;
            $duration = 0;
            $drop_lat = 0;
            $drop_long = 0;
        }

        $otp = $this->random_string();

        $booking = new Booking;
        $booking->customer_id = $customer_id;

        $booking->pickup_lat = $pickup_lat;
        $booking->pickup_long = $pickup_long;
        $booking->drop_lat = $drop_lat;
        $booking->drop_long = $drop_long;

        $booking->booking_time = $booking_datetime;
        $booking->pickup_addrees = $pickup_addrees;

        $booking->destination_address = $destination_address;
        $booking->booking_status = 'pending';
        $booking->otp = $otp;
        $booking->estimated_distance = $distance;
        $booking->estimated_time = $duration;
        $booking->offer_code = $offer_code;
        $booking->booking_type = $booking_type;
        $booking->schedule_booking = 1;
        $booking->save();
        $bookingId = $booking->id;

        //send OTP to CM
        $customer_det = Customer::where('id', $customer_id)->get();
        $mobilearray = array($customer_det[0]->mobile);
        $msg = "Your otp is " . $otp;
        $res = commonSms($mobilearray, $msg);

        $sql = "SELECT
                drivers.id as driver_id,drivers.device_token,drivers.type,
                111.045 * DEGREES(
                ACOS(
                COS(RADIANS($pickup_lat)) * COS(
                RADIANS(driver_latlong.latitude)
                ) * COS(
                RADIANS($pickup_long) - RADIANS(driver_latlong.longitude)
                ) + SIN(RADIANS($pickup_lat)) * SIN(
                RADIANS(driver_latlong.latitude)
                )
                )
                ) AS distance_in_km
                FROM driver_latlong
                INNER JOIN drivers ON driver_latlong.driver_id = drivers.id
                LEFT JOIN vehicles on  vehicles.driver_id=drivers.id 
                LEFT JOIN vehicle_category ON vehicles.vehicle_category = vehicle_category.id
                WHERE drivers.is_active = '1' AND drivers.deleted_at IS NULL AND
                drivers.is_online = 1 AND drivers.device_token IS NOT NULL AND 
                drivers.is_receive_notifi = 0 AND vehicles.vehicle_category = $vehicle_type AND
                driver_latlong.booking_id = 0 AND 
                driver_latlong.id IN (
                SELECT MAX(id) FROM driver_latlong GROUP BY driver_latlong.id) AND
                drivers.id NOT IN(
                SELECT driver_id FROM booking WHERE booking_status = 'in_progress')
                GROUP BY drivers.id
                HAVING distance_in_km <= 5
                ORDER BY distance_in_km ASC";
        
        $results = DB::select( DB::raw($sql));                   
        
        $timeout = 30;
        $setting = Setting::where([['key','driver_request_timeout'],['is_active',1]])->first(); 
        if(!empty($setting)){
            $timeout = $setting->value;
        }

        if(!empty($results)){

            $msg = array(
                    'body' => 'Upcoming Trip Request',
                    'title' => 'Trip Notification',
                    'icon' => 'myicon', // Default Icon
                    'sound' => 'mySound'// Default sound
            );
            foreach ($results as $driver) {
                $token = '';
                if ($driver->type == "1"){
                   $token = $driver->device_token;
                   $fields = array(
                        'to'=>$token,
                        'notification' => $msg,
                        'data' => array('bookingid' => $bookingId, 'type' => '1', 'message' => 'booking_request'),
                        'android' => array('ttl' => (String)$timeout),
                        'time_to_live' => (int)$timeout
                    );
                   $this->fcmNotification($msg, $fields, 'driverapp');
                }else{
                   $token = $driver->device_token;      
                   $fields = array(
                        'to'=>$token,
                        'notification' => $msg,
                        'data' => array('bookingid' => $bookingId, 'type' => '1', 'message' => 'booking_request'),
                        'android' => array('ttl' => (String)$timeout),
                        'time_to_live' => (int)$timeout
                    );
                   $this->iosNotification($msg, $fields, 'driverapp', $environment);
                } 

                //set is_receive_notification 1 to stop notification until accept/reject/timeout
                DB::table('drivers')->where('id',$driver->driver_id)->update(['is_receive_notifi'=>1]);       
            }
            
        }

        $current_time = date('Y-m-d H:i:s');
        $stop = strtotime($current_time) + $timeout;
        
        $dbstatus = 'pending';    
        while ($dbstatus == 'pending') {
            $new = strtotime(date('Y-m-d H:i:s'));
            if ($new >= $stop) {
                $req = array('booking_id' => $bookingId, 'canceled_by' => 'auto', 'stoken' => '987654321');
                // $status = "canceled";
                $status = "rejected";
                $cancel_time = date('Y-m-d H:i:s');
                $res = Booking::where('id', $bookingId)
                        ->update(['booking_status' => $status, 'cancel_time' => $cancel_time, 'canceled_by' => "auto"]);
                $data = array("success" => false, "message" => "Drivers not responding for booking ", "data" => array());

                //After timeout if no driver response then set is_receive_notification to 0 so that drivers can get notification again.
                if(!empty($results)){
                    foreach ($results as $driver){
                        DB::table('drivers')->where('id',$driver->driver_id)
                        ->update(['is_receive_notifi'=>0]);        
                    }
                }

                return $data;
                exit;
            }
            sleep(5);
            $dbstatus = $this->getStatus($bookingId);
        }

        //After timeout if no driver response then set is_receive_notification to 0 so that drivers can get notification again.
        if(!empty($results)){
            foreach ($results as $driver){
                DB::table('drivers')->where('id',$driver->driver_id)
                ->update(['is_receive_notifi'=>0]);        
            }
        }


        $array = array(
            'otp' => $otp,
            'booking_id' => $bookingId,
        );

        $driver_id = '';
        $reg = $vehicle_img = $vehicle_cat = $basefare = $per_km_charge = '';
        $name = $mobile = $email = $image = ''; 

        $driverData = DB::table('booking')->select('driver_id')->where('id',$bookingId)->first();
        $driver_id = $driverData->driver_id;
        if($driver_id != ''){
            
            $driver_data = DB::table('drivers')
                       ->select('drivers.name', 'drivers.mobile', 'drivers.email', 'drivers.profile_image','vehicle_category.name as vehicle_cat','vehicle_category.basefare', 'vehicle_category.per_km_charges','vehicles.registration_number as reg', 'vehicles.vehicle_image')
                       ->leftJoin('vehicles', 'drivers.id', '=', 'vehicles.driver_id')
                       ->leftJoin('vehicle_category', 'vehicles.vehicle_category', '=', 'vehicle_category.id')
                       ->where('drivers.id',$driver_id)
                       ->first();           
            if(isset($driver_data->reg)) {
                $reg = $driver_data->reg;
            }
            
            $destinationPath = 'Admin/profileImage';
            $image = url('/') . '/' . $destinationPath . '/' . 'noimage.png';
            if (isset($driver_data->profile_image) && ($driver_data->profile_image != '')) {
                if (file_exists($destinationPath . '/' . $driver_data->profile_image)) {
                    $image = url('/') . '/' . $destinationPath . '/' . $driver_data->profile_image;
                }
            }

            $vehiclePath = 'Admin/vehicleImage';
            $vehicle_img = url('/') . '/' . $vehiclePath . '/' . 'noimage.png';
            if (isset($driver_data->vehicle_image) && ($driver_data->vehicle_image != '')){
                if(file_exists($vehiclePath . '/' . $driver_data->vehicle_image)) {
                    $vehicle_img = url('/') . '/' . $vehiclePath . '/' . $driver_data->vehicle_image;
                }
            }

            if (isset($driver_data->vehicle_cat) && $driver_data->vehicle_cat != null) {
                $vehicle_cat = $driver_data->vehicle_cat;
            }

            // if (isset($driver_data->basefare) && $driver_data->basefare != null) {
            //     $basefare = $driver_data->basefare;
            // } 
            if (isset($driver_data->per_km_charges) && $driver_data->per_km_charges != null) {
                $per_km_charge = $driver_data->per_km_charges;
            }
            if (isset($driver_data->name)) {
                $name = $driver_data->name;
            } 
            if (isset($driver_data->mobile)) {
                $mobile = $driver_data->mobile;
            }
            if (isset($driver_data->email)) {
                $email = $driver_data->email;
            }

        }

        $array['driver_id'] = $driver_id;
        $array['name'] = $name;
        $array['mobile'] = $mobile;
        $array['email'] = $email;
        $array['image'] = $image;
        $array['vehicle_cat'] = $vehicle_cat;
        // $array['basefare'] = $basefare;
        $array['per_km_charge'] = $per_km_charge;
        $array['booking_status'] = $dbstatus;
        $array['vehicle_img'] = $vehicle_img;
        $array['reg'] = $reg;

        // if ($dbstatus == 'canceled') {
        if ($dbstatus == 'rejected') {
            $data = array("success" => false, "message" => "No driver available for now try after sometime.", "data" => $array);
        } else {
            $data = array("success" => true, "message" => "Your booking is accepted by driver. Go to schedule booking for more information.", "data" => $array);
        }
        return $data;       

    }





/////////////////////////////////////SCHEDULE BOOKING END////////////////////////////////////// 


/////////////////////////////////////PAYMENT TESTING START ///////////////////////////////////////
    public function prepareCheckout(Request $request)
    {
        // $security_token = $request->header('stoken');
        $obj = (object) [];
        // if ($security_token == null || $security_token == '') {
        //     $status = false;
        //     $data = array("success" => false, "message" => "Security token can not be blank", "data" => $obj);
        //     return $data;
        //     exit;
        // } else if ($security_token != '987654321') {
        //     $data = array("success" => false, "message" => "Please Add Correct Security Token", "data" => $obj);
        //     return $data;
        //     exit;
        // }

        $amount = $request->input('amount');
        if ($amount == null || $amount == '') {
            $status = false;
            $data = array("success" => false, "message" => "Amount can not be blank", "data" => $obj);
            return $data;
            exit;
        }  

        $currency = $request->input('currency');
        if ($currency == null || $currency == '') {
            $status = false;
            $data = array("success" => false, "message" => "Currency can not be blank", "data" => $obj);
            return $data;
            exit;
        } 

        $paymentType = $request->input('paymentType');
        if ($paymentType == null || $paymentType == '') {
            $status = false;
            $data = array("success" => false, "message" => "payment Type can not be blank", "data" => $obj);
            return $data;
            exit;
        } 


        $url = "https://test.oppwa.com/v1/checkouts";
        $data = "entityId=8ac7a4c86991344b0169966b739d0666" .
                "&amount=".$amount.
                "&currency=".$currency.
                "&paymentType=".$paymentType.
                "&notificationUrl=http://www.example.com/notify";



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                       'Authorization:Bearer OGFjN2E0Yzg2OTkxMzQ0YjAxNjk5NjZhZmJkYzA2NjJ8NHdtQlFmbXJ6VA=='));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);


        $msg = 'Something went wrong';
        if($responseData){
            $jsonResp = json_decode($responseData,true);
            $msg = $jsonResp['result']['description'];
            
            if($jsonResp['result']['code'] == '000.200.100'){
                $id = $jsonResp['id'];
                $obj = array("success" => true, "message" => $msg, "data" => array('id'=>$id));
            }else{
                $obj = array("success" => false, "message" => $msg,'data'=>$jsonResp);
            }        
        }
        
        return $obj;
    }
    //8ac7a4c86991344b0169966b739d0666 -- client
    public function checkPaymentStatus(Request $request)
    {
        $obj = (object) [];
        $resourcePath = $request->input('resourcePath');
        if ($resourcePath == null || $resourcePath == '') {
            $status = false;
            $data = array("success" => false, "message" => "Resource Path can not be blank", "data" => $obj);
            return $data;
            exit;
        }     

        $url = "https://test.oppwa.com".$resourcePath;
        $url .= "?entityId=8ac7a4c86991344b0169966b739d0666";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                       'Authorization:Bearer OGFjN2E0Yzg2OTkxMzQ0YjAxNjk5NjZhZmJkYzA2NjJ8NHdtQlFmbXJ6VA=='));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        
        $msg = 'Something went wrong';
        if($responseData){
            $jsonResp = json_decode($responseData,true);
            $msg = $jsonResp['result']['description'];
            
            if($jsonResp['result']['code'] == '000.100.110'){
                $obj = array("success" => true, "message" => $msg, "data" => $jsonResp);
            }else{
                $obj = array("success" => false, "message" => $msg,'data'=>$jsonResp);
            }        
        }
        return $obj;
    } 


    //NEW API
    public function prepareCheckoutNew(Request $request)
    {
        $security_token = $request->header('stoken');
        $obj = (object) [];
        if ($security_token == null || $security_token == '') {
            $status = false;
            $data = array("success" => false, "message" => "Security token can not be blank", "data" => $obj);
            return $data;
            exit;
        } else if ($security_token != '987654321') {
            $data = array("success" => false, "message" => "Please Add Correct Security Token", "data" => $obj);
            return $data;
            exit;
        }

        $booking_id = $request->input('booking_id');
        if ($booking_id == null || $booking_id == '') {
            $status = false;
            $data = array("success" => false, "message" => "Booking id can not be blank", "data" => $obj);
            return $data;
            exit;
        }

        $customer_id = $request->input('customer_id');
        if ($customer_id == null || $customer_id == '') {
            $status = false;
            $data = array("success" => false, "message" => "Customer id can not be blank", "data" => $obj);
            return $data;
            exit;
        }

        $driver_id = $request->input('driver_id');
        if ($driver_id == null || $driver_id == '') {
            $status = false;
            $data = array("success" => false, "message" => "Driver id can not be blank", "data" => $obj);
            return $data;
            exit;
        }    

        $amount = $request->input('amount');
        if ($amount == null || $amount == '') {
            $status = false;
            $data = array("success" => false, "message" => "Amount can not be blank", "data" => $obj);
            return $data;
            exit;
        }  

        $currency = $request->input('currency');
        if ($currency == null || $currency == '') {
            $status = false;
            $data = array("success" => false, "message" => "Currency can not be blank", "data" => $obj);
            return $data;
            exit;
        } 

        $paymentType = $request->input('paymentType');
        if ($paymentType == null || $paymentType == '') {
            $status = false;
            $data = array("success" => false, "message" => "payment Type can not be blank", "data" => $obj);
            return $data;
            exit;
        } 

        $payment = new Payment;
        $payment->booking_id = $booking_id;
        $payment->customer_id = $customer_id;
        $payment->driver_id = $driver_id;
        $payment->amount = $amount;
        $payment->currency = $currency;
        $payment->payment_type = $paymentType; //DB,PA
        $payment->payment_status = 0;//pending
        $payment->save();
        $payment_id = $payment->id;
        
        $data['status'] = false;
        $data['message'] = 'Something went wrong';
        $data['data'] = $obj;

        if(!empty($payment_id)){
            
            $url = env('URL_CHECKOUT');
            $channel_id = env('PAYMENT_ONEOFF_CHANNELID');
            
            $postdata = "entityId=".$channel_id.
                        "&amount=".$amount.
                        "&currency=".$currency.
                        "&paymentType=".$paymentType.
                        "&notificationUrl=http://www.example.com/notify";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                           'Authorization:Bearer OGFjN2E0Yzg2OTkxMzQ0YjAxNjk5NjZhZmJkYzA2NjJ8NHdtQlFmbXJ6VA=='));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseData = curl_exec($ch);
            if(curl_errno($ch)) {
                return curl_error($ch);
            }
            curl_close($ch);

            if($responseData){
                $jsonResp = json_decode($responseData,true);
                $msg = $jsonResp['result']['description'];
                
                if($jsonResp['result']['code'] == '000.200.100'){
                    $resdata['id'] = $jsonResp['id'];
                    $resdata['payment_id'] = $payment_id;
                    $data = array("success" => true, "message" => $msg, "data" => $resdata);
                }else{
                    $data = array("success" => false, "message" => $msg,'data'=>$jsonResp);
                }        
            }
        }
        
        return $data;
    }
    
    public function checkPaymentStatusNew(Request $request)
    {
        $security_token = $request->header('stoken');
        $obj = (object) [];
        if ($security_token == null || $security_token == '') {
            $data = array("success" => false, "message" => "Security token can not be blank", "data" => $obj);
            return $data;
            exit;
        } else if ($security_token != '987654321') {
            $data = array("success" => false, "message" => "Please Add Correct Security Token", "data" => $obj);
            return $data;
            exit;
        }

        $environment = "adhoc";
        $environment = $request->header('environment'); // type of server using for iosnotification
        if ($environment == null || $environment == '') {
            $environment = "adhoc";
        }


        $payment_id = $request->input('payment_id');
        if ($payment_id == null || $payment_id == '') {
            $data = array("success" => false, "message" => "Payment id can not be blank", "data" => $obj);
            return $data;
            exit;
        }

        $customer_id = $request->input('customer_id');
        if ($customer_id == null || $customer_id == '') {
            $data = array("success" => false, "message" => "Customer id can not be blank", "data" => $obj);
            return $data;
            exit;
        }

        $booking_id = $request->input('booking_id');
        if ($booking_id == null || $booking_id == '') {
            $data = array("success" => false, "message" => "Booking id can not be blank", "data" => $obj);
            return $data;
            exit;
        }

        $resourcePath = $request->input('resourcePath');
        if ($resourcePath == null || $resourcePath == '') {
            $data = array("success" => false, "message" => "Resource Path can not be blank", "data" => $obj);
            return $data;
            exit;
        }     

        $payment = DB::table('payments')->where('id',$payment_id)->first();
        if(empty($payment)){
            $data = array("success" => false, "message" => "Payment not found", "data" => $obj);
            return $data;
            exit;
        }

        $channel_id = env('PAYMENT_ONEOFF_CHANNELID');

        $url = env('URL_CHECKSTATUS').$resourcePath;
        $url .= "?entityId=".$channel_id;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                       'Authorization:Bearer OGFjN2E0Yzg2OTkxMzQ0YjAxNjk5NjZhZmJkYzA2NjJ8NHdtQlFmbXJ6VA=='));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        
        $data['status'] = false;
        $data['message'] = 'Something went wrong';
        $data['data'] = $obj;
        
        if($responseData){
            $jsonResp = json_decode($responseData,true);
            $msg = $jsonResp['result']['description'];
            
            // if($jsonResp['result']['code'] == '000.000.00'){ //production
            if($jsonResp['result']['code'] == '000.100.110'){ //test

                if(($payment->amount == $jsonResp['amount']) && ($payment->currency == $jsonResp['currency']) && ($payment->payment_type == $jsonResp['paymentType'])){

                    DB::table('payments')->where('id',$payment_id)
                        ->update(['payment_status'=>1,'payment_response'=>$responseData]);

                    $booking_status = 'completed';
                    $completed_time = date('Y-m-d H:i:s');

                    DB::table('booking')->where('id',$booking_id)
                        ->update(['is_payment'=>1,'booking_status'=>$booking_status,'completed_time'=>$completed_time]);    
                    
                    $driver = DB::table('booking')
                                ->select('drivers.type','drivers.device_token')
                                ->leftJoin('drivers', 'booking.driver_id', '=', 'drivers.id')
                                ->where('booking.id',$booking_id)
                                ->first();    
                    if(!empty($driver)){
                        $token = $driver->device_token;
                        $ntitle = "Payment Completed";
                        $nmessage = "Payment Completed by Customer";
                        $notifimsg = array('body' => $nmessage, 'title' => $ntitle);
                        $fields = array(
                            'to'=>$token,
                            'notification' => $notifimsg,
                            'data' => array('bookingid' => $booking_id, 'type' => '11', 'message' => 'payment_success')
                        );
                        if ($driver->type == "1"){
                           $this->fcmNotification($notifimsg, $fields, 'driverapp');
                        }else{      
                           $this->iosNotification($notifimsg, $fields, 'driverapp', $environment);
                        }
                    }            

                    $data = array("success" => true, "message" => 'Payment completed!', "data" => $obj);                              
                }else{
                    if($payment->amount != $jsonResp['amount']){
                        $reason = 'Paid amount not matched with payble amount.';
                        DB::table('payments')->where('id',$payment_id)
                        ->update(['payment_status'=>2,'payment_response'=>$responseData,'failed_reason'=>$reason]);
                        $data = array("success" => false, "message" => $reason, "data" => $obj);
                        return $data;
                        exit;
                    }
                    if($payment->currency == $jsonResp['currency']){
                        $reason = 'Paid currency type not matched with payble currency type.';
                        DB::table('payments')->where('id',$payment_id)
                        ->update(['payment_status'=>2,'payment_response'=>$responseData,'failed_reason'=>$reason]);
                        $data = array("success" => false, "message" => $reason, "data" => $obj);
                        return $data;
                        exit;
                    }
                    if($payment->payment_type == $jsonResp['paymentType']){
                        $reason = 'Paid payment type not matched with payble payment type.';
                        DB::table('payments')->where('id',$payment_id)
                        ->update(['payment_status'=>2,'payment_response'=>$responseData,'failed_reason'=>$reason]);
                        $data = array("success" => false, "message" => $reason, "data" => $obj);
                        return $data;
                        exit;
                    }  
                }    
            }else{
                DB::table('payments')->where('id',$payment_id)
                        ->update(['payment_status'=>2,'payment_response'=>$responseData,'failed_reason'=>$msg]);
                $data = array("success" => false, "message" => $msg,'data'=>$jsonResp);
            }        
        }
        return $data;
    }

}