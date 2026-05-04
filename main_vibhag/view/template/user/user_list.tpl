<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-user').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
              <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="input-user-name"><?php echo $entry_username; ?></label>
                    <input type="text" name="filter_user_name" value="<?php echo $filter_user_name; ?>" placeholder="<?php echo $entry_username; ?>" id="input-user-name" class="form-control">
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="input-order-no"><?php echo $entry_name; ?></label>
                    <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control">
                </div>
              </div>
              
              <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" for="input-user-group"><?php echo $entry_user_group; ?></label>
                    <select name="filter_user_group" id="input-user-group" class="form-control">
                      <option value="*"><?php echo $entry_select_user_group; ?></option>
                      <?php if(!empty($user_groups)) { ?>
                        <?php foreach($user_groups as $user_group) { ?>
                          <option value="<?php echo $user_group['user_group_id']; ?>" <?php echo ($filter_user_group == $user_group['user_group_id'] ? 'selected' : '' ); ?> ><?php echo $user_group['name']; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group"> 
                    <label class="control-label" for="input-order-no"><?php echo $entry_status; ?></label>
                    <select name="filter_user_status" id="input-user-status" class="form-control">
                      <option value="*"><?php echo $entry_select_user_status; ?></option>
                      <?php if(!empty($user_status)) { ?>
                        <?php foreach($user_status as $key => $status) { 
                          $selected_status = "";
                          if(isset($filter_user_status) && $filter_user_status == $key) {
                            $selected_status = "selected";
                          }
                        ?>
                          <option value="<?php echo $key; ?>" <?php echo $selected_status; ?> ><?php echo $status; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                </div>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> Filter</button>

          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-user">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'username') { ?>
                    <a href="<?php echo $sort_username; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_username; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_username; ?>"><?php echo $column_username; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $column_name; ?></td>
                  <td class="text-left"><?php echo $column_group_name;?></td>  
                  <td class="text-center"><?php echo $column_branch_code; ?></td>  
                  <td class="text-left"><?php if ($sort == 'status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($users) { ?>
                <?php foreach ($users as $user) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($user['user_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $user['user_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $user['user_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $user['username']; ?></td>
                  <td class="text-left"><?php echo $user['name']; ?></td>
                  <td class="text-left"><?php echo $user['group_name']; ?></td>
                  <td class="text-center"><?php echo $user['branch_code']; ?></td>
                  <td class="text-left"><?php echo $user['status']; ?></td>
                  <td class="text-left"><?php echo $user['date_added']; ?></td>
                  <td class="text-right" style="width:16%;">
                    <a data-user="<?php echo $user['user_id']?>" href="javascript:void(0)" data-toggle="tooltip" title="Generate User Password" class="btn btn-primary change_user_password"><i class="fa fa-key" aria-hidden="true"></i></a>
                    <a href="<?php echo $user['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                      <?php if( isset($user['user_login']) && $user['user_login'] !='' ) {?>
                      <a href="<?php echo $user['user_login'];?>" data-toggle="tooltip" title="user login" class="btn btn-info " target="_blank"><i class="fa fa-lock"></i></a>
                      <?php } ?>
                    <a href="<?php echo $user['copy']; ?>" data-toggle="tooltip" title="<?php echo $button_copy; ?>" class="btn btn-primary"><i class="fa fa-copy"></i></a>
                    <div id="password_<?php echo $user['user_id']?>"></div>
                  </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 
<?php echo $url;?>
<script type="text/javascript">
  $('#button-filter').on('click', function() {
      
    url = 'index.php?route=user/user&token=<?php echo $token; ?><?php echo $url?>';

    var filter_user_name = $('input[name=\'filter_user_name\']').val();

    if (filter_user_name != '') {
        url += '&filter_user_name=' + encodeURIComponent(filter_user_name);
    }

    var filter_name = $('input[name=\'filter_name\']').val();

    if (filter_name != '') {
        url += '&filter_name=' + encodeURIComponent(filter_name);
    }

    var filter_user_group = $('select[name=\'filter_user_group\']').val();

    if (filter_user_group!= '*') {
        url += '&filter_user_group=' + encodeURIComponent(filter_user_group);
    }

    var filter_user_status = $('select[name=\'filter_user_status\']').val();

    if (filter_user_status!= '*') {
        url += '&filter_user_status=' + encodeURIComponent(filter_user_status);
    }

    location = url;
  });
  
   $('input[name=\'filter_user_name\'], input[name=\'filter_name\'], select[name=\'filter_user_group\']').on('keypress',function(e){
    if (e.keyCode == 13) {
      $('#button-filter').trigger('click');
    }
  });

$(document).ready(function () {

    $('.change_user_password').click(function() {
      
      var selected_user = $(this).data('user');
      
      $.ajax({
              url: "index.php?route=common/dashboard/changePassword&token=<?php echo $token; ?>&user_id="+selected_user+"&type=user",
              type: "get",
              dataType: "json",
              beforeSend: function() {
                //$('#change-password').html('Loading....');
              },
              success: function(json) {
                var innerHtml = '<input style="width:100px; border:0px; color:green " type="text" value="'+json.password+'" id="user_password_string'+selected_user+'">'
                    innerHtml += '<i style="padding-left:10px; color:#1e91cf; font-size:15px; cursor:pointer;" title="Copy to clipboard" class="fa fa-copy" onClick=copyToClipboard("user_password_string'+selected_user+'") aria-hidden="true"></i>';
                  $('#password_'+selected_user).html(innerHtml); 
              },
              error: function(xhr, ajaxOptions, thrownError) { 
                var string = xhr.responseText.replace(/<b>/g,'');
                    string = string.replace(/<\/b>/g,'');
                    alert("Error occure during request processing ::\n"+string);
              }                 
            });
    });

})

</script>