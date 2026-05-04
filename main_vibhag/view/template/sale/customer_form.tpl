<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-customer" id="cs-submit-button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?> 
        <?php if($is_dropshipper==1 || $is_dropshipper==2 || $is_dropshipper==3){ ?>
            <label class="dropshipper_heading1 drop_head<?php echo $is_dropshipper; ?>">
            Dropshipper</label> 
        <?php } ?>
      </h3>
        <?php if(isset($count_order)){ ?>
        <div class="pull-right">
          <b><label class="customer_total_orders">
              <a href="<?php echo $total_order_link;?>" target="_blank">
                Total Orders: <?php echo $count_order; ?>
              </a>
            </label></b>
        </div>
        <?php } ?>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-customer" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <?php if ($customer_id) { ?>
            <li><a href="#tab-history" data-toggle="tab"><?php echo $tab_history; ?></a></li>
            <li><a href="#tab-transaction" data-toggle="tab"><?php echo $tab_transaction; ?></a></li>
            <li><a href="#tab-cashback" data-toggle="tab"><?php echo $tab_cashback; ?></a></li>
            <li><a href="#tab-cashback-usage" data-toggle="tab"><?php echo $tab_cashback_usage; ?></a></li>
            <li><a href="#tab-reward" data-toggle="tab"><?php echo $tab_reward; ?></a></li>
            <li><a href="#tab-preferences" data-toggle="tab"><?php echo $tab_preferences; ?></a></li>
            <li><a href="#tab-franchise" data-toggle="tab"><?php echo $tab_franchise; ?></a></li>
            <?php } ?>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="row">
                <div class="col-sm-2">
                  <ul class="nav nav-pills nav-stacked" id="address">
                    <li class="active"><a href="#tab-customer" data-toggle="tab"><?php echo $tab_general; ?></a></li>
                    <?php $address_row = 1; ?>
                    <?php foreach ($addresses as $address) { ?>
                    <li><a href="#tab-address<?php echo $address_row; ?>" data-toggle="tab"><i class="fa fa-minus-circle" onclick="$('#address a:first').tab('show'); $('#address a[href=\'#tab-address<?php echo $address_row; ?>\']').parent().remove(); $('#tab-address<?php echo $address_row; ?>').remove();"></i> <?php echo $tab_address . ' ' . $address_row; ?></a></li>
                    <?php $address_row++; ?>
                    <?php } ?>
                    <li id="address-add"><a onclick="addAddress();"><i class="fa fa-plus-circle"></i> <?php echo $button_address_add; ?></a></li>
                  </ul>
                </div>
                <div class="col-sm-10">
                  <div class="tab-content">
                    <div class="tab-pane active" id="tab-customer">
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-firstname"><?php echo $entry_firstname; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="firstname" value="<?php echo $firstname; ?>" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $firstname; ?>" />
                          <?php if ($error_firstname) { ?>
                          <div class="text-danger"><?php echo $error_firstname; ?></div>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-lastname"><?php echo $entry_lastname; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="lastname" value="<?php echo $lastname; ?>" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $lastname; ?>" />
                          <?php // if ($error_lastname) { ?>
                          <!-- <div class="text-danger"><?php //echo $error_lastname; ?></div> -->
                          <?php //} ?>
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $email; ?>"/>
                          <?php if ($error_email) { ?>
                          <div class="text-danger"><?php echo $error_email; ?></div>
                          <?php  } ?>
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="telephone" value="<?php echo $telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $telephone; ?>"/>
                          <?php if ($error_telephone) { ?>
                          <div class="text-danger"><?php echo $error_telephone; ?></div>
                          <?php  } ?>
                        </div>
                      </div>
                       <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-gst-number"><?php echo $entry_gst_number; ?></label>
                          <div class="col-sm-10">
                            <input type="text" name="gst_number" value="<?php echo $gst_number; ?>" placeholder="<?php echo $entry_gst_number; ?>" id="input-gst_number" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $gst_number; ?>" onkeyup="gst_number_valid();" />
                              <input type="hidden" name="old_gst_number" value="<?php echo $gst_number; ?>" />
                              <span id="gst_error">
                            <?php if ($error_gst_number) { ?>
                            <div class="text-danger"><?php echo $error_gst_number; ?></div>
                            <?php } ?>
                            </span>
                          </div>
                      </div>
                      
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-password"><?php echo $entry_password; ?></label>
                        <div class="col-sm-10">
                          <input type="password" name="password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control edit_track" autocomplete="off" data-block-name="general" data-change="false" data-old-value="<?php echo $password; ?>"/>
                          <?php if ($error_password) { ?>
                          <div class="text-danger"><?php echo $error_password; ?></div>
                          <?php  } ?>
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-confirm"><?php echo $entry_confirm; ?></label>
                        <div class="col-sm-10">
                          <input type="password" name="confirm" value="<?php echo $confirm; ?>" placeholder="<?php echo $entry_confirm; ?>" autocomplete="off" id="input-confirm" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $confirm; ?>"/>
                          <?php if ($error_confirm) { ?>
                          <div class="text-danger"><?php echo $error_confirm; ?></div>
                          <?php  } ?>
                        </div>
                      </div>

                      <div class="form-group">
                          <label class="col-sm-2 control-label">Dropshipper</label>
                          <div class="col-sm-10">
                            <select name="is_dropshipper" id="input-is_dropshipper" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $is_dropshipper; ?>">
                              <?php if ($is_dropshipper) { ?>
                                <option value="0" <?php echo ($is_dropshipper == 0) ? 'selected="selected"' : ''; ?>><?php echo $text_select; ?></option>
                                <option value="1" <?php echo ($is_dropshipper == 1) ? 'selected="selected"' : ''; ?>><?php echo $text_active; ?></option>
                                <option value="2" <?php echo ($is_dropshipper == 2) ? 'selected="selected"' : ''; ?>><?php echo $text_panding; ?></option>
                                <option value="3" <?php echo ($is_dropshipper == 3) ? 'selected="selected"' : ''; ?>><?php echo $text_block; ?></option>
                              <?php } else { ?>
                                <option value="0"><?php echo $text_select; ?></option>
                                <option value="1"><?php echo $text_active; ?></option>
                                <option value="2"><?php echo $text_panding; ?></option>
                                <option value="3"><?php echo $text_block; ?></option>
                              <?php } ?>
                            </select>
                          </div>
                      </div>
                        
                         <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-country_code"><?php echo $entry_country_code; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="country_code" value="<?php echo $country_code; ?>" placeholder="<?php echo $entry_country_code; ?>" id="input-country_code" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $country_code; ?>"/>
                          <?php if ($error_country_code) { ?>
                          <div class="text-danger"><?php echo $error_country_code; ?></div>
                          <?php  } ?>
                        </div>
                      </div>
                      
                    </div>                    
                    <?php $address_row = 1; ?>
                    <?php foreach ($addresses as $address) { ?>
                    <div class="tab-pane" id="tab-address<?php echo $address_row; ?>">
                      <input type="hidden" name="address[<?php echo $address_row; ?>][address_id]" value="<?php echo $address['address_id']; ?>" />
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-firstname<?php echo $address_row; ?>"><?php echo $entry_firstname; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="address[<?php echo $address_row; ?>][firstname]" value="<?php echo $address['firstname']; ?>" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname<?php echo $address_row; ?>" class="form-control" />
                          <?php if (isset($error_address[$address_row]['firstname'])) { ?>
                          <div class="text-danger"><?php echo $error_address[$address_row]['firstname']; ?></div>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-lastname<?php echo $address_row; ?>"><?php echo $entry_lastname; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="address[<?php echo $address_row; ?>][lastname]" value="<?php echo $address['lastname']; ?>" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname<?php echo $address_row; ?>" class="form-control" />
                          <?php //f (isset($error_address[$address_row]['lastname'])) { ?>
                          <!-- <div class="text-danger"><?php //echo $error_address[$address_row]['lastname']; ?></div> -->
                          <?php //} ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-company<?php echo $address_row; ?>"><?php echo $entry_company; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="address[<?php echo $address_row; ?>][company]" value="<?php echo $address['company']; ?>" placeholder="<?php echo $entry_company; ?>" id="input-company<?php echo $address_row; ?>" class="form-control" />
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-address-1<?php echo $address_row; ?>"><?php echo $entry_address_1; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="address[<?php echo $address_row; ?>][address_1]" value="<?php echo $address['address_1']; ?>" placeholder="<?php echo $entry_address_1; ?>" id="input-address-1<?php echo $address_row; ?>" class="form-control"/>
                          <?php if (isset($error_address[$address_row]['address_1'])) { ?>
                          <div class="text-danger"><?php echo $error_address[$address_row]['address_1']; ?></div>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-address-2<?php echo $address_row; ?>"><?php echo $entry_address_2; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="address[<?php echo $address_row; ?>][address_2]" value="<?php echo $address['address_2']; ?>" placeholder="<?php echo $entry_address_2; ?>" id="input-address-2<?php echo $address_row; ?>" class="form-control"/>
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-city<?php echo $address_row; ?>"><?php echo $entry_city; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="address[<?php echo $address_row; ?>][city]" value="<?php echo $address['city']; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city<?php echo $address_row; ?>" class="form-control"/>
                          <?php if (isset($error_address[$address_row]['city'])) { ?>
                          <div class="text-danger"><?php echo $error_address[$address_row]['city']; ?></div>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-postcode<?php echo $address_row; ?>"><?php echo $entry_postcode; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="address[<?php echo $address_row; ?>][postcode]" value="<?php echo $address['postcode']; ?>" placeholder="<?php echo $entry_postcode; ?>" id="input-postcode<?php echo $address_row; ?>" class="form-control"/>
                          <?php if (isset($error_address[$address_row]['postcode'])) { ?>
                          <div class="text-danger"><?php echo $error_address[$address_row]['postcode']; ?></div>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-country<?php echo $address_row; ?>"><?php echo $entry_country; ?></label>
                        <div class="col-sm-10">
                          <select name="address[<?php echo $address_row; ?>][country_id]" id="input-country<?php echo $address_row; ?>" onchange="country(this, '<?php echo $address_row; ?>', '<?php echo $address['zone_id']; ?>');" class="form-control">
                            <option value=""><?php echo $text_select; ?></option>
                            <?php foreach ($countries as $country) { ?>
                            <?php if ($country['country_id'] == $address['country_id']) { ?>
                            <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                          </select>
                          <?php if (isset($error_address[$address_row]['country'])) { ?>
                          <div class="text-danger"><?php echo $error_address[$address_row]['country']; ?></div>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-zone<?php echo $address_row; ?>"><?php echo $entry_zone; ?></label>
                        <div class="col-sm-10">
                          <select name="address[<?php echo $address_row; ?>][zone_id]" id="input-zone<?php echo $address_row; ?>" class="form-control">
                          </select>
                          <?php if (isset($error_address[$address_row]['zone'])) { ?>
                          <div class="text-danger"><?php echo $error_address[$address_row]['zone']; ?></div>
                          <?php } ?>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_default; ?></label>
                        <div class="col-sm-10">
                          <label class="radio">
                            <?php if (($address['address_id'] == $address_id) || !$addresses) { ?>
                            <input type="radio" name="address[<?php echo $address_row; ?>][default]" value="<?php echo $address_row; ?>" checked="checked"/>
                            <?php } else { ?>
                            <input type="radio" name="address[<?php echo $address_row; ?>][default]" value="<?php echo $address_row; ?>"/>
                            <?php } ?>
                          </label>
                        </div>
                      </div>
                    </div>
                    <?php $address_row++; ?>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <?php if ($customer_id) { ?>
            <div class="tab-pane" id="tab-history">
              <div id="history"></div>
              <br />
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-comment"><?php echo $entry_comment; ?></label>
                <div class="col-sm-10">
                  <textarea name="comment" rows="8" placeholder="<?php echo $entry_comment; ?>" id="input-comment" class="form-control"></textarea>
                </div>
              </div>
              <div class="text-right">
                <button id="button-history" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $button_history_add; ?></button>
              </div>
            </div>
            <div class="tab-pane" id="tab-transaction">
              <div id="transaction"></div>
              <br />
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-transaction-description"><?php echo $entry_description; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="description" value="" placeholder="<?php echo $entry_description; ?>" id="input-transaction-description" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-amount"><?php echo $entry_amount; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="amount" value="" placeholder="<?php echo $entry_amount; ?>" id="input-amount" class="form-control" />
                </div>
              </div>
              <div class="text-right">
                <button type="button" id="button-transaction" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $button_transaction_add; ?></button>
              </div>
            </div>
            
            <div class="tab-pane" id="tab-cashback">
              <div id="cashback"></div>
            </div>
            
            <div class="tab-pane" id="tab-cashback-usage">
              <div id="cashback-usage"></div>
            </div>
            
            <div class="tab-pane" id="tab-reward">
              <div id="reward"></div>
              <br />
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-reward-description"><?php echo $entry_description; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="description" value="" placeholder="<?php echo $entry_description; ?>" id="input-reward-description" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-points"><span data-toggle="tooltip" title="<?php echo $help_points; ?>"><?php echo $entry_points; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="points" value="" placeholder="<?php echo $entry_points; ?>" id="input-points" class="form-control" />
                </div>
              </div>
              <div class="text-right">
                <button type="button" id="button-reward" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $button_reward_add; ?></button>
              </div>
            </div>
            <?php } ?>

            <div class="tab-pane" id="tab-preferences">
              
              <!-- START Category filter -->
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-language"><?php echo $entry_categories; ?></label>
                <div class="col-sm-10">
                  <div class="filter_box">
                    <div class="list-group" id="list-group-filter">
                      <?php  
                      $inc = 5;

                      foreach($data['master_preferences'] as $value){ 
                        $is_checked = "";
                        $min_price  = "";
                        $max_price  = "";
                        $cat_id     = $value['category_id'];
                        ?>
                        <ul class="expectation_list">
                          <?php
                              foreach ($categories_data as $cat_data) {
                                if(isset($cat_data['category_id'])){
                                  if($cat_id == $cat_data['category_id']){
                                    $is_checked = "checked='checked'";

                                    if(isset($cat_data['min_price'])){
                                      if(!empty($cat_data['min_price'])){
                                        $min_price = $cat_data['min_price'];
                                      }
                                    }
                                    if(isset($cat_data['max_price'])){
                                      if(!empty($cat_data['max_price'])){
                                        $max_price = $cat_data['max_price'];
                                      }
                                    }
                                    break;
                                  }
                                }
                              }
                          ?>
                          <a class="list-group-item category_name cn_title_<?php echo $value['category_id']; ?> " data-cat-id="<?php echo $value['category_id']; ?>">
                            <li>
                              <div class="checkbox">
                                <input id="checkbox-7-<?php echo $inc ;?>" type="checkbox" class="category_name cn_title_<?php echo $value['category_id']; ?>" id="category-<?php echo $value['category_id']; ?>" value="<?php echo $value['category_id']; ?>"
                                data-cat-id="<?php echo $value['category_id']; ?>" name="category[<?php echo $value['category_id'];?>][category_id]" <?php echo $is_checked; ?> />
                                <label for="checkbox-7-<?php echo $inc ;?>"><span><?php echo $value['category_name']; ?></span></label>
                              </div>
                            </li>
                          </a>
                          <div class="list-group-item lgi_<?php echo $value['category_id']; ?>" style="display: none;">
                            <!-- Start price range Filter-->
                            <div class="form-group">
                              <label class="col-sm-2 control-label" for="input-language"><?php echo $entry_price_filter; ?></label>
                              <div class="col-sm-10">
                                <ul>
                                  <li>
                                    <input id="price-min-<?php echo $value['category_id']; ?>" class="price-min" type="text" value="<?php if(!empty($min_price)) { echo $min_price; } ?>" placeholder="<?php echo $entry_price_min; ?>" data-cat-id="<?php echo $value['category_id']; ?>" name="category[<?php echo $value['category_id'];?>][price-min]"/>
                                  </li>
                                  <li>
                                    <input id="price-max-<?php echo $value['category_id']; ?>" class="price-max" type="text" value="<?php if(!empty($max_price)){ echo $max_price; }  ?>" placeholder="<?php echo $entry_price_max; ?>" data-cat-id="<?php echo $value['category_id']; ?>" name="category[<?php echo $value['category_id'];?>][price-max]"/>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <!-- END price range filter--> 
                            <!-- Start Filter -->
                            <div class="form-group">
                              <label class="col-sm-2 control-label" for="input-language"><?php echo $entry_filters; ?></label>
                              <div class="col-sm-10">
                              <?php 
                                $category_id = $cat_id;
                                $filter_ids = $this->model_sale_customer->get_customer_preferences_filter($this->request->get['customer_id'], $category_id);
                               ?>
                                <div class="filter_box">
                                  <div class="list-group" id="list-group-filter">
                                    <?php if(isset($value['filters'])){
                                        foreach ($value['filters'] as $filter) {
                                          $is_checked = "";
                                          $ids = array();
                                          $fil_id = $filter['filter_id'];
                                          if(isset($filter_ids)){
                                            if(!empty($filter_ids)){
                                              $ids[] = explode(',', $filter_ids['filter_id']);  
                                              foreach ($ids[0] as $id) {
                                                if($fil_id == $id){
                                                  $is_checked = "checked='checked'";
                                                  break;
                                                }
                                              }
                                            }
                                          } ?>
                                          <div class="checkbox">
                                            <label>
                                              <input type="checkbox" id="filter<?php echo $filter['filter_id'];?>" name="category[<?php echo $value['category_id'];?>][filter][]" value="<?php echo $filter['filter_id']; ?>" <?php echo $is_checked;?> />
                                              <?php echo $filter['filter_name']; ?>
                                            </label>
                                          </div>
                                    <?php } 
                                      } ?>
                                  </div>  
                                </div>
                              </div>
                            </div>
                            <!-- END Filter-->                       
                          </div>
                        </ul>
                        <?php $inc++;
                      } ?>
                    </div>  
                  </div> 
                </div>
              </div>
              <!-- END Category filter -->
            </div>

            <div class="tab-pane" id="tab-franchise">
                <div class="form-group franchise_status_group">
                    <label class="col-sm-2 control-label">Franchise Status</label>
                    <div class="col-sm-10">
                        <select name="franchise_status" id="franchise_status" class="form-control edit_track" data-block-name="general" data-change="false" data-old-value="<?php echo $franchise_status; ?>">
                        <option value="">--Select--</option>
                        <option value="1" <?php echo ($franchise_status == '1') ? 'selected' : ''?> >Active</option>
                        <option value="0" <?php echo ($franchise_status == '0') ? 'selected' : ''?> >In-Active</option>
                        </select>
                    </div>
                </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-franchise-coupon"><?php echo $entry_franchise_coupon; ?></label>
                <?php if(empty($franchise_coupon)) { ?>
                  <div class="col-sm-10">
                    <input type="text" name="franchise_coupon" value="<?php echo $franchise_coupon; ?>" id="input-franchise-coupon" class="form-control edit_track" data-block-name="franchise" data-change="false" data-old-value="<?php echo $franchise_coupon; ?>"/>
                  </div>
                <?php } else { ?>
                  <div class="col-sm-10">
                    <label><?php echo $franchise_coupon; ?></label>
                    <input type="hidden" name="franchise_coupon" value="<?php echo $franchise_coupon; ?>" id="input-franchise-coupon" class="form-control edit_track" data-block-name="franchise" data-change="false" data-old-value="<?php echo $franchise_coupon; ?>"/>
                  </div>
                <?php } ?>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-franchise-discount"><?php echo $entry_franchise_discount; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="franchise_discount" value="<?php echo $franchise_discount; ?>" id="input-franchise-discount" class="form-control edit_track" data-block-name="franchise" data-change="false" data-old-value="<?php echo $franchise_discount; ?>"/>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-franchise-prefix"><?php echo $entry_franchise_prefix; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="franchise_prefix" value="<?php echo $franchise_prefix; ?>" id="input-franchise-prefix" class="form-control edit_track" data-block-name="franchise" data-change="false" data-old-value="<?php echo $franchise_prefix; ?>"/>
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
<script type="text/javascript">

function gst_number_valid()
{
  var gst_number = $("#input-gst_number").val();
   gst_number = gst_number.toUpperCase();
   $("#input-gst_number").val(gst_number);

}
$('#input-gst_number').on('blur', function() {
    var gst_number = $("#input-gst_number").val();
    if(gst_number != "" && !(gstin_validatation(gst_number))) {
        $("#gst_error").html('<div class="text-danger">Invalid GST Number !!!</div>');
        $("#cs-submit-button").prop("disabled", true);
        return false;
    } else {
        $("#gst_error").html('');
        $("#cs-submit-button").prop("disabled", false);
    }
});

<!--
$('.date').datetimepicker({
    pickTime: false,
    minDate: new Date()
});
//--></script> 
  <script type="text/javascript"><!--
var address_row = <?php echo $address_row; ?>;

function addAddress() {
	html  = '<div class="tab-pane" id="tab-address' + address_row + '">';
	html += '  <input type="hidden" name="address[' + address_row + '][address_id]" value="" />';

	html += '  <div class="form-group required">';
	html += '    <label class="col-sm-2 control-label" for="input-firstname' + address_row + '"><?php echo $entry_firstname; ?></label>';
	html += '    <div class="col-sm-10"><input type="text" name="address[' + address_row + '][firstname]" value="" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname' + address_row + '" class="form-control" /></div>';
	html += '  </div>';

	html += '  <div class="form-group">';
	html += '    <label class="col-sm-2 control-label" for="input-lastname' + address_row + '"><?php echo $entry_lastname; ?></label>';
	html += '    <div class="col-sm-10"><input type="text" name="address[' + address_row + '][lastname]" value="" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname' + address_row + '" class="form-control" /></div>';
	html += '  </div>';

	html += '  <div class="form-group">';
	html += '    <label class="col-sm-2 control-label" for="input-company' + address_row + '"><?php echo $entry_company; ?></label>';
	html += '    <div class="col-sm-10"><input type="text" name="address[' + address_row + '][company]" value="" placeholder="<?php echo $entry_company; ?>" id="input-company' + address_row + '" class="form-control" /></div>';
	html += '  </div>';

	html += '  <div class="form-group required">';
	html += '    <label class="col-sm-2 control-label" for="input-address-1' + address_row + '"><?php echo $entry_address_1; ?></label>';
	html += '    <div class="col-sm-10"><input type="text" name="address[' + address_row + '][address_1]" value="" placeholder="<?php echo $entry_address_1; ?>" id="input-address-1' + address_row + '" class="form-control" /></div>';
	html += '  </div>';

	html += '  <div class="form-group">';
	html += '    <label class="col-sm-2 control-label" for="input-address-2' + address_row + '"><?php echo $entry_address_2; ?></label>';
	html += '    <div class="col-sm-10"><input type="text" name="address[' + address_row + '][address_2]" value="" placeholder="<?php echo $entry_address_2; ?>" id="input-address-2' + address_row + '" class="form-control" /></div>';
	html += '  </div>';

	html += '  <div class="form-group required">';
	html += '    <label class="col-sm-2 control-label" for="input-city' + address_row + '"><?php echo $entry_city; ?></label>';
	html += '    <div class="col-sm-10"><input type="text" name="address[' + address_row + '][city]" value="" placeholder="<?php echo $entry_city; ?>" id="input-city' + address_row + '" class="form-control" /></div>';
	html += '  </div>';

	html += '  <div class="form-group required">';
	html += '    <label class="col-sm-2 control-label" for="input-postcode' + address_row + '"><?php echo $entry_postcode; ?></label>';
	html += '    <div class="col-sm-10"><input type="text" name="address[' + address_row + '][postcode]" value="" placeholder="<?php echo $entry_postcode; ?>" id="input-postcode' + address_row + '" class="form-control" /></div>';
	html += '  </div>';

	html += '  <div class="form-group required">';
	html += '    <label class="col-sm-2 control-label" for="input-country' + address_row + '"><?php echo $entry_country; ?></label>';
	html += '    <div class="col-sm-10"><select name="address[' + address_row + '][country_id]" id="input-country' + address_row + '" onchange="country(this, \'' + address_row + '\', \'0\');" class="form-control">';
    html += '         <option value=""><?php echo $text_select; ?></option>';
    <?php foreach ($countries as $country) { ?>
    html += '         <option value="<?php echo $country['country_id']; ?>"><?php echo addslashes($country['name']); ?></option>';
    <?php } ?>
    html += '      </select></div>';
	html += '  </div>';

	html += '  <div class="form-group required">';
	html += '    <label class="col-sm-2 control-label" for="input-zone' + address_row + '"><?php echo $entry_zone; ?></label>';
	html += '    <div class="col-sm-10"><select name="address[' + address_row + '][zone_id]" id="input-zone' + address_row + '" class="form-control"><option value=""><?php echo $text_none; ?></option></select></div>';
	html += '  </div>';

	html += '  <div class="form-group">';
	html += '    <label class="col-sm-2 control-label"><?php echo $entry_default; ?></label>';
	html += '    <div class="col-sm-10"><label class="radio"><input type="radio" name="address[' + address_row + '][default]" value="1" /></label></div>';
	html += '  </div>';

    html += '</div>';

	$('#tab-general .tab-content').append(html);

	$('select[name=\'address[' + address_row + '][country_id]\']').trigger('change');

	$('#address-add').before('<li><a href="#tab-address' + address_row + '" data-toggle="tab"><i class="fa fa-minus-circle" onclick="$(\'#address a:first\').tab(\'show\'); $(\'a[href=\\\'#tab-address' + address_row + '\\\']\').parent().remove(); $(\'#tab-address' + address_row + '\').remove();"></i> <?php echo $tab_address; ?> ' + address_row + '</a></li>');

	$('#address a[href=\'#tab-address' + address_row + '\']').tab('show');

	$('.date').datetimepicker({
		pickTime: false
	});
	
	$('.datetime').datetimepicker({
		pickDate: true,
		pickTime: true
	});
	
	$('.time').datetimepicker({
		pickDate: false
	});	
	
	$('#tab-address' + address_row + ' .form-group[data-sort]').detach().each(function() {
		if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#tab-address' + address_row + ' .form-group').length) {
			$('#tab-address' + address_row + ' .form-group').eq($(this).attr('data-sort')).before(this);
		}

		if ($(this).attr('data-sort') > $('#tab-address' + address_row + ' .form-group').length) {
			$('#tab-address' + address_row + ' .form-group:last').after(this);
		}

		if ($(this).attr('data-sort') < -$('#tab-address' + address_row + ' .form-group').length) {
			$('#tab-address' + address_row + ' .form-group:first').before(this);
		}
	});
	
	address_row++;
}
//--></script> 
  <script type="text/javascript"><!--
function country(element, index, zone_id) {
	$.ajax({
		url: 'index.php?route=sale/customer/country&token=<?php echo $token; ?>&country_id=' + element.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'address[' + index + '][country_id]\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
		},
		complete: function() {
			$('.fa-spin').remove();
		},
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('input[name=\'address[' + index + '][postcode]\']').parent().parent().addClass('required');
			} else {
				$('input[name=\'address[' + index + '][postcode]\']').parent().parent().removeClass('required');
			}

			html = '<option value=""><?php echo $text_select; ?></option>';

			if (json['zone'] && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
					html += '<option value="' + json['zone'][i]['zone_id'] + '"';

					if (json['zone'][i]['zone_id'] == zone_id) {
						html += ' selected="selected"';
					}

					html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0"><?php echo $text_none; ?></option>';
			}

			$('select[name=\'address[' + index + '][zone_id]\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

$('select[name$=\'[country_id]\']').trigger('change');
//--></script> 
  <script type="text/javascript"><!--
$('#history').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#history').load(this.href);
});

$('#history').load('index.php?route=sale/customer/history&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>');

$('#button-history').on('click', function(e) {
  e.preventDefault();

	$.ajax({
		url: 'index.php?route=sale/customer/history&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>',
		type: 'post',
		dataType: 'html',
		data: 'comment=' + encodeURIComponent($('#tab-history textarea[name=\'comment\']').val()),
		beforeSend: function() {
			$('#button-history').button('loading');
		},
		complete: function() {
			$('#button-history').button('reset');
		},
		success: function(html) {
			$('.alert').remove();

			$('#history').html(html);

			$('#tab-history textarea[name=\'comment\']').val('');
		}
	});
});
//--></script> 
  <script type="text/javascript"><!--
$('#transaction').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#transaction').load(this.href);
});

$('#transaction').load('index.php?route=sale/customer/transaction&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>');

$('#button-transaction').on('click', function(e) {
  e.preventDefault();

  $.ajax({
		url: 'index.php?route=sale/customer/transaction&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>',
		type: 'post',
		dataType: 'html',
		data: 'description=' + encodeURIComponent($('#tab-transaction input[name=\'description\']').val()) + '&amount=' + encodeURIComponent($('#tab-transaction input[name=\'amount\']').val()),
		beforeSend: function() {
			$('#button-transaction').button('loading');
		},
		complete: function() {
			$('#button-transaction').button('reset');
		},
		success: function(html) {
			$('.alert').remove();

			$('#transaction').html(html);

			$('#tab-transaction input[name=\'amount\']').val('');
			$('#tab-transaction input[name=\'description\']').val('');
		}
	});
});
//--></script> 
  <script type="text/javascript"><!--
$('#cashback').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#cashback').load(this.href);
});

$('#cashback').load('index.php?route=sale/customer/cashback&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>');

//--></script> 
  <script type="text/javascript"><!--
$('#cashback-usage').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#cashback-usage').load(this.href);
});

$('#cashback-usage').load('index.php?route=sale/customer/cashbackUsage&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>');

//--></script> 
  <script type="text/javascript"><!--
$('#reward').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#reward').load(this.href);
});

