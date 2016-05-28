<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('bcp_item_option')} 
  ADD preview_image varchar(255);
");

$installer->endSetup();
