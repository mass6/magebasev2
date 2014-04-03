<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('rule')};
CREATE TABLE {$this->getTable('rule')} (
  `rule_id` int(11) unsigned NOT NULL auto_increment,
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  `orderamount` varchar(255) NOT NULL default '',
  `l1` int(10) NOT NULL,
  `l2` int(10) NOT NULL,
  `l3` int(10) NOT NULL,
  `l4` int(10) NOT NULL,
  `l5` int(10) NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `is_active` int(11) NOT NULL ,
  `website_ids` int(250) NOT NULL ,
  `customer_group_ids` int(250) NOT NULL ,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 