$('#reward').load('index.php?route=sale/customer/reward&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>');

$('#button-reward').on('click', function(e) {
	e.preventDefault();

	$.ajax({
		url: 'index.php?route=sale/customer/reward&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>',
		type: 'post',
		dataType: 'html',
		data: 'description=' + encodeURIComponent($('#tab-reward input[name=\'description\']').val()) + '&points=' + encodeURIComponent($('#tab-reward input[name=\'points\']').val()),
		beforeSend: function() {
			$('#button-reward').button('loading');
		},
		complete: function() {
			$('#button-reward').button('reset');
		},
		success: function(html) {
			$('.alert').remove();

			$('#reward').html(html);

			$('#tab-reward input[name=\'points\']').val('');
			$('#tab-reward input[name=\'description\']').val('');
		}
	});
});

$('#content').delegate('button[id^=\'button-custom-field\'], button[id^=\'button-address\']', 'click', function() {
	var node = this;
	
	$('#form-upload').remove();
	
	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#form-upload input[name=\'file\']').trigger('click');
	
	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}
	
	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);
			
			$.ajax({
				url: 'index.php?route=tool/upload/upload&token=<?php echo $token; ?>',
				type: 'post',		
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,		
				beforeSend: function() {
					$(node).button('loading');
				},
				complete: function() {
					$(node).button('reset');
				},		
				success: function(json) {
					$(node).parent().find('.text-danger').remove();
					
					if (json['error']) {
						$(node).parent().find('input[type=\'hidden\']').after('<div class="text-danger">' + json['error'] + '</div>');
					}
								
					if (json['success']) {
						alert(json['success']);
					}
					
					if (json['code']) {
						$(node).parent().find('input[type=\'hidden\']').attr('value', json['code']);
					}
				},			
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});

