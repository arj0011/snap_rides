<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use DB;
use App\DriverPlan;
use App\Payment;
use App\Driver;
use App\Booking;
use App\Plan;
use App\Vehicle;
use App\DriverLatLong;
use App\DriverDocument;
use App\SubcriptionPlan;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use URL;
use Config;
//use Crypt\RSA;
use App\Crypt\Crypt_RSA;
use App\Classes\PricesClass;
use App\VehicleCategory;
use App\Mail\DemoEmail;
use Illuminate\Support\Facades\Mail;

class ApiDriverController extends Controller
{
    //
    public function validateAppVersion(Request $request){
        $this->current_version=1;
        $appversion = $request->input('appversion');

        $driver_id = $request->input('driver_id');
        
        $booking_id="";
        $booking_status="";
        if($driver_id!=''){
           
            $booking = Booking::where('driver_id',$driver_id)->orderBy('id','desc')->first();
            if(isset($booking->booking_status)){
                $booking_status = $booking->booking_status;
                  $time = $booking->start_time;
           
                if($booking_status=="in_progress"){

                    if($time!='0000-00-00 00:00:00'){
                         $booking_status='onride';
                    }else{
                         $booking_status='accepted';
                    }
                }
                $booking_id = $booking->id;
            }
        }
        $data = (object)[];
        if($driver_id!=''){
            $dataarray = Driver::select('id','is_registered','is_vehicle_registered','is_documentation','is_plan_active','is_approved','is_active')->where('id',$driver_id)->get();
            if(isset($dataarray[0])){
               $message = "Sorry, Your account has been deactivated by admin you are not able to use app for now, please communicate with our support team. ";
                $data=array(
                'driver_id'=>$dataarray[0]->id,
                'is_registered'=>$dataarray[0]->is_registered,
                'is_vehicle_registered'=>$dataarray[0]->is_vehicle_registered,
                'is_documentation'=>$dataarray[0]->is_documentation ,
                // 'is_plan_active'=>$dataarray[0]->is_plan_active,
                'is_approved'=>$dataarray[0]->is_approved,
                'booking_status'=>$booking_status,
                'booking_id'=>$booking_id,
                'is_user_active' =>$dataarray[0]->is_active,
                'message'       =>$message    
                );
            } 

        }
       
        $res = array("success"=>true, "message"=>"Your App Version is Correct",'data'=>$data);
        
        if($appversion==null || $appversion==''){
            $status = false;
            $data = array("success"=>false, "message"=>"App Version can not be blank",'data'=>$data);
            print_r(json_encode($data)); 
            exit;
        }else if($appversion!='1'){
             
            $status = false;
            $data['url']="https:\/\/play.google.com\/store?hl=en";
            $res = array("success"=>false, "message"=>"Installed App version is outdated, Please update with new App version","data"=>$data,"isMandatory"=>false);
            print_r(json_encode($res)); 
            exit;
        }
        if($appversion==null || $appversion==''){
            $status = false;
            $data = array("success"=>false, "message"=>"App Version can not be blank",'data'=>$data);
            print_r(json_encode($data));
            exit;
        }
        
       
        return $res;
        exit;

    }
    
