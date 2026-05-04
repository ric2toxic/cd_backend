<?php echo $header; ?><?php echo $column_left; ?>
<?php if(!empty($form_for_option) && $form_for_option == 'color') {
  $color_form = 1;
} ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-option" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-option" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <?php foreach ($languages as $language) { ?>
              <div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
                <input type="text" name="option_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($option_description[$language['language_id']]) ? $option_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" class="form-control" />
              </div>
              <?php if (isset($error_name[$language['language_id']])) { ?>
              <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
              <?php } ?>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-type"><?php echo $entry_type; ?></label>
            <div class="col-sm-10">
              <select name="type" id="input-type" class="form-control">
                <optgroup label="<?php echo $text_choose; ?>">
                <?php if ($type == 'select') { ?>
                <option value="select" selected="selected"><?php echo $text_select; ?></option>
                <?php } else { ?>
                <option value="select"><?php echo $text_select; ?></option>
                <?php } ?>
                <?php if ($type == 'radio') { ?>
                <option value="radio" selected="selected"><?php echo $text_radio; ?></option>
                <?php } else { ?>
                <option value="radio"><?php echo $text_radio; ?></option>
                <?php } ?>
                <?php if ($type == 'checkbox') { ?>
                <option value="checkbox" selected="selected"><?php echo $text_checkbox; ?></option>
                <?php } else { ?>
                <option value="checkbox"><?php echo $text_checkbox; ?></option>
                <?php } ?>
                <?php if ($type == 'image') { ?>
                <option value="image" selected="selected"><?php echo $text_image; ?></option>
                <?php } else { ?>
                <option value="image"><?php echo $text_image; ?></option>
                <?php } ?>
                </optgroup>
                <optgroup label="<?php echo $text_input; ?>">
                <?php if ($type == 'text') { ?>
                <option value="text" selected="selected"><?php echo $text_text; ?></option>
                <?php } else { ?>
                <option value="text"><?php echo $text_text; ?></option>
                <?php } ?>
                <?php if ($type == 'textarea') { ?>
                <option value="textarea" selected="selected"><?php echo $text_textarea; ?></option>
                <?php } else { ?>
                <option value="textarea"><?php echo $text_textarea; ?></option>
                <?php } ?>
                </optgroup>
                <optgroup label="<?php echo $text_file; ?>">
                <?php if ($type == 'file') { ?>
                <option value="file" selected="selected"><?php echo $text_file; ?></option>
                <?php } else { ?>
                <option value="file"><?php echo $text_file; ?></option>
                <?php } ?>
                </optgroup>
                <optgroup label="<?php echo $text_date; ?>">
                <?php if ($type == 'date') { ?>
                <option value="date" selected="selected"><?php echo $text_date; ?></option>
                <?php } else { ?>
                <option value="date"><?php echo $text_date; ?></option>
                <?php } ?>
                <?php if ($type == 'time') { ?>
                <option value="time" selected="selected"><?php echo $text_time; ?></option>
                <?php } else { ?>
                <option value="time"><?php echo $text_time; ?></option>
                <?php } ?>
                <?php if ($type == 'datetime') { ?>
                <option value="datetime" selected="selected"><?php echo $text_datetime; ?></option>
                <?php } else { ?>
                <option value="datetime"><?php echo $text_datetime; ?></option>
                <?php } ?>
                </optgroup>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
            </div>
          </div>
          <table id="option-value" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left required"><?php echo $entry_option_value; ?></td>
                <td class="text-left"><?php echo $entry_image; ?></td>
                <td class="text-right"><?php echo $entry_sort_order; ?></td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              <?php $option_value_row = 0; ?>
              <?php foreach ($option_values as $option_value) { ?>
              <tr id="option-value-row<?php echo $option_value_row; ?>">
                <td class="text-left"><input type="hidden" name="option_value[<?php echo $option_value_row; ?>][option_value_id]" value="<?php echo $option_value['option_value_id']; ?>" />
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
                    <?php if(!empty($color_form)) {
                      $name = 'display_name';
                    } else {
                      $name = 'name';
                    } ?>
                    <input type="text" name="option_value[<?php echo $option_value_row; ?>][option_value_description][<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($option_value['option_value_description'][$language['language_id']]) ? $option_value['option_value_description'][$language['language_id']][$name] : ''; ?>" placeholder="<?php echo $entry_option_value; ?>" class="form-control" id="<?php if(!empty($color_form)) { echo 'input_color_'.$language['language_id'].'_'.$option_value['option_value_description'][$language['language_id']]['name'];} ?>" data-language-id="<?php echo $language['language_id']; ?>" data-row-id="<?php echo $option_value_row; ?>" />
                  </div>
                  <?php if (isset($error_option_value[$option_value_row][$language['language_id']])) { ?>
                  <div class="text-danger"><?php echo $error_option_value[$option_value_row][$language['language_id']]; ?></div>
                  <?php } ?>
                  <?php } ?>
                </td>
                  
                <td class="text-left"><a href="" id="thumb-image<?php echo $option_value_row; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $option_value['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                  <input type="hidden" name="option_value[<?php echo $option_value_row; ?>][image]" value="<?php echo $option_value['image']; ?>" id="input-image<?php echo $option_value_row; ?>" /></td>
                <td class="text-right"><input type="text" name="option_value[<?php echo $option_value_row; ?>][sort_order]" value="<?php echo $option_value['sort_order']; ?>" class="form-control" /></td>
                <td class="text-left"><button type="button" onclick="$('#option-value-row<?php echo $option_value_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
              </tr>
              <?php $option_value_row++; ?>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3"></td>
                <td class="text-left"><button type="button" onclick="addOptionValue();" data-toggle="tooltip" title="<?php echo $button_option_value_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
              </tr>
            </tfoot>
          </table>
        </form>
      </div>
    </div>
  </div>
  <!-- Add New Color Modal -->
  <div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
      <span class="close">&times;</span>
      <div> <h2>Enter Color Details: </h2></div>
      <form>
      <table width="100%">
        <tr>
          <td>Color Name:</td>
          <td>
            <?php foreach ($languages as $language) { ?>
            <div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
              <input type="text" name="new_name_<?php echo $language['language_id']; ?>" value="" placeholder="" class="form-control" id="new_name_<?php echo $language['language_id']; ?>" required/>
            </div>
            <?php } ?>
          </td>
        </tr>
        <tr>
          <td>Hex Code:</td>
          <td># <input type="text" id="new_hexcode" name="new_hexcode" value="" class="" required><br> Enter code without '#'</td>
        </tr>
        <tr>
          <td></td>
          <td><input type="button" id="add_new_form" name="add_new_form" value="Add" class=""></td>
        </tr>
      </table>
      </form>
    </div>
  </div>
  <script type="text/javascript"><!--
$('select[name=\'type\']').on('change', function() {
	if (this.value == 'select' || this.value == 'radio' || this.value == 'checkbox' || this.value == 'image') {
		$('#option-value').show();
	} else {
		$('#option-value').hide();
	}
});

$('select[name=\'type\']').trigger('change');

var option_value_row = <?php echo $option_value_row; ?>;

function addOptionValue() {
	html  = '<tr id="option-value-row' + option_value_row + '">';	
    html += '  <td class="text-left"><input type="hidden" name="option_value[' + option_value_row + '][option_value_id]" value="" />';
	<?php foreach ($languages as $language) { ?>
	html += '    <div class="input-group">';
	html += '      <span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span><input type="text" name="option_value[' + option_value_row + '][option_value_description][<?php echo $language['language_id']; ?>][name]" value="" placeholder="<?php echo $entry_option_value; ?>" class="form-control" id="input_color_<?php echo $language['language_id']; ?>_'+option_value_row+'" data-language-id="<?php echo $language["language_id"]; ?>" data-row-id="<?php echo $option_value_row; ?>"/>';
    html += '    </div>';
	<?php } ?>
	html += '  </td>';
  <?php if(!empty($form_for_option) && $form_for_option == 'color') { ?>
    //html += '<td class="text-right"><input type="text" name="option_value[' + option_value_row + '][hex_code]" value="" placeholder="<?php echo $entry_hex_code; ?>" class="form-control" /></td>';
  <?php } ?>
  html += '  <td class="text-left"><a href="" id="thumb-image' + option_value_row + '" data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="option_value[' + option_value_row + '][image]" value="" id="input-image' + option_value_row + '" /></td>';
	html += '  <td class="text-right"><input type="text" name="option_value[' + option_value_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#option-value-row' + option_value_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';	
	
	$('#option-value tbody').append(html);
	
	option_value_row++;
}
//--></script></div>
<?php echo $footer; ?>
<script type="text/javascript">
  
  // add new color logic
  $( "#add_new_form" ).click(function( event ) {
    var modal = document.getElementById('myModal');
    var new_color_name;
    var language = new Object();
    <?php foreach ($languages as $language) { ?>
      language[<?php echo $language['language_id'] ?>] = $('#new_name_<?php echo $language["language_id"]; ?>').val().trim();
    <?php } ?>
    //language = language.filter(function(n){ return n != undefined });
    var new_color_hexcode = $('#new_hexcode').val().trim();
    var language_json = JSON.stringify(language);
    $.ajax({
      url: 'index.php?route=catalog/option/addNewColor&token=<?php echo $token; ?>&hexcode='+new_color_hexcode+'&data='+language_json,
      dataType: 'json',
      success: function(json) {
        alert(json.message);
        if(json.status == "success") {
          modal.style.display = "none";
        }
      }
    });
    event.preventDefault();
  });
  
  $(document).delegate('[id*=color]', 'keyup', function() {
    var check_length = $(this).val().trim().length;
    if(check_length!= '' && check_length > 0) {
      var language_id = $(this).attr("data-language-id");
      var row_id = $(this).attr("data-row-id");
      var color = $(this).val().trim();
      var item;
      $('[id*=color]').autocomplete({
        'source': function(request, response) {
          $.ajax({
            url: 'index.php?route=catalog/option/autoCompleteColor&token=<?php echo $token; ?>&language_id='+language_id+'&color='+color,
            dataType: 'json',
            success: function(json) {
              response($.map(json, function(item) {
                return {
                  label: item['display'],
                  value: item['display'],
                  hex_code: item['hex_code']
                }
              }));
            }
          });
        },
        'select': function(item) {
          if(value  == '-- Add New Color --') {
            addNewColor();
          } else {
            $(this).val(value);
            $hex_code_box = 'option_value['+row_id+'][hex_code]';
            $('input[name="'+$hex_code_box+'"]').val(item['hex_code']);
          }
        }
      });
    }else{
      $('[id*=color]').autocomplete({
        'source': function(){ }
      });
    }
  });
  
  // Add New Color Manager
  function addNewColor(){
    var modal = document.getElementById('myModal');
    var span = document.getElementsByClassName("close")[0];
    
    modal.style.display = "block";
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    span.onclick = function() {
        modal.style.display = "none";
    }
  }
</script>

<style> <!-- // Style for Add New Color Modal-->
  body {font-family: Arial, Helvetica, sans-serif;}

  /* The Modal (background) */
  .modal {
      display: none; /* Hidden by default */
      z-index: 2; /* Sit on top */
      padding-top: 200px; /* Location of the box */
      margin-left: 570px; /* Location of the box */
      width: 30%;
      height: 100%;
  }

  /* Modal Content */
  .modal-content {
      background-color: #fefefe;
      margin: auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      align: center;
  }

  /* The Close Button */
  .close {
      color: #aaaaaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
  }

  .close:hover,
  .close:focus {
      color: #000;
      text-decoration: none;
      cursor: pointer;
  }
</style>