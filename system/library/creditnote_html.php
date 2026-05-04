<?php
$div_id = '';
if ($this->_cancelled_flag) {
    $backimg = DIR_SYSTEM . 'library/image/cancelled.png';
    $div_id = 'backimg="'.$backimg.'" backimgw="60%"';
}
?>
<page <?php echo $div_id;?> >
<h2 align="center"><u>CREDIT NOTE</u></h2>
<table style="width:90%;" border="1" cellspacing="" cellpadding="4">
    <tbody>
        <tr>
            <td style="width:50%;" rowspan="4">
                <b><?php echo $pdf_data['seller_data']['company']; ?></b><br>
                <?php echo $pdf_data['seller_data']['address1']; ?>, <br>
                <?php echo $pdf_data['seller_data']['address2']; ?>, <br>
                <?php echo $pdf_data['seller_data']['city'].' - '.$pdf_data['seller_data']['pincode']; ?><br>
                <?php echo $pdf_data['seller_data']['state'].' - '.$pdf_data['seller_data']['country']; ?><br>
                E-MAIL: <?php echo @$pdf_data['seller_data']['email']; ?><br>
                Tin No: <?php echo @$pdf_data['seller_data']['tin']; ?>
            </td>
            <td style="width:30%;"> Credit Note No.: <b><?php echo $this->_credit_note_prefix . $pdf_data['credit_note_info']['credit_note_no']; ?></b>
            </td>
        </tr>
        <tr>
            <td style="width:50%;"> Dated: <b>
                <?php echo $pdf_data['credit_note_info']['credit_note_date']; ?></b>
            </td>
        </tr>
        <tr>
            <td> Other Reference(s): 
                <b><?php echo $pdf_data['credit_note_info']['order_no']; ?></b>
            </td>
        </tr>
        <tr>
            <td>
                Suborder No: <b><?php echo $this->_suborder_id; ?></b>
            </td>
        </tr>
        <tr>
            <td style="width:50%;"> Party : </td>
            <td style="width:30%;">
                <b><?php echo $pdf_data['buyer_data']['shipping_company']; ?></b><br>
                <?php echo $pdf_data['buyer_data']['shipping_address_1']; ?><br>
                <?php echo $pdf_data['buyer_data']['shipping_address_2']; ?><br>
                <?php echo $pdf_data['buyer_data']['shipping_city'] . '-' .
                $pdf_data['buyer_data']['shipping_postcode']; ?><br>
                <?php echo $pdf_data['buyer_data']['shipping_zone'] . ' - ' . $pdf_data['buyer_data']['shipping_country']; ?><br>
                E-MAIL: <?php echo $pdf_data['buyer_data']['email']; ?><br>
                Tin No: <?php echo $pdf_data['buyer_data']['tin']; ?>
            </td>
        </tr>
    </tbody>
