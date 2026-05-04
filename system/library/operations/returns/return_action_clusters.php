 <?php
//File Created By Nishu, Dated 29th june 2018

/**
 * @info: Define All ReturnActionId keys As constants To use everywhere
 *      
 *   Note: IF any ReturnActionId is added in constant, 
 *            that return action id must be updated in clusters also accordingly
 *
 * Author: Nishu, June 2018
*/

define(
	'RETURN_ACTION_IDS', array(
						"Old_Pending"								=> 0,
						"Old_Return_Approved_Awaiting_Products"		=> 1,
						"Old_Returned_Goods_Received"				=> 2,
						"Old_Amount_Refunded"						=> 3,
						"Old_Credit_Issued"							=> 4,
						"Old_Replacement_Sent"						=> 5,
						"Old_Replacement_Reserved"					=> 6,
						"Old_Return_Request_Rejected"				=> 7,
						"Old_Returned_Goods_Rejected"				=> 8,
						"Old_Rejected_Returns_Reserved"				=> 9,
						"Old_Rejected_Returns_Resent"				=> 10,
						"Old_Cancelled"								=> 11,	
						"Old_Replacement_Not_Available"				=> 12,
						"Old_Good_To_Generate_Debit_Note"			=> 13,
						"Old_VAT_Purchase_Returned_Under_GST"		=> 14,
						"Old_Parcel_Lost_During_Return_By_Courier"	=> 15,
						
						"Pending"                                       => 101,
						"Return_Request_Accepted"                       => 102,
						"Replacement_Request_Accepted"                  => 103,
						"Return_Request_Rejected"                       => 104,
						"Self_Shipment"                                 => 105,
						"Reverse_Shipment_Generated"                    => 106,
						"Replacement_Request_Rejected"                  => 107,
						"Return_Goods_Rejected"                         => 108,
						"Goods_Received"                                => 109,
						"Extra_Goods_Received"                          => 110,
						"Short_Goods_Received"                          => 111,
						"Goods_Picked_Up"                               => 112,
						"Shipment_Lost_by_Courier"                      => 113,
						"DN_Generate_for_Logistic_Company"              => 114,
						"CN_For_Client"                                 => 115,
						"Refunded_Initiated"                            => 116,
						"DN_Generated_For_Seller"                       => 117,
						"Goods_Taken_On_WSB_Books_And_Relisted"         => 118,
						"Loss_Booked_By_WSB"                            => 119,
						"Cancel_CN"                                     => 120,
						"Goods_Given_To_Pickup_Boy"                     => 121,
						"Goods_Handed_Over_To_Seller"                   => 122,
						"DN_Cancelled"                                  => 123,
						"Goods_Damaged_By_Pickup_During_Return"         => 124,
						"Seller_Accepts_Return"                         => 125,
						"Seller_Rejected_Accepting_Returns"             => 126,
						"Issue_Resolved_With_Seller"                    => 127,
						"Replacement_Given_By_Seller"                   => 128,
						"Goods_Hold_WSB_Wait_Customer_Pickup"           => 129,
						"Shipment_Back_To_Customer"            		    => 130,
						"Customer_Picked_Items"                         => 131,
						"Goods_To_Seller_Waiting_For_Replaement"        => 132,
						"Replacement_Not_Available"                     => 133,
						"Return_Complete"                               => 134,
						"Cancelled_By_Customer"                         => 135,
						"Custom_DN_Cancelled"                           => 136,
						"Replacement_complete"                          => 137,
						"Shipment_Lost_by_Courier_While_Resending_Back" => 138,
						"Manually_Generated_Reverse_Shipment"           => 139,
						"Wrong_Item_Received_Due_To_Logistic_Fault"     => 140,
						"ActionTransferToWSBBooksWithoutRelisting"      => 141,
						"Action_Cancel_Reverse_Shipment"   				=> 142,
						"Replacement_Note"                       		=> 143
                      )
);

//WSB LOSS ReturnActionId(s) if multiple return actions are in this group, It should be updated
define('WSB_LOSS_RETURN_ACTION_IDS', '119');

//TRANSFER TO WSB BOOKS ReturnActionId(s) if multiple return actions are in this group, It should be updated
define('TRANSFER_TO_WSB_BOOKS_RETURN_ACTION_IDS', '118');



//-------Return---Cluserts---- For SellerPanel, Defined By Nishu June 2018

//Tentative Returns For Seller Panel
define('SELLER_PANEL_TENTATIVE_RETURNS', array(
												RETURN_ACTION_IDS['Pending']
											)										
	   );

//Approved Returns For Seller Panel
define('SELLER_PANEL_APPROVED_RETURNS', array(
												RETURN_ACTION_IDS['Return_Request_Accepted'],
												RETURN_ACTION_IDS['Self_Shipment'],
												RETURN_ACTION_IDS['Reverse_Shipment_Generated'],
												RETURN_ACTION_IDS['Goods_Received'],
												RETURN_ACTION_IDS['Extra_Goods_Received'],
												RETURN_ACTION_IDS['Short_Goods_Received'],
												RETURN_ACTION_IDS['Goods_Picked_Up'],
												RETURN_ACTION_IDS['CN_For_Client'],
												RETURN_ACTION_IDS['Refunded_Initiated'],
												RETURN_ACTION_IDS['DN_Generated_For_Seller'],
												RETURN_ACTION_IDS['Cancel_CN'],
												RETURN_ACTION_IDS['Goods_Given_To_Pickup_Boy'],
												RETURN_ACTION_IDS['DN_Cancelled'],
												RETURN_ACTION_IDS['Manually_Generated_Reverse_Shipment'],
												RETURN_ACTION_IDS['Wrong_Item_Received_Due_To_Logistic_Fault'],
												RETURN_ACTION_IDS['Action_Cancel_Reverse_Shipment']
											)
);

