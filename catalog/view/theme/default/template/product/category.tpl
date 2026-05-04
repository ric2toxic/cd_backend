<?php echo $header; ?>

        <!-- Content Row -->
  <div class="container-fluid width_fix">
   <div class="row">
      <div class="col-sm-12">
        <div class="row">
        <!-- filtar section saction(start) -->
      
         <?php echo $column_left; ?>
        
         <!-- filtar section saction(end) -->
         <!-- contact saction(start) -->
         <column id="content" class="col-sm-10 category_section">
<!--           <ul class="breadcrumb">
          <li><a href="">Home</a></li>
          <li><a href="">Shopping Cart</a></li>
          </ul> -->
           <h3><?php echo $heading_title; ?> </h3>
          <div class="col-sm-12 nopadding heading_bottem">    
            <div class="range_sort col-sm-9">
              <ul id="input-sort" class="sort_panel_bg" style="display:inline-block;">
                <li class="sortBy">Sort By:</li>
                <?php foreach ($sorts as $sorts) { ?>
                  <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
                  <li class="<?php echo $sorts['selected']; ?>"><a href="javascript:void(0);" data-value="<?php echo $sorts['query_string']; ?>" ><?php echo $sorts['text']; ?></a></li>
                  <?php } else { ?>
                  <li class="<?php echo $sorts['selected']; ?>"><a href="javascript:void(0);" data-value="<?php echo $sorts['query_string']; ?>"><?php echo $sorts['text']; ?></a></li>
                  <?php } ?>
                  <?php } ?>
              </ul>
            </div>


            <div class="dropp col-sm-3 pull-right">
              <div class="dropp-header"> <span class="dropp-header__title js-value">All Products</span> <a href="#" class="dropp-header__btn js-dropp-action"><span class="nav-icon_3"></span></a> </div>
              <div class="dropp-body">
                <label for="opt0">Products with Real Pic<input id="opt0" name="stock_filter[]" value="0" type="radio"></label>
                <label for="opt1">All Products <input id="opt1" name="stock_filter[]" value="1" type="radio"></label>
              </div>
            </div>

          </div>
          <div class="clearfix"></div>
          <section>
              <div class="selected_filtar col-sm-12">
               <ul>
               
                 <?php if(isset($filters) && is_array($filters) && !empty($filters)){ ?>  
                 <li class="clear_filtar"><a href="">CLEAR ALL</a></li>
                 <?php foreach($filters as $filter){ ?>
                 <li class="filter" data-id="<?php echo $filter['filter_id'];?>"><?php echo $filter['name']; ?> <label class="filtar_close_icon"></label></li> 
                 <?php } } ?>
               </ul> 
              </div>
              <div class="clearfix"></div>


        <?php if($single_store_alert != ''){ ?>

            <div class="alert alert-warning nomargin col-md-12">
                <?php echo $single_store_alert;?>
            </div>
        <?php } ?>
        
        <?php if($bandhani_alert){ ?>
            <div class="alert alert-warning nomargin col-md-12">
                <?php echo $bandhani_alert;?>
            </div>
        <?php } ?>
        <?php if($alert_thaan_dispatch){ ?>
            <div class="alert alert-warning nomargin col-md-12">
                <?php echo $alert_thaan_dispatch;?>
            </div>
        <?php } ?>
<div class="clearfix"></div>

        <div class="content_panel<?php //echo $store_class; ?>" id="list_container">
          <?php echo $product_list; ?>
          <div id="results"></div>
        </div>
