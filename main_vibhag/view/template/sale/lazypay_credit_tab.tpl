<div class="content">
  <div class="form-group">
    <label class="col-sm-2 control-label" for="lazypay_status">Lazypay Status</label>
    <div class="col-sm-8">
      <select name="lazypay_status" id="lazypay_status" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $lazypay_status; ?>">
        <?php if ($lazypay_status) { ?>
          <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
          <option value="0"><?php echo $text_disabled; ?></option>
          <?php } else { ?>
          <option value="1"><?php echo $text_enabled; ?></option>
          <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
      <?php } ?>
      </select>
    </div>
  </div>
  <div class="form-group" id="lazypay_mobile_div">
    <label class="col-sm-2 control-label" for="lazypay_mobile">Lazypay Mobile Number</label>
    <div class="col-sm-8">
      <input type="text" name="lazypay_mobile" value="<?php echo $lazypay_mobile; ?>"  id="lazypay_mobile" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $lazypay_mobile; ?>" />
    </div>
  </div>
  <div class="form-group" id="lazypay_email_div">
    <label class="col-sm-2 control-label" for="lazypay_email">Lazypay Email</label>
    <div class="col-sm-8">
      <input type="text" name="lazypay_email" value="<?php echo $lazypay_email; ?>" id="lazypay_email" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $lazypay_email; ?>" />
    </div>
  </div>
</div>