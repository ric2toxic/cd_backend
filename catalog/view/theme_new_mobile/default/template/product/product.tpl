<?php echo $header; ?>

<div id="product" class="color_white">


  <?php if ($recurrings) { ?>
  <hr>
  <h3><?php echo $text_payment_recurring ?></h3>
  <div class="form-group required">
    <select name="recurring_id" class="form-control">
      <option value=""><?php echo $text_select; ?></option>
      <?php foreach ($recurrings as $recurring) { ?>
      <option value="<?php echo $recurring['recurring_id'] ?>"><?php echo $recurring['name'] ?></option>
      <?php } ?>
    </select>
    <div class="help-block" id="recurring-description"></div>
  </div>
  <?php } ?>
  <div class="form-group container-fluid">
    <div class="row">

      <div class="col-md-12 col-xs-12">

        <div class="col-md-12 col-sm-12 col-xs-12 qtybox nopadding">

          <div id="bottom_bar">
            <input id="input-quantity"
                   type="text"
                   value="<?php echo $minimum; ?>"
                   name="quantity"
                   data-bts-min="<?php echo $minimum; ?>"
                   data-bts-max="<?php echo $quantity; ?>"
                   data-bts-init-val=""
                   data-bts-step="1"
                   data-bts-decimal="0"
                   data-bts-step-interval="100"
                   data-bts-force-step-divisibility="round"
                   data-bts-step-interval-delay="500"
                   data-bts-prefix=""
                   data-bts-postfix=""
                   data-bts-prefix-extra-class=""
                   data-bts-postfix-extra-class=""
                   data-bts-booster="true"
                   data-bts-boostat="10"
                   data-bts-max-boosted-step="false"
                   data-bts-mousewheel="true"
                   data-bts-button-down-class="btn btn-default"
                   data-bts-button-up-class="btn btn-default"
                   size="2"
                   class="form-control  pull-left touchspin_qty new_inupt_qyt"
            />
            <!-- <div class="btn_addtocart">
               <button type="button" <?php if ($quantity <= 0 || strtolower($stock_status) == 'out of stock') { echo "disabled"; } ?> id="button-cart" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary btn-lg " style="margin-right: 5px"><?php echo $button_cart; ?></button>
             </div>-->


            <button type="button" <?php if ($quantity <= 0 || strtolower($stock_status) == 'out of stock' || $status == 0) { echo "disabled"; } ?> id="button-cart" data-loading-text="<?php echo $text_loading; ?>" class="new_addtocart"><?php echo $button_cart; ?></button>
          </div>
          <div id="hr"></div>
          <div class="container product_page">
            <div class="row">
              <?php $class = 'col-sm-12'; ?>

              <div id="content" class="<?php echo $class; ?> product_popup noshadow width_hunderd"><?php echo $content_top; ?>
                <i class="fa fa-heart fa-5x wishlist_top wishlist_top" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product_id; ?>');"></i>
                <i class="fa fa-times back_icon close_popup"></i>
                </button>

                <div class="row">

                  <div class="col-sm-12 fixed_container" id="main_img_container">
                    <?php if ($thumb || $images) { ?>

                    <?php if ($images) { ?>
                    <?php foreach ($images as $image) { ?>
                    <div >
                      <a class="thumbnail" href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>"> <img src="<?php echo $image['popup']; ?>" width="150px" height="225px" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
                    </div>
                    <?php } ?>
                    <?php } ?>

                    <?php } ?>
                  </div>
                </div>
                <div class="row product_title_margin">
                  <div class="col-sm-12">
                    <span class="product_title product_title_new"><?php echo $heading_title; ?></span> <br />

                    <span><?php echo $text_model; ?> <?php echo $model; ?></span>


                    <ul class="list-unstyled">
                      <?php if ($manufacturer) { ?>
                      <li><?php echo $text_manufacturer; ?> <a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a></li>
                      <?php } ?>
                      <?php
                if($is_single == '1'){

                }else{
              if ($minimum > 1) { ?>
                      <span class="label label-primary"><?php echo $text_moq_pre . $minimum . $text_moq_post; ?></span>
                      <?php } else { ?>
                      <span class="label label-primary"><?php echo $text_moq_default; ?></span> <?php }} ?>
                      </li>
                      <?php

                      <!-- <li><h4><?php echo $stock; ?></li></h4> -->
                    </ul>

                    <?php if ($price) { ?>
                    <ul class="list-unstyled">
                      <?php
              if($is_single == '1'){

                }else{ ?>
                      <h4><font color="#1c1c1c"><?php echo $set_description; ?> <span><a href="<?php echo  $_SERVER['REQUEST_URI'] ; ?>#tabs"><?php echo $text_see_desc; ?></a> </span></font></h4>
                      <?php }?>
                      <?php if (!$special) { ?>
                      <li>
                        <h3><span id ="sell-price"><?php echo $price ?></span><?php echo  $text_per_piece; ?> <span><?php if ($tax_class_id) { echo $text_plus_cst;} ?> </span></h3>
                        <input type= "hidden" id ="product_unit_price" value ="<?php echo filter_var($price, FILTER_SANITIZE_NUMBER_INT); ?>" />
                      </li>
                      <?php } else { ?>
                      <li><span style="text-decoration: line-through;"><?php echo $price . $text_per_piece; ?></span></li>
                      <li>
                        <h2><?php echo $special . $text_per_piece; ?> <span><?php if ($tax_class_id) { echo $text_plus_cst; } ?></span></h2>
                      </li>
                      <?php } ?>
                      <?php /* if ($tax) { ?>
                      <li><?php echo $text_tax; ?> <?php echo $tax; ?></li>
                      <?php } */ ?>
                                  <hr>
                                  <?php
                                        if(!empty($custom_store_selling_price)){
                                           if($is_single== 0){
                                  ?>
                                          <div class="store_information">
                                            <span class="product_title product_title_new">
                                            <a href= <?php echo $custom_store_product_href; ?>><?php echo $text_single;?></a></span>
                                            <h3 class="store_price"><?php echo $this->currency->format((ceil($custom_store_selling_price))); ?><?php echo $text_per_piece; ?>
                                              </h3>
                                            <h5 class="store_tax"><?php if ($tax_class_id) { echo $text_plus_cst;} ?></h5>
                                          </div>
                                        <?php }else{ ?>
                                          <div class="store_information">
                                            <span class="product_title product_title_new">

                                            <a href= <?php echo $custom_store_product_href; ?>><?php echo $text_wholesaleBox;?></a></span>
                                            <h3 class="store_price"><?php echo $this->currency->format((ceil($custom_store_selling_price))); ?><?php echo $text_per_piece; ?>
                                              </h3>
                                            <h5 class="store_tax"><?php if ($tax_class_id) { echo $text_plus_cst;} ?></h5>
                                          </div>
                                       <?php } ?>
                                       <hr>
                                  <?php } ?>
                      <?php if ($points) { ?>
                      <li><?php echo $text_points; ?> <?php echo $points; ?></li>
                      <?php } ?>
                      <?php if ($discounts) { ?>
                      <!--<li>
                        <hr>
                      </li>-->
                      <li class="qty-discount-price">
                        <div class="row">
                          <div class="col-sm-8">
                            <table class="table table-striped">
                              <tr>
                                <th>
                                  <?php echo $entry_qty; ?>
                                </th>
                                <th>
                                  <?php echo $entry_price .$text_per_piece ; ?><?php if ($tax_class_id) { echo $text_tax; } ?>
                                </th>

                              </tr>
                              <?php
                    $i = 0;
                    foreach ($discounts as $discount) {
                    $i++;
                    ?>
                              <tr>
                                <td>
                                  <span class="discount-qty"><?php echo $discount['quantity'] . $text_discount;; ?></span>
                                </td>
                                <td>
                                  <?php echo $discount['price']; ?>
                                </td>
                              </tr>
                              <?php
                    }
                ?>
                            </table>
                          </div>
                          <!--
                          <div class="col-sm-4">
                            <div class="buyer_protection">&nbsp;</div>
                          </div>-->
                        </div>
                        <!-- <br class="clearall" /> -->
                      </li>

                      <?php } ?>
                      <li>
                        <?php
                    /*if($is_single == '1'){
                ?>
                        Availale In Set <h4><font color="#C71585"><?php echo $set_description; ?> <span><a href="<?php echo  $_SERVER['REQUEST_URI'] ; ?>#tabs"><?php //echo $text_see_desc; ?></a> </span></font></h4>
                        <?php }else{
                ?>
                        Availale In Single Piece <h4><font color="#C71585"><?php echo $set_description; ?> <span><a href="<?php echo  $_SERVER['REQUEST_URI'] ; ?>#tabs"><?php //echo $text_see_desc; ?></a> </span></font></h4>
                        <?php
                    }*/
                ?>
                      </li>
                    </ul>
                    <?php } ?>


                    <script>
                      $("input[name='quantity']").TouchSpin({
                      });
                    </script>



                    <!--<input type="text" name="quantity" value="<?php echo $minimum; ?>" size="2" id="input-quantity" class="form-control  pull-left" />-->
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />

                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-12 wishlist nopadding">
                    <br />
                    <!-- <div class="btn-group">
                   <button type="button" data-toggle="tooltip" class="btn btn-default" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product_id; ?>');">
                       <i class="fa fa-heart fa-5x"></i>
                     </button>

                    </div>-->
                    <?php
                        if($is_single == '1'){
                            if(isset($_GET['filter'])){
                                $last_category_href = $last_category_href.'#!filter='.$_GET['filter'];
                            }else{
                                $last_category_href = $last_category_href;
                            }
                        }else{
                            if(isset($_GET['filter'])){
                                $last_category_href = $last_category_href.'#!filter='.$_GET['filter'];
                            }else{
                                $last_category_href = $last_category_href;
                            }
                            //$last_category_href = $last_category_href;
                        }
                        //echo $last_category_href; exit;
                    ?>
                    <?php /* ?> &nbsp;&nbsp;&nbsp;&nbsp;<a class="btn btn-default btn-lg" href="<?php echo $last_category_href; ?>"><?php echo $text_browse_more; ?></a><?php */ ?>
                  </div>

                </div>
                <?php
                if($is_single == '1'){

                }else{
                ?>

                <?php } ?>



              </div>
            <div style="width: 70%; margin-left: 10px;">
              <?php if ($options) { ?>
              <hr>
              <h3><?php echo $text_option; ?></h3>
              <?php $js_var='';foreach ($options as $option) { ?>
              <?php // echo '<pre>'; print_r($option); ?>
              <?php if ($option['type'] == 'select') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?> scroll_to1">
                <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
                <select name="option[<?php echo $option['product_option_id']; ?>]" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control">
                  <option value=""><?php echo $text_select; ?></option>
                  <?php foreach ($option['product_option_value'] as $option_value) { ?>
                  <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                    <?php if ($option_value['price']) { ?>
                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                    <?php } ?>
                  </option>
                  <?php $js_var[] = array( $option_value['price_prefix'], filter_var($option_value['unfor_option_price'], FILTER_SANITIZE_NUMBER_FLOAT), $option_value['product_option_value_id'] );?>

                  <?php //; $x = trim($option_value['price']) ; $price_prefix .= "<input type = hidden
                #id = price_prefix_$option_value[product_option_value_id]
                #value = $option_value[price_prefix] /><input type = 'hidden'
                                                              #id = option_value_$option_value[product_option_value_id]
                                                              #value = $x />" ; ?>

                  <?php } ?>
                </select>
                <?php //echo "<pre>"; print_r($js_var);?>
                <!--<?php// echo $price_prefix; ?>-->
                <?php //echo $x; ?>
                <?php //echo $option_value['price'];?>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'radio') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label"><?php echo $option['name']; ?></label>
                <div id="input-option<?php echo $option['product_option_id']; ?>">
                  <?php foreach ($option['product_option_value'] as $option_value) { ?>
                  <div class="radio">
                    <label>
                      <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" />
                      <?php echo $option_value['name']; ?>
                      <?php if ($option_value['price']) { ?>
                      (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                      <?php } ?>
                    </label>
                  </div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'checkbox') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label"><?php echo $option['name']; ?></label>
                <div id="input-option<?php echo $option['product_option_id']; ?>">
                  <?php foreach ($option['product_option_value'] as $option_value) { ?>
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" />
                      <?php echo $option_value['name']; ?>
                      <?php if ($option_value['price']) { ?>
                      (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                      <?php } ?>
                    </label>
                  </div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'image') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label"><?php echo $option['name']; ?></label>
                <div id="input-option<?php echo $option['product_option_id']; ?>">
                  <?php foreach ($option['product_option_value'] as $option_value) { ?>
                  <div class="radio">
                    <label>
                      <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" />
                      <img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" class="img-thumbnail" /> <?php echo $option_value['name']; ?>
                      <?php if ($option_value['price']) { ?>
                      (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                      <?php } ?>
                    </label>
                  </div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'text') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
                <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'textarea') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
                <textarea name="option[<?php echo $option['product_option_id']; ?>]" rows="5" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control"><?php echo $option['value']; ?></textarea>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'file') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label"><?php echo $option['name']; ?></label>
                <button type="button" id="button-upload<?php echo $option['product_option_id']; ?>" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-default btn-block"><i class="fa fa-upload"></i> <?php echo $button_upload; ?></button>
                <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" id="input-option<?php echo $option['product_option_id']; ?>" />
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'date') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
                <div class="input-group date">
                  <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
                <span class="input-group-btn">
                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                </span></div>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'datetime') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
                <div class="input-group datetime">
                  <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span></div>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'time') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
                <div class="input-group time">
                  <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span></div>
              </div>
              <?php } ?>
              <?php } ?>
              <?php } ?>

