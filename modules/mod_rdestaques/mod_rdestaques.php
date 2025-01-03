<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the latest functions only once
JLoader::register('ModRdestaquesHelper', __DIR__ . '/helper.php');

$list            = ModRdestaquesHelper::getList($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');
$folder = $params->get('folder');
$modelo = $params->get('modelo');
$eximagem = $params->get('eximagem');
$share = $params->get('share');
$pDate = $params->get('pDate');

$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base(true).'/modules/mod_rdestaques/assets/css/style.css');

require JModuleHelper::getLayoutPath('mod_rdestaques', $params->get('layout', 'default'));
