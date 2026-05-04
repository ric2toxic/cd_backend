<?php echo $header_seller; ?>
<div class="container register-seller">
  <?php /* ?>
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul> <?php */ ?>

    <div class="big-heading">
        <h1><?php echo $ms_account_register_seller; ?></h1>
    </div>
  <?php /* ?> <!--
    <div class="stepwizard col-md-offset-3">
        <div class="stepwizard-row setup-panel">
            <div class="stepwizard-step">
                <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                <p>Create Account</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                <p>Seller information</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                <p>Congratulations!</p>
            </div>
        </div>
    </div>
    --> <?php */ ?>

    <div class="alert alert-danger warning main display_none"></div>

  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?> color_white box_sha"><?php echo $content_top; ?>


        <div class="signup-form-wrapper">

            <?php /* ?> <!-- <div class="form-block-heading"><?php echo $ms_account_register_details; ?></div>--> <?php */ ?>

                <div class="row contentblock">
                    <div class="col-md-7">

                        <form id="seller-form" class="form-horizontal">

                        <div class="form-element-part">
                            <div class="form-group required">
                                <label class="col-sm-3 control-label"><?php echo $entry_name; ?></label>
                                <div class="col-sm-8">
                                    <input type="text" value="<?php if(isset($seller_name)){
                                    echo $seller_name;
                                    } ?>" name="seller[name]" placeholder="<?php echo $entry_name; ?>" class="form-control name" />
                                </div>
                            </div>
                            <?php /* ?>
                            <div class="form-group required">
                                <label class="col-sm-3 control-label"><?php echo $entry_lastname; ?></label>
                                <div class="col-sm-8">
                                    <input type="text" name="seller[lastname]" placeholder="<?php echo $entry_lastname; ?>" class="form-control name" />
                                </div>
                            </div>
                            <?php */ ?>
                            <?php /* ?>
                            <div class="form-group required">
                                <label class="col-sm-3 control-label"><?php echo $ms_account_sellerinfo_nickname; ?></label>
                                <div class="col-sm-8">
                                    <input type="text" name="seller[nickname]" placeholder="<?php echo $ms_account_sellerinfo_nickname_note; ?>" class="form-control name" />
                                </div>
                            </div>
                            <?php */ ?>
                            <div class="form-group required">
                                <label class="col-sm-3 control-label"><?php echo $entry_telephone; ?></label>
                                <div class="col-sm-8">
                                    <input type="text" name="seller[reg_telephone]" value="<?php if(isset($seller_mobile)){
                                    echo $seller_mobile;
                                    } ?>" placeholder="<?php echo $entry_telephone; ?>"  class="form-control name" />
                                </div>
                            </div>

                            <div class="form-group required">
                                <label class="col-sm-3 control-label"><?php echo $entry_email; ?></label>
                                <div class="col-sm-8">
                                    <input type="email" name="seller[reg_email]" placeholder="<?php echo $entry_email; ?>" class="form-control email" value="<?php if(isset($seller_email)){
                                    echo $seller_email;
                                    } ?>" />
                                </div>
                            </div>

                            <div class="form-group required">
                                <label class="col-sm-3 control-label"><?php echo $entry_password; ?></label>
                                <div class="col-sm-8">
                                    <input type="password" name="seller[password]" placeholder="<?php echo $entry_password; ?>" class="form-control password" />
                                </div>
                            </div>

                            <div class="form-group required">
                                <label class="col-sm-3 control-label"><?php echo $entry_confirm; ?></label>
                                <div class="col-sm-8">
                                    <input type="password" name="seller[password_confirm]" placeholder="<?php echo $entry_confirm; ?>" class="form-control password" />
                                </div>
                            </div>

                            <?php if (isset($seller_terms)) { ?>
                            <div class="form-group required">
                                <label class="col-sm-3 control-label"><?php echo $ms_account_sellerinfo_terms; ?></label>
                                <div class="col-sm-8">
                                    <p style="margin-bottom: 0">
                                        <input type="checkbox" name="seller[terms]" value="1" />
                                        <?php echo $seller_terms; ?>
                                    </p>
                                </div>
                            </div>
                            <?php } ?>

                            <div class="form-group">
                                <div class="col-sm-3">&nbsp;</div>
                                <div class="col-sm-8">
                                    <div class="buttons">
                                        <div class="pull-left">
                                            <a class="btn btn-primary" id="ms-submit-button" value="<?php echo $button_continue; ?>"><span><?php echo $button_continue; ?></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </form>

                    </div>
                    <div class="col-md-4">
                        <div class="calltobox">
                            <?php echo $text_account_already; ?>
                        </div>
                    </div>
                </div>


        </div>


      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>

<script>
	var msGlobals = {
		formError: '<?php echo htmlspecialchars($ms_error_form_submit_error, ENT_QUOTES, "UTF-8"); ?>'
	};
</script>
<?php echo $footer_seller; ?>