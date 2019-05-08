<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\OfferCode;
use App\Driver;
use DB;
use App\Setting;

class OfferCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {  
         
      $type='';
      if(isset($request->type) && ($request->type!='')){
        $type = $request->type;
        if($type=='promo'){
          $type="promocode";
        }else if($type=='invite'){
          $type="invitecode";
        }
      }

      $offer_type='';  
      if(isset($request->offer_type) && ($request->offer_type!='')){
        $offer_type = $request->offer_type;
      }

      $code='';  
      $q = '';
      if(isset($request->code) && ($request->code!='')){
        $code = $request->code;
        $q = $code;
      }
    
      $res=DB::table('offer_codes');
    
      if($code!=''){
        $res->where('offer_code', 'like', '%'.$code.'%');
      }
          $res->orderBy('id','desc');
          $data=$res->paginate('10');  
       
       
      return view('admin.offercode.index',compact('data','q' ))->with('i', ($data->currentpage()-1)*$data->perpage()+1);

    }
    
    public function searchOffer(request $request)
    {
        die("ab");
    }

    public function addOffer()
    {
      $data=array();
      return view('admin.offercode.add',compact('data' ));
    }
    
    public function editOffers($id)
    {
      $offer_id = decrypt($id);
      $get_det = DB::table('offer_codes')->where('id' , '=' , $offer_id)->first();
      // return view('admin.offercode.add',compact('get_det' ));
      return view('admin.offercode.edit',compact('get_det' ));
        
    }
    
    public function setStatus(Request  $request)
    {
        $status = DB::table('offer_codes')->where('id' , '=' , $request->id )->update(array("status"=>$request->status));
        if($request->status=="1"){
            echo "Active";
        }
        else{
          echo "Inactive";  
        }
    }

    public function saveOffer(Request  $request)
    {  

      if(isset($request->offer_code)){
        $offer_code= $request->offer_code;
      }else{
        $offer_code= "";
      }

      if(isset($request->offer_type)){
        $offer_type= $request->offer_type;

      }else{
        $offer_type="";
      }

      if(isset($request->start_date)){
        $start_date=  date('Y-m-d',strtotime($request->start_date));
      }else{
        $start_date= "";
      }
       if(isset($request->end_date)){
        $end_date=  date('Y-m-d',strtotime($request->end_date));
      }else{
        $end_date= "";
      }

      if(isset($request->offer_cost)){
        $offer_cost = $request->offer_cost;
      }else{
        $offer_cost = "";
      }

      // if(isset($request->extend_day)){
      //   $extend_day = $request->extend_day;
      // }else{
      //   $extend_day ="";
      // }

      if(isset($request->description)){
        $description = $request->description;
      }else{
        $description = "";
      }
      
      if(isset($request->no_limit)){
        $no_limit = $request->no_limit;
      }else{
        $no_limit = "";
      }
      
      if(isset($request->title)){
        $title = $request->title;
      }else{
        $title = "";
      }

      if(empty($request->action)){
        $check_offer = DB::table('offer_codes')
        ->where('offer_code' , '=' ,$offer_code)
        ->where('status' , '=' ,1)
        ->whereNull('deleted_at')
        ->get();
      }
      else{
        $check_offer = DB::table('offer_codes')
        ->where('offer_code' , '=' ,$offer_code)
        ->where('status' , '=' ,1)
        ->where('id' , '!=' ,$request->offer_id)
        ->whereNull('deleted_at')
        ->get(); 
          
      }

      
      if(empty($check_offer[0]->offer_code)){
        
        $image = $request->file('image');
        if(!empty($image)){   
          $image_name         =    time().".".$image->getClientOriginalExtension();
          $destinationPath = public_path('/offers');
          $image->move($destinationPath, $image_name );
        }
        else{
          $image_name ="";
        }

        $obj = new OfferCode;
        $obj->offer_code = $offer_code;

        // $obj->offer_type = 'promocode';
        $obj->offerType = $offer_type;      // FIXED/PERCENT
        if($offer_type == "FIXED"){
          $obj->amount = $offer_cost; 
        }else if($offer_type == "PERCENT"){
          
          $obj->percentType = $request->percent_type;  // FLAT/UPTO
          
          if($request->percent_type == 'FLAT'){
            $obj->percent = $request->offer_percent;    
          }else if($request->percent_type == 'UPTO'){
            $obj->percent = $request->offer_percent;
            $obj->amount = $offer_cost;   
          }

        }

        $obj->start_date = $start_date;
        $obj->end_date = $end_date;
        $obj->amount = $offer_cost;
        // $obj->plan_extends_for_days = $extend_day;
        $obj->description = $description;
        $obj->image =  $image_name;
        $obj->used_limit = $no_limit;
        $obj->title = $title;
        if(!empty($request->action=="update")){
          $offer_id = $request->offer_id;
     
          $offer_data = array(
            "offer_code"=>$offer_code,
            /*"offer_type" => "promocode",*/
            "offerType"=>$offer_type,
            "start_date" => $start_date,
            'end_date' => $end_date,
            // 'amount' => $offer_cost,
            // 'plan_extends_for_days' =>$extend_day,
            "description" => $description,
            "image" => $image_name,
            "used_limit" => $no_limit
          );
          
          if($offer_type == "FIXED"){
            $offer_data['amount'] = $offer_cost; 
          }else if($offer_type == "PERCENT"){
      
            $offer_data['percentType'] = $request->percent_type;  // FLAT/UPTO
      
            if($request->percent_type == 'FLAT'){
              $offer_data['percent'] = $request->offer_percent;    
            }else if($request->percent_type == 'UPTO'){
              $offer_data['percent'] = $request->offer_percent;
              $offer_data['amount'] = $offer_cost;   
            }
          }

          if(empty($image_name)){
            unset($offer_data['image']);
          }
          
          $status = DB::table('offer_codes')->where('id' , '=' , $offer_id)->update($offer_data);
        
          return redirect('offers/promo')->with('msg','Offer Successfully Updated')->with('color' , 'success');
        }
        else
        {
          $res= $obj->save();
        }
      
        return redirect('offers/promo')->with('msg','Offer Successfully Added')->with('color' , 'success');
      }
      else{
        return Redirect('offers/promo')->with('msg',"Offer Code already exist.")->with('color' , 'warning');    
      }

    }

    public function addInviteCode()
    {
      return view('admin.offercode.addinvite',compact('data' ));
    }
    
    public function driverInvitation(Request $request)
    {
                     
      $driver_name = $request->driver_name;
      $driver = DB::table('drivers');
      $driver->select('drivers.id' , 'drivers.mobile' , 'drivers.email' , 'drivers.is_blocked' , 'drivers.is_active' , 'drivers.is_approved','drivers.invite_code'  , 'drivers.name as driver_name');
      $driver->where('drivers.deleted_at' , '=' , '' );
      $referal_offer_days  = Setting::where('key', '=' , "referal_code_days")->first();
      if($driver_name!=''){
      $driver->where('name', 'like', '%'.$driver_name.'%');
      }
      //$drivers = $driver->paginate('10');
      $drivers = $driver->get();
      
      foreach($drivers as $i=>$v){
      $code =$v->invite_code;
      
      if(!empty($code)){
        $drivers_count =   DB::table('drivers')->where("referral_code",'=',$code)->count();    
       
        if(empty($drivers_count)){
            $drivers_count=0;
         }
      }
      else{
       $drivers_count =0;   
       }
      if($drivers_count!=0){
      $drivers[$i]->driver_count =$drivers_count;   
      }
      else{
      unset($drivers[$i]);
      }
      }
     
      //return view('admin.driverinvitation.index',compact('drivers','referal_offer_days'))->with('i', ($drivers->currentpage()-1)*$drivers->perpage()+1);
      return view('admin.driverinvitation.index',compact('drivers','referal_offer_days'));
              
    }
    
    public function driver_invitationDetail($driver_id)
    {
      $driver_id         =  decrypt($driver_id);
      $get_code          =  DB::table('drivers')->where("id","=",$driver_id)->get();
      $drivers  = DB::table('drivers')->where("referral_code","=",$get_code[0]->invite_code)->paginate('10');
      return view('admin.driverinvitation.invitation',compact('drivers','get_code'))->with('i', ($drivers->currentpage()-1)*$drivers->perpage()+1);
    }
   
    public function sendPromocode(request $request,$promocode_id)
    {
      $promocode_id = decrypt($promocode_id);
      $res=DB::table('offer_codes')->where("id","=",$promocode_id)->get();
      $q = '';
      if(empty($request->city)){
        $customer =  DB::table('customers')->where('status','=','1')->orderBy('name')->paginate('100');
      }
      else{
      $customer =  DB::table('customers')->where('status','=','1')->where('name','like','%'.$request->city.'%')->orwhere('mobile','like','%'.$request->city.'%')->orderBy('name')->paginate('100'); 
       $q = $request->city;  
      }
      return view('admin.offercode.send',compact('customer','res','q'))->with('i', ($customer->currentpage()-1)*$customer->perpage()+1);
    }
   
    public function saveInvitecode(request $request)
    {
      $update_driver_offer = DB::table('setting')->where('key',"=", "referal_code_days")->update(array("value"=>$request->invitation_setting));
           return redirect('driverInvitation')->with('msg','Settings successfully updated')->with('color' , 'success');
    }
      
    public function changeInvitecodeSetting(request $request)
    {
      $status = DB::table('setting')->where('key',"=", "referal_code_days")->update(array("is_active"=>$request->status));
      if($request->status=="1"){
        echo "Active";
      }
      else{
        echo "Inactive";  
      }
    }
   
    
    public function send_Promocode(Request $request)
    {
      $customer = $request->customer;
      $offer_id = $request->offer_id;
      $res=DB::table('offer_codes')->where("id","=",$offer_id)->get();
      foreach($customer as $val){
        
        $offerExist = DB::table('offer_used_by_customers')
            ->where('customer_id',$val)
            ->where('offer_code',$res[0]->offer_code)
            ->whereNull('deleted_at')
            ->first();
        if(empty($offerExist)){
          $userofferid =   DB::table('offer_used_by_customers')->insertGetId(['customer_id' => $val, 'offer_code' => $res[0]->offer_code]);
          $customer_detail=DB::table('customers')->where("id","=",$val)->get();
        
          $msg = "Congradulation you have got offer from snap rides app use promocode is ".$res[0]->offer_code;
          
          $fields = array(
            'to'  => $customer_detail[0]->device_token,
            // 'data'  => $msg,
            'data'=>array('bookingid'=>"1",'type'=>'7','message'=>$msg)
          );
          
          if($customer_detail[0]->type=="1"){
            $this->fcmNotification($msg,$fields,"customerapp");
          }
          else{
            $this->iosNotification($msg,$fields,"customerapp");  
          }
        }
      }
      echo "1";
    }
   
    public function fcmNotification($msg,$fields,$to)
    { 
      $access ="AAAAji3qdWk:APA91bGq1dWOjVHLiDZt9JXOorasxGtuAKyT49yjyHc0ShlNuptQ7KUNuf4k15dtWPg_ePXvgNCJdPGL6j7owl3qKROehPzbSXkInpGS_bTnNOMGy4yJYaH4jjQNgINgr9BJnnT7c8Uk";
      $headers = array
      (
      'Authorization: key=' . $access,
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
    }
    
    public function iosNotification($msg,$fields,$app)
    {
      $deviceToken = $fields['to'];
      $passphrase = '';
      $message=$msg;
      if($app=="customerapp"){
      $path = 'Admin/CertificatesDevelopment.pem';
      }
      elseif ($app=="driverapp") {
         $path = 'Admin/CertificatesDriverDevelopment.pem';
      }
            
           
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert',$path);
            //stream_context_set_option($ctx, 'ssl', '', $passphrase);
            stream_context_set_option($ctx, 'ssl', 'passphrase', '');
            // Open a connection to the APNS server
            $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err,$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
           
            // $url = "ssl://gateway.push.apple.com:2195";
            // $fp = stream_socket_client($url, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
             if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

            //echo 'Connected to APNS' . PHP_EOL;
            // Create the payload body
            $body['aps'] = array(
            'alert' => $message,
            'badge' => 1, 'sound' => 'default',"fields"=>$fields
            );
            //apns-expiration to set exipiration
            // Encode the payload as JSON
            $payload = json_encode($body);
            // Build the binary notification
        
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;


             // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));
            fclose($fp);           
    }

  }
