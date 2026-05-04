<?php
/**
 * CourierFactory
 *
 * @info - main courier factory class to return courier class object.
 *
 * @author @MSA (Dec 2017)
 */
class CourierFactory 
{
    public static function build(string $type, array $param)
    {
        $courier = "Courier" . ucfirst($type);
        
        if( class_exists( $courier ) )
        { 
            return new $courier( $param ) ;
            
        }else{
           
            return ['error' => 'Courier class not found('.$courier.')'];
            
        }
    }
}
