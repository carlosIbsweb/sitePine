<?php
/**
 * @version     1.0.0
 * @package     com_s7dpayments
 * @copyright   Copyright (C) 2016. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
 */
define( '_JEXEC', 1 );
define( 'DS', '/' );

define( 'JPATH_BASE', $_SERVER['DOCUMENT_ROOT']);
require_once ( JPATH_BASE .DS. 'includes' .DS. 'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once ( JPATH_BASE .DS. 'components' .DS. 'com_s7dpayments'.DS.'controller.php' );
$mainframe = JFactory::getApplication('site');
$mainframe->initialise();
jimport( 'joomla.user.user');
jimport( 'joomla.session.session');
jimport( 'joomla.user.authentication');

//Dados do usuário logado
$user = JFactory::getUser(); 
$jid = $user->id; 
$jname = $user->name; 
$jguest = $user->guest;



function getCri($userid){
	// Initialiase variables.
	$db    = JFactory::getDbo();
	$query = $db->getQuery(true);
	
	// Create the base select statement.
	$query->select('*')
		->from($db->quoteName('#__s7dpayments_cri'))
		->where($db->quoteName('userid') . ' = ' . $db->quote($userid));
	
	// Set the query and load the result.
	$db->setQuery($query);
	
	try
	{
		$result = $db->loadObjectList();
	}
	catch (RuntimeException $e)
	{
		JError::raiseWarning(500, $e->getMessage());
	}

	return $result;
}

$output = [];

$searchNomes = strtolower(trim($_POST['nomes']));

foreach(getCri($jid) as $item)
{
	foreach(json_decode(trim($item-> criancas)) as $id => $di)
	{
		if(strtolower(trim($di-> nome)) == strtolower('Fernanda'))
		{
			$output['dados'] = [$di];
		}

		if(preg_match("/$searchNomes/", strtolower($di->nome))) {
   		 	$output['nomes'] = [$di->nome];
		}

	}
}

json_encode($output,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
