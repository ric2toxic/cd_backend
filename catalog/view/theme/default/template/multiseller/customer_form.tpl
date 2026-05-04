<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container">
      <div class="pull-right">
        <button type="submit" form="form-customer" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-info"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?>
<!-- deleting dropship -->
      </h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-customer" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
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
                          <input type="text" name="firstname" value="<?php echo $firstname; ?>" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname" class="form-control" />
                          <?php if ($error_firstname) { ?>
                          <div class="text-danger"><?php echo $error_firstname; ?></div>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-lastname"><?php echo $entry_lastname; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="lastname" value="<?php echo $lastname; ?>" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname" class="form-control" />
                          <?php // if ($error_lastname) { ?>
                          <!-- <div class="text-danger"><?php //echo $error_lastname; ?></div> -->
                          <?php //} ?>
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
                          <?php if ($error_email) { ?>
                          <div class="text-danger"><?php echo $error_email; ?></div>
                          <?php  } ?>
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="telephone" value="<?php echo $telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control" />
                          <?php if ($error_telephone) { ?>
                          <div class="text-danger"><?php echo $error_telephone; ?></div>
                          <?php  } ?>
                        </div>
                      </div>

                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-password"><?php echo $entry_password; ?></label>
                        <div class="col-sm-10">
                          <input type="password" name="password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control" autocomplete="off" />
                          <?php if ($error_password) { ?>
                          <div class="text-danger"><?php echo $error_password; ?></div>
                          <?php  } ?>
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-confirm"><?php echo $entry_confirm; ?></label>
                        <div class="col-sm-10">
                          <input type="password" name="confirm" value="<?php echo $confirm; ?>" placeholder="<?php echo $entry_confirm; ?>" autocomplete="off" id="input-confirm" class="form-control" />
                          <?php if ($error_confirm) { ?>
                          <div class="text-danger"><?php echo $error_confirm; ?></div>
                          <?php  } ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                          <select name="status" id="input-status" class="form-control">
                            <?php if ($status) { ?>
                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                            <option value="0"><?php echo $text_disabled; ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $text_enabled; ?></option>
                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>



                      <div class="form-group hidden">
                          <label class="col-sm-2 control-label">Dropshipper</label>

                          <div class="col-sm-10">
 <!--                            <select name="is_dropshipper" id="input-is_dropshipper" class="form-control">
                              <?php //if ($is_dropshipper) { ?>
                                <option value="0" <?//php echo ($is_dropshipper == 0) ? 'selected="selected"' : ''; ?>><?php //echo $text_select; ?></option>
                                <option value="1" <?php// echo ($is_dropshipper == 1) ? 'selected="selected"' : ''; ?>><?php //echo $text_active; ?></option>
                                <option value="2" <?php //echo ($is_dropshipper == 2) ? 'selected="selected"' : ''; ?>><?php //echo $text_panding; ?></option>
                                <option value="3" <?php// echo ($is_dropshipper == 3) ? 'selected="selected"' : ''; ?>><?php //echo $text_block; ?></option>
                              <?php //} else { ?>
                                <option value="0"><?php// echo $text_select; ?></option>
                                <option value="1"><?php// echo $text_active; ?></option>
                                <option value="2"><?php //echo $text_panding; ?></option>
                                <option value="3"><?php //echo $text_block; ?></option>
                              <?php //} ?>
                            </select>


                             <label>  --> <input type="hidden" name="is_dropshipper" value="0" />

                          </div>
                      </div>


                      <!-- Credit system is_credit_allowed & max_credit_limit etc... -->
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-credit-allowed"><?php echo $text_credit_allowed; ?></label>
                        <div class="col-sm-10">
                          <?php if(isset($is_credit_allowed) && $is_credit_allowed == 1) { ?>
                              <input type="checkbox" class="credit_allowed_checked" name="customer_credit_allowed" value="1" checked="checked"/>
                          <?php }else{ ?>
                              <input type="checkbox" class="credit_allowed_checked" name="customer_credit_allowed" value="0"/>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group input_max_credit_limit customer_input_text ">
                        <label class="col-sm-2 control-label" for="input-max-credit-limit"><?php echo $text_max_credit_limit; ?></label>
                        <div class="col-sm-10">
                          <?php if(isset($max_credit_limit) && !is_null($max_credit_limit)) { ?>
                              <input type="text" class="form-control" name="customer_max_credit_limit" value="<?php echo $max_credit_limit;?>" />
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group input_credit_limit_consumed customer_input_text">
                        <label class="col-sm-2 control-label" for="input-credit-limit-consumed"><?php echo $text_credit_limit_consumed; ?></label>
                        <div class="col-sm-10">
                          <?php if(isset($credit_limit_consumed) && !is_null($credit_limit_consumed) ) { ?>
                            <input type="text" class="form-control" name="customer_credit_limit_consumed" value="<?php echo $credit_limit_consumed; ?>" />
                          <?php } ?>
                        </div>
                      </div>
                      <?php /* ?>
                      <!--<div class="form-group input_credit_limit_consumed customer_input_text">
                        <label class="col-sm-2 control-label" for="input-credit-limit-consumed"><?php //echo $text_credit_limit_consumed; ?></label>
                        <div class="col-sm-10">
                          <button class="btn btn-warning reset_credit" type="button" >Reset Credit</button>
                        </div>
                      </div> -->
                      <?php */ ?>

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
                          <input type="text" name="address[<?php echo $address_row; ?>][address_1]" value="<?php echo $address['address_1']; ?>" placeholder="<?php echo $entry_address_1; ?>" id="input-address-1<?php echo $address_row; ?>" class="form-control" />
                          <?php if (isset($error_address[$address_row]['address_1'])) { ?>
                          <div class="text-danger"><?php echo $error_address[$address_row]['address_1']; ?></div>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-address-2<?php echo $address_row; ?>"><?php echo $entry_address_2; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="address[<?php echo $address_row; ?>][address_2]" value="<?php echo $address['address_2']; ?>" placeholder="<?php echo $entry_address_2; ?>" id="input-address-2<?php echo $address_row; ?>" class="form-control" />
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-city<?php echo $address_row; ?>"><?php echo $entry_city; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="address[<?php echo $address_row; ?>][city]" value="<?php echo $address['city']; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city<?php echo $address_row; ?>" class="form-control" />
                          <?php if (isset($error_address[$address_row]['city'])) { ?>
                          <div class="text-danger"><?php echo $error_address[$address_row]['city']; ?></div>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-postcode<?php echo $address_row; ?>"><?php echo $entry_postcode; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="address[<?php echo $address_row; ?>][postcode]" value="<?php echo $address['postcode']; ?>" placeholder="<?php echo $entry_postcode; ?>" id="input-postcode<?php echo $address_row; ?>" class="form-control" />
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
                            <input type="radio" name="address[<?php echo $address_row; ?>][default]" value="<?php echo $address_row; ?>" checked="checked" />
                            <?php } else { ?>
                            <input type="radio" name="address[<?php echo $address_row; ?>][default]" value="<?php echo $address_row; ?>" />
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
                      <?php  $inc = 5;
                      foreach($data['categories'] as $value){
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
                                      }else{
                                        $min_price = "";
                                      }
                                    }else{
                                      $min_price = "";
                                    }

                                    if(isset($cat_data['max_price'])){
                                      if(!empty($cat_data['max_price'])){
                                        $max_price = $cat_data['max_price'];
                                      }else{
                                        $max_price = "";
                                      }
                                    }else{
                                        $max_price = "";
                                    }

                                    break;
                                  }else{
                                    $is_checked = "";
                                    $min_price = "";
                                    $max_price = "";
                                  }
                                }else{
                                  $is_checked = "";
                                  $min_price = "";
                                  $max_price = "";
                                }

                              }

                           ?>
                            <a class="list-group-item category_name cn_title_<?php echo $value['category_id']; ?> " data-cat-id="<?php echo $value['category_id']; ?>">
                              <li>
                                <div class="checkbox">
                                  <input id="checkbox-7-<?php echo $inc ;?>" type="checkbox" class="category_name cn_title_<?php echo $value['category_id']; ?>" id="category-<?php echo $value['category_id']; ?>" value="<?php echo $value['category_id']; ?>"
                                  data-cat-id="<?php echo $value['category_id']; ?>" name="category[<?php echo $value['category_id'];?>][category_id]" <?php echo $is_checked;?>/>
                                  <label for="checkbox-7-<?php echo $inc ;?>"><span><?php echo $value['name']; ?></span></label>
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
                                  $filter_groups = array();
                                  $filter_grp = $this->model_sale_customer->getFiltersOfProducts($category_id);
                                  $filter_ids = $this->model_sale_customer->get_customer_preferences_filter($this->request->get['customer_id'], $category_id);
                                  if ($filter_grp) {
                                    foreach ($filter_grp as $filter_group) {
                                      $childen_data = array();

                                      foreach ($filter_group['filter'] as $filter) {
                                        $filter_data = array(
                                          'filter_category_id' => $category_id,
                                          'filter_sub_category' => true,
                                          'filter_filter'      => $filter['filter_id']
                                        );

                                        $childen_data[] = array(
                                          'filter_id' => $filter['filter_id'],
                                          'name'      => $filter['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_sale_customer->getTotalProducts($filter_data) . ')' : ''),
                                        );
                                      }

                                      $filter_groups[] = array(
                                        'filter_group_id' => $filter_group['filter_group_id'],
                                        'name'            => $filter_group['name'],
                                        'description'     => $filter_group['description'],
                                        'filter'          => $childen_data
                                      );
                                    }
                                  }
                             ?>
                              <div class="filter_box">
                                <div class="list-group" id="list-group-filter">
                                  <?php foreach ($filter_groups as $filter_group) { ?>
                                  <a class="list-group-item filter_name fg_title_<?php echo $filter_group['filter_group_id']; ?> " cursor="pointer" data-id="<?php echo $filter_group['filter_group_id']; ?>"><?php echo $filter_group['name']; ?></a>
                                  <div class="list-group-item lgi_<?php echo $filter_group['filter_group_id']; ?>" style="display: none;">
                                    <div id="filter-group<?php echo $filter_group['filter_group_id']; ?>">
                                      <div class="filter_group_description"><?php echo $filter_group['description']; ?></div>
                                      <?php foreach ($filter_group['filter'] as $filter) {
                                        $ids = array();
                                        $fil_id = $filter['filter_id'];
                                        if(isset($filter_ids)){
                                          if(!empty($filter_ids)){
                                            $ids[] = explode(',', $filter_ids['filter_id']);
                                            foreach ($ids[0] as $id) {
                                              if($fil_id == $id){
                                                $is_checked = "checked='checked'";
                                                break;
                                              }else{
                                                $is_checked = "";
                                              }
                                            }
                                          }else{
                                            $is_checked = "";
                                          }
                                        }
                                      ?>
                                      <div class="checkbox">
                                        <label>
                                          <input type="checkbox" id="filter<?php echo $filter['filter_id'];?>" name="category[<?php echo $value['category_id'];?>][filter][]" value="<?php echo $filter['filter_id']; ?>" <?php echo $is_checked;?> />
                                          <?php echo $filter['name']; ?>
                                        </label>
                                      </div>
                                      <?php } ?>
                                    </div>
                                  </div>
                                  <?php } ?>
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
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
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
		url: 'index.php?route=seller/account-customer/country&token=<?php echo $token; ?>&country_id=' + element.value,
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

$('#history').load('index.php?route=seller/account-customer/history&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>');

$('#button-history').on('click', function(e) {
  e.preventDefault();

	$.ajax({
		url: 'index.php?route=seller/account-customer/history&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>',
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

$('#transaction').load('index.php?route=seller/account-customer/transaction&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>');

$('#button-transaction').on('click', function(e) {
  e.preventDefault();

  $.ajax({
		url: 'index.php?route=seller/account-customer/transaction&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>',
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
$('#reward').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#reward').load(this.href);
});

$('#reward').load('index.php?route=seller/account-customer/reward&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>');

$('#button-reward').on('click', function(e) {
	e.preventDefault();

	$.ajax({
		url: 'index.php?route=seller/account-customer/reward&token=<?php echo $token; ?>&customer_id=<?php echo $customer_id; ?>',
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


  $('.credit_allowed_checked').change(function(){
    if(this.checked){
      $(this).val(1);
      $('.input_max_credit_limit').show();
      $('.input_credit_limit_consumed').show();
    }else{
      $(this).val(0);
      $('.input_max_credit_limit').hide();
      $('.input_credit_limit_consumed').hide();
    }
  });


  if($('.credit_allowed_checked').is(':checked')){
    $('.input_max_credit_limit').show();
    $('.input_credit_limit_consumed').show();
  }else{
    $('.input_max_credit_limit').hide();
    $('.input_credit_limit_consumed').hide();
  }
</script>
<?php echo $footer; ?>

