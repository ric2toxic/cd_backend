<!-- Add Address Popup -->
<div id="add-address-popup" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Pick-up Address</h4>
      </div>
      <div class="modal-body">
        <div class="add_pickup_address_error alert-danger"></div>
        <div class="form-horizontal" id="tab-address">
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="firstname_2">Customer Name: </label>
            <div class="col-sm-5">
              <input type="text" name="cust_name_2" placeholder="First Name" class="form-control firstname_2" value="<?php echo $customer['firstname'].' '.$customer['lastname'];?>" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="cust_telephone_2">Phone No.: </label>
            <div class="col-sm-5">
              <input type="text" name="cust_telephone_2" placeholder="Phone Number" class="form-control cust_telephone_2" value="<?php echo $shipping_address['telephone'];?>" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="cust_address1_2">Address1 :</label>
            <div class="col-sm-5">
              <input type="text" name="cust_address1_2" placeholder="Address1" class="form-control address1_2" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label" for="cust_address2_2">Address2: </label>
            <div class="col-sm-5">
              <input type="text" name="cust_address2_2" placeholder="Address2" class="form-control address2_2" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="cust_pin_2">PostCode: </label>
            <div class="col-sm-5">
              <input type="text" name="cust_pin_2" placeholder="PostCode" class="form-control postcode_2" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="cust_city_2">City: </label>
            <div class="col-sm-5">
              <input type="text" name="cust_city_2" placeholder="City" class="form-control city_2" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="select_country_2">Country: </label>
            <div class="col-sm-5">
              <select name="select_country_2" id="select_country_2" onchange=" country(this.value, '<?php echo $shipping_address['shipping_zone_id']; ?>');" class="form-control select_country_2">
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
              <select name="select_zone_2" id="select_zone_2" class="form-control select_zone_2">
              </select>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" data-type="reverse" class="btn btn-primary add_cust_pickup_address">Add</button>
        <button type="button" data-type="reverse" class="btn btn-default close-btn" data-dismiss="modal">Close</button>
      </div>
    </div>
    </div>
  </div>
</div>
<!-- Add Address Popup -->