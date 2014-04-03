<?php

class Insync_Supplier_Model_Mysql4_Supplier extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        
        $this->_init('supplier/supplier', 'id');
    }
}