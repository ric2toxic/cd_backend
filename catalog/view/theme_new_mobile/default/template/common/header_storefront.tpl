<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700,900 +' rel='stylesheet' type='text/css'>
    <link href="catalog/view/theme_new_mobile/default/stylesheet/stylesheet.css" rel="stylesheet" />

    <?php foreach ($styles as $style) { ?>
    <link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
    <?php } ?>
    <link href="catalog/view/theme_new_mobile/default/stylesheet/colors/<?php echo $color_template;?>/<?php echo $color_template;?>.css" rel="stylesheet" />
    <script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>



    <?php echo $google_analytics; ?>

    <!-- End Facebook Pixel Code -->
</head>
<body class="<?php echo $class; ?>">

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
                <a href="<?php echo $home;?>" style="color: white;
    position: relative;
    top: 15px;"><?php echo $this->config->get('config_name');?>
<!--                     <img src="http://www.wholesalebox.in/image/mobile_logo2.png<?php // echo $mobile_logo;?>" class="img-responsive logo" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" width="130" style="margin-top: 12px;" />
 -->                    <!--<img src="< ?php echo $mobile_logo; ?>" title="< ?php echo $name; ?>" alt="< ?php echo $name; ?>" class="img-responsive" /> -->
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
                <form action=" " method="get">
                    <input type="text" id="searchInput" class="form-control searchInput" name="search" value="<?php echo $searchText; ?>" placeholder="<?php echo $searchText; ?>" />
                    <input type="hidden" name="route" value="product/search" />
                    <!--<input class="sb-search-submit" type="submit" value="">-->
                </form>
            </div>

            <div class="col-xs-1">
                <a href="<?php echo $wishlist; ?>">
					<!--<i class="material-icons heart">favorite_border</i>-->
					<i class="material-heart-custom heart"></i>	
				</a>
            </div>
            <div class="col-xs-1"><div class="mini_cart"><?php echo $cart; ?></div></div>
            <div class="col-xs-1"></div>
        </div>
    </div>

    <?php  if ($categories) { ?>

    <nav id="myNavmenu" class="navmenu navmenu-default navmenu-fixed-left offcanvas" role="navigation">
        <!-- <a class="navmenu-brand" href="#">Brand</a>-->

        <ul class="nav navmenu-nav" >
            <?php foreach ($categories as $category) { ?>
            <?php if ($category['children']) { ?>

            <li class=" dropdown active">

                <a href="<?php echo $category['href']; ?>" class="dropdown-mobile" ><?php echo $category['name']; ?><span class="caret"></span></a>
                <?php foreach (array_chunk($category['children'], ceil(count($category['children']) / $category['column'])) as $children) { ?>
                <ul  class="list-unstyled dropdown-mobile-content " role="menu" style="display:none;" >
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
            <li ><a href="javascript:void(0);" id="store_switch_filter"><?php if (isset($this->session->data['custom_store'])) { ?>
                    <?php if ($this->session->data['custom_store'] == 'single') {
                    echo $text_wholesale_set_store;}
                    else {
                    echo $text_single_store;
                    } ?>
                    <?php }else { echo $text_single_store;} ?></a></li>
<!--             <li><a href="<?php //echo $base; ?>about-us">About us</a></li>
            <li><a href="<?php// echo $base; ?>about-us#team">Meet the team</a></li>
             <li><a href="<?php echo $base; ?>index.php?route=information/contact">Contact us</a></li> -->
            <?php if ($logged) { ?>
            <li><a href="<?php echo $account;?>"><?php echo $text_account; ?></a></li>
            <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
            <?php } else { ?>
            <li><a href="<?php echo $login; ?>"><?php echo $text_login_signup; ?></a></li>
            <?php } ?>
            <li>&nbsp;</li>
            <li>&nbsp;</li>
        </ul>
    </nav>

    <?php } ?>


</div>
<div class="mobile-margin">&nbsp;</div>
<style>
.material-icons {
    text-rendering: optimizeLegibility;
}
</style>
