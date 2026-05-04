<?php

    require_once('system.php');

    class CustomerController extends SystemController
    {

        public function __construct($params) {

            parent::__construct($params);
        }

        /**
         * customer login
         */
        public function login() {

            // example get querystring  / db object
            if ($this->method == 'GET') {
                return "Your name is " . $this->args[0]. "  - DB object : ". is_object($this->db);
            } else {
                return "Only accepts GET requests";
            }
        }


        public function define_constant()
        {
            return INTERNATIONAL_STORE_ID; // example to access constant
        }

        public function register()
        {
            return $this->request; // example  - access post data
        }

        private function __response(){
           // response function
        }

    }