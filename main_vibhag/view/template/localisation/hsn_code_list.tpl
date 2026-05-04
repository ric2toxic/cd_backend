<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-category').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
		  <div class="well">
			  <div class="row">
				  <div class="col-sm-3">
					  <div class="form-group">
						  <label class="control-label" for="input-hsn_code"><?php echo $entry_hsn_code; ?></label>
						  <input type="text" name="filter_hsn_code" value="<?php echo $filter_hsn_code; ?>" placeholder="<?php echo $entry_hsn_code; ?>" id="input-hsn_code" class="form-control" />
                      </div>
                   </div>
                   <div class="col-sm-3">
					   <div class="form-group">
						   <label class="control-label" for="input-description"><?php echo $entry_hsn_description; ?></label>
						   <input type="text" name="filter_description" value="<?php echo $filter_hsn_description; ?>" placeholder="<?php echo $entry_hsn_description; ?>" id="input-description" class="form-control" />
						</div>
					</div>   
                    <div class="col-sm-3">
						<button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
				    </div>
               </div>
            </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-category">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td rowspan="2" style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_hsn_code; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_hsn_code; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'sort_order') { ?>
                    <a href="<?php echo $sort_description; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_hsn_description; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_description; ?>"><?php echo $column_hsn_description; ?></a>
                    <?php } ?></td>
                   <td class="text-right"><?php if ($sort == 'sort_order') { ?>
                    <a href="<?php echo $sort_hsn_class_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_tax_class_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_hsn_class_id; ?>"><?php echo $column_tax_class_id; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($hsn_codes) { ?>
                <?php foreach ($hsn_codes as $hsn_code) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($hsn_code['hsn_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $hsn_code['hsn_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $hsn_code['hsn_id']; ?>"/>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $hsn_code['hsn_code']; ?></td>
                  <td class="text-right"><?php echo $hsn_code['hsn_description']; ?></td>
                  <td class="text-right"><?php echo $hsn_code['tax_class_title']; ?></td>
                  <td class="text-right"><a href="<?php echo $hsn_code['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
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
</div>
<?php echo $footer; ?>

<script type="text/javascript">
    $('#button-filter').click(function(){
        url = 'index.php?route=localisation/hsn_code&token=<?php echo $token; ?>';

        var filter_hsn_code = $('input[name=\'filter_hsn_code\']').val();
   
        if(filter_hsn_code){
            url += '&filter_hsn_code=' + encodeURIComponent(filter_hsn_code);
        }
        
        var filter_description = $('input[name=\'filter_description\']').val();

        if(filter_description){
            url += '&filter_description=' + encodeURIComponent(filter_description);
        }
       location = url;
    });
    
    $('input[name=\'filter_hsn_code\'], input[name=\'filter_description\']').on('keypress',function(e){
    if (e.keyCode == 13) {
      $('#button-filter').trigger('click');
    }
  });
</script>
