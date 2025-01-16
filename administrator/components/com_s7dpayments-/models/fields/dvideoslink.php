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
class JFormFieldDvideoslink extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Dvideoslink';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
?>
		<textarea id='dtextlink' placeholder="Ex: name vídeo1&#10;name vídeo2&#10;name vídeo3"><?php if(is_array(json_decode(self::getItems(),true))): foreach(json_decode(self::getItems()) as $i): ?><?= $i."\n";?><?php endforeach; endif; ?></textarea>
		<script>
			jQuery (function($){
				jQuery.noConflict();
					$(document).ready(function() {
					xxx();
					$('#dtextlink').keyup(xxx);
					function xxx(){
			    		var lines = $('#dtextlink').val().split(/\n/);
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
			      		$(".exvallink").val(jir);
			 		};

				});

			});
		</script>
<?php
		$input = '<input type="hidden" value="" name="'.$this->name.'" class="'.$this->name.'">';
		
		return $input;
	}

	protected function getItems()
	{
		$cdb = JFactory::getDbo();

        $cdb->setQuery('SELECT #__s7dpayments_courses.videos FROM #__s7dpayments_courses where id = '.$_GET['id']);

        return $cdb->loadResult();
	}
}