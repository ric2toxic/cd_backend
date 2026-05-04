<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>
                <?php echo $heading_pending_orders; ?>
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
                    <?php echo $text_pending_orders_list; ?>
                </h3>
            </div>
            <div class="panel-body">
                <div class="well">
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
                                <label class="control-label" for="input-customer"><?php echo $entry_total; ?></label> <br>
                                <input type="text" name="filter_low" value="<?php echo $filter_low; ?>" placeholder="<?php echo $entry_total_low; ?>" id="input-total-low" class="form-control filter_total" />
                                <input type="text" name="filter_high" value="<?php echo $filter_high; ?>" placeholder="<?php echo $entry_total_high; ?>" id="input-total-high" class="form-control filter_total" />
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label class="control-label" for="input-company"><?php echo $entry_company; ?></label>
                                <input type="text" name="filter_company" value="<?php echo $filter_company; ?>" placeholder="<?php echo $entry_company; ?>" id="input-company" class="form-control" />
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
                                <label class="control-label" for="input-city"><?php echo $entry_city; ?></label>
                                <input type="text" name="filter_city" value="<?php echo $filter_city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control" />
                            </div>
                            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <!--<td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td> -->
                                <td class="text-center">
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
                                <td class="text-center">
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
                                <td class="text-center">
                                    <?php if ($sort == 'o.shipping_company') { ?>
                                    <a href="<?php echo $sort_company; ?>" class="<?php echo strtolower($order); ?>">
                                        <?php echo $column_company; ?>
                                    </a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_company; ?>">
                                        <?php echo $column_company; ?>
                                    </a>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($sort == 'o.shipping_city') { ?>
                                    <a href="<?php echo $sort_city; ?>" class="<?php echo strtolower($order); ?>">
                                        <?php echo $column_city; ?>
                                    </a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_city; ?>">
                                        <?php echo $column_city; ?>
                                    </a>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($sort == 'o.total') { ?>
                                    <a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>">
                                        <?php echo $column_total_bill; ?>
                                    </a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_total; ?>">
                                        <?php echo $column_total_bill; ?>
                                    </a>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($sort == 'o.date_added') { ?>
                                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>">
                                        Order Date
                                    </a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_date_added; ?>">
                                        Order Date
                                    </a>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    Payment Action
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders) { ?>
                            <?php foreach ($orders as $order) { ?>
                                <?php
                                    $order_info = $order['order'];
                                   
                                ?>
                            <tr>
                                <td class="text-center">
                                        <strong>
                                        <!--<?php if($order_info['sales_staff_id'] == 0 OR $order_info['sales_staff_id']=='null'){ ?>
                                          <sup class="order_unassigned_sales">*</sup>
                                        <?php }   ?> -->
                                        
                                        <?php echo $order_info['order_no']; ?>

                                      </strong> <br>
                                    <?php echo $order_info['store_name']; ?> 
                                  </td>
                                  <td class="text-center">
                                    <?php echo $order_info['customer']; ?>
                                    <br/>
                                    cid: <strong>[<?php echo $order_info['customer_id']; ?>]</strong>
                                    <br/>
                                    <span style="color: #000;">
                                        <span class="click_to_see btn-primary btn-xs" data-field-type="telephone" data-field-value="<?php echo base64_encode($order_info['telephone']); ?>" onClick="clickToSee(this)"> Click to see </span>
                                    </span>
                                    <br/>
                                  </td>
                                  <td class="text-center">
                                    <strong><?php echo $order_info['shipping_company']; ?></strong> <br>
                                    <div class="order_list_comment">
                                        <?php echo $order_info['comment']; ?>
                                    </div>
                                 </td> 
                                <td class="text-center"><strong><?php echo $order_info['shipping_city']; ?></strong>
                                    <br>
                                    <div class="order_list_comment">
                                    </div>
                                </td>
                                
                                <td class="text-center">
                                    <?php echo $order_info['total']; ?> </td>
                                <td class="text-center">
                                    <?php echo $order_info['date_added']; ?>
                                </td>
                                <td class="text-center">
                                    <strong><?php echo $order_info['payment_mode']; ?> </strong></br></br>
                                    <button class="btn btn-warning fa fa-money generate_payment_link"
                                            data-id="<?php echo $order_info['order_id']; ?>"
                                            data-no="<?php echo $order_info['order_no']; ?>"
                                            data-currency_code="<?php echo $order_info['currency_code']; ?>"
                                            data-total="<?php echo $order_info['total_amount']; ?>"
                                            data-recived_amount="<?php echo $order_info['payment_history']['payed_by_customer']; ?>"
                                            data-refund_total="<?php echo $order_info['payment_history']['total_refund_amt']; ?>"
                                            data-payment_mode="<?php echo $order_info['payment_mode']; ?>"
                                            data-total_link_send_amount="<?php echo $order_info['payment_history']['total_link_send_amount']; ?>"
                                            title="<?php echo $button_generate_payment_link; ?>"
                                            type="button"
                                            data-toggle="dropdown">
                                    </button>
                                    <?php if(!empty($order['show_return_btn'])){?>
                                    <a href="<?php echo $order['order_return_url']; ?>" id="button-return<?php echo $order['order_id']; ?>" data-toggle="tooltip" title="Return" class="btn btn-danger"><i class="fa fa-reply"></i></a>
                                    <?php } ?>
                                  </td>
                              </tr>

                              <tr>
                                <td style="display: none;" id="payment_history_<?php echo $order_info['order_id']; ?>">
                                  <table  class="table table-bordered payment_history_<?php echo $order_info['order_id']; ?>">
                                    <thead>
                                      <tr>
                                        <td class="text-center"> Merchant txn id </td>
                                        <td class="text-center"> Payment Link </td>
                                        <td class="text-center"> Amount </td>
                                        <td class="text-center"> Payment Gateway </td>
                                        <td class="text-center"> Status </td>
                                        <td class="text-center"> Date Added </td>
                                        <td class="text-center"> Updated/Requested By </td>
                                        <td class="text-center"> Action </td>
                                      </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 0; foreach($order_info['payment_history']['result'] as $payment_history) { $i++; ?>
                                      <tr <?php if($payment_history['successfull'] == 1 && $payment_history['amount'] > 0 && $payment_history['payment_gateway'] != 'cash') { ?>
                                                class="payment_success"
                                            <?php } else{ ?>
                                                class="<?php echo 'payment_'.$payment_history['payment_gateway'];?>"
                                            <?php } ?> >
                                            <td class="text-center"><?php echo $payment_history['merchant_txn_id']; ?></td>
                                            <td class="text-center order_list_comment payment_history_link"><a href="<?php echo $payment_history['payment_link']; ?>" target="_blank"> <?php echo $payment_history['payment_link']; ?> </a></td>
                                            <td class="text-center"> <span class="percentage_amount"> <?php
                                            $payment_history['amount_percentage'] = ($payment_history['amount']/$order_info['total_amount'])*100;
                                            echo sprintf('%0.2f', $payment_history['amount_percentage']); ?> % </span> <span> Amt: <?php echo $payment_history['amount']; ?></span></td>
                                            <td class="text-center"><?php echo $payment_history['payment_gateway']; ?></td>
                                            <td class="text-center"><?php echo $payment_history['txn_status']; ?></td>
                                            <td class="text-center"><?php echo $payment_history['date_added']; ?></td>
                                            <td class="text-center">
                                                    <?php
                                                    if(empty($payment_history['user'])){ ?>
                                                        During Order Placement
                                                    <?php } else {
                                                        echo $payment_history['user'];
                                                    } ?>
                                            </td>
                                            <?php if($payment_history['successfull'] == 1 && $payment_history['payment_gateway'] == 'citrus' && $order_info['payment_history']['payed_by_customer'] >= $order_info['total_amount'] && $payment_history['amount'] < 0){ ?>
                                                    <!--<td class="text-center"> <button class="btn btn-danger fa fa-reply refund_payment_links_<?php echo $payment_history['order_no']; ?>" onclick="refund_payment_links(this)" data-id="<?php echo $order_info['order_id']; ?>" data-merchant_txn_id="<?php echo $payment_history['merchant_txn_id']; ?>" data-refund_total="<?php echo $order_info['payment_history']['total_refund_amt']; ?>" data-total="<?php echo $payment_history['amount']; ?>" data-no="<?php echo $payment_history['order_no']; ?>" title="Refund" type="button" data-toggle="dropdown"> </td>
                                                    -->
                                                    <td class="text-center"></td>
                                            <?php } elseif($payment_history['payment_gateway'] == 'cash') { ?>
                                            <td class="text-center">
                                                <!--
                                                <button class="btn btn-info fa fa-reply update_payment_links_<?php echo $payment_history['order_no']; ?>" data-id="<?php echo $order_info['order_id']; ?>" data-merchant_txn_id="<?php echo $payment_history['merchant_txn_id']; ?>" data-total="<?php echo $payment_history['amount']; ?>" data-no="<?php echo $payment_history['order_no']; ?>" title="Update" type="button" data-toggle="dropdown">
                                                -->
                                                <a class="btn btn-info fa fa-reply" href="<?php echo $update_cash_order_payment;?>" target="_blank">
                                            </td>
                                        <?php } else { ?>
                                            <td class="text-center"></td>
                                        <?php } ?>
                                      </tr>
                                    <?php } ?>
                                      <tr>
                                        <?php /* if($i != 0) { ?>
                                          <td>Total</td>
                                          <td><span class="percentage_amount"> Per: <?php
                                        $total_link_send_amount = ($order_info['payment_history']['total_link_send_amount']/$order_info['total_amount'])*100;
                                        echo sprintf('%0.2f', $total_link_send_amount); ?> </span> <span>Amt: <?php echo $order_info['payment_history']['total_link_send_amount']; ?> </span></td>
                                        <?php } */ ?>
                                      </tr>
                                    </tbody>
                                  </table>
                                <td>
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

    <div class="show_generate_payment_link_popup display_none">
        <div class="panel-heading order_payment_popup_heading">
            <h3 class="payment_order_no"></h3>
            <div class="apply_credit_note payment_cancel payment_cross"><i class="fa fa-times" aria-hidden="true"></i></div>
        </div>
        <div class="panel-heading order_payment_popup_body">
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-prepaid" id="credit_debit" data-toggle="tab">Credit Card / Debit Card / Net Banking </a></li>
                    <li><a href="#tab-manual_bank_transfer" data-toggle="tab">Manual Bank Transfer</a></li>
                    <li ><a href="#tab-cash-advance" data-toggle="tab">Cash Advance</a></li>
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
                        <div class="col-sm-6">
                            <div class="form-group required">
                                <label class="control-label text-left" for="payment_reff_no"><?php echo $text_payment_reff_no; ?></label>
                                <input type="text" name="payment_reff_no" value="" placeholder="<?php echo $entry_payment_reff_no; ?>" id="payment_reff_no" class="form-control" />
                                <span class="error_bank payment_reff_no"><?php echo $error_payment_reff_no; ?></span>
                            </div>
                            <div class="form-group required">
                                <label class="control-label text-left" for="bank_name"><?php echo $text_bank_name; ?></label>
                                <select name="bank_name" id="bank_name" class="form-control">
                                    <option selected="selected" value="ICICI">ICICI</option>
                                    <option value="Standard Chartered">Standard Chartered</option>
                                </select>
                                <span class="error_bank bank_name"><?php echo $error_bank_name; ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group required">
                                <label class="control-label text-left" for="bank_amount "><?php echo $text_bank_total_amount; ?></label>
                                <input type="text" name="bank_amount" value="" placeholder="<?php echo $entry_bank_total_amount; ?>" id="bank_amount" class="form-control" />
                                <span class="error_bank bank_amount"><?php echo $error_bank_amount; ?></span>
                            </div>
                            <div class="form-group required">
                                <label class="control-label text-left" for="payment_date"><?php echo $text_payment_date; ?></label>
                                <div class="input-group payment_history_date">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                    <input type="text" name="payment_date" value="" placeholder="<?php echo $entry_payment_date; ?>" id="payment_date" class="form-control" />
                                </div>
                                <span class="error_bank payment_date"><?php echo $error_payment_date; ?></span>
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
                                        <?php foreach($sales_staff_list as $sales_staff){ ?>
                                            <option value="<?php echo $sales_staff;?>"><?php echo $sales_staff;?></option>
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
                </div>
            </div>
            <div class="form-group col-sm-12">
                <hr>
                <div class="col-sm-12">
                    <label class="col-sm-4 pull-left"><span class="remaining_pay_amount recived_payment"> Total Amount : </span><span class="total_payment_amount"></span>/-</label>
                    <label class="col-sm-4 pull-left"><h3 class="text-center"> Payment History </h3></label>
                    <label class="col-sm-4 pull-right">
                        <span class="recived_amount">
                            <span> Total Payment Recived: </span> <span class="customer_recived_amount"> </span>/-<br>
                        </span>
                        <span class="refund_total">
                            <span> Total Refund Send: </span> <span class="customer_total_refund_send"> </span>/-<br>
                        </span>
                    </label>
                </div>
                <div id="payment_history_table">
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

<script type="text/javascript">
    <!--
    $('#button-filter').on('click', function() {
        url = 'index.php?route=accounts/pendingorder&token=<?php echo $token; ?>';

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

        var filter_low = $('input[name=\'filter_low\']').val();

        if (filter_low) {
          url += '&filter_low=' + encodeURIComponent(filter_low);
        }

        var filter_high = $('input[name=\'filter_high\']').val();

        if (filter_high) {
          url += '&filter_high=' + encodeURIComponent(filter_high);
        }

        var filter_pickup_city_code = $('input[name=\'filter_pickup_city_code\']').val();

        if (filter_pickup_city_code) {
        url += '&filter_pickup_city_code=' + encodeURIComponent(filter_pickup_city_code);
        }

        location = url;
    });
    //-->
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
  $('input[name=\'filter_order_no\'], input[name=\'filter_date_added\'], input[name=\'filter_customer\'], input[name=\'filter_company\'], input[name=\'filter_city\'], input[name=\'filter_total_low\'], input[name=\'filter_total_high\'],input[name=\'filter_pickup_city_code\'], select[name=\'filter_order_status\']').on('keypress',function(e){
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

            if (bank_amount.match(/^[0-9]+\.?[0-9]*$/)) {
                $.ajax({
                    url: "index.php?route=sale/order/manualBankTransfer&token=<?php echo $token; ?>",
                    type: "post",
                    dataType: "json",
                    data: "order_id=" + order_id + "&order_no=" + order_no + "&order_total=" + order_total + '&payment_reff_no=' + payment_reff_no + '&bank_name=' + bank_name + '&bank_amount=' + bank_amount + '&payment_date=' + payment_date,
                    beforeSend: function() {
                        $('.manual_bank_send').button('loading');
                    },
                    complete: function() {
                        $('.manual_bank_send').button('reset');
                    },
                    success: function(data) {
                        if (data.success == 'success') {
                            var now = new Date(Date.now());
                            var time = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate() + " " + now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds();
                            var amt_per = (bank_amount / order_total) * 100;
                            var new_total = Number(order_recived_amount) + Number(bank_amount);
                            $('.customer_recived_amount').text(new_total);
                            var html = '<tr class="payment_success">';
                            html += '<td></td>';
                            html += '<td class="order_list_comment payment_history_link"> <a href="'+ bank_name + '" target="_blank">'+ bank_name+ ' - ' + payment_reff_no + '</a> </td>';
                            html += '<td><span class="percentage_amount"> ' + amt_per.toFixed(2) + " % </span> <span> Amt:" + bank_amount + '</span> </td>';
                            html += '<td>bank_transfer</td>';
                            html += '<td>SUCCESS</td>';
                            html += '<td>' + time + '</td>';
                            html += '<td>' + data.user + '</td>';
                            html += '<td class="recived_payment"> New </td>';
                            html += '<tr>';
                            remove_bank_info();
                            $('.payment_history_' + order_id).each(function(index, element) {
                                $(element).find('tr').last().before(html);
                            });
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

        // Set empty value in Manual Bank Transfer
        function remove_bank_info(){
            $('#payment_reff_no').val('');
            $('#bank_amount').val('');
            $('#payment_date').val('');
        }

        $('.generate_payment_link').click(function() {
            var order_id                = $(this).attr('data-id');
            var order_no                = $(this).attr('data-no');
            var order_total             = $(this).attr('data-total');
            var order_payment_mode      = $(this).attr('data-payment_mode');
            var order_currency_code     = $(this).attr('data-currency_code');
            var order_recived_amount    = $(this).attr('data-recived_amount');
            var order_refund_total      = $(this).attr('data-refund_total');
            var order_total_link_send_amount = $(this).attr('data-total_link_send_amount');

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
            $('.total_payment_amount').text(order_total);

            $('.customer_recived_amount').text(order_recived_amount);
            $('.customer_total_refund_send').text(order_refund_total);
            $('#payment_history_table').html('');
            $('#payment_history_table').html($('#payment_history_' + order_id).html());

            $('.payment_link_send').prop("disabled", false);
            if (order_currency_code != 'INR' || order_total <= order_recived_amount) {
                $('.payment_link_send').prop("disabled", true);
            }
            $('.manual_bank_send').attr('data-recived_amount', order_recived_amount);

            $('.payment_link_send').attr('data-id', order_id);
            $('.payment_link_send').attr('data-no', order_no);
            $('.payment_link_send').attr('data-total', order_total);
            $('.payment_link_send').attr('data-total_link_send_amount', order_total_link_send_amount);

            $('.advance_amount_save').attr('data-id', order_id);
            $('.advance_amount_save').attr('data-no', order_no);

            if (order_payment_mode == 'P') {
                var rem_amount_percentage = (rem_amount_to_send_link / order_total) * 100;
                $('.percentage').val(rem_amount_percentage.toFixed(2));
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

        $('.payment_link_send').click(function() {
            var val = $('input[name=payment_type]:checked').val();
            if (val == 'percentage' || val == 'amount') {
                var order_id = $(this).attr('data-id');
                var order_no = $(this).attr('data-no');
                var order_total = $(this).attr('data-total');
                var total_link_send_amount = $(this).attr('data-total_link_send_amount');

                var percentage = $('.percentage').val();
                var amount = $('.amount').val();

                if (percentage != '' || amount != '') {
                    if (amount > 0) {
                        $.ajax({
                            url: "index.php?route=sale/order/generatePaymentLink&token=<?php echo $token; ?>",
                            type: "post",
                            dataType: "json",
                            data: "order_id=" + order_id + "&order_no=" + order_no + "&percentage=" + percentage + "&amount=" + amount + "&order_total=" + order_total,
                            beforeSend: function() {
                                $('.payment_link_send').button('loading');
                            },
                            complete: function() {
                                $('.payment_link_send').button('reset');
                            },
                            success: function(data) {
                                if (data.responseMsg == 'SUCCESS') {
                                    var now = new Date(Date.now());
                                    var time = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate() + " " + now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds();
                                    var amt_per = (amount / order_total) * 100;
                                    var html = '<tr>';
                                    html += '<td></td>';
                                    html += '<td class="order_list_comment payment_history_link"> <a href="'+ data.specialMsg + '" target="_blank">' + data.specialMsg + '</a> </td>';
                                    html += '<td><span class="percentage_amount"> ' + amt_per.toFixed(2) + " % </span> <span> Amt:" + amount + '</span> </td>';
                                    html += '<td>citrus</td>';
                                    html += '<td>pending</td>';
                                    html += '<td>' + time + '</td>';
                                    html += '<td>' + data.user + '</td>';
                                    html += '<td class="recived_payment"> New </td>';
                                    html += '<tr>';
                                    //alert($('.payment_history_'+order_id).length);
                                    //var ind = $('.payment_history_'+order_id ).find('tr');
                                    $('.payment_history_' + order_id).each(function(index, element) {
                                        $(element).find('tr').last().before(html);
                                    });
                                } else {
                                    alert('You dont have permission to send payment link. Please contact Administrator !');
                                }
                            },
                            failure: function(data) {
                                $('.not_send_payment_link').show();
                            }
                        });
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
            if($(this).val() == '--None--'){
                $('.other_person_name').show();
            }else{
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
             sales_person_name = ($("select[name=\'collected_by\']").val());
             if(sales_person_name == '--None--'){
                sales_person_name = $('.other_person_name').val();
             }

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
             if(sales_person_name ==''){
                $('.error_advance_collect_by').text('Collected By person name required');
                no_error_in_form = no_error_in_form && false;
             }else{
                $('.error_advance_collect_by').text('');
             }

            if (!no_error_in_form)
                return false;

            if (!isNaN(advance)) { // checking that advance value is numeric only
                $.ajax({
                    url: 'index.php?route=sale/order/applyAdvance&token=<?php echo $token; ?>&order_id=' + order_id +'&order_no=' + order_no + '&advance=' + advance + '&adv_payment_date=' + adv_payment_date + '&adv_adtinal_remrks=' + adv_adtinal_remrks + '&sales_person_name=' + sales_person_name,
                    dataType: 'json',
                    complete: function() {
                        $('#button-invoice').button('reset');
                    },
                    success: function(json) {
                        if (json['error']) {
                            alert(json['error']);
                        }

                        if (json['payable']) {
                            //alert('Rs.'+advance+' advance applied. Final payable amount is now Rs.'+json['payable']);
                            $('input#advance-value').prop('readonly', true);
                            window.location.reload();
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });

            } else {
                alert("Invalid value. Please enter a valid number for advance collected");
            }
        });
    });



</script>
<style type="text/css">
    .suborder_table table tr td {
        padding: 4px !important;
    }
</style>
