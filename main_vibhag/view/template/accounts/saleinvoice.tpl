<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_saleinvoice; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="alert alert-danger custom_error_warning"></div>
      <?php if ($error_no_result) { ?>
        <div class="error_no_result alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_no_result; ?>
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
      <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_saleinvoice; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <form action="<?php echo $action; ?>" method="post">
              <div class="row">
                <div class="col-sm-6">            
                  <div class="form-group">
                    <label class="control-label" for="input-invoice-no-form"><?php echo $entry_invoice_no_from; ?></label>
                    <input type="text" name="filter_invoice_no_from" value="" placeholder="<?php echo $entry_invoice_no_from; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  </div>
                  <div class="form-group">
                    <label class="control-label" for="input-invoice-date"><?php echo $entry_invoice_date_from; ?></label>
                    <div class="input-group date">
                      <input type="text" name="filter_invoice_date_from" value="" placeholder="<?php echo $entry_invoice_date_from; ?>" data-date-format="YYYY-MM-DD" id="input-invoice-date" class="form-control" />
                      <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                      </span></div> 
                  </div>
                </div>
                
                <div class="col-sm-6">              
                  <div class="form-group">
                    <label class="control-label" for="input-invoice-no-to"><?php echo $entry_invoice_no_to; ?></label>
                    <input type="text" name="filter_invoice_no_to" value="" placeholder="<?php echo $entry_invoice_no_to; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  </div>
                  <div class="form-group">
                    <label class="control-label" for="input-invoice-date"><?php echo $entry_invoice_date_to; ?></label>
                    <div class="input-group date">
                      <input type="text" name="filter_invoice_date_to" value="" placeholder="<?php echo $entry_invoice_date_to; ?>" data-date-format="YYYY-MM-DD" id="input-invoice-date" class="form-control" />
                      <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                      </span></div> 
                  </div>
                  <div class="form-group">
                      <button type="submit" id="button-download" class="btn btn-primary pull-right"><i class="fa fa-download"></i> <?php echo $button_download; ?></button>
                  </div>
                </div>
              </div>
          </form>
        </div>       
      </div>
    </div>
</div>


<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript">
</script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript"><!--
$('.date').datetimepicker({
  pickTime: false
});
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $('.custom_error_warning').hide();
    $('#button-download').on('click',function(){
      var invoice_no_from   = $('input[name=\'filter_invoice_no_from\']').val(); 
      var invoice_no_to     = $('input[name=\'filter_invoice_no_to\']').val(); 
      var invoice_date_from = $('input[name=\'filter_invoice_date_from\']').val(); 
      var invoice_date_to   = $('input[name=\'filter_invoice_date_to\']').val(); 
      $('.error_no_result').remove();
      if( (invoice_no_from && invoice_no_to) || (invoice_date_from && invoice_date_to ) ) {
        $('.custom_error_warning').hide();
      }else{
        $('.custom_error_warning').show();
        $('.custom_error_warning').append('<i class="fa fa-exclamation-circle"></i><button type="button" class="close" data-dismiss="alert">&times;</button> Please enter invoice no from and to !! OR Please enter invoice date from and to !!');
        return false;
      }
      
      if($('.alert').html().length == 0){
        $('.alert').remove();
      }
      
    });
    
  });
</script>
<?php echo $footer; ?>
