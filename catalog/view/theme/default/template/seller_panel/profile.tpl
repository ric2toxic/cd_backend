<?php echo $header; ?>
<div id="page-wrapper">
<?php if($seller_data['seller_status'] == 1 && empty($seller_data['gst_provisional_id'])){ ?>
<div class="gst_block_show">
    <p class="gst_block_show_close pull-right"><i class="fa fa-times"></i></p>
    <p><strong><?php echo $label_gst_popup;?></strong></p>
    <button class="btn btn-warning btn-xs pull-right gst_click_here">Click Here >> </button>
</div>
<?php } ?>
    <div class="col-sm-12">
        <!-- <div class="col-lg-12"> -->
            <h1 class="page_title"><?php echo $heading_title; ?></h1>
        <!-- </div> -->
    </div>
    <div class="col-sm-12 profile_tabs">
        <ul class="nav nav-tabs profile_tebination">
            <li class="active"><a href="#Basic" data-toggle="tab" aria-expanded="false"><?php echo $tab_basic; ?></a></li>
            <li><a href="#Business" data-toggle="tab" aria-expanded="false"><?php echo $tab_business; ?></a></li>
            <!-- <li class=""><a href="#Agreement" data-toggle="tab" aria-expanded="false">Agreement</a></li> -->
        </ul>
        <div class="col-sm-12 profile_detail">
          <div class="tab-content">
            <div id="Basic" class="tab-pane fade in active">
                <div class=" panel-default row">
                    <!-- /.panel-heading -->
                    <div class="panel-body panel-group" id="accordion">
                        <input type="hidden" name = "seller_id" value ="<?php echo $data['seller_data']['seller_id']; ?>" >
                        <form class="form-horizontal">
                            <div class="fieldset_class">
                                <fieldset for="basic_info" class="basic_information">
                                    <legend data-toggle="collapse" data-target="#basic_info"><h4><i class="fa fa-user fa-fw"></i> <?php echo $legend_basic_info; ?> 
                                     <i class="more-less fa fa-minus-square-o" aria-hidden="true"></i> 
                                     </h4>
                                     </legend>
                                    <div id="basic_info" class="collapse in">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label profile_label_left"><?php echo $label_primary_person; ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control input_box_disabled text_capitalize" id="primary_contact_name" name="primary_contact_name" disabled value="<?php echo $data['seller_data']['primary_contact_name']; ?>">  
                                                <input type="hidden" class="primary_contact_name" value="<?php echo $data['seller_data']['primary_contact_name']; ?>" >
                                            </div>
                                            <div class="col-sm-3">
                                                <button type="button" class="btn btn-warning btn-sm form_edit btn_edit_primary_contact_name" data-id="primary_contact_name"> <?php echo $btn_change; ?> </button>
                                                <button type="button" style="display:none" class="btn btn-success btn-sm form-done primary_contact_name" onclick="inerstValue('primary_contact_name')"> <?php echo $btn_save; ?> </button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label profile_label_left"><?php echo $label_primary_contact; ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control input_box_disabled text_capitalize" id="primary_contact_no" name="primary_contact_no" disabled value="<?php echo $data['seller_data']['primary_contact_no']; ?>" maxLength="10">
                                                <input type="hidden" class="primary_contact_no" value="<?php echo $data['seller_data']['primary_contact_no']; ?>" >
                                            </div>    
                                            <div class="col-sm-3">    
                                                <button type="button" class="btn btn-warning btn-sm form_edit btn_edit_primary_contact_no" data-id="primary_contact_no"><?php echo $btn_change; ?></button>
                                                <button type="button" style="display:none" class="btn btn-success btn-sm form-done primary_contact_no" onclick="inerstValue('primary_contact_no')" ><?php echo $btn_save; ?></button>
                                            </div>
                                            <hr>
                                        </div>
                                        <div class="form-group">
                                            <label  class="col-sm-3 control-label profile_label_left"><?php echo $label_email; ?></label>
                                            <div class="col-sm-9">
                                                <!-- <span><?php echo $data['seller_data']['email']; ?></span> -->
                                                <input type="text" class="form-control input_box_disabled text_capitalize" disabled name="email" value="<?php echo $data['seller_data']['email']; ?>">
                                                <input type="hidden" class="email" value="<?php echo $data['seller_data']['email']; ?>" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label profile_label_left"><?php echo $label_additional_email; ?></label>
                                            <div class="col-sm-6 col_sm_div6">
                                                <?php if ( empty($data['seller_data']['additional_emails']) ){  ?>
                                                    <span class="empty_add_email col-sm-12">
                                                      <a href="javascript:void(0);" class="click_here_edit"><?php echo $label_click_here; ?></a>
                                                    </span>
                                                <?php } ?>
                                                <div class="additional_email_group">
                                                    
                                                    <?php $additional_emails = explode(',',$data['seller_data']['additional_emails']); ?>
                                                    <?php $add_email_count = 0; foreach( $additional_emails as $emails ){ ?>
                                                        <div id="additional_emails_block<?php echo $add_email_count;?>" class="additional_emails_block">
                                                        <input type="text" 
                                                                class="form-control input_box_disabled additional_emails_input" 
                                                                name="additional_emails[]" 
                                                                value="<?php echo $emails; ?>" 
                                                                id="additional_emails_box<?php echo $add_email_count;?>" disabled />
                                                            <button type="button" 
                                                                    onclick="removeInput(this)" data-toggle="tooltip" 
                                                                    title="<?php echo $button_remove; ?>" 
                                                                    class="btn btn-danger btn-sm add_email_remove_btn hidden">
                                                                <i class="fa fa-minus-circle"></i>
                                                            </button>
                                                            
                                                        </div>
                                                        <?php $add_email_count++;  ?>
                                                    <?php } ?>

                                                   <!--<textarea class="form-control input_box_disabled text_capitalize hidden" rows="3" cols="55" id="additional_emails" disabled><?php echo $data['seller_data']['additional_emails']; ?></textarea><br/> -->
                                                    <!-- <span>Please enter comma seperated additional emails. Do not use Space and enter. </span> -->
                                                </div>
                                                 
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="hidden" class="additional_emails" value="<?php echo $data['seller_data']['additional_emails']; ?>" >
                                                <button type="button" class="btn btn-primary btn-sm add_email_add_btn hidden" onClick="addAdditionalEmail();"><i class="fa fa-plus-circle"></i></button>
                                                <button type="button" class="btn btn-warning hidden btn-sm form_edit btn_edit_additional_emails" data-id="additional_emails"><?php echo $btn_change; ?></button>
                                                <button type="button" style="display:none;" class="btn btn-success btn-sm form-done additional_emails btn_save_additional_emails" onclick="inerstValue('additional_emails')" ><?php echo $btn_save; ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="fieldset_class">
                                <fieldset for="primary_address" class="basic_information">
                                    <legend data-toggle="collapse" data-target="#legend_address">
                                        <h4><i class="fa fa-map-marker fa-fw"></i> 
                                            <?php echo $legend_address; ?> 
                                            <i class="more-less fa fa-plus-square-o" aria-hidden="true"></i>
                                            <br>
                                            <span class="text_sub_coordinator"> ( <?php echo $legend_address_sub; ?> ) </span>
                                        </h4>
                                    </legend>
                                    <div id="legend_address" class="collapse">
                                        <div class="form-group">
                                            <label class="tin_address_message col-sm-12"></label>
                                            <label class="col-sm-2 control-label profile_label_left"><?php echo $label_Pincode; ?></label>
                                            <div class="col-sm-7">
                                                <input class="form-control input_box_disabled text_capitalize" id="pincode" maxlength="6"  disabled value="<?php echo $data['seller_data']['pincode']; ?>">
                                                <input type="hidden" class="pincode" value="<?php echo $data['seller_data']['pincode']; ?>" >
                                            </div>
                                            <div class="col-sm-3">
                                                <button type="button" class="btn btn-warning btn-sm primary_address_edit" onclick="editAddressAndBankDetails();"><?php echo $btn_change; ?></button>
                                                <button type="button" style=" display:none;" class="btn btn-success btn-sm form-done address1" onclick="updateSellerBankDetailsAndAddress('address_details')" ><?php echo $btn_save; ?></button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label profile_label_left"><?php echo $label_city; ?></label>
                                            <div class="col-sm-3">
                                                <input class="form-control input_box_disabled text_capitalize" id="city"  disabled value="<?php echo $data['seller_data']['city']; ?>">
                                                <input type="hidden" class="city" value="<?php echo $data['seller_data']['city']; ?>" >
                                            </div>    
                                            <label class="col-sm-1 control-label profile_label_left"><?php echo $label_state; ?></label>
                                            <div class="col-sm-3">
                                                <select disabled class="form-control input_box_disabled text_capitalize classic" id="zone_id"  name="seller_zone">
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
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" name="seller_change_update_id" value="0">
                                            <label class="col-sm-2 control-label profile_label_left"><?php echo $label_address; ?></label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control input_box_disabled" rows="3" cols="55" id="address1" disabled><?php echo $data['seller_data']['address1'] . ''. $data['seller_data']['address2']; ?></textarea>
                                                <input type="hidden" class="address1" value="<?php echo $data['seller_data']['address1'] .''. $data['seller_data']['address2']; ?>" >
                                            </div>
                                        </div> 
                                  </div>   
                                </fieldset>
                            </div>    

                            <div class="fieldset_class">
                                <fieldset for="pickup_person" class="basic_information">
                                    <legend data-toggle="collapse" data-target="#legend_pickup">
                                        <h4>
                                            <?php echo $legend_pickup; ?>
                                            <i class="more-less fa fa-plus-square-o" aria-hidden="true"></i>
                                            <br>
                                            <sub class="text_sub_coordinator">( <?php echo $legend_pickup_sub; ?> )</sub>
                                        </h4>
                                    </legend>
                                    <div id="legend_pickup" class="collapse">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <input type="checkbox" id="same_as_primary_pickup" <?php if($data['seller_data']['same_as_primary_pickup'] == 1){ echo 'checked';}?> onclick="sameAsPrimary('same_as_primary_pickup')">
                                                <input type="hidden" class="same_as_primary_pickup" value="<?php echo $data['seller_data']['same_as_primary_pickup']; ?>" >
                                                <span class="profile_label_left same_as_class_prop"><?php echo $label_same_as; ?></span>
                                            </div>
                                            <label class="col-sm-3 control-label profile_label_left"><?php echo $label_pickup_name; ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control input_box_disabled text_capitalize" id="pickup_holder_name" name="pickup_holder_name" disabled value="<?php echo $data['seller_data']['pickup_holder_name']; ?>">
                                                <input type="hidden" class="pickup_holder_name" value="<?php echo $data['seller_data']['pickup_holder_name']; ?>" >
                                            </div>
                                            <div class="col-sm-3">
                                                <?php if($data['seller_data']['same_as_primary_pickup'] == 1){
                                                    $style = 'style="display:none"';
                                                }
                                                else{
                                                    $style = '';
                                                }
                                                ?>
                                                <button type="button" class="btn btn-warning btn-sm form_edit btn_edit_pickup_holder_name" <?php echo $style;?> data-id="pickup_holder_name"><?php echo $btn_change; ?></button> 
                                                <button type="button" style="display:none" class="btn btn-success btn-sm form-done pickup_holder_name" onclick="inerstValue('pickup_holder_name')" ><?php echo $btn_save;?></button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label profile_label_left"><?php echo $label_pickup_contact; ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control input_box_disabled text_capitalize text_capitalize" id="pickup_contact_no" name="Contact number" disabled value="<?php echo $data['seller_data']['pickup_contact_no']; ?>" maxlength="10">
                                                <input type="hidden" class="pickup_contact_no" value="<?php echo $data['seller_data']['pickup_contact_no']; ?>" >
                                            </div>
                                            <div class="col-sm-3">
                                                <?php 
                                                    if($data['seller_data']['same_as_primary_pickup'] == 1){
                                                        $style = 'style="display:none"';
                                                    } else {
                                                        $style = '';
                                                    }
                                                ?>                                        
                                                <button type="button" class="btn btn-warning btn-sm form_edit btn_edit_pickup_contact_no" <?php echo $style;?> data-id="pickup_contact_no"><?php echo $btn_change; ?></button> 
                                                <button type="button" style="display:none" class="btn btn-success btn-sm form-done pickup_contact_no" onclick="inerstValue('pickup_contact_no')" ><?php echo $btn_save; ?></button>
                                            </div>    
                                        </div>
                                    </div>

                                </fieldset> 
                            </div>

                            <div class="fieldset_class">
                                <fieldset for="pickup_address" class="basic_information">
                                    <legend data-toggle="collapse" data-target="#legend_pickup_address">
                                        <h4>
                                            <i class="fa fa-map-marker fa-fw"></i>
                                            <?php echo $legend_pickup_address; ?>
                                             <i class="more-less fa fa-plus-square-o" aria-hidden="true"></i>
                                            <br>
                                            <sub class="text_sub_coordinator">(<?php echo $legend_pickup_add_sub; ?> )</sub>
                                        </h4>    
                                    </legend>
                                    <div id="legend_pickup_address" class="collapse">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <input type="checkbox"id="same_as_primary_address" <?php if($data['seller_data']['same_as_primary_address'] == 1){ echo 'checked';}?> onclick="sameAsPrimary('same_as_primary_address')">
                                                <input type="hidden" class="same_as_primary_address" value="<?php echo $data['seller_data']['same_as_primary_address']; ?>" >
                                                <span class="profile_label_left same_as_class_prop"><?php echo $label_pickup_address_same_as; ?></span>
                                            </div>
                                            <label class="col-sm-2 control-label profile_label_left"><?php echo $label_pickup_pincode; ?></label>
                                            <div class="col-sm-7">
                                                <input class="form-control input_box_disabled text_capitalize" id="pickup_pincode" maxlength="6"  disabled value="<?php echo $data['seller_data']['pickup_pincode']; ?>">
                                                <input type="hidden" class="pickup_pincode" value="<?php echo $data['seller_data']['pickup_pincode']; ?>" >
                                            </div>
                                            <div class="col-sm-3">
                                                <?php 
                                                    if($data['seller_data']['same_as_primary_address'] == 1){
                                                        $address_edit_button_style = 'style="display:none;"';
                                                    } else {
                                                        $address_edit_button_style = 'style=""';
                                                    }
                                                ?>
                                                <button type="button" <?php echo $address_edit_button_style; ?> class="btn btn-warning btn-sm btn_edit_pickup_address" onclick="editAddressAndBankDetails('pickup_address');" data-id="pickup_address" ><?php echo $btn_change?></button> 
                                                <button type="button" style=" display:none;" class="btn btn-success btn-sm form-done pickup_address" onclick="updateSellerBankDetailsAndAddress('pickup_address')" ><?php echo $btn_save; ?></button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label profile_label_left"><?php echo $label_pickup_city; ?></label>
                                            <div class="col-sm-3">
                                                <input class="form-control input_box_disabled text_capitalize" id="pickup_city"  disabled value="<?php echo $data['seller_data']['pickup_city']; ?>">
                                                <input type="hidden" class="pickup_city" value="<?php echo $data['seller_data']['pickup_city']; ?>" >
                                            </div>
                                            <label class="col-sm-2 control-label profile_label_left"><?php echo $label_pickup_state; ?></label>
                                            <div class="col-sm-3">
                                                <select disabled class="form-control input_box_disabled text_capitalize classic" id="pickup_zone_id"  name="pickup_zone_id">
                                                    <?php 
                                                        foreach($zones as $values){
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
                                        </div>                                  
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label profile_label_left"><?php echo $label_pickup_address; ?></label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control input_box_disabled text_capitalize" rows="3" cols="33" id="pickup_address" class="form-control" disabled><?php echo $data['seller_data']['pickup_address']; ?></textarea>
                                                <input type="hidden" class="pickup_address" value="<?php echo $data['seller_data']['pickup_address']; ?>" >            
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="fieldset_class">
                                <fieldset for="account_person" class="basic_information">
                                    <legend data-toggle="collapse" data-target="#legend_account_sub">
                                        <h4>
                                            <?php echo $legend_account; ?>
                                             <i class="more-less fa fa-plus-square-o" aria-hidden="true"></i>
                                            <br>
                                            <sub class="text_sub_coordinator">( <?php echo $legend_account_sub; ?> )</sub>
                                        </h4>
                                    </legend>
                                     <div id="legend_account_sub" class="collapse">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <input type="checkbox"id="same_as_primary_account" <?php if($data['seller_data']['same_as_primary_account'] == 1){ echo 'checked';}?>   onclick="sameAsPrimary('same_as_primary_account')">
                                                <input type="hidden" class="same_as_primary_account" value="<?php echo $data['seller_data']['same_as_primary_account']; ?>" >
                                                <span class="profile_label_left same_as_class_prop"> <?php echo $label_same_as;?> </span>
                                            </div>
                                            <label class="col-sm-4 control-label profile_label_left"><?php echo $label_account_name; ?></label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control input_box_disabled text_capitalize" id="account_holder_name" name="account_holder_name" disabled value="<?php echo $data['seller_data']['account_holder_name']; ?>">
                                                <input type="hidden" class="account_holder_name" value="<?php echo $data['seller_data']['account_holder_name']; ?>" >
                                            </div>
                                            <div class="col-sm-3">
                                                <?php 
                                                    if($data['seller_data']['same_as_primary_account'] == 1){
                                                        $style = 'style="display:none"';
                                                    } else {
                                                        $style = '';
                                                    }
                                                ?>
                                                <button type="button" class="btn btn-warning btn-sm form_edit btn_edit_account_holder_name" <?php echo $style;?> data-id="account_holder_name"><?php echo $btn_change; ?></button>
                                                <button type="button" style="display:none" class="btn btn-success btn-sm form-done account_holder_name" onclick="inerstValue('account_holder_name')" ><?php echo $btn_save;?></button>
                                            </div>    
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label profile_label_left"><?php echo $label_account_contact; ?></label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control input_box_disabled text_capitalize" id="account_contact_no" name="account_contact_no" disabled value="<?php echo $data['seller_data']['account_contact_no']; ?>" maxlength="10">
                                                <input type="hidden" class="account_contact_no" value="<?php echo $data['seller_data']['account_contact_no']; ?>" >
                                            </div>
                                            <div class="col-sm-3">
                                                <button type="button" class="btn btn-warning btn-sm form_edit btn_edit_account_contact_no" <?php echo $style;?> data-id="account_contact_no"><?php echo $btn_change; ?></button>
                                                <button type="button" style="display:none" class="btn btn-success btn-sm form-done account_contact_no" onclick="inerstValue('account_contact_no')" ><?php echo $btn_save; ?></button>
                                            </div>    
                                        </div>
                                    </div>      
                                </fieldset>
                            </div>

                            <div class="fieldset_class">
                                <fieldset for="inventory_person" class="basic_information">
                                    <legend data-toggle="collapse" data-target="#legend_inventory_sub">
                                       <h4>
                                           <?php echo $legend_inventory; ?>
                                           <i class="more-less fa fa-plus-square-o" aria-hidden="true"></i>
                                           <br>
                                           <sub class="text_sub_coordinator">( <?php echo $legend_inventory_sub; ?> )</sub>
                                       </h4> 
                                    </legend>
                                    <div id="legend_inventory_sub" class="collapse">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <input type="checkbox"id="same_as_primary_inventory" <?php if($data['seller_data']['same_as_primary_inventory'] == 1){ echo 'checked';}?> onclick="sameAsPrimary('same_as_primary_inventory')">
                                                <input type="hidden" class="same_as_primary_inventory" value="<?php echo $data['seller_data']['same_as_primary_inventory']; ?>" >
                                                <span class="profile_label_left same_as_class_prop"> <?php echo $label_same_as; ?> </span>
                                            </div>
                                            <label class="col-sm-4 control-label profile_label_left"><?php echo $label_inventory_name; ?></label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control input_box_disabled text_capitalize" id="inventory_holder_name" name="inventory_holder_namer" disabled value="<?php echo $data['seller_data']['inventory_holder_name']; ?>">
                                                <input type="hidden" class="inventory_holder_name" value="<?php echo $data['seller_data']['inventory_holder_name']; ?>" >
                                            </div>
                                            <div class="col-sm-3">    
                                                <?php 
                                                    if($data['seller_data']['same_as_primary_inventory'] == 1){
                                                        $style = 'style="display:none"';
                                                    } else {
                                                        $style = '';
                                                    }
                                                ?>
                                                <button type="button" class="btn btn-warning btn-sm form_edit btn_edit_inventory_holder_name" <?php echo $style; ?> data-id="inventory_holder_name"><?php echo $btn_change; ?></button>
                                                <button type="button" style="display:none" class="btn btn-success btn-sm form-done inventory_holder_name" onclick="inerstValue('inventory_holder_name')" ><?php echo $btn_save;?></button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 profile_label_left"><?php echo $label_inventory_contact; ?></label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control input_box_disabled text_capitalize" id="inventory_contact_no" name="inventory_contact_no" disabled value="<?php echo $data['seller_data']['inventory_contact_no']; ?>" maxlength="10">
                                                <input type="hidden" class="inventory_contact_no" value="<?php echo $data['seller_data']['inventory_contact_no']; ?>" >
                                            </div>
                                            <div class="col-sm-3">    
                                                <button type="button" class="btn btn-warning btn-sm form_edit btn_edit_inventory_contact_no" <?php echo $style; ?> data-id="inventory_contact_no"><?php echo $btn_change;?></button> 
                                                <button type="button" style="display:none" class="btn btn-success btn-sm form-done inventory_contact_no" onclick="inerstValue('inventory_contact_no')" ><?php echo $btn_save; ?></button>
                                            </div>  
                                        </div> 
                                    </div>         
                                </fieldset> 
                            </div>

                        </form>
                         <div class="clearfix"></div>
                    </div>
                    <!-- /.panel-body -->
                    <div class="clearfix"></div>
                </div>
            </div>
            <div id="Business" class="tab-pane fade">
                <div class=" panel-default row">
                    <!-- /.panel-heading -->
                    <div class="panel-body panel-group" id="accordion">
                        <form class="form-horizontal">
                            <div class="fieldset_class">
                                <fieldset for="business_info" class="basic_information">
                                    <legend data-toggle="collapse" data-target="#legend_business">
                                        <h4 class="text-center clearfix">
                                            <span class="pull-left">
                                            <i class="fa fa-bar-chart-o fa-fw"></i>
                                            <?php echo $legend_business; ?>
                                            </span>
                                            <?php if( !is_numeric($data['seller_data']['nickname']) ) { ?>
                                                <span class="seller_nickname">
                                                    <?php echo $legend_seller_nickname; ?>
                                                    <span class="nick_name"><?php echo $data['seller_data']['nickname']; ?></span>
                                                </span>
                                            <?php } ?>
                                            <i class="more-less fa fa-minus-square-o" aria-hidden="true"></i>
                                        </h4>    
                                    </legend>
                                    <div id="legend_business" class="collapse in">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label profile_label_left"><?php echo $label_company_name; ?></label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control input_box_disabled text_capitalize" id="company" name="company" disabled value="<?php echo $data['seller_data']['company']; ?>">
                                                <input type="hidden" class="company" value="<?php echo $data['seller_data']['company']; ?>" >
                                            </div>
                                            <?php /* ?><!--<div class="col-sm-3">
                                                <button type="button" class="btn btn-warning btn-sm form_edit btn_edit_company" data-id="company"><?php echo $btn_change; ?></button>
                                                <button type="button" style="display:none" class="btn btn-success btn-sm form-done company" onclick="inerstValue('company')" ><?php echo $btn_save; ?></button>
                                            </div>  --><?php */ ?>
                                        </div>
                                        <div class="gst_block">
                                            <div class="col-sm-12">
                                                <p class="text-center"><strong><?php echo $legend_gst_detail; ?></strong></p><br>
                                            </div>
                                            <div class="form-group required">
                                                <div id="gst_details-alert-message"></div><br>
                                                <label class="col-sm-4 control-label profile_label_left"><?php echo $label_gst_provision_id; ?>   </label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control input_box_disabled" id="gst_provision_id" name="gst_provision_id" disabled value="<?php echo $data['seller_data']['gst_provisional_id']; ?>">
                                                    <input type="hidden" class="gst_provision_id" value="<?php echo $data['seller_data']['gst_provisional_id']; ?>" >
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-warning btn-sm btn_edit_gst_details"  onclick="editAddressAndBankDetails('gst_details');" ><?php echo $btn_change; ?></button>
                                                    <button type="button" style="display:none;" class="btn btn-success btn-sm form-done gst_details" onclick="updateSellerBankDetailsAndAddress('gst_details')" ><?php echo $btn_save; ?></button>
                                                </div>      
                                            </div>
                                            <div class="form-group required">
                                                <label class="col-sm-4 control-label profile_label_left"><?php echo $label_gst_certificate; ?></label>
                                                <div class="col-sm-5">
                                                    <input type="file" disabled id="gst_certificate_upload" class="input_box_disabled">
                                                    <input type="hidden" class="gst_certificate_upload" value= "<?php echo $data['seller_data']['gst_certificate_upload']; ?>" >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label profile_label_left"><?php echo $label_gst_arn; ?></label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control input_box_disabled" id="gst_arn" name="gst_arn" disabled value="<?php echo $data['seller_data']['gst_arn']; ?>">
                                                    <input type="hidden" class="gst_arn" value="<?php echo $data['seller_data']['gst_arn']; ?>" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div id="pan-alert-message" class="profile_label_left"></div><br>
                                            <p class="text_message">
                                                <i class="fa fa-arrow-circle-right"></i> <?php echo $pan_message; ?>
                                            </p>
                                            <label class="col-sm-4 control-label profile_label_left"><?php echo $label_pan; ?></label>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control input_box_disabled text_capitalize" id="pan" onblur="enabledInputTypeFile('pan')" name="pan" disabled value="<?php echo $data['seller_data']['pan']; ?>" maxLength="10">
                                                <input type="hidden" class="pan" value="<?php echo $data['seller_data']['pan']; ?>" >
                                            </div>
                                            <!--<div class="col-sm-3">
                                                <input type="file" id="pan_upload" name="pan_upload" disabled class="input_box_disabled text_capitalize">
                                                <div class="upload_pan_number_error"></div>
                                                <input type="hidden" id="pan_image" value= "<?php //echo $data['seller_data']['pan_image']; ?>">
                                            </div>
                                            <div class="col-sm-3">
                                                <button type="button" class="btn btn-warning btn-sm form_edit btn_edit_pan" data-id="pan"><?php //echo $btn_change; ?></button> 
                                                <button type="button" style="display:none;" class="btn btn-success btn-sm form-done pan" onclick="inerstValue('pan')" ><?php //echo $btn_save; ?></button>
                                            </div> -->
                                        </div>
                                        <?php /* ?><!--
                                        <div class="form-group">
                                            <div id="tin-alert-message"></div> <br/>
                                            <p class="text_message">
                                                <i class="fa fa-arrow-circle-right"></i> <?php echo $tin_type_category_message; ?>
                                            </p>
                                            <label class="col-sm-2 control-label profile_label_left"><?php echo $label_type_of_goods; ?></label>
                                            <div class="col-sm-7">
                                                <?php $tin_type = explode(",",$data['seller_data']['tin_tax_type']);?>
                                                <ul class="tin_type_box">
                                                    <li>
                                                        <label class="control-label">
                                                            <input type="checkbox" id="tin_tax_free"<?php if(in_array('1',$tin_type)){ echo "checked";}?> value="1" class="tin_taxs_checkbox">
                                                            <span style="vertical-align: top"><?php echo $label_tax_fee; ?></span>
                                                        </label>
                                                    </li>
                                                    <li>    
                                                        <label class="control-label">
                                                            <input type="checkbox" id="tin_taxable" <?php if(in_array('2',$tin_type)){ echo "checked";}?> value="2" class="tin_taxs_checkbox">
                                                            <span style="vertical-align: top"><?php echo $label_taxable; ?></span>
                                                        </label>
                                                    </li>
                                                    <li>    
                                                        <label class="control-label">
                                                            <input type="checkbox" id="tin_composite" <?php if(in_array('3',$tin_type)){ echo "checked";}?> value="3" class="tin_taxs_checkbox">
                                                            <span style="vertical-align: top"><?php echo $label_composite; ?></span>  
                                                        </label>
                                                    </li>
                                                </ul>     
                                                <input type="hidden" id="old_tin_tax_type" value="<?php echo $data['seller_data']['tin_tax_type']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group tin_block">
                                            <p class="text_message">
                                                <i class="fa fa-arrow-circle-right"></i> <?php echo $taxable_selected_message; ?>
                                            </p>
                                            <?php
                                                if(in_array('1',$tin_type) && !in_array('2',$tin_type) && !in_array('3',$tin_type)) {
                                                    $style = "style= display:none;";
                                                } else {
                                                    $style = '';
                                                }
                                            ?>
                                            <label  class="col-sm-2 control-label profile_label_left"><?php echo $label_tin_number; ?></label>
                                            <div class="col-sm-4">
                                                <input type=text class="form-control input_box_disabled text_capitalize" id="tin" onblur="enabledInputTypeFile('tin')" name="tin" disabled value="<?php echo $data['seller_data']['tin']; ?>">
                                                <input type="hidden" class="tin" value="<?php echo $data['seller_data']['tin']; ?>" >
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="file" id="tin_upload" disabled class="input_box_disabled text_capitalize">
                                                <div class="upload_tin_number_error"></div>
                                                <input type="hidden" id="tin_image" value= "<?php echo $data['seller_data']['tin_image']; ?>">
                                            </div>
                                            <div class="col-sm-3">
                                                <?php
                                                    if(in_array('1',$tin_type) && !in_array('2',$tin_type) && !in_array('3',$tin_type)) {
                                                        $style = "display:none;";
                                                    } else {
                                                        $style = '';
                                                    }
                                                ?>
                                                <button type="button" class="btn btn-warning btn-sm form_edit btn_edit_tin <?php echo $style;?>" data-id="tin"><?php echo $btn_change; ?></button>
                                                <button type="button" style="display: none;" class="btn btn-success btn-sm form-done tin btn_tin_save" onclick="inerstValue('tin')" ><?php echo $btn_save; ?></button>
                                            </div>
                                        </div>
                                        --> <?php */ ?>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="fieldset_class">    
                                <fieldset for="bank_details" class="basic_information">
                                    <legend data-toggle="collapse" data-target="#legend_bank_detail">
                                        <h4 >
                                            <i class="fa fa-university fa-fw"></i>
                                            <?php echo $legend_bank_detail; ?>
                                            
                                            <i class="more-less  fa fa-plus-square-o" aria-hidden="true"></i>
                                            <br>
                                            <span class="text_sub_coordinator"> ( <?php echo $bank_detail_message; ?> ) </span>
                                        </h4>
                                    </legend>
                                    <div id="legend_bank_detail" class="collapse">
                                        <div class="form-group">
                                            <div id="bank_details-alert-message"></div><br>
                                            <label class="col-sm-4 control-label profile_label_left"><?php echo $label_account_hol_name; ?></label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control input_box_disabled text_capitalize" id="bank_ac_holder_name" name="bank_ac_holder_name" disabled value="<?php echo $data['seller_data']['bank_ac_holder_name']; ?>">
                                                <input type="hidden" class="bank_ac_holder_name" value="<?php echo $data['seller_data']['bank_ac_holder_name']; ?>" >
                                            </div>
                                            <div class="col-sm-3">
                                                <button type="button" class="btn btn-warning btn-sm btn_edit_bank_detail"  onclick="editAddressAndBankDetails('bank_details')" ><?php echo $btn_change; ?></button>
                                                <button type="button" style="display:none;" class="btn btn-success btn-sm form-done bank_details" onclick="updateSellerBankDetailsAndAddress('bank_details')" ><?php echo $btn_save; ?></button>
                                            </div>    
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label profile_label_left"><?php echo $label_account_number; ?></label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control input_box_disabled text_capitalize" id="bank_ac_number" name="bank_ac_number" onblur="enabledInputTypeFile('acnt')" disabled value="<?php echo $data['seller_data']['bank_ac_number']; ?>">
                                                <input type="hidden" class="bank_ac_number" value="<?php echo $data['seller_data']['bank_ac_number']; ?>" >
                                            </div>    
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label profile_label_left"><?php echo $label_account_cheque; ?></label>
                                            <div class="col-sm-5">
                                                <input type="file" disabled id="upload_cancel_cheque" class="input_box_disabled text_capitalize">
                                                <input type="hidden" id="cancel_cheque_image" value= "<?php echo $data['seller_data']['cancel_cheque_image']; ?>" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label profile_label_left"><?php echo $label_account_ifsc_code; ?></label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control input_box_disabled text_capitalize" id="ifsc_code" name="ifsc_code" disabled value="<?php echo $data['seller_data']['ifsc_code']; ?>" onblur="enabledInputTypeFile('acnt')">
                                                <?php /* ?><!--<span class="sample_ifsc_code"><?php echo $ifsc_code_sample; ?></span> --><?php */ ?>
                                                <input type="hidden" class="ifsc_code" value="<?php echo $data['seller_data']['ifsc_code']; ?>" >
                                                <!--<input type="hidden" class="form-control" name="bank_name">
                                                <input type="hidden" class="form-control" name="bank_branch" >
                                                <input type="hidden" class="form-control" name="bank_city" >
                                                <input type="hidden" class="form-control" name="bank_state" >-->
                                                <span style="color:#337ab7;" id="ifsc_code_loading"></span>
                                            </div>    
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-10">
                                                <div class="bank_details_block" style="display: none;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div">
                        </form>    
                    </div>
                    <!-- /.panel-body -->
                </div>
            </div>
            <div id="Agreement" class="tab-pane fade">
              <div class="panel panel-default row">
                    <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i> <?php echo $label_agreement_info; ?>
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
                                <?php echo $text_data_agreement; ?>
                            </p>
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
            </div>
          </div>
      </div>
    </div>
    <!-- /.row -->
</div>
    <div class="clearfix"></div>
<!-- /#page-wrapper -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content col-sm-7">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo $label_update_password; ?> </h4>
      </div>
      <div class="modal-body" style="max-height:450px;overflow-y:auto">
        <fieldset>
            <div class="form-group clearfix">
                <label class="col-sm-4 control-label nopadding" ><?php echo $label_password; ?></label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="password" placeholder="Password">
                </div>
            </div>
            <div class="form-group clearfix">
                <label class="col-sm-4 control-label nopadding"><?php echo $label_confirm_password; ?></label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="confirm_password" id="confirm_password"  placeholder="Confirm Password">
                </div>
            </div>
          </fieldset>
      </div>
      <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="updateSellerPassword" onclick="updateSellerPassword()"><?php echo $btn_submit;?></button>
      </div>
    </div>
  </div>
</div>
<?php
  echo $footer;
?>
<script type="text/javascript">

    var add_emails = $('.additional_emails_block:first').clone();
    var additional_emails_error_value = true;

    function getPincode(pincode,zone_id,city,input_id){
        $.ajax({
            type:'post',
            dataType:'json',
            data:"pincode="+pincode,
            url:'index.php?route=seller_panel/profile/getState',
            success:function(response){
                if(response.state !='empty'){
                  $('#'+input_id).next('span').remove();  
                  $('#'+input_id).removeClass('has-error');  
                  $('#'+input_id).addClass('input_box_enabled');  
                  $("#"+zone_id).val(response.zone_id);
                  $("#"+zone_id).attr('disabled',true);
                  $("#"+zone_id).addClass('input_box_disabled');
                  $("#"+city).val(response.city);
                  $("#"+city).attr('disabled',true);
                  $("#"+city).addClass('input_box_disabled');
              } else{
                  $('#'+input_id).next('span').remove();  
                  $('#'+input_id).removeClass('has-error');  
                  $('#'+input_id).addClass('input_box_enabled'); 
                  $("#"+zone_id).find('option[value=""]').remove();
                  $("#"+zone_id).removeAttr('disabled');
                  $("#"+zone_id).addClass('classic');
                  $("#"+zone_id).removeClass('input_box_disabled');
                  $("#"+city).removeAttr('disabled');
                  var html = "<option value=''>--Select--</option>";
                  $("#"+zone_id).prepend(html);
                  $("#"+zone_id).val('');
              }
            },
        });
    }

    $(document).ready(function(){
        
        if($('.empty_add_email').length > 0 ){
            $('.additional_emails_block').remove();
        } else {
            $('.btn_edit_additional_emails').removeClass('hidden');
        }

        $('.form_edit').on('click',function(){
            var id = $(this).attr('data-id');
            $('#'+id).removeClass('input_box_disabled');
            $('#'+id).addClass('input_box_enabled').focus();
            $('#'+id).removeAttr('disabled');
            // if(id=='tin'){
            //     $("."+id).removeClass('hidden');    
            // }
            // else{
            //     $("."+id).show()   
            // }
            $("."+id).show()   
            $(this).hide();

            if( id == 'additional_emails' ){
                $('.empty_add_email').hide();
                if($('.additional_emails_block').length == 0){
                    $('.additional_email_group').html(add_emails);
                    $('.additional_email_group input').val('');
                }
                $('.add_email_add_btn, .add_email_remove_btn').removeClass('hidden');
                $('.additional_emails_input').each(function(){
                    $(this).removeAttr('disabled');
                    $(this).removeClass('input_box_disabled');
                    $(this).addClass('input_box_enabled');
                });        
            }
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
        
        $("#pincode").on('keyup',function(){
            if($("#pincode").val().length == 6){
                var pincode = $("#pincode").val();
                var CheckZipCode = /(^\d{6}$)/;
                if((!CheckZipCode.test(pincode))){
                    $("#pincode").next('span').remove();
                    $('.primary_address_edit').hide();
                    $("#pincode").addClass('has-error');
                    $("#pincode").removeClass('input_box_enabled');
                    $("#pincode").after('<span class="error"><?php echo $error_pincode_invalid; ?></span>');  
                    return false;
                }
                getPincode(pincode,'zone_id','city','pincode');

            }
        });
        $("#pickup_pincode").on('keyup',function(){
            if($("#pickup_pincode").val().length == 6){
              var pincode = $("#pickup_pincode").val();
              var CheckZipCode = /(^\d{6}$)/;
              if((!CheckZipCode.test(pincode))){
                    $("#pickup_pincode").next('span').remove();
                    $('.btn-sm btn_edit_pickup_address').hide();
                    $("#pickup_pincode").addClass('has-error');
                    $("#pickup_pincode").removeClass('input_box_enabled');
                    $("#pickup_pincode").after('<span class="error"><?php echo $error_pincode_invalid; ?></span>');  
                    return false;
              }
              getPincode(pincode,'pickup_zone_id','pickup_city','pickup_pincode');
            }
        });

        $('.tin_taxs_checkbox').on('click',function(){
          if($("#tin_tax_free").prop("checked") == true  && $("#tin_taxable").prop("checked") == false && $("#tin_composite").prop("checked") == false ) {
            var seller_id        = $("input[name = 'seller_id']").val();
            var old_tin_tax_type = $("#old_tin_tax_type").val();
            $('.tin_block').hide();
            // $('button.tin').hide();
            // $("button[data-id=\'tin\']").hide();
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
            $('.tin_block').show();
            // $("button[data-id=\'tin\']").show();
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
        var update_ifsc_value = $('#ifsc_code').val();
        var old_ifsc_value = $("input.ifsc_code").val();
        if(update_value != old_value || update_ifsc_value != old_ifsc_value){
          $("#upload_cancel_cheque").removeAttr('disabled');
        } else{
          $("#upload_cancel_cheque").attr('disabled','disabled');
        }
      }
    }

    function editAddressAndBankDetails(type){
      if(type == "bank_details"){
        $("#upload_cancel_cheque").next('span').remove();
        $('.btn_edit_bank_detail').hide();
        $(".bank_details").show();
        $("#bank_ac_holder_name").removeClass('input_box_disabled');
        $("#bank_ac_holder_name").removeAttr('disabled');
        $("#bank_ac_holder_name").addClass('input_box_enabled');
        $("#bank_ac_number").removeClass('input_box_disabled');
        $("#bank_ac_number").addClass('input_box_enabled');
        $("#bank_ac_number").removeAttr('disabled');
        $("#ifsc_code").removeClass('input_box_disabled');
        $("#ifsc_code").addClass('input_box_enabled');
        $("#ifsc_code").removeAttr('disabled');
        $('.bank_details').removeAttr('disabled');
        return false;
      } else if (type == "pickup_address") {
        var pick_pincode_value = $('#pickup_pincode').val();            

        if( pick_pincode_value != '' ){
            $('#pickup_zone_id').prop('disabled',false);
            getPincode(pick_pincode_value,'pickup_zone_id','pickup_city','pickup_pincode');
        }
        $('.btn_edit_pickup_address').hide();
        $(".pickup_address").show();
        $("#pickup_address").removeClass('input_box_disabled');
        $("#pickup_address").removeAttr('disabled');
        $("#pickup_address").addClass('text_area_enabled');
        $('#pickup_pincode').removeClass('input_box_disabled');
        $('#pickup_pincode').removeAttr('disabled');
        $("#pickup_pincode").addClass('input_box_enabled');
        $('#pickup_city').removeClass('input_box_disabled');
        $('#pickup_city').removeAttr('disabled');
        $("#pickup_city").addClass('input_box_enabled');
        $('#pickup_zone_id').removeClass('input_box_disabled');
        $('#pickup_zone_id').removeAttr('disabled');
        $("#pickup_zone_id").addClass('input_box_enabled');
      } else if (type == "gst_details") {
        $('.btn_edit_gst_details').hide();
        $(".gst_details").show();
        $("#gst_arn").removeClass('input_box_disabled');
        $("#gst_arn").removeAttr('disabled');
        $("#gst_arn").addClass('input_box_enabled');
        $('#gst_provision_id').removeClass('input_box_disabled');
        $('#gst_provision_id').removeAttr('disabled');
        $("#gst_provision_id").addClass('input_box_enabled');        
        $('#gst_certificate_upload').removeAttr('disabled');
      } else{
        var pincode_value = $('#pincode').val();
        if( pincode_value != '' ){
            $('#zone_id').prop('disabled',false);
            getPincode(pincode_value,'zone_id','city','pincode');
        }
        $('.primary_address_edit').hide();
        $(".address1").show();
        $("#address1").removeAttr('disabled');
        $("#address1").addClass('text_area_enabled');
        $('#pincode').removeClass('input_box_disabled');
        $('#pincode').addClass('input_box_enabled').focus();
        $('#pincode').removeAttr('disabled');
        $('#city').removeClass('input_box_disabled');
        $("#city").addClass('input_box_enabled').focus();
        $('#city').removeAttr('disabled');
        $('#zone_id').removeClass('input_box_disabled');
        $('#zone_id').removeAttr('disabled');
        $('#zone_id').addClass('input_box_enabled').focus();
      }
    }

    function updateSellerBankDetailsAndAddress(type){
      var form_data = new FormData();
      var seller_id          = $("input[name = 'seller_id']").val();
      if(type == 'gst_details' ){
        var gst_arn              = $("#gst_arn").val();
        var old_gst_arn          = $("input.gst_arn").val();
        var gst_provision_id     = $("#gst_provision_id").val();
        var old_gst_provision_id = $("input.gst_provision_id").val();
        var file_data            = $('#gst_certificate_upload').prop('files')[0];
        var gst_certificate_upload_path = $(".gst_certificate_upload").val();
        var seller_nickname      =  $("input.nickname").val();

        $("#gst_arn").next('span').remove();
        $("#gst_provision_id").next('span').remove();

        // if( gst_arn == '' ){
        //     $("#gst_arn").next('span').remove();
        //     $('.btn_edit_gst_details').hide();
        //     $("#gst_arn").addClass('has-error');
        //     $("#gst_arn").removeClass('input_box_enabled');
        //     $("#gst_arn").after('<span class="error"><?php echo $error_gst_arn; ?></span>');  
        //     return false;
        // }
        if( gst_provision_id == '' ){
            $("#gst_provision_id").next('span').remove();
            $('.btn_edit_gst_details').hide();
            $("#gst_provision_id").addClass('has-error');
            $("#gst_provision_id").removeClass('input_box_enabled');
            $("#gst_provision_id").after('<span class="error"><?php echo $error_gst_provision_id; ?></span>');  
            return false;
        }
        
        if( gst_arn == old_gst_arn && gst_provision_id == old_gst_provision_id ){
          $("#bank_details-alert-message").find(".alert-info").remove();
          $(".gst_details").hide();
          $('.btn_edit_gst_details').show();
          $("#gst_arn").attr('disabled','disabled');
          $("#gst_arn").addClass('input_box_disabled');
          $("#gst_arn").removeClass('input_box_enabled');
          $("#gst_provision_id").addClass('input_box_disabled');
          $("#gst_provision_id").removeClass('input_box_enabled');
          $("#gst_provision_id").attr('disabled','disabled');
          $("#gst_certificate_upload").next('span').remove();

          $("#gst_arn").next('span').remove();
          $("#gst_arn").removeClass('has-error');
          $("#gst_provision_id").next('span').remove();
          $("#gst_provision_id").removeClass('has-error');
          return false;
        }

        if(typeof file_data  === "undefined"){
          $("#gst_certificate_upload").next('span').remove();
          $("#gst_certificate_upload").after('<span class="error"><?php echo $error_choose_file; ?></span>');
          return false;
        }else{
          $("#gst_certificate_upload").next('span').remove();
        }

        // // check gst provisional id 
        var gst_validate = gstin_validatation(gst_provision_id);

        if( gst_validate == 'false' ){
            return false;
        }

        // get pan card value if seller mention in our profile
        var get_pan_card_value = $('input[name="pan"]').val();
        // get pan card value from gstin provisional id
        var pan_number_gstin = gst_provision_id.substring(2,12);
            // fill pan number from gstin value
        $('input[name="pan"]').val(pan_number_gstin);            
               
        form_data.append('gst_arn', gst_arn);
        form_data.append('old_gst_arn', old_gst_arn);
        form_data.append('gst_provision_id', gst_provision_id);
        form_data.append('old_gst_provision_id', old_gst_provision_id);
        form_data.append('type', type);
        form_data.append('img', gst_certificate_upload_path);
        form_data.append('upload_file', file_data);
        form_data.append('seller_nickname', seller_nickname);
        form_data.append('seller_id', seller_id);

      }else if(type == "bank_details"){
        var bank_ac_holder_name= $("#bank_ac_holder_name").val();
        var old_bank_ac_holder_name = $("input.bank_ac_holder_name").val();
        var bank_ac_number     = $("#bank_ac_number").val();
        var old_bank_ac_number = $("input.bank_ac_number").val();
        var cancel_img_path    = $("#cancel_cheque_image").val();
        var seller_nickname    =  $("input.nickname").val();
        var ifsc_code          = $("#ifsc_code").val();
        var old_ifsc_code      = $("input.ifsc_code").val();
        var file_data          = $('#upload_cancel_cheque').prop('files')[0];

        $("#bank_ac_holder_name").next('span').remove();
        $("#bank_ac_number").next('span').remove();
        $("#ifsc_code").next('span').remove();

        if( bank_ac_holder_name == '' ){
            $("#bank_ac_holder_name").next('span').remove();
            $('.btn_edit_pickup_address').hide();
            $("#bank_ac_holder_name").addClass('has-error');
            $("#bank_ac_holder_name").removeClass('input_box_enabled');
            $("#bank_ac_holder_name").after('<span class="error"><?php echo $error_bank_holder_name; ?></span>');  
            return false;
        }
        if( bank_ac_number == '' ){
            $("#bank_ac_number").next('span').remove();
            $('.btn_edit_pickup_address').hide();
            $("#bank_ac_number").addClass('has-error');
            $("#bank_ac_number").removeClass('input_box_enabled');
            $("#bank_ac_number").after('<span class="error"><?php echo $error_bank_account_no; ?></span>');  
            return false;
        }
        if( ifsc_code == '' ){
            $("#ifsc_code").next('span').remove();
            $('.btn_edit_pickup_address').hide();
            $("#ifsc_code").addClass('has-error');
            $("#ifsc_code").removeClass('input_box_enabled');
            $("#ifsc_code").after('<span class="error"><?php echo $error_bank_ifsc_code; ?></span>');  
            return false;
        }
        if( bank_ac_holder_name == old_bank_ac_holder_name && bank_ac_number == old_bank_ac_number && ifsc_code == old_ifsc_code){
          $("#bank_details-alert-message").find(".alert-info").remove();
          $(".bank_details").hide();
          $('.btn_edit_bank_detail').show();
          $("#bank_ac_holder_name").attr('disabled','disabled');
          $("#bank_ac_holder_name").addClass('input_box_disabled');
          $("#bank_ac_holder_name").removeClass('input_box_enabled');
          $("#bank_ac_number").addClass('input_box_disabled');
          $("#bank_ac_number").removeClass('input_box_enabled');
          $("#bank_ac_number").attr('disabled','disabled');
          $("#ifsc_code").attr('disabled','disabled');
          $("#ifsc_code").addClass('input_box_disabled');
          $("#ifsc_code").removeClass('input_box_enabled');
          $("#upload_cancel_cheque").next('span').remove();

          $("#bank_ac_holder_name").next('span').remove();
          $("#bank_ac_holder_name").removeClass('has-error');
          $("#bank_ac_number").next('span').remove();
          $("#bank_ac_number").removeClass('has-error');
          $("#ifsc_code").next('span').remove();
          $("#ifsc_code").removeClass('has-error');
          return false;
        }

        if(typeof file_data  === "undefined"){
          $("#upload_cancel_cheque").next('span').remove();
          $("#upload_cancel_cheque").after('<span class="error"><?php echo $error_choose_file; ?></span>');
          return false;
        }else{
          $("#upload_cancel_cheque").next('span').remove();
        }
        if(ifsc_code.trim().length != 11){
          alert('<?php echo $error_bank_ifsc_code_correct; ?>');
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

            $("#pickup_address").next('span').remove();
            $("#pickup_city").next('span').remove();
            $("#pickup_pincode").next('span').remove();
            $("#pickup_zone_id").next('span').remove();

            if(pickup_pincode == ''){
                $("#pickup_pincode").next('span').remove();
                $('.btn_edit_pickup_address').hide();
                $("#pickup_pincode").addClass('has-error');
                $("#pickup_pincode").removeClass('input_box_enabled');
                $("#pickup_pincode").after('<span class="error"><?php echo $error_pickup_pincode; ?></span>');  
                return false;
            }

            if(pickup_city == ''){
                $("#pickup_city").next('span').remove();
                $('.btn_edit_pickup_address').hide();
                $("#pickup_city").addClass('has-error');
                $("#pickup_city").removeClass('input_box_enabled');
                $("#pickup_city").after('<span class="error"><?php echo $error_pickup_city; ?></span>');  
                return false;
            }

            if(pickup_address == ''){
                $("#pickup_address").next('span').remove();
                $('.btn_edit_pickup_address').hide();
                $("#pickup_address").addClass('has-error');
                // $("#pickup_address").removeClass('text_area_enabled');
                $("#pickup_address").removeClass('input_box_enabled');
                $("#pickup_address").after('<span class="error"><?php echo $error_pickup_address; ?></span>');  
                return false;
            }

            if(pickup_zone_id == ''){
                $("#pickup_zone_id").next('span').remove();
                $('.btn_edit_pickup_address').hide();
                $("#pickup_zone_id").addClass('has-error');
                $("#pickup_zone_id").removeClass('input_box_enabled');
                $("#pickup_zone_id").after('<span class="error"><?php echo $error_pickup_zone_id; ?></span>');  
                return false;
            }

            if((!CheckZipCode.test(pickup_pincode))){
                $("#pickup_pincode").next('span').remove();
                $('.btn_edit_pickup_address').hide();
                $("#pickup_pincode").addClass('has-error');
                $("#pickup_pincode").removeClass('input_box_enabled');
                $("#pickup_pincode").after('<span class="error"><?php echo $error_pincode_invalid; ?></span>');  
                return false;
            }
          if(pickup_address == old_pickup_address && pickup_city == old_pickup_city && pickup_pincode == old_pickup_pincode && pickup_zone_id == old_pickup_zone_id){
            $('.btn_edit_pickup_address').show();
            $(".pickup_address").hide();
            $("#pickup_address").attr('disabled','disabled');
            $("#pickup_address").addClass('input_box_disabled');
            // $("#pickup_address").removeClass('text_area_enabled');
            $("#pickup_address").removeClass('input_box_enabled');
            $("#pickup_pincode").removeClass('input_box_enabled');
            $("#pickup_pincode").addClass('input_box_disabled');
            $("#pickup_pincode").removeClass('input_box_enabled');
            $("#pickup_city").addClass('input_box_disabled');
            $("#pickup_city").removeClass('input_box_enabled');
            $("#pickup_city").attr('disabled','disabled');
            $("#pickup_zone_id").attr('disabled','disabled');
            $("#pickup_zone_id").addClass('input_box_disabled');
            $("#pickup_zone_id").removeClass('input_box_enabled');

            $('#pickup_pincode').removeClass('has-error');
            $('#pickup_pincode').next('span').remove();
            $('#pickup_city').removeClass('has-error');
            $('#pickup_city').next('span').remove();
            $('#pickup_address').removeClass('has-error');
            $('#pickup_address').next('span').remove();
            $('#pickup_zone_id').removeClass('has-error');
            $('#pickup_zone_id').next('span').remove();
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
        $('.primary_address_edit').show();
        var address         = $("#address1").val();
        var old_address     = $("input.address1").val();
        var seller_city     = $("#city").val();
        var old_seller_city = $("input.city").val();
        var pincode         = $("#pincode").val();
        var CheckZipCode    = /(^\d{6}$)/;
        var old_pincode     = $("input.pincode").val();
        var seller_zone_id  = $("#zone_id").val();
        var old_seller_zone_id = $("input.zone_id").val();
        var seller_change_update_id = $("input[name=\'seller_change_update_id\']").val();
        var seller_zone_name  = $('option:selected',"#zone_id").text();
        
        if( pincode == '' ){
            $("#pincode").next('span').remove();
            $('.primary_address_edit').hide();
            $("#pincode").addClass('has-error');
            $("#pincode").removeClass('input_box_enabled');
            $("#pincode").after('<span class="error"><?php echo $error_pincode_invalid; ?></span>');  
            return false;
        }

        if( address == '' ){
            $("#address1").next('span').remove();
            $('.primary_address_edit').hide();
            $("#address1").addClass('has-error');
            // $("#address1").removeClass('text_area_enabled');
            $("#address1").removeClass('input_box_enabled');
            $("#address1").after('<span class="error"><?php echo $error_address; ?></span>');  
            return false;
        }

        if( seller_city == '' ){
            $("#city").next('span').remove();
            $('.primary_address_edit').hide();
            $("#city").addClass('has-error');
            $("#city").removeClass('input_box_enabled');
            $("#city").after('<span class="error"><?php echo $error_city; ?></span>');  
            return false;
        }

        

        if(seller_zone_id == ''){
            $("#zone_id").next('span').remove();
            $('.primary_address_edit').hide();
            $("#zone_id").addClass('has-error');
            $("#zone_id").removeClass('input_box_enabled');
            $("#zone_id").after('<span class="error"><?php echo $error_zone_id; ?></span>');  
            return false;
        }
        if((!CheckZipCode.test(pincode))){
            $("#pincode").next('span').remove();
            $('.primary_address_edit').hide();
            $("#pincode").addClass('has-error');
            $("#pincode").removeClass('input_box_enabled');
            $("#pincode").after('<span class="error"><?php echo $error_pincode_invalid; ?></span>');  
            return false;
        }
        if(seller_city.length > 64 ){
          alert('<?php echo $error_city_length; ?>');
          return false;
        }
        if(address == old_address && seller_city==old_seller_city && pincode==old_pincode && seller_zone_id == old_seller_zone_id && seller_change_update_id == 0){
          $(".address1").hide();
          $("#address1").attr('disabled','disabled');
          // $("#address1").removeClass('text_area_enabled');
          $("#address1").removeClass('input_box_enabled');
          $("#pincode").addClass('input_box_disabled');
          $("#pincode").removeClass('input_box_enabled');
          $("#pincode").attr('disabled','disabled');
          $("#city").addClass('input_box_disabled');
          $("#city").removeClass('input_box_enabled');
          $("#city").attr('disabled','disabled');
          $("#zone_id").attr('disabled','disabled');
          $("#zone_id").addClass('input_box_disabled');
          $("#zone_id").removeClass('input_box_enabled');
          $('.tin_address_message').text('').hide();
          $("input[name='seller_change_update_id']").val(0);
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
        form_data.append('seller_change_update_id', seller_change_update_id);
        form_data.append('seller_zone_name', seller_zone_name);
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
            if(response.valid == "not vaild" && response.type == "get_certificate"){
                
                if(response.text_box == 'error_gst_provisional_id'){
                    $("#gst_provision_id").next('span').remove();
                    //$('.btn_edit_bank_detail').hide();
                    $("#gst_provision_id").after('<span class="error">' + response.message + '</span>');  
                    return false;
                }

                $("#gst_certificate_upload").next('span').remove();
                //$('.btn_edit_bank_detail').hide();
                $("#gst_certificate_upload").after('<span class="error">' + response.message + '</span>');  
                return false;
            } else if(response.valid == "not vaild"){
                $("#upload_cancel_cheque").next('span').remove();
                $('.btn_edit_bank_detail').hide();
                $("#upload_cancel_cheque").after('<span class="error">' + response.message + '</span>');  
                return false;
            } else if(response.msg == 'update seller gst details') {
                $('.btn_edit_gst_details').show();
                $("input.gst_arn").val(gst_arn);
                $("input.gst_provision_id").val(gst_provision_id);
                $(".gst_certificate_upload").val(response.data);
                $(".gst_details").hide();
                $("#gst_arn").attr('disabled','disabled');
                $("#gst_arn").addClass('input_box_disabled');
                $("#gst_arn").removeClass('input_box_enabled');
                $("#gst_arn").removeClass('has-error');
                $("#gst_provision_id").addClass('input_box_disabled');
                $("#gst_provision_id").removeClass('input_box_enabled');
                $("#gst_provision_id").removeClass('has-error');
                $("#gst_provision_id").attr('disabled','disabled');
                $('#gst_certificate_upload').val('');
                $('#gst_certificate_upload').attr('disabled',true);
                $("#gst_details-alert-message").html('<span class="alert alert-info"><?php echo $request_message; ?></span>');
            } else if(response.msg == 'update seller bank details'){
              $('.btn_edit_bank_detail').show();
              $("input.bank_ac_holder_name").val(bank_ac_holder_name);
              $("input.bank_ac_number").val(bank_ac_number);
              $("input.ifsc_code").val(ifsc_code);
              $("#cancel_cheque_image").val(response.data);
              $(".bank_details").hide();
              $("#bank_ac_holder_name").attr('disabled','disabled');
              $("#bank_ac_holder_name").addClass('input_box_disabled');
              $("#bank_ac_holder_name").removeClass('input_box_enabled');
              $("#bank_ac_holder_name").removeClass('has-error');
              $("#bank_ac_number").addClass('input_box_disabled');
              $("#bank_ac_number").removeClass('input_box_enabled');
              $("#bank_ac_number").removeClass('has-error');
              $("#bank_ac_number").attr('disabled','disabled');
              $("#ifsc_code").attr('disabled','disabled');
              $("#ifsc_code").addClass('input_box_disabled');
              $("#ifsc_code").removeClass('input_box_enabled');
              $("#ifsc_code").removeClass('has-error');
              $(".upload_cancel_cheque_error").hide();
              $('#upload_cancel_cheque').val('');
              $('#upload_cancel_cheque').attr('disabled',true);
              $("#bank_details-alert-message").html('<span class="alert alert-info"><?php echo $request_message; ?></span>');
            } else if(response.msg == "pickup details updated"){
              $('.btn_edit_pickup_address').show();  
              $("input.pickup_address").val(pickup_address);
              $("input.pickup_city").val(pickup_city);
              $("input.pickup_pincode").val(pickup_pincode);
              $("input.pickup_zone_id").val(pickup_zone_id);
              $(".pickup_address").hide();
              $("#pickup_address").attr('disabled','disabled');
              $("#pickup_address").addClass('input_box_disabled');
              // $("#pickup_address").removeClass('text_area_enabled');
              $("#pickup_address").removeClass('input_box_enabled');
              $("#pickup_pincode").addClass('input_box_disabled');
              $("#pickup_pincode").removeClass('input_box_enabled');
              $("#pickup_pincode").attr('disabled','disabled');
              $("#pickup_city").addClass('input_box_disabled');
              $("#pickup_city").removeClass('input_box_enabled');
              $("#pickup_city").attr('disabled','disabled');
              $("#pickup_zone_id").attr('disabled','disabled');
              $("#pickup_zone_id").addClass('input_box_disabled');
              $("#pickup_zone_id").removeClass('input_box_enabled');

              $("#pickup_pincode").removeClass('has-error');
              $("#pickup_city").removeClass('has-error');
              $("#pickup_address").removeClass('has-error');
              $("#pickup_zone_id").removeClass('has-error');
            } else{

              $(".address1").hide();
              $("input.address1").val(address);
              $("input.city").val(seller_city);
              $("input.pincode").val(pincode);
              $("input.zone_id").val(seller_zone_id);
              $("#same_as_primary_address").prop("checked",false);
              $("button[data-id=\'pickup_address'\]").show();
              $("#address1").attr('disabled','disabled');
              // $("#address1").removeClass('text_area_enabled');
              $("#address1").removeClass('input_box_enabled');
              $("#pincode").addClass('input_box_disabled');
              $("#pincode").removeClass('input_box_enabled');
              $("#pincode").attr('disabled','disabled');
              $("#city").addClass('input_box_disabled');
              $("#city").removeClass('input_box_enabled');
              $("#city").attr('disabled','disabled');
              $("#zone_id").attr('disabled','disabled');
              $("#zone_id").removeClass('input_box_enabled');
              $("#zone_id").addClass('input_box_disabled');
              $('.tin_address_message').text('').hide();
              $("input[name='seller_change_update_id']").val(0);

              $('#address1').removeClass('has-error');
              $('#address1').next('span').remove();
              $('#city').removeClass('has-error');
              $('#city').next('span').remove();
              $('#pincode').removeClass('has-error');
              $('#pincode').next('span').remove();
              $('#zone_id').removeClass('has-error');
              $('#zone_id').next('span').remove();

            }
          }
      });
    }

    function inerstValue(id){
        var form_data = new FormData();
        form_data.append('field', id);
        var update_value = $("#"+id).val();

        if( id == 'additional_emails' ){
            $('.click_here_edit').show();
            add_emails_array = validateEmail('.additional_emails_block');
            if(!add_emails_array){
                return add_emails_array;
            }
            update_value = add_emails_array.join();
        }

        if(update_value == ''){
            if(id != 'additional_emails'){
                $('#'+ id).next('span').remove();
                $('#'+ id).addClass('has-error');
                $('#'+ id).removeClass('input_box_enabled');
                $('#'+ id).after('<span class="error"><?php echo $error_blank_input_box; ?></span>');
                return false;
            }
        }

        if(id != "primary_contact_no" && id != "pickup_contact_no" && id != "account_contact_no" && id != "inventory_contact_no" && id != "pan" && id != "tin" && id !='additional_emails'){
            var alpha = /^[a-zA-Z]{1}[a-zA-Z. ]*$/;
            if(!alpha.test(update_value)){
                $('#'+ id).next('span').remove();
                $('#'+ id).addClass('has-error');
                $('#'+ id).removeClass('input_box_enabled');
                $('#'+ id).after('<span class="error"><?php echo $error_correct_information; ?></span>');
                return false;
            }
        }

        if(update_value.length > 64){
            if(id !="additional_emails"){
                alert("<?php echo $error_character_length; ?>");
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
                    $("#pan_upload").next("span").remove();
                    $("#pan_upload").after('<span class="error"><?php echo $error_choose_file; ?></span>');
                    return false;
                }
               
                var pan_image       = $("#pan_image").val();
                var seller_nickname =  $("input.nickname").val();
                form_data.append('img', pan_image  );
                form_data.append('upload_file', file_data);
                form_data.append('seller_nickname', seller_nickname  );
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
                    $("#tin_upload").next("span").remove();
                    $("#tin_upload").after('<span class="error"><?php echo $error_choose_file; ?></span>');
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
                        $('.btn_edit_additional_emails').hide();
                        if( id == 'pan'){
                            $('#pan_upload').next('span').remove();
                            $('#pan_upload').after('<span class="error">' + response.message + '!</span>');
                        } else if( id == 'tin' ){
                            $('#tin_upload').next('span').remove();
                            $('#tin_upload').after('<span class="error">' + response.message + '!</span>');
                        } else{
                            $('#'+ id).next('span').remove();
                            $('#'+ id).addClass('has-error');
                            $('#'+ id).removeClass('input_box_enabled');
                            $('#'+ id).after('<span class="error">' + response.message + '!</span>');
                            //alert(response.message);
                            if(id == 'additional_emails'){
                                $('.additional_emails_input').next('span').remove();
                                $('.additional_emails_input').after('<span class="error col-sm-12">' + response.message + '!</span>');
                                $('.additional_emails_input').parent().addClass('error');
                                additional_emails_error_value = false;
                            }
                        }
                            
                        return false;
                    }
                    if(response.msg == "success"){
                        $("."+id).hide();
                        $("#"+id).removeClass('input_box_enabled');
                        $("#"+id).removeClass('has-error');
                        $("#"+id).next('span').remove();
                        $(".btn_edit_"+id).show();
                        $("input."+id).val(update_value);
                        if(id == 'primary_contact_name' || id == "primary_contact_no"){
                            $("#same_as_primary_pickup").prop("checked", false);
                            $("#same_as_primary_account").prop("checked", false);
                            $("#same_as_primary_inventory").prop("checked", false);

                            $('.same_as_primary_pickup').val(0);
                            $('.same_as_primary_account').val(0);
                            $('.same_as_primary_inventory').val(0);
                        }
                        
                        if(id == 'pan'){
                            $("#pan-alert-message").html('<span class="alert alert-info"><?php echo $request_message; ?></span>');
                            $("#pan_image").val(response.data);
                            $("#pan_upload").next("span").remove();
                            $("#tin_upload").next('span').remove();
                            $('#pan_upload').val('');
                            $("#pan_upload").attr('disabled',true);
                        }
                      
                        if(id == 'tin'){
                            $("#tin_image").val(response.data);
                            $("#tin_upload").next('span').remove();
                            $('#tin_upload').val('');
                            $("#tin-alert-message").html('<span class="alert alert-info"><?php echo $request_message; ?></span>');
                            $("#tin_upload").attr('disabled',true);

                            $('a[href=\'#Basic\']').trigger('click');
                            $('.primary_address_edit').trigger('click');
                            $('#legend_address').addClass('in');
                            if($('.basic_information > legend > h4 >i').hasClass('more-less')){
                                $('.basic_information > legend > h4 >i').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');    
                            }                            
                            $('.tin_address_message').text('<?php echo $tin_according_address; ?>').show();
                            $('input[name=\'seller_change_update_id\']').val(response.update_id);
                        }

                        if(id == 'additional_emails'){
                            additional_emails_error_value = true;
                            if($('.additional_email_group .additional_emails_block').length == 0){
                                $('.empty_add_email').show();
                            } else {
                                $('.btn_edit_additional_emails').removeClass('hidden');
                                $('.empty_add_email').hide();
                            }
                            $('.btn_edit_additional_emails').removeClass('hidden');
                           $('.add_email_add_btn, .add_email_remove_btn').addClass('hidden');
                            $('.additional_emails_input').each(function(i,e){
                                $(e).prop('disabled',true);
                                $(e).addClass('input_box_disabled');
                                $(e).removeClass('input_box_enabled');
                                $(e).next('span').remove();
                            }); 
                        }
                    }
                    $('#'+id).attr('disabled','disabled');
                    //if(id !="additional_emails"){
                    $('#'+id).addClass('input_box_disabled');
                    //}
                },
            });
        } else {
            $("#tin-alert-message").find(".alert-info").remove();
            $("#pan-alert-message").find(".alert-info").remove();
            //if(id !="additional_emails"){
            $('#'+id).addClass('input_box_disabled');
            //}
            $('#'+id).attr('disabled','disabled');
            $("."+id).hide();
            $('.btn_edit_'+id).show();
            $('#'+id).removeClass('input_box_enabled');
            $('#'+id).removeClass('has-error');
            $('#'+id).next('span').remove();
            if( id == 'additional_emails'){ 
                $('.additional_emails_input').prop('disabled',true);
                $('.additional_emails_input').addClass('input_box_disabled');
                $('.additional_emails_input').removeClass('input_box_enabled');
                $('.add_email_add_btn, .add_email_remove_btn').addClass('hidden');
                $('.empty_add_email').show();
            }
            

        }

        if($('.additional_emails_block').length == 0 && $('.empty_add_email').length == 0){
            $('.additional_email_group').before('<span class="empty_add_email col-sm-12"><a href="javascript:void(0);" class="click_here_edit">Click here to add Email!</a></span>');
        }
        
        if($('.empty_add_email').length > 0 ){
            $('.btn_edit_additional_emails').addClass('hidden');
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
            $("#pickup_holder_name").removeClass('input_box_enabled');
            $("#pickup_contact_no").removeClass('input_box_enabled');
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
           $("#pickup_holder_name").addClass('input_box_disabled');
           $("#pickup_holder_name").attr('disabled',true);
           $(".pickup_holder_name").hide();
           $("#pickup_contact_no").addClass('input_box_disabled');
           $("#pickup_contact_no").attr('disabled',true);
           $(".pickup_contact_no").hide();
           if( $('.same_as_primary_pickup').val() != 0){
                return false;
           }
         }
         var data = "type="+type+"&primary_contact_name="+primary_contact_name+"&primary_contact_no="+primary_contact_no+"&old_contact_name="+old_contact_name+"&old_contact_no="+old_contact_no+"&seller_id="+seller_id;
       }
       if(type == 'same_as_primary_address'){
         if($("#"+type).is(":checked")){
            $("#pickup_pincode").addClass('input_box_disabled');
            $("#pickup_city").addClass('input_box_disabled');
            $("#pickup_zone_id").addClass('input_box_disabled');
            $("#pickup_address").addClass('input_box_disabled');
            $("#pickup_pincode").removeClass('input_box_enabled');
            $("#pickup_city").removeClass('input_box_enabled');
            $("#pickup_zone_id").removeClass('input_box_enabled');
            $("#pickup_address").removeClass('input_box_enabled');
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
          if(primary_address == old_pickup_address && primary_city == old_primary_city && primary_pincode == old_pickup_pincode){
            $('textarea#pickup_address').attr('disabled',true);
            $("#pickup_city").addClass('input_box_disabled');
            $("#pickup_pincode").attr('disabled',true);
            $("#pickup_pincode").addClass('input_box_disabled');
            $("#pickup_city").attr('disabled',true);
            $("select[name=\'pickup_zone_id\']").attr('disabled',true);
            $("button[data-id=\'pickup_address\' ]").hide();
            $('button.pickup_address').hide();
            return false;
          }
          var data = "type="+type+"&primary_address="+primary_address+"&primary_zone="+primary_zone+"&primary_city="+primary_city+"&primary_pincode="+primary_pincode+"&old_pickup_address="+old_pickup_address+"&old_pickup_city="+old_pickup_city+"&old_pickup_pincode="+old_pickup_pincode+"&old_pickup_zone_id="+old_pickup_zone_id+"&seller_id="+seller_id;
       }
       if(type == "same_as_primary_account"){
         if($("#"+type).is(":checked")){
            $('#account_holder_name').removeClass('input_box_enabled');
            $('#account_contact_no').removeClass('input_box_enabled');
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
           $("#account_holder_name").addClass('input_box_disabled');
           $("#account_holder_name").attr('disabled',true);
           $(".account_holder_name").hide();
           $("#account_contact_no").addClass('input_box_disabled');
           $("#account_contact_no").attr('disabled',true);
           $(".account_contact_no").hide();
           if( $('.same_as_primary_account').val() != 0){
                return false;
           }
         }
         var data = "type="+type+"&primary_contact_name="+primary_contact_name+"&primary_contact_no="+primary_contact_no+"&old_contact_name="+old_contact_name+"&old_contact_no="+old_contact_no+"&seller_id="+seller_id
       }
       if(type == "same_as_primary_inventory"){
         if($("#"+type).is(":checked")){
            $('#inventory_holder_name').removeClass('input_box_enabled');
            $('#inventory_contact_no').removeClass('input_box_enabled');
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
           $("#inventory_holder_name").addClass('input_box_disabled');
           $("#inventory_holder_name").attr('disabled',true);
           $(".inventory_holder_name").hide();
           $("#inventory_contact_no").addClass('input_box_disabled');
           $("#inventory_contact_no").attr('disabled',true);
           $(".inventory_contact_no").hide();
           if( $('.same_as_primary_inventory').val() != 0){
                return false;
           }
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
              if(response.msg == "success"){
                if(type == "same_as_primary_pickup"){
                    $("button[data-id=\'pickup_holder_name'\ ]").hide();
                    $("button[data-id=\'pickup_contact_no'\ ]").hide();
                    $("#pickup_holder_name").addClass('input_box_disabled');
                    $(".pickup_holder_name").hide();
                    $("#pickup_contact_no").addClass('input_box_disabled');
                    $(".pickup_contact_no").hide();
                    $("#pickup_holder_name").val(primary_contact_name);
                    $("#pickup_contact_no").val(primary_contact_no);
                    $("input.pickup_holder_name").val(primary_contact_name);
                    $("input.pickup_contact_no").val(primary_contact_no);
                }
                if(type == "same_as_primary_account"){
                  $("button[data-id=\'account_holder_name'\ ]").hide();
                  $("button[data-id=\'account_contact_no'\ ]").hide();
                  $("#account_holder_name").addClass('input_box_disabled');
                  $(".account_holder_name").hide();
                  $("#account_contact_no").addClass('input_box_disabled');
                  $(".account_contact_no").hide();
                  $("#account_holder_name").val(primary_contact_name);
                  $("#account_contact_no").val(primary_contact_no);
                  $("input.account_holder_name").val(primary_contact_name);
                  $(".account_contact_no").val(primary_contact_no);
                }
                if(type == "same_as_primary_inventory"){
                  $("button[data-id=\'inventory_holder_name'\ ]").hide();
                  $("button[data-id=\'inventory_contact_no'\ ]").hide();
                  $("#inventory_holder_name").addClass('input_box_disabled');
                  $(".inventory_holder_name").hide();
                  $("#inventory_contact_no").addClass('input_box_disabled');
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
                    $("#pickup_city").addClass('input_box_disabled');
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
      var code= /([C,P,H,F,A,T,B,L,J,G,E])/;
      var code_chk=textObj.substring(3,4).toUpperCase();

      if (textObj!=="") {
          if(regpan.test(textObj) == false) {
                $('#pan').next('span').remove();
                $('#pan').after('<span class="error"><?php echo $error_pan_invalid; ?></span>');
                $('#pan').addClass('has-error');
               return "false";
          }
          if (code.test(code_chk)==false) {
              alert("Invalid pan card no");
              return "false";
          }
      }
      return true;
    }

    /* This function validates for GSTIN */
    function gstin_validatation(textObj){
        var reggstin = /^([0-9]){2}([A-Z]){3}([C,P,H,F,A,T,B,L,J,G,E]){1}([A-Z]){1}([0-9]){4}([A-Z]){1}([0-9]){1}([A-Z]){1}([A-Z0-9]){1}?$/;

        if (textObj!=="") {
            if(reggstin.test(textObj) == false) {
                $('#gst_provision_id').next('span').remove();
                $('#gst_provision_id').after('<span class="error"><?php echo $error_gst_provisional_id; ?></span>');
                $('#gst_provision_id').addClass('has-error');
               return "false";
            }
        }
        return true;
    }  

    function validateEmail(inputs){
        let add_emails_array = [];
        $(inputs).each(function(){
            let input = $(this).find('input');
            let email_filter = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
            $(this).find('span').remove();
            if(input.val().trim() == ''){
                $(this).addClass('error');
                 input.after('<span class="col-sm-12"><?php echo $error_additional_emails; ?></span>');
            }
            else if ( email_filter.test(input.val().trim()) ){
                add_emails_array.push(input.val());
                $(this).removeClass('invalid_email');
                $(this).find('span').remove();
            }
            else{
                $(this) .addClass('invalid_email error');
                input.after('<span class="col-sm-12">Invalid Email</span>');
            }

        
        });

        if($(inputs+'.error').length > 0){
            return false;    
        }
        else{
            return add_emails_array;
        }
    }

    // adding additional emails 

    function addAdditionalEmail() {
        let additional_emails_error_value = validateEmail('.additional_emails_block');
        if( additional_emails_error_value ){
            let add_email_count = <?php echo ($add_email_count) ? $add_email_count : 0 ; ?>;

            html = '<div id="additional_emails_block'+add_email_count+'" class="additional_emails_block">';
            html +='<input type="text" class="form-control additional_emails_input input_box_enabled" name="additional_emails[]" value="" id="additional_emails_box'+add_email_count+'" />';
            html +='<button type="button" onclick="removeInput(this)" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger btn-sm add_email_remove_btn"><i class="fa fa-minus-circle"></i></button>';
            html +='</div>';
            $('.additional_email_group').append(html);

            add_email_count++;
        }
    }

  $(document).ready(function(){
    if($('#tin_taxable').is('checked') || $('#tin_composite').is('checked')){
      $('.primary_address_edit').hide();
    }
  });      
</script>
<script type="text/javascript">
    $(document).ready(function(){

        $( document ).delegate( ".additional_emails_input", "keyup", function() {
           if($(this).val() !=''){
            $(this).next('span').remove();
            $(this).parent().removeClass('error');
            additional_emails_error_value = true;
           }else{
            additional_emails_error_value = false;
           }
        });

        if($("#tin_tax_free").prop("checked") == true  && $("#tin_taxable").prop("checked") == false && $("#tin_composite").prop("checked") == false ) {
            var seller_id        = $("input[name = 'seller_id']").val();
            var old_tin_tax_type = $("#old_tin_tax_type").val();
            $('.tin_block').hide();
            // $('button.tin').hide();
            // $("button[data-id=\'tin\']").hide();
        } else {
            $('.tin_block').show();
            // $("button[data-id=\'tin\']").show();
        }


        $('#primary_contact_name').on('keyup',function(){
            if(!isNaN($(this).val())){
                $(this).next('span').remove();
                $(this).after('<span class="error"><?php echo $error_only_alphbates; ?></span>');
                return false;
            }
        });
        $('#primary_contact_no').on('keyup',function(){
            if(isNaN($(this).val())){
                $(this).next('span').remove();
                $(this).after('<span class="error"><?php echo $error_only_digit; ?></span>');
                return false;
            }
        });
        $('#pickup_holder_name').on('keyup',function(){
            if(!isNaN($(this).val())){
                $(this).next('span').remove();
                $(this).after('<span class="error"><?php echo $error_only_alphbates; ?></span>');
                return false;
            }
        });
        $('#pickup_contact_no').on('keyup',function(){
            if(isNaN($(this).val())){
                $(this).next('span').remove();
                $(this).after('<span class="error"><?php echo $error_only_digit; ?></span>');
                return false;
            }
        });
        $('#account_holder_name').on('keyup',function(){
            if(!isNaN($(this).val())){
                $(this).next('span').remove();
                $(this).after('<span class="error"><?php echo $error_only_alphbates; ?></span>');
                return false;
            }
        });
        $('#account_contact_no').on('keyup',function(){
            if(isNaN($(this).val())){
                $(this).next('span').remove();
                $(this).after('<span class="error"><?php echo $error_only_digit; ?></span>');
                return false;
            }
        });
        $('#inventory_holder_name').on('keyup',function(){
            if(!isNaN($(this).val())){
                $(this).next('span').remove();
                $(this).after('<span class="error"><?php echo $error_only_alphbates; ?></span>');
                return false;
            }
        });
        $('#inventory_contact_no').on('keyup',function(){
            if(isNaN($(this).val())){
                $(this).next('span').remove();
                $(this).after('<span class="error"><?php echo $error_only_digit; ?></span>');
                return false;
            }
        });

        $('#company').on('keyup',function(){
            if(!isNaN($(this).val())){
                $(this).next('span').remove();
                $(this).after('<span class="error"><?php echo $error_only_alphbates; ?></span>');
                return false;
            }
        });

        $(document).on('click','.click_here_edit',function(){           
            var id = 'additional_emails';
            $(this).hide();
            if( id == 'additional_emails' ){
                $('.empty_add_email').remove();
                if($('.additional_emails_block').length == 0){
                    $('.btn_save_additional_emails').removeClass('hidden');
                    $('.additional_email_group').html(add_emails);
                    $('.additional_email_group input').val('');
                }
                $('.btn_save_additional_emails').show();
                $('.add_email_add_btn, .add_email_remove_btn').removeClass('hidden');
                $('.additional_emails_input').each(function(){
                    $(this).removeAttr('disabled');
                    $(this).removeClass('input_box_disabled');
                    $(this).addClass('input_box_enabled');
                });        
            }
        });


        $('.gst_click_here').click(function(){
            $('a[href="#Business"]').trigger('click');
            $('.gst_block_show').hide();
        }); 

        $("input[name=\'gst_provision_id\']").on('keyup',function(){
            var value = $(this).val().toUpperCase();
            if(value.length >= 2 ){
                $(this).val(value);
                return false;    
            }
        });
    });

    // it is used for automatically click on business tab for fill gst details
    if(location.hash.length > 0) {
        $('.gst_block_show').hide();
        $('a[href="'+location.hash+'"]').click();
        $(window).scrollTop(0);
    }   

    $(document).ready(function(){
        $('.gst_block_show_close').click(function(){
            $('.gst_block_show').hide();
        });
    }); 
    
    function removeInput(obj){
        //console.log($('.additional_emails_block').length);
        if( $('.additional_emails_block').length == 1){
            $('.additional_emails_block > span').removeClass('error');
            $('.additional_emails_block span').remove();
            $('.add_email_add_btn,.additional_emails,.btn_save_additional_emails').addClass('hidden');
             $('.additional_email_group').before('<span class="empty_add_email col-sm-12"><a href="javascript:void(0);" class="click_here_edit">Click here to add Email!</a></span>');
        }
        $(obj).parent().remove();
        if( $('.additional_emails_block').length == 0){
            $('.btn_save_additional_emails').click();
        }
    }

    function toggleIcon(e) {
        let elem = $(e.target).parents('.basic_information').find(".more-less");
        if($(e.target).hasClass('in')){
            elem.removeClass('fa-plus-square-o');
            elem.addClass('fa-minus-square-o');
        }
        else{
            elem.removeClass('fa-minus-square-o');
            elem.addClass('fa-plus-square-o');
        }
    }
    $('.collapse').on('hidden.bs.collapse', toggleIcon);
    $('.collapse ').on('shown.bs.collapse', toggleIcon);
</script>
