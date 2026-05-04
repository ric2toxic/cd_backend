
<?php echo $header; 
?>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?php
echo $column_left; ?>
<div id="content">
    <div class="modal" id="sellerInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content invoice-comment col-sm-12">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Edit Seller Invoice Payment Status</h4>
          </div>
          <div class="modal-body">
              <div class="modal-body">
                  <label class="control-label" for="input_invoice_comment"><?php echo $entry_comment; ?></label> <br>
                                <input type="text" name="invoice_physically_received_comment" placeholder="<?php echo $entry_comment_text; ?>" id="input_invoice_comment" class="form-control " />
              </div>
              <br>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" 
                    class="btn btn-primary"
                    data-order-no='' 
                    data-sllr-inv-id='' 
                    onclick="UpdateSellerInvoiceStatus(this)" >Save changes</button>
          </div>
        </div>
      </div>
    </div>

    <div class="page-header">
        <div class="container-fluid">
            <h1>
              <?php echo $heading_title; ?>
            </h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li>
                    <a href="<?php echo $breadcrumb['href']; ?>">
                      <?php echo $breadcrumb['text']; ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>    

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="well">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-seller-name"><?php echo $entry_seller_name; ?></label>
                                <input type="text" name="filter_seller_name" value="<?php echo ($filter_seller_name ?? ''); ?>" placeholder="<?php echo ($entry_seller_name ?? ''); ?>" id="input-seller-name" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="select-location"><?php echo $entry_location; ?></label>
                                <input type="text" name="filter_location" value="<?php echo ($filter_location ?? ''); ?>" placeholder="<?php echo ($entry_location ?? ''); ?>" id="input-location" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-order-no"><?php echo $entry_order_no; ?></label> <br>
                                <input type="text" name="filter_order_no" value="<?php echo ($filter_order_no ?? ''); ?>" placeholder="<?php echo ($entry_order_no ?? ''); ?>" id="input-order-no" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="select-status"><?php echo $entry_status; ?></label>
                                <select name="filter_status" id="select-status" class="form-control">
                                    <option value="*"><?php echo $text_select_status; ?></option>
                                    <?php $status = array('0'=>'Pending','1'=>'Received','2'=>'Rejected','3'=>'Approved');?>
                                        <?php foreach($status as $status_key =>  $status_value) { ?>
                                            <option value="<?php echo $status_key; ?>" 
                                                <?php echo (!empty($filter_status) && $status_key == $filter_status) ? 'selected' : '' ; ?> ><?php echo $status_value; ?>
                                            </option>
                                        <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-purchase-invoice-date-from"><?php echo $entry_invoice_date_from; ?></label>
                                <div class="input-group date">
                                  <input type="text" name="filter_purchase_invoice_date_from" value="<?php echo ($filter_purchase_invoice_date_from ?? ''); ?>" placeholder="<?php echo ($entry_invoice_date_from ?? ''); ?>" data-date-format="YYYY-MM-DD" id="input-purchase-invoice-date-from" class="form-control" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-purchase-invoice-date-to"><?php echo $entry_invoice_date_to; ?></label>
                                <div class="input-group date">
                                  <input type="text" name="filter_purchase_invoice_date_to" value="<?php echo ($filter_purchase_invoice_date_to ?? ''); ?>" placeholder="<?php echo ($entry_invoice_date_to ?? ''); ?>" data-date-format="YYYY-MM-DD" id="input-purchase-invoice-date-to" class="form-control" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>
                            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                            <button type="button" class="report_btn btn btn-info filter_report_btn" id="download_seller_invoice_report"><i class="fa fa-file-text" aria-hidden="true"></i>Download CSV</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <!--<td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td> -->
                                <td class="text-right">
                                    <?php echo $column_seller_name; ?>
                                </td>
                                <td class="text-left">
                                    <?php echo $column_location; ?>
                                </td>
                                <td class="text-left" id="order_no">
                                    <?php echo $column_order_no; ?>
                                </td>
                                <td class="text-left">
                                    <?php echo $column_order_date; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $column_purchase_invoice_date; ?>
                                </td>

                                <td class="text-center">
                                    <?php echo $column_purchase_invoice_no; ?>
                                </td>

                                <td class="text-center">
                                    <?php echo $column_total_value; ?>
                                </td>

                                <td class="text-center">
                                    <?php echo $column_total_product_value; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $column_total_tax; ?>
                                </td>
                                <td class="text-right">
                                    Payment Action
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($invoices) { ?>
                            <?php
                             foreach ($invoices as $invoice) { ?>
                            <tr>
                                <td class="text-left" width="20%" id="seller_name">
                                    <?php echo $invoice['seller_name']; ?>
                                </td>
                                <td class="text-left" width="10%">
                                    <?php echo $invoice['location']; ?>
                                </td>
                                <td class="text-left" width="10%" id="order_no">
                                <strong><?php echo $invoice['order_no']; ?></strong>
                                </td>
                                <td class="text-left" width="10%">
                                   <?php echo $invoice['order_date']; ?>
                                </td>

                                <td class="text-right" width="10%">
                                   <?php echo $invoice['purchase_invoice_date']; ?>
                                </td>
                                <td class="text-left" width="10%">
                                    <div class="order_list_comment">
                                        <span id="sllr_invoice_no_<?php echo $invoice['seller_invoice_id']; ?>" 
                                              html-data="<?php echo $invoice['seller_invoice_id']; ?>" >
                                            <?php echo $invoice['purchase_invoice_no']; ?>
                                        </span>  
                                    </div>                                    
                                    <span style="cursor: pointer;" 
                                          class="sllr_invoice_no" 
                                          id="sllr_invoice_no_edit_<?php echo $invoice['seller_invoice_id']; ?>" 
                                          html-data="<?php echo $invoice['seller_invoice_id']; ?>">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <span style="display:none;" 
                                          id="sllr_invoice_no_input_<?php echo $invoice['seller_invoice_id']; ?>">
                                        <input type="text" 
                                               id="sllr_invoice_no_val_<?php echo $invoice['seller_invoice_id']; ?>" 
                                               value="<?php echo $invoice['purchase_invoice_no']; ?>" />
                                        <span style="cursor: pointer;" 
                                              class="save_sllr_invoice_no" 
                                              html-data="<?php echo $invoice['seller_invoice_id']; ?>" 
                                              html-model="<?php echo $invoice['purchase_invoice_no']; ?>">
                                              <i class="fa fa-save"></i>
                                        </span>
                                        <span style="cursor: pointer;" 
                                              class="cancel_sllr_invoice_no" 
                                              html-data="<?php echo $invoice['seller_invoice_id']; ?>">
                                              <i class="fa fa-ban"></i>
                                        </span>
                                    </span>
                                </td>
                                <td class="text-left" width="10%">
                                   <?php echo $invoice['total_value']; ?>
                                </td>
                                <td class="text-left" width="10%">
                                   <?php echo $invoice['total_product_value']; ?>
                                </td>
                                <td class="text-left" width="5%">
                                   <center><?php echo $invoice['total_tax']; ?></center>
                                </td>

                                <td class="text-center">
                                    <?php if ($invoice['status'] == 0 && $invoice['invoice_image_upload'] == 0) { ?>
                                      <button type="button"
                                              data-toggle="modal" 
                                              class="alert alert-danger pending_btn invoice_status_btn"
                                              data-toggle="modal"
                                              data-invoice_image = "<?php echo $invoice['invoice_image']; ?>"
                                              data-status = "<?php echo $invoice['status']; ?>"
                                              data-comment = "<?php echo $invoice['comment']; ?>"
                                              data-suborder_id = "<?php echo $invoice['suborder_id']; ?>" 
                                              data-order_no = "<?php echo $invoice['order_no']; ?>"
                                              data-seller-invoice-id = "<?php echo $invoice['seller_invoice_id']; ?>"
                                              data-seller-invoice-no = "<?php echo $invoice['seller_invoice_no']; ?>"
                                              data-seller_name = "<?php echo $invoice['seller_name']; ?>"
                                              data-target="#invoiceModal"> Pending </button>
                                     <?php } else if($invoice['status'] == 2 && $invoice['invoice_image_upload'] == 1) { ?>
                                      <button type="button" class="btn btn-primary alert alert-danger pending_btn invoice_status_btn"
                                             data-toggle="modal"
                                             data-invoice_image = "<?php echo $invoice['invoice_image']; ?>"
                                              data-status = "<?php echo $invoice['status']; ?>"
                                              data-comment = "<?php echo $invoice['comment']; ?>"
                                              data-suborder_id = "<?php echo $invoice['suborder_id']; ?>" 
                                              data-order_no = "<?php echo $invoice['order_no']; ?>"
                                              data-seller-invoice-id = "<?php echo $invoice['seller_invoice_id']; ?>"
                                              data-seller-invoice-no = "<?php echo $invoice['seller_invoice_no']; ?>"
                                              data-seller_name = "<?php echo $invoice['seller_name']; ?>"
                                              data-target="#invoiceModal">Rejected</button>
                                     <?php } else if($invoice['status'] == 0 && $invoice['invoice_image_upload'] == 1) { ?>
                                      <button type="button" class="btn btn-primary alert alert-success invoice_status_btn"
                                             data-toggle="modal"
                                             data-invoice_image = "<?php echo $invoice['invoice_image']; ?>"
                                              data-status = "<?php echo $invoice['status']; ?>"
                                              data-comment = "<?php echo $invoice['comment']; ?>"
                                              data-suborder_id = "<?php echo $invoice['suborder_id']; ?>" 
                                              data-order_no = "<?php echo $invoice['order_no']; ?>"
                                              data-seller-invoice-id = "<?php echo $invoice['seller_invoice_id']; ?>"
                                              data-seller-invoice-no = "<?php echo $invoice['seller_invoice_no']; ?>"
                                              data-seller_name = "<?php echo $invoice['seller_name']; ?>"
                                              data-target="#invoiceModal">Received</button> 
                                    <?php } else  { ?>          
                                      <button type="button" class="btn btn-primary alert alert-success invoice_status_btn"
                                             data-toggle="modal"
                                             data-invoice_image = "<?php echo $invoice['invoice_image']; ?>"
                                              data-status = "<?php echo $invoice['status']; ?>"
                                              data-comment = "<?php echo $invoice['comment']; ?>"
                                              data-suborder_id = "<?php echo $invoice['suborder_id']; ?>" 
                                              data-order_no = "<?php echo $invoice['order_no']; ?>"
                                              data-seller-invoice-id = "<?php echo $invoice['seller_invoice_id']; ?>"
                                              data-seller-invoice-no = "<?php echo $invoice['seller_invoice_no']; ?>"
                                              data-seller_name = "<?php echo $invoice['seller_name']; ?>"
                                              data-target="#invoiceModal">Approved</button> 
                                    <?php } ?>
                                  
                                </td>
                                <?php } ?>
                                
                              </tr>

                            
                            <?php } else { ?>
                            <tr>
                              <td class="text-center" colspan="10"><?php echo $text_no_results; ?></td>
                            </tr>
                          <?php  } ?>
                    </tbody>
                  </table>
                </div>
                <div class="row">
                  <div class="col-sm-12 text-left"><?php echo $pagination; ?></div>
                  <div class="col-sm-12 text-right"><?php //echo $results; ?></div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<!-- Modal -->
<div id="invoiceModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" style="width: 800px;">

    <!-- Modal content-->
    
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="invoice_close_btn close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><b>Seller Name:</b> <span id="invoice_seller_name"></span>
         <br /> <b>Order No.</b> <span id="invoice_order_no"></span> &nbsp; 
        <b>Invoice No.</b> <span id="invoice_no"></span>
        </h4>

        <button type="button" id="upload_btn" class="btn btn-primary pull-right" onclick="$('#invoice_new_image').click();" style="margin-top: -26px; margin-right: 24px;">Upload</button>

      </div>
      <div class="modal-body invoice_image_view">
            <!-- <div class="invoice_image" style="width:100%;"></div> -->
            <div class="container1">
              <div class="imageBox" >
                  <div class="mask"></div>
                  <div class="thumbBox"></div>
              </div>
            </div>
        </div>
    </div>


   <div class="modal_icon_box">
        

        <button type="button" class="btn btn-deafult pull-left" onclick="$('#uploadForm').toggle();"><i class="fa fa-pencil"></i></button>
        
        <button type="button" class="btn btn-deafult" id="rotateLeft" title="Rotate Left"><i class="fa fa-rotate-right invoiceModalCropIcon"></i></button>
        
        <button type="button" class="btn btn-deafult" id="rotateRight" title="Rotate Right"><i class="fa fa-rotate-left invoiceModalCropIcon"></i></button>
        
        <button type="button" class="btn btn-deafult" id="zoomIn" title="Zoom In"><i class="fa fa-search-plus invoiceModalCropIcon"></i></button>
        <button type="button" class="btn btn-deafult" id="zoomOut" title="Zoom Out"><i class="fa fa-search-minus invoiceModalCropIcon"></i></button>
        <button type="button" class="btn btn-deafult" id="crop"  title="Crop"><i class="fa fa-crop invoiceModalCropIcon"></i></button>
        
        <button type="button" class="btn btn-primary alert-danger" id="reject_btn"><i class="fa fa-remove"></i></button>
        
        <button type="button" class="btn btn-primary pull-right alert-success" id="approve_btn"><i class="fa fa-check"></i></button>
          
          <br />
        <form id="uploadForm" enctype="multipart/form-data" style="display: none;">
          <input type="hidden" id="updatedImage" name="updatedImage">
        <input type="hidden" id="status" name="status" value="0">
        <input type="hidden" id="invoice_id" name="seller_invoice_id">
        <input type="hidden" id="suborder_id" name="suborder_id">
        <input type="file" name="invoice_new_image" id="invoice_new_image" style="display:none;">
        <br />
        <textarea style="width:100%; height:60px;" id="comment" name="comment" placeholder="Comment"></textarea>
        <button type="button" class="btn btn-primary  pull-right" id="save_comment_btn">Save</button>
        </form>
    </div>
    
  </div>
</div>

<script type="text/javascript">
    
    $('#button-filter').on('click', function() {

        url = 'index.php?route=accounts/seller_invoice_verification&token=<?php echo $token; ?>';

        var filter_seller_name = $('input[name=\'filter_seller_name\']').val();

        if (filter_seller_name) {
            url += '&filter_seller_name=' + encodeURIComponent(filter_seller_name);
        }

        var filter_order_no = $('input[name=\'filter_order_no\']').val();

        if (filter_order_no) {
            url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
        }

        var filter_purchase_invoice_date_from = $('input[name=\'filter_purchase_invoice_date_from\']').val();

        if (filter_purchase_invoice_date_from) {
            url += '&filter_purchase_invoice_date_from=' + encodeURIComponent(filter_purchase_invoice_date_from);
        }

        var filter_purchase_invoice_date_to = $('input[name=\'filter_purchase_invoice_date_to\']').val();

        if (filter_purchase_invoice_date_to) {
            url += '&filter_purchase_invoice_date_to=' + encodeURIComponent(filter_purchase_invoice_date_to);
        }

        var filter_location = $('input[name=\'filter_location\']').val();

        if (filter_location) {
            url += '&filter_location=' + encodeURIComponent(filter_location);
        }

        var filter_status = $('select[name=\'filter_status\']').val();

        if (filter_status!='*') {
            url += '&filter_status=' + encodeURIComponent(filter_status);
        }

        location = url;
    });
    
</script>
<script type="text/javascript" src="view/javascript/jquery.canvasCrop.js" ></script>
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript">
    <!--
    $('.date').datetimepicker({
        pickTime: false
    });
    //-->
</script>

<?php echo $footer; ?>
<script type="text/javascript">
  $('input,select').on('keypress',function(e){
    if (e.keyCode == 13) {
      $('#button-filter').trigger('click');
    }
  });




  function UpdateSellerInvoiceStatus(obj){

    var comment = $(obj).parent().parent('.invoice-comment').find('#input_invoice_comment').val();
    //var order_no = $(obj).attr('data-order-no');
    var sllr_inv_id = $(obj).attr('data-sllr-inv-id');
    
    $.ajax({
        url: 'index.php?route=accounts/seller_invoice_verification/updateInvoiceComment&token=<?php echo $token;?>&comment=' + comment +'&seller_inv_id='+sllr_inv_id,
        type:'POST',
        beforeSend: function(){ },
        complete: function(){ },
        success: function(json) {
          if (json['error']) {
            alert(json['error']);
          }
          
          window.location.reload();
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });

  }
</script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});

