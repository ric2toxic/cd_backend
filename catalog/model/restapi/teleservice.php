<?php
/**
* 
*/
class ModelRestapiTeleservice extends Model
{
	
	public function getTelecallerStaff()
	{
	  return $this->db->query("SELECT name, imei_number, teletracking_version_code, device_manufacturer_info, play_store_email  FROM oc_sales_staff WHERE role='tele' AND active_status = '1'")->rows;
	}
	public function saveTrackingDetails($tracking_json, $imei_number, $date)
	{
	$find_imei = $this->db->query("SELECT * FROM oc_telecallers_monitoring WHERE imei_number ='".$this->db->escape($imei_number) ."' AND date='".$this->db->escape($date)."' ");
	 	if (!empty($tracking_json)) {
		    if ($find_imei->num_rows) {
		      	$this->db->query("UPDATE oc_telecallers_monitoring SET tracking_json = '".$this->db->escape(html_entity_decode($tracking_json)) ."', last_updated =NOW() WHERE imei_number = '".$this->db->escape($imei_number)."' AND date='".$this->db->escape($date)."'" );
		      	return true;
		    } else {
	     	$this->db->query("INSERT INTO  oc_telecallers_monitoring SET tracking_json = '".$this->db->escape(html_entity_decode($tracking_json)) ."', imei_number = '".$this->db->escape($imei_number)."', date='".$this->db->escape($date)."', last_updated =NOW()"  );
	      	return true;
	    	}
		} else {
		    return false;
		}
	}
	public function getTelecallersLog($imei_number, $date)
	{
	 	$sql = "SELECT tracking_json, last_updated FROM oc_telecallers_monitoring WHERE imei_number = '".$this->db->escape($imei_number)."' AND  date='".$this->db->escape($date)."'";
	 	if ( $this->db->query($sql)->num_rows) {
	    	return $this->db->query($sql)->row;
	  	} else {
	    	return false;
	  	}
	}
	public function getTelecallerRecord($start_date, $end_date)
	{
	 	$sql = "SELECT tm.*, oss.name FROM oc_telecallers_monitoring tm INNER JOIN oc_sales_staff oss ON (oss.imei_number = tm.imei_number) WHERE last_updated BETWEEN '".$this->db->escape($start_date)."' AND '".$this->db->escape($end_date)."'";
	 	return $this->db->query($sql)->rows;
	}

    public function updateTeleCallerInfo($crm_user_id, $teleCallerInfo) {
	    $sql = "UPDATE oc_sales_staff 
                  SET teletracking_version_code = ". (int)$teleCallerInfo['teletracking_version_code'] . " ,
                  device_manufacturer_info = '". $this->db->escape($teleCallerInfo['device_manufacturer_info']) . "' ,
                  play_store_email = '". $this->db->escape($teleCallerInfo['play_store_email']) . "' 
                  WHERE crm_user_id = ".(int)$crm_user_id ;
	    return $this->db->query($sql);
    }
}