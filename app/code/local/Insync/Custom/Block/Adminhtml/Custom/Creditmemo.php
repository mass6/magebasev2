<?php
class Insync_Custom_Block_Adminhtml_Custom_Creditmemo extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_View{
	public function getCustomVars(){
		$model = Mage::getModel('custom/custom_order');
		return $model->getByOrder($this->getCreditmemo()->getOrderId());
	}
}