<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Account/List/Share.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ ZyBegMaBroqMqICa('df48b5811ef65e35f4f2d9f92662f2dd'); ?><?php
class Aitoc_Aitproductslists_Block_Account_List_Share extends Aitoc_Aitproductslists_Block_Account_List_Abstract
{
    public function getPublicKey()
    {
        $list = $this->getList();
        $key = $list->getPublicKey();
        /*
        if (!$key)
        {
            $publicKeyParams = array(
                $list->getQuoteId(),
                $list->getCustomerId(),
                now(),
                $list->getName()
            );
            $key = $list->generatePublicKey($publicKeyParams,true);
        }
        */
        return $this->getUrl('aitproductslists/share',array('key'=>$key));
    }
    
    public function getEmailTemplate()
    {
        $html = $this->getPublicKey();
        return $html;
    
    }
    
    public function getPreviewUrl()
    {
        return $this->getUrl("aitproductslists/share/preview",array('list_id'=>$this->getList()->getId()));
        
    }
    
} } 