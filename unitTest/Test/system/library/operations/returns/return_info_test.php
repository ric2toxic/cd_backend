 <?php
/**
 * 	ReturnInfoTest
 * Main Class for testing all Returns Related Info.
 * @author @Nishu, Jan 2018
 */
require_once('/var/www/html/wholesalebox/config.php');
require_once(DIR_UNITTEST.'unit_test_base_helper.php');
require_once(DIR_SYSTEM.'library/operations/returns/return_info.php');
require_once(DIR_SYSTEM.'library/operations/returns/return_action_base.php');

class ReturnInfoTest extends UnitTestBaseHelper
{	
	private $return_info = null;
	public function setUp(){
		//Creating return_info object
		$this->return_info = new ReturnInfo();
	}

	/**
    * Public Function to test getAllCNByOrderId
    * @author: Nishu, Jan 2018
	*/
	public function testGetAllCNByOrderId(){
    $return_info = $this->return_info;

    //Setting Db object for testDB
    $return_info->setDb($this->getDb());

    //Test Case for not passing parameter
    $result = $return_info->getAllCNByOrderId();
    $this->assertEquals(0, count($result));

    //Test Case for Order_id not existing in credit_note table
    $order_id = 1;
    $result = $return_info->getAllCNByOrderId($order_id);
    $this->assertEquals(0, count($result));

    //Test Case for Order_id is blank
    $order_id = '';
    $result = $return_info->getAllCNByOrderId($order_id);
    $this->assertEquals(0, count($result));

    //Test Case for Single credit Note exist against given orderId in TestDB
    $order_id = 39465;
    $result = $return_info->getAllCNByOrderId($order_id);
    $this->assertEquals(1, count($result)); //Returns single CN
    $this->assertEquals(39465, $result[1]['order_id']);

    //Test Case for Multiple creditNotes exist against given orderId in TestDB
    $order_id = 26193;
    $result = $return_info->getAllCNByOrderId($order_id);
    $this->assertEquals(2, count($result));
	}

	/**
    * Public Function to test getAllDNByOrderId
    * @author: Nishu, Jan 2018
	*/
	public function testGetAllDNByOrderId(){
	  $return_info = $this->return_info;
	  //Setting Db object for testDB
	  $return_info->setDb($this->getDb());

    //Test Case for not passing parameter
    $result = $return_info->getAllDNByOrderId();
    $this->assertEquals(0, count($result));

    //Test Case for Order_id not existing in debit_note table
    $order_id = 1;
    $result = $return_info->getAllDNByOrderId($order_id);
    $this->assertEquals(0, count($result));

    //Test Case for Order_id is blank
    $order_id = '';
       $result = $return_info->getAllDNByOrderId($order_id);
    $this->assertEquals(0, count($result));

    //Test Case for Single debit Note exist against given orderId in TestDB
    $order_id = 39465;
    $result = $return_info->getAllDNByOrderId($order_id);
    $this->assertEquals(1, count($result)); //Returns single CN
    $this->assertEquals(39465, $result[1]['order_id']);

    //Test Case for Multiple debit Notes exist against given orderId in TestDB
    $order_id = 26193;
    $result = $return_info->getAllDNByOrderId($order_id);
    $this->assertEquals(2, count($result));
	}

