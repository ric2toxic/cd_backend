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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-picker" class="form-horizontal">

            <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_first_name; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control edit_track" data-block-name="profile"  id="first_name" data-change="false" data-old-value="<?php echo $first_name; ?>" name="first_name" value="<?php echo $first_name; ?>" placeholder="<?php echo $entry_first_name; ?>" />
                    <?php if ($error_first_name) { ?>
                      <div class="text-danger"><?php echo $error_first_name; ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_last_name; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control edit_track" data-block-name="profile"  id="last_name" data-change="false" data-old-value="<?php echo $last_name; ?>" name="last_name" value="<?php echo $last_name; ?>" placeholder="<?php echo $entry_last_name; ?>" />
                    <?php if ($error_last_name) { ?>
                      <div class="text-danger"><?php echo $error_last_name; ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_phone; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control edit_track" data-block-name="profile"  id="phone" data-change="false" data-old-value="<?php echo $phone; ?>" name="phone" value="<?php echo $phone; ?>" placeholder="<?php echo $entry_phone; ?>" />
                    <?php if ($error_phone) { ?>
                      <div class="text-danger"><?php echo $error_phone; ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_email; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control edit_track" data-block-name="profile"  id="email" data-change="false" data-old-value="<?php echo $email; ?>" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" />
                    <?php if ($error_email) { ?>
                      <div class="text-danger"><?php echo $error_email; ?></div>
                    <?php } ?>
                </div>
            </div>

             <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_password; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control edit_track" data-block-name="profile"  id="password" data-change="false" name="password" value="" placeholder="<?php echo $entry_password; ?>" />
                    <?php if ($error_password) { ?>
                      <div class="text-danger"><?php echo $error_password; ?></div>
                    <?php } ?>
                </div>
            </div>           

             <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_confirm_password; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control edit_track" data-block-name="profile"  id="confirm_password" data-change="false" name="confirm_password" value="" placeholder="<?php echo $entry_confirm_password; ?>" />
                    <?php if ($error_confirm_password) { ?>
                      <div class="text-danger"><?php echo $error_confirm_password; ?></div>
                    <?php } ?>
                </div>
             </div>


             <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo $entry_device_id; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control edit_track" data-block-name="profile"  id="device_id" data-change="false" name="device_id" value="<?php echo $device_id; ?>" placeholder="<?php echo $entry_device_id; ?>" />
                    <?php if ($error_device_id) { ?>
                      <div class="text-danger"><?php echo $error_device_id; ?></div>
                    <?php } ?>
                </div>
             </div>


            <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_pickup_city_code; ?></label>
                <div class="col-sm-9">
                    <input type="text" maxlength="2" class="form-control edit_track" onkeydown="upperCaseF(this)" data-block-name="profile"  id="pickup_city_code" data-change="false" data-old-value="<?php echo $pickup_city_code; ?>" name="pickup_city_code" value="<?php echo $pickup_city_code; ?>" placeholder="<?php echo $entry_pickup_city_code; ?>" />
                    <?php if ($error_pickup_city_code) { ?>
                      <div class="text-danger"><?php echo $error_pickup_city_code; ?></div>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_pickup_city; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control edit_track" data-block-name="profile"  id="pickup_city" data-change="false" data-old-value="<?php echo $pickup_city; ?>" name="pickup_city" value="<?php echo $pickup_city; ?>" placeholder="<?php echo $entry_pickup_city; ?>" />
                    <?php if ($error_pickup_city) { ?>
                      <div class="text-danger"><?php echo $error_pickup_city; ?></div>
                    <?php } ?>
                </div>
            </div>
            
            <div class="form-group required">
                <label class="col-sm-3 control-label"><?php echo $entry_status; ?></label>
                <div class="col-sm-9">
                    <select name="status" class="form-control edit_track" data-block-name="profile">
                        <option value="1" <?php if($status != '' && $status == 1) echo 'selected'; ?>>Active</option>
                        <option value="0" <?php if( $status != '' && $status == 0) echo 'selected'; ?>>Deactive</option>
                    </select>
                    <?php if ($error_status) { ?>
                      <div class="text-danger"><?php echo $error_status; ?></div>
                    <?php } ?>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo $entry_asigne_seller; ?></label>
                <div class="col-sm-9">
                    <div class="seller_loader"></div>
                    <!--
                    <select name="seller[]" id="seller" style="height:200px" class="form-control" multiple data-block-name="seller">
                        <option><?php echo $entry_select_seller ?></option>  
                        <?php foreach($seller_list as $sellerIds){ ?>
                        <option <?php foreach($picker_seller_list as $pickers_seller){ if($sellerIds['seller_id'] == $pickers_seller) echo 'selected'; } ?> value="<?php echo $sellerIds['seller_id'] ?>"><?php echo $sellerIds['nickname'] ?></option>
                        <?php } ?> 
                    </select>
                    -->
                    <div class="col-sm-5">
                        <div id="all_seller" class="seller" style="overflow-y: auto; height: 200px;">
                            <p><input name="select_all_seller" id="select_all_seller" type="checkbox" value="1"> <?php echo $entry_all_seller; ?></p>
                            <?php if(count($seller_list) > 0){
                                foreach($seller_list as $sellerIds){ 
                            ?>
                                <p id="<?php echo $sellerIds['seller_id'] ?>"> <input name="seller_id[]" type="checkbox" value="<?php echo $sellerIds['seller_id'] ?>"><?php echo $sellerIds['company'] ?> (<?php echo $sellerIds['nickname'] ?>)</p>
                            <?php } 
                            }
                            ?> 
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <p><button type="button" class="btn-primary" onclick="addSeller()"> >> </button></p>
                        <p><button type="button" class="btn-primary" onclick="removeSeller()"> << </button></p>
                        
                    </div>
                    <div class="col-sm-5">
                        <div id="selected_seller" class="seller" style="overflow-y: auto; height: 200px;">
                            <p> <input name="select_selected_seller" id="select_selected_seller" type="checkbox" value="1"> <?php echo $entry_selected_seller; ?></p>
                            <?php 
                            $assign_ids = array();

                            if(isset($assign_seller_list) && count($assign_seller_list) > 0) { 
                               $assign_ids = array_column($assign_seller_list, 'seller_id');
                             }  
                         
                            if(count($picker_seller_list) > 0){
                                foreach($picker_seller_list as $pickers_seller){ 
                                  if(!in_array($pickers_seller['seller_id'], $assign_ids))
                                  {
                              ?>
                                <p id="<?php echo $pickers_seller['seller_id'] ?>">
                                    <input name="selected_seller[]" type="checkbox" city_code="<?php echo $pickers_seller['city_code'] ?>" value="<?php echo $pickers_seller['seller_id'] ?>" class="selected_seller">
                                    <?php echo $pickers_seller['company'] ?> 
                                    (<?php echo $pickers_seller['nickname'] ?>)
                                    <input name="seller[]" type="hidden" value="<?php echo $pickers_seller['seller_id'] ?>">
                                </p>
                            <?php } } 
                            }?> 

                            <?php if(isset($self_assign_seller) && !empty($self_assign_seller)) {
                                  foreach($self_assign_seller as $self_assign){                                    
                               ?>
                               <p id="<?php echo $self_assign['seller_id'] ?>" class="disable">
                                    <input name="selected_seller[]" type="checkbox" value="<?php echo $self_assign['seller_id'] ?>">
                                    <?php echo $self_assign['company'] ?> (<?php echo $self_assign['nickname'] ?>
                                </p>
                            <?php } } ?>

                        </div>
                    </div>
                    <?php if ($error_seller) { ?>
                      <div class="text-danger"><?php echo $error_seller; ?></div>
                    <?php } ?>
                    <input class="picker_id" name="picker_id" type="hidden" value="<?php echo $picker_id ?>">
                </div>
            </div> 

<?php if(isset($picker_id) && !empty($picker_id)) { ?>
            <div class="form-group">
              <div class="col-sm-12">
              <button type="button" class="btn-primary pull-right" onclick="assign_seller();" id="assign_button"> Assign </button>
              </div>
            </div>
<?php } ?>            
        </form>

                       
                         <?php if(isset($assign_seller_list) && count($assign_seller_list) > 0) { ?>
                         <br /><br /><br />
                        <div class="table-responsive">
                           <h3 class="panel-title"><i class="fa fa-list"></i>  Assign Following Sellers </h3><br />
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <td class="text-left">Pickup Associate</td>
                                        <td class="text-left">Start Date</td>
                                        <td class="text-left">End Date</td>
                                        <td class="text-left">Assign Seller</td>
                                        <td class="text-left">Delete</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                            $i= 0;   
                                            foreach($assign_seller_list as $assign_seller){ ?>
                                    <tr id="row_<?php echo $assign_seller['id']; ?>">
                                        <td class="text-left"><?php echo $assign_seller['first_name'].' '.$assign_seller['last_name']; ?></td>
                                        <td class="text-left"><?php echo $assign_seller['start_date']; ?></td>
                                        <td class="text-left"><?php echo $assign_seller['end_date']; ?></td>
                                        <td class="text-left"><?php echo $assign_seller['company']; ?> (<?php echo $assign_seller['nickname']; ?>)</td>
                                        <td class="text-left">
                                          <a href="javascript:;" onclick="delete_assign_seller(<?php echo $assign_seller['id']; ?>)" class="btn btn-primary btn-sm" data-original-title="Delete"><i class="fa fa-trash-o"></i></a> 
                                        </td>
                                    </tr>
                                    <?php    }  ?>
                                </body>

                          </table>
                        </div>
                        <?php } ?>

      </div>
    </div>
  </div>
  </div>

<!-- Modal -->
<div id="Modal-Assign" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
    <form method="post" enctype="multipart/form-data" id="form-assign_seller" class="form-horizontal">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Assign Following Sellers</h4>
      </div>
      <div class="modal-body">
        <p><img src="view/image/ajax-loader.gif" /> Please wait...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="assign_seller_save();">Assign</button>
      </div>
      </form>
    </div>
  </div>
</div>

<?php echo $footer; ?>

<script type="text/javascript">
    
    function assign_seller()
    {    
         $("#Modal-Assign .alert").remove(); 
         var sellers   = [];
         var city_code = '';
         var pickup_id = <?php if(isset($picker_id) && !empty($picker_id)) { echo $picker_id; } else { echo 0; } ?>;
        $.each($("input[class='selected_seller']:checked"), function(){            
                sellers.push($(this).val());
                city_code = $(this).attr('city_code');
            });

        if(sellers.length == 0)
        {
            alert("Please select atleast one seller");
        }
        else
        {
            $("#Modal-Assign").modal("show");
            var ajax_url = 'index.php?route=operations/pickup/assign_seller&city_code=' + city_code +'&sellers=' + sellers+'&pickup_id=' + pickup_id +'&token=<?php echo $this->session->data["token"]; ?>'

            $.ajax({
            url: ajax_url,
            success: function( data ){
                  $("#Modal-Assign .modal-body").html(data);
                  var dateToday = new Date(); 
                  $('.date').datetimepicker({
                   pickDate: true,
                   pickTime: false,
                   minDate: dateToday
                  });
                }
            });

        }

    }

$(document).delegate('.filtar_close_icon', 'click', function(e){
    $(this).parents(".filter").remove();
});

function assign_seller_save()
{        
          $("#Modal-Assign .alert").remove(); 
         var actionurl = 'index.php?route=operations/pickup/save_assign_seller&token=<?php echo $this->session->data["token"]; ?>';
         var form_data = $("#form-assign_seller").serialize();
         ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                success: function(data)
                {
                  if(data['error'] == 1)
                    {
                       $("#Modal-Assign .modal-body").prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Error: '+data['error_msg']+'</div>');
                    }
                  else 
                    {
                        $("#Modal-Assign .modal-body").prepend('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> Success: '+data['success_msg']+'</div>');
                        location.reload();
                    }     
                }
         });
}

function delete_assign_seller(id)
{
    var ajax_url = 'index.php?route=operations/pickup/delete_assign_seller&id=' + id +'&token=<?php echo $this->session->data["token"]; ?>'

            $.ajax({
            url: ajax_url,
            success: function( data ){
                  location.reload();
                }
            });
}

    //for change pickup_city_code in uppercase 
    function upperCaseF(a){
        setTimeout(function(){
            a.value = a.value.toUpperCase();
        }, 1);
    } 
    
    
    $('.edit_track').on('change',function(){
        trackChange(this);
    });
    
    
    $('#form-button').on('click',function(){
        edit_track();    
        $('#form-picker').submit();
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
        old_data_format[name] = {"old_value":"'+  old_value + '","new_value":"' + new_value + '"};
      }
    });
    
    /*
    if( $('#changes_data').val().length > 0 ){
        old_data_format = $.parseJSON($('#changes_data').val());
    }
    $('#changes_data').val( JSON.stringify(old_data_format) );
    */
    
    
  }
    
    
    //get seller onclick Pickup City Code
    $( "#pickup_city_code" ).change(function() {
        var pickup_city_code = $(this).val();
        var delay = 250;   
        
        var picker_id = $('input[name="picker_id"]').val();
        if(picker_id != '' && picker_id != 0){
            var ajax_url = 'index.php?route=operations/pickup/getSellerByPickupCityCode&pickup_city_code=' + pickup_city_code +'&picker_id=' + picker_id +'&token=<?php echo $this->session->data["token"]; ?>'                  
        }else{
            var ajax_url = 'index.php?route=operations/pickup/getSellerByPickupCityCode&pickup_city_code=' + pickup_city_code +'&token=<?php echo $this->session->data["token"]; ?>'                  
        }
        
        $.ajax({
            url: ajax_url,
            beforeSend: function() {
                $('.seller_loader').html('<img src="view/image/ajax-loader.gif" />');
                $('#all_seller').hide();
            },
            success: function( data ){
                setTimeout(function() {
                  $('#all_seller').show();
                  $('#all_seller').html( data );
                  $('.seller_loader').html('');
                }, delay);
                
                
                
            }
        }); 
    });

    
    function addSeller(){ 
        $("#selected_seller, #all_seller").addClass("disable");
        var sellers = [];
        var pickup_city_code = $('input[name="pickup_city_code"]').val();
        
        var sellers = $('input[name="seller_id[]"]:checked').each(function(){
            var seller_id = $(this).val();
            var seller_name = $('#'+seller_id).text();
            
            $('#'+seller_id).remove();
            add_text = '<p id="' + seller_id +'">'
                    +'<input name="selected_seller[]" type="checkbox" class="selected_seller" city_code="' + pickup_city_code + '" value="' + seller_id + '">' + seller_name 
                    +'<input name="seller[]" type="hidden" value="' + seller_id + '">' 
                    + '</p>'
            $('#selected_seller').append(add_text);
        }); 
        
         var actionurl = $("#form-picker").attr('action');
         var form_data = $("#form-picker").serialize();
         var ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                beforeSend : function(xhr)
                {
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(response)
                {
                  $("#selected_seller, #all_seller").removeClass("disable");
                },
               complete: function(data){
                var ajax = null;
                $("#selected_seller, #all_seller").removeClass("disable");
               }
         });
         $('#select_all_seller:checkbox').prop('checked', false);
         $('#select_selected_seller:checkbox').prop('checked', false);
    } 
    
    function removeSeller(){
        
        $("#selected_seller, #all_seller").addClass("disable");
        var sellers = [];
        var pickup_city_code = $('input[name="pickup_city_code"]').val();
        
        var sellers = $('input[name="selected_seller[]"]:checked').each(function(){
            var seller_id = $(this).val();
            var seller_city_code = $(this).attr('city_code');
            var seller_name = $('#'+seller_id).text();
            
            $('#'+seller_id).remove();
            if(seller_city_code == pickup_city_code){
                add_text = '<p id="' + seller_id +'"><input name="seller_id[]" type="checkbox" city_code="' + pickup_city_code + '" value="' + seller_id + '">' + seller_name + '</p>'
                $('#all_seller').append(add_text);
            }
        });

         var actionurl = $("#form-picker").attr('action');
         var form_data = $("#form-picker").serialize();
         var ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                beforeSend : function(xhr)
                {
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(response)
                {
                  $("#selected_seller, #all_seller").removeClass("disable");
                },
               complete: function(data){
                var ajax = null;
                $("#selected_seller, #all_seller").removeClass("disable");
               }
         });
         
         $('#select_all_seller:checkbox').prop('checked', false);
         $('#select_selected_seller:checkbox').prop('checked', false);

    } 
  
  $(document).ready(function() {

$("#select_all_seller").click(function(){
    $('#all_seller input:checkbox').not(this).prop('checked', this.checked);
});

$("#select_selected_seller").click(function(){
    $('.selected_seller:checkbox').not(this).prop('checked', this.checked);
});

  })  
    
</script>

