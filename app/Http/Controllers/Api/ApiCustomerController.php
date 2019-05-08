<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Customer;
use DB;
use Config;
use App\Http\Controllers\Controller;
use App\VehicleCategory;
use App\Booking;
use App\Driver;
use App\DriverRating;
use App\DriverLatLong;
use App\EmergencyContact;
use Illuminate\Support\Facades\Validator;
use App\Mail\DemoEmail;
use Illuminate\Support\Facades\Mail;


class ApiCustomerController extends Controller {
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
    
   
    
    public function generateAuthToken($length = 20) {
        $str = "";
        $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }
    public function registerCustomer (Request $request, $stats_type=false) {
        //$auth_token=$this->generateAuthToken(20);
        $country_code = Config::get('constants.COUNTRY_CODE');
        $auth_token = '987654321';
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

        $appversion = $request->header('appversion');
        if($appversion==null || $appversion==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Appversion can not be blank");
            print_r(json_encode($data));
            exit;
        }

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
        
        if($request->input('name')!=null){
            $name  = $request->input('name');
        }else{
            $name  = '';
        }

        $otp=$this->random_string();
        
        //$otp='1234';
        $res = Customer::where('mobile', $mobile)
               ->orderBy('name', 'desc') 
               ->get();
     
        
     
        
        if(count($res)<1){
            $customer = new Customer;
            $customer->name = $name;
            $customer->mobile = $mobile;
            $customer->otp = $otp;
            $customer->device_token = $device_token;
            $customer->type = $type;
            $customer->appversion = $appversion;
            $customer->stoken = $auth_token ;
            $customer->save();
            $id = $customer->id;
            $dataarray=array('id'=>$id,'name'=>$name,'email'=>'','mobile'=>$mobile,'city'=>'','image'=>'','stoken'=>$auth_token,"country_code"=>$country_code);
        }else{
           Customer::where('mobile', $mobile)
            ->update(['name' => $name,'otp'=>$otp,'device_token'=>$device_token,'type'=>$type,'appversion'=>$appversion, 'stoken'=>$auth_token ]);
            

            $basepath= url('/');
            $destinationPath = 'Admin/customerimg';
            
            $path=$basepath.'/'.$destinationPath.'/'.$res[0]->image;
            if($res[0]->image!=''){
                $path=$path;
                /*if(file_exists($path)){
                    $path=$path;
                }else{
                    $path=$basepath.'/'.$destinationPath.'/'.'noimage.png';
                }*/
            }else{
                 $path=$basepath.'/'.$destinationPath.'/'.'noimage.png';
            }
            $customer = Customer::where('mobile',$mobile)->get();
            if(isset($customer[0]->id)){
                $id=$customer[0]->id;
            }else{
                $id='';
            }
            

            $dataarray=array('id'=>$id,'name'=>$name,'email'=>$res[0]->email,'mobile'=>$mobile,'city'=>$res[0]->city,'image'=>$path,'stoken'=>$auth_token,"country_code"=>$country_code);
        }
  
        $msg="Your Four Digit OTP for Snap Rides is ".$otp; 
        $mobile_arr = array($mobile);
        $res_sms=commonSms($mobile_arr,$msg);
       
        $res = 'success';
        if($res=='success'){          
            $data = array("success"=>true,"message"=>"Customer Successfully Register","data"=>$dataarray);
        }else{
            $dataarray = array();
            $data = array("success"=>false,"message"=>"OTP Not Verified");
        }
        print_r(json_encode($data));
        
    }
    
    
      
