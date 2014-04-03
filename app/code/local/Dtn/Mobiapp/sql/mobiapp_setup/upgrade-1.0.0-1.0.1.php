<?php
/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE  `mobiapp_pn_message` ADD  `badge` INT NOT NULL DEFAULT  '0' AFTER  `message`");
$installer->endSetup();