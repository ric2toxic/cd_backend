<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <h1 class="order_title"><?php echo $heading_title; ?></h1>
      <?php if ($orders) { ?>
      <div class="col-sm-12 row">
        <?php foreach ($orders as $order) { ?>
          <div class="order_table_box">
            <div class="order-expanded">
              <div class="order_no_text">
                <span class="smallText"><?php echo $column_order_no; ?></span>
                <strong> <?php echo $order['order_no']; ?></strong><br>
                <span class="smallText payable_invoice_value"><?php echo $text_payable_invoice_value; ?></span>
                <strong> <?php echo $order['payable_invoice_value']; ?></strong><br>
                
                <?php if(!empty($order['payable_cashback_coupon_discount'])) {?>
                <span class="smallText payable_cashback_coupon_discount"><?php echo $text_payable_cashback_coupon_discount; ?></span>
                <strong> <?php echo $order['payable_cashback_coupon_discount']; ?></strong><br>
                <span class="smallText payable_net_payable"><?php echo $text_payable_net_payable; ?></span>
                <strong> <?php echo $order['payable_net_payable']; ?></strong>
                <?php } ?>
                
              </div>
              <!--<div class="totle_value"> <?php echo $order['order_total']; ?></div>-->
              <div class="clearfix"></div>
            </div>
            <div class="order-details">
              <span>
                <label><?php echo $column_date_added; ?></label> <?php echo $order['date_added']; ?>
              </span>
              <span>
                <label><?php echo $text_payment_method; ?></label> <?php echo $order['payment_method']; ?>
              </span>
            </div>
            <div class="order-bottem">
              <div class="collapsed button_box" data-toggle="collapse" data-target="#order_id_<?php echo $order['order_id'];?>"><?php echo $click_to_see_suborder; ?>
                <span class="fa plus"></span>

                <span class="fa minus"></span>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="collapse" id="order_id_<?php echo $order['order_id'];?>">
              <?php if( !empty( $order['suborder'] ) ){ ?>
                <?php foreach( $order['suborder'] as $suborder_data ) { ?>
                  <div class="suborder_box">
                    <div class="suborder_text">
                      <span>
                        <label><?php echo $column_suborder_id; ?></label> <?php echo $suborder_data['suborder_id']; ?>
                      </span>
                      <span class="suborder_ammount">
                        <label><?php echo $column_amount; ?></label> <i class="fa fa-inr"></i> 
                        <?php echo $suborder_data['total']; ?>
                      </span>
                      <span><label><?php echo $column_status; ?></label> 
                        <?php echo $suborder_data['status']; ?> [ <?php echo $suborder_data['last_update_history']; ?> ]
                      </span>
                    </div>
                    <div class="suborder_bnt">
                      <a href="javascript:;" class="bnt suborder_bnt_box mrg_10 js-open-modal btn btn-info returnPopup" data-toggle="tooltip" title="Return" data-modal-id="popup";><i class="fa fa-undo" aria-hidden="true"></i></a>
                      <a href="<?php echo $suborder_data['href']; ?>" class="bnt suborder_bnt_box" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye" aria-hidden="true"></i></a>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                <?php } ?>  
              <?php } ?>  
            </div>
          </div>
        <?php } ?>
      </div>
      <div class="text-right"><?php echo $pagination; ?></div>
      <?php } else { ?>
      <p><?php echo $text_empty; ?></p>
      <?php } ?>
      <div class="buttons clearfix">
        <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-continue"><?php echo $button_continue; ?></a></div>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>
<div id="discount_shipping_popup" style="background-color:transparent; min-height: 150px; bottom: 45%; display: none;">
<div class="discount_ship_popup" style=" border: 2px dotted #133595;">
  <i class="fa fa-times close_ship_popup discount_popup1"></i>
      <div class="popup_content">
       <div style="color:#f13041;">Alert!</div>
      
     </div> 
   </div>
    <div class="popup_content">
       <div class="mssg" style="color:#666;">Return to using mobile please download our app</div>
      
     </div> 
   <div class="pop_up_btton_area">
   <div class="popup_button"><a href="https://play.google.com/store/apps/details?id=in.wholesalebox&amp;hl=en" target="_blank">Android</a></div>
       <div class="popup_button"><a href="https://itunes.apple.com/us/app/wholesalebox/id1254820324?mt=8" target="_blank">Ios</a></div> 
      </div> 
 </div>
<style type="text/css">
  .suborder_table table tr td {
    padding: 3px !important;
  }
</style>

<script type="text/javascript">
  <?php /* if (isset($international_store)) { ?>

var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5a420d52bbdfe97b137fd4ba/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

<?php } else { ?>

var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/55faa35605ceaf627695ea99/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

<?php } */ ?>
$(function(){
 $(".returnPopup").click(function(){
            $("#discount_shipping_popup").show("slide", {
                direction: "left"
            }, 500);
            //$(".discount_ship_popup").hide();
        });
 $(".close_ship_popup").click(function(){
           $("#discount_shipping_popup").hide("slide", {
               direction: "left"
           }, 500);
        });
});
</script>
