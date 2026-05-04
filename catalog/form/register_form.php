<?php require_once( 'wsbform.php' );
      require_once( 'validation.php' );
      require_once( DIR_SYSTEM.'library/language.php' );

class RegisterForm extends wsbform
{

	protected $result                   =  array();
	protected $config                   = array();
	protected $validation_result        = array();

	/**
	 * Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($config = NULL) {
		if ( ! empty($config)) {
			$this->initialize($config);
		}

	}

	// ------------------------------------------------------------------------
	/**
	 * Initialize library.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function initialize($config) {
		$this->config = $config;

	}


   /*************Create login Form **********/
  public function login_form($action='', $form_name='login_form')
  {
      $language = new Language();
      $language->load('account/login');

      $this->result['form_start']  = $this->form_create($form_name, $action, array('id'=>$form_name));

      $this->result['password']  = $this->password('password', '', array('class'=>'password_box', 'label'=>false, 'placeholder'=>$language->get('entry_password'), 'alt'=>$language->get('entry_password'), 'autocomplete'=>'new-password'));

      $this->result['redirect_cart'] = $this->hidden('redirect_cart', 0, array('label'=>false));

      $this->result['submit']  = $this->submit('login', $language->get('button_continue'), array('class'=>'btn deliver_btn pull-right', 'id'=>'login'));

      $this->result['form_end'] = $this->form_end();

      return $this->result;

  }


  /*************Create Register Form **********/
  public function register_form($action='', $form_name='register_form')
  {
      $language = new Language();
      $language->load('account/login');

      $this->result['form_start']  = $this->form_create($form_name, $action, array('id'=>$form_name));

      if(isset($this->config['CustomersType']))
      {
        foreach($this->config['CustomersType'] as $CustomersType)
         {
      $this->result['customer_type_id'][]  = $this->checkbox('customer_type_id[]', $CustomersType['customer_type_id'], array('label'=>false), '', $CustomersType['type']);
         }
     }

      $zones_array = array(0=>$language->get('entry_zone'));
      $country_array = array(0=>$language->get('entry_country'));

      if(isset($this->config['country_data']))
      {
        foreach($this->config['country_data'] as $country)
         {
           $country_array[$country['country_id']]  = $country['name'];
         }
      }

      $this->result['reg_telephone']  = $this->input('reg_telephone', '', array('label'=>false, 'class'=>'password_box', 'placeholder'=>$language->get('entry_telephone'), 'alt'=>$language->get('entry_telephone'), 'id'=>'reg_telephone_signup', 'maxlength'=>10));

      $this->result['reg_email']  = $this->input('reg_email', '', array('label'=>false, 'class'=>'password_box', 'placeholder'=>$language->get('entry_email_address'), 'alt'=>$language->get('entry_email_address'), 'autocomplete'=>'new-password'));

      $this->result['password']  = $this->input('password', '', array('label'=>false, 'class'=>'password_box', 'placeholder'=>$language->get('entry_password'), 'alt'=>$language->get('entry_password'), 'id'=>'register_password', 'autocomplete'=>'new-password'));

      $this->result['name']  = $this->input('name', '', array('label'=>false, 'class'=>'password_box capitalize', 'placeholder'=>$language->get('entry_name'), 'alt'=>$language->get('entry_name')));

      $this->result['company']  = $this->input('company', '', array('label'=>false, 'class'=>'password_box', 'placeholder'=>$language->get('entry_company'), 'alt'=>$language->get('entry_company')));

      $this->result['gst_uin_number']  = $this->checkbox('gst_uin_number', 1, array('label'=>false, 'id'=>'gst_uin_number'), '', $language->get('entry_check_gst'));

      $this->result['gst_number']  = $this->input('gst_number', '', array('label'=>false, 'class'=>'password_box uppercase', 'placeholder'=>$language->get('entry_gst_number'), 'id'=>'gst_number', 'onkeyup'=>"$(this).val($(this).val().toUpperCase())", 'alt'=>$language->get('entry_gst_number')));

      $this->result['register_dealer']  = $this->checkbox('register_dealer', 1, array('label'=>false), '', $language->get('register_dealer'));

      $this->result['vat_number']  = $this->input('vat_number', '', array('label'=>false, 'class'=>'password_box width92', 'placeholder'=>$language->get('entry_vat_number'), 'alt'=>$language->get('entry_vat_number')));

      $this->result['cst_number']  = $this->input('cst_number', '', array('label'=>false, 'class'=>'password_box width92', 'placeholder'=>$language->get('entry_cst_number'), 'alt'=>$language->get('entry_cst_number')));

      $this->result['postcode']  = $this->input('postcode', '', array('label'=>false, 'class'=>'password_box width92', 'placeholder'=>$language->get('entry_postcode'), 'alt'=>$language->get('entry_postcode'), 'id'=>'postcode', 'maxlength'=>10));

      $this->result['city']  = $this->input('city', '', array('label'=>false, 'class'=>'password_box width92', 'placeholder'=>$language->get('entry_city'), 'id'=>'city'));

      $this->result['zone_id']  = $this->select('zone_id', '', $zones_array, array('label'=>false, 'class'=>'password_box width92', 'id'=>'zone_id'));

      $selected = '';
     if(isset($this->config['country_id']) && !empty($this->config['country_id']))
      { $selected = $this->config['country_id']; }

      $this->result['country_id']  = $this->select('country_id', $selected, $country_array, array('label'=>false, 'class'=>'password_box width92', 'id'=>'country_id'));

      $this->result['address_1']  = $this->textarea('address_1', '', array('label'=>false, 'class'=>'password_box', 'placeholder'=>$language->get('entry_address'), 'alt'=>$language->get('entry_address')));

      $this->result['address_2']  = $this->textarea('address_2', '', array('label'=>false, 'class'=>'password_box', 'placeholder'=>$language->get('entry_address_2'), 'alt'=>$language->get('entry_address_2')));

      $this->result['submit']  = $this->submit('register', $language->get('button_continue'), array('class'=>'btn deliver_btn pull-right', 'id'=>'register'));

      $this->result['form_end'] = $this->form_end();

      return $this->result;

  }


