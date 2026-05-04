 <?php
/**
 * 	ReturnInfo
 *  @info: ReturnInfo class to fetch return related all information
 * 	@author @Nishu, Dec 2017
 */
require_once(DIR_SYSTEM.'/library/wsbregisterybase.php');
class ReturnInfo extends WSBRegisteryBase
{
	public function __construct($registry = null){
		parent::__construct($registry);
	}
	/**
     * Public Function to get all CNs against orderId
     * @param: $order_id
     * @return: $data Array
     * @author: Nishu, Jan 2018
	*/
	public function getAllCNByOrderId($order_id = ''){
		$data = array();
		if(empty($order_id)){
			return $data;
		}
		$sql = "SELECT cn.*, td.*, GROUP_CONCAT(DISTINCT trxn_done) AS trxn_status  
		         FROM " . DB_PREFIX . "credit_note as cn 
                 LEFT JOIN 
                 ".DB_PREFIX."trxn_details as td ON td.trxn_for_id = cn.credit_note_id 
                            AND td.trxn_for = 'CREDIT_NOTE' 
		        WHERE 
		           cn.order_id = ". (int)$order_id ."
                GROUP BY cn.credit_note_id
                ORDER BY cn.credit_note_id DESC
		       ";
		$result = $this->db->query($sql);
        
		if($result->num_rows > 0){
			foreach ($result->rows as $key => $value) {
                $data[$value['credit_note_id']] = $value;
            }
		}
		return $data;
	}



	/**
     * Public Function to get all DNs against orderId
     * @param: $order_id
     * @return: $data Array
     * @author: Nishu, Jan 2018
	*/
	public function getAllDNByOrderId($order_id = ''){
		$data = array();
		if(empty($order_id)){
			return $data;
		}
		$sql = "SELECT * 
		         FROM " . DB_PREFIX . "seller_debit_note 
		         WHERE 
		           order_id = ". (int)$order_id ."
		       ";
		$result = $this->db->query($sql);
		if($result->num_rows > 0){
            foreach ($result->rows as $key => $value) {
                $data[$value['debit_note_id']] = $value;
            }
		}
		return $data;
	}

