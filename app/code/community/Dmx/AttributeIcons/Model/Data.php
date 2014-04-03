<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Dmx_AttributeIcons_Model_Data extends Mage_Core_Model_Abstract{
	
	protected $_files = array('upload'=>array(),'delete'=>array());
	
	protected function _construct(){
		$this->_init('attributeicons/data');
	}
	
	protected function _beforeSave(){
		$this->_deleteImages();		
		$this->_uploadImages();
		
		return parent::_beforeSave();
	}
	
	public function addFileToDelete($fieldName,$value){
		$this->_files['delete'][$fieldName] = $value;
		return $this;
	}
	public function addFileToUpload($fieldName,$value){
		$this->_files['upload'][$fieldName] = $value;
		return $this;
	}
	
	protected function _uploadImages(){
		if(!empty($this->_files['upload'])){
			foreach($this->_files['upload'] as $key => $imageFieldName){
				$this->_uploadImage($key,$imageFieldName);
			}
		}
	}
	
	protected function _deleteImages(){
		if(!empty($this->_files['delete'])){
			foreach($this->_files['delete'] as $fieldName => $image){
				try{
					$this->setData($fieldName,null);
					unlink(Mage::getBaseDir('media').DS.$image);
				}catch(Exception $exc){
				
				}
				
			}
		}
	}
	
	protected function _uploadImage($fieldName,$imageFieldName){
		try{
			$uploader = new Varien_File_Uploader($imageFieldName);
			
			$uploader->save(Mage::getBaseDir('media').DS.'attributeicons');
			if($uploader->getUploadedFileName()){
				$this->setData($fieldName,$uploader->getUploadedFileName());
			}
		}catch(Exception $exc){
				
		}
	}
	
	public function loadByOption($attributeId,$optionId){
		$this->_getResource()->loadByOption($this,$attributeId,$optionId);
		return $this;
	}
}