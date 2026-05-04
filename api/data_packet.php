<?php
    class DataPacket {
		
		public $statusCode;
		public $message;
		public $data;
		public $totalRecord;	
		public $userSession;
	
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

	    public function maintenanceMode()
		{
		   $this->statusCode = 902;
		   $this->message    = MAINTENANCE_URL;
		   echo json_encode($this);
		   exit();
		   //throw new Exception($this->message, $this->statusCode);
	    }

    }

?>

