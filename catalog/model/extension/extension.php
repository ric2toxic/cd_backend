<?php
class ModelExtensionExtension extends Model {
	function getExtensions($type, $store_id = -1, $hasCod=false) {

        $sql = '';
		if($store_id >= 0 && $type == 'payment'){

            $sql = "SELECT e.*, e2s.store_id 
                    FROM " . DB_PREFIX . "extension e 
                    INNER JOIN ". DB_PREFIX ."wsb_extension_to_store e2s 
                                ON e.extension_id = e2s.extension_id
                    WHERE e.type = '" . $this->db->escape($type) . "' 
                      AND e2s.store_id = ". $store_id;

            if (!$this->cart->hasCOD() || !$hasCod){
                $sql .= " AND e.code != 'cod'";
            }

		} else {
			$sql = "SELECT e.* 
                    FROM " . DB_PREFIX . "extension e 
                    WHERE e.type = '" . $this->db->escape($type) . "'
                    ";
		}

        $query = $this->db->query($sql);
		return $query->rows;
	}

	public function checkCODServiceability($pincode=0){
        $sql = "SELECT cod FROM ".DB_PREFIX."bluedart_pincodes WHERE pincode = '".(float)$pincode."' AND status = 1 AND cod = 1 UNION 
                SELECT cod FROM ".DB_PREFIX."delhivery_pincodes WHERE pincode = '".(float)$pincode."' AND status = 1 AND cod = 1 UNION 
                SELECT cod FROM ".DB_PREFIX."dotzot_pincodes WHERE pincode = '".(float)$pincode."' AND status = 1 AND cod = 1 UNION 
                SELECT cod_serviceable as cod FROM ".DB_PREFIX."fedex_pincodes WHERE pincode = '".(float)$pincode."' AND status = 1 AND cod_serviceable = 1 AND oda_opa_or_reg = 'regular' UNION 
                SELECT cod FROM ".DB_PREFIX."gati_pincodes WHERE pincode = '".(float)$pincode."' AND status = 1 AND cod = 1";

        $query = $this->db->query($sql);

        if($query->num_rows){
            return true;
        } 

        return false;
    }

//Check wether a pincode comes under ess service charge area or not.
	public function checkESSServiceability($pincode=0){
		
        $hasESS = false;
        
        $query_gati = $this->db->query("SELECT 
                                            `serviceability` 
                                        FROM ".DB_PREFIX."gati_pincodes 
                                        WHERE 
                                            pincode = ".(float)$pincode." 
                                            AND 
                                            status = 1 
                                            LIMIT 1"
                                        );
        
        if($query_gati->num_rows > 0 && strtolower($query_gati->row['serviceability']) == 'ess'){
        
            $hasESS = true;
        
        } else {
        
            $query_fedex = $this->db->query("SELECT 
                                                `oda_opa_or_reg` 
                                            FROM ".DB_PREFIX."fedex_pincodes 
                                            WHERE 
                                                pincode = ".(float)$pincode." 
                                                AND 
                                                status = 1 
                                                LIMIT 1"
                                            );
        
            if( $query_fedex->num_rows > 0 && 
                strtolower($query_fedex->row['oda_opa_or_reg']) != 'regular'
            ){
        
                $hasESS = true;
        
            }
        
        }
        return $hasESS;
    }

}
