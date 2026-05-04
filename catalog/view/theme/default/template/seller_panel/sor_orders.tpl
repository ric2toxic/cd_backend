<?php echo $header;?>
<div id="page-wrapper">
  <!-- page title-->
  <div class="col-sm-12">
    <h1 class="page_title">Orders</h1>
    <?php
    
    ?>
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
      <li>
        <a href="<?php echo $pickup_tentative_link; ?>" target="_blank">
          <?php echo $tab_tentative_orders; ?>
        </a>
      </li>
      <li class="active">
          <a href="<?php echo $sor_order_link; ?>">
            <?php echo $tab_sor_orders; ?>
          </a>
        </li>
    </ul>
    <div class="col-sm-12 profile_detail panel-group">
    <!-- kuldeep work(start)-->
    <div class="filter_box_order pickup_done_records_box pd_record order_pockupdone_filter_title_box">
        <h3 class="order_pockupdone_filter_title" data-toggle="collapse" data-target="#order_pockupdone_filters" aria-expanded="true"> 
            <span class="fa fa-search"></span> Search 
            <small class="filter_text_pickupdone">(Click here to refine results using various option such as Invoice No, Invoice Date, Order No, Amount etc.)</small>
        </h3>
    </div>
    <div id="order_pockupdone_filters" class="collapse in" aria-expanded="true" style="">
        <div class="order_pockupdone_filters_section">
            <div class="form-inline">
                <div class="form-group opd_filters_box">
                    <label>Select Quarter </label>
                    <select name="filter_month_range" class="filters_input_section date_year_change" id="month_block">
                        <option value="">--Select--</option>
                        <?php if( !empty($getQuarters) ){ ?>
                        <?php foreach($getQuarters as $quarter_key => $quarter_value) { ?>
                        <option value="<?php echo $quarter_key; ?>" <?php echo ($quarter_key == $filter_month_range) ? 'selected' : ''; ?>><?php echo $quarter_value ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group opd_filters_box">
                    <label>Select Year </label>
                    <select name="filter_year_range" class="filters_input_section date_year_change" id="year_block">
                        <option value="">--Select--</option>
                        <?php if( !empty($getFinancialYears) ) { ?>
                        <?php foreach($getFinancialYears as $year_key => $year_value) {?>
                        <option value="<?php echo $year_value; ?>" <?php echo ($filter_year_range == $year_value) ? 'selected' : '' ; ?> ><?php echo $year_value; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group opd_filters_box">
                    <label>Order No. </label>
                    <input type="text" name="filter_order_no" class="filters_input_section" placeholder="<?php echo $entry_order_no; ?>" value="<?php echo $filter_order_no; ?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-inline">
                <div class="form-group opd_filters_box">
                    <label>Date From </label>
                    <input type="text" name="datefilter_from" placeholder="<?php echo $entry_invoice_date_from;?>" class="filter_datepicar filters_input_section" value="<?php echo $filter_invoice_date_from; ?>" readonly />
                </div>
                <div class="form-group opd_filters_box">
                    <label>Date To </label>
                    <input type="text" name="datefilter_to" placeholder="<?php echo $entry_invoice_date_to;?>" class="filter_datepicar filters_input_section" value="<?php echo $filter_invoice_date_to; ?>" readonly />
                </div>
                <div class="form-group opd_filters_box">
                    <label>Invoice No. </label>
                    <input type="text" name="filter_invoice_no" placeholder="<?php echo $entry_invoice_no; ?>" class="filters_input_section" value="<?php echo $filter_invoice_no; ?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-inline">
                <div class="form-group opd_filters_box">
                    <label>Amount Range </label>
                    <div class="filter_box_order_range">
                        <div class="form-group range" data-toggle="#sale_price_range">
                            <div class="input-group">
                                <input type="text" value="<?php echo (!empty($filters_pickup_done) && array_key_exists('filter_sale_from',$filters_pickup_done)) ? $filters_pickup_done['filter_sale_from']['value'] : '' ; ?>" placeholder="<?php echo $entry_sale_price;?>" id="input-date-added" class="form-control group_sale_filter" readonly />
                                <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-caret-right" aria-hidden="true"></i></button>
                                </span>
                            </div>
                        </div>
                        <div class="price_popup" id="sale_price_range">
                            <i data-toggle="#sale_price_range" class="fa fa-times range close_btn_price_popup" aria-hidden="true"></i>
                            <h3><?php echo $label_select_range; ?></h3>
                            <div class="price_pange_popup_box">
                                 <?php echo $entry_price_from; ?>
                                    <input type="text" name="filter_sale_from" class="price_pange_input" value="<?php echo $filter_sale_from; ?>" >  
                                 <?php echo $entry_price_to; ?>
                                    <input type="text" name="filter_sale_to" class="price_pange_input" value="<?php echo $filter_sale_to; ?>">
                                 <button type="button" class="price_apply_btn sale_apply_btn"> <?php echo $btn_apply; ?> </button>
                            </div>                                              
                        </div>
                    </div>
                </div>
                <div class="form-group opd_filters_box">
                    <label>Payment Status </label>
                    <select name="filter_payment_status" class="filters_input_section">
                        <option value="">--Select--</option>
                        <?php $payment_status_array = array('paid'=>'Paid', 'un_paid'=> 'Un Paid');?>
                        <?php foreach( $payment_status_array as $key => $value) { ?>
                            <option value="<?php echo $key; ?>" <?php echo ((!empty($filters_pickup_done)) && (array_key_exists('filter_payment_status',$filters_pickup_done)) && $filters_pickup_done['filter_payment_status']['value'] == $key) ? 'selected' : ''; ?> > <?php echo $value; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group opd_filters_box">
                    <label>Records range</label>
                    <select name="record_range" class="filters_input_section">
                        <?php $range = array('all'=>'ALL', '15'=>'15', '20'=>'20', '25'=>'25','50'=>'50');?>
                        <option value="*">--Select Record--</option>
                        <?php foreach( $range as $key => $value) { ?>
                            <option value="<?php echo $key; ?>" <?php echo ($key == $filter_record_range) ? 'selected' : '';?> ><?php echo $value; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
             <div class="clearfix"></div>
             <style type="text/css">
                 .filter_sor_listing{
                    display: block;
                    height: 50px;
                    vertical-align: middle;
                 }   
                 .filter_sor_listing lable{
                    margin-right: 7px;
                 }
                 .filter_sor_listing input{
                    margin-top: 7px;
                 }
             </style>
            <div class="form-inline text-right">
                
               
                <div class="form-group opd_filters_box text-left">
                    <button class="filter_search_btn btn btn-lg pull-left" id="button-filter">
                        <i class="fa fa-search" aria-hidden="true"></i> <?php echo $btn_search; ?>
                    </button>
                    <button type="button" class="report_btn btn btn-info filter_report_btn" id="download_pickup_done_report">
                        <i class="fa fa-file-text" aria-hidden="true"></i> 
                         Download Report
                    </button>
                </div>
            </div>    
        </div>
    </div>
    <!-- kuldeep work(End)-->
      <div class="tab-content">
        <div id="pickup_done">
            <div class=" panel-default row">
                <div class="panel-body" id="accordion">
                    <table class="orders_table">
                        <thead>
                            <tr>
                                <td><?php echo $column_order_no;?></td>
                                <td>
                                    <table width="100%">
                                        <tr>
                                            <td style="width: 150px;"><?php echo $column_invoice_no;?></td>
                                            <td style="width: 150px;"><?php echo $order_processing_date;?></td>
                                        </tr>
                                    </table>
                                </td>
                                <td><?php echo $column_sale;?></td>
                                <td><?php echo $column_return_amount;?></td>
                                <td><?php echo $column_penalty_amount;?></td>
                                <td><?php echo $column_payable_amount;?></td>
                                <td><?php echo $column_payment_status;?></td>
                                <!-- <td><?php echo $column_download_invoice;?></td> -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="filter_space"><td colspan="10"></td></tr>
                            <!--<tr class="filter_space"><td colspan="10"></td></tr> -->
                            <?php if ( $filters_pickup_done ){ ?>
                            <tr class="filter_select_box">
                                <td colspan="9">
                                    <span class="filter_select_section">
                                        <a href="<?php echo $clear_all_link; ?>" ><?php echo $label_clear_all; ?></a>
                                    </span>

                                    <?php foreach( $filters_pickup_done as $key_label => $value){ ?>
                                        <span class="filter_select_close">
                                        <?php if($value['value']=='un_paid'){$value['value']=ucwords(str_replace('_',' ',$value['value'])); }?>
                                            <?php echo ucwords($value['title']); ?> : <?php echo $value['value']; ?>
                                            <a href="<?php echo $value['url'];?>">
                                                <i class="fa fa-times-circle" aria-hidden="true"></i>
                                            </a>    
                                        </span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if( !empty($order_pickup_data ) ) { ?>
                            <??>
                                <?php
                                $order_no = '';
                                 foreach($order_pickup_data as $pickup_values) { 
                                    
                                    ?>
                                <tr>
                                    <?php
                                    $rowspan = 1;
                                    if (count($pickup_values['seller_sor_product'])>0) {
                                       $rowspan = count($pickup_values['seller_sor_product']);
                                    }                                    

                                    if ($pickup_values['order_no']!=$order_no) {
                                       
                                    ?>
                                    <td rowspan="<?php echo $rowspan;?>" width="5%"><a data-target="#<?php echo $pickup_values['suborder_id']; ?>" data-toggle="modal"><?php echo $pickup_values['order_no'];?></a>
                                        
                                        <?php if (isset($pickup_values['seller_sor_product']) && count($pickup_values['seller_sor_product'])>0) {
                                          echo '<br><span style="padding-left:5px;padding-right:5px;background-color:#f49090; font-size:9px;">Contains SOR</span>';
                                        }
                                        ?>
                                    </td>
                                    <?php
                                    }
                                    $order_no = $pickup_values['order_no'];
                                    ?>
                                    <td width="15%">
                                        <table width="100%">
                                            <?php
                                            echo '<tr>';
                                              if (isset($pickup_values['sor_invoice_no'])) {
                                             
                                                 echo '<td style="width:176px;">'.$pickup_values['sor_invoice_no'].'</td>';
                                                 
                                                 echo '<td>'.date('Y-m-d',strtotime($pickup_values['order_processing_date'])).'</td>';

                                               }
                                            echo '</tr>';            
                                         ?>                                                
                                        </table>
                                    </td>
                                    <td width="5%"><?php echo $pickup_values['sale']; ?></td>
                                    <td width="5%"><?php echo $pickup_values['return_amount']; ?></td>
                                    <td width="5%"><?php echo $pickup_values['penalty_amount']; ?></td>
                                    <td width="5%"><?php 
                                    if($pickup_values['order_status_id']=='2')
                                    {
                                      echo '0';
                                    }
                                    else
                                    {
                                    echo $pickup_values['net_payable_amount'];
                                     } 
                                    ?>  
                                    </td>
                                    <td class="paid_tooltip" data-order-no="<?php echo $pickup_values['suborder_id'];?>" data-paid-data='<?php echo html_entity_decode($pickup_values["payment_tool_tip"]); ?>'>
                                      
                                        <?php
                                         if($pickup_values['trxn_done'])
                                         {
                                        ?>
                                        <span class="tooltip_li paid_tooltip_box" id="tooltip_li_<?php echo $pickup_values['suborder_id'];?>" ><?php echo $pickup_values['trxn_done']; ?></span>
                                        <?php 
                                         }
                                        else if ($pickup_values['order_status_id']=='2') {
                                            echo '<span style="display: block; background-color: #ff8c8c; color: #fff; padding: 1px; font-size:14px;">Cancelled</span>';
                                        }
                                        else if (!$pickup_values['trxn_done'] && $pickup_values['order_status_id']=='15') {
                                            echo 'Payment Pending';
                                        }
                                        else
                                        {
                                          echo $pickup_values['order_status_name'];
                                        }
                                        ?>
                                    </td>
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
            <?php
            
             if( !empty($order_pickup_data ) ) { ?>
                <?php foreach($order_pickup_data as $pickup_values) { 
                    ?>
                    <div id="<?php echo $pickup_values['suborder_id']; ?>" class="modal fade order_popup_section active" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                  <button type="button" class="close model_popup_close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                  <div class="title_right popup_tight_title">
                                    <span class="oredr_right">Order No : <?php echo $pickup_values['order_no']; ?> </span>
                                    <span class="oredr_right">Order Processing Date : <?php echo date('d-m-Y',strtotime($pickup_values['order_processing_date']));?></span>
                                     <span class="oredr_right">Invoice Date : <?php echo date('d-m-Y',strtotime($pickup_values['order_date_added']));?></span>
                                  </div>
                                  <div class="help_line_for_order"><i class="fa fa-phone" aria-hidden="true"></i> For help: 9116134791 | 9649558363</div>
                              </div>
                              
                              <div class="modal-body">
                                  <div role="tabpanel">
                                    <ul class="nav nav-tabs profile_tebination subtable_box" role="tablist">
                                       
                                    <?php if (isset($pickup_values['seller_sor_product']) && count($pickup_values['seller_sor_product'])>0) { ?>
                                    <li class="<?php if($si==1){echo 'active';}?>">
                                        <a href="#seller_invoices_tab_<?php echo $pickup_values['order_no'];?>" aria-controls="seller_invoices" role="tab" data-toggle="tab">SOR Order</a>
                                    </li>
                                    <?php
                                    }
                                    ?>
                                    </ul>
                                    <div class="tab-content popup_content_table">                                        
                                        <?php if (isset($pickup_values['seller_sor_product']) && count($pickup_values['seller_sor_product'])>0) {
                                         ?>
                                            <div class="tab-pane active" id="seller_invoices_tab_<?php echo $pickup_values['order_no'];?>">
                                                <div class="tab_order_info" style="display: block; height: 41px;">
                                                   
                                                </div>    
                                                <div class="detail">
                                                    <table width="100%" class="order_detail_popup_table table">
                                                        <thead>
                                                            <tr>
                                                                <td>Invoice Detail</td>
                                                                <td>Product Name</td>
                                                                <td>SKU</td>
                                                                <td>Set Description</td>
                                                                <td>Invoice No.</td>
                                                                <td>Sets</td>
                                                                <td>No. of Pieces</td>
                                                                <td>Price/Piece</td>
                                                                <td>Product Amount</td>
                                                                <td>Tax Rate<br>Tax Amount</td>
                                                                <td>Total Amount</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                           
                                                            if ( !empty($pickup_values['seller_sor_product']) ) {                                                                   
                                                                $total_amount = 0;
                                                                foreach( $pickup_values['seller_sor_product'] as $suborder_key => $ps ) {
                                                                foreach ($ps as $key => $product_details) {
                                                                       
                                                                         
                                                            ?>
                                                                <tr>
                                                                    <td>
                                                                    <?php 
                                                                    if (isset($product_details['sor_invoice_no'])) {
                                             
                                                                         echo '<b>'.$product_details['sor_invoice_no'].'</b><br>';
                                                                         echo date('Y-m-d',strtotime($product_details['sor_invoice_date']));

                                                                    } 
                                                                    ?>
                                                                    </td>
                                                                    <td class="left">
                                                                        <a href="<?php echo $product_details['href']; ?>" target="_blank">
                                                                            <img src="<?php echo $product_details['image']; ?>" alt="<?php echo $product_details['name']; ?>" title="<?php echo $product_details['name']; ?>" />
                                                                        </a>
                                                                        <br>
                                                                        <?php echo $product_details['name']; ?>
                                                                    </td>
                                                                    
                                                                    <td><?php echo $product_details['seller_sku']; ?></td>
                                                                    <td><?php echo $product_details['comment']; ?></td>
                                                                    <td><?php echo $product_details['seller_invoice_id']; ?></td>
                                                                    <td><?php echo $product_details['quantity']; ?></td>
                                                                    <td><?php echo $product_details['no_of_piece']; ?></td>
                                                                    <td><?php echo $product_details['price_per_piece'];?></td>
                                                                    <td><?php echo $product_details['product_amount']; ?></td>
                                                                     <td><?php echo $product_details['vat_cst_rate']; ?><br>(<?php echo $product_details['vat_cst_amount'];?>)</td>
                                                                    <td><?php echo $product_details['total_amount']; ?></td>                   
                                                                </tr>
                                                            <?php 
                                                                $total_amount += $product_details['total_amount']; 
                                                                }
                                                            } 
                                                            ?>    
                                                            <?php  }?>                              
                                                        </tbody>
                                                    </table>
                                                    <div class="order_popup_footer"><p>Total : <?php echo round($total_amount,2);?></p></div>
                                                </div>
                                             </div>
                                        <?php
                                        }
                                        ?>

                                    </div>
                                  </div>
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


<?php echo $footer;?>



<script type="text/javascript">
$(function() {

    // invoice date date_picker
    $('input[name=\"datefilter_from\"]').datepicker({
      format: "dd MM yyyy",
    });  
    $('input[name=\"datefilter_to\"]').datepicker({
      format: "dd MM yyyy",
    });


    $('input[name="filter_order_no"]').keyup(function() {
      if (/\D/g.test(this.value)) {
       var node = $(this);
       node.val(node.val().replace(/[^0-9]/g,'') );
      }
    });

    $('input[name="filter_sale_from"]').keyup(function(e) {
      if (/\D/g.test(this.value)) {
       var node = $(this);
       node.val(node.val().replace(/[^0-9]/g,'') );
      }
    });

    $('input[name="filter_sale_to"]').keyup(function(e) {
      if (/\D/g.test(this.value)) {
       var node = $(this);
       node.val(node.val().replace(/[^0-9]/g,'') );
      }
    });


});

$('.range').click(function(){
    if(!$(this).hasClass('close_btn_price_popup')){
        $('.price_popup').slideUp();    
    }
    $($(this).data('toggle')).slideToggle();  
});

$('#download_pickup_done_report').click(function(){
    var url = "index.php?route=seller_panel/account-order/getSorOrders"; 

    var month_range = $('select[name=\'filter_month_range\']').val();
    var year_range = $('select[name=\'filter_year_range\']').val();

    if (month_range != '') {
        if(year_range !=''){
            url += '&filter_month_range=' + encodeURIComponent(month_range);    
        } else{
            alert('Please select year!');
            return false;
        }
    }

    if (year_range != '') {
        if(month_range !=''){
            url += '&filter_year_range=' + encodeURIComponent(year_range);
        } else{
            alert('Please select quarter!');
            return false;
        }
    }

    var filter_order_no = $('input[name=\'filter_order_no\']').val();

    if (filter_order_no != '') {
        url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
    }

    var filter_invoice_no = $('input[name=\'filter_invoice_no\']').val();

    if (filter_invoice_no != '') {
        url += '&filter_invoice_no=' + encodeURIComponent(filter_invoice_no);
    }

    var filter_invoice_date_from = $('input[name=\'datefilter_from\']').val();
    var filter_invoice_date_to = $('input[name=\'datefilter_to\']').val();;

    if (filter_invoice_date_from != '') {
        if(filter_invoice_date_to !='' ){
            url += '&filter_invoice_date_from=' + encodeURIComponent(filter_invoice_date_from);
        } else {
            alert('Please choose filter date to!!');
            return false;
        }
    }    

    if (filter_invoice_date_to != '') {
        if(filter_invoice_date_from !='' ){
            url += '&filter_invoice_date_to=' + encodeURIComponent(filter_invoice_date_to);
        } else {
            alert('Please choose filter date from!!');
            return false;
        }
    }

    var filter_sale_from = $('input[name=\'filter_sale_from\']').val();
    if (filter_sale_from != '') {
        url += '&filter_sale_from=' + encodeURIComponent(filter_sale_from);
    }

    var filter_sale_to = $('input[name=\'filter_sale_to\']').val();
    if (filter_sale_to != '') {
        url += '&filter_sale_to=' + encodeURIComponent(filter_sale_to);
    }

    var filter_payment_status = $('select[name=\'filter_payment_status\']').val();
    if (filter_payment_status != '') {
        url += '&filter_payment_status=' + encodeURIComponent(filter_payment_status);
    }

    var filter_record_range = $('select[name=\'record_range\']').val();
    if (filter_record_range != '*') {
        url += '&filter_record_range=' + encodeURIComponent(filter_record_range);
    }

    var date_from = new Date(filter_invoice_date_from);
    var date_to = new Date(filter_invoice_date_to);
    var date_diff = date_to.getTime() - date_from.getTime();

    if(filter_invoice_date_from != ''){
        if(filter_invoice_date_to != ''){
            if( date_diff < 0 ){
              alert('From date should be less then To date !!');
              return false;
            }
        } else {    
            alert('Please enter date to !!');
            return false;  
        }
    }  

    if( (parseInt( filter_sale_from )) > (parseInt( filter_sale_to ))){
        alert('From amount should be less then To amount !!');
        return false;
    }
    
    url += '&download_pickup_done_report=true';
        
    location = url;

});

var month_blocking = '<?php echo ($filter_month_range) ? $filter_month_range : '' ?>';
var year_blocking = '<?php echo ($filter_year_range) ? $filter_year_range : '' ?>';
var select_start_date = '';
var select_end_date = '';
var select_year = '';
var finally_from_date = '';
var finally_to_date = '';
$('.date_year_change').on('change',function(){
    if($(this).attr('id') == 'month_block'){
        month_blocking = $(this).val();
    }

    if($(this).attr('id') == 'year_block'){
        year_blocking = $(this).val();
    }

    if( month_blocking != '' && year_blocking != ''){

        switch( month_blocking ){
            case 'Q1':
        
                select_start_date = '01 April ';
                select_end_date   = '30 June ';
                select_year = year_blocking.substr(0, 4);
                break;
        
            case 'Q2':
        
                select_start_date = '01 July ';
                select_end_date   = '30 September ';
                select_year = year_blocking.substr(0, 4);
                break;
        
            case 'Q3':
        
                select_start_date = '01 October ';
                select_end_date   = '31 December '; 
                select_year = year_blocking.substr(0, 4);
                break;

            case 'Q4':
        
                select_start_date = '01 January ';
                select_end_date   = '31 March '; 
                let spliting_year = year_blocking.split('-');
                select_year = spliting_year[0].substr(0,2) +''+ spliting_year[1];       
                break;
        }
    
    finally_from_date = select_start_date +''+ select_year;    
    finally_to_date = select_end_date +''+ select_year;    
    $('input[name=datefilter_from]').val(finally_from_date);
    $('input[name=datefilter_to]').val(finally_to_date);

    }
});

$('input[name=datefilter_from]').change(function(){
    $('select[name=filter_month_range]').val('');
    $('select[name=filter_year_range]').val('');
    // $('input[name=datefilter_to]').val('');
});

$('input[name=datefilter_to]').change(function(){
    $('select[name=filter_month_range]').val('');
    $('select[name=filter_year_range]').val('');
    // $('input[name=datefilter_from]').val('');
});

$('#button-filter').click(function(){   

    var url = "index.php?route=seller_panel/account-order/getSorOrders";
    
    var filter_listing = $("input[name='filter_listing']:checked").val();

    if (filter_listing != '') {
        url += '&filter_sor_record=' + encodeURIComponent(filter_listing);
    }

    var month_range = $('select[name=\'filter_month_range\']').val();
    var year_range = $('select[name=\'filter_year_range\']').val();

    if (month_range != '') {
        if(year_range !=''){
            url += '&filter_month_range=' + encodeURIComponent(month_range);    
        } else{
            alert('Please select year!');
            return false;
        }
    }

    if (year_range != '') {
        if(month_range !=''){
            url += '&filter_year_range=' + encodeURIComponent(year_range);
        } else{
            alert('Please select quarter!');
            return false;
        }
    }

    var filter_order_no = $('input[name=\'filter_order_no\']').val();

    if (filter_order_no != '') {
        url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
    }

    var filter_invoice_no = $('input[name=\'filter_invoice_no\']').val();

    if (filter_invoice_no != '') {
        url += '&filter_invoice_no=' + encodeURIComponent(filter_invoice_no);
    }

    var filter_invoice_date_from = $('input[name=\'datefilter_from\']').val();
    var filter_invoice_date_to = $('input[name=\'datefilter_to\']').val();;

    if (filter_invoice_date_from != '') {
        if(filter_invoice_date_to !='' ){
            url += '&filter_invoice_date_from=' + encodeURIComponent(filter_invoice_date_from);
        } else {
            alert('Please choose filter date to!!');
            return false;
        }
    }    

    if (filter_invoice_date_to != '') {
        if(filter_invoice_date_from !='' ){
            url += '&filter_invoice_date_to=' + encodeURIComponent(filter_invoice_date_to);
        } else {
            alert('Please choose filter date from!!');
            return false;
        }
    }

    var filter_sale_from = $('input[name=\'filter_sale_from\']').val();
    if (filter_sale_from != '') {
        url += '&filter_sale_from=' + encodeURIComponent(filter_sale_from);
    }

    var filter_sale_to = $('input[name=\'filter_sale_to\']').val();
    if (filter_sale_to != '') {
        url += '&filter_sale_to=' + encodeURIComponent(filter_sale_to);
    }

    var filter_payment_status = $('select[name=\'filter_payment_status\']').val();
    if (filter_payment_status != '') {
        url += '&filter_payment_status=' + encodeURIComponent(filter_payment_status);
    }

    var filter_record_range = $('select[name=\'record_range\']').val();
    if (filter_record_range != '*') {
        url += '&filter_record_range=' + encodeURIComponent(filter_record_range);
    }

    var date_from = new Date(filter_invoice_date_from);
    var date_to = new Date(filter_invoice_date_to);
    var date_diff = date_to.getTime() - date_from.getTime();

    if(filter_invoice_date_from != ''){
        if(filter_invoice_date_to != ''){
            if( date_diff < 0 ){
              alert('From date should be less then To date !!');
              return false;
            }
        } else {    
            alert('Please enter date to !!');
            return false;  
        }
    }  

    if( (parseInt( filter_sale_from )) > (parseInt( filter_sale_to ))){
        alert('From amount should be less then To amount !!');
        return false;
    }
    
    location = url;
});


$('input').on('keypress',function(e){
    if (e.keyCode == 13) {
      $('#button-filter').trigger('click');
    }
  });

$("select[name='filter_record_range']").change(function(){
    $('#button-filter').trigger('click');
});


$(document).ready(function(){
  $('.sale_apply_btn').click(function(){
    var value_from = $('input[name="filter_sale_from"]').val();
    var value_to = $('input[name="filter_sale_to"]').val();
    var combine_value = value_from +'-'+ value_to;
    if( value_from !='' && value_to !=''){
      $('.group_sale_filter').val(combine_value);
      $('#sale_price_range').slideUp();
    }else {
      alert('Please fill price of from and to !');
    }
  });

  $('.sor_order_tab_class').click(function(){
      $('.tab_order_info').hide();
    });
    $('.sho_info_tab').click(function(){
      $('.tab_order_info').show();
    });


  // all filters arrow sign effect for filter title
  // $('.order_pockupdone_filter_title').click(function(){
  //   if(!$('#order_pockupdone_filters').hasClass('in')){
  //       $('.order_pockupdone_filter_title span').addClass('nav-icon-upper');
  //   } else{
  //       $('.order_pockupdone_filter_title span').removeClass('nav-icon-upper');
  //   }
  // });
});

</script>
<script type="text/javascript">
$( document ).ready(function() {
    
    var all_data_tooltip = '';
    
    $('.paid_tooltip').mouseover(function(){
        
        let order_no                = $(this).data('order-no');
        let all_data_tooltip        = $(this).data('paid-data');
        var tooltip_li              = '';
        
        $('#tooltip_li_'+order_no).tooltip({title: all_data_tooltip, html: true, placement: "bottom"});
    }).mouseout(function(){
        
    });
});
</script>
<style type="text/css">
    .order_detail_popup_table{margin-bottom: 0px;}
    .order_detail_popup_table thead{background: #f3f3f3;}
    .order_detail_popup_table tr td{ border: 1px solid #e2e2e2; border-collapse: collapse; text-align: center; }
    .order_detail_popup_table tbody{height:200px; overflow-y:auto;width: 100%; }
</style>