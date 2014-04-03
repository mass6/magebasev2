<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Adminhtml/Customer/Edit/Tab/List/Grid/Massaction.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ MjrkaZgrBoqZqfQO('73d7670f43b0c5ab1bdd5dbda94e12ff'); ?><?php
class Aitoc_Aitproductslists_Block_Adminhtml_Customer_Edit_Tab_List_Grid_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract
{
    public function getJavaScript()
    {
        return str_replace('var ', '', parent::getJavaScript()) . '
   
aitpplGridUpdater = function(grid, massaction, transport){
                $(grid.containerId).update(transport.responseText);
                grid.initGridAjax();
            }
            
aitpplRedirect = function(grid, massaction, transport){
            var response = transport.responseText.evalJSON();
            location.href = response.redirect;   
            }
       
        ';
    }
} } 