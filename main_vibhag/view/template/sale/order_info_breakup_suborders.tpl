
<table class="table table-bordered">
    <tbody>
        <?php echo $breakup_product_info; ?>
    </tbody>
    <div class="pull-right" style="margin-bottom: 5px;">
        <button class="btn btn-warning" data-loading-text="Loading..." type="submit" id="button-not-supplied"><i class="fa fa-plus-circle"></i> Mark Not Supplied </button>
        <?php
        $admin_id_arr = explode(',', ADMIN_IDS);
        if($payment_code!='' && ($payment_code!='cod' || in_array($logged_user_id, $admin_id_arr) )) {
            ?>
            <button class="btn btn-primary" data-loading-text="Loading..." type="submit" id="button-break"><i class="fa fa-plus-circle"></i> Break Suborder</button>
            <?php
        }
        ?>
        
    </div>
</table>
<script type="text/javascript">
    $(document).ready(function () {
        
        $('.check_seller').click(function(){
            //console.log($(this).is(':checked'));
            var seller_id = $(this).val();
            var inner_seller_id ='';
            if($(this).is(':checked') == true) {
                $('.order-product').each(function(index, value){
                    inner_seller_id = $(value).attr('data-seller');
                        if(inner_seller_id == seller_id) {
                            $(".order-product[data-seller="+seller_id+"]").prop('checked',true);
                        }                 

                });
            } else {
                $(".order-product[data-seller="+seller_id+"]").prop('checked',false);
            } 
            /*else {
                $('.order-product').each(function(){
                    $('.order-product').prop('checked',false);
                });
            }*/
        });
        
        
        $('.order-product').click(function(){
            var seller_id1 = $(this).attr('data-seller');
            var flag = true;
            $(".order-product[data-seller="+seller_id1+"]").each(function(index, value){
                if($(value).is(':checked') == false) {
                        flag = false;
                }                 
            });

            if(flag == false) {
               $(".check_seller[value="+seller_id1+"]").prop('checked', false);
            } else {
                $(".check_seller[value="+seller_id1+"]").prop('checked', true);
            }
        });
        
       
        var order_id = '<?php echo $order_id ?>';
        var suborder_id = '<?php echo $suborder_id ?>'
        $('#button-break').click(function () {
            $('#button-break').hide();
            var flag = true;
            var order_product_id = [];
            $(".order-product").each(function () {
                if ($(this).is(':checked') == true) {
                    if (order_product_id.indexOf($(this).val()) == -1) {
                        order_product_id.push($(this).val());
                    }
                } else {
                    flag = false;
                }
            });
            if (flag == false) {
                alert("Please clear all products either Seller Not Supplied or Break into another Suborder.");
                $('#button-break').show();
                return false;
            } else if ((order_id == '') || (suborder_id == '')) {
                alert("Invalid order id and suborder id.");
                $('#button-break').show();
                return false;
            } 
            else {
                $.ajax({
                    type: 'post',
                    url: 'index.php?route=sale/order/updateBreakedSuborderProduct&token=<?php echo $token; ?>',
                    data:{'order_id': order_id, 'suborder_id': suborder_id, 'order_product_ids':order_product_id},
                    success: function (data) {
                        if(data.success == 'Successful updated the order product field') {
                            alert("Successful updated");
                            window.location.reload();
                        }
                        window.location.reload();
                    }
                });
            }

        });
        
        $('#button-not-supplied').click(function () {
            var flag = false;
            var order_product_id = [];
            var product_id = [];
            $(".order-product").each(function () {
                if ($(this).is(':checked') == true) {
                    flag = true;
                    if (order_product_id.indexOf($(this).val()) == -1) {
                        order_product_id.push($(this).val());
                    }
                    if (product_id.indexOf($(this).attr('dir')) == -1) {
                        product_id.push($(this).attr('dir'));
                    }
                }
            });
            if (flag == false) {
                alert("Please select atleast one product to mark Seller Not Supplied.");
                $('#button-not-supplied').show();
                return false;
            } else if ((order_id == '') || (suborder_id == '')) {
                alert("Invalid order id and suborder id.");
                return false;
            } 
            else {
                $.ajax({
                    type: 'post',
                    url: 'index.php?route=sale/order/updateNotSuplliedSuborderProduct&token=<?php echo $token; ?>',
                    data:{'order_id': order_id, 'suborder_id': suborder_id, 'order_product_ids':order_product_id, 'product_ids':product_id},
                    success: function (data) {
                        if(data.success == 'Successful updated the order product field') {
                            alert("Successful updated");
                            window.location.reload();
                        }
                        window.location.reload();
                    }
                });
            }

        });
        
    });
</script>
