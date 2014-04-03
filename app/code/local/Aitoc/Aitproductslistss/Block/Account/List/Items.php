<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Block/Account/List/Items.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ amejBgrekphghUcr('184974a751c051712ddebfe7915c882c'); ?><?php
class Aitoc_Aitproductslists_Block_Account_List_Items extends Aitoc_Aitproductslists_Block_Account_List_Abstract
{
    protected $_items;


    public function __construct()
    {
        parent::__construct();
        $quote = $this->_getQuote();
        $collection = Mage::getModel('aitproductslists/quote_item')
                ->getCollection()->noParrent($quote)->addNote();
    
        $this->_items = $collection;

    }
    
    public function getSubtotal()
    {
        return $this->_getQuote()->getSubtotal();
    }

    public function getTotalQty()
    {
        return $this->_getQuote()->getItemsQty()*1;
    }


    public function getItems()
    {
        return $this->_items;
    }
    public function getVisibleItems()
    {
        $items = array();
        foreach ($this->getItems() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                $items[] =  $item;
            }
        }
        return $this->getItems();
    }
    
    public function getEditUrl($item)
    {
        //$url = $item->getProduct()->getProductUrl();
//        $url .= "?list_id=".$this->getList()->getId();
//        $url .= "&qty=".$item->getQty()*1;
        $url = Mage::getUrl('*/*/configure',array('id'=>$item->getId(),'list_id'=>$this->getList()->getId())); 
        return $url;
    }
    protected function _prepareLayout()
    {
       

        $pager = $this->getLayout()->createBlock('page/html_pager', 'aitproductslists.account.list.items.pager')
            ->setCollection($this->getItems());
        $this->getItems()->load();
        $this->setChild('pager', $pager);
        foreach ($this->getItems() as $_item)
        {
           $this->checkSalable($_item);
           $this->checkAvalible($_item);
           $this->checkQty($_item);
        }        
	parent::_prepareLayout();         
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
//    public function canDiscount() {
//        return parent::canDiscount($this->getList());
//    }
} } 