  /*************Create Mobile Form for send otp **********/
  public function send_otp_form($action='', $form_name='otp_form')
  {
      $language = new Language();
      $language->load('account/login');
      $customer_mobile = '';
      $country_code = '';
      if(isset($_COOKIE['customer_mobile']) && !empty($_COOKIE['customer_mobile']))
      {
        $customer_mobile = $_COOKIE['customer_mobile'];
      }
      if(isset($_COOKIE['country_code']) && !empty($_COOKIE['country_code']))
      {
        $country_code = $_COOKIE['country_code'];
      }
      $this->result['form_start']  = $this->form_create($form_name, $action, array('id'=>$form_name));
      $this->result['country_code'] = $this->select('country_code', $country_code, array('+91'=>'+91'), array('class'=>'form-control country_code', 'label'=>false));
      $this->result['reg_telephone'] = $this->input('reg_telephone', $customer_mobile, array('class'=>'mobile_input', 'label'=>false, 'placeholder'=>$language->get('entry_telephone'), 'alt'=>$language->get('entry_telephone'), 'id'=>'reg_telephone', 'autocomplete'=>'off'));
     
      $this->result['reg_email']  = $this->input('reg_telephone', $customer_mobile, array('label'=>false, 'class'=>'mobile_input', 'placeholder'=>$language->get('entry_email_address'), 'alt'=>$language->get('entry_email_address'), 'id'=>'reg_telephone', 'autocomplete'=>'off'));
      
      $this->result['submit']  = $this->submit('send_otp', $language->get('button_continue'), array('class'=>'btn deliver_btn pull-right', 'id'=>'send_otp', 'disabled'=>'disabled'));
      $this->result['form_end'] = $this->form_end();
      return $this->result;

  }


