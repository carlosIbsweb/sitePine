<?php
/**
 * @version     1.0.0
 * @package     com_s7dpayments
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
class JFormFieldDvideos extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Dvideos';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
?>
		<textarea id='<?=$this->class?>' class="dtext"><?php if(is_array(json_decode(self::getItems($this->class),true))): foreach(json_decode(self::getItems($this->class)) as $i): ?><?= $i."\n";?><?php endforeach; endif; ?></textarea>
		<script>
			jQuery (function($){
				jQuery.noConflict();
					$(document).ready(function() {
					<?=$this->class?>();
					$('#<?=$this->class?>').keyup(<?=$this->class?>);
					function <?=$this->class?>(){
			    		var lines = $('#<?=$this->class?>').val().split(/\n/);
			     		var texts = [];
			     		var mega = 'vid';
						for (var i=0; i < lines.length; i++) {
			  				// only push this line if it contains a non whitespace character.
			  				if (/\S/.test(lines[i])) {
			    				texts.push('"'+mega+i+'"'+':'+'"'+lines[i]+'"');
			  				}
						}
					
						var jar = '{'+texts;
						var jir = jar.slice(0,-1)+'"}';
			      		$(".<?=$this->class?>").val(jir);
			 		};

				});

			});
		</script>
	<?php
		$input = '<input type="hidden" value="" name="'.$this->name.'" class="'.$this->class.'">';
		
		return $input;
	}

	protected function getItems($name)
	{
		$cdb = JFactory::getDbo();

        $cdb->setQuery("SELECT #__s7dpayments_courses.$name FROM #__s7dpayments_courses where id = ".$_GET['id']);

        return $cdb->loadResult();
	}
}