<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Mysql4/List/Reminder.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ MjrkaZgrBoqZqfQO('3e3ae9fbc844e7987d8967665992f3d7'); ?><?php
class Aitoc_Aitproductslists_Model_Mysql4_List_Reminder extends Aitoc_Aitproductslists_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('aitproductslists/list_reminder', 'id');
        
        $this->_dateFields = array(
            'start_date'
        );
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->getStatus())
        {
            $object->getShedule()->setReminder($object)->create();
        }
        parent::_beforeSave($object);
    }
} } 