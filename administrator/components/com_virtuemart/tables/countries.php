<?php
/**
*
* Country table
*
* @package	VirtueMart
* @subpackage Country
* @author RickG
* @link https://virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: countries.php 10593 2022-03-03 13:08:25Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Country table class
 * The class is is used to manage the countries in the shop.
 *
 * @package		VirtueMart
 * @author RickG
 */
class TableCountries extends VmTable {

	/** @var int Primary key */
	var $virtuemart_country_id				= 0;
	/** @var integer Zone id */
	var $virtuemart_worldzone_id           		= 0;
	/** @var string Country name */
	var $country_name           = '';
	/** @var char 3 character country code */
	var $country_3_code         = '';
    /** @var char 2 character country code */
	var $country_2_code         = '';
	var $country_num_code         = '';
	var $ordering				= '';
    /** @var int published or unpublished */
	var $published 		        = 1;


	/**
	 * @author Max Milbers
	 * @param JDataBase $db
	 */
	function __construct(&$db)
	{
		parent::__construct('#__virtuemart_countries', 'virtuemart_country_id', $db);

		$this->setUniqueName('country_name');
		$this->setObligatoryKeys('country_2_code');
		$this->setObligatoryKeys('country_3_code');

		$this->setLoggable();
		$this->setOrderable('ordering',false);
	}

}
// pure php no closing tag