	/**
    * Public Function to test getReturnInfo
    * @author: Nishu, Jan 2018
	*/
	public function testGetReturnInfo(){
    //$return_info = $this->return_info;
    //Setting Db object for testDB
    $db = $this->getDb();
    //Initialize array
    $data     = array();
    $selector = array();

    //Test Case1 for paasing is blank array keys and selector is not set
    $result = ReturnInfo::getReturnInfo($db, $data);
    $this->assertEquals(0, count($result['oc_return']));
    $this->assertEquals(0, count($result['oc_master_return']));

    //Test Case2 for setting array keys is blank and selector is not set
    $data['order_id']         = '';
    $data['order_product_id'] = '';
    $data['master_return_id'] = '';
    $result = ReturnInfo::getReturnInfo($db, $data);
    $this->assertEquals(0, count($result['oc_return']));
    $this->assertEquals(0, count($result['oc_master_return']));

    //Test Case3 for setting array keys and selector is empty
    $data['order_id']         = '';
    $data['order_product_id'] = '';
    $data['master_return_id'] = '';
    $result = ReturnInfo::getReturnInfo($db, $data, $selector);
    $this->assertEquals(0, count($result['oc_return']));
    $this->assertEquals(0, count($result['oc_master_return']));


    //Test Case4 for setting only Order_id array key
    $data['order_id']         = 27726;
    $data['order_product_id'] = '';
    $data['master_return_id'] = '';
    $result = ReturnInfo::getReturnInfo($db, $data);
    $this->assertEquals(10, count($result['oc_return']));
    $this->assertEquals(1, count($result['oc_master_return']));

    //Test Case5 for setting Order_id array key and selector
    $data['order_id']         = 27726;
    $data['order_product_id'] = '';
    $data['master_return_id'] = '';
    $selector['oc_return'] = array('select' => array('return_id', 'quantity', 'active_row' ));
    $selector['oc_master_return'] = array('select' => array('master_return_id', 'return_shipment_tracking_id' ));
    $result = ReturnInfo::getReturnInfo($db, $data, $selector);
    $this->assertEquals(10, count($result['oc_return']));
    $this->assertEquals(1, count($result['oc_master_return']));

    //Test Case6 for setting wrong Order_id array key against oop_id
    $data['order_id']         = 27727;
    $data['order_product_id'] = 290472;
    $data['master_return_id'] = 4082;
    $selector['oc_return'] = array(
                              'select' => array('return_id', 'quantity', 'active_row' ),
                      			  'sort' => array('return_id DESC')
                      			 );
    $selector['oc_master_return'] = array('select' => array('master_return_id', 'return_shipment_tracking_id' ));
    $result = ReturnInfo::getReturnInfo($db, $data, $selector);
    $this->assertEquals(0, count($result['oc_return']));
    $this->assertEquals(0, count($result['oc_master_return']));

    //Test Case7 for setting Order_id array key and selector
    $data['order_id']         = 27726;
    $data['order_product_id'] = 290472;
    $data['master_return_id'] = 7024;
    $selector['oc_return'] = array(
                              'select' => array('return_id', 'quantity', 'active_row' ),
                        			'sort' => array('return_id DESC')
                        			);
    $selector['oc_master_return'] = array(
                                   'select' => array('master_return_id', 'return_shipment_tracking_id' ));
    $result = ReturnInfo::getReturnInfo($db, $data, $selector);
    $this->assertEquals(5, count($result['oc_return']));
    $this->assertEquals(1, count($result['oc_master_return']));
    //Check assert equal return array with $result['return']
    $return = array(
              'return_id' => 61051,
              'quantity'  => 4,
              'active_row'=> 1
              );
    $this->assertEquals($return, $result['oc_return']['61051']);

    //Test Case8 for setting Order_id and oop_id array key and selector
    $data['order_id']         = 27726;
    $data['order_product_id'] = 290472;
    $data['master_return_id'] = '';
    $selector['oc_return'] = array(
                              'select' => array('return_id', 'quantity', 'active_row' ),
                      			  'sort' => array('return_id DESC')
                      			);
    $selector['oc_master_return'] = array(
                        'select' => array('master_return_id', 
                             'return_shipment_tracking_id' ));
    $result = ReturnInfo::getReturnInfo($db, $data, $selector);
    $this->assertEquals(5, count($result['oc_return']));
    $this->assertEquals(1, count($result['oc_master_return']));
    //Check assert equal return array with $result['return']
    $return = array(
                'return_id' => 61051,
                'quantity'  => 4,
                'active_row'=> 1
                );
    $this->assertEquals($return, $result['oc_return']['61051']);

    //Test Case9 for setting Order_id array key and selector with return_id ASC
    $data['order_id']         = 27726;
    $data['order_product_id'] = 290472;
    $data['master_return_id'] = 7024;
    $selector['oc_return'] = array(
                               'select' => array('return_id', 'quantity', 'active_row' ),
    			                     'sort' => array('return_id ASC')
    			                    );
    $selector['oc_master_return'] = array('select' => array('master_return_id', 
                                                        'return_shipment_tracking_id' ));
    $result = ReturnInfo::getReturnInfo($db, $data, $selector);
    $this->assertEquals(5, count($result['oc_return']));
    $this->assertEquals(1, count($result['oc_master_return']));
    //Check assert equal return array with $result['return']
    $return = array(
              'return_id' => 61049,
              'quantity'  => 4,
              'active_row'=> 0
              );
    $this->assertEquals($return, $result['oc_return']['61049']);

    //Test Case10 for setting Order_id array key and selector with return_action_name and return_reason_name
    $data['order_id']         = 27726;
    $data['order_product_id'] = 290472;
    $data['master_return_id'] = 7024;
    $selector['oc_return'] = array(
                              'select' => array('return_id', 'quantity', 'active_row' ),
                      			 'sort' => array('return_id DESC')
                      			);
    $selector['oc_master_return'] = array(
                               'select' => array('master_return_id', 'return_shipment_tracking_id' ));
    $selector['oc_return_action'] = array('select' => array('name'));
    $selector['oc_return_reason'] = array('select' => array('name'));
    $result = ReturnInfo::getReturnInfo($db, $data, $selector);
    $this->assertEquals(5, count($result['oc_return']));
    $this->assertEquals(1, count($result['oc_master_return']));
    $this->assertEquals('61051', $result['oc_return']['61051']['return_id']);
    $this->assertEquals('4', $result['oc_return']['61051']['quantity']);
	}


