<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_feed
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the feed functions only once
JLoader::register('ModColoniaHelper', __DIR__ . '/helper.php');

//ModColoniaHelper::update();

$vamos = array_column(ModColoniaHelper::getCats(305,1),'title');

$arrs = implode(',',$vamos);

//ModColoniaHelper::update($arrs);

//print_r($arrs);

$json = file_get_contents(__DIR__.'/colonia.json');

ModColoniaHelper::insertCats($json);

require JModuleHelper::getLayoutPath('mod_colonia', $params->get('layout', 'default'));
