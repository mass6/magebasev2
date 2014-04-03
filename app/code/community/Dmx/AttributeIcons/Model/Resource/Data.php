<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Dmx_AttributeIcons_Model_Resource_Data extends Mage_Core_Model_Resource_Db_Abstract{
	
	protected function _construct(){
		$this->_init('attributeicons/data', 'id');
	}
	
	public function loadByOption($object,$attributeId,$optionId){
		$read = $this->_getReadAdapter();
		$select = $read->select()->from($this->getMainTable());
		$select->where('attribute_id = ?',$attributeId)->where('option_id = ?',$optionId);
		$data = $read->fetchRow($select);

		if($data){
			$object->setData($data);
		}
	}
}