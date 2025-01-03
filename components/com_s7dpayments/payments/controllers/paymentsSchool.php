<?php
/**
 * @version     1.0.0
 * @package     com_s7dpayments
 * @copyright   Copyright (C) 2016. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
 */

// No direct access
defined('_JEXEC') or die;

class paymentsSchool extends s7dPayments
{

//Cart ------------------------------------------------------------
	public static function setScholl($cartid)
	{
        
        $idcart = "'".$cartid."'";

        //date
        $date = "'".date('Y/m/d H:i:s')."'";

        //Inserindo os dados do usúario;
        $db =& JFactory::getDBO();

        $query = "INSERT INTO `#__s7dpayments_cart` (`cartid`,`date`)
        VALUES ($idcart,$date);";
        $db->setQuery( $query );

        return $db->query();
            
	}
	

	public static function getSchool()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id','nome')));
		$query->where($db->quoteName('state').' = '.$db->quote(1));
		$query->from($db->quoteName('#__s7dpayments_school'));
		$query->order('nome ASC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		$out = array();
		$out[] = '<option value="">Selecione</option>';
		foreach($results as $item)
		{
			$out[] = '<option value="'.trim($item->nome).'">'.trim($item->nome).'</option>';

		}
		$out[] = '<option class="nescola" value="naescola">Outros</option>';

		return implode('',$out);
	}
}