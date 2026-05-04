<div class="row products_panel hidden tabs remove_products">
  <div class="col-lg-12">
      <input type="hidden" form="product_form" name="order_id" value="<?php echo $order_id; ?>" />
      <input type="hidden" form="product_form" name="suborder_id" value="<?php echo $suborder_id; ?>" />
      <form action="<?php echo $save; ?>" id="product_form" method="post">
        <div class="row">
          <div class="col-sm-6"></div>
          <div class="col-sm-6">
            <div class="col-sm-5">
              <!--<div class="form-group">
                <label class="control-label" for="input-status"><?php echo 'Edit Type'; ?></label>
                <select name="edit_type" id="input-status" class="form-control" disabled>
                  <option value="">Select Edit Type</option>
                  <option value="CANCELLED_BY_CUSTOMER">CANCELLED BY CUSTOMER</option>
                  <option value="SELLER_NOT_SUPPLIED">SELLER NOT SUPPLIED</option>
                </select>
              </div>-->
            </div>
            <div class="col-sm-5">
              <!--<div class="form-group">
                <label class="control-label" for="input-status"><?php echo 'Comment'; ?></label>
                <textarea name="comment"
                          placeholder="Comment"
                          class="form-control" disabled></textarea>
              </div>-->
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                <button type="subimt" 
                        class="btn btn-primary rmv_prod_save_button" 
                        id="rmv_pro_button"
                        disabled>
                  <i class="fa fa-save"></i>  Save
                </button>
              </div>
            </div>
          </div>
        </div>
        <input type="hidden" form="product_form" name="is_remove_product" value="1" />
        <table class="table table-bordered">
          <thead>
            <tr>
              <td>
                <span> Sr No.</span> <br>
                <!--<input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" id="group_checkbox" />-->
              </td>
              <td>Name</td>
              <td>Sets</td>
              <td>Total Pieces</td>
              <td>Price Per Piece</td>
              <td>Set Description</td>
              <td>Edit Type</td>
              <!-- <td>Action</td> -->
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
                          <td>
                            <span><?php echo $sno;?></span>
                            <?php if (strtolower($product['edit_type']) == 'yes' ||
                                      strtolower($product['edit_type']) == 'seller_later_dispatch') { 
                                  $editable_class = ' editable ';
                            ?>
                            <input id="checkbox_<?php echo $product['order_product_id']; ?>" 
                                   type="checkbox" 
                                   name="selected[]" value="<?php echo $product['order_product_id']; ?>"
                                   class="remove_product_checkbox check_combo_product_id_<?php echo $product['combo_product_id']; ?>"
                                   data-order-product-id="<?php echo $product['order_product_id']; ?>"
                                   data-combo-product-id="<?php echo $product['combo_product_id']; ?>" 
                                   data-product-id="<?php echo $product['product_id']; ?>"
                                   data-product-quantity="<?php echo $product['quantity']; ?>"/>
                            <?php } ?>                                
                          </td>
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
                          </td>
                          <td width="150px">
                            <span><?php echo $product['comment']; ?></span>
                          </td>
                          <td style="width:150px!important" class="edit_type_td">
                            <?php if (strtolower($product['edit_type']) == 'yes' || 
                                      strtolower($product['edit_type']) == 'seller_later_dispatch') { ?>
                              <select name="products[<?php echo $product['order_product_id']; ?>][edit_type]"
                                      class="form-control required edit_type select_combo_product_id_<?php echo $product['combo_product_id']; ?>"
                                      id="rmv_pro_edit_type_<?php echo $product['order_product_id']; ?>"
                                      data-order-product-id="<?php echo $product['order_product_id']; ?>"
                                      data-combo-product-id="<?php echo $product['combo_product_id']; ?>"
                                      data-product-id="<?php echo $product['product_id']; ?>"
                                      disabled >
                                <option value="">Select Edit Type</option>
                                <option value="CANCELLED_BY_CUSTOMER">CANCELLED BY CUSTOMER</option>
                                <option value="SELLER_NOT_SUPPLIED">SELLER NOT SUPPLIED</option>
                              </select>
                              <br>
                              <textarea name="products[<?php echo $product['order_product_id']; ?>][comment]"
                                        placeholder="Comment"
                                        class="form-control pull-left required comment-rqd text_area_combo_product_id_<?php echo $product['combo_product_id']; ?>"
                                        style="width:140px;resize: vertical" 
                                        id="rmv_pro_comment_<?php echo $product['order_product_id']; ?>"
                                        data-order-product-id="<?php echo $product['order_product_id']; ?>"
                                        data-combo-product-id="<?php echo $product['combo_product_id']; ?>"
                                        data-product-id="<?php echo $product['product_id']; ?>"
                                        disabled></textarea>
                            <?php } else { ?>
                                <span><?php echo str_replace("_", ' ', $product['edit_type']); ?>
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
                            <?php } ?>
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
                          <td>
                            <span><?php echo $sno;?></span>
                            <?php if (strtolower($product['edit_type']) == 'yes' ||
                                      strtolower($product['edit_type']) == 'seller_later_dispatch') {
                                  $editable_class = 'remove_editable';  ?>
                            <input id="checkbox_<?php echo $product['order_product_id']; ?>" 
                                   type="checkbox" 
                                   name="selected[]" value="<?php echo $product['order_product_id']; ?>"
                                   class="remove_product_checkbox check_combo_product_id_<?php echo $product['combo_product_id']; ?>"
                                   data-order-product-id="<?php echo $product['order_product_id']; ?>"
                                   data-combo-product-id="<?php echo $product['combo_product_id']; ?>" 
                                   data-product-id="<?php echo $product['product_id']; ?>" 
                                   data-product-quantity="<?php echo $product['quantity']; ?>"/>
                             <?php } ?>                                
                          </td>
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
                          </td>
                          <td width="150px">
                            <span><?php echo $product['comment']; ?></span>
                          </td>
                          <td style="width:150px!important" class="edit_type_td">
                            <?php if (strtolower($product['edit_type']) == 'yes' ||
                                      strtolower($product['edit_type']) == 'seller_later_dispatch') { ?>
                              <select name="products[<?php echo $product['order_product_id']; ?>][edit_type]"
                                      class="form-control required edit_type select_combo_product_id_<?php echo $product['combo_product_id']; ?>"
                                      id="rmv_pro_edit_type_<?php echo $product['order_product_id']; ?>"
                                      data-order-product-id="<?php echo $product['order_product_id']; ?>"
                                      data-combo-product-id="<?php echo $product['combo_product_id']; ?>"
                                      data-product-id="<?php echo $product['product_id']; ?>"
                                      disabled >
                                <option value="">Select Edit Type</option>
                                <option value="CANCELLED_BY_CUSTOMER">CANCELLED BY CUSTOMER</option>
                                <option value="SELLER_NOT_SUPPLIED">SELLER NOT SUPPLIED</option>
                              </select>
                              <br>
                              <textarea name="products[<?php echo $product['order_product_id']; ?>][comment]"
                                        placeholder="Comment"
                                        class="form-control pull-left required comment-rqd text_area_combo_product_id_<?php echo $product['order_product_id']; ?>"
                                        style="width:140px;resize: vertical"
                                        id="rmv_pro_comment_<?php echo $product['order_product_id']; ?>"
                                        data-order-product-id="<?php echo $product['order_product_id']; ?>"
                                        data-combo-product-id="<?php echo $product['combo_product_id']; ?>"
                                        data-product-id="<?php echo $product['product_id']; ?>"
                                        disabled></textarea>
                            <?php } else { ?>
                                <span><?php echo str_replace("_", ' ', $product['edit_type']); ?>
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
                            <?php } ?>
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
  var total_checkbox_checked = 0;
  $('.remove_product_checkbox').click(function(){
    var order_product_id = $(this).attr('data-order-product-id');
    var combo_product_id = $(this).attr('data-combo-product-id');
    var product_id = $(this).attr('data-product-id');
    var product_quantity = $(this).attr('data-product-quantity');
    
    if($(this).is(':checked')){
      if(product_id == combo_product_id) {
        $('#rmv_pro_edit_type_'+order_product_id).attr('disabled',false);  
        $('#rmv_pro_comment_'+order_product_id).attr('disabled',false);
        if(product_quantity != 1 ){
          $('#rm_prod_quan_pencil_'+order_product_id).removeClass('hidden');
        }        
      } else {
        $('.check_combo_product_id_'+combo_product_id).prop('checked',true);  
        $('.select_combo_product_id_'+combo_product_id).attr('disabled',false);  
        $('.text_area_combo_product_id_'+combo_product_id).attr('disabled',false);
        if(product_quantity != 1 ){
          $('.rm_prod_quan_pencil_'+combo_product_id).removeClass('hidden');
        }
      }
      
      total_checkbox_checked++;
    } else {
      if(product_id == combo_product_id) {
        $('#rmv_pro_edit_type_'+order_product_id).attr('disabled',true).val('');  
        $('#rmv_pro_comment_'+order_product_id).attr('disabled',true).val('');
        $('#rm_prod_quan_pencil_'+order_product_id).addClass('hidden');
        $('#rmv_pro_edit_input_block_'+order_product_id).addClass('hidden');
        $('#rmv_pro_edit_input_default_'+order_product_id).removeClass('hidden');
      } else {
        $('.check_combo_product_id_'+combo_product_id).prop('checked',false);  
        $('.select_combo_product_id_'+combo_product_id).attr('disabled',true).val('');  
        $('.text_area_combo_product_id_'+combo_product_id).attr('disabled',true).val('');
        $('.rm_prod_quan_pencil_'+combo_product_id).addClass('hidden'); 
        $('.rmv_pro_edit_input_block_'+combo_product_id).addClass('hidden');
        $('.rmv_pro_edit_input_default_'+combo_product_id).removeClass('hidden');
      }
      
      total_checkbox_checked--;
    }

    if(!total_checkbox_checked){
      $('#rmv_pro_button').attr('disabled',true);
    } else {
      $('#rmv_pro_button').attr('disabled',false);
    }

    $('.edit_type').on('change',function(){
      var order_product_id = $(this).attr('data-order-product-id');
      var combo_product_id = $(this).attr('data-combo-product-id');
      var product_id = $(this).data('product-id');
      var selected_value = $(this).val();
      if(combo_product_id == product_id) {
        $('#rmv_pro_edit_type_'+order_product_id).val(selected_value);  
      } else {
        $('.select_combo_product_id_'+combo_product_id).val(selected_value);  
      }
    });


    $('.comment-rqd').on('change',function(){
      var order_product_id = $(this).attr('data-order-product-id');
      var combo_product_id = $(this).attr('data-combo-product-id');
      var product_id = $(this).data('product-id');
      var comment_data = $(this).val();
      if(combo_product_id == product_id) {
        $('#rmv_pro_comment_'+order_product_id).val(comment_data);
      } else {
        $('.text_area_combo_product_id_'+combo_product_id).val(comment_data);
      }      
    });
  });

  // save button
  $('.rmv_prod_save_button').click(function(){
      
    var combo_product_ids = [];
    var error_flag = false;
    
    $('.remove_product_checkbox').each(function(){
      var order_product_id = $(this).attr('data-order-product-id');
      var combo_product_id = $(this).attr('data-combo-product-id');
      var product_id       = $(this).attr('data-product-id');
      var new_quantity_value = $('#rmv_quan_input_'+order_product_id).val();
      var old_quantity_value = $('#rmv_quan_input_'+order_product_id).attr('data-old-quantity');
      
      if($(this).is(':checked')){
        if(product_id == combo_product_id){
          var txtar = $('#rmv_pro_comment_'+order_product_id).val();
          var select_val = $('#rmv_pro_edit_type_'+order_product_id).val();
          if(new_quantity_value == old_quantity_value){
            $('#rmv_quan_input_'+order_product_id).attr('disabled',true);
            $('#hidden_rmv_quan_input_'+order_product_id).attr('disabled', true); 
          }
          if(new_quantity_value <= 0 ){
            alert('Quantity must not be empty or zero.');
            return false;
          }
          if( select_val == '' || txtar.trim().length == 0){
            error_flag = true;
          }  
        } else {
          var txtar = $('.text_area_combo_product_id_'+combo_product_id).val();
          var select_val = $('.select_combo_product_id_'+combo_product_id).val();
          if(new_quantity_value == old_quantity_value){
            $('.rmv_quan_input_'+combo_product_id).attr('disabled',true);
            $('.hidden_rmv_quan_input_'+combo_product_id).attr('disabled', true); 
          }
          if(new_quantity_value <= 0 ){
            alert('Quantity must not be empty or zero.');
            return false;
          }
          if(select_val == '' || txtar.trim().length == 0){
            error_flag = true;
          }  
        }        
      }        
    });
      
    if(error_flag) {
      alert("Please select edit type and enter the comment !!");
      return false;
    }      
  });

  // edit quantity input (pencil sign)
  $('.remove_pro_edit_quan').click(function(){
    var order_product_id = $(this).attr('data-order-product-id');
    var combo_product_id = $(this).attr('data-combo-product-id');
    var product_id       = $(this).attr('data-product-id');
    if(product_id == combo_product_id){
      $('#rmv_pro_edit_input_block_'+order_product_id).removeClass('hidden');
      $('#rmv_pro_edit_input_default_'+order_product_id).addClass('hidden');
      $('#rmv_quan_input_'+order_product_id).attr('disabled', false);
      $('#hidden_rmv_quan_input_'+order_product_id).attr('disabled', false);
    } else {
      $('.rmv_pro_edit_input_block_'+combo_product_id).removeClass('hidden');
      $('.rmv_pro_edit_input_default_'+combo_product_id).addClass('hidden');
      $('.rmv_quan_input_'+combo_product_id).attr('disabled', false);
      $('.hidden_rmv_quan_input_'+combo_product_id).attr('disabled', false);
    }
  });

  // cancel (ban sign)
  $('.remove_pro_edit_quan_cancel').click(function(){
    var order_product_id = $(this).attr('data-order-product-id');
    var combo_product_id = $(this).attr('data-combo-product-id');
    var product_id       = $(this).attr('data-product-id');
    var old_quantity     = $(this).data('old-quantity');
    if(product_id == combo_product_id){
      $('#rmv_pro_edit_input_block_'+order_product_id).addClass('hidden');
      $('#rmv_pro_edit_input_default_'+order_product_id).removeClass('hidden');
      $('#rmv_quan_input_'+order_product_id).attr('disabled', true); 
      $('#rmv_quan_input_'+order_product_id).val(old_quantity); 
      $('#hidden_rmv_quan_input_'+order_product_id).attr('disabled', true); 
    } else {
      $('.rmv_pro_edit_input_block_'+combo_product_id).addClass('hidden');
      $('.rmv_pro_edit_input_default_'+combo_product_id).removeClass('hidden');  
      $('.rmv_quan_input_'+combo_product_id).attr('disabled', true);
      $('.rmv_quan_input_'+combo_product_id).val(old_quantity);
      $('.hidden_rmv_quan_input_'+combo_product_id).attr('disabled', true); 
    }    
  });

  $('.rmv_quan_input').on('change',function(){
    var order_product_id = $(this).data('order-product-id');
    var combo_product_id = $(this).data('combo-product-id');
    var product_id = $(this).data('product-id');
    var quantity_data = $(this).val();
    var old_quantity = $(this).data('old-quantity');

    if(combo_product_id == product_id) {
      $('#rmv_quan_input_'+order_product_id).val(quantity_data);
    } else {
      $('.rmv_quan_input_'+combo_product_id).val(quantity_data);
    } 

    if(quantity_data <= 0 ){
      alert('Quantity must not be empty or zero.');
      return false;
    }

    if(quantity_data == old_quantity){
      alert('You can not partial!!' );
      return false;
    }

    if(quantity_data > old_quantity){
      alert('You can select max ' + ( old_quantity - 1 ) + ' and minimum 1' );
      return false;
    }


         
  });

</script>