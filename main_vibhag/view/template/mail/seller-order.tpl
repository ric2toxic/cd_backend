<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Book a new Order No:<?php echo $suborder_id; ?></title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
<div style="width: 680px;"><a href="<?php echo $store_url; ?>" title="<?php echo $store_name; ?>"><img src="<?php echo $logo; ?>" alt="<?php echo $store_name; ?>" style="margin-bottom: 20px; border: none;" /></a>

     <?php if($store_id == INTERNATIONAL_STORE_ID) { ?>
    <div style="background-color: #3baf3b; padding: 5px; color: #fff; font-weight: bold; text-align: center;"> INTERNATIONAL ORDER - Wholesale Box : Book a new Order No: <?php echo $suborder_id; ?> </div> <br><br>
      <?php  } ?>
  <p style="margin-top: 0px; margin-bottom: 20px;">

      Dear <b><?php echo $seller_name; ?></b>, <br><br>

      Greetings from Wholesale Box ! <br><br>

      <?php 
      $quality_check_variable = 'Please do a through QC to avoid unnecessary returns hassles. Kindly keep the stock ready for pick-up as soon as possible, so that out pick-up team can complete all pick-ups lined up. Kindly do not delay the shipment and keep invoice ready. ';

      if(!empty($is_add_remove_item_in_order)) {
        ?>
        There has been an update in the purchase Order No: <b><?php echo $suborder_id; ?></b>. Please find the update details below: <br><br>
        <?php
      } else {
        ?>
        Please book a new Order No: <b><?php echo $suborder_id; ?></b>.<br><br>
        <?php
      }
      ?>
      

      Invoice should be billed to: <br>
        <!-- <b>Wholesalebox Internet Pvt Ltd, B-1 Crystal Mall, Bani park, Jaipur-302016 <br> -->
        <b><?php echo $buyer_details['company']  . ', ' .
                      $buyer_details['address1'] . ', ' .
                      $buyer_details['address2'] . ', ' .
                      $buyer_details['city']     . '-' .
                      $buyer_details['pincode']; ?></br>
        GSTIN: <?php echo $buyer_details['tin']; ?></b><br>
      Invoice should be dated <b><?php echo $process_date; ?></b>. Please also mention the type of item such as kurti, dupatta etc in the invoice besides the SKU code.<br><br>

      <?php 
      echo $quality_check_variable;
      ?>
      <?php if($show_store_sales_notice){ ?>
      <br><br> For the highlighted items in yellow color, NO pickup is required. These are sold from WholesaleBox stores directly. Only invoice needs to be generated against them.<br><br>
      <?php } ?>
       <?php if($show_sor_product_notice){ ?>
      <br><br> For the highlighted items in blue color: These are the SOR product(s), ordered and delivered directly from our Store(s). No pickup is required, as well as no invoice is required to be generated. This is for your information only.
      <?php } ?>
  </p>

Order details are below:

  <!-- SKU breakup -->
<?php foreach($order_products as $key => $value){
        $invoice_text = '';
        if($key == 'uninvoiced') {
          $invoice_text = 'Un-invoiced SKU';  
        } else if($key == 'invoiced_not_given') {
            $invoice_text = 'Invoiced but not given SKU';
        } else if($key == 'invoiced_given') {
            $invoice_text = 'Invoiced and given SKU';
        }

echo '<br><br><b>'.$invoice_text.'</b>'; 
 ?>

<table border="1" cellpadding="8" cellspacing="0" >
    <thead>
    <tr>
      <th>Product</th>
      <th>SKU</th>
      <th>Comment</th>
      <th>Total Pieces</th>
      <th>Transfer Price / Piece</th>
      <th>Amount (Inc. Tax)</th>
    </tr>
    </thead>
    <tbody>
      <?php foreach($value as $product){ ?>
            <tr style="background-color:<?php echo $product['store_sales'] != 'NO' ? '#fff8c3': 'inherit'; ?>">
              <td style="text-align: center; padding: 0px;"> <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['sku']; ?>" title="<?php echo $product['sku']; ?>" class="img-thumbnail" /> <br>
                <?php /*
                      if($product['edit_type'] == 'SELLER_NOT_SUPPLIED') {
                        $bg_colors = '#ff4444';
                      } else if($product['edit_type'] == 'SELLER_LATER_DISPATCH') {
                        $bg_colors = '#ffbb33';
                      } else if($product['edit_type'] == 'SELLER_APPROVED') {
                        $bg_colors = '#00C851';
                      } else if($product['edit_type'] == 'SELLER_PARTIAL') {
                        $bg_colors = '#33b5e5';
                      }else if($product['sor_product']=='1'){
                        $bg_colors = '#4150f2';
                      } else{
                        $bg_colors = 'inherit';
                      } */ 
                ?>
                <!-- <span style="background-color:<?php echo  $bg_colors; ?>; font-size: 11px; padding: 3px;"><?php echo  str_replace('_',' ',$product['edit_type']) ; ?></span> -->
              </td>
              <td><?php echo $product['sku']; ?></td>
              <td><?php echo $product['set_description']; ?></td>
              <td><?php echo $product['total_pieces']; ?></td>
              <td width="20%"><?php echo $product['transfer_price_per_piece']; ?></td>
              <td width="20%"><?php echo $product['total_amount_row']; ?></td>
            </tr>
      <?php } ?>

    <tr>
      <td  colspan="5" style="text-align: right;"><strong>Total Bill Amount (Inc. Tax)</strong></td>
      <td><strong><?php echo $seller_product_totals_category_wise[$key];?></strong></td>
    </tr>
    </tbody>
  </table>

<?php } ?>
  

   <br><br><b>We are again emphasizing that you need to send products which have been checked thoroughly. Defects, delay in handing over and incorrect product sets will affect your ratings and sales on <a href="www.wholesalebox.in" target="_blank">Wholesale Box</a></b>. <br><br>

   Please regularly maintain inventory by logging into seller panel on daily basis. If an item has gone out-of-stock, then quantity should be updated to zero, on a priority basis. Similarly, items which are showing quantity as zero, should be updated if they are in-stock. Items which have quantity zero on your panel are not shown to customers, and you would be losing on potential orders, if they are not updated regularly. <br><br>

   Looking forward to a long term business. <br><br>

   Thanks,<br>
   Wholesalebox Team<br>
   0141-4049163<br>

</div>
</body>
</html>
