<?php echo $login_popup; ?>
<footer>
    <div class="container">
        <div class="row">
            <?php echo $language; ?>
            <?php if ($informations) { ?>
            <div class="col-sm-3">
                <h4><?php echo $text_information; ?></h4>
                <ul class="list-unstyled">

                    <li><a href="http://www.wholesalebox.in/about-us" id="footer-aboutus">About us</a></li>
                    <?php foreach ($informations as $information) { ?>
                    <li><a href="<?php echo $information['href']; ?>" id="footer-<?php print $getSlugId;?>"><?php echo $information['title']; ?></a></li>
                    <?php } ?>
                    <?php if ($logged) { ?>
                    <?php if($is_dropshipper == 0){ ?>
                    <li><a href="<?php echo HTTP_SERVER; ?>dropshipper"><?php echo $text_dropshipper; ?></a></li>
                    <?php } ?>
                    <li id="footer-logout"><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
                    <?php }else { ?>
                    <li><a href="<?php echo HTTP_SERVER; ?>dropshipper"><?php echo $text_dropshipper; ?></a></li>
                    <li><a id="footer-login" href="javascript:;" data-toggle="modal" data-target="#login_verify_popup"><?php echo $text_login; ?>/<?php echo $text_register; ?></a></li>
                    <!-- <li><a id="footer-register" href="<?php echo $register; ?>"><?php echo $text_register; ?></a></li>
                    <li><a  id=" footer-login" href="<?php echo $login; ?>"><?php echo $text_login; ?></a></li> -->
                    <li><a  id="footer-career" href="<?php echo $careers; ?>"><?php echo $text_career;?></a></li>
                    <?php } ?>
                    <li><a  id="footer-store-locator" href="http://www.wholesalebox.in/storelocator"><?php echo $text_storelocator; ?></a></li>
                </ul>
            </div>
            <?php } ?>

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

                            <a href="tel:+91 141 4049163" id="footer-landline" class="phone"><i class="fa fa-phone change_color"></i>(+91) 141 - 4049163</a>
                            <br /><span class="ofc_time" style="padding:0px">(10am to 8pm)</span>

                        </li>

                        <li>
                            <a id="footer-whatsapp" href="tel:+918696491521" class="whatsapp"><i class="fa fa-whatsapp"></i>+918696491521</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="footer4">
                    <h3>Logistics Partners</h3>
                    <div class = "col-sm-12 col-xs-12" style="padding:0px;">
                         <img src="<?php echo $footer_images['gk']; ?>" alt="Gati is our Logistics Partner">
                    </div>
                    <div class = "col-sm-12 col-xs-12" style="padding:0px;">
                        <img src="<?php echo $footer_images['blue_dart']; ?>" alt="BlueDart is our Logistics Partner">
                    </div>
                </div>
            </div>

        </div>
        <br />
        <div class="alexa">
            <a href="http://www.alexa.com/siteinfo/wholesalebox.in"><script type="text/javascript" src="http://xslt.alexa.com/site_stats/js/t/a?url=wholesalebox.in"></script></a>
        </div>

        <div class="row popular_tags">
            <div class="col-sm-3">
                <h5> <?php echo $text_popular_tag_lable; ?> </h5>
            </div>
            <div class="col-sm-9">
                <div class="popular-tags-list">
                    <ul>
                        <?php if(isset($popular_tags)) { ?>
                        <?php foreach($popular_tags as $tags) { ?>
                        <li>
                            <?php if(!empty($tags['link'])) { ?>
                            <a href="<?php echo $tags['link'];?>" title="<?php echo $tags['text'];?>">
                                <?php echo $tags['text'];?>
                            </a>
                            <?php }else{ ?>
                            <a href="<?php echo $tags['href'];?>" title="<?php echo $tags['text'];?>">
                                <?php echo $tags['text'];?>
                            </a>
                            <?php } ?>
                        </li>
                        <?php } ?>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <hr>


        <p><?php echo $powered; ?></p>
    </div>
</footer>



<!-- indiaStoreOtherCountryAlert Modal -->
<?php
if(!empty($redirect_popup))
    echo $redirect_popup;
?>
<script type="text/javascript">

    var alertSwitchStore = '<?php echo isset($show_redirect_popup)?$show_redirect_popup:false;?>';
    if(alertSwitchStore){
        $("#indiaStoreOtherCountryAlert").modal({show: 'true',backdrop: 'static', keyboard: false});
    }

    $(document).ready(function () {

        /* highlight the top nav as scrolling occurs8*/
        $('body').scrollspy({ target: '#sticky' })

        $('#column-left').removeClass('hidden-xs')
        $('button.navbar-toggle').click(function() {
            $('.navmenu').offcanvas('toggle');
        });

        $( "#filter_icon, div.filter_click" ).click(function() {

            $( "div.filter_box" ).slideToggle( "slow" );
            $( "div.filter_box").removeClass('hidden-xs');

        });

        /*$("a#filter_option").fancybox({
            'hideOnContentClick': true
        });*/
        $("a#sortby").fancybox({
            'hideOnContentClick': true
        });
        $(".fancybox").click(function(e){
            e.preventDefault();
            var $href = $(this).attr('href');
           // history.replaceState(null, null, 'women-kurtis#!test');
           // location.href  = $href;
            var popup_string = '&';
            var find_query_string = $href.indexOf("?");
            if(find_query_string > 0) {
                popup_string = '&';
            }
            $href = $href+popup_string +'popup=true';

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


        <?php if($is_home != 1) { ?>
            $('#category_dropdown').click(function(){
                $(this).children('i').toggleClass('fa-caret-down, fa-caret-up');
                $('ul.nav').toggle(1);
            });
        <?php } ?>

        $('a.dropdown-mobile').click(function(e){
            e.preventDefault();
            $('ul.dropdown-mobile-content').hide('fast');
            $(this).next('ul.dropdown-mobile-content').toggle('fast');

        });

    });


</script>


<?php


/***
 * Start Home Page PopUp Code
 * ***/
?>
<a href="#popUpForm" id="OpenPop" class="" style="display:none;">&nbsp;</a>
<a href="#" id="quality_popup" class="" style="display:none;">&nbsp;</a>
<div id="popUpForm" class="pop_up-outer" style="display:none;">

    <div class="top_heading">
        <h1>SHOPS BUY ONLINE <br />
            @FACTORY PRICE</h1>
    </div>
    <div class="con_text">To get latest design and updates, enter your mobile number </div>
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
<?php  if($is_home && isset($discount_shipping_popup) && $discount_shipping_popup == 1){ ?>

<?php /* ?>
<div id="refer_btn_container">
    <!--    <a href="http://www.wholesalebox.in/refer-and-earn"><i class="fa fa-gift gift"></i> Deal Of The Day</a> -->

    <a href="http://www.wholesalebox.in/refer-and-earn" style="color: white;"><i class="fa fa-gift" style="color: white; font-size: 20px;"></i> Refer & Earn</a>
</div>
<?php */ ?>

<div id="discount_shipping_popup_show">
    <a href="#" style="color: white;" class="offer_popup"><i class="fa fa-gift" style="color: white; font-size: 20px;"></i> Offers**</a>
</div>



<!-- discount popups -->

<div id="discount_shipping_popup">
    <i class="fa fa-times close_ship_popup discount_popup1"></i><br />
    <!-- <span style="font-size: 12px; color: whitesmoke;">PREPAID ORDERS</span><hr style="margin-top:2px;" />-->
    <div style="font-size: 18px; color: #000!important; font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 10px;">PREPAID ORDERS</div>
    <div style="margin-top:10px; color: #000;"><?php echo $text_two_percent_discount; ?></div>
    <div id="free_shipping_popup">
        <?php echo $text_three_percent_discount; ?>
        <br /><br />
    </div>
</div>
<?php } ?>
<!-- discount popups -->

<script type="text/javascript">


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

<script>
    $(document).ready(function(){
        $(".SearchEffect").click(function(){

            //$('#searchInput').toggle('fast');

            if($('#searchInput').is(":visible")){ 
				$(this).removeClass("material-search-cross-custom").addClass("material-search-custom");
                $('#searchInput').hide('fast');
                //$(this).removeClass('clear').addClass('search').html('search');
                $(this).removeClass('clear').addClass('search').html('');
                $("i.heart").show();
                $("img.logo").show();

            }

            if($('#searchInput').is(":hidden")){ 
				$(this).removeClass("material-search-custom").addClass("material-search-cross-custom");
				
                $('#searchInput').show('fast');
                //$(this).removeClass('search').addClass('clear').html('clear');
                $(this).removeClass('search').addClass('clear').html('');
                $("img.logo").hide();
            }

        });

        $('#store_switch_filter').on('click', function (argument) {
            $.ajax({
                url : 'index.php?route=common/header/getStoreSwitchNew',
                dataType: 'json',

                beforeSend: function () {
                    $('body').removeClass('loaded').addClass('loading');
                },
                success: function (json) {
                    location.reload();

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }

            });
        });

    });

</script>
<div class="global-bg-layer"></div>
<div class="global-ajax-loader"></div>

<?php if($is_home == 1 ) { ?>
<div class="container-fluid app_download_banner">
    <div class="row">
        <div class="col-xs-7 nopadding">
            App loved by more than 20,000+ shops
        </div>
        <div class="col-xs-4 nopadding">
            <button class="btn btn-primary">DOWNLOAD</button>
        </div>

    </div>
</div>

<script type="text/javascript">
    var isIOS = "<?php echo CONFIG_IS_IOS;?>";
    $(document).ready(function () {
        $(".app_download_banner").click(function(){

            if(isIOS == 1)
                window.open("https://itunes.apple.com/us/app/wholesalebox/id1254820324?mt=8");
            else
                window.open("https://play.google.com/store/apps/details?id=in.wholesalebox");
        });
       device = getMobileOperatingSystem();
        if( device != 'Android'){
            $(".app_download_banner").css('display', 'none');
        }
    });
</script>
<?php }  ?>

<script type="text/javascript">
    $("#searchInput").autocomplete({
        source: 'index.php?route=common/search/autoComplete',
        dataType: "json",
        success: function( data ) {
            response( $.map( data, function( item ) {

                return {
                    label: item['label'],
                    value: item['value']
                }
            }));
        },

        autoFocus: false,
        select: function( event, ui ) {

            $('input[name=\'search\']').val(ui.item.label);
        },
        open: function(event, ui) {
            $(".ui-autocomplete").addClass('dropdown-menu');
            $(".ui-autocomplete").css("z-index", 1000);
        }
    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
        return $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( "<a>" + item.label +"</a>" )
                .appendTo( ul );
    };

</script>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700,900 +' rel='stylesheet' type='text/css'>

<script  src="catalog/view/javascript/jquery/jquery-ui.min.js" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery.validate.min.js" type="text/javascript"></script>
<script  src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script  src="catalog/view/theme_new_mobile/default/javascript/jasny-bootstrap/js/jasny-bootstrap.min.js" type="text/javascript"></script>
<script  src="catalog/view/theme_new_mobile/default/javascript/fancybox/jquery.fancybox.pack.js" type="text/javascript"></script>
<script  src="catalog/view/theme_new_mobile/default/javascript/expand-search/modernizr.custom.js" type="text/javascript"></script>
<script  src="catalog/view/javascript/common.js" type="text/javascript"></script>
<?php foreach ($scripts as $script) { ?>
<script  src="<?php echo $script; ?>" type="text/javascript"></script>
<?php } ?>
<?php foreach ($additional_scripts as $additional_script) {
    echo $additional_script;
} ?>
<?php /* ?>
<!-- Browser notifications
<script src="https://notifi.io/push/notifier/142/loader.js"></script> -->
<!-- Pixel
<script type="text/javascript" src="//js.m-bazaar.in/wholesalebox.min.js" ></script>-->
<?php */ ?>
<script  src="catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js" defer type="text/javascript"></script>
<?php if($is_home== 1){ ?>
<script async src="https://cdn.ampproject.org/v0.js"></script>
<script>self.AMP_CONFIG={"canary":0,"amp-ios-overflow-x":1,"amp-experiment":1,"pan-y":1,"expAdsenseA4A":0.1,"expDoubleclickA4A":0.1,"a4aProfilingRate":1,"amp-form":0,"form-submit":0,"ad-type-custom":1,"amp-scrollable-carousel":1,"amp-app-banner":1,"amp-inabox":1,"ios-embed-wrapper":1,"amp-apester-media":1,"amp-accordion-session-state-optout":1,};
</script>

<script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
<?php } ?>

</body></html>
