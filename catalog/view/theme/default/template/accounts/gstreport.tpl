<?php echo $header; ?>

<div id="page-wrapper">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_payments_report; ?> <?php if(isset($invoice_details[0]['seller_name'])){echo 'of ('.$invoice_details[0]['seller_name'].')';}?></h1>
     
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
        <h3 class="panel-title"><i class="fa fa-list"></i><?php echo $heading_payments_report; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
           <div class="row">

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-order-no"><?php echo $entry_select_year; ?></label>
                <select name="filter_select_year" class="form-control filter_select_year">
                    <option value="">--Select--</option>
                    
                    <?php //echo "pre";print_r($getYears);//echo $filter_select_year; die;?>

                    <?php if( !empty($getYears) ){ ?>
                    <?php foreach($getYears as $year_key => $year_value) { ?>
                    <option value="<?php echo $year_key; ?>" <?php echo ($year_key == $filter_select_year) ? 'selected' : ''; ?>><?php echo $year_value ?></option>
                    <?php } ?>
                    <?php } ?>
                </select>

              </div>              
            </div>


            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-order-no"><?php echo $entry_select_quarter; ?></label>
                <select name="filter_select_quarter" class="form-control filter_select_quarter">
                    <option value="">--Select--</option>
                    <?php if( !empty($getQuarters) ){ ?>
                    <?php foreach($getQuarters as $quarter_key => $quarter_value) { ?>
                    <option value="<?php echo $quarter_key; ?>" <?php echo ($quarter_key == $filter_select_quarter) ? 'selected' : ''; ?>><?php echo $quarter_value ?></option>
                    <?php } ?>
                    <?php } ?>
                </select>
              </div>              
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-order-no"><?php echo $entry_select_month; ?></label>
                <select name="filter_select_month" class="form-control filter_select_month">
                    <option value="">--Select--</option>
                    <?php if( !empty($getMonths) ){ ?>
                    <?php foreach($getMonths as $month_key => $month_value) { ?>
                    <option value="<?php echo $month_key; ?>" <?php echo ($month_key == $filter_select_month) ? 'selected' : ''; ?>><?php echo $month_value ?></option>
                    <?php } ?>
                    <?php } ?>
                </select>
              </div>              
            </div>

            <!-- <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-order-no"><?php echo $entry_select_year; ?></label>
                <select name="filter_year_range" class="form-control filter_year_range">
                    <option value="">--Select--</option>
                    <?php if( !empty($getFinancialYears) ) { ?>
                    <?php foreach($getFinancialYears as $year_key => $year_value) {?>
                    <option value="<?php echo $year_value; ?>" <?php echo ($filter_year_range == $year_value) ? 'selected' : '' ; ?> ><?php echo $year_value; ?></option>
                    <?php } ?>
                    <?php } ?>
                </select>
              </div>              
            </div> -->


            <div class="col-sm-3">
              <div class="form-group">
                  <button style="margin-top: 22px;" type="button" id="button-search" class="btn btn-primary pull-left">
                    <i class="fa fa-search"></i> <?php echo $button_search; ?>
                  </button>
                  <!--button style="margin-top: 22px;" type="button" class="button-download-csv btn btn-primary pull-left" csvname="csvall">
                    <i class="fa"></i> <?php //echo $button_download_csv; ?>
                  </button-->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div>
      <span style="font-size:25px;">Sales Report</span>
      <button style="margin-left: 202px;float: none!important;" type="button" class="button-download-csv btn btn-primary pull-left" csvname="csvInvoice">
        <i class="fa"></i> <?php echo $button_download_csv; ?>
      </button>
    </div>
    <div class="table-responsive">
      <?php if(!empty($invoice_details)){ ?>
        <table class="table table-bordered table-hover payment-details" align="center">
          <thead>
            <tr>
              <td><?php echo $header_gst_rate; ?></td>
              <td><?php echo $header_product_value; ?></td>
              <td><?php echo $header_cgst; ?></td>
              <td><?php echo $header_sgst; ?></td>
              <td><?php echo $header_igst; ?></td>
              <td><?php echo $header_total_tax; ?></td>
            </tr>
          </thead>
          <tbody>
          <?php foreach($invoice_details as $data){ ?>
            
            <tr>
              <td><?php echo round($data['seller_input_tax'],2); ?></td>
              <td><?php echo round($data['total_product_value'],2); ?></td>
              <td><?php echo round($data['SGST'],2); ?></td>
              <td><?php echo round($data['CGST'],2); ?></td>
              <td><?php echo round($data['IGST'],2); ?></td>
              <td><?php echo round($data['total_tax'],2); ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      <?php }else{
          echo '<div><i>No Data Found.</i></div>';
      } ?>
    </div>

    <div>
      <span style="font-size:25px;">Return Report</span>
      <button style="margin-left: 202px;float: none!important;" type="button" class="button-download-csv btn btn-primary pull-left" csvname="csvReturn">
        <i class="fa"></i> <?php echo $button_download_csv; ?>
      </button>
    </div>
    <div class="table-responsive">
      <?php if(!empty($return_details)){ ?>
        <table class="table table-bordered table-hover payment-details" align="center">
          <thead>
            <tr>
              <td><?php echo $header_gst_rate; ?></td>
              <td><?php echo $header_product_value; ?></td>
              <td><?php echo $header_cgst; ?></td>
              <td><?php echo $header_sgst; ?></td>
              <td><?php echo $header_igst; ?></td>
              <td><?php echo $header_total_tax; ?></td>
            </tr>
          </thead>
          <tbody>
          <?php foreach($return_details as $data){ ?>
            
            <tr>
              <td><?php echo round($data['seller_input_tax'],2); ?></td>
              <td><?php echo round($data['total_product_value'],2); ?></td>
              <td><?php echo round($data['SGST'],2); ?></td>
              <td><?php echo round($data['CGST'],2); ?></td>
              <td><?php echo round($data['IGST'],2); ?></td>
              <td><?php echo round($data['total_tax'],2); ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      <?php }else{
          echo '<div><i>No Data Found.</i></div>';
      } ?>
    </div>

    <div>
      <span style="font-size:25px;">HSN Summary</span>
      <button style="margin-left: 202px;float: none!important;" type="button" class="button-download-csv btn btn-primary pull-left" csvname="csvHSN">
        <i class="fa"></i> <?php echo $button_download_csv; ?>
      </button>
    </div>
    <div class="table-responsive">
      <?php if(!empty($hsn_details)){ ?>
        <table class="table table-bordered table-hover payment-details" align="center">
          <thead>
            <tr>
              <td>HSN Code</td>
              <td><?php echo $header_gst_rate; ?></td>
              <td><?php echo $header_product_value; ?></td>
              <td><?php echo $header_cgst; ?></td>
              <td><?php echo $header_sgst; ?></td>
              <td><?php echo $header_igst; ?></td>
              <td><?php echo $header_total_tax; ?></td>
            </tr>
          </thead>
          <tbody>
          <?php foreach($hsn_details as $data){ ?>
            
            <tr>
              <td><?php echo $data['hsn_code']; ?></td>
              <td><?php echo round($data['seller_input_tax'],2); ?></td>
              <td><?php echo round($data['total_product_value'],2); ?></td>
              <td><?php echo round($data['SGST'],2); ?></td>
              <td><?php echo round($data['CGST'],2); ?></td>
              <td><?php echo round($data['IGST'],2); ?></td>
              <td><?php echo round($data['total_tax'],2); ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      <?php }else{
          echo '<div><i>No Data Found.</i></div>';
      } ?>
    </div>
  </div>
</div>
<?php echo $footer; ?>

<script type="text/javascript"><!--

$('#button-search').on('click', function() {
  var base_url = 'index.php?route=seller_panel/gstreports&token=<?php echo $token; ?>';
  var url = base_url;

  var filter_select_month = $('select[name=\'filter_select_month\']').val();
  if (filter_select_month) {
    url += '&filter_select_month=' + encodeURIComponent(filter_select_month);
  }

  var filter_select_year = $('select[name=\'filter_select_year\']').val();
  if (filter_select_year) {
    url += '&filter_select_year=' + encodeURIComponent(filter_select_year);
  }

  var filter_select_quarter = $('select[name=\'filter_select_quarter\']').val();
  if (filter_select_quarter) {
    url += '&filter_select_quarter=' + encodeURIComponent(filter_select_quarter);
  }

  var filter_year_range = $('select[name=\'filter_year_range\']').val();
  if (filter_year_range) {
    url += '&filter_year_range=' + encodeURIComponent(filter_year_range);
  }

  location = url;
});

$('.button-download-csv').on('click', function() {
  var val = $(this).attr('csvname');
   
  var base_url = 'index.php?route=seller_panel/gstreports&token=<?php echo $token; ?>'+'&download='+val;
  //var base_url = 'index.php?route=seller_panel/gstreports&token=<?php echo $token; ?>'+'&download=csvInvoice';

  var url = base_url;

  var filter_select_month = $('select[name=\'filter_select_month\']').val();
  if (filter_select_month) {
    url += '&filter_select_month=' + encodeURIComponent(filter_select_month);
  }

  var filter_select_year = $('select[name=\'filter_select_year\']').val();
  if (filter_select_year) {
    url += '&filter_select_year=' + encodeURIComponent(filter_select_year);
  }

  var filter_select_quarter = $('select[name=\'filter_select_quarter\']').val();
  if (filter_select_quarter) {
    url += '&filter_select_quarter=' + encodeURIComponent(filter_select_quarter);
  }

  var filter_year_range = $('select[name=\'filter_year_range\']').val();
  if (filter_year_range) {
    url += '&filter_year_range=' + encodeURIComponent(filter_year_range);
  }

  location = url;
});
///////////////////////////////////////
$('#input-payment-done-date').datepicker({
  pickTime: false,
  maxDate: new Date(),
});
//--></script>

<style type="text/css">
.btn{margin-right: 10px;}
.block{border: 1px solid rgb(221, 221, 221);padding: 3px;margin-bottom: 2px;}
</style>

<script>
jQuery(document).ready(function(){
  jQuery('.filter_select_month').change(function() {
    jQuery(".filter_select_quarter").prop('selectedIndex', 0);  
    //jQuery(".filter_year_range").prop('selectedIndex', 0);  
  });

  jQuery('.filter_select_quarter, .filter_year_range').change(function() {
    jQuery(".filter_select_month").prop('selectedIndex', 0);  
    //jQuery(".filter_select_year").prop('selectedIndex', 0);  

    //jQuery(".filter_year_range").prop('selectedIndex', 1);  
  });

});
</script>