</div>
              <?php if ($review_status) { ?>
              <div class="rating">
                <p>
                  <?php for ($i = 1; $i <= 5; $i++) { ?>
                  <?php if ($rating < $i) { ?>
                  <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
                  <?php } else { ?>
                  <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
                  <?php } ?>
                  <?php } ?>
                  <a href="" onclick="$('a[href=\'#tab-review\']').trigger('click'); return false;"><?php echo $reviews; ?></a> / <a href="" onclick="$('a[href=\'#tab-review\']').trigger('click'); return false;"><?php echo $text_write; ?></a></p>
                <?php /* ?> <hr>
                <!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_default_style"><a class="addthis_button_facebook_like" fb:like:layout="button_count"></a> <a class="addthis_button_tweet"></a> <!-- <a class="addthis_button_pinterest_pinit"></a> <a class="addthis_counter addthis_pill_style"></a> --> </div>
                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-515eeaf54693130e"></script>
                <!-- AddThis Button END -->
                <?php */ ?>
              </div>
              <?php } ?>
            </div>
          </div>

        <div class="container nopadding ">
          <ul class="nav nav-tabs" id="tabs">
            <li class="active" ><a href="#tab-description" data-toggle="tab"><?php echo $tab_description; ?></a></li>
            <?php if ($attribute_groups) { ?>
            <li><a href="#tab-specification" data-toggle="tab"><?php echo $tab_attribute; ?></a></li>
            <?php } ?>
            <?php if ($review_status) { ?>
            <li><a href="#tab-review" data-toggle="tab"><?php echo $tab_review; ?></a></li>
            <?php } ?>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active tab_margin" id="tab-description">

              <div class="row">
                <div class="col-md-6">
                  <p class="set_descriptioin">
                    <?php echo $set_description; ?>
                  </p>
                  <?php echo $description; ?>
                  </br></br>
                </div>
                <div class="col-md-6">
                  <!-- added by kuldeep -->
                  <?php
               if(isset($filters) && count($filters) > 0){
                  ?>

                  <table class="table table-striped ">


                    <?php
            foreach($filters as $filter){

          ?>
                    <tr>
                      <td><?php echo $filter['group_name']; ?></td>
                      <td><?php echo $filter['filter_name']; ?></td>
                    </tr>
                    <?php

            }
            ?>
                  </table>

                  <?php
          }
        ?>
                </div>

              </div>




            </div>
            <!-- added by kuldeep -->

            <?php if ($attribute_groups) { ?>
            <div class="tab-pane" id="tab-specification">
              <table class="table table-bordered">
                <?php foreach ($attribute_groups as $attribute_group) { ?>
                <thead>
                <tr>
                  <td colspan="2"><strong><?php echo $attribute_group['name']; ?></strong></td>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($attribute_group['attribute'] as $attribute) { ?>
                <tr>
                  <td><?php echo $attribute['name']; ?></td>
                  <td><?php echo $attribute['text']; ?></td>
                </tr>
                <?php } ?>
                </tbody>
                <?php } ?>
              </table>
            </div>
            <?php } ?>
            <?php if ($review_status) { ?>
            <div class="tab-pane" id="tab-review">
              <form class="form-horizontal" id="form-review">
                <div id="review"></div>
                <h2><?php echo $text_write; ?></h2>
                <?php if ($review_guest) { ?>
                <div class="form-group required">
                  <div class="col-sm-12">
                    <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                    <input type="text" name="name" value="" id="input-name" class="form-control" />
                  </div>
                </div>
                <div class="form-group required">
                  <div class="col-sm-12">
                    <label class="control-label" for="input-review"><?php echo $entry_review; ?></label>
                    <textarea name="text" rows="5" id="input-review" class="form-control"></textarea>
                    <div class="help-block"><?php echo $text_note; ?></div>
                  </div>
                </div>
                <div class="form-group required">
                  <div class="col-sm-12">
                    <label class="control-label"><?php echo $entry_rating; ?></label>
                    &nbsp;&nbsp;&nbsp; <?php echo $entry_bad; ?>&nbsp;
                    <input type="radio" name="rating" value="1" />
                    &nbsp;
                    <input type="radio" name="rating" value="2" />
                    &nbsp;
                    <input type="radio" name="rating" value="3" />
                    &nbsp;
                    <input type="radio" name="rating" value="4" />
                    &nbsp;
                    <input type="radio" name="rating" value="5" />
                    &nbsp;<?php echo $entry_good; ?></div>
                </div>
                <?php if ($site_key) { ?>
                <div class="form-group">
                  <div class="col-sm-12">
                    <div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>"></div>
                  </div>
                </div>
                <?php } ?>
                <div class="buttons clearfix">
                  <div class="pull-right">
                    <button type="button" id="button-review" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><?php echo $button_continue; ?></button>
                  </div>
                </div>
                <?php } else { ?>
                <?php echo $text_login; ?>
                <?php } ?>
              </form>
            </div>
            <?php } ?>
          </div>
          <br /><br />
        </div>
          <?php if ($tags) { ?>
          <p style="margin-left: 10px;"><?php echo $text_tags; ?>
            <?php for ($i = 0; $i < count($tags); $i++) { ?>
            <?php if ($i < (count($tags) - 1)) { ?>
            <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>,
            <?php } else { ?>
            <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>
            <?php } ?>
            <?php } ?>
          </p>
          <?php } ?>
          <?php //echo $content_bottom; ?></div>
        <?php //echo $column_right; ?></div>
    </div>

    <?php if ($products) { ?>
    <div class="container product_page">
      <div class="row">
        <div class="">
          <h3><?php echo $text_related; ?></h3>
          <div class="related_products">

            <div class="owl-related-nav">
              <span class="owl-related-prev"><i class="fa fa-arrow-circle-left"></i></span>
              <span class="owl-related-next"><i class="fa fa-arrow-circle-right"></i></span>
            </div>
            <div id="owl-related" class="owl-carousel">
              <?php $i = 0; ?>
              <?php foreach ($products as $product) { ?>
              <?php if ($column_left && $column_right) { ?>
              <?php $class = 'col-lg-6 col-md-6 col-sm-12 col-xs-12'; ?>
              <?php } elseif ($column_left || $column_right) { ?>
              <?php $class = 'col-lg-4 col-md-4 col-sm-6 col-xs-12'; ?>
              <?php } else { ?>
              <?php $class = 'col-lg-3 col-md-3 col-sm-6 col-xs-12'; ?>
              <?php } ?>
              <div >
                <div class="product-thumb  transition">
                  <div class="thumb-inner">
                    <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['image_medium']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a></div>
                    <?php /* ?>
                    <div class="caption">
                      <h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
                      <p><?php echo $product['description']; ?></p>
                      <?php if ($product['rating']) { ?>
                      <div class="rating">
                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                        <?php if ($product['rating'] < $i) { ?>
                        <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
                        <?php } else { ?>
                        <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
                        <?php } ?>
                        <?php } ?>
                      </div>
                      <?php } ?>

                    </div>
                    <?php */ ?>
                    <div class="caption_box">
                      <h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
                      <?php if ($product['price']) { ?>
                      <p class="price">
                        <?php if (!$product['special']) { ?>
                        <?php echo $product['price']; ?>
                        <?php } else { ?>
                        <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
                        <?php } ?>
                        <?php if ($product['tax']) { ?>
                        <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                        <?php } ?>
                      </p>
                    </div>
                    <?php } ?>
                  </div>
                  <div class="button-group">
                    <button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><span class="hidden-xs hidden-sm hidden-md"><?php echo $button_cart; ?></span> <i class="fa fa-shopping-cart"></i></button>
                    <button type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><span class="fa fa-heart fa-5x"></span></button>
                    <!-- <button type="button" data-toggle="tooltip" title="<?php //echo $button_compare; ?>" onclick="compare.add('<?php //echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button> -->
                  </div>
                </div>
              </div>
              <?php if (($column_left && $column_right) && ($i % 2 == 0)) { ?>
              <div class="clearfix visible-md visible-sm"></div>
              <?php } elseif (($column_left || $column_right) && ($i % 3 == 0)) { ?>
              <div class="clearfix visible-md"></div>
              <?php } elseif ($i % 4 == 0) { ?>
              <div class="clearfix visible-md"></div>
              <?php } ?>
              <?php $i++; ?>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>
</div>

<script type="text/javascript">
  function continueShopping(){
    window.history.back();


  }
  function viewCart(){
    parent.jQuery.fancybox.close();
    parent.location.replace('index.php?route=checkout/cart');

  }

</script>
<script type="text/javascript"><!--
  $('select[name=\'recurring_id\'], input[name="quantity"]').change(function(){
    $.ajax({
      url: 'index.php?route=product/product/getRecurringDescription',
      type: 'post',
      data: $('input[name=\'product_id\'], input[name=\'quantity\'], select[name=\'recurring_id\']'),
      dataType: 'json',
      beforeSend: function() {
        $('#recurring-description').html('');
      },
      success: function(json) {
        $('.alert, .text-danger').remove();

        if (json['success']) {
          $('#recurring-description').html(json['success']);
        }
      }
    });
  });
  //--></script>
<script type="text/javascript"><!--
  $('#button-cart').on('click', function() {
   if(getCookie("customer_mobile") == '')
        {
          $("input[name=redirect_cart]").val(1);
            $('#login_verify_popup').modal('show');
        }
    else if(getCookie("register_user") == 1 && getCookie("customer_id") == '')
       {
         $("input[name=redirect_cart]").val(1);
         $('#login_popup').modal('show');
       }
    else
    {
    $.ajax({
      url: 'index.php?route=checkout/cart/add&popup=true',
      type: 'post',
      data: $('#product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea'),
      dataType: 'json',
      beforeSend: function() {
        $('#button-cart').button('loading');
      },
      complete: function() {
        $('#button-cart').button('reset');
      },
      success: function(json) {
        $('.alert, .text-danger').remove();
        $('.form-group').removeClass('has-error');

        if (json['error']) {
          if (json['error']['option']) {
            for (i in json['error']['option']) {
              var element = $('#input-option' + i.replace('_', '-'));

              if (element.parent().hasClass('input-group')) {
                element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
              } else {
                element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
              }
            }
          }

          if (json['error']['recurring']) {
            $('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
          }

          // Highlight any found errors
          $('.text-danger').parent().addClass('has-error');
        }

        if (json['success']) {
          //$('.breadcrumb').after('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

         if($("#cart_shopping_popup").hasClass('in') == false)
         {
           $('#content').parent().before( json['success'] );
         }  

          $('#cart > button#cart_btn', window.parent.document).html('<i class="fa fa-shopping-cart"></i> ' + json['total']);
          $('#cart > button#cart_btn_mobile', window.parent.document).html('<i class="fa fa-shopping-cart"></i> <span id="cart-total">' + json['total_in_cart']+'</span>');

          //jQuery('#continue_shop').show(fade);

          $('#cart > ul').load('index.php?route=common/cart/info ul li');
        }
      }
    });
  }
  });
  //--></script>
<script type="text/javascript"><!--
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

  $('button[id^=\'button-upload\']').on('click', function() {
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
          url: 'index.php?route=tool/upload',
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
            $('.text-danger').remove();

            if (json['error']) {
              $(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
            }

            if (json['success']) {
              alert(json['success']);

              $(node).parent().find('input').attr('value', json['code']);
            }
          },
          error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
      }
    }, 500);
  });
  //--></script>
