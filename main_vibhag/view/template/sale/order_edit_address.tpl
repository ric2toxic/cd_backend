<?php
$old_add_detail = array('shipping'=>array());
?>
<div class="col-sm-12 order_address_update">
    <form class="form-horizontal" action="<?php echo $edit_address_detail; ?>" method="post">
        <div class="form-group">
            <div class="col-sm-4">
                <h2><?php echo $heading_edit_address; ?></h2>
            </div>
            <div class="col-sm-8">
                <div class="col-sm-4 address_update">
                    <input type="checkbox" id="payment_address" name="payment_address_checked" value="1">
                    <label class="" for="payment_address">Payment Address</label>
                </div>
                <div class="col-sm-4 address_update">
                    <input type="checkbox" checked id="shipping_address" name="shipping_address_checked" value="1">
                    <label class="" for="shipping_address">Shipping Address</label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="input-name" class="col-sm-2 control-label"><?php echo $text_firstname; ?></label>
            <div class="col-sm-10">
                <input type="text" name="first_name" class="form-control" id="input-name" placeholder="<?php echo $entry_firstname; ?>" value="<?php echo $payment_firstname; ?>">
                <?php $old_add_detail['shipping']['first_name'] = $entry_firstname; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="input-name" class="col-sm-2 control-label"><?php echo $text_lastname; ?></label>
            <div class="col-sm-10">
                <input type="text" name="last_name" class="form-control" id="input-name" placeholder="<?php echo $entry_lastname; ?>" value="<?php echo $payment_lastname; ?>">
                <?php $old_add_detail['shipping']['last_name'] = $entry_lastname; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="input-name" class="col-sm-2 control-label"><?php echo $text_company; ?></label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="input-name" placeholder="<?php echo $entry_company; ?>" value="<?php echo $payment_company; ?>" name="company_name">
                <?php $old_add_detail['shipping']['company_name'] = $payment_company; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="input-name" class="col-sm-2 control-label"><?php echo $text_telephone; ?></label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="input-name" placeholder="<?php echo $entry_telephone; ?>" value="<?php echo $payment_telephone; ?>" name="telephone">
                <?php $old_add_detail['shipping']['telephone'] = $payment_telephone; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="input-name" class="col-sm-2 control-label"><?php echo $text_address_1; ?></label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="input-name" placeholder="<?php echo $entry_address_1; ?>" value="<?php echo $payment_address_1; ?>" name="address_1">
                <?php $old_add_detail['shipping']['address_1'] = $payment_address_1; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="input-name" class="col-sm-2 control-label"><?php echo $text_address_2; ?></label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="input-name" placeholder="<?php echo $entry_address_2; ?>" value="<?php echo $payment_address_2; ?>" name="address_2">
                <?php $old_add_detail['shipping']['address_2'] = $payment_address_2; ?>
            </div>
        </div>


        <div class="form-group">
            <label for="input-name" class="col-sm-2 control-label"><?php echo $text_country; ?></label>
            <div class="col-sm-10">
                <select name="country" class="form-control" id="input-country">
                    <?php foreach($countries as $country){ ?>
                        <?php if($country['country_id'] == $payment_country_id){ ?>
                            <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name'];?></option>
                        <?php }else{ ?>
                            <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name'];?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <?php $old_add_detail['shipping']['country'] = $payment_country_id; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="input-name" class="col-sm-2 control-label"><?php echo $text_zone; ?></label>
            <div class="col-sm-10">
                <select name="zone" id="input-zone" class="form-control">
                    <?php foreach($zones as $zone){ ?>
                        <?php if($zone['zone_id'] == $payment_zone_id){ ?>
                            <option value="<?php echo $zone['zone_id']; ?>" selected="selected"><?php echo $zone['name']; ?></option>
                        <?php }else{ ?>
                            <option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['name']; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <?php $old_add_detail['shipping']['zone'] = $payment_zone_id; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="input-name" class="col-sm-2 control-label"><?php echo $text_city; ?></label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="input-name" placeholder="<?php echo $entry_city; ?>" value="<?php echo $payment_city; ?>" name="city">
                <?php $old_add_detail['shipping']['city'] = $payment_city; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="input-name" class="col-sm-2 control-label"><?php echo $text_postcode; ?></label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="input-name" placeholder="<?php echo $entry_postcode; ?>" value="<?php echo $payment_postcode;?>" name="postcode">
                <?php $old_add_detail['shipping']['postcode'] = $payment_postcode; ?>
            </div>
        </div>

        <?php foreach ($custom_fields as $custom_field) { ?>
            <?php if((int) $custom_field['custom_field_id'] == 1) {
                    continue;
                    /*
                    $custom_field['name'] = (!empty($gst) && $gst) ? "TIN / GST Number" : $custom_field['name'];
                    $custom_field['value'] = (!empty($gst) && $gst) ? $gst_number : $custom_field['value'];
                    $payment_custom_field[$custom_field['custom_field_id']] = (!empty($gst) && $gst) ? $gst_number : $payment_custom_field[$custom_field['custom_field_id']];
                    */
                }
        
            ?>
            <?php if ($custom_field['location'] == 'address') { ?>
                <?php if ($custom_field['type'] == 'text') { ?>
                        <div class="form-group custom-field" data-sort="<?php echo $custom_field['sort_order']; ?>">
                            <label class="col-sm-2 control-label" for="input-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($payment_custom_field[$custom_field['custom_field_id']]) ? $payment_custom_field[$custom_field['custom_field_id']] : $custom_field['value']); ?>" placeholder="<?php echo $custom_field['name']; ?>" id="input-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control" />
                                <!-- <input type="hidden" name="gst" value="<?php echo (isset($gst) && $gst) ? $gst : 0; ?>"  /> -->
                                <?php $old_add_detail['shipping']['gst'] =(isset($gst) && $gst) ? $gst : 0; ?>
                            </div>
                        </div>
                        <?php $old_add_detail['shipping']['custom_field'.$custom_field['custom_field_id']] = (isset($payment_custom_field[$custom_field['custom_field_id']]) ? $payment_custom_field[$custom_field['custom_field_id']] : $custom_field['value']); ?>
                <?php } ?>
            <?php } ?>
        <?php } ?>
        <?php $old_data = serialize($old_add_detail); ?>
        <div class="form-group">
            <div class="buttons">
                <div class="pull-left">
                    <input type="button" class="btn btn-warning back_button" value="<?php echo $text_back; ?>">
                </div>
                <div class="pull-right">
                    <input type="hidden" name="old_data" value='<?php echo $old_data; ?>'  />
                    
                    <input type="submit" id="continue_submit" class="btn btn-primary" value="<?php echo $text_continue; ?>">
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    
    /* This function validates for GSTIN */
    /*function gstin_validatation(textObj){
        var reggstin = /^([0-9]){2}([A-Z]){3}([C,P,H,F,A,T,B,L,J,G,E]){1}([A-Z]){1}([0-9]){4}([A-Z]){1}([A-Z0-9]){1}([A-Z]){1}([A-Z0-9]){1}?$/;

        if (textObj!=="") {
            if(reggstin.test(textObj) == false) {
                alert("Invalid GST Number");
               return false;
            }
        }
        return true;
    } 
    $('#continue_submit').click(function() {
        var gst_number = $("#input-custom-field1").val();
        if(gst_number != '') {
            if(gstin_validatation(gst_number)==false) {
                return false;
            } else {
                return true;
            }
        }
    });
    */
    $('.back_button').click(function(){
        $('.edit_address_detail_popup').hide();
    });

    $('select[name="country"]').on('change', function() {
        country_id = this.value;
        $.ajax({
            type:'post',
            url:'index.php?route=sale/order/getZones&token=<?php echo $token; ?>',
            data:{country_id},
            dataType:'json',
            success: function(json) {
                html = '<option value=""><?php echo $text_select; ?></option>';

                if (json['zones'] && json['zones'] != '') {
                    for (i = 0; i < json['zones'].length; i++) {
                        html += '<option value="' + json['zones'][i]['zone_id'] + '"';

                        if (json['zones'][i]['zone_id'] == '<?php echo $payment_zone_id; ?>') {
                            html += ' selected="selected"';
                        }

                        html += '>' + json['zones'][i]['name'] + '</option>';
                    }

                } else {
                    html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
                }
                $('select[name=\'zone\']').html(html);
            }
        });
    });



</script>
