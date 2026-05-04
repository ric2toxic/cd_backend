<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php // echo $direction; ?>" lang="<?php // echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php // echo $direction; ?>" lang="<?php // echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<!--<![endif]-->
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>seller</title>
    <script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
    <link href="catalog/view/theme_new_mobile/default/stylesheet/styleseller.css" rel="stylesheet" />

    <script  src="catalog/view/javascript/jquery/jquery-ui.min.js" type="text/javascript"></script>

    <link href="catalog/view/theme_new_mobile/default/javascript/jasny-bootstrap/css/jasny-bootstrap.min.css" rel="stylesheet" media="screen" />

    <link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />

    <script  src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

    <link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!--<link href="//fonts.googleapis.com/css?family=Open+Sans:400,400i,300,700" rel="stylesheet" type="text/css" />-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700,900 +' rel='stylesheet' type='text/css'>

    <script  src="catalog/view/theme_new_mobile/default/javascript/jasny-bootstrap/js/jasny-bootstrap.min.js" type="text/javascript"></script>
    <script  src="catalog/view/theme_new_mobile/default/javascript/fancybox/jquery.fancybox.pack.js" type="text/javascript"></script>

    <!--<script  src="catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js" type="text/javascript"></script>-->

    <link href="catalog/view/theme_new_mobile/default/stylesheet/stylesheet.css" rel="stylesheet" />
    <link href="catalog/view/theme_new_mobile/default/stylesheet/stylesheet-popup.css" rel="stylesheet" />

    <link href="catalog/view/theme_new_mobile/default/stylesheet/expand-search/component.css" rel="stylesheet" />
    <link href="catalog/view/theme_new_mobile/default/stylesheet/jquery.fancybox.css" rel="stylesheet" />
    <!--<link href="catalog/view/javascript/jquery/magnific/magnific-popup.css" rel="stylesheet" />-->
    <link href="catalog/view/theme_new_mobile/default/stylesheet/colors/<?php echo $color_template;?>/<?php echo $color_template;?>.css" rel="stylesheet" />

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
                <a href="http://www.wholesalebox.in">
                    <img src="http://www.wholesalebox.in/image/mobile_logo2.png<?php // echo $mobile_logo;?>" class="img-responsive logo" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" width="130" style="margin-top: 12px;" />
                    <!--<img src="< ?php echo $mobile_logo; ?>" title="< ?php echo $name; ?>" alt="< ?php echo $name; ?>" class="img-responsive" /> -->
                </a>
            </div>
            <div class="col-xs-2"></div>
            <div class="col-xs-1">
                <a href="javascript:void(0);" id="ic_search">
                    <i class="material-icons search SearchEffect">search</i>
                </a>
                <form action=" " method="get">
                    <input type="text" id="searchInput" class="form-control searchInput" name="search" value="<?php echo $searchText; ?>" placeholder="<?php echo $searchText; ?>" />
                    <input type="hidden" name="route" value="product/search" />
                    <!--<input class="sb-search-submit" type="submit" value="">-->
                </form>
            </div>

           <!-- <div class="col-xs-1">
                <a href="<?php // echo $wishlist; ?>"><i class="material-icons heart">favorite_border</i></a>
            </div>
            <div class="col-xs-1"><div class="mini_cart"><?php // echo $cart; ?></div></div>
            <div class="col-xs-1"></div>-->
        </div>
    </div>



    <nav id="myNavmenu" class="navmenu navmenu-default navmenu-fixed-left offcanvas" role="navigation">
        <!-- <a class="navmenu-brand" href="#">Brand</a>-->

        <ul class="nav navmenu-nav" >

            <?php if ($logged) { ?>
                <!--<li><span class="fa fa-user new_s"></span><a href="<?php echo $this->url->link('seller/account-profile', '', 'SSL'); ?>">Settings</a></li>
                <li class="ben_li"><span class="fa fa-pencil-square-o new_s"></span><a href="<?php echo $this->url->link('account/password', '', 'SSL'); ?>">Change Password</a></li>
                <li class="ben_li"><span class="fa fa-search-plus new_s"></span><a href="<?php echo $this->url->link('seller/account-order', '', 'SSL'); ?>">View Orders</a></li>
                <li class="ben_li"><span class="fa fa-pencil-square-o new_s"></span><a href="<?php echo $manage; ?>">Manage Inventory</a></li> -->

                <li><span class="fa fa-user new_s"></span><a href="<?php echo $this->url->link('seller_panel/profile', '', 'SSL'); ?>">Profile</a></li>
                <li class="ben_li"><a href="<?php echo $this->url->link('account/password', '', 'SSL'); ?>">Change Password</a></li>
                <li class="dropdown"><a href="<?php echo $this->url->link('seller/account-order', '', 'SSL'); ?>">Sales</a>
                    <?php if(!empty($strs)){ ?>
                        <ul class="list-unstyled dropdown-menu" role="menu">
                            <li>
                                <a href="<?php echo $this->url->link('seller/account-order', '', 'SSL'); ?>">Orders</a>
                            </li>
                            <li>
                                <a href="<?php echo $this->url->link('seller/account-customer', '', 'SSL'); ?>">Customers</a>
                            </li>
                        </ul>
                    <?php } ?>
                </li>
                <?php if( $seller_status ==1 ) { ?>
                        <li class="dropdown"><a href="<?php echo $manage; ?>">Inventory</a>
                            <ul class="list-unstyled dropdown-menu" role="menu">
                                <li>
                                    <a href="<?php echo $import;?>">Bulk Inventory</a>
                                  
                                </li>
                                <li>
                                    <a href="<?php echo $manage; ?>">Manage Inventory</a>
                                </li>
                                <li>
                                    <a href="<?php echo $update_bulk_price; ?>">Update bulk price </a>
                                </li>
                                <!--<li>
                                    <a href="<?php echo $add_products; ?>">Add New Product</a>
                                </li>
                                <li>
                                    <a href="<?php echo $add_upcoming_design; ?>">Upcoming Design</a>
                                </li> -->
                                <?php if(!empty($strs)){ ?>
                                <li>
                                    <a href="<?php echo $reviews; ?>"> Reviews</a>
                                </li>
                                <?php } ?>
                            </ul>
                        </li>
                        <?php if(!empty($strs)){ ?>
                        <li class="dropdown"><a href="<?php echo $coupons; ?>"><i class="fa fa-share-alt fa-fw"></i>Marketing</a>
                            <ul class="list-unstyled dropdown-menu" role="menu">
                                <li>
                                    <a href="<?php echo $coupons; ?>">Coupons</a>
                                </li>
                            </ul>
                        </li>
                        <?php } ?>
                    <?php } ?>
            <?php }else{ ?>
                <li><a href="<?php echo HTTP_SERVER; ?>"> Wholesalebox.in</a></li>
                <li class="ben_li"><a href="#" id="scrollto_ben"> Benefits</a></li>
                <li class="ben_li"><a href="#" id="scrollto_htosell"> How to sell</a></li>

            <?php } ?>


            <?php if ($logged) { ?>
                <!-- <li><a href="<?php // echo $account;?>"><?php // echo $text_account; ?></a></li> -->
                <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
            <?php } else { ?>
                <li><a href="<?php echo $login; ?>"><?php echo $text_login_signup; ?></a></li>
            <?php } ?>
            <li>&nbsp;</li>
            <li>&nbsp;</li>
        </ul>
    </nav>




</div>
<div class="mobile-margin">&nbsp;</div>
<script>
    $(document).ready(function(){
        $(".SearchEffect").click(function(){

            //$('#searchInput').toggle('fast');

            if($('#searchInput').is(":visible")){
                $('#searchInput').hide('fast');
                $(this).removeClass('clear').addClass('search').html('search');
                $("i.heart").show();
                $("img.logo").show();

            }

            if($('#searchInput').is(":hidden")){
                $('#searchInput').show('fast');
                $(this).removeClass('search').addClass('clear').html('clear');
                $("img.logo").hide();
            }

        });
    });
    $('#store_switch_filter').on('click', function (argument) {
        $.ajax({
            url : 'index.php?route=common/header/getStoreSwitchNew',
            dataType: 'json',

            beforeSend: function () {
                $('body').removeClass('loaded').addClass('loading');
            },
            success: function (json) {
                location.reload();

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }

        });
    });

</script>
