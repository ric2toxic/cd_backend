<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" form="form-category" id="form-category-submit" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-data" data-toggle="tab"><?php echo $tab_data; ?></a></li>
            <li><a href="#tab-design" data-toggle="tab"><?php echo $tab_design; ?></a></li>
            <li><a href="#tab-image" data-toggle="tab"><?php echo $entry_image; ?></a></li>
            <li><a href="#tab-inventory_filter" data-toggle="tab"><?php echo " Inventory Upload Related"; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active in" id="tab-general">
              <ul class="nav nav-tabs" id="language">
                <?php foreach ($languages as $language) { ?>
                <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
                <?php } ?>
              </ul>
              <div class="tab-content">
                <?php foreach ($languages as $language) { ?>
                <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-name<?php echo $language['language_id']; ?>"><?php echo $entry_name; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="category_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_name[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>"><?php echo $entry_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="category_description[<?php echo $language['language_id']; ?>][description]" placeholder="<?php echo $entry_description; ?>" id="input-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['description'] : ''; ?></textarea>
                    </div>
                  </div>
                   <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-short_description<?php echo $language['language_id']; ?>"><?php echo $entry_short_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="category_description[<?php echo $language['language_id']; ?>][short_description]" placeholder="<?php echo $entry_short_description; ?>" id="input-short_description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['short_description'] : ''; ?></textarea>
                      <div class="text-warning"><?php echo $notice_short_description; ?></div>
                    </div>
                  </div> 
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo $entry_meta_title; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="category_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_meta_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-description<?php echo $language['language_id']; ?>"><?php echo $entry_meta_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="category_description[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-keyword<?php echo $language['language_id']; ?>"><?php echo $entry_meta_keyword; ?></label>
                    <div class="col-sm-10">
                      <textarea name="category_description[<?php echo $language['language_id']; ?>][meta_keyword]" rows="5" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-meta-keyword<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
                    </div>
                  </div>
                </div>
                <?php } ?>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-price-range"><?php echo $entry_price_range; ?></label>
                  <div class="col-sm-2">
                    <input type = "text" name="price_range_start" placeholder="<?php echo $entry_price_range_start; ?>" id="price_range_start" class="form-control" value = "<?php echo isset($price_range_start) ? $price_range_start : ''; ?>" />
                    <?php if ($error_price_range) { ?>
                    <div class="text-danger"><?php echo $error_price_range; ?></div>
                    <?php } ?>
                  </div>
                  <div class="col-sm-2">
                    <input type = "text" name="price_range_end" placeholder="<?php echo $entry_price_range_end; ?>" id="price_range_end" class="form-control" value = "<?php echo isset($price_range_end) ? $price_range_end : ''; ?>" />
                  </div>
                  <div class="col-sm-5">
                    <font color="red">Necessary for Parent Categories (Ex. Kurti, Saree etc.)<br>This range will be shown to customer when setting preference from app.</font>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="tab-data">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-parent"><?php echo $entry_parent; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="path" value="<?php echo $path; ?>" placeholder="<?php echo $entry_parent; ?>" id="input-parent" class="form-control" />
                  <input type="hidden" name="parent_id" value="<?php echo $parent_id; ?>" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-filter"><span data-toggle="tooltip" title="<?php echo $help_filter; ?>"><?php echo $entry_filter; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="filter" value="" placeholder="<?php echo $entry_filter; ?>" id="input-filter" class="form-control" />
                  <div id="category-filter" class="well well-sm" style="height: 150px; overflow: auto;">
                    <ul id="fillter_ui" class="category_filter_ui">
                      <?php 
                      foreach ($category_filters as $key=>$category_filter) { 
                      $category_filter_short_order=$key+1;
                      ?>
                      <li id="category-filter<?php echo $category_filter['filter_group_id']; ?>"><i class="fa fa-sort fa_short"></i><i class="fa fa-minus-circle"></i> <?php echo $category_filter['name']; ?>
                        <input type="hidden" name="category_filter[]" value="<?php echo $category_filter['filter_group_id']; ?>" />
                         <input type="hidden" name="category_filter_short_order[]" value="<?php echo $category_filter_short_order; ?>" />
                      </li>
                      <?php } ?>
                    </ul>
                  </div>
                </div>
                <label class="col-sm-2 control-label" for="input-filter">&nbsp;</label>
                <div class="col-sm-10">
                  <span class="alert_ui_filter_sort">Move up and down selected category to sort filter category</span>
                </div>
              </div>
              <!-- unit_id dropdown -->
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-unit_id"><?php echo $entry_unit_id; ?></label>
                <div class="col-sm-10">
                  <select name="unit_id" id="input-unit_id" class="form-control">
					  <?php if(!empty($units_array)) { foreach ($units_array as $unit) { ?>
						<?php if (isset($unit['unit_name']) && $unit['unit_id'] == $unit_id && !empty($unit_id)) { ?>
							<option value="<?php echo $unit['unit_id']; ?>" selected="selected"><?php echo $unit['unit_name']; ?></option>
                        <?php } else { ?>
							<option value="<?php echo $unit['unit_id']; ?>"><?php echo $unit['unit_name']; ?></option>
                          <?php } ?>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_store; ?></label>
                <div class="col-sm-10">
                  <div class="well well-sm" style="height: 150px; overflow: auto;">
                    <div class="checkbox">
                      <label>
                        <?php if (in_array(0, $category_store)) { ?>
                        <input type="checkbox" name="category_store[]" value="0" checked="checked" />
                        <?php echo $text_default; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="category_store[]" value="0" />
                        <?php echo $text_default; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php foreach ($stores as $store) { ?>
                    <div class="checkbox">
                      <label>
                        <?php if (in_array($store['store_id'], $category_store)) { ?>
                        <input type="checkbox" name="category_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
                        <?php echo $store['name']; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="category_store[]" value="<?php echo $store['store_id']; ?>" />
                        <?php echo $store['name']; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-keyword"><span data-toggle="tooltip" title="<?php echo $help_keyword; ?>"><?php echo $entry_keyword; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="<?php echo $entry_keyword; ?>" id="input-keyword" class="form-control" />
                  <?php if ($error_keyword) { ?>
                  <div class="text-danger"><?php echo $error_keyword; ?></div>
                  <?php } ?>                
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_image; ?></label>
                <div class="col-sm-10"><a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                  <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-top"><span data-toggle="tooltip" title="<?php echo $help_top; ?>"><?php echo $entry_top; ?></span></label>
                <div class="col-sm-10">
                  <div class="checkbox">
                    <label>
                      <?php if ($top) { ?>
                      <input type="checkbox" name="top" value="1" checked="checked" id="input-top" />
                      <?php } else { ?>
                      <input type="checkbox" name="top" value="1" id="input-top" />
                      <?php } ?>
                      &nbsp; </label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_non_returnable; ?></label>
                <div class="col-sm-4">
                  <input type="checkbox" name="non_returnable"  value="1" class="form-control edit_track" data-block-name="profile" id="non_returnable" data-change="false" data-old-value="<?php echo $non_returnable;?>" <?php if( $non_returnable ){ ?> checked <?php } ?> />
                  <input type="hidden" name="non_returnable"  value="0"  id="non_returnable_hidden" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-column"><span data-toggle="tooltip" title="<?php echo $help_column; ?>"><?php echo $entry_column; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="column" value="<?php echo $column; ?>" placeholder="<?php echo $entry_column; ?>" id="input-column" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
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
            </div>
            <div class="tab-pane" id="tab-design">
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_store; ?></td>
                      <td class="text-left"><?php echo $entry_layout; ?></td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-left"><?php echo $text_default; ?></td>
                      <td class="text-left"><select name="category_layout[0]" class="form-control">
                          <option value=""></option>
                          <?php foreach ($layouts as $layout) { ?>
                          <?php if (isset($category_layout[0]) && $category_layout[0] == $layout['layout_id']) { ?>
                          <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
                          <?php } else { ?>
                          <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                        </select></td>
                    </tr>
                    <?php foreach ($stores as $store) { ?>
                    <tr>
                      <td class="text-left"><?php echo $store['name']; ?></td>
                      <td class="text-left"><select name="category_layout[<?php echo $store['store_id']; ?>]" class="form-control">
                          <option value=""></option>
                          <?php foreach ($layouts as $layout) { ?>
                          <?php if (isset($category_layout[$store['store_id']]) && $category_layout[$store['store_id']] == $layout['layout_id']) { ?>
                          <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
                          <?php } else { ?>
                          <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                        </select></td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="tab-pane" id="tab-image">
              <div class="table-responsive">
                <table id="images" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_image; ?></td>
                      <td class="text-left"><?php echo $entry_image_height; ?></td>
                      <td class="text-left"><?php echo $entry_image_width; ?></td>
                      <td class="text-left"><?php echo $entry_sort_order; ?></td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody id="multiple_image" data-count-row="<?php echo count($category_images);?>" >
                    <?php $image_row = 0; ?>
                    <?php foreach ($category_images as $category_image) { ?>
                    <tr id="image-row<?php echo $image_row; ?>">
                      <td class="text-left"><a href="" id="thumb-image<?php echo $image_row; ?>" data-directory="<?php echo $category_image['directory']; ?>"  data-toggle="image" class="img-thumbnail"><img src="<?php echo $category_image['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="category_image[<?php echo $image_row; ?>][image]" value="<?php echo $category_image['image']; ?>" id="input-image<?php echo $image_row; ?>" /></td>
                      <td class="text-right"><input type="text" name="category_image[<?php echo $image_row; ?>][image_height]" value="<?php echo $category_image['image_height']; ?>" placeholder="<?php echo $entry_image_height; ?>" class="form-control" /></td>
                      <td class="text-right"><input type="text" name="category_image[<?php echo $image_row; ?>][image_width]" value="<?php echo $category_image['image_width']; ?>" placeholder="<?php echo $entry_image_width; ?>" class="form-control" /></td>
                      <td class="text-right"><input type="text" name="category_image[<?php echo $image_row; ?>][sort_order]" value="<?php if($image_row == 0){echo "1";}else{echo $category_image['sort_order'];} ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>
                      <td class="text-left"><button type="button" onclick="$('#image-row<?php echo $image_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $image_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="4"></td>
                      <td class="text-left"><button type="button" onclick="addImage();" data-toggle="tooltip" title="<?php echo $button_image_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            <div class="tab-pane" id="tab-inventory_filter"> 
            <div class="form-group">
                <label class="col-sm-2 control-label" for="input-filter"><span data-toggle="tooltip" title="<?php echo $help_filter; ?>"><?php echo 'Filter Groups'; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="filter-inventory" value="" placeholder="<?php echo 'Filter Groups'; ?>" id="input-filter-inventory" class="form-control" />
                  <div id="category-filter-inventory" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($category_filters_groups as $filtergoup) { ?>
                    <div id="category-filter-inventory<?php echo $filtergoup['filter_group_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $filtergoup['name']; ?>
                      <input type="hidden" name="category-filter-inventory[]" value="<?php echo $filtergoup['filter_group_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-filter"><span data-toggle="tooltip" title="<?php echo $help_filter; ?>"><?php echo 'Non Mandatory Filter Groups'; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="filter-nonmandatory-inventory" value="" placeholder="<?php echo 'Non Mandatory Filter Groups'; ?>" id="input-non-mandatory-filter-inventory" class="form-control" />
                  <div id="category-nonmandatoryfilter-inventory" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php if(!empty($non_mandatory_filter_groups)) { foreach ($non_mandatory_filter_groups as $filtergoup) { ?>
                    <div id="category-nonmandatoryfilter-inventory<?php echo $filtergoup['filter_group_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $filtergoup['name']; ?>
                      <input type="hidden" name="category-nonmandatoryfilter-inventory[]" value="<?php echo $filtergoup['filter_group_id']; ?>" />
                    </div>
                    <?php } } ?>
                  </div>
                </div>
              </div>
              
               <div class="form-group">
                <label class="col-sm-2 control-label" for="input-filter"><span data-toggle="tooltip" title="<?php echo $help_filter; ?>"><?php echo 'Naming Filter Groups'; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="filter-naming-inventory" value="" placeholder="<?php echo 'Naming Filter Groups'; ?>" id="input-naming-filter-inventory" class="form-control" />
                  <div id="category-naming-inventory" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php if(!empty($naming_filter_groups)) { foreach ($naming_filter_groups as $filtergoup) { ?>
                    <div id="category-naming-inventory<?php echo $filtergoup['filter_group_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $filtergoup['name']; ?>
                      <input type="hidden" name="category-naming-inventory[]" value="<?php echo $filtergoup['filter_group_id']; ?>" />
                    </div>
                    <?php } } ?>
                  </div>
                </div>
              </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo "Min_Weight"; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="Min_Weight" value="<?php echo $min_weight; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" />
                </div>
      </div>
       <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo "Max_Weight"; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="Max_Weight" value="<?php echo $max_weight; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" />
                </div>
      </div>

             <div class="form-group">
        <label class="col-sm-2 control-label" for="input-show-for-import"><span data-toggle="tooltip" title="<?php echo "Checks if category need to be shown for import inventory listing or not"; ?>"><?php echo "Show For Import"; ?></span></label>
                <div class="col-sm-10">
                  <div class="checkbox-show-for-import">
                    <label>
                      <?php if ($show_for_import) { ?>
                      <input type="checkbox" name="show_for_import" value="1" checked="checked" id="input-show-for-import" />
                      <?php } else { ?>
                      <input type="checkbox" name="show_for_import" value="1" id="input-show-for-import" />
                      <?php } ?>
                      &nbsp; </label>
                  </div>
                </div>
              </div>
              </div>
          </div>
          <input type="hidden" id="changes_data" name="changes_data" value="<?php echo $changes_data; ?>" />
        </form>
      </div>
    </div>
  </div>
  <script src="view/javascript/jquery/jquery-ui.min_sortable.js"></script>
<script type="text/javascript">

var image_row = '<?php echo $image_row; ?>';

function addImage() {
  html  = '<tr id="image-row' + image_row + '">';
  html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '" data-directory="" data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /><input type="hidden" name="category_image[' + image_row + '][image]" value="" id="input-image' + image_row + '" /></td>';
  html += '  <td class="text-right"><input type="text" name="category_image[' + image_row + '][image_height]" value="" placeholder="<?php echo $entry_image_height; ?>" class="form-control" /></td>';
  html += '  <td class="text-right"><input type="text" name="category_image[' + image_row + '][image_width]" value="" placeholder="<?php echo $entry_image_width; ?>" class="form-control" /></td>';
  html += '  <td class="text-right"><input type="text" name="category_image[' + image_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
  html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '</tr>';
  $('#images tbody').append(html);
  image_row++;
}

<?php foreach ($languages as $language) { ?>
$('#input-description<?php echo $language['language_id']; ?>').summernote({
  height: 300
});
$('#input-short_description<?php echo $language['language_id']; ?>').summernote({
  height: 110
});
<?php } ?>


</script>
<script type="text/javascript">
    //set global in first key of seller array
     var item1 = [
            {
            'name': 'GLOBAL',
            'seller_id': 'gbl'
            },
          <?php 
          // setting up all seller in js array          
          foreach($sellers_list as  $slist){
            ?>
            {
            'name': "<?php echo $slist['name'];?>",
            'seller_id': "<?php echo $slist['seller_id'];?>"
            },
          <?php
          }
          ?>
          ];
// autocomplete of commission rule
$('input[name=\'commission\']').autocomplete({
  'source': function(request, response) {   
    
    if (request!='') {
      
      item = item1;
      item = item.filter(function(n){ return n != undefined });
      for (var i = 0; i < item.length; i++) {
        if ((item[i]['name']).toLowerCase().indexOf(request) < 0) {             
         delete item[i];
        }
      }      
    }else{
      item = item1;
    }
    item = item.filter(function(n){ return n != undefined });    
    response($.map(item, function(item) {      
   
          return {
            label: item['name'],
            value: item['seller_id']
          }
        }));
  },
  'select': function(item) {

    $('#input-commission2').removeClass('hide');
    $("#input-commission").prop('disabled', true);
    $("#input-commission").addClass('isdisabled');
    $('#add_amt').removeClass('hide');
    $('#add_amt').data('commid',item['value']);
    $("#input-commission").css({"float":"left", "width":"70%"});

    $('input[name=\'commission\']').val('');
    $('#category-commission' + item['value']).remove();
    $('#category-commission').append('<div id="category-commission' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" id="'+item['value']+'" name="category_commission['+item['value']+']" value="" /><span style="margin-left: 20px;" id="com-' + item['value'] + '"></span></div>');
  }
});
// remove seller name from commission list
$('#category-commission').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
    $("#input-commission").prop('disabled', false);
    $("#input-commission").removeClass('isdisabled');
});
// add commission rate to the seller
$('#add_amt').click(function(){
  
  var commission = $('#input-commission2').val();
  
  if (parseInt(commission)<=0 || commission=='') {
    alert('Please enter proper commission rate.');
    return false;
  }

  var commid = $(this).data('commid');
  
  $('#'+commid).val(commission);
  $(this).data('commid','');
  $('#input-commission2').val('');
  $(this).addClass('hide');
  $('#input-commission2').addClass('hide');
  $("#input-commission").css({"width":"100%"});
  $('#com-'+commid).html(commission);
  $("#input-commission").prop('disabled', false);
  $("#input-commission").removeClass('isdisabled');
});
// check the last seller commission rate and than submit the form
$('#form-category-submit').click(function(){
  
  var last_seller_commission = $('#category-commission').last().children().last().children().last().html();
  
  if (last_seller_commission=='') {
    alert('Please enter commission rate of last seller.');
    return false;
  }else{
      edit_track();
    if(document.getElementById('non_returnable').checked){
        document.getElementById('non_returnable_hidden').disabled = true;
    }
    $('#form-category').submit();
  }
 });
// allow only numbers key for input
 $("#input-commission2").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    //console.log(e.keyCode);
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
         // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
         // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
             // let it happen, don't do anything
             return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});



$('input[name=\'path\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        json.unshift({
          category_id: 0,
          name: '<?php echo $text_none; ?>'
        });

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
    $('input[name=\'path\']').val(item['label']);
    $('input[name=\'parent_id\']').val(item['value']);
  }
});
</script> 

<!-- category inventory filters code in inventory upload data tab-->

  <script type="text/javascript">
$('input[name=\'filter-inventory\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/category/autocomplete_filter_group&token=<?php echo $token; ?>&filter_group_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['filter_group_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'filter-inventory\']').val('');
    $('#category-filter-inventory' + item['value']).remove();

    $('#category-filter-inventory').append('<div id="category-filter-inventory' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="category-filter-inventory[]" value="' + item['value'] + '" /></div>');
  }
});
</script> 
<!-- category inventory non mandatory filters code in inventory upload data tab-->

  <script type="text/javascript">
$('input[name=\'filter-nonmandatory-inventory\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/category/autocomplete_filter_group&token=<?php echo $token; ?>&filter_group_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['filter_group_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'filter-nonmandatory-inventory\']').val('');
    $('#category-nonmandatoryfilter-inventory' + item['value']).remove();
    $('#category-nonmandatoryfilter-inventory').append('<div id="category-nonmandatoryfilter-inventory' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="category-nonmandatoryfilter-inventory[]" value="' + item['value'] + '" /></div>');
  }
});
</script> 

<!-- category inventory naming filters code in inventory upload data tab-->

  <script type="text/javascript">
$('input[name=\'filter-naming-inventory\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/category/autocomplete_filter_group&token=<?php echo $token; ?>&filter_group_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['filter_group_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'filter-naming-inventory\']').val('');
    $('#category-naming-inventory' + item['value']).remove();
    $('#category-naming-inventory').append('<div id="category-naming-inventory' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="category-naming-inventory[]" value="' + item['value'] + '" /></div>');
  }
});
</script> 
<script type="text/javascript">
$('input[name=\'filter\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/category/autocomplete_filter_group&token=<?php echo $token; ?>&filter_group_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['filter_group_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'filter\']').val('');
    $('#category-filter' + item['value']).remove();
    var orderNumber=parseInt($('#fillter_ui li').length)+parseInt(1);
    $('#category-filter ul').append('<li id="category-filter' + item['value'] + '"><i class="fa fa-sort fa_short"></i><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="category_filter[]" value="' + item['value'] + '" /><input type="hidden" name="category_filter_short_order[]" value="'+orderNumber+'"></li>');


    $('#category-filter-inventory').append('<div id="category-filter-inventory' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="category-filter-inventory[]" value="' + item['value'] + '" /></div>');
  }
});

