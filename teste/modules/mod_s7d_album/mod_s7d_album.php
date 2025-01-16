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
JLoader::register('ModSanisliderHelper', __DIR__ . '/helper.php');

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

$items = ModSanisliderHelper::getImages($params);
// Get the document object.
$doc = JFactory::getDocument();
$doc->addScript(JUri::base(true).'/modules/mod_s7d_album/assets/js/slideLateral.js?'.uniqid());
$doc->addScript(JUri::base(true).'/modules/mod_s7d_album/assets/js/jquery.magnifc-popup.js');
$doc->addStyleSheet(JUri::base(true).'/modules/mod_s7d_album/assets/css/style.css?'.uniqid());
$doc->addStyleSheet(JUri::base(true).'/modules/mod_s7d_album/assets/css/anima.css?'.uniqid());
$doc->addStyleSheet(JUri::base(true).'/modules/mod_s7d_album/assets/css/magnifc-popup.css');

require JModuleHelper::getLayoutPath('mod_s7d_album', $params->get('layout', 'default'));
