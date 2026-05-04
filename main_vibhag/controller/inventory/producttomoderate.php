<?php
class ControllerInventoryProductToModerate extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('inventory/product_to_moderate');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('inventory/producttomoderate');
        $this->load->model('catalog/category');

        $this->getList();
    }

    protected function getList() {

        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('inventory/product_to_moderate', $data);

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        //get product id with in array by approve and reject button
        if (isset($this->request->post['product_id'])) {
            $data['product_id'] = (array)$this->request->post['product_id'];
        } else {
            $data['product_id'] = array();
        }

        $url = '';

        $general_url = $url;
        if (isset($this->request->get['page'])) {
            $general_url .= '&page=' . $this->request->get['page'];
        }
          
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('inventory/producttomoderate', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

        $data['products'] = array();

        $filter_data = array(
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
        );

        $this->load->model('tool/image');

        $product_total = $this->model_inventory_producttomoderate->getTotalProducts($filter_data);

        $results = $this->model_inventory_producttomoderate->getProducts($filter_data);

        foreach ($results as $result) {
                $image = $this->model_tool_image->resize($result['image'], 40, 40);

            $data['products'][] = array(
                'product_id' => $result['product_id'],
                'image'      => $image,
                'name'       => $result['name'],
                'model'      => $result['model'],
                'price'      => $result['price'],
                'seller_tax' => $result['seller_tax'],
                'commission' => $result['commission'],
                'piece_in_set' => $result['piece_in_set'],
                'set_description' => $result['set_description'],
                'weight'    => $result['weight'],
                'edit'       => $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url, 'SSL')
            );
        }

        $data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $pagination_url = $url;
        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('inventory/producttomoderate', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));
        $data['moderate_approve'] = $this->url->link('inventory/producttomoderate/productToModerateApprove', 'token=' . $this->session->data['token'] .$pagination_url, 'SSL');
        $data['moderate_reject'] = $this->url->link('inventory/producttomoderate/productToModerateReject', 'token=' . $this->session->data['token'] .$pagination_url, 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('inventory/product_to_moderate.tpl', $data));
    }


    public function productToModerateApprove(){
        $this->load->model('inventory/producttomoderate');
        $this->load->language('inventory/product_to_moderate');

        if (isset($this->request->post['product_id'])) {
            $product_id = $this->request->post['product_id'];
            $this->model_inventory_producttomoderate->updateProductModerateApprove($product_id);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('inventory/producttomoderate', 'token=' . $this->session->data['token'], 'SSL'));
        }
        $this->getList();
    }

    public function productToModerateReject(){
        $this->load->model('inventory/producttomoderate');
        $this->load->language('inventory/product_to_moderate');

        if (isset($this->request->post['product_id'])) {
            $product_id = $this->request->post['product_id'];
            $this->model_inventory_producttomoderate->updateProductModerateReject($product_id);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('inventory/producttomoderate', 'token=' . $this->session->data['token'], 'SSL'));
        }
        $this->getList();
    }
}