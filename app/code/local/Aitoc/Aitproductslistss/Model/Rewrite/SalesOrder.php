<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Rewrite/SalesOrder.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ mearZDMagipDpNRW('aef7b8847fc56fc0519b9926ee7f3b16'); ?><?php
class Aitoc_Aitproductslists_Model_Rewrite_SalesOrder extends Mage_Sales_Model_Order
{

    /**
     * Order state setter.
     * If status is specified, will add order status history with specified comment
     * the setData() cannot be overriden because of compatibility issues with resource model
     *
     * @param string $state
     * @param string|bool $status
     * @param string $comment
     * @param bool $isCustomerNotified
     * @return Mage_Sales_Model_Order
     */
    protected function _setState($state, $status = false, $comment = '', $isCustomerNotified = null, $shouldProtectState = false)
    {
      //  echo $state; exit;
        if ($state == Mage_Sales_Model_Order::STATE_COMPLETE)
        {
            Mage::dispatchEvent('aitproductlists_order_status_complete', array('order'  => $this));
        }
        return parent::_setState($state, $status, $comment, $isCustomerNotified, $shouldProtectState);
    }
} } 