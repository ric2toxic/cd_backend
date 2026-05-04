<?php
/**
* 
*/
class ControllerRestapiTelecalling extends Controller
{

    public function addTrackingDetails()
    {
        $tracking_json =  isset($this->request->post['tracking_json'])?$this->request->post['tracking_json']:'';
        $imei_number =  isset($this->request->post['imei_number'])?$this->request->post['imei_number']:'';

        $device_manufacturer =  isset($this->request->post['device_manufacturer'])?$this->request->post['device_manufacturer']:'';
        $current_version_code =  isset($this->request->post['current_version_code'])?$this->request->post['current_version_code']:'1';
        $device_model_number =  isset($this->request->post['device_model_number'])?$this->request->post['device_model_number']:'';
        $play_store_email =  isset($this->request->post['play_store_email'])?$this->request->post['play_store_email']:'';

        $date =  isset($this->request->post['date'])?$this->request->post['date']:'';

        if (!$imei_number || !$date) {
            echo json_encode(array('status' => 'failure'));exit();
        }

        $this->load->model('restapi/teleservice');
        $this->load->model('staff/staff');

        $staff_info = $this->model_staff_staff->getStaffInfoByImei($imei_number);

        $user_settings = '';

        if (!empty($staff_info['crm_user_id'])) {
            $crm_user_id = (int)$staff_info['crm_user_id'];

            //Get User settings
            $this->load->model('crm/user');

            if ($crm_user_id > 0) {
                $user_settings = $this->model_crm_user->getUserSettings($crm_user_id);

                $teleCallerInfo = array(
                    'teletracking_version_code' => $current_version_code,
                    'device_manufacturer_info' => $device_manufacturer . ' ' . $device_model_number,
                    'play_store_email' => $play_store_email
                );
                $this->model_restapi_teleservice->updateTeleCallerInfo($crm_user_id, $teleCallerInfo);
            }
        }

        if ($this->model_restapi_teleservice->saveTrackingDetails(($tracking_json) , $imei_number, $date)){
            $apk_latest_version = (TELECALLING_LATEST_APK_VERSION) ? TELECALLING_LATEST_APK_VERSION : 20;

            // for newer versions
            if($staff_info['role'] === 'tele' || $staff_info['role'] === 'team_lead' || $staff_info['role'] === 'head' || $staff_info['role'] === 'sale_support') {
                if ((int)$current_version_code > 17) {
                    $response = array( "status"=>"success",
                        "update_data" => array(
                            "containBlockedPackageName"=>array(
                                "com.android.setting",
                                "com.android.vending",
                            ),
                            "containWhiteListedPackageName"=>array(
                                "android/com.android.server.am.AppNotRespondingDialog",
                                "com.android.keyguard/android.widget.FrameLayout","com.android.phone/.OutgoingCallBroadcaster",
                                "com.android.settings/.SubSettings","com.android.settings/amigo.app.AmigoAlertDialog",
                                "gallery3d", "wholesalebox","whatsapp","dialer","contacts","salesbooster","launcher",
                                "messaging","crmlocal","systemui",
                                "com.google.android.packageinstaller/com.android.packageinstaller.InstallAppProgress",
                                "com.google.android.packageinstaller/com.android.packageinstaller.PackageInstallerActivity",
                                "googlequicksearchbox","SoftInputWindow","inputmethod","ChooserActivity","incallui",
                                "com.gionee.amisystem","com.gionee.video","com.gionee.gallery","com.sprd.fileexplorer",
                                "com.android.mms","com.google.android.apps.docs/com.google.android.apps.viewer.PdfViewerActivity",
                                "com.miui.gallery","com.android.packageinstaller",
                                "com.android.settings/.Settings$"."AccessibilitySettingsActivity",
                                "com.gionee.setting.adapter.wifi","com.android.settings/amigo.app.AmigoAlertDialog",
                                "com.truecaller","com.android.server.telecom", "com.android.contacts/.activities.PeopleActivity",
                                "com.android.contacts/.activities.UnknownContactActivity","com.android.contacts/android.app.AlertDialog",
                                "com.android.contacts/.activities.ContactEditorActivity","com.android.contacts/.activities.ContactDetailActivity",
                                "com.android.contacts/.quickcontact.QuickContactActivity","com.android.contacts/android.widget.FrameLayout",
                                "com.android.contacts/miui.app.AlertDialog","com.miui.notes","com.android.contacts/.activities.ContactPhonePickerActivity","WifiDialog", "com.emoji.keyboard.touchpal"
                            ),
                            "exactBlockedPackageName"=>array(),
                            "exactWhiteListedPackageName"=>array(
                                                                "com.android.contacts/.activities.GnDialtactsActivityV2"
                                                                //"com.android.vending/com.google.android.finsky.activities.MainActivity",
                                                                //"com.android.vending/com.google.android.finsky.billing.lightpurchase.LightPurchaseFlowActivity",
                                                                //"com.android.vending/com.google.android.finsky.activities.AppsPermissionsActivity"
                                                            ),
                            "latest_apk_url"=>"https://cdnimages.net/telecalling_apk/app-release.apk",
                            "apk_latest_version"=>$apk_latest_version,
                            "password_version_code"=>1,
                            "wifi_password"=>"",
                            "wifi_ssid"=>"",
                            "calling_multiplication_factor"=>"0.85",
                            "wan_address"=>"https://www.wholesalebox.biz/crmapi/drive/saveRecording/"
                        ),
                        "user_settings" => $user_settings

                    );

                    if($staff_info['role'] === 'team_lead' || $staff_info['role'] === 'head' || $staff_info['role'] === 'sale_support') {
                        $temp = array(
                            "com.android.settings/.Settings\$WifiSettingsActivity","com.oneplus.gallery","com.oneplus.calculator/.Calculator","com.google.android.calendar","com.google.android.keep","com.android.contacts","com.truecaller","android/com.android.internal.app.ResolverActivity","com.lbe.parallel.intl","com.skype.raider"

                        );

                        $response['update_data']['containWhiteListedPackageName']= array_merge($response['update_data']['containWhiteListedPackageName'], $temp);

                        $response['update_data']['is_copy_allowed'] = "1";
                        $response['update_data']['password_version_code'] = "2";
                    } else {
                        $response['update_data']['is_copy_allowed'] = "0";
                        $response['update_data']['password_version_code'] = "1";
                        if(strtolower($device_manufacturer) == 'xiaomi' || strtolower($device_manufacturer) == 'micromax'){}else{

                            $response['update_data']['exactBlockedPackageName'] = array_merge($response['update_data']['exactBlockedPackageName'], array("com.android.contacts/.activities.GnDialtactsActivityV2","com.android.dialer/.DialtactsActivity") );

                        }
                    }
                } else { // for older versions
                    $response = array( "status"=>"success",
                        "update_data" => array(
                            "containBlockedPackageName"=>array(
                                "com.android.setting",
                                "com.android.vending",
                            ),
                            "containWhiteListedPackageName"=>array(
                                "android/com.android.server.am.AppNotRespondingDialog",
                                "com.android.keyguard/android.widget.FrameLayout","com.android.phone/.OutgoingCallBroadcaster",
                                "com.android.settings/.SubSettings","com.android.settings/amigo.app.AmigoAlertDialog",
                                "gallery3d", "wholesalebox","whatsapp","dialer","contacts","salesbooster","launcher",
                                "messaging","crmlocal","systemui",
                                "com.google.android.packageinstaller/com.android.packageinstaller.InstallAppProgress",
                                "com.google.android.packageinstaller/com.android.packageinstaller.PackageInstallerActivity",
                                "googlequicksearchbox","SoftInputWindow","inputmethod","ChooserActivity","incallui",
                                "com.gionee.amisystem","com.gionee.video","com.gionee.gallery","com.sprd.fileexplorer",
                                "com.android.mms","com.google.android.apps.docs/com.google.android.apps.viewer.PdfViewerActivity",
                                "com.miui.gallery","com.android.packageinstaller",
                                "com.android.settings/.Settings$"."AccessibilitySettingsActivity",
                                "com.gionee.setting.adapter.wifi","com.android.settings/amigo.app.AmigoAlertDialog",
                                "com.truecaller","com.android.server.telecom","WifiDialog", "com.emoji.keyboard.touchpal"
                            ),

                            //"exactBlockedPackageName"=>array("com.android.contacts/.activities.GnDialtactsActivityV2","com.android.dialer/.DialtactsActivity"),
                            "exactBlockedPackageName"=>array(),
                            "exactWhiteListedPackageName"=>array( 
                                                                "com.android.contacts/.activities.GnDialtactsActivityV2"
                                                                // "com.android.vending/com.google.android.finsky.activities.MainActivity",
                                                                // "com.android.vending/com.google.android.finsky.billing.lightpurchase.LightPurchaseFlowActivity",
                                                                // "com.android.vending/com.google.android.finsky.activities.AppsPermissionsActivity"
                                                            ),
                            "latest_apk_url"=>"https://cdnimages.net/telecalling_apk/app-release.apk",
                            "apk_latest_version"=>$apk_latest_version,
                            "password_version_code"=>1,
                            "wifi_password"=>"",
                            "wifi_ssid"=>"",
                            "calling_multiplication_factor"=>"0.85",
                            "wan_address"=>"https://www.wholesalebox.biz/crmapi/drive/saveRecording/"
                        ),

                        "user_settings" => $user_settings
                    );
                }
            }else{

                $response = array( "status"=>"success",
                    "update_data" => array(
                        "containWhiteListedPackageName"=>array(
                            crmlocal
                        ),
                        "exactBlockedPackageName"=>array(),
                        "exactWhiteListedPackageName"=>array(),
                        "latest_apk_url"=>"https://cdnimages.net/telecalling_apk/app-release.apk",
                        "apk_latest_version"=>$apk_latest_version,
                        "password_version_code"=>1,
                        "wifi_password"=>"",
                        "wifi_ssid"=>"",
                        "calling_multiplication_factor"=>"0.85",
                        "wan_address"=>"https://www.wholesalebox.biz/crmapi/drive/saveRecording/"
                    ),

                    "user_settings" => $user_settings

                );
            }


            echo json_encode($response);die;
        } else {
            echo json_encode(array('status' => 'failure'));die;
        }

    }

