<?php
class ControllerSaleReferral extends Controller {

	private $_is_admin                          = false;
	private $_action_emailer                    = null;
	private $error                              = '';

	public function index() 
	{
        $data = array(); 
        $this->load->autoLoadLanguage('sale/referral', $data);

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		 $this->load->model('affiliate/affiliate', 'frontend');

		$url = '';

		if (isset($this->request->get['filter_date_added_from'])) 
		{
			$url .= '&filter_date_added_from=' . $this->request->get['filter_date_added_from'];
		    $data['filter_date_added_from'] = $this->request->get['filter_date_added_from'];
		} else {
			$data['filter_date_added_from'] = null;
		}

		if (isset($this->request->get['filter_date_added_to'])) {
			$url .= '&filter_date_added_to=' . $this->request->get['filter_date_added_to'];
		    $data['filter_date_added_to'] = $this->request->get['filter_date_added_to'];
		} else {
			$data['filter_date_added_to'] = null;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$filter_data = array(
			'filter_date_added_from' =>  $data['filter_date_added_from'],
			'filter_date_added_to' =>  $data['filter_date_added_to'],
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

        $data['total_referral'] = $this->frontend_model_affiliate_affiliate->getTotalReferralCodes($filter_data);

		$data['total_signup'] = $this->frontend_model_affiliate_affiliate->getTotalSignupsAffiliateCoupons($filter_data);

		$data['total_orders'] = $this->frontend_model_affiliate_affiliate->getTotalOrdersAffiliateCoupons($filter_data);


		$affiliate_data = $this->frontend_model_affiliate_affiliate->getAffiliateList($filter_data);
        
        $data['affiliate_total'] = $affiliate_data[0];
		$data['affiliate_list'] = $affiliate_data[1];
        
        $i=0; 

        $affiliate_ids = array();  
		foreach($data['affiliate_list'] as $affiliate_list)
		{
          $data['affiliate_list'][$i]['ordering_customer_url'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'].'&filter_referral_code='.$affiliate_list['referral_code'].'&ordering_customer=1', 'SSL');

           $data['affiliate_list'][$i]['customer_url'] = $this->url->link('sale/customer', 'token=' . $this->session->data['token'].'&filter_referral_code='.$affiliate_list['referral_code'].'&ordering_customer=0', 'SSL');

          $data['affiliate_list'][$i]['order_url'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'].'&filter_referral_code='.$affiliate_list['referral_code'], 'SSL');

          $affiliate_ids[] = $affiliate_list['affiliate_id'];
          $i++;
		}
       
       $data['affiliate_signup_data'] = $this->frontend_model_affiliate_affiliate->getAffiliateSignups($affiliate_ids);

		$pagination = new Pagination();
		$pagination->total = $data['affiliate_total'];
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('sale/referral/index', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
		$data['pagination'] = $pagination->render();


		$data['results'] = sprintf($data['text_pagination'], ($data['affiliate_total']) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($data['affiliate_total'] - $this->config->get('config_limit_admin'))) ? $data['affiliate_total'] : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $data['affiliate_total'], ceil($data['affiliate_total'] / $this->config->get('config_limit_admin')));
        
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['token'] =$this->session->data['token'];
		$this->response->setOutput($this->load->view('sale/referral_list.tpl', $data));
	}

     //get affiliate commission amount
     public function getCommission()
    {
    	$referral_details = array();

    	if (!empty($this->request->get['affiliate_id'])) 
	  	{
          $affiliate_id = $this->request->get['affiliate_id'];
	    }
	    else
	    {
         $affiliate_id = 0;
	    }

	    $this->load->model('affiliate/affiliate', 'frontend');	

        // get total tentative order amoount
	    $tentative_commission = $this->frontend_model_affiliate_affiliate->getAffiliateTentativeCommissionAmount( (int) $affiliate_id );

        // get total pending commission amoount
	    $referral_details['pending_commission_amount'] = $this->frontend_model_affiliate_affiliate->getAffiliateCurrentCommissionAmount( (int) $affiliate_id, 'PENDING' );

        // get total paid commission amoount
    	$referral_details['paid_commission_amount'] = $this->frontend_model_affiliate_affiliate->getAffiliateCurrentCommissionAmount( (int) $affiliate_id, 'PAID' );


    	$referral_details['total_commission_amount'] = $referral_details['pending_commission_amount']+$referral_details['paid_commission_amount'];


         // set currency format
    	$referral_details['total_commission_amount'] = $this->currency->format($referral_details['total_commission_amount']);

    	$referral_details['pending_commission_amount'] = $this->currency->format($referral_details['pending_commission_amount']);

    	$referral_details['paid_commission_amount'] = $this->currency->format($referral_details['paid_commission_amount']);

    	$referral_details['tentative_order_amount'] = $this->currency->format($tentative_commission['net_sale']);

    	$referral_details['tentative_order_commission'] = $this->currency->format($tentative_commission['commission']);

    	echo json_encode($referral_details);
        exit;

    }

     //get affiliate commission rates
    public function getCommissionRates()
    {
         $result = '';
         $result .= '<tr><td>Default</td><td>';
         $result .= REFERRAL_COMMISSION.'%';
         $result .= '</td><td></td><td></td></tr>';

        if(!empty($this->request->get['affiliate_id'])) 
		{
           $affiliate_id = $this->request->get['affiliate_id'];
           
           $this->load->model('affiliate/affiliate', 'frontend');

           // get commission rates
	       $commission_rates = $this->frontend_model_affiliate_affiliate->getAffiliateCommissionRates( (int) $affiliate_id );

	        foreach($commission_rates as $rates)
                {
                  $result .= '<tr id="commission_rate_'.$rates['commission_rate_id'].'"><td>';
                  $result .= $rates['order_count_from'];
                  $result .= ' to ';
                  if($rates['order_count_to'] == '-1')
                  {
                    $result .= 'All'; 
                  }
                  else
                  {
                    $result .= $rates['order_count_to'];
                  } 
                  $result .= '</td>';
                  $result .= '<td>'.$rates['rate'].'%'.'</td>';
                  $result .= '<td>'.$rates['created'].'</td>';
                  $result .= '<td><i class="delete_rate fa fa-trash" data-id="'.$rates['commission_rate_id'].'"></i> ';
                  $result .= '&nbsp; 
                            <i class="edit_rate fa fa-edit" data-id="'.$rates['commission_rate_id'].'" data-order_from="'.$rates['order_count_from'].'" data-order_to="'.$rates['order_count_to'].'" data-rate="'.$rates['rate'].'"></i></td></tr>';
                  $result .= '</td></tr>';          
                                      
                }
	    }

	    echo $result;
        exit;

    }

     //save affiliate commission rates
    public function saveCommissionRates()
    {
       $data = array();

      if($this->validateCommissionRate())
        {
    		  $this->load->model('affiliate/affiliate', 'frontend');
          if(empty($this->request->post['commission_rate_id']))
          {
            $response = $this->frontend_model_affiliate_affiliate->saveCommissionRates($this->request->post);
          }
          else
          {
            $response = $this->frontend_model_affiliate_affiliate->updateCommissionRates($this->request->post);
          }
    	  	
      	}


    	if($response){
    			$data['status'] = 1;
    			$data['message'] = "Commission Rate Added Successfully";
    		}else{
    			$data['status'] = 0;
    			$data['message'] = $this->error;
    		}
    	echo json_encode($data);
		  die; 
    }

    //delete affiliate commission rates
    public function deleteCommissionRate()
    {
       $data = array();

      if(!empty($this->request->get['commission_rate_id']))
        {
          $this->load->model('affiliate/affiliate', 'frontend');
          $response = $this->frontend_model_affiliate_affiliate->deleteCommissionRate($this->request->get['commission_rate_id']);
        }

      if($response){
          $data['status'] = 1;
          $data['message'] = "Commission Rate Delete Successfully";
        }else{
          $data['status'] = 0;
          $data['message'] = "Incorrect Commission Rate Id";
        }
      echo json_encode($data);
      die; 
    }



     //get last referral order
    public function getLastReferralOrder()
    {
      $result = array();
      if(!empty($this->request->get['affiliate_id'])) 
       {
           $affiliate_id = $this->request->get['affiliate_id'];
           
           $this->load->model('affiliate/affiliate', 'frontend');

         $result = $this->frontend_model_affiliate_affiliate->getAffiliateLastOrder( (int) $affiliate_id );

        }

      echo json_encode($result);
        exit;
    }


     //get referral customers
    public function getReferralCustomers()
    {
      $result = array();
      if(!empty($this->request->get['affiliate_id'])) 
       {
           $affiliate_id = $this->request->get['affiliate_id'];
           
           $this->load->model('affiliate/affiliate', 'frontend');

          $result = $this->frontend_model_affiliate_affiliate->getReferralCustomers( (int) $affiliate_id );

        }

      echo json_encode($result);
        exit;
    }

     //get referral customers
    public function removeReferral()
    {
      $result = false;
      if(!empty($this->request->get['customer_id'])) 
       {
           $customer_id = $this->request->get['customer_id'];
           
           $this->load->model('affiliate/affiliate', 'frontend');

          $result = $this->frontend_model_affiliate_affiliate->removeReferral( (int) $customer_id );

        }

      echo $result;
        exit;
    }

    public function validateCommissionRate()
    {
      $this->load->autoLoadLanguage('sale/referral', $data);

    	if(empty($this->request->post['affiliate_id']))
    	{
    	  $this->error = $this->language->get('error_affiliate_id');
    	  return false;	
    	}

    	else if(empty($this->request->post['order_count_from']))
    	{
    	  $this->error = $this->language->get('error_order_count');	
    	  return false;	
    	}

    	else if(empty($this->request->post['order_count_to']))
    	{
    	  $this->error = $this->language->get('error_order_count');	
    	  return false;	
    	}

    	else if($this->request->post['order_count_from'] > $this->request->post['order_count_to'] && $this->request->post['order_count_to'] != '-1')
    	{
    	  $this->error = $this->language->get('error_order_count_invalid');		
    	  return false;	
    	}
    	else if(empty($this->request->post['rate']))
    	{
    	  $this->error = $this->language->get('error_rate');    
    	  return false;	
    	}
    	else
    	{
    	  $this->error = '';
    	  return true;
    	}
    }
    


}
