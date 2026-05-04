<?php
class ControllerReportCreditApplications extends Controller {
  
  //private $_document_status = array('no_status','document_awaited','under_process','rejected','customer_not_interested','approved_but_agreement_pending','approved_without_bank_statement','document_in_transit','limit_issue','approved_document_received'); 
  private $_document_status = array();
  private $_list_page_url;

  public function __construct($registry) {
    
    parent::__construct($registry);

    $this->_list_page_url = $this->url->link('sale/customer_credit_application', 'token=' . $this->session->data['token'], 'SSL');
  
    $this->setDocumentStatus();
  }

  private function setDocumentStatus()
  {
    $this->_document_status = $this->db->getEnumValues('oc_credit_application_status_remarks','status');
    array_unshift($this->_document_status, 'no_status');
  }

  public function index()
  {
      $this->load->model('report/credit_applications');

      $data = array(); // Initializing the data array to be passed on to template files
      // Autoloading the lanugage
      $this->load->autoLoadLanguage('report/credit_applications', $data);

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
          'href' => $this->url->link('report/credit_applications', 'token=' . $this->session->data['token'] . $url, 'SSL')
      );

      $data['credit_activated_cases']     = $this->getCreditActivatedCases($data);

      $data['credit_approved_cases']      = $this->getCreditApprovedCases($data);
      
      $data['application_records_data']   = $this->getApplicationRecords($data);

      $data['under_process_applications'] = $this->getUnderProcessCases($data);

      $data['image_path'] = HTTPS_CATALOG.'khufiya_vibhag/view/image/ajax-loader.gif';

      $data['header'] = $this->load->controller('common/header');
      $data['column_left'] = $this->load->controller('common/column_left');
      $data['footer'] = $this->load->controller('common/footer');

      $this->response->setOutput($this->load->view('report/credit_applications.tpl', $data));
  }

   protected function getCreditActivatedCases(&$data)
   {
      $data['activate_cases'] = $this->model_report_credit_applications->getActivateCasesReportData();
      return $this->htmlView('activate_cases', $data); 
   }

   protected function getCreditApprovedCases(&$data)
   {
      $data['approved_cases'] = $this->model_report_credit_applications->getCreditApprovedCasesData();
      return $this->htmlView('approved_cases', $data); 
   }

   protected function getApplicationRecords(&$data)
   {
     $document_status = $data['document_status'] ?? '';
     $data['application_records'] = $this->model_report_credit_applications->getApplicationRecordsData($document_status);
     $data['show_document_status_filter'] = 1;
     return $this->htmlView('application_records', $data);
   }

   protected function getUnderProcessCases(&$data)
   {
     $data['under_process_applications'] = $this->model_report_credit_applications->getUnderProcessApplicationsData();
     $data['show_document_status_filter'] = 0;
     $data['is_link'] = 1;
     return $this->htmlView('under_process_applications', $data);
   }

   public function reload_credit_data() 
   {
     $section = $this->request->get['section']; 
     $data = array();
     $this->load->model('report/credit_applications');
     $this->load->autoLoadLanguage('report/credit_applications', $data);

     $data['section'] = $this->request->get['section'] ?? '';
     $data['document_status'] = $this->request->get['document_status'] ?? '';

     $response = array();
     
     if(in_array($section, array('activated_cases_data',
                                 'approved_cases_data',
                                 'application_records_data',
                                 'under_process_applications'
                                )
                )
        )
     {
        $response['success'] = $this->getSectionData($section, $data);
     }else{
        $response['error'] = '<p style="text-align:center; color:red">Credit data not found</p>';
     }

     echo json_encode($response); 

   }

   protected function getSectionData(string $section, array &$data)
   {
        switch ($section) {
          case 'activated_cases_data'      : return $this->getCreditActivatedCases($data); break;
          case 'approved_cases_data'       : return $this->getCreditApprovedCases($data); break;
          case 'application_records_data'  : return $this->getApplicationRecords($data); break;
          case 'under_process_applications': return $this->getUnderProcessCases($data); break;
        }
   }

   protected function htmlView(string $row_label, array &$data)
   {
      $html = "";
      if(!empty($data[$row_label]))
      {
        $html .= "<table width='100%'>";
        $html .= $this->getMISHeadings($row_label, $data);
        foreach ($data[$row_label] as $key => $value) {
          $html .= "<tr>";
            $html .= "<td>".( $data['text_'.$key] ?? $key )."</td>";
            foreach ($value as $row_key => $row_val) {
                $row_val = ( $row_val ?? 0 );
                $html .= "<td>".$this->getColumnValue($data, $row_key, $row_val)."</td>";  
            }
          $html .= "</tr>";

        }
        $html .= "</table>";
      }
      return $html;
   }

   protected function getColumnValue(array &$data, string $column_heading, string $column_val)
   {
      if(!empty($data['is_link']) && $column_val > 0 ) {
          $credit_application_ids = $this->model_report_credit_applications->getUnderProcessApplicationsToProcess($column_heading);
          $link = $this->_list_page_url.'&filter_application_id='.$credit_application_ids;
          return '<a href="'.$link.'" target="_blank" title="Click To View Credit Applications">'.$column_val.'</a>';
      }else{
        return $column_val;
      }
   }

   protected function getMISHeadings(string $row_label, array &$data)
   {
      $columns = array();
      if(!empty($data[$row_label])) 
      {
          $columns = array_keys( array_values( array_slice($data[$row_label], 0,1) )[0] );
      
          $row = "<tr>";
          if(!empty($data['show_document_status_filter'])) {
            $row .= "<th style='width: 20%'>".$this->getDocumentStatusFilter($data)."</th>";
          }else{
            $row .= "<th style='width: 20%'>&nbsp;</th>";  
          }
          if(!empty($columns)) {
            foreach ($columns as $heading) {
              $val = strtolower(str_replace("-", "", $heading));
              $row .= "<td style='width: 11%'>".(  $data['text_'.$val]  ?? ucwords($val) )."</td>";
            }
          }
        $row .= "<tr>";
        return $row;
      }
      
   }

   protected function getDocumentStatusFilter(array &$data)
   {
      $document_status = $this->request->get['document_status'] ?? '';
      $document_filter = "<select class='form-control document-status-filter' document-status-filter=''>";
        $document_filter .= "<option value=''>--Select--</option>";  
        foreach ($this->_document_status as $status) {
          $selected = ( $status == $document_status ) ? 'selected="selected"' : '';
          $document_filter .= "<option value='".$status."' ".$selected.">".ucwords(str_replace("_"," ",$status))."</option>";  
        }
      $document_filter .= '</select>';      
      return $document_filter;
   }

}
