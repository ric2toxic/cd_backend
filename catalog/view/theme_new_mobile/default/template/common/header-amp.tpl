<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" amp>
<!--<![endif]-->
<head>
    <meta charset="UTF-8" />
    <meta name="google-play-app" content="app-id=in.wholesalebox">
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

    <link href="catalog/view/theme_new_mobile/default/stylesheet/all-library.css" rel="stylesheet" media="screen" />

    <link href="catalog/view/theme_new_mobile/default/stylesheet/stylesheet.css" rel="stylesheet" />

    <?php foreach ($styles as $style) {
       if($style['href'] != 'catalog/view/theme/default/stylesheet/stylesheet_new.css') {
     ?>
    <link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
    <?php } } ?>
    
    <link href="catalog/view/theme_new_mobile/default/stylesheet/colors/<?php echo $color_template;?>/<?php echo $color_template;?>.css" rel="stylesheet" />

    <link href="catalog/view/theme_new_mobile/default/css/flags.css" type="text/css" rel="stylesheet" media="screen" />

 <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both;background: #fff;} @-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>


    <script src="catalog/view/javascript/jquery/jquery-3.2.0.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="catalog/view/javascript/jquery.flagstrap.min.js"></script>
    <script type="text/javascript" src="catalog/view/theme/default/javascript/jquery.placeholder.label.js"></script>

    <?php echo $social_meta_tags; ?>

 <?php if ($store_id == INTERNATIONAL_STORE_ID) { ?>
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
    <!--<script> window._izq = window._izq || []; window._izq.push(["init"]); </script>
    <script src="//cdn.izooto.com/scripts/b3d1b5a840cc965212d75ff5389b37023a40bfed.js"></script> -->
</head>
<body class="<?php echo $class; ?>">
    <?php if ($store_id == INTERNATIONAL_STORE_ID) { ?>
    
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MBB945"
                          height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    <?php } else { ?>
    
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TRP9H5"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php } ?>

<div id="header-mobile" class="scrollspy">

    <div id="sticky" class="affix">

        <div class="row">
            <div class="col-xs-2">
                <div class="navbar navbar-default navbar-fixed-top">

                    <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target="#myNavmenu" data-canvas="body">
                        <!--<a href="javascript:void(0);"><i class="material-icons menu">menu</i></a>-->
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
            </div>
            <div class="col-xs-5">
                <a href="<?php echo $home;?>">
                    <img src="<?php  echo $mobile_logo;?>" class="img-responsive logo" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" width="130" style="margin-top: 12px;" />
                    <!--<img src="< ?php echo $mobile_logo; ?>" title="< ?php echo $name; ?>" alt="< ?php echo $name; ?>" class="img-responsive" /> -->
                </a>
            </div>
            <div class="col-xs-1"></div>
            <div class="col-xs-1">
                <a href="javascript:void(0);" id="ic_search">
                    <!--<i class="material-icons search SearchEffect">search</i>-->
                    <i class="material-search-custom search SearchEffect"></i>
                </a>
                <!-- comment on (02-01-2016) -->
                <!--<form action="http://www.wholesalebox.com/" method="get">-->
                <form action="<?php echo $home; ?>" method="get">
                    <input type="text" id="searchInput" class="form-control searchInput" name="search" value="<?php echo $searchText; ?>" placeholder="<?php echo $searchText; ?>" />
                    <input type="hidden" name="route" value="product/search" />
                    <!--<input class="sb-search-submit" type="submit" value="">-->
                </form>
            </div>

            <div class="col-xs-1">
                <?php if ($logged) { ?>
                    <a href="<?php echo $wishlist; ?>">
                    <i class="material-heart-custom heart"></i> 
                   </a>              
                 <?php } else { ?>
                    <a data-toggle="modal" data-target="#login_verify_popup">
                    <i class="material-heart-custom heart"></i> 
                    </a> 
                <?php } ?>
            </div>
            <div class="col-xs-1"><div class="mini_cart"><?php echo $cart; ?></div></div>
            <div class="col-xs-1"></div>
        </div>
    </div>

    <?php  if ($categories) { ?>

    <nav id="myNavmenu" class="navmenu navmenu-default navmenu-fixed-left offcanvas" role="navigation">
        <!-- <a class="navmenu-brand" href="#">Brand</a>-->

        <ul class="nav navmenu-nav" >
            <li class="highlighted"><a href="javascript:void(0);" id="store_switch_filter"><?php if (isset($this->session->data['custom_store'])) { ?>
                    <?php if ($this->session->data['custom_store'] == 'single') {
                    echo $text_wholesale_set_store;}
                    else {
                    echo $text_single_store;
                    } ?>
                    <?php }else { echo $text_single_store;} ?></a></li>
            <?php if(isset($sor_store_link_show) && $sor_store_link_show == 1){ ?>
      <!--  <li class="sor highlighted">
                <a href="<?php echo $sor_store_link; ?>" ><?php echo $text_store_link; ?></a>
            </li> -->
            <?php } ?>
            <?php foreach ($categories as $category) { ?>
            <?php if ($category['children']) { ?>

            <li class=" dropdown active">

                <a href="<?php echo $category['href']; ?>" class="dropdown-mobile" id="wsb-nav-mobile<?php echo $category['name_for_id']; ?>"><?php echo $category['name']; ?><span class="caret"></span></a>
                <?php foreach (array_chunk($category['children'], ceil(count($category['children']) / $category['column'])) as $children) { ?>
                <ul  class="list-unstyled dropdown-mobile-content " role="menu" style="display:none;" >
                    <?php foreach ($children as $child) { ?>
                    <li><a href="<?php echo $child['href']; ?>" id="wsb-nav-mobile<?php echo $child['name_for_id']; ?>"><?php echo $child['name']; ?></a></li>
                    <?php } ?>
                    <li><a href="<?php echo $category['href']; ?>" class="see-all" id="wsb-nav-mobile<?php echo $category['name_for_id']; ?>-see-all"><?php echo $text_all; ?> <?php echo $category['name']; ?></a></li>
                </ul>
                <?php } ?>

            </li>
            <?php } else { ?>
            <li><a href="<?php echo $category['href']; ?>" id="wsb-nav-mobile<?php echo $category['name_for_id']; ?>"><?php echo $category['name']; ?></a></li>
            <?php } ?>
            <?php } ?>

            <li class="highlighted"><a  id="topbar-aboutus" href="<?php echo $base; ?>about-us">About us</a></li>
            <li class="highlighted"><a id="topbar-tnc" href="<?php echo $base; ?>about-us#team">Meet the team</a></li>
            <li class="highlighted"><a id="topbar-contactus" href="<?php echo $base; ?>index.php?route=information/contact">Contact us</a></li>
            <?php if ($logged) { ?>
            <li class="highlighted"><a href="<?php echo $account;?>"><?php echo $text_account; ?></a></li>
            <li class="highlighted"><a id="topbar-login" href="javascript:void(0)" onclick="logout()"><?php echo $text_logout; ?></a></li>
            <?php } else { ?>
            <li><a id="login_link_header" href="javascript:;" class="highlighted" data-toggle="modal" data-target="#login_verify_popup"><?php echo $text_login_signup; ?></a></li>
            <?php } ?>
            <li>&nbsp;</li>
            <li>&nbsp;</li>
        </ul>
    </nav>

    <?php } ?>


</div>
<div class="mobile-margin">&nbsp;</div>
<script>
    function logout(){
        var customer_id = getCookie('customer_id');
        localStorage.removeItem(customer_id+'_cart_data');
        window.location = '<?php echo $logout; ?>';
    }
</script>
<style>
.material-icons {
    text-rendering: optimizeLegibility;
}
</style><input type="hidden" value="<?php echo $this->session->data['ctoken']; ?>" name="ctoken" id="ctoken"  class="ctoken" />
