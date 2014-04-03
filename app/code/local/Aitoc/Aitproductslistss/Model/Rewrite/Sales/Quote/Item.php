<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Rewrite/Sales/Quote/Item.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ ZyBegMaBroqMqICa('318c2661e19bfc2e4ace9df0bed1bb74'); ?><?php
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc. 
 */

class Aitoc_Aitproductslists_Model_Rewrite_Sales_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    /**
     * Check product representation in item
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  bool
     */
    public function representProduct($product)
    {
        $parentProductId = $product->loadParentProductIds()->getData('parent_id');
        if ($parentProductId && ($product->getId()!=$parentProductId))
        {
            $product = Mage::getModel('catalog/product')->load($parentProductId);
        }
        $itemProduct = $this->getProduct();
        if (!$product || ($itemProduct->getId() != $product->getId())) {
            return false;
        }
        
        /**
         * Check maybe product is planned to be a child of some quote item - in this case we limit search
         * only within same parent item
         */
        $stickWithinParent = $product->getStickWithinParent();
        if ($stickWithinParent) {
            if ($this->getParentItem() !== $stickWithinParent) {
                return false;
            }
        }

        // Check options
        $itemOptions    = $this->getOptionsByCode();
        $productOptions = $product->getCustomOptions();

        // START AITOC FIX
        // To tell apart Aitoc Product List items & options from other products and options in cart.
        $fakeOption = new Varien_Object(array(
                    'code' => 'aitproductslists',
                    'value' => $product->getIsAitocProductList(),
                    'product_id' => $product->getId(),
                    'product' => $product,
                ));
        
        if ($product->getIsAitocProductList())
        {
            $productOptions['aitproductslists'] = $fakeOption;
        }
        // END AITOC FIX

        if(!$this->compareOptions($itemOptions, $productOptions)){
            return false;
        }
        if(!$this->compareOptions($productOptions, $itemOptions)){
            return false;
        }
        return true;
    }
    
    public function addNotRepresentOptionCode($code)
    {
        $this->_notRepresentOptions[] = $code;
        return $this;
    }
    
    public function removeNotRepresentOptionCode($code)
    {
        unset($this->_notRepresentOptions[$code]);
        return $this;
    }
} } 