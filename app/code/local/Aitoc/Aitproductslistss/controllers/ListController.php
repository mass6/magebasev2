<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/controllers/ListController.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ mearZDMagipDpNRW('2d4a7af41be883fbb0292890d8c2ed4f'); ?><?php
class Aitoc_Aitproductslists_ListController extends Aitoc_Aitproductslists_Controller_Front_Action
{   
    /**
     * Grid view
     */
    public function indexAction()
    {
        $this->getResponse()->setRedirect(Mage::getUrl('aitproductslists/list/grid'));
    }
    
    /**
     * Add product to shopping cart action
     */
    public function addAction()
    {
        
        $messages = array();
        $list   = $this->_getList();
        
        $params = $this->getRequest()->getParams();
      
       try {
            if (isset($params['list_qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = (int) $filter->filter($params['list_qty']);
            }
            $this->getRequest()->setParams($params);
            $product = $this->_initProduct();
            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $listId = $this->_getSession()->getCurrentListId();
            if ($listId !== null)
            {
                $list = $list->load($listId);
                $item = $list->addProduct($product);
                if (!$item)
                {
                    return $this->_redirect($product->getUrlPath());
                }
                Mage::dispatchEvent('aitproductslists_add_product_complete',
                    array('item'=>$item,'product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );
            if (!$list->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your product list', Mage::helper('core')->htmlEscape($product->getName()));
                    $this->_getSession()->addSuccess($message);
            }
                return $this->_redirect("aitproductslists/list/view",array("list_id"=>$this->_getSession()->getCurrentListId()));
    
            }
            else
            {
                $this->_getCartSession()->addError($this->__("Please specify your Products List."));
                return $this->_redirect($product->getUrlPath());
            }
            
        } catch (Mage_Core_Exception $e) {
            $this->_getCartSession()->addError($this->__($e->getMessage())); 
             return $this->_redirect($product->getUrlPath());
        }
    }
    
    public function configureAction()
    {
        // Extract item and product to configure
        $id = (int) $this->getRequest()->getParam('id');
        $list_id = (int) $this->getRequest()->getParam('list_id');                            
        $quoteItem = null;
        $list = $this->_getList()->load($list_id);
        if ($id) {
            $quoteItem = $list->getQuote()->getItemById($id);
        }

        if (!$quoteItem) {
            $this->_getSession()->addError($this->__('Quote item is not found.'));
            $this->_goBack();
            return;
        }

        try {
            $params = new Varien_Object();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);
            $params->setBuyRequest($quoteItem->getBuyRequest());
            Mage::helper('catalog/product_view')->prepareAndRender($quoteItem->getProduct()->getId(), $this, $params);
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot configure product.'));
            Mage::logException($e);
            $this->_goBack();
            return;
        }
    }
    
     /**
     * Update product configuration for a cart item
     */
    public function updateItemOptionsAction()
    {
        $list   = $this->_getList()->load($this->getRequest()->getParam('list_id'));
        $id = (int) $this->getRequest()->getParam('item_id');
        $params = $this->getRequest()->getParams();
      
        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        try {
            if (isset($params['list_qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['list_qty']);
            }

            $quoteItem = $list->getQuote()->getItemById($id);
            if (!$quoteItem) {
                Mage::throwException($this->__('Quote item is not found.'));
            }

            $item = $list->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                Mage::throwException($item);
            }

            $list->getQuote()->collectTotals()->save();
            $list->save();
       
            Mage::dispatchEvent('aitproductslists_list_update_item_complete',
                array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$list->getQuote()->getHasError()){
                    $message = $this->__('%s was updated in your list.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                 return $this->_redirect('aitproductslists/list/view',array('list_id'=>$list->getId()));
    
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('aitproductslists/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update the item.'));
            Mage::logException($e);
            $this->_redirect('aitproductslists/list/view',array('list_id'=>$list->getId()));
        }
        $this->_redirect('*/*');
    }
    
    public function addProductToListAction()
    {
        if ($this->getRequest()->getParam('list_action') == 'exist')
        {
            $list = $this->_getList()
                    ->load($this->getRequest()->getParam('existing_lists'))
                   ;                      
            $this->getRequest()->setPost('list',$list->getData());
        }
     //   echo "<pre>"; print_r($this->getRequest()->getPost('list')); exit;
        $this->_getList()->setData($this->getRequest()->getPost('list'))->save();
        return $this->addAction();
    }
    
    public function sendToCartAction()
    {
        $list = $this->_getList()->load($this->getRequest()->getParam('list_id'));
        $quote = $list->getQuote();
       
        
        $cart = Mage::getSingleton('checkout/cart');
        //$cart->init();
       // $cart->setQuote(null)->save();
//       / $cart->init();
//        $cart
//            ->setCustomer(Mage::getSingleton('customer/session')->getCustomer())
//            ->getQuote()->getShippingAddress()->setCollectShippingRates(true)
//            ->save();
//        if (!$cart->getQuote()->getId())
//        {
//          //  echo 5;
//            $cart->getQuote()->setCustomer(Mage::getSingleton('customer/session')->getCustomer());
//            $cart->getQuote()->getBillingAddress();
//            $cart->getQuote()->getShippingAddress()->setCollectShippingRates(true);
//            $cart->getQuote()->collectTotals();
//            $cart->getQuote()->save();
//            $cart->init();
//        }
      
//        echo "<pre>"; print_r($cart->getQuote()->getData());
//        exit;
        foreach ($quote->getAllVisibleItems() as $item)
        {
        $this->_prepareProductParams($item->getId());
        $params = $this->getRequest()->getParams();
         //   $item = $this->_initItem($itemId);
            if ($item->getQty()) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($item->getQty());
            }
           
            $this->getRequest()->setParam('product',$item->getProductId());       
            $product = $this->_initProduct();  
            
            /**
             * Check product availability
             */
            if (!$product) {
                return;
            }
            try{
                $product->setIsAitocProductList($list->getId());
                $cart->addProduct($product, $params);
                $this->_getCartSession()->setCartWasUpdated(true);
            }  
            catch (Mage_Core_Exception $e)
            {
                  $this->_getSession()->addError($e->getMessage());
            }             
            
        }
            
        $cart->getQuote()->collectTotals()->save();
        $this->_getCartSession()->setCartWasUpdated(true);

        Mage::getSingleton('checkout/session')->setQuoteId($cart->getQuote()->getId());

        return $this->_redirect('checkout/cart');
    }
    
    /**
     * Customer PPLs
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('aitproductslists/session');
        $this->_initLayoutMessages('checkout/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('My Products Lists'));

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }
    
    
    /**
     * PPL view
     */
    public function viewAction($new = false)
    {                                          
     //   $this->_checkAccess();
        if (! $this->getRequest()->getParam('list_id'))
        {
           return $this->_redirect("*/*/grid");
        }
        $loggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
        $this->loadLayout();
            
        if (!$loggedIn) {

                $root = $this->getLayout()
                    ->getBlock('aitproductslists.account.list.view')
                    ->unsetChild('aitproductslists_list_share');
        }
        else
        {
            $root = $this->getLayout()
                    ->getBlock('aitproductslists.account.list.view')
                    ->unsetChild('customer_form_login');
        }
        
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('aitproductslists/list/grid');
        }

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('My Products List - %s',$this->_getList()->load($this->getRequest()->getParam('list_id'))->getName()));
        }
     
        $listId = $this->getRequest()->getParam('list_id');
        $list = Mage::getModel('aitproductslists/list')->load($listId);
//print_r(Mage::getSingleton('customer/session')->getData());
//print_r(Mage::getSingleton('aitproductslists/session')->getData()); exit;
       
        if ($loggedIn AND $message = $this->getListMessage($list))
        {
            $this->_getSession()->addNotice($message);
        }
        $this->_initLayoutMessages('aitproductslists/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }
    
    public function getListMessage($list)
    {
        $message = null;
       
        switch ($list->getDiscountListStatus())
        {
            case Aitoc_Aitproductslists_Model_List::AITPPL_LIST_DISCONT_STATUS_NO :
                if ($list->getPayQty()<Mage::getStoreConfig('aitproductslists/customer/discount'))
                {
              //       echo "<pre>"; print_r($list->getDiscount()->getData()); exit;
                    $message = $this->__(" Buy product list %s times before being able to ask for a discount on it.",$list->getDiscount()->getMinQty() ? $list->getDiscount()->getMinQty() : Mage::getStoreConfig('aitproductslists/customer/discount'));
                }
                break;
            case Aitoc_Aitproductslists_Model_List::AITPPL_LIST_DISCONT_STATUS_WAITING :
                    $message = $this->__("Waiting for discount approve by admin.");
                break;
            case Aitoc_Aitproductslists_Model_List::AITPPL_LIST_DISCONT_STATUS_DECLINE :
                $message = $this->__("Discount for this list is unapplicable.");
                break;
            case Aitoc_Aitproductslists_Model_List::AITPPL_LIST_DISCONT_STATUS_APPROVE :
                break;
        }
        return $message;
    }
    
    public function newAction()
    {
        $loggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
        $this->loadLayout();
            
        if (!$loggedIn) {

                $root = $this->getLayout()
                    ->getBlock('aitproductslists.account.list.view')
                    ->unsetChild('aitproductslists_list_share');
        }
        else
        {
            $root = $this->getLayout()
                    ->getBlock('aitproductslists.account.list.view')
                    ->unsetChild('customer_form_login');
        }
        
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('aitproductslists/list/grid');
        }

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('My Products List - Create New Products List'));
        }
     
        $this->_initLayoutMessages('aitproductslists/session');
        $this->renderLayout();
    }
    
