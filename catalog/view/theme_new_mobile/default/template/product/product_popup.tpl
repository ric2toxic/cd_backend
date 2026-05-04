<?php //echo $header; ?>
<div id="product" class="color_white">
  <div class="container-fluid" id="content">
    <div class="wishlist_top" id="color_red">
    </div>
    <button type="button" data-toggle="tooltip" data-wishlist-value = "<?php echo $product_id; ?>" class="addtowishlist addtowishlist1" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product_id; ?>'); heart_effect()"><i class="<?php echo $fill_heart ; ?> size_heart" id="wishlist_heart_<?php echo $product_id?>"></i></button>

    <!-- Product Images -->

    <div class="row whitebox">
      <?php if (!empty($percent_discount)) { ?>
      <div class="discount-arrow"><?php echo $percent_discount; ?>% Discount</div>
      <?php } ?>
       <?php echo $landscape_photos; ?>
    </div>

    <div class="row whitebox">            
      <span class="getImage downloadImages1 col-sm-12 pull-right main-image"><?php echo '<a href="javascript:void(0);">'.$downloadText.' <i class="fa fa-download" aria-hidden="true"></i> </a>'; ?></span>
    </div>
    <!-- End Product Images -->
    <!-- Name and prices -->
    <div class="row whitebox">
      <div class="col-sm-12 product_title">
        <h2><?php echo $heading_title; ?></h2>

        <span><?php echo $text_model; ?> <?php echo $model; ?></span><br />
        <span>HSN: <?php echo $hsn_code; ?></span>
        <?php if($rating > 0) { ?>
        <span>
          <div class="rating">
            <p>
              <?php /* for ($i = 1; $i <= 5; $i++) { ?>
              <?php if ($rating < $i) { ?>
              <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
              <?php } else { ?>
              <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
              <?php } ?>
              <?php } */ ?>
              
               <?php 
                    if ($rating == 1 || $rating == 2 || $rating == 3) { 
                        $rating_class = 'danger';
                        $product_rating = 'Average';
                    }elseif($rating == 4){
                        $rating_class = 'warning';
                        $product_rating = 'Good';
                    }elseif($rating == 5){
                        $rating_class = 'success';
                        $product_rating = 'Excellent';
                    }else{
                        $rating_class = '';
                        $product_rating = '';
                    }
                ?>
                <span class="label rating_span label-<?php echo $rating_class ?>"><?php echo $product_rating ?> Quality</span>

            </p>
          </div>
          <div class="clearfix"></div>
        </span>
        <?php } ?>
        <?php if($previously_ordered == 1){ ?>
          <span class="previously_ordered"><?php echo '( '. $text_previously_ordered . ' )'; ?></span> <br />
        <?php } ?>
        <?php /* Display MOQ for wholesale set store items */ ?>
        <?php if( $is_single != 1 ){
                            if( $minimum > 1 ){
        ?>
        <span class="label label-primary"><?php echo $text_moq_pre . $minimum . $text_moq_post; ?></span>

        <?php } else { ?>

        <span class="label label-primary"><?php echo $text_moq_default; ?></span>

        <?php } ?>

        <?php } ?>
        <?php if(isset($exp_final_date) && $exp_final_date > 1) { ?>
          <div class="exp_dispatch_date offer-block"><i aria-hidden="true" class="fa fa-tag"></i> <?php echo $text_available_after; ?> <?php echo $exp_final_date; ?> <?php echo $text_days; ?></div>
        <?php } ?>
        <?php /* End Display MOQ for wholesale set store items */ ?>

        <!--Display Set description -->
        <?php if( $is_single != 1 ){ ?>
        <h4><?php echo $set_description; ?></h4>
        <?php } ?>
        <!-- end of set description -->

        <!-- Price of the item -->
        <ul class="list-unstyled    ">
          <?php if (!$special) { ?>
          <li>
            <h3><span id ="sell-price"><?php echo $price ?></span><?php echo  $text_per_piece; ?> <span><?php if ($tax_class_id and $text_tax_rate) { echo $text_tax_rate;} ?> </span></h3>
            <input type= "hidden" id ="product_unit_price" value ="<?php echo filter_var($price, FILTER_SANITIZE_NUMBER_INT); ?>" />
          </li>
          <?php } else { ?>
          <li><span style="text-decoration: line-through;"><?php echo $price . $text_per_piece; ?></span></li>
          <li>
            <h2><?php echo $special . $text_per_piece; ?> <span><?php if ($tax_class_id and $text_tax_rate) { echo $text_tax_rate; } ?></span></h2>
          </li>
          <?php } ?>
          <?php /* if ($tax) { ?>
          <li><?php echo $text_tax; ?> <?php echo $tax; ?></li>
          <?php } */ ?>
        </ul>
        <!-- end price of the item -->
        <!-- show MRP -->
        <?php if(isset($mrp) && $mrp > 0){ ?>
        <div class="saving_money">
          <table class="table table-bordered">
            <tr>
              <td> <?php echo $column_mrp; ?> </td>
              <td> <?php echo $column_our_price; ?> </td>
              <td> <?php echo $column_save_money; ?> </td>
            </tr>
            <tr>
              <td><?php echo $format_mrp; ?></td>
              <td><?php echo $selling_price; ?></td>
              <td><?php echo $saving_money; ?></td>
            </tr>
          </table>
        </div>
        <?php } ?>
        <!--offer-->
        <div class="offer-block">
            <i class="fa fa-tag" aria-hidden="true"></i>
            2% discount on all prepaid order.
        </div>
        
        
        <?php if(isset($cod_available)&& ($cod_available == 0)){ ?>
          <h4><span class="not_available_cod"><?php echo $text_cod_available; ?></span></h4>
        <?php } ?>
      </div>
    </div>
    <!-- End name and prices -->
    <!-- Show alternate product if available -->
    <?php
      if(!empty($custom_store_selling_price)){
      ?>
        <div class="row whitebox">
          <div class="col-sm-12">
            <?php

                       if($is_single== 0){

                    ?>
            <h2><a href = "<?php echo $custom_store_product_href; ?>"><?php echo $text_single;?></a></h2>

            <?php
                       }else{ ?>
            <h2><a href="<?php echo $custom_store_product_href; ?>"><?php echo $text_wholesaleBox;?></a></h2>

            <?php
                      } ?>
            <table cellpadding="0" cellspacing="10" border="0" width="100%">
              <tr>
                <td>
                  <div class="store_information">

                    <h3 class="store_price"><?php echo $this->currency->format((ceil($custom_store_selling_price))); ?><?php echo $text_per_piece; ?></h3>
                    <h5 class="store_tax"><?php if ($tax_class_id and $text_custom_tax_rate) { echo $text_custom_tax_rate;} ?></h5>
                  </div>
                </td>
                <td class="view_alternate_product">
                 <?php /* ?> <a href="<?php echo $custom_store_product_href; ?>" >View More</a><?php */ ?>
                </td>
              </tr>

            </table>

          </div>
        </div>
  <?php
    } ?>
    <!-- End Show alternate product if available -->

    <!-- Discount table -->
    <?php
                if ($discounts) { ?>
    <div class="row whitebox">
      <div class="col-sm-12">
        <h2>Discount</h2>

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
    </div>
    <?php
                } ?>
    <!-- End Discount table -->
    <!-- Options of product -->
    <?php 
    if(isset($options_tpl)){
      echo $options_tpl;
    }
    ?>
    <!-- End options of product -->

    <!-- Tab Ask a question -->

    <div class="row whitebox">
      <div class="col-sm-12 ask-a-question">Ask A Question</div>
      <div class="question_popup col-sm-12" id="question_popup">
        <div class="content_q_popup ">
          <div class="popup-q-header msg_q_body">
            <button type="button" class="close" data-product-id="<?php echo $product_id; ?>">&times;</button>
            <h4 class="popup-title"><?php echo $question_popup_heading;?></h4>
          </div>
          <div class="popup-q-body">
            <?php if(isset($logged_in)){ ?>
            <input type="hidden" name="customer_name_login" value="<?php echo $customer_name; ?>" class="customer_q_name_login">
            <?php }else{ ?>
            <div class="form-group col-sm-12">
              <label class="col-sm-2 control-label" for="input-fax"><?php echo "Name"; ?></label>
              <div class="col-sm-10">
                <input type="text" name="customer_name" value="" class="customer_q_name">
              </div>
            </div>

            <?php } ?>
            <?php if(isset($logged_in)){ ?>
            <input type="hidden" name="customer_telephone_login" value="<?php echo $telephone; ?>" class="customer_q_telephone_login">
            <input type="hidden" name="customer_email_login" value="<?php echo $email; ?>" class="customer_q_email_login">
\
            <?php }else{ ?>
            <div class="form-group col-sm-12">
              <label class="col-sm-2 control-label" for="input-fax"><?php echo "Mobile"; ?></label>
              <div class="col-sm-10">
                <input type="text" name="customer_telephone" value="" class="customer_q_telephone">
              </div>
            </div>
            <div class="form-group col-sm-12">
              <label class="col-sm-2 control-label"><?php echo "Email"; ?></label>
              <div class="col-sm-10">
                <input type="text" name="customer_email" value="" class="customer_q_email">
              </div>
            </div>
            <?php } ?>
            <!--<input type="hidden" name="user_id" value="<?php echo $customer_id; ?>" class="user_id_<?php echo $product['product_id'];?>">-->
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" class="product_q_id_<?php echo $product_id;?>">
            <div class="form-group col-sm-12">
              <label class="col-sm-2 control-label" for="input-fax"><?php echo "Your Question"; ?></label>
              <div class="col-sm-10">
                <textarea name="popup_q_question" class="popup_q_question_<?php echo $product_id;?>"></textarea>
              </div>
            </div>

          </div>
          <div class="popup-q-footer ">
            <button type="button" class="btn btn-default custom-btn-default question-btn" data-product-id="<?php echo $product_id;?>"><?php echo $comment_popup_send;?></button>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab description -->
    <div class="row whitebox">
      <div class="col-sm-12">

        <div class="description">
          <h2><?php echo $tab_description; ?></h2>


          <div class="row">
            <div class="col-md-6">
              <p class="set_descriptioin">
                <?php echo $set_description; ?>
              </p>
              <?php echo $description; ?>
              </br></br>

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

        </div>

      </div>
    </div>
    <!-- end tab description -->
    <!-- Review status -->
    <?php if ($review_status) { ?>
    <div class="row whitebox">
      <div class="col-sm-12">

        <div class="rating"> 
          <p>
            <?php /* for ($i = 1; $i <= 5; $i++) { ?>
            <?php if ($rating < $i) { ?>
            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
            <?php } else { ?>
            <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
            <?php } ?>
            <?php } */?>
            
            <?php 
                if ($rating == 1 || $rating == 2 || $rating == 3) { 
                    $rating_class = 'danger';
                    $product_rating = 'Average';
                }elseif($rating == 4){
                    $rating_class = 'warning';
                    $product_rating = 'Good';
                }elseif($rating == 5){
                    $rating_class = 'success';
                    $product_rating = 'Excellent';
                }else{
                    $rating_class = '';
                    $product_rating = '';
                }
            ?>
            <span class="label rating_span label-<?php echo $rating_class ?>"><?php echo $product_rating ?> Quality</span>
            
            
            <a href="" onclick="$('a[href=\'#tab-review\']').trigger('click'); return false;"><?php echo $reviews; ?></a> / <a href="" onclick="$('a[href=\'#tab-review\']').trigger('click'); return false;"><?php echo $text_write; ?></a></p>

        </div>
        <div class="review_detail">

          <?php if ($review_status) { ?>

          <form class="form-horizontal" id="form-review">
            <div id="review"></div>

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

          <?php } ?>
        </div>

      </div>
    </div>
    <?php } ?>
    <!-- End Review Status -->

    <!-- Tags -->
    <?php if ($tags) { ?>
    <div class="row whitebox">
      <div class="col-sm-12">

        <p><?php echo $text_tags; ?>
          <?php for ($i = 0; $i < count($tags); $i++) { ?>
          <?php if ($i < (count($tags) - 1)) { ?>
          <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>,
          <?php } else { ?>
          <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>
          <?php } ?>
          <?php } ?>
        </p>

      </div>
    </div>
    <?php } ?>
    <!-- End Tags -->

    <!-- Related Products -->
    <div id="relatedProducts">
      <div class="row whitebox">
        <div class="col-sm-12 col-xs-12"  style="text-align:center;">
              <i class="fa fa fa-refresh fa-spin" style="font-size:24px;"></i>
        </div>
      </div>
    </div>
    <!-- End Related Products -->

    <!-- add to cart -->
    <div class="row">



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
        <?php
            if ($quantity <= 0 || strtolower($stock_status) == 'out of stock' || $status == 0){ ?>
              <button type="button" id="button-cart-design" data-loading-text="<?php echo $text_loading; ?>" class="new_addtocart comment_btn"><?php echo $i_want_this_design; ?></button>
        <?php }else{ ?>
              <button   data-popupgaid="<?php echo $cart_tracking_id_for_ga; ?>" type="button" id="button-cart" data-loading-text="<?php echo $text_loading; ?>" class="new_addtocart"><?php echo $button_cart; ?></button>
        <?php } ?>
        <!--<button type="button" <?php if ($quantity <= 0 || strtolower($stock_status) == 'out of stock' || $status == 0) { echo "disabled"; } ?> id="button-cart" data-loading-text="<?php echo $text_loading; ?>" class="new_addtocart"><?php echo $button_cart; ?></button>-->
      </div>


    </div>
    <!-- end add to cart -->

  </div><!-- container-fluid -->
  <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
  <?php if (isset($product['quantity']) && $product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { ?>
  <!-- Start Comment Popup -->
  <div class="comment_popup " id="comment_popup">
    <div class="content_popup ">
      <div class="popup-header msg_body">
        <button type="button" class="close" data-product-id="<?php echo $product['product_id'];?>">&times;</button>
        <h4 class="popup-title">
         <?php echo $comment_popup_heading; ?>
        </h4>
      </div>
      <div class="popup-body">
        <!--<input type="hidden" name="user_id" value="<?php echo $customer_id; ?>" class="user_id_<?php echo $product['product_id'];?>">-->
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" class="product_id_<?php echo $product['product_id'];?>">
        <input type="hidden" name="product_status" value="<?php echo 'out of stock'; ?>" class="product_status_<?php echo $product['product_id'];?>">
        <textarea name="popup_comment" class="popup_comment_<?php echo $product['product_id'];?>"></textarea>
      </div>
      <div class="popup-footer ">
        <button type="button" class="btn btn-default custom-btn-default" data-product-id="<?php echo $product['product_id'];?>"><?php echo $comment_popup_send;?></button>
      </div>
    </div>
  </div>
  <!-- End Comment Popup -->
  <?php } ?>
</div>


<script type="text/javascript">
  function continueShopping(){
   // window.history.back();
    parent.jQuery.fancybox.close();
  }
  function viewCart(){
    parent.jQuery.fancybox.close();
    parent.location.replace('index.php?route=checkout/cart');

  }
  function heart_effect() {
    $('#color_red').fadeIn();
    setTimeout(function(){
      $('#color_red').fadeOut();
    }, 0);
  }

  var productRelatedJson;
  var w;
  function startWorker()
  {
      if(typeof(Worker)!=="undefined")            //check whether the user's browser supports it
      {
          w=new Worker("catalog/view/javascript/related_products_workers.js");          //creates a new web worker object and runs the code in "demo_workers.js"
          //Add an "onmessage" event listener to the web worker
          //When the web worker posts a message, the code within the event listener is executed. The data from the web worker
          //is stored in event.data.
          var getProductUrl = '<?php echo $this->url->link("product/product/getRelatedProducts&popup=true&product_id=$product_id", "", "SSL")?>';
          w.postMessage({ "args": getProductUrl });
          w.onmessage = function (event) {
              document.getElementById("relatedProducts").innerHTML=event.data;
              stopWorker();

              var owl_related = $("#owl-related");
              owl_related.owlCarousel({
                  autoPlay : false,
                  itemsMobile: [479, 1],
                  lazyLoad : true,
                  pagination: true,
                  singleItem:true
              });

              // Custom Navigation Events
              $(".owl-related-next").click(function(){
                  owl_related.trigger('owl.next');
              });
              $(".owl-related-prev").click(function(){
                  owl_related.trigger('owl.prev');
              });

              var getStringObj = $("#productRelatedJson").attr('value');
              console.log(getStringObj);
              productRelatedJson = $.parseJSON(getStringObj);

          };
      }
      else
      {
          document.getElementById("relatedProducts").innerHTML=""; //Sorry, your browser does not support Web Workers...
      }

  }
  function stopWorker()
  {
      w.terminate();         //terminate a web worker, and free browser/computer resources
  }
  startWorker();

  $(document).ready(function() {

    $("input[name='quantity']").TouchSpin({});

    //add to cart
    //add to cart
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
           $('#product').after( json['success'] );
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
    //end add to cart

    //Review
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
    //end Review

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

    $(".close_popup").click(function(){
      window.history.back();
    });

    $(".new_inupt_qyt").click(function() {
      $('html,body').animate({
                scrollTop: $(".scroll_to").offset().top},
              'slow');
    });

    $(".new_addtocart").click(function() {
      if($('h2').hasClass('scroll_to_options')) {
        $('html,body').animate({
                  scrollTop: $(".scroll_to_options").offset().top
                },
                'slow');
      }
    });

    $("a.popup_image").click(function (e){
          e.preventDefault();
          href="index.php?route=product/product/zoomImage&image="+$(this).attr('href');
          //href = $(this).attr('href');
         zoomWindow = window.open(href, "_blank", "width=300, height=400");
    });

  }); //Domready end
</script>
<script>
  $('.comment_btn').click(function(){
    <?php if($this->customer->isLogged()){ ?>
      $('.content_popup .popup-body,.content_popup .popup-footer ').show();
      $('.popup-body textarea').val('');
      $('.alert-success').hide();
      $('.comment_popup').show();
      $(window).scrollTop(0);
    <?php }else{ ?>
      parent.jQuery.fancybox.close();
      parent.location.replace('index.php?route=account/login');
    <?php } ?>
  });
  $('.popup-header button.close').click(function(){
    $('.comment_popup').hide();
  });

  $('.popup-footer .btn-default').click(function(){
    //var user_id = $('.user_id_'+$(this).attr('data-product-id')).val();
    var product_id = $('.product_id_'+$(this).attr('data-product-id')).val();
    var product_status = $('.product_status_'+$(this).attr('data-product-id')).val();
    var popup_comment = $('.popup_comment_'+$(this).attr('data-product-id')).val();

    $.ajax({
      type : "POST",
      url  : 'index.php?route=product/search/user_comment',
      data : {product_id:product_id,product_status:product_status,popup_comment:popup_comment},
      beforeSend: function() {
        $('.popup-footer .btn-default').button('loading');
      },
      complete: function() {
        $('.popup-footer .btn-default').button('reset');
      },
      success: function(data){
        $('.content_popup .popup-body,.content_popup .popup-footer ').hide();
        $('.msg_body').after('<div class="alert alert-success"><i class="fa fa-check-circle"></i>'+data['message']+'</div>');
      }
    });
  });
</script>

<script>
  $('.ask-a-question').click(function(){
    // alert("aaya");
    //alert("andar aaya");
    $('.content_q_popup .popup-q-body,.content_q_popup .popup-q-footer ').show();
    $('.popup-q-body textarea').val('');
    $('.alert-success').hide();
    $('.question_popup').show();
  });
  $('.popup-q-header button.close').click(function(){
    $('.question_popup').hide();
  });
  <?php if($this->customer->isLogged()){ ?>
    $('.popup-q-footer .question-btn').click(function(){
      //var user_id = $('.user_id_'+$(this).attr('data-product-id')).val();
      var product_id = $('.product_q_id_'+$(this).attr('data-product-id')).val();
      var customer_name = $('.customer_q_name_login').val();
      var telephone = $('.customer_q_telephone_login').val();
      var popup_question = $('.popup_q_question_'+$(this).attr('data-product-id')).val();
      var error = 'Fill out all the given fields to ask a question.';
      if(popup_question == ''){
        $('.alert-danger').remove();
        $('.msg_q_body').after('<div class="alert alert-danger">'+error+'</div>');
        return false;
      }
      // alert(customer_name);
      $.ajax({
        type : "POST",
        url  : 'index.php?route=product/product/askAQuestion',
        data : {product_id:product_id,popup_question:popup_question,customer_name:customer_name,telephone:telephone},
        dataType: 'json',
        beforeSend: function() {
          $('.popup-q-footer .btn-default').button('loading');
        },
        complete: function() {
          $('.popup-q-footer .btn-default').button('reset');
        },
        success: function(json){
          $('.alert-danger').remove();
          $('.content_q_popup .popup-q-body,.content_q_popup .popup-q-footer ').hide();
          $('.msg_q_body').after('<div class="alert alert-success"><i class="fa fa-check-circle"></i>'+json['message']+'</div>');
        }
      });
    });
    <?php }else{ ?>
    $('.popup-q-footer .question-btn').click(function(){
      //var user_id = $('.user_id_'+$(this).attr('data-product-id')).val();
      var product_id = $('.product_q_id_'+$(this).attr('data-product-id')).val();
      var customer_name = $('.customer_q_name').val();
      var telephone = $('.customer_q_telephone').val();
      var popup_question = $('.popup_q_question_'+$(this).attr('data-product-id')).val();
      var error = 'Fill out all the given fields to ask a question.';
      var error_email = 'Please provide your Mobile No or Email';
      var email = $('.customer_q_email').val();

      if(customer_name == ''){
        $('.alert-danger').remove();
        $('.msg_q_body').after('<div class="alert alert-danger">'+error+'</div>');
        return false;
      }

      if(telephone == '' && email == ''){
        $('.alert-danger').remove();
        $('.msg_q_body').after('<div class="alert alert-danger">'+error_email+'</div>');
        return false;
      }

      if(popup_question == ''){
        $('.alert-danger').remove();
        $('.msg_q_body').after('<div class="alert alert-danger">'+error+'</div>');
        return false;
      }
      $.ajax({
        type : "POST",
        url  : 'index.php?route=product/product/askAQuestion',
        data : {product_id:product_id,popup_question:popup_question,customer_name:customer_name,telephone:telephone,email:email},
        dataType: 'json',
        beforeSend: function() {
          $('.popup-q-footer .btn-default').button('loading');
        },
        complete: function() {
          $('.popup-q-footer .btn-default').button('reset');
        },
        success: function(data){
          $('.alert-danger').remove();
          $('.content_q_popup .popup-q-body,.content_q_popup .popup-q-footer ').hide();
          $('.msg_q_body').after('<div class="alert alert-success"><i class="fa fa-check-circle"></i>'+data['message']+'</div>');
        }
      });
    });
    <?php } ?>
</script>

<script>
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
</script>
<div class="download-inner" style="display: none;">
  <?php if ($images) { ?>
    <?php $i=0; foreach ($images as $image) { ?>
      <a class="thumbnail" href="<?php echo 'image/'.$image['original']; ?>" title="<?php echo $heading_title; ?>" download> <img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
    <?php } ?>
  <?php } ?>
</div>
<?php //echo $footer; ?>
<script src="catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js" type="text/javascript"></script>
<link href="catalog/view/javascript/jquery/owl-carousel/owl.carousel.css" type="text/css" rel="stylesheet" media="screen" />

