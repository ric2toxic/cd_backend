<?php
$total_tax_value = 0.0;
$total_taxable_value = 0.0;
$total_product_value = 0.0;
$total_discount_value = 0.0;
$highest_tax_rate = 0.0;
$total = 0.0;
$total_val = 0.0;
$net_refundable_amt = 0.0;
$total_section = array();
$all_hsn_codes = array();
$buyer_zones = $pdf_data['buyer_data']['payment_zone_id'] . "," . $pdf_data['buyer_data']['shipping_zone_id'];
//Get StateCode for buyer
$model_localisation_zone = null;
$this->load->model('localisation/zone', 'frontend');
if (method_exists($this->_registry, 'get')) {
    $model_localisation_zone = $this->_registry->get('frontend_model_localisation_zone');
} else {
    $model_localisation_zone = $this->_registry->frontend_model_localisation_zone;
}

$state_codes = $model_localisation_zone->getZoneGSTStateCode($buyer_zones);
$gst_state_code = array();
foreach ($state_codes as $state_code) {
    $gst_state_code[$state_code['zone_id']] = $state_code['gst_state_code'];
}

$div_id = '';
if ($this->_cancelled_flag) {
    $backimg = DIR_SYSTEM . 'library/image/cancelled.png';
    $div_id = 'backimg="'.$backimg.'" backimgw="60%"';
}

