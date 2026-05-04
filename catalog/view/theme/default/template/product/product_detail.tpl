<?php echo $header; ?>
<style> .zoomContainer{z-index:100!important;}</style>
<script src="catalog/view/javascript/jquery/jquery.cycle2.min.js" type="text/javascript"></script>
<script src="catalog/view/javascript/jquery/jquery.cycle2.carousel.min.js" type="text/javascript"></script>
<div itemscope itemtype="http://schema.org/Product">

  <div class="container-fluid width_fix">
   <div class="row">

    <?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>

     <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>

        <div class="row">
   
          <?php echo $landscape_photos; ?>
 


         <div class="clearfix"></div>
          <br>

          <?php if ($products) { ?>
         <section class="col-sm-12 detail_page_slider">
            <div class="slider_title"><h4 class="text_line"><span>MORE FROM THIS SELLER</span></h4></div>

            <div class="carousel slide multi-item-carousel" id="theCarousel">
              <div class="carousel-inner related_product_slider">
                <div class="item active">
                <?php $i = 1; ?>
          <?php foreach ($products as $product) { ?>
                  <div class="c-product-card new_ outofstock installments_ mastercard c-product-card_js_inited c-product-card_view_grid home-product-list">
                    
                      <div class="c-product-card__img-placeholder">
                          <a href="<?php echo $product['href']; ?>" class="c-product-card__img-placeholder-inner">
                              <span class="c-img-lazy  c-product-card__img  c-img-lazy_js_inited c-img-lazy_loaded">
                                  <img class="largeImage c-img-lazy__img" src="<?php echo $product['image_medium']; ?>" width="<?php echo $product['img_releted_width']; ?>" height="<?php echo $product['img_releted_height']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" >
                              </span>
                          </a>
                      </div>
                      <div class="c-product-card__price-block">
                          <div class="c-quick-buy  c-product-card__buy-button c-quick-buy_js_inited">
                             <a href="<?php echo $product['href']; ?>" class="fancybox fancybox.iframe ellipsis productRelatedView">
                              <button class="c-quick-buy__button btn_margin c-button c-button_size_big c-button_color_old-orange  c-button_js_inited">DETAIL VIEW</button>
                             </a> 

                              <button  class="c-quick-buy__button c-button c-button_size_big c-button_color_old-orange  c-button_js_inited addtocart product_addtocart" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>"
                           onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"
                           data-product-id="<?php echo $product['product_id']; ?>"
                           data-popupgaid="<?php echo $cart_tracking_id_for_ga; ?>">ADD TO CART</button>

                          </div>
                      </div>
                      <div class="c-product-card__description">
                        <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                       </div>
                        <div class="c-product-card__price">
                        <?php if ($product['price']) { ?>
                        <?php if (!$product['special']) { ?>
                        <?php echo $product['price']; ?>
                        <?php } else { ?>
                         <span class="c-product-card__price-final"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
                        <?php } ?>
                        <?php if ($product['tax']) { ?>
                         <span class="c-product-card__price-final"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                        <?php } ?>
                     
                    <?php } ?>
                          
                        </div>
                        <div class="product_card_description">
                           <span><?php echo $product['set_description'];?></span>
                        </div>
                  </div>
                 <?php if($i%6==0) { echo '</div><div class="item">'; } $i++; } ?>
                </div>

                <!--  Example item end -->
              </div>
              <div class="detail_slider_btn">
              <a class="" href="#theCarousel" data-slide="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a>
              <a class="" href="#theCarousel" data-slide="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
              </div>
            </div>
         </section>
         <!--seller slider saction(end) -->
         <div class="clearfix"></div>
         <?php } ?>

          <?php if ($products) { ?>
         <section class="col-sm-12 detail_page_slider">
            <div class="slider_title"><h4 class="text_line"><span><?php echo $text_related; ?></span></h4></div>

            <div class="carousel slide multi-item-carousel" id="theCarousel2">
              <div class="carousel-inner related_product_slider">
                <div class="item active">
                <?php $i = 1; ?>
          <?php foreach ($products as $product) { ?>
                  <div class="c-product-card new_ outofstock installments_ mastercard c-product-card_js_inited c-product-card_view_grid home-product-list">
                    
                      <div class="c-product-card__img-placeholder">
                          <a href="<?php echo $product['href']; ?>" class="c-product-card__img-placeholder-inner">
                              <span class="c-img-lazy  c-product-card__img  c-img-lazy_js_inited c-img-lazy_loaded">
                                  <img class="largeImage c-img-lazy__img" src="<?php echo $product['image_medium']; ?>" width="<?php echo $product['img_releted_width']; ?>" height="<?php echo $product['img_releted_height']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" >
                              </span>
                          </a>
                      </div>
                      <div class="c-product-card__price-block">
                          <div class="c-quick-buy  c-product-card__buy-button c-quick-buy_js_inited">
                             <a href="<?php echo $product['href']; ?>" class="fancybox fancybox.iframe ellipsis productRelatedView">
                              <button class="c-quick-buy__button btn_margin c-button c-button_size_big c-button_color_old-orange  c-button_js_inited">DETAIL VIEW</button>
                             </a> 

                              <button  class="c-quick-buy__button c-button c-button_size_big c-button_color_old-orange  c-button_js_inited addtocart product_addtocart" html-data="<?php echo $product['product_id']; ?>-<?php echo $product['minimum']; ?>"
                           onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"
                           data-product-id="<?php echo $product['product_id']; ?>"
                           data-popupgaid="<?php echo $cart_tracking_id_for_ga; ?>">ADD TO CART</button>

                          </div>
                      </div>
                      <div class="c-product-card__description">
                        <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                       </div>
                        <div class="c-product-card__price">
                        <?php if ($product['price']) { ?>
                        <?php if (!$product['special']) { ?>
                        <?php echo $product['price']; ?>
                        <?php } else { ?>
                         <span class="c-product-card__price-final"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
                        <?php } ?>
                        <?php if ($product['tax']) { ?>
                         <span class="c-product-card__price-final"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                        <?php } ?>
                       <?php } ?>
                          
                        </div>
                        <div class="product_card_description">
                           <span><?php echo $product['set_description'];?></span>
                        </div>
                  </div>
                 <?php if($i%6==0) { echo '</div><div class="item">'; } $i++; } ?>
                </div>

                <!--  Example item end -->
              </div>
              <div class="detail_slider_btn">
              <a class="" href="#theCarousel2" data-slide="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a>
              <a class="" href="#theCarousel2" data-slide="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
              </div>
            </div>
         </section>
         <!--seller slider saction(end) -->
         <div class="clearfix"></div>
         <?php } ?>



         <br><br>
         <!--seller slider saction(end) -->
         <div class="clearfix"></div>
        </div>   
      </div>
   

  </div>
    <!-- /.row -->
  </div>                

 <!-- ask question popup(start) -->
  <div class="modal fade add_new_address" id="ask_question_popup" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header address_popup_head msg_q_body">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><?php echo $question_popup_heading;?></h4>
          </div>
          <div class="modal-body">
           <form>
              <div class="mobile_details_panel">
                 <div class="clearfix"></div>
                  <?php if(isset($logged_in)){ ?>
                  <input type="hidden" name="customer_name_login" value="<?php echo $customer_name; ?>" class="customer_q_name_login">
                   <input type="hidden" name="customer_telephone_login" value="<?php echo $telephone; ?>" class="customer_q_telephone_login">
                  <input type="hidden" name="customer_email_login" value="<?php echo $email; ?>" class="customer_q_email_login">
                  <?php }else{ ?>

                 <input id="customer_name" name="customer_name" class="customer_q_name password_box" type="text" placeholder="Please Enter your Name" alt="Name">
                 <div class="clearfix"></div>
                 <input id="customer_telephone" name="customer_telephone" class="customer_q_telephone password_box" type="text" placeholder="Please Enter your Mobile Number" alt="Mobile Number">
                 <div class="clearfix"></div>
                 <input id="customer_email" name="customer_email" class="customer_q_email password_box" type="text" placeholder="Please Enter your Email ID" alt="Email ID">
                 <div class="clearfix"></div>
                 <?php } ?>
                 <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" class="product_q_id_<?php echo $product_id;?>">
                 <textarea id="Question" name="popup_q_question" rows="4" class="popup_q_question_<?php echo $product_id;?> password_box" placeholder="Enter your Question" alt="Question"></textarea>
                 <div class="clearfix"></div>
                  <div class="popup-q-footer ">
                  <button type="button" class="btn deliver_btn pull-right question-btn" data-product-id="<?php echo $product_id;?>"><?php echo $comment_popup_send;?></button>
                  </div>
              </div>

           </form>
          <div class="clearfix"></div>
          </div>
           
        </div>
    </div>
  </div>


