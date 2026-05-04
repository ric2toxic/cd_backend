 <?php
/**
 * 	ReturnActionBase
 *  TestCases for Main Class ReturnActionBase
 * 	@author @Nishu, Jan 2018
 */
require_once('/var/www/html/wholesalebox/config.php');
require_once(DIR_UNITTEST.'unit_test_base_helper.php');
require_once(DIR_SYSTEM.'library/operations/returns/return_action_base.php');

class ReturnActionBaseTest extends UnitTestBaseHelper
{	
	private $return_action_base = null;
	public function setUp(){
		//SetUp return_action_base object
		$this->return_action_base = new ReturnActionBase();
	}

	/**
     * Public Function to check available post return actions
     * @author: Nishu, Jan 2018
	*/
	public function testGetAvailablePostAction(){
		//Setting Db object for testDB
	    $this->return_action_base->setDb($this->getDb());
	    $return_action_id = '';

	    //TestCase to check available postRetunrActions when empty array is passed as param
	    $result = $this->return_action_base->getAvailablePostAction($return_action_id);
		$this->assertEquals(0, $result);

	    //TestCase to check available postRetunrActions when return_action_id is 0
		$return_action_id = 1;
		$result = $this->return_action_base->getAvailablePostAction($return_action_id);
		$this->assertEquals(0, $result);

		//TestCase to check available postRetunrActions when return_action_id is blank
		$return_action_id = -1;
		$result = $this->return_action_base->getAvailablePostAction($return_action_id);
		$this->assertEquals(0, $result);

		//TestCase to check available postRetunrActions
		$return_action_id = 101;
		$result = $this->return_action_base->getAvailablePostAction($return_action_id);
		$result = explode(',', $result);
		$this->assertEquals(6, count($result));
	}

	/**
     * Public Function to check prepare prefixes
     * @author: Nishu, Jan 2018
	*/
	public function testGetReturnPrefix(){
		//Setting Db object for testDB
	    $this->return_action_base->setDb($this->getDb());

	    //TestCase to check available ReturnPrefix
	    $return_prefix = $this->return_action_base->getReturnPrefix();
	    $financial_year = '';
		if ( (int)(date('m')) <= 3 ) {
			$financial_year = date('y', strtotime('-1 years'));
		} else {
			$financial_year = date('y');
		}
		$this->assertEquals('WSB-RTN-'.$financial_year.'-', $return_prefix);
	}

	/**
     * Public Function to check addMasterReturn() 
     * @author: Nishu, Jan 2018
	*/
	public function testAddMasterReturn(){
		//Setting Db object for testDB
	    $this->return_action_base->setDb($this->getDb());
	    //Initialize User object
	    $this->initializeUser(38);
	    //Setting User object for testUser
	    $this->return_action_base->setUser($this->getUser());
	    //Initialize $data array
	    $data = array();

	    //TestCase to check available postRetunrActions when empty array is passed as param
	    $master_return_id = $this->return_action_base->addMasterReturn($data);
		$this->assertEquals(0, $master_return_id);

	    //TestCase to check when order_id is 0
		$data['order_id'] = 0;
		$data['source'] = 'back_end';
		$data['customer_id'] = 0;
		$data['shipping_method'] = 'wsb_pickup';
		$master_return_id = $this->return_action_base->addMasterReturn($data);
		$this->assertEquals(0, $master_return_id);

		//TestCase to check when order_id is empty string
		$data['order_id'] = 0;
		$data['source'] = 'back_end';
		$data['customer_id'] = 0;
		$data['shipping_method'] = 'wsb_pickup';
		$master_return_id = $this->return_action_base->addMasterReturn($data);
		$this->assertEquals(0, $master_return_id);

		//TestCase to add master return with complete data
		$data['order_id'] = 123;
		$data['source'] = 'back_end';
		$data['customer_id'] = 0;
		$data['shipping_method'] = 'wsb_pickup';
		$master_return_id = $this->return_action_base->addMasterReturn($data);
		$this->assertGreaterThan(0, $master_return_id);
		$sql = "DELETE FROM oc_master_return WHERE master_return_id = ". (int)$master_return_id;
		$this->db->query($sql);
	}

