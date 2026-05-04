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



<div class="container-fluid nopadding store_locator">
	<div id="map"></div>
	<div class="direction_box">
		<div class="locator-heading"><h1>Store Locator</h1></div>
		<div class="stores">
                    <?php 
                        if(count($store_locators) > 0){ 
                            $i = 0;
                            foreach($store_locators as $sl){
                    ?>
                                <div class="<?php echo $sl['location_id'].$i; ?>">
                                        <h3><?php echo $sl['name']; ?><i class="fa fa-arrow-circle-right pull-right" aria-hidden="true" id="address-<?php echo $sl['location_id'].$i; ?>"></i></h3>
                                        <p><?php
                                                echo $sl['address']; 
                                                
                                            ?>
                                        </p>
                                        
                                        
                                        <?php
                                         if( $sl['timing'] != '' ) { ?>
                                            <p><strong>Opening Time :</strong> <?php echo $sl['timing']; ?></p>
                                        <?php } 
                                            if( $sl['telephone'] != '' ) {
                                        ?>
                                            <p><strong>Phone : </strong><?php echo $sl['telephone']; ?></p>
                                        <?php } 
                                            if( $sl['direction_url'] != '' ) {
                                        ?>
                                            <p><a href="<?php echo $sl['direction_url']; ?>">Get Directions</a></p>
                                        <?php } ?>
                                </div>
                    <?php   //if($i == 0) break;
                            $i++; }  
                        }
                    ?>
                    
	</div>
</div>
        
        <?php 
            if(count($store_locators) > 0){ 
                $i = 0;
                foreach($store_locators as $sl){
        ?>
	<div class="direction-popup" style="display: none; height: auto;<?php if($sl['image'] != '' ){ ?> background-image: url(<?php echo STATIC_CONTENT_URL_SSL . 'img/dw=280,dh=200,q=90/' .  $sl['image'] ?>)<?php } ?>" id="direction-popup-<?php echo $sl['location_id'].$i; ?>">

		<h1><?php echo $sl['name']; ?> <img src="<?php echo STATIC_CONTENT_URL_SSL;?>img/dw=40,dh=40,q=90/wsb-marker.png"><i class="fa fa-times pull-right" aria-hidden="true" id="crossbtn-<?php echo $sl['location_id'].$i; ?>"></i></h1> 
		<p><strong>Address :</strong> <?php echo $sl['address']; ?> <br/>
                        <?php if( $sl['timing'] != '' ) { ?>
                            <strong>Opening Time :</strong> <?php echo $sl['timing']; ?> <br />
                        <?php } ?> 
			<strong>Email :</strong> info@wholesalebox.in<br/>
                        <?php if( $sl['telephone'] != '') { ?>
                            <strong>Phone :</strong> <?php echo $sl['telephone']; ?> <br/>
                        <?php } ?>
			<strong>Whats App :</strong> (+91) 8696491521 <br/>
                        <?php if( $sl['direction_url'] != '') { ?>
                            <a href="<?php echo $sl['direction_url']; ?>">Get Directions</a>
                        <?php } ?>
                
                </p>
			
		<h3>Product Categories in Store</h3>
		<ul>
			<li>
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=69">Suits</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=61">Kurtis</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=85">Sarees</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=103">Western</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=121">Kidswear</a>,
                        </li>
			<li>
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=119">Menswear</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=154">Handicrafts</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=105">Accessories</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=126">Footwear</a>
                        </li>
		</ul>

	</div>
        <?php   //if($i == 0) break;
                $i++; }  
            }
        ?>

<script async defer
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCAwVsw0m7M9vHhGzZmzqPWZUiXblA_Uks&callback=initMap"></script>



<style type="text/css">
	html,
	body {
		height: 100%;
		margin: 0;
		padding: 0;
	}
        .show{display: block;}
	.hide{display: none;}
	.direction-popup ul{padding: 0px;}
	.direction-popup ul li{text-decoration: none; display: block; line-height: 1.3;font-size: 15px;}
	.direction-popup h1{color:#214097; font-size: 25px}
	.direction-popup p{line-height: 2; font-size: 18px;}
	.direction-popup h1 i{color: #ccc; font-size: 30px; margin-top: -30px; cursor: pointer;}
	.direction-popup {
		-moz-user-select: text;
		border: 1px solid rgba(0, 0, 0, 0.2);
		border-radius: 2px;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
		color: #000;
		outline: medium none;
		overflow: hidden;
		position: absolute;
		left:38%;
		top: 20px;
		z-index: 990;
		height: 550;
		width: 700px;
		padding: 20px;
		background:  #fff no-repeat right bottom ;

	}
        .direction-popup h3{font-size: 22px}  
	
        .locator-heading h1{font-size:24px}
	.stores h3{font-size: 18px; background: #fff; padding: 4px 15px; color: #282828; box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.16), 0 0 0 1px rgba(0, 0, 0, 0.08);}
	.stores h3 i{font-size: 23px; cursor: pointer;}
	.stores p{padding: 0px 20px;}
	#map {height: 600px;width: 100%; margin:auto;}
	.store_locator{	position: relative;}
	.direction_box{position: absolute; height: 600px; width: 350px; background:#fff; box-shadow: 2px 0 3px 0 rgba(0,0,0,0.2); z-index: 100;top: 0px;left: 8%; overflow-x: hidden;}
	.locator-heading{color: #4a4a4a;text-align: center;}
</style>


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
                
    function initMap() {
    
        var store_location = '<?php echo json_encode($store_locators); ?>'; 
        var store_location_object = JSON.parse(store_location);        
        
        var locations = [];
        
        for (i in store_location_object) {
            var tempArray = []; 
            if(store_location_object[i].lat != '' && store_location_object[i].long != ''){
                tempArray[0] = '<strong>'+store_location_object[i].name+'</strong><br>'+store_location_object[i].address+'<br>'
                if(store_location_object[i].direction_url != ''){
                    tempArray[0] += '<a href="'+store_location_object[i].direction_url+'">Get Directions</a>'
                }
                tempArray[1] = store_location_object[i].lat;
                tempArray[2] = store_location_object[i].long;
            }
            locations.push(tempArray);
        }
        
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 5,
            center: new google.maps.LatLng(21.1702, 72.8311),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var infowindow = new google.maps.InfoWindow({});

        var marker, i;
        
        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map,
                icon: '<?php echo STATIC_CONTENT_URL_SSL;?>wsb-marker.png'
            });

            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    infowindow.setContent(locations[i][0]);
                    infowindow.open(map, marker);
                }
            })(marker, i));
        }
    }
</script>
<script type="text/javascript">
        var store_location = '<?php echo json_encode($store_locators); ?>'; 
        var store_location_object = JSON.parse(store_location);        
        
        for (i in store_location_object) {
            
            //store_location_object[i].name
            var show_click = "#address-"+store_location_object[i].location_id+i;
            var div_name = "#direction-popup-"+store_location_object[i].location_id+i;
            var hide_click = "#crossbtn-"+store_location_object[i].location_id+i;
            
            show_popoup(show_click,div_name,hide_click);
            
        }
        
        function show_popoup(show_click,div_name,hide_click){
        
            $(show_click).click(function(){   
                $(".direction-popup").hide();
                $(div_name).show(1000);
            });
            
            $(hide_click).click(function(){  
                $(div_name).hide(0);
            });
            
        }
        
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
