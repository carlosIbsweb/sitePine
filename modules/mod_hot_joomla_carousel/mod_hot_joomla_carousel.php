<?php
/*------------------------------------------------------------------------
# "Hot Joomla Carousel" Joomla module
# Copyright (C) 2018 HotThemes. All Rights Reserved.
# License: http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 only
# Author: HotThemes
# Website: https://www.hotjoomlatemplates.com
-------------------------------------------------------------------------*/
 
// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Uri\Uri;

// Path assignments
$mosConfig_absolute_path = JPATH_SITE;
$mosConfig_live_site = URI :: base();
if(substr($mosConfig_live_site, -1)=="/") { $mosConfig_live_site = substr($mosConfig_live_site, 0, -1); }
 
// get parameters from the module's configuration
$carouselImagesPath = $params->get('carouselImagesPath','images/headers');
$pauseTime = $params->get('pauseTime','5000');
$navArrows = $params->get('navArrows','1');
$navDots = $params->get('navDots','1');
$uniqueid = $params->get('uniqueid','');
$fullWidth = $params->get('fullWidth','1');
$slideWidth = $params->get('slideWidth','25%');
$responsiveWidth = $params->get('responsiveWidth','767px');
$slidePaddingVertical = $params->get('slidePaddingVertical','5px');
$slidePaddingHorizontal = $params->get('slidePaddingHorizontal','5px');
$slideMargin = $params->get('slideMargin','10px');
$backgroundColor = $params->get('backgroundColor','#eeeeee');
$borderWidth = $params->get('borderWidth','1px');
$borderStyle = $params->get('borderStyle','solid');
$borderColor = $params->get('borderColor','#cccccc');
$navArrowsPosition = $params->get('navArrowsPosition','30px');
$navDotsPosition = $params->get('navDotsPosition','40px');

require ModuleHelper::getLayoutPath('mod_hot_joomla_carousel', $params->get('layout', 'default'));