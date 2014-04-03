<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Rewrite/Sales/Quote.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ yBZaDjmZMwojoQTP('e7928a10b361d901631a31547bab2ce1'); ?><?php
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc. 
 */

class Aitoc_Aitproductslists_Model_Rewrite_Sales_Quote extends Mage_Sales_Model_Quote
{
    protected function _addCatalogProduct(Mage_Catalog_Model_Product $product, $qty = 1)
    {
        $newItem = false;
        $item = $this->getItemByProduct($product);
        
        if (!$item) {
            $item = Mage::getModel('sales/quote_item');
            $item->setQuote($this);
            if (Mage::app()->getStore()->isAdmin()) {
                $item->setStoreId($this->getStore()->getId());
            }
            else {
                $item->setStoreId(Mage::app()->getStore()->getId());
            }
            $newItem = true;
        }

        /**
         * We can't modify existing child items
         */
        if ($item->getId() && $product->getParentProductId()) {
            return $item;
        }

        $item->setOptions($product->getCustomOptions())
            ->setProduct($product);

        // AITOC FIX START
        // Add only item that is not in quote already (there can be other new or already saved item
        if ($newItem) {
            if ($product->getIsAitocProductList())
            {
                $item->addOption(array(
                    'code' => 'aitproductslists',
                    'value' => $product->getIsAitocProductList(),
                    'product_id' => $product->getId(),
                    'product' => $product,
                ));
            }
            $this->addItem($item);
        }
        // AITOC FIX END

        return $item;
    }
} } 