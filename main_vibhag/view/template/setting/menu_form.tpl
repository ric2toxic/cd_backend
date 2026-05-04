<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-user" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-user" class="form-horizontal">
          
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-title"><?php echo $entry_parent; ?></label>
            <div class="col-sm-10">
                <select name="parent" id="input-parent" class="form-control">
                    <option value="0">--Select Parent--</option>
                    <?php echo $tree_options?>
                </select>
              
            </div>
          </div>
            
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-title"><?php echo $entry_title; ?></label>
            <div class="col-sm-10">
              <input type="text" name="title" value="<?php echo $title; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title" class="form-control" />
              <?php if ($error_title) { ?>
              <div class="text-danger"><?php echo $error_title; ?></div>
              <?php } ?>
            </div>
          </div>
            
            
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-permission">
                <span data-toggle="tooltip" title="<?php echo "Permission for controller & action, Example - catalog / category"; ?>"><?php echo $entry_permission; ?></span>
            </label>
            <div class="col-sm-10">
              <input type="text" name="permission" value="<?php echo $permission; ?>" placeholder="<?php echo $entry_permission; ?>" id="input-permission" class="form-control" />
              <?php if ($error_permission_controller) { ?>
              <div class="text-danger"><?php echo $error_permission_controller; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-link">
            <span data-toggle="tooltip" title="<?php echo "Route url for controller & action, Example - catalog / category / index"; ?>"><?php echo $entry_link; ?></span>
            </label>
            <div class="col-sm-10">
              <input type="text" name="link" value="<?php echo $link; ?>" placeholder="<?php echo $entry_link; ?>" id="input-link" class="form-control" />
               <?php if ($error_link) { ?>
              <div class="text-danger"><?php echo $error_link; ?></div>
              <?php } ?>
            </div>
          </div>
            <div class="form-group">
            <label class="col-sm-2 control-label" for="input-icon"><?php echo $entry_icon; ?></label>
            <div class="col-sm-10">
              <input type="text" name="icon" value="<?php echo $icon; ?>" placeholder="<?php echo $entry_icon; ?>" id="input-icon" class="form-control" />
               <?php if ($error_icon) { ?>
              <div class="text-danger"><?php echo $error_icon; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-show-for-import"><span data-toggle="tooltip" title="<?php echo "Checks if this menu has sub menu items"; ?>"><?php echo $entry_sub_menu; ?></span></label>
            <div class="col-sm-10">
              <div class="checkbox-show-for-import">
                <label>
                  <?php if ($sub_menu) { ?>
                  <input type="checkbox" name="sub_menu" value="1" checked="checked" id="input-show-for-import" />
                  <?php } else { ?>
                  <input type="checkbox" name="sub_menu" value="1" id="input-show-for-import" />
                  <?php } ?>
                  &nbsp; </label>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="status" id="input-status" class="form-control">
                <?php if ($status) { ?>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <?php } else { ?>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <option value="1"><?php echo $text_enabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-icon"><?php echo $entry_menu_order; ?></label>
            <div class="col-sm-10">
              <input type="number" name="menu_order" value="<?php echo $menu_order; ?>" placeholder="<?php echo $entry_menu_order; ?>" id="input-menu_order" class="form-control" />
            </div>
          </div>
        </form>
          
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 

<script>
 $(document).ready(function(){
       $('#input-user-group').change(function(){
        var user_group_id = $(this).val();
        if(user_group_id == '')return false;
        
            $.ajax({
                url: 'index.php?route=user/user/userGroupPermissions&token=<?php echo $token; ?>&user_group_id='+user_group_id,
                dataType: 'json',
                beforeSend: function() {
                    $('#access_permissions_data').html('<p>Loading...</p>');
                    $('#modify_permissions_data').html('<p>Loading...</p>');
                },
                success:function(json){
                   access = [];
                   access_methods = [];
                   permissions = [];
                   methods = [];
                   $.each(json, function(index, element) {
                        if(index === 'permissions') {
                            permissions = element;
                        }
                        if(index === 'methods') {
                            methods = element;
                        }
                        if(index === 'access') {
                            access = element;
                        }
                        if(index === 'access_methods') {
                            access_methods = element;
                        }
                        if(index === 'modify') {
                            modify = element;
                        }
                        if(index === 'modify_methods') {
                            modify_methods = element;
                        }
                    });
                 
                  
                /* generate access permissions html*/
                 html_access = '';
                 
                   if(permissions!== null && permissions.length > 0 )
                   { 
                       
                       for(i=0;i<permissions.length;i++)
                       {
                            controller = permissions[i];
                            template = controller.replace('/','_');
                            html_access += '<div class="checkbox main_class">';
                        
                        cflag = checkVal(access,controller); 
                        
                        //alert(access); return false;
                        
                        if(cflag === true){ 
                            html_access += '<label><input data-section="access" type="checkbox" name="permission[access][]" value="'+controller+'"  checked="checked" /> '+controller+'</label>';  
                        }else{ 
                            html_access += '<label><input data-section="access" type="checkbox" name="permission[access][]" value="'+controller+'"  /> '+controller+'</label>';  
                        }
                        
                            html_access += '<div id="'+template+'_access" >';    
                            html_access += '<ul style="width: 100%!important;">';

                            if(template in methods) 
                            {
                               list = methods[template]; 

                                if(list!==null && list.length > 0) {
                                    
                                    for(j=0;j<list.length;j++)
                                    {
                                        html_access += '<li style="display: inline;margin:0 15px;">';
                                          
                                        check = checkVal(access_methods[template],list[j]); 
                                          
                                          if(check === true){
                                              html_access += '<input type="checkbox" name="permission[access][methods]['+controller+'][]" value="'+list[j]+'" checked="checked" />';
                                          }else{
                                              html_access += '<input type="checkbox" name="permission[access][methods]['+controller+'][]" value="'+list[j]+'"  />';
                                          }
                                        
                                        html_access += list[j];
                                        html_access += '</li>';
                                    }
                                } 
                            }
                            html_access += '</ul>'; 
                            html_access += '</div>'; 
                            html_access += '</div>'; 
                       }
                    }
                    $('#access_permissions_data').html(html_access);
                /* generate access permissions html*/    
                
        
                /* generate modify permissions html*/
                 html_modify = '';
                   if(permissions!==null && permissions.length > 0 )
                   {
                       for(i=0;i<permissions.length;i++)
                       {
                            controller = permissions[i];
                            template = controller.replace('/','_');
                            html_modify += '<div class="checkbox main_class">';
                        
                        cflag = checkVal(modify,controller); 
                        
                        if(cflag === true){ 
                            html_modify += '<label><input data-section="access" type="checkbox" name="permission[access][]" value="'+controller+'"  checked="checked" /> '+controller+'</label>';  
                        }else{ 
                            html_modify += '<label><input data-section="access" type="checkbox" name="permission[access][]" value="'+controller+'"  /> '+controller+'</label>';  
                        }
                        
                            html_modify += '<div id="'+template+'_access" >';    
                            html_modify += '<ul style="width: 100%!important;">';

                            if(template in methods) 
                            {
                               list = methods[template]; 

                                if(list!==null && list.length > 0) {
                                    
                                    for(j=0;j<list.length;j++)
                                    {
                                        html_modify += '<li style="display: inline;margin:0 15px;">';
                                          
                                        check = checkVal(modify_methods[template],list[j]); 
                                          
                                          if(check === true){
                                              html_modify += '<input type="checkbox" name="permission[access][methods]['+controller+'][]" value="'+list[j]+'" checked="checked" />';
                                          }else{
                                              html_modify += '<input type="checkbox" name="permission[access][methods]['+controller+'][]" value="'+list[j]+'"  />';
                                          }
                                        
                                        html_modify += list[j];
                                        html_modify += '</li>';
                                    }
                                } 
                            }
                            html_modify += '</ul>'; 
                            html_modify += '</div>'; 
                            html_modify += '</div>'; 
                       }
                    }
                    $('#modify_permissions_data').html(html_modify);
                /* generate modify permissions html*/   
                },
                error: function(xhr, ajaxOptions, thrownError) { alert("Errror parts"); return false;
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            })
        }) 
    
     $(document).on('change','.main_class input:checkbox',function(){
        var ischecked= $(this).is(':checked');
        var section = $(this).attr('data-section');
        var childListId = $(this).val().replace('/','_');
        if(ischecked)
           $('#'+childListId+'_'+section).find('input[type=checkbox]').prop("checked",true);
        else
           $('#'+childListId+'_'+section).find('input[type=checkbox]').prop("checked",false);  
    })   
    
}) // ready

function checkVal(list,checkVal)
{
   check = false;
   if(list!== undefined)
   {
        $.each(list, function(i,val){
          if(val === checkVal){
             check = true;
          }
        }) 
   }
  return check;
}

</script>