<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/List/Reminder.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ kgDZjeyDmhieiRUE('263b9268b89949330ad1441c4edb2160'); ?><?php
class Aitoc_Aitproductslists_Model_List_Reminder extends Aitoc_Aitproductslists_Model_List_Abstract
{
    protected function _construct()
    {
        $this->_init('aitproductslists/list_reminder');
    }
    
    public function getShedule()
    {
        return Mage::getModel('aitproductslists/list_reminder_shedule');
    }
} } 