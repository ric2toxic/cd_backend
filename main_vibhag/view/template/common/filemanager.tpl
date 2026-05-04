<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title"><?php echo $heading_title; ?></h4>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-sm-4">
		  <a href="<?php echo $parent; ?>" data-toggle="tooltip" title="<?php echo $button_parent; ?>" id="button-parent" class="btn btn-default"><i class="fa fa-level-up"></i></a> <a href="<?php echo $refresh; ?>" data-toggle="tooltip" title="<?php echo $button_refresh; ?>" id="button-refresh" class="btn btn-default"><i class="fa fa-refresh"></i></a>
          <button type="button" data-toggle="tooltip" title="<?php echo $button_upload; ?>" id="button-upload" class="btn btn-primary"><i class="fa fa-upload"></i></button>
          <button type="button" data-toggle="tooltip" title="<?php echo $button_folder; ?>" id="button-folder" class="btn btn-default"><i class="fa fa-folder"></i></button>
          <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" id="button-delete" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
          <button type="button" data-toggle="tooltip" title="<?php echo 'Select Images'; ?>" id="button-selected-images" class="btn btn-warning"><i class="fa fa-check"></i></button>
		  <input type="checkbox" data-toggle="tooltip" title="<?php echo 'Select All Images'; ?>" onclick="$('input[name*=\'image_path\']').prop('checked', this.checked);" class="all_images" />
        </div>
        <div class="col-sm-4">
          <div class="input-group">
            <input type="text" name="search" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_search; ?>" class="form-control">
            <span class="input-group-btn">
            <button type="button" data-toggle="tooltip" title="<?php echo $button_search; ?>" id="button-search" class="btn btn-primary"><i class="fa fa-search"></i></button>
            </span></div>
        </div>
		<div class="col-sm-4">
			<?php echo $pagination; ?>
		</div>
      </div>
      <hr />
      <?php foreach (array_chunk($images, 4) as $image) { ?>
      <div class="row">
        <?php foreach ($image as $image) { ?>
        <div class="col-sm-3 text-center">
          <?php if ($image['type'] == 'directory') { ?>
          <div class="text-center"><a href="<?php echo $image['href']; ?>" class="directory" style="vertical-align: middle;"><i class="fa fa-folder fa-5x"></i></a></div>
          <label>
            <input type="checkbox" name="path[]"  class="path" value="<?php echo $image['path']; ?>" />
            <?php echo $image['name']; ?></label>
          <?php } ?>
          <?php if ($image['type'] == 'image') { ?>
          <a href="<?php echo $image['href']; ?>" class="thumbnail"><img src="<?php echo $image['thumb']; ?>" alt="<?php echo $image['name']; ?>" title="<?php echo $image['name']; ?>" /></a>
          <label>
            <input type="checkbox" name="image_path[]" class="path"  value="<?php echo $image['path']; ?>" data-width="<?php echo $image['width']; ?>" data-height="<?php echo $image['height']; ?>" />
            <?php echo $image['name']; ?></label>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
      <br />
      <?php } ?>
    </div>
	  <div id="uploader"> </div>
    <div class="modal-footer">
		<?php //echo $pagination; ?>
	</div>

  </div>
</div>

<script type="text/javascript"><!--
$('a.thumbnail').on('click', function(e) {
	e.preventDefault();

	<?php if ($thumb) { ?>
	$('#<?php echo $thumb; ?>').find('img').attr('src', $(this).find('img').attr('src'));
	<?php } ?>
	
	<?php if ($target) { ?>
	$('#<?php echo $target; ?>').attr('value', $(this).parent().find('input').attr('value'));
	$('#<?php echo $target; ?>-width').attr('value', $(this).parent().find('input').attr('data-width'));
	$('#<?php echo $target; ?>-height').attr('value', $(this).parent().find('input').attr('data-height'));
	<?php } else { ?>
	var range, sel = document.getSelection(); 

	if (sel.rangeCount) {
		var img = document.createElement('img');
		img.src = $(this).attr('href');
	
		range = sel.getRangeAt(0); 
		range.insertNode(img); 
	}
	<?php } ?>

	$('#modal-image').modal('hide');
});

$('a.directory').on('click', function(e) {
	e.preventDefault();
	
	$('#modal-image').load($(this).attr('href'));
});

$('.pagination a').on('click', function(e) {
	e.preventDefault();
	
	$('#modal-image').load($(this).attr('href'));
});

$('#button-parent').on('click', function(e) {
	e.preventDefault();
	
	$('#modal-image').load($(this).attr('href'));
});

$('#button-refresh').on('click', function(e) {
	e.preventDefault();
	
	$('#modal-image').load($(this).attr('href'));
});

$('input[name=\'search\']').on('keydown', function(e) {
	if (e.which == 13) {
		$('#button-search').trigger('click');
	}
});

$('#button-search').on('click', function(e) {
    var url = 'index.php?route=common/filemanager&token=<?php echo $token; ?>&directory=<?php echo $directory; ?>';
		
	var filter_name = $('input[name=\'search\']').val();
	
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}
							
	<?php if ($thumb) { ?>
	url += '&thumb=' + '<?php echo $thumb; ?>';
	<?php } ?>
	
	<?php if ($target) { ?>
	url += '&target=' + '<?php echo $target; ?>';
	<?php } ?>
			
	$('#modal-image').load(url);
});
//--></script> 
<script type="text/javascript"><!--
$('#button-upload').on('click', function() {
	$('#form-upload').remove();
	
	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" value="" /></form>');
	
	$('#form-upload input[name=\'file\']').trigger('click');
	
	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}
		
	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);
			var url = 'index.php?route=common/filemanager/upload&token=<?php echo $token; ?>&directory=<?php echo $directory; ?>';
			if('<?php echo NGINX_ENABLED; ?>' == '1'){
			    url = '<?php echo $cdn_url; ?>upload.php?token=<?php echo $token; ?>&directory=<?php echo $directory; ?>';
            }
			$.ajax({
				url: url,
				type: 'post',		
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,		
				beforeSend: function() {
					$('#button-upload i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
					$('#button-upload').prop('disabled', true);
				},
				complete: function() {
					$('#button-upload i').replaceWith('<i class="fa fa-upload"></i>');
					$('#button-upload').prop('disabled', false);
				},
				success: function(json) {
					if (json['error']) {
						alert(json['error']);
					}
					
					if (json['success']) {
						alert(json['success']);
						
						$('#button-refresh').trigger('click');
					}
				},			
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});	
		}
	}, 500);
});

