<?php echo $header; ?>
<link href="catalog/view/theme/default/stylesheet/stylesheet_new.css" rel="stylesheet" />
<div itemscope itemtype="http://schema.org/Product">
<div class="container product_page no_popup" >
  <div class="row">
    <?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>" style="background: #eee;"><?php echo $content_top; ?>
      <section class="category_head1">
      <div class="container ">
        <div class="row">
          <ul class="breadcrumb" vocab="http://schema.org/" typeof="BreadcrumbList">
            <?php $c = 1; ?>
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
              <li property="itemListElement" typeof="ListItem">
                <a href="<?php echo $breadcrumb['href']; ?>" property="item" typeof="WebPage"><span  property="name"><?php echo $breadcrumb['text']; ?></span></a>
                <meta property="position" content="<?php echo $c; ?>">
              </li>
            >
            <?php $c++; ?>
            <?php } ?>
          </ul>
        </div>
        </ul>
        </div>
      </section>
      <div class="row">
        <div class="col-sm-12 col-xs-12" id="panzoom-container">
          <div class="col-sm-2 vetical-image-offset">
            <?php if ($thumb || $images) { ?>
            <ul class="thumbnails col-sm-2" >
              <li id="myCarousel" class="thumbs-col vertical-slider carousel vertical DownLoad_Product_Image" data-ride="carousel">
                <?php
                  if ($images) {
                    if(count($images) > 3){
                ?>
                <img src="image/product_verticel_top.png" data-slide="next" class="btn-vertical-slider glyphicon glyphicon-circle-arrow-up ">
                <?php } ?>
                <ul class="carousel-inner">
                  <?php
                       $i=0;
                         $preload_img_string = '';
                         foreach ($images as $image) {
                          if($i==0) {
                            $active  = 'active';
                          }else{
                            $active='';
                          }
                       $i++;
                       $preload_img_string .= "'".$image['popup']."',";
                       $image_preload_path_array[] = $image['popup'];
                    ?>
                  <li class="image-additional1 item <?php echo $active; ?>"><a class="thumbnail getcurrentImg" html-data="<?php echo $image['popup']; ?>" href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>"> <img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a></li>
                  <?php } ?>
                </ul>
                <?php if(count($images)>3){ ?>
                <img src="image/product_verticel_bottom.png" data-slide="prev" class="btn-vertical-slider glyphicon glyphicon-circle-arrow-down">
                <?php }?>
                <?php } ?>
              </li>
            </ul>
            <?php } ?>
          </div>
          <div class="col-sm-10 col-md-10 product_img_detail">
            <?php if ($thumb) { ?>
            <div class="col-md-4 col-xs-11 col-sm-5 main-image mainImgToDown">
              <?php
                    if($quantity <= 0 || strtolower($stock_status) == 'out of stock'){
                      $bgcolor = 'red';
                      $instock = '<span class="in_stock">'.$text_out_of_stock.'</span>';
              }else{
              $bgcolor = 'green';
              //if($is_single == '1'){
              //$instock = '<span class="in_stock">'.$text_in_stock.'</span>';
              //}else{
//              $instock = '<span class="in_stock">'.$text_in_stock.'</span>';
              //}
              //}
              //if($is_single == '1'){
              //}else{
              //?>
              <!--<div class="stock_circle <?php //echo $bgcolor; ?>"><div><?php //echo $instock; ?><span class="soldout"><?php //echo $text_sold_out;?></span></div></div>-->
              <?php }?>
              <a class="thumbnail panzoom-parent" href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>"><img itemprop="image" class="panzoom" src="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
              <!--<span class="downloadImages col-md-9 col-xs-12 col-sm-8 pull-right main-image"><?php echo '<a href="'.$downloadImages.'">'.$downloadText.'</a>'; ?></span>-->
              <span class="getImage downloadImages col-md-9 col-xs-12 col-sm-8 pull-right main-image"><?php echo '<a href="javascript:void(0);">'.$downloadText.'</a>'; ?></span>
            </div>
            <?php } ?>
            <div class="col-sm-7 col-xs-12 col-md-8 nopadding product_details_popup">
              <div class="col-sm-12">
                <div class="col-sm-11">
                  <h1 class="product_detail_popup_heading" itemprop="name"><?php echo $heading_title; ?></h1>
                </div>
                <div class="col-sm-1">
                  <?php
                  /*------ start fill heart after user login ----*/
                  $fill_heart = 'fa fa-heart-o';
                  if($this->customer->isLogged()){
                    if(isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist']) && !empty($this->session->data['wishlist'])){
                      if(in_array($product_id,$this->session->data['wishlist'])){
                        $fill_heart = 'fa fa-heart custom_heart';
                      }
                    }
                  }else{
                    if(isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist']) && !empty($this->session->data['wishlist'])){
                      if(in_array($product_id,$this->session->data['wishlist'])){
                        $fill_heart = 'fa fa-heart custom_heart';
                      }
                    }
                  }
                  /*------ END fill heart after user login ----*/
                  ?>
                  <div class="button_add_wishlist" data-product-id="<?php echo $product_id; ?>">
                    <button type="button" id="wishlist_<?php echo $product_id; ?>" data-toggle="tooltip" class="addtowishlist" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product_id; ?>');">
                      <i class="<?php echo $fill_heart; ?>" id="wishlist_heart_<?php echo $product_id; ?>"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-sm-12">
                <ul class="list-unstyled">
                  <?php if ($manufacturer) { ?>
                  <li><?php echo $text_manufacturer; ?> <a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a></li>
                  <?php } ?>
                  <li><?php echo $text_model; ?> <?php echo $model; ?> &nbsp;&nbsp;&nbsp;</li>
                  <hr/>
                  <?php

                  <!-- <li><h4><?php echo $stock; ?></li></h4> -->
                </ul>
              </div>
              <div class="col-sm-12 col-xs-12 product_detail">
                <?php if ($price) { ?>
                <div class="row">
                  <?php if (!$special) { ?>
                  <div class="col-sm-12">
                    <div class="col-sm-6">
                      <h3>
                          <span>
                        <span id ="sell-price"><?php echo $price; ?></span>
                        <span style="display: none;" itemprop="price"><?php echo $price_value; ?></span><br/>
                <?php if($quantity >= 0 || strtolower($stock_status) == 'in stock'){ ?>
                   <div style="display: none" itemprop="availability" href="http://schema.org/InStock"><?php echo "in stock"; ?></div>
                  <?php } ?>
                        </span>
                            <span><?php echo $text_withoutslash_piece; ?><?php if ($tax_class_id) { echo $text_plus_cst;} ?> </span>
                      </h3>
                      <input type= "hidden" id ="product_unit_price" value ="<?php echo floatval($unformatted_price); ?>" />
                    </div>
                    <div class="col-sm-6">
                      <?php
                            if(!empty($custom_store_selling_price)){
                               if($is_single==0){
                      ?>
                        <div class="store_information">
                          <h1 class="store_title"><a href= <?php echo $custom_store_product_href; ?>><?php echo $text_single;?></a></h1>
                          <p class="store_price"><?php echo $this->currency->format((ceil($custom_store_selling_price))); ?>
                            </p>
                          <h3 class="store_tax"><?php echo $text_withoutslash_piece; ?><?php if ($tax_class_id) { echo $text_plus_cst;} ?></h3>
                        </div>
                      <?php }else{ ?>
                        <div class="store_information">
                          <h1 class="store_title"><a href= <?php echo $custom_store_product_href; ?>><?php echo $text_wholesaleBox;?></a></h1>
                          <p class="store_price"><?php echo $this->currency->format((ceil($custom_store_selling_price))); ?></p>
                          <h3 class="store_tax"><?php echo $text_withoutslash_piece; ?><?php if ($tax_class_id) { echo $text_plus_cst;} ?></h3>
                        </div>
                      <?php } ?>
                      <?php }else{ ?>
                      <?php if ($review_status) { ?>
                      <div class="rating">
                        <p>
                          <?php for ($i = 1; $i <= 5; $i++) { ?>
                          <?php if ($rating < $i) { ?>
                          <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
                          <?php } else { ?>
                          <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
                          <?php } ?>
                          <?php } ?><br/>
                        </p>
                        <div>
                          <a href="<?php echo $_SERVER['REQUEST_URI'];?>#reviews" ><?php echo $reviews; ?></a> / <a href="<?php echo $_SERVER['REQUEST_URI'];?>#reviews" ><?php echo $single_txt_write; ?></a>
                        </div>


                        <?php /* ?> <hr>
                        <!-- AddThis Button BEGIN -->
                        <div class="addthis_toolbox addthis_default_style"><a class="addthis_button_facebook_like" fb:like:layout="button_count"></a> <a class="addthis_button_tweet"></a> <!-- <a class="addthis_button_pinterest_pinit"></a> <a class="addthis_counter addthis_pill_style"></a> --> </div>
                        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-515eeaf54693130e"></script>
                        <!-- AddThis Button END -->
                        <?php */ ?>
                      </div>
                      <?php } ?>
                      <?php }?>
                    </div>
                  </div>
                  <?php
                    //if($is_single == '1'){

                      //}
                     // else{ ?>
                  <div class="col-sm-12 product_set_description">
                    <?php if ($minimum > 1) { ?>
                    <span class="label label-minimum-order"><?php echo $text_moq_pre . $minimum . $text_moq_post; ?></span>
                    <?php } else { ?>
                    <span class="label label-minimum-order"><?php echo $text_moq_default; ?></span>
                    <?php } ?>
                    <h4>
                      <font color="#000"><?php echo $set_description; ?></font>
                    </h4>
                  </div>
                  <?php //}?>
                  <?php } else { ?>
                  <div class="col-sm-12"><span style="text-decoration: line-through;"><?php echo $price . $text_per_piece; ?></span></div>
                  <div class="col-sm-12">
                    <h2><?php echo $special . $text_per_piece; ?> <span><?php if ($tax_class_id) { echo $text_plus_cst; } ?></span></h2>
                  </div>
                  <?php } ?>
                  <?php /* if ($tax) { ?>
                  <div class="col-sm-12"><?php echo $text_tax; ?> <?php echo $tax; ?></div>
                  <?php } */ ?>
                  <?php if ($points) { ?>
                  <div class="col-sm-12"><?php echo $text_points; ?> <?php echo $points; ?></div>
                  <?php } ?>
                  <?php if ($discounts) { ?>
                  <div class="col-sm-12 qty-discount-price">
                    <div class="row">
                      <table class="table table-bordered">
                        <tr>
                          <th class="table_data_color">
                            <?php echo $entry_qty; ?>
                          </th>
                          <th class="table_data_color table_data_align">
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
                          <td align="right" style="font-weight: bold; color:#000;">
                            <?php echo $discount['price']; ?>
                          </td>
                        </tr>
                        <?php
                }
            ?>
                      </table>
                    </div>
                  </div>
                  <?php } ?>
                </div>
                <?php } ?>
              </div>
              <div class="col-sm-12 col-xs-12">
                <div id="product" class="product_detail_cart">
                  <?php if ($options) { ?>
                  <hr>
                  <h3><?php echo $text_option; ?></h3>
                  <?php $js_var='';foreach ($options as $option) { ?>
                  <?php // echo '<pre>'; print_r($option); ?>
                  <?php if ($option['type'] == 'select') { ?>
                  <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                    <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
                    <?php /* ?><!--<select name="option[<?php echo $option['product_option_id']; ?>]" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control">
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
                    </select>-->
                    <?php */ ?>
                    <ul id="input-option<?php echo $option['product_option_id']; ?>" class="col-sm-12 singleproductsize form-control" name="option[<?php echo $option['product_option_id'];?>]">
                      <?php foreach ($option['product_option_value'] as $option_value) { ?>
                      <li data-name ="option[<?php echo $option['product_option_id']; ?>]" data-value="<?php echo $option_value['product_option_value_id']; ?>" id="input-option-radio<?php echo $option_value['product_option_value_id']; ?>" class="singleproductsizelist">
                        <div class="sizeChecked">
                          <input type="radio" data-value-selected="<?php echo $option_value['product_option_value_id']; ?>" name="option[<?php echo $option['product_option_id']; ?>]" id="input-option-radio-section<?php echo $option_value['product_option_value_id']; ?>" class="sizeSelected"  value="<?php echo $option_value['product_option_value_id']; ?>" />
                        </div>
                        <?php echo $option_value['name']; ?>
                        <?php if ($option_value['price']) { ?>
                        (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                        <?php } ?>
                        <?php $js_var[] = array( $option_value['price_prefix'], filter_var($option_value['unfor_option_price'], FILTER_SANITIZE_NUMBER_FLOAT), $option_value['product_option_value_id'] );?>

                        <?php //; $x = trim($option_value['price']) ; $price_prefix .= "<input type = hidden
                #id = price_prefix_$option_value[product_option_value_id]
                #value = $option_value[price_prefix] /><input type = 'hidden'
                                                              #id = option_value_$option_value[product_option_value_id]
                                                              #value = $x />" ; ?>
                      </li>
                      <?php } ?>
                    </ul>
                    <?php //echo "<pre>"; print_r($js_var);?>
                    <?php // echo $price_prefix; ?>
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
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-12 col-sm-12 col-xs-12 qtybox nopadding">
                          <!--<label class="control-label  " for="input-quantity"><?php //echo $entry_qty; ?></label> <br />-->
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
                                 class="form-control  pull-left touchspin_qty"
                          />
                          <div class="btn_addtocart">
                            <button type="button" <?php if ($quantity <= 0 || strtolower($stock_status) == 'out of stock' || $status == 0) { echo "disabled"; } ?> id="button-cart" data-loading-text="<?php echo $text_loading; ?>" class="addtocart btn btn-lg " style="margin-right: 5px"><i class="fa fa-shopping-cart"></i> <?php echo $button_cart; ?></button>
                          </div>
                          <?php if ($quantity <= 0 || strtolower($stock_status) == 'out of stock' || $status == 0) { ?>
                            <div class="out-of-stock">Out Of Stock!!!</div>
                          <?php } ?>
                          <script>
                            $("input[name='quantity']").TouchSpin({ });
                          </script>
                          <!--<input type="text" name="quantity" value="<?php echo $minimum; ?>" size="2" id="input-quantity" class="form-control  pull-left" />-->
                          <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
                      </div>
                    </div>
                  </div>

                </div>
              </div>
              <?php /* ?>
              <!--<div class="col-sm-12 money_back_guarantee">
                <h4>48 Hours return 100% money back guarantee </h4>
              </div> -->
              <?php */ ?>
              <?php
                    if(isset($seller_returnable) && $seller_returnable != 0){
                      if($is_single == 1){
              ?>
                        <div class="col-sm-12 return_accepted">
                          <h4><?php echo $text_no_return; ?></h4>
                        </div>
              <?php
                      }else{
               ?>
                        <div class="col-sm-12 return_accepted">
                          <h4><?php echo $text_no_return; ?></h4>
                        </div>
              <?php   }
                    }else{
                        if($is_single == 1){
              ?>
                          <div class="col-sm-12 return_accepted">
                            <h4><?php echo $text_no_return; ?></h4>
                          </div>
              <?php   }
                   }
              ?>
            </div>
            </div>
          </div>
        </div>

      <div class="row set_show_description">
        <div class="col-sm-12">
          <div class="col-sm-9">
            <div class="row">
              <div class="col-sm-12">
                <h4 class="active" ><a href="#tab-description" data-toggle="tab" class="heading_title"><?php echo $tab_description; ?></a></h4>
                <div class="show_description">
                  <div class="row">
                    <div class="col-md-12" style="margin-bottom:25px;">
                      <p class="set_descriptioin">
                        <?php echo $set_description; ?>
                      </p>
                      <?php echo $description; ?>
                    </div>
                    <div class="col-md-12">
                      <!-- added by kuldeep -->
                      <?php
                        if(isset($filters) && count($filters) > 0){
                      ?>
                      <table class="table table-bordered ">
                        <?php
                            foreach($filters as $filter){
                          ?>
                        <tr class="table_data_color table_data_weight">
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
                    <div class="col-sm-12">
                      <?php if ($tags) { ?>
                      <p><?php echo $text_tags; ?>
                        <?php for ($i = 0; $i < count($tags); $i++) { ?>
                        <?php if ($i < (count($tags) - 1)) { ?>
                        <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>,
                        <?php } else { ?>
                        <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>
                        <?php } ?>
                        <?php } ?>
                      </p>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php if ($attribute_groups) { ?>
                <div class="col-sm-12">
                  <h4><a href="#tab-specification" data-toggle="tab" class="heading_title"><?php echo $tab_attribute; ?></a></h4>
                </div>
              <?php } ?>
              <?php if ($review_status) { ?>
                <a id="reviews"></a>
                <div class="col-sm-12" >
                  <h4><a href="#tab-review" data-toggle="tab" class="heading_title"><?php echo $tab_review; ?></a></h4>
                  <?php if ($review_status) { ?>
                    <div class="show_reviews" id="show_review">
                    <form class="form-horizontal" id="form-review">
                      <div id="review"></div>
                      <!--<p><?php echo $text_write; ?></p>-->
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
                      <p><?php echo $text_write; ?></p>
                      <?php } ?>
                    </form>
                  </div>
                  <?php } ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="col-sm-3 recently_viewed">
            <?php
                if(!empty($viewed)){
              ?>
                  <h4 class="active" >
                    <a href="javascript:void(0);" data-toggle="tab" class="heading_title">
                      <?php echo $text_recently_viewed; ?>
                    </a>
                  </h4>
                  <?php if(count($products)>2){ ?>
                    <div class="row viewed-top-button">
                      <img src="image/recently-viewed-top.png" data-slide="next" class="btn-vertical-slider-viewed glyphicon glyphicon-circle-arrow-up ">
                    </div>
                          <?php echo $viewed; ?>
                    <div class="row viewed-bottom-button">
                      <img src="image/recently-viewed-bottom.png" data-slide="prev" class="btn-vertical-slider-viewed glyphicon glyphicon-circle-arrow-up ">
                    </div>
                  <?php } ?>

              <?php
                  }else{
                ?>
            <h4 class="active" >
              <a href="javascript:void(0);" data-toggle="tab" class="heading_title">
                <?php echo $text_featured; ?>
              </a>
            </h4>
            <?php if($products){
                    $i=1;
                    foreach($products as $product){
                    if($i<=2){
                  ?>
            <div class="product-thumb  transition">
              <div class="thumb-inner">
                <div class="image">
                  <a href="<?php echo $product['href']; ?>">
                    <img src="<?php echo $product['image_medium']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" />
                  </a>
                </div>
                <div class="caption_box">
                  <h4><a href="<?php echo $product['href']; ?>"><?php echo mb_strimwidth($product['name'], 0, 30, "..."); ?><?php //echo $product['name']; ?></a></h4>
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
                  <?php } ?>
                </div>
              </div>
            </div>
            <?php $i++;
                        }
                       }
                      }
                  ?>
            <?php
                }
              ?>
          </div>
        </div>
      </div>
    <?php echo $column_right; ?></div>
</div>
</div>
<?php if ($products) { ?>
<div class="container product_page" style="width:100%">
  <div class="row">
    <div class="col-sm-12 bottom_releted_product">
      <h4><a href="javascript:void(0);" class="heading_title"><?php echo $text_related; ?></a> </h4>
      <div class="related_products">
        <?php  ?>
        <div class="owl-related-nav">
          <span class="owl-related-prev"><i class="fa fa-arrow-circle-left"></i></span>
          <span class="owl-related-next"><i class="fa fa-arrow-circle-right"></i></span>
        </div>
        <?php  ?>
        <div id="owl-related" class="owl-carousel ">
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
            <div class="product-thumb  transition ">
              <div class="thumb-inner ">
                <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['image_medium']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a></div>
                <?php /* ?>
                <div class="caption">
                  <h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
                  <p><?php echo $product['description']; ?></p>
                  <?php if ($product['rating']) { ?>
                  <div class="rating">
                    <?php for ($i = 1; $i <=5; $i++) { ?>
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
                    <?php } ?>
                </div>
                <?php
                      /*------ start fill heart after user login ----*/
                      $fill_heart = 'fa fa-heart-o';
                      if($this->customer->isLogged()){
                        if(isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist']) && !empty($this->session->data['wishlist'])){
                          if(in_array($product['product_id'],$this->session->data['wishlist'])){
                            $fill_heart = 'fa fa-heart custom_heart';
                          }
                        }
                      }else{
                        if(isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist']) && !empty($this->session->data['wishlist'])){
                          if(in_array($product['product_id'],$this->session->data['wishlist'])){
                            $fill_heart = 'fa fa-heart custom_heart';
                          }
                        }
                      }
                      /*------ END fill heart after user login ----*/
                      ?>
                <div class="button_add_wishlist releted_product_wishlist" data-product-id="<?php echo $product['product_id']; ?>">
                  <button type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');" class="addtowishlist" id="wishlist_<?php echo $product['product_id']; ?>"   ><i class=" <?php echo $fill_heart;?>" id="wishlist_heart_<?php echo $product['product_id']; ?>"></i></button>
                </div>
              </div>
              <div class="button-group">
                <button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><!-- <span class="hidden-xs hidden-sm hidden-md"><?php //echo $button_cart; ?></span> --> <i class="fa fa-shopping-cart"> <?php echo $button_cart;?></i></button>
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

<?php } ?>
</div>
<script type="text/javascript">
  function continueShopping(){
     parent.jQuery.fancybox.close();
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

          $('#cart > button#cart_btn', window.parent.document).html('<i class="fa fa-shopping-cart"></i><span id="cart-total-desktop"> Cart <span class="cart_number">' + json['total_in_cart'] + '</span></span>');
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
<script type="text/javascript">
  $(document).ready(function(){
    $('.singleproductsizelist').click(function(){
      $('ul li').removeClass('sizeBackColor');
      var name = $(this).attr("data-name");
      var SelectdValue = $(this).attr("data-value");
      $('input[name="' + name+ '"][value="' + SelectdValue + '"]').prop('checked', true);
      $(this).addClass('sizeBackColor');
    });
  });
</script>
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
      success: function(json) { alert(1);
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
      //Swap image
    $('a.thumbnail').each(function() {
    //$(document).each('a.thumbnail',function() {
     $(this).click(function(e) {
       e.preventDefault();
        var imgSrc = $(this).attr('href');
        //alert(imgSrc);
        $('img.panzoom').fadeOut('fast',function(){
          $(this).attr('src', imgSrc).fadeIn('fast');
        });

      });
    });
    (function() {
      var $section = $('#panzoom-container');

      $section.find('.panzoom').panzoom({
        $zoomIn: $section.find(".zoom-in"),
        $zoomOut: $section.find(".zoom-out"),
        $zoomRange: $section.find(".zoom-range"),
        minScale: 1,
        startTransform: 'scale(1)',
        disablePan: false,
        $reset: $section.find(".reset")
      }).panzoom();
    })();
  });
  <?php  if( isset($option) && is_array($option) && isset($option['product_option_id'])){ ?>
  /*$('select#input-option<?php echo $option['product_option_id']; ?>').change(function(){
    var jsvar = <?php echo json_encode($js_var); ?>;
    // alert(jsvar[0].length);
    for (var i = 0; i < jsvar.length; i++) {
      for (var j = 0; j < jsvar[i].length; j++) {

        if ($('select#input-option<?php echo $option['product_option_id']; ?>').val() == jsvar[i][j]) {
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
            $('#sell-price').html("Rs. " + existing_price + ".00");
          };
        };
      };
    };
  });*/
    $('input[type="radio"]#input-option<?php echo $option['product_option_id']; ?>').change(function(){
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
$(document).ready(function(){
   var links = [];
  $(".getImage").click(function(){
      var links = [];
      $(".download-inner a ").each(function(){
        //alert($(this).attr("href"));
        if($(this).attr("href") != ""){
          links.push( $(this).attr("href") );
        }
      });
    downloadAll(links);
  });
  function downloadAll(urls) {
      var link = document.createElement('a');

      link.setAttribute('download', link);
      link.style.display = 'none';

      document.body.appendChild(link);

      for (var i = 0; i < urls.length; i++) {
        link.setAttribute('href', urls[i]);
        link.click();
      }

      document.body.removeChild(link);
    }
  });

  $(document).ready(function () {
    $('#myCarousel').carousel({
      pause: true,
      interval: false
    });
    $('.btn-vertical-slider').on('click', function () {

      if ($(this).attr('data-slide') == 'next') {
        $('#myCarousel').carousel('next');
      }
      if ($(this).attr('data-slide') == 'prev') {
        $('#myCarousel').carousel('prev');
      }
    });

//    $('.carousel .item').each(function() {
//      var item = $(this);
//      item.siblings().each(function(index) {
//        if (index < 4) {
//          $(this).children(':first-child').clone().appendTo(item);
//        }
//      });
//    });

var count_image = '<?php echo count($images);?>';
    if(count_image > 3) {
      $('.carousel .item').each(function () {
        var next = $(this).next();
        if (!next.length) {
          next = $(this).siblings(':first');
        }
        next.children(':first-child').clone().appendTo($(this));

        for (var i = 0; i < 3; i++) {
          next = next.next();
          if (!next.length) {
            next = $(this).siblings(':first');
          }
          next.children(':first-child').clone().appendTo($(this));
        }
      });
    }else{
      $('.carousel .item').each(function() {
        var next = $(this).next();
        if (!next.length) {
          next = $(this).siblings(':first');
        }
        next.children(':first-child').clone().appendTo($(this));

        for (var i = 0; i <1; i++) {
          next = next.next();
          if (!next.length) {
            next = $(this).siblings(':first');
          }
          next.children(':first-child').clone().appendTo($(this));
        }
      });
    }

  });

</script>

<!-- panzoom  by garvit/vikas (08-01-2015) -->
<script type="text/javascript">  <!--
  $(document).ready(function() {
// script for  desktop
    $('.thumbnail').click(function(e) {
    //$(document).click('.thumbnail',function(e) {
      e.preventDefault();
      var imgSrc = $(this).attr('href'); //alert(imgSrc);
      $('img.panzoom').fadeOut('fast',function(){
        $(this).attr('src', imgSrc).fadeIn('fast');
        var path = "url("+imgSrc+")";
        $('.zoomWindowContainer div').css("background-image",path);
      });
    });

    //image zoom
    $('.panzoom').elevateZoom({
      cursor: 'crosshair',
      //  scrollZoom : true,
      borderSize : 0,
      responsive : true,
      zoomWindowWidth   : 676,
      zoomWindowHeight  : 500,
      zoomWindowOffetx  : 10,
      zoomWindowOffety  : -1


    });
    //fancy-box Image
    $('.panzoom').click(function(e) {
      var image = $(this).attr('src');
      $.fancybox(image).attr('rel', 'gallery').fancybox({});
      return false;
    });
  });
  //-->

  $('.button_add_wishlist').click(function(){
    $('#wishlist_heart_'+$(this).attr('data-product-id')).removeClass( "fa fa-heart-o" ).addClass( "fa fa-heart custom_heart" );
  });

  $(document).ready(function(){
    $(".btn-vertical-slider").mouseover(function(){
      $(".btn-vertical-slider").css("opacity","1");
    }).mouseleave(function(){
      $(".btn-vertical-slider").css("opacity","0.7");
    });
  });
</script>
<script type="text/javascript">
  // preload for product thumb
  $.preload(
    <?php echo rtrim($preload_img_string,','); ?>
  );
</script>
<div class="download-inner" style="display: none;">
  <?php if ($images) { ?>
  <?php $i=0; foreach ($images as $image) { ?>
        <a class="thumbnail" href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>" download> <img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
  <?php } ?>
  <?php } ?>
</div>
</script>
<?php echo $footer; ?>
