<?php

use WebChemistry\Definition\Startup;

require __DIR__ . '/vendor/autoload.php';

$startup = new Startup();
$startup->run(__DIR__ . '/theme.definition.php');
