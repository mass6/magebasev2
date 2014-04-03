<?php


class Dmx_AttributeIcons_Helper_Output extends Mage_Core_Helper_Abstract{
	
	public function getAttributeIcon($product,$attributeCode,$attributeValue){
		$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product',$attributeCode);
		return $attribute->getFrontend()->getOption($attributeValue);
	}
}