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
JLoader::register('ModCheckinHelper', __DIR__ . '/helper.php');



$code = JFactory::getApplication()->input->getString('code', '');

$checkinHelper = new ModCheckinHelper();
$result = $checkinHelper->processarCheckCrianca($code);

// Exibir a resposta no módulo
echo json_encode($result);

require JModuleHelper::getLayoutPath('mod_checkin', $params->get('layout', 'default'));
