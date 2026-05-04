<?php
class ControllerReportCohort extends Controller {

  public function index()
  {
      $this->load->model('report/cohort');

      $data = array(); // Initializing the data array to be passed on to template files
      // Autoloading the lanugage
      $this->load->autoLoadLanguage('report/cohort', $data);

      $this->document->setTitle($data['heading_title']);

      $data['token'] = $this->session->data['token'];

      $url = '';
      $data['breadcrumbs'] = array();
      $data['breadcrumbs'][] = array(
          'text' => $data['text_home'],
          'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
      );

      $data['breadcrumbs'][] = array(
          'text' => $data['heading_title'],
          'href' => $this->url->link('report/analysis', 'token=' . $this->session->data['token'] . $url, 'SSL')
      );

      $data['order_types_filter'] = $this->getOrderTypes($data);
     
      $data['customer_types_filter'] = $this->getCustomerType($data);

      $data['cohort_types'] = $this->getCohortTypes($data);

      $data['form_action'] = $this->url->link('report/analysis/cohortAnalysis', 'token=' . $this->session->data['token'], 'SSL');

      $data['deffault_filter_dates'] = date('Y-m-d');

      $data['image_path'] = HTTPS_CATALOG.'khufiya_vibhag/view/image/ajax-loader.gif';

      $data['header'] = $this->load->controller('common/header');
      $data['column_left'] = $this->load->controller('common/column_left');
      $data['footer'] = $this->load->controller('common/footer');

      $this->response->setOutput($this->load->view('report/cohort_analysis.tpl', $data));
  }

