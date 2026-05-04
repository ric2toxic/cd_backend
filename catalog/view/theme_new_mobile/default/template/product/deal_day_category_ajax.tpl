<?php
         if ($products) { ?>

<div class="row" id="list_container">



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
<?php } ?>

<?php } ?>
<?php /* if (!$categories && !$products) { */?>
<?php if (!$products) { ?>
<p><strong><h3><?php echo $text_empty; ?><h3></p></strong>
<div class="buttons">
    <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
</div>
<?php } ?>
<?php echo $content_bottom; ?>

<script type="text/javascript">
    sorting = '';
    category_id = '';
    $(document).ready(function () {


        $('div.product-thumb').mouseenter(function(){
            $(this).find('div.button-group').addClass('hoverclass');
        });

        $('div.product-thumb').mouseleave(function(){
            $(this).find('div.button-group').removeClass('hoverclass').addClass( "button-group" );;
        })

        var hash = window.location.hash;
        var spliting = hash.split("&");

        if(window.location.hash) {
//            if(typeof spliting[1] != 'undefined') {
//                sorting = spliting[1];
//            }
            if(typeof spliting[2] == 'undefined'){
                category_id = spliting[1].split('=')[1];
            }else{
                sorting = spliting[0].split('=')[1]+"&order="+spliting[1].split('=')[1];
                category_id = spliting[2].split('=')[1];
            }
        }

        $("span.filter").click(function() { //alert("here");


            //addFilterInHistoryState(sorting);
            addFilterInHistoryState(sorting,category_id);

            $.ajax({
                url: 'index.php?route=product/category/dealoftheday',
                type: 'get',
                //data: 'path=<?php echo $path;?>&post_type=ajax&sort='+sorting,
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

//        $(".sorting").click(function() {
//            sorting = ($(this).attr('data-sorting-value') ? $(this).attr('data-sorting-value') : " ");
//
//            addFilterInHistoryState(sorting,category_id);
//
//            $.ajax({
//                url: 'index.php?route=product/category/dealoftheday',
//                type: 'get',
//                data: 'post_type=ajax&sort='+sorting+'&category_id='+category_id,
//                dataType: 'html',
//                beforeSend: function () {
//                    $('body').removeClass('loaded').addClass('loading');
//                    $('.filter_box').addClass('hidden-xs');
//                },
//                complete: function () {
//                    $('body').removeClass('loading').addClass('loaded');
//                },
//                success: function (data) {
//
//                    $('#content').html(data);
//
//                }
//
//            });
//        });
//
//        $('.nomargin-mobileonly select[name=\'category_id\']').change(function(){
//            category_id = $(this).val();
//            addFilterInHistoryState(sorting,category_id);
//
//            $.ajax({
//                url: 'index.php?route=product/category/dealoftheday',
//                type: 'get',
//                data: 'post_type=ajax&sort='+sorting+'&category_id='+category_id,
//                dataType: 'html',
//                beforeSend: function () {
//                    $('body').removeClass('loaded').addClass('loading');
//                    $('.filter_box').addClass('hidden-xs');
//                },
//                complete: function () {
//                    $('body').removeClass('loading').addClass('loaded');
//                },
//                success: function (data) {
//
//                    $('#content').html(data);
//
//                }
//
//            });
//        });

    });

    //function addFilterInHistoryState(sorting){
    function addFilterInHistoryState(sorting,category_id){
        if (window.location.hash) {
            var url_page = window.location.protocol + "//" + window.location.host + window.location.pathname;

            //filter_string = url_page + "#!sort="+sorting;
            filter_string = url_page + "#!sort="+sorting+"&category_id="+category_id;

        } else {
            //filter_string = window.location + "#!sort="+sorting;
            filter_string = window.location + "#!sort="+sorting+"&category_id="+category_id;
        }

        history.pushState(null, null, filter_string);
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.btn_browse_more').click(function(){ //alert(1);
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
                        //data: "<?php echo $qry_string;?>&post_type=ajax_pagination&path= "+(parseInt($(this).attr('path_no')))+"&page=" + (parseInt($(this).attr('data-page')) + 1)+fiter_qry_str,
                        data: "<?php echo $qry_string;?>&post_type=ajax_pagination&page=" + (parseInt($(this).attr('data-page')) + 1)+fiter_qry_str,
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
    });
</script>