  /*************Create Email Form for send otp **********/
  public function send_email_otp_form($action='', $form_name='otp_form')
  {
      $language = new Language();
      $language->load('account/login');
      $this->result['form_start']  = $this->form_create($form_name, $action, array('id'=>$form_name));
      $this->result['reg_telephone'] = $this->input('reg_telephone', '', array('class'=>'mobile_input', 'label'=>false, 'placeholder'=>$language->get('entry_email_address'), 'alt'=>$language->get('entry_email_address'), 'id'=>'reg_telephone', 'autocomplete'=>'off'));
      $this->result['submit']  = $this->submit('send_otp', $language->get('button_continue'), array('class'=>'btn deliver_btn pull-right', 'id'=>'send_otp', 'disabled'=>'disabled'));
      $this->result['form_end'] = $this->form_end();
      return $this->result;

  }


  /*************Create Verify otp form **********/
  public function verify_otp_form($action='', $form_name='verify_otp_form')
  {
      $language = new Language();
      $language->load('account/login');
      $this->result['form_start']  = $this->form_create($form_name, $action, array('id'=>$form_name));

      $this->result['otp'] = $this->input('otp', '', array('class'=>'password_box', 'label'=>false, 'placeholder'=>$language->get('entry_otp'),'maxlength'=>6, 'alt'=>$language->get('OTP')));
      
      $this->result['otp_page']          = $this->hidden('otp_page', 'sign_up', array('label'=>false));
      $this->result['otp_forgot_page']   = $this->hidden('otp_page', 'forgot_password', array('label'=>false));
      $this->result['referrers']         = $this->hidden('referrers', '', array('label'=>false));
      $this->result['reg_telephone']     = $this->hidden('reg_telephone', '', array('label'=>false));
      $this->result['country_code']      = $this->hidden('country_code', '', array('label'=>false));
      $this->result['country_iso_code']  = $this->hidden('country_iso_code', '', array('label'=>false));
      $this->result['redirect_cart']     = $this->hidden('redirect_cart', 0, array('label'=>false, 'id'=>'redirect_cart'));
      $this->result['submit']            = $this->submit('verify_otp', $language->get('button_verify'), array('class'=>'btn deliver_btn pull-right', 'id'=>'verify_otp'));
      $this->result['form_end']          = $this->form_end();
      return $this->result;
  }


  /*************Create Forgot form **********/
  public function update_password_form($action='', $form_name='password_from')
  {
      $language = new Language();
      $language->load('account/login');

      $this->result['form_start']  = $this->form_create($form_name, $action, array('id'=>$form_name));

      $this->result['password']  = $this->password('password', '', array('id'=>'input-reg_password', 'class'=>'otp_input', 'label'=>false, 'placeholder'=>$language->get('entry_password'), 'alt'=>$language->get('entry_password')));

      $this->result['confirm']  = $this->password('confirm', '', array('id'=>'input-confirm', 'class'=>'otp_input', 'label'=>false, 'placeholder'=>$language->get('entry_confirm'), 'alt'=>$language->get('entry_password')));

      $this->result['submit']  = $this->submit('Update', $language->get('button_continue'), array('class'=>'btn deliver_btn verify_btn', 'id'=>'Update'));

      $this->result['form_end'] = $this->form_end();

      return $this->result;

  }

