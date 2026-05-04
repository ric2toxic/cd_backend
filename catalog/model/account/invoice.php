<?php
class ModelAccountInvoice extends Model {
    public function getOrder($order_id, $customer_id= '') {

        $sql = "SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'";
    
        if (!empty($customer_id)) {
            $sql .= " AND o.customer_id ='".$customer_id."'";
        } else {
            $sql .= " AND o.customer_id ='".$this->customer->getId()."'";
        }

        $order_query = $this->db->query($sql);
        // $order_query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "' AND o.customer_id = '".$this->customer->getId()."'");

        if ($order_query->num_rows) {

            $order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

            if ($country_query->num_rows) {
                $payment_iso_code_2 = $country_query->row['iso_code_2'];
                $payment_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $payment_iso_code_2 = '';
                $payment_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $payment_zone_code = $zone_query->row['code'];
            } else {
                $payment_zone_code = '';
            }

            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

            if ($country_query->num_rows) {
                $shipping_iso_code_2 = $country_query->row['iso_code_2'];
                $shipping_iso_code_3 = $country_query->row['iso_code_3'];
            } else {
                $shipping_iso_code_2 = '';
                $shipping_iso_code_3 = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

            if ($zone_query->num_rows) {
                $shipping_zone_code = $zone_query->row['code'];
            } else {
                $shipping_zone_code = '';
            }
            // Select Courier Partner URL 
            if (!empty($order_query->row['courier_partner']) && !is_null($order_query->row['courier_partner'])) {
                $tracking_url = $this->db->query("SELECT tracking_url FROM ".DB_PREFIX."courier_partners WHERE courier_name ='".$order_query->row['courier_partner']."'")->row['tracking_url'];
            } else {
                $tracking_url = null;
            }
/*
            if ($order_query->row['affiliate_id']) {
                $affiliate_id = $order_query->row['affiliate_id'];
            } else {
                $affiliate_id = 0;
            }

            $this->load->model('marketing/affiliate');

            $affiliate_info = $this->model_marketing_affiliate->getAffiliate($affiliate_id);

            if ($affiliate_info) {
                $affiliate_firstname = $affiliate_info['firstname'];
                $affiliate_lastname = $affiliate_info['lastname'];
            } else {
                $affiliate_firstname = '';
                $affiliate_lastname = '';
            }

            $this->load->model('localisation/language');

            $language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

            if ($language_info) {
                $language_code = $language_info['code'];
                $language_directory = $language_info['directory'];
            } else {
                $language_code = '';
                $language_directory = '';
            }
*/
            return array(
                'order_id'                => $order_query->row['order_id'],
                'order_no'               => $order_query->row['order_no'],
                'invoice_no'              => $order_query->row['invoice_no'],
                'invoice_date'            => $order_query->row['invoice_date'],
                'invoice_prefix'          => $order_query->row['invoice_prefix'],
                'store_id'                => $order_query->row['store_id'],
                'store_name'              => $order_query->row['store_name'],
                'store_url'               => $order_query->row['store_url'],
                'customer_id'             => $order_query->row['customer_id'],
                'customer'                => $order_query->row['customer'],
                'firstname'               => $order_query->row['firstname'],
                'lastname'                => $order_query->row['lastname'],
                'email'                   => $order_query->row['email'],
                'telephone'               => $order_query->row['telephone'],
                'custom_field'            => unserialize($order_query->row['custom_field']),
                'payment_firstname'       => $order_query->row['payment_firstname'],
                'payment_lastname'        => $order_query->row['payment_lastname'],
                'payment_company'         => $order_query->row['payment_company'],
                'payment_address_1'       => $order_query->row['payment_address_1'],
                'payment_address_2'       => $order_query->row['payment_address_2'],
                'payment_postcode'        => $order_query->row['payment_postcode'],
                'payment_city'            => $order_query->row['payment_city'],
                'payment_zone_id'         => $order_query->row['payment_zone_id'],
                'payment_zone'            => $order_query->row['payment_zone'],
                'payment_zone_code'       => $payment_zone_code,
                'payment_country_id'      => $order_query->row['payment_country_id'],
                'payment_country'         => $order_query->row['payment_country'],
                'payment_iso_code_2'      => $payment_iso_code_2,
                'payment_iso_code_3'      => $payment_iso_code_3,
                'payment_address_format'  => $order_query->row['payment_address_format'],
                'payment_custom_field'    => unserialize($order_query->row['payment_custom_field']),
                'payment_method'          => $order_query->row['payment_method'],
                'payment_code'            => $order_query->row['payment_code'],
                'shipping_firstname'      => $order_query->row['shipping_firstname'],
                'shipping_lastname'       => $order_query->row['shipping_lastname'],
                'shipping_company'        => $order_query->row['shipping_company'],
                'shipping_address_1'      => $order_query->row['shipping_address_1'],
                'shipping_address_2'      => $order_query->row['shipping_address_2'],
                'shipping_postcode'       => $order_query->row['shipping_postcode'],
                'shipping_city'           => $order_query->row['shipping_city'],
                'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
                'shipping_zone'           => $order_query->row['shipping_zone'],
                'shipping_zone_code'      => $shipping_zone_code,
                'shipping_country_id'     => $order_query->row['shipping_country_id'],
                'shipping_country'        => $order_query->row['shipping_country'],
                'shipping_iso_code_2'     => $shipping_iso_code_2,
                'shipping_iso_code_3'     => $shipping_iso_code_3,
                'shipping_address_format' => $order_query->row['shipping_address_format'],
                'shipping_custom_field'   => unserialize($order_query->row['shipping_custom_field']),
                'shipping_method'         => $order_query->row['shipping_method'],
                'shipping_code'           => $order_query->row['shipping_code'],
                'comment'                 => $order_query->row['comment'],
                'total'                   => $order_query->row['total'],
                'order_status_id'         => $order_query->row['order_status_id'],
                'affiliate_id'            => $order_query->row['affiliate_id'],
                //'affiliate_firstname'     => $affiliate_firstname,
                //'affiliate_lastname'      => $affiliate_lastname,
                'commission'              => $order_query->row['commission'],
                'language_id'             => $order_query->row['language_id'],
                //'language_code'           => $language_code,
                //'language_directory'      => $language_directory,
                'currency_id'             => $order_query->row['currency_id'],
                'currency_code'           => $order_query->row['currency_code'],
                'currency_value'          => $order_query->row['currency_value'],
                'ip'                      => $order_query->row['ip'],
                'forwarded_ip'            => $order_query->row['forwarded_ip'],
                'user_agent'              => $order_query->row['user_agent'],
                'accept_language'         => $order_query->row['accept_language'],
                'date_added'              => $order_query->row['date_added'],
                'date_modified'           => $order_query->row['date_modified'],
                'cform_submit'           => $order_query->row['cform_submit'],
                'cst_with_cform'         => $order_query->row['cst_with_cform'],
                'courier_partner'         => $order_query->row['courier_partner'],
                'tracking_no'             => $order_query->row['tracking_no'],
                'tracking_url'             => $tracking_url,
                'bank_slip_image'             => $order_query->row['bank_slip_image'],
                'refundable_cform'     => $order_query->row['refundable_cform'],
                'refund_status'           => $order_query->row['refund_status'],
                'sales_staff'          => $this->getSalesPersonName($order_query->row['sales_staff_id'])
            );
        } else {
            return;
        }
    }

    public function getOrderProducts($order_id) {
        $query = $this->db->query("SELECT oop.*, op.image, pd.set_description, r.order_product_id as return_order_product_id, r.quantity as return_pieces, r.return_action_id, r.return_reason_id, r.comment as return_comment 
            FROM 
            " . DB_PREFIX . "order_product oop 
            INNER JOIN 
                " . DB_PREFIX . "product op ON op.product_id = oop.product_id 
            LEFT JOIN 
                ".DB_PREFIX."return r ON(oop.order_product_id = r.order_product_id)
            LEFT JOIN 
                ".DB_PREFIX."product_description pd ON (op.product_id = pd.product_id)
            WHERE 
                oop.order_id = '" . (int)$order_id . "' 
                AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
            ORDER BY 
                oop.order_product_id ASC");

        return $query->rows;
    }

    public function getOrderOptions($order_id, $order_product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

        return $query->rows;
    }

    public function getOrderVouchers($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");

        return $query->rows;
    }

    public function getOrderTotals($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

        return $query->rows;
    }

    public function getSalesPersonName($staff_id) {

        $staff_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sales_staff WHERE staff_id = '". (int)$staff_id . "'");

        if ($staff_query->rows) {
            return $staff_query->row['name'];
        } else {
            return false;
        }
    }

    public function isWayBillReqd($order_id) {

        // List of states (zones) where no way bill is required.
        // Andaman and Nicobar (1475)
        // Chandigarh (1480)
        // Chattisgarh (4232)
        // Dadra & Nagar Haveli (1481)
        // Daman & Diu (1482)
        // Goa (1484)
        // Haryana (1486)
        // Himachal Pradesh (1487)
        // Lakshadweep (1491)
        // Maharashtra (1493)
        // Pondicherry / Puducherry (1499)
        // Rajasthan (1501)
        // Tamil Nadu (1503)

        $noWayBillStates = array(1475, 1480, 4232, 1481, 1482, 1484, 1486, 1487, 1491, 1493, 1499, 1501, 1503);

        $order_query = $this->db->query("SELECT payment_zone_id FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$order_id . "'");

        if ($order_query->num_rows) {
            $zone_id = (int)$order_query->row['payment_zone_id'];

            if (in_array($zone_id, $noWayBillStates))
                return false;
            else
                return true;
        }

        return true;
    }


    // this function is belongs to sale/custom_field of khufiya_vibhag.
    public function getCustomFields($data = array()) {
        
        $sql = "SELECT * FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN " . DB_PREFIX . "custom_field_description cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND cfd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sort_data = array(
            'cfd.name',
            'cf.type',
            'cf.location',
            'cf.status',
            'cf.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY cfd.name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    // this function is belongs to setting/setting of khufiya_vibhag.
    public function getSetting($code, $store_id = 0) {
        $setting_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

        foreach ($query->rows as $result) {
            if (!$result['serialized']) {
                $setting_data[$result['key']] = $result['value'];
            } else {
                $setting_data[$result['key']] = unserialize($result['value']);
            }
        }

        return $setting_data;
    }
}