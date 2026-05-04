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
.navbar {min-height: 50px;}
.cart_statement_pree_load{width: 25%;min-height: 300px;  margin-top: 8px;}
.cart_full_box .cart_box .cart_main_title{border-radius: 0;}
.panel-body .cart_table .cart_table_height{ min-height: 400px; }
.error_border{ border:solid 1px #a94442!important; }
.error_text{color: #a94442;}
</style>

<?php if ($international_store == 1) { ?>
    <!-- Google Tag Manager -->
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
<body class="loaded my_account_body">
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

<!-- Content-->
<div class="container-fluid width_fix">
  
    <div class="row my_account">
        <div id="content" class="col-sm-12">
            <div class="col-sm-12">
                <div class="page_mail_title">
                    <h3>Order No: #<?php echo $order_no; ?></h3><br>
                    <?php if(isset($master_return_no)){ ?>
                        <h3>Return No: #<?php echo $master_return_no; ?></h3>
                    <?php } ?>
                    <div class="clearfix"></div>
                </div>
                <?php if(isset($error)){ ?>
                <div class='error'>
                    <div class="alert alert-danger" style="margin-top: 10px;"><?php echo $error; ?></div>
                </div>
                <?php } ?>
                <?php if (isset($success)) { ?>
                    <div class="alert alert-success" style="margin-top: 10px;"><?php echo $success; ?></div>
                <?php } ?>
            </div>
            <div class="cart_full_box">
                <div class="col-sm-12">
                    <div class="panel-group" id="accordion">
                    <!-- shopping cart saction(start) -->
                        <section class="panel cart_box">
                            <div>
                            <div class="panel-body nopadding">
                                <div class="cart_table">
                                    <form action="" method="post" id="form-return"  enctype="multipart/form-data" class="form-horizontal"  >
                                        <table width="100%" class="table-striped table-hover">
                                            <thead>
                                            <tr class="cart_table_title">
                                                <th><?php echo $entry_product;?></th>
                                                <th><?php echo $entry_total_pieces;?></th>
                                                <th><?php echo $entry_price;?></th>
                                                <th><?php echo $entry_return_reason;?></th>
                                                <th><?php echo $entry_piece_to_return;?></th>
                                                <th><?php echo $entry_comment;?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(isset($master_return_id)){ ?>
                                                <input type="hidden" id="view_return" value="<?php echo $master_return_id; ?>">
                                                <?php }else{ ?>
                                                <input type="hidden" id="view_return" value="" >
                                                <?php } ?>
                                                <?php if(!empty($products) && $all_returns !="true"){
                                                    $i = 1;
                                                    foreach($products as $key => $product){
                                                      $total_pieces = (int) $product['quantity'] * (int) $product['piece_in_set'];
                                                      $return_id = !empty($returns[$key]['return_id']) ? $returns[$key]['return_id'] : 0;
                                                      $check_master_return_id = !empty($returns[$key]['master_return_id']) ? $returns[$key]['master_return_id'] : 0;
                                                      $total_quantity = !empty($returns[$key]['total_quantity']) ? $returns[$key]['total_quantity'] : 0;
                                                      $pervious_quantity = !empty($returns[$key]['pervious_quantity']) ? $returns[$key]['pervious_quantity'] : 0;
                                                      
                                                      $is_returnable = $product['is_returnable'];

                                                      if(empty($total_quantity) && empty($return_id)){
                                                        $show = true;
                                                      }
                                                      if(!empty($return_id) && empty($total_quantity)){
                                                        $show = false;
                                                      }
                                                      if(!empty($return_id) && !empty($total_quantity)){
                                                        $show = true;
                                                      }
                                                      if(!isset($master_return_id) && empty($master_return_id) && !empty($show)){
                                                        if(!empty($total_quantity) ){
                                                            $total_pieces = $total_quantity; 
                                                        }
                                                ?>

                                                <input type="hidden" name="product[<?php echo $key; ?>][name]" value="<?php echo $product['name']; ?>">
                                                <input type="hidden" name="product[<?php echo $key; ?>][combo_id]" value="<?php echo $product['combo_product_id']; ?>">
                                                <input type="hidden" name="product[<?php echo $key; ?>][seller_invoice_id]" value="<?php echo $product['seller_invoice_id']; ?>">
                                                <input type="hidden" name="product[<?php echo $key; ?>][model]" value="<?php echo $product['model']; ?>">
                                                <input type="hidden" name="product[<?php echo $key; ?>][img_link]" value="<?php echo $hrefs[$product['product_id']]; ?>">
                                                <input type="hidden" name="product[<?php echo $key; ?>][image]" value="<?php echo $pid_to_imgs[$product['product_id']]; ?>">
                                                <input type="hidden" name="order_no" value="<?php echo $order_no; ?>">
                                                
                                                <?php if($is_returnable) { ?>
                                                <tr class="even gradeA">
                                                    <td>
                                                        <div class="col-sm-3 nopadding">
                                                        <a href="<?php echo $hrefs[$product['product_id']]; ?>">
                                                        <img style= "height:117px;" class="img-thumbnail" src="<?php echo $pid_to_imgs[$product['product_id']]; ?>"  title="<?php echo $product['name']; ?>" width="74">
                                                        </a>
                                                        </div>
                                                        <div class="col-sm-9  product_dec">
                                                        <a href="<?php echo $hrefs[$product['product_id']]; ?>"><?php echo $product['name']; ?></a><br>
                                                        <span style="font-size: 14px;"><?php echo $product['model']; ?></span><br><br>
                                                        <div class="product_dec_bottem">
                                                            <input type="file" style="visibility: hidden;"/>
                                                                <button class="return_btn_box hidden" type="button" id="cancel_return_<?php echo $i;?>" onclick="cancelReturn(<?php echo $i;?>)">Click to cancel return</button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $total_pieces ; ?></td>
                                                    <td><?php echo $product['price_per_piece_with_currency']; ?></td>
                                                    <td >
                                                        <div>
                                                        <select name="product[<?php echo $key; ?>][return_reason_id]" class="select_reason_set" id="return_reason_<?php echo $i ?>" onchange="toggleQuantity(<?php echo $i ?>)">
                                                            <option value="0">Select return/replacement</option>
                                                            <?php if($product['quality_issue'] && empty($product['non_returnable']) && empty($total_quantity)){ ?>
                                                            <optgroup label="Quality Issues" >
                                                            <?php foreach($quality_return as $val){ ?>
                                                            <option value="<?php echo $val; ?>"><?php echo $return_reasons[$val]; ?></option>
                                                            <?php } ?>
                                                            </optgroup>
                                                             <?php } ?>
                                                            <optgroup label="Replacement Issues">
                                                            <?php foreach($replacement_return as $val){ ?>
                                                            <option value="<?php echo $val; ?>"><?php echo $return_reasons[$val]; ?></option>
                                                            <?php } ?>
                                                            </optgroup>
                                                        </select>
                                                        <span id="upload_text_<?php echo $i ?>" class="hidden">Upload Defected Pieces</span>
                                                        </div>
                                                        <?php if(!empty($pervious_quantity)){
                                                            $tool_tip  = " You have already requested ";
                                                            $tool_tip .= $pervious_quantity;
                                                            $tool_tip .= " out of ";
                                                            $tool_tip .= $total_pieces;
                                                            $tool_tip .= " pieces in return. If you place a request for return for this product then quanity of pieces to return will overwrite.Ex. You have requested 3 out of 10 pieces If you request a return for 1 piece then pieces to retur would be 4";
                                                        }else{
                                                            $tool_tip = '';
                                                        } ?>
                                                        <input type="file" style="margin-top:10px;" class="defect_images hidden" id="defect_images_<?php echo $i ?>" multiple name="manufacturing[<?php echo $key; ?>][]" />
                                                    </td>
                                                    <td >
                                                        <input type="number" class="amount_reason_set" data-placement="top" value="0" name="product[<?php echo $key; ?>][quantity]" data-toggle="tooltip" data-placement="bottom" title="<?php echo $tool_tip;?>" min="1" id="return_quantity_<?php echo $i; ?>" max="<?php echo $total_pieces; ?>" readonly>
                                                    </td>
                                                    <td class="center">
                                                        <textarea class="textarea_reason_set" name="product[<?php echo $key; ?>][comment]" rows="3" cols="30" id="textarea_reason_set_<?php echo $i;?>"></textarea>
                                                    </td>
                                                </tr>
                                                <?php } else { ?>

                                                <!-- Non returnable product row-->
                                                    <tr class="even gradeA">
                                                        <td>
                                                            <div class="col-sm-3 nopadding">
                                                            <a href="<?php echo $hrefs[$product['product_id']]; ?>">
                                                            <img style= "height:117px;" class="img-thumbnail" src="<?php echo $pid_to_imgs[$product['product_id']]; ?>"  title="<?php echo $product['name']; ?>" width="74">
                                                            </a>
                                                            </div>
                                                            <div class="col-sm-9  product_dec">
                                                            <a href="<?php echo $hrefs[$product['product_id']]; ?>"><?php echo $product['name']; ?></a><br>
                                                            <span style="font-size: 14px;"><?php echo $product['model']; ?></span><br><br>
                                                            <div class="product_dec_bottem">
                                                                <input type="file" style="visibility: hidden;"/>
                                                                    <button class="return_btn_box hidden" type="button" id="cancel_return_<?php echo $i;?>" onclick="cancelReturn(<?php echo $i;?>)">Click to cancel return</button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?php echo $total_pieces ; ?></td>
                                                        <td><?php echo $product['price_per_piece_with_currency']; ?></td>
                                                        <td colspan="3" align="left" style="font-size: 20px; color: red">
                                                            (Non-Returnable Product)
                                                          </td>
                                                    </tr>
                                                <!-- Non returnable product row-->

                                                <?php } ?>

                                                <?php
                                                    $i++;
                                                } else{
                                                if(isset($master_return_id) && $master_return_id == $check_master_return_id){       
                                                ?>

                                                <?php if($is_returnable) { ?>
                                                    <tr class="even gradeA">
                                                        <td>
                                                            <div class="col-sm-3 nopadding">
                                                            <a href="<?php echo $hrefs[$product['product_id']]; ?>">
                                                            <img style= "height:117px;" class="img-thumbnail" src="<?php echo $pid_to_imgs[$product['product_id']]; ?>"  title="<?php echo $product['name']; ?>" width="74">
                                                            </a>
                                                            </div>
                                                            <div class="col-sm-9  product_dec">
                                                            <a href="<?php echo $hrefs[$product['product_id']]; ?>"><?php echo $product['name']; ?></a><br>
                                                            <span style="font-size: 14px;"><?php echo $product['model']; ?></span><br><br>
                                                            </div>
                                                        </td>
                                                        <td><?php echo $total_pieces ; ?></td>
                                                        <td><?php echo $product['price_per_piece_with_currency']; ?></td>
                                                        <td >
                                                            <div style='width: 277px;'><?php echo $return_reasons[$returns[$key]['return_reason_id']]; ?></div>
                                                        </td>
                                                        <td >
                                                            <div><?php echo $returns[$key]['quantity']; ?></div>
                                                        </td>
                                                        <td class="center">
                                                            <div><?php echo $returns[$key]['comment']; ?></div>
                                                        </td>
                                                    </tr>
                                                    <?php } else { ?>
                                                        <!-- Non returnable product row-->
                                                            <tr class="even gradeA">
                                                                <td>
                                                                    <div class="col-sm-3 nopadding">
                                                                    <a href="<?php echo $hrefs[$product['product_id']]; ?>">
                                                                    <img style= "height:117px;" class="img-thumbnail" src="<?php echo $pid_to_imgs[$product['product_id']]; ?>"  title="<?php echo $product['name']; ?>" width="74">
                                                                    </a>
                                                                    </div>
                                                                    <div class="col-sm-9  product_dec">
                                                                    <a href="<?php echo $hrefs[$product['product_id']]; ?>"><?php echo $product['name']; ?></a><br>
                                                                    <span style="font-size: 14px;"><?php echo $product['model']; ?></span><br><br>
                                                                    </div>
                                                                </td>
                                                                <td><?php echo $total_pieces ; ?></td>
                                                                <td><?php echo $product['price_per_piece_with_currency']; ?></td>
                                                                <td colspan="3" align="left" style="font-size: 20px; color: red">
                                                                    (Non-Returnable Product)
                                                                 </td>
                                                            </tr>
                                                        <!-- Non returnable product row-->

                                                    <?php } ?>

                                                <?php
                                                }
                                                }
                                                }
                                                } else{   ?>
                                                <tr class="even gradeA">
                                                    <td colspan="6" align="center"><?php echo $entry_no_return;?></td>
                                                </tr>
                                                <?php }  ?>
                                            </tbody>
                                         </table>
                                        </form>
                                        <?php if($all_returns !="true"){ ?>
                                        <!-- /.table-responsive -->
                                        <div class="col-sm-12">
                                        <?php if(!isset($master_return_id) && $all_returns !="true"){ ?>
                                        <div class="product_ret_bottem">
                                            <button class="return_btn_box pull-right" data-toggle="collapse" id='pickup_button' data-target="#pick_up_address"><?php echo $entry_chosse_pickup;?></button>
                                                <div class="clearfix"></div>
                                        </div>
                                        <?php } ?>    
                                        <div class="clearfix"></div>
                                            <div id="pick_up_address" class="collapse return_table_box">
                                                <h4 class="pick_up_address_title"><?php echo $text_pickup_address;?></h4>
                                                <?php if(!isset($master_return_id) && $all_returns !="true"){ ?>
                                                <div class="pick_up_type">
                                                    <div class="col-sm-2 nopadding black"><?php echo $entry_select_pick_up;?></div>
                                                    <div class="col-sm-2 nopadding">
                                                        <label class="js-open">
                                                        <div class="pick_up_radio_btn control--radio">
                                                        <?php if(isset($shipping_method) && $shipping_method == "self_courier"){ ?>
                                                            <input type="hidden" name="pickup_method" form="form-return" value="<?php echo $shipping_method; ?>">
                                                            <input id="rating_filter_8" name="pickup_method" <?php echo $check_box_disabled; ?> checked="checked" class='pickup_method' value="self_courier" form="form-return" type="radio">
                                                        <?php }else{ ?>
                                                            <input id="rating_filter_8" name="pickup_method" <?php echo $check_box_disabled; ?> class='pickup_method' value="self_courier" form="form-return" type="radio">
                                                        <?php }  ?>
                                                        <div class="control__indicator"></div>
                                                        </div>
                                                        <span><?php echo $entry_self_courier;?></span>
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-2 nopadding">
                                                        <label class="js-open">
                                                            <div class="pick_up_radio_btn control--radio">
                                                            <?php if(isset($shipping_method) && $shipping_method == "wsb_pickup"){ ?>
                                                            <input type="hidden" name="pickup_method" form="form-return" value="<?php echo $shipping_method; ?>">
                                                            <input id="rating_filter_8" name="pickup_method" <?php echo $check_box_disabled; ?> checked="checked" class='pickup_method' value="wsb_pickup" form="form-return" type="radio">
                                                            <?php }else{ ?>
                                                            <input id="rating_filter_8" name="pickup_method" <?php echo $check_box_disabled; ?>  class='pickup_method' value="wsb_pickup" form="form-return" type="radio">
                                                            <?php }  ?>
                                                            <div class="control__indicator"></div>
                                                            </div>
                                                            <span><?php echo $entry_wsb_pickup?></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                 <?php }else{  ?>
                                                <div class="col-sm-3 nopadding black">
                                                    <?php 
                                                    if($shipping_method = "wsb_pickup"){
                                                        $pickup = "Wholesale Box";
                                                    }else{
                                                        $pickup = "Self Courier";
                                                    }
                                                    
                                                    echo 'Pickup Type : '.$pickup; ?>
                                                 </div>  
                                                 <?php } ?>             
                                                <div class="clearfix"></div>
                                                    <?php if(!isset($master_return_id) && $all_returns !="true"){ ?>
                                                    <div class="col-sm-12 nopadding">
                                                        <div class="col-sm-3 nopadding black"><?php echo $entry_send_shipping_address;?></div>
                                                        <div class="clearfix"></div>
                                                        <p>Wholesalebox Jaipur Office:<br>B-1, Crystal Mall, Banipark,<br>Jaipur - 302016<br>Email: info@wholesalebox.in<br>Telephone (Jaipur)<br>(+91) 9587896265</p>
                                                    </div>
                                                    <?php } ?>          
                                                    <div class="clearfix"></div>
                                            </div>
                                        </div>
                                        <?php if(!isset($master_return_id) && $all_returns !="true"){ ?>
                                            <div class="col-sm-12">
                                                <div class="product_ret_bottem">
                                                    <button class="return_btn_box pull-right" id="return_review_button" onclick="return_review('show');" disabled>Go to Return Review</button>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div id="return_review" show="show" class="collapse return_table_box" style="display: none;">
                                                    <h4 class="pick_up_address_title ">Pick Up Address</h4>
                                                    <table width="100%" class="table-striped table-hover">
                                                        <thead>
                                                        <tr class="cart_table_title pickup_address_table_title">
                                                            <td><?php echo $entry_product;?></td>
                                                            <td><?php echo $entry_return_pieces;?></td>
                                                            <td><?php echo $entry_return_reason_review;?></td>
                                                        </tr>
                                                        </thead>
                                                        <tbody id='return_review_table'>
                                                        </tbody>
                                                    </table>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-12">
                                        <?php if(!isset($master_return_id) && $all_returns !="true"){ ?>
                                            <div class="product_ret_bottem">
                                                <button class="return_btn_box pull-right" data-toggle="collapse" data-target="#add_banking_details">Add Banking details</button>
                                                <div class="clearfix"></div>
                                            </div>
                                        <?php } ?>    
                                       <div class="clearfix"></div>
                                            <div id="add_banking_details" class="collapse return_table_box">
                                                <h4 class="pick_up_address_title"><?php echo $entry_refund_detail;?></h4>
                                                <div class="clearfix"></div>
                                                <div class="col-sm-4 account_bank_detail_left">
                                                    <div class="refund_details">
                                                        <div class="clearfix"></div>
                                                        <?php if(!isset($master_return_id) && $all_returns !="true"){ ?>
                                                        <div class="col-sm-8 left_no_padding"><p><?php echo $entry_refund_pieces;?><p></div>
                                                        <span class="col-sm-4 sum_refund_quantity"></span>
                                                        <?php }else{  ?>
                                                          <div class="col-sm-8 left_no_padding"><p><?php echo $entry_refund_pieces;?></p></div>
                                                          <span class="col-sm-4 sum_refund_quantity"><?php echo $return_total_quantity;?></span>
                                                        <?php }  ?>
                                                        <div class="clearfix"></div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <div class="left_Shipping_details">
                                                            <?php if(!isset($master_return_id) && $all_returns !="true"){ ?>
                                                            <div class="col-sm-8 left_no_padding"><?php echo $entry_shipping_adjustment;?></div>
                                                            <span class="col-sm-4 sum_shipping_adjustment"></span>
                                                            <?php }else{  ?>
                                                            <div class="col-sm-8 left_no_padding"><?php echo $entry_shipping_adjustment;?></div>
                                                            <span class="col-sm-4 sum_shipping_adjustment"><?php echo $shipping_adjustment_ammount; ?></span>
                                                            <?php }  ?>
                                                            <div class="clearfix"></div>
                                                            <div class="tentative">
                                                                <?php if(!isset($master_return_id) && $all_returns !="true"){ ?>
                                                                    <div class="col-sm-8 left_no_padding"><?php echo $entry_tentative_refund?></div>
                                                                    <span class="col-sm-4 sum_ten_refund_amt"></span>
                                                                    <?php }else{  ?>
                                                                        <div class="col-sm-8 left_no_padding"><?php echo $entry_tentative_refund;?></div>
                                                                        <span class="col-sm-4 sum_ten_refund_amt"><?php echo $tentative_refund_amount;?></span>
                                                                    <?php }  ?>
                                                                    <div class="clearfix"></div>
                                                            </div>
                                                        </div>
                                                        <?php if(!empty($credit_note)){ ?>
                                                        <div>
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <td> Credit Note No. </td>
                                                                        <td><i class="fa fa-download" aria-hidden="true"></i></td>
                                                                    </tr>   
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach($credit_note as $value){  ?>
                                                                        <tr>
                                                                            <td><?php echo $value['credit_note_no'] ?></td>
                                                                            <td><a href="<?php echo $value['credit_note_link'] ?>"><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                                                        </tr>   
                                                                     <?php } ?>   
                                                                </tbody>     
                                                             </table>   
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="col-sm-4 account_bank_detail_center">
                                                        <h4><?php echo $entry_bank_details;?></h4>
                                                        <?php if(!isset($master_return_id) && $all_returns !="true"){ 
                                                             $bank_detail_disabled = "";
                                                        }else{
                                                            $bank_detail_disabled = "disabled";
                                                        } ?>
                                                        <input type="hidden" name="ifsc_error" id="ifsc_error" value='' form="form-return">
                                                        <div class="form-group">
                                                            <label><h4>Account Holder Name</h4></label>
                                                            <input type="text" <?php echo $bank_detail_disabled; ?> form="form-return" name="ac_holder_name" class="form-control bank_detail_input" id="ac_holder_name_id" value="<?php echo $bank_ac_holder_name; ?>" /> 
                                                        </div>
                                                        <div class="form-group">
                                                            <label><h4>Account Number</h4></label>
                                                            <input type="text" <?php echo $bank_detail_disabled; ?> form="form-return" name="ac_number" class="form-control bank_detail_input" value="<?php echo $bank_ac_number; ?>" id="ac_number_id" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label><h4>Ifsc Code</h4></label>
                                                            <input type="text" <?php echo $bank_detail_disabled; ?> form="form-return" name="ifsc_code" class="form-control sample_ifsc_code bank_detail_input" value="<?php echo $ifsc_code; ?>" id="ifsc_code_id" />
                                                        </div>
                                                        <div class="form-group no_margin_bottom" id="otp_area" style="display: none;">
                                                            <label><h4>OTP</h4></label>
                                                            <input type="text" form="form-return" name="otp" class="form-control" id="otp" />
                                                             <a href="javascript:;" onclick="send_bank_otp(1)" id="resend_otp">Did'nt get OTP?</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 bank_details_block"></div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="col-sm-12">
                                                    <div class="return_submit_box">
                                                        <?php if(!isset($master_return_id) && $all_returns !="true"){ ?>
                                                            <div class="product_dec_bottem">
                                                                <button type="button" class="return_submit_btn pull-right"onclick="addReturn()"><?php echo $entry_submit_request;?></button>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                    </div>
                                                </div>
                                                <?php } } ?>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
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
<script type="text/javascript">
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

$(document).ready(function() {
   var master_return_id = $("#view_return").val();
   if(master_return_id.length > 0){
       $(".return_table_box").addClass('in');
   }
   var ifsc_code = $("input[name=ifsc_code]").val();
   getIfscCodeDetails("input[name=ifsc_code]",ifsc_code);
});


function cancelReturn(id){
    $("#return_reason_"+id).val(0);
    $('#return_quantity_'+id).val(0);
    $('#textarea_reason_set_'+id).val('');
    $('#defect_images_'+id).addClass('hidden');
}

function toggleQuantity(id){
    var return_reason = $("#return_reason_"+id).val();
    if(return_reason != 1){
        var total_return_quantity = $("#return_quantity_"+id).attr('max');
        $("#defect_images_"+id).addClass('hidden');
        $("#return_quantity_"+id).val(total_return_quantity);
        $(".return_btn_box").removeAttr('disabled');
        $("#return_quantity_"+id).attr('readonly','readonly');
    }else{
        $(".return_btn_box").removeAttr('disabled');
        $("#defect_images_"+id).removeClass('hidden');
        $("#return_quantity_"+id).removeAttr('readonly');
        $("#return_quantity_"+id).val(1);

    }
    if(return_reason == 0){
        $("#return_quantity_"+id).val(0);
        $("#cancel_return_"+id).addClass('hidden');
        $("#defect_images_"+id).addClass('hidden');
    }else{
        $("#cancel_return_"+id).removeClass('hidden');
    }
}

function return_review(type){
    if(type == 'show'){
        var form_data = $("#form-return").serialize();
        var check_pickup = $('.pickup_method').is(':checked');
        if(check_pickup){
             $("#return_review_table").html('');
             $("#return_review_button").text('loading..');
             $("#return_review_button").attr('disabled','disabled');
                $.ajax({
                type:'post',
                dataType:'json',
                data: form_data,
                url:'<?php echo $return_review_url; ?>',
                complete:function(){
                     $("#return_review_button").text('Go to Return Review');
                     $("#return_review_button").removeAttr('disabled','disabled');
                },
                success:function(response){
                    if(response.success){
                            var html  = '';
                            var form  = '';
                            var keys = Object.keys(response['data']);
                            $(keys).each(function(i,e){
                            html += '<tr class="odd gradeC">';
                            html +=      '<td>';
                            html +=          '<div class="col-sm-3 nopadding">';
                            html +=              '<a href='+response["data"][e]["img_link"]+'>';
                            html +=                  '<img class="img-thumbnail" style="height:117px; title="'+response['data'][e]['name'] + '" src="'+response['data'][e]['image'] + '" width="74">';
                            html +=              '</a>';
                            html +=           '</div>';
                            html +=            '<div class="col-sm-9 nopadding product_dec">';
                            html +=                  '<a href='+response["data"][e]["img_link"]+'>';
                            html +=                      response["data"][e]["name"]
                            html +=                  '</a>';
                            html +=                   '<br>';
                            html +=                    '<span style="font-size: 14px;">' +response["data"][e]["model"] +'</span>';
                            html +=                   '<br>';
                            html +=                   '<br>';
                            html +=                   '<div class="product_dec_bottem">';
                            html +=                        'Refund Value:' + response["data"][e]["refund_amount"];
                            html +=                    '</div>';
                            html +=            '</div>';
                            html +=       '</td>';
                            html +=       '<td>'+ response["data"][e]["quantity"] +'</td>';
                            html +=        '<td>'+ response["data"][e]["return_reason_name"] +'</td>';
                            html +=  '</tr>';
                            });
                        $("#return_review_table").append(html);
                        $('#return_review').show('slow');
                        $('#return_review').addClass('in');
                        $('#return_review_button').attr("onclick","return_review('hide')");
                        $(".sum_refund_quantity").text(response['total_refund_quanity']);
                        $(".sum_shipping_adjustment").text(response['shipping_adjustment_ammount']);
                        $(".sum_ten_refund_amt").text(response['tentative_refund_amount']);
                        $(".return_submit_btn").removeClass('hidden');
                    }else{
                        $html ='<div class="alert alert-danger" style="margin-top: 20px;">Return Request Failed Please Enter Valid Return Values</div>';
                        $("body").scrollTop( 0 );
                        $('.error').find('.alert-danger').remove();
                        $('.error').append($html);
                    }
                }
            });
        }else{
            alert("Please Choose Pickup type");
        }
    }else{
        $('#return_review').hide('slow');
        $('#return_review_button').attr('onclick',"return_review('show')");
    }
}

$('input[name="ifsc_code"]').on('input',function(){
    $(this).removeClass('error_border');
    $('.error-ms-bank').remove();
    $('.bank_details_block').html('');
	var getIFSCcode = $(this).val().toUpperCase().trim();
	//var filter = /^([a-zA-Z0-9]){1}$/;
	var filter = /^[A-Z0-9]*$/;
	if (filter.test(getIFSCcode)){
		getIfscCodeDetails(this, getIFSCcode);
	}else{
		$(this).addClass('error_border');
		$(this).parent().append('<span class="error_border error-ms-bank">Invalid IFSC code</span>');
	}
});

function getIfscCodeDetails(obj, ifsc_code){
    var ifsc_code = ifsc_code.trim();
	if(ifsc_code.length == 11){
		$.ajax({
			 url : 'index.php?route=seller/account-profile/bankDetails&ifsc_code='+ifsc_code ,
             async:false,
			success:function(json){
                var json = JSON.parse(json);
				if(typeof(json) == 'object'){
                    $('.error_text').remove();
					html = '';
                    html += '<h4>' +json['BANK'] +'</h4>';
					html +=	'<span>'+json['BRANCH'] +'</span>';
					html +=	'<p>'+json['ADDRESS']+'</tp><br>';
					html +=	'<p><b>Place: </b></p>';
					html +=	'<p>'+json['DISTRICT']+'<br> '+json['CITY']+', '+json['STATE']+'</p>';
					$('.bank_details_block').html(html);
                    $("#ifsc_error").val(true);
				}else{
					$('.error_text').remove();
                    $("#ifsc_error").val(false);
					$(obj).parent().find('.sample_ifsc_code').after('<span class="error_text">'+json+'</span>');
					$('.bank_details_block').html('');
				}
			}

		});
	}else{
        $(obj).addClass('error_border');
		//$('#ms-bank-submit-button').attr('readonly',true);
	}
}

function addReturn(){
  var check = 'true';
  var check_return = "false" ;
  var image_extension = 'true';
  var defect_images_check = 'true';
  var file_name = '';
  var no_image_upload = 'true';
  var check_upload_images_quantity = 0; 
  var image_quantity = 'true'; 
  $('.gradeA').each(function(i,e){
      var reason_return   = $(this).find('.select_reason_set').val();
      if(reason_return == 1){              
        var defect_images = $(this).find('.defect_images');  
        var items = defect_images[0].files;   
        var lg = defect_images[0].files.length;
        if (lg > 0) {
           for (var i = 0; i < lg; i++) {
               check_upload_images_quantity++;
               file_name = items[i].name; // get file size
               var extension = file_name.substring(file_name.lastIndexOf('.') + 1).toLowerCase();
               if(defect_images_check != 'false'){
                    if(extension == 'jpeg' || extension == 'jpg' || extension == 'png'){
                    }else{
                        image_extension = 'false';
                        defect_images_check = 'false';
                    }     
                }
           }
        }else{
            no_image_upload = 'false';
        }     
      }
      var piece_to_return = $(this).find('.amount_reason_set').val();
      if(piece_to_return > check_upload_images_quantity && check_upload_images_quantity !=0){
            image_quantity = 'false';
      }
      check_upload_images_quantity = 0;
      if(reason_return != 0 && piece_to_return != 0){
          check_return = 'true';
      }
  });
  
  if(check_return != 'true'){
      alert("Add at least one product for return");
      return false;
  }
  if(no_image_upload == 'false'){
      alert("Please upload manufacturing defected images");
      return false;
  }
  if(image_extension == 'false'){
      alert('Please upload only jpg, jpeg or png type images');
      return false;
  }
  if(image_quantity == 'false'){
      alert('Image quantity does not match with return product quantity');
      return false;
  }

  $('.bank_detail_input').each(function()
  {
      if($(this).val().trim().length == 0){
          $(this).addClass('error_border');
          check = 'false';
      }
  });

  if(check == 'false'){
      alert('Please add bank details');
      return false;
  }else{
      if($("#ifsc_error").val() == "false"){
          alert("Please insert correct ifsc code");
          return false;
      }


var bank_ac_holder_name = '<?php echo $bank_ac_holder_name; ?>';
var bank_ac_number = '<?php echo $bank_ac_number; ?>';
var ifsc_code = '<?php echo $ifsc_code; ?>';
var otp_override = 0;

if($("#ac_holder_name_id").val() != bank_ac_holder_name || $("#ac_number_id").val() != bank_ac_number || $("#ifsc_code_id").val() != ifsc_code)
 { send_bank_otp(); }
else { otp_override = 1; }

$.ajax({url : '<?php echo $bank_detail_validate_url; ?>' ,
             type: 'post',
             dataType: 'json',
             data: {'bank_ac_number':$('#ac_number_id').val(),'bank_ac_holder_name':$('#ac_holder_name_id').val(),'ifsc_code':$('#ifsc_code_id').val(),'otp':$('#otp').val(),'otp_override':otp_override},
            success:function(json){
                if(json.statusCode=='200'){
                    var check_pickup = $('.pickup_method').is(':checked');
                        if(check_pickup){
                            $('#form-return').submit();
                        }else{
                            alert('Please select pickup method');
                            return false;
                        }

                }else{
                            alert(json.message);
                            return false;
                }
            }

        });


     return false;
  }
}

 function send_bank_otp(resend=0)
 {      
        $("#resend_otp").html('<i class="fa fa-circle-o-notch"></i>');
        var customer_id = getCookie('customer_id');
        var customer_access_token = getCookie('customer_access_token');
        var ajax_url = './api/bank/update_bank_otp&customer_id='+customer_id+'&customer_access_token='+customer_access_token;
        $.ajax({
            url: ajax_url,
            dataType: 'json',
            success: function( data )
            {
              $("#resend_otp").html("Didn't get OTP?");  
              if(data.statusCode == 200)
              {
                if(resend) { alert("OTP sent successfully."); }
                $("#otp_area").show();
              }
              else
              {
                 alert(data.message);
              }
            }
          });  
       return true;   
}
</script>
</body>
</html>