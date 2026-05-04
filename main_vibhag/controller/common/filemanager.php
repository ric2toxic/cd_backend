<?php
class ControllerCommonFileManager extends Controller {
	public function index() {
		//$this->load->language('common/filemanager');
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('common/filemanager', $data);
        if(NGINX_ENABLED == 1){
            if ($_SERVER['HTTPS']) {
                $static_content_url =  STATIC_CONTENT_URL_SSL;
            } else {
                $static_content_url =  STATIC_CONTENT_URL ;
            }
            $cdn_url = $static_content_url.'filemanager/';
            $qr_string = $_SERVER['QUERY_STRING'];
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );
            $dir_data = file_get_contents($cdn_url.'?'.$qr_string, false, stream_context_create($arrContextOptions));
            //var_dump($dir_data);die;
            $dir_data = json_decode($dir_data,true);
            if(is_array($dir_data))
                $data = array_merge($data, $dir_data);

            $data['cdn_url'] = $cdn_url;

            $data['token'] = $this->session->data['token'];

            if(isset($data['image_total']))
                $image_total = $data['image_total'];
            else
                $image_total = 0;
        }
        else {
            if (isset($this->request->get['filter_name'])) {
                $filter_name = rtrim(str_replace(array('../', '..\\', '..', '*'), '', $this->request->get['filter_name']), '/');
            } else {
                $filter_name = null;
            }

            // Make sure we have the correct directory

            if (isset($this->request->get['data_directory']) && !empty($this->request->get['data_directory'])) {
                $directory = rtrim(DIR_IMAGE . $this->request->get['data_directory']);
            } elseif (isset($this->request->get['full_path']) && !empty($this->request->get['full_path'])) {
                $directory = trim($this->request->get['full_path']);
            } else {
                if (isset($this->request->get['directory'])) {
                    $directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', $this->request->get['directory']), '/');
                } else {
                    $directory = DIR_IMAGE . 'catalog';
                }
            }

            if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
            } else {
                $page = 1;
            }

            $data['images'] = array();

            $this->load->model('tool/image');


            if (strlen(trim($filter_name)) > 0) {
                $characters = str_split($filter_name);
                $filter_name = '';
                foreach ($characters as $char) {
                    $filter_name .= ('[' . strtolower($char) . strtoupper($char) . ']');
                }
            }

            // Get directories
            $directories = glob($directory . '/*' . $filter_name . '*', GLOB_ONLYDIR);

            if (!$directories) {
                $directories = array();
            }

            // Get files
            $files = glob($directory . '/*' . $filter_name . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);

            if (!$files) {
                $files = array();
            }

            // Merge directories and files
            $images = array_merge($directories, $files);

            // Get total number of files and directories
            $image_total = count($images);

            // Split the array based on current page number and max number of items per page of 10
            $images = array_splice($images, ($page - 1) * 8, 8);

            foreach ($images as $image) {
                $name = str_split(basename($image), 32);

                if (is_dir($image)) {
                    $url = '';

                    if (isset($this->request->get['target'])) {
                        $url .= '&target=' . $this->request->get['target'];
                    }

                    if (isset($this->request->get['thumb'])) {
                        $url .= '&thumb=' . $this->request->get['thumb'];
                    }

                    $data['images'][] = array(
                        'thumb' => '',
                        'name' => implode(' ', $name),
                        'type' => 'directory',
                        'path' => utf8_substr($image, utf8_strlen(DIR_IMAGE)),
                        'href' => $this->url->link('common/filemanager', 'token=' . $this->session->data['token'] . '&directory=' . html_entity_decode(utf8_substr($image, utf8_strlen(DIR_IMAGE . 'catalog/'))) . $url, 'SSL')
                    );
                } elseif (is_file($image)) {
                    // Find which protocol to use to pass the full image link back
                    if ($this->request->server['HTTPS']) {
                        $server = HTTPS_CATALOG;
                    } else {
                        $server = HTTP_CATALOG;
                    }

                    $data['images'][] = array(
                        'thumb' => $this->model_tool_image->resize(utf8_substr($image, utf8_strlen(DIR_IMAGE)), 74, 111),
                        'name' => implode(' ', $name),
                        'type' => 'image',
                        'path' => utf8_substr($image, utf8_strlen(DIR_IMAGE)),
                        'href' => $server . 'image/' . utf8_substr($image, utf8_strlen(DIR_IMAGE))
                    );
                }
            }


            $data['token'] = $this->session->data['token'];

            if ((isset($this->request->get['data_directory']) && !empty($this->request->get['data_directory']))
                or
                (isset($this->request->get['full_path']) && !empty($this->request->get['full_path']))
            ) {
                $prefix = DIR_IMAGE . 'catalog';

                if (substr($directory, 0, strlen($prefix)) == $prefix) {
                    $data['directory'] = urlencode(substr($directory, strlen($prefix)));
                }
            } else {

                if (isset($this->request->get['directory'])) {
                    $data['directory'] = urlencode($this->request->get['directory']);
                } else {
                    $data['directory'] = '';
                }
            }

