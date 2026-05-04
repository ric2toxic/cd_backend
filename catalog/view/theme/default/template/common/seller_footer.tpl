<footer class="color_white">
    <div class="container">
        <div class="row">
            <?php if ($informations) { ?>
            <div class="col-sm-3">
                <h3><?php echo $text_information; ?></h3>
                <ul class="list-unstyle list-unstyled">
                    <li><a href="http://www.wholesalebox.in/about-us">About us</a></li>
                    <li><a href="http://www.wholesalebox.in/contact-us">Contact us</a></li>
                    <?php foreach ($informations as $information) { ?>
                    <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
                    <?php } ?>
                    <?php if ($logged) { ?>
                    <?php if($is_dropshipper == 0){ ?>
                    <li><a href="<?php echo $dropshipper; ?>"><?php echo $text_dropshipper; ?></a></li>
                    <?php } ?>
                    <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
                    <?php }else { ?>
                    <li><a href="<?php echo $dropshipper; ?>"><?php echo $text_dropshipper; ?></a></li>
                    <li><a href="<?php echo $affiliate; ?>"><?php echo $text_Affiliate; ?></a></li>

                    <?php } ?>
                </ul>
            </div>
            <?php } ?>

            <div class="col-sm-3">
                <div class="footer4">
                    <h3>Helpline Number</h3>
                    <ul class="list-unstyle list-unstyled">
                        <li>

                            <a href="tel:+91 141 4049163" class="phone"><i class="fa fa-phone"></i>(+91) 141 - 4049163</a>
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
                        <img src="https://cdnimages.net/img/fd.jpg" alt="Fedex is our Logistics Partner">
                    </div>
                    <div class = "col-sm-12 col-xs-8" style="padding:0px;">
                        <img src="https://cdnimages.net/img/blue_dart.jpg" alt="BlueDart is our Logistics Partner">
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="trust_seal">

                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-3 social_profiles">
                <ul>
                    <li>
                        <a class="sf_facebook" href="https://web.facebook.com/wholesalebox1" target="_blank"></a>
                    </li>
                    <li><!-- google plus link -->
                        <a class="sf_googleplus" href="https://plus.google.com/115306833718886596322" target="_blank"></a>
                    </li>
                    <?php /* ?> <li>
                        <a class="sf_youtube" href="https://web.facebook.com/wholesalebox1" target="_blank"></a>
                    </li>
                    <?php */ ?>

                </ul>
            </div>
            <div class="col-sm-6 ">
                <div class="ic_payment_methods">

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
    <div class="global-bg-layer"></div>
    <div class="global-ajax-loader"></div>
    <a href="#top" id="scroll_to_top" style="display: none;">&nbsp;</a>
        <div style="display: none;"><?php echo $seller_terms; ?></div>
        <input type="hidden" name="term_condition" value="<?php echo $logging_in; ?>" id="seller_term_condition" />
    </div>

