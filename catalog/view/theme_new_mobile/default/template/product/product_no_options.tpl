<?php if($options){ ?>
<div class="row whitebox ">
  <div class="col-sm-12 ">
    <div>


      <h2 class="scroll_to_options"><?php echo $text_option; ?></h2>
      <?php $js_var='';foreach ($options as $option) { ?>
      <?php // echo '<pre>'; print_r($option); ?>
      <?php if ($option['type'] == 'select') { ?>
      <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
        <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
        <select name="option[<?php echo $option['product_option_id']; ?>]" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control">
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
       </select>
        <?php //echo "<pre>"; print_r($js_var);?>
        <!--<?php// echo $price_prefix; ?>-->
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


    </div>
  </div>
</div>
<script type="text/javascript">
  
$(document).ready(function () {
  //add to cart
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
      data: $('#product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea'),
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

              if (element.parent().hasClass('input-group')) {
                element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
              } else {
                element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
              }
            }
          }

          if (json['error']['recurring']) {
            $('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</iv>');
          }

          // Highlight any found errors
          $('.text-danger').parent().addClass('has-error');
        }

        if (json['success']) {
          //$('.breadcrumb').after('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

         if($("#cart_shopping_popup").hasClass('in') == false)
         {
           $('#product').after( json['success'] );
         }          

          $('#cart > button#cart_btn', window.parent.document).html('<i class="fa fa-shopping-cart"></i> ' + json['total']);
          $('#cart > button#cart_btn_mobile', window.parent.document).html('<i class="fa fa-shopping-cart"></i> <span id="cart-total">' + json['total_in_cart']+'</span>');

          //jQuery('#continue_shop').show(fade);

          $('#cart > ul').load('index.php?route=common/cart/info ul li');
        }
      }
    });
  });
}
  //end add to cart
})

</script>
<?php }?>