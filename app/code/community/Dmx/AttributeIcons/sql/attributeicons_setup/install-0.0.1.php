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
$table = $connection->newTable($this->getTable('attributeicons/data'));
$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
									'identity'  => true,
							        'nullable'  => false,
							        'primary'   => true,
							),'ID')
	->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
									'identity'  => false,
							        'unsigned'  => true,
							        'nullable'  => false,
							        'primary'   => false,
							),'Catalog Attribute ID')
	->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
									'identity'  => false,
							        'unsigned'  => true,
							        'nullable'  => true,
							        'primary'   => false,
							),'Catalog Attribute Option ID')
	->addColumn('icon', Varien_Db_Ddl_Table::TYPE_VARCHAR,null,array(
									'length'	=> 255,
									'identity'  => false,
							        'unsigned'  => true,
							        'nullable'  => true,
							        'primary'   => false,
							),'Catalog Attribute Option ID')
	->addColumn('image', Varien_Db_Ddl_Table::TYPE_VARCHAR,null,array(
									'length'	=> 255,
									'identity'  => false,
							        'unsigned'  => true,
							        'nullable'  => true,
							        'primary'   => false,
							),'Catalog Attribute Option ID')
	->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT,null,array(
									'identity'  => false,
							        'unsigned'  => true,
							        'nullable'  => true,
							        'primary'   => false,
							),'Catalog Attribute Option ID');
$table->addIndex($this->getIdxName('attributeicons/data',array('attribute_id','option_id')),
				 array('attribute_id','option_id'),
				 array('type'=>Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));

$connection->createTable($table);

$this->endSetup();