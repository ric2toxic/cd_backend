<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-user-group" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-user-group" class="form-horizontal">
          <textarea hidden="hidden" id="permission_list" name="permission_list" rows="10" style="width:100%"></textarea>
            <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php  } ?>
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
            <label class="col-sm-2 control-label"><?php echo $entry_access; ?></label>
            <div class="col-sm-10">
              <div class="well well-sm" style="height: 350px; overflow: auto;">
                
                <?php
                
                foreach ($controller_classes as $permission) { ?>
                
                <div class="checkbox main_class">
                  <div>  
                    <label>
                      <?php if (in_array($permission, $access)) { ?>
                      <input id="<?php echo str_replace('/','_',$permission)?>_parent" class="access_controller" data-section="access" type="checkbox" name="permission[access][]" value="<?php echo $permission; ?>" checked="checked" />
                      <?php echo $permission; ?>
                      <?php } else { ?>
                      <input id="<?php echo str_replace('/','_',$permission)?>_parent" class="access_controller" data-section="access" type="checkbox" name="permission[access][]" value="<?php echo $permission; ?>" />
                      <?php echo $permission; ?>
                      <?php } ?>
                    </label>

                    <div style="display: inline;  padding-left:20px;">
                        <a href="javascript:void(0);" 
                           class="chk_all" 
                           data-status="1" 
                           data-class="<?php echo str_replace('/','_',$permission)?>" >Check all</a>
                        /
                        <a href="javascript:void(0);" 
                            class="chk_all" 
                            data-status="0" 
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
                
                <?php  ?>
                <!--MSA-->
                <?php $classMethods = $methods[$permission]; ?>	
                <div id="<?php echo str_replace('/','_',$permission)?>_access" >
                <ul style="width: 100%!important;">
                    <?php 

                    $access_methods = array();
                    if(isset($access['methods'][$permission]))
                    {
                        $access_methods = $access['methods'][$permission];
                    }

                    if(!empty($classMethods))
                     {
                        foreach($classMethods as $method) 
                        {
                    ?>     
                            <label> 
                                <li style="display: inline; margin:0 15px;">

                                    <?php  if(in_array($method['title'],$access_methods)) { ?>

                                        <input class="<?php echo str_replace('/','_',$permission)?>" type="checkbox" name="permission[access][methods][<?php echo $permission;?>][]" value="<?php echo $method['title']; ?>" checked="checked" />

                                    <?php } else { ?>

                                        <input class="<?php echo str_replace('/','_',$permission)?>" type="checkbox" name="permission[access][methods][<?php echo $permission;?>][]" value="<?php echo $method['title']; ?>" />

                                    <?php }  ?>

                                    <?php echo $method['title']?>
                                    <?php if(!empty($method['comment'])) {?>
                                        <a href='javascript:void(0)' tooltip='toggle' title='<?php echo $method['comment']?>'><i class="fa fa-question-circle" aria-hidden="true"></i></a>
                                    <?php } ?>
                                    
                                </li>
                            </label>
                    <?php 
                      }  
                        }
                        
                    ?>
    		</ul>
                </div>
                <?php   ?>
                
                </div>
                
                
                <?php } ?>
                
                <!--MSA-->
                
              </div>
              <a onclick="$(this).parent().find(':checkbox').prop('checked', true);"><?php echo $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);"><?php echo $text_unselect_all; ?></a></div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
<script>
 $(document).ready(function(){
   
   $('#search_permission_button').click(function(e){
        var txt = $('#input-search-permission').val();
        $('.main_class').hide();
        $('.main_class').each(function(){
           if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1){
                $(this).show(); 
           }
        });
    });
   
   
    $('#form-user-group').submit(function(){
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
        
     $(".main_class input:checkbox").change(function() {
        var ischecked= $(this).is(':checked');
        var section = $(this).attr('data-section');
        var childListId = $(this).val().replace('/','_');
        if(ischecked)
            $('#'+childListId+'_'+section).find('input[type=checkbox]').prop("checked",true);
          else
            $('#'+childListId+'_'+section).find('input[type=checkbox]').prop("checked",false);  

      });    
    
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



 })   
    
    
</script>