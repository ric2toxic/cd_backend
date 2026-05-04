<?php echo $header;?>
  <div id="page-wrapper">
    <!-- page title-->
    <div class="col-sm-12">
      <h1 class="page_title">SOR SKU Payment Detail (<?php echo $paid_data[0]['seller_sku'];?>)</h1>
    </div>
    <!-- page Contant-->
    <div class="row">
      <div class="col-sm-12">
        <div class="sor_invoice_page">
            <ul class="nav sor_inventory_header">
              <li><a style="cursor: pionter;" href="<?php echo $sor_inventory_link?>">Invoice</a></li>
              <li><a style="cursor: pionter;" href="<?php echo $sor_sku_link?>">SKU</a></li>              
              <li class="active"><a style="cursor: pionter; width:171px;" href="<?php echo $sor_sku_link?>">Payment Detail of SKU</a></li>
            </ul>

            <div class="tab-content sor_inventory_content">             
              <div id="sku_wise" class="tab-pane active">
                <?php

                if (!empty($paid_data)) {
                ?>
                  <table width="100%">
                    <thead class="main_head">
                      <th style="text-align:center;">Sub Order No</th>
                      <th style="text-align:center;">Total Pieces</th>
                      <th style="text-align:center;">Total Amount</th>
                      <th style="text-align:center;">Reference No.</th>
                      <th style="text-align:center;">Payment Done Date</th>
                    </thead>
                      <?php
                      foreach ($paid_data as $key => $value) {
                      ?>
                      <tr>
                        <td style="text-align:center;"><?php echo $value['suborder_id'];?></td>
                        <td style="text-align:center;"><?php echo ($value['quantity']*$value['piece_in_set']);?></td>
                        <td style="text-align:center;"><?php echo $value['sold_amt'];?></td>
                        <td style="text-align:center;"><?php echo $value['trxn_utr'];?></td>                        
                        <td style="text-align:center;">
                        <?php 
                        if ($value['trxn_utr_date']!='') {
                          echo date('d-m-Y',strtotime($value['trxn_utr_date']));
                        }                        
                        ?>                          
                        </td>                        
                      </tr>                      
                      <?php 
                      }
                      ?>
                  </table>
                 
                <?php 
                }
                ?>
              </div>
            </div>
        </div>
      </div>
    </div>
     <div class="row">
        <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
        <div class="col-sm-6 text-right"><?php echo $results; ?></div>
      </div>
  </div> 
<?php echo $footer; ?>
<script type="text/javascript">
$( document ).ready(function() {
    
    var all_data_tooltip  = '';
    var order_no          = '';
    
    $('.paid_tooltip').mouseover(function(){
        
        order_no                = $(this).data('order-no');
        all_data_tooltip        = $(this).data('paid-data');
        
        //console.log(all_data_tooltip);

        $('#tooltip_li_'+order_no).tooltip({title: all_data_tooltip, html: true, placement: "bottom"});
    }).mouseout(function(){
       
    });
});  
</script>