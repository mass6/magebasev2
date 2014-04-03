<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Rewrite/PageHtmlNotices.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ DkgBMmZgaipmpchZ('f9c4d7e95e80606420d88706ba0c0b03'); ?><?php

class Aitoc_Aitproductslists_Block_Rewrite_PageHtmlNotices extends Mage_Page_Block_Html_Notices
{

    public function _toHtml() {
        $html = "";
        $loggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
        $ajax = Mage::app()->getRequest()->isAjax();
        if(!$loggedIn AND !$ajax AND sizeof(Mage::getSingleton('aitproductslists/session')->getNonLoginListIds())>0 AND Mage::app()->getSafeStore()->getId() !== 0 )
        {
            $html = '<p class="demo-notice">'.Mage::helper('customer')->__('If you want your Products Lists to be saved please do login before leaving our site, otherwise Products Lists will be wiped.').'</p>';
        }
       return $html . parent::_toHtml();
    }

} } 