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



        <!-- Content Row -->
  <div class="container-fluid width_fix">
   <div class="row">
<!--       <ul class="breadcrumb" style="padding: 3px 15px;">
        <li><a href="">Home</a></li>
        <li><a href="">Dropshipper</a></li>
      </ul> -->
      <div class="col-sm-12 nopadding">
        <div class="row">
         <!-- contact saction(start) -->
         <section class="col-sm-12 privacy_text">
          <div class="col-sm-12 nopadding">
            <h2 class="page_title">What is Dropshipping?</h2>
            <span class="dropshiper_helpline">Helpline: <i class="fa fa-phone" aria-hidden="true"></i> <i class="fa fa-whatsapp" aria-hidden="true"></i> +91 91161 21383 (Timing: 10.30 AM to 7.30 PM Monday to Saturday)</span>
          </div>
          <div class="clearfix"></div>

           <P>Dropshipping is a practice of selling a product to your customers that you don’t have physically in stock. It’s a new trend of marketing and distribution of products. We can make this possible by shipping the stock directly to your customer with your name. </P>

           <div class="col-sm-12 col-lg-6"><div class="dropshipper_img"><img src="<?php echo $dropshipper; ?>" alt="dropshipper" class="img-responsive"></div></div>
           <div class="col-sm-12 col-lg-6">
               <h3  class="page_subtitle">Benefits of Dropshipping</h3>
               <ul class="dropshipper_li">
                  <li>Do not have to source products in bulk before you sell anything.</li>
                  <li>Not facing the risk of having dead stock.</li>
                  <li>No need of inventory management or maintaining your own warehouse.</li>
                  <li>Get low wholesale prices even on small quantities.</li>
                  <li>You can start your business without any need for start-up capital.</li>
                  <li>Wholesalebox.in will be managing the stock for you.</li>
                  <li>Everything in stock that we list online. Only once you get an order, you pay us for shipping it out.</li>
                  <li>You don't have to pick, pack, and ship orders as we will do that.</li>
                  <li>You can completely focus on your sales and just communicate the sales to us.</li>
                  <li>Best part is, the whole transaction is completely anonymous.</li>
                  <li>Customer wouldn’t know that it came from us!</li>
                 
               </ul>
               <br>
               <p>Register an account with <a href="#">www.wholesalebox.in</a></p>
               <div class="dropshipper_center">
                  <button class="dropshipper_center_btn" data-toggle="modal" data-target="#login_verify_popup" id="dropshipper_login_link">Become a Dropshipper</button>
                  <p>If you want to know anything else, please drop us an e-mail at <a href="#">info@wholesalebox.in</a> or call on 9587881065
                  </p>
               </div>
           
            <br>
             
           </div>
           

             
          </section>

          <section>
            <div class="col-sm-12 nopadding">
               <div class="col-sm-12"><h3 class="page_title">Dropshipper FAQ</h3></div>
               <div class="col-sm-12 col-lg-6">
                  <div class="well question_box">
                     <div class="question">1. How to become a drop shipper with the WholesaleBox or detailed procedure to start with dropshipping?</div>
                     <div class="answer">Dropshipping with WholesaleBox is very easy. The Dropshipper simply has to register at Wholesalebox.in with some contact details.  If they have a website then send an e-mail at info@wholesalebox.in. Once you are done with any of these steps, then we will activate your drop shipper account. 
In case you want to us to provide you Product .csv file or feed or API's, then we will check your business, sales, and traffic, etc. on your website and if you fulfill all the requirements then we will activate your account for Dropshipping.</div>
                  </div>


                   <div class="well question_box">
                     <div class="question">2. What are requirements in case I want WholesaleBox Product .csv file or feed or API's?</div>
                     <div class="answer">We only provide our product feed, if certain criteria are fulfilled: <br />
1. Your e-commerce store should be active for at least a year.<br />
2. We would require the monthly sales and monthly traffic on your website.<br />
3. Your website URL.<br />

