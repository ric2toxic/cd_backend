<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-customer" id="cs-submit-button" data-toggle="tooltip" title="<?php echo $btn_update_bank_detail; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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



 <?php if (!empty($success)) { ?>
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
     <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?> 
      </h3>

      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-customer" class="form-horizontal">
            <div class="tab-pane" id="tab-bank-details">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-bank-ac-holder-name"><?php echo $entry_bank_ac_holder_name; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="bank_ac_holder_name" value="<?php echo $bank_ac_holder_name; ?>" id="input-bank-ac-holder-name" class="form-control edit_track" placeholder="Bank Account Holder Name" data-block-name="bankdetails" data-change="false" data-old-value="<?php echo $bank_ac_holder_name??''; ?>" autocomplete="off"/>
                     <span class="sample_ifsc_code"><?php if (isset($error["error_account_holder_name"])) { ?>
              <div class="text-danger"><?php echo $error["error_account_holder_name"]; ?></div>
              <?php } ?></span>
                  </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-bank-ac-number"><?php echo $entry_bank_ac_number; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="bank_ac_number" value="<?php echo $bank_ac_number; ?>" id="input-bank-ac-number" class="form-control edit_track" placeholder="Bank Account Number" data-block-name="bankdetails" data-change="false" data-old-value="<?php echo $bank_ac_number??''; ?>" autocomplete="off"/>
                  <span class="sample_ifsc_code"><?php if (isset($error["error_account_number"])) { ?>
              <div class="text-danger"><?php echo $error["error_account_number"]; ?></div>
              <?php } ?></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-ifsc-code"><?php echo $entry_ifsc_code; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="ifsc_code" value="<?php echo $ifsc_code??''; ?>" id="input-ifsc-code" class="form-control edit_track" placeholder="Bank IFSC Code" data-block-name="bankdetails" data-change="false" data-old-value="<?php echo $ifsc_code; ?>" maxlength="11" autocomplete="off"/>
                  <span class="sample_ifsc_code"><?php if (isset($error["error_ifsc_code"])) { ?>
              <div class="text-danger"><?php echo $error["error_ifsc_code"]; ?></div>
              <?php } ?></span>
                  <span class="sample_ifsc_code">IFSC Code Ex:- KARB0000001 or BARB0DIGJAI</span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2"></label>
                <div class="col-sm-10">
                  <button type="button" id="ifsc_code_loading" class="btn btn-primary btn-xs hidden"></button>
                  <div class="bank_details_block"></div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 hidden"></label>
              <div class="col-sm-10">
                <input type="hidden" name="changes_data" id="changes_data" value="">
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />