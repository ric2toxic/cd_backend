<?php
class ControllerReportAnalysis extends Controller {

    public function getProductWiseMargin() {
        $this->load->model('report/analysis');
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('report/analysis', $data);

        $this->document->setTitle($data['heading_title']);

        $data['token'] = $this->session->data['token'];

        $url = '';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('report/analysis', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );
        //Get Product Filters list, 27 filter_group_id is for Brand name filter
        $data['filters'] = Product::getFiltersByGroupId($this->db, 27);

        // get seller pickup city code
        $data['seller_pickup_city_code'] = $this->model_report_analysis->getSellerPickupCityCode();

        // Seller List
        $obj_seller_profile = new SellerProfile( $this );
        // get seller nickname asscending order
        $order_by = array('sort'  => 'ms.nickname',
                              'order' => 'ASC'      
                             );
        $data['seller_list'] = $obj_seller_profile->getSellers($order_by);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/product_wise_margin.tpl', $data));
    } 


    public function downloadProductWiseMarginCSV(){
        $this->load->model('report/analysis');
        $filter_data = array();
        
        $filter_data['filter_wsb_product_code'] = NULL;
        if ( !empty($this->request->get['filter_wsb_product_code']) ) {
            $filter_wsb_product_code                 = $this->request->get['filter_wsb_product_code'];
            $filter_data['filter_wsb_product_code']  = $filter_wsb_product_code;
        }

        $filter_data['filter_seller_sku_code'] = NULL;
        if ( !empty($this->request->get['filter_seller_sku_code']) ) {
            $filter_seller_sku_code                = $this->request->get['filter_seller_sku_code'];
            $filter_data['filter_seller_sku_code'] = $filter_seller_sku_code;
        }
        
        
        $filter_data['filter_seller_id'] = NULL;
        if ( !empty($this->request->get['filter_seller_id']) ) {
            $filter_seller_id                 = $this->request->get['filter_seller_id'];
            $filter_data['filter_seller_id']  = $filter_seller_id;
        }        
        
        $filter_data['filter_brand_name'] = NULL;
        if ( !empty($this->request->get['filter_brand_name']) ) {
            $filter_brand_name                 = $this->request->get['filter_brand_name'];
            $filter_data['filter_brand_name']  = $filter_brand_name;
        }       

        $filter_data['filter_sale_invoice_date_from']  = NULL;
        if ( !empty($this->request->get['filter_sale_invoice_date_from']) ) {
            $filter_sale_invoice_date_from                 = $this->request->get['filter_sale_invoice_date_from'];
            $filter_data['filter_sale_invoice_date_from']  = $filter_sale_invoice_date_from;
        }
        
        $filter_data['filter_sale_invoice_date_to'] = NULL;
        if ( !empty($this->request->get['filter_sale_invoice_date_to']) ) {
            $filter_sale_invoice_date_to                 = $this->request->get['filter_sale_invoice_date_to'];
            $filter_data['filter_sale_invoice_date_to']  = $filter_sale_invoice_date_to;
        }

        $filter_data['filter_order_date_from'] = NULL;
        if ( !empty($this->request->get['filter_order_date_from']) ) {
            $filter_order_date_from                 = $this->request->get['filter_order_date_from'];
            $filter_data['filter_order_date_from']  = $filter_order_date_from;
        }
        
        $filter_data['filter_order_date_to']  = NULL;
        if ( !empty($this->request->get['filter_order_date_to']) ) {
            $filter_order_date_to                 = $this->request->get['filter_order_date_to'];
            $filter_data['filter_order_date_to']  = $filter_order_date_to;
        }
        
        $filter_data['filter_group_by_order'] = NULL;
        if ( !empty($this->request->get['filter_group_by_order']) ) {
            $filter_group_by_order                 = $this->request->get['filter_group_by_order'];
            $filter_data['filter_group_by_order']  = $filter_group_by_order;
        }

        $filter_data['filter_include_non_invoiced_orders']  = 0;
        if ( !empty($this->request->get['filter_include_non_invoiced_orders']) ) {
            $filter_data['filter_include_non_invoiced_orders'] = $this->request->get['filter_include_non_invoiced_orders'];
        }
        
        $filter_data['filter_customer'] = NULL;
        if ( !empty($this->request->get['filter_customer']) ) {
            $filter_customer                 = $this->request->get['filter_customer'];
            $filter_data['filter_customer']  = $filter_customer;
        }

        $filter_data['filter_seller_pickup_city_code'] = NULL;
        if ( !empty($this->request->get['filter_seller_pickup_city_code']) ) {
            $filter_seller_pickup_city_code                 = $this->request->get['filter_seller_pickup_city_code'];
            $filter_data['filter_seller_pickup_city_code']  = $filter_seller_pickup_city_code;
        }

        $results = $this->model_report_analysis->getProductWiseMargin($filter_data);


        $file_name = DIR_DOWNLOAD .'wsb_product_wise_margin.csv';
        $fp = fopen($file_name, 'w');
                     

        $data = array('Product ID', 
                      'WSB Product Code', 
                      'Seller Sku', 
                      'Categories', 
                      'Brand Name', 
                      'Transfer price per piece', 
                      'Product Name', 
                      'Vendor Code', 
                      'Vendor Name', 
                      'Vendor City',
                      'Vendor Pickup City Code'
                     );
                     
        if ( !empty($filter_data['filter_group_by_order']) ) {
            $data = array_merge($data, array('Order No', 
                                             'Suborder No', 
                                             'Order Date', 
                                             'Sale Invoice No', 
                                             'Sale Invoice Date',
                                             'Customer ID', 
                                             'Master ID', 
                                             'Customer Name', 
                                             'Customer Firm', 
                                             'Delivery City', 
                                             'Delivery State', 
                                             'Delivery Country', 
                                             'Delivery Postcode', 
                                             'Set Description' 
                                             ));
        }
        
        $data = array_merge($data, array('Total Pieces Sold', 
                                         'Gross Sales (P)',
                                         'Total Discount (D)',  
                                         'Output Tax',  
                                         'Total Purchase (P-D)', 
                                         'Input Tax',  
                                         'Absolute Margin', 
                                         'Margin Percentage'
                                         ));
        fputcsv($fp, $data);
        
        if( !empty($results) ) {
            foreach ($results as $key => $value) {
                $data = array($value['product_id'], 
                              $value['model'], 
                              $value['sku'], 
                              $value['categories'], 
                              $value['brand_name'], 
                              $value['price'], 
                              $value['name'], 
                              $value['nickname'], 
                              $value['company'], 
                              $value['city'],
                              $value['pickup_city_code']);
                             
                if ( !empty($filter_data['filter_group_by_order']) ) {
                    $data = array_merge($data, array($value['order_no'], 
                                                     $value['suborder_id'], 
                                                     date('d-M-Y', strtotime($value['order_date'])), 
                                                     $value['sale_invoice_no'], 
                                                     ($value['invoice_date'] ? date('d-M-Y', strtotime($value['invoice_date'])) : ''),
                                                     $value['customer_id'], 
                                                     $value['master_id'], 
                                                     trim($value['customer_name']), 
                                                     trim($value['shipping_company']), 
                                                     trim($value['shipping_city']), 
                                                     trim($value['shipping_zone']), 
                                                     trim($value['shipping_country']), 
                                                     trim($value['shipping_postcode']), 
                                                     $value['comment'] 
                                                    ));
                }
                
                $data = array_merge($data, array($value['total_pieces_sold'],
                                                 round($value['gross_product_sales'],2), 
                                                 round($value['total_product_discount'],2), 
                                                 round($value['total_product_sales_tax'],2), 
                                                 round($value['total_product_purchase'],2), 
                                                 round($value['total_product_purchase_tax'],2), 
                                                 round($value['margin'],2), 
                                                 round($value['margin_percentage'],2)
                                                 ));            
                fputcsv($fp, $data);
            }
        }    
        
        fclose($fp);

        if (file_exists($file_name)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_name));
            readfile($file_name);
            exit();
        }

        $this->redirect($this->url->link('report/product_wise_margin.tpl', $data));
    }

    public function getSellerWiseRevenue() {
        $this->load->model('catalog/category');
        $this->load->model('report/analysis');
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('report/analysis', $data);

        $this->document->setTitle($data['heading_title']);

        $data['token'] = $this->session->data['token'];

        $url = '';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('report/analysis', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        // get seller pickup city code
        $data['seller_pickup_city_code'] = $this->model_report_analysis->getSellerPickupCityCode();

        // Seller List
        $obj_seller_profile = new SellerProfile( $this );
        // get seller nickname asscending order
        $order_by = array();
        $order_by['sort']  = 'ms.nickname';
        $order_by['order'] = 'ASC'; 
        $data['seller_list'] = $obj_seller_profile->getSellers($order_by);
        
        //get product categories
        $data['categories'] = array();
        $categories_1 = $this->model_catalog_category->getCategoriesByParent(0);
        foreach ($categories_1 as $category_1) {
            $level_2_data = array();

            $categories_2 = $this->model_catalog_category->getCategoriesByParent($category_1['category_id']);

            foreach ($categories_2 as $category_2) {
                $level_3_data = array();

                $categories_3 = $this->model_catalog_category->getCategoriesByParent($category_2['category_id']);

                foreach ($categories_3 as $category_3) {
                    $level_3_data[] = array(
                            'category_id' => $category_3['category_id'],
                            'name'        => $category_3['name'],
                    );
                }

                $level_2_data[] = array(
                        'category_id' => $category_2['category_id'],
                        'name'        => $category_2['name'],
                        'children'    => $level_3_data
                );
            }

            $data['categories'][] = array(
                    'category_id' => $category_1['category_id'],
                    'name'        => $category_1['name'],
                    'children'    => $level_2_data
            );
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/seller_wise_revenue.tpl', $data));
    }

    public function downloadSellerWiseRevenueCSV(){

        $this->load->model('report/analysis');
        $filter_data = array();
        
        $filter_data['filter_seller_id'] = NULL;
        if ( !empty($this->request->get['filter_seller_id']) ) {
            $filter_seller_id                 = $this->request->get['filter_seller_id'];
            $filter_data['filter_seller_id']  = $filter_seller_id;
        }
        
        $filter_data['filter_category']  = NULL;
        if ( !empty($this->request->get['filter_category']) ) {
            $filter_category                 = $this->request->get['filter_category'];
            $filter_data['filter_category']  = $filter_category;
        }
        
        
        $filter_data['filter_seller_date_added_from']  = NULL;
        if ( !empty($this->request->get['filter_seller_date_added_from']) ) {
            $filter_seller_date_added_from                 = $this->request->get['filter_seller_date_added_from'];
            $filter_data['filter_seller_date_added_from']  = $filter_seller_date_added_from;
        }
        
        $filter_data['filter_seller_date_added_to'] = NULL;
        if ( !empty($this->request->get['filter_seller_date_added_to']) ) {
            $filter_seller_date_added_to                 = $this->request->get['filter_seller_date_added_to'];
            $filter_data['filter_seller_date_added_to']  = $filter_seller_date_added_to;
        }

        $filter_data['filter_sale_invoice_date_from']  = NULL;
        if ( !empty($this->request->get['filter_sale_invoice_date_from']) ) {
            $filter_sale_invoice_date_from                 = $this->request->get['filter_sale_invoice_date_from'];
            $filter_data['filter_sale_invoice_date_from']  = $filter_sale_invoice_date_from;
        }
        
        $filter_data['filter_sale_invoice_date_to'] = NULL;
        if ( !empty($this->request->get['filter_sale_invoice_date_to']) ) {
            $filter_sale_invoice_date_to                 = $this->request->get['filter_sale_invoice_date_to'];
            $filter_data['filter_sale_invoice_date_to']  = $filter_sale_invoice_date_to;
        }
        
        $filter_data['filter_order_date_from'] = NULL;
        if ( !empty($this->request->get['filter_order_date_from']) ) {
            $filter_order_date_from                 = $this->request->get['filter_order_date_from'];
            $filter_data['filter_order_date_from']  = $filter_order_date_from;
        }
        
        $filter_data['filter_order_date_to']  = NULL;
        if ( !empty($this->request->get['filter_order_date_to']) ) {
            $filter_order_date_to                 = $this->request->get['filter_order_date_to'];
            $filter_data['filter_order_date_to']  = $filter_order_date_to;
        }

        $filter_data['filter_seller_pickup_city_code'] = NULL;
        if ( !empty($this->request->get['filter_seller_pickup_city_code']) ) {
            $filter_seller_pickup_city_code                 = $this->request->get['filter_seller_pickup_city_code'];
            $filter_data['filter_seller_pickup_city_code']  = $filter_seller_pickup_city_code;
        }

        $results = $this->model_report_analysis->getSellerWiseRevenue($filter_data);

        $file_name = DIR_DOWNLOAD .'wsb_seller_wise_revenue.csv';
        $fp = fopen($file_name, 'w');
                     

        $data = array('Seller ID', 
                      'Seller Code', 
                      'Seller Firm Name', 
                      'Seller City',
                      'Pickup City Code',
                      'Seller Onboaring Date',
                      'Order No',
                      'Suborder ID', 
                      'Total Sales (P-D)',
                      'Output Tax',  
                      'Total Purchase (P-D)', 
                      'Input Tax', 
                      'Absolute Margin', 
                      'Margin Percentage');
        fputcsv($fp, $data);
        
        if( !empty($results) ) {
            foreach ($results as $key => $value) {
                $data = array($value['seller_id'], 
                              $value['nickname'], 
                              $value['company'], 
                              $value['city'],
                              $value['pickup_city_code'],
                              date('d/m/Y',strtotime($value['date_created'])), 
                              $value['order_no'], 
                              $value['suborder_id'], 
                              round($value['total_product_sales'],2), 
                              round($value['total_product_sales_tax'],2), 
                              round($value['total_product_purchase'],2), 
                              round($value['total_product_purchase_tax'],2), 
                              round($value['margin'],2), 
                              round($value['margin_percentage'],2));            
                fputcsv($fp, $data);
            }
        }    
        
        fclose($fp);

        if (file_exists($file_name)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_name));
            readfile($file_name);
            exit();
        }

        $this->redirect($this->url->link('report/seller_wise_margin.tpl', $data));
    }
    
    
    public function getOrderWiseMargin() {
        
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('report/analysis', $data);

        $this->document->setTitle($data['heading_title']);

        $data['token'] = $this->session->data['token'];

        $url = '';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('report/analysis', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/order_wise_margin.tpl', $data));
    } 


    public function downloadOrderWiseMarginCSV(){
        $this->load->model('report/analysis');
        $filter_data = array();
        
        $filter_data['filter_customer'] = NULL;
        if ( !empty($this->request->get['filter_customer']) ) {
            $filter_customer                 = $this->request->get['filter_customer'];
            $filter_data['filter_customer']  = $filter_customer;
        }

        $filter_data['filter_shipping_city'] = NULL;
        if ( !empty($this->request->get['filter_shipping_city']) ) {
            $filter_shipping_city                = $this->request->get['filter_shipping_city'];
            $filter_data['filter_shipping_city'] = $filter_shipping_city;
        }
        
        
        $filter_data['filter_order_date_from'] = NULL;
        if ( !empty($this->request->get['filter_order_date_from']) ) {
            $filter_order_date_from                 = $this->request->get['filter_order_date_from'];
            $filter_data['filter_order_date_from']  = $filter_order_date_from;
        }
        
        $filter_data['filter_order_date_to']  = NULL;
        if ( !empty($this->request->get['filter_order_date_to']) ) {
            $filter_order_date_to                 = $this->request->get['filter_order_date_to'];
            $filter_data['filter_order_date_to']  = $filter_order_date_to;
        }
        
        
        $filter_data['filter_sale_invoice_date_from']  = NULL;
        if ( !empty($this->request->get['filter_sale_invoice_date_from']) ) {
            $filter_sale_invoice_date_from                 = $this->request->get['filter_sale_invoice_date_from'];
            $filter_data['filter_sale_invoice_date_from']  = $filter_sale_invoice_date_from;
        }
        
        $filter_data['filter_sale_invoice_date_to'] = NULL;
        if ( !empty($this->request->get['filter_sale_invoice_date_to']) ) {
            $filter_sale_invoice_date_to                 = $this->request->get['filter_sale_invoice_date_to'];
            $filter_data['filter_sale_invoice_date_to']  = $filter_sale_invoice_date_to;
        }

        $filter_data['filter_include_non_invoiced_orders']  = 0;
        if ( !empty($this->request->get['filter_include_non_invoiced_orders']) ) {
            $filter_data['filter_include_non_invoiced_orders'] = $this->request->get['filter_include_non_invoiced_orders'];
        }
        
        $filter_data['filter_include_cancelled_orders']  = 0;
        if ( !empty($this->request->get['filter_include_cancelled_orders']) ) {
            $filter_data['filter_include_cancelled_orders'] = $this->request->get['filter_include_cancelled_orders'];
        }

        $results = $this->model_report_analysis->getOrderWiseMargin($filter_data);


        $file_name = DIR_DOWNLOAD .'wsb_order_wise_margin.csv';
        $fp = fopen($file_name, 'w');
                     

        $data = array('Order No', 
                      'Suborder No', 
                      'Sale Invoice No', 
                      'Sale Invoice Date', 
                      'Order Processing Date',
                      'Order Date', 
                      'Order Status', 
                      'Customer ID', 
                      'Master ID', 
                      'Customer Name', 
                      'Customer Firm', 
                      'Delivery City', 
                      'Delivery State', 
                      'Delivery Country', 
                      'Delivery Postcode', 
                      'Payment Method', 
                      'Gross Sales (P)',
                      'Total Discount (D)',  
                      'Output Tax', 
                      'Total Purchase (P-D)', 
                      'Input Tax', 
                      'Absolute Margin', 
                      'Margin Percentage', 
                      'Shipping Charged',
                      'Tax on Shipping', 
                      'Sales Mode');
        fputcsv($fp, $data);
        
        if( !empty($results) ) {
            foreach ($results as $key => $value) {

              $order_processing_date = '';
              if(!empty($value['processing_date'])){
                $order_processing_date = date('d-M-Y', strtotime($value['processing_date']));
              }

                $data = array($value['order_no'], 
                              $value['suborder_id'], 
                              $value['sale_invoice_no'], 
                              !empty($value['sale_invoice_date']) ? date('d-M-Y', strtotime($value['sale_invoice_date'])) : '', 
                              $order_processing_date, 
                              date('d-M-Y', strtotime($value['order_date'])), 
                              $value['order_status'], 
                              $value['customer_id'], 
                              $value['master_id'], 
                              trim($value['customer_name']), 
                              trim($value['shipping_company']), 
                              trim($value['shipping_city']), 
                              trim($value['shipping_zone']), 
                              trim($value['shipping_country']), 
                              trim($value['shipping_postcode']), 
                              trim($value['payment_code']), 
                              round($value['gross_product_sales'], 2),  
                              round($value['total_product_discount'], 2),  
                              round($value['total_product_sales_tax'], 2),  
                              round($value['total_product_purchase'], 2), 
                              round($value['total_product_purchase_tax'], 2), 
                              round($value['margin'], 2), 
                              round($value['margin_percentage'], 2),
                              round($value['shipping_charged'], 2),
                              round($value['tax_on_shipping'], 2),
                              trim($value['sales_role']));            
                fputcsv($fp, $data);
            }
        }    
        
        fclose($fp);

        if (file_exists($file_name)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_name));
            readfile($file_name);
            exit();
        }

        $this->redirect($this->url->link('report/order_wise_margin.tpl', $data));
    } 
    
    
    public function getCustomerWiseRevenue() {
        
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('report/analysis', $data);

        $this->document->setTitle($data['heading_title']);

        $data['token'] = $this->session->data['token'];

        $url = '';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('report/analysis', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/customer_wise_revenue.tpl', $data));
    } 
    
    
    public function downloadCustomerWiseRevenueCSV(){
        $this->load->model('report/analysis');
        $filter_data = array();
        
        $filter_data['filter_customer'] = NULL;
        if ( !empty($this->request->get['filter_customer']) ) {
            $filter_customer                 = $this->request->get['filter_customer'];
            $filter_data['filter_customer']  = $filter_customer;
        }

        $filter_data['filter_shipping_city'] = NULL;
        if ( !empty($this->request->get['filter_shipping_city']) ) {
            $filter_shipping_city                = $this->request->get['filter_shipping_city'];
            $filter_data['filter_shipping_city'] = $filter_shipping_city;
        }
        
        
        $filter_data['filter_customer_onboarding_date_from'] = NULL;
        if ( !empty($this->request->get['filter_customer_onboarding_date_from']) ) {
            $filter_customer_onboarding_date_from                 = $this->request->get['filter_customer_onboarding_date_from'];
            $filter_data['filter_customer_onboarding_date_from']  = $filter_customer_onboarding_date_from;
        }
        
        $filter_data['filter_customer_onboarding_date_to']  = NULL;
        if ( !empty($this->request->get['filter_customer_onboarding_date_to']) ) {
            $filter_customer_onboarding_date_to                 = $this->request->get['filter_customer_onboarding_date_to'];
            $filter_data['filter_customer_onboarding_date_to']  = $filter_customer_onboarding_date_to;
        }
        
        
        $filter_data['filter_order_date_from'] = NULL;
        if ( !empty($this->request->get['filter_order_date_from']) ) {
            $filter_order_date_from                 = $this->request->get['filter_order_date_from'];
            $filter_data['filter_order_date_from']  = $filter_order_date_from;
        }
        
        $filter_data['filter_order_date_to']  = NULL;
        if ( !empty($this->request->get['filter_order_date_to']) ) {
            $filter_order_date_to                 = $this->request->get['filter_order_date_to'];
            $filter_data['filter_order_date_to']  = $filter_order_date_to;
        }
        
        
        $filter_data['filter_sale_invoice_date_from']  = NULL;
        if ( !empty($this->request->get['filter_sale_invoice_date_from']) ) {
            $filter_sale_invoice_date_from                 = $this->request->get['filter_sale_invoice_date_from'];
            $filter_data['filter_sale_invoice_date_from']  = $filter_sale_invoice_date_from;
        }
        
        $filter_data['filter_sale_invoice_date_to'] = NULL;
        if ( !empty($this->request->get['filter_sale_invoice_date_to']) ) {
            $filter_sale_invoice_date_to                 = $this->request->get['filter_sale_invoice_date_to'];
            $filter_data['filter_sale_invoice_date_to']  = $filter_sale_invoice_date_to;
        }

        $results = $this->model_report_analysis->getCustomerWiseRevenue($filter_data);


        $file_name = DIR_DOWNLOAD .'wsb_customer_wise_revenue.csv';
        $fp = fopen($file_name, 'w');
                     

        $data = array('Customer ID', 
                      'Customer Name', 
                      'Customer Firm', 
                      'Delivery City', 
                      'Delivery State', 
                      'Delivery Country', 
                      'Delivery Postcode', 
                      'Total Orders', 
                      'Total Sales (P-D)',
                      'Output Tax',  
                      'Total Purchase (P-D)', 
                      'Input Tax', 
                      'Absolute Margin', 
                      'Margin Percentage');
        fputcsv($fp, $data);
        
        if( !empty($results) ) {
            foreach ($results as $key => $value) {
                $data = array($value['customer_id'], 
                              trim($value['customer_name']), 
                              trim($value['shipping_company']), 
                              trim($value['shipping_city']), 
                              trim($value['shipping_zone']), 
                              trim($value['shipping_country']), 
                              trim($value['shipping_postcode']), 
                              $value['no_of_orders'],
                              round($value['total_product_sales'], 2), 
                              round($value['total_product_sales_tax'], 2), 
                              round($value['total_product_purchase'], 2), 
                              round($value['total_product_purchase_tax'], 2), 
                              round($value['margin'], 2), 
                              round($value['margin_percentage'], 2));            
                fputcsv($fp, $data);
            }
        }    
        
        fclose($fp);

        if (file_exists($file_name)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_name));
            readfile($file_name);
            exit();
        }

        $this->redirect($this->url->link('report/customer_wise_revenue.tpl', $data));
    } 
    
    public function getProductWiseReturn() {
        
        $this->load->model('report/analysis');
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('report/analysis', $data);

        $this->document->setTitle($data['heading_title']);

        $data['token'] = $this->session->data['token'];

        $url = '';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('report/analysis', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        //Get Product Filters list, 27 filter_group_id is for Brand name filter
        $data['filters'] = Product::getFiltersByGroupId($this->db, 27);

        // get seller pickup city code
        $data['seller_pickup_city_code'] = $this->model_report_analysis->getSellerPickupCityCode();

        $data['form_action'] = $this->url->link('report/analysis/downloadProductReturnCSV', 'token=' . $this->session->data['token'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/product_wise_return.tpl', $data));
    }
    
    public function downloadProductReturnCSV() {
		
	  if(empty($this->request->post)){
          return true;
      }
      $this->load->model('report/analysis');
      $filter_data = array();
      
      $filter_data['filter_date_from'] = NULL;
      if ( !empty($this->request->post['filter_date_from']) ) {
          $filter_date_from                 = $this->request->post['filter_date_from'];
          $filter_data['filter_date_from']  = $filter_date_from;
      }

      $filter_data['filter_date_to'] = NULL;
      if ( !empty($this->request->post['filter_date_to']) ) {
          $filter_date_to                = $this->request->post['filter_date_to'];
          $filter_data['filter_date_to'] = $filter_date_to;
      }
      
      $filter_data['filter_model'] = NULL;
      if ( !empty($this->request->post['filter_model']) ) {
          $filter_model                 = $this->request->post['filter_model'];
          $filter_data['filter_model']  = $filter_model;
      }

      $filter_data['filter_category_id'] = NULL;
      if ( !empty($this->request->post['filter_category_id']) ) {
          $filter_category_id                 = $this->request->post['filter_category_id'];
          $filter_data['filter_category_id']  = $filter_category_id;
      }
      
      $filter_data['filter_seller_id']  = NULL;
      if ( !empty($this->request->post['filter_seller_id']) ) {
          $filter_seller_id                 = $this->request->post['filter_seller_id'];
          $filter_data['filter_seller_id']  = $filter_seller_id;
      }

      $filter_data['filter_brand_name']  = NULL;
      if ( !empty($this->request->post['filter_brand_name']) ) {
          $filter_brand_name                 = $this->request->post['filter_brand_name'];
          $filter_data['filter_brand_name']  = $filter_brand_name;
      }
      
      $filter_data['filter_order'] = NULL;
      if ( !empty($this->request->post['filter_order']) ) {
          $filter_order                 = $this->request->post['filter_order'];
          $filter_data['filter_order']  = $filter_order;
      }

      $filter_data['filter_seller_pickup_city_code'] = NULL;
      if ( !empty($this->request->post['filter_seller_pickup_city_code']) ) {
          $filter_seller_pickup_city_code                 = $this->request->post['filter_seller_pickup_city_code'];
          $filter_data['filter_seller_pickup_city_code']  = $filter_seller_pickup_city_code;
      }
      
      $results = $this->model_report_analysis->getProductWiseReturn($filter_data);

      header("Content-type: application/csv");
      header("Content-Disposition: attachment; filename=product_return_analysis.csv");
      header("Pragma: no-cache");
      header("Expires: 0");
        
      // create a file pointer connected to the output stream
      $file = fopen('php://output', 'w');
      $head = array('Product ID', 
                    'Category',
                    'Brand Name',
                    'WSB Product Code',
                    'Product Name',
                    'BuyerInvoice No.',
                    'BuyerInvoice Date',
                    'Pieces',
                    'Seller Input Tax',
                    'TransferPrice Per Piece',
                    'Output Tax Rates',
                    'Price Per Piece',
                    'Discount Per Piece',
                    'Vendor-Code',
                    'Vendor-Name',
                    'City',
                    'Pickup City Code', 
                    'Order No',
                    'SuborderId',
                    'Return Reason',
                    'Current Return Action',
                    'Return Qty',
                    'Custom DN For Firm Name',
                    'DN No.',
                    'DN Date',
                    'CN No.',
                    'CN Date',
                    'Stock Transfer',
                    'Is SOR'
                   );
		  fputcsv($file, $head);

        // output each row of the data
    	foreach ($results as $row)
    	{
        $row_data = array(
                      $row['product_id'],
                      $row['category'], 
                      $row['brand_name'], 
                      $row['model'],
                      $row['product'],
                      $row['buyer_invoice_no'],
                      $row['buyer_invoice_date'],
                      $row['quantity'],
                      $row['seller_input_tax'],
                      $row['transfer_price_per_piece'],
                      $row['output_tax_rates'],
                      $row['price_per_piece'],
                      $row['discount_per_piece'],
                      $row['nickname'],
                      $row['company'],
                      $row['city'],
                      $row['pickup_city_code'],
                      $row['order_no'],
                      $row['suborder_id'],
                      $row['return_reason'],
                      $row['return_action'],
                      $row['return_qty'],
                      $row['custom_party_name'],
                      $row['dn_no'],
                      $row['dn_date'],
                      $row['cn_no'],
                      $row['cn_date'],
                      $row['stock_transfer'],
                      $row['is_sor']);

    		fputcsv($file, $row_data);
    	}
    	exit;
        
      
	}

}
