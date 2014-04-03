<?php
class Insync_Custom_Model_Sales_Order extends Mage_Sales_Model_Order{
	public function hasCustomFields(){
		$var = $this->getSsn();
		if($var && !empty($var)){
			return true;
		}else{
			return false;
		}
	}
	public function getFieldHtml(){
		$customerId=$this->getCustomerId();
		$customer= Mage::getModel('customer/customer')->load($customerId);
		$contractId = $customer->getContractId();
		
		$contractDetails = Mage::getModel('web/web')->getCollection()->addFieldToFilter('web_id', $contractId);
		$contractName='';
		foreach ($contractDetails as $each) {
			$contractName=$each['name'];
		}
		$html = '<b>RefId #: </b>'.$contractName.'<br/>';
		return $html;
	}
}