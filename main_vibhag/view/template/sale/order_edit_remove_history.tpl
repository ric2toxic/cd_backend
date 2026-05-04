<?php if ( ( $is_admin || $is_operation_admin) && !empty($order_history)) { ?>
    <div class="row tabs hidden edit_remove_history" style="color: #666666;">
        <form action="<?php echo $save; ?>" method="post">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
            <input type="hidden" name="suborder_id" value="<?php echo $suborder_id; ?>" />



            <div class="col-lg-12">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="7.5%">&nbsp;</th>
                            <th width="12.5%">Order Status</th>
                            <th width="20%">Comment</th>
                            <th width="20%">Notes</th>
                            <th width="10%">Notify Email</th>
                            <th width="10%">Notify SMS</th>
                            <th width="10%">User</th>
                            <th width="10%">Date Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_history as $order_history_id => $history_item) { ?>
                            <tr>
                                <td width="7.5%"><input type="checkbox" name="history_id[]" value="<?php echo $order_history_id; ?>" class="remove_his" /></td>
                                <td width="12.5%"><?php echo $history_item['order_status_name']; ?></td>
                                <td width="20%"><?php echo $history_item['comment']; ?></td>
                                <td width="20%"><?php echo $history_item['notes']; ?></td>
                                <td width="10%"><?php echo $history_item['notify_email']; ?></td>
                                <td width="10%"><?php echo $history_item['notify_sms']; ?></td>
                                <td width="10%"><?php echo $history_item['user']; ?></td>
                                <td width="10%"><?php echo $history_item['date_added']; ?></td>
                            </tr>
                        <?php } ?> 

                    </tbody>
                </table>
                <br>
                <div class="submit">
                    <div class="col-sm-6">
                        <label class="col-sm-2">Comment:<span style="color: red">*</span> </label>
                        <div class="col-sm-8">
                        <textarea name="comments" class="form-control" required="required" title="comment" placeholder="Comment" id="remove-comment_his"></textarea> 
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <input type="hidden" name="old_order_history" value='<?php echo base64_encode(serialize($order_history)); ?>'  />
                        <button type="subimt" class="btn btn-primary pull-right clearfix remove-btns" id="remove-btn" data-txt="order history item/s" data-vali="history item" data-id-check="his">
                        <i class="fa fa-save"></i>  Remove
                        </button>
                    </div>
                    
                    <br><br><br><br>
                </div>
            </div>
        </form>

        <!--Already deleted order history item-->
        <?php 
        echo $deleted_order_history;
        ?>
    </div>
<?php } ?>