    public function removeAction()
    {
        $this->_getList()->load($this->getRequest()->getParam('list_id'))->delete();
        $this->_getSession()->addSuccess($this->__("List was deleted."));
        return  $this->_redirect("*/*/grid");
    }
    
    public function duplicateAction()
    {
        $toList = $this->_getList()->load($this->getRequest()->getParam('list_id'))->duplicate();
        $this->_getSession()->addSuccess($this->__("List was duplicated."));
        return  $this->_redirect("*/*/view",array('list_id'=>$toList->getId()));
    }

    public function saveAction()
    {
        $this->saveItemsNotes($this->getRequest()->getParam('item'));
        if ($this->getRequest()->getParam('form_action') == 'mass_action')
        {
            return $this->_forward('index', 'massaction', 'aitproductslists');
        }
       
        $list = $this->getRequest()->getPost('list');
        $list['customer_id'] = Mage::getSingleton('customer/session')->getCustomerId();
        $list['id'] = $this->getRequest()->getParam('list_id');
       // $this->_getList()->getReminder()->setData($this->getRequest()->getPost('reminder'));
        $this->_getList()->setData($list)->save();
        $this->_getSession()->addSuccess($this->__("List was saved."));
        if ($this->getRequest()->getParam('list_id'))
        {
            return  $this->getResponse()->setRedirect($this->_getRefererUrl());
        }
        return  $this->_redirect("*/*/grid");
    }
    
