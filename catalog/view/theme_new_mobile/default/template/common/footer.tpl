<footer>
    <div class="container">
        <div class="row">
            <?php echo $language; ?>
            <?php if ($informations) { ?>
            <div class="col-sm-3">
                <h4><?php echo $text_information; ?></h4>
                <ul class="list-unstyled">

                    <li><a id="footer-aboutus" href="http://www.wholesalebox.in/about-us">About us</a></li>
                    <?php foreach ($informations as $information) { ?>
                    <li><a id="footer-<?php print $getSlugId;?>" href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
                    <?php } ?>
                    <?php if ($logged) { ?>
                    <?php if($is_dropshipper == 0){ ?>
                    <li><a href="<?php echo HTTP_SERVER; ?>dropshipper"><?php echo $text_dropshipper; ?></a></li>
                    <?php } ?>
                    <li id="footer-logout"><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
                    <?php }else { ?>
                    <li><a href="<?php echo HTTP_SERVER; ?>dropshipper"><?php echo $text_dropshipper; ?></a></li>
                    <li><a id="footer-login" href="javascript:;" data-toggle="modal" data-target="#login_verify_popup"><?php echo $text_login; ?>/<?php echo $text_register; ?></a></li>
                   <!--  <li><a id="footer-register" href="<?php echo $register; ?>"><?php echo $text_register; ?></a></li> 
                    <li><a  id=" footer-login" href="<?php echo $login; ?>"><?php echo $text_login; ?></a></li>-->
                    <li><a id="footer-career" href="<?php echo $careers; ?>"><?php echo $text_career;?></a></li>
                    <?php } ?>
                    <li><a id="footer-store-locator" href="http://www.wholesalebox.in/storelocator"><?php echo $text_storelocator; ?></a></li>
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

                            <a href="tel:+91 141 4049163"  id="footer-landline" class="phone"><i class="fa fa-phone change_color"></i>(+91) 141 - 4049163</a>
                            <br /><span class="ofc_time" style="padding:0px">(10am to 8pm)</span>

                        </li>

                        <li>
                            <a href="tel:+918696491521"  id="footer-whatsapp" class="whatsapp"><i class="fa fa-whatsapp"></i>+918696491521</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="footer4">
                    <h3>Logistics Partners</h3>
                    <div class = "col-sm-12 col-xs-12" style="padding:0px;">
                        <img src="http://cdnimages.net/img/gk.jpg" alt="Gati is our Logistics Partner">
                    </div>
                    <div class = "col-sm-12 col-xs-12" style="padding:0px;">
                        <img src="http://cdnimages.net/img/blue_dart.jpg" alt="BlueDart is our Logistics Partner">
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

if(!empty($show_redirect_popup)) { ?>
    <script type="text/javascript">

        $(".productInfoAjax, .productRelatedAjax").each(function() {
            $(this).removeClass("fancybox"); // unbind  to click action of fancybox
        });

        $(".addtocart_bottem, .addtocart, .new_addtocart").each(function() {
            $(this).prop('onclick',null).off('click'); // unbind click action on this button
        });

        $('.productInfoAjax, .addtocart_bottem, .addtocart, .productRelatedView, .new_addtocart').on('click',function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            var pId = $(this).data('product-id');

            var goToUrl = '<?php echo "http://".INTERNATIONAL_STORE_HOST."/index.php?route=product/product&product_id=";?>' + pId;

            $('.btn-redirect').attr('href',goToUrl);

            $("#indiaStoreOtherCountryAlert").modal({show: 'true',backdrop: 'static', keyboard: false});
            return false;
        });

    </script>
<?php } ?>

<script>
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


<!-- start chatbox -->
<div id="tawkchat">
    <i class="fa fa-comments"></i>
</div>

<div class="custom_chat_box">
    <div class="chat_box_heading_div">
        <p class="chat_box_heading">Online <i class="fa fa-angle-down"></i></p>
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
    #tawkchat{
        background: #17319f;
        position: fixed;
        right: 15px;
        bottom: 15px;
        cursor: pointer;
        color: #fff;
        padding: 9px 11px;
        border-radius: 50%;
        background-attachment: fixed;
        box-shadow: 0 4px 5px -1px rgba(0,0,0,0.5);
        z-index: 999;
    }
    #tawkchat i{
        font-size: 21px;
    }

    
    
    
    .custom_chat_box{
        width: 100%;
        position: fixed;
        z-index: 999;
        top: 0;
        font-size: 15px;
        background: #fff;
        display: none;
        min-height: 100%;
    }
    .chat_box_heading_div{
        background-color: #17319f;
        color: #fff;
        padding: 6px 10px;
        cursor: pointer;
    }
    .chat_box_heading_div i {
        float: left;
        font-weight: bold;
        margin: 4px;
        font-size: 25px;
    }
    .chat_box_heading{
        padding: 0px;
        font-size: 22px;
        margin: 0;
        text-align: center; 
    }
    .call_us{
        margin: 0;
    }
    .chat_box_content{
        padding: 10px 10px 0 10px;
        background: #fff;
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
    /*
    li.chat_here{
        border:none !important;
    }
    */
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
        visibility: visible;
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

$("#tawkchat").click(function(){
    $('.custom_chat_box').show();
})


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

$(".chat_box_heading_div i").click(function(){
    $(".chat_box_content ul.list-unstyled").hide();
    $(".chat_box_content li.dropdown").removeClass("active");
    $(".chat_box_content li.dropdown").css("border-bottom", "1px solid #ccc");
    $(".chat_box_content li.dropdown .dropdown_arrow i").removeClass("fa-angle-down"); 
    $('.callback_request_input').val('');
    $('.chat_box_content .error_msg').html(''); 
    
    $('.custom_chat_box').hide();
    
});
$(".chat_here").click(function(){
     $(".custom_chat_box").hide();
     //$(".custom_chat_box").toggle();
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
    $(document).ready(function () {
        $(".app_download_banner").click(function(){
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
<script  src="catalog/view/theme_new_mobile/default/javascript/fancybox/jquery.fancybox.pack.js" type="text/javascript">
</script>
<script  src="catalog/view/theme_new_mobile/default/javascript/expand-search/modernizr.custom.js" type="text/javascript">
</script>
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



<?php echo $login_popup; ?>


</body></html>
