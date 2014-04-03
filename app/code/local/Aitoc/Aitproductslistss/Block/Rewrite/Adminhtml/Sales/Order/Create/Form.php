<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Rewrite/Adminhtml/Sales/Order/Create/Form.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ mearZDMagipDpNRW('4213b98c2acc7aee33cc53a9cd617f4c'); ?><?php
class Aitoc_Aitproductslists_Block_Rewrite_Adminhtml_Sales_Order_Create_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Form
{
    public function _toHtml()
    {
        $html = parent::_toHtml();
        $session = Mage::getSingleton('adminhtml/session_quote');
        if(Mage::app()->getRequest()->getParam('ait_ajax_reload') AND $session->getCreateOrder())
        {
            $session->setCreateOrder(false);
            $html .= "
                <script type=\"text/javascript\">
                Event.observe(window, 'load', function() {
                    var params = ".Mage::app()->getRequest()->getParam('lists').";
                    var indicator = true;
                    var area = \"".Mage::app()->getRequest()->getParam('block')."\";
                    var url = order.loadBaseUrl;
                    if (area) {
                        area = order.prepareArea(area);
                        url += 'block/' + area;
                    }
                    if (indicator === true) indicator = 'html-body';
                    params = order.prepareParams(params);
                    params.json = true;
                    if (!order.loadingAreas) this.loadingAreas = [];
                    if (indicator) {
                        order.loadingAreas = area;
                        new Ajax.Request(url, {
                            parameters:params,
                            loaderArea: indicator,
                            onSuccess: function(transport) {
                               order.sidebarApplyChanges()
                            }.bind(this)
                        });
                    }
                })
            </script>
            ";
        }
        return $html;
    }
} } 