  /*************Mobile form validation **********/
   public function mobileValidation($data=array())
   {
      $language = new Language();
      $language->load('account/login');
     if(is_numeric($data['reg_telephone']))
     {
       $validation = new validation();
       $validation->set_data($data);

        if($this->config['config_store_id'] == INTERNATIONAL_STORE_ID && $data['country_code'] != 91)
          {
             $validation->required(array('reg_telephone'), $language->get('error_telephone'))
                        ->num(array('reg_telephone'), $language->get('error_telephone2'))
                        ->minlen(array('reg_telephone'), 7, $language->get('error_telephone2'))
                        ->maxlen(array('reg_telephone'), 10, $language->get('error_telephone2'));
         }
        else
         {
              $validation->required(array('reg_telephone'), $language->get('error_telephone'))
                     ->mobile_number(array('reg_telephone'), $language->get('error_telephone2'));
         }     

      }
      else 
      {
        $validation = new validation();
        $validation->set_data($data);
        $validation->required(array('reg_telephone'), $language->get('error_email2'))
                   ->email(array('reg_telephone'), $language->get('error_email2'));  # code...
      }           

      $this->validation_result['errors'] = $validation->get_error();
      $this->validation_result['valid']  = $validation->is_valid();
      return $this->validation_result;

   }

  /*************Email form validation **********/
   public function emailValidation($data=array())
   {
      $language = new Language();
      $language->load('account/login');

      $validation = new validation();
      $validation->set_data($data);
      $validation->required(array('reg_telephone'), $language->get('error_email2'))
                 ->email(array('reg_telephone'), $language->get('error_email2'));

      $this->validation_result['errors'] = $validation->get_error();
      $this->validation_result['valid']  = $validation->is_valid();
      return $this->validation_result;

   }


  /*************Forgot password form validation **********/
   public function passwordValidation($data=array())
   {
      $language = new Language();
      $language->load('account/login');

      $validation = new validation();
      $validation->set_data($data);
      $validation->required(array('reg_telephone'), $language->get('error_telephone'))
                 ->num(array('reg_telephone'), $language->get('error_telephone'))
                 ->maxlen(array('reg_telephone'), 10, $language->get('error_telephone'))
                 ->minlen(array('reg_telephone'), 10, $language->get('error_telephone'))
                 ->required(array('password'), $language->get('error_password'))
                 ->maxlen(array('password'), 20, $language->get('error_password'))
                 ->minlen(array('password'), 4, $language->get('error_password'))
                 ->required('confirm', $language->get('error_confirm'))
                 ->equal('password', 'confirm', $language->get('error_confirm'));

      $this->validation_result['errors'] = $validation->get_error();
      $this->validation_result['valid']  = $validation->is_valid();
      return $this->validation_result;

   }



