<?php

// ALTER TABLE  `mobiapp_pn_device` CHANGE  `device_token`  `device_token` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE  `mobiapp_pn_device` CHANGE  `device_token`  `device_token` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
$installer->endSetup();
