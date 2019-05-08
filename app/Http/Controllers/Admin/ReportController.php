<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;
use App\Payment;
use App\Booking;
use DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 

     */
    public function index(Request $request)
    {  
        //$this->authorize('index', Payment::class);
        try {

            $data['totalDriver'] =  DB::table('drivers')
                       ->whereNull('deleted_at')
                       ->count();

            $data['totalRider'] =  DB::table('customers')
                       ->whereNull('deleted_at')
                       ->count();

            $data['totalDispatcher'] =  DB::table('users')
                       ->whereNull('deleted_at')
                       ->where('is_role',14)
                       ->count();

            $data['totalBooking'] =  DB::table('booking')
                       ->whereNull('deleted_at')
                       ->where('booking.driver_id','!=',0)
                       ->count();

            $data['filterTotalBooking'] = $data['totalBooking'];             

            $data['totalscheduleBooking'] =  DB::table('booking')
                       ->whereNull('deleted_at')
                       ->where('booking.driver_id','!=',0)
                       ->where('booking.schedule_booking',1)
                       ->count();                                  


            $data['complitedTrip'] =  DB::table('booking')
                       ->where('booking_status' , '=' , 'completed')
                       ->wherenull('deleted_at')
                       ->count();

            $data['runningTrip'] = DB::table('booking')
                     ->where('booking_status' , '=' , 'in_progress')
                     ->wherenull('deleted_at')
                     ->count();

            $data['cancelTrip']  = DB::table('booking')
                   ->where('booking_status' , '=' , 'canceled')
                    ->wherenull('deleted_at')
                    ->count();

            $data['acceptTrip']  = DB::table('booking')
                   ->where('booking_status' , '=' , 'accept')
                    ->wherenull('deleted_at')
                    ->count(); 

            $data['paymentPendingTrip']  = DB::table('booking')
                   ->where('booking_status' , '=' , 'payment_pending')
                    ->wherenull('deleted_at')
                    ->count();        

            $fareData =  DB::table('booking')
                   ->select(DB::Raw('SUM(final_amount) as totalFare')) 
                   ->where('booking_status' , '=' , 'completed')
                   ->wherenull('deleted_at')
                   ->first();               
            $data['totalFare'] = $fareData->totalFare;

           $bookings = DB::table('booking')
            ->select( 'booking.id' , 'drivers.id as driver_id' , 'customers.id as rider_id' ,'customers.name as rider_name', 'customers.mobile','booking.booking_time' , 'booking.start_time' , 'booking.pickup_addrees' , 'booking.destination_address' , 'booking.total_amount' , 'booking.tax_amount' , 'booking.discount_amount' , 'booking.final_amount' , 'drivers.name as driver_name','booking.vehicle_id','vehicle_category.name as vehicle_name','booking.booking_status','schedule_booking' )
            ->leftJoin('customers', 'booking.customer_id', '=', 'customers.id')
            ->leftJoin('drivers', 'booking.driver_id', '=', 'drivers.id')
            ->leftJoin('vehicle_category', 'vehicle_category.id', '=', 'booking.vehicle_id')       
            ->whereNull('booking.deleted_at')
            ->where('booking.driver_id','!=',0)
            ->orderby('booking.id' , 'DESC' )
            
            ->paginate('5');
            foreach($bookings as $value){
               $veh_det = DB::table('vehicles')->where("id",'=',$value->vehicle_id)->first();
               if(!empty($veh_det->vehicle_category)){
               $veh_cat = DB::table('vehicle_category')->where("id",'=',$veh_det->vehicle_category)->first();
               $value->vehicle_name = $veh_cat->name; 
               }
               
               
            }
            
            return view('admin.reports.index',compact('bookings','data'))->with('i', ($bookings->currentpage()-1)*$bookings->perpage()+1);
            
        } catch ( \Exception $e) {
            return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
        }
    }

    public function search(Request $request){
       
       // $this->authorize('index', Booking::class);

        try {

        $data['totalDriver'] =  DB::table('drivers')
                       ->whereNull('deleted_at')
                       ->count();

        $data['totalRider'] =  DB::table('customers')
                   ->whereNull('deleted_at')
                   ->count();

        $data['totalDispatcher'] =  DB::table('users')
                   ->whereNull('deleted_at')
                   ->where('is_role',14)
                   ->count(); 

        $data['totalBooking'] =  DB::table('booking')
                       ->whereNull('deleted_at')
                       ->where('booking.driver_id','!=',0)
                       ->count(); 

        $data['totalscheduleBooking'] =  DB::table('booking')
                   ->whereNull('deleted_at')
                   ->where('booking.driver_id','!=',0)
                   ->where('booking.schedule_booking',1)
                   ->count();            


        $p  = $request->p;    // for filed name  
        $q  = $request->q;    // searched string
        $filter  = $request->filter; // searched string
        $type  = $request->type; // Booking type string
        
        $bookings = DB::table('booking')
        ->select( 'booking.id' , 'drivers.id as driver_id' , 'customers.id as rider_id' ,'customers.name as rider_name','customers.mobile' ,'booking.booking_time' , 'booking.start_time' , 'booking.pickup_addrees' , 'booking.destination_address' , 'booking.total_amount' , 'booking.tax_amount' , 'booking.discount_amount' , 'booking.final_amount' , 'drivers.name as driver_name','booking.vehicle_id','vehicle_category.name as vehicle_name','booking.booking_status','schedule_booking' )
        ->leftJoin('customers', 'booking.customer_id', '=', 'customers.id')
        ->leftJoin('drivers', 'booking.driver_id', '=', 'drivers.id')
        ->leftJoin('vehicle_category', 'vehicle_category.id', '=', 'booking.vehicle_id')
        ->whereNull('booking.deleted_at')
        ->where('booking.driver_id','!=',0)
        ->orderby('booking.id' , 'DESC' )
        ->where(function($query) use ($p,$q,$filter,$type) {
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

                if($filter != 'all'){
                    $query->where('booking.booking_status',$filter);
                }

                if($type != 'all'){
                    $btype = ($type == 'schedule') ? 1 : 0;
                    $query->where('booking.schedule_booking',$btype);
                }

            })
           ->paginate('5');

            foreach($bookings as $value){
               $veh_det = DB::table('vehicles')->where("id",'=',$value->vehicle_id)->first();
               if(!empty($veh_det->vehicle_category)){
               $veh_cat = DB::table('vehicle_category')->where("id",'=',$veh_det->vehicle_category)->first();
               $value->vehicle_name = $veh_cat->name; 
               }   
            }
        
        $data['filterTotalBooking'] = $this->searchCount($request,'');
        $data['complitedTrip'] = $this->searchCount($request,'completed');
        $data['runningTrip'] = $this->searchCount($request,'in_progress');
        $data['acceptTrip'] = $this->searchCount($request,'accept');
        $data['cancelTrip'] = $this->searchCount($request,'canceled');    
        $data['paymentPendingTrip'] = $this->searchCount($request,'payment_pending');    
        $data['totalFare'] = $this->searchFare($request,$filter);

        return view('admin.reports.index',compact('bookings','p','q','filter','type','data'))->with('i', ($bookings->currentpage()-1)*$bookings->perpage()+1);
            
        } catch ( \Exception $e) {
            print_r($e->getMessage());die;
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

    public function searchCount(Request $request,$status='')
    {

        $p  = $request->p;    // for filed name  
        $q  = $request->q;   // searched string
        $type  = $request->type;   // booking type
    
        $bookings = DB::table('booking')
        ->select( 'booking.id' , 'drivers.id as driver_id' , 'customers.id as rider_id' ,'customers.name as rider_name','customers.mobile' ,'booking.booking_time' , 'booking.start_time' , 'booking.pickup_addrees' , 'booking.destination_address' , 'booking.total_amount' , 'booking.tax_amount' , 'booking.discount_amount' , 'booking.final_amount' , 'drivers.name as driver_name','booking.vehicle_id','vehicle_category.name as vehicle_name','booking.booking_status' )
        ->leftJoin('customers', 'booking.customer_id', '=', 'customers.id')
        ->leftJoin('drivers', 'booking.driver_id', '=', 'drivers.id')
        ->leftJoin('vehicle_category', 'vehicle_category.id', '=', 'booking.vehicle_id')
        ->whereNull('booking.deleted_at')
        ->where('booking.driver_id','!=',0)
        ->orderby('booking.id' , 'DESC' )
        ->where(function($query) use ($p,$q,$type,$status) {
                if (empty($p) && $q != '') {
                    $query -> whereRaw('LOWER(booking.id) like ?', '%'.str_replace('bkid','',strtolower($q)).'%' );
                    $query -> orWhereRaw('LOWER(drivers.name) like ?', '%'.strtolower($q).'%');
                    $query -> orWhereRaw('LOWER(customers.name) like ?', '%'.strtolower($q).'%');
                    $query -> orWhereRaw('LOWER(booking.booking_time) like ?', '%'.strtolower($q).'%');
                    $query -> orWhereRaw(DB::raw("DATE(booking.booking_time) = '".date('y-m-d' ,strtotime($q))."'"));
                    $query -> orWhereRaw(DB::raw("DATE(booking.booking_time) >= '".date('y-m-d' ,strtotime($q))."' AND DATE(booking.booking_time) <= '".date('y-m-d')."'"));
                }elseif($p == 'id' && $q != ''){
                    $query ->   whereRaw('LOWER(booking.id) like ?', '%'.str_replace('bkid','',strtolower($q)).'%' );
                }elseif ($p == 'driver' && $q != '') {
                    $query -> orWhereRaw('LOWER(drivers.name) like ?', '%'.strtolower($q).'%');
                }elseif ($p == 'customer' && $q != '') {
                    $query -> orWhereRaw('LOWER(customers.name) like ?', '%'.strtolower($q).'%') ;
                }
                elseif ($p == 'booking' && $q != '') {
                    $query -> orWhereRaw(DB::raw("DATE(booking.booking_time) = '".date('y-m-d' ,strtotime($q))."'"));
                }
                elseif ($p == 'from_date'  && $q != '') {
                    $query -> orWhereRaw(DB::raw("DATE(booking.booking_time) >= '".date('y-m-d' ,strtotime($q))."' AND DATE(booking.booking_time) <= '".date('y-m-d')."'"));
                }

                if($status != ''){
                    $query->where('booking.booking_status' , $status);
                }

                if($type != 'all'){
                    $btype = ($type == 'schedule') ? 1 : 0;
                    $query->where('booking.schedule_booking' , $btype);
                }

            })
        ->count();
        return $bookings;
    }


    public function searchFare(Request $request,$status='')
    {
        $p  = $request->p;    // for filed name  
        $q  = $request->q;    // searched string
        $type  = $request->type;   // booking type
        $bookings = DB::table('booking')
        // ->select(DB::Raw('SUM(final_amount) as totalFare'))
        ->leftJoin('customers', 'booking.customer_id', '=', 'customers.id')
        ->leftJoin('drivers', 'booking.driver_id', '=', 'drivers.id')
        ->leftJoin('vehicle_category', 'vehicle_category.id', '=', 'booking.vehicle_id')
        ->whereNull('booking.deleted_at')
        ->where('booking.driver_id','!=',0)
        ->where('booking.booking_status' , 'completed')
        ->orderby('booking.id' , 'DESC' )
        ->where(function($query) use ($p,$q,$type,$status) {
                if (empty($p)) {
                    $query -> whereRaw('LOWER(booking.id) like ?', '%'.str_replace('bkid','',strtolower($q)).'%' );
                    $query -> orWhereRaw('LOWER(drivers.name) like ?', '%'.strtolower($q).'%');
                    $query -> orWhereRaw('LOWER(customers.name) like ?', '%'.strtolower($q).'%');
                    $query -> orWhereRaw('LOWER(booking.booking_time) like ?', '%'.strtolower($q).'%');
                    $query -> orWhereRaw(DB::raw("DATE(booking.booking_time) = '".date('y-m-d' ,strtotime($q))."'"));
                    $query -> orWhereRaw(DB::raw("DATE(booking.booking_time) >= '".date('y-m-d' ,strtotime($q))."' AND DATE(booking.booking_time) <= '".date('y-m-d')."'"));
                }elseif($p == 'id'){
                    $query ->   whereRaw('LOWER(booking.id) like ?', '%'.str_replace('bkid','',strtolower($q)).'%' );
                }elseif ($p == 'driver') {
                    $query -> orWhereRaw('LOWER(drivers.name) like ?', '%'.strtolower($q).'%');
                }elseif ($p == 'customer') {
                    $query -> orWhereRaw('LOWER(customers.name) like ?', '%'.strtolower($q).'%') ;
                }
                elseif ($p == 'booking') {
                    $query -> orWhereRaw(DB::raw("DATE(booking.booking_time) = '".date('y-m-d' ,strtotime($q))."'"));
                }
                elseif ($p == 'from_date'  && $q != '') {
                    $query -> orWhereRaw(DB::raw("DATE(booking.booking_time) >= '".date('y-m-d' ,strtotime($q))."' AND DATE(booking.booking_time) <= '".date('y-m-d')."'"));
                }

                if($status != 'all'){
                    $query->where('booking.booking_status' , $status);
                }

                if($type != 'all'){
                    $btype = ($type == 'schedule') ? 1 : 0;
                    $query->where('booking.schedule_booking' , $btype);
                }

            })
        ->sum('final_amount');
        return $bookings;
    }



    public function invoice(Request $request)
    {   
        $this->authorize('invoice', Booking::class);
     
          try {
               $id = decrypt($request->id);
             $invoice = DB::table('booking')
            ->select( 'booking.id' ,'booking.pickup_lat','booking.pickup_long','booking.drop_lat','booking.drop_long', 'drivers.id as driver_id' , 'drivers.mobile as driver_mobile' , 'customers.id as rider_id' ,'customers.name as rider_name' , 'customers.mobile as rider_mobile','booking.booking_time' , 'booking.start_time' , 'booking.pickup_addrees' , 'booking.destination_address' , 'booking.total_amount' , 'booking.tax_amount' , 'booking.discount_amount' , 'booking.final_amount' , 'vehicles.per_km_charge as per_km_charges', 'booking.actual_distance' , 'booking.estimated_distance' ,'vehicle_category.basefare' , 'drivers.name as  driver_name' , 'booking.completed_time' , 'booking.estimated_distance as distance','booking.booking_status')
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
}
