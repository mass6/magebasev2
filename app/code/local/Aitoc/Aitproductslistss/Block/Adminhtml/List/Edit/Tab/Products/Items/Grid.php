<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Edit/Tab/Products/Items/Grid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ amejBgrekphghUcr('6c79ec8b4c021dd44a7b495b21acd928'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tab_Products_Items_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Items_Grid
{
    public function getQuote()
    {
        return Mage::registry('current_list')->getQuote();
    }
    
    public function getSession()
    {
        return Mage::getSingleton('aitproductslists/adminhtml_quote_session');
    }
} } 