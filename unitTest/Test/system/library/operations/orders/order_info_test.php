 <?php
/**
 * 	OrderInfoTest
 * Class for testing Order Info Methods.
 * @author @Nishu, Aug 2018
 */
require_once('/var/www/html/wholesalebox/config.php');
require_once(DIR_SYSTEM . 'library/config.php');
require_once(DIR_UNITTEST.'unit_test_base_helper.php');
require_once(DIR_SYSTEM.'library/operations/orders/order_info.php');

class OrderInfoTest extends UnitTestBaseHelper
{	
	private $order_info = null;
	public function setUp(){
		//Creating order_info object
		$this->order_info = new OrderInfo();
	}

	/**
    * Public Function to test getAllCNByOrderId
    * @author: Nishu, Jan 2018
	*/
	public function testGetAllOrdersByCustomerId(){
        $order_info = $this->order_info;

        //Setting Db object for testDB
        $db = $this->getDb();

        //Test Case for not passing parameter
        $data = array();
        $result = $order_info->getAllOrdersByCustomerId($db, $data);
        $this->assertEquals(0, count($result));

        //Test Case for not passing parameter
        $data = array();
        $data['customer_id']  = 0;
        $result = $order_info->getAllOrdersByCustomerId($db, $data);
        $this->assertEquals(0, count($result));

        //Test Case for customer_id not existing in oc_order table
        $data = array();
        $data['customer_id']  = -1;
        $result = $order_info->getAllOrdersByCustomerId($db, $data);
        $this->assertEquals(0, count($result));

        //Test Case for customer_id existing in oc_order table
        $data = array();
        $data['customer_id']  = 80317;
        $result = $order_info->getAllOrdersByCustomerId($db, $data);
        $this->assertEquals(1, count($result));
	}

	
}//End of Class

?>