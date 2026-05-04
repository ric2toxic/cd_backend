<?php //Generate HTML structure 
$div_id = '';
if ($this->_cancelled_flag) {
    $backimg = DIR_SYSTEM . 'library/image/cancelled.png';
    $div_id = 'backimg="'.$backimg.'" backimgw="60%"';
}
?>
<page <?php echo $div_id;?> >
<h1 align="center"><?php echo $pdf_data['buyer_data']['company']; ?></h1>
<h3 align="center">
    <?php echo  $pdf_data['buyer_data']['address1'] . ', ' . $pdf_data['buyer_data']['address2'] . ', ' . $pdf_data['buyer_data']['city'] . ', Pin: ' . $pdf_data['buyer_data']['pincode']; ?> 
</h3>
<?php if (isset($pdf_data['buyer_data']['gst_number'])) { ?>
    <h4 align="center">GSTIN: <?php echo $pdf_data['buyer_data']['gst_number'];?></h4>
<?php } ?>
<?php
    $total_dn_val = (float)$pdf_data['debit_note_amount'];
    $tax_rate     = (float)$pdf_data['sac_tax_rate'];
    $amt_exclude_tax = round($total_dn_val / (1 + $tax_rate / 100), 2);
    $total_tax_value = $total_dn_val - $amt_exclude_tax;
?>
<h2 align="center"><u>DEBIT NOTE</u></h2>
<table style="width:100%;" border="1" cellspacing="" cellpadding="4">
    <tr> 
        <td style="width:50%;" rowspan="2">
          Document No. : <b><?php echo $pdf_data['debit_note_prefix'] . $pdf_data['debit_note_no'];?></b>
          <br />
          Date of Issue :<b><?php echo $pdf_data['date_added']; ?></b><br />
          City : <?php echo $pdf_data['buyer_data']['city']; ?> <br />
          State : <?php echo $pdf_data['buyer_data']['state']; ?> <br />
          Pin Code : <?php echo $pdf_data['buyer_data']['pincode']; ?> <br />
          <span style="text-align:right;">State Code :
            <b><?php echo $gst_state_code[$pdf_data['buyer_data']['zone_id']]; ?></b>
          </span>
        </td>
        <td style="width:50%;">
            Against Invoice/Bill of Supply No.: 
                <b><?php echo !empty($pdf_data['custom_party_meta']['invoice_ref'])? $pdf_data['custom_party_meta']['invoice_ref'] : 0; ?></b>
        </td>
    </tr>
    <tr>
        <td style="width:50%;">
            Ref. P.O. :  <?php echo $pdf_data['order_no']; ?>
        </td>
    </tr>
    <tr>
        <td align="center" colspan="2">
            <b>Details of Receiver | Billed to:</b>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>Name :</b><?php echo $pdf_data['custom_party_meta']['firm_name']; ?><br />
            <b>Address :</b><?php echo $pdf_data['custom_party_meta']['address1'] . ',' . $pdf_data['custom_party_meta']['address2']; ?><br />
            <b>GSTIN :</b><?php echo $pdf_data['custom_party_meta']['gst_number']; ?><br />
            <b>City :</b><?php echo $pdf_data['custom_party_meta']['city']; ?><br />
            <b>State :</b><?php echo $pdf_data['custom_party_meta']['state']; ?><br />  
            <b>Pin Code :</b> <?php echo $pdf_data['custom_party_meta']['pincode']; ?><br>
            <span class="pull-right">State Code : 
               <b><?php echo $gst_state_code[$pdf_data['custom_party_meta']['zone_id']]; ?></b>
            </span>
        </td>
    </tr>
