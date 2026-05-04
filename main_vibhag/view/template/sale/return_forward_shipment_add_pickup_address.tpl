<!-- Add Address Popup -->
<div id="add-address-popup-forward" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Delivery Address</h4>
      </div>
      <div class="modal-body">
        <div class="add_pickup_address_error_forward alert-danger"></div>
        <div class="form-horizontal" id="tab-address">
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="firstname_2">Customer Name: </label>
            <div class="col-sm-5">
              <input type="text" name="cust_name_2_forward" placeholder="First Name" class="form-control firstname_2_forward" value="<?php echo $customer['firstname'].' '.$customer['lastname'];?>" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="cust_telephone_2">Phone No.: </label>
            <div class="col-sm-5">
              <input type="text" name="cust_telephone_2_forward" placeholder="Phone Number" class="form-control cust_telephone_2_forward" value="<?php echo $shipping_address['telephone'];?>" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="cust_address1_2">Address1 :</label>
            <div class="col-sm-5">
              <input type="text" name="cust_address1_2_forward" placeholder="Address1" class="form-control address1_2_forward" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label" for="cust_address2_2">Address2: </label>
            <div class="col-sm-5">
              <input type="text" name="cust_address2_2_forward" placeholder="Address2" class="form-control address2_2_forward" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="cust_pin_2">PostCode: </label>
            <div class="col-sm-5">
              <input type="text" name="cust_pin_2_forward" placeholder="PostCode" class="form-control postcode_2_forward" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="cust_city_2">City: </label>
            <div class="col-sm-5">
              <input type="text" name="cust_city_2_forward" placeholder="City" class="form-control city_2_forward" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="select_country_2">Country: </label>
            <div class="col-sm-5">
              <select name="select_country_2_forward" id="select_country_2_forward" onchange=" country1(this.value, '<?php echo $shipping_address['shipping_zone_id']; ?>');" class="form-control select_country_2_forward">
                <option value="">Select</option>
                <?php foreach ($countries as $country) { ?>
                <?php if ($country['country_id'] == $shipping_address['shipping_country_id']) { ?>
                <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="select_zone_2">Zone: </label>
            <div class="col-sm-5">
              <select name="select_zone_2_forward" id="select_zone_2_forward" class="form-control select_zone_2_forward">
              </select>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" data-type="forward" class="btn btn-primary add_cust_pickup_address_forward">Add</button>
        <button type="button" data-type="forward" class="btn btn-default close-btn" data-dismiss="modal">Close</button>
      </div>
    </div>
    </div>
  </div>
</div>
<!-- Add Address Popup -->