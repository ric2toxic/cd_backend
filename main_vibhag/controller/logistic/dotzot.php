<?php
class ControllerLogisticDotzot extends Controller {

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
            
            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard&token='.$this->request->get["token"])
            );
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('Dotzot'),
                'href' => $this->url->link('logistic/dotzot&token='.$this->request->get["token"], '', 'SSL')
            );
            $data["download"] = 'sample/dotzot_sample.csv';
            $this->load->autoLoadLanguage('logistic/dotzot', $data);
            $this->document->setTitle($this->language->get('heading_title'));
            $this->load->model('logistic/dotzot');
            
            if(isset($this->request->files) && 
                empty($this->request->files["dotzot_upload"]["error"]) && 
                !empty($this->request->files)
            ){
              
                $fileName   = $this->request->files["dotzot_upload"]["name"];
                $fileExten  = explode(".",$fileName);
                $fileExten  = $fileExten[count($fileExten)-1];

                $numberOfColumnInCSV = 10;

                if($fileExten == 'csv'){
                   
                    $tmpName    = $this->request->files["dotzot_upload"]["tmp_name"];
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
                         if(count($columns) != $numberOfColumnInCSV){
                            echo 'Number of column mis-matched in CSV file.<br> 
                                  Reset CSV file with below number of columns and upload it again.';
                            $required_columns = $this->model_logistic_dotzot->requiredCSVfileColumns();
                            echo '<pre>'; print_r($required_columns); echo '<pre>';
                            exit;
                         }

                        $this->model_logistic_dotzot->truncateDotZotPincodesTable();
                        $ind = 0;
                        $batches = array();
                        
                        foreach ($sheetData as $key => $value){
                            $value[6] = ($value[6] == "Y") ? 1 : 0;
                            $value[7] = ($value[7] == "Y") ? 1 : 0;
                            $value[8] = ($value[8] == "Y") ? 1 : 0;
                            $value[9] = ($value[9] == "Y") ? 1 : 0;
                            unset($value[0]);
                            $value[10] = $currentDate;
                            $batches[$ind][] = "('".implode("','",$value)."')";
                            
                            if( $key > 10 &&  ($key+1) % 500 == 0 ) {
                                $ind++;
                            }
                        }
                        
                        $user = array();
                        $user["name"]        = $userName;
                        $user["update_date"] = $currentDate;
                        $jsonUser            =json_encode($user);
                        
                        $result_ulpload_file = $this->model_logistic_dotzot
                                                    ->addDotZotPincodes($batches);
                        if($result_ulpload_file){
                            $data['success'] = "Succesfully Updated";
                            $this->response->setOutput($this->load->view('logistic/dotzot_upload.tpl',
                                                                         $data
                                                                        )
                                                        );
                        }
                        else{
                            $data['error_warning'] = "File not uploded";
                            $this->response->setOutput($this->load->view('logistic/dotzot_upload.tpl',
                                                                         $data
                                                                        )
                                                        );
                        }
                     }
                     
                } else{
                    $data['error_warning'] = "Please Upload Valid (.CSV) file";
                    $this->response->setOutput($this->load->view('logistic/dotzot_upload.tpl',$data));
                }
            }
            if (isset($this->request->get['filter_pincode_no']) &&
                !empty($this->request->get['filter_pincode_no'])
            ) {
                $filterPincodeNo = $this->request->get['filter_pincode_no'];
            } else {
                $filterPincodeNo = null;
            }

            $filter_data = array(
                'filter_pincodes'   => $filterPincodeNo,
                'start'                => 0,
                'limit'                => $this->config->get('config_limit_admin')
            );
            $this->load->model('logistic/dotzot');
            if(isset($filterPincodeNo) && !empty($filterPincodeNo)){
                $filter_pincode = $this->model_logistic_dotzot->getFilterPincodes($filter_data);
                foreach ($filter_pincode as $value){

                    if($value["cod"] == 0){
                        $data["cod"][] = "No";
                    } else{
                        $data["cod"][] = "Yes";
                    }
                    if($value["prepaid"] == 0){
                        $data["prepaid"][] = "No";
                    } else{
                        $data["prepaid"][] = "Yes";
                    }
                    $data["filter_pincodes"][] = $value;
                }
            }

            if(!empty($this->request->get['msg'])) {
              $data['success'] = $this->request->get['msg'];
            }

            $data['header']       = $this->load->controller('common/header');
            $data['column_left']  = $this->load->controller('common/column_left');
            $data['footer']       = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('logistic/dotzot_upload.tpl',$data));

    }

    /**
    * Public function update_status to update pincodes status
    * 
    * @author MSA, August 2019
    */
    public function update_status()
    {
       $selected_pincodes   = $this->request->get['selected'] ?? '';
       $filter_pincode_no   = $this->request->get['filter_pincode_no'] ?? '';
       if(trim($selected_pincodes))
       {
           $pincode_data = array_unique( explode( ",", $selected_pincodes ) );
           $this->load->model('logistic/dotzot');
           $filter_pincode = $this->model_logistic_dotzot->updatePincodeStatus($pincode_data);
       }

       $url = "&filter_pincode_no=".$filter_pincode_no."&msg=Succesfully Updated";
       
       $this->response->redirect($this->url->link('logistic/dotzot', 
                                                  'token=' . $this->session->data['token'] . $url, 
                                                  'SSL'));
    }


}