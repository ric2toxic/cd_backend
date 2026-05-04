<table style="width:100%!important"> 
   <tbody>
    <tr width="834px" height="60" background="<?php echo STATIC_CONTENT_URL_SSL.'img/background_screen.png'; ?>"> 
     <td> 
      <table style="width:600px!important;text-align:center;margin:0 auto" width="100%" height="60" cellspacing="0" cellpadding="0"> 
       <tbody>
        <tr> 
         <td> 
          <table style="width:640px;max-width:640px;padding-right:20px;padding-left:20px"> 
           <tbody>
            <tr> 
             <td width="50%" valign="middle" height="50" align="left"> <a href="<?php echo HTTPS_CATALOG; ?>" style="text-decoration:none;display:table;" target="_blank"> <img style="border:none;color:#818181;font-size:9px;display:table-cell;margin-right:5px; width:50%" src="<?php echo STATIC_CONTENT_URL_SSL.'catalog/rsz_wsb_tmp_logo_286.png'; ?>"> </a> </td>
             <td style="width:60%;text-align:right;padding-top:5px"> <p style="color:rgba(255,255,255,0.8);font-family:Arial;font-size:16px;text-align:right;color:#000000;font-style:normal;font-stretch:normal"> Order <span style="font-weight:bold">Placed </span></p> </td> 
            </tr> 
           </tbody>
          </table> </td> 
        </tr> 
       </tbody>
      </table> 
       </td> 
    </tr> 
    <tr> 
     <td> 
      <table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f5f5f5"> 
       <tbody>
        <tr> 
         <td valign="top" bgcolor="#f5f5f5" align="center"> 
           
          <table style="width:640px;max-width:640px;padding-right:20px;padding-left:20px;background-color:#fff;padding-top:20px;padding-bottom:20px; margin-bottom:10px;" cellspacing="0" cellpadding="0" border="0"> 
           <tbody>
            <tr> 
             <td class="m_814922419738374835container-padding" align="left"> 
              <table class="m_814922419738374835force-row" width="320" cellspacing="0" cellpadding="0" border="0" align="left"> 
               <tbody>
                <tr> 
                  <td class="m_814922419738374835col" valign="top"> <p style="font-family:Arial;color:#878787;font-size:12px;font-weight:normal;font-style:normal;font-stretch:normal;margin-top:0px;line-height:12px;padding-top:0px;margin-bottom:7px"> Hi 
                   <span style="font-weight:bold;color:#191919"> <?php echo $customer_name; ?>,</span> </p> 
                  </td> 
                </tr> 
               </tbody>
              </table> 
              <table class="m_814922419738374835force-row" width="250" cellspacing="0" cellpadding="0" border="0" align="right"> 
               <tbody>
                <tr> 
                 <td class="m_814922419738374835col" valign="top"> <p style="font-family:Arial;color:#747474;font-size:11px;font-weight:normal;text-align:right;font-style:normal;line-height:12px;font-stretch:normal;margin-top:0px;margin-bottom:7px;padding-top:0px;color:#878787">Order placed on <span style="font-weight:bold;color:#000"><?php echo $date_added; ?></span></p> <p style="font-family:Arial;font-size:11px;color:#878787;line-height:12px;text-align:right;padding-top:0px;margin-top:0px;margin-bottom:7px">Order No. <span style="font-weight:bold;color:#000"><?php echo $order_no; ?></span></p> </td> 
                </tr> 
               </tbody>
              </table> </td> 
            </tr> 
            <tr> 
             <td class="m_814922419738374835container-padding m_814922419738374835content" border="1" style="background-color:rgba(245,245,245,0.5);background:rgba(245,245,245,0.5);border:.5px solid #233c98;border-radius:2px;padding-top:20px;padding-bottom:5x;border-color:#233c98;border-width:.08em;border-style:solid;border:.08em solid #233c98" align="left"> 
              <table class="m_814922419738374835force-row" style="margin-bottom:20px" cellspacing="0" cellpadding="0" border="0" align="left"> 
               <tbody>
                <tr> 
                 <td class="m_814922419738374835col" style="padding-left:15px" valign="top"> <img src="<?php echo STATIC_CONTENT_URL_SSL.'img/order_placed.png'; ?>" class="m_814922419738374835journey CToWUd" alt="journey" style="padding-top:3px"> </td> 
                </tr>
                <tr>
                  <td>
                    <p style="margin-left:15px;margin-top:30px">
                  <a href="<?php echo $link. '&searching_value='. $order_no; ?>" style="background-color:rgb(41,121,251);color:#fff;padding:0;border:0px;font-size:14px;display:table-cell;margin-top:0px;border-radius:2px;text-decoration:none;text-align:center;width:160px;height:32px;vertical-align:middle;font-family:Arial" target="_blank" data-saferedirecturl="<?php echo $link; ?>"><button type="button" style="background-color:#233c98;color:#fff;border:0px;font-size:14px;border-radius:2px;text-decoration:none;height:32px;padding:0;width:160px;display:table-cell;vertical-align:middle;font-family:Arial">View Your Order </button></a></p>
                  </td>
                </tr> 
               </tbody>
              </table> 
              <table class="m_814922419738374835force-row" style="margin-bottom:20px" width="255" cellspacing="0" cellpadding="0" border="0" align="right"> 
               <tbody>
                <tr> 
                 <td class="m_814922419738374835col" style="padding-right:15px;padding-left:15px" valign="top" align="left"> <p style="margin-top:0px;line-height:1.56;margin-bottom:0px"><span style="font-family:Arial;font-size:14px;font-weight:bold;text-align:left;color:#212121;line-height:20px;margin-bottom:2px;display:block">Order Summary</span>
                  <span style="font-family:Arial;font-size:12px;color:#212121;line-height:17px;display:block"><b>Total Qty. :</b> <?php echo $total_sets; ?> Sets | <?php echo $total_pieces; ?> Pcs.</span>

                  <?php foreach ($totals as $total) { ?>
                    <?php if ($total['code'] == 'tax' and $cform_submit) { ?>

                      <span style="font-family:Arial;font-size:12px;color:#212121;line-height:17px;display:block"><b><?php echo $text_cst; ?>:</b> <?php echo $cst_with_cform; ?> </span>
                      <span style="font-family:Arial;font-size:12px;color:#212121;line-height:17px;display:block"><b><?php echo $text_tax_refund; ?>:</b> <?php echo $refundable_cform; ?> </span>
                    
                    <?php } else { ?>

                      <?php 
                      if($total['code'] == 'paycharge' && !empty($total['breakup'])) {
                        foreach ($total['breakup'] as $dis_rate => $paycharge_data) {
                          $dis_percent = "Discount (" . (-1) * $dis_rate . "%)";
                      ?>
                          <span style="font-family:Arial;font-size:12px;color:#212121;line-height:17px;display:block"><b><?php echo $dis_percent; ?>:</b> <?php echo $paycharge_data['discount']; ?> </span>
                          
                      <?php } ?>
                      <?php } else { ?>

                        <span style="font-family:Arial;font-size:12px;color:#212121;line-height:17px;display:block"><b><?php echo $total['title']; ?>:</b> <?php echo $total['text']; ?> </span>
                        
                      <?php } ?>

                    <?php } ?>
                  <?php } ?>
                  </p>
                 </td> 
                </tr> 
               </tbody>
              </table> 
              <table class="m_814922419738374835force-row" width="600" cellspacing="0" cellpadding="0" border="0" align="left"> 
               <tbody>
                <tr> 
                 <td class="m_814922419738374835col" style="padding-right:15px;padding-left:15px" valign="top" align="left"> <p style="font-family:Arial;font-size:12px;text-align:left;color:#212121;padding-top:0px;margin-top:0px;padding-bottom:0px;line-height:1.58;margin-bottom:20px">Thank you for your interest in WholesaleBox products. Your order has been received and will be confirmed, as per payment receipt and other details accordingly.</p> </td> 
                </tr> 
               </tbody>
              </table>
               </td> 
            </tr>
            
           </tbody>
          </table> 
          
          <table style="width:640px;max-width:640px;padding-right:20px;padding-left:20px;background-color:#fff;padding-top:20px;padding-bottom:20px;border-collapse:collapse;width:640px;max-width:640px;margin-bottom:10px">
          <thead>
            <tr>
              <td colspan="2" style="font-family:Arial;font-size:12px;border-bottom:1px solid #dddddd;text-align:left;padding:10px 20px;color:#233c98">Shipping And Payment Details
              </td></tr></thead>          
            <tbody>
              <tr>
                <td style="font-family:Arial;font-size:12px;border-right:1px solid #dddddd;text-align:left;padding:20px;line-height:1.5; width:110px; min-width: 110px;">                  
                  <b>Payment Method:</b><br>
                  <b>Shipping Method:</b><br>        
                  <b>Payment Address:</b><br>
                  <b>Shipping Address:</b><br>
                  <?php if( !empty($gst_number) ){ ?>
                    <b>GST No. :</b><br>
                  <?php } ?> 
                </td>
                <td style="font-family:Arial;font-size:12px;text-align:left;padding:20px;line-height:1.5;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">                  
                   <div style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap; width: 440px;"><?php echo $payment_method; ?></div>
                   <div style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap; width: 440px;"><?php echo $shipping_method; ?></div>        
                   <div style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap; width: 440px;"><?php echo $payment_address; ?></div>
                   <div style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap; width: 440px;"><?php echo $shipping_address; ?></div>
                 <?php if( !empty($gst_number) ){ ?>
                   <div style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap; width: 440px;"><?php echo $gst_number; ?></div>
                 <?php } ?>    
                </td>
              </tr>
            </tbody>
          </table>

          <!---Product Listing -->
          <?php foreach ($products as $product) { ?>

            <table class="m_-3411267466098705577container" style="background-color:#fff;width:640px;max-width:640px" width="100%" cellspacing="0" cellpadding="0" border="0"> 
             <tbody>
              <tr> 
                <td class="m_-3411267466098705577link" style="padding-top:20px;padding-bottom:20px;width:25%" width="120" valign="middle" align="center"> 
                  <a 
                    style="color:#027cd8;text-decoration:none;outline:none;color:#fff;font-size:0px" 
                    href="<?php echo $product['href']; ?>"
                    onMouseOver="this.style.white-space='normal"> 
                    <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" style="border:none;max-width:125px;max-height:146px" class="CToWUd" border="0"> 
                  </a> 
                </td> 
                <td 
                  style="padding-top:20px;padding-bottom:20px" valign="top" align="left"> <p style="margin-bottom:7px;margin-top:0px;line-height:20px" class="m_-3411267466098705577link"> 
                  <a href="<?php echo $product['href']; ?>" class="m_-3411267466098705577item" style="font-family:Arial;font-size:14px;font-weight:normal;font-style:normal;font-stretch:normal;text-decoration:none;word-spacing:0.2em;max-width:420px;min-width:420px;display:block" target="_blank"><?php echo $product['name']; ?></a> </p> 
                <p style="font-family:Arial;font-size:12px;color:#212121;line-height:18px;margin-top:0;margin-bottom:7px">Product Code: <?php echo $product['model']; ?></p> 
                <p style="font-family:Arial;font-style:normal;font-size:12px;font-stretch:normal;color:#212121;line-height:15px;margin-top:0;margin-bottom:7px"> <b>Sets : </b>  <?php echo $product['quantity']; ?>  |   <b>Pieces : </b> <?php echo $product['pieces']; ?></p> 
                <p style="font-family:Arial;font-style:normal;font-size:12px;font-stretch:normal;color:#212121;line-height:15px;margin-top:0;margin-bottom:7px"> <b>Rate / pc. : </b>  <?php echo $product['price_per_piece']; ?> |  <b>Discount : </b> <?php echo $product['discount_per_piece']; ?></p> 
                <p style="font-family:Arial;font-style:normal;font-size:12px;font-stretch:normal;color:#212121;line-height:15px;margin-top:0;margin-bottom:7px"><b>Tax Rate (<?php echo $product['tax']; ?>) : </b> <?php echo $product['tax_per_piece']; ?></p> 
                <p style="font-family:Arial;font-style:normal;font-size:12px;font-stretch:normal;color:#212121;line-height:15px;margin-top:0;margin-bottom:7px"><b>Amount (Ex. Tax) : </b>  <?php echo $product['total']; ?></p>
                </td> 
              </tr> 
             </tbody>
            </table>

          <?php } ?>

          <table class="m_814922419738374835container" style="padding-right:20px;padding-left:20px;background-color:#fff;width:640px;max-width:640px" width="600" cellspacing="0" cellpadding="0" border="0"> 
           <tbody>
            <tr> 
             <td class="m_814922419738374835container-padding" align="left"> 
              <table style="margin-bottom:20px" width="100%" cellspacing="0" cellpadding="0" border="0"> 
               <tbody> 
                <tr> 
                 <td style="border-top:1px solid #f0f0f0"></td> 
                </tr> 
               </tbody> 
              </table> </td> 
            </tr> 
            <tr> 
             <td> 
              <table style="width:600px;max-width:600px;background:#ffffff" width="100%" cellspacing="0" cellpadding="0"> 
               <tbody>
                <tr class="m_814922419738374835col" style="color:#212121"> 
                 <td class="m_814922419738374835container" style="color:#212121;border-bottom:1px solid #f0f0f0" valign="top" align="left"> <p style="font-family:Arial;font-size:14px;font-weight:bold;line-height:1.86;color:#212121;margin-top:0px">Thank you for shopping with <span class="il">Wholesalebox</span>!</p> <br> </td> 
                </tr> 
               </tbody>
              </table> </td> 
            </tr> 
            <tr> 
             <td> 
              <table style="width:600px;max-width:600px;margin-top:14px" width="100%" cellspacing="0" cellpadding="0"> 
               <tbody>
                <tr> 
                 <td class="m_814922419738374835container" style="color:#2c2c2c;line-height:20px;font-weight:300;background-color:transparent" valign="top" align="left"> 
                  <table class="m_814922419738374835body-wrapper" style="width: 100%;"> 
                   <tbody>
                    <tr> 
                       <td style="text-align:left; display: inline-flex; float: left;width: 60%;"> 
                          <a href="https://www.facebook.com/wholesaleboxOfficial/" style="text-decoration:none;display:table;" target="_blank"><img style="width: 30px;" src="<?php echo STATIC_CONTENT_URL_SSL. 'fb.png'; ?>"> 
                          </a> 
                          <a href="https://twitter.com/wholesalebox_in?lang=en" style="text-decoration:none;display:table;" target="_blank"><img style="width: 30px;" src="<?php echo STATIC_CONTENT_URL_SSL. 'twitter.png'; ?>"> 
                          </a>
                          <a href="https://in.linkedin.com/company/wholesalebox-internet-pvt--ltd-" style="text-decoration:none;display:table;" target="_blank"><img style="width: 30px;" src="<?php echo STATIC_CONTENT_URL_SSL. 'linkedin.png'; ?>"> 
                          </a>
                          <a href="https://www.instagram.com/wholesalebox/" style="text-decoration:none;display:table;" target="_blank"><img style="width: 30px;" src="<?php echo STATIC_CONTENT_URL_SSL. 'insta.png'; ?>"> 
                          </a>
                        </td>
                                            
                        <td style="padding: 6px 3px 5px 0px; height: 30px" align="right">
                          <a href="https://play.google.com/store/apps/details?id=in.wholesalebox&hl=en_IN" style="display: inline-block; outline: none; margin-right: 10px" target="_blank">
                          <img alt="" style="border: 0" src="<?php echo STATIC_CONTENT_URL_SSL. 'android_app_image.png'; ?>" width="87" height="30">
                          </a>
                          <a href="https://apps.apple.com/us/app/wholesalebox/id1254820324" style="display: inline-block; outline: none; margin-right: 10px" target="_blank">
                          <img style="border: 0" src="<?php echo STATIC_CONTENT_URL_SSL. 'ios_download.png'; ?>" width="87" height="30">
                          </a>
                        </td>
                    </tr> 
                   </tbody>
                  </table> </td> 
                </tr> 
                
               </tbody>
              </table> 
               </td> 
            </tr> 
           </tbody>
          </table> </td> 
        </tr> 
       </tbody>
      </table> </td> 
    </tr> 
   </tbody>
  </table>