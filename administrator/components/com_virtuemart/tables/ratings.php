<?php
/**
*
* Ratings table
*
* @package	VirtueMart
* @subpackage
* @author Max Milbers
* @link https://virtuemart.net
* @copyright Copyright (c) 2004 - 2021 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: ratings.php 10558 2021-12-02 23:11:15Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Product review table class
 * The class is is used to manage the reviews in the shop.
 *
 * @package		VirtueMart
 * @author Max Milbers
 */
class TableRatings extends VmTable {

	/** @var int Product ID */

	var $virtuemart_rating_id	= 0;
	var $virtuemart_product_id           = 0;

	var $rates         					= 0;
	var $ratingcount      				= 0;
	var $rating      					= 0;

	/** @var int State of the review */
	var $published         		= 0;


	/**
	* @author Max Milbers
	* @param JDataBase $db
	*/
	function __construct(&$db) {
		parent::__construct('#__virtuemart_ratings', 'virtuemart_rating_id', $db);
		//In a VmTable the primary key is the same as the _tbl_key and therefore not needed

//		$this->setObligatoryKeys('virtuemart_product_id');

		$this->setLoggable();

		$this->setTableShortCut('r');
	}
}
// pure php no closing tag
