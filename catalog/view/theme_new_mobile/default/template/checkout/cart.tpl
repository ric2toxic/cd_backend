<?php echo $header; ?>

<div class="container-fluid white_bg"  >
  <div class="row">

    <div class="cart_full_box" id="cart_full_box" >
        <div class="col-xs-12 skeleton" style="background: white;" >
            <div class="shopping_cart"></div>
            <div class="pickup_city" ></div>
            <div class="item" ></div>
            <div class="item" ></div>
        </div>
    </div>

  </div>
    <!-- ESTIMATE SHIPPING(Correct pincode) popup -->
    <div class="modal fade add_new_address" id="estimate_shipping_success" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header address_popup_head">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">ESTIMATE SHIPPING</h4>
                </div>
                <div class="panel-body padding_top_bottem">
                    <div id="shipping_method_header">

                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-12 cart_table nopadding">
                        <div id="shipping_method_table"></div>
                        <div id="ess_charges"></div>
                    </div>

                    <div class="clearfix"></div>
                    <div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn deliver_btn" id="save_estimate_shipping" style="margin-right: 50px;" data-dismiss="modal">Save & Close</button>
                </div>
             <br/>

            </div>
        </div>
    </div>

    <!-- show notifications on cart page -->
    <div  class="alert alert-success" style="display: none;"></div>
    <div class="col-xs-6 col-md-6" id="notification" style="display: none;">
    </div>

    <!-- I want this design popup -->
    <div class="modal fade want_design" id="want_design" role="dialog">
        <div class="modal-dialog" style="top:20%;">
            <div class="modal-content" >
                <div class="modal-body padding_top_bottem" style="padding: 0px;min-height: 15px;">
                    <input type="hidden" id="want_design_product_id" value=""/>
                    <input type="hidden" id="want_design_product_status" value=""/>
                    <button type="button" class="close" data-dismiss="modal" style="size: 15px;">&times;</button>
                    <br/>
                    <p><strong>Hi <?php echo $this->customer->getFirstName(); ?></strong></p>
                    <?php echo $comment_popup_heading; ?>
                    <textarea rows="3" style="width:100%;" id="want_design_comment" maxlength="300" ></textarea>
                    <button class="btn deliver_btn pull-right" id="submit_want_design"><?php echo $comment_popup_send; ?></button>
                </div>
                <div class="clearfix"></div>
                <div class="modal-footer popup_footer_padding_none mobile_modal_footer">
                    <div id="want_design_warning" style="color: #710909;padding-bottom: 8px;text-align: center;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm action(remove,move to wishlist,clear cart etc) model -->
    <div id="confirm_popup" class="modal fade" role="dialog">
		 <div class="modal-dialog" style="top:20%;" >
			 <div class="modal-content alert alert-warning">
				<div class="modal-body" id="confirm_body" style="padding: 0px;min-height: 15px;"></div>
				<div class="modal-footer popup_footer_padding_none mobile_modal_footer" id="confirm_footer"></div>
			</div>
		</div>
	</div>

    <!-- alert model -->
    <div id="alert_popup" class="modal fade" role="dialog">
        <div class="modal-dialog" style="top:20%;">
            <div class="modal-content alert alert-info">
                <div class="modal-body" style="padding: 0px;min-height: 15px;">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div id="alert_body" ></div>
                </div>
                <div class="modal-footer popup_footer_padding_none mobile_modal_footer">
                    <button type="button" data-dismiss="modal" class="btn popup_close_btn pull-right">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- comment popup (start) -->
    <div class="modal fade add_new_address" id="product_comment_popup" role="dialog">
        <div class="modal-dialog" style="top:20%;">
            <div class="modal-content">
                <div class="modal-body" style="padding: 0px;min-height: 15px;">
                    <input type="hidden" id="comment_product_key" value=""/>
                    <textarea rows="4" id="product_comment" class="password_box" placeholder="Enter your comment" alt="comment"></textarea>
                    <div class="clearfix"></div>
                    <div class="text-danger" id="error_product_comment" style={{marginLeft:'5%'}}></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn deliver_btn clear_popup_btn" id="submit_product_comment">Submit</button>
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- comment popup (End)-->

</div>
<input type="hidden" id="cartlimitcross" value="<?php echo $cartlimitcross;?>" />
<img src="/image/loader.gif" id="loading-indicator" style="display:none" />
<a href="#top" id="scroll_to_top" style="display: none;">&nbsp;</a>
<style>
    #loading-indicator {
        position: fixed;
        left: 45%;
        top: 45%;
        z-index:1060;
    }
