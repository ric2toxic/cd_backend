<div class="row products_panel hidden tabs edit_selling_price">
  <div class="col-lg-12">
      <form action="<?php echo $update_selling_price; ?>" id="product_form" method="post">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
        <input type="hidden" name="suborder_id" value="<?php echo $suborder_id; ?>" />
        <div class="row">
          <div class="col-sm-6">&nbsp;</div>
          <div class="col-sm-6">
             <div class="form-group pull-right">
                <button type="subimt" class="btn btn-primary rmv_prod_save_button" id="rmv_pro_button">
                  <i class="fa fa-save"></i>  Save
                </button>
              </div>
          </div>
        </div>
        <input type="hidden" name="edit_selling_price" value="1" />
        <table class="table table-bordered">
          <thead>
            <tr>
              <td><span> Sr No.</span></td>
              <td>Name</td>
              <td>Sets</td>
              <td>Total Pieces</td>
              <td>Price Per Piece</td>
              <td>Set Description</td>
              <td>Additional Info</td>
            </tr>
          </thead>
          <?php if( !empty($order_combo_products) ){ $sno = 1; ?>
            <?php foreach( $order_combo_products as $key => $order_combo_product ){ ?>
              <?php if($key === 'combo_products' ) { ?>
                <?php foreach( $order_combo_product as $combo_prod_id_key => $combo_product ) { ?>
                  <tbody style="border:3px solid #000">
                    <?php  foreach( $combo_product as $product ) { 
                            $editable_class = '';
                            ?>
                        <tr>
                          <td><span><?php echo $sno;?></span></td>
                          <td>
                            <img src="<?php echo $product['image']; ?>"
                                 width="<?php echo $product['width']; ?>px"
                                 height="<?php echo $product['height']; ?>px"
                                 class="pull-left" />
                            <p style="margin-left:10px;width: calc( 100% - 100px );" 
                               class="pull-left">
                                <?php echo $product['name']; ?> <br><br>
                                <b>Model: </b> <?php echo $product['model']; ?> <br><br>
                                <b>Seller Sku: </b> <?php echo $product['seller_sku']; ?>
                            </p>
                          </td>
                          <td>
                            <div id="rmv_pro_edit_input_default_<?php echo $product['order_product_id']; ?>"
                                 class="rmv_pro_edit_input_default_<?php echo $product['combo_product_id']; ?>">
                              <span><?php echo $product['quantity']; ?></span>
                              <?php if (strtolower($product['edit_type']) == 'yes' ||
                                      strtolower($product['edit_type']) == 'seller_later_dispatch') {
                                   ?>
                              <span style="cursor: pointer;" 
                                    class="remove_pro_edit_quan rm_prod_quan_pencil_<?php echo $product['combo_product_id'];?> hidden"
                                    id="rm_prod_quan_pencil_<?php echo $product['order_product_id'];?>"
                                    data-order-product-id="<?php echo $product['order_product_id']; ?>"
                                    data-combo-product-id="<?php echo $product['combo_product_id']; ?>"
                                    data-product-id="<?php echo $product['product_id']; ?>" >
                                <i class="fa fa-pencil"></i>
                              </span>
                              <?php } ?>
                            </div>
                            <?php if (strtolower($product['edit_type']) == 'yes' ||
                                      strtolower($product['edit_type']) == 'seller_later_dispatch') {
                                   ?>
                            <div class="hidden rmv_pro_edit_input_block_<?php echo $product['combo_product_id']; ?>" 
                                 id="rmv_pro_edit_input_block_<?php echo $product['order_product_id']; ?>">  
                              <input type="number"
                                     name="products[<?php echo $product['order_product_id']; ?>][quantity][new]"
                                     value="<?php echo ((int) $product['quantity']); ?>"
                                     class="form-control rmv_quan_input rmv_quan_input_<?php echo $product['combo_product_id']; ?>"
                                     id="rmv_quan_input_<?php echo $product['order_product_id']; ?>"
                                     data-order-product-id="<?php echo $product['order_product_id']; ?>"
                                     data-combo-product-id="<?php echo $product['combo_product_id']; ?>"
                                     data-product-id="<?php echo $product['product_id']; ?>"
                                     min="1"
                                     max="<?php echo ((int) $product['quantity']); ?>"
                                     data-old-quantity="<?php echo ((int) $product['quantity']); ?>"
                                     disabled 
                                     style="width: 50px;"/>
                              <span class="remove_pro_edit_quan_cancel" 
                                    data-order-product-id="<?php echo $product['order_product_id']; ?>"
                                    data-combo-product-id="<?php echo $product['combo_product_id']; ?>"
                                    data-product-id="<?php echo $product['product_id']; ?>"
                                    data-old-quantity="<?php echo ((int) $product['quantity']); ?>">
                                <i class="fa fa-ban"></i>
                              </span>       
                              <input type="hidden"
                                     name="products[<?php echo $product['order_product_id']; ?>][quantity][old] *"
                                     class="hidden_rmv_quan_input_<?php echo $product['combo_product_id']; ?>"
                                     value="<?php echo ((int) $product['quantity']); ?>" 
                                     disabled="disabled"/>
                            </div> 
                            <?php } ?>              
                          </td>
                          <td style="width:100px!important">
                            <span><?php echo ((int) $product['quantity'] * (int) $product['piece_in_set']); ?></span>
                          </td>
                          <td width="150px">
                            <span><?php echo $product['price_per_piece']; ?></span>
                            <a title="Edit product selling price" href="javascript:void(0);" data-product-id="<?php echo $product['order_product_id']?>" class="update-selling-price">
                              <i class="fa fa-pencil"></i>
                            </a>
                            <div style="display: none" id="edit_selling_price_<?php echo $product['order_product_id']?>">
                              <input type="hidden" name="selling_price[<?php echo $product['order_product_id']?>][old]" class="form-control numbers" style="width:90px" value="<?php echo $product['price_per_piece']; ?>">
                              <input type="text" name="selling_price[<?php echo $product['order_product_id']?>][new]" class="form-control numbers" style="width:90px" value="<?php echo $product['price_per_piece']; ?>">
                            </div>
                          </td>
                          <td width="150px">
                            <span><?php echo $product['comment']; ?></span>
                          </td>
                          <td style="width:150px!important" class="edit_type_td">
                              <span>
                                <?php echo str_replace("_", ' ', $product['edit_type']); ?>
                                      <?php 
                                        if(!empty($product['edit_history'])) {
                                          $edit_history  = unserialize($product['edit_history']);
                                          reset($edit_history);
                                          $edit_history = end($edit_history);
                                          echo '<br>Edited By <b>' . (isset($edit_history['user_name']) ? $edit_history['user_name'] : "") .
                                          '</b><br> on <b>' . date('d-m-Y', strtotime($edit_history['date_added'])) . '</b>';
                                          echo (!empty($edit_history['additional_comment']) ? "<br>Comment: ".$edit_history['additional_comment'] : "" ); 
                                        }   
                                      ?>
                              </span>
                          </td>
                        </tr>
                    <?php $sno++; } ?>
                  </tbody>
                <?php } ?>
              <?php } else { ?>
                <tbody>
                    <?php foreach( $order_combo_product as $product ) { 
                          $editable_class = ''; 
                          ?>
                        <tr>
                          <td><span><?php echo $sno;?></span></td>
                          <td>
                            <img src="<?php echo $product['image']; ?>"
                                 width="<?php echo $product['width']; ?>px"
                                 height="<?php echo $product['height']; ?>px"
                                 class="pull-left" />
                            <p style="margin-left:10px;width: calc( 100% - 100px );" 
                               class="pull-left">
                                <?php echo $product['name']; ?> <br><br>
                                <b>Model: </b> <?php echo $product['model']; ?> <br><br>
                                <b>Seller Sku: </b> <?php echo $product['seller_sku']; ?>
                            </p>
                          </td>
                          <td class="<?php echo $editable_class; ?>" style="width:100px!important">
                            <div id="rmv_pro_edit_input_default_<?php echo $product['order_product_id']; ?>"
                                 class="rmv_pro_edit_input_default_<?php echo $product['order_product_id']; ?>" >
                              <span><?php echo $product['quantity']; ?></span>
                              <span style="cursor: pointer;" 
                                    class="remove_pro_edit_quan rm_prod_quan_pencil_<?php echo $product['combo_product_id'];?> hidden"
                                    id="rm_prod_quan_pencil_<?php echo $product['order_product_id'];?>" 
                                    data-order-product-id="<?php echo $product['order_product_id']; ?>" 
                                    >
                                <i class="fa fa-pencil"></i>
                              </span>
                            </div>
                            <div class="hidden rmv_pro_edit_input_block_<?php echo $product['order_product_id']; ?>" id="rmv_pro_edit_input_block_<?php echo $product['order_product_id']; ?>">  
                              <input type="number"
                                     name="products[<?php echo $product['order_product_id']; ?>][quantity][new]"
                                     value="<?php echo ((int) $product['quantity']); ?>"
                                     class="form-control rmv_quan_input rmv_quan_input_<?php echo $product['order_product_id']; ?>"
                                     id="rmv_quan_input_<?php echo $product['order_product_id']; ?>"
                                     data-order-product-id="<?php echo $product['order_product_id']; ?>"
                                     data-combo-product-id="<?php echo $product['combo_product_id']; ?>"
                                     data-product-id="<?php echo $product['product_id']; ?>"
                                     min="1"
                                     max="<?php echo ((int) $product['quantity']); ?>"
                                     data-old-quantity="<?php echo ((int) $product['quantity']); ?>" 
                                     disabled
                                     style="width:50px"/>
                              <span class="remove_pro_edit_quan_cancel" 
                                    data-order-product-id="<?php echo $product['order_product_id']; ?>"
                                    data-old-quantity="<?php echo ((int) $product['quantity']); ?>">
                                <i class="fa fa-ban"></i>
                              </span>       
                              <input type="hidden"
                                     name="products[<?php echo $product['order_product_id']; ?>][quantity][old] *"
                                     value="<?php echo ((int) $product['quantity']); ?>"
                                     id="hidden_rmv_quan_input_<?php echo $product['order_product_id']; ?>" 
                                     disabled="disabled"/>
                            </div>         
                          </td>
                          <td style="width:100px!important">
                            <span><?php echo ((int) $product['quantity'] * (int) $product['piece_in_set']); ?></span>
                          </td>
                          <td width="150px">
                            <span><?php echo $product['price_per_piece']; ?></span>
                            <a title="Edit product selling price" href="javascript:void(0);" data-product-id="<?php echo $product['order_product_id']?>" class="update-selling-price">
                              <i class="fa fa-pencil"></i>
                            </a>
                            <div style="display: none" id="edit_selling_price_<?php echo $product['order_product_id']?>">
                              <input type="hidden" name="selling_price[<?php echo $product['order_product_id']?>][old]" class="form-control numbers" style="width:90px" value="<?php echo $product['price_per_piece']; ?>">
                              <input type="text" name="selling_price[<?php echo $product['order_product_id']?>][new]" class="form-control numbers" style="width:90px" value="<?php echo $product['price_per_piece']; ?>">
                            </div>
                          </td>
                          <td width="150px">
                            <span><?php echo $product['comment']; ?></span>
                          </td>
                          <td>
                              <span><?php 
                                  if(strtolower($product['edit_type']) != 'yes') {
                                      echo str_replace("_", ' ', $product['edit_type']); 
                                      if(!empty($product['edit_history'])) {
                                        $edit_history  = unserialize($product['edit_history']);
                                        reset($edit_history);
                                        $edit_history = end($edit_history);
                                        echo '<br>Edited By <b>' . (isset($edit_history['user_name']) ? $edit_history['user_name'] : "") .
                                        '</b><br> on <b>' . date('d-m-Y', strtotime($edit_history['date_added'])) . '</b>';
                                        echo (!empty($edit_history['additional_comment']) ? "<br>Comment: ".$edit_history['additional_comment'] : "" ); 
                                      } 
                                    }else{
                                      echo 'N/A';
                                  }

                                      ?>
                              </span>
                          </td>
                        </tr>
                    <?php $sno++; } ?>
                  </tbody>
              <?php } ?>
            <?php } ?>
          <?php } ?>
        </table>
      </form>
  </div>
</div>


<script type="text/javascript">

$(document).on('click','.update-selling-price',function(){

  var product_id = $(this).data('product-id');
  $('#edit_selling_price_'+product_id).toggle();

})

$('.numbers').keyup(function () { 
    this.value = this.value.replace(/[^0-9\.]/g,'');
});

</script>