To place a single order, you can use the singles store section of our website. If you select pieces from a Wholesale set store, we will not be able to break sets.<br />
Once you get an order on your website, you have to manually place that order on WholesaleBox website and use the 'shipping address' section to mention the details of your customer.</div>
                  </div>

                   <div class="well question_box">
                     <div class="question">3. Who can be a drop shipper at WholesaleBox?</div>
                     <div class="answer">Anyone who sells products and has a clientele can be a drop shipper with us whether he is a Whatsapp seller, Home-based seller, or online retailer on other eCommerce portals can be a Dropshipper at Wholesalebox.in</div>
                  </div>
                  <div class="well question_box">
                     <div class="question">4. How much time does it take to activate the account of drop shippers?</div>
                     <div class="answer">Mostly it takes maximum 24 Hours to activate your drop shipping account, but sometimes it may take more time.</div>
                  </div>


                   <div class="well question_box">
                     <div class="question">5. Is there any minimum amount or a fixed amount for drop shippers to purchase Wholesale products from your website? </div>
                     <div class="answer">No, there are no minimum or maximum amount requirements, you can drop products for any amount.</div>
                  </div>

                   <div class="well question_box">
                     <div class="question">6.  What type of shipping do you provide and procedure for same?</div>
                     <div class="answer">We offer only air courier for Drop-shipping and it charges as per the volumetric dimensions of the product.</div>
                   </div>

                 
               </div>

               <div class="col-sm-12 col-lg-6">
                  <div class="well question_box">
                     <div class="question">7.  Is there any charge for drop shipping or it is free of cost?</div>
                     <div class="answer">No, we don't charge a single penny from Dropshipper. </div>
                  </div>
                  <div class="well question_box">
                     <div class="question">8. Can I buy in a single set or I need to buy the full sets?</div>
                     <div class="answer">You can buy any of the two, but you have to buy single-Piece from a single store and a full set from wholesale-Set Store. Also make sure to place an order from the singles store, if you want to drop ship single piece because if you select pieces from a Wholesale set store, we will not be able to break sets.</div>
                  </div>

                   <div class="well question_box">
                     <div class="question">9. What will be return and replacement policy for drop shipping? </div>
                     <div class="answer">No return policy is there, only Replacement option and in that case, either customer or drop shipper will pay one side courier charge.</div>
                  </div>

                   <div class="well question_box">
                     <div class="question">10. What if a customer receives some defective products? Whom should he complain about the product? ? Can he return directly to WholesaleBox or he has to return to the drop shipper?</div>
                     <div class="answer">Of course, he will complain to your Drop-shipper because for customer WholesaleBox is nowhere in the delivery & purchase process and afterward, Drop-shipper will be complaining to us about the Return. In such cases, all the shipping charges will be paid either by the Dropshipper or customer only.</div>
                  </div>
                  <div class="well question_box">
                     <div class="question">11. Who will in charge the fulfillment processes like who will do the shipping and what about invoicing for cases like return? Who is going to bear shipping charges?</div>
                     <div class="answer">In drop-shipping case, WholesaleBox needs to collect full payment before the product is dispatched. It completely depends on Dropshipper who will Bear the shipping charge either Dropshipper or Buyer.   </div>
                  </div>
                   <div class="well question_box">
                     <div class="question">12.  How to use App as a drop shipper?</div>
                     <div class="answer">Once you register on WholesaleBox app as a drop shipper, in some time we will activate your account. Then you have to select online seller option on an app and finally, you can place an order. </div>
                  </div>

<!--                    <div class="well question_box">
                     <div class="question">13.  What if I received any defective products? Whom should I complain for the product? ? Can he return directly to wholesalebox or he has to return to the drop shipper?</div>
                     <div class="answer">First you should complain to your Drop-shipper . And  Drop-shipper will be complain to  wholesalebox for Return .</div>
                  </div>
                  <div class="well question_box">
                     <div class="question">14.  What fulfillment process will take place like who will do shipping and what about invoicing for cases like return? Also, who is going to bear shipping charges?</div>
                     <div class="answer">In drop-shipping case we need to collect full payment and than we will Dispatch the Product . Depend on Drop-shipper  who will Bear the shipping charger  Drop-shipper/ Buyer .</div>
                  </div>
                  <div class="well question_box">
                     <div class="question">15.  How to use App as a drop shipper?</div>
                     <div class="answer">Wholesalebox will activate his account as a drop-shipper and than he will select online seller option on  app  and finally he can place order . </div>
                  </div> -->
                 
               </div>
            </div>
          </section>


         <!-- contact saction(end) -->

         <div class="clearfix"></div>

        </div>   
      </div>
   

  </div>
    <!-- /.row -->
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
    $(document).ready(function(){

    });
    $("#popup").click(function(){
        $.fancybox(
          '<h2 clsss="dropshipper_popup">Thank you</h2><p>We would review your request shortly and would get back to you.</p>',
            {
              'autoDimensions'  : false,
              'width'           : 350,
              'height'          : 'auto',
              'transitionIn'    : 'none',
              'transitionOut'   : 'none'
            }
          );
          $.ajax({
            url : "index.php?route=account/dropshipper/updateDropshipper",
            type: "POST",
            success: function( data ) {
            //  alert(data);
            }
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