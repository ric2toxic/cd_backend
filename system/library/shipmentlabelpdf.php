<?php 
require(DIR_SYSTEM.'library/fpdf181/fpdf.php');

class ShipmentLabelPdf extends FPDF
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


	function ShippingLabel($data_to, $data_from, $courier = ''){

//		$barcode_image = '../image/barcode/20160906467.png';
//		$shor_state = 'HYDU';
//		$to = "TO \n Lakshmi Tulasi \n c/o Charshimas Collections \n Secretariat Colony, Near Golden Temple \n Manikonda \n Hyderabad - 302016 \n Telangana \n Phone: 9885479333";
//		$from = "FROM \n WholesaleBox Internet Pvt. Ltd. \n B-1, Crystal Mall, Banipark, \n Jaipur - 302016 \n Rajasthan \n (+91) 141-4049163 ";
//		$this->SetFont('Arial','',20);
//
//		$this->Image($barcode_image,10,6,50);
//		$this->SetXY(75,6);
//		$this->Cell(60,10,$shor_state,0,0,'C',false);
//		$this->Image($barcode_image,150,6,50);
//
//		$this->Ln(15);
//		$this->SetFont('Arial','B',12);
//		$this->Cell(60,8,'Docket No: 140480165',0,0,'L',false);
//		$this->Cell(80,8,'CASH ON DELIVERY - Rs. 5,555.00 ',0,0,'C',false);
//		$this->Cell(50,8,'Weight : 8 Kg',0,0,'R',false);
//
//		$this->Ln(10);
//		$this->SetFont('Arial','',12);
//		$this->Cell(60,8,'Order No: 20160906579',0,0,'L',false);
//
//		$this->Ln(10);
//		$this->SetFont('Arial','',12);
//		$this->SetFillColor(255,255,255);
//		$this->MultiCell(100,6,$to,0,1,'L',false);
//
//		$this->Ln(5);
//		$this->SetFont('Arial','',12);
//		$this->SetFillColor(255,255,255);
//		$this->MultiCell(100,6,$from,0,1,'L',false);

		$barcode_image = '';
		if( !empty($data_to['shipping_address']['barcode_img']) ){
			$barcode_image = $data_to['shipping_address']['barcode_img'];
		}

		$shor_state = $data_to['shipping_address']['short_state_name'];


		$shipping_address_1 = '';
		$shipping_address_2 = '';

		$warehouse_address_1 = '';
		$warehouse_address_2 = '';

		if(!empty($data_to['shipping_address']['shipping_address_1'])){
			$shipping_address_1 = "\n ". $data_to['shipping_address']['shipping_address_1'];
			  
		}
		if(!empty($data_to['shipping_address']['shipping_address_2'])){
			$shipping_address_2= "\n ". $data_to['shipping_address']['shipping_address_2'];
		}
					

		if(!empty($data_from['from_address']['address_1'])){
			$warehouse_address_1 = " \n ". $data_from['from_address']['address_1'] ;
			  
		}

		if(!empty($data_from['from_address']['address_2'])){
			$warehouse_address_2= " \n ". $data_from['from_address']['address_2'] ;
		}


		$to = " ".$data_to['shipping_address']['shipping_customername'].
			"\n c/o ". $data_to['shipping_address']['shipping_company'].
				     $shipping_address_1.
				     $shipping_address_2.
			"\n ". $data_to['shipping_address']['shipping_city']." - ".
				   $data_to['shipping_address']['shipping_postcode'].
			"\n ". $data_to['shipping_address']['shipping_zone']." - ".
		 		   $data_to['shipping_address']['shipping_country'].
			" \n Phone: ". $data_to['shipping_address']['telephone']."";

		$from = " ".$data_from['from_address']['warehouse_name'].
				$warehouse_address_1 .
				$warehouse_address_2 .
			" \n ". $data_from['from_address']['city'] . " - " . 
			        $data_from['from_address']['postcode'] .
			" \n ". $data_from['from_address']['state'] . " -  " . 
			        $data_from['from_address']['country'].
			" \n ". $data_from['from_address']['telephone'] . "";


		$this->SetFont('Arial','',20);
                
                if(isset($courier) and $courier!='') {
                    $this->SetFont('Arial','B',12);
                    $this->SetXY(25,20);
                    $this->Cell(75,10,'Courier : ',0,0,'L',false);
                    
                    $this->SetFont('Arial','B',20);
                    $this->SetXY(57,20);
                    $this->Cell(75,10,$courier,0,0,'L',false);
                }
                
                
		if( $barcode_image ){
			$this->Image($barcode_image,25,35,70,23);
			$this->SetXY(70,40);
			$this->Cell(68,15,$shor_state,0,0,'C',false);
			$this->Image($barcode_image,115,35,70,23);
		}

			$this->Ln(25);
			$this->SetFont('Arial','B',16);
			$this->SetXY(25,65);

		if( !empty($data_to['shipping_address']['tracking_no']) ){ 
			$this->Cell(75,10,'Docket No: '.$data_to['shipping_address']['tracking_no'],0,0,'L',false);
		}
		if( !empty($data_to['shipping_address']['weight']) ){ 
			$this->Cell(75,10,$data_to['shipping_address']['weight'] ." K.G.",0,0,'R',false);
		}	

                if( !empty($data_to['shipping_address']['no_of_pkg']) ){ 
                    $this->Ln(25);
                    $this->SetFont('Arial','B',16);
                    $this->SetXY(152,70);
                    $this->Cell(155,12,'No of Pkg: '.$data_to['shipping_address']['no_of_pkg'],0,0,'L',false);
		}
                
		$this->Ln(10);
		$this->SetFont('Arial','',20);
		$this->SetXY(25,75);
		$this->Cell(160,15,$data_to['shipping_address']['payment_cod'],0,0,'C',false);
		

		$this->Ln(5);
		$this->SetFont('Arial','B',16);
		$this->SetXY(25,92);
		$this->Cell(60,8,'Order No: '.$data_to['shipping_address']['order_no'],0,0,'L',false);

		$this->Ln(10);
		$this->SetXY(25,110);
		$this->SetFont('Arial','B',16);
		$this->cell(50,5,'TO',0,1,'L',false);

		$this->SetXY(25,115);
		$this->SetFont('Arial','',18);
		$this->SetFillColor(255,255,255);
		$this->MultiCell(170,8,$to,0,1,'L',false);

		$y = $this->GetY();
		//$this->Ln(10);
		$this->SetXY(25,$y+10);
		$this->SetFont('Arial','B',16);
		$this->cell(50,5,'FROM',0,1,'L',false);

		$y1 = $this->GetY();
		//$this->Ln(5);
		$this->SetXY(25,$y1);
		$this->SetFillColor(255,255,255);
		$this->SetFont('Arial','',18);
		$this->MultiCell(170,8,$from,0,1,'L',false);
	}


	function PrintChapter($data_to, $data_from, $courier = '')
	{
		// Add chapter
	    $this->AddPage();
		$this->ShippingLabel($data_to, $data_from, $courier);
	}

}

?>
