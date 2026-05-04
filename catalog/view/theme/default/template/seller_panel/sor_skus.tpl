<?php echo $header;?>
  <div id="page-wrapper">
    <!-- page title-->
    <div class="col-sm-12">
      <h1 class="page_title">SOR SKU</h1>
    </div>
    <!-- page Contant-->
    <div class="row">
      <div class="col-sm-12">
        <div class="sor_invoice_page">
            <ul class="nav sor_inventory_header">
              <li><a style="cursor: pionter;" href="<?php echo $sor_inventory_link?>">Invoice</a></li>
              <li class="active"><a style="cursor: pionter;" href="<?php echo $sor_sku_link?>">SKU</a></li>
            </ul>

            <div class="tab-content sor_inventory_content">             
              <div id="sku_wise" class="tab-pane active">
                <?php

                if (!empty($sorSkusData)) {
                ?>
                  <table width="100%">
                    <thead class="main_head">
                      <th style="text-align:center;">Image</th>
                      <th style="text-align:center;">SKU Code</th>
                      <th style="text-align:center;">Total Pieces</th>
                      <th style="text-align:center;">Pieces returned back to you</th>
                      <th style="text-align:center;">Pieces Sold</th>
                      <th style="text-align:center;">Pieces return by Customer</th>
                       <th style="text-align:center;">Unsold Pieces with us</th>
                      <th style="text-align:center;">Net Pieces sold</th>
                      <th style="text-align:center;">Amount Paid</th>
                    </thead>
                      <?php
                      foreach ($sorSkusData as $key => $value) {
                        
                        ?>
                        <tr>                        
                          <td align="center"><img class="sor_inventory_popup_image" src="<?php echo $value['image'];?>" width="<?php echo $value['width'];?>" height="<?php echo $value['height'];?>"></td>
                          <td style="text-align:center;"><?php echo $value['sku'];?></td>
                          <td style="text-align:center;">
                            <?php 
                            if ($value['total_pur_qty']!='' && $value['total_pur_qty']!='0') {
                              echo $value['total_pur_qty'].' pieces';
                            }                          
                            ?>
                            <br />
                            <span class="price_value_cls">
                            <?php 
                            if ($value['total_pur_amt']!='' && $value['total_pur_amt']!='0') {
                              echo $value['total_pur_amt'];
                            }                          
                            ?>
                            </span>
                          </td>

                          <td style="text-align:center;">
                            <?php 
                            if ($value['purchase_return_pieces']!='' && $value['purchase_return_pieces']!='0') {
                              echo $value['purchase_return_pieces'].' pieces';
                            }                          
                            ?>
                          </td>

                         
                          <td style="text-align:center;">
                            <?php 
                            if ($value['total_actual_sold_qty']!='' && $value['total_actual_sold_qty']!='0') {
                              echo $value['total_actual_sold_qty'].' pieces';
                            }                          
                            ?>
                            <br />
                            <span class="price_value_cls">
                            <?php 
                            if ($value['total_actual_sold_amt']!='' && $value['total_actual_sold_amt']!='0') {
                              echo $value['total_actual_sold_amt'];
                            }                          
                            ?>
                            </span>
                          </td>
                          <td style="text-align:center;">

                          <?php 
                            if ($value['total_return_qty']!='' && $value['total_return_qty']!='0') {
                              echo $value['total_return_qty'].' pieces';
                            }                          
                            ?>
                            <br />
                            <span class="price_value_cls">
                            <?php 
                            if ($value['total_sold_amt']!='' && $value['total_sold_amt']!='0') {
                              echo $value['product_return_amt'];
                            }                          
                            ?>
                           </span>
                          </td>

                          <td style="text-align:center;">
                            <?php 
                            if ($value['unsold_pieces']!='' && $value['unsold_pieces']!='0') {
                              echo $value['unsold_pieces'].' pieces';
                            }                          
                            ?>
                            <br />
                            <span class="price_value_cls">
                             <?php 
                            if ($value['total_unsold_amt']!='' && $value['total_unsold_amt']!='0') {
                              echo $value['total_unsold_amt'];
                            }                          
                            ?>
                            </span>
                          </td>                          
                          
                           <td style="text-align:center;">
                            <?php 
                            if ($value['total_sold_qty']!='' && $value['total_sold_qty']!='0') {
                              echo $value['total_sold_qty'].' pieces';
                            }                          
                            ?>
                            <br />
                            <span class="price_value_cls">
                            <?php 
                            if ($value['total_sold_amt']!='' && $value['total_sold_amt']!='0') {
                              echo $value['total_sold_amt'];
                            }                          
                            ?>
                            </span>
                          </td>
                          
                           <td style="text-align:center;">
                          <?php 
                          
                          if ($value['paid_amount']!='' && $value['paid_amount']!='0') {
                              echo $value['paid_amount'];
                            
                          ?>
                          <a target="__blank" href="<?php echo $sor_sku_payment_detail_link.'&product_id='.$value['product_id'].'&purchase_id='.$value['purchase_id'];?>">
                          <?php echo $value['trxn_done']; ?>  
                          </a>                          
                          <?php
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