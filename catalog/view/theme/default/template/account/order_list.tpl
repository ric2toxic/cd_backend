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
  .suborder_table table tr td {
    padding: 3px !important;
  }
.payable_cashback_coupon_discount{
  padding-left: 10px !important;
}
.payable_net_payable{
  padding-left: 10px !important;
}
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

  <div class="width_fix account_page_bg">
    <div class="clearfix"></div>
    <div class="row"><?php echo $column_left; ?>
      <?php if ($column_left && $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
      <?php } elseif ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-9'; ?>
      <?php } else { ?>
        <?php $class = 'col-sm-12'; ?>
      <?php } ?>
      <?php echo $column_right; ?>

      <div id="content" class="<?php echo $class; ?> box_shadow_none">
        <ul class="breadcrumb_new">
          <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
          <?php } ?>
        </ul>

        <?php echo $content_top; ?>
        <?php if($is_dropshipper==1){
            $droppshipper_value =  $text_active;
            $color = "GREEN"; ?>
        <?php
          }else if($is_dropshipper==2){
            $droppshipper_value = $text_pending;
            $color = "ORANGE";
          }else if($is_dropshipper==3){
            $droppshipper_value = $text_blocked;
            $color = "RED";
          }else{
            $droppshipper_value = '';
          }?>
        <label class="dropshipper_heading1" style="color: <?php echo $color;?>;"> <?php echo $droppshipper_value; ?> </label>

        <h1 class="account_title"><?php echo $welcome; ?>: <?php echo $name; ?></h1>
        <div class="account_referral_box">
          <?php /* ?>
          <div class="float_to_position col-sm-6">
            <span class="referral_text"><?php echo $text_referral ?></span>
            <span class="referral_code"> <?php echo $referral_code; ?> </span> <br />
            <span id="referral_copy" style="cursor: pointer; color: #133595;"><?php echo $text_refer_url; ?></span>
          </div>
          <div class="width_fifty col-sm-6">
            <span class="referral_text">
              <?php echo $text_credit_balance; ?> : <i class="fa fa-inr" style="font-size: 12px;"></i>
              <strong><?php echo $amount; ?></strong>
            </span><br />
            <ul class="list-unstyled">
              <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
            </ul>
          </div>
          <?php */ ?>
          <div class="clearfix"></div>
        </div>

        <h3><?php echo $text_my_orders; ?></h3>

        <?php if ($orders) { ?>
          <!--
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr class="table_head_title">
                  <td class="text-right"><?php echo $column_order_no; ?></td>
                  <td class="text-right"><?php echo $column_date_added; ?></td>
                  <td class="text-left">
                    <table class="table table-bordered">
                      <tr>
                        <td><?php echo $column_suborder_id; ?></td>
                        <td><?php echo $column_status; ?></td>
                        <td><?php echo $column_amount; ?></td>
                        <td></td>
                      </tr>
                    </table>
                  </td>
                  <!--<td class="text-right"><?php echo $column_total; ?></td>-- ->
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $order) { ?>
                  <tr>
                    <td class="text-center">
                      <strong> <?php echo $order['order_no']; ?></strong>
                    </td>
                    <td class="text-center">
                      <?php echo $order['order_date']; ?>
                    </td>
                    <td class="text-left suborder_table">
                      <?php if( !empty( $order['suborder'] ) ) { ?>
                        <table class="table table-bordered">
                          <?php foreach( $order['suborder'] as $suborder_data ) { ?>
                            <tr>
                              <td><?php echo $suborder_data['suborder_id']; ?></td>
                              <td class="">
                                <strong> <?php echo $suborder_data['status']; ?></strong> <br>
                                <?php echo $suborder_data['last_update_history']; ?>
                              </td>
                              <td><?php echo $suborder_data['total']; ?>  </td>
                              <td class="text-left">
                                <a href="<?php echo $suborder_data['href']; ?>"
                                   data-toggle="tooltip"
                                   title="<?php echo $button_view; ?>"
                                   class="btn btn-info">
                                   <i class="fa fa-eye"></i>
                                </a>
                                <?php if(!empty($suborder_data['invoice_no']) && $suborder_data['invoice_no'] != 0){ ?>
                                  <a href="<?php echo $suborder_data['invoice_href']; ?>"
                                     data-toggle="tooltip"
                                     title="<?php echo $button_print; ?>"
                                     class="btn btn-success">
                                     <i class="fa fa-print"></i>
                                  </a>
                                <?php }  ?>
                                <?php /*  if($suborder_data['order_status_id'] == 15){ ?>
                                  <a href="<?php echo $suborder_data['order_return']; ?>"
                                     id="button-return<?php echo $order['order_id']; ?>"
                                     data-toggle="tooltip"
                                     title="Return"
                                     class="btn btn-danger">
                                     <i class="fa fa-reply"></i>
                                  </a>
                                <?php } */ ?>
                              </td>
                            </tr>
                          <?php } ?>
                        </table>
                      <?php } ?>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          -->
          <div class="orders_content">
            <!-- <ul class="orders_category">
              <li>Sort By:</li>
              <li class="active"><a data-toggle="tab" href="#latest_orders">Latest Orders</a></li>
              <li><a data-toggle="tab" href="#pending_orders">Pending Orders</a></li>
              <li><a data-toggle="tab" href="#successful_orders">Successful Orders</a></li>
            </ul>  -->
            <div class="tab-content">
              <!-- ***Latest Orders tab start*** -->
              <?php foreach ($orders as $order) { ?>
              <?php //echo "<pre>"; print_r($order); ?>
              <div id="latest_orders" class="tab-pane fade in active">
                <div class="order_table_box">
                  <div class="order-expanded">
                    <div class="col-sm-3 order_no_text">
                      <span class="smallText"><?php echo $column_order_no; ?></span>
                      <strong><?php echo $order['order_no']; ?></strong>
                    </div>
                    <div class="col-sm-3 order_no_text order_date_text">
                      <span class="smallText"><?php echo $column_date_added; ?>&nbsp;</span><strong><?php echo $order['order_date']; ?></strong>
                    </div>
                    <div class="col-sm-6 order_no_text order_method_text">
                      <span class="smallText"><?php echo $text_payment_method; ?></span>&nbsp;<strong><?php echo $order['payment_method']; ?></strong>
                    </div>
                    <?php /*  if($suborder_data['order_status_id'] == 15){ ?>
                      <div class="col-sm-2 return_btn">
                        <a href="<?php echo $suborder_data['order_return']; ?>"
                           id="button-return<?php echo $order['order_id']; ?>"
                           data-toggle="tooltip"
                           title="Return"
                           class="btn btn-danger">
                           <i class="fa fa-repeat" aria-hidden="true"></i>Return
                        </a>
                      </div>
                    <?php } */ ?>
                    <div class="clearfix"></div>
                  </div>
                  <div class="order-details">
                    <div class="order-total">
                      <div class="col-sm-12" style="padding-bottom: 10px;">
                        <!--<span class="smallText"><?php echo $text_order_total_amt; ?></span>
                        <strong> <?php echo $order['order_total']; ?></strong><br>-->
                        <span class="smallText payable_invoice_value"><?php echo $text_payable_invoice_value; ?></span>
                        <strong> <?php echo $order['payable_invoice_value']; ?></strong>
                        
                        <?php if(!empty($order['payable_cashback_coupon_discount'])) {?>
                        <span class="smallText payable_cashback_coupon_discount"><?php echo $text_payable_cashback_coupon_discount; ?></span>
                        <strong> <?php echo $order['payable_cashback_coupon_discount']; ?></strong>
                        <span class="smallText payable_net_payable"><?php echo $text_payable_net_payable; ?></span>
                        <strong> <?php echo $order['payable_net_payable']; ?></strong>
                        <?php } ?>
                        
                      </div>
                      <div class="col-sm-12">
                        <div class="">
                         <?php if(!empty($order['return_link']) && $is_dropshipper != 1 && $order['store_id'] !=2 && empty($order['franchise_id'])){ ?>
                            <a href="<?php echo $order['return_link']; ?>" class="btn btn-order_red collapsed" style="margin: 0px 6px 0px 0px;"><i class="fa fa-reply"></i>Return/Replacement</a>
                         <?php } ?>
                          <a class="btn btn-order_red collapsed" data-toggle="collapse" data-target="#order_id_<?php echo $order['order_id'];?>">
                            <span class="fa plus"></span>
                            <span class="fa minus"></span>
                            <?php echo $click_to_see_suborder; ?>
                          </a>
                        </div>
                      </div>
                      <div class="clearfix"></div>
                    </div>
                  </div>
                  <!-- Start suborder -->
                  <div class="table-responsive collapse table_title_text" id="order_id_<?php echo $order['order_id'];?>">
                    <table class="table table-bordered table-hover">
                      <thead>
                        <tr class="table_head_title">
                          <td class="text-center"><?php echo $column_suborder_id; ?></td>
                          <td class="text-left"><?php echo $column_status; ?></td>
                          <td class="text-center"><?php echo $column_amount; ?></td>
                          <td></td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if( !empty( $order['suborder'] ) ){ ?>
                        <?php foreach( $order['suborder'] as $suborder_data ) { ?>
                        <?php //echo "<pre>"; print_r($suborder_data);?>
                          <tr>
                            <td class="text-center"><?php echo $suborder_data['suborder_id']; ?></td>
                            <td class="text-left">
                              <strong> <?php echo $suborder_data['status']; ?></strong> <br>
                                       <?php echo $suborder_data['last_update_history']; ?>
                                  <!--Orders Successful At 31/01/2017 04:30 -->
                            </td>
                            <td class="text-center"><?php echo $suborder_data['total']; ?></td>
                            <td class="text-center">
                              <a href="<?php echo $suborder_data['href']; ?>"
                                 data-toggle="tooltip"
                                 title="<?php echo $button_view; ?>"
                                 class="btn order_view_btn" target="_blank">
                                 <i class="fa fa-eye"></i>
                              </a>
                              <!--<a href="<?php echo $order['href']; ?>"
                                 title="invoice"
                                 class="btn order_view_btn">
                                 <i class="fa fa-download" aria-hidden="true"></i>
                              </a>-->
                              <!--<a href="<?php echo $order['href']; ?>"
                                 title="return"
                                 class="btn order_view_btn">
                                 <i class="fa fa-repeat" aria-hidden="true"></i>
                              </a>-->
                              <?php if(!empty($suborder_data['invoice_no']) && $suborder_data['invoice_no'] != 0){ ?>
                                  <a href="<?php echo $suborder_data['invoice_href']; ?>"
                                     data-toggle="tooltip"
                                     title="<?php echo $button_print; ?>"
                                     class="btn btn-success">
                                     <i class="fa fa-print"></i>
                                  </a>
                                <?php }  ?>
                              <?php /*  if($suborder_data['order_status_id'] == 15){ ?>
                                  <a href="<?php echo $suborder_data['order_return']; ?>"
                                     id="button-return<?php echo $order['order_id']; ?>"
                                     data-toggle="tooltip"
                                     title="Return"
                                     class="btn btn-danger">
                                     <i class="fa fa-reply"></i>
                                  </a>
                                <?php } */ ?>
                            </td>
                          </tr>
                        <?php } ?>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                  <!-- End suborder -->
                </div>
              </div>
              <?php } ?>
              <!-- ***Pending Orders tab start*** -->
              <div id="pending_orders" class="tab-pane fade">
                <h3>Menu 1</h3>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
              </div>
              <!-- ***Successful Orders tab start*** -->
              <div id="successful_orders" class="tab-pane fade">
                <h3>Menu 2</h3>
                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
              </div>
            </div>
          </div>

          <div class="text-right"><?php echo $pagination; ?></div>
        <?php } else { ?>
          <p><?php echo $text_empty; ?></p>
        <?php } ?>
        <div class="buttons clearfix">
          <div class="pull-right padding_right_box">
            <a href="<?php echo $continue; ?>" class="btn btn-continue"><?php echo $button_continue; ?></a>
          </div>
        </div>
        <?php echo $content_bottom; ?>
      </div>
    </div>
  </div>


<section id="footer_box"></section>
<section id="login_box"></section>
<section id="opt_box"></section>
<section id="register_box"></section>
<section id="success_box"></section>
<div class="global-bg-layer"></div>
<div class="global-ajax-loader"></div>
<!-- jQuery -->
<script src="<?php echo LOCAL_CDN_URL_SSL.COMMON_VENDOR_JS; ?>"></script>
<script src="<?php echo LOCAL_CDN_URL_SSL.COMMON_REACT_JS; ?>"></script>

<script>

  var header_language     = <?php echo $header_language; ?>;
  var footer_language     = <?php echo $footer_language; ?>;
  var login_language      = <?php echo $login_language ; ?>;
  var international_store = <?php echo $international_store; ?>;

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


<script type="text/javascript">
  function copyToClipboard(text) {
    window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
  }

  $('#referral_copy').click(function() {
    copyToClipboard('<?php echo HTTP_SERVER; ?>index.php?route=account/register&ref=<?php echo $referral_code; ?>')
  });
</script>
<script type="text/javascript">
<?php if ($international_store == 1) { ?>

var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5a420d52bbdfe97b137fd4ba/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

<?php } else { ?>

var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/55faa35605ceaf627695ea99/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

<?php } ?>
</script>
</body>

</html>