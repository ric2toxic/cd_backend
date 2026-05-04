

<form action="<?php echo $save; ?>" method="post">
    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
    <input type="hidden" name="suborder_id" value="<?php echo $suborder_id; ?>" />
    <input type="hidden" name="is_break_move_peice" value="<?php echo $is_break_move_peice; ?>" />
    <div class="row" style=" border-bottom: 1px solid #e5e5e5;"> 
        <div class="col-sm-12 " style="margin:0 0 10px 0;">

            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Make more changes</button>
            <button type="submit" class="btn btn-default pull-right">Confirm</button>
            <div style="clear:both"></div>
        </div>
    </div>
    <div class="row" style=" border-bottom: 1px solid #e5e5e5;"> 
        <div class="col-sm-12 " style="margin:10px 0 10px 0;">

            <span class="pull-left ">
                Revised Existing Invoice Total: <b><span class="old-invoice-amount"> </span></b>
            </span>
            <span class="pull-right ">
                New Invoice Total: <b><span class="new-invoice-amount"></span></b>
            </span>
            <div style="clear:both"></div>
        </div>
    </div>
    <div class="row" style="padding-top: 10px;">
        <?php
        if (!empty($suborder_id_arrs) && $is_move_suborder) {
            ?>
            <div class="col-sm-6" style="margin:10px 0;">
                <input type="hidden" name="is_move_suborder" value="<?php echo $is_move_suborder; ?>" />
                <select name="move_suborder_id" class="form-control" id="move_suborder_id" required="required">
                    <option value="">---Select Suborder---</option>
                    <?php
                    foreach ($suborder_id_arrs as $value) {
                        ?>
                        <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                        <?php
                    }
                    ?>

                </select>
            </div>
            <?php
        }
        ?>
        <div class="col-sm-12" style="overflow-x: scroll;">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-left">Sr. No.</th>
                        <th class="text-left">SKU</th>
                        <th class="text-left">Pickup Status</th>
                        <th class="text-right">Sets</th>
                        <th class="text-right">Total Pieces</th>
                        <th class="text-right">Total Amount (Ex. Tax)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $x = 1;
                    $order_product_ids = '';
                    foreach ($products as $product) {
                        $pickup_date = '';
                        if ($product['pickup_last_modified'] != '') {
                            $pickup_date = ' (' . DATE('d-m-Y', strtotime($product['pickup_last_modified'])) . ') ';
                        }
                        
                        if (in_array($product['order_product_id'], $order_product_id)) {
                            if ($is_break_move_peice) {
                                $product['quantity'] = $sets_qty[$product['order_product_id']];
                            }
                            $total_pieces = $product['quantity'] * $product['piece_in_set'];
                            $total_amount_per_product = $total_pieces * ($product['price_per_piece']+$product['discount_per_piece']);
                            $order_product_ids .= $product['order_product_id'] . ',';
                            ?>
                            <tr>
                                <td class="text-left"><?php echo $x; ?></td>
                                <td class="text-left">
                                    <b><?php echo $product['model']; ?></b>
                                    <br>
                                    <i style="font-size:10px">
                                        <?php echo $product['name']; ?>                                                                </i>
                                </td>
                                <td class="text-right"><?php echo $product['pickup_status'] . $pickup_date; ?></td>
                                <td class="text-right"><?php echo $product['quantity']; ?></td>
                                <td class="text-right"><?php echo $total_pieces; ?></td>
                                <td class="text-right"><?php echo $total_amount_per_product; ?></td>
                            </tr>
                            <?php
                            $x++;
                        }
                    }
                    $order_product_ids = rtrim($order_product_ids, ',');
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <input type="hidden" name="order_product_id" value="<?php echo $order_product_ids; ?>" />
    <input type="hidden" name="sets_qty" value="<?php echo base64_encode(serialize($sets_qty)); ?>" />
</form>

<script>
    $(document).ready(function () {
        var order_id = '<?php echo $order_id; ?>';
        var suborder_id = '<?php echo $suborder_id; ?>';
        var sets_qty = '';
        var new_move_suborder_id = '';
        var is_break_move_peice = '<?php echo $is_break_move_peice; ?>';
        var is_move_suborder = '<?php echo $is_move_suborder; ?>';
<?php
if ($is_break_move_peice) {
    ?>
            sets_qty = '<?php echo base64_encode(serialize($sets_qty)); ?>';
    <?php
}
?>

        var order_product_id = '<?php echo $order_product_ids; ?>';

        $.ajax({
            type: 'post',
            url: 'index.php?route=sale/edit_order/calculateInvoiceAmounts&token=<?php echo $token; ?>',
            dataType: 'JSON',
            data: {'order_id': order_id, 'suborder_id': suborder_id, 'new_move_suborder_id': new_move_suborder_id, 'order_product_id': order_product_id, 'sets_qty': sets_qty, 'is_break_move_peice': is_break_move_peice, 'is_move_suborder': is_move_suborder},
            success: function (data) {
                $('.new-invoice-amount').text(data.total_invoice_amount_new);
                $('.old-invoice-amount').text(data.total_invoice_amount_old);
            }
        });
        $('#move_suborder_id').change(function () {
            new_move_suborder_id = $(this).val();
            $.ajax({
                type: 'post',
                url: 'index.php?route=sale/edit_order/calculateInvoiceAmounts&token=<?php echo $token; ?>',
                dataType: 'JSON',
                data: {'order_id': order_id, 'suborder_id': suborder_id, 'new_move_suborder_id': new_move_suborder_id, 'order_product_id': order_product_id, 'sets_qty': sets_qty, 'is_break_move_peice': is_break_move_peice, 'is_move_suborder': is_move_suborder},
                success: function (data) {
                    $('.new-invoice-amount').text(data.total_invoice_amount_new);
                    $('.old-invoice-amount').text(data.total_invoice_amount_old);
                }
            });
        });
    });
</script>