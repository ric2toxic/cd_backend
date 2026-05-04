
<?php echo $header;?>
  <div id="page-wrapper">
    <!-- page title-->
    <div class="col-sm-12">
      <h1 class="page_title">Orders</h1>
    </div>
    <!-- page Contant-->
    <div class="col-sm-12">
      <ul class="nav nav-tabs profile_tebination">
        <li class="active">
          <a href="<?php echo $pickup_requested_link; ?>">
            <?php echo $tab_pickup_requested; ?>
          </a>
        </li>
        <li>
          <a href="<?php echo $pickup_done_link; ?>" target="_blank">
            <?php echo $tab_pickup_done; ?>
          </a>
        </li>
        <li>
          <a href="<?php echo $pickup_tentative_link; ?>" target="_blank">
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
        <!-- kuldeep work(start)-->
        <div class="filter_box_order pickup_done_records_box pd_record order_pockupdone_filter_title_box">
            <h3 class="order_pockupdone_filter_title" data-toggle="collapse" data-target="#order_pockuprequested_filters" aria-expanded="true"> 
                <span class="fa fa-search"></span> Search 
                <small class="filter_text_pickupdone">(Click here to refine results using various option such as Invoice No, Invoice Date, Order No, Amount etc.)</small>
            </h3>
        </div>
        <div id="order_pockuprequested_filters" class="collapse in" aria-expanded="true" style="">
            <div class="order_pockupdone_filters_section">
                <div class="form-inline">
                    <div class="form-group opd_filters_box">
                      <label>Order No. </label>
                      <input type="text" name="filter_order_no_requested" class="filters_input_section" placeholder="<?php echo $entry_order_no; ?>" value="<?php echo $filter_order_no_requested; ?>">
                    </div>
                    <div class="form-group opd_filters_box">
                        <label>Amount Range </label>
                        <div class="filter_box_order_range">
                          <div class="form-group range_requested" data-toggle="#total_price">
                            <div class="input-group date">
                              <input type="text"  value="<?php echo !empty($filter_order_amount_from) ? $filter_order_amount_from .'-'.$filter_order_amount_to : '' ; ?>" placeholder="<?php echo $entry_sale_price; ?>" id="input-date-added" class="form-control price_range_total" readonly />
                              <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-caret-right" aria-hidden="true"></i></button>
                              </span>
                            </div>
                          </div>
                          <div class="price_popup" id="total_price">
                            <i data-toggle="#total_price" class="fa fa-times range_requested close_btn_price_popup" aria-hidden="true"></i>
                            <h3>Select price pange</h3>
                            <div class="price_pange_popup_box">
                              <?php echo $entry_price_from; ?>
                              <input type="text" name="filter_order_amount_from" class="price_pange_input" value="<?php echo $filter_order_amount_from; ?>" >  
                              <?php echo $entry_price_to; ?>
                              <input type="text" name="filter_order_amount_to" class="price_pange_input" value="<?php echo $filter_order_amount_to; ?>">
                              <button type="button" class="price_apply_btn requested_sale_apply_btn"> <?php echo $btn_apply; ?> </button>
                            </div>
                          </div>
                        </div>
                    </div>
                    <div class="form-group opd_filters_box">
                      <label>Status </label>
                      <select name="edit_type_status" class="filters_input_section">
                        <?php $range = array('invoiced'=>'Invoiced', 'partial'=>'Partial', 'cancelled'=>'Cancelled', 'pending'=>'Pending');?>
                        <?php $bck_color = array('Invoiced'=>'green_color', 'Partial'=>'orange_color', 'Cancelled'=>'red_color', 'Pending'=>'yellow_color');?>
                        <option value="*">--Select Status--</option>
                        <?php foreach( $range as $key => $value) { ?>
                          <option value="<?php echo $key; ?>"<?php echo ((!empty($edit_type_status)) && $edit_type_status == $key) ? 'selected' : ''; ?> class="<?php echo $bck_color[$value];?>"><?php echo $value; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-inline">
                  <div class="form-group opd_filters_box">
                      <label>Date From </label>
                      <input type="text" name="datefilter_requested_from" placeholder="<?php echo $entry_invoice_date_from;?>" class="filter_datepicar reportrange filters_input_section" value="<?php echo $filter_order_processing_date_from ; ?>" readonly />
                  </div>
                  <div class="form-group opd_filters_box">
                      <label>Date To </label>
                      <input type="text" name="datefilter_requested_to" placeholder="<?php echo $entry_invoice_date_to;?>" class="filter_datepicar reportrange filter_datepicar_right filters_input_section" value="<?php echo $filter_order_processing_date_to ; ?>" readonly />
                  </div>
                  <div class="form-group opd_filters_box">
                    <label>Records range</label>
                    <select name="filter_record_range" class="filters_input_section">
                      <?php $range = array('all'=>'ALL', '15'=>'15', '20'=>'20', '25'=>'25','50'=>'50');?>
                      <option value="*">--Select Records--</option>
                      <?php foreach( $range as $key => $value) { ?>
                        <option value="<?php echo $key; ?>" ><?php echo $value; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="clearfix"></div>             
                <div class="form-inline text-right">
                  <div class="form-group opd_filters_box button-margin-set">
                    <button class="filter_search_btn btn btn-lg" id="button_filter_requested">
                      <i class="fa fa-search" aria-hidden="true"></i> <?php echo $btn_search; ?>
                    </button>
                  </div>
                </div>
            </div>
        </div>
        <!-- kuldeep work(End)-->
        <div class="tab-content">
          <div id="pickup_requested" class="">
            <div class=" panel-default row">
              <div class="panel-body panel-group" id="accordion">
                <table width="100%" class="orders_table pickup_requested_table">
                  <thead>
                    <tr>
                      <td width="15%"><?php echo $column_order_no; ?></td>
                      <td width="15%"><?php echo 'Suborder ID'; ?></td>
                      <td width="15%">
                        <?php if ($sort == 'order_processing_date') { ?>
                              <a href="<?php echo $sort_order_proccessing_date; ?>" class="<?php echo strtolower($order); ?>">
                                  <?php echo $column_order_date; ?>
                              </a>
                              <?php } else { ?>
                              <a href="<?php echo $sort_order_proccessing_date; ?>">
                                  <?php echo $column_order_date; ?>
                              </a>
                        <?php } ?>
                      </td>
                      <td width="15%"><?php echo $column_order_amount; ?></td>
                      <td width="15%"><?php echo $column_status; ?></td>
                      <td width="10%">&nbsp;</td>
                   </tr>
                  </thead>
                  <!-- <tbody> -->
                    <?php if (!empty($filters_requested) ) { ?>
                    <tr class="filter_space"><td colspan="6"></td></tr>
                    <tr class="filter_select_box"><td colspan="6">
                      <span class="filter_select_section"><a href="<?php echo $clear_all_link; ?>">Clear all</a></span>
                      <?php foreach($filters_requested as $key => $values) { ?>
                        <span class="filter_select_close">
                          <?php echo ucwords($values['title']); ?> : <?php echo $values['value']; ?>
                          <a href="<?php echo $values['url'];?>">
                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                          </a>
                        </span>
                      <?php } ?>
                    </tr>
                    <?php } ?>
                    <tr class="filter_space"><td colspan="4"></td></tr>
                    <?php if ( !empty($pickup_requested) ) {
                      
                    ?> 
                    <?php foreach($pickup_requested as $request_valued ) {  

                      $row_group_class = '';
                      if (count($request_valued) > 1) {
                        $row_group_class = "group_rows";
                      }
                    ?>
                    

                    <tbody class="<?php echo $row_group_class;?>" >
                        
                          <?php foreach($request_valued as $suborder_id_key => $request_value){ 
                                  $total_products = 0;
                                  if(!empty($request_value['pending_orders'])){
                                    $total_products = count($request_value['pending_orders'][$suborder_id_key]);
                                  }                                 
                          ?>
                         
                          <tr>
                            <td width="15%">
                              <a data-target="#<?php echo $request_value['suborder_id']; ?>"
                                 data-order-id="<?php echo $request_value['order_id']; ?>"
                                 data-suborder-id="<?php echo $request_value['suborder_id']; ?>"
                                 data-toggle="modal" 
                                 class="data_model_button" 
                                 data-total-products="<?php echo $total_products;?>">

                                <?php echo $request_value['order_no']; ?>
                              </a>
                              <?php

                              if (isset($request_value['seller_sor_product']) && count($request_value['seller_sor_product'])>0) {

                                echo '<br><span style="padding-left:5px;padding-right:5px;background-color:#f49090; font-size:9px;">Contains SOR</span>';
                              }
                              ?>
                            </td>
                            <td width="15%"><?php echo $request_value['suborder_id'];?></td>
                            <td width="15%"><?php echo $request_value['order_processing_date'];?></td>
                            <td width="15%"><?php echo $request_value['total']; ?></td>
                            <td width="15%">
                              <span class="common_invoice_status <?php echo $bck_color[$request_value['edit_type_status']]; ?>">
                                <?php echo $request_value['edit_type_status']; ?>
                              </span>
                            </td>       
                            <td width="10%">
                              <button class="btn btn-primary btn-sm data_model_button" 
                                      data-order-id="<?php echo $request_value['order_id']; ?>" 
                                      data-suborder-id="<?php echo $request_value['suborder_id']; ?>" 
                                      data-target="#<?php echo $request_value['suborder_id']; ?>" 
                                      data-toggle="modal" 
                                      data-total-products="<?php echo $total_products;?>">
                                Check order detail</button>
                            </td>
                          </tr>
                        
                          <?php } ?>
                        
                     </tbody>
                    <?php } ?>
                    <?php } ?>
                 <!--  </tbody>        -->         
                </table>
              </div>
                <div class="row">
                  <div class="col-sm-6 text-left pickupdone_pagination"><?php echo $pagination; ?></div>
                  <div class="col-sm-6 text-right pickupdone_pagination_result"><?php echo $results; ?></div>
                </div>
            </div>
            <!-- Sets more popup  tabindex="-1" -->
            <?php if( !empty($pickup_requested ) ) { ?>
            <?php foreach($pickup_requested as $pickup_valued) {
                    foreach($pickup_valued as $pickup_values){ 
                      $class_PO = "";
                      $class_SNG = "";
                      $class_SIG = "";
                      $class_SOR = "";

                      $cFlag = 0;
                      $pending_orders = array();
                      if(!empty($pickup_values['pending_orders'])){
                        $pending_orders = $pickup_values['pending_orders']; 
                        if($cFlag == 0){
                          $class_PO = "active";
                          $cFlag = 1;
                        }        
                      }

                      $sllr_invc_genrted = array();
                      if(!empty($pickup_values['sllr_invc_genrted'])){
                        $sllr_invc_genrted = $pickup_values['sllr_invc_genrted']; 
                         if($cFlag == 0){
                          $class_SIG = "active";
                          $cFlag = 1;
                        }       
                      }

                      $seller_not_given = array();
                      if(!empty($pickup_values['seller_not_given'])){
                        $seller_not_given = $pickup_values['seller_not_given']; 
                         if($cFlag == 0){
                          $class_SNG = "active";
                          $cFlag = 1;
                        }        
                      }

            ?>
              <div class="modal fade order_popup_section" id="<?php echo $pickup_values['suborder_id']; ?>" role="dialog">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close model_popup_close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                   <!--    <h4 class="modal-title popup_title" id="myModalLabel">Orders Details</h4> -->
                      <div class="title_right popup_tight_title">
                        <span class="oredr_right">Order No : <?php echo $pickup_values['suborder_id']; ?> </span>
                        <span class="oredr_right">Order Processing Date : <?php echo $pickup_values['order_processing_date'];?></span>
                        <input type="hidden" name="hidden_seller_id" value="<?php echo $seller_id; ?>">
                      </div>
                      <div class="help_line_for_order"><i class="fa fa-phone" aria-hidden="true"></i> For help: 9116134791 | 9649558363</div>
                    </div>
                    <div class="modal-body">
                      <div role="tabpanel">
                        <ul class="nav nav-tabs profile_tebination subtable_box" role="tablist">
                          <!-- Customer Placed Orders  -->
                          <?php /* if(!empty($product_data) ) { ?>
                          <li class="<?php echo $class_PD;?>">
                            <a href="#orders_<?php echo $pickup_values['order_id'];?>" aria-controls="Orders" role="tab" data-toggle="tab">Orders</a>
                          </li>
                          <?php } */ ?>

                          <!-- Seller Later Dispatch  --> 
                          <?php if(!empty($pending_orders) ) { ?>
                          <li class="<?php echo $class_PO;?> sho_info_tab pending_order_tab">
                            <a href="#pending_orders_<?php echo $pickup_values['suborder_id']; ?>" aria-controls="pending_orders" role="tab" data-toggle="tab">PENDING</a>
                          </li>
                          <?php } ?>
                          
                          <!-- Seller Invoice generated  --> 
                          <?php if(!empty($sllr_invc_genrted) ) { ?>
                            <?php $i = 1; 
                                  foreach($sllr_invc_genrted as $sllr_inv_id_key => $values) { 
                                  $pickup_status = $values['pickup_status'];

                            ?>
                              <li class="<?php echo ($i==1)?$class_SIG:"";?> sho_info_tab seller_invoiced_tab <?php echo ($pickup_status) ? 'has_edit_option': '';?>" data-seller-invoice-id="<?php echo $sllr_inv_id_key;?>" data-suborder-id="<?php echo $pickup_values['suborder_id']; ?>">
                                <a href="#seller_invoice_<?php echo $sllr_inv_id_key;?>" aria-controls="sllr_invc_genrted" role="tab" data-toggle="tab" ><?php echo $values['invoice_no'];?> </a>
                              </li>
                            <?php $i++; } ?>
                          <?php } ?>

                          <!-- Seller Not Given Order Products -->
                          <?php if(!empty($seller_not_given) ) { ?>
                            <li class="<?php echo $class_SNG;?> sho_info_tab seller_not_given_tab">
                              <a href="#goods_not_given_<?php echo $pickup_values['suborder_id'];?>" aria-controls="goods_not_given" role="tab" data-toggle="tab">GOODS NOT GIVEN </a>
                            </li>
                          <?php } ?>                          
                          
                        </ul>
                        <div class="tab-content popup_content_table">
                          <!-- Seller Later Dispatch (pending)  -->                   
                          <?php if ( !empty($pending_orders) ) { $total_amount = 0; $total_products = 0; ?>
                          <div class="tab-pane <?php echo $class_PO;?>" id="pending_orders_<?php echo $pickup_values['suborder_id']; ?>">
                            <div class="tab_order_info">
                              <div class="col-sm-12 pd_approve_section">
                                <p>To generate an Invoice, Please approve below products using these options.</p>
                                  <?php $total_remaining_value = 0 ; 
                                    if ( !empty($pending_orders) ){
                                      foreach($pending_orders as $key => $product_data)
                                      $total_remaining_value = count($product_data);
                                    }

                                    if( $total_remaining_value ) {
                                  ?>
                                      <div class="pd_remaining_box text_blink">
                                        <span id="total_products_<?php echo $pickup_values['suborder_id'];?>">
                                          <?php echo $total_remaining_value ; ?>
                                        </span> Products are remaining. Please Mark!</div>
                                  <?php  } ?> 
                                <div class="clearfix"></div>
                              </div> 
                              <ul>
                                <li class="complete"><i class="fa fa-check green"></i> Complete (You are shipping all requested pieces)</li>
                                <li class="edit"><i class="fa fa-pencil blue" aria-hidden="true"></i> Partial(you can select how many pieces you will ship) </li>
                                <li class="cancel"><i class="fa fa-times red"></i> None (Out of stock. you will not ship this product) </li>
                                <li class="processing"><i class="fa fa-truck orange"></i> Later Dispatch </li>
                                <li class="edit"><i class="fa fa-undo blue"></i> Undo</li>
                              </ul>                            
                              <div class="clearfix"></div>
                            </div>
                            <div class="pickup_done_records_box">
                              <div class="invoice_popup_box_top">
                                <div class="hidden form_box form-group inv_no_inv_date_form_<?php echo $pickup_values['suborder_id'];?>" 
                                     id="inv_no_inv_date_form_<?php echo $pickup_values['suborder_id'];?>">
                                  <span>Enter Invoice Number & Date :-</span>
                                   <label>Invoice Number</label>
                                   <input type="text" name="invoice_number" id="invoice_number_<?php echo $pickup_values['suborder_id'];?>" placeholder="Invoice Number" class="invoice_input" value="" disabled>
                                   <label>Date</label>
                                   <select class="filters_input_section select_rang_hight" id="invoice_date_<?php echo $pickup_values['suborder_id'];?>" name="invoice_date" disabled readonly  data-order-id="<?php echo $pickup_values['order_id'];?>"  data-order-processing-date="<?php echo $pickup_values['order_processing_date'];?>" >
                                      <option value="">--Select--</option>
                                      <?php foreach($pickup_values['date_ranges'] as $date_value){ ?>
                                      <option value="<?php echo $date_value;?>"><?php echo $date_value;?></option>
                                      <?php } ?>
                                   </select>
                                </div>
                              </div>
                              <div class="invoice_icon">
                                <a href="javascript:void(0);" 
                                   data-order-id="<?php echo $pickup_values['order_id'];?>" 
                                   class="hidden btn btn-primary btn-sm invoice_icon_btn invc_dwnlod_<?php echo $pickup_values['suborder_id']; ?>" 
                                   title="View &amp; Download Invoice"  class="invoice_download_btn" 
                                   id="invc_dwnlod_<?php echo $pickup_values['suborder_id']; ?>" 
                                   data-suborder-id="<?php echo $pickup_values['suborder_id']; ?>" 
                                   data-order-id="<?php echo $pickup_values['order_id']; ?>"
                                   data-order-no="<?php echo $pickup_values['order_no']; ?>"
                                   disabled>
                                   <i class="fa fa-file-text"></i> Submit Invoice Detail
                                </a>
                              </div>
                              <div class="clearfix"></div>
                              <div class="invoice_popup_little hidden" id="invoice_popup_<?php echo $pickup_values['suborder_id'];?>">
                                <i data-toggle="" 
                                   data-order-no="<?php echo $pickup_values['order_no'];?>" 
                                   data-order-id="<?php echo $pickup_values['order_id'];?>" 
                                   data-suborder-id="<?php echo $pickup_values['suborder_id'];?>" 
                                   class="fa fa-times range_requested close_btn_invoice_popup" 
                                   aria-hidden="true"></i>
                                <div class="col-sm-12 nopadding min_popup_info_<?php echo $pickup_values['suborder_id'];?>"></div>
                                <div class="col-sm-12 invoice_popup_little_btn">
                                  <button class="btn pull-left invoice_popup_little_btn_cancel" 
                                          data-order-no="<?php echo $pickup_values['order_no']; ?>" 
                                          type="button" 
                                          data-order-id="<?php echo $pickup_values['order_id']; ?>"
                                          data-suborder-id="<?php echo $pickup_values['suborder_id']; ?>">CANCEL</button>
                                  <button class="btn btn-primary pull-right submit_invoice_button" 
                                          type="button" 
                                          data-order-no="<?php echo $pickup_values['order_no']; ?>"
                                          data-order-id="<?php echo $pickup_values['order_id']; ?>" 
                                          data-suborder-id="<?php echo $pickup_values['suborder_id']; ?>" >
                                          <i class="fa fa-file-text"></i> Submit</button>
                                </div>
                                <div class="clearfix"></div>
                              </div> 
                            </div>

                            <div class="col-sm-12 pd_popup_table_contant">
                               <div class="pd_popup_thead">
                                 <div class="pd_poup_head_td col-sm-1">Action</div>
                                 <div class="pd_poup_head_td  col-sm-3">Product</div>
                                 <div class="pd_poup_head_td col-sm-2">Set Description</div>
                                 <div class="pd_poup_head_td col-sm-1">No. of Pieces</div>
                                 <div class="pd_poup_head_td col-sm-1">Price / Piece</div>
                                 <div class="pd_poup_head_td col-sm-1">Product Amount</div>
                                 <div class="pd_poup_head_td col-sm-1">Tax Rate</div>
                                 <div class="pd_poup_head_td col-sm-1">Tax Amount</div>
                                 <div class="pd_poup_head_td child_box col-sm-1">Total Amount</div>
                                 <div class="clearfix"></div>
                               </div>
                               <div class="pd_popup_body">
                                <?php foreach( $pending_orders as $suborder_id_key => $suborder_products_data ) { ?>
                                  <input type="hidden" name="product_suborder_id" value="<?php echo $suborder_id_key; ?> " class="pending_order_<?php echo $pickup_values['order_id']; ?>" data-suborder-id="<?php echo $suborder_id_key; ?>">
                                  <?php foreach($suborder_products_data as $product_details){ ?>
                                    <fieldset class="edit_order_<?php echo $pickup_values['suborder_id']; ?> edit_track_<?php echo $pickup_values['suborder_id'];?>" 
                                             data-change="false" id="block_products_<?php echo $pickup_values['order_id'];?>_<?php echo $product_details['order_product_id'];?>"
                                             data-order-product-id="<?php echo $product_details['order_product_id'];?>"
                                             data-product-id="<?php echo $product_details['product_id'];?>">
                                       <div class="pd_poup_body_td content col-sm-1">
                                          <div class="pd_action_btn_box">
                                            <ul>
                                                <li class="checkbox_information_agree action_table_icon" 
                                                   id="checkbox_information_agree_<?php echo $product_details['order_product_id']; ?>">
                                                  <a href="javascript:void(0);" class="pd_popup_selected">
                                                    <label> 
                                                      <input value="" 
                                                             type="checkbox" 
                                                             id="list_sub_order_product_id_<?php echo $product_details['order_product_id'];?>" 
                                                             class="popup_products"
                                                             data-order-id="<?php echo $pickup_values['order_id'];?>" 
                                                             data-suborder-id="<?php echo $pickup_values['suborder_id'];?>" 
                                                             data-order-product-id="<?php echo $product_details['order_product_id'];?>"
                                                             data-product-id="<?php echo $product_details['product_id'];?>"
                                                             data-total-piece="<?php echo $product_details['no_of_piece'];?>"
                                                             data-total-amount="<?php echo $product_details['total_amount'];?>"
                                                             data-product-amount="<?php echo $product_details['product_amount'];?>"
                                                             data-tax-amount="<?php echo $product_details['vat_cst_amount'];?>">
                                                      <div class="control__indicator"></div>
                                                    </label>
                                                  </a>
                                                </li>

                                               <?php if($product_details['partial'] && $product_details['no_of_piece'] > 1)
                                                 { ?> 
                                                <li id="checkbox_information_edit_<?php echo $product_details['order_product_id']?>" 
                                                    class="checkbox_information_edit product_quantity action_table_icon" 
                                                    data-order-product-id="<?php echo $product_details['order_product_id']; ?>" 
                                                    data-product-id="<?php echo $product_details['product_id']; ?>"
                                                    data-order-id="<?php echo $pickup_values['order_id'];?>"
                                                    data-suborder-id="<?php echo $pickup_values['suborder_id'];?>"
                                                    data-product-id="<?php echo $product_details['product_id'];?>">
                                                  <a href="javascript:void(0);">
                                                    <i class="fa fa-pencil blue" aria-hidden="true"></i>
                                                    <input type="checkbox" 
                                                           name="" 
                                                           class="hidden" 
                                                           id="information_edit_<?php echo $product_details['order_product_id']?>">
                                                  </a>
                                                </li>
                                                <?php } ?>
 
                                                <li id="checkbox_information_decline_<?php echo $product_details['order_product_id']?>" 
                                                    class="checkbox_information_decline action_table_icon" 
                                                    data-order-product-id="<?php echo $product_details['order_product_id'];?>"
                                                    data-order-id="<?php echo $pickup_values['order_id'];?>"
                                                    data-suborder-id="<?php echo $pickup_values['suborder_id'];?>"
                                                    data-product-id="<?php echo $product_details['product_id'];?>">
                                                  <a href="javascript:void(0);">
                                                    <i class="fa fa-times red" aria-hidden="true"></i>
                                                    <input type="checkbox" 
                                                           name="" 
                                                           class="hidden" 
                                                           id="information_decline_<?php echo $product_details['order_product_id']?>">
                                                  </a>
                                                </li>
                                                <li id="checkbox_information_dispatch_<?php echo $product_details['order_product_id']?>" 
                                                    class="checkbox_information_dispatch action_table_icon" 
                                                    data-order-product-id="<?php echo $product_details['order_product_id'];?>"
                                                    data-order-id="<?php echo $pickup_values['order_id'];?>"
                                                    data-suborder-id="<?php echo $pickup_values['suborder_id'];?>">
                                                  <a href="javascript:void(0);">
                                                    <i class="fa fa-truck orange" aria-hidden="true"></i>
                                                    <input type="checkbox" 
                                                         name="" 
                                                         class="hidden" 
                                                         id="information_dispatch_<?php echo $product_details['order_product_id']?>">
                                                  </a>
                                                </li>
                                                <li id="checkbox_information_undo_<?php echo $product_details['order_product_id']?>" 
                                                    class="checkbox_information_undo action_table_icon" 
                                                    data-order-product-id="<?php echo $product_details['order_product_id'];?>"
                                                    data-order-id="<?php echo $pickup_values['order_id'];?>"
                                                    data-suborder-id="<?php echo $pickup_values['suborder_id'];?>"
                                                    data-product-id="<?php echo $product_details['product_id']; ?>"
                                                    data-total-piece="<?php echo $product_details['no_of_piece'];?>"
                                                    data-total-amount="<?php echo $product_details['total_amount'];?>"
                                                    data-product-amount="<?php echo $product_details['product_amount'];?>"
                                                    data-tax-amount="<?php echo $product_details['vat_cst_amount'];?>">
                                                  <a href="javascript:void(0);">
                                                    <i class="fa fa-undo blue" aria-hidden="true"></i>
                                                  </a>
                                                </li>
                                            </ul>
                                          </div> 
                                       </div>
                                       <div class="pd_poup_body_td content col-sm-3">
                                          <div class="pd_img_box">
                                            <a href="<?php echo $product_details['href']; ?>" target="_blank">
                                              <img src="<?php echo $product_details['image']; ?>" alt="<?php echo $product_details['name']; ?>" title="<?php echo $product_details['name']; ?>" width="74" height="111" />
                                            </a>
                                          </div>
                                          <div class="pd_popup_sq_code"><?php echo $product_details['seller_sku']; ?></div>
                                          <span class="pd_poup_title"><a href="<?php echo $product_details['href']; ?>" data-toggle="popover"  data-trigger="hover" data-content="<?php echo $product_details['name']; ?>" target="_blank"><?php echo $product_details['name']; ?></a></span>
                                      </div>
                                      <div class="pd_poup_body_td col-sm-2"><?php echo $product_details['comment']; ?></div>
                                      <div class="pd_poup_body_td col-sm-1" >
                                        <span class="pd_pieces_box label label-danger quantity_label" id="product_quantity_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>"><?php echo $product_details['no_of_piece']; ?></span>
                                        <?php if($product_details['partial'])
                                                 { ?> 
                                        <span id="prod_quantity_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>" style="display: none;">
                                          <input type="text" 
                                                 name="product_quantity_update"
                                                 class="product_quantity" 
                                                 id="input_quantity_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>" 
                                                 data-order-product-id="<?php echo $product_details['order_product_id']; ?>" 
                                                 data-product-id="<?php echo $product_details['product_id']; ?>" 
                                                 value="<?php echo $product_details['no_of_piece']; ?>" 
                                                 style="width: 50px;" 
                                                 data-old-value="<?php echo $product_details['no_of_piece']; ?>">
                                              <span class="quantity_save_input btn btn-primary btn-xs" 
                                                    id="quantity_save_input_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>" 
                                                    data-order-product-id="<?php echo $product_details['order_product_id']; ?>" 
                                                    data-product-id="<?php echo $product_details['product_id']; ?>"
                                                    data-order-id="<?php echo $pickup_values['order_id'];?>"
                                                    data-suborder-id="<?php echo $pickup_values['suborder_id'];?>"
                                                    data-total-piece="<?php echo $product_details['no_of_piece'];?>"
                                                    data-total-amount="<?php echo $product_details['total_amount'];?>"
                                                    style="margin-top: 5px;" >
                                                Update
                                              </span>
                                              <span class="quantity_close_input btn btn-danger btn-xs"
                                                    data-order-product-id="<?php echo $product_details['order_product_id']; ?>" 
                                                    data-product-id="<?php echo $product_details['product_id']; ?>"
                                                    style="margin-top: 5px;">
                                                Cancel
                                              </span>
                                        </span>
                                        <?php } ?>
                                      </div>
                                       <div class="pd_poup_body_td col-sm-1 price_per_piece_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>"><?php echo $product_details['price_per_piece'];?></div>
                                       <div class="pd_poup_body_td col-sm-1 prod_amt_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>"><?php echo $product_details['product_amount']; ?></div>
                                       <div class="pd_poup_body_td col-sm-1 tax_rate_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>" data-tax-rate="<?php echo trim(str_replace('%','',$product_details['vat_cst_rate'])); ?>"><?php echo $product_details['vat_cst_rate']; ?></div>
                                       <div class="pd_poup_body_td col-sm-1 tax_amt_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>"><?php echo $product_details['vat_cst_amount'];?></div>
                                       <div class="pd_poup_body_td col-sm-1 total_prod_amt_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>"><?php echo $product_details['total_amount']; ?></div>
                                       <div class="clearfix"></div>
                                    </fieldset> 
                                  <?php $total_amount += $product_details['total_amount']; $total_products++;} ?>
                                <?php } ?>    
                                  
                               </div>
                               <div class="pd_popup_table_footer">
                                <div class="col-sm-4 nopadding">
                                  <!--<span id="total_products_<?php echo $pickup_values['order_id'];?>"><?php //echo $total_products; ?></span> Products are remaining -->
                                </div>
                                <div class="col-sm-3 table_total_box">
                                  <span id="table_total_box_<?php echo $pickup_values['suborder_id'];?>">
                                    Total : <?php echo $this->currency->format(round($total_amount,2),'INR','1');?>
                                  </span>
                                </div>
                                <div class="clearfix"></div>
                               </div>           
                            </div>
                             
                            <div class="clearfix"></div>
                          </div>
                          <?php } ?>
                          
                          <!-- Seller Invoice generated  -->                   
                          <?php if(!empty($sllr_invc_genrted)  ) {  ?>
                            <?php 
                                  $i = 1; 
                                  foreach($sllr_invc_genrted as $sllr_inv_id_key => $seller_product_values) {
                                  $pickup_status = $seller_product_values['pickup_status'];
                                  $edit_invoice_available = $seller_product_values['edit_invoice_available'];
                                  $invoice_date = $seller_product_values['date'];

                             ?>
                              <div class="seller_invoices_tab tab-pane <?php echo ($i==1)?$class_SIG:'';?>" id="seller_invoice_<?php echo $sllr_inv_id_key;?>">
                                <div class="pickup_done_records_box">
                                  <div class="col-sm-9">
                                    <div class="invoiced_popup_box_top">
                                      <div class="hidden form_box invoiced_update_block_<?php echo $sllr_inv_id_key?>" 
                                           id="">
                                        <span>Enter Invoice Number & Date :-</span>
                                         <label>Invoice Number</label>
                                         <input type="text" 
                                                name="invoice_number" 
                                                id="invoice_number_<?php echo $pickup_values['suborder_id'];?>" 
                                                placeholder="Invoice Number" 
                                                class="invoice_input invoiced_input_<?php echo $sllr_inv_id_key; ?>" 
                                                value="<?php echo $seller_product_values['invoice_no'];?>" 
                                                disabled>
                                         <label>Date</label>
                                         <select class="filters_input_section 
                                                        select_rang_hight 
                                                        filters_input_section_<?php echo $sllr_inv_id_key; ?>" 
                                                 id="invoice_date_<?php echo $pickup_values['suborder_id'];?>" 
                                                 name="invoice_date" 
                                                 disabled 
                                                 readonly
                                                 data-order-id="<?php echo $pickup_values['order_id'];?>"
                                                 data-order-processing-date="<?php echo $pickup_values['order_processing_date'];?>" >
                                            <option value="">--Select--</option>
                                            <?php foreach($pickup_values['date_ranges'] as $date_value){ ?>
                                            <option value="<?php echo $date_value;?>"<?php echo ($date_value==$invoice_date) ? 'selected' : '' ?>><?php echo $date_value;?></option>
                                            <?php } ?>
                                         </select>
                                         <input type="hidden" class="input_hidden_field_<?php echo $sllr_inv_id_key; ?>" value="<?php echo $seller_product_values['date'];?>">
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-sm-3">
                                    <div class="edit_invc_and_dwnlod_invce_blck" id="edit_inv_dwnlod_inv_<?php echo $sllr_inv_id_key; ?>">
                                      <?php if($pickup_status && $edit_invoice_available) { ?>
                                      <button type="button" class="btn btn-warning btn-sm edit_invoice_order" id="edit_invoice_<?php echo $sllr_inv_id_key; ?>" data-seller-invoice-id="<?php echo $sllr_inv_id_key; ?>">Change Invoice</button>
                                      <?php } ?>
                                      <a href="<?php echo $seller_product_values['invoice_pdf'];?>" title="View & Download Invoice" class="btn btn-success btn-xs btn-view-invoice"> <i class="fa fa-file-text"></i> Download Invoice</a>
                                    </div>
                                    <div class="edit_invoice_order_block">
                                      <button type="button" 
                                              class="btn btn-success btn-sm edit_invoice_save"
                                              id="edit_inv_save_<?php echo $sllr_inv_id_key; ?>"
                                              data-order-id="<?php echo $pickup_values['order_id'];?>" 
                                              data-suborder-id="<?php echo $pickup_values['suborder_id'];?>"
                                              data-seller-id="<?php echo $seller_id; ?>"
                                              data-seller-invoice-id="<?php echo $sllr_inv_id_key; ?>">Save</button>
                                      <button type="button" 
                                              class="btn btn-danger btn-sm edit_inv_cancel"
                                              id="edit_inv_cancel_<?php echo $sllr_inv_id_key; ?>"
                                              data-order-id="<?php echo $pickup_values['order_id'];?>" 
                                              data-suborder-id="<?php echo $pickup_values['suborder_id'];?>"
                                              data-sllr-inv-id="<?php echo $sllr_inv_id_key; ?>" >Cancel Change Invoice</button>
                                    </div>
                                  </div>
                                 <div class="clearfix"></div>
                                </div>

                                <div class="col-sm-12 pd_popup_table_contant">
                                   <div class="pd_popup_thead">                                    
                                     <div class="pd_poup_head_td  col-sm-1">&nbsp;</div>
                                     <div class="pd_poup_head_td  col-sm-3">Product</div>
                                     <div class="pd_poup_head_td col-sm-2">Set Description</div>
                                     <div class="pd_poup_head_td col-sm-1">No. of Pieces</div>
                                     <div class="pd_poup_head_td col-sm-1">Price / Piece</div>
                                     <div class="pd_poup_head_td col-sm-1">Product Amount</div>
                                     <div class="pd_poup_head_td col-sm-1">Tax Rate</div>
                                     <div class="pd_poup_head_td col-sm-1">Tax Amount</div>
                                     <div class="pd_poup_head_td child_box col-sm-1">Total Amount</div>
                                     <div class="clearfix"></div>
                                   </div>
                                   <div class="pd_popup_body">
                                    <?php $total_amount = 0;
                                          $tbody_row = 1; 
                                          foreach( $seller_product_values['products_data'] as $suborder_id_key => $suborder_products_data ) { 
                                            foreach( $suborder_products_data as $product_details){ 
                                    ?>
                                      <fieldset class="invoiced_edit_order_<?php echo $pickup_values['suborder_id']; ?> 
                                                       invoiced_edit_track_<?php echo $pickup_values['suborder_id'];?>
                                                       invoiced_product_count_<?php echo $sllr_inv_id_key; ?>
                                                       <?php echo ($pickup_status) ? ($product_details['pickup_status'] !='Picked_Up') ? ' ' : 'ord_pro_picked_up_bakgnd' : ' ' ; ?>" 
                                                data-change="false" 
                                                id="invoiced_block_products_<?php echo $pickup_values['order_id'];?>_<?php echo $product_details['order_product_id'];?>"
                                                data-order-product-id="<?php echo $product_details['order_product_id'];?>"
                                                data-product-id="<?php echo $product_details['product_id'];?>">
                                        <div class="pd_poup_body_td content col-sm-1">
                                          <?php if($pickup_status && $edit_invoice_available) { if($product_details['pickup_status'] !='Picked_Up') { ?>
                                          <div class="pd_action_btn_box edt_inv_order_actions">
                                            <ul>
                                              <li class="invoiced_information_agree action_table_icon" 
                                                  id="invoiced_information_agree_<?php echo $product_details['order_product_id']; ?>">
                                                  <a href="javascript:void(0);" class="pd_popup_selected">
                                                    <label> 
                                                      <input value="" 
                                                             type="checkbox" 
                                                             id="invoiced_list_sub_order_product_id_<?php echo $product_details['order_product_id'];?>" 
                                                             class="invoiced_input_popup_products"
                                                             data-order-id="<?php echo $pickup_values['order_id'];?>" 
                                                             data-suborder-id="<?php echo $pickup_values['suborder_id'];?>" 
                                                             data-order-product-id="<?php echo $product_details['order_product_id'];?>"
                                                             data-product-id="<?php echo $product_details['product_id'];?>"
                                                             data-total-piece="<?php echo $product_details['no_of_piece'];?>"
                                                             data-total-amount="<?php echo $product_details['total_amount'];?>"
                                                             data-product-amount="<?php echo $product_details['product_amount'];?>"
                                                             data-tax-amount="<?php echo $product_details['vat_cst_amount'];?>"
                                                             data-seller-invoice-id="<?php echo $sllr_inv_id_key;?>">
                                                      <div class="control__indicator"></div>
                                                    </label>
                                                  </a>
                                                </li>
                                                 <?php if($product_details['no_of_piece'] > 1)
                                                { ?>
                                                <li id="invoiced_information_edit_<?php echo $product_details['order_product_id']?>" 
                                                    class="invoiced_information_edit product_quantity action_table_icon" 
                                                    data-order-product-id="<?php echo $product_details['order_product_id']; ?>" 
                                                    data-product-id="<?php echo $product_details['product_id']; ?>"
                                                    data-order-id="<?php echo $pickup_values['order_id'];?>"
                                                    data-suborder-id="<?php echo $pickup_values['suborder_id'];?>"
                                                    data-product-id="<?php echo $product_details['product_id'];?>"
                                                    data-seller-invoice-id="<?php echo $sllr_inv_id_key;?>">
                                                  <a href="javascript:void(0);">
                                                    <i class="fa fa-pencil blue" aria-hidden="true"></i>
                                                    <input type="checkbox" 
                                                           name="" 
                                                           class="hidden" 
                                                           id="invoiced_input_information_edit_<?php echo $product_details['order_product_id']?>">
                                                  </a>
                                                </li>
                                                <?php } ?>
                                                <li id="invoiced_information_decline_<?php echo $product_details['order_product_id']?>" 
                                                    class="invoiced_information_decline action_table_icon" 
                                                    data-order-product-id="<?php echo $product_details['order_product_id'];?>"
                                                    data-order-id="<?php echo $pickup_values['order_id'];?>"
                                                    data-suborder-id="<?php echo $pickup_values['suborder_id'];?>"
                                                    data-product-id="<?php echo $product_details['product_id'];?>"
                                                    data-seller-invoice-id="<?php echo $sllr_inv_id_key;?>">
                                                  <a href="javascript:void(0);">
                                                    <i class="fa fa-times red" aria-hidden="true"></i>
                                                    <input type="checkbox" 
                                                           name="" 
                                                           class="hidden" 
                                                           id="invoiced_input_information_decline_<?php echo $product_details['order_product_id']?>">
                                                  </a>
                                                </li>
                                                <li id="invoiced_information_dispatch_<?php echo $product_details['order_product_id']?>" 
                                                    class="invoiced_information_dispatch action_table_icon" 
                                                    data-order-product-id="<?php echo $product_details['order_product_id'];?>"
                                                    data-order-id="<?php echo $pickup_values['order_id'];?>"
                                                    data-suborder-id="<?php echo $pickup_values['suborder_id'];?>"
                                                    data-seller-invoice-id="<?php echo $sllr_inv_id_key;?>">
                                                  <a href="javascript:void(0);">
                                                    <i class="fa fa-truck orange" aria-hidden="true"></i>
                                                    <input type="checkbox" 
                                                         name="" 
                                                         class="hidden" 
                                                         id="invoiced_input_information_dispatch_<?php echo $product_details['order_product_id']?>">
                                                  </a>
                                                </li>
                                                <li id="invoiced_information_undo_<?php echo $product_details['order_product_id']?>" 
                                                    class="invoiced_information_undo action_table_icon" 
                                                    data-order-product-id="<?php echo $product_details['order_product_id'];?>"
                                                    data-order-id="<?php echo $pickup_values['order_id'];?>"
                                                    data-suborder-id="<?php echo $pickup_values['suborder_id'];?>"
                                                    data-product-id="<?php echo $product_details['product_id']; ?>"
                                                    data-total-piece="<?php echo $product_details['no_of_piece'];?>"
                                                    data-total-amount="<?php echo $product_details['total_amount'];?>"
                                                    data-product-amount="<?php echo $product_details['product_amount'];?>"
                                                    data-tax-amount="<?php echo $product_details['vat_cst_amount'];?>"
                                                    data-seller-invoice-id="<?php echo $sllr_inv_id_key;?>">
                                                  <a href="javascript:void(0);">
                                                    <i class="fa fa-undo blue" aria-hidden="true"></i>
                                                  </a>
                                                </li>
                                            </ul>
                                          </div>
                                          <?php } else { ?>
                                          <div class="ord_pro_pickup_stts">
                                            <span>Picked Up</span>
                                          </div>
                                          <?php } } ?>
                                        </div>
                                        <div class="pd_poup_body_td content col-sm-3">
                                          <div class="pd_img_box">
                                            <a href="<?php echo $product_details['href']; ?>" target="_blank">
                                              <img src="<?php echo $product_details['image']; ?>" alt="<?php echo $product_details['name']; ?>" title="<?php echo $product_details['name']; ?>" width="74" height="111" />
                                            </a>
                                          </div>
                                          <div class="pd_popup_sq_code"><?php echo $product_details['seller_sku']; ?></div>
                                          <span class="pd_poup_title"><a href="<?php echo $product_details['href']; ?>" data-toggle="popover"  data-trigger="hover" data-content="<?php echo $product_details['name']; ?>" target="_blank"><?php echo $product_details['name']; ?></a></span>
                                        </div>
                                        <div class="pd_poup_body_td col-sm-2"><?php echo $product_details['comment']; ?></div>
                                        <div class="pd_poup_body_td col-sm-1" >
                                          <span class="pd_pieces_box 
                                                       label 
                                                       label-danger 
                                                       quantity_label 
                                                       inv_pro_piece_<?php echo $sllr_inv_id_key;?>_<?php echo $tbody_row; ?>" 
                                            data-original-piece="<?php echo $product_details['no_of_piece']; ?>" 
                                            id="invoiced_product_quantity_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>">
                                              <?php echo $product_details['no_of_piece']; ?>
                                          </span>
                                          <span id="invoiced_prod_quantity_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>" style="display: none;">
                                            <input type="text" 
                                                 name="invoiced_product_quantity_update"
                                                 class="invoiced_product_quantity" 
                                                 id="invoiced_input_quantity_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>" 
                                                 data-order-product-id="<?php echo $product_details['order_product_id']; ?>" 
                                                 data-product-id="<?php echo $product_details['product_id']; ?>" 
                                                 value="<?php echo $product_details['no_of_piece']; ?>" 
                                                 style="width: 50px;" 
                                                 data-old-value="<?php echo $product_details['no_of_piece']; ?>">
                                              <span class="invoiced_quantity_save_input btn btn-primary btn-xs" 
                                                    id="invoiced_quantity_save_input_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>" 
                                                    data-order-product-id="<?php echo $product_details['order_product_id']; ?>" 
                                                    data-product-id="<?php echo $product_details['product_id']; ?>"
                                                    data-order-id="<?php echo $pickup_values['order_id'];?>"
                                                    data-suborder-id="<?php echo $pickup_values['suborder_id'];?>"
                                                    data-total-piece="<?php echo $product_details['no_of_piece'];?>"
                                                    data-total-amount="<?php echo $product_details['total_amount'];?>"
                                                    data-seller-invoice-id="<?php echo $sllr_inv_id_key;?>"
                                                    style="margin-top: 5px;" >
                                                Update
                                              </span>
                                              <span class="invoiced_quantity_close_input btn btn-danger btn-xs"
                                                    data-order-product-id="<?php echo $product_details['order_product_id']; ?>" 
                                                    data-product-id="<?php echo $product_details['product_id']; ?>"
                                                    style="margin-top: 5px;">
                                                Cancel
                                              </span>
                                          </span>
                                        </div>
                                       <div class="pd_poup_body_td col-sm-1 
                                                   invoiced_price_per_piece_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>">
                                          <?php echo $product_details['price_per_piece'];?>
                                       </div>
                                       <div class="pd_poup_body_td 
                                                   col-sm-1
                                                   inv_pro_amt_<?php echo $sllr_inv_id_key;?>_<?php echo $tbody_row; ?> 
                                                   invoiced_prod_amt_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>"
                                            data-original-amt="<?php echo $product_details['product_amount']; ?>">
                                          <?php echo $product_details['product_amount']; ?>
                                       </div>
                                       <div class="pd_poup_body_td 
                                                   col-sm-1 
                                                   invoiced_tax_rate_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>" 
                                            data-tax-rate="<?php echo trim(str_replace('%','',$product_details['vat_cst_rate'])); ?>">
                                          <?php echo $product_details['vat_cst_rate']; ?>
                                       </div>
                                       <div class="pd_poup_body_td 
                                                   col-sm-1
                                                   inv_pro_tax_amt_<?php echo $sllr_inv_id_key;?>_<?php echo $tbody_row; ?>
                                                   invoiced_tax_amt_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>"
                                            data-original-tax-amt="<?php echo $product_details['vat_cst_amount'];?>">
                                            <?php echo $product_details['vat_cst_amount'];?>
                                       </div>
                                       <div class="pd_poup_body_td 
                                                   col-sm-1
                                                   inv_pro_total_amt_<?php echo $sllr_inv_id_key;?>_<?php echo $tbody_row; ?> 
                                                   invoiced_total_prod_amt_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>"
                                            data-original-total-amt="<?php echo $product_details['total_amount']; ?>">
                                          <?php echo $product_details['total_amount']; ?>
                                       </div>
                                       <div class="clearfix"></div>
                                      </fieldset> 
                                    <?php $total_amount += $product_details['total_amount']; $tbody_row++; } } ?>  
                                  </div>
                                  <div class="clearfix"></div>
                                  <div class="pd_popup_table_footer">
                                    <div class="col-sm-4 nopadding"></div>
                                    <div class="col-sm-3 table_total_box">
                                      <span id="table_total_box_<?php echo $pickup_values['suborder_id'];?>_<?php echo $sllr_inv_id_key;?>"
                                            class="inv_pro_all_totals_<?php echo $sllr_inv_id_key;?>"
                                            data-original-total-amount="<?php echo $this->currency->format(round($total_amount,2),'INR','1');?>">
                                        Total : <?php echo $this->currency->format(round($total_amount,2),'INR','1');?>
                                      </span>
                                    </div>
                                    <div class="clearfix"></div>
                                  </div>           
                                </div>
                              </div>
                            <?php $i++; } ?>
                          <?php } ?>

                          <!-- Seller Not Given Order Products -->
                          <?php if ( !empty($seller_not_given) ) {  $total_amount = 0; ?>
                            <div class="tab-pane <?php echo $class_SNG;?>" id="goods_not_given_<?php echo $pickup_values['suborder_id'];?>">
                              <div class="revert_button_block">
                                <div class="revert_button btn btn-warning" data-order-id="<?php echo $pickup_values['order_id'];?>" data-suborder-id="<?php echo $pickup_values['suborder_id'];?>">Revert Goods</div>
                                <div class="fa fa-exclamation exclamation" data-toggle="tooltip" data-placement="right" title="If you want to ship following SKUs then you can move SKU(s) by selecting the checkbox in action column and click Revert Goods Button."></div>
                              </div>
                              <div class="col-sm-12 pd_popup_table_contant">
                                <div class="pd_popup_thead">
                                  <div class="pd_poup_head_td col-sm-1">Action</div>
                                  <div class="pd_poup_head_td  col-sm-3">Product</div>
                                  <div class="pd_poup_head_td col-sm-2">Set Description</div>
                                  <div class="pd_poup_head_td col-sm-1">No. of Pieces</div>
                                  <div class="pd_poup_head_td col-sm-1">Price / Piece</div>
                                  <div class="pd_poup_head_td col-sm-1">Product Amount</div>
                                  <div class="pd_poup_head_td col-sm-1">Tax Rate</div>
                                  <div class="pd_poup_head_td col-sm-1">Tax Amount</div>
                                  <div class="pd_poup_head_td child_box col-sm-1">Total Amount</div>
                                  <div class="clearfix"></div>
                                </div>
                                <div class="pd_popup_body">
                                  <?php 
                                        foreach( $seller_not_given as $suborder_id_key => $suborder_products_data ) { 
                                          foreach($suborder_products_data as $product_details){
                                  ?>
                                    <fieldset class="edit_order edit_track">
                                      <div class="pd_poup_body_td content col-sm-1">
                                          <div class="pd_action_btn_box">
                                            <ul>
                                              <li class="checkbox_information_revert action_table_icon" 
                                                 id="checkbox_information_revert_<?php echo $pickup_values['order_id'];?>">
                                                <a href="javascript:void(0);" class="pd_popup_selected">
                                                  <label> 
                                                    <input value="" 
                                                           type="checkbox" 
                                                           id="revert_product_<?php echo $product_details['order_product_id'];?>" 
                                                           class="revert_checkbox"
                                                           data-order-id="<?php echo $pickup_values['order_id'];?>" 
                                                           data-order-product-id="<?php echo $product_details['order_product_id'];?>"
                                                           data-product-id="<?php echo $product_details['product_id'];?>"
                                                           data-total-piece="<?php echo $product_details['no_of_piece'];?>"
                                                           data-total-amount="<?php echo $product_details['total_amount'];?>"
                                                           data-product-amount="<?php echo $product_details['product_amount'];?>"
                                                           data-tax-amount="<?php echo $product_details['vat_cst_amount'];?>">
                                                    <div class="control__indicator"></div>
                                                  </label>
                                                </a>
                                              </li>                                                
                                            </ul>
                                          </div> 
                                       </div>
                                      <div class="pd_poup_body_td content col-sm-3">
                                        <div class="pd_img_box">
                                          <a href="<?php echo $product_details['href']; ?>" target="_blank">
                                            <img src="<?php echo $product_details['image']; ?>" alt="<?php echo $product_details['name']; ?>" title="<?php echo $product_details['name']; ?>" width="74" height="111" />
                                          </a>
                                        </div>
                                        <div class="pd_popup_sq_code"><?php echo $product_details['seller_sku']; ?></div>
                                        <span class="pd_poup_title"><a href="<?php echo $product_details['href']; ?>" target="_blank" data-toggle="popover"  data-trigger="hover" data-content="<?php echo $product_details['name']; ?>"><?php echo $product_details['name']; ?></a></span>
                                      </div>
                                      <div class="pd_poup_body_td col-sm-2"><?php echo $product_details['comment']; ?></div>
                                      <div class="pd_poup_body_td col-sm-1" >
                                        <span class="pd_pieces_box label label-danger quantity_label" id="product_quantity_<?php echo $product_details['order_product_id']; ?>_<?php echo $product_details['product_id']; ?>">
                                          <?php echo $product_details['no_of_piece']; ?>
                                        </span>
                                      </div>
                                      <div class="pd_poup_body_td col-sm-1"><?php echo $product_details['price_per_piece'];?></div>
                                      <div class="pd_poup_body_td col-sm-1"><?php echo $product_details['product_amount']; ?></div>
                                      <div class="pd_poup_body_td col-sm-1"><?php echo $product_details['vat_cst_rate']; ?></div>
                                      <div class="pd_poup_body_td col-sm-1"><?php echo $product_details['vat_cst_amount'];?></div>
                                      <div class="pd_poup_body_td col-sm-1"><?php echo $product_details['total_amount']; ?></div>
                                      <div class="clearfix"></div>
                                    </fieldset> 
                                  <?php $total_amount += $product_details['total_amount']; } } ?>  
                                </div>
                                <div class="pd_popup_table_footer">
                                  <div class="col-sm-4 nopadding"></div>
                                  <div class="col-sm-3 table_total_box">
                                    <span id="table_total_box_<?php echo $pickup_values['order_id'];?>">
                                      Total : <?php echo $this->currency->format(round($total_amount,2),'INR','1');?>
                                    </span>
                                  </div>
                                  <div class="clearfix"></div>
                                </div>           
                              </div>
                              <div class="clearfix"></div>
                            </div>
                          <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>
            <?php } ?>
            <?php } ?>
            <!-- Sets more popup (End) -->                 
          </div>                 
        </div>
      </div>
    </div>
  </div> 
<?php echo $footer; ?>