<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales order history block
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_Block_Order_Orderapproval extends Mage_Sales_Block_Order_History
{

    public function __construct()
    {
		$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'cust_category');
		$options1 = array();
		$options2 = array();
		if ($attribute->usesSource()) {
			foreach ($attribute->getSource()->getAllOptions() as $optionValue) {
			foreach($optionValue as $key=>$value){
				if($key == 'label')$options1[] = $value ; 
				if($key == 'value')$options2[] = $value ; 
				}
			}
		}
		$appId = '';
		for($i=0;$i<count($options1);$i++){
		if($options1[$i] == 'Approver')
		$appId = $options2[$i];
		}
		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		$customer= Mage::getModel('customer/customer')->load($customerId);
		$IsAprrover=$customer->getCustCategory();
		if($IsAprrover==$appId){
		$approve=(string)$customerId;
        // Mage::log('$approve============');
        // Mage::log($approve);

		
		$collection = Mage::getResourceModel('sales/order_collection')
                ->addAttributeToSelect('*')
				->addFieldToFilter('status', 'pending_approval')
				->addFieldToFilter('approver',$customerId)
				->setOrder('created_at', 'desc');
		$ordersId=array();
		foreach($collection as $eachCollection){
		$ordersId[]=$eachCollection->getEntityId();
	
		}
		// $ids = implode(',',$ordersId);
        parent::__construct();
        $this->setTemplate('sales/order/history.phtml');
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', 'pending_approval')
             ->addFieldToFilter('entity_id', array('in'=> $ordersId))
            ->setOrder('created_at', 'desc');
        $this->setOrders($orders);
	}
	else{
		  parent::__construct();
        $this->setTemplate('sales/order/history.phtml');
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', 'pending_approval')
             ->addFieldToFilter('customer_id',$customerId)
            ->setOrder('created_at', 'desc');
        $this->setOrders($orders);
	}
        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('My Orders'));
    }
	
	
	
	
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'sales.order.history.pager')
            ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }

    public function getTrackUrl($order)
    {
        return $this->getUrl('*/*/track', array('order_id' => $order->getId()));
    }
	
    public function getReorderUrl($order)
    {
        return $this->getUrl('*/*/reorder', array('order_id' => $order->getId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
