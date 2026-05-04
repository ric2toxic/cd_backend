<?php
class ControllerRestapiLanguage extends Controller{
   
    /** *******
     * Function : getLanguageList
     * Type : Get
     * Output : {"language":"language_data"}
     ******* */
    public function index () {
            
            $data = array();
            $data['language'] = VERNACULAR_LANGUAGE;
            echo json_encode($data); die;
    }

}