	public function getTelecallersList()
	{
	    $this->load->model('restapi/teleservice');

	    $tele_callers = $this->model_restapi_teleservice->getTelecallerStaff();

	    if ($tele_callers) {
	        echo json_encode(array('tele_list' => ($tele_callers), 'status' => 'success'));
	    } else {
	        echo json_encode(array('tele_list' => '', 'status' => 'failure'));
	    }
	}
	public function getTelecallerTrackingDetails()
	{
	    $imei_number =  isset($this->request->post['imei_number'])?$this->request->post['imei_number']:'';
	    $date =  isset($this->request->post['date'])?$this->request->post['date']:'';

	    $this->load->model('restapi/teleservice');


	    $tele_callers_log = $this->model_restapi_teleservice->getTelecallersLog($imei_number, $date);
	    if ($tele_callers_log) {
	        echo  json_encode (array('tracking_json' => $tele_callers_log['tracking_json'], 'status' => 'success', 'last_updated' => $tele_callers_log['last_updated'] )); exit();
	    } else {
	        echo json_encode(array('status' => 'failure', 'tracking_json' => '')); exit();
	    } 
	}
	public function generateTelecallingReport()
	{
	    $start_date =  isset($this->request->post['start_date'])?$this->request->post['start_date']:Date('Y-m-d');
	    $end_date =  isset($this->request->post['end_date'])?$this->request->post['end_date']:'';

        if (empty($end_date)) {
            $date1 = str_replace('-', '/', $start_date);
            $end_date = date('Y-m-d',strtotime($date1 . "+1 days"));
        }
	    $this->load->model('restapi/teleservice');
	    if (!$start_date || !$end_date) {
	        echo json_encode(array(['status' =>'failure', 'list' => ''])); exit();
	    } else {
	        $list = $this->model_restapi_teleservice->getTelecallerRecord($start_date, $end_date);
	        echo json_encode(array('status' =>'success', 'list' => $list)); exit();
	    }
	}

