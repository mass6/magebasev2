<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/controllers/ShareController.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ gDkyraBkephahANV('38347235c533da7977da8784de818cd5'); ?><?php
class Aitoc_Aitproductslists_ShareController extends Mage_Core_Controller_Front_Action
{
    
    public function indexAction()
    {
        $this->loadLayout();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('Shared Products Lists'));
        }
        $this->renderLayout();
    }
    
    public function previewAction()
    {
        $this->loadLayout();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('Preview Products List'));
        }
        $this->renderLayout();
    }
    
} } 