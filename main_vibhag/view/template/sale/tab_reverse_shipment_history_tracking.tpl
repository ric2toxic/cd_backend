<div class="table-responsive">

<?php if(!empty($reverse_shipments)){ ?>
<table style="width:100%;" border="0" >
  <tr>
    <td>
      <h4><b>Reverse Shipment(s)</b></h4>
    </td>
  </tr>
</table><br>
  
  <table class="table table-bordered">
    <tr>
      <th>S. No.</th>
      <th>Master ReturnId(s)</th>
      <th>Shipper</th>
      <th>Shipping Method</th>
      <th>Tracking No.</th>
      <th>Token No.</th>
      <th>Date</th>
      <th>Qty</th>
      <th>Value</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
    <?php 
      $row_count = 0;
      $courier_partners = array_map( 'strtolower',   
                                array_column($courier_partners_reverse,'courier_name')
                                );
      foreach($reverse_shipments as $shipping_id => $reverse_shipment){
        $row_count++;
        $is_self_courier = false;
        if( !empty($reverse_shipment['shipping_method'])
            && 
            $reverse_shipment['shipping_method'] == 'self_courier'
          ) {
          $is_self_courier = true;
        }

    ?>
    <tr class="products" <?php if($is_self_courier){?> style="background-color:#f3e3e3; color:#666666" <?php } ?> >
      <td><?php echo $row_count;?></td>
      <td>
        <?php echo isset($reverse_shipment['master_return_id'])?$reverse_shipment['master_return_id']:'0'; ?>
      </td>
      <td>
        <?php echo isset($reverse_shipment['courier_company'])?$reverse_shipment['courier_company']:''; ?>
      </td>
      <td>
        <?php $method =  isset($reverse_shipment['shipping_method'])
                  ? ucwords(str_replace('_',' ',$reverse_shipment['shipping_method']))
                  :''; 
              if($is_self_courier) {
                echo '<b>'.$method.'</b>';
              }else{
                echo $method; 
            }    
          ?>
      </td>
      <td>
        <?php echo isset($reverse_shipment['tracking_no'])?$reverse_shipment['tracking_no']:''; ?>
      </td>
      <td>
        <?php 
        if(!empty($reverse_shipment['request_param']))
        {
          $request_reponse_data = unserialize($reverse_shipment['request_param']);
          if(!empty($request_reponse_data[1]->GenerateWayBillResult->TokenNumber) )
          {
            echo $request_reponse_data[1]->GenerateWayBillResult->TokenNumber;  
          }
         }
        ?>
      </td>
      <td>
        <?php echo isset($reverse_shipment['date_added'])?$reverse_shipment['date_added']:''; ?>
      </td>
      <td>
        <?php echo isset($reverse_shipment['qty'])?$reverse_shipment['qty']:'0'; ?>
      </td>
      <td>
        <?php echo isset($reverse_shipment['value'])?$reverse_shipment['value']:'0'; ?>
      </td>
      <td>
        <?php echo !empty($reverse_shipment['equivalent_status'])?$reverse_shipment['equivalent_status']:'';?> 
        <?php echo !empty($reverse_shipment['shipment_status'])?'<br>('.$reverse_shipment['shipment_status'].')':''; ?>
      </td>
      <td>
        <a class="btn button-default"
          id="tracking_button_<?php echo $shipping_id;?>"
          style="border: solid 1px #ccc;background:#fff"
          href="javascript:void(0)"
          data-opid="shipping_id"
          onclick="getReverseShipmentHistory(<?php echo $shipping_id; ?>)"
          toggle="tooltip"
          title="View History"
          data-original-title="View History">
          <i class="fa fa-eye"></i>
        </a>
       <?php if(in_array(strtolower($reverse_shipment['courier_company']),$courier_partners) 
                &&
                isset($reverse_shipment['is_cancel']) 
                && 
                !$reverse_shipment['is_cancel']
                ) { ?>  
        <a class="btn button-default"
          id="shipment_cancel_button_<?php echo $shipping_id;?>"
          style="border: solid 1px #ccc;background:#fff"
          href="javascript:void(0)"
          data-opid="cancel_shipping_id"
          onclick="cancelReverseShipment(<?php echo $shipping_id; ?>,'reverse')"
          toggle="tooltip"
          title="Cancel Reverse Shipment"
          data-original-title="Cancel Reverse Shipment">
          <i class="fa fa-close" style="color:red"></i>
        </a>
        <?php } ?>

        <?php if(!empty($reverse_shipment['shipping_slip'])) { ?>  
        <a class="btn button-default" target="_blank"
          id="shipment_cancel_button_<?php echo $shipping_id;?>"
          style="border: solid 1px #ccc;background:#fff"
          href="<?php echo $reverse_shipment['download_slip'];?>"
          toggle="tooltip"
          title="Download shipping slip">
          <i class="fa fa-picture-o" aria-hidden="true"></i>
        </a>
        <?php } ?>
      </td>
   
      <input type="hidden" id="vendor_<?php echo $shipping_id;?>" value="<?php echo $reverse_shipment['warehouse_name'];?>">
        <input type="hidden" id="vendor_city_<?php echo $shipping_id;?>" value="<?php echo $reverse_shipment['city'];?>">
        <input type="hidden" id="tracking_no_<?php echo $shipping_id;?>" value="<?php echo $reverse_shipment['tracking_no'];?>">
        <input type="hidden" id="courier_company_<?php echo $shipping_id;?>" value="<?php echo $reverse_shipment['courier_company'];?>">
    </tr>
    <?php } ?>
  </table><br><br>
<?php } ?>
<!--------------------------------- Shipment(s) Back to Customer History  ---------------->
<?php if(!empty($shipments_backto_cutomer)){ ?>
<table style="width:100%;" border="0" >
  <tr>
    <td>
      <h4><b>Shipment(s) Back To Customer</b></h4>
    </td>
  </tr>
</table><br>
  <table class="table table-bordered">
    <tr>
      <th>S. No.</th>
      <th>ReturnId(s)</th>
      <th>Shipper</th>
      <th>Tracking No.</th>
      <th>Token No.</th>
      <th>Date</th>
      <th>Qty</th>
      <th>Value</th>
      <th>Status</th>
      <th></th>
    </tr>
    <?php 
      $row_count = 0;
      foreach($shipments_backto_cutomer as $shipping_id => $shipments){
        $row_count++;

        $is_self_courier = false;
        if( isset($shipments['shipping_method'])
            && 
            $shipments['shipping_method'] === 'self_courier'
            &&
            empty($shipments)
          ) {
          $is_self_courier = true;
        }
    ?>
    <tr class="products" <?php if($is_self_courier){?> style="background-color:#f3e3e3; color:#666666" <?php } ?> >
      <td><?php echo $row_count;?></td>
      <td>
        <?php echo isset($shipments['return_ids'])?$shipments['return_ids']:'0'; ?>
      </td>
      <td>
        <?php echo isset($shipments['courier_company'])?$shipments['courier_company']:''; ?>
      </td>
      <td>
        <?php echo isset($shipments['tracking_no'])?$shipments['tracking_no']:''; ?>
      </td>
      <td>
        <?php
        if(!empty($reverse_shipment['request_param']))
        {
          $request_reponse_data = unserialize($reverse_shipment['request_param']);
          if(!empty($request_reponse_data[1]->GenerateWayBillResult->TokenNumber) )
          {
            echo $request_reponse_data[1]->GenerateWayBillResult->TokenNumber;  
          }
         }
        ?>
      </td>
      <td>
        <?php echo isset($shipments['date_added'])?$shipments['date_added']:''; ?>
      </td>
      <td>
        <?php echo isset($shipments['qty'])?$shipments['qty']:'0'; ?>
      </td>
      <td>
        <?php echo isset($shipments['value'])?$shipments['value']:'0'; ?>
      </td>
      <td>
        <?php echo !empty($shipments['equivalent_status'])?$shipments['equivalent_status']:'';?> 
        <?php echo !empty($shipments['shipment_status'])?'<br>('.$shipments['shipment_status'].')':''; ?>
      </td>
      <td>
        <input type="hidden" id="vendor_<?php echo $shipping_id;?>" value="<?php echo $shipments['warehouse_name'];?>">
        <input type="hidden" id="vendor_city_<?php echo $shipping_id;?>" value="<?php echo $shipments['city'];?>">
        <input type="hidden" id="tracking_no_<?php echo $shipping_id;?>" value="<?php echo $shipments['tracking_no'];?>">
        <input type="hidden" id="courier_company_<?php echo $shipping_id;?>" value="<?php echo $shipments['courier_company'];?>">
        <?php if(!$is_self_courier) { ?>
        <a class="btn button-default"
          id="tracking_button_<?php echo $shipping_id;?>"
          style="border: solid 1px #ccc;background:#fff"
          href="javascript:void(0)"
          data-opid="shipping_id"
          onclick="getReverseShipmentHistory(<?php echo $shipping_id; ?>)"
          toggle="tooltip"
          title="View History"
          data-original-title="View History">
          <i class="fa fa-eye"></i>
        </a>
        <?php } ?>

    <?php if(strtolower($shipments['courier_company']) == 'fedex' && !empty($shipments['file_name'])){ ?>

    <a href="index.php?route=download/download&token=<?php echo $token?>&file_desc=shipping_label&file_path=<?php echo $shipments['file_name'];?>" class="btn button-default" style="border: solid 1px #ccc;background:#fff">
          <i class="fa fa-download" aria-hidden="true"></i>
        </a>

    <?php } else { ?>

        <a href="index.php?route=sale/return/shippingLabelPdf&token=<?php echo $token?>&shipping_id=<?php echo $shipping_id;?>" class="btn button-default" style="border: solid 1px #ccc;background:#fff">
          <i class="fa fa-download" aria-hidden="true"></i>
        </a>

    <?php } ?>    

       </td>
    </tr>
    <?php } ?>
  </table>
<?php } ?>


