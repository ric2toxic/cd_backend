<?php

class ControllerNotificationNotification extends Controller
{
    /***
     * Get Customers and send notification them on mobile app
     ***/
	public function sendPushNotificationToCustomers(){
		$customers = array();
		$cids = array();
		$this->load->model('notification/notification');
		/*if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			if(isset($this->request->post['sellers'])){
				$customers = $this->request->post['sellers'];
			}
		}
		if(empty($customers)){
			$this->load->model('notification/notification');
			$customers = $this->model_notification_notification->getCustomers();
		}
		foreach($customers as $customer){
			$cids[] = $customer['ws_gcm_registration_id'];
		}*/
		$cids[] = "APA91bHkStO8nfbojT1gDilYSIdnGTvqOg6JCwkP8hsCtj236EJ5HJsZVvzvzFnIZhZp_fm2o8V0TbgiuQ0tzRVLgS49ZbGqP9-aCr2kj7Co7ZKOVvnL3YU";
		//$cids[] = "APA91bElyiLKvqRVsZcbnCMumgPuRnIxd6zOnz9xAMVS8qvI1PHHvzpGjTJmZBKv-r0QRZkxRsS-CHJQ6lAzm06cYHrMzRXzFMZrwgTlau3tMRFqyVecxKQ";
		//echo "<pre>"; print_r($cids); exit("last");
		//$registrationIds = array( $_GET['id'] );
		// prep the bundle
		$msg = array
		(
			'message' 	=> 'here is a message. message',
			'title'		=> 'This is a title. title',
			'subtitle'	=> 'This is a subtitle. subtitle',
			'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
			'vibrate'	=> 1,
			'sound'		=> 1,
			'largeIcon'	=> 'http://www.wholesalebox.in/image/cache/catalog/TBS_JP/saree/tfs08-800x1200.jpg',
			'smallIcon'	=> 'small_icon'
		);
		$rs = $this->model_notification_notification->sendPushNotification($cids,$msg);
		echo "<pre>"; print_r($rs); exit("last");
	}

	public function sendTestPN(){
        //$registrationIds[] = "cZN1Pf8UuQw:APA91bHpkP_KcxJgJPTCN4wb8R-ka6nYVzHQnGi53ZKiaVCiYFqLi2beddLRqbqGz45nCf-Qcw6TLBeHEJ41FT32-yCBYlfsRi8Q09gNiBQKB9YjkeVJHCGPllk4DRDY5RkRPjRTF019";
        $registrationIds[] = "cbHH19aQLAo:APA91bFo-iGgK1gKbVR1HpoPaONgtF-1C3RTd7sZtjaMc3s-uA4w6d9JwdmDEnyq-9_pGVLZ6dvkjRR1i9HiErCjzku4XEwVDciCduN0M8PR--knXdozqSvtqoiYqIfRaZ73xA0BNoov";
        // prep the bundle
        $msg = array
        (
            'message' 	=> 'here is a message. message',
            'title'		=> 'This is a title. title',
            'subtitle'	=> 'This is a subtitle. subtitle',
            'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
            'vibrate'	=> 1,
            'sound'		=> 1,
            'largeIcon'	=> '',
            'smallIcon'	=> 'small_icon',
            'pull_call_history' => false,
            'pull_contacts' => false
        );

        $fields = array
        (
            'registration_ids' 	=> $registrationIds,
            'data'			=> $msg
        );

        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
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
        curl_close( $ch );
        echo $result;

    }

}
