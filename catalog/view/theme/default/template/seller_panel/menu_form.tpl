<?php echo $header_seller; ?>
<div class="container">
<div id="content">
  <form action="index.php?route=seller/menu/edit&menu_id=<?php echo $this->request->get['menu_id']; ?>" method="post" id="form-menu" class="form-horizontal">
    <div class="row">
      <div class="pull-right col-sm-4">
        <?php if(!empty($storeid) && $count > 1){ ?>
        <div class="control-label">
          <div class="col-sm-5"><h4>Select Store</h4></div>
          <div class="col-sm-7">
            <select class ="form-control seller_store col-sm-6">
              <?php foreach ($stores as $value) { ?>
              <option  value="<?php echo $value['store_id']; ?>"
              <?php if($value['store_id'] == $select_seller_store_id){ ?> selected
              <?php } ?>
              >
              <a><label class="control-label"><?php echo $value['name']; ?>
                </label></a>
              </option>
              <?php } ?>
            </select>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
    <div class="page-header">
      <div class="container-fluid">
        <div class="pull-right">
          <button type="button" form="form-menu" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary save_all_menu"><i class="fa fa-save"></i></button>
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
      <?php if ($error_warning) { ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
      <?php } ?>
      <div class="row">
        <div class="col-sm-12">
          <p class="menu_box_error error"></p>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
        </div>
        <div class="panel-body">
          <table id="menus" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
              <td></td>
              <td class="text-left">Menu Title</td>
              <td class="text-left">Item</td>
              <td class="text-left">Value</td>
              <!--<td class="text-left">Type</td>-->
              <td class="text-left">Sort order</td>
              <td class="text-left">Status</td>
              <td></td>
            </tr>
            </thead>
            <tbody class="main_tbody">
            <?php $i = 1;
                if(isset($menu_info)){
                foreach ($menu_info as $info) { ?>
            <tr id="menu_row_<?php echo $i; ?>" class="table_row_<?php echo $info['parent_id'];?> parent_menu_row">
              <td>
                <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][menu_db_id]" value="<?php echo $info['id']; ?>"/>
                <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][store_id]" value="<?php echo $select_seller_store_id; ?>"/>
                <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>
              </td>
              <td class="text-left">
                <input type="text" class="form-control"  name="info[<?php echo $i; ?>][link_title]" value="<?php echo $info['link_title']; ?>"/>
              </td>
              <td class="text-left">
                <select class ="form-control select_item" html-data="<?php echo $i; ?>" id="select_item_<?php echo $i; ?>" name="info[<?php echo $i; ?>][link_type]">
                  <option value="category" <?php if($info['link_type'] == 'category'){ ?> selected <?php } ?> >Category</option>
                  <option value="page" <?php if($info['link_type'] == 'page'){ ?> selected <?php } ?>>Page</option>
                  <option value="others" <?php if($info['link_type'] == 'others'){ ?> selected <?php } ?>>Custom Link</option>
                </select>
              </td>
              <td class="text-left">
                <input type="text" id="category_value_<?php echo $i; ?>" data-hidden-id="category_value_hidden<?php echo $i; ?>" class="form-control category_value" html-data="<?php echo $i; ?>" name="name_info[<?php echo $i; ?>][category]" value="<?php echo $info['value_label']; ?>" />
                <input type="hidden" id="category_value_hidden<?php echo $i; ?>" html-data="<?php echo $i; ?>" class="form-control value"  name="info[<?php echo $i; ?>][category]" value="<?php echo $info['value']; ?>" />
              </td>
              <!--<td class="text-left">
                <select class ="form-control" name="info[<?php echo $i; ?>][type]">
                  <option value="parent" <?php if($info['type'] == 'parent'){ ?> selected <?php } ?> >Parent</option>
                  <option value="child" <?php if($info['type'] == 'child'){ ?> selected <?php } ?> >Child</option>
                </select>
              </td>-->

              <?php if($info['type'] == 'parent'){ ?>
                <input type="hidden" class ="form-control" name="info[<?php echo $i; ?>][type]" value="parent" />
              <?php } ?>
              <?php if($info['type'] == 'child'){ ?>
                <input type="hidden" class ="form-control" name="info[<?php echo $i; ?>][type]" value="child" />
              <?php } ?>
              <td class="text-left">
                <input type="text" class="form-control"  name="info[<?php echo $i; ?>][position]" value="<?php echo $info['position']; ?>" size="5"/>
              </td>
              <td class="text-left">
                <select name="info[<?php echo $i; ?>][status]" id="input-status" class="form-control">
                  <?php if ($info['status'] == 1) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </td>
              <td class="text-left"><button type="button"  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger parent_menu" row-type ="<?php echo $info['type'];?>" parent_menu_item_id="<?php echo $info['id'];?>"><i class="fa fa-minus-circle"></i></button></td>
            </tr>
            <tr>
              <td colspan="7">
            <?php if($info['parent_id'] == 0){ ?>
                  <button type="button" data-toggle="tooltip" data-id="<?php echo $i; ?>" count_sub_menu="<?php echo (isset($info['child']) ? count($info['child']) : 0);?>" total-child="0" data-db-id="<?php echo $info['id']; ?>" title="<?php echo $button_menu_add; ?>" class="btn btn-primary addmenu"><i class="fa fa-plus-circle"></i>Add Child</button>
                  <table class="table table-striped table-bordered table-hover">
                    <tbody class="child_<?php echo $i; ?>"></tbody>
                  </table>
            <?php } ?>
              </td>
            </tr>

              <?php if(isset($info['child']) && count($info['child']) > 0){ ?>
                <?php
                  $j = $i.'01';
                  foreach($info['child'] as $child_menu) { ?>
                  <tr id="child_menu_row_<?php echo $i; ?>" class="table_row_<?php echo $child_menu['parent_id'];?>">
                    <td>
                      <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][menu_db_id]" value="<?php echo $child_menu['id']; ?>"/>
                      <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][store_id]" value="<?php echo $select_seller_store_id; ?>"/>
                      <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>
                    </td>
                    <td class="text-left">
                      <input type="text" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][link_title]" value="<?php echo $child_menu['link_title']; ?>"/>
                    </td>
                    <td class="text-left">
                      <select class ="form-control select_item" html-data="<?php echo $j; ?>" id="select_item_<?php echo $j; ?>" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][link_type]">
                        <option value="category" <?php if($child_menu['link_type'] == 'category'){ ?> selected <?php } ?> >Category</option>
                        <option value="page" <?php if($child_menu['link_type'] == 'page'){ ?> selected <?php } ?>>Page</option>
                        <option value="others" <?php if($child_menu['link_type'] == 'others'){ ?> selected <?php } ?>>Custom Link</option>
                      </select>
                    </td>
                    <td class="text-left">
                      <input type="text" id="category_value_<?php echo $j; ?>" data-hidden-id="category_value_hidden<?php echo $j; ?>" class="form-control category_value" html-data="<?php echo $j; ?>"  name="name_info[<?php echo $i; ?>][child][<?php echo $j; ?>][category]" value="<?php echo $child_menu['value_label']; ?>" />
                      <input type="hidden" id="category_value_hidden<?php echo $j; ?>" html-data="<?php echo $j; ?>" class="form-control value"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][category]" value="<?php echo $child_menu['value']; ?>" />
                    </td>
                    <!--<td class="text-left">
                      <select class ="form-control" name="info[<?php echo $i; ?>][type]">
                        <option value="parent" <?php if($info['type'] == 'parent'){ ?> selected <?php } ?> >Parent</option>
                        <option value="child" <?php if($info['type'] == 'child'){ ?> selected <?php } ?> >Child</option>
                      </select>
                    </td>-->

                    <?php if($child_menu['type'] == 'parent'){ ?>
                    <input type="hidden" class ="form-control" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][type]" value="parent" />
                    <?php } ?>
                    <?php if($child_menu['type'] == 'child'){ ?>
                    <input type="hidden" class ="form-control" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][type]" value="child" />
                    <?php } ?>
                    <td class="text-left">
                      <input type="text" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][position]" value="<?php echo $child_menu['position']; ?>" size="5"/>
                    </td>
                    <td class="text-left">
                      <select name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][status]" id="input-status" class="form-control">
                        <?php if ($child_menu['status'] == 1) { ?>
                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                        <option value="0"><?php echo $text_disabled; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $text_enabled; ?></option>
                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                        <?php } ?>
                      </select>
                    </td>
                    <td class="text-left"><button type="button"  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger parent_menu" row-type ="<?php echo $child_menu['type'];?>" parent_menu_item_id="<?php echo $child_menu['id'];?>"><i class="fa fa-minus-circle"></i></button></td>
                  </tr>
                  <?php $j++; ?>
                <?php } ?>
              <?php  }  ?>
            <?php $i++; } } ?>
            </tbody>
            <tfoot>
            <tr>
              <td colspan="7" class="text-right"><button type="button" data-toggle="tooltip" title="<?php echo $button_menu_add; ?>" total-child="0" data-id="0" class="btn btn-primary addmenu"><i class="fa fa-plus-circle"></i></button></td>
            </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </form>
