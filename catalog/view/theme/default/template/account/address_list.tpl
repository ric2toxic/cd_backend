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
<section id="header_box"></section>


<div class="width_fix account_page_bg">
<div class="clearfix"></div>
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
  <?php } ?>
  <?php if ($error_warning) { ?>
  <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <?php echo $column_right; ?>
    <div id="content" class="<?php echo $class; ?> box_shadow_none">
      <ul class="breadcrumb_new">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
      <?php } ?>
      </ul>
    <?php echo $content_top; ?>
      <h1 class="account_title"><?php echo $text_address_book; ?></h1>
      <?php if ($addresses) { ?>
<!--       <table class="table table-bordered table-hover">
        <?php foreach ($addresses as $result) { ?>
        <tr>
          <td class="text-left"><?php echo $result['address']; ?></td>
          <td class="text-right"><a href="<?php echo $result['update']; ?>" class="btn btn-info"><?php echo $button_edit; ?></a> &nbsp; <a href="<?php echo $result['delete']; ?>" class="btn btn-danger"><?php echo $button_delete; ?></a></td>
        </tr>
        <?php } ?>
      </table> -->
      <div class="address_card">
        <?php foreach ($addresses as $result) { ?>
        <?php echo $result['address']; ?>
      <div class="address_card_btn clearfix">  
     <a href="<?php echo $result['update']; ?>"><?php echo $button_edit; ?></a> &nbsp; <a href="<?php echo $result['delete']; ?>"><?php echo $button_delete; ?></a>
     </div>
        <?php } ?>
      </div>


      <?php } else { ?>
      <p><?php echo $text_empty; ?></p>
      <?php } ?>
      <div class="buttons clearfix">
        <div class="pull-left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
        <div class="pull-right"><a href="<?php echo $add; ?>" class="btn btn-continue"><?php echo $button_new_address; ?></a></div>
      </div>
      <?php echo $content_bottom; ?></div>
    </div>
</div>


<section id="footer_box"></section>
<section id="login_box"></section>
<section id="opt_box"></section>
<section id="register_box"></section>
<section id="success_box"></section>
<div class="global-bg-layer"></div>
<div class="global-ajax-loader"></div>

<!-- start chatbox -->
<div class="custom_chat_box">
    <div class="chat_box_heading_div">
        <p class="chat_box_heading">Online <i class="fa fa-angle-up"></i></p>
    </div> 
    <div class="chat_box_content">
        <p><?php echo $text_chatbox_message; ?></p>
        <ul class="list-unstyled1 components">
            <li class="dropdown">
                <a href="#whatsapp" data-toggle="collapse" aria-expanded="false" class="dropdown-link dropdown-toggle"><i class="fa fa-whatsapp"></i> <div class="dropdown_arrow"><i class="fa fa-angle-right"></i></div><?php echo $text_whatsApp ?></a>
                <ul class="collapse list-unstyled" id="whatsapp">
                    <li>
                        <div class="whatsaap_web_title">
                            <a href="https://web.whatsapp.com/send?phone=<?php echo $text_whatsappw_no ?>&text=<?php echo $text_whatsappw_no_msg_text ?>" target="_blank"><i class="fa fa-comments-o"></i> <?php echo $text_chat_on_whatsappw_web ?></a>
                        </div>
                        <p class="or_outer"><span class="or">or</span></p>
                        <p class="whatsapp_on">Whatsapp on <?php echo $text_whatsappw_no ?></p>
                    </li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="#callus" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle dropdown-link"><i class="fa fa-phone" aria-hidden="true"></i> <?php echo $text_callus ?> <div class="dropdown_arrow"><i class="fa fa-angle-right"></i></div></a>
                <ul class="collapse list-unstyled" id="callus">
                    <li><p class="call_us">Call us on <?php echo $text_callus_no ?></p></li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="#call_back_request" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle dropdown-link"><i class="fa fa-user"></i> <?php echo $text_call_back_request ?> <div class="dropdown_arrow"><i class="fa fa-angle-right"></i></div></a>
                <ul class="collapse list-unstyled" id="call_back_request">
                    <li>
                        <p><?php echo $text_call_back_request_messgae; ?></p>
                        <input class="callback_request_input" type="text" maxlength="10" onkeypress="return isNumberKey(event)">
                        <input class="callback_request_button" type="submit" value="Submit">
                        <div class="loading"></div>
                        <div class="error_msg"></div>
                        <div class="success_mail">Thank you! We shall call you shortly.</div>
                    </li>
                </ul>
            </li>

            <li class="chat_here">
                <a href="javascript:void(Tawk_API.toggle())"><i class="fa fa-comments"></i> <?php echo $text_chat_here ?></a>
            </li>
            
        </ul>
    </div> 
