<?php foreach ($products as $product) { ?>
              <div class="product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <?php
                  $quantity = $product['quantity'];
                  $stock_status = $product['stock_status'];
                  $text_out_of_stock = $product['text_out_of_stock'];
                  $text_in_stock = $product['text_in_stock'];
                  $text_sold_out = $product['text_sold_out'];

                  if($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock'){
                    $bgcolor = 'red';
                    $instock = '<span class="in_stock">'.$text_out_of_stock.'</span> ';
                  }else{
                    $bgcolor = 'green';
                    if($product['is_single'] == '1'){
                      //$instock = '<span class="in_stock">'.$product['items_in_stock'].'</span>';
                      $instock = '';
                    }else{
                      $instock = '<span class="in_stock">'.$text_in_stock.'</span>';
                    }
                  }
                ?>
                <?php /* if($product['is_single'] == '1'){ ?>
                 <span class="singleStore"><?php echo $single_item;?></span>
                <?php } */ ?>
                <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') {
                    echo '<div class="product-thumb " style="opacity:0.4;">'; }
                  else {
                    echo '<div class="product-thumb" data-product-id="'.$product['product_id'].'">'; }
                ?>
                <div class="image"><a href="<?php echo $product['href']; ?>" class="fancybox fancybox.iframe"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a>
                </div>
                <div>
                  <div class="caption">
                        <h4><a href="<?php echo $product['href']; ?>" class="fancybox fancybox.iframe ellipsis"><?php echo mb_strimwidth($product['name'], 0, 50, "..."); ?></a></h4>
                    <!-- <?php // if ($product['rating']) { ?>
                    <div class="rating">
                      <?php // for ($i = 1; $i <= 5; $i++) { ?>
                      <?php // if ($product['rating'] < $i) { ?>
                      <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
                      <?php // } else { ?>
                      <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>
                      <?php // } ?>
                      <?php // } ?>
                    </div>
                    <?php // } ?> -->

                    <?php if ($product['price']) { ?>
                      <p class="price">
                        <?php if (!$product['special']) { ?>
                          <span class="price-new"><?php echo $product['price']; ?></span><?php echo $text_per_piece; ?>
                        <?php } else { ?>
                          <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span> <?php echo $text_per_piece; ?>
                        <?php } ?>
                        <?php /* if ($product['tax']) { ?>
                        <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                        <?php } */ ?>
                      </p>
                    <?php } ?>

                    <?php
                    if($product['is_single'] == '1'){

                    }else{
                      if ($product['minimum']) { ?>
                        <span class="min-moq">
                          <?php if ($product['minimum'] > 1) { ?>
                            <b><?php echo $text_moq_pre . $product['minimum'] . $text_moq_post; ?></b>
                          <?php } else { ?>
                            <b><?php echo $text_moq_default; ?></b>
                          <?php } ?>
                        </span>
                      <?php } else { ?>
                        <p>
                          <b><?php echo $text_moq_default; ?></b>
                        </p>
                      <?php } ?>
                    <?php /* ?>
                      <p><?php echo $product['set_description']; ?></p>
                      <div class="stock_circle_list <?php echo $bgcolor; ?>"><div><?php echo $instock; ?><span class="soldout"><?php echo $text_sold_out;?></span></div></div>
                    <?php */ ?>
                    <?php } ?>
                  </div>
                  <!--<div class="button-group">
                    <button type="button" <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { echo "disabled"; } ?> class="addtocart" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $button_cart; ?></span></button>
                    <button type="button" class="addtowishlist" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart"></i></button>
                    <!-- <button type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button>
                  </div>-->
                </div>
              </div>
    <!-- Start hover description -->
           	<div class="hover_description product-grid col-lg-4" id= "hover_description_<?php echo $product['product_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>">
                <div class="button-group">
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
                    <div class="button_add_wishlist" data-product-id="<?php echo $product['product_id']; ?>">
                      <button type="button" class="addtowishlist" id="wishlist_<?php echo $product['product_id']; ?>" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class=" <?php echo $fill_heart;?>" id="wishlist_heart_<?php echo $product['product_id']; ?>"></i></button>
                    </div>
                    <div class="button_add_cart">
                    <a href="<?php echo $product['href']; ?>" class="fancybox fancybox.iframe ellipsis">
                      <button type="button" class="quickview btn"><i class="fa fa-eye"></i> <span class=""><?php echo $detail_view; ?></span></button>
                    </a>


                      <button type="button" <?php if ($product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { echo "disabled"; } ?> class="addtocart btn" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><i class="fa fa-shopping-cart"></i> <span class=""><?php echo $button_cart; ?></span></button>
                    </div>
                    <!-- <button type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button> -->
              	</div>
              	<div class="product_description">
              		<span class="product_set_description">
              			<?php echo $product['set_description'];?>
              		</span>
              	</div>
                <!-- Start Product Options(Size)  -->
                <?php
                       if($product['product_options']){
                        $opt_value = array();
                ?>
                <div class="product_options">
                    <?php foreach($product['product_options'] as $options){
                        if(isset($options['name']) && $options['name']=='color'){
                    ?>
                            <span class='title_color'><?php echo "Color:";?>
                        <?php foreach($options['product_option_value'] as $opt_value){ ?>
                              <?php if(isset($opt_value['quantity']) && $opt_value['quantity'] > 0){ ?>
                                <span style='background:<?php echo $opt_value['name']; ?>'></span>
                        <?php  } }?>
                            </span>
                    <?php }
                        if(isset($options['name']) && $options['name']=='Size'){
                    ?>
                            <span class='title_size'><?php echo "Size:";?>
                        <?php foreach($options['product_option_value'] as $opt_value){ ?>
                             <?php if($opt_value['quantity'] > 0){ ?>
                               <span><?php echo $opt_value['name']; ?></span>
                        <?php  } } ?>
                            </span>
                    <?php    }?>
                    <?php } ?>
                </div>
                <?php } ?>
                <!-- End Product Options(Size)  -->
            </div>
    <!-- End hover description -->

            </div>
          <?php } ?>

 <!-- Start Script for hover description -->
 <script type="text/javascript">
   $(document).ready(function(){
     $('.product-thumb, .hover_description').mouseover(function(){
       //alert($( this ).attr('data-product-id'));
       $('#hover_description_'+$(this).attr('data-product-id')).show();
     }).mouseleave(function(){
       //alert($( this ).attr('data-product-id'));
       $('#hover_description_'+$(this).attr('data-product-id')).hide();
     });

       $('.button_add_wishlist').click(function(){
           $('#wishlist_heart_'+$(this).attr('data-product-id')).removeClass( "fa fa-heart-o" ).addClass( "fa fa-heart custom_heart" );
       });

/*
     $('#wishlist_'+<?php echo $product['product_id']; ?>).click(function(){
       $('#wishlist_heart_'+<?php echo $product['product_id']; ?>).removeClass( "fa fa-heart-o" ).addClass( "fa fa-heart fa-3x custom_heart" );;
     });
     */

       $(".fancybox").click(function(e){

           e.preventDefault();
           var $href = $(this).attr('href');
           var popup_string = '&';
           var find_query_string = $href.indexOf("?");
           if(find_query_string > 0) {
               popup_string = '&';
           }
           $href = $href+popup_string+'popup=true';

           //+'?popup=true';
           $.fancybox({

               'hideOnContentClick': true,
               maxWidth: 980,
               width:'100%',
               padding:0,
               type: 'iframe',
               href:$href,
               iframe: {
                   preload: false // fixes issue with iframe and IE
               }
           });

       });
   });

 </script>
 <!-- End Script for hover description -->
