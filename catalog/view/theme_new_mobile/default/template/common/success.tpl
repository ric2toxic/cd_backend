<?php echo $header; ?>
<style type="text/css">
  .mobile-margin{display: none!important;}
   #header-mobile{display: none!important;}
</style>


<div class="container success_page" style="background-color: #fff;">

  <div class="cart_main_title main_title_active" style="border-bottom: 2px solid #000;
    -webkit-transition: border-bottom 0.6s;
    transition: border-bottom 0.6s;
    color: #000;
    background: #fff; border-radius: 0;
    padding: 10px 0px;
    margin-top: 0px;">
    <h4 class="panel-title shopping_cart_title" style="margin-top: 0;
    margin-bottom: 0;
    font-size: 16px; color: #000!important; display: block;">
    <a class="accordion-toggle" style="display: block;
    font-size: 17px;
    font-weight: 600;"><?php echo $text_success; ?></a></h4></div>

  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <h1 style="color: #636363;
    margin-top: 10px;
    font-size: 20px;"><i class="fa fa-check-circle" aria-hidden="true" style="font-size: 21px;
    color: #008000;"></i> <?php echo $heading_title; ?></h1>

     <div>
      <?php if($web_engage_data['payment_mode'] != 'wsb_credit') { ?>
        <p style="padding-left: 0px; margin-top: 5px;">
            <a href="<?php echo $credit_application_link; ?>" target="_blank">
                <img class="img-responsive" src="https://cdnimages.net/img/creditwithcashback_NEWEST.png">
            </a>
        </p>
     <?php } ?>   

    </div>


    <?php if(!isset($register)) { ?> 
      <div id="shipping_preferences" style=" border: 1px solid #ccc; border-radius: 5px; ">
        <div id="packaging_preference" style=" padding: 10px;">
          <div style="margin-bottom: 10px; font-size: 16px;">
            <label><strong><?php echo $text_packaging_preference ?></strong></label>
          </div>
          
          <div>
            <input type="checkbox" id="no_wsb_tape">
            <label style="vertical-align: super; margin-left: 5px; font-weight: 200;"><?php echo $text_no_wsb_tape ?></label>
          </div>
          
          <div>
            <input type="checkbox" id="no_invoice_with_shipment">
            <label style="vertical-align: super; margin-left: 5px; font-weight: 200;"><?php echo $text_no_offline_invoice ?></label>
          </div>
        </div>
        
        
        <div id="courier_preference" style="padding: 10px;">
          <div style="font-size: 16px;">
            <label><strong><?php echo $text_courier_preferences ?></strong></label>
          </div>
          <?php foreach($courier_partners as $courier_partner) { ?>
            <div style="display: inline-block; margin: 5px;">
              <input type="radio" name="courier_partner" value="<?php echo $courier_partner['courier_name'] ?>" style="vertical-align: text-bottom;">
                <?php echo $courier_partner['courier_name'] ?>
              </input>
            </div>
          <?php } ?>
          <div>
            <br />
            <label><strong><?php echo $text_shipping_preferences_note; ?></strong></label>
          </div>
        </div>
        
        <div>
          <button id="submit_shipping_preferences"  class="btn btn-primary success_button" style="background-color:#233c98;border-radius: 0px 0px 5px 5px; padding: 10px;font-size: 16px; width:100%" /><?php echo $button_submit; ?></button>
        </div>

        <?php if (isset($custom_duty_charge_message)) { ?>
        <p style="margin-top: 10px; margin-bottom: 20px;"><?php echo $custom_duty_charge_message; ?></p>
        <?php } ?>
        
      </div>
        

       <?php } ?>

     <div class="text_message" style="margin-bottom: 11%; padding: 10px 0px 10px 5px;">
      <?php echo $text_message; ?>
      </div>

         
      <div class="buttons" style="position: fixed;bottom: 0px; width: 100% !important; left: 0;">
        <div class="pull-right" style="width:100%;"><a href="<?php echo $continue; ?>" style="width:100%;" class="btn btn-primary success_button">CONTINUE SHOPPING</a></div>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>

<?php
if(isset($gaTracking)) {
  if($gaTracking) {
    echo "<script>" . "\n";
    echo "ga('require', 'ecommerce');" . "\n";
    echo $gaTracking;
    echo "ga('ecommerce:send');" . "\n";
    echo "</script>" . "\n";
    }
  }
?>

<?php echo $footer; ?>
<style type="text/css">
 footer {display: none !important; } 
</style>
<!-- UPI help model -->
<div id="upi_help_popup" class="modal fade" role="dialog">
    <div class="modal-dialog" style="top:4%;z-index: 1050;">
        <div class="modal-header help_popup_head" style="border-bottom: 0px;">
            <button type="button" class="close close_btn_payment_info" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
        </div>
        <div class="modal-content">
            <img class="img-responsive" src="image/upi_help_mobile.jpg"/>
        </div>
    </div>
</div>

<script>
  $(document).ready(function(){
    $('.checkout_step4').show();

    $(window).scroll(function(){
      if ($(this).scrollTop() > 0) {
        $('.checkout_steps').css('position','fixed');
      } else {
        $('.checkout_steps').css('position','relative');
      }
    });

    var getTagId = '<?php echo (isset($ecomm_tagging_id)?$ecomm_tagging_id:"");?>';
    var getTagRevenue = '<?php echo (isset($ecomm_tagging_revenue)?$ecomm_tagging_revenue:"");?>';
    // Send transaction data with a pageview if available
    // when the page loads. Otherwise, use an event when the transaction
    // data becomes available.
    if(getTagId.length > 0 && getTagRevenue.length > 0) {
        dataLayer.push({
            'ecommerce': {
                'purchase': {
                    'actionField': {
                        'id': getTagId,
                        'revenue': getTagRevenue
                    }
                }
            }
        });
    }
  });
