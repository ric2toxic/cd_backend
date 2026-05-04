<?php
if(!empty($products_details) && !empty($product_final_array)) {
    ?>
    <style type="text/css">
    .color_code_big {
    width: 40px!important;
    height: 25px!important;
}
.color_code {
    width: 20px;
    height: 20px;
    margin-right: 10px;
    display: inline-block;
}
.red {
    background: red;
}
.blue {
    background: #00f;
}
.green {
    background: #006400;
}
.yellow {
    background: gold;
}
.fuchsia, .pink {
    background: #f0f;
}
.black {
    background: #000;
}
.peach {
    background: #ffdab9;
}
</style>
<form action="<?php echo $save; ?>" onsubmit="return handleData(this)" method="post">
<input type="hidden" name="is_add_order" value="1" />
<input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
<input type="hidden" name="suborder_id" value="<?php echo $suborder_id; ?>" />
<table  class="table table-bordered">
    <thead>
    <th>Product Name</th>
    <th>Set Description</th>
    <!--<th>Price</th>-->
    <th>Add Set/Quantity</th>
    </thead>
    <!-- multiple set quantity block -->
    <tbody>
        <?php
        foreach ($product_final_array as $product_id => $product_arr_wise) {
            if(empty($products_details[$product_id]['qty']) && empty($products_details[$product_id]['madel_name']) &&
            !empty($product_arr_wise['options'])) {
                foreach ($product_arr_wise['options'] as $product_option_id => $options_arr) {
                    foreach ($options_arr['product_option_value'] as $option_value_id => $options_val) {
                        if(empty($products_details[$product_id][$product_option_id][$option_value_id])) {
                            continue;
                        }
                        ?>
                        <input type="hidden" name="product[<?php echo $product_id; ?>][qty][<?php echo $product_option_id; ?>][<?php echo $option_value_id; ?>]" value="<?php echo $products_details[$product_id][$product_option_id][$option_value_id]['qty']; ?>" />
                        <input type="hidden" name="product[<?php echo $product_id; ?>][stock]" value="<?php echo $product_arr_wise['stock']; ?>" />
                        <input type="hidden" name="product[<?php echo $product_id; ?>][stock_status]" value="<?php echo $product_arr_wise['stock_status']; ?>" />
                        <input type="hidden" name="product[<?php echo $product_id; ?>][seller_id]" value="<?php echo $product_arr_wise['seller_id']; ?>" />
                        <input type="hidden" name="product[<?php echo $product_id; ?>][combo_product_id]" value="<?php echo $product_arr_wise['combo_product_id']; ?>" />
                        
                        <tr>
                            <td>
                                <p class=""><?php echo $product_arr_wise['name']; ?> <br><br>
                                <img src="<?php echo $product_arr_wise['image']; ?>"
                                     width="125px"
                                     height="150px"
                                     class="" />
                                <br><br>
                                    <b>Model: </b><?php echo $product_arr_wise['model']; ?><br><br>
                                 </p>                  
                            </td>
                            <td><?php echo $product_arr_wise['set_description']; ?></td>
                            <!--<td><?php echo $product_arr_wise['price']; ?></td>-->
                            <td>
                                <table >
                                    <thead>
                                        <tr>
                                            <th><?php echo $options_arr['name']; ?> </th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php                                            
                                        $color_size_td1 = '';
                                        if ($options_arr['name'] == 'Color') {
                                            $color_size_td1 = '<span class="color_code color_code_big '.strtolower($options_val['name']).'"></span><br><span class="option_color_name">'.$options_val['name'].'</span>';
                                        } else {
                                            $color_size_td1 = $options_val['name'];
                                        }
                                        ?>
                                        <tr class="odd gradeA">
                                            <td>
                                                <?php echo $color_size_td1; ?>
                                            </td>
                                            <td>
                                                <?php echo $products_details[$product_id][$product_option_id][$option_value_id]['qty']; ?> Sets
                                            </td>
                                        </tr>
                                            
                                    </tbody>
                                </table> 
                                    
                            </td>
                        </tr>
                        <?php
                    }
                }
                
            } else {
                ?>
                <tr>
                            <td>
                                <p class=""><?php echo $product_arr_wise['name']; ?> <br><br>
                                <img src="<?php echo $product_arr_wise['image']; ?>"
                                     width="125px"
                                     height="150px"
                                     class="" />
                                <br><br>
                                    <b>Model: </b><?php echo $product_arr_wise['model']; ?><br><br>
                                 </p>                  
                            </td>
                            <td><?php echo $product_arr_wise['set_description']; ?></td>
                            <!--<td><?php echo $product_arr_wise['price']; ?></td>-->
                            <td>
                <table >
                    <thead>
                        <tr>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="odd gradeA">
                            <td>
                                <input type="hidden" name="product[<?php echo $product_id; ?>][qty]" value="<?php echo $products_details[$product_arr_wise['combo_product_id']]['qty']; ?>" />
                                 <input type="hidden" name="product[<?php echo $product_id; ?>][stock]" value="<?php echo $product_arr_wise['stock']; ?>" />
                                <input type="hidden" name="product[<?php echo $product_id; ?>][stock_status]" value="<?php echo $product_arr_wise['stock_status']; ?>" />
                                <input type="hidden" name="product[<?php echo $product_id; ?>][seller_id]" value="<?php echo $product_arr_wise['seller_id']; ?>" />
                                 <input type="hidden" name="product[<?php echo $product_id; ?>][combo_product_id]" value="<?php echo $product_arr_wise['combo_product_id']; ?>" />
                                <?php echo $products_details[$product_arr_wise['combo_product_id']]['qty']; ?> Sets
                            </td>
                        </tr>
                    </tbody>
                </table>
                </td>
                        </tr>
                <?php
            }
        }
    ?>
    </tbody>
</table>
<div class="row">
    <div class="col-sm-6">
        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Make More Changes</button>
    </div>
    <div class="col-sm-6">
        <div class="pull-left">
            <div>
                 <label>
                    Auto-update Discount: <i class="fa fa-info-circle fa-lg" data-toggle="tooltip" title="Auto update discount will refresh discount in whole order."></i>
                </label>
                 <input type="radio" name="discount_type" value="1" checked="checked">
            </div>
            <div>
                <label>
                    Keep Discount as it is: <i class="fa fa-info-circle fa-lg" data-toggle="tooltip" title="In this case if custom discount is already applied, it will not refresh discount and will apply discount to this/these product\s with minimum discount rate."></i>
                </label>
                <input type="radio" name="discount_type" value="2" >
            </div>
        </div>
        <div class="pull-right">
            <button type="submit" class="btn btn-success pull-right review_confirm">Confirm</button>
        </div>
        
        
        
    </div>
</div>
</form>
    <?php
} else {
    ?>
    <div class="row">
        <div class="col-sm-9">
            Invalid Products. Please select product and quantity.
        </div>
        <div class="col-sm-3">
            <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Cancel</button>
        </div>
    </div>
    <?php
}
?>