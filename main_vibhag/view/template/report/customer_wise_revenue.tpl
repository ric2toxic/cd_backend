<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_customer_wise; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-customer"><?php echo $entry_customer; ?></label>
                <input type="text" name="filter_customer" id="input-customer" value="" class="form-control" placeholder="<?php echo $entry_customer; ?>">
              </div>
              <div class="form-group">
                <label class="control-label" for="input-shipping-city"><?php echo $entry_shipping_city; ?></label>
                <input type="text" name="filter_shipping_city" id="input-shipping-city" value="" class="form-control" placeholder="<?php echo $entry_shipping_city; ?>">
              </div>
            </div>
            
            
            <div class="col-sm-3">              
              <div class="form-group">
                <label class="control-label" for="input-customer-onboarding-date-from"><?php echo $entry_customer_onboarding_date_from; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_customer_onboarding_date_from" id="input-customer-onboarding-date-from" value="" placeholder="<?php echo $entry_customer_onboarding_date_from; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              
            <div class="form-group">
                <label class="control-label" for="input-customer-onboarding-date-to"><?php echo $entry_customer_onboarding_date_to; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_customer_onboarding_date_to" id="input-customer-onboarding-date-to" value="" placeholder="<?php echo $entry_customer_onboarding_date_to; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>              
            </div>
            
            
            <div class="col-sm-3">              

              <div class="form-group">
                <label class="control-label" for="input-sale-invoice-date-from"><?php echo $entry_sale_invoice_date_from; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_sale_invoice_date_from" id="input-sale-invoice-date-from" value="" placeholder="<?php echo $entry_sale_invoice_date_from; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              
                            
              <div class="form-group">
                <label class="control-label" for="input-sale-invoice-date-to"><?php echo $entry_sale_invoice_date_to; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_sale_invoice_date_to" id="input-sale-invoice-date-to" value="" placeholder="<?php echo $entry_sale_invoice_date_to; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              
            </div>
            
            <div class="col-sm-3">

              <div class="form-group">
                <label class="control-label" for="input-order-date-from"><?php echo $entry_order_date_from; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_order_date_from" id="input-order-date-from" value="" placeholder="<?php echo $entry_order_date_from; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              
              <div class="form-group">
                <label class="control-label" for="input-order-date-to"><?php echo $entry_order_date_to; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_order_date_to" id="input-order-date-to" value="" placeholder="<?php echo $entry_order_date_to; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              
              <div class="form-group">
                  <button type="button" 
                          id="button-download-csv" 
                          class="btn btn-primary pull-right">
                    <i class="fa fa-search"></i> <?php echo $button_download_csv; ?>
                  </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
    $('.date').datetimepicker({
        pickTime: false
    });
//--></script>

<script type="text/javascript"><!--
$('#button-download-csv').on('click', function() {

    var url = 'index.php?route=report/analysis/downloadCustomerWiseRevenueCSV&token=<?php echo $token; ?>';

    var filter_customer = $('input[name=\'filter_customer\']').val();
    if (filter_customer) {
        url += '&filter_customer=' + encodeURIComponent(filter_customer);
    }

    var filter_shipping_city = $('input[name=\'filter_shipping_city\']').val();
    if (filter_shipping_city) {
        url += '&filter_shipping_city=' + encodeURIComponent(filter_shipping_city);
    }

    var filter_customer_onboarding_date_from = $('input[name=\'filter_customer_onboarding_date_from\']').val();
    if (filter_customer_onboarding_date_from) {
        url += '&filter_customer_onboarding_date_from=' + encodeURIComponent(filter_customer_onboarding_date_from);
    }
    
    var filter_customer_onboarding_date_to = $('input[name=\'filter_customer_onboarding_date_to\']').val();
    if (filter_customer_onboarding_date_to) {
        url += '&filter_customer_onboarding_date_to=' + encodeURIComponent(filter_customer_onboarding_date_to);
    }


    var filter_sale_invoice_date_from = $('input[name=\'filter_sale_invoice_date_from\']').val();
    if (filter_sale_invoice_date_from) {
        url += '&filter_sale_invoice_date_from=' + encodeURIComponent(filter_sale_invoice_date_from);
    }

    var filter_sale_invoice_date_to = $('input[name=\'filter_sale_invoice_date_to\']').val();
    if (filter_sale_invoice_date_to) {
        url += '&filter_sale_invoice_date_to=' + encodeURIComponent(filter_sale_invoice_date_to);
    }
    
    
    var filter_order_date_from = $('input[name=\'filter_order_date_from\']').val();
    if (filter_order_date_from) {
        url += '&filter_order_date_from=' + encodeURIComponent(filter_order_date_from);
    }
    
    var filter_order_date_to = $('input[name=\'filter_order_date_to\']').val();
    if (filter_order_date_to) {
        url += '&filter_order_date_to=' + encodeURIComponent(filter_order_date_to);
    }
    
    location = url;    
});
//--></script>


</div>

<?php echo $footer; ?>