<script type="text/javascript"><!--
  $('#review').delegate('.pagination a', 'click', function(e) {
    e.preventDefault();

    $('#review').fadeOut('slow');

    $('#review').load(this.href);

    $('#review').fadeIn('slow');
  });

  $('#review').load('index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');

  $('#button-review').on('click', function() {
    $.ajax({
      url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
      type: 'post',
      dataType: 'json',
      data: $("#form-review").serialize(),
      beforeSend: function() {
        $('#button-review').button('loading');
      },
      complete: function() {
        $('#button-review').button('reset');
      },
      success: function(json) {
        $('.alert-success, .alert-danger').remove();

        if (json['error']) {
          $('#review').after('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
        }

        if (json['success']) {
          $('#review').after('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');

          $('input[name=\'name\']').val('');
          $('textarea[name=\'text\']').val('');
          $('input[name=\'rating\']:checked').prop('checked', false);
        }
      }
    });
  });

  $(document).ready(function() {

    var owl = $("#main_img_container");
    owl.owlCarousel({
      autoPlay : 3000,
      itemsMobile : [768, 1],
      pagination : true,
      autoPlay : false,
      singleItem: true
    });



  });

  <!--code added by parth for custom options -->
    <?php  if( isset($option) && is_array($option) && isset($option['product_option_id'])){ ?>
      $('select#input-option<?php echo $option['product_option_id']; ?>').change(function(){
          var jsvar = <?php echo json_encode($js_var); ?>;
          //alert(jsvar[0].length);
          for (var i = 0; i < jsvar.length; i++) {
            for (var j = 0; j < jsvar[i].length; j++) {
              console.log($('select#input-option<?php echo $option['product_option_id']; ?>').val());
              if ($('input[type="radio"]#input-option<?php echo $option['product_option_id']; ?>').val() == jsvar[i][j]) {
                if (jsvar[i][0] == "+" && jsvar[i][1] != "") {
                  var existing_price = parseFloat($('#product_unit_price').val());
                  var add_price = parseFloat(jsvar[i][1]);
                  var net_price = add_price + existing_price;
                  $('#sell-price').html("Rs. " + net_price);
                };
                if (jsvar[i][0] == "-" && jsvar[i][1] != "") {
                  var existing_price = parseFloat($('#product_unit_price').val());
                  var sub_price = parseFloat(jsvar[i][1]);
                  var net_price =  existing_price - sub_price;
                  $('#sell-price').html("Rs. " + net_price);
                };
                if (jsvar[i][1] == "" ) {
                  var existing_price = parseFloat($('#product_unit_price').val());
                  $('#sell-price').html("Rs. " + existing_price);
                };
              };
            };
          };
        });

  <?php } ?>
  //-->

  $(".close_popup").click(function(){
    window.history.back();
  });

  $(".new_inupt_qyt").click(function() {
    $('html,body').animate({
              scrollTop: $(".scroll_to").offset().top},
            'slow');
  });

  $(".new_addtocart").click(function() {
    $('html,body').animate({
              scrollTop: $(".scroll_to1").offset().top},
            'slow');
  });
</script>

<?php echo $footer; ?>
