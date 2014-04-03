<?php
class Insync_Supplier_Block_Supplier extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getSupplier()     
     { 
        if (!$this->hasData('supplier')) {
            $this->setData('supplier', Mage::registry('supplier'));
        }
        return $this->getData('supplier');
        
    }
}