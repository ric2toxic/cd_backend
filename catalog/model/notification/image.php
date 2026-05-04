<?php
/**
 * User: Garvit
 * Date: 28/9/2016
 * Time: 16:23 PM
 */
class ModelNotificationImage extends Model {
	/**
     * Make Image Collage According to Category
     * @param $data
     * @return array
     */
    private $realWidth;
    private $realHeight;
    private $gridWidth;
    private $gridHeight;
    private $image;

    private $registrationIds;
    private $msg;

    public function __construct($registry)
    {
        parent::__construct($registry);
    }

    public function image($product, $category_id, $image_text){

    	header("Content-type: image/png");

    	$realWidth 			= 1200;
        $realHeight 		= 650;
        $gridWidth 			= 2;
        $gridHeight		 	= 10;

    	$this->realWidth 	= $realWidth;
        $this->realHeight 	= $realHeight;
        $this->gridWidth 	= $gridWidth;
        $this->gridHeight 	= $gridHeight;

        // create destination image
        $this->image = imagecreatetruecolor($realWidth, $realHeight);

        // set image default background
        $white 	= imagecolorallocate($this->image, 255, 255, 255);
        imagefill($this->image, 0, 0, $white);
        //$grey 	= imagecolorallocate($this->image, 128, 128, 128);
        //$black 	= imagecolorallocate($this->image, 0, 0, 0);
	    
        //NOTE :- It is used when we want two or more pic.
        
	     //    imagesetthickness($this->image, 1);
		    // $cellWidth 	= ($this->realWidth - 1) / $this->gridWidth;   // note: -1 to avoid writting
		    // $cellHeight = ($this->realHeight - 1) / $this->gridHeight; // a pixel outside the image

		    // for ($x = 0; ($x <= $this->gridWidth); $x++)
		    // {
		    //     for ($y = 0; ($y <= $this->gridHeight); $y++)
		    //     {
		    //         imageline($this->image, ($x * $cellWidth), 0, ($x * $cellWidth), $this->realHeight, $black);
		    //         imageline($this->image, 0, ($y * $cellHeight), $this->realWidth, ($y * $cellHeight), $black);
		    //     }
		    // }
		

	   	$x = 1;
	    $posstart = 0;
	    $i=0;
	    foreach ($product as $value) {
	    	if($i<2){
	    		if(!empty($value['image']) && file_exists(DIR_IMAGE.$value['image'])){
	    			$img = imagecreatefromjpeg(DIR_IMAGE.$value['image']);
	    			$this->putSquareImage($img,$x, 9, $posstart, 0);
		    		$posstart = $posstart + $x;	
	    		}
	    	}
	    	$i++;
	    }

	    $this->load->model('restapi/service');
	    $price = $this->model_restapi_service->getPriceByCategory($category_id);

		$cat_min_price = $price['minimum_price'];
		$cat_max_price = $price['maximum_price'];
		$currency  = $price['currency'];
		$min_price = $cat_min_price;
		$max_price = $cat_max_price;
		if($currency == 'INR'){
			$currency = 'Rs.';
		}else{
			$currency = '$';
		}

		$cat_name = $this->model_restapi_service->getCategoryName($category_id);

	    // The text to draw
		$image_text = "Buy ". $cat_name['name'] ." at wholesale price, starting from ". $currency ." ". $min_price;

        imagefilledrectangle($this->image, 0, 525, $realWidth, $realHeight, 0xf03140);
        imagettftext($this->image, 30, 0, 110, 590, $white, DIR_COMMON_TEMPLATE.'default/stylesheet/font/ProximaNova/ProximaNova-Light_0.otf', $image_text);
     	imagepng($this->image);

        $name 	= $category_id.".png";
        $today 	= Date('d_M_y');
 		imagepng($this->image, DIR_IMAGE."notification/" . $today ."/". $name);
 		imagedestroy($this->image);

	}

	public function putSquareImage($img, $sizeW, $sizeH, $posX, $posY){
	    // Cell width
	    $cellWidth 	= $this->realWidth / $this->gridWidth;
	    $cellHeight = $this->realHeight / $this->gridHeight;

	    // Conversion of our virtual sizes/positions to real ones
	    $realSizeW 	= ceil($cellWidth * $sizeW);
	    $realSizeH 	= ceil($cellHeight * $sizeH);
	    $realPosX 	= ($cellWidth * $posX);
	    $realPosY 	= ($cellHeight * $posY);

	    $img = $this->resizePreservingAspectRatio($img, $realSizeW, $realSizeH);

	    // Copying the image
	    imagecopyresampled($this->image, $img, $realPosX, $realPosY, 0, 0, $realSizeW, $realSizeH, imagesx($img), imagesy($img));
	}

	public function resizePreservingAspectRatio($img, $targetWidth, $targetHeight){
	    $srcWidth 	= imagesx($img);
	    $srcHeight 	= imagesy($img);

	    $srcRatio 	= $srcWidth / $srcHeight;
	    $targetRatio 	= $targetWidth / $targetHeight;
	    if (($srcWidth <= $targetWidth) && ($srcHeight <= $targetHeight))
	    {
	        $imgTargetWidth 	= $srcWidth;
	        $imgTargetHeight 	= $srcHeight;
	    }
	    else if ($targetRatio > $srcRatio)
	    {
	        $imgTargetWidth 	= (int) ($targetHeight * $srcRatio);
	        $imgTargetHeight 	= $targetHeight;
	    }
	    else
	    {
	        $imgTargetWidth 	= $targetWidth;
	        $imgTargetHeight 	= (int) ($targetWidth / $srcRatio);
	    }

	    $targetImg = imagecreatetruecolor($targetWidth, $targetHeight);

	    imagecopyresampled(
	       $targetImg,
	       $img,
	       ($targetWidth - $imgTargetWidth) / 2, // centered
	       ($targetHeight - $imgTargetHeight) / 2, // centered
	       0,
	       0,
	       $imgTargetWidth,
	       $imgTargetHeight,
	       $srcWidth,
	       $srcHeight
	    );

	    return $targetImg;
	}

	public function getCustomerGcmId($customer_id){
		$sql =   "SELECT  DISTINCT ws_gcm_registration_id FROM oc_customer c where c.ws_gcm_registration_id != '' AND customer_id = '". $customer_id ."' ";
		$query = $this->db->singleFieldquery($sql, 'ws_gcm_registration_id');

		return $query->rows;
	}
	public function sendPushNotification($registrationIds,$msg){

		// API access key from Google API's Console
		//define( 'API_ACCESS_KEY', 'YOUR-API-ACCESS-KEY-GOES-HERE' );
		//define( 'API_ACCESS_KEY', 'define( 'API_ACCESS_KEY', 'YOUR-API-ACCESS-KEY-GOES-HERE' );' );
		$fields = array
		(
				'registration_ids' 	=> $registrationIds,
				'data'			=> $msg
		);

		$headers = array
		(
				'Authorization: key=' . API_ACCESS_KEY,
				'Content-Type: application/json'
		);

		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
		// curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );

	}
}