<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('bcp_item_attribute_value')};
CREATE TABLE {$this->getTable('bcp_item_attribute_value')} (
    value_id         int(10)	unsigned	NOT NULL	auto_increment	PRIMARY KEY
	,item_id         int(10)	unsigned	NOT NULL
	,attribute_code  varchar(255)
	,store_id        int(11)    unsigned	NOT NULL	default 0
	,value           text                   NOT NULL
	,CONSTRAINT FK_PBP_ITEM_ATTRIBUTE_VALUE_ITEM_ID FOREIGN KEY (item_id)
		REFERENCES {$this->getTable('bcp_item')} (item_id)
		ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();