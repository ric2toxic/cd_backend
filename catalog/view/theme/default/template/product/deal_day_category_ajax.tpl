<!--
<row>
    <div class="col-md-8 category-title"><h2><?php echo $heading_title; ?> </h2></div>
</row>
-->
<div class="clear"></div>
<div class="row sorting_bottom_line">
    <div class="col-md-12 col-xs-6 text-right">
    <!--<label class="control-label" for="input-sort"><?php echo $text_sort; ?></label>-->
        <div class="col-sm-8">
            <div class="range_sort">
                <!--<label class="control-label" for="input-sort"><?php echo $text_sort; ?></label>-->
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
            <?php //echo "<pre>"; print_r($category_id); echo "</pre>";?>
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
    <?php /*?>
    <select id="input-sort" class="form-control" style="width:auto; display:inline-block;"  name="sorting_values">
    <?php foreach ($sorts as $sorts) { ?>
    <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
    <option value="<?php echo $sorts['query_string']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $sorts['query_string']; ?>"><?php echo $sorts['text']; ?></option>
    <?php } ?>
    <?php } ?>
   </select>
       <?php */?>
    <?php /* ?>

    <?php foreach ($sorts as $sorts) { ?>
    <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
    <a href="<?php echo $sorts['href']; ?>" class="selected"><?php echo $sorts['text']; ?></a>
    <?php } else { ?>
    <a href="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></a>
    <?php } ?>
    <?php } ?>
    <?php */ ?>
</div>
    </div>
</div>

<row>
    <?php /*?>
    <div class="col-md-12 text-right">
        <label class="control-label" for="input-sort"><?php echo $text_sort; ?></label>
        <select id="input-sort" class="form-control" style="width:auto; display:inline-block;">
            <?php foreach ($sorts as $sorts) { ?>
            <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
            <option value="<?php echo $sorts['query_string']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $sorts['query_string']; ?>"><?php echo $sorts['text']; ?></option>
            <?php } ?>
            <?php } ?>
        </select>
    </div> <?php */?>
    <div class="col-md-12 selected_filters nopadding">
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
                    <span class="filter clearall" data-id="all">Clear All&nbsp;<i class="fa fa-times"></i>
                        <?php
                }
            }
        ?>

    </div>
</row>

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
<?php } ?> -->
<?php /* ?>
<?php if ($categories) { ?>
<h3><?php echo $text_refine; ?></h3>
<?php if (count($categories) <= 5) { ?>
<div class="row">
    <div class="col-sm-3">

        <ul>
            <?php foreach ($categories as $category) { ?>
            <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
            <?php } ?>
        </ul>
    </div>
</div>
<?php } else { ?>
<div class="row">
    <?php foreach (array_chunk($categories, ceil(count($categories) / 4)) as $categories) { ?>
    <div class="col-sm-3">
        <ul>
            <?php foreach ($categories as $category) { ?>
            <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>
</div>
<?php } ?>
<?php } ?>
<?php */ ?>

<?php if ($products) { ?>
<!-- <p><a href="<?php echo $compare; ?>" id="compare-total"><?php echo $text_compare; ?></a></p> -->

<div class="row" >
    <div class="col-md-7">
        <div class="btn-group hidden">
            <button type="button" id="list-view" class="btn btn-default" data-toggle="tooltip" title="<?php echo $button_list; ?>"><i class="fa fa-th-list"></i></button>
            <button type="button" id="grid-view" class="btn btn-default" data-toggle="tooltip" title="<?php echo $button_grid; ?>"><i class="fa fa-th"></i></button>
        </div>
    </div>

</div>
<br />
<?php if($single_store_alert != '' ){ ?>
<div class="alert alert-warning">
    <?php echo $single_store_alert; ?>
</div>
<?php } ?>

<?php if($bandhani_alert){ ?>
<div class="alert alert-warning">
    <?php echo $bandhani_alert;?>
</div>
<?php } ?>

<div class="row" id="list_container">
    <?php echo $product_list; ?>
    <div id="results"></div>

</div>
    <?php if($current_page < $total_pages) { ?>
        <div class="browse_more">
            <button type="button" class="btn_browse_more btn" data-page="<?php echo $current_page;?>" data-total-pages="<?php echo $total_pages;?>" path_no="<?php echo $current_page_path;?>">Browse More</button>
        </div>
    <?php } ?>
</div>
<?php } ?>

<?php if (!$products) { ?>
<p><strong><h3><?php echo $text_empty; ?></h3></strong></p>
<?php /* ?>
<div class="buttons">
    <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
</div>
<?php */ ?>
<?php } ?>
<?php echo $content_bottom; ?>

<script type="text/javascript">
    var query_string_sort = '<?php if((!empty($sort))&& (!empty($order))){echo $sort.'&order='.$order;}else{echo "";}?>';  // it is used for store sorting value
    var category_id = '<?php if((!empty($category_id))){echo $category_id ;}else{echo "";}?>';  // it is used for store sorting value


    $(document).ready(function () {

        $('div.product-thumb').mouseenter(function(){
            $(this).find('div.button-group').addClass('hoverclass');
        });

        $('div.product-thumb').mouseleave(function(){
            $(this).find('div.button-group').removeClass('hoverclass').addClass( "button-group" );
        });


        $("#input-sort li a").click(function() {
            query_string_sort = ($(this).attr('data-value') ? $(this).attr('data-value') : " ") ;

            addFilterInHistoryState(query_string_sort,category_id);

            $.ajax({
                url: 'index.php?route=product/category/dealoftheday',
                type: 'get',
                data: 'post_type=ajax&sort='+query_string_sort+'&category_id='+category_id,
                dataType: 'html',
                beforeSend: function () {
                    $('body').removeClass('loaded').addClass('loading');
                    $('.filter_box').addClass('hidden-xs');
                },
                complete: function () {
                    $('body').removeClass('loading').addClass('loaded');
                },
                success: function (data) {

                    $('#content').html(data);

                }

            });
        });

        $('.sorting_bottom_line select[name=\'category_id\']').change(function(){
            category_id = $(this).val();
            addFilterInHistoryState(query_string_sort,category_id);

            $.ajax({
                url: 'index.php?route=product/category/dealoftheday',
                type: 'get',
                data: 'post_type=ajax&sort='+query_string_sort+'&category_id='+category_id,
                dataType: 'html',
                beforeSend: function () {
                    $('body').removeClass('loaded').addClass('loading');
                    $('.filter_box').addClass('hidden-xs');
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

    function addFilterInHistoryState(sort,category_id){
        if (window.location.hash) {
            var url_page = window.location.protocol + "//" + window.location.host + window.location.pathname;
            //filter_string = url_page + "#!filter=" + filter.join(',')+"&price_filter="+price_filter+"&sort="+sort;
            //filter_string = url_page + "?route=product/category/dealOfTheDay#!sort="+sort;
            filter_string = url_page + "#!sort="+sort+'&category_id='+category_id;
        } else {
            //filter_string = window.location + "#!filter=" + filter.join(',')+"&price_filter="+price_filter+"&sort="+sort;
            filter_string = window.location + "#!sort="+sort+'&category_id='+category_id;
        }

        history.pushState(null, null, filter_string);
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.btn_browse_more').click(function(){
            fiter_qry_str = '';
            if(window.location.hash) {
                fiter_qry_str = "&"+window.location.hash.replace("#!", "");
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
                        data: "<?php echo $qry_string;?>&post_type=ajax_pagination&page=" + (parseInt($(this).attr('data-page')) + 1)+fiter_qry_str,
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