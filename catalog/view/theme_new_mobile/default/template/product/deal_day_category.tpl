<?php echo $header; ?>

<div class="container category_page">
    <?php if(!empty($total_deal_day)){
            $today = date('Y-m-d');
            $today = date('Y-m-d', strtotime($today));
            if(($today >= $start_date) &&($today <= $end_date)){
     ?>
        <div class="row mobile_filter_row">
            <div id="filter_bar" class="panel panel-default mobileonly fillter_panel">
                <?php //echo $module_filters;?>
                <div class="row nomargin-mobileonly">
                    <div class="sort_by col-xs-6 hidden-md hidden-sm mobileonly">
                        <div class="panek-heading sort_by_click"><a href="#sort_by_options" id="sortby">Sort by</a></div>
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
                        <select name="category_id" class="form-control mobile_all_category">
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
        </div>
        <?php if(!empty($data_deal_day) && isset($data_deal_day)){ ?>
        <div class="row deal_day_info">
            <div class="col-sm-12">
                <div class="col-sm-12 block ">
                    <?php /* ?> <div class="ic_ruppee"></div> <?php */ ?>
                    <ul class="promo_box">
                        <?php
                            foreach($data_deal_day as $data){
                                if(!empty($data['start_date'])){
                                    $start = explode("-",substr($data['start_date'],0,10));
                                    $data['start_date'] = $start[2].'-'.$start[1].'-'.$start[0];
                                }
                                if(!empty($data['end_date'])){
                                    $end = explode("-",substr($data['end_date'],0,10));
                                    $data['end_date'] = $end[2].'-'.$end[1].'-'.$end[0];
                                }
                                $today = date('Y-m-d');
                                $today = date('Y-m-d', strtotime($today));
                                $data_start_date = date('Y-m-d', strtotime($data['start_date']));
                                $data_end_date = date('Y-m-d', strtotime($data['end_date']));
                                if(($today >= $data_start_date) &&($today <= $data_end_date)){

                         ?>
                               <?php /* ?> <li class="promo_text"><?php echo abs($data['discount']);?>% Discount On  RS. <?php echo $data['order_amount'];?> and more</li> <?php */ ?>
                                <li class="promo_text"><?php echo $data['description'];?></li>
                        <?php
                                }
                            }
                         ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php } } }?>
    <div class="row">
        <div id="content" class="col-sm-12 category_page">
            <?php echo $content_top; ?>
            <div class="row">
                <div class="col-md-8 category-title"><h2><?php echo $heading_title; ?> </h2></div>
            </div>
            <?php if ($products) { ?>
            <div class="row <?php echo $store_class; ?>" id="list_container">
                <div class="row">
                    <div class="col-md-8 selected_filters">
                        <?php
                            if(isset($filters) && is_array($filters) && !empty($filters)){
                                foreach($filters as $filter){
                        ?>
                                    <span class="filter" data-id="<?php echo $filter['filter_id'];?>"><?php echo $filter['name']; ?><i class="fa fa-times"></i></span>
                        <?php
                                }
                             }
                        ?>
                    </div>
                </div>

                <?php if($single_store_alert != ''){ ?>

                <div class="alert alert-warning col-md-12">
                    <?php echo $single_store_alert;?>
                </div>

                <?php } ?>

                <?php echo $product_list; ?>
                <div id="results"></div>
            </div>

            <?php if($current_page < $total_pages) { ?>
            <div class="browse_more">
                <button type="button" class="btn_browse_more btn btn-primary" data-page="<?php echo $current_page;?>" data-total-pages="<?php echo $total_pages;?>" path_no="<?php echo $current_page_path;?>">Browse More</button>
            </div>
            <?php } ?>

            <?php } ?>

            <?php if (!$categories && !$products) { ?>
            <p><strong><h3><?php echo $text_empty; ?><h3></p></strong>
            <div class="buttons">
                <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
            </div>

            <?php } ?>
            <?php echo $content_bottom; ?></div>
        <?php //echo $column_right; ?>
    </div>
