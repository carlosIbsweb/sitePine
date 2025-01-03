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

function existe($nome){
	// Get a db connection.
	$db = JFactory::getDbo();

	// Create a new query object.
	$query = $db->getQuery(true);

	$nome = trim(strtolower($nome));

	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	$query->select($db->quoteName(array('nome')));
	$query->from($db->quoteName('#__s7dpayments_school'));
	$query->where($db->quoteName('nome') . ' = ' . $db->quote($nome));

	// Reset the query using our newly populated query object.
	$db->setQuery($query);

	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$results = $db->loadObjectList();

	return count($results);
}

function criaEscola($data){
	// Initialiase variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Create the base insert statement.
		$query->insert($db->quoteName('#__s7dpayments_school'))
			->columns(array($db->quoteName('nome'), $db->quoteName('state'), $db->quoteName('criado')))
			->values($db->quote($data['nome']) . ', ' . $db->quote(1). ', ' . $db->quote(date('Y-m-d H:i:s')));
		
		// Set the query and execute the insert.
		$db->setQuery($query);
		
		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return true;
}

$out = array();

if(existe($_POST['nome'])){
	$out['error'] = 'Já existe uma Escola com esse nome';
	echo json_encode($out);
	exit();
}

criaEscola($_POST);

$out['success'] = true;
echo json_encode($out);
exit();