</style>
<script type="text/javascript">
    $('#submit_want_design').click(function() {
        var product_id = $('#want_design_product_id').val();
        var product_status = $('#want_design_product_status').val();
        var popup_comment = $('#want_design_comment').val();
        if(popup_comment.length < 2){
            $('#want_design_warning').html("Comment should have atleast two characters.");
            return;
        }
        $.ajax({
            type : "POST",
            url  : 'index.php?route=product/search/user_comment',
            data : "product_id="+product_id+"&product_status="+product_status+"&popup_comment="+popup_comment,
            dataType: 'json',
            beforeSend: function() {
                $('#submit_want_design').button('loading');
            },
            complete: function() {
                $('#submit_want_design').button('reset');
                $('#want_design').modal('hide');
            },
            success: function(data){
                $('#want_design').modal('hide');
                $("#notification").fadeIn("slow").html('<?php echo $text_want_design_sucess; ?>');
                $("#notification").fadeOut(1500);
            }
        });
        $('#want_design_comment').val("");
    });
</script>

<!-- Custom CSS -->
<link href="catalog/view/theme_new_mobile/default/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="catalog/view/theme_new_mobile/default/css/style_mobile.css?v=1.2" rel="stylesheet"/>
<link href="catalog/view/theme_new_mobile/default/css/skeleton.css" rel="stylesheet"/>

<script src="catalog/view/javascript/reactjs/react.js"></script>
<script src="catalog/view/javascript/reactjs/react-dom.js"></script>
<script src="catalog/view/theme_new_mobile/default/javascript/cart.js?v=40.18" ></script>

<script>

  var is_empty = <?php echo $empty; ?>;
  var items = <?php echo ( ( isset($product_json) )?$product_json:"[]");  ?> ;
  var total_sets = <?php echo ( ( isset($total_sets) )?$total_sets:'0'); ?>;
  var total_pieces = <?php echo ( ( isset($total_pieces) )?$total_pieces:'0'); ?>;
  var totals = <?php echo ( ( isset($totalJson) )?$totalJson:"[]"); ?>;
  var clear_cart = <?php echo ( ( isset($clear_cart) )?$clear_cart:"[]"); ?>;
  var checkout = <?php echo "'".( ( isset($checkout) )?$checkout:"")."'"; ?>;
  var tax_refund = <?php echo "'".( ( isset($tax_refund) )?$tax_refund:"")."'"; ?>;
  var disable_place_order = <?php echo "'".( ( isset($disable_place_order) )?$disable_place_order:"")."'"; ?>;
  var text_cart_minimum = <?php echo "'".( ( isset($text_cart_minimum) )?$text_cart_minimum:"")."'"; ?>;
  var show_cform_option = <?php echo "'".( ( isset($show_cform_option) )?$show_cform_option:"0")."'"; ?>;
  var total_out_of_stock_products = <?php echo "'".( ( isset($total_out_of_stock_products) )?$total_out_of_stock_products:"0")."'"; ?>;
  var total_quantity_reduced_products = <?php echo "'".( ( isset($total_quantity_reduced_products) )?$total_quantity_reduced_products:"0")."'"; ?>;
  var international_store = <?php echo ( ( isset($international_store) )?$international_store:0); ?>;
  var cartlimitcross = <?php echo ( ( isset($cartlimitcross) )?$cartlimitcross:0); ?>;
  var customer_data = <?php echo ( ( isset($customer_data) )?$customer_data:"[]");  ?> ;
  var coupon = '<?php echo ( ( isset($coupon_code) )?$coupon_code:"");  ?>' ;
  var weight = '<?php echo ( ( isset($weight) )?$weight:"");  ?>' ;

  var language = <?php echo $language; ?>;

  var cart_summary = {"totals":totals,"disable_place_order":disable_place_order,"text_cart_minimum":text_cart_minimum,"text_cart_minimum":text_cart_minimum,"price_in_rupees":"",
                    "tax_refund":tax_refund,"checkout":checkout,"show_cform_option":show_cform_option,
          "total_out_of_stock_products":total_out_of_stock_products,"total_quantity_reduced_products":total_quantity_reduced_products
                    };
  var cart_data = { "products":items ,"total_sets":total_sets,"total_pieces":total_pieces,
                    "clear_cart":clear_cart,"cartlimitcross":cartlimitcross,"coupon":coupon,"weight":weight };

  ReactDOM.render(React.createElement(Cart, {customer:customer_data, cart_data:cart_data, is_empty:is_empty, cart_summary:cart_summary, language:language,international_store:international_store},null ), document.getElementById('cart_full_box'));

</script>

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
<!--Start of Tawk.to Script-->
<script type="text/javascript">
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
<!--End of Tawk.to Script-->
<?php echo $footer; ?>
