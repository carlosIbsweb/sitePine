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