  public function getCohortReport()
  {
      $this->load->model('report/cohort');

      $filter_data = $this->getFilters();
     
      if(!empty($filter_data)) {
       
        $cohort_type           = $filter_data['cohort_type'] ?? '';
        $month_interval        = $filter_data['filter_month_interval'] ?? 1;
        $x_axis_month_interval = array('month_interval' => $month_interval);
        $y_axis                = array(
                                       'date_start' => $filter_data['filter_date_from'] ?? '',
                                       'date_end'   => $filter_data['filter_date_to'] ?? '',
                                       'sort'       => $filter_data['filter_sort'] ?? '',
                                      );
        
        $order_filters         = array('failed_order' => 0, 
                                       'non_INR'      => 0, 
                                       'franchise'    => 0, 
                                       'containing_payment_code' => '',
                                       'excluding_payment_code' => '',
                                       'shipping_zone_id' => ''
                                      ); 
        //pr($this->request->get); die;
        if(!empty($filter_data['order_type'])) {
            $order_types = explode(',', $filter_data['order_type']);
            $order_filters['failed_order']  =  in_array('failed_order',$order_types) ? 1 : 0;
            $order_filters['non_INR']       =  in_array('non_INR',$order_types) ? 1 : 0;
            $order_filters['franchise']     =  in_array('franchise',$order_types) ? 1 : 0;
        }
        if(!empty($filter_data['containing_payment_code'])) {
            $containing_payment_code = explode(',', $filter_data['containing_payment_code']);
            $order_filters['containing_payment_code'] = $containing_payment_code;
        }
        if(!empty($filter_data['excluding_payment_code'])) {
            $excluding_payment_code = explode(',', $filter_data['excluding_payment_code']);
            $order_filters['excluding_payment_code'] = $excluding_payment_code;
        }
        if(!empty($filter_data['contains_category_order_ids'])) {
            $contains_category_order_ids = explode(',', $filter_data['contains_category_order_ids']);
            $order_filters['contains_category_order_ids'] = $contains_category_order_ids;
        }
        if(!empty($filter_data['exclusive_category_order_ids'])) {
            $exclusive_category_order_ids = explode(',', $filter_data['exclusive_category_order_ids']);
            $order_filters['exclusive_category_order_ids'] = $exclusive_category_order_ids;
        }
        if(!empty($filter_data['shipping_zone_id'])){
          $shipping_zone_ids = explode(',', $filter_data['shipping_zone_id']);
          $order_filters['shipping_zone_id'] = $shipping_zone_ids;
        }
        $customer_filters = array();
        if(!empty($filter_data['min_order_count'])) {
          $customer_filters['min_order_count'] = (int) $filter_data['min_order_count'];
        }
        if(!empty($filter_data['avg_order_value'])) {
          $customer_filters['avg_order_value'] = (int) $filter_data['avg_order_value'];
        }
        if(!empty($filter_data['skip_master_id'])) {
          $customer_filters['skip_master_id'] = $filter_data['skip_master_id'];
        }
        if(!empty($filter_data['consider_master_id'])) {
          $customer_filters['consider_master_id'] = $filter_data['consider_master_id'];
        }
        if(!empty($filter_data['customer_type'])) {
          $customer_types = explode(',', $filter_data['customer_type']);
        if(in_array('has_gst', $customer_types)){
          $customer_filters['has_gst'] = 1;
        }
        if(in_array('has_not_gst', $customer_types)){
          $customer_filters['has_not_gst'] = 1;
        }
        if(in_array('has_fashcart', $customer_types)){
          $customer_filters['has_fashcart'] = 1;
        }
        if(in_array('has_not_fashcart', $customer_types)){
          $customer_filters['has_not_fashcart'] = 1;
        }
        if(in_array('has_app', $customer_types)){
          $customer_filters['has_app'] = 1;
        }
        if(in_array('has_not_app', $customer_types)){
          $customer_filters['has_not_app'] = 1;
        }
        if(in_array('has_wsb_credit', $customer_types)){
          $customer_filters['has_wsb_credit'] = 1;
        }
        if(in_array('has_not_wsb_credit', $customer_types)){
          $customer_filters['has_not_wsb_credit'] = 1;
        }
        if(in_array('has_neogrowth_credit', $customer_types)){
          $customer_filters['has_neogrowth_credit'] = 1;
        }
        if(in_array('has_not_neogrowth_credit', $customer_types)){
          $customer_filters['has_not_neogrowth_credit'] = 1;
        }
        if(in_array('seller', $customer_types)){
          $customer_filters['seller'] = 1;
        }
        if(in_array('has_membership_atleast_once', $customer_types)){
          $customer_filters['has_membership_atleast_once'] = 1;
        }
        if(in_array('has_cod_security', $customer_types)){
          $customer_filters['has_cod_security'] = 1;
        }
        if(in_array('has_not_cod_security', $customer_types)){
          $customer_filters['has_not_cod_security'] = 1;
        }
        if(in_array('is_self_ordering_customer', $customer_types)){
          $customer_filters['is_self_ordering_customer'] = 1;
        }
      }

        try {
            $results = $this->model_report_cohort->getCohortData(
                                                             $cohort_type,
                                                             $x_axis_month_interval,
                                                             $y_axis,
                                                             $order_filters,
                                                             $customer_filters
                                                            );  
            if(!empty($results)) 
            {
              $response['success'] = 1;
              $this->saveCohortReport($results);
              $response['show_data'] = $this->getShowData($results, $cohort_type);
              if($filter_data['action'] == 'download') {
                $response['download'] = 1; 
              }
            }else{
              $response['show_data'] = 'No data found!!';
            }

         }catch(Exception $e) {

            $response['error'] = '<p style="color:red">' . $e->getMessage() . '</p>';
         }
        
      }else{

        $response['error'] = 'Filter data not found!!';
      }

     echo json_encode($response); 
  }

  public function saveCohortReport($data)
  {
    if(!empty($data)) {

     $columns = array_keys($data[0]);

     $file = DIR_UPLOAD . 'cohort_report.csv';
     
     if(file_exists($file)) {
        chmod($file, 0777);
     }

      $file = fopen($file, 'w');

      // create csv file headers
      fputcsv($file, $columns);

        // output each row of the data
        foreach ($data as $row)
        {
          $row_data = $row;

          fputcsv($file, $row_data);
        }
    }
  }
  public function downloadCohortCSV()
  {
      $download_file = $this->request->get['download_file'] ?? '';
      $file = DIR_UPLOAD . $download_file . '.csv';
      if(!empty($file) && file_exists($file)) 
      {
        header('Content-disposition: attachment; filename=' . basename($file));
        header('Expires: 0');
        header('Cache-Control: no-cache');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit();
      }else{
        echo 'Download file not found!!';
      }
  }

