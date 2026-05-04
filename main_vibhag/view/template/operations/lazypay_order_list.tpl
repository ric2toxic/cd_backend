<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
    <div class="modal fade" id="lazypay_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-lg" style="top:5%;width:90%;">
        <div class="modal-content" >
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Lazypay Transaction logs </h4>
          </div>
          <div class="modal-body">
               <iframe src="about:blank" frameborder="0" id="lazypay_logs" width="100%" height="400px;"></iframe> 
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Confirm action(remove,move to wishlist,clear cart etc) model -->
    <div id="confirm_popup" class="modal fade" role="dialog">
        <div class="modal-dialog" style="top:20%;width:350px;z-index: 1050;">
            <div class="modal-content alert alert-info">
                <div class="modal-body" style="min-height: 50px;font-size: 18px;">
                  <form action="<?php echo $lazypay_action_url; ?>" method="post">
                    <div style="display:block;">
                      <label>Please verify below details</label>                      
                    </div>
                    <hr />
                    <div id="confirm_body" style="display:block;">
                    </div>
                    <hr />
                    <div style="display:block;">
                        <button type="button" data-dismiss="modal" class="btn deliver_btn" style="margin-left: 10px;">Cancel</button>
                        <button type="submit" class="btn btn-info" id="submit_to_lazypay" style="margin-left: 10px;">Submit</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>

    <div class="page-header">
        <div class="container-fluid">
            <h1>
                <?php echo $heading_title; ?>
            </h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li>
                    <a href="<?php echo $breadcrumb['href']; ?>">
                        <?php echo $breadcrumb['text']; ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
            <div class="pull-right">
              <span class="small">O  = Orders</span> |
              <span class="small">R  = Returns</span> |
              <span class="small">F  = COD Failed</span> |
              <span class="small">DI = Delivery Issues</span> |
              <span class="small">C  = Cancelled Orders</span> | 
              <span class="small">LO = Last Order Date</span> |
              <span class="small">PO = Previous Order Date</span> |
              <span class="small">OT = Order Total</span> | <br>
              <span class="small">IA = Invoice Amount</span> | 
              <span class="small">CN = Credit Note</span> | 
              <span class="small">P Rcv = Payment Received</span> | 
              <span class="small">P Ref = Payment Refunded</span> | 
              <span class="small">CD  = Cash Discount</span> |  
              <span class="small">Bal = Balance Amount</span> 
              <span class="small">&nbsp;</span>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
            <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i>
            <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
      </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i>
                    <?php echo $text_list; ?>
                </h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-12">
                  <div class="col-sm-6">
                    <div style="height: 35px;">
                      <span class="order_weight"><a href="javascript:void(0)" id="advance_filter">+Filters</a></span>
                    </div>
                  </div>
                </div>
              </div>  
                
                <div class="well" style="display: none;">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-order-no"><?php echo $entry_order_no; ?></label>
                                <input type="text" name="filter_order_no" value="<?php echo $filter_order_no; ?>" placeholder="<?php echo $entry_order_no; ?>" id="input-order-no" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-customer"><?php echo $entry_customer_email; ?></label>
                                <input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" placeholder="<?php echo $entry_customer_email; ?>" id="input-customer" class="form-control" />
                            </div>
                            
                            
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                                <select name="filter_order_status" id="input-order-status" class="form-control">
                                  <option value="*"></option>
                                  <?php if ($filter_order_status == '0') { ?>
                                  <option value="0" selected="selected"><?php echo $text_missing; ?></option>
                                  <?php } else { ?>
                                  <option value="0"><?php echo $text_missing; ?></option>
                                  <?php } ?>
                                  <?php foreach ($order_statuses as $order_status) { ?>
                                  <?php if ($order_status['order_status_id'] == $filter_order_status) { ?>
                                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                  <?php } else { ?>
                                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                  <?php } ?>
                                  <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <td class="text-right">
                                    <?php if ($sort == 'o.order_no') { ?>
                                    <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>">
                                        <?php echo $column_order_no; ?>
                                    </a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_order; ?>">
                                        <?php echo $column_order_no; ?>
                                    </a>
                                    <?php } ?>
                                </td>
                                <td class="text-left">
                                    <?php if ($sort == 'customer') { ?>
                                    <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>">
                                        <?php echo $column_customer; ?>
                                    </a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_customer; ?>">
                                        <?php echo $column_customer; ?>
                                    </a>
                                    <?php } ?>
                                </td>
                                <td class="text-left">
                                    <?php echo $column_company; ?>
                                    <hr class="btn-success">
                                    <?php echo $column_city; ?>
                                </td>
                                <td class="text-left"> Stats </td>
                                <td class="text-left">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td>
                                              Suborder No
                                              <hr class="btn-success">
                                              Total
                                            </td>
                                            <td class="text-center">Status</td>
                                            <td class="text-center">
                                              Shipping
                                              <hr class="btn-success">
                                              Inv #
                                            </td>
                                            <td style="width: 10px;"></td>
                                        </tr>
                                    </table>

                                </td>
                                <td class="text-center">
                                    <?php echo $column_total_bill; ?>
                                </td>
                                <td class="text-left">
                                    <?php if ($sort == 'o.date_added') { ?>
                                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>">
                                        Order Date
                                    </a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_date_added; ?>">
                                        Order Date
                                    </a>
                                    <?php } ?>
                                    <hr class="btn-success">
                                    Processing Date
                                </td>
                                <?php if (!(isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1')) { ?>
                                <td style="width: 10px;"> Action</td>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders) { 

                                foreach ($orders as $order) { 
                                    
                                    $lazypay_freezed_amount = $order['order']['lazypay_freezed_amount'];
                                    $lazypay_release_show_button = $order['order']['lazypay_release_show_button'];

                                    $order_info = $order['order'];
                                    $suborder_info = $order['suborder'];
                                ?>
                            <tr class="<?php echo ($order_info['credit_color']) ? 'credit_color' : (($order_info['franchise_color']) ? 'franchise_color' : '' ); ?>">
                                <td class="text-left" width="10%">
                                    <?php if(!strcasecmp( $order_info['operations_status'], "good_to_process" )) { ?>
                                        <div class="alert alert-success">
                                            <i class="fa fa-check-circle"> </i>
                                            <?php echo $text_good_process; ?>
                                        </div>
                                    <?php } elseif(!strcasecmp( $order_info['operations_status'], "good_to_dispatch" )) { ?>
                                        <div class="alert alert-info">
                                            <i class="fa fa-check-circle"> </i>
                                            <?php echo $text_good_dispatch; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if( $order_info['stock_transfer'] ) { ?>
                                        <div class="btn btn-primary btn-xs">
                                            <i class="fa fa-check-circle"> </i>
                                            <?php echo $text_stock_transfer; ?>
                                        </div>
                                    <?php } ?>
                                    <strong>
                                        <h6 class="order_unassigned_sales">
                                            <?php echo $order_info['sales_staff_name']; ?>
                                        </h6>
                                        
                                        <?php echo $order_info['order_no'];?>
                                          
                                    </strong> <br>
                                    <?php echo $order_info['store_name']; ?>
                                    <br/>
                                    <?php if( !strcasecmp( $order_info['operations_status'], "dont_dispatch" )  ){ ?>
                                    <span class="dont_disptach"><?php echo $text_dont_dispatch;?></span>
                                    <?php } ?>
                                    <span class="label label-success"><?php echo $order_info['order_from']; ?></span>
                                    
                                    <?php if($order_info['customer_self_order'] == 1 ) { ?>
                                      <div style = "padding-top: 10px;"><span class="label label-success">SELF ORDER</span></div>
                                    <?php } else if($order_info['self_order'] == 1 && $this->user->getGroupId() == 1) { ?>
                                      <div style = "padding-top: 10px;"><span class="label label-success">SELF ORDER</span></div>
                                    <?php } ?>
                                  </td>
                                  <td class="text-left">
                                  <div style="display: block;">
                                      <a href="<?php echo $order_info['customer_link']; ?>" target="_blank"><?php echo $order_info['customer'];?></a>
                                  </div>
                                    
                                    <div style="display: block;">
                                        <div class="btn-group" style="float: left; display: block;" data-toggle="tooltip" title="<?php echo $button_login; ?>">
                                            <button type="button" data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs"><i class="fa fa-lock"></i></button>
                                            <ul class="dropdown-menu pull-left">
                                                <li>
                                                    <a href="index.php?route=sale/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $order_info['customer_id']; ?>&store_id=0" target="_blank">
                                                        <?php echo $text_default; ?>
                                                    </a>
                                                </li>
                                                <?php foreach ($stores as $store) { ?>
                                                <li>
                                                    <a href="index.php?route=sale/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $order_info['customer_id']; ?>&store_id=<?php echo $store['store_id']; ?>" target="_blank">
                                                        <?php echo $store['name']; ?>
                                                    </a>
                                                </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                       
                                    <div style="display: block; width: 100%; clear: left;">
                                        cid: <strong>[<?php echo $order_info['customer_id']; ?>]</strong>
                                    </div>
                                    <span style="color: #000;">
                                        <span class="click_to_see btn-primary btn-xs" data-field-type="telephone" data-field-value="<?php echo base64_encode($order_info['telephone']); ?>" onClick="clickToSee(this)"> Click to see </span>
                                        <!-- <?php //echo $order_info['telephone'];?> -->
                                    </span>
                                    <br/>
                                    <?php if ($order_info['app_installed']) { ?>
                                    <label> <span class="app_installed">App Installed</span> <br>
                                      <?php } ?>
                                    <?php if(!empty($order_info['website_link'])){ ?>
                                      <br />
                                      <a href="<?php echo $order_info['website_link']; ?>" target="_blank"><b>Fashcart</b></a>
                                    <?php } ?>
                                  </td>
                                  <td class="text-left">
                                  <div class="order_list_comment"><strong><?php echo $order_info['shipping_company']; ?></strong> <br></div>
                                    <div class="order_list_comment">
                                        <?php echo $order_info['comment']; ?>
                                    </div>
                                    <hr class="btn-success">
                                    <strong><?php echo $order_info['shipping_city']; ?></strong>
                               </td>
                                <td class="text-left btn-xs">
                                  <label>
                                    <a href="<?php echo $order_info['total_order_link']; ?>" target="_blank">
                                      O-<?php echo $order_info['ltvTotalOrders']['total_orders']; 
                                                echo " (". $order_info['ltvTotalOrders']['total_orders_amt'] .")" ;?>  
                                    </a>
                                  </label> <br />
                                  <label class="<?php echo ($order_info['ltvTotalReturn']['total_orders']) ? 'text-danger' : ''; ?>" > 
                                    R-<?php echo $order_info['ltvTotalReturn']['total_orders']; 
                                              echo " (". $order_info['ltvTotalReturn']['totalOrderAmtPer'] .")" ;?>
                                  </label> <br />
                                  <label class="<?php echo ($order_info['ltvCodFailedOrders']['total_orders']) ? 'text-danger' : ''; ?>" > 
                                    F-<?php echo $order_info['ltvCodFailedOrders']['total_orders']; 
                                              echo " (". $order_info['ltvCodFailedOrders']['totalOrderAmtPer'] .")" ;?>
                                  </label> <br />

                                  <label class="<?php echo ($order_info['ltvDeliveryIssueOrders']['total_orders']) ? 'text-danger' : ''; ?>" > 
                                    DI-<?php echo $order_info['ltvDeliveryIssueOrders']['total_orders']; 
                                               echo " (". $order_info['ltvDeliveryIssueOrders']['totalOrderAmtPer'] . ")"; ?>
                                  </label> <br /> 
                                  <label class="<?php echo ($order_info['ltvCancelledOrders']['total_orders']) ? 'text-danger' : ''; ?>" > 
                                    C-<?php echo $order_info['ltvCancelledOrders']['total_orders']; 
                                               echo " (". $order_info['ltvCancelledOrders']['totalOrderAmtPer'] . ")"; ?>
                                  </label> <br />
                                  <label class="order_list_comment" > 
                                    LO-[<?php
                                              if(!empty($order_info['ltvTotalOrders']['last_order_date'])){
                                                  echo date('d-M-y', strtotime($order_info['ltvTotalOrders']['last_order_date'])); 
                                              } 
                                        ?> ]
                                  </label><br> 
                                  <span style="color: #000;">
                                    <span 
                                      class="btn-primary btn-xs" 
                                      id="click_to_see_<?php echo $order_info['order_id']?>" 
                                      onClick="getPreviousOrderDate(<?php echo $order_info['order_id']?>,<?php echo $order_info['master_id']?>, '<?php echo $order_info['date_added']?>')">View PO Date</span>
                                  </span>
                                  <label 
                                    class="order_list_comment" 
                                    id="previous_order_date_<?php echo $order_info['order_id']?>"
                                    style="display: none"
                                  >
                                    PO-[NA] 
                                  </label> 
                                </td>
                                <td class="text-left suborder_table">
                                    <table class="table table-bordered">
                                      <?php if( !empty( $suborder_info ) ) { ?>
                                          <?php foreach($suborder_info as $key_suborder_id => $suborder_data){ ?>
                                            <tr class="<?php echo ($order_info['credit_color']) ? 'credit_color' : ''; ?>" <?php if ($suborder_data['red_flag']) {
                                                    echo('style="background-color:yellow; color:red;"');
                                                  } elseif ($suborder_data['dispute_flag']) {
                                                    echo('style="background-color:red; color:white;"');
                                                  } ?>>
                                              <td class="text-center">
                                                <?php echo $key_suborder_id; ?>
                                                <hr class="btn-success">
                                                <?php echo $suborder_data['total']; ?>
                                              </td>
                                              <td>
                                                <strong>
                                                <?php 
                                                if (!empty($suborder_data['tracking_url']) && !empty($suborder_data['tracking_no'])) { ?>
                                                  <a href="<?php echo $suborder_data['tracking_url'].$suborder_data['tracking_no']; ?>" target="_blank">
                                                  <?php echo $suborder_data['status']; ?> </a>
                                                <?php } else {
                                                  echo $suborder_data['status'] ; 
                                                } ?>
                                                <?php if ($suborder_data['order_status_id'] == 15 ){ ?>
                                                <i class="fa fa-exclamation-circle text-success" data-toggle="tooltip" title="Courier Partner : <?php echo $suborder_data['courier_partner'];?><br/>Tracking No.: <?php echo $suborder_data['tracking_no'];?>">
                                                </i>
                                                <?php } ?>
                                                </strong><br>
                                                <div>
                                                  <?php echo $suborder_data['delivery_date'] ; ?>
                                                </div><br>
                                                <div class="order_list_comment">
                                                    <?php
                                                        if ( !empty($suborder_data['order_history']) &&
                                                             is_array($suborder_data['order_history']) ) {
                                                            $last_history_comment = end($suborder_data['order_history']);
                                                            echo html_entity_decode($last_history_comment['comment']);
                                                        }
                                                     ?>
                                                </div>
                                              </td>
                                            </tr>
                                            <?php } ?>
                                            <?php 
                                              $bal_amt = -(float)$order_info['total_amount'] 
                                                         + (float)$order_info['cn_amount'] 
                                                         - (float)$order_info['cn_penality'] 
                                                         + (float)$order_info['cashback_coupon_amount'] 
                                                         + (float)$order_info['payment_history']['payed_by_customer']
                                                         + (float)$order_info['payment_history']['actual_refund_amt']
                                                         - (float)$order_info['less_cash_discount'];
                                                         + (float)$order_info['other_charges'];
                                              $bal_amt = round($bal_amt, 2);

                                              if($bal_amt > 0 ) {
                                            ?>
                                                <tr>
                                                  <td colspan="2" align="center">
                                                    <div style="display:block; padding: 5px; ">
                                                            <button 
                                                              type="button"
                                                              data-toggle="tooltip"
                                                              title="To Lazypay"
                                                              class="btn btn-sm btn-info button-lazypay-action"
                                                              data-action="refund" 
                                                              data-order-no="<?php echo $order_info['order_no'] ?>" 
                                                              data-order-id="<?php echo $order_info['order_id'] ?>" 
                                                              data-credit-note-id=""
                                                              data-detail="" 
                                                              data-customer-id="<?php echo $order_info['customer_id'] ?>" 
                                                              data-total="<?php echo $bal_amt;?>" 
                                                              data-text="" 
                                                            >
                                                            Refund Over Lazypay
                                                          </button>
                                                    </div> 
                                                 </td>
                                                </tr>
                                                <?php } ?>

                                                <?php if($bal_amt < 0 ) { ?>
                                                <tr>
                                                  <td colspan="2" align="center">
                                                    <div style="display:block; padding: 5px; ">
                                                            <button 
                                                              type="button"
                                                              data-toggle="tooltip"
                                                              title="To Lazypay"
                                                              class="btn btn-sm btn-info button-lazypay-action"
                                                              data-action="capture" 
                                                              data-order-no="<?php echo $order_info['order_no'] ?>" 
                                                              data-order-id="<?php echo $order_info['order_id'] ?>" 
                                                              data-credit-note-id=""
                                                              data-detail="" 
                                                              data-customer-id="<?php echo $order_info['customer_id'] ?>" 
                                                              data-total="<?php echo abs( $bal_amt ) ;?>" 
                                                              data-text="" 
                                                            >
                                                            Capture Payment Over Lazypay
                                                          </button>
                                                    </div> 
                                                 </td>
                                                </tr>
                                                <?php } ?>

                                              <?php if( $lazypay_release_show_button ) { ?>

                                                <tr>
                                                  <td colspan="2" align="center">
                                                    <div style="display:block; padding: 5px; ">
                                                            <button 
                                                              type="button"
                                                              data-toggle="tooltip"
                                                              title="To Lazypay"
                                                              class="btn btn-sm btn-info button-lazypay-action"
                                                              data-action="release" 
                                                              data-order-no="<?php echo $order_info['order_no'] ?>" 
                                                              data-order-id="<?php echo $order_info['order_id'] ?>" 
                                                              data-credit-note-id=""
                                                              data-detail="" 
                                                              data-customer-id="<?php echo $order_info['customer_id'] ?>" 
                                                              data-total="<?php echo $lazypay_freezed_amount ;?>" 
                                                              data-text="" 
                                                            >
                                                            Release Freezed Order Amount Over Lazypay
                                                          </button>
                                                    </div> 
                                                 </td>
                                                </tr>

                                              <?php } ?>  

                                        <?php } ?>
                                  </table>
                                  </td>

                                <td class="text-right item">    
                                  <h6>
                                  <span>
                                    <button class="btn btn-sm btn-info" data-toggle="tooltip" data-original-title="View Details"
                                      onclick="view_order_detail('demo_<?php echo $order_info['order_id'];?>');"
                                      ><i class="fa fa-plus"></i></button>
                                    <a href="javascript:void(0);"
                                       data-toggle="tooltip"
                                       title="Refresh Order Total"
                                       class="btn btn-sm btn-primary refresh_total"
                                       data-order_id = "<?php echo $order_info['order_id']; ?>" >
                                       <i class="fa fa-refresh"></i>
                                    </a>
                                  </span>
                                  </h6>
                                  <div id="demo_<?php echo $order_info['order_id'];?>" style="display:none">
                                    <table class="text-info order-details" >
                                      <tr>
                                        <td><b>IA:</b> 
                                        <?php echo $order_info['total_amount']; ?>
                                        </td> 
                                        <td><b>CN:</b> 
                                        <?php echo $order_info['cn_amount']; ?>
                                        </td> 
                                      </tr>
                                      <tr>
                                        <td><b>P Rcv:</b> <?php echo $order_info['payment_history']['payed_by_customer']; ?></td>
                                        <td><b>P Ref:</b> <?php echo $order_info['payment_history']['actual_refund_amt']; ?></td> 
                                      </tr>
                                      <tr>
                                        <td><b>CD:</b> <?php echo $order_info['cashback_coupon_amount']; ?></td> 
                                        <td><b>Bal:</b> <?php echo $bal_amt; ?></td> 
                                      </tr>
                                    </table><br>
                                  </div>
                                  <div class="order-amount">
                                    <h6>
                                      <strong class="text-info">
                                        <span>OT: </span>
                                        <span><?php echo $order_info['total']; ?></span>
                                      </strong>
                                    </h6>
                                  </div>
                                  <?php
                                  if($order_info['currency_code'] == 'INR') {
                                    ?>
                                    <h4 style="font-size: 14px;">
                                    <span class="label <?php echo ($bal_amt < 0) ? 'label-danger': 'label-success' ; ?> ">
                                      <span>Bal: </span>
                                      <span><?php echo $this->currency->format($bal_amt, 1); ?></span>  
                                    </span>                                    
                                  </h4>
                                    <?php 
                                  }
                                  ?>
                                  
                                </td>
                                <td class="text-left">
                                    <?php echo $order_info['date_added']; ?>
                                    <!-- order processing date -->
                                    <hr class="btn-success">
                                    <?php if($order_info['order_processing_date']){ echo $order_info['order_processing_date'] ; } ?>
                                    <!-- END  -->
                                </td>
                                
                                <td class="text-center" style="width: 10px;">
                                    <button type="button" title="View Lazypay Log" class="btn btn-primary pull-right button-view-log" data-order-no="<?php echo $order_info['order_no'] ?>"><i class="fa fa-eye"></i></button>
                                </td>
                                  
                              </tr>

                              <tr>
                                <td style="display: none;" id="payment_history_<?php echo $order_info['order_id']; ?>">
                                  <table  class="table table-bordered payment_history_<?php echo $order_info['order_id']; ?>">
                                    <thead>
                                      <tr>
                                        <td class="text-center" width="20%"> Merchant txn id </td>
                                        <td class="text-center" width="20%"> Payment Link </td>
                                        <td class="text-center" width="10%"> Amount </td>
                                        <td class="text-center" width="10%"> Payment Gateway </td>
                                        <td class="text-center" width="10%"> Status </td>
                                        <td class="text-center" width="10%"> Date Added </td>
                                        <td class="text-center" width="10%"> Updated/Requested By </td>
                                        <td class="text-center" width="10%"> Txn Date </td>
                                      </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 0; 
                                          $already_exists_amount = array(); 
                                          foreach($order_info['payment_history']['result'] as $payment_history) { $i++; 
                                           $bck_color = ''; 
                                            if( $payment_history['successfull'] == 1 && $payment_history['amount'] > 0 && ($payment_history['bank_transfer_mode'] != 'cheque_deposited' OR $payment_history['bank_transfer_mode'] != 'cheque_failed') ) { $bck_color = 'label-success'; } 
                                            
                                            if($payment_history['successfull']){
                                              $already_exists_amount[] = trim($payment_history['amount']);
                                            }
                                    ?>
                                      <tr class="<?php echo 'payment_'.$payment_history['payment_gateway']; echo ' '. $bck_color;?>" >
                                            <td class="text-center order_list_comment" width="20%"><?php echo $payment_history['merchant_txn_id']; ?></td>
                                            <td class="text-center order_list_comment payment_history_link" width="20%">
                                                <?php echo $payment_history['payment_gateway'] == 'upi'?$payment_history['payment_link']:"<a href='".$payment_history['payment_link']."' target='_blank'>".$payment_history['payment_link']."</a>";?> 
                                            </td>
                                            <td class="text-center order_list_comment" width="10%">
                                              <span> <?php echo $payment_history['amount']; ?></span>
                                            </td>
                                            <td class="text-center order_list_comment" width="10%"><?php echo $payment_history['payment_gateway']; ?></td>
                                            <td class="text-center order_list_comment text-uppercase" width="10%"><?php echo $payment_history['txn_status']; ?></td>
                                            <td class="text-center order_list_comment" width="10%"><?php echo $payment_history['date_added']; ?></td>
                                            <td class="text-center order_list_comment" width="10%">
                                                    <?php
                                                    if(empty($payment_history['user'])){ ?>
                                                        During Order Placement
                                                    <?php } else {
                                                        echo $payment_history['user'];
                                                    } ?>
                                            </td>
                                            <td class="text-left order_list_comment" width="10%">
                                                    <?php if($payment_history['txn_date_time'] != NULL && $payment_history['txn_date_time'] !='00:00:00 00:00:00'){
                                                        echo $payment_history['txn_date_time'];
                                                     } ?>    
                                            </td>
                                      </tr>
                                    <?php } ?>
                                    <input type="hidden" 
                                           id="exists_amount_<?php echo $order_info['order_id'];?>" 
                                           value='<?php echo json_encode($already_exists_amount);?>'>
                                      <tr>
                                        <?php /* if($i != 0) { ?>
                                          <td>Total</td>
                                          <td><span class="percentage_amount order_list_comment"> Per: <?php
                                        $total_link_send_amount = ($order_info['payment_history']['total_link_send_amount']/$order_info['total_amount'])*100;
                                        echo sprintf('%0.2f', $total_link_send_amount); ?> </span> <span>Amt: <?php echo $order_info['payment_history']['total_link_send_amount']; ?> </span></td>
                                        <?php } */ ?>
                                      </tr>
                                    </tbody>
                                  </table>
                                </td>
                                <td style="display: none;" id="successfully_payment_history_<?php echo $order_info['order_id']; ?>">
                                    <!-- Successfully = 1 order payment history -->
                                    <table  class="table table-bordered successfully_payment_history_<?php echo $order_info['order_id']; ?>">
                                        <thead>
                                          <tr>
                                            <td class="text-center" width="20%"> Merchant txn id </td>
                                            <td class="text-center" width="20%"> Payment Link </td>
                                            <td class="text-center" width="10%"> Amount </td>
                                            <td class="text-center" width="10%"> Payment Gateway </td>
                                            <td class="text-center" width="10%"> Status </td>
                                            <td class="text-center" width="10%"> Date Added </td>
                                            <td class="text-center" width="10%"> Updated/Requested By </td>
                                            <td class="text-center" width="10%"> Txn Date </td>
                                          </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($order_info['payment_history']['result'] as $payment_history) { ?>
                                            <?php if( $payment_history['successfull'] == 1 && $payment_history['amount'] > 0 && ($payment_history['bank_transfer_mode'] != 'cheque_deposited' OR $payment_history['bank_transfer_mode'] != 'cheque_failed') ) { ?>
                                            <tr class="payment_success label-success">
                                                <td class="text-center order_list_comment" width="20%"><?php echo $payment_history['merchant_txn_id']; ?></td>
                                                <td class="text-center order_list_comment payment_history_link" width="20%"><a href="<?php echo $payment_history['payment_link']; ?>" target="_blank"> <?php echo $payment_history['payment_link']; ?> </a></td>
                                                <td class="text-center order_list_comment" width="10%">
                                                  <span> <?php echo $payment_history['amount']; ?></span>
                                                </td>
                                                <td class="text-center order_list_comment" width="10%"><?php echo $payment_history['payment_gateway']; ?></td>
                                                <td class="text-center order_list_comment text-uppercase" width="10%"><?php echo $payment_history['txn_status']; ?></td>
                                                <td class="text-center order_list_comment" width="10%"><?php echo $payment_history['date_added']; ?></td>
                                                <td class="text-center order_list_comment" width="10%">
                                                        <?php
                                                        if(empty($payment_history['user'])){ ?>
                                                            During Order Placement
                                                        <?php } else {
                                                            echo $payment_history['user'];
                                                        } ?>
                                                </td>
                                                <td class="text-left order_list_comment" width="10%">
                                                        <?php if($payment_history['txn_date_time'] != NULL && $payment_history['txn_date_time'] !='00:00:00 00:00:00'){
                                                            echo $payment_history['txn_date_time'];
                                                         } ?>    
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </td>
                                <td style="display: none;" id="tentative_advance_payment_history_<?php echo $order_info['order_id']; ?>">
                                    <!-- tentative advance order payment history -->
                                    <table  class="table table-bordered tentative_advance_payment_history_<?php echo $order_info['order_id']; ?>">
                                        <thead>
                                          <tr>
                                            <td class="text-center"> Payment Mode </td>
                                            <td class="text-center"> Cheque No. </td>
                                            <td class="text-center"> Txn No </td>
                                            <td class="text-center"> Dated </td>
                                            <td class="text-center"> Amount </td>
                                            <td class="text-center"> Notes </td>
                                            <td class="text-center"> Branch Name </td>
                                            <td class="text-center"> Transaction Status </td>
                                            <td class="text-center"> Staff Name </td>
                                          </tr>
                                        </thead>
                                        <tbody>
                                        <?php if(!empty($order_info['tentative_advance_history'])){ ?>
                                        <?php foreach($order_info['tentative_advance_history'] as $tentative_adv_history) { ?>
                                            <tr class="tentative_advance_history <?php //echo $bck_color; ?>">
                                                <td class="text-center order_list_comment"><?php echo $tentative_adv_history['payment_mode']; ?></td>
                                                <td class="text-center order_list_comment tentative_adv_history_link"> <?php echo $tentative_adv_history['cheque_no']; ?></td>
                                                <td class="text-center order_list_comment">
                                                  <?php echo $tentative_adv_history['txn_id']; ?>
                                                </td>
                                                <td class="text-center order_list_comment"><?php echo $tentative_adv_history['dated']; ?></td>
                                                <td class="text-center order_list_comment text-uppercase"><?php echo $tentative_adv_history['amount']; ?></td>
                                                <td class="text-center order_list_comment">
                                                        <?php echo $tentative_adv_history['notes'];?>
                                                </td>
                                                <td class="text-left order_list_comment"><?php echo $tentative_adv_history['branch_name'];?>  
                                                </td>
                                                <td><?php echo $tentative_adv_history['transaction_status'];?></td>
                                                <td><?php echo $tentative_adv_history['staff_name'];?></td>
                                            </tr>
                                        <?php } ?>
                                        <input type="hidden" class="tentative_advance_payment_history_hidden_<?php echo $order_info['order_id']; ?>" 
                                        data-tentative-amount="<?php echo $order_info['getOnlyTentativeAmount']; ?>"
                                        data-format-tentative-amount="<?php echo $this->currency->format($order_info['getOnlyTentativeAmount'], $order_info['currency_code'], $order_info['currency_value']); ?>"
                                        data-cashback-coupon-total-amount="<?php echo $order_info['cashback_coupon_amount']; ?>" 
                                        data-format-cashback-coupon-total-amount="<?php echo $this->currency->format($order_info['cashback_coupon_amount'], $order_info['currency_code'], $order_info['currency_value']); ?>"
                                        >
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </td>
                                <td style="display: none;" id="refund_payment_history_<?php echo $order_info['order_id']; ?>">
                                    <!-- refund order payment history -->
                                    <table  class="table table-bordered refund_payment_history_<?php echo $order_info['order_id']; ?>">
                                        <thead>
                                          <tr>
                                            <td class="text-center" width="20%"> Merchant txn id </td>
                                            <td class="text-center" width="20%"> Payment Link </td>
                                            <td class="text-center" width="10%"> Amount </td>
                                            <td class="text-center" width="10%"> Payment Gateway </td>
                                            <td class="text-center" width="10%"> Status </td>
                                            <td class="text-center" width="10%"> Date Added </td>
                                            <td class="text-center" width="10%"> Updated/Requested By </td>
                                            <td class="text-center" width="10%"> Txn Date </td>
                                          </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 0; foreach($order_info['payment_history']['result'] as $payment_history) { $i++; ?>
                                        <?php if( $payment_history['successfull'] == 1 &&  $payment_history['amount'] < 0 ) { ?>
                                          <tr class="label-danger <?php echo 'payment_'.$payment_history['payment_gateway'];?>" >
                                                <td class="text-center order_list_comment" width="20%"><?php echo $payment_history['merchant_txn_id']; ?></td>
                                                <td class="text-center order_list_comment payment_history_link" width="20%"><a href="<?php echo $payment_history['payment_link']; ?>" target="_blank"> <?php echo $payment_history['payment_link']; ?> </a></td>
                                                <td class="text-center order_list_comment" width="10%">
                                                  <span> <?php echo $payment_history['amount']; ?></span>
                                                </td>
                                                <td class="text-center order_list_comment" width="10%"><?php echo $payment_history['payment_gateway']; ?></td>
                                                <td class="text-center order_list_comment text-uppercase" width="10%"><?php echo $payment_history['txn_status']; ?></td>
                                                <td class="text-center order_list_comment" width="10%"><?php echo $payment_history['date_added']; ?></td>
                                                <td class="text-center order_list_comment" width="10%">
                                                        <?php
                                                        if(empty($payment_history['user'])){ ?>
                                                            During Order Placement
                                                        <?php } else {
                                                            echo $payment_history['user'];
                                                        } ?>
                                                </td>
                                                <td class="text-left order_list_comment" width="10%">
                                                        <?php if($payment_history['txn_date_time'] != NULL && $payment_history['txn_date_time'] !='00:00:00 00:00:00'){
                                                            echo $payment_history['txn_date_time'];
                                                         } ?>    
                                                </td>
                                          </tr>
                                        <?php } ?>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </td>
                              </tr>
                            <?php } ?>
                          <?php } else { ?>
                            <tr>
                              <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                            </tr>
                          <?php } ?>
                    </tbody>
                  </table>
                </div>
                <div class="row">
                  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
                </div>
            </div>
        </div>
    </div>
    <!-- popup for courier advisery -->
    <div class="courier_advisery_popup hidden"></div>
    <!-- -->

    <div class="show_generate_payment_link_popup display_none ui-widget-content" id="draggable">
        <div class="panel-heading order_payment_popup_heading" id="draggableheader">
            <h3 class="payment_order_no"></h3>
            <div class="apply_credit_note payment_cancel payment_cross"><i class="fa fa-times" aria-hidden="true"></i></div>
        </div>
        <div class="panel-heading order_payment_popup_body">
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-prepaid" id="credit_debit" data-toggle="tab">Credit Card / Debit Card / Net Banking / UPI </a></li>
                    <li><a href="#tab-manual_bank_transfer" data-toggle="tab">Manual Bank Transfer</a></li>
                    <li ><a href="#tab-promo" data-toggle="tab">Apply Promo</a></li>
                    <li ><a href="#tab-cash-advance" data-toggle="tab">Cash Advance</a></li>
                    <li ><a href="#tab-paytm-offline-qr" data-toggle="tab">Paytm Offline QR</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-prepaid">
                        <div class="form-group col-sm-12">
                        <div class="radio payment_percentage pull-left">
                            <label><input class="pull-left payment_link" type="radio" id ="percentage_radio" name="payment_type" title="Percentage" value="percentage" >Percentage:</label>
                        </div>

                        <div class="col-sm-10 pull-right">
                            <input type="text" name="payment_link_percentage" placeholder="Enter the % of Amount" id ="percentage" value="" class="form-control percentage" />
                            <span class="error_check display_none">* Please Enter Numeric Value *</span>
                        </div>
                        </div>
                        <br/>
                        <div class="form-group col-sm-12">
                        <div class="radio payment_amount pull-left">
                            <label><input class="pull-left payment_link" type="radio" id ="amount_radio" name="payment_type" title="Amount" value="amount">Amount:</label>
                        </div>
                        <div class="col-sm-10 pull-right">
                            <input type="text" name="payment_link_amount" placeholder="Enter the Amount" id="amount" value="" class="form-control amount" />
                        </div>
                        </div>
                        <br/>
                        <!--//////////-->

                        <div class="form-group col-sm-12">
                        <div class="payment_link_generation_for pull-left">
                            <label>Generate Payment Link for:</label>
                        </div>
                        <div class="col-sm-10 pull-right">
                            <select class="form-control payment_link_generation_for_options" id="payment_link_generation_for_options">
                              <option value="upi">UPI</option>
                              <option value="citrus">Citrus</option>
                              <?php 
                                if( in_array( $this->user->getId(), explode( ',', ADMIN_IDS )) 
                                    || in_array( $this->user->getId(), explode( ',', OPERATIONS_ADMIN_IDS ))) {

                                    echo "<option value='razorpay'>Razorpay</option>";
                                }
                              ?>
                              <!-- <option value="all" selected>All</option> -->
                            </select>
                        </div>
                        </div>
                        <br/>
                        <!--/////////-->
                        <!--//////////-->

                        <div class="form-group col-sm-12">
                        <div class="upi_vpa pull-left">
                            <label>UPI VPA Address:</label>
                        </div>
                        <div class="col-sm-10 pull-right">
                            <div class="col-sm-11 upi_text">
                               <input type="text" name="payment_link_upi_vpa" placeholder="Enter Customer's VPA" id ="upi_vpa" value="" class="form-control payment_link_upi_vpa" />
                               <span class="error_check display_none">* Please Enter valid VPA *</span>
                            </div>
                            <div class="col-sm-1 upi_edit_pencil">
                             <i class="fa fa-pencil edit_vpa"></i>
                            </div>
                            
                        </div>
                        </div>
                        <br/>
                        <!--/////////-->
                        <div class="col-sm-12">
                        <div class="common_buttons_payment_link  pull-left">
                            <button type="button" class="btn btn-danger form-control payment_cancel"><?php echo $text_cancel; ?></button>
                        </div>
                        <div class="common_buttons_payment_link pull-right">
                            <button type="button" class="btn btn-success form-control payment_link_send"><?php echo $text_send; ?></button>
                        </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab-manual_bank_transfer">
                        <div class="col-sm-12">
                            <div class="form-group required col-sm-6">
                                <label class="control-label text-left" for="payment_reff_no"><?php echo $text_payment_reff_no; ?></label>
                                <input type="text" name="payment_reff_no" value="" placeholder="<?php echo $entry_payment_reff_no; ?>" id="payment_reff_no" class="form-control" />
                                <span class="error_bank payment_reff_no"><?php echo $error_payment_reff_no; ?></span>
                            </div>
                            <div class="form-group required col-sm-6">
                                <label class="control-label text-left" for="bank_name"><?php echo $text_bank_name; ?></label>
                                <select name="bank_name" id="bank_name" class="form-control">
                                    <option selected="selected" value="ICICI">ICICI</option>
                                    <option value="Standard Chartered">Standard Chartered</option>
                                </select>
                                <span class="error_bank bank_name"><?php echo $error_bank_name; ?></span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group required col-sm-4">
                                <label class="control-label text-left" for="bank_amount "><?php echo $text_bank_total_amount; ?></label>
                                <input type="text" name="bank_amount" value="" placeholder="<?php echo $entry_bank_total_amount; ?>" id="bank_amount" class="form-control" />
                                <span class="error_bank bank_amount"><?php echo $error_bank_amount; ?></span>
                            </div>
                            <div class="form-group required col-sm-4">
                                <label class="control-label text-left" for="payment_date"><?php echo $text_payment_date; ?></label>
                                <div class="input-group payment_history_date">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                    <input type="text" name="payment_date" value="" placeholder="<?php echo $entry_payment_date; ?>" id="payment_date" class="form-control" />
                                </div>
                                <span class="error_bank payment_date"><?php echo $error_payment_date; ?></span>
                            </div>
                            <div class="form-group required col-sm-4">
                                <label class="control-label text-left" for="bank_name"><?php echo $text_bnk_trnsfer_mode; ?></label>
                                <select name="bank_transfer_mode" id="bank_transfer_mode" class="form-control">
                                    <option value="">--Select--</option>
                                    <option value="instant"><?php echo $text_instant; ?></option>
                                    <option value="cheque_deposited"><?php echo $text_cheque_deposit; ?></option>
                                </select>
                                <span class="error_bank bank_transfer_mode"><?php echo $error_bank_transfer_mode; ?></span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="common_buttons_payment_link  pull-left">
                                <button type="button" class="btn btn-danger form-control payment_cancel"><?php echo $text_cancel; ?></button>
                            </div>
                            <div class="common_buttons_payment_link pull-right">
                                <button type="button" class="btn btn-success form-control manual_bank_send"><?php echo $text_save; ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab-promo">
                        <div class="col-sm-4">
                            <div class="form-group required">
                                <label class="control-label" for="promo_amount">Amount</label>
                                <input type="text" name="promo_amount" value="" placeholder="Amount" id="promo_amount" class="form-control" />
                                <span class="error_promo_amount error"></span>
                            </div>
                            <div class="form-group required">
                                <label class="control-label" for="reasons">Promocode Against</label>
                                <select name="reasons" id="reasons" class="form-control">
                                  <option value="">--SELECT--</option>
                                  <option value="COD_Failed_Order">COD Failed Order</option>
                                  <option value="Excess_Payment">Excess Payment</option>
                                  <option value="Extra_Discount">Extra Discount</option>
                                  <option value="Short_Delivery">Short Delivery</option>
                                  <option value="Shipping_Charges">Shipping Charges</option>
                                  <option value="Damaged_Defective_Wrong_Product">Damaged/Defective/Wrong Product</option>
                                  <option value="Client_Retention">Client Retention</option>
                                  <option value="Cashback_Expired">Cashback Expired</option>
                                </select>
                                <span class="error_reasons error"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="form-group required">
                              <label class="control-label" for="promo_code">Promo Code</label>
                              <input type="text" name="promo_code" value="" placeholder="Promo Code" id="promo_code" class="form-control" />
                              <span class="error_promo_code error"></span>
                          </div>
                            <div class="form-group">
                                <label class="control-label text-left" for="order_no">Order No. Against</label>
                                <input type="text" name="order_no_against" value="" placeholder="Order No. Against" id="order_no_against" class="form-control order_no" />
                                <span class="error error_order_no"></span>
                            </div>
                            
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="collected_by"><?php echo 'Additional Remarks'; ?></label>
                                <textarea placeholder="<?php echo 'Additional Remarks'; ?>" class="promo_additional_remarks form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="common_buttons_payment_link  pull-left">
                                <button type="button" class="btn btn-danger form-control payment_cancel"><?php echo $text_cancel; ?></button>
                            </div>
                            <div class="common_buttons_payment_link pull-right">
                                <button type="button" 
                                        class="btn btn-success form-control promo_amount_save">
                                        <?php echo $text_save; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab-cash-advance">
                        <div class="col-sm-6">
                            <div class="form-group required">
                                <label class="control-label" for="advance_amount"><?php echo 'Amount'; ?></label>
                                <input type="text" name="advance_amount" value="" placeholder="<?php echo 'Amount'; ?>" id="advance_amount" class="form-control" />
                                <span class="error_advance_amt error"></span>
                            </div>
                            <div class="form-group required">
                                <label class="control-label text-left" for="payment_date"><?php echo $text_payment_date; ?></label>
                                <div class="input-group payment_history_date">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                    <input type="text" name="adv_payment_date" value="" placeholder="<?php echo $entry_payment_date; ?>" id="adv_payment_date" class="form-control adv_payment_date" />
                                </div>
                                <span class="error error_advance_amt_date"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group required">
                                <label class="control-label" for="collected_by"><?php echo 'Collected By'; ?></label>
                                <select name="collected_by" id="collected_by" class="form-control">
                                    <?php if(!empty($sales_staff_list)) { ?>
                                        <option value="">--SELECT--</option>
                                        <?php foreach($sales_staff_list as $sales_staff_id => $sales_staff){ ?>
                                            <option value="<?php echo $sales_staff_id;?>"><?php echo $sales_staff['name'];?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <input type="text" class="form-control other_person_name" value="" placeholder="Other person name" name="sales_person_name">
                                <span class="error_advance_collect_by error"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="collected_by"><?php echo 'Additional Remarks'; ?></label>
                                <textarea placeholder="<?php echo 'Additional Remarks'; ?>" class="adv_additional_remarks form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="common_buttons_payment_link  pull-left">
                                <button type="button" class="btn btn-danger form-control payment_cancel"><?php echo $text_cancel; ?></button>
                            </div>
                            <div class="common_buttons_payment_link pull-right">
                                <button type="button" class="btn btn-success form-control advance_amount_save"><?php echo $text_save; ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab-paytm-offline-qr">
                        <div class="col-sm-12">
                            <div class="form-group required col-sm-4">
                                <label class="control-label text-left" for="paytm_payment_reff_no"><?php echo $text_payment_reff_no; ?></label>
                                <input type="text" name="paytm_payment_reff_no" value="" placeholder="<?php echo $entry_payment_reff_no; ?>" id="paytm_payment_reff_no" class="form-control" />
                                <span class="error_bank paytm_payment_reff_no"><?php echo $error_payment_reff_no; ?></span>
                            </div>
                            <div class="form-group required col-sm-4">
                                <label class="control-label text-left" for="paytm_amount"><?php echo $text_bank_total_amount; ?></label>
                                <input type="text" name="paytm_amount" value="" placeholder="<?php echo $entry_bank_total_amount; ?>" id="paytm_amount" class="form-control" />
                                <span class="error_bank paytm_amount"><?php echo $error_bank_amount; ?></span>
                            </div>
                            <div class="form-group required col-sm-4">
                                <label class="control-label text-left" for="paytm_payment_date"><?php echo $text_payment_date; ?></label>
                                <div class="input-group payment_history_date">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                    <input type="text" name="paytm_payment_date" value="" placeholder="<?php echo $entry_payment_date; ?>" id="paytm_payment_date" class="form-control" />
                                </div>
                                <span class="error_bank paytm_payment_date"><?php echo $error_payment_date; ?></span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="common_buttons_payment_link  pull-left">
                                <button type="button" class="btn btn-danger form-control payment_cancel"><?php echo $text_cancel; ?></button>
                            </div>
                            <div class="common_buttons_payment_link pull-right">
                                <button type="button" class="btn btn-success form-control paytm_offline_send_btn"><?php echo $text_save; ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-12">
                <hr>
                <div class="row payment_and_balence_amount_div">
                    <label class="col-sm-4 pull-left payment_history_block_sm"><span class="remaining_pay_amount recived_payment"> Total Amount : </span><span class="total_payment_amount"></span>/-</label>
                    <label class="col-sm-4 pull-left">
                      <span class="balance_total_amount text-warning payment_history_block_sm">
                        <span>Balance Amount : </span><span class="total_balance_amount"> </span>/- <br>                        
                      </span>
                      <span class="tentative_total text-info btn-xs payment_history_block_sm">
                        <span> Tentative Amount: </span> <span class="customer_total_tentative_amount"> </span>/-<br>
                      </span>
                      <span class="cash_back_coupon text-info btn-xs payment_history_block_sm">
                        <span> Cashback / Coupon : </span> <span class="cash_back_coupon_total_amount"> </span>/-<br>
                      </span>
                    </label>
                    <label class="col-sm-4 pull-right payment_history_block_sm">
                        <span class="text-success">
                            <span> Payment Recived: </span> <span class="customer_recived_amount"> </span>/-<br>
                        </span>
                        <span class="refund_total">
                            <!-- <span> Total Refund Send: </span> <span class="customer_total_refund_send"> </span>/-<br> -->
                        </span>
                    </label>
                </div>
                <!-- <div id="payment_history_table">
                </div> -->
                <div class="block_wise_order_payment_history">
                    <ul class="nav nav-tabs profile_tebination">
                        <li class="active"><a href="#all_transaction" data-toggle="tab" aria-expanded="false">All Transaction</a></li>
                        <li class=""><a href="#receipts" class="label-success" data-toggle="tab" aria-expanded="true">Receipts</a></li>
                        <li class=""><a href="#refund" class="label-danger" data-toggle="tab" aria-expanded="true">Refund</a></li>
                        <li class=""><a href="#tentative_entries" data-toggle="tab" aria-expanded="false">Tentative Entries</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="all_transaction" class="tab-pane fade active in">
                            <div id="payment_history_table"></div>
                        </div>
                        <div id="receipts" class="tab-pane fade">
                            <div id="successfully_payment_history_table"></div>
                        </div>
                        <div id="refund" class="tab-pane fade">
                            <div id="refund_payment_history_table"></div>
                        </div>
                        <div id="tentative_entries" class="tab-pane fade">
                          <div id="tentative_advance_payment_history_table"></div>
                        </div>
                    </div>
                </div>   
                <span class="not_send_payment_link recived_payment display_none"> Payment Link Not Send due to Some Error <br> Please contact to Administrator</span>
            </div>
        </div>
    </div>
</div>

<div class="refund_payment_links_popup display_none">
    <h3 class="not_send_payment_refund display_none"></h3><br>
    <div class="col-sm-12">
        <span class="col-sm-11 pull-left"><h3 class="refund_payment_order_no"></h3></span>
        <span class="col-sm-1 payment_refund_cancel apply_credit_note pull-right"><i class="fa fa-times" aria-hidden="true"></i></span>
    </div>
    <br><hr>
    <div class="form-group col-sm-12">
        <div class="col-sm-4 radio full_amount_refund pull-left">
            <label><input class="pull-left refund_type" type="radio" id ="full_amount" name="refund_amount" title="Full Refund" value="full"> Full Refund </label>
        </div>
        <div class="col-sm-7 full_refund_amt">
            <input type="hidden" name="full_refund_amount" value="" class="form-control full_refund_amount" />
            <span class ="full_refund_amount_value"></span>
        </div>
    </div>
    <div class="form-group col-sm-12">
        <div class="col-sm-4 radio partial_amount_refund pull-left">
            <label><input class="pull-left refund_type" type="radio" id ="partial_amount" name="refund_amount" title="Partial Refund" value="partial"> Partial amount </label>
        </div>
        <div class="col-sm-7 partial_refund_amount_text display_none">
            <input type="text" name="partial_refund_amount" placeholder="Enter Partial Amount" value="" class="form-control partial_refund_amount" />
        </div>
    </div>
    <div class="form-group col-sm-12">
        <div class="col-xs-3  pull-left">
            <button type="button" class="btn btn-danger form-control payment_refund_cancel"> Cancel </button>
        </div>
        <div class="col-xs-3 pull-right">
            <button type="button" class="btn btn-success form-control payment_refund_send"> SEND </button>
        </div>
    </div>
</div>
<!-- customer duplicate id modal kss -->
<div id="mark_duplicate_popup" class="modal fade" role="dialog"><?php echo $duplicate_popup;?></div>
<!-- Modal end -->

<!-- NILESH SCRIPT-->
<script type="text/javascript">
    
    $(document).ready(function(){
        $('.well').hide();
        $('#advance_filter').click(function(){
            $('.well').toggle();
        });
    });
    $('#input-sales-staff').on('keyup',function(){
        if($(this).val().trim().length == 0){
            $('#input-sales-staff-id').val(0);
        }
    });
    $('#input-sales-staff').autocomplete({
      'source': function(request, response) {
         let json = [];
         if(request.length > 0){
             if(typeof request == 'string'){
                request = request.toLowerCase();
             }
             let sales_staff_list = <?php echo json_encode($sales_staff_list); ?>;
             
             $.each(sales_staff_list, function(ind,element){
                let data;
                let name = element.name.toLowerCase();

                if( name.search(request) >= 0 ){
                    data = $.parseJSON('{"label":"' + element.name + '","value":"' + ind + '"}');
                }
                if( data ){
                 json.push(data);
                }
             });
         }
         response(json);
      },
      'select': function(item) {
        $('#input-sales-staff').val(item['label']);
        $('#input-sales-staff-id').val(item['value']);
      }
    });
</script>
<script type="text/javascript">
    
    
  $(function () {
      var order_total_advance = <?php echo !empty($order_total_advance) ? $order_total_advance : '{}'; ?>;
      var order_advance_breakup = <?php echo !empty($order_advance_breakup) ? $order_advance_breakup : '{}'; ?>;
      var advance_locked_status = <?php echo !empty($advance_locked_status) ? $advance_locked_status : '{}'; ?>;
      $('#myModal').on('show.bs.modal', function (e) {
          $('#advance_form').html('');
          var order_id = $(e.relatedTarget).data('order_id');
          $('#advance_form').attr('data-order_id',order_id);
          if(order_total_advance[order_id]){
              $('#total_advance').html(order_total_advance[order_id]);
          }
          var suborder_total_advance = 0;
          if(order_advance_breakup[order_id]){
              var suborder_ids = order_advance_breakup[order_id];
              var html  = "";
              $(suborder_ids).each(function( index , element ){
                  var advance = element;
                  var locked_status = advance_locked_status[element['suborder_id']];
                      html += '<div id="' + element['suborder_id'] + '" data-locked="' + locked_status + '" class="row suborder" style="margin-top:10px">';
                      html +=    '<div class="col-xs-3">';
                      html +=      element['suborder_id'];
                      html +=    '</div>';
                      html +=    '<div class="col-xs-3">';
                      html +=      element['value'];
                      html +=    '</div>';
                      html +=    '<div class="col-xs-2">';
                      html +=      '<input class="advance_edit" style="width:80px" ' + ( locked_status ? 'disabled' : '' ) +' type="text" name="advance['+ element['suborder_id'] +'][value]" value="' +  element['value'] + '" />';
                      html +=    '</div>';
                      //html +=    '<div class="col-xs-2">';
                      //html +=       '<input type="checkbox" name="advance[' + element['suborder_id'] + '][locked]"' + ( locked_status ? ' disabled ' : '' )  + ( locked_status || (element['locked'] == 'true') ? ' checked ' : '')  + ' value="true">';
                      //html +=    '</div>';
                      html +=    '<div class="col-xs-2">';
                      html +=       element['user'] ;
                      html +=    '</div>';
                      html +=    '<input type="hidden" name="advance[' + element['suborder_id'] + '][user]" value="<?php echo $user['name']; ?>">';
                      html +=    '<input type="hidden" name="order_id" value="' + order_id + '">';
                      html +=  '</div>';
                      suborder_total_advance += parseFloat(element['value']);
              });
              $('#advance_form').append(html);
          }
          $('#remaining_advance').html(parseFloat(order_total_advance[order_id]) - suborder_total_advance );
      });
      $('.advance_edit').on('change',function(){
          $(this).value();
          $(this).parents('.row').find('');
      });

      $('.save_advance').click(function(){
          if(validateAdvance()){
             var formdata = new FormData($('#advance_form')[0]);
             $.ajax({
                 url: 'index.php?route=sale/order/saveadvance&token=<?php echo $token; ?>',
                 type: 'post',
                 data: formdata,
                 mimeType: "form-data",
           contentType: false,
           processData: false,
                 dataType: 'json',
                 success: function(data){
                     if(data['success']){
                        location.reload();
                     }
                     else{
                         alert(data['message']);
                     }
                 }
             });
          }
      });

      function validateAdvance(){
      
          var order_id = $('#advance_form').data('order_id');
          var total = order_total_advance[order_id];
          var new_total = 0;
          var change = [];
          $('#advance_form > .suborder').each( function( index , element ){
              var val = parseFloat($(this).find('.advance_edit').val());
              var suborder_id = this.id;
              new_total += val;
          });
          if(total < new_total){
              alert("New Advance Total can not greater than total advance value.");
              return false;
          }
          return true;
      }
  });
</script>

<script type="text/javascript">

    $('#franchise_data_download').on('click',function(){
      url = window.location.href;
      url += '&franchise_data_download=1';
      location = url;
    });

    $('.change_payment').click(function(){
      if (confirm("Do you really want change order as Credit?")) {
          var payment_code = 'credit';
          var order_id = $(this).attr('data-order_id');
            $.ajax({
                url: "index.php?route=franchise/franchise/changePaymentMethodOfFranchiseOrder&token=<?php echo $token; ?>",
                type: "post",
                dataType: "json",
                data: "order_id=" + order_id + "&payment_code=" + payment_code,
                beforeSend: function(data) {
                    $('#change_payment_'+order_id).button('loading');
                },
                success: function(data) {
                    if (data) {
                        //window.location.reload();
                    } else {
                        alert('You dont have permission. Please contact Administrator !');
                    }
                },
                complete: function(data) {
                    $('#change_payment_'+order_id).hide();
                }
            });
        } else {
            return false;
        }
    });
    
    $('#button-filter').on('click', function() {

        if ($('#filter_franchise_tab').length > 0) {
            url = 'index.php?route=operations/lazypay_order&filter_franchise_tab=1&token=<?php echo $token; ?>';
        } else {
            url = 'index.php?route=operations/lazypay_order&token=<?php echo $token; ?>';
        }

        var filter_order_no = $('input[name=\'filter_order_no\']').val();

        if (filter_order_no) {
            url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
        }

        var filter_customer = $('input[name=\'filter_customer\']').val();

        if (filter_customer) {
            url += '&filter_customer=' + encodeURIComponent(filter_customer);
        }

        var filter_company = $('input[name=\'filter_company\']').val();

        if (filter_company) {
            url += '&filter_company=' + encodeURIComponent(filter_company);
        }

        var filter_city = $('input[name=\'filter_city\']').val();

        if (filter_city) {
            url += '&filter_city=' + encodeURIComponent(filter_city);
        }

        var filter_order_status = $('select[name=\'filter_order_status\']').val();

        if (filter_order_status != '*') {
            url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
        }

        var filter_total = $('input[name=\'filter_total\']').val();

        if (filter_total) {
            url += '&filter_total=' + encodeURIComponent(filter_total);
        }

        var filter_date_added = $('input[name=\'filter_date_added\']').val();

        if (filter_date_added) {
            url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
        }

        var filter_total_low = $('input[name=\'filter_total_low\']').val();

        if (filter_total_low) {
          url += '&filter_total_low=' + encodeURIComponent(filter_total_low);
        }

        var filter_total_high = $('input[name=\'filter_total_high\']').val();

        if (filter_total_high) {
          url += '&filter_total_high=' + encodeURIComponent(filter_total_high);
        }

        var filter_pickup_city_code = $('input[name=\'filter_pickup_city_code\']').val();

        if (filter_pickup_city_code) {
        url += '&filter_pickup_city_code=' + encodeURIComponent(filter_pickup_city_code);
        }

        var filter_payment_code = $('select[name=\'filter_payment_code\']').val();

        if (filter_payment_code != '*') {
            url += '&filter_payment_code=' + encodeURIComponent(filter_payment_code);
        }
        
        var filter_sales_staff_id = $('input[name=\'filter_sales_staff_id\']').val();

        if (filter_sales_staff_id) {
            url += '&filter_sales_staff_id=' + encodeURIComponent(filter_sales_staff_id);
        }
        
        var filter_sales_zone = $('select[name=\'filter_sales_zone\']').val();

        if (filter_sales_zone != '*') {
            url += '&filter_sales_zone=' + encodeURIComponent(filter_sales_zone);
        }
        
        var filter_sales_staff = $('input[name=\'filter_sales_staff\']').val();

        if (filter_sales_staff) {
            url += '&filter_sales_staff=' + encodeURIComponent(filter_sales_staff);
        }

        var filter_good_process = $('select[name=\'filter_good_process\']').val();

        if (filter_good_process != '*') {
            url += '&filter_good_process=' + encodeURIComponent(filter_good_process);
        }

        var filter_courier = $('select[name=\'filter_courier\']').val();

        if (filter_courier != '*') {
            url += '&filter_courier=' + encodeURIComponent(filter_courier);
        }

        var filter_gst_number = $('input[name=\'filter_gst_number\']').val();

        if (filter_gst_number) {
            url += '&filter_gst_number=' + encodeURIComponent(filter_gst_number);
        }


        var filter_franchise_id = $('select[name=\'filter_franchise_id\']').val();
        if (filter_franchise_id) {
            url += '&filter_franchise_id=' + encodeURIComponent(filter_franchise_id);
          }
          
        var filter_tracking_no = $('input[name=\'filter_tracking_no\']').val();

        if (filter_tracking_no) {
            url += '&filter_tracking_no=' + encodeURIComponent(filter_tracking_no);
        }

        location = url;
    });
    
</script>

<script type="text/javascript">
    <!--
    $('input[name^=\'selected\']').on('change', function() {
        $('#button-detail-invoice, #button-b2b-invoice, #button-b2c-invoice').prop('disabled', true);

        var selected = $('input[name^=\'selected\']:checked');

        if (selected.length) {
            $('#button-detail-invoice, #button-b2b-invoice, #button-b2c-invoice').prop('disabled', false);
        }

        for (i = 0; i < selected.length; i++) {
            if ($(selected[i]).parent().find('input[name^=\'shipping_code\']').val()) {
                $('#button-shipping').prop('disabled', false);

                break;
            }
        }
    });

    $('input[name^=\'selected\']:first').trigger('change');

    $('a[id^=\'button-delete\']').on('click', function(e) {
        e.preventDefault();

        if (confirm('<?php echo $text_confirm; ?>')) {
            location = $(this).attr('href');
        }
    });
    //-->
</script>
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript">
    <!--
    $('.date').datetimepicker({
        pickTime: false
    });
    $('.payment_history_date').datetimepicker({
        maxDate: new Date()
    });
    //-->
</script>

<?php echo $footer; ?>
<script type="text/javascript">
  $('input[name=\'filter_order_no\'], input[name=\'filter_date_added\'], input[name=\'filter_customer\'], input[name=\'filter_company\'], input[name=\'filter_city\'], input[name=\'filter_total_low\'], input[name=\'filter_total_high\'],input[name=\'filter_pickup_city_code\'], select[name=\'filter_order_status\'], select[name=\'filter_sales_zone\'], input[name=\'filter_sales_staff_id\'], input[name=\'filter_sales_staff\'], select[name=\'filter_franchise_id\']').on('keypress',function(e){
    if (e.keyCode == 13) {
      $('#button-filter').trigger('click');
    }
  });

  $(document).ready(function(){
    $('input[name = \'filter_pickup_city_code\']').keyup(function(){
      var input_value = $(this).val();
      input_value_1 = $.trim(input_value.replace(/[_-]/g, " "));
      $(this).val(input_value_1);
    });
  });
</script>
<style>
    .filter_total {
        width: 50% !important;
        float: left;
    }
</style>
<script type="text/javascript">
    function refund_payment_links(obj) {
        $('.refund_payment_links_popup').show();
        $('.show_generate_payment_link_popup').hide();
        $("#full_amount").prop("checked", true);
        $('.partial_refund_amount_text').hide();
        $('.not_send_payment_refund').hide();
        var merchant_txn_id = $(obj).attr('data-merchant_txn_id');
        var amount = $(obj).attr('data-total');
        var refund_total = $(obj).attr('data-refund_total');
        var order_no = $(obj).attr('data-no');
        var order_id = $(obj).attr('data-id');
        var new_refund_total = Number(amount) + Number(refund_total);

        $('.refund_payment_order_no').text(order_no);
        $('.payment_refund_send').attr('data-merchant_txn_id', merchant_txn_id);
        $('.payment_refund_send').attr('data-refund_total', new_refund_total);
        $('.payment_refund_send').attr('data-order_no', order_no);
        $('.payment_refund_send').attr('data-order_id', order_id);
        $('.full_refund_amount_value').val(new_refund_total);
        $('.full_refund_amount_value').text(new_refund_total);
    }
    $(document).ready(function() {

      //Refresh order total
      $('.refresh_total').click(function(){
        var order_id = $(this).attr('data-order_id');
        if(order_id != '') {
          $.ajax({
              url: "index.php?route=sale/order/refreshOrderTotal&token=<?php echo $token; ?>",
              type: "post",
              dataType: "json",
              data: "order_id=" + order_id,
              success: function(data) {
                  if (data.success == 'Order Total updated successfully.') {
                      window.location.reload();
                  } else {
                      alert(data.error);
                  }
              }
          });
        } else {
          alert('Something went wrong. Please try again');
          return false;
        }
      });

        $('.sub_order').click(function() {
            //$('.sub_order_id').hide();
            var id = $(this).attr('data-id');
            $('#sub_order_id_' + id).toggle();
        });
        $('.payment_refund_cancel').click(function() {
            $('.refund_payment_links_popup').hide();
            $('.show_generate_payment_link_popup').show();
        });
        $('.refund_type').click(function() {
            var val = $(this).val();
            if (val == 'partial') {
                $('.partial_refund_amount_text').show();
            } else {
                $('.partial_refund_amount_text').hide();
            }
        });

        $('.payment_refund_send').click(function() {
            var order_id = $(this).attr('data-order_id');
            var order_no = $(this).attr('data-order_no');
            var refund_total = $(this).attr('data-refund_total');
            var merchant_txn_id = $(this).attr('data-merchant_txn_id');

            var val = $('input[name=refund_amount]:checked').val();
            if (val == 'partial') {
                var refund_amount = $('.partial_refund_amount').val();
            } else {
                var refund_amount = $('.full_refund_amount_value').val();
            }
            if (refund_amount == '') {
                alert("Please Enter Refund Amount");
            } else {
                if (refund_amount <= refund_total && refund_amount.match(/^[0-9]+\.?[0-9]*$/) ) {
                    if (confirm("Do you really want to Refund Rs. " + refund_amount + " for Order No: " + order_no )) {
                        $.ajax({
                            url: "index.php?route=sale/order/paymentRefund&token=<?php echo $token; ?>",
                            type: "post",
                            dataType: "json",
                            data: "order_id=" + order_id + "&order_no=" + order_no + "&merchant_txn_id=" + merchant_txn_id + "&refund_amount=" + refund_amount,
                            success: function(data) {
                                if (data.respMsg == 'Transaction successful' && data.merchantTxnId == merchant_txn_id) {
                                    window.location.reload();
                                } else {
                                    alert('You dont have permission to send payment link. Please contact Administrator !');
                                }
                            },
                            failure: function(data) {
                                $('.not_send_payment_refund').show();
                            }
                        });
                    } else {
                        return false;
                    }
                } else {
                    alert("You have enter wrong amount please check your refund amount");
                }
            }
        });

        $('.manual_bank_send').click(function() {            
            $('.error_bank').hide();
            var order_id                = $('.payment_link_send').attr('data-id');
            var order_no                = $('.payment_link_send').attr('data-no');
            var order_total             = $('.payment_link_send').attr('data-total');
            var order_recived_amount    = $(this).attr('data-recived_amount');
            var payment_reff_no         = $('#payment_reff_no').val();
            var bank_name               = $('#bank_name').val();
            var bank_amount             = $('#bank_amount').val();
            var payment_date            = $('#payment_date').val();
            var bank_transfer_mode      = $('#bank_transfer_mode').val();

            already_exists_amount = $("#exists_amount_"+order_id).attr('value');

            if (payment_reff_no == '') {
                $('.payment_reff_no').show();
                return false;
            }
            if (bank_name == '') {
                $('.bank_name').show();
                return false;
            }
            if (bank_amount == '') {
                $('.bank_amount').show();
                return false;
            }else if(bank_amount <= 0){
                $('.bank_amount').show();
                $('.bank_amount').text('Enter the amount is greater than 0');
                return false;
            }
            if (payment_date == '') {
                $('.payment_date').show();
                return false;
            }
            if(bank_transfer_mode == ''){
                $('.bank_transfer_mode').show();
                return false;
            }

            if(already_exists_amount.search(bank_amount)>0){
              if( !confirm('Payment entry of Rs. '+ bank_amount + ' already exists against this order. Are you sure that you are adding a new payment entry ?') ){
                return false;                
              } 
            }

            if (bank_amount.match(/^[0-9]+\.?[0-9]*$/)) {
                $.ajax({
                    url: "index.php?route=sale/order/manualBankTransfer&token=<?php echo $token; ?>",
                    type: "post",
                    dataType: "json",
                    data: "order_id=" + order_id + "&order_no=" + order_no + "&order_total=" + order_total + '&payment_reff_no=' + payment_reff_no + '&bank_name=' + bank_name + '&bank_amount=' + bank_amount + '&payment_date=' + payment_date + '&bank_transfer_mode=' + bank_transfer_mode,
                    beforeSend: function() {
                        $('.manual_bank_send').button('loading');
                    },
                    complete: function() {
                        $('.manual_bank_send').button('reset');
                    },
                    success: function(data) {
                        if (data.success == 'success') {

                            var txt_success = 'SUCCESS';
                            if(data.bnk_trnfr_mode == 'cheque_deposited'){
                                txt_success = data.bnk_trnfr_mode;
                            }

                            $('#bank_transfer_mode').val('');
                            var now = new Date(Date.now());
                            var time = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate() + " " + now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds();
                            var amt_per = (bank_amount / order_total) * 100;
                            var new_total = Number(order_recived_amount) + Number(bank_amount);
                            $('.customer_recived_amount').text(new_total);
                            var html = '<tr class="payment_success">';
                            html += '<td>' + $('#payment_reff_no').val()+ '</td>';
                            html += '<td class="order_list_comment payment_history_link"> <a href="'+ bank_name + '" target="_blank">'+ bank_name+ ' - ' + payment_reff_no + '</a> </td>';
                            html += '<td><span class="percentage_amount"> ' + amt_per.toFixed(2) + " % </span> <span> Amt:" + bank_amount + '</span> </td>';
                            html += '<td>bank_transfer</td>';
                            html += '<td class="text-uppercase">'+ txt_success +'</td>';
                            html += '<td>' + time + '</td>';
                            html += '<td>' + data.user + '</td>';
                            html += '<td class="recived_payment"> New </td>';
                            html += '<tr>';
                            remove_bank_info();
                            $('.payment_history_' + order_id).each(function(index, element) {
                                $(element).find('tr').last().before(html);
                            });
                            window.location.reload();
                        } else if(data.error == 'error'){
                            alert(data.message);
                        } else {
                            alert('You dont have permission to update Bank payments. Please contact administrator !');
                        }
                    },
                    failure: function(data) {
                        $('.not_send_payment_link').show();
                    }
                });
            } else {
                $('.bank_amount').show();
            }
        });

        $('.paytm_offline_send_btn').click(function() {
          $('.error_bank').hide();
          var order_id                = $('.payment_link_send').attr('data-id');
          var order_no                = $('.payment_link_send').attr('data-no');
          var order_total             = $('.payment_link_send').attr('data-total');
          var order_recived_amount    = $(this).attr('data-recived_amount');
          var paytm_payment_reff_no   = $('#paytm_payment_reff_no').val();
          var paytm_amount            = $('#paytm_amount').val();
          var paytm_payment_date      = $('#paytm_payment_date').val();

          already_exists_amount = $("#exists_amount_"+order_id).attr('value');

          if (paytm_payment_reff_no == '') {
            $('.paytm_payment_reff_no').show();
            return false;
          }
          
          if (paytm_amount == '') {
              $('.paytm_amount').show();
              return false;
          }else if(paytm_amount <= 0){
              $('.paytm_amount').show();
              $('.paytm_amount').text('Enter the amount is greater than 0');
              return false;
          }
          if (paytm_payment_date == '') {
              $('.paytm_payment_date').show();
              return false;
          }

          if(already_exists_amount.search(paytm_amount)>0){
            if( !confirm('Payment entry of Rs. '+ paytm_amount + ' already exists against this order. Are you sure that you are adding a new payment entry ?') ){
              return false;                
            } 
          }
          
          if (paytm_amount.match(/^[0-9]+\.?[0-9]*$/)) {
              $.ajax({
                  url: "index.php?route=sale/order/applyPaytmOfflineQR&token=<?php echo $token; ?>",
                  type: "post",
                  dataType: "json",
                  data: "order_id=" + order_id + 
                        "&order_no=" + order_no + 
                        "&order_total=" + order_total + 
                        '&paytm_payment_reff_no=' + paytm_payment_reff_no + 
                        '&paytm_amount=' + paytm_amount + 
                        '&paytm_payment_date=' + paytm_payment_date,
                  beforeSend: function() {
                      $('.paytm_offline_send_btn').button('loading');
                  },
                  complete: function() {
                      $('.paytm_offline_send_btn').button('reset');
                  },
                  success: function(data) {
                      if (data.success == 'success') {

                          var txt_success = 'SUCCESS';
                          var now = new Date(Date.now());
                          var getMonth = ('0' + now.getMonth()+1).slice(-2);
                          var time = now.getFullYear() + "-" + getMonth + "-" + now.getDate() + " " + now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds();
                          var amt_per = (paytm_amount / order_total) * 100;
                          var new_total = Number(order_recived_amount) + Number(paytm_amount);
                          $('.customer_recived_amount').text(new_total);
                          var html = '<tr class="payment_success">';
                          html += '<td>' + $('#paytm_payment_reff_no').val()+ '</td>';
                          html += '<td class="order_list_comment payment_history_link"> </td>';
                          html += '<td><span class="percentage_amount"> ' + amt_per.toFixed(2) + " % </span> <span> Amt:" + paytm_amount + '</span> </td>';
                          html += '<td>paytm</td>';
                          html += '<td class="text-uppercase">'+ txt_success +'</td>';
                          html += '<td>' + time + '</td>';
                          html += '<td>' + data.user + '</td>';
                          html += '<td class="recived_payment"> New </td>';
                          html += '<tr>';
                          remove_paytm_offline_qr_info();
                          $('.payment_history_' + order_id).each(function(index, element) {
                              $(element).find('tr').last().before(html);
                          });
                          window.location.reload();
                      } else if(data.error == 'error'){
                        alert(data.message);
                      } else {
                        alert('You dont have permission to update Paytm Offline QR. Please contact administrator !');
                      }
                  },
                  failure: function(data) {
                    $('.not_send_payment_link').show();
                  }
              });
          } else {
              $('.paytm_amount').show();
          }
        });

        // Set empty value in Manual Bank Transfer
        function remove_bank_info(){
            $('#payment_reff_no').val('');
            $('#bank_amount').val('');
            $('#payment_date').val('');
        }

        // Set empty value in Paytm  Offline QR
        function remove_paytm_offline_qr_info(){
            $('#paytm_payment_reff_no').val('');
            $('#paytm_amount').val('');
            $('#paytm_payment_date').val('');
        }

        $('.generate_payment_link').click(function() {
            var order_id                = $(this).attr('data-id');
            var order_no                = $(this).attr('data-no');
            var order_total             = $(this).attr('data-total');
            var order_format_total      = $(this).attr('data-format-amount');
            var order_payment_mode      = $(this).attr('data-payment_mode');
            var order_currency_code     = $(this).attr('data-currency_code');
            var order_recived_amount    = $(this).attr('data-recived_amount');
            var order_format_recived_amount = $(this).attr('data-format-recived_amount');
            var order_refund_total      = $(this).attr('data-refund_total');
            var order_total_link_send_amount = $(this).attr('data-total_link_send_amount');
            var customer_id             = $(this).attr('data-customerid');
            var order_tentative_amt     = $('.tentative_advance_payment_history_hidden_'+order_id).attr('data-tentative-amount');
            var customer_vpa            = $(this).attr('data-customer_vpa');
            var order_format_tentative_amt     = $('.tentative_advance_payment_history_hidden_'+order_id).attr('data-format-tentative-amount');
            var cashback_coupon_total_amount = $('.tentative_advance_payment_history_hidden_'+order_id).attr('data-cashback-coupon-total-amount');
            var format_cashback_coupon_total_amount = $('.tentative_advance_payment_history_hidden_'+order_id).attr('data-format-cashback-coupon-total-amount');
            var total_format_balance_amount = $(this).attr('data-balance-format-total-amount');
            var total_balance_amount = $(this).attr('data-balance-total-amount');

            $('#credit_debit').trigger("click");
            remove_bank_info();

            $('.not_send_payment_link').hide();
            $('.refund_payment_links_popup').hide();

            if(order_refund_total != 0){
                $('.refund_total').show();
            }else{
                $('.refund_total').hide();
            }

            $('.payment_order_no').text('Order No: ' + order_no);
            if (order_total > order_recived_amount) {
                var rem_amount_to_send_link = order_total - order_recived_amount;
            } else {
                var rem_amount_to_send_link = 0;
            }
            $('.total_payment_amount').text(order_format_total);

            $('.customer_recived_amount').text(order_format_recived_amount);
            $('.customer_total_refund_send').text(order_refund_total);

            if( typeof(order_tentative_amt) == "undefined" || order_tentative_amt <= 0 ){
              $('.customer_total_tentative_amount').parent().addClass('hidden');  
            }else{
              $('.customer_total_tentative_amount').parent().removeClass('hidden');  
            }
            if( typeof(cashback_coupon_total_amount) == "undefined" || cashback_coupon_total_amount <= 0 ){
              $('.cash_back_coupon_total_amount').parent().addClass('hidden');  
            }else{
              $('.cash_back_coupon_total_amount').parent().removeClass('hidden');  
            }

            $('.customer_total_tentative_amount').text(order_format_tentative_amt);
            $('.cash_back_coupon_total_amount').text(format_cashback_coupon_total_amount);

             if( total_balance_amount < 0 ){
              $('.total_balance_amount').addClass('text-danger').removeClass('text-primary').text(total_format_balance_amount);
            } else {
              $('.total_balance_amount').addClass('text-primary').removeClass('text-danger').text(total_format_balance_amount);
            }
            $('#payment_history_table').html('');
            $('#payment_history_table').html($('#payment_history_' + order_id).html());

            $('#successfully_payment_history_table').html('');
            $('#successfully_payment_history_table').html($('#successfully_payment_history_' + order_id).html());

            $('#tentative_advance_payment_history_table').html('');
            $('#tentative_advance_payment_history_table').html($('#tentative_advance_payment_history_' + order_id).html());

            $('#refund_payment_history_table').html('');
            $('#refund_payment_history_table').html($('#refund_payment_history_' + order_id).html());

            $('.manual_bank_send').attr('data-recived_amount', order_recived_amount);
            $('.paytm_offline_send_btn').attr('data-recived_amount', order_recived_amount);

            $('.payment_link_send').attr('data-id', order_id);
            $('.payment_link_send').attr('data-no', order_no);
            $('.payment_link_send').attr('data-total', order_total);
            $('.payment_link_send').attr('data-total_link_send_amount', order_total_link_send_amount);
            $('.payment_link_send').attr('data-customer_vpa', customer_vpa);

            $('.advance_amount_save').attr('data-id', order_id);
            $('.advance_amount_save').attr('data-no', order_no);

            $('.promo_amount_save').attr('data-id', order_id);
            $('.promo_amount_save').attr('data-no', order_no);
            $('.promo_amount_save').attr('data-customerid', customer_id);
            

            if (order_payment_mode == 'P') {
                var rem_amount_percentage = (rem_amount_to_send_link / order_total) * 100;
                $('.percentage').val((rem_amount_percentage) ? rem_amount_percentage.toFixed(2) : '0.00' );
                $('.amount').val(rem_amount_to_send_link);
                $("#percentage_radio").prop("checked", true);
                $('.percentage').prop("disabled", false);
            } else {
                $('input[name=payment_type]').attr('checked', false);
                $('.percentage').val(10);

                var link_send_amount = Math.ceil((10 * order_total) / 100);
                $('.amount').val(link_send_amount);
                $('.percentage').prop("disabled", true);
            }
            $('.amount').prop("disabled", true);
            if(customer_vpa != "") {
              $('.payment_link_upi_vpa').val(customer_vpa);
              $('.payment_link_upi_vpa').prop("disabled", true);
            }

            if(order_currency_code != 'INR') {
              $('.payment_and_balence_amount_div').addClass('hidden');
            } else {
              if($('.payment_and_balence_amount_div').hasClass('hidden') == true) {
                $('.payment_and_balence_amount_div').removeClass('hidden');
              }
            }

            $('.show_generate_payment_link_popup').show();
        });

        $('.payment_cancel').click(function() {
            $('.show_generate_payment_link_popup').hide();
        });

        $('input[name=\'payment_link_percentage\']').keyup(function() {
            $('.error_check').hide();
            var key_value = $(this).val();
            var total_amount = $('.payment_link_send').attr('data-total');
            if (key_value.match(/^[0-9]+\.?[0-9]*$/)) {
                var link_send_amount = Math.ceil((key_value * total_amount) / 100);
                $('.amount').attr('disabled', false);
                $('.amount').val(link_send_amount);
                $('.amount').attr('disabled', true);
            } else if (key_value == '') {
                var link_send_amount = Math.ceil((10 * total_amount) / 100);
                $('.amount').attr("value", link_send_amount);
            } else {
                $('.error_check').show();
            }
        });

        $('.payment_link').click(function() {
            var val = $(this).val();
            if (val == 'amount') {
                $('.percentage').prop("disabled", true);
                $('.amount').prop("disabled", false);
            } else {
                $('.amount').prop("disabled", true);
                $('.percentage').prop("disabled", false);
            }
        });

        $('.edit_vpa').click(function() {
          $('.payment_link_upi_vpa').prop("disabled", false);
        });

        $('.payment_link_send').click(function() {
            var val = $('input[name=payment_type]:checked').val();
            if (val == 'percentage' || val == 'amount') {
                var order_id = $(this).attr('data-id');
                var order_no = $(this).attr('data-no');
                var order_total = $(this).attr('data-total');
                var total_link_send_amount = $(this).attr('data-total_link_send_amount');
                var customer_vpa = $('.payment_link_upi_vpa').val();
                var percentage = $('.percentage').val();
                var amount = $('.amount').val();
                var payment_link_generation_option = $('#payment_link_generation_for_options').val();

                if (percentage != '' || amount != '') {
                    if (amount > 0) {
                        if((payment_link_generation_option == 'citrus') || (payment_link_generation_option == 'razorpay') || !(customer_vpa.length === 0)) {
                          $.ajax({
                              url: "index.php?route=sale/order/generatePaymentLink&token=<?php echo $token; ?>",
                              type: "post",
                              dataType: "json",
                              data: "order_id=" + order_id + "&order_no=" + order_no + "&percentage=" + percentage + "&amount=" + amount + "&order_total=" + order_total + "&payment=" + payment_link_generation_option + "&customer_vpa=" + customer_vpa,
                              beforeSend: function() {
                                  $('.payment_link_send').button('loading');
                              },
                              complete: function() {
                                  $('.payment_link_send').button('reset');
                              },
                              success: function(data) {
                                  if (data.responseMsg == 'SUCCESS') {
                                      var now = new Date(Date.now());
                                      var current_month = now.getMonth()+1;
                                      var time = now.getFullYear() + "-" + current_month + "-" + now.getDate() + " " + now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds();
                                      var amt_per = (amount / order_total) * 100;
                                      var html = '';
                                      if((payment_link_generation_option == 'citrus' || payment_link_generation_option == 'all') && data.citrus.responseMsg == 'SUCCESS') {

                                          html += '<tr>';
                                          html += '<td></td>';
                                          html += '<td class="order_list_comment payment_history_link"> <a href="'+ data.citrus.specialMsg + '" target="_blank">' + data.citrus.specialMsg + '</a> </td>';
                                          html += '<td><span class="percentage_amount"> ' + amt_per.toFixed(2) + " % </span> <span> Amt:" + amount + '</span> </td>';
                                          html += '<td>citrus</td>';
                                          html += '<td>PENDING</td>';
                                          html += '<td>' + time + '</td>';
                                          html += '<td>' + data.user + '</td>';
                                          html += '<td class="recived_payment"> New </td>';
                                          html += '<tr>';
                                      }
                                      if((payment_link_generation_option == 'razorpay' || payment_link_generation_option == 'all') && data.razorpay.responseMsg == 'SUCCESS') {

                                          html += '<tr>';
                                          html += '<td>'+data.razorpay.razorpay_txn_id+'</td>';
                                          html += '<td class="order_list_comment payment_history_link"> <a href="'+ data.razorpay.specialMsg + '" target="_blank">' + data.razorpay.specialMsg + '</a> </td>';
                                          html += '<td><span class="percentage_amount"> ' + amt_per.toFixed(2) + " % </span> <span> Amt:" + amount + '</span> </td>';
                                          html += '<td>razorpay</td>';
                                          html += '<td>PENDING</td>';
                                          html += '<td>' + time + '</td>';
                                          html += '<td>' + data.user + '</td>';
                                          html += '<td class="recived_payment"> New </td>';
                                          html += '<tr>';
                                      }
                                      if((payment_link_generation_option == 'upi' || payment_link_generation_option == 'both') && data.upi.responseMsg == 'SUCCESS') {

                                          html += '<tr><td>'+data.upi.upi_txn_id+'</td>';
                                          html += '<td class="order_list_comment payment_history_link">' + 'Customer Offline Payment' + '</td>';
                                          html += '<td><span class="percentage_amount"> ' + amt_per.toFixed(2) + " % </span> <span> Amt:" + amount + '</span> </td>';
                                          html += '<td>upi</td>';
                                          html += '<td>PENDING</td>';
                                          html += '<td>' + time + '</td>';
                                          html += '<td>' + data.user + '</td>';
                                          html += '<td class="recived_payment"> New </td>';
                                          html += '<tr>';
                                      }
                                      //alert($('.payment_history_'+order_id).length);
                                      //var ind = $('.payment_history_'+order_id ).find('tr');
                                      $('.payment_history_' + order_id).each(function(index, element) {
                                          $(element).find('tr').last().before(html);
                                      });
                                  }
                              },
                              failure: function(data) {
                                  $('.not_send_payment_link').show();
                              }
                          });
                        } else {
                          alert("Please enter valid customer UPI VPA.");
                        }
                    } else {
                        alert("Amount can not be ZERO(0) or Less than 0. Please Enter a Amount Value ");
                    }
                } else {
                    alert("Please Enter a " + val);
                }
            } else {
                alert("Please Select an options");
            }

        });
    });
</script>
<script type="text/javascript">

    $(document).ready(function(){
        $("select[name=\'collected_by\']").on('change',function(){
            if($(this).val() == 0){
                $('.other_person_name').val('');
                $('.other_person_name').show();
            }else{
                $('.other_person_name').val($(this).find('option:selected').text());
                $('.other_person_name').hide();
            }
        });

        //advance amount update in order_total and insert in order_payment

        $('.advance_amount_save').click(function(){
             var advance             = $('input[name=\'advance_amount\']').val();
             var order_id           = $(this).attr('data-id');
             var order_no           = $(this).attr('data-no');
             var adv_payment_date   = $('.adv_payment_date').val();
             var adv_adtinal_remrks = $('.adv_additional_remarks').val();
             var no_error_in_form   = true;
             sales_person_id = ($("select[name=\'collected_by\']").val());
             if(sales_person_id == 0){
                sales_person_id = $('.other_person_name').val();
             }
             var sales_person_name = $('.other_person_name').val();

            already_exists_amount = $("#exists_amount_"+order_id).attr('value');

             if(advance==''){
                $('.error_advance_amt').text('Advance amount required');
                no_error_in_form = no_error_in_form && false;
             }else if(advance <= 0 ){
                $('.error_advance_amt').text('Enter the advance amount is greater than 0');
                no_error_in_form = no_error_in_form && false;
             }else if(!advance.match(/^\d+$/)){
                $('.error_advance_amt').text('Cash Advance amount cannot be in decimals');
                no_error_in_form = no_error_in_form && false;
             }else{
                $('.error_advance_amt').text('');
             }
             if(adv_payment_date==''){
                $('.error_advance_amt_date').text('Payment date required');
                no_error_in_form = no_error_in_form && false;
             }else{
                $('.error_advance_amt_date').text('');
             }
             if(sales_person_id ==''){
                $('.error_advance_collect_by').text('Collected By person name required');
                no_error_in_form = no_error_in_form && false;
             }else{
                $('.error_advance_collect_by').text('');
             }

              if(already_exists_amount.search(advance)>0){
                if(!confirm('Payment entry of Rs. '+ advance + ' already exists against this order. Are you sure that you are adding a new payment entry ?')){
                  return false;                
                } 
              }

            if (!no_error_in_form)
                return false;

            if (!isNaN(advance)) { // checking that advance value is numeric only
                $.ajax({
                    url: 'index.php?route=sale/order/applyAdvance&token=<?php echo $token; ?>&order_id=' + order_id +'&order_no=' + order_no + '&advance=' + advance + '&adv_payment_date=' + adv_payment_date + '&adv_adtinal_remrks=' + adv_adtinal_remrks + '&sales_person_id=' + sales_person_id + '&sales_person_name='+sales_person_name,
                    dataType: 'json',
                    beforeSend: function(){
                        $('.advance_amount_save').attr('disabled','disabled');
                    },
                    complete: function() {
                        $('#button-invoice').button('reset');
                    },
                    success: function(json) {
                        if (json['error']) {
                            alert(json['error']);
                        }
                        //alert('Rs.'+advance+' advance applied. Final payable amount is now Rs.'+json['payable']);
                        $('input#advance-value').prop('readonly', true);
                        window.location.reload();

                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });

            } else {
                alert("Invalid value. Please enter a valid number for advance collected");
            }
        });


        //Promo code update in order_total and insert in order_payment

         $('.promo_amount_save').click(function(){
             var order_id             = $(this).attr('data-id');
             var order_no             = $(this).attr('data-no');
             var customer_id          = $(this).attr('data-customerid');
             var promo_amount         = $('#promo_amount').val();
             var promo_code           = $('#promo_code').val();
             var promo_code_against   = $('#reasons').val();
             var order_no_against     = $('#order_no_against').val();
             var promo_adtinal_remrks = $('.promo_additional_remarks').val();
             var no_error_in_form     = true;

             if(promo_amount==''){
                $('.error_promo_amount').text('Promo Amount required');
                no_error_in_form = no_error_in_form && false;
             }else if(promo_amount <= 0 ){
                $('.error_promo_amount').text('Enter the Promo Amount is greater than 0');
                no_error_in_form = no_error_in_form && false;
             }else if(!promo_amount.match(/^\d+$/)){
                $('.error_promo_amount').text('Promo Amount cannot be in decimals');
                no_error_in_form = no_error_in_form && false;
             }else{
                $('.error_promo_amount').text('');
             }
             if(promo_code ==''){
                $('.error_promo_code').text('Promo code is required');
                no_error_in_form = no_error_in_form && false;
             }else{
                $('.error_promo_code').text('');
             }
             if(promo_code_against ==''){
                $('.error_reasons').text('Promo Code Against is required');
                no_error_in_form = no_error_in_form && false;
             }else{
                $('.error_reasons').text('');
             }

            if (!no_error_in_form)
                return false;

            if (!isNaN(promo_amount)) { // checking that advance value is numeric only
                $.ajax({
                    url: 'index.php?route=sale/order/applyPromoCodeDiscount&token=<?php echo $token; ?>&order_id=' + order_id +'&order_no=' + order_no +'&customer_id=' + customer_id + '&promo_amount=' + promo_amount + '&promo_code=' + promo_code + '&promo_code_against=' + promo_code_against + '&order_no_against=' + order_no_against + '&promo_adtinal_remrks='+promo_adtinal_remrks,
                    dataType: 'json',
                    beforeSend: function(){
                        $('.promo_amount_save').button('loading');
                    },
                    complete: function() {
                        $('.promo_amount_save').button('reset');
                    },
                    success: function(json) {
                        if (json['error']) {
                            alert(json['error']);
                        }
                        //alert('Rs.'+advance+' advance applied. Final payable amount is now Rs.'+json['payable']);
                        if(json['success']) {
                          window.location.reload();
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });

            } else {
                alert("Invalid value. Please enter a valid number for promo amount.");
            }
        });
    });


$(document).ready(function(){


    $('.button-lazypay-action').click(function(){
          var order_id = $(this).data('orderId');
          var order_no = $(this).data('orderNo');
          var customer_id = $(this).data('customerId');
          var detail = $(this).data('detail');
          var credit_note_id = $(this).data('creditNoteId');
          var total = $(this).data('total');
          var action = $(this).data('action');
          var text = $(this).data('text');
          var modal_body = '<div>'+
            '<input type= "hidden" name="action" value="' +action  +'" />'+
            '<input type= "hidden" name="order_no" value="' +order_no  +'" />'+
            '<input type= "hidden" name="order_id" value="' +order_id  +'" />'+
            '<input type= "hidden" name="customer_id" value="' +customer_id  +'" />'+
            '<input type= "hidden" name="credit_note_id" value="' +credit_note_id  +'" />'+
            '<input type= "hidden" name="detail" value="' +detail  +'" />'+
            '<input type= "hidden" name="total" value="' +total  +'" />'+
            '<label>Order No: &nbsp;</label><span>' + order_no + '</span><br/>'+
            '<label>Amount: &nbsp;</label><span>Rs.&nbsp;' + total + '</span><br/>'+
          '</div>';
          
          $('#confirm_body').html(modal_body);
          if(action == 'refund') {
            $('#submit_to_lazypay').html('Refund Payment Over Lazypay');  
          }else if(action == 'capture') {
            $('#submit_to_lazypay').html('Capture Payment Over LazyPay');
          }else if(action == 'release') {
            $('#submit_to_lazypay').html('Release Payment Over LazyPay');
          }
          
          $('#confirm_popup').modal({
                    backdrop: 'static',
                    keyboard: false
                });
        });
        

        $('.button-view-log').click(function(){
          var order_no = $(this).data('orderNo');
          var url = 'index.php?route=operations/lazypay_order/getLazypayTransactions&token=<?php echo $token; ?>&order_no='+order_no;
          $('#lazypay_logs').attr('src', url);
          $('#lazypay_modal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
        });


    $('set_marked_as_master').hide();
    //show popup
    $('.mark_duplicate').click(function(){

        $('#mark_duplicate_popup').modal('show');
        
        var customerid = $(this).data('customerid');               
        $('#set_marked_as_master').data('ordercustomerid',customerid);

    });
    //get duplicate customer record
    $("body").delegate("#get_duplicate_record", "click", function(){    
        
        $('#set_marked_as_master').hide();

        var customer_name       = $('input[name=\'customer_name\']').val();
        var customer_email      = $('input[name=\'customer_email\']').val();
        var customer_phone      = $('input[name=\'customer_phone\']').val();
        var customer_city       = $('input[name=\'customer_city\']').val();
        var company_name        = $('input[name=\'company_name\']').val();
        var customer_cid        = $('input[name=\'customer_cid\']').val();
        var customer_postcode   = $('input[name=\'customer_postcode\']').val();
        var customer_id          = $('#set_marked_as_master').data('ordercustomerid');
        
        if (customer_name=='' && customer_email=='' && customer_phone=='' && customer_city=='' && company_name=='' && customer_cid=='' && customer_postcode=='') {
            alert('Please fill atleast one value in search form.');
            return false;
        }

        $.ajax({
            url: 'index.php?route=sale/order/getDuplicateCustomers&token=<?php echo $token; ?>&customer_name=' + customer_name +'&customer_email=' + customer_email + '&customer_phone=' + customer_phone + '&customer_city=' + customer_city + '&company_name=' + company_name + '&customer_cid=' + customer_cid + '&customer_postcode=' + customer_postcode + '&customer_id=' + customer_id,
            beforeSend: function(){
                $('#get_record_loading').removeClass('hide');
            },
            complete: function() {
                
                $('#get_record_loading').addClass('hide');  
            },
            success: function(json) {
                if (json['error']) {
                    alert(json['error']);
                }else{
                    $('#duplicate_customer_search_modal').html(''); 
                    if ($.trim(json)!='') {                        
                        $('#duplicate_customer_search_modal').html(json); 
                        $('#set_marked_as_master').show();
                         
                    }else{
                        alert('Record not found, please change your filters');
                    }
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        }); 
    });
    //set master customer id to order customer id
    $("body").delegate("#set_marked_as_master", "click", function(){ 
        
        if ($("input[type=radio]:checked").length > 0) {
            
            var customerid          = $('#set_marked_as_master').data('ordercustomerid');
            var master_customer_id  = $("input[name='master_customer_id']:checked").val();
            
            $.ajax({
                url: 'index.php?route=sale/order/setMasterCustomerId&token=<?php echo $token; ?>&order_customer_id=' + customerid +'&master_customer_id=' + master_customer_id,
                success: function(data) {                   
                   
                    if (data['status']==1) {
                        alert(data['message']); 
                    }
                    if (data['status']==2) {
                        alert(data['permission_error']);                         
                    }
                    if (data['status']==3) {
                        alert(data['message']); 
                    }
                    window.location.reload();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            }); 
        }else{
            alert('Please select atleast one option in customer list.');
        }
    });

    $('.button_shipping_postcode').click(function(){
      var order_id = $(this).data('order-id');
      var shipping_postcode = $(this).data('shipping-postcode');
      var payment_code      = $(this).data('payment-code');
      var shipping_zone_id  = $(this).data('shipping-zone-id');
      $.ajax({
        url: 'index.php?route=sale/order/orderInfoLabelCourierAdvisory&token=<?php echo $token; ?>',
        type:'POST',
        data:{'shipping_postcode':shipping_postcode, 'payment_code':payment_code, 'shipping_zone_id':shipping_zone_id},
        //dataType: 'json',
        beforeSend: function(){ },
        complete: function(){ },
        success: function(json) {
          $('.courier_advisery_popup').html(json);
          $('.courier_advisery_popup').removeClass('hidden');
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    }); 
});
</script>
<style type="text/css">
    .suborder_table table tr td {
        padding: 4px !important;
    }
    .order_list_comment{
        word-wrap: break-word;
    }
    .payment_history_block_sm{
      display: block;
    }
    #draggableheader {        
        cursor: crosshair;
    }
</style>
<script>
//Make the DIV element draggagle:
dragElement(document.getElementById("draggable"));

function dragElement(elmnt) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  if (document.getElementById(elmnt.id + "header")) {
    /* if present, the header is where you move the DIV from:*/
    document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
  } else {
    /* otherwise, move the DIV from anywhere inside the DIV:*/
    elmnt.onmousedown = dragMouseDown;
  }

  function dragMouseDown(e) {
    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e = e || window.event;
    e.preventDefault();
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    // set the element's new position:
    elmnt.style.top = (elmnt.offsetTop <= 50 || elmnt.offsetTop >= 800) ? '60px' : ((elmnt.offsetTop - pos2) + "px");
    elmnt.style.left = (elmnt.offsetLeft <= -600 || elmnt.offsetLeft >= 1150) ? '50px': ((elmnt.offsetLeft - pos1) + "px");
  }

  function closeDragElement() {
    /* stop moving when mouse button is released:*/
    document.onmouseup = null;
    document.onmousemove = null;
  }
}
  function view_order_detail(id){
    $('#'+id).toggle();
  }
  function getPreviousOrderDate(order_id, master_id, order_date){
    $('#click_to_see_'+order_id).hide();
    $.ajax({
      url: 'index.php?route=sale/order/getOrderPreviousDate&token=<?php echo $token; ?>',
      type:'POST',
      data:{'order_id':order_id, 'master_id':master_id, 'order_date':order_date},
      success: function(json) {
        json = json.trim();
        $('#previous_order_date_'+order_id).html('PO-['+json+']' );
        $('#previous_order_date_'+order_id).css('display', 'block');
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  }
</script>
