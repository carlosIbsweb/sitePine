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

// Função para substituir novas linhas por \n
function prepareForJson($data) {
    if (is_array($data)) {
        foreach ($data as &$value) {
            if (is_array($value) || is_object($value)) {
                $value = prepareForJson($value);
            } else {
                $value = str_replace(array("\r\n", "\r", "\n"), '\\n', $value);
            }
        }
    } elseif (is_object($data)) {
        foreach ($data as $key => $value) {
            $data->$key = prepareForJson($value);
        }
    } else {
        $data = str_replace(array("\r\n", "\r", "\n"), '\\n', $data);
    }
    return $data;
}

$output = [];
$names  = [];
$dados = [];

$searchNomes 	= strtolower(trim($_POST['nomes']));
$searchDadosId 	= strtolower(trim($_POST['dadosId']));

foreach(getCri($jid) as $item)
{
	foreach(json_decode($item-> criancas) as $id => $di)
	{
		if(isset($_POST['dadosId']) && !empty($_POST['dadosId']))
		{
			if($id == $searchDadosId)
			{
				array_push($dados,$di);
			}
		}

		if(!empty($searchNomes))
		{


   		
   		if(strpos(strtolower($di->nome),$searchNomes) !== false)
   		{
   			array_push($names,array(
   					"id" => $id,
   					"nome" => $di->nome
   				));
   		}
   	}
   	
   				
	}
}

$output['nomes'] = $names;
$output['dados'] = $dados;

$output = prepareForJson($output);

echo json_encode($output,JSON_UNESCAPED_UNICODE);
exit();
