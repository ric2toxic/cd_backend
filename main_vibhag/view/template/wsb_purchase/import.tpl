<?php //exit($seller_name);  ?><?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
            <div class="pull-right">
                  <button type="submit"
                          name="preview"
                          form="form-wsb-purchase"
                          data-toggle="tooltip"
                          title="<?php echo $button_save; ?>"
                          class="btn btn-primary">
                                <i class="fa fa-view"></i>Preview
                  </button>
                  <?php if (empty($error_warning) && !empty($products)) { ?>
                  <button type="submit"
                          name="submit"
                          form="form-wsb-purchase"
                          data-toggle="tooltip"
                          title="<?php echo $button_save; ?>"
                          class="btn btn-primary">
                                <i class="fa fa-save"></i>
                  </button>
                  <?php } ?>

            </div>
            <h1><?php echo $page_title; ?></h1>
            <!-- <ul class="breadcrumb">
              <?php //foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php //echo $breadcrumb['href']; ?>"><?php //echo $breadcrumb['text']; ?></a></li>
        <?php //} ?>
      </ul> -->
        </div>
  </div>
  <div class="container-fluid">
    <?php if (!empty($error_warning)) { ?>
     <button type="button" class="close" data-dismiss="alert">&times;</button>
    <div class="alert alert-danger"> <?php echo $error_warning; ?>
     
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3><i class="fa fa-list"></i><?php echo $heading_title; ?></h3>
                <a  href="<?php echo $sample_file; ?>"
                    data-toggle="tooltip"
                    class="btn pull-right"> <i class="fa fa-download"></i> Download Sample File </a>
      </div>
      <div class="panel-body">
        <div class="well">
                    <form id = "form-wsb-purchase" onsubmit="return validateForm(this)" action="" method="POST" enctype="multipart/form-data">
              <div class="row">
              <div class="col-sm-4">
                <div class="form-group required" style="position:relative">
                                    <label class="control-label" for="seller_id">Seller</label>
                                    <input autocomplete="off" required type="text" required class="form-control" id="seller_id" placeholder="Seller" value="<?php echo  $seller_name; ?>" />
                                    <!-- <input type="hidden" class="form-control"  value="<?php //echo $seller_id; ?>" name="seller_id" placeholder="Seller" /> -->
                                    <button id="dLabel11" type="button" class="hidden  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="caret pull-right" style="margin-top:5px"></span>
                                    </button>
                                    <ul class="dropdown-menu" style="height:200px;overflow-y:auto;" aria-labelledby="dLabel11">
                                        <?php if(!empty($seller_id)){ ?>
                                            <li>
                                                <a data-value="<?php echo $seller_id; ?>">
                                                    <label for="seller_<?php echo $seller_id; ?>">
                                                    <?php echo  $seller_name; ?>
                                                    </label>
                                                </a>

                                                <input type="radio"
                                                       class="hidden seller"
                                                       id="purchase_firm_<?php echo $seller_id; ?>"
                                                       name="seller_id"
                                                       value="<?php echo $seller_id; ?>"
                                                       checked  />
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <div class="form-group required">
                                    <label for="invoice_no" class="control-label">Invoice Date</label>
                                    <div class="input-group date">
                                        <input required type="text"
                                               class="form-control"
                                               id="invoice_date"
                                               name="invoice_date"
                                               placeholder="Invoice Date"
                                               data-date-format="YYYY-MM-DD"
                                               value="<?php echo $invoice_date; ?>"
                                                />

                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    
                                    <div class="input-group date">
                                    <label style="margin-left: 2px;margin-top: 2px;" for="sor_purchase">SOR Purchase</label>
                                        <input style="float: left;" <?php if($sor_purchase==1){ echo 'checked="checked"';}?> type="checkbox" id="sor_purchase" name="sor_purchase" value="<?php echo $sor_purchase; ?>" />
                                    </div>

                                    
                                </div>
                                <div class="form-group hidden" id="date-gap">
                                    <label id="payment_release_invoice_date_gap_label" for="payment_done" class="control-label" >Payment Release Invoice Date Gap</label>
                                    <input class="form-control"
                                           name="payment_release_invoice_date_gap"
                                           type="number"
                                           min="0"
                                           id="payment_release_invoice_date_gap"
                                           value="<?php echo $payment_release_invoice_date_gap; ?>" />
                                </div>
              </div>
                            <div class="col-sm-4">
                                <div class="form-group required">
                                    <label for="invoice_no" class="control-label">Purchase Firm</label>
                                    <div class="btn-group show">
                                      <button style="text-align: left;" id="dLabel" type="button" class="btn form-control btn-default prcse-btn cstm-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                          Select Purchase Firm
                                        <span class="caret pull-right" style="margin-top:5px"></span>
                                      </button>
                                      <ul class="dropdown-menu" aria-labelledby="dLabel">
                                          <?php foreach($purchase_firms as $purchase_firm){ ?>
                                              <li >
                                                  <a data-value="<?php echo $purchase_firm['purchase_firm_id']; ?>">
                                                      <label for="purchase_firm_<?php echo $purchase_firm['purchase_firm_id']; ?>">
                                                      <?php echo $purchase_firm['purchase_firm_name']; ?><br>
                                                      <?php echo $purchase_firm['purchase_firm_city']; ?><br>
                                                      <?php echo $purchase_firm['tin']; ?>
                                                      <label>
                                                  </a>
                                                  <input type="radio"
                                                         class="hidden purchase_firm"
                                                         id="purchase_firm_<?php echo $purchase_firm['purchase_firm_id']; ?>"
                                                         name="purchase_firm"
                                                         onchange="selectThis(this,'.prcse-btn')"
                                                         value="<?php echo $purchase_firm['purchase_firm_id']; ?>"
                                                         <?php echo $purchase_firm_id == $purchase_firm['purchase_firm_id'] ? 'checked' : ''; ?>  />
                                              </li>
                                          <?php } ?>
                                      </ul>
                                      <div class="clearfix"></div>
                                  </div>
                                    <!-- <select class="form-control text-uppercase" name="purchase_firm" required>
                                        <option>/option>
                                        <?php //foreach($purchase_firms as $purchase_firm){ ?>
                                            <option <?php //echo $purchase_firm_id == $purchase_firm['purchase_firm_id'] ? 'selected' : ''; ?>
                                                   value="<?php //echo $purchase_firm['purchase_firm_id']; ?>">
                                                   <?php //echo $purchase_firm['purchase_firm_name']; ?><br>
                                                   <?php //echo $purchase_firm['purchase_firm_city']; ?>
                                            </option>
                                        <?php //s} ?>
                                    </select> -->
                                </div>

                                
                                <?php if((empty($products)) || (!empty($products) && !empty($error_warning))){ ?>
                                <div class="form-group required">
                                    <label for="csvname" class="control-label">Purchase Goods Details CSV</label>
                                  <input class="form-control" name="purchase_csv"  type="file" id="purchase_csv" required />
                                </div>
                                <?php } ?>
                                <?php if(!empty($products)){ ?>
                                 <div class="form-group">
                                    <label for="purchaseimage" class="control-label">Purchase Goods Details Image</label>
                                  <input class="form-control" name="purchase_image"  type="file" id="purchase_image" />
                                </div>
                                 <?php } ?>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group required">
                                    <label for="invoice_no" class="control-label">Invoice No.</label>
                                    <input required type="text" class="form-control" id="invoice_no" name="invoice_no" value="<?php echo $invoice_no; ?>" placeholder="Invoice No" />

                                </div>
                                <div class="form-group required">
                                    <label for="payment_done" class="control-label">Payment Done</label>
                                    <select class="form-control" <?php if($sor_purchase==1){ echo 'disabled="disabled"';}?> id="payment_done" name="payment_done" onchange="showDateGap(this,'#date-gap')">
                                        <option value="1" <?php echo $payment_done == 1 ? 'selected' : ''; ?>>Yes</option>
                                        <option value="0" <?php echo $payment_done == 0 ? 'selected' : ''; ?> >No</option>
                                    </select>
                                </div>
                                <div class="input-group date">                                
                                    <?php
                                    if (!empty($products) && count($products)>0) {
                                      
                                      $totalAmount  = 0;
                                      $totalPieces  = 0;
                                      $totalTax     = 0;

                                      foreach( $products as $key => $product ){ 
                                      
                                      $totalAmount += (float)$product['pieces']*(float)$product['transfer_price_per_piece'];
                                      $totalPieces += (float)$product['pieces'];
                                      $totalTax += ((float)$product['pieces']*(float)$product['transfer_price_per_piece'])-((float)$product['pieces']*(float)(round($product['transfer_price_per_piece']/(1 + $product['seller_tax']/100),2)));

                                      }
                                      echo '<b style="font-size:18px;">Total Amount : '.$totalAmount.'</b><br><br>';
                                      echo '<b style="font-size:18px;">Total Tax : '.$totalTax.'</b><br><br>';
                                      echo '<b style="font-size:18px;">Total Pieces : '.$totalPieces.'</b>';
                                    }
                                    
                                    ?>
                                </div>
                            </div>

              </div>
            </form>
              <div class="row">
                <div class="col-ms-12">
                  <?php if(!empty($products)){ ?>
                    <table style="width: 100%;">
                      <thead>
                        <th>Sr. No.</th>
                        <th>SKU</th>
                        <th>Pieces</th>
                        <th>Base price</th>
                        <th>Tax Rate</th>
                        <th>Transfer price</th>
                        <th>Amount</th>
                        <th>Seller</th>
                        <th>Store Code</th>
                      </thead>                       
                    <tbody>
                      <?php
                      $j = 1;
                      foreach( $products as $key => $product ){ ?>
                      <tr>
                        <td><?php echo $j; ?></td>
                        <td><input style="with:90%;" type="text" form="form-wsb-purchase" name="product[<?php echo $key; ?>][sku]" value="<?php echo $product['sku']; ?>" ></td>

                        <td><input type="text" form="form-wsb-purchase" name="product[<?php echo $key; ?>][pieces]" value="<?php echo $product['pieces']; ?>" ></td>

                        <td><?php echo round($product['transfer_price_per_piece']/(1 + $product['seller_tax']/100),2); ?></td>

                        <td><input type="text" form="form-wsb-purchase" name="product[<?php echo $key; ?>][seller_tax]" value="<?php echo $product['seller_tax']; ?>" ></td>

                        <td><input type="text" form="form-wsb-purchase" name="product[<?php echo $key; ?>][transfer_price_per_piece]" value="<?php echo $product['transfer_price_per_piece']; ?>" ></td>

                        <td><b><?php echo $product['pieces']*$product['transfer_price_per_piece']; ?></b></td>

                        <td> <b><?php echo $product['nickname']; ?></b></td>
                        <td> <b><?php echo $product['store_sales']; ?></b></td>
                      </tr>
                      <?php
                      if(!empty($product['error']) && count($product['nickname'])>0){ 
                        echo '<tr style="background-color: #fef1f1; border-color: #fcd9df; color: #f56b6b;"><td></td><td colspan="8">';
                        foreach ($product['error'] as $keyer => $valueer) {
                        echo '<p>'.$valueer.'</p>';
                      ?>

                      <?php   
                        }
                        echo '</td></tr>';
                        $j++;
                      }
                      ?>                              
                      <?php  } ?>
                    </tbody>
                  </table>
                  <?php } ?>
                </div>
              </div>
        </div>
      </div>
    </div>
  </div>
</div>
<style>
.error-required{
    border: solid 1px red;
}
.cstm-btn{
    text-align: left!important;
    white-space: normal!important;
    float: none!important;
    height: auto!important;
}
</style>
<script type="text/javascript">
    $(document).ready(function(){
        selectThis('input[type="radio"].purchase_firm:checked','.prcse-btn');
        showDateGap('#payment_done','#date-gap');
      /*
      * SOR checkbox work
      */
      $('#sor_purchase').click(function(){
        if ($(this).prop("checked") == true) {
          $(this).val('1');
          if (!confirm('Are you sure you want to add SOR Purchase?')) {
            $(this).val('0');
            return false;
          }else{
            $('#payment_done').attr('disabled','disabled');
            $('#payment_release_invoice_date_gap').attr('disabled','disabled');
            $('#payment_release_invoice_date_gap,#payment_release_invoice_date_gap_label').hide();

          }
        }else{
          $(this).val('0');
        }
      });
    });

    function selectThis(obj,btn,val=''){
        let text = $(obj).siblings('a').find('label').text();
        if(btn=='#seller_id'){
            $(btn).val(val);
        }
        else{
            $(btn).html(text+'<span class="caret pull-right" style="margin-top:5px"></span>');
        }
    }
    function showDateGap(obj,target){
        if($(obj).val()==0){
            $(target).removeClass('hidden');
        }
        else{
            $(target).addClass('hidden');
        }
    }
    $('#seller_id').on(' keyup ',function(){
              let request = $(this).val();
             if( request.length > 0){
                 if(typeof request == 'string'){
                    request = $(this).val().toLowerCase().trim();
                 }
                 $(this).siblings('.dropdown-menu').html('');
                 let input = $(this);
                 let sellers = <?php echo json_encode($sellers); ?>;
                 $(sellers).each(function(ind,element){

                     let nickname = element['nickname'].toLowerCase();
                     let name = element['company'].toLowerCase();
                     if( nickname.search(request) >= 0 || element['seller_id']  == request || name.search(request) >= 0 ){
                         let li  = '<li>';
                             li += '<a data-value="'+ element.seller_id +'">';
                             li +=    '<label for="seller_'+ element.seller_id +'">';
                             li +=       element.company + '<br>' + element.address1 + '<br> ' + element.address2;
                             li +=       '<br>'+element.city;
                             
                             if (element.gst_provisional_id!=null) {
                              li +=       '<br>'+element.gst_provisional_id;
                             }

                             li +=    '</label>';
                             li += '</a>';
                             li += '<input type="radio" onchange="selectThis(this,\'#seller_id\',\'' + (element.company).replace(/'/g, "\\'") + '\')" class="hidden seller_id" id="seller_' + element.seller_id +'" name="seller_id" value="' + element.seller_id + '" />';
                             li += '</li>';
                         input.siblings('.dropdown-menu').append(li);
                     }
                 });
                 $('#dLabel11').click();
             }
        });
        $('.date').datetimepicker({
            pickTime: false,
            maxDate: new Date()
          });

    function validateForm(form){
        $(form).find(' .form-control ').each(function(){
            if($(this).prop('required') && $(this).val().length == 0){
                $(this).addClass('error-required');
            }
            else{
                $(this).removeClass('error-required');
            }
        });

        if($(form).find('.error-required').length > 0){
            return false;
        }
    }
</script>
<?php echo $footer; ?>