//hsn_code for category mapping
$('input[name=\'hsn-code\']').autocomplete({
  
  'source': function(request, response) {
    
    $.ajax({
      url: 'index.php?route=catalog/filter/hsn_id_autocomplete&token=<?php echo $token; ?>&hsn_code=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            
            label: item['hsn_code'],
            value: item['hsn_code']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'hsn_code\']').val('');
    $('#hsn-naming-code' + item['value']).remove();

    $('#hsn-naming-code').append('<div id="hsn-naming-code' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="hsn-naming-code[]" value="' + item['value'] + '" /></div>');
  }
});

$('#hsn-naming-code').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});

$('#category-filter').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
    $('#fillter_ui li').each(function( index ) {
    var current_short_number=parseInt(index)+parseInt('1');
    $(this).find('input[name="category_filter_short_order[]"]').val(current_short_number);
    })
});
$('#category-filter-inventory').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});
$('#category-nonmandatoryfilter-inventory').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});
$('#category-naming-inventory').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});
</script> 
  <script type="text/javascript">
$('#language a:first').tab('show');
</script></div>

<script type="text/javascript">

$('#input-store_description').summernote({
             height: 300
            });

$('.store_id').change(function(event) {
  var store_id = $('.store_id').val();
  var category_id = $(this).attr('data-category-id');  
    $.ajax({
      url: 'index.php?route=catalog/category/store_data&token=<?php echo $token; ?>',
      dataType: 'json',
      data: '&store_id='+ store_id+ '&category_id='+ category_id, 
      success: function(json) {    
           $('#input-language').val(json['language']);
           $('#input-meta_title').val(json['meta_title']);
           $('#input-meta_keyword').val(json['meta_keywords']);
           $('#input-meta_description').val(json['meta_description']);
           $('#input-store_description').val(json['description']);
           $('#input-store_description').next(".note-editor").remove();
           $('#input-store_description').summernote({
             height: 300
            });
          }
    });
});
</script>
<script>
    $('.edit_track').on('change',function(){
        trackChange(this);
    });

    function trackChange(obj){
        if($(obj).val().trim() != $(obj).attr('data-old-value').trim()){
            $(obj).attr('data-change','true');
        }else{
            $(obj).attr('data-change','false');
        }
    }

    function edit_track(){
        var old_data_format = {};

        $('.edit_track').each(function(){
            var data_change = $(this).data('change');

            var old_value = $(this).data('old-value');
            var new_value = $(this).val();
            var name = $(this).attr('name');

            if(name=='non_returnable'){
                if($(this).prop('checked') == true)
                    new_value = 1;
                else
                    new_value = 0;
                if(old_value != new_value){
                    data_change = $(this).attr('data-change');
                }
            }

            if(typeof old_value === 'string'){
                old_value = old_value.replace(/\r?\n|\r/g,'');
                old_value = old_value.replace(/"/g, '\\"');
            }

            if(typeof new_value === 'string'){
                new_value = new_value.replace(/\r?\n|\r/g,'');
                new_value = new_value.replace(/"/g, '\\"');
            }

            if( data_change ) {
                old_data_format[name] = $.parseJSON('{"old_value":"'+  old_value + '","new_value":"' + new_value + '"}');
            }
        });

        if( $('#changes_data').val().length > 0 ){
            old_data_format = $.parseJSON($('#changes_data').val());
        }

        $('#changes_data').val( JSON.stringify(old_data_format) );
    }
</script>

<script type="text/javascript">
  $('#tax_class').on('keyup',function(){
    if(tax_selected == 1 && state_selected == 1) {
      $('#tax_submit').show();

    }
  });
  
    $('#states').on('keyup',function(){
    if(tax_selected == 1 && state_selected == 1) {
      $('#tax_submit').show();
    }
  });
   
    $('#tax_submit').on('click',function() {
    var show_data = $('#states_id').val() + ',' + $('#tax_classid').val();
    $('#send_tax_data').val(show_data);
    $('#display-input').val($('#states').val() + '=>' + $('#tax_class').val());
    $('#display-input').show();
    $('.display-input').show();
    $("#display-input").prop("readonly", true); 
  });
  
  
$( "#fillter_ui" ).sortable({
 // axis: "y",
  containment: "parent",
  cursorAt: { top:1 },
  stop: function( event, ui ) {
       $('#fillter_ui li').each(function( index ) {
          var current_short_number=parseInt(index)+parseInt('1');
          $(this).find('input[name="category_filter_short_order[]"]').val(current_short_number);
       })
  }
});
$( "#fillter_ui" ).disableSelection();
</script>
<?php echo $footer; ?>
