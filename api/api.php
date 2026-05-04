<?php

    require_once 'rest.php';

    class WsbApi extends API
    {
        public function __construct($request, $origin) {
            parent::__construct($request);
        }

        /**
         * Ready to go
         */
        public function go() {

            try {
                echo $this->processAPI();
            } catch (Throwable $e) {
                $this->mailException($e);
                echo json_encode(array('error' => 'Internal server error'));
            }
        }
    }

    $initObj = new WsbApi($_REQUEST['request'], '');
    require_once($initObj->folder.$initObj->endpoint.".php");
    $initObj->go();
