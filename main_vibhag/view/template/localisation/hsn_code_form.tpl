<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-category" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-category" class="form-horizontal">
          <div class="tab-content">
			<div class="form-group required">
                <label class="col-sm-2 control-label" for="input-filter"><span data-toggle="tooltip" title="<?php echo $help_hsn_code; ?>"><?php echo $entry_hsn_code; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="hsn_code" value="<?php echo $hsn_code; ?>" placeholder="<?php echo $entry_hsn_code; ?>" class="form-control" />
                  <?php if ($error_hsn_code) { ?>
					  <div class="text-danger"><?php echo $error_hsn_code; ?></div>
				  <?php } ?>  
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-filter"><span data-toggle="tooltip" title="<?php echo $help_hsn_description; ?>"><?php echo $entry_hsn_description; ?></span></label>
                <div class="col-sm-10">
                  <textarea rows = "10" name="hsn_description" id="input-hsn-description" placeholder="<?php echo $entry_hsn_description; ?>" class="form-control" ><?php echo $hsn_description; ?></textarea>
                   <?php if ($error_hsn_description) { ?>
					  <div class="text-danger"><?php echo $error_hsn_description; ?></div>
				  <?php } ?>  
                </div>
              </div>
			</div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-filter"><span data-toggle="tooltip" title="<?php echo $help_hsn_tax_rule; ?>"><?php echo $entry_tax_class_id; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="hsn_class_id" value="<?php echo $tax_class_title; ?>" placeholder="<?php echo $entry_tax_class_id; ?>" class="form-control" />
                  <?php if ($error_hsn_class_id) { ?>
					<div class="text-danger"><?php echo $error_hsn_class_id; ?></div>
				  <?php } ?>
				  <input type="hidden" name="tax_class_id" id="tax_class_id" value="<?php echo $tax_class_id; ?>">
                </div>
              </div>
          </div>
        </form>
      </div>
    </div>
  </div>

<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript">

	$('input[name=\'hsn_class_id\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=localisation/hsn_code/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['title'],
							value: item['tax_class_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'hsn_class_id\']').val(item['label']);
			$('#tax_class_id').val(item['value']);
		}
	});
</script>
<?php echo $footer; ?>
