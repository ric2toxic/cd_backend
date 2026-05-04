<?php if (!isset($redirect)) { ?>
<div class="row">
    <div class="pull-left">
        <div class="panel-heading">
            <h4 class="panel-title"><?php echo $text_checkout_confirm; ?></h4>
        </div>
    </div>
    <div class="pull-right checkout-confirm-order-button">
        <div class="buttons">
            <div class="pull-left">
                <div class="continue back-cash-delivery"><?php echo $button_back; ?></div>
            </div>
        </div>
    </div>
</div>
<div style="overflow-x:auto">
  <table class="table table-bordered">
    <thead>
      <tr>
          <td class="text-center"><?php echo $column_image; ?></td>
          <td class="text-left" style="width:285px;"><?php echo $column_name; ?></td>
          <!-- <td class="text-left"><?php // echo $column_model; ?></td> -->
          <td class="text-right"><?php echo $column_quantity; ?></td>
          <td class="text-right"><?php echo $pieces; ?></td>
          <td class="text-right"><?php echo $column_price . $column_per_piece; ?></td>
          <td class="text-right"><?php echo $column_price . $column_per_set; ?></td>
          <td class="text-right"><?php echo $column_total; ?></td>
          <td class="text-right"><?php echo $column_tax; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product) { ?>
      <tr>
          <td class="text-center" data-th="Image"><?php if ($product['thumb']) { ?>
              <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-thumbnail" /></a>
              <?php } ?></td>
        <td class="text-left"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a><br/> <span style="font-size:11px;"><?php echo $text_product_code . $product['model']; ?></span><br/> <span style="font-size:11px;"><?php echo $product['set_description']; ?></span>
          <?php foreach ($product['option'] as $option) { ?>
          <br />
          &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
          <?php } ?>
          <?php if($product['recurring']) { ?>
          <br />
          <span class="label label-info"><?php echo $text_recurring_item; ?></span> <small><?php echo $product['recurring']; ?></small>
          <?php } ?></td>
        <td class="text-right"><?php echo $product['quantity']; ?></td>

          <td class="text-right" data-th="Price per piece"><?php echo $product['piece_in_set']; ?></td>
          <td class="text-right" data-th="Price per piece"><?php echo $product['price_per_piece']; ?></td>
          <td class="text-right" data-th="Price per set"><?php echo $product['price']; ?></td>
          <td class="text-right"><?php echo $product['total']; ?></td>
          <td class="text-right" data-th="Tax"><?php echo $product['tax']; ?></td>
      </tr>
      <?php } ?>
      <tr>
          <td colspan="2" class="text-right"><strong> <?php echo $text_total_qty; ?> </strong></td>
          <td class="text-right"> <?php echo($total_sets . " " . $column_quantity); ?></td>
          <td class="text-right"> <?php echo($total_pieces . " " . $pieces); ?></td>
      </tr>
      <?php foreach ($vouchers as $voucher) { ?>
      <tr>
        <td class="text-left"><?php echo $voucher['description']; ?></td>
        <td class="text-left"></td>
        <td class="text-right">1</td>
        <td class="text-right"><?php echo $voucher['amount']; ?></td>
        <td class="text-right"><?php echo $voucher['amount']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php foreach ($totals as $total) { ?>
      <?php /* ?>
          <tr>
            <td colspan="7" class="text-right"><strong><?php echo $total['title']; ?>:</strong></td>
            <td class="text-right"><?php echo $total['text']; ?></td>
          </tr>
      <?php */ ?>

        <?php if ($total['code'] != 'tax') { ?>
            <tr>
              <td colspan="7" class="text-right"><strong><?php echo $total['title']; ?>:</strong></td>
              <?php if ($total['code'] == 'total') { ?>
                  <td class="text-right" style="font-size:14px;"><strong><?php echo $total['text']; ?></strong></td>
               <?php } else { ?>
                  <td class="text-right"><?php echo $total['text']; ?></td>
               <?php } ?>
            </tr>
            <?php } else {

                        if($cform_submit == 1 ){
             ?>
                            <tr>
                              <td colspan="7" class="text-right"><strong><?php echo $text_cst; ?>:</strong></td>
                              <td class="text-right"><?php echo $cst; ?></td>
                            </tr>
                            <tr>
                              <td colspan="7" class="text-right"><strong><?php echo $text_tax_refund; ?>:</strong></td>
                              <td class="text-right"><?php echo $tax_refund; ?></td>
                            </tr>
            <?php } else{
            ?>
                            <tr>
                              <td colspan="7" class="text-right"><strong><?php echo $total['title']; ?>:</strong></td>
                              <td class="text-right"><?php echo $total['text']; ?></td>
                            </tr>
            <?php
            } ?>
        <?php } ?>
      <?php } ?>
    </tfoot>
  </table>
</div>
<?php echo $payment; ?>
<?php } else { ?>
<script type="text/javascript"><!--
location = '<?php echo $redirect; ?>';
//--></script>
<?php } ?>


<script>
    $(document).ready(function(){
        $('.back-cash-delivery').click(function(){
            $("body").scrollTop(0);
            $('.account_billing_detail').hide();
            $('.new_shipping_address').hide();
            $('.checkout_payment_address').hide();
            $('.checkout_payment_method').show();
            $('.checkout_confirm').hide();

            $('.checkout-pages-block').removeClass('col-sm-12');
            $('.order-summary-block').show();
            $('.checkout-pages-block').addClass('col-sm-8');

            $('.checkout_step1').hide();
            $('.checkout_step2').show();
            $('.checkout_step3').hide();
            $('.checkout_step4').hide();
        });
    });
</script>