	/**
     * Public Function to check returnReasonsForOrderProduct() 
     * @author: Nishu, Feb 2018
	*/
	public function testReturnReasonsForOrderProduct(){
		//Setting Db object for testDB
	    $this->return_action_base->setDb($this->getDb());

	    //Initialize $data array
	    $data = array();

	    //TestCase to Return reasons for order product on order status bases, When passed empty array
	    $returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(0, count($returns));

	    /*TestCase to Return reasons for order product on order status bases, 
	      When passed order_id, suborder_id, suborder_status and buyer_invoice_id is 0
	    */
		$data['order_id']         = 0;
		$data['suborder_id']      = 0;
		$data['suborder_status']  = 0;
		$data['buyer_invoice_id'] = 0;
		$data['order_product_id'] = 0;
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(0, count($returns));

		/*TestCase to Return reasons for order product on order status bases, 
	      When passed suborder_status is pending
	    */
		$data['order_id']         = 27726;
		$data['suborder_id']      = '20171204761-DL';
		$data['suborder_status']  = 1;
		$data['buyer_invoice_id'] = 38544;
		$data['order_product_id'] = 290471;
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(0, count($returns));

		/*TestCase to Return reasons for order product on order status bases, 
	      When passed suborder_status is Canceled
	    */
		$data['order_id']          = 27726;
		$data['suborder_id']       = '20171204761-DL';
		$data['suborder_status']   = 8;
		$data['buyer_invoice_id']  = 38544;
		$data['seller_invoice_id'] = 38544;
		$data['order_product_id']  = 290471;
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(1, count($returns));

		/*TestCase to Return reasons for order product on order status bases, 
	      When passed suborder_status is Complete
	    */
		$data['order_id']         = 27726;
		$data['suborder_id']      = '20171204761-DL';
		$data['suborder_status']  = 5;
		$data['buyer_invoice_id'] = 38544;
		$data['order_product_id'] = 290471;
		$data['admin_mode']       = 'on';
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(2, count($returns['quality']));
		$this->assertEquals(2, count($returns['replacement']));

		/*TestCase to Return reasons for order product on order status bases, 
	      When passed suborder_status is Client Dispute
	    */
		$data['order_id']         = 27726;
		$data['suborder_id']      = '20171204761-DL';
		$data['suborder_status']  = 6;
		$data['buyer_invoice_id'] = 38544;
		$data['order_product_id'] = 290471;
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(0, count($returns));

		/*TestCase to Return reasons for order product on order status bases, 
	      When passed suborder_status is Failed
	    */
		$data['order_id']         = 27726;
		$data['suborder_id']      = '20171204761-DL';
		$data['suborder_status']  = 8;
		$data['buyer_invoice_id'] = 38544;
		$data['order_product_id'] = 290471;
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(1, count($returns));

		/*TestCase to Return reasons for order product on order status bases, 
	      When passed suborder_status is Processed, pickup status is received
	    */
		$data['order_id']         = 27726;
		$data['suborder_id']      = '20171204761-DL';
		$data['suborder_status']  = 9;
		$data['buyer_invoice_id'] = 38544;
		$data['order_product_id'] = 290471;
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(0, count($returns));

		/*TestCase to Return reasons for order product on order status bases, 
	      When passed suborder_status is Parcel Lost
	    */
		$data['order_id']         = 27726;
		$data['suborder_id']      = '20171204761-DL';
		$data['suborder_status']  = 11;
		$data['buyer_invoice_id'] = 38544;
		$data['order_product_id'] = 290471;
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(1, count($returns));


		/*TestCase to Return reasons for order product on order status bases, 
	      When passed suborder_status is Reversed
	    */
		$data['order_id']         = 27726;
		$data['suborder_id']      = '20171204761-DL';
		$data['suborder_status']  = 12;
		$data['buyer_invoice_id'] = 38544;
		$data['order_product_id'] = 290471;
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(0, count($returns));


		/*TestCase to Return reasons for order product on order status bases, 
	      When passed suborder_status is Shipped
	    */
		$data['order_id']         = 27726;
		$data['suborder_id']      = '20171204761-DL';
		$data['suborder_status']  = 13;
		$data['buyer_invoice_id'] = 38544;
		$data['order_product_id'] = 290471;
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(0, count($returns));


		/*TestCase to Return reasons for order product on order status bases, 
	      When passed suborder_status is Shipped with tracking
	    */
		$data['order_id']         = 27726;
		$data['suborder_id']      = '20171204761-DL';
		$data['suborder_status']  = 14;
		$data['buyer_invoice_id'] = 38544;
		$data['order_product_id'] = 290471;
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(0, count($returns));

		/*TestCase to Return reasons for order product on order status bases, 
	      When passed suborder_status is Delivered
	    */
		$data['order_id']         = 27726;
		$data['suborder_id']      = '20171204761-DL';
		$data['suborder_status']  = 15;
		$data['buyer_invoice_id'] = 38544;
		$data['order_product_id'] = 290471;
		$data['admin_mode']       = 'on';
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(2, count($returns));
		$this->assertEquals(2, count($returns));

		/*TestCase to Return reasons for order product on order status bases, 
	      When passed suborder_status is Tentative Processed, pickup status is received
	    */
		$data['order_id']         = 27726;
		$data['suborder_id']      = '20171204761-DL';
		$data['suborder_status']  = 16;
		$data['buyer_invoice_id'] = 38544;
		$data['order_product_id'] = 290471;
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(0, count($returns));

		/*TestCase to Return reasons for order product on order status bases, 
	      When passed suborder_status is Tentative Delivery Issues
	    */
		$data['order_id']         = 27726;
		$data['suborder_id']      = '20171204761-DL';
		$data['suborder_status']  = 17;
		$data['buyer_invoice_id'] = 38544;
		$data['order_product_id'] = 290471;
		$returns = $this->return_action_base->returnReasonsForOrderProduct($data);
		$this->assertEquals(0, count($returns));
	}

	
	

}//End of Class
