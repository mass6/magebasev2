<?php

/**
 * Product:       Xtento_OrderExport (1.3.8)
 * ID:            RtshGK/D60/cbvmdWBMvl9/MUFw80f/wMpYXqrQnZmE=
 * Packaged:      2014-02-12T11:19:29+00:00
 * Last Modified: 2013-11-21T17:55:55+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Condition/Product.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Condition_Product extends Mage_CatalogRule_Model_Rule_Condition_Product
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('xtento_orderexport/export_condition_product');
    }

    /**
     * Load attribute options
     *
     * @return Mage_CatalogRule_Model_Rule_Condition_Product
     */
    public function loadAttributeOptions()
    {
        $productAttributes = Mage::getResourceSingleton('catalog/product')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();
        foreach ($productAttributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if (!$attribute->isAllowedForRuleCondition() /* || !$attribute->getDataUsingMethod($this->_isUsedForRuleProperty)*/) {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    public function validate(Varien_Object $object)
    {
        $product = Mage::getModel('catalog/product')->load($object->getProductId());
        #var_dump($this->getAttribute(), $product->getData($this->getAttribute()), parent::validateAttribute($product));

        return parent::validate($product);
    }
}
