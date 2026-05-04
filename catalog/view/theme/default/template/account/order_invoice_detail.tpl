<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<link href="catalog/view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="all" />
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/bootstrap/js/bootstrap.min.js"></script>
<link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<link type="text/css" href="catalog/view/theme/default/stylesheet/stylesheet_new.css" rel="stylesheet" media="all" />
</head>

<body>
<div class="container">
  <?php foreach ($orders as $order) { ?>
  <div style="page-break-after: always;">
  
    <table class="table" style="border: none; margin-bottom: 0px; table-layout: fixed; font-size: 18px; font-weight: bold;">
    <td style="width:33%;"> 
      <?php if (isset($order['gati_ou'])) {
              if ($order['gati_ou']) {
                echo $order['gati_ou'];
              }
            } ?> </td>
    <td align="center" style="width:33%;">
      <?php if (!($order['shipping_tin_no'] or $order['payment_tin_no']) and $order['wayBillReqd']) { echo $text_retail; } echo $text_invoice . $order['invoice_no']; ?></td>
    <td align="right" style="width:33%;"> 
      <?php if (isset($order['tracking_no'])) { 
              if ($order['tracking_no']) { 
                echo "Docket: " . $order['tracking_no'];
              }
            } ?> </td>
    </table>

    <table class="table table-bordered" style="margin-bottom: 5px">
    <tr>
    <td style="padding: 5px;"><b><?php echo $text_order_no; ?></b><?php echo $order['order_no']; ?> </td>
    <td style="padding: 5px;"><center><b><?php 
    if ( $order['payment_code'] == 'cod' ) {
        echo $text_cod . $order['order_total']; 
    } else { echo $text_prepaid; } ?> </b></center></td>
    <td style="padding: 5px;" align="right"><b><?php echo $text_invoice_date; ?></b><?php echo $order['invoice_date']; ?> </td>
    </tr>
    </table>
    <table class="table table-bordered" style="margin-bottom: 5px">
      <thead>
        <tr>
        <td class="invoice"><?php echo $text_ship_to; ?></td>
        <td class="invoice"><?php echo $text_payer; ?></td>
        <td class="invoice"><?php echo $text_order_detail; ?></td>
        </tr>
      </thead>
      <tbody>
        <tr>
        
        <td class="invoice"><address>
            <?php echo $order['shipping_address']; ?> </address>
            <?php if ($order['shipping_tin_no']) { ?>
            <b><?php echo $text_tin_no; ?></b><?php echo $order['shipping_tin_no']; ?></br>
            <?php } ?>
            <b><?php echo $text_telephone; ?></b><?php echo $order['telephone']; ?>
        </td>
        
        <td class="invoice"><address>
            <?php echo $order['payment_address']; ?> </address>
            <?php if ($order['payment_tin_no']) { ?>
            <b><?php echo $text_tin_no; ?></b><?php echo $order['payment_tin_no']; ?></br>
            <?php } ?>
            <b><?php echo $text_telephone; ?></b><?php echo $order['telephone']; ?>
        </td>
        
        <td class="invoice"><address>
            <strong>WholesaleBox Internet Pvt. Ltd.</strong><br />
            <?php echo $order['store_address']; ?>
            </address>
            <b><?php echo $text_seller_tin_no; ?></b> <?php echo "08195900085"; ?><br />
            <b><?php echo $text_helpline; ?></b> <?php echo $order['store_telephone']; ?><br />
            <?php if ($order['store_fax']) { ?>
            <b><?php echo $text_fax; ?></b> <?php echo $order['store_fax']; ?><br />
            <?php } ?>
            <b><?php echo $order['store_url']; ?></b>
        </td>

        </tr>
      </tbody>
    </table>
    
    <table class="table table-bordered"  style="margin-bottom: 5px">
      <thead>
        <tr>
          <td class="invoice"><b><?php echo "S#"; ?></b></td>
          <td class="invoice"><b><?php echo $column_product; ?></b></td>
          <td class="invoice"><b><?php echo $column_model; ?></b></td>
          <td class="invoice-right-text"><b><?php echo $column_sets; ?></b></td>
          <td class="invoice-right-text"><b><?php echo $column_pieces; ?></b></td>
          <td class="invoice-right-text"><b><?php echo $column_price; ?></b></td>
          <td class="invoice-right-text"><b><?php echo $column_total; ?></b></td>
          <!-- <td class="invoice-right-text"><b><?php // echo $column_tax; ?></b></td> -->
        </tr>
      </thead>
      <tbody>
        <?php $counter = 0; ?>
        <?php foreach ($order['product'] as $product) { ?>
        <tr>
        <?php $counter = $counter + 1; ?>
        <td class="invoice"><?php echo $counter; ?></td>
          <td class="invoice"><?php echo $product['name']; ?>
            <?php foreach ($product['option'] as $option) { ?>
            <br />
            &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
            <?php } ?></td>
          <td class="invoice"><?php echo $product['model']; ?></td>
          <td class="invoice-right-text"><?php echo $product['quantity']; ?></td>
          <td class="invoice-right-text"><?php echo $product['total_pieces']; ?></td>
          <td class="invoice-right-text"><?php echo $product['price_per_piece']; ?></td>
          <td class="invoice-right-text"><?php echo $product['total']; ?></td>
          <!-- <td class="invoice-right-text"><?php // echo $product['tax']; ?></td> -->
        </tr>
        <?php } ?>
        <?php foreach ($order['voucher'] as $voucher) { ?>
        <tr>
          <td class="invoice"><?php echo $voucher['description']; ?></td>
          <td class="invoice"></td>
          <td class="invoice-right-text">1</td>
          <td class="invoice-right-text"><?php echo $voucher['amount']; ?></td>
          <td class="invoice-right-text"><?php echo $voucher['amount']; ?></td>
        </tr>
        <?php } ?>
        
        <tr>
            <td class="invoice-right-text" colspan="4"><b><?php echo $text_total_pieces; ?> </b></td>
            <td class="invoice-right-text"> <strong><?php echo $order['total_pieces_order']; ?> </strong></td>
        </tr>
        
        <?php foreach ($order['total'] as $total) { ?>
          <?php if ($total['code'] == 'tax' and ($order['cform_submit'] != 'no_submit')) { ?>
            <tr>
              <td class="invoice-right-text" colspan="6"><b><?php echo $text_cst; ?></b></td>
              <td class="invoice-right-text"><?php echo $order['cst_with_cform']; ?></td>
            </tr>
            <?php if ($order['cform_submit'] == 'will_submit') { ?>
              <tr>
                <td class="invoice-right-text" colspan="6"><b><?php echo $text_tax_refund; ?></b></td>
                <td class="invoice-right-text"><?php echo $order['refundable_cform']; ?></td>
              </tr>
            <?php } ?>
          <?php } else { ?>
            <tr>
              <td class="invoice-right-text" colspan="6"><b><?php echo $total['title']; ?></b></td>
              <?php if ($total['code'] == 'total') { ?>
                <td class="invoice-right-text" style="border:solid;"><strong><?php echo $total['text']; ?></strong></td>
              <?php } else { ?>
                 <td class="invoice-right-text"><?php echo $total['text']; ?></td>
              <?php } ?>
            </tr>
          <?php } ?>
        <?php } ?>
      </tbody>
    </table>
    
    <ol>
    <li><?php echo $text_dupatta_taxfree; ?></li>
    <li><?php echo $text_additional_octroi; ?></li>
    </ol>
    <?php if (!($order['shipping_tin_no'] or $order['payment_tin_no']) and $order['wayBillReqd']) { ?>
    <b><?php echo $text_customer_declaration; ?> </b></br>
    <?php echo $text_pre_declaration . $order['shipping_name'] . $text_post_declaration ; ?></br>
    <?php } ?> 
    <hr style="margin-top: 3px; margin-bottom: 3px;">
    <b><center><?php echo $text_signature; ?></center></b><hr style="margin-top: 3px; margin-bottom: 3px;">
  </div>
  <?php } ?>
</div>
</body>
</html>

<style>
  body {
    background-color: #ffffff;
    margin: 0;
  }
  html, body {
    color: #666666;
    font-family: "Arial", arial;
    font-size: 12px;
    height: 100%;
    line-height: 18px;
    margin: 0;
    padding: 0;
    text-rendering: optimizelegibility;
  }

  .table thead > tr > td, .table tbody > tr > td {
    vertical-align: middle;
  }
  .table > thead > tr > td.invoice {
    padding: 2px;
  }
  .table > tbody > tr > td.invoice {
    padding: 2px;
  }
  .table thead td {
    font-weight: bold;
  }
  .table > tbody > tr > td.invoice-right-text {
    padding: 2px;
    text-align: right;
  }

</style>