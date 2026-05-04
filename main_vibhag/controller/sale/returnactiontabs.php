<?php

class ControllerSaleReturnactiontabs extends Controller {

	public function __construct() {
		/* Do not remove this method from class */
	}

	public static function tabAll_return() {
		/*  return pending status returns*/
		return array(
				RETURN_ACTION_IDS['Pending'],
				RETURN_ACTION_IDS['Return_Request_Accepted'],
				RETURN_ACTION_IDS['Replacement_Request_Accepted'],
				RETURN_ACTION_IDS['Self_Shipment'],
				RETURN_ACTION_IDS['Reverse_Shipment_Generated'],
				RETURN_ACTION_IDS['Return_Goods_Rejected'],
				RETURN_ACTION_IDS['Goods_Received'],
				RETURN_ACTION_IDS['Extra_Goods_Received'],
				RETURN_ACTION_IDS['Short_Goods_Received'],
				RETURN_ACTION_IDS['Goods_Picked_Up'],
				RETURN_ACTION_IDS['Shipment_Lost_by_Courier'],
				RETURN_ACTION_IDS['Shipment_Lost_by_Courier_While_Resending_Back'],
				RETURN_ACTION_IDS['DN_Generate_for_Logistic_Company'],
				RETURN_ACTION_IDS['CN_For_Client'],
				RETURN_ACTION_IDS['Refunded_Initiated'],
				RETURN_ACTION_IDS['DN_Generated_For_Seller'],
				RETURN_ACTION_IDS['Cancel_CN'],
				RETURN_ACTION_IDS['Goods_Given_To_Pickup_Boy'],
				RETURN_ACTION_IDS['Goods_Handed_Over_To_Seller'],
				RETURN_ACTION_IDS['DN_Cancelled'],
				RETURN_ACTION_IDS['Goods_Damaged_By_Pickup_During_Return'],
				RETURN_ACTION_IDS['Seller_Accepts_Return'],
				RETURN_ACTION_IDS['Seller_Rejected_Accepting_Returns'],
				RETURN_ACTION_IDS['Issue_Resolved_With_Seller'],
				RETURN_ACTION_IDS['Goods_Hold_WSB_Wait_Customer_Pickup'],
				RETURN_ACTION_IDS['Shipment_Back_To_Customer'],
				RETURN_ACTION_IDS['Goods_To_Seller_Waiting_For_Replaement'],
				RETURN_ACTION_IDS['Replacement_Not_Available'],
				RETURN_ACTION_IDS['Custom_DN_Cancelled'],
				RETURN_ACTION_IDS['Customer_Picked_Items'],
				RETURN_ACTION_IDS['Replacement_Given_By_Seller'],
				RETURN_ACTION_IDS['Wrong_Item_Received_Due_To_Logistic_Fault'],
				RETURN_ACTION_IDS['Manually_Generated_Reverse_Shipment'],
				RETURN_ACTION_IDS['Action_Cancel_Reverse_Shipment'],
				RETURN_ACTION_IDS['Replacement_Note']
				);
	}

	/**
	 * @info : Public function to show return with specific return_actions in WSB GOODS Tab
	 *          Like :  Loss_Booked_By_WSB,
	 *					Goods_Taken_On_WSB_Books_And_Relisted,
	 *					ActionTransferToWSBBooksWithoutRelisting
	 * @author: Nishu, July 2018
	*/
	public static function tabWSB_Goods() {
		return WSB_LOSS_N_BOOKS_ACTION_IDS;
	}

	/**
	 * @info: Public function to Show ReturnCompleted Actions tab in return panel
	 * @author: Nishu, July 2018
	*/
	public static function tabReturn_Closed() {
		return CLOSED_ACTION_IDS;
	}

}