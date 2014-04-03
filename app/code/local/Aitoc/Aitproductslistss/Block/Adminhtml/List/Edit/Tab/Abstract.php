<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Edit/Tab/Abstract.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ BZEmkreyjqCrwXfd('36006de111b8ac3adb7735ea78073fff'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Edit_Tab_Abstract extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     *  @var Aitoc_Aitproductslists_Model_List
     */
    protected $_list;

    public function __construct()
    {
        parent::__construct();
        
        $this->_list = Mage::registry('current_list');
    }
    
    protected function _getList()
    {
        return $this->_list;
    }
} } 