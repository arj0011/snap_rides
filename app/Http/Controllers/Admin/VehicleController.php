<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Vehicle;
use App\Driver;
use DB;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
          $this->authorize('create', Driver::class);
       try {
            $vehicles = DB::table('vehicles')
            ->select('vehicles.id' ,'vehicle_category.basefare' , 'vehicles.is_active' , 'vehicle_category.name as vehicle_category' , 'vehicles.registration_number' , 'vehicles.insurance_number' , 'drivers.id as driver_id')
            ->selectRaw("drivers.name as driver_name")
            ->whereNull('vehicles.deleted_at')
            ->leftJoin('drivers', 'vehicles.driver_id', '=', 'drivers.id')
            ->leftJoin('vehicle_category', 'vehicles.vehicle_category', '=', 'vehicle_category.id')
            ->orderby('vehicles.id','DESC')
            ->paginate('10');

            return view('admin.vehicles.index',compact('vehicles'))->with('i', ($vehicles->currentpage()-1)*$vehicles->perpage()+1);
       

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

    public function search(Request $request){
           
           $this->authorize('create', Driver::class);
            try {
            $action  = $request->p;      
            $string  = $request->q;
            $vehicles = DB::table('vehicles')
                ->select('vehicles.id' ,'vehicle_category.basefare' , 'vehicles.is_active' , 'vehicle_category.name as vehicle_category' , 'vehicles.registration_number' , 'vehicles.insurance_number' , 'drivers.id as driver_id')
                ->selectRaw("drivers.name as driver_name")
                ->whereNull('vehicles.deleted_at')
                ->leftJoin('drivers', 'vehicles.driver_id', '=', 'drivers.id')
                ->leftJoin('vehicle_category', 'vehicles.vehicle_category', '=', 'vehicle_category.id')
                  ->where(function($query) use ($action,$string) {
                    if (empty($action)) {
                        $query -> whereRaw('LOWER(vehicles.registration_number) like ?', '%'.strtolower($string).'%');
                        $query -> orWhereRaw('LOWER(vehicles.insurance_number) like ?', '%'.strtolower($string).'%');
                        $query -> orWhereRaw('LOWER(drivers.name) like ?', '%'.strtolower($string).'%') ;
                    }elseif($action == 'reg'){
                        $query -> whereRaw('LOWER(vehicles.registration_number) like ?', '%'.strtolower($string).'%');
                    }elseif ($action == 'ins') {
                        $query -> whereRaw('LOWER(vehicles.insurance_number) like ?', '%'.strtolower($string).'%');
                    }elseif ($action == 'dri') {
                        $query -> whereRaw('LOWER(drivers.name) like ?', '%'.strtolower($string).'%');
                    }
                })
               ->paginate('10');
            return view('admin.vehicles.index',compact('vehicles','string','action'))->with('i', ($vehicles->currentpage()-1)*$vehicles->perpage()+1);
       

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $this->authorize('create', Driver::class);

        try {
            $vehicle_categories = DB::table('vehicle_category')
                                 ->select( 'id' , 'name' , 'basefare')
                                 ->where('is_active' , '=' , '1' )
                                 ->whereNull('deleted_at')
                                 ->get();

            $vehicle_model = DB::table('vehicle_cat_model')
                                 ->select( 'id' , 'name')
                                 ->get();                     
            $arr[] = '';

            $driverAssign   = DB::table('vehicles')->select('driver_id')->get();
            if(!empty($driverAssign)){
                foreach ($driverAssign as $key => $value) {
                          $arr[] =  $value->driver_id;
                }
           }

            $drivers = DB::table('drivers')
                                ->select('drivers.id' , 'drivers.name as driver_name')
                                ->whereNotIn('drivers.id' , $arr)
                                ->get();


            return view('admin.vehicles.add',compact(['vehicle_categories' , 'drivers','vehicle_model'])); 

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
         $this->authorize('create', Driver::class);

          $request->validate([
            'vehicle_category' => 'required',   
            'vehicle_name' => 'required',  
            'registration_number' => 'required|unique:vehicles,registration_number,NULL,id,deleted_at,NULL',
            'color' => 'required',
            // 'per_km_charge' => 'required|numeric',
            'vehicle_driver' => 'required'
        ]);

        try {

         $vehicle_data = array(
            'vehicle_category' =>  ($request->vehicle_category),   
            'vehicle_name' => $request->vehicle_name,
            'registration_number' => strtolower($request->registration_number),
            'color' => $request->color,
            'insurance_number' => strtolower($request->insurance_number),
            // 'per_km_charge' => $request->per_km_charge,
            'driver_id' => decrypt($request->vehicle_driver),
            'car_year'=>$request->car_year,
            'created_at' =>  \Carbon\Carbon::now(), 
            'updated_at' => \Carbon\Carbon::now()
            );


        if ($request->hasFile('vehicle_image')) {
                    $imageName = str_random(10).time().'.'.$request->vehicle_image->getClientOriginalExtension(); 
                    $request->vehicle_image->move('Admin/vehicleImage', $imageName);
                    $vehicle_data['vehicle_image'] = $imageName;
        }

        $insertID = DB::table('vehicles')->insertGetId($vehicle_data);
        if($insertID){
                
                    DB::table('drivers') 
                            ->where('id', '=' ,decrypt($request->vehicle_driver))
                            ->update(['is_vehicle_registered' => '1']);

                  return Redirect::route('driver/store-documents' , ['id' => $request->vehicle_driver])->with('msg','Successfully registered Vehicle information')->with('color' , 'success');
        }
        else{
             if ($request->hasFile('vehicle_image') && file_exists('Admin/vehicleImage/'.$imageName)) {
                unlink('Admin/vehicleImage', $imageName);
            }
            return Redirect::back()->with('msg','Failed to add vehicle')->with('color' , 'danger');
        }

   } catch ( \Exception $e) {
            return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
    }

}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {    

          $this->authorize('view', Driver::class);

        try {
         $vehicle = DB::table('vehicles')
            ->select('vehicles.id' ,'vehicles.registration_number' , 'vehicles.is_active' , 'vehicle_category.basefare' ,  'vehicles.color' , 'vehicles.insurance_number' , 'vehicles.driver_id' , 'drivers.name as driver_name' , 'vehicle_category.name as vehicle_category' , 'vehicles.vehicle_image' , 'vehicles.vehicle_name' , 'vehicles.created_at as registration_date' , 'drivers.id as driver_id')
            ->where('vehicles.id' , '=' , decrypt($request->id))
            ->leftJoin('drivers', 'vehicles.driver_id', '=', 'drivers.id')
            ->leftJoin('vehicle_category', 'vehicles.vehicle_category', '=', 'vehicle_category.id')
            ->first();
        return view('admin.vehicles.vehicle',compact('vehicle')); 
       

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request) 
    {   
        $this->authorize('update', Driver::class);

        $vehicle_categories = DB::table('vehicle_category')
                             ->select( 'id' , 'name' , 'basefare')
                             ->where('is_active' , '=' , '1' )
                             ->whereNull('deleted_at')
                             ->get();

        try {

            $drivers = DB::table('drivers')
                                ->select('id' , 'name as driver_name')
                                ->get();
                                 
            $vehicle = DB::table('vehicles')
                ->select('vehicles.id', 'vehicles.vehicle_category' , 'vehicles.vehicle_name' , 'vehicles.vehicle_image' ,'vehicles.registration_number' , 'vehicles.is_active'  ,  'vehicles.color' , 'vehicles.insurance_number' , 'vehicles.driver_id')
                ->where('vehicles.id' , '=' , decrypt($request->id))
                ->first();
              
                return view('admin.vehicles.edit',compact(['vehicle','vehicle_categories' , 'drivers'])); 
       

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
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
        $this->authorize('update', Driver::class);
           
        if( isset($request->vehicle_id) && !empty($request->vehicle_id)){
            $vehicle_id = decrypt($request->vehicle_id);
            $request->validate([
                'vehicle_category' => 'required',  
                'vehicle_name' => 'required', 
               'registration_number' => 'required|unique:vehicles,registration_number,'. $vehicle_id.',id,deleted_at,NULL',
            
                'color' => 'required'
                // 'per_km_charge' => 'required|numeric'
            ]);
        
       }else{
        
          $request->validate([
                'vehicle_category' => 'required',  
                'vehicle_name' => 'required', 
                'registration_number' => 'required|unique:vehicles',
                'color' => 'required'
                // 'per_km_charge' => 'required|numeric'
            ]);
       }
  
        try {
 
            $vehicle_data = array(
            'vehicle_category' =>$request->vehicle_category,  
            'vehicle_name' => $request->vehicle_name, 
            'registration_number' => strtolower($request->registration_number),
            // 'per_km_charge' => $request->per_km_charge,
            'color' => $request->color,
            'insurance_number' => strtolower($request->insurance_number),
            'car_year'=>$request->car_year
            );

            if( isset($request->vehicle_id) && !empty($request->vehicle_id)){
                $vehicle_data['updated_at'] =  \Carbon\Carbon::now();
            }else{
                $vehicle_data['driver_id'] =  decrypt($request->driver_id);
                $vehicle_data['created_at'] =  \Carbon\Carbon::now();
                $vehicle_data['updated_at'] =  \Carbon\Carbon::now();
            }

            if($request->hasFile('vehicle_image')) {
                        $imageName = str_random(10).time().'.'.$request->vehicle_image->getClientOriginalExtension(); 
                        $request->vehicle_image->move('Admin/vehicleImage', $imageName);
                        $vehicle_data['vehicle_image'] = $imageName;
            }
            
            if( isset($request->vehicle_id) && !empty($request->vehicle_id)){
                $status = DB::table('vehicles') 
                ->where('id', '=' ,decrypt($request->vehicle_id))
                ->update($vehicle_data);
            }else{
                $status = DB::table('vehicles')->insertGetId($vehicle_data);
            }


            $redirects_to = $request->redirects_to; // for redirect to perticular previews page
            
            $status = true;
            
            if($status){
                if($request->hasFile('vehicle_image') && file_exists('Admin/vehicleImage/'.$request->old_vehicle_image)){
                          @unlink('Admin/vehicleImage/'.$request->old_vehicle_image);
                }

               
                return redirect($redirects_to)->with('msg','Successfully updated vehicle!')->with('color' , 'success');
            }else{
                if($request->hasFile('vehicle_image') && file_exists('Admin/vehicleImage/'.$request->vehicle_image)){
                          @unlink('Admin/vehicleImage/'.$request->vehicle_image);
                }
                return Redirect::back($redirects_to)->with('msg','Failed to update vehicle!')->with('color' , 'danger');
                
            }

            
       
        } catch ( \Exception $e) {
            $redirects_to = $request->redirects_to; 

            return Redirect::back($redirects_to)->with('msg',$e->getMessage())->with('color' , 'danger');
               
           
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
          $this->authorize('delete', Driver::class);

        try {
       
        $id = decrypt($request->id);

          $vehicle = Vehicle::find($id);
          if($vehicle->delete()){
                return redirect('vehicles')->with('msg','Successfully deleted vehicle record!')->with('color' , 'success');
            }
            else{
                return Redirect::back()->with('msg','Failed to delete vehicle record')->with('color' , 'danger');
            }

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }

    }

}
