<?php

defined('_JEXEC') or die('Direct Access to ' . basename(__FILE__) . 'is not allowed.');

/**
 *
 * @package    VirtueMart
 * @subpackage vmpayment
 * @version $Id: closeorderreferenceresponse.php 9413 2017-01-04 17:20:58Z Milbo $
 * @author Valérie Isaksen
 * @link https://virtuemart.net
 * @copyright Copyright (c) 2004 - April 26 2022 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 */
class amazonHelperCloseOrderReferenceResponse extends amazonHelper {

	public function __construct (OffAmazonPaymentsService_Model_CloseOrderReferenceResponse $closeOrderReferenceResponse, $method) {
		parent::__construct($closeOrderReferenceResponse, $method);
	}



	public function getStoreInternalData () {
		return NULL;
	}



	function getContents () {

		$contents = $this->tableStart("CloseOrderReferenceResponse");
		$contents .= $this->getRow("Dump: ", var_export($this->amazonData, true));

		$contents .= $this->tableEnd();

		return $contents;
	}


}