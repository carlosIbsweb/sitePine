<?php

/**
 * @subpackage  mod_wkcontact
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */


// Acesso ao Joomla
defined('_JEXEC') or die;

$moduleId = 'awForm-'.$module->id;

/*
*Gerando o PDF.
*/
//Bloqueio de acesso a usuário
if($params['editUser'] && empty($user->id))
{
	$return = base64_encode(JUri::getInstance()->toString());
	$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return, false));
}

if(isset($_GET['pdf']))
{
	awPdf::gPdf($params);
}

if(isset($_GET['awEdit']))
{
	include('_edit.php');
}else
{
	include('_form.php');
}


