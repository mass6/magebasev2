<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/controllers/Adminhtml/Aitppl/SidebarController.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ gDkyraBkephahANV('41e0341af9ddd36d674cf605e92f4186'); ?><?php
class Aitoc_Aitproductslists_Adminhtml_Aitppl_SidebarController extends Mage_Adminhtml_Controller_Action
{
   public function loadItemsAction()
   {
       $block = $this->getLayout()->createBlock('aitproductslists/adminhtml_sidebar_items')
               ->setTemplate('aitproductslists/sidebar/items.phtml');
       echo $block->toHtml();
   }
} } 