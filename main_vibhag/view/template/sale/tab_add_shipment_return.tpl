<div class="table-responsive reverse-shipment">
<input type="hidden" name="hidden_token" id="hidden_token" value="<?php echo $token;?>" >
<form name="reverse-shipment-form" id="reverse-shipment-form" class="form-horizontal">
  <input type="hidden" name="hddn_order_no" value="<?php echo $order_no;?>" />
  <input type="hidden" name="order_id" value="<?php echo $order_id;?>" />
  <div class="col-sm-12" style="margin-bottom:10px;">
      <fieldset class="col-sm-12">
        <legend>Courier Partners:</legend>
        <div class="col-sm-6" style="padding-bottom:10px;">
            <?php if(!empty($courier_partners_reverse)) { ?>
            <select name="courier_partner" id="courier_partner" class="form-control">
                <option value="">--Select Courier Partner--</option>
                <?php foreach($courier_partners_reverse as $row) { ?>
                <option value="<?php echo $row['courier_name']?>"><?php echo $row['courier_name']?></option>
                <?php } ?>
            </select>    
            <?php } ?>
        </div>
      </fieldset>  
    </div>
  
  <div class="col-sm-12">
      <fieldset class="col-sm-12">
        <legend>Check for Pincode Availablity:</legend>
        <div class="col-sm-6">
            <label for="pincode">Pin Code:</label> <br />
            <div class="col-sm-8" id="pincode_servisability_type" style="display: none">
              <div class="col-sm-4"><label><input type="radio" name="serviceability_type" id="servisability_economy" value="Surface" checked="checked"> Surface</label></div>
              <div class="col-sm-4"><label><input type="radio" name="serviceability_type" id="servisability_express" value="Apex" > Apex<label></div>
            </div>
            <input type="text" name="pincode" id="pincode" class="form-control" value="<?php echo $shipping_address['shipping_postcode'];?>">
        </div>
        <div class="col-sm-6" style="text-align:left; padding-top:48px;">
            <button type="button" name="check_serviceable" id="check_serviceable" class="btn btn-primary check_serviceable">Check</button>
        </div>
        <div class="col-sm-12" style="text-align:left;">
            <span id="msg-class" style="display: none;margin-top: 5px;"></span>
        </div>
      </fieldset>  
    </div>  
    
  <div class="col-sm-12 add_shippment_box reverse_shippment_box" style="display:none">
    <div class="row reverse-shipment-div">
      <div class="error-div"></div>
      <div class="col-sm-6"> 
        <?php if(!empty($master_return_ids)){ ?>
          <fieldset class="fieldset select_master_return">
            <legend>Select Master Return:</legend>
            <?php foreach($master_return_ids as $key => $value){?>
              <?php 
                $value['oop_model'] = implode(array_unique(explode(',', $value['oop_model'])),', ');
              ?>
              <label><input type="checkbox" name="selected_master_return_id[]" class="selected_master_return_id" value="<?php echo $value['master_return_id']; ?>"> <?php echo $value['master_return_id']; ?>,  (<b><?php echo $value['oop_model'];?></b>)</label><br>
            <?php } ?>
          </fieldset>
        <?php } ?>
      </div>

    <div class="col-sm-12 api-service-type" style="margin-top:10px;">
      <fieldset class="col-sm-12">
           <legend>Service Type:</legend>
            <div class="col-sm-6">
              <div class="form-group required">
                <div class="col-sm-6">
                  <div class="input-group">
                    <label><input type="radio" name="service_type" id="service_type_economy" value="Economy" checked="checked">
                    Surface (Economy)</label> 
                  </div>
                </div>  
                <div class="col-sm-6">
                  <div class="input-group">
                    <label><input type="radio" name="service_type" id="service_type_express" value="Express">
                    Apex (Express)</label>
                  </div>
                </div>
              </div>
            </div>
      </fieldset> 
    </div>

    <div class="col-sm-12" style="margin-top:10px;">
      <fieldset class="col-sm-12">
           <legend>Package Descriptions:</legend>
            <div class="col-sm-6">
              <div class="form-group required">
              <label for="package_desc" class="col-sm-3 control-label required"> Description: </label>
              <div class="col-sm-6">
                <div class="input-group">
                  <input name="package_desc" id="package_desc" class="form-control" type="text" style="width: 322px;">
                </div>
              </div>  
            </div>
          </div>
          <div class="col-sm-6">
              <div class="form-group">
              <label for="package_desc" class="col-sm-3 control-label">Remark: </label>
              <div class="col-sm-6">
                <div class="input-group">
                  <textarea name="remarks" id="remarks" cols="40" rows="3"></textarea>
                </div>
              </div>  
            </div>
          </div>
      </fieldset> 
    </div>

    </div>
    <div class="col-sm-4"> 
      <div class="form-group">
        <label for="package_value" class="col-sm-4 control-label">Package Value: </label>
        <div class="col-sm-8">
          <div class="input-group">
            <input name="package_value" id="package_value" readonly class="form-control" type="text" value="0">
            <input type="hidden" name="hddn_return_ids" id="hddn_return_ids" class="form-control"  value="">
          </div>
        </div>
      </div>
    </div>  
    <div class="col-sm-4"> 
      <div class="form-group">
        <label for="package_qty" class="col-sm-4 control-label">Qty: </label>
        <div class="col-sm-8">
          <div class="input-group">
            <input name="package_qty" id="package_qty" class="form-control" readonly="" type="text" value="0">
          </div>
        </div>
      </div>
    </div> 
    <div class="col-sm-4"> 
      <div class="form-group">
        <label for="package_weight" class="col-sm-4 control-label">Package Weight: </label>
        <div class="col-sm-8">
          <div class="input-group">
            <input name="package_weight" id="package_weight" class="form-control" readonly="" type="text" value="0">
            <input name="return_reason" id="return_reason" type="hidden">
          </div>
        </div>
      </div>
    </div>
    <!--   Ware Houses listing ------>
    <div class="col-sm-12 shipment_label ">
      <fieldset class="col-sm-12">
        <legend>WareHouse Listing: </legend>
        <div class="form-group">
          <div class="col-sm-4">
            <input type="text" name="filter_warehouse_name" class="form-control" placeholder="WareHouse Name" />
          </div>
          <div class="col-sm-4">
            <input type="text" name="filter_warehouse_city" class="form-control" placeholder="City" />
          </div>
          <div class="col-sm-3">
            <button type="button" name="search" class="btn btn-primary" id="button-search">Search</button>
          </div>
        </div>
        <div class="address_form_list">
          <?php if(isset($Warehouses)){
                  $row_count = 0;
                  foreach($Warehouses as $address_list){
                    $row_count++;
                    if($row_count > 10){ break;}
                    $address_1 = '';
                    $address_2 = '';
                    if(!empty($address_list['address_1'])){
                      $address_1 = $address_list['address_1'] . '<br>';
                    }
                    if(!empty($address_list['address_2'])){
                      $address_2 = $address_list['address_2'] . '<br>';
                    }
          ?>
              <div class="col-sm-3">
                <label class="custom_lbl" for="input-address-<?php echo $address_list['warehouse_id'];?>" >
                <div class="warehouse_addresses_list address-div warehouse_address_height_set" id="warehouse_id_<?php echo $address_list['warehouse_id'];?>" data-address-id="<?php echo $address_list['warehouse_id'];?>">
                    <?php echo $address_list['warehouse_name'];?><br>
                    <?php echo $address_1; ?>
                    <?php echo $address_2; ?>
                    <?php echo $address_list['city'];?> - 
                    <?php echo $address_list['postcode'];?><br>
                    <?php echo $address_list['zone_name'];?> - 
                    <?php echo $address_list['country_name'];?><br>
                    <?php echo $address_list['telephone'];?>
                </div>
                </label>
                <div class="address_select">
                  <input type="radio" id="input-address-<?php echo $address_list['warehouse_id'];?>" class="selected_nuvoex_vendor_code" name="select_warehouse_id" value="<?php echo $address_list['warehouse_id'];?>" />
                  <label class="" for="input-address-<?php echo $address_list['warehouse_id'];?>" ><?php echo $address_list['zone_name'];?> - <?php echo $address_list['country_name'];?> </label>
                </div>
              </div>
          <?php } } ?>
        </div>
      </fieldset>
    </div>
    <!-- -- Pick-up address ------>
    <div class="col-sm-12" style="margin-bottom: 20px;">
      <fieldset class="col-sm-12">
        <legend>Pick-Up Address: </legend>
        <div class="pickup-address">
          <div id="add_address_div" class="col-sm-12 add_address_button">
            <input type="hidden" name="payment_zone_id" id="payment_zone_id" value="<?php echo $payment_address['payment_zone_id']?>">
            <div><span class="cart_data_heading">Please select the address from where you want products to be picked.</span></div>
            <button style="margin-right: 1%;" data-type="reverse" data-target="#add-address-popup" class="btn btn_clear_cart pull-right" data-toggle="modal" type="button" onclick="return onPopupLoad();">
               <i aria-hidden="true" class="fa fa-plus-square-o"></i>Add Address
            </button>
            <div class="clearfix"></div>
            <div class="col-sm-4 cart_table">
              <div style="margin: 5px; padding: 4%;" title="" data-toggle="tooltip" class="address_penal">
                <label class="custom_lbl" for="pickup-address-1" >
                <div class="pickup_addresses_list">
                  <?php echo $shipping_address['shipping_address_1']; ?>,
                  <?php echo $shipping_address['shipping_address_2']; ?><br>
                  <?php echo 'Contact No.: '. $shipping_address['telephone'];?>  <br>
                  <?php 
                    if( !empty($shipping_address['alternate_numbers']) ) {
                        echo 'Alternate Contact No: ';
                        echo implode(', ', $shipping_address['alternate_numbers']);
                        echo '<br>';
                    }
                  ?>
                  <?php echo $shipping_address['shipping_city'];?>  <br>
                  <?php echo $shipping_address['shipping_postcode'];?><br>
                  <?php echo $shipping_address['shipping_zone'];?> <br>
                  <?php echo $shipping_address['shipping_country'];?><br>
                  <input type="hidden" name="cust_name_1" value ="<?php echo $customer['firstname'].' '.$customer['lastname'];?>">
                  <input type="hidden" name="cust_address1_1" value ="<?php echo $shipping_address['shipping_address_1'];?>">
                  <input type="hidden" name="cust_address2_1" value ="<?php echo $shipping_address['shipping_address_2'];?>">
                  <input type="hidden" name="cust_city_1" value ="<?php echo $shipping_address['shipping_city'];?>">
                  <input type="hidden" name="cust_pin_1" value ="<?php echo $shipping_address['shipping_postcode'];?>">
                  <input type="hidden" name="cust_telephone_1" value ="<?php echo $shipping_address['telephone'];?>">
                </div>
                </label>
                <input type="hidden" name="zone_id_1" id="zone_id_1" class="zone_1" value ="<?php echo $shipping_address['shipping_zone_id'];?>"> 
                <input type="hidden" name="cust_zone_1" class="cust_zone_1" value ="<?php echo $shipping_address['shipping_zone'];?>">
                <input type="hidden" name="cust_country_1" class="cust_country_1" value ="<?php echo $shipping_address['shipping_country'];?>">
                <div class="selected_address">
                  <input type="radio" id="pickup-address-1" name="pickup-address-id" class="pickup-address-id" value="1" />
                  <label for="pickup-address-1" >
                  PICKUP FROM THIS ADDRESS
                  </label>
                </div>
              </div>
            </div>
            <div class="col-sm-4 cart_table pickup-address-2" style="display: none;">
              <div style="margin: 5px; padding: 4%;" class="address_penal">
                <label class="custom_lbl" for="pickup-address-2" >
                <div class="pickup_addresses_list pickup_address_2">
                </div>
                </label>
                <input type="hidden" name="zone_id_2" id="zone_id_2" class="zone_2" value ="<?php echo $shipping_address['shipping_zone_id'];?>"> 
                <input type="hidden" name="cust_zone_2" class="cust_zone_2" value ="<?php echo $shipping_address['shipping_zone'];?>">
                <input type="hidden" name="cust_country_2" class="cust_country_2" value ="<?php echo $shipping_address['shipping_country'];?>">
                <div class="selected_address">
                  <input type="radio" id="pickup-address-2" name="pickup-address-id" class="pickup-address-id" value="2" />
                  <label for="pickup-address-2" >
                  PICKUP FROM THIS ADDRESS
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </fieldset>
    </div>
    
    <div class="col-sm-12" style="text-align: center;">
      <input type="reset" value="Cancel" class="btn btn-primary">
      <button type="button" name="reverse_shippment_add_btn" class="btn btn-primary reverse-shippment" id="reverse_shippment">Submit</button>
    </div>
    <?php echo $tab_add_pickup_address;?>
  </div>
