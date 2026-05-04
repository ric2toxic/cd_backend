<page>
<table align="center" style="width:100%; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95;border:1px #000 solid;">
    <tr>
        <td>
            <table  cellpadding="0" cellspacing="0" align="left" style="width:97%; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95;border:1px #000 solid;">
                <tr>
                    <td style="width:25%; vertical-align:middle;  padding-top:5px;" align="center">
                        <img src="<?php echo $data['shipping_address']['site_logo'];?>" alt=""/>
                    </td>
                    <td style="width:35%; vertical-align:top; border-left: 1px solid #000; border-right: 1px solid #000;">
                        <table style="width:100%;">
                                   
                                <?php if(isset($data['from_address']['warehouse_name'])) { ?>      
                                    <tr>
                                        <td style="text-align:left; font-weight: bold">
                                           <?php echo $data['from_address']['warehouse_name']?>
                                        </td>
                                    </tr>
                                   <?php } ?>
                                    
                                    <?php if(isset($data['from_address']['address_1'])) { ?>        
                                    <tr>
                                        <td style="text-align:left;">
                                           <?php echo wordwrap($data['from_address']['address_1'], 40, "<br>", true);?> 
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    
                                    <?php if(isset($data['from_address']['address_2'])) { ?>        
                                    <tr>
                                        <td style="text-align:left;">
                                           <?php echo wordwrap($data['from_address']['address_2'], 40, "<br>", true);?> 
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    
                                     <?php if(isset($data['from_address']['city'])) { ?>        
                                    <tr>
                                        <td style="text-align:left;">
                                           <?php echo $data['from_address']['city']?>, 
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    
                                    <?php if(isset($data['from_address']['state'])) { ?>     
                                    <tr>
                                        <td style="text-align:left;">
                                           <?php echo $data['from_address']['state']?>, 
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    
                                    <?php if(isset($data['from_address']['email'])) { ?>    
                                    <tr>
                                        <td style="text-align:left;">
                                           Email: <?php echo $data['from_address']['email']?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td style="text-align:left;">
                                           Website: www.wholesalebox.in
                                        </td>
                                    </tr>
                            </table>
                    </td>
                    <td style="width:35%; vertical-align:top;">
                            <table width="100%">
                            <?php if(isset($data['shipping_address']['courier'])) { ?>      
                                <tr>
                                    <td style="text-align:left; font-weight: bold; font-size: 30px;">
                                       <?php echo $data['shipping_address']['courier']?>
                                       <?php echo ($data['shipping_address']['service_type']) ? ' - '.$data['shipping_address']['service_type'] : ''?>
                                    </td>
                                </tr>
                            <?php } ?>    
                            <?php if(isset($data['shipping_address']['invoice_date'])) { ?>   
                                <tr>
                                    <td style="text-align:left;">
                                      INVOICE DATE : <?php echo date('d/m/Y',strtotime($data['shipping_address']['invoice_date']));?>
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php if(isset($data['shipping_address']['invoice_number'])) { ?>   
                               <tr>
                                    <td style="text-align:left;">
                                      INVOICE NO : <?php echo $data['shipping_address']['invoice_number'];?>
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php if(isset($data['shipping_address']['gstin'])) { ?>   
                                <tr>
                                    <td style="text-align:left;">
                                      GSTIN/UIN : <?php echo $data['shipping_address']['gstin'];?>
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php if(isset($data['shipping_address']['pan_no'])) { ?>  
                                <tr>
                                    <td style="text-align:left;">
                                      CO. PAN NO : <?php echo $data['shipping_address']['pan_no'];?>
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php if(isset($data['from_address']['customer_code']) && trim($data['from_address']['customer_code'])!=='') { ?>  
                                <tr>
                                    <td style="text-align:left; font-weight: bold; font-size: 18px;">
                                      <?php echo strtoupper($data['shipping_address']['payment_method']);?> : 
                                      <?php echo $data['from_address']['customer_code'];?>
                                    </td>
                                </tr>
                            <?php } ?>

                            </table>
                    </td>
                </tr>

            </table>

     
        </td>
    </tr>
            
    
    <tr>
        <td>
            <table style="width:99%; font-size: 18px; font-weight: bold; " cellpadding="2" cellspacing="2" align="left">
                <tr>
                    <td style="text-align: left; font-weight: bold;">DELIVER TO</td>
                </tr>

            </table>
        </td>
    </tr>
    
    
    
    <tr>
        <td style="width:100%">
            <table cellpadding="0" cellspacing="0" align="left" style="width:100%; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95;border:1px #000 solid;">
                <tr>
                    <td style="width:40%; text-align: left; border-right: 1px solid #000;">
                        <table style="width:100%; padding-left:10px;">
                          <?php if(isset($data['shipping_address']['shipping_customername'])) { ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-size: 20px; font-weight: bold;" valign="top">
                                    <?php echo wordwrap(ucfirst($data['shipping_address']['shipping_customername']), 40, "<br>", true)?>
                                </td>
                            </tr>
                          <?php } ?>

                           <?php if(isset($data['shipping_address']['shipping_company'])) { ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight: bold;" valign="top">
                                    c/o <?php echo wordwrap(ucfirst($data['shipping_address']['shipping_company']), 40, "<br>", true)?>
                                </td>
                            </tr>
                           <?php } ?>

                            <?php if(isset($data['shipping_address']['shipping_address_1'])) { ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight: bold;" valign="top">
                                    <?php echo wordwrap($data['shipping_address']['shipping_address_1'], 40, "<br>", true)?>
                                </td>
                            </tr>
                           <?php } ?>

                           <?php if(isset($data['shipping_address']['shipping_address_2'])) { ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight: bold;" valign="top">
                                    <?php echo wordwrap($data['shipping_address']['shipping_address_2'], 40, "<br>", true)?>
                                </td>
                            </tr>
                           <?php } ?>

                           <?php if(isset($data['shipping_address']['shipping_city'])) { ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight: bold;" valign="top">
                                    <?php echo $data['shipping_address']['shipping_city']?>, 
                                </td>
                            </tr>
                           <?php } ?>

                           <?php if(isset($data['shipping_address']['shipping_zone'])) { ?> 
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight: bold;" valign="top">
                                    <?php echo $data['shipping_address']['shipping_zone']?>
                                </td>
                            </tr>
                            <?php } ?>
                            
                            <?php if(isset($data['shipping_address']['shipping_postcode'])) { ?> 
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight: bold;" valign="top">
                                    <?php echo $data['shipping_address']['shipping_postcode']?>
                                </td>
                            </tr>
                            <?php } ?>

                           <?php if(!empty($data['shipping_address']['alternate_numbers'])) { ?>  
                            <tr>
                                <td style="width:25%; text-align:left; font-weight: bold;" valign="top">
                                    Phone(s) : 
                                </td>
                                 <td style="width:75%; text-align:left; font-weight: bold;" valign="top">
                                   <?php echo implode(', ',$data['shipping_address']['alternate_numbers']);?>
                                </td>
                            </tr>
                           <?php } ?>

                        </table>
                    </td>
                    <td style="width:60%; text-align: left;">
                        <table style="width:100%; ">
                         <?php if(file_exists($data['shipping_address']['barcode_img_tracking'])) {?>   
                            <tr>
                                <td style="text-align: left; font-weight: bold;">AWB NO.</td>
                                <td style="text-align: left">
                                    <img src="<?php echo $data['shipping_address']['barcode_img_tracking'];?>"  alt=""/>
                                </td>
                            </tr>
                         <?php } ?>   
                        <?php if(file_exists($data['shipping_address']['barcode_img_order'])) {?> 
                            <tr>
                                <td style="text-align: left; font-weight: bold;">ORDER NO.</td>
                                <td style="text-align: left">
                                    <img src="<?php echo $data['shipping_address']['barcode_img_order'];?>"  alt=""/>
                                </td>
                            </tr>
                         <?php } ?>    
                        </table>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
    
 <?php if(isset($data['shipping_address']['payment_method']) && trim($data['shipping_address']['payment_method'])!='') {?>     
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" align="left" style="width:100%;">
                <tr>
                    <td style="width:100%; text-align: left; font-weight: bold; font-size: 18px;">
                        <?php if(isset($data['shipping_address']['payment_method']) && strtolower($data['shipping_address']['payment_method']) == 'cod'){ ?>

                            CASH ON DELIVERY

                       <?php  }else{ ?>

                            PRE-PAID ORDER - <?php echo $this->currency->format($data['shipping_address']['parcel_amount']);?>

                       <?php }  ?> 

                    </td>
                </tr>
            </table>
        </td>
    </tr>
  <?php }  ?> 
    
   <?php if(isset($data['shipping_address']['payment_method']) && strtolower($data['shipping_address']['payment_method']) == 'cod'){ ?>
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" align="left" style="width:100%; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95;border:1px #000 solid;">
                <tr>
                    <td style="padding:10px; width:50%; text-align: left; font-weight: bold; font-size: 18px; border-right:1px solid #000;">
                       Collectable Amount : <?php echo $this->currency->format($data['shipping_address']['cod_amount']);?>
                    </td>
                    <td style="padding:10px; width:50%; text-align: left; font-weight: bold; font-size: 18px;">
                        Declared Value : <?php echo $this->currency->format($data['shipping_address']['parcel_amount']);?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
   <?php } ?> 
    
    <tr>
        <td style="width:100%; text-align: left;">
            <table cellpadding="0" cellspacing="0" align="left" style="width:100%; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95;border:1px #000 solid;">
                <tr>
                    <td style="width:15%; text-align: left; padding:5px; border-right: 1px solid #000; border-bottom: 1px solid #000;">Sr. No</td>
                    <td style="width:25%; text-align: left; padding:5px; border-right: 1px solid #000; border-bottom: 1px solid #000;">Goods Description</td>
                   <?php if(!empty($data['shipping_address']['length']) && !empty($data['shipping_address']['breadth']) 
                            && !empty($data['shipping_address']['height'])) { ?>
                    <td style="width:25%; text-align: left; padding:5px; border-right: 1px solid #000; border-bottom: 1px solid #000;" border-bottom: 1px solid #000;"">Dimension<br>(cm)(LxBxH)</td>
                    <?php } ?>
                    <td style="width:10%; text-align: left; padding:5px; border-right: 1px solid #000; border-bottom: 1px solid #000;">Wt.</td>
                    <td style="width:10%; text-align: left; padding:5px; border-right: 1px solid #000; border-bottom: 1px solid #000;">Qty.</td>
                    <td style="width:10%; text-align: left; padding:5px; border-bottom: 1px solid #000;">Amount</td>
                </tr> 

                <tr>
                    <td style="width:15%; text-align: left; padding:5px; border-right: 1px solid #000;">1</td>
                    <td style="width:25%; text-align: left; padding:5px; border-right: 1px solid #000;"><?php echo $data['shipping_address']['goods_label'];?></td>
                    <?php if(!empty($data['shipping_address']['length']) && !empty($data['shipping_address']['height'])) { ?>
                    <td style="width:25%; text-align: left; padding:5px; border-right: 1px solid #000;">
                        <?php echo $data['shipping_address']['length'];?> X 
                        <?php echo $data['shipping_address']['breadth'];?> X 
                        <?php echo $data['shipping_address']['height'];?>
                    </td>
                    <?php } ?>
                    <td style="width:10%; text-align: left; padding:5px; border-right: 1px solid #000;"><?php echo $data['shipping_address']['weight'];?></td>
                    <td style="width:10%; text-align: left; padding:5px; border-right: 1px solid #000;"><?php echo $data['shipping_address']['count'];?></td>
                    <td style="width:15%; text-align: left; padding:5px;">
                        <?php if(strtolower($data['shipping_address']['payment_method']) == 'prepaid'){ ?>

                            <?php echo $this->currency->format($data['shipping_address']['parcel_amount'])?>

                       <?php  }else{ ?>

                            <?php echo $this->currency->format($data['shipping_address']['cod_amount'])?>

                       <?php }  ?>
                    </td>
                </tr> 
            </table>
        </td>
    </tr>
    
    <tr>
        <td style="width:99%; text-align: left;">
            <table style="width:100%;" cellpadding="2" cellspacing="2" align="left">
                <tr>
                    <td style="width:100%; text-align: left; font-style: italic;">If un-delivered, please return to : </td>
                </tr>
                <tr>
                    <td style="width:100%; text-align: left;">
                        <b><?php echo $data['from_address']['warehouse_name']?></b>
                        <?php echo $data['from_address']['address_1']?>, 
                        <?php echo $data['from_address']['address_2']?>, 
                        <?php echo $data['from_address']['city']?>,
                        <b><?php echo trim($data['from_address']['postcode']);?></b>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr>
        <td style="width:99%; text-align: left;">
            <table style="width:100%;" cellpadding="2" cellspacing="2" align="center">
                <tr>
                    <td style="width:100%; text-align: center; height: 20px;"></td>
                </tr>
                <tr>
                    <td style="width:100%; text-align: left; font-size: 25px; font-weight: bold; font-style: italic;"> NOTE: DO NOT ACCEPT THE SHIPMENT IF NORMAL WHITE TAPE IS USED. </td>
                </tr>
            </table>
        </td>
    </tr>
   
    <tr>
        <td style="width:99%; text-align: left;">
            <table style="width:100%;" cellpadding="2" cellspacing="2" align="center">
                <tr>
                    <td style="width:100%; text-align: center; height: 20px;"></td>
                </tr>
                <tr>
                    <td style="width:100%; text-align: center; font-style: italic;">This is computer generated document, hence does not required signature. </td>
                </tr>
            </table>
        </td>
    </tr>
    
</table>
</page>