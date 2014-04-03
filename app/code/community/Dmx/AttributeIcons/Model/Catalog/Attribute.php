<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * 
 *@method void setAttribute(Mage_Eav_Model_Entity_Atribute $attribute)
 *@method Mage_Eav_Model_Entity_Atribute getAttribute()
 */
class Dmx_AttributeIcons_Model_Catalog_Attribute extends Varien_Object{
	
	protected $_optionsData = array();
	
	public function setOptionsData($optionsData,$joinFiles = true){
		$this->_optionsData = $optionsData;
		if($joinFiles){
			$this->_optionsData = array_merge($this->_optionsData,$_FILES);
		}
	}
	public function save(){
		$attributeId = $this->_optionsData['attribute_id'];
		/* TODO: refactor getting attribute code */
		$attributeCode = Mage::getModel('catalog/entity_attribute')->load($attributeId)->getAttributeCode();
		
		foreach($this->_optionsData['options'] as $optionId){
			/* @var $dataModel Dmx_AttributeIcons_Model_Data */
			$dataModel = Mage::getModel('attributeicons/data')->loadByOption($attributeId,$optionId);
			
			$dataModel->setData('option_id',$optionId);
			$dataModel->setData('attribute_id',$attributeId);
			$dataModel->setData('attribute_code',$attributeCode);
			
			$dataModel->addFileToUpload('icon','icon['.$optionId.']');
			$dataModel->addFileToUpload('image','image['.$optionId.']');
			
			$dataModel->setData('description',$this->_optionsData['description'][$optionId]);
			if(isset($this->_optionsData['show_both'][$optionId])){
				$dataModel->setData('show_both',$this->_optionsData['show_both'][$optionId]);
			}else{
				$dataModel->setData('show_both',0);
			}
			
			if(isset($this->_optionsData['image'][$optionId]['delete'])){
				$dataModel->addFileToDelete('image', $this->_optionsData['image'][$optionId]['value']);
			}
			if(isset($this->_optionsData['icon'][$optionId]['delete'])){
				$dataModel->addFileToDelete('icon', $this->_optionsData['icon'][$optionId]['value']);
			}
			
			$dataModel->save();
		}
		
		$config = Mage::getModel('eav/config');
		$attribute = $config->getAttribute('catalog_product', $attributeId);
		$attribute->setIsHtmlAllowedOnFront(1);
		$attribute->save();
	}
}