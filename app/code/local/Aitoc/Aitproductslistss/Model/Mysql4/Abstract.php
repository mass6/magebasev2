<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Mysql4/Abstract.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ yBZaDjmZMwojoQTP('0f780bacd3190bb0c097ab925abdef2e'); ?><?php
abstract class Aitoc_Aitproductslists_Model_Mysql4_Abstract extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_dateFields = array();    

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        foreach($this->_dateFields as $dateField)
        {
            $this->_prepareDateField($object, $dateField);
        }
        
        return parent::_beforeSave($object);
    }

    protected function _prepareDateField(Mage_Core_Model_Abstract $object, $fieldName)
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