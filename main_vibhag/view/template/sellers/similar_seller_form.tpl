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
    <div class="alert alert-danger"><i class="fa fa-right-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-custom-url" class="form-horizontal">
            <input type="hidden" name="edit_id" value="<?php echo $edit_id ?? 0; ?>" />
            <div class="form-group required" id="orignal_url"> 
                <label class="col-sm-3 control-label"><?php echo $entry_category; ?></label>
                <div class="col-sm-9">
                     <?php if(isset($category_name) && ($category_name != '') ){ ?>
                        <p class="form-control"><?php echo $category_name; ?></p>
                        <input type="hidden" name="category_id" value="<?php echo $category_id; ?>" />
                    <?php }else{ ?>
                        <select class="form-control edit_track" name="category_id" id="category_id"> 
                             <option value="0">select category</option> 
                             <?php foreach($category_data as $category){ ?>
                             <option <?php if($category_id == $category['category_id']) echo 'selected'  ?>  value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option> 

                             <?php } ?>
                        </select>
                    <?php } ?>

                    <?php if ($error_category_id) { ?>
                      <div class="text-danger"><?php echo $error_category_id; ?></div>
                    <?php } ?>
                    <!-- No seller msg -->
                    <div id="no_seller" style="display: none" class="text-warning"><?php echo $warning_no_cat_seller; ?></div>
                    <!-- No seller msg -->
                    <div id="single_seller" style="display: none" class="text-warning"><?php echo $warning_single_cat_seller; ?></div>
                </div>
            </div>
            
            <div class="form-group required" id="seller_outer">  
                <label class="col-sm-3 control-label"><?php echo $entry_cluster_name; ?></label>
                <div class="col-sm-9" id="seller_id_outer"> 
                    <input class="form-control" type="text" name="cluster_name" id="cluster_name" value="<?php echo $cluster_name ?? ''; ?>" placeholder="<?php echo $entry_cluster_name?>" />
                    <?php if ($error_cluster_name) { ?>
                      <div class="text-danger"><?php echo $error_cluster_name; ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group required" id="similar_seller"> 
                <label class="col-sm-3 control-label"><?php echo $entry_similar_seller; ?></label>
                <div class="col-sm-4 all_seller_outer">
                    <p>
                       <label style="font-weight: normal;">
                        <input name="all_select_sellers" type="checkbox" onclick="$('input[name*=seller_id]').prop('checked', this.checked)" >
                        <?php echo $text_all_seller; ?>
                        </label>
                    </p>
                    <img style="display:none" class="loading_img" src="view/image/ajax-loader.gif" /> 
                    <div id="all_seller" class="seller" style="overflow-y: auto; height: 200px;">
                    </div>
                <?php if ($error_similar_seller) { ?>
                    <div class="text-danger"><?php echo $error_similar_seller; ?></div>
                <?php } ?>
                </div>
                 
                <div class="col-sm-1">
                    <p><button type="button" class="btn-primary" onclick="addSeller()"> >> </button></p>
                    <p><button type="button" class="btn-primary" onclick="removeSeller()"> << </button></p>
                </div>
                
                <div class="col-sm-4 similar_seller_outer">
                    <p>
                        <label style="font-weight: normal;">
                        <input name="all_select_similar_sellers" type="checkbox" onclick="$('input[name*=selected_seller]').prop('checked', this.checked);" >
                        <?php echo $text_selected_seller; ?>
                        </label>
                    </p>
                    <img style="display:none" class="loading_img" src="view/image/ajax-loader.gif" /> 
                    <div id="selected_seller" class="seller" style="overflow-y: auto; height: 200px;">
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
  </div>

<?php echo $footer; ?>

<script type="text/javascript">
    
$( document ).ready(function() {

    var edit_id = '<?php echo $edit_id ?? '';?>';
    var category_id = '<?php echo $category_id ?? '';?>';

    $("#category_id").on('change',function(){

        var category_id = $(this).val();
        getAllCategorySellers(category_id, edit_id);

    })

   if(category_id != '' || edit_id != '') {
        getAllCategorySellers(category_id, edit_id);
   }

})


function getAllCategorySellers(category_id, edit_id){ 
        if(category_id == 0){
            var category_id = $('#category_id').val();
        }
        
        if(edit_id == 0){
            var edit_id = $('#edit_id').val();  
        }

        //default show
        $("#similar_seller").show(); 
        $("#single_seller").hide();  
        $("#no_seller").hide();
        
        $.ajax({
            type: 'post',
            url: 'index.php?route=sellers/similar_sellers/getAllseller&token=<?php echo $token; ?>',  
            data: {"category_id": category_id, "edit_id": edit_id},   
            dataType: 'json',
            beforeSend: function(msg){ 
                $(".all_seller_outer .loading_img").show(); 
                $("#selected_seller").html('');
            },
            success: function (data) { 

                if(data.status == true){
                    $("#all_seller").html(data.html);
                    if((edit_id == '' || edit_id == undefined) && data.seller_count <= '1'){
                        //$("#similar_seller").hide();
                        $("#single_seller").show();
                        $("#no_seller").hide();
                        $("#all_seller").html('');
                    } 
                    if(data.seller_count == '0') {
                        $("#similar_seller").hide();
                        $("#single_seller").hide();
                        $("#no_seller").show();
                    }
                    if(edit_id != '' && data.selected_html !=''){
                        $("#selected_seller").html(data.selected_html);
                    }
                }else if(data.status == false && data.seller_count == '0') {
                    $("#no_seller").show();
                    $("#all_seller").html('');
                    $("#selected_seller").html('');
                }
            },
            complete: function(msg){ 
                $(".all_seller_outer .loading_img").hide();  
            }
            
        });
    }
   
    function addSeller(){ 
        var sellers = [];
        var sellers = $('input[name="seller_id[]"]:checked').each(function(){
            var seller_id = $(this).val();
            var seller_name = $('#'+seller_id+' a ').attr('title');
            
            $('#all_seller #'+seller_id).remove();
            var add_text = '<p id="' + seller_id +'">'
                    +'<label style="font-weight:normal!important;"><a title="' + seller_name +'"><input name="selected_seller[]" type="checkbox" value="' + seller_id + '">&nbsp;' + seller_name.substring(0, 35) + '</a>'
                    +'&nbsp;<input name="similar_seller[]" type="hidden" value="' + seller_id + '">' 
                    + '</label></p>'
            $('#selected_seller').append(add_text);
        }); 

        //reset select_all checkbox
        $("input[name='all_select_sellers']:checkbox").prop('checked',false);
    } 

    function removeSeller(){
        
        var sellers = [];
        var sellers = $('input[name="selected_seller[]"]:checked').each(function(){
            var seller_id = $(this).val();
            var seller_name = $('#'+seller_id+' a').attr('title');
            
            $('#selected_seller #'+seller_id).remove(); 
            
            var add_text = '<p id="' + seller_id +'"><label style="font-weight:normal!important;"><a title="' + seller_name +'"><input name="seller_id[]" type="checkbox" value="' + seller_id + '">&nbsp;' + seller_name.substring(0, 35) + '</a></label></p>'
            $('#all_seller').append(add_text);
            
        }); 

        //reset select_all checkbox
        $("input[name='all_select_similar_sellers']:checkbox").prop('checked',false);
    } 

    $('#form-button').on('click',function(){
        edit_track();    
        $('#form-custom-url').submit();
    });   
    

   function edit_track(){ 
    var old_data_format = {};
    
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
    
  }

</script>
<style>
    #similar_seller a{
        color: #666666;
    }
    #similar_seller a:hover{
        color: #666666;
    }
</style>

