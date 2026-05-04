 <?php
//File Created By Nishu, Dated 5th Sept 2018

/**
 * @info: Define All ReturnReasonId keys As constants To use everywhere
 *      
 *   Note: IF any ReturnReason is added, key must be added in constant, 
 *            
 * @author: Nishu, Sept 2018
*/

//All Return Reasons Exist
define(
	'RETURN_REASON_IDS', array(
				"Manufacturing_Defect"                    => 1,
                        "Quality_Issue"                           => 2,
                        "Pricing_Issue"                           => 3,
                        "Wrong_Item_Received"                     => 4,
                        "Order_Error_By_Customer"                 => 5,
                        "Other_Please_Supply_Details"             => 6,
                        "COD_Failed"                              => 7,
                        "Canceled_Order"                          => 8,
                        "Rejected_Wrong_Product"                  => 9,
                        "Rejected_Seller_Damage"                  => 10,
                        "Loss_By_WSB"                             => 11,
                        "Damaged_By_Courier_Company"              => 12,
                        "Wrong_Item_Received_Replacement"         => 13,
                        "Transfer_To_WSB_Books"                   => 14,
                        "Cancelled_By_Customer"                   => 15,
                        "Other_Please_Supply_Details_Replacement" => 16,
                        "Damaged_By_Courier_During_Pickup"        => 17,
                        "sor_return_under_buyback"                => 18
                      )
);
