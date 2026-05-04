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
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" ></script>
    <script   src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"   integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E="   crossorigin="anonymous"></script>

  <link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />

  <script  src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

  <link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
  <!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons"    rel="stylesheet">

  <script  src="catalog/view/theme/default/javascript/jasny-bootstrap/js/jasny-bootstrap.min.js" type="text/javascript"></script>-->
  <script  src="catalog/view/theme/default/javascript/fancybox/jquery.fancybox.pack.js" type="text/javascript"></script>

  <link href="catalog/view/theme/default/stylesheet/stylesheet.css" rel="stylesheet" />
  <link href="catalog/view/theme/default/stylesheet/stylesheet_new.css" rel="stylesheet" />
    <link href="catalog/view/theme/default/stylesheet/colors/<?php echo $color_template;?>/<?php echo $color_template;?>.css" rel="stylesheet" />
  <link href="catalog/view/theme/default/stylesheet/jquery-ui.css" rel="stylesheet" />

  <link href="catalog/view/theme/default/stylesheet/jquery.fancybox.css" rel="stylesheet" />
  <?php foreach ($styles as $style) { ?>
  <link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
  <?php } ?>
  <link href="catalog/view/theme/default/stylesheet/colors/<?php echo $color_template;?>.css" rel="stylesheet" />
  <script  src="catalog/view/javascript/common.js" type="text/javascript"></script>
  <script  src="catalog/view/theme/default/javascript/theme_common.js" type="text/javascript"></script>
 <!-- <script  src="catalog/view/theme/default/javascript/expand-search/modernizr.custom.js" type="text/javascript"></script>-->

   <?php foreach ($scripts as $script) { ?>

  <script  src="<?php echo $script; ?>" type="text/javascript"></script>
  <?php } ?>
  <?php echo $google_analytics; ?>
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
                document,'script','//connect.facebook.net/en_US/fbevents.js');

        fbq('init', '1100690113309500');
        fbq('track', "PageView");
        <?php
                if(isset($route) && $route == 'account/success'){
        ?>
                    fbq('track', 'CompleteRegistration');

        <?php
                }else if($route == 'checkout/cart'){
        ?>
                    fbq('track', 'AddToCart');
        <?php
                }else if($route == 'checkout/checkout'){
                    ?>
                    fbq('track', 'InitiateCheckout');
                <?php
                }else if($route == 'checkout/success'){
                    ?>
                    fbq('track', 'Purchase');
                <?php
                }
                ?>
    </script>

    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=1100690113309500&ev=PageView&noscript=1"
        /></noscript>
    <!-- End Facebook Pixel Code -->
</head>
<body class="<?php echo $class; ?>">

<div id="header-desktop">

  <header>

      <div class="container">
          <div class="row">
              <div class="col-sm-12">
                  <nav id="top">
                      <?php //echo $currency; ?>
                      <?php //echo $language; ?>
                      <div id="top-links" class="nav pull-right">
                          <ul class="list-inline">
                              <?php //if($seller_login){ ?>
 <!--                              <li><a href="<?php echo $manufacturer_dashboard_link; ?>"><span class="blink_text"><?php echo $dashboard; ?></span> </a> </li>
  -->                            <?php //}else{ ?>
                              <!-- <li><a href="http://www.wholesalebox.in/seller"><span class="blink_text"><?php// echo $manufacturer; ?></span> </a> </li> -->
                              <?php //} ?>
