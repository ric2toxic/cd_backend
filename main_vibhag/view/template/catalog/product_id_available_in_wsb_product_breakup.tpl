<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $product_model; ?></h3>
                &nbsp;[ <?php echo $product_title;?> ]
            </div>
            <div class="col-sm-6">
                <ul class="total_qty_pcs">
                    <li><span>Product ID : <strong><?php echo $product_id; ?></strong></span></li>
                    <li><span>Current Seller : <strong><?php echo $current_seller['nickname']; ?></strong></span></li>
                    <li><span>Store Code : <strong><?php echo $current_seller['pickup_city_code']; ?></strong></span></li>
                </ul>                       
            </div>
        </div>
    </div>
    <div class="panel-body">
        <?php if(!empty($record_summary)){ ?>
        <div class="well">
            <div class="row">
                 <div class="col-sm-4">
                <span>
                    <table style="width:267px;">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <?php foreach($record_summary as $store_name_key => $record ){  ?>                                    
                                    <th><?php echo $store_name_key; ?></th>
                                <?php } ?>
                            </tr>                                
                        </thead>
                        <tbody>
                            <tr>
                                <td>Online Stock</td>
                                <?php foreach($record_summary as $store_name_key => $record ){ ?>                                    
                                    <td class="<?php echo $record['diff_stock'];?>"><?php echo $record['system_stock']; ?></td>
                                <?php } ?>
                            </tr>    
                            <tr>    
                                <td style="width: 235px;">Running Balance</td>
                                <?php foreach($record_summary as $store_name_key => $record ){ ?>                                    
                                    <td class="<?php echo $record['diff_stock'];?>"><?php echo $record['running_balance']; ?></td>
                                <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                </span>
                </div>
                <div class="col-sm-4 stock_summary">
                    <div>
                        <ul>
                        <?php foreach($record_summary as $store_name_key => $record ){  ?>                                    
                            <li><?php echo $store_name_key; ?></li>
                        <?php } ?>
                        </ul>
                    </div>
                    <div>
                        <ul>
                          <?php foreach($record_summary as $store_name_key => $record ){ ?>                                    
                            <li class="<?php echo $record['diff_stock'];?>">
                            <?php
                           
                            $balance_stock = $record['running_balance']-$record['system_stock'];
                            
                             
                             if ($balance_stock>0) {
                                 echo $balance_stock. "  Stock Shortfall";
                             }
                             if ($balance_stock<0) {
                                 echo abs($balance_stock). " Stock Excess";
                             }
                             if ($balance_stock==0) {
                                 echo " All Good";
                             }
                             ?>  
                             </li>
                         <?php } ?>  
                        </ul>
                    </div>
                </div>
                 <div class="col-sm-4">
                 <?php
                 if ( !empty($intermediates) ) {
                    echo '<ul class="intermediates pull-right">';
                    foreach ($intermediates as $key => $value) {
                        echo '<li>'.$key.' -- '.$value.' Intermediates</li>';
                    }
                    echo '</ul>';
                 }                 
                 ?>
                 </div>
            </div>
        </div>
        <?php } ?>
        <?php if(!empty($products_order)){ ?>
        <ul class="nav nav-tabs">
            <?php foreach($products_order as $wsb_firm_key => $product_record) { ?>
            <li class="<?php echo ($wsb_firm_key == $current_seller['code']) ? 'active' : '';?>"><a href="#<?php echo $wsb_firm_key; ?>" data-toggle="tab"><?php echo $wsb_firm_key; ?></a></li>
            <?php } ?>
        </ul>

        <div class="tab-content">
            <?php foreach($products_order as $wsb_firm_key => $products_records) { ?>
            <div class="tab-pane <?php echo ($wsb_firm_key == $current_seller['code']) ? 'active' : '';?>" id="<?php echo $wsb_firm_key; ?>">                        
                <?php if(!empty($common_record)){ ?>
                    <ul class="list-inline" style="margin: 5px;">
                        <li><strong>WSB Firm : </strong><span><?php echo $common_record[$wsb_firm_key]['wsb_firm']; ?></span></li>
                        <li><strong>WSB GST NO. : </strong><span><?php echo $common_record[$wsb_firm_key]['wsb_gst']; ?></span></li>
                    </ul>
                    
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <td class="text-center" style="width: 100px;"><?php echo $column_sno; ?></td>
                            <td class="text-center"><?php echo $column_sku; ?></td>
                            <td class="text-center"><?php echo $column_qty_in_out; ?></td>
                            <td class="text-center">Product Options</td>
                            <td class="text-center"><?php echo $column_running_balance; ?></td>
                            <td class="text-center"><?php echo 'Stock Movement'; ?></td>                            
                            <td class="text-center"><?php echo $column_transaction_type; ?></td>
                            <td class="text-center"><?php echo 'Seller firm'; ?></td>
                            <td class="text-center"><?php echo 'Transfer price per piece'; ?></td>
                            <td class="text-center"><?php echo 'Reference'; ?></td>
                            <td class="text-center"><?php echo $column_date; ?></td>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products_records as $key => $product_order){ 
                                $style = '';
                            ?>                                        
                                <tr style="<?php echo $style?>">
                                    <td class="text-center order_list_comment"><?php echo ++$key;?> </td>
                                    <td class="text-center order_list_comment"><?php echo $product_order['seller_sku']; ?> </td>
                                    <td class="text-center order_list_comment">
                                    <?php
                                        if($product_order['qty_in']!='') {
                                            echo "+ ".  $product_order['qty_in'];
                                        } 
                                        if($product_order['qty_out']!='') {
                                            echo "- ".  $product_order['qty_out'];
                                        } 
                                    ?> 
                                    </td>
                                    <td class="text-center display_options_table">                                       
                                        <?php 
                                            $get_size_value = array();
                                            if(!empty($product_order['option_value'])){
                                                $get_size_value = explode(',',$product_order['option_value']);
                                        ?>
                                                <table>
                                                    
                                                    <tbody>
                                                        <tr>
                                                        <td>
                                                        <ul class="display_options">
                                                        <?php $i=1; foreach($get_size_value as $size_value) { ?>
                                                            <li><?php echo $size_value; ?></li>
                                                        <?php if($i%3 == 0 ){ echo "</tr><tr>";  } ?>
                                                        <?php $i++; } ?>
                                                        </ul>
                                                        </td>
                                                        </tr>
                                                    </tbody>       
                                                </table>
                                        <?php         
                                            }                                                
                                        ?>
                                    </td>
                                     <td class="text-center <?php echo ($product_order['running_balance']< 0 ) ? bg-danger : '';?>">
                                        <?php echo $product_order['running_balance']; ?>
                                    </td>
                                    <td class="text-center"><?php echo $product_order['stock_movement']; ?>
                                    <?php if(!empty($product_order['stock_movement_detail'])) { ?>
                                    <br>[<?php echo $product_order['stock_movement_detail']; ?>]
                                    <?php } ?>
                                    </td>
                                    <td class="text-center"><?php echo $product_order['transaction_type']; ?></td>
                                    <td><?php echo $product_order['seller_firm']; ?></td>
                                    <td><?php echo $product_order['transfer_price_per_piece']; ?></td>
                                    <td><?php echo $product_order['reference']; ?></td>
                                    <td class="text-center order_list_comment"><?php echo date('dS M, Y', strtotime($product_order['reference_document_date'])); ?></td>
                                </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } ?>
        </div> 
        <?php } ?> 
    </div>
</div>