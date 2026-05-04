<div>
    <p>Greetings Sir/Ma'am,</p><br />
    <p>We are glad to inform you that your Credit Limit of  Rs.<?php echo number_format($limit);?>/- is approved for the purpose of buying the goods From Wholesalebox Internet PVT LTD subject to the to the agreement as attached.This limit has been approved by <b>SRNG Finance Pvt Ltd (WSB Channel Partner)</b>.</p><br>

    <?php
     if(is_array($data['limit_break']))
     {
     ?>
     <p><b>Credit Monthly Breakup</b></p><br />
     <table border="1" width="400" cellpadding="5" cellspacing="0">
     <tr><th>Month</th><th>Limit</th></tr>
     <?php $i=0; foreach($data['limit_break']['limit'] as $limit_break) { ?>
      
      <tr>
      	<td align="center"><?php echo $data['limit_break']['month_list'][$i]; ?></td>
      	<td align="center"><?php echo $limit_break; ?></td>
      </tr>

     <?php $i++; } ?>
     </table>
   <?php } ?>  

    <p>
    	<b>PLEASE PRINTOUT THESE FORM IN ACTUAL SIZE</b>. This option is available in the print options. Screenshot attached for your reference.<br />
		Kindly send us Physical original documents as below:<br />
		1. Pay Later agreement along with Sanction Terms (Schedule-1).<br />
		2. NACH FORM<br />
		3. PAN (Self Attested)<br />
		4. ADHAR (Self Attested)<br />
		6. One passport size photo
		</p><br />

		<p>send us the images of all the above mentioned documents on whatsapp at +91 8239778680 before sending us all required documents through courier.</p><br />

		<p>For any clarification, if needed, you may reach us at 8239778680.</p><br /><br />

    <b>WHOLESALEBOX INTERNET PVT LTD,</b> <br/>
	<b>CREDIT-DIVISION</b><br />
	<b>B-22, Crystal Mall, Banipark,</b><br />
	<b>Jaipur-302016, Rajasthan, India</b><br />
	<b>+91 8239778680</b><br /><br />

	<b style="color: rgb(153,0,0);">**Kindly dispatch the documents in 24 hrs and get RS.100/- Coupon in first credit order.**</b><br /><br />

    Best Regards,<br />
    WSB Credit Division<br />
    <a target="_blank" href="mailto:credit@wholesalebox.in">credit@wholesalebox.in</a>| +91-8239778680
</div>