</div>
<script src="http://cdnjs.cloudflare.com/ajax/libs/masonry/3.1.5/masonry.pkgd.min.js"></script>
<script>
    var msnry = '';
    $(window).load(function () {

        var msnry = $('#list_container').masonry({
            // specify itemSelector so stamps do get laid out
            columnWidth: '.item',
            itemSelector: '.item'
        });
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
                        dataType: 'html',
                        //type: 'get',
                        //url: '<?php echo $url_path;?>',
                        //data: "<?php echo $qry_string;?>&post_type=ajax_pagination&path= "+(parseInt($(this).attr('path_no')))+"&page=" + (parseInt($(this).attr('data-page')) + 1),
                        //dataType: 'html',
                        beforeSend: function() {
                            $('.btn_browse_more').button('loading');
                        },
                        complete: function() {
                            $('.btn_browse_more').button('reset');
                        },
                        success: function(data) {
                            //class_result =  'result'+$a;
                            //data = '<div class="'+class_result+'">'+data+'</div>';
                            //$("#list_container").append(data);

                            $("#list_container").append(data).masonry( 'reloadItems' );
                            var msnry = $('#list_container').masonry({
                                // specify itemSelector so stamps do get laid out
                                columnWidth: '.item',
                                itemSelector: '.item'
                            });

                            //msnry.appended(class_result);
                            // console.log(msnry);


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

    $('#closeing_image').click(function() {
        $('#tooltip_container').hide();
    });


</script>

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
<script type="text/javascript">
    $('#store_change').on('click' , function(argument) {
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

<script type="text/javascript"><!--
    sorting = '';
    category_id = '';
    $(document).ready(function () {
        $('.sorting').click(function(){
            sorting = ($(this).attr('data-sorting-value') ? $(this).attr('data-sorting-value') : " ");
            jQuery.fancybox.close();
            callAjaxToFilter(category_id);
        });
        $('.nomargin-mobileonly select[name=\'category_id\']').change(function(){
            category_id = $(this).val();
            jQuery.fancybox.close();
            callAjaxToFilter(category_id);
        });

        var hash = window.location.hash;
        var spliting = hash.split('&');
        if(window.location.hash){
            if(typeof spliting[2] == 'undefined'){
                category_id = spliting[1].split('=')[1];
            }else{
                sorting = spliting[0].split('=')[1]+"&order="+spliting[1].split('=')[1];
                category_id = spliting[2].split('=')[1];
            }

            callAjaxToFilter(category_id);
        }
    });

    //location = '<?php echo $action; ?>&filter=' + filter.join(',');
    function callAjaxToFilter(category_id) {

        //addFilterInHistoryState(sorting);
        addFilterInHistoryState(sorting,category_id);


        $.ajax({
            url: 'index.php?route=product/category/dealoftheday',
            type: 'get',
            //data: 'post_type=ajax&sort='+sorting,
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

                $('#content').html(data);
                $("select.mobile_all_category").val(category_id);


            }
        });
    }

    //function addFilterInHistoryState(sorting){
    function addFilterInHistoryState(sorting,category_id){
        if (window.location.hash) {
            var url_page = window.location.protocol + "//" + window.location.host + window.location.pathname;
            //filter_string = url_page + "#!sort="+sorting;
            filter_string = url_page + "#!sort="+sorting+"&category_id="+category_id;
        } else {
            //filter_string = window.location + "#!sort="+sorting;;
            filter_string = window.location + "#!sort="+sorting+"&category_id="+category_id;
        }

        history.pushState('<?php echo $path;?>', null, filter_string)
    }

    //--></script>
<?php echo $footer; ?>

<style type="text/css">
    .category_page .nomargin-mobileonly .mobile_all_category {
        border: medium none;
        height: 35px !important;
        padding-top: 8px;
        color: #323581;
    }
    .category_page .nomargin-mobileonly .mobile_all_category:focus{
        box-shadow: none;
    }
</style>