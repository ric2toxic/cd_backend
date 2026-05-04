<?php echo $header; ?>
<div class="container">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>

    <div class="row">
        <form action="<?php echo $form_action;?>" method="POST">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo $searchTextLabel; ?></label>
                        <input type="text" name="filters" value="<?php echo $searchTextValue; ?>" placeholder="<?php echo $searchText; ?>" id="input-name" class="form-control" />
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label"><?php echo $searchTextNameLabel; ?></label>
                        <input type="text" name="filtersName" value="<?php echo $searchTextNameValue; ?>" placeholder="<?php echo $searchTextName; ?>" id="input-name" class="form-control" />
                    </div>
                </div>

                <div class="col-sm-1">
                    <div class="form-group">

                        <input style="margin-top: 27px;" type="submit" class="btn btn-primary pull-right form-control" value="Search" />
                    </div>
                </div>
        <div class="col-sm-3"></div>
        </form>

    </div>


    <div id="content" class="ms-account-dashboard card_box">
        <ul class="nav nav-tabs" style= "margin-bottom:2px;">
          <li class="active" ><a href="#tab-wholesale" data-toggle="tab" style="font-size:18px"><strong>Wholesale Store</strong></a></li>
          <li><a href="#tab-singles" data-toggle="tab" style="font-size:18px"><strong>Singles Store</strong></a></li>
        </ul>
        <div class="tab-content">
        <div class="tab-pane fade in active" id="tab-wholesale">
            <?php //echo '<pre>'; print_r($data);?>
            <table class="list table table-bordered">
                    <thead>
                        <tr>
                            <!--<td><?php // echo $image; ?></td>
                            <?php // if (!$this->config->get('msconf_hide_customer_email')) { ?>
                                <td><?php // echo $ms_account_orders_customer; ?></td>
                            <?php // } ?>
                            <td><?php // echo $ms_status; ?></td>
                            <?php /* ?><td style="width: 40%"><?php echo $ms_account_orders_products; ?></td><?php */ ?>
                            <td><?php // echo $ms_date_created; ?></td>
                            <td><?php // echo $ms_account_orders_total; ?></td>
                            <td><?php // echo $ms_action; ?></td>-->
                            <td class="col-md-1">S. No.</td>
                            <td class="col-md-2"><?php echo $imgheading;?></td>
                            <td class="text-right col-md-1"><?php if ($sort_sku_wholesale == 'sort_order') { ?>
                                <a href="<?php echo $sort_sku_wholesale; ?>" class="<?php echo strtolower($order); ?>"><?php echo $SKU; ?></a>
                                <?php } else { ?>
                                <a href="<?php echo $sort_sku_wholesale; ?>"><?php echo $SKU; ?></a>
                                <?php } ?></td>

                            <td class="col-md-2"><?php echo $pName;?></td>
                            <td class="col-md-1"><?php echo $Quantity;?></td>
                            <!-- <td class="col-md-2"><?php echo $transferprice;?></td> -->
                            <td class="col-md-2"><?php echo $wholesale_price;?></td>
                            <td class="col-md-2"><?php echo $setdescription;?></td>
                            <td class="col-md-2"><?php echo $pieceinset;?></td>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (isset($data_wholesale) && $data_wholesale) {


                            $sr_no = (($page_wholesale-1) * $limit) + 1;
                    ?>
                        <?php foreach ($data_wholesale as $product) { ?>
                        <?php if ($product['single_product']==0){ ?>
                        <tr>
                            <td><?php echo $sr_no++; ?></td>
                            <td>
                                <?php //echo $image = $this->MsLoader->MsFile->resizeImage('no_image.png', 50, 50);?>
                                <a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']); ?>" target="_blank" class="" title=""><img src="<?php echo $product['image']; ?>" /></a>
                            </td>
                            <td><a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']); ?>" target="_blank" class="" title=""><?php echo $product['sku']; ?></a></td>
                            <td><a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']); ?>" target="_blank" class="" title=""><?php echo $product['name']; ?></a></td>
                            <td>
                                <span id="quantity_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $product['quantity']; ?></span>
                                <span style="cursor: pointer;" class="quantity_text" id="quantity_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-pencil"></i>
</span>
                                <span style="display:none;" id="quantity_input_<?php echo $product['product_id']; ?>"><input style="width: 50px;" type="text" id="quantity_val_<?php echo $product['product_id']; ?>" value="<?php echo $product['quantity']; ?>" /> <span style="cursor: pointer;" class="save_quantity" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-save"></i></span> <span style="cursor: pointer;" class="calcel_quantity" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i>
</span></span>
                            </td>
                            <td>


                                <span id="tp_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $this->currency->format($product['transferprice'], $this->config->get('config_currency')); ?></span>
                                <span style="cursor: pointer;" class="tp_text" id="tp_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-pencil"></i>
</span>
                                <span style="display:none;" id="tp_input_<?php echo $product['product_id']; ?>"><input style="width: 60px;" type="text" id="tp_val_<?php echo $product['product_id']; ?>" value="<?php echo str_replace('Rs. ','',$this->currency->format($product['transferprice'], $this->config->get('config_currency'))); ?>" /><input type="hidden" id="tp_hid_<?php echo $product['product_id']; ?>" value="<?php echo str_replace('Rs. ','',$this->currency->format($product['transferprice'], $this->config->get('config_currency'))); ?>" /> <span style="cursor: pointer;" class="save_tp" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-save"></i></span>
                                <span style="cursor: pointer;" class="calcel_tp" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i>
</span></span>


                                <?php /*echo $product['transferprice'];
                                ?>
                                <span id="Status_show_<?php echo $product['product_id']; ?>">
                                <?php
                                   // echo $product['status'];
                                ?>
                                </span>
                                <?php  if($product['status'] == 'Out Of Stock'){
                                ?>
                                        <span class="make_out_stock" html-content="7" html-data="<?php echo $product['product_id']; ?>"><input type="button" value="Set In Stock" /></span>
                                <?php
                                    }elseif($product['status'] != 'Out Of Stock'){
                                ?>
                                        <span class="make_out_stock" html-content="5" html-data="<?php echo $product['product_id']; ?>"><input type="button" value="Set Out of Stock" /></span>
                                <?php
                                    }*/
                                ?>
                            </td>
                            <td>
                                <span style="display:none; color:red" id="des_pis_<?php echo $product['product_id']; ?>">Please change Pieces in set also<br/></span><span id="des_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $product['set_description']; ?></span>
                                <span style="cursor: pointer;" class="des_text" id="des_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-pencil"></i>
</span>
                                <span style="display:none;" id="des_input_<?php echo $product['product_id']; ?>"><input style="width: 125px;" type="text" id="des_val_<?php echo $product['product_id']; ?>" value="<?php echo $product['set_description']; ?>" /> <span style="cursor: pointer;" class="save_des" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-save"></i></span>
                                <span style="cursor: pointer;" class="calcel_des" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i>
</span></span>
                            <td>
                                <span style="display:none; color:red" id="pis_des_<?php echo $product['product_id']; ?>">Please change the Set Description also<br/></span><span id="pis_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $product['piece_in_set']; ?></span>
                                <span style="cursor: pointer;" class="pis_text" id="pis_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-pencil"></i>
</span>
                                <span style="display:none;" id="pis_input_<?php echo $product['product_id']; ?>"><input style="width: 125px;" type="text" id="pis_val_<?php echo $product['product_id']; ?>" value="<?php echo $product['piece_in_set']; ?>" /> <span style="cursor: pointer;" class="save_pis" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-save"></i></span>
                                <span style="cursor: pointer;" class="calcel_pis" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i>
</span></span>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="8"><?php echo 'No Products';//$ms_account_orders_noorders; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="breadcrumb row custom-pagination-class">
                    <div class="col-sm-6 text-left"><?php echo $pagination_wholesale; ?></div>
                    <div class="col-sm-6 text-right"><?php echo $results_wholesale; ?></div>
                </div>

            </div> 
            <div class="tab-pane" id="tab-singles">
            <?php //echo '<pre>'; print_r($data);?>
            <table class="list table table-hover table-bordered table-condensed table-striped" style="border-collapse:collapse;">
                    <thead>
                        <tr>
                            <td class="col-md-1">SR. NO.</td>
                            <td class="col-md-2"><?php echo $imgheading;?></td>
                            <td class="text-right col-md-1"><?php if ($sort_sku_singles == 'sort_order') { ?>
                                <a href="<?php echo $sort_sku_singles; ?>" class="<?php echo strtolower($order); ?>"><?php echo $SKU; ?></a>
                                <?php } else { ?>
                                <a href="<?php echo $sort_sku_singles; ?>"><?php echo $SKU; ?></a>
                                <?php } ?></td>

                            <td class="col-md-2"><?php echo $pName;?></td>
                            <td class="col-md-1"><?php echo $Quantity;?></td>
                            <td class="col-md-2"><?php echo $transferprice;?></td>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (isset($data_singles) && $data_singles) {


                            $sr_no = (($page_singles-1) * $limit) + 1;
                    ?>
                        <?php foreach ($data_singles as $product) { ?>
                        <?php if ($product['single_product']==1){ ?>
                        <tr data-toggle="collapse" data-target="#option_data_<?php echo $product['product_id']; ?>" class="accordion-toggle";>
                            <td><?php echo $sr_no++; ?></td>
                            <td>
                                <?php //echo $image = $this->MsLoader->MsFile->resizeImage('no_image.png', 50, 50);?>
                                <a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']); ?>" target="_blank" class="" title=""><img src="<?php echo $product['image']; ?>" /></a>
                            </td>
                            <td><a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']); ?>" target="_blank" class="" title=""><?php echo $product['sku']; ?></a></td>
                            <td><a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']); ?>" target="_blank" class="" title=""><?php echo $product['name']; ?></a></td>
                            <td>
                                <span id="quantity_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $product['quantity']; ?></span>
                                <span style="cursor: pointer;" class="quantity_text" id="quantity_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-pencil"></i>
</span>
                                <span style="display:none;" id="quantity_input_<?php echo $product['product_id']; ?>"><input style="width: 50px;" type="text" id="quantity_val_<?php echo $product['product_id']; ?>" value="<?php echo $product['quantity']; ?>" /> <span style="cursor: pointer;" class="save_quantity" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-save"></i></span> <span style="cursor: pointer;" class="calcel_quantity" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i>
</span></span>
                            </td>
                            <td>


                                <span id="tp_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $this->currency->format($product['transferprice'], $this->config->get('config_currency')); ?></span>
                                <span style="cursor: pointer;" class="tp_text" id="tp_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-pencil"></i>
</span>
                                <span style="display:none;" id="tp_input_<?php echo $product['product_id']; ?>"><input style="width: 60px;" type="text" id="tp_val_<?php echo $product['product_id']; ?>" value="<?php echo str_replace('Rs. ','',$this->currency->format($product['transferprice'], $this->config->get('config_currency'))); ?>" /><input type="hidden" id="tp_hid_<?php echo $product['product_id']; ?>" value="<?php echo str_replace('Rs. ','',$this->currency->format($product['transferprice'], $this->config->get('config_currency'))); ?>" /> <span style="cursor: pointer;" class="save_tp" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-save"></i></span>
                                <span style="cursor: pointer;" class="calcel_tp" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i>
</span></span><i class="pull-right fa fa-caret-down" style="cursor:pointer;font-size:12px;">View Options</i>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" class="hiddenRow">
                                <div class="accordian-body collapse" id="option_data_<?php echo $product['product_id']; ?>">
                                    <?php if (isset($product['sizes']) && !empty($product['sizes'])): $label = "Size"?>
                                    <?php endif ?>
                                    <?php if (isset($product['colors']) && !empty($product['colors'])): $label = "Color"?>
                                    <?php endif ?>
                                    <table class="table table-bordered" style="margin-bottom:0px">    
                                        <thead>
                                            <tr>
                                                <td><?php echo "Sr No"; ?></td>
                                                <td><?php echo $label; ?></td>
                                                <td><?php echo "Quantity"; ?></td>
                                                <td><?php echo "Price"; ?></td>
                                            </tr>
                                        </thead> 
                                        <tbody>                 
                                    <?php $count=0 ;foreach ($product['sizes'] as $sizes) { $count++;?>
                                                <tr>
                                                    <td>
                                                        <?php echo $count; ?>
                                                    <td><?php echo $sizes['size']; ?></td>
                                                    <td>
                                                        <span id="opt_qty_text_<?php echo $sizes['value_id']; ?>" html-data="<?php echo $sizes['value_id']; ?>"><?php echo $sizes['quantity']; ?></span>
                                                        <span style="cursor: pointer;" class="opt_qty_text" id="opt_qty_edit_<?php echo $sizes['value_id']; ?>" html-data="<?php echo $sizes['value_id']; ?>"><i class="fa fa-pencil"></i>
                                                        </span>
                                                        <span style="display:none;" id="opt_qty_input_<?php echo $sizes['value_id']; ?>"><input style="width: 50px;" type="text" id="opt_qty_val_<?php echo $sizes['value_id']; ?>" value="<?php echo $sizes['quantity']; ?>" /> <span style="cursor: pointer;" class="save_opt_qty" html-data="<?php echo $sizes['value_id']; ?>"><i class="fa fa-save"></i></span> <span style="cursor: pointer;" class="calcel_opt_qty" html-data="<?php echo $sizes['value_id']; ?>"><i class="fa fa-ban"></i></span></span>
                                                        </td>
                                                    </td>
                                                    <td><?php echo "To change the price of this variant OR adding more options contact WSB" ;?></td>
                                                    
                                                </tr>                                      
                                    <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="7"><?php echo 'No Products!! Now you can sell your single skus as well. Contact WSB';//$ms_account_orders_noorders; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="breadcrumb row custom-pagination-class">
                    <div class="col-sm-6 text-left"><?php echo $pagination_singles; ?></div>
                    <div class="col-sm-6 text-right"><?php echo $results_singles; ?></div>
                </div>
            </div>
            </div>
            
        </div>
    </div>
</div>

    <?php echo $footer; ?>
<script>
$(document).ready(function(){

    //Update Quantity
    $("span.quantity_text").click(function(){
        var pid = $(this).attr("html-data");
        $(this).hide();
        $("span#quantity_text_"+pid).hide();
        $("span#quantity_input_"+pid).show();
    });

    $("span.save_quantity").click(function(){
        $("span#quantity_input_"+$(this).attr("html-data")).hide();
        $("span#quantity_text_"+$(this).attr("html-data")).html($("input#quantity_val_"+$(this).attr("html-data")).val());
        $("span#quantity_text_"+$(this).attr("html-data")).show();
        $("span#quantity_edit_"+$(this).attr("html-data")).show();
        
        $.ajax({
            url : "index.php?route=seller/manage-inventory/UpdateQuantity",
            type: "post",
            dataType: "json",
            data: "quantity="+$("input#quantity_val_"+$(this).attr("html-data")).val()+"&product_id="+$(this).attr("html-data"),
            success: function( data ) {
                //alert(data);
                if(data.trim() == 'success'){
                    //alert("here");
                }else{
                    //alert("123");
                }
            }
        });
        
    });

    $("span.calcel_quantity").click(function(){
        $("span#quantity_input_"+$(this).attr("html-data")).hide();
        var preval = $("span#quantity_text_"+$(this).attr("html-data")).html
        ();
        $("input#quantity_val_"+$(this).attr("html-data")).attr("value",preval);
        $("span#quantity_text_"+$(this).attr("html-data")).show();
        $("span#quantity_edit_"+$(this).attr("html-data")).show();
    });

    //Update Set Description

    $("span.des_text").click(function(){
        var pid = $(this).attr("html-data");
        $(this).hide();
        $("span#des_text_"+pid).hide();
        $("span#des_input_"+pid).show();
    });

    $("span.save_des").click(function(){
        $("span#des_input_"+$(this).attr("html-data")).hide();
        $("span#des_text_"+$(this).attr("html-data")).html($("input#des_val_"+$(this).attr("html-data")).val());
        $("span#des_text_"+$(this).attr("html-data")).show();
        $("span#des_edit_"+$(this).attr("html-data")).show();
        $("span#des_pis_"+$(this).attr("html-data")).show();
        var field = 'set_description';

        $.ajax({
            url : "index.php?route=seller/manage-inventory/UpdatePDField",
            type: "post",
            dataType: "json",
            data: "field_val="+$("input#des_val_"+$(this).attr("html-data")).val()+"&product_id="+$(this).attr("html-data")+"&field="+field,
            success: function( data ) {
                //alert(data);
                if(data.trim() == 'success'){
                    //alert("here");
                }else{
                    //alert("123");
                }
            }
        });

    });

    $("span.calcel_des").click(function(){
        $("span#des_pis_"+$(this).attr("html-data")).hide();
        $("span#des_input_"+$(this).attr("html-data")).hide();
        var preval = $("span#des_text_"+$(this).attr("html-data")).html
        ();
        $("input#des_val_"+$(this).attr("html-data")).attr("value",preval);
        $("span#des_text_"+$(this).attr("html-data")).show();
        $("span#des_edit_"+$(this).attr("html-data")).show();
    });

    //Update Set Transfer Price
    var existing_price ='';

    $("span.tp_text").click(function(){
        var pid = $(this).attr("html-data");
        $(this).hide();
        //var existing_price = parseFloat($("input#tp_hid_"+$(this).attr("html-data")).val());
        $("span#tp_text_"+pid).hide();
        $("span#tp_input_"+pid).show();
    });

    $("span.save_tp").click(function(){
        $("p").remove();
        $("span#tp_input_"+$(this).attr("html-data")).hide();
        var val = 'Rs .'+$("input#tp_val_"+$(this).attr("html-data")).val();
        var new_price = parseFloat($("input#tp_val_"+$(this).attr("html-data")).val());
        var existing_price = parseFloat($("input#tp_hid_"+$(this).attr("html-data")).val());
        var exist_val = 'Rs .'+$("input#tp_hid_"+$(this).attr("html-data")).val();
        if (new_price > existing_price) {
            $("span#tp_text_"+$(this).attr("html-data")).prepend("<p style='color:red'>Prices cant be increased. Contact WSB</p>");
            $("span#tp_text_"+$(this).attr("html-data")).show();
            $("span#tp_edit_"+$(this).attr("html-data")).show();
            return;
        };

        $("span#tp_text_"+$(this).attr("html-data")).html(val);
        $("span#tp_text_"+$(this).attr("html-data")).show();
        $("span#tp_edit_"+$(this).attr("html-data")).show();
        var field = 'price';

        $.ajax({
            url : "index.php?route=seller/manage-inventory/UpdatePField",
            type: "post",
            dataType: "json",
            data: "field_val="+$("input#tp_val_"+$(this).attr("html-data")).val()+"&product_id="+$(this).attr("html-data")+"&field="+field,
            success: function( data ) {
                //alert(data);
                if(data.trim() == 'success'){
                    //alert("here");
                }else{
                    //alert("123");
                }
            }
        });

    });

    $("span.calcel_tp").click(function(){
        $("p").remove();
        $("span#tp_input_"+$(this).attr("html-data")).hide();
        var preval = $("span#tp_text_"+$(this).attr("html-data")).html
        ();
        $("input#tp_val_"+$(this).attr("html-data")).attr("value",preval);
        $("span#tp_text_"+$(this).attr("html-data")).show();
        $("span#tp_edit_"+$(this).attr("html-data")).show();
    });



    $("span.make_out_stock").click(function(){
        var htmlContent = $(this).attr("html-content");
        if(htmlContent == '5'){
            var showText = "Out Of Stock";
            var showButton = "<input type='button' value='In Stock' />";
            $(this).attr("html-content","8");
        }else{
            var showText = "In Stock";
            var showButton = "<input type='button' value='Out Stock' />";
            $(this).attr("html-content","5");
        }
        $("span#Status_show_"+$(this).attr("html-data")).html(showText);
        $(this).html(showButton);
        $.ajax({
            url : "index.php?route=seller/manage-inventory/UpdateStatus",
            type: "post",
            dataType: "json",
            data: "stock_status_id="+htmlContent+"&product_id="+$(this).attr("html-data"),
            success: function( data ) {
                alert(data);
                if(data.trim() == 'success'){
                    alert("here");
                }else{
                    alert("123");
                }
            }
        });
    });

    //Update Pieces in Set
    $("span.pis_text").click(function(){
        var pid = $(this).attr("html-data");
        $(this).hide();
        $("span#pis_text_"+pid).hide();
        $("span#pis_input_"+pid).show();
    });

    $("span.save_pis").click(function(){
        $("span#pis_input_"+$(this).attr("html-data")).hide();
        $("span#pis_text_"+$(this).attr("html-data")).html($("input#pis_val_"+$(this).attr("html-data")).val());
        $("span#pis_text_"+$(this).attr("html-data")).show();
        $("span#pis_edit_"+$(this).attr("html-data")).show();
        $("span#pis_des_"+$(this).attr("html-data")).show();
        
        $.ajax({
            url : "index.php?route=seller/manage-inventory/UpdatePieceInSet",
            type: "post",
            dataType: "json",
            data: "pis="+$("input#pis_val_"+$(this).attr("html-data")).val()+"&product_id="+$(this).attr("html-data"),
            success: function( data ) {
                //alert(data);
                if(data.trim() == 'success'){
                    //alert("here");
                }else{
                    //alert("123");
                }
            }
        });
        
    });

    $("span.calcel_pis").click(function(){
        $("span#pis_des_"+$(this).attr("html-data")).hide();
        $("span#pis_input_"+$(this).attr("html-data")).hide();
        var preval = $("span#pis_text_"+$(this).attr("html-data")).html
        ();
        $("input#pis_val_"+$(this).attr("html-data")).attr("value",preval);
        $("span#pis_text_"+$(this).attr("html-data")).show();
        $("span#pis_edit_"+$(this).attr("html-data")).show();
    });

    //Update Option Quantity
    $("span.opt_qty_text").click(function(){
        var oid = $(this).attr("html-data");
        $(this).hide();
        $("span#opt_qty_text_"+oid).hide();
        $("span#opt_qty_input_"+oid).show();
    });

    $("span.save_opt_qty").click(function(){
        $("span#opt_qty_input_"+$(this).attr("html-data")).hide();
        $("span#opt_qty_text_"+$(this).attr("html-data")).html($("input#opt_qty_val_"+$(this).attr("html-data")).val());
        $("span#opt_qty_text_"+$(this).attr("html-data")).show();
        $("span#opt_qty_edit_"+$(this).attr("html-data")).show();
        
        $.ajax({
            url : "index.php?route=seller/manage-inventory/UpdateOptionQuantity",
            type: "post",
            dataType: "json",
            data: "optqty="+$("input#opt_qty_val_"+$(this).attr("html-data")).val()+"&optid="+$(this).attr("html-data"),
            success: function( data ) {
                //alert(data);
                if(data.trim() == 'success'){
                    //alert("here");
                }else{
                    //alert("123");
                }
            }
        });
        
    });

    $("span.calcel_opt_qty").click(function(){
        $("span#opt_qty_input_"+$(this).attr("html-data")).hide();
        var preval = $("span#opt_qty_text_"+$(this).attr("html-data")).html
        ();
        $("input#opt_qty_val_"+$(this).attr("html-data")).attr("value",preval);
        $("span#opt_qty_text_"+$(this).attr("html-data")).show();
        $("span#opt_qty_edit_"+$(this).attr("html-data")).show();
    });



});
        // Javascript to enable link to tab
        var url = document.location.toString();
        if (url.match('#')) {
            $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
        } 

        // Change hash for page-reload
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        })
</script>
