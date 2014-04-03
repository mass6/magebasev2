<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Dmx_AttributeIcons_Block_Catalog_Attribute_Renderer_Default extends Mage_Core_Block_Template{
		const XPATH_CONFIG_RENDERER_TYPES = 'default/attributeicons/renderer/types';
		
		protected $_renderer = null;
		protected $_rendererTypes = array();
		protected $_content = array();
		
		protected function _construct(){
			parent::_construct();
			if($this->getAttributeCode() && $this->_getTemplateByCode($this->getAttributeCode())){
				$this->setTemplate($this->_getTemplateByCode($this->getAttributeCode()));
			}else{
				$this->setTemplate('attributeicons/renderer/default.phtml');
			}
			
			$this->_rendererTypes = Mage::getConfig()->getNode(self::XPATH_CONFIG_RENDERER_TYPES)->asCanonicalArray();
		}
		
		protected function _getTemplateByCode($attributeCode){
			if(file_exists(Mage::getDesign()->getTemplateFilename('attributeicons/renderer/attribute/'.$attributeCode.'.phtml'))){
				return 'attributeicons/renderer/attribute/'.$attributeCode.'.phtml';
			}else{
				return 'attributeicons/renderer/default.phtml';
			}
			
		}
		
		protected  function _getRenderer($type){
			$this->setTemplate($this->_rendererTypes[$type]);
			
			return $this;
		}
		
		protected function _toHtml(){
			$html = '';
			if(!$this->getTemplate()){
				$html = implode('',$this->_content);
			}else{
				$html = parent::_toHtml();
			}
			
			return $html;
		}
		
		public function addContent($data,$type = null){
			if(!$type){
				$this->_content[] = $data;
			}
			/*else{
				$this->_content[] = $this->_getRenderer($type)->setValue($data)->toHtml();
			}*/

			return $this;
		}
		
		public function getContent(){
			return $this->_content;
		}
		
		public function render(){
			return $this->toHtml();
		}
}