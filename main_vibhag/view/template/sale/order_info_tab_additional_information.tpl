<div id="additional_information">
  <table class="table table-bordered">
    <tr>
      <td><b><?php echo $text_store_name; ?></b></td>
      <td><?php echo $store_name; ?></td>
    </tr>
    <tr>  
      <td><b><?php echo $text_store_url; ?></b></td>
      <td><a href="<?php echo $store_url; ?>" target="_blank"><?php echo $store_url; ?></a></td>
    </tr>
    <tr>
      <td><b><?php echo $text_order_from; ?></b></td>
      <td><?php echo $order_from; ?></td>
    </tr>
    <?php if ($ip) { ?>
      <tr>
        <td><b><?php echo $text_ip; ?></b></td>
        <td><?php echo $ip; ?></td>
      </tr>  
    <?php } ?>
  <?php if ($forwarded_ip) { ?>
    <tr>
      <td><b><?php echo $text_forwarded_ip; ?></b></td>
      <td colspan="3"><?php echo $forwarded_ip; ?></td>
    </tr>  
  <?php } ?>
  <?php if ($user_agent) { ?>
    <tr>
      <td><b><?php echo $text_user_agent; ?></b></td>
      <td colspan="3"><?php echo $user_agent; ?></td>
    </tr>
  <?php } ?>
  <?php if ($accept_language) { ?>
    <tr>
      <td><b><?php echo $text_accept_language; ?></b></td>
      <td colspan="3"><?php echo $accept_language; ?></td>
    </tr>
    <?php } ?>
  </table>
</div>

<div class="courier_additional_remarks">
  <fieldset id="courier_additional_remarks">
    <legend><?php echo $text_paperwork_additional_notes; ?></legend>
    <span>
       <p>Intra India movements of the commodities in exemption list of VAT & Entry Tax regulations of the destination state are not subject to any VAT form under B2C & B2B Movements.
       <p> TIN number of shipper & Consignee on shipping invoice in case of B2B and of shipper in case of B2C is mandatory
      <p>*Entry permit for ecomm to Odisha, WB & Assam is exempted subject to Entry Tax . E comm shippers should be profiled before startup. FedEx readiness for Odisha & Assam is under review.
      <p>** Self certified copy of Registration certificate of consignee is required to move the shipment to Arunachal Pradesh
      <p>*** LBT registration of ecomm shippers moving COD shipments to LBT bound cities into Maharashtra is mandatory. LBT # should be mentioned on invoice. Consignee LBT # is must for normal B2B movements to LBT zones VAT Form wherever applicable may be for select goods or select category of dealers. Pls visit the destination state VAT website for further detail
      <p> Declaration from consignee may be required in case of B2C / C2C movements as per the destination state requirement Shipment to Arunachal Pradesh as gift or sample declared on Invoice upto the INR value 10,000 is allowed entry witilout Entry Tax. VAT form DG01 is filled up and signed by the carrier registered with VAT authority.
      <p> Entry Tax on B2C shipments to Odisha & WB is applicable for online/e-comm movements only, VAT form is applicable for other B2C, B2B & C2C movements Shipments can move to Tripura (AAG )by air without VAT Form and will be delivered after endorsement of VAT form from VAT authority by the consignee.
      <p> Entry Tax is charged on selected commodity as listed under the destination state VAT website. Pls log on the respective state website or FedEx regulatory link before pickup under B2C movement
      <p> Mis-declaration may result in fine/ penalty / seizure of goods Transit pass is granted for Assam if the entry permits of the importing Statesare accompanied with shipments of NE regions. 
      <p> Regulatory requirement are same for surface and air mode unless specified otherwise in the destination state VAT regulations. 
      <p> In most state VAT reaulations w.r.t. to e-commerce and personal shipments (B2C & C2C) are not clearly defined, therefore state boarder clearance is subject to the discretion of the concerned check post Vat officers. 
      <p>Threshold for Bihar& UP is under review by the respective VAT authorities. Threshold is suspended for these states until we have clear verdict from both the authorities. Status quo on exemption limit for B2B to Bihar & UP. This document supersedes all documents posted or shared in past.
    </span>
  </fieldset>
</div>

