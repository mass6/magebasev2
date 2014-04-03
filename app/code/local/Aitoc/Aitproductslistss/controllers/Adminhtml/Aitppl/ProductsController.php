<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/controllers/Adminhtml/Aitppl/ProductsController.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ BZEmkreyjqCrwXfd('c63ebab4ff5dfdefbc17b1906fb92c7e'); ?><?php
class Aitoc_Aitproductslists_Adminhtml_Aitppl_ProductsController extends Aitoc_Aitproductslists_Controller_Adminhtml_Abstract
{
    /**
     * Retrieve order create model
     *
     * @return Aitoc_Aitproductslists_Model_Adminhtml_List_Edit
     */
    protected function _getEditListModel()
    {
        return Mage::getSingleton('aitproductslists/adminhtml_list_edit');
    }

    public function gridAction()
    {
        $this->_initList();
        $this->getResponse()->setBody($this->getLayout()->createBlock('aitproductslists/adminhtml_list_edit_tab_products_search_grid')->toHtml());
    }
    
    public function configureQuoteItemsAction()
    {
        // Prepare data
        $configureResult = new Varien_Object();
        try {
            $quoteItemId = (int) $this->getRequest()->getParam('id');
            if (!$quoteItemId) {
                Mage::throwException($this->__('Quote item id is not received.'));
            }

            $quoteItem = Mage::getModel('aitproductslists/quote_item')->load($quoteItemId);
            if (!$quoteItem->getId()) {
                Mage::throwException($this->__('Quote item is not loaded.'));
            }

            $configureResult->setOk(true);
            $optionCollection = Mage::getModel('sales/quote_item_option')->getCollection()
                    ->addItemFilter(array($quoteItemId));
            $quoteItem->setOptions($optionCollection->getOptionsByItem($quoteItem));

            $configureResult->setBuyRequest($quoteItem->getBuyRequest());
            $configureResult->setCurrentStoreId($quoteItem->getStoreId());
            $configureResult->setProductId($quoteItem->getProductId());
            //$sessionQuote = Mage::getSingleton('adminhtml/session_quote');
            //$configureResult->setCurrentCustomerId($quoteItem->getQuote()->getCustomerId());

        } catch (Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        // Render page
        /* @var $helper Mage_Adminhtml_Helper_Catalog_Product_Composite */
        $helper = Mage::helper('adminhtml/catalog_product_composite');
        $helper->renderConfigureResult($this, $configureResult);
    }
    
    public function configureProductToAddAction()
    {
        // Prepare data
        $productId  = (int) $this->getRequest()->getParam('id');

        $configureResult = new Varien_Object();
        $configureResult->setOk(true);
        $configureResult->setProductId($productId);
        $sessionQuote = Mage::getSingleton('adminhtml/session_quote');
        $configureResult->setCurrentStoreId($sessionQuote->getStore()->getId());
        $configureResult->setCurrentCustomerId($sessionQuote->getCustomerId());

        // Render page
        /* @var $helper Mage_Adminhtml_Helper_Catalog_Product_Composite */
        $helper = Mage::helper('adminhtml/catalog_product_composite');
        $result = $helper->renderConfigureResult($this, $configureResult);

        return $result;
    }
    
    public function loadBlockAction()
    {
        $request = $this->getRequest();
        $this->_initList();
        $this->_initCustomer('customer_id');
        
        $this->_processData();

        $asJson= $request->getParam('json');
        $block = $request->getParam('block');

        $update = $this->getLayout()->getUpdate();
        if ($asJson) {
            $update->addHandle('adminhtml_sales_order_create_load_block_json');
        } else {
            $update->addHandle('adminhtml_sales_order_create_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $update->addHandle('adminhtml_aitppl_products_block_' . $block);
            }
        }

        $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        $result = $this->getLayout()->getBlock('content')->toHtml();
        
        $this->getResponse()->setBody($result);
    }
    
    /**
     * Process buyRequest file options of items
     *
     * @param array $items
     * @return array
     */
    protected function _processFiles($items)
    {
        /* @var $productHelper Mage_Catalog_Helper_Product */
        $productHelper = Mage::helper('catalog/product');
        foreach ($items as $id => $item) {
            $buyRequest = new Varien_Object($item);
            $params = array('files_prefix' => 'item_' . $id . '_');
            $buyRequest = $productHelper->addParamsToBuyRequest($buyRequest, $params);
            if ($buyRequest->hasData()) {
                $items[$id] = $buyRequest->toArray();
            }
        }
        return $items;
    }
    
    protected function _processData()
    {
        /**
         * Adding products to quote from special grid
         */
        if ($this->getRequest()->has('item') && !$this->getRequest()->getPost('update_items'))
        {
            $items = $this->getRequest()->getPost('item');
            $items = $this->_processFiles($items);
            $this->_getEditListModel()->addProducts($items);
        }

        /**
         * Update quote items
         */
        if ($this->getRequest()->getPost('update_items'))
        {
            $items = $this->getRequest()->getPost('item', array());
            $items = $this->_processFiles($items);
            $this->_getEditListModel()->updateQuoteItems($items);
        }
        
        /**
         * Remove quote item
         */
        $removeItemId = (int) $this->getRequest()->getPost('remove_item');
        $removeFrom = (string) $this->getRequest()->getPost('from');
        if ($removeItemId && $removeFrom) {
            $this->_getEditListModel()->removeItem($removeItemId, $removeFrom);
        }
        
        $this->_getEditListModel()->saveQuote();
    }
} } 