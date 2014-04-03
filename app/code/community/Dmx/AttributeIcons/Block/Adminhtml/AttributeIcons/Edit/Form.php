<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
/**
 * 
 *@method void setAttributeId(int $attributeId)
 *@method int getAttributeId()
 *@method void setOptionCollection(Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection $optionCollection)
 *@method Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection getOptionCollection()
 */
class Dmx_AttributeIcons_Block_Adminhtml_AttributeIcons_Edit_Form extends Mage_Adminhtml_Block_Widget_Form{
	
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
			}
			$form->setValues($optionData);
		}

		$this->setForm($form);
		
		return parent::_prepareForm();
	}
}