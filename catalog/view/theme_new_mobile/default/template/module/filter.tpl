<div class="panel panel-default mobileonly fillter_panel" id="filter_bar" >

  <div class="row nomargin-mobileonly">
    <div class="sort_by col-xs-6 hidden-md hidden-sm mobileonly">
      <div class="panek-heading sort_by_click"><a id="sortby" href="#sort_by_options">Sort by</a></div>
      <div style="display: none;">
        <div id="sort_by_options">
          <?php //echo $sort . '-' . $order. '<pre>'; print_r($sorts); echo '</pre>'; ?>
          <ul >
            <?php foreach ($sorts as $sorts) { ?>
            <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
            <li><a href="javascript:void(0);" data-sorting-value="<?php echo $sorts['query_string']; ?>" class="selected sorting"><?php echo $sorts['text']; ?></a></li>
            <?php } else { ?>
            <li><a href="javascript:void(0);" data-sorting-value="<?php echo $sorts['query_string']; ?>" class="sorting"><?php echo $sorts['text']; ?></a></li>
            <?php } ?>
            <?php } ?>
          </ul>
        </div>
      </div>

    </div>
    <div class="col-md-12 col-sm-6 col-xs-6">

      <div class="panel-heading filter_click_mobile" ><span id="filter_option" href=""><?php echo $heading_title; ?></span></div>
    </div>

  </div>


</div>
<?php /* ?>
<div class="row nomargin-mobileonly">
  <div class="single-store-btn hdbtn hidden-sm hidden-md col-xs-6 mobileonly"><a href="javascript:void(0);" id="store_switch"><?php if (isset($this->session->data['custom_store'])) { ?>
        <?php if ($this->session->data['custom_store'] == 'single') {
        echo "WHOLESALE SET STORE";}
       else {
          echo "SINGLES STORE";
        } ?>
    <?php }else { echo "SINGLES STORE";} ?></a>
  </div>
</div>

<?php */ ?>


