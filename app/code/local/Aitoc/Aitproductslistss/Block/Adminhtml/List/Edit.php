<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/List/Edit.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ mearZDMagipDpNRW('06adadc91aec76f2e9be70e7b3b31cd7'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_List_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'list_id';
        $this->_controller = 'adminhtml_list';
        $this->_blockGroup = 'aitproductslists';
        $this->_headerText = $this->_getList()->getId()?$this->_getList()->getName():Mage::helper('aitproductslists')->__('New List');
        
        $this->_formScripts[] = "
            function saveAndContinueEdit(link){
                editForm.submit(link);
            }
        ";
        parent::__construct();
    }
    
    protected function _prepareLayout()
    {
        if ($this->_getList()->getDiscountListStatus()==Aitoc_Aitproductslists_Model_List::AITPPL_LIST_DISCONT_STATUS_WAITING OR $this->_getList()->getDiscountListStatus()==Aitoc_Aitproductslists_Model_List::AITPPL_LIST_DISCONT_STATUS_APPROVE)
        {
            $this->_addButton('decline_discount', array(
                'label' => Mage::helper('catalog')->__('Decline Discount'),
                'onclick' => 'setLocation(\'' . $this->_getDeclineUrl() . '\')',
                'class' => 'delete',
            ), 0);
        }
        
        $this->_addButton('duplicate', array(
            'label' => Mage::helper('catalog')->__('Duplicate'),
            'onclick' => 'setLocation(\'' . $this->_getDuplicateUrl() . '\')',
            'class' => 'add',
        ), 0);
        
        $this->_addButton('save_and_continue', array(
            'label'     => Mage::helper('customer')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
            'class'     => 'save'
        ), 10);
    
        
        return parent::_prepareLayout();
    }

    /**
     * Retirve currently edited list model
     *
     * @return Aitoc_Aitproductslists_Model_List
     */
    protected function _getList()
    {
        return Mage::registry('current_list');
    }
    
    protected function _getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current' => true));
    }
    
    protected function _getDeclineUrl()
    {
        return $this->getUrl('*/*/declineDiscount', array('list_id'=>$this->_getList()->getId()));
    }
    
    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        $id = $this->_getList()->getCustomer()->getId();
        if (!$id)
        {
            $id = $this->getRequest()->getParam('customer_id');
        }
        
        return $this->getUrl('*/customer/edit', array('id' => $id, 'tab' => 'customer_info_tabs_ppl'));
    }
    
    public function getHeaderText()
    {
        $html='';
        
        if($this->_getList()->getId() && $this->_getList()->getCustomerId())
        {
            $html .= $this->htmlEscape(Mage::getModel('customer/customer')->load($this->_getList()->getCustomerId())->getName()).' / ';
        }
    
        if ($this->_getList()->getId())
        {
            return $html . $this->htmlEscape($this->_getList()->getName());
        }
        else
        {
            return $html . Mage::helper('aitproductslists')->__('New List');
        }
    }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'  => true,
            'back'      => 'edit',
            'tab'       => '{{tab_id}}'
        ));
    }
} } 