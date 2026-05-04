<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_advancereports; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
      <?php if ($error) { ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
      <?php } ?>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_salesreports_list; ?></h3>
        </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-6">
             <div class="form-group">
                <label class="control-label" for="input-invoice-date"><?php echo $entry_advance_voucher_date_from; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_advance_voucher_date_from" value="<?php echo $filter_advance_voucher_date_from; ?>" placeholder="<?php echo $entry_advance_voucher_date_from; ?>" data-date-format="YYYY-MM-DD" id="filter_advance_voucher_date_from" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
               </div>
              </div>
              
              <div class="form-group">
                <label class="control-label" for="input-invoice-date"><?php echo $entry_advance_voucher_check; ?></label>
                <div class="input-group date">
                    <input type="checkbox" id="checkopenorder" name="filter_advance_voucher_check" value="1" placeholder="<?php echo $entry_advance_voucher_check; ?>" />
                </div>
              </div>
              
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-invoice-date"><?php echo $entry_advance_voucher_date_to; ?></label>
                <div class="input-group date">
                    <input type="text" name="filter_advance_voucher_date_to" value="<?php echo $filter_advance_voucher_date_to; ?>" placeholder="<?php echo $entry_advance_voucher_date_to; ?>" data-date-format="YYYY-MM-DD" id="filter_advance_voucher_date_to" class="form-control" />
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
      </div>
    </div>
</div>


<script type="text/javascript">
$('#button-filter').on('click', function() {
  base_url = 'index.php?route=accounts/advancevoucherreports&token=<?php echo $token. ( $is_tally ? "&tally=1" : ''  ); ?>';
  var url = base_url;
  
  var filter_advance_voucher_date_from = $('input[name=\'filter_advance_voucher_date_from\']').val();

  if (filter_advance_voucher_date_from) {
    url += '&filter_advance_voucher_date_from=' + encodeURIComponent(filter_advance_voucher_date_from);
  }

  var filter_advance_voucher_date_to = $('input[name=\'filter_advance_voucher_date_to\']').val();

  if (filter_advance_voucher_date_to) {
    url += '&filter_advance_voucher_date_to=' + encodeURIComponent(filter_advance_voucher_date_to);
  }
  
  var filter_advance_voucher_check = $('input[name=\'filter_advance_voucher_check\']').val();
  
  if ($('#checkopenorder').prop('checked')==true) {
    if(filter_advance_voucher_date_from && filter_advance_voucher_date_to) {
        url += '&filter_advance_voucher_check=' + encodeURIComponent(filter_advance_voucher_check);
    } else {
        alert("Please select Advance vouchers from and to date.");
        return false;
    }
    
  }
  
  if(base_url == url){
      alert(<?php echo "'" . $error_warning . "'"; ?>);
  } else{
        location = url;
  }

});
</script>


<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript">
</script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript">
$('.date').datetimepicker({
  pickTime: false
});
</script>
<?php echo $footer; ?>