<div class="filter_container" id="filter_container">
  <div class="apply_filters">
    <div class="clear-all pull-left">
      <span>Clear All</span>
    </div>
    <div class="apply-selected pull-right">
      <span>Apply</span>
    </div>
  </div>



  <div class="filter_box">
    <div class="list-group" id="list-group-filter">
      <div class="filter_head">
        <span class="pull-left">Filters</span><span class="pull-right close_filters"><i class="fa fa-times"></i></span>
      </div>
      <div style="height: 40px;"></div>
      <!-- start Rating -->
      <a class="list-group-item ">Rating </a>
      <div class="list-group-item">
        <div class="filter-group">
          <div class="filter_group_description1">
            <div class="rating radio rating_filter">
              <?php /*for ($i = 5; $i >=3; $i--) { ?>
              <label>
                <input id="rating_filter_<?php echo $i; ?>" type="radio" name="rating_filter[]" value="<?php echo $i; ?>"/>
                <?php for ($j = 1; $j <=$i; $j++) { ?>
                  <span class="fa fa-stack">
                    <i class="fa fa-star fa-stack-1x"></i>
                    <i class="fa fa-star-o fa-stack-1x"></i>
                  </span>
              </label>
              <?php } */?>  
              
              <label>
                  <input id="rating_filter_5" type="radio" name="rating_filter[]" value="5"/> 
                  <span class="label rating_span label-success">Excellent Quality</span> 
              </label>
              <label>
                  <input id="rating_filter_4" type="radio" name="rating_filter[]" value="4"/> 
                  <span class="label rating_span label-warning">Good Quality</span>
              </label>
                <?php if($this->config->get('config_store_id') != INTERNATIONAL_STORE_ID){ ?>
              <label>
                  <input id="rating_filter_3" type="radio" name="rating_filter[]" value="3"/> 
                  <span class="label rating_span label-danger">Average Quality</span>
              </label>
                <?php } ?>
              <label>
                  <input id="rating_filter_nothing" type="radio" name="rating_filter[]" value="nothing"/>
                  <span class="label rating_span label-default">All</span> 
              </label>
            </div>
          </div>
        </div>
      </div>
      <!-- END  Rating-->

      <!--start Price Rnage filter -->
      <a class="list-group-item ">Price Range Filter</a>
      <div class="list-group-item">
        <div class="filter-group">
          <input type="text" id="min_text_val" name="price_filter[]" size="7" placeholder="Min" maxlength="4">
          <span>TO</span>
          <input type="text" id="max_text_val" name="price_filter[]" size="7" placeholder="Max" maxlength="4">
          <button type="button" id="button_price_search" class="button_price_search btn btn-default btn-xs">GO</button>
          <div class="price_error_show"><span class="price_error"></span></div>
        </div>
      </div>
      <!--End  Price Rnage filter -->
      <?php if (isset($option_groups)) { ?>
      <?php foreach ($option_groups as $option_group) { ?>

      <a class="list-group-item fg_title_<?php echo $option_group['option_group_id']; ?>"><?php echo $option_group['name']; ?></a>
      <div class="list-group-item lgi_<?php echo $option_group['option_group_id']; ?>">
        <div id="filter-group<?php echo $option_group['option_group_id']; ?>">
          <!-- <div class="filter_group_description"><?php echo $option_group['description']; ?></div> -->
          <?php foreach ($option_group['option'] as $option) { ?>
          <div class="checkbox">
            <label>
              <?php if (in_array($option['option_value_id'], $option_category)) { ?>
              <input type="checkbox" id="option<?php echo $option['option_value_id'];?>" name="option[]" value="<?php echo $option['option_value_id']; ?>" checked="checked" />
              <?php echo $option['name']; ?>
              <?php } else { ?>
              <input type="checkbox" id="option<?php echo $option['option_value_id'];?>" name="option[]" value="<?php echo $option['option_value_id']; ?>" />
              <?php echo $option['option_value']; ?>
              <?php } ?>
            </label>
          </div>
          <?php } ?>
        </div>
      </div>
      <?php } ?>
      <?php } ?>
      <?php foreach ($filter_groups as $filter_group) { ?>
      <a class="list-group-item fg_title_<?php echo $filter_group['filter_group_id']; ?>"><?php echo $filter_group['name']; ?></a>
      <div class="list-group-item lgi_<?php echo $filter_group['filter_group_id']; ?>">
        <div id="filter-group<?php echo $filter_group['filter_group_id']; ?>">
          <div class="filter_group_description"><?php echo $filter_group['description']; ?></div>
          <?php foreach ($filter_group['filter'] as $filter) { ?>
          <div class="checkbox">
            <label>
              <?php if (in_array($filter['filter_id'], $filter_category)) { ?>
              <input type="checkbox" id="filter<?php echo $filter['filter_id'];?>" name="filter[]" value="<?php echo $filter['filter_id']; ?>" checked="checked" />
              <?php echo $filter['name']; ?>
              <?php } else { ?>
              <input type="checkbox" id="filter<?php echo $filter['filter_id'];?>" name="filter[]" value="<?php echo $filter['filter_id']; ?>" />
              <?php echo $filter['name']; ?>
              <?php } ?>
            </label>
          </div>
          <?php } ?>
        </div>
      </div>
      <?php } ?>
    </div>
    <!-- <div class="panel-footer text-right">
   <button type="button" id="button-filter" class="btn btn-primary"><?php echo $button_filter; ?></button>
  </div>-->
  </div>
