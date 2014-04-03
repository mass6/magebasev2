<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Edit/Tab/Products/Search.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ amejBgrekphghUcr('225ab9945037eac3a0dc6e387d10811c'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tab_Products_Search extends Mage_Adminhtml_Block_Sales_Order_Create_Search
{
    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label' => Mage::helper('sales')->__('Add Selected Product(s) to the List'),
            'onclick' => 'order.productGridAddSelected()',
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }
} } 