<style>
    .search_input_box{
        width: 70%;
        float: left;
    }  
</style>
<div class="row products_panel hidden tabs edit_add_product">
    <div class="col-sm-12">
        <div class="col-sm-5">
            <div class="search_input_box">
                <input type="text" id="mainsearch" name="search" class="form-control serch_input_box ui-autocomplete-input" placeholder="Search from more than 1 Lac Products..." autocomplete="off">
            </div>
            <div class="search_btn">
                <button class="btn btn-default btn-search" type="button" id="search_magnifier"><i class="fa fa-search" aria-hidden="true"></i></button>
            </div>

        </div>
        <div class="col-sm-7">
            <!--<span class="cart_amount" id="cart-total">0</span>-->
            <button class="btn btn-primary pull-right clickbtns" type="button" data-toggle="modal" data-target="#myModals">REVIEW & CONFIRM</button>
        </div>
    </div>
    <br><br><br>
    <div class="col-lg-12">
        <input type="hidden" form="product_form" name="order_id" value="<?php echo $order_id; ?>" />
        <input type="hidden" form="product_form" name="suborder_id" value="<?php echo $suborder_id; ?>" />
        <form action="<?php echo $save; ?>" id="product_add_form" onsubmit="return handleData(this)" method="post">
                        
        </form>
    </div>
    <div id="myModals" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content" style="overflow-x: scroll;">
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
    <style>
    .show_add_to_cart_msg {
        padding: 20px;
        width: 35%;
        text-align: center;
        position: fixed;
        top: 40%;
        left: 42%;
        z-index: 999999;
    }
    .cart_amount {
        background: #fff;
        border-radius: 50px;
        color: #d41a21;
        font-size: 11px;
        height: 24px;
        padding: 3px 4px 4px 4px;
        position: absolute;
        right: 10px;
        top: -7px;
        width: 22px;
        z-index: 999;
        border: 1px solid #ddd;
        box-shadow: 1px 1px 1px #888;
        text-align: center;
}
</style>
    <div class="show_add_to_cart_msg alert alert-success hidden">
        <p>You have added Product to Add/Review.</p>
        <p>For review and add, please click on "Add/Review" button!</p>
    </div>
    <br>
</div>

<script>
    $(document).ready(function () {
        $('#search_magnifier').click(function () {
            var search = $('#mainsearch').val();
            search = search.trim();
            if (search == '') {
                alert('Please search with some keywords');
                return false;
            } else {
                

            var path = '<?php echo (!empty($path) ? $path : ""); ?>';
            var order_id = '<?php echo $order_id; ?>';
            var suborder_id = '<?php echo $suborder_id; ?>';
            var page = '<?php echo (!empty($page) ? $page : ""); ?>';
            var sort_order = '<?php echo (!empty($sort_order) ? $sort_order : ""); ?>';
            var stock_filter = '<?php echo (!empty($stock_filter) ? $stock_filter : ""); ?>';
            var clearance_sale = '<?php echo (!empty($clearance_sale) ? $clearance_sale : ""); ?>';
            var last_filter_action = '<?php echo (!empty($last_filter_action) ? $last_filter_action : ""); ?>';
            var handpicked_ids = '<?php echo (!empty($handpicked_ids) ? $handpicked_ids : ""); ?>';
        

                $.ajax({
                    type: 'POST',
                    url: 'index.php?route=sale/edit_order/getProductLists&token=<?php echo $token; ?>',
                    data: {'order_id':order_id,'suborder_id':suborder_id,'search':search, 'path': path, 'page': page, 'sort_order':sort_order, 'stock_filter':stock_filter, 'clearance_sale':clearance_sale, 'last_filter_action':last_filter_action, 'handpicked_ids':handpicked_ids},
                    beforeSend: function() {  
                        $('#product_add_form').html(''); 
                        $('#search_magnifier').button('loading');
                    },
                    success: function (data) {
                        $('#product_add_form').html(data);
                    },
                    complete:  function() {
                        $('#search_magnifier').button('reset');
                    },
                    error: function() {
                        alert('Product Not Found...');
                        $('#search_magnifier').button('reset');
                    },
                });
            }
        });

        
    });
