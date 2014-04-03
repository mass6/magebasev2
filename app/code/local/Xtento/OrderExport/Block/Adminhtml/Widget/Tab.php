<?php

/**
 * Product:       Xtento_OrderExport (1.3.8)
 * ID:            RtshGK/D60/cbvmdWBMvl9/MUFw80f/wMpYXqrQnZmE=
 * Packaged:      2014-02-12T11:19:29+00:00
 * Last Modified: 2013-01-18T14:24:47+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Widget/Tab.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Widget_Tab extends Mage_Adminhtml_Block_Widget_Form
{
    protected function getFormMessages()
    {
        $formMessages = array();
        return $formMessages;
    }

    protected function _toHtml()
    {
        if ($this->getRequest()->getParam('ajax')) {
            return parent::_toHtml();
        }
        return $this->_getFormMessages() . parent::_toHtml();
    }

    protected function _getFormMessages()
    {
        $html = '<div id="messages"><ul class="messages">';
        foreach ($this->getFormMessages() as $formMessage) {
            $html .= '<li class="' . $formMessage['type'] . '-msg"><ul><li><span>' . $formMessage['message'] . '</span></li></ul></li>';
        }
        $html .= '</ul></div>';
        return $html;
    }
}