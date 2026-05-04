<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid order_detail_page">
      <div class="pull-left">
        <h1><?php echo $heading_title; ?></h1>
        <ul class="breadcrumb">
          <?php foreach ($breadcrumbs as $breadcrumb) { ?>
          <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
          <?php } ?>
        </ul>
      <?php if(isset($data['rbl_dpd_status']) && $data['rbl_dpd_status'] > 0){ ?>  
         <h1 style="color: red; font-size: 20px;"><i><?php echo sprintf($text_rbl_dpd_count_status, $data['rbl_dpd_status'])?></i></h1>
      <?php } ?>
      </div>
      <div class="pull-right">
        <ul class="actions">
          <?php if (!($data['request_page'] == 'franchise_info_order')) { ?>
          <li>
            <a href="javascript:void(0);" data-order-id = "<?php echo $order_id; ?>" data-suborder-id = "<?php echo $suborder_id; ?>" target="" data-toggle="tooltip" title="<?php echo $button_email_to_buyer; ?>" class="btn btn-primary  email_to_buyer"><i class="fa fa-envelope"></i></a>
          </li>
          <?php } ?>
          <li>
            <a href="<?php echo $detail_invoice; ?>" data-toggle="tooltip" title="<?php echo $button_detail_invoice_print; ?>" class="btn btn-info"><i class="fa fa-print"></i></a>
          </li>
          <li>
            <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
          </li>
        </ul>
      </div>
    </div>
      <div class="container-fluid">
          <?php if ($no_wsb_tape == 1) { ?>
          <div style="background-color: #fcebe8; padding: 5px; margin: 5px; display: inline-block;"><label style="font-size: 16px;"><?php echo $text_no_wsb_tape; ?></label></div>
          <?php } ?>

          <?php if ($no_invoice_with_shipment == 1) { ?>
          <div style="background-color: #fcebe8; padding: 5px; margin: 5px; display: inline-block;"><label style="font-size: 16px;"><?php echo $text_no_offline_invoice; ?></label></div>
          <?php } ?>
      </div>
  <div class="container-fluid">
        <div class="pull-left change_shipping">
            <label class="change_shipping_charge"><?php echo $text_change_shipping_charges;?></label>
            <input type="text" style="width:150px;" id="change-shipping-value" <?php if ($shipping_charge_collected) { ?> value="<?php echo $shipping_charge_collected; ?>" <?php } else { echo 'value=""'; } ?> placeholder = "Change Shipping Charge" readonly="true" ondblclick="this.readOnly='';" <?php echo (!empty($invoice_no) && !$admin ) ? 'disabled="disabled"' : ""; ?>>
            <?php if(!empty($weight)){ ?>
                <span class="pull-right"><h4>Weight: <b><?php echo $weight; ?></b></h4></span>
            <?php } ?>
        </div>
  </div>
  <div class="container-fluid order-info-page">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><b><?php echo $suborder_id; ?></b>
          <!-- Dropshipper Start-->
          <?php if ($data['is_dropshipper'] == 1 || $data['is_dropshipper'] == 2 || $data['is_dropshipper'] == 3) { ?>
          <label class="dropshipper_heading1 drop_head<?php echo $data['is_dropshipper']; ?>">   Dropshipper</label>
          <?php } else { ?>

          <?php } ?>
          <!--Dropshipper END-->
        </h3>
        <div class="important_information">
          <?php echo $order_info_label_important_information; ?>
        </div>
          <?php if( !empty($order_info_label_courier_advisory) ){ ?>
          <div class="courier_advisory">
            <?php echo $order_info_label_courier_advisory; ?>
          </div>
          <?php } ?>

          <?php if( !empty($data['payment_code']) && $data['payment_code'] == 'wsb_credit' ){ ?>
            <div class="courier_advisory" data-toggle="modal" data-target="#wsb_credit_popup" onclick="view_wsb_credit_detail('<?php echo $customer_id; ?>', '<?php echo $data['order_id'];?>');"><label>WSB Credit Dtails</label></div>
          <?php } ?>

          
          <?php if( !empty($suborder_weight) ){ ?>
          <div class="pull-right">

          <?php if(!empty($cod_security_amount) && $cod_security_amount > 0 ) {?>
            <span class="order_weight" style="font-size: 16px; padding-right: 50px;">
              Available COD Security Balance: <?php echo $cod_security_amount_formated?>
            </span>
          <?php } ?>

              <span class="order_weight">Weight: <?php echo !empty($suborder_weight) ? $suborder_weight." Kg." : ""; ?></span>
          </div>
          <?php } ?>
          
          <?php if( $stock_transfer ) { ?>
            <div class="btn btn-primary btn-xs pull-right">
                <i class="fa fa-check-circle"> </i>
                <?php echo $text_stock_transfer; ?>
            </div>
          <?php } ?>

        <?php if(isset($count_order)){ ?>
        <div class="pull-right">
          <b><label class="customer_total_orders">
            <a href="<?php echo $total_order_link;?>" target="_blank">
              Total Orders: <?php echo $count_order; ?>
            </a>
          </label></b>

        </div>
        <?php } ?>
      </div>
      <div class="panel-body">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab-order" data-toggle="tab"><?php echo $tab_order; ?></a></li>
        
        <!--TAB(Products)-->  
        <?php if(in_array('orderInfoTabProducts',$allowed_tabs)) { ?>
          <li><a href="#tab-product" data-toggle="tab" id="tab-product-title"><?php echo $tab_product; ?></a></li>
        <?php } ?>
          
        <!--TAB(Seller Product Break-Up)-->
         <?php if(in_array('orderInfoTabSellerProductBreak_up',$allowed_tabs)) { ?>  
            <li><a href="#tab-seller-product" data-toggle="tab" id="tab-seller-product-title"><?php echo $tab_seller_product; ?></a></li>
        <?php } ?>
        
        <!--TAB(Payment History)-->  
        <?php if(in_array('orderInfoTabPaymentHistory',$allowed_tabs)) { ?>
          <li><a href="#tab-payment_history" data-toggle="tab" id="tab-payment-history-title"><?php echo $tab_payment_history;?></a></li>
        <?php } ?>  

        <!--TAB(History)-->  
        <?php if(in_array('orderInfoTabHistory',$allowed_tabs)) { ?>
          <li><a href="#tab-history" data-toggle="tab" id="tab-history-title"><?php echo $tab_history; ?></a></li>
        <?php } ?>
        
    <?php if (!(isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1')) { ?>
          
        <!--TAB(Courier Partners)-->  
        <?php if(in_array('orderInfoTabCourierPartners',$allowed_tabs)) { ?>
            <li><a href="#tab-courier-partners" data-toggle="tab" id="tab-courier-partners-title"><?php echo $tab_courier_partners; ?></a></li>
        <?php } ?>
        
        <!--TAB(Shipping Labels)--> 
        <?php if(in_array('orderInfoCustomerAddressDetails',$allowed_tabs)) { ?>
            <li><a href="#tab-shipping-labels" data-toggle="tab" id="tab-shipping-labels-title"><?php echo $tab_shipping_label; ?></a></li>
        <?php } ?>    
        
        <!--TAB(Additional Informations)--> 
        <?php if(in_array('orderInfoTabAdditionalInformation',$allowed_tabs)) { ?> 
            <li><a href="#tab-additional_info" data-toggle="tab" id="tab_additnl_info"><?php echo $tab_additional_information;?></a></li>
        <?php } ?>        
        
        <!--TAB(Order Products Statuses)--> 
        <?php if(in_array('orderInfoTabOrderProductStatus',$allowed_tabs)) { ?>
            <li><a href="#tab-op_status" data-toggle="tab" id="tab_op_status">Order Products Statuses</a></li>
        <?php } ?>

        <li><a href="#tab-tab_original_status" data-toggle="tab" id="tab_original_status">Original Order Products</a></li>
    <?php } ?>
          
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab-order">
            <div class="col-sm-4">
              <h4><?php echo $tab_order; ?></h4>
              <table class="table table-bordered">
                <tr>
                  <td><?php echo $text_order_no; ?></td>
                  <td><?php echo $order_no; ?></td>
                </tr>
                <tr>
                  <td>Sub-Order No:</td>
                  <td><?php echo $suborder_id; ?></td>
                </tr>
                <?php if (!($data['request_page'] == 'franchise_info_order')) { ?>
                <tr>
                    <td>
                            <?php
                            echo $text_invoice_no;
                            if (!empty($invoice_no) && ($data['admin'] || $data['is_operations_superadmin']) && ($order_status_id == 1 || $order_status_id == 9 || $order_status_id == 16)) {
                                ?>
                                <span style="cursor:pointer;" data-toggle="tooltip" data-original-title="Cancel Invoice" class="pull-right"><i class="fa fa-ban btn-danger" style="padding: 5px;" id="cancel_buyer_invoice"></i></span>
                                <?php
                            }
                            ?>
                        </td>
                  <td>
                    <?php

                    if (!$order_cancelled) {
                        if (empty($invoice_no)) {
                            if (!empty($error_msg_not_show_button['error'])) {
                                echo $error_msg_not_show_button['error'];
                            } else {
                                if($is_dont_dispatch) {
                                  echo "<span style='color: red'>Don't Dispatch</span><br>";
                                }
                                echo '<button id="button-invoice"
                                              class="btn btn-success btn-xs"
                                              <i class="fa fa-cog"></i>' . $button_generate . '
                                              </button>
                                              <span id="invoice-no"></span>';
                            }
                        } else {
                            echo '<b>' . $invoice_no . '</b>';
                        }
                    } else {
                        if ($invoice_no) {
                            echo '<b>' . $invoice_no . '</b><br>';
                        }
                        echo $text_order_cancelled;
                    }
                    ?>
                  </td>
                </tr>
                <?php } ?>
                <?php if($invoice_date){ ?>
                <tr>
                  <td>Invoice Date: </td>
                  <td><b><?php echo $invoice_date;?></b></td>
                </tr>
                <?php } ?>
                <?php if ($customer) { ?>
                <tr>
                  <td><?php echo $text_customer; ?></td>
                  <td>
                    <a href="<?php echo $customer; ?>" target="_blank"><?php echo $firstname; ?> <?php echo $lastname; ?></a>
                    <div class="btn-group" data-toggle="tooltip" title="<?php echo $button_login; ?>">
                        <span style="margin-left:30px">cid: <strong>[<?php echo $customer_id; ?>]</strong></span>
                      <button type="button" data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs"><i class="fa fa-lock"></i></button>
                      <ul class="dropdown-menu pull-left">
                        <li><a href="index.php?route=sale/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>&store_id=0" target="_blank"><?php echo $text_default; ?></a></li>
                        <?php foreach ($stores as $store) { ?>
                        <li><a href="index.php?route=sale/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>&store_id=<?php echo $store['store_id']; ?>" target="_blank"><?php echo $store['name']; ?></a></li>
                        <?php } ?>
                      </ul>
                    </div>
                  </td>
                </tr>
                <?php } else { ?>
                <tr>
                  <td><?php echo $text_customer; ?></td>
                  <td><?php echo $firstname; ?> <?php echo $lastname; ?></td>
                </tr>
                <?php } ?>
                <tr>
                  <td><?php echo $text_email; ?></td>
                  <td>
                    <!-- <a href="mailto:<?php echo $email; ?>">
                    </a> -->
                      <span class="click_to_see btn-primary btn-xs" data-field-type="email" data-field-value="<?php echo base64_encode($email); ?>" onClick="clickToSee(this)"> Click to see </span>
                      <!-- <?php //echo $email; ?> -->
                  </td>
                </tr>
                <tr>
                  <td><?php echo $text_telephone; ?></td>
                  <td>
                    <span class="click_to_see btn-primary btn-xs" data-field-type="telephone" data-field-value="<?php echo base64_encode($telephone); ?>" onClick="clickToSee(this)"> Click to see </span>
                    <!-- <?php //echo $telephone; ?></td> -->
                </tr>
                <tr>
                  <td><?php echo $text_total; ?></td>
                  <td><?php echo $total; ?></td>
                </tr>
                <tr>
                  <td><?php echo $text_sales_person; ?></td>
                  <td>
                    <table style="margin-top:auto; margin-bottom:auto" class="table table-bordered sales_staff_table taged_sales_staff">

                      <tr>
                       <!--  <td style="cursor:pointer;" id="sales_staff_none" > <?php if(empty($order_taged_sales_staff)){ ?> --None-- <?php } ?> </td> -->
                        <td style="cursor:pointer;" class="sales_staff_add"><i class="fa fa-plus" aria-hidden="true"></i></td>
                      </tr>

                      
                      <?php if(!empty($order_taged_sales_staff)){ 

                          foreach($order_taged_sales_staff as $taged_sales_staff) {

                            $active_status = ($taged_sales_staff['active_status'] == 1) ? 'active' : 'inactive';
                  
                            $tag_sales_staff_name = $taged_sales_staff['role'] . '-' . $taged_sales_staff['name'] . '-' . $active_status;             

                           ?>
                          <tr class="sales_staff" id="sales_staff_<?php echo $taged_sales_staff['sales_staff_id']; ?>" data-id="<?php echo $taged_sales_staff['sales_staff_id']; ?>" >
                            <td> <?php echo $tag_sales_staff_name; ?> </td>
                            <!-- <td style="cursor:pointer;" class="remove_sales_staff" id="remove_sales_staff_<?php echo $taged_sales_staff['sales_staff_id']; ?>" data-id="<?php echo $taged_sales_staff['sales_staff_id']; ?>" > <i class="fa fa-trash" aria-hidden="true"></i> </td> -->
                          </tr>
                      <?php }
                      } ?>

                      <!-- <tr style="display:none;">
                        <td>
                          <span id="sales_staff_input">
                              <select class="col-sm-12 sales_staff_val">
                                <option value = "0"> SELECT SALES STAFF </option>
                                  <?php foreach ($sales_staff_list as $key => $staff) {
                                      if(isset( $staff['status'] ) && $staff['status'] != 0 ){ ?>
                                          <option value = "<?php echo $key; ?>">
                                            <?php echo $staff['name']; ?>
                                          </option>
                                      <?php }
                                    } ?>
                              </select>
                          </span>
                        </td>
                      </tr> -->

                    </table>
                  </td>
                </tr>

                <tr>
                  <td><?php echo $text_self_order;?></td>
                  <td id="order-self">
                    <!-- Rounded switch -->
                    <div class="btn-group self_order" data-toggle="buttons">
                      <label class="btn btn-default btn-on btn-sm <?php echo ($self_order == 1) ? 'active' : '' ?> ">
                        <input type="radio" value="1" name="self_order" <?php echo ($self_order == 1) ? 'checked="checked"' : '' ?> >YES</label>
                      <label class="btn btn-default btn-off btn-sm <?php echo ($self_order == 0) ? 'active' : '' ?>">
                        <input type="radio" value="0" name="self_order" <?php echo ($self_order == 0) ? 'checked="checked"' : '' ?> >NO</label>
                    </div>
                  </td> 
                </tr>

                <?php if ($order_status) { ?>
                <tr>
                  <td><?php echo $text_order_status; ?></td>
                  <td id="order-status">
                    <?php if (!empty($tracking_url) && !empty($tracking_no)) { ?>
                      <a href="<?php echo $tracking_url.$tracking_no; ?>" target="_blank">
                      <?php echo $order_status; ?> </a>
                    <?php } else { ?>
                      <?php echo $order_status; } ?>
                  </td>
                </tr>
                <?php } ?>
                <?php if ($comment) { ?>
                <tr>
                  <td><?php echo $text_comment; ?></td>
                  <td><?php echo $comment; ?></td>
                </tr>
                <?php } ?>
                <tr>
                  <td><?php echo $text_date_added; ?></td>
                  <td><?php echo $date_added; ?></td>
                </tr>
                <tr>
                  <td><?php echo $text_date_modified; ?></td>
                  <td><?php echo $date_modified; ?></td>
                </tr>
              </table>
            </div>
            <div class="col-sm-4">
              <h4><?php echo $tab_payment; ?></h4>
              <?php echo $order_info_tab_payment_details; ?>
            </div>
            <?php if ($shipping_method) { ?>
            <div class="col-sm-4">
              <h4><?php echo $tab_shipping; ?></h4>
              <?php echo $order_info_tab_shipping_details; ?>
            </div>
            <?php } ?>
            <?php if(!empty($customer_comment_array)) { ?>
              <div class="row">
                <div class="col-sm-12">
                  <div class="col-sm-6">
                    <table class="table table-bordered">
                      <caption><b><?php echo $text_customer_comment; ?></b></caption>
                      <tr>
                        <th><?php echo $column_sku; ?></th>
                        <th><?php echo $column_set_description;?></th>
                        <th><?php echo $column_user_comment;?></th>
                      </tr>
                      <?php foreach($customer_comment_array as $sku_key => $customer_comment){ ?>
                      <tr>
                        <td><b><?php echo $customer_comment['model']; ?></b></td>
                        <td class="order_list_comment"><?php echo $customer_comment['set_description']; ?></td>
                        <td class="order_list_comment"><?php echo $customer_comment['customer_comment']; ?></td>
                      </tr>
                      <?php  }  ?>  
                    </table>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
          <div class="tab-pane table-responsive" id="tab-product">
            <i class="fa fa-spinner fa-spin" style="font-size:36px"></i>
          </div>

          <div class="tab-pane table-responsive" id="tab-seller-product">
            <i class="fa fa-spinner fa-spin" style="font-size:36px"></i>
          </div>

          <div class="tab-pane table-responsive" id="tab-payment_history">
            <i class="fa fa-spinner fa-spin" style="font-size:36px"></i>
          </div>

          <div class="tab-pane table-responsive" id="tab-history">
            <i class="fa fa-spinner fa-spin" style="font-size:36px"></i>
          </div>

          <div class="tab-pane" id="tab-courier-partners">
            <i class="fa fa-spinner fa-spin" style="font-size:36px"></i>
          </div>

          <div class="tab-pane" id="tab-shipping-labels">
            <div id="shipping_label">
                <i class="fa fa-spinner fa-spin" style="font-size:36px"></i>
            </div>
          </div>

          <div class="tab-pane table-responsive" id="tab-additional_info">
            <i class="fa fa-spinner fa-spin" style="font-size:36px"></i>
          </div>
   
          <div class="tab-pane table-responsive" id="tab-op_status">
            <i class="fa fa-spinner fa-spin" style="font-size:36px"></i>
          </div>

            <div class="tab-pane table-responsive" id="tab-tab_original_status">
                <div class="alert alert-warning alert-dismissible" role="alert" style="color: #8a6d3b;letter-spacing: 0.5px;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Note: </strong>Highlighted items in yellow color will not deliver to customer. Because they are deleted or edited partially.
                </div><br>
                <table class="table table-bordered">
                <tbody id="original_product_status_div">
                    <tr><td><i class="fa fa-spinner fa-spin" style="font-size:36px"></i></td></tr>
                </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>
    <!-- Edit address detail popup -->
    <?php if( empty($invoice_no) || $admin) { ?>
    <div class="edit_address_detail_popup"></div>
    <?php } ?>

              <!-- tag popup-->
                <!-- Trigger the modal with a button -->
                <button type="button" class="hide tag_modal_popup" data-toggle="modal" data-target="#tag_modal_popup"></button>

                <!-- Modal -->
                <div class="modal fade" id="tag_modal_popup" role="dialog">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Tag Sales Staff</h4>
                      </div>
                      <div class="modal-body">
                          <div id="sales_staff_input">
                             <!--  <div style="cursor:pointer;" id="sales_staff_none" > <?php if(empty($order_taged_sales_staff)){ ?> --None-- <?php } ?> </div> -->
                              <!-- <select class="col-sm-12 sales_staff_val">
                                <option value = "0"> SELECT SALES STAFF </option>
                                  <?php foreach ($sales_staff_list as $key => $staff) {
                                      if(isset( $staff['status'] ) && $staff['status'] != '' ){ ?>
                                          <option value = "<?php echo $key; ?>">
                                            <?php echo $staff['name']; ?>
                                          </option>
                                      <?php }
                                    } ?>
                              </select> -->
                              <div class="input text" style="margin-left:15px;">
                                
                                <label for="select-assign-user">Select Staff (Auto populate - type min 3 character to show list)</label>

                                <input name="select_assign_user" value="" class="ui-autocomplete-input sales_staff_val select-assign-user" autocomplete="off" type="text" style="width: 95%">

                                <input name="select_staff_id" class="select_staff_id" value="0" type="hidden">
                                
                                <input class="sales_staff_button" type="hidden">

                              </div>
                          </div>
                          
                          <div style="margin:45px 0px 7px 23px">
                            <table class="taged_sales_staff_popup">
                                <?php if (!empty($order_taged_sales_staff)) {
                                    foreach($order_taged_sales_staff as $taged_sales_staff){ 
                                      
                                      $active_status = ($taged_sales_staff['active_status'] == 1) ? 'active' : 'inactive';
                  
                                      $tag_sales_staff_name = $taged_sales_staff['role'] . '-' . $taged_sales_staff['name'] . '-' . $taged_sales_staff['telephone'] . '-' . $active_status; 
                            ?>
                                        <tr class="sales_staff" id="sales_staff_<?php echo $taged_sales_staff['sales_staff_id']; ?>" data-id="<?php echo $taged_sales_staff['sales_staff_id']; ?>" >
                                            <td>
                                              <?php echo $tag_sales_staff_name; ?> 
                                            </td>
                                            <td style="cursor:pointer;" class="remove_sales_staff" id="remove_sales_staff_<?php echo $taged_sales_staff['sales_staff_id']; ?>" data-id="<?php echo $taged_sales_staff['sales_staff_id']; ?>" >
                                                <i class="fa fa-trash" aria-hidden="true"></i> 
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                              </table>
                          </div>
                      </div>                     
                      <div class="modal-footer">
                        <button type="button" class="btn btn-primary  tag_submit">Submit</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>             
  </div>
</div>
<!-- WSB Credit Popup -->
<div id="wsb_credit_popup" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">WSB Credit Balance Details</h4>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-hover" >
          <tr>
            <td><b>WSB Credit Status:</b> </td>
            <td><span class="credit_satus"></span></td> 
          </tr>
          <tr>
            <td><b>Approved Credit Limit:</b> </td>
            <td><span class="approved_limit"></span></td> 
          </tr>
          <tr>
            <td><b>Total Used Limit:</b> </td>
            <td><span class="total_used_limit"></span></td>
          </tr>
          <tr>
            <td><b>Used Limit(In Order):</b> </td>
            <td><span class="only_used_limit"></span></td>
          </tr>
          <tr>
            <td><b>Free Available Credit Limit:</b> </td>
            <td><span class="free_limit"></span></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
<?php echo $footer; ?>
<script type="text/javascript">
  function view_wsb_credit_detail(customer_id, order_id){

    $.ajax({
      url: 'index.php?route=sale/order/getWsbCreditdetails&token=<?php echo $token; ?>',
      type:'POST',
      data:{'order_id':order_id, 'customer_id':customer_id},
      success: function(json) {
        json = JSON.parse(json);
        $('#wsb_credit_popup .credit_satus').html(json.credit_status);
        $('#wsb_credit_popup .approved_limit').html(json.credit_limit);
        $('#wsb_credit_popup .total_used_limit').html(json.total_used_credit_bal);
        $('#wsb_credit_popup .only_used_limit').html(json.credit_used_in_order);
        $('#wsb_credit_popup .free_limit').html(json.total_available_bal);
      //  $('#wsb_credit_popup').toggle();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  }
        $('.select-assign-user').autocomplete({
          'source': function (request, response) {
           $('.tag_submit').attr('disabled','disabled');
             if(request.trim().length == 0) {
              $('.tag_submit').removeAttr('disabled');
             }
              if (request.trim().length >= 3) {              
                  $.ajax({
                    url: 'index.php?route=sale/order/getsalesStaff&token=<?php echo $token; ?>&term=' + encodeURIComponent(request),
                    dataType: 'json',
                    success: function (json) {
                      response($.map(json, function (item) {
                        return {
                          label: item['label'],
                          value: item['id']
                        }
                      }));
                    }
                  });
              } else {
                $('.dropdown-menu').hide('');
              }
          },
          'select': function (item) {
            $('.select_staff_id').val(item['value']);
            $('.select-assign-user').val(item['label']);
            $('.tag_submit').removeAttr('disabled');
            $('.sales_staff_button').click();
          },
          response: function (item) {
            if (item['value'] == '') {
                $('.select_staff_id').val(0);
                $('.select-assign-user').val('');
            }                
             
          }

        })
  </script>
  <script type="text/javascript">
    $(document).ready(function() {

      $('#cancel_buyer_invoice').click(function() {
          if(confirm("Are you sure? Do you want to cancel this buyer invoice ?")) {
              var order_id = '<?php echo $order_id ?>';
              var suborder_id = '<?php echo $suborder_id ?>';
              $.ajax({
                url : "index.php?route=sale/order/cancelSuborderBuyerInvoice&token=<?php echo $token; ?>",
                type: "post",
                data: {yes:"yes", order_id:order_id, suborder_id:suborder_id},
                success: function(json) {              
                     window.location.reload();
                }
            });
            window.location.reload();
          } else {
            return false;
          }
          
      });
    });
  </script>
  <script type="text/javascript">
     $(document).delegate('#button-invoice', 'click', function() {
      $.ajax({
        url: 'index.php?route=sale/order/createinvoiceno&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&shipping_method=<?php echo $shipping_method; ?>',
        dataType: 'json',
        beforeSend: function() {
          $('#button-invoice').button('loading');
        },
        complete: function() {
          $('#button-invoice').button('reset');
        },
        success: function(json) {
          $('.alert').remove();

          if (json['error']) {
            $('#tab-order').prepend('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
          }

          if (json['invoice_no']) {
            $('#button-invoice').hide();
            $('#invoice-no').text(json['invoice_no']);
            $('#invoice-no').show();
            $('#button-cancel').show();
            $('#history_invoice_no_check').attr('data-value','1');
            window.location.reload();
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    });

    //-->

  $(document).ready(function(){
    /** Open sales staff popup on click **/
    $("td.sales_staff_add").click(function() {
        $(".select-assign-user").val('');
        $(".select_staff_id").val(0);
        $(".tag_modal_popup").click();
    });

    /** Validate sales staff when add sales staff **/
    $(document).on('click','.sales_staff_button',function() {
      
       /*var sales_staff_id = $(this).val();
       var sales_staff_name = $(".sales_staff_val :selected").text().trim();      
       var is_staff_exist = document.getElementById("sales_staff_"+sales_staff_id);*/

      var sales_staff_id = $(".select_staff_id").val().trim();
      var sales_staff_name = $(".select-assign-user").val().trim();      
      var is_staff_exist = document.getElementById("sales_staff_"+sales_staff_id);

      if (is_staff_exist != null) {
        alert('Sales Staff Already Selected');
        return false;
      } else if(sales_staff_id == 0 || sales_staff_id == '') {
        alert('Select Valid Sales Staff');
        return false;
      }

      if(confirm("Are you sure? Do you want to select "+sales_staff_name+" as staff  ?")) {
         /** Add sales staff in sales staff list **/
        var html = '';
        html += '<tr class="sales_staff_current" id="sales_staff_'+sales_staff_id+'" data-id="'+sales_staff_id+'" >';     
        html += '<td>'+sales_staff_name+'</td>';
        html2 = '<td style="cursor:pointer;" class="remove_sales_staff" id="remove_sales_staff_'+sales_staff_id+'" data-id="'+sales_staff_id+'"><i class="fa fa-trash" aria-hidden="true"></i></td>';       
        
        sales_staff_name_split = sales_staff_name.split('-');
        sales_staff_name_without_mobile='';
        sales_staff_name_without_mobile = sales_staff_name_split[0] + '-' + sales_staff_name_split[1] +'-' + sales_staff_name_split[3];
        
        sales_staff_without_mobile = '';
        sales_staff_without_mobile += '<tr class="sales_staff_current" id="sales_staff_'+sales_staff_id+'" data-id="'+sales_staff_id+'" >';     
        sales_staff_without_mobile += '<td>'+sales_staff_name_without_mobile+'</td>';
        
        $(".taged_sales_staff_popup").append(html+html2);
        $(".taged_sales_staff").append(sales_staff_without_mobile);
        
      } else {
          $('.select_staff_id').val(0);
          $('.select-assign-user').val('');
          $('.tag_submit').removeAttr('disabled');
          return false;
      }      
    });

    /** Click on sales staff tag submit button **/
    $(document).on('click', '.tag_submit', function() { 
        var sales_staff_ids_previous = [];
        var sales_staff_id = [];

        /** Get all sales staff ids [ Already Tagged ] **/
        $('.taged_sales_staff_popup .sales_staff').each(function () {
            sales_staff_ids_previous.push($(this).attr('data-id'));
        });

        /** Get all sales staff ids [ Currently Tagged ] **/
        $('.taged_sales_staff_popup .sales_staff_current').each(function () {     
            sales_staff_id.push($(this).attr('data-id'));
        });

        /** Check empty, When click on submit **/
        if (sales_staff_id == '' && sales_staff_id < 1) {
          if (sales_staff_ids_previous == '') {
            alert('Select Valid Sales Staff');
            return false;
          }
        } 

        $('.tag_submit').attr('disabled','disabled');
      

       /** Call function save_sales_staff **/
       save_sales_staff('add', sales_staff_id, sales_staff_name='', sales_staff_ids_previous);
    })

    /** Click on Remove sales staff **/
    $(document).on('click', '.remove_sales_staff', function() {

      var retVal = confirm("Are you sure? Do you want to delete this sales staff ?");
       if( retVal == true ) {          
       }
       else{
          return false;
       }
      $(".sales_staff_val").val('');
      $(".select_staff_id").val(0);
      if ($(this).closest('tr').hasClass( "sales_staff_current")) {
        var id = $(this).closest('tr').attr('id');
        $("tr#"+id).remove();
        return false;
      }      

      var sales_staff_id = $(this).attr('data-id');
      var sales_staff_ids_previous = [];

      /** Get all sales staff ids [ Already Tagged ] **/
      $('.taged_sales_staff_popup .sales_staff').each(function () {
          sales_staff_ids_previous.push($(this).attr('data-id'));
      });    
      
      save_sales_staff('remove', sales_staff_id, sales_staff_name = '', sales_staff_ids_previous);
    });

    /**
    * save_sales_staff
    * Update Sales staff [ Multiple or single ], Responsible for the order
    * @param : type, sales_staff_id, sales_staff_name
    * @param : sales_staff_ids_previous( array )  
    */
    function save_sales_staff(type, sales_staff_id='', sales_staff_name='', sales_staff_ids_previous=[]) { 

        $.ajax({
            url : "index.php?route=sale/order/updateSalesStaff&token=<?php echo $token; ?>",
            type: "post",
            dataType: "json",
            data: "order_id=" + <?php echo $order_id; ?> + "&order_no=" + <?php echo $order_no; ?> + "&total=" + "<?php echo $without_crncy_frmt_total; ?>" + "&customer_id=" + <?php echo $customer_id; ?> + "&sales_staff=" + sales_staff_id+ "&type=" + type + "&sales_staff_ids_previous=" + sales_staff_ids_previous,
            success: function(json) {              
                if (json['error']) {
                  $("#sales_staff_"+json['id']).remove();
                  alert(json['error']);
                } else if(json['force_tag']) {                  
                 save_sales_staff('force_tag', sales_staff_id, sales_staff_name, sales_staff_ids_previous);
                } else if (type == 'remove') {                  
                  $("tr#sales_staff_"+sales_staff_id).remove();
                  $("tr#sales_staff_"+sales_staff_id).remove();
                } else if (json['success']) {
                  $(".taged_sales_staff_popup .sales_staff_current").removeClass('sales_staff_current').addClass('sales_staff'); 
                  alert(json['success']);     
                  $('#tag_modal_popup').modal('toggle');
                }
                $('.tag_submit').removeAttr('disabled'); 
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }    

    // Update change shipping charge value
    $('input#change-shipping-value').on("keypress", null, function(e) {
      if (e.keyCode == 13) {

        var shipping_value = $("input#change-shipping-value").val();

        if(!isNaN(shipping_value)) { // checking that shpping charge value is numeric only
          $.ajax({
            url: 'index.php?route=sale/order/changeShippingCharge&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&shipping_charge='+shipping_value,
            dataType: 'json',
            complete: function() {
              $('#button-invoice').button('reset');
            },
            success: function(json) {
              if (json['error']) {
                alert(json['error']);
              }

              if (json['payable']) {
                //alert('Rs.'+credit+' credit applied. Final payable amount is now Rs.'+json['payable']);
                $('input#change-shipping-value').prop('readonly',true);
                window.location.reload();
              }
            },
            error: function(xhr, ajaxOptions, thrownError) {
              alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
          });

        } else {
          alert("Invalid value. Please enter a valid number for shipping charges collected");
        }
      }
    });
  });
  </script>
<script>
  $(document).ready(function(){
    // resend Mail To Buyer
    $('.email_to_buyer').click(function(e){
      e.preventDefault();
      order_id = $(this).attr('data-order-id');
      suborder_id = $(this).attr('data-suborder-id');
      $.ajax({
        type:'post',
        url:'index.php?route=sale/order/resendMailToBuyer&token=<?php echo $token; ?>',
        data:{'order_id':order_id, 'suborder_id':suborder_id},
        beforeSend: function() {
          $('.email_to_buyer').button('loading');
        },
        complete: function() {
          $('.email_to_buyer').button('reset');
        },
        success: function(data) {
          alert(data);
        }
      });
    });

 });
</script>

  <script type="text/javascript">
    $(document).ready(function(){
      $(".datepicker").datetimepicker( {pickTime: false, format: 'YY-MM-DD'});    
    });

    $(".self_order :input").change(function(e) {       
        e.preventDefault();

        order_id = '<?php echo $order_id; ?>';
        customer_id = '<?php echo $customer_id; ?>';
        
        //console.log(order_id); 
        //console.log(customer_id);

        $.ajax({
            type:'post',
            url:'index.php?route=sale/order/updateSelfOrder&token=<?php echo $token; ?>',
            data:{'order_id':order_id, 'customer_id':customer_id, 'self_order': $(this).val()},
            complete: function() {          
            },
            success: function(data) {
              alert(data);
            }
          });
      });

  </script>
  <style type="text/css">
  .btn-default.btn-on.active{background-color: #5BB75B;color: white;}
  .btn-default.btn-off.active{background-color: #DA4F49;color: white;}
  </style>

  <script>
  $(document).ready(function(){
      var is_products_detail_loaded = false;
      var is_seller_products_breakup_loaded = false;
      var is_payment_history_loaded = false;
      var is_history_loaded = false;
      var is_courier_partners_loaded = false;
      var is_additional_info_loaded = false;
      var is_shipping_labels_loaded = false;
      var is_op_status_tab_loaded = false;
      var is_original_status_tab_loaded = false;

      $('#tab-product-title').click(function(){
          if(!is_products_detail_loaded) {
              is_products_detail_loaded = true;
            $.ajax({
                url: 'index.php?route=sale/order/orderInfoTabProducts&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>',
                complete: function() {
                
                },
                success: function(response) {
                    $('#tab-product').html(response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
          }
      });

      $('#tab-seller-product-title').click(function(){
          if(!is_seller_products_breakup_loaded) {
              is_seller_products_breakup_loaded = true;
            $.ajax({
                url: 'index.php?route=sale/order/orderInfoTabSellerProductBreak_up&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&order_no=<?php echo $order_no; ?>',
                complete: function() {
                
                },
                success: function(response) {
                    $('#tab-seller-product').html(response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
          }
      });

      $('#tab-history-title').click(function(){
          if(!is_history_loaded) {
              is_history_loaded = true;
            $.ajax({
                url: 'index.php?route=sale/order/orderInfoTabHistory&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&order_no=<?php echo $order_no; ?>',
                complete: function() {
                
                },
                success: function(response) {
                    $('#tab-history').html(response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
          }
      });

      $('#tab-payment-history-title').click(function(){
          if(!is_payment_history_loaded) {
              is_payment_history_loaded = true;
            $.ajax({
                url: 'index.php?route=sale/order/orderInfoTabPaymentHistory&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&order_no=<?php echo $order_no; ?>',
                complete: function() {
                
                },
                success: function(response) {
                    $('#tab-payment_history').html(response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
          }
      });

      $('#tab-courier-partners-title').click(function(){
          if(!is_courier_partners_loaded) {
              is_courier_partners_loaded = true;
            $.ajax({
                url: 'index.php?route=sale/order/orderInfoTabCourierPartners&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&order_no=<?php echo $order_no; ?>',
                complete: function() {
                
                },
                success: function(response) {
                    $('#tab-courier-partners').html(response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
          }
      });

      $('#tab_additnl_info').click(function(){
          if(!is_additional_info_loaded) {
              is_additional_info_loaded = true;
            $.ajax({
                url: 'index.php?route=sale/order/orderInfoTabAdditionalInformation&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&order_no=<?php echo $order_no; ?>',
                complete: function() {
                
                },
                success: function(response) {
                    $('#tab-additional_info').html(response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
          }
      });

      $('#tab-shipping-labels-title').click(function(){
          if(!is_shipping_labels_loaded) {
              is_shipping_labels_loaded = true;
              $.ajax({
                url: 'index.php?route=sale/order/shippingLabel&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>',
                complete: function() {
                
                },
                success: function(response) {
                    $('#shipping_label').html(response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
              });
          }
      });

      $('#tab_op_status').click(function(){
          if(!is_op_status_tab_loaded) {
              is_op_status_tab_loaded = true;
              $.ajax({
                url: 'index.php?route=sale/order/orderInfoTabOrderProductStatus&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>',
                complete: function() {
                
                },
                success: function(response) {
                    $('#tab-op_status').html(response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
              });
          }
      });

      $('#tab_original_status').click(function(){
          if(!is_original_status_tab_loaded) {
              is_original_status_tab_loaded = true;
              $.ajax({
                url: 'index.php?route=sale/order/orderInfoTabOriginalProductStatusInfo&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>',
                complete: function() {
                
                },
                success: function(response) {
                    $('#original_product_status_div').html(response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
              });
          }
      });
  });
  </script>