$('#button-folder').popover({
	html: true,
	placement: 'bottom',
	trigger: 'click',
	title: '<?php echo $entry_folder; ?>',
	content: function() {
		html  = '<div class="input-group">';
		html += '  <input type="text" name="folder" value="" placeholder="<?php echo $entry_folder; ?>" class="form-control">';
		html += '  <span class="input-group-btn"><button type="button" title="<?php echo $button_folder; ?>" id="button-create" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></span>';
		html += '</div>';
		
		return html;	
	}
});

$('#button-folder').on('shown.bs.popover', function() {
	$('#button-create').on('click', function() {
        var url = 'index.php?route=common/filemanager/folder&token=<?php echo $token; ?>&directory=<?php echo $directory; ?>';
        if('<?php echo NGINX_ENABLED; ?>' == '1'){
            url = '<?php echo $cdn_url; ?>folder.php?token=<?php echo $token; ?>&directory=<?php echo $directory; ?>'
        }
		$.ajax({
			url: url,
			type: 'post',		
			dataType: 'json',
			data: 'folder=' + encodeURIComponent($('input[name=\'folder\']').val()),
			beforeSend: function() {
				$('#button-create').prop('disabled', true);
			},
			complete: function() {
				$('#button-create').prop('disabled', false);
			},
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
				}
				
				if (json['success']) {
					alert(json['success']);
										
					$('#button-refresh').trigger('click');
				}
			},			
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});	
});

