<?php 
/**
 * @Class: Profiling
 * @Purpose: To record the response time of a particular request
 * @author: Devendra Dhayal Sep 2019
 */
class Profiling {
    private $_start_time_in_ms = null;
    private $_end_time_in_ms = null;
    private $_db = null;

    public function __construct(DataBase\DB $db) {
        $this->_db = $db;
    }

    /**
     * this method will record the start time
     */
    public function start() {
        $this->_start_time_in_ms = round(microtime(true) * 1000);
    }

    /**
     * this method will record the end time and return the time difference in milliseconds
     */
    public function end() {
        $this->_end_time_in_ms = round(microtime(true) * 1000);
        // return the time difference, if anyone wants to use it, they can
        return ($this->_end_time_in_ms - $this->_start_time_in_ms);
    }

    /**
     * This method will update the response time (end time minus start time) in database
     */
    public function updateProfilingDataInDB() {
        if(empty($this->_end_time_in_ms) || empty($this->_start_time_in_ms)) {
            return false;
        }

        $path = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $get_params = json_encode($_GET);
        $post_params = json_encode($_POST);
        $sql = "INSERT INTO 
                    " . DB_PREFIX . "wsb_route_profiles 
                SET 
                    path='" . $this->_db->escape($path) . "',
                    route='" . $this->_db->escape($_GET['route'] ?? '') . "',
                    get_params='" . $this->_db->escape($get_params) . "',
                    post_params='" . $this->_db->escape($post_params) . "',
                    response_time_in_ms='" . (int)($this->_end_time_in_ms - $this->_start_time_in_ms) . "'
                ";

        if($this->_db->query($sql)){
            return true;
        } else {
            return false;
        }
    }
}