$('#download_seller_invoice_report').click(function(){
    var url = "index.php?route=accounts/seller_invoice_verification/index&token=<?php echo $token;?>";

    var filter_seller_name = $('input[name=\'filter_seller_name\']').val();

    if (filter_seller_name != '') {
        url += '&filter_seller_name=' + encodeURIComponent(filter_seller_name);
    }

    var filter_order_no = $('input[name=\'filter_order_no\']').val();

    if (filter_order_no != '') {
        url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
    }

    var filter_purchase_invoice_date_from = $('input[name=\'filter_purchase_invoice_date_from\']').val();

    if(filter_purchase_invoice_date_from !='' ){
        url += '&filter_purchase_invoice_date_from=' + encodeURIComponent(filter_purchase_invoice_date_from);
    }

    var filter_purchase_invoice_date_to = $('input[name=\'filter_purchase_invoice_date_to\']').val();;

    if(filter_purchase_invoice_date_to !='' ){
        url += '&filter_purchase_invoice_date_to=' + encodeURIComponent(filter_purchase_invoice_date_to);
    }

    var filter_location = $('input[name=\'filter_location\']').val();
    if (filter_location != '') {
        url += '&filter_location=' + encodeURIComponent(filter_location);
    }

    var filter_status = $('select[name=\'filter_status\']').val();
    if (filter_status != '*') {
        url += '&filter_status=' + encodeURIComponent(filter_status);
    }
    
    url += '&download_seller_invoice_report=true';
        
    location = url;

});


 //Update sllr_invoice_no
    $("span.sllr_invoice_no").click(function(){
        var pid = $(this).attr("html-data");
        $("span#sllr_invoice_no_"+pid).hide();
        $("span#sllr_invoice_no_edit_"+pid).hide();
        $("span#sllr_invoice_no_input_"+pid).show();
    });

    $("span.save_sllr_invoice_no").click(function(){
        $("span#sllr_invoice_no_input_"+$(this).attr("html-data")).hide();
        $("span#sllr_invoice_no_"+$(this).attr("html-data")).html($("input#sllr_invoice_no_val_"+$(this).attr("html-data")).val());
        $("span#sllr_invoice_no_"+$(this).attr("html-data")).show();
        $("span#sllr_invoice_no_edit_"+$(this).attr("html-data")).show();

        $.ajax({
            url : "index.php?route=accounts/seller_invoice_verification/updateSellerInvoiceNo&token=<?php echo $token; ?>",
            type: "post",
            dataType: "json",
            data: "seller_invoice_id="+$(this).attr("html-data")+
                  "&seller_invoice_no="+$("input#sllr_invoice_no_val_"+$(this).attr("html-data")).val(),
            success: function( data ) {
                if(data['status']) {
                    alert(data['message']);
                    window.location.reload();
                }
            }
        });
    });

    $("span.cancel_sllr_invoice_no").click(function(){
        $("span#sllr_invoice_no_input_"+$(this).attr("html-data")).hide();
        var preval = $("span#sllr_invoice_no_"+$(this).attr("html-data")).text().trim();
        $("input#sllr_invoice_no_val_"+$(this).attr("html-data")).attr("value",preval);
        $("span#sllr_invoice_no_"+$(this).attr("html-data")).show();
        $("span#sllr_invoice_no_edit_"+$(this).attr("html-data")).show();
    });