</form>
</div>
<script type="text/javascript" src="view/javascript/jquery/clockpicker/bootstrap-clockpicker.min.js"></script>
<script type="text/javascript">

  $('.clockpicker').clockpicker();

  $('.warehouse_addresses_list').click(function(){
    $('.selected_nuvoex_vendor_code').each(function( index ) {
      $(this).removeAttr('checked');
    });
  });

  $('#reverse_shippment').click(function(){
      addReverseShipment();
  });

 $('#check_serviceable').click(function(){
    isPincodeServiceable();
 })

</script>

<link rel="stylesheet" type="text/css" href="view/javascript/jquery/clockpicker/bootstrap-clockpicker.min.css">
<style type="text/css">
.reverse-shipment fieldset {border: 1px solid #bfbfbf;display: inline-block;padding: 0 5px 5px;
}
.reverse-shipment fieldset legend {border: 0 none;font-size: 14px;padding: 0;width: auto;
}
.reverse-shipment-div{margin: 25px 0px !important;}
.add_address_button {display: block;clear: both;margin-bottom: 10px;float: none;}
.btn_clear_cart {position: absolute;right: 10px;top: -7px;background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #fff 0px, #fff 3%, #f2f2f2 3%, #f2f2f2 100%) repeat scroll 0 0;border: 1px solid #c9c9c9;color: #565656;}
.pickup_addresses_list{border: 1px groove #E0E0E0;padding: 5px;border-radius: 5px;cursor: pointer;}
</style>

<style type="text/css">
  .add_shippment_box{
      border: 1px groove #bfbfbf;
      margin: 10px 0;
      padding-bottom: 10px;
  }
  .error-div{
    display: block;
    padding: 5px;
    margin-bottom: 5px;
  }
  .warehouse-detail{
    font-weight: bold;
    margin-bottom: 5px;
    font-size: medium;
  }
  .loading{padding: 0px 10px;font-size: 15px;}
  .clockpicker{width: 178px;}
  .custom_lbl{display: block;font-weight: normal;}
  .warehouse_addresses_list{border: 1px solid #E0E0E0!important;}
</style>