  /**
   *  Public Function to test getAllMasterReturnsByOrderId
   * @author: Nishu, Jan 2018
  */
  public function testGetAllMasterReturnsByOrderId(){
    $return_info = $this->return_info;
    //Setting Db object for testDB
    $return_info->setDb($this->getDb());

    //Test Case for not passing parameter
    $result = $return_info->getAllMasterReturnsByOrderId();
    $this->assertEquals(0, count($result));

    //Test Case for Order_id not existing in master_return table
    $order_id = 1;
    $result = $return_info->getAllMasterReturnsByOrderId($order_id);
    $this->assertEquals(0, count($result));

    //Test Case for Order_id is blank
    $order_id = '';
    $result = $return_info->getAllMasterReturnsByOrderId($order_id);
    $this->assertEquals(0, count($result));

    //Test Case for Single master_return exist against given orderId in TestDB
    $order_id = 27726;
    $result = $return_info->getAllMasterReturnsByOrderId($order_id);
    $this->assertEquals(1, count($result)); //Returns single MaterReturn
    $this->assertEquals(27726, $result[7024]['order_id']);

    //Test Case for Multiple master_return exists against given orderId in TestDB
    $order_id = 16171;
    $result = $return_info->getAllMasterReturnsByOrderId($order_id);
    $this->assertEquals(2, count($result));
 }

  /**
     * Public Function to test getReturnById()
     * @author: Nishu, Jan 2018
  */
  public function testGetReturnById(){
    //Setting Db object for testDB
    $this->return_info->setDb($this->getDb());

    //TestCase to check when return_id is not passed
    $result = $this->return_info->getReturnById();
    $this->assertEquals(0, count($result));

    //TestCase when return_id is 0
    $return_id = 0;
    $result = $this->return_info->getReturnById($return_id);
    $this->assertEquals(0, count($result));

    //TestCase when return_id is empty string
    $return_id = '';
    $result = $this->return_info->getReturnById($return_id);
     $this->assertEquals(0, count($result));

    //TestCase when return_id is not exist in db
    $return_id = 234;
    $result = $this->return_info->getReturnById($return_id);
     $this->assertEquals(0, count($result));

    //TestCase when return_id is exist in db with count 1
    $return_id      = 61051;
    $result = $this->return_info->getReturnById($return_id);
    $this->assertGreaterThan(0, count($result));
    $this->assertEquals(61051, $result[0]['return_id']);
    $this->assertEquals(7024, $result[0]['master_return_id']);
    $this->assertEquals(117, $result[0]['return_action_id']);
  }

