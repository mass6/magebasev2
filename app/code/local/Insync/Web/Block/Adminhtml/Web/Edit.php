<?php

class Insync_Web_Block_Adminhtml_Web_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'web';
        $this->_controller = 'adminhtml_web';
        
        $this->_updateButton('save', 'label', Mage::helper('web')->__('Save Contract'));
        $this->_updateButton('delete', 'label', Mage::helper('web')->__('Delete Contract'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		// $redirect =$this->_redirect('*/*/new');
		$id =Mage::registry('web_data')->getId();
		if($id!='')
		{
		$this->_addButton('duplicate', array(
            'label'     => Mage::helper('adminhtml')->__('Duplicate'),
            'onclick'   => 'setLocation(\'' . $this->getDuplicateUrl() . '\')',
            'class'     => 'add',
        ), -100);
		 } 
		$base = $this->getBaseUrl();
		$relocation = $base.'web/adminhtml_web/edit/';
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
        if( Mage::registry('web_data') && Mage::registry('web_data')->getId() ) {
			$contractId = Mage::registry('web_data')->getId();
			$contractDetails = Mage::getModel('web/web')->getCollection()->addFieldToFilter('web_id', $contractId);
			$contractName = '';
			foreach ($contractDetails as $each) {
					$contractName=$each['name'];
			}
            return Mage::helper('web')->__("Edit Contract '%s'", $contractName);
        } else {
            return Mage::helper('web')->__('Add Contract');
        }
    }
	public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current'=>true));
    }
}