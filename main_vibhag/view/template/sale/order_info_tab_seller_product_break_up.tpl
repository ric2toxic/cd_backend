<style type="text/css">
    .email_sent{
        
    color: blue;
    display: inline-block;
    cursor: pointer;
    margin: 0 0 0 10px;
    padding: 4px 10px 0;
    }
    .email_sent_box_close {
        background: #000 none repeat scroll 0 0;
    border: 0px groove #000; border-radius: 20px; color: #fff; cursor: pointer; float: right; margin: -10px -5px; padding: 1px 6px;
    }
    .email_sent_box {
        margin: auto; box-shadow: 1px 1px 10px #ccc; border-radius: 5px; position: fixed; top: 30%; left: 50%; background: #dcdcdc;
    }
    .email_sent_box_outter_div {
        max-height: 300px;
    overflow-y: scroll;
    margin-top: 15px;
    min-height: 100px;
    }
</style>
<?php if ($show_store_sales_notice) { ?>
    <div class="alert alert-warning alert-dismissible" role="alert" style="color: #8a6d3b;letter-spacing: 0.5px;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Note: </strong>For the highlighted items in yellow color, NO pickup is required. These are sold from WholesaleBox stores directly.
    </div><br>
<?php } ?>
<div class="scroll_actions pull-right">
    <span id="seller_not_given_button" class="btn btn-danger btn-xs">Seller Not Given</span>
    <span id="seller_later_disptach_button" class="btn btn-warning btn-xs">Seller Later Disptach</span>
    <span id="cancelled_by_customer_button" class="btn btn-info btn-xs">Cancelled By Customer</span>