<!--                               <li><a href="<?php echo $wishlist; ?>" id="wishlist-total" title="<?php echo $text_wishlist; ?>"><i class="fa fa-heart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_wishlist; ?></span></a></li>
 -->                              <!--<li><a href="<?php //  echo $shopping_cart; ?>" title="<?php // echo $text_shopping_cart; ?>"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php // echo $text_shopping_cart; ?></span></a></li>
                              <li><a href="<?php // echo $checkout; ?>" title="<?php // echo $text_checkout; ?>"><i class="fa fa-share"></i> <span class="hidden-xs hidden-sm hidden-md"><?php // echo $text_checkout; ?></span></a></li>-->
                              <li><a href="tel:<?php echo $telephone; ?>"><i class="fa fa-phone"></i>&nbsp;<span class="hidden-xs hidden-sm hidden-md">8890958318</span></a></li>
                              <li><a href="tel:+8890958318 " class="whatsapp"><i class="fa fa-whatsapp"></i>&nbsp;(+91) 8890958318 </a></li>
                              <?php
                                if ($this->customer->isLogged()) {
                              $class = "";
                              $link = "index.php?route=account/account";
                              }else{
                              $class = "";//"accountLink";
                              $link = "javascript:void(0);";
                              }
                              ?>
                              <li class="dropdown">
                                  <?php /* ?><a href="<?php echo $link; ?>" title="<?php echo $text_account; ?>" class="dropdown-toggle <?php echo $class; ?>" data-toggle="dropdown"><i class="fa fa-user"></i> <span class="hidden-xs hidden-sm hidden-md"><?php if ($logged) { echo " ".$cust_name; }else {echo $text_login_signup;}?></span><span class="caret"></span></a><?php */ ?>
                                  <a href="<?php echo $link; ?>" title="<?php echo $text_account; ?>" class="dropdown-toggle <?php echo $class; ?>" data-toggle="dropdown"><i class="fa fa-user"></i> <span class="hidden-xs hidden-sm hidden-md"><?php if ($logged) { echo " ".$cust_name; }else {echo $text_login_signup;}?></span><span class=""></span></a>
                                  <ul class="dropdown-menu dropdown-menu-right">
                                      <?php if ($logged) { ?>
                                      <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
                                      <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
                                      <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
                                      <?php /* ?> <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li><?php */ ?>
                                      <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
                                      <?php } else { ?>
                                      <li><a href="<?php echo $register; ?>"><?php echo $text_register; ?></a></li>
                                      <li><a href="<?php echo $login; ?>"><?php echo $text_login; ?></a></li>
                                      <?php } ?>
                                  </ul>
                              </li>
						  </ul>
					  </div>
                  </nav>
              </div>
          </div>
          <div class="row sticky_new">
              <div class="col-sm-12">
                  <div class="row sticky">
                      <div class="col-sm-3">
                          <div id="logo">
                              <?php if ($logo) { ?>
                              <a href="<?php echo $home; ?>">
                                  <img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive" />
                              </a>
                              <?php } else { ?>
                              <h1><a href="<?php echo $home; ?>"><?php echo $name; ?></a></h1>
                              <?php } ?>
                          </div>
                      </div>
                      <div class="col-sm-9">
                          <div class="row">
                              <div class="col-sm-6"><?php echo $search; ?></div>

                              <div class="col-sm-6">
                                  <div class="row">
                                      <div class="wishlist_in_header col-xs-7" >
                                          <a href="javascript:void(0);" id="store_switch"><?php if (isset($this->session->data['custom_store'])) { ?>
                                                <?php if ($this->session->data['custom_store'] == 'single') {
                                                echo $text_wholesale_set_store;}
                                                else {
                                                  echo $text_single_store;
                                                } ?>
                                            <?php }else { echo $text_single_store;} ?></a>
                                      </div>
                                      <div class="cart_in_header col-xs-5">
                                          <?php echo $cart; ?>
                                      </div>
                                  </div>
                              </div>
                          </div>

                      </div>
                  </div>
              </div>
          </div>
      </div>
    <section id="top_nav">

        <div class="container">

            <div class="navbar-header"><span class="visible-xs" id="category">Categories</span>
              <button data-target=".navbar-ex1-collapse" data-toggle="collapse" class="btn btn-navbar navbar-toggle" type="button"><i class="fa fa-bars"></i></button>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse nopadding">
            <ul class="nav navbar-nav">
              <?php  if ($categories) { ?>

                        <?php foreach ($categories as $category) { ?>
                          <?php if ($category['children']) { ?>

                            <li class="dropdown">

                              <a href="<?php echo $category['href']; ?>" class="dropdown-toggle"   ><?php echo $category['name']; ?><i class="fa fa-caret-down"></i></a>

                                    <?php foreach (array_chunk($category['children'], ceil(count($category['children']) / $category['column'])) as $children) { ?>
                                        <ul  class="list-unstyled dropdown-menu" role="menu"  >
                                          <?php foreach ($children as $child) { ?>
                                          <li><a href="<?php echo $child['href']; ?>"><?php echo $child['name']; ?></a></li>
                                          <?php } ?>
                                          <li><a href="<?php echo $category['href']; ?>" class="see-all"><?php echo $text_all; ?> <?php echo $category['name']; ?></a></li>
                                        </ul>
                                    <?php } ?>



                            </li>
                          <?php } else { ?>
                            <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
                          <?php } ?>
                        <?php } ?>
              <?php } ?>
          </ul>
        </div>

         </div>
      </section>
<?php /* ?>
      <div id="refer_btn_container">

          <a href="http://wholesalebox.in/refer-and-earn" style="color: white;">
			<i class="fa fa-gift" style="color: white; font-size: 20px;"></i> Refer & Earn
          </a>
      </div>
<?php */ ?>
  </header>
</div>

<div id="header-mobile" class="scrollspy">

  <div id="sticky" class="affix">
    <div class="mobile-logo">

      <a href="<?php echo $home;?>">
      <?php /* ?>  WholesaleBox <br /> <span class="tagline">Lowest Factory Price</span><?php /* ?><img src="<?php echo $base;?>image/mobile_logo.png" alt="WholesaleBox"  /><?php */ ?>
        <img src="<?php echo $mobile_logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive" />
      </a>

    </div>
    <div class="container">
      <div class="row">
        <div class="search_box"><?php echo $search_mobile; ?></div>
        <div class="mini_cart"><?php echo $cart; ?></div>
      </div>
    </div>
  </div>

</div>
<div class="mobile-margin">&nbsp;</div>

<script>
    $(document).ready(function() {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 1) {
                $('#top').slideUp('fast');
                $('#top_nav').slideUp('fast');
                $('#header-desktop .sticky').css({"position":"fixed","width":"1170px"});
                $('#header-desktop header').css({"position":"fixed","height":"80px","z-index":"10","width":"100%"});
                $('#header-desktop header').slideDown(1000);
                $('#header-desktop .sticky_new').slideDown(1000);
            } else {
                $('#top').slideDown('fast');
                $('#top_nav').slideDown('fast');
                $('#header-desktop header').css({"position":"","height":"","z-index":"","width":""});
                $('#header-desktop .sticky').css({"position":"","width":""});
                $('#header-desktop .sticky_new').slideDown(1000);
                $('#header-desktop header').slideDown(1000);
            }
        });

        $('#store_switch').on('click', function (argument) {
            $.ajax({
                url: 'index.php?route=common/header/getStoreSwitchNew',
                dataType: 'json',

                beforeSend: function () {
                    $('body').removeClass('loaded').addClass('loading');
                },

                success: function (json) {
                    location.reload();

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }

            });
        });
    });

</script>

<style>
    #logo {
        margin:0px !important;
    }
</style>

<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "url": "https://http://www.wholesalebox.in/",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "http://www.wholesalebox.in/index.php?route=product/search&search={search}",
    //"target": "android-app://in.wholesalebox/https/www.wholesalebox.in/index.php?route=product/search&search={search}"
    "query-input": "required name=search"
  }
}
</script>