<?php
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: kFkbuT14FmWJApKR3QE9iykvMGpr0gLJWvjTDjDtbI
 * Generated:   2013-03-08 08:15:21
 * File path:   app/code/local/Aitoc/Aitproductslists/controllers/Adminhtml/AitpplController.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitproductslists')){ gDkyraBkephahANV('b519cb881a21151146f0f879dd40e2ba'); ?><?php
class Aitoc_Aitproductslists_Adminhtml_AitpplController extends Aitoc_Aitproductslists_Controller_Adminhtml_Abstract
{
      public function listsAction()
    {
        $this->_initCustomer();
        $this->_initLayoutMessages('adminhtml/session');
        $this->getResponse()->setBody($this->getLayout()->createBlock('aitproductslists/adminhtml_customer_edit_tab_list_grid')->toHtml());
    }

    public function indexAction()
    {  
//       $this->_title($this->__('Catalog'))
//             ->_title($this->__('Manage Products'));

        $this->loadLayout();
        $this->_title($this->__('Aitoc Products Lists Grid'));
        $this->_setActiveMenu('customer/aitppl');
        
        $this->renderLayout();
    }
     /**
     * List grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('aitproductslists/adminhtml_list_grid')->toHtml()
        );
    }
    public function editAction()
    {
        $this->_initCustomer('customer_id');
        $this->_initList();
        
        $list = Mage::registry('current_list');
        
        if(!$list->getId())
        {
            $this->_getSession()->addNotice($this->__('Please save the list to be able to add products into it.'));
        }
        
        $this->loadLayout();
        
        if($list->getId() && $list->getCustomerId())
        {
            $customer = Mage::getModel('customer/customer')->load($list->getCustomerId());
            $this->_title($customer->getName());
        }
        
        $this->_title($list->getId() ? $list->getName() : $this->__('New List'));
        
        $this->_setActiveMenu('customer/new');

        if($aitpplData = $this->_getSession()->getAitpplData())
        {
            $list->setData($aitpplData['info']);
            $list->getReminder()->setData($aitpplData['reminder']);
            $list->getDiscount()->setData($aitpplData['discount']);
        }
        
        $this->renderLayout();
    }
    
    /**
     * Create list duplicate
     */
    public function duplicateAction()
    {
        $this->_initList();
        $list = Mage::registry('current_list');
        
        try
        {
            $newList = $list->duplicate();
            $this->_getSession()->addSuccess($this->__('The list was duplicated successfully.'));
            $this->_redirect('*/*/edit', array( 'list_id'=>$newList->getId(),'customer_id'=>$newList->getData('customer_id')));
        }
        catch (Exception $e){
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('_current'=>true));
        }
    }
    
    public function deleteAction()
    {
        $this->_initList();
        $list = Mage::registry('current_list');
        $customerId = $list->getCustomerId();
        
        try
        {
            $list->delete();
            $this->_getSession()->addSuccess($this->__('The list was deleted successfully.'));
            $this->_redirect('*/customer/edit', array('id' => $customerId, 'tab' => 'customer_info_tabs_ppl'));
        }
        catch (Exception $e){
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('_current'=>true));
        }
    }
    
    public function addAction()
    {
        $this->editAction();
    }
    
    public function declineDiscountAction()
    {
        $list = Mage::getModel('aitproductslists/list')->load($this->getRequest()->getParam('list_id'));
        $list->setDiscountListStatus(Aitoc_Aitproductslists_Model_List::AITPPL_LIST_DISCONT_STATUS_DECLINE)->save();
        $list->getDiscount()->setPrice(0)->setIsApproved(0)->save();
        $this->_getSession()->addSuccess($this->__('Discount was declined'));
        $this->_redirect("*/*/edit",array('list_id'=>$list->getId()));
    }
    public function saveAction()
    {
        $redirectBack   = $this->getRequest()->getParam('back', false);
         $listId         = $this->_getListId();
        $isEdit         = (int)($this->getRequest()->getParam('list_id') != null);
        
               
        $data = $this->getRequest()->getPost('aitppl_info');
        $customerId     = $data['customer_id'];
        $reminderData = $this->getRequest()->getPost('aitppl_reminder');
        $discountData = $this->getRequest()->getPost('aitppl_discount');
        
        if($data)
        {
            $this->_initList();
            $list = Mage::registry('current_list');

            try
            {
                $list->getReminder()->setData($reminderData);
                $list->getDiscount()->setData($discountData);
                $list->setData($data);      
               
                $list->save();
              
                $listId = $list->getId();
             
                if($isEdit)
                {
                    $this->_getSession()->addSuccess(Mage::helper('aitproductslists')->__('The list have been saved successfully.'));
                }else{
                    $this->_getSession()->addSuccess(Mage::helper('aitproductslists')->__('The list have been created successfully.'));
                }
                $this->_getSession()->unsAitpplData();
            }
            catch(Exception $e){
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setAitpplData(array('info' => $data, 'reminder' => $reminderData, 'discount' => $discountData))
                ;
                $redirectBack = true;
            }
        }
        
        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'list_id'    => $listId,
                '_current'=>true,
                'tab' => 'customer_info_tabs_ppl'
            ));
        }
        else {
            return $this->_redirect('*/customer/edit', array('id' => $customerId, 'tab' => 'customer_info_tabs_ppl'));
        }
    }
    
    public function mergeAction()
    {
        $this->_initList();
        $list = Mage::registry('current_list');
        
        $data = $this->getRequest()->getPost('aitppl_info');
       
        $mergeListId = isset($data['merge'])?$data['merge']:false;
        
        try
        {
            if(!$mergeListId) {
                throw new Exception($this->__('Please select the list to merge with!'));
            }
            $mergeList = Mage::getModel('aitproductslists/list')->load($mergeListId);
           // $list->getService()->merge($mergeList, $list);
           $params = array( 'list_id'=>$list->getId(),
                            'merge_list'=>$mergeListId
                            );
            $list->getService()->merge($params, Aitoc_Aitproductslists_Model_List::AITPPL_MERGE_IN);
            $mergeList->delete();
            $this->_getSession()->addSuccess(Mage::helper('aitproductslists')->__('Lists were merged successfully.'));
        }catch(Esception $e){
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/edit', array('_current'=>true));
    }
    
    public function massDeleteAction()
    {
        $this->_massDelete();
        $this->listsAction();
    }
    public function massDeleteGridAction()
    {
        $this->_massDelete();
        $this->_redirect("*/*/index");
    }
    protected function _massDelete()
    {
        $ids = $this->_getListIds();
        try
        {
            foreach ($ids as $id)
            {
                Mage::getModel('aitproductslists/list')->load($id)->delete();
            }
            $this->_getSession()->addSuccess(Mage::helper('aitproductslists')->__(((count($ids)>1)?'Lists were':'The list was').'  deleted successfully.'));
        }
        catch(Exception $e){
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }
        
    }
    
    protected function getQuoteSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }
    
    public function massOrderAction()
    {
        $ids = $this->_getListIds();
        $lists = array();
        if (sizeof($ids)<1)
        {
            return $this->_getSession()->addError('No select items');
        }
        foreach ($ids as $listId)
        {
            $lists["sidebar[aitproductslists_sidebar][$listId]"] = 1;
        }
        
        $this->getQuoteSession()->clear();
        $this->getQuoteSession()
                ->setCreateOrder(true)
                ->setCustomerId((int) $this->getRequest()->getParam('id'))
                ->setStoreId((int) "1")
                ->setCurrencyId((string) 'false');
        $data = array(
                'lists'   => Mage::helper('core')->jsonEncode($lists),
                '_secure'   => true,
                'json' => "true",
                'ajax' => "true",
                'reset_shipping'=>'true',
                'ait_ajax_reload' => "true",
                'block' => "sidebar,items,shipping_method,billing_method,totals,giftmessage"
        );
     //   $this->getRequest()->setPost('sidebar',array('aitproductslists_sidebar'=>$lists));
        $this->getRequest()->setParams($data);
        $redirectUrl = $this->getUrl('adminhtml/sales_order_create/index',$data);
        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('redirect'=>$redirectUrl)));
   //     return Mage::helper('core')->"blalblavbla";
    }
    
    public function massMergeAction()
    {
        $ids = $this->_getListIds();
        try
        {
            if(count($ids)<2){
                throw new Exception($this->__('You have to choose at least 2 lists to proceed with merge!'));
            }
            
            $resultList = Mage::getModel('aitproductslists/list')->load(array_shift($ids));
            foreach ($ids as $id)
            {
                $mergeList = Mage::getModel('aitproductslists/list')->load($id);
                $resultList->merge($mergeList, $resultList);
                //$mergeList->delete();
            }
            $this->_getSession()->addSuccess(Mage::helper('aitproductslists')->__('Lists were merged successfully.'));
        }
        catch(Exception $e){
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }
        $this->listsAction();    
    }
    
    /**
     * list edit "Products" tab
     */
    public function productsAction()
    {
        $this->_initList();
        $this->_initCustomer('customer_id');
        
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * list edit "Orders" tab
     */    
    public function ordersAction()
    {
        $this->_initList();
        
        $this->_initLayoutMessages('adminhtml/session');
        $this->getResponse()->setBody($this->getLayout()->createBlock('aitproductslists/adminhtml_list_edit_tab_orders')->toHtml());    
    }
} } 