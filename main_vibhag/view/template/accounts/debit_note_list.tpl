<?php echo $header; echo $column_left; ?>

<div id="content">
    <div class="modal" id="sellerInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content debit-note-comment col-sm-12">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Edit Debit Note Status</h4>
          </div>
          <div class="modal-body">
              <div class="modal-body">
                  <label class="control-label" for="input_debit_note_comment"><?php echo $entry_comment; ?></label> <br>
                                <input type="text" name="debit_note_physically_received_comment" placeholder="<?php echo $entry_comment_text; ?>" id="input_debit_note_comment" class="form-control " />
              </div>
              <br>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" 
                    class="btn btn-primary"
                    data-order-no='' 
                    data-dbt-note-id='' 
                    onclick="UpdateSellerInvoiceStatus(this)" >Save changes</button>
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

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="well">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-seller-name"><?php echo $entry_seller_name; ?></label>
                                <input type="text" name="filter_seller_name" value="<?php echo $filter_seller_name; ?>" placeholder="<?php echo $entry_seller_name; ?>" id="input-seller-name" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="select-location"><?php echo $entry_location; ?></label>
                                <input type="text" name="filter_location" value="<?php echo $filter_location; ?>" placeholder="<?php echo $entry_location; ?>" id="input-location" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-order-no"><?php echo $entry_order_no; ?></label> <br>
                                <input type="text" name="filter_order_no" value="<?php echo $filter_order_no; ?>" placeholder="<?php echo $entry_order_no; ?>" id="input-order-no" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="select-status"><?php echo $entry_status; ?></label>
                                <select name="filter_status" id="select-status" class="form-control">
                                    <option value="*"><?php echo $text_select_status; ?></option>
                                    <?php $status = array('0'=>'Pending','1'=>'Received');?>
                                        <?php foreach($status as $status_key =>  $status_value) { ?>
                                            <option value="<?php echo $status_key; ?>" 
                                                <?php echo ($status_key == $filter_status) ? 'selected' : '' ; ?> ><?php echo $status_value; ?>
                                            </option>
                                        <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-debit-note-date-from"><?php echo $entry_debit_note_date_from; ?></label>
                                <div class="input-group date">
                                  <input type="text" name="filter_debit_note_date_from" value="<?php echo $filter_debit_note_date_from; ?>" placeholder="<?php echo $entry_debit_note_date_from; ?>" data-date-format="YYYY-MM-DD" id="input-debit-note-date-from" class="form-control" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-debit-note-date-from"><?php echo $entry_debit_note_date_to; ?></label>
                                <div class="input-group date">
                                  <input type="text" name="filter_debit_note_date_to" value="<?php echo $filter_debit_note_date_to; ?>" placeholder="<?php echo $entry_debit_note_date_to; ?>" data-date-format="YYYY-MM-DD" id="input-debit-note-date-to" class="form-control" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>
                            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                            <button type="button" class="report_btn btn btn-info filter_report_btn" id="download_debit_note_report"><i class="fa fa-file-text" aria-hidden="true"></i>Download CSV</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <!--<td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td> -->
                                <td class="text-right">
                                    <?php echo $column_seller_name; ?>
                                </td>
                                <td class="text-left">
                                    <?php echo $column_location; ?>
                                </td>
                                <td class="text-left" id="order_no">
                                    <?php echo $column_order_no; ?>
                                </td>
                                <td class="text-left">
                                    <?php echo $column_order_date; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $column_debit_note_date; ?>
                                </td>

                                <td class="text-center">
                                    <?php echo $column_debit_note_no; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $column_total_value; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $column_product_value; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $column_tax; ?>
                                </td>
                                <td class="text-right">
                                    Action
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($debit_notes) { ?>
                            <?php foreach ($debit_notes as $debit_note) { ?>
                                
                            <tr class="<?php echo ($order_info['credit_color']) ? 'credit_color' : ''; ?>">
                                <td class="text-left" width="20%" id="seller_name">
                                    <?php echo $debit_note['seller_name']; ?>
                                </td>
                                <td class="text-left" width="10%">
                                    <?php echo $debit_note['location']; ?>
                                </td>
                                <td class="text-left" width="10%" id="order_no">
                                <strong><?php echo $debit_note['order_no']; ?></strong>
                                </td>
                                <td class="text-left" width="10%">
                                   <?php echo $debit_note['order_date']; ?>
                                </td>
                                <td class="text-right" width="10%">
                                   <?php echo $debit_note['debit_note_date']; ?>
                                </td>
                                <td class="text-left" width="10%">
                                   <?php echo $debit_note['debit_note_no']; ?>
                                </td>
                                <td class="text-left" width="5%">
                                   <center><?php echo $debit_note['total_value']; ?></center>
                                </td>
                                <td class="text-left" width="5%">
                                   <center><?php echo $debit_note['product_value']; ?></center>
                                </td>
                                <td class="text-left" width="10%">
                                    <center><?php echo $debit_note['tax']; ?></center>
                                </td>
                                <td class="text-center">
                                    <?php if ($debit_note['status'] == 0) { ?>
                                      <button type="button" 
                                              class="alert alert-danger pending_btn"
                                              data-toggle="modal" 
                                              data-order_no = "<?php echo $debit_note['order_no']; ?>"
                                              data-debit-note-id = "<?php echo $debit_note['debit_note_id']; ?>"
                                              data-target="#sellerInvoiceModal"> Pending </button>
                                    <?php } else { ?>
                                            <button type="button" class="btn btn-primary alert alert-success" data-toggle="tooltip" title="<?php echo $debit_note['comment']; ?>">Received</button>
                                    <?php } ?>
                                </td>
                                <?php } ?>
                              </tr>

                            
                            <?php } else { ?>
                            <tr>
                              <td class="text-center" colspan="12"><?php echo $text_no_results; ?></td>
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
    
</div>

<script type="text/javascript">
    
    $('#button-filter').on('click', function() {

        url = 'index.php?route=accounts/seller_debit_note_verification&token=<?php echo $token; ?>';

        var filter_seller_name = $('input[name=\'filter_seller_name\']').val();

        if (filter_seller_name) {
            url += '&filter_seller_name=' + encodeURIComponent(filter_seller_name);
        }

        var filter_order_no = $('input[name=\'filter_order_no\']').val();

        if (filter_order_no) {
            url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
        }

        var filter_debit_note_date_from = $('input[name=\'filter_debit_note_date_from\']').val();

        if (filter_debit_note_date_from) {
            url += '&filter_debit_note_date_from=' + encodeURIComponent(filter_debit_note_date_from);
        }

        var filter_debit_note_date_to = $('input[name=\'filter_debit_note_date_to\']').val();

        if (filter_debit_note_date_to) {
            url += '&filter_debit_note_date_to=' + encodeURIComponent(filter_debit_note_date_to);
        }

        var filter_location = $('input[name=\'filter_location\']').val();

        if (filter_location) {
            url += '&filter_location=' + encodeURIComponent(filter_location);
        }

        var filter_status = $('select[name=\'filter_status\']').val();

        if (filter_status!='*') {
            url += '&filter_status=' + encodeURIComponent(filter_status);
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
    //-->
</script>

<?php echo $footer; ?>
<script type="text/javascript">
  $('input,select').on('keypress',function(e){
    if (e.keyCode == 13) {
      $('#button-filter').trigger('click');
    }
  });

$(document).ready(function(){
  $('.pending_btn').click(function(){
    var order_no = $(this).data('order_no');
    var debit_note_id = $(this).data('debit-note-id');
    
    $('.btn-primary').attr('data-order-no',order_no);
    $('.btn-primary').attr('data-dbt-note-id',debit_note_id);
  });
});


  function UpdateSellerInvoiceStatus(obj){

    var comment = $(obj).parent().parent('.debit-note-comment').find('#input_debit_note_comment').val();
    //var order_no = $(obj).attr('data-order-no');
    var dbt_note_id = $(obj).attr('data-dbt-note-id');
    $.ajax({
        url: 'index.php?route=accounts/seller_debit_note_verification/updateStatusComment&token=<?php echo $token;?>&comment=' + comment +'&debt_note_id='+dbt_note_id,
        type:'POST',
        beforeSend: function(){ },
        complete: function(){ },
        success: function(json) {
          if (json['error']) {
            alert(json['error']);
          }
          
          window.location.reload();
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });

  }
</script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});

$('#download_debit_note_report').click(function(){
    var url = "index.php?route=accounts/seller_debit_note_verification/index&token=<?php echo $token;?>";

    var filter_seller_name = $('input[name=\'filter_seller_name\']').val();

    if (filter_seller_name != '') {
        url += '&filter_seller_name=' + encodeURIComponent(filter_seller_name);
    }

    var filter_order_no = $('input[name=\'filter_order_no\']').val();

    if (filter_order_no != '') {
        url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
    }

    var filter_debit_note_date_from = $('input[name=\'filter_debit_note_date_from\']').val();

    if(filter_debit_note_date_from !='' ){
        url += '&filter_debit_note_date_from=' + encodeURIComponent(filter_debit_note_date_from);
    }

    var filter_debit_note_date_to = $('input[name=\'filter_debit_note_date_to\']').val();;

    if(filter_debit_note_date_to !='' ){
        url += '&filter_debit_note_date_to=' + encodeURIComponent(filter_debit_note_date_to);
    }

    var filter_location = $('input[name=\'filter_location\']').val();
    if (filter_location != '') {
        url += '&filter_location=' + encodeURIComponent(filter_location);
    }

    var filter_status = $('select[name=\'filter_status\']').val();
    if (filter_status != '*') {
        url += '&filter_status=' + encodeURIComponent(filter_status);
    }
    
    url += '&download_debit_note_report=true';
        
    location = url;

});
</script>