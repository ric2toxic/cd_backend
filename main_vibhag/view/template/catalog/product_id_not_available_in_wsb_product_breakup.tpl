<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $product_model; ?></h3>
                &nbsp;[ <?php echo $product_title;?> ]
            </div>
            <div class="col-sm-12" style="padding-top:10px">
                <ul class="total_qty_pcs">
                   <?php if($received_qty) { ?>
                    <li><?php echo 'Received Quantity'; ?> [<b><?php echo $received_qty; ?></b>]</li>
                   <?php } ?>

                   <?php if($purchase_return) { ?>
                    <li><?php echo 'Purchase Return'; ?> [<b><?php echo $purchase_return; ?></b>]</li>
                   <?php } ?>

                   <?php if($sales_qty) { ?>
                    <li><?php echo 'Sales Quantity'; ?> [<b><?php echo $sales_qty; ?></b>]</li>
                   <?php } ?>

                   <?php if($return_qty) { ?>
                    <li><?php echo 'Sales Return'; ?><span style='color:#f13d3d; font-weight: bold;'> [<b><?php echo $return_qty; ?></b>]</span></li>
                   <?php } ?>

                   <?php if($stock_transfer) { ?>
                    <li><?php echo 'Stock Movement'; ?>[<b><?php echo $stock_transfer; ?></b>]</li>
                   <?php } ?>
                   <?php
                   if($system_stock == $available_qty ) {
                        $style = 'background-color:#95f195; padding:2px;';
                    }else{
                        $style = 'background-color:red; color:#fff; padding:2px;';
                    }
                   ?>
                   <span style="<?php echo $style?>">
                    <li><?php echo 'System Stock'; ?> [<b><?php echo $system_stock; ?></b>]</li>
                    <li><?php echo 'Running Balance As Per Accounting'; ?> [<b><?php echo $available_qty; ?></b>]</li>
                   </span>
                </ul>

            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <td class="text-center"><?php echo $column_sno; ?></td>
                    <td class="text-center"><?php echo $column_sku; ?></td>
                    <td class="text-center"><?php echo $column_date; ?></td>
                    <td class="text-center"><?php echo $column_qty_in; ?></td>
                    <td class="text-center"><?php echo $column_qty_out; ?></td>
                    <td class="text-center"><?php echo 'Stock Movement'; ?></td>

                    <td class="text-center"><?php echo $column_transaction_type; ?></td>
                    <td class="text-center"><?php echo $column_running_balance; ?></td>
                    <td class="text-center"><?php echo $column_reference; ?></td>
                </tr>
                </thead>
                <tbody>
                    <?php if(isset($products_order)){ ?>
                        <?php foreach($products_order as $key => $product_order){

                                $style = '';
                        ?>

                            <tr style="<?php echo $style?>">
                                <td class="text-center order_list_comment"><?php echo ++$key; ?> </td>
                                <td class="text-center order_list_comment"><?php echo $product_order['seller_sku']; ?> </td>
                                <td class="text-center order_list_comment"><?php echo $product_order['invoice_date']; ?></td>
                                <td class="text-center order_list_comment"><?php echo $product_order['qty_in']; ?></td>
                                <td class="text-center"><?php echo $product_order['qty_out']; ?></td>
                                <td class="text-center"><?php echo $product_order['stock_movement']; ?>
                                <?php if(!empty($product_order['stock_movement_detail'])) { ?>
                                <br>[<?php echo $product_order['stock_movement_detail']; ?>]
                                <?php } ?>
                                </td>
                                <td class="text-center"><?php echo $product_order['transaction_type']; ?></td>
                                <td class="text-center"><?php echo $product_order['running_balance']; ?></td>
                                <td class="text-center"><?php echo $product_order['reference']; ?>
                                  <?php if(!empty( $product_order['reference_status'])) { ?>
                                    <br>[<?php echo $product_order['reference_status']; ?>]
                                  <?php } ?>
                                </td>
                            </tr>
                        <?php }?>
                    <?php }?>
                </tbody>
            </table>
        </div>
    </div>
</div>
   