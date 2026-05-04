<?php
require_once(DIR_SYSTEM . 'library/operations/membership/membership.php');

class TotalBase {

    // tax is used for _tax::getRates(),tax::getCST(),_tax::getTax in cashback, coupon and paycharge
    protected $_tax;
    protected $_registry;
    // _cart is user for _cart::getSubTotal(), _cart::getProducts(), _cart::_cart_data in subtotal,cashback and paycharge,
    //                  _cart->is_dropshipper in paycharge
    protected $_cart;
    // used for _config::credit_status in credit, _config::unit_testing_coupon in coupon, _config::paycharge, _config::config_language_id
    //          _config::config_store_id in paycharge , _config::config_cart_limit, _config::cashback_status in cashback, _config::round_off_status
    //           in roundoff, _config::sort_order in all classes
    protected $_config;
    // _load::language in all classes for loading language and for loading model, it is used in coupon,
    protected $_load;
    // _currency::getDecimalPlace in cashback, coupon, paycharge, _currency::getDecimalPlace and  round_off
    protected $_currency;
    // _language is used in all classes for getting the title
    protected $_language;
    protected $_db;
    // _customer::getBalance() in credit,_customer::getCashbackAvailable in Cashback
    protected $_customer;
    // _session is used for _session::data['coupon'], _session::data['shipping_method'] in coupon, _session::data['payment_method']['code']
    //                      in paycharge, _session::data['shipping_method'] in shipping, _session::data['voucher'] in voucher
    protected $_session;
    // _request is used for _request::get['route'] in paycharge
    protected $_request;
    // _gst is used for find if gst applicable or not.
    protected $_gst;
    protected $_decimal_places = 2;
    protected $_cart_data;
    protected $_sort_order;
    public $order_id;
    public $suborder = false;
    public $suborder_id;
    public $membership_info = null;

    protected $_custom_call = false;

    public function __construct( $registry ){
        $this->_registry = $registry;
        if(isset($registry->gst) && $registry->gst==0) {
            $this->_gst = 0;
        } else {
            $this->_gst = 1;
        }

        if ($this->_gst) {
            $this->_sort_order = array(
                'shipping_sort_order' => 4,
                'sub_total_sort_order' => 1,
                'tax_sort_order' => 5,
                'total_sort_order' => 6,
                'credit_sort_order' => 8,
                'coupon_sort_order' => 3,
                'paycharge_sort_order' => 2,
                'cashback_sort_order' => 7,
                'membership_sort_order' => 3
              );
        } else {
            $this->_sort_order = array(
                'shipping_sort_order' => 5,
                'sub_total_sort_order' => 1,
                'tax_sort_order' => 4,
                'total_sort_order' => 6,
                'credit_sort_order' => 8,
                'coupon_sort_order' => 3,
                'paycharge_sort_order' => 2,
                'cashback_sort_order' => 7,
                'membership_sort_order' => 3
              );
        }

        if (method_exists($registry, 'get')) {
            $this->_cart = $registry->get('cart');
            $this->_config = $registry->get('config');
            $this->_load = $registry->get('load');
            $this->_tax = $registry->get('tax');
            $this->_currency = $registry->get('currency');
            $this->_language = $registry->get('language');
            $this->_db = $registry->get('db');
            $this->_customer = $registry->get('customer');
            $this->_session = $registry->get('session');
            $this->_request = $registry->get('request');
        } else if ($registry->suborder) {
            $this->_db = $registry->db;
            $this->_cart_data = $registry->cart_data;
            $this->suborder = $registry->suborder;
            $this->order_id = $registry->order_id;
            $this->suborder_id = $registry->suborder_id;
            if (!empty($this->suborder) && $this->suborder && !empty($this->order_id) && $this->order_id <= POST_ORDER_ID_FOR_CASH_DISCOUNT_COUPON) {

                if ($this->_gst) {
                    $this->_sort_order = array(
                        'shipping_sort_order' => 5,
                        'sub_total_sort_order' => 1,
                        'tax_sort_order' => 6,
                        'total_sort_order' => 9,
                        'credit_sort_order' => 7,
                        'coupon_sort_order' => 4,
                        'paycharge_sort_order' => 3,
                        'cashback_sort_order' => 2,
                        'membership_sort_order' => 4
                      );
                } else {
                    $this->_sort_order = array(
                        'shipping_sort_order' => 6,
                        'sub_total_sort_order' => 1,
                        'tax_sort_order' => 5,
                        'total_sort_order' => 9,
                        'credit_sort_order' => 7,
                        'coupon_sort_order' => 4,
                        'paycharge_sort_order' => 3,
                        'cashback_sort_order' => 2,
                        'membership_sort_order' => 4
                      );
                }
            }
        } else {
            $this->_cart = $registry->cart;
            $this->_config = $registry->config;
            $this->_load = $registry->load;
            $this->_tax = $registry->tax;
            $this->_currency = $registry->currency;
            $this->_language = $registry->language;
            $this->_db = $registry->db;
            $this->_customer = $registry->customer;
            $this->_session = $registry->session;
            $this->_request = $registry->request;
        }
    }

    public function setOptions( $flag_name, $flag_value ){
        if(property_exists($this,'_'. $flag_name )){
            $this->{'_'.$flag_name} = $flag_value;
        }
        else{
            throw new Exception("Class TotalBase doesn't contain $flag_name");
        }
    }
    
    protected function getMembershipInfo() {
      $membership_obj = new Membership($this->_registry);
      $membership_obj->setProperty('master_id', $this->_customer->getMasterId());
      $this->membership_info = $membership_obj->getMembershipInfoByCustomerIds();
    }

}

?>
