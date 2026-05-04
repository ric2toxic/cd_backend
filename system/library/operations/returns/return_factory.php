 <?php
/**
 * 	ReturnFactory
 *  ReturnFactory class to 
 * 	@author @Nishu Rani, Jan 2018
 */
require_once(DIR_SYSTEM.'/library/wsbregisterybase.php');
class ReturnFactory extends WSBRegisteryBase
{
    public static function build($class_name) {
        // assumes the use of an autoloader
        if (class_exists($class_name)) {
            return new $class_name();
        }
        else {
            throw new Exception("Invalid Class Name given.");
        }
    }

}//End of Class
