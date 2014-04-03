<?php

class Dtn_Mobiapp_CartController extends Mage_Core_Controller_Front_Action {

    function allAction() {
        $session = Mage::getSingleton('customer/session');
        $sid = $session->getSessionId();
        if (Mage::helper('customer')->isLoggedIn()) {
            $customer = $session->getCustomer()->getData();
        }
        $h = array();
        $r = array();

        $p = array();
        $allcart = Mage::getBlockSingleton('checkout/cart_sidebar')->getRecentItems();
        $cart = Mage::getSingleton('checkout/cart');
        foreach ($allcart as $cartcur) {
            $productId = $cartcur->getData('product_id');
            $product = Mage::getModel('catalog/product')->load($productId);
            if ($product->getStatus() == 1) {
                $id = $product->getId();
                $title = $product->getName();
                $thumb = $product->getThumbnailUrl();
                $image = $product->getImageUrl();
                $thumb = (string) Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(200, 200);
                $image = (string) Mage::helper('catalog/image')->init($product, 'image')->resize(300, 300);
                $price = $product->getFinalPrice();
                //$price = number_format($price, 2);
                $shortdesc = $product->getShortDescription();
                $description = $product->getDescription();
                $qrcode = $product->getSku();
                $nappUpdate = $product->getUpdatedAt();
                $qty = $cartcur->getQty();
                $pArray = array("id" => $id, "price" => $price, "qty" => $qty, "thumb" => $thumb, "title" => $title);
                $h[] = $pArray;
            }
        }
        $p['result_code'] = '1';
        $p['SID'] = $sid;
        $r['total_result'] = count($h);
        $r['product_list'] = $h;
        $p['result'] = $r;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
    }

    function addcartAction() {
        $productIds = $this->getRequest()->getParam('productid');
        $productIds = explode(',', $productIds);
        $qtys = $this->getRequest()->getParam('qty');
        $qtys = explode(',', $qtys);
        $date = $this->getRequest()->getParam('date');
        $session = Mage::getSingleton('customer/session');
        $sid = $session->getSessionId();
        if (Mage::helper('customer')->isLoggedIn()) {
            $customer = $session->getCustomer()->getData();
        }

        $cart = Mage::getSingleton('checkout/cart');
        $cart->init();
        $allcart = Mage::getBlockSingleton('checkout/cart_sidebar')->getRecentItems();
        $pis = array();
        foreach ($allcart as $item) {
            $pis[] = $item->getProductId();
        }
        try {
            for ($i = 0; $i < count($productIds); $i++) {
                $productId = $productIds[$i];

                if (!isset($qtys[$i]) || $qtys[$i] == null) {
                    $qty = 1;
                } else {
                    $qty = $qtys[$i];
                }

                $product = Mage::getModel('catalog/product')->load($productId);
                $able = $product->isSaleable();
                if ($productId == $product->getId()) {
                    if ($able == 1) {
                        if (!in_array($productId, $pis)) {
                            $cart->addProduct($product, $qty);
                            $this->getResponse()->setBody(json_encode(array('result_code' => '1', 'SID' => $sid, 'message' => 'Add product in cart success')));
                        } else {
                            $this->getResponse()->setBody(json_encode(array('result_code' => '0', 'SID' => $sid, 'message' => 'Products already in the cart')));
                        }
                    } else {
                        $this->getResponse()->setBody(json_encode(array('result_code' => '0', 'SID' => $sid, 'message' => 'Product out of stock')));
                        return;
                    }
                } else {
                    $this->getResponse()->setBody(json_encode(array('result_code' => '0', 'SID' => $sid, 'message' => 'Product not available')));
                    return;
                }
            }
            $cart->save();
        } catch (Exception $e) {
            $this->getResponse()->setBody(json_encode(array('result_code' => '0', 'message' => 'Can not add product in cart')));
        }
    }

    function editcartAction() {
        $productId = $this->getRequest()->getParam('productid');
        $qty = $this->getRequest()->getParam('qty');
        $date = $this->getRequest()->getParam('date');

        $session = Mage::getSingleton('customer/session');
        $sid = $session->getSessionId();
        if (Mage::helper('customer')->isLoggedIn()) {
            $customer = $session->getCustomer()->getData();
        }


        $allcart = Mage::getBlockSingleton('checkout/cart_sidebar')->getRecentItems();
        //$items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        $cart = Mage::getSingleton('checkout/cart');

        $found = false;
        foreach ($allcart as $cartcur) {
            if ($cartcur->getData('product_id') == $productId) {
                $data = array($cartcur->getData('item_id') => array('qty' => $qty));
                $cartData = $cart->suggestItemsQty($data);
                $cart->updateItems($cartData);
                $found = true;
                break;
            }
        }
        if ($found) {
            $cart->save();
            $this->getResponse()->setBody(json_encode(array('result_code' => '1', 'SID' => $sid, 'message' => 'Edit product in cart success')));
        } else {
            $this->getResponse()->setBody(json_encode(array('result_code' => '0', 'SID' => $sid, 'message' => 'Product does not exist in cart')));
        }
    }

