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
         <div class="col-sm-12 nopadding"><img src="<?php echo $images['wholesalebox_about']; ?>" alt="wholesalebox about" class="img-responsive about_img"></div>
         <section class="col-sm-12 nopadding">
           <h2 class="page_title about_title"><span>About Us</span></h2>
             <div class="col-sm-8">
                 <div class="row">
                     <h3 style="font-size:22px;">WholesaleBox is a controlled marketplace for wholesale buying and selling across India.</h3>
                     <p class="about_content_text">Wholesalebox has factories producing excellent selection of products as sellers who list their ready inventory stock for sale to shopkeepers across the country. We do this to eliminate the wholesalers and traders in between to get a 20-25% lower price for the shopkeepers by sourcing directly form manufacturers along with our tech enabled curation to provide fast selling designs. We are a bunch of technology and business people from IIMs, IITs, NITs, etc, who are working to bring efficiency in the whole distribution system and help retailers get more variety at their doorsteps. They can take advantage of our simple app or web interface to buy products for their retail outlets and get those without having to travel to different cities / manufacturing hubs or to buy at much higher prices from the wholesalers near them. We've put the entire wholesale buying process online to enable manufacturers & brands and retailers to drive incremental revenue, cut costs, improve their customer experience and analyze performance through data analytics.</p>
                     <p class="about_content_text">For the shopkeepers, they only need to deal with one entity Wholesalebox to take care of entire order and returns experience while for the factories also it’s a similar seamless experience where they only need to bill and coordinate with only Wholesalebox and we take care of the rest while everyone does their business with ease.</p>
                 </div>
             </div>
             <div class="col-sm-4 pull-right"><img src="<?php echo $images['wholesalebox_about_2']; ?>" alt="wholesalebox about" class="img-responsive about_img"></div>
          </section>
          <section class="col-sm-12 nopadding">
           <h2 class="page_title about_title"><span>Lowest Factory Price Assured</span></h2>
             <div class="col-sm-4"><img src="<?php echo $images['wholesalebox_about_graph']; ?>" alt="wholesalebox about" class="img-responsive about_img"></div>
             <div class="col-sm-8">
              <div class="row">
               <h3>We bring you the best prices as we operate at very low margins.</h3>
               <p class="about_content_text">If you compare us with a normal Wholesaler nearby you. Buying from us, you can save 25%-35% of the procurement cost on every order. We have more than <?php echo $total_products; ?>+ designs on our website for retailers with convenience of e-commerce like COD, Home Delivery, Easy Returns, Value for Money with respect to Quality, Personal Buying Assistance, Volume Discounts, Credit Facilities, and the most important one, Guaranteed Lowest Prices!!! Shop at WholesaleBox, so that you can offer great pricing to your customers. In overall, buying at WholesaleBox will increase your profit margins at the convenience of your fingertips. </p>
              </div>  
             </div>
          </section>
          <section class="col-sm-12 nopadding">
           <h2 class="page_title about_title"><span>How We Work</span></h2>
             <div class="col-sm-8">
              <div class="row">
               <h3>WholesaleBox is an online platform where retailers/shopkeepers can buy hasslefree.</h3>
               <p class="about_content_text">And they reduce their procurements costs at better quality, from the convenience of his/her home/shop. From WholesaleBox, a retailer can order products in bulk for reselling and the order will be delivered at his/her chosen place. The ordering process is pretty simple. Sign Up (if first time), or Login the website using your mobile number or email address. Select designs you wish to place the order for, and add them to your cart. Use filters, sorts, and search to get the desired products of your choice from more than <?php echo $total_products; ?>+ to choose from. Once selection is complete, go to cart, click checkout and follow the steps to place the order with WholesaleBox and get Door Delivery done. Easy returns policy is provided post delivery of the Goods. Please review the Returns and Cancellation Policy.</p>
              </div>  
             </div>
             <div class="col-sm-4 pull-right"><img src="<?php echo $images['wholesalebox_about_how_we_work']; ?>" alt="wholesalebox about" class="img-responsive about_img"></div>
          </section>
          <section class="col-sm-12 nopadding">
           <h2 class="page_title about_title"><span>Co-Founders</span></h2>
               <h3 class="text_center">We do everything with our core values of honesty, hard work and trust.</h3>
               <div class="about_team">
                <ul>
                  <li>
                    <img src="<?php echo $images['wholesalebox_rohit_dangayach']; ?>" alt="wholesalebox about" class="img-responsive about_team_photo">
                    <span>Rohit Dangayach</span>
                  </li>
                  <li>
                    <img src="<?php echo $images['wholesalebox_chandan_agarwal']; ?>" alt="wholesalebox about" class="img-responsive about_team_photo">
                    <span>Chandan Agarwal</span>
                  </li>
                  <li>
                    <img src="<?php echo $images['wholesalebox_rakesh_shekhawat']; ?>" alt="wholesalebox about" class="img-responsive about_team_photo">
                    <span>Rakesh Shekhawat</span>
                  </li>
                  <li>
                    <img src="<?php echo $images['wholesalebox_madhur_maheshwari']; ?>" alt="wholesalebox about" class="img-responsive about_team_photo">
                    <span>Madhur Maheshwari</span>
                  </li>
                  
                </ul>
                <div class="clearfix"></div> 
             </div>
          </section>         
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

  $(function () {
    if ($('html').hasClass('csstransforms3d')) {  
    
      $('.thumb').removeClass('scroll').addClass('flip');   
      $('.thumb.flip').hover(
        function () {
          $(this).find('.thumb-wrapper').addClass('flipIt');
        },
        function () {
          $(this).find('.thumb-wrapper').removeClass('flipIt');     
        }
      );
      
    } else {

      $('.thumb').hover(
        function () {
          $(this).find('.thumb-detail').stop().animate({bottom:0}, 500, 'easeOutCubic');
        },
        function () {
          $(this).find('.thumb-detail').stop().animate({bottom: ($(this).height() * -1) }, 500, 'easeOutCubic');      
        }
      );

    }
  
  });

$(document).ready(function() {
    $("div.tab-menu>div.tab_list>a").click(function(e) {
        e.preventDefault();
        $(this).siblings('a.active').removeClass("active");
        $(this).addClass("active");
        var index = $(this).index();
        $("div.bhoechie-tab>div.tab-text").removeClass("active");
        $("div.bhoechie-tab>div.tab-text").eq(index).addClass("active");
    });
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
