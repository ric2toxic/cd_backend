<?php

	include('vendor/phpseclib/phpseclib/phpseclib/Crypt/RSA.php');

	// Signature from SDK Response
	$signature = "MKhkIdGSxHLDcLtVVoVRsAt9ovRtSdhpJ3tias5sjrnyrhe2vchNNsJY7/wCYARjgTgQ1WsXMWfVxwwn26xINHiWztEg88bdBrJXT+wQgPWe4r+TZV+zfkY4BX/w/rfrnjEjqTt9N8qulIkbhxRvAuK7L5hXwNwfNViK8th0dMc=";

	// Payload from SDK response
	$package = "eyJyZXF1ZXN0Tm9uY2UiOiI1NDMxNmE3NS0zNjY5LTRhZDMtYmNlYS00MmVlMzM5YmQwNmQiLCJyZXF1ZXN0VGltZSI6MTUyNzMyMjkzNywicGhvbmVOdW1iZXIiOiIrOTE3OTc2ODc4MTYzIiwiZmlyc3ROYW1lIjoiRHVkZSIsImxhc3ROYW1lIjoiLi4uIiwiZ2VuZGVyIjoiTSIsImNvdW50cnlDb2RlIjoiaW4iLCJlbWFpbCI6ImVyLnIua3VtYXdhdEBnbWFpbC5jb20iLCJhdmF0YXJVcmwiOiJodHRwczovL3MzLWV1LXdlc3QtMS5hbWF6b25hd3MuY29tL2ltYWdlczEudHJ1ZWNhbGxlci5jb20vbXl2aWV3LzEvYWM1YjlhYzQzZGU1Njc5Yjg0N2I1MTQ0YzA0MzM0NjgvMyIsImlzVHJ1ZU5hbWUiOmZhbHNlLCJpc0FtYmFzc2Fkb3IiOmZhbHNlfQ==";
	// Public Key Fetched from 'https://api4.truecaller.com/v1/key'
	$key = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDEpFwIarbm48m6ueG+jhpt2vCGaqXZlwR/HPuL4zH1DQ/eWFbgQtVnrta8QhQz3ywLnbX6s7aecxUzzNJsTtS8VxKAYll4E1lJUqrNdWt8CU+TaUQuFm8vzLoPiYKEXl4bX5rzMQUMqA228gWuYmRFQnpduQTgnYIMO8XVUQXl5wIDAQAB";

	$rsa = new Crypt_RSA(); 
	$rsa->setHash("sha512"); 
	$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1); 
	$rsa->loadKey( $key ); 
	// $verify = $rsa->verify( base64_decode($package), base64_decode($signature) ); 

	if( $rsa->verify( $package, base64_decode($signature) ) ){
		echo "Verified";
	}else{
		echo "Not Verified";
	}

?>