//Completed/Delivered Returns For Seller Panel
/*
SELLER_PANEL_DELIVERED_RETURNS key is not define(code commented), 
because Some case can not be handled through this key only- So this directly handleing by Query
Case Like: In replacement request, if replacement is not available,
then that replacement case will be shown in returns section (Still return_reason_tyep is REPLACEMENT)

define('SELLER_PANEL_DELIVERED_RETURNS' => array(
													RETURN_ACTION_IDS['Goods_Handed_Over_To_Seller'],
													RETURN_ACTION_IDS['Seller_Accepts_Return'],
													RETURN_ACTION_IDS['Issue_Resolved_With_Seller'],
													RETURN_ACTION_IDS['Return_Complete']
												)
);
*/

//Disputed Returns For Seller Panel
define('SELLER_PANEL_DISPUTED_RETURNS', array(
													RETURN_ACTION_IDS['Seller_Rejected_Accepting_Returns']
												)
);


//-------Replacement---Cluserts---- For SellerPanel, Defined By Nishu June 2018

//Tentative Replacement For Seller Panel
define('SELLER_PANEL_TENTATIVE_REPLACEMENTS', array(
													RETURN_ACTION_IDS['Pending']
												)
);

//Approved Replacement For Seller Panel
define('SELLER_PANEL_APPROVED_REPLACEMENTS', array(
													RETURN_ACTION_IDS['Replacement_Request_Accepted'],
													RETURN_ACTION_IDS['Self_Shipment'],
													RETURN_ACTION_IDS['Reverse_Shipment_Generated'],
													RETURN_ACTION_IDS['Goods_Received'],
													RETURN_ACTION_IDS['Extra_Goods_Received'],
													RETURN_ACTION_IDS['Short_Goods_Received'],
													RETURN_ACTION_IDS['Goods_Picked_Up'],
													RETURN_ACTION_IDS['Goods_Given_To_Pickup_Boy'],
													RETURN_ACTION_IDS['Manually_Generated_Reverse_Shipment'],
													RETURN_ACTION_IDS['Issue_Resolved_With_Seller'],
													RETURN_ACTION_IDS['Goods_Handed_Over_To_Seller'],
													RETURN_ACTION_IDS['Goods_To_Seller_Waiting_For_Replaement'],
													RETURN_ACTION_IDS['Wrong_Item_Received_Due_To_Logistic_Fault'],
													RETURN_ACTION_IDS['Action_Cancel_Reverse_Shipment']
												)
);

//Completed/Delivered Replacement For Seller Panel
/*
SELLER_PANEL_DELIVERED_REPLACEMENTS : Must check return_reason_type = 'REPLCAMENT' and DN must not be generated
*/
define('SELLER_PANEL_DELIVERED_REPLACEMENTS', array(
													RETURN_ACTION_IDS['Return_Complete'],
													RETURN_ACTION_IDS['Replacement_complete'],
													RETURN_ACTION_IDS['Customer_Picked_Items'],
													RETURN_ACTION_IDS['Shipment_Back_To_Customer'],
													RETURN_ACTION_IDS['Replacement_Given_By_Seller']
                                                  )
);


//Disputed Replacement For Seller Panel
define('SELLER_PANEL_DISPUTED_REPLACEMENTS', array(
													RETURN_ACTION_IDS['Seller_Rejected_Accepting_Returns']
												)
);



//----Return/Replacement Action Ids marked as closed 
define('CLOSED_ACTION_IDS', array(
							RETURN_ACTION_IDS['Goods_Taken_On_WSB_Books_And_Relisted'],
                            RETURN_ACTION_IDS['ActionTransferToWSBBooksWithoutRelisting'],
                            RETURN_ACTION_IDS['Loss_Booked_By_WSB'],
                            RETURN_ACTION_IDS['Return_Complete'],
                            RETURN_ACTION_IDS['Replacement_complete'],
                            RETURN_ACTION_IDS['Cancelled_By_Customer'],
                            RETURN_ACTION_IDS['Return_Request_Rejected'],
                            RETURN_ACTION_IDS['Replacement_Request_Rejected'],
                            RETURN_ACTION_IDS['Old_VAT_Purchase_Returned_Under_GST'],
                            RETURN_ACTION_IDS['Old_Cancelled']
						)
);

//----Return/Replacement Action Ids marked as WSB books and Loss
define('WSB_LOSS_N_BOOKS_ACTION_IDS', array(
							RETURN_ACTION_IDS['Loss_Booked_By_WSB'],
							RETURN_ACTION_IDS['Goods_Taken_On_WSB_Books_And_Relisted'],
							RETURN_ACTION_IDS['ActionTransferToWSBBooksWithoutRelisting']
						)
);