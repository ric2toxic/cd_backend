     <?php
         if ($products) { ?>

              <div class="row list_container_class" id="list_container" >



                      <div class="col-md-8 selected_filters ">
                          <?php
                                if(isset($filters) && is_array($filters) && !empty($filters)){

                                    foreach($filters as $filter){
                                     ?>
                                            <span class="filter" data-id="<?php echo $filter['filter_id'];?>"><?php echo $filter['name']; ?>&nbsp;<i class="fa fa-times"></i>
                                        </span>
                                     <?php
                                    }

                                    if(count($filters) > 2 ){
                                              ?>
                                        <span class="filter clearall" data-id="all">Clear All&nbsp;<i class="fa fa-times"></i></span>
                                            <?php
                                    }
                                    ?>
                                            <br /> <br />
                                <?php
                                 }
                           ?>
                      </div>
                      <div class="col-md-8 selected_filters ">
                          <?php
                                if(isset($options) && is_array($options) && !empty($options)){

                                    foreach($options as $option){
                                     ?>
                                            <span class="filter" data-id="<?php echo $option['option_value_id'];?>"><?php echo $option['name']; ?>&nbsp;<i class="fa fa-times"></i>
                                        </span>
                                     <?php
                                    }

                                    if(count($options) > 2 ){
                                              ?>
                                        <span class="filter clearall" data-id="all">Clear All&nbsp;<i class="fa fa-times"></i></span>
                                            <?php
                                    }
                                    ?>
                                            <br /> <br />
                                <?php
                                 }
                           ?>
                      </div>




                  <?php if($single_store_alert != '' ){ ?>
                  <div class="col-xs-12 col-sm-12 alert alert-warning">
                      <?php echo $single_store_alert; ?>
                  </div>
                  <?php } ?>

                  <?php echo $product_list; ?>
                  <div id="results"></div>

              </div>

                <?php
                 if($current_page < $total_pages) { ?>
                  <div class="browse_more">
                      <button type="button" class="btn_browse_more btn btn-primary" data-page="<?php echo $current_page;?>" data-total-pages="<?php echo $total_pages;?>" path_no="<?php echo $current_page_path;?>">Browse More</button>
                  </div>
                <br />
                <?php } ?>

  <?php } ?>
      <?php if (!$categories && !$products) { ?>
      <p><strong><h3><?php echo $text_empty; ?><h3></p></strong>
      <div class="buttons">
        <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
      </div>
      <?php } ?>
      <?php echo $content_bottom; ?>

