<?php
class ControllerReviewUserQuestions extends Controller{
    private $error = array();

    public function index(){
        $this->getList();
    }
    public function delete(){
        $this->load->language('review/user_questions');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('review/user_questions');
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $question_id) {
                $this->model_review_user_questions->deleteQuestion($question_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            $this->response->redirect($this->url->link('review/user_questions', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    /**
     * get full information
     */
    protected function getList() {

        $this->load->model('review/user_questions');
        $this->load->model('tool/image');

        // Initialization
        $data = array();
        $filter_data = array();
        $general_url = '';

        // Autoloading the lanugage
        $this->load->autoLoadLanguage('review/user_questions', $data);
        $this->document->setTitle($this->language->get('heading_title'));

        // Looping over GET request params
        foreach ( $this->request->get as $key => $value ) {
            // Deal with filter_% keys
            if ( stripos($key, 'filter_') === 0 ) {
                // Filter(s) to get Orders from Model
                $filter_data[$key] = $value;

                // Populating URL
                $general_url .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));

                // Populating data array
                $data[$key] = $value;
            }
        }

        // Sorting is always ORDER BY question_id DESC; Getting Page number
        $page  = $this->request->get['page']  ?? 'FIRST';
        $filter_data['limit'] = 10;
        $filter_data['page'] = $page;

        // Breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('review/user_questions', 'token=' . $this->session->data['token'] , 'SSL')
        );

        // Get Questions based on Filter data
        $results = $this->model_review_user_questions->getQuestions($filter_data);

        // Populating questions to be passed onto template file
        $data['questions'] = array();
        foreach($results as $result){
            $data['questions'][] = array(
                'question_id'       => $result['question_id'],
                'product_id'        => $result['product_id'],
                'product_image'     => $this->model_tool_image->resize($result['image'], 80, 80),
                'product_model'     => $result['model'],
                'seller_company'    => $result['company'],
                'seller_mobile_no'  => $result['mobile_no'],
                'seller_email'      => $result['seller_email'],
                'question'          => $result['question'],
                'customer_id'       => $result['customer_id'],
                'customer_name'     => $result['customer_name'],
                'customer_telephone'=> $result['telephone'],
                'customer_email'    => $result['customer_email'],
                'date_added'        => date('d-m-Y',strtotime($result['date_added'])),
                'is_answered'       => $result['is_answered'],
                'product_link'      => $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . $general_url .'&product_id='.$result['product_id'], 'SSL'),
                'customer_link'      => $this->url->link('sale/customer/edit', 'token=' . $this->session->data['token'] . $general_url .'&customer_id='.$result['customer_id'], 'SSL')
            );
        }

        $data['token'] = $this->session->data['token'];
        $data['delete'] = $this->url->link('review/user_questions/delete', 'token=' . $this->session->data['token'] . $general_url, 'SSL');
        $data['selected'] = (array)($this->request->post['selected'] ?? array());

        $pagination = new PaginationV2();
        $pagination->page = $page;
        $pagination->next = end($results)['question_id'];
        $pagination->total = count($results);
        $pagination->limit = 10;
        $pagination->url = $this->url->link('review/user_questions', 'token=' . $this->session->data['token'] . $general_url . '&page={page}', 'SSL');
        $data['pagination'] = $pagination->render();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('review/user_questions_list.tpl', $data));

    }

    public function mark_as_answered(){
        $this->load->model('review/user_questions');
        $answered = $this->request->get['is_answered'];
        $qid = $this->request->get['qid'];

        $this->model_review_user_questions->markAnswered($answered, $qid);
    }

    public function update_question(){
        $this->load->model('review/user_questions');
        $question_text = $this->request->get['question_text'];
        $qid = $this->request->get['qid'];

        $json['qt'] = $question_text;

        $this->model_review_user_questions->updateQuestion($question_text, $qid);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'review/user_questions')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}