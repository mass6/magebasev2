<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/List/Sidebar/Item/Renderer/Configurable.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ MjrkaZgrBoqZqfQO('a61f3819084f9c900d07fa83be78a670'); ?><?php
class Aitoc_Aitproductslists_Block_List_Sidebar_Item_Renderer_Configurable extends Aitoc_Aitproductslists_Block_List_Sidebar_Item_Renderer
{
    const CONFIGURABLE_PRODUCT_IMAGE= 'checkout/cart/configurable_product_image';
    const USE_PARENT_IMAGE          = 'parent';

    /**
     * Get item configurable product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getConfigurableProduct()
    {
        if ($option = $this->getItem()->getOptionByCode('product_type')) {
            return $option->getProduct();
        }
        return $this->getProduct();
    }

    /**
     * Get item configurable child product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getChildProduct()
    {
        if ($option = $this->getItem()->getOptionByCode('simple_product')) {
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
        $product = $this->getChildProduct();
        if (!$product || !$product->getData('thumbnail')
            || ($product->getData('thumbnail') == 'no_selection')
            || (Mage::getStoreConfig(self::CONFIGURABLE_PRODUCT_IMAGE) == self::USE_PARENT_IMAGE)) {
            $product = $this->getProduct();
        }
        return $this->helper('catalog/image')->init($product, 'thumbnail');
    }

    /**
     * Get item product name
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->getProduct()->getName();
    }

    /**
     * Get selected for configurable product attributes
     *
     * @return array
     */
    public function getProductAttributes()
    {
        $attributes = $this->getProduct()->getTypeInstance(true)
            ->getSelectedAttributesInfo($this->getProduct());
        return $attributes;
    }

    /**
     * Get list of all otions for product
     *
     * @return array
     */
    public function getOptionList()
    {
        /* @var $helper Mage_Catalog_Helper_Product_Configuration */
        $helper = Mage::helper('catalog/product_configuration');
        $options = $helper->getConfigurableOptions($this->getItem());
        return $options;
    }
} } 