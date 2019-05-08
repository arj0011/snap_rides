<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;
use App\Booking;
use App\Customer;
use App\Setting;
use App\Category;
use DB;
use Session;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 

     */
    public function index(Request $request)
    {  
           $this->authorize('index', Booking::class);

        try {
            $filter = '';
            $filter = $request->filter;

           $bookings = DB::table('booking')
            ->select( 'booking.id' , 'drivers.id as driver_id' , 'customers.id as rider_id' ,'customers.name as rider_name', 'customers.mobile','booking.booking_time' , 'booking.start_time' , 'booking.pickup_addrees' , 'booking.destination_address' , 'booking.total_amount' , 'booking.tax_amount' , 'booking.discount_amount' , 'booking.final_amount' , 'drivers.name as driver_name','booking.vehicle_id','vehicle_category.name as vehicle_name','booking.booking_status','booking.schedule_booking' )
            ->leftJoin('customers', 'booking.customer_id', '=', 'customers.id')
            ->leftJoin('drivers', 'booking.driver_id', '=', 'drivers.id')
            ->leftJoin('vehicle_category', 'vehicle_category.id', '=', 'booking.vehicle_id')       
            ->whereNull('booking.deleted_at')
            ->where('booking.driver_id','!=',0)
            ->orderby('booking.id' , 'DESC' )
            ->where(function($query) use ($filter) {
                    if (!empty($filter)) {
                         if($filter != 'all'){
                            $query -> where('booking_status' , '=' , $filter);                        
                         }
                        }
            })
            ->paginate('10');
            foreach($bookings as $value){
               $veh_det = DB::table('vehicles')->where("id",'=',$value->vehicle_id)->first();
               if(!empty($veh_det->vehicle_category)){
               $veh_cat = DB::table('vehicle_category')->where("id",'=',$veh_det->vehicle_category)->first();
               $value->vehicle_name = $veh_cat->name; 
               }
               
               
            }
            
            return view('admin.bookings.index',compact('bookings','filter'))->with('i', ($bookings->currentpage()-1)*$bookings->perpage()+1);
            
        } catch ( \Exception $e) {
            return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
        }
    }

    public function search(Request $request){
       
       $this->authorize('index', Booking::class);

        try {

        $p  = $request->p;    // for filed name  
        $q  = $request->q;    // searched string
        $type  = $request->type;    // searched string booking type

        $bookings = DB::table('booking')
        ->select( 'booking.id' , 'drivers.id as driver_id' , 'customers.id as rider_id' ,'customers.name as rider_name','customers.mobile' ,'booking.booking_time' , 'booking.start_time' , 'booking.pickup_addrees' , 'booking.destination_address' , 'booking.total_amount' , 'booking.tax_amount' , 'booking.discount_amount' , 'booking.final_amount' , 'drivers.name as driver_name','booking.vehicle_id','vehicle_category.name as vehicle_name','booking.booking_status','booking.schedule_booking' )
        ->leftJoin('customers', 'booking.customer_id', '=', 'customers.id')
        ->leftJoin('drivers', 'booking.driver_id', '=', 'drivers.id')
        ->leftJoin('vehicle_category', 'vehicle_category.id', '=', 'booking.vehicle_id')
        ->whereNull('booking.deleted_at')
        ->where('booking.driver_id','!=',0)
        ->orderby('booking.id' , 'DESC' )
        ->where(function($query) use ($p,$q,$type) {
                if (empty($p)  && $q != '') {
                    $query -> whereRaw('LOWER(booking.id) like ?', '%'.str_replace('bkid','',strtolower($q)).'%' );
                    $query -> orWhereRaw('LOWER(drivers.name) like ?', '%'.strtolower($q).'%');
                    $query -> orWhereRaw('LOWER(customers.name) like ?', '%'.strtolower($q).'%');
                    $query -> orWhereRaw('LOWER(booking.booking_time) like ?', '%'.strtolower($q).'%');
                    $query -> orWhereRaw(DB::raw("DATE(booking.booking_time) = '".date('y-m-d' ,strtotime($q))."'"));
                    $query -> orWhereRaw(DB::raw("DATE(booking.booking_time) >= '".date('y-m-d' ,strtotime($q))."' AND DATE(booking.booking_time) <= '".date('y-m-d')."'"));
                }elseif($p == 'id'  && $q != ''){
                    $query ->   whereRaw('LOWER(booking.id) like ?', '%'.str_replace('bkid','',strtolower($q)).'%' );
                }elseif ($p == 'driver'  && $q != '') {
                    $query -> orWhereRaw('LOWER(drivers.name) like ?', '%'.strtolower($q).'%');
                }elseif ($p == 'customer'  && $q != '') {
                    $query -> orWhereRaw('LOWER(customers.name) like ?', '%'.strtolower($q).'%') ;
                }
                elseif ($p == 'booking'  && $q != '') {
                    $query -> orWhereRaw(DB::raw("DATE(booking.booking_time) = '".date('y-m-d' ,strtotime($q))."'"));
                }
                elseif ($p == 'from_date'  && $q != '') {
                    $query -> orWhereRaw(DB::raw("DATE(booking.booking_time) >= '".date('y-m-d' ,strtotime($q))."' AND DATE(booking.booking_time) <= '".date('y-m-d')."'"));
                }

                if($type != 'all'){
                    $btype = ($type == 'schedule') ? 1 : 0;
                    $query->where('booking.schedule_booking',$btype);
                }


            })
           ->paginate('10');

           foreach($bookings as $value){
               $veh_det = DB::table('vehicles')->where("id",'=',$value->vehicle_id)->first();
               if(!empty($veh_det->vehicle_category)){
               $veh_cat = DB::table('vehicle_category')->where("id",'=',$veh_det->vehicle_category)->first();
               $value->vehicle_name = $veh_cat->name; 
               }
               
               
            }

        return view('admin.bookings.index',compact('bookings','p','q','type'))->with('i', ($bookings->currentpage()-1)*$bookings->perpage()+1);
            
        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }


    public function invoice(Request $request)
    {   
        $this->authorize('invoice', Booking::class);
     
          try {
               $id = decrypt($request->id);
             $invoice = DB::table('booking')
            ->select( 'booking.id' ,'booking.pickup_lat','booking.pickup_long','booking.drop_lat','booking.drop_long', 'drivers.id as driver_id' , 'drivers.mobile as driver_mobile' , 'customers.id as rider_id' ,'customers.name as rider_name' , 'customers.mobile as rider_mobile','booking.booking_time' , 'booking.start_time' , 'booking.pickup_addrees' , 'booking.destination_address' , 'booking.total_amount' , 'booking.tax_amount' , 'booking.discount_amount' , 'booking.final_amount' , 'vehicle_category.per_km_charges as per_km_charges', 'booking.actual_distance' , 'booking.estimated_distance' ,'vehicle_category.basefare' , 'drivers.name as  driver_name' , 'booking.completed_time' , 'booking.estimated_distance as distance','booking.booking_status')
            ->leftJoin('customers', 'booking.customer_id', '=', 'customers.id')
            ->leftJoin('drivers', 'booking.driver_id', '=', 'drivers.id')
             ->leftJoin('vehicles', 'vehicles.driver_id', '=', 'drivers.id')
            ->leftJoin('vehicle_category', 'vehicles.vehicle_category', '=', 'vehicle_category.id')
            ->where('booking.id',$id)
            ->first();

            //die();

            return view('admin.bookings.invoice',compact('invoice'));
            
        } catch ( \Exception $e) {
            return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    { 

          $this->authorize('delete', Booking::class);

        try {

          $id = decrypt($request->id);
       
          $status = DB::table('booking')->where('id' ,'=' , $id)->update(['deleted_at' => \Carbon\Carbon::now()]);
          
          if($status){
                return Redirect('bookings')->with('msg','Successfully deleted bookings record!')->with('color' , 'success');
            }
            else{
                return Redirect::back()->with('msg','Failed to delete bookings record')->with('color' , 'danger');
            }
            
        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

    //create booking by dispatcher
    public function create()
    {   
        try{
            /*$drivers = array();
            $pickuplatlng = array();*/
            $categories = Category::get();
            /*$data = Session::get('data');
            if(!empty($data)){
                $drivers = $data['drivers'];
                $pickuplatlng = $data['pickuplatlng'];
            }*/
            
            // return view('admin.bookings.add',compact('categories','drivers','pickuplatlng'));  
            return view('admin.bookings.add',compact('categories'));  
        } catch ( \Exception $e) {
            // print_r($e->getMessage());die;
            return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
        }
    }

    public function store(Request $request)
    {
        /*$input=$request->all();
        $validator=Validator::make($input,[
            'mobile' => 'required|min:8|numeric'
            'name' => 'required',   
            'pickup_loc' => 'required',  
            'drop_loc' => 'required',  
            'booking_date' => 'required',
            'booking_time' => 'required',
            'vehicle_type'=>'required|numeric'
        ]);
        if($validator->fails()){
            return back()->withErrors($validator);
        }*/
        
        $vehicle_type = $request->input('vehicle_type');
        $pickup_addrees = $request->input('pickup_loc');
        $destination_address = $request->input('drop_loc');
        $booking_date = $request->input('booking_date');
        $booking_time = $request->input('booking_time');
        
        $pickup_lat = $request->input('pickup_lat');
        $pickup_long = $request->input('pickup_long');
        $drop_lat = $request->input('drop_lat');
        $drop_long = $request->input('drop_long');

        $mobile = $request->mobile;
        $name = $request->name;
        $customerData = Customer::where('mobile',$mobile)->select('id')->first();
        $customer_id = '';
        if(empty($customerData)){
            $customer_data['mobile'] = $name;
            $customer_data['name'] = $name;
            $customer_id = DB::table('customers')->insertGetId($customer_data);
        }else{
            $customer_id = $customerData->id;   
        }

        $pickup_lat_long = $pickup_lat . ',' . $pickup_long;

        if ($drop_lat != null || $drop_long != '') {

            $destinations_lat_long = $drop_lat . ',' . $drop_long;
            $api = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$pickup_lat_long&destinations=$destinations_lat_long&key=AIzaSyAFwsRyc4HATOYM5ZjS3kFsKfj4EUoFRqs";
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

        date_default_timezone_set ("Africa/Johannesburg") ;
        if(empty($booking_time)){
            $booking_time = date('Y-m-d G:i:s');         
        }else{
            $datetime = $booking_date.''.$booking_time;         
            $booking_datetime = date('Y-m-d G:i:s',strtotime($datetime));         
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

        /*$otp = $this->random_string();
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

        $mobilearray = array($mobile);
        $msg = "Your otp is " . $otp;
        $res = commonSms($mobilearray, $msg);*/
        
        $bookingId = 171;
        
        $vehiclePath = 'Admin/vehicleImage';
        $img_path = url('/') . '/' . $vehiclePath . '/';
        $no_img = url('/') . '/' . $vehiclePath . '/' . 'noimage.png';

        $sql = "SELECT
                drivers.name,drivers.mobile,
                vehicle_category.name as vehicle_type,
                vehicle_category.image as type_img,
                IF(vehicles.vehicle_image IS NOT NULL,CONCAT('$img_path',vehicles.vehicle_image),'$no_img') as vehicle_img,
                driver_latlong.latitude,
                driver_latlong.longitude,
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
                vehicles.vehicle_category = $vehicle_type AND
                driver_latlong.booking_id = 0 AND 
                driver_latlong.id IN (
                SELECT MAX(id) FROM driver_latlong GROUP BY driver_latlong.id) AND
                drivers.id NOT IN(
                SELECT driver_id FROM booking WHERE booking_status = 'in_progress')
                GROUP BY drivers.id
                HAVING distance_in_km <= 5
                ORDER BY distance_in_km ASC";
        
        $drivers = DB::select( DB::raw($sql));  
        $data = array();
        $pickuplatlng = array('pickup_lat'=>$pickup_lat,'pickup_long'=>$pickup_long);
        $data['drivers'] = $drivers;
        $data['pickuplatlng'] = $pickuplatlng;
        $data['booking_id'] = $bookingId;
        $data['vehicle_type'] = $vehicle_type;
        
        // return Redirect::to('create-booking')->with('data',$data);
        return Redirect::to('load-drivers')->with('data',$data);
    }

    public function loadDriver(Request $request)
    {
        
        $drivers = array();
        $pickuplatlng = array();
        $booking_id = '';
        $vehicle_type = '';

        $categories = Category::get();
        
        $data = Session::get('data');
        if(!empty($data)){
            $drivers = $data['drivers'];
            $pickuplatlng = $data['pickuplatlng'];
            $booking_id = $data['booking_id'];
            $vehicle_type = $data['vehicle_type'];

            if($booking_id != ''){
                $booking = Booking::select('booking.pickup_addrees','booking.destination_address','booking.pickup_lat','booking.pickup_long','booking.drop_lat','booking.drop_long','booking.booking_time','customers.name','customers.mobile')
                            ->leftJoin('customers', 'booking.customer_id', '=', 'customers.id')
                            ->where('booking.id',$booking_id)
                            ->first();
            }
        }
        
        return view('admin.bookings.assigndrivers',compact('drivers','pickuplatlng','booking_id','booking','categories','vehicle_type')); 
    }

}
