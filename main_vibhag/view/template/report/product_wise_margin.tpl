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
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_product_wise; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
            
          <div class="row">
              
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="input-wsb-product-code"><?php echo $entry_wsb_product_code; ?></label>
                    <input type="text" name="filter_wsb_product_code" id="input-wsb-product-code" value="" class="form-control" placeholder="<?php echo $entry_wsb_product_code; ?>">
                </div>
            </div>
            
            <div class="col-sm-4">              
                <div class="form-group">
                    <label class="control-label" for="input-seller-sku-code"><?php echo $entry_seller_sku_code; ?></label>
                    <input type="text" name="filter_seller_sku_code" id="input-seller-sku-code" value="" class="form-control" placeholder="<?php echo $entry_seller_sku_code; ?>">
                </div>
            </div>
            
            <div class="col-sm-4">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="input-order-date-from"><?php echo $entry_order_date_from; ?></label>
                        <div class="input-group date">
                            <input type="text" name="filter_order_date_from" id="input-order-date-from" value="" placeholder="<?php echo $entry_order_date_from; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="input-order-date-to"><?php echo $entry_order_date_to; ?></label>
                        <div class="input-group date">
                            <input type="text" name="filter_order_date_to" id="input-order-date-to" value="" placeholder="<?php echo $entry_order_date_to; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            </div>           

        </div>
        
        <div class="row">
            
            <div class="col-sm-4">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="input-seller-list"><?php echo $entry_seller_list; ?></label>
                        <select name="filter_seller_id" id="input-seller-list" class="form-control">
                        <option value="*">--SELECT--</option>
                        <option value="0">&nbsp;&nbsp;&nbsp;&nbsp;ALL</option>
                        <?php if(!empty( $seller_list ) ) {?>
                            <?php foreach( $seller_list  as $seller_value) { ?>
                            <option value="<?php echo $seller_value['seller_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $seller_value['nickname']; ?> ( <?php echo $seller_value['company']; ?> )</option>
                            <?php } ?>  
                        <?php } ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="input-seller-pickup-city-code"><?php echo $entry_seller_pickup_city; ?></label>
                        <select name="filter_seller_pickup_city_code" id="input-seller-pickup-city-code" class="form-control">
                        <option value="*">--SELECT--</option>
                        <?php if(!empty( $seller_pickup_city_code ) ) {?>
                            <?php foreach( $seller_pickup_city_code  as $city_code) { ?>
                            <option value="<?php echo $city_code; ?>"><?php echo $city_code; ?></option>
                            <?php } ?>  
                        <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            </div>

            
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="input-customer"><?php echo $entry_customer; ?></label>
                    <input type="text" name="filter_customer" id="input-customer" value="" class="form-control" placeholder="<?php echo $entry_customer; ?>">
                </div>
            </div>
            
            <div class="col-sm-4">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="input-sale-invoice-date-from"><?php echo $entry_sale_invoice_date_from; ?></label>
                        <div class="input-group date">
                            <input type="text" name="filter_sale_invoice_date_from" value="" placeholder="<?php echo $entry_sale_invoice_date_from; ?>" data-date-format="YYYY-MM-DD" id="input-sale-invoice-date-from" class="form-control" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="input-sale-invoice-date-to"><?php echo $entry_sale_invoice_date_to; ?></label>
                        <div class="input-group date">
                            <input type="text" name="filter_sale_invoice_date_to" value="" placeholder="<?php echo $entry_sale_invoice_date_to; ?>" data-date-format="YYYY-MM-DD" id="input-sale-invoice-date-to" class="form-control" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            
        </div>
        
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="input-seller-pickup-city-code"><?php echo $entry_brand_name; ?></label>
                    <select name="filter_brand_name" id="filter_brand_name" class="form-control">
                    <option value="">--SELECT--</option>
                    <?php if(!empty( $filters ) ) {?>
                        <?php foreach( $filters  as $filter){ ?>
                        <option value="<?php echo $filter['filter_id']; ?>" ><?php echo $filter['name']; ?></option>
                        <?php } ?>  
                    <?php } ?>
                    </select>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="input-include_non_invoiced_orders"><?php echo $entry_include_non_invoiced_orders; ?></label>
                            <input type="checkbox" name="filter_include_non_invoiced_orders" class="filter_include_non_invoiced_orders form-control" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-group-by-order"><?php echo $entry_group_by_order; ?></label>
                                <input type="checkbox" name="filter_group_by_order" class="filter_group_by_order form-control" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                
            <div class="form-group">
                <button type="button" id="button-download-csv" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_download_csv; ?></button>
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

    var url = 'index.php?route=report/analysis/downloadProductWiseMarginCSV&token=<?php echo $token; ?>';

    var filter_wsb_product_code = $('input[name=\'filter_wsb_product_code\']').val();
    if (filter_wsb_product_code) {
        url += '&filter_wsb_product_code=' + encodeURIComponent(filter_wsb_product_code);
    }

    var filter_seller_sku_code = $('input[name=\'filter_seller_sku_code\']').val();
    if (filter_seller_sku_code) {
        url += '&filter_seller_sku_code=' + encodeURIComponent(filter_seller_sku_code);
    }

    var filter_seller_id = $('select[name=\'filter_seller_id\']').val();
    if (filter_seller_id && filter_seller_id != '*') {
        url += '&filter_seller_id=' + encodeURIComponent(filter_seller_id);
    }

    var filter_brand_name = $('select[name=\'filter_brand_name\']').val();
    if (filter_brand_name && filter_brand_name != '') {
        url += '&filter_brand_name=' + encodeURIComponent(filter_brand_name);
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

    var filter_seller_pickup_city_code = $('select[name=\'filter_seller_pickup_city_code\']').val();
    if (filter_seller_pickup_city_code && filter_seller_pickup_city_code != '*') {
        url += '&filter_seller_pickup_city_code=' + encodeURIComponent(filter_seller_pickup_city_code);
    }
    
    if($('.filter_group_by_order').is(':checked')){
        url += '&filter_group_by_order=' + encodeURIComponent(1);
    }
    
    if($('.filter_include_non_invoiced_orders').is(':checked')){
      url += '&filter_include_non_invoiced_orders=' + encodeURIComponent(1);
    }else{
      url += '&filter_include_non_invoiced_orders=' + encodeURIComponent(0);
    }

    var filter_customer = $('input[name=\'filter_customer\']').val();
    if (filter_customer) {
        url += '&filter_customer=' + encodeURIComponent(filter_customer);
    }
    
    location = url;    
});
//--></script>


</div>

<?php echo $footer; ?>
