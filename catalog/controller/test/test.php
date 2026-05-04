<?php
use Aws\S3\S3Client;

class ControllerTestTest extends Controller
{
    /**
     *
     */
    public function getAllCustomers(){

        //ORDER BY date_added
        $this->load->model('test/test');
        $this->load->model('account/customer');

        $results = $this->model_test_test->getAllCustomers();

        $today = Date('d_M_y');
        if (!file_exists(DIR_SYSTEM.'upload/assets/customers/'.$today)) {
            mkdir(DIR_SYSTEM.'upload/assets/customers/'.$today, 0777,true);
        }
        $i = 1;
        $fp = fopen(DIR_SYSTEM.'upload/assets/customers/'.$today.'/customer_data.csv', 'w');
        foreach($results as $result){

            //$customer_info = $this->model_account_customer->getCustomer($result['customer_id']);

            $name =  $result['name'];
            $email = $result['email'];
            $mob = $result['telephone'];
            $city = $result['city'];
            $address1 = $result['address_1'];
            $address2 = $result['address_2'];
            $postcode = $result['postcode'];


            if($i == 1){
                $i++;
                $data = array('Name','Email','Mobile','City', 'Address Line 1','Address Line 2','Post Code');
            }else{
                $i++;
                $data = array($name,$email,$mob,$city,$address1,$address2,$postcode);
            }
            fputcsv($fp, $data);
        }
        fclose($fp);
        // exit;
    }

    public function getCustomersByCity()
    {
        //ORDER BY date_added
        $this->load->model('test/test');
        $this->load->model('account/customer');
        if (isset($_GET['city'])) {
            $city = $_GET['city'];
        } else {
            $city = 'pune';
        }
        $results = $this->model_test_test->getCustomersByCity($city);
        //echo '<pre>'; print_r($results); exit;
        $today = Date('d_M_y');
        if (!file_exists(DIR_SYSTEM . 'upload/assets/city/' . $city . '/' . $today)) {
            mkdir(DIR_SYSTEM . 'upload/assets/city/' . $city . '/' . $today, 0777, true);
        }
        $i = 1;
        $fp = fopen(DIR_SYSTEM . 'upload/assets/city/' . $city . '/' . $today . '/city_data.csv', 'w');
        foreach ($results as $result) {
            //$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
            $customer_info = $this->model_account_customer->getCustomer($result['customer_id']);
            echo '<pre>';
            print_r($result);
            echo '</pre>';
            $name = $result['firstname'] . " " . $result['lastname'];
            $email = $customer_info['email'];
            $mob = $customer_info['telephone'];
            $address1 = $result['address_1'];
            $address2 = $result['address_2'];
            $postcode = $result['postcode'];
            //$ba = 'Business Address';
            $ref_code = '';

            if ($i == 1) {
                $i++;
                $data = array('Name', 'Email', 'Mobile', 'Address Line 1', 'Address Line 2', 'Post Code', 'Referral Code');
            } else {
                $i++;
                $data = array($name, $email, $mob, $address1, $address2, $postcode, $ref_code);
            }
            fputcsv($fp, $data);
        }
        fclose($fp);
        // exit;
    }

    /**
     * Get Customer By City (Which Have Ordered Already)
     * function getCustomersByCityWithOrder
     * @param city
     * @return Customer Details With Orders Details
     * @author Ravindra Singh
     * @date 14-12-2015
     *
     */
    public function getCustomersByCityWithOrder()
    {
        //ORDER BY date_added
        $this->load->model('test/test');
        $this->load->model('account/customer');
        //$city = 'pune';
        if (isset($_GET['city'])) {
            $city = $_GET['city'];
        } else {
            $city = 'all';
        }
        $city_folder = $city;
        $results = $this->model_test_test->getCustomersByCityWithOrder($city);
        //echo "<pre>"; print_r($results); die;
        $today = Date('d_M_y');
        if (!file_exists(DIR_SYSTEM . 'upload/assets/city/' . $city . '/' . $today)) {
            mkdir(DIR_SYSTEM . 'upload/assets/city/' . $city . '/' . $today, 0777, true);
        }
        $i = 1;
        $fp = fopen(DIR_SYSTEM . 'upload/assets/city/' . $city . '/' . $today . '/ordered_customers_by_city.csv', 'w');
        foreach ($results as $result) {
            $or_der = '';
            foreach ($result['orders'] as $order) {
                $or_der[] = date("d-M-y", strtotime($order['date_added'])) . "_" . $order['total'];
            }
            //$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
            $customer_info = $this->model_account_customer->getCustomer($result['customer_id']);
            //echo '<pre>'; print_r($result); echo '</pre>';
            $name = $result['name'];
            $email = $result['email'];
            $mob = $result['telephone'];
            $address = $result['address'];
            $postcode = $result['postcode'];
            $company = $result['company'];
            $total_order = $result['total_order'];
            $lastOrderDate = $result['last_order_date'];
            $orders = implode(", ", $or_der);
            $city = $result['city'];
            //$ba = 'Business Address';

            if ($i == 1) {
                $i++;
                $data = array('Name', 'Company', 'Mobile', 'Address', 'Post Code', 'Email', 'Total Order', 'Last Order Date Time', 'Orders','City');
            } else {
                $i++;
                $data = array($name, $company, $mob, $address, $postcode, $email, $total_order, $lastOrderDate, $orders,$city);
            }


            fputcsv($fp, $data);
        }
        fclose($fp);
        // Added by Parth on discussion with Rakesh Shekhwat
        // &-Jan-2015
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Rakesh Singh');
        $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');

        $mail->addAddress($this->config->get('config_email'), 'WholesaleBox');
        $mail->addCC(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
        $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);



        $mail->Subject = 'WholesaleBox - Ordered Customers List for city - ' . $city_folder . '(' . Date("d/m/Y") . ')';
        $mail->Body = 'Please find the csv of logged users with orders in ' . $city_folder;

        $city_order_data = DIR_SYSTEM . 'upload/assets/city/' . $city_folder . '/' . $today . '/ordered_customers_by_city.csv';

        $mail->AddAttachment($city_order_data);

        if ($mail->send()) {
            echo "success";
            exit;
        } else {
            echo "error";
            exit;
        }

    }

    public function getCustomersByStateWithOrder()
    {
        //ORDER BY date_added
        $this->load->model('test/test');
        $this->load->model('account/customer');
        //$city = 'pune';
        if (isset($_GET['state'])) {
            $state = $_GET['state'];
        } else {
            $state = 'all';
        }
        $city_folder = $state;
        $results = $this->model_test_test->getCustomersByStateWithOrder($state);
        //echo "<pre>"; print_r($results); die;
        $today = Date('d_M_y');
        if (!file_exists(DIR_SYSTEM . 'upload/assets/state/' . $state . '/' . $today)) {
            mkdir(DIR_SYSTEM . 'upload/assets/state/' . $state . '/' . $today, 0777, true);
        }
        $i = 1;
        $fp = fopen(DIR_SYSTEM . 'upload/assets/state/' . $state . '/' . $today . '/ordered_customers_by_state.csv', 'w');
        foreach ($results as $result) {
            $or_der = '';
            foreach ($result['orders'] as $order) {
                $or_der[] = date("d-M-y", strtotime($order['date_added'])) . "_" . $order['total'];
            }
            //$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
            $customer_info = $this->model_account_customer->getCustomer($result['customer_id']);
            //echo '<pre>'; print_r($result); echo '</pre>';
            $name = $result['name'];
            $email = $result['email'];
            $mob = $result['telephone'];
            $address = $result['address'];
            $postcode = $result['postcode'];
            $company = $result['company'];
            $total_order = $result['total_order'];
            $lastOrderDate = $result['last_order_date'];
            $orders = implode(", ", $or_der);
            $city = $result['city'];
            //$city = $result['state'];
            //$ba = 'Business Address';

            if ($i == 1) {
                $i++;
                $data = array('Name', 'Company', 'Mobile', 'Address', 'Post Code', 'Email', 'Total Order', 'Last Order Date Time', 'Orders','City', 'State');
            } else {
                $i++;
                $data = array($name, $company, $mob, $address, $postcode, $email, $total_order, $lastOrderDate, $orders,$city, $state);
            }


            fputcsv($fp, $data);
        }
        fclose($fp);
        // Added by Parth on discussion with Rakesh Shekhwat
        // &-Jan-2015
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Rakesh Singh');
        $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');

        $mail->addAddress($this->config->get('config_email'), 'WholesaleBox');
        $mail->addCC(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
        $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);

        $mail->Subject = 'WholesaleBox - Ordered Customers List for state -' . $city_folder . '(' . Date("d/m/Y") . ')';
        $mail->Body = 'Please find the csv of logged users with orders in ' . $city_folder;

        $city_order_data = DIR_SYSTEM . 'upload/assets/state/' . $city_folder . '/' . $today . '/ordered_customers_by_state.csv';

        $mail->AddAttachment($city_order_data);

        if ($mail->send()) {
            echo "success";
            exit;
        } else {
            echo "error";
            exit;
        }

    }

    public function getCampusMembers()
    {
        $this->load->model('test/test');
        $results = $this->model_test_test->getCampusMembers();
        //echo '<pre>'; print_r($results); exit;
        $i = 1;
        $fp = fopen(DIR_APPLICATION . 'controller/test/campusmembers.csv', 'w');
        foreach ($results as $result) {
            if ($i == 1) {
                $i++;
                $data = array('Member Id', 'Member Name', 'Member Email', 'Member Contact', 'Member City', 'Campus Id', 'Internship Company', 'Internship Role', 'Grade Point', 'Desired Profile', 'Campus Name', 'Team Member Id', 'Team Id', 'Team Name');
            } else {
                $i++;
                $data = array($result['member_id'], $result['member_name'], $result['member_email'], $result['member_contact_no'], $result['member_city'], $result['campus_id'], $result['internship_company'], $result['internship_role'], $result['grade_point'], $result['desired_profile'], $result['campus_name'], $result['team_member_id'], $result['team_id'], $result['team_name']);
            }
            fputcsv($fp, $data);
        }
        fclose($fp);
    }

    public function convertSingleFiltersToStore()
    {
        $this->load->model('test/test');
        $results = $this->model_test_test->convertSingleFiltersToStore();
        echo '<pre>';
        print_r($results);
        exit;

    }

    public function updateProductToCategory()
    {
        /* $filter_data = array(
             'filter_category_id' => 61,
             'filter_filter'=>'102,30,5,36'
         );
         $this->load->model('catalog/product');
        echo  $this->model_catalog_product->getTotalProducts($filter_data);
 */
        $this->load->model('test/test');
        //$results = $this->model_test_test->updateProductToCategory(74);
        $results = $this->model_test_test->updateProductToCategory(73);

        //echo '<pre>'; print_r($results); exit;
    }

    public function insertMainCategory()
    {
        $this->load->model('test/test');
        $results = $this->model_test_test->insertMainCategory();
    }

    public function getProductsWithOptions($value = '')
    {
        $this->load->model('test/test');

        $option_products = $this->model_test_test->getProductsWithOptions();
        echo("<pre>");
        print_r($option_products);

        # code...
    }

