<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Model/List/Reminder/Shedule.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ mearZDMagipDpNRW('0ee6f5ec6773535264600a331b84b169'); ?><?php
class Aitoc_Aitproductslists_Model_List_Reminder_Shedule extends Mage_Core_Model_Abstract
{
    const AITPRODUCTSLISTS_REMINDER_STATUS_READY = 'ready';
    const AITPRODUCTSLISTS_REMINDER_STATUS_COMPLETE = 'complete';
    const AITPRODUCTSLISTS_REMINDER_STATUS_ERROR = 'error';

    protected $_start_date;
    
    protected function _construct()
    {
        $this->_init('aitproductslists/list_reminder_shedule');
    }

    protected function _getPeriod()
    {
        $periods = Mage::helper("aitproductslists/list")->getShedulePeriods();
        if (isset($periods[$this->getReminder()->getPeriod()]))
        {
            return $periods[$this->getReminder()->getPeriod()];
        }
        return false;
    }
    
    protected function _getStartDate()
    {
        if ($this->_start_date)
        {
            return $this->_start_date;
        }
        $this->_start_date = $this->getReminder()->getStartDate();
        return $this->_start_date;
    }
    
    protected function _clear($listId = false)
    {
        //echo $listId;
        if (!$listId)
        {
            $listId = $this->getReminder()->getListId();
        }
        $collection = $this->getCollection()->loadByList($listId);
        if ($collection->getSize()>0)
        {
            foreach ($collection as $item)
            {
                $item->delete();
            }
        }
    }
    
    public function clear($listId)
    {
        $this->_clear($listId);
    }
    public function create()
    {
        $this->_clear();
        
       $period = $this->_getPeriod();
       for($i=0; $i<$this->getReminder()->getMaxNotifyQty(); $i++)
       {
       $start = $this->_getStartDate();
       $freq = $this->getReminder()->getFrequency();
       if ($period == "half-month")
       {
           $period = 'days';
           $freq = 14*$freq;
           
       }
       $finish = strtotime($start . " + " . $freq . ' ' . $period);
       $finish = date("Y-M-d",$finish);  # какой-то косяк. не хочет с таймстампом правильно работать
       $finish = Mage::app()->getLocale()->date($finish,
               Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
               null, false
            );
            $this->setId(null)
                ->setData('list_id',$this->getReminder()->getListId())
                ->setData('start_date',$finish->toString(Varien_Date::DATETIME_INTERNAL_FORMAT))
                ->setData('finish_date',$finish->toString(Varien_Date::DATETIME_INTERNAL_FORMAT))
                ->setData('status',self::AITPRODUCTSLISTS_REMINDER_STATUS_READY)
           ->save();
           $this->_start_date = $finish->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
       }
    }
   
