<?php
/*------------------------------------------------------------------------
# plg_system_vm2finalize - Finalize order for Virtuemart 2 Plugin
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class plgSystemJsAsyncDeferInstallerScript {

	/**
	* Called on installation
	*
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function install($adapter) {
	}

	/**
	* Called on uninstallation
	*
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*/
	function uninstall($adapter) {
		//echo '<p>'. JText::_('1.6 Custom uninstall script') .'</p>';
	}

	/**
	* Called on update
	*
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function update($adapter) {
		//echo '<p>'. JText::_('1.6 Custom update script') .'</p>';
	}

	/**
	* Called before any type of action
	*
	* @param   string  $route  Which action is happening (install|uninstall|discover_install)
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function preflight($route, $adapter) {
		//echo '<p>'. JText::sprintf('1.6 Preflight for %s', $route) .'</p>';
	}

	/**
	* Called after any type of action
	*
	* @param   string  $route  Which action is happening (install|uninstall|discover_install)
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function postflight($route, $adapter) {
		
		if ($route=='update') {
			$oldfolders = array();
			foreach ($oldfolders as $oldfolder) {
				if (JFolder::exists($oldfolder))
					JFolder::delete($oldfolder);
			}
	
			$oldfiles = array();
			
			foreach ($oldfiles as $oldfile) {
				if (JFile::exists($oldfile))
					JFile::delete($oldfile);
			}
		}
		
		if ($route=='install' || $route=='update') {
			$lang = JFactory::getLanguage();
			$lang->load('plg_system_jsasyncdefer',JPATH_SITE.'/plugins/system/jsasyncdefer');
			$url = 'index.php?option=com_plugins&filter_search=async';
	
			$plugin_name = JText::_('PLG_SYSTEM_JSASYNCDEFER');
			?>
			<div class="well clearfix">
				<h2><img src="../plugins/system/jsasyncdefer/assets/images/appstore48.png" width="48" height="48" alt="<?php echo $plugin_name; ?>"/>&nbsp; <?php echo $plugin_name; ?></h2>
				<p class="lead">Plugin installed</p>
				<div class="row-fluid">
					<a class="btn btn-large btn-primary pull-left span5" href="<?php echo $url; ?>"><?php echo JText::_('PLG_DAYCOUNTS_CONFIGURE'); ?></a>
					<a href="https://www.daycounts.com/" target="new" class="pull-right span5"><img src="../plugins/system/jsasyncdefer/assets/images/daycounts.png" style="" alt="DayCounts.com"/></a>
				</div>
			</div>
			<br />
			<?php
		}
	}
}