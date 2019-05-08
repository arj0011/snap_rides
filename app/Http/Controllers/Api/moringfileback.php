<?php

namespace App\Http\Controllers\Api;
 

use App\Http\Controllers\Controller;
use DB;
use App\Driver;
use App\Vehicle;
use App\DriverDocument;
use App\SubcriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiDriverController extends Controller
{
    //

    public function validateAppVersion(Request $request){
        $this->current_version=1;
        $appversion = $request->input('appversion');
        if($appversion==null || $appversion==''){
            $status = false;
            $data = array("success"=>false, "message"=>"App Version can not be blank");
            print_r(json_encode($data));
            exit;
        }

        if($appversion!=$this->current_version){
            $status = false;
            $data = array("success"=>false, "message"=>"Please Update yout App Version."); 
        }else if($appversion==$this->current_version){
            $status = true;
            $data = array("success"=>true, "message"=>"You are in latest App Version.");
        }
        print_r(json_encode($data));
        exit;

    }

     public function imageUpdate (Request $request){
      
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

        if($type=='profile'){

            $reseponse = Driver::Select('profile_image as image')->where('id', $driver_id)
            ->orderBy('id', 'desc') 
            ->get();
 
        
           
            Driver::where('id', $driver_id)->update(['profile_image'=>$image]);

        }else if($type=='vehicle'){

            $reseponse = Vehicle::Select('vehicle_image as image')->where('driver_id', $driver_id)
            ->orderBy('id', 'desc') 
            ->get();
           
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

/*
        $data=array('success'=>true,'message'=>"Image Uploaded","data"=>array());
             print_r(json_encode($data));
             exit;*/
    
   //echo $oldimage=$reseponse[0]->image;
        if(isset($reseponse[0]->image) && ($reseponse[0]->image!='')){
                $oldimage=$reseponse[0]->image;
                if(file_exists($destinationPath.'/'.$oldimage)){

                    unlink($destinationPath.'/'.$oldimage);
                } 

        }

        $data=array('success'=>true,'message'=>"Image Uploaded","data"=>array());
             print_r(json_encode($data));

     
    } 

   

    public function driverRegistration(Request $request){
       /* $appversion = $request->header('appversion');
        if($appversion==null || $appversion==''){
            $status = false;
            $data = array("success"=>false, "message"=>"App Version can not be blank");
            print_r(json_encode($data));
            exit;
        }*/
        if($request->input('device_token')!=null){
            $device_token  = $request->input('device_token');
        }else{
            $device_token  = '';
        }
        $type  = $request->header('type');
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

        //mobile email existance script
        
        $query = DB::table('drivers')->where('is_registered' , '=' , '1')
                 ->where(function($query) use ($email,$mobile) {
                    $query -> where('mobile'  , '=' , $mobile )->orWhere('email'  , '=' , $email );
                })
            ->count();

      /*  $sql="select id from driver where (mobile='$mobile' or email='$email') and is_registered= 1 ";
          DB::table(
        print_r($sql);
        die;*/
        
        if($query){
            $status = false;
            $data = array("success"=>false, "message"=>"this mobile or email already exist");
            print_r(json_encode($data));
            exit;
        }

        

        $destinationPath = 'Admin/profileImage';
        /*$file = $request->file('image');
        $destinationPath = 'Admin/profileImage';
       
        if($file!=null || $file!=''){
            $file->move($destinationPath,$file->getClientOriginalName());
            $image=$file->getClientOriginalName();

            $imagepath=url('/').'/'.$destinationPath.'/'.$image;
        }else{
            $image='';
            $imagepath=url('/').'/'.$destinationPath.'/'.'noimage.png';
        }
*/        

        $imagepath=url('/').'/'.$destinationPath.'/'.'noimage.png';
        $res = Driver::select('id','name','email','mobile','profile_image','is_registered')->where('mobile', $mobile)
               ->orderBy('name', 'desc') 
               ->get();

        $otp='1234';
        $auth_token = '987654321';
        $dataarray = array();
        if(count($res)<1){ //no data
            $driver = new Driver;
            $driver->name = $name;
            $driver->mobile = $mobile;
            $driver->email = $email;
            $driver->password = $password;
            $driver->otp = $otp;
            $driver->device_token = $device_token;
            $driver->type = $type;
            //$driver->appversion = $appversion;
            $driver->stoken = $auth_token ;
            //$driver->profile_image = $image;
            $driver->is_registered = 0;
            $driver->save();
            $id=$driver->id;

            $dataarray=array('driver_id'=>$id,'name'=>$name,'email'=>$email,'mobile'=>$mobile,'image'=>$imagepath, 'stoken'=>$auth_token ,'is_registered'=>'0');
            $data = array("success"=>true,"message"=>"Driver Successfully Register, Please verify OTP","data"=>$dataarray);
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
                $driver->password = $password;
                $driver->otp = $otp;
                $driver->device_token = $device_token;
                $driver->type = $type;
                //$driver->appversion = $appversion;
                $driver->stoken = $auth_token ;
                //$driver->profile_image = $image;
                $driver->is_registered = 0;
                $driver->save();
                $dataarray=array('driver_id'=>$id,'name'=>$name,'email'=>$email,'mobile'=>$mobile,'image'=>$imagepath, 'stoken'=>$auth_token ,'is_registered'=>'0');
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

    public function verifyOTP (Request $request) {
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

        $otp = $request->input('otp');
        if($otp==null || $otp==''){
            $status = false;
            $data = array("success"=>false, "message"=>"OTP can not be blank");
            print_r(json_encode($data));
            exit;
        }
     
        $res=Driver::where('mobile', $mobile)
            ->where('otp', $otp)
            ->update(['is_registered' =>1]);

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

    /*  $type  = $request->header('type');
        if($type==null || $type==''){
            $data = array("success"=>false, "message"=>"Type can not be blank");
            print_r(json_encode($data));
            exit;
        } */

        $driver_id  = $request->input('driver_id');
        if($driver_id==null || $driver_id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Driver Id can not be blank");
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

    /*    $vehicle_no  = $request->input('vehicle_no');
        if($vehicle_no==null || $vehicle_no==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Vehicle Number can not be blank");
            print_r(json_encode($data));
            exit;
        }*/
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

     /*   $file = $request->file('image');
        $destinationPath = 'Admin/vehicleImage';
        if($file!=null || $file!=''){
            $file->move($destinationPath,$file->getClientOriginalName());
            $image=$file->getClientOriginalName();
            $imagepath=$destinationPath.'/'.$image;
        }else{
            $image='';
            $imagepath='';
        }
*/
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
           // $vehicle->vehicle_no = $vehicle_no;
            $vehicle->registration_number = $vehicle_reg;
            $vehicle->color = $color;
           // $vehicle->vehicle_image = $image;
            $vehicle->driver_id = $driver_id;
            $vehicle->registration_date = $registration_date;
            $vehicle->save();
            Driver::where('id',$driver_id)->update(['is_vehicle_registered' =>1]);
            //$dataarray= array('' => , );
            $data = array("success"=>true,"message"=>"Vehicle Successfully Register","data"=>$dataarray);
        }else{
   
            if($driverinfo[0]->is_vehicle_registered==1){
              //  $imagepath = $destinationPath.'/'.$res[0]->profile_image;          
                $data = array("success"=>true,"message"=>"Vehicle Already Register","data"=>$dataarray);  
            }else{

                Vehicle::where('driver_id',$driver_id)->update(['vehicle_name' => $name,'vehicle_category'=>$vehicle_catgory,'registration_number'=>$vehicle_reg,'color'=>$color , 'registration_date' => $registration_date]);

               /* Vehicle::where('driver_id',$driver_id)->update(['vehicle_category'=>$vehicle_catgory,'registration_number'=>$vehicle_reg,'color'=>$color]);*/

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
            $status = false;
            $data = array("success"=>false, "message"=>"Security token can not be blank");
            print_r(json_encode($data));
            exit;
        }else if($security_token!='987654321'){
            $data = array("success"=>false, "message"=>"Please Add Correct Security Token");
            print_r(json_encode($data));
            exit;
        }

        $plans = SubcriptionPlan::select('id','plan_name','plan_description','plan_type','plan_cost','unit')->where('is_active',1)->get();
        $data=array('success'=>true ,'message'=>"Subscription plans",'data'=>$plans );  
        return $data;
    }

    public function driverLogin(Request $request){

        $login_by = '1';
        if($request->input('login_by')!=''){
            $login_by = $request->input('login_by');
        }

        //echo $login_by;
        $mobile = $request->input('mobile');
        $email = $request->input('email');
        $password = $request->input('password');
        $driver = Driver::where('password',$password);
        if($login_by=='1'){
            $driver->where('mobile',$mobile) ;
        }else if($login_by=='2'){
            $driver->where('email',$email) ;
        }

        $dataarray=$driver->get();

 
        
        $destinationPath = 'Admin/profileImage';
        if(isset($dataarray[0]->profile_image) && $dataarray[0]->profile_image!=''){
            $imagepath=url('/').'/'.$destinationPath.'/'.$dataarray[0]->profile_image;
            if(file_exists($imagepath)){
                $imagepath=$imagepath;
            }else{
                $imagepath=url('/').'/'.$destinationPath.'/'.'noimage.png';
            }
             
          }else{
               $imagepath=url('/').'/'.$destinationPath.'/'.'noimage.png';
          }
      
    
        if((isset($dataarray[0])) && count($dataarray)>0){

            $data=array(
             'driver_id'=>$dataarray[0]->id,
            'name'=>$dataarray[0]->name,
            'email'=>$dataarray[0]->email,
            'mobile'=>$dataarray[0]->mobile,
            'is_registered'=>$dataarray[0]->is_registered,
            'is_vehicle_registered'=>$dataarray[0]->is_vehicle_registered,
            'is_documentation'=>$dataarray[0]->is_documentation ,
            'is_plan_active'=>$dataarray[0]->is_plan_active,
            'plan_id'=>$dataarray[0]->plan_id,
            'profile_image'=>$imagepath,
            'is_approved'=>$dataarray[0]->is_approved,
            'stoken'=>'987654321'
             );
            
            $res=array('success'=>true ,'message'=>"Driver Successfully Logined",'data'=>$data );   
        }else{ 
            $res=array('success'=>false ,'message'=>"Credentials not matched",'data'=>array() );    
        }

        return $res;

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
                        $status = true;
                        $data = array("success"=> true, "message"=>"driver is approved");
                        print_r(json_encode($data));
                        exit; 
                    }else{
                        
                        if($data->is_vehicle_registered == '0'){
                            $status = false;
                            $data = array("success"=>false, "message"=>"Vehicle is not registered");
                            print_r(json_encode($data));
                            exit;
                        }

                         if($data->is_documentation == '0'){
                            $status = false;
                            $data = array("success"=>false, "message"=>"document is not verified by admin");
                            print_r(json_encode($data));
                            exit;
                        }
                    }
                }else{
                     $status = false;
                     $data = array("success"=>false, "message"=>"this driver not exist");
                     print_r(json_encode($data));
                     exit; 
                }
            
        } catch ( \Exception $e) {
            return  $response = response(['success' => false ,'message' => 'something went wrong']);
        }
    }

    public function vehicleCategoies(Request $request){
        try {

            $categories =  DB::table('vehicle_category')->select('id','name')->get();            
            if(!empty($categories->toArray())){
                $data = array("success"=>true, "message"=>"data found" , "data" => $categories );
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
     
}
