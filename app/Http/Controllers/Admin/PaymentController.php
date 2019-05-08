<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;
use App\Payment;
use App\Booking;
use App\Customer;
use App\Setting;
use App\Category;
use DB;
use Session;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 

     */
    public function index(Request $request)
    {  
        $this->authorize('index', Payment::class);

        try {
            
            $payments = DB::table('payments')
            ->select( 'payments.id','payments.payment_status','payments.amount','payments.customer_id as rider_id','payments.driver_id','drivers.name as driver_name','customers.name as rider_name', 'customers.mobile','booking.booking_time', 'booking.pickup_addrees' , 'booking.destination_address' , 'booking.final_amount' , 'drivers.name as driver_name','booking.id as booking_id','booking.booking_status','payments.created_at')
            ->leftJoin('booking', 'payments.booking_id', '=', 'booking.id')
            ->leftJoin('customers', 'payments.customer_id', '=', 'customers.id')
            ->leftJoin('drivers', 'payments.driver_id', '=', 'drivers.id')
            ->whereNull('booking.deleted_at')
            ->where('payments.payment_status','!=',0)
            ->where('booking.driver_id','!=',0)
            ->orderby('payments.id' , 'DESC' )
            
            ->paginate('10');
            return view('admin.payments.index',compact('payments','filter'))->with('i', ($payments->currentpage()-1)*$payments->perpage()+1);
            
        } catch ( \Exception $e) {
            print_r($e->getMessage());die;
            return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
        }
    }

    public function search(Request $request){
       
       $this->authorize('index', Payment::class);

        try {

        $p  = $request->p;    // for filed name  
        $q  = $request->q;    // searched string

        $payments = DB::table('payments')
            ->select( 'payments.id','payments.payment_status','payments.amount','payments.customer_id as rider_id','payments.driver_id','drivers.name as driver_name','customers.name as rider_name', 'customers.mobile','booking.booking_time', 'booking.pickup_addrees' , 'booking.destination_address' , 'booking.final_amount' , 'drivers.name as driver_name','booking.id as booking_id','booking.booking_status','payments.created_at')
            ->leftJoin('booking', 'payments.booking_id', '=', 'booking.id')
            ->leftJoin('customers', 'payments.customer_id', '=', 'customers.id')
            ->leftJoin('drivers', 'payments.driver_id', '=', 'drivers.id')
            ->whereNull('booking.deleted_at')
            ->where('payments.payment_status','!=',0)
            ->where('booking.driver_id','!=',0)
            ->orderby('payments.id' , 'DESC' )
            ->where(function($query) use ($p,$q) {
                if (empty($p)  && $q != '') {
                    $query -> whereRaw('LOWER(booking.id) like ?', '%'.str_replace('bkid','',strtolower($q)).'%' );
                    $query -> orWhereRaw('LOWER(drivers.name) like ?', '%'.strtolower($q).'%');
                    $query -> orWhereRaw('LOWER(customers.name) like ?', '%'.strtolower($q).'%');
                    $query -> orWhereRaw('LOWER(booking.booking_time) like ?', '%'.strtolower($q).'%');
                    $query -> orWhereRaw(DB::raw("DATE(booking.booking_time) = '".date('y-m-d' ,strtotime($q))."'"));
                    $query -> orWhereRaw(DB::raw("DATE(payments.created_at) >= '".date('y-m-d' ,strtotime($q))."' AND DATE(payments.created_at) <= '".date('y-m-d')."'"));
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
                    $query -> orWhereRaw(DB::raw("DATE(payments.created_at) >= '".date('y-m-d' ,strtotime($q))."' AND DATE(payments.created_at) <= '".date('y-m-d')."'"));
                }
            })
           ->paginate('10');

        return view('admin.payments.index',compact('payments','p','q'))->with('i', ($payments->currentpage()-1)*$payments->perpage()+1);
            
        } catch ( \Exception $e) {
            print_r($e->getMessage());die;
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }


}