    function deletecartAction() {
        $productId = $this->getRequest()->getParam('productid');
        $qty = $this->getRequest()->getParam('qty');
        $date = $this->getRequest()->getParam('date');
        $session = Mage::getSingleton('customer/session');
        $sid = $session->getSessionId();
        if (Mage::helper('customer')->isLoggedIn()) {
            $customer = $session->getCustomer()->getData();
        }

        $allcart = Mage::getBlockSingleton('checkout/cart_sidebar')->getRecentItems();
        $found = false;
        foreach ($allcart as $cartcur) {
            if ($cartcur->getData('product_id') == $productId) {
                $item_id = $cartcur->getData('item_id');
                $cart = Mage::getSingleton('checkout/cart');
                $cart->removeItem($item_id)->save();
                $found = true;
                break;
            }
        }
        if ($found) {
            $this->getResponse()->setBody(json_encode(array('result_code' => '1', 'SID' => $sid, 'message' => 'Delete product in cart success')));
        } else {
            $this->getResponse()->setBody(json_encode(array('result_code' => '0', 'SID' => $sid, 'message' => 'Product does not exist in cart')));
        }
    }

    function latestorderAction() {
        $session = Mage::getSingleton('customer/session');
        $sid = $session->getSessionId();
        if (Mage::helper('customer')->isLoggedIn()) {
            $approver = $session->getCustomer();
        }
        $result = array('result_code' => 0, 'SID' => $sid);
        if (isset($approver)) {
            // get id of approver customer category
            $appId = Mage::helper('mobiapp/attribute')->getCustomerApproverCategoryId();
            if ($approver->getCustCategory() != $appId) {
                $result['result_code'] = 0;
                $result['message'] = 'You are not an approver';
            } else {
                $limit = (int) $this->getRequest()->getParam('limit');
                if (!$limit) $limit = 10;
                $page = (int) $this->getRequest()->getParam('p');
                if (!$page) $page = 1;
                
                $orders = Mage::getResourceModel('sales/order_collection')
                        ->addAttributeToSelect('*')
                        ->addFieldToFilter('status', 'pending_approval')
                        ->addFieldToFilter('approver', $approver->getId())
                        ->setOrder('created_at', 'desc')
                        ->setPageSize($limit)
                        ->setCurPage($page)
                        ->load();
                
                $result['result_code'] = 1;
                $result['orders'] = array();
                $lastPage = $orders->getLastPageNumber();
                if ($page <= $lastPage) {
                    $contracts = array();
                    foreach ($orders as $order) {
                        $contractId = $order->getContractId();
                        if (!isset($contracts[$contractId])) {
                            $contract = Mage::getModel('web/web')->load($contractId);
                            $contracts[$contractId] = $contract->getName();
                        }
                        $result['orders'][] = array(
                            'id' => $order->getId(),
                            'increment_id' => $order->getIncrementId(),
                            'contract_id' => $contractId,
                            'contract_name' => $contracts[$contractId],
                            'created_at' => $order->getCreatedAt(),
                            'updated_at' => $order->getUpdatedAt(),
                            'grouped_by_date' => date('F d, Y', strtotime($order->getCreatedAt())),
                            'created_time' => date('g:i A', strtotime($order->getCreatedAt())),
                            'total' => strip_tags($order->formatPrice($order->getGrandTotal())),
                            'status' => $order->getStatusLabel()
                        );
                    }
                }
                
                $result['total'] = $orders->getSize();
            }
        } else {
            $result['result_code'] = 0;
            $result['message'] = 'Please log in';
        }
        $this->getResponse()->setBody(json_encode($result));
    }

    function orderdetailAction() {
        $session = Mage::getSingleton('customer/session');
        $sid = $session->getSessionId();
        if (Mage::helper('customer')->isLoggedIn()) {
            $approver = $session->getCustomer();
        }
        $result = array('result_code' => 0, 'SID' => $sid);
        if (isset($approver)) {
            $orderId = (int) $this->getRequest()->getParam('orderid');
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order->getId()) {
                $result['result_code'] = 0;
                $result['message'] = 'Order not found';
            } else {
                $result['result_code'] = 1;
                $contract = Mage::getModel('web/web')->load($order->getContractId());
                $result['order'] = array(
                    'id' => $order->getId(),
                    'increment_id' => $order->getIncrementId(),
                    'contract' => $contract->getName(),
                    'ordered_by' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                    'created_date' => date('F d, Y g:i A', strtotime($order->getCreatedAt())),
                    'total' => strip_tags($order->formatPrice($order->getGrandTotal()))
                );
                
                $comments = $order->getAllStatusHistory();
                $result['order']['comments'] = array();
                foreach ($comments as $comment) {
                    $cobj = $comment->getComment();
                    if($cobj==null){
                        $ctxt = '';
                    }else{
                        $ctxt = $cobj;
                    }
                    $result['order']['comments'][] = array(
                        'created_at' => $comment->getCreatedAt(),
                        'comment' => $ctxt
                    );
                }
                
                $items = $order->getAllItems();
                $result['order']['items'] = array();
                foreach ($items as $item) {
                    $itemUom = $item->getProduct()->getUom();
                    if (!$itemUom) $itemUom = "";
                    $result['order']['items'][] = array(
                        'name' => $item->getName(),
                        'uom' => $itemUom,
                        'ordered_qty' => number_format($item->getQtyOrdered()),
                        'sub_total' => strip_tags($order->formatPrice($item->getRowTotal()))
                    );
                }
            }
        } else {
            $result['result_code'] = 0;
            $result['message'] = 'Please log in';
        }
        $this->getResponse()->setBody(json_encode($result));
    }
    
}
