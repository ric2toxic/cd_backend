
<?php echo $header;?>
  <div id="page-wrapper">
    <!-- page title-->
    <div class="col-sm-12">
      <h1 class="page_title">Orders</h1>
    </div>
    <!-- page Contant-->
    <div class="col-sm-12">
      <ul class="nav nav-tabs profile_tebination">
        <li>
          <a href="<?php echo $pickup_requested_link; ?>" target="_blank">
            <?php echo $tab_pickup_requested; ?>
          </a>
        </li>
        <li>
          <a href="<?php echo $pickup_done_link; ?>" target="_blank">
            <?php echo $tab_pickup_done; ?>
          </a>
        </li>
        <li class="active">
          <a href="<?php echo $pickup_tentative_link; ?>">
            <?php echo $tab_tentative_orders; ?>
          </a>
        </li>
        <li>
          <a href="<?php echo $sor_order_link; ?>" target="_blank">
            <?php echo $tab_sor_orders; ?>
          </a>
        </li>
      </ul>

      <div class="col-sm-12 profile_detail panel-group">
        <div class="tab-content">
          <div id="pickup_requested" class="">
            <div class=" panel-default row">
              <div class="panel-body panel-group" id="accordion">
                <ul class="tentative_orders_box">
                  <li>These are not confirmed orders, but tentative orders placed by the buyers with wholesalebox.</li>
                  <li>These orders are not yet informed to you and processed, as we are confirming few things with the customer.</li>
                  <li>These orders are just an indication for you the upcoming stock requirement, so that you may get them ready, but please note that these are not yet confirmed and can be cancelled also.</li>
                </ul>
                <div class="clearfix"></div>
                <?php if ( (!empty($tentative_order) && count($tentative_order) > 15 ) || (!empty($filter_record_range) && $filter_record_range > 15)) { ?>
                <div class="filter_box_order pickup_done_records_box pd_record">
                  <div class="form-group pull-right">
                    <span><b>Records:  </b></span>
                    <ul>
                      <li><a href="<?php echo $record_range_15;?>">15</a></li>
                      <li><a href="<?php echo $record_range_20;?>">20</a></li>
                      <li><a href="<?php echo $record_range_25;?>">25</a></li>
                    </ul>
                  </div>
                  <div class="clearfix"></div>
                </div>
                <?php } ?>
                <table width="100%" class="orders_table">
                  <thead>
                    <tr>
                      <td>
                        <?php if ($sort == 'o.order_no') { ?>
                          <a href="<?php echo $sort_order_no; ?>" class="<?php echo strtolower($order); ?>">
                              <?php echo $column_order_no; ?>
                          </a>
                          <?php } else { ?>
                          <a href="<?php echo $sort_order_no; ?>">
                              <?php echo $column_order_no; ?>
                          </a>
                          <?php } ?>
                      </td>
                      <td>
                        <?php if ($sort == 'o.date_added') { ?>
                          <a href="<?php echo $sort_date; ?>" class="<?php echo strtolower($order); ?>">
                              <?php echo $column_date; ?>
                          </a>
                          <?php } else { ?>
                          <a href="<?php echo $sort_date; ?>">
                              <?php echo $column_date; ?>
                          </a>
                          <?php } ?>
                      </td>
                      <td>
                        <?php if ($sort == 'total') { ?>
                          <a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>">
                              <?php echo $column_total_amt; ?>
                          </a>
                          <?php } else { ?>
                          <a href="<?php echo $sort_total; ?>">
                              <?php echo $column_total_amt; ?>
                          </a>
                          <?php } ?>
                      </td>
                    </tr>
                  </thead>
                  <tbody>
                     <!-- <tr class="filter_space"><td colspan="3"></td></tr> -->
                     <!-- <tr class="filter_box_order">
                        <td><input type="text" name="filter_tentative_order_no" class="filter_input" value="<?php echo $filter_tentative_order_no; ?>"></td>
                        <td>
                            <button class="filter_search_btn btn btn-lg" id="button_filter_tantative">
                                <i class="fa fa-search" aria-hidden="true"></i> <?php echo $btn_search; ?>
                            </button>    
                        </td>
                        <td></td>   
                     </tr> -->
                     <tr class="filter_space"><td colspan="3"></td></tr>
                    <?php if (!empty($filters_tentative) ) { ?>
                    <tr class="filter_space"><td colspan="4"></td></tr>
                    <tr class="filter_select_box"><td colspan="6">
                      <span class="filter_select_section"><a href="<?php echo $clear_all_link; ?>">Clear all</a></span>
                      <?php foreach($filters_tentative as $key => $values) { ?>
                        <span class="filter_select_close">
                          <?php echo ucwords($values['title']); ?> : <?php echo $values['value']; ?>
                          <a href="<?php echo $values['url'];?>">
                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                          </a>
                        </span>
                      <?php } ?>
                    </tr>
                    <?php } ?>
                    <?php
                    if ( !empty($tentative_order)) { ?> 
                    <?php foreach( $tentative_order as $tentative_values) { ?>
                      <tr>
                        <td><a data-target="#<?php echo $tentative_values['order_no']; ?>" data-toggle="modal"><?php echo $tentative_values['order_no']; ?></a></td>
                        <td><?php echo $tentative_values['order_date_added']; ?></td>
                        <td><?php echo $tentative_values['total']; ?></td>
                      </tr>
                    <?php } ?>      
                    <?php } ?>
                  </tbody>
                </table>
              </div>
                <div class="row">
                  <div class="col-sm-6 text-left pickupdone_pagination"><?php echo $pagination; ?></div>
                  <div class="col-sm-6 text-right pickupdone_pagination_result"><?php echo $results; ?></div>
                </div>
            </div>
            
            <!-- Sets more popup -->
            <?php if( !empty($tentative_order ) ) { ?>
            <?php foreach($tentative_order as $pickup_values) { ?>
              <div id="<?php echo $pickup_values['order_no']; ?>" class="modal fade order_popup_section" role="dialog">
                <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close model_popup_close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <div class="title_right popup_tight_title">
                        <span class="oredr_right">Order No : <?php echo $pickup_values['order_no']; ?> </span>
                        <span class="oredr_right">Order Date : <?php echo $pickup_values['order_date_added'];?></span>
                      </div>
                      <div class="help_line_for_order"><i class="fa fa-phone" aria-hidden="true"></i> For help: 9116134791 | 9649558363</div>
                    </div>
                    <div class="modal-body">
                      <div class="order_detail_popup">
                        <table width="100%" class="order_detail_popup_table">
                          <thead>
                              <tr>
                                  <td>Product Image</td>
                                  <td>Name</td>
                                  <td>SKU</td>
                                  <td>Set Description</td>
                                  <td>Sets</td>
                                  <td>No. of Pieces</td>
                                  <td>Price/Piece</td>
                                  <td>Product Amount</td>
                                  <td>Tax Rate</td>
                                  <td>Tax Amount</td>
                                  <td>Total Amount</td>
                              </tr>
                          </thead>
                          <tbody>
                              <?php
                              
                               if ( !empty($pickup_values['product_data']) ) { 
                                      $total_amount = 0;
                                      foreach( $pickup_values['product_data'] as $suborder_key => $suborders_product_data ) {
                                        foreach( $suborders_product_data as $product_details){
                              ?>
                                  <tr>
                                      <td class="left">
                                          <a href="<?php echo $product_details['href']; ?>" target="_blank">
                                              <img src="<?php echo $product_details['image']; ?>" alt="<?php echo $product_details['name']; ?>" title="<?php echo $product_details['name']; ?>" />
                                          </a>
                                      </td>
                                      <td><?php echo $product_details['name']; ?></td>
                                      <td><?php echo $product_details['seller_sku']; ?></td>
                                      <td><?php echo $product_details['comment']; ?></td>
                                      <td><?php echo $product_details['quantity']; ?></td>
                                      <td><?php echo $product_details['no_of_piece']; ?></td>
                                      <td><?php echo $product_details['price_per_piece'];?></td>
                                      <td><?php echo $product_details['product_amount']; ?></td>
                                      <td><?php echo $product_details['vat_cst_rate']; ?></td>
                                      <td><?php echo $product_details['vat_cst_amount'];?></td>
                                      <td><?php echo $product_details['total_amount']; ?></td>             
                                  </tr>
                              <?php $total_amount += $product_details['total_amount']; } ?>    
                              <?php } }?>                              
                          </tbody>
                        </table>
                      </div>
                      <div class="order_popup_footer"><p>Total : <?php echo round($total_amount,2);?></p></div>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>
            <?php } ?>             
          </div>                 
        </div>
      </div>
    </div>
  </div> 
<?php echo $footer; ?>

<!-- <script type="text/javascript">
// $('#button_filter_tantative').click(function(){   

//     var url = "index.php?route=seller_panel/account-order";

//     var filter_tentative_order_no = $('input[name=\'filter_tentative_order_no\']').val();

//     if (filter_tentative_order_no) {
//         url += '&filter_tentative_order_no=' + encodeURIComponent(filter_tentative_order_no);
//     }
    
//     url += '&filter=tentative-orders#tentative_orders';
//         // alert(url); return false;
//     location = url;
// });

// $('input').on('keypress',function(e){
//     if (e.keyCode == 13) {
//         $('#button_filter_tantative').trigger('click');
//     }
// });
</script> -->