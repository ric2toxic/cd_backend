<?php echo $header; ?>
<div id="content">
  <div class="container-fluid banner_container">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default add_banner_top">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <div class="pull-right">
          <button type="submit" form="form-banner" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
          <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-add-banner"><i class="fa fa-reply"></i></a></div>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-banner" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="status" id="input-status" class="form-control">
                <?php if ($status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <?php if($count > 1) { ?>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-status">Stores<?php // echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="store_id" id="input-status" class="form-control">
                <?php if ($stores) { ?>
                <option value="">Select a Stores</option>
                <?php foreach($stores as $store) {
                  if($store['store_id'] == $store_id){
                    $selected = 'selected = "selected"';
                  }else{
                    $selected = '';
                  }
                ?>
                <option value="<?php echo $store['store_id'] ?>" <?php echo $selected;?>><?php echo $store['name']; ?></option>
                <?php } } ?>
              </select>
              <?php if ($error_select_store) { ?>
              <div class="text-danger"><?php echo $error_select_store; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php }else { ?>
          <?php foreach($stores as $store) { ?>
          <input type="hidden" value="<?php echo $store['store_id'] ?>" name="store_id">
          <?php } } ?>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-status">Position<?php // echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="banner_position" id="input-status" class="form-control">
                <?php if ($positions) { ?>
                <option value="">Select a Position</option>
                <?php foreach($positions as $position) {
                   if($position['value'] == $banner_position){
                    $selected = 'selected = "selected"';
                  }else{
                    $selected = '';
                  }
                ?>
                <option value="<?php echo $position['value'] ?>" <?php echo $selected;?>><?php echo $position['position']; ?></option>
                <?php } } ?>
              </select>
              <?php if ($error_banner_position) { ?>
              <div class="text-danger"><?php echo $error_banner_position; ?></div>
              <?php } ?>
            </div>
          </div>
          <input type="hidden" value="">
          <?php if ($error_banner_image) { ?>
          <div class="text-danger"><?php echo $error_banner_image; ?></div>
          <?php } ?>

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $text_width;?></label>
            <div class="col-sm-10">
              <input type="text" name="banner_width" value="<?php echo $banner_width; ?>" placeholder="<?php echo $entry_banner_width; ?>" class="form-control" />
              <?php if ($error_banner_width) { ?>
              <div class="text-danger"><?php echo $error_banner_width; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $text_height;?></label>
            <div class="col-sm-10">
              <input type="text" name="banner_height" value="<?php echo $banner_height; ?>" placeholder="<?php echo $entry_banner_height; ?>" class="form-control" />
              <?php if ($error_banner_height) { ?>
              <div class="text-danger"><?php echo $error_banner_height; ?></div>
              <?php } ?>
            </div>
          </div>


          <table id="images" class="table table-striped table-bordered table-hover" style="display: block;">
            <thead>
              <tr>
                <td class="text-left"><?php echo $entry_title; ?></td>
                <td class="text-left"><?php echo $entry_link; ?></td>
                <td class="text-left"><?php echo $entry_image; ?></td>
                <td class="text-right"><?php echo $entry_sort_order; ?></td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              <?php $image_row = 0; ?>
              <?php foreach ($banner_images as $banner_image) { ?>
              <tr id="image-row<?php echo $image_row; ?>">
                <td class="text-left"><?php foreach ($languages as $language) { ?>
                  <div class="input-group pull-left"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> </span>
                    <input type="text" name="banner_image[<?php echo $image_row; ?>][banner_image_description][<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($banner_image['banner_image_description'][$language['language_id']]) ? $banner_image['banner_image_description'][$language['language_id']]['title'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" class="form-control" />
                  </div>
                  <?php if (isset($error_banner_image[$image_row][$language['language_id']])) { ?>
                  <div class="text-danger"><?php echo $error_banner_image[$image_row][$language['language_id']]; ?></div>
                  <?php } ?>
                  <?php } ?></td>
                <td class="text-left" style="width: 30%;"><input type="text" name="banner_image[<?php echo $image_row; ?>][link]" value="<?php echo $banner_image['link']; ?>" placeholder="<?php echo $entry_link; ?>" class="form-control" /></td>
                <td class="text-left"><a href="" id="thumb-image<?php echo $image_row; ?>" data-toggle="image_seller" class="img-thumbnail"><img src="<?php echo $banner_image['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                  <input type="hidden" name="banner_image[<?php echo $image_row; ?>][image]" value="<?php echo $banner_image['image']; ?>" id="input-image<?php echo $image_row; ?>" /></td>
                <td class="text-right"><input type="text" name="banner_image[<?php echo $image_row; ?>][sort_order]" value="<?php echo $banner_image['sort_order']; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>
                <td class="text-left"><button type="button" onclick="$('#image-row<?php echo $image_row; ?>, .tooltip').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
              </tr>
              <?php $image_row++; ?>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4"></td>
                <td class="text-left"><button type="button" onclick="addImage();" data-toggle="tooltip" title="<?php echo $button_banner_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
              </tr>
            </tfoot>
          </table>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
var image_row = <?php echo $image_row; ?>;

function addImage() {
	html  = '<tr id="image-row' + image_row + '">';
    html += '  <td class="text-left">';
	<?php foreach ($languages as $language) { ?>
	html += '    <div class="input-group">';
	html += '      <span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span><input type="text" name="banner_image[' + image_row + '][banner_image_description][<?php echo $language['language_id']; ?>][title]" value="" placeholder="<?php echo $entry_title; ?>" class="form-control" />';
    html += '    </div>';
	<?php } ?>
	html += '  </td>';	
	html += '  <td class="text-left"><input type="text" name="banner_image[' + image_row + '][link]" value="" placeholder="<?php echo $entry_link; ?>" class="form-control" /></td>';	
	html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '" data-toggle="image_seller" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="banner_image[' + image_row + '][image]" value="" id="input-image' + image_row + '" /></td>';
	html += '  <td class="text-right"><input type="text" name="banner_image[' + image_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';
	
	$('#images tbody').append(html);
	
	image_row++;
}



    $(document).delegate('a[data-toggle=\'image_seller\']', 'click', function(e) {
      e.preventDefault();

      $('.popover').popover('hide', function() {
        $('.popover').remove();
      });

      var element = this;

      $(element).popover({
        html: true,
        placement: 'right',
        trigger: 'manual',
        content: function() {
          return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
        }
      });

      $(element).popover('show');

      $('#button-image').on('click', function() {
        $('#modal-image').remove();

        $.ajax({
          url: 'index.php?route=common/filemanager&token=' + getURLVar('token') + '&target=' + $(element).parent().find('input').attr('id') + '&thumb=' + $(element).attr('id'),

          dataType: 'html',
          beforeSend: function() {
            $('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
            $('#button-image').prop('disabled', true);
          },
          complete: function() {
            $('#button-image i').replaceWith('<i class="fa fa-pencil"></i>');
            $('#button-image').prop('disabled', false);
          },
          success: function(html) {
            $('body').append('<div id="modal-image" class="modal">' + html + '</div>');

            $('#modal-image').modal('show');
          }
        });

        $(element).popover('hide', function() {
          $('.popover').remove();
        });
      });

      $('#button-clear').on('click', function() {
        $(element).find('img').attr('src', $(element).find('img').attr('data-placeholder'));

        $(element).parent().find('input').attr('value', '');

        $(element).popover('hide', function() {
          $('.popover').remove();
        });
      });
    });

//--></script></div>
<?php echo $footer; ?>