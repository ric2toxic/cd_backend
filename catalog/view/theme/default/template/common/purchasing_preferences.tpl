<!DOCTYPE html>
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
    

    <meta property="og:title" content="<?php echo $title; ?>"/>
    <meta property="og:site_name" content="Wholesalebox"/>
    <meta property="og:description" content="<?php echo $description; ?>"/>
    <meta property="og:url" content="<?php echo $home_url; ?>"/>

    <?php if ($icon) { ?>
    <link href="<?php echo $icon; ?>" rel="icon" />
    <?php } ?>

    <link rel="alternate" hreflang="x-default" href="<?php echo $co_store.$request_uri;?>" />
    <link rel="alternate" hreflang="en-in" href="<?php echo $in_store.$request_uri;?>" />
    <link rel="dns-prefetch" href="//fonts.googleapis.com"/>
    <link rel="dns-prefetch" href="//www.googletagmanager.com"/>
    <link href="catalog/view/theme/default/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">

 @font-face {
  font-family: 'SourceSansPro-Regular';
  src: url('https://cdnimages.net/css/fonts/SourceSansPro-Regular.eot');
  src: url('https://cdnimages.net/css/fonts/SourceSansPro-Regular.eot') format('embedded-opentype'), url('../fonts/SourceSansPro-Regular.woff2') format('woff2'), url('https://cdnimages.net/css/fonts/SourceSansPro-Regular.woff') format('woff'), url('../fonts/SourceSansPro-Regular.ttf') format('truetype'), url('(https://cdnimages.net/css/fonts/SourceSansPro-Regular.svg?v=4.7.0#fontawesomeregular') format('svg');
  font-weight: normal;
  font-style: normal;
}
body {
    color: #565656;
    font-family:"SourceSansPro-Regular";
    background:#ffff !important;
}
a{ outline: none; text-decoration: none; }
a:hover, a:focus{ outline: none; text-decoration: none; }
.btn{outline: none;}

.btn {-moz-user-select: none;background-image: none;border: 1px solid transparent;cursor: pointer;display: inline-block;font-size: 14px;font-weight: 400;line-height: 1.42857;margin-bottom: 0;padding: 6px 12px;text-align: center;vertical-align: middle;white-space: nowrap;border-radius: 0px;}
.panel-title > .small, .panel-title > .small > a, .panel-title > a, .panel-title > small, .panel-title > small > a {
    color: inherit;
}
.btn.focus, .btn:focus, .btn:hover {
    color: #dedede;
    text-decoration: none;
}
a {
    color: #17319f;
    text-decoration: none;
}   
 /*---purchasing preferences page (start)---*/
.background_image{
  background: linear-gradient(rgba(0, 0, 0, 0.45),rgba(0, 0, 0, 0.45)), url("../image/webside_bg.jpg"); background-size: cover;}
.purchasing_saction{background: #fff;margin: 3% auto;padding: 2% 3%;width: 980px;display: block; }
.purchasing_saction header{text-align: center; border-bottom:1px solid #ddd; padding: 10px; color:#17319f; font-size: 16px; }
.purchasing_body{display: block;padding: 10px;}
.purchasing_body .purchasing_category{padding: 0px; position: relative; background-repeat: no-repeat !important;}
.purchasing_body .purchasing_category a{display: block; text-decoration: none; min-height: 230px;}
.purchasing_body .purchasing_category a .purchasing_category_img{position: absolute; bottom: 0px; left: 0px;}
.purchasing_body .purchasing_category a .purchasing_category_text{text-align: right; display: block; padding: 20px; color: #000; font-size: 16px; line-height: 10; text-transform: uppercase;transform: perspective(1px) translateZ(0px); transition-duration: 0.3s; transition-property: transform; }
.purchasing_body .purchasing_category a:hover .purchasing_category_text{  transform: scale(1.1);  color: #b4040a;}
.purchasing_body .purchasing_category a .right_arrow_box{background: #fff;border: 2px solid #17319f;border-radius: 40px;display: none;height: 30px;left: 48%;padding: 0 5px;position: absolute;right: 14%;top: 20px;width: 30px;}
.purchasing_body .purchasing_category a:hover .right_arrow_box{display: block;}

.purchasing_body .women_section{ background: #ffecb8; }
.purchasing_body .fabrics_section{ background: #70dcff; }
.purchasing_body .man_section{ background: #baafff; }
.purchasing_body .man_section a .purchasing_category_img{left:20px;}
.purchasing_body .kidsWear_section{ background: #affffe; }
.purchasing_body .footwear_section{ background: #ffef64; }
.purchasing_body .footwear_section a .purchasing_category_img{top: 0px; right: 0px;left: 148px;}
.purchasing_body .footwear_section a .purchasing_category_text{text-align: left;}
.purchasing_body .home_furnishing_section{ background: #f5ffbe; }


.purchasing_body .section_1{ background: #ffecb8; background-position:0px 23px; }
.purchasing_body .section_2{ background: #70dcff; background-position:0px 55px; }
.purchasing_body .section_3{ background: #baafff;  background-position:21px 21px;}
.purchasing_body .section_4{ background: #affffe; background-position:0px 0px; }
.purchasing_body .section_5{ background: #ffef64; background-position:153px 0px;  }
.purchasing_body .section_5 a .purchasing_category_text{text-align: left;}
.purchasing_body .section_6{ background: #f5ffbe; background-position:0px 20px;  }
/*---purchasing preferences page (end)---*/ 

    </style>
<body>
<div class="container-fluid background_image">
   <!-- contact saction(start) -->
   <div class="purchasing_saction">
       <header> Tell us your preferences and never miss new stocks matching your taste.</header>
        <div class="purchasing_body">
        <?php
          $i=1;
         foreach($menus as $menus_data) {
         ?>
         <div class="col-sm-4 purchasing_category section_<?php echo $i; ?>" style="background-image: url(<?php echo $menus_data['image']; ?>);">
           <a href="javascript:;" onclick="set_preferences_cookie('preferences', <?php echo $menus_data['value']; ?>, 7);">
            <div class="right_arrow_box"><img src="image/right_arrow.png" /></div>
            <div class="purchasing_category_text" ><?php echo $menus_data['link_title']; ?></div>
           </a>
         </div>
        <?php if($i==6) { $i=0; }  $i++; } ?> 
         <div class="clearfix"></div>
       </div>
   </div>
   <div class="clearfix"></div>
   <!-- contact saction(end) -->
</div>         
       <!-- jQuery -->
<script src="catalog/view/theme/default/js/bootstrap.min.js"></script>       
<script src="catalog/view/theme/default/js/jquery.js"></script>

<script type="text/javascript">

    $(document).ready(function(){
        // $(document).on('load',function () {
          var heights =  window.innerHeight + 'px';
          // alert(heights);
          $('.background_image').css('height', heights);
        // });
    });  


function set_preferences_cookie(name, value, days)
{
  var expires;
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    expires = "; expires=" + date.toGMTString();
  }
  else {
    expires = "";
  }
  document.cookie = name + "=" + value + expires + "; path=/";
  location.reload();
}

</script>
    



</body>

</html>