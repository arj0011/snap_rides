<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Mail\SendMail;
use App\DriverDocument;
use App\Driver;
use Response;
use Mail;
use DB;
use Config;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {    
         $this->authorize('index', Driver::class);
            
            try {
              $drivers = DB::table('drivers')
              ->select('drivers.id' , 'drivers.mobile','drivers.deleted_at' , 'drivers.email' , 'drivers.is_blocked' , 'drivers.is_active' , 'drivers.is_approved' ,'companies.name as company_name' , 'drivers.name as driver_name' , 'vehicles.id as vehicle_id' , 'driver_documents.id as documents_id' )
              ->whereNull('drivers.deleted_at')
              //->wherenull('drivers.deleted_at')
              ->groupBy('drivers.id')
              ->orderBy('drivers.id','DESC')
              ->leftJoin('companies', 'drivers.company_id', '=', 'companies.id')
              ->leftJoin('vehicles', 'vehicles.driver_id', '=', 'drivers.id')
              ->leftJoin('driver_documents', 'driver_documents.driver_id', '=', 'drivers.id')
              ->paginate('10'); 

              return view('admin.drivers.index',compact('drivers'))->with('i', ($drivers->currentpage()-1)*$drivers->perpage()+1);
              
            } catch ( \Exception $e) {
                return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
            }
    }
    
    public function search(Request $request){

      $this->authorize('index', Driver::class);

          try {
          $p  = $request->p;    // fiels name  
          $q  = $request->q;    // string name that will be searched
          $is_approved  = $request->is_approved;
          $is_active  = $request->is_active;

               $drivers = DB::table('drivers')
              ->select('drivers.id' , 'drivers.mobile' , 'drivers.email' , 'drivers.is_blocked' , 'drivers.is_active' , 'drivers.is_approved' ,'companies.name as company_name' , 'drivers.name as driver_name' , 'vehicles.id as vehicle_id' , 'driver_documents.id as documents_id' )
              ->whereNull('drivers.deleted_at')
              //->wherenull('drivers.deleted_at')
              ->groupBy('drivers.id')
              ->orderBy('drivers.id','DESC')
              ->leftJoin('companies', 'drivers.company_id', '=', 'companies.id')
              ->leftJoin('vehicles', 'vehicles.driver_id', '=', 'drivers.id')
              ->leftJoin('driver_documents', 'driver_documents.driver_id', '=', 'drivers.id')
              ->where(function($query) use ($p,$q) {
                  if (empty($p)) {
                      $query -> whereRaw('LOWER(drivers.name) like ?', '%'.strtolower($q).'%');
                      $query -> orWhereRaw('LOWER(drivers.mobile) like ?', '%'.strtolower($q).'%');
                      $query -> orWhereRaw('LOWER(drivers.email) like ?', '%'.strtolower($q).'%') ;
                      $query -> orWhereRaw('LOWER(companies.name) like ?', '%'.strtolower($q).'%') ;
                  }elseif($p == 'name'){
                      $query -> whereRaw('LOWER(drivers.name) like ?', '%'.strtolower($q).'%');
                  }elseif ($p == 'mobile') {
                      $query -> whereRaw('LOWER(drivers.mobile) like ?', '%'.strtolower($q).'%');
                  }elseif ($p == 'email') {
                      $query -> whereRaw('LOWER(drivers.email) like ?', '%'.strtolower($q).'%');
                  }
                  elseif ($p == 'company') {
                      $query -> whereRaw('LOWER(companies.name) like ?', '%'.strtolower($q).'%');
                  }
              });

          
            if($is_approved!=''){
               $drivers =$drivers->where('drivers.is_approved',$is_approved);
            }
            if($is_active!=''){
              $drivers = $drivers->where('drivers.is_active',$is_active);
            }
             //toSql();
          
          
            $drivers =$drivers->paginate('10') ;
           
          //paginate('10') ;
             
          return view('admin.drivers.index',compact('drivers','p','q','is_approved','is_active'))->with('i', ($drivers->currentpage()-1)*$drivers->perpage()+1); 
              
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

          try{
          $companies = DB::table('companies')
          ->select('id' ,'name')
          ->where('is_active' , '=' , '1')
          ->get();

         $countries = DB::table('countries')
                     ->orderBy('name', 'ASC')
                     ->get();

         /*$discountRange = DB::table('setting')->where( 'key' , '=' , 'driver_discount_range')->first();
         
         $discountRange = $discountRange->value;

         $discountRange =  explode( ',', $discountRange);*/

          // return view('admin.drivers.add',compact('countries','companies' , 'discountRange' ));
          
          return view('admin.drivers.add',compact('countries','companies'));
              
            } catch ( \Exception $e) {
                return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
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

         $empty = '';
          
           $request->validate([
                  'name' => 'required',   
                  'mobile' => 'required|min:8|numeric|unique:drivers,mobile,NULL,id,deleted_at,'.$empty,
                  'email' => 'required|unique:drivers,email,NULL,id,deleted_at,'.$empty,
                  'gender' => 'required',
                  'password' => 'required_with:confirm_password|same:confirm_password|min:6',
                  'confirm_password'=> 'required',
                  'zip_code' => 'max:6|min:6|numeric',
                  'profile_image' => 'image|mimes:jpeg,jpg,png',  
            ]);
          
          try {
           $check_drivermobileexist  = DB::table("drivers")->where('mobile','=',$request->mobile)->where('is_active','=','1')->whereNull('deleted_at')->first(); 
           
           $check_driveremailexist   = DB::table("drivers")->where('email','=' ,$request->email)->where('is_active','=','1')->whereNull('deleted_at')->first(); 
           if(empty($check_drivermobileexist->mobile)){
           if(empty($check_driveremailexist->email)){ 

           $driver_data = array(
              'name' => strtolower($request->name),   
              'mobile' => $request->mobile,
              'email' => $request->email,
              'gender' => $request->gender,
              'password' => Hash::make($request->password),
              'country' => $request->country,
              'state' => $request->state,
              'city' => $request->city,
              'address' => strtolower($request->address),
              'zip_code' => $request->zcode,
//              'company_id' => $request->company,
              'is_registered' => '1',
              'created_at' =>  \Carbon\Carbon::now(), 
              'updated_at' => \Carbon\Carbon::now()
              );
                
            if(isset($request->dob) && !empty($request->dob)){
              $dob = date('Y-m-d',strtotime($request->dob));
              $driver_data['dob'] = $dob;  
            }
            
            if(isset($request->identity_no) && !empty($request->identity_no)){
              $driver_data['identity_no'] = $request->identity_no;  
            }  

              if ($request->hasFile('profile_image')) {
                     $profileImg = str_random(10).'-'.time().'.'.request()->profile_image->getClientOriginalExtension(); 
                         request()->profile_image->move('Admin/profileImage', $profileImg);
                    $driver_data['profile_image'] = $profileImg;
               }
               
              $insertID = DB::table('drivers')->insertGetId($driver_data);
              $invite_code = $request->name.$insertID;          
              $updateinvitecode=Driver::where('id', $insertID)->update(['invite_code' =>$invite_code ]);
              if($insertID){

                   $data = array(
                                 'name'          => $request->name,
                                 'toMail'        => $request->email,
                                 'password'      =>  $request->password,
                                 'username'      => $request->username,
                                ); 

                   Mail::send( 'admin.emails.driver_register' ,  $data ,function ($message) use ($data){
                         $message->to($data['toMail'])->subject('Welcome to Snap Rides App');
                         $message->from('support@snaprides.co.za','Snap Rides');
                   });

                   return Redirect::route('vehicle/create' , [ 'id' => encrypt($insertID) ])->with('msg','Driver Successfully Added')->with('color' , 'success');
              }else{
                   if(isset($profileImg) && !empty($profileImg)){
                      if ($request->hasFile('profile_image') && file_exists('Admin/profileImage/'.$profileImg)) {
                        unlink('Admin/profileImage/'.$profileImg);
                      }
                    }
                  return Redirect::back()->with('msg','Failed to add driver')->with('color' , 'danger');  
              }
             }
             else{
              return Redirect::back()->with('msg','Email already exist.')->with('color' , 'danger');   
             }
              }
             else{
              return Redirect::back()->with('msg','Mobile already exist.')->with('color' , 'danger');   
             }
            } catch ( \Exception $e) {
                  if(isset($profileImg) && !empty($profileImg)){
                       if ($request->hasFile('profile_image') && file_exists('Admin/profileImage/'.$profileImg)) {
                          unlink('Admin/profileImage/'.$profileImg);
                     }
                  }
                return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
            }
         
    }

    function randomString() {
      $str = "";
      $characters = array_merge(range('A','Z'),range('0','9'));
      $max = count($characters) - 1;
      for ($i = 0; $i < 8; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
      }
      return $str;
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
          $driver = DB::table('drivers')
              ->select('drivers.id', 'drivers.name as driver_name','drivers.dob','drivers.identity_no', 'drivers.gender' , 'drivers.address','drivers.is_active' , 'drivers.is_blocked' ,'drivers.is_approved' ,'drivers.profile_image' , 'drivers.zip_code', 'drivers.password' , 'drivers.mobile' , 'drivers.email' , 'countries.name as country_name','states.name as state_name',  'cities.name as city_name' , 'driver_documents.driving_licence' , 'driver_documents.vehicle_registration' , 'driver_documents.insurance as vehicle_insurance' , 'driver_documents.id_proof' , 'driver_documents.id_verification' , 'driver_documents.lince_verification' , 'driver_documents.reg_verification' , 'driver_documents.ins_verification' ,'drivers.created_at' ,'vehicles.registration_number' ,'vehicles.car_year', 'vehicles.is_active as vehicle_status' , 'vehicle_category.basefare' ,'vehicle_category.per_km_charges',  'vehicles.color' , 'vehicles.insurance_number' , 'vehicles.driver_id' , 'drivers.name as driver_name' , 'vehicle_category.name as vehicle_category' , 'vehicles.vehicle_image' , 'vehicles.vehicle_name' , 'vehicles.created_at as registration_date' , 'drivers.allow_discount' , 'drivers.discount' )
              ->selectRaw("AVG(reviews.star)  as rating")
              ->where('drivers.id','=',decrypt($request->id))
              ->leftJoin('countries', 'drivers.country', '=', 'countries.id')
              ->leftJoin('states', 'drivers.state', '=', 'states.id')
              ->leftJoin('cities', 'drivers.city', '=', 'cities.id')
              ->leftJoin('driver_documents', 'drivers.id', '=', 'driver_documents.driver_id')
              ->leftJoin('reviews', 'drivers.id', '=', 'reviews.driver_id')
              ->leftJoin('vehicles', 'vehicles.driver_id', '=', 'drivers.id')
              ->leftJoin('vehicle_category', 'vehicles.vehicle_category', '=', 'vehicle_category.id')
              ->first();

          return view('admin.drivers.driver',compact('driver')); 
              
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
        /*  try {*/
        $companies = DB::table('companies')
        ->select('id' ,'name')
        ->where('is_active' , '=' , '1')
        ->get();

        $countries = DB::table('countries')
        ->orderBy('name', 'ASC')
        ->get();

        $charge_between =  DB::table('setting')
        ->select('key','value')
        ->where('key' , '=' , 'charge_between')
        ->first();
        
        $arr = explode(',', $charge_between->value);
        $charge_between->staring = $arr[0];
        $charge_between->ending = $arr[1];

        $vehicle_model = DB::table('vehicle_cat_model')
                         ->select( 'id' , 'name')
                         ->get(); 

        // $discountRange = DB::table('setting')->where( 'key' , '=' , 'driver_discount_range')->first();

        // $discountRange = $discountRange->value;

        // $discountRange =  explode( ',', $discountRange);
      
        $driver = DB::table('drivers')
              ->select('drivers.id', 'drivers.name','drivers.dob','drivers.identity_no', 'drivers.gender' , 'drivers.address', 'drivers.profile_image', 'doc.driving_licence',  'drivers.zip_code' , 'drivers.mobile' , 'drivers.email' , 'companies.id as company_id' ,'countries.id as country_id', 'countries.name as country_name','states.id as state_id', 'states.name as state_name', 'cities.id as city_id' , 'cities.name as city_name' , 'doc.driving_licence' , 'doc.id_proof' ,'doc.vehicle_registration','doc.insurance as vehicle_insurance' , 'vehicles.vehicle_category' , 'vehicles.vehicle_name' , 'vehicles.vehicle_image' ,'vehicles.registration_number' ,'vehicles.car_year', 'vehicles.is_active'  ,  'vehicles.color' , 'vehicles.insurance_number' , 'doc.id as documents_id' , 'vehicles.id as vehicle_id' ,'vehicles.per_km_charge' ,'drivers.discount' , 'drivers.allow_discount' )
              ->where('drivers.id','=',decrypt($request->id))
              ->leftJoin('countries', 'drivers.country', '=', 'countries.id')
              ->leftJoin('states', 'drivers.state', '=', 'states.id')
              ->leftJoin('cities', 'drivers.city', '=', 'cities.id')
              ->leftJoin('companies', 'drivers.company_id', '=', 'companies.id')
              ->leftJoin('vehicles', 'vehicles.driver_id', '=', 'drivers.id')
              ->leftJoin('driver_documents as doc', 'doc.driver_id', '=', 'drivers.id')
              ->first();

              $data = $driver->vehicle_category;

              $vehicle_categories = DB::table('vehicle_category')
                 ->select( 'id' , 'name' , 'basefare')
                 ->where('is_active' , '=' , '1' )
                 ->whereNull('deleted_at')
                 ->where(function($query) use ($data) {
                   if (!empty($data)) {
                       $query -> where('id' , '!=' , $data );
                    }
                  })
                 ->get();

              if(!empty($data)){
                $assign_category = DB::table('vehicle_category')
                  ->select( 'id' , 'name' , 'basefare')->where('id' , '=' , $data)->first();
              }

          /*return view('admin.drivers.edit',compact(['countries','driver','companies' , 'vehicle_categories','charge_between', 'assign_category' , 'discountRange','vehicle_model']));*/

          return view('admin.drivers.edit',compact(['countries','driver','companies' , 'vehicle_categories','charge_between', 'assign_category','vehicle_model'])); 
              
           /* } catch ( \Exception $e) {
                return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
            }*/
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

       $empty = '';

          $request->validate([
              'name' => 'required|max:25|min:2',   
              'mobile' => 'required|max:18|min:8|unique:drivers,mobile,'.decrypt($request->id).',id,deleted_at,'.$empty,
              'email' => 'required|email|unique:drivers,email,'.decrypt($request->id).',id,deleted_at,'.$empty,
              'gender' => 'required|numeric',
              
              'zip_code' => 'max:6|min:6|numeric',
              'profile_image' => 'image|mimes:jpeg,jpg,png',

            ]);

         try {
             $check_drivermobileexist  = DB::table("drivers")->where('mobile','=',$request->mobile)->where('is_active','=','1')->where('id' , '!=' , decrypt($request->id))->whereNull('deleted_at')->first(); 
           
           $check_driveremailexist   = DB::table("drivers")->where('email','=' ,$request->email)->where('id' , '!=' , decrypt($request->id))->where('is_active','=','1')->whereNull('deleted_at')->first(); 
           if(empty($check_drivermobileexist->mobile)){
           if(empty($check_driveremailexist->email)){  
            $driver_data = array(
                'name' => $request->name,   
                'mobile' => $request->mobile,
                'email' => $request->email,
                'gender' => $request->gender,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'address' => $request->address,
                'zip_code' => $request->zcode,
                'updated_at' => \Carbon\Carbon::now()
            );

                if ($request->hasFile('profile_image')) {
                     $profileImg = str_random(10).'-'.time().'.'.request()->profile_image->getClientOriginalExtension(); 
                    request()->profile_image->move('Admin/profileImage', $profileImg);
                    $driver_data['profile_image'] = $profileImg;
                }

                if(isset($request->dob) && !empty($request->dob)){
                  $dob = date('Y-m-d',strtotime($request->dob));
                  $driver_data['dob'] = $dob;  
                }
                
                if(isset($request->identity_no) && !empty($request->identity_no)){
                  $driver_data['identity_no'] = $request->identity_no;  
                } 

               $status =  DB::table('drivers')->where('id' , '=' , decrypt($request->id))->update($driver_data);

               $redirects_to = $request->redirects_to; // for redirect to perticular page previws 
                
                if($status){
                    if ($request->hasFile('profile_image') && file_exists('Admin/profileImage/'.$profileImg) && file_exists('Admin/profileImage/'.$request->old_profile_image)){
                       unlink('Admin/profileImage/'.$request->old_profile_image);
                    }
                  
                  return redirect($redirects_to)->with('msg','Driver Successfully updated')->with('color' , 'success');
                }else{
                        if ($request->hasFile('profile_image') && file_exists('Admin/profileImage/'.$profileImg)) {
                           unlink('Admin/profileImage/'.$profileImg);
                       }

                    return redirect($redirects_to)->with('msg','Failed to update driver')->with('color' , 'danger');
                }   
                   }
             else{
              return Redirect::back()->with('msg','Email already exiist.')->with('color' , 'danger');   
             }
              }
             else{
              return Redirect::back()->with('msg','Mobile already exiist.')->with('color' , 'danger');   
             }  
                } catch (\Exception $e) {

                       if ($request->hasFile('profile_image') && file_exists('Admin/profileImage/'.$profileImg)) {
                           unlink('Admin/profileImage/'.$profileImg);
                       }

                    return redirect($redirects_to)->with('msg','Something went wrong!')->with('color' , 'warning');
                }
    }

    function storeDocuments(Request $request){

      $this->authorize('create', Driver::class);

       try { 
        return view('admin.drivers.upload_documents')->with( ['id' => decrypt($request->id) ]);
       } catch ( \Exception $e) {
         return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
       }
    }

    function editDocuments(Request $request){
       
       $this->authorize('update', Driver::class);

       try { 
             $driver = DB::table('driver_documents')
                       ->select( 'id' ,'id_proof','driving_licence' ,'vehicle_registration' , 'insurance as vehicle_insurance')
                       ->where('id' , decrypt($request->id) )
                       ->first();
        return view('admin.drivers.update_documents',compact('driver'));
       } catch ( \Exception $e) {
         return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
       }
    }

    public function uploadDocuments(Request $request)
    {
       $this->authorize('update', Driver::class);
        
  
          $request->validate([
             'id_proof' => 'required',
             'driving_licence' => 'required',
             'vehicle_registration' => 'required' 
          ]);

          $document_data = array( 
             'driver_id'  => decrypt($request->driver_id),
             'created_at' =>  \Carbon\Carbon::now(), 
             'updated_at' => \Carbon\Carbon::now()
          );

          if ($request->hasFile('id_proof')) {
             $idProofImg = str_random(10).'-'.time().'.'.request()->id_proof->getClientOriginalExtension(); 
            $path = request()->id_proof->move('Admin/driver_documents', $idProofImg);
            $document_data['id_proof'] = $idProofImg;
          }

          if ($request->hasFile('driving_licence')) {
             $licenceImg = str_random(10).'-'.time().'.'.request()->driving_licence->getClientOriginalExtension(); 
            request()->driving_licence->move('Admin/driver_documents', $licenceImg);
            $document_data['driving_licence'] = $licenceImg;
          }

          if ($request->hasFile('vehicle_registration')) {
             $registrationImg = str_random(10).'-'.time().'.'.request()->vehicle_registration->getClientOriginalExtension(); 
            request()->vehicle_registration->move('Admin/driver_documents', $registrationImg);
            $document_data['vehicle_registration'] = $registrationImg;
          }

          if ($request->hasFile('vehicle_insurance')) {
             $insuranceImg = str_random(10).'-'.time().'.'.request()->vehicle_insurance->getClientOriginalExtension(); 
            request()->vehicle_insurance->move('Admin/driver_documents', $insuranceImg);
            $document_data['insurance'] = $insuranceImg;
          }

          $docStatus = DB::table('driver_documents')->insertGetId($document_data);
          
          if($docStatus){

               DB::table('drivers') 
                            ->where('id', '=' ,decrypt($request->driver_id))
                            ->update(['is_documentation' => '1']);
                            
            /*return redirect('drivers')->with('msg','Document Successfully Uploaded')->with('color' , 'success');*/
          }else{

                if ($request->hasFile('id_proof') && file_exists('Admin/driver_documents/'.$idProofImg)) {
                  unlink('Admin/driver_documents/'.$idProofImg);
                }
                
                if ($request->hasFile('driving_licence') && file_exists('Admin/driver_documents/'.$licenceImg)) {
                  unlink('Admin/driver_documents/'.$licenceImg);
                }

                if ($request->hasFile('vehicle_registration') && file_exists('Admin/driver_documents/'.$registrationImg)) {
                  unlink('Admin/driver_documents/'.$registrationImg);
                }

                if ($request->hasFile('vehicle_insurance') && file_exists('Admin/driver_documents/'.$licenceImg)) {
                  unlink('Admin/driver_documents/'.$licenceImg);
                }

                // return Redirect::back()->with('msg','Failed to uploaded documents')->with('color' , 'danger');  
          }

          return redirect('drivers')->with('msg','Document Successfully Uploaded')->with('color' , 'success');
                      
        
    }
    

    public function updateDocuments(Request $request)
    {
        $this->authorize('update', Driver::class);

 
              
              
              if( isset($request->document_id) && !empty($request->document_id)){
                  $document_data = array( 
                       'updated_at' => \Carbon\Carbon::now()
                  );
              }else{
                   $document_data = array(
                       'driver_id' => decrypt($request->driver_id),
                       'created_at' => \Carbon\Carbon::now(),
                       'updated_at' => \Carbon\Carbon::now()
                   );
              }


              if ($request->hasFile('id_proof')) {
                 $idProofImg = str_random(10).'-'.time().'.'.request()->id_proof->getClientOriginalExtension(); 
                   request()->id_proof->move('Admin/driver_documents', $idProofImg);
                   $document_data['id_proof'] = $idProofImg;
              }

              if ($request->hasFile('driving_licence')) {
                 $licenceImg = str_random(10).'-'.time().'.'.request()->driving_licence->getClientOriginalExtension(); 
                request()->driving_licence->move('Admin/driver_documents', $licenceImg);
                $document_data['driving_licence'] = $licenceImg;
              }

              if ($request->hasFile('vehicle_registration')) {
                 $registrationImg = str_random(10).'-'.time().'.'.request()->vehicle_registration->getClientOriginalExtension(); 
                request()->vehicle_registration->move('Admin/driver_documents', $registrationImg);
                $document_data['vehicle_registration'] = $registrationImg;
              }

              if ($request->hasFile('vehicle_insurance')) {
                 $insuranceImg = str_random(10).'-'.time().'.'.request()->vehicle_insurance->getClientOriginalExtension(); 
                request()->vehicle_insurance->move('Admin/driver_documents', $insuranceImg);
                $document_data['insurance'] = $insuranceImg;
              }

              if( isset($request->document_id) && !empty($request->document_id)){
                 $status = DB::table('driver_documents')->where('id' , '=' , decrypt($request->document_id))->update($document_data);
              }else{
                $status = DB::table('driver_documents')->insertGetId($document_data);
              }
                     
          
                if($status){
 
                  if ($request->hasFile('id_proof') && file_exists('Admin/driver_documents/'.$idProofImg)  && file_exists('Admin/driver_documents/'.$request->old_id_proof)) {
                     if(!empty($request->old_id_proof)){
                      unlink('Admin/driver_documents/'.$request->old_id_proof);
                     }
                      
                     }

                  if ($request->hasFile('driving_licence') && file_exists('Admin/driver_documents/'.$licenceImg) && file_exists('Admin/driver_documents/'.$request->old_driving_licence)) {
                     if(!empty($request->old_driving_licence)){
                      unlink('Admin/driver_documents/'.$request->old_driving_licence);
                  }
                  }

                  if ($request->hasFile('vehicle_registration') && file_exists('Admin/driver_documents/'.$registrationImg) && file_exists('Admin/driver_documents/'.$request->old_vehicle_registration)) {
                     if(!empty($request->old_vehicle_registration)){
                      unlink('Admin/driver_documents/'.$request->old_vehicle_registration);
                  }
                  }

                  if ($request->hasFile('vehicle_insurance') && file_exists('Admin/driver_documents/'.$insuranceImg) && file_exists('Admin/driver_documents/'.$request->old_vehicle_insurance)) {
                     if(!empty($request->old_vehicle_insurance)){
                      unlink('Admin/driver_documents/'.$request->old_vehicle_insurance);
                  }
                  }
                            
                  return redirect('drivers')->with('msg','Document Successfully Updated ')->with('color' , 'success');
                }else{
            
                   if ($request->hasFile('id_proof') && file_exists('Admin/driver_documents/'.$idProofImg)) {
                       if(!empty($idProofImg)){
                       unlink('Admin/driver_documents/'.$idProofImg);
                   }
                   }

                   if ($request->hasFile('driving_licence') && file_exists('Admin/driver_documents/'.$licenceImg)) {
                        if(!empty($licenceImg)){
                       unlink('Admin/driver_documents/'.$licenceImg);
                   }
                   }

                   if ($request->hasFile('vehicle_registration') && file_exists('Admin/driver_documents/'.$registrationImg)) {
                       if(!empty($registrationImg)){
                       unlink('Admin/driver_documents/'.$registrationImg);
                   }
                   }

                   if ($request->hasFile('vehicle_insurance') && file_exists('Admin/driver_documents/'.$insuranceImg)) {
                       if(!empty($insuranceImg)){
                       unlink('Admin/driver_documents/'.$insuranceImg);
                   }
                   }
                     

                   return redirect('drivers')->with('msg','Failed to update documents')->with('color' , 'danger');
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

           DB::table('drivers')->where('id' , '=' , $id)->update([ 'is_registered' => '0' ,'deleted_at' => \Carbon\Carbon::now() ]);
     
           DB::table('vehicles')->where('driver_id' , '=' , $id)->update([ 'deleted_at' => \Carbon\Carbon::now() ]);
           
          $status = true;

          if($status){
           return redirect('drivers')->with('msg','Driver Successfully deleted')->with('color' , 'success');
          }else{
           return Redirect::back()->with('msg','Failed to delete driver')->with('color' , 'danger');
          }
        
      } catch (\Exception $e) {
           return Redirect::back()->with('msg','Something went wrong!')->with('color' , 'warning');
       }
    }

    public function setVerificationStatus(Request $request){
        
         $this->authorize('setStatus', Driver::class);

       try{   
               switch ($request->document) {
                   case 'id_proof':
                        $status_column   = 'id_verification';
                   break;
                   case 'licence':
                        $status_column   = 'lince_verification';
                   break;
                   case 'registration':
                        $status_column   = 'reg_verification';
                   break;
                   case 'insurance':
                        $status_column   = 'ins_verification';
                   break;
                   default:
                        throw new \Exception("some thing went wrong");
                       break;
               }     

                $preData =  DB::table('driver_documents')->select($status_column)->where('driver_id' , '=' , decrypt($request->id))->first();

                 switch ($preData->$status_column) {
                   case 'unverified':
                        $status  = 'verified';
                   break;
                   case 'verified':
                        $status  = 'unverified';
                   break;
                   default:
                        throw new \Exception("some thing went wrong");
                       break;
               }    

                    $arr = array(
                            $status_column => $status
                        );

                     $verificationStatus =  DB::table('driver_documents')->where('driver_id' , '=' , decrypt($request->id))->update($arr);

                      if($verificationStatus){
                         echo json_encode(['status' => true , 'title' => 'Success' ,'message' => 'Successfully changed status']);
                        }else{
                         echo json_encode(['status' => false ,  'title' => 'Failed' , 'message' => 'Failed to change status']);
                        }

         }catch(\Exception $e){
                        echo json_encode(['status' => false , 'message' => $e->getMessage()]);
         }
}

    public function declineDriver(Request $request){

      $this->authorize('declined', Driver::class);

         try{
              $docData = '';
              $documents = $request->decline_documents;

              if (!empty($documents)) {

                    foreach ($documents as $document) {
                          if(!in_array($document, [ 1, 2 , 3 ,4])){
                                throw new \Exception("Something went wrong!");
                          }
                    }

                    $docData = implode(",", $documents);
               } 

                    $arr = array(
                            'is_approved' => '2',
                            'decline_reason' => $request->reason,
                            'decline_documents' => $docData
                        );

            $declinedStatus =  DB::table('drivers')->where('id' , '=' , decrypt($request->id))->update($arr);

                      if($declinedStatus){

                          $arr = array(
                              'msg' => 'Status Successfully changed',
                              'color'  => 'success'
                            );
                          //send email to driver to notify that his document decline
                          $driver = DB::table('drivers')
                                    ->select('name','email','mobile')
                                    ->where('id',decrypt($request->id))
                                    ->first();
                          if(!empty($driver)){
                            $mobile = $driver->mobile;
                            $email = $driver->email;
                            $name = $driver->name;
                            
                            //Send SMS
                            if($mobile != ''){
                              $mobilearray = array($mobile);
                              $msg = "Your account is declined.";
                              $res = commonSms($mobilearray,$msg);  
                            }
                            
                            if($email != ''){
                              $data = array(
                                'name'          => $name,
                                'reason'        =>$request->reason,
                                'toMail'        => $email
                              );
                                       
                              //Send Email     
                              Mail::send( 'admin.emails.decline_status' ,  $data ,function ($message) use ($data){
                                $message->to($data['toMail'])->subject('Account Declined');
                                $message->from('support@snaprides.co.za','Snap Rides');
                              }); 
                            }
                          }          
                          return Redirect::back()->with($arr);
                        }
                        else{
                            $arr = array(
                              'msg' => 'Failed to change status',
                              'color'  => 'warning'
                            );
                           return Redirect::back()->with($arr);
                        }

         }catch(\Exception $e){
          print_r($e->getMessage());die;
                    return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'danger');
         }
    }
    
    public function setApprovedStatus(Request $request){

       $this->authorize('approved', Driver::class);
      
         try {

            $preData = DB::table('drivers')->select('is_approved')->where('id' , '=' , decrypt($request->id))->first();

            if($preData->is_approved == $request->status){
                throw new \Exception(" This status already set ");
            }   
                if(!in_array($request->status,[ '0' , '1' ])){
                          throw new \Exception(" Some thing went wrong ");
                }

                      $arr = array(
                           'is_approved' => $request->status,
                           'decline_reason' => ''
                        );

              $docData = DB::table('driver_documents as dd')
                         ->select('dd.id_verification' , 'dd.lince_verification' , 'dd.reg_verification' , 'dd.ins_verification' , 'd.email' )->where( 'dd.driver_id' , '=' , decrypt($request->id) )
                         ->leftJoin('drivers as d' , 'd.id' , '=' , 'dd.driver_id' )
                         ->first();

                if(!empty($docData)){

                    if($docData->id_verification == 'unverified' || $docData->lince_verification == 'unverified' || $docData->reg_verification == 'unverified' || $docData->ins_verification == 'unverified'){
                         echo json_encode(['status' => false , 'message' => 'Failed To Approve  due to few documents not verified']);
                         die;
                    }else{ 

                        $preData = DB::table('drivers')->where('id' , '=' , decrypt($request->id))->update($arr);

                        if ($preData) {
                          $data = array(
                             'temp'    => 'approve_status', 
                              'title'  => 'Successfully approved',
                              'body'   => 'To more information to check your account to your mobile Snap Rides App'
                            );
                                  
                          
                          echo json_encode(['status' => true , 'message' => 'Status Successfully changed']);
                        }else{
                            echo json_encode(['status' => false , 'message' => 'Failed to change status' ]);
                        }
                    }
                }else{
                       echo json_encode(['status' => false , 'message' => 'Can not be approved due to documents are not available by you' ]);
                }
         } catch (\Exception $e) {
                     echo json_encode(['status' => false , 'message' => $e->getMessage() ]);
         }
    }

      public function setStatus(Request $request){
         
        $this->authorize('setStatus', Driver::class);

        try{
          $preStatus = DB::table('drivers')->select('is_active','is_approved')->where('id' , '=' , decrypt($request->id))->first();
 
          switch ($preStatus->is_active) {
             case '0':
                 $status = '1';
                 break;
             case '1':
                 $status = '0';
                 break;
             default:
                 throw new \Exception('Something went wrong');
                 break;
          }

          if($preStatus->is_approved == 1 ){
            $arr =  array('is_active' => $status);

            DB::table('drivers')->where('id' , '=' , decrypt($request->id))->update($arr);

            if($status == '1'){
              $driver = Driver::where('id',decrypt($request->id))->first();
              if(!empty($driver)){
                $data = array(
                        'name'          => $driver->name,
                        'toMail'        => $driver->email
                      );
                //Send SMS       
                $mobilearray = array($driver->mobile);
                $msg = "Your account is approved and active now.You can login in snap rides app";
                $res = commonSms($mobilearray,$msg);
                //Send Email     
                Mail::send( 'admin.emails.approve_status' ,  $data ,function ($message) use ($data){
                  $message->to($data['toMail'])->subject('Successfully approved');
                  $message->from('support@snaprides.co.za','Snap Rides');
                });  
              }  
              

              $arr = array('status' => true);
            }else{
              $arr = array('status' => false);
            }
          } else {
            $arr = array('status' => 'Failed');
          }

          echo json_encode($arr);

      }catch(\Exception $e){
        return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
      }
    }

  public function realtimeTracking(Request $request)
  {            
    $this->authorize('index', Driver::class);
    try {

      $vehiclePath = 'Admin/vehicleImage';
      $img_path = url('/') . '/' . $vehiclePath . '/';
      $no_img = url('/') . '/' . $vehiclePath . '/' . 'noimage.png';
      $q = "SELECT
            drivers.name,
            drivers.mobile,
            vehicle_category.name as vehicle_type,
            vehicle_category.image as type_img,
            IF(vehicles.vehicle_image IS NOT NULL,CONCAT('$img_path',vehicles.vehicle_image),'$no_img') as vehicle_img,
            driver_latlong.latitude,
            driver_latlong.longitude,
            111.045 * DEGREES(
            ACOS(
            COS(RADIANS(-33.927987)) * COS(
            RADIANS(driver_latlong.latitude)
            ) * COS(
            RADIANS(18.421647) - RADIANS(driver_latlong.longitude)
            ) + SIN(RADIANS(-33.927987)) * SIN(
            RADIANS(driver_latlong.latitude)
            )
            )
            ) AS distance_in_km
            FROM driver_latlong
            INNER JOIN drivers ON driver_latlong.driver_id = drivers.id
            LEFT JOIN vehicles on  vehicles.driver_id=drivers.id 
            LEFT JOIN vehicle_category ON vehicles.vehicle_category = vehicle_category.id
            WHERE drivers.is_active = '1' AND drivers.deleted_at IS NULL AND
            drivers.is_online = '1' AND
            driver_latlong.booking_id = 0 AND 
            driver_latlong.id IN (
            SELECT MAX(id) FROM driver_latlong GROUP BY driver_latlong.id) AND
            drivers.id NOT IN(
            SELECT driver_id FROM booking WHERE booking_status = 'in_progress')";
    
    $group = " GROUP BY drivers.id";
    $where = $veh_cat = $mobile = $name = $dis = $vcategory = '';

    if(isset($request->veh_cat)){
      $vcategory = $request->veh_cat;
      $veh_cat = ' AND vehicles.vehicle_category = '.$request->veh_cat;
    }
    
    $param  = $request->p;    // search param  
    $paramVal  = $request->q;    // param value
    if(!empty($param)){
      if($param == 'mobile'){
        $mobile = ' AND drivers.mobile = '.$paramVal.'';
      }else if ($param == 'driver'){
        $name = ' AND drivers.name LIKE "%'.$paramVal.'%"';
      }else if($param == 'distance'){
        $dis = ' HAVING distance_in_km <= '.$paramVal;
      }  
    }
    
    $where = $veh_cat.$mobile.$name;

    $sql = $q.$where.$group.$dis;

    $drivers = DB::select( DB::raw($sql)); 

    return view('admin.drivers.trackdrivers',compact('drivers','param','paramVal','vcategory'));  
    
    }catch(\Exception $e){
      return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
    }


  }  


  public function downloadDoc(Request $request,$filename)
  {
      //Check if file exists in app/storage/file folder
      $folder_name = 'Admin/driver_documents';
      $destinationPath = Config::get('constants.PROJECT_ROOT').$folder_name;
      $file_path = $destinationPath. '/' . $filename;
      
      if ( file_exists( $file_path ) ) {
        // Send Download
        return \Response::download( $file_path, $filename) ;
      }   else {
        // Error
        return Redirect::back()->with('msg','File not found')->with('color' , 'warning');
      }
  }

}

