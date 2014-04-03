<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Dmx_AttributeIcons_Helper_Data extends Mage_Core_Helper_Data{
	
	const XPATH_CONFIG_WIDTH = 'attributeicons/size/%s/width';
	const XPATH_CONFIG_HEIGHT = 'attributeicons/size/%s/height';
	
	public function hasIcons($attributeId){
		/* @var $coreResource Mage_Core_Model_Resource */
		$coreResource = Mage::getModel('core/resource');
		$table = $coreResource->getTableName('attributeicons/data');
		$readConnection = $coreResource->getConnection('read');
		$res = $readConnection->fetchOne("SELECT count(*) FROM {$table} WHERE attribute_id = {$attributeId}");
		
		return $res?true:false;
	}
	
	public function getImageDir(){
		return Mage::getBaseDir('media').'attributeicons';
	}
	
	public function getImageUrl($image){
		return Mage::getBaseUrl('media').'attributeicons/'.$image;
	}
	
	public function getDimensions($type){
		$dimensions = array();
		
		$path = sprintf(self::XPATH_CONFIG_WIDTH,strtolower($type));
		$dimensions['width'] = Mage::getStoreConfig($path);
		$path = sprintf(self::XPATH_CONFIG_HEIGHT,strtolower($type));
		$dimensions['height'] = Mage::getStoreConfig($path);
		
		return $dimensions;
	}
}