</table> <br><br>
<table style="width:100%;" border="1" cellspacing="" cellpadding="4" >
    <tbody>
        <tr style="width:100%;">
            <td style="width:6%;">S. No.</td>
            <td style="width:40%;">Description Of Goods </td>
            <td style="width:10%;">Total Piece </td>
            <td style="width:10%;"> Rate / Pc </td>
            <td style="width:10%;">Dis. /Pc </td>
            <td style="width:9%;">Tax </td>
            <td style="width:15%;">Amount </td>
        </tr>
        <?php
        $i = 1;
        $quantity = 0;
        $product_info = $pdf_data["return_order_product_id"];
        $products = $pdf_data['products'];
        $total_pieces = 0;
        $currency_code = $pdf_data['credit_note_info']['currency_code'];
        $currency_value = $pdf_data['credit_note_info']['currency_value'];
        if(!empty($products)){
            foreach ($products as $key => $value) {
        ?>
        <tr>
            <td style="width:6%;"><?php echo $i; ?></td>
            <td style="width:40%;"><?php echo $value["model"]; ?></td>
            <td style="width:10%;"><?php echo $product_info[$value['order_product_id']]["quantity"]; ?></td>
            <td style="width:10%;"><?php echo $this->currency->format($value["price_per_piece"], $currency_code, $currency_value, false); ?></td>
            <?php
            $tax = (float) $value["output_tax_rates"] * (float) $value["price_per_piece"] / 100;
            $total = (float) $value["price_per_piece"] * (int) $product_info[$value['order_product_id']]["quantity"];
            ?>
            <td style="width:10%;">
                <?php echo $this->currency->format((float) $value["discount_per_piece"], $currency_code, $currency_value, false); ?>
            </td>
            <td style="width:9%;">
                <?php echo $this->currency->format($tax, $currency_code, $currency_value, false); ?>
            </td>
            <td style="width:15%;">
                <?php echo $this->currency->format($total, $currency_code, $currency_value, false); ?>
            </td>
        </tr>
        <?php
            $total_pieces += (int) $product_info[$value['order_product_id']]["quantity"];
            $i++;
            }
        }
        $total_amnt = 0;
        foreach ($pdf_data["totals"] as $key => $total) {
            if (!in_array($key, $this->_ignorable_total)) {
                $total_amnt += (float) $total['value'];
        ?>
        <tr>
            <td colspan="6" align="right"><b><?php echo $total['title']; ?> </b> </td>
            <td colspan="1" align="right"> <b><?php echo $this->registry->get('currency')->format($total['value'], 'INR');?></b></td>
        </tr>
        <?php
            }
        }
        if ($pdf_data['buyer_data']['order_status_id'] == 8) {//For COD Failed Orders
        ?>
        <tr>
            <td colspan="6" align="right"><b>Surface Courier (6-9 days)</b> </td>
            <td colspan="1" align="right"> <b><?php echo $this->registry->get('currency')->format($pdf_data["credit_note_info"]['invoice_shipping'], 'INR');?></b></td>
        </tr>
        <?php
                $total_amnt += (float)$pdf_data["credit_note_info"]['invoice_shipping'];
            }
        ?>
        <tr>
            <td colspan="6" align="right"><b>Total Amount</b> </td>
            <td colspan="1" align="right">
                <b><?php echo $this->registry->get('currency')->format($total_amnt, 'INR');?></b>
            </td>
        </tr>
        <?php
        $shipping_charge = (float) $pdf_data['credit_note_info']['shipping_collected']*(-1);

        $net_refundable = 0.00;
        $cn_amt         = $total_amnt;
        if ($pdf_data['buyer_data']['order_status_id'] == 8) {
            $amount_not_collected = (float)$total_amnt-(float) $pdf_data['credit_note_info']['cod_failed_penalty'];
        ?>
        <tr>
            <td colspan="6" align="right"><b>Advance Collected</b> </td>
            <td colspan="1" align="right"> <b><?php echo $this->registry->get('currency')->format((float)$pdf_data['credit_note_info']['cod_failed_penalty']*(-1), 'INR');?></b>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="right"><b>Amount Not Collected in COD</b> </td>
            <td colspan="1" align="right">
                <b><?php echo $this->registry->get('currency')->format((float)$amount_not_collected, 'INR');?></b>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="right"><b>Penalty Due To COD Failed</b> </td>
            <td colspan="1" align="right">
                <b><?php echo $this->registry->get('currency')->format($pdf_data['credit_note_info']['cod_failed_penalty'], 'INR');?></b>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="right"><b>Net Refundable Amount</b> </td>
            <td colspan="1" align="right"> <b><?php echo $this->registry->get('currency')->format(0, 'INR');?></b>
            </td>
        </tr>
        <?php
            } else {
        ?>
        <tr>
            <td colspan="6" align="right"><b>Reverse Shipping Charges</b> </td>
            <td colspan="1" align="right"> <b><?php echo $this->registry->get('currency')->format((float)$shipping_charge, 'INR');?></b></td>
        </tr>

        <?php $net_amount = (float) $total_amnt + (float) $shipping_charge; ?>
        <tr>
            <td colspan="6" align="right"><b>Net Refundable Amount</b> </td>
            <td colspan="1" align="right"> <b><?php echo $this->registry->get('currency')->format($net_amount, 'INR');?></b></td>
        </tr>
        <?php
                $net_refundable = $cn_amt = $net_amount;
            }
            $net_amount_in_words = convert_to_currency_indian_format($net_refundable, $currency_code);
        ?>
        <tr>
            <td colspan="7" align="center">
                Amount Chargeable (in words) : <b><?php echo $net_amount_in_words; ?></b>
            </td>
        </tr>
        <tr>
            <td colspan="7" align="right">Authorised Signatory</td>
        </tr>
    </tbody>
</table>
</page>