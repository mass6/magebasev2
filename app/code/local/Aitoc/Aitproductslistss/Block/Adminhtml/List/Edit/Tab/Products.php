<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Edit/Tab/Products.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ MjrkaZgrBoqZqfQO('f4f552983c8d965793773369197e6572'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tab_Products extends Mage_Adminhtml_Block_Template
{
    /**
     * Retrieve url for loading blocks
     * @return string
     */
    public function getLoadBlockUrl()
    {
        return $this->getUrl('*/aitppl_products/loadBlock', array('customer_id' => Mage::registry('current_customer')->getId(), 'list_id' => Mage::registry('current_list')->getId()));
    }
} } 