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
.form_label{font-size:14px!important;}
.radio-inline{font-size:14px!important;}
.price_range>p {font-size:14px!important;}
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



<div class="container width_fix" style="width: 83%;">
    <div class="row">
        <div class="">    
        <?php
            if(isset($error) && !empty($error)){
        ?>
            <div class="alert alert-danger">
                <?php echo $error;?>
            </div>
        <?php 
            }

            if(isset($success) && !empty($success)){
        ?>
                <div class="alert alert-success">
                    <?php echo $success;?>
                </div>
        <?php
            }
        ?>
        <div class="col-md-12 main-heading col-md-offset-2">        
            <h3 class="heading">Post Your Requirement</h3>
            <h5 class='requirement-text'><?php echo $preference_text;?></h5>
        </div>
        <form id='form-requirement' method="post" action="index.php?route=common/home/postYourRequirement" enctype="multipart/form-data" onsubmit="return checkForm()" >
            <div class="col-md-12 form-div col-md-offset-2"> 
                    <div class="col-md-3" style="padding:0px;"> 
                        <label class="form_label">Please Select Category</label>
                    </div>
                    <div class="col-md-9" style="padding:0px;">
                        <?php if(is_array($menu)){ 
                            foreach($menu as $name){
                        ?>
                        <label class="radio-inline">
                            <?php if($name == 'Women’s Fashion') { 
                                $checked = 'checked';
                            }else{
                                $checked = '';
                            }
                            ?>
                            <input type="radio" value="<?php echo $name;?>" <?php echo $checked;?> name="category"><?php echo $name; ?>
                        </label>
                        <?php } } ?>
                    </div>   
            </div>
            <div class="col-md-12 col-md-offset-2 form-div" style="padding:0px;"> 
                <label class="col-md-3 form_label">Quantity</label>
                <input type="text" class="only_number required post-form-input" name="quantity">
                <div class="col-md-8 form-div col-md-offset-4 nopadding">
                    <span class="error_text hidden">This filed is required</span>
                </div>
            </div><br>
                <div class="col-md-12 form-div col-md-offset-4 nopadding">
                    <span class="error_text hidden">This filed is required</span>
                </div> 
            <div class="col-md-12 form-div col-md-offset-2" style="padding:0px;"> 
                <label class="col-md-3 form_label">Required Days!</label>
                <input type="text" name="require_days" class="post-form-input required">
                <div class="col-md-8 form-div col-md-offset-4 nopadding">
                    <span class="error_text hidden">This filed is required</span>
                </div>
            </div>
            <div class="col-md-12 form-div nopadding"> 
                <div class="form-group col-md-3">
                    <label class="form_label">Price Range</label>
                </div>
                <div class="form-group col-md-1 price_range nopadding">
                        <p>From Rs.</p>
                </div>
                <div class="form-group col-md-2 nopadding" style="width:150px;">
                        <input type="text" class="only_number required" id="price_from" style="width:115px;" name="price_from">
                        <div class="col-md-12  form-div col-md-offset-4 nopadding">
                            <span class="error_text hidden">This filed is required</span>
                        </div>
                </div>     
                <div class="form-group col-md-1 nopadding price_range nopadding">
                    <p>To Rs.</p>
                </div>
                <div class="form-group col-md-4 nopadding" style="width:145px;">
                    <input type="text" class="only_number post-form-input required" id="price_to" style="width:120px;"name="price_to">
                    <div class="col-md-12 form-div col-md-offset-4 nopadding">
                        <span class="error_text hidden">This filed is required</span>
                    </div>
                </div>     
            </div> 
            <div class="col-md-12 form-div col-md-offset-2" style="padding:0px;"> 
                    <label class="col-md-3 form_label">Upload Refernce Image</label>
                    <input type="file" multiple class="nopadding" name="reference_image[]">
            </div> 
            <div class="col-md-12 form-div col-md-offset-2">
                <p>
                    Don't have refernce image? DO you have any refernce link from any website?    
                </p>  
            </div>         
            <div class="col-md-12 form-div col-md-offset-2" style="padding:0px;"> 
                    <label class="col-md-3 form_label">Please Provide link in box</label>
                    <input type="text"  name="refrenece_link" class="post-form-input">
            </div>
            <div class="col-md-12 form-div col-md-offset-2"> 
                <p><?php echo $description_text;?></p>
                <textarea name="description" rows="3" cols="151"></textarea>
             </div> 
             <div class="col-md-12 form-div col-md-offset-2" style="margin-top: 15px;">
                <div class="col-md-4 nopadding" style="width:30%">
                    <label class="col-md-3 form_label nopadding">Name</label>
                    <input type="text" class="required post-form-input" name="name">
                   <div class="col-md-12 nopadding alert_post">
                        <span class="error_text hidden pull-left" style="margin-left:30px;">This filed is required</span>
                    </div>
                </div>  
                <div class="col-md-6">
                    <label class="col-md-2 form_label nopadding">Email</label>
                    <input type="email" class="required post-form-input" name="email">
                    <div class="col-md-12 nopadding">
                        <span class="error_text hidden pull-left" style="margin-left:120px;">This filed is required</span>
                    </div>
                </div>
             </div>   
             <div class="col-md-12 form-div col-md-offset-2" style="margin-top: 15px;">
                <div class="col-md-4 nopadding" style="width:30%">
                        <label class="col-md-3 nopadding form_label">Mobile No</label>
                        <input type="text" name="mobile_no" class="required mobile_no post-form-input">
                   <div class="col-md-12 nopadding alert_post">
                        <span class="error_text hidden pull-left" style="margin-left:30px;">This filed is required</span>
                    </div>
                </div>  
         </div>   
             <div class="col-md-11 form-div">
                    <input type="submit" class="form-submit-button post_submit" value="Send Enquiry">
             </div>     
        </form>  
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