<!-- Cancelled Shipments -->
<?php if(!empty($cancel_reverse_shipments)){ ?>
<table style="width:100%;" border="0" >
  <tr>
    <td>
      <h4><b>Cancelled Reverse Shipment(s)</b></h4>
    </td>
  </tr>
</table><br>
  
  <table class="table table-bordered">
    <tr>
      <th>S. No.</th>
      <th>Master ReturnId(s)</th>
      <th>Shipper</th>
      <th>Shipping Method</th>
      <th>Tracking No.</th>
      <th>Token No.</th>
      <th>Date</th>
      <th>Qty</th>
      <th>Value</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
    <?php 
      $row_count = 0;
      foreach($cancel_reverse_shipments as $shipping_id => $reverse_shipment){
        $row_count++;
        $is_self_courier = false;
        if( !empty($reverse_shipment['shipping_method'])
            && 
            $reverse_shipment['shipping_method'] == 'self_courier'
          ) {
          $is_self_courier = true;
        }

    ?>
    <tr class="products" style="background-color:#ff0000; color:#ffffff" >
      <td><?php echo $row_count;?></td>
      <td>
        <?php echo isset($reverse_shipment['master_return_ids'])?$reverse_shipment['master_return_ids']:'0'; ?>
      </td>
      <td>
        <?php echo isset($reverse_shipment['courier_company'])?$reverse_shipment['courier_company']:''; ?>
      </td>
      <td>
        <?php $method =  isset($reverse_shipment['shipping_method'])
                  ? ucwords(str_replace('_',' ',$reverse_shipment['shipping_method']))
                  :''; 
              if($is_self_courier) {
                echo '<b>'.$method.'</b>';
              }else{
                echo $method; 
            }    
          ?>
      </td>
      <td>
        <?php echo isset($reverse_shipment['tracking_no'])?$reverse_shipment['tracking_no']:''; ?>
      </td>
      <td>
        <?php 
        if(!empty($reverse_shipment['request_param']))
        {
          $request_reponse_data = unserialize($reverse_shipment['request_param']);
          if(!empty($request_reponse_data[1]->GenerateWayBillResult->TokenNumber) )
          {
            echo $request_reponse_data[1]->GenerateWayBillResult->TokenNumber;  
          }
         }
        ?>
      </td>
      <td>
        <?php echo isset($reverse_shipment['date_cancelled'])?$reverse_shipment['date_cancelled']:''; ?>
      </td>
      <td>
        <?php echo isset($reverse_shipment['qty'])?$reverse_shipment['qty']:'0'; ?>
      </td>
      <td>
        <?php echo isset($reverse_shipment['value'])?$reverse_shipment['value']:'0'; ?>
      </td>
      <td>
        <?php echo !empty($reverse_shipment['equivalent_status'])?$reverse_shipment['equivalent_status']:'';?> 
        <?php echo !empty($reverse_shipment['shipment_status'])?'<br>('.$reverse_shipment['shipment_status'].')':''; ?>
      </td>
      <td>
        <a class="btn button-default"
          id="tracking_button_<?php echo $shipping_id;?>"
          style="border: solid 1px #ccc;background:#fff"
          href="javascript:void(0)"
          data-opid="shipping_id"
          onclick="getReverseShipmentHistory(<?php echo $shipping_id; ?>)"
          toggle="tooltip"
          title="View History"
          data-original-title="View History">
          <i class="fa fa-eye"></i>
        </a>
      </td>
   
      <input type="hidden" id="vendor_<?php echo $shipping_id;?>" value="<?php echo $reverse_shipment['warehouse_name'];?>">
        <input type="hidden" id="vendor_city_<?php echo $shipping_id;?>" value="<?php echo $reverse_shipment['city'];?>">
        <input type="hidden" id="tracking_no_<?php echo $shipping_id;?>" value="<?php echo $reverse_shipment['tracking_no'];?>">
        <input type="hidden" id="courier_company_<?php echo $shipping_id;?>" value="<?php echo $reverse_shipment['courier_company'];?>">
    </tr>
    <?php } ?>
  </table><br><br>
<?php } ?>



<!-- Cancelled Shipments -->


</div>

<!----- Show History for reverse shipment pop-up----- ------>
<div class="modal fade" id="shipment_histopry_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="shipmentHistoryTab">Shipment history -  </h4>
      </div>
      <div class="modal-body" style="overflow-y:auto">
        <div class="warehouse-detail"> WareHouse: </div>
        <table id="shipmentHistoryBody" class="table table-striped table-bordered">
        </table>
        <div class="no-shipment-history">
          <p class="alert alert-danger">No History find for this Shipment<p>
          <p><b>Note:</b> For NuvoEx Shipments(AddedDate is before 30 Oct 2017), Kindly track status with following URL:<br>
          <b><i class="nuvo-track-url"></i></b>
          </p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

