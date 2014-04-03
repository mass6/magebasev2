<?php 

class Dmx_AttributeIcons_Block_Catalog_Layer_State extends Mage_Catalog_Block_Layer_State{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('attributeicons/catalog/layer/state.phtml');
    }
    
}