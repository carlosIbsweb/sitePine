<?php
/**
* @package com_speasyimagegallery
* @subpackage mod_uteasyimagegallery_carousel
* @author Unitemplates https://www.unitemplates.com
* @copyright Copyright (c) 2020 - 2023 Unitemplates
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
* @filesource mod_speasyimagegallery of Joomshaper
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

// Include the search functions only once
JLoader::register('ModUteasyimagegalleryCarouselHelper', __DIR__ . '/helper.php');

//$params
$lang = Factory::getLanguage();
$app  = Factory::getApplication();

$layout = $params->get('layout', 'album');

if($layout == 'albums') {
  $albums = ModUteasyimagegalleryCarouselHelper::getAlbumList($params);
} else {
  $images = ModUteasyimagegalleryCarouselHelper::getImages($params);
}

require ModuleHelper::getLayoutPath('mod_uteasyimagegallery_carousel', $layout);
