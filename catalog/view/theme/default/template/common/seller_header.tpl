<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" >
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>
        Sell your product on Wholesalebox.in | Grow your business with fast-growing Indian wholesale Clothing e-commerce
    </title>
    <base href="<?php echo $base; ?>" />

	<script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js"></script>
    <script  src="catalog/view/javascript/jquery/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
    <link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />

     <link href="catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen" />

     <script  src="catalog/view/javascript/jquery/datetimepicker/moment.min.js" type="text/javascript"></script>

     <script  src="catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>

    <script  src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script  src="catalog/view/theme/default/javascript/fancybox/jquery.fancybox.pack.js" type="text/javascript"></script>
    <link href="catalog/view/theme/default/stylesheet/jquery.fancybox.css" rel="stylesheet" />
    <link href="catalog/view/theme/default/stylesheet/jquery-ui.css" rel="stylesheet" />
    <link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="catalog/view/theme/default/stylesheet/styleseller.css" rel="stylesheet" />
    <link href="catalog/view/theme/default/stylesheet/stylesheet.css" rel="stylesheet" />
    <link href="catalog/view/javascript/multimerch/summernote/summernote.css" rel="stylesheet" />
    <link href="catalog/view/theme/default/stylesheet/stylesheet_new.css?v=3" rel=
    "stylesheet" />
    <link href="catalog/view/theme/default/stylesheet/colors/<?php echo $color_template;?>/<?php echo $color_template;?>.css" rel="stylesheet" />

    <?php foreach ($scripts as $script) { ?>
    <script  src="<?php echo $script; ?>" type="text/javascript"></script>
    <?php } ?>
    <script  src="catalog/view/javascript/common.js" type="text/javascript"></script>
    <script  src="catalog/view/theme/default/javascript/theme_common.js" type="text/javascript"></script>
    <script  src="catalog/view/javascript/seller.js" type="text/javascript"></script>
</head>
<body class="header_back">
<header class="color_white height_header">

