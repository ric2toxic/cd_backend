<style type="text/css">
    .color_code_big {
    width: 40px!important;
    height: 25px!important;
}
.color_code {
    width: 20px;
    height: 20px;
    margin-right: 10px;
    display: block;
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
.view_more_popups ul {
    overflow-x: auto;
    overflow-y: hidden;
    display: flex;
    flex-flow: column wrap;
    height: 260px;
    margin-left: 10px!important;
    margin-right: 18px!important;
    padding: 4px 18px 10px 0!important;
    white-space: nowrap;
    margin-bottom: 0;
}
.view_more_popups ul li {
    display: flex;
    text-transform: uppercase;
}
.view_more_popups ul li {
    margin-left: 10px;
    margin-right: 10px;
    height: 45px;
    display: flex;
    text-transform: uppercase;
}
.view_more_popups ul li.li_divider {
    height: 30px;
}
.more_color_box_title{
    padding: 5px;
    float: left;
    min-width: 110px;
    font-weight: 700;
}
.more_color_box {
    padding: 5px;
    float: left;
    min-width: 110px;
}
.Red{
    background: red;
}
.popup_add_button{ margin: 10px 0px 0px 0px; height: 40px; }
.view_more_popup_model_close{
        border: 1px groove #000;
        float: right;
        padding: 1px 5px;
        border-radius: 10px;
        margin-top: -10px;
        margin-right: -10px;
        background: #000;
        color: #fff;
    }
.blue_bg
{
    position: relative;
    width: inherit;
    height: 30px;
    margin: 10px 5px;
}
.blue_bg .buyback_text
{
    color: #fff;        
}
.blue_bg .buyback_text span
{
    background-color: #2874F0;
    padding: 4px 6px 4px 20px;
    border-radius: 5px;
    font-size: 12px;
}
.blue_bg img
{
    width: 30px;
    position: absolute;
    left: -12px;
    top: -6px;
}
.round_box
{
    z-index: 100000;
}
.nopadding
{
    padding:0px;
}
</style>
<table  class="table table-bordered">
    <thead>
    <th>Product Name</th>
    <th>Set Description</th>
    <th>Price</th>
    <th>Add Set/Quantity</th>
    </thead>
    <!-- multiple set quantity block -->
    <tbody>
        <?php
        if(!empty($data['no_record'])) {
            echo '<tr><td colspan="4" align="center"><b>'.$data['no_record'].'</b></td></tr>';
        } else {
            foreach ($add_products as $product) {

                ?>
                <tr>
                    <td>
                        <p class=""><?php echo $product['name']; ?> <br><br>
                        <img src="<?php echo $product['image']; ?>"
                             width="125px"
                             height="150px"
                             class="" />
                        <br><br>
                            <b>Model: </b><?php echo $product['model']; ?><br><br>
                         </p>                  
                    <?php if($product['is_sor_enabled']) { ?>    
                        <div class="blue_bg">
                            <div ><img class="round_box" src="<?php echo STATIC_CONTENT_URL_SSL;?>sor_box.png"></div>
                        <div class="buyback_text"><span><?php echo $product['sor_enabled_text']?>*</span></div>
                        </div>
                    <?php } ?>

                    </td>
                    <td><?php echo $product['set_description']; ?></td>
                    <td><?php echo $product['price']; ?></td>
                    <td>
                        <?php 
                        if (!empty($product['options'])) {
                            foreach ($product['options'] as $options_arr) {
                                if (!empty($options_arr['product_option_value'])) {
                                    ?>
                                    <table >
                                        <thead>
                                            <tr>
                                                <th><?php echo $options_arr['name']; ?> </th>
                                                <th>Available </th>
                                                <th>Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    <?php
                                    $option_count = count($options_arr['product_option_value']);
                                    //$popup_html[$product['product_id']][$options_arr['product_option_id']] = '';
                                    foreach ($options_arr['product_option_value'] as $key => $options_val) {
                                        $disabled = '';
                                        $disabled_title = '';
                                        $span_cont = '';
                                        if(!empty($order_product_arr[$product['product_id']][$options_arr['product_option_id']][$options_val['product_option_value_id']])) {
                                            /*$disabled = 'disabled="disabled"';
                                            $disabled_title = 'title= "This product option is already added to this suborder. Please contact tech team."';*/
                                            $span_cont = '<span style="color: #17319f">Note: ' . $order_product_arr[$product['product_id']][$options_arr['product_option_id']][$options_val['product_option_value_id']] . ' set/s of ' . $options_arr['name'] . ' ' . $options_val['name'] . ' is already in this suborder.</span>';
                                        }
                                        if($product['seller_pick_city_code'] != $suborder_city) {
                                            $disabled = 'disabled="disabled"';
                                            $disabled_title = 'title= "This product belongs to different pickup city compare to this suborder."';
                                        }
                                        
                                        $color_size_td1 = '';
                                        if ($options_val['quantity'] == 0) {
                                            $td_class = "out_of_stock_text color_option";
                                            $td_text = "Out of stock";
                                            $color_size_td3 = 'N/A';
                                        } else {
                                            $td_class ='';
                                            $td_text = $options_val['quantity']." Set";
                                            $color_size_td3 = '<div class="set_quantity_panel">
                                                                    <input type="number" 
                                                                           value="" 
                                                                           name="option_quantities['.$product['product_id'].']['.$options_arr['product_option_id'].']['.$options_val['product_option_value_id'].']" 
                                                                           class="col-md-8 form-control check_product_checked option_quantities_'.$product['product_id'].'_'.$options_arr['product_option_id'].' option_quantities_for_v_m_'.$product['product_id'].'_'.$options_arr['product_option_id'].'"
                                                                           max="'.$options_val['quantity'].'"
                                                                           min="0"
                                                                           data-option-value="'.$options_val['product_option_value_id'].'"
                                                                           data-model-detail="'.$product['model'].'"
                                                                           '.$disabled.' 
                                                                           '.$disabled_title.'>
                                                                    '.$span_cont.'
                                                                </div>';
                                        }
                                        if ($options_arr['name'] == 'Color') {
                                            $color_size_td1 = '<span class="color_code color_code_big '.strtolower($options_val['name']).'"></span><br><span class="option_color_name">'.$options_val['name'].'</span>';
                                        } else {
                                            $color_size_td1 = $options_val['name'];
                                        }
                                        if ($key < 4) {
                                            ?>
                                            <tr class="odd gradeA">
                                                <td>
                                                    <?php echo $color_size_td1; ?>
                                                </td>
                                                <td class="<?php echo $td_class; ?>">
                                                    <?php echo $td_text; ?>
                                                </td>
                                                <td>
                                                    <?php echo $color_size_td3; ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    
                                    ?>
                                    <?php if($option_count > 4) { ?>
                                            <div class="view_more_popups hidden" 
                                                 id="view_more_popup_<?php echo $product['product_id']; ?>_<?php echo $options_arr['product_option_id']; ?>">
                                                <h3>Select <?php echo $options_arr['name'];?>   Option</h3>
                                                <ul>
                                                    <?php foreach ($options_arr['product_option_value'] as $key => $options_val) { 

                                                            $disabled = '';
                                                            $disabled_title = '';
                                                            $span_cont = '';
                                                            if(!empty($order_product_arr[$product['product_id']][$options_arr['product_option_id']][$options_val['product_option_value_id']])) {
                                                               /* $disabled = 'disabled="disabled"';
                                                                $disabled_title = 'title= "This product option is already added to this suborder. Please contact tech team."';*/
                                                                $span_cont = '<span style="/*color: #17319f*/" class="btn btn-xs" data-placement="top" data-html="true" data-toggle="tooltip" title="Note: ' . $order_product_arr[$product['product_id']][$options_arr['product_option_id']][$options_val['product_option_value_id']] . ' set/s of ' . $options_arr['name'] . ' ' . $options_val['name'] . ' is <br> already in this suborder."><i class="fa fa-info-circle"></i></span> ';


                                                            }
                                                            if($product['seller_pick_city_code'] != $suborder_city) {
                                                                $disabled = 'disabled="disabled"';
                                                                $disabled_title = 'title= "This product belongs to different pickup city compare to this suborder."';
                                                            }
                                                    ?>
                                                        <?php if($key%4==0){ ?>
                                                        <li class="li_divider">
                                                            <div class="more_color_box_title"><?php echo $options_arr['name'];?> </div>
                                                            <div class="more_color_box_title">Available</div>
                                                            <div class="more_color_box_title">Quantity</div>
                                                        </li>
                                                        <?php } ?>
                                                        <li>
                                                            <div class="more_color_box">
                                                                <?php if( strtolower($options_arr['name']) == 'color') { ?>
                                                                <span class="color_code color_code_big <?php echo strtolower($options_val['name']);?>"></span>
                                                                <?php } ?>
                                                                <span class="option_color_name"><?php echo $options_val['name'];?></span>
                                                            </div>
                                                            <div class="more_color_box">
                                                                <?php if($options_val['quantity']){  ?>
                                                                <?php echo $options_val['quantity'];?> Set
                                                                <?php } else { ?>
                                                                    <p> Out Of Stock </p>
                                                                <?php } ?>
                                                            </div>
                                                            <div class="more_color_box">
                                                               <?php if($options_val['quantity']){  ?>
                                                                    <div class="set_quantity_panel">
                                                                        <input type="number" 
                                                                               value="" 
                                                                               name="option_quantities[<?php echo $product['product_id']; ?>][<?php echo $options_arr['product_option_id']; ?>][<?php echo $options_val['product_option_value_id']; ?>]" 
                                                                               class="col-md-8 form-control check_product_checked option_quantities_<?php echo $product['product_id'].'_'.$options_arr['product_option_id']; ?> option_quantities_in_v_m_<?php echo $product['product_id'].'_'.$options_arr['product_option_id'].'_'.$options_val['product_option_value_id'];?>" 
                                                                               max="<?php echo $options_val['quantity'];?>" 
                                                                               min="0" 
                                                                               data-option-value="<?php echo $options_val['product_option_value_id'];?>" 
                                                                               data-model-detail="<?php echo $product['model']; ?>"
                                                                               <?php echo $disabled.' '.$disabled_title; ?>>
                                                                        <?php echo $span_cont; ?>
                                                                    </div>    
                                                                <?php } else { ?>
                                                                    <p> N/A </p>
                                                                <?php } ?>
                                                            </div>
                                                        </li>
                                                    <?php } ?>                                                    
                                                </ul>
                                                <div class="popup_add_button">
                                                    <button type="button" class="btn btn-primary pull-right" onclick="return add_item('<?php echo $product['product_id']; ?>', '1', '<?php echo $options_arr['product_option_id'] ?>');" >ADD</button> 
                                                </div>                                               
                                            </div>  

                                            <tr class="odd gradeA">
                                                <td colspan="3" align="right">
                                                    <a href="javascript:;" class="more_btn" data-product-ids="<?php echo $product['product_id']; ?>" data-option-ids="<?php echo $options_arr['product_option_id']; ?>" data-toggle="modal" data-target="#moreProductOptions">+ View More</a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                    </tbody>
                                    </table> <br>
                                    <button type="button" class="btn btn-primary" onclick="add_item('<?php echo $product['product_id']; ?>', '1', '<?php echo $options_arr['product_option_id'] ?>')">ADD</button>
                                    <?php
                                }
                            }
                            ?>
                            
                            <?php
                        } else {
                            $disabled = '';
                            $disabled_title = '';
                            $span_cont = '';
                            if(!empty($order_product_arr[$product['product_id']])) {
                                /*$disabled = 'disabled="disabled"';
                                $disabled_title = 'title= "This product is already added to this suborder. Please contact tech team."';*/
                                $span_cont = '<span style="color: #17319f">Note: ' . $order_product_arr[$product['product_id']] . ' set/s is already in this suborder.</span>';
                            }
                            if($product['seller_pick_city_code'] != $suborder_city) {
                                $disabled = 'disabled="disabled"';
                                $disabled_title = 'title= "This product belongs to different pickup city compare to this suborder."';
                            }
                            ?>

                            <table >
                                <thead>
                                    <tr>
                                        <th>Available </th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="odd gradeA">
                                        <td>
                                            <?php echo $product['quantity']; ?> Sets
                                        </td>
                                        <td>
                                            <div class="set_quantity_panel">
                                                <?php
                                                if($product['quantity']>0) {
                                                    ?>
                                                    <input type="number" 
                                                       id="option_input<?php echo $product['product_id']; ?>" 
                                                       value="" 
                                                       name="option_quantities[<?php echo $product['product_id']; ?>]" 
                                                       class="col-md-8 form-control check_product_checked"
                                                       max="<?php echo $product['quantity']; ?>"
                                                       min="0"
                                                       data-model-detail="<?php echo $product['model']; ?>"
                                                       <?php echo $disabled; ?> 
                                                       <?php echo $disabled_title; ?>>
                                                    <?php
                                                } else {
                                                    echo 'N/A';
                                                }
                                                echo $span_cont;
                                                ?>
                                                
                                            </div>
                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table><br>       
                            <button type="button" class="btn btn-primary" onclick="return add_item('<?php echo $product['product_id']; ?>', '0');" >ADD</button> 
                            <?php
                        }
                        ?>

                        
                    </td>
                </tr>
                <?php
            }
        }
        
        ?>
        
    </tbody>
</table>
<div id="moreProductOptions" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="view_more_popup_model_close" data-dismiss="modal"><i class="fa fa-times"></i></button>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.check_product_checked').keyup(function () {
                var curr_val = Number($(this).val());
                var check_max = Number($(this).attr('max'));
                var check_min = 1;
                if (curr_val == 0 || (curr_val > check_max)) {
                    alert("You can select max set " + check_max + " and minimum 1.");
                    $(this).val('');
                    return false;
                }
            });
        $('.more_btn').click(function(){
            var product_id = $(this).attr('data-product-ids');
            var product_option_id = $(this).attr('data-option-ids');
            $('.option_quantities_for_v_m_'+product_id+'_'+product_option_id).each(function (index, value) {

                var cur_product_value = Number($(value).val());
                var option_value_id = $(value).attr('data-option-value');
                if (cur_product_value > 0) {
                    console.log('.option_quantities_in_v_m_'+product_id+'_'+product_option_id+'_'+option_value_id);
                    var curr_vals = $('.option_quantities_in_v_m_'+product_id+'_'+product_option_id+'_'+option_value_id);
                    curr_vals.val(cur_product_value);
                    
                }
            }); 
            // $('.view_more_popup_block').show();
            $('#view_more_popup_'+product_id+'_'+product_option_id).removeClass('hidden');
            //$('.view_more_popup_set').html($('#view_more_popup_'+product_id+'_'+product_option_id));
            // $('.view_more_popup_set').html($('#view_more_popup_'+product_id+'_'+product_option_id));
            $('#moreProductOptions .modal-content .modal-body').html($('#view_more_popup_'+product_id+'_'+product_option_id));
        });
        $('.view_more_popup_close').click(function(){
            $('.view_more_popup_block').hide();
        });
    });
</script>
<style type="text/css">
    .popup_color_size{
        width: 40px;
        height: 25px;
    }
    .view_more_popup{
        display: none;
    }
    .view_more_popup_block{
       border: 1px groove #000; position: absolute; top: 0; background: #fff; border-radius: 8px; width: 900px; margin: auto; display: none;box-shadow: 1px 1px 15px #000;
    }
    .view_more_popup_close{
        border: 1px groove #000;
        padding: 5px 10px;
        border-radius: 15px;
        margin-top: -15px;
        margin-right: -25px;
        background: #000;
        color: #fff;

    }
</style>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
</script>