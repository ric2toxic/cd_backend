<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
 <!--  <form action="index.php?route=catalog/menu/getForm&token=<?php echo $this->session->data['token']; ?>&menu_id=<?php echo $this->request->get['menu_id']; ?>" method="Post" id="form-menu_1" class="form-horizontal">
    <div class="row">
      <div class="pull-right col-sm-3">
        <div class="control-label">
          <div class="col-sm-4"><h4>Status</h4></div>
          <div class="col-sm-8">
            <select class ="form-control menu_store_status col-sm-6">
              <option>Select Menu status</option>
              <option value="1">Enable</option>
              <option value="0">Disable</option>              
            </select>
          </div>
        </div>
      </div>  
      <div class="pull-right col-sm-3">
        <div class="control-label">
          <div class="col-sm-4"><h4>Language</h4></div>
          <div class="col-sm-8">
            <select class ="form-control menu_store_language col-sm-6" name="menu_store_language" html-menu-id="<?php echo $this->request->get['menu_id']; ?>">
            <option value="0">Select Menu Language</option>
              <?php foreach($get_language as $language){ ?>
                <?php if($menu_store_language == $language['language_id']){ ?>
                    <option value="<?php echo $language['language_id']; ?>" selected="selected" ><?php echo $language['name']; ?></option>
                <?php }else{ ?>
                    <option value="<?php echo $language['language_id']; ?>" ><?php echo $language['name']; ?></option>
                <?php } ?>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
    </div>
  </form> -->

  <form action="index.php?route=catalog/menu/edit&token=<?php echo $this->session->data['token']; ?>&menu_id=<?php echo $this->request->get['menu_id']; ?>" method="post" id="form-menu" class="form-horizontal" enctype="multipart/form-data">
    <!-- <div class="row">
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
    </div> -->
    <input type="hidden" class="form-control seller_store" value="<?php echo $stores; ?>"/>
    <input type="hidden" name="menu_store_language" class="form-control menu_hid_store_language" value="<?php echo $menu_store_language; ?>"/>
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

      <?php if ($success) { ?>
       <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
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
          <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
        </div>
        <div class="panel-body">
          <table id="menus" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
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
             <input type="hidden" id="total_sub_menu">   
            <?php $i = 1;
                  $sub_menu_id = 0; 
                if(isset($menu_info) && !empty($menu_info) ){
                foreach ($menu_info as $info) { ?>
                  <tr id="menu_row_<?php echo $i; ?>" class="table_row_<?php echo $info['parent_id'];?>">
                    <td class="text-left">
                     <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][menu_db_id]" value="<?php echo $info['id']; ?>"/>
                      <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][store_id]" value="<?php echo $select_seller_store_id; ?>"/>
                      <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>
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
                    
                    <td class="text-left">

                     <?php if($info['parent_id'] == 0){ ?>
                      <a href="<?php echo $info['add_child']; ?>" class="btn btn-primary addmenu">
                           <i class="fa fa-pencil"></i> Edit
                      </a>      

                      <?php } ?>
                        &nbsp;   &nbsp;
                       <button type="button"  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger parent_menu" row-type ="<?php echo $info['type'];?>" parent_menu_item_id="<?php echo $info['id'];?>"><i class="fa fa-minus-circle"></i></button>

                       <table class="table table-striped table-bordered table-hover">
                              <tbody class="child_<?php echo $i; ?>"></tbody>
                            </table>

                    </td>
                  </tr>

            <?php $i++; $sub_menu_id++; } } ?>
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
<input type="hidden" id="sub_menu_id" value="<?php echo $sub_menu_id; ?>" />
<style>
.parent_menu_row {
   border: solid 2px; 
}
.sub-child-border{
     border: solid 2px brown;
}
</style>