<?php if (isset($product['quantity']) && $product['quantity'] <= 0 || strtolower($stock_status) == 'out of stock') { ?>
<div class="modal fade add_new_address" id="comment_popup" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header address_popup_head msg_q_body">
            <button type="button" class="close" data-dismiss="modal" data-product-id="<?php echo $product['product_id'];?>">&times;</button>
            <h4 class="modal-title"><?php echo $comment_popup_heading;?></h4>
          </div>
          <div class="modal-body">
           <form>
              <div class="mobile_details_panel">
                 <div class="clearfix"></div>
                
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" class="product_id_<?php echo $product['product_id'];?>">
            <input type="hidden" name="product_status" value="<?php echo 'out of stock'; ?>" class="product_status_<?php echo $product['product_id'];?>">
                  
                 <textarea  name="popup_comment" rows="4" class="popup_comment_<?php echo $product_id;?> password_box" placeholder="Comment" alt="Comment"></textarea>
                 <div class="clearfix"></div>
                  <div class="popup-footer ">
                 <button type="button" class="btn deliver_btn pull-right custom-btn-default" data-product-id="<?php echo $product['product_id'];?>"><?php echo $comment_popup_send;?>
            </button>
                  </div>
              </div>

           </form>
          <div class="clearfix"></div>
          </div>
           
        </div>
    </div>
  </div>
      <!-- End Comment Popup -->
<?php } ?>

