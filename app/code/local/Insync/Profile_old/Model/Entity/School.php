<?php
class Insync_Profile_Model_Entity_School extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	public function getAllOptions()
	{
		
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql = "SELECT  `name` ,  `rule_id` 
FROM  `salesrule` ";
		$temp = $read->fetchAll($sql);
		$tempName = array();
		$tempId = array();
		foreach ($temp as $key) {
			foreach ($key as $key1 => $value) {
				if ($key1 == 'name') {
					$tempName[] = $value;
				}
				if ($key1 == 'rule_id') {
					$tempId[] = $value;
				}
			}
		}
		if ($this->_options === null) {
			$this->_options = array();
			// Mage::log('count($tempId)============');
			// Mage::log(count($tempId));
			$this->_options[] = array(
                    'value' => 0,
                    'label' => 'choose option'
			);
			for($i=0;$i<count($tempId); $i++){
			$this->_options[] = array(
                    'value' => $tempId[$i],
                    'label' => $tempName[$i]
			);
			}
			
		}

		return $this->_options;
	}
}