<div class="clearfix"></div>
          <?php if($current_page < $total_pages) { ?>
          <div class="browse_more">
            <button type="button" class="btn_browse_more btn" data-page="<?php echo $current_page;?>" data-total-pages="<?php echo $total_pages;?>" path_no="<?php echo $current_page_path;?>">Browse More</button>
          </div>
        <?php } ?> 


      <?php if (!$categories && !$products) { ?>
      <p><strong><h3><?php echo $text_empty; ?><h3></p></strong>
      <div style="background-color: whitesmoke;
    border-radius: 100%;
    color: #515151;
    font-size: 14px;
    font-weight: 800;
    text-align:center;
    height: 30px;
    line-height: 30px;
    margin-left :50%;
    width: 30px;"
    >OR</div>
      <p>
      <h3><?php echo $text_empty_store; ?><a href="javascript:void(0)" class="store_change">Click here</a> to change store<h3></p>
     

      <div class="buttons">
        <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
      </div>
      <?php } ?>        

              <div class="clearfix"></div>

           
          </section>



          </column>
         <!-- contact saction(end) -->


         <div class="clearfix"></div>
        </div>   
      </div>
   

  </div>
    <!-- /.row -->
  </div> 


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
              url: '<?php echo $url_path;?>&track=1',
              data: "<?php echo $qry_string;?>&post_type=ajax_pagination&path= "+(parseInt($(this).attr('path_no')))+"&page=" + (parseInt($(this).attr('data-page')) + 1)+"&handpicked_ids=<?php echo $handpicked_ids;?>&random_string=<?php echo $random_string; ?>&product_total=<?php echo $product_total; ?>",
              dataType: 'html',
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
    $('#button-filter').on('click', function() {
        filter = [];

        $('input[name^=\'filter\']:checked').each(function(element) {
            if (filter.indexOf(this.value) < 0) filter.push(this.value);
        });
    });
    sorting = '';
    price_filter_script = '';

    $(document).ready(function () {
        $("#input-sort li a").click(function() {
            sorting = $(this).attr('data-value');
            jQuery.fancybox.close();
            callAjaxToFilter();
        });
    });

    function callAjaxToFilter(urlcheck) { 
      filter = [];
      option = [];
      rating_filter = [];
     var url    = window.location.href; 
    
    var get_sorting = getUrlParameter('sort',url)+'&order='+getUrlParameter('order',url);
    if(urlcheck == 1){
      sorting = get_sorting;
    }
     // price_filter = ;

      $('input[name^=\'filter\']:checked').each(function (element) { 
        if (filter.indexOf(this.value) < 0) filter.push(this.value);
        //filter.push(this.value);
      });    
      $('input[name^=\'option\']:checked').each(function (element) { 
        if (option.indexOf(this.value) < 0) option.push(this.value);
        //filter.push(this.value);
      });
      $('input[name^=\'rating_filter\']:checked').each(function (element) {
          if (rating_filter.indexOf(this.value) < 0) rating_filter.push(this.value);
          //filter.push(this.value);
      });

      price_filter_script = $('#amount_hidden').val();
    //alert(price_filter_script);
      addFilterInHistoryState(filter,price_filter_script,option,sorting,rating_filter);

      $.ajax({46+0
        url: 'index.php?route=product/category',
        type: 'get',
        data: 'path=<?php echo $path;?>&post_type=ajax&filter=' + filter.join(',') + '&price_filter=' + price_filter_script +'&option='+ option.join(',')+ "&sort="+sorting+'&rating_filter='+ rating_filter.join(','),
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


    function addFilterInHistoryState(filter,price_filter,option,sorting,rating_filter){
        if (window.location.hash) {
            var url_page = window.location.protocol + "//" + window.location.host + window.location.pathname;
            filter_string = url_page + "#!filter=" + filter.join(',') + "&price_filter=" + price_filter + "&option="+ option.join(',')+ "&sort="+sorting + "&rating_filter="+ rating_filter.join(',');
        } else {
            filter_string = window.location + "#!filter=" + filter.join(',') + "&price_filter=" + price_filter + "&option=" +option.join(',')+"&sort="+sorting + "&rating_filter="+ rating_filter.join(',');
        }
        history.pushState('<?php echo $path;?>', null, filter_string);
    }


    $('#closeing_image').click(function() {
        $.ajax({
            url : "index.php?route=product/category/informativeTooltipClose",
            type: "POST",
            //dataType: "json",
            data: "cross alert",
            success: function( data ) {
                //alert(data);
            }
        });
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
            $('#tooltip_container').slideUp(500);
        });
    });
</script>
<script type="text/javascript">
$('.store_change').on('click' , function(argument) {
  $.ajax({
    url : 'index.php?route=common/header/getStoreSwitchNew',
    dataType: 'json',

    beforeSend: function () {
      $('body').removeClass('loaded').addClass('loading');        
    },

    success: function (json) {
      location.reload();

    },
    error: function(xhr, ajaxOptions, thrownError) {
      alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }

  });
});
</script>

<?php echo $footer; ?>
