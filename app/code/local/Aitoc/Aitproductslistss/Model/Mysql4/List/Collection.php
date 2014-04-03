<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/Mysql4/List/Collection.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ ZyBegMaBroqMqICa('e6c29e820d755605734b11976d511135'); ?><?php
class Aitoc_Aitproductslists_Model_Mysql4_List_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('aitproductslists/list');
    }
    
    public function getCollectionByCustomer($customerId)
    {
        $this->addFieldToFilter('customer_id', $customerId);
        return $this;
    }
    public function getSidebarCollection($customerId)
    {
    	$this->joinQuote()->getSelect()->where('main_table.customer_id = ?',(int) $customerId);
        return $this;
    }
    
    public function loadByIds($listIds)
    {
        $this->joinQuote()->getSelect()
                ->where("main_table.id IN (?)",$listIds);
        return $this;
    }
    
    public function loadByQuoteItem($itemId)
    {
        $this->joinQuote()->joinQuoteItem()
               ->getSelect()->where('quote_item.item_id =?',$itemId);
        return $this;
    }
    
    public function joinQuote()
    {        
//        $fields = array();
//        foreach (Mage::getConfig()->getFieldset('customer_account') as $code=>$node) {
//            if ($node->is('name')) {
//                //$this->addAttributeToSelect($code);
//                $fields[$code] = $code;
//            }
//        }
        $this->getSelect()
        ->join(array('quote'=>$this->getTable('sales/quote')),"quote.entity_id=main_table.quote_id"
//           ,array('*',
//           'full_name'=> 'CONCAT('
//            .(isset($fields['prefix']) ? 'IF(quote.customer_'.$fields['prefix'].' IS NOT NULL AND quote.customer_'.$fields['prefix'].' != "", CONCAT(TRIM(quote.customer_'.$fields['prefix'].')," "), ""),' : '')
//            .'TRIM(quote.customer_'.$fields['firstname'].')'.(isset($fields['middlename']) ?  ',IF(quote.customer_'.$fields['middlename'].' IS NOT NULL AND quote.customer_'.$fields['middlename'].' != "", CONCAT(" ",TRIM(quote.customer_'.$fields['middlename'].')), "")' : '').'," ",TRIM(quote.customer_'.$fields['lastname'].')'
//            .(isset($fields['suffix']) ? ',IF(quote.customer_'.$fields['suffix'].' IS NOT NULL AND quote.customer_'.$fields['suffix'].' != "", CONCAT(" ",TRIM(quote.customer_'.$fields['suffix'].')), "")' : '')
//        .')'
//            )
        );
    //    echo $this->getSelect()->__toString(); exit;
        return $this;
    }
    public function joinCustomer()
    {

//echo "<pre>"; print_r($fields); exit;
        $this->getSelect()
        ->where('main_table.customer_id <> 0')
        ->join(array('customer'=>$this->getTable('customer/entity')),"customer.entity_id=main_table.customer_id");
//        ->join(array('customer'=>$this->getTable('customer/entity_varchar')),"customer.entity_id=main_table.customer_id")
//                
//                ;
  //      echo $this->getSelect()->__toString(); exit;
        return $this;
    }

    public function joinQuoteItem()
    {
        $this->getSelect()
        ->join(array('quote_item'=>$this->getTable('sales/quote_item')),"quote_item.quote_id=quote.entity_id");
        return $this;
    }


    public function getGridList($customerId)
    {
       $this->joinQuote()->getSelect()
            ->where('main_table.customer_id = ?',$customerId)
            ->order('quote.created_at DESC');
        return $this;
    }
    
    public function getGridListById($listIds = array())
    {
        $this->joinQuote()->getSelect()
            ->where('main_table.id IN (?)',$listIds)->order('quote.created_at ASC')
           ;
        return $this;
    }
    
    public function selectUnSubscribe($customerId)
    {
        $this->getCollectionByCustomer($customerId)
                ->getSelect()
                ->where('product_change_notify_status = 1');
        return $this;
    }
    
    public function addStoreFilter($store)
    {
        $this->getSelect()
                ->where('quote.store_id=?',$store->getId());
        return $this;
    }
    
    public function addStoreToGrid()
    {
        $this->getSelect()
                ->join(array('storeview'=>$this->getTable('core/store')),'storeview.store_id = quote.store_id','')  
                ->join(array('store'=>$this->getTable('core/store_group')),'store.group_id = storeview.group_id','')  
                ->join(array('website'=>$this->getTable('core/website')),'website.website_id = storeview.website_id',array("REPLACE(REPLACE(REPLACE(`main_table`.`pattern`,'[WEBSITE]',`website`.`name`),'[STORE]',`store`.`name`),'[STOREVIEW]',`storeview`.`name`) as store_name")  )
        ->where('`storeview`.`store_id`=`quote`.`store_id`') 
        ;           
      return $this; // echo $this->getSelect()->__toString(); exit;
    }
    
    public function selectAllQuoteIds()
    {
        $this->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->distinct(true)
                ->columns('quote_id')
                ->where('quote_id <> 0');
        return $this;
    }
} } 