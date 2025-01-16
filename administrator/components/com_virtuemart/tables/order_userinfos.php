<?php
/**
 *
 * Order table holding user info
 *
 * @package	VirtueMart
 * @subpackage Orders
 * @author 	Oscar van Eijk, Max Milbers
 * @link https://virtuemart.net
 * @copyright Copyright (c) 2004 - 2021 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: order_userinfos.php 10558 2021-12-02 23:11:15Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class TableOrder_userinfos extends VmTable {

    /**
     * @author Max Milbers
     * @param string $_db
     */
    function __construct(&$_db){
		parent::__construct('#__virtuemart_order_userinfos', 'virtuemart_order_userinfo_id', $_db);
		parent::showFullColumns();
		$this->setLoggable();
	}

}
// No closing tag