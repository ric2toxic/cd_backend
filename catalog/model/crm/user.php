<?php
class ModelCrmUser extends Model {

    public $db_crm;
    function __construct()
    {
        //CRM Database
        $this->db_crm = new Database\DB( DBCRM_SERVERS );

    }

    public function getUserSettings($user_id) {
        $sql = "SELECT * FROM user_settings 
        WHERE user_id = '" . $user_id . "'";

        $query = $this->db_crm->query($sql);
        $user_settings['user_id'] =  $user_id;
        if (!empty($query->rows)) {
            foreach ($query->rows as $row) {
                $user_settings['settings'] = array(

                    'key_name'   => $row['key_name'],
                    'key_value' => $row['key_value']

                    );
            }
        }

        return $user_settings;
    }

    public function getCustomerId($phone) {
        $customer_id = 0;
        $sql = "SELECT
                 leads_website_customer_ids.customer_id,
                 lead_contacts.mobile AS mobile
               FROM leads
               LEFT JOIN leads_website_customer_ids ON leads_website_customer_ids.lead_id = leads.id
               LEFT JOIN lead_contacts ON lead_contacts.lead_id = leads.id
               WHERE ( leads.mobile = '".$this->db_crm->escape($phone)."' 
               OR lead_contacts.mobile = '".$this->db_crm->escape($phone)."' ) 
               AND leads.status != 'MERGE_DEAD'";

        $query = $this->db_crm->query($sql);
        foreach($query->rows as $rows)
        {
            if(!empty($rows['customer_id']))
            {
              $customer_id = $rows['customer_id'];
              break; 
            }
        }
        return $customer_id;
    }

}
