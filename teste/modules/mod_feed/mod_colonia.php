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



$items = ModColoniaHelper::getFeed($params);

require JModuleHelper::getLayoutPath('mod_colonia', $params->get('layout', 'default'));
