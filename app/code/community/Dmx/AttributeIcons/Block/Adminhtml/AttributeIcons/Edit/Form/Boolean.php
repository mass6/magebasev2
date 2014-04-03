<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Dmx_AttributeIcons_Block_Adminhtml_AttributeIcons_Edit_Form_Boolean extends Mage_Adminhtml_Block_Widget_Form{
	
	protected function _prepareForm(){
		$optionData = Mage::getResourceModel('attributeicons/data_collection')->setAttributeFilter($this->getAttributeId())->toArrayByOption(true);
		$form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getUrl('*/*/save'), 'method' => 'post','enctype'=>'multipart/form-data'));
		$form->setUseContainer(true);
	
		$form->addField('attribute_id','hidden',array('name'=>'attribute_id'));
		
		$options = $this->getAttributeObject()->getSource()->getAllOptions();
		$optionData['attribute_id'] = $this->getAttributeId();
		foreach($options as $option){
			
				$optionData['option_'.$option['value']] = $option['value'];
				$form->addField('option_'.$option['value'],'hidden', array(
						'name'			=>			'options[]',
						'value'			=>			$option['value']
				));
			
				$fieldset = $form->addFieldset('fieldset_'.$option['value'],array('legend'=>'Option : '.$option['label']));
				$fieldset->addField('icon['.$option['value'].']','image', array(
						'label'			=>		$this->__('Icon'),
						'type'			=>		'image',
						'name'			=>		'icon['.$option['value'].']',
				));
				$fieldset->addField('image['.$option['value'].']', 'image',array(
						'label'			=>		$this->__('Image'),
						'name'			=>		'image['.$option['value'].']'
				));
				$fieldset->addField('description['.$option['value'].']','textarea', array(
						'label'			=>		$this->__('Description'),
						'name'			=>		'description['.$option['value'].']'
				));
		}
		$form->setValues($optionData);

		$this->setForm($form);
	
		return parent::_prepareForm();
	}
}