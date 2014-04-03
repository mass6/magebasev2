<?php

$installer = $this;

$installer->startSetup();

$installer->run("

 -- DROP TABLE IF EXISTS {$this->getTable('aitproductslists/list')};
   CREATE TABLE IF NOT EXISTS {$this->getTable('aitproductslists/list')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  `pay_qty` int(5) NOT NULL,
  `notes` text NOT NULL,
  `product_change_notify_status` tinyint(1) NOT NULL,
  `public_key` VARCHAR( 255 ) NOT NULL,
  `discount_list_status` TINYINT( 2 ) NOT NULL,
  `pattern` VARCHAR( 100 ) NOT NULL DEFAULT '[WEBSITE]\n[STORE]\n[STOREVIEW]',
  PRIMARY KEY (`id`),
  KEY `quote_id` (`quote_id`),
  KEY `customer_id` (`customer_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
  
 -- DROP TABLE IF EXISTS {$this->getTable('aitproductslists/list_reminder')};
   CREATE TABLE IF NOT EXISTS {$this->getTable('aitproductslists/list_reminder')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `start_date` datetime NULL DEFAULT NULL,
  `period` int(5) NOT NULL,
  `frequency` int(5) NOT NULL,
  `max_notify_qty` int(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `list_id` (`list_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
  
-- ALTER TABLE `{$this->getTable('aitproductslists/list_reminder')}`
  -- ADD CONSTRAINT `aitproductslists_list_reminder_ibfk_1` FOREIGN KEY (`list_id`) REFERENCES `{$this->getTable('aitproductslists/list')}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
   
 -- DROP TABLE IF EXISTS {$this->getTable('aitproductslists/list_shedule')};
   CREATE TABLE IF NOT EXISTS {$this->getTable('aitproductslists/list_shedule')} (
  `id` INT( 111 ) NOT NULL AUTO_INCREMENT,
  `list_id` INT( 11 ) NOT NULL ,
  `start_date` DATETIME NOT NULL ,
  `finish_date` DATETIME NOT NULL ,
  `status` VARCHAR (50) NOT NULL,
   PRIMARY KEY `id` (`id`),
   KEY `list_id` (`list_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
   
  -- ALTER TABLE `{$this->getTable('aitproductslists/list_shedule')}`
  -- ADD CONSTRAINT `aitproductslists_list_shedule_ibfk_1` FOREIGN KEY (`list_id`) REFERENCES `{$this->getTable('aitproductslists/list')}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
   
 -- DROP TABLE IF EXISTS {$this->getTable('aitproductslists/list_discount')};
   CREATE TABLE IF NOT EXISTS {$this->getTable('aitproductslists/list_discount')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL,
  `price` decimal(5,2) UNSIGNED NOT NULL,
  `min_qty` VARCHAR (5) NOT NULL,
  `from_date` datetime NULL DEFAULT NULL,
  `to_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `list_id` (`list_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
  
-- ALTER TABLE `{$this->getTable('aitproductslists/list_discount')}`
  -- ADD CONSTRAINT `aitproductslists_list_discount_ibfk_1` FOREIGN KEY (`list_id`) REFERENCES `{$this->getTable('aitproductslists/list')}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

 -- DROP TABLE IF EXISTS {$this->getTable('aitproductslists/list_order')};
   CREATE TABLE IF NOT EXISTS {$this->getTable('aitproductslists/list_order')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `is_approved` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `list_id` (`list_id`),
  KEY `order_id` (`order_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
  
 -- ALTER TABLE `{$this->getTable('aitproductslists/list_order')}`
  -- ADD CONSTRAINT `aitproductslists_list_order_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `{$this->getTable('sales/order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  -- ADD CONSTRAINT `aitproductslists_list_order_ibfk_1` FOREIGN KEY (`list_id`) REFERENCES `{$this->getTable('aitproductslists/list')}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

  -- DROP TABLE IF EXISTS {$this->getTable('aitproductslists/list_purchase')};
  CREATE TABLE IF NOT EXISTS `{$this->getTable('aitproductslists/list_purchase')}` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `qty_in_list` int(10) NOT NULL,
  `qty_in_cart` int(10) NOT NULL,
  `product_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 
 -- DROP TABLE IF EXISTS {$this->getTable('aitproductslists/list_item')};
  CREATE TABLE IF NOT EXISTS `{$this->getTable('aitproductslists/list_item')}` (
  `note_id` int(50) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `notice` TEXT NOT NULL,
  PRIMARY KEY (`note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
  
 -- DROP TABLE IF EXISTS {$this->getTable('aitproductslists/notices')};  
    CREATE TABLE IF NOT EXISTS `{$this->getTable('aitproductslists/notices')}` (
    `notice_id` INT( 111 ) NOT NULL AUTO_INCREMENT ,
    `message` TEXT NOT NULL ,
    `customer_id` INT( 11 ) NOT NULL ,
    `list_id` INT( 22 ) NOT NULL ,
    `date_added` DATETIME NOT NULL,
    `status` VARCHAR( 20 ) NOT NULL ,
    PRIMARY KEY ( `notice_id` ) ,
    INDEX ( `notice_id` )
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    
    -- DROP TABLE IF EXISTS {$this->getTable('aitproductslists/list_order_multishipping')};
    CREATE TABLE `{$this->getTable('aitproductslists/list_order_multishipping')}` (
    `id` INT( 111 ) NOT NULL AUTO_INCREMENT ,
    `quote_id` INT( 11 ) NOT NULL ,
    `order_id` INT( 11 ) NOT NULL ,
    `total` DECIMAL( 12, 4 ) NOT NULL ,
    `status` VARCHAR( 20 ) NOT NULL ,
    PRIMARY KEY ( `id` ) ,
    INDEX ( `id` )
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");

$installer->endSetup(); 
