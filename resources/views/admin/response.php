<?php
//load RSA library
include 'Crypt/RSA.php';
//initialize RSA
$rsa = new Crypt_RSA();
$publickey = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDa9HmPuVVAAkBQsjobrEW3R188
YwN3xef1gGF+k379+MhDeJSTU1cT+JQz/2UgMEKes+Y9zYbPRwr6tXOcHeQ39cDM
PCn51hGLrkd0bMumE2kJKSiU+4CfqcTpmySzfyl950HWywPMn9+5nNLvCy53uNY2
OnBa/lOmQUSThC1tzwIDAQAB
-----END PUBLIC KEY-----";
$skey="a662e418-0dec-4cd2-9f70-77456dd89024";
$rsa->loadKey($publickey); 
//verify signature
$signature_status = $rsa->verify($payment, $signature) ? TRUE : FALSE;

	$responseVariables = explode('|', $payment);       
	$custom_fields_varible = explode('|', $custom_fields);
	if($responseVariables[3]==0){
		$data = array("success"=>true, "message"=>"Pyament Successfully done",'payment_success'=>true);
	}else{
		 $data = array("success"=>false, "message"=>"Pyament Unsuccessed",'payment_success'=>false);
	}
	//print_r($custom_fields_varible);
	//ApiDriverController::buySubscriptionPlan($custom_fields_varible);
	print_r(json_encode($data)) ;
 
	
?>  