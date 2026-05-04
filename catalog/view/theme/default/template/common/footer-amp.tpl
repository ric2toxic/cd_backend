<footer>

    <div class="container nopadding">
        
        <div class="row">
            <div class="col-sm-12 advertise_article">
                <marquee onMouseOver="this.stop();" OnMouseOut="this.start();">
                    <?php echo $home_advertise_article;?>
                </marquee>
            </div>
        </div>

    </div>
    <hr class="line_under_pro_boxes">


    <div class="container">
        <div class="row">
            <?php if ($informations) { ?>
            <div class="col-sm-3">
                <h5><?php echo $text_information; ?></h5>
                <ul class="list-unstyled">
                    <li><a href="http://www.wholesalebox.in/about-us"  id="footer-aboutus"><?php echo $text_about_us; ?></a></li>
                    <li><a href="http://www.wholesalebox.in/contact-us" id="footer-contactus"><?php echo $text_contact_us; ?></a></li>
                                       
                    <?php foreach ($informations as $information) {
                        $getSlugId = trim(parse_url($information['href'], PHP_URL_PATH), '/');
                     ?>
                    <li><a href="<?php echo $information['href']; ?>" id="footer-<?php print $getSlugId;?>"><?php echo $information['title']; ?></a></li>
                    <?php } ?>
                    <?php if ($logged) { ?>
                    <?php if($is_dropshipper == 0){ ?>
                    <li><a href="<?php echo $dropshipper; ?>"><?php echo $text_dropshipper; ?></a></li>
                    <?php } ?>
                    <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
                    <?php }else { ?>
                    <li><a href="<?php echo $dropshipper; ?>"><?php echo $text_dropshipper; ?></a></li>

                    <li><a id="footer-login" href="<?php echo $login; ?>"><span class="hidden-xs hidden-sm hidden-md"><?php echo $text_login_signup; ?></span></a></li>

                    <li><a href="<?php echo $careers; ?>" id="footer-career"><?php echo $text_career;?></a></li>
                    <li><a href="http://www.wholesalebox.in/storelocator" id="footer-store-locator"><?php echo $text_storelocator; ?></a></li>

                    <?php } ?>
                    <li><span class="exclusive"><?php echo $text_exclusive;?></span></li>
                    <li class="display_none text_exclusive"><input class="col-sm-6 exclusive_voucher_code" type="text" name="voucher_code" placeholder="Enter code" value=""/><span class="col-sm-3 show_exclusive" style="" id="check_exclusive_voucher_code">Go</span></li>
                    <br>
                    <br>
                    <li class="display_none invalid_exclusive_voucher_code"> <?php echo $text_invalid_exclusive_voucher_code; ?> </li>
                </ul>
            </div>
            <?php } ?>

            <div class="col-sm-3">
                <div class="footer4">
                    <h5>Helpline Number</h5>
                    <ul>
                        <li>

                            <span class="phone"><i class="fa fa-phone"></i>(+91) 141 - 4049163</span>
                            <br /><span class="ofc_time" style amp-custom="padding:0px">(10am to 8pm)</span>

                        </li>

                        <?php
                         if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) { ?>
                        <li><span class="whatsapp"><i class="fa fa-whatsapp"></i>+919116134795</span></li>
                          <?php } else { ?>
                          <li><span class="whatsapp"><i class="fa fa-whatsapp"></i>+918696491521</span></li>
                        <?php } ?>
                        <li>
                            <span class="whatsapp"><i class="fa fa-whatsapp"></i>+919982330835</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="footer4">
                    <h5>Logistics Partners</h5>
                    <div class = "col-sm-12 col-xs-8" style="padding:0px;">
                        <img src="image/fedex.png" alt="FedEx is our Logistics Partner">
                    </div>
                    <div class = "col-sm-12 col-xs-4" style="padding:0px;">
                        <img src="image/gatikwe.png" alt="Gati is our Logistics Partner">
                    </div>
                    <div class = "col-sm-12 col-xs-8" style="padding:0px;">
                        <img src="image/DTDC.png" alt="DTDC is our Logistics Partner">
                    </div>
                    <div class = "col-sm-12 col-xs-8" style="padding:0px;">
                        <img src="image/bluedart.png" alt="BlueDart is our Logistics Partner">
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="trust_seal"></div>
                <b>Scan QR code to get the link</b>
                <div class="QR_code"></div>
            </div>

        </div>
        <div id="link_popup" class="link_popup">
            <button class="appLink_close_popup"><i class="fa fa-close"></i></button>
            <p></p>
        </div>
        <div class="row">
            <div class="col-sm-3 social_profiles">
                <ul>
                    <li>
                        <a class="sf_facebook" id="footer-social_1" href="https://web.facebook.com/wholesalebox1" target="_blank"></a>
                    </li>
                    <li><!-- google plus correct link -->
                        <a class="sf_googleplus" id="footer-social_2" href="https://plus.google.com/u/0/111602807217891500299" target="_blank"></a>
                    </li>
                    <?php /* ?> <li>
                        <a class="sf_youtube" id="footer-social_3" href="https://web.facebook.com/wholesalebox1" target="_blank"></a>
                    </li>
                    <?php */ ?>

                </ul>
            </div>
            <div class="col-sm-6 ">
                <div class="ic_payment_methods">
                </div>
                <div class="get_app_link" id="getAppLink">
                    <label><?php echo $text_app_label;?></label>
                    <div class="get_link">
                        <input type="text" value="" name="get_app_link" placeholder="<?php echo $entry_app_mobile;?>" maxlength="10">
                        <button id="download-app-landing" type="button" id="get_applink_button" class="get_applink_button"><?php echo $label_button_app; ?></button>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 app_link" style="padding:10px 0px;">
                <p><?php echo $text_cashback;?></p>
                <a href="https://play.google.com/store/apps/details?id=in.wholesalebox" target="_blank">
                    <img src="image/google-play-android-app.svg" alt="Android app on google play" width="130px">
                </a>
                <a href="https://itunes.apple.com/us/app/wholesalebox/id1254820324?mt=8" target="_blank">
                    <img src="image/ios_download.svg" alt="ios app on app store" width="130px" class="pull-right">
                </a>
            </div>


        </div>
        <div class="row popular_tags">
            <div class="col-sm-12 col-lg-12">
                <h5> <?php echo $text_popular_tag_lable; ?> </h5>
            </div>
            <div class="col-sm-12 col-lg-12">
                <div class="popular-tags-list">
                    <ul>
                        <?php if(isset($popular_tags)) { ?>
                        <?php foreach($popular_tags as $tags) { ?>
                        <li>
                            <?php if(!empty($tags['link'])) { ?>
                            <a href="<?php echo $tags['link'];?>" id="footer-<?php print $tags['text'];?>" title="<?php echo $tags['text'];?>">
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
    </div>
    <div class="footer_copyright">
        <div class="container ">
            <div class="row">
                <div class="col-sm-12">
                    <p><?php echo $powered; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- discount popups -->
    <?php
    if(isset($discount_shipping_popup) && $discount_shipping_popup == 1 && $is_home== 1){ 
        $display_content= 'none';
        $display_icon= 'none';
        
    }else{
        $display_content = 'block';
        $display_icon= 'none';
    }
        ?>
    
    <?php  if(isset($discount_shipping_popup) && $discount_shipping_popup == 1 ){ ?>
<!--     <div id="discount_shipping_popup" style="background-color:transparent; min-height: 240px; display: <?php echo $display_content;?>;">
    <div class="discount_ship_popup" style="display: <?php echo $display_content;?>; border: 2px dotted #CD061B;">
    
        <i class="fa fa-times close_ship_popup discount_popup1"></i>
        <p>PREPAID ORDERS</p>
        <div id="prepaid_discount" style="display: block;">
            <span><?php echo $text_two_percent_discount; ?></span>
        </div>
        <hr>
        <div class="two_percent">
        <span><?php echo $text_three_percent_discount; ?></span>
        </div> -->
        
          <?php /*
<!--         <div id="or_div">OR</div>
        <div id="free_shipping_popup">

            <span>FREE SHIPPING ON ORDERS ABOVE RS. 10000</span><br />
        </div>
 -->           <?php   */ ?>
<!--     </div>
  </div>
    <div id="discount_shipping_popup_show" style="display: <?php echo $display_icon;?>;">
        <i class="fa fa-gift" style="margin-top:28%; font-size:24px; margin-left:53%;"></i>
    </div> -->
    <?php }  ?>
    <!-- discount popups -->

</footer>

<?php echo $login_popup; ?>


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

    $(document).ready(function() {
        
        /* highlight the top nav as scrolling occurs8*/
        $('body').scrollspy({target: '#sticky'});
    });

</script>
<script>
    $(document).ready(function () {


<?php if($is_home != 1) { ?>
      $('#category_dropdown').click(function(){
          $(this).children('i').toggleClass('fa-caret-down, fa-caret-up');
           $('ul.nav').toggle(1);
      });
<?php } ?>



  });
  $('a.dropdown-mobile').click(function(e){
      e.preventDefault();
      $('ul.dropdown-mobile-content').hide();
      $(this).next('ul').toggle('fast');

  });

</script>

<?php


/***
 * Start Home Page PopUp Code--Garvit
 * ***/
?>
<a href="#popUpForm" id="OpenPop" class="" style amp-custom="display:none;">&nbsp;</a>

<!-- <div id="popUpForm" class="pop_up-outer" style="display:none;">
    <div class="top_heading">
        <h1>SHOPS BUY ONLINE <br />
            @FACTORY PRICE</h1>
    </div>
    <div class="con_text">To get latest design and updates, enter your mobile number</div>
    <br>
    <form name="MobileHomePopUp" id="MobileHomePopUp" onsubmit="secondpopup(false)" action="javascript:void(0);" method="post">
        <div class="MobileTextInPop">
          <div>
            <input type="text" id="input-telephone" name="telephone" placeholder="ENTER YOUR MOBILE NUMBER" class="form-control input_mobile_number_class" />
            <?php /* Hide Check Box With WhatsApp Mobile Number 21-12-2015 (Ravindra Singh) ?>
            <br />
            <input type="checkbox" id="showWhatsAppNumber" value="WhatsApp Number" /><?php echo $text_mob_pop;?>
            <br />
            <input type="text" id="input-whatsapp-telephone" name="whatsapp_telephone" placeholder="ENTER YOUR WHATSAPP NUMBER" class="form-control input_mobile_number_class" style="display:none;" />
            <?php */?>
            <input type="hidden" id="input-hidden" name="hidden_text" value="mob" />
            <input type="submit" onclick="return phonenumber(document.MobileHomePopUp.telephone)"  value="ENTER" class="btn-primary popup_btn" />
          </div>
        </div>
    </form>
    <div class="skippopup"><span class="SkipPage">Skip</span></div>
    <a href="#" class="skippopup"><span class="SkipPage">Skip</span></a>
</div> -->

<!--latest as category page wishlist -->
<script type="text/javascript">
  $(document).ready(function(){
    $('.addtowishlist').click(function(){ 
      var product_id = $(this).attr('data-product-id');
      $('#wishlist_heart_'+product_id).removeClass(' fa fa-heart-o').addClass('fa fa-heart');
    });
  });
</script>
<!--END -->
<script type="text/javascript">
    //script for home page popup

  $(document).ready(function(){
      //parent.fancybox.close();
    /*$("#showWhatsAppNumber").click(function(){
      $("#input-whatsapp-telephone").toggle();
    });*/

   $(".SkipPage").click(function(e){
        e.preventDefault();
		//parent.$.fancybox.close();
		//$.fancybox.close
		//alert("ddd");
        $("#input-telephone").val("skiping...");
        $("#input-hidden").val("skip");

       ga('send', {
           hitType: 'event',
           eventCategory: 'MobilePopup',
           eventAction: 'Skip',
           eventLabel: 'Skiped mobile number popup'
       });


       secondpopup(false);



    });

    $("a#OpenPop").fancybox({
        padding: 10 ,
        helpers : {
            overlay : {closeClick: false}
        },
        // closeClick  : false, // prevents closing when clicking INSIDE fancybox
        'closeBtn' : false,
        onClosed: function(currentArray, currentIndex, currentOpts){

		}
    });



  });//domready

  ?>  


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
    <?php if(isset($show_mobile_number_popup ) && $show_mobile_number_popup == 1) { ?>
        $(document).ready(function(){
            // $("a#OpenPop").trigger("click"); // Do not need oprn popup now


        });
    <?php } ?>
</script>
<?php } ?>
<?php
  /***
 * End Home Page PopUp Code
 * ***/
?>

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

<div class="global-bg-layer"></div>
<div class="global-ajax-loader"></div>
<a href="#top" id="scroll_to_top" style="display: none;">&nbsp;</a>


<!-- <script type="text/javascript">
    $(document).ready(function(){
        $(".close_ship_popup").click(function(){
           $("#discount_shipping_popup").hide("slide", {
               direction: "left"
           }, 500);
           $("#discount_shipping_popup_show").show("slide", {
               direction: "left"
           }, 500);
        });

        $("#discount_shipping_popup_show").click(function(){
            $("#discount_shipping_popup").show("slide", {
                direction: "left"
            }, 500);
            $("#discount_shipping_popup_show").hide("slide", {
                direction: "left"
            }, 500);
        });
    });
</script> -->
<!-- get app link over sms -->
<script>
    function mobilenumber(inputtxt)
    {
        var flag = 1;
        var phoneno = /^\d{10}$/;
        var mobile = inputtxt;
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
            if(inputtxt.match(phoneno)){
                return true;
            }else{
                $('.get_app_link input[name="get_app_link"]').after('<span class="app-link-error">Please Enter Valid Mobile Number</span>');
                return false;
            }
        }else{
            $('.get_app_link input[name="get_app_link"]').after('<span class="app-link-error">Please Enter Valid Mobile Number</span>');
            return false;
        }
    }
    $(document).ready(function(){
        $('.get_applink_button').click(function(){
            $('.app-link-error').hide();
            var mobile_no = $('input[name="get_app_link"]').val();
            if(mobilenumber(mobile_no)){
                $.ajax({
                    type: 'POST',
                    url : 'index.php?route=common/footer/getAppLink',
                    data: {mobile_no},
                    dataType:'json',
                    beforeSend: function() {
                      $('.get_applink_button').button('loading');
                    },
                    complete: function() {
                      $('.get_applink_button').button('reset');
                    },
                    success: function(json){
                        if(json.error == true){
                            $('.get_app_link input[name="get_app_link"]').after('<span class="app-link-error">'+json['error_msg']+'</span>');
                        }else{
                            $('input[name="get_app_link"]').attr("placeholder", "Enter your mobile number").val("").focus().blur();
                        }
                        if(json.success == true){
                            $("#link_popup p").html('<i class="fa fa-check"></i> '+ json['success_msg']);
                            $("#link_popup").show();
                        }

                    }
                });
            }
        });
        $('.appLink_close_popup').click(function(){
          $("#link_popup").hide();
        });
    });
</script>

<script type="text/javascript">
    ie = (function(){
        var undef,
                v = 3,
                div = document.createElement('div'),
                all = div.getElementsByTagName('i');

        while (
                div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
                        all[0]
                );

        return v > 4 ? v : undef;
    }());

    if(ie < 10){
        window.location = '<?php echo $base; ?>update-your-browser';
    }
    /**
    * Load Categories after 5 seconds of page load
    * */

    var is_home = '<?php echo $is_home; ?>';

    var w;
    function startWorker()
    {
        if(typeof(Worker)!=="undefined")           
        {
            //get latest and tranding data
           var latest_product = ['latest', 'trending'];
            $.each(latest_product, function (key, value) {
               var category_data = '';
               var getLatestProductUrl = '<?php echo $this->url->link("module/' + value +  '&amp=1", "", "SSL")?>';
               w=new Worker("catalog/view/javascript/latest_category_worker.js");
               w.postMessage({ "url": getLatestProductUrl});
               w.onmessage = function (event) {
                   $("div#latest_data"+key).html(event.data.result);
                   stopWorker();
               };
           });

           //get category data
           var category_ids = jQuery.parseJSON('<?php echo $category_ids; ?>');
           for(var i =0;i < category_ids.length;i++)
           {
                var getCatId = category_ids[i];
                var getCategoryUrl = '<?php echo $this->url->link("module/latest_as_category&amp=1&category_id=' + getCatId + '", "", "SSL")?>';
                $("#wait").css("display", ""); // loader               
                w=new Worker("catalog/view/javascript/latest_category_worker.js");
                w.postMessage({ "url": getCategoryUrl, "catId":getCatId});
                w.onmessage = function (event) {
                    $("div#category_" + event.data.catId).html(event.data.result);
                    $("#wait").css("display", "none");
                    stopWorker();
                };
           }
        }
    }

    function stopWorker()
    {
        w.undefined;
    }

    startWorker(); // background processing for all categories

</script>

<script>
    $('.post_code').keyup(function(){
        var char_count = $(this).val().length;
        if(char_count > 5){
            var parent_class = $(this).attr('data-parent_class');
            var pincode = $('.'+parent_class+' .post_code').val();
            $.ajax({
                url : "index.php?route=account/address/autoPopulateAddress",
                type: "post",
                dataType: "json",
                data: "pincode="+pincode,
                success: function( json ) {
                    $('.'+parent_class+' .city').val(json['city']);
                    $("."+parent_class+" .zone option:contains(" + json['state'] + ")").attr('selected', 'selected');
                }
            });
        }
    });
</script>

<script  src="catalog/view/javascript/common-min.js" defer type="text/javascript"></script>

<script  src="catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js" defer type="text/javascript"></script>


<?php foreach ($scripts as $script) { ?>
<script  src="<?php echo $script; ?>" type="text/javascript"></script>
<?php } ?>
<?php foreach ($additional_scripts as $additional_script) {
    echo $additional_script;
} ?>

<?php if($is_home== 1){ ?>
<script async src="https://cdn.ampproject.org/v0.js"></script>
<script>self.AMP_CONFIG={"canary":0,"amp-ios-overflow-x":1,"amp-experiment":1,"pan-y":1,"expAdsenseA4A":0.1,"expDoubleclickA4A":0.1,"a4aProfilingRate":1,"amp-form":0,"form-submit":0,"ad-type-custom":1,"amp-scrollable-carousel":1,"amp-app-banner":1,"amp-inabox":1,"ios-embed-wrapper":1,"amp-apester-media":1,"amp-accordion-session-state-optout":1,};
</script>

<script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
<?php } ?>
</body>
</html>
