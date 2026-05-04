<div class="panel_new desktop_only" id="filter_bar" >
    <div class="row">

      <div class="col-md-12 col-sm-12 col-xs-6">
        <div class="discount_box">
            <div class="discount_text"><i class="fa fa-tag"></i> <?php echo $text_discount_on_order;?></div>
            <div class="discount_text" style="padding-top:5px"><i class="fa fa-tag"></i> <?php echo $text_discount_above_order;?></div>
        </div>
        <!-- <div class="panel-heading filter_click" ><?php echo $heading_title; ?></div> -->

          <div class="filter_box">
              <div class="list-group" id="list-group-filter">
              <div class="filter_penal_box">
                  <a class="list-group_title">Rating</a>
                  <div class="list-group-item_new">
                      <div class="filter-group">
                          <div class="rating radio rating_filter">

                              <label>
                                <input id="rating_filter_5" type="radio" name="rating_filter[]" value="5" style="top:2px"/>
                                  <span class='label label-success' style='padding:4px; margin:3px 0px; font-size: 12px;'>Excellent Quality</span>
                              </label>
                              <label>
                                  <input id="rating_filter_4" type="radio" name="rating_filter[]" value="4" style="top:2px"/>
                                  <span class='label label-warning' style='padding:4px; margin:3px 0px; font-size: 12px;'>Good Quality</span>
                              </label>
                              <label>
                                  <input id="rating_filter_3" type="radio" name="rating_filter[]" value="3" style="top:2px"/>
                                  <span class='label label-danger' style='padding:4px; margin:3px 0px; font-size: 12px;'>Average Quality</span>
                              </label>

                              <!--<?php for ($i = 5; $i >=3; $i--) { ?>
                                <label>
                                  <input id="rating_filter_<?php echo $i; ?>" type="radio" name="rating_filter[]" value="<?php echo $i; ?>"/>
                                    <?php for ($j = 1; $j <=$i; $j++) { ?>
                                    <span class="fa fa-stack">
                                      <i class="fa fa-star fa-stack-1x"></i>
                                      <i class="fa fa-star-o fa-stack-1x"></i>
                                    </span>
                                    <?php } ?>
                                </label>
                                  <?php } ?> --><br/>
                              <label><input id="rating_filter_all" type="radio" name="rating_filter[]" value="all" style="top:0px"/> <strong>All</strong></label>
                          </div>
                      </div>
                  </div>
                  </div>
              </div>
          </div>
		<?php 
			if($price_filter_show == 1){ 
		?>
			<div class="filter_box">
				<div class="list-group" id="list-group-filter">
        <div class="filter_penal_box">
				  <a class="list-group_title ">Price Range Filter</a>
				  <div class="list-group-item_new">
					<div class="filter-group">
						  <p>
							<label for="amount_lable" id="amount_lable" style="/*color:#000; font-weight:bold; font-size: 14px;*/"></label>
							<input type="hidden" id="amount_hidden" readonly size="30" name="price_filter[]" value="">
						  </p>
						  <div id="slider-range1"></div>                    
					</div>
				  </div>
          </div>

				</div>
			  </div>
		<?php 
			} else{
			?>
			<!--<input type="hidden" id="amount_hidden" readonly size="30" name="price_filter[]" value="<?php echo $price_filter_option["minimum_price"];?>-<?php echo $price_filter_option["maximum_price"];?>">-->
			<input type="hidden" id="amount_hidden" readonly size="30" name="price_filter[]" value="<?php echo floor($price_with_currency["minimum_price"]);?>-<?php echo ceil($price_filter_option["maximum_price"]);?>">
			<?php
			}
		?>

        <div class="filter_box">
          <div class="list-group" id="list-group-option">
            <?php if (isset($option_groups)) { ?>
            <?php foreach ($option_groups as $option_group) { ?>
            <div class="filter_penal_box">
            <a class="list-group_title lg_title_<?php echo $option_group['option_group_id']; ?>"><?php echo $option_group['name']; ?></a>
            <div class="list-group-item_new lgi_<?php echo $option_group['option_group_id']; ?>">
              <div id="option-group<?php echo $option_group['option_group_id']; ?>">
                <!-- <div class="filter_group_description"><?php echo $filter_group['description']; ?></div> -->
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
            </div>
            <?php } ?>
            <?php }?>
          </div>
          <!-- <div class="panel-footer text-right">
         <button type="button" id="button-filter" class="btn btn-primary"><?php echo $button_filter; ?></button>
        </div>-->
        </div>
        <div class="filter_box">
          <div class="list-group" id="list-group-filter">
            <?php foreach ($filter_groups as $filter_group) { ?>
            <div class="filter_penal_box">
            <a class="list-group_title fg_title_<?php echo $filter_group['filter_group_id']; ?>"><?php echo $filter_group['name']; ?></a>
            <div class="list-group-item_new lgi_<?php echo $filter_group['filter_group_id']; ?>">
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
            </div>
            <?php } ?>
          </div>
          <!-- <div class="panel-footer text-right">
         <button type="button" id="button-filter" class="btn btn-primary"><?php echo $button_filter; ?></button>
        </div>-->
        </div>       

      </div>

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
      if (filter.indexOf(this.value) < 0) filter.push(this.value);
      //filter.push(this.value);
    });
  });
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

      callAjaxToFilter('1');
    }

    $("input[name^='filter']").click(function() {
      jQuery.fancybox.close();
       callAjaxToFilter();
    });
    $("input[name^='option']").click(function() {
      jQuery.fancybox.close();
       callAjaxToFilter();
    });

    $("input[name^='rating_filter']").click(function() {
        jQuery.fancybox.close();
        callAjaxToFilter();
    });

    /*$("#input-sort").change(function() {
      jQuery.fancybox.close();
       callAjaxToFilter();
    });*/



