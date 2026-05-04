<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" style="display: none" id="save" form="form-weight" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
      <div style="height:60px; padding-top:12px; background-color:rgb(252,252,252); border-bottom: 1px solid rgb(232,232,232);">
        <h3 class="panel-title col-sm-4" style="font-size:16px; font-weight:500; margin-top:8px;"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
        <div class="col-sm-6 pull-right">
          <div class="col-sm-10" style="padding: 0;"><input type="text" style="border-radius:0; border-right: none;" placeholder="Search" class="form-control" id="search" name="search" value="<?php echo $search; ?>" /></div>
          <div class="col-sm-1" style="height:35px; border:1px solid #ccc; background-color:white; border-left:none;"><a href="<?php echo $action; ?>" class="search" ><i class="fa fa-search" style="color:black; font-size:22px; margin-top:5px; margin-left:-4px;"></i></a></div>
        </div>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-weight" class="form-horizontal">
          <div class="row">

            <div class="col-sm-2">
              <ul class="nav nav-pills nav-stacked">
                <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <li><a href="#tab-geo-zone<?php echo $geo_zone['geo_zone_id']; ?>" data-toggle="tab"><?php echo $geo_zone['name']; ?></a></li>
                <?php } ?>
              </ul>
            </div>
            <div class="col-sm-10">

              <div class="tab-content">
                <div class="tab-pane active" id="tab-general">
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-tax-class"><?php echo $entry_tax_class; ?></label>
                    <div class="col-sm-10">
                     <!-- As for Now we are not handle this case due to split order total calculation -->
                      <select style="display:none" name="weight_tax_class_id" id="input-tax-class" class="form-control">
                        <option value="0"><?php echo $text_none; ?></option>
                        <?php foreach ($tax_classes as $tax_class) { ?>
                        <?php if ($tax_class['tax_class_id'] == $weight_tax_class_id) { ?>
                        <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                    <div class="col-sm-10">
                      <select name="weight_status" id="input-status" class="form-control">
                        <?php if ($weight_status) { ?>
                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                        <option value="0"><?php echo $text_disabled; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $text_enabled; ?></option>
                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="weight_sort_order" value="<?php echo $weight_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                    </div>
                  </div>
                </div>
                <?php $i = 0; ?>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if (${'weight_' . $geo_zone['geo_zone_id'] . '_check'} == 0) { ?>
                <?php $display_excel_rates = "display_none"; ?>
                <?php $display_rates = "display_block;"; ?>
                <?php } else { ?>
                <?php $display_excel_rates = "display_block"; ?>
                <?php $display_rates = "display_none"; ?>
                <?php } ?>
                <div class="tab-pane" id="tab-geo-zone<?php echo $geo_zone['geo_zone_id']; ?>">
                  <div class="form-group show_rates_onclick" data-geo="<?php echo $geo_zone['geo_zone_id']; ?>" style="padding: 10px; z-index: 10; margin-bottom:0; border: 1px solid rgb(232,232,232); border-top: 2px solid rgb(191,191,191); height: 40px; cursor: pointer; background-color: rgb(252,252,252); text-align: center; font-size: 16px; font-weight: 500;">Rates as string </div>
                  <div class="form-group <?php echo $display_excel_rates; ?>" id="show_rates_onclick_<?php echo $geo_zone['geo_zone_id']; ?>" style="border-bottom: 1px solid rgb(232,232,232); border-left: 1px solid rgb(232,232,232); padding: 15px;">
                    <label class="col-sm-2 control-label" for="input-rate<?php echo $geo_zone['geo_zone_id']; ?>"><span data-toggle="tooltip" title="<?php echo $help_rate; ?>"><?php echo $entry_rate; ?></span></label>
                    <div class="col-sm-10">
                      <textarea id="weight_<?php echo $geo_zone['geo_zone_id']; ?>_rate" name="weight_<?php echo $geo_zone['geo_zone_id']; ?>_rate" rows="5" placeholder="<?php echo $entry_rate; ?>" class="form-control"><?php echo ${'weight_' . $geo_zone['geo_zone_id'] . '_rate'}; ?></textarea>
                    </div>
                    <div class="col-sm-12" style="margin-top: 15px;">
                    <label class="col-sm-2 control-label" for="cost_after_string_range<?php echo $geo_zone['geo_zone_id']; ?>"><span"><?php echo "Cost After Range Finsh"; ?></span></label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="cost_after_string_range<?php echo $geo_zone['geo_zone_id']; ?>" name="weight_<?php echo $geo_zone['geo_zone_id']; ?>_cost_after_string_range" value="<?php echo isset(${'weight_'.$geo_zone['geo_zone_id'].'_cost_after_string_range'}) ? ${'weight_'.$geo_zone['geo_zone_id'].'_cost_after_string_range'} : 0; ?>" />
                    </div>
                    </div>
                  </div>
                  <div class="form-group show_rates_one" data-geo="<?php echo $geo_zone['geo_zone_id']; ?>" style=" margin-top: 15px; padding: 10px; z-index: 10; margin-bottom:0; border: 1px solid rgb(232,232,232); border-top: 2px solid rgb(191,191,191); height: 40px; cursor: pointer; background-color: rgb(252,252,252); text-align: center; font-size: 16px; font-weight: 500;">Rates per weight range</div>
                  <div class="form-group <?php echo $display_rates; ?>" data-geo="<?php echo $geo_zone['geo_zone_id']; ?>" id="show_rates_one_<?php echo $geo_zone['geo_zone_id']; ?>" style="border: 1px solid rgb(232,232,232); padding: 15px;">
                    <label class="col-sm-2 control-label" for="input-rate<?php echo $geo_zone['geo_zone_id']; ?>"><span data-toggle="tooltip" title="<?php echo $help_rate; ?>"><?php echo $entry_rate; ?></span></label>
                    <div class="col-sm-10"  id="rates_container_<?php echo $geo_zone['geo_zone_id']; ?>">
                      <?php if (isset(${'weight_'.$geo_zone['geo_zone_id'].'_meta'}['cost_by_weight_range'])) { ?>
                      <?php foreach (${'weight_'.$geo_zone['geo_zone_id'].'_meta'}['cost_by_weight_range'] as $key => $value) { ?>
                      <div class="rates col-sm-12" id="rates_<?php echo $geo_zone['geo_zone_id']; ?>" style="padding-top: 10px;">
                      <label class="col-sm-2 control-label" for="input-weight"><span data-toggle="tooltip" title="Weight"><?php echo "Weight (Kg)"; ?></span></label>
                      <div class="col-sm-3"><input type="text" placeholder="Weight" data-identity="weight" class="form-control" id="weight_cost<?php echo $geo_zone['geo_zone_id']; ?>_<?php echo $key; ?>" name="weight_<?php echo $geo_zone['geo_zone_id']; ?>_meta[cost_by_weight_range][<?php echo $i; ?>][weight]" value="<?php echo $value['weight']; ?>" /></div>
                      <label class="col-sm-2 control-label" for="input-cost"><span data-toggle="tooltip" title="Cost"><?php echo "Cost per kg (INR)"; ?></span></label>
                      <div class="col-sm-3"><input type="text" placeholder="Cost" data-identity="cost" id="cost_weight<?php echo $geo_zone['geo_zone_id']; ?>_<?php echo $key; ?>" class="form-control attr_container" name="weight_<?php echo $geo_zone['geo_zone_id']; ?>_meta[cost_by_weight_range][<?php echo $i; ?>][cost]" value="<?php echo $value['cost']; ?>" /></div>
                      <div class="col-sm-2"><button type="button" data-geo="" style="border: 1px solid #ccc; color: black; text-align: center; font-size: 12px; font-weight: 400; line-height: 30px; width:100%;" class="delete_rows">DELETE</button></div>
                      </div>
                      <?php $i++; ?>
                      <?php } ?>
                      <?php } ?>
                    </div>
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10" style="padding-right: 28px;">
                      <div class="col-sm-3 pull-right">
                      <button type="button" data-geo="<?php echo $geo_zone['geo_zone_id']; ?>" style="border: 1px solid #ccc; color: black; margin-top: 15px; width: 100%;   text-align: center; font-size: 12px; font-weight: 400; line-height: 30px;" class="add_more_rates">ADD MORE</button>
                      </div>
                    </div>
                    <div style="padding: 15px; padding-bottom: 0" class="col-sm-12">
                      <label class="col-sm-3 control-label" for="input-cost"><span data-toggle="tooltip" title="Cost"><?php echo "Cost after range finsh"; ?></span></label>
                      <div class="col-sm-9"><input type="text" class="form-control" id="max_weight_cost_<?php echo $geo_zone['geo_zone_id']; ?>" name="weight_<?php echo $geo_zone['geo_zone_id']; ?>_meta[cost_after_range_finish]" value="<?php echo ${'weight_'.$geo_zone['geo_zone_id'].'_meta'}['cost_after_range_finish']; ?>" /></div>
                    </div>
                    <div style="padding: 15px; padding-bottom: 0" class="col-sm-12">
                      <label class="col-sm-3 control-label" for="input-cost"><span data-toggle="tooltip" title="Minimum shipping cost"><?php echo "Minimum Cost (INR)"; ?></span></label>
                      <div class="col-sm-9"><input type="text" class="form-control" id="min_weight_cost_<?php echo $geo_zone['geo_zone_id']; ?>" name="weight_<?php echo $geo_zone['geo_zone_id']; ?>_meta[minimum_cost]" value="<?php echo isset(${'weight_'.$geo_zone['geo_zone_id'].'_meta'}['minimum_cost']) ? ${'weight_'.$geo_zone['geo_zone_id'].'_meta'}['minimum_cost'] : 0; ?>" /></div>
                    </div>
                  </div>
                  <div class="form-group" style="margin-top:15px;">
                    <label class="col-sm-2 control-label" for="input-status<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo "Rates"; ?></label>
                    <div class="col-sm-9">
                      <select name="weight_<?php echo $geo_zone['geo_zone_id']; ?>_check" id="input_rates_<?php echo $geo_zone['geo_zone_id']; ?>" class="form-control">
                        <?php if (${'weight_' . $geo_zone['geo_zone_id'] . '_check'} == 0) { ?>
                        <option value="0" selected="selected"><?php echo "Rates as weight and cost"; ?></option>
                        <option value="1"><?php echo "Rates as string"; ?></option>
                        <?php } else { ?>
                        <option value="0"><?php echo "Rates as weight and cost"; ?></option>
                        <option value="1" selected="selected"><?php echo "Rates as string"; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                   <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-status<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $entry_status; ?></label>
                    <div class="col-sm-9">
                      <select name="weight_<?php echo $geo_zone['geo_zone_id']; ?>_status" id="input-status<?php echo $geo_zone['geo_zone_id']; ?>" class="form-control">
                        <?php if (${'weight_' . $geo_zone['geo_zone_id'] . '_status'}) { ?>
                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                        <option value="0"><?php echo $text_disabled; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $text_enabled; ?></option>
                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group col-sm-12" style="margin-top: 15px;">
                    <button style="width: 100%;" type="button" form="form-weight" data-submit="<?php echo $geo_zone['geo_zone_id']; ?>" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary submit">Save Shipping for ( ** <?php echo $geo_zone['name']; ?> ** )</button>
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
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

  $(document).delegate('.delete_rows' , 'click', function () {
    $(this).closest('.rates').remove();
  });

  $(document).delegate('.submit' , 'click', function () {
    if ($('#input_rates_'+$(this).attr('data-submit')+' option:selected').val() == 0) {
      var input_values_weight = new Array(1);
      var input_values_cost = new Array(1);
      var input_values_id_cost = new Array(1);
      var input_values_id_weight = new Array(1);
      var c = 0;
      var e = 0;
      var input = 'rates_container_'+$(this).attr('data-submit');
      var check_return = 0;
      var max_weight_cost = $('#max_weight_cost_'+$(this).attr('data-submit')).val();

      $("#"+input+" input").each(function(){
        if(this.value == '' || this.value == null) {
          $("#"+this.id).css({"border-color": "rgb(245,107,107)"});
          $(this).attr("placeholder", "Can't be left empty");
          check_return = 1;
          return false;
        } else {
          var identity = $('#'+this.id).attr('data-identity');
          $("#"+this.id).css({"border-color": "#ccc"});
          var val = $("#"+this.id).val();
          if(identity == 'weight') {
            input_values_weight[c] = val;
            input_values_id_weight[c] = this.id;
            c++;
          } else {
            input_values_cost[e] = val;
            input_values_id_cost[e] = this.id;
            e++;
          }
        }
      });

      for(k = 0; k <= input_values_weight.length-1; k++) {
        var value_weight = input_values_weight[k];
        for (l = 0; l <= input_values_weight.length-1; l++) {
          if(Number(value_weight) > Number(input_values_weight[l])) {
            if (Number(input_values_cost[k]) > Number(input_values_cost[l])) {
              alert('Higher weight vales can not have higher cost!! Weight = '+input_values_weight[l]+' has cost ='+input_values_cost[l]+' and Weight = '+value_weight+' has cost = '+input_values_cost[k]+'.');
              $("#"+input_values_id_cost[k]).css({"border-color": "rgb(245,107,107)"});
              check_return = 1;
              return false;
            }
          }else {
            $("#"+input_values_id_cost[k]).css({"border-color": "#ccc"});
          }
        }
        if (Number(input_values_cost[k]) < Number(max_weight_cost)) {
          alert('Cost after range finish can not be more than other costs.');
          $('#max_weight_cost_'+$(this).attr('data-submit')).css({"border-color": "rgb(245,107,107)"});
          check_return = 1;
          return false;
        } else {
          $('#max_weight_cost_'+$(this).attr('data-submit')).css({"border-color": "#ccc"});
        }
      }
      if(check_return == 0) {
        $( "#save" ).trigger( "click" );
      }
    } else {
      $( "#save" ).trigger( "click" );
    }

  });

  $(document).delegate('.submit_rates' , 'click', function () {
    $( "#save" ).trigger( "click" );
  });

  $(document).delegate('.search' , 'click', function () {
    $(this).attr('href', function() {
      return this.href + '&search='+$('#search').val();
    });
  });

  $('.show_rates_onclick').click(function () {
    var zone = $(this).attr('data-geo');
    $('#show_rates_onclick_'+zone).slideToggle('slow');
  });

  $('.show_rates_one').click(function () {
    var zone = $(this).attr('data-geo');
    $('#show_rates_one_'+zone).slideToggle('slow');
  });
</script>
<?php echo $footer; ?>
