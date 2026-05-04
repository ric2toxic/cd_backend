<?php
if($invoice_no == '' || $invoice_no == '0') { // checking for invoice number for the order?>

<div class="row" >
    
    <div>&nbsp;</div>
    
    <div class="col-sm-12" style="text-align: center; color:red">
        <h2> InvoiceNo not generated for this order!! </h2>
    </div>
    
    <div>&nbsp;</div>
    
</div>

<?php } else  { ?>

<div class="row">
  <div class="col-sm-2">
    <ul class="nav nav-pills nav-stacked">

 <?php 
 $order_status_check = 0;
 
    if( isset($order_status_id) 
        && 
        in_array($order_status_id, array(5, 13, 14, 15))
        && 
        !in_array($this->user->getId(), explode(',',ADMIN_IDS))
     ){
     
     $order_status_check = 1;
 ?>       
        
 <li class="active"><a href="#form-non-gati" data-toggle="tab"><?php echo $text_courier_no_docket;?></a></li>
 
 
<?php } else { ?> 
 
        <?php if($isDotZot){ ?>    
            <li  class="active"><a href="#form-dotzot" data-toggle="tab"><?php echo $text_courier_dotzot;?></a></li>      
            <li><a href="#form-gati" data-toggle="tab"><?php echo $text_courier_gati;?></a></li>
        <?php } else { ?> 
            <li class="active"><a href="#form-gati" data-toggle="tab"><?php echo $text_courier_gati;?></a></li>
            <li><a href="#form-dotzot" data-toggle="tab"><?php echo $text_courier_dotzot;?></a></li>
        <?php } ?>

            <li><a href="#form-bluedart" data-toggle="tab"><?php echo $text_courier_blue_dart;?></a></li> 
            <li><a href="#form-connect-india" data-toggle="tab"><?php echo $text_courier_connect_india;?></a></li>
            <li><a href="#form-fedex" data-toggle="tab"><?php echo $text_courier_fedex;?></a></li> 
            <li><a href="#form-delhivery" data-toggle="tab"><?php echo $text_courier_delhivery;?></a></li> 
            <li><a href="#form-non-gati" data-toggle="tab"><?php echo $text_courier_no_docket;?></a></li>
<?php } ?>  

    </ul>
  </div>

  <div class="col-sm-10">
    <div class="tab-content">
      <div class="tab-pane <?php echo (!$isDotZot && !$order_status_check) ? 'active' : '';?>" id="form-gati">
        <div class="form-horizontal">
          
          <div class="form-group required">
            <label for="actualweight" class="col-sm-2 control-label"><?php echo $text_courier_actual_weight; ?></label>
            <div class="col-sm-10">
              <input type="text" name= "gati_weight" id="gati_weight" class="form-control"/>
            </div>
          </div>
          <div class="form-group required">
            <label for="actualweight" class="col-sm-2 control-label"><?php echo $text_courier_service_type; ?></label>
            <div class="col-sm-10">
              <select name="gati_service_type" id="gati_service_type" class="form-control" disabled>
                    <option value="">----Select Type----</option>
                    <option value="gati_kwe">Gati KWE</option>
                    <option value="gati_ltd" selected>Gati Ltd</option>
              </select>
            </div>
          </div>
            
          <div class="form-group required">
            <label for="docketnumber" class="col-sm-2 control-label"><?php echo $text_courier_docket_number; ?></label>
            <div class="col-sm-10">
              <input type="text" name="gati_docket" id="gati_docket" class="form-control" value="<?php echo $docket; ?>" readonly/>
            </div>
          </div>  
          <div class="form-group required">
            <label for="dimensions" class="col-sm-2 control-label"><?php echo $text_courier_dimensions?> (cm)</label>
             <div class="col-sm-3">
                <input type="number" min="0" name= "gati_breadth" class="form-control" placeholder="Breadth"/>
            </div>
            <div class="col-sm-3">
                <input type="number" min="0" name= "gati_length" id="length" class="form-control" placeholder="Length"/>
            </div>
            <div class="col-sm-3">
                <input type="number" min="0" name= "gati_height" class="form-control" placeholder="Height"/>
            </div>
           </div>
            <div class="form-group required">
                <label for="noOfPkg" class="col-sm-2 control-label"><?php echo $text_number_of_packages; ?></label>
                <div class="col-sm-10">
                    <input value="" type="number" step="0.01" name= "gati_number_of_packages" class="form-control" placeholder="No.of Pkg"/>
                </div>
            </div> 
            
            <?php if(ceil($order_total) >= 50000) { ?>    
        
                <div class="form-group required">
                   <div class="col-sm-12" style="text-align: center; font-weight: bold">
                       <?php echo $text_courier_order_total?> :: <?php echo $order_total;?> <br>
                       <a href="javascript:void(0)" class="eWayBill" data-id="GatieWayBillForm"><?php echo $text_ewaybill_alert?></a>
                   </div>
               </div>  

               <div id="GatieWayBillForm" style="display:none">

                   <div class="form-group">
                       <label for="gati_ewaybill_number" class="col-sm-3 control-label"><?php echo $text_ewaybill_number; ?></label>
                       <div class="col-sm-9">
                           <input value="" type="number" step="0.01" name= "gati_ewaybill_number" class="form-control" placeholder="EWay Bill Number"/>
                       </div>
                   </div> 

                   <div class="form-group">
                       <label for="gati_ewaybill_exp_date" class="col-sm-3 control-label"><?php echo $text_ewaybill_expiry_date; ?></label>
                       <div class="col-sm-9">
                           <input type="text" value="<?php echo date('Y-m-d')?>" name="gati_ewaybill_exp_date" value="" placeholder="EWayBill Expiry Date" id="input-eway-bill-expiry-date" class="form-control pickup_time" />
                       </div>
                   </div>

               </div>

           <?php } ?> 

        <div class="text-right">
            <div id="download_shipment" style="margin-bottom:10px;text-align:center;"></div>
            <div class="pull-right">
              <button class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>" type="submit" id="button-gati"><i class="fa fa-plus-circle"></i>Submit</button>
              <div class="shipping_label_pdf pull-right" id="shipping_label_pdf" style="margin:0px 5px;"></div>
            </div>
          </div>
        </div>
      </div>
        
    <!--DotZot (MSA) -->
    <div class="tab-pane <?php echo ($isDotZot && !$order_status_check) ? 'active' : '';?>" id="form-dotzot">
        <div class="form-horizontal">
            <div class="form-group">
            <label for="actualweight" class="col-sm-3 control-label">Service Type</label>
            <div class="col-sm-9">
                <label><input type="radio" name="dotzot_service_type" value="Economy">&nbsp;Surface (Economy)</label>
                <label><input type="radio" name="dotzot_service_type" value="Express" checked>&nbsp;Apex (Express)</label>
            </div>
          </div>
          <div class="form-group required">
            <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_number_of_packages; ?></label>
            <div class="col-sm-9">
                <input value="" type="number" step="0.01" name= "dotzot_number_of_packages" class="form-control" placeholder="No.of Pkg"/>
            </div>
          </div>
          <div class="form-group required">
            <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_courier_actual_weight; ?> (Kg)</label>
            <div class="col-sm-9">
                <input type="text" name= "dotzot_weight" class="form-control"/>
            </div>
          </div> 
          <div class="text-right">
            <div id="download_shipment" style="margin-bottom:10px;text-align:center;"></div>
            <div class="pull-right">
              <button class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>" type="submit" id="button-dotzot"><i class="fa fa-plus-circle"></i>Submit</button>
              <div class="shipping_label_pdf pull-right" id="shipping_label_pdf_dotzot" style="margin:0px 5px;"></div>
            </div>
          </div>
        </div>
      </div>
    <!--BlueDart (MSA) -->
    <div class="tab-pane" id="form-bluedart">
        <div class="form-horizontal">
            
           <div class="form-group">
            <label for="actualweight" class="col-sm-3 control-label">Shipping method</label>
            <div class="col-sm-9">
                <input checked type="radio" name="bluedart_shipping_method" value="<?php echo $shipping_method?>">&nbsp;<?php echo $shipping_method?>
            </div>
          </div>
            
          <div class="form-group">
            <label for="actualweight" class="col-sm-3 control-label">Shipping code</label>
            <div class="col-sm-9">
                <input type="radio" name="bluedart_shipping_code" value="Surface" <?php echo ($shipping_code_type == 'surface') ? 'checked' : ''?>>&nbsp;Surface <span id="bluedart-surface-customer-code"></span>
            </div>
          </div>  
            
           <div class="form-group required">
            <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_courier_actual_weight; ?> (Kg)</label>
            <div class="col-sm-9">
                <input type="number" step="0.01" name= "bluedart_weight" class="form-control" placeholder="Weight"/>
            </div>
          </div> 
          <div class="form-group required">
            <label for="length" class="col-sm-3 control-label"><?php echo $text_courier_dimensions?> (cm)</label>
             <div class="col-sm-3">
                <input type="number" min="0" name= "bluedart_breadth" class="form-control" placeholder="Breadth"/>
            </div>
            <div class="col-sm-3">
                <input type="number" min="0" name= "bluedart_length" id="length" class="form-control" placeholder="Length"/>
            </div>
            <div class="col-sm-3">
                <input type="number" min="0" name= "bluedart_height" class="form-control" placeholder="Height"/>
            </div>
           </div>
            <div class="form-group required">
                <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_number_of_packages; ?></label>
                <div class="col-sm-9">
                    <input value="" type="number" step="0.01" name= "bluedart_number_of_packages" class="form-control" placeholder="No.of Pkg"/>
                </div>
            </div>
            <div class="form-group required">
                <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_courier_declared_amount; ?></label>
                <div class="col-sm-9">
                    <input value="<?php echo isset($order_total) ? $order_total : '0';?>" type="number" step="0.01" name= "bluedart_declared_value" class="form-control" placeholder="Declared Amount"/>
                </div>
            </div>
            <div class="form-group required">
                <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_courier_collectable_amount; ?></label>
                <div class="col-sm-9">
                    <input value="<?php echo isset($net_payble) ? $net_payble : '0';?>" type="number" step="0.01" name= "bluedart_collectable_amount" class="form-control" placeholder="Collectable Amount" disabled/>
                </div>
            </div> 
            <div class="form-group required">
              <label for="pickupDate" class="col-sm-3 control-label"><?php echo $text_courier_pickup_date_time?></label>
              <div class="col-sm-4">
                  <input type="text" value="<?php echo date('Y-m-d h:i A')?>" name="bluedart_pickup_date" value="" placeholder="Pickup Time" id="input-pickup_time" class="form-control pickup_time" />
              </div>
            </div>
            
            <?php if(ceil($order_total) >= 50000) { ?>    
        
                <div class="form-group required">
                   <div class="col-sm-12" style="text-align: center; font-weight: bold">
                       <?php echo $text_courier_order_total?> :: <?php echo $order_total;?> <br>
                       <a href="javascript:void(0)" class="eWayBill" data-id="BluedarteWayBillForm"><?php echo $text_ewaybill_alert?></a>
                   </div>
               </div>  

               <div id="BluedarteWayBillForm" style="display:none">

                   <div class="form-group">
                       <label for="bluedart_ewaybill_number" class="col-sm-3 control-label"><?php echo $text_ewaybill_number; ?></label>
                       <div class="col-sm-9">
                           <input value="" type="number" step="0.01" name= "bluedart_ewaybill_number" class="form-control" placeholder="EWay Bill Number"/>
                       </div>
                   </div> 

                   <div class="form-group">
                       <label for="bluedart_ewaybill_exp_date" class="col-sm-3 control-label"><?php echo $text_ewaybill_expiry_date; ?></label>
                       <div class="col-sm-9">
                           <input type="text" value="<?php echo date('Y-m-d')?>" name="bluedart_ewaybill_exp_date" value="" placeholder="EWayBill Expiry Date" id="input-eway-bill-expiry-date" class="form-control pickup_time" />
                       </div>
                   </div>

               </div>

           <?php } ?> 
            
          <div class="text-right">
            <div id="download_shipment" style="margin-bottom:10px;text-align:center;"></div>
            <div class="pull-right">
              <button class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>" type="submit" id="button-bluedart"><i class="fa fa-plus-circle"></i>Submit</button>
              <div class="shipping_label_pdf pull-right" id="shipping_label_pdf_bluedart" style="margin:0px 5px;"></div>
            </div>
          </div>
        </div>
      </div>
    
    <!--Connect-India (MSA) -->
    <div class="tab-pane" id="form-connect-india">
        
        <?php if(!$isServicablePostcode) { ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> Connect India : No delivery service available for shipping postcode :: <?php echo $shipping_postcode?>
                <button type="button" class="close" data-dismiss="alert">x</button>
            </div>
        <?php } ?>
        
        <div class="form-horizontal">
          <div class="form-group required">
            <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_courier_actual_weight; ?> (Kg)</label>
            <div class="col-sm-9">
                <input type="number" step="0.01" name= "connect_india_weight" class="form-control" placeholder="Weight"/>
            </div>
          </div> 
          <div class="form-group required">
            <label for="length" class="col-sm-3 control-label"><?php echo $text_courier_dimensions?> (cm)</label>
             <div class="col-sm-3">
                <input type="number" min="0" name= "connect_india_width" class="form-control" placeholder="Width"/>
            </div>
            <div class="col-sm-3">
                <input type="number" min="0" name= "connect_india_length" id="length" class="form-control" placeholder="Length"/>
            </div>
            <div class="col-sm-3">
                <input type="number" min="0" name= "connect_india_height" class="form-control" placeholder="Height"/>
            </div>
           </div>
            <div class="form-group required">
                <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_courier_declared_amount; ?></label>
                <div class="col-sm-9">
                    <input value="<?php echo isset($order_total) ? $order_total : '0';?>" type="number" step="0.01" name= "connect_india_declared_value" class="form-control" placeholder="Declared Amount"/>
                </div>
            </div>
            <div class="form-group required">
                <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_courier_collectable_amount; ?></label>
                <div class="col-sm-9">
                    <input value="<?php echo isset($net_payble) ? $net_payble : '0';?>" type="number" step="0.01" name= "connect_india_collectable_amount" class="form-control" placeholder="Collectable Amount" disabled />
                </div>
            </div> 
            <div class="form-group required">
              <label for="pickupDate" class="col-sm-3 control-label"><?php echo $text_courier_pickup_date_time?></label>
              <div class="col-sm-4">
                  <input type="text" value="<?php echo date('Y-m-d h:i A')?>" name="connect_india_pickup_date" value="" placeholder="Pickup Time" id="connect_india_pickup_date" class="form-control pickup_time" />
              </div>
            </div>
          <div class="text-right">
            <div id="download_shipment" style="margin-bottom:10px;text-align:center;"></div>
            <div class="pull-right">
              <button class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>" type="submit" id="button-connect-india"><i class="fa fa-plus-circle"></i>Submit</button>
              <div class="shipping_label_pdf pull-right" id="shipping_label_pdf_connect_india" style="margin:0px 5px;"></div>
            </div>
          </div>
        </div>
      </div>
    
     <!--Courier By Fedex -->
      <div class="tab-pane " id="form-fedex" data-default-city="jaipur">
        <div class="form-horizontal">
          <div class="form-group required">
            <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_courier_account_number; ?></label>
            <div class="col-sm-9">
                <label><input class="fedex-account-codes" id="fedex_account_code_1" checked type="radio" name="fedex_account_code" value="<?php echo FEDEX_ACCOUNT_1?>">&nbsp;<b id="fedex_account_code_1_txt"><?php echo FEDEX_ACCOUNT_1?></b></label>
                &nbsp;&nbsp;&nbsp;
                <label><input class="fedex-account-codes" id="fedex_account_code_2"  type="radio" name="fedex_account_code" value="<?php echo FEDEX_ACCOUNT_2?>">&nbsp;<b id="fedex_account_code_2_txt"><?php echo FEDEX_ACCOUNT_2?></b></label>
            </div>
          </div> 
            <div class="form-group required">
            <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_courier_service_type; ?></label>
            <div class="col-sm-9">
                <select name="fedex_service_type" id="fedex_service_type" class="form-control">
                    <option value="">--Select Service Type--</option>
                    <?php if(!empty($fedex_service_types)) 
                        { 
                            foreach($fedex_service_types as $key => $value)
                            {
                                echo '<option value="'.$value.'">'.ucwords(strtolower(str_replace('_',' ',$value))).'</option>';
                            }
                        } 
                    ?>
                </select>
            </div>
          </div>   
          <div class="form-group required">
            <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_courier_actual_weight; ?> (Kg)</label>
            <div class="col-sm-9">
                <input type="number" step="0.01" name= "fedex_weight" class="form-control" placeholder="Weight"/>
            </div>
          </div> 
          <div class="form-group required">
            <label for="length" class="col-sm-3 control-label"><?php echo $text_courier_dimensions?> (cm)</label>
             <div class="col-sm-3">
                <input type="number" min="0" name= "fedex_width" class="form-control" placeholder="Width"/>
            </div>
            <div class="col-sm-3">
                <input type="number" min="0" name= "fedex_length" id="length" class="form-control" placeholder="Length"/>
            </div>
            <div class="col-sm-3">
                <input type="number" min="0" name= "fedex_height" class="form-control" placeholder="Height"/>
            </div>
           </div>
           <!--<div class="form-group required">
                <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_number_of_packages; ?></label>
                <div class="col-sm-9">
                    <input value="1" disabled type="number" step="0.01" name= "fedex_number_of_packages" id="fedex_number_of_packages" class="form-control" placeholder="No.of Pkg"/>
                </div>
            </div> -->
            <div class="form-group required">
                <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_courier_declared_amount; ?></label>
                <div class="col-sm-9">
                    <input value="<?php echo isset($order_total) ? $order_total : '0';?>" type="number" step="0.01" name= "fedex_declared_value" class="form-control" placeholder="Declared Amount"/>
                </div>
            </div>
            <div class="form-group required">
                <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_courier_collectable_amount; ?></label>
                <div class="col-sm-9">
                    <input value="<?php echo isset($net_payble) ? $net_payble : '0';?>" type="number" step="0.01" name= "fedex_collectable_amount" class="form-control" placeholder="Collectable Amount" disabled/>
                </div>
            </div> 
            <div class="form-group required">
              <label for="pickupDate" class="col-sm-3 control-label"><?php echo $text_courier_pickup_date_time?></label>
              <div class="col-sm-4">
                  <input type="text" value="<?php echo date('Y-m-d h:i A')?>" name="fedex_pickup_date" value="" placeholder="Pickup Time" id="fedex_pickup_date" class="form-control pickup_time" />
              </div>
            </div>
          <div class="text-right">
            <div id="download_shipment" style="margin-bottom:10px;text-align:center;"></div>
            <div class="pull-right">
              <button class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>" type="submit" id="button-fedex"><i class="fa fa-plus-circle"></i>Submit</button>
              <div class="shipping_label_pdf pull-right" id="shipping_label_pdf_fedex" style="margin:0px 5px;"></div>
            </div>
          </div>
        </div>
      </div>
   
    <!--delhivery Delivery (MSA) -->
    <div class="tab-pane" id="form-delhivery">
        <div class="form-horizontal">
            <div class="form-group">
            <label for="actualweight" class="col-sm-3 control-label">Service Type</label>
            <div class="col-sm-9">
                <label><input type="radio" checked="checked" name="delhivery_service_type" value="Economy">&nbsp;Surface (Economy)</label>
            </div>
          </div>
           <div class="form-group required">
            <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_courier_actual_weight; ?> (Kg)</label>
            <div class="col-sm-9">
                <input type="text" name= "delhivery_weight" class="form-control" placeholder="Weight (Kg)" />
            </div>
          </div> 
          <!--<div class="form-group required">
            <label for="actualweight" class="col-sm-3 control-label"><?php echo $entry_shipment_width; ?> (cm)</label>
            <div class="col-sm-9">
                <input type="text" name= "delhivery_width" class="form-control" placeholder="Shipment width" />
            </div>
          </div>  -->
          <div class="form-group required">
            <label for="actualweight" class="col-sm-3 control-label"><?php echo $text_number_of_packages; ?></label>
            <div class="col-sm-9">
                <input value="" type="number" step="0.01" name= "delhivery_number_of_packages" class="form-control" placeholder="No.of Pkg"/>
            </div>
          </div>
          <div class="text-right">
            <div id="download_shipment" style="margin-bottom:10px;text-align:center;"></div>
            <div class="pull-right">
              <button class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>" type="submit" id="button-delhivery"><i class="fa fa-plus-circle"></i>Submit</button>
              <div class="shipping_label_pdf pull-right" id="shipping_label_pdf_delhivery" style="margin:0px 5px;"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="tab-pane <?php echo ($order_status_check) ? 'active' : '';?>" id="form-non-gati" >
        <div class="form-horizontal">
          <div class="form-group" id="courier" >
            <label class="col-sm-2 control-label" for="input-notify"><?php echo $text_courier_label?></label>
            <div class="col-sm-10">
              <select name="courier" id="input-courier" class="form-control">
                <option value="">----Select Courier----</option>
                <?php foreach ($courier_partners as $courier_partners) { ?>
                <option value="<?php echo $courier_partners['courier_name']; ?>"><?php echo $courier_partners['courier_name']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="docketlabel" class="col-sm-2 control-label"><?php echo $text_courier_docket_number; ?></label>
            <div class="col-sm-10">
              <input type="text" name="docketno" class="form-control" value="" />
            </div>
          </div>
          <div class="form-group">
            <label for="actualweight" class="col-sm-2 control-label"><?php echo $text_courier_actual_weight; ?></label>
            <div class="col-sm-10">      

              <input type="text" name= "actual_weight" class="form-control"/>
            </div>
          </div>
          <div class="form-group">
            <label for="border_info_tab_courier_partnersarcode" class="col-sm-2 control-label"><?php echo $text_courier_barcode; ?></label>
            <div class="col-sm-10">
              <input type="checkbox" name= "generate_borcode" class="form-control" value="0" />
            </div>
          </div>
          <div class="text-right">
            <div id="download_shipment-non-gati" style="margin-bottom:10px;text-align:center;"></div>
            <div class="pull-right">
              <button class="btn btn-warning" data-loading-text="<?php echo $text_loading; ?>" type="submit" id="button-non-gati"><i class="fa fa-barcode"></i> Generate </button>
              <div class="shipping_label_pdf pull-right" id="non-gati-shipping_label_pdf" style="margin:0px 5px;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-12 shipment_label ">
    <fieldset>
      <legend><?php echo $text_company_address; ?></legend>
      <div class="form-group">
        <div class="col-sm-4">
          <input type="text" name="filter_warehouse_name" class="form-control" placeholder="<?php echo $entry_search_warehouse; ?>" value="<?php //echo $search_warehouse; ?>" />
        </div>
        <div class="col-sm-4">
          <input type="text" name="filter_city" class="form-control" placeholder="<?php echo $entry_search_city; ?>" value="<?php //echo $search_city; ?>"/>
        </div>
        <div class="col-sm-3">
          <button type="button" name="search" class="btn btn-primary" id="button-search"><?php echo $button_search; ?></button>
        </div>
      </div>
      <div class="address_form_list">
        <?php if(isset($getAddressOfWarehouse)){
                  foreach($getAddressOfWarehouse as $address_list){
                    $address_1 = '';
                    $address_2 = '';
                    if(!empty($address_list['address_1'])){
                      $address_1 = $address_list['address_1'] . '<br>';
                    }
                    if(!empty($address_list['address_2'])){
                      $address_2 = $address_list['address_2'] . '<br>';
                    }
            $warehouse_city = strtolower(str_replace(' ','_',$address_list['city']));
          if(array_key_exists($warehouse_city,FEDEX_ACCESS)) {
            $fedex_account_numbers = array_keys(FEDEX_ACCESS[$warehouse_city]);      
          }else{
            $fedex_account_numbers = array_keys(FEDEX_ACCESS['jaipur']);   
          }
            
        ?>
            <div class="col-sm-3">
              <div class="addresses_list warehouse_address_height_set" id="warehouse_id_<?php echo $address_list['warehouse_id'];?>" data-address-id="<?php echo $address_list['warehouse_id'];?>">
                  <?php echo $address_list['warehouse_name'];?><br>
                  <?php echo $address_1; ?>
                  <?php echo $address_2; ?>
                  <?php echo $address_list['city'];?> - 
                  <?php echo $address_list['postcode'];?><br>
                  <?php echo $address_list['zone_name'];?> - 
                  <?php echo $address_list['country_name'];?><br>
                  <?php echo $address_list['telephone'];?>
              </div>
              <div class="address_select">
                <input type="radio" id="input-address-<?php echo $address_list['warehouse_id'];?>" class="" name="select_warehouse_id" value="<?php echo $address_list['warehouse_id'];?>" />
                <label class="" for="input-address-<?php echo $address_list['warehouse_id'];?>" ><?php echo $address_list['zone_name'];?> - <?php echo $address_list['country_name'];?> </label>
                <input type="hidden" id="input-gati-vendor-<?php echo $address_list['warehouse_id'];?>" class="" name="gati_vendor_code" value="<?php echo $address_list['gati_vendor_code'];?>" />
                <input type="hidden" id="input-bluedart-vendor-code-<?php echo $address_list['warehouse_id'];?>" class="" name="bluedart_vendor_code" value="<?php echo $address_list['bluedart_vendor_code'];?>" />
                <input type="hidden" id="input-bluedart-surface-customer-code-<?php echo $address_list['warehouse_id'];?>" class="" name="bluedart_surface_customer_code" value="<?php echo $address_list['bluedart_surface_customer_code'];?>" />
                <input type="hidden" id="input-bluedart-apex-customer-code-<?php echo $address_list['warehouse_id'];?>" class="" name="bluedart_apex_customer_code" value="<?php echo $address_list['bluedart_apex_customer_code'];?>" />
              <!--Fedex city wise account numbers -->  
                <input type="hidden" id="input-feex-account-number1-<?php echo $address_list['warehouse_id'];?>" class="" name="fedex_account_number1" value="<?php echo $fedex_account_numbers[0];?>" />
                <input type="hidden" id="input-feex-account-number2-<?php echo $address_list['warehouse_id'];?>" class="" name="fedex_account_number2" value="<?php echo $fedex_account_numbers[1];?>" />
                </div>
            </div>
        <?php } } ?>
      </div>
    </fieldset>
  </div>
</div>

<?php } // checking invoice number ?>

<script type="text/javascript">

    function selectMaunalyShippingMode() {
       $("#shipping_mode").val("manual");
    }
    function changeShippingService(){
      $("#shipping_service").removeAttr('disabled');
    }   
    function  getWareHouse(){
        if(!$('input[name=\'select_warehouse_id\']').is(":checked")){
          alert('Please select warehouse address !');
          return false;
        }else{
         var select_warehouse_id = $('input[name=\'select_warehouse_id\']:checked').val();
         return select_warehouse_id;
        }
    }
    function getGatiDocket()
    {
        var gati_service = $('#gati_service_type option:selected').val();
        
        $.ajax({
                url: 'index.php?route=sale/order/getGatiDocket&token=<?php echo $token; ?>&gati_service_type='+gati_service,
                type: 'get',
                success: function(response) {
                    $('#gati_docket').val(response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                  var string = xhr.responseText.replace(/<b>/g,'');
                      string = string.replace(/<\/b>/g,'');
                      string = string.replace(/\\/g,'');
                      alert("Error occure during API processing ::\n"+string);
                }
              });
    }
 
 
/*ready-state*/
$(document).ready(function(){

  /*DotZot click event*/
    $('#button-dotzot').on('click',function(){
        
        if($('input[name=\'dotzot_service_type\']').val() == '') {
             alert('Please select service type!');
             return false;
        }
        
        if($('input[name=\'dotzot_number_of_packages\']').val() == '') {
             alert('Please enter number of packages!');
             return false;
        }
        
        if($('input[name=\'dotzot_weight\']').val() == '') {
             alert('Please enter weight!');
             return false;
        }
        
        var select_warehouse_id = getWareHouse();
        if ( !select_warehouse_id ){
            return false;
        }
        
        var no_of_pkg = $('input[name=\'dotzot_number_of_packages\']').val();
                
        $.ajax({
            url:'index.php?route=sale/order/courierApi&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>',
            dataType:'json',
            data:'courier=dotzot' + 
                    '&weight='+ ($('input[name=\'dotzot_weight\']').val()) +
                    '&service_type='+ $('input[name=\'dotzot_service_type\']:checked').val() +
                    '&no_of_pkg='+ ($('input[name=\'dotzot_number_of_packages\']').val()) +
                    '&warehouse_id=' + select_warehouse_id + 
                    '&vendor_code=' + ($('#input-gati-vendor-'+select_warehouse_id).val()),
            beforeSend: function() {
                $('#button-dotzot').button('loading');
            },
            complete: function() {
                $('#button-dotzot').button('reset');
            },
            success: function(json) {
                
                if (json['error']) { 
                     $('#form-gati').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }
                
                if (json['success']) {
                    $('#form-gati').before('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    
                    $('#shipping_label_pdf_dotzot').html('<a href="index.php?route=sale/order/shippingLabelPdf&token=<?php echo $token;?>&shipping_label_id='+json['shipping_label_id']+'&no_of_pkg='+no_of_pkg+'" class="btn btn-warning" data-toggle="tooltip" data-original-title="<?php echo 'Generate Shipping Label'; ?>"><i class="fa fa-barcode"></i></a>');
                    $('#button-dotzot').remove();
                    $('#shipping_label_pdf_dotzot').show();
                    
                    var docket = json['docket'] ;
                     
                        $.ajax({
                          url: '<?php echo $https_catalog; ?>index.php?route=api/order/history&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&order_status_id=14',
                          type: 'post',
                          dataType: 'json',
                          data: '&notify=1' + 
                                '&notify_sms=1'  + 
                                '&comment=' + encodeURIComponent('Shipment sent by DotZot with Tracking No: '+ docket )+ 
                                '&shippingco=DotZot' + 
                                '&tracking=' + docket ,
                          success: function(json) {
                              
                            $('.alert').remove();

                            if (json['error']) {
                              $('#form-gati').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                            }

                            if (json['success']) {

                              $('#form-gati').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                              
                            }
                          },
                          error: function(xhr, ajaxOptions, thrownError) {
                              var string = xhr.responseText.replace(/<b>/g,'');
                                  string = string.replace(/<\/b>/g,'');
                                  string = string.replace(/\\/g,'');
                                  alert("Error occure during API processing ::\n"+string);
                          }
                        });
                    } 
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    var string = xhr.responseText.replace(/<b>/g,'');
                        string = string.replace(/<\/b>/g,'');
                        string = string.replace(/\\/g,'');
                        alert("Error occure during API processing ::\n"+string);
                }
            
        })

    }) 
  /*DotZot click event*/
  
  /*delhivery Delivery click event*/
    $('#button-delhivery').on('click',function(){
        
        if($('input[name=\'delhivery_service_type\']').val() == '') {
             alert('Please select service type!');
             return false;
        }
        
        if($('input[name=\'delhivery_weight\']').val().trim() == '') {
             alert('Please enter weight!');
             return false;
        }

        if($('input[name=\'delhivery_number_of_packages\']').val().trim() == '') {
             alert('Please enter number of packages!');
             return false;
        }
        if($('input[name=\'delhivery_number_of_packages\']').val().trim() <= 0) {
             alert('Please enter valid number of packages!');
             return false;
        }
        
        var select_warehouse_id = getWareHouse();
        if ( !select_warehouse_id ){
            return false;
        }
        
        var no_of_pkg = $('input[name=\'delhivery_number_of_packages\']').val();

        $.ajax({
            url:'index.php?route=sale/order/courierApi&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>',
            dataType:'json',
            data:'courier=Delhivery' + 
                    '&weight='+ ($('input[name=\'delhivery_weight\']').val()) +
                    '&width='+ ($('input[name=\'delhivery_width\']').val()) +
                    '&service_type='+ $('input[name=\'delhivery_service_type\']:checked').val() +
                    '&no_of_pkg='+ ($('input[name=\'delhivery_number_of_packages\']').val()) +
                    '&warehouse_id=' + select_warehouse_id + 
                    '&vendor_code=' + ($('#input-gati-vendor-'+select_warehouse_id).val()),
            beforeSend: function() {
                $('#button-delhivery').button('loading');
            },
            complete: function() {
                $('#button-delhivery').button('reset');
            },
            success: function(json) {
                
                if (json['error']) { 
                     $('#form-delhivery').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }
                
                if (json['success']) {
                    $('#form-delhivery').before('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    
                    $('#shipping_label_pdf_delhivery').html('<a href="index.php?route=sale/order/shippingLabelPdf&token=<?php echo $token;?>&shipping_label_id='+json['shipping_label_id']+'&no_of_pkg='+no_of_pkg+'" class="btn btn-warning" data-toggle="tooltip" data-original-title="<?php echo 'Generate Shipping Label'; ?>"><i class="fa fa-barcode"></i></a>');
                    $('#button-delhivery').remove();
                    $('#shipping_label_pdf_delhivery').show();
                    
                    var docket = json['docket'] ;
                     
                        $.ajax({
                          url: '<?php echo $https_catalog; ?>index.php?route=api/order/history&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&order_status_id=14',
                          type: 'post',
                          dataType: 'json',
                          data: '&notify=1' + 
                                '&notify_sms=1'  + 
                                '&comment=' + encodeURIComponent('Shipment sent by Delhivery with Tracking No: '+ docket )+ 
                                '&shippingco=Delhivery' + 
                                '&tracking=' + docket ,
                          success: function(json) {
                              
                            $('.alert').remove();

                            if (json['error']) {
                              $('#form-delhivery').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                            }

                            if (json['success']) {

                              $('#form-delhivery').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                              
                            }
                          },
                          error: function(xhr, ajaxOptions, thrownError) {
                              var string = xhr.responseText.replace(/<b>/g,'');
                                  string = string.replace(/<\/b>/g,'');
                                  string = string.replace(/\\/g,'');
                                  alert("Error occure during API processing ::\n"+string);
                          }
                        });
                    } 
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    var string = xhr.responseText.replace(/<b>/g,'');
                        string = string.replace(/<\/b>/g,'');
                        string = string.replace(/\\/g,'');
                        alert("Error occure during API processing ::\n"+string);
                }
            
        })

    }) 
  /*delhivery Delivery click event*/

  /*Gati click event*/
  
  $('.eWayBill').click(function(){
      var div_id = $(this).attr('data-id');
       $('#'+div_id).toggle();
  })
  
    $('#gati_weight').change(function(){
      
        var gati_weight = $('input[name=\'gati_weight\']').val();
        if(gati_weight >=8)
        {
            $('#gati_service_type').attr('disabled',false);
            $('#gati_service_type').val('gati_kwe').change();
        }else{
            $('#gati_service_type').val('gati_ltd').change();
        }

        getGatiDocket();

    })

    $('#gati_service_type').change(function(){
          if($(this).val() !='')
          {
              getGatiDocket($(this).val());
          }
    })
    
    $('#button-gati').on('click', function() {
      var today ='<?php echo date('d_M_y'); ?>';
      var docket = $('input[name=\'gati_docket\']').val();

      var select_warehouse_id = getWareHouse();
      if ( !select_warehouse_id ){
          return false;
      }
      
      if($('input[name=\'gati_weight\']').val() == '') {
             alert('Please enter weight!');
             return false;
        }
      if($('#gati_service_type option:selected').val() == '') {
             alert('Please select service type!');
             return false;
        }
      if(docket.trim() == '') {
             alert('Docket number can not be blank!');
             return false;
        }  
       if($('input[name=\'gati_breadth\']').val() == '') {
             alert('Please enter breadth!');
             return false;
        }
       if($('input[name=\'gati_length\']').val() == '') {
             alert('Please enter length!');
             return false;
        }
      if($('input[name=\'gati_height\']').val() == '') {
             alert('Please enter height!');
             return false;
        }
       if($('input[name=\'gati_number_of_packages\']').val() == '') {
             alert('Please enter no of package!');
             return false;
        }
     
    //EWay Bill form validations  
      var eWayBillFormStatus = $('#GatieWayBillForm').css('display');
        if(eWayBillFormStatus == 'block')
        {
          if($('input[name=\'gati_ewaybill_number\']').val() == '') {
              alert('Please enter EWay Bill number!');
              return false;
          }
          if($('input[name=\'gati_ewaybill_exp_date\']').val() == '') {
              alert('Please enter EWay Bill expiry date!');
              return false;
          }
        }
       
     var eWayBillNo = '';
     if($('input[name=\'gati_ewaybill_number\']').val() !== undefined){
         eWayBillNo = $('input[name=\'gati_ewaybill_number\']').val();
     }
     var eWayBillExpDate = '';
     if($('input[name=\'gati_ewaybill_exp_date\']').val() !== undefined){
         eWayBillExpDate = $('input[name=\'gati_ewaybill_exp_date\']').val();
     }

    var noOfPkg = $('input[name=\'gati_number_of_packages\']').val();
    
    $.ajax({
        url:'index.php?route=sale/order/courierApi&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>',
        dataType: 'json',
        data :'courier=gati' +
              '&docket='+($('input[name=\'gati_docket\']').val()) +
              '&weight='+ ($('input[name=\'gati_weight\']').val()) + 
              '&type='+ ($('#gati_service_type option:selected').val()) + 
              '&breadth='+ ($('input[name=\'gati_breadth\']').val()) + 
              '&length='+ ($('input[name=\'gati_length\']').val()) + 
              '&height='+ ($('input[name=\'gati_height\']').val()) + 
              '&noOfPkg='+ ($('input[name=\'gati_number_of_packages\']').val()) + 
              '&eWayBillNo='+ eWayBillNo + 
              '&eWayBillExpDate='+ eWayBillExpDate + 
              '&orders=' +($('input[name=\'gati_orders\']').val()) +
              '&warehouse_id=' + select_warehouse_id + 
              '&gati_vendor_code=' + ($('#input-gati-vendor-'+select_warehouse_id).val()),

        beforeSend: function() {
          $('#button-gati').button('loading');
        },
        complete: function() {
          $('#button-gati').button('reset');
        },

        success: function(json) {
            
         if (json['error']) {
            $('#form-gati').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
          }   
            
          if (json['success']) {
              
            $('.alert').remove();
              
            $('#shipping_label_pdf').html('<a href="index.php?route=sale/order/shippingLabelPdf&token=<?php echo $token;?>&shipping_label_id='+json['shipping_label_id']+'&no_of_pkg='+noOfPkg+'" class="btn btn-warning" data-toggle="tooltip" data-original-title="<?php echo 'Generate Shipping Label'; ?>"><i class="fa fa-barcode"></i></a>');
            $('#button-gati').remove();
            $('#shipping_label_pdf').show();
            $.ajax({
              url: '<?php echo $https_catalog; ?>index.php?route=api/order/history&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&order_status_id=14',
              type: 'post',
              dataType: 'json',
              data: '&notify=1' +
                    '&notify_sms=1'  +
                    '&comment=' + encodeURIComponent('Shipment sent by GATI with Tracking No: '+ docket )+
                    '&shippingco=Gati' +
                    '&tracking=' + docket ,

              beforeSend: function() {},
              complete: function() {},
              success: function(json) {
                $('.alert').remove();

                if (json['error']) {
                  $('#form-gati').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }

                if (json['success']) {

                  $('#form-gati').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

                }
              },
              error: function(xhr, ajaxOptions, thrownError) {
                var string = xhr.responseText.replace(/<b>/g,'');
                    string = string.replace(/<\/b>/g,'');
                    string = string.replace(/\\/g,'');
                    alert("Error occure during API processing ::\n"+string);
              }
            });
          }

        },
        error: function(xhr, ajaxOptions, thrownError) {
          var string = xhr.responseText.replace(/<b>/g,'');
              string = string.replace(/<\/b>/g,'');
              string = string.replace(/\\/g,'');
              alert("Error occure during API processing ::\n"+string);
        }
      });
    });
  /*Gati click event*/
  
  
  /* Blue Dart click event */
    $('#button-bluedart').click(function(){
    
        var select_warehouse_id = getWareHouse();
        if ( !select_warehouse_id ){
            return false;
        }
        if($('input[name=\'bluedart_weight\']').val() == '') {
             alert('Please enter weight!');
             return false;
        }
        if($('input[name=\'bluedart_breadth\']').val() == '') {
             alert('Please enter breadth!');
             return false;
        }
        if($('input[name=\'bluedart_length\']').val() == '') {
             alert('Please enter length!');
             return false;
        }
        if($('input[name=\'bluedart_height\']').val() == '') {
             alert('Please enter height!');
             return false;
        }
        if($('input[name=\'bluedart_number_of_packages\']').val() == '') {
             alert('Please enter No. of Packages!');
             return false;
        }
        if($('input[name=\'bluedart_declared_value\']').val() == '') {
             alert('Please enter declared amount value!');
             return false;
        }
        if($('input[name=\'bluedart_collectable_amount\']').val() == '') {
             alert('Please enter collactable amount value!');
             return false;
        }
        
        if($('input[name=\'bluedart_pickup_date\']').val() == '') {
             alert('Please enter pickup date & time!');
             return false;
        }

        var no_of_pkg = $('input[name=\'bluedart_number_of_packages\']').val();

        shipping_method =  $('input[name=\'bluedart_shipping_method\']').val();
        shipping_code   =  $('input[name=\'bluedart_shipping_code\']:checked').val();
        vendor_code     =  $('#input-bluedart-vendor-code-'+select_warehouse_id).val();
        
        //EWay Bill form validations  
        var eWayBillFormStatus = $('#BluedarteWayBillForm').css('display');
          if(eWayBillFormStatus == 'block')
          {
            if($('input[name=\'bluedart_ewaybill_number\']').val() == '') {
                alert('Please enter EWay Bill number!');
                return false;
            }
            if($('input[name=\'bluedart_ewaybill_exp_date\']').val() == '') {
                alert('Please enter EWay Bill expiry date!');
                return false;
            }
          }

       var eWayBillNo = '';
       if($('input[name=\'bluedart_ewaybill_number\']').val() !== undefined){
           eWayBillNo = $('input[name=\'bluedart_ewaybill_number\']').val();
       }
       var eWayBillExpDate = '';
       if($('input[name=\'bluedart_ewaybill_exp_date\']').val() !== undefined){
           eWayBillExpDate = $('input[name=\'bluedart_ewaybill_exp_date\']').val();
       }
        
        
        
        /*if(shipping_code == 'Surface') {
           customer_code    =  $('#input-bluedart-surface-customer-code-'+select_warehouse_id).val();
        }else{
           customer_code    =  $('#input-bluedart-apex-customer-code-'+select_warehouse_id).val();
        }*/
        
        customer_code    =  $('#input-bluedart-surface-customer-code-'+select_warehouse_id).val();
        
        shipping_html = ' <div style="line-height:25px"><strong>Shipping method - '+ shipping_method +'</strong></div>';
        shipping_html += '<div style="line-height:25px"><strong>Customer code - '+ shipping_code + ' ('+customer_code+')' + '</strong></div>';
        shipping_html += '<div style="line-height:25px"><strong>Vendor code - '+ vendor_code +'</strong></div>';
        
        $.confirm({
                icon: 'fa fa-warning',
                title: 'Confirm shipping label details',
                content: shipping_html,
                buttons: {
                    Confirm: function () {
                        
                        $.ajax({
                                url:'index.php?route=sale/order/courierApi&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>',
                                dataType:'json',
                                data:'',
                                data:'courier=bluedart' + 
                                        '&weight='+ ($('input[name=\'bluedart_weight\']').val()) +
                                        '&length='+ ($('input[name=\'bluedart_length\']').val()) +
                                        '&breadth='+ ($('input[name=\'bluedart_breadth\']').val()) +
                                        '&height='+ ($('input[name=\'bluedart_height\']').val()) +
                                        '&no_of_pkg='+ ($('input[name=\'bluedart_number_of_packages\']').val()) +
                                        '&declared_value='+ ($('input[name=\'bluedart_declared_value\']').val()) +
                                        '&collectable_amount='+ ($('input[name=\'bluedart_collectable_amount\']').val()) +
                                        '&pickup_date='+ ($('input[name=\'bluedart_pickup_date\']').val()) +
                                        '&warehouse_id=' + select_warehouse_id + 
                                        '&shipping_method='+ shipping_method +
                                        '&shipping_code='+ shipping_code +
                                        '&customer_code='+ customer_code +
                                        '&eWayBillNo='+ eWayBillNo + 
                                        '&eWayBillExpDate='+ eWayBillExpDate + 
                                        '&vendor_code=' + vendor_code ,
                                
                                beforeSend: function() {
                                    $('#button-bluedart').button('loading');
                                },
                                complete: function() {
                                    $('#button-bluedart').button('reset');
                                },
                                success: function(json) {

                                    if (json['error']) {

                                         $('#form-bluedart').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                                    }
                                    if (json['success']) {

                                        $('#form-bluedart').before('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                                        $('#shipping_label_pdf_bluedart').html('<a href="index.php?route=sale/order/shippingLabelPdf&token=<?php echo $token;?>&shipping_label_id='+json['shipping_label_id']+'&no_of_pkg='+no_of_pkg+'" class="btn btn-warning" data-toggle="tooltip" data-original-title="<?php echo 'Generate Shipping Label'; ?>"><i class="fa fa-barcode"></i></a>');
                                        $('#button-bluedart').remove();
                                        $('#shipping_label_pdf_bluedart').show();

                                        var docket = json['docket'] ;

                                            $.ajax({
                                              url: '<?php echo $https_catalog; ?>index.php?route=api/order/history&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&order_status_id=14',
                                              type: 'post',
                                              dataType: 'json',
                                              data: '&notify=1' + 
                                                    '&notify_sms=1'  + 
                                                    '&comment=' + encodeURIComponent('Shipment sent by BlueDart with Tracking No: '+ docket )+ 
                                                    '&shippingco=BlueDart' + 
                                                    '&tracking=' + docket ,
                                              success: function(json) {

                                                $('.alert').remove();

                                                if (json['error']) {
                                                  $('#form-gati').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                                                }

                                                if (json['success']) {

                                                  $('#form-gati').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

                                                }
                                              },
                                              error: function(xhr, ajaxOptions, thrownError) {
                                                var string = xhr.responseText.replace(/<b>/g,'');
                                                    string = string.replace(/<\/b>/g,'');
                                                    string = string.replace(/\\/g,'');
                                                    alert("Error occure during API processing ::\n"+string);
                                              }
                                            });
                                        } 
                                    },
                                    error: function(xhr, ajaxOptions, thrownError) {
                                        var string = xhr.responseText.replace(/<b>/g,'');
                                            string = string.replace(/<\/b>/g,'');
                                            string = string.replace(/\\/g,'');
                                            alert("Error occure during API processing ::\n"+string);
                                    }
                            })
                        
                    },
                    Cancel: function () {
                    },
                }
            });

    })
  /* Blue Dart click event */
  

  /* Connect India Logistics click event */
  
    $('#button-connect-india').click(function(){
    
        var select_warehouse_id = getWareHouse();
        if ( !select_warehouse_id ){
            return false;
        }
        if($('input[name=\'connect_india_weight\']').val() == '') {
             alert('Please enter weight!');
             return false;
        }
        if($('input[name=\'connect_india_width\']').val() == '') {
             alert('Please enter width!');
             return false;
        }
        if($('input[name=\'connect_india_length\']').val() == '') {
             alert('Please enter length!');
             return false;
        }
        if($('input[name=\'connect_india_height\']').val() == '') {
             alert('Please enter height!');
             return false;
        }
        if($('input[name=\'connect_india_declared_value\']').val() == '') {
             alert('Please enter declared amount value!');
             return false;
        }
        if($('input[name=\'connect_india_collectable_amount\']').val() == '') {
             alert('Please enter collactable amount value!');
             return false;
        }
        
        if($('input[name=\'connect_india_pickup_date\']').val() == '') {
             alert('Please enter pickup date & time!');
             return false;
        }

        $.ajax({
            url:'index.php?route=sale/order/courierApi&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>',
            dataType:'json',
            data:'',
            data:'courier=connectindia' + 
                    '&weight='+ ($('input[name=\'connect_india_weight\']').val()) +
                    '&length='+ ($('input[name=\'connect_india_length\']').val()) +
                    '&width='+ ($('input[name=\'connect_india_width\']').val()) +
                    '&height='+ ($('input[name=\'connect_india_height\']').val()) +
                    '&declared_value='+ ($('input[name=\'connect_india_declared_value\']').val()) +
                    '&collectable_amount='+ ($('input[name=\'connect_india_collectable_amount\']').val()) +
                    '&pickup_date='+ ($('input[name=\'connect_india_pickup_date\']').val()) +
                    '&warehouse_id=' + select_warehouse_id + 
                    '&vendor_code=' + ($('#input-gati-vendor-'+select_warehouse_id).val()),
            beforeSend: function() {
                $('#button-connect-india').button('loading');
            },
            complete: function() {
                $('#button-connect-india').button('reset');
            },
            success: function(json) {
                
                if (json['error']) {
                    
                     $('#form-connect-india').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }
                if (json['success']) {
                    
                    $('#form-connect-india').before('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    $('#shipping_label_pdf_connect_india').html('<a href="index.php?route=sale/order/shippingLabelPdf&token=<?php echo $token;?>&shipping_label_id='+json['shipping_label_id']+'&no_of_pkg=1" class="btn btn-warning" data-toggle="tooltip" data-original-title="<?php echo 'Generate Shipping Label'; ?>"><i class="fa fa-barcode"></i></a>');
                    $('#button-connect-india').remove();
                    $('#shipping_label_pdf_connect_india').show();
                    
                    var docket = json['docket'] ;
                     
                        $.ajax({
                          url: '<?php echo $https_catalog; ?>index.php?route=api/order/history&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&order_status_id=14',
                          type: 'post',
                          dataType: 'json',
                          data: '&notify=1' + 
                                '&notify_sms=1'  + 
                                '&comment=' + encodeURIComponent('Shipment sent by Connect-india-Logistics with Tracking No: '+ docket )+ 
                                '&shippingco=ConnectIndia' + 
                                '&tracking=' + docket ,
                          success: function(json) {
                              
                            $('.alert').remove();

                            if (json['error']) {
                              $('#form-gati').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                            }

                            if (json['success']) {

                              $('#form-gati').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                              
                            }
                          },
                          error: function(xhr, ajaxOptions, thrownError) {
                                var string = xhr.responseText.replace(/<b>/g,'');
                                    string = string.replace(/<\/b>/g,'');
                                    string = string.replace(/\\/g,'');
                                    alert("Error occure during API processing ::\n"+string);
                                //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                          }
                        });
                    } 
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    var string = xhr.responseText.replace(/<b>/g,'');
                        string = string.replace(/<\/b>/g,'');
                        string = string.replace(/\\/g,'');
                        alert("Error occure during API processing ::\n"+string);
                }
        
        })
    
    })
  /* Connect India Logistics click event */
  
  /*Fedex click event*/
    /*
    $('.fedex-account-codes').click(function(){
        var code = $(this).val();
        if(code == '693834325') {
            $('#fedex_number_of_packages').removeAttr('disabled','disabled');   
        }else{
            $('#fedex_number_of_packages').val('1');
            $('#fedex_number_of_packages').attr('disabled','disabled');   
        }
    })
    */
    $('#button-fedex').click(function(){
    
        var select_warehouse_id = getWareHouse();
        if ( !select_warehouse_id ){
            return false;
        }
        
        if($('input[name=\'fedex_account_code\']:checked').val() == ''){
            alert("Select account code.");
            return false;
        }
                
        if($("#fedex_service_type").val() == ''){
            alert("Service type can not be empty");
            return false;
        }
        if($("input[name='fedex_weight']").val() == ''){
            alert("Weight can not be empty");
            return false;
        }
        if($("input[name='fedex_width']").val() == ''){
            alert("Width can not be empty");
            return false;
        }
        if($("input[name='fedex_length']").val() == ''){
            alert("Length can not be empty");
            return false;
        }
        if($("input[name='fedex_height']").val() == ''){
            alert("Height can not be empty");
            return false;
        }
        if($('input[name=\'fedex_number_of_packages\']').val() == '') {
             //alert('Please enter No. of Packages!');
             //return false;
        }
        if($('input[name=\'fedex_pickup_date\']').val() == '') {
             alert('Please enter pickup date & time!');
             return false;
        }
        
        var no_of_pkg = $('input[name=\'fedex_number_of_packages\']').val();
    
    var fedex_account_code = $('input[name=\'fedex_account_code\']:checked').val();
    var service_type       = $("#fedex_service_type").val();
    var weight             = $("input[name='fedex_weight']").val();

    shipping_html = ' <div style="line-height:25px"><strong>Account code - '+ fedex_account_code +'</strong></div>';
    shipping_html += '<div style="line-height:25px"><strong>Service type - '+ service_type + '</strong></div>';
    shipping_html += '<div style="line-height:25px"><strong>Weight       - '+ weight + ' kg</strong></div>'; 

    $.confirm({
      icon: 'fa fa-warning',
      title: 'Confirm Fedex shipping details',
      content: shipping_html,
      buttons: {
          Confirm: function () { 
              $.ajax({
                  url:'index.php?route=sale/order/courierApi&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>',
                  dataType:'json',
                  data:'',
                  data :'courier=fedex' +
                          '&service_type='+($("#fedex_service_type").val()) +
                          '&account_code='+($('input[name=\'fedex_account_code\']:checked').val())+
                          '&weight='+($("input[name='fedex_weight']").val()) +
                          '&length='+ ($("input[name='fedex_length']").val()) +
                          '&width=' +($("input[name='fedex_width']").val()) +
                          '&height=' +($("input[name='fedex_height']").val()) +
                          '&no_of_pkg='+ ($('input[name=\'fedex_number_of_packages\']').val()) +
                          '&pickup_time=' +($("input[name='fedex_pickup_date']").val()) +
                          '&warehouse_id=' + select_warehouse_id,
                  beforeSend: function() {
                      $('#button-fedex').button('loading');
                  },
                  complete: function() {
                      $('#button-fedex').button('reset');
                  },
                  success: function(json) {
                      
                      if (json['error']) {
                          
                           $('#form-fedex').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>'); 
                      }
                      if (json['success']) {
                          
                          $('#form-fedex').before('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                          if(json['file_name']!='') {
                              $('#shipping_label_pdf_fedex').html('<a href="index.php?route=download/download&token=<?php echo $token;?>&shipping_label_id='+json['shipping_label_id']+'&no_of_pkg='+no_of_pkg+'&file_desc=shipping_label&file_path='+json['file_name']+'" class="btn btn-warning" data-toggle="tooltip" data-original-title="<?php echo 'Generate Shipping Label'; ?>"><i class="fa fa-barcode"></i></a>');
                          }else{
                              $('#shipping_label_pdf_fedex').html('<a href="index.php?route=sale/order/shippingLabelPdf&token=<?php echo $token;?>&shipping_label_id='+json['shipping_label_id']+'&no_of_pkg='+no_of_pkg+'" class="btn btn-warning" data-toggle="tooltip" data-original-title="<?php echo 'Generate Shipping Label'; ?>"><i class="fa fa-barcode"></i></a>'); 
                          }   
                          
                          $('#button-fedex').remove();
                          $('#shipping_label_pdf_fedex').show();
                          
                          var docket = json['docket'] ;
                           
                              $.ajax({
                                url: '<?php echo $https_catalog; ?>index.php?route=api/order/history&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>&order_status_id=14',
                                type: 'post',
                                dataType: 'json',
                                data: '&notify=1' + 
                                      '&notify_sms=1'  + 
                                      '&comment=' + encodeURIComponent('Shipment sent by Fedex with Tracking No: '+ docket )+ 
                                      '&shippingco=Fedex' + 
                                      '&tracking=' + docket ,
                                success: function(json) {
                                    
                                  $('.alert').remove();

                                  if (json['error']) {
                                    $('#form-gati').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                                  }

                                  if (json['success']) {

                                    $('#form-gati').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                                    
                                  }
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                  var string = xhr.responseText.replace(/<b>/g,'');
                                        string = string.replace(/<\/b>/g,'');
                                        string = string.replace(/\\/g,'');
                                        alert("Error occure during API processing ::\n"+string);
                                }
                              });
                          } 
                      },
                      error: function(xhr, ajaxOptions, thrownError) {
                        var string = xhr.responseText.replace(/<b>/g,'');
                              string = string.replace(/<\/b>/g,'');
                              string = string.replace(/\\/g,'');
                              alert("Error occure during API processing ::\n"+string);
                      }
              })

      },
        Cancel: function () {
        },
      }

    });
  
  })


  /*Fedex click event*/
  
  // non gati generated shipping label pdf
    $("input[name=\'generate_borcode\'").click(function(){
      if($(this).is(':checked')){
        $(this).val(1);
      } else {
        $(this).val(0);
      }
    });
  
  /*Non-Gati click event*/
  $('#button-non-gati').click(function(){
      var today ='<?php echo date('d_M_y'); ?>';

      if(!$('input[name=\'select_warehouse_id\']').is(":checked")){
        alert('Please select warehouse address !');
        return false;
      }else{
       var select_warehouse_id = $('input[name=\'select_warehouse_id\']:checked').val();
      }

      $.ajax({
        url:'index.php?route=sale/order/nonGatiDocket&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&suborder_id=<?php echo $suborder_id; ?>',
        dataType: 'json',
        data :'courier='+($('select[name=\'courier\']').val())+
              '&docket='+($('input[name=\'docketno\']').val()) +
              '&weight='+ ($('input[name=\'actual_weight\']').val()) +
              '&barcode=' +($('input[name=\'generate_borcode\']').val()) +
              '&warehouse_id=' + select_warehouse_id ,

        beforeSend: function() {
          $('#button-gati').button('loading');
        },
        complete: function() {
          $('#button-gati').button('reset');
        },

        success: function(json) {

          if (json['success']) {
            alert('Generated successfully. Please download shipping label !');
            $('#non-gati-shipping_label_pdf').html('<a href="index.php?route=sale/order/shippingLabelPdf&token=<?php echo $token;?>&shipping_label_id='+json['shipping_label_id']+'&no_of_pkg=1&isNoDocket=yes" class="btn btn-warning" data-toggle="tooltip" data-original-title="<?php echo 'Generate Shipping Label'; ?>"><i class="fa fa-barcode"></i></a>');
            $('#button-non-gati').remove();
            $('#non-gati-shipping_label_pdf').show();
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          var string = xhr.responseText.replace(/<b>/g,'');
                string = string.replace(/<\/b>/g,'');
                string = string.replace(/\\/g,'');
                alert("Error occure during data processing ::\n"+string);
        }
      });
    });
  /*Non-Gati click event*/
  
  /*Search button click event*/  
    $('#button-search').click(function () {
        
      $('#bluedart-surface-customer-code').html('');
      $('#bluedart-apex-customer-code').html('');
      
      $('.address_form_list').empty();
      
      $.ajax({
        url : 'index.php?route=sale/order/getSearchAddress&token=<?php echo $token;?>',
        type: 'POST',
        dataType: 'json',
        data: '&filter_warehouse=' + encodeURIComponent($('input[name=\'filter_warehouse_name\']').val()) +
               '&filter_city=' + encodeURIComponent($('input[name=\'filter_city\']').val()),
        beforeSend: function() {
          $('#button-search').button('loading');
        },
        complete: function() {
          $('#button-search').button('reset');
        },
        success: function(json) {
          $.each(json, function(index,element){

            var warehouse_address1 = '';
            var warehouse_address2 = '';
            if(element.address_1 != ''){
              warehouse_address1 = element.address_1 + '<br>';
            }
            if(element.address_2 != ''){
              warehouse_address2 = element.address_2 + ' <br>';
            }

            $('.address_form_list').append(
                '<div class="col-sm-3">' +
                    '<div class="addresses_list  warehouse_address_height_set" id="warehouse_id_'+element.warehouse_id+'" data-address-id="'+element.warehouse_id+'">'
                                          + element.warehouse_name +'<br>'
                                          + warehouse_address1
                                          + warehouse_address2
                                          + element.city +'-'
                                          + element.postcode +'<br>'
                                          + element.zone_name +'-'
                                          + element.country_name+'<br>'
                                          + element.telephone
                    +'</div>'
                    +'<div class="address_select">' +
                      '<input type="radio" id="input-address-'+element.warehouse_id+'" class="" name="select_warehouse_id" value="'+element.warehouse_id+'" />' +
                      '<label class="" for="input-address-'+element.warehouse_id+'" >'+element.zone_name+' , '+ element.country_name +'</label>' +
                      '<input type="hidden" id="input-gati-vendor-'+element.warehouse_id+'" class="" name="gati_vendor_code" value="'+element.gati_vendor_code+'" />' +
                      '<input type="hidden" id="input-bluedart-vendor-code-'+element.warehouse_id+'" class="" name="bluedart_vendor_code" value="'+element.bluedart_vendor_code+'" />' +
                      '<input type="hidden" id="input-bluedart-surface-customer-code-'+element.warehouse_id+'" class="" name="bluedart_surface_customer_code" value="'+element.bluedart_surface_customer_code+'" />' +
                      '<input type="hidden" id="input-bluedart-apex-customer-code-'+element.warehouse_id+'" class="" name="bluedart_apex_customer_code" value="'+element.bluedart_apex_customer_code+'" />' +

                      '<input type="hidden" id="input-feex-account-number1-'+element.warehouse_id+'" class="" name="fedex_account_number1" value="'+element.fedex_account_numbers[0]+'" />' +
                      '<input type="hidden" id="input-feex-account-number2-'+element.warehouse_id+'" class="" name="fedex_account_number2" value="'+element.fedex_account_numbers[1]+'" />' +

                    '</div>' +
                '</div>'
            );
          });
        }
      });
    });
  /*Search button click event*/ 
  
  /*Other click event*/
  $('input[name=\'filter_warehouse_name\'], input[name=\'filter_city\']').on('keypress',function(e){
      if (e.keyCode == 13) {
        $('#button-search').trigger('click');
      }
    });
  $(document).delegate('.pickup_time', 'focus', function () {
        $(this).datetimepicker({
          format: 'YYYY-MM-DD hh:mm A'

        });
    });  
  $(document).delegate('div.addresses_list','click',function(){
    $('div.addresses_list').removeClass('selected_warehouse_address');
    $('input[name=\'select_warehouse_id\']').removeAttr('checked');
    $(this).addClass('selected_warehouse_address');
    var selected_warehouse_id = $(this).attr('data-address-id');
    $('#input-address-'+selected_warehouse_id).prop('checked',true);
    
    surface_code = $('#input-bluedart-surface-customer-code-'+selected_warehouse_id).val();
    if(surface_code != '') {
        $('#bluedart-surface-customer-code').html('('+surface_code+')');
    }else{
        $('#bluedart-surface-customer-code').html('');
    }

    /*set fedex account numbers */
    fedex_account_number1 = $('#input-feex-account-number1-'+selected_warehouse_id).val();
    fedex_account_number2 = $('#input-feex-account-number2-'+selected_warehouse_id).val();
    $('#fedex_account_code_1').val(fedex_account_number1); 
    $('#fedex_account_code_1_txt').html(fedex_account_number1);
    $('#fedex_account_code_2').val(fedex_account_number2); 
    $('#fedex_account_code_2_txt').html(fedex_account_number2);

  });

  $(document).delegate('input[name=\'select_warehouse_id\']','click',function(){ 
    var selected_warehouse_id = $(this).val();
    $('div.addresses_list').removeClass('selected_warehouse_address');
    $('#warehouse_id_'+selected_warehouse_id).addClass('selected_warehouse_address');
    
    /*set fedex account numbers */
    fedex_account_number1 = $('#input-feex-account-number1-'+selected_warehouse_id).val();
    fedex_account_number2 = $('#input-feex-account-number2-'+selected_warehouse_id).val();
    $('#fedex_account_code_1').val(fedex_account_number1); 
    $('#fedex_account_code_1_txt').html(fedex_account_number1);
    $('#fedex_account_code_2').val(fedex_account_number2); 
    $('#fedex_account_code_2_txt').html(fedex_account_number2);
    
  });
  
  
})/*ready-state*/

</script>