<?php 
require(DIR_SYSTEM.'library/fpdf181/fpdf.php');

class SellerInvoicePDF extends FPDF
{
	protected $col = 0; // Current column
	protected $y0;      // Ordinate of column start
	public $title;
	public $data;
	protected $dynamic_y;
	protected $address_y;

	public $last_Y_position = 0;

	function Header()
	{
	    // Page header

		$this->SetFont('Arial','B',10);
		$w = $this->GetStringWidth($this->title)+6;
		$this->SetX((210-$w)/2);
		//$this->SetDrawColor(80,255,255);
		//$this->SetFillColor(255,255,255);
		$this->SetTextColor(0,0,0);
		$this->SetLineWidth(0);
		$this->Cell($w,9,$this->title,0,1,'C',false);
		$this->Ln(10);
		// Save ordinate
		$this->y0 = $this->GetY();
	}

	function Footer()
	{
	    // Page footer
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->SetTextColor(128);
	    //$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
	}

	function AddressInformation($seller_address, 
		                        $order_info,
		                        $invoice_no,
		                        $invoice_date){


		$to = $seller_address['company'].
				" \n ". $seller_address['address1'].
				"\n".  $seller_address['address2'].
				"\n".  $seller_address['city'] .' - ' .$seller_address['pincode'] .
				"\n".  $seller_address['state'] . '- '. $seller_address['country'] ;

		$from = "\n".'BUYER:'. "\n".
				'  WHOLESALEBOX INTERNET PVT LTD.'.
				"\n".'  B-1 CRYSTAL MALL, BANI PARK'.
				"\n".'  JAIPUR, RAJASTHAN'.
				"\n".'  Buyer Tin number : 08195900085';		 

		
		$this->SetXY(15,25);
		$this->SetFont('Times','B',11);
		$this->Cell(180,10,'All Subject to JAIPUR Jurisdiction',1,0,'C',false);

		$this->Ln(10);
		$this->SetX(15);
		$this->SetFont('Times','',9);
		$this->SetFillColor(255,255,255);
		$this->Cell(75,45,'',1,0,'',false);

		$this->SetX(15);
		$this->SetFont('Arial','B',9);
		$this->MultiCell(75,5,$to,"LR",1,'L',false);		

		$this->SetFont('Times','',8);
		$this->SetXY(90,35);
		$this->Cell(25,5,'Original',1,'0','',false);
		$this->SetXY(90,40);
		$this->Cell(25,5,'For Buyers',1,'0','',false);
		$this->SetXY(115,35);
		$this->Cell(35,10,'Duplicate For Sellers',1,'0','C',false);
		$this->SetXY(150,35);
		$this->Cell(45,10,'Triplicate For Transporter',1,'0','L',false);

		$this->SetFont('Times','B',8);
		$this->SetXY(90,45);
		$this->Cell(55,5,'Inv No : '. $invoice_no,1,'0','L',false);

		$this->SetFont('Times','B',8);
		$this->SetXY(145,45);
		$this->Cell(50,5,'Dated : ' . $invoice_date,1,'0','L',false);

		$this->SetXY(90,50);
		$this->SetFont('Times','B',9);
		$this->SetFillColor(255,255,255);
		$this->MultiCell(105,5,$from,1,1,'C',false);


		$this->SetXY(15,80);
		$this->SetFont('Times','',8);
		$this->Cell(75,5,'Booked From : JAIPUR',1,0,'',false);

		$this->SetXY(90,80);
		$this->SetFont('Times','B',8);
		$this->Cell(50,5,'Order No: '. $order_info['order_no'],1,1,'',false);
		$this->SetXY(140,80);
		$this->Cell(55,5,'Dated: ' . date('d-m-Y',strtotime($order_info['date_added'])),1,1,'',false);

		//$this->y0 = $this->GetY();
	}



	function TableHeader($data) {

		$w = array(10,70,10,13,13,15,15,14,20);

		$this->SetXY(15,85);
		$this->SetFont('Times','B',8);
		$this->Cell($w[0],10,$data['column_sno'],1,'0','C',false);
		$this->Cell($w[1],10,$data['column_sku'],1,'0','C',false);
		$this->Cell($w[2],10,$data['column_sets'],1,'0','C',false);
		$this->Cell($w[3],10,$data['column_pcs_set'],1,'0','C',false);
		$this->Cell($w[4],10,$data['column_total_pcs'],1,'0','C',false);
		$this->Cell($w[5],10,$data['column_rate_pcs'],1,'0','C',false);
		$this->Cell($w[6],10,$data['column_seller_input_tax'],1,'0','C',false);
		$this->Cell($w[7],10,$data['column_tax'],1,'0','C',false);
		$this->Cell($w[8],10,$data['column_amount'],1,'0','C',false);
		$this->Ln();

	}