$('.date').datetimepicker({
	pickTime: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});

$('.time').datetimepicker({
	pickDate: false
});	

// Sort the custom fields
<?php $address_row = 1; ?>
<?php foreach ($addresses as $address) { ?>
$('#tab-address<?php echo $address_row ?> .form-group[data-sort]').detach().each(function() {
	if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#tab-address<?php echo $address_row ?> .form-group').length) {
		$('#tab-address<?php echo $address_row ?> .form-group').eq($(this).attr('data-sort')).before(this);
	}

	if ($(this).attr('data-sort') > $('#tab-address<?php echo $address_row ?> .form-group').length) {
		$('#tab-address<?php echo $address_row ?> .form-group:last').after(this);
	}

	if ($(this).attr('data-sort') < -$('#tab-address<?php echo $address_row ?> .form-group').length) {
		$('#tab-address<?php echo $address_row ?> .form-group:first').before(this);
	}
});
<?php $address_row++; ?>
<?php } ?>


<?php foreach ($addresses as $address) { ?>
$('#tab-customer .form-group[data-sort]').detach().each(function() {
	if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#tab-customer .form-group').length) {
		$('#tab-customer .form-group').eq($(this).attr('data-sort')).before(this);
	}

	if ($(this).attr('data-sort') > $('#tab-customer .form-group').length) {
		$('#tab-customer .form-group:last').after(this);
	}

	if ($(this).attr('data-sort') < -$('#tab-customer .form-group').length) {
		$('#tab-customer .form-group:first').before(this);
	}
});
<?php } ?>
//--></script></div>
<script type="text/javascript"><!--
  $('.filter_name').click(function() {
    var data_id = $(this).attr('data-id');
    $(".lgi_"+data_id).toggle(500);
  });
  $('.category_name').click(function() {
    var data_id = $(this).attr('data-cat-id');
    $(".lgi_"+data_id).toggle(500);
  });

