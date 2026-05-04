<?php
require_once('config.php');
$msg = '';

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


define('CDN_URL', STATIC_CONTENT_URL_SSL);


/**
 * Get IP address of client machine
*/
function getIpAddress(){ 
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return  $ipaddress;
}

$ip_address = getIpAddress();

//live url
if ($_SERVER['SERVER_NAME'] == 'www.wholesalebox.co') {
    $store_id = '2';
    $phone_no_for_domain = '+919116134795';
    define('SITEURL', 'https://www.wholesalebox.co');

} else if ($_SERVER['SERVER_NAME'] == 'www.wholesalebox.in') {
    $store_id = '1';
    $phone_no_for_domain = '+918696491521';
    define('SITEURL', 'https://www.wholesalebox.in');

} else {
    $store_id = '1';
    $phone_no_for_domain = '+918696491521';
    define('SITEURL', 'http://www.wsb.in');
}

//check staging url
if (isset($_SERVER['REQUEST_URI'])) {
    $is_staging = strpos($_SERVER['REQUEST_URI'], 'staging');
}

// Get Url
if ($_SERVER['SERVER_NAME'] == 'www.wsb.in') {
  $crm_site_url = "http://localhost/wsbox-crm/";

} elseif($is_staging !== false) {
  $crm_site_url = "https://www.wholesalebox.biz/staging/";

} else {
  $crm_site_url = "https://www.wholesalebox.biz/";
}

//campaign id
$campaign_id = isset($_REQUEST['cid'])? $_REQUEST['cid'] : '1';

if (isset($_REQUEST['cid']) && !empty($_REQUEST['cid'])) {
    $url_param = "cid=".$_REQUEST['cid']."&";
} else {
    $url_param = '';
}


