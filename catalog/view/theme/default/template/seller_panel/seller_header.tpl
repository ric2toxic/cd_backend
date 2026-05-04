<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>
        Sell your product on Wholesalebox.in | Grow your business with fast-growing Indian wholesale Clothing e-commerce
    </title>
    <base href="<?php echo $base; ?>" />
    <!-- Bootstrap Core CSS -->
    <link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css?v=3" rel="stylesheet" media="screen" />
    <link href="<?php echo LOCAL_CDN_URL_SSL; ?>css/seller_panel/seller-panel-stylesheet.css?v=19" rel="stylesheet" media="screen" />

    <!-- Custom CSS -->
    <link href="<?php echo LOCAL_CDN_URL_SSL; ?>css/seller_panel/sb-admin-2.css?v=3" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css?v=3" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- datepickar filter css -->
    <link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/datetimepicker/daterangepicker.css?v=3" />
    <link href="<?php echo LOCAL_CDN_URL_SSL; ?>css/seller_panel/datepicker.min.css?v=3" rel="stylesheet">
    <!-- jQuery -->
    <!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" ></script> -->
    <script type="text/javascript" src="catalog/view/javascript/jquery/jquery-3.2.0.min.js?v=3" ></script>
        <!-- Datepicker JavaScript -->
    <script src="<?php echo LOCAL_CDN_URL_SSL; ?>js/seller_panel/bootstrap-datepicker.js?v=6"></script> 





    <!-- Bootstrap Core JavaScript -->
    <script  src="catalog/view/javascript/bootstrap/js/bootstrap.min.js?v=3" type="text/javascript"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?php echo LOCAL_CDN_URL_SSL; ?>js/seller_panel/metisMenu/metisMenu.min.js?v=3"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?php echo LOCAL_CDN_URL_SSL; ?>js/seller_panel/sb-admin-2.js?v=3"></script>

    <!-- datepickar filter JavaScript -->
    <script type="text/javascript" src="catalog/view/javascript/jquery/datetimepicker/moment.min.js?v=3"></script>
    <script type="text/javascript" src="catalog/view/javascript/jquery/datetimepicker/daterangepicker.js?v=3"></script> 
    <script type="text/javascript" src="<?php echo LOCAL_CDN_URL_SSL; ?>js/seller_panel/seller_panel.js?v=22"></script>

</head>

<body>

    <div id="wrapper" >
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand sp-logo" href="<?php echo $account_dashboard; ?>">
                <img src="https://cdnimages.net/img/rsz_wsb_tmp_logo_286.png"></a>
            </div>

            <div class="col-sm-6 col-xs-6 gst_title gst_title_text_blink">Transfer Price will be inclusive of GST<span> ( For any help: 9116134791 | 9649558363 )</span></div>
            <!-- /.navbar-header -->
            <div class="col-sm-2 col-xs-3 nopadding head_right_section">
                <ul class="nav navbar-top-links navbar-right">
                    <!-- /.dropdown -->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <!-- <li><a href="<?php echo $account_profile; ?>"><i class="fa fa-user fa-fw"></i> User Profile</a></li> -->
                            <!-- <li class="divider"></li> -->
                            <li><a href="<?php echo $change_password;?>"><i class="fa fa-edit fa-fw"></i>Change Password</a></li>
                            <!-- <li class="divider"></li> -->
                            <li><a href="<?php echo $account_logout; ?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                            </li>
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                    <!-- /.dropdown -->
                </ul>
                <!-- /.navbar-top-links -->
                <div class="header_seller_nickname"> User : <?php echo $get_seller_nickname['nickname']; ?></div>
            </div>
            <div class="sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <!-- remove this menu -->
                            <!-- <a href="<?php echo $account_sales;?>"><i class="fa fa-bar-chart-o fa-fw"></i> Sales</a> -->

                            <?php /* ?><!-- <a href="<?php echo $account_order;?>"><i class="fa fa-bar-chart-o fa-fw"></i> Sales<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="order.html">WholesaleBox</a>
                                </li>
                                <li>
                                    <a href="demo_store.html">Demo Store</a>
                                </li>
                                <li>
                                    <a href="#">Vasavi</a>
                                </li>
                            </ul> --> <?php */ ?>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="<?php echo $account_profile; ?>"><i class="fa fa-user fa-fw"></i> Profile</a>
                        </li>
                        <?php if( $get_seller_status['seller_status'] == 1 ) { ?>
                        <li>
                            <a href="<?php echo $manage_inventory; ?>"><i class="fa fa-list fa-fw"></i> Inventory </a>
                        </li>
                       <?php } ?>
                        <li>
                            <a href="<?php echo $account_order;?>"><i class="fa fa-bar-chart-o fa-fw"></i> Orders</a>
                        </li>                         
                         <li>
                            <a href="<?php echo $order_return;?>"><i class="fa fa-reply fa-fw"></i> Return</a>
                        </li> 
                        <li>
                            <a href="<?php echo $order_payment_report;?>"><i class="fa fa-bar-chart-o fa-fw"></i> Payments Report</a>
                        </li>
                        <li>
                            <a href="<?php echo $gst_report;?>"><i class="fa fa-bar-chart-o fa-fw"></i> GST Report</a>
                        </li>
                        <li>
                            <a href="<?php echo $sor_inventory_link;?>"><i class="fa fa-list fa-fw"></i> SOR Inventory</a>
                        </li> 
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
