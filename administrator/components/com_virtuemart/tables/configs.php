<?php
/**
*
* Configuration table
*
* @package	VirtueMart
* @subpackage Config
* @author RickG, Max Milbers
* @link https://virtuemart.net
* @copyright Copyright (c) 2004 - 2021 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: configs.php 10558 2021-12-02 23:11:15Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Coupon table class
 * The class is is used to manage the coupons in the shop.
 *
 * @package	VirtueMart
 * @subpackage Config
 * @author RickG
 */
class TableConfigs extends VmTable {

	/** @var int Primary key */
	var $virtuemart_config_id			= 0;
	/** @var config */
	var $config       		= 0;

	/**
	 * @author RickG
	 * @param JDataBase $db
	 */
	function __construct(&$db)
	{
		parent::__construct('#__virtuemart_configs', 'virtuemart_config_id', $db);

		$this->setLoggable();

	}

}
// pure php no closing tag