</div>

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
      filter.push(this.value);
    });

    //location = '<?php echo $action; ?>&filter=' + filter.join(',');


  });
  price_filter ='';
  sorting = '';

  $(document).ready(function () { 
     
    var is_custom_url = <?php echo $is_custom_url; ?>;
    
    if(is_custom_url == '1'){ 
        var hash = "'" + <?php echo $hash_value; ?> + "'";
        setFilterData(hash);
            
    }else{ 
        if(window.location.hash) {
          var hash = window.location.hash;
          setFilterData(hash);
        }
    }
    
    function setFilterData(hash){
        var spliting = hash.split("&");
          filter_arr_str = spliting[0].split("=")[1];
          arr_filter_data = filter_arr_str.split(",");


          if(arr_filter_data.length > 0) {
            $.each(arr_filter_data, function (index, value) {
              $filter_id = $("input#filter" + value);
              $filter_id.prop("checked", true);
            });
          }

          price_filter = spliting[1].split("=")[1];
          $('#min_text_val').val(price_filter.split("-")[0]);
          $('#max_text_val').val(price_filter.split("-")[1]);

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

          callAjaxToFilter('1');
    }

//    $("input[name^='filter']").click(function() {
//      jQuery.fancybox.close();
//      callAjaxToFilter();
//    });

    $(".apply-selected").click(function() {
      $('.filter_container').slideUp('slow');
      callAjaxToFilter(1);
    });

//    $("input[name^='option']").click(function() {
//      //jQuery.fancybox.close();
//      callAjaxToFilter();
//    });


    $('#button_price_search').click(function(){
      var min_value = $('#min_text_val').val();
      var max_value = $('#max_text_val').val();

      if((min_value=='')){
        $('.price_error').html('Enter the minimum value.');
      }else if((min_value <= 0)){
        $('.price_error').html('Enter the minimum value greater than 1 Rs.');
      }else if(max_value==''){
        max_value = 0;
        price_filter = min_value+'-'+max_value;
        $('.price_error').html(' ');
        jQuery.fancybox.close();
        $('.filter_container').slideUp('slow');
        callAjaxToFilter('1');
      }else if(parseInt(max_value) <= parseInt(min_value)){
        $('.price_error').html('Enter maximum value greater than minimum value.');
      }else{
        price_filter = (min_value)+'-'+(max_value);
        $('.price_error').html(' ');
        jQuery.fancybox.close();
        $('.filter_container').slideUp('slow');
        callAjaxToFilter('1');
      }
    });

    $('.sorting').click(function(){
      sorting = ($(this).attr('data-sorting-value') ? $(this).attr('data-sorting-value') : " ");
      jQuery.fancybox.close();
      callAjaxToFilter();
    });

    /*$("input[name^='rating_filter']").click(function() {
      jQuery.fancybox.close();
      $('.filter_container').slideUp('slow');
      callAjaxToFilter();
    });*/

  });

  //location = '<?php echo $action; ?>&filter=' + filter.join(',');
  function callAjaxToFilter(uncheck) {
    filter = [];
    option = [];
    rating_filter = [];

    $('input[name^=\'filter\']:checked').each(function (element) {
      filter.push(this.value);
    });
    $('input[name^=\'option\']:checked').each(function (element) {
      option.push(this.value);
    });
    $('input[name^=\'rating_filter\']:checked').each(function (element) {
      rating_filter.push(this.value);
      //filter.push(this.value);
    });
    
    var is_custom_url = <?php echo $is_custom_url; ?>;
    
    var url    = window.location.href;
    //change url for custom url
    if(is_custom_url == '1'){ 
        var url = "'" + <?php echo $hash_value; ?> + "'";
    }
   
    var get_sorting = getUrlParameter('sort',url)+'&order='+getUrlParameter('order',url);
    if(uncheck == 1 ){
      sorting = get_sorting;
    }
    
    //nedd only for default original url 
    if(is_custom_url == '0'){ 
        addFilterInHistoryState(filter,price_filter,option,sorting,rating_filter);
    }
    
    $.ajax({
      url: 'index.php?route=product/category',
      type: 'get',
      data: 'path=<?php echo $path;?>&post_type=ajax&filter=' + filter.join(',')+"&price_filter="+price_filter+'&option='+ option.join(',')+"&sort="+sorting+'&rating_filter='+rating_filter.join(','),
      dataType: 'html',
      beforeSend: function () {
        $('body').removeClass('loaded').addClass('loading');
        //$('.filter_box').addClass('hidden-xs');

      },
      complete: function () {
        $('body').removeClass('loading').addClass('loaded');

      },
      success: function (data) {

        $('#content').html(data);

      }
    });
  }

  function addFilterInHistoryState(filter,price_filter,option,sorting,rating_filter){

    if (window.location.hash) {
      var url_page = window.location.protocol + "//" + window.location.host + window.location.pathname;

      filter_string = url_page + "#!filter=" + filter.join(',')+"&price_filter="+price_filter+'&option='+ option.join(',')+"&sort="+sorting+'&rating_filter='+ rating_filter.join(',');
    } else {
      filter_string = window.location + "#!filter=" + filter.join(',')+"&price_filter="+price_filter+'&option='+ option.join(',')+"&sort="+sorting+'&rating_filter='+ rating_filter.join(',');
    }

    history.pushState('<?php echo $path;?>', null, filter_string)
  }

//--></script>
<script type="text/javascript">
  //script for home page popup
  $('#store_switch').on('click', function (argument) {
      $.ajax({
        url : 'index.php?route=common/header/getStoreSwitchNew',
        dataType: 'json',

        success: function (json) {
          location.reload();
        },

        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }

      });
  });
</script>

<script type="text/javascript">
  $(document).ready(function() {
    $('.filter_click_mobile').click(function () {
      var heights = screen.height;
      var height = heights-63;
      $("#filter_container").css({"height": height+"px", "display":"block", "position":"absolute", "z-index":"1", "border-top":"1px solid #ccc", "top":"0" });
    });

    $('.close_filters').click(function(){
      $('#filter_container').toggle('slow');
    });

    $('.clear-all').click(function(){
      $('input:checkbox').removeAttr('checked','checked');
      $(this).val('uncheck all')
    });

  });

</script>
