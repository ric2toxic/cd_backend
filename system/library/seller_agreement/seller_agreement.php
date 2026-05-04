<?php
class SellerAgreement {
	/**
	 *Update Seller Profile
	 */
	private $_registry;
	private $_db;
	private $_load;
	
	public function __construct($registry){
		$this->_registry = $registry;
	    if(method_exists($registry, 'get')){
	   		$this->_db = $registry->get('db');
	   		$this->_load = $registry->get('load');
	   	}else{
	   		$this->_db = $registry->db;
	   		$this->_load = $registry->load;
	   	}
	}

	
	/**
	* Method for add New Agreement
	* @param $data : array of seller data
	* @return NULL
	* @author vikas, 2017
	*/
    public function addAgreement($data = array()) {

        $this->_db->query("INSERT INTO ". DB_PREFIX ."seller_agreement
                            SET clause_type = '" . $this->_db->escape($data['clause_type']) . "',
                                clause_version = '" . (float)$data['clause_version'] . "',
                                clause_content = '" . $this->_db->escape($data['clause_content']) . "',
                                status = '" . (int)($data['clause_status']) . "',
                                user = '" . $this->_db->escape($data['user']) . "',
                                date_added = NOW()
                        ");

    }

	/**
	* Method for update  old agreement with status and add new (copy of old data) agreement
	* @param $seller_id : Integer of seller id
	* @param $data : array of seller data
	* @return NULL
	* @author vikas, 2017
	*/
	public function editAgreement($agreement_id, $data) {

        $this->_db->query("UPDATE  ". DB_PREFIX ."seller_agreement
                            SET status = 0 ,
                                date_added = NOW()
                           WHERE agreement_id = '". (int)$agreement_id ."'
                        ");
        $this->_db->query("INSERT INTO ". DB_PREFIX ."seller_agreement
                            SET clause_type = '" . $this->_db->escape($data['clause_type']) . "',
                                clause_version = '" . (float)$data['clause_version'] . "',
                                clause_content = '" . $this->_db->escape($data['clause_content']) . "',
                                status = '" . (int)($data['clause_status']) . "',
                                user = '" . $this->_db->escape($data['user']) . "',
                                date_added = NOW()
                        ");
    }


    /**
	* Method for get seller agreement list
	* @param $data : array of filter data
	* @return seller agreement information with in array format
	* @author vikas, 2017
	*/
    public function getSellerAgreements($data = array()){
        $sql = "SELECT  agreement_id,
                        clause_type,
                        clause_version,
                        clause_content,
                        date_added,
                        user,
                        status
                FROM ". DB_PREFIX ."seller_agreement
                WHERE 1=1 ";


        if (!empty($data['filter_clause_type'])) {
            $sql .= " AND clause_type LIKE '%" . $this->_db->escape($data['filter_clause_type']) . "%'";
        }

        if (!empty($data['filter_clause_version'])) {
            $sql .= " AND clause_version = '" . (float)$data['filter_clause_version'] . "'";
        }

        if (isset($data['filter_clause_status']) and $data['filter_clause_status'] != -1) {
            $sql .= " AND status = '" . (int)$data['filter_clause_status'] . "'";
        }

        if (!empty($data['filter_clause_date_added'])) {
            $sql .= " AND DATE(date_added) = DATE('" . $this->_db->escape($data['filter_clause_date_added']) . "')";
        }

        $sort_data = array(
            'date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY date_added";
        }

        if (isset($data['order']) && ($data['order'] == 'ASC')) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 30;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }


        $query = $this->_db->query($sql);

        if( $query->num_rows ){
            return $query->rows;
        } else {
            return false;
        }
    }

    /**
	* Method for get total seller
	* @param $data : array of filter data
	* @return total sellers
	* @author vikas, 2017
	*/
    public function getTotalSellerAgreements($data = array()){
        $sql = "SELECT  count(*) as total
                FROM ". DB_PREFIX ."seller_agreement
                WHERE 1=1 ";

        if (!empty($data['filter_clause_type'])) {
            $sql .= " AND clause_type LIKE '%" . $this->_db->escape($data['filter_clause_type']) . "%'";
        }

        if (!empty($data['filter_clause_version'])) {
            $sql .= " AND clause_version = '" . (float)$data['filter_clause_version'] . "'";
        }

        if (isset($data['filter_clause_status']) and $data['filter_clause_status'] != "-1") {
            $sql .= " AND status = '" . (int)$data['filter_clause_status'] . "'";
        }

        if (!empty($data['filter_clause_date_added'])) {
            $sql .= " AND DATE(date_added) = DATE('" . $this->_db->escape($data['filter_clause_date_added']) . "')";
        }

        // echo $sql; die;
        $query = $this->_db->query($sql);

        if( $query->num_rows ){
            return $query->row['total'];
        } else {
            return false;
        }
        
    }

    /**
	* Method for get seller information by seller id
	* @param $seller_id : Integer of seller id
	* @return seller information with in array format
	* @author vikas, 2017
	*/
    public function getSellerAgreement($agreement_id) {

        $query = $this->_db->query("SELECT  agreement_id,
                                            clause_type,
                                            clause_version,
                                            clause_content,
                                            date_added,
                                            user,
                                            status
                                    FROM " . DB_PREFIX . "seller_agreement
                                    WHERE agreement_id = " . (int)$agreement_id );
        if( $query->num_rows ){
            return $query->row;
        } else {
            return false;
        }
    }
} 