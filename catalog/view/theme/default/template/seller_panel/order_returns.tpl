<?php echo $header; ?>
<div id="page-wrapper">
    <!-- page title-->
    <div class="col-sm-12">
        <h1 class="page_title">Return</h1>
    </div>
    <!-- page Contant-->
    <div class="col-sm-12">
		    <!-- kuldeep work(start)-->
    <div class="filter_box_order pickup_done_records_box pd_record order_pockupdone_filter_title_box">
      <h3 class="order_pockupdone_filter_title" data-toggle="collapse" data-target="#order_pockupdone_filters"> <span class="nav-icon"></span> Filters</h3>
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
    <div id="order_pockupdone_filters" class="collapse in">
        <div class="order_pockupdone_filters_section">
            <div class="form-inline">
                <div class="form-group opd_filters_box">
                    <label>Order No. </label>
                    <input type="text" name="filter_order_no" class="filters_input_section" placeholder="<?php echo $entry_order_no; ?>" value="<?php echo $filter_order_no; ?>">
                </div>
                <div class="form-group opd_filters_box">
                    <label>Debit Ref. NO.</label>
                    <input type="text" name="filter_debit_ref_no" placeholder="<?php echo $entry_debit_ref_no; ?>" class="filters_input_section" value="<?php echo $filter_debit_ref_no; ?>">

                </div>
               
                <div class="form-group opd_filters_box">
                    <label>Debit Amount </label>
                    <div class="filter_box_order_range">
						<div class="form-group range" data-toggle="#sale_price_range1">
							<div class="input-group">
								<input type="text" value="<?php echo !empty($filter_debit_rate_from) ? $filter_debit_rate_from .'-'.$filter_debit_rate_to : '' ; ?>" placeholder="<?php echo $entry_debit_amount;?>" id="input-date-added1" class="form-control group_sale_filter1" />
								<span class="input-group-btn">
								<button type="button" class="btn btn-default"><i class="fa fa-caret-right" aria-hidden="true"></i></button>
								</span>
							</div>
						</div>
						<div class="price_popup" id="sale_price_range1">
						<i data-toggle="#sale_price_range1" class="fa fa-times range close_btn_price_popup" aria-hidden="true"></i>
						<h3><?php echo $label_select_range; ?></h3>
						<div class="price_pange_popup_box">
							 <?php echo $entry_price_from; ?>
								<input type="text" name="filter_sale_from1" class="price_pange_input" value="<?php echo $filter_debit_rate_from; ?>" >  
							 <?php echo $entry_price_to; ?>
								<input type="text" name="filter_sale_to1" class="price_pange_input" value="<?php echo $filter_debit_rate_to; ?>">
							 <button type="button" class="price_apply_btn sale_apply_btn1"> <?php echo $btn_apply; ?> </button>
						</div>                                              
					</div>
                  </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-inline">
                <div class="form-group opd_filters_box">
                    <label>Debit Note Date from </label>
                    <input type="text" name="datefilter_from2" placeholder="<?php echo $entry_invoice_date_from;?>" class="filter_datepicar filters_input_section" value="<?php echo $filter_sale_from; ?>" />
                </div>
                <div class="form-group opd_filters_box">
                    <label>Debit Note Date to </label>
                    <input type="text" name="datefilter_to2" placeholder="<?php echo $entry_invoice_date_to;?>" class="filter_datepicar filters_input_section" value="<?php echo $filter_sale_to; ?>" />
                </div>
                <div class="form-group opd_filters_box">
                     <button class="filter_search_btn btn btn-lg pull-left" id="button-filter">
                        <i class="fa fa-search" aria-hidden="true"></i> <?php echo $btn_search; ?>
                    </button>
                    <button type="button" class="report_btn btn btn-info filter_report_btn" id="download_return_report">
                        <i class="fa fa-file-text" aria-hidden="true"></i> 
                         Download Report
                    </button>
                </div>
            </div>             
        </div>
    </div>
    <!-- kuldeep work(End)-->
        <div class="tab-content">
			<thead>
            <table class="orders_table">
				<?php if (!empty($filters_return) ) { ?>
                    <tr class="filter_space"><td colspan="4"></td></tr>
                    <tr class="filter_select_box"><td colspan="6">
                      <span class="filter_select_section"><a href="<?php echo $clear_all_link; ?>">Clear all</a></span>
                      <?php foreach($filters_return as $key => $values) { ?>
                        <span class="filter_select_close">
                          <?php echo ucwords($values['title']); ?> : <?php echo $values['value']; ?>
                          <a href="<?php echo $values['url'];?>">
                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                          </a>
                        </span>
                      <?php } ?>
                   </tr>
                   <?php } ?>
                <tr class="filter_space"><td colspan="7"></td></tr>
              <tr>
				<td width="12%"><?php echo $column_order_no;?></td>
                <td width="12%"><?php echo $column_debit_ref_no;?></td>
                <td width="20%"><?php echo $column_debit_note_date;?></td>
                <td width="15%"><?php echo $column_debit_amount;?></td>
                <td width="30%">
					<table width="100%">
						<tr>
							<td>Invoice No</td>
							<td>Invoice Date</td>
							<td>Invoice Amount</td>
						</tr>
					</table><?php //echo $column_seller_invoice_details;?>
				</td>
                <!--<td width="15%"><?php // echo $column_sell_invoice_amount;?></td> -->
               </tr>
               <tbody>
                   <tr class="filter_space"><td colspan="7"></td></tr>
                   <?php if (!empty($return_order) ) { ?>
                      <?php foreach($return_order as $key => $return) { ?>
                          <tr>
							<td><?php echo $return['order_no'];?></td>
							<td><?php echo $return['debit_note_no'];?></td>
							<td><?php echo $return['debit_note_date'];?></td>
							<td><?php echo $return['debit_note_amount'];?></td>
							
							<td>
								<table class="orders_table" width="100%">
									
								<?php foreach($return['invoice_datas'] as $invoice_values){ ?>
									<tr>
									<?php foreach(explode(';',$invoice_values) as $sub_inv_values ){ ?>
											<td><?php echo $sub_inv_values; ?></td>											
									<?php }	?>
									</tr>	
								<?php } ?>	
								
								</table>
							</td>
						  </tr>
                      <?php } ?>
                   <?php } ?>

                </tbody>
            </table>               
        </div>
    </div>
