<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1>Sale Return Report</h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
      <?php if ($error) { ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>Atleast ONE Filter must be selected to Download Sales Return Report CSV !!
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
      <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> Sale Return Report</h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <form action="" method="get">
              <div class="row">
                <div class="col-sm-4">
                 <div class="form-group">
                     <input type="hidden"
                            name="token"
                            value="<?php echo $token; ?>"/>
                     <input type="hidden"
                            name="route"
                            value="<?php echo $route; ?>"/>
                    <label class="control-label" for="input-invoice-date">Invoice Date From</label>
                    <div class="input-group date">
                      <input type="text"
                             name="filter_invoice_date_from"
                             value="<?php echo $filter_invoice_date_from; ?>"
                             placeholder="Invoice Date From"
                             data-date-format="YYYY-MM-DD"
                             id="input-invoice-date"
                             class="form-control" />
                      <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                      </span>
                   </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label" for="input-date-added">Credit Note Date From</label>
                    <div class="input-group date">
                      <input type="text"
                             name="filter_credit_note_date_from"
                             value="<?php echo $filter_credit_note_date_from; ?>"
                             placeholder="Credit Note Date From"
                             data-date-format="YYYY-MM-DD"
                             id="input-date-added"
                             class="form-control" />
                      <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                      </span>
                   </div>
                  </div>
                  
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" for="input-invoice-date">Invoice Date To</label>
                    <div class="input-group date">
                      <input type="text"
                             name="filter_invoice_date_to"
                             value="<?php echo $filter_invoice_date_to; ?>"
                             placeholder="Invoice Date To"
                             data-date-format="YYYY-MM-DD"
                             id="input-invoice-date"
                             class="form-control" />
                      <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label" for="input-date-added">Credit Note Date To</label>
                    <div class="input-group date">
                      <input type="text"
                             name="filter_credit_note_date_to"
                             value="<?php echo $filter_credit_note_date_to; ?>"
                             placeholder="Credit Note Date From To"
                             data-date-format="YYYY-MM-DD"
                             id="input-date-added"
                             class="form-control" />
                      <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                      </span></div>
                  </div>
                  
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                  <label class="control-label" for="input-customer-id">Customer ID</label>
                  <input type="text" 
                         name="filter_customer_id" 
                         value="<?php echo $filter_customer_id; ?>"
                         placeholder="Customer ID" 
                         id="input-customer-id" 
                         class="form-control" />
                                 
                </div>
                <div class="form-group">
                      <?php if($is_tally){ ?>
                      <input type="hidden"
                             name="tally"
                             value="1"/>
                      <?php } ?>
                      <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-search"></i>Download CSV</button>
                  </div>
                </div>
              </div>
          </form>
        </div>
      </div>
    </div>
</div>


<!-- Auto complete scripts -->
<script type="text/javascript"><!--
$('input[name=\'filter_order_no\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=accounts/salesreports/autocomplete&token=<?php echo $token; ?>&filter_order_no=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['order_no']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'filter_order_no\']').val(item['label']);
  }
});
</script>

<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript">
</script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript"><!--
$('.date').datetimepicker({
  pickTime: false
});
</script>
<?php echo $footer; ?>
