<?php
class ControllerRestapiImage extends Controller{

  public function get_http_response_code($url) {
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
  }
  
  public function load(){
    
    ob_start();
    $url = "";
    $original_image = isset($_GET['original_image'])?$_GET['original_image']:'';
    $display_image = isset($_GET['display_image'])?$_GET['display_image']:'';
    $width = isset($_GET['width'])?$_GET['width']:'';
    $height = isset($_GET['height'])?$_GET['height']:'';

    if (isset($_SERVER["HTTPS"]) && ((strtolower($_SERVER["HTTPS"]) == "on") || ($_SERVER['HTTPS'] == '1'))) {
      $static_content_url =  STATIC_CONTENT_URL_SSL;
    } else {
      $static_content_url =  STATIC_CONTENT_URL;
    }

    $extension  = pathinfo($display_image, PATHINFO_EXTENSION);
    $url = $static_content_url.$display_image;

    if($this->get_http_response_code($url) != "200"){
      $this->load->model('tool/image');
      $url = $this->model_tool_image->resizeBasedOnLargeDimension($original_image, $width);
      $opts = array('http' =>
        array(
            'header'  => 'Content-type: image/'.$extension,
        )
      );
      $context  = stream_context_create($opts);
      $image = file_get_contents(trim($url), false, $context);
      while(ob_get_level()){
        ob_end_clean();
      }
      header('Content-Type: image/'.$extension); // it will return image
      header('Cache-Control: no-cache, no-store, max-age=0, s-maxage=0,  must-revalidate');
      header('Cache-Control: post-check=0, pre-check=0', false);
      header('Pragma: no-cache');
      echo $image;
    } else {
      $opts = array('http' =>
        array(
            'header'  => 'Content-type: image/'.$extension,
        )
      );
      $context  = stream_context_create($opts);
      $image = file_get_contents(trim($url), false, $context);//echo $extension;
      while(ob_get_level()){
        ob_end_clean();
      }
      header('Content-Type: image/'.$extension); // it will return image
      header('Cache-Control: no-cache, no-store, max-age=0, s-maxage=0,  must-revalidate');
      header('Cache-Control: post-check=0, pre-check=0', false);
      header('Pragma: no-cache');
      echo $image;
    }
  }
}