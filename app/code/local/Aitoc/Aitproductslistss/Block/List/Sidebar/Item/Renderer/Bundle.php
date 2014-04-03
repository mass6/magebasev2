<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/List/Sidebar/Item/Renderer/Bundle.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ yBZaDjmZMwojoQTP('f7ac23c0dc8a4c6348ff3fb6a4903fcb'); ?><?php
class Aitoc_Aitproductslists_Block_List_Sidebar_Item_Renderer_Bundle extends Aitoc_Aitproductslists_Block_List_Sidebar_Item_Renderer
{
    /**
     * Get bundled selections (slections-products collection)
     *
     * Returns array of options objects.
     * Each option object will contain array of selections objects
     *
     * @return array
     */
    protected function _getBundleOptions($useCache = true)
    {
        return Mage::helper('bundle/catalog_product_configuration')->getBundleOptions($this->getItem());
    }

    /**
     * Obtain final price of selection in a bundle product
     *
     * @param Mage_Catalog_Model_Product $selectionProduct
     * @return decimal
     */
    protected function _getSelectionFinalPrice($selectionProduct)
    {
        return Mage::helper('bundle/catalog_product_configuration')->getSelectionFinalPrice($this->getItem(), $selectionProduct);
    }

    /**
     * Get selection quantity
     *
     * @param int $selectionId
     * @return decimal
     */
    protected function _getSelectionQty($selectionId)
    {
        return Mage::helper('bundle/catalog_product_configuration')->getSelectionQty($this->getProduct(), $selectionId);
    }

    /**
     * Overloaded method for getting list of bundle options
     * Caches result in quote item, because it can be used in cart 'recent view' and on same page in cart checkout
     *
     * @return array
     */
    public function getOptionList()
    {
        return Mage::helper('bundle/catalog_product_configuration')->getOptions($this->getItem());
    }

    /**
     * Return cart backorder messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = $this->getData('messages');
        if (is_null($messages)) {
            $messages = array();
        }
        $options = $this->getItem()->getQtyOptions();

        foreach ($options as $option) {
            if ($option->getMessage()) {
                $messages[] = array(
                    'text' => $option->getMessage(),
                    'type' => ($this->getItem()->getHasError()) ? 'error' : 'notice'
                );
            }
        }

        return $messages;
    }
} } 