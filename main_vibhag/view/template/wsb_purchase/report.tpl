<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $page_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
      <?php
       if ($session_succ_msg!='' && $session_succ_msg!='Error : Delete not possiable') {
        echo '<div class="alert alert-success"><i class="fa fa-check-circle"></i> '.$session_succ_msg.' <button type="button" class="close" data-dismiss="alert">×</button></div>';
      }else{

        if ($session_succ_msg!='') {
          echo '<div class="alert alert-danger"><i class="fa fa-check-circle"></i> '.$session_succ_msg.' <button type="button" class="close" data-dismiss="alert">×</button></div>';
        }        
      }
      ?>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
           <form action="" method="get" id="filter_form">
               <input type="hidden" name="route" value="<?php echo $route; ?>">
               <input type="hidden" name="token" value="<?php echo $token; ?>">
                <div class="row">
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label class="control-label" for="input-order-no"><?php echo $text_purchase_firm; ?></label>
                        <select class="form-control text-uppercase" name="filter_purchase_firm_id" >
                            <option value="0">Select Purchase Firm</option>
                            <?php foreach($purchase_firms as $purchase_firm){ ?>
                                <option <?php echo $filter_purchase_firm_id == $purchase_firm['purchase_firm_id'] ? 'selected' : ''; ?>
                                       value="<?php echo $purchase_firm['purchase_firm_id']; ?>">
                                       <?php echo 'Wholesalebox - '.$purchase_firm['purchase_firm_city']; ?>
                                </option>
                            <?php } ?>
                        </select>
                      </div>
                      <div class="form-group">
                          <label class="control-label" for="seller_id">Invoice No</label>
                          <input type="text" class="form-control" value="<?php echo $filter_invoice_no; ?>" name="filter_invoice_no" placeholder="Invoice No" />
                      </div>
                      <div class="form-group date">
                          <label class="control-label" for="seller_id">Purchase Date Added To</label>
                          <div class="input-group date">
                              <input type="text" class="form-control" data-date-format="YYYY-MM-DD" value="<?php echo $filter_date_added_to; ?>" name="filter_date_added_to" placeholder="Purchase Date Added To" />
                              <span class="input-group-btn">
                                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                              </span>
                          </div>
                      </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="seller_id">Seller</label>
                            <input type="text" class="form-control" id="seller_id" placeholder="Seller" value="<?php echo $filter_seller_name; ?>" />
                            <input type="hidden" class="form-control"  value="<?php echo $filter_seller_id; ?>" name="filter_seller_id" placeholder="Seller" />
                            <div class="dropdown">
                              <ul class="dropdown-menu" aria-labelledby="dLabel">
                              </ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="seller_id">Purchase Value From</label>
                            <input type="text" class="form-control" value="<?php echo $filter_purchase_value_from; ?>" name="filter_purchase_value_from" placeholder="Purchase Value From" />

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="seller_id">SKU</label>
                            <input type="text" class="form-control" value="<?php echo $filter_sku; ?>" name="filter_sku" placeholder="Product SKU" />
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group date">
                            <label class="control-label" for="seller_id">Invoice Date From</label>
                            <div class="input-group date">
                                <input type="text" class="form-control" data-date-format="YYYY-MM-DD" value="<?php echo $filter_invoice_date_from; ?>" name="filter_invoice_date_from" placeholder="Invoice Date From" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="seller_id">Purchase Value To</label>
                            <input type="text" class="form-control" value="<?php echo $filter_purchase_value_to; ?>" name="filter_purchase_value_to" placeholder="Purchase Value To" />
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="seller_id">Invoice Date To</label>
                            <div class="input-group date">
                                <input type="text" class="form-control" data-date-format="YYYY-MM-DD" value="<?php echo $filter_invoice_date_to; ?>" name="filter_invoice_date_to" placeholder="Invoice Date To" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group date">
                            <label class="control-label" for="seller_id">Purchase Date Added From</label>
                            <div class="input-group date">
                                <input type="text" class="form-control" data-date-format="YYYY-MM-DD" value="<?php echo $filter_date_added_from; ?>" name="filter_date_added_from" placeholder="Purchase Date Added From" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <br>
                            <button type="submit" form="filter_form" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                        </div>
                    </div>
                </div>

          </div>
            </form>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-return">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <td><a href="<?php echo $sort_url['invoice_no']; ?>"><?php echo $text_invoice_no; ?></a></td>
                  <td><a href="<?php echo $sort_url['invoice_date']; ?>"><?php echo $text_invoice_date; ?></a></td>
                  <td><a href="<?php echo $sort_url['total_purchase_value']; ?>"><?php echo $text_pruchase_value; ?></a></td>
                  <td ><?php echo $text_purchase_firm; ?></td>
                  <td ><?php echo $text_seller; ?></td>
                  <td >Payment Status</td>
                  <td ><?php echo $text_user; ?></td>
                  <td>
                      <a href="<?php echo $sort_url['date_added']; ?>">
                        <?php echo $text_date_added; ?>
                      </a>
                  </td>
                  <td>Invoice Image</td>
                  <td>Action</td>
                  <!-- <td class="text-left"><?php //if ($sort == 'return_requested_date') { ?>
                      <a href="<?php //echo $sort_request_date; ?>" class="<?php //echo strtolower($order); ?>">Return Request Added Date</a>
                      <?php //} else { ?>
                      <a href="<?php //echo $sort_request_date; ?>">Return Request Added Date</a>
                      <?php //} ?></td> -->
                  <!-- <td><?php //echo $column_action; ?></td> -->
                </tr>
              </thead>
              <tbody>
                <?php if ($wsb_purchases) { ?>
                <?php $i = 1; ?>
                <?php foreach ($wsb_purchases as $key => $wsb_purchase) { ?>
                <tr class="parent" data-child="child_<?php echo $key; ?>" >
                  <td class="text-right">
                  <?php
                   echo $wsb_purchase['invoice_no'];
                   if ($wsb_purchase['sor_purchase']==1) {
                     echo '<br><span style="padding-left:5px;padding-right:5px;background-color:#f49090; font-size:10px;">Contains SOR</span>';
                   } ?>
                  </td>
                  <td class="text-left"><?php echo $wsb_purchase['invoice_date']; ?></td>
                  <td class="text-left"><?php echo $wsb_purchase['total_purchase_value']; ?></td>
                  <td class="text-left"><?php echo $wsb_purchase['purchase_firm']; ?></td>
                  <td class="text-left order_list_comment"><?php echo $wsb_purchase['seller']; ?></td>
                  <td class="text-left">
                      <?php if ($wsb_purchase['payment_cleared'] == 'NO') { ?>
				      <?php 	echo 'Delayed '. $wsb_purchase['payment_release_invoice_date_gap'] .' days <br>From invoice date.'; ?>
					  <?php } else { ?>
					  <?php 	echo 'Success'; ?>
					  <?php } ?>
                  </td>
                  <td class="text-left"><?php echo $wsb_purchase['user']['name']; ?></td>
                  <td class="text-left"><?php echo $wsb_purchase['date_added']; ?></td>
                  <td class="text-left">
                    <?php if($wsb_purchase['dwnlod_img_href']){ ?>
                      <a href="<?php echo $wsb_purchase['dwnlod_img_href']; ?>" class="btn btn-primary">Download Invoice Image</a>
                    <?php } ?>
                    <br><br>
                    
                  <?php /* ?>
                    <?php if(!empty($wsb_purchase['dn_list'])){ ?>
                    <select class="form-control pull-left dn_list" name="dn_list"> 
                    <option value="0">---Select DebitNote---</option>
                    <?php foreach($wsb_purchase['dn_list'] as $dn){?>
                      <option value="<?php echo $dn['debit_note_id']?>">
                        <?php echo $dn['debit_note_prefix'].$dn['debit_note_no'];?>
                      </option>
                    <?php } ?>
                    </select>
                    <?php */ ?>

                    <!-- New Debit note listing with download and cancel link -->
                    <?php if(!empty($wsb_purchase['dn_list'])){ ?>
                      <div class="col-lg-3 col-md-3">
                      <div class="debit_note_dropdown">
                          <div class="selectme">---Select DebitNote---</div>
                          <div class="debit_note_dropdown-content">
                           <ul>
                             <?php foreach($wsb_purchase['dn_list'] as $dn){?>
                                <li>
                                  <a href="<?php echo $dn['download_dn'];?>">
                                    <?php echo $dn['debit_note_prefix'].$dn['debit_note_no'];?>
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                  </a>
                                <?php 
                                 if(!empty($dn['debit_note_status'])) { ?>  
                                  <span class="cancel_dn" data-id="<?php echo $dn['debit_note_id'] ?>">
                                    &nbsp;|&nbsp;
                                    <a href="javascript:void(0);"> 
                                      Cancel
                                      <i class="fa fa-times-circle" aria-hidden="true"></i>
                                    </a>
                                  <span>
                                  <?php } ?>
                              </li>
                            <?php } ?>
                          </ul>
                          </div>
                        </div>
                      </div>
                    <!-- New Debit note listing with download and cancel link -->

                  <?php } ?>
                  </td>
                  <td class="text-right">
                      <input type="checkbox" class="form-control toggleclass">
                      <br><br>
                      <?php if($show_delete_btn){?>
                      <a data-purchaseid="<?php echo $wsb_purchase['purchase_id']; ?>" data-toggle="tooltip" title="Delete Purchase" class="btn btn-danger delete_wbs_purchase pull-left"><i class="fa fa-trash-o"></i></a>
                      <?php } ?>
                      <a href="<?php echo $wsb_purchase['return_url']; ?>" id="button-return<?php echo $wsb_purchase['purchase_id']; ?>" data-toggle="tooltip" title="Return" class="btn btn-danger"><i class="fa fa-reply"></i></a>
                  </td>
                </tr>
                <tr class="child child_<?php echo $key; ?>" style="display:none;">
                    <td class="text-center"  colspan="12">
                   <?php if(!empty($wsb_purchase['products'])) { ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>SKU</td>
                                <td>Pieces</td>
                                <td>Price Per Piece</td>
                            </tr>
                        </thead>
                        <tbody>
                    <?php foreach( $wsb_purchase['products'] as $product ){ ?>
                        <tr>
                            <td><?php echo $product['sku']; ?></td>
                            <td><?php echo $product['pieces']; ?></td>
                            <td><?php echo $product['transfer_price_per_piece']; ?></td>
                        </tr>
                    <?php }  ?>
                       </tbody>
                    </table>
                <?php } else{
                                echo "No Products Found.";
                     } ?>
                    </td>
                  </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="10"><?php echo $text_no_results; ?></td>
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
<?php echo $footer; ?>
<style>
.show_product_row + .child{
    display: table-row!important;
}
</style>
<script type="text/javascript">
$(document).ready(function(){

  $('.toggleclass').click(function(){
    if($(this).prop('checked')){
        $(this).parents('tr').addClass('show_product_row bg-success');
    }
    else{
        $(this).parents('tr').removeClass('show_product_row bg-success');
    }

  });
  ////////////////////////////////////////
  $('#seller_id').on('keyup',function(){
      if($(this).val().trim().length == 0){
          $('input[name="filter_seller_id"]').val(0);
      }
  });
  /////////////////////////////////////////
  $('#seller_id').autocomplete({
        'source': function(request, response) {
           let json = [];
           if(request.length > 0){
               if(typeof request == 'string'){
                  request = request.toLowerCase();
               }
               let sellers = <?php echo json_encode($sellers); ?>;
               $(sellers).each(function(ind,element){
                   let nickname = element['nickname'].toLowerCase();
                   let name = element['company'].toLowerCase();
                   let data;
                   if( nickname.search(request) >= 0 ){
                       data = $.parseJSON('{"label":"' + element['nickname'] + '","value":"' + element['seller_id'] + '"}');
                   }
                   if( element['seller_id']  == request ){
                       data = $.parseJSON('{"label":"' + element['company'] + '","value":"' + element['seller_id'] + '"}');
                   }
                   if( name.search(request) >= 0){
                       data = $.parseJSON('{"label":"' + element['company'] + '","value":"' + element['seller_id'] + '"}');
                   }
                   if( data ){
                       json.push(data);
                   }
               });
           }
           response(json);
        },
        'select': function(item) {
          $('#seller_id').val(item['label']);
          $('input[name="filter_seller_id"]').val(item['value']);
        }
  });
  ////////////////////////////////////////
  $('.date').datetimepicker({
    pickTime: false
  });
  /////////////////////Delete wsb Purchase
  $('.delete_wbs_purchase').click(function(){
    
    if (confirm('Are you sure you want to delete this purchase')) {
     var purchase_id = $(this).data('purchaseid');
      location.href = 'index.php?route=wsb_purchase/import/deleteWsbPurchase&token=<?php echo $token;?>&purchase_id='+purchase_id;
    }else{
      return false;
    }
  });
  /*
  * download wsb purchase image
  */
  $('.download_wsb_purchase_invoice_image').on('click', function() {
      
    var purchase_id = $(this).data('purchase_id');

    var base_url = 'index.php?route=wsb_purchase/import/downloadWsbPurchaseInvoiceImage&token=<?php echo $token;?>&purchase_id='+purchase_id;   
    location = base_url;
  });
});

  $('.dn_list').change(function(){
    var dn_id = $(this).val();
    $.ajax({
      type: 'POST',
      data: {
              'dn_id' : dn_id
            },
      url: 'index.php?route=wsb_purchase/purchase_return/downloadDebitNote&token=<?php echo $token;?>',
      dataType: 'json',
      async: false,
      complete: function(json) {
        //console.log(json.responseText);
        window.location.assign(json.responseText);
      },
    });
  });

  $('.cancel_dn').click(function(){
    var dn_id = $(this).data('id');
    var obj = $(this);
    if(dn_id > 0){
      $.ajax({
        type: 'GET',
        data: {
                'dn_id' : dn_id
              },
        url: 'index.php?route=wsb_purchase/purchase_return/cancelDebitNote&token=<?php echo $token;?>',
        dataType: 'json',
        async: false,
        success:function(json){
          window.location.assign(json);
          obj.hide();
          //reload page after 2 second
          setTimeout(function(){
            location.reload();
          },2000);
        },
        complete: function(json) {
          //location.reload();
        },
        error:function(xhr, ajaxOptions, thrownError){
          var string = xhr.responseText.replace(/<b>/g,'');
            string = string.replace(/<\/b>/g,'');
            alert("Error::\n"+string);
        }
      });
    }
  })

</script>