</footer>
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




    // Seller Agreement Popup And it's Submission
    $(document).ready(function(){
        var seller_term_condition = $('#seller_term_condition').val();
        function trigger_click() {
            $('.agree').click();
        }
   

        function getCookie(c_name) 
        {
          if (document.cookie.length > 0) { 
          var c_start = document.cookie.indexOf(c_name + "=");
          if (c_start !== -1) {
          c_start = c_start + c_name.length + 1;
          var c_end = document.cookie.indexOf(";", c_start);
          if (c_end === -1) {
             c_end = document.cookie.length;
            }
           return unescape(document.cookie.substring(c_start, c_end));
           }
         }
       return "";
        }


        if(getCookie('seller_agreement_<?php echo $logged; ?>') == false && <?php echo SELLER_AGREEMENT_POPUP; ?>)
        {
          $('#modal-agree').remove();
          //var element =  $(".agree");
           $.ajax({
           url: '<?php echo $seller_agreement_link; ?>',
           type: 'get',
           dataType: 'html',
           success: function(data) {
            html  = '<div id="modal-agree" class="modal">';
            html += '  <div class="modal-dialog" style="width:75%;">';
            html += '    <div class="modal-content">';
            html += '      <div class="modal-header">';
            html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
            html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
            html += '      </div>';
            html += '      <div class="modal-body">' + data + '</div>';
            html += '      <div class="modal-footer">';
            html += '      <input type="button" class="btn btn-primary pull-right seller_agree" data-dismiss="modal" value="Agree">';
            html += '      <input type="button" class="btn btn-default pull-right" data-dismiss="modal" value="Cancel" style="margin-right:10px;">';
            html += '      </div>';
            html += '    </div';
            html += '  </div>';
            html += '</div>';

            $('body').append(html);

            $('#modal-agree').modal('show');
            }
          });

        }

        $(document).delegate('.agree_seller_terms' , 'click', function () {
            $.ajax({
                type: 'POST',
                url : 'index.php?route=seller/account-dashboard/sellerAgreementSubmission',
                data : 'seller_agreement=1',
                dataType:'html',
                success: function(data){
                    $('.modal-header button').click();
                }
            });
        });

        $(document).delegate('.seller_agree' , 'click', function () {
         var name = 'seller_agreement_<?php echo $logged; ?>'; 
          var value = true; 
          var days = 200;
          var expires;
          if (days) 
          {
             var date = new Date();
             date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
             expires = "; expires=" + date.toGMTString();
          }
          else 
          {
             expires = "";
          }
             document.cookie = name + "=" + value + expires + "; path=/";
        });

    });

</script>

