<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Mysql4/List.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ kgDZjeyDmhieiRUE('f9dbb796c016d677fbab254c210c703d'); ?><?php
class Aitoc_Aitproductslists_Model_Mysql4_List extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('aitproductslists/list', 'id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $dateFields = array(
            'reminder_start_date',
            'special_price_from_date',
            'special_price_to_date'
        );
        
        $time = now();
        if(!$object->getId())
        {
            $object->setData('create_date', $time);
        }
        $object->setData('update_date', $time);
        
        foreach($dateFields as $dateField)
        {
            $this->_prepareDateFields($object, $dateField);
        }
        
        return parent::_beforeSave($object);
    }
    
    protected function _prepareDateFields($object, $fieldName)
    {
        $date = $object->getData($fieldName);
        if(empty($date))
        {
            $object->setData($fieldName, null);
        }
        else
        {
            $date = Mage::app()->getLocale()->date($date,
               Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
               null, false
            );
            $object->setData($fieldName, $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }
    }
} } 