//Call back request
if (isset($_POST['submit_callback'])) {
    
    if (!empty($_POST['phone_number_callback'])) {
        
        $data_json = array(
            'phone_number'  => $_POST['phone_number_callback'],
            'ip'            => $ip_address,
            'browser'       => isset($_POST['browser']) ? $_POST['browser']: '',
            'referral'      => isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '',
            'campaign_event_no'   => $campaign_id
          );

        //curl url       
        $curl_url = $crm_site_url."/api/callbackToMeBusinessLanding";   
        
        $data_json = json_encode($data_json);           
        $ch =  curl_init();
        curl_setopt($ch,CURLOPT_URL,$curl_url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        $result=curl_exec($ch);
        curl_close($ch);

        // echo "<pre>";
        // print_r($result); die; 

        if(!empty($result)) {        
          // $msg = "Thanks for your call back request, We will call you back shortly.";
          header("location:".SITEURL."/thankyou.php?".$url_param."action=callme");
        }

    } else {      
          $msg = "Please enter valid mobile number.";
    }
      
    if (isset($result['status'])) {
      //header("location:".SITEURL."/thankyou.php");
  
    } else {
        $_SESSION['message']['msg'] = $msg;
        $_SESSION['message']['status'] = isset($result['status']) ? $result['status'] : '';
    }
    unset($_POST);
}



//create a free account request || signup request 

if (isset($_POST['submit_btn']) || isset($_POST['subscribe_btn'])) {

  if ( !empty($_POST['phone_number']) && !empty($_POST['business_email']) && !empty($_POST['name'])) {

        $data_json = array(
            'country_code'    => isset($_POST['country_code']) ? $_POST['country_code']: '',

            'phone_number'    => isset($_POST['phone_number']) ? $_POST['phone_number']: '',

            'name'            => isset($_POST['name']) ? $_POST['name']: '',

            'business_email'  => isset($_POST['business_email']) ? $_POST['business_email']: '',


            'business_name'   => isset($_POST['business_name']) ? $_POST['business_name']: '',

            'ip'              => $ip_address,

            'browser'         => isset($_POST['browser']) ? $_POST['browser']: '',

            'referral'        =>  isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '',
            'campaign_event_no'   => $campaign_id,
            'store_id'   => $store_id
        );       


        //curl url
        $curl_url = $crm_site_url."cron/business_landing_page";
        $data_json = json_encode($data_json);
        $ch =  curl_init();
        curl_setopt($ch,CURLOPT_URL,$curl_url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        $result=curl_exec($ch);
        curl_close($ch);

        /* echo "<pre>";
         print_r($result); die; */

        //$result = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result), true );

        //$msg =  isset($result['msg']) ? $result['msg'] : '';

    } else {
        $msg = "Please fill the all validate fields.";
    }

    if (!empty($result)) {
        header("location:".SITEURL."/thankyou.php?".$url_param."action=signup");

    } else {
        $_SESSION['message']['msg'] = $msg;
        $_SESSION['message']['status'] = isset($result['status']) ? $result['status'] : '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="description" content=""> -->
    <meta name="author" content="">

    <!-- <title>The WholesaleBox Business Difference</title> -->

    <?php if($store_id > 1) { ?>

        <title> Online store for wholesale buying & selling of Affordable clothing</title>

    <meta name="description" content="Online Wholesale Clothing shopping and dropshipping company. Indian wholesale fabrics, womenswear, menswear, stocklot,wholesale lingerie suppliers at Worldwide" />

    <meta name="keywords" content= "Wholesale Clothing, kidswear, menswear, wholesale footwear, wholesale lingerie, wholesale shopping, wholesale womens clothing, wholesale home decor, wholesale fashion jewelry, wholesale online shopping, kids accessories" />

        <script type="text/javascript">
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-MBB945');
        </script>

    <?php } else { ?>

        <title>Wholesale Clothing Supplier of Kidswear, menswear, womens clothing and home decor</title>

    <meta name="description" content=" Wholesalebox connects clothing manufacturing units to boutiques & shopkeepers. Online shopping website for wholesale sarees, Salwar Kameez, Kurti & dress material" />

    <meta name="keywords" content= "wholesale kurtis, Ladies wholesale designer suits online , wholesale web site for ladies dress material, wholesale Salwar Kameez,Dress Wholesaler, saree wholesale, wholesale online shopping, party wear kurtis,Designer suits wholesaler,Bedsheets wholesale, wholesale fabrics, stocklots"/>

        <script type="text/javascript">
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-TRP9H5');
        </script>

    <?php } ?>

    <!-- awesome Fonts -->
    <link href="<?php echo CDN_URL ?>landing-page/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo CDN_URL ?>landing-page/css/bootstrap.min.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="<?php echo CDN_URL ?>landing-page/css/custom.min.css" rel="stylesheet" type="text/css">
</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-fixed-top header_section">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand scroll_effect" data-target="#lp-pom-root"><img src="<?php echo CDN_URL ?>landing-page/images/wsb_logo_blue.jpg" alt="wsb_logo"></a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right subscribe_nav">
                <li><a class="scroll_effect" data-target="#wholesaleBox_work">Our Business Model</a></li>
                <li><a class="scroll_effect" data-target="#buy_us">Why Us?</a></li>
                <li><a class="scroll_effect" data-target="#about_section">About us</a></li>
                <li><a class="scroll_effect" data-target="#can_buy">What We Sell ?</a></li>
                <li><a class="scroll_effect" data-target="#mobile_app">Download App</a></li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>

<!-- Header Carousel -->
<header class="slide" id="lp-pom-root">

    <!-- slides section -->
    <div class="slider_bg">
        <div class="slider_bg_color">
            <div class="container slider_contant">
                <div class="slider_contant_text">
                    <h3>
                        Are you a seller of Fashion clothes, Footwear, Handbags or Home decor items?
                    </h3>
                    <span>
                            Yes, you are on right place. We are online wholesalers who provide all types of fashion clothes, footwear, Mens wear, Kids wear, bed-sheets etc. at a wholesale price with a <span class="green">guarantee of 25-30 % saving on your current buying.</span>
                            </span>
                            <div class="col-sm-12 slider_input_box">
                                <div class="slider_input_section">
                                    <!-- <input id="email" class="form-control slider_input" placeholder="Your Email id" type="email">
                                    <button class="button slider_input_btn" type="button">Subscribe</button> -->
                                  <?php if ($store_id > 1) { ?>
                                      <form onsubmit="return validateAccount('subscribe_email')" >
                                        
                                          <input type="text" required name="subscribe_email"  class="form-control slider_input subscribe_email" placeholder="Your Email id" pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,3}$" x-moz-errormessage="Please enter the valid email address" oninvalid="setCustomValidity(this.willValidate?'':'Please enter the valid email address')" title="Please enter the valid email address"/>             
                                        
                                          <button type="submit" class="button slider_input_btn">SUBSCRIBE</button>
                                      </form>

                                  <?php } else { ?>

                                        <form method="post" action="" name="callback_form" >                                       
                                          <input type="text" name="phone_number_callback"  class="form-control slider_input" placeholder="Phone Number" required pattern="[1-9]{1}[0-9]{9}" x-moz-errormessage="Please enter the valid mobile number (e.g. 8696491521)" oninvalid="setCustomValidity(this.willValidate?'':'Please enter the valid mobile number (e.g. 8696491521)')" title="Please enter the valid mobile number (e.g. 8696491521)"/>           

                                          <input type="hidden" name="browser" class="browser" value=""/>

                                          <button type="submit" class="button slider_input_btn" name="submit_callback">Call Me </button>                                          
                                      </form>

                                  <?php } ?>
                                </div>                                
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Page Content -->
<div class="container" id="wholesaleBox_work">

    <!-- WholesaleBox work Section -->
    <div class="row">
        <Section class="col-lg-12 content_space">
            <h3 class="page-header_title">How does WholesaleBox work?</h3>
            <div  class="col-sm-12 title_line_box">
                <div class="title_line"></div>
            </div>
            <div class="text-center padding_text_contant"><p>WholesaleBox is leading Online wholesale Supplier with the aim to provide goods <br> directly from manufacturers to Retailers at lowest factory price.</p></div>
            <div class="content_center"><img src="<?php echo CDN_URL ?>landing-page/images/wsb-dekstop.jpg" alt="WholesaleBox work" class="img-responsive contact_img_h">
                <img src="<?php echo CDN_URL ?>landing-page/images/wsb-mobile.jpg" alt="WholesaleBox work" class="img-responsive contact_img_v">
            </div>
        </Section>
    </div>
</div>
<!-- buy us Section -->
<div class="container-fluid buy_wholesalebox" id="buy_us">
    <div class="container">
        <div class="row">
            <Section class="col-lg-12 content_space">
                <h3 class="page-header_title">What makes people to buy from wholesalebox</h3>
                <div  class="col-sm-12 title_line_box">
                    <div class="title_line"></div>
                </div>
                <div class="text-center padding_text_contant_full">
                    <div class="our_story_text" style="color: #fff;  text-align: justify">
                        <ul>
                            <li><label><i class="fa fa-hand-o-right" aria-hidden="true"></i></label> Lowest Factory Price</li>
                            <li><label><i class="fa fa-hand-o-right" aria-hidden="true"></i></label> No Membership Fees</li>
                            <li><label><i class="fa fa-hand-o-right" aria-hidden="true"></i></label> Multiple Payment Options</li>
                            <li><label><i class="fa fa-hand-o-right" aria-hidden="true"></i></label> Order online from shop</li>
                            <li><label><i class="fa fa-hand-o-right" aria-hidden="true"></i></label> Easy Return Policy</li>
                            <li><label><i class="fa fa-hand-o-right" aria-hidden="true"></i></label> Direct Contact with us</li>
                            <li><label><i class="fa fa-hand-o-right" aria-hidden="true"></i></label> Proper Tax Billing</li>
                            <li><label><i class="fa fa-hand-o-right" aria-hidden="true"></i></label> Guaranteed Savings On Every Order</li>
                            <li><label><i class="fa fa-hand-o-right" aria-hidden="true"></i></label> Best Logistic Partners</li>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                    <div class="business_btn">
                        <P>Run Your Business Hassle-free and leave all sourcing to us </P>
                        <button class="create_account_btn open_popup" data-toggle="modal" data-target="#signup_popup">CREATE A FREE ACCOUNT</button>

                    </div>
                </div>
            </Section>
        </div>
    </div>
</div>
<!-- about Section -->
<div class="container" id="about_section">
    <div class="row">
        <Section class="col-lg-12 content_space">
            <h3 class="page-header_title">WholesaleBox a perfect platform for wholesale buying</h3>
            <div  class="col-sm-12 title_line_box">
                <div class="title_line"></div>
            </div>
            <div class="text-center padding_text_contant">
                <div class="about_text">
                    <ul>
                        <li>WholesaleBox is a perfect marketplace for wholesale buying and selling across the World, connecting manufacturers or big wholesalers directly to retailers.</li>
                        <li>We are a bunch of technology and business people from IIMs, IITs, NITs, etc., who is working to bring efficiency in the whole distribution system and help retailers get more variety at their doorsteps.</li>
                        <li>Use the technology a boon for retailers to buy products on wholesale price for your outlets.</li>
                        <li>You save time to travel in different cities / manufacturing hubs as we provide clothes at lowest factory prices.</li>
                        <li>We've put the entire wholesale buying process online to enable manufacturers & brands and retailers to drive incremental revenue, cut costs, improve their customer experience and analyze performance through data analytics.</li>
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>
        </Section>
    </div>
</div>
<!-- Shopkeepers Section -->
<div class="container-fluid shopkeepers_section">
    <div class="slider_bg_color">
        <div class="container">
            <div class="row">
                <Section class="col-lg-12 content_space">
                    <h3 class="Shopkeepers_header_title">Shopkeepers Impressed By Your Wholesale Products</h3>
                    <div class="testimonial-text">
                        <div class="text-center padding_text_contant">
                            <div class="carousel slide Shopkeepers_slider" id="fade-quote-carousel" data-ride="carousel" data-interval="3000">
                                <!-- Carousel items -->
                                <div class="carousel-inner">
                                    <div>
                                        <img src="<?php echo CDN_URL ?>landing-page/images/testimonialtop.png" class="test_top" alt="">
                                        <img src="<?php echo CDN_URL ?>landing-page/images/testimonialbottom.png" class="test_bottom hidden-xs" alt="">
                                    </div>
                                    <div class="item">
                                        <blockquote>
                                            <p>It is a great app. Placing orders and buying stock for my store has become very convenient. Excellent delivery and great quality of products.</p>
                                            <div><p class="testimonial">Shivam Chopra (Gurugram)</p></div>
                                        </blockquote>
                                    </div>
                                    <div class="item">
                                        <blockquote>
                                            <p>Best quality products, I bought kurtis and i loved it. best quality and best service. Way to go... </p>
                                            <div><p class="testimonial">Sheeba Gandhi (Rajkot)</p></div>
                                        </blockquote>
                                    </div>
                                    <div class="active item">
                                        <blockquote>
                                            <p>VERY GOOD, Trust Worthy & Reasonable Prices.... The Ordered Products Reached my Customer On Time.... </p>
                                            <div><p class="testimonial">Ammu Selvam (Hyderabad)</p></div>
                                        </blockquote>
                                    </div>
                                    <div class="item">
                                        <blockquote>
                                            <p>Super good concept and well executed. We have placed many orders and happy. Easy returns and super good pricing.</p>
                                            <div><p class="testimonial">Gaurav Sanghi (Howrah)</p></div>
                                        </blockquote>
                                    </div>
                                    <div class="item">
                                        <blockquote>
                                            <p>This is user friendly app. Any one can handle easily .its very helpful to improve business for retailers. from Manufacturers direct to retailer. . </p>
                                            <div><p class="testimonial">Raj Kumar (Varanasi)</p></div>
                                        </blockquote>
                                    </div>
                                    <div class="item">
                                        <blockquote>
                                            <p>Very nice collections in reasonable price ðŸ‘</p>
                                            <div><p class="testimonial">Vishwas Khandelwal (Bikaner)</p></div>
                                        </blockquote>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </Section>
            </div>
        </div>
    </div>
</div>
<!-- video Section -->
<div class="container-fluid buy_wholesalebox">
    <div class="container">
        <div class="row">
            <Section class="col-lg-12 content_space">
                <h3 class="page-header_title">The WholesaleBox Business Difference</h3>
                <div  class="col-sm-12 title_line_box">
                    <div class="title_line"></div>
                </div>
                <div class="text-center padding_text_contant">
                    <div class="col-sm-6 nopadding">
                        <div class="video_text_box">
                            <p>Minimize Product Sourcing Cost</p>
                            <span>No travelling costs and zero loss in business in your absence.</span>
                        </div>
                        <div class="video_text_box">
                            <p>Low Inventory Risk</p>
                            <span>We do not force to place big orders so no risk of dead stocks.</span>
                        </div>
                        <div class="video_text_box">
                            <p>Keep you always Updated</p>
                            <span>You can place order anytime without any extra charges.</span>
                        </div>
                        <div class="video_text_box">
                            <p>Exclusive collection</p>
                            <span>We have collection from various cities so you can have variety in collection.</span>
                        </div>
                    </div>
                    <div class="col-sm-6 video_section">
                        <div class="lp-pom-image-container" style="overflow: hidden;">
                            <img src="<?php echo CDN_URL ?>landing-page/images/lappy.png" alt="" class="img-responsive">
                        </div>

                        <div class="lp-pom-video">
                            <!-- <iframe width="100%" height="100%"  src="https://www.youtube.com/embed/Dg21Q0VdyzE" frameborder="0" allowfullscreen></iframe> -->
                            <div class="youtube" data-embed="Dg21Q0VdyzE">

                                <div class="play-button"></div>
                            </div>
                        </div>


                    </div>

                    <div class="clearfix"></div>
                    <div class="business_btn">
                        <P>Run Your Business Hassle-free and leave all sourcing to us </P>
                        <button class="create_account_btn" data-toggle="modal" data-target="#signup_popup">CREATE A FREE ACCOUNT</button>

                    </div>
                </div>
            </Section>
        </div>
    </div>
</div>
<!-- wsb information Section -->
<div class="container-fluid wsb_info">
    <div class="slider_bg_color">
        <div class="container">
            <div class="row">
                <Section class="col-lg-12 content_space">
                    <div class="col-sm-3 wsb_info_box">
                        <span class="wsb_info_most">108094+</span>
                        <span>Designs</span>
                    </div>
                    <div class="col-sm-3 wsb_info_box">
                        <span class="wsb_info_most">68829+</span>
                        <span>Shops Joined</span>
                    </div>
                    <div class="col-sm-3 wsb_info_box">
                        <span class="wsb_info_most">24x7</span>
                        <span>Availability</span>
                    </div>
                    <div class="col-sm-3 wsb_info_box">
                        <span class="wsb_info_most">85%</span>
                        <span>Repeat Customers</span>
                    </div>
                </Section>
            </div>
        </div>
    </div>
</div>
<!-- What You Can Buy Section -->
<div class="container-fluid Can_Buy" id="can_buy">
    <div class="container">
        <div class="row">
            <Section class="col-lg-12 content_space">
                <h3 class="page-header_title"> What You Can Buy..</h3>
                <div  class="col-sm-12 title_line_box">
                    <div class="title_line"></div>
                </div>
                <div class="text-center padding_text_contant">
                    <div class="about_text we_sell_box">
                        <ul>
                            <li class="kurti"><a href="JavaScript:Void(0);"> Kurti</a></li>
                            <li class="saree"><a href="JavaScript:Void(0);"> Saree</a></li>
                            <li class="Suit"><a href="JavaScript:Void(0);"> Suits </a></li>
                            <li class="Western"><a href="JavaScript:Void(0);"> Western</a></li>
                            <li class="bottoms"><a href="JavaScript:Void(0);"> Bottoms</a></li>
                            <li class="suit_catalog"><a href="JavaScript:Void(0);"> Suit Catalog</a></li>
                            <li class="fabrics"><a href="JavaScript:Void(0);"> Fabrics </a></li>
                            <li class="dupattas_shawls"><a href="JavaScript:Void(0);"> Dupattas & Shawls</a></li>
                            <li class="home_furnishing"><a href="JavaScript:Void(0);"> Home Furnishing</a></li>
                            <li class="accessories"><a href="JavaScript:Void(0);"> Accessories</a></li>
                            <li class="menswear"><a href="JavaScript:Void(0);"> Menswear </a></li>
                            <li class="kidswear"><a href="JavaScript:Void(0);"> Kidswear</a></li>
                            <li class="footwear"><a href="JavaScript:Void(0);"> Footwear</a></li>
                            <li class="jackets"><a href="JavaScript:Void(0);"> Jackets</a></li>
                            <li class="lingerie"><a href="JavaScript:Void(0);"> Lingerie</a></li>
                            <li class="blouse"><a href="JavaScript:Void(0);"> Blouse</a></li>
                            <li class="handicrafts"><a href="JavaScript:Void(0);"> Handicrafts </a></li>
                            <li class="stock_lots"><a href="JavaScript:Void(0);"> Stock Lots</a></li>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </Section>
        </div>
    </div>
</div>
<!-- mobile app Section -->
<div class="container" id="mobile_app">
    <div class="row">
        <Section class="col-lg-12 content_space">
            <div class="col-sm-4">
                <div class="info_img"><img src="<?php echo CDN_URL ?>landing-page/images/mobile_app.jpg" alt="mobile_app"></div>
            </div>
            <div class="col-sm-8">
                <div class="info_content_box">
                    <h3 class="info_content_title">Make your business easy with app</h3>
                    <div class="info_content">
                        <ul>
                            <li>Browse 1 lakh+ designs instantly on the Wholesalebox App.</li>
                            <li>Exclusive Cashback for orders placed from App.</li>
                            <li>Share  your shortlisted designs to customer with your brand name</li>
                            <li>Zero inventory risk</li>
                            <li>Easily search the specific design OR use filters.</li>
                            <li>Place order hasslefree and choose from various payment options.</li>
                        </ul>
                    </div>
                    <div class="mobile_btn">
                         <a href="https://play.google.com/store/apps/details?id=in.wholesalebox&referrer=utm_source%3DLanding%26utm_medium%3Dtwitter_ad" target="_blank"><img src="<?php echo CDN_URL ?>landing-page/images/google-play-android-app.svg" alt="Android app on google play" width="140px"></a>

                         <a href="https://itunes.apple.com/app/apple-store/id1254820324?mt=8" target="_blank"><img src="<?php echo CDN_URL ?>landing-page/images/ios_download.svg" alt="ios app on app store" width="140px"></a>
                    </div>
                </div>
            </div>


        </Section>
    </div>
</div>
<!-- What from Section -->
<div class="container-fluid contact_box">
    <div class="container">
        <div class="row">
            <Section class="col-lg-12 content_space">
                <h3 class="page-header_title"> Signup with us !</h3>
                <div  class="col-sm-12 title_line_box">
                    <div class="title_line"></div>
                </div>
                <div class="text-center padding_text_contant"><p>Get Access to 100,000+ Designs of Kurtis, Sarees, Tops, Leggings, Jeans, Jackets,<br> Salwar-Kameez, Dress Material, Bedsheets, Handbags, Fashion Accessories, Footwear, Mens wear, Kids wear.</p></div>
                <div class="text-center padding_text_contant">
                    <div class="col-sm-12 login_from">


                        <form class="business_login_form" name="business_login_form" id="business_login_form" method="post" onsubmit="return validateAccount('subscribe')">

                            <div class="col-sm-6 login_input">

                                <div class="col-xs-4 country_code padding_left_none">
                                    <input class="form-control" type="text" name="country_code" value="91" />
                                </div>

                                <div class="col-xs-12 nopadding">
                                    <input type="text" id="contact" class="form-control phone_number_subscribe" placeholder="Phone Number" name="phone_number" required pattern="[1-9]{1}[0-9]{9}" x-moz-errormessage="Please enter the valid mobile number (e.g. 8696491521)" oninvalid="setCustomValidity(this.willValidate?'':'Please enter the valid mobile number (e.g. 8696491521)')" title="Please enter the valid mobile number (e.g. 8696491521)"/>
                                </div>
                                <div class="clearfix"></div>
                            </div>

                            <div class="form-group col-sm-6 login_input">
                                <input class="form-control" placeholder="Name" name="name" type="text" required x-moz-errormessage="Please enter your name" title="Please enter your name" oninvalid="setCustomValidity(this.willValidate?'':'Please enter your name')" pattern=".*[^ ].*">
                            </div>
                            <div class="form-group col-sm-6 login_input">
                                <input type="text" class="form-control" placeholder="Email"  name="business_email" required x-moz-errormessage="Please enter the valid email address" pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,3}$" oninvalid="setCustomValidity(this.willValidate?'':'Please enter the valid email address')" title="Please enter the valid email address" >
                            </div>
                            <div class="form-group col-sm-6 login_input">
                                <input class="form-control"  placeholder="Business Name" type="text" name="business_name">
                            </div>

                            <div class="form-group col-sm-12 login_input">
                                <button name="subscribe_btn" value="subscribe" type="submit" class="button subscribe_btn" type="button">SUBSCRIBE</button>
                            </div>
                            <input type="hidden" name="browser" class="browser" value=""/>

                        </form>
                    </div>
                    <div  class="clearfix"></div>
                </div>
            </Section>
        </div>
    </div>
</div>

<!-- Signup popup -->
<div class="modal fade add_new_address" id="signup_popup" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header address_popup_head">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="logo_popup"><img src="<?php echo CDN_URL ?>landing-page/images/wsb_logo_blue.jpg" alt="logo"></div>
            </div>
            <div class="modal-body popup_scroll padding_top_bottem">
                <div class="mobile_details_panel popup_title">
                    <h3>Signup with us !</h3>
                    <p>Get Access to 100,000+ Designs of Kurtis, Sarees, Tops, Leggings, Jeans, Jackets, Handbags, Fashion Accessories, Footwear, Salwar-Kameez, Dress Material, Mens wear, Kids wear, Bedsheets</p>
                    <div class="lp-element lp-pom-form popup_content">
                        <form name="business_account_form" id="business_account_form" action="" method="post" onsubmit="return validateAccount('signup')">
                            <div class="form-group col-sm-6">

                                <div class="col-xs-4 padding_left_none">
                                    <input type="text" class="form-control" id="country_code" name="country_code" value="91">
                                </div>

                                <div class="col-xs-12 nopadding">
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Phone Number" required pattern="[1-9]{1}[0-9]{9}" x-moz-errormessage="Please enter the valid mobile number (e.g. 8696491521)" oninvalid="setCustomValidity(this.willValidate?'':'Please enter the valid mobile number (e.g. 8696491521)')" title="Please enter the valid mobile number (e.g. 8696491521)">
                                </div>
                                <div class="clearfix"></div>
                            </div>

                            <div class="form-group col-sm-6">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" required x-moz-errormessage="Please enter your name" title="Please enter your name" oninvalid="setCustomValidity(this.willValidate?'':'Please enter your name')" pattern=".*[^ ].*">
                            </div>

                            <div class="form-group col-sm-6">
                                <input type="text" class="form-control" name="business_email" id="business_email" placeholder="Email" pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,3}$" required x-moz-errormessage="Please enter the valid email address" oninvalid="setCustomValidity(this.willValidate?'':'Please enter the valid email address')" title="Please enter the valid email address"/>
                            </div>

                            <div class="form-group col-sm-6">
                                <input type="text" class="form-control" id="business_name" name="business_name" placeholder="Business Name">
                            </div>

                            <div class="form-group col-sm-12">
                                <button name="submit_btn" value="submit_btn" type="submit" class="button popup_btn" >Sign Up</button>
                            </div>

                            <input type="hidden" name="browser" class="browser" value=""/>

                            <div class="clearfix"></div>
                        </form>


                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="clearfix"></div>

            </div>

        </div>
    </div>
</div>

<!-- Signup (End) -->



<!-- Footer -->
<footer>
    <div class="container">
        <div>

            <div class="col-sm-4 col-xs-12">
                <div class="footer4">
                    <h5>Helpline Number</h5>
                    <ul>
                        <li>
                            <a href="tel:+911414049163" id="footer-landline" class="phone"><i class="fa fa-phone"></i>(+91) 141 - 4049163</a>
                            <br /><span class="ofc_time" style="padding:0px">(10am to 8pm)</span>
                        </li>

                        <li>
                            <a  id="footer-whatsapp-1" href="tel:<?php echo $phone_no_for_domain; ?>" class="whatsapp"><i class="fa fa-whatsapp" aria-hidden="true"></i><?php echo $phone_no_for_domain; ?></a>
                        </li>
                        <li>
                            <a id="footer-whatsapp-3" href="tel:+919982330835" class="whatsapp"><i class="fa fa-whatsapp" aria-hidden="true"></i>+919982330835</a>
                        </li>
                        <li>
                            <a id="footer-whatsapp-4" href="tel:+918882842211" class="whatsapp"><i class="fa fa-whatsapp" aria-hidden="true"></i>+918882842211</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-sm-4 col-xs-12">
                <div class="footer4">
                    <h5>Logistics Partners</h5>
                    <div class = "col-sm-12" style="padding:0px;">
                        <img src="<?php echo CDN_URL ?>landing-page/images/fedex.png" class="Logistics_img" alt="FedEx is our Logistics Partner">
                    </div>
                    <div class = "col-sm-12" style="padding:0px;">
                        <img src="<?php echo CDN_URL ?>landing-page/images/gatikwe.png" class="Logistics_img" alt="Gati is our Logistics Partner">
                    </div>
                    <div class = "col-sm-12" style="padding:0px;">
                        <img src="<?php echo CDN_URL ?>landing-page/images/DTDC.png" class="Logistics_img" alt="DTDC is our Logistics Partner">
                    </div>
                    <div class = "col-sm-12" style="padding:0px;">
                        <img src="<?php echo CDN_URL ?>landing-page/images/bluedart.png" class="Logistics_img" alt="BlueDart is our Logistics Partner">
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xs-12">
                <div class="trust_seal">
                    <img src="<?php echo CDN_URL ?>landing-page/images/wsb_seal_of_trust.png" class="Logistics_img" alt="BlueDart is our Logistics Partner">
                </div>
            </div>

        </div>
        <div class="clearfix"></div>
        <div>
            <div class="col-sm-12 col-md-4 col-xs-12 social_profiles">
                <ul>
                    <li>
                        <a class="sf_facebook" id="footer-social_1" href="https://web.facebook.com/wholesalebox1" target="_blank"></a>
                    </li>
                    <li>
                        <a class="sf_twitter" id="footer-social_2" href="https://twitter.com/wholesalebox_in" target="_blank"></a>
                    </li>
                    <li>
                        <a class="sf_pinterest" id="footer-social_3" href="https://in.pinterest.com/wholesalebox" target="_blank"></a>
                    </li>
                    <li>
                        <a class="sf_instagram" id="footer-social_4" href="https://www.instagram.com/wholesalebox" target="_blank"></a>
                    </li>
                    <li>
                        <a class="sf_youtube" id="footer-social_5" href="https://www.youtube.com/channel/UCQFvYTsk3f0OllDs9UJgTUw" target="_blank"></a>
                    </li>


                </ul>
            </div>
            <div class="col-sm-12 col-md-6 hidden-xs">
                <div class="ic_payment_methods">
                </div>

            </div>


        </div>
    </div>
    <!-- /.container -->
    <script src="<?php echo CDN_URL?>landing-page/js/jquery.min.js" type="text/javascript"></script>

    <script src="<?php echo CDN_URL?>landing-page/js/bootstrap.min.js" type="text/javascript"></script>

    <script src="<?php echo CDN_URL?>landing-page/js/custom.min.js" type="text/javascript"></script>

    <?php
    if (isset($_SESSION['message']['msg']) && $_SESSION['message']['msg'] != '') { ?>

        <!-- Trigger the modal with a button -->
        <button type="button" class="hide btn btn-info btn-lg my_modal_message" data-toggle="modal" data-target="#myModalMessage">Message button</button>

        <!-- Modal -->
        <div class="modal fade" id="myModalMessage" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Message Box</h4>
                    </div>
                    <div class="modal-body message_status" data-status = "<?php echo $_SESSION['message']['status'];?>" data-url="<?php echo SITEURL ?>">
                        <p><?php echo $_SESSION['message']['msg']; ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(".my_modal_message").click();
            var status = $(".message_status").attr('data-status');

            if (status > 0) {
                var url = $(".message_status").attr('data-url');
                window.setTimeout(function() {
                    window.location.href = url;
                }, 5000);
            }

            document.onkeydown = function (e) {
                return (e.which || e.keyCode) != 116;
            };
        </script>

        <?php
        session_unset($_SESSION['message']);
    } ?>
</footer>
</body>
</html>