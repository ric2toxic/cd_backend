<?php echo $header; ?>
<div class="container category_page"  id = "category_page">


    <?php if(isset($column_left) && $column_left != '') { ?>
    <?php echo $column_left; ?>
    <?php } ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right || $ajax) { ?>
    <?php $class = 'col-sm-10'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-10'; ?>
    <?php } ?>
    <div id="content" class="content_panel <?php echo $class; ?>" >
    <div class="content_panel_box">
      <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  
    <?php echo $content_top; ?>
      <?php echo $sorts_list;?>
      <div class="clear"></div>
      <div id="selected_filters" class="selected_filters_box"></div>
      <?php if ($products) { ?>

      <div class="col-sm-12 <?php echo $store_class; ?> content_panel" id="list_container">

        <?php echo $product_list; ?>

      </div>
      <?php if ($current_page < $total_pages) {
        $display_browse_more = 'display:block;';
      } else {
        $display_browse_more = 'display:none;';
      } ?>
      <div class="clear"></div>
      <div class="browse_more" style="<?php echo $display_browse_more; ?>">
        <button type="button" class="btn_browse_more btn btn-primary" data-page="<?php echo $current_page;?>" data-total-pages="<?php echo $total_pages;?>" path_no="<?php echo $current_page_path;?>" data-search="<?php echo $search;?>">Browse More</button>
      </div>


      <?php } else { ?>
      <div class="row <?php echo $store_class; ?>" id="list_container">
      <p><?php echo $text_empty; ?></p>
       </div>
      <?php } ?>
      <?php echo $content_bottom; ?>
    <?php echo $column_right; ?></div></div>
</div>

