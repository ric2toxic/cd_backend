    <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>

    <?php } ?>

    </ul>
    <div class="category-title_update">
    <h2><?php echo $heading_title; ?> </h2>
    </div>
<div class="clear"></div>
<div class="sorting_bottom_line">
    <div class="col-md-12 col-xs-6 text-right">    
    <div class="range_sort">
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
</div>
    </div>
</div>

<row>
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
    <div class="col-md-12 selected_filters nopadding">
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
                    <span class="filter clearall" data-id="all">Clear All&nbsp;<i class="fa fa-times"></i>
                        <?php
                }
            }
        ?>

    </div>
</row>
<?php if(isset($no_products_for_applied_filters)){ ?>
<div class="no_products_for_applied_filters">
    <?php echo $no_products_for_applied_filters; ?>
</div>
<?php } ?>


<?php if ($products) { ?>

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
<?php if($alert_thaan_dispatch){ ?>
<div class="row">
    <div class="alert alert-warning nomargin col-md-12">
        <?php echo $alert_thaan_dispatch;?>
    </div>
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
<?php if (!$categories && !$products) { ?>
<p><strong><h3><?php echo $text_empty; ?><h3></p></strong>
<div class="buttons">
    <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
</div>
<?php } ?>
<?php echo $content_bottom; ?>

<script type="text/javascript">
    // it is get from category controller  for price filter.
    var price_filter = '<?php if(!empty($price_filter)){echo $price_filter;}else{echo "0";}?>';

    var query_string_sort = '<?php if((!empty($sort))&& (!empty($order))){echo $sort.'&order='.$order;}else{echo "sort_order";}?>';  // it is used for store sorting value
    var rating_filter = '';
    $(document).ready(function () {

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

        $('div.product-thumb').mouseenter(function(){
            $(this).find('div.button-group').addClass('hoverclass');
        });

        $('div.product-thumb').mouseleave(function(){
            $(this).find('div.button-group').removeClass('hoverclass').addClass( "button-group" );
        });

        var hash = window.location.hash;
        var spliting = hash.split("&");

        if(window.location.hash) {
            price_filter = spliting[1].split("=")[1];
            $('#amount_hidden').val(price_filter);
        
            if(typeof spliting[4] != 'undefined') {
              query_string_sort = spliting[3].split('=')[2] + "&" + spliting[4];
            }

            rating_filter = getUrlParameter('rating_filter',hash);

        }
        $("span.filter").click(function() {
            filter = [];
            option = [];

            $('input[name^=\'filter\']:checked').each(function (element) {
                if (filter.indexOf(this.value) < 0) filter.push(this.value);
            });
            $('input[name^=\'option\']:checked').each(function (element) {
                if (option.indexOf(this.value) < 0) option.push(this.value);
            });



//            var unique=filter.filter(function(itm,i,a){
//                return i==a.indexOf(itm);
//            });
//            var filter = unique;




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

            addFilterInHistoryState(filter,price_filter,option,query_string_sort,rating_filter);

            $.ajax({
                url: 'index.php?route=product/category',
                type: 'get',
                data: 'path=<?php echo $path;?>&post_type=ajax&filter=' + filter.join(',')+'&price_filter='+price_filter+"&option="+option.join(',')+"&sort="+query_string_sort+"&rating_filter="+rating_filter,
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

        /****-------- start ajax of sorting product ----****/
            // this functionality for sorting product by ajax  AND add sort in addFilterInHistoryState()

        $("#input-sort li a").click(function() {
            query_string_sort = ($(this).attr('data-value') ? $(this).attr('data-value') : " ") ;

            addFilterInHistoryState(filter,price_filter,option,query_string_sort,rating_filter);

            $.ajax({
                url: 'index.php?route=product/category',
                type: 'get',
                data: 'path=<?php echo $path;?>&post_type=ajax&filter=' + filter.join(',')+'&price_filter='+price_filter+"&option="+option.join(',')+"&sort="+query_string_sort+"&rating_filter="+rating_filter,
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

//        $("#input-sort").change(function() {
//            query_string_sort = ($(this).val() ? $(this).val() : " ") ;
//            addFilterInHistoryState(filter,price_filter,query_string_sort);
//
//            $.ajax({
//                url: 'index.php?route=product/category',
//                type: 'get',
//                data: 'path=<?php echo $path;?>&post_type=ajax&filter=' + filter.join(',')+'&price_filter='+price_filter+"&sort="+query_string_sort,
//                dataType: 'html',
////                beforeSend: function () {
////                    $('body').removeClass('loaded').addClass('loading');
////                    $('.filter_box').addClass('hidden-xs');
////                },
////                complete: function () {
////                    $('body').removeClass('loading').addClass('loaded');
////
////
////                },
//                success: function (data) {
//
//                    $('#content').html(data);
//
//                }
//
//            });
//        });
        /****-------- End ajax of sorting product ----****/
    });

    function addFilterInHistoryState(filter,price_filter,option,sort,rating_filter){

        if (window.location.hash) {
            var url_page = window.location.protocol + "//" + window.location.host + window.location.pathname;
            filter_string = url_page + "#!filter=" + filter.join(',')+"&price_filter="+price_filter+"&option="+option.join(',')+"&sort="+sort+"&rating_filter="+rating_filter;

        } else {
            filter_string = window.location + "#!filter=" + filter.join(',')+"&price_filter="+price_filter+"&option="+option.join(',')+"&sort="+sort+"&rating_filter="+rating_filter;
        }

        history.pushState(null, null, filter_string);
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.btn_browse_more').click(function(){
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
                        data: "<?php echo $qry_string;?>&track=1&post_type=ajax_pagination&path="+(parseInt($(this).attr('path_no')))+"&page="+(parseInt($(this).attr('data-page'))+1)+"&handpicked_ids=<?php echo $handpicked_ids;?>&random_string=<?php echo $random_string; ?>&product_total=<?php echo $product_total; ?>"+filter_qry_str,
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
                            /*alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);*/
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
