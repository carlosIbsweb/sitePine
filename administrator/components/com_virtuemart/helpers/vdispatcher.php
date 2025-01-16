<?php

/**
 * virtuemart dispatcher class
 *
 *
 * @version $Id$
 * @package    VirtueMart
 * @subpackage Helpers
 * @author Max Milbers
 * @copyright Copyright (c) 2019 - 2021 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */

use Joomla\Event\Dispatcher as EventDispatcher;

class vDispatcher {

	static $dispatcher = null;

	static function trigger ($name, $params){

		if(self::$dispatcher === null){
			if(JVM_VERSION<4){
				self::$dispatcher = JEventDispatcher::getInstance();
			} else {
				self::$dispatcher = JFactory::getApplication();
			}
		}

		if(JVM_VERSION<4){
			return self::$dispatcher->trigger($name, $params);
		} else {
			return self::$dispatcher->triggerEvent($name, $params);
		}
	}

	static function setDispatcher() {

	}

	/**
	 * Executes a function of a plugin directly, which is loaded via element
	 *
	 * @author Max Milbers
	 * @param $type type of the plugin, for example vmpayment
	 * @param $element the element of the plugin as written in the extensions table (usually lowercase)
	 * @param $trigger the function which was the trigger to execute
	 * @param $args the arguments (as before for the triggers)
	 * @param $enabled trigger only enabled plugins
	 * @return mixed
	 */
	static public function directTrigger($type,$element,$trigger, $args, $enabled = TRUE){
		//vmdebug('Calling directTrigger',$type,$element,$trigger, $args);

		JPluginHelper::importPlugin($type);
		if(empty($element)){
			$plugins = JPluginHelper::getPlugin($type);
			foreach($plugins as $plugin){
				$plg = self::createPlugin($type, $plugin->name, $enabled);
				if($plg and method_exists($plg, $trigger)){
					call_user_func_array(array($plg,$trigger),$args);
				}
			}
		} else {
			$plg = self::createPlugin($type,$element, $enabled);
			if($plg and method_exists($plg, $trigger)){
				return call_user_func_array(array($plg,$trigger),$args);
			} else {
				return false;
			}
		}
	}

	static function importVMPlugins($ptype){

		static $types = array('vmextended'=>TRUE,'vmuserfield'=>TRUE, 'vmcalculation'=>TRUE, 'vmcustom'=>TRUE, 'vmcoupon'=>TRUE, 'vmshipment'=>TRUE, 'vmpayment'=>TRUE);
		if(!isset($types[$ptype])) return;

		foreach($types as $type => $v){
			vmStartTimer('importPlugins');
			JPluginHelper::importPlugin($type);
			unset($types[$type]);
			vmTime('time to import plugins '.$type,'importPlugins');
			if($type == $ptype){
				break;
			}
		}
	}

	/** Creates a plugin object. Used by the directTrigger and therefore loads also unpublished plugins.
	 * Otherwise, we would not be able to use the plug-in functions during the method saving process.
	 * @param $type
	 * @param $element
	 * @return false|mixed
	 */
	static public function createPlugin($type, $element, $enabled = TRUE){

		if(empty($type) or empty($element)){
			vmdebug('Developer error, class vmpluglin function createPlugin: empty type or element');
		}

		$plugin = self::getPlugin($type, $element, $enabled);
		if(!isset($plugin->type) or !isset($plugin->name)){
			if(!empty($type) and !empty($element)) {
				vmdebug('VmPlugin function createPlugin, plugin unpublished', $type, $element);
				vmTrace('VmPlugin function createPlugin, plugin unpublished '. $type .' '. $element.' $enabled '.(int)$enabled);
				//vmError('VmPlugin function createPlugin, plugin unpublished '. $type .' '. $element);

			} else {
				vmdebug('VmPlugin function createPlugin, type or name empty',$type,$element);
				vmTrace('VmPlugin function createPlugin, type or name empty '. $type .' '. $element);
				vmError('VmPlugin function createPlugin, type or name empty '. $type .' '. $element);
			}
		}

		$className = 'Plg' . str_replace('-', '', $type) . $element;

		if(!class_exists($className) and JFile::exists(VMPATH_PLUGINS.'/'.$type.'/'.$element.'/'.$element.'.php')){
			require(VMPATH_PLUGINS.'/'.$type.'/'.$element.'/'.$element.'.php');
		}
		if(class_exists($className)){
			// Instantiate and register the plugin.
			if(JVM_VERSION<4){
				if(self::$dispatcher === null){
					self::$dispatcher = JEventDispatcher::getInstance();
				}
				return new $className(self::$dispatcher, (array) $plugin);
			} else {
				$dummy = new EventDispatcher();
				return new $className($dummy, (array) $plugin);
			}

		} else {
			vmdebug('VmPlugin function createPlugin, class does not exist '.$className, $type, $element);
			vmTrace('VmPlugin function createPlugin, class does not exist '. $type .' '. $element);
			vmError('VmPlugin function createPlugin, class does not exist '. $type .' '. $element,'VmPlugin function createPlugin, class does not exist');
			return false;
		}

	}

	static function getPlugin($type, $element, $enabled = TRUE){

		$q = 'SELECT `extension_id` as `id`, `folder` as `type`, `element` as `name`, `params` FROM #__extensions WHERE 
						`type`="plugin" and `folder`="'.$type.'" and `element` = "'.$element.'" and `state` = "0"';
		if($enabled){
			$q .= ' AND enabled = "1"';
		}
		$db = JFactory::getDbo();
		$db->setQuery($q);

		try{
			$plugin = $db->loadObject();
		} catch (Exception $e) {
			$t = 'Could not load Plugin '.$type.' '.$element;
			vmError($t.' '.$e->getMessage(),$t);
		}
		return $plugin;
	}

}