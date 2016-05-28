<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('bcp_item')};
CREATE TABLE {$this->getTable('bcp_item')} (
	item_id                 int(10)	unsigned	NOT NULL	auto_increment	PRIMARY KEY
	,product_id		int(10) unsigned	NOT NULL
	,code			varchar(255)		NOT NULL 	default ''
	,title			varchar(255)		NOT NULL 	default ''
	,sort_order		int(11) unsigned	NOT NULL	default 0
	,price			decimal(12,4)		NOT NULL	default 0.0000
	,qty			decimal(12,4)		NOT NULL 	default 1
	,CONSTRAINT FK_PBP_PRODUCT_ENTITY FOREIGN KEY (product_id)
		REFERENCES {$this->getTable('catalog_product_entity')} (entity_id)
		ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('bcp_item_option')};
CREATE TABLE {$this->getTable('bcp_item_option')} (
	option_id		int(10)	unsigned	NOT NULL	auto_increment	PRIMARY KEY
	,item_id               int(10)	unsigned	NOT NULL
	,product_id		int(10) unsigned	NOT NULL
	,CONSTRAINT FK_PBP_ITEM_OPTION_ITEM_ID FOREIGN KEY (item_id)
		REFERENCES {$this->getTable('bcp_item')} (item_id)
		ON DELETE CASCADE ON UPDATE CASCADE
	,CONSTRAINT FK_PBP_ITEM_OPTION_PRODUCT_ENTITY FOREIGN KEY (product_id)
		REFERENCES {$this->getTable('catalog_product_entity')} (entity_id)
		ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$fieldList = array(
    'tax_class_id'
);

// make these attributes applicable to package products
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, $field, 'apply_to'));
    if (!in_array('extendedbundle', $applyTo)) {
        $applyTo[] = 'extendedbundle';
        $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, $field, 'apply_to', join(',', $applyTo));
    }
}

$installer->endSetup();