<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Helper/Data.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ DkgBMmZgaipmpchZ('f0d92a2695713862571dcfd77ed4cf77'); ?><?php
class Aitoc_Aitproductslists_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getProduct()
    {
        if (!Mage::registry('product') && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            Mage::register('product', $product);
        }
        return Mage::registry('product');
    }
    
    /**
     * Get request for product add to cart procedure
     *
     * @param   mixed $requestInfo
     * @return  Varien_Object
     */
    public function getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof Varien_Object) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new Varien_Object();
            $request->setQty($requestInfo);
        } else {
            $request = new Varien_Object($requestInfo);
        }
        if (!$request->hasQty()) {
            $post = $request->getPost();
            $request->setQty(1);
        }
        return $request;
    }
    
    public function getButtonLabel()
    {
        if (Mage::app()->getRequest()->getParam('list_id'))
        {
            return $this->__('Update List');
        }
        return $this->__('Add to List');
    }
    
    public function formatDate($date=null, $format='short', $showTime=false)
    {
        if (empty($date))
        {
            return null;
        }
        if (Mage_Core_Model_Locale::FORMAT_TYPE_FULL    !==$format &&
            Mage_Core_Model_Locale::FORMAT_TYPE_LONG    !==$format &&
            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM  !==$format &&
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT   !==$format) {
            return $date;
        }
        
        if (!($date instanceof Zend_Date) && $date && !strtotime($date)) {
            return '';
        }
        
        if (!$date instanceof Zend_Date) {
            $date = Mage::app()->getLocale()->date(strtotime($date), null, null, false);
#            d($date);
        }

        if ($showTime) {
            $format = Mage::app()->getLocale()->getDateTimeFormat($format);
        }
        else {
            $format = Mage::app()->getLocale()->getDateFormat($format);
        }

        return $date->toString($format);
    }
    
    public function getNewListUrl()
    {
        return Mage::getUrl('aitproductslists/list/new');
    }
    
    public function prepareItemRequest($itemId)
    {
        $params = array();
        $item = null;
        $item = $this->initItem($itemId);
        $params['product'] = $item->getProductId();
        $options = Mage::getResourceModel('sales/quote_item_option_collection')
            ->addItemFilter($item);
        foreach ($options->getItems() as $option)
        {
            if ($option->getCode() == 'info_buyRequest')
            {
                $params = unserialize($option->getValue());
            }
        }
        $params['product'] = $item->getProductId();
        $listId = $this->getRequest()->getParam('list_id');
        $params['list_id'] = $listId;
        $params['qty'] = $item->getQty();
        
        if ($listId !=="-1")
        {
            $this->getRequest()->setParam('return_url', Mage::getUrl("aitproductslists/list/view/",array('list_id'=>$listId)));
        }
        return $this->getRequest()->setParams($params);
    }
    
    public function getRequest()
    {
        return Mage::app()->getRequest();
    }
    
    /**
     * Initialize item instance from request data
     *
     * @return Aitoc_Aitproductslists_Model_Quote_Item
     */
    public function initItem($itemId)
    {
        $item = Mage::getModel('aitproductslists/quote_item')->load($itemId);
        return $item;
    }
    
    /**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    public function initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }
    
    public function isAiteditablecartActive()
    {
        return $this->isModuleEnabled('Aitoc_Aiteditablecart');
    }
} } 