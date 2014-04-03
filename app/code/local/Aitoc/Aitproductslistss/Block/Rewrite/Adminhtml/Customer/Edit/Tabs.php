<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Rewrite/Adminhtml/Customer/Edit/Tabs.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ rMjDeBkjyqwBwTIk('36eaa2ff20b24a83699af67c76542240'); ?><?php
class Aitoc_Aitproductslists_Block_Rewrite_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Customer_Edit_Tabs
{
    protected function _beforeToHtml()
    {
        if (Mage::registry('current_customer')->getId())
        {
            $this->addTab('ppl', array(
                'label'     => Mage::helper('aitproductslists')->__('Personal Products Lists'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/aitppl/lists', array('_current' => true)),
                'after'     => 'cart'
            ));
        }
        
        return parent::_beforeToHtml();
    }
} } 