</div>
<!-- End chatbox -->


<!-- start chatbox css -->
<style>
    .custom_chat_box{
        width: 300px;
        position: fixed;
        z-index: 99;
        right: 12px;
        bottom: 0;
        font-size: 15px;
    }
    .chat_box_heading_div{
        background-color: #17319f;
        color: #fff;
        border-radius: 10px 9px 0 0;
        padding: 6px 10px;
        cursor: pointer;
    }
    .chat_box_heading_div i {
        float: right;
        font-weight: bold;
        margin: 4px;
    }
    .chat_box_heading{
        padding: 0px;
        font-size: 22px;
        margin: 0;
    }
    .call_us{
        margin: 0;
    }
    .chat_box_content{
        padding: 10px 10px 0 10px;
        border: 1px solid #ddd;
        display: none;
        background: #fff;
        /*height: 230px;
        overflow: scroll;*/
    }
    ul.list-unstyled1.components {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    ul.list-unstyled1 li.inactive{
        background: none;
    }
    ul.list-unstyled1 li.active{
        background: #edf2fa;
        color: #636363;
        border-radius: 7px 7px 0 0;
        
    }
    
    ul.list-unstyled1 li.active a.dropdown-toggle:hover{
        border-radius: 7px 7px 0 0;
        
    }
    ul.list-unstyled1 li.active a.dropdown-toggle{
        color: #636363;
    }
    
    .custom_chat_box ul li{
        border-bottom: 1px solid #ccc;
    }
    li.chat_here{
        border:none !important;
    }
    .custom_chat_box ul li a{
        padding: 5px 5px;
        display: block;
    }
    .custom_chat_box ul li a.dropdown-toggle:hover{
        background: #edf2fa;
        color: #636363;
    }
    .custom_chat_box ul li i{
        font-size: 20px;
        margin-right: 5px;
    }
    .custom_chat_box .dropdown_arrow{
        float: right;
    }
    .dropdown_arrow i {
        font-size: 15px !important;
    }
    .custom_chat_box ul li ul{
        padding: 5px 15px;
        background : #f9f9f9;
    }
    .custom_chat_box ul li ul li{
        border-bottom: 0;
    }
    .fa-whatsapp{
        color: #25D366;
    }
    .callback_request_input{
        height: 30px;
    }
    .callback_request_button{
        height: 30px;
        color: #fff;
        background-color: #17319f;
        margin-left: 5px;
        padding: 0 15px;
        border: none;
    }
    .error_msg {
        color: #FF0000;
    }
    .success_mail {
        color: #228B22;
        display: none;
    }
    .whatsaap_web_title a{
        padding: 5px 10px !important;
        border-radius: 7px;
        border: 1px solid #636363;
        color: #636363;
        display: table !important;
        margin: 0 auto !important;
    }
    .whatsaap_web_title a:hover{
        color: #636363;
    }
    .whatsaap_web_title p{
        margin: 0;
    }
    
    .or_outer{
        text-align: center;
        margin: 10px 0 !important;
        padding: 0;
    }
    .whatsapp_on{
        margin: 0;
        text-align: center;
    }
    .or{
        background: #f0f0f0;
        padding: 1px 4px;
        border-radius: 9px 9px 9px 9px;
    }
    .custom_chat_box input[type=number]::-webkit-inner-spin-button, 
    .custom_chat_box input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0; 
    }
    
</style>
<!-- End chatbox css -->

