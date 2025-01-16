<?php
/**
 * @version     1.0.0
 * @package     com_s7dpayments
 * @copyright   Copyright (C) 2015. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldAwdb extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Awdb';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{

		// Get the document object.
		$doc = JFactory::getDocument();
		
		$doc->addScript(JUri::root(true).'/modules/mod_s7dform/elements/assets/js/addb.js');
		$doc->addScript(JUri::root(true).'/modules/mod_s7dform/elements/assets/js/json.js');
		

		$script = 
		"
			jQuery(function($){
				$( document ).ready(function(){
					$( document ).addb();
				})
			})

			
			
		";

		$doc->addScriptDeclaration($script);


   echo '<div class=""><h3 type="h3" class="ibsmDependentes" name="" id="">Dependentes</h3>

               					
               					
               				    
               					<div class="ndnn">Nenhum dependente</div><span class="ibsmDm">Adicionar</span>
               				</div>';



$json = '{"9aam5bnsm":{"type":"CHAR","name":"nome","tamanho":"255"},"iv8jvt09h":{"type":"CHAR","name":"email","tamanho":"255"},"vs11is5xt":{"type":"VARCHAR","name":"name","tamanho":"255"},"ijz0mhssu":{"type":"VARCHAR","name":"cpf","tamanho":"140"},"9dfubfo0y":{"type":"TEXT","name":"255","tamanho":"140"}}';

foreach(json_decode($json) as $id => $v)
{
	echo $v->type.$v->name.$v->tamanho;
}


self::setDb();
  	




	}

	public static function setDb(){
		echo 'aqui'.$_POST['jform[title]'];
	}



}