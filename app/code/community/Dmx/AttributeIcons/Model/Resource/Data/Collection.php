<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Dmx_AttributeIcons_Model_Resource_Data_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract{
	
	protected function _construct(){
		$this->_init('attributeicons/data');
	}
	/**
	 * 
	 * @param int $attributeId
	 */
	public function setAttributeFilter($attributeId){
		$this->addFieldToFilter('attribute_id',array('eq'=>$attributeId));
		
		return $this;
	} 
	
	public function toArrayByOption($prepareForForm = false){
		$retVal = array();
		if($prepareForForm){
			foreach($this as $item){
				if($item->getIcon()){
					$retVal['icon['.$item->getOptionId().']'] = 'attributeicons/'.$item->getIcon();
				}
				if($item->getImage()){
					$retVal['image['.$item->getOptionId().']'] = 'attributeicons/'.$item->getImage();
				}
				$retVal['description['.$item->getOptionId().']'] = $item->getDescription();
				$retVal['show_both['.$item->getOptionId().']'] = $item->getShowBoth();
			}
		}else{
			foreach($this as $item){
				$retVal[$item->getOptionId()] = $item->getData();
			}
		}
		
		return $retVal;
	}
}