<script type="text/javascript">
  var menu_row = <?php echo $i; ?>;
  var childcount = menu_row;
  $(document).delegate( ".addmenu", "click", function() {
    var sub_menu_id = $("#sub_menu_id").val();
    
      sub_menu_id = parseInt(sub_menu_id) + 1;
      $("#sub_menu_id").val(sub_menu_id);
    
    if($(this).hasClass("subchild")){
       var sub_child = $(this).attr('data-subchlid-id');
       var sub_child_add = parseInt(sub_child) + 1; 
       var val = $(this).attr('total-sub-child');
       var total_sub_child = parseInt(val);

    }else{
        var id = $(this).attr('data-id');
        var val = $(this).attr('total-child');
        if($(this).hasClass("child")){
            var sub_menu = $("#total_sub_menu").val();            
            total_sub_child = parseInt(total_sub_child)+1;
            var sub_menu = 1;
        }
        var count_sub_menu = parseInt($(this).attr('count_sub_menu'));
        var total_child = parseInt(val);
    }
    
    if(id == 0){
      child = '['+menu_row+']';
      childcount = menu_row;
      parent_menu_row_css = 'parent_menu_row';
    }else{
      if(count_sub_menu > 0){
        total_child += count_sub_menu;
        child = '[' + id + '][child][' + total_child + ']';
        parent_menu_row_css = '';
      }else{
        child = '[' + id + '][child][' + total_child + ']';
        parent_menu_row_css = '';
      }
    }
    if(sub_child == 1 && sub_child > 1){
      sub_child_indx = '['+sub_child+']';
    }
        html  = '<tr id="menu_row_' + menu_row + '">';
        html += '  <td class="text-left">';
        html += '   <input type="hidden" class="form-control"  name="info'+ child +'[menu_db_id]" value="0"/>'
        html += '   <input type="hidden" class="form-control"  name="info'+ child+'[store_id]" value="<?php echo $select_seller_store_id; ?>"/>';
        html += '   <input type="hidden" class="form-control"  name="info'+ child +'[menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>';
        html += '<input type="text" class="form-control menu_title"  name="info'+ child +'[link_title]" value="" placeholder=" Enter Your Menu Title"/>';
        html += '</td> <td class="text-left"><select class ="form-control select_item" html-data="'+ menu_row +'" id="select_item_'+menu_row+'" name="info'+ child +'[link_type]"><option  value="category">Category</option><option  value="page">Page</option><option  value="others">Custom Link</option></select></td>';
         html += '  <td class="text-left"><input type="text" class="form-control category_value" id="category_value_' + menu_row + '" html-data="'+menu_row+'"  name="name_info'+ child +'[category]" value="" placeholder=" Select Category" data-hidden-id="category_value_hidden'+menu_row+'" /><input type="hidden" id="category_value_hidden'+menu_row+'" class="form-control value"  name="info'+ child +'[category]" value="" /></td>';
        //html += '<td class="text-left"><select class ="form-control" name="info'+ child +'[type]">';
        if(id ==0){
            //html +='<option  value="parent">Parent</option>';
            html +='<input type="hidden" name="info'+ child +'[type]" value="parent">';
        }
        if(id !=0){
            //html += '<option  value="child">Child</option></select></td>';
            html += '<input type="hidden" name="info'+ child +'[type]" value="child">';
            html += '<input type="hidden" name="info'+ child +'[megamenu_type]" value="">';
        }
        html += '  <td class="text-left"><input type="text" class="form-control"  name="info'+ child +'[position]" value="" placeholder=" Enter Your Menu position" size="5"/> </td>';
        html += '<td class="text-left"><select name="info'+ child +'[status]" id="input-status" class="form-control"><option value="1" selected="selected"><?php echo $text_enabled; ?></option><option value="0"><?php echo $text_disabled; ?></option></select></td>';

        html += '<td class="text-left"></td>';
 
        html += '  <td class="text-left"><button type="button" row_number= '+menu_row+'  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger button_remove"><i class="fa fa-minus-circle"></i></button></td>';

        html += '</tr>';
  

    if(id == 0){
      $('#menus tbody.main_tbody').append(html);
    }else{
      if(sub_child){
        $('#sub_child_row_'+sub_child).after(html);
      }
      $('#menus tbody.child_'+id).append(html);
    }

    menu_row++;
    total_child = total_child + 1;
    $(this).attr('total-child', total_child);
  });


  function addSubmenu(id){
    //var sub_menu_id = $("#sub_menu_id").val();
    //var upddate_submenu_id = parseInt(sub_menu_id)+1;
    //$("#sub_menu_id").val(upddate_submenu_id);
    var row_submenu = $('.subchild_'+id).attr('row_submenu');
    var child = $(".subchild_"+id).attr('child_number');
    var last_child = $(".subchild_"+id).attr('last_child');
    var val = $(".subchild_"+id).attr('total-sub-child');
    var sub_child_add =  parseInt(val)+1;
    $(".subchild_"+id).attr('total-sub-child',sub_child_add);
    var total_sub_child = parseInt(val);


    var parent_data = $(".subchild_"+id).attr('parent_data');
    var parent_data_arr = parent_data.split(',');
    var upddate_submenu_id = parent_data_arr[0]+''+parent_data_arr[1]+''+parent_data_arr[2];
    var last_element = parent_data_arr[parent_data_arr.length - 1];
    last_element = parseInt(last_element)+1;

    var sub_child_indx  = '';
    var new_parent_data = '';
    //var sub_child_indx = '['+row_submenu+'][child]['+child+'][child]['+total_sub_child+']';
    for(i=0; i<parent_data_arr.length;i++)
    {
      if(i == (parent_data_arr.length)-1)
      {
        sub_child_indx = sub_child_indx + '['+parent_data_arr[i]+']';
      }
      else
      {
       sub_child_indx = sub_child_indx + '['+parent_data_arr[i]+'][child]'; 
      }

     if(i < (parent_data_arr.length)-1)
      { 
        new_parent_data = new_parent_data+parent_data_arr[i]+',';
      } 
      
    }


    $(".subchild_"+id).attr('parent_data',new_parent_data+last_element);

        html  = '<tr id="sub_menu_row_' + upddate_submenu_id + '" row-number="'+upddate_submenu_id+'"">';
        html += '  <td class="text-left">';
        html += '   <input type="hidden" class="form-control"  name="info'+ sub_child_indx +'[menu_db_id]" value="0"/>'
        html += '   <input type="hidden" class="form-control"  name="info'+ sub_child_indx+'[store_id]" value="<?php echo $select_seller_store_id; ?>"/>';
        html += '   <input type="hidden" class="form-control"  name="info'+ sub_child_indx +'[menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/></td>';
        html += '<td class="text-left"><input type="text" class="form-control menu_title"  name="info'+ sub_child_indx +'[link_title]" value="" placeholder=" Enter Your Menu Title"/>';

        html += '</td> <td class="text-left"><select class ="form-control select_item" html-data="'+ upddate_submenu_id +'" id="select_item_'+upddate_submenu_id+'" name="info'+ sub_child_indx +'[link_type]"><option  value="category">Category</option><option  value="page">Page</option><option  value="others">Custom Link</option></select></td>';

        html += '  <td class="text-left"><input type="text" class="form-control category_value" id="category_value_' + upddate_submenu_id + '" html-data="'+upddate_submenu_id+'"  name="name_info'+ sub_child_indx +'[category]" value="" placeholder=" Select Category" data-hidden-id="category_value_hidden'+upddate_submenu_id+'" /><input type="hidden" id="category_value_hidden'+upddate_submenu_id+'" class="form-control value"  name="info'+ sub_child_indx +'[category]" value="" />';

        html += '<input type="hidden" name="info'+ sub_child_indx +'[type]" value="child">';
        html += '<input type="hidden" name="info'+ sub_child_indx +'[megamenu_type]" value="">';

        html += '</td>';
 
        html += '  <td class="text-left"><input type="text" class="form-control"  name="info'+ sub_child_indx +'[position]" value="" placeholder=" Enter Your Menu position" size="5"/> </td>';
        html += '<td class="text-left"><select name="info'+ sub_child_indx +'[status]" id="input-status" class="form-control"><option value="1" selected="selected"><?php echo $text_enabled; ?></option><option value="0"><?php echo $text_disabled; ?></option></select></td>';
        html += '  <td class="text-right"><button type="button" row_number= '+upddate_submenu_id+'  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger button_remove"><i class="fa fa-minus-circle"></i></button></td>';
        html += '</tr>';


      if(parent_data_arr.length < 4)
      {
        html += '<tr id="sub_child_row_'+ upddate_submenu_id + '" class="row_'+ upddate_submenu_id +' row_'+upddate_submenu_id+' " ><td colspan="7">';

        html += '<button type="button" style="float: right;" onclick="addSubmenu('+upddate_submenu_id+')" data-toggle="tooltip" row_submenu= '+row_submenu+' child_number="'+child+'"  data-subchlid-id="'+ upddate_submenu_id +'" total-sub-child="0" title="Sub Child Add" class="btn btn-primary menu_sub_menu subchild_'+upddate_submenu_id+'" parent_data="'+parent_data+',0"><i class="fa fa-plus-circle"></i>Add Sub Child</button>  ';

        html += '<button type="button" style="float: right;margin-right: 10px;" onclick="addmegamenu('+upddate_submenu_id+')" data-toggle="tooltip" data-subchlid-id="'+ upddate_submenu_id +'" total-sub-child="0"  class="btn btn-primary megamenu_('+upddate_submenu_id+')" parent_data="'+parent_data+',0"><i class="fa fa-plus-circle"></i>Add Sub Megamenu</button> <br />';

        html += '</tr>';
      }  

       $('#sub_child_row_'+id).after(html);
     
  }

