 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
<style type="text/css">
	.bootstrap-tagsinput {
		width: 100%;
		font-size:15px;
	}
</style>
<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_cohort_analysys; ?></h3>
      </div>
    <div class="panel-body">

      <form id="cohort-form" action="<?php echo $form_action; ?>" method="post" id="form-product_rating">

    <div class="well">
		<div class="row">
			<div class="col-sm-4">
		        <h3 class="panel-title" style="text-align: center; font-weight: bold; padding-bottom: 10px;"><?php echo $text_x_axis_filters?></h3>
		        <div class="col-sm-6" style="padding-left:0px;">
              <div class="form-group">
                <label class="control-label" for="input-date-end"><?php echo $text_cohort_types; ?></label>
                <select name="cohort_types" id="cohort_types" class="form-control">
                    <option value="">--SELECT--</option>
                    <?php if(!empty( $cohort_types ) ) {?>
                    <?php foreach( $cohort_types  as $key => $value) { $sel = ($key=='master_id')?'selected':'';?>
                      <option value="<?php echo $key; ?>" <?php echo $sel;?>><?php echo $value; ?></option>
                    <?php } ?>  
                    <?php } ?>
              </select>
            </div>
           </div>
           <div class="col-sm-6" >
              <div class="form-group">
                  <label class="control-label" for="input-date-end"><?php echo $text_month_interval; ?></label>
                  <input type="text" name="month_interval" id="x_month_interval" value="1" id="input-month-interval" class="form-control" onkeypress="return isNumber(event)" />
              </div>
           </div>   
			</div>
			<div class="col-sm-8">
				<div class="col-sm-12" >
		            <h3 class="panel-title" style="text-align: center; font-weight: bold; padding-bottom: 10px;"><?php echo $text_y_axis_filters?></h3>
		        </div>
		        <div class="col-sm-4">
	              <div class="form-group">
	                <label class="control-label" for="input-date-from"><?php echo $order_date_from; ?></label>
	                  	<div class="input-group date" id="y-date-from">
		                  <input type="text" name="filter_date_from" id="y_filter_date_from" value="<?php //echo $deffault_filter_dates; ?>" placeholder="<?php echo $order_date_from; ?>" data-date-format="YYYY-MM-DD" class="form-control"  />
		                  <span class="input-group-btn">
		                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
		                  </span>
	              		</div>
	                </div>
	            </div>
	            <div class="col-sm-4">
	                <div class="form-group">
	                  <label class="control-label" for="input-date-to"><?php echo $order_date_to; ?></label>
	                  <div class="input-group date" id="y-date-to">
	                  <input type="text" name="filter_date_to" id="y_filter_date_to" value="<?php //echo $deffault_filter_dates; ?>" placeholder="<?php echo $order_date_to; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
	                  <span class="input-group-btn">
	                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
	                  </span></div>
	                </div>
	            </div>
              <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" for="input-date-to"><?php echo $acq_month_sortby; ?></label>
                    <select name="sort" id="sort" class="form-control">
                    <option value="">--SELECT--</option>
                    <option value="ASC" selected="selected">ASC</option>
                    <option value="DESC">DESC</option>
                  </select>
                  </div>
              </div>
			</div>

		</div>
        <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-date-from"><?php echo $text_order_types; ?></label>
                  <select name="order_types" id="order_types" class="form-control">
                    <option value="">--SELECT--</option>
                    <?php if(!empty( $order_types_filter ) ) {?>
                      <?php foreach( $order_types_filter  as $key => $value) { ?>
                          <?php if(is_array($value)) {?>
                              <optgroup label="<?php echo ucwords(str_replace('_',' ',$key));?>">
                                  <?php foreach($value as $sub_key => $sub_value){?>
                                    <option value="<?php echo $sub_key; ?>"><?php echo $sub_value; ?></option>
                                  <?php } ?>
                              </optgroup>
                          <?php } else {?>
                          <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                          <?php } ?>
                      <?php } ?>  
                    <?php } ?>
                  </select>
                  <div id="sub_order_types" style="padding-top:5px;"></div>
                </div>
              </div>
              <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-date-from"><?php echo $text_customer_types; ?></label>
                  <select name="customer_types" id="customer_types" class="form-control">
                    <option value="">--SELECT--</option>
                    <?php if(!empty( $customer_types_filter ) ) {?>
                      <?php foreach( $customer_types_filter  as $key => $value) { ?>
                            <?php if(is_array($value)) {?>
                              <optgroup label="<?php echo ucwords(str_replace('_',' ',$key));?>">
                                  <?php foreach($value as $sub_key => $sub_value){?>
                                    <option value="<?php echo $sub_key; ?>"><?php echo $sub_value; ?></option>
                                  <?php } ?>
                              </optgroup>
                          <?php } else {?>
                          <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                          <?php } ?>
                      <?php } ?>  
                    <?php } ?>
                  </select>
                  <div id="sub_customer_types" style="padding-top:5px;"></div>
                </div>
              </div>
             
          	</div>
          	<div class="row" style="margin-left: 0px;"> 
              <label class="control-label" for="input-date-from"><?php echo $text_tags; ?></label>
          		<input type="text" class="div-tag" value="" />
          	</div>
            <div class="form-group" style="padding-top:10px">
              <button type="button" id="button-show" data-action="show" class="btn btn-primary button-filter pull-left" data-button-text="<?php echo $button_show_data; ?>"><i class="fa fa-search"></i> <?php echo $button_show_data; ?></button>
              &nbsp;
              <button type="button" id="button-filter" data-action="download" class="btn btn-primary button-filter pull-right" data-button-text="<?php echo $button_download_csv; ?>" ><i class="fa fa-search"></i> <?php echo $button_download_csv; ?></button>
            </div>
		  </div> 
      </form>
      <div id="ajax-loader" style="text-align: center;"></div>
      <div class="table-responsive cohort-data" id="show_data" style="text-align: center">
      </div>
		</div>
      </div>
    </div>
  </div>
  </div>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"></script>

