
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

.download_link{margin-top: 60px; font-size: 15px;}
		.download_link a:link, .download_link a:visited{text-decoration: none; color: #F03140;}
		.download_link a:hover, .download_link a:active{color: #c12733;border: none;}
		.download_btn button:hover{background: #F03140; color: #Fff; border-color:#F03140;}
		.testimonial-text #fade-quote-carousel.carousel{position: static!important;}
		#fade-quote-carousel.carousel .carousel-indicators {bottom: 10px;}
		blockquote{border: 0px!important;}
		.carousel-indicators{position: absolute;bottom: 10%;}
		.carousel-inner{width: 60%!important; margin: auto;}
		.app-enquiry{height: 300px; background: #fff; margin-top: 10px; text-align: center;}
		.app-enquiry img{height: 300px; padding: 10px;}
		.application{ margin-top: 20px;}
		.app-intro{width: 100%; text-align: center;}
		.app-landing-background{margin-top: 50px;}
		.app-landing-background h1{color: #fff;}
		.app-landing-content{text-align: center; color:#fff;display:block; width: 100%; float: left; background: url('image/application/app-banner.jpg') no-repeat scroll center top; height: 523px;}
		.app-landing-download{margin-top: 100px;}
		.app-landing-download a button{margin-top: 20px;border: 2px solid #fff; background: none; color: #fff;padding: 10px 20px; font-size: 15px;}
		.app-landing-download a button i {font-size: 25px; margin-right: 10px;}
		.app-landing-download h3{color: #fff;}
		.description ul{display: inline-block; margin-top: 20px; line-height: 1.8;}
		.description ul li {float: left; text-align: justify;}
		.testimonial-text{background: #F03140; height: 350px; color: #fff;text-align: center; padding: 25px 30px 10px;position: relative;text-align: center;}
		.testimonial-text h1{color: #fff;}
		.testimonial-text p{line-height: 25px;margin: 0 auto !important;max-width: 960px;padding: 30 50px;position: relative;font-size:15px;}
		.testimonial{border-top: 1px solid #f280a3;content: "";display: block;padding: 20px;width:320px;}
		.testimonial-text img.test_top{position: absolute; top: 10px; left: 0px; height: 30px;}
		.testimonial-text img.test_bottom{position: absolute; top: 115px; right: 0px; height: 30px;}
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
	<div class="app-landing-content">
		<div class="app-landing-background">
			<div><h1>India's #1 Wholesale clothes shopping app</h1></div>
			<div><h1>----------------------------------------------------</h1></div>
			<div><h1>Lowest Wholesale Price</h1></div>
			<div class="app-landing-download">
				<div><h3>Get Wholesalebox app for free</h3></div>
				<div class="download_btn">
					<a href="https://play.google.com/store/apps/details?id=in.wholesalebox&hl=en" target="blank">
						<button><i class="fa fa-android"></i> DOWNLOAD NOW</button>
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="app-info">
				<div class="col-sm-12 app-enquiry">
					<div class="col-sm-8 description">
						<div><h3 class="pull-left">Install Wholesalebox Application from Google Play Store</h3></div>
						<br/>
						<div><ul>
							<li>Browse 40,000+ designs (ever increasing), instantly on the Wholesalebox App.</li>
							<li>Discover the latest trends in fashion and lifestyle products such as Kurtis, Dress Materials, 
							    Suits, Sarees, Western, Bottoms, Footwear, Home furnishing and much more.</li>
							<li>Exclusive Cashback for orders placed from App.</li>
							<li>Select categories in which your business deals and get latest designs only for those categories.</li>
							<li>Be assured of the product quality with our product star rating.</li>
						</ul></div>
					</div>
					<div class="col-sm-4">
						<img class="pull-right" src="image/application/downloadapp.png">
					</div>
				</div>
				<div class="col-sm-12 app-enquiry">
					<div class="col-sm-4"><img class="pull-left" src="image/application/share.png">
					</div>
					<div class="col-sm-8 description">
						<div><h3 class="pull-left">Share your shortlisted designs to your customer with your brand name</h3></div><br/>
						<div><ul>							
							<li>Zero inventory risk-Shortlist and share on whatsapp/facebook with your margins and 
							    order only when you receive positive response.</li>
							<li>Convert Wholesalebox App as your own App, bearing your shop name, and impress your customers 
							    by showing designs with your added margins to customers.</li>
							<li>Need Help ? With our app, you are just one click away to chat with us.</li>
						</ul></div>
					</div>
				</div>
				<div class="col-sm-12 app-enquiry">
					<div class="col-sm-8 description">
						<div><h3 class="pull-left">Easy steps for hasslefree buying</h3></div><br/>
						<div>
							<ul>
								<li>Easily search the specific design OR use filters to choose products and add to cart.</li>
								<li>Once the shopping cart is ready, go to Cart for review.</li>
								<li>Place order hasslefree and choose from payment options, including instant and Cash on Delivery.</li>
							</ul>
							<div class="download_link pull-left">
								<a href="https://play.google.com/store/apps/details?id=in.wholesalebox&hl=en" target="blank">Download Wholesalebox Application</a>
							</div>
						</div>
					</div>
					<div class="col-sm-4"><img class="pull-right" src="image/application/checkout.png"></div>
				</div>
			</div>
		</div>
	</div>

<section id="carousel">    				
	<div class="container-fluid nopadding">
		<div class="testimonial-text">
		<div><h1>Why Shopkeepers Love Us.</h1></div>
			<div>
   				<div class="carousel slide" id="fade-quote-carousel" data-ride="carousel" data-interval="3000">
				  <!-- Carousel indicators -->
                  <ol class="carousel-indicators">
				    <li data-target="#fade-quote-carousel" data-slide-to="0"></li>
				    <li data-target="#fade-quote-carousel" data-slide-to="1"></li>
				    <li data-target="#fade-quote-carousel" data-slide-to="2" class="active"></li>
                    <li data-target="#fade-quote-carousel" data-slide-to="3"></li>
                    <li data-target="#fade-quote-carousel" data-slide-to="4"></li>
                    <li data-target="#fade-quote-carousel" data-slide-to="5"></li>
				  </ol>
				  <!-- Carousel items -->
				  <div class="carousel-inner">
				  		<div>
							<img src="image/application/testimonialtop.png" class="test_top"><img src="image/application/testimonialbottom.png"class="test_bottom">
						</div>
				    <div class="item">
				    	<blockquote>
				    		<p>It is a great app. Placing orders and buying stock for my store has become very convenient. Excellent delivery and great quality of products.</p>
				    		<div><p class="testimonial">Shivam Chopra (Gurugram)</p></div>
				    	</blockquote>	
				    </div>
				    <div class="item">
				    	<blockquote>
				    		<p>Best quality products, I bought kurtis and i loved it. best quality and best service. Way to go... </p>
				    		<div><p class="testimonial">Sheeba Gandhi (Rajkot)</p></div>
				    	</blockquote>
				    </div>
				    <div class="active item">
				    	<blockquote>
				    		<p>VERY GOOD, Trust Worthy & Reasonable Prices.... The Ordered Products Reached my Customer On Time.... </p>
				    		<div><p class="testimonial">Ammu Selvam (Hyderabad)</p></div>
				    	</blockquote>
				    </div>
                    <div class="item">
    			    	<blockquote>
				    		<p>Super good concept and well executed. We have placed many orders and happy. Easy returns and super good pricing.</p>
				    		<div><p class="testimonial">Gaurav Sanghi (Howrah)</p></div>
				    	</blockquote>
				    </div>
                    <div class="item">
    			    	<blockquote>
				    		<p>This is user friendly app. Any one can handle easily .its very helpful to improve business for retailers. from Manufacturers direct to retailer. . </p>
				    		<div><p class="testimonial">Raj Kumar (Varanasi)</p></div>
				    	</blockquote>
				    </div>
                    <div class="item">
    			    	<blockquote>
				    		<p>Very nice collections in reasonable price 👍</p>
				    		<div><p class="testimonial">Vishwas Khandelwal (Bikaner)</p></div>
				    	</blockquote>
				    </div>
				  </div>
				</div>
			</div>							
		</div>
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
</script>

</body>

</html>