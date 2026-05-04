<?php

require_once(DIR_SYSTEM . 'library/tcpdf/tcpdf.php');
require_once(DIR_SYSTEM . 'library/tcpdf/config/tcpdf_config.php');


// Extend the TCPDF class to create custom Header and Footer
class MYTCPDF extends TCPDF {
    //Page header
    public function Header() {
        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);
        // set bacground image
        $img_file = DIR_SYSTEM_IMAGE.'cancelled.png'; 
        //no-repeat fixed center
        $this->Image($img_file, '40', '70', '', '', '', '', 'C', false, '', '', false, false, 0);
        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
    }
}