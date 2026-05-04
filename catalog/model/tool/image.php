<?php

class ModelToolImage extends Model {

    public function resize($filename, $width, $height='') {
        $restapi_headers = $this->restapi->getRequestHeader();
        $static_content_url = STATIC_CONTENT_URL;
        if($this->request->server['HTTPS'] && (!isset($restapi_headers['SIGNATURE']))){
            $static_content_url = STATIC_CONTENT_URL_SSL;
        }
        // if nginx is enable, then no need to do resizing, directly return the url.
        if(NGINX_ENABLED == 1){

            if (empty($filename)) {
                $filename = 'placeholder.jpg';
            }

            $quality = 90;
            //if( (int)$width < 100 ) $quality = 40;
            //else if( (int)$width < 200 ) $quality = 60;
            return $static_content_url . 'img/dw=' . $width . ',dh=' . $height . ',q='. $quality .'/' . $filename;
            exit();
        }

        if (!is_file(DIR_IMAGE . $filename) || !file_exists(DIR_IMAGE . $filename)) {
            $filename = 'placeholder.png';
        }


        $is_staging = strpos($_SERVER['REQUEST_URI'], 'staging');
        
        if ($is_staging !== false) {

           $extension = pathinfo($filename, PATHINFO_EXTENSION);            

           $new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;            

           if ($this->request->server['HTTPS'] && (!isset($restapi_headers['SIGNATURE']))) {

               return STATIC_CONTENT_URL_SSL . $new_image;

           } else {

               return STATIC_CONTENT_URL . $new_image;

           }        



           } 
           else 
           {

            $this->load->model('tool/s3');
            if (empty($height)) {

                if (file_exists(DIR_IMAGE . $filename)) {

                    list($original_width, $original_height, $type, $attr) = getimagesize(DIR_IMAGE . $filename);

                    $ratio = $original_width / $original_height;

                    $height = floor($width / $ratio);

                }

            }



            $extension = pathinfo($filename, PATHINFO_EXTENSION);

            $s3_image = '';

            $old_image = $filename;

            $new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;





            if (S3_ENABLED == 0 || !$this->model_tool_s3->checkExistence($new_image)) {



                if (!is_file(DIR_IMAGE . $new_image) || (filectime(DIR_IMAGE . $old_image) > filectime(DIR_IMAGE . $new_image))) {

                    $path = '';



                    $directories = explode('/', dirname(str_replace('../', '', $new_image)));



                    foreach ($directories as $directory) {

                        $path = $path . '/' . $directory;



                        if (!is_dir(DIR_IMAGE . $path)) {

                            @mkdir(DIR_IMAGE . $path, 0777);

                        }

                    }



                    list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $old_image);



                    if ($width_orig != $width || $height_orig != $height) {

                        $image = new Image(DIR_IMAGE . $old_image);

                        $image->resize($width, $height);

                        $image->save(DIR_IMAGE . $new_image);

                    } else {

                        copy(DIR_IMAGE . $old_image, DIR_IMAGE . $new_image);

                    }

                }

             //upload to S3
            if (S3_ENABLED == 1) {
               $s3_image = $this->model_tool_s3->createObject($new_image);
                        unlink(DIR_IMAGE . $new_image);
              }   

            } else {

                $s3_image = $this->model_tool_s3->getObject($new_image);

            }



            if (S3_ENABLED == 1) {

                return $s3_image;

            } else {

                //This header ensure that we do send non SSL image url in case of mobile api call

                $restapi_headers = $this->restapi->getRequestHeader();



                if ($this->request->server['HTTPS'] && (!isset($restapi_headers['SIGNATURE']))) {

                    return STATIC_CONTENT_URL_SSL . $new_image;

                } else {

                    return STATIC_CONTENT_URL . $new_image;

                }

            }

        }

    }



    public function resizeBasedOnLargeDimension($filename, $size) {
        $restapi_headers = $this->restapi->getRequestHeader();
        // if nginx is enable, then no need to do resizing, directly return the url.
        if(NGINX_ENABLED == 1) {
            if ($this->request->server['HTTPS'] && (!isset($restapi_headers['SIGNATURE']))) {
                return STATIC_CONTENT_URL_SSL . 'img/dw=' . $size . ',q=90/' . $filename;
            } else {
                return STATIC_CONTENT_URL . 'img/dw=' . $size . ',q=90/' . $filename;
            }
        }


        if (!is_file(DIR_IMAGE . $filename) || !file_exists(DIR_IMAGE.$filename)) {

            return;

        }





        if (file_exists(DIR_IMAGE . $filename)) {

            list($original_width, $original_height, $type, $attr) = getimagesize(DIR_IMAGE . $filename);

            $ratio = $original_width / $original_height;

            if ($ratio > 1) {

                $height = $size;

                $width = floor($size * $ratio);

            } else {

                $width = $size;

                $height = floor($size/$ratio);

            }

        }



       return $this->resize($filename, $width, $height);




    }



    public function getOriginalImage($filename) {
        $restapi_headers = $this->restapi->getRequestHeader();
        if(NGINX_ENABLED == 1) {
            if ($this->request->server['HTTPS'] && (!isset($restapi_headers['SIGNATURE']))) {
                return STATIC_CONTENT_URL_SSL . 'img/' . $filename;
            } else {
                return STATIC_CONTENT_URL . 'img/' . $filename;
            }
        }
        else{
            if ($this->request->server['HTTPS'] && (!isset($restapi_headers['SIGNATURE']))) {
                return STATIC_CONTENT_URL_SSL . $filename;
            } else {
                return STATIC_CONTENT_URL . $filename;
            }
        }

    }

}