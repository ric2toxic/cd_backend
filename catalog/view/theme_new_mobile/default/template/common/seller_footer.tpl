<footer>
    <div class="container">
        <div class="row">
            <?php if ($informations) { ?>
            <div class="col-sm-3">
                <h4><?php echo $text_information; ?></h4>
                <ul class="list-unstyled">
                    <li><a href="http://www.wholesalebox.in/about-us">About us</a></li>
                    <?php foreach ($informations as $information) { ?>
                    <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
                    <?php } ?>
                    <?php if ($logged) { ?>
                    <?php if($is_dropshipper == 0){ ?>
                    <li><a href="<?php echo HTTP_SERVER; ?>dropshipper"><?php echo $text_dropshipper; ?></a></li>
                    <?php } ?>
                    <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
                    <?php }else { ?>
                    <li><a href="<?php echo HTTP_SERVER; ?>dropshipper"><?php echo $text_dropshipper; ?></a></li>
                    <li><a href="<?php echo $register; ?>"><?php echo $text_register; ?></a></li>
                    <li><a href="<?php echo $login; ?>"><?php echo $text_login; ?></a></li>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>
            <!-- <div class="col-sm-2 hidden-xs">
              <h5><?php // echo $text_service; ?></h5>
              <ul class="list-unstyled">
                <li><a href="<?php // echo $contact; ?>"><?php // echo $text_contact; ?></a></li>
                <li><a href="<?php // echo $return; ?>"><?php // echo $text_return; ?></a></li>
                <li><a href="<?php // echo $sitemap; ?>"><?php // echo $text_sitemap; ?></a></li>
              </ul>
            </div> -->
            <!--
            <div class="col-sm-3">
              <h5><?php // echo $text_extra; ?></h5>
              <ul class="list-unstyled">
                <li><a href="<?php // echo $manufacturer; ?>"><?php // echo $text_manufacturer; ?></a></li>
                <li><a href="<?php // echo $voucher; ?>"><?php // echo $text_voucher; ?></a></li>
                <li><a href="<?php // echo $affiliate; ?>"><?php // echo $text_affiliate; ?></a></li>
                <li><a href="<?php // echo $special; ?>"><?php // echo $text_special; ?></a></li>
              </ul>
            </div>
            -->
            <div class="col-sm-2 hidden-xs">
                <h5><?php echo $text_account; ?></h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
                    <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
                    <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
                </ul>
            </div>

            <div class="col-sm-4">
                <div class="footer4">
                    <h2>Helpline Number</h2><br />
                    <ul>
                        <li>

                            <a href="tel:+91 141 4049163" class="phone"><i class="fa fa-phone change_color"></i>(+91) 141 - 4049163</a>
                            <br /><span class="ofc_time" style="padding:0px">(10am to 8pm)</span>

                        </li>

                        <li>
                            <a href="tel:+918696491521" class="whatsapp"><i class="fa fa-whatsapp"></i>+918696491521</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="footer4">
                    <h3>Logistics Partners</h3>
                    <div class = "col-sm-12 col-xs-4" style="padding:0px;">
                        <img src="image/fedex.png" alt="Fedex is our Logistics Partner">
                    </div>
                    <div class = "col-sm-12 col-xs-8" style="padding:0px;">
                        <img src="image/bluedart.png" alt="BlueDart is our Logistics Partner">
                    </div>
                </div>
            </div>

        </div>
        <br />
        <div class="alexa">
            <a href="http://www.alexa.com/siteinfo/wholesalebox.in"><script type="text/javascript" src="http://xslt.alexa.com/site_stats/js/t/a?url=wholesalebox.in"></script></a>
        </div>

        <hr>


        <p><?php echo $powered; ?></p>
    </div>
</footer>

<script type="text/javascript">

    /* affix the navbar after scroll below header*/
    /*$('#sticky').affix({
     offset: {
     top: $('header').height()-$('#sticky').height()+150
     }
     }); */

    /* highlight the top nav as scrolling occurs8*/
    $('body').scrollspy({ target: '#sticky' })

    /* smooth scrolling for scroll to top
     $('.scroll-top').click(function(){
     $('body,html').animate({scrollTop:0},1000);
     })

     /* smooth scrolling for nav sections
     $('#nav .navbar-nav li>a').click(function(){
     var link = $(this).attr('href');
     var posi = $(link).offset().top;
     $('body,html').animate({scrollTop:posi},700);
     });


     */






</script>
<script>
    $(document).ready(function () {
        $('#column-left').removeClass('hidden-xs')
        $('button.navbar-toggle').click(function() {
            $('.navmenu').offcanvas('toggle');
        });

        $( "#filter_icon, div.filter_click" ).click(function() {

            $( "div.filter_box" ).slideToggle( "slow" );
            $( "div.filter_box").removeClass('hidden-xs');

        });

        $("a#filter_option").fancybox({
            'hideOnContentClick': true
        });
        $("a#sortby").fancybox({
            'hideOnContentClick': true
        });
        $(".fancybox").click(function(e){
            e.preventDefault();
            var $href = $(this).attr('href');
            var popup_string = '&';
            var find_query_string = $href.indexOf("?");
            if(find_query_string > 0) {
                popup_string = '&';
            }
            $href = $href+popup_string+'popup=true';

            //+'?popup=true';
            $.fancybox({

                hideOnContentClick: true,
                maxWidth: 768,
                width:'100%',
                padding:0,
                type: 'iframe',
                href:$href,
                centerOnScroll: true,
                autoScale: true,
                iframe: {
                    preload: false // fixes issue with iframe and IE
                },
                helpers: {
                    overlay: {
                        locked: true,
                        closeClick: false
                    }
                }
            });
        });
        // $( "div.sort_by_click" ).click(function() {


        // $( "div.sort_by_options" ).slideToggle( "slow" );
        //$( "div.sort_by_options").removeClass('hidden-xs');

        // });

        <?php if($is_home != 1) { ?>
            $('#category_dropdown').click(function(){
            $(this).children('i').toggleClass('fa-caret-down, fa-caret-up');
            $('ul.nav').toggle(1);
        });
            <?php } ?>

        //get cookie
        //show only if cookie does not exist
        /*
        if(getCookie('show_ad') != 1) {
          //alert('dddddd');
          $('.ad_sidebar').css("display", "block");
          setTimeout(function () {

              $("#sidebar_banner").animate({left: '-200px'}, "slow")
          }, 1000);

          $("#hide").click(function () {
              $("#sidebar_banner").hide("slow");
              $('.ad_sidebar').hide();
              createCookie("show_ad", 1, 2);
              //create cookie
          });

          $(".close_ad_btn").click(function () {
              $("#sidebar_banner").hide("slow");
              $('.ad_sidebar').hide();
              createCookie("show_ad", 1, 2);
              //create cookie
          });
      }
*/

  });
    $('a.dropdown-mobile').click(function(e){
        e.preventDefault();
        $('ul.dropdown-mobile-content').hide('fast');
        $(this).next('ul.dropdown-mobile-content').toggle('fast');

    });

</script>
<?php if($route == 'product/product'){ ?>
<script>
    $(document).ready(function () {

        var owl = $("#owl-related");
        owl.owlCarousel({
            autoPlay : 3000,
            itemsMobile: [479, 1],
            goToFirstSpeed : 3000,
            transitionStyle:"slide",
            lazyLoad : true,
            rewindSpeed : 3000,
            pagination: false,
            singleItem:true
        });

        // Custom Navigation Events
        $(".owl-related-next").click(function(){
            owl.trigger('owl.next');
        })
        $(".owl-related-prev").click(function(){
            owl.trigger('owl.prev');
        })


    });


</script>

<?php } ?>

<?php


/***
 * Start Home Page PopUp Code
 * ***/
?>
<a href="#popUpForm" id="OpenPop" class="" style="display:none;">&nbsp;</a>
<div id="popUpForm" class="pop_up-outer" style="display:none;">

    <div class="top_heading">
        <h1>SHOPS BUY ONLINE <br />
            @FACTORY PRICE</h1>
    </div>
    <div class="con_text">ENTER YOUR MOBILE BELOW TO GET STARTED</div>
    <br>
    <form name="MobileHomePopUp" id="MobileHomePopUp" onsubmit="secondpopup(false)" action="javascript:void(0);" method="post">

        <div>
            <input type="text" id="input-telephone" name="telephone" placeholder="ENTER YOUR MOBILE NUMBER" class="form-control input_mobile_number_class" />
            <input type="hidden" id="input-hidden" name="hidden_text" value="mob" />
            <input type="submit" onclick="return phonenumber(document.MobileHomePopUp.telephone)"  value="ENTER" class="btn-primary popup_btn" />
        </div>
    </form>
    <br /><br />
    <a href="javascript:void(0);" class="skippopup"><span class="SkipPage">Skip</span></a>
</div>
<?php if($is_home){ ?>
<div id="refer_btn_container">
    <!--    <a href="http://www.wholesalebox.in/refer-and-earn"><i class="fa fa-gift gift"></i> Deal Of The Day</a> -->

    <a href="http://www.wholesalebox.in/refer-and-earn" style="color: white;"><i class="fa fa-gift" style="color: white; font-size: 20px;"></i> Refer & Earn</a>
</div>

<div id="discount_shipping_popup_show">
    <a href="#" style="color: white;" class="offer_popup"><i class="fa fa-gift" style="color: white; font-size: 20px;"></i> Offers**</a>
</div>

<?php } ?>

<!-- discount popups -->

<div id="discount_shipping_popup">
    <i class="fa fa-times close_ship_popup discount_popup1"></i><br />
    <span style="font-size: 12px; color: whitesmoke;">PREPAID ORDERS</span><hr style="margin-top:2px;" />
    2% DISCOUNT
    <div id="or_div">OR</div>
    <div id="free_shipping_popup">
        FREE SHIPPING ON ORDERS ABOVE RS. 10000
        <br /><br />
    </div>
</div>

<!-- discount popups -->

<script type="text/javascript">
    //script for home page popup

    $(document).ready(function() {

    /*  $(".SkipPage").click(function () {
     $("#input-telephone").val("skiping...");
     $("#input-hidden").val("skip");
     ga('send', {
     hitType: 'event',
     eventCategory: 'MobilePopup',
     eventAction: 'Skip',
     eventLabel: 'Skiped mobile number popup on Mobile'
     });
     secondpopup(false);

     });


     $("a#OpenPop").fancybox({
     padding: 10 ,

     helpers : {
     overlay : {closeClick: false}
     },
     // closeClick  : false, // prevents closing when clicking INSIDE fancybox
     'closeBtn' : false
     });
     });//dom ready


     function containsAny(str, substrings)
     {
     for (var i = 0; i != substrings.length; i++) {
     var substring = substrings[i];
     if (str.indexOf(substring) != - 1) {
     return substring;
     }
     }
     return null;
     }


     function phonenumber(inputtxt)
     {
     var flag = 1;
     var phoneno = /^\d{10}$/;
     var mobile = inputtxt.value;
     var res = mobile.charAt(0);
     var result = containsAny(res, ["9", "8", "7"]);

     var flc = (mobile.match(new RegExp(res, "g")) || []).length;
     if(flc > 9){
     flag = 0;
     }
     if(!result){
     flag = 0;
     }
     if(flag == '1'){
     if(inputtxt.value.match(phoneno))
     {
     return true;
     }
     else
     {
     $("div.removePopError").remove();
     $("div#popUpForm form").append("<div class='removePopError' style='color:#f03140;'>Please Enter Valid Mobile Number</div>");
     return false;
     }
     }
     else
     {
     $("div.removePopError").remove();
     $("div#popUpForm form").append("<div class='removePopError' style='color:#f03140;'>Please Enter Valid Mobile Number</div>");
     return false;
     }
     }
</script>

<?php if($pop > 0){ ?>
<script>
    $(document).ready(function(){
        $("a#OpenPop").trigger("click");
    });
</script>
<?php } ?>
<?php
  /***
 * End Home Page PopUp Code
 * ***/
?>
<?php if($route == 'product/category' || $route == 'product/search'){ ?>

<script type="text/javascript">
    $(document).ready(function() {
        $(window).scroll(function() {
            if ($(this).scrollTop()) {
                $('#scroll_to_top:hidden').stop(true, true).fadeIn();
            } else {
                $('#scroll_to_top').stop(true, true).fadeOut();
            }
        });

        $("a[href='#top']").click(function () {
            $("html, body").animate({scrollTop: 0}, "slow");
            return false;
        });
    });
</script>
<a href="#top" id="scroll_to_top" style="display: none;">&nbsp;</a>

<?php } ?>

<script type="text/javascript">
    $(document).ready(function(){
        $(".close_ship_popup").click(function(){
            $("#discount_shipping_popup").hide("slide", {
                direction: "left"
            }, 500);
        });

        $("#discount_shipping_popup_show").click(function(){
            $("#discount_shipping_popup").show("slide", {
                direction: "left"
            }, 500);

        });
    });
</script>

<div class="global-bg-layer"></div>
<div class="global-ajax-loader"></div>

<!-- Browser notifications
<script src="https://notifi.io/push/notifier/142/loader.js"></script> -->
<!-- Pixel -->
<script type="text/javascript" src="//js.m-bazaar.in/wholesalebox.min.js" ></script>

</body></html>
