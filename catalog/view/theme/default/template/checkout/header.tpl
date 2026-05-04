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
    <title><?php echo $title;?></title>
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
    <link href="catalog/view/theme/default/stylesheet/all-library.css" rel="stylesheet" />
    <?php /* Its embeded in all-library.css ?>
    <link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
    <?php */ ?>
    <?php // Its embeded in all-library.css ?>
    <link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <?php  ?>
    <link href="catalog/view/theme/default/stylesheet/stylesheet_new.css?v=11" rel="stylesheet" />
    <link href="catalog/view/theme/default/stylesheet/colors/<?php echo $color_template;?>/<?php echo $color_template;?>.css" rel="stylesheet" />
    <?php /* Its embeded in all-library.css ?>
    <link href="catalog/view/theme/default/stylesheet/jquery-ui.css" rel="stylesheet" />
    <?php */ ?>
    <?php /* Its embeded in all-library.css ?>
    <link href="catalog/view/theme/default/stylesheet/jquery.fancybox.css" rel="stylesheet" />
    <?php */ ?>

    <?php foreach ($styles as $style) { ?>
    <link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
    <?php } ?>

    <link href="catalog/view/theme/default/css/flags.css" type="text/css" rel="stylesheet" media="screen" />

    <link href="catalog/view/theme/default/javascript/touchspin/jquery.bootstrap-touchspin.css" type="text/css" rel="stylesheet" media="screen" />
    <link href="catalog/view/theme/default/css/bootstrap-tagsinput.css" type="text/css" rel="stylesheet" media="screen" />

    <script type="text/javascript" src="catalog/view/theme/default/javascript/modernizr.2.5.3.min.js"></script>
    <!-- <script  src="catalog/view/theme/default/javascript/theme_common.js" type="text/javascript"></script>
     <script  src="catalog/view/theme/default/javascript/expand-search/modernizr.custom.js" type="text/javascript"></script>-->
    <script   src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="catalog/view/javascript/jquery.flagstrap.min.js"></script>
    <script type="text/javascript" src="catalog/view/theme/default/javascript/touchspin/jquery.bootstrap-touchspin.js"></script>
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




<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="header_top">
        <div class="container-fluid">
            <div class="col-sm-3 logo_width"></div>
            <?php if($international_store == 0) { ?>
            <div class="col-sm-2 discounts_title">DISCOUNTS
                <span class="down"></span>
            </div>
            <?php } ?>

            <div class="col-sm-7 top_header_contant_box">
                <ul class="top_header_contant">
                    <li>Need Help?<label class="header_top_right_icon"><i class="fa fa-caret-right" aria-hidden="true"></i></label>
                    </li>
                    <li><label><i class="fa fa-phone" aria-hidden="true"></i></label> +91 - 141 - 4049163
                    </li>
                    <li><a href="https://web.whatsapp.com/send?phone=918696491521&text=Hello,%20I%20visited%20Wholesalebox.%20I%20am%20interested%20in%20your%20products." target="_blank"><label><i class="fa fa-whatsapp" aria-hidden="true"></i></label> +91 - 8696491521</a>
                    </li>
                    <?php if (!(!empty($this->session->data['payment_method']['code']) && strtolower($this->session->data['payment_method']['code']) == 'upi')) {?>
                    <li><a href="javascript:void(0)" class="call_back_request_btn"><label><i class="fa fa-mobile" aria-hidden="true"></i></label> Call Back Request</a>
                    </li>
                    <?php } ?>
                    <!--                     <li class="login_right"><a  data-toggle="modal" data-target="#login_verify_popup" ><label><i class="fa fa-user" aria-hidden="true"></i></label> Login/Signup</a>
                                        </li> -->
                </ul>
            </div>
        </div>
    </div>
    <div class="full_header navbar">
        <div class="navbar-header logo_box col-sm-3  ">
            <?php if (!empty($this->session->data['payment_method']['code']) && strtolower($this->session->data['payment_method']['code']) == 'upi' && !empty($this->session->data['header_block'])) {?>
                <a class="navbar-brand nopadding logo-max-width"><img src="<?php echo $logo; ?>" class="img-responsive" alt="Wholesalebox"></a>
            <?php } else {?>
            <a class="navbar-brand nopadding logo-max-width" href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" class="img-responsive" alt="Wholesalebox"></a>
            <?php }?>
        </div>
       
        <?php if($international_store == 0) { ?>
        <div class="col-sm-2 discounts_text">2% discount on Prepaid <br> 3% on Rs. 25,000 and above</div>
         <?php } ?>
         
        <div class="col-sm-7 nopadding pull-right">
            <ul class="icon_details">
                <li class="col-sm-3"><label class="add_icons manufacturer"></label> DIRECT FROM <br> MANUFACTURER</li>

                <li class="col-sm-3"><label class="add_icons returns"></label> EASY <br> RETURNS</li>

                <li class="col-sm-3"><label class="add_icons delivery"></label> DOORSTEP <br> DELIVERY</li>

                <li class="col-sm-3"><label class="add_icons payment_pic"></label> SECURE <br> PAYMENT</li>
            </ul>
        </div>

    </div>

</nav>




<style>
    #logo {
        margin:0px !important;
    }
</style>
<input type="hidden" value="<?php echo $this->session->data['ctoken']; ?>" name="ctoken" id="ctoken"  class="ctoken" />
<script>
    $('.call_back_request_btn').click(function(){ $('#error_phone_number').html(""); $('#help_popup_section').modal();});
</script>