function addmegamenu(id){
    var sub_menu_id = $("#sub_menu_id").val();
    var upddate_submenu_id = parseInt(sub_menu_id)+1;
    $("#sub_menu_id").val(upddate_submenu_id);
    var row_submenu = $('.subchild_'+id).attr('row_submenu');
    var child = $(".subchild_"+id).attr('child_number');

    var val = $(".subchild_"+id).attr('total-sub-child');
    var sub_child_add =  parseInt(val)+1;

    var parent_data = $(".subchild_"+id).attr('parent_data');
    var parent_data_arr = parent_data.split(',');
    var last_element = parent_data_arr[parent_data_arr.length - 1];
    last_element = parseInt(last_element)+1;

    var sub_child_indx  = '';
    var new_parent_data = '';
    //var sub_child_indx = '['+row_submenu+'][child]['+child+'][child]['+total_sub_child+']';
    for(i=0; i<parent_data_arr.length;i++)
    {
      if(i == (parent_data_arr.length)-1)
      {
        sub_child_indx = sub_child_indx + '['+parent_data_arr[i]+']';
      }
      else
      {
       sub_child_indx = sub_child_indx + '['+parent_data_arr[i]+'][child]'; 
      }

     if(i < (parent_data_arr.length)-1)
      { 
        new_parent_data = new_parent_data+parent_data_arr[i]+',';
      } 
      
    }

    $(".subchild_"+id).attr('parent_data',new_parent_data+last_element);


    html  = '<tr id="sub_menu_row_'+ upddate_submenu_id + '" colspan="7">';
    html += ' <td class="text-left">';
    html += ' <td class="text-left col-sm-9" colspan="4">';
    html += '<input type="hidden" name="info'+ sub_child_indx +'[type]" value="megamenu">';
    html += '<input type="hidden" name="info'+ sub_child_indx +'[megamenu_type]" value="1">';
    html += '   <input type="hidden" class="form-control"  name="info'+ sub_child_indx +'[menu_db_id]" value="0"/>'
    html += '   <input type="hidden" class="form-control"  name="info'+ sub_child_indx+'[store_id]" value="<?php echo $select_seller_store_id; ?>"/>';
    html += '   <input type="hidden" class="form-control"  name="info'+ sub_child_indx +'[menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>';  
    html += '   <textarea class="form-control megamenu" name="info'+ sub_child_indx +'[megamenu]" value=""/> </textarea> <br />';
    html += ' </td>';
    html += ' <td class="text-left"><select name="info'+ sub_child_indx +'[status]" id="input-status" class="form-control"><option value="1" selected="selected"><?php echo $text_enabled; ?></option><option value="0"><?php echo $text_disabled; ?></option></select></td>';
    html += ' <td class="text-right"><button type="button" onclick="deleteRow('+upddate_submenu_id+')" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger button_remove"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';
    html += '<tr id="child_row_' + menu_row + '"><td colspan="7">';
    html += '</td></tr>';
   $('#sub_child_row_'+id).after(html);

 $('.megamenu').summernote({
    height: 300
  });
    
}


 function deleteRow(id){
   $("#sub_menu_row_"+id).remove();
   $("#sub_child_row_"+id).remove();
 }


  $('.megamenu').summernote({
    height: 300
  });

  $(document).delegate( ".addmegamenu", "click", function() {
    var id = $(this).attr('data-id');
    var val = $(this).attr('total-child');
    var count_sub_menu = parseInt($(this).attr('count_sub_menu'));
    var total_child = parseInt(val);
    if(id == 0){
      child = '['+menu_row+']';
      childcount = menu_row;
      parent_menu_row_css = 'parent_menu_row';
    }else{
      if(count_sub_menu > 0){
        total_child += count_sub_menu;
        child = '[' + id + '][child][' + total_child + ']';
        parent_menu_row_css = '';
      }else{
        child = '[' + id + '][child][' + total_child + ']';
        parent_menu_row_css = '';
      }
    }
    html  = '<tr id="menu_row_' + menu_row + '" colspan="7" class="'+parent_menu_row_css+'">';
    html += ' <td class="text-left">';
    html += '   <input type="hidden" class="form-control"  name="info'+ child +'[menu_db_id]" value="0"/>';
    html += '   <input type="hidden" name="info'+ child +'[type]" value="child">';
    html += '   <input type="hidden" class="form-control"  name="info'+ child+'[store_id]" value="<?php echo $select_seller_store_id; ?>"/>';
    html += '   <input type="hidden" name="info'+ child +'[megamenu_type]" value="1">';
    html += '   <input type="hidden" class="form-control"  name="info'+ child +'[menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/></td>';
    html += ' <td class="text-left col-sm-9" colspan="4">';
    html += '   <textarea class="form-control megamenu" name="info'+ child +'[megamenu]" value=""/> </textarea> <br />';
    html += ' </td>';
    html += ' <td class="text-left"><select name="info'+ child +'[status]" id="input-status" class="form-control"><option value="1" selected="selected"><?php echo $text_enabled; ?></option><option value="0"><?php echo $text_disabled; ?></option></select></td>';
    html += ' <td class="text-right "><button type="button" row_number= '+menu_row+'  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger button_remove"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';
    html += '<tr id="child_row_' + menu_row + '"><td colspan="7">';
    html += '</td></tr>';
    $('#menus tbody.child_'+id).append(html);
    menu_row++;
    total_child = total_child + 1;
    $(this).attr('total-child', total_child);
  });

