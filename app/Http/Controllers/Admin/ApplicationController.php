<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Setting;
use App\AppCity;
use DB;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {    
         $this->authorize('index', Setting::class);

        try {
            $settings = DB::table('setting')->get();
         
            return view('admin.settings.index',compact('settings'));

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }
    public function list() {    
        $appcity = AppCity::get();
        return view('admin.settings.city',compact('appcity'));
    }
    public function updateCity(Request $request){
        $city = $request->city;
        $obj = new AppCity; 
        $obj->name = $city;
        $obj->save();
        $insertedId = $obj->id;
        $array =array('success'=>1,'id'=>$insertedId);
        return $array; 
    }
    public function deletecity(Request $request){
        $id = $request->id;
        AppCity::where('id',$id)->delete();

        $array =array('success'=>1 );
        return $array; 
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {  
         $this->authorize('update', Setting::class);

        try {

               $q1 = DB::table('setting')->where('key' , '=' , 'base_km')->update(['value' => $request->setting_base_fare_km]);
               
               $q3 = DB::table('setting')->where('key' , '=' , 'driver_request_timeout')->update(['value' => $request->setting_time_out]);
               
               // $q5 = DB::table('setting')->where('key' , '=' , 'wait_charge_permin')->update(['value' => $request->wait_charge_permin]);
               
               //Commissions on ride
               $q6 = DB::table('setting')->where('key' , '=' , 'sedan_commission')->update(['value' => $request->sedan_commission]);
               $q7 = DB::table('setting')->where('key' , '=' , 'seater7_commission')->update(['value' => $request->seater7_commission]);
               $q8 = DB::table('setting')->where('key' , '=' , 'delux_commission')->update(['value' => $request->delux_commission]);

               //Drivers Payouts
               $q9 = DB::table('setting')->where('key' , '=' , 'sedan_driver_payouts')->update(['value' => $request->sedan_driver_payouts]);
               $q10 = DB::table('setting')->where('key' , '=' , 'seater7_driver_payouts')->update(['value' => $request->seater7_driver_payouts]);
               $q11 = DB::table('setting')->where('key' , '=' , 'delux_driver_payouts')->update(['value' => $request->delux_driver_payouts]);

    return Redirect::back()->with('msg','Successfully changed setting')->with('color' , 'success');
             
        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
