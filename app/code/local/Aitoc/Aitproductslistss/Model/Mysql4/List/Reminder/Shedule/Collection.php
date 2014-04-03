<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Mysql4/List/Reminder/Shedule/Collection.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ MjrkaZgrBoqZqfQO('1ffea5fe6778d0bcb60c720960e2347d'); ?><?php
class Aitoc_Aitproductslists_Model_Mysql4_List_Reminder_Shedule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('aitproductslists/list_reminder_shedule');
    }
    
    public function loadByList($listId)
    {
        $this->getSelect()->where('list_id=?',$listId);
      //  echo $this->getSelect()->__toString(); exit;
        return $this;
    }
    
    public function readyToSendToday()
    {
        $dateToday = date(now());
        $this->getSelect()
            ->where("start_date < ?",$dateToday)
            ->where("status = ?",Aitoc_Aitproductslists_Model_List_Reminder_Shedule::AITPRODUCTSLISTS_REMINDER_STATUS_READY);
        return $this;
    }
} } 