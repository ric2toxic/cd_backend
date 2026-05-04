<?php 
$total_tax_value = 0.0;
$total_taxable_value = 0.0;
$all_hsn_codes = array();
$gst_state_code = array();
?>
<?php //Generate HTML structure 
$div_id = '';
if ($this->_cancelled_flag) { //Set Cancelled tag
    $backimg = DIR_SYSTEM . 'library/image/cancelled.png';
    $div_id = 'backimg="'.$backimg.'" backimgw="60%"';
}
?>
<page <?php echo $div_id;?> >
<h1 align="center"><?php echo $pdf_data['buyer_data']['company']; ?></h1>
<h3 align="center">
    <?php echo $pdf_data['buyer_data']['address1'] . ', ' . $pdf_data['buyer_data']['address2'] . ', ' . $pdf_data['buyer_data']['city'] . ', Pin: ' . $pdf_data['buyer_data']['pincode']; ?>
</h3>
<?php if (isset($pdf_data['buyer_data']['tin'])) { ?>
<h4 align="center">GSTIN: <?php echo $pdf_data['buyer_data']['tin']; ?></h4>
<?php } ?>
<h2 align="center"><u>REPLACEMENT CHALLAN</u></h2>
<table style="width:100%;" border="1" cellspacing="" cellpadding="4">
    <tr> 
        <td style="width:50%;" rowspan="3">
            Document No. : <b>
            <?php echo $this->_replacement_note_prefix . $pdf_data['replacement_note_info']['replacement_note_no']; ?></b>
            <br />
            Date of Issue :<b> <?php echo $pdf_data['replacement_note_info']['replacement_note_date']; ?></b><br />
            City : <?php echo $pdf_data['buyer_data']['city']; ?>, <br />
            State : <?php echo $pdf_data['buyer_data']['state']; ?>, <br />
            Pin Code : <?php echo $pdf_data['buyer_data']['pincode']; ?> 
            <br />
            <span style="text-align:right;">
                State Code : 
                <b><?php echo $pdf_data['buyer_data']['state_code'][0]['gst_state_code']; ?></b>
            </span>
        </td>
        <td>
            Against Invoice/Bill of Supply No.: 
            <?php echo $pdf_data['invoice_prefix'] . $pdf_data['invoice_no']; ?>
        </td>
    </tr>
    <tr>
        <td>
            Date of Invoice/Bill of Supply: 
            <?php echo date('d-m-Y', strtotime($pdf_data['invoice_date'])); ?>
        </td>
    </tr>
    <tr>
        <td>Ref. P.O. :  <?php echo $pdf_data['replacement_note_info']['order_no']; ?></td>
    </tr>
    <tr>
        <td style="width:50%;" align="center"><b>Details of Receiver | Billed to:</b></td>
        <td style="width:50%;" align="center"><b>Details of Consignee | Shipped to:</b></td>
    </tr>
    <tr>
        <td style="width:50%;">
            <b>Name :</b> <?php echo $pdf_data['seller_address']['company']; ?>
            <?php if (isset($pdf_data['seller_address']['nickname'])) { ?>
                ( <?php echo $pdf_data['seller_address']['nickname']; ?> )
            <?php } ?>
            <br />
            <b>Address :</b> 
            <?php echo $pdf_data['seller_address']['address1'].','.$pdf_data['seller_address']['address2']; ?>
            <br />
            <b>GSTIN :</b> <?php echo $pdf_data['seller_address']['tin']; ?><br />
            <b>City :</b> <?php echo $pdf_data['seller_address']['city']; ?>, <br />
            <b>State :</b> <?php echo $pdf_data['seller_address']['state']; ?>, <br />  
            <b>Pin Code :</b> <?php echo $pdf_data['seller_address']['pincode']; ?> 
            <br />
            <span style="text-align:right;">
                State Code : 
                <b><?php echo $pdf_data['seller_address']['state_code'][0]['gst_state_code']; ?></b>
            </span>
        </td>
        <td style="width:50%;">
            <b>Name :</b> <?php echo $pdf_data['seller_address']['company'] ?>
            <?php if (isset($pdf_data['seller_address']['nickname'])) { ?>
            ( <?php echo $pdf_data['seller_address']['nickname'];?> )
            <?php }?>
            <br />
            <b>Address :</b> 
            <?php echo $pdf_data['seller_address']['address1'] . ',' . $pdf_data['seller_address']['address2']; ?><br />
            <b>GSTIN :</b> <?php echo $pdf_data['seller_address']['tin']; ?><br />
            <b>City :</b> <?php echo $pdf_data['seller_address']['city']; ?>, <br />
            <b>State :</b> <?php echo $pdf_data['seller_address']['state']; ?>, <br />   
            <b>Pin Code :</b> <?php echo $pdf_data['seller_address']['pincode']; ?><br />
            <span style="text-align:right;">State Code : 
                <b><?php echo $pdf_data['seller_address']['state_code'][0]['gst_state_code']; ?></b>
            </span>
        </td>
    </tr>
