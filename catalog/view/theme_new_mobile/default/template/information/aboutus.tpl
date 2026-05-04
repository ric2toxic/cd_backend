<?php echo $header; ?>
<div class="about_us_page">
  <div class="about_us">
      <div class="row">
        <div class="col-sm-12">
          <img src="<?php echo $images['wsb_topimg']; ?>">
        </div>
      </div>
    </div>
  <div class="container"> 
      <div class="about_tab">
            <div class="bhoechie-tab">
              <div class="tab-text">
              <div class="col-sm-12">
                <a href="#">
                  <h2>LOWEST FACTORY PRICE ASSURED</h2>
                </a>
              </div>
                <div class="about_image col-sm-12"><img src="<?php echo $images['infographics_1']; ?>" class="tab_infografics"></div>
                      <p>We bring you the best prices as we operate at very low margins.
                      If you compare us with a normal Wholesaler nearby you. Buying from us, you can save 25%-35% of the procurement cost on every order. We have more than 15000+ designs on our website for retailers with convenience of e-commerce like COD, Home Delivery, Easy Returns, Value for Money with respect to Quality, Personal Buying Assistance, Volume Discounts, Credit Facilities, and the most important one, Guaranteed Lowest Prices!!! Shop at WholesaleBox, so that you can offer great pricing to your customers. In overall, buying at WholesaleBox will increase your profit margins at the convenience of your fingertips. </p>
              </div>
              <div class="tab-text">
                <div class="col-sm-12">
                  <a href="#">
                    <h2>HOW DO WE WORK</h2>
                  </a></div>
                  <div class="about_image col-sm-12"><img src="<?php echo $images['infographics_2']; ?>" class="tab_infografics"></div>
                        <p>WholesaleBox is an online platform where retailers/shopkeepers can buy hasslefree.
                        And they reduce their procurements costs at better quality, from the convenience of his/her home/shop. From WholesaleBox, a retailer can order products in bulk for reselling and the order will be delivered at his/her chosen place. The ordering process is pretty simple. Sign Up (if first time), or Login the website using your mobile number or email address. Select designs you wish to place the order for, and add them to your cart. Use filters, sorts, and search to get the desired products of your choice from more than 20000 to choose from. Once selection is complete, go to cart, click checkout and follow the steps to place the order with WholesaleBox and get Door Delivery done. Easy returns policy is provided post delivery of the Goods. Please review the Returns and Cancellation Policy. </p>
              </div>
              <div class="tab-text">
                <div class="col-sm-12">
                  <a href="#">
                    <h2>ABOUT US</h2>
                  </a>
                  </div>
                  <div class="about_image col-sm-12"><img src="<?php echo $images['infographics_3']; ?>" class="tab_infografics"></div>
                        <p>WholesaleBox is a marketplace for wholesale buying and selling across India.
                        We are connecting manufacturers or big wholesalers directly to retailers. We are a bunch of technology and business people from IIMs, IITs, NITs, etc, who are working to bring efficiency in the whole distribution system and help retailers get more variety at their doorsteps. They can take advantage of our simple web interface to buy products for their retail outlets and get those without having to travel to different cities / manufacturing hubs or to buy at much higher prices from the wholesalers near them. We've put the entire wholesale buying process online to enable manufacturers & brands and retailers to drive incremental revenue, cut costs, improve their customer experience and analyze performance through data analytics. </p>
              </div>
            </div>
      </div>
        
        <div class="wsb-life">
          <h1>LIFE @ WHOLESALEBOX</h1>
          <div class="imagecollage">
            <div class="row">
              <div class="col-sm-12 img-adjust">
                <div class="col-sm-4"><img src="<?php echo $images['AU_img_2']; ?>"></div>
                <div class="col-sm-4 middle_image"><img src="<?php echo $images['AU_img_3']; ?>"></div>
                <div class="col-sm-4"><img src="<?php echo $images['AU_img_4']; ?>"></div>
              </div>
              <div class="col-sm-12 img-adjust-2"><img src="<?php echo $images['AU_img_5']; ?>"></div>
            </div>
          </div>
        </div>

        <div class="team_work">
          <h1>
            <p>We employ a certain type of individual at Wholesalebox:</p>
            <p>motivated, enterprising, and unafraid.</p>
              <br/>
            <p>Each person is proud to represent the Wholesalebox team. </p>
            <p>Ask around and we’ll tell you, this is the best job I’ve ever had.</p>
          </h1>
        </div>

        <div class="blue_line col-sm-12">  
          <img src="<?php echo $images['blue_line']; ?>">
        </div>

        <div class="members">
          <div class="row">
            
            <div class="col-sm-2">
              <h1>TEAM</h1>
            </div>
            
          </div>
        </div>                      
        <div class="member_image">
            <div class="col-sm-12">
              <div class="team_cofounder">
                <h3>Co-Founders</h3>
                  <?php foreach($team_cofounder as $member){ ?>
                    <div class="thumb scroll">
                      <div class="thumb-wrapper">
                        <img src=" <?php echo $member['photo']; ?>" alt="<?php echo $member['name']; ?>" />
                        <div class="thumb-detail">
                          <a href="<?php echo $member['linkedin']; ?>" target="_blank" ><img src="image/my_linkedin_profile_icon.png" />
                            <?php echo $member['name']; ?></a>
                          </a>        
                        </div>
                      </div>
                    </div>    
                  <?php } ?>
              </div>
              <div class="team_cofounder">
                <h3>Technical Team</h3>
                  <?php foreach($team_tech as $member){ ?>
                    <div class="thumb scroll">
                      <div class="thumb-wrapper">
                        <img src=" <?php echo $member['photo']; ?>" alt="<?php echo $member['name']; ?>" />
                        <div class="thumb-detail">
                          <a href="">
                            <?php echo $member['name']; ?><br/><br/>
                          </a>        
                        </div>
                      </div>
                    </div>    
                  <?php } ?>
              </div>
              <div class="team_cofounder">
                <h3>Operations Team</h3>
                  <?php foreach($team_operations as $member){ ?>
                    <div class="thumb scroll">
                      <div class="thumb-wrapper">
                        <img src=" <?php echo $member['photo']; ?>" alt="<?php echo $member['name']; ?>" />
                        <div class="thumb-detail">
                          <a href="">
                            <?php echo $member['name']; ?><br/><br/>
                          </a>        
                        </div>
                      </div>
                    </div>    
                  <?php } ?>
              </div>
            </div>
        </div>
  </div>
