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
class JFormFieldDarquivos extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Darquivos';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{	

		echo '<div id="darquivos">';
		echo 
		'	
			<div id="darquivosaddn" class="ydel darquivosaddn" onclick="javascript:delItemMInput(\'daddin\');javascript:delItemMS(\'darquivosadd\');javascript:delItemD(\'darquivosaddn\')"></div>
			<div id="darquivosadd" class="darquivosadd ydelitem darquivosaddn">
			<p>Adicionar Arquivo</p>
			<span class="addx" onclick="javascript:delItemMInput(\'daddin\');javascript:delItemMS(\'darquivosadd\');javascript:delItemD(\'darquivosaddn\')">X</span>
				<input type="text" name="dform[arquivo][title]" class="daddin" placeholder="Título">
				<textarea name="dform[arquivo][description]" class="" placeholder="Descrição"></textarea>
            	<input type="file" name="file[]" class="daddin">
            	<input type="hidden" name="dform[arquivo][linkpdf]" value="arquivo">
            	<buttom onclick="Joomla.submitbutton(\'course.apply\')" class="dbtn")>Criar</buttom>
            </div>
            <label class="dbtnmais" onclick="javascript:delItemMInput(\'daddin\');javascript:delItemM(\'darquivosadd\');javascript:delItem(\'darquivosaddn\')">Novo</label>
		';
		$i = 1;

		echo empty(json_decode(self::getItems('s7dpayments_courses','arquivos',JRequest::getInt('id')))) ? 'Nenhum arquivo' : null;		
		if(is_array(json_decode(self::getItems('s7dpayments_courses','arquivos',JRequest::getInt('id')),true))):
			foreach(json_decode(self::getItems('s7dpayments_courses','arquivos',JRequest::getInt('id'))) as $atitle => $its):
				$title = strlen($its->title) > 30 ? substr($its->title,0,strrpos(substr($its->title,0,30),' ')).'...' : $its->title;
				echo 
				'
					<div class="darquivositems">
						<h4>'.$its->title.'</h4>
						<label for="cx'.$i.'" onclick="javascript:delItemM(\'mm'.$i.'\');javascript:delItem(\'cx'.$i.'\')">Deletar</label>
					</div>

				'; 
	
				echo '<div class="ydel cx'.$i.'"  onclick="javascript:delItemMS(\'mm'.$i.'\');javascript:delItemD(\'cx'.$i.'\')"></div>
				<div class="ydelitem cx'.$i.' mm'.$i.'">
					<span class="addx" onclick="javascript:delItemMS(\'mm'.$i.'\');javascript:delItemD(\'cx'.$i.'\')" >X</span>
					<p>Excluir Arquivo</p>
					<h5>'.$title.'</h5>
					<span class="dbtne">
						<input type="checkbox" class="delItem" id="del'.$i.'" name="delitem" onchange="Joomla.submitbutton(\'course.apply\')" value="'.$atitle.'">
						<label for="del'.$i.'">Excluir</label>
					</span>
					<span class="dbtnc">
						<label class="dbtn" onclick="javascript:delItemMS(\'mm'.$i.'\');javascript:delItemD(\'cx'.$i.'\')">Cancelar</label>
					</span>
				</div>	
				';
			$i++;
		endforeach;
		endif;
		?>

		<script>
			function delItem(id) {
		 var x = document.getElementsByClassName(id);
    	var i;
    	for (i = 0; i < x.length; i++) {
        	x[i].style.opacity = "1";
        	x[i].style.visibility = "visible";
    	}
		}

		function delItemM(id) {
		 var x = document.getElementsByClassName(id);
    	var i;
    	for (i = 0; i < x.length; i++) {
        	x[i].style.marginTop = "-200px";
    	}
		}

		function delItemMS(id) {
		 var x = document.getElementsByClassName(id);
    	var i;
    	for (i = 0; i < x.length; i++) {
        	x[i].style.marginTop = "-300px";
    	}
		}

		function delItemD(id) {
		 var x = document.getElementsByClassName(id);
    	var i;
    	for (i = 0; i < x.length; i++) {
        	x[i].style.opacity = "0";
        	x[i].style.visibility = "hidden";
    	}
		}

		function delItemMInput(id) {
		 var x = document.getElementsByClassName(id);
    	var i;
    	for (i = 0; i < x.length; i++) {
        	if(x[i].required)
        	{
        		x[i].required = false;
        	}
        	else {
        		x[i].required = "true";
        	}
    	}
		}
		</script>

		<?php
		
		return $input;
	}

	public function getItems($table,$name,$id)
	{
		$cdb = JFactory::getDbo();

        $cdb->setQuery("SELECT #__$table.$name FROM #__$table where id = ".$id);

        return $cdb->loadResult();
	}


}