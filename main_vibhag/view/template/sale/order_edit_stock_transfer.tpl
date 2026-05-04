<?php if ($is_stock_transfer) { ?>
    <div class="row tabs hidden edit_stock_transfer">
        <form action="<?php echo $save; ?>" method="post">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
            <input type="hidden" name="suborder_id" value="<?php echo $suborder_id; ?>" />
            <input type="hidden" name="stock_transfer" value="1" >

            <div class="col-lg-4">
                <div class="submit">
                    <button type="subimt" class="btn btn-primary pull-right clearfix stock-transfer">
                        <i class="fa fa-save"></i>  Mark As Stock Transfer
                    </button>
                </div>
            </div>
        </form>
    </div>
<?php } ?>