    /**
     * Public Function to get all RNs against orderId
     * @param: $order_id
     * @return: $data Array
     * @author: Nishu, Jan 2018
    */
    public function getAllRNByReplacementIds(array $replacement_note_id){
       $data = array();
        if(empty($replacement_note_id)){
            return $data;
        }
        $sql = "SELECT 
                        rn.replacement_note_id ,
                        rn.replacement_note_prefix,
                        rn.replacement_note_no,
                        rn.replacement_note_amount,
                        rn.date_added,
                        rn.user,
                        rn.file_name,
                        r.quantity,
                        op.name,
                        op.model,
                        op.order_product_id,
                        op.seller_sku,
                        op.is_returnable,
                        ms.seller_id,
                        ms.nickname,
                        ms.company,
                        ms.company
                 FROM 
                    " . DB_PREFIX . "replacement_note rn
                 INNER JOIN 
                    " . DB_PREFIX . "return r ON r.replacement_note_id = rn.replacement_note_id
                 INNER JOIN 
                    " . DB_PREFIX . "order_product op ON op.order_product_id = r.order_product_id
                 INNER JOIN 
                    " . DB_PREFIX . "ms_seller ms ON ms.seller_id = op.seller_id   
                 WHERE 
                   rn.replacement_note_id IN (".implode(',', $replacement_note_id).")
               ";
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            foreach ($result->rows as $key => $value) {
                $data[$value['replacement_note_id']] = $value;
            }
        }
        return $data;
    }


	/**
     * Default Selector
     * Using this we can specify tablewise fields to select, and sorting rules to apply.
     * array - ( [table_name without DB_PREFIX] => array ( 'select' => array (<fields>)  OR <field>,
     *                                                     'sort' => array(<field1> => <ASC or DESC>,
     *                                                                     <field2> => <ASC or DESC>, .. and so on
     *                                                                    )
     *                                                   )
     *         )
     * @note: This is a default selector. It does not include all return tables,
     * but some of the commonly used ones. If getOrder is not specified selector, then this is used.
     * @note: If there is no 'select' key in the table specifc array, or 'select' key points to empty array,
     * then it selects all (*) the fields from table.
     * @note: 'select' key can have a single field OR array of fields.
     * @note: 'sort' key can be used to define multi-level sorts as well.
     * @author Nishu, Jan 2018
     */
    private static $_default_selector = array(
								    	'oc_return' => array('sort' => array('return_id DESC')),
								        'oc_master_return' => array()
								       );


	/**
     * General method to get Return details given $data array with keys order_id, order_product_id and master_return_id
     * Public static method. No need to create Class object to call this method.
     * @params:
     * $db - Database object (required)
     * $data - Array
     * $selector - Selector array for fields to select from specific return tables with sorting rules etc. 
     * Like: <oc_return, oc_master_return, oc_return_reason, oc_return_action>
     * Example: $selector = array(
     *                   'oc_return'=> array('select' => array()) ,
     *                   'oc_master_return' => array( 'select' => array()),
     *                   'oc_return_reason' => array( 'select' => array()),
     *                   'oc_return_action' => array( 'select' => array())
     *                  );
     *        Defaults to '', meaning $_default_selector of this table is used.
     *  
     * ////////////////////////////////////////////////////////////////////////////
     *  Note: 
     *          Columns are common in multiple tables but having diffrent values
     *          quantity in oc_return and oc_order_product table with different values
     *           (Use Alias Names for these columns)
     *     
     *      Currently We are using  aliasing for 
     *           From oc_return:
     *            quantity as return_quantity
     *            comment  as return_comment
     *
     *      For Future Tip : 
     *              If any field is added with same name in multiple tables and having different values
     *          Handle that case with aliasing
     *
     * ////////////////////////////////////////////////////////////////////////////
     * @return:
     *     array('oc_return' => array(<field> => <value>,
     *                                'return_action' => '<optional array()>',
     *                                'return_reason' => '<optional array()>'
     *                               ),
     *           'oc_master_return' => array(
     *                                <field> => <value>
     *                                )
     *        )
     * @author Nishu, Jan 2018
     */
    public static function getReturnInfo($db, $data, $selector = '') {
        // Initializing return variable
        $return_info = array();
        $return_info['oc_return'] = array();
        $return_info['oc_master_return'] = array();
        if(empty($data)){
            return $return_info;
        }else if(empty($data['order_id']) && empty($data['order_product_id']) && empty($data['master_return_id']) && empty($data['return_ids'])){
            return $return_info;
        }

        //Check and Update if more then one active_row = 1 for multiple rows of single return
            //(Means Group By order_product_id, master_return_id)
        ReturnActionBase::checkAndUpdateMultipleActiveRowForSingleReturn($db);


        // Using default selector, if no selector provided
        if (empty($selector)){
            $selector = self::$_default_selector;
        }

        //Start dynamic Select Query
        $sql = "";
        $field_list = "";
        $join_tbl = "";
        $return_reason_sql = "";
        $return_action_sql = "";
        $sort_sql = "";
        $whr = "";
        $whr_suborder = "";

        // Looping over the selector tables
        foreach ($selector as $table => $rules) {
            //To get Sub-order details
            if($table == "oc_suborder"){
                $join_tbl = " INNER JOIN ".DB_PREFIX."suborder 
                            ON ".DB_PREFIX."suborder.order_id = ".DB_PREFIX."order_product.order_id ";

                $whr_suborder = DB_PREFIX."suborder.suborder_id = ".DB_PREFIX."order_product.suborder_id ";

            }

            if($table == "oc_return_reason"){
                $return_reason_sql = " SELECT * FROM " .$table;
                $field_list .= ", oc_return.return_reason_id";
            }else if($table == "oc_return_action"){
                $field_list .= ", oc_return.return_action_id";
                $return_action_sql = " SELECT * FROM " .$table;
            }else if($table == "oc_replacement_note"){
                $field_list .= ", oc_return.replacement_note_id";
                //$return_action_sql = " SELECT * FROM " .$table;
            }else{
                // Checking if 'select' rules are defined
                if (!empty($rules['select'])) {
                    if (is_array($rules['select'])) {

                        if (!empty($rules['select'])) { // Checking if selector is not blank array 
                            foreach ($rules['select'] as $f) {
                              $field_list .= ", ".$table. "." . $f;
                            }
                        }else{
                            $field_list .= ", ".$table. "." . "*";
                        }
                    } else { // Only single select field specified
                        $field_list .= ", ".$table. "." . $rules['select'];
                    }
                } else { // all fields to be obtained for specific table name
                    $field_list .= ", ".$table. "." . "*";
                }
            }
            // Adding Sort Rules
            if (!empty($rules['sort'])) {
                foreach ($rules['sort'] as $s) {
                  $sort_sql .= ", ".$table. "." . $s;
                }
            }
        } // close for loop over tables
        //Trimming field list by ','
        $field_list = trim($field_list, ',');

        if(!empty($data['order_id']) ){
            $whr = " WHERE ".DB_PREFIX . "order_product.order_id = ". (int)$data['order_id'];
        }
        if(!empty($data['order_product_id']) ){
            if($whr != ""){ $whr .= " AND ";}
            else{ $whr = " WHERE "; }
            $whr .= DB_PREFIX . "return.order_product_id = ". (int)$data['order_product_id'];
        }
        if(!empty($data['return_ids']) ){
            if($whr != ""){ $whr .= " AND ";}
            else{ $whr = " WHERE "; }
            $whr .= DB_PREFIX . "return.return_id IN (". $data['return_ids']. ") ";
        }
        if(!empty($data['master_return_id']) ){
            if($whr != ""){ $whr .= " AND ";}
            else{ $whr = " WHERE "; }
            $whr .= DB_PREFIX . "return.master_return_id = ". (int)$data['master_return_id'];
        }

        if($whr_suborder != ""){
            if($whr != ""){ $whr .= " AND ";}
            else{ $whr = " WHERE "; }
            $whr .= $whr_suborder;
        }

        //Prepare mail Sql query to get return and master return data
        $sql .= "SELECT 
                     ".$field_list." , 
                     " . DB_PREFIX . "return.quantity as return_quantity,
                     " . DB_PREFIX . "return.comment as return_comment,
                     " . DB_PREFIX . "return.shipping_method as shipping_method,
                     " . DB_PREFIX . "return.date_added as date_added
                  FROM " . DB_PREFIX . "return
                  INNER JOIN " . DB_PREFIX . "master_return 
                    ON " . DB_PREFIX . "master_return.master_return_id = " . DB_PREFIX . "return.master_return_id
                  INNER JOIN " . DB_PREFIX . "order_product
                    ON " . DB_PREFIX . "order_product.order_product_id = " . DB_PREFIX . "return.order_product_id " .
                  $join_tbl.
                  $whr;
        //add ORDER By if sorting is mentioned in selector
        if(!empty($sort_sql)){
            $sql .= " ORDER BY ". trim($sort_sql , ','); 
        }else{
            $sql .= " ORDER BY " . DB_PREFIX . "return.debit_note_id DESC, " . DB_PREFIX . "return.credit_note_id DESC"; 
        }
        
        // Executing SQL query
        $query = $db->query($sql);

        //If need return action details
        $return_actions = array();
        if($return_action_sql != ''){
            $action_data = $db->query($return_action_sql);
            $return_actions = $action_data->rows;
            $return_actions = array_combine(
                                array_column($return_actions, 'return_action_id'),
                                $return_actions
                              );
        }
        //If need return reason details
        $return_reasons = array();
        if($return_reason_sql != ''){
            $reason_data = $db->query($return_reason_sql);
            $return_reasons = $reason_data->rows;
            $return_reasons = array_combine(
                                array_column($return_reasons, 'return_reason_id'),
                                $return_reasons
                              );   
        }

        if ($query->num_rows) {
            // Set return_info array with mentioned table keys
           foreach ($query->rows as $row) {
              if (array_key_exists("oc_return",$selector)){
                if(!empty($selector['oc_return']['select'])){
                    foreach ($selector['oc_return']['select'] as $f) {
                      $return_info['oc_return'][$row['return_id']][$f] = $row[$f];
                    }
                }else{
                    $return_info['oc_return'][$row['return_id']] = $row;
                }
              }
              if (array_key_exists("oc_master_return",$selector)){
                if(!empty($selector['oc_return']['select'])){
                    foreach ($selector['oc_master_return']['select'] as $f) {
                      $return_info['oc_master_return'][$row['master_return_id']][$f] = $row[$f];
                    }
                }else{
                    $return_info['oc_master_return'][$row['master_return_id']] = $row;
                }
                
              }
              if (array_key_exists("oc_return_action",$selector)){
                $return_info['oc_return'][$row['return_id']]['return_action'] = !empty($row['return_action_id'])?$return_actions[$row['return_action_id']]:'';
              }
              if (array_key_exists("oc_return_reason",$selector)){
                $return_info['oc_return'][$row['return_id']]['return_reason'] = !empty($row['return_reason_id'])?$return_reasons[$row['return_reason_id']]:'';
              }
           }
        } // close if ($query->num_rows)
        return $return_info;
    }// close getOrderInfo function

	/**
     * Public Function to get all Master returns against orderId
     * @param: $order_id
     * @return: $data Array
     * @author: Nishu, Jan 2018
    */
    public function getAllMasterReturnsByOrderId($order_id = ''){
        $data = array();
        if(empty($order_id)){
            return $data;
        }
        $sql = "SELECT * 
                 FROM " . DB_PREFIX . "master_return 
                 WHERE 
                   order_id = ". (int)$order_id ."
               ";
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            foreach ($result->rows as $key => $value) {
                $data[$value['master_return_id']] = $value;
                if(!empty($value['return_shipment_tracking_id'])){
                    $data[$value['master_return_id']]['is_active'] = 1;
                }else{
                    $data[$value['master_return_id']]['is_active'] = 0;
                }
            }
        }
        return $data;
    }

    /**
     * Public Function to get all Master returns against orderId
     * @param: $return_id
     * @return: $data Array
     * @author: Nishu, Jan 2018
    */
    public function getReturnById($return_ids = ''){
        $data = array();
        if(empty($return_ids)){
            return $data;
        }
        $sql = "SELECT * 
                 FROM " . DB_PREFIX . "return 
                 WHERE 
                   return_id IN (". $this->db->escape($return_ids) .")
               ";
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            $data = $result->rows;
        }
        return $data;
    }

    /**
     * Public Function to get return reason
     * if return_reason_id is given then retunr_reason will return
     * if return_reason_id is not given then all avaiable return reasons will return
     * @param: $return_reason_id
     * @return: $data Array
     * @author: Nishu, Jan 2018
    */
    public function getReturnReason($return_reason_id = 0){
        $data = array();
        $whr = '';
        if(!empty($return_reason_id)){
            $whr = " AND return_reason_id = ".(int)$return_reason_id;
        }
        $sql = "SELECT * 
                 FROM " . DB_PREFIX . "return_reason 
                 WHERE status = 1 " . $whr;
        
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            $data = $result->rows;
        }
        $data = array_combine( 
                    array_column($data, 'return_reason_id'), 
                    $data
                    );
        return $data;
    }

    /**
     * Public Function to get return action
     * if return_action_id is given then retunr_action will return
     * if return_action_id is not given then all avaiable retunr_action will return
     * @param: $return_action_id
     * @return: $data Array
     * @author: Nishu, Jan 2018
    */
    public function getReturnAction($return_action_id = ''){
        $data = array();
        $whr = '';
        if(!empty($return_action_id) || $return_action_id === 0){
            $whr = " WHERE return_action_id = ".(int)$return_action_id;
        }
        $sql = "SELECT * 
                 FROM " . DB_PREFIX . "return_action " . $whr;
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            $data = $result->rows;
        }
        $data = array_combine( 
                    array_column($data, 'return_action_id'), 
                    $data
                    );
        return $data;
    }

    /**
     * Public Function to get return action
     * if return_action_id is given then retunr_action will return
     * if return_action_id is not given then all avaiable retunr_action will return
     * @param: $return_action_id
     * @return: $data Array
     * @author: Nishu, Jan 2018
    */
    public function getReturnActionNameById($return_action_id = ''){
        $return_action_name = '';
        if(!empty($return_action_id) || $return_action_id === 0){
            $sql = "SELECT name 
                     FROM " . DB_PREFIX . "return_action WHERE return_action_id = ".(int)$return_action_id;
            $result = $this->db->query($sql);
            if($result->num_rows > 0){
                $return_action_name = $result->row['name'];
            }
        }
        return $return_action_name;
    }

    /**
    * Public function to get reverse shipment for nuvoex 
    *@param: Order No
    *@return: array
    *@author: Nishu, August 2017
    */
    public function getReverseShipments($order_no){
        $data = array();
        $sql  = "SELECT 
                    *, 
                    rst.status as shipment_status, 
                    GROUP_CONCAT(mr.master_return_id) as master_return_id
                    FROM ".DB_PREFIX."return_shipment_tracking rst
                INNER JOIN ".DB_PREFIX."master_return as mr ON mr.return_shipment_tracking_id = rst.shipping_id
                INNER JOIN ".DB_PREFIX."warehouse_address as wa ON wa.warehouse_id = rst.warehouse_id
                WHERE rst.order_no = '". $this->db->escape($order_no) ."'
                GROUP BY rst.tracking_no
                ";
        $results = $this->db->query($sql);
        if($results->num_rows > 0){
            $data = $results->rows;
        }
        return $data;
    }

    /**
     * @info: Public method to get all returns by order_product_ids
     * @param: $op_ids Array
     * @return $returns Array
     * @author Nishu, March 2018
    */
    public function getReturnsByOpIds($op_ids){
        $data = array();

        if(empty($op_ids)){
            return $data;
        }
        
        $sql = "
                SELECT * FROM ".DB_PREFIX."return
                  WHERE 
                    order_product_id IN (". implode(',', $op_ids) .")
                    AND active_row = 1
               ";

        $result = $this->db->query($sql);

        if($result->num_rows > 0){
            $data = $result->rows;
        }
        return $data;
    }

    /**
     * @info: Public method to get already returned quantity
     * @param:  $op_id Integer, 
                $return_ids (Optional): 
                     If you want to skip specific Return Ids to add Qty,
                     Value will be as comma seperated string
     * @return: Integer
     * @author: 
    */
    public function getReturnedQtyByOpId($op_id, $return_ids = ''){
        $return_quantity = 0;
        $return_actions_not_to_deduct_qty = array(
                                                    RETURN_ACTION_IDS['Return_Request_Rejected'],
                                                    RETURN_ACTION_IDS['Replacement_Request_Rejected'],
                                                    RETURN_ACTION_IDS['Return_Goods_Rejected'],
                                                    RETURN_ACTION_IDS['Shipment_Back_To_Customer'],
                                                    RETURN_ACTION_IDS['Customer_Picked_Items'],
                                                    RETURN_ACTION_IDS['Cancelled_By_Customer']
                                                  );
        $sql = "
                SELECT 
                   SUM(ocr.quantity) AS return_quantity
                   FROM
                     ".DB_PREFIX."return AS ocr
                      INNER JOIN
                   ".DB_PREFIX."return_reason AS orr ON orr.return_reason_id = ocr.return_reason_id

                   WHERE
                      ocr.order_product_id = ". (int)$op_id ."
                      AND ocr.active_row = 1
                      AND ocr.return_action_id NOT IN (".implode(',', $return_actions_not_to_deduct_qty) .")
               ";
        if(!empty($return_ids)){
            $sql .= " AND ocr.return_id NOT IN (". $return_ids .") ";
        }

        $result = $this->db->query($sql);
        
        if($result->num_rows > 0){
            $return_quantity = $result->row['return_quantity'];
        }

        return $return_quantity;
    }

    /**
     * @info: Public method to, Get CN Id and DN Id: Return Id wise
     * @param: $return_ids, Array of Max(Active) ReturnIds
     * @return: Array of CN Id and DN Id with Return ids
     * @author: Nishu, June 2018
    */
    public function getReturnIdWiseCnDn($return_ids){
        //Array Initialize
        $data = array();
        
        //NotEmpty Check for Input return ids
        if(!empty($return_ids)){
            $sql = "
                    SELECT  
                        MAX(ort.return_id) AS max_return_id,
                        MAX(ort.credit_note_id) AS credit_note_id,
                        MAX(ort.debit_note_id) AS debit_note_id,
                        MAX(ort.replacement_note_id) AS replacement_note_id
                    FROM
                        oc_return ort 
                    JOIN (SELECT 
                            order_product_id, master_return_id 
                          FROM 
                            oc_return 
                          WHERE 
                            return_id IN (". implode(',', $return_ids) .")
                         ) dt ON dt.order_product_id = ort.order_product_id  
                             AND dt.master_return_id = ort.master_return_id 

                    GROUP BY 
                        ort.order_product_id , ort.master_return_id
                  ";
            $result = $this->db->query($sql);

            if($result->num_rows > 0){
                foreach ($result->rows as $value) {
                    $data[$value['max_return_id']]['cn_id'] = $value['credit_note_id'];
                    $data[$value['max_return_id']]['dn_id'] = $value['debit_note_id'];
                    $data[$value['max_return_id']]['rn_id'] = $value['replacement_note_id'];
                }
            }
        }

        return $data;
    }

    public function getComboProductForOrderProductIds($order_product_ids = array())
    {
        $data = array();
        if(empty($order_product_ids)) {
            return false;
        }
        $sql = "
                SELECT 
                        GROUP_CONCAT( DISTINCT op1.order_product_id) AS order_product_id,
                        op.seller_invoice_id,
                        op1.combo_product_id

                FROM 
                    ".DB_PREFIX."order_product op
                INNER JOIN ".DB_PREFIX."order_product op1 ON (
                            op1.combo_product_id = op.combo_product_id
                            AND op1.order_id = op.order_id
                        )
                WHERE
                    op.order_product_id IN (".implode(',', $order_product_ids).")
                    AND op.combo_product_id > 0 
                    AND op.combo_product_id != op.product_id
                    AND op.seller_invoice_id = op1.seller_invoice_id
                GROUP BY 
                    op1.combo_product_id, op.seller_invoice_id
        ";
        
        $result = $this->db->query($sql);
        if($result->num_rows > 0) {
            //Loop over result to format data according to our requirment
            foreach ($result->rows as $row) {
                $combo_product_id  = $row['combo_product_id'];
                $seller_invoice_id = $row['seller_invoice_id'];
                if(!isset($data[$combo_product_id][$seller_invoice_id])){
                    $data[$combo_product_id][$seller_invoice_id] = $row['order_product_id'];
                }else{
                    $data[$combo_product_id][$seller_invoice_id] = ','.$row['order_product_id'];
                }
            }
            
        }

        return $data;
    }

    /**
     * @info : Public method to get Defected images for order_product_id and master_return_id
     * @param: $data AS Array - order_product_id and master_return_id
     * @return: $images AS Array
     * @author: Nishu, July 2018
    */
    public function getReturnsDefectedImages($data){
        $images = array();
        if(!empty($data['order_product_id']) && !empty($data['master_return_id'])){
            $sql = "
                    SELECT 
                        rdi.image
                    FROM
                        " . DB_PREFIX . "return_defect_images AS rdi
                    INNER JOIN
                        " . DB_PREFIX . "return AS ocr ON rdi.return_id = ocr.return_id
                    WHERE
                        ocr.order_product_id = ". (int)$data['order_product_id'] ."
                        AND ocr.master_return_id = ". (int)$data['master_return_id'] ."
                   ";
            $result = $this->db->query($sql);
            if($result->num_rows > 0){
                $images = $result->rows;
            }
        }
        return $images;
    }

    /**
     * @ info: Public method to get all return for order
     * @param: $data Array
                    keys - order_id (required)
                           customer_id (required)
                           master_return_id (optional)
     * @return: returns as array and return_no as string
    */
    public function getReturnDetailsByOrderId($data){
        $returns = array();

        //Check for required data keys
        if(!empty($data['order_id']) && !empty($data['customer_id'])){
            $sql = "
                    SELECT 
                        o.order_no,
                        ocr.return_id,
                        ocr.master_return_id,
                        ocr.quantity AS return_qty,
                        ocr.return_reason_id,
                        ocr.return_action_id,
                        ocr.comment,
                        orr.name AS return_reason,
                        op.order_product_id,
                        op.model,
                        op.name,
                        op.quantity,
                        op.piece_in_set,
                        op.price_per_piece
                    FROM
                        " . DB_PREFIX . "return AS ocr
                    INNER JOIN
                        " . DB_PREFIX . "return_reason AS orr ON ocr.return_reason_id = orr.return_reason_id
                    INNER JOIN 
                        " . DB_PREFIX . "order_product AS op ON op.order_product_id = ocr.order_product_id
                    INNER JOIN
                        " . DB_PREFIX . "order AS o ON o.order_id = op.order_id
                    WHERE
                        op.order_id = ". (int)$data['order_id'] ."
                        AND ocr.active_row = 1
                        AND o.customer_id = ". (int)$data['customer_id'] ."
                   ";
            if(!empty($data['master_return_id'])){
                $sql .= " AND ocr.master_return_id = ". (int)$data['master_return_id'];
            }

            $sql .= " GROUP BY ocr.return_id";
            
            $query = $this->db->query($sql);
            if($query->num_rows > 0){
                $returns = $query->rows;
                $returns = array_combine(
                                array_column($returns, 'return_id'),
                                $returns
                            );
            }
        }
        return $returns;
    }
    
    /**
     * Public Function to get all CNs against orderId & suborderId
     * @param: integer $order_id
     * @param: integer $suborder_id
     * @param: array $param
     * @return: array $data 
     * @author: Nishu, Jan 2018
    */
    public function getAllCNBySubOrderId($db, $order_id = '', $suborder_id = '', $param = array()){
        
        $data = array();
        $check_field = '';

        if(empty($order_id) || empty($suborder_id)){
            return $data;
        }

        if(!empty($param)) {
            $check_field .= " AND " ;
            $field_count = count($param);
            $counter = 0;
            foreach ($param as $key => $value) {
                $check_field .=  $key . " = " . $value;
                if($counter < $field_count-1 ){
                    $check_field .= " AND ";
                }
              $counter++;      
            }
        }

        $sql = "
                SELECT 
                    `credit_note_id`,
                    `credit_note_status`
                FROM 
                   " . DB_PREFIX . "credit_note  
                WHERE
                    order_id = ".(int)$order_id."
                    AND
                    suborder_id = '".$db->escape($suborder_id)."'
                " . $check_field ;
        $result = $db->query($sql);
        if($result->num_rows > 0){
            foreach ($result->rows as $key => $value) {
                $data[$value['credit_note_id']] = $value;
            }
        }
        return $data;
    }

    /**
     * Public method to get customer COD security balance amount
     * @param: Object DB object - $db
     * @param: integer customer master id
     * @return: float COD security balance
     * @author: MSA Sept 2018
     */
    public static function getCustomerCODSecurityData($db, int $master_id)
    { 
            $sql = "
                SELECT 
                   GROUP_CONCAT(DISTINCT IF(ocor.table_name = 'oc_receipt_sub_csv', ocor.table_id, NULL)) AS table_id,
                   ( 
                        SUM( IFNULL(orsc.amount, 0) ) 
                        - 
                        SUM( IFNULL(ps.amount, 0) )
                    ) as  cod_security_balance
                FROM  
                     " . DB_PREFIX ."cod_security as oco
                INNER JOIN
                        " . DB_PREFIX ."cod_security_to_receipt AS ocor 
                            ON ocor.master_id = oco.master_id
                LEFT JOIN 
                    " . DB_PREFIX ."receipt_sub_csv AS orsc 
                        ON orsc.oc_receipt_sub_csv_id = ocor.table_id 
                            AND 
                           ocor.table_name = 'oc_receipt_sub_csv'
                 LEFT JOIN 
                    " . DB_PREFIX ."payment_sub AS ps 
                        ON ps.payment_sub_id = ocor.table_id 
                            AND 
                           ocor.table_name = 'oc_payment_sub'         
                WHERE 
                    oco.master_id = ".(int)$master_id."
            ";
        $result = $db->query( $sql );    
        if( $result->num_rows ) {
            return $result->row;
        }
        return array();
    }

    /**
     * @info: Public method to get master level returns data for given customer_id
     *        This method's main use to show return list data in frontend for web and mobile app
     * @param: array $data
     *          keys: customer_id (required)
     *                order_no (optional)
     * @return: array with keys:- order_no, return_no, master_return_id, total_pieces, customer_id
     * @author: Nishu, Sept 2019
    */
    public function getReturnListData(array $filter_data){
        $data = array();
        if(!empty($filter_data['customer_id'])){
            $sql = "
                    SELECT 
                        mr.master_return_id,
                        mr.return_no,
                        MAX(o.order_no) AS order_no,
                        MAX(o.customer_id) AS customer_id,
                        SUM(r.quantity) AS total_pieces
                    FROM
                        ".DB_PREFIX."master_return AS mr
                    INNER JOIN 
                        ".DB_PREFIX."return AS r ON mr.master_return_id = r.master_return_id
                        AND r.active_row = 1
                    INNER JOIN
                        ".DB_PREFIX."order AS o ON mr.order_id = o.order_id
                    WHERE
                        o.customer_id = ".(int)$filter_data['customer_id']."
                        AND mr.cancel_return = 0
                   ";
            $filter_order_no = trim($filter_data['order_no'] ?? '');
            if(!empty($filter_order_no)){
                $sql .= " AND o.order_no LIKE '".$this->db->escape($filter_order_no)."' ";
            }

            if(!empty($filter_data['beyond_master_return_id'])){
                $sql .= " AND mr.master_return_id < ".(int)$filter_data['beyond_master_return_id'];
            }

            $sql .= " GROUP BY 
                        mr.master_return_id 
                      ORDER BY
                        mr.master_return_id DESC ";

            $page  = (int)($filter_data['page'] ?? 1);
            $limit = (int)$filter_data['limit'];

            $start = ($page - 1) * $limit;
            $sql  .= " LIMIT " . $start . "," . $limit;

            $result = $this->db->query($sql);
            if($result->num_rows > 0){
                $data = $result->rows;
            }
        }

        return $data;
    }

    /**
     * @info: Public method for return detail page, for product return details
     * @return: array
     * @author: Nishu, Sept 2019
    */
    public function getProductsForReturnDetailPage(int $master_return_id){
        $data = array();
        if(!empty($master_return_id)){
            $sql = "
                    SELECT 
                        r.return_id,
                        r.master_return_id,
                        r.quantity AS return_quantity,
                        rr.name AS return_reason,
                        r.comment,
                        r.return_action_id,
                        op.order_product_id,
                        op.name,
                        op.model,
                        op.price_per_piece,
                        (op.quantity * op.piece_in_set) AS total_pieces,
                        p.product_id, p.image
                    FROM
                        ".DB_PREFIX."return AS r
                    INNER JOIN 
                        ".DB_PREFIX."return_reason AS rr ON r.return_reason_id = rr.return_reason_id
                                                         AND rr.language_id = 1
                    INNER JOIN
                        ".DB_PREFIX."order_product AS op ON op.order_product_id = r.order_product_id 
                    INNER JOIN
                        ".DB_PREFIX."product AS p ON op.product_id = p.product_id
                    WHERE
                        r.master_return_id = ".(int)$master_return_id."
                        AND r.active_row = 1
                   ";

            
            $result = $this->db->query($sql);
            if($result->num_rows > 0){

                //Load images model, if we have to show images with product details
                $this->load->model('tool/image');
                if (method_exists($this->registry, 'get')) {
                    $model_tool_image = $this->registry->get('model_tool_image');
                } else {
                    $model_tool_image = $this->registry->model_tool_image;
                }

                foreach ($result->rows as $key => $value) {
                    $data[$value['return_id']] = $value;
                    $data[$value['return_id']]['image'] = $model_tool_image->resize(
                                                                $value['image'], 
                                                                $this->config->get('config_image_product_width'), 
                                                                $this->config->get('config_image_product_height')
                                                            );
                    $data[$value['return_id']]['thumb'] = $model_tool_image->resize(
                                                                $value['image'], 
                                                                $this->config->get('config_image_additional_width'), 
                                                                $this->config->get('config_image_additional_height')
                                                            );
                }
            }

        }

        return $data;
    }

    
    /**
     * @info: Public method for return detail page, for getting product return status which is vieable to customer
     * @param: array
     * @return: array
     * @author: Nishu, Sept 2019
    */
    public function getProductReturnStatusForReturnDetailPage(array $data){
        $results = array();

        if(!empty($data)){

            $sql = "
                    SELECT 
                        dt.master_return_id,
                        dt.order_product_id,
                        ra2.name AS return_action_name
                    FROM
                        (SELECT 
                            rt.master_return_id,
                                rt.order_product_id,
                                MAX(rt.return_id) AS latest_return_id
                        FROM
                            oc_return rt
                        JOIN oc_return_action ra ON ra.return_action_id = rt.return_action_id
                            AND ra.viewable_to_customer = 1
                            AND ra.language_id = 1
                        WHERE
                            (rt.master_return_id , rt.order_product_id) IN (
                    ";
            foreach ($data as $key => $value) {
                $sql .= " (". (int)$value['master_return_id'] ." , ". (int)$value['order_product_id'] .") ,";
            }
            $sql = trim($sql, ',');
            $sql .= " ) ";
            $sql .=  "
                        GROUP BY rt.master_return_id , rt.order_product_id) AS dt
                            STRAIGHT_JOIN
                        oc_return rt2 ON rt2.return_id = dt.latest_return_id
                            STRAIGHT_JOIN
                        oc_return_action ra2 ON ra2.return_action_id = rt2.return_action_id
                            AND ra2.language_id = 1

                   ";

            $query = $this->db->query($sql);
            if($query->num_rows > 0){
                $results = $query->rows;                
            }
        }

        return $results;
    } 

    public function getReturnDetailPage($data){
        $resp = array();

        if(!empty($data)){
            $master_return_id = (int)($data['master_return_id'] ?? 0);

            $master_return_data = $this->getMasterReturnData($master_return_id);
            $resp = $master_return_data;

            //Get Return(s) List by given request params for single customer
            $products_data = $this->getProductsForReturnDetailPage( $master_return_id );
            $resp['products'] = array();

            if(!empty($products_data)){
                $filter_data = array();

                $order_product_ids = array_column($products_data, 'order_product_id');
                $temp = array();
                $temp['order_id'] = $master_return_data['order_id'] ?? 0;
                $temp['shipping_address']['shipping_name']      = $master_return_data['shipping_name'] ?? '';
                $temp['shipping_address']['shipping_company']   = $master_return_data['shipping_company'] ?? '';
                $temp['shipping_address']['shipping_address_1'] = $master_return_data['shipping_address_1'] ?? '';
                $temp['shipping_address']['shipping_address_2'] = $master_return_data['shipping_address_2'] ?? '';
                $temp['shipping_address']['shipping_city']      = $master_return_data['shipping_city'] ?? '';
                $temp['shipping_address']['shipping_postcode']  = $master_return_data['shipping_postcode'] ?? '';
                $temp['shipping_address']['shipping_country']   = $master_return_data['shipping_country'] ?? '';
                $temp['shipping_address']['shipping_zone']      = $master_return_data['shipping_zone'] ?? '';
                //Get Pickup address for master return (depends over self_shipment or wsb_pickup)
                $resp['pickup_address'] = $this->getMasterReturnPickupAddress($order_product_ids, $master_return_data['shipping_method'], $temp);
                
                $resp['show_upload_courier_btn'] = false;
                if(
                    trim($master_return_data['shipping_method']) == "self_courier" &&
                    empty($master_return_data['return_shipment_tracking_id'])
                ){
                    $resp['show_upload_courier_btn'] = true;
                }

                $total_return_pieces = array_sum(array_column($products_data, 'return_quantity'));
                $resp['total_return_pieces'] = $total_return_pieces;

                foreach ($products_data as $key => $value) {
                    $filter_data[$key]['master_return_id'] = $value['master_return_id'];
                    $filter_data[$key]['order_product_id'] = $value['order_product_id'];
                }
                
                $statuses_return_wise = $this->getProductReturnStatusForReturnDetailPage($filter_data);

                //Loop over Product wise data and merge return status to response
                foreach ($products_data as $return_id => $details) {
                    $resp['products'][$return_id] = $details;
                    $resp['products'][$return_id]['return_status'] = '';

                    foreach ($statuses_return_wise as $return_status) {
                        if(
                            $return_status['master_return_id'] == $details['master_return_id'] &&
                            $return_status['order_product_id'] == $details['order_product_id']
                        ){
                            $resp['products'][$return_id]['return_status'] = $return_status['return_action_name'] ?? '';
                        }
                    }
                }
            }
            $credit_note = new CreditNote($this->registry);
            $resp['credit_note_data'] = $credit_note->getAllCreditNoteByMasterReturnId($master_return_id);
            $resp['total_credit_note_amount'] = 0;
            $resp['total_net_refundable']     = 0;

            if(!empty($resp['credit_note_data'])){
                //Set Credit note download link in response
                foreach ($resp['credit_note_data'] as $key => $credit_note) {
                    $encode_file = array();
                    $encode_file['order_id']       = (int)($credit_note['order_id'] ?? 0);
                    $encode_file['credit_note_id'] = (int)($credit_note['credit_note_id'] ?? 0);
                    $encode_file                   = base64_encode(serialize($encode_file));
                    
                    $securefiledownload  = new SecureFileDownload($this->registry);
                    $creditnote_link     = $securefiledownload->getDownloadLink('buyer_credit_note',$encode_file, false);

                    //Set dowmload link
                    $resp['credit_note_data'][$key]['download_link'] = $creditnote_link;
                }

                $resp['total_credit_note_amount'] = round(array_sum(
                                                            array_column(
                                                                $resp['credit_note_data'],
                                                                'credit_note_amount'
                                                            )
                                                          ), 2);
                $resp['total_net_refundable'] = round(array_sum(
                                                            array_column(
                                                                $resp['credit_note_data'],
                                                                'net_refundable'
                                                            )
                                                    ), 2);
            }

            
        }
        return $resp;
    }

    private function getMasterReturnData(int $master_return_id){
        $data = array();
        if(!empty($master_return_id)){
            $sql = "
                    SELECT
                        mr.master_return_id,
                        mr.return_no,
                        mr.shipping_method,
                        mr.return_shipment_tracking_id,
                        o.order_id,
                        o.order_no,
                        TRIM(CONCAT(o.shipping_firstname,' ', o.shipping_lastname)) AS shipping_name,
                        o.shipping_company,
                        o.shipping_address_1,
                        o.shipping_address_2,
                        o.shipping_city,
                        o.shipping_postcode,
                        o.shipping_country,
                        o.shipping_zone,
                        mr.return_shipment_tracking_id,
                        rst.tracking_no
                    FROM
                        ".DB_PREFIX."master_return AS mr
                    INNER JOIN ".DB_PREFIX."order AS o
                        ON o.order_id = mr.order_id
                    LEFT JOIN ".DB_PREFIX."return_shipment_tracking AS rst
                        ON rst.shipping_id = mr.return_shipment_tracking_id
                    WHERE
                        mr.cancel_return = 0
                        AND mr.master_return_id = ".(int)$master_return_id."
                    ORDER BY 
                        NULL
                   ";
            $result = $this->db->query($sql);
            if($result->num_rows > 0){
                $data = $result->row;
            }
        }
        return $data;
    }

    private function getMasterReturnPickupAddress($order_product_ids, $shipping_method, $data){
        $pickup_address = array();

        if(!empty($order_product_ids)){
            if(!is_array($order_product_ids)){
                $order_product_ids = array($order_product_ids);
            }

            if(
                !empty($shipping_method) && 
                $shipping_method == 'self_courier'
            ){
                $pickup_address_code = OrderInfo::getPickupCityCodes($this->db,$order_product_ids);
                if( count($pickup_address_code) > 1) {
                    $pickup_city_code = 'JP';
                }else{
                    $pickup_city_code = $pickup_address_code[0]['pickup_city_code'];
                }

                $pickup_address =  OrderInfo::getWarehouseDetailsByCityCode($this->db,$pickup_city_code);
                $pickup_address['address_title'] = "Shipping Details: ";
                $pickup_address['pickup_type']   = "Self Shipment";

            }else{
                if(!empty($data) && !empty($data['shipping_address'])){
                    $pickup_address =  $data['shipping_address'];
                }else{
                    $selector = array(
                                'order'=> array('select' => array(
                                            'TRIM(CONCAT(shipping_firstname," ", shipping_lastname)) AS shipping_name', 
                                            'shipping_company',
                                            'shipping_address_1',
                                            'shipping_address_2',
                                            'shipping_city',
                                            'shipping_postcode',
                                            'shipping_country',
                                            'shipping_zone') 
                                          )
                               );
                    //Get OrderInfo details
                    $order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'],'',$selector);
                    $pickup_address =  $order_info['order'];
                }
                $pickup_address['address_title'] = "Pick Up Details: ";
                $pickup_address['pickup_type']   = "Wholesale Box Pickup";
            }
        }

        return $pickup_address;
    }

    /**
     * @info: Public method for API use to get list of all returnable products agaisn given order_id
     *        all give return reason list available product wise , which is depends on subrder's delivery date
     *        Including order product complete details
     * @param: $data array
     * @return: array
     * @author: Nishu, Sept 2019 
    */
    public function getReturnableProducts($data){
        $all_products = array();
        if(!empty($data) && !empty($data['order_id']) ){
            $filter_data['select'] = array(
                                            "op.order_product_id",
                                            "op.name",
                                            "op.model",
                                            "op.product_id",
                                            "(op.quantity*op.piece_in_set) AS total_pieces",
                                            "op.buyer_invoice_id",
                                            "op.is_returnable",
                                            "op.seller_invoice_id",
                                            "o.order_id",
                                            "o.order_no",
                                            "op.suborder_id",
                                            "DATE(osub.delivered_date) AS delivered_date",
                                            "DATE(osub.delivered_date) AS status_date",
                                            "osub.order_status_id AS suborder_status",
                                            "op.combo_product_id"
                                        );
            $filter_data['where']  = array("op.is_returnable = 1");
            $all_order_products = OrderProduct::getOrderProductByOrderId($this->db, (int)$data['order_id'], $filter_data);

            $all_return_reasons = array_flip(RETURN_REASON_IDS);
            
            //Loop over order_product to get return_reasons order_product wise
            $return_action_base = new ReturnActionBase($this->registry);

            $limit_in_days_for_quality     = (int)($data['limit_in_days_for_quality'] ?? 4);
            $limit_in_days_for_replacement = (int)($data['limit_in_days_for_replacement'] ?? 17);

            $return_action_base->__set('_limit_in_days_for_quality', $limit_in_days_for_quality);
            $return_action_base->__set('_limit_in_days_for_replacement', $limit_in_days_for_replacement);
            foreach ($all_order_products as $op_id => $order_product) {
                $return_reasons = $return_action_base->returnReasonsForOrderProduct($order_product);

                //If return_reasons array empty then that order_product is not returnable
                if(empty($return_reasons)){
                    unset($all_order_products[$op_id]);
                    continue;
                }
                
                //Set Return reasons name from id
                foreach ($return_reasons as $reason_type => $reasons) {
                    $all_order_products[$op_id]['return_reasons'][$reason_type]['title'] = $reasons['title'];
                    //Loop return reasons
                    foreach ($reasons['reasons'] as  $reason_key => $return_reason_id) {
                        $all_order_products[$op_id]['return_reasons'][$reason_type]['reasons'][$return_reason_id] = $all_return_reasons[$return_reason_id] ?? '';
                    }
                    
                }
                $all_order_products[$op_id]['image']                       = "";
                $all_order_products[$op_id]['buyback_limit_days_msg']      = "";
                $all_order_products[$op_id]['buyback_limit_remaining_msg'] = "";
            }

            //Get returnable unique order_product_ids
            $all_order_product_ids = array_column($all_order_products, 'order_product_id');

            //Set OrderProduct images
            $order_product_wise_images = Product::getOrderProductOptionImages($this->db, $all_order_product_ids);
            foreach ($order_product_wise_images as $op_id => $image) {
                $all_order_products[$op_id]['image'] = STATIC_CONTENT_URL_SSL.$image;
            }


            //Set SOR reutrn messages for order product
            $sor_order_product = new SorOrderProduct($this->registry);
            $all_order_product_sor_day_limit = $sor_order_product->getSorPeriodByOrderProductIds($all_order_product_ids);
            
            foreach ($all_order_product_sor_day_limit as $op_id => $days_limit) {
                
                if($days_limit > 0){
                    $delivered_date = $all_order_products[$op_id]['delivered_date'];
                    $max_date_for_sor = date(
                                                'd-m-Y',
                                                strtotime(
                                                    "+".$days_limit." days",
                                                  strtotime($delivered_date)
                                                )
                                            );
                
                    $start = strtotime(date('d-m-Y'));
                    $end   = strtotime($max_date_for_sor);

                    $days_diff = $end - $start;
                    $sor_remaining_days = ($days_diff > 0) ? ceil($days_diff / 86400) : 0;
                    $all_order_products[$op_id]['buyback_limit_days_msg'] = "Buyback Guarantee within ". $days_limit. " Days";
                    $all_order_products[$op_id]['buyback_limit_remaining_msg'] = "Buyback Guarantee days remaining: ".$sor_remaining_days;
                }
            }

        }
        return $all_order_products;
    }

    /**
     * @info: Public method to get pickup addres to add return
     *        pickup type can be self_courier or wsb_pickup
     *        
     * @author: Nishu, Sept 2019
    */
    public function getPickupAddress($order_product_ids, $order_id, $order_no) {
      $result = array();

      $result['pickup_type'] = array(
                                        "self_courier" => "Self Courier",
                                        "wsb_pickup"   => "Wholesalebox Pickup"
                                       );

      $result['self_courier_text'] = "";
      $result['wsb_pickup_text']   = "";

      //Pickup address for pickup type self_courier
      $self_courier = $this->getMasterReturnPickupAddress($order_product_ids, 'self_courier', array('order_id' => $order_id));

      //Set Self Courier text 
      $self_courier_text =  '<p>You have chosen self shipment for the return/replacement for Order No '.$order_no.'. </p>';
      if(!empty($self_courier)) {
        $self_courier_text .= '<p>';
        $self_courier_text .= 'Please send products to below address:<br><br>';
        $self_courier_text .= '<b>'.$self_courier['warehouse_name'].'<br>';
        $self_courier_text .= $self_courier['address_1'].'<br>';
        $self_courier_text .= $self_courier['address_2'].'<br>';
        $self_courier_text .= $self_courier['city'].'</b>';
        $self_courier_text .= '</p>';
      }   
      $self_courier_text .= '<p>If you will not ship within 3 days then your return/replacement request will be canceled. So please send following products by '.date('d-m-Y',strtotime('+3 Days')).'.</p>';
      $result['self_courier_text'] = $self_courier_text;
      
      //Set WSB Pickup text
      $wsb_pickup_text = '<p>Your Return/Replacement request for the Order No '.$order_no.'. </p>';
      $wsb_pickup_text .= '<p>Kindly pack the returned products and keep ready, at your shipping address.</p>';

      //Get Shipping address details of cusotmer for given order_id
      $wsb_pickup_address = $this->getMasterReturnPickupAddress($order_product_ids, 'wsb_pickup', array('order_id' => $order_id));
      
      if(!empty($wsb_pickup_address)) {
        $wsb_pickup_text .= '<p>';
        $wsb_pickup_text .= '<b>'.$wsb_pickup_address['shipping_company'].'<br>';
        $wsb_pickup_text .= $wsb_pickup_address['shipping_address_1'].'<br>';
        $wsb_pickup_text .= $wsb_pickup_address['shipping_address_2'].'<br>';
        $wsb_pickup_text .= $wsb_pickup_address['shipping_city'].'</br>';
        $wsb_pickup_text .= $wsb_pickup_address['shipping_zone'].', ';
        $wsb_pickup_text .= $wsb_pickup_address['shipping_postcode'].'</b>';
        $wsb_pickup_text .= '</p>';
      } 
      
      $result['wsb_pickup_text'] = $wsb_pickup_text;
      
      //Set and return Response of API
      return $result;
    }

    /**
     * @info: Public method to validate Add return data.
     *        is data param valid to add return
     * @param: array $data
     *       keys-> 
     *              order_id,   (Required)
     *              products,   (Required)
     *              pickup_type (Required)
     *
     * @author: Nishu, Sept 2019
    */
    public function validateAddReturnsData($data){
        $validate_resp = array();
        $validate_resp['is_valid'] = true;
        $validate_resp['message']  = "";

        if(!empty($data)){
            if(empty($data['customer_id'])){
                $validate_resp['is_valid'] = false;
                $validate_resp['message']  = "Customer Id must be passed to add return.";
                return $validate_resp;
            }
            if(empty($data['order_id'])){
                $validate_resp['is_valid'] = false;
                $validate_resp['message']  = "Order Id must be passed to add return.";
                return $validate_resp;
            }

            if(empty($data['products'])){
                $validate_resp['is_valid'] = false;
                $validate_resp['message']  = "No order products to add return.";
                return $validate_resp;
            }

            if(empty($data['pickup_type'])){
                $validate_resp['is_valid'] = false;
                $validate_resp['message']  = "Pickup Type must be selected to add return.";
                return $validate_resp;
            }

            //Check passed order_products must be belongs to same order
            $valid = $this->validateOrderProductsToAddReturn($data);
            if(trim($valid) != ""){
                $validate_resp['is_valid'] = false;
                $validate_resp['message']  = $valid;
                return $validate_resp;
            }

        }else{
            $validate_resp['is_valid'] = false;
            $validate_resp['message']  = "No params found to validate.";
        }

        return $validate_resp;
    }


    private function validateOrderProductsToAddReturn($data){
        $valid = "";
        if(!empty($data)){
            //First check All given order_products belongs to same order
            $order_product_ids = array_column($data['products'], 'order_product_id');
            $order_id = (int)($data['order_id'] ?? 0);
                
            if(!empty($order_product_ids)){
                $sql = "
                        SELECT
                            order_product_id
                        FROM
                            ".DB_PREFIX."order_product 
                        WHERE
                            order_product_id IN (". implode(',', $order_product_ids).")
                            AND order_id != ".(int)$order_id."
                       ";
                $results = $this->db->query($sql);

                if($results->num_rows > 0){
                    $valid = "All passed order products not belongs to given orderId.";
                }
            }

            //Check all given orderPoducts are returnable or not
            $valid = $this->checkGivenOPIdsReturnableOrNot($order_product_ids);
        }

        return $valid;
    }

    /**
     * @info: Private method to check given order products ids are returnable or not
     * @param: $order_products Array or string Or integer(i.e. single id or mutiple ids)
     * @return: string, empty if all orderProducts ids are valid
     *                  else error message
     * @author: Nishu, Sept, 2019
    */
    private function checkGivenOPIdsReturnableOrNot($order_product_ids){
        $valid = "";
        if(!empty($order_product_ids)){
            if(!is_array($order_product_ids)){
                $order_product_ids = explode(',', $order_product_ids);
            }
            $order_product_ids = array_unique($order_product_ids);

            $sql = "
                  SELECT  
                    op.order_product_id,
                    op.order_id,
                    op.suborder_id,
                    op.buyer_invoice_id,
                    DATE(osub.delivered_date) AS status_date,
                    osub.order_status_id AS suborder_status,
                    op.seller_invoice_id
                  FROM 
                    " . DB_PREFIX . "order_product AS op
                  INNER JOIN
                    ".DB_PREFIX."suborder AS osub ON op.suborder_id = osub.suborder_id
                                                AND op.order_id = osub.order_id
                  WHERE
                    op.order_product_id IN (" . implode(',', $order_product_ids) . ")
                    AND op.buyer_invoice_id > 0
                    AND op.is_returnable = 1
                  ORDER BY NULL
                  ";
            $result = $this->db->query($sql);
            if($result->num_rows > 0){

                $all_order_products = $result->rows;
                //Loop over order_product to get return_reasons order_product wise
                $return_action_base = new ReturnActionBase($this->registry);
                
                foreach ($all_order_products as $order_product) {
                    $return_reasons = $return_action_base->returnReasonsForOrderProduct($order_product);

                    //If return_reasons array empty then that order_product is not returnable
                    if(empty($return_reasons)){
                        $valid = "Passed OrderProduct Id: " .$order_product['order_product_id']. " is not returable.";
                        return $valid;
                    }
                    
                }
            }else{
                $valid = "Not even a single OrderProduct Id is not returable.";
            }
        }else{
            $valid = "OrderProduct Ids are empty.";
        }

        return $valid;
    }


    public function addNewReturnFromClient($data){
        $resp = false;
        if(!empty($data)){
            $add_data = array();

            //Get All Unique Order_product Ids 
            $order_product_ids = array_unique(array_column($data['products'], 'order_product_id'));

            //Get data by order_product_ids to add return 
            $product_data = OrderProduct::getOrderProductInfoByOPIds($this->db, $order_product_ids); 

            foreach ($data['products'] as $key => $order_product) {
                $op_id = $order_product['order_product_id'];
                
                $add_data[$key]['order_product_id']         = (int)$op_id;
                $add_data[$key]['combo_id']                 = $product_data[$op_id]['combo_product_id'];
                $add_data[$key]['seller_invoice_id']        = $product_data[$op_id]['seller_invoice_id'];
                $add_data[$key]['order_id']                 = $product_data[$op_id]['order_id'];
                $add_data[$key]['order_no']                 = $product_data[$op_id]['order_no'];
                $add_data[$key]['customer_id']              = $product_data[$op_id]['customer_id'];
                $add_data[$key]['first_name']               = $product_data[$op_id]['firstname'];
                $add_data[$key]['last_name']                = $product_data[$op_id]['lastname'];
                $add_data[$key]['email']                    = $product_data[$op_id]['email'];
                $add_data[$key]['telephone']                = $product_data[$op_id]['telephone'];
                $add_data[$key]['last_return_id']           = 0;
                $add_data[$key]['last_return_reason']       = NULL;
                $add_data[$key]['last_return_quantity']     = NULL;
                $add_data[$key]['last_shipping_method']     = NULL;
                $add_data[$key]['last_master_return_id']    = NULL;
                $add_data[$key]['last_comment']             = NULL;
                $add_data[$key]['return_quantity']          = $order_product['return_quantity'];
                $add_data[$key]['return_reason']            = $order_product['return_reason_id'];
                $add_data[$key]['shipping_method']          = $data['pickup_type'];
                $add_data[$key]['buyer_invoice_id']         = $product_data[$op_id]['buyer_invoice_id'];
                $add_data[$key]['seller_id']                = $product_data[$op_id]['seller_id'];
                $add_data[$key]['total_quantity']           = $product_data[$op_id]['total_quantity'];
                $add_data[$key]['comment']                  = $order_product['comment'];
                $add_data[$key]['customer_payment_company'] = $product_data[$op_id]['payment_company'];
            }

            //Add New return with pending status
            $return_base = new ReturnBase($this);
            $return_base->addReturnWithPendingStatus(array("data" => $add_data));
            $resp = true;

        }

        return $resp;
        /*temp_item = {
                      "order_product_id"     : op_id,
                      "combo_id"             : combo_id,
                      "seller_invoice_id"    : seller_invoice_id,
                      "customer_payment_company" : customer_payment_company,
                      "order_id"             : $('#hddn_order_id').val(),
                      "order_no"             : $('#hddn_order_no').val(),
                      "customer_id"          : $('#hddn_customer_id').val(),
                      "first_name"           : $('#hddn_first_name').val(),
                      "last_name"            : $('#hddn_last_name').val(),
                      "email"                : $('#hddn_email').val(),
                      "telephone"            : $('#hddn_telephone').val(),
                      "last_return_id"       : last_return_id,
                      "last_return_reason"   : last_return_reason, 
                      "return_reason"        : return_reason,
                      "last_return_quantity" : last_return_quantity,
                      "return_quantity"      : return_quantity,
                      "last_shipping_method" : last_shipping_method,
                      "shipping_method"      : shipping_method,
                      "last_master_return_id": last_master_return_id,
                      "buyer_invoice_id"     : buyer_invoice_id,
                      "seller_id"            : seller_id,
                      "total_quantity"       : total_quantity,
                      "last_comment"         : last_comment,
                      "comment"              : comment
                      };
            */
    }

    /**
     * @info : Public method to get uploaded courier details for self-shippment returns type
     * @param: array
     * @return: array, Courier details
     * @author: Nishu, Sept 2019 
    */
    public function getUploadedCourierDetails($data){
        $courier_details = array();
        if(!empty($data) && !empty($data['master_return_id'])){
            $sql = "
                    SELECT 
                        rst.courier_company,
                        rst.tracking_no,
                        rst.shipping_slip
                    FROM
                        ".DB_PREFIX."master_return AS mr
                    INNER JOIN ".DB_PREFIX."return_shipment_tracking AS rst
                        ON rst.shipping_id = mr.return_shipment_tracking_id
                    WHERE
                        mr.master_return_id = '".(int)$data['master_return_id']."'
                   ";
            
            $result = $this->db->query($sql);
            if($result->num_rows > 0){
                $courier_details = $result->row;
                $courier_details['shipping_slip'] = STATIC_CONTENT_URL_SSL."return/courier_slip/".$courier_details['tracking_no'].'/'.$courier_details['shipping_slip'];
            }
        }

        return $courier_details;

    }

}//End of Class

?>



