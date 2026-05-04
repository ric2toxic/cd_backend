<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
    <!-- RBL action(disbursal or cancelled request etc) model -->
    <div id="confirm_popup" class="modal fade" role="dialog">
        <div class="modal-dialog" style="top:20%;width:350px;z-index: 1050;">
            <div class="modal-content alert alert-info">
                <div class="modal-body" style="min-height: 50px;font-size: 18px;">
                  <form action="<?php echo $rbl_action_url; ?>" method="post">
                    <div style="display:block;">
                      <label>Please verify below details</label>                      
                    </div>
                    <hr />
                    <div id="confirm_body" style="display:block;">
                    </div>
                    <hr />
                    <div style="display:block;">
                        <button type="button" data-dismiss="modal" class="btn deliver_btn" style="margin-left: 10px;">Cancel</button>
                        <button type="submit" class="btn btn-info" id="submit_to_rbl" style="margin-left: 10px;">Submit</button>
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
                                <input type="text" name="filter_order_no" value="<?php echo $filter_order_no ?? ''; ?>" placeholder="<?php echo $entry_order_no; ?>" id="input-order-no" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                                <select name="filter_order_status" id="input-order-status" class="form-control">
                                  <option value="*"></option>
                                  <?php if (isset($filter_order_status) && $filter_order_status == '0') { ?>
                                  <option value="0" selected="selected"><?php echo $text_missing; ?></option>
                                  <?php } else { ?>
                                  <option value="0"><?php echo $text_missing; ?></option>
                                  <?php } ?>
                                  <?php foreach ($order_statuses as $order_status) { ?>
                                  <?php if (isset($filter_order_status) && $order_status['order_status_id'] == $filter_order_status) { ?>
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
                                    <input type="text" name="filter_date_added" value="<?php echo $filter_date_added ?? ''; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
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
                                <td class="text-left">
                                    <?php echo $column_order_no; ?>
                                </td>
                                <td class="text-left">
                                    <?php echo $column_customer; ?>
                                </td>
                                <td class="text-left">
                                    <?php echo $column_company; ?>
                                    <hr class="btn-success">
                                    <?php echo $column_city; ?>
                                </td>
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
                                    Order Date
                                    <hr class="btn-success">
                                    Processing Date
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders) { 
                                foreach ($orders as $order) { 
                                    
                                    $order_info = $order['order'];
                                    $suborder_info = $order['suborder'];
                                    $disbursal_status_data = $order['disbursal_status_data'];
                                    $show_disbursal_button = $order['show_disbursal_button'];
                                    $is_disbursal    = $order['is_disbursal'];
                                    $is_disbursed    = $order['is_disbursed'];
                                    $total_amount    = $order_info['total_amount'];
                                    $net_payment     = $order_info['net_payble_amount'];
                                    $order_punch_amount = $order_info['order_punch_amount'];
                                ?>
                            <tr>
                                <td class="text-left" width="10%">
                                    <strong>
                                      <?php echo $order_info['order_no'];?>
                                    </strong> 
                                  </td>
                                  <td class="text-left">
                                  <div style="display: block;">
                                      <?php echo ucfirst($order_info['customer']);?>
                                  </div>
                                  </td>
                                  <td class="text-left">
                                  <div class="order_list_comment"><strong><?php echo $order_info['shipping_company']; ?></strong> <br></div>
                                    <div class="order_list_comment">
                                        <?php echo $order_info['comment']; ?>
                                    </div>
                                    <hr class="btn-success">
                                    <strong><?php echo $order_info['shipping_city']; ?></strong>
                               </td>
                               
                                <td class="text-left suborder_table">
                                    <table class="table table-bordered">
                                      <?php if( !empty( $suborder_info ) ) { 
                                        $order_amount = 0;
                                        foreach($suborder_info as $key_suborder_id => $suborder_data){ 
                                          $order_amount += (float)$suborder_data['suborder_total'];
                                        ?>
                                            <tr>
                                              <td class="text-center">
                                                <?php echo $key_suborder_id; ?>
                                                <hr class="btn-success">
                                                <?php echo $suborder_data['suborder_total']; ?>
                                                <?php if(!empty($suborder_data['detail_invoice'])) {?>  
                                                  <a href="<?php echo $suborder_data['detail_invoice']; ?>" data-toggle="tooltip" title="Download details invoice" class="" style="font-size:25px; padding-left: 10px;"><i class="fa fa-arrow-circle-o-down"></i></a>
                                                <?php } ?>
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
                                                <i class="fa fa-check-circle text-success" data-toggle="tooltip" title="Order Delivered">
                                                </i>
                                                <?php } ?>
                                                </strong><br>
                                                <div>
                                                  <?php echo $suborder_data['delivery_date'] ; ?>
                                                </div><br>
                                              </td>
                                            </tr>
                                            <?php } ?>

                                        <?php } ?>
                                  </table>
                                  
                                  <?php if(!empty($show_disbursal_button) && empty($order_info['order_cancellation_status'])) {?>
                                        <div style="display:block; padding: 5px; ">
                                              <button 
                                                type="button"
                                                data-toggle="tooltip"
                                                title="To RBL"
                                                class="btn btn-sm btn-info button-rbl-action"
                                                data-action="disbursal_request" 
                                                data-order-no="<?php echo $order_info['order_no']; ?>" 
                                                data-order-id="<?php echo $order_info['order_id']; ?>" 
                                                data-customer-id="<?php echo $order_info['customer_id'] ?>" 
                                                data-customer-name="<?php echo $order_info['firstname'].' '.$order_info['lastname']; ?>" 
                                                data-customer-company="<?php echo $order_info['shipping_company']; ?>" 
                                                data-total="<?php echo $net_payment ;?>" 
                                                data-order-punch-amount="<?php echo $order_punch_amount ;?>" 
                                                data-order-limit="<?php echo RBL_ORDER_LIMIT ;?>"
                                                data-text="" 
                                              >
                                              Send Disbursal Request to RBL
                                            </button>

                                            <?php if($order_info['total_amount'] < RBL_ORDER_LIMIT ) { ?>
                                                <div style="display:block; padding: 5px; ">
                                                    <button 
                                                      type="button"
                                                      data-toggle="tooltip"
                                                      title="To RBL"
                                                      class="btn btn-sm btn-info button-rbl-cancel-action"
                                                      data-action="cancel_request"
                                                      data-date-added = "<?php echo $order_info['date_added']; ?>" 
                                                      data-order-no="<?php echo $order_info['order_no']; ?>" 
                                                      data-order-id="<?php echo $order_info['order_id']; ?>" 
                                                      data-customer-id="<?php echo $order_info['customer_id'] ?>" 
                                                      data-total="<?php echo $order_punch_amount ;?>" 
                                                    >
                                                    Send Order Cancel Request to RBL
                                                  </button>
                                            </div> 

                                            <?php } ?>

                                      </div> 

                                  <?php } ?>


                                  <?php if(!empty($order_info['all_suborder_cancelled']) && empty($order_info['order_cancellation_status'])) { ?>
                                            <div style="display:block; padding: 5px; ">
                                                    <button 
                                                      type="button"
                                                      data-toggle="tooltip"
                                                      title="To RBL"
                                                      class="btn btn-sm btn-info button-rbl-cancel-action"
                                                      data-action="cancel_request"
                                                      data-date-added = "<?php echo $order_info['date_added']; ?>" 
                                                      data-order-no="<?php echo $order_info['order_no']; ?>" 
                                                      data-order-id="<?php echo $order_info['order_id']; ?>" 
                                                      data-customer-id="<?php echo $order_info['customer_id'] ?>" 
                                                      data-total="<?php echo $order_punch_amount ;?>" 
                                                    >
                                                    Send Order Cancel Request to RBL
                                                  </button>
                                            </div> 
                                  <?php } ?>

                                  <?php if( !empty($order_info['order_cancellation_status'])) { ?>
                                        <p><i>Order cancellation Request sent to RBL</i></p>
                                  <?php } ?>

                                  <?php if( !empty($is_disbursal)) { ?>
                                        <p><i>Disbursal Request sent to RBL</i></p>
                                  <?php } ?>

                                  <?php if( !empty($is_disbursed)) { ?>
                                        <p><i>Order amount disbursed from RBL for this order</i></p>
                                  <?php } ?>

                                  </td> 

                                <td class="text-right item">    
                                  <div class="order-amount">
                                    <h6>
                                      <strong class="text-info">
                                        <span>OT: </span>
                                        <span><?php echo $order_info['total']; ?></span>
                                      </strong>
                                    </h6>
                                  </div>
                                  </td>
                                <td class="text-left">
                                    <?php echo $order_info['date_added']; ?>
                                    <!-- order processing date -->
                                    <hr class="btn-success">
                                    <?php if(!empty($order_info['order_processing_date'])){ echo $order_info['order_processing_date'] ; } ?>
                                    <!-- END  -->
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
                  <div class="col-sm-12"><?php echo $pagination; ?></div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<!-- NILESH SCRIPT-->
<script type="text/javascript">
    
    $(document).ready(function(){
        $('.well').hide();
        $('#advance_filter').click(function(){
            $('.well').toggle();
        });
    });
</script>

<script type="text/javascript">
    
    $('#button-filter').on('click', function() {

        if ($('#filter_franchise_tab').length > 0) {
            url = 'index.php?route=operations/rbl_order&filter_franchise_tab=1&token=<?php echo $token; ?>';
        } else {
            url = 'index.php?route=operations/rbl_order&token=<?php echo $token; ?>';
        }

        var filter_order_no = $('input[name=\'filter_order_no\']').val();

        if (filter_order_no) {
            url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
        }

        var filter_customer = $('input[name=\'filter_customer\']').val();

        if (filter_customer) {
            url += '&filter_customer=' + encodeURIComponent(filter_customer);
        }

        var filter_order_status = $('select[name=\'filter_order_status\']').val();

        if (filter_order_status != '*') {
            url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
        }

        var filter_date_added = $('input[name=\'filter_date_added\']').val();

        if (filter_date_added) {
            url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
        }

        location = url;
    });
    
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

<style>
    .filter_total {
        width: 50% !important;
        float: left;
    }
</style>
    
<script type="text/javascript">
$(document).ready(function(){


    $('.button-rbl-action').click(function(){
          var order_id = $(this).data('orderId');
          var order_no = $(this).data('orderNo');
          var customer_id = $(this).data('customerId');
          var customer_name = $(this).data('customerName');
          var customer_company = $(this).data('customerCompany');
          var detail = $(this).data('detail');
          var total = $(this).data('total');
          var order_limit = $(this).data('orderLimit');
          var order_punch_amount = $(this).data('orderPunchAmount');
          var action = $(this).data('action');
          var text = $(this).data('text');
          var modal_body = '<div>'+
            '<input type= "hidden" name="action" value="' +action  +'" />'+
            '<input type= "hidden" name="order_no" value="' +order_no  +'" />'+
            '<input type= "hidden" name="order_id" value="' +order_id  +'" />'+
            '<input type= "hidden" name="customer_id" value="' +customer_id  +'" />'+
            '<input type= "hidden" name="customer_name" value="' +customer_name  +'" />'+
            '<input type= "hidden" name="customer_company" value="' +customer_company  +'" />'+
            '<input type= "hidden" name="total" value="' +total  +'" />'+
            '<input type= "hidden" name="order_punch_amount" value="' +order_punch_amount  +'" />'+
            '<label>Order ID: &nbsp;</label><span>' + order_id + '</span><br/>'+
            '<label>Disbursal Amount: &nbsp;</label><span>Rs.&nbsp;' + total.toFixed(2) + '</span><br/>'+
          '</div>';

          if(total > order_limit) {

            $('#submit_to_rbl').html('Send Disbursal Request To RBL');

          } else { 

            modal_body += '<br><div style="color:red;"><i>Invalid order amount to send Disbursal request to RBL!!</i></div>';
            
            $('#submit_to_rbl').html("Can't disbursed amount on RBL");
            
            $('#submit_to_rbl').attr("disabled", true);
          }
          
          $('#confirm_body').html(modal_body);

          $('#confirm_popup').modal({
                    backdrop: 'static',
                    keyboard: false
                });
        });

    $('.button-rbl-cancel-action').click(function(){
          var order_id = $(this).data('orderId');
          var order_no = $(this).data('orderNo');
          var date_added = $(this).data('dateAdded');
          var customer_id = $(this).data('customerId');
          var action = $(this).data('action');
          var total = $(this).data('total');
          var modal_body = '<div>'+
            '<input type= "hidden" name="action" value="' +action  +'" />'+
            '<input type= "hidden" name="order_no" value="' +order_no  +'" />'+
            '<input type= "hidden" name="order_id" value="' +order_id  +'" />'+
            '<input type= "hidden" name="customer_id" value="' +customer_id  +'" />'+
            '<input type= "hidden" name="date_added" value="' +date_added  +'" />'+
            '<input type= "hidden" name="total" value="' +total  +'" />'+
            '<label>Order Id: &nbsp;</label><span>' + order_id + '</span><br/>'+
            '<label>Cancel Request: &nbsp;</label><span>Rs.&nbsp;' + total + '</span><br/>'+
          '</div>';
          
          $('#confirm_body').html(modal_body);

          $('#submit_to_rbl').attr("disabled", false);
          
          $('#submit_to_rbl').html('Send Cancel Request To RBL');
          
          $('#confirm_popup').modal({
                    backdrop: 'static',
                    keyboard: false
                });
        });    

  
});
</script>

