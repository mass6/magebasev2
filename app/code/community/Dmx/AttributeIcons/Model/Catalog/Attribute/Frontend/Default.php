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
 * @author dimonixx
 *
 */
class Dmx_AttributeIcons_Model_Catalog_Attribute_Frontend_Default extends Mage_Eav_Model_Entity_Attribute_Frontend_Default{
	
	public function getValue(Varien_Object $object){
		return parent::getValue($object);
	}
	
	public function getOption($optionId){
		$attributeId = $this->getAttribute()->getAttributeId();
		
		$optionIds = explode(',',$optionId);
		
		$renderer = Mage::getSingleton('core/layout')->createBlock('attributeicons/catalog_attribute_renderer_default','attribute_icons.renderer',array('attribute_code'=>$this->getAttribute()->getAttributeCode()));
		$renderer->setAttributeCode($this->getAttribute()->getAttributeCode());
		
		foreach($optionIds as $optionId){
			$data = Mage::getModel('attributeicons/data')->loadByOption($attributeId,$optionId);
			
			$iconData = $data->getData();
			$iconData['original_value'] = $this->getAttribute()->getSource()->getOptionText($optionId);
				
			$renderer->addContent($iconData);
		}
		
		return $renderer->toHtml();
		
	}
	
	public function getSelectOptions(){
        $options = parent::getSelectOptions();
        
        if(Mage::helper('attributeicons')->hasIcons($this->getAttribute()->getAttributeId())){
        	foreach($options as $idx => $option){
        		$options[$idx]['label'] = $this->getOption($option['value']);
        	}
        }
        
        return $options;
    }
}