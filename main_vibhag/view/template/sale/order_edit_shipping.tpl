
<div class="row tabs hidden edit_shipping">
    <form action="<?php echo $save; ?>" method="post">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
        <input type="hidden" name="suborder_id" value="<?php echo $suborder_id; ?>" />

        <div class="col-sm-6 pull-left">
            <select class="form-control shipping_code" name="shipping_code[new]">

            </select>
            <input type="hidden" name="shipping_code[old]" value="<?php echo $shipping_code; ?>" />
            <input type="hidden" name="shipping_method[old]" value="<?php echo $shipping_method; ?>" />
            <input type="hidden" name="shipping_method[new]" value="" /><br>
            <?php
            if(!empty($ess_charges)) {
                ?>
                <span>
                    <b> ESS Charges Already Applied:</b> <?php echo $ess_charges; ?>
                </span> <br>
                <span>
                    <b> Suborder Total Shipping Charge</b> <?php echo $shipping_charges; ?>
                </span>
                <br><br><br>
                <?php
                if(!empty($suborder_edit_history) && (!empty($is_operation_admin) || !empty($is_admin))) {
                    ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">&nbsp;</th>
                                <th width="30%">ESS Charges</th>
                                <th width="30%">Applied By</th>
                                <th width="30%">Added Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $x = 1;
                            foreach($suborder_edit_history as $his_info) { ?>
                                <tr>
                                    <td width="5%"><input type="checkbox" name="history_edit_id[]" value="<?php echo $his_info['edit_id']; ?>" class="remove_his" /></td>
                                    <td width="10%"><?php echo $his_info['new_value']; ?></td>
                                    <td width="10%"><?php echo $his_info['name']; ?></td>
                                    <td width="10%"><?php echo $his_info['date_added']; ?></td>
                                </tr>
                                <?php 
                                $x++; 
                            } 
                            ?> 
                        </tbody>
                    </table>
                    <div class="submit row">
                        <div class="col-sm-8">
                            <textarea name="comments" class="form-control" title="comment" placeholder="Comment*" id="remove-comment_ess"></textarea> 
                        </div>
                        <div class="col-sm-4">
                            <input type="hidden" name="old_order_history" value='<?php echo base64_encode(serialize($order_history)); ?>'  />
                            <button type="subimt" class="btn btn-primary pull-right clearfix remove-btns" id="remove-btn" data-txt="ESS Charges" data-vali="ESS charges" data-id-check="ess">
                            <i class="fa fa-save"></i>  Remove ESS Charges
                            </button>
                        </div>
                        <br><br><br><br>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="checkbox click_ess_charges">
                <label>
                    <input type="checkbox" name="apply_ess_charges" value="1" /> Do You want to apply further ESS charges..??
                </label>
            </div>
            <div class="col-sm-6 hidden input_ess_charges">
                <input type="number" name="ess_charges" value="" class="form-control" min='1'/>
            </div>
            <div class="submit hidden input_ess_buttons">
                <button type="button" class="btn btn-danger clearfix btn_ess_cancel">
                    <i class="fa fa-reply"></i>
                </button>
                <button type="subimt" class="btn btn-primary clearfix pull-right">
                    <i class="fa fa-save"></i>  Save
                </button>

            </div>
            <button type="subimt" class="btn btn-primary pull-right clearfix btn-refresh">
                <i class="fa fa-save"></i>  Refresh Shipping
            </button>
        </div>
        <div class="col-sm-6 pull-left">
            <div class="col-sm-6 order-info-page">
                <?php if (!empty($courier_adv)) { ?>
                    <div class="courier_advisory">
                        <?php echo $courier_adv; ?>
                    </div>
                <?php } ?>
            </div>
            <div class="col-sm-6 ">
                <span class="order_weight">Weight: <?php echo !empty($total_weight) ? $total_weight . " Kg." : ""; ?></span>
            </div>    
        </div>        
    </form>
</div>