</table><br /><br />
<table style="width:100%;" border="1" cellspacing="" cellpadding="4">
    <tr align="center">
        <th style="width:70%;"><font size="-1">Particulars</font></th>
        <th style="width:30%;"><font size="-1">Amount</font></th>
    </tr>
    <tr>
        <td style="width:70%;"><b>Goods Lost Claim</b></td>
        <td style="width:30%;" align="right"><font size="-1"><?php echo $amt_exclude_tax; ?></font></td>
    </tr>
    <?php $cgst = $sgst = $igst = '-'; ?>
        
    <?php //Check credit note is intra state or inter state ?>
    <?php 
        if ($gst_state_code[$pdf_data['custom_party_meta']['zone_id']] == $gst_state_code[$pdf_data['buyer_data']['zone_id']]) { 
            $cgst = $sgst = number_format((float) $total_tax_value / 2, 2, '.', '');
        } else {
            $igst = number_format((float) $total_tax_value, 2, '.', '');
        }
    ?>
    <tr align="right">
        <td style="width:70%;"><b><font size="-1">Total Taxable Value (a)</font></b> </td>
        <td style="width:30%;"><b><font size="-1">
            <?php echo $this->_currency->format((float)$amt_exclude_tax); ?></font></b>
        </td>
    </tr>
    <tr align="right">
        <td style="width:70%;"><b><font size="-1">Add: CGST (b)</font></b> </td>
        <td style="width:30%;"><b><font size="-1">
            <?php echo $this->_currency->format((float)$cgst); ?></font></b>
        </td>
    </tr>
    <tr align="right">
        <td style="width:70%;"><b><font size="-1">Add: SGST (c)</font></b> </td>
        <td style="width:30%;">
            <b><font size="-1"><?php echo $this->_currency->format((float)$sgst); ?></font></b>
        </td>
    </tr>
    <tr align="right">
        <td style="width:70%;"><b><font size="-1">Add: IGST (d)</font></b> </td>
        <td style="width:30%;"><b>
            <font size="-1"><?php echo $this->_currency->format((float)$igst); ?></font></b>
        </td>
    </tr>
    <tr align="right">
        <td style="width:70%;"><b><font size="-1">TOTAL GST (b+c+d)</font></b> </td>
        <td style="width:30%;">
            <b><font size="-1"><?php echo $this->_currency->format($total_tax_value); ?></font></b>
        </td>
    </tr>
    <tr align="right">
        <td style="width:70%;"><b><font size="-1">TOTAL Amount(inc. Tax) (a+b+c+d)</font></b> </td>
        <td style="width:30%;">
            <b><font size="-1">
                <?php echo $this->_currency->format((float)$amt_exclude_tax + (float)$total_tax_value); ?>
            </font></b>
        </td>
    </tr>
    <tr align="center">
        <td colspan="2" style="width:100%;"><b>
            <font size="-1">
                Total Amount (in Words) :<?php echo convert_to_currency_indian_format((float)$amt_exclude_tax + (float)$total_tax_value); ?>
            </font></b>
        </td>
    </tr>
</table><br /><br />
<table style="width:100%;" border="1" cellspacing="" cellpadding="4">
    <tr align="center" >
        <th style="width:30%;"><font size="-1">HSN/SAC</font></th>
        <th style="width:20%;"><font size="-1">Taxable Value</font></th>
        <th style="width:20%;"><font size="-1">Taxable Rate</font></th>
        <th style="width:10%;"><font size="-1">CGST</font></th>
        <th style="width:10%;"><font size="-1">SGST</font></th>
        <th style="width:10%;"><font size="-1">IGST</font></th>
    </tr>
    <?php //Grouping products on HSN code bases ?>
    <tr>
        <td><font size="-1"><?php echo $pdf_data['sac_code']; ?></font></td>
        <td>
            <font size="-1"><?php echo number_format((float) $amt_exclude_tax, 2, '.', ''); ?></font>
        </td>
        <td>
            <font size="-1">
            <?php echo number_format((float) $pdf_data['sac_tax_rate'], 2, '.', ''); ?>%
            </font>
        </td>
        <td><font size="-1"><?php echo $cgst; ?></font></td>
        <td><font size="-1"><?php echo $sgst; ?></font></td>
        <td><font size="-1"><?php echo $igst; ?></font></td>
    </tr>
    <tr>
        <td align="center"><b><font size="-1">Total</font></b></td>
        <td><font size="-1"><?php echo $this->_currency->format($amt_exclude_tax); ?></font></td>
        <td>-</td>
        <td><font size="-1"><?php echo $this->_currency->format($cgst); ?></font></td>
        <td><font size="-1"><?php echo $this->_currency->format($sgst); ?></font></td>
        <td><font size="-1"><?php echo$this->_currency->format($igst); ?></font></td>
    </tr>
</table><br /><br />
<table style="width:100%;" border="1" cellspacing="" cellpadding="4">
    <tr align="center"> 
        <td align="center" style="width:100%;"><font size="-1">
            For <?php echo $pdf_data['buyer_data']['company']; ?>)<br /><br /><br />
            Authorised Signatory </font>
        </td>
    </tr>
</table>
</page>