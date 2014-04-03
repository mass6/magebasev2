<?php
class Sebi_DeactivateOnOutOfStock_Model_Observer
{
    public function salesOrderSaveAfter($observer)
    {
        $storeId = 0; //the admin store view, change this if you want to disable only for the store view from which the order came
        $order= $observer->getEvent()->getOrder();

        foreach ($order->getItemsCollection() as $item) {
            $stockQty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($item->getProductId())->getQty();

            if ($stockQty == 0) {
                Mage::getModel('catalog/product_status')->updateProductStatus($item->getProductId(), $storeId, Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
            }
        }

    }
}