<?php
/**
 *	OperationsFactory
 *
 *  @info in OperationsFactory every time getinstance is call and in getinstance method recive $data.
 *  here $data has three keys $data['method'], $data['class'], $data['params'].
 *  $data['method'] contain method name , $data['class'] contain class name and $data['parms'] contain data for the calculations.
 *
 * 	@author @Garvit Joshi
 */
class OperationsFactory 
{

	public function __construct($registry) {

	}

	/**
	 *	getInstance
	 *
	 *	@info Here $data is recive as an Array. 
	 *	First $data array key is $data['method']. $data['method'] contain the which type of data is this. if the date is related to accounts then in $data['method'] we get Accounts eg. $data['method'] = 'accounts'; $data['method'] like Accounts, PaymentGateWay, And Order.
	 * 	Second array key is $data['class']. $data['class'] contain class name which is usefull for calling class an eg. if we want to call generatePaymentLink in citrus then we need to pass citrus eg. $data['class'] = 'citrus';
	 *	Third array key is $data['params']. $data['params'] contain data for the calculations 
	 *
	 *	Note: inside getInstance call new method according to method and class name which is present in $data.
	 *
	 *	@param 	array $data-> Array{$data['method'], $data['class'], $data['params']}
	 *	@return objects of the class which we want to call. 
	 */
	protected function getInstance($data) {
		if (!empty($data['method'])) { 
			$method_name = 'get'.ucfirst($data['method']).'Instance('.$data.')';
			if(function_exists($method_name)) { // Here we can check that method is exit or not
            	return $this->$method_name;
            } else {
            	throw new Exception("Invalid Method Name in OperationsFactory->getInstance.");
            }
        } else {
            throw new Exception("Empity Method Name in OperationsFactory->getInstance.");
        }
	}

	/**
	 *	doAction
	 *	@info Here we recive two parameter i.e. $order_id, $data
	 *	$data is an array which contain old state and new state like $data{'old state'=>'new state'}
	 *  doAction can perform acion for a given order_id to change its old state to new state.
	 *	@param 	$order_id, $data-> Array{'Old State'=>'New State'}
	 *	@return new state of on order
	 */
	protected function doAction($order_id, $data) {
		
	}

	/**
	 *	getAccountsInstance
	 *	@info Here $data recive form getInstance method and this $data is related to Accounts.
	 *	With the help of $data['class'] we pass class name which we want to call.
	 *	$data['params'] for calculations 
	 *	@param 	$data-> Array{$data['method'], $data['class'], $data['params']}
	 *	@return accounts class object
	 */
	protected function getAccountsInstance($data) {
		$data_class =  $data['class'];
		if(!empty($data_class)) {

		} else {
			throw new Exception("Empity class name in OperationsFactory->getAccountsInstance.");
		}
		return new $data();
	}

	/**
	 *	getOrderInstance
	 *	@info Here $data is comes form getInstance method and this $data is related to Order.
	 *	With the help of $data['class'] we pass class name which we want to call.
	 *	@param 	$data-> Array{$data['method'], $data['class'], $data['params']}
	 *	@return order class object
	 */
	protected function getOrderInstance($data) {
		$data_class =  $data['class'];
		if(!empty($data_class)) {

		} else {
			throw new Exception("Empity class name in OperationsFactory->getOrderInstance.");
		}

		return new $data();
	}

	/**
	 *	getPaymentGateWayInstance
	 *	@info Here $data is comes form getInstance method and this data is related to PaymentGateWay.
	 *	$data['class'] contain class name which is easy to know that which type of payment is that for an eg. if we want to call generatePaymentLink in citrus then we need to pass citrus eg. $data['class'] = 'citrus';
	 *	@param 	$data-> Array{$data['method'], $data['class'], $data['params']}
	 *	@return paymentGateWay class object
	 */
	protected function getPaymentGateWayInstance($data) {
		$data_class =  $data['class'];
		if(!empty($data_class)) {

		} else {
			throw new Exception("Empity class name in OperationsFactory->getPaymentGateWayInstance.");
		}
		return new $data();
	}
}