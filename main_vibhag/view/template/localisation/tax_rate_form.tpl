<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-tax-rate" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-tax-rate" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
          <!--
          <div class="form-group required">				
				<label class="col-sm-2 control-label" for="input-rate"><?php echo "Start Range"; ?></label>
				<div class="col-sm-2"> 
				  <input type="text" name="rate" value="<?php echo $rate; ?>" placeholder="<?php echo $entry_rate; ?>" id="input-rate" class="form-control" />
				  <?php if ($error_rate) { ?>
				  <div class="text-danger"><?php echo $error_rate; ?></div>
				  <?php } ?>
				</div>
				
				<label class="col-sm-2 control-label" for="input-rate"><?php echo "End Range"; ?></label>
				<div class="col-sm-2"> 
				  <input type="text" name="rate" value="<?php echo $rate; ?>" placeholder="<?php echo $entry_rate; ?>" id="input-rate" class="form-control" />
				  <?php if ($error_rate) { ?>
				  <div class="text-danger"><?php echo $error_rate; ?></div>
				  <?php } ?>
				</div>
				<label class="col-sm-2 control-label" for="input-rate"><?php echo "Tax Rate"; ?></label>
				<div class="col-sm-2"> 
				  <input type="text" name="rate" value="<?php echo $rate; ?>" placeholder="<?php echo $entry_rate; ?>" id="input-rate" class="form-control" />
				  <?php if ($error_rate) { ?>
				  <div class="text-danger"><?php echo $error_rate; ?></div>
				  <?php } ?>
				</div>
          </div>
          -->
          <div class="form-group required">
			<label class="col-sm-2 control-label" for="input-rate"><?php echo "Tax Rate above End Range"; ?></label>
			<div class="col-sm-10">
				<input type="text" name="tax_rate_end_range" value="<?php echo $tax_rate_end_range; ?>" placeholder="<?php echo $entry_above_rate; ?>" id="input-rate" class="form-control" />
				  <?php if ($error_tax_rate_end_range) { ?>
				  <div class="text-danger"><?php echo $error_tax_rate_end_range; ?></div>
				  <?php } ?>
			</div>
			  
          </div>
         
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-type"><?php echo $entry_type; ?></label>
            <div class="col-sm-10">
              <select name="type" id="input-type" class="form-control">
                <?php if ($type == 'P') { ?>
                <option value="P" selected="selected"><?php echo $text_percent; ?></option>
                <?php } else { ?>
                <option value="P"><?php echo $text_percent; ?></option>
                <?php } ?>
                <?php if ($type == 'F') { ?>
                <option value="F" selected="selected"><?php echo $text_amount; ?></option>
                <?php } else { ?>
                <option value="F"><?php echo $text_amount; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>        
          
		<table id="tax-rule" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left"><?php echo 'Start Range'; //$entry_based; ?></td>
                <td class="text-left"><?php echo 'End Range'; //$entry_priority; ?></td>
                <td class="text-left"><?php echo 'Tax Rate'; //$entry_priority; ?></td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              <?php $tax_rule_row = 0;?>
              <?php if(!empty($tax_arr)) { foreach ($tax_arr as $key=>$tax_field) { if($tax_field['end_range'] == 1e10) break; ?>
              <tr id="tax-rule-row<?php echo $tax_rule_row; ?>">
                <td class="text-left"><input type="text" name="tax_rule[<?php echo $tax_rule_row; ?>][start_range]" value="<?php echo $tax_field['start_range']; ?>" placeholder="<?php echo $entry_start_range; ?>" class="form-control" /></td>
                <td class="text-left"><input type="text" name="tax_rule[<?php echo $tax_rule_row; ?>][end_range]" value="<?php echo $tax_field['end_range']; ?>" placeholder="<?php echo $entry_end_range; ?>" class="form-control" /></td>
                <td class="text-left"><input type="text" name="tax_rule[<?php echo $tax_rule_row; ?>][rate]" value="<?php echo $tax_field['rate']; ?>" placeholder="<?php echo $entry_tax_rate; ?>" class="form-control" /></td>
                <td class="text-left"><button type="button" onclick="$('#tax-rule-row<?php echo $tax_rule_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
              </tr>
              <?php $tax_rule_row++; //if($tax_rule_row == sizeof($tax_fields)-1) {break;}?>
              <?php } }?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3"></td>
                <td class="text-left"><button type="button" onclick="addRule();" data-toggle="tooltip" title="<?php echo $button_rule_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
              </tr>
            </tfoot>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript"><!--
var tax_rule_row = <?php echo $tax_rule_row; ?>;

function addRule() {
	html  = '<tr id="tax-rule-row' + tax_rule_row + '">';
	html += '  <td class="text-left">';
	html += '    <input type="text" name="tax_rule[' + tax_rule_row + '][start_range]" value="" placeholder="<?php echo $entry_start_range; ?>" id="input-rate" class="form-control" />';
    html += '  </td>';
	html += '  <td class="text-left">';
    html += '    <input type="text" name="tax_rule[' + tax_rule_row + '][end_range]" value="" placeholder="<?php echo $entry_end_range; ?>" id="input-rate" class="form-control" />';
    html += '  </td>';
	html += '  <td class="text-left"><input type="text" name="tax_rule[' + tax_rule_row + '][rate]" value="" placeholder="<?php echo $entry_tax_rate; ?>" id="input-rate" class="form-control" /></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#tax-rule-row' + tax_rule_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';
	
	$('#tax-rule tbody').append(html);
	
	tax_rule_row++;
}
//--></script>

<script>
	
  var i = 0;
  
  $('.add_more_rates').click(function () {
    var html = '';
    html += '<div style="padding-top: 10px;" class="rates col-sm-12"><label class="col-sm-2 control-label" for="input-weight"><span data-toggle="tooltip" title="Weight">Weight (Kg)</span></label>';
    html += '<div class="col-sm-3"><input type="text" placeholder="Weight" data-identity="weight" id="weight_cost'+$(this).attr('data-geo')+'_'+$(this).attr('data-geo')+''+10+''+i+'" class="form-control shake" name="weight_'+$(this).attr('data-geo')+'_meta[cost_by_weight_range]['+$(this).attr('data-geo')+''+10+''+i+'][weight]" /></div>';
    html += '<label class="col-sm-2 control-label" for="input-cost"><span data-toggle="tooltip" title="Cost">Cost per kg (INR)</span></label>';
    html += '<div class="col-sm-3"><input type="text" placeholder="Cost" data-identity="cost" id="cost_weight'+$(this).attr('data-geo')+'_'+$(this).attr('data-geo')+''+10+''+i+'" class="form-control shake" name="weight_'+$(this).attr('data-geo')+'_meta[cost_by_weight_range]['+$(this).attr('data-geo')+''+10+''+i+'][cost]" /></div>';
    html += '<div class="col-sm-2"><button type="button" data-geo="" style="border: 1px solid #ccc; color: black; text-align: center; font-size: 12px; font-weight: 400; line-height: 30px; width:100%;" class="delete_rows">DELETE</button></div></div>';
    $('#rates_container_'+$(this).attr('data-geo')).append($(html));
    i++;
  });

</script>
<?php echo $footer; ?>