<style>
    .error{
        border: 1px solid red;
    }
    .error_text{
        color: red;
    }
</style> 

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

<?php if ($international_store == 1) { ?>
        <script type="text/javascript">
            var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
            (function(){
                var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
                s1.async=true;
                s1.src='https://embed.tawk.to/5a420d52bbdfe97b137fd4ba/default';
                s1.charset='UTF-8';
                s1.setAttribute('crossorigin','*');
                s0.parentNode.insertBefore(s1,s0);
            })();
        </script>
<?php } else { ?>
    <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
            var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
            s1.async=true;
            s1.src='https://embed.tawk.to/55faa35605ceaf627695ea99/default';
            s1.charset='UTF-8';
            s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0);
        })();
    </script>
<?php } ?>

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
function showTickets( status = '', page=1){ 
    var url = "<?php echo $page_url; ?>" + "&status=" + status + "&page=" + page;
    location.href = url;
    // var status_data = "status=" + status;
    // $.ajax({
    //   type: "POST",
    //   url : "http://www.wsb.in/index.php?route=account/helpdesk/viewHelpdeskTicket",
    //   data:status_data, 
    //   dataType:'html',
    //   success: function(data) {
    //            console.log(data);
               
    //   }
    // });
    
  }
</script>



   
<script>

    $(document).ready(function () {
        //called when key is pressed in textbox
        $(".only_number").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                //display error message
                    return false;
            }
        });
    });
    
    function checkForm(){
        var check = [];
        var value = '';
       $(".required").each(function(i,e){
          value = $(e).val();
          var atr =  $(e).attr('name');
          if(value == ''){
            check.push('false');
            $(e).next().find('span').removeClass('hidden');
            $(e).addClass('error');
          }else{
                $(e).next().find('.error_text').addClass('hidden');
                $(e).removeClass('error');
                if(atr == 'mobile_no'){
                    var filter = /^\d*(?:\.\d{1,2})?$/;
                    if (filter.test(value)) {
                        if(value.length == 10){
                        }else{
                        if(value != ''){
                                $(e).next().find('span').removeClass('hidden');
                                $(e).next().find('span').text("Mobile number is not valid");
                                check.push('false'); 
                            }
                        }
                    }else{
                        if(value != ''){
                            $(e).next().find('span').removeClass('hidden');
                            $(e).next().find('span').text("Mobile number is not valid");
                            check.push('false'); 
                        }
                    }
                }
          }
       });
       var price_from = $("#price_from").val();
       var price_to   = $("#price_to").val();
       if(parseInt(price_from ) > parseInt(price_to)) {
           alert("Price range is not proper");
           return false;
       }
      if(check[0] == 'false'){
        return false;
      }else{
          return true;
      }
        
    }
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
</script>

</body>

</html>