<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" id="form-button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-custom-url" class="form-horizontal">
            <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_url_type; ?></label>
                <div class="col-sm-9"> 
                    <select class="form-control edit_track" name="url_type" id="url_type" onchange="checkUrlType()">
                        <option <?php if($url_type == '0') echo 'selected'; ?> value="0"> <?php echo $entry_select_url_type; ?></option>
                        <!--<option <?php if($url_type == 'filter') echo 'selected'; ?> value="filter" ><?php echo $entry_filter; ?></option>-->
                        <option <?php if($url_type == 'category') echo 'selected'; ?> value="category" ><?php echo $entry_category; ?></option>
                        <option <?php if($url_type == 'category_search') echo 'selected'; ?> value="category_search" ><?php echo $entry_category_search; ?></option>
                        <option <?php if($url_type == 'product') echo 'selected'; ?> value="product"><?php echo $entry_product; ?></option>
                        <option <?php if($url_type == 'multiple_query_string') echo 'selected'; ?> value="multiple_query_string"><?php echo $entry_multiple_query_string; ?></option>
                        <!--
                        <option <?php if($url_type == 'manufacturer') echo 'selected'; ?> value="manufacturer"><?php echo $entry_manufacturer; ?></option>
                        <option <?php if($url_type == 'seller') echo 'selected'; ?> value="seller"><?php echo $entry_seller; ?></option>
                        <option <?php if($url_type == 'information') echo 'selected'; ?> value="information"><?php echo $entry_information; ?></option>
                        -->
                    </select>
                    <?php if ($error_url_type) { ?> 
                      <div class="text-danger"><?php echo $error_url_type; ?></div>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group required" id="orignal_url"> 
                <label class="col-sm-3 control-label"><?php echo $entry_orignal_url; ?></label>
                <div class="col-sm-9">
                    <textarea class="form-control edit_track" id="orignal_url_input" data-change="false" data-old-value="<?php echo $query; ?>" name="query" ><?php echo $query; ?></textarea>
                    <?php if ($error_query) { ?>
                      <div class="text-danger"><?php echo $error_query; ?></div>
                    <?php } ?>
                    <div id="notice_search_url" class="text-warning" style="display:none"><?php echo $notice_search_url; ?></div> 
                </div>
            </div>
            <div class="form-group required" id="search_id" style="display:none"> 
                <label class="col-sm-3 control-label"><b id="lable_search"></b><?php echo $entry_search_id; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control edit_track" id="search_id_input" data-change="false" data-old-value="<?php echo $search_id; ?>" name="search_id" value="<?php echo $search_id; ?>" placeholder="<?php echo $entry_search_id; ?>" />
                    <div id="notice_search_id" class="text-warning"><?php echo $text_related_search_id; ?></div> 
                    <?php if ($error_search_id) { ?>
                      <div class="text-danger"><?php echo $error_search_id; ?></div>
                    <?php } ?>
                </div>
            </div>
            
            <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_slug; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control edit_track" id="keyword" data-change="false" data-old-value="<?php echo $keyword; ?>" name="keyword" value="<?php echo $keyword; ?>" placeholder="<?php echo $entry_slug; ?>" />
                    <?php if ($error_keyword) { ?>
                      <div class="text-danger"><?php echo $error_keyword; ?></div>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo $entry_redirect_301; ?></label>
                <div class="col-sm-9">
                    <select class="form-control" name="is_redirect_301">
                        <option value="0" <?php if($is_redirect_301 == "0") echo 'selected' ?>>No</option>
                        <option value="1"  <?php if($is_redirect_301 == "1") echo 'selected' ?>>Yes</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_title; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control edit_track" id="title" data-change="false" data-old-value="<?php echo $title; ?>" name="title" value="<?php echo $title; ?>">
                    <?php if ($error_title) { ?>
                      <div class="text-danger"><?php echo $error_title; ?></div>
                    <?php } ?>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo $entry_description; ?></label>
                <div class="col-sm-9">
                    <textarea class="form-control edit_track" id="description" data-change="false" data-old-value="<?php echo $description; ?>" name="description" ><?php echo $description; ?></textarea>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo $entry_short_description; ?></label>
                <div class="col-sm-9">
                    <textarea class="form-control edit_track" id="short_description" data-change="false" data-old-value="<?php echo $short_description; ?>" name="short_description" ><?php echo $short_description; ?></textarea>
                    <div class="text-warning"><?php echo $notice_short_description; ?></div>
                </div>
            </div>
            
            <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_meta_title; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control edit_track" id="meta_title" data-change="false" data-old-value="<?php echo $meta_title; ?>" name="meta_title" value="<?php echo $meta_title; ?>" placeholder="<?php echo $entry_meta_title; ?>" />
                    <?php if ($error_meta_title) { ?>
                      <div class="text-danger"><?php echo $error_meta_title; ?></div>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_meta_description; ?></label>
                <div class="col-sm-9">
                    <textarea type="text" class="form-control edit_track" id="meta_description" data-change="false" data-old-value="<?php echo $meta_description; ?>" name="meta_description" ><?php echo $meta_description; ?></textarea>
                    <?php if ($error_meta_description) { ?>
                      <div class="text-danger"><?php echo $error_meta_description; ?></div>
                    <?php } ?>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
  </div>

<?php echo $footer; ?>

<script type="text/javascript">
    //show default
    $( document ).ready(function() {
        //auto fill category_id
        var url_type = $('#url_type').val(); 
        if(url_type == 'category' || url_type == 'category_search'){      
            $('#search_id_input').attr('value',''); 
        }
        
        $("#orignal_url_input").focusout(function(){
            var url_type = $('#url_type').val(); 
            if(url_type == 'category'){  
                autoFillCategoryID(); 
            }
            
        });
        checkUrlType();
    });
    
    //autofill categoryid
    function autoFillCategoryID(){ 
        var orignal_url = $("#orignal_url_input").val(); 
        $.ajax({
            type: 'post',
            url: 'index.php?route=digital_marketing/custom_url/autoFillCategoryId&token=<?php echo $token; ?>',
            data: {"orignal_url": orignal_url},
            dataType: 'json',
            success: function (data) {
                if(data.status == true){ 
                    $('#search_id_input').attr('value',data.category_id);
                }
            }
        });
    }
    
    
    
    //change according to URL Type
    function checkUrlType(){ 
        var url_type = $('#url_type').val(); 
        
        //set default session for validation on search_id
        var url_type = $('#url_type').val();
        $.ajax({
          type: 'post',
          url: 'index.php?route=digital_marketing/custom_url/setSessionForValidation&token=<?php echo $token; ?>',
          data: {"url_type":url_type},
          dataType: 'json',
          success: function (json) {
            //alert(json['success']);
          }
          
        });
        //end set default session for validation on search_id
        
        
        
        if(url_type == 'category'){  
            $('#orignal_url').show();
            $("#orignal_url").addClass( "required" );  
            $("#orignal_url .text-danger").show();
            $("#notice_search_url").hide();
            
            $("#search_id").hide(); 
            $("#search_id").removeClass( "required" );  
            $("#search_id .text-danger").hide();
            
            autoFillCategoryID(); 
            
            
        }else if(url_type == 'category_search'){  
            $('#orignal_url').show();
            $("#orignal_url").addClass( "required" );  
            $("#orignal_url .text-danger").show();
            $("#search_id").hide(); 
            $("#search_id").removeClass( "required" );  
            $("#search_id .text-danger").hide();
            $("#notice_search_url").show();
            $('#search_id_input').attr('value','');
            
            
            
        }else if(url_type == 'product'){  
            $('#orignal_url_input').val('');  
            $('#orignal_url').hide();
            $("#orignal_url").removeClass( "required" );  
            $("#orignal_url .text-danger").hide();
            $("#search_id").show(); 
            $("#search_id").addClass( "required" );  
            $("#search_id .text-danger").show();
            $("#notice_search_id").text('Notice : Records will be displayed on page based on the product id');
            $("#notice_search_url").hide();
            $("#lable_search").text('Product');
            
        }
    }
    
    
    $('.edit_track').on('change',function(){
        trackChange(this);
    });
    
    
    $('#form-button').on('click',function(){
        edit_track();    
        $('#form-custom-url').submit();
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
    /*
    summernote.forEach(function(element){
      var textarea = $('#'+$(element)[0].id);
      textarea.val($(element).code());
      if( $(element).code() != '<p><br></p>'){
        trackChange(textarea);
      }
    });
    */
    $('.edit_track').each(function(){
      var data_change = $(this).data('change');
      
        var old_value = $(this).data('old-value');
        var new_value = $(this).val();
        var name = $(this).attr('name');
        
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
    
    /*
    if( $('#changes_data').val().length > 0 ){
        old_data_format = $.parseJSON($('#changes_data').val());
    }
    $('#changes_data').val( JSON.stringify(old_data_format) );
    */
    
    
  }
    
    
    $('#description').summernote({
        height: 300
    });
    $('#short_description').summernote({
        height: 110
    }); 
    
    
</script>

