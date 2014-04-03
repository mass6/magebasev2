<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Dmx_AttributeIcons_Model_Resource_Catalog_Attribute_Collection extends Mage_Catalog_Model_Resource_Product_Attribute_Collection{
	
	public function addFrontendInputTypeFilter($frontendInput){
		if(!is_array($frontendInput)){
			$frontendInput = array($frontendInput);
		}	
		$frontendInput = implode('\',\'',$frontendInput);
		$this->getSelect()->where('frontend_input IN (\''.$frontendInput.'\')');
		
		return $this;
	}
}