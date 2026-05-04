<?php echo $header; ?>
<div class="container">
  <div class="row account-page">        
	  <div class="" id="content">      
		<div class="tab-content">
          <div class="tab-pane fade active in forgot_password" id="forgot_password_show">
            <h3>Change Customer Password </h3>
            <!--<p>Enter the e-mail address associated with your account. Click submit to have your password e-mailed to you.</p> -->
            <form class="form-horizontal" enctype="multipart/form-data" method="post" action="<?php echo $action; ?>">
              <fieldset class="col-sm-12" id="forgotten">
                <legend></legend>
                <div class="form-group required">
                  <label for="input-email" class="control-label">E-Mail Address or Mobile Number</label>
                  <input type="text" class="form-control forget-email" id="input-email" placeholder="E-Mail Address or Mobile Number" value="" name="email">
                  <div class="forgot-password-error error"></div>
                </div>
              </fieldset>
              <div class="buttons">
                <input type="hidden" value="<?php echo $staff_id;?>" name="staff_id">
                <input type="submit" class="btn btn-primary forgot_submit_button" value="Continue">
              </div>
            </form>
            <h2><?php if(isset($success)){ echo $success; } ?></h2>
          </div>
        </div>
      </div>
  </div>
</div>

<?php echo $footer; ?>