</div>
</div>
<script type="text/javascript"><!--
  var menu_row = <?php echo $i; ?>;
  var childcount = menu_row;
  $(document).delegate( ".addmenu", "click", function() {
    var id = $(this).attr('data-id');
    var val = $(this).attr('total-child');
    var count_sub_menu = parseInt($(this).attr('count_sub_menu'));
    var total_child = parseInt(val);
    if(id == 0){
      child = '['+menu_row+']';
      childcount = menu_row;
      parent_menu_row_css = 'parent_menu_row';
    }
    else{
      if(count_sub_menu > 0){
        total_child += count_sub_menu;
        child = '[' + id + '][child][' + total_child + ']';
        parent_menu_row_css = '';
      }else{
        child = '[' + id + '][child][' + total_child + ']';
        parent_menu_row_css = '';
      }
    }
    html  = '<tr id="menu_row_' + menu_row + '" class="'+parent_menu_row_css+'">';
    html += '  <td class="text-left">';
    html += '<input type="hidden" class="form-control"  name="info'+ child +'[menu_db_id]" value="0"/>'
    html += '<input type="hidden" class="form-control"  name="info'+ child+'[store_id]" value="<?php echo $select_seller_store_id; ?>"/>';
    html += '<input type="hidden" class="form-control"  name="info'+ child +'[menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/></td>';
    html += '<td class="text-left"><input type="text" class="form-control menu_title"  name="info'+ child +'[link_title]" value="" placeholder=" Enter Your Menu Title"/>';
    html += '</td> <td class="text-left"><select class ="form-control select_item" html-data="'+ menu_row +'" id="select_item_'+menu_row+'" name="info'+ child +'[link_type]"><option  value="category">Category</option><option  value="page">Page</option><option  value="others">Custom Link</option></select></td>';
    html += '  <td class="text-left"><input type="text" class="form-control category_value" id="category_value_' + menu_row + '" html-data="'+menu_row+'"  name="name_info'+ child +'[category]" value="" placeholder=" Select Category" data-hidden-id="category_value_hidden'+menu_row+'" /><input type="hidden" id="category_value_hidden'+menu_row+'" class="form-control value"  name="info'+ child +'[category]" value="" /></td>';
    //html += '<td class="text-left"><select class ="form-control" name="info'+ child +'[type]">';
    if(id ==0){
      //html +='<option  value="parent">Parent</option>';
      html +='<input type="hidden" name="info'+ child +'[type]" value="parent">';
    }
    if(id !=0){
      //html += '<option  value="child">Child</option></select></td>';
      html +='<input type="hidden" name="info'+ child +'[type]" value="child">';
    }
    html += '  <td class="text-left"><input type="text" class="form-control"  name="info'+ child +'[position]" value="" placeholder=" Enter Your Menu position" size="5"/> </td>';
    html += '<td class="text-left"><select name="info'+ child +'[status]" id="input-status" class="form-control"><option value="1" selected="selected"><?php echo $text_enabled; ?></option><option value="0"><?php echo $text_disabled; ?></option></select></td>';
    html += '  <td class="text-right"><button type="button" row_number= '+menu_row+'  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger button_remove"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';
    html += '<tr id="child_row_' + menu_row + '"><td colspan="7">';
    if(id == 0){
      html += '<button type="button" data-toggle="tooltip" data-id="'+ menu_row +'" total-child="0" data-db-id="'+ id +'" title="<?php echo $button_menu_add; ?>" class="btn btn-primary addmenu"><i class="fa fa-plus-circle"></i>Add Child</button> <br />';
      html += '<table class="table table-striped table-bordered table-hover"><tbody class="child_'+ menu_row+'"></tbody></table></td>';
    }
    html += '</tr>';
    if(id == 0){
      $('#menus tbody.main_tbody').append(html);
    }else{
      $('#menus tbody.child_'+id).append(html);
    }
    menu_row++;
    total_child = total_child + 1;
    $(this).attr('total-child', total_child);
  });