<?php echo $footer; ?>

<script type="text/javascript">
  $('.date').datetimepicker({
      pickTime: false
  });


function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}


$(document).ready(function(){

$(document).on('click','.button-filter',function(){

	download = 'index.php?route=report/cohort/downloadCohortCSV&token=<?php echo $token; ?>&download_file=cohort_report';
  url = 'index.php?route=report/cohort/getCohortReport&token=<?php echo $token; ?>';

  var ajax_loader = '<img src="<?php echo $image_path?>">';

  var action        = $(this).data('action');
  var buttonText    = $(this).data('button-text');
  var download_type = $(this).data('download-type');
  var acq_month     = $(this).data('acq-month');

  url += '&action='+action;

  var month_interval = $('input[name=\'month_interval\']').val();
  
  if (month_interval) {
    url += '&filter_month_interval=' + encodeURIComponent(month_interval);
  }

  var filter_date_from = $('input[name=\'filter_date_from\']').val();
  
  if (filter_date_from) {
    url += '&filter_date_from=' + encodeURIComponent(filter_date_from);
  }
    
  var filter_date_to = $('input[name=\'filter_date_to\']').val();
  
  if (filter_date_to) {
    url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
  }

  var filter_cohort_types = $('#cohort_types').val();
  
  if (filter_cohort_types) {
    url += '&cohort_type=' + encodeURIComponent(filter_cohort_types);
  }

  var filter_sort = $('#sort').val();
  
  if (filter_sort) {
    url += '&filter_sort=' + encodeURIComponent(filter_sort);
  }

	var elt = $('.div-tag').tagsinput('items');

	var params = [];
	var order_type = [];
  var containing_payment_code = [];
  var excluding_payment_code = [];
  var customer_type = [];
  var shipping_zone_id = [];
  var contains_category_order = [];
  var exclusive_category_order = [];
	var query = '';
	$.each(elt, function(index, value){
		var id =  value.id;
		var val = value.value

		if(id.substring(0,10) === 'order_type') {
			order_type.push(val);
		}else if(id.substring(0,23) === 'containing_payment_code') {
			containing_payment_code.push(val);
    }else if(id.substring(0,22) === 'excluding_payment_code') {
      excluding_payment_code.push(val);
		}else if(id.substring(0,13) === 'customer_type') {
			customer_type.push(val);
		}else if(id.substring(0,16) === 'shipping_zone_id') {
      shipping_zone_id.push(val);
    }else if(id.substring(0,23) === 'contains_category_order') {
      contains_category_order.push(val)
    }else if(id.substring(0,24) === 'exclusive_category_order') {
      exclusive_category_order.push(val)
    }else{
			query += '&' + id + "=" + val ;
		}
		
	})	
	
	var order_type_values = '';
	if(order_type.length > 0) {
		order_type_values = '&order_type='+order_type.join( "," );
	}
  var containing_payment_method_values = '';
  if(containing_payment_code.length > 0) {
    containing_payment_method_values = '&containing_payment_code='+containing_payment_code.join( "," );
  }
  var excluding_payment_method_values = '';
  if(excluding_payment_code.length > 0) {
    excluding_payment_method_values = '&excluding_payment_code='+excluding_payment_code.join( "," );
  }
	var customer_type_values = '';
	if(customer_type.length > 0) {
		customer_type_values = '&customer_type='+customer_type.join( "," );
	}
  var shipping_zone_ids = '';
  if(shipping_zone_id.length > 0) {
    shipping_zone_ids = '&shipping_zone_id='+shipping_zone_id.join( "," );
  }
  var contains_category_order_ids = '';
  if(contains_category_order.length > 0) {
    contains_category_order_ids = '&contains_category_order_ids='+contains_category_order.join( "," );
  }
  var exclusive_category_order_ids = '';
  if(exclusive_category_order.length > 0) {
    exclusive_category_order_ids = '&exclusive_category_order_ids='+exclusive_category_order.join( "," );
  }

	url += query + order_type_values + containing_payment_method_values + excluding_payment_method_values + customer_type_values + shipping_zone_ids;
  url += contains_category_order_ids + exclusive_category_order_ids;
  
  //console.log(url); 
  
  $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json',
        beforeSend: function() {
          $('#ajax-loader').html(ajax_loader);
          $('#show_data').html('');
        },
        complete:function(){
          $('#ajax-loader').html('');
        },
        success: function(response) {
           $.each(response,function(index,item){
                if(index == 'show_data') {
                  $('#show_data').html(item);
                }else if(index == 'error') {
                  $('#show_data').html(item);
                }else if(index == 'download') {
                  window.location.href  = download
                }
            });
        },
        error: function(xhr, ajaxOptions, thrownError) { 
        var string = xhr.responseText.replace(/<b>/g,'');
            string = string.replace(/<\/b>/g,'');
            alert(string);
        }
    });
    
})

