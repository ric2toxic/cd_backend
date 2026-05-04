<?php
class ControllerLogisticConnectIndia extends Controller {

	public function index () { 
            
            $userName = $this->user->getUserName();
            if(is_array($userName)){
                $userName = $userName['username'];
            }
            
            $currentDate = date("y-m-d H:i:s", time());
            set_time_limit (10000);
            $data = array();
            if(isset($this->request->get["token"]) && !empty($this->request->get["token"])){
                $data['token'] = $this->request->get["token"];
            }
            //$this->document->setTitle($this->language->get('heading_title'));
            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard&token='.$this->request->get["token"])
            );
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('ConnectIndia'),
                'href' => $this->url->link('logistic/connect_india&token='.$this->request->get["token"], '', 'SSL')
            );
            $data["download"] = 'sample/connect_india_sample.csv';
            $this->load->autoLoadLanguage('logistic/connect_india', $data);
            $this->load->model('logistic/connect_india');
            
            
            if(isset($this->request->files) && empty($this->request->files["connect_india_upload"]["error"]) && !empty($this->request->files)){
              
                $fileName   = $this->request->files["connect_india_upload"]["name"];
                $fileExten  = explode(".",$fileName);
                $fileExten  = $fileExten[count($fileExten)-1];

                if($fileExten == 'csv'){
                   
                    $tmpName    = $this->request->files["connect_india_upload"]["tmp_name"];
                    $targetPath = DIR_UPLOAD.$userName.$currentDate.$fileName;
                    move_uploaded_file($tmpName, $targetPath);
                    $sheetData = array();
                    
                    if (($handle = fopen($targetPath, "r")) !== FALSE) {
                        while (($csvData = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            $sheetData[] = $csvData;
                        }
                        fclose($handle);
                    }
                    if(isset($sheetData) && !empty($sheetData)){
                         $columns = array_shift($sheetData);
                         if(count($columns) < 3){
                             exit;
                         }
                        $this->load->model('logistic/connect_india');
                        $this->model_logistic_connect_india->truncatePincodesTable();
                        $ind = 0;
                        $batches = array();
                        
                        foreach ($sheetData as $key => $value){
                            // remove S.No. number value from array
                            if(isset($value[3])){
                              $value[3] = $this->removeExtraChars($value[3]);
                            }
                            unset($value[0]);
                            $batches[$ind][] = "('".implode("','",$value)."')";
                            if( $key > 10 &&  ($key+1) % 500 == 0 ) {
                                $ind++;
                            }
                        }
                        $result_ulpload_file = $this->model_logistic_connect_india->addPincodes($batches);
                        
                        if($result_ulpload_file){
                            $data['success'] = "Succesfully Updated";
                            $this->response->setOutput($this->load->view('logistic/connect_india_upload.tpl',$data));
                        }
                        else{
                            $data['error_warning'] = "File not uploded";
                            $this->response->setOutput($this->load->view('logistic/connect_india_upload.tpl',$data));
                        }
                     }
                     
                } else{
                    $data['error_warning'] = "Please Upload Valid (.CSV) file";
                    $this->response->setOutput($this->load->view('logistic/connect_india_upload.tpl',$data));
                }
            }
            if (isset($this->request->get['filter_pincode_no']) &&!empty($this->request->get['filter_pincode_no'])) {
                $filterPincodeNo = $this->request->get['filter_pincode_no'];
            } else {
                $filterPincodeNo = null;
            }

            $filter_data = array(
                'filter_pincode'   => $filterPincodeNo,
                'start'                => 0,
                'limit'                => $this->config->get('config_limit_admin')
            );

            if(isset($filterPincodeNo) && !empty($filterPincodeNo)){
                $filter_pincode = $this->model_logistic_connect_india->getFilterPincodes($filter_data);
                foreach ($filter_pincode as $value){
                    $data["filter_pincodes"][] = $value;
                }
            }

            $data['header']       = $this->load->controller('common/header');
            $data['column_left']  = $this->load->controller('common/column_left');
            $data['footer']       = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('logistic/connect_india_upload.tpl',$data));

    }
    protected function removeExtraChars($item)
    {   
        $search = array(" ","'","\"","&nbsp;");
        $replace = array("","","","");
        return str_replace($search, $replace, htmlentities($item));
    }
}