$cash_discount      = $pdf_data['credit_note_info']['cash_discount'] ?? 0;
$less_cash_discount = $pdf_data['credit_note_info']['less_cash_discount'] ?? 0;
?>
<page <?php echo $div_id;?> >
    
    <h2 align="center">
        <?php echo $pdf_data['seller_data']['company']; ?>
    </h2>
   
    <p style="margin-top: 0px; text-align: center">
        <?php echo $pdf_data['seller_data']['address1'] . ', ' . $pdf_data['seller_data']['address2'] . ', ' . $pdf_data['seller_data']['city'] . ', ' . $pdf_data['seller_data']['state'] . ', Pin: ' . $pdf_data['seller_data']['pincode']; ?>
    </p>
    
    <?php if (isset($pdf_data['seller_data']['tin'])) { ?>
        <p style="margin-top: 0px; text-align: center">GSTIN: <b><?php echo $pdf_data['seller_data']['tin']; ?></b></p>
    <?php } ?>
   
    <p style="margin-top: 0px; text-align: center"><b><u>CREDIT NOTE</u></b></p>
   
    <table style="width:98%; margin-bottom: 10px;" border="1" cellspacing="1" cellpadding="4">
        <tbody>
            <tr> 
                <td style="width:50%;" rowspan="3">
                    Document No. :
                    <b><?php echo $pdf_data['credit_note_info']['credit_note_prefix'] . $pdf_data['credit_note_info']['credit_note_no']; ?></b><br />
                    Date of Issue : <?php echo $pdf_data['credit_note_info']['credit_note_date']; ?><br />
                    City : <?php echo $pdf_data['seller_data']['city']; ?>, <br />
                    State : <?php echo $pdf_data['seller_data']['state']; ?>,<br /> 
                    Pin Code : <?php echo $pdf_data['seller_data']['pincode']; ?><br />
                    <span style="text-align:right;">State Code : 
                        <b><?php echo $pdf_data['seller_data']['state_code'][0]['gst_state_code']; ?></b>
                    </span>
                </td>
                <td style="width:50%;">
                    Against Invoice/Bill of Supply No.: 
                    <?php echo $pdf_data['credit_note_info']['invoice_prefix'] . $pdf_data['credit_note_info']['invoice_no']; ?>
                </td>
            </tr>
            <tr>
                <td style="width:50%;">
                    Date of Invoice/Bill of Supply: <?php echo $pdf_data['credit_note_info']['invoice_date']; ?>
                </td>
            </tr>
            <tr>
                <td style="width:50%;">Ref. P.O. : <?php echo $pdf_data['credit_note_info']['order_no']; ?></td>
            </tr>
            <tr>
                <td style="width:50%;" align="center"><b>Details of Receiver | Billed to:</b></td>
                <td style="width:50%;" align="center"><b>Details of Consignee | Shipped to:</b></td>
            </tr>
            <tr>
                <td style="width:50%;">
                    <b>Name :</b> <?php echo $pdf_data['buyer_data']['payment_company']; ?><br />
                    <b>Address :</b> <?php echo $pdf_data['buyer_data']['payment_address_1'] . ',' . $pdf_data['buyer_data']['payment_address_2']; ?><br />
                    <b>GSTIN :</b> <?php echo $pdf_data['buyer_data']['gst_number']; ?><br />
                    <b>City :</b> <?php echo $pdf_data['buyer_data']['payment_city']; ?>, <br />
                    <b>State :</b> <?php echo $pdf_data['buyer_data']['payment_zone']; ?>, <br />  
                    <b>Pin Code :</b> <?php echo $pdf_data['buyer_data']['payment_postcode']; ?><br />
                    <span style="text-align:right;">State Code : 
                        <b><?php echo $gst_state_code[$pdf_data['buyer_data']['payment_zone_id']]; ?></b>
                    </span>
                </td>
                <td style="width:50%;">
                    <b>Name :</b> <?php echo $pdf_data['buyer_data']['shipping_company']; ?><br />
                    <b>Address :</b> <?php echo $pdf_data['buyer_data']['shipping_address_1'] . ',' . $pdf_data['buyer_data']['shipping_address_2']; ?><br />
                    <b>GSTIN :</b> <?php echo $pdf_data['buyer_data']['gst_number']; ?><br />
                    <b>City :</b> <?php echo $pdf_data['buyer_data']['shipping_city']; ?>, <br />
                    <b>State :</b> <?php echo $pdf_data['buyer_data']['shipping_zone']; ?>, <br />  
                    <b>Pin Code :</b> <?php echo $pdf_data['buyer_data']['shipping_postcode']; ?><br />
                    <span style="text-align:right;">State Code : 
                        <b><?php echo $gst_state_code[$pdf_data['buyer_data']['shipping_zone_id']]; ?></b>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <table style="width:98%; margin-bottom: 10px;" border="1" cellspacing="1" cellpadding="4">
        <tr align="center">
            <td style="width:4%;"><font size="-1">S. No.</font></td>
            <td style="width:30%;"><font size="-1">Description Of Goods</font></td>
            <td style="width:10%;"><font size="-1">HSN/SAC</font></td>
            <td style="width:6%;"><font size="-1">UOM</font></td>
            <td style="width:4%;"><font size="-1">Qty</font></td>
            <td style="width:10%;"><font size="-1">Rate</font></td>
            <td style="width:10%;""><font size="-1">Amount</font></td>
            <td style="width:9%;"><font size="-1">Less: Discount</font></td>
            <td style="width:7%;"><font size="-1">Tax Rate</font></td>
            <td style="width:10%;"><font size="-1">Taxable Value</font></td>
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
                 $all_hsn_codes[] = $value['hsn_code'];
        ?>
        <tr>
            <td style="width:4%;"><font size="-1"><?php echo $i; ?></font></td>
            <td style="width:28%;">
                <b><font size="-1"><?php echo $value["model"]; ?></font></b><br />
                <i><font size="-2"><?php echo $value["name"]; ?></font></i>
            </td>
            <td style="width:10%;"><font size="-1"><?php echo $value['hsn_code']; ?></font></td>
            <td style="width:6%;"><font size="-1">Piece</font></td>
            <td style="width:4%;">
                <font size="-1"><?php echo $product_info[$value['order_product_id']]["quantity"]; ?></font>
            </td>
            <td style="width:10%;">
                <font size="-1">
                    <?php echo $this->currency->format($value["price_per_piece"], $currency_code, $currency_value, false); ?>
                </font>
            </td>
            <?php
            $tax = (float) $value["output_tax_rates"] * (float) $value["price_per_piece"] / 100;
            $total = (float) $value["price_per_piece"] * (int) $product_info[$value['order_product_id']]["quantity"];
            ?>
            <td style="width:9%;">
                <font size="-1">
                    <?php echo $this->currency->format($total, $currency_code, $currency_value, false); ?>
                </font>
            </td>
            <?php $total_discount_per_product = (float) $product_info[$value['order_product_id']]["quantity"] * (float) $value["discount_per_piece"]; ?>
            <td style="width:9%;">
                <font size="-1">
                    <?php echo $this->currency->format((float) $total_discount_per_product, $currency_code, $currency_value, false); ?>
                </font>
            </td>
            <?php
                $taxable_value = (float) ($total + $total_discount_per_product);
                $total_discount_value += $total_discount_per_product;
                $total_product_value += (float) $value["price_per_piece"] * $product_info[$value['order_product_id']]["quantity"];
                if ($value["output_tax_rates"] > $highest_tax_rate) {
                    $highest_tax_rate = $value["output_tax_rates"];
                }
                $pdf_data['highest_tax_rate'] = $highest_tax_rate;
                $total_tax_value += (float) ($value["output_tax_rates"] * $taxable_value) / 100;
            ?>
            <td style="width:7%;">
                <font size="-1">
                    <?php echo $this->currency->format($value["output_tax_rates"], $currency_code, $currency_value, false); ?>%
                </font>
            </td>
            <?php $total_taxable_value += $taxable_value; ?>
            <td style="width:10%;">
                <font size="-1">
                    <?php echo $this->currency->format($taxable_value, $currency_code, $currency_value, false); ?>
                </font>
            </td>
        </tr>
        <?php 
                $total_pieces += (int) $product_info[$value['order_product_id']]["quantity"];
                $i++;
                }
            }
            $all_hsn_codes = array_unique($all_hsn_codes);

            $total_section['sub_total'] = array(
                'title' => 'Total Taxable Value (a)',
                'value' => $total_taxable_value
                    );
            $shipping_tax_value          = 0;
            $reversal_shipping_tax_value = 0;
            $reversal_shipping           = $pdf_data["credit_note_info"]['reversal_shipping'];
            $other_charges = (float)$pdf_data["credit_note_info"]['other_charges'];

            if (
                $pdf_data['credit_note_info']['is_cod_failed'] == '1'
                && !empty($pdf_data["credit_note_info"]['invoice_shipping'])
            ) {
                $total_val += $pdf_data["credit_note_info"]['invoice_shipping'];
                $shipping_tax_value = $this->calculateTaxValue($pdf_data["credit_note_info"]['invoice_shipping'], $highest_tax_rate);
                $pdf_data['credit_note_info']['shipping_tax_value'] = $shipping_tax_value;
                $total_section['shipping'] = array(
                    'title' => 'Shipping charges (b)',
                    'value' => $pdf_data["credit_note_info"]['invoice_shipping']
                        );
            } else if ( $pdf_data['credit_note_info']['is_cod_failed'] != '1' ){
                $total_val -= $pdf_data['credit_note_info']['shipping_collected'];
                $shipping_tax_value = $this->calculateTaxValue($pdf_data['credit_note_info']['shipping_collected'], $highest_tax_rate);
                $shipping_tax_value = (-1) * $shipping_tax_value;
                $pdf_data['credit_note_info']['shipping_tax_value'] = $shipping_tax_value;
                $total_section['shipping'] = array(
                    'title' => 'Reverse Shipping Recovered (b1)',
                    'value' => (-1) * $pdf_data['credit_note_info']['shipping_collected']
                        );

                if($reversal_shipping != 0){
                    $total_val += $reversal_shipping;
                    $reversal_shipping_tax_value = $this->calculateTaxValue($reversal_shipping, $highest_tax_rate);
                    $pdf_data['credit_note_info']['reversal_shipping_tax_value'] = $reversal_shipping_tax_value;
                    $total_section['reversal_shipping'] = array(
                                                            'title' => 'Reversal of Shipping (b2)',
                                                            'value' => $reversal_shipping
                                                           );
                }
            }

            $total_val += $total_product_value + $total_discount_value + $total_tax_value + $shipping_tax_value + $reversal_shipping_tax_value;

            $total_tax_value += $shipping_tax_value + $reversal_shipping_tax_value;

            $cgst = 0.0;
            $sgst = 0.0;
            $igst = 0.0;

            if ($pdf_data['seller_data']['zone_id'] == $pdf_data['buyer_data']['payment_zone_id']) {
                $cgst = $sgst = (float) $total_tax_value / 2;
            } else {
                $igst = (float) $total_tax_value;
            }

            $total_section['cgst'] = array(
                'title' => 'Add: CGST (c)',
                'value' => $cgst
                    );
            $total_section['sgst'] = array(
                'title' => 'Add: SGST (d)',
                'value' => $sgst
                    );
            $total_section['igst'] = array(
                'title' => 'Add: IGST (e)',
                'value' => $igst
                    );
            //Check credit note is intra state or inter state
            $total_section['gst'] = array(
                'title' => 'TOTAL GST (c+d+e)',
                'value' => $total_tax_value
                    );
            $total_section['total_amt'] = array(
                'title' => 'Total Amount (a+b+c+d+e)',
                'value' => $total_val
                    );

           if( $cash_discount > 0) {

            $total_section['cash_discount'] = array(
                'title' => 'Cash Discount',
                'value' => $cash_discount
                    );

            $total_section['less_cash_discount'] = array(
                'title' => 'Less: Cash Discount',
                'value' => -$less_cash_discount
                    );
            }

            $net_refundable_amt = 0;

            if ($pdf_data['credit_note_info']['is_cod_failed'] == '1') {
                $this->getCodFailedSubTotals($pdf_data, $total_section, $total_val);
                
                $net_refundable_amt += $total_val;
                
                $net_refundable_amt += $total_section['not_collected']['value'];
                
                $net_refundable_amt += $total_section['not_refundable']['value'];
            
            } else {
                $net_refundable_amt = $total_val;
            }

            if($other_charges != 0){
                $total_section['other_charges'] = array(
                                                    'title' => 'Other Charges :',
                                                    'value' => $other_charges
                                                        );
                $net_refundable_amt += $other_charges;

            }

            $total_section['net_refundable'] = array(
                                                    'title' => 'Net Refundable Amount',
                                                    'value' => round($net_refundable_amt, 2)
                                                    );
         

        if (!empty($total_section)) {
            foreach ($total_section as $key => $total) {
        ?>
        <tr align="right">
            <td style="width:80%;" colspan="8"><b><font size="-1"><?php echo $total['title']; ?></font></b> </td>
            <td style="width:20%;" colspan="2"><b>
                <font size="-1">
                <?php echo $this->currency->format($total['value'], $currency_code, $currency_value, true); ?>
                </font></b>
            </td>
        </tr>
        <?php
            }
        }
        ?>
        <tr align="center">
            <td colspan="10" style="width:100%;"><b>
                <font size="-1">
                    <?php
                        $amount_in_words = '';
                        if($net_refundable_amt < 0 ){
                            $amount_in_words = 'Minus - '.convert_to_currency_indian_format(abs($net_refundable_amt), $currency_code);
                        }else{
                            $amount_in_words = convert_to_currency_indian_format($net_refundable_amt, $currency_code);
                        }
                    ?>
                    Total Amount (in Words): 
                    <?php echo $amount_in_words; ?>
                </font></b>
            </td>
        </tr>
    </table>
    <table style="width:98%; margin-bottom: 10px;" border="1" cellspacing="1" cellpadding="4">
        <tr align="center" >
            <th style="width:25%;"><b><font size="-1">HSN/SAC</font></b></th>
            <th style="width:20%;"><b><font size="-1">Taxable Value</font></b></th>
            <th style="width:10%;"><b><font size="-1">Tax Rate</font></b></th>
            <th style="width:15%;"><b><font size="-1">CGST</font></b></th>
            <th style="width:15%;"><b><font size="-1">SGST</font></b></th>
            <th style="width:15%;"><b><font size="-1">IGST</font></b></th>
        </tr>
        <?php
            $hsn_cgst = 0.0;
            $hsn_sgst = 0.0;
            $hsn_igst = 0.0;

            $groupHsnCodeWiseProducts = $this->summrizeProductsForHsnCode($pdf_data, $all_hsn_codes);
            $total_hsn_product_value = 0.0;
            $total_hsn_cgst = 0.0;
            $total_hsn_sgst = 0.0;
            $total_hsn_igst = 0.0;
            $shipping_cgst = 0.0;
            $shipping_sgst = 0.0;
            $shipping_igst = 0.0;

            $reversal_shipping_cgst = 0.0;
            $reversal_shipping_sgst = 0.0;
            $reversal_shipping_igst = 0.0;

            //Summrizing on HSN/SAC code bases
            foreach ($groupHsnCodeWiseProducts as $hsn_code => $hsnProducts) {
                foreach ($hsnProducts as $tax_rate => $hsnProduct) {
                    //Check credit note is intra state or inter state
                    if ($pdf_data['seller_data']['zone_id'] == $pdf_data['buyer_data']['payment_zone_id']) {
                        $hsn_cgst = $hsn_sgst = $this->currency->format((float) $hsnProduct['tax_value'] / 2, $currency_code, $currency_value, false);
                    } else {
                        $hsn_igst = $this->currency->format((float) $hsnProduct['tax_value'], $currency_code, $currency_value, false);
                    }
                    $total_hsn_product_value += $hsnProduct['product_value'];
                    $total_hsn_cgst += $hsn_cgst;
                    $total_hsn_sgst += $hsn_sgst;
                    $total_hsn_igst += $hsn_igst;
        ?>
        <tr>
            <td><font size="-1"><?php echo $hsn_code; ?></font></td>
            <td>
                <font size="-1">
                <?php echo $this->currency->format($hsnProduct['product_value'], $currency_code, $currency_value, false); ?>
                </font>
            </td>
            <td><font size="-1">
                <?php echo $this->currency->format($hsnProduct['taxable_rate'], $currency_code, $currency_value, false); ?>%</font>
            </td>
            <td><font size="-1"><?php echo $hsn_cgst; ?></font></td>
            <td><font size="-1"><?php echo $hsn_sgst; ?></font></td>
            <td><font size="-1"><?php echo $hsn_igst; ?></font></td>
        </tr>
        <?php
            }
        }
        if (!empty($total_hsn_cgst) && !empty($total_hsn_sgst)) {
            $total_hsn_cgst += (float) $pdf_data['credit_note_info']['shipping_tax_value'] / 2;
            $total_hsn_sgst += (float) $pdf_data['credit_note_info']['shipping_tax_value'] / 2;

            $total_hsn_cgst += (float) $reversal_shipping_tax_value / 2;
            $total_hsn_sgst += (float) $reversal_shipping_tax_value / 2;

        } else if (!empty($total_hsn_igst)) {
            $total_hsn_igst += $pdf_data['credit_note_info']['shipping_tax_value'];

            $total_hsn_igst += $reversal_shipping_tax_value;
        }
        if ($pdf_data['seller_data']['zone_id'] == $pdf_data['buyer_data']['payment_zone_id']) {
            $shipping_cgst = $shipping_sgst = $this->currency->format((float) $pdf_data['credit_note_info']['shipping_tax_value'] / 2, $currency_code, $currency_value, false);
        } else {
            $shipping_igst = $this->currency->format((float) $pdf_data['credit_note_info']['shipping_tax_value'], $currency_code, $currency_value, false);
        }

        if ($pdf_data['seller_data']['zone_id'] == $pdf_data['buyer_data']['payment_zone_id']) {
            $reversal_shipping_cgst = $reversal_shipping_sgst = $this->currency->format((float)$reversal_shipping_tax_value / 2, $currency_code, $currency_value, false);
        } else {
            $reversal_shipping_igst = $this->currency->format((float)$reversal_shipping_tax_value, $currency_code, $currency_value, false);
        }

        if ($pdf_data['buyer_data']['order_status_id'] == $this->_failed_state_id) {
            if (!empty($pdf_data["credit_note_info"]['invoice_shipping']) && $pdf_data["credit_note_info"]['invoice_shipping'] > 0) {
        ?>
        <tr>
            <td><font size="-1">Shipping Charges</font></td>
            <td><font size="-1">
                <?php echo $this->currency->format($pdf_data["credit_note_info"]['invoice_shipping'], $currency_code, $currency_value, false); ?></font>
            </td>
            <td><font size="-1">
                <?php echo $this->currency->format($pdf_data['highest_tax_rate'], $currency_code, $currency_value, false); ?>%</font>
            </td>
            <td><font size="-1"><?php echo $shipping_cgst; ?></font></td>
            <td><font size="-1"><?php echo $shipping_sgst; ?></font></td>
            <td><font size="-1"><?php echo $shipping_igst; ?></font></td>
        </tr>
        <?php
                $total_hsn_product_value += $pdf_data["credit_note_info"]['invoice_shipping'];
            }
        } else if (!empty($pdf_data['credit_note_info']['shipping_collected']) && $pdf_data['credit_note_info']['shipping_collected'] > 0) {
        ?>
        <tr>
            <td><font size="-1">Reverse Shipping Recovered</font></td>
            <td><font size="-1"><?php echo (-1) * ($this->currency->format($pdf_data['credit_note_info']['shipping_collected'], $currency_code, $currency_value, false)); ?></font>
            </td>
            <td><font size="-1">
                <?php echo $this->currency->format($pdf_data['highest_tax_rate'], $currency_code, $currency_value, false); ?>%</font>
            </td>
            <td><font size="-1"><?php echo $shipping_cgst; ?></font></td>
            <td><font size="-1"><?php echo $shipping_sgst; ?></font></td>
            <td><font size="-1"><?php echo $shipping_igst; ?></font></td>
        </tr>
        <?php $total_hsn_product_value += (-1) * $pdf_data['credit_note_info']['shipping_collected']; ?>
        <?php if($reversal_shipping != 0){ ?>
        <tr>
            <td><font size="-1">Reversal of Shipping</font></td>
            <td><font size="-1"><?php echo $this->currency->format($reversal_shipping, $currency_code, $currency_value, false); ?></font>
            </td>
            <td><font size="-1">
                <?php echo $this->currency->format($pdf_data['highest_tax_rate'], $currency_code, $currency_value, false); ?>%</font>
            </td>
            <td><font size="-1"><?php echo $reversal_shipping_cgst; ?></font></td>
            <td><font size="-1"><?php echo $reversal_shipping_sgst; ?></font></td>
            <td><font size="-1"><?php echo $reversal_shipping_igst; ?></font></td>
        </tr>
        <?php 
            }
            $total_hsn_product_value += $reversal_shipping;
        }
        ?>

        <?php if($other_charges != 0){ ?>
        <tr>
            <td><font size="-1">Other Charges</font></td>
            <td><font size="-1"><?php echo $this->currency->format($other_charges, $currency_code, $currency_value, false); ?></font>
            </td>
            <td><font size="-1">
                0%</font>
            </td>
            <td><font size="-1">0.00</font></td>
            <td><font size="-1">0.00</font></td>
            <td><font size="-1">0.00</font></td>
        </tr>
        <?php } $total_hsn_product_value += $other_charges; ?>

        <tr>
            <td align="center"><b><font size="-1">Total</font></b></td>
            <td><b><font size="-1">
                <?php echo $this->currency->format($total_hsn_product_value, $currency_code, $currency_value, true); ?></font></b>
            </td>
            <td><b>-</b></td>
            <td><b>
                <font size="-1">
                <?php echo $this->currency->format($total_hsn_cgst, $currency_code, $currency_value, true); ?>
                </font></b>
            </td>
            <td><b>
                <font size="-1">
                <?php echo $this->currency->format($total_hsn_sgst, $currency_code, $currency_value, true); ?>
                </font></b>
            </td>
            <td><b>
                <font size="-1">
                <?php echo $this->currency->format($total_hsn_igst, $currency_code, $currency_value, true); ?>
                </font></b>
            </td>
        </tr>
    </table>
    <table style="width:98%; margin-bottom: 10px;" border="1" cellspacing="1" cellpadding="4">
        <tr>
            <td style="width:50%;" align="center"><font size="-1">
                  For (<?php echo $pdf_data['seller_data']['company']; ?>)<br /><br /><br />
                  Authorised Signatory</font>
            </td>
            <td style="width:50%;" valign="middle"><font size="-1">Terms & Conditions</font></td>
        </tr>
    </table>
</page>