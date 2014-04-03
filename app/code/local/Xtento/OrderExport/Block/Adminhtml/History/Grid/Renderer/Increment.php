<?php

/**
 * Product:       Xtento_OrderExport (1.3.8)
 * ID:            RtshGK/D60/cbvmdWBMvl9/MUFw80f/wMpYXqrQnZmE=
 * Packaged:      2014-02-12T11:19:29+00:00
 * Last Modified: 2013-02-10T17:00:53+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/History/Grid/Renderer/Increment.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_History_Grid_Renderer_Increment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    public function render(Varien_Object $row)
    {
        $incrementIdFields = array('increment_id', 'order_increment_id', 'invoice_increment_id', 'shipment_increment_id', 'creditmemo_increment_id');
        foreach ($incrementIdFields as $incrementIdField) {
            if ($row->getData($incrementIdField) !== NULL) {
                return $row->getData($incrementIdField);
            }
        }
        return '';
    }
}