	function TableData($data){

		//$w = array(10,95,10,10,10,15,15,15);
		$w = array(10,70,10,13,13,15,15,14,20);

		
		$this->SetFont('Arial','',8);
		$j = 1;
		foreach($data['product_data'] as $list){
			$this->SetX(15);
			$this->Cell($w[0],5,$j,'1','0','C',false);
			$this->Cell($w[1],5,$list['product_name'],1,'0','C',false);
			$this->Cell($w[2],5,$list['quantity'],1,'0','C',false);
			$this->Cell($w[3],5,$list['piece_in_set'],1,'0','C',false);
			$this->Cell($w[4],5,$list['total_pieces'],1,'0','C',false);
			$this->Cell($w[5],5,$list['rate_per_piece'],1,'0','C',false);
			$this->Cell($w[6],5,$list['seller_input_tax'],1,'0','C',false);
			$this->Cell($w[7],5,$list['tax'],1,'0','C',false);
			$this->Cell($w[8],5,$list['amount'],1,'0','C',false);
			$this->Ln();
			$j++;
		}

		$this->SetX(15);
		$this->SetFont('Arial','B',8);
		$this->Cell($w[0],10,'','1','0','C',false);
		$this->Cell($w[1],10,'Total','1','0','R',false);
		$this->Cell($w[2],10,$data['total_set'],1,'0','C',false);
		$this->Cell($w[3],10,'',1,'0','C',false);
		$this->Cell($w[4],10,$data['total_pieces'],1,'0','C',false);
		$this->Cell($w[5],10,'',1,'0','C',false);
		$this->Cell($w[6],10,'',1,'0','C',false);
		$this->Cell($w[7],10,'',1,'0','C',false);
		$this->Cell($w[8],10,$data['total_amount'],1,'0','C',false);
		$this->Ln();
				
		$this->SetX(15);
		$this->SetFont('Arial','B',8);
		$this->Cell($w[0],10,'','1','0','C',false);
		$this->Cell($w[1],10,'Total Tax','1','0','R',false);
		$this->Cell($w[2],10,'',1,'0','C',false);
		$this->Cell($w[3],10,'',1,'0','C',false);
		$this->Cell($w[4],10,'',1,'0','C',false);
		$this->Cell($w[5],10,'',1,'0','C',false);
		$this->Cell($w[6],10,'',1,'0','C',false);
		$this->Cell($w[7],10,'',1,'0','C',false);
		$this->Cell($w[8],10,$data['total_tax'],1,'0','C',false);
		$this->Ln();

		$this->SetX(15);
		$this->SetFont('Arial','B',8);
		$this->Cell($w[0],10,'','1','0','C',false);
		$this->Cell($w[1],10,'Grand Total','1','0','R',false);
		$this->Cell($w[2],10,'',1,'0','C',false);
		$this->Cell($w[3],10,'',1,'0','C',false);
		$this->Cell($w[4],10,'',1,'0','C',false);
		$this->Cell($w[5],10,'',1,'0','C',false);
		$this->Cell($w[6],10,'',1,'0','C',false);
		$this->Cell($w[7],10,'',1,'0','C',false);
		$this->Cell($w[8],10,$data['grand_total'],1,'0','C',false);
		$this->Ln();
			
		$this->SetX(15);
		$this->SetFont('Arial','B',8);				
		$this->Cell(180,5,$data['amount_in_word'],'1','0','L',false);
		//$this->Ln();
	}

	function TableFooter($seller_address){

		$this->Ln();

		$this->setX(15);
		$this->SetFont('Times','',9);
		$this->SetFillColor(255,255,255);
		$this->Cell(180,35,'',1,0,'C',false);

		


		//$this->setXY(15,120);
		$this->setX(15);
		$this->SetFont('Times','B',9);
		$this->Cell(90,5,'Our VAT / CST Reg.No : '.$seller_address['tin'],0,0,'',false);

		$this->setX(15);
		$this->Cell(90,45,'All payments are to be made By A/C payee cheque / Draft Only. ',0,'B','',false);
		$this->setX(15);
		//$this->Cell(90,55,'Payable at '. ucwords($seller_address['city']) .' only.',0,0,'',false);

		
		//$this->setXY(105,125);
		$this->setX(105);
		$this->Cell(90,5,'E & O E',0,0,'C',false);
		$this->setX(105);
		$this->Cell(90,15,'For '. ucwords($seller_address['company']),0,0,'C',false);

		
		//$this->setXY(105,150);
		$this->setX(105);
		$this->SetFont('Times','',11);
		$this->SetFillColor(255,255,255);
		$this->Cell(90,60,'Authorised Signatory',0,'0','C',false);
	}


	function PrintChapter($seller_address, 
		                  $order_info, 
		                  $table_header,
		                  $table_data,
		                  $invoice_name, 
		                  $invoice_date)
	{
		// Add chapter
	    $this->AddPage();
	    $this->AddressInformation($seller_address, $order_info, $invoice_name, $invoice_date);
	    $this->TableHeader($table_header);
	    $this->TableData($table_data);
	    $this->TableFooter($seller_address);
	}

	

}

?>
