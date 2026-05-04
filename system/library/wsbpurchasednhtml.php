<?php 
$div_id = '';
if ($this->_cancelled_flag) {
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
<h2 align="center"><u>DEBIT NOTE</u></h2>
<table style="width:100%;" border="1" cellspacing="" cellpadding="4">
    <tr>
        <td style="width:50%;" rowspan="2">
            Document No. : <b><?php echo $this->_debit_note_prefix. $this->_debit_note_year . $this->_debit_note_no; ?></b><br/>
            Date of Issue :<b><?php echo  @$pdf_data['dn_date']; ?></b><br />
            City : <?php echo $pdf_data['buyer_data']['city']; ?>, <br />
            State : <?php echo $pdf_data['buyer_data']['state']; ?>, <br />
            Pin Code : <?php echo $pdf_data['buyer_data']['pincode']; ?><br />
            <span style="text-align:right;">
                State Code : 
                <b><?php echo $pdf_data['buyer_data']['state_code'][0]['gst_state_code']; ?></b>
            </span>
        </td>
        <td style="width:50%;">Against Invoice/Bill of Supply No.: <?php echo $pdf_data['invoice_no']; ?></td>
    </tr>
    <tr>
        <td style="width:50%;">
            Date of Invoice/Bill of Supply: 
            <?php echo date('d-m-Y', strtotime($pdf_data['invoice_date'])); ?>
        </td>
    </tr>
    <tr>
        <td style="width:50%;" align="center"><b>Details of Receiver | Billed to:</b></td>
        <td style="width:50%;" align="center"><b>Details of Consignee | Shipped to:</b></td>
    </tr>
    <tr>
        <td style="width:50%;">
            <b>Name :</b>
            <?php echo  $pdf_data['seller_data']['company']; ?>
            <?php if (isset($pdf_data['seller_data']['nickname'])) { ?>
                (<?php echo $pdf_data['seller_data']['nickname']; ?>)
            <?php } ?><br />
            <b>Address :</b> <?php echo $pdf_data['seller_data']['address1'] . ',' . $pdf_data['seller_data']['address2']; ?><br />
            <b>GSTIN :</b> <?php echo $pdf_data['seller_data']['tin']; ?><br />
            <b>City :</b> <?php echo $pdf_data['seller_data']['city']; ?>, <br />
            <b>State :</b> <?php echo $pdf_data['seller_data']['state']; ?>, <br />  
            <b>Pin Code :</b> <?php echo $pdf_data['seller_data']['pincode']; ?><br />
            <span style="text-align:right;">State Code : <b><?php echo  $pdf_data['seller_data']['state_code'][0]['gst_state_code']; ?></b>
            </span>
        </td>
        <td style="width:50%;"><b>Name :</b> <?php echo $pdf_data['seller_data']['company']; ?>
        <?php if (isset($pdf_data['seller_data']['nickname'])) { ?>
            (<?php echo $pdf_data['seller_data']['nickname']; ?>)
        <?php } ?><br />
            <b>Address :</b> <?php echo $pdf_data['seller_data']['address1'] . ',' . $pdf_data['seller_data']['address2']; ?><br />
            <b>GSTIN :</b> <?php echo $pdf_data['seller_data']['tin']; ?><br />
            <b>City : </b> <?php echo $pdf_data['seller_data']['city']; ?>, <br />
            <b>State :</b> <?php echo $pdf_data['seller_data']['state']; ?>, <br />   
            <b>Pin Code :</b> <?php echo $pdf_data['seller_data']['pincode']; ?><br />
            <span style="text-align:right;">
                State Code : <b> <?php echo $pdf_data['seller_data']['state_code'][0]['gst_state_code']; ?></b>
            </span>
        </td>
    </tr>
</table><br /><br />
<?php
$total_tax_value = 0.0;
$total_taxable_value = 0.0;
$gst_state_code = array();
if(!empty($pdf_data['product'])){
?>
<table style="width:100%;" border="1" cellspacing="" cellpadding="4">
    <thead>
        <tr align="center">
            <th style="width:5%;"><font size="-1">S. No.</font></th>
            <th style="width:30%;"><font size="-1">Description Of Goods</font></th>
            <th style="width:10%;"><font size="-1">HSN/SAC</font></th>
            <th style="width:5%;"><font size="-1">Qty</font></th>
            <th style="width:10%;"><font size="-1">Rate</font></th>
            <th style="width:10%;"><font size="-1">Amount</font></th>
            <th style="width:10%;"><font size="-1">Less: Discount</font></th>
            <th style="width:10%;"><font size="-1">Tax Rate</font></th>
            <th style="width:10%;"><font size="-1">Taxable Value</font></th>
        </tr>
    </thead>
    <tbody>
        <?php        
            $i = 1;
            $noOfPieces = 0;
            foreach ($pdf_data['product'] as $p_id=>$product) {
                $seller_tax = (float)$product['tax'];
                $transfer_price_per_piece = (float)$product['transfer_price'];
                $rate_per_piece = $transfer_price_per_piece / (1 + $seller_tax / 100);
                $pdf_data['product'][$p_id]['rate_per_piece'] = $rate_per_piece;
                $tax_piece = $transfer_price_per_piece - $rate_per_piece;
                $tax_value = $tax_piece * $product['quantity'];
                $product['amount'] = (float)($rate_per_piece * $product['quantity']);
                //Set HSN Code of product
                $this->all_hsn_codes[] = $product['hsn_code'];
                $noOfPieces += (int)$product['quantity'];
        ?>
            <tr style="width:100%;">
                <td style="width:5%;"><font size="-1"><?php echo $i; ?></font></td>
                <td style="width:30%;">
                    <b><font size="-1"><?php echo $product['sku']; ?></font></b><br />
                    <i><font size="-2"><?php echo $product['product_name']; ?></font></i>
                </td>
                <td style="width:10%;"><font size="-1"><?php echo $product['hsn_code']; ?></font></td>
                <td style="width:5%;"><font size="-1"><?php echo $product['quantity']; ?></font></td>
                <td style="width:10%;"><font size="-1"><?php echo round($rate_per_piece, 2); ?></font></td>
                <td style="width:10%;"><font size="-1"><?php echo round($product['amount'], 2); ?></font></td>
                <?php //For now discount is 0 
                    $product['discount'] = 0;
                    $pdf_data['product'][$p_id]['discount'] = 0;
                ?>
                <td style="width:10%;"><font size="-1">
                    <?php echo number_format($product['discount'], 2, '.', ''); ?></font>
                </td>
                <?php $total_tax_value += $tax_value; ?>
                <td style="width:10%;"><font size="-1"><?php echo (float)$product['tax']; ?></font></td>
                <?php 
                    $taxable_value = $product['amount'] - $product['discount'];
                    $total_taxable_value += $taxable_value;
                ?>
                <td style="width:10%;">
                    <font size="-1"><?php echo number_format((float)$taxable_value, 2, '.', ''); ?></font>
                </td>
            </tr>
            <?php
             $i++;
            }
            $this->all_hsn_codes = array_unique($this->all_hsn_codes);
            $cgst = '-';
            $sgst = '-';
            $igst = '-';
            //Check credit note is intra state or inter state
            if ($pdf_data['seller_data']['state_code'][0]['gst_state_code'] == $pdf_data['buyer_data']['state_code'][0]['gst_state_code']) {
                $cgst = $sgst = number_format((float) $total_tax_value / 2, 2, '.', '');
            } else {
                $igst = number_format((float) $total_tax_value, 2, '.', '');
            }
            ?>
            <tr style="width:100%;" align="right">
                <td colspan="3"><b><font size="-1">Total Qty</font></b> </td>
                <td colspan="6" align="left"><b><font size="-1">
                    <?php echo $noOfPieces; ?></font></b>
                </td>
            </tr>
            <tr style="width:100%;" align="right">
                <td colspan="6"><b><font size="-1">Total Taxable Value (a)</font></b> </td>
                <td colspan="3"><b><font size="-1">
                    <?php echo $this->_currency->format((float)$total_taxable_value); ?></font></b>
                </td>
            </tr>
            <tr style="width:100%;" align="right">
                <td colspan="6"><b><font size="-1">Add: CGST (b)</font></b> </td>
                <td colspan="3"><b><font size="-1">
                    <?php echo $this->_currency->format((float)$cgst); ?></font></b>
                </td>
            </tr>
            <tr style="width:100%;" align="right">
                <td colspan="6"><b><font size="-1">Add: SGST (c)</font></b> </td>
                <td colspan="3"><b><font size="-1">
                    <?php echo $this->_currency->format((float)$sgst); ?></font></b>
                </td>
            </tr>
            <tr align="right">
                <td colspan="6"><b><font size="-1">Add: IGST (d)</font></b> </td>
                <td colspan="3"><b><font size="-1">
                    <?php echo $this->_currency->format((float)$igst); ?></font></b>
                </td>
            </tr>
            <tr align="right">
                <td colspan="6"><b><font size="-1">TOTAL GST (b+c+d)</font></b> </td>
                <td colspan="3"><b><font size="-1">
                    <?php echo $this->_currency->format($total_tax_value); ?></font></b>
                </td>
            </tr>
            <tr align="right">
                <td colspan="6"><b><font size="-1">TOTAL Amount(inc. Tax) (a+b+c+d)</font></b> </td>
                <td colspan="3"><b>
                    <font size="-1">
                    <?php echo $this->_currency->format((float)$total_taxable_value + (float)$total_tax_value); ?>
                    </font></b>
                </td>
            </tr>
            <tr align="center">
                <td colspan="9"><b>
                    <font size="-1">Total Amount (in Words) : 
                    <?php echo convert_to_currency_indian_format((float)$total_taxable_value + (float)$total_tax_value); ?>
                    </font></b>
                </td>
            </tr>
    </tbody>
</table><br /><br />
<?php 
$this->dn_amt = (float)$total_taxable_value + (float)$total_tax_value;
}
?>
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
        $groupHsnCodeWiseProducts = $this->summrizeProductsForHsnCode($pdf_data['product']);
        $total_hsn_taxable_value = 0.0;
        $total_hsn_cgst = 0.0;
        $total_hsn_sgst = 0.0;
        $total_hsn_igst = 0.0;
        //Summrizing on HSN/SAC code bases
        foreach ($groupHsnCodeWiseProducts as $hsn_code => $hsnProducts) {
            foreach ($hsnProducts as $tax_rate => $hsnProduct) {
                //Check credit note is intra state or inter state
                if ($pdf_data['seller_data']['state_code'][0]['gst_state_code'] == $pdf_data['buyer_data']['state_code'][0]['gst_state_code']) {
                  $hsn_cgst = $hsn_sgst = number_format((float) $hsnProduct['tax_value'] / 2, 2, '.', '');
                } else {
                  $hsn_igst = number_format((float) $hsnProduct['tax_value'], 2, '.', '');
                }

                $total_hsn_taxable_value += (float)$hsnProduct['taxable_value'];
                $total_hsn_cgst += (float)$hsn_cgst;
                $total_hsn_sgst += (float)$hsn_sgst;
                $total_hsn_igst += (float)$hsn_igst;
    ?>
    <tr>
        <td><font size="-1"><?php echo $hsn_code; ?></font></td>
        <td>
            <font size="-1"><?php echo number_format((float) $hsnProduct['taxable_value'], 2, '.', ''); ?></font>
        </td>
        <td>
            <font size="-1"><?php echo number_format((float) $hsnProduct['taxable_rate'], 2, '.', ''); ?>%</font>
        </td>
        <td><font size="-1"><?php echo $hsn_cgst; ?></font></td>
        <td><font size="-1"><?php echo $hsn_sgst; ?></font></td>
        <td><font size="-1"><?php echo $hsn_igst; ?></font></td>
    </tr>
    <?php
            }
        }
    ?>
    <tr>
        <td align="center"><b><font size="-1">Total</font></b></td>
        <td><font size="-1"><?php echo $this->_currency->format($total_hsn_taxable_value); ?></font></td>
        <td>-</td>
        <td><font size="-1"><?php echo $this->_currency->format($total_hsn_cgst); ?></font></td>
        <td><font size="-1"><?php echo $this->_currency->format($total_hsn_sgst); ?></font></td>
        <td><font size="-1"><?php echo $this->_currency->format($total_hsn_igst); ?></font></td>
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