<?php

class Insync_Custom_Helper_Data extends Mage_Core_Helper_Abstract
{
	 public function getValue($orderId){
		
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql = sprintf("SELECT `value` 
		FROM `sales_order_custom` 
		WHERE `order_id` = '%d'", $orderId);
		$id = $read->fetchAll($sql);
		
		$tempid = '';
		foreach ($id as $key) {
			foreach ($key as $key1 => $value) {
				if ($key1 == 'value') {
					$tempid = $value;
				}
			}
		}
		
		return $tempid;
	 }

}