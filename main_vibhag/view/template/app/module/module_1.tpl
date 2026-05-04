<div class="tab-pane" id="tab-image">
    <div class="table-responsive">
        <table id="images" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <td class="text-left">Image</td>
                    <td class="text-left">Title</td>
                    <td class="text-left">subtitle</td>
                    <td class="text-left">Image Ratio</td>
                    <td class="text-left">On Click</td>
                    <td class="text-left">Sort Order</td>
                    <td></td>
                </tr>
            </thead>
            <tbody id="multiple_image" data-count-row="" >
                <?php $image_row = 0; ?>
                <?php if(isset($module)){ ?>
                <?php foreach ($module as $image) { ?>
                <tr id="image-row<?php echo $image_row; ?>">
                    <td class="text-left">
                        <a href="" id="thumb-module<?php echo $image_row; ?>" data-directory="<?php echo $image['directory']; ?>" data-toggle="image" class="img-thumbnail">
                            <img src="<?php echo $image['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" />
                        </a>
                        <input type="hidden" name="module[<?php echo $image_row; ?>][image]" value="<?php echo $image['image']; ?>" id="input-image<?php echo $image_row; ?>" />
                    </td>
                    <td class="text-right">
                        <input type="text" name="module[<?php echo $image_row; ?>][title]" value="<?php echo $image['title']; ?>" placeholder="<?php echo $entry_image_title; ?>" class="form-control" />
                        <br/>
                        <span>Is Show Title:</span>
                        <select class="form-control" name = "module[<?php echo $image_row; ?>][is_show_title]">
                            <?php $is_show_title = ($image['is_show_title'] == 0)?'selected="selected"':''; ?>
                            <option value="1" >Yes</option>
                            <option value="0" <?php echo $is_show_title; ?> >NO</option>
                        </select>
                    </td>
                    <td class="text-right">
                        <input type="text" name="module[<?php echo $image_row; ?>][subtitle]" value="<?php echo $image['subtitle']; ?>" placeholder="<?php echo $entry_image_subtitle; ?>" class="form-control" />
                        <br/>
                        <span>Is Show Sub Title:</span>
                        <select class="form-control" name = "module[<?php echo $image_row; ?>][is_show_subtitle]">
                            <?php $is_show_subtitle = ($image['is_show_subtitle'] == 0)?'selected="selected"':''; ?>
                            <option value="1" >Yes</option>
                            <option value="0" <?php echo $is_show_subtitle; ?> >NO</option>
                        </select>
                    </td>
                    <td class="text-right">
                        <input type="text" name="module[<?php echo $image_row; ?>][image_height]" value="<?php echo $image['image_height']; ?>" placeholder="<?php echo $entry_image_height; ?>" class="form-control col-xs-4" />
                        <input type="text" name="module[<?php echo $image_row; ?>][image_width]" value="<?php echo $image['image_width']; ?>" placeholder="<?php echo $entry_image_width; ?>" class="form-control col-xs-4" />
                        <input type="text" name="module[<?php echo $image_row; ?>][module_width]" value="<?php echo $image['module_width']; ?>" placeholder="<?php echo $entry_module_width; ?>" class="form-control col-xs-4" />
                    </td>
                    <td class="text-right">
                        <input type="text" name="module[<?php echo $image_row; ?>][headline]" value="<?php echo $image['headline']; ?>" placeholder="<?php echo $entry_image_headline; ?>" class="form-control col-xs-4" />
                        <input type="hidden" name="module[<?php echo $image_row; ?>][category_id]" value="<?php echo $image['category_id']; ?>" class="form-control col-xs-4" id="category_id_<?php echo $image_row; ?>"/>
                        <input type="text" name="module[<?php echo $image_row; ?>][category_name]" value="<?php echo $image['category_name']; ?>" placeholder="<?php echo $entry_category_name; ?>" class="form-control category_name col-xs-4" data-id="<?php echo $image_row; ?>" />
                        <input type="text" name="module[<?php echo $image_row; ?>][search_url]" value="<?php echo $image['search_url']; ?>" placeholder="<?php echo $entry_image_search_url; ?>" class="form-control col-xs-4" />
                    </td>
                    <td class="text-right">
                        <input type="text" name="module[<?php echo $image_row; ?>][sort_order]" value="<?php echo $image['sort_order']; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" />
                    </td>
                    <td class="text-left"><button type="button" onclick="$('#image-row<?php echo $image_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                </tr>
                <?php $image_row++; ?>
                <?php } ?>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6"></td>
                    <td class="text-left"><button type="button" onclick="addImage();" data-toggle="tooltip" title="<?php echo $button_image_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script type="text/javascript">
var image_row = "<?php echo $image_row; ?>";

function addImage() {
	html  = '<tr id="image-row' + image_row + '">';
	html += '  <td class="text-left"><a href="" id="thumb-module' + image_row + '" data-directory="" data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="module[' + image_row + '][image]" value="" id="input-image' + image_row + '" /></td>';
	html += '  <td class="text-right"><input type="text" name="module[' + image_row + '][title]" value="" placeholder="<?php echo $entry_image_title; ?>" class="form-control" /><br/><span>Is Show Title:</span><select class="form-control" name = "module[' + image_row + '][is_show_title]"><option value="1">Yes</option><option value="0">NO</option></select></td>';
    html += '  <td class="text-right"><input type="text" name="module[' + image_row + '][subtitle]" value="" placeholder="<?php echo $entry_image_subtitle; ?>" class="form-control" /><br/><span>Is Show Sub Title:</span><select class="form-control" name = "module[' + image_row + '][is_show_subtitle]"><option value="1">Yes</option><option value="0">NO</option></select></td>';
    html += '  <td class="text-right"><input type="text" name="module[' + image_row + '][image_height]" value="" placeholder="<?php echo $entry_image_height; ?>" class="form-control col-xs-4" /><input type="text" name="module[' + image_row + '][image_width]" value="" placeholder="<?php echo $entry_image_width; ?>" class="form-control col-xs-4" /><input type="text" name="module[' + image_row + '][module_width]" value="" placeholder="<?php echo $entry_module_width; ?>" class="form-control col-xs-4" /></td>';
    html += '  <td class="text-right"><input type="text" name="module[' + image_row + '][headline]" value="" placeholder="<?php echo $entry_image_headline; ?>" class="form-control col-xs-4" /><input type="hidden" name="module[' + image_row + '][category_id]" value="" class="form-control col-xs-4" id="category_id_' + image_row + '"/><input type="text" name="module[' + image_row + '][category_name]" value="" placeholder="<?php echo $entry_category_name; ?>" class="form-control category_name col-xs-4" data-id="' + image_row + '" /><input type="text" name="module[' + image_row + '][search_url]" value="" placeholder="<?php echo $entry_image_search_url; ?>" class="form-control col-xs-4" /></td>';
    html += '  <td class="text-right"><input type="text" name="module[' + image_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';
	$('#images tbody').append(html);
	image_row++;
}

$(document).delegate('input.category_name', 'focus' ,function(){
    let val = $(this).attr('data-id');
    $(this).autocomplete({
        'source': function(request, response) {
            let key = $(this).val();
            $.ajax({
                url: 'index.php?route=notification/notification/autocomplete&token=<?php echo $this->session->data['token']; ?>&category_name=' +  encodeURIComponent(key),
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
            $('#category_id_'+val).val(item['value']);
            $(this).val(item['label']);
        }
    });
});
</script>