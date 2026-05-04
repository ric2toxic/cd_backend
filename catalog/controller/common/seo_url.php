<?php
class ControllerCommonSeoUrl extends Controller {
    public function index() {
        // Add rewrite to url class
        if ($this->config->get('config_seo_url')) {
            $this->url->addRewrite($this);
        }
                
                //default unset custom url sessions
                if(isset($this->session->data['is_custom'])) unset($this->session->data['is_custom']);
                if(isset($this->session->data['url_alias_id'])) unset($this->session->data['url_alias_id']);
                if(isset($this->session->data['is_filter'])) unset($this->session->data['is_filter']);
                if(isset($this->session->data['is_search'])) unset($this->session->data['is_search']);
                if(isset($this->session->data['is_search_with_filter'])) unset($this->session->data['is_search_with_filter']);
                
        //echo $this->request->get['_route_'];
        //exit;        
        // Decode URL
        if (isset($this->request->get['_route_'])) {
            $parts = explode('/', $this->request->get['_route_']);

            // remove any empty arrays from trailing
            if (utf8_strlen(end($parts)) == 0) {
                array_pop($parts);
            }

            
            //custom redirect
            $static_redirect = $this->url->staticRedirect($this->request->get['_route_']);

            if(isset($static_redirect['redirect_type']) && $static_redirect['redirect_type'] == '404')
            {
              header("HTTP/1.1 404 Not Found");
              header("Location: ".$static_redirect['redirect_url']);
              exit();
            }

            if(isset($static_redirect['redirect_type']) && $static_redirect['redirect_type'] == '301')
            {
              header("HTTP/1.1 301 Moved Permanently");
              header("Location: /".$static_redirect['redirect_url']);
              exit();
            }  

                        //check custom url
                        $sql = "SELECT url_alias_id, query, keyword, url_type, store_id, search_id, is_custom, is_redirect_301 FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($this->request->get['_route_']) . "' AND is_custom = 1";
                        //echo $sql; die;
                        $query = $this->db->query($sql);
                        if($query->num_rows == 1){
                            //set session for meta_title and meta_decription
                            $this->session->data['is_custom'] = $query->row['is_custom'];
                            $this->session->data['url_alias_id'] = $query->row['url_alias_id'];
                            //echo 'custom';
                            //echo '<br/>';
                            
                            $url_type  = $query->row['url_type'];
                            $id = $query->row['search_id'];
                            
                            
                            
                            //set session for filter url
                            //if url have 'category&search' and '#!filter'
                            if(strpos($query->row['query'], "category&amp;search") && strpos($query->row['query'], '#!filter')){
                                //echo 'sdss'; die; 
                                $this->session->data['is_search_with_filter'] = 1;
                                unset($this->session->data['is_filter']); 
                                unset($this->session->data['is_search']); 
                            }
                            elseif (strpos($query->row['query'], '#!filter') !== false) { 
                               $this->session->data['is_filter'] = 1;
                               unset($this->session->data['is_search']);
                               unset($this->session->data['is_search_with_filter']);
                                
                            }elseif(strpos($query->row['query'], 'search') !== false) { 
                                $this->session->data['is_search'] = 1;
                                unset($this->session->data['is_filter']); 
                                unset($this->session->data['is_search_with_filter']); 
                            }
                            
                            if (isset($this->session->data['is_filter']) ) {
                                if ($url_type == 'category') { 
                                    if (!isset($this->request->get['path'])) {  
                                        $this->request->get['path'] = $id;
                                    } else { 
                                        $this->request->get['path'] .= '_' . $id; 
                                    }
                                }
                                $this->request->get['route'] = "product/category"; 

                            }elseif (isset($this->session->data['is_search']) || isset($this->session->data['is_search_with_filter'])) {
                                $this->request->get['route'] = "product/search"; 
                            }else{
                                //echo 'no filter'; 
                                if ($url_type == 'product') { 
                                    $this->request->get['product_id'] = $id;
                                    //$this->request->get['route'] = "product/product";
                                }
                                if ($url_type == 'category') {
                                    if (!isset($this->request->get['path'])) {
                                        $this->request->get['path'] = $id;
                                    } else {
                                        $this->request->get['path'] .= '_' . $id;
                                    }
                                    //$this->request->get['route'] = "product/search";
                                }
                                if ($url_type == 'manufacturer') {
                                    $this->request->get['manufacturer_id'] = $id;
                                }
                                if ($url_type == 'seller') {
                                    $this->request->get['seller_id'] = $id;
                                }
                                if ($url_type == 'information') {
                                    $this->request->get['information_id'] = $id;
                                }
                                
                                if($url_type == 'multiple_query_string') {
                                  $raw_url = explode('&amp;', $query->row['query']);
                                  foreach ($raw_url as $key => $value) {
                                    $url = explode('=', $value);
                                    if ($url[0] == 'category_id') {
                                        if (!isset($this->request->get['path'])) {
                                            $this->request->get['path'] = $url[1];
                                        } else {
                                            $this->request->get['path'] .= '_' . $url[1];
                                        }
                                    }
                                    if ($url[0] == 'location') {
                                      $this->request->get['location'] = $url[1];
                                    }
                                  }
                                }
                                if ($query->row['query'] && $url_type != 'information' && $url_type != 'manufacturer' && $url_type != 'category' && $url_type != 'product' && $url_type != 'seller' && $url_type != 'multiple_query_string') {
                                    $this->request->get['route'] = $query->row['query'];
                                }
                            }
                        }
                        else{ 
                            
                            //first check is_redirect_301
                            $tem_query = rtrim($this->request->get['_route_'], '/');
                            $sql = "SELECT url_alias_id, query, keyword, url_type, store_id, search_id, is_custom, is_redirect_301 FROM " . DB_PREFIX . "url_alias WHERE query = '" . $this->db->escape($tem_query) . "' AND is_custom = 1 AND is_redirect_301 = 1 LIMIT 1";
                            $query = $this->db->query($sql);
                            if($query->num_rows > 0){                                
                                $redirect_url = HTTPS_SERVER;
                                if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID){
                                    $redirect_url = HTTPS_SERVER_INTERNATIONAL;
                                } 
                                header("HTTP/1.1 301 Moved Permanently");
                                header("Location: ".$redirect_url.$query->row['keyword']);
                                exit();
                            }
                            
                            //unset custom url session
                            if(isset($this->session->data['is_custom'])) unset($this->session->data['is_custom']);
                            if(isset($this->session->data['url_alias_id'])) unset($this->session->data['url_alias_id']);
                            if(isset($this->session->data['is_filter'])) unset($this->session->data['is_filter']);
                            if(isset($this->session->data['is_search'])) unset($this->session->data['is_search']);
                            if(isset($this->session->data['is_search_with_filter'])) unset($this->session->data['is_search_with_filter']);
                            
                            foreach ($parts as $part) {
                             if($part != 'p' && $part != 'i')
                             {
                               $sql = "SELECT url_alias_id, query, keyword, url_type, store_id, search_id, is_custom, is_redirect_301 FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($part) . "' AND ( store_id = '" . (int)$this->config->get('config_store_id') . "' OR store_id IS NULL )";
                               //echo $sql; //die;
                               $query = $this->db->query($sql);
                               if ($part == $this->config->get('msconf_sellers_slug') || $part == 'products') {
                                                       continue;
                                               } else {
                                   if ($query->num_rows) {

                                         $url = explode('=', $query->row['query']);

                                         if ($url[0] == 'product_id') 
                                         {
                                             $this->request->get['product_id'] = $url[1];
                                             if($parts[0] != 'p')
                                             {
                                               header("HTTP/1.1 301 Moved Permanently");
                                               header("Location: ".HTTPS_SERVER."p/".$parts[0]);
                                                exit();
                                             }
                                         }

                                         if ($url[0] == 'category_id') {
                                             if (!isset($this->request->get['path'])) {
                                                 $this->request->get['path'] = $url[1];
                                             } else {
                                                 $this->request->get['path'] .= '_' . $url[1];
                                             }
                                         }

                                         if ($url[0] == 'manufacturer_id') {
                                             $this->request->get['manufacturer_id'] = $url[1];
                                         }

                                         if ($url[0] == 'seller_id') {
                                                                     $this->request->get['seller_id'] = $url[1];
                                                             }

                                         if ($url[0] == 'information_id') 
                                         {
                                             $this->request->get['information_id'] = $url[1];
                                             if($parts[0] != 'i')
                                             {
                                               header("HTTP/1.1 301 Moved Permanently");
                                               header("Location: ".HTTPS_SERVER."i/".$parts[0]);
                                                exit();
                                             }
                                         }

                                         if ($query->row['query'] && $url[0] != 'information_id' && $url[0] != 'manufacturer_id' && $url[0] != 'category_id' && $url[0] != 'product_id' && $url[0] != 'seller_id' && $url[0] != 'location') {
                                             $this->request->get['route'] = $query->row['query'];
                                         }
                                   } elseif (isset($parts[1]) && $parts[1] == 'cart') {
                                           $this->request->get['route'] = 'checkout/cart';
                                   }  elseif ( isset($parts[1]) && $parts[1] == 'checkout') {
                                           $this->request->get['route'] = 'checkout/checkout';
                                   } elseif ( isset($parts[1]) && $parts[1] == 'one_page_checkout') {
                                           $this->request->get['route'] = 'checkout/one_page_checkout';
                                   }
                                    else {
                                       $this->request->get['route'] = 'error/not_found';

                                       break;
                                   }
                               }
                           }
                         }   //die;
                }
            if (!isset($this->request->get['route'])) {
                if (isset($this->request->get['product_id'])) {
                    $this->request->get['route'] = 'product/product';
                } elseif (isset($this->request->get['path']) || isset($this->request->get['location'])) {
                    $this->request->get['route'] = 'product/category';
                } elseif (isset($this->request->get['manufacturer_id'])) {
                    $this->request->get['route'] = 'product/manufacturer/info';
                } elseif (isset($this->request->get['seller_id'])) {
                    if (strpos($this->request->get['_route_'], "products") !== FALSE) {
                        $this->request->get['route'] = 'seller/catalog-seller/products';
                    }
                    else {
                        $this->request->get['route'] = 'seller/catalog-seller/profile';
                    }
                } elseif (strpos($this->request->get['_route_'], $this->config->get('msconf_sellers_slug')) === 0) {
                    $this->request->get['route'] = 'seller/catalog-seller';
                } elseif (isset($this->request->get['information_id'])) {
                    $this->request->get['route'] = 'information/information';
                }
            }
            
            if (isset($this->request->get['route'])) {

                // Due to react
                if(CONFIG_IS_MOBILE == 0) {
                    if ($this->request->get['route'] == "product/product") {
                        $this->request->get['route'] = "react/product";
                    }

                    if ($this->request->get['route'] == "product/category" || $this->request->get['route'] == "product/search") {
                        $this->request->get['route'] = "react/list";
                    }
                }
                
                return new Action($this->request->get['route']);
            }
        }
    }

    public function rewrite($link) {
        $url_info = parse_url(str_replace('&amp;', '&', $link));

        $url = '';

        $data = array();

        parse_str($url_info['query'], $data);

        foreach ($data as $key => $value) {
            
            if ($data['route'] == 'seller/catalog-seller') {
                $url .= '/' . $this->config->get('msconf_sellers_slug') . '/';
            }
                
            if (isset($data['route'])) {
                if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id') || ($data['route'] == 'seller/catalog-seller/profile' && $key == 'seller_id') || ($data['route'] == 'seller/catalog-seller/products' && $key == 'seller_id')) {
                    $query = $this->db->query("SELECT url_alias_id, query, keyword, url_type, store_id, search_id, is_custom, is_redirect_301 FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");

                    if ($query->num_rows && $query->row['keyword']) {
                        
                        if ($data['route'] == 'seller/catalog-seller/profile') {
                            $url .= '/' . $this->config->get('msconf_sellers_slug') . '/' . $query->row['keyword'];
                        } else if ($data['route'] == 'seller/catalog-seller/products') {
                            $url .= '/' . $this->config->get('msconf_sellers_slug') . '/' . $query->row['keyword'] . '/products/';
                        } else {
                            $url .= '/' . $query->row['keyword'];
                        }

                        unset($data[$key]);
                    }
                } elseif ($data['route'] == 'checkout/checkout') {
                    $url .= '/' . 'checkout';
                }elseif ($data['route'] == 'checkout/one_page_checkout') {
                    $url .= '/' . 'onepagecheckout';
                }elseif ($data['route'] == 'checkout/cart') {
                    $url .= '/' . 'cart';
                }

                elseif ($key == 'path') {
                    $categories = explode('_', $value);

                    foreach ($categories as $category) {
                        $query = $this->db->query("SELECT url_alias_id, query, keyword, url_type, store_id, search_id, is_custom, is_redirect_301 FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int)$category . "'");

                        if ($query->num_rows && $query->row['keyword']) {
                            $url .= '/' . $query->row['keyword'];
                        } else {
                            $url = '';

                            break;
                        }
                    }

                    unset($data[$key]);
                }elseif($key == 'static'){

                    $query = $this->db->query("SELECT url_alias_id, query, keyword, url_type, store_id, search_id, is_custom, is_redirect_301 FROM " . DB_PREFIX . "url_alias WHERE `query` = 'static=" . $this->db->escape($value) . "'");

                    if ($query->num_rows && $query->row['keyword']) {
                        $url .= '/' . $query->row['keyword'];
                    } else {
                        $url = '';

                        break;
                    }
                    unset($data[$key]);
                }
            }
        }

        if ($url) {
            unset($data['route']);

            $query = '';

            if ($data) {
                foreach ($data as $key => $value) {
                    $query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((string)$value);
                }

                if ($query) {
                    $query = '?' . str_replace('&', '&amp;', trim($query, '&'));
                }
            }

            return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
        } else {
            $link = str_replace('index.php?route=common/home', '', $link);
            return $link;


        }
    }
}
