<?php

     function fcmNTFC($msg,$fields,$to){ 
        #API access key from Google API's Console
        /*  define( 'API_ACCESS_KEY', 'YOUR-SERVER-API-ACCESS-KEY-GOES-HERE' );*/
        if($to=='driverapp'){
            // define('API_ACCESS_KEY','AAAAuIvKrEM:APA91bEix38_XugJjJfB3HregtLFzg0xhMJ_0-PMvIzTca7SrdNFs2eBcKYAdq0lY0oiDKmW0ccDoMCsp-nNhkVT90kwJMpAxs2kCmjlcZebEK4VhQK9Qi1-aqIJcxpAc9dcIEebamsEZG5rUHdW1LIk-MDcBF5Xqg');

          define('API_ACCESS_KEY','AAAA9ySehbk:APA91bFNiawcfu4KAL81fn8_836pEJ0RVvtIvaUwISStvM0lSDX7HS8__KKgI4Dnr_SbAArC1SSHAVhqjhAmsriCA3r94Wl1mc7IQw-gu9bDFdInk7_FfoQ0Oh4YVCxbw30oYgY3_HSM');


            
         }else {
            //customer app
            // define('API_ACCESS_KEY','AAAAk5ejpl4:APA91bHrLr9l6rKfceoEK_lECAh3rkyQhFN9MthlN2u7EissI5UIt3UuWD3wf6bXyJBmt6OUfE32vTr67NT_nZmVXsKPDiEinXessPsLdM6W3mYj6aMi6yCj7aRjBbXuQLGfHkEdQX6iVKGUA5Xb5cZtRLOrBQhIlA');
            
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
    if (! function_exists('commonSms')) {
   

        function commonSms($mobilearray,$message){
          
          $SMSUSERID = env('SMS_USERID');
          $SMSPASSWORD = env('SMS_PASSWORD');
          $country_code = Config::get('constants.COUNTRY_CODE');
          $mobile_str = ''; 
          foreach($mobilearray as $val){
            // $mobile_str .= $country_code.$val;
            $mobile_str .= $val;
          }

          $fields = 'UserId='.$SMSUSERID.'&Password='.$SMSPASSWORD.'&PhoneNumber='.$mobile_str.'&MessageText='.$message;

          $ch = curl_init();

          curl_setopt($ch, CURLOPT_URL, 'https://iweb.itouch.co.za/Submit?');
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
          curl_setopt($ch, CURLOPT_POST, 1);

          $headers = array();
          $headers[] = 'Content-Type: application/x-www-form-urlencoded';
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

          $result = curl_exec($ch);
          if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
          }
          curl_close ($ch);
          
          $output = array();
          parse_str($result, $output);

          if(isset($output['Success'])){
            return '1';
          }else{
            return '0';
          } 
        }
 
    }
     
 
    ?>