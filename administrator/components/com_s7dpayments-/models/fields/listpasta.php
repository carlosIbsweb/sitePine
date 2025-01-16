<?php
/**
 * @version     1.0.0
 * @package     com_s7dgallery
 * @copyright   Copyright (C) 2015. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldListpasta extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Listpasta';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$path = JPATH_SITE."/images/"; 
		$diretorio = dir($path);
		echo "Lista de Arquivos do diretório '<strong>".$path."</strong>':<br />"; 
		while($arquivo = $diretorio -> read()){ 
		$feio .= $path; }
		$diretorio -> close();


		return $feio;
	}
}