//$("input[name^='filter']").trigger("click");
  });
 
  function callAjaxToFilter() { 
    filter = [];
    option = [];
    rating_filter = [];

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

    addFilterInHistoryState(filter,price_filter_script,option,sorting,rating_filter);

    $.ajax({
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
    // alert(window.location.hash);
    if (window.location.hash) {
      var url_page = window.location.protocol + "//" + window.location.host + window.location.pathname;
      filter_string = url_page + "#!filter=" + filter.join(',') + "&price_filter=" + price_filter +"&option="+ option.join(',')+ "&sort="+sorting+"&rating_filter="+ rating_filter.join(',');
    } else {
      filter_string = window.location + "#!filter=" + filter.join(',') + "&price_filter=" + price_filter + "&option=" +option.join(',')+"&sort="+sorting  + "&rating_filter=" +rating_filter.join(',');
    }

    history.pushState('<?php echo $path;?>', null, filter_string)
  }
//-->
</script>
		<?php
			if($price_filter_show == 1){
		?>

<script type="text/javascript">

var price_filter_script='';
  $(function() {
    //var type = window.location.hash.substr(1);

    var hash = window.location.hash;
    //alert(hash);
    if(hash == "" || hash == null){
       var val1 = "<?php echo (isset($get_url_price_filter[0])?$get_url_price_filter[0]:floor($price_with_currency['minimum_price'])); ?>";
        var val2 = "<?php echo (isset($get_url_price_filter[1])?$get_url_price_filter[1]:ceil($price_with_currency['maximum_price'])); ?>";
    }else{
      var spliting = hash.split("&");
      var price_filtering =spliting[1].split("=")[1];
      var getactialVal = price_filtering.split("-");
      if((getactialVal[0] != "" || getactialVal[0] == null) && (getactialVal[1] != "" || getactialVal[1] == null)) {
        var val1 = getactialVal[0];
        var val2 = getactialVal[1];
      }else{
        var val1 = "<?php echo (isset($get_url_price_filter[0])?$get_url_price_filter[0]:floor($price_with_currency['minimum_price'])); ?>";
        var val2 = "<?php echo (isset($get_url_price_filter[1])?$get_url_price_filter[1]:ceil($price_with_currency['maximum_price'])); ?>";

      }
    }


    $( "#slider-range1" ).slider({
      range: true,
      min: <?php echo floor($price_with_currency['minimum_price']);?>,
      max: <?php echo ceil($price_with_currency['maximum_price']);?>,
      values: [val1, val2],
      slide: function( event, ui ) {
        $( "#amount_lable" ).text( "<?php echo $price_with_currency['symbol'];?> "+ui.values[ 0 ]+" - <?php echo $price_with_currency['symbol'];?>  "+ui.values[ 1 ] );
        $( "#amount_hidden" ).val( ui.values[ 0 ]+"-"+ ui.values[ 1 ] );
      },
      change: function(e,ui) { 
          //price_filter_script = $('#amount_hidden').val();
          jQuery.fancybox.close();
          callAjaxToFilter();
    }
    });
    $( "#amount_lable" ).text( "<?php echo $price_with_currency['symbol'];?>  " + $( "#slider-range1" ).slider( "values", 0 ) +
      " -  <?php echo $price_with_currency['symbol'];?> " + $( "#slider-range1" ).slider( "values", 1 ) );

    $( "#amount_hidden" ).val($( "#slider-range1" ).slider( "values", 0 ) +
      "-"+$( "#slider-range1" ).slider( "values", 1 ) );
    $( "#slider-range1 .ui-slider-range" ).css('background', '#133596');
    
  });
</script>
<?php } ?>
