<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/Controller/Front/Action.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ BZEmkreyjqCrwXfd('d323e5a7ce1aef1ec2555833c655e161'); ?><?php
class Aitoc_Aitproductslists_Controller_Front_Action extends Mage_Core_Controller_Front_Action
{
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
//    public function preDispatch()
//    {
//        $exceptionActions = array('grid','index');
//        parent::preDispatch();
//        $action = $this->getRequest()->getActionName();
//        $loginUrl = Mage::helper('customer')->getLoginUrl();
//        
//        if (in_array($action,$exceptionActions) AND !Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
//            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
//        }
//    }
    
    protected function _getList()
    {
        return Mage::getModel('aitproductslists/list');
    }
    
    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
    /**
     * Get PPL session model instance
     *
     * @return Aitoc_Aitproductslists_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('aitproductslists/session');
    }
     
    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCartSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    
    /**
     * Set back redirect url to response
     *
     * @return Mage_Checkout_CartController
     */
    protected function _goBack($params = array())
    {
        
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {
            // clear layout messages in case of external url redirect
            if ($this->_isUrlInternal($returnUrl)) {
                $this->_getSession()->getMessages(true);
            }
            $this->getResponse()->setRedirect($returnUrl);
        }
        else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('aitproductslists/list');
        }
        if (count($params)>0)
        {
                $product = Mage::getModel('catalog/product')->load($params['product']);
                $message = $this->__('%s was not added to your product list. Please, create new product list.', Mage::helper('core')->htmlEscape($product->getName()));
                Mage::getSingleton('catalog/session')->addError($message);
                return $this->_redirect($product->getUrlPath());
        }
        return $this;
    }
    /**
     * Initialize item instance from request data
     *
     * @return Aitoc_Aitproductslists_Model_Quote_Item
     */
    protected function _initItem($itemId)
    {
        return Mage::helper('aitproductslists')->initItem($itemId);
    }
    
    /**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct()
    {
        return Mage::helper('aitproductslists')->initProduct();
    }
    
    /**
     * Delete PPL item action
     */
    public function deleteItemAction()
    {
        
        $id = (int) $this->getRequest()->getParam('id');
        $this->getRequest()->setParam('product',$id);
        $listId = (int) $this->getRequest()->getParam('list_id');
        if ($id) {
            try {
                $product = $this->_initProduct();
                if(!$product)
                {
                    $product = Mage::getModel('catalog/product')->load($id);
                }
                $list = $this->_getList()->load($listId);
        
                $message = $this->__('Product %s was removed from %s',$product->getName(),$list->getName());
             //   if (
                        $this->_getList()->removeItem($id,$listId); //)
               // {
                    $this->_getCartSession()->addSuccess($message);
              //  }
       
            } catch (Exception $e) {
                $this->_getCartSession()->addError($this->__('Cannot remove the item.'));
                Mage::logException($e);
            }
        }
        //return $this->_redirect($product->getUrlPath());
        
    }
    
    public function deleteItemSidebarAction()
    {
        $this->deleteItemAction();
        return $this->_redirectUrl($this->_getRefererUrl());
        
    }
    protected function _prepareProductParams($itemId)
    {
        return Mage::helper('aitproductslists')->prepareItemRequest($itemId);
    }
    
    public function saveItemsNotes($items)
    {
//        $model = Mage::getModel('aitproductslists/list_item');
//        foreach ($items as $key=>$value)
//        {
//            $model->load($key,'item_id')->setNotice($value['note'])->setItemId($key)->save();
//        }
        return true;
    }
    public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        if($this->getRequest()->getParam('list_id') AND $this->_checkAccess())
        {
                $this->setFlag('', 'no-dispatch', true);
        } 
        else {
            $this->_getSession()->setNoReferer(true);
        }
    }

//     public function preDispatch() {
//        parent::preDispatch();
//        if($this->getRequest()->getParam('list_id'))
//        {
//           return $this->_checkAccess();
//        }
//        
//    }
    protected function _checkAccess()
    {
        $access = true;
        $action = $this->getRequest()->getActionName();
        $exceptions = array('addShare');
		if (in_array($action,$exceptions))
		{
			return ;
		}
        $listId = $this->getRequest()->getParam('list_id');
        $list = Mage::getModel('aitproductslists/list')->load($listId);
        switch (Mage::getSingleton('customer/session')->isLoggedIn())
        {
            case true:
                if ($list->getData('customer_id')!==Mage::getSingleton('customer/session')->getCustomerId())
                {
                    echo 'false 1';
                    $access = false;
                }
                break;
            case false:
                if (!in_array($listId,$this->_getSession()->getNonLoginListIds()))
                {
                    echo 'false 2';
                    $access = false;
                }
                break;
        }
      
   // exit;
        if (!$access)
        {
            $this->_getSession()->addError($this->__("Access denied!"));
            return $this->_redirect('*/*/grid');
        }
    }
} } 