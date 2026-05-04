<?php  
include_once DIR_SYSTEM . '../rabbitmq/task_directive_constants.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ModelWebengageWebengage extends Model {
  /**
   * Function : updateCreditApplicationStatusOnWebengage
   * Method to add user profile attribute on webengage
   * Parameters : customer id and status
   * @author Rahul 26th April 2019
   * Output : true or false
   * */
  public function updateCreditApplicationStatusOnWebengage($customer_id=NULL,$draft=NULL,$status=NULL){
    
    $attribute='';
    if(isset($draft) && $draft!='NA'){
      $attribute="Credit Application Step";
      $value=(int)$draft;
    }
    if(!empty($status)){
      $attribute="Credit Document Status";
      $value=$status;
    }
    if(!empty($attribute)){
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.webengage.com/v1/accounts/".WEBENGAGE_LICENSE_CODE."/users",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n  \"userId\": \"".$customer_id."\",\n  \"attributes\": {\n    \"".$attribute."\":\"".$value."\"\n  }\n}",
            CURLOPT_HTTPHEADER => array(
              "Authorization: Bearer ".WEBENGAGE_API_KEY,
              "Content-Type: application/json",
              "cache-control: no-cache"
            ),
          ));
          $response = curl_exec($curl);
          $err = curl_error($curl);
          curl_close($curl);
          if ($err) {
            return "cURL Error #:" . $err;
          } else {
            return $response;
          }

    }
          return true;
  }

  public function updateCreditApplicationCreditOnWebengage($customer_id=NULL,$total_credit=NULL,$credit_available=NULL){
    
    $attribute='';
    if(isset($total_credit) && $total_credit!='NA'){
      $attribute="Total Credit";
      $value= (int)$total_credit;
    }

    if(!empty($credit_available)){
      $attribute2="Credit Available";
      $value2= (int)$credit_available;
    }

    if(!empty($attribute)){
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.webengage.com/v1/accounts/".WEBENGAGE_LICENSE_CODE."/users",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n  \"userId\": \"".$customer_id."\",\n  \"attributes\": {\n    \"".$attribute."\":".$value."\n, \n    \"".$attribute2."\":".$value2."\n}\n}",
            CURLOPT_HTTPHEADER => array(
              "Authorization: Bearer ".WEBENGAGE_API_KEY,
              "Content-Type: application/json",
              "cache-control: no-cache"
            ),
          ));
          $response = curl_exec($curl);
          $err = curl_error($curl);
          curl_close($curl);
          if ($err) {
            return "cURL Error #:" . $err;
          } else {
            return $response;
          }

    }
          return true;
          
  } 

  /**
   * @info: Public method to send notification to customer for NACH debit amount 
   * @author: nishu, May 2019
  */
  public function notifyCustomerForNachDebit(array $customers)
  {
      $tot = $fail = $success = 0;
      
      foreach ($customers as $value) {

        $data = array();
        $data['eventName']  = 'nachDebit';
        $data['eventData']  = array(
                                    'Nach Debit Date'  => (string)$value['nach_debit_date'],
                                    'Customer Id'      => (string)$value['customer_id'] ,
                                    'Nach Debit Amount'=> (string)$value['nach_debit_amount'],
                                    'Day Type'         => (string)$value['day_type']
                                   );


        $res = $this->triggerBusinessEventRelay($data);
        $tot += 1;
        $fail += ($res === false ? 1 : 0);
        $success += ($res === true ? 1 : 0);

      }//End of Foreach
      
      return 'Total Relay Events: ' . $tot . ' | Success: ' . $success . ' | Fail: ' . $fail;
  } 


  /**
   * @info: Public method to create Business Events
   * @author: mahaveer, June 2019
  */
  public function createBusinessEvent($data)
  {  
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.webengage.com/v2/accounts/".WEBENGAGE_LICENSE_CODE."/business/events/definition",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
              "Authorization: Bearer ".WEBENGAGE_API_KEY,
              "Accept: application/json, text/plain, */*",
              "Referer: https://dashboard.webengage.com/accounts/".WEBENGAGE_LICENSE_CODE."/data-management/business-events/",
              "Origin: https://dashboard.webengage.com",
              "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36",
              "Content-Type: application/json;charset=UTF-8",
              "cache-control: no-cache"
            ),
          ));
          $response = curl_exec($curl);
          $err = curl_error($curl);
          curl_close($curl);
          if ($err) {
            return "cURL Error #:" . $err;
          } else {
            return $response;
          }
   }

  /**
   * @info: Public method to trigger business event (Relay)
   $data = array();
   $data['eventName']  = 'nachDebit';
   $data['eventData']   = array('Nach Debit Date'=>'09-07-2019',
                                  'Customer Id'=>'22182',
                                  'Nach Debit Amount'=>'200',
                                  'Day Type' => 'today/tomorrow'
                                  );
   * @author: Mahaveer, june 2019*/
  public function triggerBusinessEventRelay($data)
  {  
      $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.webengage.com/v2/accounts/".WEBENGAGE_LICENSE_CODE."/business/save-event",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
          "Authorization: Bearer ".WEBENGAGE_API_KEY,
          "Content-Type: application/json",
          "cache-control: no-cache"
        ),
      ));
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);
      if ($err) {
        return false;
      }
      
      return true;      
   }


  /**
   * @info: Public method to trigger event (Relay)
   $data = array();
   * @author: Mahaveer, OCT 2019*/
  public function pushEventToQueue($event_data)
  {  
    // Sending to RabbitMQ Queue
            try {
                $connection = new AMQPStreamConnection( 'localhost', 5672, 'guest', 'guest' );
                $channel = $connection->channel();

                $queue_name = 'STAGING_WEBENGAGE_TASKS_QUEUE';
            
                if (SITE_ENVIRONMENT == 'Production') {
                   $queue_name = 'WEBENGAGE_TASKS_QUEUE';
                }

                $channel->queue_declare($queue_name, false, true, false, false);

                $data = array(
                            'constant_value' => unserialize(WEBENGAGE_EVENT),
                            'data_array' => $event_data
                        );

                $queue_object = base64_encode( serialize( $data ));
                
                // delivery_mode = 2 makes message persistent (durable)
                $msg = new AMQPMessage($queue_object, array('delivery_mode' => 2));
                $channel->basic_publish($msg, '', $queue_name);
                $channel->close();
                $connection->close();

                return true;

            } catch ( \Exception $e ) {
                // Now we ensure that the actual SMS is still sent
                return false;
            }
  }


}
