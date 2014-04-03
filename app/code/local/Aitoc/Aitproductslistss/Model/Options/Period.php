<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Options/Period.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ BZEmkreyjqCrwXfd('dd338652640b42a584481c0e86ad2475'); ?><?php
class Aitoc_Aitproductslists_Model_Options_Period
{
    public function toOptionArray()
    {
       $options = Mage::helper('aitproductslists/list')->getReminderPeriods();
       $array = array(array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('-- Please select --')));
       foreach ($options as $value=>$label)
       {
           $array[] = array('value'=>$value, 'label'=>Mage::helper('sitemap')->__($label));
       }
        return $array; 
    }
} } 