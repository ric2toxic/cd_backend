<?php
/**
* Dynamic Module for app
* @author   GARVIT
*/
class Module {
   
    public  $module_array = array('module_1' => 'ImageSlideShow', 'module_2' => 'Filter Product');
    public 	$registry;
	private $load;
	private $db;

	public function __construct($registry) {
		$this->registry 	= $registry;
		$this->db 			= $registry->db;
		$this->load 		= $registry->load;
	}

   /**
    * module_1
    * In module_1 offers images or category images are show. 
    * @author   GARVIT
    */
    public function module_1($modules){
        $rt = array();
        if( isset($modules) && !empty($modules) ){
            foreach($modules as $module){

                $sort_order = $module['sort_order'];
                if(isset($module['image'])){
                    if( strpos($module['image'], STATIC_CONTENT_URL) !== false ) {
                        $image = $module['image'];
                    } else {
                        $image = STATIC_CONTENT_URL.$module['image'];
                    }
                }else{
                    $image = "";
                }
                
                $rt[$sort_order]['image']              = $image;
                $rt[$sort_order]['is_show_image']      = isset($module['is_show_image'])?$module['is_show_image']:1;
                $rt[$sort_order]['title']              = isset($module['title'])?$module['title']:"";
                $rt[$sort_order]['is_show_title']      = isset($module['is_show_title'])?$module['is_show_title']:1;
                $rt[$sort_order]['subtitle']           = isset($module['subtitle'])?$module['subtitle']:"";
                $rt[$sort_order]['is_show_subtitle']   = isset($module['is_show_subtitle'])?$module['is_show_subtitle']:1;
                $rt[$sort_order]['image_height']       = isset($module['image_height'])?$module['image_height']:"";
                $rt[$sort_order]['image_width']        = isset($module['image_width'])?$module['image_width']:"";
                $rt[$sort_order]['module_width']       = isset($module['module_width'])?$module['module_width']:"";
                $rt[$sort_order]['headline']           = isset($module['headline'])?$module['headline']:"";
                $rt[$sort_order]['category_id']        = isset($module['category_id'])?$module['category_id']:"";
                $rt[$sort_order]['category_name']      = isset($module['category_name'])?$module['category_name']:"";
                $rt[$sort_order]['search_url']         = isset($module['search_url'])?$module['search_url']:'';
                $rt[$sort_order]['search_filters']     = isset($module['search_url'])?$this->_searchUrl($module['search_url']):(object)array();
                $rt[$sort_order]['sort_order']         = isset($module['sort_order'])?$module['sort_order']:1;
            }
        }
        // echo "<pre>"; print_r($rt); die;
       return $rt;
    }

    /**
    * _searchUrl
    * get filters according to serach url.
    * @param    $search_url     STRING 
    * @author   GARVIT
    */ 
    private function _searchUrl($search_url){
        
        $url = parse_url($search_url); 
        
        if (isset($url['path'])) {
            $path = explode('/', $url['path']);
            $new_path = array_pop($path);
            $cat  = $this->db->query("SELECT query FROM ".DB_PREFIX."url_alias WHERE keyword='".$new_path ."'" );
            if ($cat->num_rows) {
                if (explode('=', $cat->row['query'])[0] == 'category_id') {
                    $data['category_id'] = explode('=', $cat->row['query'])[1];
                } elseif(explode('=', $cat->row['query'])[0] == 'product_id'){
                    $data['product_id'] = explode('=', $cat->row['query'])[1];
                }
            }
        }
        
        if (isset($url['query'])) {
            $query_arr = explode('&amp;', $url['query']);        
        } elseif (isset($url['fragment'])) {
            $query_arr = explode('&amp;', substr($url['fragment'], 1));    
            $set_filter = true;            
        }
        
        $inner_query_arr = array();
        $i = 0;
        
        if(!empty($query_arr) && isset($query_arr)){
            foreach ($query_arr as $value) {
                $arr = explode('=', $value);
                if ($arr[0] == 'filter') {
                    $inner_query_arr['filter'] =$arr[1] ;
                }
                if ($arr[0] == 'rating_filter') {
                    switch ($arr[1]) {
                        case 1:
                            if (isset($inner_query_arr['filter'])) {
                            $inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20001";
                            }else { 
                                $inner_query_arr['filter_options'] = "20001";
                            }

                        break;
                        case 2:
                            if (isset($inner_query_arr['filter'])) {
                                $inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20002";
                            }  else {
                                $inner_query_arr['filter_options'] =  "20002";
                            }              
                        break;
                        case 3:
                            if (isset($inner_query_arr['filter'])) {
                                $inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20003";
                            } else {
                                $inner_query_arr['filter_options'] = "20003";
                            }                
                        break;
                        case 4:
                            if (isset($inner_query_arr['filter'])) {
                                $inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20004";
                            } else {
                                $inner_query_arr['filter_options'] =  "20004";
                            }               
                        break;
                        case 5:
                            if (isset($inner_query_arr['filter'])) {
                                $inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20005";
                            } else {
                                $inner_query_arr['filter_options'] = "20005";
                            }                
                        break; 
                        default:
                            if (isset($inner_query_arr['filter'])) {
                                $inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20000";
                            } else {
                                $inner_query_arr['filter_options'] =  "20000";
                            }                
                        break;
                    }   
                }if ($arr[0] == 'sort') {
                    $inner_temp = $arr[1];
                }
                if ($arr[0] == 'order') {
                    if ($arr[1] == 'DESC') {
                        if (isset($inner_temp)) {
                            if ($inner_temp == 'p.date_added') {
                                $inner_query_arr['sort_options'] = 'latest_designs';
                            } elseif ($inner_temp == 'p.selling_price') {
                                $inner_query_arr['sort_options'] = 'price_high_to_low';
                            } 
                        }
                    } else {
                        if (isset($inner_temp)) {
                            if ($inner_temp == 'p.selling_price') {
                                $inner_query_arr['sort_options'] = 'price_low_to_high';
                            } 
                        }
                    }
                } if ($arr[0] == 'search') {
                    $inner_query_arr['search_term'] = $arr[1];
                } if ($arr[0] == 'category_id') {
                    $data['category_id'] = $arr[1];
                } if ($arr[0] == 'price_filter') {
                    $data['price_filter'] = $arr[1];
                }

                $i++;
            }
        }
        return (object)$inner_query_arr;
    }

    public function module_2(){

    }
}