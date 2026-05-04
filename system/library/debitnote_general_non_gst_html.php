<?php //Generate HTML structure 
$div_id = '';
if ($this->_cancelled_flag) {
    $backimg = DIR_SYSTEM . 'library/image/cancelled.png';
    $div_id = 'backimg="'.$backimg.'" backimgw="60%"';
}
?>
<page <?php echo $div_id;?> >
<h2 align="center"><u>DEBIT NOTE</u></h2>
<table border="1" cellspacing="" cellpadding="5" width="100%">
    <tr>
        <td colspan="4" rowspan="4">
            <b><?php echo $pdf_data['buyer_data']['company']; ?></b><br>
            <?php $buyer_address = ""; 
                if (!empty($pdf_data['buyer_data']['address1'])) {
                    $buyer_address .= $pdf_data['buyer_data']['address1'];
                }
                if (!empty($pdf_data['buyer_data']['address2'])) {
                    $buyer_address .= !empty($buyer_address) ? ", <br>" . $pdf_data['buyer_data']['address2'] : $pdf_data['buyer_data']['address2'];
                }
                if (!empty($pdf_data['buyer_data']['address'])) {
                    $buyer_address = $pdf_data['buyer_data']['address'];
                }
            ?>
            <?php echo $buyer_address; ?>,<br>
            <?php echo $pdf_data['buyer_data']['city'] . ' - ' . $pdf_data['buyer_data']['pincode']; ?><br>
            <?php echo $pdf_data['buyer_data']['state'] . ' - ' . $pdf_data['buyer_data']['country']; ?><br>
            E-MAIL: <?php echo @$pdf_data['buyer_data']['email']; ?>
        </td>
        <td colspan="4" width="50%"> Debit Note No.: 
            <b><?php echo $this->_debit_note_prefix . $pdf_data['debit_note_info']['debit_note_no']; ?></b> 
        </td>
    </tr>
    <tr>
        <td colspan="4"> Dated: 
            <b><?php echo $pdf_data['debit_note_info']['debit_note_date']; ?></b>
        </td>
    </tr>
    <tr>
        <td colspan="4"> Supplier(s) Ref. : 
             <?php echo $pdf_data['seller_address']['nickname']; ?>
        </td>
    </tr>
    <tr>
        <td colspan="4"> Other Reference(s) : 
            <b><?php echo $pdf_data['debit_note_info']['order_no']; ?></b>
        </td>
    </tr>
    <tr>
        <td colspan="8"> Party : </td>
    </tr>
    <tr>
        <td colspan="8">
            <b><?php echo $pdf_data['seller_address']['company']; ?></b><br>
            <?php echo $pdf_data['seller_address']['address1']; ?><br>
            <?php echo $pdf_data['seller_address']['address2']; ?><br>
            <?php echo $pdf_data['seller_address']['city'] . '-' .
                   $pdf_data['seller_address']['pincode']; ?><br>
            <?php echo $pdf_data['seller_address']['state'] . ' - ' . $pdf_data['seller_address']['country']; ?><br>
            E-MAIL: <?php echo @$pdf_data['seller_address']['email']; ?>
        </td>
    </tr>
    <tr>
        <td width="3%">Sr NO </td>
        <td width="27%">Description Of Goods </td>
        <td width="10%">Total Piece </td>
        <td width="10%"> Rate / Pcs </td>
        <td width="10%">Rate Tax </td>
        <td width="10%">Tax </td>
        <td width="10%">Amount </td>
        <td width="20%">Remarks/comments </td>
    </tr>
    <?php
        $i = 1;
        
        foreach ($return_calculated_data['table_product_data']['product_data'] as $products) {
    ?>
    <tr>
        <td width="3%"><?php echo $i; ?></td>
        <td width="27%"><?php echo $products['product_name']; ?></td>
        <td width="10%"><?php echo $products['total_pieces']; ?></td>
        <td width="10%">
            <?php echo number_format((float) $products['rate_per_piece'], 2, '.', ''); ?>
        </td>
        <td width="10%"><?php echo $products['seller_tax']; ?></td>
        <td width="10%"><?php echo number_format((float) $products['tax'], 2, '.', ''); ?></td>
        <td width="10%"><?php echo number_format((float) $products['amount'], 2, '.', ''); ?></td>
        <td width="20%">
            <?php echo (!empty($pdf_data['product_data'][$products['order_product_id']]['name'])?$pdf_data['product_data'][$products['order_product_id']]['name'] : ''); ?>
        </td>
    </tr>
    <?php $i++; ?>
    <?php } ?>
    <tr>
        <td colspan="6" align="right"><b>Total </b> </td>
        <td colspan="2" align="left"> <b>
           <?php echo $return_calculated_data['table_product_data']['total_amount']; ?></b>
        </td>
    </tr>
    <tr>
        <td colspan="6" align="right"><b>Tax </b> </td>
        <td colspan="2" align="left"> 
            <b><?php echo $return_calculated_data['table_product_data']['total_tax']; ?></b>
        </td>
    </tr>
    <tr>
        <td colspan="6" align="right"><b>Grand Total </b> </td>
        <td colspan="2" align="left"> 
            <b><?php echo $return_calculated_data['table_product_data']['grand_total']; ?></b>
        </td>
    </tr>
    <tr>
        <td colspan="8" align="center">Amount Chargeable (in words): <?php echo $return_calculated_data['table_product_data']['amount_in_word'];?></td>
    </tr>
    <tr>
        <td colspan="4">
            <table border="0" width="100%">
                <tr>
                    <td>
                       <b><?php echo $return_calculated_data['table_product_data']['amount_in_word']; ?></b>
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td>
                       Company(s) VAT TIN : <b><?php echo (!empty($pdf_data['buyer_data']['tin']) ? $pdf_data['buyer_data']['tin'] : ''); ?></b>
                    </td>
                </tr>
                <tr>
                    <td>Party(s) VAT TIN :  <b><?php echo (!empty($pdf_data['seller_address']['tin']) ? $pdf_data['seller_address']['tin'] : ''); ?></b> 
                    </td>
                </tr>
            </table>
        </td>
        <td colspan="4">
            <table border="0" width="100%">
                <tr>
                    <td align="right">For <?php echo $pdf_data['buyer_data']['company']; ?></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td align="right">Authorised Signatory</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</page>