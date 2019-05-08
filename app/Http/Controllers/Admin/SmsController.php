<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Sms;
use DB;

class SmsController extends Controller
{
    
    public function create(){

          $this->authorize('create', Sms::class);
          /*
          $cities = DB::table('cities')->select('cities.id' , 'cities.name')
                                       ->join('drivers' , 'drivers.city' , '=' , 'cities.id')
                                       ->groupBy('cities.id')
                                       ->get();
         */
          $drivers =  DB::table('drivers')
              ->select('drivers.id' , 'drivers.name')
              ->whereNull('drivers.deleted_at')
              ->where('is_active',"=","1")    
              ->get();    
          return view('admin.sms.add' , compact('drivers'));
    }

    public function getUsers(Request $request){
         $cities   = $request->cities;
         $users = DB::table('drivers')->select('id' , 'name')
                                      ->whereIn('city', $cities)
                                      ->get();
         $html = '';

	          foreach ($users as $user) {
	                $html .= '<option value='.$user->id.'>'.$user->name.'</option>';
	          }
            
	          echo $html;
    }
}
