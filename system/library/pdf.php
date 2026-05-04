<?php 
require(DIR_SYSTEM.'library/fpdf181/fpdf.php');

class PDF extends FPDF
{
	protected $col = 0; // Current column
	protected $y0;      // Ordinate of column start
	public $title;
	public $data;
	protected $dynamic_y;
	protected $address_y;

	function Header()
	{
	    // Page header

	    $this->SetFont('Arial','B',15);
	    $w = $this->GetStringWidth($this->title)+6;
	    $this->SetX((210-$w)/2);
	    $this->SetDrawColor(255);
	    // $this->SetFillColor(230,230,0);
	    $this->SetFillColor(255,255,255);

	    $this->SetTextColor(220,50,50);
	    $this->SetLineWidth(1);
	    $this->Cell($w,9,$this->title,1,1,'C');
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
	    $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
	}

	function SetCol()
	{

	    // Set position at a given column
	    $this->col = $this->col+1;
	    $x = 10+$this->col*65;
	    $this->SetLeftMargin($x);
	    $this->SetX($x);
	    $this->SetY($this->y0);
	}

	/*function AcceptPageBreak()
	{
	    // Method accepting or not automatic page break
	    if($this->col<2)
	    {
	        // Go to next column
	        $this->SetCol($this->col+1);
	        // Set ordinate to top
	        $this->SetY($this->y0);
	        // Keep on page
	        return false;
	    }
	    else
	    {
	        // Go back to first column
	        $this->SetCol(0);
	        // Page break
	        return true;
	    }
	}*/

	function invoice_title($order_no, $order_code, $invoice_date)
	{
	    // Title
	    $this->SetFont('Arial','B',12);
	    $this->SetLineWidth(0.1);

	    // $this->SetFillColor(200,250,255);
	    $this->SetFillColor(255,255,255);
	    $this->Cell(60,6,"Order No.: ". $order_no,0,0,'L',true);
	    $this->Cell(60,6,"". $order_code,0,0,'C',true);
	    $this->Cell(60,6,"Invoice Date: ". $invoice_date,0,1,'R',true);
	    $this->Ln(2);
	    // Save ordinate
	    $this->y0 = $this->GetY();
	}
	public function invoice_mini()
	{
		$this->SetFont('Arial','B',12);
		// $this->SetFillColor(200,220,255);
		$this->Cell(65,6,$this->data['text_ship_to'],1,0,'L',false);
		$this->Cell(65,6,$this->data['text_payer'],1,0,'L',false);
		$this->Cell(65,6,$this->data['text_order_detail'],1,1,'L',false);
		// Save ordinate


		$this->y0 = $this->GetY();

	}

