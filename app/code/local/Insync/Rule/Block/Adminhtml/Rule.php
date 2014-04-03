<?php
class Insync_Rule_Block_Adminhtml_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_rule';
    $this->_blockGroup = 'rule';
    $this->_headerText = Mage::helper('rule')->__('Rule Manager');
    $this->_addButtonLabel = Mage::helper('rule')->__('Add Rule');
    parent::__construct();
  }
}