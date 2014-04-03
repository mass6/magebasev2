<?php

/**
 * Product:       Xtento_OrderExport (1.3.8)
 * ID:            RtshGK/D60/cbvmdWBMvl9/MUFw80f/wMpYXqrQnZmE=
 * Packaged:      2014-02-12T11:19:29+00:00
 * Last Modified: 2012-12-02T17:55:57+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/History.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_History extends Mage_Core_Model_Abstract
{
    /*
     * History model. Keeps track of which objects have been exported and which haven't
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('xtento_orderexport/history');
    }
}