</script>
<script>
    var local_storage_product_detail = {};
    var final_json_obj = [];
    var cart_total_counter = 0;
    function add_item(product_id, is_options, product_option_id = 0) {
        
        var p_id = product_id;
        local_storage_product_detail[p_id] = {};
        if (product_id=='') {
            alert('Please select product');
            delete local_storage_product_detail[p_id];
            return false;
        }
        
        if ((is_options == 1) && (product_option_id=='')) {
            alert('Invalid product option');
            delete local_storage_product_detail[p_id];
            return false;
        }

        if (is_options == 0) {
            var cur_product_value = Number($('#option_input'+product_id).val());
            var model_name = $('#option_input'+product_id).attr('data-model-detail');
            if (cur_product_value==0 || cur_product_value=='') {
                alert('Product sets must not be empty or zero.');
                delete local_storage_product_detail[p_id];
                return false;
            }
            local_storage_product_detail[p_id]['qty'] = cur_product_value;
            local_storage_product_detail[p_id]['madel_name'] = model_name;
        } else {
            check_qty_zero = true;
            local_storage_product_detail[p_id][product_option_id] = {};
            $(".option_quantities_"+product_id+"_"+product_option_id).each(function (index, value) {
                var cur_product_value = Number($(value).val());
                var option_value_id = $(value).attr('data-option-value');
                var model_name = $(value).attr('data-model-detail');
                if (cur_product_value > 0) {
                    check_qty_zero = false;
                    if (!local_storage_product_detail[p_id][product_option_id][option_value_id]) {
                        local_storage_product_detail[p_id][product_option_id][option_value_id] = {};
                    }
                    local_storage_product_detail[p_id][product_option_id][option_value_id]['qty'] = cur_product_value;
                    local_storage_product_detail[p_id][product_option_id][option_value_id]['madel_name'] = model_name;
                }
            }); 
            if (check_qty_zero == true) {
                alert('Product Options sets must not be empty or zero.');
                delete local_storage_product_detail[p_id];
                return false;
            }
        }
        $(".show_add_to_cart_msg").removeClass('hidden').fadeIn(2000); 
        setTimeout(function() { $(".show_add_to_cart_msg").addClass('hidden').fadeOut(2000); }, 3000);
        cart_total_counter++;
        cart_counter();
        //console.log();
        
        console.log(local_storage_product_detail);
    }

    function cart_counter() {
        $('#cart-total').text('');
        $('#cart-total').text(cart_total_counter);
    }
    $('.clickbtns').click(function(){
        
        local_storage_product = JSON.stringify(local_storage_product_detail);
        if(local_storage_product.length > 0) {
            $.ajax({
                type: 'POST',
                url: 'index.php?route=sale/edit_order/addReviewProducts&token=<?php echo $token; ?>',
                data: {'local_storage_product_detail': local_storage_product_detail, 'order_id':'<?php echo $order_id; ?>', 'suborder_id': '<?php echo $suborder_id; ?>'},
                beforeSend:function(){
                    $('#myModals .modal-content .modal-body').html('');
                    $('.clickbtns').button('loading');
                },
                complete:function(){
                    $('.clickbtns').button('reset');
                },
                success: function (data) {
                    $('#myModals .modal-content .modal-body').html(data);            
                },
                error:function(e){
                    alert(e);
                    console.log(e.responseText);
                }
            });
        } else {
            $('#myModals .modal-content .modal-body').html('Please add product first');
            alert("Please add product first");
            return false;
        }
    });
        
        
    
</script>
<style type="text/css">
    .popup_color_size{
        width: 40px;
        height: 25px;
    }
    .view_more_popup{
        display: none;
    }
    .view_more_popup_block{
       border: 1px groove #000; position: absolute; top: 0; background: #fff; border-radius: 8px; width: 900px; margin: auto; display: none;box-shadow: 1px 1px 15px #000;
    }
    .view_more_popup_close{
        border: 1px groove #000;
        padding: 5px 10px;
        border-radius: 15px;
        margin-top: -15px;
        margin-right: -25px;
        background: #000;
        color: #fff;

    }
</style>