    public function send($customer,$list)
    {
        $vars = array();
        $vars = $this->_prepareVars($list);
        $vars['customer_name'] = $customer->getName();
       // echo "<pre>"; print_r($vars); exit;
        $email = Mage::getConfig('aitproductslists/reminder/template');
        $name = 'Mail Reminder';
        $storeId = $list->getQuote()->getStoreId();
        
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
        
        $mailTemplate = Mage::getModel("core/email_template");
        $mailTemplate->setDesignConfig(array("area"=>"frontend", "store"=>$storeId));
        $mailTemplate->setTemplateSubject($name);
        $emailId = Mage::getStoreConfig('aitproductslists/reminder/template',$storeId);
        $mailTemplate->sendTransactional(
            $emailId,
            Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId),
            $customer->getEmail(),
            $name,
            $vars
        );
        $translate->setTranslateInline(true);
        if ($mailTemplate->getSentSuccess())
        {
            return true;
        }
        return false;
    }
    
    protected function _prepareVars($list)
    {
        $website = Mage::app()->getStore($list->getQuote()->getStoreId())->getWebsite()->getName();
        $listLink = Mage::getUrl('aitproductslists/list/view',array('list_id'=>$list->getId()));
        $name_listLink = "<a href='".$listLink."'>".$list->getName()."</a>";
        $product_table = $this->_generateProducts($list);
        $sendLink = Mage::getUrl('aitproductslists/list/sendToCart',array('list_id'=>$list->getId()));
        $cart_link = $sendLink;
        
        return array('website'=>$website,
                     'name_list_link'=>$name_listLink,
                     'product_table'=>$product_table,
                     'cart_link'=>$cart_link,
                     'list_link'=>$listLink
                    );
    }
    
    public function getItemOptions($item)
    {
        $options = array();
        $helper = Mage::helper('catalog/product_configuration');   
        if ($item->getProductType() !=="configurable")
        {
            return $helper->getCustomOptions($item);    
        }
        $options = $helper->getConfigurableOptions($item);
        
        return $options;  
    }
    
    public function getFormatedOptionValue($optionValue)
    {
        /* @var $helper Mage_Catalog_Helper_Product_Configuration */
        $helper = Mage::helper('catalog/product_configuration');
        $params = array(
            'max_length' => 55,
            'cut_replacer' => ' <a href="#" class="dots" onclick="return false">...</a>'
        );
        return $helper->getFormattedOptionValue($optionValue, $params);
    }
    
    public function getChildItems($item) {
       return $collection = $this->_getQuote()->getItemsCollection()
            ->addFieldToFilter('parent_item_id', array('eq' => $item->getId()));
    }
    
    private function _generateProducts($list)
    {
        $collection = $list->getQuote()->getAllVisibleItems();
        
        $out = '
        <table width="100%" border="1" class="aitproductslists_email_tempale" id="my-orders-table-'.  rand(1, 9999999999).'">
            <col width="1" />
            <col />
            <col width="1" />
            <col width="1" />
            <thead>
                <tr>
                    <th width="50%">'.Mage::helper('core')->__('Name').'</th>
                    <th>'.Mage::helper('core')->__('Price').'</th>
                    <th>'.Mage::helper('core')->__('Qty').'</th>
                    <th>'.Mage::helper('core')->__('Subtotal Price').'</th>
                </tr>
            </thead>';
            $_odd = '';
            foreach ($collection as $item)
            {
                $out .='       
                <tbody>
                    <tr ';
                    if($this->getProductType($item) !== 'bundle') 
                    {
                        $out .= 'class="border"';
                    }
                    $out .= ' id="order-item-row-'.$item->getId().'">
                        <td>
                        <h3 class="product-name">'.$item->getName().'</h3>';       
                        if ($_options = $this->getItemOptions($item)): 
                            $out .='<dl class="item-options">';
                                foreach ($_options as $_option) :   
                                $_formatedOptionValue = $this->getFormatedOptionValue($_option);
                                $out.='<dt>'.Mage::helper('core')->htmlEscape($_option['label']).'</dt>
                                <dd';
                                if (isset($_formatedOptionValue['full_view'])): 
                                    $out .= ' class="truncated"';
                                endif;
                                $out .= '>'.$_formatedOptionValue['value'];
                                    if (isset($_formatedOptionValue['full_view'])): 
                                    $out .='<div class="truncated_full_value">
                                        <dl class="item-options">
                                            <dt>'.Mage::helper('core')->htmlEscape($_option['label']).'</dt>
                                            <dd>'.$_formatedOptionValue['full_view'].'</dd>
                                        </dl>
                                    </div>';
                                    endif;
                                $out .='</dd>';
                                endforeach;
                            $out .='</dl>';
                        endif;
                   
                        $out .= '
                        </td>
                        <td><span class="nobr">
                        '.Mage::helper('core')->currency($item->getPrice()).'
                        </span></td>
                        <td>'. $item->getQty()*1 .'</td>
                        <td><em>'.Mage::helper('core')->currency($item->getRowTotal()).'</em></td>
                    </tr>';
                    if ( $this->getProductType($item) == 'bundle')
                    {
                        $childs = $this->getChildItems($item);
                        $_count = count ($childs);
                        foreach ($childs as $child)
                        {
                            $out .= '<tr';
                            if (++$_index==$_count)
                            {
                                $out .= ' class="border"';
                            }
                            $out .= 'id="order-item-row-'.$item->getId().'">
                                <td>&nbsp;</td>
                                <td><em style="font-size:90%;">'.$child->getName().'</em></td>
                                <td>&nbsp;</td>
                                <td><em>'.$child->getQty()*1 .'</em></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>';
                        }
                    }
                $out .= '</tbody>';
            }
            $out .= '<tfoot>';
            if ($list->checkDiscount()){
                $out .= '
                        <tr id="order-item-row-special_price">
                            <th colspan="2" style="text-align:right"><b style="color:red;">'.Mage::helper('core')->__('Discount').'</b></th>
                            <th></th>
                            <th><b style="color:red;">'.Mage::helper('core')->currency($list->getQuote()->getSubtotal()*$list->getDiscount()->getPrice()/100).'</b></th>
                            <th class="nobr"><i style="color:green">'.Mage::helper('core')->__($list->getDiscount()->getPrice()."% Off").'</i></th>
                        </tr>';
            } 
             $out .= '<tr id="order-item-row-special_price">
                            <th colspan="2" style="text-align:right"><b>'.Mage::helper('core')->__('Total').'</b></th>
                            <th><b>'.$list->getQuote()->getItemsQty()*1 .'</b></th>';
                            if ($list->checkDiscount())
                            {        
                            $out.='
                                <th><b>'.Mage::helper('core')->currency($list->getQuote()->getSubtotal()-($list->getQuote()->getSubtotal()*$list->getDiscount()->getPrice()/100)).'</b></th>
                            ';
                            }
                            else
                            {
                                $out .= '<th><b>'.Mage::helper('core')->currency($list->getQuote()->getSubtotal()).'</b></th>';
                            }
                            $out .='
                     </tr>';
            $out .='</tfoot>
        </table>';
        //    echo $out; exit;
        return $out;
    }
    
    public function cronSender()
    {
        try {
            $oList = Mage::getModel('aitproductslists/list');
            $oCustomer = Mage::getModel('customer/customer');
            $collection = $this->getCollection()->readyToSendToday();
            foreach ($collection as $item)
            {
                $list = $oList->load($item->getListId());
                $quoteItems = $list->getQuote()->getItemsCollection();
                $customer = $oCustomer->load($list->getData('customer_id'));
          
                $result = $this->send($customer,$list);
                if ($result)
                {
                    $item->setStatus(self::AITPRODUCTSLISTS_REMINDER_STATUS_COMPLETE)->save();
                }
                else
                {
                    $item->setStatus(self::AITPRODUCTSLISTS_REMINDER_STATUS_ERROR)->save();
                }
                return;
            }
        }
        catch (Mage_Core_Exception $e)
        {
            return Mage::log($e->getMessage());
        }
    }
} } 