<?php echo $header; ?>

<section class="category_head1">
<div class="container ">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>

    <?php } ?>

  </ul>
  </div>
</section>

  <div class="container category_page">

      <div class="row">
          <div class="col-sm-12 category-title">
              <h2><?php echo $heading_title; ?> </h2>
          </div>
      </div>
      <?php if(!empty($data_deal_day)){ ?>
          <div class="row deal_day_blocks">
              <?php
                $column_class = 'col-sm-12';
                $total_num_deal_day = $total_deal_day;

                switch($total_num_deal_day){
                     case 1:
                         $column_class = 'col-sm-12';
                         $break_line = '';
                         $break_line_new = '';
                         break;
                     case 2:
                         $column_class = 'col-sm-6';
                         $break_line = '';
                         $break_line_new = '';
                         break;
                     case 3:
                         $column_class = 'col-sm-4';
                         $break_line_new = '<br>';
                         $break_line = '';
                         break;
                     case 4:
                         $column_class = 'col-sm-3 deal_blocks';
                         $break_line = '<br>';
                         $break_line_new = '';
                         break;
                     default:
                         $column_class = 'col-sm-3 deal_blocks';
                         $break_line = '<br>';
                         $break_line_new = '';
                         break;
                }

                foreach($data_deal_day as $data){
                ?>
                    <div class="<?php echo $column_class; ?>">
                        <div class="col-sm-12 block ">
                            <ul class="promo_box">
                                <?php /* ?><li class="ic_ruppee"> </li> <?php */ ?>
                               <?php /* ?> <li class="promo_text"><?php //echo abs($data['discount']);?>% Discount On <?php  //echo $break_line; ?>  RS. <?php //echo $data['order_amount']?> <?php  //echo $break_line_new; ?> and more</li> <?php */ ?>
                                <li class="promo_text"><?php echo $data['description'];?></li>
                            </ul>
                        </div>
                    </div>

              <?php } ?>
          </div>
      <?php } ?>
  <div class="row"><?php echo $column_left; ?>
  <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?> coulmn_right_flat1"><?php echo $content_top; ?>
        <!--<div class="col-md-8 category-title"><h2><?php echo $heading_title; ?> </h2></div>-->
     
      <div class="clear"></div>

        <?php if(!empty($products)){ ?>
          <div class="row sorting_bottom_line">
            <?php //echo "<pre>"; print_r($data); echo "</pre>";?>
            <div class="col-md-12 col-xs-6 text-right desktop_only">
              <!--<label class="control-label" for="input-sort"><?php // echo $text_sort; ?></label>-->
                <div class="col-sm-8">
                    <div class="range_sort">
                        <!--<label class="control-label" for="input-sort"><?php // echo $text_sort; ?></label>-->
                        <ul id="input-sort" class="form-control" style="display:inline-block;">
                            <li class="sortBy">Sort By:</li>
                            <?php foreach ($sorts as $sorts) { ?>
                            <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
                            <li class="active"><a href="javascript:void(0);" data-value="<?php echo $sorts['query_string']; ?>" ><?php echo $sorts['text']; ?></a></li>
                            <?php } else { ?>
                            <li><a href="javascript:void(0);" data-value="<?php echo $sorts['query_string']; ?>"><?php echo $sorts['text']; ?></a></li>
                            <?php } ?>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-4">
                    <select name="category_id" class="form-control">
                        <option value="0"><?php echo $text_category; ?></option>
                        <?php foreach ($categories as $category_1) { ?>
                        <?php if ($category_1['category_id'] == $category_id) { ?>
                        <option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
                        <?php } ?>
                        <?php foreach ($category_1['children'] as $category_2) { ?>
                        <?php if ($category_2['category_id'] == $category_id) { ?>
                        <option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
                        <?php } ?>
                        <?php foreach ($category_2['children'] as $category_3) { ?>
                        <?php if ($category_3['category_id'] == $category_id) { ?>
                        <option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
          </div>
        <?php }else{ ?>
            <p><strong><h3><?php echo $text_empty; ?></h3></strong></p>
            <div class="buttons">
                <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
            </div>
        <?php } ?>
      <div class="row">

        <div class="col-md-8 selected_filters">

          <?php
            if(isset($filters) && is_array($filters) && !empty($filters)){

                foreach($filters as $filter){
        ?>
            <span class="filter" data-id="<?php echo $filter['filter_id'];?>"><?php echo $filter['name']; ?><i class="fa fa-times"></i>
</span>

          <?php
                }
            }
        ?>
        </div>
      </div>

        <?php /* ?>
      <!-- <?php if ($thumb || $description) { ?>
      <div class="row">
        <?php if ($thumb) { ?>
        <div class="col-sm-2"><img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" title="<?php echo $heading_title; ?>" class="img-thumbnail" /></div>
        <?php } ?>
        <?php if ($description) { ?>
        <div class="col-sm-10"><?php echo $description; ?></div>
        <?php } ?>
      </div>
      <hr>
      <?php } ?> --> <?php */ ?>

        <!-- <?php // if($single_store_alert != ''){ ?>
        <div class="row">
            <div class="alert alert-warning nomargin col-md-12">
                <?php // echo $single_store_alert;?>
            </div>
        </div>
        <?php // } ?> -->
        
        <?php if($bandhani_alert){ ?>
        <div class="row">
            <div class="alert alert-warning nomargin col-md-12">
                <?php echo $bandhani_alert;?>
            </div>
        </div>
        <?php } ?>
        
      <?php if ($products) { ?>
      <!-- <p><a href="<?php //echo $compare; ?>" id="compare-total"><?php // echo $text_compare; ?></a></p> -->

      <div class="row" >
        <?php /* ?>
          <div class="col-md-7">
            <div class="btn-group hidden">
              <button type="button" id="list-view" class="btn btn-default" data-toggle="tooltip" title="<?php echo $button_list; ?>"><i class="fa fa-th-list"></i></button>
              <button type="button" id="grid-view" class="btn btn-default" data-toggle="tooltip" title="<?php echo $button_grid; ?>"><i class="fa fa-th"></i></button>
            </div>
          </div>


          <div class="col-md-1 text-right">
            <label class="control-label" for="input-limit"><?php echo $text_limit; ?></label>
          </div>
          <div class="col-md-2 text-right">
            <select id="input-limit" class="form-control" onchange="location = this.value;">
              <?php foreach ($limits as $limits) { ?>
              <?php if ($limits['value'] == $limit) { ?>
              <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
          </div>
          <?php */ ?>
        </div>
        <br />
        <div class="row <?php echo $store_class; ?>" id="list_container">
          <?php echo $product_list; ?>
          <div id="results"></div>
        </div>
        <?php if($current_page < $total_pages) { ?>
          <div class="browse_more">
            <button type="button" class="btn_browse_more btn" data-page="<?php echo $current_page;?>" data-total-pages="<?php echo $total_pages;?>" path_no="<?php echo $current_page_path;?>">Browse More</button>
          </div>
        <?php } ?>
      <?php /* ?>
        <div class="row custom-pagination-class">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      <?php */ ?>

      <?php } ?>
      <?php if (!$categories && !$products) { ?>
      <p><strong><h3><?php echo $text_empty; ?><h3></p></strong>
      <!--<div class="buttons">
        <div class="pull-right"><a href="<?php //echo $continue; ?>" class="btn btn-primary"><?php //echo $button_continue; ?></a></div>
      </div>-->
      <?php } ?>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php //echo $url_path; ?>
<?php // echo $qry_string; ?>
<script type="text/javascript">
  $(document).ready(function () {
    $('div.product-thumb').mouseenter(function(){
        $(this).find('div.button-group').addClass('hoverclass');
    });

    $('div.product-thumb').mouseleave(function(){
      $(this).find('div.button-group').removeClass('hoverclass').addClass( "button-group" );;
    })
  });
</script>
<script type="text/javascript">
    $(document).ready(function() {

      $('.btn_browse_more').click(function(){
          if(window.location.hash) {
              fiter_qry_str = window.location.hash.replace("#!", "");

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
              url: 'index.php?route=product/category/dealoftheday&post_type=ajax_pagination&page=' + (parseInt($(this).attr('data-page')) + 1),
              //url: 'index.php?route=product/categoruy',
              //data: "&post_type=ajax_pagination&page=" + (parseInt($(this).attr('data-page')) + 1),
              dataType: 'html',
              processData : 'false',
              beforeSend: function() {
                $('.btn_browse_more').button('loading');
              },
              complete: function() {
                $('.btn_browse_more').button('reset');
              },
              success: function(data) {     
                $("#results").append(data); 
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
    });
</script>
<script type="text/javascript"><!--
  sorting = '';
  category_id = '';
    //price_filter_script = '';

    $(document).ready(function () {
        $("#input-sort li a").click(function() {
            sorting = $(this).attr('data-value');
            jQuery.fancybox.close();
            callAjaxToFilter();
        });

        $('.sorting_bottom_line select[name=\'category_id\']').change(function(){
            category_id = $(this).val();
            jQuery.fancybox.close();
            callAjaxToFilter();
        });

        var hash = window.location.hash;
        var spliting = hash.split('&');
//console.log(spliting);
        if(window.location.hash){
            if(typeof spliting[2] == 'undefined'){
                category_id = spliting[1].split('=')[1];
            }else{
                sorting = spliting[0].split('=')[1]+"&order="+spliting[1].split('=')[1];
                category_id = spliting[2].split('=')[1];
            }
            callAjaxToFilter();
        }
    });


    function callAjaxToFilter() {
        addFilterInHistoryState(sorting,category_id);
        $.ajax({
            url: 'index.php?route=product/category/dealoftheday',
            type: 'get',
            data: 'post_type=ajax&sort='+sorting+'&category_id='+category_id,
            dataType: 'html',
            beforeSend: function () {
                $('body').removeClass('loaded').addClass('loading');
                $('.filter_box').addClass('hidden-xs');

            },
            complete: function () {
                $('body').removeClass('loading').addClass('loaded');

            },
            success: function (data) {
                $('body').removeClass('loading').addClass('loaded');
                $('#content').html(data);

            }
        });
    }


    function addFilterInHistoryState(sorting,category_id){
        if (window.location.hash) {
            var url_page = window.location.protocol + "//" + window.location.host + window.location.pathname;
            //filter_string = url_page + "#!filter=" + filter.join(',') + "&price_filter=" + price_filter + "&sort="+sorting;
            filter_string = url_page + "#!sort="+sorting+"&category_id="+category_id;
        } else {
            //filter_string = window.location + "#!filter=" + filter.join(',') + "&price_filter=" + price_filter + "&sort="+sorting;
            filter_string = window.location + "#!sort="+sorting+"&category_id="+category_id;
        }
        history.pushState('<?php echo $path;?>', null, filter_string);
    }
    //-->


    $('#closeing_image').click(function() {
        $('#tooltip_container').hide();
    });


</script>

<!-- ajax called to set cookie for informative tooltip on category page -->

<script type="text/javascript">
    $(document).ready(function(){
        $("#tooltip_checkbox").click(function(){
            //alert($(this).attr("html-data"));
            $.ajax({
                url : "index.php?route=product/category/informativetooltip",
                type: "POST",
                //dataType: "json",
                data: "checkbox alert",
                success: function( data ) {
                    //alert(data);
                }
            });
        });
    });
</script>

<?php echo $footer; ?>
