<?php
class Insync_Custom_Block_Adminhtml_Custom_Invoice extends Mage_Adminhtml_Block_Sales_Order_Invoice_View{
	public function getCustomVars(){
		$model = Mage::getModel('custom/custom_order');
		return $model->getByOrder($this->getInvoice()->getOrderId());
	}
}