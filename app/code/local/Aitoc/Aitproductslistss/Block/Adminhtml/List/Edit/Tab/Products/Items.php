<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Edit/Tab/Products/Items.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ MjrkaZgrBoqZqfQO('5b562b174bd1b6a12ee5753c0268beba'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tab_Products_Items extends Mage_Adminhtml_Block_Sales_Order_Create_Items
{
    public function getHeaderText()
    {
        return Mage::helper('aitproductslists')->__('Products in the List');
    }
    
    public function getQuote()
    {
        return Mage::registry('current_list')->getQuote();
    }
    
    protected function _toHtml()
    {
        return Mage_Adminhtml_Block_Widget::_toHtml();
    }
} } 