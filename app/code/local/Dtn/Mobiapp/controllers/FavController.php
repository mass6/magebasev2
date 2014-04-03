<?php

class Dtn_Mobiapp_FavController extends Mage_Core_Controller_Front_Action
{
    function allAction()
    {
        $userid = $this->getRequest()->getParam('session');
        $userid = (int)$userid;
        $session = Mage::getSingleton('customer/session');
        if(Mage::helper('customer')->isLoggedIn())
        {           
            $customer = $session->getCustomer()->getData();            
	    }
        $secret_key = Mage::helper('mobiapp')->getKey();
		if(isset($customer)&& $secret_key == $userid)
        {
			$p = array();
            $h = array();
            $r = array();
            $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
            $wishlist = $this->_getWishlist();
        if($wishlist!=NULL)
        {
            $collection = $wishlist
                ->getItemCollection();
            foreach($collection as $item)
            {
                $data = $item->getData();
                $product = Mage::getModel('catalog/product')->load($data['product_id']);
                if($product->getStatus() == 1)
				{
					$id = $product->getId();
					$title = $product->getName();
					$thumb = $product->getThumbnailUrl();
        			$image = $product->getImageUrl();
        			$thumb = (string)Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(200, 200); 
                    $image = (string)Mage::helper('catalog/image')->init($product, 'image')->resize(300, 300);
    				$price = $product->getFinalPrice();
    				//$price = number_format($price,2);
					$shortdesc = $product->getShortDescription();
					$description = $product->getDescription();
					$qrcode = $product->getSku();
					$nappUpdate = $product->getUpdatedAt();
					$pArray = array("id"=>$id, "price"=>$price, "thumb"=>$thumb, "title"=>$title);
					$r[] = $pArray;
				}
            }
            if(count($r)>0)
            {
                $p['result_code'] = '1';
                $h['total_result'] = count($r);
                $h['product_list'] = $r;
                $p['result'] = $h;
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
            }
            else
            {
                $p['result_code'] = '0';
                $p['message'] = 'no item in your wishlist';
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
            }
        }
        }
        else
        {
            $this->getResponse()->setBody(json_encode(array('result_code'=>'1','message'=>'Please log in')));
        }
    }
    function addAction()
    {
        $productIds = $this->getRequest()->getParam('id');
        $productIds = explode(',',$productIds);
        $wishlist = $this->_getWishlist();
        $userid = $this->getRequest()->getParam('session');
        $userid = (int)$userid;
        $session = Mage::getSingleton('customer/session');
        $buyRequest = new Varien_Object($this->getRequest()->getParams());
        if(Mage::helper('customer')->isLoggedIn())
        {           
            $customer = $session->getCustomer()->getData();            
	    }
        $secret_key = Mage::helper('mobiapp')->getKey();
		if(isset($customer)&& $secret_key == $userid)
        {
            for($i=0;$i<count($productIds);$i++)
            {
                $product = Mage::getModel('catalog/product')->load($productIds[$i]);
                if (!$product->getId() || !$product->isVisibleInCatalog() || $product->isSaleable() == 0) 
                {
                    $this->getResponse()->setBody(json_encode(array('result_code'=>'0','message'=>'Product not available')));
                    return;
                }
                if($wishlist!=NULL)
                {
                    $wishlist->addNewItem($productIds[$i],$buyRequest);
                    $wishlist->save();
                    Mage::dispatchEvent('wishlist_add_product', array('wishlist'=>$wishlist, 'product'=>$product));
                    $this->getResponse()->setBody(json_encode(array('result_code'=>'1','message'=>'Product : '.$productIds[$i].' added')));
                }
            }
            if($wishlist!=NULL)
            {
                Mage::helper('wishlist')->calculate();
            }
        }
        else
        {
            $this->getResponse()->setBody(json_encode(array('result_code'=>'0','message'=>'Please log in')));
        }
    }
    function removeAction()
    {
        
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        $productIds = $this->getRequest()->getParam('id');
        $productIds = explode(',',$productIds);
        $wishlist = $this->_getWishlist();
        $userid = $this->getRequest()->getParam('session');
        $userid = (int)$userid;
        $session = Mage::getSingleton('customer/session');
        if(Mage::helper('customer')->isLoggedIn())
        {           
            $customer = $session->getCustomer()->getData();            
	    }
        $secret_key = Mage::helper('mobiapp')->getKey();
		if(isset($customer)&& $secret_key == $userid)
        {
        
            if($wishlist!=NULL)
            {
                $collection = $wishlist->getItemCollection();
				$_found = false;
                foreach($collection as $item)
                {
                    if(in_array($item->getProductId(),$productIds))
                    {
                        Mage::getModel('wishlist/item')->load($item->getWishlistItemId())->delete();    
						$_found = true;
                    }
                }
				if ($_found) {
					$wishlist->save();
					Mage::helper('wishlist')->calculate();				
					$this->getResponse()->setBody(json_encode(array('result_code'=>'1','message'=>'Remove product from wishlist successfully')));
				}
				else {
					$this->getResponse()->setBody(json_encode(array('result_code'=>'0','message'=>'There is no product in wishlist')));
				}
            }
        }
        else
        {
            $this->getResponse()->setBody(json_encode(array('result_code'=>'0','message'=>'Please log in')));
        }
    }
    public function addNewItem($productId)
    {
        $item = Mage::getModel('wishlist/item');
        $item->loadByProductWishlist($this->getId(), $productId, $this->getSharedStoreIds());

        if (!$item->getId()) {
            $item->setProductId($productId)
                ->setWishlistId($this->getId())
                ->setAddedAt(now())
                ->setStoreId($this->getStore()->getId())
                ->save();
        }
        return $item;
    }
    function _getWishlist()
    {
        try 
        {
            $wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
            Mage::register('wishlist', $wishlist);
            return $wishlist;
        }
        catch (Exception $e) 
        {
           $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Please log in')));
        }
    }
}
