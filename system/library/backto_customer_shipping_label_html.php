<page>
<table align="center" style="width:100%; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95;border:1px #000 solid;">
    <tr>
        <td>
            <table  cellpadding="0" cellspacing="0" align="left" style="width:97%; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95;border:1px #000 solid;">
                <tr>
                    <td style="width:25%; vertical-align:middle;  padding-top:5px;" align="center">
                        <img src="<?php echo $data['site_logo'];?>" alt=""/>
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
                            <?php if(isset($data['courier_company'])) { ?>      
                                <tr>
                                    <td style="text-align:left; font-weight: bold; font-size: 30px;">
                                       <?php echo $data['courier_company']?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if(isset($data['gstin'])) { ?>   
                                <tr>
                                    <td style="text-align:left;">
                                      GSTIN/UIN : <?php echo $data['gstin'];?>
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php if(isset($data['pan_no'])) { ?>  
                                <tr>
                                    <td style="text-align:left;">
                                      CO. PAN NO : <?php echo $data['pan_no'];?>
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
                          <?php if(isset($data['to_address']['name'])) { ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-size: bold; font-weight: bold;" valign="top">
                                    <?php echo wordwrap(ucfirst($data['to_address']['name']), 40, "<br>", true)?>
                                </td>
                            </tr>
                          <?php } ?>

                           <?php if(isset($data['to_address']['company'])) { ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight: bold;" valign="top">
                                    c/o <?php echo wordwrap(ucfirst($data['to_address']['company']), 40, "<br>", true)?>
                                </td>
                            </tr>
                           <?php } ?>

                            <?php if(isset($data['to_address']['address_1'])) { ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight: bold;" valign="top">
                                    <?php echo wordwrap($data['to_address']['address_1'], 40, "<br>", true)?>
                                </td>
                            </tr>
                           <?php } ?>

                           <?php if(isset($data['to_address']['address_2'])) { ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight: bold;" valign="top">
                                    <?php echo wordwrap($data['to_address']['address_2'], 40, "<br>", true)?>
                                </td>
                            </tr>
                           <?php } ?>

                           <?php if(isset($data['to_address']['city'])) { ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight: bold;" valign="top">
                                    <?php echo $data['to_address']['city']?>, 
                                </td>
                            </tr>
                           <?php } ?>

                           <?php if(isset($data['to_address']['state'])) { ?> 
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight: bold;" valign="top">
                                    <?php echo $data['to_address']['state']?>
                                </td>
                            </tr>
                            <?php } ?>
                            
                            <?php if(isset($data['to_address']['postcode'])) { ?> 
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight: bold;" valign="top">
                                    <?php echo $data['to_address']['postcode']?>
                                </td>
                            </tr>
                            <?php } ?>

                           <?php /* if(!empty($data['to_address']['phone'])) { ?>  
                            <tr>
                                <td style="width:50%; text-align:left; font-weight: bold;" valign="top">
                                    Tel. :<?php echo $data['to_address']['phone']?>
                                </td>
                                <td style="width:50%; text-align:left; font-weight: bold;" valign="top">
                                    Mob. :<?php echo $data['to_address']['phone']?>
                                </td>
                            </tr>
                           <?php } */ ?>
                           <?php if(!empty($data['to_address']['alternet_numbers'])) { ?>  
                            <tr>
                                <td style="width:25%; text-align:left; font-weight: bold;" valign="top">
                                    Phone(s) : 
                                </td>
                                <td style="width:75%; text-align:left; font-weight: bold;" valign="top">
                                    <?php echo implode(', ', $data['to_address']['alternet_numbers']);?>
                                </td>
                            </tr>
                           <?php } ?>
                        </table>
                    </td>
                    <td style="width:60%; text-align: left;">
                        <table style="width:100%; ">
                         <?php if(file_exists($data['barcode_tracking_no'])) {?>   
                            <tr>
                                <td style="text-align: left; font-weight: bold;">AWB NO.</td>
                                <td style="text-align: left">
                                    <img src="<?php echo $data['barcode_tracking_no'];?>"  alt=""/>
                                </td>
                            </tr>
                         <?php } ?>   
                        <?php if(file_exists($data['barcode_order_no'])) {?> 
                            <tr>
                                <td style="text-align: left; font-weight: bold;">ORDER NO.</td>
                                <td style="text-align: left">
                                    <img src="<?php echo $data['barcode_order_no'];?>"  alt=""/>
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
                    <td style="width:100%; text-align: left; font-size: 25px; font-weight: bold; font-style: italic;"> NOTE: DO NOT ACCEPT THE SHIPMENT IF NORAML WHITE
TAPE IS USED. </td>
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