</script>
<script type="text/javascript">
  // $(".seller_store").change(function () {
  //   var store_id  = $(this).val();

  //   $.ajax({
  //     url: 'index.php?route=seller/website-settings/change_seller_store ',
  //     type: 'POST',
  //     dataType: 'html',
  //     data: {store_id: store_id},
  //     success: function () {
  //       window.location.reload();
  //     }
  //   });
  // });
  $(".menu_store_status").change(function () {
    var menu_status  = $(this).val();
    var menu_lang = $('.menu_hid_store_language').val();
    $.ajax({
      url: 'index.php?route=catalog/menu/menu_store_status&token=<?php echo $this->session->data['token']; ?> ',
      type: 'POST',
      dataType: 'html',
      data: {menu_status: menu_status, menu_lang: menu_lang},
      success: function () {
        window.location.reload();
      }
    });
  });
  $(".menu_store_language").change(function () {
    var menu_language = $(this).val();
    if(menu_language != 0){
      $('.menu_hid_store_language').attr('value', menu_language);
      $('#form-menu_1').submit();  
    }
  });

  $(document).delegate( ".select_item", "change", function() {
      var select_item = $(this).val();
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
    var token = '<?php echo $this->session->data['token']; ?>';
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
              url: 'index.php?route=catalog/menu/' + call_function + '&store_id=' + store_id + '&filter_name=' + encodeURIComponent(request) + '&token=' + token,
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
    var token = '<?php echo $this->session->data['token']; ?>';
    $('.parent_menu').click(function() {

    if(confirm('Do you really want delete this menu item?'))
    {  
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
              url: 'index.php?route=catalog/menu/deleteMenuAll&token=' + token,
              data: {parent_menu_item_id,store_id},
              success: function () {
                window.location.reload();
              }
            });
          }
        }else{
          $.ajax({
            type: 'POST',
            url: 'index.php?route=catalog/menu/deleteMenuAll&token=' + token,
            data: {parent_menu_item_id,store_id},
            success: function () {
              window.location.reload();
            }
          });
        }
      }else{
        $.ajax({
          type: 'POST',
          url: 'index.php?route=catalog/menu/deleteMenuAll&token=' + token,
          data: {parent_menu_item_id,store_id},
          success: function (html) {
            window.location.reload();
          }
        });
      }
     }
    });
  });
</script>
<!-- Remove blanks menus-->
<script type="text/javascript">
  $(document).delegate( ".button_remove", "click", function() {
      var row_number = $(this).attr('row_number');
      $("#sub_menu_row_"+row_number).remove();
      $('tr#menu_row_'+row_number).remove();
      $('tr#child_row_'+row_number).remove();
      $('.row_'+row_number).remove();
      //$('#sub_child_row_'+row_number).remove();
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
<?php echo $footer; ?>