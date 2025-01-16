<?php

/**
 * @subpackage	mod_s7d_scroll
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */



// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';


$items = modS7dScroll::getScroll($params);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));


require JModuleHelper::getLayoutPath('mod_s7d_scroll', $params->get('layout', 'default'));