	/**
	 * upload contacts csv
	 *
	 **/
	public function emailCsv(){
		$this->load->model('restapi/service');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(isset($_FILES['csv_file']['tmp_name']) && !empty($_FILES['csv_file']['tmp_name'])){

				$mail = new PHPMailer();
				$mail->isSMTP();
				$mail->Host = $this->config->get('config_mail_smtp_hostname');
				$mail->Port = $this->config->get('config_mail_smtp_port');
				$mail->SMTPSecure = 'ssl';

				$mail->SMTPDebug = 0;
				$mail->Debugoutput = 'html';
				$mail->SMTPAuth = true;
				$mail->Username = $this->config->get('config_mail_smtp_username');
				$mail->Password = $this->config->get('config_mail_smtp_password');
				$mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesalebox');
				$mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');
				$mail->addAddress('rakesh.shekhawat@gmail.com', 'Rakesh Shekhawat');
				if (isset($_REQUEST['email'])) {
					$email = explode(',', $_REQUEST['email']);
					foreach ($email as $id) {
						$mail->addCC($id, 'Emails');
					}
				}

				$mail->Subject = $_REQUEST['subject'];

				$mail->AddAttachment($_FILES['csv_file']['tmp_name'], $_FILES['csv_file']['name']);

				if($mail->send()){
					$data['data'] = 'done';
					$data['status'] = '1';
					$data['status_text'] = 'Success';
				}else{

					$data['status'] = '0';
					$data['status_text'] = 'Failed';
					$data['message'] = 'email not sent successfully.';
				}

			}else{
				$data['status'] = '0';
				$data['status_text'] = 'Failed';
				$data['message'] = 'Please select a file.';
			}

			echo json_encode($data); exit;
		}
	}

	/**
     * Unlock tracking
     */

	public function unlockTracking(){
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $inputJSON = file_get_contents('php://input');
            $request = json_decode($inputJSON, TRUE);
            if (!empty($request['imei'])) {
                $imei = $request['imei'];
                $time = $request['timestamp'];

                $this->load->model('staff/staff');

                $staff_info = $this->model_staff_staff->getStaffInfoByImei($imei);


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
                $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
                $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');
                $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
                $mail->addBCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
                $mail->addBCC(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
                $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
                $mail->Subject = 'Unlocked device settings';
                $mail->Body = $staff_info['name'] . " - " . $staff_info['telephone'] . " unlocked the settings of phone 
                          on " . date("d/m/Y H:i:s");

                //send to admin;
                $mail->send();
            }

        }
    }
}