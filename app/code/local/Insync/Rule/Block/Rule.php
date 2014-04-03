<?php
class Insync_Rule_Block_Rule extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getRule()     
     { 
        if (!$this->hasData('rule')) {
            $this->setData('rule', Mage::registry('rule'));
        }
        return $this->getData('rule');
        
    }
}