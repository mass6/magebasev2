<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Dmx_AttributeIcons_Block_Adminhtml_AttributeIcons_Edit_Form_Select extends Mage_Adminhtml_Block_Widget_Form{
	
	protected function _prepareForm(){
		$form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getUrl('*/*/save'), 'method' => 'post','enctype'=>'multipart/form-data'));
		$form->setUseContainer(true);
	
		$form->addField('attribute_id','hidden',array('name'=>'attribute_id'));
	
		if(count($this->getOptionCollection())){
			$optionData = Mage::getResourceModel('attributeicons/data_collection')->setAttributeFilter($this->getAttributeId())->toArrayByOption(true);
			$optionData['attribute_id'] = $this->getAttributeId();
				
			foreach($this->getOptionCollection() as $option){
				$optionData['option_'.$option->getId()] = $option->getId();
				$form->addField('option_'.$option->getId(),'hidden', array(
						'name'			=>			'options[]',
						'value'			=>			$option->getId()
				));
	
				$fieldset = $form->addFieldset('fieldset_'.$option->getId(),array('legend'=>'Option : '.$option->getValue()));
				$fieldset->addField('icon['.$option->getId().']','image', array(
						'label'			=>		$this->__('Icon'),
						'type'			=>		'image',
						'name'			=>		'icon['.$option->getId().']',
				));
				$fieldset->addField('image['.$option->getId().']', 'image',array(
						'label'			=>		$this->__('Image'),
						'name'			=>		'image['.$option->getId().']'
				));
				$fieldset->addField('description['.$option->getId().']','textarea', array(
						'label'			=>		$this->__('Description'),
						'name'			=>		'description['.$option->getId().']'
				));
				
				$fieldset->addField('show_both['.$option->getId().']','checkbox', array(
						'label'			=>		$this->__('Show both'),
						'onclick'   	=>		'this.value = this.checked ? 1 : 0;',
						'name'			=>		'show_both['.$option->getId().']',
						'checked'	=>		array_key_exists("show_both[{$option->getId()}]", $optionData)?$optionData["show_both[{$option->getId()}]"]:false,
				));
			}
			$form->setValues($optionData);
		}
	
		$this->setForm($form);
	
		return parent::_prepareForm();
	}
}