$('#modal-image #button-delete').on('click', function(e) {
	var checkedVals = $('.path:checkbox:checked').map(function() {
		return this.value;
	}).get();

	if (confirm('<?php echo $text_confirm; ?>')) {
        var url = 'index.php?route=common/filemanager/delete&token=<?php echo $token; ?>';
        if('<?php echo NGINX_ENABLED; ?>' == '1'){
            url = '<?php echo $cdn_url; ?>delete.php?token=<?php echo $token; ?>';
        }
		$.ajax({
			url: url,
			type: 'post',		
			dataType: 'json',
			data: {'path': checkedVals},
			beforeSend: function() {
				$('#button-delete').prop('disabled', true);
			},	
			complete: function() {
				$('#button-delete').prop('disabled', false);
			},		
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
				}
				
				if (json['success']) {
					alert(json['success']);
					
					$('#button-refresh').trigger('click');
				}
			},			
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
});
//--></script>

<script type="text/javascript">
	// Initialize the widget when the DOM is ready
	$(function() {
        var url = "index.php?route=common/filemanager/multipleImageUpload&token=<?php echo $token?>&directory=<?php echo $directory; ?>";
        if('<?php echo NGINX_ENABLED; ?>' == '1'){
            url = "<?php echo $cdn_url; ?>multipleImageUpload.php?token=<?php echo $token?>&directory=<?php echo $directory; ?>";
        }
		var uploader = $("#uploader").pluploadQueue({
			url : url,

			chunk_size : '1mb',
			rename : true,
			dragdrop: true,
			drop_element: true,

			filters : {
				// Maximum file size
				max_file_size : '10mb',
				// Specify what files to browse for
				mime_types: [
					{title : "Image files", extensions : "jpg,JPG,jpeg,JPEG,png,PNG"}
				],
				//prevent_duplicates: false,
				file:[],
			},

			// Resize images on clientside if we can
			/*resize: {
				width : 200,
				height : 200,
				quality : 90,
				crop: true // crop to exact dimensions
			},*/
			//flash_swf_url : "js/Moxie.swf",
			//silverlight_xap_url: "js/Moxie.xap",
			preinit: attachCallbacks,
		});

		function attachCallbacks(Uploader) {
			Uploader.bind('FileUploaded', function (Up, File, Response) {
				if ((Uploader.total.uploaded + 1) == Uploader.files.length) {
					$('#modal-image').load("index.php?route=common/filemanager&token=<?php echo $token; ?>&directory=<?php echo $directory; ?>");
				}
			});
		}

	});
</script>

<script type="text/javascript">
	$('#button-selected-images').click(function(){
		var image_row = $('tbody#multiple_image').attr('data-count-row');

		$('input[name^=\'image_path\']:checked').each(function(index,element){
			html  = '<tr id="image-row' + image_row + '">';
			html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '" data-dirctory="<?php echo $directory; ?>" data-toggle="image" class="img-thumbnail"><img src="'+$(this).parent().parent().find('img').attr('src')+'" alt="" title="" data-placeholder="" /></a><input type="hidden" name="product_image[' + image_row + '][image]" value="'+$(element).val()+'" id="input-image' + image_row + '" />' +
                '<input type="hidden" name="product_image[' + image_row + '][width]" value="'+$(element).attr('data-width')+'" id="input-image' + image_row + '-width" /><input type="hidden" name="product_image[' + image_row + '][height]" value="'+$(element).attr('data-height')+'" id="input-image' + image_row + '-height" />' +
                '</td>';
			html += '  <td class="text-right"><input type="text" name="product_image[' + image_row + '][sort_order]" value="" placeholder="sort order" class="form-control" /></td>';
			html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
			html += '</tr>';
			$('tbody#multiple_image').append(html);
			image_row++;
			$(this).removeAttr('checked');
			$('.all_images').removeAttr('checked');

		});

		$('tbody#multiple_image').attr('data-count-row',image_row);
		$('#modal-image').modal('hide');
	});
</script>