<?php if($seller_status == 1 && empty($gst_provisional_id)){ ?>
<div class="gst_block_show">
    <p class="gst_block_show_close pull-right"><i class="fa fa-times"></i></p>
        <p><strong>Please fill your GST Details!</strong></p>
        <a href="<?php echo $gst_popup_link_click_here ;?>" class="btn btn-warning btn-xs pull-right gst_click_here">Click Here >> </a>
</div>
<?php } ?>
<div class="container">
    <div class="row">
        <?php if(isset($error_login) == 'error=error_in_login'){ ?>
        <div class="col-sm-4 error_email">Wrong Email id OR Password.<li class="fa fa-times-circle cursor"></li></div>
        <?php } ?>
        <div class="seller_head col-sm-12">
        <div class="col-sm-4 logo">
           <a href="<?php echo HTTP_SERVER; ?>"> <img src="<?php echo $mobile_logo; ?>" width="296px" height="60px" /></a>
            <div class="seller_hub"><?php echo $text_seller_hub; ?></div>
        </div>
        <?php if ($logged) { ?>
        <div class="col-sm-4 gst_title gst_title_text_blink">
            Transfer Price will be inclusive of GST <br>
            <span> ( For any help: 9116134791 | 9649558363 )</span>
        </div>
        <div class="col-sm-4 float_right login_seller">
            <div class="in_style_reg logout_btn">
                <a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a>
            </div>
            <?php if( $seller_status ==1 ) { ?>
                <div class="new_sm">
                    <!-- <a class="new_sm" href="<?php echo $this->url->link('seller_panel/account-order', '', 'SSL'); ?>">
                        <span class="fa fa-dashboard"></span><?php echo $dashboard; ?>
                    </a> -->
                    <a class="new_sm" href="./seller_panel/#/orders/getPickpupOrderRequested">
                        <span class="fa fa-dashboard"></span><?php echo $dashboard; ?>
                    </a>
                </div>
            <?php } ?>
         </div>
       <?php }else { ?>
        <div class="col-sm-8 float_right login_seller">
               <?php if ($error_warning) { ?>
                 <div class="clearfix"></div>
                <div class="alert alert-danger" style="position: absolute; top: -30px; right: 98px;">
                <i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
                <?php } ?>
            <form class="form_login float_right" id="log_form" action="" method="post" enctype="multipart/form-data">
                <label class="lable_seller"><?php echo $text_seller_login; ?></label><br />
                <input type="text" name="email" class="in_style" placeholder="<?php echo $text_email; ?>" required />
                <!--<input type="hidden" name="seller_login">-->
                <input type="hidden" name="form_button" value="log-button">
                <input type="password" name="password" class="in_style" placeholder="<?php echo $text_password; ?>" required />
                <input type="submit" name="submit" value="<?php echo $text_submit; ?>" class="submit_btn log_bt">
               <div class="clearfix"></div>
                <div class="otp_agin pull-left">
                  <a id="login_link_header" onclick="$('#login_verify_popup').modal('show');"><?php echo $text_forgot_password; ?></a>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>
    </div>
    <?php if ($logged) { ?>
        <!--<div class="col-sm-7">
        <ul class="menu_bar list-unstyled">
            <li><span class="fa fa-user new_s"></span><a href="<?php echo $this->url->link('seller/account-profile', '', 'SSL'); ?>">Settings</a></li>
            <li class="ben_li"><span class="fa fa-pencil-square-o new_s"></span><a href="<?php echo $this->url->link('account/password', '', 'SSL'); ?>">Change Password</a></li>
            <li class="ben_li"><span class="fa fa-search-plus new_s"></span><a href="<?php echo $this->url->link('seller_panel/account-order', '', 'SSL'); ?>">View Orders</a></li>
            <li class="ben_li"><span class="fa fa-pencil-square-o new_s"></span><a href="<?php echo $manage; ?>">Manage Inventory</a></li>
             <li class="ben_li"><span class="fa fa-pencil-square-o new_s"></span><a href="<?php echo $add_products; ?>">Add Products</a></li> -->

            <div class="collapse navbar-collapse navbar-ex1-collapse nopadding col-sm-7">
                <ul class="nav navbar-nav">
                   <!--  <li class="dropdown"><a href="<?php echo $this->url->link('seller_panel/profile', '', 'SSL'); ?>"><span class="fa fa-user new_s"></span>Profile</a> -->
                    <li class="dropdown"><a href="<?php echo HTTPS_SERVER ?>seller_panel/#/profile"><span class="fa fa-user new_s"></span>Profile</a>
                    <?php /*if(!empty($strs)){ ?>
                        <ul class="list-unstyled dropdown-menu" role="menu"  >

                            <li>
                                <a href="<?php echo $this->url->link('seller/account-profile', '', 'SSL'); ?>">Personal Settings</a>
                            </li>
                            <li>
                                <a href="<?php echo $this->url->link('seller/website-settings', '', 'SSL'); ?>">Websites Settings</a>
                            </li>
                            <li>
                                <a href="<?php echo $this->url->link('design/banner', '', 'SSL'); ?>">Banners</a>
                            </li>

                            <li>
                                <a href="<?php echo $this->url->link('seller/menu', '', 'SSL'); ?>">Menu</a>
                            </li>

                        </ul>
                    <?php } */?>
                    </li>

                   <!--  <li class="dropdown"><a href="<?php echo $this->url->link('account/password', '', 'SSL'); ?>"><span class="fa fa-pencil-square-o new_s"></span>Change Password</a></li> -->

                    <li class="dropdown"><a href="<?php echo HTTPS_SERVER ?>seller_panel/#/profile"><span class="fa fa-pencil-square-o new_s"></span>Change Password</a></li>

                    <!-- <li class="dropdown"><a href="<?php echo $this->url->link('seller_panel/account-order', '', 'SSL'); ?>"><span class="fa fa-search-plus new_s"></span>Sales</a> -->
                    <li class="dropdown"><a href="./seller_panel/#/orders/getPickpupOrderRequested"><span class="fa fa-search-plus new_s"></span>Sales</a>
                    <?php if(!empty($strs)){ ?>
                        <ul class="list-unstyled dropdown-menu" role="menu"  >

                            <li>
                                <a href="<?php echo HTTPS_SERVER ?>seller_panel/#/orders/getPickpupOrderRequested">Orders</a>
                            </li>
                            <li>
                                <a href="<?php echo $this->url->link('seller/account-customer', '', 'SSL'); ?>">Customers</a>
                            </li>

                        </ul>
                    <?php } ?>
                    </li>
                    <?php if( $seller_status ==1 ) { ?>
                        <li class="dropdown"><a href="<?php echo $manage; ?>"><span class="fa fa-pencil-square-o new_s"></span>Inventory</a>
                            <ul class="list-unstyled dropdown-menu" role="menu">
                                <li>
                                    <a href="<?php echo $import;?>">Bulk Inventory</a>
                                  
                                </li>
                                <li>
                                    <a href="<?php echo $manage; ?>">Manage Inventory</a>
                                </li>
                                 <li>
                                    <a href="<?php echo $archive; ?>">Archived Inventory</a>
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
                    <!--<li class="dropdown"><a href="<?php echo $import_inventory; ?>"><span class="fa fa-pencil-square-o new_s"></span>Import Inventory</a>
                        <ul class="list-unstyled dropdown-menu" role="menu">
                            <li>
                                <a href="<?php echo $import_inventory; ?>">Import Inventory </a>
                            </li>
                        </ul>
                    </li>-->
                </ul>
            </div>
    </div>
    <?php }else { ?>
    <div class="col-sm-4">
    <ul class="menu_bar list-unstyled">
        <li><a href="<?php echo HTTP_SERVER; ?>"> Wholesalebox.in</a></li>
        <li class="ben_li"><a href="#" id="scrollto_ben"> Benefits</a></li>
        <li class="ben_li"><a href="#" id="scrollto_htosell"> How to sell</a></li>
    </ul>
    </div>
    <?php } ?>
</div>


   <!-- login(Verify) popup -->
  <div class="modal fade add_new_address" id="login_verify_popup" role="dialog">
        <div class="modal-dialog login_register_popup">
          <div class="modal-content">
            <div class="modal-header address_popup_head">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title" id="send_otp_title">Please enter your Email address</h4>
            </div>
            <div class="modal-body">
            <div class="success_msg login_mobile_success" style="display:none;"></div>
             <div class="danger_msg login_mobile_error" style="display:none;"></div>
             <div class="otp_model">
            <?php echo $send_email_otp_form['form_start']; ?>
               <div class="mobile_details_panel" id="edit_reg_telephone" style="display:none;">
                 <div class="login_mobile_nmr"></div> &nbsp;
                   <a href="javascript:;" onclick="edit_register_no();"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
               </div>

                <div class="mobile_details_panel" id="save_reg_telephone">
                    <?php echo $send_email_otp_form['reg_telephone']; ?>
                    <?php echo $send_email_otp_form['submit']; ?>
                     <div class="clearfix"></div>
                </div>
            <?php echo $send_email_otp_form['form_end']; ?>

            <?php echo $verify_otp_form['form_start']; ?>
                <div id="collapseverify" class="panel-collapse collapse">
                  <?php echo $verify_otp_form['otp']; ?>
                  <?php echo $verify_otp_form['submit']; ?>
                <div class="clearfix"></div>
                <div class="otp_agin"><a href="javascript:;" class="send_otp_agin">Didn't get OTP?</a></div>
                 </div>
            <?php echo $verify_otp_form['reg_telephone']; ?>
            <?php echo $verify_otp_form['form_end']; ?>
           </div>
            </div>
          </div>
        </div>
   </div>
     <!-- login(Verify) popup (End) -->


</header>

<?php if(empty($strs)){ /*
    if($seller_image != '' && $seller_approval){ ?>
<div class="row-fluid">
    <div class="col-sm-12">
    <a href="tel:7505003535">
        <img class="img-responsive seller-image" src="<?php echo $seller_image ?>" alt="make your own website">
    </a>
    </div>
</div>
<?php } */ } ?>

<script>
    $(document).ready(function(){
        $('#log_form').validate({
            rules: {
                email: { required: true, email: true },
                password: "required"
            },
            messages: {
                email: "Please enter your email",
                password: "Please enter password"
            },
            errorPlacement: function (error, element) {
                element.attr("placeholder", error.text());
            }
        });
    });

</script>

<script>
    $(document).ready(function(){
        $("#scrollto_ben").click(function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $("#main_benefit").offset().top
            }, 1000);
        });

        $("#scrollto_htosell").click(function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $("#htosell").offset().top
            }, 1000);
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('.cursor').click(function(){
            $('.error_email').hide();
        });
});


