
<div class="row tabs hidden edit_refresh_paycharge">
    <form action="<?php echo $save; ?>" method="post">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
        <input type="hidden" name="suborder_id" value="<?php echo $suborder_id; ?>" />
        <input type="hidden" name="refresh_discount" value="1" />
        <div class="col-lg-12">
            <div class="row">
                <div class="col-sm-8">
                    <h4><b>Order Paycharge Discount Info</b> <i class="fa fa-info-circle fa-lg" data-toggle="tooltip" title="Below shown products are only un-invoiced, order status not cancelled and products not CANCELLED_BY_CUSTOMER of an order"></i></h4>
                </div>
                <div class="col-sm-4">
                    <?php 
                    if(!$paycharge_data['coupon_discount'] && !$paycharge_data['membership_discount']) {
                        ?>
                        <button type="subimt" class="btn btn-primary btn-refresh-paycharge pull-right">
                            <i class="fa fa-save"></i>  Refresh Discount
                        </button>
                        <?php
                    }
                    ?>
                    
                </div>
            </div>
            
            <?php
            $is_custom_discount_applied = 0;
            if(!empty($paycharge_data['order_product'])) {
                ?>
                <br>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Product</th>
                            <th>Sets</th>
                            <th>Piece in set</th>
                            <th>Price/Piece</th>
                            <th>Dis. Rate</th>
                            <th>Dis. / Pc</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $x = 1;
                        $custom_discount_applied_arr = array();
                        foreach ($paycharge_data['order_product'] as $order_product_id=>$order_product_data) {
                            $discount_rate = 0;
                            $discount_amount = 0;
                            
                            $is_custom_discount_applied = 0;
                            $temp_rate = 0;
                            if(!empty($order_product_data['discount_breakup'])) {
                                $discount_breakup = unserialize($order_product_data['discount_breakup']);
                                if(!empty($discount_breakup['paycharge'])) {
                                    $discount_rate = $discount_breakup['paycharge']['valuep'];
                                    $discount_amount = $discount_breakup['paycharge']['value'];
                                    $custom_discount_applied_arr[] = $discount_rate;
                                }
                            }
                            if($paycharge_data['coupon_discount'] && $x == 1) {
                                ?>
                                <div class="text_blink">
                                    <span  style="color: rgb(102, 102, 255); font-size: 13px;"><strong>Cannot be refreshed discount because coupon discount is already applied in this suborder.</strong></span>
                                </div>
                                <br>
                                <?php
                            }
                            if($paycharge_data['membership_discount'] && $x == 1) {
                                ?>
                                <div class="text_blink">
                                    <span  style="color: rgb(102, 102, 255); font-size: 13px;"><strong>Cannot refresh paycharge discount because membership discount is already applied in this suborder.</strong></span>
                                </div>
                                <br>
                                <?php
                            }
                            ?>
                            <tr>
                                <td><?php echo $x; ?></td>
                                <td><?php echo $order_product_data['name']."<br>".$order_product_data['model']; ?></td>
                                <td><?php echo $order_product_data['quantity']; ?></td>
                                <td><?php echo $order_product_data['piece_in_set']; ?></td>
                                <td><?php echo $order_product_data['price_per_piece']; ?></td>
                                <td><?php echo $discount_rate."%"; ?></td>
                                <td><?php echo $discount_amount; ?></td>
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
        $('.btn-refresh-paycharge').click(function() {
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