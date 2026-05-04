<style type="text/css">
    .custom_css{
        padding: 0px;
    width: auto;
    padding-top: 7px;
    }
</style>
<div class="row tabs hidden edit_custom_paycharge">
    <form action="<?php echo $save; ?>" method="post" name="apply_custom">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
        <input type="hidden" name="suborder_id" value="<?php echo $suborder_id; ?>" />
        <input type="hidden" name="apply_custom_discount" value="1" />
        <div class="col-lg-12">
            <div class="form-group" style="display: inline-block;">
                <label class="switch">
                    <input type="checkbox" id="whole-suborders" class="order_special_request check-discount-type" name="wholesuborder" value="1"/>
                    <span class="slider round"></span>
                </label>
                <label style="margin-left:10px; vertical-align:top; margin-top: 4px; font-size: 14px;">Apply discount on whole suborder</label>
            </div>
            <div class="form-group" style="display: inline-block;  margin-left: 40px;">
                <label class="switch">
                    <input type="checkbox" id="multiple-products" class="order_special_request check-discount-type" name="multipleproduct" value="1"/>
                    <span class="slider round"></span>
                </label>
                <label style="margin-left:10px; vertical-align:top; margin-top: 4px; font-size: 14px;">Apply discount on Multiple SKUs</label>
            </div>
            <div class="form-group" style="display: inline-block;  margin-left: 40px;">
                <label class="switch">
                    <input type="checkbox" id="single-products" class="order_special_request check-discount-type" name="singleproduct" value="1"/>
                    <span class="slider round"></span>
                </label>
                <label style="margin-left:10px; vertical-align:top; margin-top: 4px; font-size: 14px;">Apply discount on Single SKU</label>
            </div>
        </div>
        <div class="col-lg-12 whole-suborder hidden">
            <div class="form-group" >
                <label class="col-sm-2 custom_css">Discount <i class="fa fa-info-circle fa-lg" data-toggle="tooltip" title="Enter numeric discount value. If you want to remove discount, enter 0"></i></label>
                <div style="padding-left:10px; width: 100px;" class="col-sm-1">
                    <input type="text" name="wholesuborder_rate" class="form-control whole_suborder_input check_input_is_number"  value="" min="0" />
                </div>
                <div style="height: 35px;padding-left: 0px;" class="col-sm-2">
                    <textarea name="wholesuborder_comment" class="form-control whole_suborder_input" placeholder="Comment"  value="" style="height: 35px"></textarea>
                </div>
                <label>
                    <button type="subimt" class="btn btn-primary submit_dis">Apply</button>
                </label>
            </div>
        </div>
        <div class="col-lg-12">            
            <?php
            $is_custom_discount_applied = 0;
            if(!empty($products)) {
                ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="10%">Sr. No.</th>
                            <th width="10%">Product</th>
                            <th width="10%">Sets</th>
                            <th width="10%">Piece in set</th>
                            <th width="10%">Price/Piece</th>
                            <th width="10%">Dis. Rate</th>
                            <th width="10%">Dis. / Pc</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $x = 1;
                        $y = 1;
                        $is_custom_discount_applied = 0;
                        $custom_discount_applied_arr = array();
                        foreach ($products as $product) {
                            $discount_rate = 0;
                            $discount_amount = 0;
                            $coupon_discount = false;
                            $is_custom_discount_applied = 0;
                            $temp_rate = 0;
                            $coupon_discount_rate = 0;
                            if(!empty($product['discount_breakup'])) {
                                $discount_breakup = unserialize($product['discount_breakup']);
                                if(!empty($discount_breakup['paycharge'])) {
                                    $discount_rate = $discount_breakup['paycharge']['valuep'];
                                    $discount_amount = $discount_breakup['paycharge']['value'];
                                    $custom_discount_applied_arr[] = $discount_rate;
                                }
                                if(!empty($discount_breakup['coupon'])) {
                                    $coupon_discount = true;
                                    $coupon_discount_rate = (float)$discount_breakup['coupon']['discount'];
                                }
                            }
                            if($coupon_discount && $y == 1) {
                                ?>
                                <div class="text_blink">
                                    <span  style="color: rgb(102, 102, 255); font-size: 13px;"><strong>Coupon discount (-<?php echo $coupon_discount_rate;?>%) is already applied in this suborder. Please note that the coupon discount will automatically be replaced by custom discount, if applied.</strong></span>
                                </div>
                                <br>
                                <?php
                                $y++;
                            }
                            ?>
                            <tr>
                                <td width="10%">
                                    <input type="checkbox" name="order_product_check[]" data-order-product="<?php echo $product['order_product_id'] ?>" class="form-control order_product_check hidden check_input_is_number" value="<?php echo $product['order_product_id'] ?>" >
                                    <span class="number_order"><?php echo $x; ?></span>
                                </td>
                                <td width="10%">
                                        <div class="col-sm-9">
                                            <img src="<?php echo $product['image']; ?>"
                                                 width="<?php echo $product['width']; ?>px"
                                                 height="<?php echo $product['height']; ?>px"
                                                 class="pull-left" />
                                            <p style="margin-left:10px;width: calc( 100% - 200px );" class="pull-left"><?php echo $product['name']; ?><br><br>
                                                <b>Model: </b><?php echo $product['model']; ?><br><br>
                                                <b>Seller Sku: </b><?php echo $product['seller_sku']; ?><br><br>
                                                <?php 
                                                if(!empty($order_options[$product['order_product_id']])) {
                                                    echo "<b>" . $order_options[$product['order_product_id']]['name'] .": </b>" . $order_options[$product['order_product_id']]['value'];
                                                }
                                                ?>
                                                
                                            </p>
                                        </div>
                                        <div class="col-lg-9 single-product hidden" >
                                            <div class="form-group" >
                                                <label class="col-sm-2 custom_css">Discount <i class="fa fa-info-circle fa-lg" data-toggle="tooltip" title="Enter numeric discount value. If you want to remove discount, enter 0"></i></label>
                                                <div style="padding-left:10px;  width: 100px;" class="col-sm-2">
                                                    <input type="text" name="single_rate[<?php echo $product['order_product_id'] ?>]" class="form-control single_suborder_input check_input_is_number" id="single_rate_<?php echo $product['order_product_id'] ?>"  value="" min="0" />
                                                </div>
                                                <div style="height: 35px;padding-left: 0px;" class="col-sm-5">
                                                    <textarea name="single_comment[<?php echo $product['order_product_id'] ?>]" class="form-control single_suborder_input" id="single_comment_<?php echo $product['order_product_id'] ?>" placeholder="Comment"  value="" style="height: 35px"></textarea>
                                                </div>
                                                <label>
                                                    <button type="subimt" class="btn btn-primary submit_dis" data-order-product-id="<?php echo $product['order_product_id'] ?>">Apply</button>
                                                </label>
                                            </div>
                                        </div>
                                </td>
                                <td width="10%"><?php echo $product['quantity']; ?></td>
                                <td width="10%"><?php echo $product['piece_in_set']; ?></td>
                                <td width="10%"><?php echo $product['price_per_piece']; ?></td>
                                
                                <td width="10%"><?php echo $discount_rate."%"; ?></td>
                                <td width="10%"><?php echo $discount_amount; ?></td>
                            </tr>
                            <?php
                            $x++;
                        }
                        if(!empty($custom_discount_applied_arr)) {
                            $custom_discount_applied_arr = array_values(array_unique($custom_discount_applied_arr));
                            $is_custom_discount_applied = (count($custom_discount_applied_arr)>1) ? 1 : 0;
                        }
                        ?>
                        
                    </tbody>
                </table>
                <?php
            }
            ?>
            
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){

        $(".check_input_is_number").keyup(function(e) {
          if (/\D/g.test(this.value)) {
           var node = $(this);
           node.val(node.val().replace(/[^0-9.]/g,'') );
          }
        });

        $('.submit_dis').click(function(){
            var curr_checked_type = '';
            var cur_order_product_sub = $(this).attr('data-order-product-id');
            var is_coupon_dis = '<?php echo ($coupon_discount ?  1 : 0); ?>';
            $('.check-discount-type').each(function(){
                if($(this).is(':checked') == true) {
                    curr_checked_type = $(this).attr('id');
                }
            });
            if(curr_checked_type == 'whole-suborders') {
                var whole_rate = $("input[name='wholesuborder_rate']").val();
                var whole_comment = $("textarea[name='wholesuborder_comment']").val();
                if(whole_rate == '') {
                    alert('Discount rate should not be empty.');
                    $("input[name='wholesuborder_rate']").focus();
                    return false;
                } else if(whole_comment == '' || whole_comment == '0') {
                    alert('Comment should not be empty or zero.');
                    $("textarea[name='wholesuborder_comment']").focus();
                    return false;
                }
            } else if(curr_checked_type == 'multiple-products') {
                var whole_rate = $("input[name='wholesuborder_rate']").val();
                var whole_comment = $("textarea[name='wholesuborder_comment']").val();
                var is_checked_products = 0;
                 $('.order_product_check').each(function(){
                    if($(this).is(':checked') == true) {
                        is_checked_products = 1;
                    }
                });
                if(is_checked_products ==0) {
                    alert('Please select atleast one order product to apply custom discount.');
                    return false;
                } else if(whole_rate == '') {
                    alert('Discount rate should not be empty.');
                    $("input[name='wholesuborder_rate']").focus();
                    return false;
                } else if(whole_comment == '' || whole_comment == '0') {
                    alert('Comment should not be empty or zero.');
                    $("textarea[name='wholesuborder_comment']").focus();
                    return false;
                } 
            }  else if(curr_checked_type == 'single-products') {
                var whole_rate = $("#single_rate_" + cur_order_product_sub).val();
                var whole_comment = $("#single_comment_" + cur_order_product_sub).val();
                $('.single_suborder_input').each(function(cur_index, cur_element) {
                    var id_check = $(cur_element).attr('id');
                    if(id_check == 'single_rate_'+cur_order_product_sub || id_check == 'single_comment_'+cur_order_product_sub) {
                        return;
                    }
                    $('#'+id_check).val('');
                });
                if(whole_rate == '') {
                    alert('Discount rate should not be empty.');
                    $("#single_rate_" + cur_order_product_sub).focus();
                    return false;
                } else if(whole_comment == '' || whole_comment == '0') {
                    alert('Comment should not be empty or zero.');
                    $("#single_comment_" + cur_order_product_sub).focus();
                    return false;
                } 
            }

            var confirm_msg = 'Are you sure you want to apply custom advance?';
            if(whole_rate ==0 && is_coupon_dis == 1) {
               confirm_msg += ' Coupon discount is already applied it will replace it with zero.';     
            }


            if(confirm(confirm_msg)) {
                return true;
            } else {
                return false;
            }
                
            return false;
        });

        $('#whole-suborders').click(function(){
            if($('#whole-suborders').is(':checked') == true) {
                $('.whole-suborder').removeClass('hidden');
                $('#single-products').prop('checked', false);
                $('#multiple-products').prop('checked', false);
                $('.single-product').addClass('hidden');
                $('.single_suborder_input').val('');
                $('.whole_suborder_input').val('');
                $('.order_product_check').addClass('hidden');
                $('.number_order').removeClass('hidden');
                $('.order_product_check').prop('checked', false);
            } else {
                $('.whole-suborder').addClass('hidden');
                $('.order_product_check').addClass('hidden');
                $('.single_suborder_input').val('');
                $('.whole_suborder_input').val('');
                $('.number_order').removeClass('hidden');
                $('.order_product_check').prop('checked', false);
            }
            
        });

        $('#multiple-products').click(function(){
            if($('#multiple-products').is(':checked') == true) {
                $('.whole-suborder').removeClass('hidden');
                $('#single-products').prop('checked', false);
                $('#whole-suborders').prop('checked', false);
                $('.single-product').addClass('hidden');
                $('.single_suborder_input').val('');
                $('.whole_suborder_input').val('');
                $('.order_product_check').removeClass('hidden');
                $('.number_order').addClass('hidden');
                $('.order_product_check').prop('checked', false);
            } else {
                $('.whole-suborder').addClass('hidden');
                $('.single_suborder_input').val('');
                $('.whole_suborder_input').val('');
                $('.order_product_check').addClass('hidden');
                $('.number_order').removeClass('hidden');
                $('.order_product_check').prop('checked', false);
            }
            
        });

        $('#single-products').click(function(){
            if($('#single-products').is(':checked') == true) {
                $('.single-product').removeClass('hidden');
                $('#multiple-products').prop('checked', false);
                $('#whole-suborders').prop('checked', false);
                $('.whole-suborder').addClass('hidden');
                $('.single_suborder_input').val('');
                $('.whole_suborder_input').val('');
                $('.order_product_check').addClass('hidden');
                $('.number_order').removeClass('hidden');
                $('.order_product_check').prop('checked', false);
            } else {
                $('.single-product').addClass('hidden');
                $('.single_suborder_input').val('');
                $('.whole_suborder_input').val('');
                $('.order_product_check').addClass('hidden');
                $('.number_order').removeClass('hidden');
                $('.order_product_check').prop('checked', false);
            }
            
        });
        $('.btn-refresh-paycharge1').click(function() {
            var txt_msg = "Are you sure you want to refresh paycharge discount?";
            <?php
            if($is_custom_discount_applied) {
                ?>
                txt_msg += "Some products has customly applied discount, So, this operation may change custom discounts on products.";
                <?php
            }
            ?>
            
            if(confirm(txt_msg)) {
               return true;
            } else {
                return false;
            }
        });
    });
</script>