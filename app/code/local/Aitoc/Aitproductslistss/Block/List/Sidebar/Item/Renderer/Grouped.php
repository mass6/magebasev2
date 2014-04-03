<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/List/Sidebar/Item/Renderer/Grouped.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ gDkyraBkephahANV('5bca500cb7edcee3a6426e0957eefbfa'); ?><?php
class Aitoc_Aitproductslists_Block_List_Sidebar_Item_Renderer_Grouped extends Aitoc_Aitproductslists_Block_List_Sidebar_Item_Renderer
{
    const GROUPED_PRODUCT_IMAGE = 'checkout/cart/grouped_product_image';
    const USE_PARENT_IMAGE      = 'parent';

    /**
     * Get item grouped product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getGroupedProduct()
    {
        $option = $this->getItem()->getOptionByCode('product_type');
        if ($option) {
            return $option->getProduct();
        }
        return $this->getProduct();
    }

    /**
     * Get product thumbnail image
     *
     * @return Mage_Catalog_Model_Product_Image
     */
    public function getProductThumbnail()
    {
        $product = $this->getProduct();
        if (!$product->getData('thumbnail')
            ||($product->getData('thumbnail') == 'no_selection')
            || (Mage::getStoreConfig(self::GROUPED_PRODUCT_IMAGE) == self::USE_PARENT_IMAGE)) {
            $product = $this->getGroupedProduct();
        }
        return $this->helper('catalog/image')->init($product, 'thumbnail');
    }

    /**
     * Prepare item html
     *
     * This method uses renderer for real product type
     *
     * @return string
     */
    protected function _toHtml()
    {
        $renderer = $this->getRenderedBlock()->getItemRenderer($this->getItem()->getRealProductType());
        $renderer->setItem($this->getItem());
//        $renderer->overrideProductUrl($this->getProductUrl());
        $renderer->overrideProductThumbnail($this->getProductThumbnail());
        $rendererHtml = $renderer->toHtml();
//        $renderer->overrideProductUrl(null);
        $renderer->overrideProductThumbnail(null);
        return $rendererHtml;
    }
    
} } 