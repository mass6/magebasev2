<?php

class Insync_Supplier_Model_Supplier extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('supplier/supplier');
    }
}