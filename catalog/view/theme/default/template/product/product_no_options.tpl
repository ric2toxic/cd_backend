<div id="product" class="product_detail_cart col-sm-12">
    <?php if ($options) { ?>
    <hr>
    <h3><?php echo $text_option; ?></h3>
    <?php $js_var='';foreach ($options as $option) { ?>
    <?php //echo '<pre>'; print_r($option); ?>
    <?php if ($option['type'] == 'select') { ?>
    <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
      <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
      <?php /* ?> <!--<select name="option[<?php echo $option['product_option_id']; ?>]" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control">
        <option value=""><?php echo $text_select; ?></option>
        <?php foreach ($option['product_option_value'] as $option_value) { ?>
        <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
          <?php if ($option_value['price']) { ?>
          (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
          <?php } ?>
        </option>
        <?php $js_var[] = array( $option_value['price_prefix'], filter_var($option_value['unfor_option_price'], FILTER_SANITIZE_NUMBER_FLOAT), $option_value['product_option_value_id'] );?>

        <?php //; $x = trim($option_value['price']) ; $price_prefix .= "<input type = hidden
  #id = price_prefix_$option_value[product_option_value_id]
  #value = $option_value[price_prefix] /><input type = 'hidden'
                                                #id = option_value_$option_value[product_option_value_id]
                                                #value = $x />" ; ?>

        <?php } ?>
      </select> -->
      <?php */ ?>


      <ul id="input-option<?php echo $option['product_option_id']; ?>" class="col-sm-12 singleproductsize form-control" name="option[<?php echo $option['product_option_id'];?>]">
        <?php foreach ($option['product_option_value'] as $option_value) {
        if($option_value['quantity'] == 0){
        $disabledClass = "singleproductsizelistdisabled";
        }else{
        $disabledClass = "singleproductsizelist";
        }
        ?>
        <li data-name="option[<?php echo $option['product_option_id']; ?>]" data-value="<?php echo $option_value['product_option_value_id']; ?>" id="input-option-radio<?php echo $option_value['product_option_value_id']; ?>" class="<?php echo $disabledClass;?>">
          <div class="sizeChecked">
            <input type="radio" data-value-selected="<?php echo $option_value['product_option_value_id']; ?>" name="option[<?php echo $option['product_option_id']; ?>]" id="input-option-radio-section<?php echo $option_value['product_option_value_id']; ?>" class="sizeSelected"  value="<?php echo $option_value['product_option_value_id']; ?>" />
          </div>
          <?php echo $option_value['name']; ?>
          <?php if ($option_value['price']) { ?>
          (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
          <?php } ?>
          <?php $js_var[] = array( $option_value['price_prefix'], filter_var($option_value['unfor_option_price'], FILTER_SANITIZE_NUMBER_FLOAT), $option_value['product_option_value_id'] );?>

          <?php //; $x = trim($option_value['price']) ; $price_prefix .= "<input type = hidden
  #id = price_prefix_$option_value[product_option_value_id]
  #value = $option_value[price_prefix] /><input type = 'hidden'
                                                #id = option_value_$option_value[product_option_value_id]
                                                #value = $x />" ; ?>
        </li>
        <?php } ?>
      </ul>


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
        <div class="col-md-12 col-sm-12 col-xs-12 qtybox">
          <!--<label class="control-label  " for="input-quantity"><?php //echo $entry_qty; ?></label> <br />-->
          <div class="col-sm-5 quantity_text">Quantity:</div>
          <div class="col-sm-7 nopadding">
            <input id="input-quantity"
                   type="text"
                   value="<?php echo $minimum; ?>"
                   name="quantity"
                   data-bts-min="<?php echo $minimum; ?>"
                   data-bts-max="<?php echo $quantity; ?>"
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
                   data-bts-button-down-class="btn btn-default"
                   data-bts-button-up-class="btn btn-default"
                   size="2"
                   class="form-control  pull-left touchspin_qty"
            />          
          </div>
          <div class="btn_addtocart col-sm-12 nopadding">
            <?php if ($quantity <= 0 || strtolower($stock_status) == 'out of stock' || $status== 0 ) { ?>
            <button type="button" id="button-cart-design" data-product-id="<?php echo $product_id; ?>" data-loading-text="<?php echo $text_loading; ?>" class="addtocart btn btn-lg comment_btn col-sm-6" data-product-id="<?php echo $product_id; ?>"><i class="fa fa-shopping-cart"></i> <?php echo $i_want_this_design; ?></button>
            <div class="out-of-stock col-sm-6">Out Of Stock!!!</div>
            <?php }else{ ?>
            <button type="button"  id="button-cart" data-product-id="<?php echo $product_id; ?>" data-loading-text="<?php echo $text_loading; ?>" class="addtocart btn btn-lg col-sm-12"><i class="fa fa-shopping-cart"></i> <?php echo $button_cart; ?></button>
            <?php } ?>
            <!--<button type="button" <?php if ($quantity <= 0 || strtolower($stock_status) == 'out of stock' || $status== 0 ) { echo "disabled"; } ?> id="button-cart" data-loading-text="<?php echo $text_loading; ?>" class="addtocart btn btn-lg " style="margin-right: 5px"><i class="fa fa-shopping-cart"></i> <?php echo $button_cart; ?></button>-->
          </div>

          <!--<input type="text" name="quantity" value="<?php echo $minimum; ?>" size="2" id="input-quantity" class="form-control  pull-left" />-->
          <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
        </div>
      </div>
    </div>

  </div>         
<script type="text/javascript">
  $(document).ready(function () { 
    $("input[name='quantity']").TouchSpin({ });
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
      url: 'index.php?route=checkout/cart/add&popup=true',
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

          if (json['error']['recurring']) {
            $('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
          }

          // Highlight any found errors
          $('.text-danger').parent().addClass('has-error');
        }

        if (json['success']) {
          //$('.breadcrumb').after('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
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