<?php
class ControllerAccountsSaleinvoice extends Controller {
    private $error = array();

    public function index(){
        $data = array();
        $this->load->autoLoadLanguage('accounts/saleinvoice',$data);

        $this->document->setTitle($data['heading_saleinvoice_title']);

        $data['error_no_result'] = '';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $data['heading_saleinvoice'],
            'href' => $this->url->link('accounts/saleinvoice', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action'] = $this->url->link('accounts/saleinvoice','token=' . $this->session->data['token'], 'SSL');
        // return value of download
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            if (isset($this->request->post['filter_invoice_date_from'])) {
                $filter_invoice_date_from = $this->request->post['filter_invoice_date_from'];
            } else {
                $filter_invoice_date_from = null;
            }

            if (isset($this->request->post['filter_invoice_date_to'])) {
                $filter_invoice_date_to = $this->request->post['filter_invoice_date_to'];
            } else {
                $filter_invoice_date_to = null;
            }

            if (isset($this->request->post['filter_invoice_no_from'])) {
                $filter_invoice_no_from = $this->request->post['filter_invoice_no_from'];
            } else {
                $filter_invoice_no_from = null;
            }

            if (isset($this->request->post['filter_invoice_no_to'])) {
                $filter_invoice_no_to = $this->request->post['filter_invoice_no_to'];
            } else {
                $filter_invoice_no_to = null;
            }

            $filter_data = array(
                'filter_invoice_date_from' => $filter_invoice_date_from,
                'filter_invoice_date_to'   => $filter_invoice_date_to,
                'filter_invoice_no_from'   => $filter_invoice_no_from,
                'filter_invoice_no_to'     => $filter_invoice_no_to,
            );

            $this->load->model('accounts/saleinvoice');
            $records = $this->model_accounts_saleinvoice->getInvoiceDetails($filter_data);

            if( $records ){
                $all_urls = array();
                $dirname = '';
                foreach( $records as $content ){
                    $filename = array(
                            'order_id' => $content['order_id'],
                            'suborder_id' => $content['suborder_id'],
                        );
                    $filename = base64_encode(serialize($filename));
                    $buyer_invoice = new BuyerInvoice( $this, $filename );
                    $buyer_invoice->setOptions( 'get_full_path' , TRUE );
                    $filepath = $buyer_invoice->getFile();
                    $filepath = base64_decode($filepath);
                    $filename = basename($filepath);
                    $all_urls[] = basename($filepath);
                    $dirname = dirname($filepath);
                }

                //$files = array('readme.txt', 'test.html', 'image.gif');
                $zipname = DIR_DLOAD_BYR_INV.'saleinvoices.zip';

                if( file_exists($zipname) ){
                    unlink( $zipname );
                }

                $zip = new ZipArchive;
                if($zip->open($zipname , ZIPARCHIVE::CREATE )!==TRUE) {
                    exit("cannot open <$archive_file_name>");
                }
                foreach ($all_urls as $files) {
                  $zip->addFile($dirname.'/'.$files,$files);
                }
                $zip->close();
                header('Content-Type: application/zip');
                header('Content-disposition: attachment; filename='.basename($zipname));
                header('Content-Length: ' . filesize($zipname));
                readfile($zipname);
                exit;
            }else{
                $data['error_no_result'] = $data['text_no_result'];
            }
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('accounts/saleinvoice.tpl', $data));
    }
}
