<?php

class Insync_Web_Model_Mysql4_Web extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        
        $this->_init('web/web', 'web_id');
    }
}