$(document).on('beforeItemRemove', '.div-tag', function(event) {
   var remove_item_id = event.item.id;
});

$('.div-tag').tagsinput({
      allowDuplicates: true,
        itemValue: 'id',  // this will be used to set id of tag
        itemText: 'label' // this will be used to set text of tag
    });

  $('#order_types').change(function(){
     
    var selected_order_type = $(this).val(); 
    
    if( selected_order_type == 'containing_payment_code' ||
        selected_order_type == 'excluding_payment_code' ||
        selected_order_type == 'shipping_zone_id' || 
        selected_order_type == 'exclusive_category_order' ||
        selected_order_type == 'contains_category_order' 
      ) 
    {
        $.ajax({
          type: 'GET',
          url: 'index.php?route=report/cohort/getOrderTypeFilterDropDown&filter_type='+selected_order_type+'&token=<?php echo $token?>',
          dataType: 'json',
          beforeSend: function() {},
          success: function(response) {
            

          if(selected_order_type == 'containing_payment_code' ||
            selected_order_type == 'excluding_payment_code' || 
            selected_order_type == 'shipping_zone_id')  
          {
              var s = $('<select name="'+selected_order_type+'" id="'+selected_order_type+'" class="form-control" />');
                    $('<option />', {
                        value: 0,
                        text: '--Select--'
                    }).appendTo(s);

            $.each(response,function(index,item){
                if(selected_order_type == 'shipping_zone_id') {
                  var option_value = index;
                }else{
                  var option_value = item;
                }
                $('<option />', {
                    value: option_value,
                    text: item
                }).appendTo(s);
            });

          } else if(selected_order_type == 'exclusive_category_order' || selected_order_type == 'contains_category_order') 
          {
              var s = $('<select name="'+selected_order_type+'" id="'+selected_order_type+'" class="form-control" />');
                    $('<option />', {
                        value: 0,
                        text: '--Select--'
                    }).appendTo(s);

              $.each(response,function(index,item){
                  //console.log(item);  
                  $('<option />', {
                    value: item.category_id,
                    text: item.name
                  }).appendTo(s);

                  //second level categories
                  if(item.children.length > 0) 
                  {
                    $.each(item.children,function(child_index,child_item){
                       
                        $('<option />', {
                          value: child_item.category_id,
                          text: " > "+ child_item.name
                        }).appendTo(s);
                        
                        //third level categories
                        if(child_item.children.length > 0) {

                          $.each(child_item.children,function(grand_child_index,grand_child_item){

                            $('<option />', {
                                value: grand_child_item.category_id,
                                text: "  - "+ " > "+ grand_child_item.name
                            }).appendTo(s);

                          })
                        }
                    })

                  }
                  
              });
          }
            $('#sub_order_types').html(s);

          },
          error: function(xhr, ajaxOptions, thrownError) { 
          var string = xhr.responseText.replace(/<b>/g,'');
              string = string.replace(/<\/b>/g,'');
              alert(string);
          }
      });

    } else {
      $('.div-tag').tagsinput('remove', { id: 'order_type_'+selected_order_type, text: '' });
	    var item = { id: 'order_type_'+selected_order_type, label: "Order Type ("+selected_order_type+")", value: selected_order_type};
	    $('.div-tag').tagsinput('add', item);
    }

  })  

  $(document).on("change", "#shipping_zone_id", function(){
     //event.preventDefault();
    var country = $(this).val();
    var country = $("#shipping_zone_id option:selected").val();
    var country_label = $("#shipping_zone_id option:selected").text();
  	if(country.trim() != '') {
  		$('.div-tag').tagsinput('remove', { id: 'shipping_zone_id_'+country, text: '' });
      var item = { id: 'shipping_zone_id_'+country, label: "Country ("+country_label+")", value:country };
      $('.div-tag').tagsinput('add', item);
  	}
  });

  $(document).on("change", "#containing_payment_code",function(){
     //event.preventDefault();
    var payment_code = $(this).val();
    if(payment_code.trim() != '') {
      $('.div-tag').tagsinput('remove', { id: 'containing_payment_code_'+payment_code, text: '' });
      var item = { id: 'containing_payment_code_'+payment_code, label: "Order Type > Containing Payment Method ("+payment_code+")", value:payment_code };
      $('.div-tag').tagsinput('add', item);
    }
  });

  $(document).on("change", "#excluding_payment_code",function(){
     //event.preventDefault();
    var payment_code = $(this).val();
    if(payment_code.trim() != '') {
      $('.div-tag').tagsinput('remove', { id: 'excluding_payment_code_'+payment_code, text: '' });
      var item = { id: 'excluding_payment_code_'+payment_code, label: "Order Type > Excluding Payment Method ("+payment_code+")", value:payment_code };
      $('.div-tag').tagsinput('add', item);
    }
  });