</div>
<style type="text/css">
.member_image div.team_cofounder div.thumb-detail a img{ width: 20px; height:20px; margin-bottom: 20%}
.member_image div.team_cofounder div.thumb-detail a{text-align: center; margin-top: 40%}
.member_image div.team_cofounder{width: 100%; float: left;}
div.tab-menu div.tab_list>a.active,div.tab-menu div.tab_list>a.active .glyphicon,div.tab-menu div.tab_list>a.active .fa{color:#000;border-bottom:6px solid #F03140!important; display: inline-block;}

.about_tab.list-group.active, .about_tab.list-group.active:focus, .about_tab.list-group.active{border-bottom:6px solid #F03140!important; display: inline-block;}
.about_tab.list-group.active, .home_tab.list-group.active:focus, .home_tab.list-group.active{display:block;border:none;}
.about_tab a:focus, .home_tab *:focus{noFocusLine:expression(this.onFocus=this.blur());text-decoration:none;outline:none;}
.wsb-life{text-align: center;width: 100%;float: left;}
.wsb-life h1{font-weight: 100;font-size: 30px;margin-top: 30px;}
.tab_list a h1{font-weight: 100;line-height: 1.2;color: #000; margin-left: 4%;}
.tab_list a{padding: 0px;}
.tab-text{background: #ffffff; padding: 10px;}
.tab-text img {width: 200px; margin-right: 10px;margin-top: 15px;}
.tab-text h2, .about_image, .blue_line{width: 100%;margin: auto;text-align: center;}
.tab-text h2{border-bottom: 4px solid #F03140; padding: 10px;}
.tab-text p{text-align: justify;}
.wsb-topbar{border-top:6px solid #ccc!important;}
.wsb-bottombar{border-bottom:6px solid #ccc!important; display: inline-block;}
.wsb-hover-light-grey{-webkit-transition:background-color .3s,color .15s,box-shadow .3s,opacity 0.3s;transition:background-color .3s,color .15s,box-shadow .3s,opacity 0.3s}
.members_image{border:  1px solid #000;height: 250px;padding: 0px;}
.team_work{width: 100%;float: left;}
.team_work h1{margin-top: 20px;font-weight: 400;}
.team_work p{text-align: center;}
.members {float: left; width: 100%; margin-bottom: 20px;}

.members h1{text-align: center;color: red;margin-top: 30px;font-size: 40px;font-weight: 100;}
.members img{margin-top: 30px;}
.imagecollage{float: left;background-color: #fff;margin-top: 20px;}
.middle_image{margin-top: 45px;padding: 0px;}
.img-adjust{margin-top: 15px;}
.img-adjust-2{padding: 15px 29px;}
.imagecollage img{width: 100%;float: left;}   
.about_us{background-color: #000;}
.about_us img{width: 100%;float: left;opacity: 0.6;}
.about_us_page div.headline p{font-size:35px;line-height: 1.2;margin-top: 30px;color: #000;}

/****************flip***************/
.member_image h1 {font-weight: bold;font-size: 26px;text-shadow: white 0px 1px 0, black 0 -1px 0;color: #444;letter-spacing: -1px;text-transform: uppercase;display: block;margin: 15px 0 10px 0;line-height: 1.2em;}  
#note {font-size:11px;color:#333;padding:10px;border:1px solid #b99f35;background:#f4eccb;border-radius: 3px;}  
.thumb {display:block;width:135px;height:200px;position:relative;margin-bottom:20px;margin-right:2px;float:left;padding: 0px !important;}  
.thumb-wrapper {display:block;width:100%;height:100%;}
.thumb img {width:100%;height:100%;position:absolute;display:block;}
.thumb .thumb-detail {display:block;width:100%;height:100%;position:absolute;background:#fff;font-family:arial;font-weight:bold;font-size:16px;}
.thumb .thumb-detail a {display:block;width:100%;height:100%;font-weight:bold;color:#333;text-decoration:none;
font-family: 'Open Sans', sans-serif;letter-spacing:-1px;padding:10px;font-size:16px;}   
  /*
  * Without CSS3
  */
.thumb.scroll {overflow: hidden;} 
.thumb.scroll .thumb-detail {bottom:-280px;}
  /*
  * CSS3 Flip
  */  
.thumb.flip {-webkit-perspective:800px; -moz-perspective:800px; -ms-perspective:800px;  -o-perspective:800px;  perspective:800px;}
.thumb.flip .thumb-wrapper {-webkit-transition: -webkit-transform 1s; -moz-transition: -moz-transform 1s; -ms-transition: -moz-transform 1s;
           -o-transition: -moz-transform 1s; transition: -moz-transform 1s;-webkit-transform-style: preserve-3d;
         -moz-transform-style: preserve-3d; -ms-transform-style: preserve-3d; -o-transform-style: preserve-3d; transform-style: preserve-3d;}
.thumb.flip .thumb-detail {-webkit-transform: rotateY(-180deg); -moz-transform: rotateY(-180deg); -ms-transform: rotateY(-180deg);
           -o-transform: rotateY(-180deg);  transform: rotateY(-180deg);}
.thumb.flip img,.thumb.flip .thumb-detail {-webkit-backface-visibility: hidden; -moz-backface-visibility: hidden;-ms-backface-visibility: hidden;
           -o-backface-visibility: hidden; backface-visibility: hidden;}
.thumb.flip .flipIt {-webkit-transform: rotateY(-180deg); -moz-transform: rotateY(-180deg);  -ms-transform: rotateY(-180deg); 
  -o-transform: rotateY(-180deg); transform: rotateY(-180deg);}
  
  </style>
  
  <script type="text/javascript">
  $(function () {
  

    if ($('html').hasClass('csstransforms3d')) {  
    
      $('.thumb').removeClass('scroll').addClass('flip');   
      $('.thumb.flip').hover(
        function () {
          $(this).find('.thumb-wrapper').addClass('flipIt');
        },
        function () {
          $(this).find('.thumb-wrapper').removeClass('flipIt');     
        }
      );
      
    } else {

      $('.thumb').hover(
        function () {
          $(this).find('.thumb-detail').stop().animate({bottom:0}, 500, 'easeOutCubic');
        },
        function () {
          $(this).find('.thumb-detail').stop().animate({bottom: ($(this).height() * -1) }, 500, 'easeOutCubic');      
        }
      );

    }
  
  });
</script>
<script type="text/javascript">
$(document).ready(function() {
    $("div.tab-menu>div.tab_list>a").click(function(e) {
        e.preventDefault();
        $(this).siblings('a.active').removeClass("active");
        $(this).addClass("active");
        var index = $(this).index();
        $("div.bhoechie-tab>div.tab-text").removeClass("active");
        $("div.bhoechie-tab>div.tab-text").eq(index).addClass("active");
    });
});

<?php /*if (isset($international_store)) { ?>

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

<?php }*/ ?>
</script>



</div>
<?php echo $footer; ?>