<?php
/**
 * Dima Makaruk
 * @category   Dmx
 * @package    Dmx_AttributeIcons
 * @copyright  Copyright (c) 2012 Dima Makaruk. 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/* @var $this Mage_Eav_Model_Entity_Setup */
$this->startSetup();

$connection = $this->getConnection();
$tableName = $this->getTable('attributeicons/data');
$connection->query("ALTER TABLE {$tableName} ADD COLUMN show_both SMALLINT(6) NOT NULL DEFAULT 0");
$this->endSetup();