var ajax = null;
 $("#otp_form").submit(function(e) {
    $("#otp_form input[type=submit]").val('loading...').prop("disabled", true);
    e.preventDefault();
    var actionurl  = '<?php echo $send_otp_url ?>';
    var form_data  = $("#otp_form").serialize();
     ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                xhrFields: { withCredentials: true },
                crossDomain: true,
                beforeSend : function(xhr)
                {
                  xhr.setRequestHeader("Cookie", "session=xxxyyyzzz");
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(data) {
                    $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
                    if(data['error'])
                    {
                     $('.login_mobile_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                     $('.login_mobile_error').show();
                     $('.login_mobile_success').hide();
                     $("#collapseverify").css('visibility','hidden').hide();
                    }
                    else
                    {
                        $(".login_mobile_nmr").html(data['reg_telephone']);
                        $("#verify_otp_form input[name=reg_telephone]").val(data['reg_telephone']);
                        $('.login_mobile_success').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                        $('.login_mobile_success').show();
                        $("#save_reg_telephone, #send_otp_title").hide();
                        $("#edit_reg_telephone").show();
                        $("#collapseverify").css('visibility','visible').show();
                    }
                },
               complete: function(data){
                var ajax = null;
               }
         });
            e.stopImmediatePropagation();
            return false;
       });


