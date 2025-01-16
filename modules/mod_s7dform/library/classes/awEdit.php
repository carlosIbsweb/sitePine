
<?php

/**
 * @package     
 * @subpackage  mod AwForm
 **/

// No direct access.
defined('_JEXEC') or die;

/********
 Classe Aw Valid.
 Desenvolvido por Carlos (IBS WEB)
********/

class awEdit {

	public static function getItem($name,&$params)
    {
        // Build the query for the table list.
        $db = JFactory::getDbo();
        $db->setQuery(
            'SELECT '.$name
            . ' FROM '.$params->get('db')
        );
        
        $result = $db->loadResult();
    }

}