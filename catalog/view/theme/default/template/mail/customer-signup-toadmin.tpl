<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>WholesaleBox</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
<div style="width: 680px;">

    <h3><?php echo $text_signup; ?></h3>
    
    <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
       <?php if ($website) { ?>
       <tr>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $text_website; ?> </td>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px; color:blue;"><b><?php echo $website; ?></b></td>
       </tr>
       <?php } ?>
       
       <tr>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $text_name; ?> </td>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px; color:blue;"><b><?php echo $name; ?></b></td>
       </tr>
       
       <!-- <?php if ($lastname) { ?>
       <tr>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $text_lastname; ?> </td>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px; color:blue;"><b><?php echo $lastname; ?></b></td>
       </tr>
       <?php } ?> -->
       
       <tr>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $text_email; ?> </td>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px; color:blue;"><b><?php echo $email; ?></b></td>
       </tr>
       
       <tr>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $text_mobile; ?> </td>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px; color:blue;"><b><?php echo $mobile; ?></b></td>
       </tr>
       
       <tr>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $text_whatsapp; ?> </td>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px; color:blue;"><b><?php echo $whatsapp; ?></b></td>
       </tr>
       
       <tr>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $text_business_name; ?> </td>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px; color:blue;"><b><?php echo $business_name; ?></b></td>
       </tr>
       
       <tr>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $text_tinno; ?> </td>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px; color:blue;"><b><?php echo $tinno; ?></b></td>
       </tr>
       
       <tr>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $text_address; ?> </td>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px; color:blue;"><b><?php echo $address; ?></b></td>
       </tr>
       
       <tr>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $text_city; ?> </td>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px; color:blue;"><b><?php echo $city; ?></b></td>
       </tr>
       
       <tr>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $text_state; ?> </td>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px; color:blue;"><b><?php echo $state; ?></b></td>
       </tr>
       
       <tr>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $text_postcode; ?> </td>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px; color:blue;"><b><?php echo $postcode; ?></b></td>
       </tr>
       
       <tr>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $text_landmark; ?> </td>
       <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px; color:blue;"><b><?php echo $landmark; ?></b></td>
       </tr>
       
     </table>
       
</div>
</body>
</html>
