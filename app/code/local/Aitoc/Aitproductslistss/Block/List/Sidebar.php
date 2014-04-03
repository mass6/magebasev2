<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/List/Sidebar.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ DkgBMmZgaipmpchZ('89cf729410183eb3986ae6602601f007'); ?><?php
class Aitoc_Aitproductslists_Block_List_Sidebar extends Mage_Checkout_Block_Cart_Sidebar
{
    const XML_PATH_CHECKOUT_SIDEBAR_COUNT   = 'aitproductslists/sidebar/count';
    protected $_itemRenders = array();
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->addItemRender('default', 'aitproductslists/list_sidebar_item_renderer', 'aitproductslists/list/sidebar/default.phtml');
    }
    
    public function getCurrentList()
    {
        return Mage::getModel('aitproductslists/list')->load(Mage::getSingleton('aitproductslists/session')->getCurrentListId());
    }
    
    /**
     * Get shopping cart items qty based on configuration (summary qty or items qty)
     *
     * @return int | float
     */
    public function getSummaryCount()
    {
        return sizeof($this->getItems());
    }

    
    /**
     * Define if Shopping Cart Sidebar enabled
     *
     * @return bool
     */
    public function getIsNeedToDisplaySideBar()
    {
        return (bool) Mage::app()->getStore()->getConfig('checkout/sidebar/display');
    }
    
    public function getTotalsCache()
    {
        if (empty($this->_totals)) {
            $this->_totals = Mage::getSingleton('aitproductslists/list')->getQuote()->getTotals();
            $this->_totals = Mage::getSingleton('aitproductslists/list')->getQuote()->getTotals();
        }
        return $this->_totals;
    }
    
    public function getTotals()
    {
        return $this->getCurrentList()->getQuote()->getSubtotal();
    }
    
    public function getItemsQty()
    {
        $quote = $this->getCurrentList()->getQuote();
        return count($quote->getAllVisibleItems())*1;
    }
    
    public function getItems()
    {
        $quote = $this->getCurrentList()->getQuote();
        return $quote->getAllVisibleItems();
    }
    /**
     * Get item row html
     *
     * @param   Aitoc_Aitproductslists_Model_Quote_Item $item
     * @return  string
     */
    public function getItemHtml(Mage_Sales_Model_Quote_Item $item)
    {
        $renderer = $this->getItemRenderer($item->getProductType())->setItem($item);
        return $renderer->toHtml();
    }
    
    public function getSidebarButtonUrl()
    {
        return $this->getUrl('aitproductslists/list/sendToCart',array('list_id'=>$this->getCurrentList()->getId()));
    }
    
    /**
     * Get array of last added items
     *
     * @return array
     */
    public function getRecentItems($count = null)
    {
        if ($count === null) {
            $count = $this->getItemCount();
        }

        $items = array();
        if (!$this->getSummaryCount()) {
            return $items;
        }

        $i = 0;
        $allItems = array_reverse($this->getItems());
        foreach ($allItems as $item) {
            /* @var $item Mage_Sales_Model_Quote_Item */
            if (!$item->getProduct()->isVisibleInSiteVisibility()) {
                $productId = $item->getProduct()->getId();
                $products  = Mage::getResourceSingleton('catalog/url')
                    ->getRewriteByProductStore(array($productId => $item->getStoreId()));
                if (!isset($products[$productId])) {
                    continue;
                }
                $urlDataObject = new Varien_Object($products[$productId]);
                $item->getProduct()->setUrlDataObject($urlDataObject);
            }

            $items[] = $item;
            if (++$i == $count) {
                break;
            }
        }
        return $items;
    }
    
    public function getItemRenderer($type)
    {
        if (!isset($this->_itemRenders[$type])) {
            $type = 'default';
        }
        if (is_null($this->_itemRenders[$type]['blockInstance'])) {
             $this->_itemRenders[$type]['blockInstance'] = $this->getLayout()
                ->createBlock($this->_itemRenders[$type]['block'])
                    ->setTemplate($this->_itemRenders[$type]['template'])
                    ->setRenderedBlock($this);
        }

        return $this->_itemRenders[$type]['blockInstance'];
    }
} } 