</script>
<script type="text/javascript">
  $(".seller_store").change(function () {
    var store_id  = $(this).val();

    $.ajax({
      url: 'index.php?route=seller/website-settings/change_seller_store',
      type: 'POST',
      dataType: 'html',
      data: {store_id: store_id},
      success: function () {
        window.location.reload();
      }
    });
  });

  $(document).delegate( ".select_item", "change", function() {
      var select_item = $("select#select_item_"+$(this).attr("html-data")+" option:selected").val();
      var data_id = $(this).attr('html-data');
      if(select_item == "category"){
        $('#category_value_'+data_id).attr('placeholder',' Select Category');
        $('#category_value_'+data_id).attr('value','');
        $('#category_value_hidden'+data_id).attr('value','');
      }else if(select_item == "page"){
        $('#category_value_'+data_id).attr('placeholder',' Select Page');
        $('#category_value_'+data_id).attr('value','');
        $('#category_value_hidden'+data_id).attr('value','');
      }else{
        $('#category_value_'+data_id).attr('placeholder',' http://www.google.com');
        $('#category_value_'+data_id).attr('value','');
        $('#category_value_hidden'+data_id).attr('value',0);
      }
  });

  $(document).delegate( ".category_value", "focusin", function() {
    var call_function = "";
    var select_item = $("select#select_item_"+$(this).attr("html-data")+" option:selected").val();
    //alert(select_item)
    var flagt = 1;
    if(select_item == "category"){
      $(this).attr('placeholder',' Select Category');
      call_function = "autocompleteCategory";
      flagt = 0;
    }else if(select_item == "page"){
      $(this).attr('placeholder',' Select Page');
      call_function = "autoComplatePages";
      flagt = 0;
    }else{
      $("#category_value_hidden"+$(this).attr("html-data")).val(0);
      $(this).attr('placeholder',' http://www.google.com');
      var call_function = "";
      flagt = 0;
    }

    if(flagt == '0') {
      var store_id = ($('.seller_store').val()) ? $('.seller_store').val() : "<?php echo $select_seller_store_id ;?>";
      $(this).autocomplete({
        'source': function (request, response) {
            $.ajax({
              url: 'index.php?route=seller/coupon/' + call_function + '&store_id=' + store_id + '&filter_name=' + encodeURIComponent(request),
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
          $(this).val(item['label']);
          hidden_field_id = $(this).attr('data-hidden-id');
          //alert(hidden_field_id);
          //alert(item['value']);
          $('#' + hidden_field_id).val(item['value']);
        }
      });
    }
  });
</script>


<!-- remove menus -->
<script type="text/javascript">
  $(document).ready(function(){
    $('.parent_menu').click(function() {
      var parent_menu_item_id = $(this).attr('parent_menu_item_id');
      var row_type = $(this).attr('row-type');
      var store_id = $('.seller_store').val();
      var child_row = $('.table_row_'+parent_menu_item_id).length;
      if(row_type == 'parent'){
        if(child_row > 0){
          ok = confirm('Are you sure do you want to that menu with sub menu');
          if(ok){
            $.ajax({
              type: 'POST',
              url: 'index.php?route=seller/menu/deleteMenuAll',
              data: {parent_menu_item_id,store_id},
              success: function () {
                window.location.reload();
              }
            });
          }
        }else{
          $.ajax({
            type: 'POST',
            url: 'index.php?route=seller/menu/deleteMenuAll',
            data: {parent_menu_item_id,store_id},
            success: function () {
              window.location.reload();
            }
          });
        }
      }else{
        $.ajax({
          type: 'POST',
          url: 'index.php?route=seller/menu/deleteMenuAll',
          data: {parent_menu_item_id,store_id},
          success: function (html) {
            window.location.reload();
          }
        });
      }

    });
  });
</script>
<!-- Remove blanks menus-->
<script type="text/javascript">
  $(document).delegate( ".button_remove", "click", function() {
      var row_number = $(this).attr('row_number');
      $('tr#menu_row_'+row_number).remove();
      $('tr#child_row_'+row_number).remove();
    });
</script>
<!-- validation for blank menus -->
<script type="text/javascript">
  $('.save_all_menu').click(function(){
    var submit_fail = 0;

    $('.category_value').each(function() {
      if($(this).val()==''){
        $('.menu_box_error').text('Please enter menu value').parent().css('text-align','center');
        submit_fail = 1;
        return false;
      }
    });
    $('.menu_title').each(function() {
      if($(this).val()==''){
        $('.menu_box_error').text('Please enter menu title').parent().css('text-align','center');
        submit_fail = 1;
        return false;
      }
    });
    if(submit_fail == '0') {
      $("form#form-menu").submit();
    }else{
      return false;
    }
  });
</script>
<?php echo $footer_seller; ?>