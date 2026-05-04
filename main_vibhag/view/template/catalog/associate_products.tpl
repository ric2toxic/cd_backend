<div id="associate_products_content">
  <div class="container-fluid product_list">
    <div class="panel panel-default">
      <div class="panel-heading">
       <div class="row">
         <div class="col-sm-3">
            <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3><br><br>
         </div>
       </div>

      </div>

      <div class="panel-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td rowspan="2" class="text-center"><?php echo $column_image; ?></td>
                  <td rowspan="2" class="text-left"><?php echo $column_name; ?></td>
                  <td rowspan="2" class="text-left"><?php echo $column_wsb_product_code; ?></td>
                  <td rowspan="2" class="text-left"><?php echo $column_seller_sku; ?></td>
                  <td class="text-center" colspan="2"><?php echo $column_price ; ?></td>
                  <td colspan="1" >Selling Price</td>
                  <td rowspan="2" class="text-right"><?php echo 'Set Description';?></td>
                  <td rowspan="2" class="text-right"><?php echo $column_quantity; ?></td>
                  <td rowspan="2" class="text-left"><?php echo "Stock Status Info"; ?></td>
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
              <tbody id="associate_product_list">
                <?php if ($products) { ?>
                <?php foreach ($products as $product) { ?>
                <tr class="moderated_<?php echo $product['product_id'];?>" id="row_<?php echo $product['product_id'];?>_1"
                  <?php if($product['is_archived']) {?> style="background-color:#ffc966" <?php }?> >
                  <td rowspan="2" class="text-center">
                    <input type="hidden" name="associate_product_ids[]" value="<?php echo $product['product_id'];?>" />
                    <?php if ($product['image']) { ?>
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
                    <button type="button" class="btn btn-danger remove-associate-product"  value="<?php echo $product['product_id']; ?>"><i class="fa fa-trash-o"></i></button>
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
                  <td class="text-center" colspan="11"></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
      </div>
    </div>
  </div>
  
  <div class="container-fluid product_list">
    <div class="panel panel-default">
      <div class="panel-heading">
       <div class="row">
         <div class="col-sm-6">
            <h3 class="panel-title"><i class="fa fa-list"></i>Add Associate Products</h3><br><br>
         </div>
         
         <div class="col-sm-6">
           <button type="button" id="button-add-products" class="btn btn-primary pull-right" style="margin-right: 5px;">
             <i class="fa fa-plus" style="font-size: 12px;"></i> Add
           </button>
         </div>
       </div>
      </div>
      
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="form-group" style="margin-left: 10px; margin-right: 10px; ">
              <input type="checkbox" id="filter_solr_enable" checked/>
              <label>SOLR-Enabled</label>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-5">
              <div class="form-group" style="margin-left: 10px; margin-right: 10px; ">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
            </div>
            <div class="col-sm-5">
              <div class="form-group" style="margin-left: 10px; ">
                  <label class="control-label" for="input-model"><?php echo $entry_wsb_product_code; ?></label>
                  <input type="text" name="filter_model" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control" />
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                <button type="button" id="button-filter" class="btn btn-primary pull-right button-filter" style="margin-right: 5px; margin-top: 20%;">
                  <i class="fa fa-search"></i> <?php echo $button_filter; ?>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <div id="search_results">
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--

$('.button-filter').on('click', function() {

  var url = 'index.php?route=catalog/product/searchAssociateProducts&token=<?php echo $token; ?>';

  var filter_name = $('input[name=\'filter_name\']').val();
  var filter_model = $('input[name=\'filter_model\']').val();
  var filter_solr_enabled = $('#filter_solr_enable').is(":checked") ? 1: 0;
  
  var data = 'filter_name='+filter_name+'&filter_model='+filter_model+"&filter_solr_enabled="+filter_solr_enabled;

  $.ajax({
    url: url,
    dataType: 'json',
    type: 'post',
    data: data,
    success: function(json) {
      if (json['error']) {
        alert(json['error']);
      } else {
        $('#search_results').html(json['success']);
      }
    },
    error: function(xhr, ajaxOptions, thrownError) {
      alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
  });
  
});
//--></script>

<script type="text/javascript"><!--
  $('#button-add-products').click(function(){
    var checkbox_selected_value = new Array();
    var count = $('input[name=\'selected[]\']:checked').length;
    if(count > 0) {
      $('input[name=\'selected[]\']:checked').each(function () {
        var ele1 = "#row_" + $(this).val() + "_1";
        var ele2 = "#row_" + $(this).val() + "_2";
        $(ele1).find('td:first').remove();
        $(ele1).find('td:first').append('<input type="hidden" name="associate_product_ids[]" value="'+$(this).val()+'" />');
        $(ele1).find('td:last').append('<button type="button" class="btn btn-danger remove-associate-product" value="'+$(this).val()+'"><i class="fa fa-trash-o"></i></button>');
        
        $('#associate_product_list').append($(ele1));
        $('#associate_product_list').append($(ele2));
      });
    }else{
      alert('Please select at-least one product to add.');
    }
  });

//--></script>

<script type="text/javascript"><!--
  $('tbody').on("click", ".remove-associate-product", function(){
      var associate_product_id = $(this).val();
      var ele1 = "#row_" + associate_product_id + "_1";
      var ele2 = "#row_" + associate_product_id + "_2";
      $(ele1).remove();
      $(ele2).remove();
  });
//--></script>

