<?php echo $header; ?>
<div id="content">
  <div class="container-fluid"><br />
    <br />
    <div class="row">
      <div class="col-sm-offset-4 col-sm-4" id="login-form">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title"><i class="fa fa-lock"></i> <?php echo $text_login; ?></h1>
          </div>
          <div class="panel-body">
            <?php if ($success) { ?>
            <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
              <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php } ?>
            <?php if ($error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
              <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php } ?>
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label for="input-username"><?php echo $entry_username; ?></label>
                <div class="input-group"><span class="input-group-addon"><i class="fa fa-user"></i></span>
                  <input type="text" name="username" value="<?php echo $username; ?>" placeholder="<?php echo $entry_username; ?>" id="input-username" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label for="input-password"><?php echo $entry_password; ?></label>
                <div class="input-group"><span class="input-group-addon"><i class="fa fa-lock"></i></span>
                  <input type="password" name="password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control" />
                </div>
              </div>
              <div class="text-right">
                <button type="submit" class="btn btn-primary"><i class="fa fa-key"></i> <?php echo $button_login; ?></button>
              </div>
              <?php if ($redirect) { ?>
              <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
              <?php } ?>
            </form>
          </div>
        </div>
      </div>
      <!-- This area will show on new password generation alert -->
        <div class="col-sm-offset-3 col-sm-6" style="display: none;" id="password-information">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title" style="font-size: 20px;">Password Information</h4>
              </div>
              <div class="modal-body message_status">
                  <p style="font-size: 16px;">
                    Your old password has been expired and new password generated : 
                  </p>
                  <br>
                  <p style="font-size: 28px">
                    <input style="width:200px; border:0px; color:green; font-size: 25px " type="text" value="<?php echo $new_password_string?>" id="password_string_popup">
                    <i style="color:#1e91cf; font-size:30px; cursor:pointer;" title="Copy to clipboard" class="fa fa-copy" onClick="copyToClipboard('password_string_popup')" aria-hidden="true"></i>    
                    </p>
                   <br> 
                  
                  <p style="font-size: 16px;">

                   <b>COPY this password RIGHT NOW!</b> After closing this information dialog box, 
                     <b>Use the Copied password to Login.</b> 
                       Do not Refresh or Close this page without saving the new password. 
                       Old password will not work anymore.
                  </p>
                  
                  <p style="font-size: 16px; font-weight: bold; color:red; font-style: italic;">
                    After logging in, your browser will ask you to Update your saved password. Without fail, do that immediately; else you will forget the password afterwards (as usual)!!
                  </p>

              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default confirm-password-saved">Confirm Saved The Password</button>
              </div>
          </div>
        </div>
      <!-- This area will show on new password generation alert -->
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    
    var user_old_password_status = '<?php echo $old_password_status?>';
    var new_password_string  = '<?php echo $new_password_string?>'; 
    var username  = '<?php echo $username?>'; 
    $('#input-username').val('');
    $('#input-password').val('');
    if(user_old_password_status == '1' && new_password_string !='') {
      $('#input-username').val(username);
      $('#input-password').val(new_password_string);
    }
    
  })

</script>

<?php echo $footer; ?>