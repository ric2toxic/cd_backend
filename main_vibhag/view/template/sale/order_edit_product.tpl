<div class="row products_panel hidden tabs edit_product">
    <div class="col-lg-12">
        <input type="hidden" form="product_form" name="order_id" value="<?php echo $order_id; ?>" />
        <input type="hidden" form="product_form" name="suborder_id" value="<?php echo $suborder_id; ?>" />
        <form action="<?php echo $save; ?>" id="product_form" onsubmit="return handleData(this)" method="post">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>Sr No.</td>
                        <td>Name</td>
                        <td class="hidden">Pieces In Set</td>
                        <td>Total Pieces</td>
                        <td>Price Per Piece</td>
                        <td>Set Description</td>
                        <td>Edit Type</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($products)) { ?>
                    <?php foreach ($products as $key => $product) { ?>
                        <?php $editable_class = strtolower($product['edit_type']) == 'yes' ? ' editable ' : ''; ?>
                            <tr>
                                <td><?php echo $key + 1; ?></td>
                                <td>
                                    <img src="<?php echo $product['image']; ?>"
                                         width="<?php echo $product['width']; ?>px"
                                         height="<?php echo $product['height']; ?>px"
                                         class="pull-left" />
                                    <p style="margin-left:10px;width: calc( 100% - 100px );" class="pull-left"><?php echo $product['name']; ?><br><br>
                                        <b>Model: </b><?php echo $product['model']; ?><br><br>
                                        <b>Seller Sku: </b><?php echo $product['seller_sku']; ?>
                                    </p>
                                </td>
                                <!-- <td class="hidden">
                                    <span><?php echo $product['piece_in_set']; ?></span>
                                    <input type="number"
                                           name="products[<?php echo $product['order_product_id']; ?>][piece_in_set][new]"
                                           value="1"
                                           class="hidden form-control"
                                           min="0"
                                           data-old="<?php echo $product['piece_in_set']; ?>" />
                                    <input type="hidden"
                                          name="products[<?php echo $product['order_product_id']; ?>][piece_in_set][old]"
                                          value="<?php echo $product['piece_in_set']; ?>" />
                                </td> -->
                                <td class="<?php echo $editable_class; ?>" style="width:100px!important">
                                    <span><?php echo ((int) $product['quantity'] * (int) $product['piece_in_set']); ?></span>
                                    <input type="number"
                                           name="products[<?php echo $product['order_product_id']; ?>][quantity][new]"
                                           value="<?php echo ((int) $product['quantity'] * (int) $product['piece_in_set']); ?>"
                                           class="hidden form-control"
                                           min="0"
                                           max="<?php echo ((int) $product['quantity'] * (int) $product['piece_in_set']); ?>"
                                           data-old="<?php echo ((int) $product['quantity'] * (int) $product['piece_in_set']); ?>" />
                                    <input type="hidden"
                                           name="products[<?php echo $product['order_product_id']; ?>][quantity][old] *"
                                           value="<?php echo ((int) $product['quantity'] * (int) $product['piece_in_set']); ?>" />
                                </td>
                                <td width="150px">
                                    <span><?php echo $product['price_per_piece']; ?></span>
                                    <input type="text"
                                           name="products[<?php echo $product['order_product_id']; ?>][price_per_piece][new]"
                                           value="<?php echo $product['price_per_piece']; ?>"
                                           class="hidden form-control"
                                           data-old="<?php echo $product['price_per_piece']; ?>"
                                           disabled/>
                                    <input type="hidden"
                                           name="products[<?php echo $product['order_product_id']; ?>][price_per_piece][old]"
                                           value="<?php echo $product['price_per_piece']; ?>" disabled />
                                </td>
                                <td class="<?php echo $editable_class; ?>" width="150px">
                                    <span><?php echo $product['comment']; ?></span>
                                    <textarea name="products[<?php echo $product['order_product_id']; ?>][set_description][new]"
                                              placeholder="Comment"
                                              data-old="<?php echo $product['comment']; ?>"
                                              class="hidden form-control pull-left required"
                                              style="width:140px;resize: vertical"
                                              ><?php echo $product['comment']; ?></textarea>
                                    <input type="hidden"
                                           name="products[<?php echo $product['order_product_id']; ?>][set_description][old]"
                                           value="<?php echo $product['comment']; ?>" />
                                </td>
                                <td style="width:150px!important" class="edit_type_td">
                                    <?php if (strtolower($product['edit_type']) == 'yes') { ?>
                                        <select name="products[<?php echo $product['order_product_id']; ?>][edit_type]"
                                                class="form-control required edit_type"
                                                disabled >
                                            <option value="">Select Edit Type</option>
                                            <?php foreach ($edit_types as $edit_type) { ?>
                                                <option value="<?php echo $edit_type; ?>">
                                                    <?php echo str_replace('_', ' ', $edit_type); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <?php
                                    } else {
                                        echo str_replace("_", ' ', $product['edit_type']);
                                    }
                                    ?>

                                </td>
                                <td class="action_row" width="200px">
                                    <?php if (strtolower($product['edit_type']) == 'yes') { ?>
                                        <textarea name="products[<?php echo $product['order_product_id']; ?>][comment]"
                                                  placeholder="Comment"
                                                  class="hidden form-control pull-left required"
                                                  style="width:140px;resize: vertical"
                                                  ></textarea>
                                                  <?php if ($product['seller_invoice_id'] == '' || ($product['sor_product'] == 1 && $invoice_no == 0)) { ?>
                                            <a class="btn btn-sm btn-danger delete_btn pull-right"
                                               style="margin:0 0px 5px 5px;"
                                               onclick="deleteProduct(this, product_<?php echo $product['order_product_id']; ?>)">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        <?php } ?>
                                        <a class="btn btn-sm btn-danger hidden pull-right reset_btn"
                                           onclick="resetProduct(this, product_<?php echo $product['order_product_id']; ?>)">
                                            <i class="fa fa-reply"></i>
                                        </a>
                                        <input type="checkbox"
                                               id="product_<?php echo $product['order_product_id']; ?>"
                                               name="products[<?php echo $product['order_product_id']; ?>][delete]"
                                               class="hidden form-control"
                                               value="1" />
                                    </td>
                                    <?php
                                } else if (!empty($product['edit_history'])) {
                                    $edit_history = unserialize($product['edit_history']);
                                    reset($edit_history);
                                    $edit_history = end($edit_history);
                                    echo 'Edited By <b>' . (isset($edit_history['user_name']) ? $edit_history['user_name'] : "") .
                                    '</b><br> on <b>' . date('d-m-Y', strtotime($edit_history['date_added'])) . '</b>';
                                }
                                ?>
                            </tr>

                        <?php } ?>
                    <?php } ?>
                    </tbody>
            </table>
        </form>
    </div>
    <br>
    <div class="col-lg-12">
        <button type="subimt" onclick="validateChanges('#product_form')" class="btn btn-primary pull-right clearfix">
            <i class="fa fa-save"></i>  Save
        </button>
    </div>
</div>