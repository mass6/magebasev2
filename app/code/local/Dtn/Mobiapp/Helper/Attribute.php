<?php

class Dtn_Mobiapp_Helper_Attribute extends Mage_Core_Helper_Abstract
{	
	/**
	* TODO: optimize this function
	*/
    public function getCustomerApproverCategoryId() {
    	$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'cust_category');
		$options1 = array();
		$options2 = array();
		if ($attribute->usesSource()) {
			foreach ($attribute->getSource()->getAllOptions() as $optionValue) {
				foreach($optionValue as $key=>$value) {
					if($key == 'label') $options1[] = $value ; 
					if($key == 'value') $options2[] = $value ; 
				}
			}
		}
		$appId = '';
		for($i=0;$i<count($options1);$i++){
			if($options1[$i] == 'Approver')
				$appId = $options2[$i];
		}
		return $appId;
    }

    public function getApprovalLevelValues() {
    	$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'approval_level');
		$options3 = array();
		$options4 = array();
		if ($attribute->usesSource()) {
			foreach ($attribute->getSource()->getAllOptions() as $optionValue) {
				foreach($optionValue as $key=>$value) {
					if($key == 'label')$options3[] = $value ; 
					if($key == 'value')$options4[] = $value ; 
				}
			}
		}
		$applevel1 = '';
		$applevel2 = '';
		$applevel3 = '';
		$applevel4 = '';
		$applevel5 = '';
		for($i=0; $i < count($options3); $i++){
			if ($options3[$i] == 1) 
				$applevel1 = $options4[$i];
			else if($options3[$i] == 2)
				$applevel2 = $options4[$i];
			else if($options3[$i] == 3)
				$applevel3 = $options4[$i];
			else if($options3[$i] == 4)
				$applevel4 = $options4[$i];
			else if($options3[$i] == 5)
				$applevel5 = $options4[$i];
		}

		return array($applevel1, $applevel2, $applevel3, $applevel4, $applevel5);
    }

    public function getActiveApproverAttributeValue() {
    	$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'active_approver');
		$options5 = array();
		$options6 = array();
		if ($attribute->usesSource()) {
			foreach ($attribute->getSource()->getAllOptions() as $optionValue) {
				foreach($optionValue as $key=>$value){
					if($key == 'label')$options5[] = $value ; 
					if($key == 'value')$options6[] = $value ; 
				}
			}
		}

		$appA = '';

		for($i=0;$i<count($options5);$i++){
			if($options5[$i] == 'Yes'){
				$appA = $options6[$i];
			}
		}
		
		return $appA;
    }
}