  public function getPercentageClsss($percentage)
  {
    $css_class = '';
    if($percentage <= 25) {
      $css_class = "style='background-color:#f48fb1; color:black;'";
    }else if($percentage > 25 && $percentage <= 50) {
      $css_class = "style='background-color:#fff59d; color:black;'";
    }else if($percentage > 50 && $percentage <= 75) {
      $css_class = "style='background-color:#81d4fa; color:black;'";
    }else if($percentage > 75){
      $css_class = "style='background-color:#a5d6a7; color:black;'";
    }
    return $css_class;
  }
  public function getShowData($results, $cohort_type)
  {

    $columns = array_keys($results[0]);
    $total_row = $results[0];

  $html ='';  
   if($cohort_type == 'master_id')
    {
      $html = '<table width="250" style="font-size:11px; margin-bottom:10px;">';   
        $html .= '<tr>';
          $html .= '<th style="text-align:center;">0-25%</th>';
          $html .= '<th style="text-align:center;">26-50%</th>';
          $html .= '<th style="text-align:center;">51-75%</th>';
          $html .= '<th style="text-align:center;">76-100%</th>';
        $html .= '</tr>';
        $html .= '<tr>';
          $html .= '<td style="background-color:#f48fb1; color:black; width:25%"></td>';
          $html .= '<td style="background-color:#fff59d; color:black; width:25%"></td>';
          $html .= '<td style="background-color:#81d4fa; color:black; black:25%"></td>';
          $html .= '<td style="background-color:#a5d6a7; color:black; width:25%"></td>';
        $html .= '</tr>';
      $html .= '</table>';  
    }

    $html .= '<table width="100%" style="font-size:11px;">';
    $html .= '<tr>';
    foreach ($columns as $header) {
      if($header == 'Acq. Month') {
        $html .= '<th nowrap="nowrap">'.str_replace('_',' ',$header).' </th>';
      }else{
        $html .= '<th>'.str_replace('_',' ',$header).'</th>';
      }
    }
    //generate total row
    $counter=0;  
    foreach ($results as $key => $value) {
       foreach ($value as $label => $data) {
         if($label=='Acq. Month') {
            $total_row[$label] = 'Total';
         }else{
          if($counter){
            $total_row[$label] = (float)$total_row[$label] + (float)$data ; 
          }
         }
       }
      $counter++;
    } 

    $html .= '</tr>';
    $first_column = true;
    $style = '';
    $download_link = '';
    foreach ($results as $key => $data) {
      $html .= '<tr>';
        $acq_mid = 0;
        foreach ($data as $label => $value) {
          
          if($first_column) {
            if($cohort_type == 'master_id'){
              $download_link = '<a href="javascript:void(0)" id="'.$value.'" data-acq-month="'.$value.'" class="download-acq-month-csv" title="Download csv for customers in '.$value.'"><i class="fa fa-arrow-circle-o-down" style="font-size:15px;"></i></a>';
            }
            $html .= '<td nowrap="nowrap">'.$value.'&nbsp;&nbsp;'.$download_link.'</td>';
          }else{
            if($cohort_type == 'master_id') {
              if($label == 'Acq. MIDs'){
                $acq_mid = $value;
                $html .= '<td>'.$value.' </td>';
              }else{
                $percentage = round(((int)$value / $acq_mid) * 100);
                $style = $this->getPercentageClsss($percentage);
                if($value == ''){
                  $html .= '<td>'.$value.'</td>';  
                }else{
                  $html .= '<td '.$style.' title="'.$percentage.'%">'.$value.'</td>';
                }
              }
            }else{
              $html .= '<td>'.$value.'</td>';
            }
          }
          $first_column = false;
        }
        $first_column = true;
      $html .= '</tr>';
    }
    /*add Total Row*/
    if(!empty($total_row)) {
      $html .= '<tr style="background-color:#1e91cf; color:white;">';
      foreach ($total_row as $key => $value) {
          $html .= '<td><b>'.$value.'</b></td>'; 
      }
      $html .= '</tr>';
    }
     $html .= '</table>';
     return $html;
  }
  
