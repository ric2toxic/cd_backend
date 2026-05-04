<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
      <a href="<?php echo $duplicate_customer_url; ?>" data-toggle="tooltip" title="<?php echo 'Download Duplicate Customer'; ?>" class="btn btn-primary"><i class="fa fa-files-o"></i></a>
      <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-customer').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
    <div class="text-right">
      <span class="small">O.T = Order Total</span> |
      <span class="small">N.P = Net Payable</span> |
      <span class="small">B.A.= Balance Amount</span> |
      <span class="small">O = Orders</span> |
      <span class="small">R = Returns</span> |
      <span class="small">F = COD Failed</span> |
      <span class="small">DI = Delivery Issues</span> |
      <span class="small">C = Cancelled Orders</span> |
      <span class="small">LO = Last Order date</span>
      <span class="small">&nbsp;</span>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
        <div style="float:right">
          <form name="form-customer-notification" action="index.php?route=notification/notification/notification&token=<?php echo $token; ?>" onsubmit="return sendNotifications()" method="post">
            <input type="submit" value="Send Notifications"></input>
            <input type="hidden" class="send_notifications_customers" name="customers" value="" id="input-customer-ids" class="form-control" />
          </form>
        </div>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                <input type="text" name="filter_email" value="<?php echo $filter_email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-gst-number"><?php echo $entry_gst_number; ?></label>
                <input type="text" name="filter_gst_number" value="<?php echo $filter_gst_number; ?>" placeholder="<?php echo $entry_gst_number; ?>" id="input-gst-number" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
                <input type="text" name="filter_telephone" value="<?php echo $filter_telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-filter-customer-type"><?php echo $entry_customer_type; ?></label>
                <select name="filter_customer_type" id="input-filter-customer-type" class="form-control">
                  <?php $customer_type = array('0' => 'Normal Customer Only', '1' => 'Dropshipper Only', '2' => 'Pending Dropshipper', '3' => 'Blocked Dropshipper', '4' => 'Dropshipper + Normal Customer','5' => 'Franchise Only','all'=> 'ALL');?>
                  <?php foreach($customer_type as $index_key => $values) { ?>
                  <option value="<?php echo $index_key; ?>" <?php echo ($index_key == $filter_customer_type ) ? 'selected' : ''?> ><?php echo $values; ?></option>
                  <?php } ?>
                </select>
              </div> 
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_customer_id; ?></label>
                <input type="text" name="filter_customer_id" value="<?php echo $filter_customer_id; ?>" placeholder="<?php echo $entry_customer_id; ?>" id="input-name" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-email"><?php echo $entry_city; ?></label>
                <input type="text" name="filter_city" value="<?php echo $filter_city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-order-count"><?php echo $entry_order_count; ?></label>
                <input type="text" name="filter_order_count" value="<?php echo $filter_order_count; ?>" placeholder="<?php echo $entry_order_count; ?>" id="input-order-count" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_master_id; ?></label>
                <input type="text" name="filter_master_id" value="<?php echo $filter_master_id; ?>" placeholder="<?php echo $entry_master_id; ?>" id="input-name" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
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
                <label class="control-label" for="filter_umrn_lan"><?php echo $entry_umrn_lan; ?></label>
                <input type="text" name="filter_umrn_lan" value="<?php echo $filter_umrn_lan; ?>" placeholder="<?php echo $entry_umrn_lan; ?>" id="filter_umrn_lan" class="form-control" />
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
              </div>
            </div>  
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-customer">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php echo $column_name; ?></td>
                  <td class="text-left">
                    <?php echo $column_email; ?>
                    <hr class="btn-success">
                    <?php echo $column_telephone; ?>
                  </td>
                  <td class="text-left"> Stats </td>
                  <td class="text-left"><?php echo $column_cart_items; ?></td> 
                  <td class="text-left"><?php if ($sort == 'cc.date_modified') { ?> 
                    <a href="<?php echo $sort_last_cart_modified; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_last_cart_modified; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_last_cart_modified; ?>"><?php echo $column_last_cart_modified; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $column_buildup_time; ?></td>
                  <td class="text-left"><?php if ($sort == 'c.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <td class="text-right" width=""><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($customers) { ?>
                <?php foreach ($customers as $customer) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($customer['customer_id'], $selected)) { ?>
                    <input type="checkbox" class="check_customer" name="selected[]" value="<?php echo $customer['customer_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" class="check_customer" name="selected[]" value="<?php echo $customer['customer_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left order_list_comment"><strong><?php echo $customer['name'];?></strong>
                  <?php if ($customer['company'] or $customer['city']) { ?>
                    <br><?php echo trim($customer['company'] . ' ' . $customer['city']); ?>
                  <?php } ?>
                  <?php if($customer['is_dropshipper']==1 || $customer['is_dropshipper']==2 || $customer['is_dropshipper']==3 ){ ?>
                    <br><label class="dropshipper_heading drop_head<?php echo $customer['is_dropshipper']; ?>">
                      Dropshipper</label> 
                  <?php }else{
                  }?>
                  <br><?php echo !empty($customer['agent_name'])?"<hr>Agent: ".$customer['agent_name']:""; ?>
                    <?php if ($customer['id_status'] == 'unmarked') {?>
                    <div title="Mark Duplicate"  data-toggle="tooltip">
                        <button type="button" data-customerid="<?php echo $customer['customer_id'];?>" 
                         style="margin-left: 10px;" class="btn btn-danger btn-xs mark_duplicate">
                            <i class="fa fa-files-o" aria-hidden="true"></i>
                        </button>                                        
                    </div>
                    <?php } elseif ($customer['id_status'] == 'master') { ?>
                    <div>
                        <button type="button" style="margin-left: 10px;" class="btn btn-danger btn-xs">M</button>                                       
                    </div>
                    <?php } elseif ($customer['id_status'] == 'duplicate') { ?>
                    <div>
                        <button type="button" style="margin-left: 10px;" class="btn btn-danger btn-xs">D</button>                                        
                    </div>
                    <?php } ?>
                  </td>
                  <td class="text-left">
                    <?php if($customer['total_orders_today'] > 0 ){ ?>
                    <span class="total_orders_today">T</span>
                    <?php } ?><br>
                    <?php if($customer['self_order'] == 1 && $this->user->getGroupId() == 1) { ?>
                    <div><span class="app_installed">SELF ORDER</span></div>
                    <?php } ?>
                    <span class="click_to_see btn-primary btn-xs" data-field-type="email" data-field-value="<?php echo base64_encode($customer['email']); ?>" onClick="clickToSee(this)"> Click to see </span>
                    <hr class="btn-success">
                    <span class="click_to_see btn-primary btn-xs" data-field-type="telephone" data-field-value="<?php echo base64_encode($customer['telephone']); ?>" onClick="clickToSee(this)"> Click to see </span>
                    <?php if ($customer['app_installed']) { ?>
                    <br/>
                    <label> <span class="app_installed"><?php echo "App Installed"; ?></span></label>
                    <?php }
                      echo "<br/>";
                      ?>
                    <?php if(in_array($this->user->getId(),SMS_CRITERIA_ICON_PERMISSION) || in_array($this->user->getId(),SHORT_SMS_PERMISSION)) { ?>
                    <hr class="btn-success">
                    <div id="sms_details_<?php echo $customer['customer_id']; ?>">
                        <span class="click_to_see btn-primary btn-xs" onclick="getSMSDetails(<?php echo $customer['customer_id'];?>, 'sms_details_<?php echo $customer['customer_id']; ?>')">Get Profile</span>
                    </div>
                    <?php } ?>

                     <?php if($customer['credit_lead'] == 0) { ?> 
                       <br /><br />
                        <button class="btn-xs  btn-warning credit_application_btn "
                              data-customerid="<?php echo $customer['customer_id']; ?>"
                              id="credit_application_btn_<?php echo $customer['customer_id']; ?>">
                                     Add Credit Lead
                        </button>
                      <?php }?>
                  </td>
                  <td class="text-left btn-xs">
                    <div id="order_stats_<?php echo $customer['customer_id'];?>">
                        <span class="click_to_see btn-primary btn-xs" onclick="getOrderStats(<?php echo $customer['customer_id'];?>, 'order_stats_<?php echo $customer['customer_id'];?>')"> Get Stats </span>
                    </div>
                  </td>
                  
                  <td class="text-center">
                    <?php echo $customer['cart_items']; ?>
                    <div style="display: block; width: 100%; clear: left;">
                      cid: <strong>[<?php echo $customer['customer_id']; ?>]</strong>
                      MasterId: <strong>[<?php echo $customer['master_id']; ?>]</strong>
                    </div>
                  </td>
                  <td class="text-left"><?php echo $customer['last_cart_modified']; ?></td>
                  <td class="text-left"><?php echo $customer['buildup_time']; ?></td>
                  <td class="text-left"><?php echo $customer['date_added'];?></td>

                  <td class="text-right" style="width: 120px;">
                     <a data-id="<?php echo $customer['customer_id']; ?>" data-toggle="modal" title = "Credit Application" class="edit_application btn btn-info"><i class="fa fa-file"></i></a>
                    <div class="btn-group margin-top" data-toggle="tooltip" title="<?php echo $button_login; ?>">
                      <button type="button" data-toggle="dropdown" class="btn btn-info dropdown-toggle"><i class="fa fa-lock"></i></button>
                      

                      <ul class="dropdown-menu pull-right">
                        <li><a href="index.php?route=sale/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $customer['customer_id']; ?>&store_id=0" target="_blank"><?php echo $text_default; ?></a></li>
                      <?php if (!empty($stores)) {  
                              foreach ($stores as $store) { ?>
                                <li><a href="index.php?route=sale/customer/login&token=<?php echo $token; ?>&customer_id=<?php echo $customer['customer_id']; ?>&store_id=<?php echo $store['store_id']; ?>" target="_blank"><?php echo $store['name']; ?></a></li>
                            <?php } 
                           } 
                      ?>
                      </ul>
                    </div>
                    <a href="<?php echo $customer['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary margin-top"><i class="fa fa-pencil"></i></a>
                    <?php if(!empty($customer['has_website']) && $customer['has_website']>0){?>
                    <a href="javascript:;" data-toggle="tooltip" title="<?php echo $has_website; ?>" class="btn btn-danger"><i class="fa fa-globe"></i></a>
                    <?php } ?>
                    <?php if(!empty($customer['has_approved_wsb_credit']) && $customer['has_approved_wsb_credit'] == 'ENABLED' ){ ?>
                    <button type="button" class="btn btn-success margin-top" style="padding: 7px 12px;"
                    data-toggle="modal" data-target="#wsb_credit_popup" onclick="view_wsb_credit_detail('<?php echo $customer['customer_id']; ?>' );">
                      <i class="fa fa-credit-card"></i>
                    </button>
                    <?php } ?>
                    <a href="<?php echo $customer['credit_tab']; ?>" style="padding: 7px 11px;" data-toggle="tooltip" title="Edit Credit Tab" class="btn btn-primary margin-top" target="_blank"><i class="fa fa-credit-card"></i></a>

                    <?php if ($customer['edit_bank_detail']) { ?>
                      <a href="<?php echo $customer['edit_bank_detail']; ?>" data-toggle="tooltip" title="<?php echo $btn_update_bank_detail; ?>" class="btn btn-success margin-top"><i class="fa fa-university"></i></a>
                    <?php } else { ?>
                    <button type="button" class="btn btn-success" disabled><i class="fa university"></i></button>
                    <?php } ?>
                   
                    

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
        </form>
        <div class="row"><?php echo $pagination; ?></div>
      </div>
    </div>
  </div>
  <!-- customer duplicate id modal kss -->
  <div id="mark_duplicate_popup" class="modal fade" role="dialog"><?php echo $duplicate_popup;?></div>
  <!-- Modal end -->

  <div id="credit_application_edit" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 850px;height:700px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3 class="modal-title">Edit Credit Application</h3>
    </div>
    <div class="modal-body">
        <div class="credit_application_iframe">
        </div>
    </div>
    <div class="modal-footer">     
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



<style type="text/css">
  .margin-top{
    margin-top: 4px;
  }
</style>
<script type="text/javascript">
  function view_wsb_credit_detail(customer_id){

    $.ajax({
      url: 'index.php?route=sale/order/getWsbCreditdetails&token=<?php echo $token; ?>',
      type:'POST',
      data:{ 'customer_id':customer_id},
      success: function(json) {
        console.log(json);
        json = JSON.parse(json);
        $('#wsb_credit_popup .credit_satus').html(json.credit_status);
        $('#wsb_credit_popup .approved_limit').html(json.credit_limit);
        $('#wsb_credit_popup .total_used_limit').html(json.total_used_credit_bal);
        $('#wsb_credit_popup .free_limit').html(json.total_available_bal);
      //  $('#wsb_credit_popup').toggle();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  }
  <!--
$('#button-filter').on('click', function() {
  url = 'index.php?route=sale/customer&token=<?php echo $token; ?>';
  
  var filter_name = $('input[name=\'filter_name\']').val();
  
  if (filter_name) {
    url += '&filter_name=' + encodeURIComponent(filter_name);
  }

  var filter_customer_id = $('input[name=\'filter_customer_id\']').val();
  
  if (filter_customer_id) {
    url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
  }

  var filter_master_id = $('input[name=\'filter_master_id\']').val();
  
  if (filter_master_id) {
    url += '&filter_master_id=' + encodeURIComponent(filter_master_id);
  }
  
  var filter_email = $('input[name=\'filter_email\']').val();
  
  if (filter_email) {
    url += '&filter_email=' + encodeURIComponent(filter_email);
  }
    
    var filter_telephone = $('input[name=\'filter_telephone\']').val();
  
  if (filter_telephone) {
    url += '&filter_telephone=' + encodeURIComponent(filter_telephone);
  } 
  
  var filter_order_count = $('input[name=\'filter_order_count\']').val();
  
  if (filter_order_count) {
    url += '&filter_order_count=' + encodeURIComponent(filter_order_count);
  }
    
  var filter_date_added = $('input[name=\'filter_date_added\']').val();
  
  if (filter_date_added) {
    url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
  }

  var filter_city = $('input[name=\'filter_city\']').val();

  if (filter_city) {
      url += '&filter_city=' + encodeURIComponent(filter_city);
  }

  var filter_customer_type = $('select[name=\'filter_customer_type\']').val();

  if (filter_customer_type != '*') {
    url += '&filter_customer_type=' + encodeURIComponent(filter_customer_type);
  }

  var filter_gst_number = $('input[name=\'filter_gst_number\']').val();

  if (filter_gst_number) {
    url += '&filter_gst_number=' + encodeURIComponent(filter_gst_number);
  }
  
  var filter_umrn_lan = $('input[name=\'filter_umrn_lan\']').val();

  if (filter_umrn_lan) {
    url += '&filter_umrn_lan=' + encodeURIComponent(filter_umrn_lan);
  }

  location = url;
});
//--></script> 

  <script type="text/javascript">
$('.date').datetimepicker({
  pickTime: false
});
</script></div>
<?php echo $footer; ?>

<script type="text/javascript">
  $('input, select').on('keypress',function(e){
    if (e.keyCode == 13) {
      $('#button-filter').trigger('click');
    }
  });

  function sendNotifications(){
    var chkArray = [];
    $('.check_customer:checked').each(function() {
        chkArray.push($(this).val());
    });
    if(chkArray == ''){
      alert("Please Select Customers");
      return false;
    }
    $('.send_notifications_customers').val(chkArray);
  }

  $(document).ready(function(){
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
    $("body").delegate(".edit_application", "click", function(){
      var customer_id = $(this).data('id');
        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/edit&token=<?php echo $token; ?>&customer_id=' + customer_id,
            dataType: "json",
            success: function(data) {
              if(data['status'] == '1'){
                $('.credit_application_iframe').html("<iframe src='<?php echo HTTPS_CATALOG; ?>index.php?route=account/credit_application&customer_id=" + customer_id + "&token="+ data['token']+"&khufiya_user_id="+ data['user_id']+"' style='height:600px;width:800px;'></iframe>");
                $('#credit_application_edit').modal('show');
              }

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        }); 

    });

    $('.credit_application_btn').click(function()
    {
        var customer_id = $(this).data('customerid');
         $.ajax({
        url: 'index.php?route=sale/customer_credit_application/create_lead&token=<?php echo $token; ?>',
        type:'POST',
        data:{'customer_id':customer_id},
        dataType: 'json',
        success: function(json) {
           if(json['status'] == 1)
           {
             $("#credit_application_btn_"+customer_id).hide();
           }
           alert(json['message']);
           console.log(json);
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    });
 
});
</script>
