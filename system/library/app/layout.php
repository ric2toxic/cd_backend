<?php
/**
* Dynamic Module Layout for app
* @author   GARVIT
*/
class Layout {

    public $layout_array = array('layout_1' => 'layout_1','layout_2' => 'layout_2');

   /**
    * layout_1
    * In layout_1 offers images or category images are show. 
    * @author   GARVIT
    */
    public function layout_1($layout){
        $rt = array();
        if( isset($layout) && !empty($layout) ){
            $rt['layout_id']                = 1;
            $rt['layout_title']             = isset($layout['layout_title'])?$layout['layout_title']:"";
            $rt['is_show_layout_title']     = isset($layout['is_show_layout_title'])?$layout['is_show_layout_title']:1;
            $rt['is_single_image']          = isset($layout['is_single_image'])?$layout['is_single_image']:0;
            $rt['status']                   = isset($layout['status'])?$layout['status']:1;
        }
       return $rt;
    }

    public function layout_2(){
        echo "wewq"; die;
    }

}