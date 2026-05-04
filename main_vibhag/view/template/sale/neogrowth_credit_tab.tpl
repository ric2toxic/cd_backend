<div class="content">
  
  <div class="form-group">
    <label class="col-sm-2 control-label" for="neogrowth_credit_status">NeoGrowth Credit Status</label>
    <div class="col-sm-8">
      <select name="neogrowth_credit_status" id="neogrowth_credit_status" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $neogrowth_credit_status ?? 0; ?>" >
        <?php if (!empty($neogrowth_credit_status)) { ?>
          <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
          <option value="0"><?php echo $text_disabled; ?></option>
          <?php } else { ?>
          <option value="1"><?php echo $text_enabled; ?></option>
          <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
      <?php } ?>
      </select>
    </div>
  </div>

  <div class="form-group" id="neogrowth_account_div">
    <label class="col-sm-2 control-label" for="neogrowth_account_number">NeoGrowth Account Number</label>
    <div class="col-sm-8">
      <input type="text" name="neogrowth_account_number" value="<?php echo $neogrowth_account_number ?? ''; ?>"  
      id="neogrowth_account_number" class="form-control" data-block-name="general" data-old-value="<?php echo $neogrowth_account_number ?? 0; ?>" />
    </div>
  </div>
  
  <div class="form-group" id="neogrowth_registration_div">
    <label class="col-sm-2 control-label" for="neogrowth_registration_number">NeoGrowth Registration Number</label>
    <div class="col-sm-8">
      <input type="text" name="neogrowth_registration_number" value="<?php echo $neogrowth_registration_number ?? ''; ?>"  id="neogrowth_registration_number" class="form-control" data-block-name="general" data-old-value="<?php echo $neogrowth_registration_number ?? 0; ?>" />
    </div>
  </div>

</div>