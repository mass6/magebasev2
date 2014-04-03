<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Dmx_AttributeIcons_Model_Catalog_Attribute_Source_Default extends Mage_Eav_Model_Entity_Attribute_Source_Table{
	
	/*public function getOptionText($value){
		
        $isMultiple = false;
        if (strpos($value, ',')) {
            $isMultiple = true;
            $value = explode(',', $value);
        }

        $options = $this->getAllOptions(false);
		
        if ($isMultiple) {
            $values = array();
            
	                if(Mage::helper('attributeicons')->hasIcons($this->getAttribute()->getAttributeId())){
	                	$renderer = Mage::getSingleton('core/layout')->createBlock('attributeicons/catalog_attribute_renderer_default');
	                	foreach ($options as $item) {
                			if (in_array($item['value'], $value)) {
			        			$data = Mage::getModel('attributeicons/data')->loadByOption($this->getAttribute()->getAttributeId(),$item['value']);
			        			$renderer->addContent($data->getData());
                			}
	                	}
	                	$values = $renderer->toHtml();
			        }else{
			        	foreach ($options as $item) {
                			if (in_array($item['value'], $value)) {
                				$values[] = $item['label'];
                			}
			        	}
			        }
            return $values;
        }

        foreach ($options as $item) {
            if ($item['value'] == $value) {
	            if(Mage::helper('attributeicons')->hasIcons($this->getAttribute()->getAttributeId())){
	            	$renderer = Mage::getSingleton('core/layout')->createBlock('attributeicons/catalog_attribute_renderer_default');
		        	$data = Mage::getModel('attributeicons/data')->loadByOption($this->getAttribute()->getAttributeId(),$value);
		        	$renderer->addContent($data->getData());
		        	return $renderer->toHtml();
		        }else{
		        	return $item['label'];
		        }
            }
        }
        return false;
    }*/
}