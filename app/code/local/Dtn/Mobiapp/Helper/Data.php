<?php

class Dtn_Mobiapp_Helper_Data extends Mage_Core_Helper_Abstract
{	
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    public function getCustomerId(){
		$session = $this->_getSession();
		$data = $session->getCustomer();
		$id = $data->getEntityId();
		return $id;
	}
	public function getKey()
    {
		$id = $this->getCustomerId();
		if(!$id) return false;
		$get = 'getCustomer'.$id;
		$sessionKey = Mage::getSingleton('core/session')->$get();			
		return $sessionKey;
	}
}