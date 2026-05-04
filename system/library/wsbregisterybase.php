<?php
require_once(DIR_SYSTEM . 'library/db/db.php');

class WSBRegisteryBase
{ 

  public $registry = null;
  public $load     = null;
  public $user     = null;
  public $db       = null;
  public $currency = null;
  public $config = null;
  

  public function __construct($registry = null) {

    if (isset($registry)) {
      if (method_exists($registry, 'get')) {
        $this->setRegistry($registry);
        $this->setDb($registry->get('db'));
        $this->setLoad($registry->get('load'));
        $this->setUser($registry->get('user'));
        $this->setCurrency($registry->get('currency'));
        $this->setConfig($registry->get('config'));
      } else {
        $this->setRegistry($registry);
        $this->setDb($registry->db);
        $this->setLoad($registry->load);
        $this->setUser($registry->user);
        $this->setCurrency($registry->currency);
        $this->setConfig($registry->config);
      }
    }
  }

  public function setRegistry($registry){
    $this->registry = $registry;
  }
  public function getRegistry(){
    return $this->registry;
  }

  public function setDb($db){
    $this->db = $db;
  }
  public function getDb(){
    return $this->db;
  }

  public function setLoad($load){
    $this->load = $load;
  }
  public function getLoad(){
    return $this->load;
  }

  public function setUser($user){
    $this->user = $user;
  }
  public function getUser(){
    return $this->user;
  }

  public function setCurrency($currency){
    $this->currency = $currency;
  }
  public function getCurrency(){
    return $this->currency;
  }


  public function setConfig($config){
    $this->config = $config;
  }
  public function getConfig(){
    return $this->config;
  }

}//End of Class

?>