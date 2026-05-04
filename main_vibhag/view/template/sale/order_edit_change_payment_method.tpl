<div class="row tabs hidden edit_change_payment_method">
    <form id="change_payment_method" action="<?php echo $save; ?>" method="post">
        <input type="hidden" name="order_id" id="payment_method_order_id" value="<?php echo $order_id; ?>" />
        <input type="hidden" name="suborder_id" value="<?php echo $suborder_id; ?>" />

        <div class="col-lg-6">
            <select class="form-control payment_code" name="payment_method[new]">
                <option>SELECT PAYMENT CODE</option>
                <?php foreach ($payment_methods as $key => $payment_method) { ?>
                    <option value="<?php echo $key; ?>"
                            <?php echo ( trim(strtolower($order['payment_code'])) == trim($key) ? ' selected ' : '' ); ?> >
                                <?php echo $payment_method; ?>
                    </option>
                <?php } ?>
            </select>
            <input type="hidden" name="payment_method[old]" value="<?php echo $order['payment_code']; ?>" /><br>
            <div class="checkbox hidden">
                <label>
                    <input type="checkbox" name="apply_discounts" value="1" /> Do You want to apply discounts..??
                </label>
            </div>
            <div class="submit hidden">
                <button type="subimt" class="btn btn-primary pull-right clearfix check-credits">
                    <i class="fa fa-save"></i>  Save
                </button>
            </div>
        </div>
    </form>
</div>