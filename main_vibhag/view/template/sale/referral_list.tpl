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

       <div class="well">
          <div class="row">
            <div class="col-sm-5">
              <div class="form-group">
                <div class="input-group date">
                  <input type="text" name="filter_date_added_from" value="<?php echo $filter_date_added_from; ?>" placeholder="Date From" data-date-format="YYYY-MM-DD" id="input-date-added-from" class="form-control" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
            </div>

            <div class="col-sm-5">
              <div class="form-group">
                <div class="input-group date">
                  <input type="text" name="filter_date_added_to" value="<?php echo $filter_date_added_to; ?>" placeholder="Date To" data-date-format="YYYY-MM-DD" id="input-date-added-to" class="form-control" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
            </div>
            
            <div class="col-sm-2">
            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> Search</button>
           </div>
          </div>
        </div>
        <div class="row">
             <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="tile">
                      <div class="tile-heading">Account Created</div>
                       <div class="tile-body">
                        <h2><?php echo $total_referral; ?></h2>
                        </div>
                  </div>
              </div>

               <div class="col-lg-3 col-md-3 col-sm-3">
                 <div class="tile">
                      <div class="tile-heading">Sign up</div>
                       <div class="tile-body">
                        <h2><?php echo $total_signup; ?></h2>
                        </div>
                  </div>
              </div>

              <div class="col-lg-3 col-md-3 col-sm-3">
                 <div class="tile">
                      <div class="tile-heading">Orders</div>
                       <div class="tile-body">
                        <h2><?php echo $total_orders[0]; ?></h2>
                        </div>
                  </div>
              </div>

               <!-- <div class="col-lg-2 col-md-2 col-sm-3">
                 <div class="tile">
                      <div class="tile-heading">Orders Excluding Canceled</div>
                       <div class="tile-body">
                        <h2><?php //echo $total_orders_excluding_cancel['0'] ?></h2>
                        </div>
                  </div>
              </div> -->  

               <div class="col-lg-3 col-md-3 col-sm-3">
                 <div class="tile">
                      <div class="tile-heading">Orders Amount</div>
                       <div class="tile-body">
                        <h2><?php echo $total_orders[1]; ?></h2>
                        </div>
                  </div>
              </div>

              <!-- <div class="col-lg-2 col-md-2 col-sm-3">
                 <div class="tile">
                      <div class="tile-heading">Excluding Canceled</div>
                       <div class="tile-body">
                        <h2><?php //echo $total_orders_excluding_cancel['1'] ?></h2>
                        </div>
                  </div>
              </div> -->

        </div>

        <form action="" method="post" enctype="multipart/form-data" id="form-return">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td class="text-left">Referral Details</td>
                  <td class="text-left">Total Signup</td>
                  <td class="text-left">Ordering Customer</td>
                  <td class="text-left">Number of Orders</td>
                  <td class="text-left">Orders Total</td>
                  <td class="text-left">Action</td>
                </tr>
              </thead>
            <tbody>
            <?php if (count($affiliate_list) > 0) { ?>
            <?php foreach($affiliate_list as $affiliate) { ?>
              <tr>
                  <td class="text-left"><?php echo $affiliate['referrer_firstname'].' '.$affiliate['referrer_lastname'];
                   ?> 
                   <br />Code: <?php echo $affiliate['referral_code']; ?> 
                   <br />Customer ID: <?php echo $affiliate['referrer_customer_id']; ?>
                   <br />Type: <?php echo $affiliate['type']; ?>
                   </td>

                   <td class="text-left"><a href="<?php echo $affiliate['customer_url']; ?>"><?php echo $affiliate_signup_data[$affiliate['affiliate_id']]; ?></a></td>
                  <td class="text-left">
                  <?php if($affiliate['signup_count'] > 1) { ?>
                 
                  <a href="<?php echo $affiliate['ordering_customer_url']; ?>"><?php echo $affiliate['signup_count']; ?></a>
                  <?php } else { ?>

                  <a href="<?php echo $affiliate['ordering_customer_url']; ?>"><?php echo $affiliate['order_customer_firstname'].' '.$affiliate['order_customer_lastname'];
                   ?> </a>
                   <br />Customer ID: <?php echo $affiliate['referrer_customer_id']; ?>
                   <br />Email: <?php echo $affiliate['order_customer_email']; ?> 
                   <br />Mobile: <?php echo $affiliate['order_customer_telephone']; ?>

                   <?php } ?>

                  </td>
                  <td class="text-left"><a href="<?php echo $affiliate['order_url']; ?>"><?php echo $affiliate['order_count']; ?></a></td>
                  <td class="text-left"><?php echo $affiliate['order_total']; ?></td>
                  
                  <td class="text-left">

                   <a style="cursor: pointer;" onclick="get_commission(<?php echo $affiliate['affiliate_id']; ?>)">
                    <i class="fa fa-money" style="font-size: 16px;"></i>
                   </a> 
                    &nbsp; 

                   <a style="cursor: pointer;" onclick="get_commission_rate(<?php echo $affiliate['affiliate_id']; ?>)">
                    <i class="fa fa-cog" style="font-size: 16px;"></i>
                   </a>

                   &nbsp; 
                   <a style="cursor: pointer;" onclick="get_referral_customers(<?php echo $affiliate['affiliate_id']; ?>)">
                    <i class="fa fa-user" style="font-size: 16px;"></i>
                   </a>

                   &nbsp; 
                   <a style="cursor: pointer;" onclick="get_last_order(<?php echo $affiliate['affiliate_id']; ?>)">
                    <i class="fa fa-history" style="font-size: 16px;"></i>
                   </a>

                  </td>

                </tr>
            <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="9"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
          </tbody>
          </table>
          </div>
        </form>

        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>

      </div>
    </div>
  </div>
</div>


<!-- commission model -->
<div id="commission_model" class="modal fade" role="dialog">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 600px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h2 class="modal-title">Commission</h2>
    </div>
    <div class="modal-body">

     <h4>Tentative Commission</h4>

    <table class="table table-bordered table-hover" style="width: 100%; font-size: 15px">
      <tr><td>Total Order Amount</td><td id="total_tentative_amount" style="font-weight: bold;"></td></tr>
      <tr><td>Total Commission</td><td id="total_tentative_commission" style="font-weight: bold;"></td></tr>
      </table>

      <h4>Transferable Commission</h4>

      <table class="table table-bordered table-hover" style="width: 100%; font-size: 15px">
      <tr><td>Total Commission</td><td id="total_commission" style="font-weight: bold;"></td></tr>
      <tr><td>Paid Commission</td><td id="paid_commission" style="font-weight: bold;"></td></tr>
      <tr><td>Pending Commission</td><td id="pending_commission" style="font-weight: bold;"></td></tr>
      </table>

    </div>
   
  </div>
</div>
</div>
<!-- end commission model -->


<!-- commission rate model -->
<div id="commission_rate_model" class="modal fade" role="dialog">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 600px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h2 class="modal-title">Commission Rate</h2>
    </div>
    <div class="modal-body">
       
      <table class="table table-bordered table-hover" style="width: 100%; font-size: 15px">
      <thead>
      <tr>
      <th>Order Count</th>
      <th>Total Commission</th>
      <th>Date Added</th>
      <th>Action</th>
      </tr>
      </thead>
      <tbody id="commission_rates_html">
        <tr>
          <td>Default</td>
          <td><?php echo REFERRAL_COMMISSION; ?>%</td>
        </tr>
      </tbody>
      </table>
  <form method="post" id="form_rates" style="margin-top: 40px;">
     <input type="hidden" name="affiliate_id"  />
     <input type="hidden" name="commission_rate_id"  />

    <div class="form-group">
       <div class="row">
         <label class="col-sm-3 control-label" for="input-customer-group">
          Order Count From
         </label>
            <div class="col-sm-3">
            <select name="order_count_from" id="order_count" class="form-control">
                <?php for($i=1; $i<=100; $i++) { ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php } ?>
            </select>
          </div>
          <label class="col-sm-1 control-label" for="input-customer-group">
          To
         </label>
          <div class="col-sm-3">
             <select name="order_count_to" id="order_count_to" class="form-control">
                <?php for($i=1; $i<=100; $i++) { ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php } ?>
                <option value="-1">All</option>
            </select>
          </div>
        </div>
      </div>

    <div class="form-group">
       <div class="row">
         <label class="col-sm-3 control-label" for="input-customer-group">
          Commission Rate
         </label>
            <div class="col-sm-7">
              <input type="number" name="rate" placeholder="rate" class="form-control" />
          </div>
        </div>
    </div>

    </form>


    </div>

    <div class="modal-footer">
      <button type="button" data-dismiss="modal" class="btn btn-default"> Cancel </button>
    <button type="button" id="rate_submit" class="btn btn-default" onclick="save_rates()"> Submit </button>
    </div>

  </div>
</div>
</div>
<!-- end commission rate model -->


<!-- order model -->
<div id="order_detail_model" class="modal fade" role="dialog">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 600px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h2 class="modal-title">Last Order</h2>
    </div>
    <div class="modal-body">

    <table class="table table-bordered table-hover" style="width: 100%; font-size: 15px">
      <tr><td>Order Number</td>
          <td id="order_no" style="font-weight: bold;"></td>
      </tr>
      <tr><td>Customer Name</td>
          <td id="customer_name" style="font-weight: bold;"></td>
      </tr>
      <tr><td>Customer Email</td>
          <td id="customer_email" style="font-weight: bold;"></td>
      </tr>
      <tr><td>Customer Mobile</td>
          <td id="customer_mobile" style="font-weight: bold;"></td>
      </tr>
      <tr><td>Address</td>
          <td id="customer_address" style="font-weight: bold;"></td>
      </tr>
      <tr><td>City</td>
          <td id="customer_city" style="font-weight: bold;"></td>
      </tr>
      <tr><td>Zone</td>
          <td id="customer_zone" style="font-weight: bold;"></td>
      </tr>
      <tr><td>Country</td>
          <td id="customer_country" style="font-weight: bold;"></td>
      </tr>
      <tr><td>Postcode</td>
          <td id="customer_postcode" style="font-weight: bold;"></td>
      </tr>
       <tr><td>Order Date</td>
          <td id="order_date" style="font-weight: bold;"></td>
      </tr>
      </table>

    </div>
   
  </div>
</div>
</div>
<!-- end order model -->


<!-- order model -->
<div id="customers_model" class="modal fade" role="dialog">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 800px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h2 class="modal-title">Customers</h2>
    </div>
    <div class="modal-body">

    <table class="table table-bordered table-hover" style="width: 100%; font-size: 15px">
       <thead>
      <tr>
      <th>Customer ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Mobile</th>
      <th>Remove</th>
      </tr>
      </thead>
      <tbody id="customers_html">
      </tbody>
     </table>

    </div>
   
  </div>
</div>
</div>
<!-- end order model -->


<?php echo $footer; ?>
<style type="text/css">
.tile{background-color:#fff;float: left;width: 100%; border: 1px solid #279FE0;}
.tile-heading{text-align: center; height: 40px;}
.tile-body{color: #279FE0;text-align: center;padding: 25px;}
.tile-body h2{font-size: 16px !important; font-weight:bold;}
thead td{font-size: 16px;}
thead td span.note{font-size: 12px; color: #ccc;}
.control-label{padding-top: 10px;}
.delete_rate, .edit_rate {cursor: pointer;}
</style>
<script type="text/javascript">
$('.date').datetimepicker({
  pickTime: false
});

$('#button-filter').on('click', function() {
  url = 'index.php?route=sale/referral/index&token=<?php echo $token; ?>';

  
  var filter_date_added_from = $('input[name=\'filter_date_added_from\']').val();

  if (filter_date_added_from) {
    url += '&filter_date_added_from=' + encodeURIComponent(filter_date_added_from);
  }

   var filter_date_added_to = $('input[name=\'filter_date_added_to\']').val();

  if (filter_date_added_to) {
    url += '&filter_date_added_to=' + encodeURIComponent(filter_date_added_to);
  }

  location = url;
});


function get_last_order(affiliate_id)
{
  $.ajax({
        url: 'index.php?route=sale/referral/getLastReferralOrder&token=<?php echo $token; ?>&affiliate_id='+affiliate_id,
        type: 'get',
        dataType: "json",
        success: function (data) {
          $("#order_detail_model").modal("show");
          $("#order_no").html(data.order_no);
          $("#customer_name").html(data.firstname+' '+data.lastname);
          $("#customer_email").html(data.email);
          $("#customer_mobile").html(data.telephone);
          $("#customer_address").html(data.shipping_address_1+' '+data.shipping_address_2);
          $("#customer_city").html(data.shipping_city);
          $("#customer_zone").html(data.shipping_zone);
          $("#customer_country").html(data.shipping_country);
          $("#customer_postcode").html(data.shipping_postcode);
          $("#order_date").html(data.date_added);
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function get_referral_customers(affiliate_id)
{
  $.ajax({
        url: 'index.php?route=sale/referral/getReferralCustomers&token=<?php echo $token; ?>&affiliate_id='+affiliate_id,
        type: 'get',
        dataType: "json",
        success: function (data) {
          $("#customers_model").modal("show");
          var html = '';
          for(i=0; i < data.length; i++)
          {
            html += '<tr>';
            html += '<td>'+data[i]['customer_id']+'</td>';
            html += '<td>'+data[i]['firstname']+' '+data[i]['lastname']+'</td>';
            html += '<td>'+data[i]['email']+'</td>';
            html += '<td>'+data[i]['telephone']+'</td>';
            html += '<td><a onclick="remove_referral('+data[i]['customer_id']+', '+affiliate_id+');"><i class="fa fa-trash"></i></a></td>';
            html += '</tr>';
          }

          $("#customers_html").html(html);

        },
        cache: false,
        contentType: false,
        processData: false
    });
}


function get_commission(affiliate_id)
{
  $.ajax({
        url: 'index.php?route=sale/referral/getCommission&token=<?php echo $token; ?>&affiliate_id='+affiliate_id,
        type: 'get',
        dataType: "json",
        success: function (data) {
          $("#commission_model").modal("show");
          $("#total_commission").html(data.total_commission_amount);
          $("#pending_commission").html(data.pending_commission_amount);
          $("#paid_commission").html(data.paid_commission_amount);
          $("#total_tentative_amount").html(data.tentative_order_amount);
          $("#total_tentative_commission").html(data.tentative_order_commission);
          
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function remove_referral(customer_id, affiliate_id)
{
  if(confirm("Are you sure? You want to ramove this customer."))
  {
   $.ajax({
        url: 'index.php?route=sale/referral/removeReferral&token=<?php echo $token; ?>&customer_id='+customer_id,
        type: 'get',
        dataType: "json",
        success: function (data) {
          if(data)
          {
            get_referral_customers(affiliate_id);
          }
          
        },
        cache: false,
        contentType: false,
        processData: false
    });
  }
}


function get_commission_rate(affiliate_id)
{
  $("#rate_submit").html("Submit");
  $("#form_rates input[name=commission_rate_id]").val('');
  $("#form_rates input[name=rate]").val('');
  $("#form_rates select[name=order_count_from]").val(1);
  $("#form_rates select[name=order_count_to]").val(-1);
  $("#form_rates input[name=affiliate_id]").val(affiliate_id);
  $.ajax({
        url: 'index.php?route=sale/referral/getCommissionRates&token=<?php echo $token; ?>&affiliate_id='+affiliate_id,
        type: 'get',
        dataType: "html",
        success: function (data) {
          $("#commission_rates_html").html(data);
          $("#commission_rate_model").modal("show");
        },
        cache: false,
        contentType: false,
        processData: false
    });
}


 function save_rates()
 {
    var affiliate_id = $("#form_rates input[name=affiliate_id]").val();
    var commission_rate_id = $("#form_rates input[name=commission_rate_id]").val();
    var order_count_from = $("#form_rates select[name=order_count_from]").val();
    var order_count_to = $("#form_rates select[name=order_count_to]").val();
    var rate = $("#form_rates input[name=rate]").val();


    var formData = new FormData();
    formData.append('affiliate_id', affiliate_id);
    formData.append('commission_rate_id', commission_rate_id);
    formData.append('order_count_from', order_count_from);
    formData.append('order_count_to', order_count_to);
    formData.append('rate', rate);
 
    $.ajax({
        url: 'index.php?route=sale/referral/saveCommissionRates&token=<?php echo $token; ?>',
        type: 'post',
        data: formData,
        dataType: "json",
        success: function (data) {
            if(data.status)
            {
              get_commission_rate(affiliate_id);
            }
            else
            {
              alert(data.message);
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
 }

 $(document).delegate('.delete_rate', 'click', function(e)
  { 
   if(confirm("Are you sure you want to delete this commission rate"))
   {
     var commission_rate_id = $(this).data('id');

     $("#rate_submit").html("Submit");
     $("#form_rates input[name=commission_rate_id]").val('');
     $("#form_rates input[name=rate]").val('');
     $("#form_rates select[name=order_count_from]").val(1);
     $("#form_rates select[name=order_count_to]").val(-1);

    $.ajax({
        url: 'index.php?route=sale/referral/deleteCommissionRate&token=<?php echo $token; ?>&commission_rate_id='+commission_rate_id,
        type: 'get',
        dataType: "json",
        success: function (data) {
            if(data.status)
            {
              $("#commission_rate_"+commission_rate_id).remove();
            }
            else
            {
              alert(data.message);
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
  }
 });

 $(document).delegate('.edit_rate', 'click', function(e)
  { 
   var commission_rate_id = $(this).data('id');
   var order_count_from   = $(this).data('order_from');
   var order_count_to     = $(this).data('order_to');
   var rate               = $(this).data('rate');

   $("#form_rates input[name=commission_rate_id]").val(commission_rate_id);
   $("#form_rates select[name=order_count_from]").val(order_count_from);
   $("#form_rates select[name=order_count_to]").val(order_count_to);
   $("#form_rates input[name=rate]").val(rate);
   $("#rate_submit").html("Update");
    
 });

</script>
