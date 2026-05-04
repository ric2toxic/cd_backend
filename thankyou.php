<?php 
require_once('config.php');

if ($_SERVER['REQUEST_SCHEME'] == 'http')
  define('CDN_URL', STATIC_CONTENT_URL);
else
  define('CDN_URL', STATIC_CONTENT_URL_SSL);

if ($_SERVER['SERVER_NAME'] == 'www.wholesalebox.co') { 
    define('SITEURL', 'https://www.wholesalebox.co');
    $store_id = '2';   

} else if ($_SERVER['SERVER_NAME'] == 'www.wholesalebox.in') {    
    define('SITEURL', 'https://www.wholesalebox.in');
    $store_id = '1';
       
} else {
    define('SITEURL', 'http://www.wsb.in');
    $store_id = '1';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>The WholesaleBox Business Difference</title>   

</head>
<body style="background-color:#F0F8FF">   

    <!-- Header Carousel -->
    <header class="slide" id="lp-pom-root">

    </header>
    <!-- Page Content -->
    <div class="container" id="wholesaleBox_work" style="width:auto;padding:10px;" align="center">
       <p><b>Thank you!</b></p>
       <?php 

            if($_REQUEST['action'] == 'signup') {
                echo '<p>You are successfully signed up. You are redirecting on our store.</p>';
            } else {
                echo '<p>Thanks for your call back request, We will call you back shortly.</p>';
            }
       ?>
       
       <div class"counter"><img src="<?php echo CDN_URL?>landing-page/images/counting.gif"/></div>
    </div>
    
</body>
</html>

<script src="<?php echo CDN_URL?>landing-page/js/jquery.min.js" type="text/javascript"></script>      

<script type="text/javascript">
    var url = '<?php echo SITEURL ?>';
        window.setTimeout(function() {            
              window.location.href = url;
          }, 3000);
</script>

<script type="text/javascript">
    var store_id = '<?php echo $store_id; ?>';

if (store_id > 1 ) {
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                        })(window,document,'script','dataLayer','GTM-MBB945');
} else {
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                        })(window,document,'script','dataLayer','GTM-TRP9H5');
}
</script>