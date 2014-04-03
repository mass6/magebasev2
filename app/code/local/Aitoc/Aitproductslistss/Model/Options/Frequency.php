<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Options/Frequency.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ rMjDeBkjyqwBwTIk('c513a8cdfa7403f6954fa8d3e646c223'); ?><?php
class Aitoc_Aitproductslists_Model_Options_Frequency
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('-- Please select --')),
            array('value'=>'daily', 'label'=>Mage::helper('sitemap')->__('Daily')),
            array('value'=>'weekly', 'label'=>Mage::helper('sitemap')->__('Weekly')),
            array('value'=>'monthly', 'label'=>Mage::helper('sitemap')->__('Monthly')),
            array('value'=>'yearly', 'label'=>Mage::helper('sitemap')->__('Yearly')),
        );
    }
} } 