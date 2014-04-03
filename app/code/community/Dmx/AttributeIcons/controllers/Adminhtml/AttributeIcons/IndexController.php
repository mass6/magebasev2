<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Dmx_AttributeIcons_Adminhtml_AttributeIcons_IndexController extends Dmx_AttributeIcons_Controller_Abstract{
	
	public function indexAction(){
		$this->loadLayout();
		
		$grid = $this->getLayout()->createBlock('attributeicons/adminhtml_attributeIcons_grid');
		$this->_addContent($grid);
		
		$this->renderLayout();
	}
	
	public function editAction(){
		$this->loadLayout();
		
		$attributeId = $this->getRequest()->getParam('attribute_id');
		if($attributeId){
			$attribute = Mage::getModel('eav/entity_attribute');
			$attributeResource = Mage::getResourceModel('eav/entity_attribute')->load($attribute,$attributeId);
			
			$optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')->setAttributeFilter($attributeId)
									                ->setPositionOrder('desc', true)
									                ->load();
			$blockAttributes = array('attribute_id'=>$attributeId,'type'=>$attribute->getFrontendInput(),'attribute_object'=>$attribute);
			if(count($optionCollection)){
				$blockAttributes['option_collection'] = $optionCollection;
			}			
			
			$form = $this->getLayout()->createBlock('attributeicons/adminhtml_attributeIcons_edit','attributeicons.edit.form',$blockAttributes);
			$this->_addContent($form);

		}
		
		$this->renderLayout();
	}
	
	public function saveAction(){
		$optionsData = $this->getRequest()->getPost();
		$catalogAttribute = Mage::getSingleton('attributeicons/catalog_attribute');
		/* @var $catalogAttribute Dmx_AttributeIcons_Model_Catalog_Attribute */
		$catalogAttribute->setOptionsData($optionsData,false);
		$catalogAttribute->save();
		$this->_redirect('*/*/edit',array('attribute_id'=>$optionsData['attribute_id']));
	}
}