	function invoice_address($address, $tin='', $telephone='')
	{
		$this->Line(10 ,$this->GetY(), 205, $this->GetY());

	    // Read text file
		$this->SetFont('Arial','',12);
		// $this->SetFillColor(200,250,250);

	    // $this->MultiCell(60,5,$txt, 1,1);
	    
	    foreach ($address as $key => $value) {
	    	$this->MultiCell(65,5,$value,'L,R',1, 'L', false );
	    }
	    $this->Cell(65,5,"", 'L,R',1,'C', false);
	    if (isset($tin) && !empty($tin)) {
	    	$this->MultiCell(65, 5, $this->data['text_tin_no']."".$tin, 'L,R', 1, 'L', false );
	    }
	    if (isset($telephone) && !empty($telephone)) {
	    	$this->MultiCell(65, 5, $this->data['text_telephone']."".$telephone, 'L,R', 1, 'L', false );
	    }
	    // Font
	    // Output text in a 6 cm width column
	    if ($this->col != 2) {
	    	$this->dynamic_y = $this->GetY();
		    $this->Ln();
		    $this->SetCol();
	    }else {
	    	$this->MultiCell(65, ((float)$this->dynamic_y-(float)$this->GetY()),"",'L,R', 1, 'L',false  );
	    	$this->Line(10 ,$this->GetY(), 205, $this->GetY());
	    	$this->Ln(2);
	    	$this->address_y = $this->GetY();
	    	$this->SetY($this->GetY());
	    	$this->SetX(10);
	    }
	}
	public function product_mini()
	{
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(255,255,255);
		$this->Cell(10,6,"S#",1,0,'L',false);
		$this->Cell(60,6,$this->data['column_product'],1,0,'L',false);
		$this->Cell(35,6,$this->data['column_model'],1,0,'L',false);
		$this->Cell(15,6,$this->data['column_sets'],1,0,'L',false);
		$this->Cell(20,6,$this->data['column_pieces'],1,0,'L',false);
		$this->Cell(25,6,$this->data['column_price'],1,0,'L',false);
		$this->Cell(30,6,$this->data['column_total'],1,1,'L',false);
		// Save ordinate

		$this->y0 = $this->GetY();
		$this->SetY($this->GetY());
		$this->SetX(10);


	}
	public function product_data()
	{
		$this->SetFont('Arial','',10);

		$i =1;
		// $this->SetFillColor(200,220,255);

		foreach ($this->data['orders']['product'] as $key => $value) {
			$this->SetX(10);


			$this->MultiCell(10,5,$i, 0,1, 'L', false);
			$this->SetXY(20,$this->y0);
			
			if (!empty($value['option'])) {
				$str = '';
				foreach ($value['option'] as $option) {
					$str .= "-".$option['name'].":".$option['value']."\n";
				}
				$this->MultiCell(60,5,$value['name']."\n".$str, 'L,R',1, 'L', false);
			} else {
				$this->MultiCell(60,5,$value['name'],'L,R',1, 'L', false);			
			}
			$this->Line(10 ,$this->y0, 10,  $this->GetY());
			$this->dynamic_y = $this->GetY();


			$this->SetXY(80,$this->y0);
			// $this->Cell(5,5,'herre');
			$this->MultiCell(35,5,$value['model'], 'L,R',1, 'L', false);
			$this->SetX(80);
			$this->MultiCell(35, ((float)$this->dynamic_y-(float)$this->GetY()),"",'L,R',1, 'L',false );
			$this->Line(10 ,$this->y0, 10,  $this->GetY());
			$this->dynamic_y = $this->GetY();


			$this->SetXY(115,$this->y0);			
			$this->MultiCell(15,5,$value['quantity'], 'L,R',1, 'L', false);
			$this->SetX(115);
			$this->MultiCell(15, ((float)$this->dynamic_y-(float)$this->GetY()),"",'L,R',1, 'L',false );
			$this->Line(10 ,$this->y0, 10,  $this->GetY());
			$this->dynamic_y = $this->GetY();


			$this->SetXY(130,$this->y0);
			$this->MultiCell(20,5,$value['total_pieces'], 'L,R',1, 'R', false);
			$this->SetX(130);
			$this->MultiCell(20, ((float)$this->dynamic_y-(float)$this->GetY()),"",'L,R',1, 'R',false );
			$this->Line(10 ,$this->y0, 10,  $this->GetY());
			$this->dynamic_y = $this->GetY();


			$this->SetXY(150,$this->y0);
			$this->MultiCell(25,5,$value['price_per_piece'], 'L,R',1, 'R', false);
			$this->SetX(150);
			$this->MultiCell(25, ((float)$this->dynamic_y-(float)$this->GetY()),"",'L,R',1, 'R',false );
			$this->Line(10 ,$this->y0, 10,  $this->GetY());
			$this->dynamic_y = $this->GetY();

			// echo $this->dynamic_y. "<br>";

			$this->SetXY(175,$this->y0);
			$this->MultiCell(30,5,$value['total'], 'L,R',1, 'R', false);
			$this->SetX(175);			
			$this->MultiCell(30, ((float)$this->dynamic_y-(float)$this->GetY()),"",'L,R',1, 'R',false );

			// echo $this->GetY();
			$this->rect(10,$this->GetY(),195,1);
			$this->Ln(0.5);
			$this->dynamic_y = $this->GetY();
			$this->SetX(10);
			$this->y0 = $this->dynamic_y;

		// $this->SetXY($this->GetX(),$this->y0);
			// $this->MultiCell(15,5,$value['quantity'], 1,1, 'L', true);
			// $this->SetXY($this->GetX(),$this->y0);
			// $this->MultiCell(20,5,$value['total_pieces'], 1,1, 'L', false);
			

				// if (!is_array($pv)) {
				// 	$this->MultiCell(20,5,$pv,0,0,'L',true);
				// }
				// echo $pv; die;
			$i++;
		}

	}
	public function OrderTotals()
	{
		$this->SetFont('Arial','B',10);
		$this->SetX(10);
		$this->Cell(120,5,$this->data['text_total_pieces'], 1,0, 'R', false);
		$this->Cell(20,5,$this->data['orders']['total_pieces_order'], 1,1, 'R', false);
		$this->SetX(10);

		foreach ($this->data['orders']['total'] as $key => $value) {
			if ($value['code'] == 'tax' and ($this->data['orders']['cform_submit'] != 'no_submit')) {
				$this->Cell(165, 5, $this->data['text_cst'], 1,0, 'R', false);
				$this->Cell(30, 5, $this->data['orders']['cst_with_cform'], 1,1, 'R', false);
				$this->SetX(10);
				if ($this->data['orders']['cform_submit'] == 'will_submit') {
					$this->Cell(165, 5, $this->data['text_tax_refund'], 1,0, 'R', false);
					$this->Cell(30, 5, $this->data['orders']['refundable_cform'], 1,1, 'R', false);
					$this->SetX(10);
				}
			}  else {
				$this->Cell(165, 5, $value['title'], 1,0, 'R', false);
				$this->Cell(30, 5, $value['text'], 1,1, 'R', false);
				$this->SetX(10);				
			}

		}

	}
	public function additionalInfo()
	{
		$this->SetFont('Arial', '', 8);
		$this->Ln(2);
		$this->SetX(10);
		$this->Cell(0,5,$this->data['text_dupatta_taxfree'], 0,1, 'L', false);
		$this->SetX(10);
		$this->Cell(0,5,$this->data['text_additional_octroi'], 0,1, 'L', false);
		if (!($this->data['orders']['shipping_tin_no'] or $this->data['orders']['payment_tin_no']) and $this->data['orders']['wayBillReqd']) {
		$this->Cell(0, 5, $this->data['text_customer_declaration'], 0,1,'C' ,false);
		// $text_pre_declaration . $order['shipping_name'] . $text_post_declaration ; 
		}
		$this->Ln(2);
		$this->SetX(10);

		$this->SetFont('Arial', 'B', 10);
		$this->Cell(0, 5, $this->data['text_signature'], 0,1,'C' ,false);

	}

	function PrintChapter($order_no, $order_code, $invoice_date)
	{
	    // Add chapter
	    $this->AddPage();
	    $this->invoice_title($order_no, $order_code, $invoice_date);
	    $this->invoice_mini();
	    $this->invoice_address($this->data['orders']['shipping_address'],$this->data['orders']['shipping_tin_no'],$this->data['orders']['telephone']);
	    $this->invoice_address($this->data['orders']['payment_address'],$this->data['orders']['payment_tin_no'],$this->data['orders']['telephone']);
	    $store_tin = $this->data['text_seller_tin_no']." 08195900085" ;
	    $helpline = $this->data['text_helpline'].":"."(+91) 141-4049163";
	    $whatsapp = $this->data['text_whatsapp']."+918696491521";
	    $this->invoice_address(array(
	    	$this->data['orders']['store_name'],
	    	$this->data['orders']['store_address'],
	    	$this->data['orders']['store_email'],
	    	$store_tin,
	    	$helpline
	    	)
	    );
	    $this->product_mini();
	    $this->product_data();
	    $this->OrderTotals();
	    $this->additionalInfo();

	}

}

?>