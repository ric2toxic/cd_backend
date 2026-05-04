<?php if ($is_operation_admin || $is_admin) {
    ?>
    <div class="row tabs hidden edit_break_suborder">

        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
        <input type="hidden" name="suborder_id" value="<?php echo $suborder_id; ?>" />

        <div class="col-lg-12">

            <button class="btn btn-primary pull-right clickbtn commonBtn" data-loading-text="Loading..." type="submit" id="button-break" data-toggle="modal" data-target="#myModal" style="margin-right:10px"><i class="fa fa-plus-circle"></i> Break Suborder</button>
            <?php
            if (!empty($suborder_id_arrs)) {
                ?>
                <button class="btn btn-warning pull-right clickbtn commonBtn" data-loading-text="Loading..." type="submit" id="button-move" data-toggle="modal" data-target="#myModal" style="margin-right:10px"><i class="fa fa-plus-circle"></i> Move Products</button>
                <?php
            }
            if (empty($is_buyer_invoice_generated) && $is_sets_edit) {
                ?>
                <button class="btn btn-success pull-right clickbtn1 commonBtn" data-loading-text="Loading..." type="submit" id="button-break-sets" style="margin-right:10px"><i class="fa fa-plus-circle"></i> Break Sets</button>

                <?php
                if (!empty($suborder_id_arrs)) {
                    ?>
                    <button class="btn btn-info pull-right clickbtn1 commonBtn" data-loading-text="Loading..." type="submit" id="button-move-sets" style="margin-right:10px"><i class="fa fa-plus-circle"></i> Move Sets</button>    
                    <?php
                }
            }
            $temp = '';
            $invoice_head_temp = '';

            if (!empty($break_suborder_products)) {
                $invoice_heading = '';
                foreach ($break_suborder_products as $key => $seller_id_wise_arr) {
                    $disable_select_product = '';
                    if ($key == 'seller_not_invoiced') {
                        $invoice_heading = 'Uninvoiced Products';
                    } else if ($key == 'seller_invoiced') {
                        $invoice_heading = 'Invoiced Products';
                        $disable_select_product = 'disabled="disabled"';
                    }

                    if (!empty($seller_id_wise_arr)) {
                        if ($key == 'seller_invoiced') {
                            ?>
                            <br>
                            <h4><b><?php echo $invoice_heading; ?></b></h4>
                            <?php
                            foreach ($seller_id_wise_arr as $seller_invoice_id => $seller_product_info) {
                                if (!empty($seller_product_info)) {
                                    foreach ($seller_product_info as $seller_id => $products_details) {
                                        $products_details_count = count($products_details);
                                        $x = 1;
                                        foreach ($products_details as $product) {
                                            if ($temp != $key . '_' . $seller_id) {
                                                ?>
                                                <table style="width:100%;" border="0">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <h4><?php echo $product['company'] . '-' . $product['nickname']; ?></h4>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <?php
                                            }
                                            $total_piece = ($product['quantity'] * $product['piece_in_set']);
                                            $total_amount = ((float) $product['price_per_piece'] + (float) $product['discount_per_piece']) * $total_piece;
                                            if ($x == 1) {
                                                ?>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-left"><input type="checkbox" value="<?php echo $seller_id; ?>" class="check_seller check_seller_<?php echo $key; ?>" name="check_seller<?php echo $seller_id; ?>" data-invoice="<?php echo $key; ?>"></th>
                                                            <th class="text-left">SKU</th>
                                                            <th class="text-right">Sets</th>
                                                            <th class="text-right">Total Pieces</th>
                                                            <th class="text-right">Total Amount (Ex. Tax)</th>
                                                            <?php
                                                            if ($is_sets_edit) {
                                                                ?>
                                                                <th class="text-right hide qty_input">Break/Move Sets Qty</th>
                                                            <?php } ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td class="text-left"><input type="checkbox" name="check_product_seller<?php echo $seller_id; ?>" value="<?php echo $product['order_product_id']; ?>" dir="<?php echo $product['product_id']; ?>" data-seller="<?php echo $seller_id; ?>" data-invoice="<?php echo $key; ?>" class="order_product_check order-product_<?php echo $key; ?> " data-qty="<?php echo $product['quantity']; ?>" data-combo-product-id = "<?php echo $product['combo_product_id']; ?>" <?php echo $disable_select_product; ?>></td>
                                                        <td class="text-left">
                                                            <b><?php echo $product['model']; ?></b>
                                                            <br>
                                                            <i style="font-size:10px">
                                                                <?php echo $product['name']; ?>
                                                            </i>
                                                        </td>
                                                        <td class="text-right"><?php echo $product['quantity']; ?></td>
                                                        <td class="text-right"><?php echo $total_piece; ?></td>
                                                        <td class="text-right"><?php echo $total_amount; ?></td>
                                                        <?php
                                                        if ($product['quantity'] > 1 && $is_sets_edit && ($product['seller_invoice_id'] == 0 || $product['seller_invoice_id'] == NULL)) {
                                                            ?>
                                                            <td class="text-right hide qty_input"><input type="number" name="qty[<?php echo $product['order_product_id']; ?>]" value="" id="order_product_<?php echo $product['order_product_id']; ?>" min="1" max="<?php echo ($product['quantity'] - 1); ?>" class="form-control check_product_checked order_product_<?php echo $product['combo_product_id']; ?>" disabled="disabled" title="Please select this product for edit break/move sets." data-seller="<?php echo $seller_id; ?>" data-invoice="<?php echo $key; ?>" required="required" data-combo-product-id="<?php echo $product['combo_product_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>" /></td>
                                                            <?php
                                                        }
                                                        ?>

                                                    </tr>
                                                    <?php
                                                    if ($x == $products_details_count) {
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <?php
                                            }
                                            $x++;
                                            $temp = $key . '_' . $seller_id;
                                        }
                                    }
                                }
                                $invoice_head_temp = $key;
                            }
                        } else {
                            ?>
                            <br>
                            <h4><b><?php echo $invoice_heading; ?></b></h4>
                            <?php
                            foreach ($seller_id_wise_arr as $seller_id => $products_details) {
                                
                                if (!empty($products_details)) {
                                    $products_details_count = count($products_details);
                                    $x = 1;
                                    foreach ($products_details as $product) {
                                        if ($temp != $key . '_' . $seller_id) {
                                            ?>
                                            <table style="width:100%;" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <h4><?php echo $product['company'] . '-' . $product['nickname']; ?></h4>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        $total_piece = ($product['quantity'] * $product['piece_in_set']);
                                        $total_amount = ((float) $product['price_per_piece'] + (float) $product['discount_per_piece']) * $total_piece;
                                        if ($x == 1) {
                                            ?>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="text-left"><input type="checkbox" value="<?php echo $seller_id; ?>" class="check_seller check_seller_<?php echo $key; ?>" name="check_seller<?php echo $seller_id; ?>" data-invoice="<?php echo $key; ?>"></th>
                                                        <th class="text-left">SKU</th>
                                                        <th class="text-right">Sets</th>
                                                        <th class="text-right">Total Pieces</th>
                                                        <th class="text-right">Total Amount (Ex. Tax)</th>
                                                        <?php
                                                        if ($is_sets_edit) {
                                                            ?>
                                                            <th class="text-right hide qty_input">Break/Move Sets Qty</th>
                                                        <?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                }
                                                ?>
                                                <tr>
                                                    <td class="text-left"><input type="checkbox" name="check_product_seller<?php echo $seller_id; ?>" value="<?php echo $product['order_product_id']; ?>" dir="<?php echo $product['product_id']; ?>" data-seller="<?php echo $seller_id; ?>" data-invoice="<?php echo $key; ?>" class="order_product_check order-product_<?php echo $key; ?> combo_product_<?php echo $product['combo_product_id']; ?>" data-qty="<?php echo $product['quantity']; ?>" data-combo-product-id = "<?php echo $product['combo_product_id']; ?>" <?php echo $disable_select_product; ?>></td>
                                                    <td class="text-left">
                                                        <b><?php echo $product['model']; ?></b>
                                                        <br>
                                                        <i style="font-size:10px">
                                                            <?php echo $product['name']; ?>
                                                        </i>
                                                    </td>
                                                    <td class="text-right"><?php echo $product['quantity']; ?></td>
                                                    <td class="text-right"><?php echo $total_piece; ?></td>
                                                    <td class="text-right"><?php echo $total_amount; ?></td>
                                                    <?php
                                                    if ($product['quantity'] > 1 && $is_sets_edit && ($product['seller_invoice_id'] == 0 || $product['seller_invoice_id'] == NULL)) {
                                                        ?>
                                                        <td class="text-right hide qty_input"><input type="number" name="qty[<?php echo $product['order_product_id']; ?>]" value="" id="order_product_<?php echo $product['order_product_id']; ?>" min="1" max="<?php echo ($product['quantity'] - 1); ?>" class="form-control check_product_checked order_product_<?php echo $product['combo_product_id'] ?>" disabled="disabled" title="Please select this product for edit break/move sets." data-seller="<?php echo $seller_id; ?>" data-invoice="<?php echo $key; ?>" required="required" data-combo-product-id="<?php echo $product['combo_product_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>" /></td>
                                                        <?php
                                                    }
                                                    ?>

                                                </tr>
                                                <?php
                                                if ($x == $products_details_count) {
                                                    ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        $x++;
                                        $temp = $key . '_' . $seller_id;
                                    }
                                }
                                $invoice_head_temp = $key;
                            }
                        }   
                        
                    }
                }
            }
            ?>
            <button class="btn btn-success pull-right clickbtn save-changes hide" data-loading-text="Loading..." type="submit" id="button-move-sets-btn" data-toggle="modal" data-target="#myModal" style="margin-right:10px"><i class="fa fa-plus-circle"></i> Save Changes</button>
            <button class="btn btn-success pull-right clickbtn save-changes hide" data-loading-text="Loading..." type="submit" id="button-break-sets-btn" data-toggle="modal" data-target="#myModal" style="margin-right:10px"><i class="fa fa-plus-circle"></i> Save Changes</button>
            <a class="btn btn-danger hide pull-right reset_btn"
               onclick="resetChanges()" style="margin-right:10px">
                <i class="fa fa-reply"></i>
            </a>
        </div>

        <div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript">
        function resetChanges(obj, id) {
            $('.commonBtn').removeClass('hide');
            $('.qty_input').addClass('hide');
            $('.save-changes').addClass('hide');
            $('.reset_btn').addClass('hide');
            $('input[type=checkbox]').prop('checked', false);
            $('input[type=number]').attr('disabled', true);
            $('input[type=number]').val('');
            $('input[type=number]').attr('title', "Please select this product for edit break/move sets.");
            
            var seller_id_arr = [];
            $(".order_product_check").each(function () {
                var data_qty = Number($(this).attr('data-qty'));
                var data_invoice = $(this).attr('data-invoice');
                var curr_seller = $(this).attr('data-seller');
                if (data_invoice == 'seller_invoiced') {
                    if (seller_id_arr.indexOf(curr_seller) == -1) {
                        seller_id_arr.push(curr_seller);
                    }
                    $(this).attr('disabled', false);
                }
                if (data_qty <= 1 && data_invoice == 'seller_not_invoiced') {
                    
                    if (seller_id_arr.indexOf(curr_seller) == -1) {
                        seller_id_arr.push(curr_seller);
                    }
                    $(this).attr('disabled', false);
                }
            });

            $.each(seller_id_arr, function (key, value) {
                $('input[name=check_seller'+value+']').attr('disabled', false);
            });
        }

        $(document).ready(function () {
            $('.check_product_checked').change(function () {
                var curr_val = ($(this).val());
                var check_max = Number($(this).attr('max'));
                var check_min = 1;
                var combo_product_id = $(this).attr('data-combo-product-id');
                var product_id = $(this).attr('data-product-id');
                if (curr_val == 0 || (curr_val > check_max)) {
                    alert("You can asign max set " + check_max + " and minimum 1.");
                    $(this).val('');
                    if (combo_product_id != product_id) {
                        $('.order_product_'+combo_product_id).val('');
                    }
                    return false;
                }
                if (combo_product_id != product_id) {
                    $('.order_product_'+combo_product_id).val(curr_val);
                }
            });
            $('.check_seller').click(function () {
                var seller_id = $(this).val();
                var invoice = $(this).attr('data-invoice');
                var inner_seller_id = '';
                var inner_invoice = '';
                if ($(this).is(':checked') == true) {
                    $('.order-product_' + invoice).each(function (index, value) {
                        inner_seller_id = $(value).attr('data-seller');
                        inner_invoice = $(value).attr('data-invoice');
                        if (inner_seller_id == seller_id && inner_invoice == invoice) {
                            $(".order-product_" + invoice + "[data-seller=" + seller_id + "]").prop('checked', true);
                        }

                    });

                    $('.check_product_checked').each(function (index, value) {
                        inner_seller_id = $(value).attr('data-seller');
                        inner_invoice = $(value).attr('data-invoice');
                        if (inner_seller_id == seller_id && inner_invoice == 'seller_not_invoiced') {
                            $(value).attr('disabled', false);
                            $(value).removeAttr("title");
                        }

                    });
                } else {
                    $(".order-product_" + invoice + "[data-seller=" + seller_id + "]").prop('checked', false);
                    $(".check_product_checked[data-seller=" + seller_id + "]").attr('disabled', true);
                    $(".check_product_checked[data-seller=" + seller_id + "]").val('');
                    $(".check_product_checked[data-seller=" + seller_id + "]").attr('title', "Please select this product for edit break/move sets.");
                }
            });


            $('.order-product_seller_not_invoiced, .order-product_seller_invoiced').click(function () {
                var seller_id1 = $(this).attr('data-seller');
                var invoice = $(this).attr('data-invoice');
                var combo_product_id = $(this).attr('data-combo-product-id');
                var product_id = $(this).attr('dir');
                var flag = true;
                var order_products = $(this).val();
                if ($(this).is(':checked') == true) {
                    $('#order_product_' + order_products).attr('disabled', false);
                    $('#order_product_' + order_products).removeAttr("title");

                    if(combo_product_id != product_id) {
                        $('.combo_product_'+combo_product_id).prop('checked', true);
                        $('.order_product_' + combo_product_id).attr('disabled', false);
                        $('.order_product_' + combo_product_id).removeAttr("title");
                    }
                } else {
                    $('#order_product_' + order_products).val('');
                    $('#order_product_' + order_products).attr('disabled', true);
                    $('#order_product_' + order_products).attr('title', "Please select this product for edit break/move sets.");

                    if(combo_product_id != product_id) {
                        $('.combo_product_'+combo_product_id).prop('checked', false);
                        $('.order_product_' + combo_product_id).attr('disabled', true);
                        $('.order_product_' + combo_product_id).attr("title", "Please select this product for edit break/move sets.");
                    }
                    
                }

                $(".order-product_" + invoice + "[data-seller=" + seller_id1 + "]").each(function (index, value) {
                    if ($(value).is(':checked') == false) {
                        flag = false;
                    }
                });

                if (flag == false) {
                    $(".check_seller_" + invoice + "[value=" + seller_id1 + "]").prop('checked', false);
                } else {
                    $(".check_seller_" + invoice + "[value=" + seller_id1 + "]").prop('checked', true);
                }
            });


            var order_id = '<?php echo $order_id ?>';
            var suborder_id = '<?php echo $suborder_id ?>';
            $('.clickbtn1').click(function () {
                $('.commonBtn').addClass('hide');
                $('.qty_input').removeClass('hide');
                if ($(this).attr('id') == 'button-break-sets') {
                    $('#button-break-sets-btn').removeClass('hide');
                } else {
                    $('#button-move-sets-btn').removeClass('hide');
                }

                $('.reset_btn').removeClass('hide');
                var seller_check_disable = 0;
                var seller_id_arr = [];
                $(".order_product_check").each(function () {
                    var data_qty = Number($(this).attr('data-qty'));
                    var d_invoice = $(this).attr('data-invoice');
                    var curr_seller = $(this).attr('data-seller');
                    var combo_product_id = $(this).attr('data-combo-product-id');
                    var product_id = $(this).attr('dir');
                    if (d_invoice == 'seller_invoiced') {
                        if (seller_id_arr.indexOf(curr_seller) == -1) {
                            seller_id_arr.push(curr_seller);
                        }
                        $(this).attr('disabled', true);
                        $(this).prop('checked', false);
                    }
                    if (data_qty <= 1) {
                        
                        if (seller_id_arr.indexOf(curr_seller) == -1) {
                            seller_id_arr.push(curr_seller);
                        }
                        $(this).attr('disabled', true);
                        $(this).prop('checked', false);
                    }
                    if(combo_product_id != product_id) {

                    }
                });

                $.each(seller_id_arr, function (key, value) {
                    $('input[name=check_seller'+value+']').attr('disabled', true);
                    $('input[name=check_seller'+value+']').prop('checked', false);
                });

            });
            $('.clickbtn').click(function () {
                var is_buyer_invoice_generated = '<?php echo $is_buyer_invoice_generated; ?>';
                var order_product_id = [];
                var order_product_count = $('.order_product_check').length;
                var order_product_checked_count = 0;
                var is_move_suborder = 0;
                var sets_qty = {};
                var is_break_move_peice = 0;
                var is_move_further = 1;
                if (($(this).attr('id') == 'button-move') || ($(this).attr('id') == 'button-move-sets-btn')) {
                    is_move_suborder = 1;
                }
                if ($(this).attr('id') == 'button-break-sets-btn' || $(this).attr('id') == 'button-move-sets-btn') {
                    is_break_move_peice = 1;
                }
                $(".order_product_check").each(function () {
                    if ($(this).is(':checked') == true) {
                        var cur_order_prosuct_id = $(this).val();
                        if(is_break_move_peice==1) {
                            var cur_qty_val = $("#order_product_"+cur_order_prosuct_id).val();
                            if(cur_qty_val == '') {
                                $("#order_product_"+cur_order_prosuct_id).focus();
                                is_move_further = 0;
                            }
                        }
                        if (order_product_id.indexOf(cur_order_prosuct_id) == -1) {

                            order_product_id.push(cur_order_prosuct_id);
                            if (is_break_move_peice == 1) {
                                sets_qty[cur_order_prosuct_id] = $('#order_product_' + cur_order_prosuct_id).val();
                            }

                        }
                        order_product_checked_count++;
                    }
                });
                if(is_move_further == 0) {
                    alert("Sets quantity cannot be empty. Please enter sets quantity for move/break.");
                    return false;
                }
                if (is_buyer_invoice_generated == 1) {
                    if (is_break_move_peice == 1) {
                        alert("You canot Break/Move sets after invoice generated.");
                        return false;
                    }
                    if (confirm('Do you really want to split after generating invoice?')) {
                    } else {
                        return false;
                    }
                }
                if ((order_id == '') || (suborder_id == '')) {
                    alert("Invalid order id or suborder id.");
                    return false;
                } else if (order_product_id == '') {
                    alert("Invalid order product. Please select atleast one product for split into another suborder.");
                    return false;
                } else if ((order_product_count == order_product_checked_count) && is_break_move_peice == 0) {
                    alert("You cannot split all product into another suborder.");
                    return false;
                } else {
                    order_id = '<?php echo $order_id; ?>';
                    suborder_id = '<?php echo $suborder_id; ?>';
                    $.ajax({
                        type: 'post',
                        url: 'index.php?route=sale/edit_order/suborderBreakConfirm&token=<?php echo $token; ?>',
                        data: {'order_id': order_id, 'suborder_id': suborder_id, 'order_product_id': order_product_id, 'is_move_suborder': is_move_suborder, 'is_break_move_peice': is_break_move_peice, 'sets_qty': sets_qty},
                        success: function (data) {
                            $('#myModal .modal-content .modal-body').html(data);
                        }
                    });
                }

            });

        });
    </script>
<?php }
?>