            if (isset($this->request->get['filter_name'])) {
                $data['filter_name'] = $this->request->get['filter_name'];
            } else {
                $data['filter_name'] = '';
            }

            // Return the target ID for the file manager to set the value
            if (isset($this->request->get['target'])) {
                $data['target'] = $this->request->get['target'];
            } else {
                $data['target'] = '';
            }

            // Return the thumbnail for the file manager to show a thumbnail
            if (isset($this->request->get['thumb'])) {
                $data['thumb'] = $this->request->get['thumb'];
            } else {
                $data['thumb'] = '';
            }

            // Parent
            $url = '';
            if (isset($this->request->get['data_directory'])) {
                $pos = strrpos($directory, '/');
                if ($pos) {
                    $url .= '&full_path=' . html_entity_decode(substr($directory, 0, $pos));
                }
            } else {
                if (isset($this->request->get['directory'])) {
                    $pos = strrpos($this->request->get['directory'], '/');

                    if ($pos) {
                        $url .= '&directory=' . urlencode(substr($this->request->get['directory'], 0, $pos));
                    }
                }
            }

            if (isset($this->request->get['target'])) {
                $url .= '&target=' . $this->request->get['target'];
            }

            if (isset($this->request->get['thumb'])) {
                $url .= '&thumb=' . $this->request->get['thumb'];
            }

            $data['parent'] = $this->url->link('common/filemanager', 'token=' . $this->session->data['token'] . $url, 'SSL');

            // Refresh
            $url = '';
            if (isset($this->request->get['data_directory'])) {
                $url .= '&data_directory=' . urlencode(html_entity_decode($this->request->get['data_directory']));
            } elseif (isset($this->request->get['full_path'])) {
                $url .= '&full_path=' . html_entity_decode($this->request->get['full_path']);
            } else {
                if (isset($this->request->get['directory'])) {
                    $url .= '&directory=' . urlencode($this->request->get['directory']);
                }
            }

            if (isset($this->request->get['target'])) {
                $url .= '&target=' . $this->request->get['target'];
            }

            if (isset($this->request->get['thumb'])) {
                $url .= '&thumb=' . $this->request->get['thumb'];
            }

