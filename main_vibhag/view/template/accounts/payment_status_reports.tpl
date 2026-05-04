<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="container-fluid col-sm-6">
        <form action="index.php?route=accounts/paymentsreports&token=<?php echo $token;?>" method="post" enctype="multipart/form-data" id="form-order">
              <h2><strong><?php echo $seller_name; ?></strong>
              <span><input type="submit" id="button-update-filter" class="btn btn-primary pull-right" value="<?php echo $button_update; ?>"></span></h2>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="input-order-no"><?php echo $text_transaction_no; ?></label>
                        <input type="text" name="transaction_ref_no" value="" placeholder="<?php echo 'Transaction Ref. No.'; ?>" id="input-order-no" class="form-control" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="input-date-added"><?php echo $text_date; ?></label>
                        <div class="input-group date">
                            <input type="text" name="update_date" value="" placeholder="<?php echo 'Date'; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                              <span class="input-group-btn">
                              <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                              </span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="table-responsive">
                <input type="hidden" value="<?php echo $seller_id; ?>" name="hidden_seller_id" />
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <td style="width: 2px;" class="text-center">
                                <input type="checkbox" onclick="$('input[name*=\'order_id\']').prop('checked', this.checked);" />
                            </td>
                            <td class="text-center">
                                <?php echo $text_order_no; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $text_pymt_val; ?>
                            </td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                              if (!empty($order_lists)) {
                              foreach ($order_lists as $list) {
                            ?>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="order_id[]" value="<?php echo $list['order_id']; ?>" />
                            </td>
                            <td class="text-center">
                              <a href="<?php echo $list['view']; ?>" target="_blank"><strong><?php echo $list['order_no']; ?></strong></a></td>
                            <td class="text-center"><?php echo $list['payment_value']; ?></td>
                        </tr>
                        <?php } ?>
                        <?php }else{ ?>
                        <tr>
                            <td class="text-center" colspan="8"><?php echo "No results!"; ?></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
        </form>
    </div>
</div>
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript">
</script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript">
$('.date').datetimepicker({
    pickTime: false
});
</script>
<?php // echo $footer; ?>