<script>
    /**
     * Add offer for a particular product.
     * */

    $('.deals').click(function () {
        $(".opacity_changer").addClass("opacity_change");
        var product_id = $(this).attr('data-id');
        var product_price = $(this).attr('data-price');
        var selected_fix = ' ';
        var selected_percentage = ' ';

        if($(this).attr('data-discount_type') == 'percentage') {
             selected_percentage = "selected='selected'";
        } else if ($(this).attr('data-discount_type') == 'fix') {
             selected_fix = "selected='selected'";
        }
        var html = '';
        html += '<div class="col-sm-12" style="border-bottom:1px solid #ccc;"><h3>Add Offer to this Product<i class="fa fa-times close_method_prompt pull-right cursor-pointer"></i></h3></div>';
        html += '<div class="col-sm-12 margin_top_15">';
        html += '<label class="col-sm-3 control-label font_weight_600" for="input-offer-type"><?php echo "Discount Type"; ?></label>';
        html += '<div class="col-sm-9"><select name="offer_type" id="input-offer-type" class="form-control">';
        html += '<option value="percentage" '+selected_percentage+'><?php echo "Percentage"; ?></option>';
        //html += '<option value="fix" '+selected_fix+'><?php echo "Fixed"; ?></option>';
        html += '</select></div></div>';
        html += '<div class="col-sm-12 margin_top_15">';
        //html += '<label class="col-sm-3 control-label font_weight_600" for="input-offer-price"><?php echo "Offer (Fix/Percentage)"; ?></label>';
        html += '<label class="col-sm-3 control-label font_weight_600" for="input-offer-price"><?php echo "Offer (Percentage)"; ?></label>';
        html += '<div class="col-sm-9"><input type="text" class="form-control" id="input-offer-price" value="'+$(this).attr('data-discount_value')+'" name="input_offer_price" /><div class="error-box" id="input-offer-error"></div></div></div>';
        html += '<div class="col-sm-12 margin_top_15">';
        html += '<div class="offer_date font_weight_600 col-sm-12"><div class="offer_date_text">Date to avail offer</div></div>';
        html += '<div class="col-sm-12" style="margin-top:15px;">';
        html += '<div class="col-sm-5"><span class="font_weight_600">Start</span> <span style="font-size: 10px;">(yyyy-mm-dd)</span></div><div class="col-sm-7"><input class="form-control" id="input-date-from" value="'+$(this).attr('data-offer_date_start')+'" type="text"><div class="error-box" id="input-date-from-error"></div></div>';
        html += '<div class="col-sm-5 margin_top_15"><span class="font_weight_600">End</span> <span style="font-size: 10px;">(yyyy-mm-dd)</span></div><div class="margin_top_15 col-sm-7"><input class="form-control" id="input-date-to" value="'+$(this).attr('data-offer_date_end')+'" type="text"><div class="error-box" id="input-date-to-error"></div></div>';
        html += '<input id="input-product-id" type="hidden" value="'+product_id+'" /><input id="input-product-price" type="hidden" value="'+product_price+'" /></div></div>';
        if ($(this).attr('data-discount_value') != '') {
            html += '<div class="apply_offer_button col-sm-6 nopadding">Apply Offer</div><div class="remove_offer_button col-sm-6 nopadding">Remove Offer</div></div>';
        } else {
            html += '<div class="apply_offer_button col-sm-12">Apply Offer</div></div>';
        }

        $('.relative_offer_information').html(html);

        $(".offer_information").show();

    });

    $(document).delegate('#input-offer-price' , 'keyup', function () {

        if (isNaN($('#input-offer-price').val())) {
            $('#input-offer-price').css({"border-color": "rgb(245,107,107)"});
            $('#input-offer-error').html("only numeric values are allowed");
        } else {
            var percent_fix = ($('#input-product-price').val()*90)/100;
            if ($("#input-offer-type option:selected").val() == 'percentage' && $('#input-offer-price').val() < 20) {
                $('#input-offer-price').css({"border-color": "rgb(245,107,107)"});
                $('#input-offer-error').html("discount percent can not be less than 20");
            } else if($("#input-offer-type option:selected").val() == 'percentage' && $('#input-offer-price').val() > Number(90)) {
                $('#input-offer-price').css({"border-color": "rgb(245,107,107)"});
                $('#input-offer-error').html("discount percent can not be more than 90%");
            } else if($("#input-offer-type option:selected").val() == 'fix' && $('#input-offer-price').val() < Number(1)) {
                $('#input-offer-price').css({"border-color": "rgb(245,107,107)"});
                $('#input-offer-error').html("fixed discount can not be less than 1");
            } else if($("#input-offer-type option:selected").val() == 'fix' && $('#input-offer-price').val() > percent_fix) {
                $('#input-offer-price').css({"border-color": "rgb(245,107,107)"});
                $('#input-offer-error').html("fixed discount can not more than 90 %");
            }else {
                $('#input-offer-price').css({"border-color": "#ccc"});
                $('#input-offer-error').html('');
            }
        }
    });

    $(document).delegate('#input-date-from' , 'keyup', function () {
        if($('#input-offer-from').val() != '' || $('#input-offer-from').val() != null){
            $('#input-date-from').css({"border-color": "#ccc"});
            $('#input-date-from-error').html('');
        }
    });

    $(document).delegate('#input-date-to' , 'change', function () {
        if($('#input-date-to').val() != '' || $('#input-offer-from').val() != null){
            $('#input-date-to').css({"border-color": "#ccc"});
            $('#input-date-to-error').html('');
        }
    });
    var start_date = 0;
    $(document).delegate('.apply_offer_button' , 'click', function () {

        var product_id = $('#input-product-id').val();
        var product_price = $('#input-product-price').val();
        var offer = $('#input-offer-price').val();
        var offer_type = $("#input-offer-type option:selected").val();
        var date_from  = $('#input-date-from').val();
        var date_to    = $('#input-date-to').val();
        var error      = 0;
        var percent_fix = ($('#input-product-price').val()*90)/100;
        var percent_fix_ten = ($('#input-product-price').val()*10)/100;
        if (offer == '' || offer == null) {
            $('#input-offer-price').css({"border-color": "rgb(245,107,107)"});
            $('#input-offer-error').html("Can not be left empty");
            error = 1;
        } else if (isNaN(offer)) {
            $('#input-offer-price').css({"border-color": "rgb(245,107,107)"});
            $('#input-offer-error').html("only numeric values are allowed.");
            error = 1;
        } else if (date_from == '' || offer == null) {
            $('#input-date-from').css({"border-color": "rgb(245,107,107)"});
            $('#input-date-from-error').html("Can not be left empty");
            error = 1;
        } else if (date_to == '' || offer == null) {
            $('#input-date-to').css({"border-color": "rgb(245,107,107)"});
            $('#input-date-to-error').html("Can not be left empty");
            error = 1;
        } else if ($("#input-offer-type option:selected").val() == 'percentage' && $('#input-offer-price').val() < 10) {
            $('#input-offer-price').css({"border-color": "rgb(245,107,107)"});
            $('#input-offer-error').html("discount percent can not be less than 20");
            error = 1;
        } else if($("#input-offer-type option:selected").val() == 'percentage' && $('#input-offer-price').val() > Number(90)) {
            $('#input-offer-error').html("discount percent can not be more than 90");
            error = 1;
        } else if($("#input-offer-type option:selected").val() == 'fix' && $('#input-offer-price').val() < Number(1)) {
            error = 1;
            $('#input-offer-error').html("fixed discount can not be less than 1");
        } else if($("#input-offer-type option:selected").val() == 'fix' && $('#input-offer-price').val() > percent_fix) {
            error = 1;
            $('#input-offer-price').css({"border-color": "rgb(245,107,107)"});
            $('#input-offer-error').html("fixed discount can not more than 90%");
        } else if($("#input-offer-type option:selected").val() == 'fix' && $('#input-offer-price').val() < percent_fix_ten) {
            error = 1;
            $('#input-offer-price').css({"border-color": "rgb(245,107,107)"});
            $('#input-offer-error').html("fixed discount can not be less than 10%");
        } else if ($("#input-date-to").val() != ' ') {
            //alert('input-date-to');
            var text = $("#input-date-to").val();
            var comp = text.split('-');
            var y = parseInt(comp[0], 0);
            var m = parseInt(comp[1], 0);
            var d = parseInt(comp[2], 0);
            if (isNaN(y) || isNaN(m) || isNaN(d) || m > 12 || d > 31) {
                $('#input-date-to').css({"border-color": "rgb(245,107,107)"});
                $('#input-date-to-error').html("invalid date format");
                error = 1;
            }
            var text_check = $("#input-date-from").val();
            var comp_check = text_check.split('-');
            var y_check = parseInt(comp_check[0], 0);
            var m_check = parseInt(comp_check[1], 0);
            var d_check = parseInt(comp_check[2], 0);
            if (y < y_check) {
                $('#input-date-to').css({"border-color": "rgb(245,107,107)"});
                $('#input-date-to-error').html("must be more then start date");
                error = 1;
            } else if(y == y_check && m == m_check && d <= d_check) {
                $('#input-date-to').css({"border-color": "rgb(245,107,107)"});
                $('#input-date-to-error').html("must be more then start date");
                error = 1;
            } else if (y == y_check && m < m_check) {
                $('#input-date-to').css({"border-color": "rgb(245,107,107)"});
                $('#input-date-to-error').html("must be more then start date");
                error = 1;
            }
        }

        if ($("#input-date-from").val() != '') {
            //alert('input-date-from');
            var text_from = $("#input-date-from").val();
            var comp_from = text_from.split('-');
            var y_from = parseInt(comp_from[0], 0);
            var m_from = parseInt(comp_from[1], 0);
            var d_from = parseInt(comp_from[2], 0);
            //alert(d_from);
            if (isNaN(y_from) || isNaN(m_from) || isNaN(d_from) || m_from > 12 || d_from > 31) {
                $('#input-date-from').css({"border-color": "rgb(245,107,107)"});
                $('#input-date-from-error').html("invalid date format");
                error = 1;
            }
            var inputDate = new Date(y_from, m_from -1 ,d_from);
            //alert (inputDate);
            var todaysDate = new Date();
            //alert(todaysDate);
            if(inputDate.setHours(0,0,0,0) < todaysDate.setHours(0,0,0,0)) {
                $('#input-date-from').css({"border-color": "rgb(245,107,107)"});
                $('#input-date-from-error').html("must be equal or more than today date");
                error = 1;
            }
        }

        if (error) {
            alert('oops!!! there are some errors in you form.');
            return false;
        } else {
            $.ajax({
                type: "post",
                dataType: "html",
                url: 'index.php?route=seller/manage-inventory/addOfferToAParticularProduct',
                data: 'product_id='+product_id+'&offer='+offer+'&price='+product_price+'&offer_type='+offer_type+'&date_from='+date_from+'&date_to='+date_to,
                beforeSend: function() {
                    $(".offer_information").addClass("opacity_change");
                    $(".loading-icon").show();
                },
                success: function(data) {
                    if(data == 1)
                    {
                      $('.offer_information   ').fadeOut('500');
                      $(".offer_information").removeClass("opacity_change");
                      $(".opacity_changer").removeClass("opacity_change");
                      $(".loading-icon").hide();
                      location.reload();
                    }
                    else
                    {  
                      $('.offer_information   ').fadeOut('500');
                      $(".offer_information").removeClass("opacity_change");
                      $(".opacity_changer").removeClass("opacity_change");
                      $(".loading-icon").hide(); 
                       alert('oops!!! Incorrect offer value.');
                       return false;  
                    }
                    
                }
            });
        }
    });

    $(document).delegate('.close_method_prompt' , 'click', function () {
        $('.offer_information   ').fadeOut('500');
        $(".opacity_changer").removeClass("opacity_change");
    });

    $(document).delegate('#input-date-to', 'focus', function () {
        if($("#input-date-from").val() != '') {
            //alert($('#input-date-from').val());
            var text = $("#input-date-from").val();
            //alert(text);
            var comp = text.split('-');
            var y = parseInt(comp[0], 10);
            var m = parseInt(comp[1], 10);
            var d = parseInt(comp[2], 10);

            $(this).datetimepicker({
                format: 'YYYY-MM-DD',
                minDate: new Date(y, m -1, d +1),
                container: '#picker-container',
                pickTime: false
            });
            $('.bootstrap-datetimepicker-widget').addClass('position-absolute');
        } else {
            if(start_date == 1) {
                $('#input-date-to').css({"border-color": "rgb(245,107,107)"});
                $('#input-date-to-error').html("start date must be valid");
            } else {
                $('#input-date-to').css({"border-color": "rgb(245,107,107)"});
                $('#input-date-to-error').html("fill the start date first");
            }

        }
    });

    $(document).delegate('#input-date-from  ', 'focus', function () {
        var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        //alert(today);
        $(this).datetimepicker({
            format: 'YYYY-MM-DD',
            container: '#picker-container',
            minDate: today,
            pickTime: false
        });
        $('.bootstrap-datetimepicker-widget').addClass('position-absolute');
    });

    /**
     * Remove deal
     * */
    $(document).delegate('.remove_offer_button' , 'click', function () {
        var product_id = $('#input-product-id').val();
        $.ajax({
            type: "post",
            dataType: "html",
            url: 'index.php?route=seller/manage-inventory/deleteOfferFromProduct',
            data: 'product_id='+product_id,
            beforeSend: function() {
                $(".offer_information").addClass("opacity_change");
                $(".loading-icon").show();
            },
            success: function(data) {
                $('.offer_information   ').fadeOut('500');
                $(".offer_information").removeClass("opacity_change");
                $(".opacity_changer").removeClass("opacity_change");
                $(".loading-icon").hide();
                location.reload();
            }
        });
    });
</script>

</body>
</html>
