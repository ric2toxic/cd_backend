<?php
/**
 *  PaymentTransactions
 *  @author @Garvit Joshi
 **/

class PaymentTransactions {

    public  $registry;
    private $load;
    private $db;

    public function __construct($registry) {
        //print_r($registry); exit();
        $this->registry     = $registry;
        $this->db           = $registry->db;
        $this->load         = $registry->load;
    }
    
    private $solr_fields = array('TxId', 'merchantTxnId', 'OrderNO', 'TxStatus', 'TxMsg', 'amount', 'transactionId', 'paymentMode', 'currency', 'cardHolderName', 'firstName', 'lastName', 'email', 'addressStreet1', 'addressStreet2', 'addressCity', 'addressState', 'addressCountry', 'addressZip', 'mobileNo', 'isCOD', 'txnDateTime', 'payment_id', 'payment_type', 'order_id', 'suborder_id', 'date_added', 'payment_gateway', 'successfull', 'reference', 'payment_link', 'json_response', 'respCode', 'respMsg');

    /**
     *addTranscationsToSolr
     * @param $data: Array of arrays $data
     */
    public function addTranscationsToSolr($data) {
        
        $config_solr = $this->config->solrConfig(SOLR_PATH_Payment_Transactions);
        // create a client instance
        $client = new Solarium\Client($config_solr);
        // get an update query instance
        $update = $client->createUpdate();
        // create a new document for the data
        // please note that any type of validation is missing in this example to keep it simple!

        foreach ($data as $single_trans_data) {
            if(!empty($single_trans_data)){

                $doc = $update->createDocument();
                foreach ($single_trans_data as $key => $value) {
                    if(in_array($key, $this->solr_fields)){
                        if($key == 'TxId' || $key == 'merchantTxnId'){
                            $doc->id     = $value;
                        }else{
                            $doc->$key   = $value;
                        }
                    }
                }
                $update->addDocument($doc);
            }
        }
        $update->addCommit();
        // this executes the query and returns the result
        $result = $client->update($update);
    }
}