$("#verify_otp_form").submit(function(e) {
    e.preventDefault();
    $("#verify_otp_form input[type=submit]").val('loading...').prop("disabled", true);
    var actionurl = '<?php echo $verify_otp_from_url ?>';
    var form_data = $("#verify_otp_form").serialize();
    ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                xhrFields: { withCredentials: true },
                crossDomain: true,
                beforeSend : function(xhr)
                {
                  xhr.setRequestHeader("Cookie", "session=xxxyyyzzz");
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(data)
                {
                    $("#verify_otp_form input[type=submit]").val('verify').prop("disabled", false);
                    if(data['error'])
                    {
                     $('.login_mobile_error').html('<i class="fa fa-check-circle"></i> '+data['msg']);
                     $('.login_mobile_error').show();
                     $('.login_mobile_success').hide();
                    }
                    else
                    {
                      window.location.assign(data['url']);
                    }
                },
               complete: function(data){
                var ajax = null;
               }
         });
          e.stopImmediatePropagation();
          return false;
       });



$(document).delegate('.send_otp_agin', 'click', function(e){
   $('.login_mobile_success, .login_mobile_error').hide();
   $("#verify_otp_form input[name=otp]").val('');
   $("#verify_otp_form input[name=otp]").prev('label').css('margin-top', '20px'); 
   e.preventDefault();
   $('#otp_form').submit();
});

$('input[name=otp]').keyup(function(e){
   if (/\D/g.test(this.value))
   {
       var node = $(this);
       node.val(node.val().replace(/[^0-9]/g,'') );
    }
});  


$('#reg_telephone').keyup(function(e)
{
 check_reg_telephone();
});
       

function check_reg_telephone(){
    var reg_email = $('#reg_telephone').val();
    var email_pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
            
    if (email_pattern.test(reg_email))
    {
        $("#otp_form input[type=submit]").val('continue').prop("disabled", false);
    }                   
    else
    {
       $("#otp_form input[type=submit]").val('continue').prop("disabled", true);
    }
} 

function edit_register_no(){
    $("#edit_reg_telephone").hide();
    $("#save_reg_telephone, #send_otp_title").show();
    $('.login_mobile_success, .login_mobile_error').hide(500);
    $("#verify_otp_form :input[name=otp]").val('');
    $("#verify_otp_form :input[name=otp]").prev('label').css('margin-top', '20px');
    $("#collapseverify").hide();
}

$(document).ready(function(){
    $('.gst_block_show_close').click(function(){
        $('.gst_block_show').hide();
    });
});
</script>
