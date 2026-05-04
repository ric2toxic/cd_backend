       <!DOCTYPE html>
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
    
    <meta property="fb:page_id" content="1647420208625877" />
    <meta property="og:title" content="<?php echo $title; ?>"/>
    <meta property="og:site_name" content="Wholesalebox"/>
    <meta property="og:description" content="<?php echo $description; ?>"/>
    <meta property="og:url" content="<?php echo $home_url; ?>"/>

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
    <link href="<?php echo UPLOAD_CONTENT_URL_SSL.COMMON_CSS; ?>" rel="stylesheet">

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
.carousel-inner {overflow: hidden;position: relative;width: 100%;}
.b2bVideo {display: inline-block;float: right;padding: 0px 7px 10px 0px;}
.home-product-list {width: 20%;}
.c-product-card__gallery{width: 42px;}
.tab_list li a h4 {font-size: 1.2em;}
.stats-bar ul li a { font-size: 24px;padding: 25px 30px;}
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
<input type="hidden" value="<?php echo $this->session->data['ctoken']; ?>" name="ctoken" id="ctoken"  class="ctoken" />
<section id="home_page"></section>

<div class="global-bg-layer"></div>
<div class="global-ajax-loader">Loading...</div>

<!-- jQuery -->

<script type='text/javascript' src="<?php echo LOCAL_CDN_URL_SSL.COMMON_VENDOR_JS; ?>"></script>

<script src="<?php echo UPLOAD_CONTENT_URL_SSL.COMMON_REACT_JS; ?>"></script>
<!-- home jsx -->
<script src="catalog/view/theme/default/javascript/my_account_components.js?v=2.4" type="text/javascript"></script>


<script type="text/javascript">
    ie = (function(){

        var undef,
                v = 3,
                div = document.createElement('div'),
                all = div.getElementsByTagName('i');

        while (
                div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
                        all[0]
                );

        return v > 4 ? v : undef;

    }());

    if(ie < 10){
        window.location = '<?php echo $base; ?>update-your-browser';
    }
    /**
    * Load Categories after 5 seconds of page load
    * */
</script>

<script type="text/javascript">

var myaccount_language  = <?php echo $myaccount_language; ?>;
var header_language     = <?php echo $header_language; ?>;
var footer_language     = <?php echo $footer_language; ?>;
var international_store = <?php echo $international_store; ?>;
var left_menu           = <?php echo $left_menu; ?>;
var custom_store_val    = '<?php echo $custom_store_val; ?>';
var order_type_title    = <?php echo $order_type_title; ?>;
var filter_order_type_value = <?php echo $filter_order_type_value; ?>;
var menus               = <?php echo $menus; ?>;
var popular_tags        = <?php echo $popular_tags; ?>;
var common_order_data   = <?php echo $common_order_data; ?>;

<?php if (isset($this->session->data['custom_store'])) {
        if ($this->session->data['custom_store'] == 'single') { ?>
var custom_store        = header_language.text_wholesale_store;
<?php  } else { ?>
var custom_store        = header_language.text_singles_store;
<?php  } }else { ?>
var custom_store        = header_language.text_singles_store;
<?php } ?>

ReactDOM.render(React.createElement(MyAccountLayout, { myaccount_language:myaccount_language, header_language:header_language, footer_language:footer_language, international_store:international_store, custom_store:custom_store, custom_store_val:custom_store_val, left_menu:left_menu, filter_order_type_value:filter_order_type_value, order_type_title:order_type_title, menus:menus, popular_tags:popular_tags, common_order_data:common_order_data},null ), document.getElementById('home_page')); 

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

<script type="text/javascript">

   $(document).ready(function() { 

    // $('.toggle_icon').click(function(){
    //  $(this).find('i').toggleClass('fa-plus-square-o fa-minus-square-o');  
    // });
 
    // $('input[placeholder], textarea[placeholder]').not("input.serch_input_box, input.subscibe_input").placeholderLabel();
    // navcation Icons(header)
         // $(window).on('scroll',function(){
         //   let pos = $(window).scrollTop();
         //   let pos_top = $('.trending_products').offset().top;
         //  //let pos_top = 500000000;
         //   /*if(pos > 0) { $('.home_page_menu').addClass('home_page_menu_scroll'); }
         //   else { $('.home_page_menu').removeClass('home_page_menu_scroll'); }*/

         //   if(pos >= pos_top){
         //     $('.header_navigation').find('.nav_icon_bar').removeClass('hidden');
         //     $('.Category_title_text_home').addClass('hidden');
         //     $('.header_navigation').find('li').eq(0).addClass('sticky_menu_top_btn');
         //     //$('.home_page_menu').addClass('hidden');
         //     $('.home_logo').addClass('logo_scroll_right');
             
         //   }
         //   else{
         //     $('.header_navigation').find('.nav_icon_bar').addClass('hidden');
         //     $('.Category_title_text_home').removeClass('hidden');
         //     $('.header_navigation').find('li').eq(0).removeClass('sticky_menu_top_btn');
         //     //$('.home_page_menu').removeClass('hidden');
         //     $('.home_logo').removeClass('logo_scroll_right');
         //   }
         // }); 
         //    /* affix the navbar after scroll below header */
         //    $("header").affix({offset: {top: $(".sub_navbar").outerHeight(true)} });



 });

    var getOriginal;
    $(document).delegate('.product_thumbs_box img', 'mouseover', function(e)
     { 
        var src = $(this).attr("data-large");
        getOriginal =  $(this).parents('.c-product-card').find('img.largeImage').attr("src");         
        $(this).parents('.c-product-card').find('img.largeImage').attr("src",src);
      })

     $(document).delegate('.product_thumbs_box img', 'mouseout', function(e)
     { 
        $(this).parents('.c-product-card').find('img.largeImage').attr("src",getOriginal);
      })

</script>



<?php
if(!empty($redirect_popup))
    echo $redirect_popup;

if(!empty($show_redirect_popup)) { ?>
    <script type="text/javascript">
       $("#indiaStoreOtherCountryAlert").modal({show: 'true',backdrop: 'static', keyboard: false});
        $(".productInfoAjax, .productRelatedAjax").each(function() {
            $(this).removeClass("fancybox"); // unbind  to click action of fancybox
        });

        $(".addtocart_bottem, .addtocart").each(function() {
            $(this).prop('onclick',null).off('click'); // unbind click action on this button
        });

        $('.productInfoAjax, .addtocart_bottem, .addtocart, .productRelatedView').on('click',function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            var pId = $(this).data('product-id');

            var goToUrl = '<?php echo "http://".INTERNATIONAL_STORE_HOST."/index.php?route=product/product&product_id=";?>' + pId;

            $('.btn-redirect').attr('href',goToUrl);

            $("#indiaStoreOtherCountryAlert").modal({show: 'true',backdrop: 'static', keyboard: false});
            return false;
        });

    </script>
<?php } ?>


</body>

</html>
