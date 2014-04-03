<?php

class Insync_Rule_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        
        $this->_init('rule/rule', 'rule_id');
    }
}