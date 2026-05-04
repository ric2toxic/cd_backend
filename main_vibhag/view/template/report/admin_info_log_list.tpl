<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
       <!--  <div class="pull-right">
        <?php if( $show_csv_button ) { ?>
          <label>CSV:</label>
          <a href="<?php echo $b2c_csv; ?>" data-toggle="tooltip" id="download_csv" title="<?php echo $button_csv; ?>" class="btn btn-warning"><i class="fa fa-file-excel-o"></i></a>
        <?php } ?>
        </div> -->
      <h1><?php echo $heading_admin_info_log; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_admin_info_log_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-4">

              <div class="form-group">
                <label class="control-label" for="input-order-no"><?php echo $entry_field_type; ?></label>
                <select name="filter_field_type" id="input-sales-staff" class="form-control">
                  <option value="*">--Select--</option>
                  <?php foreach( $all_field_type as $field_type_name ){ ?>
                  <option value="<?php echo $field_type_name; ?>" <?php echo ($field_type_name== strtolower($filter_field_type)) ? 'selected' :''; ?>><?php echo ucfirst($field_type_name); ?></option>
                  <?php } ?>
                </select>
              </div>

              <div class="form-group">
                <label class="control-label" for="input-sales-staff"><?php echo $entry_field_value; ?></label>
                <input type="text" name="filter_field_value" value="<?php echo $filter_field_value; ?>" placeholder="<?php echo $entry_field_type; ?>" id="input-order-no" class="form-control" />                
              </div>

            </div>
            <div class="col-sm-4">
               <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_from; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_from" value="<?php echo $filter_date_from; ?>" placeholder="<?php echo $entry_date_from; ?>" data-date-format="YYYY-MM-DD HH:mm:ss" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-city"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-city" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_to; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_to" value="<?php echo $filter_date_to; ?>" placeholder="<?php echo $entry_date_to; ?>" data-date-format="YYYY-MM-DD HH:mm:ss" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-order-status"><?php echo $entry_user_group; ?></label>
                <select name="filter_user_group" id="input-order-status" class="form-control">
                  <option value="*">--Select--</option>
                  <?php foreach( $all_user_group as $all_user_group ){ ?>
                  <option value="<?php echo $all_user_group; ?>" <?php echo (strtolower($all_user_group) == strtolower($filter_user_group)) ? 'selected' : '';?> ><?php echo ucfirst($all_user_group); ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                  <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
              </div>

            </div>
          </div>
        </div>
        <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td class="text-left">
                    <?php echo $column_field_type; ?>
                  </td>
                  <td class="text-left">
                    <?php echo $column_field_value; ?>
                  </td>
                  <td class="text-left">
                    <?php echo $column_name; ?>
                  </td>
                  <td class="text-left">
                    <?php echo $column_user_group; ?>
                  </td>
                  <td class="text-left">
                    <?php echo $column_click_no; ?>
                  </td>
                </tr>
              </thead>
              <tbody>
                <?php if ( $records ) { ?>
                  <?php foreach ($records as $record) { ?>
                      <tr>
                        <td><?php echo $record['field_name'];?></td>  
                        <td><?php echo $record['new_value'];?></td>  
                        <td><?php echo $record['name'];?></td>  
                        <td><?php echo $record['user_group'];?></td>  
                        <td><?php echo $record['total'];?></td>  
                      </tr>
                  <?php } ?>
                <?php } else { ?>
                      <tr>
                        <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                      </tr>
                  <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript">
  $('#button-filter').on('click', function() {
      url = 'index.php?route=report/admin_info_log&token=<?php echo $token; ?>';

      var filter_field_type = $('select[name=\'filter_field_type\']').val();

      if (filter_field_type != '*') {
          url += '&filter_field_type=' + encodeURIComponent(filter_field_type);
      }

      var filter_field_value = $('input[name=\'filter_field_value\']').val();

      if (filter_field_value ) {
          url += '&filter_field_value=' + encodeURIComponent(filter_field_value);
      }

      var filter_date_from = $('input[name=\'filter_date_from\']').val();

      if (filter_date_from) {
          url += '&filter_date_from=' + encodeURIComponent(filter_date_from);
      }

      var filter_date_to = $('input[name=\'filter_date_to\']').val();

      if (filter_date_to) {
          url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
      }

      var filter_name = $('input[name=\'filter_name\']').val();

      if (filter_name) {
        url += '&filter_name=' + encodeURIComponent(filter_name);
      }

      var filter_user_group = $('select[name=\'filter_user_group\']').val();

      if (filter_user_group != '*') {
        url += '&filter_user_group=' + encodeURIComponent(filter_user_group);
      }

      location = url;
  });

  $('input[name=\'filter_field_value\'], input[name=\'filter_date_from\'], input[name=\'filter_date_to\'], input[name=\'filter_name\'], select[name=\'filter_user_group\'], select[name=\'filter_field_type\']').on('keypress',function(e){
    if (e.keyCode == 13) {
      $('#button-filter').trigger('click');
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

//--></script></div>
<?php echo $footer; ?>