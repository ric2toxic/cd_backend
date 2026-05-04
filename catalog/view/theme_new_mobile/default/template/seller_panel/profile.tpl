<?php
  echo $header;
?>
<div id="page-wrapper">
  <div class="row">
    <div class="col-lg-12">
      <h1 class="page-header page_title">Profile</h1>
    </div>
  </div>
  <div class="col-sm-12">
    <div class="row profile_tabs">
      <ul class="nav nav-tabs order_tebination hidden-xs hidden-sm">
        <li class="active"><a href="#Basic" data-toggle="tab" aria-expanded="false">Basic</a></li>
        <li class=""><a href="#Business" data-toggle="tab" aria-expanded="false">Business</a></li>
        <!-- <li class=""><a href="#Agreement" data-toggle="tab" aria-expanded="false">Agreement</a></li> -->
      </ul>
      <div class="col-xs-12 hidden-md hidden-lg order_tab_dropdown">
        <select class="col-xs-12 col-sm-12 col-md-12 order_md_tab">
          <option value="basic">Basic</option>
          <option value="business">Business</option>
          <!-- <option value="saab">Agreement</option> -->
        </select>
      </div>
      <div class="clearfix"></div>
      <div class="col-sm-12 profile_detail">
        <div class="tab-content">
          <div id="Basic" class="tab-pane fade in active">
            <div class="panel panel-default row">
              <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i> Basic info </div>
              <!-- /.panel-heading -->
              <div class="panel-body ">
                <div class="business_form">
                  <input type="hidden" name = "seller_id" value ="<?php echo $data['seller_data']['seller_id']; ?>" >
                  <form class="form-inline">
                    <div class="form-group">
                      <label>Primary Person:</label>
                      <div class="btn-group pull-right">
                        <button type="button" class="btn btn-primary btn-xs form_edit" data-id="primary_contact_name">
                          <span class="fa fa-pencil"></span>
                        </button>
                        <button type="button" style="display:none" class="btn btn-primary btn-xs form-done primary_contact_name" onclick="inerstValue('primary_contact_name')" >
                          <span class="fa fa-floppy-o"></span>
                        </button>
                      </div>
                      <input type="text" class="form-control" id="primary_contact_name" name="primary_contact_name" disabled value="<?php echo $data['seller_data']['primary_contact_name']; ?>">
                      <input type="hidden" class="primary_contact_name" value="<?php echo $data['seller_data']['primary_contact_name']; ?>" >
                      
                    </div>
                    <div class="form-group">
                      <label>Primary Contact:</label>
                      <div class="btn-group pull-right">
                        <button type="button" class="btn btn-primary btn-xs form_edit" data-id="primary_contact_no">
                            <span class="fa fa-pencil"></span>
                        </button>
                        <button type="button" style="display:none" class="btn btn-primary btn-xs form-done primary_contact_no" onclick="inerstValue('primary_contact_no')" >
                            <span class="fa fa-floppy-o"></span>
                        </button>
                      </div>
                      <input type="text" class="form-control" id="primary_contact_no" name="primary_contact_no" disabled value="<?php echo $data['seller_data']['primary_contact_no']; ?>">
                      <input type="hidden" class="primary_contact_no" value="<?php echo $data['seller_data']['primary_contact_no']; ?>" >
                      <hr>
                    </div>
                    <div class="form-group">
                      <label>Email:</label>
                      <input type="text" class="form-control" disabled value="<?php echo $data['seller_data']['email']; ?>" >
                    </div>
                    <div class="form-group">
                        <hr>
                        <button type="button" style="display:none;float: right;" class="btn btn-primary btn-xs form-done additional_emails" onclick="inerstValue('additional_emails')" >
                            <span class="fa fa-floppy-o"></span>
                        </button>
                        <button type="button" style="float: right;" class="btn btn-primary btn-xs form_edit" data-id="additional_emails">
                            <span class="fa fa-pencil"></span>
                        </button>
                    </div>
                    <div class="form-group">
                      <label  class="col-sm-3 nopadding">Additional Emails:</label>
                      <textarea rows="5" cols="33" id="additional_emails" class="form-control" disabled ><?php echo $data['seller_data']['additional_emails']; ?>
                      </textarea>
                      <label>Please enter comma seperated additional emails. Do not use Space and enter. </label>
                      <input type="hidden" class="additional_emails" value="<?php echo $data['seller_data']['additional_emails']; ?>" >
                    </div>
                    <div class="form-group">
                        <hr>
                        <button type="button" style=" display:none;float: right;" class="btn btn-primary btn-xs form-done address1" onclick="updateSellerBankDetailsAndAddress('address_details')" >
                            <span class="fa fa-floppy-o"></span>
                        </button>
                        <button type="button" style="float: right;" class="btn btn-primary btn-xs"onclick="editAddressAndBankDetails();">
                            <span class="fa fa-pencil"></span>
                        </button>
                    </div>
                    <div class="form-group">
                      <label>Address:</label>
                      <textarea rows="5" cols="33" id="address1" disabled class="form-control"><?php echo $data['seller_data']['address1'] . ''. $data['seller_data']['address2']; ?></textarea>
                      <input type="hidden" class="address1" value="<?php echo $data['seller_data']['address1'] .''. $data['seller_data']['address2']; ?>" >
                    </div>
                    <div class="form-group">
                        <label>City :</label>
                        <input class="form-control" id="city"  disabled value="<?php echo $data['seller_data']['city']; ?>">
                        <input type="hidden" class="city" value="<?php echo $data['seller_data']['city']; ?>" >
                    </div>
                    <div class="form-group">
                        <label>Pincode :</label>
                        <input class="form-control" id="pincode" maxlength="6" disabled value="<?php echo $data['seller_data']['pincode']; ?>">
                        <input type="hidden" class="pincode" value="<?php echo $data['seller_data']['pincode']; ?>" >
                    </div>
                    <div class="form-group">
                      <label>State:</label>
                      <select disabled class="form-control" id="zone_id" name="seller_zone">
                          <?php foreach($zones as $values){
                              if($data['seller_data']['zone_id'] == $values['zone_id']){
                                $selected = "selected";
                              } else{
                                $selected = '';
                              }
                            ?>
                            <option value="<?php echo $values['zone_id']; ?>" <?php echo $selected ;?>><?php echo $values['name']; ?></option>
                          <?php } ?>
                      </select>
                      <input type="hidden" class="zone_id" value="<?php echo $data['seller_data']['zone_id']; ?>" >
                    </div>
                    <div class="form-group">
                      <label>Country</label>
                      <select class="form-control"  disabled name="seller_country" class="form-control input-box-disabled" id="country_id">
                         <option value="99" selected="selected">India</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <hr>
                      <label>
                        <input type="checkbox" id="same_as_primary_pickup" <?php if($data['seller_data']['same_as_primary_pickup'] == 1){ echo 'checked';}?> onclick="sameAsPrimary('same_as_primary_pickup')">  
                        <input type="hidden" class="same_as_primary_pickup" value="<?php echo $data['seller_data']['same_as_primary_pickup']; ?>" >
                        <span class="nopadding" style="vertical-align: top">Pickup(Same as primary)</span>
                      </label>                      
                    </div>
                    <div class="form-group">
                      <label>Pickup Person:</label>
                      <div class="btn-group pull-right">
                        <?php 
                          if($data['seller_data']['same_as_primary_pickup'] == 1){
                            $style = 'style="display:none"';
                          } else {
                            $style = '';
                          }
                        ?>
                        <button type="button" <?php echo $style;?> class="btn btn-primary btn-xs form_edit" data-id="pickup_holder_name">
                          <span class="fa fa-pencil"></span>
                        </button>
                        <button type="button" style="display:none" class="btn btn-primary btn-xs form-done pickup_holder_name" onclick="inerstValue('pickup_holder_name')" >
                          <span class="fa fa-floppy-o"></span>
                        </button>
                      </div>
                      <input type="text"  class="form-control" id="pickup_holder_name" name="pickup_holder_name" disabled value="<?php echo $data['seller_data']['pickup_holder_name']; ?>">
                      <input type="hidden" class="pickup_holder_name" value="<?php echo $data['seller_data']['pickup_holder_name']; ?>" >
                    </div>
                    <div class="form-group">
                      <label>Pickup Contact:</label>
                      <div class="btn-group pull-right">
                        <button type="button" <?php echo $style;?> class="btn btn-primary btn-xs form_edit" data-id="pickup_contact_no">
                          <span class="fa fa-pencil"></span>
                        </button>
                        <button type="button" style="display:none" class="btn btn-primary btn-xs form-done pickup_contact_no" onclick="inerstValue('pickup_contact_no')" >
                          <span class="fa fa-floppy-o"></span>
                        </button>
                      </div>
                      <input type="text" class="form-control" id="pickup_contact_no" name="Contact number" disabled value="<?php echo $data['seller_data']['pickup_contact_no']; ?>">
                      <input type="hidden" class="pickup_contact_no" value="<?php echo $data['seller_data']['pickup_contact_no']; ?>" >
                      <hr>
                    </div>
                    <div class="form-group">
                      <label>
                        <input type="checkbox" id="same_as_primary_address" <?php if($data['seller_data']['same_as_primary_address'] == 1){ echo 'checked';}?> onclick="sameAsPrimary('same_as_primary_address')">
                        <input type="hidden" class="same_as_primary_address" value="<?php echo $data['seller_data']['same_as_primary_address']; ?>" >
                        <span class="nopadding" style="vertical-align: top">Pickup Address(Same as primary)</span>
                      </label>
                      <div class="btn-group pull-right">
                        <?php 
                          if($data['seller_data']['same_as_primary_address'] == 1){
                            $address_edit_button_style = 'style="display:none; float:right"';
                          } else {
                            $address_edit_button_style = 'style="float:right"';
                          }
                        ?>
                        <button type="button" style=" display:none;float: right;" class="btn btn-primary btn-xs form-done pickup_address" onclick="updateSellerBankDetailsAndAddress('pickup_address')" >
                          <span class="fa fa-floppy-o"></span>
                        </button>
                        <button type="button" <?php echo $address_edit_button_style; ?> class="btn btn-primary  btn-xs" data-id="pickup_address" onclick="editAddressAndBankDetails('pickup_address');" >
                              <span class="fa fa-pencil"></span>
                          </button>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Pickup Address:</label>
                      <textarea rows="5" cols="33" id="pickup_address" class="form-control" disabled><?php echo $data['seller_data']['pickup_address']; ?></textarea>
                      <input type="hidden" class="pickup_address" value="<?php echo $data['seller_data']['pickup_address']; ?>" >
                    </div>
                    <div class="form-group">
                        <label>pickup City :</label>
                        <input class="form-control" id="pickup_city"  disabled value="<?php echo $data['seller_data']['pickup_city']; ?>">
                        <input type="hidden" class="pickup_city" value="<?php echo $data['seller_data']['pickup_city']; ?>" >
                    </div>
                    <div class="form-group">
                        <label>Pickup Pincode :</label>
                        <input class="form-control" id="pickup_pincode" maxlength="6"  disabled value="<?php echo $data['seller_data']['pickup_pincode']; ?>">
                        <input type="hidden" class="pickup_pincode" value="<?php echo $data['seller_data']['pickup_pincode']; ?>" >
                    </div>
                    <div class="form-group">
                        <label>pickup State:</label>
                        <select disabled class="form-control" id="pickup_zone_id"  name="pickup_zone_id">
                          <?php foreach($zones as $values){
                              if($data['seller_data']['pickup_zone_id'] == $values['zone_id']){
                                $selected = "selected";
                              } else{
                                $selected = '';
                              }
                            ?>
                            <option value="<?php echo $values['zone_id']; ?>" <?php echo $selected ;?>><?php echo $values['name']; ?></option>
                          <?php } ?>
                        </select>
                        <input type="hidden" class="pickup_zone_id" value="<?php echo $data['seller_data']['pickup_zone_id']; ?>" >
                    </div>
                    <div class="form-group">
                      <label>pickup Country :</label>
                      <select class="form-control"  disabled name="seller_country" class="form-control" id="pickup_country_id">
                         <option value="99" selected="selected">India</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <hr>
                      <label>
                        <input type="checkbox"id="same_as_primary_account" <?php if($data['seller_data']['same_as_primary_account'] == 1){ echo 'checked';}?>   onclick="sameAsPrimary('same_as_primary_account')">
                        <input type="hidden" class="same_as_primary_account" value="<?php echo $data['seller_data']['same_as_primary_account']; ?>" >
                        <span class="nopadding" style="vertical-align: top">Account(Same as primary)</span>
                      </label>
                    </div>
                    <div class="form-group">
                      <label>Account Person:</label>
                      <div class="btn-group pull-right">
                        <?php 
                          if($data['seller_data']['same_as_primary_account'] == 1){
                            $style = 'style="display:none"';
                          } else{
                            $style = '';
                          }
                        ?>
                        <button type="button" <?php echo $style;?> class="btn btn-primary btn-xs form_edit" data-id="account_holder_name">
                          <span class="fa fa-pencil"></span>
                        </button>
                        <button type="button" style="display:none" class="btn btn-primary btn-xs form-done account_holder_name" onclick="inerstValue('account_holder_name')" >
                          <span class="fa fa-floppy-o"></span>
                        </button>
                      </div>
                      <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" disabled value="<?php echo $data['seller_data']['account_holder_name']; ?>">
                      <input type="hidden" class="account_holder_name" value="<?php echo $data['seller_data']['account_holder_name']; ?>" >
                    </div>
                    <div class="form-group">
                      <label>Account Contact:</label>
                      <div class="btn-group pull-right">
                        <button type="button" <?php echo $style;?>  class="btn btn-primary btn-xs form_edit" data-id="account_contact_no">
                          <span class="fa fa-pencil"></span>
                        </button>
                        <button type="button" style="display:none" class="btn btn-primary btn-xs form-done account_contact_no" onclick="inerstValue('account_contact_no')" >
                          <span class="fa fa-floppy-o"></span>
                        </button>
                      </div>
                      <input type="text" class="form-control" id="account_contact_no" name="account_contact_no" disabled value="<?php echo $data['seller_data']['account_contact_no']; ?>">
                      <input type="hidden" class="account_contact_no" value="<?php echo $data['seller_data']['account_contact_no']; ?>" >
                      <hr>
                    </div>
                    <div class="form-group">
                      <label>
                        <input type="checkbox" id="same_as_primary_inventory" <?php if($data['seller_data']['same_as_primary_inventory'] == 1){ echo 'checked';}?> onclick="sameAsPrimary('same_as_primary_inventory')">
                        <input type="hidden" class="same_as_primary_inventory" value="<?php echo $data['seller_data']['same_as_primary_inventory']; ?>" >
                        <span class="nopadding" style="vertical-align: top">Inventory(Same as primary)</span>
                      </label>
                    </div>
                    <div class="form-group">
                      <label>Inventory Person:</label>
                      <div class="btn-group pull-right">
                        <?php 
                          if($data['seller_data']['same_as_primary_inventory'] == 1){
                            $style = 'style="display:none"';
                          } else {
                            $style = '';
                          }
                        ?>
                        <button type="button" <?php echo $style; ?> class="btn btn-primary btn-xs form_edit" data-id="inventory_holder_name">
                          <span class="fa fa-pencil"></span>
                        </button>
                        <button type="button" style="display:none" class="btn btn-primary btn-xs form-done inventory_holder_name" onclick="inerstValue('inventory_holder_name')" >
                          <span class="fa fa-floppy-o"></span>
                        </button>
                      </div>
                      <input type="text" class="form-control" id="inventory_holder_name" name="inventory_holder_namer" disabled value="<?php echo $data['seller_data']['inventory_holder_name']; ?>">
                      <input type="hidden" class="inventory_holder_name" value="<?php echo $data['seller_data']['inventory_holder_name']; ?>" >
                    </div>
                    <div class="form-group">
                      <label>Inventory Contact:</label>
                      <div class="btn-group pull-right">
                        <button type="button" <?php echo $style; ?> class="btn btn-primary btn-xs form_edit" data-id="inventory_contact_no">
                          <span class="fa fa-pencil"></span>
                        </button>
                        <button type="button" style="display:none" class="btn btn-primary btn-xs form-done inventory_contact_no" onclick="inerstValue('inventory_contact_no')" >
                          <span class="fa fa-floppy-o"></span>
                        </button>
                      </div>
                      <input  type="text" class="form-control" id="inventory_contact_no" name="inventory_contact_no" disabled value="<?php echo $data['seller_data']['inventory_contact_no']; ?>">
                      <input type="hidden" class="inventory_contact_no" value="<?php echo $data['seller_data']['inventory_contact_no']; ?>" >
                    </div>
                  </form>
                </div>
              </div>
              <!-- /.panel-body -->
            </div>
          </div>
          <div id="Business" class="tab-pane fade">
            <div class="panel panel-default row">
              <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Business info
              </div>
              <!-- /.panel-heading -->
              <div class="panel-body">
                <div class="business_form clearfix">
                  <div class="form-inline">
                    <div class="form-group">
                      <label>Company Name:</label>
                      <div class="btn-group pull-right">
                        <button type="button" class="btn btn-primary btn-xs form_edit" data-id="company">
                          <span class="fa fa-pencil"></span>
                        </button>
                        <button type="button" style="display:none" class="btn btn-primary btn-xs form-done company" onclick="inerstValue('company')" >
                          <span class="fa fa-floppy-o"></span>
                        </button>
                      </div>
                      <input type="text" class="form-control" id="company" name="company" disabled value="<?php echo $data['seller_data']['company']; ?>">
                      <input type="hidden" class="company" value="<?php echo $data['seller_data']['company']; ?>" >
                    </div>
                    <div class="form-group">
                      <label>Nickname:</label>
                      <input type="text" class="form-control" id="nickname" name="nickname" disabled value="<?php echo $data['seller_data']['nickname'] ?>">
                      <input type="hidden" class="nickname" value="<?php echo $data['seller_data']['nickname'] ?>">
                    </div>
                    <div class="form-group">
                      <hr>
                      <div id="pan-alert-message"></div>
                    </div>
                    <div class="form-group">
                      <label>Pan Number:</label>
                      <div class="btn-group pull-right">
                        <button type="button" style="display:none;float: right;" class="btn btn-primary btn-xs form-done pan" onclick="inerstValue('pan')" >
                          <span class="fa fa-floppy-o"></span>
                        </button>
                        <button type="button" style="float: right;" class="btn btn-primary btn-xs form_edit" data-id="pan">
                            <span class="fa fa-pencil"></span>
                        </button>
                      </div>
                      <input type="text" class="form-control" id="pan" onblur="enabledInputTypeFile('pan')" name="pan" disabled value="<?php echo $data['seller_data']['pan']; ?>">
                      <input type="hidden" class="pan" value="<?php echo $data['seller_data']['pan']; ?>" >
                    </div>
                    <div class="form-group upload_pan_number">
                      <label>Upload Pan Image:</label>
                      <form id="img_form" >
                        <input type="file" id="pan_upload" disabled name="pan_upload" >
                        <input type="hidden" id="pan_image" value= "<?php echo $data['seller_data']['pan_image']; ?>">
                      </form>
                    </div>
                    <div class="upload_pan_number_error"></div>
                    <div class="form-group">
                      <hr>
                      <div id="tin-alert-message"></div>
                      <?php
                        $tin_type = explode(",",$data['seller_data']['tin_tax_type']);
                      ?>
                      <div class="form-group">
                        <label>Type Of Goods:</label>
                        <label>
                          <input type="checkbox" id="tin_tax_free"<?php if(in_array('1',$tin_type)){ echo "checked";}?> value="1" class="tin_taxs_checkbox"> Tax Free
                          <input type="checkbox" id="tin_taxable" <?php if(in_array('2',$tin_type)){ echo "checked";}?> value="2" class="tin_taxs_checkbox"> Taxable
                          <input type="checkbox" id="tin_composite" <?php if(in_array('3',$tin_type)){ echo "checked";}?> value="3" class="tin_taxs_checkbox"> Composite
                        </label>
                      </div>
                      <?php
                        if(in_array('1',$tin_type) && !in_array('2',$tin_type) && !in_array('3',$tin_type)) {
                            $style = "display:none;";
                        } else {
                          $style = '';
                        }
                      ?>
                      <input type="hidden" id="old_tin_tax_type" value="<?php echo $data['seller_data']['tin_tax_type']; ?>">
                    </div>
                    <?php
                      if(in_array('1',$tin_type) && !in_array('2',$tin_type) && !in_array('3',$tin_type)) {
                          $style = "style= display:none;";
                      } else{
                        $style = '';
                      }
                    ?>
                    <div id="tin_block" class="<?php echo $style;?>">
                      <div class="form-group">
                        <label>Tin Number:</label>
                        <div class="btn-group pull-right">
                          <button type="button" style="display:none;float: right;" class="btn btn-primary btn-xs form-done tin" onclick="inerstValue('tin')" >
                            <span class="fa fa-floppy-o"></span>
                          </button>
                          <button type="button" style="float: right;<?php echo $style;?>" class="btn btn-primary btn-xs form_edit" data-id="tin">
                            <span class="fa fa-pencil"></span>
                          </button>
                        </div>
                        <input type=text  class="form-control" id="tin" onblur="enabledInputTypeFile('tin')" name="tin" disabled value="<?php echo $data['seller_data']['tin']; ?>">
                        <input type="hidden" class="tin" value="<?php echo $data['seller_data']['tin']; ?>" >
                      </div>
                      <div class="form-group upload_tin_number">
                        <label>Upload Tin:</label>
                        <input type="file" id="tin_upload" disabled>
                        <input type="hidden" id="tin_image" value= "<?php echo $data['seller_data']['tin_image']; ?>">
                      </div>
                      <div class="upload_tin_number_error col-sm-6" style="padding-right:0px;"></div>
                    </div>
                  </div>
                  <hr>
                </div>
                  <fieldset>
                    <legend>Bank Details</legend>
                    <div id="bank_details-alert-message"></div>
                    <div class="btn-group pull-right">
                      <button type="button" style="display:none;float: right;"class="btn btn-primary btn-xs form-done bank_details" onclick="updateSellerBankDetailsAndAddress('bank_details')" >
                          <span class="fa fa-floppy-o"></span>
                      </button>
                      <button type="button" style="float: right;" class="btn btn-primary btn-xs"  onclick="editAddressAndBankDetails('bank_details')" >
                          <span class="fa fa-pencil"></span>
                      </button>
                    </div>
                    <div class="business_form clearfix">
                      <div class="form-group">
                        <label>Account Holder Name:</label>
                        <input type="text" class="form-control" id="bank_ac_holder_name" name="bank_ac_holder_name" disabled value="<?php echo $data['seller_data']['bank_ac_holder_name']; ?>">
                        <input type="hidden" class="bank_ac_holder_name" value="<?php echo $data['seller_data']['bank_ac_holder_name']; ?>" >
                        </div>
                      </div>
                      <div class="form-group">
                        <label>Account Number:</label>
                        <input type="text" class="form-control" id="bank_ac_number" name="bank_ac_number" onblur="enabledInputTypeFile('acnt')" disabled value="<?php echo $data['seller_data']['bank_ac_number']; ?>">
                        <input type="hidden" class="bank_ac_number" value="<?php echo $data['seller_data']['bank_ac_number']; ?>" >
                      </div>
                      <div class="form-group upload_cancel_cheque">
                        <label>Upload Cancel Cheque:</label>
                        <input type="file" disabled id="upload_cancel_cheque" >
                        <input type="hidden" id="cancel_cheque_image" value= "<?php echo $data['seller_data']['cancel_cheque_image'] ?>">
                      </div>
                      <div class="upload_cancel_cheque_error col-sm-6 nopadding">
                      </div>
                      <div class="form-group">
                        <label>IFSC Code:</label>
                        <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" disabled value="<?php echo $data['seller_data']['ifsc_code']; ?>">
                        <!--<input type="hidden" class="ifsc_code" value="<?php echo $data['seller_data']['ifsc_code']; ?>" >
                        <input type="hidden" class="form-control" name="bank_name">
                        <input type="hidden" class="form-control" name="bank_branch" >
                        <input type="hidden" class="form-control" name="bank_city" >
                        <input type="hidden" class="form-control" name="bank_state" > -->
                      </div>
                      <div class="col-sm-6 clearfix">
                        <span style="float:right;color:#337ab7;" id="ifsc_code_loading"></span>
                      </div>
                      <div class="form-group">
                          <div class="col-sm-10">
                              <div class="bank_details_block" style="display: none;"></div>
                          </div>
                      </div>
                  </fieldset>
              </div>
              <!-- /.panel-body -->
            </div>
          </div>
          <div id="Agreement" class="tab-pane fade">
            <div class="panel panel-default row">
                  <div class="panel-heading">
                      <i class="fa fa-bar-chart-o fa-fw"></i> Agreement info
                      <div class="pull-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-xs form_edit">
                                <span class="fa fa-pencil"></span>
                            </button>
                            <button type="button" class="btn btn-primary btn-xs form-done">
                                <span class="fa fa-floppy-o"></span>
                            </button>
                        </div>
                      </div>
                  </div>
                  <!-- /.panel-heading -->
                  <div class="panel-body">
                      <div>
                          <p>
                              Lorem ipsum dolor sit amet, consectetur adipiscing elit.  Vestibulum tincidunt est vitae ultrices accumsan. Aliquam ornare lacus adipiscing, posuere lectus et, fringilla augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit.  Vestibulum tincidunt est vitae ultrices accumsan. Aliquam ornare lacus adipiscing, posuere lectus et, fringilla augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit.  Vestibulum tincidunt est vitae ultrices accumsan. Aliquam ornare lacus adipiscing, posuere lectus et, fringilla augue.
                          </p>
                      </div>
                  </div>
                  <!-- /.panel-body -->
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.row -->
</div>
<!-- /#page-wrapper -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content col-sm-7">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Update Password </h4>
      </div>
      <div class="modal-body" style="max-height:450px;overflow-y:auto">
        <fieldset>
            <div class="form-group clearfix">
                <label class="col-sm-4 control-label nopadding" >Password</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="password"   placeholder="Password">
                </div>
            </div>
            <div class="form-group clearfix">
                <label class="col-sm-4 control-label nopadding">Confirm Password</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="confirm_password" id="confirm_password"  placeholder="Confirm Password">
                </div>
            </div>
          </fieldset>
      </div>
      <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="updateSellerPassword" onclick="updateSellerPassword()">Submit</button>
      </div>
    </div>
  </div>
