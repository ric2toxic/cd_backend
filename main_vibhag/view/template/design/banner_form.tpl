<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button id="submit_btn" type="submit" form="form-banner" data-toggle="tooltip" title="<?php echo $text_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
            
            <?php if ($mode == 'add' && (count($store_data) > 0)) { ?>
             <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_store; ?></label>
            <div class="col-sm-10">
              <select onchange="getMenuByStoreID(this.value)" name="store_id" id="input-status" class="form-control">
                  <option value="0" selected="selected"><?php echo $text_default_store; ?></option>
                <?php foreach($store_data as $store) { ?>
                <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
            
            <?php } ?>
            
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
            <?php if($mode == 'add') { ?>   
            <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_is_category; ?></label>
            <div class="col-sm-10">
                <select onchange="showCategory(this.value)" <?php if($mode == 'edit') echo 'disabled'; ?> name="is_category" id="is_category" class="form-control"> 
                    <?php /* if ($is_category) { ?> 
                        <option selected="selected" value="0"><?php echo $entry_no; ?></option>
                        <option value="1"><?php echo $entry_yes; ?></option> 
                    <?php } else { ?>
                        <option value="0"><?php echo $entry_no; ?></option>
                        <option selected="selected" value="1"><?php echo $entry_yes; ?></option> 
                    <?php } */?>
                    
                    <option <?php if ($is_category == '0') echo 'selected' ?> value="0"><?php echo $entry_no; ?></option>
                    <option <?php if ($is_category == '1') echo 'selected' ?> value="1"><?php echo $entry_yes; ?></option> 
                    
              </select>
            </div>
          </div> 
            <?php } ?>
            
            <?php if($mode == 'edit') { ?>   
                <input type="hidden" name="is_category" value="<?php echo $is_category; ?>" />
            <?php } ?>
            
          <?php if($is_category == '1' || $mode == 'add') { ?>  
          <div class="form-group" id="select_category_outer">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_category; ?></label>
            <div class="col-sm-10">
                <select name="select_category" id="select_category" class="form-control" onchange="selectCategory(this.value,<?php echo isset($banner_id) ? $banner_id : '0'; ?>)">
                <option value="0"><?php echo $entry_select_category; ?></option>
                <!--<option value="-1"><?php echo $entry_select_all; ?></option>-->
                <?php foreach($categories as $val){  ?>
                    <option <?php if($val['category_id'] == $category_id){ echo 'selected'; }?> value="<?php echo $val['category_id']; ?>"><?php echo $val['name'] ?></option>
                <?php } ?>
              </select>
              <?php if ($error_select_category) { ?>
              <div class="text-danger"><?php echo $error_select_category; ?></div> 
              <?php } ?>
              
              <!--show default message -->
              <?php if($mode == 'edit' && $category_id == '0'){ ?> 
                <div class="text-danger"><?php echo $notice_select_category; ?></div> 
              <?php } ?>
            </div>
          </div>  
          <?php } ?>               
          <table id="images" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left"><?php echo $entry_title; ?></td>
                <td class="text-left"><?php echo $entry_type; ?></td>
                <td class="text-left"><?php echo $entry_link; ?></td>
                <?php if(CART_BANNER_MOBILE_APP == $banner_id) { ?>
                <td class="text-left">Positive Category</td>
                <td class="text-left">Negative Category</td>
                <?php } ?>
                <td class="text-left"><?php echo $entry_image; ?></td>
                <td class="text-right"><?php echo $entry_sort_order; ?></td>
                <td class="text-right"><?php echo $entry_status; ?></td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              <?php $image_row = 0;
                    $new_image_row = 0;
              ?>
              <?php 
              if(count($banner_images) > 0){
              foreach ($banner_images as $banner_image) { 
              ?>
              <tr id="image-row<?php echo $image_row; ?>">
                <td class="text-left">
                  <input type="hidden" name="banner_image[<?php echo $image_row; ?>][banner_item]" value="<?php echo $image_row; ?>" />
                  <input type="hidden" name="banner_image[<?php echo $image_row; ?>][banner_image_id]" value="<?php echo $banner_image['banner_image_id']; ?>" />

                  <?php 
                    foreach ($languages as $language) { 
                        if($language['language_id'] == 1){ 
                  ?>
                            <div class="input-group pull-left"><span class="input-group-addon">
                                <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> </span>
                                <input type="text" name="banner_image[<?php echo $image_row; ?>][banner_image_description][<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($banner_image['banner_image_description'][$language['language_id']]) ? $banner_image['banner_image_description'][$language['language_id']]['title'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" class="form-control" />
                            </div>
                            <?php if (isset($error_banner_image[$image_row]['banner_image_description'][$language['language_id']]['error_title'])) { ?>
                                <div class="text-danger"><?php echo $error_banner_image[$image_row]['banner_image_description'][$language['language_id']]['error_title']; ?></div>
                            <?php } 
                        } 
                    } 
                  ?>
                
                </td>
                <td class="text-left">
                      <?php if ($banner_image['type'] == 'category') { 
                              $type_value = $banner_image['value_label'];
                            } else {
                              $type_value = $banner_image['link']; 
                            }
                      ?>

                      <select class ="form-control select_item" html-data="<?php echo $image_row; ?>" id="select_item_<?php echo $image_row; ?>" name="banner_image[<?php echo $image_row; ?>][type]" data-link-value-label="<?php echo $type_value; ?>" data-link-value="<?php echo $banner_image['link']; ?>">
                        <option value="link" <?php if($banner_image['type'] == 'link'){ ?> selected <?php } ?> >Link</option>
                        <option value="search" <?php if($banner_image['type'] == 'search'){ ?> selected <?php } ?>>Search</option>
                        <option value="category" <?php if($banner_image['type'] == 'category'){ ?> selected <?php } ?>>Category</option>
                        <option value="sale" <?php if($banner_image['type'] == 'sale'){ ?> selected <?php } ?>>Sale</option>
                        <option value="others" <?php if($banner_image['type'] == 'others'){ ?> selected <?php } ?>>Others</option>
                      </select>
                </td>
                <td class="text-left">
                      <?php if ($banner_image['type'] == 'category') { 
                              $type_value = $banner_image['value_label'];
                              $class = "form-control banner_type_value forAutoSelect";
                            } elseif ($banner_image['type'] == 'sale') {
                              $type_value = 'Sale';
                               $class = "form-control banner_type_value";
                            }else {
                              $type_value = $banner_image['link']; 
                               $class = "form-control banner_type_value";
                            }
                      ?>

                      <input type="text" 
                              id="banner_type_value_<?php echo $image_row; ?>" 
                              data-hidden-id="banner_type_value_hidden<?php echo $image_row; ?>" 
                              class="<?php echo $class; ?>" 
                              html-data="<?php echo $image_row; ?>" 
                              name="banner_image[<?php echo $image_row; ?>][link]" 
                              value="<?php echo $type_value; ?>" />

                      <input type="hidden" 
                             id="banner_type_value_hidden<?php echo $image_row; ?>" 
                             html-data="<?php echo $image_row; ?>" 
                             class="form-control value"  
                             name="banner_image[<?php echo $image_row; ?>][category_id]" 
                             value="<?php echo $banner_image['link']; ?>" />

                      <label style="margin-top: 10px;">Target Blank</label>
                      <?php if($banner_image['target_blank'] == 1){ ?>    
                          <input type="checkbox" value="1" checked style="margin-bottom:-2px;margin-left:6px;" name="banner_image[<?php echo $image_row; ?>][target_blank]">
                      <?php }else{ ?>   
                          <input type="checkbox" value="0" style="margin-bottom:-2px;margin-left:6px;" name="banner_image[<?php echo $image_row; ?>][target_blank]">
                      <?php } ?>

                      <?php if (isset($error_banner_image[$image_row]['error_category_id'])) { ?>
                                <div class="text-danger"><?php echo $error_banner_image[$image_row]['error_category_id']; ?></div>
                      <?php } ?>
                </td>

              <?php if(CART_BANNER_MOBILE_APP == $banner_id) { ?>
                <td class="text-left">
                    <select 
                    id="banner_type_cart_value_<?php echo $image_row; ?>" 
                    class="form-control banner_type_value" 
                    html-data="<?php echo $image_row; ?>" 
                    name="banner_image[<?php echo $image_row; ?>][positive_category][]" style="width:200px; min-height: 150px" multiple>
                    <?php
                     $positive_category = array();
                     if(isset($banner_image['cart_categories']['positive_category']))
                     {
                      $positive_category = $banner_image['cart_categories']['positive_category'];
                     }

                     foreach($cart_category as $category)
                     {
                     ?>
                      <option value="<?php echo $category['category_id']; ?>" <?php if(in_array($category['category_id'], $positive_category)) { echo "selected"; } ?>>
                      <?php echo $category['name']; ?></option>
                    <?php } ?> 
                    </select>
                </td>

                 <td class="text-left">
                    <select 
                    id="banner_type_cart_value_<?php echo $image_row; ?>" 
                    class="form-control banner_type_value" 
                    html-data="<?php echo $image_row; ?>" 
                    name="banner_image[<?php echo $image_row; ?>][negative_category][]" style="width:200px; min-height: 150px" multiple>
                    <?php
                     $negative_category = array();
                     if(isset($banner_image['cart_categories']['negative_category']))
                     {
                      $negative_category = $banner_image['cart_categories']['negative_category'];
                     }

                     foreach($cart_category as $category)
                     {
                     ?>
                      <option value="<?php echo $category['category_id']; ?>" <?php if (in_array($category['category_id'], $negative_category)) { echo "selected"; } ?>>
                      <?php echo $category['name']; ?></option>
                    <?php } ?> 
                    </select> 
                </td>
                <?php } ?>
                <td class="text-left">
                  <?php foreach ($languages as $language) { 
                    if($language['language_id'] == 1){ 
                  ?>
                  <div class="col-sm-12 input-group pull-left">
                      
                  <?php
                        if( !empty($banner_image['banner_image_description'][$language['language_id']]['thumb']) ){   
                            $src = $banner_image['banner_image_description'][$language['language_id']]['thumb'];
                        }else{
                            $src = $placeholder; 
                            //$src = ''; 
                        }
                  ?>  
                  <a href="" id="thumb-image<?php echo $new_image_row; ?>" data-toggle="image" data-directory="<?php //echo $banner_image['banner_image_description'][$language['language_id']]['directory'];?>" class="img-thumbnail">  
                      <img src="<?php echo $src; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                  <input type="hidden" name="banner_image[<?php echo $image_row; ?>][banner_image_description][<?php echo $language['language_id']; ?>][image]" value="<?php echo $banner_image['banner_image_description'][$language['language_id']]['image']; ?>" id="input-image<?php echo $new_image_row; ?>" />
                  <input type="hidden" name="banner_image[<?php echo $image_row; ?>][banner_image_description][<?php echo $language['language_id']; ?>][thumb]" value="<?php echo $src; ?>" />
                  </div>
                  <?php $new_image_row++; ?>
                <?php }} ?>
                
                  <?php if ($is_category == '1' || $mode == 'add') { ?>   
                      <div class="category_outer">  
                          <div class="nopadding">
                            <label><?php echo $text_select_all_category; ?></label>
                            <input type="checkbox" <?php if($banner_image['flage_select_all'] == 1) echo 'checked'; ?> class="category-check_<?php echo $image_row; ?> pull-right select_all_cat" onclick="checkAllCategory(this,<?php echo $image_row; ?>);" />
                          </div>   
                          <?php 
                            $categores_val = '';
                            foreach($categories as $values){ 
                                    $categores_val .= $values['category_id'].',';
                            } 
                            $categores_val = rtrim($categores_val,',');
                            
                            if($banner_image['flage_select_all'] == 0){
                                $categores_val = $category_id;
                            }
                          ?>    
                          
                          <div class="col-sm-12 nopadding category_check">
                                <input type="hidden" class="pull-right category-check_<?php echo $image_row; ?>" name="banner_image[<?php echo $image_row; ?>][category_image]" value="<?php echo $categores_val; ?>" />  
                            </div>
                      </div>  
                 <?php } ?>   
                    
                    
                </td>

                <td class="text-right">
                    <input type="text" name="banner_image[<?php echo $image_row; ?>][sort_order]" value="<?php echo $banner_image['sort_order']; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>
                <td>
                    <select name="banner_image[<?php echo $image_row; ?>][status]" class="form-control">  
                        <option <?php if($banner_image['status'] == 1) echo 'selected'; ?> value="1"><?php echo $text_enable; ?></option>
                        <option <?php if($banner_image['status'] == 0) echo 'selected'; ?> value="0"><?php echo $text_disable; ?></option>
                    </select> 
                </td>
                <td class="text-left">
                    <button type="button" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#image-row<?php echo $image_row; ?>, .tooltip').remove() : false;" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
              </tr>
              <?php $image_row++; ?>
              <?php } }else{?>
              <?php if(($category_id != '0')){ ?>
              <tr>
                  <td colspan=8><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
               <?php if(CART_BANNER_MOBILE_APP == $banner_id) { ?>
                <td colspan="8"></td>
                <?php } else { ?>
                <td colspan="6"></td>
                <?php } ?>
                <td class="text-left">
                    <?php if($category_id == '0' && $is_category == '1' && $mode == 'edit' ){ 
                        //do noting
                    ?>
                        
                    <?php }else{ ?>
                        <button type="button" onclick="addImage();" data-toggle="tooltip" title="<?php echo $button_banner_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button>
                    <?php } ?>
                </td>
              </tr>
            </tfoot>
          </table>
        </form>
      </div>
    </div>
  </div>

   <select id="banner_cart_category_data" style="display: none;">
    <?php foreach($cart_category as $category) {  ?>
    <option value="<?php echo $category['category_id']; ?>">
        <?php echo $category['name']; ?>
    </option>
    <?php } ?> 
  </select>

  <script type="text/javascript">
      
    $(document).ready(function(){  
        //check is_category
        var is_cat = $("#is_category").val();
        showCategory(is_cat);
    });  
      
      
    function showCategory(val){
        if(val == 0){
            $("#select_category_outer").hide();
        }else if(val == 1){
            $("#select_category_outer").show();
        }
    }
      
      
    //get menu by store id for add banner case 
    function getMenuByStoreID(store_id){
        //alert(store_id);
        $.ajax({
            type: 'post',
            url: 'index.php?route=design/banner/getMenuByStoreID&token=<?php echo $token; ?>',
            data: {'store_id' : store_id},
            dataType: 'json',
            beforeSend: function() {
                //$('.seller_loader').html('<img src="view/image/ajax-loader.gif" />');
                //$('#all_seller').hide();
            },
            success: function( data ){  
                if(data.html != ''){ //alert(data.html);
                    $('#select_category').html(data.html)
                }
            }
        });
        
    }  
    
    function selectCategory(val,banner_id) {
        if(banner_id != '' && banner_id != '0' && banner_id != 'undefined'){
            var mode = '<?php echo $mode; ?>';
            
            url = '<?php echo HTTP_SERVER; ?>index.php?route=design/banner/edit&token=<?php echo $token; ?>';
            url += '&banner_id=' + banner_id;
            url += '&category_id=' + val;
            url += '&mode=' + mode;
            
            window.location.replace(url);
        }else{
            $(".category_outer").each(function(){
                var all_select = $(this).find('input.select_all_cat:checked').val();
                if (all_select != "on"){ 
                    $(this).find("input[type=checkbox]").prop("checked",false);
                    $(this).find("input[type=checkbox][value="+ val +"]").prop("checked",true); 
                }
            });
        }
    } 
      
    var image_row = <?php echo $image_row; ?>;
    var new_image_row = <?php echo $new_image_row; ?>;
    
    function addImage() {

      var cat_val = $('#select_category').val();
      var banner_cart_category_data = $("#banner_cart_category_data").html();
        
      html  = '<tr id="image-row' + image_row + '">';
      html += '   <td class="text-left">';
      html += '     <input type="hidden" name="banner_image[' + image_row + '][banner_item]" value="' + image_row + '" />';

          <?php foreach ($languages as $language) { 
                  if($language['language_id'] == 1){ 
          ?>
                    html += ' <div class="input-group">';
                    html += '   <span class="input-group-addon">';
                    html += '      <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />';
                    html += '   </span>';
                    html += '   <input type="text" name="banner_image[' + image_row + '][banner_image_description][<?php echo $language['language_id']; ?>][title]" value="" placeholder="<?php echo $entry_title; ?>" class="form-control" />';
                    html += ' </div><br/><br/>';
          <?php } }?>
      html += '  </td>';
            
      html += ' <td class="text-left">';
      html += '   <select class ="form-control select_item" html-data="'+ image_row +'" id="select_item_'+image_row+'" name="banner_image['+image_row+'][type]">';
      html += '     <option value="link">Link</option>';
      html += '     <option value="search">Search</option>';
      html += '     <option value="category">Category</option>';
      html += '     <option value="sale">Sale</option>';
      html += '     <option value="others">Others</option>';
      html += '   </select>';
      html += ' </td>';

      html += ' <td class="text-left">';
      html += '  <input type="text" id="banner_type_value_'+image_row+'" data-hidden-id="banner_type_value_hidden'+image_row+'" class="form-control banner_type_value" html-data="'+ image_row +'" name="banner_image['+image_row+'][link]" value="" />';
      html += '  <input type="hidden" id="banner_type_value_hidden'+image_row+'" html-data="'+image_row+'" class="form-control value" name="banner_image['+image_row+'][category_id]" value="" />';

      html += '   <label style="margin-top: 10px;">Target Blank</label>';
      html +=     '<input type="checkbox" style="margin-bottom:-2px;margin-left: 6px;"  name="banner_image[' + image_row + '][target_blank]">';
      html += ' </td>';

      <?php if(CART_BANNER_MOBILE_APP == $banner_id) { ?>
      html += ' <td class="text-left">';
      html += '  <select id="banner_type_cart_value_'+image_row+'" data-hidden-id="banner_type_cart_value_hidden'+image_row+'" class="form-control banner_type_value" html-data="'+ image_row +'" name="banner_image['+image_row+'][positive_category][]" style="width:200px; min-height: 150px" multiple>';
      html += banner_cart_category_data;
      html += ' </select>';
      html += ' </td>';

      html += ' <td class="text-left">';
      html += '  <select id="banner_type_cart_value_'+image_row+'" data-hidden-id="banner_type_cart_value_hidden'+image_row+'" class="form-control banner_type_value" html-data="'+ image_row +'" name="banner_image['+image_row+'][negative_category][]" style="width:200px; min-height: 150px" multiple>';
      html += banner_cart_category_data;
      html += ' </select>';
      html += ' </td>';
     <?php } ?>
      
      html += '  <td class="text-left">';

        <?php foreach ($languages as $language) { 
          if($language['language_id'] == 1){ 
        ?>
            html += ' <div class="col-sm-12 input-group">';
            html += '   <a href="" id="thumb-image' + new_image_row + '" data-toggle="image" class="img-thumbnail" data-directory="<?php //echo $directory;?>" ><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>';
            html += '     <input type="hidden" name="banner_image[' + image_row + '][banner_image_description][<?php echo $language['language_id']; ?>][image]" value="" id="input-image' + new_image_row + '" />';
            html += ' </div>';
            new_image_row++;
        <?php } } ?>
        
        <?php if ($is_category == '1' || $mode == 'add') { ?>   
            html += ' <div class="category_outer">';
            html += '  <div class="nopadding">';
            html += '   <label><?php echo $text_select_all_category; ?></label>';
            html += '     <input type="checkbox" class="category-check_' + image_row + ' pull-right select_all_cat" onclick="checkAllCategory(this,' + image_row + ');" />';
            html += '  </div>'; 

            html += '  <div class="col-sm-12 nopadding category_check">';
            html += '   <input type="hidden" class="pull-right category-check_' + image_row + '" name="banner_image[' + image_row + '][category_image]" value="<?php echo $category_id; ?>" />';      
            html += '  </div>';
      
            html += ' </div>';
        <?php } ?>

      html += ' </td>';

      html += ' <td class="text-right">';
      html += '   <input type="text" name="banner_image[' + image_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" />';
      html += ' </td>';
      html += ' <td class="text-right">';
      html += '    <select name="banner_image[' + image_row + '][status]" class="form-control">';
      html += '     <option value="1"><?php echo $text_enable; ?></option>';
      html += '     <option value="0"><?php echo $text_disable; ?></option>';
      html += '    </select>';
      html += ' </td>';
      html += ' <td class="text-left">';
      html += '  <button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger">';
      html += '   <i class="fa fa-minus-circle"></i>';
      html += '  </button>';
      html += ' </td>';
      html += '</tr>';

      $('#images tbody').append(html);
      image_row++;
    }
    
    function checkAllCategory(obj,id){
        <?php 
            if(count($categories) > 0){
                $categories_ids = '';
                foreach($categories as $val){  
                        $categories_ids .= $val['category_id'].',';
                }
                $categories_ids = rtrim($categories_ids,',');
            }
        ?>
        
        if ($(obj).is(':checked')) {
            $(obj).attr('value', 'true');
        } else {
            $(obj).attr('value', 'false');
        }
        var all_select = $('.category-check_' + id).val();
        //console.log(all_select);  
           
        if (all_select == 'true'){  
            $('.category-check_' + id).val('<?php echo $categories_ids; ?>'); 
        }else{
            $('.category-check_' + id).val('<?php echo $category_id; ?>'); 
        }
    }
    
</script>

<script type="text/javascript">
  
$(document).delegate( ".forAutoSelect", "click", function() {
 
 var data_id = $(this).attr('html-data');
 var token = '<?php echo $this->session->data['token']; ?>';
 var store_id = 0;

$('#banner_type_value_'+data_id + '.forAutoSelect').autocomplete({
        'source': function (request, response) {
            $.ajax({
              url: 'index.php?route=catalog/menu/autocompleteCategory&store_id=' + store_id + '&filter_name=' + encodeURIComponent(request) + '&token=' + token,
              dataType: 'json',
              success: function (json) {
                response($.map(json, function (item) {
                  return {
                    label: item['name'],
                    value: item['category_id']
                  }
                }));
              }
            });
        },
        'select': function (item) {
            
          var full_label = item['label'];
          var arrow_position = full_label.lastIndexOf(">");
          var child_cat_lable = full_label.substring(arrow_position+1).trim();
          
          $(this).val(child_cat_lable);
          hidden_field_id = $(this).attr('data-hidden-id');
          $('#' + hidden_field_id).val(item['value']);
        }
      });

  });


  $(document).delegate( ".select_item", "change", function() {
    var select_item = $(this).val();
    var data_id = $(this).attr('html-data');
    var link_value_label = $(this).attr('data-link-value-label');
    var data_link_value = $(this).attr('data-link-value');

    // document.getElementById('banner_type_value_'+data_id).value = "";
    // document.getElementById('banner_type_value_hidden'+data_id).value = "";

    if(select_item == "category"){

      document.getElementById('banner_type_value_hidden'+data_id).value = "";

      $('#banner_type_value_'+data_id).attr('placeholder',' Select Category');
      $('#banner_type_value_'+data_id).addClass('forAutoSelect');
      
      var token = '<?php echo $this->session->data['token']; ?>';
      var store_id = 0;
      $('#banner_type_value_'+data_id + '.forAutoSelect').autocomplete({
        'source': function (request, response) {
            $.ajax({
              url: 'index.php?route=catalog/menu/autocompleteCategory&store_id=' + store_id + '&filter_name=' + encodeURIComponent(request) + '&token=' + token,
              dataType: 'json',
              success: function (json) {
                response($.map(json, function (item) {
                  return {
                    label: item['name'],
                    value: item['category_id']
                  }
                }));
              }
            });
        },
        'select': function (item) {
            
          var full_label = item['label'];
          var arrow_position = full_label.lastIndexOf(">");
          var child_cat_lable = full_label.substring(arrow_position+1).trim();
          
          $(this).val(child_cat_lable);
          hidden_field_id = $(this).attr('data-hidden-id');
          $('#' + hidden_field_id).val(item['value']);
        }
      });  
    }else if(select_item == "search"){
      $('ul.dropdown-menu').remove();
      $('#banner_type_value_'+data_id).removeClass('forAutoSelect');
      $('#banner_type_value_'+data_id).attr('placeholder',' Search');
    }else if(select_item == "sale"){
      $('ul.dropdown-menu').remove();
      $('#banner_type_value_'+data_id).removeClass('forAutoSelect');
      $('#banner_type_value_'+data_id).val('Sale');
      document.getElementById('banner_type_value_hidden'+data_id).value = "1";
      $('#banner_type_value_'+data_id).attr('placeholder',' Sale');
    }else if(select_item == "others") {
      $('ul.dropdown-menu').remove();
      $('#banner_type_value_'+data_id).removeClass('forAutoSelect');
      $('#banner_type_value_'+data_id).attr('placeholder',' Others');
    }else{
      $('ul.dropdown-menu').remove();
      $('#banner_type_value_'+data_id).removeClass('forAutoSelect');
      $('#banner_type_value_'+data_id).attr('placeholder',' http://www.google.com');
    }
  });

</script>

</div>
<?php echo $footer; ?>