<div id="product" class="product_detail_cart col-sm-12">
    <?php if ($options) { //echo "<pre>";print_r($options); die;?>
    <h3><?php echo $text_option; ?></h3>
    <?php $js_var='';foreach ($options as $option) { ?>
    <?php if ($option['type'] == 'select') { ?>
    <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
      <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
      <div class="options_table">
      <table class="table-responsive" width="100%">
        <tbody>
        <tr>
          <td>Size</td>
          <td>Available</td>
          <td>Quantity</td>
        </tr>
    <?php foreach ($option['product_option_value'] as $option_value) {
    if($option_value['quantity'] == 0){
    $disabledClass = "singleproductsizelistdisabled";
    }else{
    $disabledClass = "singleproductsizelist";
    }
    ?>

        <tr class="table_data_weight fix_height_options">
          <td><?php 
          if($option_value['image']) { ?>
          <div class="mini_options_image"><a class="thumbnail getcurrentImg" html-data="<?php echo $option_value['image']; ?>" href="<?php echo $option_value['image']; ?>" title="<?php echo $heading_title; ?>"> <img src="<?php echo $option_value['image_thumb'];?>" /></a></div>

          <?php } else {
            echo $option_value['name'];
          }
           ?></td>
          <td> 
          <?php if(!$option_value['quantity']){ ?>
       Out of stock!!
          <?php }else { ?>
          <?php if($piece_in_set > 1){ ?>
            <?php echo $option_value['quantity'] ?>&nbsp;<?php echo $text_available_set ?>
          <?php } else { ?>
            <?php echo $option_value['quantity'] ?>&nbsp;<?php echo $text_available_pieces ?>
          <?php } ?>
      <?php } ?>
          </td>

          <td><input id="option-input_<?php echo $option_value['product_option_value_id']; ?>"
                         type="text"
                         value="0"
                         name="option_quantities[<?php echo $option['product_option_id']; ?>][<?php echo $option_value['product_option_value_id']; ?>]"
                         data-bts-min="0"
                         data-bts-max="<?php echo $option_value['quantity']; ?>"
                         data-bts-init-val=""
                         data-bts-step="1"
                         data-bts-decimal="0"
                         data-bts-step-interval="100"
                         data-bts-force-step-divisibility="round"
                         data-bts-step-interval-delay="500"
                         data-bts-prefix=""
                         data-bts-postfix=""
                         data-bts-prefix-extra-class=""
                         data-bts-postfix-extra-class=""
                         data-bts-booster="true"
                         data-bts-boostat="10"
                         data-bts-max-boosted-step="false"
                         data-bts-mousewheel="true"
                         data-bts-button-down-class="btn btn-default height_adjusted_options"
                         data-bts-button-up-class="btn btn-default height_adjusted_options"
                         size="2"
                         <?php 
                         if(!$option_value['quantity']) { 
                         echo "style='background:#f2f2f2' ";
                         echo "readonly='readonly'";
                         }?>
                         class="input_qty  pull-left touchspin_qty"
                  />
                  <span style="display:none;"  id="span_input_<?php echo $option_value['product_option_value_id']; ?>" class="qty_span_hid">Max <?php echo $option_value['quantity']; ?>!!</span>
                  <input type="hidden" value="<?php echo $option_value['quantity']; ?>" name="option_max_quantities[<?php echo $option['product_option_id']; ?>][<?php echo $option_value['product_option_value_id']; ?>]" >
                  </td>
        </tr>


          <?php if ($option_value['price']) { ?>
          (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
          <?php } ?>
          <?php $js_var[] = array( $option_value['price_prefix'], filter_var($option_value['unfor_option_price'], FILTER_SANITIZE_NUMBER_FLOAT), $option_value['product_option_value_id'] );?>

          <?php //; $x = trim($option_value['price']) ; $price_prefix .= "<input type = hidden
  #id = price_prefix_$option_value[product_option_value_id]
  #value = $option_value[price_prefix] /><input type = 'hidden'
                                                #id = option_value_$option_value[product_option_value_id]
                                                #value = $x />" ; ?>
        <?php } ?>
          </tbody>
        </table>
        </div>


      <?php //echo "<pre>"; print_r($js_var);?>
      <?php // echo $price_prefix; ?>
      <?php //echo $x; ?>
      <?php //echo $option_value['price'];?>
    </div>
    <?php } ?>
    <?php if ($option['type'] == 'radio') { ?>
    <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
      <label class="control-label"><?php echo $option['name']; ?></label>
      <div id="input-option<?php echo $option['product_option_id']; ?>">
        <?php foreach ($option['product_option_value'] as $option_value) { ?>
        <div class="radio">
          <label>
            <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" />
            <?php echo $option_value['name']; ?>
            <?php if ($option_value['price']) { ?>
            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
            <?php } ?>
          </label>
        </div>
        <?php } ?>
      </div>
    </div>
    <?php } ?>
    <?php if ($option['type'] == 'checkbox') { ?>
    <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
      <label class="control-label"><?php echo $option['name']; ?></label>
      <div id="input-option<?php echo $option['product_option_id']; ?>">
        <?php foreach ($option['product_option_value'] as $option_value) { ?>
        <div class="checkbox">
          <label>
            <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" />
            <?php echo $option_value['name']; ?>
            <?php if ($option_value['price']) { ?>
            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
            <?php } ?>
          </label>
        </div>
        <?php } ?>
      </div>
    </div>
    <?php } ?>
    <?php if ($option['type'] == 'image') { ?>
    <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
      <label class="control-label"><?php echo $option['name']; ?></label>
      <div id="input-option<?php echo $option['product_option_id']; ?>">
        <?php foreach ($option['product_option_value'] as $option_value) { ?>
        <div class="radio">
          <label>
            <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" />
            <img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" class="img-thumbnail" /> <?php echo $option_value['name']; ?>
            <?php if ($option_value['price']) { ?>
            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
            <?php } ?>
          </label>
        </div>
        <?php } ?>
      </div>
    </div>
    <?php } ?>
    <?php if ($option['type'] == 'text') { ?>
    <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
      <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
      <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
    </div>
    <?php } ?>
    <?php if ($option['type'] == 'textarea') { ?>
    <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
      <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
      <textarea name="option[<?php echo $option['product_option_id']; ?>]" rows="5" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control"><?php echo $option['value']; ?></textarea>
    </div>
    <?php } ?>
    <?php if ($option['type'] == 'file') { ?>
    <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
      <label class="control-label"><?php echo $option['name']; ?></label>
      <button type="button" id="button-upload<?php echo $option['product_option_id']; ?>" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-default btn-block"><i class="fa fa-upload"></i> <?php echo $button_upload; ?></button>
      <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" id="input-option<?php echo $option['product_option_id']; ?>" />
    </div>
    <?php } ?>
    <?php if ($option['type'] == 'date') { ?>
    <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
      <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
      <div class="input-group date">
        <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
  <span class="input-group-btn">
  <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
  </span></div>
    </div>
    <?php } ?>
    <?php if ($option['type'] == 'datetime') { ?>
    <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
      <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
      <div class="input-group datetime">
        <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
  <span class="input-group-btn">
  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
  </span></div>
    </div>
    <?php } ?>
    <?php if ($option['type'] == 'time') { ?>
    <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
      <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
      <div class="input-group time">
        <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
  <span class="input-group-btn">
  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
  </span></div>
    </div>
    <?php } ?>
    <?php } ?>
    <?php } ?>


    <?php if ($recurrings) { ?>
    <hr>
    <h3><?php echo $text_payment_recurring ?></h3>
    <div class="form-group required">
      <select name="recurring_id" class="form-control">
        <option value=""><?php echo $text_select; ?></option>
        <?php foreach ($recurrings as $recurring) { ?>
        <option value="<?php echo $recurring['recurring_id'] ?>"><?php echo $recurring['name'] ?></option>
        <?php } ?>
      </select>
      <div class="help-block" id="recurring-description"></div>
    </div>
    <?php } ?>
    <div class="form-group">
      <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <?php if ($quantity <= 0 || strtolower($stock_status) == 'out of stock' || $status== 0 ) { ?>
            <div class="out-of-stock col-sm-12">Out Of Stock!!!</div>
            <button type="button" id="button-cart-design" data-loading-text="<?php echo $text_loading; ?>" class=" btn btn-lg col-sm-12" data-product-id="<?php echo $product_id; ?>" ><i class="fa fa-shopping-cart"></i> <?php echo $i_want_this_design; ?></button>
            <?php }else{ ?>
            <button type="button"  id="button-cart" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-lg button-cart-option col-sm-12"><i class="fa fa-shopping-cart"></i> <?php echo $button_cart; ?></button>
            <?php } ?>
          <!--<input type="text" name="quantity" value="<?php echo $minimum; ?>" size="2" id="input-quantity" class="form-control  pull-left" />-->
          <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
        </div>
      </div>
    </div>

  </div>
<script type="text/javascript">
  $(document).ready(function () {
    $(".input_qty").TouchSpin({ });
  });
</script>
<script type="text/javascript"><!--
  $('#button-cart').on('click', function() {
   if(getCookie("customer_mobile") == '')
        {
          $("input[name=redirect_cart]").val(1);
            $('#login_verify_popup').modal('show');
        }
    else if(getCookie("register_user") == 1 && getCookie("customer_id") == '')
       {
         $("input[name=redirect_cart]").val(1);
         $('#login_popup').modal('show');
       }
    else
    {  
    $.ajax({
      url: 'index.php?route=checkout/cart/addWithOptions&popup=true',
      type: 'post',
      data: $('#product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea, #product #option-input'),
      dataType: 'json',
      beforeSend: function() {
        $('#button-cart').button('loading');
      },
      complete: function() {
        $('#button-cart').button('reset');
      },
      success: function(json) {
        $('.alert, .text-danger').remove();
        $('.form-group').removeClass('has-error');
        // $('span').removeClass('remove-red');
        $('span.qty_span_hid').hide();
        $('.input_qty').removeClass("make_it_red");

        if (json['error']) {
          if (json['error']['option']) {
            for (i in json['error']['option']) {
              var element = $('#input-option' + i.replace('_', '-'));
              //alert(element.parent().hasClass('input-group'));
              if (element.parent().hasClass('input-group')) {

                element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
              } else {
                element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
              }
            }
          }         
          if (json['error']['option_value']) {
            for (product_option_id in json['error']['option_value']) {
              for (j in json['error']['option_value'][product_option_id]) {
                var element = $('#option-input_' + j);
                var element_span = $('#span_input_' + j);
                element.addClass("make_it_red");
                element_span.css("display","block");
                element_span.addClass("remove-red");
              }

            }
          }


          if (json['error']['recurring']) {
            $('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
          }

          // Highlight any found errors
          $('.text-danger').parent().addClass('has-error');
        }

        if (json['success']) {
 
         if($("#cart_shopping_popup").hasClass('in') == false)
         {
           $('#content').parent().before( json['success'] );
         }  

          $('#cart > button#cart_btn', window.parent.document).html('<i class="fa fa-shopping-cart"></i><span id="cart-total-desktop"> Cart <span class="cart_number">' + json['total_in_cart'] + '</span></span>');
          $('#cart > button#cart_btn_mobile', window.parent.document).html('<i class="fa fa-shopping-cart"></i> <span id="cart-total">' + json['total_in_cart']+'</span>');

          //jQuery('#continue_shop').show(fade);

          $('#cart > ul').load('index.php?route=common/cart/info ul li');
        }
      }
    });
  }
  });
  //--></script>