    public function mergeAction()
    {
      //  echo "<pre>"; print_r($this->getRequest()->getParams()); exit;
        $this->_getList()->getService()->merge($this->getRequest()->getParams(),$this->getRequest()->getParam('merge_list_mode'));
        $this->_getSession()->addSuccess($this->__("Lists was merged."));
        if ($this->getRequest()->getPost('merge_list_delete'))
        {
            if ($new)
            {
                $this->getRequest()->setParam('list_id',$fromList2->getId());
            }
            $this->removeAction();
        }
        return  $this->_redirect("*/*/grid");
    }
    
    public function addShareAction()
    {
        $toListId = $this->_getList()
                ->load($this->getRequest()->getParam('list_id'))
                ->addShare($this->getRequest()->getParam('customer'));
        $this->_getSession()->addSuccess($this->__("Lists was added."));
        return  $this->_redirect("*/*/grid");
    }
    
    public function saveCartAction()
    {
        $list = $this->getRequest()->getPost('list');
        if (!isset($list['name']) OR $list['name']=="")
        {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('aitproductslists')->__('Please specify the name of the list.'));
            return $this->_redirect("checkout/cart/");
        }
        $list = $this->_getList()->getService()->saveFrom($this->getRequest()->getParams(),'cart');
        $this->_getCartSession()->addSuccess($this->__("List %s was created.",$list->getName()));
        if ($this->getRequest()->getParam('edit_new_list'))
        {
           return $this->getResponse()->setRedirect(Mage::getUrl('aitproductslists/list/view',array('list_id'=>$list->getId())));
        }
        return $this->_redirect("checkout/cart/");
    }
    
    public function saveOrderAction()
    {
        $list = $this->getRequest()->getPost('list');
        if (!isset($list['name']) OR $list['name']=="")
        {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('aitproductslists')->__('Please specify the name of the list.'));
            return $this->_redirect("sales/order/view",array('order_id'=>$this->getRequest()->getParam('order_id')));
        }
        $list = $this->_getList()->getService()->saveFrom($this->getRequest()->getParams(),'order');
        if ($this->getRequest()->getParam('edit_new_list'))
        {
            $this->_getSession()->addSuccess($this->__("List %s was created.",$list->getName()));
            return $this->getResponse()->setRedirect(Mage::getUrl('aitproductslists/list/view',array('list_id'=>$list->getId())));
        }
        Mage::getModel('catalog/session')->addSuccess($this->__("List %s was created.",$list->getName()));
        return $this->_redirect("sales/order/view",array("order_id"=>$this->getRequest()->getParam('order_id')));
    }
    
    public function discountAction()
    {
        $notice = array();
        $this->_getList()->getNotifier()->sendNotice('admin','discount',array('list_url'=>"<a href=\"".Mage::getUrl('adminhtml/aitppl/edit',array('list_id'=>$this->getRequest()->getParam('list_id'),'_secure'=>true))."\">" .Mage::helper('aitproductslists')->__('List')." #".$this->getRequest()->getParam('list_id')."</a>"));
        Mage::getSingleton('checkout/session')->addSuccess(Mage::helper('aitproductslists')->__('Your request sent to Administrator.'));
        $list = Mage::getModel('aitproductslists/list')->load($this->getRequest()->getParam('list_id'));
        
        $data = array(
            'notice_id'     => null,
            'date_added'    => gmdate('Y-m-d H:i:s', time()),
            'message'       => Mage::helper('aitproductslists')->__("Customer %s requested a discount for his #%s product list. You can approve the discount from the Customers->Manage Aitoc Products Lists", Mage::getModel('customer/session')->getCustomer()->getName(),$this->getRequest()->getParam('list_id')),
            'customer_id'   => (int) $list->getData('customer_id'),
            'list_id'       => (int) $list->getId(),
            'status'         => 'new'
        );
        Mage::getModel('aitproductslists/notices')->setData($data)->save();
        $list->setDiscountListStatus(Aitoc_Aitproductslists_Model_List::AITPPL_LIST_DISCONT_STATUS_WAITING)->save();
        return $this->getResponse()->setRedirect($this->_getRefererUrl());
    }
       
    /**
     * test function
     */
    public function cronSenderAction()
    {
        return Mage::getModel('aitproductslists/list_reminder_shedule')->cronSender();
    }
} } 