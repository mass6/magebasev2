<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/List/Sidebar/Item/Renderer.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ DkgBMmZgaipmpchZ('612bfedb4d2d931235423f4057dce201'); ?><?php
class Aitoc_Aitproductslists_Block_List_Sidebar_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{
    /** @var Mage_Checkout_Model_Session */
    protected $_checkoutSession;
    protected $_item;
    protected $_productUrl = null;
    protected $_productThumbnail = null;

    private function _getListSession()
    {
        return Mage::getSingleton('aitproductslists/session');
    }
    /**
     * Get item configure url
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        $url = Mage::getUrl('aitproductslists/list/configure',array('id'=>$this->getItem()->getId(),'list_id'=>$this->_getListSession()->getCurrentListId()));
        return $url;
    }

    /**
     * Get item delete url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            'aitproductslists/list/deleteItemSidebar',
            array(
                'id'=>$this->getItem()->getId(),
                'list_id'=>$this->_getListSession()->getCurrentListId()
            )
        );
    }

    /**
     * Get quote item qty
     *
     * @return mixed
     */
    public function getQty()
    {
        return $this->getItem()->getQty()*1;
    }

    /**
     * Check item is in stock
     *
     * @deprecated after 1.4.2.0-beta1
     * @return bool
     */
    public function getIsInStock()
    {
        if ($this->getItem()->getProduct()->isSaleable()) {
            if ($this->getItem()->getProduct()->getStockItem()->getQty() >= $this->getItem()->getQty()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        if (null === $this->_checkoutSession) {
            $this->_checkoutSession = Mage::getSingleton('checkout/session');
        }
        return $this->_checkoutSession;
    }

    /**
     * Retrieve item messages
     * Return array with keys
     *
     * text => the message text
     * type => type of a message
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = array();
        $quoteItem = $this->getItem();

        // Add basic messages occuring during this page load
        $baseMessages = $quoteItem->getMessage(false);
        if ($baseMessages) {
            foreach ($baseMessages as $message) {
                $messages[] = array(
                    'text' => $message,
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

    /**
     * Accept option value and return its formatted view
     *
     * @param mixed $optionValue
     * Method works well with these $optionValue format:
     *      1. String
     *      2. Indexed array e.g. array(val1, val2, ...)
     *      3. Associative array, containing additional option info, including option value, e.g.
     *          array
     *          (
     *              [label] => ...,
     *              [value] => ...,
     *              [print_value] => ...,
     *              [option_id] => ...,
     *              [option_type] => ...,
     *              [custom_view] =>...,
     *          )
     *
     * @return array
     */
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

    /**
     * Check whether Product is visible in site
     *
     * @return bool
     */
    public function isProductVisible()
    {
        return $this->getProduct()->isVisibleInSiteVisibility();
    }

    /**
     * Return product additional information block
     *
     * @return Mage_Core_Block_Abstract
     */
    public function getProductAdditionalInformationBlock()
    {
        return $this->getLayout()->getBlock('additional.product.info');
    }
} } 