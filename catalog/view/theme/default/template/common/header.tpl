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

    <!--<link href="//fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />-->
    <link href="catalog/view/theme/default/stylesheet/all-library.css" rel="stylesheet" />
    <?php /* Its embeded in all-library.css ?>
    <link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
    <?php */ ?>
    <?php // Its embeded in all-library.css ?>
    <!--<link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />-->
    <?php  ?>
    <link href="catalog/view/theme/default/stylesheet/stylesheet_new.css?v=10" rel="stylesheet" />
    <link href="catalog/view/theme/default/stylesheet/colors/<?php echo $color_template;?>/<?php echo $color_template;?>.css" rel="stylesheet" />
    <?php /* Its embeded in all-library.css ?>
    <link href="catalog/view/theme/default/stylesheet/jquery-ui.css" rel="stylesheet" />
    <?php */ ?>
    <?php /* Its embeded in all-library.css ?>
    <link href="catalog/view/theme/default/stylesheet/jquery.fancybox.css" rel="stylesheet" />
    <?php */ ?>

    <?php /* foreach ($styles as $style) { ?>
    <link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
    <?php } */?>

  <!-- <link href="catalog/view/theme/default/css/flags.css" type="text/css" rel="stylesheet" media="screen" />

    <link href="catalog/view/theme/default/javascript/touchspin/jquery.bootstrap-touchspin.css" type="text/css" rel="stylesheet" media="screen" />

    <script type="text/javascript" src="catalog/view/theme/default/javascript/modernizr.2.5.3.min.js"></script>
    <script  src="catalog/view/theme/default/javascript/theme_common.js" type="text/javascript"></script>
    <script  src="catalog/view/theme/default/javascript/expand-search/modernizr.custom.js" type="text/javascript"></script>
    <script   src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="catalog/view/javascript/jquery.flagstrap.min.js"></script>
    <script type="text/javascript" src="catalog/view/theme/default/javascript/touchspin/jquery.bootstrap-touchspin.js"></script>
    <script type="text/javascript" src="catalog/view/theme/default/javascript/jquery.placeholder.label.js"></script>-->

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
    <!-- End Google Tag Manager (noscript)    -->
    <?php } ?>