<script type="text/javascript">

  $(document).ready(function () {
      //price_filter = '<?php echo isset($price_filter)? $price_filter : '';?>';
      //sorting = '<?php echo isset($sort)? $sort : '';?>';

      var getUrlParameter = function getUrlParameter(sParam,url) {
          var sPageURL = decodeURIComponent(url.substring(1)),
                  sURLVariables = sPageURL.split('&'),
                  sParameterName,
                  i;

          for (i = 0; i < sURLVariables.length; i++) {
              sParameterName = sURLVariables[i].split('=');

              if (sParameterName[0] === sParam) {
                  return sParameterName[1] === undefined ? true : sParameterName[1];
              }
          }
      };

      price_filter = '';
      sorting = '';
      var rating_filter = '';
    $('div.product-thumb').mouseenter(function(){
        $(this).find('div.button-group').addClass('hoverclass');
    });

    $('div.product-thumb').mouseleave(function(){
      $(this).find('div.button-group').removeClass('hoverclass').addClass( "button-group" );;
    })

      var hash = window.location.hash;
      var spliting = hash.split("&");

      if(window.location.hash) {
          price_filter = spliting[1].split("=")[1];


          if(typeof spliting[4] != 'undefined') {
            sorting = spliting[3].split('=')[2] + "&" + spliting[4];
          }

          rating_filter = getUrlParameter('rating_filter',hash);
      }
      $("span.filter").click(function() { //alert("here");
          filter = [];
          option = [];
          $('input[name^=\'filter\']:checked').each(function (element) {
              filter.push(this.value);
          });
          $('input[name^=\'option\']:checked').each(function (element) {
              option.push(this.value);
          });



          if($(this).attr('data-id') == 'all'){
              filter = [];
              $('input[name^=\'filter\']').attr('checked',false);
              option = [];
              $('input[name^=\'option\']').attr('checked',false);
          }else{
              filter.splice($.inArray($(this).attr('data-id'), filter),1);

              $('input#filter'+$(this).attr('data-id')).attr('checked',false);
              option.splice($.inArray($(this).attr('data-id'), option),1);
              $('input#option'+$(this).attr('data-id')).attr('checked',false);

          }





          addFilterInHistoryState(filter,price_filter,option,sorting,rating_filter);

          $.ajax({
              url: 'index.php?route=product/category',
              type: 'get',
              data: 'path=<?php echo $path;?>&post_type=ajax&filter=' + filter.join(',')+"&price_filter="+price_filter+"&option="+option.join(',')+"&sort="+sorting+"&rating_filter="+rating_filter,
              dataType: 'html',
              beforeSend: function () {
                  $('body').removeClass('loaded').addClass('loading');
                 // $('.filter_box').addClass('hidden-xs');
              },
              complete: function () {
                  $('body').removeClass('loading').addClass('loaded');


              },
              success: function (data) {

                  $('#content').html(data);

              }
          });
      });

  });

  function addFilterInHistoryState(filter,price_filter,option,sorting,rating_filter){
      if (window.location.hash) {
          var url_page = window.location.protocol + "//" + window.location.host + window.location.pathname;

          filter_string = url_page + "#!filter=" + filter.join(',')+"&price_filter="+price_filter+"&option="+option.join(',')+"&sort="+sorting+"&rating_filter="+rating_filter;

      } else {
          filter_string = window.location + "#!filter=" + filter.join(',')+"&price_filter="+price_filter+"&option="+option.join(',')+"&sort="+sorting+"&rating_filter="+rating_filter;
      }

      history.pushState(null, null, filter_string);
  }
</script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.btn_browse_more').click(function(){ //alert(1);
            filter_qry_str = '';
            if(window.location.hash) {
                filter_qry_str = "&"+window.location.hash.replace("#!", "");
            }
            $a = parseInt($(this).attr('data-page'));
            $b = parseInt($(this).attr('data-total-pages'));
            if($a+1 == $b || $a == $b)
            {
                $(this).hide();
            }
            $flag=0;
            if($a < $b){
                if($flag==0){
                    $flag = 1;

                    $.ajax({
                        type: 'get',
                        url: '<?php echo $url_path;?>',
                        data: "<?php echo $qry_string;?>&track=1&mobile=1&post_type=ajax_pagination&path="+(parseInt($(this).attr('path_no')))+"&page="+(parseInt($(this).attr('data-page'))+1)+"&handpicked_ids=<?php echo $handpicked_ids; ?>&random_string=<?php echo $random_string; ?>&product_total=<?php echo $product_total; ?>"+filter_qry_str,
                        dataType: 'html',
                        beforeSend: function() {
                            $('.btn_browse_more').button('loading');
                        },
                        complete: function() {
                            //$('.btn_browse_more').button('reset');
                        },
                        success: function(data) {
                            //$("#results").append(data);
                            if($("#results").append(data)){
                                $('.btn_browse_more').button('reset');
                            }
                            $('.btn_browse_more').attr('data-page',$a+1);
                            $flag = 0;
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            }
            if($a+1 == $b || $a == $b)
            {
                $(this).hide();
            }
        });

        /*$(".fancybox").click(function(e){
            e.preventDefault();
            var $href = $(this).attr('href');



                // no history support :(
                // fall back to a scripted solution like History.js

                var popup_string = '&';
                var find_query_string = $href.indexOf("?");
                if (find_query_string > 0) {
                    popup_string = '&';
                }
                $href = $href + popup_string + 'popup=true';

                //+'?popup=true';
                $.fancybox({

                    hideOnContentClick: true,
                    maxWidth: 768,
                    width: '100%',
                    padding: 0,
                    type: 'iframe',
                    href: $href,
                    centerOnScroll: true,
                    autoScale: true,
                    iframe: {
                        preload: false // fixes issue with iframe and IE
                    },
                    helpers: {
                        overlay: {
                            locked: true,
                            closeClick: false
                        }
                    }
                });

        });*/

    });
</script>