<script type="text/javascript">
   $(document).ready(function(){
    $(".input_qty").TouchSpin({ });
     $('.related_product_adjust, .hover_description').mouseover(function(){
       //alert($(this).attr('data-product-id'));
       $('#hover_description_'+$(this).attr('data-product-id')).css('display' , 'block');
       $('#related_product_'+$(this).attr('data-product-id')).css('opacity' , '0.3');
     }).mouseleave(function(){
       //alert($(this).attr('data-product-id'));
       $('#hover_description_'+$(this).attr('data-product-id')).css('display' , 'none');
       $('#related_product_'+$(this).attr('data-product-id')).css('opacity' , '1');
     });

   });


 </script>

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
<script type="text/javascript">

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
 </script>
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


  <?php  if( isset($option) && is_array($option) && isset($option['product_option_id'])){ ?>
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
    //pass the images to Fancybox
    $("#img_zoom").bind("click", function(e) {
        e.preventDefault();
        var ez =   $('#img_zoom').data('elevateZoom');
        $(ez.zoomContainer).css('z-index','1000');
        $.fancybox(ez.getGalleryList()); //cycle-carousel-wrap
        return false;
    });

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

<!-- panzoom  by garvit/vikas (08-01-2015) -->
<script type="text/javascript">

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

<div class="download-inner" style="display: none;">
  <?php if ($images) { ?>
  <?php $i=0; foreach ($images as $image) { ?>
        <a class="thumbnail" href="<?php echo 'image/'.$image['original']; ?>" title="<?php echo $heading_title; ?>" download> <img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
  <?php } ?>
  <?php } ?>
</div>
</script>
<script>
$('.comment_btn').click(function(){
  <?php if($this->customer->isLogged()){ ?>
    $('.content_popup .popup-body,.content_popup .popup-footer ').show();
    $('.popup-body textarea').val('');
    $('.alert-success').hide();
    $('body').scrollTop(0);
    $('.comment_popup').show();
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
      var email = $('.customer_q_email_login').val();
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
<script type="text/javascript">
    $(document).ready(function() {

        $(".more_desc").click(function() {
    $('html, body').animate({
        scrollTop: $(".share-heading").offset().top - 50
    }, 1500);
});
    });
    
</script>
<?php echo $footer; ?>