<?php

class ProductHotness {
  private $_hotness_value = array();
  // Database Object
  private $_db = null;

  public function __construct($db) {
    $this->_db = $db;
    $this->_hotness_value = array(
      'see-product-detail' => 10,
      'add-to-wishList' => 15,
      'remove-from-wishlist' => -15,
      'add-to-cart' => 10,
      'remove-from-cart' => -10,
      'place-order' => 10,
      'cancel-order' => -10,
      'return-request' => -50
    );
    //$event = 'addToCart';
  }

  public function updateHotness($event,$product_id){
    if(!isset($this->_hotness_value[$event])){
      throw new \Exception('Error: Could not load hotness value for '.$event.'.');
      return;
    }

    $hotness = (int)$this->_hotness_value[$event];
    $sql = "
                  INSERT IGNORE INTO ". DB_PREFIX ."product_hotness ( product_id, hotness_points )
                    VALUES ( '". (int)$product_id ."', '". $hotness ."' )
                      ON DUPLICATE KEY UPDATE
                      hotness_points = hotness_points + " . $hotness;

    return $this->_db->query( $sql );
  }

  public function deleteHotness($product_id){
    $sql = "DELETE FROM ".DB_PREFIX."product_hotness WHERE product_id = ".(int)$product_id;
    $this->_db->query($sql);
  }

  public function hasHotness($product_id,&$old_hotness){
    $sql = "SELECT hotness_points FROM ".DB_PREFIX."product_hotness WHERE product_id = ".(int)$product_id;
    $query = $this->_db->query($sql);
    if($query->num_rows){
      $old_hotness = $query->row['hotness_points'];
      return true;
    }
    
    return false;
  }

    /**
     * @param  string $start_date [description]
     * @param  string $end_date   [description]
     * @return array eligible product data
     * @author Anurag Jain, 08 Aug 2019
     */
    public function getUpdatedHotnessProductsByDate( string $start_date, string $end_date ): array
    {
        $sql = "
                SELECT 
                  product_id, 
                  hotness_points 
                FROM ". DB_PREFIX ."product_hotness 
                WHERE 
                  modified >= '". $this->_db->escape($start_date) . "'
                  AND modified <= '". $this->_db->escape($end_date) ."'";

        $result = $this->_db->query( $sql );
        return $result->rows;
    }

    /**
     * updates hotness data in solr
     * @param  array  $hotness_data array o product id and hotness points
     * @return bool
     * @author Anurag Jain, 08 Aug 2019
     */
    public function updateProductHotnessToSolr( array $hotness_data ): bool
    {
        $solr_obj = array(
                    'endpoint' => array(
                                    'localhost' => array(
                                        'host' => SOLR_HOST,
                                        'port' => SOLR_PORT,
                                        'path' => SOLR_PATH,
                                        'timeout' => 50000
                                )));

        $client = new Solarium\Client( $solr_obj );
        $update = $client->createUpdate();

        // prepare data
        foreach ( $hotness_data as $key => $data ) {
            $doc = $update->createDocument();
            $doc->setKey( 'id', $data['product_id'] );
            $doc->addField( 'hotness_value', $data['hotness_points'] );
            $doc->setFieldModifier( 'hotness_value', 'set' );
            $update->addDocument( $doc );
        }

        $update->addCommit();
        $result = $client->update( $update );
        return true;
    }

    /**
     * deletes Archived Product's Hotness entry
     * @return bool
     * @author Anurag Jain, 09th Aug 2019
     */
    public function deleteArchivedProductHotness(): bool
    {
        $sql = "
                DELETE ph 
                FROM ". DB_PREFIX ."product_hotness AS ph 
                JOIN ". DB_PREFIX ."product AS p 
                  ON p.product_id = ph.product_id 
                     AND p.is_archived = 1";

        return (bool)$this->_db->query( $sql );
    }
}

 ?>
