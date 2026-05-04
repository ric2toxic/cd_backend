
<div id="content">
  <div class="container-fluid product_list">
    <div class="panel panel-default">
      <div class="panel-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td rowspan="2" style="width: 1px;" class="text-center">
                    <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" id="group_checkbox" />
                  </td>
                  <td rowspan="2" class="text-center"><?php echo $column_image; ?></td>

                  <td rowspan="2" class="text-left"><?php echo $column_name; ?></td>
                  <td rowspan="2" class="text-left"><?php echo $column_wsb_product_code; ?></td>
                  <td rowspan="2" class="text-left"><?php echo $column_seller_sku; ?></td>
                  <td class="text-center" colspan="2"><?php echo $column_price ; ?></td>
                  <td colspan="1" >Selling Price</td>
                  <!--<td rowspan="2" class="text-right"><?php if ($sort == 'p.commission') { ?>
                    <a href="<?php echo $sort_commission; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_commission; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_commission; ?>"><?php echo $column_commission; ?></a>
                    <?php } ?></td>-->
                  <td rowspan="2" class="text-right"><?php echo 'Set Description';?></td>
                  <td rowspan="2" class="text-right"><?php echo $column_quantity; ?></td>
                  <td rowspan="2" class="text-left">
                    <a href="<?php echo $sort_status; ?>"><?php echo "Stock Status Info"; ?></a>
                  </td>
                  <td rowspan="2" class="text-left"><?php echo $column_status; ?></td>
                  <td rowspan="2" class="text-right"><?php echo $column_action; ?></td>
                </tr>

                <!-- this table row for Transfer price with divided 3 column -->
                <tr>
                  <td class="text-center">Input Seller Tax Rate</td>
                  <td rowspan="2" class="text-center"><?php echo $column_commission; ?></td>
                  <td class="text-center">Output Tax Rate</td>
                </tr>
              </thead>
              <tbody>
                <?php if ($products) { ?>
                <?php foreach ($products as $product) { ?>
                <tr class="moderated_<?php echo $product['product_id'];?>"  id="row_<?php echo $product['product_id'];?>_1"
                  <?php if($product['is_archived']) {?> style="background-color:#ffc966" <?php }?> >
                  <td rowspan="2" class="text-center">
                    <?php if (in_array($product['product_id'], $selected)) { ?>
                    <input id="checkbox_<?php echo $product['product_id']; ?>" data-sorname="<?php echo $product['sku']; ?>" data-sorproduct="<?php if($product_edit_enable!=1){echo $product['sor_product'];}?>" type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" product-status-id = "<?php echo $product['product_status_id'];?>" checked="checked" data-seller-id="<?php echo $product['seller_id']; ?>" data-product-rating="<?php echo $product['product_rating']; ?>"
                    data-franchise-id="<?php echo $product['franchise_id']; ?>" data-product-has-option="<?php echo $product['has_product_option']; ?>"
                    data-product-model="<?php echo $product['model']; ?>"/>
                    <?php } else { ?>
                    <input id="checkbox_<?php echo $product['product_id']; ?>" data-sorname="<?php echo $product['sku']; ?>"  data-sorproduct="<?php if($product_edit_enable!=1){echo $product['sor_product'];}?>" type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" product-status-id = "<?php echo $product['product_status_id'];?>" data-seller-id="<?php echo $product['seller_id']; ?>" data-product-rating="<?php echo $product['product_rating']; ?>" data-franchise-id="<?php echo $product['franchise_id']; ?>" data-product-has-option="<?php echo $product['has_product_option']; ?>" data-product-model="<?php echo $product['model']; ?>"/>
                    <?php } ?></td>
                    <td rowspan="2" class="text-center"><?php if ($product['image']) { ?>
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-thumbnail" />
                    <?php } else { ?>
                    <span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>
                    <?php } ?></td>
                    <td rowspan="2" class="text-left order_list_comment product_weight" data-product-id="<?php echo $product['product_id'];?>">
                    <span class="product_list_class" id="product_weight_<?php echo $product['product_id'];?>">
                      <?php echo 'Weight : '. number_format($product['weight'], 3, '.', ''). ' '.$product['weight_unit'];?>
                    </span>
                    <br>
                    <?php echo $product['name']; ?>
                    <?php if($product['product_option_name']) { ?>
                      <br>
                      <span class="btn btn-success btn-xs"><?php echo $product['product_option_name'].' option'; ?></span>
                    <?php } ?>
                  </td>
                  <td rowspan="2" class="text-left">
                    <sup class="product_star">
                        <label><?php echo $product['product_rating']; ?></label>
                        <i class="fa fa-star fa-stack-1x"></i>
                    </sup>
                    <br/>
                    <?php echo $product['model']; ?> <br/>
                    <?php if( $product['store_sales'] ) { ?>
                      <span class="store_sales_span"> <?php echo $product['store_sales']; ?> </span>
                    <?php } ?>
                    <?php if( $product['hsn_code'] ) { ?>
                      <span class="store_sales_span"> <?php echo $product['hsn_code']; ?> </span>
                    <?php } ?>
                  </td>
                  <td rowspan="2" class="text-left">
                    <?php echo $product['sku'];?> <br />
                    <b><?php echo $product['exclusive'];?></b> <br />
                    <?php if ( $product['nickname'] ) { ?>
                      <span class="store_sales_span">
                        <?php echo $product['nickname']; ?>
                      </span>
                    <?php } ?>

                  </td>
                  <td colspan="2"  class="text-center transfer_price"data-product-id="<?php echo $product['product_id'];?>">
                    <span id="transfer_price_<?php echo $product['product_id'];?>">
                      <?php if ($product['special']) { ?>
                      <span style="text-decoration: line-through;"><?php echo $product['price']; ?></span><br/>
                      <div class="text-danger"><?php echo $product['special']; ?></div>
                      <?php } else { ?>
                      <?php echo $product['price']; ?>
                      <?php } ?>
                    </span>
                  </td>
                  <td><?php echo $product['selling_price'];?></td>
                  <!--<td rowspan="2" class="text-right"><?php echo $product['commission']; ?></td>-->
                  <!--<td rowspan="2" class="text-right"><?php echo $product['commission']; ?></td>-->
                  <td rowspan="2" class="text-left order_list_comment">
                    <span class="product_list_class"><?php echo 'Piece in set : '. $product['piece_in_set'];?></span> <br>
                    <?php echo $product['set_description']; ?>
                  </td>
                  <?php
                    $sor_stock = '';
                  if ($product['seller_invoice_generate_status']==1 || $product_edit_enable) {
                    $sor_stock = 'product_quantity';
                  }
                  ?>
                  <td rowspan="2" class="text-right <?php echo $sor_stock;?> " data-product-id="<?php echo $product['product_id'];?>">
                    <span id="product_quantity_<?php echo $product['product_id'];?>">
                      <?php if ($product['quantity'] <= 0) { ?>
                      <span class="label label-warning"><?php echo $product['quantity']; ?></span>
                      <?php } elseif ($product['quantity'] <= 5) { ?>
                      <span class="label label-danger"><?php echo $product['quantity']; ?></span>
                      <?php } else { ?>
                      <span class="label label-success"><?php echo $product['quantity']; ?></span>
                      <?php } ?>
                    </span>
                  </td>

                   <td rowspan="2" class="text-right" data-product-id="<?php echo $product['product_id'];?>">
                    <span <?php echo $product['product_id'];?>>
                      <?php if(!empty($product['stock_status_info']['stock'])) { ?>
              <span class="label label-success"><?php echo "in stock"; }?> </span>
              <span class="label label-danger"><?php  if(empty($product['stock_status_info']['stock'])) {
                echo "out of stock" ; echo "<br>";
                foreach($product['stock_status_info']['reason'] as $key=>$value) {
                echo "$value";
                echo "<br>";
              } }?></span>
              <span class="label label-danger"><?php ?></span>
                    </span>
                  </td>


                  <td rowspan="2" class="text-left"><?php echo $product['status']; ?></td>
                  <td rowspan="2" class="text-right">
                    
                  </td>
                </tr>
                <!-- this table row for Transfer price with divided 3 column -->
                <tr class="moderated_<?php echo $product['product_id'];?>" id="row_<?php echo $product['product_id'];?>_2" 
                  <?php if($product['is_archived']) {?> style="background-color:#ffc966" <?php }?> > 
                  <td class="text-center"><?php echo $product['seller_tax'];?></td>
                  <td class="text-center product_commission" data-product-id="<?php echo $product['product_id'];?>">
                     <span id="product_commission_<?php echo $product['product_id'];?>">
                       <?php echo $product['commission'];?>
                    </span>
                  </td>
                  <td class="text-center"><?php echo $product['output_tax_rate'];?></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="11"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
