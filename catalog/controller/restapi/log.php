<?php

/**
 *
 */
class ControllerRestapiLog extends Controller
{
    /**
     * @deprecated
     * Track browse more and product detail page seen
     */

    public function track_product_seen(){
        // We have deprecated this API - nothing to be done 
        // as other tools like Firebase etc are doing tracking now.
        // So, just returning a success message without doing anything.
        $rt = array();
        $rt['success_code'] = '1001';
        $rt['status'] = '1';
        $rt['status_text'] = 'Success - Deprecated API (Nothing done)';
        echo json_encode($rt); exit;
    }

    /**
     * @deprecated
     * track_sharing
     * Request Parameters : user_id,access_token,data
     * Type : Post
     * Output : {{"status":"1","status_text":"Success"}}
     **/
    public function track_sharing() {
        // We have deprecated this API - nothing to be done 
        // as other tools like Firebase etc are doing tracking now.
        // So, just returning a success message without doing anything.
        $rt = array();
        $rt['success_code'] = '1001';
        $rt['status'] = '1';
        $rt['status_text'] = 'Success - Deprecated API (Nothing done)';
        echo json_encode($rt); exit;
    }
}
