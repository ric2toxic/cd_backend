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
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_product_wise_return; ?></h3>
      </div>
      <div class="panel-body">
		  <form action="<?php echo $form_action; ?>" method="post" id="form-product_rating">
		  <div class="well">
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label" for="input-date-from"><?php echo $order_date_from; ?></label>
								<div class="input-group date">
								<input type="text" name="filter_date_from" value="" placeholder="<?php echo $order_date_from; ?>" data-date-format="YYYY-MM-DD" id="input-date-from" class="form-control" />
								<span class="input-group-btn">
								<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
								</span></div>
							</div>
							<div class="form-group">
								<label class="control-label" for="input-date-to"><?php echo $order_date_to; ?></label>
								<div class="input-group date">
								<input type="text" name="filter_date_to" value="" placeholder="<?php echo $order_date_to; ?>" data-date-format="YYYY-MM-DD" id="input-date-to" class="form-control" />
								<span class="input-group-btn">
								<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
								</span></div>
							</div>
              <div class="form-group">
                <label class="control-label" for="input-order"><?php echo $entry_orders; ?></label>
                <input type="text" name="filter_order" value="" placeholder="<?php echo $entry_orders; ?>" id="input-order" class="form-control" />
                <input type="hidden" name="filter_order_no" value="" id="input-order_no" class="form-control" />
              </div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="input-model"><?php echo $entry_model; ?></label>
								<input type="text" name="filter_model" value="" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control" />
							</div>
							<div class="form-group">
                <label class="control-label" for="input-category"><?php echo $entry_category; ?></label>
                <input type="text" name="filter_category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" />
                <input type="hidden" name="filter_category_id" value="" id="input-category_id" class="form-control" />
              </div>
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
              <div class="form-group">
                <label class="control-label" for="input-seller"><?php echo $entry_seller; ?></label>
                <input type="text" name="filter_seller" value="" placeholder="<?php echo $entry_seller; ?>" id="input-seller" class="form-control" />
                <input type="hidden" name="filter_seller_id" value="" id="input-seller_id" class="form-control" />
              </div>
							
              <div class="form-group">
                  <label class="control-label" for="input-date-end"><?php echo $entry_seller_pickup_city; ?></label>
                  <select name="filter_seller_pickup_city_code" class="form-control">
                    <option value="">--SELECT--</option>
                    <?php if(!empty( $seller_pickup_city_code ) ) {?>
                    <?php foreach( $seller_pickup_city_code  as $city_code) { ?>
                      <option value="<?php echo $city_code; ?>"><?php echo $city_code; ?></option>
                    <?php } ?>  
                    <?php } ?>
                  </select>
              </div>


							<div class="form-group">
								<button type="submit" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_download_csv; ?></button>
							</div>
						</div>
					</div>
			  </div>
		</div>
      </div>
    </div>
  </div>
  </div>

<?php echo $footer; ?>
<script type="text/javascript">
    $('.date').datetimepicker({
        pickTime: false
    });

    // Seller
    $('input[name=\'filter_seller\']').on('keyup', function() {
        $('input[name=\'filter_seller\']').autocomplete({
            'source': function(request, response) {
                $.ajax({
                    url: 'index.php?route=sellers/sellers/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
                    dataType: 'json',
                    success: function(json) {
                        response($.map(json, function(item) {
                            return {
                                label: item['name'],
                                value: item['seller_id']
                            }
                        }));
                    }
                });
            },
            'select': function(item) {
                $('input[name=\'filter_seller\']').val(item['label']);
                $('input[name=\'filter_seller_id\']').val(item['value']);
            }
        });
    });

    // Category
    $('input[name=\'filter_category\']').on('keyup', function() {
        $('input[name=\'filter_category\']').autocomplete({
            'source': function(request, response) {
                $.ajax({
                    url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
                    dataType: 'json',
                    success: function(json) {
                        response($.map(json, function(item) {
                            return {
                                label: item['name'],
                                value: item['category_id']
                            }
                        }));
                    }
                });
            },
            'select': function(item) {
                $('input[name=\'filter_category\']').val(item['label']);
                $('input[name=\'filter_category_id\']').val(item['value']);
            }
        });
    });

</script>
