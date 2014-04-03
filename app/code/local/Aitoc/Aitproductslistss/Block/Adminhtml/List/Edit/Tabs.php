<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Edit/Tabs.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ DkgBMmZgaipmpchZ('6762c3d322e97d24940edb1ed7e30c2d'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('aitppl_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('aitproductslists')->__('List Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('aitproductslists')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('aitproductslists/adminhtml_list_edit_tab_info')->initForm()->toHtml(),
            'active'    => Mage::registry('current_list')->getId() ? false : true
        ));
        
        $this->addTab('reminder', array(
            'label'     => Mage::helper('aitproductslists')->__('Reminder'),
            'content'   => $this->getLayout()->createBlock('aitproductslists/adminhtml_list_edit_tab_reminder')->initForm()->toHtml(),
        ));
        
        $this->addTab('discount', array(
            'label'     => Mage::helper('aitproductslists')->__('Discount'),
            'content'   => $this->getLayout()->createBlock('aitproductslists/adminhtml_list_edit_tab_discount')->initForm()->toHtml(),
        ));

        if(Mage::registry('current_list')->getId())
        {
            $this->addTab('products', array(
                'label'     => Mage::helper('catalog')->__('Products'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/*/products', array('_current' => true)),
            ));
        
            $this->addTab('orders', array(
                'label'     => Mage::helper('sales')->__('Orders'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/*/orders', array('_current' => true)),
            ));
        }

        $this->_updateActiveTab();
        Varien_Profiler::stop('aitppl/tabs');
        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if( $tabId ) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
} } 