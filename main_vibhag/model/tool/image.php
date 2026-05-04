<?php
class ModelToolImage extends Model {
	public function resize($filename, $width, $height) {
        if(NGINX_ENABLED == 1) {

            if (empty($filename)) {
                $filename = 'placeholder.jpg';
            }

            if ($this->request->server['HTTPS']) {
                return STATIC_CONTENT_URL_SSL . 'img/dw=' . $width . ',dh=' . $height . ',q=80/' . $filename;
            } else {
                return STATIC_CONTENT_URL . 'img/dw=' . $width . ',dh=' . $height . ',q=80/' . $filename;
            }

            exit();
        }

        $is_staging = strpos($_SERVER['REQUEST_URI'], 'staging');
        if ($_SERVER['SERVER_ADDR'] == '127.0.0.1' OR $_SERVER['SERVER_ADDR'] == '::1' OR $is_staging !== false) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);

            $new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

            if ($this->request->server['HTTPS'] && (!isset($restapi_headers['SIGNATURE']))) {
                return STATIC_CONTENT_URL_SSL . $new_image;
            } else {
                return STATIC_CONTENT_URL . $new_image;
            }

        } else {
            if (!is_file(DIR_IMAGE . $filename) || !file_exists(DIR_IMAGE . $filename)) {
                return '';
            }


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


            if ($this->request->server['HTTPS']) {
                return STATIC_CONTENT_URL_SSL . $new_image;
            } else {
                return STATIC_CONTENT_URL . $new_image;
            }
        }

    }

    public function getOriginalImage($filename) {

        if(NGINX_ENABLED == 1) {
            if ($this->request->server['HTTPS'] /*&& (!isset($restapi_headers['SIGNATURE']))*/) {
                return STATIC_CONTENT_URL_SSL . 'img/' . $filename;
            } else {
                return STATIC_CONTENT_URL . 'img/' . $filename;
            }
        }
        else{
            return $filename;
        }

    }
}

// OLD Resize Function
/*
		if (!is_file(DIR_IMAGE . $filename)) {
			return;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$old_image = $filename;
		$new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

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

		if ($this->request->server['HTTPS']) {
			return HTTPS_CATALOG . 'image/' . $new_image;
		} else {
			return HTTP_CATALOG . 'image/' . $new_image;
		}
		
		*/