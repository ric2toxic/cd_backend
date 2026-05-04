<?php
include_once DIR_SYSTEM . '../rabbitmq/task_directive_constants.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ControllerWsbPurchasePurchaseReturn extends Controller{
    private $error = array();

    public function index() {
        $data = array();
        //GET wsb_purchase_id
        $wsb_purchase_id = (!empty($this->request->get['purchase_id']))? $this->request->get['purchase_id']: 0;
        $data['purchase_id'] = $wsb_purchase_id;
        $data['token'] = (!empty($this->request->get['token']))? $this->request->get['token']: null;

        //Load Model
        $this->load->model('wsb_purchase/purchase_return');
        // Autoloading the lanugage file
        $this->load->autoLoadLanguage('wsb_purchase/report',$data);

        $this->document->setTitle($this->language->get('return_heading_title'));
        //Set breadcrumbs
        $data['breadcrumbs'] = array();
        $url = '';
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $data['return_heading_title'],
            'href' => $this->url->link('wsb_purchase/import', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );
        //Set Common tpl to $data
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $product_breakup = $this->model_wsb_purchase_purchase_return->getWSBPurchaseProductDetails($wsb_purchase_id);
        $pids = array_column($product_breakup, 'product_id');
        //Foreach Loop to set .tpl data
        foreach ($product_breakup as $product) {
            $data['wsb_products'][$product['breakup_id']] = $product;

            //Set Purchase Firm name
            $purchase_firm_details = unserialize($product['purchase_firm_meta']);
            $data['wsb_products'][$product['breakup_id']]['purchase_firm'] = $purchase_firm_details['company'].'( '.$purchase_firm_details['city'].' )';
            
            $returnable_qty = $product['total_qty']-$product['dn_qty'];
            $data['wsb_products'][$product['breakup_id']]['remaining_qty'] = $returnable_qty;
            
            if(
              $returnable_qty > 0 
                && 
              $product['seller_purchase_firm'] == $product['purchase_firm_id']
            ){
                $data['wsb_products'][$product['breakup_id']]['is_returnable']  = 1;
                $data['wsb_products'][$product['breakup_id']]['returnable_qty'] = $returnable_qty;
            }else{
                $data['wsb_products'][$product['breakup_id']]['is_returnable']  = 0;
                $data['wsb_products'][$product['breakup_id']]['returnable_qty'] = 0;
            }
        }
        //Get all DebitNotes for specific PurchaseId
        $wsb_dn_obj = new WsbPurchaseDebitNote($this);
        $data['all_debit_notes'] = $wsb_dn_obj->getAllDnByPurchaseId($wsb_purchase_id);
        if(!empty($data['all_debit_notes'])) {
            foreach($data['all_debit_notes'] as $key => $value) {
             //Secure download debit note link   
                $file_name = array();
                $file_name['dn_id'] = (int)$value['debit_note_id'];
                $file_name = serialize($file_name);
                $file_name = base64_encode($file_name);
                $data['all_debit_notes'][$key]['download_dn'] = $this->securefiledownload->getDownloadLink('wsb_purchase_return', $file_name, false); 
            }
        }
        //Set Product Images
        $is_op_id = 0;
        MsProduct::setProductImages($this, $pids, $data, $is_op_id);

        $this->response->setOutput($this->load->view('wsb_purchase/purchase_return.tpl', $data));
    }

    /**
     * @info: Public method to generate DebitNote Preview
     * @param: 
     * @return: void
     * @author: Nishu, Jan 2018
    */
    Public function getDebitNotePreview(){
        $data    = array();
        //Set $_POST data
        parse_str($_POST['data'], $form_data);
        
        if(empty($form_data['dn_check'])){
            echo "No Products for Debit Note.";
            exit();// Exits if any checkbox is not checked
        }
        foreach ($form_data['dn_qty'] as $key => $qty) {
            if($qty == 0 || !isset($form_data['dn_check'][$key])){
                unset($form_data['dn_qty'][$key]);//Unset Array key when product qty is 0 for DebitNotes
            }
        }
        $form_data['breakup_ids']  = array_keys($form_data['dn_qty']);
        if(empty($form_data['breakup_ids'])){
            echo "No Products for Debit Note.";
            exit();
        }

        $inventory_dn = new WsbPurchaseDebitNote($this);
        //Get Debit Note Preview HTML
        $dn_html = $inventory_dn->getDebitNotePreviewHTML($form_data);
        print_r($dn_html);die;
    }

    /**
     * @info: Public method to generate DebitNote Preview
     * @param: void
     * @return: void
     * @author: Nishu, Jan 2018
    */
    Public function getGenerateDebitNote(){
        $data    = array();
        //Set Form data from $_POST data
        parse_str($_POST['data'], $form_data);
        
        if(empty($form_data['dn_check'])){
            echo "No Products for Debit Note.";
            exit();// Exits if any checkbox is not checked
        }

        foreach ($form_data['dn_qty'] as $key => $qty) {
            if($qty <= 0 || !isset($form_data['dn_check'][$key])){
                unset($form_data['dn_qty'][$key]);//Unset Array key when product qty is 0 for DebitNotes
            }
        }
        $form_data['breakup_ids']  = array_keys($form_data['dn_qty']);
        if(empty($form_data['breakup_ids'])){
            echo "No Products for Debit Note.";
            exit();// Exits if No Products to generate DebitNote
        }
        

        //Create Object for WsbPurchaseDebitNote library
        $inventory_dn = new WsbPurchaseDebitNote($this);
        //Get dn related all details
        $dn_data = $inventory_dn->getWsbPurchaseDnDetails($form_data);
        
        //Get HTML for pdf and dn amount
        $dn_html = $inventory_dn->getDebitNotePreviewHTML($form_data);
        
        $file_name = $inventory_dn->saveDnPdf($dn_html); 

        //Generate DebitNote
        $dn_data['dn_id'] = $inventory_dn->generateDebitNote($dn_data);

        //To save DebitNote Breakup into DB
        $inventory_dn->saveDebitNoteBreakUp($dn_data);  
        
        //Send Mail to inform generated DebitNote
        $this->sendMail($file_name);
    }

    /**
     * @info: Public method to send mail for WSB Purchase DebitNote
     * @param: $file_name
     * @return: void
     * @author: Nishu, Jan 2018
    */
    Public function sendMail($file_name = "", $cancelled_flag = false){
        if(empty($file_name)){
            return;
        }

        //Set Connection With RabbitMQ
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $queue_name = 'STAGING_GENERAL_TASKS_QUEUE';
            
        if (SITE_ENVIRONMENT == 'Production') {
           $queue_name = 'GENERAL_TASKS_QUEUE';
        }

        // third parameter is for queue durability. we set it to true
        // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
        // passive - false ; exclusive - false; auto-delete - false
        $channel->queue_declare($queue_name, false, true, false, false);

        if($cancelled_flag) {
            $data = array(
                        'constant_value' => unserialize(WSBPURCHASECANCELEDDEBITNOTE),
                        'data_array' => $file_name
                    );
        }else{
            $data = array(
                        'constant_value' => unserialize(WSBPURCHASEDEBITNOTE),
                        'data_array' => $file_name
                    );
        }
        
        $queue_object = base64_encode(serialize($data));

        // delivery_mode = 2 makes message persistent (durable)
        $msg = new AMQPMessage($queue_object, array('delivery_mode' => 2));
        $channel->basic_publish($msg, '', $queue_name);

        $channel->close();
        $connection->close(); //Closes Connection
    }

    /**
     * @info: Public method to get download WSB Purchase Debit Note
     * @return: void
     * @author: Nishu, Jan 2018
    */
    public function downloadDebitNote(){
        $data    = array();
        //Set DebitNoteId
        $dn_id = $_POST['dn_id'];
        $inventory_dn = new WsbPurchaseDebitNote($this);

        // Various buttons at the top of the page
        $file_name = array();
        $file_name['dn_id'] = (int)$dn_id;
        $file_name = serialize($file_name);
        $file_name = base64_encode($file_name);

        $data['dn_download'] = $this->securefiledownload->getDownloadLink('wsb_purchase_return', $file_name, false); 
        echo $data['dn_download'];
    }
   
    /**
    * @info: Public method to Cancel WSB Purchase Debit Note
    * @return: void
    * @author: MSA July 2018
    */
    public function cancelDebitNote()
    {
        $dn_id = $this->request->get['dn_id'];

        if(!empty($dn_id)) {

            $data = base64_encode(serialize (array('dn_id' => $dn_id) ) );               
            
            //Create Object for WsbPurchaseDebitNote library
            $inventory_dn = new WsbPurchaseDebitNote($this, $data);
            
            //Update DN status for cancel in wsb purchase return table
            $inventory_dn->updatedDebitNoteStatusForCancel(); 

            //Generate DN file for cancel status
            $debit_note_file = base64_decode( $inventory_dn->getDnByPurchaseId() );

            //Send Mail to inform for cancelled DebitNote
            $this->sendMail($debit_note_file, true);

            // Create file name structure to generate secure file download link
            $file_name = array();
            $file_name['dn_id'] = (int)$dn_id;
            $file_name          = serialize($file_name);
            $file_name          = base64_encode($file_name);

            //Get DN download file link
            $response['dn_download'] = $this->securefiledownload->getDownloadLink('wsb_purchase_return', $file_name, false); 
            echo json_encode($response['dn_download'] );
        }
    }



}
?>