<?php //echo $header;  die;?>
<div class="productpopup">
<div itemscope itemtype="http://schema.org/Product">
<div class="product_page_popup" >
    <?php $class = 'col-sm-12'; ?>
    <div id="content" class="<?php echo $class; ?>" style="background: #eee;"><?php echo $content_top; ?>
      <div class="row">
        <div class="col-sm-12 col-xs-12" id="panzoom-container">
            <?php echo $landscape_photos; ?>
        </div>
      <a id="description"></a>
      <div class="set_show_description home_width_adjustment">
                <div class="show_description row">
                <h4 class="active" >
                  <a href="#tab-description" data-toggle="tab" class="heading_title"><?php echo $tab_description; ?></a>
                </h4>
                  <div class="col-sm-12 description">
                  <div class="down"></div>
                    <div class="col-md-12" style="margin:25px 0px;">
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
        </div>
    <?php //echo $column_right; ?></div>
</div>


<div id="relatedProducts">
    <div class="container product_page home_width_adjustment" style="width:100%" >
        <div class="row">
            <div class="col-sm-12" style="text-align:center;"><i class="fa fa fa-refresh fa-spin" style="font-size:36px;"></i></div>
        </div>
    </div>
</div>

</div>


<div class="download-inner" style="display: none;">
  <?php if ($images) { ?>
  <?php $i=0; foreach ($images as $image) { ?>
  <a class="thumbnail" href="<?php echo 'image/'.$image['original']; ?>" title="<?php echo $heading_title; ?>" download> <img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
  <?php } ?>
  <?php } ?>
</div>


<script type="text/javascript">
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

                var owl = $("#owl-related");
                owl.owlCarousel({
                    autoPlay : 3000,
                    itemsMobile: [479, 1],
                    goToFirstSpeed : 3000,
                    transitionStyle:"slide",
                    lazyLoad : true,
                    rewindSpeed : 3000,
                    pagination: false,
                });

                // Custom Navigation Events
                $(".owl-related-next").click(function(){
                    owl.trigger('owl.next');
                });
                $(".owl-related-prev").click(function(){
                    owl.trigger('owl.prev');
                });

                $('#relatedProducts .related_product_adjust, #relatedProducts .hover_description').mouseover(function(){
                    $('#relatedProducts #hover_description_'+$(this).attr('data-product-id')).show();
                    $('#relatedProducts #related_product_'+$(this).attr('data-product-id')).css('opacity' , '0.3');
                }).mouseleave(function(){
                    $('#relatedProducts #hover_description_'+$(this).attr('data-product-id')).hide();
                    $('#relatedProducts #related_product_'+$(this).attr('data-product-id')).css('opacity' , '1');
                });
                var getStringObj = $("#productRelatedJson").attr('value');
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

    $(document).ready(function(){

       $(".input_qty").TouchSpin();

       //#############
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


       //#########
       $('.singleproductsizelist').click(function(){
          $('ul li').removeClass('sizeBackColor');
          var name = $(this).attr("data-name");
          var SelectdValue = $(this).attr("data-value");
          $('input[name="' + name+ '"][value="' + SelectdValue + '"]').prop('checked', true);
          $(this).addClass('sizeBackColor');
        });

       //## For downloads images
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
        $('.button_add_wishlist').on('click', function(){

            $(".tooltip").css('z-index','10000');
            $(this).find('#wishlist_heart_'+$(this).attr('data-product-id')).removeClass( "fa fa-heart-o" ).addClass( "fa fa-heart custom_heart" );
        });


        $(".btn-vertical-slider").mouseover(function(){
            $(".btn-vertical-slider").css("opacity","1");
        }).mouseleave(function(){
            $(".btn-vertical-slider").css("opacity","0.7");
        });

    });

    function continueShopping(){
        parent.jQuery.fancybox.close();
        $(".addedincart_popup_close").trigger('click');
    }
    function viewCart(){
      parent.jQuery.fancybox.close();
      parent.location.replace('index.php?route=checkout/cart');

    }
</script>
<script>
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
</script>

<script>
    $(document).ready(function(){

        $('.comment_btn').click(function(){
        <?php if($this->customer->isLogged()){ ?>
          $('.content_popup .popup-body,.content_popup .popup-footer ').show();
          $('.popup-body textarea').val('');
          $('.alert-success').hide();
          $('body').scrollTop(0);
          //$('.comment_popup').show();
          $('#comment_popup_detail_' + $(this).attr('data-product-id')).show();

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

    $('.ask-a-question').click(function(){
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

    });
</script>

<?php //echo $footer; ?>