</script>
<script>
    var customer_id = getCookie('customer_id');
    localStorage.removeItem(customer_id+'_cart_data');
</script>
<script>
    $('#submit_shipping_preferences').on('click', function() {
        $('#submit_shipping_preferences').button('loading');
        var no_wsb_tape_data = document.getElementById('no_wsb_tape');
        var no_wsb_tape = no_wsb_tape_data.checked ? 1 : 0;
      
        var no_invoice_with_shipment_data = document.getElementById('no_invoice_with_shipment');
        var no_invoice_with_shipment = no_invoice_with_shipment_data.checked ? 1 : 0;
        
        var courier_partner_preference = $('input[name=courier_partner]:checked').val();
        
        $.ajax({
            url: 'index.php?route=account/account/updateShippingPreferences&ctoken=<?php echo $ctoken; ?>',
            type: 'post',
            dataType: 'json',
            data: {
                'order_id': '<?php echo $order_id; ?>',
                'no_wsb_tape':no_wsb_tape,
                'no_invoice_with_shipment': no_invoice_with_shipment,
                'courier_partner_preference': courier_partner_preference
            },
            success: function(json) {
                if (json['error']) {
                    alert(json['error']);
                }
                $('#submit_shipping_preferences').button('reset');
                if (json['success']) {
                    $('#shipping_preferences').html('<label style="color:green; margin: 10px; font-size: 16px;">'+json['success']+'</label>');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                $('#submit_shipping_preferences').button('reset');
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

if(<?php echo $customer_data['is_dropshipper']; ?> == 1)
{
  var dropshipper = true;
}
else
{
   var dropshipper = false;
}

<?php if(!isset($register)) { ?> 

 dataLayer.push({'transaction_id': "<?php echo $web_engage_data['transaction_id']; ?>"});
 dataLayer.push({'payment_mode': "<?php echo $web_engage_data['payment_mode']; ?>"});
 dataLayer.push({'product_ids': "<?php echo implode(',', $web_engage_data['product_ids']); ?>"});
 dataLayer.push({'product_names': "<?php echo implode(',', $web_engage_data['product_names']); ?>"});
 dataLayer.push({'category_ids': "<?php echo implode(',', $web_engage_data['category_ids']); ?>"});
 dataLayer.push({'category_names': "<?php echo implode(',', $web_engage_data['category_names']); ?>"});
 dataLayer.push({'products_price': "<?php echo implode(',', $web_engage_data['products_price']); ?>"});
  dataLayer.push({'cart_item': <?php echo $web_engage_data['cart_item']; ?>});
  dataLayer.push({'cart_value': <?php echo $web_engage_data['cart_value']; ?>});
  dataLayer.push({'customer_id': <?php echo $web_engage_data['customer_id']; ?>});
  dataLayer.push({'phone': <?php echo $web_engage_data['telephone']; ?>});
  dataLayer.push({'payment_postcode': <?php echo $web_engage_data['payment_postcode']; ?>});
  dataLayer.push({'shipping_postcode': <?php echo $web_engage_data['shipping_postcode']; ?>});
  dataLayer.push({'number_of_unique_sku': <?php echo $web_engage_data['number_of_unique_sku']; ?>});
  dataLayer.push({'order_total': <?php echo $web_engage_data['order_total']; ?>});

  dataLayer.push({'email': "<?php echo $web_engage_data['email']; ?>"});
  dataLayer.push({'order_date': "<?php echo $web_engage_data['date_added']; ?>"});
  dataLayer.push({'payment_city': "<?php echo $web_engage_data['payment_city']; ?>"});
  dataLayer.push({'shipping_city': "<?php echo $web_engage_data['shipping_city']; ?>"});
  dataLayer.push({'seller_ids': "<?php echo implode(',', $web_engage_data['seller_id']); ?>"});
  dataLayer.push({'seller_nicknames': "<?php echo implode(',', $web_engage_data['seller_nickname']); ?>"});
  dataLayer.push({'checkout_mode': "<?php echo $web_engage_data['order_from']; ?>"});
 dataLayer.push({'event': 'we-custom-checkout-completed'}); 

<?php } else { ?>

dataLayer.push({'event': 'we-custom-logout'});

dataLayer.push({'customer_id': <?php echo $customer_data['customer_id']; ?>, 
                'first_name': "<?php echo $customer_data['firstname']; ?>", 
                'last_name': "<?php echo $customer_data['lastname']; ?>", 
                'email': "<?php echo $customer_data['email']; ?>", 
                'phone': <?php echo $customer_data['telephone']; ?>,
                'customer_type': "<?php echo $customer_data['customer_type_id']; ?>",
                'store_id': 0,
                'user_city': "<?php echo $customer_data['city']; ?>",
                'gst': <?php echo $customer_data['gst_number']; ?>,
                'dropshipper': dropshipper,
                'has_website': parseInt(<?php echo $customer_data['has_website']; ?>, 10),
                'self_order': "<?php echo $customer_data['self_order']; ?>",
                'pincode': parseInt(<?php echo $customer_data['postcode']; ?>, 10),
                'membership': "<?php echo $customer_data['membership']; ?>" });


dataLayer.push({'event': 'we-custom-login'});

<?php } ?>  


</script>

<script type="text/javascript">
    if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('/sw.js').then(function(registration) {
      // Registration was successful
      console.log('ServiceWorker registration successful with scope: ', registration.scope);
    }, function(err) {
      // registration failed :(
      console.log('ServiceWorker registration failed: ', err);
    });
  });
}
</script>