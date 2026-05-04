<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_purchase_return_reports; ?></h1>
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
        <h3 class="panel-title"><i class="fa fa-list"></i><?php echo $heading_purchase_return_reports; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">

            <div class="col-sm-4">
               <div class="form-group">
                <label class="control-label" for="input-order-date-from"><?php echo $entry_order_date_from; ?></label>
                <div class="input-group date">
                  <input type="text"
                         name="filter_order_date_from"
                         value="<?php echo $filter_order_date_from; ?>"
                         placeholder="<?php echo $entry_order_date_from; ?>"
                         data-date-format="YYYY-MM-DD"
                         id="input-order-date-from"
                         class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
               </div>

               <div class="form-group">
                <label class="control-label" for="input-wsb-inv-date-from"><?php echo $entry_debit_note_date_from; ?></label>
                <div class="input-group date">
                  <input type="text"
                         name="filter_debit_note_date_from"
                         value="<?php  ?>"
                         placeholder="<?php echo $entry_debit_note_date_from; ?>"
                         data-date-format="YYYY-MM-DD"
                         id="input-wsb-inv-date-from"
                         class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
               </div>
            </div>

            <div class="col-sm-4">

              <div class="form-group">
                <label class="control-label" for="input-order-date-to"><?php echo $entry_order_date_to; ?></label>
                <div class="input-group date">
                  <input type="text"
                         name="filter_order_date_to"
                         value="<?php echo $filter_order_date_to; ?>"
                         placeholder="<?php echo $entry_order_date_to; ?>"
                         data-date-format="YYYY-MM-DD"
                         id="input-order-date-to"
                         class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label" for="input-wsb-inv-date-to"><?php echo $entry_debit_note_date_to; ?></label>
                <div class="input-group date">
                  <input type="text"
                         name="filter_debit_note_date_to"
                         value="<?php echo $filter_debit_note_date_to; ?>"
                         placeholder="<?php echo $entry_debit_note_date_to; ?>"
                         data-date-format="YYYY-MM-DD"
                         id="input-wsb-inv-date-to"
                         class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>

            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-supplier-id"><?php echo $entry_filter_supplier_id; ?></label>
                  <input type="text"
                         name="filter_supplier_id"
                         value="<?php echo $filter_supplier_id; ?>"
                         placeholder="<?php echo $entry_filter_supplier_id; ?>"
                         id="input-supplier-id"
                         class="form-control" />
              </div>

              <div class="form-group">
                  <label class="control-label" for="input-supplier-id"><?php echo $entry_filter_allow_wsb_order; ?></label>
                  <?php $str = ''; ?>
                  <?php if($filter_allow_wsb_order == 1) { $str = 'checked="checked"'; } ?>
                  <input type="checkbox"
                         name="filter_allow_wsb_order"
                         id="filter_allow_wsb_order"
                         <?php echo $str; ?>
                         class="form-control" />
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
$('#button-download-csv').on('click', function() {
    var base_url = 'index.php?route=accounts/purchasereturnreports&token=<?php echo $token . ( $is_tally ? "&tally=1" : ''  ); ?>';
	var url = base_url;

    var filter_order_date_from = $('input[name=\'filter_order_date_from\']').val();
    if (filter_order_date_from) {
        url += '&filter_order_date_from=' + encodeURIComponent(filter_order_date_from);
    }

    var filter_order_date_to = $('input[name=\'filter_order_date_to\']').val();
    if (filter_order_date_to) {
        url += '&filter_order_date_to=' + encodeURIComponent(filter_order_date_to);
    }

    var filter_debit_note_date_from = $('input[name=\'filter_debit_note_date_from\']').val();
    if (filter_debit_note_date_from) {
        url += '&filter_debit_note_date_from=' + encodeURIComponent(filter_debit_note_date_from);
    }

    var filter_debit_note_date_to = $('input[name=\'filter_debit_note_date_to\']').val();

    if (filter_debit_note_date_to) {
        url += '&filter_debit_note_date_to=' + encodeURIComponent(filter_debit_note_date_to);
    }

    var filter_supplier_id = $('input[name=\'filter_supplier_id\']').val();

    if (filter_supplier_id) {
        url += '&filter_supplier_id=' + encodeURIComponent(filter_supplier_id);
    }

    var filter_allow_wsb_order = 0;
    if ($('#filter_allow_wsb_order').prop("checked") == true ) {
      filter_allow_wsb_order = 1;
    }
    url += '&filter_allow_wsb_order=' + encodeURIComponent(filter_allow_wsb_order);

    if (base_url == url) {
        alert(<?php echo "'" . $error_warning . "'"; ?>);
    } else {
        location = url;
    }

});
//--></script>

<!-- Auto complete scripts -->
<script type="text/javascript"><!--
$('.date').datetimepicker({
   pickTime: false,
});


//--></script>
</div>
<?php echo $footer; ?>
