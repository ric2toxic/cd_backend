<label>
    <a href="<?php echo $total_order_link; ?>" target="_blank">
    O-<?php echo $order_stats['total_orders']['total_orders']; 
            echo "(". $order_stats['total_orders']['total_orders_amt'] .")" ;?>  
    </a>
</label>
<br />
<label class="<?php echo ($order_stats['return_orders']['total_orders']) ? 'text-danger' : ''; ?>" > 
    R-<?php echo $order_stats['return_orders']['total_orders']; 
            echo "(". $order_stats['return_orders']['total_order_amt_per'] .")" ?>
</label>
<br />
<label class="<?php echo ($order_stats['cod_failed_orders']['total_orders']) ? 'text-danger' : ''; ?>" > 
    F-<?php echo $order_stats['cod_failed_orders']['total_orders']; 
            echo "(". $order_stats['cod_failed_orders']['total_order_amt_per'] .")" ?>
</label>
<br />
<label class="<?php echo ($order_stats['delivery_issue_orders']['total_orders']) ? 'text-danger' : ''; ?>" > 
    DI-<?php echo $order_stats['delivery_issue_orders']['total_orders']; 
            echo "(". $order_stats['delivery_issue_orders']['total_order_amt_per'] .")" ?>
</label>
<br /> 
<label class="<?php echo ($order_stats['cancelled_orders']['total_orders']) ? 'text-danger' : ''; ?>" > 
    C-<?php echo $order_stats['cancelled_orders']['total_orders']; 
            echo "(". $order_stats['cancelled_orders']['total_order_amt_per'] .")" ?>
</label>
<br />
<label class="order_list_comment" > 
    LO-[<?php
        if(!empty($order_stats['last_order_date'])){
            echo date('d-M-y', strtotime($order_stats['last_order_date'])); 
        } 
    ?> ]
</label> 