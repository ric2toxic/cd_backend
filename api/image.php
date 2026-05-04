 <?php
     require_once('system.php');

    class ImageController extends SystemController
    {
        private static $displayed = false;
        public function __construct($params) {

            parent::__construct($params);
        }
        /**
         * customer login
         */
        public function thumb_image() 
        {
          $this->load->model('tool/image');

          if (isset($this->request['image'])) {
            $image = $this->request['image'];
           } else {
            $image = false;
           }

              $data['image'] = $this->model_tool_image->resize($image, $this->request['width'], $this->request['height']);
              if(isset($this->request['additional_width']))
              {
                $data['thumb'] = $this->model_tool_image->resize($image, $this->request['additional_width'], $this->request['additional_height']);
              }
              if(isset($this->request['popup_width']))
              {
                $data['popup'] = $this->model_tool_image->resize($image, $this->request['popup_width'], $this->request['popup_height']);
              }

            $this->data       = $data;
            $this->message    = 'data fatch successfully';
            $this->statusCode = 200;
            return $this->__response();
        }

        public function download_image() 
        {
           $this->load->model('catalog/product');
           $this->load->model('tool/image');

          if (isset($this->request['product_id'])) {
            $product_id = $this->request['product_id'];
           } else {
            $product_id = 0;
           }

           if (isset($this->request['model'])) {
            $model = $this->request['model'];
           } else {
            $model = time().$this->request['product_id'];
           }

          if ($_SERVER['HTTPS']) {
           $static_content_url =  STATIC_CONTENT_URL_SSL;
           } else {
            $static_content_url =  STATIC_CONTENT_URL ;
           } 
           
           $result = $this->model_catalog_product->getProductImages($product_id);
           $files = array();
           foreach($result as $nw_result)
           {
               $extension  = pathinfo($nw_result['image'], PATHINFO_EXTENSION);
               $dimensions = unserialize($nw_result['image_dimensions']);
               if(!empty($dimensions['width']))
                   $width_orig = $dimensions['width'];
               if(!empty($dimensions['height']))
                   $height_orig = $dimensions['height'];

               if (!empty($width_orig) && !empty($height_orig) && ($width_orig / $height_orig) > 1)
               {
                   $files[] = $this->model_tool_image->resize($nw_result['image'],$this->config->get('config_image_popup_height'),$this->config->get('config_image_popup_width'));
               }
               else
               {
                   $files[] = $this->model_tool_image->resize($nw_result['image'],$this->config->get('config_image_popup_width'),$this->config->get('config_image_popup_height'));
               }
           }

            $original_image_result = $this->model_catalog_product->getProductOriginalImages($product_id);
            $original_image = $original_image_result['image'];
            $extension  = pathinfo($original_image, PATHINFO_EXTENSION);

            $dimensions = unserialize($original_image_result['image_dimensions']);
            if(!empty($dimensions['width']))
                $width_orig = $dimensions['width'];
            if(!empty($dimensions['height']))
                $height_orig = $dimensions['height'];
            if (!empty($width_orig) && !empty($height_orig) && ($width_orig / $height_orig) > 1)
            {
                $files[] = $this->model_tool_image->resize($original_image,$this->config->get('config_image_popup_height'),$this->config->get('config_image_popup_width'));
            }
            else
            {
                $files[] = $this->model_tool_image->resize($original_image,$this->config->get('config_image_popup_width'),$this->config->get('config_image_popup_height'));
            }
            
           $file_name = $model;
           createAndDownloadZip($files, $file_name, true, true, true);
           exit;
        }

    public function get_http_response_code($url) {
      $headers = get_headers($url);
      return substr($headers[0], 9, 3);
    }

    public function download_mobile_image()
    {
      $this->load->model('catalog/product');
      $this->load->model('tool/image');

      if (isset($this->request['product_id'])) {
            $product_id = $this->request['product_id'];
       } else {
            $product_id = 0;
           }

      if (isset($this->request['model'])) 
        {
            $model = $this->request['model'];
        } 
      else 
       {
            $model = time().$this->request['product_id'];
       }


      $result = $this->model_catalog_product->getProductImages($product_id);
      
      $images = array();   
      foreach($result as $nw_result)
      {
         $images[] =  $this->model_tool_image->resize($nw_result['image'],$this->config->get('config_image_popup_width'),$this->config->get('config_image_popup_height'));
      }

       $OriginalImages = $this->model_catalog_product->getProductOriginalImages($product_id);

       $images[] = $this->model_tool_image->resize($OriginalImages['image'],$this->config->get('config_image_popup_width'),$this->config->get('config_image_popup_height'));

        $this->data_packet->data       = $images;
        $this->data_packet->message    = 'data fatch successfully';
        $this->data_packet->statusCode = 200;
        return $this->data_packet;

    }  



        private function __response(){
           // response function
           $response = array();
           $response['statusCode'] = $this->statusCode;
           $response['message']    = $this->message;
           $response['data']       = $this->data;
           return $response; 
        }

    }