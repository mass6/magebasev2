<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Rewrite/SalesObserver.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ mearZDMagipDpNRW('7f4d55e7b0c4deb84519859dcaec901e'); ?><?php
class Aitoc_Aitproductslists_Model_Rewrite_SalesObserver extends Mage_Sales_Model_Observer
{
    /**
     * Clean expired quotes (cron process)
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @return Mage_Sales_Model_Observer
     */
    public function cleanExpiredQuotes()
    {
        $listCollection = Mage::getModel('aitproductslists/list')->getCollection()->selectAllQuoteIds();
        $listIds = array();
        foreach ($listCollection->getData() as $element)
        {
            $listIds[] = $element['quote_id'];
        }
        
        $lifetimes = Mage::getConfig()->getStoresConfigByPath('checkout/cart/delete_quote_after');
        foreach ($lifetimes as $storeId=>$lifetime) {
            $lifetime *= 86400;
           
            $quotes = Mage::getModel('sales/quote')->getCollection();
            /* @var $quotes Mage_Sales_Model_Mysql4_Quote_Collection */

            $quotes->addFieldToFilter('store_id', $storeId);
            $quotes->addFieldToFilter('updated_at', array('to'=>date("Y-m-d", time()-$lifetime)));
            $quotes->addFieldToFilter('is_active', 0);
            $quotes->addFieldToFilter('entity_id', array('nin'=>$listIds)); # check if not a aitoc list
            $quotes->walk('delete');
        }
        return $this;
    }
} } 