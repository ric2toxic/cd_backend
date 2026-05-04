 <?php
//File Created By MSA, Dated 17th july 2018

/**
 * @info: Define customer SMS template for all return actions
 *      
 *   Note: SMS template will get by return action ids defined in 
 * 			return action clusters - RETURN_ACTION_IDS
 *
 * Author: MSA, July 2018
*/

define('RETURN_CUSTOMER_SMS_TEMPLATE',array(

RETURN_ACTION_IDS['Pending']	=> "Your return/replacement request has been Received for Order No %s. We will review and update you soon. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Return_Request_Accepted']	=> "Your return request has been Accepted for Order No %s. Keep %s pieces ready. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Return_Request_Rejected']	=> "Your return request for %s pieces has been Rejected for Order No %s. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Replacement_Request_Accepted']	=> "Your replacement request has been Accepted for Order No %s. Keep %s pieces ready. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Replacement_Request_Rejected']	=> "Your replacement request for %s pieces has been Rejected for Order No %s. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Self_Shipment']	=> "You have chosen Self Shipment for Order No %s. Please send %s pieces to %s%s and upload courier receipt in WholesaleBox website/app, within 3 days. Please ship soon, else request will be Canceled. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Goods_Picked_Up']	=> "We have picked goods as per your request for Order No %s, against Tracking No %s. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Goods_Received']	=> "We have received goods for Order No %s. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Extra_Goods_Received']	=> "We have received extra goods, compared to your original request for Order No %s. Your request may be Rejected. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Short_Goods_Received']	=> "We have received less goods, compared to your original request for Order No %s. Your request may be Rejected. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Return_Goods_Rejected']	=> "Your return request for %s pieces has been Rejected for Order No %s. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['CN_For_Client']	=> "Credit Note %s of amount %.2f, has been generated, against your return request for Order No %s. Refund shall be initiated soon. Please update your Bank Account Details, if any changes. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Shipment_Back_To_Customer']	=> "We have shipped back %s pieces for your Order No %s. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Cancel_CN']	=> "Credit Note %s of amount %.2f, has been Canceled, against your return request for Order No %s. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Goods_Hold_WSB_Wait_Customer_Pickup']	=> "Please pickup goods for Order No %s, from %s%s. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Replacement_Not_Available']	=> "Replacement of %s pieces, is not available for your Order No %s. Credit Note and Refund will be initiated soon. Check %s. For assistance, call %s",

RETURN_ACTION_IDS['Reverse_Shipment_Generated']	=> "Reverse Shipment against Order No %s has been generated. Our courier partner (%s), shall come for pickup soon. Keep %s pieces ready. Check %s. Call %s, for help",

));