$(document).on('change','#min_order_count, #avg_order_value, #skip_master_id, #consider_master_id ',	function(){
	var ctype = $(this).data('ctype');
	var customer_type = $('#'+ctype).val();
	if(customer_type.trim() != '') { 
		$('.div-tag').tagsinput('remove', { id: ctype, text: '' });
		var item = { id: ctype , label: ctype+" ("+customer_type+")", value: customer_type };
    	$('.div-tag').tagsinput('add', item);
	}
})

$(document).on("change", "#contains_category_order", function(){
     //event.preventDefault();
    var contains_category = $(this).val();
    var contains_category = $("#contains_category_order option:selected").val();
    var contains_category_label = $("#contains_category_order option:selected").text().replace(" > ","").replace(" - ","").trim();
    if(contains_category.trim() != '') {
      $('.div-tag').tagsinput('remove', { id: 'contains_category_order'+contains_category, text: '' });
      var item = { id: 'contains_category_order'+contains_category, label: "Contains ("+contains_category_label+")", value:contains_category };
      $('.div-tag').tagsinput('add', item);
    }
  });

$(document).on("change", "#exclusive_category_order", function(){
     //event.preventDefault();
    var exclusive_category = $(this).val();
    var exclusive_category = $("#exclusive_category_order option:selected").val();
    var exclusive_category_label = $("#exclusive_category_order option:selected").text().replace(" > ","").replace(" - ","").trim();
    if(exclusive_category.trim() != '') {
      $('.div-tag').tagsinput('remove', { id: 'exclusive_category_order'+exclusive_category, text: '' });
      var item = { id: 'exclusive_category_order'+exclusive_category, label: "Exclusive ("+exclusive_category_label+")", value:exclusive_category };
      $('.div-tag').tagsinput('add', item);
    }
  });

  $("#customer_types").change(function(){

    var customer_type = $(this).val();
    var item = '';

    if(customer_type == 'min_order_count' || customer_type == 'avg_order_value' || customer_type == 'skip_master_id' || customer_type == 'consider_master_id') 
    {
        item = '<input name="'+customer_type+'" id="'+customer_type+'" class="form-control customer-types" data-ctype="'+customer_type+'" >';
    
    } else {
      $('.div-tag').tagsinput('remove', { id: 'customer_type_'+customer_type, text: '' });
      var item = { id: 'customer_type_'+customer_type, label: 'Customer type > '+customer_type, value: customer_type };
      $('.div-tag').tagsinput('add', item);
    }


    if(item !='') {
      $('#sub_customer_types').html(item);
    }else{
      $('#sub_customer_types').html('');
    }

  })