  public function getOrderTypes(array $data):array {
    $order_types =  array(
                        'failed_order'    => $data['text_failed_order'],
                        'non_INR'         => $data['text_non_inr'],
                        'franchise'       => $data['text_franchise'],
                        'shipping_zone_id'=> $data['text_shipping_zone_id'],
                        'payment_method'  => array(
                                                    'containing_payment_code'   => $data['text_containing_payment_code'],
                                                    'excluding_payment_code'=> $data['text_excluding_payment_code'],
                                                  ),
                        'category_order'  => array(
                                                  'exclusive_category_order'  => $data['text_exclusive_category_order'],
                                                  'contains_category_order'   => $data['text_contains_category_order']
                                                ),
                        
                    );
    return $order_types;
  }
  public function getCustomerType(array $data):array
  {
    return array(
              'min_order_count'       => $data['text_min_order_count'],
              'avg_order_value'       => $data['text_avg_order_value'],
              'gst'                   => array(
                                          'has_gst'     => $data['text_has_gst'],
                                          'has_not_gst' => $data['text_has_not_gst'],
                                        ),
              'fashcart'              => array(
                                          'has_fashcart'          => $data['text_has_fashcart'],
                                          'has_not_fashcart'      => $data['text_has_not_fashcart'],
                                        ),
                'app'                 => array(
                                          'has_app'               => $data['text_has_app'],
                                          'has_not_app'           => $data['text_has_not_app'],
                                        ),          
                'wsb_credit'          => array(
                                          'has_wsb_credit'        => $data['text_has_wsb_credit'],
                                          'has_not_wsb_credit'    => $data['text_has_not_wsb_credit'],
                                        ),
              
                'neogrowth_credit'    => array(
                                          'has_neogrowth_credit'      => $data['text_has_neogrowth_credit'],
                                          'has_not_neogrowth_credit'  => $data['text_has_not_neogrowth_credit'],
                                        ),
              
              'master_id'             => array(
                                          'skip_master_id'            => $data['text_skip_master_id'],
                                          'consider_master_id'        => $data['text_consider_master_id'],
                                        ),
              'cod_security'          => array(
                                          'has_cod_security'            => $data['text_has_cod_security'],
                                          'has_not_cod_security'        => $data['text_has_not_cod_security'],
                                        ),

              'seller'                      => $data['text_seller'],
              'has_membership_atleast_once' => $data['text_has_membership_atleast_once'],
              'is_self_ordering_customer'   => $data['text_is_self_ordering_customer']
          );
  }
  public function getCohortTypes(array $data): array
  {
    return array('master_id'    => $data['text_master_id'],
                 'order_count'  => $data['text_order_count'], 
                 'order_total'  => $data['text_order_total']
               );
  }
  public function getOrderTypeFilterDropDown()
  {
    $filter_type = $this->request->get['filter_type'] ?? '';
    $data = array();
    if(!empty($filter_type) && in_array($filter_type, array('containing_payment_code','excluding_payment_code'))) {
      
      $this->load->model('sale/order');
      $data = $this->model_sale_order->getAllPaymentCodes();

    }else if(!empty($filter_type) && $filter_type == 'shipping_zone_id'){

      $this->load->model('localisation/country');
      $countries = $this->model_localisation_country->getCountries();
      if(!empty($countries)) {
        foreach ($countries as $key => $value) {
          $data[$value['country_id']] = $value['name'];
        }
      }
    }else if(!empty($filter_type) && in_array($filter_type, array('exclusive_category_order','contains_category_order'))) {
     
      $this->load->model('catalog/category');
      $data = array();
      $categories_1 = $this->model_catalog_category->getCategoriesByParent(0);
      foreach ($categories_1 as $category_1) {
        $level_2_data = array();

        $categories_2 = $this->model_catalog_category->getCategoriesByParent($category_1['category_id']);

        foreach ($categories_2 as $category_2) {
          $level_3_data = array();

          $categories_3 = $this->model_catalog_category->getCategoriesByParent($category_2['category_id']);

          foreach ($categories_3 as $category_3) {
            $level_3_data[] = array(
                'category_id' => $category_3['category_id'],
                'name'        => $category_3['name'],
            );
          }

          $level_2_data[] = array(
              'category_id' => $category_2['category_id'],
              'name'        => $category_2['name'],
              'children'    => $level_3_data
          );
        }

        $data[] = array(
            'category_id' => $category_1['category_id'],
            'name'        => $category_1['name'],
            'children'    => $level_2_data
        );
      }
    }
    echo json_encode($data);
  }

  protected function resetFilterTypes($filter_types)
  {
     /* reset filter type flags */
     $filter_types = explode(",",$filter_types);
     $filter_types = implode(",", array_unique($filter_types));
     return $filter_types;
  }

