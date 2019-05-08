<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DriverLatLong;

class ShareRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {  $booking_id= $request->id;
       $data['booking_id']=$booking_id;
       return view('share_route',$data);
    }

    public function getRoutes(Request $request)
    {  
    	//$booking_id = 109;

        //$id = 5593;
        $booking_id= $request->id;
        /*  $id = $request->id;
        $limit = $request->limit ;*/
      	/*$booking_id = $request->input('booking_id');
    	$id = $request->input('id');*/
    	$data= DriverLatLong::select('id','latitude as lat','longitude as long')->where('booking_id',$booking_id)->orderBy('id','desc')->first();
        //->where('is_active','1')
        return $data;
    }

 
}
