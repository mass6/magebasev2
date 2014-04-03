<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Account/View.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ rMjDeBkjyqwBwTIk('6a45065fba23bf6dba5a2b0214792a7f'); ?><?php
class Aitoc_Aitproductslists_Block_Account_View extends Mage_Core_Block_Template
{
    public function isLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }
    
    public function __construct()
    {
        parent::__construct();
        
        $this->setTemplate('aitproductslists/account/view.phtml');
        $listId = $this->getRequest()->getParam('list_id');
        $list = Mage::getModel('aitproductslists/list')->load($listId);
        
        $this->setList($list);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('core')->__('My List'));
    }

    public function getListName($_list)
    {
        $listName = $_list->getName();
        if ($listName == "")
        {
            $listName = "Create New Products List";
        }
        return $listName;
    }
    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    public function getBackUrl()
    {
        return Mage::getUrl('*/*/history');
    }

    public function getInvoiceUrl($order)
    {
        return Mage::getUrl('*/*/invoice', array('order_id' => $order->getId()));
    }

    public function getShipmentUrl($order)
    {
        return Mage::getUrl('*/*/shipment', array('order_id' => $order->getId()));
    }

    public function getCreditmemoUrl($order)
    {
        return Mage::getUrl('*/*/creditmemo', array('order_id' => $order->getId()));
    }
    
    public function getReminderPeriods()
    {
        return Mage::helper('aitproductslists/list')->getReminderPeriods();
    }
    
    public function getRequringPeriods()
    {
        return Mage::helper('aitproductslists/list')->getReminderPeriods();
    }
    
    public function getReminderFrequencies()
    {
        return Mage::helper('aitproductslists/list')->getReminderFrequencies();
    }
    
    public function getCalendar($sFieldName, $sFieldId, $sLabel, $sFieldClass, $sFieldValue)
    {
        $dateFormat = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT;
        $sFieldValue = Mage::helper('aitproductslists')->formatDate($sFieldValue, $dateFormat);
       
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat($dateFormat);
        $calendar = Mage::getModel('core/layout')
                    ->createBlock('core/html_date')
                    ->setName($sFieldName)
                    ->setId($sFieldId)
                    ->setTitle($sLabel) 
                    ->setClass($sFieldClass)
                    ->setValue($sFieldValue, $dateFormatIso)
#                    ->setValue($sDateValue)
                    ->setClass('validate-date')
                    ->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'))
                    ->setFormat(Mage::app()->getLocale()->getDateStrFormat($dateFormat));
        
                return  $calendar->getHtml();
    }
    
    public function getDeleteListUrl()
    {
        return $this->getUrl('*/*/remove',array("list_id"=>$this->getList()->getId()));
    }
    
    public function getDuplicateListUrl()
    {
        return $this->getUrl('*/*/duplicate',array("list_id"=>$this->getList()->getId()));
    }
    
    public function getSaveListUrl()
    {
        return $this->getUrl('*/*/save',array("list_id"=>$this->getList()->getId()));
    }
    
    public function getWantDiscountUrl()
    {
        return $this->getUrl('*/*/discount',array("list_id"=>$this->getList()->getId()));
    }
    
    public function isListNew()
    {
        return ($this->getList()->getId()) ? true : false ;
    }
} } 