$(document).on('click','.download-acq-month-csv',function(){

  var download_url   = 'index.php?route=report/cohort/downloadCohortCSV&token=<?php echo $token; ?>&download_file=cohort_customers';
  var acq_month      = $(this).data('acq-month');
  var month_interval = $('input[name=\'month_interval\']').val();
  
  var ajax_loader = '<img src="<?php echo $image_path?>">';
  var icon = '<i class="fa fa-arrow-circle-o-down" style="font-size:15px;">';

  url = 'index.php?route=report/cohort/downloadAcqMonthCustomersCSV&token=<?php echo $token; ?>&acq_month='+acq_month;

  if (month_interval) {
    url += '&filter_month_interval=' + encodeURIComponent(month_interval);
  }

  var filter_date_from = $('input[name=\'filter_date_from\']').val();
  
  if (filter_date_from) {
    url += '&filter_date_from=' + encodeURIComponent(filter_date_from);
  }
    
  var filter_date_to = $('input[name=\'filter_date_to\']').val();
  
  if (filter_date_to) {
    url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
  }

  var filter_cohort_types = $('#cohort_types').val();
  
  if (filter_cohort_types) {
    url += '&cohort_type=' + encodeURIComponent(filter_cohort_types);
  }

  var elt = $('.div-tag').tagsinput('items');

  var params = [];
  var order_type = [];
  var containing_payment_code = [];
  var excluding_payment_code = [];
  var customer_type = [];
  var shipping_zone_id = [];
  var contains_category_order = [];
  var exclusive_category_order = [];
  var query = '';
  $.each(elt, function(index, value){
    var id =  value.id;
    var val = value.value

    if(id.substring(0,10) === 'order_type') {
      order_type.push(val);
    }else if(id.substring(0,23) === 'containing_payment_code') {
      containing_payment_code.push(val);
    }else if(id.substring(0,22) === 'excluding_payment_code') {
      excluding_payment_code.push(val);
    }else if(id.substring(0,13) === 'customer_type') {
      customer_type.push(val);
    }else if(id.substring(0,16) === 'shipping_zone_id') {
      shipping_zone_id.push(val);
    }else if(id.substring(0,23) === 'contains_category_order') {
      contains_category_order.push(val)
    }else if(id.substring(0,24) === 'exclusive_category_order') {
      exclusive_category_order.push(val)
    }else{
      query += '&' + id + "=" + val ;
    }
    
  })  
  
  var order_type_values = '';
  if(order_type.length > 0) {
    order_type_values = '&order_type='+order_type.join( "," );
  }
  
  var containing_payment_method_values = '';
  if(containing_payment_code.length > 0) {
    containing_payment_method_values = '&containing_payment_code='+containing_payment_code.join( "," );
  }
  var excluding_payment_method_values = '';
  if(excluding_payment_code.length > 0) {
    excluding_payment_method_values = '&excluding_payment_code='+excluding_payment_code.join( "," );
  }
  var containing_payment_method_values = '';
  if(containing_payment_code.length > 0) {
    containing_payment_method_values = '&containing_payment_code='+containing_payment_code.join( "," );
  }
  var excluding_payment_method_values = '';
  if(excluding_payment_code.length > 0) {
    excluding_payment_method_values = '&excluding_payment_code='+excluding_payment_code.join( "," );
  }
  var customer_type_values = '';
  if(customer_type.length > 0) {
    customer_type_values = '&customer_type='+customer_type.join( "," );
  }
  var shipping_zone_ids = '';
  if(shipping_zone_id.length > 0) {
    shipping_zone_ids = '&shipping_zone_id='+shipping_zone_id.join( "," );
  }
  var contains_category_order_ids = '';
  if(contains_category_order.length > 0) {
    contains_category_order_ids = '&contains_category_order_ids='+contains_category_order.join( "," );
  }
  var exclusive_category_order_ids = '';
  if(exclusive_category_order.length > 0) {
    exclusive_category_order_ids = '&exclusive_category_order_ids='+exclusive_category_order.join( "," );
  }

  url += query + order_type_values + containing_payment_method_values + excluding_payment_method_values + customer_type_values + shipping_zone_ids;
  url += contains_category_order_ids + exclusive_category_order_ids;
  
  $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json',
        beforeSend: function() {
          $('#'+acq_month).html(ajax_loader);
        },
        complete:function(){
          $('#'+acq_month).html(icon);
        },
        success: function(response) {
           $.each(response,function(index,item){
                if(index == 'success') {
                  window.location.href  = download_url
                }else if(index == 'error') {
                  alert(item);
                }
            });
        },
        error: function(xhr, ajaxOptions, thrownError) { 
        var string = xhr.responseText.replace(/<b>/g,'');
            string = string.replace(/<\/b>/g,'');
            alert(string);
        }
    });
})


})

</script>