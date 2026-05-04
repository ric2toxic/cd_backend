<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-user" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
    <?php if(!empty($reset_cache)) {?>   
        <a href="<?php echo $reset_cache; ?>" data-toggle="tooltip" class="btn btn-default" title="Reset cache permissions data"><i class="fa fa-refresh"></i></a>
    <?php } ?>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-user" class="form-horizontal">
          
            <textarea hidden="hidden" id="permission_list" name="permission_list" rows="10" style="width:100%"></textarea>
          
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-username"><?php echo $entry_username; ?></label>
            <div class="col-sm-10">
              <input type="text" name="username" value="<?php echo $username; ?>" placeholder="<?php echo $entry_username; ?>" id="input-username" class="form-control" />
              <?php if ($error_username) { ?>
              <div class="text-danger"><?php echo $error_username; ?></div>
              <?php } ?>
            </div>
          </div>
          
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-firstname"><?php echo $entry_firstname; ?></label>
            <div class="col-sm-10">
              <input type="text" name="firstname" value="<?php echo $firstname; ?>" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname" class="form-control" />
              <?php if ($error_firstname) { ?>
              <div class="text-danger"><?php echo $error_firstname; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-lastname"><?php echo $entry_lastname; ?></label>
            <div class="col-sm-10">
              <input type="text" name="lastname" value="<?php echo $lastname; ?>" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname" class="form-control" />
              <?php if ($error_lastname) { ?>
              <div class="text-danger"><?php echo $error_lastname; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></label>
            <div class="col-sm-10">
              <input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-image"><?php echo $entry_image; ?></label>
            <div class="col-sm-10"><a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
              <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
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
            <label class="col-sm-2 control-label" for="input-confirm"><?php echo $entry_branch_code; ?></label>
            <div class="col-sm-10">
              <input type="text" name="branch_code" value="<?php echo $branch_code; ?>" placeholder="<?php echo $entry_branch_code; ?>" id="branch-code" class="form-control" />
            </div>
          </div>
    
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-confirm"><?php echo $entry_device_id; ?></label>
            <div class="col-sm-10">
              <input type="text" name="device_id" value="<?php echo $device_id; ?>" placeholder="<?php echo $entry_device_id; ?>" id="device-id" class="form-control" />
            </div>
          </div> 

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-confirm"><?php echo $entry_default_landing_page; ?></label>
            <div class="col-sm-10">
              <input <?php if($dont_show_dashboard){ echo 'checked';}?>  type="checkbox" name="dont_show_dashboard" value="Yes" placeholder="" id="dont-show-dashboard" class="form-control" />
            </div>
        </div>  
        <div class="form-group">
            <label class="col-sm-2 control-label" for="input-confirm"><?php echo $entry_landing_page_url; ?></label>
            <div class="col-sm-10">
              <input type="text" name="default_landing_page_url" value="<?php echo $default_landing_page_url; ?>" placeholder="<?php echo $entry_landing_page_url_placeholder; ?>" id="default-landing-page-url" class="form-control" />
              <?php if ($error_landing_page) { ?>
              <div class="text-danger"><?php echo $error_landing_page; ?></div>
              <?php } ?>
            </div>
          </div> 

         <!-- user permission -->   
         
         <div class="form-group">
            <label class="col-sm-2 control-label" for="input-user-group"><?php echo $entry_user_group; ?></label>
            <div class="col-sm-10">
              <select name="user_group_id" id="input-user-group" class="form-control">
                <option value="">Select User Group</option>
                <?php foreach ($user_groups as $user_group) { ?>
                <?php if ($user_group['user_group_id'] == $user_group_id) { ?>
                <option value="<?php echo $user_group['user_group_id']; ?>" selected="selected"><?php echo $user_group['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $user_group['user_group_id']; ?>"><?php echo $user_group['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
         
         
         <br>
         
         <div class="form-group">
            <label class="col-sm-2 control-label" for="input-confirm">Search Permission</label>
            <div class="col-sm-5">
              <input type="text" name="search" value="" placeholder="search controller" id="input-search-permission" class="form-control" />
            </div>
            <div class="col-sm-5">
                <button id="search_permission_button" class="btn btn-primary pull-left" type="button">Search</button>
            </div>
          </div>         
         
          <div class="form-group">
          <!-- Accessed Permission Start -->
            <label class="col-sm-2 control-label"><?php echo $entry_access; ?></label>
            <div class="col-sm-10">
              <div id="chkd_acss_permission_data" class="well well-sm" style="height: 350px; overflow: auto;">
                <?php
                  if(!empty($controller_classes)) {
                    foreach ($controller_classes as $permission) {
                      if (in_array($permission, $access)) {
                ?>
                  <div class="checkbox main_class">                 
                    <div>
                      <label>
                          <input class="access_controller" id="<?php echo str_replace('/','_',$permission)?>_parent" data-section="access" type="checkbox" name="permission[access][]" value="<?php echo $permission; ?>" checked="checked" />
                          <?php echo $permission; ?>
                      </label>
                      <div style="display: inline;  padding-left:20px;">
                        <a href="javascript:void(0);" 
                           class="chk_all" 
                           data-status="1" 
                           data-class="<?php echo str_replace('/','_',$permission)?>" >Check all</a>
                        /
                        <a href="javascript:void(0);" 
                           class="chk_all" data-status="0" 
                           data-class="<?php echo str_replace('/','_',$permission)?>" >Un-check</a>
                        /
                        <a href="javascript:void(0);" 
                            data-section="access" 
                            data-find-type="view" 
                            class="findall" 
                            data-status="0" 
                            data-class="<?php echo str_replace('/','_',$permission)?>" >@View</a>
                        /
                        <a href="javascript:void(0);" 
                            data-section="access" 
                            data-find-type="add" 
                            class="findall" 
                            data-status="0" 
                            data-class="<?php echo str_replace('/','_',$permission)?>" >@Add</a>
                        /
                        <a href="javascript:void(0);" 
                            data-section="access" 
                            data-find-type="edit" 
                            class="findall" 
                            data-status="0" 
                            data-class="<?php echo str_replace('/','_',$permission)?>" >@Edit</a>
                        /
                        <a href="javascript:void(0);" 
                            data-section="access" 
                            data-find-type="delete" 
                            class="findall" 
                            data-status="0" 
                            data-class="<?php echo str_replace('/','_',$permission)?>" >@Delete</a>
                      </div>   
                    </div>
                    <!--MSA-->
                    <?php $classMethods = $methods[str_replace('/','_',$permission)]; ?>
                    <div id="<?php echo str_replace('/','_',$permission)?>_access" >
                      <ul style="width: 100%!important;">
                        <?php 
                          
                          $access_methods = array();
                          
                          if(isset($access['methods'][$permission])) {
                            $access_methods = $access['methods'][$permission];
                          }
                           
                          if(!empty($classMethods)) 
                          {
                            foreach($classMethods as $method) 
                            {
                              if(in_array($method['title'],$access_methods)) 
                                {
                                ?>
                                    <label>
                                      <li style="display: inline;margin:0 15px;">
                                        <input class="<?php echo str_replace('/','_',$permission)?> child_chk" type="checkbox" name="permission[access][methods][<?php echo $permission;?>][]" value="<?php echo $method['title']; ?>" checked="checked" /> 
                                            <?php echo $method['title']?>
                                            <?php if(!empty($method['comment'])) {?>
                                                <a href='javascript:void(0)' tooltip='toggle' title='<?php echo $method['comment']?>'><i class="fa fa-question-circle" aria-hidden="true"></i></a>
                                            <?php } ?>
                                      </li>
                                    </label>    
                                <?php 
                                }  
                            } // end foreach loop  
                          }  // end if                        
                        ?>
                      </ul>
                    </div>                
                  </div>
                <?php 
                      }
                    } 
                  } ?>
              </div>
              <!-- /*onclick="$(this).parent().hasClass('access_controller').prop('checked', true);"*/ -->
              <!-- onclick="$(this).parent().find(':checkbox').prop('checked', false);" -->
              <a id="accesscheckboxes" ><?php echo $text_select_all; ?></a> / 
              <a id="unaccesscheckboxes"><?php echo $text_unselect_all; ?></a><br/><br/>
            </div>
          </div>

            <!-- Accessed Permission Ends -->

            <!-- Unallowed Permission Starts -->
            <div class="form-group">
          <!-- Accessed Permission Start -->
            <label class="col-sm-2 control-label"><?php echo $entry_unaccess; ?></label>
            <div class="col-sm-10">
              <div id="access_permissions_data" class="well well-sm" style="height: 350px; overflow: auto;">
                <?php
                  if(!empty($controller_classes)) {
                    foreach ($controller_classes as $permission) {
                
                    $access_methods = array();
                          
                    if(isset($access['methods'])) {
                      $access_methods = $access['methods'];
                    }

                    if (!in_array($permission, $access_methods)) {

                      $classMethods = $methods[str_replace('/','_',$permission)];
                      $access_methods = array();
                          
                      if(isset($access['methods'][$permission])) {
                        $access_methods = $access['methods'][$permission];
                      }
                      $remaining_methods = array();
                      if(!empty($classMethods)) {
                        foreach($classMethods as $method) {
                          if(!in_array($method['title'],$access_methods)) {
                            $remaining_methods[] = $method;
                          }
                        }
                      }
                  ?>
                  <?php if(!empty($remaining_methods)) { ?>
                  <div class="checkbox main_class">                 
                    <div>
                      <label>
                          <input class="access_controller" id="<?php echo str_replace('/','_',$permission)?>_parent" data-section="unaccess" type="checkbox" name="permission[access][]" value="<?php echo $permission; ?>" />
                          <?php echo $permission; ?>
                      </label>                
                      <div style="display: inline;  padding-left:20px;">
                        <a href="javascript:void(0);" 
                           class="unallow_chk_all" 
                           data-status="1" 
                           data-class="<?php echo str_replace('/','_',$permission)?>" >Check all</a>
                        /
                        <a href="javascript:void(0);" 
                            class="unallow_chk_all" 
                            data-status="0" 
                            data-class="<?php echo str_replace('/','_',$permission)?>" >Un-check</a>
                        /
                        <a href="javascript:void(0);" 
                            data-section="unaccess" 
                            data-find-type="view" 
                            class="findall" 
                            data-status="0" 
                            data-class="<?php echo str_replace('/','_',$permission)?>" >@View</a>
                        /
                        <a href="javascript:void(0);" 
                            data-section="unaccess" 
                            data-find-type="add" 
                            class="findall" 
                            data-status="0" 
                            data-class="<?php echo str_replace('/','_',$permission)?>" >@Add</a>
                        /
                        <a href="javascript:void(0);" 
                            data-section="unaccess" 
                            data-find-type="edit" 
                            class="findall" 
                            data-status="0" 
                            data-class="<?php echo str_replace('/','_',$permission)?>" >@Edit</a>
                        /
                        <a href="javascript:void(0);" 
                            data-section="unaccess" 
                            data-find-type="delete" 
                            class="findall" 
                            data-status="0" 
                            data-class="<?php echo str_replace('/','_',$permission)?>" >@Delete</a>
                      </div>   
                    </div>
                    
                    <div id="<?php echo str_replace('/','_',$permission)?>_unaccess" >
                      <ul style="width: 100%!important;">
                        <?php 

                          if(!empty($remaining_methods)) {
                            foreach($remaining_methods as $method) {
                        ?>
                        <label>
                          <li style="display: inline;margin:0 15px;">
                            <input class="<?php echo str_replace('/','_',$permission)?> un_child_chk" type="checkbox" name="permission[access][methods][<?php echo $permission;?>][]" value="<?php echo $method['title']; ?>" /> 
                            <?php echo $method['title']?>
                            <?php if(!empty($method['comment'])) {?>
                                <a href='javascript:void(0)' tooltip='toggle' title='<?php echo $method['comment']?>'><i class="fa fa-question-circle" aria-hidden="true"></i></a>
                            <?php } ?>
                          </li>
                        </label>
                        <?php 
                            } // end foreach loop
                          }  // end if
                        ?>
    		      </ul>
                    </div>
                  </div>
                <?php 
                        }
                      }
                    } 
                  } 
                ?>
              </div> 
              <!-- Unallowed Permission Ends -->
              <!-- onclick="$(this).parent().find(':checkbox').prop('checked', true); -->
              <!-- onclick="$(this).parent().find(':checkbox').prop('checked', false); -->
              <a id="accesscheckbox"><?php echo $text_select_all; ?></a> / 
              <a id="unaccesscheckbox"><?php echo $text_unselect_all; ?></a>

            </div>
          </div>
         <!-- user permission --> 
            
            
            <!-- Unallowed Permission Starts -->
          
         
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 

<script>
 $(document).ready(function(){

  // select for all methods, having value text contains @view, @add, @edit, @delete
    $(document).on('click', '.findall', function(){
       var section        = $(this).data('section');
       var find_type      = $(this).data('find-type');
       var section_class  = $(this).data('class');
       var parent         = $(this).data('parent');
       $('#'+section_class+'_'+section).find('ul li').each(function(){
          var val = $(this).find('input[type=checkbox]').val();
          if(val.toUpperCase().indexOf(find_type.toUpperCase()) != -1){
            var checkBoxes = $(this).find('input[type=checkbox]');
            checkBoxes.prop("checked", !checkBoxes.prop("checked"));
            //mark parent as checked
            if( checkBoxes.prop("checked") ) {
                $('#'+section_class+'_parent').prop("checked",true);
            }
          }
       })
    })

    $(document).on('click','.chk_all',function(){
        var section = 'access';
        var childListId = $(this).attr('data-class');
        var status = $(this).attr('data-status');
        if(status == '1'){
            $('#'+childListId+'_'+section).find('input[type=checkbox]').prop("checked",true);
        }else{
            $('#'+childListId+'_'+section).find('input[type=checkbox]').prop("checked",false);
        }
    })

    $(document).on('click','.child_chk',function(){
        var ischecked= $(this).is(':checked');
        var div_class = $(this).attr('class').split(' ');
        $('#'+div_class[0]+"_parent").prop("checked",true);
    })


    $(document).on('click','.unallow_chk_all',function(){
        var section = 'unaccess';
        var childListId = $(this).attr('data-class');
        var status = $(this).attr('data-status');
        if(status == '1'){
            $('#'+childListId+'_'+section).find('input[type=checkbox]').prop("checked",true);
        }else{
            $('#'+childListId+'_'+section).find('input[type=checkbox]').prop("checked",false);
        }
    })

    
    $(document).on('click','.un_child_chk',function(){
        var ischecked= $(this).is(':checked');
        var div_class = $(this).attr('class').split(' ');
        $('#'+div_class[0]+"_parent").prop("checked",true);
    })
    

    $('#search_permission_button').click(function(e){
        var txt = $('#input-search-permission').val();
        $('.main_class').hide();
        $('.main_class').each(function(){
           if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1){
                $(this).show(); 
           }
        });
    });
        
  $('#form-user').submit(function(){
    
    //check for show dashboard status
    if($("#dont-show-dashboard").prop('checked') == true){
        var landing_page = $('#default-landing-page-url').val();
        if(landing_page.trim() == '') {
          alert('Please spcify landing page url.');
          return false;
        } 
    }
    

    jsonObj = [];
    $('input.access_controller[type=checkbox]:checked').each(function(){
        controller = $(this).val().replace('/','_'); 
        item = [];
        $('input.'+controller+'[type=checkbox]:checked').each(function(){
             val = $(this).val();
             item.push(val);
        })
        jsonObj.push({ 
                        controller : $(this).val() , 
                        methods    : item
                    });
    }) 
    jsonString = JSON.stringify(jsonObj);
    $('#permission_list').val(jsonString);
  })    
     
  $('#input-user-group').change(function(){
    var user_group_id = $(this).val();
    if(user_group_id == '')return false;
    $.ajax({
      url: 'index.php?route=user/user/userGroupPermissions&token=<?php echo $token; ?>&user_group_id='+user_group_id,
      dataType: 'json',
      beforeSend: function() {
        $('#access_permissions_data').html('<p>Loading...</p>');
        $('#modify_permissions_data').html('<p>Loading...</p>');
        $('#chkd_acss_permission_data').html('<p>Loading...</p>');
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
        html_unaccess = '';
        if(permissions!== null && permissions.length > 0 ) {
          for(i=0;i<permissions.length;i++) {
            controller = permissions[i];
            template = controller.replace('/','_');
            cflag = checkVal(access,controller); 
              if(cflag === true){ 
                html_access += '<div class="checkbox main_class">';
                html_access += ' <div>';  
                html_access += '  <label>'; 
                html_access += '   <input class="access_controller" id="'+template+'_parent" data-section="access" type="checkbox" name="permission[access][]" value="'+controller+'"  checked="checked" /> '+controller;
                html_access += '  </label>';
                html_access += '  <div style="display: inline;  padding-left:20px;">';
                html_access += '    <a href="javascript:void(0);" class="chk_all" data-status="1" data-class="'+template+'" >Check all</a> / ';
                html_access += '    <a href="javascript:void(0);" class="chk_all" data-status="0" data-class="'+template+'" >Un-check</a>';
                
                html_access += ' / <a href="javascript:void(0);" data-section="access" ';
                        html_access += ' data-find-type="view" class="findall" ';
                        html_access += ' data-status="0" data-class="'+template+'" >';
                        html_access += '@View'; 
                html_access += '</a>'; 
                html_access += ' / <a href="javascript:void(0);" data-section="access" ';
                        html_access += ' data-find-type="add" class="findall" ';
                        html_access += ' data-status="0" data-class="'+template+'" >';
                        html_access += '@Add'; 
                html_access += '</a>'; 
                html_access += ' / <a href="javascript:void(0);" data-section="access" ';
                        html_access += ' data-find-type="edit" class="findall" ';
                        html_access += ' data-status="0" data-class="'+template+'" >';
                        html_access += '@Edit'; 
                html_access += '</a>';
                html_access += ' / <a href="javascript:void(0);" data-section="access" ';
                        html_access += ' data-find-type="delete" class="findall" ';
                        html_access += ' data-status="0" data-class="'+template+'" >';
                        html_access += '@Delete'; 
                html_access += '</a>';

                html_access += '  </div>';
                html_access += ' </div>';
                html_access += ' <div id="'+template+'_access" >';    
                html_access += '   <ul style="width: 100%!important;">';
                if(template in methods) {
                  list = methods[template]; 
                  if(list!==null && list.length > 0) {
                    for(j=0;j<list.length;j++) {
                      check = checkVal(access_methods[template],list[j]['title']); 
                      if(check === true || cflag === true){
                        html_access += '   <label>';
                        html_access += '    <li style="display: inline;margin:0 15px;">';
                        html_access += '     <input class="'+template+' child_chk" type="checkbox" name="permission[access][methods]['+controller+'][]" value="'+list[j]['title']+'" checked="checked" />';
                        html_access += list[j]['title'];
                        if(list[j]['comment']!='')
                        {
                            html_access += '    <a href="javascript:void(0)" tooltip="toggle" title="'+list[j]['comment']+'"><i class="fa fa-question-circle" aria-hidden="true"></i></a>'; 
                        }
                        html_access += '    </li>';
                        html_access += '   </label>';
                      }
                    }
                  } 
                }
                html_access += '   </ul>'; 
                html_access += '  </div>'; 
                html_access += '</div>';
              } else {
                html_unaccess += '<div class="checkbox main_class">';
                html_unaccess += ' <div>';  
                html_unaccess += '  <label>'; 
                html_unaccess += '   <input class="access_controller" id="'+template+'_parent" data-section="unaccess" type="checkbox" name="permission[access][]" value="'+controller+'" /> '+controller;
                html_unaccess += '  </label>';
                html_unaccess += '  <div style="display: inline;  padding-left:20px;">';
                html_unaccess += '    <a href="javascript:void(0);" class="unallow_chk_all" data-status="1" data-class="'+template+'" >Check all</a> / ';
                html_unaccess += '    <a href="javascript:void(0);" class="unallow_chk_all" data-status="0" data-class="'+template+'" >Un-check</a>';
                
                html_unaccess += ' / <a href="javascript:void(0);" data-section="unaccess" ';
                        html_unaccess += ' data-find-type="view" class="findall" ';
                        html_unaccess += ' data-class="'+template+'" >';
                        html_unaccess += '@View'; 
                html_unaccess += '</a>'; 
                html_unaccess += ' / <a href="javascript:void(0);" data-section="unaccess" ';
                        html_unaccess += ' data-find-type="add" class="findall" ';
                        html_unaccess += ' data-class="'+template+'" >';
                        html_unaccess += '@Add'; 
                html_unaccess += '</a>'; 
                html_unaccess += ' / <a href="javascript:void(0);" data-section="unaccess" ';
                        html_unaccess += ' data-find-type="edit" class="findall" ';
                        html_unaccess += ' data-class="'+template+'" >';
                        html_unaccess += '@Edit'; 
                html_unaccess += '</a>';
                html_unaccess += ' / <a href="javascript:void(0);" data-section="unaccess" ';
                        html_unaccess += ' data-find-type="delete" class="findall" ';
                        html_unaccess += ' data-class="'+template+'" >';
                        html_unaccess += '@Delete'; 
                html_unaccess += '</a>';

                html_unaccess += '  </div>';
                html_unaccess += ' </div>';
                html_unaccess += ' <div id="'+template+'_unaccess" >';    
                html_unaccess += '   <ul style="width: 100%!important;">';
                if(template in methods) {
                  list = methods[template];
                  if(list!==null && list.length > 0) {
                    for(j=0;j<list.length;j++) {
                      check = checkVal(access_methods[template],list[j]['title']); 
                      if(check !== true || cflag !== true){
                        html_unaccess += '   <label>';
                        html_unaccess += '    <li style="display: inline;margin:0 15px;">';
                        html_unaccess += '     <input class="'+template+' un_child_chk" type="checkbox" name="permission[access][methods]['+controller+'][]" value="'+list[j]['title']+'" />';
                        html_unaccess += list[j]['title'];
                        if(list[j]['comment']!='')
                        {
                            html_unaccess += '    <a href="javascript:void(0)" tooltip="toggle" title="'+list[j]['comment']+'"><i class="fa fa-question-circle" aria-hidden="true"></i></a>'; 
                        }
                        html_unaccess += '    </li>'; 
                        html_unaccess += '   </label>';
                      }
                    }
                  } 
                }
                html_unaccess += '   </ul>'; 
                html_unaccess += '  </div>'; 
                html_unaccess += '</div>';
              }
          }
        }
        $('#chkd_acss_permission_data').html(html_access);
        $('#access_permissions_data').html(html_unaccess);
        /* generate access permissions html*/    
      },
      error: function(xhr, ajaxOptions, thrownError) { alert("Errror parts"); return false;
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    }); // end ajax
  }); 
      
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

<script type="text/javascript">
  $(document).ready(function(){
    $('#accesscheckbox').click(function(){
      $('#access_permissions_data .access_controller, #access_permissions_data .un_child_chk').prop('checked',true);
    });
    $('#unaccesscheckbox').click(function(){
      $('#access_permissions_data .access_controller, #access_permissions_data .un_child_chk').prop('checked',false);
    });

    $('#accesscheckboxes').click(function(){
      $('#chkd_acss_permission_data .access_controller, #chkd_acss_permission_data .child_chk').prop('checked',true);
    });
    $('#unaccesscheckboxes').click(function(){
      $('#chkd_acss_permission_data .access_controller, #chkd_acss_permission_data .child_chk').prop('checked',false);
    });
  });
</script>