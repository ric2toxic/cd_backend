<?php echo $header; ?>
<?php echo $column_left; 

?>

<div id="content">
    <div class="modal fade" id="neogrowth_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-lg" style="top:5%;width:90%;">
        <div class="modal-content" >
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">NeoGrowth Transaction logs </h4>
          </div>
          <div class="modal-body">
               <iframe src="about:blank" frameborder="0" id="neogrowth_logs" width="100%" height="400px;"></iframe> 
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
              <span class="small">OT = Order Total</span> |
              <span class="small">NP = Net Payable</span> |
              <span class="small">BA = Balance Amount</span> |
              <span class="small">P = Purchased</span> |
              <span class="small">C = Cancelled</span> |
              <span class="small">D = Delivered</span>
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
                  <div class="col-sm-12">
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
                                <!--<td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td> -->
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
                                <td style="width: 10px;"> NeoGrowth Transactions</td>
                                <td class="text-left">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td>
                                              Suborder No
                                              <hr class="btn-success">
                                              Total
                                            </td>
                                            <td class="text-center">Status</td>
                                            <td style="width: 10px;">NeoGrowth Action</td>
                                        </tr>
                                    </table>

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
                                <td class="text-center">
                                    <?php echo $column_total_bill; ?>
                                </td>
                                
                                <td class="text-center">
                                    View Logs
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders) { ?>
                            <?php foreach ($orders as $order) { ?>
                                <?php 
                                    $order_info = $order['order'];
                                    $suborder_info = $order['suborder'];
                                ?>
                            <tr>
                                <td class="text-left" width="10%">
                                  <strong>
                                      <?php echo $order_info['order_no']; ?>
                                  </strong>
                                  <?php echo $order_info['store_name']; ?>
                                    <span class="label label-success"><?php echo $order_info['order_from']; ?></span>
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

                                  </td>
                                  
                                  
                                  <td class="text-center" style="width: 10px;">
                                    <h6>
                                      <strong class="text-info">
                                        <span>P: </span>
                                        <span><?php echo $this->currency->format($order_info['neogrowth_purchased_amount']); ?></span>
                                      </strong>
                                    </h6>
                                    
                                    <h6>
                                      <strong class="text-info">
                                        <span>C: </span>
                                        <span><?php echo $this->currency->format($order_info['neogrowth_cancelled_amount']); ?></span>
                                      </strong>
                                    </h6>
                                    
                                    <h6>
                                      <strong class="text-info">
                                        <span>D: </span>
                                        <span><?php echo $this->currency->format($order_info['neogrowth_delivered_amount']); ?></span>
                                      </strong>
                                    </h6>
                                    
                                    <!-- <h6>
                                      <strong class="text-info">
                                        <span>R: </span>
                                        <span><?php echo $this->currency->format($order_info['neogrowth_return_amount']); ?></span>
                                      </strong>
                                    </h6> -->
                                  
                                  </td>
                                  
                                <td class="text-left suborder_table">
                                    <table class="table table-bordered">
                                      <?php 
                                        if( !empty( $suborder_info ) ) { 
                                          $render_neogrowth_actions = true;
                                      ?>
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
                                                <?php echo $suborder_data['status'] ; ?>
                                                </strong><br>
                                                <div>
                                                  <?php echo $suborder_data['delivery_date'] ; ?>
                                                </div><br>
                                                <div class="order_list_comment">
                                                    <?php
                                                        if ( !empty($suborder_data['order_history']) &&
                                                             is_array($suborder_data['order_history']) ) {
                                                            $last_history_comment = end($suborder_data['order_history']);
                                                            echo $last_history_comment['comment'];
                                                        }
                                                     ?>
                                                </div>
                                              </td>
                                              <?php 
                                                if ($render_neogrowth_actions) { 
                                                  $render_neogrowth_actions = false;
                                              ?>
                                                <td class="" style="width:10px;" rowspan="3">
                                                  <?php 
                                                    if (!empty($order_info['neogrowth_actions'])) { 
                                                      foreach($order_info['neogrowth_actions'] as $neogrowth_action => $neogrowth_action_data_arr) {
                                                        foreach($neogrowth_action_data_arr as $neogrowth_action_data) {
                                                  ?>
                                                    <div style="display:block; padding: 5px; ">
                                                        <button 
                                                          type="button"
                                                          data-toggle="tooltip"
                                                          title="To NeoGrowth"
                                                          class="btn btn-sm btn-info button-neogrowth-action"
                                                          data-action="<?php echo $neogrowth_action; ?>" 
                                                          data-order-no="<?php echo $neogrowth_action_data['order_no'] ?>" 
                                                          data-detail="<?php echo $neogrowth_action_data['detail'] ?>" 
                                                          data-customer-id="<?php echo $order_info['customer_id'] ?>" 
                                                          data-total="<?php echo $neogrowth_action_data['total'] ?>" 
                                                          data-text="<?php echo $neogrowth_action_data['text'] ?>" 
                                                        >
                                                        <?php echo $neogrowth_action_data['text'] ?>
                                                      </button>
                                                    </div>
                                                    <?php 
                                                          }
                                                        }
                                                      }
                                                    ?>
                                                </td>
                                              <?php } ?>
                                            </tr>
                                            <?php } ?>
                                        <?php } ?>
                                  </table>
                                  </td>
                                  
                                  <td class="text-left">
                                      <?php echo $order_info['date_added']; ?>
                                      <!-- order processing date -->
                                      <hr class="btn-success">
                                      <?php if($order_info['order_processing_date']){ echo $order_info['order_processing_date'] ; } ?>
                                      <!-- END  -->
                                  </td>
                                <td class="text-left">                                  
                                  <h6>
                                    <strong class="text-info">
                                      <span>OT: </span>
                                      <span><?php echo $order_info['total']; ?></span>
                                    </strong>
                                  </h6>
                                  <h6>
                                    <strong class="text-info">
                                      <span>NP: </span>
                                      <span><?php echo $this->currency->format($order_info['net_payment'], $order_info['currency_code'],$order_info['currency_value']); ?></span>
                                    </strong>  
                                  </h6>
                                  <h4 style="font-size: 14px;">
                                    <span class="label <?php echo ($order_info['balance_total_amount'] < 0) ? 'label-danger': 'label-success' ; ?> ">
                                      <span>BA: </span>
                                      <span><?php echo $this->currency->format($order_info['balance_total_amount'], $order_info['currency_code'], $order_info['currency_value']); ?></span>  
                                    </span>                                    
                                  </h4>
                                </td>
                                
                                <td class="text-right">
                                  <button type="button" class="btn btn-primary pull-right button-view-log" data-order-no="<?php echo $order_info['order_no'] ?>"><i class="fa fa-eye"></i></button>
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

    <!-- Confirm action(remove,move to wishlist,clear cart etc) model -->
    <div id="confirm_popup" class="modal fade" role="dialog">
        <div class="modal-dialog" style="top:20%;width:350px;z-index: 1050;">
            <div class="modal-content alert alert-info">
                <div class="modal-body" style="min-height: 50px;font-size: 18px;">
                  <form action="<?php echo $neogrowth_action_url; ?>" method="post">
                    <div style="display:block;">
                      <label>Please verify below details</label>                      
                    </div>
                    <hr />
                    <div id="confirm_body" style="display:block;">
                    </div>
                    <hr />
                    <div style="display:block;">
                        <button type="button" data-dismiss="modal" class="btn deliver_btn" style="margin-left: 40px;">Cancel</button>
                        <button type="submit" class="btn btn-info" id="submit_to_neogrowth" style="margin-left: 10px;">Submit</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">

    $(document).ready(function(){
        $('.well').hide();
        $('#advance_filter').click(function(){
            $('.well').toggle();
        });
        
        $('.button-neogrowth-action').click(function(){
          var order_no = $(this).data('orderNo');
          var customer_id = $(this).data('customerId');
          var detail = $(this).data('detail');
          var total = $(this).data('total');
          var action = $(this).data('action');
          var text = $(this).data('text');
          var modal_body = '<div>'+
            '<input type= "hidden" name="action" value="' +action  +'" />'+
            '<input type= "hidden" name="order_no" value="' +order_no  +'" />'+
            '<input type= "hidden" name="customer_id" value="' +customer_id  +'" />'+
            '<input type= "hidden" name="detail" value="' +detail  +'" />'+
            '<input type= "hidden" name="total" value="' +total  +'" />'+
            '<label>Order No: &nbsp;</label><span>' + order_no + '</span><br/>'+
            '<label>Amount: &nbsp;</label><span>Rs.&nbsp;' + total + '</span><br/>'+
          '</div>';
          
          $('#confirm_body').html(modal_body);
          $('#submit_to_neogrowth').html('Mark ' + action + ' to NeoGrowth');
          
          $('#confirm_popup').modal({
                    backdrop: 'static',
                    keyboard: false
                });
        });
        
        $('.button-view-log').click(function(){
          var order_no = $(this).data('orderNo');
          var url = 'index.php?route=operations/neogrowth_order/getNeoGrowthTransactions&token=<?php echo $token; ?>&order_no='+order_no;
          $('#neogrowth_logs').attr('src', url);
          $('#neogrowth_modal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
        });
    });
    
    $('#button-filter').on('click', function() {

        if ($('#filter_franchise_tab').length > 0) {
            url = 'index.php?route=operations/neogrowth_order&filter_franchise_tab=1&token=<?php echo $token; ?>';
        } else {
            url = 'index.php?route=operations/neogrowth_order&token=<?php echo $token; ?>';
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

        var filter_total = $('input[name=\'filter_total\']').val();

        if (filter_total) {
            url += '&filter_total=' + encodeURIComponent(filter_total);
        }

        var filter_date_added = $('input[name=\'filter_date_added\']').val();

        if (filter_date_added) {
            url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
        }

        var filter_payment_code = $('select[name=\'filter_payment_code\']').val();

        if (filter_payment_code != '*') {
            url += '&filter_payment_code=' + encodeURIComponent(filter_payment_code);
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
<script type="text/javascript">
  $('input[name=\'filter_order_no\'], input[name=\'filter_date_added\'], input[name=\'filter_customer\'], select[name=\'filter_order_status\'], select[name=\'filter_franchise_id\']').on('keypress',function(e){
    if (e.keyCode == 13) {
      $('#button-filter').trigger('click');
    }
  });
</script>
<style>
    .filter_total {
        width: 50% !important;
        float: left;
    }
</style>

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