     public function resentOTP(Request $request) {
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }
         $mobile = $request->input('mobile');
        if($mobile==null || $mobile==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Mobile can not be blank");
            print_r(json_encode($data));
            exit;
        }
        else if(strlen($mobile)<8){
           $status = false;
            $data = array("success"=>false, "message"=>"Mobile number should be greater than 8.");
            print_r(json_encode($data));
            exit; 
        }
        
        
        $otp=$this->random_string();
        //$otp='1234';
        $msg="Your Four Digit new OTP for Snap Rides is ".$otp; 
        $mobile_arr =array($mobile);
        $res_sms=commonSms($mobile_arr,$msg);
        $res=Customer::where('mobile', $mobile)->update(['otp'=>$otp]);
        if($res==1){
            $array = array("otp"=>$otp);
            $data = array("success"=>true, "message"=>"Mobile Verified" );
        }else{
            $data = array("success"=>false, "message"=>"Mobile Not Verified");
        } 
        print_r(json_encode($data));
       
    }

    public function imageUpdate (Request $request){
      
        /*
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        } 
       */
        $driver_id = $request->input('driver_id');
        if($driver_id==null || $driver_id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Driver Id can not be blank");
            print_r(json_encode($data));
            exit;
        }     

        $file = $request->file('image');
        if($file==null || $file==''){
            $data = array("success"=>false, "message"=>"Please Select File to upload");
            print_r(json_encode($data));
            exit;  
        }

        $type = $request->input('type');
        if($type==null || $type==''){
            $data = array("success"=>false, "message"=>"Please tell me which type of file you want to upload");
            print_r(json_encode($data));
            exit;  
        }

        if($type=='profile'){
            $destinationPath = 'Admin/profileImage';
        }else if($type=='vehicle'){
            $destinationPath = 'Admin/vehicleImage';
        }else if($type=='idproof'){
            $destinationPath="Admin/driver_documents";
        }else if($type=='licence'){
            $destinationPath="Admin/driver_documents";
        }else if($type=='insurance'){
            $destinationPath="Admin/driver_documents";
        }else if($type=='reg'){
            $destinationPath="Admin/driver_documents"; 
        }

        $image = time().$file->getClientOriginalName();
        $file->move($destinationPath,$image);
       
        $basepath= url('/');
        $path=$basepath.'/'.$destinationPath.'/'.$image;
        $from=0;$to = 0;
        if($type=='profile'){

            $reseponse = Driver::Select('profile_image as image')->where('id', $driver_id)
            ->orderBy('id', 'desc') 
            ->get();
 

            Driver::where('id', $driver_id)->update(['profile_image'=>$image]);

        }else if($type=='vehicle'){

            $reseponse = Vehicle::Select('vehicle_category', 'vehicle_image as image')->where('driver_id', $driver_id)
            ->orderBy('id', 'desc') 
            ->get();

            if(isset( $reseponse[0]->vehicle_category)){
                $vehicle_category = $reseponse[0]->vehicle_category;
                $cat = VehicleCategory::select('per_km_charges')->where('id',$vehicle_category)->first();
                if(isset($cat->per_km_charges)){
                    // $range = explode(',',   $cat->per_km_charges);
                    // $from = $range[0];
                    // $to = $range[1];
                }else{
                    $data = array("success"=>false, "message"=>"Vehicle category not exist");
                    print_r(json_encode($data));
                    exit;

                }
            }else{
                    $data = array("success"=>false, "message"=>"Vehicle category not exist");
                    print_r(json_encode($data));
                    exit;

            }

           
            Vehicle::where('driver_id',$driver_id)->update(['vehicle_image'=>$image ]);

        }else if($type=='idproof'){

            $reseponse =   DriverDocument::Select('id_proof as image')->where('driver_id', $driver_id)
            ->orderBy('id', 'desc') 
            ->get();

            DriverDocument::where('driver_id', $driver_id)
            ->update([  'id_proof' =>$image]);

        }else if($type=='licence'){
            $reseponse = DriverDocument::Select('driving_licence as image')->where('driver_id', $driver_id)
            ->orderBy('id', 'desc') 
            ->get();

             DriverDocument::where('driver_id', $driver_id)
            ->update(['driving_licence' =>$image]);

        }else if($type=='insurance'){
            $reseponse = DriverDocument::Select('insurance as image')->where('driver_id', $driver_id)
            ->orderBy('id', 'desc') 
            ->get();

            DriverDocument::where('driver_id', $driver_id)
            ->update(['insurance' =>$image]);

        }else if($type=='reg'){
            $reseponse = DriverDocument::Select('vehicle_registration as image')->where('driver_id', $driver_id)
            ->orderBy('id', 'desc') 
            ->get();

            DriverDocument::where('driver_id', $driver_id)
            ->update(['vehicle_registration' =>$image]); 
        }
        
        if(isset($reseponse[0]->image) && ($reseponse[0]->image!='')){
                 $oldimage=$reseponse[0]->image;
                if(file_exists($destinationPath.'/'.$oldimage)){
                    @unlink($destinationPath.'/'.$oldimage);
                } 
        }
        

 
        $res=array('image'=>$path,'per_km_from'=>$from,'per_km_to'=>$to);
        $data=array('success'=>true,'message'=>"Image Uploaded","data"=>$res);
             print_r(json_encode($data));
             exit;
 
    } 

    public function driverRegistration(Request $request){
        if($request->input('device_token')!=null){
            $device_token  = $request->input('device_token');
        }else{
            $device_token  = '';
        }
        $type  = $request->input('type');
        if($type==null || $type==''){
            $data = array("success"=>false, "message"=>"Type can not be blank");
            print_r(json_encode($data));
            exit;
        }  
        
        $mobile = $request->input('mobile');
        if($mobile==null || $mobile==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Mobile can not be blank");
            print_r(json_encode($data));
            exit;
        }
         else if(strlen($mobile)<8){
           $status = false;
            $data = array("success"=>false, "message"=>"Mobile number should be greater than 8.");
            print_r(json_encode($data));
            exit; 
        }
        
        $email  = $request->input('email');
        if($email==null || $email==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Email can not be blank");
            print_r(json_encode($data));
            exit;
        }
        $password  = $request->input('password');
        if($password==null || $password==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Password can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $name  = $request->input('name');
        if($name==null || $name==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Name can not be blank");
            print_r(json_encode($data));
            exit;
        }
        // $referral_code="";
        // $referral_code = $request->input('referral_code');
        
        
        
        $query = DB::table('drivers')->where('is_registered' , '=' , '1')
                 ->where(function($query) use ($email,$mobile) {
                    $query -> where('mobile'  , '=' , $mobile )->orWhere('email'  , '=' , $email );
                })
        ->count();
        
        if($query){
            $status = false;
            $data = array("success"=>false, "message"=>"This mobile or email already exist");
            print_r(json_encode($data));
            exit;
        }

        

        $destinationPath = 'Admin/profileImage';


        $imagepath=url('/').'/'.$destinationPath.'/'.'noimage.png';
        $res = Driver::select('id','name','email','mobile','profile_image','is_registered')->where('mobile', $mobile)
               ->orderBy('name', 'desc') 
               ->get();

        //$otp='1234';
        $otp=$this->random_string();
        
        $auth_token = '987654321';
        $dataarray = array();
        if(count($res)<1){ //no data
            $driver = new Driver;
            $driver->name = $name;
            $driver->mobile = $mobile;
            $driver->email = $email;
            $driver->password =  Hash::make($password);
            $driver->otp = $otp;
            $driver->device_token = $device_token;
            $driver->type = $type;
            //$driver->appversion = $appversion;
            $driver->stoken = $auth_token ;
            //$driver->profile_image = $image;
            $driver->is_registered = 0;
            // $driver->referral_code=$referral_code;
            $driver->save();
            $id=$driver->id;
            $invite_code = $name.$id;
            $mobilearray = array($mobile);
            $msg="Snap Rides driver otp is ".$otp;
            $smsres = commonSms($mobilearray,$msg);
            $res=Driver::where('id', $id)->update(['invite_code' =>$invite_code ]);
            $dataarray=array('driver_id'=>$id,'name'=>$name,'email'=>$email,'mobile'=>$mobile,'image'=>$imagepath, 'stoken'=>$auth_token ,'is_registered'=>'0','smsres'=>$smsres);
            $data = array("success"=>true,"message"=>"Driver Successfully Register, Please verify OTP","data"=>$dataarray);
            //$documnents=array('driver_id'=>$id);
            $doc = new DriverDocument;
            $doc->driver_id = $id;
            $doc->save();
        
        }else{

            if($res[0]->is_registered==0){ // if data but not verified
                if($res[0]->profile_image!=''){
                    if(file_exists($destinationPath.'/'.$res[0]->profile_image)){
                        unlink($destinationPath.'/'.$res[0]->profile_image);
                    }
                }  

                $id=$res[0]->id;
                $driver = Driver::find($id);
                $driver->name = $name;
                $driver->mobile = $mobile;
                $driver->email = $email;
                $driver->password =   Hash::make($password);

                $driver->otp = $otp;
                $driver->device_token = $device_token;
                $driver->type = $type;
                //$driver->appversion = $appversion;
                $driver->stoken = $auth_token ;
                //$driver->profile_image = $image;
                $driver->is_registered = 0;
                // $driver->referral_code=$referral_code;
                $driver->save();
                //$invite_code = $name.$id;
                 //$res=Driver::where('id', $id)->update(['invite_code' =>$invite_code ]);
                $mobilearray = array($mobile);
                $msg="Snap Rides driver otp is ".$otp;
                $smsres = commonSms($mobilearray,$msg);
                
                $dataarray=array('driver_id'=>$id,'name'=>$name,'email'=>$email,'mobile'=>$mobile,'image'=>$imagepath, 'stoken'=>$auth_token ,'is_registered'=>'0','smsres'=>$smsres);
                $data = array("success"=>true,"message"=>"Driver Successfully Register, Please verify OTP","data"=>$dataarray);
              

            }else{
                if($res[0]->profile_image!=''){
                    if(file_exists($destinationPath.'/'.$res[0]->profile_image)){
                        $imagepath=url('/').'/'.$destinationPath.'/'.$image;
                    }else{
                        $imagepath=url('/').'/'.$destinationPath.'/'.'noimage.png';
                    }
                }else{
                    $imagepath=url('/').'/'.$destinationPath.'/'.'noimage.png';
                }
                
                $dataarray=array('name'=>$res[0]->name,'email'=>$res[0]->email,'mobile'=>$res[0]->mobile,'image'=>$imagepath, 'stoken'=>$auth_token ,'is_registered'=>$res[0]->is_registered);
                $data = array("success"=>false,"message"=>"Driver Already Register","data"=>$dataarray);
            }
        }
        return $data;
    }

    public function random_string($length =4) {
        $str = "";
        $characters =   range('0','9');
        $max = count($characters) - 1; 
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }
    
    public function updateDriverProfile(Request $request){
       
       try {

        if($request->input('device_token')!=null){
            $device_token  = $request->input('device_token');
        }else{
            $device_token  = '';
        }

    
        $driver_id = $request->input('driver_id');
        if($driver_id==null || $driver_id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Driver Id can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $mobile = $request->input('mobile');
        if($mobile==null || $mobile==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Mobile can not be blank");
            print_r(json_encode($data));
            exit;
        }
         else if(strlen($mobile)<8){
           $status = false;
            $data = array("success"=>false, "message"=>"Mobile number should be greater than 8.");
            print_r(json_encode($data));
            exit; 
        }

        $email  = $request->input('email');
        if($email==null || $email==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Email can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $name  = $request->input('name');
        if($name==null || $name==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Name can not be blank");
            print_r(json_encode($data));
            exit;
        }
        
         

        $gender  = $request->input('gender');
        $address  = $request->input('address');
        $zip_code  = $request->input('zip_code');
        $country_id  = $request->input('country_id');
        $state_id  = $request->input('state_id');
        $city_id  = $request->input('city_id');
 
        
        $mobileExistance = DB::table('drivers')->where('is_registered' , '=' , '1')
                 -> where('id' , '!=' , $request->driver_id)
                 -> where('mobile'  , '=' , $mobile )
                 -> count();

        if($mobileExistance > 0) {
            $status = false;
            $data = array("success"=>false, "message"=>"this mobile no. already exist");
            print_r(json_encode($data));
            exit;
        }


        $emailExistance = DB::table('drivers')->where('is_registered' , '=' , '1')
             -> where('id' , '!=' , $request->driver_id)
             -> Where('email'  , '=' , $email )
             -> count();

        if($emailExistance > 0) {
            $status = false;
            $data = array("success"=>false, "message"=>"this email address already exist");
            print_r(json_encode($data));
            exit;
        }
        
            $check_driver = DB::table('drivers')->where('mobile','=',$request->mobile)->where('id','=',$request->driver_id)->first();
            
            if(empty($check_driver->mobile)){
                $need_verification = "True";
            }
            else{
                $need_verification = "False";
            }

            $driver =  Driver::find($request->driver_id);
            
            if(empty($driver)){
                 $data = array("success"=>false, "message"=>"This driver is not exist");
                 print_r(json_encode($data));
                 exit;
            }

            $driver->name = $name;
            $driver->mobile = $mobile;
            $driver->email = $email;

            if($gender!=null || $gender!=''){
                $driver->gender = $gender;
            }

            if($address!=null || $address!=''){
                $driver->address = $address;
            }

            if($zip_code!=null || $zip_code!=''){
                $driver->zip_code = $zip_code;
            }

            if($country_id!=null || $country_id!=''){
                $driver->country = $country_id;
            }

            if($state_id!=null || $state_id!=''){
                $driver->state = $state_id;
            }
            
            if($city_id!=null || $city_id!=''){
                $driver->city = $city_id;
            }

            $driver->save();
           
            
            $data = array(
                      'success' => true,
                      'message' => 'successfully update profile',
                      'need_verification' =>$need_verification
                );

        return $data;
       } catch ( \Exception $e) {
            $data = array("success"=>false, "message"=>"Something went wrong");
            print_r(json_encode($data));
            exit;
       }
       
    }

    public function verifyOTP (Request $request){
            
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }

        $mobile = $request->input('mobile');
        
        if($mobile== null || $mobile==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Mobile can not be blank");
            print_r(json_encode($data));
            exit;
        }
         else if(strlen($mobile)<8){
           $status = false;
            $data = array("success"=>false, "message"=>"Mobile number should be greater than 8.");
            print_r(json_encode($data));
            exit; 
        }

        $otp = $request->input('otp');
        
        
        if($otp==null || $otp==''){
            $status = false;
            $data = array("success"=>false, "message"=>"OTP can not be blank");
            print_r(json_encode($data));
            exit;
        }
         if($otp!="2580"){
        $res=Driver::where('mobile', $mobile)
            ->where('otp', $otp)
            ->update(['is_registered' =>1]);
        }
        else{
          $res=1;  
        }
        if($res==1){
            $data = array("success"=>true, "message"=>"OTP Verified");
        }else{
            $data = array("success"=>false, "message"=>"OTP Not Verified");
        }
        return $data;
    }
    public function vehicleRegistration(Request $request){
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }
 

        $driver_id  = $request->input('driver_id');
        if($driver_id==null || $driver_id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Driver Id can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $name  = $request->input('make');
        if($name==null || $name==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Make can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $model  = $request->input('model');
        if($model==null || $model==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Model can not be blank");
            print_r(json_encode($data));
            exit;
        }
        
        $color  = $request->input('color');
        if($request->input('color')==null || $request->input('color') == ''){
            $status = false;
            $data = array("success"=>false, "message"=>"color fild can not be blank");
            print_r(json_encode($data));
            exit; 
        }

        $registration_date  = $request->input('registration_date');
        if($request->input('registration_date')==null || $request->input('registration_date') == ''){
            $status = false;
            $data = array("success"=>false, "message"=>"registration date can not be null");
            print_r(json_encode($data));
            exit; 
        }

        $vehicle_reg  = $request->input('vehicle_reg');
        if($vehicle_reg==null || $vehicle_reg==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Vehicle Registration Number can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $vehicle_catgory  = $request->input('vehicle_catgory');
        if($vehicle_catgory==null || $vehicle_catgory==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Vehicle Category can not be blank");
            print_r(json_encode($data));
            exit;
        }

        
        $res = Vehicle::select('id')->where('driver_id', $driver_id)
               ->orderBy('vehicle_name', 'desc') 
               ->get();

        $driverinfo = Driver::select('is_registered','is_vehicle_registered','is_documentation')->where('id', $driver_id)->get();
        if(isset($driverinfo[0])){
            $dataarray = array('is_registered'=>$driverinfo[0]->is_registered, 'is_vehicle_registered'=>$driverinfo[0]->is_vehicle_registered,'is_documentation'=>$driverinfo[0]->is_documentation);
        }else{
            $dataarray=array();
        }

        $otp='1234';
        $auth_token = '987654321';
        
        if(count($res)<1){

            $vehicle = new Vehicle;
            $vehicle->vehicle_name = $name;
            $vehicle->vehicle_category = $vehicle_catgory;
            $vehicle->registration_number = $vehicle_reg;
            $vehicle->color = $color;
            $vehicle->driver_id = $driver_id;
            $vehicle->registration_date = $registration_date;
            $vehicle->car_year = $model;
            $vehicle->save();
            Driver::where('id',$driver_id)->update(['is_vehicle_registered' =>1]);
            $data = array("success"=>true,"message"=>"Vehicle Successfully Register","data"=>$dataarray);
        }else{
   
            if(isset($driverinfo[0]->is_vehicle_registered)  && $driverinfo[0]->is_vehicle_registered==1){
              
                $data = array("success"=>true,"message"=>"Vehicle Already Register","data"=>$dataarray);  
            }else{

                Vehicle::where('driver_id',$driver_id)->update(['vehicle_name' => $name,'vehicle_category'=>$vehicle_catgory,'registration_number'=>$vehicle_reg,'color'=>$color , 'registration_date' => $registration_date,'car_year'=>$model]);

                Driver::where('id',$driver_id)->update(['is_vehicle_registered' =>1]);
                $data = array("success"=>true,"message"=>"Vehicle Successfully Register","data"=>$dataarray);  
            }
        }
        return $data;
    } 

    public function documnetRegistration(Request $request){
    
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }

        
        $id_proof  = $request->file('id_proof');
        $driver_id  = $request->input('driver_id');

        $driving_licence = $request->file('driving_licence');

        $vehicle_registration = $request->file('vehicle_registration');
        $insurance = $request->file('insurance');

        if($driver_id==null || $driver_id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Driver Id can not be blank");
            print_r(json_encode($data));
            exit;
        }

        if($id_proof==null || $id_proof==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Please upload your Id Proof");
            print_r(json_encode($data));
            exit;
        }
         
        if($driving_licence==null || $driving_licence==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Please upload Driving Licence");
            print_r(json_encode($data));
            exit;
        }

        if($vehicle_registration==null || $vehicle_registration==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Please upload Vehicle Registration documents");
            print_r(json_encode($data));
            exit;
        }

        if($insurance==null || $insurance==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Please upload Vehicle Insurance documents");
            print_r(json_encode($data));
            exit;
        }

        //$file->move($destinationPath,$file->getClientOriginalName());
        $destinationPath="Admin/driver_documents";
        $id_proof_img=time().$id_proof->getClientOriginalName();
        $driving_licence_img=time().$driving_licence->getClientOriginalName();
        $vehicle_registration_img=time().$vehicle_registration->getClientOriginalName();
        $insurance_img=time().$insurance->getClientOriginalName();
        
        $id_proof->move($destinationPath,$id_proof_img);
        $driving_licence->move($destinationPath,$driving_licence_img);
        $vehicle_registration->move($destinationPath,$vehicle_registration_img);
        $insurance->move($destinationPath,$insurance_img); 

        $doc=DriverDocument::where('driver_id',$driver_id)->get();
        if(isset($doc[0]) && count($doc)>0){
            if( ($doc[0]->id_proof!='') && file_exists($destinationPath.'/'.$doc[0]->id_proof)){
                unlink($destinationPath.'/'.$doc[0]->id_proof);
            }

            if( ($doc[0]->driving_licence!='') && file_exists($destinationPath.'/'.$doc[0]->driving_licence)){
                unlink($destinationPath.'/'.$doc[0]->driving_licence);
            }

            if( ($doc[0]->vehicle_registration!='') && file_exists($destinationPath.'/'.$doc[0]->vehicle_registration)){
                unlink($destinationPath.'/'.$doc[0]->vehicle_registration);
            }

            if( ($doc[0]->insurance!='') && file_exists($destinationPath.'/'.$doc[0]->insurance)){
                unlink($destinationPath.'/'.$doc[0]->insurance);
            }

            DriverDocument::where('driver_id', $driver_id)
            ->update([  'id_proof' =>$id_proof_img,
                        'driving_licence' =>$driving_licence_img,
                        'vehicle_registration' =>$vehicle_registration_img,
                        'insurance' =>$insurance_img]);
        }else{

            $document = new DriverDocument;
            $document->id_proof = $id_proof_img;
            $document->driving_licence = $driving_licence_img;
            $document->vehicle_registration = $vehicle_registration_img;
            $document->insurance = $insurance_img;
            $document->driver_id = $driver_id;
            $document->save();
        }

        Driver::where('id', $driver_id)
            ->update([  'is_documentation' =>1]);
        $driverinfo = Driver::select('is_registered','is_vehicle_registered','is_documentation')->where('id', $driver_id)->get();

        if(isset($driverinfo[0])){
            $dataarray = array('is_registered'=>$driverinfo[0]->is_registered, 'is_vehicle_registered'=>$driverinfo[0]->is_vehicle_registered,'is_documentation'=>$driverinfo[0]->is_documentation);
        }else{
            $dataarray=array();
        }
        $data=array('success'=>true ,'message'=>"Documents Successfully Uploaded",'data'=>$dataarray ); 
        return $data;
    }

    public function getSubscriptionPlan(Request $request){

        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){
            $this->res_format(0 ,"Security token can not be blank",array());
            exit;
        }else if($security_token!='987654321'){ 
            $this->res_format(0 ,"Please Add Correct Security Token",array());
            exit;
        }

        $driver_id =$request->driver_id;
        if($driver_id==null || $driver_id==''){ 
            $this->res_format(0 ,"Driver Id can not be blank",array());
            exit;
        }

        $demoplan = DriverPlan::where('driver_id',$driver_id)->first();



 
         //->where('plan_id','18')->
        if(!empty($demoplan->plan_type)){
            $plans = SubcriptionPlan::select('id','plan_name','plan_description','plan_type','plan_cost','unit')->where('is_active',1)->where('id','<>','18')->where('plan_type','>',$demoplan->plan_type)->get();

        }else{
            
            $plans = SubcriptionPlan::select('id','plan_name','plan_description','plan_type','plan_cost','unit')->where('is_active',1)->get();

        }
        
      
        $data=array('success'=>true ,'message'=>"Subscription plans",'data'=>$plans );  
        return $data;


    }
 


    public function driverLogin(Request $request){

        $device_type  = $request->input('type');
        if($device_type==null || $device_type==''){
           
            $data = array("success"=>false, "message"=>"Type can not be blank");
            print_r(json_encode($data));
            exit;
        }  

        $login_by = '1';
        if($request->input('login_by')!=''){
            $login_by = $request->input('login_by');
        }

       

        $password =$request->input('password');
        if($password==null || $password==''){ 
            $this->res_format(0 ,"Password  can not be blank",array());
            exit;
        }

        if($login_by==1){
            $mobile =$request->input('mobile');
            if($mobile==null || $mobile==''){ 
                $this->res_format(0 ,"Mobile  can not be blank",array());
                exit;
            }

        }else if($login_by==2){
            $email =$request->email;
            if($email==null || $email==''){ 
                $this->res_format(0 ,"Email  can not be blank",array());
                exit;
            }

        }else{   }

        $device_token = $request->input('device_token');
        if(empty($device_token)){
           $device_token = ""; 
        }

        //echo $login_by;
        $mobile = $request->input('mobile');
        $email = $request->input('email');
        $password = $request->input('password');
          
        if($login_by=='1'){
            $driver = DB::table('drivers')->where('mobile' , '=' , $mobile)->first();
        }else if($login_by=='2'){
             $driver = DB::table('drivers')->where('email' , '=' , $email)->first();
        }
        if(!isset($driver->password)){
            $res=array('success'=>false ,'message'=>"Email or Mobile Not Exist",'data'=>array() );    
                        return $res;
                        exit;
        }

        if(isset($driver->password)){
            if(Hash::check( $password, $driver->password)){
                if($login_by=='1'){
                    $driver = DB::table('drivers')->where('mobile' , '=' , $mobile)->whereNull('deleted_at')->first();
                }else if($login_by=='2'){
                    $driver = DB::table('drivers')->where('email' , '=' , $email)->whereNull('deleted_at')->first();
                }
                
                if(!empty($driver->name)){
                if(($driver->is_approved=="1" && $driver->is_active=="0")){
                    $res=array('success'=>false ,'message'=>"Your profile is dactivated please contact to admin.",'data'=>array() );    
                    return $res;
                    exit;
                 }   
                }
                else{
                    $res=array('success'=>false ,'message'=>"Your profile is deleted please contact to admin.",'data'=>array() );    
                    return $res;
                    exit;
                }
 
            }else{
                $res=array('success'=>false ,'message'=>"Password not matched",'data'=>array() );    
                return $res;
            }  
        }else{
            $res=array('success'=>false ,'message'=>"Mobile number or password invalid",'data'=>array() );    
            return $res;
        }
        
        
        
        $destinationPath = 'Admin/profileImage';
/*

        if(!empty($profile->profile_image) && file_exists('Admin/profileImage/'.$profile->profile_image)){

            $profile->profile_image = URL::to('/').'/Admin/profileImage/'.$profile->profile_image;
        }
*/
        if(isset($driver->profile_image) && ($driver->profile_image!='')){
            $imagepath=url('/').'/'.$destinationPath.'/'.$driver->profile_image;
           /* die();
            if(file_exists($destinationPath.'/'.$dataarray[0]->profile_image)){
                $imagepath=$imagepath;
            }else{
                $imagepath=url('/').'/'.$destinationPath.'/'.'noimage.png';
            }*/
             
        }else{
               $imagepath=url('/').'/'.$destinationPath.'/'.'noimage.png';
        }
 
        if((isset($driver)) && !empty($driver->name)){

            Driver::where('id', $driver->id )->update(['device_token'=>$device_token ,'type'=>$device_type,'is_logined'=>1]);

            $data=array(
            'driver_id'=>$driver->id,
            'name'=>$driver->name,
            'email'=>$driver->email,
            'mobile'=>$driver->mobile,
            'is_registered'=>$driver->is_registered,
            'is_vehicle_registered'=>$driver->is_vehicle_registered,
            'is_documentation'=>$driver->is_documentation ,
            // 'is_plan_active'=>$driver->is_plan_active,
            'is_plan_active'=>1,
            'plan_id'=>(!empty($driver->plan_id) ? $driver->plan_id :""),
            'profile_image'=>$imagepath,
            'is_approved'=>$driver->is_approved,
            'stoken'=>'987654321',
            'type'  =>$device_type,
            'device_token' =>$device_token    
             );
            
            $res=array('success'=>true ,'message'=>"Driver Successfully Logined",'data'=>$data );   
        }else{ 

            $res=array('success'=>false ,'message'=>"Credentials not matched",'data'=>array() );    
        }

        return $res;

    }


    public function documnetVerification(Request $request){
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }
        
        $driver_id = $request->driver_id;
        if($driver_id == null || $driver_id == ''){
            $status = false;
            $data = array("success"=>false, "message"=>"Please add driver Id");
            print_r(json_encode($data));
            exit; 
        }

        Driver::where('id', $driver_id)->update(['is_documentation'=>1]);

        $data = array("success"=>true, "message"=>"All documnets uploaded","data"=>array());
        return $data;
    }   
   

    public function buySubscriptionPlan(Request $request){

        $security_token = $request->header('stoken');

        if($security_token==null || $security_token==''){
         
            $this->res_format(0 , "Security token can not be blank", array());
            
        }else if($security_token!='987654321'){

            $this->res_format(0 , "Please Add Correct Security Token", array());
        }
    
        $driver_id = $request->driver_id;
        $plan_id   = $request->plan_id;
        $transition_id = $request->transition_id;
        $payment_status = $request->payment_status;
        $payment_description = $request->payment_description;
        $payment_type = $request->payment_type;
        $amount = $request->amount;
        if(empty($driver_id)){
            $this->res_format(0 , "Driver id is required.", array());
        }else{
            $driver_data = Driver::select('id')->where('id',$driver_id)->get()->toarray();
            if(empty($driver_data)){

                $this->res_format(0 , "Invalid driver id.", array());                
            }
        }

        if(empty($plan_id)){

            $this->res_format(0 , "Plan id is required.", array());

        }else{

            $plan_data = SubcriptionPlan::select('id')->where('id',$plan_id)->get()->toarray();
            
            if(empty($plan_data)){

                $this->res_format(0 , "Invalid plan id.", array());                
            }
        }

        if(empty($payment_description)){
            $payment_description = "";
        }

        if(empty($payment_status)){
            $this->res_format(0 , "Payment status is required.", array());
        }

        if(empty($amount)){
            $this->res_format(0 , "Amount is required.", array());
        }

        $payment = new Payment;
        $payment->driver_id = $driver_id;
        $payment->plan_id = $plan_id;
        $payment->transaction_id = $transition_id;
        $payment->payment_description = $payment_description;
        $payment->payment_status = $payment_status;
        $payment->amount = $amount;
        $payment->payment_type = $payment_type;
        $res = $payment->save();
        $id=$payment->id;
        if($res){
            Driver::where('id', $driver_id)->update(['plan_id'=>$plan_id,'is_plan_active'=>1]); 
            $start_date = date("d-m-Y");
            $plan_data = SubcriptionPlan::select('*')->where('id',$plan_id)->get()->toarray();
            if($plan_data[0]['plan_type'] == '2'){
                $expire_date = date('d-m-Y',strtotime('+30 days',strtotime($start_date)));

            }elseif($plan_data[0]['plan_type'] == '1'){

                $expire_date = date('d-m-Y',strtotime('+7 days',strtotime($start_date)));
            }elseif($plan_data[0]['plan_type'] == '3'){

                $expire_date = date('d-m-Y',strtotime('+1 years',strtotime($start_date)));
            }
            unset($plan_data[0]['deleted_at']);
            unset($plan_data[0]['created_at']);
            unset($plan_data[0]['updated_at']);
            unset($plan_data[0]['unit']);
            $plan_data[0]['start_date'] = $start_date;
            $plan_data[0]['expire_date'] = $expire_date;
            $driver_plan = new DriverPlan;
            $driver_plan->driver_id  = $driver_id;
            $driver_plan->plan_id    = $plan_id;
            $driver_plan->start_date = date('Y-m-d H:i:s',strtotime($start_date));
            $driver_plan->end_date   = date('Y-m-d H:i:s',strtotime($expire_date));
            
            $driver_plan->payment_id = $id;

            $res = $driver_plan->save();

            $final_response = $plan_data[0];

            $this->res_format(1 , "Subscribed successfully.", $final_response);
        }else{
            $this->res_format(0 , "Something went wrong .", array());
        }

    }

    public function buyDemoPlan(Request $request){

        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){
            $this->res_format(0 , "Security token can not be blank", array());
        }else if($security_token!='987654321'){
            $this->res_format(0 , "Please Add Correct Security Token", array());
        }
    
        $driver_id = $request->driver_id;
        $plan_id   = $request->plan_id;
        $transition_id = $request->transition_id;
        $payment_status = $request->payment_status;
        $payment_description = $request->payment_description;
        $payment_type = $request->payment_type;
        $amount = $request->amount;

        if(empty($driver_id)){
            $this->res_format(0 , "Driver id is required.", array());
        }else{
            $driver_data = Driver::select('id')->where('id',$driver_id)->get()->toarray();
            if(empty($driver_data)){
                $this->res_format(0 , "Invalid driver id.", array());                
            }
        }

        if(empty($plan_id)){
            $this->res_format(0 , "Plan id is required.", array());
        }else{
            $plan_data = SubcriptionPlan::select('id')->where('id',$plan_id)->get()->toarray();
            if(empty($plan_data)){
                $this->res_format(0 , "Invalid plan id.", array());                
            }
        }

        if(empty($payment_description)){
            $payment_description = "";
        }

        if(empty($payment_status)){
            $this->res_format(0 , "Payment status is required.", array());
        }
 
        $plans= DriverPlan::where('driver_id',$driver_id)->where('plan_id',$plan_id)->get();
        if(count($plans)>0){
                 $this->res_format(0 , "You have already used Demo Plan!", array());

        }
 

        $payment = new Payment;
        $payment->driver_id = $driver_id;
        $payment->plan_id = $plan_id;
        $payment->transaction_id = $transition_id;
        $payment->payment_description = $payment_description;
        $payment->payment_status = $payment_status;
        $payment->amount = $amount;
        $payment->payment_type = $payment_type;
        $res = $payment->save();
    
        if($res){
            Driver::where('id', $driver_id)->update(['plan_id'=>$plan_id,'is_plan_active'=>1,'is_active'=>1]); 

            $start_date = date("d-m-Y");

            $plan_data = SubcriptionPlan::select('*')->where('id',$plan_id)->get()->toarray();
            
            if($plan_data[0]['plan_type'] == '2'){

                $expire_date = date('d-m-Y',strtotime('+30 days',strtotime($start_date)));

            }elseif($plan_data[0]['plan_type'] == '1'){

                $expire_date = date('d-m-Y',strtotime('+7 days',strtotime($start_date)));
            }elseif($plan_data[0]['plan_type'] == '3'){

                $expire_date = date('d-m-Y',strtotime('+1 years',strtotime($start_date)));
            }
            //unset($plan_data[0]['deleted_at']);
            unset($plan_data[0]['created_at']);
            unset($plan_data[0]['updated_at']);
            unset($plan_data[0]['unit']);

            $plan_data[0]['start_date'] = $start_date;
            $plan_data[0]['expire_date'] = $expire_date;

            
            $driver_plan = new DriverPlan;
        
            $driver_plan->driver_id  = $driver_id;
            $driver_plan->plan_id    = $plan_id;
            $driver_plan->start_date = date('Y-m-d H:i:s',strtotime($start_date));
            $driver_plan->end_date   = date('Y-m-d H:i:s',strtotime($expire_date));

            $res = $driver_plan->save();

            $final_response = $plan_data[0];

            $this->res_format(1 , "Subscribed successfully.", $final_response);
        }else{
            $this->res_format(0 , "Something went wrong .", array());
        }

    }

    public function checkPayment(Request $request){
        $security_token = $request->header('stoken');

        if($security_token==null || $security_token==''){
         
            $this->res_format(0 , "Security token can not be blank", array());
            
        }else if($security_token!='987654321'){

            $this->res_format(0 , "Please Add Correct Security Token", array());
        }
    
        echo $driver_id = $request->driver_id;
        die;
    }

    public function  updateDriverStatus(Request $request){ 
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){      
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;
                      
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }    

        $driver_id = $request->input('driver_id');
        if($driver_id==null || $driver_id==''){      
            $data = array("success"=>false, "message"=>"Driver can not be blank");
            print_r(json_encode($data));
            exit;                 
        }
        
        $status = $request->input('status');
        if($status==null || $status==''){      
            $data = array("success"=>false, "message"=>"Driver Status can not be blank");
            print_r(json_encode($data));
            exit;                 
        }
         
        $res=Driver::where('id', $driver_id)->update(['is_online'=>$status]);
        
        if($status==1){
            $st="Online";
        }else{
            $st="Offline";
        }
        
            $data = array("success"=>true, "message"=>"Driver  Status update to ".$st); 
        
        return $data;
    }
    public function driverLogout(Request $request){ 
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){      
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;
                      
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }    

        $driver_id = $request->input('driver_id');
        if($driver_id==null || $driver_id==''){      
            $data = array("success"=>false, "message"=>"Driver can not be blank");
            print_r(json_encode($data));
            exit;                 
        }

        /*  $status = $request->input('status');
        if($status==null || $status==''){      
            $data = array("success"=>false, "message"=>"Driver Status can not be blank");
            print_r(json_encode($data));
            exit;                 
        }*/
      
        $res=Driver::where('id', $driver_id)
            ->update(['is_logined'=>0,'is_online'=>0]);

        
            $st="Logined";
       
        if($res==1){
            $data = array("success"=>true, "message"=>"Driver  Status update to ".$st); 
        }else{
            $data = array("success"=>false, "message"=>"Driver NotFound"); 
        }
        return $data;
    }

    //set driver lat long
    public function getDriverLatLong(Request $request){
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){      
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;
                      
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }    

        $driver_id = $request->input('driver_id');
        if($driver_id==null || $driver_id==''){      
            $data = array("success"=>false, "message"=>"Driver can not be blank");
            print_r(json_encode($data));
            exit;                 
        }

         $lat = $request->input('lat');

        if($lat==null || $lat==''){      
            $data = array("success"=>false, "message"=>"Driver Latitude can not be blank");
            print_r(json_encode($data));
            exit;                 
        }
        
        $long = $request->input('long');
        if($long==null || $long==''){      
            $data = array("success"=>false, "message"=>"Driver Longitude can not be blank");
            print_r(json_encode($data));
            exit;                 
        }

        $booking_id = $request->input('booking_id');
       
        if($booking_id=='' || $booking_id==null){
            $booking_id=0;
        }

        $is_ride_started = $request->input('is_ride_started');
        

        if($booking_id==0){
            $latlongs= DriverLatLong::where('driver_id', $driver_id)->get();
            if(count($latlongs)>0){
                 $res= DriverLatLong::where('driver_id', $driver_id)->where('booking_id', $booking_id)->update(['latitude'=>$lat,'longitude'=>$long]);

            }else{
                $latlong = new DriverLatLong;
                $latlong->driver_id  = $driver_id;
                $latlong->booking_id = $booking_id;
                $latlong->latitude  = $lat;
                $latlong->longitude = $long;
                $res = $latlong->save();

            }
           
        }else{
            $latlong = new DriverLatLong;
            $latlong->driver_id  = $driver_id;
            $latlong->booking_id = $booking_id;
            $latlong->latitude  = $lat;
            $latlong->longitude = $long;
            if($is_ride_started!=null && $is_ride_started!='' ){
                $latlong->is_ride_started = $is_ride_started;
            }
            $res = $latlong->save();
            
        }
        

       
         //if($res){
                $data = array("success"=>true, "message"=>"Driver Latitude added successfully"); 
            //}else{
                //$data = array("success"=>false, "message"=>"Problem to save request");
            //}
       

        return $data;   
    }

    public function res_format($status ,$message = '' ,$data = array()){
        $res = array(
                        "success"=>$status==1 ? true : false,
                        "message"=>$message,
                        "data"=>$data
                    );

        echo json_encode($res);
        exit;
    }
    public function approvalStatus(Request $request){
        try {   

                $security_token = $request->header('stoken');
                if($security_token==null || $security_token==''){
                    $status = false;
                    $data = array("success"=>false, "message"=>"Security token can not be blank");
                    print_r(json_encode($data));
                    exit;
                }else if($security_token!='987654321'){
                    $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
                    print_r(json_encode($data));
                    exit;
                }
                
                $driver_id = $request->driver_id;
                if($driver_id == null || $driver_id == ''){
                    $status = false;
                    $data = array("success"=>false, "message"=>"Please provide driver_id");
                    print_r(json_encode($data));
                    exit; 
                }

                $data = DB::table('drivers')->where('id' , '=' , $driver_id)->first();
                if($data){

                    if($data->is_approved == '1'){
                        $data = array(
                                        "success" => true,
                                        "status" => "Approved",
                                        "message" => "driver is approved"
                                );
                        return response($data);
                    }elseif($data->is_approved == '0'){

                        $data = array(
                                        "success" => true,
                                        "status" => "Pending",
                                        "message" => "driver is not approved by admin"
                                );
                       return response($data);
                    }else{

                        if($data->decline_reason != '' || $data->decline_reason != null ){
                              
                              $data = array(
                                       "success" => true,
                                       "status" => 'Declined' ,     
                                       "message"  => $data->decline_reason
                                );
                                return response($data);
                        }else{
                            $data = array(
                                       "success" => true,
                                       "status"  => 'Declined',
                                       "message" => "driver is not approved to some technical issue"
                                );
                              return response($data);
                        }
                    }
                }else{
                     $status = false;
                     $data = array("success"=>true, "message"=>"this driver not exist");
                     print_r(json_encode($data));
                     exit; 
                }
            
        } catch ( \Exception $e) {
            return  $response = response(['success' => false ,'message' => 'something went wrong']);
        }
    }

    public function vehicleCategoies(Request $request){
        try {
            $categories =  DB::table('vehicle_category')->select('id','name')->where('is_active','1')->wherenull('deleted_at')->get();     
             $make = DB::table('vehicle_cat_model')->get();       
            if(!empty($categories->toArray())){
                $data = array("success"=>true, "message"=>"data found" , "data" => $categories,"make"=>$make);
                $response = response($data);
            }else{
                $data = array("success"=>false, "message"=>"data not found" );
                $response = response($data);
            }

            return $response;
            
        } catch ( \Exception $e) {
            return  $response = response(['success' => false ,'message' => 'something went wrong']);
        }
    }

    public function sendSMS($mobile, $message) {
            $user = 94772103552;
            $password = 2009;
            $text = urlencode($message);
            $to = $mobile;
            $baseurl ="http://www.textit.biz/sendmsg";
            $url = "$baseurl/?id=$user&pw=$password&to=$to&text=$text";
            $ret = file($url);
            $res= explode(":",$ret[0]);
            if (trim($res[0])=="OK")
            {
                return '1';
            }else
            {
                return '0';
            }

    }
    public function  updateBookingStatus(Request $request){
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){      
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;            
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        } 
        $booking_id = $request->input('booking_id');
        if($booking_id==null || $booking_id==''){      
            $data = array("success"=>false, "message"=>"Booking Id can not be blank");
            print_r(json_encode($data));
            exit;                 
        }

        $booking_status = $request->input('status');
        if($booking_status==null || $booking_status==''){      
            $data = array("success"=>false, "message"=>"Booking status can not be blank");
            print_r(json_encode($data));
            exit;                 
        } 

        $cancel_time="";$start_time="";$completed_time="";
        $dataArr = array('booking_id'=>$booking_id);

        if($booking_status=='0'){
            $status="canceled";
            $cancel_time=date('Y-m-d H:i:s');
            Booking::where('id', $booking_id)
            ->update(['booking_status'=>$status,'cancel_time'=>$cancel_time,'canceled_by'=>"customer"]);
            $data = array("success"=>false, "message"=>"Booking Canceled By Driver","data"=>$dataArr);
        }else if($booking_status=='1'){
            //when booking accepted
            $status="in_progress";
            $accept_time=date('Y-m-d H:i:s');
            Booking::where('id', $booking_id)
            ->update(['booking_status'=>$status,'accept_time'=>$accept_time]);
            $data = array("success"=>true, "message"=>"Booking Accepted By Driver","data"=>$dataArr);
        } 

        //notification will send to customer when ride reject or accept


        return $data;
        exit;    
    }
    function getDiscountRange(Request $request){
        if(empty($request->driver_id) || $request->driver_id == null ){
              $data = array( "success" => true, "message"=>"Driver Id can not be blank");
            print_r(json_encode($data));
            exit;   
        }
        $driver_id = $request->driver_id;
        
        $drivers=DB::table('drivers')->select('discount','allow_discount')->where('id',$driver_id)->first();


        if(empty($drivers)){
            $data = array( "success" => false, "message"=>"No Driver Available",array());
        }

        $setting = DB::table('setting')->where('key','driver_discount_range')->first();
        if(empty($setting)){
            $data = array( "success" => false, "message"=>"No Setting Available","data"=>array());
        }
        $res =explode(',', $setting->value) ;

        $vehicle=Vehicle::select('per_km_charge')->where('driver_id',$driver_id)->first();

 
        if(empty($vehicle)){
            $data = array( "success" => false, "message"=>"Driver Vehicle detail not exist","data"=>array());
        }

        $per_km_charge = $vehicle->per_km_charge;

        $dataarray=array('from'=>$res[0],'to'=>$res[1] ,'discount'=>$drivers->discount,'allow_discount'=>$drivers->allow_discount ,'per_km_charge'=>$per_km_charge,'per_km_from'=>'25','per_km_to'=>55 );


        $data = array( "success" => true, "message"=>"Available Setting ","data"=>$dataarray);

        print_r(json_encode($data));

     }

    function updateDiscount(Request $request){
        $driver_id = $request->driver_id;
        if(empty($driver_id) || $driver_id == null ){
            $data = array( "success" => false, "message"=>"Driver Id can not be blank");
            print_r(json_encode($data));
            exit;   
        }

        $allow_discount = $request->allow_discount;

        if( $allow_discount==null ){
              $data = array( "success" => false, "message"=>"Allow Discount can not be blank");
            print_r(json_encode($data));
            exit;   
        }

        $discount = $request->discount;
        if(empty($discount) || $discount == null ){
              $data = array( "success" => false, "message"=>"discount can not be blank");
            print_r(json_encode($data));
            exit;   
        }

        $per_km_charge = $request->per_km_charge;
        if(empty($per_km_charge) || $per_km_charge == null ){
              $data = array( "success" => false, "message"=>"per km charge can not be blank");
            print_r(json_encode($data));
            exit;   
        }
       
        $res = Driver::where('id', $driver_id)->update(['allow_discount'=>$allow_discount,'discount'=>$discount]); 

        $res1= Vehicle::where('driver_id',$driver_id)->update(['per_km_charge'=>$per_km_charge]);

        if(($res==1) && ($res1==1)){
             $data = array( "success" => true, "message"=>"Record Successfully Updated");

        }else{
             $data = array( "success" => false, "message"=>"Some Issue in Discount Update");

        }
        return $data;

    }

    function updatePerKmCharge(Request $request){
        $driver_id = $request->driver_id;
        if(empty($driver_id) || $driver_id == null ){
            $data = array( "success" => false, "message"=>"Driver Id can not be blank");
            print_r(json_encode($data));
            exit;   
        }

        $per_km_charge = $request->per_km_charge;
        if(empty($per_km_charge) || $per_km_charge == null ){
              $data = array( "success" => false, "message"=>"per km charge can not be blank");
            print_r(json_encode($data));
            exit;   
        }
       
        $res= Vehicle::where('driver_id',$driver_id)->update(['per_km_charge'=>$per_km_charge]);

        if($res==1){
             $data = array( "success" => true, "message"=>"Record Successfully Updated");

        }else{
             $data = array( "success" => false, "message"=>"Some Issue in Discount Update");

        }
        return $data;

    }

    function getDriverProfile(Request $request){
        /*  try {*/
         
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){      
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;            
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        } 

        if(empty($request->driver_id) || $request->driver_id == null ){
              $data = array( "success" => true, "message"=>"Driver Id can not be blank");
            print_r(json_encode($data));
            exit;   
        }
        $profile =  DB::table('drivers as d')->select('d.id as driver_id','d.name' , 'd.mobile' , 'd.email','d.discount','d.allow_discount' , 'd.gender' , 'd.profile_image' , 'd.address' , 'd.zip_code' , 'd.country as country_id' ,  'd.state as state_id' , 'd.city as city_id' , 'countries.name as country_name' , 'states.name as state_name' , 'cities.name as city_name','driver_plan.plan_id')
             ->selectRaw("AVG(driver_rating.rating)  as rating")
             ->leftJoin('countries' , 'countries.id' , '=' , 'd.country')
             ->leftJoin('states' , 'states.id' , '=' , 'd.state')
             ->leftJoin('cities' , 'cities.id' , '=' , 'd.city')
             ->leftJoin('driver_rating' , 'driver_rating.driver_id' , '=' , 'd.id')
             ->leftjoin('driver_plan', 'd.id','=','driver_plan.driver_id')
             ->where('d.id' , '=' , $request->driver_id)
             ->first();  
            $id= $request->driver_id;
            $name = str_replace(' ', '',$profile->name);
            $referral_code = $id.'_'.$name; 
            $profile->referral_code = $referral_code;

            /*  return $profile;*/
            //echo "<pre>"; print_r($profile); die();

            if(!empty($profile->rating)){
                $profile->rating = round($profile->rating).' Profile Rating';
            }
            

             foreach ($profile as $key => $value) {
                    if($value == null || empty($value))
                         $profile->$key = '';
             }

             if(!empty($profile->profile_image) && file_exists('Admin/profileImage/'.$profile->profile_image)){
                $profile->profile_image = URL::to('/').'/Admin/profileImage/'.$profile->profile_image;
             }else{
                 $profile->profile_image = '';
             }

            if(!empty($profile)){
                $data = array("success"=>true, "message"=>"data found" , "data" => $profile );
                $response = response($data);
            }else{
                $data = array("success"=>true, "message"=>"data not found" );
                $response = response($data);
            }

            return $response;
            
        /*} catch ( \Exception $e) {
            return  $response = response(['success' => true ,'message' => 'something went wrong']);
        }*/
    }
    public function getInvteCode(Request $request){
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){      
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;            
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        } 


        $driver_id = $request->input('driver_id');
        if($driver_id==null || $driver_id==''){      
            $data = array("success"=>false, "message"=>"Driver Id can not be blank");
            print_r(json_encode($data));
            exit;                 
        }
        $drivers = DB::table('drivers')->where('id',$driver_id)->first();
        $name = str_replace(' ', '',$drivers->name);
        $referral_code = $name.'_'.$driver_id; 
        $data = array("success"=>true, "message"=>"Driver Referral code","referral_code"=>$referral_code);
         print_r(json_encode($data));
      
    }

    public function getDriverDocuments(Request $request){
        
        $security_token = $request->header('stoken');

        $driver_id = $request->input('driver_id');
        if($driver_id==null || $driver_id==''){      
            $data = array("success"=>false, "message"=>"Driver Id can not be blank");
            print_r(json_encode($data));
            exit;                 
        }

        $reseponse =   DriverDocument::Select('*')->where('driver_id', $driver_id)
            ->orderBy('id', 'desc') 
            ->get();

        $destinationPath =url('/')."/Admin/driver_documents/";
        $absulutepath ="Admin/driver_documents/";
        if(isset($reseponse[0]) ){
            if(file_exists($absulutepath.$reseponse[0]->driving_licence)){
                $driving_licence = $destinationPath.$reseponse[0]->driving_licence;
            }else{
                $driving_licence ='';
            }
        }
        if(isset($reseponse[0]) ){
            if(file_exists($absulutepath.$reseponse[0]->vehicle_registration)){
                $vehicle_registration = $destinationPath.$reseponse[0]->vehicle_registration;
            }else{
                $vehicle_registration ='';
            }
        }

         if(isset($reseponse[0]) ){
            if(file_exists($absulutepath.$reseponse[0]->insurance)){
                $insurance = $destinationPath.$reseponse[0]->insurance;
            }else{
                $insurance ='';
            }
        }

        if(isset($reseponse[0]) ){
            if(file_exists($absulutepath.$reseponse[0]->id_proof)){
                $id_proof = $destinationPath.$reseponse[0]->id_proof;
            }else{
                $id_proof ='';
            }
        }
       
        if(isset($reseponse[0]) ){
            $data = array('driver_id'=>$driver_id,'driving_licence'=>$driving_licence,'vehicle_registration'=>$vehicle_registration,'insurance'=>$insurance,'id_proof'=>$id_proof);           
            $res = array("success"=>true, "message"=>"Driver Documents", "data"=>$data);
            return $res;
            exit;  

        }else{
            $data = array('driver_id'=>$driver_id,'driving_licence'=>'','vehicle_registration'=>'','insurance'=>'','id_proof'=>'');           
            $res = array("success"=>false, "message"=>"Driver not found", "data"=>$data);
            return $res;
            exit;  

        }
                       
    }

    public function getPlanDetail(Request $request){
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){      
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;            
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        } 

        $driver_id = $request->input('driver_id');
        if($driver_id==null || $driver_id==''){      
            $data = array("success"=>false, "message"=>"Plan Id can not be blank");
            print_r(json_encode($data));
            exit;                 
        }

        

        $data=DB::table('driver_plan')
           ->join('subcription_plans','subcription_plans.id','=','driver_plan.plan_id') 
            ->join('plan_type','subcription_plans.plan_type', '=', 'plan_type.id')
          
            ->select('subcription_plans.*','plan_type.name as plan_type','driver_plan.start_date','driver_plan.end_date','driver_plan.renewal_date')
            ->where('driver_plan.driver_id',$driver_id)
            ->where('subcription_plans.is_active','1')->get();

        if(count($data)>0){
            
            foreach ($data as $key => $value) {
                if($value->renewal_date==null){
                    $value->renewal_date=""; 
                }
                unset($value->created_at);
                unset($value->updated_at);
                unset($value->deleted_at);
                if($value->end_date==null){
                    $value->end_date="";
                }else{
                   $value->end_date=date('m/d/Y',strtotime($value->end_date));
                }
                if($value->start_date==null){
                      $value->start_date="";
                }else{
                   $value->start_date=date('m/d/Y',strtotime($value->start_date));
                }
        
            }

            $res = array("success"=>true, "message"=>"Plan Detail",'data'=>$data[0]);
            return $res;
            exit; 
        }else{
            $res = array("success"=>false, "message"=>"Plan Id not matched",'data'=>array());
            return $res;
            exit; 
        }
        
 
    }

    public function driverRideHistory(Request $request){
        $security_token = $request->header('stoken');
        if($security_token==null || $security_token==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }
        $driver_id = intval($request->input('driver_id'));  
        if($driver_id==null || $driver_id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Driver id can not be blank");
            print_r(json_encode($data));
            exit;
        }
  
        $rides = DB::table('booking')
            ->join('drivers', 'booking.driver_id', '=', 'drivers.id') 
            ->leftjoin('vehicles','vehicles.driver_id','=','drivers.id')
           
            ->leftjoin('vehicle_category','vehicles.vehicle_category','=','vehicle_category.id')
            ->leftjoin('customers','booking.customer_id','=','customers.id')
            ->select('booking.id as booking_id','booking.booking_status','customers.name as customer_name','final_amount','pickup_addrees','destination_address','booking_time','completed_time','start_time', 'customers.image as customer_image','customers.mobile', 'vehicle_name','vehicle_category.name as vehicle_category','registration_number')
            ->where('booking.driver_id',$driver_id)
            ->where('booking.booking_status','<>','auto')
            ->orderBy('booking.id', 'desc')
            ->get();
 

 
            if(count($rides)>0){
                $data=array();
                foreach ($rides as $key => $value) {
                    # code...
                    $booking_time=date('D d M Y g:i A',strtotime($value->booking_time));
                    $value->booking_time  = $booking_time;
                    if($value->completed_time==null){
                         $value->completed_time ='0000-00-00 00:00:00';
                    }
                    $destinationPath =  url('/').'/Admin/customerimg/';
                    $image=$destinationPath."noimage.png";
                    if($value->customer_image!=''){
                        if(file_exists($destinationPath.$value->customer_image)){
                            $image=$destinationPath.$value->customer_image;
                        }
                    }
                    $value->customer_image = $image;
                    if($value->vehicle_category==null){
                        $value->vehicle_category ="No category Selected";
                    }else{
                        $value->vehicle_category = $value->vehicle_category;
                    }
                    
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
                    
                    //print_r($value->booking_time);
                }
                
                $data = array("success"=>true, "message"=>"Your All Rides" ,'data'=>$rides);
            }else{
               // $obj = (object)[];
                $data = array("success"=>false, "message"=>"No Rides Found",'data'=>array());
            }
            return $data;
    }

 
    public function placeOrder(Request $request){

        $driver_id = $request->driver_id;
        $plan_id = $request->plan_id;
        $amount=0;

        $plan = Plan::where('id',$plan_id)->first();
        if(isset($plan->plan_cost)){
            $amount=$plan->plan_cost;
        }else{
            $data = array("success"=>false, "message"=>"No Amount Set",'data'=>array());
        }
          
        $existing_plan =  DB::table('driver_plan')->join('subcription_plans','subcription_plans.id','=','driver_plan.plan_id')->select('plan_cost')->where('driver_plan.end_date','>',date('Y-m-d'))->orderBy('driver_plan.id','desc')->first();
   
       /* echo "<pre>";
        print_r($existing_plan);*/

        $paid_cost = 0;
        if(isset($existing_plan->plan_cost)){
            $paid_cost = $existing_plan->plan_cost;
        }

        $amount= $amount-$paid_cost;

        $amount=10;
        
        
        $driver=Driver::where('id',$driver_id)->get();

         if(isset($driver[0]) && (count($driver)>0)){
            $driver = $driver[0];
            //return view('admin.order',compact('driver','amount','plan_id'));
        }else{
            $data = array("success"=>false, "message"=>"No Driver Exist",'data'=>array());
        }

         
        $rsa = new Crypt_RSA();
        // unique_order_id|total_amount
        $plaintext = '525|'.$amount;
        
 
      
         
        $publickey ="-----BEGIN PUBLIC KEY-----
                    MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDSickkJGa7YTaR8jcYzClHdDw2
                    xvfKNT0DTUeXMEwc0865wcT4v1W5dVXrUzC6XdrD7b5ZZrmOfAYnUDwOqCXGg/de
                    LYzvuFQovK1ve0UYTFJKlox9EBUo0Nt+bg8OTH194H6AWCIWnTLu3girVLHnL1M/
                    f3GhdZQKN+x+VRbz/wIDAQAB
                    -----END PUBLIC KEY-----";
        $skey="a662e418-0dec-4cd2-9f70-77456dd89024";
        //load public key for encrypting
        $rsa->loadKey($publickey);
        $encrypt = $rsa->encrypt($plaintext);
        //encode for data passing
        $payment = base64_encode($encrypt);
        $url = 'https://webxpay.com/index.php?route=checkout/billing';
        $custom_fields = base64_encode($driver_id.'|'.$plan_id);

        return view('admin.order',compact('driver','amount','plan_id','custom_fields','payment'));
      
    } 
 
    public function orderResponse(Request $request){
        //include   public_path().'/Crypt/RSA.php';
        $payment = base64_decode($request->payment);
        $signature = base64_decode($request->signature);
        $custom_fields = base64_decode($request->custom_fields);
        $rsa = new Crypt_RSA();
        $publickey ="-----BEGIN PUBLIC KEY-----
                    MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDSickkJGa7YTaR8jcYzClHdDw2
                    xvfKNT0DTUeXMEwc0865wcT4v1W5dVXrUzC6XdrD7b5ZZrmOfAYnUDwOqCXGg/de
                    LYzvuFQovK1ve0UYTFJKlox9EBUo0Nt+bg8OTH194H6AWCIWnTLu3girVLHnL1M/
                    f3GhdZQKN+x+VRbz/wIDAQAB
                    -----END PUBLIC KEY-----"; 
        
        $rsa->loadKey($publickey); 
        //verify signature
        $signature_status = $rsa->verify($payment, $signature) ? TRUE : FALSE;

         /*  order_id 0 |order_refference_number 1 |date_time_transaction 2 |status_code 3 |comment 4|payme
        nt_gateway_used  5*/

        $responseVariables = explode('|', $payment);       
        $custom_fields_variable = explode('|', $custom_fields);

        $transition_id  = $responseVariables[1];
        $transaction_time = $responseVariables[2];
        $payment_status = $responseVariables[3];
        $payment_description = $responseVariables[4];
        $payment_type = $responseVariables[5];

        $driver_id = $custom_fields_variable[0];
        $plan_id = $custom_fields_variable[1];


 
        if($payment_status==0){

            $plan_data = SubcriptionPlan::select('*')->where('id',$plan_id)->first()->toarray();

            $amount = $plan_data['plan_cost'];
            $payment = new Payment;
            $payment->driver_id = $driver_id;
            $payment->plan_id = $plan_id;
            $payment->transaction_id = $transition_id;
            $payment->payment_description = $payment_description;
            $payment->payment_status = $payment_status;
            $payment->amount = $amount ;
            $payment->payment_type = $payment_type;
            $res = $payment->save();

            $existing_plan =  DriverPlan::where('end_date','>',date('Y-m-d'))->first();
            if(isset($existing_plan->end_date)){
                $existing_end_date = $existing_plan->end_date;


                $now = time();

                $existing_end_date = strtotime($existing_end_date);
                $datediff = $now - $existing_end_date;

                $days= round($datediff / (60 * 60 * 24));
            }else{
                $days= 0; 
            }


            if($res){
                Driver::where('id', $driver_id)->update(['plan_id'=>$plan_id,'is_plan_active'=>1,'is_active'=>1]); 

                $start_date = date("d-m-Y");
                $expire_date  = date("d-m-Y");
                if($plan_data['plan_type'] == '2'){
                    //$days=30+$days;

                    $expire_date = date('d-m-Y',strtotime('+30 days',strtotime($start_date)));
                }elseif($plan_data['plan_type'] == '1'){
                    //$days=7+$days;
                    $expire_date = date('d-m-Y',strtotime('+7 days',strtotime($start_date)));
                }elseif($plan_data['plan_type'] == '3'){
                    // $days=7+$days;
                    $expire_date = date('d-m-Y',strtotime('+1 years',strtotime($start_date)));
                }

                $end_date = date('d-m-Y',strtotime('+'.$days.' days',strtotime($expire_date)));

              

                $driver_plan = new DriverPlan;
                $driver_plan->driver_id  = $driver_id;
                $driver_plan->plan_id    = $plan_id;
                $driver_plan->start_date = date('Y-m-d H:i:s',strtotime($start_date));
                $driver_plan->end_date   = date('Y-m-d H:i:s',strtotime($end_date));
                $res = $driver_plan->save();


                $data = array("success"=>true, "message"=>"Payment Successfully done",'payment_success'=>true);
            }else{
                
                $data = array("success"=>false, "message"=>"Something went wrong",'payment_success'=>false);
            }


         }else{
            $data = array("success"=>false, "message"=>"Payment Unsuccessed",'payment_success'=>false);
        }
        
       print_r(json_encode($data)) ;

    }


     public function driverForgotPassword (Request $request) {
            $input = $request->all();
            $mobile_or_email = $request->input('mobile_email');
            if($mobile_or_email==null || $mobile_or_email==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Please enter mobile or email.");
            print_r(json_encode($data));
            exit;
        }
        
            if (strpos($mobile_or_email, '@') !== false) {
                $validator  = Validator :: make($input,[
                  "mobile_email"         => "required | Email",
                ]);
      
                if($validator->fails()){
                $data = array("success"=>false, "message"=>"Please enter valid email.");
                print_r(json_encode($data));
               }
               else{
                    $get_customer = Driver::where('email','=',$mobile_or_email)->get();
                    if(!empty($get_customer[0]->email)){
                     //
                         $password = time();
                         $password_crypt =  Hash::make($password); 
                         $res=Driver::where('id', $get_customer[0]->id)
                            ->update(['password' => $password_crypt]);
                         $objDemo = new \stdClass();
                         $objDemo->demo_one = 'Your new password is '.$password;
                         $objDemo->sender =  Config::get('constants.SENDER_EMAIL');
                         $objDemo->sender_name =  Config::get('constants.SENDER_NAME');
                         $objDemo->receiver = $get_customer[0]->email;
                         //$objDemo->receiver = "sumit.parmar@tekzee.com";
                         $objDemo->receiver_name = $get_customer[0]->name;
                         $objDemo->subject = "Forgot Password Mail";
                         $mail= Mail::to($objDemo->receiver)->send(new DemoEmail($objDemo));  
                         $data = array("success"=>true, "message"=>"Password updated successfully..");
                         print_r(json_encode($data));
                         
                 }
                 else{
                       $data = array("success"=>false, "message"=>"No record found..");
                       print_r(json_encode($data));
                 }
                }
            }
        else{
              $validator  = Validator :: make($input,[
                  "mobile_email"         => "required | Digits:10",
               ]);
               if($validator->fails()){
                $data = array("success"=>false, "message"=>"Please enter valid mobile.");
                print_r(json_encode($data));
               }else{
                    $get_customer = Driver::where('mobile','=',$mobile_or_email)->get();
               if(!empty($get_customer[0]->email)){
                     //
                     //$password = time();
                     $password = time();
                     $password_crypt =  Hash::make($password); 
                     $res=Driver   ::where('id', $get_customer[0]->id)
                            ->update(['password' => $password_crypt]);
                     $mobilearray = array($get_customer[0]->mobile);
                     $msg = "Your new password is ".$password;
                     $res = commonSms($mobilearray,$msg);
                     $data = array("success"=>true, "message"=>"Password updated successfully..");
                     print_r(json_encode($data));
                }
                 else{
                        $data = array("success"=>false, "message"=>"No record found..");
                        print_r(json_encode($data));
                 }
              }
            }
        }
 
        public function driverResetPassword(Request $request){
          $input = $request->all();
          $security_token = $request->header('stoken');
          if($security_token==null || $security_token==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }
         $validator  = Validator :: make($input,[
                  "userid"         => "required",
                  "old_password"   => "required",
                  "new_password"   => "required" 
               ]);
      
               if($validator->fails()){
                $data = array("success"=>false, "message"=>$validator->errors()->first());
                print_r(json_encode($data));
               }
               else{
                    $get_driver = Driver::where('id','=',$input['userid'])->get();
                    if(Hash::check($input['old_password'], $get_driver[0]->password)){
                         $newpassword          = $input['new_password'];
                         $newpassword_crypt    =  Hash::make($newpassword); 
                         $res=Driver::where('id', $get_driver[0]->id)
                            ->update(['password' => $newpassword_crypt]);
                         $objDemo = new \stdClass();
                         $objDemo->demo_one = 'You have successfully changed your password.';
                         $objDemo->sender =  Config::get('constants.SENDER_EMAIL');
                         $objDemo->sender_name =  Config::get('constants.SENDER_NAME');
                         $objDemo->receiver = $get_driver[0]->email;
                         //$objDemo->receiver = "sumit.parmar@tekzee.com";
                         $objDemo->receiver_name = $get_driver[0]->name;
                         $objDemo->subject = "Password Changed Mail";
                         $mail= Mail::to($objDemo->receiver)->send(new DemoEmail($objDemo));  
                         $data = array("success"=>true, "message"=>"Password changed successfully.");
                         print_r(json_encode($data));
                        
                    }
                    else{
                     $data = array("success"=>false, "message"=>"Old password not matched.");
                     print_r(json_encode($data));   
                    }
                }
    }

    //Schedule booking list for Deriver
    public function getScheduleBookingsDriver(Request $request)
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
                       'driver_id' =>'required'
                       ]);
            if($validator->fails()){
               $data = array("success" => false, "message" => $validator->errors()->first(), "data" => $obj);
               return $data;exit;
            }
            $driver_id = $input['driver_id'];
            $filter = isset($input['filter']) ? $input['filter'] : '';
                
            $rides = DB::table('booking')

            ->select('booking.id as booking_id','booking.booking_status','pickup_addrees','destination_address','start_time','booking_time')
            ->where('booking.driver_id',$driver_id)
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

    //get Schedule booking detail for Driver
    public function scheduleBookingDetailDriver(Request $request)
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
                
            $ride = DB::table('booking')
            ->join('drivers', 'booking.driver_id', '=', 'drivers.id') 
            ->leftjoin('vehicles','vehicles.driver_id','=','drivers.id')
           
            ->leftjoin('vehicle_category','vehicles.vehicle_category','=','vehicle_category.id')
            ->leftjoin('customers','booking.customer_id','=','customers.id')
            ->select('booking.id as booking_id','customers.id as user_id','booking.driver_id','booking.booking_status','customers.name as customer_name','drivers.name as driver_name','final_amount','pickup_addrees','destination_address','pickup_lat','pickup_long','drop_lat','drop_long','booking_time','completed_time','start_time', 'customers.image as customer_image','customers.mobile','drivers.mobile as driver_mobile','drivers.profile_image as driver_image', 'vehicle_name','vehicle_category.name as vehicle_category','registration_number','vehicles.vehicle_image','booking.otp')
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

        if (isset($cat->basefare)) {
            $basefare = $cat->basefare;
        } else {
            $basefare = 20;
        }

        if (isset($cat->per_km_charges)) {
            $per_km_chargs = $cat->per_km_charges;
        } else {
            $per_km_chargs = 20;
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

        $time_charges = 0;

        $settings = Setting::get()->keyBy('key');
        if(isset($settings['wait_charge_permin']->value) && ($settings['wait_charge_permin']->value!='') ){
            $per_min_charges = $settings['wait_charge_permin']->value;
        }else{
            $per_min_charges = 2 ;
        }

        if(isset($settings['base_km']->value) && ($settings['base_km']->value!='') ){
            $base_km = $settings['base_km']->value;
        }else{
            $base_km = 1 ;
        }

        //$time_charges = $time_min*$per_min_charges;
        $time_charges = 0;
        //km charges calculation
        if ($km < $base_km) {
            $distance_charges = $basefare;
        } else {
            $distance_charges = (($km - $base_km) * $per_km_chargs) + $basefare;
        }
        $total_charges = $distance_charges + $time_charges;

        //end here
        //+- 20 amount
        $next = $total_charges + 20;
        $charges = number_format($total_charges) . '-' . number_format($next);

        return $charges; 
    }


    //Driver out For Customer Pickup
    public function outForCustomerPickup(Request $request) {
        try{
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

        $user_id = $request->input('user_id');
        if ($user_id == null || $user_id == '') {
            $data = array("success" => false, "message" => "Customer Id can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $booking_time = $request->input('booking_time');
        if ($booking_time == null || $booking_time == '') {
            $data = array("success" => false, "message" => "Booking time can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $customer = DB::table('booking')
                    ->join('customers', 'booking.customer_id', '=', 'customers.id')
                    ->select('customers.device_token', 'customers.type')
                    ->where('booking.booking_status','accept')
                    ->where('booking.id', $booking_id)
                    ->where('booking.customer_id', $user_id)
                    ->where('booking.driver_id', $driver_id)
                    ->first();
        if($customer){
            $token = $customer->device_token;
            $device_type = $customer->type;    
            
            $date = date('Y-m-d');
            $booking_date = date('Y-m-d',strtotime($booking_time));
            $actual_date = date('D d M Y',strtotime($booking_time));

            if($date == $booking_date){
                $status = 'in_progress';
                $res = Booking::where('id', $booking_id)
                        ->update(['booking_status' => $status]);


               $type = 'OUT_FOR_PICKUP';
            
                $title = "Pickup Request";
                $message = "Please be ready, Driver is on the ways to pick you up.";
                $msg = array('body' => $message, 'title' => $title);

                $fields = array(
                    'to' => $token,
                    'notification' => $msg,
                    'data' => array(
                        'bookingid' => $booking_id, 
                        'type' => $type, 
                        'message' => $title
                    )
                );    
            
                              
                //notification will send to customer 
                if ($device_type == "1") {
                    $this->fcmNotification($msg, $fields, 'customerapp');
                } else {
                   
                    $this->iosNotification($msg, $fields, 'customerapp', $environment);
                }    
                $data = array("success" => true, "message" => "Please be ready, Driver is on the ways to pick you up","data"=>$customer); 
            }else{
                $data = array("success" => false, "message" => "Sorry! Today you are not able to place pickup request, you can try  pickup request on ".$actual_date,"data"=>(object)[]);
            }
        }else{
            $data = array("success" => false, "message" => "Booking not found","data"=>(object)[]);
        }            
    
        return $data;
        exit;
    }catch(Exception $e){
        print_r($e->getMessage());die;
    }
    }


    //Driver wants to cancel booking
    public function cancelScheduleBookingByDriver(Request $request) {
        try{
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

            $user_id = $request->input('user_id');
            if ($user_id == null || $user_id == '') {
                $data = array("success" => false, "message" => "Customer Id can not be blank");
                print_r(json_encode($data));
                exit;
            }

            $booking_time = $request->input('booking_time');
            if ($booking_time == null || $booking_time == '') {
                $data = array("success" => false, "message" => "Booking time can not be blank");
                print_r(json_encode($data));
                exit;
            }

            $customer = DB::table('booking')
                        ->join('customers', 'booking.customer_id', '=', 'customers.id')
                        ->select('customers.device_token', 'customers.type','booking.start_time')
                        ->where('booking.booking_status','accept')
                        ->where('booking.id', $booking_id)
                        ->where('booking.customer_id', $user_id)
                        ->where('booking.driver_id', $driver_id)
                        ->first();
            if($customer){
                $token = $customer->device_token;
                $device_type = $customer->type;    
                $start_time = $customer->start_time;    
                if ($start_time != '0000-00-00 00:00:00') {
                    $data = array("success" => false, "message" => "You cant cancel Onride Booking");
                    print_r(json_encode($data));
                    exit;
                }

                $date = date('Y-m-d');
                $booking_date = date('Y-m-d',strtotime($booking_time));
                $actual_date = date('D d M Y',strtotime($booking_time));

                if($date != $booking_date){
                    $time = date('Y-m-d H:i:s');
                    $status = "canceled";
                    Booking::where('id', $booking_id)
                        ->update(['booking_status' => $status, 'cancel_time' => $time, 'canceled_by' => "driver"]);


                    $type = '3';
                    $title = "Booking Cancelled";
                    $message = "Your booking id ".$booking_id." has been cancelled by Driver.";
                    $msg = array('body' => $message, 'title' => $title);

                    $fields = array(
                        'to' => $token,
                        'notification' => $msg,
                        'data' => array(
                            'bookingid' => $booking_id, 
                            'type' => $type, 
                            'message' => $title
                        )
                    );                 
                    //notification will send to customer 
                    if ($device_type == "1") {
                        $this->fcmNotification($msg, $fields, 'customerapp');
                    } else {
                       
                        $this->iosNotification($msg, $fields, 'customerapp', $environment);
                    }    
                    $data = array("success" => true, "message" => "Your booking ".$booking_id." have been cancelled by Driver.","data"=>$customer); 
                }else{
                    $data = array("success" => false, "message" => "Sorry! You are not able to cancel booking on same day. For more information please contact with admin","data"=>(object)[]);
                }
            }else{
                $data = array("success" => false, "message" => "Booking not found","data"=>(object)[]);
            }            
        
            return $data;
            exit;
        }catch(Exception $e){
            print_r($e->getMessage());die;
        }
    }

    public function noPaymentRequired(Request $request)
    {
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

        $driver = DB::table('booking')
                    ->select('booking.id as booking_id','booking.booking_time','drivers.type','drivers.device_token','drivers.id as driver_id','drivers.profile_image','vehicles.vehicle_image')
                    ->join('drivers', 'booking.driver_id', '=', 'drivers.id')
                    ->leftJoin('vehicles', 'drivers.id', '=', 'vehicles.driver_id')
                    ->where('booking.id',$booking_id)
                    ->first();     
        if(!empty($driver)){

            $booking_status = 'completed';
            $completed_time = date('Y-m-d H:i:s');

            DB::table('booking')->where('id',$booking_id)
                ->update(['is_payment'=>1,'booking_status'=>$booking_status,'completed_time'=>$completed_time]);

            $destinationPath = 'Admin/profileImage';
            $image = url('/') . '/' . $destinationPath . '/' . 'noimage.png';
            if (isset($driver->profile_image) && ($driver->profile_image != '')) {
                $driver->profile_image = url('/') . '/' . $destinationPath . '/' . $driver->profile_image;
                
            }else{
                $driver->profile_image = $image;
            }

            $vehiclePath = 'Admin/vehicleImage';
            $vehicle_img = url('/') . '/' . $vehiclePath . '/' . 'noimage.png';
            if (isset($driver->vehicle_image) && ($driver->vehicle_image != '')){
                $driver->vehicle_image = url('/') . '/' . $vehiclePath . '/' . $driver->vehicle_image;
                
            }else{
                $driver->vehicle_image = $vehicle_img;
            }    

            $token = $driver->device_token;
            $ntitle = "Payment not Required";
            $nmessage = "Payment not required for this booking";
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

            unset($driver->type);
            unset($driver->device_token);

            $data = array("success" => true, "message" => "Payment not required for this booking","data"=>$driver);
        }else{
            $data = array("success" => false, "message" => "booking not found","data"=>(object)[]);
        }

        return $data;  
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
            $customer = 'Admin/CertificatesCustomerProduction.pem';
            $driver = 'Admin/CertificatesDriverProduction.pem';
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

 }
