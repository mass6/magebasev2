<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/Abstract.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ rMjDeBkjyqwBwTIk('146efe5c4d82759b86ed81e3c7a0cc65'); ?><?php

class Aitoc_Aitproductslists_Block_Adminhtml_Abstract extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{
    public function getListDiscount($listId)
    {
        $list = Mage::getModel('aitproductslists/list')->load($listId);
        if ($discount = $list->checkDiscount())
        {
            return $list->getDiscount()->getPrice();
        }
        return false;
    }
} } 