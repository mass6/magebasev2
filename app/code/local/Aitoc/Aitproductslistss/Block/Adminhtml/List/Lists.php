<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Lists.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ ZyBegMaBroqMqICa('7a21fa1f98243f4bbcae26b16645b499'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Lists extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_list';
        $this->_blockGroup = 'aitproductslists';
        $this->_removeButton('new');
        $this->_headerText = Mage::helper('customer')->__('Manage Aitoc Products Lists');
        
        parent::__construct();
    }
    
     protected function _prepareLayout()
    {
         $this->_removeButton('add');
        $this->setChild('store_switcher',
        $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setUseConfirm(false)
                ->setSwitchUrl($this->getUrl('*/*/*', array('store'=>null)))
           //     ->setTemplate('aitproductslists/list/grid/switcher.phtml')
        );
        return parent::_prepareLayout();
    }

} } 