$(document).ready(function(){
 var rot = 0,ratio = 1;
    var CanvasCrop='';
    var imgUpload='0';
  $('.invoice_status_btn').click(function(){
    $(this).parent("td").parent("tr").css('background-color','#ccc');
    var order_no = $(this).data('order_no');
    var seller_invoice_id = $(this).data('seller-invoice-id');
    var seller_invoice_no = $(this).data('seller-invoice-no');
    var comment = $(this).data('comment');
    var status  = $(this).data('status');
    var invoice_image  = $(this).data('invoice_image');
    var suborder_id  = $(this).data('suborder_id');
    var seller_name  = $(this).data('seller_name');

    $("#comment").val(comment);
    $("#suborder_id").val(suborder_id);
    $("#invoice_id").val(seller_invoice_id);
    $("#invoice_order_no").html(order_no);
    $("#invoice_no").html(seller_invoice_no);
    $("#invoice_seller_name").html(seller_name);
    var imageCropSplit=invoice_image.split('/img');
    if(imageCropSplit.length >1){
      var invoice_image=imageCropSplit[0]+imageCropSplit[1];
    }
     CanvasCrop = $.CanvasCrop({
            cropBox : ".imageBox",
            //thumbBox : "#dynamic",
            imgSrc : invoice_image,
            limitOver : 1
        });
     imgUpload='0';
     $('#updatedImage').val('');
    //=======crop and rotate image function End==========//

  });

  
  $('#invoice_new_image').on('change', function(){
            var reader = new FileReader();
            reader.onload = function(e) {
                CanvasCrop = $.CanvasCrop({
                    cropBox : ".imageBox",
                    imgSrc : e.target.result,
                    limitOver : 1
                });
                rot =0 ;
                ratio = 1;
            }
            imgUpload='1';
            $('#updatedImage').val('');
            reader.readAsDataURL(this.files[0]);
            this.files = [];
        });
        

   //=======rotate image function Start==========//
   $("#rotateLeft").on("click",function(){
            rot -= 90;
            rot = rot<0?270:rot;
            CanvasCrop.rotate(rot);
        });
        $("#rotateRight").on("click",function(){
            rot += 90;
            rot = rot>360?90:rot;
            CanvasCrop.rotate(rot);
        });
        $("#zoomOut").on("click",function(){
            ratio =ratio*0.9;
            CanvasCrop.scale(ratio);
        });
        $("#zoomIn").on("click",function(){
            ratio =ratio*1.1;
            CanvasCrop.scale(ratio);
        });
        $("#crop").on("click",function(){
            var updatedImageVal=CanvasCrop.getDataURL("jpeg",imgUpload);
            $('#updatedImage').val(updatedImageVal);
            //var src = CanvasCrop.getDataURL("png");
            //console.log(updatedImageVal);
            //$("body").append("<div style='word-break: break-all;'>"+src+"</div>");  
            //$(".container").append("<img src='"+src+"' />");
        });

    //=======rotate image function End==========//


  $('.invoice_close_btn').click(function(){
    $(".table-bordered tr").css('background-color','#fff');
  });


  $('#approve_btn').click(function(){
     $(".invoice_msg").remove();
     $("#status").val(1);
     $("#uploadForm").submit();
  });



  $('#reject_btn').click(function(){
         $(".invoice_msg").remove();
         $("#status").val(2);
         $("#uploadForm").submit();
  });

  $('#save_comment_btn').click(function(){
        $.ajax({
            url: "index.php?route=accounts/seller_invoice_verification/save_comment&token=<?php echo $token; ?>",
            type: "POST",
            data:  "seller_invoice_id="+$("#invoice_id").val()+"&comment="+$("#comment").val(),
            dataType: "json",
            success: function(data)
            {
                  var msg = data['message'];
                 if(data['status'])
                 {
                  $(".modal-body").prepend("<div class='alert alert-success invoice_msg'><i class='fa fa-check-circle'></i> Success: "+msg+"</div>");
                  window.location.reload();
                 }
                 else
                 {
                    $(".modal-body").prepend("<div class='alert alert-danger invoice_msg'><i class='fa fa-exclamation-circle'></i> Error: "+msg+"</div>");
                 }
            },
            error: function(data) 
            {
               $(".modal-body").prepend("<div class='alert alert-danger invoice_msg'><i class='fa fa-exclamation-circle'></i> Error: server internal error</div>");
            }           
       });
  });


    $("#uploadForm").on('submit',(function(e) {
        e.preventDefault();
        $.ajax({
            url: "index.php?route=accounts/seller_invoice_verification/invoice_approve&token=<?php echo $token; ?>",
            type: "POST",
            data:  new FormData(this),
            contentType: false,
            processData:false,
            dataType: "json",
            success: function(data)
            {
                  var msg = data['message'];
                 if(data['status'])
                 {
                  $(".modal-body").prepend("<div class='alert alert-success invoice_msg'><i class='fa fa-check-circle'></i> Success: "+msg+"</div>");
                  window.location.reload();
                 }
                 else
                 {
                    $(".modal-body").prepend("<div class='alert alert-danger invoice_msg'><i class='fa fa-exclamation-circle'></i> Error: "+msg+"</div>");
                 }
            },
            error: function(data) 
            {
               $(".modal-body").prepend("<div class='alert alert-danger invoice_msg'><i class='fa fa-exclamation-circle'></i> Error: server internal error</div>");
            }           
       });
    }));
    $('.modal-dialog').draggable({
    handle: $('.modal-header')
    });

});  

// function showPreview(objFileInput) {
//     if (objFileInput.files[0]) {
//         var fileReader = new FileReader();
//         fileReader.onload = function (e) {
//             //$('#dynamic').attr('src', e.target.result);
//             CanvasCrop = $.CanvasCrop({
//                     cropBox : ".imageBox",
//                     imgSrc : e.target.result,
//                     limitOver : 1
//                 });
//                 rot =0 ;
//                 ratio = 1;
//         }
//         fileReader.readAsDataURL(objFileInput.files[0]);


      
//     }
// }  
</script>