    /**
     * Update Selling Price With price And Commission
     * function updateSellingPrice
     * @author Ravindra Singh
     * @date 22-12-2015
     * */
    public function updateSellingPrice()
    {
        // get products
        $products = $this->db->query("SELECT product_id, 
                                             hsn_code, 
                                             price, 
                                             commission 
                                      FROM oc_product 
                                      WHERE hsn_code > 0")->rows;
                                      
        foreach ($products as $product) {
            
            $selling_price = Cart::getPrice($product, $this)['original_selling_price'];
            $this->db->query("UPDATE oc_product 
                              SET selling_price = '" . (float)$selling_price . "' 
                              WHERE product_id = '" . (int)$product['product_id'] . "'");
        }
        
    }

    /**
     * Getting seller side purchase details for accounting purposes
     * Returns for only orders which are processed.
     * Prints out in a csv format
     * @author Madhur Bhaiya
     */
    public function getPurchaseDetails()
    {

        $this->load->model('test/test');
        //$this->load->model('account/customer');
        $results = $this->model_test_test->updateSellingPrice();
        echo "<pre>";
        print_r($results);
        exit;
    }

    /**
     * Get Customers with their cart
     * function getCustomersWithCart
     * @author Ravindra Singh
     * @date 29-12-2015
     * */
    public function getCustomersWithCart()
    {
        $this->load->model('test/test');
        $this->load->model('account/customer');
        $rs = $this->model_test_test->getCustomersWithCart();
        //echo "<pre>"; print_r($rs); exit;
        $today = Date('d_M_y');
        if (!file_exists(DIR_SYSTEM . 'upload/assets/customer/' . $today)) {
            mkdir(DIR_SYSTEM . 'upload/assets/customer/' . $today, 0777, true);
        }
        $i = 1;
        $fp = fopen(DIR_SYSTEM . 'upload/assets/customer/' . $today . '/customers_saved_cart.csv', 'w');
        foreach ($rs as $result) {
            //$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
            $customer_info = $this->model_account_customer->getCustomer($result['customer_id']);
            //echo '<pre>'; print_r($result); echo '</pre>';
            $name = $result['name'];
            $email = $result['email'];
            $mob = $result['telephone'];
            $address = $result['address_1'] . ' ' . $result['address_2'];
            $postcode = $result['postcode'];
            $company = $result['company'];
            //$total_order = $result['total_order'];
            //$lastOrderDate = $result['last_order_date'];
            //$cart = $result['cart'];
            //$ba = 'Business Address';

            if ($i == 1) {
                $i++;
                $data = array('Name', 'Company', 'Mobile', 'Address', 'Post Code', 'Email');
            } else {
                $i++;
                $data = array($name, $company, $mob, $address, $postcode, $email);
            }
            fputcsv($fp, $data);
        }
        fclose($fp);


    }
    public function testInfo()
    {

        echo "<pre>";
        print_r(get_loaded_extensions());
        phpinfo();
    }


    public function gatiApi($value = '')
    {
        $test_link = "http://119.235.57.47:9080/TESTFKGatiXMLpickup2.jsp";
        // $test_link = "http://www.gati.com/webservices/JGatiXMLpickup.jsp";
        $data =

            '<gati>
                   <pickuprequest><?php echo date_timestamp_get()?></pickuprequest>                                                          
                   <custcode>55604502</custcode>                                                          
                   <details>                                                            
                   <req>                                                            
                   <DOCKET_NO>511251838</DOCKET_NO>                                                            
                   <DELIVERY_STN></DELIVERY_STN>                                                           
                   <GOODS_CODE>410</GOODS_CODE>                                                          
                   <DECL_CARGO_VAL>11258</DECL_CARGO_VAL>                                                           
                   <ACTUAL_WT>10.2</ACTUAL_WT>                                                         
                   <CHARGED_WT>10.2</CHARGED_WT>                                                            
                   <SHIPPER_CODE>55604502</SHIPPER_CODE>                                                         
                   <ORDER_NO>20160202107</ORDER_NO>                                                            
                   <COD_AMT>11258</COD_AMT>                                                            
                   <COD_IN_FAVOUR_OF>G</COD_IN_FAVOUR_OF>                                                         
                   <RECEIVER_CODE>99999</RECEIVER_CODE>                                                            
                   <RECEIVER_NAME>Pandian</RECEIVER_NAME>                                                           
                   <RECEIVER_ADD1>c/o citiz fabric</RECEIVER_ADD1>                                                            
                   <RECEIVER_ADD2>No. 4, 3rd main road chitlapakkam</RECEIVER_ADD2>                                                            
                   <RECEIVER_CITY>Chennai</RECEIVER_CITY>                                                            
                   <RECEIVER_PHONE_NO>9840345056</RECEIVER_PHONE_NO>                                                          
                   <RECEIVER_PINCODE>600064</RECEIVER_PINCODE>                                                          
                   <NO_OF_PKGS>1</NO_OF_PKGS>                                                          
                   <FROM_PKG_NO>1</FROM_PKG_NO>                                                          
                   <TO_PKG_NO>1</TO_PKG_NO>                                                            
                   <RECEIVER_MOBILE_NO>9840345056</RECEIVER_MOBILE_NO>                                                         
                   <Cust_Date_Delivery></Cust_Date_Delivery>                                                         
                   <SPL_Instruction></SPL_Instruction>                                                          
                   <PROD_SERV_CODE>1</PROD_SERV_CODE>
                   <CUST_VEND_CODE>JAICWC</CUST_VEND_CODE>
                   </req>                                                           
                   </details>                                                      
                   </gati>';


        // $data = array(
        //              "q" => array('name'=>'rakesh', 'city'=>'pune'),
        //              "limit"=>0,
        //              "offset"=>30
        //          );

        // $data_string = json_encode($data);

        $ch = curl_init($test_link);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        echo $result = curl_exec($ch);

        // $url = $test_link.$data;
        // $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $curl_scraped_page = curl_exec($ch);
        // curl_close($ch);


    }

    /**
     * Takes data from product to store and adds is_single =1
     * @author Parth Gupta
     * @dateTime 2016-01-19T15:53:12+0530
     * @return   void
     */
    public function convertToSingles($value=''){
        $this->load->model('test/test');

        $result = $this->model_test_test->convertToSingles();

    }


    /**
     * Functions to update referral code field with randomly generated 8 digit code
     */
    public function updatereferralcode()
    {
        $this->load->model('test/test');
        $customers = $this->model_test_test->getallcustomers();
        foreach ($customers as $customer_ids) {
            // echo "<pre>"; print_r($customer_ids['referral_code']); die;
            $customer_id = $customer_ids['customer_id'];
            $referral = $customer_ids['referral_code'];
            if(!$referral){
                $referral_code = $this->generatereferralcode();
                $this->model_test_test->updatereferralcode($customer_id, $referral_code);
            }
        }
    }

    public function generatereferralcode()
    {
        //Generate random referral code
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $referral_code = substr(str_shuffle($chars), 0, 8);
        return $referral_code;
    }



    /***
     * Get Customers and send notification them on mobile app
     ***/
    public function sendPushNotificationToCustomers(){
        $customers = array();
        $cids = array();

        $cids[] = 'cjBai6WVGUE:APA91bH4hQvz_zIg0MH6CowvyNDF7chXakDS8tmXssTCLgnr7NeEsvRbWTf9DG3fDkyF8NEuNSG4c-G0ylIlF8ZSXn186sdWCEz5tqo23DLluDP7IM13YzOIg_5_NUhDt0nicPMR98V-';
        //$cids[] = 'ci7kOesIABU:APA91bFCsMSnDtMxYEAbHW4sNjJy90tY8kiM33W1jmCijkpMomhH8TPoc85Bl1Fq0kVPsAIwttUcqKXKttlh2qVQwsp6j-A89JdhS00a0kZ62MZEN6Kft3XoYGzLaZNxhtaLz7aZ2tQc';
        //echo "<pre>"; print_r($cids); exit("last");
        //$registrationIds = array( $_GET['id'] );
        // prep the bundle
        $msg = array
        (
            'message' 	=> 'A test message',
            'title'		=> 'This is a title. title',
            'subtitle'	=> 'This is a subtitle. subtitle',
            'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
            'vibrate'	=> 1,
            'sound'		=> 1,
            'largeIcon'	=> 'large_icon',
            'smallIcon'	=> 'small_icon',
            'pull_call_history' => 'true'

        );
        $fields = array
        (
            'registration_ids' 	=> $cids,
            'data'			=> $msg
        );

        $API_ACCESS_KEY = 'AIzaSyCqnxMbpr2Y-51AH5bdIO7sR7v-oJQF4iE';
        $headers = array
        (
            'Authorization: key=' . $API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        if(curl_error($ch)) {
            echo 'error: '. curl_error($ch);
        }
        curl_close( $ch );
        echo "<pre>"; print_r($result); exit("last");
    }
    public function addProductsToInternationalStore(){
        $sql = "SELECT  product_id FROM
            ".DB_PREFIX."product_to_store ps WHERE
            ps.store_id=0 AND ps.store_id NOT IN (2)";
        // echo "<pre>";
        $results = $this->db->query($sql)->rows;
        echo count($results);

        foreach ($results as $result) {
            echo("INSERT INTO ".DB_PREFIX."product_to_store  
            SET product_id=".$result['product_id'].", store_id=2 ")."<br>";
        }
    }
    /**
     * polulating product to store table
     * @author Parth Gupta
     * @dateTime 2016-03-28T16:43:40+0530
     * @return void diplays the insert queries on screen
     */
    public function addProductsToSellerStore(){
        $this->load->model('setting/setting');
        $store_info = $this->model_setting_setting->getSetting('config', $this->config->get('config_store_id'));

        $sql = "SELECT mp.product_id FROM ".DB_PREFIX."ms_product mp
                INNER JOIN ".DB_PREFIX."product_to_store p2s 
                ON (mp.product_id = p2s.product_id)
                WHERE mp.seller_id =".
            $store_info['config_seller_id']." 
                AND p2s.product_id NOT IN 
                (SELECT product_id FROM oc_product_to_store WHERE store_id IN (".$this->config->get('config_store_id').")) 
                GROUP BY mp.product_id";

        $results = $this->db->query($sql);

        foreach ($results->rows as $result) {
            echo "INSERT INTO ".DB_PREFIX."product_to_store 
                    SET product_id =".$result['product_id'].", 
                    store_id=".$this->config->get('config_store_id') . ";<br>";
        }
    }
    public function getSellerStore(){
        $this->load->model('setting/store');
        $seller_id = 53;
        $store = $this->db->query("SELECT os.store_id FROM ".DB_PREFIX."setting os
            WHERE os.code = 'config' 
            AND os.key = 'config_seller_id'   
            AND os.value = ".$seller_id)->rows;


        if (!empty($store)) {
            return $this->model_setting_store->getStore($store['store_id']);
        }
        else{
            return 0;
        }
    }

    /**
     * Takes data from product to store and adds is_single =1
     * @author Parth Gupta
     * @dateTime 2016-01-19T15:53:12+0530
     * @return   void
     */
    public function convertToSOR($value=''){
        $this->load->model('test/test');

        $result = $this->model_test_test->convertToSingles();

    }

    /**
     *
     */
    public function copyCategoryToStore(){

        $this->load->model('test/test');

        $this->model_test_test->copyCategoryToStore(0, 6);


    }

    public function sorFix(){

        $this->load->model('test/test');

        $this->model_test_test->sorFix();



    }

    public function test(){
        if(defined('WSB_STORES_ID')) {
            echo WSB_STORES_ID;
        }else{
            echo 'not defined';
        }
    }
    /**
     * Equalises the quantity of SOR store to the singles and wholesale set store quantity
     * @author Parth Gupta
     * @dateTime 2016-06-04T12:21:03+0530
     */
    public function QuantityEqualisation(){

        $sql =  "SELECT p.model, p.product_id, p.quantity FROM ".DB_PREFIX."product p 
                INNER JOIN ".DB_PREFIX."product_to_store p2s 
                ON(p.product_id = p2s.product_id) 
                WHERE p2s.store_id = ". SOR_STORE_ID ;

        $model_results = $this->db->query($sql)->rows;


        foreach ($model_results as $model_result) {

            $sql3 = "SELECT p.model, p.product_id, p.quantity FROM ".DB_PREFIX."product p 
                    WHERE p.model = ". "'".split("-SOR", $model_result['model'])[0]."'";

            $product = $this->db->query($sql3)->row;

            echo $update_sql = "UPDATE ".DB_PREFIX."product p 
            SET p.quantity = ".$product['quantity']." WHERE 
            p.product_id = ".$model_result['product_id'] . " ;<br>";

        }

    }
    public function CrmCsvForCustomers(){
        $sql =
            "SELECT 
                a.company as 'business_name',
                c.firstname  as 'contact_name', 
                c.email as 'email', 
                c.telephone as 'landline', 
                a.telephone as 'contact_mobile', 
                a.address_1 as 'address_1', 
                a.address_2 as 'address_2', 
                a.postcode as 'zipcode', 
                a.city as 'city', 
                z.name as 'state', 
                co.name as 'country',  
                os.telephone as 'assigned_to'  

                FROM oc_customer c 
                LEFT JOIN oc_address a ON (c.customer_id = a.customer_id)
                LEFT JOIN oc_country co ON (a.country_id = co.country_id) 
                LEFT JOIN oc_zone z ON (a.zone_id = z.zone_id) 
                LEFT JOIN oc_order o ON (c.customer_id = o.customer_id)
                LEFT JOIN oc_sales_staff os ON (o.sales_staff_id = os.staff_id )
                GROUP BY c.telephone
                ";

        $results = $this->db->query($sql)->rows;
        // echo "<pre>"; print_r($results); die;
        $today = Date('d_M_y');
        if (!file_exists(DIR_SYSTEM . 'upload/assets/customer/' . $today)) {
            mkdir(DIR_SYSTEM . 'upload/assets/customer/' . $today, 0777, true);
        }
        $i = 1;
        $handle = fopen(DIR_SYSTEM . 'upload/assets/customer/' . $today . '/crm_csv.csv', 'w');

        $i = 1;
        foreach ($results as $result) {


            $business_name = $result['business_name'];
            $contact_name = $result['contact_name'];
            $email = $result['email'];
            $landline = $result['landline'];
            $mobile = '';
            $contact_mobile = $result['contact_mobile'];
            $address_1 = $result['address_1'];
            $address_2 = $result['address_2'];
            $zipcode = $result['zipcode'];
            $locality = '';
            $city = $result['city'];
            $state = $result['state'];
            $country = $result['country'];
            $assigned_to = $result['assigned_to'];
            $contact_email = $result['email'];
            $source = 7;



            if ($i == 1) {
                $i++;
                $data = array('Business Name',
                    'Landline',
                    'Mobile',
                    'Email',
                    'Address 1',
                    'Address 2',
                    'Zipcode',
                    'Locality',
                    'City',
                    'State',
                    'Country',
                    'Contact Name',
                    'Contact Mobile',
                    'Contact Email',
                    'Assigned to',
                    'Source');
            } else {
                $i++;
                $data = array($business_name, $landline, $mobile, $email, $address_1, $address_2, $zipcode,$locality, $city,$state, $country, $contact_name,$contact_mobile,$contact_email,$assigned_to,$source);
            }
            fputcsv($handle,$data);
        }
        fclose($handle);

    }

    /**
     * Get translitaed values for category and update category description accordinngly
     */
    public function translateCategories(){
        $this->load->model('catalog/category');
        $this->load->model('localisation/reverie');

        $data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            $category_name[] = $category['name'];
            $category_description[] = $category['description'];
            $children = $this->model_catalog_category->getCategories($category['category_id']);
            //Language Translation API

            foreach ($children as $child) {
                $category_name[] = $child['name'];
                $category_description[] = $child['description'];
            }
        }

        $t_category_name = $this->model_localisation_reverie->translate($category_name, 'hindi');

        $this->model_localisation_reverie->update($t_category_name, 'hindi', 'category_description', 'name');

        // echo '<pre>'; print_r($t_category_name);

        /* if(isset($t_category_name->outArray) && count($t_category_name->outArray) > 0){
             echo $this->model_localisation_reverie->getLanguageRecordId('category_description', 61, 2);
         }*/
    }

    /**
     * Get translitaed values for category and update category description accordinngly
     */
    public function translateFilters(){

        $this->load->model('localisation/reverie');

        $data['filters'] = array();

        $sql = "SELECT f.filter_id, f.name
                FROM oc_filter_description f
                WHERE f.language_id = 1
                ORDER BY f.filter_id ASC
                ";
        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $filter_name[] = $result['name'];

        }

        $t_filter_name = $this->model_localisation_reverie->translate($filter_name, 'hindi');
        
        $this->model_localisation_reverie->update($t_filter_name, 'hindi', 'filter_description', 'name');

    }

    public function translateProducts(){

        $this->load->model('catalog/product');
        $this->load->model('localisation/reverie');

        $data['products'] = array();

        if(isset($this->request->get['field']) && $this->request->get['field'] != '') {
            $field = $this->request->get['field'];
        }else{
            echo "Field parameter is missing."; die;
        }

        $sql = "SELECT count(p.product_id) as total
                FROM oc_product p
                LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id)
                WHERE pd.language_id = 1

                ";
        $query = $this->db->query($sql);
        $total_products = $query->row['total'];

        $product_translate_in_one_shhot = 5000;

        $total_pages = ceil($total_products/$product_translate_in_one_shhot);

        if(isset($this->request->get['page']) && $this->request->get['page'] > 0){
            $page = $this->request->get['page'];
        }else{
            $page = 1;
        }

        $next_page = $page + 1;

        $limitstart = $this->request->get['limitstart'];
        $limit = $this->request->get['limit'];

        $sql = "SELECT p.product_id
                FROM oc_product p
                LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id)
                WHERE pd.language_id = 1
                ORDER BY product_id ASC
                LIMIT $limitstart, $limit;
                ";
        $query = $this->db->query($sql);
        //echo "<pre>"; print_r($query->rows); exit;
        foreach ($query->rows as $result) {

            $product = $this->model_catalog_product->getProduct($result['product_id']);
            if($field == 'name' && $product['name'] != '') {
                $product_name[] = $product['name'];
            }
            if( $field == 'set_description' && $product['set_description'] != '') {
                $set_description[] = $product['set_description'];
            }

            $result['product_id'] ."<br />";
            //$products[$result['product_id']] = $this->getProduct($result['product_id']);
        }
        sleep(5);

        if($field == 'name') {
            $t_product_name = $this->model_localisation_reverie->translate($product_name, 'hindi', 4);
            $this->model_localisation_reverie->update($t_product_name, 'hindi', 'product_description', 'name');
        }
        if($field == 'set_description') {
            $t_set_description = $this->model_localisation_reverie->translate($set_description, 'hindi', 4);
            $this->model_localisation_reverie->update($t_set_description, 'hindi', 'product_description', 'set_description');
        }
    }

    public function translateLanguageFiles(){
        if ($handle = opendir(DIR_LANGUAGE."english")) {

            echo "Directory handle: $handle\n";
            echo "Entries:\n";

            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {


                sleep(10);
                echo '<pre>';
                echo $entry . "<br />";
                //print_r($files);
            }


            closedir($handle);
        }
    }
    public function testReverie(){
        $this->load->model('localisation/reverie');
        $inArray = array('My Account');
        $t_set_description = $this->model_localisation_reverie->localisation($inArray, 'hindi', 6);

        echo '<pre>'; print_r($t_set_description);
    }

    public function localLanguageTranslation(){
        $dir = DIR_LANGUAGE.'/english';
        $results = $this->dirToArray($dir);
        //echo "<pre>"; print_r($results); die;
        $_english = array();
        $full_array = array();
        foreach($results as $key => $result){
            //echo "<pre>"; print_r($result); die;
            foreach($result as $value){

                $_ = '';
                include_once ("".$dir."/".$key."/".$value."");
                $e_text = array();
                foreach($_ as $k => $text){
                    // echo $text."<br/>" ;
                    $e_text[] = strip_tags($text);
                }
                $full_array[$key][$value] = $_;
                $_english[] = $e_text;
                $this->load->model('localisation/reverie');

                $_hindi = $this->model_localisation_reverie->localisation($_english[0], 'hindi', 6);
                //echo "<pre>"; print_r($_hindi); die;
                $this->updateLocalLanguage($_hindi, $full_array);
            }
        }
    }

    public function dirToArray($dir) {

        $result = array();

        $cdir = scandir($dir);
        //echo "<pre>"; print_r($cdir); die;

        foreach ($cdir as $key => $value)
        {
            if (!in_array($value,array(".","..")))
            {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
                {
                    $result[$value] = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                }
                else
                {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    //find product which match model number (use model like '%model%') and then update rating by vikas(18-07-2016)
    public function updateRatingByProductModel(){
        $model_name = $this->request->get['sku'];
        $rating = $this->request->get['rating'];
        $sql = "SELECT product_id FROM oc_product WHERE model LIKE '%".$model_name."%'";
        $query = $this->db->query($sql);
        foreach($query->rows as $key=>$values){
            $get_review_product = "SELECT product_id FROM oc_review WHERE product_id = '".$values['product_id']."' AND author = 'Admin' ";
            $result_value = $this->db->query($get_review_product);
            if($result_value->num_rows > 0){
                $sql = "UPDATE oc_review
                        SET rating='".$rating."',
                            status = 1,
                            customer_id= 0,
                            date_modified = NOW()
                        WHERE product_id ='".$values['product_id']."'
                            AND author = 'Admin';
                        ";
            }else{
                $sql = "INSERT INTO oc_review
                        SET product_id ='".$values['product_id']."',
                            customer_id= 0,
                            author='Admin',
                            rating='".$rating."',
                            status = 1,
                            date_added = NOW();
                        ";
            }

            echo "<br>"; print_r($sql);
        }

    }

    public function testSolrSelect(){
        $filter_data = array(
            'filter_category_id' => 61,
            'filter_filter'      => '',
            'option'      		 => '',
            'price_filter'	 	 => '',
            'rating_filter'	 	 => 5,
            'sort'               => '',
            'order'              => '',
            'start'              => 0,
            'limit'              => 60

        );
        $solr = new SolrProduct($this);
        $results = $solr->getProductFromSolr($filter_data);
    }

    /**
     * to translate information
     */
    public function translateInformation(){
        $this->load->model('localisation/reverie');
        $this->load->model('catalog/information');
        $result = $this->model_catalog_information->getInformations();
        //echo "<pre>"; print_r($result); exit;
        $info_title = array();
        foreach ($result as $res){
            $info_title[] = $res['title'];
        }
        $t_info_title = $this->model_localisation_reverie->translate($info_title, 'hindi', 4);
        $this->model_localisation_reverie->update($t_info_title, 'hindi', 'information_description', 'title');
        //echo "<pre>"; print_r($result); die;
    }

    /**
     * Partially update the solr document in bulk [Commit after updatig all docs]
     * @author Garvit Joshi
     * @param $data
     * @param $type
     */
    public function searchedTerms(){
        $this->load->model('test/test');
        $results = $this->model_test_test->searchedTerms();
        echo "Done";
    }

    public function testS3(){


        $bucket = 'cdnimages.net.products';
        $keyname = 'image/tops.jpg';

        // Instantiate the client.
        $s3 = new S3Client([
            'profile'=>'default',
            'version' => 'latest',
            'region'  => 'ap-south-1'
        ]);

        $filepath = "/var/www/html/wholesalebox/image/tops.jpg";
        // Upload data.
        $result = $s3->putObject(array(
            'Bucket' => $bucket,
            'Key'    => $keyname,
            'SourceFile' => $filepath, // file path which is putting on AWS S3, Path should be absolute path like $filepath = "/var/www/html/for_testing_aws/assets/img/avtar.png";
            'ContentType' => mime_content_type($filepath),
        ));

        echo $result['ObjectURL'];
    }

    /**
     * Sync all rating, order, wishlist to solr
        No more use of this function from 19th April 2018
        Now rating is added in oc_product and order wishlist will be manage by preferences logic.
     */
    /* public function syncProductToSolrAfterDataImport(){

        $start = microtime(true);

        set_time_limit(0);
        $solr = new SolrProduct($this);

        if(isset($this->request->get['sync']) && $this->request->get['sync'] == 'rating'){
            $sync_type = 'rating';
        }elseif(isset($this->request->get['sync']) && $this->request->get['sync'] == 'order_wishlist'){
            $sync_type = 'order_wishlist';
        }else{
            $sync_type = 'both';
        }

        $check = true;
        $limit = 0;
        while($check == true){
            $check = $solr->syncProductToSolrAfterDataImport($limit, $sync_type);
            $limit++;
        }

        $end = microtime(true);
        echo "Sync Time: ". ($end - $start).' seconds';
    } */

    public function UpdatePreferencesAccOrder(){

        $sql = "SELECT distinct customer_id FROM ". DB_PREFIX ."order";
        $cust_ids = $this->db->query($sql)->rows;
        $data = array();
        foreach ($cust_ids as $value) {
            $sql        = "SELECT order_id FROM ". DB_PREFIX ."order WHERE customer_id = '" . $value['customer_id'] . "'";
            $order      = $this->db->query($sql)->rows;
            $order_id   = array_column($order, 'order_id');

            $data['order_ids']      = implode(',', $order_id);
            $data['customer_id']    = $value['customer_id'];

            $sql = "SELECT distinct p2c.category_id
                FROM ". DB_PREFIX ."order_product op
                INNER JOIN ". DB_PREFIX ."product_to_category p2c ON (op.product_id = p2c.product_id)
                WHERE op.order_id IN (" . $data['order_ids'] . ")";
            $category   = $this->db->query($sql)->rows;
            $data['category_id'] = array_column($category, 'category_id');

            $sql = "SELECT customer_id, category_id FROM ". DB_PREFIX ."customer_preference WHERE customer_id = ".$value['customer_id']."";
            $query  = $this->db->query($sql)->rows;
            $data['preferences_category_id'] = array_column($query, 'category_id');

            foreach ($data['category_id'] as $value) {
                if(in_array($value, $data['preferences_category_id'])){
                }else{
                    $sql  = "INSERT INTO ". DB_PREFIX ."customer_preference set customer_id = '".$data['customer_id']."', category_id= '".$value."', filter_id= '', min_price= '', max_price= '', created= NOW(), modify= NOW()";
                    $query = $this->db->query($sql);
                }
            }
        }
    }


    public function testglobalheader()    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = 'html';
        $html = 'HEY !! ';
        $mail_header = new MailTemplate();
        $mail_header->applyGeneralHeader($html);
        $mail_header->applyGeneralFooter($html);
        $mail->Host = ($this->config->get('config_mail_smtp_hostname'));
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Rakesh Singh');
        $mail->addAddress("vks.agrawal0110@gmail.com", 'WholesaleBox');
        $mail->Subject = 'Generic Mail Template Test Mode';
        $mail->msgHTML($html);
        $mail->send(1);
    }

    /**
     * Import zipcode in DB
     */
    public function importZipCodeInDb(){
        $this->load->model('localisation/zone');

        $file = fopen('/var/www/html/wholesalebox1/all_india_pin_code.csv', 'r');
        $i = 0;
        while (($line = fgetcsv($file)) !== FALSE) {
            //$line is an array of the csv elements
            if ($i <= 0) {
                $i++;
                continue;
            }
            $post_office = $line[0];
            $pincode = $line[1];
            $taluka = $line[7];
            $city = $line[8];
            $state = $line[9];
            $country_id = 99;

            $zone_id = $this->model_localisation_zone->getZoneIdByName($state);
            $not_exist_states = array();
            $error_record = array();

            if ($zone_id <= 0) {
                $not_exist_states[] = $state;

                $error[] = array("row"=>$i+1, "message"=>"Zone ".$state." is not exist in our DB");

                echo "row - ".$i+1 ." message: Zone ".$state." is not exist in our DB";
                echo "<br />";
                $error_record = array($post_office, $pincode, $city, $state);
            }else{

                $sql = "INSERT INTO ".DB_PREFIX."pin_codes ".
                    " SET city = '".$city."', ".
                    " taluka = '".$taluka."', ".
                    " zone_id = '".$zone_id."',".
                    " country_id = '".$country_id."',".
                    " pin_code = '".$pincode."',".
                    " status = 1";

                $this->db->query($sql);
            }



        }
        fclose($file);
    }

    public function testTrigger(){
        $product_id = 37368;

        // Current last_modifed, model
        $query = $this->db->query("SELECT model, date_modified FROM oc_product 
                        WHERE product_id = '" . (int)$product_id . "'");

        echo "Current Model: " . $query->row['model']; echo "<br>";
        echo "Current Last Modified: " . $query->row['date_modified']; echo "<br>";

        $model = time();

        $this->db->query("UPDATE " . DB_PREFIX . "product SET model = '". $model . "' 
                           WHERE product_id = '" . (int)$product_id . "'");

        // New last_modifed, model
        $query = $this->db->query("SELECT model, date_modified FROM oc_product 
                        WHERE product_id = '" . (int)$product_id . "'");

        echo "New Model: " . $query->row['model']; echo "<br>";
        echo "New Last Modified: " . $query->row['date_modified']; echo "<br>";

    }

    public function updateNTDSinglesPrice() {

        $sql = "SELECT p2s.product_id, p.model
                    FROM oc_product_to_store p2s
                    INNER JOIN oc_product p
                    ON p2s.product_id = p.product_id
                    WHERE p2s.store_id = 16
                    AND p.model like '%-SNGL'";

        $query = $this->db->query($sql);
        foreach($query->rows as $key=>$values){
            $q = "SELECT price FROM oc_product WHERE product_id = ".$values['product_id'];
            $query_price = $this->db->query($q);
            foreach($query_price->rows as $product){

                $price1 = (2 * $product['price']);

                /*echo $update = "UPDATE oc_product_to_store SET store_price = '".(float)$price1."'
                           WHERE product_id = ".$values['product_id'] ."
                           AND store_id = 16;";
                           * */

                echo $update = "DELETE FROM oc_product_to_store  
				WHERE product_id = ".$values['product_id'] ."
				AND store_id = 0;";

                echo '<br />';
            }
        }
    }

    public function treasureHunt() {
        if(empty($this->request->get['clue'])) {
            echo 'Nice Try!!!';
            return true;
        }
        $mobile = (int)$this->request->get['clue'];

        if (strlen($mobile) != 10) {
            echo "Boom!!!";
            return true;
        }

        $q = "SELECT contact_csv_url 
               FROM ".DB_PREFIX."customer
               WHERE telephone = ".$mobile;


        $query = $this->db->query($q);

        if($query->num_rows > 0) {
            if (!empty($query->row['contact_csv_url']) && file_exists($query->row['contact_csv_url'])) {
                $csv_path = $query->row['contact_csv_url'];

                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->SMTPSecure = 'ssl';
                $mail->SMTPDebug = 2;
                $mail->Debugoutput = 'html';
                $mail->Host = $this->config->get('config_mail_smtp_hostname');
                $mail->Port = $this->config->get('config_mail_smtp_port');
                $mail->SMTPAuth = true;
                $mail->Username = $this->config->get('config_mail_smtp_username');
                $mail->Password = $this->config->get('config_mail_smtp_password');
                $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Rakesh Singh');
                $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');
                $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
                $mail->addCC(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
                
                $mail->Subject = 'CSV Wholesaler';
                $mail->Body = 'Please find the csv of '.$mobile.' in attachment';
                $mail->AddAttachment($csv_path);
                //send to admin;
                $mail->send();

                echo "Well Done!!!";
                exit;

            }


        }

    }

    public function huntWholesaler() {

        $sql = "SELECT c.customer_id, CONCAT( c.firstname, ' ', c.lastname ) as name , a.company, c.telephone, c.customer_type_id, CONCAT( a.address_1, ' ', a.address_2 ) as address , a.postcode, a.city, c.date_added
                FROM `oc_customer` c
                LEFT JOIN oc_address a ON a.address_id = c.address_id
                WHERE find_in_set( '4', c.customer_type_id ) <> 0
                ORDER BY c.date_added DESC ";

        $results = $this->db->query($sql)->rows;
        // echo "<pre>"; print_r($results); die;
        $today = Date('d_M_y');
        $dir_path =  DIR_SYSTEM . 'upload/assets/wholesaler/' . $today;
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0777, true);
        }
        $i = 1;
        $handle = fopen($dir_path . '/wholesalers.csv', 'w');

        $i = 1;
        foreach ($results as $result) {

            $name = $result['name'];
            $business = $result['company'];
            $mobile = $result['telephone'];
            $postcode = $result['postcode'];
            $address = $result['address'];
            $city = $result['city'];
            $date_added = $result['date_added'];
            $customer_type_id = $result['customer_type_id'];

            if ($i == 1) {
                $i++;
                $data = array('Name',
                    'Business Name',
                    'Mobile',
                    'Postcode',
                    'Address',
                    'City',
                    'Signed up Date',
                    'Customer Type
                    2 - Wholesaler
                    1 - Manufacturer
                    3 - Shopkeeper
                    Some selected multiples');

                fputcsv($handle,$data);
                $data = array($name, $business, $mobile, $postcode, $address, $city, $date_added,$customer_type_id);
                fputcsv($handle,$data);

            } else {
                $i++;
                $data = array($name, $business, $mobile, $postcode, $address, $city, $date_added,$customer_type_id);
                fputcsv($handle,$data);
            }

        }
        fclose($handle);

        if (file_exists($dir_path . '/wholesalers.csv')) {
            $csv_path = $dir_path . '/wholesalers.csv';

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'html';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Rakesh Singh');
            $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');
            $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
            $mail->addCC(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
            $mail->Subject = 'Wholesaler data';
            $mail->Body = 'Please find the csv of registered wholesalers';
            $mail->AddAttachment($csv_path);
            //send to admin;
            $mail->send();

            echo "Well Done!!!";
            exit;

        }

    }

    public function getDataForIncentive() {

        $this->load->model('staff/staff');
        $this->load->model('test/test');
        $incentive['month'] = isset($this->request->get['month'])? $this->request->get['month'] : 1;
        $incentive['year'] = isset($this->request->get['year'])? $this->request->get['year'] : 2017;
        $send_email = false;

        //Get list of active sales staff
        $staffs = $this->model_staff_staff->getStaffs('DirectSale', 1);
        $incentive_data = '';
        if (count($staffs) > 0) {
            $i = 0;

            if (!file_exists(DIR_SYSTEM . 'upload/assets/incentives')) {
                mkdir(DIR_SYSTEM . 'upload/assets/incentives', 0777, true);
            }

            $filename = DIR_SYSTEM . 'upload/assets/incentives/'.$incentive['month'].'-'.$incentive['year'].'.csv';

            $handle = fopen($filename, 'w');

            $data_head = array('Order#', 'Order Date', 'Version', 'Total', 'Credit Note', 'Net Amount' );
            fputcsv($handle,$data_head);

            foreach($staffs as $staff) {
                $send_email = true;
                $incentive_data[$i]['staff_name'] = $staff['name']."-".$staff['telephone'];;

                $incentive_data[$i]['staff_id'] = $staff['staff_id'];

                $staff_head = array($incentive_data[$i]['staff_name']);
                fputcsv($handle,$staff_head);

                $incentive_data[$i]['data'] = $this->model_test_test->getIncentiveData($staff['staff_id'], $incentive['month'], $incentive['year']);

                foreach($incentive_data[$i]['data'] as $data) {
                    echo '<pre>'; print_r($data);
                    fputcsv($handle,$data);
                }

                $i++;
            }
        }


        fclose($handle);
        if ( $send_email == true ) {
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'html';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Rakesh Singh');
            $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');
            $mail->addCC(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
            $mail->Subject = 'CSV Wholesaler';
            $mail->Body = 'Please find the incentive data for month '. $incentive['month'].' - '.$incentive['year'].' in attachment';
            $mail->AddAttachment($filename);
            //send to admin;
            $mail->send();

            echo "Well Done!!! E-mail sent";
            exit;
        }

        echo '<pre>';
        // print_r($incentive_data);
    }

   public function get_cancelled_orders() {
       $q = " SELECT o.order_no, o.total, o.firstname, o.lastname, o.date_added
                FROM oc_order o
                INNER JOIN oc_suborder osub ON o.order_id = osub.order_id
                WHERE MONTH( o.date_added ) =1
                AND YEAR( o.date_added ) =2017
                GROUP BY o.order_id
                HAVING max( order_status_id ) =2
                AND min( order_status_id ) =2
                LIMIT 0 , 30
                            ";

       //Get list of cancelled order with comments addedin history

       $q = "
            SELECT oh.suborder_id, o.order_no, o.total, o.firstname, o.lastname, o.date_added, oh.date_added AS cancel_date, oh.order_status_id, oh.comment
            FROM oc_order_history oh
            INNER JOIN oc_suborder osub ON osub.suborder_id = oh.suborder_id
            INNER JOIN oc_order o ON o.order_id = oh.order_id
            WHERE oh.order_status_id =2
            AND MONTH( oh.date_added ) =1
            AND YEAR( oh.date_added ) =2017
            ORDER BY oh.date_added DESC
            LIMIT 90 , 30
          ";
   }

    private function url_exists($file){
        $file_headers = @get_headers($file);
        if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
            return false;
        }
        else {
            return true;
        }
    }


    public function productCatalogPDF(){

        // Get popular products
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $products = $this->model_catalog_product->getPopularProducts(200);


        require_once(DIR_SYSTEM.'library/tcpdf/tcpdf.php');
        //$action = 'I';
        require_once(DIR_SYSTEM.'library/tcpdf/config/tcpdf_config.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);
        $pdf->SetTitle('Product Catalog');
        $pdf->SetSubject('Product Catalog');
        $pdf->SetKeywords('Product Catalog');

        $pdf->setPrintHeader(false);
        // $pdf->setPrintFooter(false);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        //$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetAutoPageBreak(true,0);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



        // set font
        $pdf->SetFont('times', '', 9);

        $pdf->addPage();
        // $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
        //$pdf->SetFont('times', '', 9);
        $pdf->writeHTML($this->addImage(DIR_IMAGE.'subscription_campaign/coverpage.jpg'), true, false, false, false, '');

        $pdf->addPage();

        $html = '';
        $html .= '<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td align="center"  valign="top">';
        $html .= '<table align="center" border="0" cellpadding="0" cellspacing="0"   width="100%">';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td align="center"  valign="top">';
        $html .= '<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
        $html .= '<tbody>';

        // $html .= $this->addImage(DIR_SYSTEM.'coverpage.jpg');
        $count = 0;
        foreach ( $products as $product ){

            //$image_url = $this->model_tool_image->resize($product['image'],$this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
            $image_url = $this->model_tool_image->resize($product['image'],267,400);

            if ( /*$this->url_exists($image_url)*/ true ) {

                if ($count % 2 == 0)
                { $html .= '<tr>'; }

                $html .= '<td align="center"  valign="middle" style="margin-top: 50px;">';
                $html .= $this->createProductHTML($product, $image_url);
                $html .= '</td>';
                if ($count % 2 != 0) { $html .= '</tr>';  }

                $count++;
            }
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->addPage();

        $pdf->writeHTML($this->addImage(DIR_IMAGE.'subscription_campaign/coverpage_back.jpg'), true, false, true, false, '');
        $pdf->lastPage();

        //Close and output PDF document
//        if(!file_exists(DIR_DLOAD_SLR_INV)){
//            mkdir(DIR_DLOAD_SLR_INV, 0777, true);
//        }

        $file_name = 'ProductCatalog.pdf';
        $action = 'D';
        $pdf->Output($file_name, $action);
        exit();
    }

    private function addImage($imageurl){
        $html = '<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td align="center"  valign="top">';
        $html .= '<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td align="center" valign="middle"><img alt="wholesalebox" src="'.$imageurl.'" /></td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }

    private function createProductHTML($product,$image_url){
        $product_url = $this->url->link('product/product', 'product_id=' . $product['product_id'], 'SSL');


        // $html = '<div class="block" width="300" > ';
        $html = '<table align="center" border="0" cellpadding="0" cellspacing="0" width="90%">';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td align="center"  valign="top">';
        $html .= '<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td height="10" > </td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td align="center" valign="top"><a href="'.$product_url.'" style="text-decoration: none; color:  #898788;"><img alt="wholesalebox" src="'.$image_url.'" height="360" /> </a></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td height="7" > </td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td align="left" style="font-family: Arial, sans-serif; font-size: 13px; mso-line-height-rule: exactly; line-height: 16px; font-weight: 400;color: #181818; " valign="top"><b><a href="'.$product_url.'" style="text-decoration: none; color:  #181818;">'.$product['name'].'</a></b></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td height="4" style="height: 4px; line-height:4px;"> </td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td align="left" style="font-family: Arial, sans-serif; font-size: 11px; mso-line-height-rule: exactly; line-height: 15px; font-weight: 400;color: #181818;" valign="top"><b>'.$product['model'].'</b></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td height="4" style="height: 4px; line-height:4px;"> </td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td align="left" style="font-family: Arial, sans-serif; font-size: 11px; mso-line-height-rule: exactly; line-height: 15px; font-weight: 400;color: #181818;" valign="top"><b>Rs. '.$product['selling_price'].'</b> / Piece</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td align="left" style="font-family: Arial, sans-serif; font-size: 10px; mso-line-height-rule: exactly; line-height: 16px; font-weight: 400;color: #181818;" valign="top"><b>Minimum Order: '.$product['minimum'].' Set</b></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td height="4" style="height: 4px; line-height:4px;"> </td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td height="4" style="height: 4px; line-height:4px;"> </td>';
        $html .= '</tr>';

        if ( (int)$product['margin_percentage'] != 0 ) {
            $html .= '<tr>';
            $html .= '<td align="center" valign="top">';
            $html .= '<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
            $html .= '<tbody>';
            $html .= '<tr>';
            $html .= '<td align="center" style="border: 1px solid #000;" valign="top">';
            $html .= '<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
            $html .= '<tbody>';
            $html .= '<tr>';
            $html .= '<td height="4" style="height: 4px; line-height:4px;"> </td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td align="center" style="font-family: Arial, sans-serif; font-size: 11px; mso-line-height-rule: exactly; line-height: 11px; font-weight: 700; text-transform: uppercase;color: #000;" valign="top">Your margin - ' . $product['margin_percentage'] . '%</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td height="4" style="height: 4px; line-height:4px;"> </td>';
            $html .= '</tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</td>';
            $html .= '</tr>';

        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        //$html .= '</div>';

        return $html;
    }

    public function lastSixMonthsOrderedData(){

        // open the file "last_six_months_ordered_data.csv" for writing
        $file = fopen(DIR_SYSTEM . 'upload/assets/last_six_months_ordered_data.csv', 'w');
        
        $sql = "SELECT c.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS name, count(o.order_id) AS total_order, SUM(so.total) AS total_amount
                FROM oc_customer AS c
                INNER JOIN oc_order AS o ON (c.customer_id = o.customer_id)
                INNER JOIN oc_suborder so ON (o.order_id = so.order_id)
                WHERE o.date_added > DATE_SUB(now(), INTERVAL 6 MONTH)
                AND so.order_status_id NOT IN (0,1,2,8)
                GROUP BY c.customer_id";
        $deliverd_order = $this->db->query($sql)->rows;

        foreach($deliverd_order as $order) {
            $customer[$order['customer_id']]['deliverd'] = $order;
        }

        $sql = "SELECT c.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS name, count(o.order_id) AS total_order, SUM(so.total) AS total_amount
                FROM oc_customer AS c
                INNER JOIN oc_order AS o ON (c.customer_id = o.customer_id)
                INNER JOIN oc_suborder so ON (o.order_id = so.order_id)
                WHERE o.date_added > DATE_SUB(now(), INTERVAL 6 MONTH)
                AND so.order_status_id = 1
                GROUP BY c.customer_id";
        $pending_order = $this->db->query($sql)->rows;

        foreach($pending_order as $order) {
            $customer[$order['customer_id']]['pending'] = $order;
        }

        // save the column headers
        fputcsv($file, array('Name', 'Processed Order\'s', 'Total Processed Order\'s Amount', 'Pending Order\'s', 'Total Pending Order\'s Amount'));
        
        // save each row of the data
        foreach ($customer as $value) {
            if( isset($value['deliverd']) && isset($value['pending']) ) {
                $row = array($value['deliverd']['name'], $value['deliverd']['total_order'], sprintf('%.2F', $value['deliverd']['total_amount']), $value['pending']['total_order'], sprintf('%.2F', $value['pending']['total_amount']));
            } elseif( isset($value['deliverd']) && !isset($value['pending']) ) {
                $row = array($value['deliverd']['name'], $value['deliverd']['total_order'], sprintf('%.2F', $value['deliverd']['total_amount']), '', '');
            } elseif( !isset($value['deliverd']) && isset($value['pending']) ) {
                $row = array($value['pending']['name'], '', '', $value['pending']['total_order'], sprintf('%.2F', $value['pending']['total_amount']));
            }
            fputcsv($file, $row);
        }
        
        // Close the file
        fclose($file);
        
    }

    public function multipleOrderTaging() {
        $query = $this->db->query("SELECT order_id, sales_staff_id FROM oc_order WHERE sales_staff_id != 0")->rows;
        foreach($query as $val) {
            $this->db->query("INSERT INTO oc_order_sales_staff SET order_id = ".$val['order_id'].", sales_staff_id = ".$val['sales_staff_id'].", user_id = 0, date_added = now()");
        }
    }

    public function syncSalesStaff(){
        $sales_staff = $this->db->query("SELECT staff_id, crm_user_id FROM `oc_sales_staff` WHERE crm_user_id != 0 ORDER BY `crm_user_id` ASC")->rows;
        $this->load->model('lead/lead');
        foreach($sales_staff as $val) {
            $users = $this->model_lead_lead->syncSalesStaff($val['crm_user_id']);
            if(!empty($users)){
                $this->db->query("UPDATE `oc_sales_staff` SET date_added = STR_TO_DATE('".$users['created']."', '%Y-%m-%d'), date_modified = STR_TO_DATE('".$users['modified']."', '%Y-%m-%d') WHERE crm_user_id =".$val['crm_user_id']);
            }
        }
    }

    public function getNetStaffSalesAppInstalledAndOrderedValue(){
        if(isset($this->request->get['sales_staff_id'])){
            $staff_id = $this->request->get['sales_staff_id'];

            $staff_ids = $this->db->query("SELECT crm_user_id, staff_id, name FROM oc_sales_staff WHERE staff_id IN (".$staff_id.") AND crm_user_id != 0")->rows;
            $user = array();
            $this->load->model('lead/lead');
            foreach($staff_ids as $staff_id){
                $user[$staff_id['crm_user_id']]['crm_user_id']  = $staff_id['crm_user_id'];
                $user[$staff_id['crm_user_id']]['staff_id']     = $staff_id['staff_id'];
                $user[$staff_id['crm_user_id']]['name']         = $staff_id['name'];

                // get Net App installed by sales staff using crm_id
                $net_app_installed = $this->model_lead_lead->getNetAppInstalledByStaffSales($staff_id['crm_user_id']);
                $user[$staff_id['crm_user_id']]['app_installed'] = $net_app_installed;
                
                // get Net Ordered valed by Sales staff using crm_id
                $net_ordered_value = $this->getNetOrdredValueBySalesStaff($staff_id['crm_user_id']);
                $user[$staff_id['crm_user_id']]['net_ordered_val'] = $net_ordered_value;
            }
            if(!empty($user)){
                $fp = fopen(DIR_LOGS . 'net_sales_value.csv', 'w');
                $data = array('CRM ID', 'Sales staff id',  'Staff name', 'Net app Installed', 'Net Ordered value', 'Date Added');
                fputcsv($fp, $data);
                foreach($user as $usr){
                    $data = array($usr['crm_user_id'], $usr['staff_id'], $usr['name'], $usr['app_installed'], $usr['net_ordered_val'], Date('Y-m-d H:i:s'));
                    fputcsv($fp, $data);
                }
                echo "check file at system/logs/net_sales_value.csv";
            } else{
                echo "No user found";
            }
        } else {
            echo "ENTER sales_staff_id in URL using comma seprated"; die;
        }
    }
    public function getNetOrdredValueBySalesStaff($crm_user_id) {
         $sql = "SELECT o.order_id,GROUP_CONCAT(oop.order_product_id) AS order_product,SUM((oop.price_per_piece + oop.discount_per_piece) * oop.piece_in_set * oop.quantity ) AS subTotal
                    FROM oc_order o  
                    INNER JOIN oc_suborder osub ON (osub.order_id = o.order_id)
                    INNER JOIN oc_order_sales_staff ooss ON (ooss.order_id = o.order_id)
                    INNER JOIN oc_sales_staff oss ON (ooss.sales_staff_id = oss.staff_id)
                    INNER JOIN oc_order_product oop ON (osub.order_id = oop.order_id)
                    WHERE oop.suborder_id = osub.suborder_id
                    AND oss.crm_user_id ='" . $crm_user_id . "'
                    AND osub.order_status_id NOT IN (0,1,2,8)
                    GROUP BY o.order_id
                    Order By o.order_id DESC";
        $result = $this->db->query($sql)->rows;
        $net_ordered_value  = 0;
        $net_return_total   = 0;
        foreach($result as $order_val){
            $return_amount      = $this->getNetReturnOrderTotal($order_val['order_product']);
            $net_return_total   = $net_return_total + $return_amount;
            $net_ordered_value  = $net_ordered_value + $order_val['subTotal'];
        }
        $net_ordered_value = $net_ordered_value - $net_return_total;
        return $net_ordered_value;
    }

    public function getNetReturnOrderTotal($order_product_id) {

        $returnTotal = 0;
        /*
         * We need to fetch max return id because a product can have multiple return status.
         */
        $sql = "SELECT max(return_id) AS return_id FROM oc_return AS ort WHERE ort.order_product_id IN ($order_product_id) GROUP BY ort.order_product_id";
        $max_id = $this->db->query($sql)->rows;

        $max_id = implode(",", array_column($max_id, 'return_id'));
        $data = array();
        if (!empty($max_id)) {
            $sql = "SELECT oop.order_id,
                ROUND(SUM((oop.price_per_piece + oop.discount_per_piece)  * ort.quantity )) AS returnTotal
                FROM oc_return ort  
                INNER JOIN oc_order_product oop ON (oop.order_product_id = ort.order_product_id)  
                INNER JOIN oc_seller_debit_note osd ON (osd.debit_note_id = ort.debit_note_id)  
                WHERE ort.order_product_id IN ($order_product_id) AND(ort.debit_note_id > 0 OR (return_id in ($max_id) AND (return_action_id BETWEEN 1 AND 6) ))
                AND osd.debit_note_status = 1
                GROUP BY oop.order_id LIMIT 1";
            $data = $this->db->query($sql)->rows;
        }

        if (isset($data[0]['returnTotal']) && !empty($data[0]['returnTotal'])) {
            return $data[0]['returnTotal'];
        }
        return $returnTotal;
    }

    public function importCartDataInBigQuery(){

        // Create a CSV for insert a cart data along with customer
        $fp = fopen(DIR_LOGS . 'CartData.csv', 'w');
        $data = array('customer_id', 'customer_name', 'customer_preference', 'customer_status', 'date_added', 'is_dropshipper', 'app_version', 'postcode', 'city', 'state_name', 'country_name', 'total_order_placed', 'total_order_amount', 'product_id', 'cart_quantity', 'model', 'exclusive', 'date_added', 'name', 'category_name');
        fputcsv($fp, $data);
        
        // GET Products data on the basis of customer cart 
        $sql = "SELECT c.customer_id, c.firstname, c.lastname, c.status, c.date_added, c.is_dropshipper, c.app_version, 
                        GROUP_CONCAT(DISTINCT(cd.name)) as customer_preference,
                        a.postcode, a.city, 
                        z.name as state_name, 
                        cou.name as country_name,
                        cc.cart_data
                    FROM oc_customer c
                    LEFT JOIN oc_customer_preference cp ON (c.customer_id = cp.customer_id)
                    LEFT JOIN oc_category_description cd ON (cp.category_id = cd.category_id)
                    INNER JOIN oc_address a ON (c.customer_id = a.customer_id)
                    INNER JOIN oc_zone z ON (a.zone_id = z.zone_id)
                    INNER JOIN oc_country cou ON (z.country_id = cou.country_id)
                    INNER JOIN oc_customer_cart cc ON (c.customer_id = cc.customer_id)
                    WHERE cd.language_id = 1
                    GROUP BY c.customer_id
                    ORDER BY c.customer_id ASC";
        $result = $this->db->query($sql)->rows;

        $cart_data = array();
        $i = 0;
        foreach( $result as $val ) {
            $cart_data[$i]['customer_id']           = $val['customer_id'];
            $cart_data[$i]['firstname']             = $val['firstname'];
            $cart_data[$i]['lastname']              = $val['lastname'];
            $cart_data[$i]['customer_preference']   = $val['customer_preference'];
            $cart_data[$i]['status']                = $val['status'];
            $cart_data[$i]['date_added']            = $val['date_added'];
            $cart_data[$i]['is_dropshipper']        = $val['is_dropshipper'];
            $cart_data[$i]['app_version']           = $val['app_version'];
            $cart_data[$i]['postcode']              = $val['postcode'];
            $cart_data[$i]['city']                  = $val['city'];
            $cart_data[$i]['state_name']            = $val['state_name'];
            $cart_data[$i]['country_name']          = $val['country_name'];


            // GET toatl orderd amount and and total order placed by customer
            $sql = "SELECT count(o.order_id) as total_order_placed, SUM(o.total) as total_order_amount
                    FROM oc_customer c
                    LEFT JOIN oc_order o ON (c.customer_id = o.customer_id)
                    LEFT JOIN oc_suborder so ON (o.order_id = so.order_id)
                    WHERE so.order_status_id NOT IN (0,1,2,8) AND c.customer_id =".$val['customer_id'];
            $order_result = $this->db->query($sql)->row;

            $cart_data[$i]['total_order_placed']    = $order_result['total_order_placed'];
            $cart_data[$i]['total_order_amount']    = $order_result['total_order_amount'];
            
            $cart_data_arr = unserialize($val['cart_data']);
            // $cart_data[$i]['old_cart_data']         = $cart_data_arr;
            $inserted_into_csv = 0;

            foreach ( $cart_data_arr as $key => $quantity ) {
			    $products = unserialize(base64_decode($key));
                $product_id = $products['product_id'];
                $sql = "SELECT p.product_id, p.model, p.exclusive, p.date_added, pd.name, GROUP_CONCAT(DISTINCT(cd.name)) as category_name
                        FROM oc_product p
                        INNER JOIN oc_product_description pd ON (p.product_id = pd.product_id)
                        INNER JOIN oc_product_to_category ptc ON (p.product_id = ptc.product_id)
                        INNER JOIN oc_category_description cd ON (ptc.category_id = cd.category_id)
                        WHERE p.product_id = ".$product_id." AND cd.language_id = 1
                        GROUP BY p.product_id";
                $product_result = $this->db->query($sql)->row;

                $cart_data[$i]['products'][$product_id]['cart_quantity'] = $quantity;
                $cart_data[$i]['products'][$product_id]['product_info'] = $product_result;

                // Insert data of products 
                if(!empty($product_result)){
                    $inserted_into_csv = 1;
                    $data = array($val['customer_id'], $val['firstname']." ".$val['lastname'], $val['customer_preference'], $val['status'], $val['date_added'], $val['is_dropshipper'], $val['app_version'], $val['postcode'], $val['city'], $val['state_name'], $val['country_name'], $order_result['total_order_placed'], $order_result['total_order_amount'], $product_result['product_id'], $quantity, $product_result['model'], $product_result['exclusive'], $product_result['date_added'], $product_result['name'], $product_result['category_name']);
                    fputcsv($fp, $data);
                }else{
                    $inserted_into_csv = 0;
                }
            }

            if($inserted_into_csv == 0){
                $data = array($val['customer_id'], $val['firstname']." ".$val['lastname'], $val['customer_preference'], $val['status'], $val['date_added'], $val['is_dropshipper'], $val['app_version'], $val['postcode'], $val['city'], $val['state_name'], $val['country_name'], $order_result['total_order_placed'], $order_result['total_order_amount'], '', '', '', '', '', '', '');
                fputcsv($fp, $data);
            }
            $i++;
        }

        echo "<pre>"; print_r($cart_data); die;
    }

    public function updateDiscountPerPieceForOldOrders(){
        // $discount_types = array('paycharge','coupon','cashback','discount','deal_discount');

        $sql_dis = "SELECT SUM(oot.value) as discount_value, o.order_id
                FROM oc_order o 
                INNER JOIN oc_order_total oot 
                  ON (oot.order_id = o.order_id)
                WHERE o.code_version = '1.0' 
                  AND oot.code IN ('paycharge','coupon','cashback','discount','deal_discount')
                GROUP BY o.order_id";
        $query_dis = $this->db->query($sql_dis);
        
        // array of key - value pair of order id with discount
        $query_dis_array = array();
        foreach( $query_dis->rows as $values ){
            $query_dis_array[$values['order_id']] = -abs($values['discount_value']);
        }

        $sql_sub_total = "SELECT SUM(oot.value) as sub_total_value, o.order_id
                          FROM oc_order o 
                          INNER JOIN oc_order_total oot 
                            ON (oot.order_id = o.order_id)
                          WHERE o.code_version = '1.0' 
                            AND oot.code  = 'sub_total'
                          GROUP BY o.order_id";
        $query_sub_total = $this->db->query($sql_sub_total);
        
        // array of  key - value pair of order id with sub total
        $query_sub_array = array();
        foreach( $query_sub_total->rows as $values ){
            $query_sub_array[$values['order_id']] = $values['sub_total_value'];
        }

        // get discount percentage
        $discount_percentage = array();
        foreach( $query_sub_array as $key_order_id => $values_sub_total){
            $discount = 0;
            if (isset($query_dis_array[$key_order_id])) {
                $discount = (float)$query_dis_array[$key_order_id];
            }
            $discount_percentage[$key_order_id] = ($discount * 100) / $values_sub_total;
        }
        

        $sql_product = "SELECT oop.order_id, oop.price_per_piece, oop.order_product_id FROM oc_order o
                        INNER JOIN oc_order_product oop
                          ON ( oop.order_id = o.order_id )
                        WHERE o.code_version = '1.0'";
        $query_product = $this->db->query($sql_product);                
        

         // update order product table with discount per price
        foreach( $query_product->rows as $datas){
            $discount_per_piece = round(((float)$datas['price_per_piece'] * (float)$discount_percentage[$datas['order_id']]) / 100, 2);
            
            $this->db->query("UPDATE oc_order_product 
                              SET discount_per_piece = '" . $discount_per_piece . "' 
                              WHERE order_product_id = '" . (int)$datas['order_product_id'] . "'");            
        }
    }


    public function recoverClearCartAccident()
    {
        $sql_cart = "SELECT customer_id, count( customer_id ) AS count, cart_type, cart_history, date_added
                        FROM oc_wsb_cart_history
                        WHERE DATE( date_added ) = DATE( '2017-06-20' )
                        GROUP BY customer_id
                        HAVING count = 1 AND cart_type = 'accident'
                        ORDER BY count( customer_id ) DESC ";
        $rs_cart = $this->db->query($sql_cart);

        if ($rs_cart->num_rows > 0) {

            $i = 1;
            foreach ($rs_cart->rows as $cart_his_data) {

                $sql_check_customer_exist = $this->db->query("Select * from oc_customer_cart where customer_id = " . $cart_his_data['customer_id']);
                if ($sql_check_customer_exist->num_rows > 0) {

                    // do nothing
                } else {
                    echo $i . "__";

                    $get_data = explode(',', $cart_his_data['cart_history']);
                    $get_cart = (substr(str_replace('"cart_data":"', "", $get_data[1]), 0, -1));


                    $get_date = (substr(str_replace('"date_added":"', "", $get_data[2]), 0, -2));

                    echo $ins_sql = "Insert into oc_customer_cart set customer_id = " . $cart_his_data['customer_id'] . ", 
											cart_session_id = 0,
											parent_cart_id = '',
											cart_data = '" . $get_cart . "',
											user_agent = '',
											ip = '',
											app_version	 = 0,
											cart_history =  '" . $get_cart . "',
											date_added =  '" . $get_date . "', 
											date_modified = '" . $get_date . "' ";
                    $rtn = $this->db->query($ins_sql);

                    echo "<br/> result: " . $rtn;

                    $newInsertedId = $this->db->getLastId();

                    $this->db->query("update oc_customer_cart set parent_cart_id = '" . $newInsertedId . "' where customer_id = " . $cart_his_data['customer_id'] . " ");
                    echo "<br/>";

                    $i++;
                }

            }

        } else {

            echo "no record found";

        }
    }

    public function addNewFieldInSolr(){
        $sql = "SELECT product_id, hsn_code FROM oc_product WHERE hsn_code != ''";
        $results = $this->db->query($sql)->rows;
        //echo "<pre>"; print_r($results); die;
        foreach($results as $result){
            $filter = array();
            $filter['product_id']           = $result['product_id'];
            $filter['fields']['hsn_code']   = $result['hsn_code'];
            $results = SolrProduct::atomicUpdateToSolr($filter);
        }
    }


    /* Method for update seller invoice id in oc_order_product
     * @author: vikas, 2017
    */

    public function updateSellerInvoiceIdInOrderProduct(){
        $sql = "UPDATE oc_order_product oop
                INNER JOIN  oc_seller_invoice osi 
                  ON (oop.order_id = osi.order_id )
                SET oop.seller_invoice_id = osi.seller_invoice_id ,
                    oop.edit_type = 'SELLER_APPROVED'
                WHERE oop.suborder_id = osi.suborder_id 
                  AND oop.seller_id = osi.seller_id 
                  AND oop.seller_invoice_id = 0";

        $this->db->query($sql);
    }
	
    public function updateSellerInvoiceIdInOrderProductNew(){
        $sql = "UPDATE oc_order_product oop
                INNER JOIN  oc_seller_invoice osi 
                  ON (oop.order_id = osi.order_id )
                SET oop.edit_type = 'SELLER_APPROVED'
                WHERE oop.suborder_id = osi.suborder_id 
                  AND oop.seller_id = osi.seller_id 
                  AND (oop.seller_invoice_id > 0
		       OR oop.seller_invoice_id IS NULL)
		  AND oop.edit_type = 'YES'";

        $this->db->query($sql);
    }
    
    /**
     * method outputs a csv with the return order details
     * */

     public function getReturnOrdersDetails() {	
       
		$sql = "SELECT 	o.order_no,
						osub.suborder_id,
						oop.name as product_name,
						oop.model,
						oop.seller_sku,
						o.date_added as order_date,
						r.date_added as return_date,
						ms.nickname,
						ms.company,
						rr.name as return_reason,
						r.quantity as return_pieces,
						(oop.quantity)*(oop.piece_in_set) as original_pieces,
						r.comment,
						r.internal_note,
						op.price as current_price, 
						oop.price_per_piece as order_price, 
						o.payment_method, 
                        o.order_from 
					FROM oc_seller_debit_note osdn
					INNER JOIN oc_return r
					ON (osdn.debit_note_id = r.debit_note_id)
					INNER JOIN oc_return_reason rr
					ON (rr.return_reason_id = r.return_reason_id)
					INNER JOIN oc_order_product oop
					ON (oop.order_product_id = r.order_product_id)
					INNER JOIN oc_ms_seller ms
					ON ms.seller_id = oop.seller_id
					INNER JOIN oc_order o 
					ON (o.order_id = oop.order_id) 
					INNER JOIN oc_suborder osub 
					ON (osub.order_id = o.order_id)
                    INNER JOIN oc_product op 
                    ON (op.product_id = oop.product_id) 
					WHERE osdn.debit_note_status = 1
					AND osub.suborder_id = oop.suborder_id 
					ORDER BY osdn.debit_note_id ASC ";
								
		$query = $this->db->query($sql);
		$results = $query->rows;
		
        $i = 1;
        $fp = fopen(DIR_SYSTEM . 'upload/assets/return_order.csv', 'w+');
		
        foreach ($results as $result) {
			$order_number = $result['order_no'];
			$sub_order_id = $result['suborder_id'];
			$order_date = $result['order_date'];
			$payment_method = $result['payment_method'];
			$model = $result['model'];
			$seller_sku = $result['seller_sku'];
			$seller_code = $result['nickname'];
			$seller_company = $result['company'];
			$product_name = $result['product_name'];
			$original_pieces = $result['original_pieces'];
			$return_date = $result['return_date'];
			$return_pieces = $result['return_pieces'];
			$return_reason = $result['return_reason'];
			$comment = $result['comment'];
			$internal_notes = $result['internal_note'];
			$current_price = $result['current_price'];
			$order_price = $result['order_price'];
            $order_from = $result['order_from'];

			
            if ($i == 1) {
                $i++;
                $data = array('Order No', 'Suborder No', 'Order Date', 'Payment Method', 'Order Mode', 'WSB Product Code',
								'Seller SKU Code', 'Seller Code','Seller Firm Name', 'Product Name', 'Original Pieces Ordered', 
								'Return Date', 'Returned Pieces', 'Return Reason', 'Additional Comments', 
								'Internal Notes' , 'Order Price per piece' , 'Current Price per piece');
            } else {
                $i++;
                $data = array($order_number, 
								$sub_order_id, $order_date, $payment_method, $order_from, $model, $seller_sku, $seller_code,
								$seller_company, $product_name, $original_pieces , $return_date, $return_pieces,
								$return_reason,$comment,$internal_notes,$order_price,$current_price);
            }
            
            fputcsv($fp, $data);
        }
        
        fclose($fp);

    }


    public function createReturnNo(){
        $sql  = "SELECT max(master_return_id) as return_no ";
        $sql .= "FROM ".DB_PREFIX."master_return ";
        $query = $this->db->query($sql);
        if($query->row['return_no']){
            $return_no = $query->row['return_no'] + 1;
        }else{
            $return_no = 1;
        }

        return $return_no;
    }

    public function addMasterReturn($order_id, $customer_id, $shipping_method){
        $financial_year = '';
        if ( (int)(date('m')) <= 3 ) {
            $financial_year = date('Y', strtotime('-1 years')) . '-' . date('y');
        } else {
            $financial_year = date('Y') . '-' . date('y',strtotime('+1 years'));
        }

        $return_no_prefix = 'WSB-RTN-' . $financial_year . '_';
        $user_id = 0;
        if($customer_id == 0){
            $user_id = 1;
        }

        $return_no = $this->createReturnNo();
        $return_no = $return_no_prefix.$return_no;
        $sql = "INSERT INTO ".DB_PREFIX."master_return SET
                order_id = '".$order_id."',
                return_no = '".$return_no."',
                user_id= '".$user_id."',
                customer_id= '".$customer_id."',
                shipping_method = '" . $this->db->escape($shipping_method) . "',
                date_added = NOW()";   
        if($this->db->query($sql)){
            return $this->db->getLastId();
        }else{
            return 0;
        }
    }

    public function addReturn() {
        $sql =  "SELECT oop.order_id, ocr.customer_id, ocr.master_return_id, ocr.shipping_method
                    FROM oc_order_product as oop
                  INNER JOIN (
                        SELECT * from oc_return ORDER BY return_id
                        ) 
                    as ocr ON oop.order_product_id = ocr.order_product_id
                  WHERE ocr.master_return_id = 0
                  GROUP BY oop.order_id
                ";

        $result = $this->db->query($sql);
        $result = $result->rows;
        foreach ($result as $key => $data) {
            $master_return_id = $this->addMasterReturn($data['order_id'],$data['customer_id'],$data['shipping_method']);
            $sql = "UPDATE oc_return as ocr
                    INNER JOIN oc_order_product as oop ON oop.order_product_id = ocr.order_product_id
                    SET ocr.master_return_id = '".$master_return_id."'
                    where ocr.master_return_id = 0 AND oop.order_id = '".$data['order_id']."'
                    ";
            $this->db->query($sql);
        }
        echo "Process Completed Successfully.";

    }
     
    /*
     * updateProductOptionSizeFilter
     * Comment: This method get the size option of all the products and will make entry in oc_product_filter table
     * author:Devendra Dhayal
     * Date:27-09-2017
     * */
    public function updateProductOptionSizeFilter(){
        $sql = "SELECT product_id,option_value_id FROM oc_product_option_value WHERE option_id = 11 AND quantity > 0";
        $results = $this->db->query($sql);
        $total_records = $results->num_rows;
        echo "Total records found:".$total_records;
        if($total_records == 0) return;
        $results = $results->rows;
        $optionToFilterMap = array();
        foreach ($results as $result){
            if(array_key_exists($result['option_value_id'],$optionToFilterMap)){
                $filter_id = $optionToFilterMap[$result['option_value_id']];
            }
            else{
                $sql = "SELECT name FROM oc_option_value_description  WHERE option_value_id = ".(int)$result['option_value_id']." AND language_id = 1 LIMIT 1";
                $temp = $this->db->query($sql);
                if(!$temp->num_rows) {
                    continue;
                }
                $filter_name = $temp->row['name'];
                $sql = "SELECT filter_id FROM oc_filter_description WHERE name = '".$filter_name."' AND language_id = 1 LIMIT 1";
                $temp = $this->db->query($sql);
                if(!$temp->num_rows) {
                    $filter_group_id = 9;
                    $sql = "INSERT INTO oc_filter SET filter_group_id = ".$filter_group_id.", sort_order = 0";
                    $this->db->query($sql);
                    $sql = "SELECT LAST_INSERT_ID() as filter_id";
                    $filter_id = $this->db->query($sql)->row['filter_id'];
				    $sql = "INSERT IGNORE INTO oc_filter_description SET filter_id = ".(int)$filter_id.", language_id = 1, name = '".$filter_name."'";
                    $this->db->query($sql);
				    $sql = "SELECT o.name FROM oc_option_value_description o WHERE o.option_value_id = ".$result['option_value_id']." AND o.language_id = 2 LIMIT 1";
				    $filter_name_hindi = $this->db->query($sql)->row['name'];
                    $sql = "INSERT IGNORE INTO oc_filter_description SET filter_id = ".(int)$filter_id.", language_id = 2, name = '".$filter_name_hindi."'";
                    $this->db->query($sql);
                }
                else{
                    $filter_id = $temp->row['filter_id'];
                }
                $optionToFilterMap[$result['option_value_id']] = $filter_id;
            }
            $sql = "INSERT IGNORE INTO oc_product_filter SET product_id = ".(int)$result['product_id'].", filter_id = ".(int)$filter_id;
            $this->db->query($sql);
        }
    }

    /*
     * updateProductOptionColorFilter
     * Comment: This method get the color option of all the products and will make entry in oc_product_filter table
     * author:Devendra Dhayal
     * Date:27-09-2017
     * */
    public function updateProductOptionColorFilter(){
        $sql = "SELECT product_id,option_value_id FROM oc_product_option_value WHERE option_id = 5 AND quantity > 0";
        $results = $this->db->query($sql);
        $total_records = $results->num_rows;
        echo "Total records found:".$total_records;
        if($total_records == 0) return;
        $results = $results->rows;
        $optionToFilterMap = array();
        foreach ($results as $result){
            if(array_key_exists($result['option_value_id'],$optionToFilterMap)){
                $filter_id = $optionToFilterMap[$result['option_value_id']];
            }
            else{
                $sql = "SELECT name FROM oc_option_value_description  WHERE option_value_id = ".(int)$result['option_value_id']." AND language_id = 1 LIMIT 1";
                $temp = $this->db->query($sql);
                if(!$temp->num_rows) {
                    continue;
                }
                $filter_name = $temp->row['name'];
                $sql = "SELECT filter_id FROM oc_filter_description WHERE name = '".$filter_name."' AND language_id = 1 LIMIT 1";
                $temp = $this->db->query($sql);
                if(!$temp->num_rows) {
                    $filter_group_id = 8;
                    $sql = "INSERT INTO oc_filter SET filter_group_id = ".$filter_group_id.", sort_order = 0";
                    $this->db->query($sql);
                    $sql = "SELECT LAST_INSERT_ID() as filter_id";
                    $filter_id = $this->db->query($sql)->row['filter_id'];
                    $sql = "INSERT IGNORE INTO oc_filter_description SET filter_id = ".(int)$filter_id.", language_id = 1, name = '".$filter_name."'";
                    $this->db->query($sql);
                    $sql = "SELECT o.name FROM oc_option_value_description o WHERE o.option_value_id = ".$result['option_value_id']." AND o.language_id = 2 LIMIT 1";
                    $filter_name_hindi = $this->db->query($sql)->row['name'];
                    $sql = "INSERT IGNORE INTO oc_filter_description SET filter_id = ".(int)$filter_id.", language_id = 2, name = '".$filter_name_hindi."'";
                    $this->db->query($sql);
                }
                $filter_id = $temp->row['filter_id'];
                $optionToFilterMap[$result['option_value_id']] = $filter_id;
            }
            $sql = "INSERT IGNORE INTO oc_product_filter SET product_id = ".(int)$result['product_id'].", filter_id = ".(int)$filter_id;
            $this->db->query($sql);
        }
    }

    public function testMail(){
        $mail = new PHPMailer;
        $mail->isSMTP();
        // change this to 0 if the site is going live
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        $mail->Host = "smtp.sendgrid.net"; //'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = 'ssl';

        //use SMTP authentication
        $mail->SMTPAuth = true;
        
        $mail->setFrom(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
        $mail->Subject = "Test Mail Scraper";
        // $message is gotten from the form
        $html = "test mail content";
        $mail->msgHTML($html);
    }

    public function createTree(&$list, $parent){
        $tree = array();
        foreach ($parent as $k=>$l){
            if(isset($list[$l['id']])){
                $l['children'] = $this->createTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        }
        return $tree;
    }


    public function copyMenu(){

        $results = $this->db->query("SELECT * from oc_menu_item where menu_id = 7 and store_id = 0 and language_id = 1 and status = 1 order by parent_id, id");
        $arr = array();
        foreach ($results->rows as $val){
            $arr[] = $val;
        }

        $new = array();
        foreach ($arr as $a){
            $new[$a['parent_id']][] = $a;
        }
        $tree = $this->createTree($new, $new[0]); // changed


        foreach($tree as $arrVal){

            $tt = $this->db->query("INSERT INTO oc_menu_item set `menu_id` = '8', 
                                                           `link_title` = '".$arrVal['link_title']."',
                                                           `link_type` = '".$arrVal['link_type']."',
                                                           `value` = '".$arrVal['value']."',
                                                           `type` = '".$arrVal['type']."',
                                                           `parent_id` = '0',
                                                           `position` = '".$arrVal['position']."',
                                                           `status` = '".$arrVal['status']."',
                                                           `store_id` = '2',
                                                           `megamenu` = '".$arrVal['megamenu']."',
                                                           `language_id` = '".$arrVal['language_id']."' ");

            if(!empty($arrVal['children'])){
                $newParent_id = $this->db->getLastId();

                foreach($arrVal['children'] as $firstChild){

                    $this->db->query("INSERT INTO oc_menu_item set `menu_id` = '8', `link_title` = '".$firstChild['link_title']."', `link_type` = '".$firstChild['link_type']."', `value` = '".$firstChild['value']."', `type` = '".$firstChild['type']."', `parent_id` = '".$newParent_id."', `position` = '".$arrVal['position']."', `status` = '".$firstChild['status']."', `store_id` = '2',`megamenu` = '".$firstChild['megamenu']."', `language_id` = '".$firstChild['language_id']."' ");

                    if(!empty($firstChild['children'])) {
                        $newSParent_id = $this->db->getLastId();

                        foreach($firstChild['children'] as $secondChild){
                            $this->db->query("INSERT INTO oc_menu_item set `menu_id` = '8',`link_title` = '".$secondChild['link_title']."',`link_type` = '".$secondChild['link_type']."',`value` = '".$secondChild['value']."',`type` = '".$secondChild['type']."',`parent_id` = '".$newSParent_id."',`position` = '".$secondChild['position']."',`status` = '".$secondChild['status']."',`store_id` = '2',`megamenu` = '".$secondChild['megamenu']."',`language_id` = '".$secondChild['language_id']."' ");

                            if(!empty($secondChild['children'])) {
                                    $newTParent_id = $this->db->getLastId();

                                    foreach($secondChild['children'] as $thirdChild) {
                                    $this->db->query("INSERT INTO oc_menu_item set `menu_id` = '8', `link_title` = '" . $thirdChild['link_title'] . "', `link_type` = '" . $thirdChild['link_type'] . "',`value` = '" . $thirdChild['value'] . "', `type` = '" . $thirdChild['type'] . "',`parent_id` = '" . $newTParent_id . "',`position` = '" . $thirdChild['position'] . "',
                                                           `status` = '" . $thirdChild['status'] . "',`store_id` = '2',`megamenu` = '" . $thirdChild['megamenu'] . "', `language_id` = '" . $thirdChild['language_id'] . "' ");
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     * @info : Public method to download all CNs
     * @author: Nishu, Jan 2018
    */
    public function downloadAllCN(){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $sql = "SELECT * 
                 FROM oc_credit_note
                 WHERE credit_note_status = 1
                 AND net_refundable IS NULL
                 ";
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            foreach ($result->rows as $cn_detail) {
                $encode_file = array();
                $encode_file['order_id'] = $cn_detail['order_id'];
                $encode_file['credit_note_id'] = $cn_detail['credit_note_id'];
                $encode_file = base64_encode(serialize($encode_file));
                $cn_obj = new CreditNote($this->registry, $encode_file, false);
                $html = $cn_obj->getFile();
            }
        }
        echo "Completed";
    }
    
    public function testNilesh() {
        $order_id = '29435';
        $suborder_id = '20180106470-JP-2';
        $order_product_id = array(
            /*'301233',*/
            '301234'
        );
        $qty_arr = array(
            /*'301233' => 4,*/
            '301234' => 2
        );
//         $order_id = '29433';
//        $suborder_id = '20180106468-DL';
//        $order_product_id = array('301222', '301221');
//        $qty_arr = array(
//            '301222' => 4,
//            '301221' => 2
//        );
        $edit_history = array();
        OrderEdit::updateSplittedSuborderOrderProductsQtyWise($this, $order_id, $suborder_id, $order_product_id, $edit_history, $qty_arr);
    }

    /**
     * Method to get the cart products using customer's mobile no
     * if more than one customer found against the telephone no, then will return with error,
     *
     * @author: Devendra
     * @date: 02-02-2018
     */
    public function getCartUsingTelephone() {
        // check for telephone in request
        if (!isset($this->request->get['telephone'])) {
            die('Error: Request does not have telephone');
        }

        // get telephone
        $telephone = $this->request->get['telephone'];

        // get customer using telephone
        $this->load->model('test/test');
        $customer_data = $this->model_test_test->getCustomerByTelephone($telephone);

        // if no customer found, return with error.
        if(empty($customer_data)) {
            die('No customer found against telephone no: '.$telephone);
        }

        // if more than one customer found, return with error
        if(sizeof($customer_data) > 1) {
            die('More than one customers found against telephone no: '.$telephone);
        }

        $customer = $customer_data[0];

        // get customer cart
        $cart_products = $this->model_test_test->getCartProducts($customer['customer_id']);

        exit;

    }

    /***
     * test method to send notifications to sellers
     ***/
    public function sendPushNotificationToSellers(){
        $cids = array();

        $cids[] = 'e_bvjRW29Dc:APA91bED6MWNFG6PKN2ysJs12mCGgS6NDI2QlexzIUXa8zcQkqQWg6yPTAt3iRFn17QzZtUWqZZH6w4WKESY_u1A9osTo7jSWAGz2a5wuHbO6YTpyHcUwr9oqEaF5VhuYlfJUAJPbyyZ';
        //$cids[] = 'fb2eNDP4XP8:APA91bEkDljtQv6ploq9qNagpMtYz8nfusnpjeAoRSU25gE6uvMS1eBiHyCAOIA4IJ4aEy2u-8F86DniQ1ObDh4c1fbG3ihPnjV8Dleh8qEOfhKskeQEbHmDxIGWhEAHnSrqhWOFPI8v';
        $msg = array(
            'custom_notification' => array
            (
                'sound' => 'default',
                'title' 	=> 'Greetings from Wholesalebox!',
                'body'    => 'Test push notifications',
                'large_icon' => 'https://cdnimages.net/img/wsbseller.png',
                'order_id' => '1629',
                'suborder_id' =>'20171202873-JP',
                'priority'      => 'high',
                'show_in_foreground' => true,
                'type' => 'PickUpOrder' // user will be redirected to order detail page
            ),
            'order_id' => '1629',
            'suborder_id' => '20171202873-JP',
            'priority'      => 'high',
            'show_in_foreground' => true,
            'type' => 'PickUpOrder' // user will be redirected to order detail page
        );

        // send notification
        $notification = new Notification($cids, $msg);
        $result = $notification->sendPushNotificationToSellers();
        echo "<pre>"; print_r($result); die;
    }
    
    public function getAllCourierPartner() {
        $result = array();
        $sql = "SELECT * FROM oc_courier_partners";
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            $result = $query->rows;
        }
        return $result;
    }

    public function insertOldCodOrdersDocketsNotExistsInTable() {
        $all_courier_p = $this->getAllCourierPartner();

        $sql = "SELECT osub.order_id,
                       osub.suborder_id,
                       osub.tracking_no,
                       osub.courier_partner,
                       osub.total
                FROM oc_order oo INNER JOIN 
                     oc_suborder osub ON oo.order_id=osub.order_id
                WHERE oo.date_added>DATE('2017-05-31') AND
                      osub.gst = '1' AND
                      oo.payment_code='cod' AND
                      osub.tracking_no IS NOT NULL AND
                      osub.courier_partner IS NOT NULL";
        $order_data = $this->db->query($sql);

        $return_final_arr = array();
        if ($order_data->num_rows) {
            $order_info = $order_data->rows;
            foreach ($order_info as $key => $value) {
                $order_id = $value['order_id'];
                $suborder_id = $value['suborder_id'];
                $sql = "SELECT ocd.id
                        FROM oc_courier_dockets ocd INNER JOIN 
                             oc_courier_partners ocp ON ocd.courier_partners_id=ocp.id
                        WHERE ocd.order_id='" . (int) $order_id . "' AND
                              ocd.suborder_id ='" . $this->db->escape($suborder_id) . "' AND
                              ocp.courier_name='" . $this->db->escape($value['courier_partner']) . "' AND
                              ocd.docket_no='" . $this->db->escape($value['tracking_no']) . "'";
                $courier_query = $this->db->query($sql);

                if ($courier_query->num_rows) {
                    continue;
                } else {
                    $courier_part_id = 0;
                    foreach ($all_courier_p as $couriers) {
                        if ($couriers['courier_name'] == $value['courier_partner']) {
                            $courier_part_id = $couriers['id'];
                            break;
                        }
                    }
                    $parcel_amount = (float) $value['total'];
                    $payment_mode = 'cod';
                    $cod_amount = 0;
                    $buyer_invoice = new BuyerInvoice($this->registry);
                    $total_data = $buyer_invoice->getTotals($order_id, $suborder_id);
                    if (!empty($total_data['net_amount'])) {
                        $cod_amount = (float) $total_data['net_amount']['value'];
                        if ($cod_amount==0) {
                            $payment_mode = 'prepaid';
                            $cod_amount = 0;
                        }
                    }

                    $return_final_arr[] = array(
                        'order_id' => $order_id,
                        'suborder_id' => $suborder_id,
                        'courier_partners_id' => $courier_part_id,
                        'docket_no' => $value['tracking_no'],
                        'payment_mode' => $payment_mode,
                        'parcel_amount' => $parcel_amount,
                        'cod_amount' => $cod_amount
                    );
                }
            }
        }

        if (!empty($return_final_arr)) {


            $sql_insert = "INSERT INTO oc_courier_dockets (`id`, `order_id`, `suborder_id`, `courier_partners_id`, `docket_no`, `payment_mode`, `cod_amount`, `parcel_amount`) VALUES ";
            foreach ($return_final_arr as $key => $value) {
                $sql_insert .= '("", ' . (int) $value['order_id'] . ', "' . $this->db->escape($value['suborder_id']) . '", ' . (int) $value['courier_partners_id'] . ', "' . $this->db->escape($value['docket_no']) . '", "' . $this->db->escape($value['payment_mode']) . '", ' . (float) $value['cod_amount'] . ', ' . (float) $value['parcel_amount'] . '),';
            }
            $sql_insert = rtrim($sql_insert, ',');
            $this->db->query($sql_insert);
        }
        echo "Successfully updated<br><pre>";
        print_r($return_final_arr);
        die;
    }
    
    public function testOrderNetPayable() {
        $order_id = 18008;
        $suborder_id = '20170818492-JP';
        $buyer_invoice = new BuyerInvoice($this->registry);
        $total_data = $buyer_invoice->getTotals($order_id, $suborder_id);
        echo "<pre>";
        print_r($total_data);
        die;
    }


    private function __readCSV($csvFile){
        $file_handle = fopen($csvFile, 'r');
        if (!$file_handle) return array();
        while (!feof($file_handle) ) {
            $line_of_text[] = fgetcsv($file_handle, 1024);
        }
        fclose($file_handle);
        return $line_of_text;
    }

    /*
    * method to give cashback to customers
    * csv format - first row contain customer ids, second row contain cashback amount
    */
    public function giveCashback() {
      $this->load->model('account/customer');

      // Set path to CSV file
      $csvFile = '/var/www/html/cashback_data.csv';
      $csv = $this->__readCSV($csvFile);

      $expiry_date = Date('d M Y', strtotime("+2 days"));

      $today = Date('d_M_y_h_i_s');
      $fp = fopen(DIR_SYSTEM.'logs/cahback_given_on_'.$today.'.csv', 'w');
      $data = array('Customer ID','Name','Telephone','Cashback');
      fputcsv($fp, $data);

      foreach ($csv as $customer) {
        if (empty($customer)) continue;
        $customer_info = $this->model_account_customer->getCustomer($customer[0]);
        if (empty($customer_info)) continue;

        $sql = "INSERT INTO " . DB_PREFIX . "customer_cashback
                SET customer_id = '" . (int) $customer[0] . "',
                    order_id = '0',
                    description = 'Promotional Cashback',
                    amount = '" . (float) $customer[1] . "',
                    date_added = NOW(),
                    validity = '" . (int) (2) . "',
                    amount_utilized = '0',
                    expired = '0'";

        if ($this->db->query($sql)) {
          $message    = "We miss you at www.wholesalebox.in. Rs " . $customer[1] . " credited to your account and it's applicable on ALL PRODUCTS. Expires by " . $expiry_date . "! Shop NOW! https://goo.gl/qHsJLm";
          $send_sms   = new SMS($message, $customer_info['telephone']);
          $send_sms->sendMessage(0, false);
          $data = array($customer[0],$customer_info['firstname'],$customer_info['telephone'],$customer[1]);
          fputcsv($fp, $data);
        }
      }

      fclose($fp);
      echo "Done"; exit;
    }

     public function implementGSTStateCodeInSellerInvoiceMeta() {
        $final_arr_update = array();
        $flag = true;
        $sql = "
                SELECT 
                    purchase_id, 
                    seller_firm_meta, 
                    purchase_firm_meta 
                FROM 
                    oc_wsb_purchase
                ";

        $editable_arr = $this->db->query($sql);
        $editable_arr = $editable_arr->rows;
        foreach ($editable_arr as $key => $value) {
            
            $purchase_id = $value['purchase_id'];

            $final_arr_update[$purchase_id]['seller_firm_meta'] = unserialize($value['seller_firm_meta']);
            $final_arr_update[$purchase_id]['purchase_firm_meta'] = unserialize($value['purchase_firm_meta']);

            if (empty($final_arr_update[$purchase_id]['seller_firm_meta']['state_code'])) {

                $seller_state = ucfirst(strtolower($final_arr_update[$purchase_id]['seller_firm_meta']['state']));

                $zone_sql = "SELECT zone_id, gst_state_code FROM `oc_zone` where name='" . $this->db->escape($seller_state) . "'";
                $seller_zone_str = $this->db->query($zone_sql)->row;
                if (!empty($seller_zone_str)) {
                    $final_arr_update[$purchase_id]['seller_firm_meta']['state_code'][0]['zone_id'] = $seller_zone_str['zone_id'];
                    $final_arr_update[$purchase_id]['seller_firm_meta']['state_code'][0]['gst_state_code'] = $seller_zone_str['gst_state_code'];
                }
            }

            if (empty($final_arr_update[$purchase_id]['purchase_firm_meta']['state_code'])) {

                $buyer_state = ucfirst(strtolower($final_arr_update[$purchase_id]['purchase_firm_meta']['state']));
                $zone_sql = "SELECT zone_id, gst_state_code FROM `oc_zone` where name='" . $this->db->escape($buyer_state) . "'";
                $buyer_zone_str = $this->db->query($zone_sql)->row;

                if (!empty($buyer_zone_str)) {
                    $final_arr_update[$purchase_id]['purchase_firm_meta']['state_code'][0]['zone_id'] = $buyer_zone_str['zone_id'];
                    $final_arr_update[$purchase_id]['purchase_firm_meta']['state_code'][0]['gst_state_code'] = $buyer_zone_str['gst_state_code'];
                }
            }
        }
        foreach ($final_arr_update as $purchase_id => $value) {
            $sql_update = "
                            UPDATE 
                                oc_wsb_purchase 
                            SET 
                                seller_firm_meta='". serialize($value['seller_firm_meta'])."',
                                purchase_firm_meta = '".serialize($value['purchase_firm_meta'])."' 
                            WHERE
                                purchase_id=".(int) $purchase_id;
            if(!($this->db->query($sql_update)))
                $flag = false;
        }
        if($flag) {
            echo "Succesfully updated.";
        }
    }

    public function getShortMsg() {
		$this->load->model('test/test');
		$telephone = $this->request->get['mobile'];
		
		if (empty($telephone)) {
				echo "Mobile number is missing";
				die;
		}
		
		$telephone = preg_replace('/\s+/', '', $telephone);
		
		
		
        $sql = "SELECT c.customer_id,  c.telephone, CONCAT( c.firstname, ' ', c.lastname ) as name , a.company, c.customer_type_id
                FROM `oc_customer` c
                LEFT JOIN oc_address a ON a.address_id = c.address_id
                WHERE c.telephone IN (".$telephone.")
                ORDER BY c.customer_id ASC ";

        $results_cid = $this->db->query($sql)->rows;
        $mobile_array = array();
        $cid = array();
        
        foreach ($results_cid as $result_cid) {
			$cid[] = $result_cid['customer_id'];
			$mobile_array[$result_cid['customer_id']]['name'] = $result_cid['name'];
			$mobile_array[$result_cid['customer_id']]['telephone'] = $result_cid['telephone'];
			$mobile_array[$result_cid['customer_id']]['company'] = $result_cid['company'];
		}
        
        $implode_cid = implode(",", $cid);
        
        $this->model_test_test->getShortMsg($implode_cid, $mobile_array, $telephone, $this);
        
       

    }

    public function updateNeogrowthRegistrationNumber() {
      $this->load->model('account/customer');

      // Set path to CSV file
      $csvFile = '/var/www/html/neogrowth_customers.csv';
      $csv = $this->__readCSV($csvFile);

      $today = Date('d_M_y_h_i_s');
      $fp = fopen(DIR_SYSTEM.'logs/fault_neogrowth_mobiles_'.$today.'.csv', 'w');
      $data = array('Mobile','Buyer registration number');
      fputcsv($fp, $data);

      foreach ($csv as $customer) {
        if (empty($customer)) continue;
        $query = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "customer WHERE telephone = '" . $this->db->escape(trim($customer[0])) . "'");
        if ($query->num_rows != 1 ) { 
          $data = array($customer[0],$customer[1]);
          fputcsv($fp, $data);
          continue;
        }
        
        $customer_id = $query->row['customer_id'];

        $query = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "customer_credit WHERE customer_id = '" . (int)$customer_id . "'");
        if ($query->num_rows < 1) {
          $sql = "INSERT INTO " . DB_PREFIX . "customer_credit
                  SET customer_id = '" . (int) $customer_id . "',
                      credit_status = '1',
                      credit_balance = '0',
                      last_updated = NOW(),
                      neogrowth_registration_number = '" . $this->db->escape(trim($customer[0])) . "', 
                      neogrowth_account_number = '" . $this->db->escape(trim($customer[1])) . "'";
        } else {
          $sql = "UPDATE " . DB_PREFIX . "customer_credit
                  SET credit_status = '1',
                      last_updated = NOW(),
                      neogrowth_registration_number = '" . $this->db->escape(trim($customer[0])) . "',
                      neogrowth_account_number = '" . $this->db->escape(trim($customer[1])) . "'
                  WHERE customer_id = '" . (int) $customer_id . "'";
        }

        $this->db->query($sql);
      }

      fclose($fp);
      echo "Done"; exit;
    }

    /**
     * Public method to remove BOM chars from GST Number of seller_invioce_meta
    */
    public function removeBomFromSellerInvoiceMeta(){
        $sql = "
                SELECT
                    seller_invoice_id,
                    seller_invioce_meta,
                FROM
                ".DB_PREFIX."seller_invoice
                WHERE
                    seller_invioce_meta IS NOT NULL
                    AND seller_invioce_meta != ''
               ";
        $qry = $this->db->query($sql);

        $results = $qry->rows;
        foreach ($results as $key => $value) {
            $seller_invioce_meta = unserialize( $value['seller_invioce_meta']);
            $seller_gst = $seller_invioce_meta['seller_data']['tin'] ?? '';
            $seller_gst = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $seller_gst);
            $seller_gst = trim($seller_gst);

            $buyer_gst  = $seller_invioce_meta['buyer_data']['tin'] ?? '';
            $buyer_gst  = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $seller_gst);
            $buyer_gst  = trim($seller_gst);

            $seller_invioce_meta['seller_data']['tin'] = $seller_gst;
            $seller_invioce_meta['buyer_data']['tin']  = $buyer_gst;

            //update seler_invoice meta 
            $update_sql = "
                            UPDATE 
                                ".DB_PREFIX."seller_invoice
                            SET
                               seller_invioce_meta = '".$this->db->escape(trim(serialize($seller_invioce_meta)))."' 
                            WHERE
                                seller_invoice_id = ". (int)$value['seller_invoice_id'] ."
                          ";
            $this->db->query($update_sql);


        }


    }

    /**
     * function fixes gst number checksum only ; if gst number has leading single quote
     */
    public function fixGSTNumbersWithQuote()
    {
        $sql_gst = "
                    SELECT
                        customer_id,
                        gst_number
                    FROM " . DB_PREFIX . "customer 
                    WHERE gst_number LIKE '\'%' ";

        $gst_result = $this->db->query( $sql_gst );

        if ( $gst_result->num_rows ) {
            
            foreach ( $gst_result->rows as $key => $data ) {
                
                $sanitized_gst = str_replace( "'", "", $data['gst_number'] );
                $checksum = GST::calculateGSTNumberChecksum( $sanitized_gst );
                $correct_gst = $sanitized_gst . $checksum;

                $validation_result = GST::validateGSTNumberFormat( $correct_gst );

                if ( $validation_result['result'] === true ) {

                    $update_sql = "
                                    UPDATE
                                        " . DB_PREFIX . "customer 
                                    SET gst_number = '" . $correct_gst . "' 
                                    WHERE customer_id = '" . $data['customer_id'] . "' ";

                    $update_result = $this->db->query( $update_sql );

                    print_r( "Updated GST from: ". $data['gst_number'] ." to: " . $correct_gst . " for customer_id: " . $data['customer_id'] . "<br>" );
                }
            }
        }
        echo "Process completed !!!";
        exit();
    }

}
