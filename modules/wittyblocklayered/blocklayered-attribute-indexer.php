<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/wittyblocklayered.php');

if (substr(Tools::encrypt('wittyblocklayered/index'),0,10) != Tools::getValue('token') || !Module::isInstalled('wittyblocklayered'))
	die('Bad token');

$blockLayered = new BlockLayered();
echo $blockLayered->indexAttribute();