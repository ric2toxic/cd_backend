<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
  	<div class="page-header">
	    <div class="container-fluid">
	      	<div class="pull-right">
	        	<button type="submit" form="form-pickup" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
	        	<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
	    <div class="panel panel-default">
		    <div class="panel-heading">
		        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?> </h3>
		    </div>
	        <div class="panel-body">
		        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-pickup" class="form-horizontal">
		          	<div class="form-group">
			            <label class="col-sm-2 control-label" for="input-category"><?php echo $entry_category; ?></label>
			            <div class="col-sm-10">
			              	<input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" />
			            </div>
		          	</div>
		          	<hr>
			        <div class="form-group search_category">
			            <?php // Get Master Preferences from oc_mast_preference ?>
			            <?php $i = 1; if(!empty($preferences)){ 
			            
			            	foreach ($preferences as $cat) {
			            		if(!empty($cat['category_id'])){ 
									
									//if (is_file(DIR_IMAGE . $cat['category_image'])) {
										$image = $cat['category_image'];
										$thumb = $cat['category_image'];
									//} else {
										//$image = '';
										//$thumb = 'no_image.png';
									//}

									$cat_image = array(
											'image'      => $image,
											'thumb'      => $this->model_tool_image->resize($thumb, 100, 100)
									);
			            		?>
					            	<div class="col-sm-12 panel search_category_<?php echo $cat['category_id']; ?>" id="category_<?php echo $cat['category_id']; ?>" >
				            			<div class="col-sm-3">
					            			<h4><i class="fa fa-trash category_remove" data_id="<?php echo $cat['category_id']; ?>" aria-hidden="true"></i> <?php echo $cat['category_name']; ?><h4>
					            			<input type="hidden" name="category[<?php echo $cat['category_id']; ?>]" value="<?php echo $cat['category_id']; ?>" />
					            		</div>
					            		<div class="col-sm-4">
					            			<input type="text" value="" data-cat="<?php echo $cat['category_id']; ?>" placeholder="<?php echo $entry_filter; ?>" class="filter form-control" />
					            		</div>
					            		<div class="col-sm-2">
					            			<a id="thumb-image<?php echo $i; ?>" class="desktop_website_logo" data-toggle="category_image" type="file" ><img src="<?php echo $cat_image['thumb']; ?>" alt="" title="" /></a>
					                  		<input type="hidden" class="config_logo" name="category[<?php echo $cat['category_id']; ?>][image]" value="<?php echo $cat_image['image']; ?>" id="input-image<?php echo $i; ?>" />
					            		</div>
						            	<div class="category_filter category_filter_<?php echo $cat['category_id']; ?> col-sm-12 panel">
						            		<?php if(isset($cat['filters']) && !empty($cat['filters'] )){ 
						            		 	foreach ($cat['filters'] as $fil) { ?>
						            		 		<div class="category_filter_<?php echo $cat['category_id']; ?>_<?php echo $fil['filter_id']; ?> col-sm-3 pull-left">
							            				<h4><i class="fa fa-trash filter_remove" data_cat_id="<?php echo $cat['category_id']; ?>" data_id="<?php echo $fil['filter_id']; ?>" aria-hidden="true"></i> <?php echo $fil['filter_name']; ?></h4>
							            				<input type="hidden" name="category[<?php echo $cat['category_id']; ?>][filter][<?php echo $fil['filter_id']; ?>]" value="<?php echo $fil['filter_id']; ?>" />
							            			</div>
						            		 	<?php }?>
						            		<?php } ?>
						            	</div>
					            	</div>
						<?php   }
							$i++;
							}
						} ?>
			        </div>
			        <input class="cat_image_count" type="hidden" name="image_no" value="<?php echo $i; ?>" class="form-control" />
		        </form>
	        </div>
	    </div>
  	</div>
</div>

<?php echo $footer; ?>

<script type="text/javascript"><!--
	$(document).ready(function(){
		// Category
		$('input[name=\'category\']').autocomplete({
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
				var cat_image_count = $('.cat_image_count').val();
				<?php 	$image = '';
						$thumb = 'no_image.png';
						
						$cat_image = array(
							'image'      => $image,
							'thumb'      => $this->model_tool_image->resize($thumb, 100, 100)
						); 
				?>
				var cat_image_count = Number(cat_image_count) + 1;
				$('input[name=\'category\']').val('');
				//$('input[name=\'category_id\']').val(item['value']);
				$('.search_category_' + item['value']).remove();
				$('.search_category').append('<div class="col-sm-12 panel search_category_'+ item['value'] +'" id="category_' + item['value'] + '"><div class="col-sm-3"><h4><i class="fa fa-trash category_remove" data_id="'+item['value']+'" aria-hidden="true"></i> ' + item['label'] + '<h4><input type="hidden" name="category['+item['value']+']" value="' + item['value'] + '" /></div><div class="col-sm-4"><input type="text" value="" data-cat="'+item['value']+'" placeholder="<?php echo $entry_filter; ?>" class="filter form-control" /></div> <div class="col-sm-2"><a id="thumb-image'+cat_image_count+'" class="desktop_website_logo" data-toggle="category_image" type="file" ><img src="<?php echo $cat_image['thumb']; ?>" alt="" title="" /></a><input type="hidden" class="config_logo" name="category['+item['value']+'][image]" value="<?php echo $cat_image['image']; ?>" id="input-image'+cat_image_count+'" /></div><div class="category_filter category_filter_'+ item['value'] +' col-sm-12 panel"></div></div>');
				var cat_image_count = $('.cat_image_count').attr('value', cat_image_count);
			}
		});
		$(document).delegate('.category_remove', 'click', function() {
			var cat_id = $(this).attr('data_id');
			$('.search_category_'+cat_id).remove();
		});

		//Filter
		$(document).delegate('.filter', 'focus', function(e) {
			var cat_id = $(this).attr('data-cat');
			$(this).autocomplete({
				'source': function(request, response) {
					$.ajax({
						url: 'index.php?route=preferences/preferences/autocompleteFilter&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request) +'&cat_id='+ cat_id,
						dataType: 'json',
						success: function(json) {
							response($.map(json, function(item) {
								return {
									label: item['name'],
									value: item['filter_id']
								}
							}));
						}
					});
				},
				'select': function(item) {
					$('input[name=\'filter\']').val('');

					$('.category_filter_'+cat_id+'_'+ item['value']).remove();

					$('.category_filter_'+cat_id).append('<div class="category_filter_'+cat_id+'_' + item['value'] + ' col-sm-3 pull-left"><h4><i class="fa fa-trash filter_remove" data_cat_id="'+cat_id+'" data_id="'+ item['value'] +'" aria-hidden="true"></i> ' + item['label'] + '</h4><input type="hidden" name="category['+cat_id+'][filter]['+item['value']+']" value="' + item['value'] + '" /></div>');
				}
			});
		});
		$(document).delegate('.filter_remove', 'click', function() {
			var filter_id = $(this).attr('data_id');
			var cat_id = $(this).attr('data_cat_id');
			$('.category_filter_'+cat_id+'_'+filter_id).remove();
		});

		var base64image = $('.desktop_website_logo').attr('src');
	});

	// Image Manager
	$(document).delegate('a[data-toggle=\'category_image\']', 'click', function(e) {
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
				return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button>';
			}
		});

		$(element).popover('show');

		$('#button-image').on('click', function() {
			$('#modal-image').remove();

			$.ajax({
				url: 'index.php?route=common/filemanager&token=<?php echo $token; ?>&target=' + $(element).parent().find('input').attr('id') + '&thumb=' + $(element).attr('id'),
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
//--></script> 