</table>
<br /><br />
<table style="width:100%;" border="1" cellspacing="" cellpadding="4">
    <tr align="center">
        <th style="width:4%;"><font size="-1">S. No.</font></th>
        <th style="width:27%;"><font size="-1">Description Of Goods</font></th>
        <th style="width:10%;"><font size="-1">HSN/SAC</font></th>
        <th style="width:5%;"><font size="-1">Qty</font></th>
        <th style="width:7%;"><font size="-1">Rate</font></th>
        <th style="width:8%;"><font size="-1">Amount</font></th>
        <th style="width:8%;"><font size="-1">Less: Discount</font></th>
        <th style="width:8%;"><font size="-1">Taxable Rate</font></th>
        <th style="width:11%;"><font size="-1">Taxable Value</font></th>
        <th style="width:12%;"><font size="-1">Remarks / Comments</font></th>
    </tr>
    <?php
        $i = 1;
        $order_product_data = array();
        $calculated_data = array();
        $noOfPieces = 0;
        foreach ($return_calculated_data['table_product_data']['product_data'] as $products) 
        {
            $order_product_data[$products['order_product_id']] = $products;
        }
        foreach ($pdf_data['product_data'] as $return_product) {
            $order_product_id = $return_product['order_product_id'];
            $return_id = $return_product['return_id'];
            $product_details = $order_product_data[$order_product_id];
            $product_option = !empty($product_details['option_name']) ? ' - ' . $product_details['option_name'] . ' : ' . $product_details['option_value'] : '';
            $seller_tax = (float)$product_details['seller_tax'];
            $transfer_price_per_piece = (float)$product_details['transfer_price_per_piece'];
            $rate_per_piece = round($transfer_price_per_piece / (1 + $seller_tax / 100), 2);
            $tax_piece = round($transfer_price_per_piece - $rate_per_piece, 2);
            $tax_value = $tax_piece * $return_product['product_quantity'];
            $amount = $rate_per_piece * $return_product['product_quantity'];
            $product_details['taxable_value'] = $amount - $product_details['discount'];
            $product_details['tax_value'] = $tax_value;
            $product_details['amount'] = $amount;
            $all_hsn_codes[] = $product_details['hsn_code'];
            $calculated_data[$return_id] = $product_details;
            $noOfPieces += (int)$return_product['product_quantity'];
    ?>
    <tr>
        <td style="width:4%;"><font size="-1"><?php echo $i; ?></font></td>
        <td style="width:27%;"><b>
            <font size="-1">
            <?php echo $product_details['product_sku']; ?>
            </font></b><br />
            <i><font size="-2"><?php echo $product_details['product_name'] . $product_option; ?></font></i>
        </td>
        <td style="width:10%;"><font size="-1"><?php echo $product_details['hsn_code']; ?></font></td>
        <td style="width:5%;"><font size="-1"><?php echo $return_product['product_quantity']; ?></font></td>
        <td style="width:7%;"><font size="-1"><?php echo $rate_per_piece; ?></font></td>
        <td style="width:8%;"><font size="-1"><?php echo $amount; ?></font></td>
        <?php //For now discount is 0 ?>
        <td style="width:8%;">
            <font size="-1"><?php echo number_format($product_details['discount'], 2, '.', ''); ?></font>
        </td>
        <?php $total_tax_value += $tax_value; ?>
        <td style="width:8%;">
            <font size="-1"><?php echo (float)$product_details['seller_tax']; ?></font>
        </td>
        <?php $taxable_value = $amount - $product_details['discount']; ?>
        <?php $total_taxable_value += $taxable_value; ?>
        <td style="width:11%;">
            <font size="-1"><?php echo number_format((float)$taxable_value, 2, '.', ''); ?></font>
        </td>
        <td style="width:12%;"><font size="-1"><?php echo $return_product['name']; ?></font></td>
    </tr>
    <?php 
          $i++; 
        } 
        $all_hsn_codes = array_unique($all_hsn_codes);
        $cgst = '-';
        $sgst = '-';
        $igst = '-';

        //Check credit note is intra state or inter state 
        if ($pdf_data['seller_address']['state_code'][0]['gst_state_code'] == $pdf_data['buyer_data']['state_code'][0]['gst_state_code']) {
            $cgst = $sgst = number_format((float) $total_tax_value / 2, 2, '.', '');
        } else {
            $igst = number_format((float) $total_tax_value, 2, '.', '');
        }
    ?>
    <tr align="right">
        <td colspan="3" align="right"><b><font size="-1">Total Qty</font></b> </td>
        <td colspan="7" align="left"><b><font size="-1">
          <?php echo $noOfPieces ?></font></b>
        </td>
    </tr>
    <tr align="right">
        <td colspan="8"><b><font size="-1">Total Taxable Value (a)</font></b> </td>
        <td colspan="2"><b><font size="-1">
          <?php echo $this->_currency->format((float)$total_taxable_value); ?></font></b>
        </td>
    </tr>
    <tr align="right">
        <td colspan="8"><b><font size="-1">Add: CGST (b)</font></b> </td>
        <td colspan="2"><b><font size="-1">
            <?php echo $this->_currency->format((float)$cgst); ?></font></b>
        </td>
    </tr>
    <tr align="right">
        <td colspan="8"><b><font size="-1">Add: SGST (c)</font></b> </td>
        <td colspan="2"><b><font size="-1">
            <?php echo $this->_currency->format((float)$sgst); ?></font></b>
        </td>
        </tr>
        <tr align="right">
        <td colspan="8"><b><font size="-1">Add: IGST (d)</font></b> </td>
        <td colspan="2"><b><font size="-1">
            <?php echo $this->_currency->format((float)$igst); ?></font></b>
        </td>
    </tr>
    <tr align="right">
        <td colspan="8"><b><font size="-1">TOTAL GST (b+c+d)</font></b> </td>
        <td colspan="2"><b><font size="-1">
            <?php echo $this->_currency->format($total_tax_value); ?></font></b>
        </td>
    </tr>
    <tr align="right">
        <td colspan="8"><b>
            <font size="-1">TOTAL Amount(inc. Tax) (a+b+c+d)</font></b> 
        </td>
        <td colspan="2"><b>
            <font size="-1">
            <?php echo $this->_currency->format((float)$total_taxable_value + (float)$total_tax_value); ?>
            </font></b>
        </td>
    </tr>
    <tr align="center">
        <td colspan="10"><b>
            <font size="-1">
                Total Amount (in Words) : 
                <?php echo convert_to_currency_indian_format((float)$total_taxable_value + (float)$total_tax_value); ?>
            </font></b>
        </td>
    </tr>
