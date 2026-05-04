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
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_seller_wise; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-end"><?php echo $entry_seller_list; ?></label>
                <select name="filter_seller_id" class="form-control">
                  <option value="*">--SELECT--</option>
                  <option value="0">&nbsp;&nbsp;&nbsp;&nbsp;ALL</option>
                  <?php if(!empty( $seller_list ) ) {?>
                  <?php foreach( $seller_list  as $seller_value) { ?>
                    <option value="<?php echo $seller_value['seller_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $seller_value['nickname']; ?> ( <?php echo $seller_value['company']; ?> )</option>
                  <?php } ?>  
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-date-end"><?php echo $entry_categories; ?></label>
                <select name="filter_category" class="form-control">
                  <option value="*">--SELECT--</option>
                  <option value="0">All Categories</option>
                   <?php foreach ($categories as $category_1) { ?>
                   <?php if ($category_1['category_id'] == $filter_category) { ?>
                   <option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
                   <?php } else { ?>
                   <option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
                   <?php } ?>
                   <?php foreach ($category_1['children'] as $category_2) { ?>
                   <?php if ($category_2['category_id'] == $filter_category) { ?>
                   <option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
                   <?php } else { ?>
                   <option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
                   <?php } ?>
                   <?php foreach ($category_2['children'] as $category_3) { ?>
                   <?php if ($category_3['category_id'] == $filter_category) { ?>
                   <option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
                   <?php } else { ?>
                   <option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
                   <?php } ?>
                   <?php } ?>
                   <?php } ?>
                   <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-date-end"><?php echo $entry_seller_pickup_city; ?></label>
                <select name="filter_seller_pickup_city_code" class="form-control">
                  <option value="*">--SELECT--</option>
                  <?php if(!empty( $seller_pickup_city_code ) ) {?>
                  <?php foreach( $seller_pickup_city_code  as $city_code) { ?>
                    <option value="<?php echo $city_code; ?>"><?php echo $city_code; ?></option>
                  <?php } ?>  
                  <?php } ?>
                </select>
              </div>              
            </div>
            <div class="col-sm-3">              
              <div class="form-group">
                <label class="control-label" for="input-date-start"><?php echo $entry_seller_date_added_from; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_seller_date_added_from" value="" placeholder="<?php echo $entry_seller_date_added_from; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              
              <div class="form-group">
                <label class="control-label" for="input-date-end"><?php echo $entry_seller_date_added_to; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_seller_date_added_to" value="" placeholder="<?php echo $entry_seller_date_added_to; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
                            
            </div>
            <div class="col-sm-3">
                
              <div class="form-group">
                <label class="control-label" for="input-date-start"><?php echo $entry_sale_invoice_date_from; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_sale_invoice_date_from" value="" placeholder="<?php echo $entry_sale_invoice_date_from; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              
              <div class="form-group">
                <label class="control-label" for="input-date-end"><?php echo $entry_sale_invoice_date_to; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_sale_invoice_date_to" value="" placeholder="<?php echo $entry_sale_invoice_date_to; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
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

    var url = 'index.php?route=report/analysis/downloadSellerWiseRevenueCSV&token=<?php echo $token; ?>';

    var filter_seller_id = $('select[name=\'filter_seller_id\']').val();
    if (filter_seller_id && filter_seller_id != '*') {
        url += '&filter_seller_id=' + encodeURIComponent(filter_seller_id);
    }

    var filter_category = $('select[name=\'filter_category\']').val();
    if (filter_category && filter_category !='*') {
        url += '&filter_category=' + encodeURIComponent(filter_category);
    }

    var filter_seller_date_added_from = $('input[name=\'filter_seller_date_added_from\']').val();
    if (filter_seller_date_added_from) {
        url += '&filter_seller_date_added_from=' + encodeURIComponent(filter_seller_date_added_from);
    }

    var filter_seller_date_added_to = $('input[name=\'filter_seller_date_added_to\']').val();
    if (filter_seller_date_added_to) {
        url += '&filter_seller_date_added_to=' + encodeURIComponent(filter_seller_date_added_to);
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
    
    location = url;    
});
//--></script>


</div>

<?php echo $footer; ?>
