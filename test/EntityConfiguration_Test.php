<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/

declare(strict_types = 1);

require_once('../src/EntityData/EntityConfiguration.php');
require_once('../src/EntityData/EntityData.php');
require_once('../src/EntityData/Attribute.php');

/**
 * Test following things
 * 
 * get - entity, invalid entity, invalid entity_definition.yaml
 * check singleton pattern implementation
 */

$entityConfigPath = '../config/entity_definition.yaml';
$entityNamespace = 'App\Entities';

$entityConfiguration = new Infinite\SimpleOrm\Config\EntityConfiguration($entityConfigPath,$entityNamespace);

echo "============== GETTING AN ENTITY CONFIGURATION ================ \n";

$entityData = $entityConfiguration->get('Subject'); var_dump($entityData);
echo "\n \n";


echo "============ GETTING AN ENTITY (INVALID NAME) ======================== \n";

try {
    $invalidEntity = $entityConfiguration->get('Students');
}
catch (\Exception $e) {
    echo "==== ERROR WHILE GETTING ENTITY DATA ====== \n";
    echo $e->getMessage() . "\n \n \n \n";
}

echo "============ GETTING AN ENTITY (INVALID CONFIGURATION) ======================== \n";

try {
    $invalidEntity = $entityConfiguration->get('Students');
}
catch (\Exception $e) {
    echo "==== ERROR WHILE GETTING ENTITY DATA ====== \n";
    echo $e->getMessage() . "\n \n \n \n";
}