<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Account/List/Abstract.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ kgDZjeyDmhieiRUE('cd05980ffcc30a05c343a94a865dfa4c'); ?><?php
class Aitoc_Aitproductslists_Block_Account_List_Abstract extends Aitoc_Aitproductslists_Block_Account_Abstract
{
    public function getCustomerLists()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn())
        {
        return Mage::getModel('aitproductslists/list')->getCollectionByCustomer(Mage::getSingleton('customer/session')->getCustomerId());
        }
            
        return Mage::getModel('aitproductslists/list')->loadByIds(Mage::getSingleton('aitproductslists/session')->getNonLoginListIds());
    }
    
    public function getList()
    {
        $listId = (int) $this->getRequest()->getParam('list_id');
        if ($listId)
        {
            return Mage::getModel('aitproductslists/list')->load($listId);
        }
        return Mage::getModel('aitproductslists/list')->setQuoteId(null);
    }
    
    protected function _getQuote()
    {
        $list = $this->getList();
        return $list->getQuote();
    }
    
    public function getProductType($item)
    {
        return $item->getProductType();
    }
    
    
    public function getChildItems($item) {
       return $collection = $this->_getQuote()->getItemsCollection()
            ->addFieldToFilter('parent_item_id', array('eq' => $item->getId()));
    }
    
    public function getItemOptions($item)
    {
        $options = array();
        $helper = Mage::helper('catalog/product_configuration');   
        if ($item->getProductType() !=="configurable")
        {
       //     print_r($helper->getCustomOptions($item));
            return $helper->getCustomOptions($item);    
            
        }
        $options = $helper->getConfigurableOptions($item);
        
        return $options;  
    }
    
    public function getCustomOptions($item)
    {
        $product = $item->getProduct();
        $options = array();
        $optionIds = $item->getOptionByCode('option_ids');
        if ($optionIds) {
            $options = array();
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option) {
                    $itemOption = $item->getOptionByCode('option_' . $option->getId());
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItem($item)
                        ->setConfigurationItemOption($itemOption);

                    if ('file' == $option->getType()) {
                        $downloadParams = $item->getFileDownloadParams();
                        if ($downloadParams) {
                            $url = $downloadParams->getUrl();
                            if ($url) {
                                $group->setCustomOptionDownloadUrl($url);
                            }
                            $urlParams = $downloadParams->getUrlParams();
                            if ($urlParams) {
                                $group->setCustomOptionUrlParams($urlParams);
                            }
                        }
                    }

                    $options[] = array(
                        'label' => $option->getTitle(),
                        'value' => $group->getFormattedOptionValue($itemOption->getValue()),
                        'print_value' => $group->getPrintableOptionValue($itemOption->getValue()),
                        'option_id' => $option->getId(),
                        'option_type' => $option->getType(),
                        'custom_view' => $group->isCustomizedView()
                    );
                }
            }
        }

        $addOptions = $item->getOptionByCode('additional_options');
        if ($addOptions) {
            $options = array_merge($options, unserialize($addOptions->getValue()));
        }

        return $options;
    }
    
    public function getFormatedOptionValue($optionValue)
    {
        /* @var $helper Mage_Catalog_Helper_Product_Configuration */
        $helper = Mage::helper('catalog/product_configuration');
        $params = array(
            'max_length' => 55,
            'cut_replacer' => ' <a href="#" class="dots" onclick="return false">...</a>'
        );
        return $helper->getFormattedOptionValue($optionValue, $params);
    }
    
    public function checkSalable($_item)
    {
        $check = $_item->getProduct()->isInStock();
        if (!$check)
        {
          return Mage::getSingleton('aitproductslists/session')->addNotice(Mage::helper('aitproductslists')->__('Product %s out of stock',$_item->getProduct()->getName()));
        }
        return true;
    }
    public function checkAvalible($_item)
    {
        $check = $_item->getProduct()->isAvailable();
        if (!$check)
        {
          return Mage::getSingleton('aitproductslists/session')->addError(Mage::helper('aitproductslists')->__('Product %s is not avalible',$_item->getProduct()->getName()));
        }
        return true;
    }
    public function checkQty($_item)
    {
        if ($_item->getProduct()->isAvailable() AND $_item->getProduct()->getStockItem()->getQty() == 0)
        {
            return true;
        }
        $itemQty = $_item->getQty();
       
        $productAvalibleQty = $_item->getProduct()->getStockItem()->getQty();
//            if ($productAvalibleQty<$itemQty)
//        {
        $messages = $this->getItemMessages($_item);
       // echo "<pre>"; print_r($messages); echo "</pre>";
        foreach ($messages as $message)
           {
                $noticeMethod = "add".$message['type'];
                $message['text'] = str_replace("This product", $_item->getProduct()->getName(), $message['text']);
               Mage::getSingleton('aitproductslists/session')->$noticeMethod(Mage::helper('aitproductslists')->__($message['text']));
           }
           //    }
       return true;
    }
    
     public function getItemMessages($quoteItem)
    {
        $messages = array();

        // Add basic messages occuring during this page load
        $baseMessages = $quoteItem->getMessage(false);
        if ($baseMessages) {
            foreach ($baseMessages as $message) {
                if(substr_count(strtolower($message), strtolower("Pre-Order")))
                {
                    continue;                                                   # Compatibility with Pre-Order Mantis Id 27362
                }
                $messages[] = array(
                    'text' => $quoteItem->getName().": ".$message,
                    'type' => $quoteItem->getHasError() ? 'error' : 'notice'
                );
            }
        }

        // Add messages saved previously in checkout session
        $checkoutSession = $this->getCheckoutSession();
        if ($checkoutSession) {
            /* @var $collection Mage_Core_Model_Message_Collection */
            $collection = $checkoutSession->getQuoteItemMessages($quoteItem->getId(), true);
            if ($collection) {
                $additionalMessages = $collection->getItems();
                foreach ($additionalMessages as $message) {
                    /* @var $message Mage_Core_Model_Message_Abstract */
                    $messages[] = array(
                        'text' => $message->getCode(),
                        'type' => ($message->getType() == Mage_Core_Model_Message::ERROR) ? 'error' : 'notice'
                    );
                }
            }
        }

        return $messages;
    }
    

} } 