</div>
<?php
  echo $footer;
?>
<script type="text/javascript">
    $(document).ready(function(){
        $('.form_edit').on('click',function(e){
            var id = $(this).attr('data-id');
            $('#'+id).removeClass('input-box-disabled');
            $('#'+id).removeAttr('disabled');
            $("."+id).show();
        });
        $("#ifsc_code").on('keyup',function(e){
            var filter = /^([a-zA-Z0-9]){1}$/;
            if($("#ifsc_code").val().length < 11){
                $(".bank_details").attr('disabled','disabled');
                $('.bank_details_block').hide();
                $('.bank_details_block').html('<span class="alert alert-danger" seller-ifsc">IFSC Code Not Proper</span>');
                $('.bank_details_block').css("text-align","center");
                $('.bank_details_block').show();
            } else{
                $('.bank_details_block').find('.alert-danger').remove();
            }
            if (filter.test(e.key)){
                $('.bank_details_block').find('.alert alert-danger').remove();
                getIfscCodeDetails(this);
            }
        });
    function getPincode(pincode,id){
        $.ajax({
            type:'post',
            dataType:'json',
            data:"pincode="+pincode,
            url:'index.php?route=seller_panel/profile/getState',
            success:function(response){
                if(response.state !='empty'){
                  $("#"+id).val(response.zone_id);
              } else{
                  $("#"+id).find('option[value=""]').remove();
                  $("#"+id).removeAttr('disabled');
                  var html = "<option value=''>--Select--</option>";
                  $("#"+id).prepend(html);
                  $("#"+id).val('');
              }
            },
        });
    }
        $("#pincode").on('keyup',function(){
            if($("#pincode").val().length == 6){
              var pincode = $("#pincode").val();
              var CheckZipCode = /(^\d{6}$)/;
              if((!CheckZipCode.test(pincode))){
                  alert("Pincode is invalid");
                  return false;
              }
              getPincode(pincode,'zone_id');
            }
        });
        $("#pickup_pincode").on('keyup',function(){
            if($("#pickup_pincode").val().length == 6){
              var pincode = $("#pickup_pincode").val();
              var CheckZipCode = /(^\d{6}$)/;
              if((!CheckZipCode.test(pincode))){
                  alert("Pincode is invalid");
                  return false;
              }
              getPincode(pincode,'pickup_zone_id');
            }
        });

        $('.tin_taxs_checkbox').on('click',function(){
          if($("#tin_tax_free").prop("checked") == true  && $("#tin_taxable").prop("checked") == false && $("#tin_composite").prop("checked") == false ) {
            var seller_id        = $("input[name = 'seller_id']").val();
            var old_tin_tax_type = $("#old_tin_tax_type").val();
            $('#tin_block').hide();
            $('button.tin').hide();
            $("button[data-id=\'tin\']").hide();
            $.ajax({
              type:'post',
              url:'index.php?route=seller_panel/profile/updateTinType',
              data: "tin_type=1"+"&seller_id="+seller_id+"&old_tin_tax_type="+old_tin_tax_type,
              async: false,
                success: function(response) {
                },
            });
          }
          else{
            $('#tin_block').show();
            $("button[data-id=\'tin\']").show();
          }
        });

        function getIfscCodeDetails(obj){
            var ifsc_code = $(obj).val().toUpperCase();
            if(ifsc_code.length == 11){
                $.ajax({
                    //url: 'https://ifsc.razorpay.com/'+ifsc_code,
                    url : 'index.php?route=seller_panel/profile/bankDetails&ifsc_code='+ifsc_code ,
                    // dataType : 'json',
                    beforeSend: function() {
                        $("#ifsc_code_loading").button('loading');
                        $('#ifsc_code_loading').removeClass('hidden');
                    },
                    complete: function() {
                        $('#ifsc_code_loading').button('reset');
                        $('#ifsc_code_loading').addClass('hidden');
                    },
                    success:function(json){
                        $('.seller-ifsc').hide();
                        if(typeof(json) == 'string'){
                          json = JSON.parse(json);
                          if(json == 'Not Found'){
                            html = '<span class="alert alert-danger seller-ifsc">IFSC Code Not Found</span>';
                            $('.bank_details_block').html(html);
                            $('.bank_details_block').show();
                            $('.bank_details_block').css("text-align","center");
                            $('.bank_details').attr('disabled',true);
                            return false;
                          }
                            $(".seller-ifsc").remove();
                            html = '<table class="table table-responsive table-bordered">';
                            html += '<caption align="center"><h4>Bank Detail</h4></caption>';
                            html +=     '<tr>';
                            html +=         '<td><b>Bank Name: </b></td>';
                            html +=         '<td>'+json['BANK']+'</td>';
                            html +=     '</tr>';
                            html +=     '<tr>';
                            html +=         '<td><b>Branch: </b></td>';
                            html +=         '<td>'+json['BRANCH']+'</td>';
                            html +=     '</tr>';
                            html +=     '<tr>';
                            html +=         '<td><b>Address: </b></td>';
                            html +=         '<td>'+json['ADDRESS']+'</td>';
                            html +=     '</tr>';
                            html +=     '<tr>';
                            html +=         '<td><b>Place: </b></td>';
                            html +=         '<td>'+json['DISTRICT']+'<br> '+json['CITY']+', '+json['STATE']+'</td>';
                            html +=     '</tr>';
                            html += '</table>';

                            $('.bank_details_block').html(html);
                            $('.bank_details_block').show();
                            $('input[name=\'bank_name\']').val(json['BANK']);
                            $('input[name=\'bank_branch\']').val(json['BRANCH']);
                            $('input[name=\'bank_city\']').val(json['CITY']);
                            $('input[name=\'bank_state\']').val(json['STATE']);
                            $('.bank_details').removeAttr('disabled',true);
                            validateBankDetails(false);
                        }else{

                            $('.bank_details').attr('disabled',true);
                            $('.error-ms-bank').remove();
                            $('.bank_details_block').html('');
                        }
                    },
                });
            }else if(ifsc_code.length == 0){
                validateBankDetails(false);
                $('.bank_details_block').html('');

            }
            else{
                $('.bank_details').attr('disabled',true);
                $('.bank_details_block').html('');
            }
        }

        $(function(){
            getIfscCodeDetails('input[name=\'ifsc_code\']');
        });

        function validateBankDetails(callgetifsc = true){
            var ac_name = $("#bank_ac_holder_name").val();
            var ac_no = $('#bank_ac_number').val().trim();
            var ifsc = $('#ifsc_code').val().trim();
            var error = [];
            if(ac_name.length == 0){
                error.push("error");

            }
            if(ac_no.length == 0 ){
                error.push("error");
            }
            if(ifsc.length == 0 ){
                error.push("error");
            }
            if(error.length > 0 && error.length < 3){
               //alert(error.length);
               $('.bank_details').attr('disabled',true);
            }
            else{
               $('.bank_details').attr('disabled',false);
               if(callgetifsc){
                 getIfscCodeDetails('input[name=\'ifsc_code\']');
               }
            }
        }

    });

    function enabledInputTypeFile(type){
      if(type == "pan"){
        var update_value = $("#pan").val();
        var old_value    = $("input.pan").val();
        if(update_value != old_value){
          $("#pan_upload").removeAttr('disabled');
        } else{
          $("#pan_upload").attr('disabled','disabled');
        }
      } else if (type == "tin"){
        var update_value = $("#tin").val();
        var old_value    = $("input.tin").val();
        if(update_value != old_value){
          $("#tin_upload").removeAttr('disabled');
        } else{
          $("#tin_upload").attr('disabled','disabled');
        }
      } else{
        var update_value = $("#bank_ac_number").val();
        var old_value    = $("input.bank_ac_number").val();
        if(update_value != old_value){
          $("#upload_cancel_cheque").removeAttr('disabled');
        } else{
          $("#upload_cancel_cheque").attr('disabled','disabled');
        }
      }
    }

    function editAddressAndBankDetails(type){
      if(type == "bank_details"){
        $(".upload_cancel_cheque_error").find(".alert-danger").remove();
        $(".bank_details").show();
        $("#bank_ac_holder_name").removeClass('input-box-disabled');
        $("#bank_ac_holder_name").removeAttr('disabled');
        $("#bank_ac_number").removeClass('input-box-disabled');
        $("#bank_ac_number").removeAttr('disabled');
        $("#ifsc_code").removeClass('input-box-disabled');
        $("#ifsc_code").removeAttr('disabled');
        return false;
      } else if (type == "pickup_address") {
        $(".pickup_address").show();
        $("#pickup_address").removeClass('input-box-disabled');
        $("#pickup_address").removeAttr('disabled');
        $('#pickup_pincode').removeClass('input-box-disabled');
        $('#pickup_pincode').removeAttr('disabled');
        $('#pickup_city').removeClass('input-box-disabled');
        $('#pickup_city').removeAttr('disabled');
      } else{
        $(".address1").show();
        $("#address1").removeAttr('disabled');
        $('#pincode').removeClass('input-box-disabled');
        $('#pincode').removeAttr('disabled');
        $('#city').removeClass('input-box-disabled');
        $('#city').removeAttr('disabled');
      }
    }
    function updateSellerBankDetailsAndAddress(type){
      var form_data = new FormData();
      var seller_id          = $("input[name = 'seller_id']").val();
      if(type == "bank_details"){
        var bank_ac_holder_name= $("#bank_ac_holder_name").val();
        var old_bank_ac_holder_name = $("input.bank_ac_holder_name").val();
        var bank_ac_number     = $("#bank_ac_number").val();
        var old_bank_ac_number = $("input.bank_ac_number").val();
        var cancel_img_path    = $("#cancel_cheque_image").val();
        var seller_nickname    =  $("input.nickname").val();
        var ifsc_code          = $("#ifsc_code").val();
        var old_ifsc_code      = $("input.ifsc_code").val();
        var file_data          = $('#upload_cancel_cheque').prop('files')[0];
        if(bank_ac_holder_name == '' || bank_ac_number == '' || ifsc_code == '' ){
          alert("please fill the empty field");
          return false;
        }
        if( bank_ac_holder_name == old_bank_ac_holder_name && bank_ac_number == old_bank_ac_number && ifsc_code == old_ifsc_code){
          $("#bank_details-alert-message").find(".alert-info").remove();
          $(".bank_details").hide();
          $("#bank_ac_holder_name").attr('disabled','disabled');
          $("#bank_ac_holder_name").addClass('input-box-disabled');
          $("#bank_ac_number").addClass('input-box-disabled');
          $("#bank_ac_number").attr('disabled','disabled');
          $("#ifsc_code").attr('disabled','disabled');
          $("#ifsc_code").addClass('input-box-disabled');
          $(".upload_cancel_cheque_error").find(".alert-danger").remove();
          return false;
        }
        if(typeof file_data  === "undefined"){
          $(".upload_cancel_cheque_error").find(".alert-danger").remove();
          $(".upload_cancel_cheque_error").append('<span class="alert alert-danger" style="float:right;margin-right: 46px;">please choose file</span>');
          return false;
        }else{
          $(".upload_cancel_cheque_error").find(".alert-danger").remove();
        }
        if(ifsc_code.trim().length != 11){
          alert('Please enter correct Ifsc code !!');
          return false;
        }
        form_data.append('bank_ac_holder_name', bank_ac_holder_name);
        form_data.append('old_bank_ac_holder_name', old_bank_ac_holder_name);
        form_data.append('bank_ac_number', bank_ac_number);
        form_data.append('old_bank_ac_number', old_bank_ac_number);
        form_data.append('ifsc_code', ifsc_code);
        form_data.append('old_ifsc_code', old_ifsc_code);
        form_data.append('seller_id', seller_id);
        form_data.append('type', type);
        form_data.append('img', cancel_img_path);
        form_data.append('upload_file', file_data);
        form_data.append('seller_nickname', seller_nickname);
      } else if (type == 'pickup_address') {
          var pickup_address     = $("#pickup_address").val();
          var old_pickup_address = $("input.pickup_address").val();
          var pickup_city        = $("#pickup_city").val();
          var old_pickup_city        = $("input.pickup_city").val();
          var pickup_pincode     = $("#pickup_pincode").val();
          var CheckZipCode = /(^\d{6}$)/;
          var old_pickup_pincode = $("input.pickup_pincode").val();
          var pickup_zone_id     = $("#pickup_zone_id").val();
          var old_pickup_zone_id = $("input.pickup_zone_id").val();
          if(pickup_address == '' || pickup_city == '' || pickup_pincode == ''){
            alert("Please fill the empty field");
            return false;
          }
          if(pickup_zone_id == ''){
              alert("Please select state");
              return false;
          }
          if((!CheckZipCode.test(pickup_pincode))){
              alert("Pincode is invalid");
              return false;
          }
          if(pickup_address == old_pickup_address && pickup_city == old_pickup_city && pickup_pincode == old_pickup_pincode && pickup_zone_id == old_pickup_zone_id){
            $(".pickup_address").hide();
            $("#pickup_address").attr('disabled','disabled');
            $("#pickup_pincode").addClass('input-box-disabled');
            $("#pickup_city").addClass('input-box-disabled');
            $("#pickup_city").attr('disabled','disabled');
            $("#pickup_zone_id").attr('disabled','disabled');
            return false;
          }
          form_data.append('pickup_address', pickup_address);
          form_data.append('old_pickup_address', old_pickup_address);
          form_data.append('pickup_city', pickup_city);
          form_data.append('old_pickup_city', old_pickup_city);
          form_data.append('pickup_pincode', pickup_pincode);
          form_data.append('old_pickup_pincode', old_pickup_pincode);
          form_data.append('pickup_zone_id', pickup_zone_id);
          form_data.append('old_pickup_zone_id', old_pickup_zone_id);
          form_data.append('seller_id', seller_id);
          form_data.append('type', type);
          var data = form_data;
      } else{
        var address     = $("#address1").val();
        var old_address = $("input.address1").val();
        var seller_city        = $("#city").val();
        var old_seller_city    = $("input.city").val();
        var pincode            = $("#pincode").val();
        var CheckZipCode = /(^\d{6}$)/;
        var old_pincode        = $("input.pincode").val();
        var seller_zone_id     = $("#zone_id").val();
        var old_seller_zone_id = $("input.zone_id").val();
        if(address == '' || seller_city == '' || pincode == ''){
          alert("please fill the empty field");
          return false;
        }
        if(seller_zone_id == ''){
            alert("please select state");
            return false;
        }
        if((!CheckZipCode.test(pincode))){
            alert("Pincode is invalid");
            return false;
        }
        if(seller_city.length > 64 ){
          alert('City must be between 1 and 64 characters!');
          return false;
        }
        if(address == old_address && seller_city==old_seller_city && pincode==old_pincode && seller_zone_id == old_seller_zone_id){
          $(".address1").hide();
          $("#address1").attr('disabled','disabled');
          $("#pincode").addClass('input-box-disabled');
          $("#pincode").attr('disabled','disabled');
          $("#city").addClass('input-box-disabled');
          $("#city").attr('disabled','disabled');
          $("#zone_id").attr('disabled','disabled');
          return false;
        }
        form_data.append('address', address);
        form_data.append('old_address', old_address);
        form_data.append('seller_city', seller_city);
        form_data.append('old_seller_city', old_seller_city);
        form_data.append('pincode', pincode);
        form_data.append('old_pincode', old_pincode);
        form_data.append('seller_zone_id', seller_zone_id);
        form_data.append('old_seller_zone_id', old_seller_zone_id);
        form_data.append('seller_id', seller_id);
        form_data.append('type', type);
        var data = form_data;
      }
      $.ajax({
          type:"post",
          data: form_data,
          dataType:'json',
          url:'index.php?route=seller_panel/profile/updateSellerBankDetailsAndAddress',
          async: false,
          mimeType: "form-data",
          contentType: false,
          processData: false,
          success: function(response) {
            if(response.valid == "not vaild"){
              alert(response.message);
              return false;
            }
            else if(response.msg == 'update seller bank details'){
              $("input.bank_ac_holder_name").val(bank_ac_holder_name);
              $("input.bank_ac_number").val(bank_ac_number);
              $("input.ifsc_code").val(ifsc_code);
              $("#cancel_cheque_image").val(response.data);
              $(".bank_details").hide();
              $("#bank_ac_holder_name").attr('disabled','disabled');
              $("#bank_ac_holder_name").addClass('input-box-disabled');
              $("#bank_ac_number").addClass('input-box-disabled');
              $("#bank_ac_number").attr('disabled','disabled');
              $("#ifsc_code").attr('disabled','disabled');
              $("#ifsc_code").addClass('input-box-disabled');
              $(".upload_cancel_cheque_error").hide();
              $('#upload_cancel_cheque').val('');
              $('#upload_cancel_cheque').attr('disabled',true);
              $("#bank_details-alert-message").html('<span class="alert alert-info">Your Request went for vefication after approved it will be update</span>');
            } else if(response.msg == "pickup details updated"){
              $("input.pickup_address").val(pickup_address);
              $("input.pickup_city").val(pickup_city);
              $("input.pickup_pincode").val(pickup_pincode);
              $("input.pickup_zone_id").val(pickup_zone_id);
              $(".pickup_address").hide();
              $("#pickup_address").attr('disabled','disabled');
              $("#pickup_pincode").addClass('input-box-disabled');
              $("#pickup_pincode").attr('disabled','disabled');
              $("#pickup_city").addClass('input-box-disabled');
              $("#pickup_city").attr('disabled','disabled');
              $("#pickup_zone_id").attr('disabled','disabled');
            } else{
              $("input.address1").val(address);
              $("input.city").val(seller_city);
              $("input.pincode").val(pincode);
              $("input.zone_id").val(seller_zone_id);
              $("#same_as_primary_address").prop("checked",false);
              $("button[data-id=\'pickup_address'\]").show();
              $(".address1").hide();
              $("#address1").attr('disabled','disabled');
              $("#pincode").addClass('input-box-disabled');
              $("#pincode").attr('disabled','disabled');
              $("#city").addClass('input-box-disabled');
              $("#city").attr('disabled','disabled');
              $("#zone_id").attr('disabled','disabled');

            }
          }
      });
    }

    function inerstValue(id){
      var form_data = new FormData();
      form_data.append('field', id);
      var update_value = $("#"+id).val();
      if(update_value == ''){
        if(id != 'additional_emails'){
          alert("please fill empty field");
          return false;
        }
      }

      if(id != "primary_contact_no" && id != "pickup_contact_no" && id != "account_contact_no" && id != "inventory_contact_no" && id != "pan" && id != "tin" && id !='additional_emails'){
          var alpha = /^[a-zA-Z]{1}[a-zA-Z. ]*$/;
          if(!alpha.test(update_value)){
              alert("Name is not proper");
              return false;
          }
      }

      if(update_value.length > 64){
        if(id !="additional_emails"){
          alert("Name should be less than 64 characters");
          return false;
         }
      }
      var old_value    = $("input."+id).val();
      if(update_value != old_value){
        form_data.append('update_value', update_value);
        if(id == "pan"){
           var result = pan_card_validation(update_value);
           if(result == "false"){
               return false;
           }
           var file_data = $('#pan_upload').prop('files')[0];
           if(typeof file_data  === "undefined"){
            //  $("."+id).hide();
            //  $('#'+id).addClass('input-box-disabled');
            //  $('#'+id).attr('disabled','disabled');
            $(".upload_pan_number_error").find(".alert-danger").remove();
            $(".upload_pan_number_error").html('<span class="alert alert-danger" style="float:right;margin-right: 46px;">please choose file</span>');
             return false;
           }
           var pan_image       = $("#pan_image").val();
           var seller_nickname =  $("input.nickname").val();
           form_data.append('img', pan_image  );
           form_data.append('upload_file', file_data);
           form_data.append('seller_nickname', seller_nickname  );
            //var form_data = new FormData($('#img_form')[0]);
            // $(".upload_pan_number").hide();
        }
        if(id == "tin"){
          var check = '';
          var tax_type = [];
          $(".tin_taxs_checkbox").each(function(i,e) {
            if($(this).prop("checked") == true){
              tax_type.push($(this).val());
                check = "true";
            }
          });
          if(check != "true"){
            alert("please select type of goods");
            return false;
          }
          var file_data = $('#tin_upload').prop('files')[0];
          if(typeof file_data  === "undefined"){
            $(".upload_tin_number_error").find(".alert-danger").remove();
            $(".upload_tin_number_error").html('<span class="alert alert-danger" style="float:right;margin-right: 46px;">please choose file</span>');
            return false;
          }
          var old_tin_tax_type = $("#old_tin_tax_type").val();
          var tin_image       = $("#tin_image").val();
          var seller_nickname =  $("input.nickname").val();
          form_data.append('tax_type', tax_type);
          form_data.append('old_tin_tax_type', old_tin_tax_type);
          form_data.append('img', tin_image);
          form_data.append('upload_file', file_data);
          form_data.append('seller_nickname', seller_nickname);
          //$(".upload_tin_number").hide();
        }
        var seller_id = $("input[name = 'seller_id']").val();
        form_data.append('seller_id', seller_id);

        form_data.append('old_value', old_value);

        if(id == "pickup_holder_name" || id == "pickup_contact_no" ){
            var same_as_primary = $(".same_as_primary_pickup").val();
            form_data.append('same_as_primary', same_as_primary);
        }
        if(id == "account_holder_name" || id == "account_contact_no"){
            var same_as_primary = $(".same_as_primary_account").val();
            form_data.append('same_as_primary', same_as_primary);
        }
        if(id == "inventory_holder_name" || id == "inventory_contact_no"){
            var same_as_primary = $(".same_as_primary_inventory").val();
            form_data.append('same_as_primary', same_as_primary);
        }
        //$('#'+id).attr('disabled','disabled');
       $.ajax({
          url:'index.php?route=seller_panel/profile/updateSellerProfile',
          type:'post',
          data: form_data,
          dataType:'json',
          mimeType: "form-data",
          contentType: false,
          processData: false,
          async: false,
          success: function(response) {
            if(response.valid == "not vaild"){
              alert(response.message);
              return false;
            }
            if(response.msg == "success"){
              $("."+id).hide();
              $("input."+id).val(update_value);
              if(id == 'primary_contact_name' || id == "primary_contact_no"){
                $("#same_as_primary_pickup").prop("checked", false);
                $("#same_as_primary_account").prop("checked", false);
                $("#same_as_primary_inventory").prop("checked", false);
              }
              if(id == 'pan'){
                $("#pan-alert-message").html('<span class="alert alert-info">Your Request went for vefication after approved it will be update</span>');
                $("#pan_image").val(response.data);
                $(".upload_pan_number_error").find(".alert-danger").remove();
                $(".upload_tin_number_error").find(".alert-danger").remove();
                $('#pan_upload').val('');
                $("#pan_upload").attr('disabled',true);

              }
              if(id == 'tin'){
                $("#tin_image").val(response.data);
                $(".upload_tin_number_error").find(".alert-danger").remove();
                $('#tin_upload').val('');
                $("#tin-alert-message").html('<span class="alert alert-info">Your Request went for vefication after approved it will be update</span>');
                $("#tin_upload").attr('disabled',true);
              }
            }
            $('#'+id).attr('disabled','disabled');
            if(id !="additional_emails"){
                $('#'+id).addClass('input-box-disabled');
            }
          },
        });
    } else {
            $("#tin-alert-message").find(".alert-info").remove();
            $("#pan-alert-message").find(".alert-info").remove();
             if(id !="additional_emails"){
                 $('#'+id).addClass('input-box-disabled');
             }
             $('#'+id).attr('disabled','disabled');
             $("."+id).hide();
      }
    }

     function sameAsPrimary(type){
       var primary_contact_name     = $("#primary_contact_name").val();
       var old_primary_contcat_name = $("input.primary_contact_name").val();
       var primary_contact_no       = $("#primary_contact_no").val();
       var old_primary_contact_no   = $("input.primary_contact_no").val();
       var seller_id                = $("input[name = 'seller_id']").val();
       var alpha = /^[a-zA-Z]{1}[a-zA-Z .]*$/;
       if($("#"+type).is(":checked")){
           if(primary_contact_name != old_primary_contcat_name){
               $("#"+type).prop("checked", false);
               alert("Please update first primary details");
               return false;
           }
           if(primary_contact_no != old_primary_contact_no){
               $("#"+type).prop("checked", false);
               alert("Please update first primary details");
               return false;
           }
          if(type != "same_as_primary_address"){
              if(primary_contact_name == ''){
                  $("#"+type).prop("checked", false);
                  alert("Primary name is empty");
                  return false;
              }
              if(primary_contact_no == ''){
                  $("#"+type).prop("checked", false);
                  alert("Primary mobile no is empty");
                  return false;
              }
              if(!alpha.test(primary_contact_name)){
                  $("#"+type).prop("checked", false);
                  alert("Primary name is not proper");
                  return false;
              }
          }

       }
       if(type == "same_as_primary_pickup"){
         if($("#"+type).is(":checked")){
         } else{
           $("button[data-id=\'pickup_holder_name'\ ]").show();
           $("button[data-id=\'pickup_contact_no'\ ]").show();
           return false;
         }
         var old_contact_name = $("input.pickup_holder_name").val();
         var old_contact_no   = $("input.pickup_contact_no").val();
         var same_as_primary  = $(".same_as_primary_pickup").val();
         if(old_contact_name == primary_contact_name && old_contact_no == primary_contact_no){
           $("button[data-id=\'pickup_holder_name'\ ]").hide();
           $("button[data-id=\'pickup_contact_no'\ ]").hide();
           $("btn.pickup_holder_name").hide();
           $("btn.pickup_contact_no").hide();
           $("#pickup_holder_name").addClass('input-box-disabled');
           $("#pickup_holder_name").attr('disabled',true);
           $(".pickup_holder_name").hide();
           $("#pickup_contact_no").addClass('input-box-disabled');
           $("#pickup_contact_no").attr('disabled',true);
           $(".pickup_contact_no").hide();
            return false;
         }
         var data = "type="+type+"&primary_contact_name="+primary_contact_name+"&primary_contact_no="+primary_contact_no+"&old_contact_name="+old_contact_name+"&old_contact_no="+old_contact_no+"&seller_id="+seller_id;
       }
       if(type == 'same_as_primary_address'){
         if($("#"+type).is(":checked")){
         } else{
           $("button[data-id=\'pickup_address'\ ]").show();
           return false;
         }
          var primary_address     = $("#address1").val();
          var old_primary_address = $("input.address1").val();
          var primary_zone = $("#zone_id").val();
          var old_primary_zone = $("input.zone_id").val();
          var primary_city = $("#city").val();
          var old_primary_city = $("input.city").val();
          var primary_pincode = $("#pincode").val();
          var old_primary_pincode = $("input.pincode").val();
          var old_pickup_pincode  = $("input.pickup_pincode").val();
          var old_pickup_address = $("input.pickup_address").val();
          var old_pickup_city   = $("input.pickup_city").val();
          var old_pickup_zone_id   = $("input.pickup_zone_id").val();
          if($("#"+type).is(":checked")){
              if(primary_address != old_primary_address){
                  alert("Firstly update primary address");
                  $("#same_as_primary_address").prop("checked", false);
                  return false;
              }
              if(primary_city != old_primary_city){
                  alert("Firstly update primary city");
                  $("#same_as_primary_address").prop("checked", false);
                  return false;
              }
              if(primary_pincode != old_primary_pincode){
                  alert("Firstly update primary pincode");
                  $("#same_as_primary_address").prop("checked", false);
                  return false;
              }
          }

          $("textarea#pickup_address").val(primary_address);
          $("#pickup_city").val(primary_city);
          $("#pickup_pincode").val(primary_pincode);
          $("#pickup_zone_id").val(primary_zone);
          if(primary_address == old_pickup_address && primary_city == old_primary_city && primary_pincode == old_primary_city){
            $('textarea#pickup_address').attr('disabled',true);
            $("#pickup_city").addClass('input-box-disabled');
            $("#pickup_pincode").attr('disabled',true);
            $("#pickup_pincode").addClass('input-box-disabled');
            $("#pickup_city").attr('disabled',true);
            $("select[name=\'pickup_zone_id\']").attr('disabled',true);
            $("button[data-id=\'pickup_address\' ]").hide();
            $('.pickup_address').hide();
            // $("button[data-id=\'account_contact_no'\ ]").hide();
            // $(".pickup_zone_id").hide();
            // $("#account_contact_no").addClass('input-box-disabled');
            // $(".pickup_city").hide();
            return false;
          }
          var data = "type="+type+"&primary_address="+primary_address+"&primary_zone="+primary_zone+"&primary_city="+primary_city+"&primary_pincode="+primary_pincode+"&old_pickup_address="+old_pickup_address+"&old_pickup_city="+old_pickup_city+"&old_pickup_pincode="+old_pickup_pincode+"&old_pickup_zone_id="+old_pickup_zone_id+"&seller_id="+seller_id;
       }
       if(type == "same_as_primary_account"){
         if($("#"+type).is(":checked")){
         } else{
           $("button[data-id=\'account_holder_name'\ ]").show();
           $("button[data-id=\'account_contact_no'\ ]").show();
           return false;
         }
         var old_contact_name = $("input.account_holder_name").val();
         var old_contact_no   = $("input.account_contact_no").val();
         var same_as_primary  = $(".same_as_primary_account").val();
         if(old_contact_name == primary_contact_name && old_contact_no == primary_contact_no){
           $("button[data-id=\'account_holder_name'\ ]").hide();
           $("button[data-id=\'account_contact_no'\ ]").hide();
           $("#account_holder_name").addClass('input-box-disabled');
           $("#account_holder_name").attr('disabled',true);
           $(".account_holder_name").hide();
           $("#account_contact_no").addClass('input-box-disabled');
           $("#account_contact_no").attr('disabled',true);
           $(".account_contact_no").hide();
            return false;
         }
         var data = "type="+type+"&primary_contact_name="+primary_contact_name+"&primary_contact_no="+primary_contact_no+"&old_contact_name="+old_contact_name+"&old_contact_no="+old_contact_no+"&seller_id="+seller_id
       }
       if(type == "same_as_primary_inventory"){
         if($("#"+type).is(":checked")){
         } else{
           $("button[data-id=\'inventory_holder_name'\ ]").show();
           $("button[data-id=\'inventory_contact_no'\ ]").show();
           return false;
         }
         var old_contact_name = $("input.inventory_holder_name").val();
         var old_contact_no   = $("input.inventory_contact_no").val();
         var same_as_primary  = $(".same_as_primary_inventory").val();
         if(old_contact_name == primary_contact_name && old_contact_no == primary_contact_no){
           $("button[data-id=\'inventory_holder_name'\ ]").hide();
           $("button[data-id=\'inventory_contact_no'\ ]").hide();
           $("#inventory_holder_name").addClass('input-box-disabled');
           $("#inventory_holder_name").attr('disabled',true);
           $(".inventory_holder_name").hide();
           $("#inventory_contact_no").addClass('input-box-disabled');
           $("#inventory_contact_no").attr('disabled',true);
           $(".inventory_contact_no").hide();
           return false;
         }
         var data = "type="+type+"&primary_contact_name="+primary_contact_name+"&primary_contact_no="+primary_contact_no+"&old_contact_name="+old_contact_name+"&old_contact_no="+old_contact_no+"&seller_id="+seller_id
       }
       $.ajax({
          type:'post',
          dataType:'json',
          url:'index.php?route=seller_panel/profile/sameAsPrimary',
          data: data,
          async: false,
          success: function(response) {
              if(response.valid == "not vaild"){
                $("#"+type).prop("checked", false);
                alert("Primary Mobile number is not valid");
                return false;
              }
              if(response == "success"){
                if(type == "same_as_primary_pickup"){
                    $("button[data-id=\'pickup_holder_name'\ ]").hide();
                    $("button[data-id=\'pickup_contact_no'\ ]").hide();
                    $("#pickup_holder_name").addClass('input-box-disabled');
                    $(".pickup_holder_name").hide();
                    $("#pickup_contact_no").addClass('input-box-disabled');
                    $(".pickup_contact_no").hide();
                    $("#pickup_holder_name").val(primary_contact_name);
                    $("#pickup_contact_no").val(primary_contact_no);
                    $("input.pickup_holder_name").val(primary_contact_name);
                    $("input.pickup_contact_no").val(primary_contact_no);
                }
                if(type == "same_as_primary_account"){
                  $("button[data-id=\'account_holder_name'\ ]").hide();
                  $("button[data-id=\'account_contact_no'\ ]").hide();
                  $("#account_holder_name").addClass('input-box-disabled');
                  $(".account_holder_name").hide();
                  $("#account_contact_no").addClass('input-box-disabled');
                  $(".account_contact_no").hide();
                  $("#account_holder_name").val(primary_contact_name);
                  $("#account_contact_no").val(primary_contact_no);
                  $("input.account_holder_name").val(primary_contact_name);
                  $(".account_contact_no").val(primary_contact_no);
                }
                if(type == "same_as_primary_inventory"){
                  $("button[data-id=\'inventory_holder_name'\ ]").hide();
                  $("button[data-id=\'inventory_contact_no'\ ]").hide();
                  $("#inventory_holder_name").addClass('input-box-disabled');
                  $(".inventory_holder_name").hide();
                  $("#inventory_contact_no").addClass('input-box-disabled');
                  $(".inventory_contact_no").hide();
                  $("#inventory_holder_name").val(primary_contact_name);
                  $("#inventory_contact_no").val(primary_contact_no);
                  $("input.inventory_holder_name").val(primary_contact_name);
                  $("input.inventory_contact_no").val(primary_contact_no);
                }
                if(type == "same_as_primary_address"){
                    $("button[data-id=\'pickup_address'\ ]").hide();
                    $("#pickup_address").attr("disabled");
                    $("#pickup_city").attr("disabled");
                    $("#pickup_city").addClass('input-box-disabled');
                    $("#pickup_zone_id").attr("disabled");
                    $("input.pickup_address").val(primary_address);
                    $("input.pickup_city").val(primary_city);
                    $("input.pickup_pincode").val(primary_pincode);
                    $("input.pickup_zone_id").val(primary_zone);
                }
              }
          },
       })
    }

    /*  This function validates for PAN Card No.*/
        function pan_card_validation(textObj) {
          var regpan = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/;
          var code= /([C,P,H,F,A,T,B,L,J,G])/;
          var code_chk=textObj.substring(3,4);

          if (textObj!=="") {
              if(regpan.test(textObj) == false) {
                    alert("Invalid pan card no");
                   return "false";
              }
              if (code.test(code_chk)==false) {
                  alert("Invalid pan card no");
                  return "false";
              }
          }
          return true;
        }
</script>

<script type="text/javascript">
  $('.order_md_tab').on('change',function(){
    if($(this).val() == 'business'){
      $('#Basic').removeClass(' in active')
      $("#Business").addClass(' in active');
    }

    if($(this).val() == 'basic'){
      $("#Basic").addClass(' in active');
      $('#Business').removeClass(' in active')
    }
  });
</script>