<?php echo $header_seller; ?>
<div class="offer_information" id="picker-container"><div class="relative_offer_information"></div></div>
<div class="loading-icon"></div>
<div class="container">
    <form action="<?php echo $form_action;?>" id="manage_inventory" method="POST">
        <div class="row">
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
                <li class="pull-right vacation_mode">
                    <?php if(isset($vacation) && $vacation == 1){ ?>
                        <a href="javascript:void(0);"><span class="btn btn-success vacation vac_0 form-control" data-val="0" ><?php echo $text_vacation_disable; ?></span></a>
                    <?php }else{ ?>
                        <a href="javascript:void(0);"><span class="btn btn-danger vacation vac_1 form-control" data-val="1" ><?php echo $text_vacation_enable; ?></span></a>
                    <?php } ?>
                </li>
            </ul>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label"><?php echo $searchTextLabel; ?></label>
                    <input type="text" name="filters" value="<?php echo $searchTextValue; ?>" placeholder="<?php echo $searchText; ?>" id="input-name" class="form-control" />
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label class="control-label">Categories</label>
                    <select name="category_id" class="form-control">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $category_1) { ?>
                    <?php if ($category_1['category_id'] == $category_id) { ?>
                    <option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
                    <?php } ?>
                    <?php foreach ($category_1['children'] as $category_2) { ?>
                    <?php if ($category_2['category_id'] == $category_id) { ?>
                    <option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
                    <?php } ?>
                    <?php foreach ($category_2['children'] as $category_3) { ?>
                    <?php if ($category_3['category_id'] == $category_id) { ?>
                    <option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Price</label>
                        <input type="text" name="low_price" value="<?php echo $low_price; ?>" placeholder="low-price" id="input-low-price" class="form-control" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">&nbsp</label>
                        <input type="text" name="price" value="<?php echo $price; ?>" placeholder="high-price" id="input-high-price" class="form-control" />
                    </div>
                </div>
            </div>
            <div class="col-sm-1">
                <div class="form-group">
                    <input style="margin-top: 27px;" type="submit" class="btn btn-primary pull-right form-control" value="Search" />
                </div>
            </div>
            <div class="col-sm-3">
                <input type="hidden" id="products_per_page" name="products_per_page" class=" form-control" value="" />
            </div>
        </div>

        <div id="content" class="ms-account-dashboard card_box">
            <ul class="nav nav-tabs" style= "margin-bottom:2px;">
                <li><a href="<?php echo $wholesale_store;?>" style="font-size:18px"><strong>Wholesale Store</strong></a></li>
                <li class="active"><a href="<?php echo $single_store;?>" style="font-size:18px"><strong>Singles Store</strong></a></li>
                <li class="add_product">
                    <select class="product_status_change form-control" name= "stock_status_change">
                        <option value="">Change Stock Status</option>
                        <option value="out_stock">Out of Stock</option>
                        <option value="in_stock">In Stock</option>
                    </select>
                </li>
                <?php if ($seller_store) { ?>
                    <li class="add_product"><a href="<?php echo $add_products; ?>" style="font-size:18px"><strong>Add Products</strong></a></li>
                <?php } ?>
            </ul>
            <div id="tab-singles">
                <table class="list table table-hover table-bordered table-condensed table-striped" style="border-collapse:collapse;">
                    <thead>
                        <tr>
                            <td class="col-md-1"><input type="checkbox" class="select_all_product"></input></td>
                            <td class="col-md-1">SR. NO.</td>
                            <td class="col-md-1">
                                <?php if ($sort_sku_singles == 'sort_order') { ?>
                                    <a href="<?php echo $sort_sku_singles; ?>" class="<?php echo strtolower($order); ?>"><?php echo $SKU; ?> <i class="fa fa-angle-down"></i>
                                <?php } else { ?>
                                    <a href="<?php echo $sort_sku_singles; ?>"><?php echo $SKU; ?>
                                    <?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'p.sku'){ ?>
                                        <i class="fa fa-angle-down"></i>
                                    <?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'p.sku'){ ?>
                                        <i class="fa fa-angle-up"></i>
                                    <?php } ?>
                                </a>
                                <?php } ?>
                            </td>
                            <td class="col-md-2"><?php echo $pName;?></td>
                            <td class="col-md-1"><?php echo $Quantity;?></td>
                            <td class="col-md-2">
                                <?php if ($sort_price_singles == 'sort_order') { ?>
                                    <a href="<?php echo $sort_price_singles; ?>" class="<?php echo strtolower($order); ?>"><?php echo $wholesale_price;?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_price_singles; ?>"><?php echo $wholesale_price;?>
                                        <?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'p.price'){ ?>
                                    <i class="fa fa-angle-down"></i>
                                        <?PHP } ?>
                                        <?php if(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'p.price'){ ?>
                                        <i class="fa fa-angle-up"></i>
                                        <?php } ?>
                                    </a>
                                <?php } ?>
                            </td>
                            <?php if ($stores) {
                                foreach($stores as $store) { ?>
                            <td class="col-md-2"><?php echo $store['name'];?></td>
                            <?php } } ?>
                            <?php if ($seller_store) { ?>
                            <td class="col-md-2"><?php echo $text_edit; ?></td>
                            <?php } ?>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (isset($data_singles) && $data_singles) {
                            $sr_no = (($page_singles-1) * $limit) + 1;
                    ?>
                        <?php foreach ($data_singles as $product) { ?>
                        <?php if ($product['single_product']== 1){

                             if($product['stock_status'] == 'In Stock'){
                                $class = "1";
                            }
                            if($product['stock_status'] == 'Out Of Stock'){
                                $class = "0";
                            }
                            if($product['quantity'] == 0){
                                $product['stock_status'] = 'Out Of Stock';
                                $class = "0";
                            }
                        ?>
                        <tr data-toggle="collapse" data-target="#option_data_<?php echo $product['product_id']; ?>" class="accordion-toggle">
                            <td class="col-md-1"><input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" class="check_product" ></input></td>
                            <td><?php echo $sr_no++; ?></td>
                            <td><a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']); ?>" target="_blank" class="" title=""><?php echo $product['sku']; ?></a>
                                <a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']); ?>" target="_blank" class="" title=""><img src="<?php echo $product['image']; ?>" /></a>
                            </td>
                            <td>
                                <a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']); ?>" target="_blank" class="" title=""><?php echo $product['name']; ?></a>
                                <br/><label class="stock_status_<?php echo $class; ?>"><?php echo $product['stock_status']; ?></label>
                                <?php if($product['product_status'] == 2){ ?> <br/><label class="stock_status_2">Need to moderate</label> <?php } ?>
                                <?php if($product['product_status'] == 3){ ?> <br/><label class="stock_status_0">Disable</label> <?php } ?>
                            </td>

                            <td>
                                <table class="table table-bordered" style="margin-bottom:0px">

                                <?php $i = 0;
                                    if(!empty($product['sizes'])){
                                        foreach ($product['sizes'] as $sizes) {
                                            if($i==0){ ?>
                                                <thead>
                                                    <tr>
                                                        <td><?php echo "Size"; ?></td>
                                                        <td><?php echo "Quantity"; ?></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                            <?php $i++; } ?>
                                                <tr>
                                                    <td><?php echo $sizes['size']; ?></td>
                                                    <td>
                                                        <span id="opt_qty_text_<?php echo $sizes['value_id']; ?>" html-pid="<?php echo $product['product_id']; ?>" html-data="<?php echo $sizes['value_id']; ?>"><?php echo $sizes['quantity']; ?></span>
                                                        <span style="cursor: pointer;" class="opt_qty_text" id="opt_qty_edit_<?php echo $sizes['value_id']; ?>" html-pid="<?php echo $product['product_id']; ?>" html-data="<?php echo $sizes['value_id']; ?>"><i class="fa fa-pencil"></i>
                                                        </span>
                                                        <span style="display:none;" id="opt_qty_input_<?php echo $sizes['value_id']; ?>"><input style="width: 50px;" type="text" id="opt_qty_val_<?php echo $sizes['value_id']; ?>" value="<?php echo $sizes['quantity']; ?>" /> <span style="cursor: pointer;" class="save_opt_qty" html-pid="<?php echo $product['product_id']; ?>" html-data="<?php echo $sizes['value_id']; ?>"><i class="fa fa-save"></i></span> <span style="cursor: pointer;" class="calcel_opt_qty" html-pid="<?php echo $product['product_id']; ?>" html-data="<?php echo $sizes['value_id']; ?>"><i class="fa fa-ban"></i></span></span>
                                                    </td>
                                                </tr>
                                            <?php if($i > 0){ ?>
                                                </tbody>
                                            <?php } ?>
                                        <?php }
                                    }else{ ?>
                                        <thead>
                                            <tr>
                                                <td>Quantity</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <span id="quantity_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $product['quantity']; ?></span>
                                                    <span style="cursor: pointer;" class="quantity_text" id="quantity_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-pencil"></i></span>
                                                    <span style="display:none;" id="quantity_input_<?php echo $product['product_id']; ?>"><input style="width: 50px;" type="text" id="quantity_val_<?php echo $product['product_id']; ?>" value="<?php echo $product['quantity']; ?>" />
                                                        <span style="cursor: pointer;" class="save_quantity" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-save"></i></span>
                                                        <span style="cursor: pointer;" class="calcel_quantity" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i></span>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                </table>
                            </td>
                            <td>
                                <?php if(!empty($product['store_product'])){
                                        $is_checked = "checked='checked'";
                                        $show_price = "";
                                    }else{
                                        $is_checked = "";
                                        $show_price = "display:none;";
                                    }?>
                                <span>
                                    <input type="checkbox" class="show_store" id="show_store_<?php echo $product['product_id']; ?>_0" html-product-status="<?php echo $product['product_status']; ?>" html-pid="<?php echo $product['product_id']; ?>" html-store="0"<?php echo $is_checked;?>></input> Show on Wholeasalebox
                                </span>
                                <br><br>
                                <?php $transferprice = $product['transferprice']; ?>
                                <?php if(!empty($product['special'])) { ?>
                                    <div class="previous-price"><div class="previous-price-removed"></div><?php echo $this->currency->format($transferprice, $this->config->get('config_currency')); ?></div>
                                <?php $transferprice = $product['special']; } ?>


                                <span style="<?php echo $show_price; ?>" id="tp_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $this->currency->format($transferprice, $this->config->get('config_currency')); ?></span>
                                <span style="cursor: pointer; <?php echo $show_price; ?>" class="tp_text" id="tp_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-pencil"></i></span>
                                <span style="display:none;" id="tp_input_<?php echo $product['product_id']; ?>"><input style="width: 60px;" type="text" id="tp_val_<?php echo $product['product_id']; ?>" value="<?php echo $this->currency->format($transferprice, $this->config->get('config_currency'), '', false); ?>" /><input type="hidden" id="tp_hid_<?php echo $product['product_id']; ?>" value="<?php echo $this->currency->format($transferprice, $this->config->get('config_currency'), '', false); ?>" /> <span style="cursor: pointer;" class="save_tp" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-save"></i></span>
                                <span style="cursor: pointer;" class="calcel_tp" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i></span></span>
                                <?php if(!empty($product['special'])) { ?>
                                <button type="button" class="deals" data-id="<?php echo $product['product_id']; ?>" data-discount_type="<?php echo $product['discount_type']; ?>" data-discount_value="<?php echo $product['discount_value']; ?>" data-offer_date_start="<?php echo $product['offer_date_start']; ?>" data-offer_date_end="<?php echo $product['offer_date_end']; ?>"  data-price="<?php echo $product['transferprice']; ?>" >Change Offer</button>
                                <?php } else { ?>
                                <button type="button" class="deals" data-id="<?php echo $product['product_id']; ?>" data-discount_type="<?php echo $product['discount_type']; ?>" data-discount_value="<?php echo $product['discount_value']; ?>" data-offer_date_start="<?php echo $product['offer_date_start']; ?>" data-offer_date_end="<?php echo $product['offer_date_end']; ?>"  data-price="<?php echo $product['transferprice']; ?>" >New Offer</button>
                                <?php } ?>
                            </td>

                            <?php if (!empty($product['store_info'])) { ?>
                                <?php foreach ($product['store_info'] as $store) { ?>
                                <td>
                                    <?php if(!empty($product['enabled_on_stores']) && in_array($store['store_id'], $product['enabled_on_stores'])){
                                            $checked = "checked='checked'";
                                            $show_price = "";
                                        }else{
                                            $checked = "";
                                            $show_price = "display:none;";
                                        }?>
                                    <span>
                                        <input type="checkbox" class="show_store" id="show_store_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" html-product-status="<?php echo $product['product_status']; ?>" html-pid="<?php echo $product['product_id']; ?>" html-store="<?php echo $store['store_id']?>" <?php echo $checked;?> ></input> Show on <?php echo $store['name']; ?>
                                    </span>
                                    <br><br>
                                    <span style="<?php echo $show_price; ?>" id="sp_text_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" html-data="<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>"><?php echo $this->currency->format($store['store_price'], $this->config->get('config_currency')); ?></span>
                                    <span style="cursor: pointer; <?php echo $show_price; ?>" class="sp_text" id="sp_edit_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" html-data="<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>"><i class="fa fa-pencil"></i></span>
                                    <span style="display:none;" id="sp_input_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>">
                                        <input style="width: 60px;" type="text" id="sp_val_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" value="<?php echo $this->currency->format($store['store_price'], $this->config->get('config_currency'), '', false); ?>" />
                                        <input type="hidden" id="sp_hid_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" value="<?php echo $this->currency->format($store['store_price'], $this->config->get('config_currency'), '', false); ?>" />
                                        <span style="cursor: pointer;" class="save_sp" html-data="<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" html-store="<?php echo $store['store_id']?>"><i class="fa fa-save"></i></span>
                                        <span style="cursor: pointer;" class="calcel_sp" html-data="<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>"><i class="fa fa-ban"></i></span>
                                    </span>

                                </td>
                                <?php } ?>
                            <?php if ($seller_store) { ?>
                            <td>
                                <a href="index.php?route=seller/product/edit&product_id=<?php echo $product['product_id']; ?>&single=1&sor_product_id="><i class="fa fa-edit"><?php echo $text_edit; ?></i></a>
                            <?php } ?>
                            <?php } ?>
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
                    <div class="col-sm-12">
                        <div class="col-sm-6 text-left"><?php echo $pagination_singles; ?></div>
                        <div class="col-sm-4 text-right manage_pagination"><?php echo $results_singles; ?></div>
                        <div class="col-sm-2 pull-right manage_product_page">
                            <label>products per page</label>
                            <select name="products_per_page" class="products_per_page form-control">
                                <option value="60" <?php if($limit == 60){ ?>
                                    selected="selected"
                                   <?php } ?> >60</option>
                                <option value="100" <?php if($limit == 100){ ?>
                                    selected="selected"
                                   <?php } ?> >100</option>
                                <option value="150" <?php if($limit == 150){ ?>
                                    selected="selected"
                                   <?php } ?> >150</option>
                                <option value="250" <?php if($limit == 250){ ?>
                                    selected="selected"
                                   <?php } ?> >250</option>
                                <option value="500" <?php if($limit == 500){ ?>
                                    selected="selected"
                                   <?php } ?> >500</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="show_vacation_mgs" style="display:none;">
    <div>
        <h3 class="error_pis">
        <?php if(isset($vacation) && $vacation == 1){
        echo $off_vacation_mgs;
        }else{
         echo $on_vacation_mgs;
        } ?>
        </h3>
    </div>
    <br/>
    <div class="col-xs-4 pull-right">
        <a href="javascript:void(0);"><span data-val="" class="btn btn-success form-control show_vacation"> Ok. </span></a>
    </div>
    &nbsp;
    <div class="col-xs-3 ud-alignment">
        <a href="javascript:void(0);"><span class="cancel_show_vacation_mgs">Cancel</span></a>
    </div>
</div>
<?php echo $footer_seller; ?>
<script type="text/javascript">
    $(document).ready(function(){

        $('.products_per_page').change( function(){
            var per_page = $(this).val();
            $('#products_per_page').attr("value", per_page);
            document.getElementById("manage_inventory").submit();
        });

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
    			data: "quantity="+$("input#quantity_val_"+$(this).attr("html-data")).val()+"&product_id="+$(this).attr("html-data")+"&product_model=&sor_product_id=",
    			success: function( data ) {

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

        //Update Set Transfer Price
        var existing_store_price ='';

        $("span.sp_text").click(function(){
            var pid = $(this).attr("html-data");
            $(this).hide();
            //var existing_store_price = parseFloat($("input#sp_hid_"+$(this).attr("html-data")).val());
            $("span#sp_text_"+pid).hide();
            $("span#sp_input_"+pid).show();
        });

        $("span.save_sp").click(function(){
            $("p").remove();
            $("span#sp_input_"+$(this).attr("html-data")).hide();
            var loval = 'Rs '+$("input#sp_val_"+$(this).attr("html-data")).val();
            var new_store_price = parseFloat($("input#sp_val_"+$(this).attr("html-data")).val());
            var existing_store_price = parseFloat($("input#sp_hid_"+$(this).attr("html-data")).val());
            var new_exist_val = 'Rs .'+$("input#sp_hid_"+$(this).attr("html-data")).val();
            var store = $(this).attr("html-store");
            if (0) {
                $("span#sp_text_"+$(this).attr("html-data")).prepend("<p style='color:red'>Prices cant be increased. Contact WSB</p>");
                $("span#sp_text_"+$(this).attr("html-data")).show();
                $("span#sp_edit_"+$(this).attr("html-data")).show();
                return;
            };
            $("span#sp_text_"+$(this).attr("html-data")).html(loval);
            $("span#sp_text_"+$(this).attr("html-data")).show();
            $("span#sp_edit_"+$(this).attr("html-data")).show();
            var field = 'store_price';

            $.ajax({
                url : "index.php?route=seller/manage-inventory/UpdateSellerStorePrice",
                type: "post",
                dataType: "json",
                data: "field_val="+$("input#sp_val_"+$(this).attr("html-data")).val()+"&data_arr="+$(this).attr("html-data")+"&field="+field+"&store="+store,
                success: function( data ) {

                }
            });

        });

        $("span.calcel_sp").click(function(){
            $("p").remove();
            $("span#sp_input_"+$(this).attr("html-data")).hide();
            var preval = $("span#sp_text_"+$(this).attr("html-data")).html
            ();
            $("input#sp_val_"+$(this).attr("html-data")).attr("value",preval);
            $("span#sp_text_"+$(this).attr("html-data")).show();
            $("span#sp_edit_"+$(this).attr("html-data")).show();
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
                data: "optqty="+$("input#opt_qty_val_"+$(this).attr("html-data")).val()+"&optid="+$(this).attr("html-data")+"&pid="+$(this).attr("html-pid"),
                success: function( data ) {

                    window.location.reload();
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

        $(".changestore").click(function(){
            var temp = $(this).attr("html-temp-store");
            $.ajax({
                url : "index.php?route=seller/manage-inventory/index",
                type: "post",
                data: "temp="+temp,
                success: function( data ) {
                    window.location.reload();
                }
            });
        });

        $(".show_store").click(function(){
            var pid = $(this).attr("html-pid");
            var store = $(this).attr("html-store");
            var product_status = $(this).attr("html-product-status");
            var product = document.getElementById('show_store_'+pid+'_'+ store).checked ;
            if(product == true){
                var ischeck = 1;
                if(store != 0){
                    $('#sp_text_'+pid+'_'+store).show();
                    $('#sp_edit_'+pid+'_'+store).show();
                }else{
                    $('#tp_text_'+pid).show();
                    $('#tp_edit_'+pid).show();
                }
            }else{
                var ischeck = 0;
                if(store != 0){
                    $('#sp_text_'+pid+'_'+store).hide();
                    $('#sp_edit_'+pid+'_'+store).hide();
                }else{
                    $('#tp_text_'+pid).hide();
                    $('#tp_edit_'+pid).hide();
                }
            }
            $.ajax({
                url : "index.php?route=seller/manage-inventory/showProductInStore",
                type: "post",
                data: "pid="+pid+"&store_id="+store+"&ischeck="+ischeck+"&product_status="+product_status,
                success: function( data ) {
                    //window.location.reload();
                }
            });
        });

        $(".select_all_product").click(function() {
            $('input[name*=\'selected\']').prop('checked', this.checked);
        });
        $(".product_status_change").change(function() {
            var val = $(this).val();
            if(val == 'out_stock'){
               confirm("Are you sure you want stock out this product") ? $('#manage_inventory').submit() : false;
            }
            if(val == 'in_stock'){
               confirm("Are you sure you want stock In this product") ? $('#manage_inventory').submit() : false;
            }
        });
        $(".vacation").click(function() {
            $('.show_vacation_mgs').show();
            var val = $(this).attr('data-val');
            $('.show_vacation').attr('data-val', val);
        });
        $(".cancel_show_vacation_mgs").click(function() {
            $('.show_vacation_mgs').hide();
        });
        $(".show_vacation").click(function() {
            var val = $(this).attr('data-val');
            $.ajax({
                url : "index.php?route=seller/manage-inventory/updateVacation",
                type: "post",
                data: "vacation_val="+val,
                success: function( data ) {
                    window.location.reload();
                }
            });
        });
    });
</script>
