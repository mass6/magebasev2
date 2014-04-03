<?php 
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Dmx_AttributeIcons_Block_Adminhtml_AttributeIcons_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{
	
	protected $_blockGroup = 'attributeicons';
	protected $_controller = 'adminhtml_attributeIcons';
	
	protected function _beforeToHtml(){
		$this->_headerText = $this->__('Edit attribute icons');
		
		return parent::_beforeToHtml();
	}
	
	protected function _prepareLayout()
	{
		if ($this->_blockGroup && $this->_controller && $this->_mode) {
			#$blockAttributes = array('attribute_id')
			
			$form = $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode . '_form_'.$this->getType());
			$form->setAttributeId($this->getAttributeId());
			$form->setAttributeObject($this->getAttributeObject());
			
			if($this->getOptionCollection()){
				$form->setOptionCollection($this->getOptionCollection());
			}
			$this->setChild('form', $form);
		}
		
		foreach ($this->_buttons as $level => $buttons) {
			foreach ($buttons as $id => $data) {
				$childId = $this->_prepareButtonBlockId($id);
				$this->_addButtonChildBlock($childId);
			}
		}
		
		return $this;
	}
}