<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('supplier')};
CREATE TABLE {$this->getTable('supplier')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` VARCHAR( 255 ) NOT NULL,
  `sup_name` VARCHAR( 255 ) NOT NULL,
  `sup_email` VARCHAR( 255 ) NOT NULL,
  `sup_tel` VARCHAR( 255 ) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 