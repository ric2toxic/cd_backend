<?php
    class ResponseError {
		
		public $statusCode;
		public $message;
		public $error_reference;
	
		public function __construct(){
		
			/* some code here*/
		}

	    public function authenticationFail()
		{
			$this->statusCode = 900;
			$this->message    = 'Authentication Failed. Invalid Access Token.';
			echo json_encode($this);
			exit();
			//throw new Exception($this->message, $this->statusCode);
	    }

	    public function sellerInactive()
		{
		   $this->statusCode = 901;
		   $this->message    = 'Dear Seller, your profile is Pending Approval. Kindly complete filling your details (if any pending), for quick approval!';
		    echo json_encode($this);
			exit();
		   //throw new Exception($this->message, $this->statusCode);
	    }

	    public function validationFail()
		{
			$this->statusCode = 999;
			$this->error_reference = "gst_number";
			echo json_encode($this);
			exit();
			//throw new Exception($this->message, $this->error_reference, $this->statusCode);
	    }
    }

?>