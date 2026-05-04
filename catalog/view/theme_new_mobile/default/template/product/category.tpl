<?php echo $header; ?>
  <div class="container category_page">

    <div class="row mobile_filter_row">
        <?php echo $module_filters;?>
    </div>
  <div class="row">

    <div id="content" class="col-sm-12 ">


        <div class="<?php //echo $store_class; ?> list_container_class" id="list_container">
            <?php echo $content_top; ?>
            <row>
                <div class="col-md-8 category-title"><h2><?php echo $heading_title; ?> </h2></div>

            </row>
            <?php if($single_store_alert != ''){ ?>
            <div class="row bandhani_message">
                <div class="alert alert-warning nomargin col-md-10">
                    <?php echo $single_store_alert;?>
                </div>
            </div>
            <?php } ?>

            <?php if($bandhani_alert){ ?>
            <div class=" bandhani_message">
                <div class="alert alert-warning nomargin col-md-10">
                    <?php echo $bandhani_alert;?>
                </div>
            </div>
            <?php } ?>
            <?php if($alert_thaan_dispatch){ ?>
            <div class=" bandhani_message">
                <div class="alert alert-warning nomargin col-md-10">
                    <?php echo $alert_thaan_dispatch;?>
                </div>
            </div>
            <?php } ?>

      <?php if ($products) {  ?>




              <?php echo $product_list; ?>
              <div id="results"></div>




      <?php } ?>
            </div>
        <?php if($current_page < $total_pages) { ?>
        <div class="browse_more">
            <button type="button" class="btn_browse_more btn btn-primary" data-page="<?php echo $current_page;?>" data-total-pages="<?php echo $total_pages;?>" path_no="<?php echo $current_page_path;?>">Browse More</button>
        </div>
        <br />
        <?php } ?>
      <?php if (!$categories && !$products) { ?>
          <br /><br /><br /><br /><br />
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
            <p><strong><h3><?php echo $text_empty_store; ?><a href="javascript:void(0)" class="store_change">Click here</a> to change store<h3></p></strong>

          <div class="buttons">
            <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
          </div>

      <?php } ?>
      <?php echo $content_bottom; ?></div>
    <?php //echo $column_right; ?>
  </div>
</div>
<div class="container">
    <div class="row">
        <div class=" col-xs-12 text-center">
            <br />
            <?php echo $description; ?>
        </div>
    </div>
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
              url: '<?php echo $url_path;?>&track=1&mobile=1',
              data: "<?php echo $qry_string;?>&post_type=ajax_pagination&path="+(parseInt($(this).attr('path_no')))+"&page="+(parseInt($(this).attr('data-page')) + 1)+"&handpicked_ids=<?php echo $handpicked_ids; ?>&random_string=<?php echo $random_string; ?>&product_total=<?php echo $product_total; ?>",
              dataType: 'html',
              beforeSend: function() {
                $('.btn_browse_more').button('loading');
              },
              complete: function() {
                $('.btn_browse_more').button('reset');
              },
              success: function(data) {
                //class_result =  'result'+$a;
                //data = '<div class="'+class_result+'">'+data+'</div>';
                 $("#list_container").append(data);




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