  protected function getFilters()
  {
      $filter_data = array();
      
      if ( !empty($this->request->get['action']) ) {
          $filter_data['action']  = $this->request->get['action'];
      }

      if ( !empty($this->request->get['cohort_type']) ) {
          $filter_data['cohort_type']  = $this->request->get['cohort_type'];
      }

      if ( !empty($this->request->get['filter_month_interval']) ) {
          $filter_data['filter_month_interval']  = $this->request->get['filter_month_interval'];
      }

      if ( !empty($this->request->get['filter_date_from']) ) {
          $filter_data['filter_date_from']  = $this->request->get['filter_date_from'];
      }

      if ( !empty($this->request->get['filter_date_to']) ) {
          $filter_data['filter_date_to'] = $this->request->get['filter_date_to'];
      }

      if ( !empty($this->request->get['filter_sort']) ) {
          $filter_data['filter_sort'] = $this->request->get['filter_sort'];
      }

      if ( !empty($this->request->get['shipping_zone_id']) ) {
          $filter_data['shipping_zone_id'] = $this->request->get['shipping_zone_id'];
      }

      if ( !empty($this->request->get['min_order_count']) ) {
          $filter_data['min_order_count'] = $this->request->get['min_order_count'];
      }

      if ( !empty($this->request->get['avg_order_value']) ) {
          $filter_data['avg_order_value'] = $this->request->get['avg_order_value'];
      }

      if ( !empty($this->request->get['skip_master_id']) ) {
          $filter_data['skip_master_id'] = $this->request->get['skip_master_id'];
      }

      if ( !empty($this->request->get['order_type']) ) {
          $filter_data['order_type'] = $this->resetFilterTypes($this->request->get['order_type']);
      }

      if ( !empty($this->request->get['containing_payment_code']) ) {
          $filter_data['containing_payment_code'] = $this->resetFilterTypes($this->request->get['containing_payment_code']);
      }

      if ( !empty($this->request->get['excluding_payment_code']) ) {
          $filter_data['excluding_payment_code'] = $this->resetFilterTypes($this->request->get['excluding_payment_code']);
      }

      if ( !empty($this->request->get['contains_category_order_ids']) ) {
          $filter_data['contains_category_order_ids'] = $this->resetFilterTypes($this->request->get['contains_category_order_ids']);
      }

      if ( !empty($this->request->get['exclusive_category_order_ids']) ) {
          $filter_data['exclusive_category_order_ids'] = $this->resetFilterTypes($this->request->get['exclusive_category_order_ids']);
      }

      if ( !empty($this->request->get['customer_type']) ) {
          $filter_data['customer_type'] = $this->resetFilterTypes($this->request->get['customer_type']);
      }
      return $filter_data;
  }

  public function saveAcqMonthCustomersCSV($acq_month, $data)
  {
    if(!empty($data['master_ids'])) 
    {

      $file = DIR_UPLOAD . 'cohort_customers.csv';
     
      if(file_exists($file)) {
        chmod($file, 0777);
      }

      $file = fopen($file, 'w');

      $first_row = array('Acquisition Month',$acq_month);

      fputcsv($file, $first_row);

      $second_row_heading = array('Master Ids Not Active Presenting');

      // create csv file headers
      fputcsv($file, $second_row_heading);

      // output each row of the data
      foreach ($data['master_ids'] as $row)
      {
        fputcsv($file, array($row));
      }

      return $file;
    }
  }

