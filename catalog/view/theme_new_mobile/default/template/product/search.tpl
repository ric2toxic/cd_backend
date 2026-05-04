<?php echo $header; ?>
<div class="container">

  <div class="row">

    <div id="content" class="col-sm-12 category_page ">

        <div class="row"><?php echo $column_left; ?>
            <?php if ($column_left && $column_right) { ?>
            <?php $class = 'col-sm-6'; ?>
            <?php } elseif ($column_left || $column_right) { ?>
            <?php $class = 'col-sm-9'; ?>
            <?php } else { ?>
            <?php $class = 'col-sm-12'; ?>
            <?php } ?>
            <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
                <h1><?php echo $heading_title; ?></h1>
                <label class="control-label" for="input-search"><?php echo $entry_search; ?></label>
                <?php if($hide_price == 0)
                { ?>
                <div class="row">
                    <div class="col-sm-4">
                        <input type="text" name="search" value="<?php echo $search; ?>" placeholder="<?php echo $text_keyword; ?>" id="input-search" class="form-control" />
                    </div>
                    <br />
                    <div class="col-sm-3">
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
                    <div class="col-sm-3">
                        <label class="checkbox-inline">
                            <?php if ($sub_category) { ?>
                            <input type="checkbox" name="sub_category" value="1" checked="checked" />
                            <?php } else { ?>
                            <input type="checkbox" name="sub_category" value="1" />
                            <?php } ?>
                            <?php echo $text_sub_category; ?></label>
                    </div>
                </div>
              
                <!-- <p>
                  <label class="checkbox-inline">
                    <?php if ($description) { ?>
                    <input type="checkbox" name="description" value="1" id="description" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="description" value="1" id="description" />
                    <?php } ?>
                    <?php echo $entry_description; ?></label>
                </p> --> </br>
                <input type="button" value="<?php echo $button_search; ?>" id="button-search" class="btn btn-primary search_btn_color" />
                  <?php } ?>
                <h2><?php echo $text_search; ?></h2>

      <?php if ($products) { ?>
      <div class="row <?php echo $store_class; ?>  nomargin_top" id="list_container">
        <?php echo $content_top; ?>

        <row>

        <?php if($hide_price == 0)
                { ?>
          <div class="col-sm-1 col-sm-offset-2" >
            <label class="control-label" for="input-sort"><?php echo $text_sort; ?></label>
          </div>
        <div class="col-sm-3 text-right">
          <select id="input-sort" class="form-control col-sm-3" onchange="location = this.value;">
            <?php foreach ($sorts as $sorts) { ?>
            <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
            <option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
            <?php } ?>
            <?php } ?>
          </select>
        </div>


        <div class="col-sm-2">
            <label class="control-label" for="input-rating">Rating</label>
            <select name="rating_filter" class="form-control" onchange="location = this.value;">
                <?php foreach ($ratings as $ratings) { ?>
                <?php if ($ratings['value'] == $rating_filter) { ?>
                <option value="<?php echo $ratings['href']; ?>" selected="selected"><?php echo $ratings['text']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $ratings['href']; ?>"><?php echo $ratings['text']; ?></option>
                <?php } ?>
                <?php } ?>
            </select>
        </div>
         <?php } ?>

      </row>
        <br />



        <?php echo $product_list; ?>

        <div id="results" class="item"></div>
      </div>

      <div class="browse_more">
        <button type="button" class="btn_browse_more btn btn-primary" data-page="<?php echo $current_page;?>" data-total-pages="<?php echo $total_pages;?>" path_no="<?php echo $current_page_path;?>" data-search="<?php echo $search;?>">Browse More</button>
      </div>
      <?php /* ?>
      <div class="row custom-pagination-class">
        <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
        <div class="col-sm-6 text-right"><?php echo $results; ?></div>
      </div>
      <?php */ ?>
      <?php } else { ?>
      <p><?php echo $text_empty; ?></p>
      <?php } ?>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<div class="container">
    <div class="row">
        <div class=" col-xs-12 text-center">
            <br />
            <?php echo $description; ?>
        </div>
    </div>
</div>      
<script type="text/javascript"><!--
$('#button-search').bind('click', function() {
	url = 'index.php?route=product/search';

	var search = $('#content input[name=\'search\']').prop('value');

	if (search) {
		url += '&search=' + encodeURIComponent(search);
	}

	var category_id = $('#content select[name=\'category_id\']').prop('value');

	if (category_id > 0) {
		url += '&category_id=' + encodeURIComponent(category_id);
	}

	var sub_category = $('#content input[name=\'sub_category\']:checked').prop('value');

	if (sub_category) {
		url += '&sub_category=true';
	}

	var filter_description = $('#content input[name=\'description\']:checked').prop('value');

	if (filter_description) {
		url += '&description=true';
	}

	location = url;
});

$('#content input[name=\'search\']').bind('keydown', function(e) {
	if (e.keyCode == 13) {
		$('#button-search').trigger('click');
	}
});

$('select[name=\'category_id\']').on('change', function() {
	if (this.value == '0') {
		$('input[name=\'sub_category\']').prop('disabled', true);
	} else {
		$('input[name=\'sub_category\']').prop('disabled', false);
	}
});

$('select[name=\'category_id\']').trigger('change');
--></script>
<script type="text/javascript">
  var sorting = '<?php echo (isset($sort)? $sort :'');?>';
  var ordering = '<?php echo (isset($order)? $order :'');?>';
  $(document).ready(function() {

    $('.btn_browse_more').click(function(){
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

          var getParams = "post_type=ajax_pagination";
          if(parseInt($(this).attr('path_no')) > 0){
            getParams += "&category_id= "+(parseInt($(this).attr('path_no')));
          }
          getParams += "&search="+$(this).attr('data-search')+"&page=" + (parseInt($(this).attr('data-page')) + 1)+
          "&sort="+sorting+"&order="+ordering;

          $.ajax({
            type: 'get',
            url: 'index.php?route=product/search/',
            data: getParams,
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




<?php echo $footer; ?>

