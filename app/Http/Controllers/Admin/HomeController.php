<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Booking;
use App\Driver;
class HomeController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth');
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()

    {   
      
        $data['complitedTrip'] =  DB::table('booking')
                       ->where('booking_status' , '=' , 'completed')
                       ->wherenull('deleted_at')
                       ->count();

        $data['acceptTrip'] = DB::table('booking')
                      ->where('booking_status' , '=' , 'accept')
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
          
         $data['bookings'] = DB::table('booking')->select('booking.id','booking.booking_status', 'drivers.name as driver_name' , 'customers.name as customer_name' ,'customers.mobile', 'booking.booking_time','booking.schedule_booking')
                           ->leftJoin('drivers' , 'drivers.id' , '=' ,  'booking.driver_id')
                           ->leftJoin('customers' , 'customers.id' , '=' ,  'booking.customer_id')
                           ->wherenull('booking.deleted_at')
                           ->where('booking.driver_id','!=',0)
                           ->orderBy('booking.id','DESC')
                           ->take(10)
                           ->get();

          $data['drivers'] = DB::table('drivers')
                              ->select('name' , 'mobile' , 'email' , 'is_active')
                              ->wherenull('deleted_at')
                              ->where('is_active' , '=' , 1 )
                              ->orderBy('id','DESC')
                              ->take(10)->get();

        return view('admin.dashboard' , compact('data'));
    }

    public function changePassword(){
        return view('admin.changepassword');
    }

    public function updatePassword(Request $request){
           $items = $request->validate([
          'oldPassword' => 'required',
          'newPassword' => 'required',
          'cPassword'=> 'required',
        ]);

        if (!(Hash::check($request->oldPassword, Auth::user()->password))) {
            return redirect()->back()->with("error",__('words.your_current_password_does_not_matches_with_the_password_you_provided_please_try_again_'));
        }
        elseif(strcmp($request->oldPassword, $request->newPassword) == 0){
            return redirect()->back()->with("error",__('words.new_password_cannot_be_same_as_your_current_password_please_choose_a_different_password'));
        }
        elseif($request->newPassword != $request->cPassword) {
            return redirect()->back()->with("error",__('words.confirm_password_does_not_matches_with_the_new_password'));
        }
        else{
            $user_id = Auth::user()->id;
            $data = array(
                    'password' => Hash::make($request->newPassword),
                );
            $userData = DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);
            if($userData){
                return redirect()->back()->with("success",'Password supdated successfully');       
            }
        }    
      }

       public function getStates(Request $request)
    {
        $states = DB::table('states')
            ->where('country_id', $request->country)
            ->orderBy('name', 'ASC')
            ->get();
        echo json_encode($states);
    }

    public function getCities(Request $request)
    {
        $states = DB::table('cities')
            ->where('state_id', $request->state)
            ->orderBy('name', 'ASC')
            ->get();

        echo json_encode($states);
    }

    public function setStatus(Request $request)
    {   
        
        $fetchStatus = DB::table('drivers')
            ->select($request->column)
            ->where('id', decrypt($request->id))
            ->first();

        $column = $request->column;

        if($fetchStatus->$column)
        {
           $arr = array(
                 $request->column => '0'
           );
        }else{
           $arr = array(
                 $request->column => '1'
           );
        }

        $status = $states = DB::table('drivers')
            ->where('id', decrypt($request->id))
            ->update($arr);
        if($status){
             $response = array('status' => true ,'msg' => 'status changed'); 
        }else{
             $response = array('status' => false ,'msg' => 'failed to change status');
        }       
        echo json_encode($response);
    }

    public function getCharges(Request $request){
          $id = ($request->id);
          $charges = DB::table('vehicle_category')->select('per_km_charges')->where('id' , '=' , $id)->first();
          echo json_encode(['status' => true , 'message' => 'data found' , 'data' => $charges]);
    }
   
}
