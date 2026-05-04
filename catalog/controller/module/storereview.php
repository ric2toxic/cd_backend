<?php

class ControllerModuleStoreReview extends Controller
{
    public function index($setting)
    {
        static $module = 0;
        $data['heading_title'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8');

        $data['button_all_text'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['all_text'], ENT_QUOTES, 'UTF-8');
        $data['layout'] = (int)$setting['layout_id'];

        $data['button_all'] = (int)$setting['button_all'];
        $data['keyword'] = $this->url->link('review/store_review');

        $this->load->model('review/store_review');

        $results = $this->model_review_store_review->getModuleReviews(0, $setting['limit'], $setting['order']);

        if ($results) {
            foreach ($results as $result) {
                $data['reviews'][] = array(
                    'review_id' => $result['review_id'],
                    'text' => utf8_substr(strip_tags(html_entity_decode($result['text'], ENT_QUOTES, 'UTF-8')), 0, $setting['text_limit']) . '..',
                    'rating' => (int)$result['rating'],
                    'author' => $result['author'],
                    'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                );
            }

            $data['module'] = 'sr' . $module++;

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/storereview.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/module/storereview.tpl', $data);
            } else {
                return $this->load->view('default/template/module/storereview.tpl', $data);
            }
        }
    }
}