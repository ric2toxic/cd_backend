<?php echo $header_seller; ?>
<div class="offer_information" id="picker-container"><div class="relative_offer_information"></div></div>
<div class="loading-icon"></div>
<div class="container opacity_changer">
    <form action="<?php echo $form_action;?>" id="manage_inventory" method="POST">
        <div class="row">
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
            <ul class="nav nav-pills">
         
                <!-- archive inventory-->
                <li class="pull-right vacation_mode">
                    <button type="button" id="archive_inventory" class="<?php echo $sor_css; ?>" >Remove Archive</button>
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
                <input type="hidden" id="products_per_page" name="products_per_page" class="form-control" value="" />
            </div>
        </div>

        <div id="content" class="ms-account-dashboard card_box">
            <ul class="nav nav-tabs" style= "margin-bottom:2px;">
                <li class="active" ><a href="<?php echo $wholesale_store;?>" style="font-size:18px"><strong>Wholesale Store</strong></a></li>
                <?php /* ?>
                    <!--
                    <li><a href="<?php echo $single_store;?>" style="font-size:18px"><strong>Singles Store</strong></a></li>
                    --> 
                <?php */ ?>
              <!--  <li class="add_product">
                    <select class="product_status_change change_stock_status form-control" name= "stock_status_change">
                        <option value="">Change Stock Status</option>
                        <option value="out_stock">Out of Stock</option>
                        <option value="in_stock">In Stock</option>
                        <!-- <option value="set_qty_zero">Set Quantity Zero</option> -->
                  <!--  </select>
                </li> -->
                <?php if ($seller_store) { ?>
                    <li class="add_product"><a href="<?php echo $add_products; ?>" style="font-size:18px"><strong>Add Products</strong></a></li>
                <?php } ?>
            </ul>
            <div id="tab-wholesale">
                <table class="list table table-bordered">
                    <thead>
                        <tr>
                            <td class="col-md-1">
                                <input type="checkbox" class="select_all_product"></input>
                            </td>
                            <td class="col-md-1">S. No.</td>
                            <td class="col-md-2"><?php if ($sort_sku_wholesale == 'sort_order') { ?>
                                <a href="<?php echo $sort_sku_wholesale; ?>" class="<?php echo strtolower($order); ?>"><?php echo $SKU; ?> <i class="fa fa-angle-down"></i>
                                <?php } else { ?>
                                <a href="<?php echo $sort_sku_wholesale; ?>"><?php echo $SKU; ?>
                                    <?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'p.sku'){ ?>
                                    <i class="fa fa-angle-down"></i>
                                    <?php }elseif(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'p.sku'){ ?>
                                    <i class="fa fa-angle-up"></i>
                                    <?php } ?>
                                </a>
                                <?php } ?></td>
                            <td class="col-md-2"><?php echo $pName;?></td>
                            <td class="col-md-2">
                                <?php if ($sort_quantity_wholesale == 'sort_order') { ?>
                                    <a href="<?php echo $sort_quantity_wholesale; ?>" class="<?php echo strtolower($order); ?>"><?php echo $Quantity; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_quantity_wholesale; ?>"><?php echo $Quantity; ?>
                                        <?php if(isset($order_1,$sort_1) && $order_1 == 'DESC' && $sort_1 == 'p.quantity'){ ?>
                                    <i class="fa fa-angle-down"></i>
                                        <?PHP } ?>
                                        <?php if(isset($order_1,$sort_1) && $order_1 == 'ASC' && $sort_1 == 'p.quantity'){ ?>
                                        <i class="fa fa-angle-up"></i>
                                        <?php } ?>
                                    </a>
                                <?php } ?>
                            </td>
                            <td class="col-md-1">
                                <?php if ($sort_price_wholesale == 'sort_order') { ?>
                                    <a href="<?php echo $sort_price_wholesale; ?>" class="<?php echo strtolower($order); ?>"><?php echo $wholesale_price;?></a>
                                <?php } else { ?>
                                    <a href="<?php echo $sort_price_wholesale; ?>"><?php echo $wholesale_price;?>
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
                            <td class="col-md-2"><?php echo $setdescription;?></td>
                            <td class="col-md-1"><?php echo $pieceinset;?></td>
                            <td class="col-md-2"><?php echo $description; ?></td>
                            <?php if ($seller_store) { ?>
                            <td class="col-md-2"><?php echo $text_edit; ?></td>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data_wholesale) && $data_wholesale) {
                            $sr_no = (($page_wholesale-1) * $limit) + 1;
                        ?>
                        <?php foreach ($data_wholesale as $product) {
						if($product['is_archived'] == 1) {
                        if ($product['single_product'] == 0){

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
                        <tr>
                            <td class="col-md-1"><input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" data-sor-id="<?php echo $product['sor_product_id']; ?>" class="check_product" ></input></td>
                            <td><?php echo $sr_no++; ?></td>

                            <?php  /**
                                    *  SKU
                                    **/ ?>
                            <td><a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']); ?>" target="_blank" class="" title=""><?php echo $product['sku']; ?></a>
                                <a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']); ?>" target="_blank" class="" title=""><img src="<?php echo $product['image']; ?>" /></a> <br>
                                <?php if( !empty($product['get_pieces_sold']) ){  ?>
                                    <span class="get_pieces_sold"> Sold: <?php echo $product['get_pieces_sold']; ?></span>
                                <?php } ?>    
                            </td>

                            <?php  /**
                                    *  Name
                                    **/ ?>
                            <td><a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']); ?>" target="_blank" class="" title=""><?php echo $product['name']; ?></a>
                                <br/><label class="stock_status_<?php echo $class; ?>"><?php echo $product['stock_status']; ?></label>
                                <?php if($product['product_status'] == 2){ ?> <br/><label class="stock_status_2">Need to moderate</label> <?php } ?>
                                <?php if($product['product_status'] == 3){ ?> <br/><label class="stock_status_0">Disabled</label> <?php } ?>
                            </td>

                            <?php  /**
                                    *  Quantity
                                    **/ ?>
                            <td>
                                <table class="table table-bordered col-sm-6" style="margin-bottom:0px">
                                    <thead>
                                        <tr>
                                            <td>Set Qty.</td>
                                            <?php if(!empty($product['singles_option_sizes'])){ ?>
                                                <td>Singles</td>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <?php /* Wholesale Quantity */ ?>
                                            <?php if(!empty($product['wholesale_option_sizes'])){ ?>
                                                <td>
                                                    <table class="table table-bordered" style="margin-bottom:0px">
                                                        <thead>
                                                            <tr>
                                                                <td>Size</td>
                                                                <td>Quantity</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($product['wholesale_option_sizes'] as $sizes) { ?>
                                                                <tr>
                                                                    <td><?php echo $sizes['name']; ?></td>
                                                                    <td>
                                                                        <span id="opt_qty_text_<?php echo $sizes['product_option_value_id']; ?>" html-pid="<?php echo $sizes['product_id']; ?>" html-data="<?php echo $sizes['product_option_value_id']; ?>"><?php echo $sizes['quantity']; ?></span>
                                                                        <span style="cursor: pointer;" html-pid="<?php echo $sizes['product_id']; ?>" class="opt_qty_text" id="opt_qty_edit_<?php echo $sizes['product_option_value_id']; ?>" html-data="<?php echo $sizes['product_option_value_id']; ?>"></span>
                                                                        <span style="display:none;" html-pid="<?php echo $sizes['product_id']; ?>" id="opt_qty_input_<?php echo $sizes['product_option_value_id']; ?>">
                                                                            <input style="width: 50px;" type="text" html-pid="<?php echo $sizes['product_id']; ?>" id="opt_qty_val_<?php echo $sizes['product_option_value_id']; ?>" value="<?php echo $sizes['quantity']; ?>" />
                                                                            <span style="cursor: pointer;" class="save_opt_qty" html-pid="<?php echo $sizes['product_id']; ?>" html-data="<?php echo $sizes['product_option_value_id']; ?>"><i class="fa fa-save"></i></span>
                                                                            <span style="cursor: pointer;" class="calcel_opt_qty" html-pid="<?php echo $sizes['product_id']; ?>" html-data="<?php echo $sizes['product_option_value_id']; ?>"><i class="fa fa-ban"></i></span>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            <?php } else { ?>
                                                <td>
                                                    <span id="quantity_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $product['quantity']; ?></span>
                                                    <?php if ($product['editable_non_sor']) { ?>
                                                    <span style="cursor: pointer;" class="quantity_text" id="quantity_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"></span>
                                                    <span style="display:none;" id="quantity_input_<?php echo $product['product_id']; ?>"><input style="width: 50px;" type="text" id="quantity_val_<?php echo $product['product_id']; ?>" value="<?php echo $product['quantity']; ?>" />
                                                        <span style="cursor: pointer;" class="save_quantity" html-data="<?php echo $product['product_id']; ?>" html-model="<?php echo $product['model']; ?>" data-sor-id="<?php echo $product['sor_product_id']; ?>"><i class="fa fa-save"></i></span>
                                                        <span style="cursor: pointer;" class="calcel_quantity" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i></span>
                                                    </span>
                                                    <?php } else { ?>
                                                        <br>
                                                        <span class="editable_non_sor"> ON SOR - Edit Not Possible</span>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>

                                            <?php /* Singles Quantity */ ?>
                                            <?php if(!empty($product['singles_option_sizes'])){ ?>
                                                <td>
                                                    <table class="table table-bordered" style="margin-bottom:0px">
                                                        <thead>
                                                            <tr>
                                                                <td>Size</td>
                                                                <td>Quantity</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($product['singles_option_sizes'] as $sizes) { ?>
                                                                <tr>
                                                                    <td><?php echo $sizes['name']; ?></td>
                                                                    <td>
                                                                        <span id="opt_qty_text_<?php echo $sizes['product_option_value_id']; ?>" html-pid="<?php echo $sizes['product_id']; ?>" html-data="<?php echo $sizes['product_option_value_id']; ?>"><?php echo $sizes['quantity']; ?></span>
                                                                        <span style="cursor: pointer;" html-pid="<?php echo $sizes['product_id']; ?>" class="opt_qty_text" id="opt_qty_edit_<?php echo $sizes['product_option_value_id']; ?>" html-data="<?php echo $sizes['product_option_value_id']; ?>"></span>
                                                                        <span style="display:none;" html-pid="<?php echo $sizes['product_id']; ?>" id="opt_qty_input_<?php echo $sizes['product_option_value_id']; ?>">
                                                                            <input style="width: 50px;" type="text" html-pid="<?php echo $sizes['product_id']; ?>" id="opt_qty_val_<?php echo $sizes['product_option_value_id']; ?>" value="<?php echo $sizes['quantity']; ?>" />
                                                                            <span style="cursor: pointer;" class="save_opt_qty" html-pid="<?php echo $sizes['product_id']; ?>" html-data="<?php echo $sizes['product_option_value_id']; ?>"><i class="fa fa-save"></i></span>
                                                                            <span style="cursor: pointer;" class="calcel_opt_qty" html-pid="<?php echo $sizes['product_id']; ?>" html-data="<?php echo $sizes['product_option_value_id']; ?>"><i class="fa fa-ban"></i></span>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            <?php } else { ?>
                                                <?php /* ?><input style="margin-top: 27px;" type="submit" html-pid="<?php echo $product['product_id']; ?>" class="btn btn-primary form-control sell_single" value="Sell in Singles" /><?php */ ?>
                                            <?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>

                            <?php  /**
                                    *  Wholesalebox Price
                                    **/ ?>
                            <td>
                                <?php if(!empty($product['store_product'])){
                                        $is_checked = "checked='checked'";
                                        $show_price = "";
                                    }else{
                                        $is_checked = "";
                                        $show_price = "display:none;";
                                    }?>

                                <?php $transferprice = $product['transferprice']; ?>
                                <?php if(!empty($product['special'])) { ?>
                                <div class="previous-price"><div class="previous-price-removed"></div><?php echo $this->currency->format($transferprice, $this->config->get('config_currency')); ?></div>
                                <?php $transferprice = $product['special'];
                                $show_edit_icon = "display:none;";
                                } else {
                                $show_edit_icon = " ";
                                } ?>
                                <span style="<?php echo $show_price; ?>" id="tp_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $this->currency->format($transferprice, $this->config->get('config_currency')); ?></span>
                                <span style="cursor: pointer; <?php echo $show_price; ?>" class="tp_text" id="tp_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>">
                                </span>

                                <span style="display:none;" id="tp_input_<?php echo $product['product_id']; ?>"><input style="width: 60px;" type="text" id="tp_val_<?php echo $product['product_id']; ?>" value="<?php echo $this->currency->format($transferprice, $this->config->get('config_currency'), '', false); ?>" /><input type="hidden" id="tp_hid_<?php echo $product['product_id']; ?>" value="<?php echo $this->currency->format($transferprice, $this->config->get('config_currency'), '', false); ?>" />
                                    <span style="cursor: pointer;" class="save_tp" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-save"></i></span>
                                    <span style="cursor: pointer;" class="calcel_tp" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i></span>
                                </span>
                                <span id="tp_type_<?php echo $product['product_id']; ?>"><?php echo $product['pro_transferprice']; ?></span>
                             
                                <?php if(!empty($product['sor_product_id'])){ ?>

                                <hr>
                                <span id="tp_input_sor_<?php echo $product['sor_product_id']; ?>">SOR Price: </span>
                                <br>
                                    <span style="<?php echo $show_price; ?>" id="tp_text_<?php echo $product['sor_product_id']; ?>" html-data="<?php echo $product['sor_product_id']; ?>"><?php echo $this->currency->format($product['sor_price'], $this->config->get('config_currency')); ?></span>
                                    <span style="cursor: pointer; <?php echo $show_price; ?>" class="tp_text" id="tp_edit_<?php echo $product['sor_product_id']; ?>" html-data="<?php echo $product['sor_product_id']; ?>"><i class="fa fa-pencil"></i>
                                    </span>
                                    <span style="display:none;" id="tp_input_<?php echo $product['sor_product_id']; ?>"><input style="width: 60px;" type="text" id="tp_val_<?php echo $product['sor_product_id']; ?>" value="<?php echo $this->currency->format($product['sor_price'], $this->config->get('config_currency'), '', false); ?>" /><input type="hidden" id="tp_hid_<?php echo $product['sor_product_id']; ?>" value="<?php echo $this->currency->format($product['sor_price'], $this->config->get('config_currency'), '', false); ?>" />
                                        <span style="cursor: pointer;" class="save_tp" html-data="<?php echo $product['sor_product_id']; ?>"><i class="fa fa-save"></i></span>
                                        <span style="cursor: pointer;" class="calcel_tp" html-data="<?php echo $product['sor_product_id']; ?>"><i class="fa fa-ban"></i></span>
                                    </span>
                                <?php } ?>
                                <?php if(!empty($product['singles_price'])){ ?>
                                    <hr>
                                    <span id="tp_input_sngl_<?php echo $product['single_product_id']; ?>">Singles Price: </span>
                                    <br>
                                        <span style="<?php echo $show_price; ?>" id="tp_text_<?php echo $product['single_product_id']; ?>" html-data="<?php echo $product['single_product_id']; ?>"><?php echo $this->currency->format($product['singles_price'], $this->config->get('config_currency')); ?></span>
                                        <span style="cursor: pointer; <?php echo $show_price; ?>" class="tp_text" id="tp_edit_<?php echo $product['single_product_id']; ?>" html-data="<?php echo $product['single_product_id']; ?>">
                                        </span>
                                        <span style="display:none;" id="tp_input_<?php echo $product['single_product_id']; ?>"><input style="width: 60px;" type="text" id="tp_val_<?php echo $product['single_product_id']; ?>" value="<?php echo $this->currency->format($product['singles_price'], $this->config->get('config_currency'), '', false); ?>" /><input type="hidden" id="tp_hid_<?php echo $product['single_product_id']; ?>" value="<?php echo $this->currency->format($product['singles_price'], $this->config->get('config_currency'), '', false); ?>" />
                                            <span style="cursor: pointer;" class="save_tp" html-data="<?php echo $product['single_product_id']; ?>" ><i class="fa fa-save"></i></span>
                                            <span style="cursor: pointer;" class="calcel_tp" html-data="<?php echo $product['single_product_id']; ?>"><i class="fa fa-ban"></i></span>
                                        </span>
                                <?php } ?>

                            </td>

                            <?php  /**
                                    *  Store Price
                                    **/ ?>
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
                                        <input type="checkbox" class="show_store" id="show_store_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" html-product-status="<?php echo $product['product_status']; ?>" html-sor-pid="" html-pid="<?php echo $product['product_id']; ?>" html-store="<?php echo $store['store_id']?>" <?php echo $checked;?> ></input> Show on <?php echo $store['name']; ?>
                                    </span>
                                    <br><br>
                                    <span id="sp_text_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" style="<?php echo $show_price; ?>" html-data="<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>"><?php echo $this->currency->format($store['store_price'], $this->config->get('config_currency')); ?></span>
                                    <span style="cursor: pointer; <?php echo $show_price; ?>" class="sp_text" id="sp_edit_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" html-data="<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>">
                                    </span>
                                    <span style="display:none;" id="sp_input_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>"><input style="width: 60px;" type="text" id="sp_val_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" value="<?php echo $this->currency->format($store['store_price'], $this->config->get('config_currency'), '', false); ?>" /><input type="hidden" id="sp_hid_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" value="<?php echo $this->currency->format($store['store_price'], $this->config->get('config_currency'), '', false); ?>" />
                                        <span style="cursor: pointer;" class="save_sp" html-data="<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" html-store="<?php echo $store['store_id']?>"><i class="fa fa-save"></i></span>
                                        <span style="cursor: pointer;" class="calcel_sp" html-data="<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>"><i class="fa fa-ban"></i></span>
                                    </span>
                                </td>
                                <?php } ?>
                            <?php } ?>

                            <?php  /**
                                    *  Set Description
                                    **/ ?>
                            <td>
                                <span id="des_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $product['set_description']; ?></span>
                                <?php if ($product['editable_non_sor']) { ?>
                                    <span style="cursor: pointer;" class="des_text pis_text" id="des_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>" data-sor-id="<?php echo $product['sor_product_id']; ?>"  data-pinset="<?php echo $product['piece_in_set']; ?>" data-product-type="<?php echo $product['product_type']; ?>">
                                    </span>
                                <?php } else { ?>
                                    <br>
                                    <span class="editable_non_sor"> ON SOR - Edit Not Possible</span>
                                <?php } ?>
                            </td>

                            <?php  /**
                                    *  Pieces in Set
                                    **/ ?>
                            <td>
                                <span id="pis_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $product['piece_in_set']; ?></span>
                                <?php if ($product['editable_non_sor']) { ?>
                                    <span style="cursor: pointer;" class="pis_text" id="pis_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>" data-sor-id="<?php echo $product['sor_product_id']; ?>" data-pinset="<?php echo $product['piece_in_set']; ?>" data-product-type="<?php echo $product['product_type']; ?>">
                                    </span>
                                <?php } else { ?>
                                    <br>
                                    <span class="editable_non_sor"> ON SOR - Edit Not Possible</span>
                                <?php } ?>
                            </td>

                            <?php  /**
                                    *  Description
                                    **/ ?>
                            <td class="text-left order_list_comment">
                                <span class="desc_text_<?php echo $product['product_id']; ?>"><?php $des = html_entity_decode($product['description']);
                                 echo $des; ?></span>
                                <div style="display:none">
                                    <div id="myDivID_<?php echo $product['product_id']; ?>">
                                        <textarea name="des" rows="10"; cols="50"; class=" input-description1 des_text_<?php echo $product['product_id']; ?>"><?php echo $des; ?></textarea><br />
                                        <input type="button" data-sor-id="<?php echo $product['sor_product_id']; ?>" html-data="<?php echo $product['product_id']; ?>" class="save_des" value="Save" />
                                    </div>
                                </div>
                            </td>
                            <?php if ($seller_store) { ?>
                                <td>
                                    <a href="index.php?route=seller/product/edit&product_id=<?php echo $product['product_id']; ?>&sor_product_id=<?php echo $product['sor_product_id']; ?>"><i class="fa fa-edit">Edit</i></a>
                                </td>
                            <?php } ?>
                        </tr>
                        <?php } ?>
                        <?php } }?>
                        <?php }  else { ?>
                        <tr>
                            <td class="center" colspan="9"><?php echo 'No Products'; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="breadcrumb row custom-pagination-class">
                    <div class="col-sm-12">
                        <div class="col-sm-6 text-left"><?php echo $pagination_wholesale; ?></div>
                        <div class="col-sm-4 text-right manage_pagination"><?php echo $results_wholesale; ?></div>
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


<?php echo $footer_seller; ?>
<script type="text/javascript">
    $(document).ready(function(){
        //Update Product limit
        $('.products_per_page').change( function(){
            var per_page = $(this).val();
            $('#products_per_page').attr("value", per_page);
            document.getElementById("manage_inventory").submit();
        });

        
        // Select All Product in that page by Select all checkbox at top and left at s.no.
        $(".select_all_product").click(function() {
            $('input[name*=\'selected\']').prop('checked', this.checked);
        });

        //archive inventory 
        $('#archive_inventory').on('click',function() {
			
			var product_ids = [];

			$('input[name=\'selected[]\']:checked').each(function(){
				product_ids.push($(this).val());
			  });
			  
			  if(product_ids == ''){
				alert('Please select your product !');
			  }else{
				$.ajax({
				  type: 'post',
                  url: 'index.php?route=seller/seller_archive_inventory/archiveInventory',
				  data : 'product_ids='+product_ids,
				  dataType: 'json',
				  beforeSend: function() {
					$('.archive_inventory').button('processing');
				  },
				  complete: function() {
					$('.archive_inventory').button('reset');
				  },
				  success: function (json) {
					  if(json == 1) {
						  alert("Products successfully removed from archive");
						  $('.archive_listing').val('');
						  $('#manage_inventory input[type=\'checkbox\']').removeAttr('checked');
						  window.location.reload();
					  }
				  }
				});
			  }
		});
		
    });
</script>
<style type="text/css">
    .editable_non_sor{
        background: #f00;
        color: #fff;
        font-size: 10px;
        padding: 3px;
    }
</style>