</div>

<?php echo $footer; ?>
<script type="text/javascript">
$(function() {
     // invoice date date_picker
    $('input[name=\"datefilter_from\"]').datepicker({
      format: "dd MM yyyy",
    });  
    $('input[name=\"datefilter_to\"]').datepicker({
      format: "dd MM yyyy",
    });
    
    $('input[name=\"datefilter_from2\"]').datepicker({
      format: "dd MM yyyy",
    });
    
    $('input[name=\"datefilter_to2\"]').datepicker({
      format: "dd MM yyyy",
    });
  });

$('.range').click(function(){
    if(!$(this).hasClass('close_btn_price_popup')){
        $('.price_popup').slideUp();    
    }
    $($(this).data('toggle')).slideToggle();  
});

/*
*  filters for pickup-done tab
* */

$(document).ready(function(){
	$('.profile_tebination a[href="' + location.hash + '"]').click();
});

$('#download_return_report').click(function(){
   var url = location.href; 
   url += '&download_return_report=1';
   location = url;

});

$('#button-filter').click(function(){
   
    var url = "index.php?route=seller_panel/account-order/getOrderReturn";

    var filter_order_no = $('input[name=\'filter_order_no\']').val();

    if (filter_order_no) {
        url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
    }
    
    var filter_debit_ref_no = $('input[name=\'filter_debit_ref_no\']').val();

    if (filter_debit_ref_no) {
        url += '&filter_debit_ref_no=' + encodeURIComponent(filter_debit_ref_no);
    }
    
    var filter_sale_from = $('input[name=\"datefilter_from2\"]').val();
	
    if (filter_sale_from) {
     url += '&filter_sale_from=' + encodeURIComponent(filter_sale_from);
    }

    var filter_sale_to = $('input[name=\"datefilter_to2\"]').val();
	
    if (filter_sale_to) {
     url += '&filter_sale_to=' + encodeURIComponent(filter_sale_to);
    }
    
    var filter_debit_rate_from = $('input[name=\'filter_sale_from1\']').val();
    if (filter_debit_rate_from) {
        url += '&filter_debit_rate_from=' + encodeURIComponent(filter_debit_rate_from);
    }
    
    var filter_debit_rate_end = $('input[name=\'filter_sale_to1\']').val();
	if (filter_debit_rate_end) {
        url += '&filter_debit_rate_to=' + encodeURIComponent(filter_debit_rate_end);
    }
 
    var date_from = new Date(filter_sale_from);
    var date_to = new Date(filter_sale_to);
    var date_diff = date_to.getTime() - date_from.getTime();
    
    if( date_diff <= 0 ){
		alert('From date should be less then To date !!');
        return false;
    }

    if( (parseInt( filter_debit_rate_from )) > (parseInt( filter_debit_rate_end ))){
		alert('From amount should be less then To amount !!');
        return false;
    }
    location = url;
});


// $('input[name="filter_order_no"], input[name="datefilter"], input[name="filter_invoice_no"], select[name="filter_payment_status"]').on('keypress',function(e){
//     if (e.keyCode == 13) {
//         $('#button-filter').trigger('click');
//     }
// });

$("select[name='filter_record_range']").change(function(){
	$('#button-filter').trigger('click');
});


$(document).ready(function(){
  $('.sale_apply_btn1').click(function(){
    var value_from = $('input[name="filter_sale_from1"]').val();
    var value_to = $('input[name="filter_sale_to1"]').val();
    var combine_value = value_from +'-'+ value_to;
    if( value_from !='' && value_to !=''){
      $('.group_sale_filter1').val(combine_value);
      $('#sale_price_range1').slideUp();
    }else {
      alert('Please fill value of from and to !');
    }
  });
  
  $('.sale_apply_btn2').click(function(){
    var value_from = $('input[name="filter_sale_from2"]').val();
    var value_to = $('input[name="filter_sale_to2"]').val();
    var combine_value = value_from +'-'+ value_to;
    if( value_from !='' && value_to !=''){
      $('.group_sale_filter2').val(combine_value);
      $('#sell_price_range2').slideUp();
    }else {
      alert('Please fill value of from and to !');
    }
  });
  
  // all filters arrow sign effect for filter title
  $('.order_pockupdone_filter_title').click(function(){
    if(!$('#order_pockupdone_filters').hasClass('in')){
        $('.order_pockupdone_filter_title span').addClass('nav-icon-upper');
    } else{
        $('.order_pockupdone_filter_title span').removeClass('nav-icon-upper');
    }
  });
});

</script>
