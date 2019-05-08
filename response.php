<?php
//load RSA library
include 'Crypt/RSA.php';
//initialize RSA
$rsa = new Crypt_RSA();
//decode & get POST parameters
$payment = base64_decode($_POST ["payment"]);
//print_r($payment); die('11');


$signature = base64_decode($_POST ["signature"]);
$custom_fields = base64_decode($_POST ["custom_fields"]);
//load public key for signature matching
/*$publickey = "-----BEGIN PUBLIC KEY----- MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDYW8XwynXYe5+QgQSNdxHd/UxT xfP/VT9IumS2VuTz/PRw7Ykb09Nn6DDP4Hzwc5DMH6qliiLzrZT72R+5ZQA3baUS /qLd8Y+09jfSiiIEzCteS8vJaByV3wvfnfgspvXMaIwqzhH+DZzxFNU8Dzh9JrVe 385nOuWwg6dofWMzDwIDAQAB -----END PUBLIC KEY-----";*/
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
//get payment response in segments
//payment format: order_id|order_refference_number|date_time_transaction|payment_gateway_used|status_code|comment;
//print_r($payment);
$responseVariables = explode('|', $payment);       
print_r($responseVariables);

	//display values
	echo "<pre>";
 echo $signature_status;
	$custom_fields_varible = explode('|', $custom_fields);
	//var_dump($custom_fields_varible);
	//echo '<br/>';
	//var_dump($responseVariables);

	
?>  