<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <h3><?php echo $text_credit_facility; ?></h3>

      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <div class="col-sm-12">
              <?php echo $text_static_message; ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-12 credit_facility">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
          <fieldset>
            <h3><?php //echo $text_credit; ?></h3>
            <div class="form-group required">
              <div class="col-sm-3">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
              </div>
              <div class="col-sm-6">
                <input type="text" name="name" value="<?php echo $name; ?>" id="input-name" class="form-control" />
                <?php if ($error_name) { ?>
                <div class="text-danger"><?php echo $error_name; ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group required">
              <div class="col-sm-3">
                <label class="control-label" for="input-firm"><?php echo $entry_firm; ?></label>
              </div>
              <div class="col-sm-6">
                <input type="text" name="firm" value="<?php echo $firm; ?>" id="input-firm" class="form-control" />
                <?php if ($error_firm) { ?>
                <div class="text-danger"><?php echo $error_firm; ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group required">
              <div class="col-sm-3">
                <label class="control-label" for="input-tenure"><?php echo $entry_tenure; ?></label>
              </div>
              <div class="col-sm-6">
                <div class="credit_tenure_year">
                  <input type="text" name="tenure_year" value="<?php echo $tenure_year; ?>" placeholder="<?php echo $text_year; ?>" id="input-tenure" class="form-control" maxlength="4" minlength="2" />
                  <?php if ($error_tenure) { ?>
                  <div class="text-danger"><?php echo $error_tenure; ?></div>
                  <?php } ?>
                </div>
                <div class="credit_tenure_month">
                  <select name="shop_since" class="form-control">
                    <option value=""><?php echo $text_month; ?></option>
                    <?php
                        foreach($months as $key=>$value){
                          if($key == $shop_since){
                            $selected = 'selected';
                          }else{
                            $selected = '';
                          }
                    ?>
                      <option value="<?php echo $key; ?>" <?php echo $selected; ?> ><?php echo $value; ?></option>
                    <?php }?>
                  </select>
                  <?php if ($error_shop_since) { ?>
                  <div class="text-danger"><?php echo $error_shop_since; ?></div>
                  <?php } ?>
                </div>
                <?php if (($error_tenure) && ($error_shop_since)) { ?>
                <div class="text-danger"></div>
                <?php } ?>
              </div>

            </div>
            <div class="form-group required">
              <div class="col-sm-3">
                <label class="control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
              </div>
              <div class="col-sm-6">
                <input type="text" name="telephone" value="<?php echo $telephone; ?>" id="input-telephone" class="form-control" />
                <?php if ($error_telephone) { ?>
                <div class="text-danger"><?php echo $error_telephone; ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group required">
              <div class="col-sm-3">
                <label class="control-label" for="input-location"><?php echo $entry_email; ?></label>
              </div>
              <div class="col-sm-6">
                <input type="email" name="email" value="<?php echo $email; ?>" id="input-email" class="form-control" />
                <?php if ($error_email) { ?>
                <div class="text-danger"><?php echo $error_email; ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group required">
              <div class="col-sm-3">
                <label class="control-label" for="input-location"><?php echo $entry_city; ?></label>
              </div>
              <div class="col-sm-6">
                <input type="text" name="city" value="<?php echo $city; ?>" id="input-city" class="form-control" />
                <?php if ($error_city) { ?>
                <div class="text-danger"><?php echo $error_city; ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group required">
              <div class="col-sm-3">
                <label class="control-label" for="input-location"><?php echo $entry_location; ?></label>
              </div>
              <div class="col-sm-6">
                <select name="location" id="input-location" class="form-control">
                  <option value=""><?php echo $text_select; ?></option>
                  <?php
                      foreach($all_state as $state_list){
                        foreach($state_list as $state){
                          if($state == $location){
                            $selected = 'selected';
                          }else{
                            $selected = '';
                          }
                  ?>
                    <option value="<?php echo $state; ?>" <?php echo $selected; ?> ><?php echo $state; ?></option>
                  <?php
                        }
                      }
                  ?>
                </select>
                <?php /* ?><!--<input type="text" name="location" value="<?php echo $location; ?>" id="input-location" class="form-control" >--><?php */ ?>
                <?php if ($error_location) { ?>
                <div class="text-danger"><?php echo $error_location; ?></div>
                <?php } ?>
              </div>
            </div>
          </fieldset>
          <div class="buttons">
            <div class="col-sm-3">
              <br />
            </div>
            <div class="col-sm-6">

              <input class="btn btn-primary" type="submit" value="<?php echo $button_submit; ?>" />

            </div>
          </div>
        </form>

      </div>
      <div><?php echo $text_bottom_message ;?></div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>