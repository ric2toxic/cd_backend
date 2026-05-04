<!DOCTYPE html  PUBLIC>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<!--<![endif]-->
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo $title; ?></title>
    <base href="<?php echo $base; ?>" />
    <?php if ($description) { ?>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php } ?>
    <?php if ($keywords) { ?>
    <meta name="keywords" content= "<?php echo $keywords; ?>" />
    <?php } ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php if ($icon) { ?>
    <link href="<?php echo $icon; ?>" rel="icon" />
    <?php } ?>
    <?php foreach ($links as $link) { ?>
    <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
    <?php } ?>
    <link rel="alternate" hreflang="x-default" href="<?php echo $co_store.$request_uri;?>" />
    <link rel="alternate" hreflang="en-in" href="<?php echo $in_store.$request_uri;?>" />
    <link rel="dns-prefetch" href="//fonts.googleapis.com"/>
    <link rel="dns-prefetch" href="//www.googletagmanager.com"/>

    <link href="//fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />
    <link href="<?php echo LOCAL_CDN_URL_SSL.COMMON_CSS; ?>" rel="stylesheet">
<style type="text/css">
.affix {
     top: 0;
     width: 100%;
 }
.affix + .container-fluid {
     padding-top: 70px;
 }
.bg_gray {background: #233c98;}
.navbar {min-height: 50px;}
.slider_navgation {display: block;float: left;width: 250px;}
.carousel-inner {overflow: hidden;position: relative;width: 100%;}
.b2bVideo {display: inline-block;float: right;padding: 7px 7px 20px;}
.offerBox {display: block;min-height: 130px;width: 180px;}
.home-product-list {width: 16.6%;}
.c-product-card__gallery{width: 42px;}
.steps .procedure {width: 77%;}
.steps .procedure .procedure_img_box {height: 90px;width: 90px;}
.tab_list li a h4 {font-size: 1.2em;}
.stats-bar ul li a { font-size: 24px;padding: 25px 30px;}
.cart_page {margin: 6% 0;}
.cart_statement_pree_load{width: 25%;min-height: 300px;}
.cart_full_box .cart_box .cart_main_title{border-radius: 0; padding: 17px 8px; margin-top:7px;}
.panel-body .cart_table .cart_table_height{ min-height: 400px; }
.li_position{position: absolute; list-style: none;}
.dropdown-toggle .menu_bar {left: -1px;min-width: 254px;top: 57px; padding: 0px;}
.sticky_menu_top_btn .menu_bar {left: -1px;min-width: 254px;top: 57px; height: 70px; }
.header_navigation li .nav_icon_bar{display: none;left: 0px;position: relative;list-style: none; height: 50px; width: 50px; padding: 10px;
    padding-bottom: 0px;}
.header_navigation li .nav_icon_bar:hover{background-color: transparent;}
.header_navigation li .nav_icon_bar .icon-bar{background-color: #17319f;border-radius: 1px;display: block;height: 4px; width: 28px;}
.header_navigation li .nav_icon_bar .icon-bar + .icon-bar {margin-top: 4px;}
.sticky_menu_top_btn {left: 23px;width: 50px;padding:0px; position: fixed; top: 13px; z-index: 999;}
.inner_page_menu_bar{ position: absolute; top: 33px; z-index: 999;display: block!important;left: 23px;width: 50px;padding: 0px; }
.inner_page_menu_bar .nav_icon_bar{display: block!important;}
</style>

<?php if ($international_store == 1) { ?>
    <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MBB945');</script>
    <!-- End Google Tag Manager -->
<?php } else { ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-TRP9H5');</script>
<!-- End Google Tag Manager -->
<?php } ?>

</head>

<body class="loaded">

<?php if ($international_store == 1) { ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MBB945"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
<?php } else { ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TRP9H5"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript)    -->
<?php } ?>

<div id="myProgress">
  <div id="myBar"></div>
</div>
<section id="header_box"></section>

<div class="container width_fix success_page account_page_bg">
  <ul class="breadcrumb">
   <!--  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>-->
  </ul>

<?php if(isset($thanks_image) && ($thanks_image == 1)){ ?>
  <div class="col-sm-12 checkout_steps">
    <div class="checkout_step4" id="checkout_step4"></div>
  </div>
<?php } ?>


  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <?php echo $column_right; ?>
    <div id="content" class="<?php echo $class; ?> box_shadow_none"><?php echo $content_top; ?>
      <?php if($method != 'upi'){?>
        <h1 class="account_title"><?php echo $heading_title; ?></h1>
      <?php }?>

     <div>
      <?php if($web_engage_data['payment_mode'] != 'wsb_credit') { ?>
       <div style="width: 800px;">
        <p style="padding-left: 0px; margin-top: 5px;">
            <a href="<?php echo $credit_application_link; ?>" target="_blank">
                <img class="img-responsive" src="https://cdnimages.net/img/credit_banner_english_new.png">
            </a>
        </p>
        </div>
     <?php } ?>   


      <?php echo $text_message; ?>
      
      <?php if(!isset($register)) { ?> 

      <?php if (isset($courier_partners)) { ?>
        <div id="shipping_preferences">
          <div id="packaging_preference" style="margin: 10px; padding: 10px;">
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
          
          <div id="courier_preference" style="margin: 10px; padding: 10px;">
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
            <button id="submit_shipping_preferences"  class="btn btn-primary success_button" style="background-color:#233c98; margin-left: 20px; margin-bottom: 20px;" />Submit</button>
          </div>
        </div>
      <?php } ?>
        <?php if (isset($custom_duty_charge_message)) { ?>
            <p style="margin-top: 10px; margin-bottom: 20px;"><?php echo $custom_duty_charge_message; ?></p>
        <?php } ?>

      <?php } ?>  
      <div class="buttons">
        <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary success_button"><?php echo $button_continue; ?></a></div>
      </div>
      <?php echo $content_bottom; ?></div>
    </div>
</div>


<section id="footer_box"></section>
<section id="login_box"></section>
<section id="opt_box"></section>
<section id="register_box"></section>
<section id="success_box"></section>
<div class="global-bg-layer"></div>
<div class="global-ajax-loader"></div>

<!-- UPI help model -->
<div id="upi_help_popup" class="modal fade" role="dialog">
    <div class="modal-dialog" style="top:4%;width:750px;z-index: 1050;">
        <div class="modal-header help_popup_head" style="border-bottom: 0px;">
            <button type="button" class="close close_btn_payment_info" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
        </div>
        <div class="modal-content">
            <img src="<?php echo STATIC_CONTENT_URL_SSL ?>upi_help.jpg" height="600" width="750"/>
        </div>
    </div>
</div>
<!-- jQuery -->
<script src="<?php echo LOCAL_CDN_URL_SSL.COMMON_VENDOR_JS; ?>"></script>
<script src="<?php echo LOCAL_CDN_URL_SSL.COMMON_REACT_JS; ?>"></script>

<script>

  var header_language     = <?php echo $header_language; ?>;
  var footer_language     = <?php echo $footer_language; ?>;
  var login_language      = <?php echo $login_language ; ?>;
  var international_store = <?php echo $international_store; ?>;
  var homePage='0';

  <?php if (isset($this->session->data['custom_store'])) {
        if ($this->session->data['custom_store'] == 'single') { ?>
var custom_store        = header_language.text_wholesale_store;
<?php  } else { ?>
var custom_store        = header_language.text_singles_store;
<?php  } }else { ?>
var custom_store        = header_language.text_singles_store;
<?php } ?>

ReactDOM.render(React.createElement(Header, {custom_store:custom_store, language:header_language, international_store:international_store},null ), document.getElementById('header_box'));
ReactDOM.render(React.createElement(Footer, {language:footer_language, international_store:international_store},null ), document.getElementById('footer_box'));
ReactDOM.render(React.createElement(Otpform, {language:login_language, international_store:international_store},null ), document.getElementById('opt_box'));
ReactDOM.render(React.createElement(Login, {language:login_language, international_store:international_store},null ), document.getElementById('login_box'));
ReactDOM.render(React.createElement(Register, {language:login_language, international_store:international_store},null ), document.getElementById('register_box'));
ReactDOM.render(React.createElement(Success, {language:login_language, international_store:international_store},null ), document.getElementById('success_box'));

</script>

<script type="text/javascript">
   $(document).ready(function() {
    $('.toggle_icon').click(function(){
     $(this).find('i').toggleClass('fa-plus-square-o fa-minus-square-o');
    });

    $('input[placeholder], textarea[placeholder]').not("input.serch_input_box, input.subscibe_input").placeholderLabel();
   // navcation Icons(header)
      $(window).on('scroll',function(){
          if($('.full_header').hasClass('affix')){
            $('.header_navigation').find('.nav_icon_bar_bottem').hide();
            $('.header_navigation').find('li').eq(0).removeClass('inner_menu_icon');
          }else{
            $('.header_navigation').find('.nav_icon_bar_bottem').show();
            $('.header_navigation').find('li').eq(0).addClass('inner_menu_icon');
          }
        });
       $("header").affix({offset: {top: $(".sub_navbar").outerHeight(true)} });

 });
</script>

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

<script>
    var getTagId = '<?php echo (isset($ecomm_tagging_id)?$ecomm_tagging_id:"");?>';
    var getTagRevenue = '<?php echo (isset($ecomm_tagging_revenue)?$ecomm_tagging_revenue:"");?>';
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
</script>
<script>
    function getCookie(c_name) {
        if (document.cookie.length > 0) {
            c_start = document.cookie.indexOf(c_name + "=");
            if (c_start != -1) {
                c_start = c_start + c_name.length + 1;
                c_end = document.cookie.indexOf(";", c_start);
                if (c_end == -1) {
                    c_end = document.cookie.length;
                }
                return unescape(document.cookie.substring(c_start, c_end));
            }
        }
        return "";
    }
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


 //fb purchase
dataLayer.push({'fb_order_total': "<?php echo $web_engage_data['order_total']; ?>"});
dataLayer.push({'fb_transaction_id': "<?php echo $web_engage_data['transaction_id']; ?>"});
dataLayer.push({'event': 'fb-custom-checkout-completed'});

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
</html>
