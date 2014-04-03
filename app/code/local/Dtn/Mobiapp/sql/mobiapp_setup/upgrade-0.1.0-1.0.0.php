<?php

/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();
$installer->run(" 
CREATE TABLE IF NOT EXISTS `mobiapp_pn_device` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_token` varchar(100) DEFAULT NULL,
  `device_os` VARCHAR( 15 ) NULL,
  `device_userid` int(11) DEFAULT NULL,
  `development` varchar(15) NOT NULL DEFAULT 'production',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Store unique devices' AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `mobiapp_pn_message` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` int(11) NOT NULL DEFAULT '0',
  `message` varchar(255) DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'queued',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");
$installer->endSetup();