<div id="header-desktop">

    <header>
        <div class="container-fluid home_outer">
            <div class="row home_width_adjustment">
                <div>
                    <nav id="top">
                        <?php //echo $currency; ?>
                                
                        <div id="top-links" class="nav col-sm-12">
                            <ul class="list-inline pull-right">
                                <?php if($international_store == 0) { ?>
                                <li class="clearance_sale">
                                    <a id="topbar-sale" href="https://www.wholesalebox.in/sale"><button>Sale</button></a>
                                </li>
                                <?php } ?>
                                <li><span><?php  echo $language; ?></span></li>
                                    <li>
                                        <a href="<?php echo $careers; ?>" id=" topbar-careers"><span class="hidden-xs hidden-sm hidden-md blink_me neon-text">
                                    <?php echo $text_we_are_hiring; ?></span></a>
                                    </li>
                                    <?php if(($_SERVER['HTTP_HOST']==INDIA_STORE_HOST || $_SERVER['HTTP_HOST']==SOR_STORE_HOST) && isset($sor_store_link_show) && $sor_store_link_show == 1){ ?>
                              <!-- <li class="sor">
                                        <a href="<?php echo $sor_store_link; ?>" id="topbar-salereturn" ><span class="hidden-xs hidden-sm hidden-md blink_text">&nbsp;<?php echo $text_store_link; ?></span></a>
                                    </li> -->
                                    <?php } ?>
                                    
                                    <?php if($logged){ 
											if($seller_login){ ?>
												<li><a id="topbar-sell" href="<?php echo $manufacturer_dashboard_link; ?>"><i class="fa fa-money"></i>&nbsp;<span class="hidden-xs hidden-sm hidden-md change_color_dashbord"><?php echo $dashboard; ?></span> </a> </li>
                                    <?php 	} 
										   } else { ?>
												<li><a id="topbar-sell" href="<?php echo $manufacturer_link; ?>"><i class="fa fa-money"></i>&nbsp;<span class="hidden-xs hidden-sm hidden-md"><?php echo $manufacturer; ?></span> </a> </li>
									<?php  } ?>
                                    
                                    <?php if ($logged) { ?>
                                    <li><a href="<?php echo $wishlist; ?>" id="wishlist-total" title="<?php echo $text_wishlist; ?>"><i class="fa fa-heart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_wishlist; ?></span></a></li>
                                    <?php } else { ?>
                                    <li><a data-toggle="modal" data-target="#login_verify_popup" id="wishlist-total" title="<?php echo $text_wishlist; ?>"><i class="fa fa-heart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_wishlist; ?></span></a></li>
                                    <?php } ?>
                                        <!--<li><a href="<?php //  echo $shopping_cart; ?>" title="<?php // echo $text_shopping_cart; ?>"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php // echo $text_shopping_cart; ?></span></a></li>
                                        <li><a href="<?php // echo $checkout; ?>" title="<?php // echo $text_checkout; ?>"><i class="fa fa-share"></i> <span class="hidden-xs hidden-sm hidden-md"><?php // echo $text_checkout; ?></span></a></li>-->
                                    <li><i class="fa fa-phone"></i>&nbsp;<span class="hidden-xs hidden-sm hidden-md"><?php echo $telephone; ?></span></li>
                                    
                                    <?php
                                   
                                     if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) { ?>
                                    <li><i class="fa fa-whatsapp"></i>&nbsp;<span>(+91) 9116134795</span></li>
                                    <?php } else { ?>
                                    <li><i class="fa fa-whatsapp"></i>&nbsp;<span>(+91) 8696491521</span></li>
                                    <?php } ?>
                                    
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
                                        <?php /* ?><a id="topbar-login" href="<?php echo $link; ?>" title="<?php echo $text_account; ?>" class="dropdown-toggle <?php echo $class; ?>" data-toggle="dropdown"><i class="fa fa-user"></i> <span class="hidden-xs hidden-sm hidden-md"><?php if ($logged) { echo " ".$cust_name; }else {echo $text_login_signup;}?></span><span class="caret"></span></a><?php */ ?>
                                            <?php /* ?><!--<a href="<?php echo $link; ?>" title="<?php echo $text_account; ?>" class="dropdown-toggle <?php echo $class; ?>" data-toggle="dropdown"><i class="fa fa-user"></i> <span class="hidden-xs hidden-sm hidden-md"><?php if ($logged) { echo " ".$cust_name; }else {echo $text_login_signup;}?></span><span class=""></span></a> --> <?php */ ?>
                                        <?php if($logged){ ?>
                                        <a href="<?php echo $link; ?>" title="<?php echo $text_account; ?>" class="dropdown-toggle <?php echo $class; ?>" data-toggle="dropdown"><i class="fa fa-user"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo " ".$cust_name; ?></span><span class=""></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <?php if ($logged) { ?>
                                                <li><a href="<?php echo $order; ?>"><?php echo $text_my_orders; ?></a></li>
                                                <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
                                                <?php /* ?> <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li><?php */ ?>
                                                <li><a href="javascript:void(0)" onclick="logout()"><?php echo $text_logout; ?></a></li>
                                                <?php } else { ?>
                                                <li><a href="<?php //echo $register; ?>"><?php //echo $text_register; ?></a></li>
                                                <li><a href="<?php //echo $login; ?>"><?php //echo $text_login; ?></a></li>
                                                <?php } ?>
                                            </ul>
                                            <?php }else{ ?>

                                            <a id="login_link_header" data-toggle="modal" data-target="#login_verify_popup"><i class="fa fa-user"></i>
                                             <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_login_signup; ?></span><span class=""></span></a>
                                            <?php }?>

                                        </li>


                                    </ul>
                                </div>
                            </nav>
                        </div>
                    </div>
                    <div class="row sticky_new">
                        <div class="col-sm-12">
                            <div class="row sticky home_width_adjustment">
                                <div class="col-sm-2 nopadding">
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
                                <div class="col-sm-10">
                                        <div class="col-sm-6"><?php echo $search; ?></div>

                                        <div class="col-sm-2 nopadding">
                                                                                           
                                                <div class="cart_in_header">
                                                    <?php  echo $cart; ?>
                                                </div>
                                            
                                        </div>
                                        <a href="http://www.wholesalebox.in/storelocator" id="topbar-storelocator"><div class="store col-sm-1"><span><i class="fa fa-map-marker" aria-hidden="true"></i></span> STORES</div></a>
                                        <div class="wishlist_in_header col-sm-3 header_store_btn">
                                        <a href="javascript:void(0);" id="store_switch"><?php if (isset($this->session->data['custom_store'])) { ?>
                                        <?php if ($this->session->data['custom_store'] == 'single') {
                                        echo $text_wholesale_set_store;}
                                        else {
                                        echo $text_single_store;
                                        } ?>
                                        <?php }else { echo $text_single_store;} ?></a>
                                        </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="category-section home_width_adjustment">
                   <section id="top_nav">
                        <div class="container-fluid home_width_adjustment">
                            <div class="collapse navbar-collapse navbar-ex1-collapse nopadding" id="menu-wrapper">
                                <?php  if (isset($menus) && !empty($menus)) { ?>
                                <ul class="navbar-nav menu">
                                    <?php foreach ($menus as $category) { ?>
                                    <?php if ($category['children']) { ?>
                                    <li>
                                        <a href="<?php echo $category['href']; ?>" id="wsb-nav-<?php echo $category['name_for_id']; ?>"><?php echo $category['name']; ?></a>
                                        <?php $i = 0;
                                    foreach ($category['children'] as $children) {
                                      if($children['href'] == 'megamenu'){ ?>
                                        <?php echo $children['name']; ?>
                                        <?php }else{
                                        if($i == 0){ ?>
                                        <ul class="menu-dropdown">
                                            <?php $i++; } ?>
                                            <li><a href="<?php echo $children['href']; ?>" id="wsb-nav-<?php echo $children['name_for_id']; ?>"><?php echo $children['name']; ?></a></li>
                                            <?php } ?>
                                            <?php
                                    }
                                    if($children['href'] != 'megamenu'){ ?>
                                            <li><a href="<?php echo $category['href']; ?>" id="wsb-nav-<?php echo $category['name_for_id']; ?>-see-all"><?php echo $text_all; ?> <?php echo $category['name']; ?></a></li>
                                            <?php }
                                    if($i != 0){ ?>
                                        </ul>
                                        <?php } ?>
                                    </li>
                                    <?php } else { ?>
                                    <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
                                    <?php } ?>
                                    <?php } ?>
                                    <?php }else{ ?>
                                    <ul class="nav navbar-nav menu">
                                        <?php  if ($categories) { ?>
                                        <?php foreach ($categories as $category) { ?>
                                        <?php if ($category['children']) { ?>
                                        <li class="dropdown">
                                            <a href="<?php echo $category['href']; ?>" class="dropdown-toggle"   ><?php echo $category['name']; ?></a>
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
                                        <?php } ?>
                                    </ul>


                            </div>
                        </div>
                    </section>
                    </div>
    </header>
</div>


<style>
    #logo {
        margin:0px !important;
    }
</style>
<script>
    function logout(){
        var customer_id = getCookie('customer_id');
        localStorage.removeItem(customer_id+'_cart_data');
        window.location = '<?php echo $logout; ?>';
    }
</script>
<input type="hidden" value="<?php echo $this->session->data['ctoken']; ?>" name="ctoken" id="ctoken"  class="ctoken" />
