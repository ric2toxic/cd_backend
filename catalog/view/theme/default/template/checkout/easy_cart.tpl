<table id="cart_table" class="table table-bordered table-hover table-responsive">
  <thead>
<tr>
  <th class="text-left"><?php echo $column_image; ?></th>
  <th class="text-left"><?php echo $column_name; ?></th>
  <!--<th class="text-left hidden-xs"><?php echo $column_model; ?></th>-->
  <th class="text-right hidden-xs"><?php echo $column_quantity; ?></th>
  <th class="text-right hidden-xs"><?php echo $pieces; ?></th>
  <th class="text-right"><?php echo $column_price . $column_per_piece; ?></th>
  <th class="text-right"><?php echo $column_price . $column_per_set; ?></th>
  <th class="text-right"><?php echo $column_total; ?></th>
  <th class="text-right"><?php echo $column_tax; ?></th>
</tr>
  </thead>
  <tbody>
<?php foreach ($products as $product) { ?>
<tr>
  <td class="text-center" data-th="Image"><?php if ($product['thumb']) { ?>
    <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" width="<?php echo $product['img_width']; ?>" height="<?php echo $product['img_height']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-thumbnail" /></a>
    <?php } ?></td>
  <td class="text-left" data-th="Product"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a><br/> <span style="font-size:11px;"><?php echo $text_product_code . $product['model']; ?></span>
    <?php if($product['previously_ordered'] == 1){ ?>
    <br /><span class="previously_ordered"> <?php echo '('.$text_previously_ordered .' )'; ?></span>
    <?php } ?>
    <br/> <span style="font-size:11px;"><?php echo $product['set_description']; ?></span>

    <?php if($product['is_single'] == 0){ ?>
    <?php if (!$product['stock']) { ?>
					  <span class="text-danger"> <br />
                        <?php

						  if($product['stock_quantity'] > 0){
							  printf($this->language->get('text_insufficient_quantity'),  $product['stock_quantity']);
                          }else{
							echo $this->language->get('text_out_of_stock');
						  }
						?>
					  </span>
    <?php } ?>
    <?php } ?>
    <?php if ($product['option' ]) { ?>
    <?php foreach ($product['option'] as $option) { ?>
    <br />
    <small><?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
    <?php } ?>
    <?php } ?>
    <?php if ($product['reward']) { ?>
    <br />
    <small><?php echo $product['reward']; ?></small>
    <?php } ?>
    <?php if ($product['recurring']) { ?>
    <br />
    <span class="label label-info"><?php echo $text_recurring_item; ?></span> <small><?php echo $product['recurring']; ?></small>
    <?php } ?></td>
  <!--<td class="text-left hidden-xs"><?php echo $product['model']; ?></td> -->
  <td class="text-right hidden-xs"><?php echo $product['quantity']; ?></td>
  <td class="text-right" data-th="No. of Pieces"><?php echo $product['piece_in_set']; ?></td>
  <td class="text-right"><?php echo $product['price_per_piece']; ?></td>
  <td class="text-right hidden-xs"><?php echo $product['price']; ?></td>
  <td class="text-right"><?php echo $product['total']; ?></td>
  <td class="text-right" data-th="Tax"><?php echo $product['tax']; ?></td>
  <!--<td class="text-left" data-th="Quantity"><div class="input-group btn-block" style="max-width: 200px;">
      <input type="text" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" size="1" class="form-control qty_box" />
						<span class="input-group-btn">
						<button type="submit" data-toggle="tooltip" title="<?php echo $button_update; ?>" class="btn btn-primary"><i class="fa fa-refresh"></i></button>
						<button type="button" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger" onclick="cart.remove('<?php echo $product['key']; ?>');"><i class="fa fa-times-circle"></i></button></span></div></td>
  <td class="text-right" data-th="No. of Pieces"><?php echo $product['piece_in_set']; ?></td>
  <td class="text-right" data-th="Price per piece"><?php echo $product['price_per_piece']; ?></td>

  <td class="text-right" data-th="Tax"><?php echo $product['tax']; ?></td>-->
</tr>
<?php } ?>
<?php foreach ($vouchers as $voucher) { ?>
<tr>
  <td class="text-left"><?php echo $voucher['description']; ?></td>
  <td class="text-left hidden-xs"></td>
  <td class="text-right hidden-xs">1</td>
  <td class="text-right hidden-xs"><?php echo $voucher['amount']; ?></td>
  <td class="text-right"><?php echo $voucher['amount']; ?></td>
</tr>
<?php } ?>
  </tbody>
  <tfoot>
  <?php /* ?>
    <?php foreach ($totals as $total) { ?>
    <tr>
      <td colspan="7" class="text-right  hidden-xs"><strong><?php echo $total['title']; ?>:</strong></td>
      <td colspan="1" class="text-right  visible-xs"><strong><?php echo $total['title']; ?>:</strong></td>
      <td class="text-right"><?php echo $total['text']; ?></td>
    </tr>
    <?php } ?>
  <?php */ ?>
  <tr>
    <td colspan="2" align="right"><?php echo $text_total_qty;?></td>
    <td><?php echo $total_sets;?> <?php echo $column_quantity;?></td>
    <td colspan="5"><?php echo $total_pieces;?> <?php echo $pcs;?></td>
  </tr>
  <?php foreach ($totals as $total) { ?>
    <?php if ($total['code'] != 'tax') { ?>
      <tr>
        <td colspan="7" class="text-right"><strong><?php echo $total['title']; ?>:</strong></td>
        <?php if ($total['code'] == 'total') { ?>
          <td class="text-right" style="font-size:14px;"><strong><?php echo $total['text']; ?></strong></td>
        <?php }else{ ?>
          <td class="text-right"><?php echo $total['text']; ?></td>
        <?php } ?>
      </tr>
    <?php }else{ ?>
      <?php if($cform_submit == 1 ){ ?>
        <tr>
          <td colspan="7" class="text-right"><strong><?php echo $text_cst; ?>:</strong></td>
          <td class="text-right"><?php echo $cst; ?></td>
        </tr>
        <tr>
          <td colspan="7" class="text-right"><strong><?php echo $text_tax_refund; ?>:</strong></td>
          <td class="text-right"><?php echo $tax_refund; ?></td>
        </tr>
      <?php }else{ ?>
        <tr>
          <td colspan="7" class="text-right"><strong><?php echo $total['title']; ?>:</strong></td>
          <td class="text-right"><?php echo $total['text']; ?></td>
        </tr>
      <?php } ?>
    <?php } ?>
  <?php } ?>


  </tfoot>
</table>
