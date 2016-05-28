<?php

$this->addAttribute( Mage_Catalog_Model_Product::ENTITY, 'show_invisible', array(
            'group' => 'Extended Bundle Options',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Show products not visible in catalog?',
            'input' => 'boolean',
            'source' => 'eav/entity_attribute_source_boolean',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'default' => '1',
            'unique' => false,
            'apply_to' => 'extendedbundle',
        ) );