            $data['refresh'] = $this->url->link('common/filemanager', 'token=' . $this->session->data['token'] . $url, 'SSL');
        }
		$url = '';
		if(isset($this->request->get['data_directory'])){
			$url .= '&data_directory=' .urlencode(html_entity_decode($this->request->get['data_directory']));
		}elseif(isset($this->request->get['full_path'])){
			$url .= '&full_path=' .urlencode(html_entity_decode($this->request->get['full_path']));
		}else {
			if (isset($this->request->get['directory'])) {
				$url .= '&directory=' . (html_entity_decode($this->request->get['directory'], ENT_QUOTES, 'UTF-8'));
			}
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['target'])) {
			$url .= '&target=' . $this->request->get['target'];
		}

		if (isset($this->request->get['thumb'])) {
			$url .= '&thumb=' . $this->request->get['thumb'];
		}

        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }

		$pagination = new Pagination();
		$pagination->total = $image_total;
		$pagination->page = $page;
		$pagination->limit = 8;
		$pagination->num_links = 3;
		$pagination->url = $this->url->link('common/filemanager', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$this->response->setOutput($this->load->view('common/filemanager.tpl', $data));
	}

	public function upload() {
		$this->load->language('common/filemanager');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Make sure we have the correct directory
		if (isset($this->request->get['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', $this->request->get['directory']), '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		// Check its a directory
		if (!is_dir($directory)) {
			$json['error'] = $this->language->get('error_directory');
		}

		if (!$json) {
			if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
				// Sanitize the filename
				$filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));

				// Validate the filename length
				if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
					$json['error'] = $this->language->get('error_filename');
				}

				// Allowed file extension types
				$allowed = array(
					'jpg',
					'jpeg',
					'gif',
					'png'
				);

				if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
					$json['error'] = $this->language->get('error_filetype');
				}

				// Allowed file mime types
				$allowed = array(
					'image/jpeg',
					'image/pjpeg',
					'image/png',
					'image/x-png',
					'image/gif'
				);

				if (!in_array($this->request->files['file']['type'], $allowed)) {
					$json['error'] = $this->language->get('error_filetype');
				}

				// Check to see if any PHP files are trying to be uploaded
				$content = file_get_contents($this->request->files['file']['tmp_name']);

				if (preg_match('/\<\?php/i', $content)) {
					$json['error'] = $this->language->get('error_filetype');
				}

				// Return any upload error
				if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
					$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
				}
			} else {
				$json['error'] = $this->language->get('error_upload');
			}
		}

		if (!$json) {
			move_uploaded_file($this->request->files['file']['tmp_name'], $directory . '/' . $filename);

			$json['success'] = $this->language->get('text_uploaded');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function folder() {
		$this->load->language('common/filemanager');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Make sure we have the correct directory
		if (isset($this->request->get['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', $this->request->get['directory']), '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		// Check its a directory
		if (!is_dir($directory)) {
			$json['error'] = $this->language->get('error_directory');
		}

		if (!$json) {
			// Sanitize the folder name
			$folder = str_replace(array('../', '..\\', '..'), '', basename(html_entity_decode($this->request->post['folder'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($folder) < 3) || (utf8_strlen($folder) > 128)) {
				$json['error'] = $this->language->get('error_folder');
			}

			// Check if directory already exists or not
			if (is_dir($directory . '/' . $folder)) {
				$json['error'] = $this->language->get('error_exists');
			}
		}

		if (!$json) {
            $oldmask = umask(0);
			mkdir($directory . '/' . $folder, 0777);
            umask($oldmask);

			$json['success'] = $this->language->get('text_directory');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete() {
		$this->load->language('common/filemanager');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['path'])) {
			$paths = $this->request->post['path'];
		} else {
			$paths = array();
		}

		// Loop through each path to run validations
		foreach ($paths as $path) {
			$path = rtrim(DIR_IMAGE . str_replace(array('../', '..\\', '..'), '', $path), '/');

			// Check path exsists
			if ($path == DIR_IMAGE . 'catalog') {
				$json['error'] = $this->language->get('error_delete');

				break;
			}
		}

		if (!$json) {
			// Loop through each path
			foreach ($paths as $path) {
				$path = rtrim(DIR_IMAGE . str_replace(array('../', '..\\', '..'), '', $path), '/');

				// If path is just a file delete it
				if (is_file($path)) {
					unlink($path);

				// If path is a directory beging deleting each file and sub folder
				} elseif (is_dir($path)) {
					$files = array();

					// Make path into an array
					$path = array($path . '*');

					// While the path array is still populated keep looping through
					while (count($path) != 0) {
						$next = array_shift($path);

						foreach (glob($next) as $file) {
							// If directory add to path array
							if (is_dir($file)) {
								$path[] = $file . '/*';
							}

							// Add the file to the files to be deleted array
							$files[] = $file;
						}
					}

					// Reverse sort the file array
					rsort($files);

					foreach ($files as $file) {
						// If file just delete
						if (is_file($file)) {
							unlink($file);

						// If directory use the remove directory function
						} elseif (is_dir($file)) {
							rmdir($file);
						}
					}
				}
			}

			$json['success'] = $this->language->get('text_delete');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function multipleImageUpload(){
		// Make sure we have the correct directory
		if (isset($this->request->get['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', $this->request->get['directory']), '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		//$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
		$targetDir = $directory;
		
//$targetDir = 'uploads';
		$cleanupTargetDir = false; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds
	
// Create target dir
//		if (!file_exists($targetDir)) {
//			@mkdir($targetDir);
//		}

// Get a file name
		if (isset($_REQUEST["name"])) {
			$fileName = $_REQUEST["name"];
		} elseif (!empty($_FILES)) {
			$fileName = $_FILES["file"]["name"];
		} else {
			$fileName = uniqid("file_");
		}
		
		//$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
		$filePath = $targetDir . '/' . $fileName;
		if(file_exists($filePath)) {
			chmod($filePath,0777);
		}

		$supported_image = array(
								'jpg',
								'jpeg',
								'JPEG',
								'JPG',
								'png',
								'PNG'
								);
			
		$ext = pathinfo($filePath, PATHINFO_EXTENSION);
		$name_image = '';
		
		if(in_array($ext,$supported_image)) {
			$name_image = basename($fileName,".".$ext);
			$name = $name_image.".".strtolower($ext);
		} 
		
		
		// Using strtolower to overcome case sensitive
		if (!in_array(strtolower($ext), $supported_image)) {
			//setting file_error key to 1 and setting cleanupTargetDir to 1 in case of error in name or extension found
			$_FILES['file']['error'] = 1;
			//cleanupTargetDir will cleam up all the tmp files (.part in this case) which have expired the set time
			$cleanupTargetDir = true;
		} else if(!preg_match("/^[-a-zA-Z_0-9]*$/",trim($name_image))) {
			$_FILES['file']['error'] = 1;
			$cleanupTargetDir = true;
		}
	
		//$filePath = $directory;

// Chunking might be enabled
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

// Remove old temp files
		if ($cleanupTargetDir) {
			
			if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
			}

			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

				// If temp file is current file proceed to the next
				if ($tmpfilePath == "{$filePath}.part") {
					continue;
				}

				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
					@unlink($tmpfilePath);
				}
			}
			closedir($dir);
		}

// Open temp file
		if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}
		
		if (!empty($_FILES)) {
			if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
			}

			// Read binary input stream and append it to temp file
			if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		} else {
			if (!$in = @fopen("php://input", "rb")) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		}

		while ($buff = fread($in, 4096)) {
			fwrite($out, $buff);
		}

		@fclose($out);
		@fclose($in);

// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off
			rename("{$filePath}.part", $filePath);
			//if uploaded, then change size and permissions of file
			system("mogrify -geometry x1200 $filePath"); 
			system("mogrify -quality x60 $filePath"); 
			chmod($filePath,0777);
		}

// Return Success JSON-RPC response
		//die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
	}
}