</table>
<br /><br />
<?php   //Update debit_note_amount in oc_debit_note table ?>
<?php $this->updateReplacementNoteAmount($pdf_data['replacement_note_info']['replacement_note_id'], ((float)$total_taxable_value + (float)$total_tax_value)); ?>
<table style="width:100%;" border="1" cellspacing="" cellpadding="4">
    <tr align="center" >
        <th style="width:30%;"><font size="-1">HSN/SAC</font></th>
        <th style="width:20%;"><font size="-1">Taxable Value</font></th>
        <th style="width:20%;"><font size="-1">Taxable Rate</font></th>
        <th style="width:10%;"><font size="-1">CGST</font></th>
        <th style="width:10%;"><font size="-1">SGST</font></th>
        <th style="width:10%;"><font size="-1">IGST</font></th>
    </tr>
    <?php
        $hsn_cgst = 0.0;
        $hsn_sgst = 0.0;
        $hsn_igst = 0.0;

        //Grouping products on HSN code bases
        $groupHsnCodeWiseProducts = $this->summrizeProductsForHsnCode($calculated_data, $all_hsn_codes);

        $total_hsn_taxable_value = 0.0;
        $total_hsn_cgst = 0.0;
        $total_hsn_sgst = 0.0;
        $total_hsn_igst = 0.0;
        //Summrizing on HSN/SAC code bases
        foreach ($groupHsnCodeWiseProducts as $hsn_code => $hsnProducts) {
            foreach ($hsnProducts as $tax_rate => $hsnProduct) {
                if(isset($pdf_data['custom_party_meta']) && isset($gst_state_code[$pdf_data['custom_party_meta']['zone_id']])){
                    //Check credit note is intra state or inter state
                    if ($gst_state_code[$pdf_data['custom_party_meta']['zone_id']] == $pdf_data['buyer_data']['state_code'][0]['gst_state_code']) {
                      $hsn_cgst = $hsn_sgst = number_format((float) $hsnProduct['tax_value'] / 2, 2, '.', '');
                    } else {
                      $hsn_igst = number_format((float) $hsnProduct['tax_value'], 2, '.', '');
                    }
                }else{
                    //Check credit note is intra state or inter state
                    if ($pdf_data['seller_address']['state_code'][0]['gst_state_code'] == $pdf_data['buyer_data']['state_code'][0]['gst_state_code']) {
                      $hsn_cgst = $hsn_sgst = number_format((float) $hsnProduct['tax_value'] / 2, 2, '.', '');
                    } else {
                      $hsn_igst = number_format((float) $hsnProduct['tax_value'], 2, '.', '');
                    }  
                }

                $total_hsn_taxable_value += (float)$hsnProduct['taxable_value'];
                $total_hsn_cgst += (float)$hsn_cgst;
                $total_hsn_sgst += (float)$hsn_sgst;
                $total_hsn_igst += (float)$hsn_igst;
    ?>
    <tr style="width:100%;">
        <td style="width:30%;"><font size="-1"><?php echo $hsn_code; ?></font></td>
        <td style="width:20%;"><font size="-1">
            <?php echo number_format((float) $hsnProduct['taxable_value'], 2, '.', ''); ?></font>
        </td>
        <td style="width:20%;"><font size="-1">
            <?php echo number_format((float) $hsnProduct['taxable_rate'], 2, '.', ''); ?>%</font>
        </td>
        <td style="width:10%;"><font size="-1"><?php echo $hsn_cgst; ?></font></td>
        <td style="width:10%;"><font size="-1"><?php echo $hsn_sgst; ?></font></td>
        <td style="width:10%;"><font size="-1"><?php echo $hsn_igst; ?></font></td>
    </tr>
    <?php } } ?>
    <tr style="width:100%;">
        <td style="width:30%;" align="center"><b><font size="-1">Total</font></b></td>
        <td style="width:20%;"><font size="-1">
            <?php echo $this->_currency->format($total_hsn_taxable_value); ?></font>
        </td>
        <td style="width:20%;">-</td>
        <td style="width:10%;">
            <font size="-1"><?php echo $this->_currency->format($total_hsn_cgst); ?></font>
        </td>
        <td style="width:10%;">
            <font size="-1"><?php echo $this->_currency->format($total_hsn_sgst); ?></font>
        </td>
        <td style="width:10%;">
            <font size="-1"><?php echo $this->_currency->format($total_hsn_igst); ?></font>
        </td>
    </tr>
</table><br /><br />
<table style="width:100%;" border="1" cellspacing="" cellpadding="4">
    <tr>
        <td style="width:50%;" align="center"><font size="-1">
            For (<?php echo $pdf_data['buyer_data']['company']; ?>)<br /><br /><br />
                            Authorised Signatory </font>
        </td>
        <td style="width:50%;" valign="middle"><font size="-1">Terms & Conditions</font></td>
    </tr>
</table>
</page>