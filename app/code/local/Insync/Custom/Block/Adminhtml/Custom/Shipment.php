<?php
class Insync_Custom_Block_Adminhtml_Custom_Shipment extends Mage_Adminhtml_Block_Sales_Order_Shipment_View{
	public function getCustomVars(){
		$model = Mage::getModel('custom/custom_order');
		return $model->getByOrder($this->getShipment()->getOrderId());
	}
}