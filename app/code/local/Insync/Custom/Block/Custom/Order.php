<?php
class Insync_Custom_Block_Custom_Order extends Mage_Core_Block_Template{
	public function getCustomVars(){
		Mage::log('cxsxs');
		$id=$this->getOrder()->getId();
		Mage::log('osdc');
		Mage::log($id);
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql = sprintf("SELECT `contract_id` 
		FROM `sales_flat_order` 
		WHERE `entity_id` = '%d'", $id);
		$contract = $read->fetchAll($sql);
		Mage::log($contract);
		$tempid = '';
		foreach ($contract as $key) {
			foreach ($key as $key1 => $value) {
				if ($key1 == 'contract_id') {
					$tempid = $value;
				}
			}
		}
		Mage::log($tempid);
		$contractDetails = Mage::getModel('web/web')->getCollection()->addFieldToFilter('web_id', $tempid);
	$contractCode = '';
	foreach ($contractDetails as $each) {
	$contractCode = $each['name'];
	}
		
		return $contractCode;
	}
	public function getOrder()
	{
		return Mage::registry('current_order');
	}
}