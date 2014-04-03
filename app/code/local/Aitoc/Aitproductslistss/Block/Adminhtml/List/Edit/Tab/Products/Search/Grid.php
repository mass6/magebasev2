<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Edit/Tab/Products/Search/Grid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ mearZDMagipDpNRW('ef06f0f168a958a93e599cac880e7db4'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tab_Products_Search_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
{
    /**
     * Retrieve quote store object
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->getQuote()->getStore();
    }

    /**
     * Retrieve quote object
     * @return Aitoc_Aitproductslists_Model_Quote
     */
    public function getQuote()
    {
        return Mage::registry('current_list')->getQuote();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/aitppl_products/grid', array('_current' => true, 'collapse' => null));
    }
} } 