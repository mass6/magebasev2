<?php
class Dtn_Mobiapp_AllcatController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $categories =  Mage::getModel('catalog/category')
                    ->getCollection()
                    ->addAttributeToSelect('*')
                    //->addFieldToFilter('is_active',1)
                    //->addFieldToFilter('include_in_menu',1)
                    ->addFieldToFilter('parent_id',2);
        $cat = array();
        if(count($categories)>0)
        {
			foreach($categories as $category)
			{
				//if($category->getProductCount()>0)
				//{
					$cat_id = $category->getId();
					$name = $category->getName();
					$image = $category->getImageUrl();
					$parentId = $category->getParentId();
					$nappUpdate= $category->getUpdatedAt();
					$catArr = array("id"=>$cat_id, "parentId"=>$parentId, "title"=>$name, "image"=>$image, "nappUpdate"=>$nappUpdate);
					$cat[] = $catArr;
				//}
			}
		}
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($cat));
    }
    public function catByIdAction()
    {
        $cat_id = $this->getRequest()->getParam('id');
        $cat_id = (int)$cat_id;
        $child = $this->getRequest()->getParam('itemid');
        $child = (int)$child;
        $category = Mage::getModel('catalog/category')->load($cat_id);
        if($category->getId() == $cat_id)
        {
            $categories = $category->getChildren();
             $cat = array();
            if(strlen($categories)>0)
            {
            $categories = explode(',',$categories);
			//array_shift($categories);
            if($child>0)
            {
                if(in_array($child,$categories))
                {
                    $category = Mage::getModel('catalog/category')->load($child);
                    $cat_id = $category->getId();
    				$name = $category->getName();
    				$image = $category->getImageUrl();
                    if($image == false) $image = '';
    				$parentId = $category->getParentId();
    				$nappUpdate= $category->getUpdatedAt();
    				$catArr = array("id"=>$cat_id, "parentId"=>$parentId, "title"=>$name, "image"=>$image, "nappUpdate"=>$nappUpdate);
    				$cat[] = $catArr;
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($cat));
                    return;
                }
                else
                {
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($cat));
                    return;
                }
            }           
            foreach($categories as $category)
            {
                $category = Mage::getModel('catalog/category')->load($category);
				//if($category->getIsActive() == 1 && $category->getIncludeInMenu() == 1 && $category->getProductCount() > 0)
				//{
					$cat_id = $category->getId();
					$name = $category->getName();
					$image = $category->getImageUrl();
                    if($image == false) $image = '';
					$parentId = $category->getParentId();
					$nappUpdate= $category->getUpdatedAt();
					$catArr = array("id"=>$cat_id, "parentId"=>$parentId, "title"=>$name, "image"=>$image, "nappUpdate"=>$nappUpdate);
					$cat[] = $catArr;
				//}
            }
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($cat));
            
        }

    }
    public function productByCatAction()
    {
        clearstatcache();
		$time_start = microtime(true);
		
        $cat_id = $this->getRequest()->getParam('id');
        $limit = $this->getRequest()->getParam('limit');
        $offset = $this->getRequest()->getParam('offset');
        $child = $this->getRequest()->getParam('itemid');
        $child = (int)$child;
        $cat_id = (int)$cat_id;
        $limit = (int)$limit;
        $offset = (int)$offset;
        if($limit>0)
			$numpage = $offset/$limit + 1;
        else 
        {
            $limit=9999999;
            $numpage = 1;
        }
		
		$category = Mage::getModel('catalog/category')->load($cat_id, array('*'));

		//$categories = $category->getAllChildren();
		//$categories = $categories = explode(',',$categories);
		//array_shift($categories);
		//Mage::log($category);
		$p = array();
		//for($i=0;$i<count($categories);$i++)
		//{
			//$category = Mage::getModel('catalog/category')->load($categories[$i]);
            
        $time_start1 = microtime(true);
		if($child>0)
		{
			$product = Mage::getModel('catalog/product')->load($child);
			if($product->getStatus() == 1)
			{
				$id = $product->getId();
				$title = $product->getName();
				$thumb = $product->getThumbnail();
				$image = $product->getImage();
				$thumb2 ='media/catalog/product'.$thumb;
				$image2 ='media/catalog/product'.$image;
				if($thumb == 'no_selection') $thumb = '';
				else $thumb = (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize(120, 120); 
				if($image == 'no_selection') $image = '';
				else $image = (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize(300, 300);
				if ($product->getTypeId() == 'giftcard'){
					$giftcart = $product->getGiftcardAmounts();
					$price = $giftcart[0]['value'];
				}else $price = $product->getFinalPrice();
				$price = number_format($price, 2);
                $old_price = number_format($product->getPrice(), 2);
				$shortdesc = $product->getShortDescription();
				$description = $product->getDescription();
				$qrcode = $product->getSku();
				$nappUpdate = $product->getUpdatedAt();
				$pArray = array("id"=>$id, "old_price"=>$old_price, "categoryid"=>$cat_id, "title"=>$title, "thumb"=>$thumb, "image"=>$image, "shortdesc"=>"", "description"=>"", "qrcode"=>$qrcode, "price"=>$price, "nappUpdate"=>$nappUpdate);
				$p[] = $pArray;
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
			return;
		   
		}
        
		$products = $category->getProductCollection()
            ->addAttributeToSelect(array('*'))
            ->setPage($numpage, $limit);

		$time_start2 = microtime(true);
        //$size = $products->getSize();
        //if($offset > $size) return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
		foreach($products as $product)
		{
			if($product->getStatus() == 1)
			{
				$id = $product->getId();
				$title = $product->getName();
				$thumb = $product->getThumbnail();
				$image = $product->getImage();
				$thumb2 ='media/catalog/product'.$thumb;
				$image2 ='media/catalog/product'.$image;
				if($thumb == 'no_selection') 
					$thumb = '';
				else 
					$thumb = (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize(120, 120); 
				if($image == 'no_selection') 
					$image = '';
				else 
					$image = (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize(300, 300);

				if($product->getTypeId() == 'giftcard') 
				{
					$giftcart = $product->getGiftcardAmounts();
					$price = $giftcart[0]['value'];
				} 
				else $price = $product->getFinalPrice();
				$price = Mage::helper('all')->getPriceHtml($price);
                $old_price = ($product->getFinalPrice() != $product->getPrice()) ? Mage::helper('all')->getPriceHtml($product->getPrice()) : '';
				$shortdesc = $product->getShortDescription();
				$description = $product->getDescription();
				$qrcode = $product->getSku();
				$nappUpdate = $product->getUpdatedAt();
				$pArray = array("id"=>$id, "categoryid"=>$cat_id, "old_price"=>$old_price, "title"=>$title, "thumb"=>$thumb, "image"=>$image, "shortdesc"=>$shortdesc, "description"=>$description, "qrcode"=>$qrcode, "price"=>$price, "nappUpdate"=>$nappUpdate);
				$p[] = $pArray;
			}
		}
        $time_end = microtime(true);
		$time = $time_end - $time_start2;
		//Mage::log("productByCatAction Time 2 to End:" . $time);		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
    }

	public function searchAction()
	{
		$q = $this->getRequest()->getParam('q');
		$filter_a = array('like'=>"%$q%");
		$products = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter(array(
				array('attribute' => 'name','like'=> $filter_a),
				array('attribute'=>'description','like'=> $filter_a),
				array('attribute'=>'short_description','like'=> $filter_a)
				));
		if(count($products)>0)
		{
			foreach($products as $product)
			{
    			if($product->getStatus() == 1)
    			{
    				$id = $product->getId();
    				$title = $product->getName();
    				$thumb = $product->getThumbnail();
                    $image = $product->getImage();
                    $thumb2 ='media/catalog/product'.$thumb;
                    $image2 ='media/catalog/product'.$image;
                    if($thumb == 'no_selection') $thumb = '';
					else $thumb = (string)Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(120, 120); 
					if($image == 'no_selection') $image = '';
					else $image = (string)Mage::helper('catalog/image')->init($product, 'image')->resize(310, 310);
                    if($product->getTypeId() == 'giftcard') 
                    {
                        $giftcart = $product->getGiftcardAmounts();
                        $price = $giftcart[0]['value'];
                    } 
    				else $price = $product->getFinalPrice();
                    $price = number_format($price,2);
    				$shortdesc = $product->getShortDescription();
    				$description = $product->getDescription();
    				$qrcode = $product->getSku();
    				$nappUpdate = $product->getUpdatedAt();
    				$pArray = array("id"=>$id, "title"=>$title, "thumb"=>$thumb, "image"=>$image, "shortdesc"=>$shortdesc, "description"=>$description, "qrcode"=>$qrcode, "price"=>$price, "nappUpdate"=>$nappUpdate);
    				$p[] = $pArray;
    			}
			}
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
		}
		else
		{
            $this->getResponse()->setBody(json_encode(array('result'=>'error','message'=>'No product have search')));
		}
	}
	function addcartAction()
	{   
        Mage::log("INFO: " . __METHOD__ . " - " . __LINE__ , null, "mobileapp.log");
        
        //$productId = $this->getRequest()->getParam('productid');
        $productIds = $this->getRequest()->getParam('productid');
        $productIds = explode(',',$productIds);            
        $userid = $this->getRequest()->getParam('session');
        $qtys = $this->getRequest()->getParam('qty');
        $qtys = explode(',',$qtys);
        $productIds = array_combine($productIds,$qtys);
        $date = $this->getRequest()->getParam('date');
        $secret_keyurl = $this->getRequest()->getParam('secret_key');
        $secret_key = Mage::helper('mobiapp')->getKey();
        $session = Mage::getSingleton('customer/session');
        if(Mage::helper('customer')->isLoggedIn())
        {           
            $customer = $session->getCustomer()->getData();            
	    }
        
        //if($secret_keyurl == $secret_key)
        //{
            if(isset($customer)&&$customer['entity_id'] == $userid || $secret_keyurl == $secret_key)
            {
                $cart = Mage::getSingleton('checkout/cart');
                $cart->init();
                $allcart = Mage::getBlockSingleton('checkout/cart_sidebar')->getRecentItems();
                $pis = array();
                foreach($allcart as $item)
                {
                    $pis[]=$item->getProductId();
                }
               try
               {
                 
                    foreach($productIds as $productId => $qty)
                    {
                        $product = Mage::getModel('catalog/product')->load($productId);
                        $able = $product->isSaleable ();
                        if($productId == $product->getId())
                        {
                            if($able == 1)
                            {
                                if(!in_array($productId,$pis))
                                {
                                    $cart->addProduct($product,$qty);
                                    $_result_log = json_encode(array('status'=>'success','message'=>'Add product in cart success'));
                                    Mage::log("SUCCESSS: " . __METHOD__ . " - " . __LINE__ , null, "mobileapp.log");
                                    Mage::log($_result_log , null, "mobileapp.log");
                                   
                                    $this->getResponse()->setBody(json_encode(array('status'=>'success','message'=>'Add product in cart success')));
                                }
                                else
                                {
                                     Mage::log("ERROR: " . __METHOD__ . " - " . __LINE__ , null, "mobileapp.log");
                                     
                                     $allcart = Mage::getBlockSingleton('checkout/cart_sidebar')->getRecentItems();
                                     foreach($allcart as $cartcur)
                                     {
                                        if($cartcur->getData('product_id') == $productId) 
                                        {
                                            $data = array($cartcur->getData('item_id')=>array('qty'=>$qty));
                                            $cartData = $cart->suggestItemsQty($data);
                                            $cart->updateItems($cartData);
                                        }
                                     }
                                    $this->getResponse()->setBody(json_encode(array('status'=>'success','message'=>'Add product in cart success')));
                                }
                            }
                            else
                            {
                                 Mage::log("ERROR: " . __METHOD__ . " - " . __LINE__ , null, "mobileapp.log");
                                 $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Product out of stock')));
                                 return;                       
                            }                                                            
                        }
                        else
                        {
                             Mage::log("ERROR: " . __METHOD__ . " - " . __LINE__ , null, "mobileapp.log");
                            $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Product not available')));
                            return;
                        }
                    }
                    $cart->save();
                }
                catch(Exception $e)
                {
                    Mage::log("ERROR: " . __METHOD__ . " - " . __LINE__ , null, "mobileapp.log");
                    $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Can not add product in cart')));
                }
                
            }
            else
            {
                $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Please log in')));
            }
        /*    
        }
        else
        {
            $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Please check secret key')));
        }
        */
        
    }
    function deletecartAction()
    {
        $productIds = $this->getRequest()->getParam('productid');
        $productIds = explode(',',$productIds);            
        $userid = $this->getRequest()->getParam('session');
        $qtys = $this->getRequest()->getParam('qty');
        $qtys = explode(',',$qtys);
        $productIds = array_combine($productIds,$qtys);
        $date = $this->getRequest()->getParam('date');
        $secret_keyurl = $this->getRequest()->getParam('secret_key');
        $secret_key = Mage::helper('mobiapp')->getKey();
        $session = Mage::getSingleton('customer/session');
        if(Mage::helper('customer')->isLoggedIn())
        {           
            $customer = $session->getCustomer()->getData();            
	    }
        //if($secret_keyurl == $secret_key)
        //{
            if(isset($customer)&&$customer['entity_id'] == $userid || $secret_keyurl == $secret_key)
            {
                $allcart = Mage::getBlockSingleton('checkout/cart_sidebar')->getRecentItems();
               foreach($productIds as $productId => $qty)
               {
                
                    foreach($allcart as $cartcur)
                    {
                    
                        if($cartcur->getData('product_id') == $productId) 
                        {
                            $qty = $cartcur->getData('qty') - 1;
                            $cart = Mage::getSingleton('checkout/cart');
                            $data = array($cartcur->getData('item_id')=>array('qty'=>$qty));
                            $cartData = $cart->suggestItemsQty($data);
                            $cart->updateItems($cartData);
                        }
                    }
                    
                }
                $cart->save();
                $this->getResponse()->setBody(json_encode(array('status'=>'success','message'=>'Delete product in cart success')));
            }
            else
            {
                $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Please log in')));
            }
        /*
        }
        else
        {
            $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Please check secret key')));
        }
        */
    }
    
    function editcartAction()
    {
        $productId = $this->getRequest()->getParam('productid');            
        $userid = $this->getRequest()->getParam('session');
        $qty = $this->getRequest()->getParam('qty');
        $date = $this->getRequest()->getParam('date');
        $secret_keyurl = $this->getRequest()->getParam('secret_key');
        $secret_key = Mage::helper('mobiapp')->getKey();
        $session = Mage::getSingleton('customer/session');
        if(Mage::helper('customer')->isLoggedIn())
        {           
            $customer = $session->getCustomer()->getData();            
	    }
        //if($secret_keyurl == $secret_key)
        //{
            if(isset($customer)&&$customer['entity_id'] == $userid || $secret_keyurl == $secret_key)
            {
                $allcart = Mage::getBlockSingleton('checkout/cart_sidebar')->getRecentItems();
                //$items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
                $cart = Mage::getSingleton('checkout/cart');
                foreach($allcart as $cartcur)
                {
                    if($cartcur->getData('product_id') == $productId) 
                    {
                        $data = array($cartcur->getData('item_id')=>array('qty'=>$qty));
                        $cartData = $cart->suggestItemsQty($data);
                        $cart->updateItems($cartData);
                    }
                }
                $cart->save();
                $this->getResponse()->setBody(json_encode(array('status'=>'success','message'=>'Edit product in cart success')));
            }
            else
            {
                $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Please log in')));
            }
        /*
        }
        else
        {
            $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Please check secret key')));
        }
        */
    }
    function latestorderAction()
    {
		$userid = $this->getRequest()->getParam('session');
        $userid = (int)$userid;
        $session = Mage::getSingleton('customer/session');
        $secret_keyurl = $this->getRequest()->getParam('secret_key');
        $secret_key = Mage::helper('mobiapp')->getKey();
        if(Mage::helper('customer')->isLoggedIn())
        {           
            $customer = $session->getCustomer()->getData();            
	    }
        //if($secret_keyurl == $secret_key)
        //{
            if(isset($customer)&&$customer['entity_id'] == $userid || $secret_keyurl == $secret_key)
            {
                $orders = Mage::getModel('sales/order')->getCollection()
                        ->addAttributeToSelect('*')
                        ->addFieldToFilter('customer_id',$customer['entity_id'])
                        ->setOrder('entity_id','desc')
                        ->setPage(1,20);
                        //->getLastItem();
               
               $p = array();
               foreach($orders as $order)
               {     
                    $order_id = $order->getEntityId();
        			$grand_total = $order->getGrandTotal();
    				$grand_total = number_format($grand_total, 2,'.','');
        			//$nappUpdate = $order->getUpdatedAt();
    				$createdAt = $order->getcreatedAt();
                    $incrementid = $order->getIncrementId();
        			$x = array("order_id"=>$order_id,"incrementid"=>$incrementid,"grand_total"=>$grand_total,"createdAt"=>$createdAt);
                    $p[]=$x;
                }
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
            }
            else
            {
                $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Please log in')));
            }
        /*
        }
        else
        {
            $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Please check secret key')));
        }
        */
    }
    function qrAction()
    {
        $productId = $this->getRequest()->getParam('id');
        $product = Mage::getModel('catalog/product')->load($productId);
        $sku = $product->getSku();
		$p = array("qr"=>$sku);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
    }
}