<div class="container" id="product_description_box"></div>
<input type="hidden" name="handpicked_ids" value="<?php echo $handpicked_ids; ?>" />
<input type="hidden" name="random_string" value="<?php echo $random_string; ?>" />
<input type="hidden" name="product_total" value="<?php echo $product_total; ?>" />
<input type="hidden" id="store_code" value="<?php echo $store_code; ?>" />
<input type="hidden" id="is_exclusive" value="<?php echo $is_exclusive; ?>" />

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

  sorting = '';

  $(document).ready(function () {
    var hash = window.location.hash;
    var spliting = hash.split("&");

    if(window.location.hash) {

      filter_arr_str = spliting[0].split("=")[1];
      arr_filter_data = filter_arr_str.split(",");

      if(arr_filter_data.length > 0) {
        $.each(arr_filter_data, function (index, value) {
          $filter_id = $("input#filter" + value);
          $filter_id.prop("checked", true);

        });
      }

      price_filtering = spliting[1].split("=")[1];
      $('#amount_hidden').val(price_filtering);

      if(typeof spliting[3] != 'undefined') {
        option_str = spliting[2].split('=')[1] ;
        option_filter_data = option_str.split(',') ;
        if(option_filter_data.length > 0) {
          $.each(option_filter_data, function (index, value) {
            $option_id = $("input#option" + value);
            $option_id.prop("checked", true);
          });
        }
      }

      if(typeof spliting[4] != 'undefined') {
        sorting = spliting[3].split('=')[2] + "&" + spliting[4];
      }

      var get_rating_filter = getUrlParameter('rating_filter',hash);
      $('#rating_filter_'+get_rating_filter).prop('checked',true);

      var get_stock_filter = getUrlParameter('stock_filter',hash);
      $('#stock_filter_'+get_stock_filter).prop('checked',true);

      eventType = 'filter';
      callAjaxToFilter(eventType);
    }

    $(document).delegate("input[name^='filter']", "click", function() {
      jQuery.fancybox.close();
      eventType = 'filter';
      callAjaxToFilter(eventType);
    });
    $(document).delegate("input[name^='option']", "click", function() {
      jQuery.fancybox.close();
      eventType = 'filter';
      callAjaxToFilter(eventType);
    });

    $(document).delegate("input[name^='rating_filter']", "click", function() {
      jQuery.fancybox.close();
      eventType = 'filter';
      callAjaxToFilter(eventType);
    });
    $(document).delegate("input[name^='stock_filter']", "click", function() {
      jQuery.fancybox.close();
      eventType = 'filter';
      callAjaxToFilter(eventType);
    });
    $(document).delegate("#input-sort li a", "click", function() {
      sorting = $(this).attr('data-value');
      jQuery.fancybox.close();
      eventType = 'filter';

      $('ul#input-sort li').removeClass('active');
      $(this).parent('li').addClass('active');
      callAjaxToFilter(eventType);
    });

    $(document).delegate('.btn_browse_more', 'click', function(){
      var objThis = $(this);
      eventType = 'pagination';
      callAjaxToFilter(eventType);

    });
    //Remove item from selected filter list
    $(document).delegate("span.filter", "click", function() {
      sorting = $(this).attr('data-value');
      jQuery.fancybox.close();
      eventType = 'filter';

      callAjaxToFilter(eventType, $(this));
    });

    // Default dropdown action to show/hide dropdown content
    $('.js-dropp-action').click(function(e) {
      e.preventDefault();
      $(this).toggleClass('js-open');
      $(this).parent().next('.dropp-body').toggleClass('js-open');
    });

    // Using as fake input select dropdown -> this is being used to show stocks dropdown
    // in sort list
    $('label').click(function() {
      $(this).addClass('js-open').siblings().removeClass('js-open');
      $('.dropp-body,.js-dropp-action').removeClass('js-open');
    });
    // get the value of checked input radio and display as dropp title
    $('input[name="stock_filter[]"]').change(function() {
      var value = $("input[name='stock_filter[]']:checked").val();
      if(value == 1){
        stock_text = 'All stock';
      }else{
        stock_text = 'In stock';
      }
      $('.js-value').text(stock_text);
    });

  });
  /**
   *  @param eventTYpe pagination or null
   *  @param objThis object of current clicked dom element
   */
  function callAjaxToFilter() {
    if (arguments.length > 0) {
      eventType = arguments[0];
    } else {
      eventType = '';
    }

    filter = [];
    option = [];
    rating_filter = [];
    stock_filter = [];
    handpicked_id_query_string = '';

    if (eventType == 'pagination') {
      var current_page = parseInt($('.btn_browse_more').attr('data-page'));
      var total_pages = parseInt($('.btn_browse_more').attr('data-total-pages'));

      if (isNaN(current_page) == true) {
        current_page = 1;
      }

      if (current_page + 1 == total_pages || current_page == total_pages) {
         $(this).hide();
      }
      var page_to_open = current_page + 1;

    }

    handpicked_id_query_string += '&handpicked_ids='+$('input[name="handpicked_ids"]').val();

    handpicked_id_query_string += '&random_string='+$('input[name="random_string"]').val();

    handpicked_id_query_string += '&product_total='+$('input[name="product_total"]').val();
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
    $('input[name^=\'stock_filter\']:checked').each(function (element) {
      if (stock_filter.indexOf(this.value) < 0) stock_filter.push(this.value);
      //filter.push(this.value);
    });
    price_filter_script = $('#amount_hidden').val();
    store_code = $('#store_code').val();
    is_exclusive = $('#is_exclusive').val();

    //Code to remove selected filter
    if (arguments.length > 1) {
      objThis = arguments[1];

      if(objThis.attr('data-id') == 'all'){
        filter = [];
        $('input[name^=\'filter\']').attr('checked',false);
        option = [];
        $('input[name^=\'option\']').attr('checked',false);

      }else{

        filter.splice($.inArray(objThis.attr('data-id'), filter),1);
        $('input#filter'+objThis.attr('data-id')).attr('checked',false);
        option.splice($.inArray(objThis.attr('data-id'), option),1);
        $('input#option'+objThis.attr('data-id')).attr('checked',false);

      }

    }
    addFilterInHistoryState(filter,price_filter_script,option,sorting,rating_filter,stock_filter,store_code,is_exclusive);
    search = '';
    path = '';
    category_id = '';
    <?php if(isset($search) && $search != '') { ?>
              search = '<?php echo $search; ?>';
    <?php } ?>
    <?php if(isset($category_id) && $category_id != '') { ?>
      category_id = '<?php echo $category_id; ?>';
    <?php } ?>
    <?php if(isset($path) && $path != '') { ?>
      path = '<?php echo $path; ?>';
    <?php } ?>

    $.ajax({
      url: 'index.php?route=product/search',
      type: 'get',
      data: 'search='+search+'&category_id='+category_id+'&path='+path+'&post_type=ajax&filter=' + filter.join(',') +
            '&price_filter=' + price_filter_script +'&option='+ option.join(',')+ "&sort="+sorting+
            '&rating_filter='+ rating_filter.join(',')+'&stock_filter='+ stock_filter.join(',')+'&store_code='+store_code+'&is_exclusive='+is_exclusive+
            '&page='+page_to_open+handpicked_id_query_string,
      dataType: 'json',
      beforeSend: function () {
        $('body').removeClass('loaded').addClass('loading');
        $('.filter_box').addClass('hidden-xs');

      },
      complete: function () {
        $('body').removeClass('loading').addClass('loaded');

      },
      success: function (data) {
        $('body').removeClass('loading').addClass('loaded');
        if (eventType == 'pagination') {
          $('#results').append(data['product_list']);
        } else {
          $('#list_container').html(data['product_list']);
        }
        //$('#column-right').html(data['coulmn_left']);
        //$('#search_result_heading').html(data['search_result_heading']);
        $('#product_total').html(data['total_search_result']);
        $('#selected_filters').html(data['selected_filters']);
        if(data['stock_filter'] == 1){
          stock_text = 'All stock';
        }else{
          stock_text = 'In stock';
        }
        $('.js-value').text(stock_text);


        if ((!isNaN(current_page) && current_page + 1  == total_pages) || data['total_search_result'] < 1 ) {

          $('.browse_more').hide();
        }else{
          if(isNaN(current_page)){
            current_page = 0;
          }
          $('.browse_more').show();
          $('.btn_browse_more').attr('data-page',current_page+1);
        }

        //set handpicked hidden fileds
        $('input[name="handpicked_ids"]').val(data['handpicked_ids']);
        $('input[name="random_string"]').val(data['random_string']);
        $('input[name="product_total"]').val(data['product_total']);
        $('.btn_browse_more').attr('data-total-pages',data['total_pages']);
      }
    });
  }


  function addFilterInHistoryState(filter,price_filter,option,sorting,rating_filter,stock_filter,store_code,is_exclusive){

    if (window.location.hash) {
      var url_page = window.location.protocol + "//" + window.location.host;
      if(store_code == '' || is_exclusive == 0){
        url_page += window.location.pathname+'?route=product/search';
      }else{
        url_page += window.location.pathname;
      }

      var search = getParameterByName('search');
      if (search == '' || search == null ) {
        search = '';
      }else{
        search = '&search='+search;
      }
      var category_id = getParameterByName('category_id');
      if (category_id == '' || category_id == null ) {
        category_id = '';
      }else{
        category_id = '&category_id='+category_id;
      }
      filter_string = url_page + search + category_id +"#!filter=" + filter.join(',') + "&price_filter=" + price_filter +"&option="+ option.join(',')+ "&sort="+sorting+"&rating_filter="+ rating_filter.join(',')+"&stock_filter="+ stock_filter.join(',')+"&store_code="+store_code+"&is_exclusive="+is_exclusive;
      
    } else {
      filter_string = window.location + "#!filter=" + filter.join(',') + "&price_filter=" + price_filter + "&option=" +option.join(',')+"&sort="+sorting  + "&rating_filter=" +rating_filter.join(',')+ "&stock_filter=" +stock_filter.join(',')+"&store_code="+store_code+"&is_exclusive="+is_exclusive;
    }

    history.pushState(null, null, filter_string)
  }

  function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  }
  //-->
</script>
 <script type="text/javascript">
  $(document).ready(function(){
    $('.addtowishlist').click(function(){
      var product_id = $(this).attr('data-product-id');
      $('#wishlist_heart_'+product_id).removeClass('fa fa-heart-o').addClass('fa fa-heart custom_heart');
    });
  });
</script>

<?php echo $footer; ?>