  public function downloadAcqMonthCustomersCSV()
  {
    $this->load->model('report/cohort');
    
    $filter_data = $this->getFilters();

      if(!empty($filter_data)) {
       
        $cohort_type           = $filter_data['cohort_type'] ?? '';
        $month_interval        = $filter_data['filter_month_interval'] ?? 1;
        $x_axis_month_interval = array('month_interval' => $month_interval);
        $y_axis                = array(
                                          'date_start' => $filter_data['filter_date_from'] ?? '',
                                          'date_end'   => $filter_data['filter_date_to'] ?? '',
                                        );
        
        $order_filters         = array('failed_order' => 0, 
                                       'non_INR'      => 0, 
                                       'franchise'    => 0, 
                                       'containing_payment_code' => '',
                                       'excluding_payment_code' => '',
                                       'shipping_zone_id' => ''
                                      ); 
        if(!empty($filter_data['order_type'])) {
            $order_types = explode(',', $filter_data['order_type']);
            $order_filters['failed_order']  =  in_array('failed_order',$order_types) ? 1 : 0;
            $order_filters['non_INR']       =  in_array('non_INR',$order_types) ? 1 : 0;
            $order_filters['franchise']     =  in_array('franchise',$order_types) ? 1 : 0;
        }
        if(!empty($filter_data['containing_payment_code'])) {
            $payment_codes = explode(',', $filter_data['containing_payment_code']);
            $order_filters['containing_payment_code'] = $payment_codes;
        }
        if(!empty($filter_data['excluding_payment_code'])) {
            $payment_codes = explode(',', $filter_data['excluding_payment_code']);
            $order_filters['excluding_payment_code'] = $payment_codes;
        }
        if(!empty($filter_data['shipping_zone_id'])){
          $shipping_zone_ids = explode(',', $filter_data['shipping_zone_id']);
          $order_filters['shipping_zone_id'] = $shipping_zone_ids;
        }
        $customer_filters = array();
        if(!empty($filter_data['min_order_count'])) {
          $customer_filters['min_order_count'] = (int) $filter_data['min_order_count'];
        }
        if(!empty($filter_data['avg_order_value'])) {
          $customer_filters['avg_order_value'] = (int) $filter_data['avg_order_value'];
        }
        if(!empty($filter_data['skip_master_id'])) {
          $customer_filters['skip_master_id'] = $filter_data['skip_master_id'];
        }
        if(!empty($filter_data['customer_type'])) {
          $customer_types = explode(',', $filter_data['customer_type']);
          if(in_array('has_gst', $customer_types)){
            $customer_filters['has_gst'] = 1;
          }
          if(in_array('has_not_gst', $customer_types)){
            $customer_filters['has_not_gst'] = 1;
          }
          if(in_array('has_fashcart', $customer_types)){
            $customer_filters['has_fashcart'] = 1;
          }
          if(in_array('has_not_fashcart', $customer_types)){
            $customer_filters['has_not_fashcart'] = 1;
          }
          if(in_array('has_app', $customer_types)){
            $customer_filters['has_app'] = 1;
          }
          if(in_array('has_not_app', $customer_types)){
            $customer_filters['has_not_app'] = 1;
          }
          if(in_array('has_wsb_credit', $customer_types)){
            $customer_filters['has_wsb_credit'] = 1;
          }
          if(in_array('has_not_wsb_credit', $customer_types)){
            $customer_filters['has_not_wsb_credit'] = 1;
          }
          if(in_array('has_neogrowth_credit', $customer_types)){
            $customer_filters['has_neogrowth_credit'] = 1;
          }
          if(in_array('has_not_neogrowth_credit', $customer_types)){
            $customer_filters['has_not_neogrowth_credit'] = 1;
          }
          if(in_array('seller', $customer_types)){
            $customer_filters['seller'] = 1;
          }
          if(in_array('has_membership_atleast_once', $customer_types)){
            $customer_filters['has_membership_atleast_once'] = 1;
          }
          if(in_array('has_cod_security', $customer_types)){
            $customer_filters['has_cod_security'] = 1;
          }
          if(in_array('has_not_cod_security', $customer_types)){
            $customer_filters['has_not_cod_security'] = 1;
          }
          if(in_array('is_self_ordering_customer', $customer_types)){
            $customer_filters['is_self_ordering_customer'] = 1;
          }
        }

        $acq_month = $this->request->get['acq_month'] ?? '';
        
        try {
            $results = $this->model_report_cohort->getCohortData(
                                                               $cohort_type,
                                                               $x_axis_month_interval,
                                                               $y_axis,
                                                               $order_filters,
                                                               $customer_filters,
                                                               $acq_month
                                                              );  
            if(!empty($results)) 
            {
              $data['master_ids'] = array(1,2,3,4,5,6,7,8,9);
              $this->saveAcqMonthCustomersCSV($acq_month, $data);
              $response['success'] = 1;
            }else{
              $response['error'] = 'No data found!!';
            }

         }catch(Exception $e) {

            $response['error'] = '<p style="color:red">' . $e->getMessage() . '</p>';
         }

     }else{

        $response['error'] = 'Filter data not found!!';
      }

     echo json_encode($response); 

  }//end of method



}
