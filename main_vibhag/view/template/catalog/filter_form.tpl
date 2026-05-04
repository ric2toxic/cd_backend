<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-filter" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-filter" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label"><?php echo $entry_group; ?></label>
            <div class="col-sm-10">
              <?php foreach ($languages as $language) { ?>
              <div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
                <input type="text" name="filter_group_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($filter_group_description[$language['language_id']]) ? $filter_group_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_group; ?>" class="form-control" />
              </div>
              <?php if (isset($error_group[$language['language_id']])) { ?>
              <div class="text-danger"><?php echo $error_group[$language['language_id']]; ?></div>
              <?php } ?>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
            </div>
          </div>
          <table id="filter" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left required"><?php echo $entry_name ?></td>
                <td class="text-right"><?php echo $entry_sort_order; ?></td>
                <td class="text-right"><?php echo $tab_image; ?></td> <!-- button_image_add -->
                <td></td>
              </tr>
            </thead>
            <tbody>
              <?php $filter_row = 0; ?>
              <?php foreach ($filters as $filter) { 
               ?>
               <?php if($filter['filter_id']) { ?>
               <input type="hidden" name="filter_old[<?php echo $filter_row; ?>][filter_id]" value="<?php echo $filter['filter_id']; ?>">
               <?php } ?>
              <tr id="filter-row<?php echo $filter_row; ?>">
                <td class="text-left" style="width: 70%;"><input type="hidden" name="filter[<?php echo $filter_row; ?>][filter_id]" value="<?php echo $filter['filter_id']; ?>" />
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
                    <input type="text" name="filter[<?php echo $filter_row; ?>][filter_description][<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($filter['filter_description'][$language['language_id']]) ? $filter['filter_description'][$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name ?>" class="form-control" />
                  </div>
                  <?php if (isset($error_filter[$filter_row][$language['language_id']])) { ?>
                  <div class="text-danger"><?php echo $error_filter[$filter_row][$language['language_id']]; ?></div>
                  <?php } ?>
                  <?php } ?></td>
                <td class="text-right"><input type="text" name="filter[<?php echo $filter_row; ?>][sort_order]" value="<?php echo $filter['sort_order']; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" /></td>
                <td class="" style="text-align: center;">
                  <a href="" 
                     id="thumb-image<?php echo $filter_row; ?>" 
                     data-directory=""  
                     data-toggle="image" 
                     class="img-thumbnail">
                    <img src="<?php echo $filter['image']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" />
                  </a>
                  <input type="hidden" name="filter[<?php echo $filter_row; ?>][image]" value="<?php echo $filter['image_old']; ?>" id="input-image<?php echo $filter_row; ?>"/>
                  <input type="hidden" name="filter[<?php echo $filter_row; ?>][width]" value="<?php echo $filter['width']; ?>" />
                  <input type="hidden" name="filter[<?php echo $filter_row; ?>][height]" value="<?php echo $filter['height']; ?>" />
                  <input type="hidden" name="filter[<?php echo $filter_row; ?>][image_old]" value="<?php echo $filter['image_old']; ?>" />
                </td>
                <td class="text-left"><button type="button" onclick="removeFilterRow('filter-row<?php echo $filter_row; ?>')" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
              </tr>
              <?php $filter_row++; ?>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3"></td>
                <td class="text-left"><a onclick="addFilterRow();" data-toggle="tooltip" title="<?php echo $button_filter_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></a></td>
              </tr>
            </tfoot>
          </table>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--

var filter_row = <?php echo $filter_row; ?>;
var filter_deleted = 0;
var max_filter_deleted = <?php echo $maxNoOfFiltersCanBeDeleted ?? 3; //default max deleted filters - 3  ?>;

function addFilterRow() {
	html  = '<tr id="filter-row' + filter_row + '">';	
    html += '  <td class="text-left" style="width: 70%;"><input type="hidden" name="filter[' + filter_row + '][filter_id]" value="" />';
	<?php foreach ($languages as $language) { ?>
	html += '  <div class="input-group">';
	html += '    <span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span><input type="text" name="filter[' + filter_row + '][filter_description][<?php echo $language['language_id']; ?>][name]" value="" placeholder="<?php echo $entry_name ?>" class="form-control" />';
    html += '  </div>';
	<?php } ?>
	html += '  </td>';
	html += '  <td class="text-right"><input type="text" name="filter[' + filter_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" /></td>';
  html += ' <td class="text-center">';
  //html +=' <a href="" id="thumb-image<?php echo $filter_row; ?>" data-directory="" data-toggle="image" class="img-thumbnail">';
  html +=' <a href="" id="thumb-image'+filter_row+'" data-directory="" data-toggle="image" class="img-thumbnail">';
  html += '<img src="<?php echo $placeholder;?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /> </a>';
  html += '<input type="hidden" name="filter['+filter_row+'][image]" value="placeholder.png" id="input-image'+filter_row+'"/>';
  html += '<input type="hidden" name="filter['+filter_row+'][width]" value="74" />';
  html += '<input type="hidden" name="filter['+filter_row+'][height]" value="111" />';
  html += '<input type="hidden" name="filter['+filter_row+'][image_old]" value="placeholder.png" />';
  html +='</td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#filter-row' + filter_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';	
	
	$('#filter tbody').append(html);
	
	filter_row++;
}
function removeFilterRow(rowId=''){

  var result = confirm("Are you sure you want to delete this row?");
  if (result) {
        if(rowId!=''){
        ++filter_deleted;
        if(filter_deleted <= max_filter_deleted) {
            $('#'+rowId).remove();  
        }else{
          alert("You can not delete more than "+max_filter_deleted+" filters at a time");
        }
      }
  }
}

//--></script></div>
<?php echo $footer; ?> 