 <?php
/**
 * 	CourierFactory
 *  TestCases for Courier Factory Classes
 * 	@author MSA, June 2018
 */
require_once('/var/www/html/wholesalebox/khufiya_vibhag/config.php');
require_once(DIR_UNITTEST.'unit_test_base_helper.php');
require_once(DIR_SYSTEM.'library/operations/courier_factory/courier_factory.php');
require_once(DIR_SYSTEM.'library/operations/courier_factory/courier_base.php');
require_once(DIR_SYSTEM.'library/operations/courier_factory/courier_fedex.php');

class CourierFactoryTest extends UnitTestBaseHelper
{	
	private $courier_factory = null;

	/**
	 * Public function to setup required class object
	 * @Author: MSA June 2018
	 * */
	public function setUp()
	{
		//$this->courier_factory = new CourierFactory();
	}

	/**
	 * Public static method to generate required courier class object
	 * @Param: String $courier_name
	 * @Param: Array $data
	 * @Return: Object Courier Company Class Object
	 * @Author: MSA June 2018
	 **/
	public function testBuild()
	{
		/*List data to pass in courier class for */
		$data = array();	
		
		/* TestCase - If empty courier company name passed as param */
		$courier_company = '';
		$courier_class = CourierFactory::build($courier_company, $data);
		$this->assertInternalType('array', $courier_class);
		
		
		/* TestCase - If 0 courier company name passed as param */
		$courier_company = 0;
		$courier_class = CourierFactory::build($courier_company, $data);
		$this->assertInternalType('array', $courier_class);
		$error = $courier_class['error'];
		$this->assertEquals('Courier class not found(Courier'.$courier_company.')',$error);
		
	
		/* TestCase - If in-valid courier company (i.e MyCourier) name passed as param */
		$courier_company = 'MyCourier';
		$courier_class = CourierFactory::build($courier_company, $data);
		$this->assertInternalType('array', $courier_class);
		$error = $courier_class['error'];
		$this->assertEquals('Courier class not found(Courier'.$courier_company.')',$error);
	
	
		/* TestCase - If valid courier company (i.e Fedex) name passed as param */
		$courier_company = 'Fedex';
		$this->assertInstanceOf('CourierFedex', CourierFactory::build($courier_company, $data));
			
	}
	
	/**
	 * Public function to close or remove class object or initialised with null value for garbage collection
	 * @Author: MSA June 2018
	 * */
	public function tearDown()
	{
		//$this->courier_factory = null;
	}


}//End of Class
