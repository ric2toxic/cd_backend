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
                <li class="pull-right vacation_mode">
                    <button type="button" id="sor_enable" data-sor="<?php echo $change_to_sor;?>" class="<?php echo $sor_css; ?>" value="<?php echo $sor_button_label;?>" ><?php echo $sor_button_label;?></button>
                </li>
                <li class="pull-right vacation_mode">
                    <?php if(isset($vacation) && $vacation == 1){ ?>
                        <a href="javascript:void(0);" class="nopadding"><span class="btn btn-success vacation vac_0 form-control" data-val="0" ><?php echo $text_vacation_disable; ?></span></a>
                    <?php }else{ ?>
                        <a href="javascript:void(0);" class="nopadding"><span class="btn btn-danger vacation vac_1 form-control" data-val="1" ><?php echo $text_vacation_enable; ?></span></a>
                    <?php } ?>
                </li>
                <!-- archive inventory-->
                <li class="pull-right vacation_mode">
                    <?php if(!$product_archived){ ?>
                    <button type="button" id="archive_inventory" class="<?php echo $sor_css; ?>" value="1" >Mark Archive</button>
                    <?php } else { ?>
                    <button type="button" id="archive_inventory" class="<?php echo $sor_css; ?>" value="0" >Remove Archive</button>
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
                <li class="add_product">
                    <select class="product_status_change change_stock_status form-control" name= "stock_status_change">
                        <option value="">Change Stock Status</option>
                        <option value="out_stock">Out of Stock</option>
                        <option value="in_stock">In Stock</option>
                        <!-- <option value="set_qty_zero">Set Quantity Zero</option> -->
                    </select>
                </li>
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
                            <?php //if ($seller_store) { ?>
                            <!-- <td class="col-md-2"><?php //echo $text_edit; ?></td> -->
                            <?php //} ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data_wholesale) && $data_wholesale) {
                            $sr_no = (($page_wholesale-1) * $limit) + 1;
                        ?>
                        <?php foreach ($data_wholesale as $product) {
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
                                                                        <span style="cursor: pointer;" html-pid="<?php echo $sizes['product_id']; ?>" class="opt_qty_text" id="opt_qty_edit_<?php echo $sizes['product_option_value_id']; ?>" html-data="<?php echo $sizes['product_option_value_id']; ?>"><i class="fa fa-pencil"></i></span>
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
                                                    <span style="cursor: pointer;" class="quantity_text" id="quantity_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-pencil"></i></span>
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
                                                                        <span style="cursor: pointer;" html-pid="<?php echo $sizes['product_id']; ?>" class="opt_qty_text" id="opt_qty_edit_<?php echo $sizes['product_option_value_id']; ?>" html-data="<?php echo $sizes['product_option_value_id']; ?>"><i class="fa fa-pencil"></i></span>
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

                                <span>
                                    <input type="checkbox" class="show_store" id="show_store_<?php echo $product['product_id']; ?>_0" html-product-status="<?php echo $product['product_status']; ?>" html-pid="<?php echo $product['product_id']; ?>" html-sor-pid="<?php echo $product['sor_product_id']; ?>" html-sngl-pid="<?php echo $product['single_product_id']; ?>" html-store="0"<?php echo $is_checked;?>></input> Show on Wholeasalebox
                                </span>
                                <br><br>
                                <?php $transferprice = $product['transferprice']; ?>
                                <?php if(!empty($product['special'])) { ?>
                                <div class="previous-price"><div class="previous-price-removed"></div><?php echo $this->currency->format($transferprice, 'INR', 1); ?></div>
                                <?php $transferprice = $product['special'];
                                $show_edit_icon = "display:none;";
                                } else {
                                $show_edit_icon = " ";
                                } ?>
                                <span style="<?php echo $show_price; ?>" id="tp_text_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><?php echo $this->currency->format($transferprice, 'INR', 1); ?></span>
                                <span style="cursor: pointer; <?php echo $show_price; ?>" class="tp_text" id="tp_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>"><i style="<?php echo $show_edit_icon; ?>" class="fa fa-pencil"></i>
                                </span>

                                <span style="display:none;" id="tp_input_<?php echo $product['product_id']; ?>"><input style="width: 60px;" type="text" id="tp_val_<?php echo $product['product_id']; ?>" value="<?php echo $this->currency->format($transferprice, 'INR', 1, false); ?>" /><input type="hidden" id="tp_hid_<?php echo $product['product_id']; ?>" value="<?php echo $this->currency->format($transferprice, 'INR', 1, false); ?>" />
                                    <span style="cursor: pointer;" class="save_tp" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-save"></i></span>
                                    <span style="cursor: pointer;" class="calcel_tp" html-data="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i></span>
                                </span>
                                <span id="tp_type_<?php echo $product['product_id']; ?>"><?php echo $product['pro_transferprice']; ?></span>
                                <?php if(!empty($product['special'])) { ?>
                                <button type="button" class="deals" data-id="<?php echo $product['product_id']; ?>" data-discount_type="<?php echo $product['discount_type']; ?>" data-discount_value="<?php echo $product['discount_value']; ?>" data-offer_date_start="<?php echo $product['offer_date_start']; ?>" data-offer_date_end="<?php echo $product['offer_date_end']; ?>"  data-price="<?php echo $product['transferprice']; ?>" >Change Offer</button>
                                <?php } else { ?>
                                <button type="button" class="deals" data-id="<?php echo $product['product_id']; ?>" data-discount_type="<?php echo $product['discount_type']; ?>" data-discount_value="<?php echo $product['discount_value']; ?>" data-offer_date_start="<?php echo $product['offer_date_start']; ?>" data-offer_date_end="<?php echo $product['offer_date_end']; ?>"  data-price="<?php echo $product['transferprice']; ?>" >New Offer</button>
                                <?php } ?>
                                <?php if(!empty($product['sor_product_id'])){ ?>

                                <hr>
                                <span id="tp_input_sor_<?php echo $product['sor_product_id']; ?>">SOR Price: </span>
                                <br>
                                    <span style="<?php echo $show_price; ?>" id="tp_text_<?php echo $product['sor_product_id']; ?>" html-data="<?php echo $product['sor_product_id']; ?>"><?php echo $this->currency->format($product['sor_price'], 'INR', 1); ?></span>
                                    <span style="cursor: pointer; <?php echo $show_price; ?>" class="tp_text" id="tp_edit_<?php echo $product['sor_product_id']; ?>" html-data="<?php echo $product['sor_product_id']; ?>"><i class="fa fa-pencil"></i>
                                    </span>
                                    <span style="display:none;" id="tp_input_<?php echo $product['sor_product_id']; ?>"><input style="width: 60px;" type="text" id="tp_val_<?php echo $product['sor_product_id']; ?>" value="<?php echo $this->currency->format($product['sor_price'], 'INR', 1, false); ?>" /><input type="hidden" id="tp_hid_<?php echo $product['sor_product_id']; ?>" value="<?php echo $this->currency->format($product['sor_price'], 'INR', 1, false); ?>" />
                                        <span style="cursor: pointer;" class="save_tp" html-data="<?php echo $product['sor_product_id']; ?>"><i class="fa fa-save"></i></span>
                                        <span style="cursor: pointer;" class="calcel_tp" html-data="<?php echo $product['sor_product_id']; ?>"><i class="fa fa-ban"></i></span>
                                    </span>
                                <?php } ?>
                                <?php if(!empty($product['singles_price'])){ ?>
                                    <hr>
                                    <span id="tp_input_sngl_<?php echo $product['single_product_id']; ?>">Singles Price: </span>
                                    <br>
                                        <span style="<?php echo $show_price; ?>" id="tp_text_<?php echo $product['single_product_id']; ?>" html-data="<?php echo $product['single_product_id']; ?>"><?php echo $this->currency->format($product['singles_price'], 'INR', 1); ?></span>
                                        <span style="cursor: pointer; <?php echo $show_price; ?>" class="tp_text" id="tp_edit_<?php echo $product['single_product_id']; ?>" html-data="<?php echo $product['single_product_id']; ?>"><i class="fa fa-pencil"></i>
                                        </span>
                                        <span style="display:none;" id="tp_input_<?php echo $product['single_product_id']; ?>"><input style="width: 60px;" type="text" id="tp_val_<?php echo $product['single_product_id']; ?>" value="<?php echo $this->currency->format($product['singles_price'], 'INR', 1, false); ?>" /><input type="hidden" id="tp_hid_<?php echo $product['single_product_id']; ?>" value="<?php echo $this->currency->format($product['singles_price'], 'INR', 1, false); ?>" />
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
                                    <span id="sp_text_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" style="<?php echo $show_price; ?>" html-data="<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>"><?php echo $this->currency->format($store['store_price'], 'INR', 1); ?></span>
                                    <span style="cursor: pointer; <?php echo $show_price; ?>" class="sp_text" id="sp_edit_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" html-data="<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>"><i class="fa fa-pencil"></i>
                                    </span>
                                    <span style="display:none;" id="sp_input_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>"><input style="width: 60px;" type="text" id="sp_val_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" value="<?php echo $this->currency->format($store['store_price'], 'INR', 1, false); ?>" /><input type="hidden" id="sp_hid_<?php echo $product['product_id']; ?>_<?php echo $store['store_id']?>" value="<?php echo $this->currency->format($store['store_price'], 'INR', 1, false); ?>" />
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
                                    <span style="cursor: pointer;" class="des_text pis_text" id="des_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>" data-sor-id="<?php echo $product['sor_product_id']; ?>"  data-pinset="<?php echo $product['piece_in_set']; ?>" data-product-type="<?php echo $product['product_type']; ?>"><i class="fa fa-pencil"></i>
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
                                    <span style="cursor: pointer;" class="pis_text" id="pis_edit_<?php echo $product['product_id']; ?>" html-data="<?php echo $product['product_id']; ?>" data-sor-id="<?php echo $product['sor_product_id']; ?>" data-pinset="<?php echo $product['piece_in_set']; ?>" data-product-type="<?php echo $product['product_type']; ?>"><i class="fa fa-pencil"></i>
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
                                <span><i class="fa fa-pencil fancy_open" style="cursor: pointer; " html-data="<?php echo $product['product_id']; ?>"></i></span>
                                <div style="display:none">
                                    <div id="myDivID_<?php echo $product['product_id']; ?>">
                                        <textarea name="des" rows="10"; cols="50"; class=" input-description1 des_text_<?php echo $product['product_id']; ?>"><?php echo $des; ?></textarea><br />
                                        <input type="button" data-sor-id="<?php echo $product['sor_product_id']; ?>" html-data="<?php echo $product['product_id']; ?>" class="save_des" value="Save" />
                                    </div>
                                </div>
                            </td>
                            <?php //if ($seller_store) { ?>
                               <!--  <td>
                                    <a href="index.php?route=seller/product/edit&product_id=<?php echo $product['product_id']; ?>&sor_product_id=<?php echo $product['sor_product_id']; ?>"><i class="fa fa-edit">Edit</i></a>
                                </td> -->
                            <?php //} ?>
                        </tr>
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
                        <div class="col-sm-7 text-left"><?php echo $pagination_wholesale; ?></div>
                        <div class="col-sm-3 manage_pagination"><?php echo $results_wholesale; ?></div>
                        <div class="col-sm-2 pull-right manage_product_page">
                            <label>products per page</label>
                            <select name="products_per_page" class="products_per_page form-control">
                                <option value="">--SELECT--</option>
                                <option value="50" <?php if($limit == 50){ ?>
                                    selected="selected"
                                   <?php } ?> >50</option>
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
<?php // Vacation Mode popup ?>
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
<?php // SOR Enabled or Disabled Popup  ?>
<div class="show_sor_mgs display_none">
    <h5 class="error_sor_mgs error_pis display_none"><?php echo $error_sor_mgs; ?></h5>
    <div class="form-group required">
        <label class="col-sm-4 control-label" for="markup_price"><?php echo $entry_markup_price; ?></label>
        <div class="col-sm-8">
          <input type="text" name="markup_price" placeholder="<?php echo $text_enter_markup_price; ?>" value="<?php echo $seller_markup_price; ?>" id="markup_price" class="form-control" />
        </div>
    </div>
    <br/>
    <div class="form-group markup_box">
        <div class="col-xs-3 pull-right">
            <a href="javascript:void(0);"><span class="btn btn-success form-control show_sor"> Ok. </span></a>
        </div>
        &nbsp;
        <div class="col-xs-3 pull-left">
            <a href="javascript:void(0);"><span class="cancel_show_sor_mgs">Cancel</span></a>
        </div>
    </div>
</div>
<?php echo $footer_seller; ?>
<script type="text/javascript">
    $(document).ready(function(){
        
        //
        $(document).on('blur','input[name="filters"], input[name="low_price"], input[name="price"], select[name="category_id"]', function(){
           var newUrl = 'index.php?route=seller/manage-inventory';

           var ttFilter = $('input[name="filters"]').val();
           if(ttFilter !=''){
                newUrl += '&filters=' + encodeURIComponent(ttFilter); 
           }

           var ttCategory_id = $('select[name="category_id"]').val();
           if(ttCategory_id != 0 ){
                newUrl += '&category_id=' + encodeURIComponent(ttCategory_id); 
           }
           
           var ttLowPrice = $('input[name="low_price"]').val();
           if(ttLowPrice !=''){
                newUrl += '&low_price=' + encodeURIComponent(ttLowPrice); 
           }

           var ttPrice = $('input[name="price"]').val();
           if(ttPrice !=''){
                newUrl += '&price=' + encodeURIComponent(ttPrice); 
           }            

           $("#manage_inventory").attr('action', newUrl);
        });


        // this function is used for remove limit from url ... bcz code is mixed in controller so i maintain url by javascript.
       function RemoveParameterFromUrl(url, parameter) {
          return url
            .replace(new RegExp('[?&]' + parameter + '=[^&#]*(#.*)?$'), '$1')
            .replace(new RegExp('([?&])' + parameter + '=[^&]*&'), '$1');
        }


        //Update Product limit
        $('.products_per_page').change( function(){
            var per_page = $(this).val();
            $('#products_per_page').attr("value", per_page);
         
           if(per_page){ 

                var newUrl = $("#manage_inventory").attr('action');                
                newUrl = RemoveParameterFromUrl(newUrl,'limit');
                newUrl += '&limit=' + encodeURIComponent(per_page);                
                $("#manage_inventory").attr('action', newUrl);
           }
           else {

                var newUrl = $("#manage_inventory").attr('action');
                newUrl = RemoveParameterFromUrl(newUrl,'limit');              
                $("#manage_inventory").attr('action', newUrl);
           }

           document.getElementById("manage_inventory").submit();
        });

        //Update Quantity
        $("span.quantity_text").click(function(){
            var pid = $(this).attr("html-data");
            $("span#quantity_text_"+pid).hide();
            $("span#quantity_edit_"+pid).hide();
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
                data: "quantity="+$("input#quantity_val_"+$(this).attr("html-data")).val()+"&product_id="+$(this).attr("html-data")+"&product_model="+$(this).attr("html-model")+"&sor_product_id="+$(this).attr("data-sor-id"),
                success: function( data ) {

                }
            });
        });

        $("span.calcel_quantity").click(function(){
            $("span#quantity_input_"+$(this).attr("html-data")).hide();
            var preval = $("span#quantity_text_"+$(this).attr("html-data")).html();
            $("input#quantity_val_"+$(this).attr("html-data")).attr("value",preval);
            $("span#quantity_text_"+$(this).attr("html-data")).show();
            $("span#quantity_edit_"+$(this).attr("html-data")).show();
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


        //Update Wholesale Price
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
            var val = 'Rs. '+$("input#tp_val_"+$(this).attr("html-data")).val();
            var new_price = parseFloat($("input#tp_val_"+$(this).attr("html-data")).val());
            var existing_price = parseFloat($("input#tp_hid_"+$(this).attr("html-data")).val());
            var exist_val = 'Rs. '+$("input#tp_hid_"+$(this).attr("html-data")).val();

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

        //Update Store Price
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

        //Update Pieces in Set
        //Update Set description
        $("span.pis_text").click(function(){
            var pid                 =   $(this).attr("html-data");
            var sor_id              =   $(this).attr("data-sor-id");
            var pieceinset          =   $(this).attr("data-pinset");
            var product_type        =   $(this).attr("data-product-type");
            var data = "&product_id="+pid+"&sor_id="+sor_id+"&pieceinset="+pieceinset;
            var href =  product_type + data;

            $.fancybox({
                'hideOnContentClick': true,
                width:'100%',
                maxWidth: 980,
                padding:0,
                type: 'iframe',
                href: href,
                helpers : {
                    overlay : {closeClick: false}
                },
                iframe: {
                    preload: false // fixes issue with iframe and IE
                },
                'onComplete' : function() {
                    $('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
                        $('#fancybox-content').height($(this).contents().find('body').height()+30);
                    });
                }
            });
        });

        // Not Working right now.
        $(".sell_single").click(function() {
            var pid = $(this).attr("html-pid");
            $.ajax({
                url : "index.php?route=seller/manage-inventory/copyProductToSingle",
                type: "post",
                data: "pid="+pid,
                success: function( data ) {
                    // window.location.reload();
                }
            });
        });

        // Show Product in store or not
        $(".show_store").click(function(){
            var pid = $(this).attr("html-pid");
            var store = $(this).attr("html-store");
            var product_status = $(this).attr("html-product-status");
            var sor_product_id = $(this).attr("html-sor-pid");
            var sngl_product_id = $(this).attr("html-sngl-pid");
            var product = document.getElementById('show_store_'+pid+'_'+ store).checked ;
            if(product == true){
                var ischeck = 1;
                if(store != 0){
                    $('#sp_text_'+pid+'_'+store).show();
                    $('#sp_edit_'+pid+'_'+store).show();
                }else{
                    $('#tp_text_'+pid).show();
                    $('#tp_edit_'+pid).show();
                    $('#tp_type_'+pid).show();
                    $('#tp_text_'+sor_product_id).show();
                    $('#tp_edit_'+sor_product_id).show();
                    $('#tp_input_sor_'+sor_product_id).show();
                    $('#tp_text_'+sngl_product_id).show();
                    $('#tp_edit_'+sngl_product_id).show();
                    $('#tp_input_sngl_'+sngl_product_id).show();

                }
            }else{
                var ischeck = 0;
                if(store != 0){
                    $('#sp_text_'+pid+'_'+store).hide();
                    $('#sp_edit_'+pid+'_'+store).hide();
                }else{
                    $('#tp_text_'+pid).hide();
                    $('#tp_edit_'+pid).hide();
                    $('#tp_type_'+pid).hide();
                    $('#tp_text_'+sor_product_id).hide();
                    $('#tp_edit_'+sor_product_id).hide();
                    $('#tp_input_sor_'+sor_product_id).hide();
                    $('#tp_text_'+sngl_product_id).hide();
                    $('#tp_edit_'+sngl_product_id).hide();
                    $('#tp_input_sngl_'+sngl_product_id).hide();

                }
            }
            $.ajax({
                url : "index.php?route=seller/manage-inventory/showProductInStore",
                type: "post",
                data: "pid="+pid+"&store_id="+store+"&ischeck="+ischeck+"&product_status="+product_status+"&sor_product_id="+sor_product_id,
                success: function( data ) {
                    //window.location.reload();
                }
            });
        });

        // Open Description in fancybox
        $(".fancy_open").click(function() {
            var pid = $(this).attr("html-data");
            $.fancybox({
                'href': '#myDivID_'+pid,
                'titleShow': false,
                'transitionIn': 'elastic',
                'transitionOut': 'elastic',
                'width':400,
                'height':300,
                'autoSize' : false,
            });
        });

        // Update/Save Description Open in fancybox
        $(".save_des").click(function() {
            var pid = $(this).attr("html-data");
            var sor_product_id = $(this).attr("data-sor-id");
            //alert(pid);
            var des = $(".des_text_"+pid).val();
            //alert(des);
            $.ajax({
                url : "index.php?route=seller/manage-inventory/updateProductDescription",
                type: "post",
               // dataType: "json",
                data: "des="+des+"&pid="+pid+"&sor_product_id="+sor_product_id,
                success: function( data ) {
                   // alert(data);
                    $('.desc_text_'+pid).html(des);
                    parent.jQuery.fancybox.close();
                }
            });
        });

        // Select All Product in that page by Select all checkbox at top and left at s.no.
        $(".select_all_product").click(function() {
            $('input[name*=\'selected\']').prop('checked', this.checked);
        });

        // Update Status Of Product Like - In stock, Out of Stock
        $(".product_status_change").change(function() {
            var val = $(this).val();
            if(val == 'out_stock'){
               confirm("Are you sure you want stock out this product") ? $('#manage_inventory').submit() : false;
            }
            if(val == 'in_stock'){
               confirm("Are you sure you want stock In this product") ? $('#manage_inventory').submit() : false;
            }
        });
        // If seller is ON Vacation then we Enabled Seller vacation Mode Enabled and because of that all the seller Product are not shown at Wholesale store
        $(".vacation").click(function() {
            $('.show_vacation_mgs').show();
            var val = $(this).attr('data-val');
            $('.show_vacation').attr('data-val', val);
        });
        $(".cancel_show_vacation_mgs").click(function() {
            $('.show_vacation_mgs').hide();
        });
        $(".show_vacation").click(function() {
            $('.show_vacation_mgs').hide();
            var val = $(this).attr('data-val');
            $.ajax({
                url : "index.php?route=seller/manage-inventory/updateVacation",
                type: "post",
                data: "vacation_val="+val,
                beforeSend: function() {
                    $('.vacation').html('processing...');
                },
                success: function( data ) {
                    window.location.reload();
                }
            });
        });

        // Update SOR field in ms_seller
        $("#sor_enable").click(function() {
            var button = $(this);
            set_sor = button.attr('data-sor');
            if(set_sor == 1){
                $('.show_sor_mgs').show();
            }else{
                var x = '';
                ajaxSor(x);
            }
        });
        $(".show_sor").click(function(){
            var markup_price = $('#markup_price').val();
            if(markup_price != ''){
                ajaxSor(markup_price);
                $('.show_sor_mgs').hide();
            }else{
                $('.error_sor_mgs').show();
            }
        });
        
        //archive inventory 
        $('#archive_inventory').on('click',function() {
			
			var product_ids = [];
            var archived_value = $(this).val();
            var archived_yes_or_no = <?php echo ($product_archived) ? $product_archived : 0 ; ?>;

			$('input[name=\'selected[]\']:checked').each(function(){
				product_ids.push($(this).val());
			  });
			  
			  if(product_ids == ''){
				alert('Please select your product !');
			  }else{
				$.ajax({
				  type: 'post',
                  url: 'index.php?route=seller/manage-inventory/archiveInventory',
				  data : 'product_ids='+product_ids+'&archived_value='+archived_value,
				  dataType: 'json',
				  beforeSend: function() {
					$('.archive_inventory').button('processing');
				  },
				  complete: function() {
					$('.archive_inventory').button('reset');
				  },
				  success: function (json) {
					if(json == 1) {
                        if(archived_yes_or_no){
                            alert("Products successfully removed from archive");
                        } else {
                            alert("Products successfully marked as archive");     
                        }
					   
					  $('.archive_listing').val('');
					  $('#manage_inventory input[type=\'checkbox\']').removeAttr('checked');
					  window.location.reload();
					}
				  }
				});
			  }
		});
		
        function ajaxSor(markup_price) {
            var button = $("#sor_enable");
            set_sor = button.attr('data-sor');
            if(markup_price != ''){
                var markup_price = markup_price;
            }else{
                var markup_price = '';
            }
            $.ajax({
                type: "GET",
                dataType: "json",
                url: 'index.php?route=seller/manage-inventory/changeSorSetting',
                data: 'sor_setting='+set_sor+'&markup_price='+markup_price,
                beforeSend: function() {
                    button.val('processing...');
                    button.html('processing...');
                },
                complete: function(jqXHR, textStatus) {
                    //button.reset();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                   alert('Error in change SOR setting.');
                },
                success: function(jsonData) {
                    button.val(jsonData['label']);
                    button.html(jsonData['label']);
                    button.removeClass().addClass(jsonData['css']);
                    button.attr('data-sor', jsonData['change_sor']);
                }
            });
        }
        $(".cancel_show_sor_mgs").click(function() {
            $('.show_sor_mgs').hide();
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