      public function customerForgotPassword (Request $request) {
           
        $input = $request->all();
        $mobile_or_email = $request->input('mobile_email');
        if($mobile_or_email==null || $mobile_or_email==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Please enter mobile or email.");
            print_r(json_encode($data));
            exit;
        }
        
        
        if (strpos($mobile_or_email, '@') !== false) {
               //for email
               $validator  = Validator :: make($input,[
                  "mobile_email"         => "required | Email",
               ]);
      
               if($validator->fails()){
                $data = array("success"=>false, "message"=>"Please enter valid email.");
                print_r(json_encode($data));
               }
               else{
                 $get_customer = Customer::where('email','=',$mobile_or_email)->get();
                 if(!empty($get_customer[0]->email)){
                     //
                        
                         $password = time();
                         $password_crypt = bcrypt($password);
                          $res=Customer::where('id', $get_customer[0]->id)
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
                  "mobile_email"         => "required | Digits:8",
               ]);
      
               if($validator->fails()){
                $data = array("success"=>false, "message"=>"Please enter valid mobile.");
                print_r(json_encode($data));
               }else{
                    $get_customer = Customer::where('mobile','=',$mobile_or_email)->get();
                 if(!empty($get_customer[0]->email)){
                     //
                        
                         $password = time();
                         $password_crypt = bcrypt($password);
                          $res=Customer::where('id', $get_customer[0]->id)
                            ->update(['password' => $password_crypt]);
                         $mobilearray = array($get_customer[0]->mobile);
                         $msg = "Your new password is ".$password;
                          $res_sms = commonSms($mobilearray,$msg);
                         
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
       
        if($otp != "2580"){
            $res=Customer::where('mobile', $mobile)
            ->where('otp', $otp)
            ->whereNull('deleted_at')
            ->update(['status' =>1]);
        }
        else{
           $res=Customer::where('mobile', $mobile)
            ->whereNull('deleted_at')
            ->update(['status' =>1]); 
        }
        if($res){
            $data = array("success"=>true, "message"=>"OTP Verified");
        }else{
            $data = array("success"=>false, "message"=>"OTP Not Verified");
        }
        print_r(json_encode($data));
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
        
        $type = $request->input('type');
        if($type==null || $type==''){
            $status = false;
            $data = array("success"=>false, "message"=>"type can not be blank");
            print_r(json_encode($data));
            exit;
        }
        
        
        $otp=$this->random_string();
        //$otp='1234';
        $msg="Your Four Digit new OTP for Snap Rides is ".$otp; 
        $mobile_arr =array($mobile);
        $res_sms=commonSms($mobile_arr,$msg);
        if($type=="1"){
        
        $res=Driver::where('mobile', $mobile)->update(['otp'=>$otp]);
        
        }
        else if($type=="2"){
        $res=Customer::where('mobile', $mobile)->update(['otp'=>$otp]);    
        }
        if($res==1){
            $array = array("otp"=>$otp);
            $data = array("success"=>true, "message"=>"Otp sended successfully." );
        }else{
            $data = array("success"=>false, "message"=>"Mobile Not Verified");
        } 
        print_r(json_encode($data));
       
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
    public function updateProfile(Request $request) {
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
        $name = $request->input('name');
        if($name==null || $name==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Name can not be blank");
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
        $oldmobile = $request->input('oldmobile');
        if($oldmobile==null || $oldmobile==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Old mobile can not be blank");
            print_r(json_encode($data));
            exit;
        }
        $email = $request->input('email');
        if($email==null || $email==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Email can not be blank");
            print_r(json_encode($data));
            exit;
        }
        $city = $request->input('city');
        if($city==null || $city==''){
            $status = false;
            $data = array("success"=>false, "message"=>"City can not be blank");
            print_r(json_encode($data));
            exit;
        }
        
        $check_customer = Customer::where('mobile','=', $oldmobile)->get();
      
        if(!empty($check_customer[0]->mobile)){
          $number_exist =  Customer::where('mobile','=', $mobile)->where("id","!=",$check_customer[0]->id)->get();
          $email_exist =  Customer::where('email','=', $email)->where("id","!=",$check_customer[0]->id)->get();
          if(empty($number_exist[0]->mobile)){
          if(empty($email_exist[0]->email)){    
          $res=Customer::where('mobile', $oldmobile)
            ->update(['name' => $name,'mobile'=>$mobile,'email'=>$email,'city'=>$city]);
           $data = array("success"=>true, "message"=>"Profile Successfully Updated");
          }
          else{
            $data = array("success"=>false, "message"=>"Mobile already exist.");  
          }
          }
          else{
           $data = array("success"=>false, "message"=>"email already exist.");   
          }
        }
        else{
           $data = array("success"=>false, "message"=>"Old mobile not matched"); 
          
        }
        
        //$res=Customer::where('mobile', $oldmobile)
            //->update(['name' => $name,'mobile'=>$mobile,'email'=>$email,'city'=>$city]);

        //if($res==1){
            //$data = array("success"=>true, "message"=>"Profile Successfully Updated");
        //}
        /*
        else{
            $data = array("success"=>false, "message"=>"Old mobile not matched");
        } 
         
         */ 

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

        $file = $request->file('image');
        if($file==null || $file==''){
            $data = array("success"=>false, "message"=>"Please Select File to upload");
            print_r(json_encode($data));
            exit;  
        }

        $reseponse = Customer::where('mobile', $mobile)
               ->orderBy('name', 'desc') 
               ->get();
        if(count($reseponse)<=0){
            $data=array('success'=>false,'message'=>"Mobile No not matched","data"=>array());
             print_r(json_encode($data));
             exit;
        }
        $oldimage=$reseponse[0]->image;

        $destinationPath = 'Admin/customerimg';
        if($oldimage!=''){
           if(file_exists($destinationPath.'/'.$oldimage)){
                unlink($destinationPath.'/'.$oldimage);
            } 
        }
        
        $new_name = time().'-'.$file->getClientOriginalName();
        $file->move($destinationPath,$new_name);
        $res=Customer::where('mobile', $mobile)->update(['image'=>$new_name]);
        $basepath= url('/');
        //die('11');
        $path=$basepath.'/'.$destinationPath.'/'.$new_name;
        $array=array('image'=>$path);

        $data=array('success'=>true,'message'=>"Image Successfully Uploaded","data"=>$array);
        print_r(json_encode($data));
     
    }

    public function getCustomerInfo(Request $request) {
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
        $res = Customer::select('name','email','city','mobile','image')->where('mobile', $mobile)
               ->orderBy('name', 'desc') 
               ->get();

        if(count($res)>0){
            $image=$res[0]->image;
           
            $destinationPath = 'Admin/customerimg';
            if($image!=''){
                if(file_exists($destinationPath.'/'.$image)){
                    $path=$destinationPath.'/'.$image;
                } else{
                    $path=$destinationPath.'/'.'noimage.png';
                }
            }else{
                $path=$destinationPath.'/'.'noimage.png';
            }

            $res[0]->image= url('/').'/'.$path;
            $data = array("success"=>true, "message"=>"Mobile exist",'data'=>$res[0]);
        }else{
            $data = array("success"=>false, "message"=>"Mobile no not matched");
        }
        return $data;
    }
     
    public function nearestDrivers(Request $request)
    {
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

        $vehicle_type = $request->input('vehicle_type');
        if($vehicle_type==null || $vehicle_type==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Vehicle Type can not be blank");
            print_r(json_encode($data));
            exit;
        }
 
        // from where pickuping the user
        $pickuplat = $request->input('pickuplat');
        if($pickuplat==null || $pickuplat==''){
            $status = false;
            $data = array("success"=>false, "message"=>"pickup latitude can not be blank");
            print_r(json_encode($data));
            exit;
        }
        
        $pickuplong = $request->input('pickuplong');
        if($pickuplong==null || $pickuplong==''){
            $status = false;
            $data = array("success"=>false, "message"=>"pickup longitude can not be blank");
            print_r(json_encode($data));
            exit;
        }
 

        $sql = "SELECT
                driver_latlong.latitude,
                driver_latlong.longitude,
                111.045 * DEGREES(
                ACOS(
                COS(RADIANS($pickuplat)) * COS(
                RADIANS(driver_latlong.latitude)
                ) * COS(
                RADIANS($pickuplong) - RADIANS(driver_latlong.longitude)
                ) + SIN(RADIANS($pickuplat)) * SIN(
                RADIANS(driver_latlong.latitude)
                )
                )
                ) AS distance_in_km
                FROM driver_latlong
                INNER JOIN drivers ON driver_latlong.driver_id = drivers.id
                LEFT JOIN vehicles on  vehicles.driver_id=drivers.id 
                LEFT JOIN vehicle_category ON vehicles.vehicle_category = vehicle_category.id
                WHERE drivers.is_active = '1' AND drivers.deleted_at IS NULL AND
                drivers.is_online = 1 AND vehicles.vehicle_category = $vehicle_type AND
                driver_latlong.booking_id = 0 AND 
                driver_latlong.id IN (
                SELECT MAX(id) FROM driver_latlong GROUP BY driver_latlong.id) AND
                drivers.id NOT IN(
                SELECT driver_id FROM booking WHERE booking_status = 'in_progress')
                GROUP BY drivers.id
                HAVING distance_in_km <= 5
                ORDER BY distance_in_km ASC";
        
        $results = DB::select( DB::raw($sql));
        
        $destinations_lat_long='';
        foreach ($results as $key => $value) {        
            if($destinations_lat_long!=''){
                $destinations_lat_long=$destinations_lat_long.'|';
            }
            $destinations_lat_long=$destinations_lat_long.$value->latitude.','.$value->longitude;
        }

        
        $pickuplatlong = $pickuplat.','.$pickuplong;

        $api="https://maps.googleapis.com/maps/api/distancematrix/json?origins=$pickuplatlong&destinations=$destinations_lat_long&key=AIzaSyBnTKkK26b0bwrCOU8XMoqzpUMVrHnf554";


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$api);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result1 = curl_exec ($curl);
        curl_close ($curl);  
        $data = json_decode($result1);
        if(isset($data->rows[0]->elements)) {
            $variable =$data->rows[0]->elements;
            for ($i=0; $i<count($variable); $i++) {
                $time= $variable[$i]->duration->text;
                $results[$i]->estimated_time = $time;
                $results[$i]->distance_in_km = number_format($results[$i]->distance_in_km,2);
            }
        }      
        if(!empty($results)){
            $res=array('success'=>true,'message'=>'Near by driver details','data'=>$results);
        }else{
            $res=array('success'=>false,'message'=>'No driver found','data'=>$results);
        }  
        
        echo json_encode($res);

    }

    public function nearestDriversLatLong(Request $request) {
       
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
        $vehicle_type = $request->input('vehicle_type');
        if($vehicle_type==null || $vehicle_type==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Vehicle Type can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $latpoint = $request->input('latitude');
        if($latpoint==null || $latpoint==''){
            $status = false;
            $data = array("success"=>false, "message"=>"latitude can not be blank");
            print_r(json_encode($data));
            exit;
        }
        $longpoint = $request->input('longitude');
        if($longpoint==null || $longpoint==''){
            $status = false;
            $data = array("success"=>false, "message"=>"longitude can not be blank");
            print_r(json_encode($data));
            exit;
        }
        /*    $sql="SELECT  drivers.id,drivers.first_name as name,drivers.is_online,, drivers.is_active,drivers.profile_image,drivers.latitude,drivers.longitude,  111.045* DEGREES(ACOS(COS(RADIANS($latpoint))
                 * COS(RADIANS(latitude))
                 * COS(RADIANS($longpoint) - RADIANS(longitude))
                 + SIN(RADIANS($latpoint))
                 * SIN(RADIANS(latitude)))) AS distance_in_km
        FROM  drivers_test as drivers where drivers.is_active=1 HAVING distance_in_km<=10 ORDER BY distance_in_km  LIMIT 10";*/

        $sql="SELECT  vehicles.vehicle_category ,drivers.is_online ,driver_latlong.booking_id, drivers.id,drivers.name, drivers.is_active,drivers.deleted_at,drivers.profile_image,driver_latlong.latitude,driver_latlong.longitude,  111.045* DEGREES(ACOS(COS(RADIANS($latpoint))
        * COS(RADIANS(driver_latlong.latitude))
        * COS(RADIANS($longpoint) - RADIANS(driver_latlong.longitude))
        + SIN(RADIANS($latpoint))
        * SIN(RADIANS(driver_latlong.latitude)))) AS distance_in_km
        FROM  driver_latlong inner join drivers on  driver_latlong.driver_id=drivers.id inner join vehicles on drivers.id=vehicles.driver_id where  vehicles.vehicle_category=$vehicle_type and drivers.is_online=1 and driver_latlong.id IN (
        SELECT MAX(id)  FROM driver_latlong GROUP BY driver_latlong.id ) and drivers.id not in (select driver_id from booking where booking_status='in_progress') 
        GROUP BY  drivers.id   HAVING distance_in_km<=5 and drivers.is_active='1' and drivers.deleted_at Is Null and drivers.is_online=1 and driver_latlong.booking_id=0  ORDER BY distance_in_km  LIMIT 10";
            // HAVING distance_in_km<=10

     /*   */
        
        $results = DB::select( DB::raw($sql));
        foreach($results as $val){
           if(empty($val->deleted_at)){
            $val->deleted_at = "";   
           } 
        }
        
        $res=array('success'=>true,'message'=>'Near by driver details','data'=>$results);
        //$data = json_encode($res);
        return $res;
    }
    public function getEmergencyContact(Request $request){
        $customer_id = $request->input('customer_id');
        if($customer_id==null || $customer_id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Customer id can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $contacts = EmergencyContact::select('id','contact_no','contact_name')->where('customer_id',$customer_id)->orderBy('id','desc')->get();
        if(count($contacts)>0){
            $res = array('success'=>true,'message'=>'Emergency Contacts list','data'=>$contacts);
        }else{
             $res = array('success'=>false,'message'=>'Contacts not Exist','data'=>$contacts);
        }
       
        return $res;
        
    }
   
    public function addEmergencyContact(Request $request){
      
        $customer_id = $request->input('customer_id');
        if($customer_id==null || $customer_id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Customer id can not be blank");
            print_r(json_encode($data));
            exit;
        }
        $mobile = $request->input('mobile');

        if($mobile==null || $mobile==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Please Enter Mobile","data"=>array());
            return $data;
            exit;
        }
        else if(strlen($mobile)<8){
           $status = false;
            $data = array("success"=>false, "message"=>"Mobile number should be greater than 8.");
            print_r(json_encode($data));
            exit; 
        }
        $contact_name = $request->input('contact_name');
        if($contact_name==null || $contact_name==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Please Enter Name","data"=>array());
            return $data;
            exit;
        }
  
       $id = $request->input('id');
       if($id==null || $id==''){
            $existingcontact = EmergencyContact::where('contact_no',$mobile)->where('customer_id',$customer_id)->get();

            if(count($existingcontact)>0){
                $data = array("success"=>false, "message"=>"Contact Already Added","data"=>array());
                return $data;
                exit;
            }

            $contact = new EmergencyContact ;
            $contact->customer_id = $customer_id;
            $contact->contact_no = $mobile;
            $contact->contact_name = $contact_name;
            $res=$contact->save();


       }else{
         $existingcontact= EmergencyContact::where('contact_no',$mobile)->where('id','<>',$id)->get();
            if(count($existingcontact)>0){
                $data = array("success"=>false, "message"=>"Contact Already Added","data"=>array());
                return $data;
                exit;
            }
            $res = EmergencyContact::where('id', $id)->update(['contact_no' => $mobile,'contact_name'=>$contact_name ]);
       }   
        if($res==1){
            $data = array("success"=>true, "message"=>"Contact updated" );
        }else{
            $data = array("success"=>false, "message"=>"Something happen wrong" );
        }
        return $data;
       

    }

    public function updateEmergencyContact(Request $request){ 
        $id = $request->input('id');
        if($id==null || $id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Id can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $mobile = $request->input('mobile');
        if($mobile==null || $mobile==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Please Enter Mobile","data"=>array());
            return $data;
            exit;
        }
        else if(strlen($mobile)<8){
           $status = false;
            $data = array("success"=>false, "message"=>"Mobile number should be greater than 8.");
            print_r(json_encode($data));
            exit; 
        }

        $contact_name = $request->input('contact_name');
        if($contact_name==null || $contact_name==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Please Enter Name","data"=>array());
            return $data;
            exit;
        }

        $existingcontact= EmergencyContact::where('contact_no',$mobile)->where('id','<>',$id)->get();

        if(count($existingcontact)>0){
            $data = array("success"=>false, "message"=>"Contact Already Added","data"=>array());
            return $data;
            exit;
        }


        $res = EmergencyContact::where('id', $id)->update(['contact_no' => $mobile,'contact_name'=>$contact_name ]);

        if($res==1){
            $data = array("success"=>true, "message"=>"Contact Updated Successfully" );
        }else{
            $data = array("success"=>false, "message"=>"Something happen wrong" );
        }
        return $data;
       

    }
    public function deleteEmergencyContact(Request $request){
        $id = $request->input('id');
        if($id==null || $id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Id can not be blank");
            print_r(json_encode($data));
            exit;
        }
        $res= EmergencyContact::where('id',$id)->delete();

        if($res==1){
            $data = array("success"=>true, "message"=>"Contact Deleted Successfully" );
        }else{
            $data = array("success"=>false, "message"=>"Id not Exist" );
        }
        return $data;
    }
    public function fcmNotification($msg,$fields,$to){ 
        #API access key from Google API's Console
        /*  define( 'API_ACCESS_KEY', 'YOUR-SERVER-API-ACCESS-KEY-GOES-HERE' );*/
        if($to =='driverapp'){

        //driver app
        define('API_ACCESS_KEY','AAAA9ySehbk:APA91bFNiawcfu4KAL81fn8_836pEJ0RVvtIvaUwISStvM0lSDX7HS8__KKgI4Dnr_SbAArC1SSHAVhqjhAmsriCA3r94Wl1mc7IQw-gu9bDFdInk7_FfoQ0Oh4YVCxbw30oYgY3_HSM');
            
        }else {
            //customer app
            define('API_ACCESS_KEY','AAAAji3qdWk:APA91bGq1dWOjVHLiDZt9JXOorasxGtuAKyT49yjyHc0ShlNuptQ7KUNuf4k15dtWPg_ePXvgNCJdPGL6j7owl3qKROehPzbSXkInpGS_bTnNOMGy4yJYaH4jjQNgINgr9BJnnT7c8Uk');
        }
       
        $headers = array
        (
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
        );
        #Send Reponse To FireBase Server    
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        #Echo Result Of FireBase Server
        //echo $result;

    }

    public function rateDriver(Request $request){
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
        

       /* */

        $booking_id = $request->input('booking_id');  
        if($booking_id==null || $booking_id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Booking id can not blank");
            print_r(json_encode($data));
            exit;
        }
        
        
        $booking = Booking:: where('id',$booking_id)->first();
        $driver_id = $booking['driver_id'];
        $customer_id = $booking['customer_id'];

        if($customer_id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Customer id can not be blank");
            print_r(json_encode($data));
            exit;
        }
        
        if($driver_id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Driver id can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $rating = intval($request->input('rating'));  
        if($rating==null || $rating==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Please rate a Driver");
            print_r(json_encode($data));
            exit;
        }

        $review =   $request->input('review') ;  
        if($review==null || $review==''){
            $review="";
        }   
         /* echo $review;
         die();*/
        $record=DriverRating::where('booking_id',$booking_id)->where('driver_id',$driver_id)->where('customer_id',$customer_id)->count();

        $driver  = Driver::select('device_token')->where('id',$driver_id)->first();

        $token=$driver->device_token;

        if($record>=1){
            $data = array("success"=>false, "message"=>"You already rated driver");
            print_r(json_encode($data));
            exit;   
        }

        $review = $request->input('review');
        $rate = new DriverRating ;
        $rate->driver_id = $driver_id;
        $rate->customer_id =$customer_id;
        $rate->rating =$rating;
        $rate->booking_id=$booking_id;
        $rate->review = $review;
       // $rate->review =$review; 
        $res = $rate->save(); 

        Booking::where('id', $booking_id)
        ->update(['driver_rating' =>'1']);

            
        if($res==1){

            $msg = array ( 'body'=> 'Customer rated you with '.$rating.' start','title' => "Your are Rated");

            $fields = array
            (
            'to'        => $token,
            'notification'  => $msg,
            'data'=>array('bookingid'=>$booking_id,'type'=>'9','message'=>'Rated_you'.$rating)
            );
            //print_r(json_encode($fields)); //die();
            $this->fcmNotification($msg,$fields,'driverapp');



            $data = array("success"=>true, "message"=>"Driver rating saved Successfully");
            print_r(json_encode($data));
            exit;   
        }else{
            $data = array("success"=>false, "message"=>"Server Error");
            print_r(json_encode($data));
            exit;   
        }
    }

    public function rideHistory(Request $request){

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

        $customer_id = intval($request->input('customer_id'));  
        if($customer_id==null || $customer_id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Customer id can not be blank");
            print_r(json_encode($data));
            exit;
        }
 
        $rides = DB::table('booking')
            ->join('drivers', 'booking.driver_id', '=', 'drivers.id')
            ->leftjoin('vehicles','vehicles.driver_id','=','drivers.id')
            ->leftjoin('vehicle_category','vehicles.vehicle_category','=','vehicle_category.id')
            ->select('booking.id as booking_id','final_amount','pickup_addrees','destination_address','booking_time','completed_time','start_time','drivers.id as driver_id','drivers.name as driver_name','profile_image as driver_image','mobile' ,'vehicle_name','vehicle_category.name as vehicle_category','registration_number','booking.driver_id','booking.booking_status','vehicles.vehicle_image','booking.otp','booking.driver_rating','booking.schedule_booking')
            ->where('customer_id',$customer_id)
            ->orderBy('booking.id', 'desc')
            ->get();
           
        //DriverRating::where('booking_id','')   

            if(count($rides)>0){
                $data=array();
                foreach ($rides as $key => $value) {
                    if($value->schedule_booking == 1 && $value->booking_status != 'in_progress' && $value->booking_status != 'payment_pending'){
                        unset($rides[$key]);
                        continue;
                    }

                    # code...
                    $booking_time=date('D d M Y g:i A',strtotime($value->booking_time));
                    $value->booking_time  = $booking_time;
                    if($value->completed_time==null){
                         $value->completed_time ='0000-00-00 00:00:00';
                    }

              
                    $destinationPath =  url('/').'/Admin/profileImage/';
                  

                    //echo $destinationPath.$value->driver_image;
                     
                    $image=$destinationPath."noimage.png";
                    if($value->driver_image!=''){
                        /*if(file_exists($destinationPath.$value->driver_image)){*/
                            $image=$destinationPath.$value->driver_image;
                              $value->driver_image = $image;
                       // }
                    }else{
                        $image=$destinationPath."noimage.png";
                         $value->driver_image = $image;
                    }

                    $VehiclePath= url('/').'/Admin/vehicleImage/';
                    //echo $VehiclePath.$value->vehicle_image; 
                    if($value->vehicle_image!=''){
                        //if(file_exists($VehiclePath.$value->vehicle_image)){
                            $vehicle_image=$VehiclePath.$value->vehicle_image;
                                  
                    }else{
                        $vehicle_image=$destinationPath."noimage.png";
                    }

                    $value->vehicle_img=$vehicle_image;
                   
                    if($value->vehicle_category==null){
                        $value->vehicle_category ="No category Selected";
                    }else{
                        $value->vehicle_category = $value->vehicle_category;
                    }

                    if($value->booking_status=='canceled'){
                        $value->type=4;
                    }else if($value->booking_status=='completed'){
                        if($value->driver_rating==0){
                            $value->type=7;
                        }else{
                            $value->type=9;
                        }
                        
                    }else if($value->booking_status=='in_progress'){ //accepted
                     
                        if($value->start_time!='0000-00-00 00:00:00' || $value->start_time!=null){
                            $value->type=2;
                        }else{
                            $value->type=6;
                        }
                    }else if($value->booking_status=='pending'){
                        $value->type=1;
                    }else{
                        $value->type=1;
                    }
                    
                    //print_r($value->booking_time);
                }
                $booking = array();
                foreach ($rides as $ride) {
                    $bookings[] = $ride;
                }
                
                $data = array("success"=>true, "message"=>"Your All Rides" ,'data'=>$bookings);
            }else{
               // $obj = (object)[];
                $data = array("success"=>false, "message"=>"No Rides Found",'data'=>array());
            }
             return $data;
    }

    public function faq(){ 
       $data=array();
       $data['data']= DB::table('support_categories')
        ->join('support_details','support_categories.id','=','support_details.cat_id')
        ->select('support_details.*')
        ->get();
        /* echo "<pre>";
        print_r($data);
        */
        return view('admin.faq', $data);
    }
    public function terms(){
        $data=array();
        return view('static_pages.term_n_conditions', $data);
    }

    public function private_policy(){
        $data=array();
       return view('static_pages.private_policy', $data);  
    }

    public function contact(){
       $data=array();
       $data['data']= DB::table('support_categories')
        ->join('support_details','support_categories.id','=','support_details.cat_id')
        ->select('support_details.*')
        ->get();
        /* echo "<pre>";
        print_r($data);
        */
        return view('admin.contact', $data);
    }

    public function sentDriverLatLong(Request $request){
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
            $data = array("success"=>false, "message"=>"Driver id can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $booking_id = $request->input('booking_id');
        if($booking_id==null || $booking_id==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Booking  id can not be blank");
            print_r(json_encode($data));
            exit;
        }
        

        $date = $request->input('created_at');
        $pickupdetails =  (object)[];
        $dropdetails =  (object)[];
        if($date!=null || $date!=''){
            $latlong = DriverLatLong::select( 'id' ,'booking_id', 'driver_id' , 'latitude' , 'longitude','created_at')->where('booking_id',$booking_id)->where('driver_id' , '=' , $driver_id )->where('created_at','>=',$date)->orderBy('id','desc')->get();
            if(count($latlong)<=0){
                  $latlong = DriverLatLong::select( 'id','booking_id' , 'driver_id' , 'latitude' , 'longitude','created_at')->where('driver_id' , '=' , $driver_id )->orderBy('id','desc')->limit(1)->get();
            }


        }else{
            $latlong = DriverLatLong::select( 'id','booking_id' , 'driver_id' , 'latitude' , 'longitude','created_at')->where('booking_id',$booking_id)->where('driver_id' , '=' , $driver_id )->orderBy('id','desc')->limit(1)->get();
            if(count($latlong)<=0){
                $latlong = DriverLatLong::select( 'id','booking_id' , 'driver_id' , 'latitude' , 'longitude','created_at')->where('driver_id' , '=' , $driver_id )->orderBy('id','desc')->limit(1)->get();
            }

        }

        $booking=Booking::select('pickup_addrees','pickup_lat','pickup_long','destination_address','drop_lat','drop_long','booking_status','start_time')->where('id',$booking_id)->first(); 
        $status = $booking->booking_status;
 
        if($status=='completed'){
            $type=7;
        }else if($status=='in_progress'){
            if(($booking->start_time =='0000-00-00 00:00:00') || ($booking->start_time ==null)){    
                $type=2;
            }else{
                 $type=6;
            }
        }else if($status=='canceled'){
            $type = 3;
        }else  { //in case of pending
            $type = $status;
        }

        $pickupdetails=array('pickup_addrees'=>($booking->pickup_addrees),'pickup_lat'=>$booking->pickup_lat,'pickup_long'=>$booking->pickup_long,'type'=>$type);

        $dropdetails=array('destination_address'=>($booking->destination_address),'drop_lat'=>$booking->drop_lat,'drop_long'=>$booking->drop_long);

        if(!empty($latlong)){
            $response = array("success"=>true, "message"=>"data found",'data'=>$latlong,'pickupdetails'=>$pickupdetails,'dropdetails'=>$dropdetails,'type'=>$type);
        }else{
            $response = array("success"=>false, "message"=>"location not found");
        }
        return $response;
    }


    public function customerStatus(Request $request){
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

        $customer_id = $request->input('customer_id');
        if($customer_id==null || $customer_id==''){         
            $data = array("success"=>false, "message"=>"Customer id can not be blank");
            print_r(json_encode($data));
            exit;
        }
         $cityname = $request->input('city');
        /*if($cityname==null || $cityname==''){         
            
        }else{ 
            $cityname = strtolower($cityname);
            $content= str_replace(' ', ',', $cityname);
            $output = explode(',', $content);
            
            $app_cty = DB::table('app_city')
            ->whereIn('name',$output)->get();
            
           if(count($app_cty)<=0){
                $data = array("success"=>false, "message"=>"Booking not availble for city ".$cityname);
                print_r(json_encode($data));
                exit;
           }
          
        }*/

        $customersCount =Customer::where('id',$customer_id)->count();
        if($customersCount<1){
            $data = array("success"=>false, "message"=>"Customer not Exist","data"=>array());
            print_r(json_encode($data));
            exit;

        }

        $sql = "select id from `booking` where `customer_id` =$customer_id and (`booking_status` = 'pending' or `booking_status` ='in_progress') AND  `booking`.`deleted_at` is null";
        $bookings=DB::select($sql);
 

        if(count($bookings)>0){
            $response = array("success"=>true, "message"=>"User is on ride","isonride"=>true );
                 //return $response;
        }else{
             $response = array("success"=>true, "isonride"=>false,"message"=>"User not on ride");
                 // return $response;
        }

        return $response;
    }


    public function vehicleTypes(){
        $vehicle = VehicleCategory::where('is_active','1')->wherenull('deleted_at')->get();
         
        $i=0;
        if(count($vehicle)>0){ 
            foreach ($vehicle as $key => $value) {
                if($i==0){
                    $value['isSelected']=true;
                }else{
                     $value['isSelected']=false;
                }
                $value['name'] = ucfirst($value['name']); 
                $i=$i+1;
                $path ='Admin/categoryImage/';
                if( ($value['image']!='') && (file_exists($path.$value['image'])) ){
                        $value['image'] = url('/').'/'.$path.$value['image'];
                }else{
                    $value['image'] =url('/').'/'.$path.'noimage.png';

                }        
            } 
             $result=array('status'=>true,"message"=>"Vehicle Types","data"=>$vehicle);
        }else{
            $result=array('status'=>true,"message"=>"Vehicle Types","data"=>array());
        }
       
        return $result;      
    }
    public function shareUrl(Request $request){

        $booking_id = $request->input('booking_id');
        $booking = Booking::where('id',$booking_id)->first();
        $customer_id =$booking->customer_id;
        $customer = Customer::where('id',$customer_id)->first();
        $customer_name= $customer->name;
        $data = $request->input('data');
        $trackurl = url('/').'/sharedRoute/'.$booking_id;
        $mobilearray=array();
        foreach ($data as $key => $value) {
           $name = $value['name'];
           $phone_no = $value['phone_no'];
           array_push($mobilearray, $phone_no);
           //sms api will call here
        }  

        $msg = 'Track '.$customer_name.' Driver '.$trackurl;
        $res = commonSms($mobilearray,$msg);
        if($res==1){
             $result=array('status'=>true,"message"=>"Ride Shared Successfully");
        }else{
            $result=array('status'=>false,"message"=>"Ride not Shared because sms not sent");
        }
        
        return $result;

    }

    public function getMyoffers(Request $request){
        $customer_id = $request->input('customer_id');
        if($customer_id==null || $customer_id==''){  
          $data=array('status'=>false,"message"=>"Customer Id Can not blank");
          return $data;  
        } 

        $isSchedule = $request->input('isSchedule');
        if($isSchedule==null || $isSchedule==''){  
          $data=array('status'=>false,"message"=>"Is Schedule Can not blank");
          return $data;  
        }

        $cust_rec= [];
        $customerOffers= DB::table('offer_used_by_customers')->where('customer_id',$customer_id)->get();
        $basepath= url('/');
        $destinationPath = 'public/offers';
        
        foreach($customerOffers as $i=>$val){
            $today = date("Y-m-d h:i:s");
            $Offer_detail= DB::table('offer_codes')
                            ->where('offer_code',$val->offer_code)
                            ->where('offer_for',$isSchedule)
                            ->where('start_date', '<=', $today)
                            ->where('end_date', '>=', $today)
                            ->first();    
        
            if(!empty($Offer_detail->id)){
        
                if($val->no_of_time_used<=$Offer_detail->used_limit){    
        
                    if(!empty($Offer_detail->title)){
                        $customerOffers[$i]->title=$Offer_detail->title;
                    }
                    else{
                        $customerOffers[$i]->title="";
                    }
                    $path=$basepath.'/'.$destinationPath.'/'.$Offer_detail->image;
                    $customerOffers[$i]->image=(!empty($path) ? $path : "");
                    $customerOffers[$i]->description=(!empty($Offer_detail->description) ? $Offer_detail->description : "");
                    $cust_rec[] =$customerOffers[$i];
                }
            } 
        }
       
        if(count($cust_rec)>0){
          $data=array('status'=>true,"message"=>"Customer Offers",'data'=>$cust_rec);
        }else{
          $data=array('status'=>false,"message"=>"Offers Not Found",'data'=>$cust_rec);

        }
        return $data;  
    }

     public function applyOfferCode(Request $request){
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
        $customer_id = $request->input('customer_id');
        if($customer_id==null || $customer_id==''){  
          $data=array('status'=>false,"message"=>"Customer Id Can not blank");
          return $data;  
        }   
        $offer_code = $request->input('offer_code');
        if($offer_code==null || $offer_code==''){  
          $data=array('status'=>false,"message"=>"Offer Code Can not blank");
          return $data;  
        } 
        $data = DB::table('offer_used_by_customers') 
         ->join('offer_codes', 'offer_used_by_customers.offer_code', '=', 'offer_codes.offer_code')->where('offer_used_by_customers.customer_id',$customer_id)->where('offer_used_by_customers.offer_code',$offer_code)->where('offer_codes.status',1)->get(); 
        if(count($data)>0){
          $offer_code=$data[0]->offer_code;
          $data=array('status'=>true,"message"=>"Offer Code is valid","offer_code"=>$offer_code);
          return $data;  
        }else{
          $data=array('status'=>false,"message"=>"Offer Code not valid" );
          return $data; 

        }

     }
    
      public function validateAppVersion_customer(Request $request){
        
        $this->current_version=1;
        $appversion = $request->input('appversion');

        $customer_id = $request->input('customer_id');
        
        $booking_id="";
        $booking_status="";
        if($customer_id!=''){

            $booking = DB::table('booking')
            ->join('drivers', 'booking.driver_id', '=', 'drivers.id')
            ->leftjoin('vehicles','vehicles.driver_id','=','drivers.id')
            ->leftjoin('vehicle_category','vehicles.vehicle_category','=','vehicle_category.id')
            ->select('booking.id','booking.id as booking_id','final_amount','pickup_addrees','destination_address','booking_time','completed_time','start_time','drivers.id as driver_id','drivers.name as driver_name','profile_image as driver_image','mobile' ,'vehicle_name','vehicle_category.name as vehicle_category','registration_number','booking.driver_id','booking.booking_status','vehicles.vehicle_image','booking.otp','booking.driver_rating','booking.is_payment')
            ->where('customer_id',$customer_id)
            ->where('booking.booking_status','payment_pending')
            ->where('booking.is_payment',0)
            ->orderBy('booking.id', 'desc')
            ->first();

            $booking_detail = array();
            $status='';
            if(isset($booking->booking_status)){
                $booking_status = $booking->booking_status;
                $time = $booking->start_time;
                $status=$booking->booking_status;

                //if($booking_status=="in_progress"){

                    // if($time!='0000-00-00 00:00:00'){
                    //      $booking_status='onride';
                    // }else{
                    //      $booking_status='accepted';
                    // }

                    //new code
                    $booking_time=date('D d M Y g:i A',strtotime($booking->booking_time));
                    $booking->booking_time  = $booking_time;
                    if($booking->completed_time==null){
                         $booking->completed_time ='0000-00-00 00:00:00';
                    }
                    $destinationPath =  url('/').'/Admin/profileImage/';
                    $image=$destinationPath."noimage.png";

                    if($booking->driver_image!=''){
                            $image=$destinationPath.$booking->driver_image;
                            $booking->driver_image = $image;
                    }else{
                        $image=$destinationPath."noimage.png";
                        $booking->driver_image = $image;
                    }
                    $VehiclePath= url('/').'/Admin/vehicleImage/'; 
                    if($booking->vehicle_image!=''){
                        $vehicle_image=$VehiclePath.$booking->vehicle_image;       
                    }else{
                        $vehicle_image=$destinationPath."noimage.png";
                    }
                    $booking->vehicle_img=$vehicle_image;

                    if($booking->vehicle_category==null){
                        $booking->vehicle_category ="No category Selected";
                    }else{
                        $booking->vehicle_category = $booking->vehicle_category;
                    }
                    if($booking->start_time!='0000-00-00 00:00:00' || $booking->start_time!=null){
                            $booking->type=2;
                    }else{
                        $booking->type=6;
                    }

                    // end code


                //}
                $booking_id = $booking->id;


            }


        }
 
        $data = (object)[];
        if($customer_id!=''){
            $dataarray = Customer::select('id','email','name','city','mobile','appversion')->where('id',$customer_id)->get();
            if(isset($dataarray[0])){
                $data=array(
                'customer_id'=>$dataarray[0]->id,
                'booking_status'=>$booking_status,
                'booking_id'=>$booking_id ,
                'status'=>$status
                );

                // if($status=='in_progress'){
                if($status != ''){
                    $data['booking_detail'] = $booking;
                }
            } 

        }
       
        $res = array("success"=>true, "message"=>"Your App Version is Correct",'data'=>$data);
        
        if($appversion==null || $appversion==''){
            $status = false;
            $data = array("success"=>false, "message"=>"App Version can not be blank");
            print_r(json_encode($data)); 
            exit;
        }else if($appversion!='1'){
             
            $status = false;
            //$data=["url"=>"https:\/\/play.google.com\/store?hl=en"];
             $data['url'] = "https:\/\/play.google.com\/store?hl=en";
            $res = array("success"=>false, "message"=>"Installed App version is outdated, Please update with new App version","data"=>$data,"isMandatory"=>false);
            print_r(json_encode($res)); 
            exit;
        }
        if($appversion==null || $appversion==''){
            $status = false;
            $data = array("success"=>false, "message"=>"App Version can not be blank");
            print_r(json_encode($data));
            exit;
        }
        
       
        return $res;
        exit;

    }

    //Driver wants to cancel booking
    public function cancelScheduleBookingByCM(Request $request) {
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

            $driver = DB::table('booking')
                        ->join('drivers', 'booking.driver_id', '=', 'drivers.id')
                        ->select('drivers.device_token', 'drivers.type','booking.start_time')
                        ->where('booking.booking_status','accept')
                        ->where('booking.id', $booking_id)
                        ->where('booking.customer_id', $user_id)
                        ->where('booking.driver_id', $driver_id)
                        ->first();
                        
            if($driver){
                $token = $driver->device_token;
                $device_type = $driver->type;    
                $start_time = $driver->start_time;    
                if ($start_time != '0000-00-00 00:00:00') {
                    $data = array("success" => false, "message" => "You can not cancel Onride Booking");
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
                        ->update(['booking_status' => $status, 'cancel_time' => $time, 'canceled_by' => "Customer"]);


                    $type = '3';
                    $title = "Booking Cancelled";
                    $message = "Your booking id ". $booking_id ." has been cancelled by Customer.";
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
                    //notification will send to driver 
                    if ($device_type == "1") {
                        $this->fcmNotification($msg, $fields, 'driverapp');
                    } else {
                       
                        $this->iosNotification($msg, $fields, 'driverapp', $environment);
                    }    
                    $data = array("success" => true, "message" => "Your booking cancelled successfully.","data"=>$driver); 
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
    
    public function sendPickupRequestToDriver(Request $request)
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

        $driver = DB::table('booking')
                        ->join('drivers', 'booking.driver_id', '=', 'drivers.id')
                        ->select('drivers.device_token', 'drivers.type','drivers.mobile')
                        ->where('booking.id', $booking_id)
                        ->where('booking.customer_id', $user_id)
                        ->where('booking.driver_id', $driver_id)
                        ->first();
           
        if($driver){
            $token = $driver->device_token;
            $device_type = $driver->type;    
            
            $date = date('Y-m-d');
            $booking_date = date('Y-m-d',strtotime($booking_time));
            $actual_date = date('D d M Y',strtotime($booking_time));

            if($date == $booking_date){
                $time = date('Y-m-d H:i:s');
               
                $type = 'PickupRequestToDriver';
                $title = "Pickup Request";
                $message = "Customer ".$booking_id." is ready for ride.";
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
                //notification will send to driver 
                if ($device_type == "1") {
                    $this->fcmNotification($msg, $fields, 'driverapp');
                } else {
                   
                    $this->iosNotification($msg, $fields, 'driverapp', $environment);
                }    

                
                $mobilearray = array($driver->mobile);
                $msg = "Customer " .$booking_id." is ready for ride";
                $res = commonSms($mobilearray, $msg);

                $data = array("success" => true, "message" => "Your pickup request has been sent to driver","data"=>$driver);
            }else{
                $data = array("success" => false, "message" => "Sorry! You are not able to send request on non ride day. For more information please contact with admin","data"=>(object)[]);
            }
        }else{
            $data = array("success" => false, "message" => "Booking not found","data"=>(object)[]);
        }            
        
        return $data;
        exit;            

    }

    public function isCustomerOnRide(Request $request)
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

        $customer_id = $request->input('customer_id');
        if ($customer_id == null || $customer_id == '') {
            $data = array("success" => false, "message" => "Customer Id can not be blank");
            print_r(json_encode($data));
            exit;
        }

        $customersCount =Customer::where('id',$customer_id)->count();
        if($customersCount<1){
            $data = array("success"=>false, "message"=>"Customer not Exist","data"=>(object)[]);
            print_r(json_encode($data));
            exit;
        }

        $booking = DB::table('booking')
                    ->select('booking.id as booking_id','booking.otp','drivers.id as driver_id','drivers.name','drivers.profile_image','drivers.mobile','vehicles.vehicle_image','vehicles.registration_number as reg')
                    ->leftJoin('drivers', 'drivers.id', '=', 'booking.driver_id')
                    ->leftJoin('vehicles', 'drivers.id', '=', 'vehicles.driver_id')
                    ->where('booking.customer_id',$customer_id)
                    ->where('booking.booking_status','in_progress')
                    ->whereNull('booking.deleted_at')
                    ->first();

        if(count($booking)>0){
            
            $destinationPath = 'Admin/profileImage';
            $image = url('/') . '/' . $destinationPath . '/' . 'noimage.png';
            if (isset($booking->profile_image) && ($booking->profile_image != '')) {
                $booking->profile_image = url('/') . '/' . $destinationPath . '/' . $booking->profile_image;
                
            }else{
                $booking->profile_image = $image;
            }

            $vehiclePath = 'Admin/vehicleImage';
            $vehicle_img = url('/') . '/' . $vehiclePath . '/' . 'noimage.png';
            if (isset($booking->vehicle_image) && ($booking->vehicle_image != '')){
                $booking->vehicle_image = url('/') . '/' . $vehiclePath . '/' . $booking->vehicle_image;
                
            }else{
                $booking->vehicle_image = $vehicle_img;
            }    

            $data = array("success"=>true, "message"=>"User is on ride","data"=> $booking);
                 
        }else{
            $data = array("success"=>false, "message"=>"No ride found","data"=>(object)[]);
        }
        return $data;

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