<!-- jQuery -->
<script src="<?php echo LOCAL_CDN_URL_SSL.COMMON_VENDOR_JS; ?>"></script>
<script src="<?php echo LOCAL_CDN_URL_SSL.COMMON_REACT_JS; ?>"></script>

<script>
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


</script>

<script type="text/javascript">
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

/* Start chatbox toggle */
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if ((charCode < 48 || charCode > 57))
        return false;

    return true;
}

$('.chat_box_content li.dropdown').each(function() {
    var $dropdown = $(this);
    
    //$(".chat_box_content li.dropdown").css("border-bottom: 1px solid #ccc;");

    $("a.dropdown-link", $dropdown).click(function(e) {
        e.preventDefault();
        
        if($dropdown.hasClass("active")){ 
            $(".chat_box_content li.dropdown").removeClass("active");
            $(".chat_box_content li.dropdown").css("border-bottom", "1px solid #ccc");
            
            //toggle arraows
            $(".chat_box_content li.dropdown .dropdown_arrow i").removeClass("fa-angle-down");
            $(".dropdown_arrow i", this).addClass('fa-angle-right', 200);
            //end toggle arraows
            
        }else{ 
            $(".chat_box_content li.dropdown").removeClass("active");
            $(".chat_box_content li.dropdown").css("border-bottom", "1px solid #ccc");
            
            $dropdown.addClass("active", 200);
            $dropdown.prev().css("border-bottom", "none");
            
            //toggle arraows
            $(".chat_box_content li.dropdown .dropdown_arrow i").removeClass("fa-angle-down");
            $(".dropdown_arrow i", this).toggleClass('fa-angle-down', 200);
            //end toggle arraows
        }
        
        $div = $("ul.list-unstyled", $dropdown);
        $div.slideToggle();
        
        $("ul.list-unstyled").not($div).slideUp();
        return false;
    });

});
/* End chatbox toggle */

$(".chat_box_heading_div").click(function(){
    $(".chat_box_content").toggle();
    $("i", this).toggleClass("fa-angle-up fa-angle-down");
    $(".chat_box_content ul.list-unstyled").hide();
    $(".chat_box_content li.dropdown").removeClass("active");
    $(".chat_box_content li.dropdown").css("border-bottom", "1px solid #ccc");
    $(".chat_box_content li.dropdown .dropdown_arrow i").removeClass("fa-angle-down");  
    $('.callback_request_input').val('');
    $('.chat_box_content .error_msg').html(''); 
});
$(".chat_here").click(function(){
    $(".chat_box_content").toggle();
});

</script>
<script type="text/javascript">
    $( ".callback_request_button" ).click(function() {
            
            var mobile = $(this).closest("ul.list-unstyled").find("input").val();
            
            var status = true;
            var msg = '';   
            
            if( mobile == ''){
                msg = "<?php echo $text_error_mobile_no ?>";
                status = false;
            }else
            if(isNaN(mobile)||mobile.indexOf(" ")!=-1){
                msg = "<?php echo $text_error_mobile_no ?>";
                status = false;
            }else
            if (mobile.length!=10){
                msg = "<?php echo $text_error_mobile_no ?>";
                status = false;
            }else
            if (mobile.charAt(0)=="0"){
                msg = "<?php echo $text_error_mobile_not_start_zero ?>";
                status = false;
            }
            
            if(status == false){
                $(this).closest("ul.list-unstyled").find('.error_msg').html(msg);
                return false
            }else{
                var ajax_url = 'index.php?route=common/home/sendMailForCallBackRequest&mobile='+mobile
                $.ajax({
                    url: ajax_url,
                    beforeSend: function() {
                        $(this).closest("ul.list-unstyled").find('.loading').html('<img src="http://cdnimages.net/loader.gif" />');
                    },
                    success: function( data ){
                        $('.error_msg').html('');
                        $('.loading').html('');
                        $(".success_mail").fadeTo(2000, 500).slideUp(500, function(){
                            $(this).closest("ul.list-unstyled").find(".success_mail").slideUp(500);
                        });
                    }
                }); 
                
            }
            
        }); 
</script>

</body>

</html>