</div>
<br>
<?php if (!empty($sellers)) { ?>
    <div class="col-sm-12 seller_approved_partial_yes">
        <h3>
            <?php echo $text_order_no . $order_no . "   -  Total Purchase Value: " . $total_purchase_value; ?> 
            <span class="pull-right">
                <a href="<?php echo $breakup_print; ?>" 
                   target="_blank" 
                   data-toggle="tooltip" 
                   title="<?php echo $button_seller_product_breakup; ?>" 
                   class="btn btn-info">
                    <i class="fa fa-download"></i>
                </a>
            </span>
        </h3>
        <hr style="height:1px;border:none;color:blue;background-color:blue;">
        <?php foreach ($sellers as $seller) { ?>
            <table style="width:100%;" border="0" >
                <tr>
                    <td style="width:70%">
                        <h4><?php echo $seller_company[$seller]; ?></h4>
                    </td>
                    <?php if (!($data['request_page'] ==  'franchise_seller_product_breakup')) { ?>
                    <td style="width:30%; text-align:right;">

                        <span id="email_sent<?php echo $seller; ?>" class="email_sent" data-seller-id="<?php echo $seller; ?>" >
                            <?php
                            if(!empty($order_info_seller_mail_log_count[$seller])) {
                                echo "Email Sent (".$order_info_seller_mail_log_count[$seller].")";
                            }
                            ?>
                            </span>
                            <div class=" hidden" id="email_sent_<?php echo $seller;?>">
                                <div class="btn-div-cls">
                                    <button type="button" class="email_sent_box_close" style=""><i class="fa fa-close"></i></button>
                                </div>
                                <div class="email_sent_box_outter_div">
                                    
                                    <table class="table table-bordered table-hover table_seller_mail<?php echo $seller; ?>">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Action</th>
                                                <th>Action Date</th>
                                                <th>Action by</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if(!empty($order_info_seller_mail_log[$seller])) {
                                                $x = 1;
                                                foreach ($order_info_seller_mail_log[$seller] as $action_key => $mail_infos) {
                                                    if(!empty($mail_infos)) {
                                                        foreach ($mail_infos as $mail_info) {
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $x; ?></td>
                                                                <td><?php echo $action_key; ?></td>
                                                                <td><?php echo $mail_info['date_added']; ?></td>
                                                                <td><?php echo $mail_info['name']; ?></td>
                                                            </tr>
                                                            <?php
                                                            $x++;
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        
                        <button type="button" id=<?php echo '"button-send-' . $order_id . '-' . $seller . '"'; ?> data-seller_id = "<?php echo $seller; ?>" data-order_id = "<?php echo $order_id; ?>" data-suborder_id = "<?php echo $suborder_id; ?>" data-toggle="tooltip" title="<?php echo $button_send . $seller_company[$seller]; ?>" class="btn btn-primary send_email" >
                            <i class="fa fa-envelope"></i>
                        </button>
                        <?php
                        if (!empty($invoice_links[$seller])) {
                            foreach ($invoice_links[$seller] as $key => $values) {
                                ?>
                                <a href="<?php echo $values; ?>" data-toggle="tooltip" title="<?php echo $text_seller_invoice_download; ?>" class="btn btn-warning btn-xs" > <i class="fa fa-download"></i> <?php echo $key; ?> </a>
                                <?php
                            }
                        }
                        ?>
                    </td>
                    <?php } ?>
                </tr>
            </table>
            <br>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td class="text-left"><?php echo 'S. No.'; ?></td>
                        <td class="text-left"><?php echo $column_sku; ?></td>
                        <td class="text-left"><?php echo $column_product_launch_date; ?></td>
                        <td class="text-left"><?php echo $column_wsb_product_code; ?></td>
                        <td class="text-left"><?php echo $column_set_desc; ?></td>
                        <td class="text-right"><?php echo $column_user_comment; ?></td>
                        <td class="text-right"><?php echo $column_sets; ?></td>
                        <td class="text-right"><?php echo $column_piece_in_set; ?></td>
                        <td class="text-right"><?php echo $column_total_pieces; ?></td>
                        <td class="text-right"><?php echo $column_transfer_price; ?></td>
                        <td class="text-right"><?php echo $column_amount; ?></td>
                        <td class="text-right"><?php echo $seller_product_breakup_action; ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($seller_breakup[$seller] as $product) {
                        $tr_css = 'inherit';
                        if ($product['store_sales'] != 'NO') {
                            $tr_css = '#fff8c3';
                        } else if ($product['pickup_status'] == 'Received') {
                            $tr_css = '#dff7aa';
                        } else if ($product['pickup_status'] == 'Issue') {
                            $tr_css = '#f9c4b1';
                        }
                        ?>
                        <tr id="seller_product_<?php echo $product['order_product_id']; ?>" style="background-color:<?php echo $tr_css; ?>">
                            <td class="text-left"><?php echo $product['index']; ?>
                            <td class="text-left">
                                <img src="<?php echo $product['image']; ?>" width="<?php echo $product['width']; ?>" height="<?php echo $product['height']; ?>" alt="<?php echo $product['sku']; ?>">
                                <br><br>
                                <?php echo $product['sku']; ?></td>
                            <td><b><?php echo $product['expected_dispatch_date']; ?></b></td>
                            <td class="text-left"><?php echo $product['model']; ?></td>
                            <td class="text-left">
                                <span id="oop_comment_<?php echo $product['order_product_id']; ?>" data-oopid="<?php echo $product['order_product_id']; ?>" data-field="comment" class="not-hide">
                                    <?php echo $product['set_description']; ?></span>

                                <?php if (empty($invoice_no) && $product['seller_invoice_id']==0) { ?>
                                    <span style="cursor: pointer;" class="oop_field_edit" id="oop_comment_edit_<?php echo $product['order_product_id']; ?>" data-oopid="<?php echo $product['order_product_id']; ?>" data-field="comment"><i class="fa fa-pencil"></i></span>

                                    <span style="display:none;" id="oop_comment_input_<?php echo $product['order_product_id']; ?>">
                                        <input type="text" id="oop_comment_val_<?php echo $product['order_product_id']; ?>" value="<?php echo $product['set_description']; ?>" />

                                        <span style="cursor:pointer;" class="save_oop_field" data-oopid="<?php echo $product['order_product_id']; ?>" data-field="comment"><i class="fa fa-save"></i></span>

                                        <span style="cursor:pointer;" class="cancel_oop_field" data-oopid="<?php echo $product['order_product_id']; ?>" data-field="comment"><i class="fa fa-ban"></i></span></span>
                                <?php } ?>
                            </td>

                            <td class="text-right order_list_comment"><?php echo $product['customer_comment']; ?></td>
                            <td class="text-right"><?php echo $product['quantity']; ?></td>
                            <td class="text-right"><?php echo $product['piece_in_set']; ?></td>
                            <td class="text-right editable ">
                                <span class="total-piece"><?php echo $product['total_pieces']; ?></span>
                                <input type="number" name="total_pieces[<?php echo $product['order_product_id']; ?>][quantity][new]" value="<?php echo $product['total_pieces']; ?>" class="hidden form-control total-qty-new" min="1" max="<?php echo $product['total_pieces']; ?>" data-old="<?php echo $product['total_pieces']; ?>" />
                                <input type="hidden" name="total_pieces[<?php echo $product['order_product_id']; ?>][quantity][old]" value="<?php echo $product['total_pieces']; ?>" class="total-qty-old" />
                                <input type="hidden" name="seller_id_no[<?php echo $product['order_product_id']; ?>]" value="<?php echo $seller; ?>" class="seller_id_no" />
                                <input type="hidden" name="seller_invoice_id[<?php echo $product['order_product_id']; ?>]" value="<?php echo $product['seller_invoice_id']; ?>"  />
                                <input type="hidden" name="product_id[<?php echo $product['order_product_id']; ?>]" value="<?php echo $product['product_id']; ?>"  />
                                
                            </td>
                            <td class="text-right"><?php echo $product['transfer_price']; ?></td>
                            <td class="text-right"><?php echo $product['amount']; ?></td>
                            <td class="text-right editable ">
                                <?php
                                //((!empty($invoice_links[$seller]) || $seller_invoice_generate == 1) && $product['pickup_status'] == 'Not_Given')

                                if ($product['pickup_status'] == 'Not_Given' || $product['pickup_status'] == 'Picked_Up') {
                                    ?>
                                    <div class="action_buttons without_status">
                                        <span>
                                            <button data-toggle="tooltip" title="Seller has given all the pieces" data-status="Received" data-oopid="<?php echo $product['order_product_id']; ?>" class="pickup_status " style="margin-bottom: 5px;border-radius: 3px;border: none;font-weight: 600;width: 90px;padding: 0 0 0 2px;text-align: -webkit-center;line-height: 2;">Received <i class="fa fa-check-circle btn-success pull-right" style="padding: 6px;border-radius: 0 3px 3px 0;"></i></button>
                                        </span>
                                        <span>
                                            <button data-toggle="tooltip" title="Seller has given short pieces, but invoiced wrongly for more pieces" data-status="partial_receive" data-oopid="<?php echo $product['order_product_id']; ?>" class="pickup_status " style="margin-bottom: 5px;border-radius: 3px;border: none;font-weight: 600;width: 90px;padding: 0 0 0 2px;text-align: -webkit-center;line-height: 2;">Partial <i class="fa fa-exclamation-circle btn-warning pull-right" style="padding: 6px;border-radius: 0 3px 3px 0;"></i></button>
                                        </span>
                                        <span>
                                            <button data-toggle="tooltip" title="Seller has not given any pieces, but invoiced for them" data-status="Not_Given" data-oopid="<?php echo $product['order_product_id']; ?>" class="pickup_status " style="margin-bottom: 5px;border-radius: 3px;border: none;font-weight: 600;width: 90px;padding: 0 0 0 2px;text-align: -webkit-center;line-height: 2;">Not Given <i class="fa fa-times-circle btn-danger pull-right" style="padding: 6px;border-radius: 0 3px 3px 0;"></i></button>
                                        </span>
                                        <span>
                                            <button data-toggle="tooltip" title="Some piece(s) are either wrong, damaged, have issues etc." data-status="Issue" data-oopid="<?php echo $product['order_product_id']; ?>" class="pickup_status " style="margin-bottom: 5px;border-radius: 3px;border: none;font-weight: 600;width: 90px;padding: 0 0 0 2px;text-align: -webkit-center;line-height: 2;">Issue <i class="fa fa-ban btn-danger pull-right" style="padding: 6px;border-radius: 0 3px 3px 0;"></i></button>
                                        </span>
                                        
                                        
                                        <textarea name="products[<?php echo $product['order_product_id']; ?>][comment]"
                                                          placeholder="Comment"
                                                          class="hidden form-control required"
                                                          style="width:140px;resize: vertical"
                                                          ></textarea>
                                        <br>
                                        <div class="save-reset hidden" style="width: 70px;">
                                            <button data-toggle="tooltip" title="Save" data-status="" data-oopid="<?php echo $product['order_product_id']; ?>" class="btn btn-sm btn-success save-btn pickup_status"><i class="fa fa-floppy-o" ></i></button><label>&nbsp;&nbsp;</label>
                                            <a data-toggle="tooltip" title="Cancel" class="btn btn-sm btn-danger pull-right"
                                                       onclick="resetProduct(this, 'product_<?php echo $product['order_product_id']; ?>')">
                                                        <i class="fa fa-reply"></i>
                                            </a>
                                        </div>
                                        
                                        
                                                
                                    </div> 
                                    <?php
                                }
                                if(!empty($product['pickup_last_modified'])){
                                    echo $product['pickup_status'] . ' [ ' . date('dS M, Y g:i A', strtotime($product['pickup_last_modified'])) . ' ]';
                                }
                                ?>  
                            </td>
                            <?php if (empty($invoice_no) && $user_id == '43') { ?>
                                <td class="text-right"><span class="oop_delete" data-oop="<?php echo $product['order_product_id']; ?>"><div class="btn btn-danger" id="oop_delete_<?php echo $product['order_product_id']; ?>" ><i class="fa fa-minus-square"></i></div></span></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="11" class="text-right"><strong><?php echo $entry_total_seller_amt; ?></strong></td>
                        <td class="text-right"><strong><?php echo $seller_totals[$seller]; ?></strong></td>
                    </tr>
                </tbody>
            </table>
            </br>
        <?php } ?>
        <div class="email_sent_box" >
         </div>
    </div>
<?php } ?>
<?php if (!empty($seller_not_given['sellers'])) { ?>
    <div class="col-sm-12" id="seller_not_supplied">
        <h1 id="seller_not_supplied_heading"><b><u>Seller Not Given</u></b></h1>
        <h3>
            <?php echo $text_order_no . $order_no . "   -  Total Purchase Value: " . $seller_not_given['total_purchase_value']; ?> 
          <!--<span class="pull-right">
            <a href="<?php echo $breakup_print; ?>" 
               target="_blank" 
               data-toggle="tooltip" 
               title="<?php echo $button_seller_product_breakup; ?>" 
               class="btn btn-info">
              <i class="fa fa-download"></i>
            </a>
          </span> -->
        </h3>
        <hr style="height:1px;border:none;color:blue;background-color:blue;">
        <?php foreach ($seller_not_given['sellers'] as $seller) { ?>
            <table style="width:100%;" border="0" >
                <tr>
                    <td style="width:100%">
                        <h4><?php echo $seller_not_given['seller_company'][$seller]; ?></h4>
                    </td>
                </tr>
            </table>
            <br>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td class="text-left"><?php echo $column_sku; ?></td>
                        <td class="text-left"><?php echo $column_wsb_product_code; ?></td>
                        <td class="text-left"><?php echo $column_product_launch_date; ?></td>
                        <td class="text-left"><?php echo $column_set_desc; ?></td>
                        <td class="text-right"><?php echo $column_sets; ?></td>
                        <td class="text-right"><?php echo $column_piece_in_set; ?></td>
                        <td class="text-right"><?php echo $column_total_pieces; ?></td>
                        <td class="text-right"><?php echo $column_transfer_price; ?></td>
                        <td class="text-right"><?php echo $column_amount; ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($seller_not_given['seller_breakup'][$seller] as $product) { ?>
                        <tr id="seller_product_<?php echo $product['order_product_id']; ?>" style="background-color:<?php echo $product['store_sales'] != 'NO' ? '#fff8c3' : 'inherit'; ?>">
                            <td class="text-left"><?php echo $product['sku']; ?>
                            <td class="text-left"><?php echo $product['model']; ?></td>
                            <td><b><?php echo $product['expected_dispatch_date']; ?></b></td>
                            <td class="text-left">
                                <span id="oop_comment_<?php echo $product['order_product_id']; ?>" data-oopid="<?php echo $product['order_product_id']; ?>" data-field="comment">
                                    <?php echo $product['set_description']; ?></span>
                            </td>

                            <td class="text-right"><?php echo $product['quantity']; ?></td>
                            <td class="text-right"><?php echo $product['total_pieces']; ?></td>
                            <td class="text-right"><?php echo $product['piece_in_set']; ?></td>
                            <td class="text-right"><?php echo $product['transfer_price']; ?></td>
                            <td class="text-right"><?php echo $product['amount']; ?></td>
                            <!-- <td class="text-right">
                              
                            </td> -->
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="8" class="text-right"><strong><?php echo $entry_total_seller_amt; ?></strong></td>
                        <td class="text-right"><strong><?php echo $seller_not_given['seller_totals'][$seller]; ?></strong></td>
                    </tr>
                </tbody>
            </table>
            </br>
        <?php } ?>
    </div>
<?php } ?>

<?php if (!empty($seller_later_dispatch['sellers'])) { ?> 
    <div class="col-sm-12" id="seller_later_dispatch">
        <h1 id="seller_later_disptach_heading"><b><u>Seller Later Dispatch</u></b></h1>
        <h3>
            <?php echo $text_order_no . $order_no . "   -  Total Purchase Value: " . $seller_later_dispatch['total_purchase_value']; ?> 
          <!--<span class="pull-right">
            <a href="<?php echo $breakup_print; ?>" 
               target="_blank" 
               data-toggle="tooltip" 
               title="<?php echo $button_seller_product_breakup; ?>" 
               class="btn btn-info">
              <i class="fa fa-download"></i>
            </a>
          </span> -->
        </h3>
        <hr style="height:1px;border:none;color:blue;background-color:blue;">
        <?php foreach ($seller_later_dispatch['sellers'] as $seller) { ?>
            <table style="width:100%;" border="0" >
                <tr>
                    <td style="width:100%">
                        <h4><?php echo $seller_later_dispatch['seller_company'][$seller]; ?></h4>
                    </td>
                </tr>
            </table>
            <br>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td class="text-left"><?php echo $column_sku; ?></td>
                        <td class="text-left"><?php echo $column_wsb_product_code; ?></td>
                        <td class="text-left"><?php echo $column_product_launch_date; ?></td>
                        <td class="text-left"><?php echo $column_set_desc; ?></td>
                        <td class="text-right"><?php echo $column_sets; ?></td>
                        <td class="text-right"><?php echo $column_piece_in_set; ?></td>
                        <td class="text-right"><?php echo $column_total_pieces; ?></td>
                        <td class="text-right"><?php echo $column_transfer_price; ?></td>
                        <td class="text-right"><?php echo $column_amount; ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($seller_later_dispatch['seller_breakup'][$seller] as $product) { ?>
                        <tr id="seller_product_<?php echo $product['order_product_id']; ?>" style="background-color:<?php echo $product['store_sales'] != 'NO' ? '#fff8c3' : 'inherit'; ?>">
                            <td class="text-left"><?php echo $product['sku']; ?>
                            <td class="text-left"><?php echo $product['model']; ?></td>
                            <td><b><?php echo $product['expected_dispatch_date']; ?></b></td>
                            <td class="text-left">
                                <span id="oop_comment_<?php echo $product['order_product_id']; ?>" data-oopid="<?php echo $product['order_product_id']; ?>" data-field="comment">
                                    <?php echo $product['set_description']; ?></span>
                            </td>

                            <td class="text-right"><?php echo $product['quantity']; ?></td>
                            <td class="text-right"><?php echo $product['piece_in_set']; ?></td>
                            <td class="text-right"><?php echo $product['total_pieces']; ?></td>
                            <td class="text-right"><?php echo $product['transfer_price']; ?></td>
                            <td class="text-right"><?php echo $product['amount']; ?></td>
                            <!-- <td class="text-right">
                              
                            </td> -->
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="8" class="text-right"><strong><?php echo $entry_total_seller_amt; ?></strong></td>
                        <td class="text-right"><strong><?php echo $seller_later_dispatch['seller_totals'][$seller]; ?></strong></td>
                    </tr>
                </tbody>
            </table>
            </br>
        <?php } ?>
    </div>
<?php } ?>

<?php if (!empty($cancelled_by_customer['sellers'])) { ?> 
    <div class="col-sm-12" id="cancelled_by_customer">
        <h1 id="seller_later_disptach_heading"><b><u>Cancelled By Customer</u></b></h1>
        <h3>
            <?php echo $text_order_no . $order_no . "   -  Total Purchase Value: " . $cancelled_by_customer['total_purchase_value']; ?> 
        </h3>
        <hr style="height:1px;border:none;color:blue;background-color:blue;">
        <?php foreach ($cancelled_by_customer['sellers'] as $seller) { ?>
            <table style="width:100%;" border="0" >
                <tr>
                    <td style="width:100%">
                        <h4><?php echo $cancelled_by_customer['seller_company'][$seller]; ?></h4>
                    </td>
                </tr>
            </table>
            <br>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td class="text-left"><?php echo $column_sku; ?></td>
                        <td class="text-left"><?php echo $column_wsb_product_code; ?></td>
                        <td class="text-left"><?php echo $column_product_launch_date; ?></td>
                        <td class="text-left"><?php echo $column_set_desc; ?></td>
                        <td class="text-right"><?php echo $column_sets; ?></td>
                        <td class="text-right"><?php echo $column_piece_in_set; ?></td>
                        <td class="text-right"><?php echo $column_total_pieces; ?></td>
                        <td class="text-right"><?php echo $column_transfer_price; ?></td>
                        <td class="text-right"><?php echo $column_amount; ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cancelled_by_customer['seller_breakup'][$seller] as $product) { ?>
                        <tr id="seller_product_<?php echo $product['order_product_id']; ?>" style="background-color:<?php echo $product['store_sales'] != 'NO' ? '#fff8c3' : 'inherit'; ?>">
                            <td class="text-left"><?php echo $product['sku']; ?>
                            <td class="text-left"><?php echo $product['model']; ?></td>
                            <td><b><?php echo $product['expected_dispatch_date']; ?></b></td>
                            <td class="text-left">
                                <span id="oop_comment_<?php echo $product['order_product_id']; ?>" data-oopid="<?php echo $product['order_product_id']; ?>" data-field="comment">
                                    <?php echo $product['set_description']; ?></span>
                            </td>

                            <td class="text-right"><?php echo $product['quantity']; ?></td>
                            <td class="text-right"><?php echo $product['piece_in_set']; ?></td>
                            <td class="text-right"><?php echo $product['total_pieces']; ?></td>
                            <td class="text-right"><?php echo $product['transfer_price']; ?></td>
                            <td class="text-right"><?php echo $product['amount']; ?></td>
                            <!-- <td class="text-right">
                              
                            </td> -->
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="8" class="text-right"><strong><?php echo $entry_total_seller_amt; ?></strong></td>
                        <td class="text-right"><strong><?php echo $cancelled_by_customer['seller_totals'][$seller]; ?></strong></td>
                    </tr>
                </tbody>
            </table>
            </br>
        <?php } ?>
    </div>
<?php } ?>

<!--//Update Order product Field -->
<script>
        
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('.email_sent').click(function(){
            var seller_id = $(this).attr('data-seller-id');
            $('.email_sent_box').html('');
            $('.email_sent_box').html($('#email_sent_'+seller_id).html());
        });
        
    });
    $( ".email_sent_box" ).delegate( ".email_sent_box_close", "click", function() {
        $('.email_sent_box').html('');
    });
    function resetProduct(obj,id){
        
        let editable = $(obj).parents('tr').find('.editable');
        console.log(editable);
        $(editable).each(function( ind , elem ){
            $(elem).find('input').val($(elem).find('input').data('old'));
            $(elem).find('textarea').val($(elem).find('textarea').data('old'));
            $(elem).find('input,textarea').addClass('hidden');
            $(elem).find('span').removeClass('hidden');
        });
        $(obj).parents('tr').find('textarea').val('');
        $(obj).parents('tr').removeClass('delete_this_row');
        $(obj).parents('tr').find('textarea').addClass('hidden');
        $(obj).parents('tr').find('.edit_type_td select').prop('disabled',true);
        $(obj).parents('tr').find('.edit_type_td select').val('');
        $(obj).parents('tr').find('.edit_type').val('');
        $(obj).parents('tr').find('.edit_type').prop('disabled',true);
        $(obj).parents('tr').find('.delete_btn').removeClass('hidden');
        $(obj).parents('tr').find('.save-reset').addClass('hidden');
        $(obj).parents('tr').find('.save-btn').attr('data-status', '');
        //$(this).parents('tr').find('.edit_type_td select option[value=""]').attr('selected',true);
        //$(obj).addClass('hidden');
        $(obj).parents('tr').removeClass('open');
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        //Update Order product Field
        $("span.oop_field_edit").click(function () {
            var oop_id = $(this).attr("data-oopid");
            var field = $(this).attr("data-field");
            $(this).hide();
            $("span#oop_" + field + "_" + oop_id).hide();
            $("span#oop_" + field + "_input_" + oop_id).show();
        });
        //////////////////////////////////////////////////
        $("span.save_oop_field").click(function () {
            var oop_id = $(this).attr("data-oopid");
            var field = $(this).attr("data-field");
            var new_val = $("input#oop_" + field + "_val_" + oop_id).val().trim();

            $("span#oop_" + field + "_input_" + oop_id).hide();

            $("span#oop_" + field + "_" + oop_id).show();
            $("span#oop_" + field + "_edit_" + oop_id).show();

            $.ajax({
                url: "index.php?route=sale/order/updateOrderProductField&token=<?php echo $token; ?>",
                dataType: "json",
                data: "order_product_id=" + oop_id + "&field=" + field + "&field_val=" + new_val,
                success: function (json) {
                    if (json['error']) {
                        alert(json['error']);
                    } else {
                        $("span#oop_" + field + "_" + oop_id).html(new_val);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });

        });
        /////////////////////////////////////////////////
        $("span.cancel_oop_field").click(function () {
            var oop_id = $(this).attr("data-oopid");
            var field = $(this).attr("data-field");
            $("span#oop_" + field + "_input_" + oop_id).hide();
            var preval = $("span#oop_" + field + "_" + oop_id).html().trim();
            $("input#oop_" + field + "_val_" + oop_id).val(preval);
            $("span#oop_" + field + "_" + oop_id).show();
            $("span#oop_" + field + "_edit_" + oop_id).show();
        });

        //Delete Order Product
        //By Parth 11-03-16
        $("span.oop_delete").on('click', function (argument) {
            var oop_id = $(this).attr("data-oop");
            var check = confirm('Are you sure to delete this product');
            if (check != true) {
                return false;
            }

            $.ajax({
                url: "index.php?route=sale/order/deleteOrderProduct&token=<?php echo $token; ?>",
                dataType: "json",
                data: "order_product_id=" + oop_id + "&order_id=" + <?php echo $order_id; ?>,
                success: function (json) {
                    if (json['error']) {
                        alert(json['error']);
                    } else {
                        // $("tr#seller_product_"+oop_id).remove();
                        location.reload();
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        });


        //Send email to sellers
        $(".send_email").click(function () {

            seller_id = $(this).attr('data-seller_id');
            order_id = $(this).attr('data-order_id');
            suborder_id = $(this).attr('data-suborder_id');

            btn_dom = $(this);
            $.ajax({
                url: 'index.php?route=sale/order/sendOrderEmailToSeller&token=<?php echo $token; ?>&seller_id=' + seller_id + '&order_id=' + order_id + '&suborder_id=' + suborder_id,
                dataType: 'json',
                beforeSend: function () {
                    btn_dom.button('loading');
                },
                complete: function () {
                    btn_dom.button('reset');
                },
                success: function (json) {

                    if (json['error']) {
                        alert('Error:' + json['error']);
                    } else {
                        //alert(json['success']);
                        console.log(json['mail_count'][seller_id]);
                        var mail_count_obj = json['mail_count'][seller_id];
                        var mail_count_keys = Object.keys(mail_count_obj);
                        var mail_count = mail_count_keys.length;
                        var text_mail_count = '';
                        var counters = 1;
                        if(mail_count > 0) {
                            text_mail_count = '<tbody>';
                            $.each(mail_count_obj, function(key, values) {
                               $.each(values, function(index, element) {
                                    text_mail_count += '<tr>';
                                    text_mail_count += '<td>'+counters+'</td>';
                                    text_mail_count += '<td>'+key+'</td>';
                                    text_mail_count += '<td>'+element['date_added']+'</td>';
                                    text_mail_count += '<td>'+element['name']+'</td>';
                                    text_mail_count += '</tr>';
                                    counters++;
                               });
                            });
                            text_mail_count += '</tbody>';
                        }
                        $('.table_seller_mail'+seller_id).html(text_mail_count);
                        $('#email_sent' + seller_id).html('Email Sent ('+ json['mail_log_count'][seller_id] +')');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        });
        //////////////////////////////////////////////////////////////////
        $("#seller_not_given_button").click(function () {
            if(document.getElementById("seller_not_supplied")){
                $('html, body').animate({
                    scrollTop: $("#seller_not_supplied").offset().top
                }, 1000);
            }
        });
        /////////////////////////////////////////////////////////////////
        $("#seller_later_disptach_button").click(function () {
            if(document.getElementById("seller_later_dispatch")){
                $('html, body').animate({
                    scrollTop: $("#seller_later_dispatch").offset().top
                }, 1000);
            }
        });
        /////////////////////////////////////////////////////////////////
        $("#cancelled_by_customer_button").click(function () {
            if(document.getElementById("cancelled_by_customer")){
                $('html, body').animate({
                    scrollTop: $("#cancelled_by_customer").offset().top
                }, 1000);    
            }            
        });
        /*
         * change the status when product at wsbox
         */
        $(".pickup_status").on('click', function () {

            var received_status = $(this).attr("data-status");
            var thisobj = $(this);
            var total_piece_new = 0;
            var total_piece_old = 0;
            var seller_id_no = 0;
            var seller_invoice_id = 0;
            var product_id = 0;
            var issue_box = '';
            if(received_status=='partial_receive') {
                if(thisobj.parents('tr').hasClass('open') == false) {
                    alert("Please edit total pieces. Total pieces should be minimum 1");
                    thisobj.parents('tr').addClass('open');
                    thisobj.parents('tr').find('input').removeClass('hidden');
                    thisobj.parents('tr').find('input').removeClass('hide');
                    thisobj.parents('tr').find('span').addClass('hidden');
                    thisobj.parents('tr').find('span.not-hide').removeClass('hidden');
                    thisobj.parents('tr').find('.save-reset').removeClass('hidden');
                    thisobj.parents('tr').find('.save-btn').attr('data-status', 'partial_receive');
                    return false;
                } else {
                    total_piece_new = $("input[name='total_pieces["+ $(this).data("oopid") +"][quantity][new]']").val();
                    total_piece_old = $("input[name='total_pieces["+ $(this).data("oopid") +"][quantity][old]']").val();
                    
                    
                    if(total_piece_new == total_piece_old) {
                        alert("Please edit total pieces for partial.");
                        return false;
                    }
                }
            } else if (received_status=='Issue') {
                if(thisobj.parents('tr').hasClass('open') == false) {
                    thisobj.parents('tr').addClass('open');
                    thisobj.parents('tr').find('textarea').removeClass('hidden');
                    thisobj.parents('tr').find('textarea').removeClass('hide');
                    thisobj.parents('tr').find('span.total-piece').removeClass('hidden');
                    thisobj.parents('tr').find('span').addClass('hidden');
                    thisobj.parents('tr').find('.save-reset').removeClass('hidden');
                    thisobj.parents('tr').find('.save-btn').attr('data-status', 'Issue');
                    return false;
                } else {
                    thisobj.parents('tr').find('span.total-piece').removeClass('hidden');
                    if($("textarea[name='products["+ $(this).data("oopid") +"][comment]']").val() == '') {
                        alert("Please fill comment");
                        return false;
                    } else {
                        issue_box = $("textarea[name='products["+ $(this).data("oopid") +"][comment]']").val();
                    }
                }
            }

            if (confirm('Are you sure you want to change the product status as ' + received_status + '?')) {
                seller_id_no = $("input[name='seller_id_no["+ $(this).data("oopid") +"]']").val();
                seller_invoice_id = $("input[name='seller_invoice_id["+ $(this).data("oopid") +"]']").val();
                product_id = $("input[name='product_id["+ $(this).data("oopid") +"]']").val();
                
                var order_product_id = $(this).data("oopid");
                var order_id = '<?php echo $order_id; ?>';
                var suborder_id = '<?php echo $suborder_id; ?>';
                
                $.ajax({
                    url: "index.php?route=sale/order/orderProductPickupStatus&token=<?php echo $token; ?>",
                    dataType: "json",
                    data: "order_product_id=" + order_product_id + "&received_status=" + received_status + "&total_piece_new=" + total_piece_new + "&total_piece_old=" + total_piece_old + "&seller_id_no=" + seller_id_no + "&seller_invoice_id=" + seller_invoice_id + "&product_id=" + product_id + "&issue_box=" + issue_box + "&order_id=" + order_id + "&suborder_id=" + suborder_id,
                    success: function (json) {

                        if (json['error']) {
                            alert(json['error']);
                        } else {

                            thisobj.parent().parent().addClass("hide");
                            thisobj.parent().parent().removeClass("without_status");
                            $('input, textarea').addClass("hide");
                            if(received_status=='partial_receive') {
                                thisobj.parents('tr').find('span.total-piece').text(total_piece_new);
                                thisobj.parents('tr').find('span.total-piece').removeClass('hidden');
                            }
                            

                            if (received_status == 'Received') {
                                thisobj.parent().parent().parent().parent().css({"background-color": "#dff7aa"});
                            } else if (received_status == 'Not_Given') {
                                thisobj.parent().parent().parent().parent().css({"background-color": "#f56b6b"});
                            } else {
                                thisobj.parent().parent().parent().parent().css({"background-color": "#f9c4b1"});
                            }
                            if ($(".without_status").length < 1) {
                                location.reload();
                            }

                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            }
        });
    });

</script>