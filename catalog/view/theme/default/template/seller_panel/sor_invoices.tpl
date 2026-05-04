<?php echo $header;?>
  <div id="page-wrapper">
    <!-- page title-->
    <div class="col-sm-12">
      <h1 class="page_title">SOR Invoices</h1>
    </div>
    <!-- page Contant-->
    <div class="row">
      <div class="col-sm-12">
        <div class="sor_invoice_page">
            <ul class="nav sor_inventory_header">
              <li class="active"><a style="cursor: pionter;" href="<?php echo $sor_inventory_link?>">Invoice</a></li>
              <li><a style="cursor: pionter;" href="<?php echo $sor_sku_link?>">SKU</a></li>
            </ul>

            <div class="tab-content sor_inventory_content">
              <div id="invoice_wise" class="tab-pane fade in active">
                <?php
                if (!empty($sorInvoicesData)) {
                ?>
                  <table width="100%">
                    <thead class="main_head">
                      <th style="text-align:center;">Invoice No.</th>
                      <th style="text-align:center;">Invoice Date</th>
                      <th style="text-align:center;">Total Pieces</th>
                      <th style="text-align:center;">Pieces returned back to you</th>
                      <th style="text-align:center;">Unsold Pieces with us</th>
                      <th style="text-align:center;">Net Pieces sold</th>
                      <th style="text-align:center;">Amount Paid</th>
                      <th style="text-align:center;">Pending Amount</th>
                      
                    </thead>
                      <?php
                      $in=0;
                      foreach ($sorInvoicesData as $key => $value) {
                      ?>
                      <tr>
                        <td style="text-align:center;"><a style="cursor: pointer;" data-toggle="modal" data-target="#invoice_<?php echo $in;?>"><?php echo $value['invoice_no'];?></a></td>
                        <td style="text-align:center;"><?php echo $value['invoice_date'];?></td>
                        <td style="text-align:center;">
                        <?php echo $value['total_pieces'];?> Pieces <br />
                        <span class="price_value_cls"><?php echo $value['total_amount'];?></span></td>

                         <td style="text-align:center;">
                            <?php 
                            if ($value['total_purchase_return_pieces']!='' && $value['total_purchase_return_pieces']!='0') {
                              echo $value['total_purchase_return_pieces'].' Pieces';
                            }                          
                            ?> <br />
                          <span class="price_value_cls">  <?php echo $value['total_purchase_return_amount']; ?></span>
                          </td>

                        <td style="text-align:center;">
                        <?php echo $value['unsold_pieces'];?> Pieces<br />
                        <span class="price_value_cls"><?php echo $value['unsold_amount'];?></span></td>    

                        <td style="text-align:center;">
                        <?php echo $value['pieces_sold'];?> Pieces <br />
                        <span class="price_value_cls"><?php echo $value['amount_sold'];?></span></td>
                       
                         

                        <td style="text-align:center;"><?php echo $value['paid_amount'];?></td>


                        <td style="text-align:center;">
                          
                          <?php echo $value['seller_balance'];?></td>
                        </td>
                         
                        
                      </tr>                      
                      <?php
                      $in++; 
                      }
                      ?>
                  </table>
                      <!-- popup work start -->
                      <?php
                      $in=0;
                      foreach ($sorInvoicesData as $key => $value) {
                      ?>
                        <!-- Modal -->
                        <div id="invoice_<?php echo $in;?>" class="modal fade" role="dialog">
                          <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Invoice No.: <b><?php echo $value['invoice_no'];?></b> Invoice Date : <b><?php echo date('d-m-Y',strtotime($value['invoice_date']));?></b></h4>
                              </div>
                              <div class="modal-body sku_list">
                                <?php
                                if (!empty($value['invoice_products'])) {
                                ?>
                                <table width="100%">
                                  <thead class="main_head">
                                    <th style="text-align:center;">Image</th>
                                    <th style="text-align:center;">SKU</th>
                                    <th style="text-align:center;">Total Pieces</th>
                                    <th style="text-align:center;">Pieces returned back to you</th>
                                    <th style="text-align:center;">Pieces Sold by us</th>
                                    <th style="text-align:center;">Pieces return by Customer</th>
                                    <th style="text-align:center;">Net Pieces sold</th>               
                                  </thead>
                                  <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($value['invoice_products'] as $keyip => $valueip_new) {

                                       $rowspan   = 0;
                                       $kk        = 1;
                                       $row_class = '';
                                       if ($i%2==0) {
                                         $row_class = '#cccccc';
                                       }
                                       $i++;
                                       $rowspan = count($valueip_new);
                                        
                                        foreach ($valueip_new as $keynp => $valueip) {
                                          
                                          ?>
                                          <tr style="background-color: <?php echo $row_class;?>">
                                            <?php 
                                            if ($kk==1) {
                                            ?>  
                                            <td rowspan="<?php echo $rowspan;?>" align="center"><img class="sor_inventory_popup_image" src="<?php echo $valueip['image'];?>" width="<?php echo $valueip['width'];?>" height="<?php echo $valueip['height'];?>"></td>
                                            <?php }?>

                                            <td style="text-align:center;">
                                            <?php echo $valueip['sku'];?>
                                              
                                            </td> 
                                            <td style="text-align:center;"><?php echo $valueip['pieces'].' pieces';?>
                                              <br />
                                              <span class="price_value_cls">
                                            <?php echo $valueip['total_pur_amt'];?>
                                            </span>
                                            </td>

                                             <td style="text-align:center;">
                                            <?php 
                                             if ($valueip['purchase_return_pieces']!='' && $valueip['purchase_return_pieces']!='0') {
                                                echo $valueip['purchase_return_pieces'];
                                              }
                                            ?>
                                            </td>

                                            <td style="text-align:center;">
                                            <?php
                                            if($valueip['actual_pieces_sold']!='') {
                                             echo $valueip['actual_pieces_sold'].' pieces'; }?>
                                            <br />
                                            <span class="price_value_cls">
                                              <?php 
                                              if ($valueip['actual_amount_sold']!='' && $valueip['actual_amount_sold']!='Rs. 0.00') {
                                                echo $valueip['actual_amount_sold'];
                                              }
                                            ?>
                                            </span>
                                            </td>

                                             <td style="text-align:center;">
                                            <?php
                                            if($valueip['return_quantity']!='') {
                                             echo $valueip['return_quantity'].' pieces'; }?>
                                            <br />
                                            <span class="price_value_cls">
                                              <?php 
                                              if ($valueip['productReturnAmount']!='' && $valueip['productReturnAmount']!='Rs. 0.00') {
                                                echo $valueip['productReturnAmount'];
                                              }
                                            ?>
                                            </span>
                                            </td>
                                            
                                             <td style="text-align:center;">
                                            <?php
                                            if($valueip['pieces_sold']!='') {
                                             echo $valueip['pieces_sold'].' pieces'; }?>
                                            <br />
                                            <span class="price_value_cls">
                                              <?php 
                                              if ($valueip['amount_sold']!='' && $valueip['amount_sold']!='Rs. 0.00') {
                                                echo $valueip['amount_sold'];
                                              }
                                            ?>
                                            </span>
                                            </td>
                                            

                                          </tr>
                                          <?php
                                          $kk++;
                                      } 
                                    }
                                    ?>
                                  </tbody>
                                </table>
                                <?php 
                                }
                                ?>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                              </div>
                            </div>

                          </div>
                        </div>
                      <!-- modal end -->  
                      <?php 
                      $in++;  
                      }
                      ?>
                      <!-- popup work end -->
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