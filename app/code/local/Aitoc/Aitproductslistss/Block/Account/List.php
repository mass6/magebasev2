<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Account/List.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ gDkyraBkephahANV('e08b35430d574cd60c9746ea3830b583'); ?><?php
class Aitoc_Aitproductslists_Block_Account_List extends Aitoc_Aitproductslists_Block_Account_Abstract
{

    public function __construct()
    {
       // echo "<pre>"; print_r(Mage::getSingleton('aitproductslists/session')->getData()); exit;
        parent::__construct();
        $this->setTemplate('aitproductslists/account/list.phtml');
        $customerSession = Mage::getSingleton('customer/session');
        if ($customerSession->isLoggedIn())
        {
            $lists = Mage::getModel('aitproductslists/list')->getCollection()->getGridList($customerSession->getCustomerId());
        }
        else
        {
            $listIds = array();
            $listIds = Mage::getSingleton('aitproductslists/session')->getnonLoginListIds();
            $lists = Mage::getModel('aitproductslists/list')->getCollection()->getGridListById($listIds);
        }
        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('core')->__('My Lists'));
        $this->setListData($lists);

        }

    public function getLists()
    {
        return $this->getListData();
    }

    public function getCurrentPage()
    {
        if ($page = (int) $this->getRequest()->getParam('p')) {
            return $page;
        }
        return 1;
    }
    
    public function getLimit()
    {
        if ($limit = $this->getRequest()->getParam('limit')) {
            return $limit;
        }
        return 10;
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'aitproductslists.account.list.pager')
            ->setCollection($this->getLists());
        $this->setChild('pager', $pager);
        $this->getLists()->load();
        foreach ($this->getListData() as $list)
        {
            $list->getQuote($list->getQuoteId())->collectTotals();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($list)
    {
        return $this->getUrl('*/*/view', array('list_id' => $list->getId()));
    }
    
    public function getToCartUrl($list)
    {
        return $this->getUrl('*/*/sendToCart', array('list_id' => $list->getId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/grid');
    }
    
} } 