</script>
<?php echo $footer; ?>
<!-- FOR IFSC code -->
<script type="text/javascript">
    $(document).ready(function(){
        $('input[name=\'ifsc_code\']').on('keyup',function(e){
            var filter = /^([a-zA-Z0-9]){1}$/;

            if(($(this).val().length==11) && filter.test(e.key)){
              getIfscCodeDetails(this,'.bank_details_block', '#ifsc_code_loading');
            } else {
              $('#content #cs-submit-button').attr('disabled',true);
              $('.bank_details_block').hide();
            }
            if($(this).val().length==0){
              $('#content #cs-submit-button').attr('disabled',false);
            }

            //Commented the previous method for button disabling based on Wrong IFSC code and saving details
            /*
            if (filter.test(e.key)){
              getIfscCodeDetails(this,'.bank_details_block', '#ifsc_code_loading');   
            }else{
                $('.bank_details_block').hide();
               // $('#ms-submit-button').attr('disabled',true);
            }
            */      
        });
    
    // form submit after some information change of seller
    var old_data_format = {};
    $('.edit_track').on('change',function(){
        $(this).attr('data-change','true');
        var data_change = $(this).data('change');
        var previous_value = $(this).data('old-value');
        if(typeof previous_value === 'string'){
          previous_value = previous_value.replace(/\r?\n|\r/g,'');
          previous_value = previous_value.replace(/"/g, '\\"'); 
        }
        var new_value = $(this).val();
        if(typeof new_value === 'string'){
          new_value = new_value.replace(/\r?\n|\r/g,'');
          new_value = new_value.replace(/"/g, '\\"'); 
        }
        var update_group = $(this).data('block-name');
        if(typeof update_group === 'string'){
          update_group = update_group.replace(/\r?\n|\r/g,'');
          update_group = update_group.replace(/"/g, '\\"'); 
        }
        var name = $(this).attr('name');
    
        if( data_change ) {
          old_data_format[name] = $.parseJSON('{"previous_value":"'+  previous_value + '","new_value":"' + new_value + '","update_group":"' + update_group + '"}');
        }
        $('#changes_data').val( JSON.stringify(old_data_format) );
      });
    });

    $('.edit_track').each(function(){
      
    });

    function getIfscCodeDetails(obj, class_ctn, id_loading){
        var ifsc_code = $(obj).val().toUpperCase();
        $(obj).val(ifsc_code);
        if(ifsc_code.length == 11){
            flag = false;
            $.ajax({
                url : 'index.php?route=sellers/sellers/bankDetails&token=<?php echo $token;?>&ifsc_code='+ifsc_code ,
                dataType : 'json',
                async: false,
                beforeSend: function() {
                    setTimeout(function(){
                        $(id_loading).button('loading');
                        $(id_loading).removeClass('hidden');
                    },0);
                },
                complete: function() {
                    setTimeout(function(){
                        $(id_loading).button('reset');
                        if(id_loading == '#ifsc_code_loading'){
                            $(id_loading).addClass('hidden');
                        }
                    },1000);
                },

                success:function(json){
                    $('.customer-ifsc').hide();
                    if(typeof(json) == 'object'){ 
                        $(".customer-ifsc").remove();
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

                        $(class_ctn).html(html);
                        $(class_ctn).show();
                        // $('input[name=\'seller[bank_name]\']').val(json['BANK']);
                        // $('input[name=\'seller[bank_branch]\']').val(json['BRANCH']);
                        // $('input[name=\'seller[bank_city]\']').val(json['CITY']);
                        // $('input[name=\'seller[bank_state]\']').val(json['STATE']);
                        $('#cs-submit-button').removeAttr('disabled',true);
                        validateBankDetails(false);
                        flag = true;
                    }else{
                         
                        $('#cs-submit-button').attr('disabled',true);
                        $('.error-ms-bank').remove();
                        $(obj).parent().find('.sample_ifsc_code').after('<span class="alert-danger customer-ifsc">'+ json +'</span>');
                        $(class_ctn).html('');
                        flag = false;
                    }
                },
            });

            return flag;
        }else if(ifsc_code.length == 0){
            validateBankDetails(false);
            $(class_ctn).html(''); 
            return false;
                       
        }else{
            $('#cs-submit-button').attr('disabled',true);
            $(class_ctn).html('');
            return false;
        }
    }

    $(function(){
        getIfscCodeDetails('input[name=\'ifsc_code\']','.bank_details_block','#ifsc_code_loading');
    });

    function validateBankDetails(callgetifsc = true){
        var ac_name = $('input[name="bank_ac_holder_name"]').val().trim();
        var ac_no = $('input[name="bank_ac_number"]').val().trim();
        var ifsc = $('input[name="ifsc_code"]').val().trim();
        var error = [];
        
        $('#cs-submit-button').attr('disabled',false);
        if(callgetifsc){
          getIfscCodeDetails('input[name=\'ifsc_code\']','.bank_details_block', '#ifsc_code_loading'); 
        }
    }

</script>
