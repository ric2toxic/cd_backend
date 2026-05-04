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


<div class="container width_fix">
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
 


         <section class="col-sm-12 nopadding">

         <!-- contact saction(start) -->
         <div class="col-sm-7 padding_left">
            <div class="">
          <h2 class="page_title">Contact Us</h2>
        <h4 class="contact_we_text">We’d love to hear from you!</h4>
        <p>Got a question? Send us a message and we’ll respond as soon as possible.</p>
            
                <div class="col-sm-12 nopadding">
                 <div class="col-sm-6 contact_info">
                   <div class="col-sm-2 contact_info_icon"><i class="fa fa-tag" aria-hidden="true"></i></div>
                    <div class="col-sm-10 contact_info_text">
                        <span class="sub_contact_title">Sales</span>
                        <span class="contact_info_id"><a href="">sales@wholesalebox.in</a></span>
                    </div>
               </div>
             
                 <div class="col-sm-6 contact_info">
                   <div class="col-sm-2 contact_info_icon"><i class="fa fa-life-ring" aria-hidden="true"></i></div>
                    <div class="col-sm-10 contact_info_text">
                        <span class="sub_contact_title">Support</span>
                        <span class="contact_info_id"><a href=""> support@wholesalebox.in</a></span>
                    </div>
                 </div>
                 <div class="col-sm-6 contact_info">
                   <div class="col-sm-2 contact_info_icon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                    <div class="col-sm-10 contact_info_text">
                        <span class="sub_contact_title">Phone</span>
                        <span class="contact_info_id"><a href=""> (+91) 141 - 4049163</a></span>
                    </div>
                 </div>
                 <div class="col-sm-6 contact_info">
                   <div class="col-sm-2 contact_info_icon"><i class="fa fa-info-circle" aria-hidden="true"></i></div>
                    <div class="col-sm-10 contact_info_text">
                        <span class="sub_contact_title">Genral Info</span>
                        <span class="contact_info_id"><a href=""> info@wholesalebox.in</a></span>
                    </div>
                 </div> 
               </div>
               
               
                <!-- for head office store location sort order always should be 1-->
                <?php if (count($locations) > 0) { ?>
                <div class="col-sm-12 contact_head_office">
                  <div class="head_office_contact">
                   <h3 class="contact_subtitle"><?php echo $locations[0]['name']; ?></h3> 
                     <div class="col-sm-1 head_office_box"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
                   <div class="col-sm-11 head_office_text">
                     <h3><?php echo $locations[0]['city']; ?></h3>    
                     <p><?php echo $locations[0]['address']; ?></p>
                   </div>     
                  </div>
                  <?php if($locations[0]['telephone'] != ''){ ?>    
                      <div class="head_office_contact">
                         <div class="col-sm-1 head_office_box"><i class="fa fa-volume-control-phone" aria-hidden="true"></i></div>
                       <div class="col-sm-11 head_office_text"> 
                         <p><?php echo $locations[0]['telephone']; ?></p>
                       </div>     
                      </div>
                  <?php } ?>
                  <div class="clearfix"></div>
                  
                  </div>
                <?php } ?>
        
        
              <div class="clearfix"></div>
            </div>
          </div>
         <div class="col-sm-5">
           <div class="contact_box">
            <div class="contact_card_logo"><i class="fa fa-handshake-o" aria-hidden="true"></i></div>
             <h4>Say Hello!</h4>
              <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                <div class="col-sm-6 contact_input_box <?php if ($error_name) { ?> has-error <?php } ?>"><input id="name" class="password_box" type="text" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>*" name="name" alt="Name">
                </div>
                <div class="col-sm-6 contact_input_box <?php if ($error_email) { ?> has-error <?php } ?>"><input name="email" value="<?php echo $email; ?>" class="password_box" type="E-mail" placeholder="<?php echo $entry_email; ?>*" alt="Email">
                </div>
                  <div class="clearfix"></div>
                <div class="col-sm-6 contact_input_box <?php if ($error_telephone) { ?> has-error <?php } ?>"><input name="telephone" value="<?php echo $telephone; ?>" class="password_box" type="text" placeholder="<?php echo $entry_telephone; ?>*" alt="Phone*">
                </div>

                <div class="col-sm-6 contact_input_box <?php if ($error_company) { ?> has-error <?php } ?>"><input name="company" value="<?php echo $company; ?>" class="password_box" type="text" placeholder="<?php echo $entry_company; ?>" alt="Company">
                </div>
                    <div class="clearfix"></div>
                <div class="col-sm-12 contact_teaxtarea_box <?php if ($error_enquiry) { ?> has-error <?php } ?>"><textarea name="enquiry" rows="2" class="password_box" placeholder="<?php echo $entry_enquiry; ?>" alt="Enquiry"><?php echo $enquiry; ?></textarea>
                </div>
                <div class="clearfix"></div>
                <button type="submit" class="btn deliver_btn margin_none" data-dismiss="modal" data-direction="right">Submit</button>
              </form>
              <div class="clearfix"></div>
           </div>
         </div>
        <div class="clearfix"></div>
        <?php if ($locations) { ?>
        <div class="col-sm-12 store_details">
           <h3 class="contact_subtitle">Our Experience Store</h3> 
           <ul>
              <?php foreach ($locations as $location) { 
                    if($location['location_type'] != 'office'){ 
              ?>
               <li>
                <div class="col-sm-12 store_details_box">
                   <div class="col-sm-1 head_office_box"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
                 <div class="col-sm-10">
                   <h3><?php echo $location['name']; ?></h3> 
                   <p><?php echo $location['address']; ?></p>
                 </div>     
                </div>
                <?php if($location['telephone'] != ''){ ?>   
                <div class="col-sm-12 store_details_box">
                   <div class="col-sm-1 head_office_box"><i class="fa fa-volume-control-phone" aria-hidden="true"></i></div>
                 <div class="col-sm-10">  
                   <p><?php echo $location['telephone']; ?><br>
                 </div>     
                </div>
                <?php } ?>
              </li>
              <?php } }?>
          </ul>
        <?php } ?>  
        </div>
         </section>

      <?php echo $content_bottom; ?>
    </div>
    <?php echo $column_right; ?></div>
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
</script>
</body>

</html>
