<?php

class Dmx_AttributeIcons_Block_Cart_Item_Renderer_Configurable extends Mage_Checkout_Block_Cart_Item_Renderer_Configurable{
	
	public function getFormatedOptionValue($optionValue)
	{
		/* @var $helper Mage_Catalog_Helper_Product_Configuration */
		$helper = Mage::helper('catalog/product_configuration');
		$params = array(
				#'max_length' => 55,
				#'cut_replacer' => ' <a href="#" class="dots" onclick="return false">...</a>'
		);
		return $helper->getFormattedOptionValue($optionValue, $params);
	}
}