<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('bcp_item')} 
  ADD preview_base_image text;
");

$installer->endSetup();
