<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Sales/Order/Form.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ DkgBMmZgaipmpchZ('0d6436ef4c4246c13b3a827d6001d920'); ?><?php
class Aitoc_Aitproductslists_Block_Sales_Order_Form extends Aitoc_Aitproductslists_Block_Account_View
{
    public function getSaveListUrl()
    {
        return $this->getUrl("aitproductslists/list/saveOrder");
    }
} } 