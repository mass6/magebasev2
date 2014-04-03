<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/controllers/MassactionController.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ gDkyraBkephahANV('8dd997f3a874ea0cf82afb245143ab3b'); ?><?php
class Aitoc_Aitproductslists_MassactionController extends Aitoc_Aitproductslists_Controller_Front_Action
{
     protected function _getRefererUrl()
    {
         return Mage::getUrl('aitproductslists/list/view',array('list_id'=>$this->getRequest()->getParam('list_id')));
    }
    
    public function indexAction()
    {
        $items = array();
        $items = $this->getRequest()->getPost('item_ids');
        $list_id = $this->getRequest()->getParam('list_id');
        $list = $this->_getList($list_id);
        if (sizeof($items)<1)
        {
            $this->_getSession()->addError($this->__('No selected items. Please check items.'));
            return $this->getResponse()->setRedirect($this->_getRefererUrl());
        }
        if (Mage::getStoreConfig('aitproductslists/customer/reset'))
        {
            $list->clearDiscount()->save();
        }
       
        $params = $this->getRequest()->getParams();
   
        switch ($this->getRequest()->getPost('action'))
        {
            case "add_to_cart":
                foreach ($items as $itemId)
                {
                    $this->_sendProductToCart($itemId,$params);
                } 
                $this->_getCart()->save();
                return $this->getResponse()->setRedirect($this->_getRefererUrl());
            break;

            case "copy":
                foreach ($items as $itemId)
                {
                    $this->_copyProductToList($itemId,$params);
                }
                return $this->_redirect('*/list/view', array('list_id' => $list_id));
                break;

            case "move":
                foreach ($items as $itemId)
                {
                     $this->_moveProductToList($itemId,$params);
                }
                return $this->_redirect('*/list/view', array('list_id' => $list_id));
                break;

            case "remove":
                foreach ($items as $itemId)
                {
                    $this->_removeProductFromList($itemId,$params);
                }
                return $this->_redirect('*/list/view', array('list_id' => $list_id));
                break;
        
        }
       // return $this->_goBack();
    }
    
    protected function _sendProductToCart($itemId,$params)
    {
        $this->_prepareProductParams($itemId);
        $params = $this->getRequest()->getParams();   
        $cart   = $this->_getCart();
        try {
            $item = $this->_initItem($itemId);
            $quoteId = $item->getQuoteId();
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            $item = $quote->getItemById($itemId);
            if ($item->getQty()) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($item->getQty());
            }
           
            $product = $this->_initProduct();
            /**
             * Check product availability
             */
            if (!$product) {
                return;
            }   
            
            
            $info = $item->getOptionByCode('info_buyRequest');
            $info = unserialize($info->getValue());
            $info = $this->_ifAitdoubleproduct($info,$item,$product);
                       
            $params = array_merge($params,$info);
            $cart->addProduct($product, $params);
            
            $this->_getCartSession()->setCartWasUpdated(true);

            Mage::dispatchEvent('ait_checkount_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest())
            );
            if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                    $this->_getSession()->addSuccess($message);
            }
        } 
        catch (Exception $e) 
        {
            Mage::logException($e);
            return $this->_getSession()->addException($e, $this->__('Cannot add %s to shopping cart.',Mage::helper('core')->htmlEscape($product->getName())));
        }
        return true;
    }
    
        /**
     * Prepare options array for info buy request
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    protected function _prepareOptionsForRequest($item)
    {
        $newInfoOptions = array();
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $item->getProduct()->getOptionById($optionId);
                $optionValue = $item->getOptionByCode('option_'.$optionId)->getValue();

                $group = Mage::getSingleton('catalog/product_option')->groupFactory($option->getType())
                    ->setOption($option)
                    ->setQuoteItem($item);

                $newInfoOptions[$optionId] = $group->prepareOptionValueForRequest($optionValue);
            }
        }
       return $newInfoOptions;
    }
    
    protected function _copyProductToList($itemId,$params,$move=false)
    {
        $this->_prepareProductParams($itemId);
        $request = array();
         try {
            $requestData = $this->getRequest()->getParams();
            $listId = $this->getRequest()->getParam('list_id');
            $parentList = $this->_getList()->load($listId);
            $parentQuote = $parentList->getQuote();
            
            $list = $this->_getList()->load($params['to_list']);
            $quote = Mage::getModel("aitproductslists/quote")->load($list->getQuoteId());
            $quote->setStoreId(Mage::app()->getStore()->getStoreId());
            
            $item = $parentQuote->getItemById($itemId);
            $product = $item->getProduct();
         
            
            if ($issetItem = $quote->getItemByProduct($product))
            {
                $quote->updateItem($issetItem->getId(),array('qty'=>$issetItem->getQty()+$item->getQty()));
            }
            else
            {
            $info = $item->getOptionByCode('info_buyRequest');
           
        
            $info = unserialize($info->getValue());
            $info = $this->_ifAitdoubleproduct($info,$item,$product);
            
            $request = new Varien_Object($info);
            $request->setQty($item->getQty());
                $newItem = $quote->addProduct($product, $request);
            }
            $quote->collectTotals()->save();
            foreach ($quote->getAllItems() as $_item)
            {
                 Mage::getModel('aitproductslists/list_item')->addFromController($_item->getId());
            }
            
            if (!$move)
            {
                $this->_getCartSession()->addSuccess($this->__('Product %s was copied to %s',$product->getName(),$list->getName()));
            }
            else
            {
                $this->_getCartSession()->addSuccess($this->__('Product %s was moved to %s',$product->getName(),$list->getName()));
            }
          //  return $this->_redirect('*/list/view', array('list_id' => $listId));
        }
        catch (Exception $e)
        {
            $this->_getCartSession()->addError($this->__($e->getMessage()));
        }
    }
    
    protected function _ifAitdoubleproduct($info,$item,$product)
    {
        //  manipulate with product options
            if($product->getTypeId() == 'simple') {
                if(isset($info['super_product_config'])) {
                foreach ($item->getOptionsByCode() as $option)
                {
                    $tmpInfo[$option->getCode()] = $option->getValue();
                }
                    $info = array();
                    $info['product'] = $product->getId();
                    $info['related_product'] = '';
                    $info['options'] = array();
                    if(isset($tmpInfo['option_ids'])) {
                        foreach(explode(",",$tmpInfo['option_ids']) as $opt) {
                            $info['options'][$opt] = $tmpInfo['option_'.$opt];
                        }
                    }
                }
            }
            // FINISH Aitdoubleproduct manipulate with product options
            
            return $info;
    }
    protected function _moveProductToList($itemId,$params)
    {
        try {
            $iItemId = $itemId;
            $this->_copyProductToList($itemId,$params,true);
        //    $this->getRequest()->setParams($params);
            $this->_removeProductFromList($iItemId, $params);
        //    $this->getRequest()->setParam('return_url', Mage::getUrl("aitproductslists/list/grid"));
        }
        catch (Exception $e)
        {
        //      $this->_getCartSession()->addError($this->__($e->getMessage()));
        }
    }
    
    protected function _removeProductFromList($itemId,$params)
    {
        $this->_prepareProductParams($itemId);
        try {
            $params = $this->getRequest()->getParams();
            $this->getRequest()->setParam('id',$itemId);  
            $this->getRequest()->setParam('list_id',$params['list_id']);  
            $this->deleteItemAction();
        }
        catch (Exception $e)
        {
        //    $this->_getCartSession()->addError($this->__($e->getMessage()));
        }    
    }
} } 