<?php
/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('restricted aceess');
JLoader::registerPrefix('S7dgallery', JPATH_SITE . '/components/com_s7dgallery/');

abstract class S7dgalleryHelperRoute extends JComponentRouterBase
{
	public static function mRoute($query){

		$app = JFactory::getApplication('site');
		$menu = $app->getMenu()->getItem($query['Itemid']);
		$menuParent = $app->getMenu()->getItem($menu->parent_id);

		//print_r($menus);

		//return empty($menuParent->id) ? '' : $menuParent->alias.'/'.$menu->alias;

		echo 'aqui'.$query;
		
	}
}