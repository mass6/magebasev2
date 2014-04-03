<?php

class Insync_Supplier_Block_Adminhtml_Supplier_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'supplier';
        $this->_controller = 'adminhtml_supplier';
        
        $this->_updateButton('save', 'label', Mage::helper('supplier')->__('Save Supplier'));
        $this->_updateButton('delete', 'label', Mage::helper('supplier')->__('Delete Supplier'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		// $redirect =$this->_redirect('*/*/new');
		$id =Mage::registry('supplier_data')->getId();
		if($id!='')
		{
		// $this->_addButton('duplicate', array(
            // 'label'     => Mage::helper('adminhtml')->__('Duplicate'),
            // 'onclick'   => 'setLocation(\'' . $this->getDuplicateUrl() . '\')',
            // 'class'     => 'add',
        // ), -100);
		 } 
		$base = $this->getBaseUrl();
		$relocation = $base.'supplier/adminhtml_web/edit/';
		// Mage::log('base url');
		// Mage::log($relocation);
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('web_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'web_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'web_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
			
        ";
    }	
	
   public function getHeaderText()
    {
        if( Mage::registry('supplier_data') && Mage::registry('supplier_data')->getId() ) {
			$contractId = Mage::registry('supplier_data')->getId();
			$contractDetails = Mage::getModel('supplier/supplier')->getCollection()->addFieldToFilter('id', $contractId);
			$contractName = '';
			foreach ($contractDetails as $each) {
					$contractName=$each['name'];
			}
            return Mage::helper('supplier')->__("Edit Supplier '%s'", $contractName);
        } else {
            return Mage::helper('supplier')->__('Add Supplier');
        }
    }
	public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current'=>true));
    }
}