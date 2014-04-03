<?php

class Insync_Rule_Block_Adminhtml_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'rule';
        $this->_controller = 'adminhtml_rule';
        
        $this->_updateButton('save', 'label', Mage::helper('rule')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('rule')->__('Delete Rule'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		
		
		  
		
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('rule_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'rule_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'rule_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }	

    public function getHeaderText()
    {
        if( Mage::registry('rule_data') && Mage::registry('rule_data')->getId() ) {
			$contractId = Mage::registry('rule_data')->getId();
			$contractDetails = Mage::getModel('rule/rule')->getCollection()->addFieldToFilter('rule_id', $contractId);
			$contractName = '';
			foreach ($contractDetails as $each) {
					$contractName=$each['name'];
			}

            return Mage::helper('rule')->__("Edit Rule - '%s'", $contractName);
        } else {
            return Mage::helper('rule')->__('Add Rule');
        }
    }
}