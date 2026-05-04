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
    <link href="catalog/view/theme_new_mobile/default/stylesheet/all-library.css" rel="stylesheet" />
    <?php /* Its embeded in all-library.css ?>
    <link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
    <?php */ ?>
    <?php // Its embeded in all-library.css ?>
    <link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <?php  ?>
    <link href="catalog/view/theme_new_mobile/default/stylesheet/stylesheet.css?v=10" rel="stylesheet" />
    <link href="catalog/view/theme_new_mobile/default/stylesheet/colors/<?php echo $color_template;?>/<?php echo $color_template;?>.css" rel="stylesheet" />
    <?php /* Its embeded in all-library.css ?>
    <link href="catalog/view/theme_new_mobile/default/stylesheet/jquery-ui.css" rel="stylesheet" />
    <?php */ ?>
    <?php /* Its embeded in all-library.css ?>
    <link href="catalog/view/theme_new_mobile/default/stylesheet/jquery.fancybox.css" rel="stylesheet" />
    <?php */ ?>

    <?php foreach ($styles as $style) { ?>
    <link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
    <?php } ?>

    <link href="catalog/view/theme_new_mobile/default/css/flags.css" type="text/css" rel="stylesheet" media="screen" />

    <link href="catalog/view/theme_new_mobile/default/javascript/touchspin/jquery.bootstrap-touchspin.css" type="text/css" rel="stylesheet" media="screen" />

    <script type="text/javascript" src="catalog/view/theme_new_mobile/default/javascript/modernizr.2.5.3.min.js"></script>
    <!-- <script  src="catalog/view/theme/default/javascript/theme_common.js" type="text/javascript"></script>
     <script  src="catalog/view/theme/default/javascript/expand-search/modernizr.custom.js" type="text/javascript"></script>-->
    <script   src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="catalog/view/javascript/jquery.flagstrap.min.js"></script>
    <script type="text/javascript" src="catalog/view/theme_new_mobile/default/javascript/touchspin/jquery.bootstrap-touchspin.js"></script>
    <script type="text/javascript" src="catalog/view/theme_new_mobile/default/javascript/jquery.placeholder.label.js"></script>

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




<!-- Header -->
<!--<header class="navbar-fixed-top checkout_top_header" role="navigation">
    <div class="pull-left">
        <a class="navbar-toggle" id="checkout_header_back" href="javascript:void(0)">
            &nbsp;<span class="checkout_title" id="checkout_header">CHECKOUT</span>
        </a>
    </div>
    <?php if (!(!empty($this->session->data['payment_method']['code']) && strtolower($this->session->data['payment_method']['code']) == 'upi')) {?>
    <div class="pull-right">
        <span class="head_right_icon_box"><a href="javascript:void(0)" class="head_right_btn">HELP ?</a></span>
    </div>
    <?php } ?>

    <div class="clearfix"></div>
</header>
<div class="mobile-margin checkout_top_header">&nbsp;</div>
<div class="mobile-margin checkout_top_header">&nbsp;</div><div class="mobile-margin">&nbsp;</div>-->

<!-- Header End -->
<script>
    $('.head_right_btn').click(function(){ $('#error_phone_number').html(""); $('#help_popup_section').modal();});
</script>

<style>
    #logo {
        margin:0px !important;
    }
</style>
<input type="hidden" value="<?php echo $this->session->data['ctoken']; ?>" name="ctoken" id="ctoken"  class="ctoken" />