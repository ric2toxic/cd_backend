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


<div class="container width_fix">
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6 nopadding'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9 nopadding'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12 nopadding'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>


      <?php echo $description_data; ?>

      <?php echo $content_bottom; ?></div>

       <?php echo $column_right; ?></div>
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