  /**
   * Public Function to test getReturnReason()
   * @author: Nishu, Jan 2018
  */
  public function testGetReturnReason(){
    //Setting Db object for testDB
    $this->return_info->setDb($this->getDb());

    //TestCase to check when return_reason_id is not passed
    $result = $this->return_info->getReturnReason();
    $this->assertEquals(15, count($result));

    //TestCase when return_reason_id is 0
    $return_reason_id = 0;
    $result = $this->return_info->getReturnReason($return_reason_id);
    $this->assertEquals(15, count($result));

    //TestCase when return_reason_id is empty string
    $return_reason_id = '';
    $result = $this->return_info->getReturnReason($return_reason_id);
     $this->assertEquals(15, count($result));

    //TestCase when return_reason_id is not exist in db
    $return_reason_id = 234;
    $result = $this->return_info->getReturnReason($return_reason_id);
     $this->assertEquals(0, count($result));

    //TestCase when return_id is exist in db with count 1
    $return_reason_id  = 4;
    $result = $this->return_info->getReturnReason($return_reason_id);
    $this->assertEquals(1, count($result));
    $this->assertEquals(4, $result[4]['return_reason_id']);
    $this->assertEquals('Wrong Item Received', $result[4]['name']);
    $this->assertEquals('RETURN', $result[4]['reason_type']);
  }

  /**
     * Public Function to test getReturnAction()
     * @author: Nishu, Jan 2018
  */
  public function testGetReturnAction(){
    //Setting Db object for testDB
    $this->return_info->setDb($this->getDb());
    
    //TestCase to check when return_action_id is not passed
    $result = $this->return_info->getReturnAction();
    $this->assertGreaterThan(0, count($result));

    //TestCase when return_action_id is empty string
    $return_action_id = '';
    $result = $this->return_info->getReturnAction($return_action_id);
    $this->assertGreaterThan(0, count($result));

    //TestCase when return_action_id is not exist in db
    $return_action_id = 234;
    $result = $this->return_info->getReturnAction($return_action_id);
    $this->assertEquals(0, count($result));

    //TestCase when return_action_id is 1
    $return_action_id = 101;
    $result = $this->return_info->getReturnAction($return_action_id);
    $this->assertEquals(1, count($result));
    $this->assertEquals($return_action_id, $result[$return_action_id]['return_action_id']);
    $this->assertEquals('Pending', $result[$return_action_id]['name']);

    //TestCase when return_action_id is exist in db with count 1
    $return_action_id  = 104;
    $result = $this->return_info->getReturnAction($return_action_id);
    $this->assertEquals(1, count($result));
    $this->assertEquals($return_action_id, $result[$return_action_id]['return_action_id']);
  }

  /**
    * Public Function to test getReturnAction()
    * @author: Nishu, Jan 2018
  */
  public function testGetReturnActionNameById(){
    //Setting Db object for testDB
    $this->return_info->setDb($this->getDb());
    
    //TestCase to check when return_action_id is not passed
    $action_name = $this->return_info->getReturnActionNameById();
    $this->assertEquals('', $action_name);

    //TestCase when return_action_id is empty string
    $return_action_id = '';
    $action_name = $this->return_info->getReturnActionNameById($return_action_id);
    $this->assertEquals('', $action_name);

    //TestCase when return_action_id is not exist in db
    $return_action_id = 234;
    $action_name = $this->return_info->getReturnActionNameById($return_action_id);
    $this->assertEquals('', $action_name);

    //TestCase when return_action_id is 1
    $return_action_id = 101;
    $action_name = $this->return_info->getReturnActionNameById($return_action_id);
    $this->assertEquals('Pending', $action_name);

    //TestCase when return_action_id is exist in db with count 1
    $return_action_id  = 112;
    $action_name = $this->return_info->getReturnActionNameById($return_action_id);
    $this->assertEquals('Goods Picked Up', $action_name);
  }

  /**
   * Public function to test getReverseShipments()
   * @author: Nishu, 14th 2018
  */
  public function testGetReverseShipments(){
    //Setting Db object for testDB
    $this->return_info->setDb($this->getDb());

    //TestCase when order_no is empty string
    $order_no = '';
    $data = $this->return_info->getReverseShipments($order_no);
    $this->assertEquals(0, count($data));
    
    //TestCase when return_action_id is 0
    $order_no = 0;
    $data = $this->return_info->getReverseShipments($order_no);
    $this->assertEquals(0, count($data));

    //TestCase when ReverseShipments are not exist for given order No in db
    $order_no = 234;
    $data = $this->return_info->getReverseShipments($order_no);
    $this->assertEquals(0, count($data));

    //TestCase when return_action_id is exist in db with count 1
    $order_no  = '20170817748';
    $data = $this->return_info->getReverseShipments($order_no);
    $this->assertGreaterThan(0, count($data));

  }


}//End of Class

?>