  /*************Register form validation **********/
   public function registerValidation($data=array())
   {
      $language = new Language();
      $language->load('account/login');
      $validation = new validation();
      $validation->set_data($data);
      
      if($this->config['config_store_id'] == INTERNATIONAL_STORE_ID && $data['mobile_country_code'] != 91)
        {
           $validation->required(array('reg_telephone'), $language->get('error_telephone'))
                      ->num(array('reg_telephone'), $language->get('error_telephone2'))
                      ->minlen(array('reg_telephone'), 5, $language->get('error_telephone2'))
                      ->maxlen(array('reg_telephone'), 10, $language->get('error_telephone2'));
        }
      else if($this->config['config_store_id'] == INTERNATIONAL_STORE_ID)
        {
           $validation->required(array('reg_telephone'), $language->get('error_telephone'))
                      ->num(array('reg_telephone'), $language->get('error_telephone2'))
                      ->minlen(array('reg_telephone'), 5, $language->get('error_telephone2'))
                      ->maxlen(array('reg_telephone'), 10, $language->get('error_telephone2'));
        }  
      else
        {
           $validation->required(array('reg_telephone'), $language->get('error_telephone'))
                      ->mobile_number(array('reg_telephone'), $language->get('error_telephone2'));
        }            


      $validation->required(array('customer_type_id'), $language->get('error_customer_type_id'));


      if(!empty($data['reg_email']))
      {
        $validation->email(array('reg_email'), $language->get('error_email2')); 
      }

      // if(isset($data['gst_uin_number']) && $data['gst_uin_number'] == 1)
      // {
      //   $validation->required(array('gst_number'), $language->get('error_gst_number'))
      //              ->validateGSTNo(array('gst_number'), $language->get('error_gst_number')); 
      // }                
                 
      $validation->required(array('password'), $language->get('error_password'))
                 ->maxlen(array('password'), 20, $language->get('error_password'))
                 ->minlen(array('password'), 4, $language->get('error_password'))
                 ->required(array('name'), $language->get('error_name'))
                 ->minlen(array('name'), 2, $language->get('error_name_limit'))
                 ->maxlen(array('name'), 64, $language->get('error_name_limit'))
                 ->required(array('company'), $language->get('error_company'))
                 ->minlen(array('company'), 2, $language->get('error_company_limit'))
                 ->maxlen(array('company'), 64, $language->get('error_company_limit'));

                 if($data['country_id'] != 99)
                 {
                   $validation->required(array('postcode'), $language->get('error_postcode2'))
                    ->minlen(array('postcode'), 2, $language->get('error_postcode2'))
                    ->maxlen(array('postcode'), 10, $language->get('error_postcode2'));
                 }
                 else
                 {
                   $validation->required(array('postcode'), $language->get('error_postcode'))
                    ->num(array('postcode'), $language->get('error_postcode2'))
                    ->minlen(array('postcode'), 6, $language->get('error_postcode2'))
                    ->maxlen(array('postcode'), 6, $language->get('error_postcode2'));
                 }  

     $validation->required(array('country_id'), $language->get('error_country'))
                 ->required(array('zone_id'), $language->get('error_zone'))
                 ->required(array('city'), $language->get('error_city'))
                 ->minlen(array('city'), 2, $language->get('error_city'))
                 ->maxlen(array('city'), 64, $language->get('error_city'))
                 ->required(array('address_1'), $language->get('error_address'))
                 ->minlen(array('address_1'), 3, $language->get('error_address'))
                 ->maxlen(array('address_1'), 128, $language->get('error_address'));         

      $this->validation_result['errors'] = $validation->get_error();
      $this->validation_result['valid']  = $validation->is_valid();
      return $this->validation_result;
   }

  /*************Register form validation **********/
   public function registerValidationCo($data=array())
   {
      $language = new Language();
      $language->load('account/login');
      $validation = new validation();
      $validation->set_data($data);
      
      if(!empty($data['reg_telephone']))
        {
           $validation->required(array('reg_telephone'), $language->get('error_telephone'))
                      ->num(array('reg_telephone'), $language->get('error_telephone2'))
                      ->minlen(array('reg_telephone'), 5, $language->get('error_telephone2'))
                      ->maxlen(array('reg_telephone'), 10, $language->get('error_telephone2'));
        }

      $validation->required(array('customer_type_id'), $language->get('error_customer_type_id'));


      if(!empty($data['reg_email']))
      {
        $validation->email(array('reg_email'), $language->get('error_email2')); 
      }

      // if(isset($data['gst_uin_number']) && $data['gst_uin_number'] == 1)
      // {
      //   $validation->required(array('gst_number'), $language->get('error_gst_number'))
      //              ->validateGSTNo(array('gst_number'), $language->get('error_gst_number')); 
      // }                
                 
      $validation->required(array('password'), $language->get('error_password'))
                 ->maxlen(array('password'), 20, $language->get('error_password'))
                 ->minlen(array('password'), 4, $language->get('error_password'))
                 ->required(array('country_id'), $language->get('error_country'));        

      $this->validation_result['errors'] = $validation->get_error();
      $this->validation_result['valid']  = $validation->is_valid();
      return $this->validation_result;
   }

  /*************Postcode validation **********/
   public function postcodeValidation($data=array())
   {
      $language = new Language();
      $language->load('account/login');

      $validation = new validation();
      $validation->set_data($data);
      $validation->required(array('postcode'), $language->get('error_postcode2'))
                 ->postcode(array('postcode'), $language->get('error_postcode2'));

      $this->validation_result['errors'] = $validation->get_error();
      $this->validation_result['valid']  = $validation->is_valid();
      return $this->validation_result;
   }

}
