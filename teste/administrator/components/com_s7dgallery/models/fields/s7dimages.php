<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_S7dlv
 * @author     carlos <carlos@ibsweb.com.br>
 * @copyright  2018 carlos
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 *
 * @since  1.6
 */
class JFormFieldS7dimages extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'S7dimages';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 *
	 * @since    1.6
	 */
	protected function getInput()
	{
		// Get the document object.
		$doc = JFactory::getDocument();
		$base = JUri::base(true);

		$input  = JFactory::getApplication()->input;
        $id = $input->get('id', NULL, 'INT');

        $folder = JUri::root(true).'/images/s7dgallery/gal-'.$id.'/thumbs/';

		$doc->addScript($base.'/components/com_s7dgallery/assets/js/ajaxUpload.js?'.uniqid());
		$doc->addStyleSheet(JUri::root().'media/com_s7dgallery/css/s7dcolumns.css');
		
		$output = [];
		$output[] = '<div class="sg-image">'; 
		$output[] = '<div class="sg-image-inner">';
		$output[] = '<div class="sg-image-header">';
		$output[] = '<div class="sgButtonsLeft"><label for="sgUp" class="btnUpH"><i class="la la-upload"></i>Carregar</label><span class="imgCount"></span><div class="sg-imgsearch"><input type="text" class="sg-search" placeholder="Pesquisar"/></div></div>';
		$output[] = '<div class="sgButtons sgButtonsRight">';
		$output[] = '<div class="sgDelete sgBtn"  data-id="'.$id.'">Excluir</div>';
		$output[] = '</div>';
		$output[] = '</div>';
		$output[] = '<div class="sg-images">';
		$output[] = '<div class="sg-inner-images">';
		$output[] = '<div class="sgUpImage s7d-col-md-2 s7d-col-sm-6">';
			if(isset($id)){
				$output[] = '<div class="sg-images-inner sgUp">';
				$output[] = '<input id="sgUp" class="sgBtnUpload" type="file" title="'.JText::_('COM_S7DGALLERY_SGUPLOAD_TITLE').'" multiple="true" data-id="'.$id.'">';
				$output[] = '<label for="sgUp"><i class="la la-cloud-upload"></i><span>Largar imagens aqui<br> <strong>ou selecionar ficheiros</strong></span></label>';
				$output[] = '</div>';
			}else{
				$output[] = '<div class="sg-noid"><i class="la la-meh-o"></i><p>'.JText::_('COM_S7DGALLERY_NOID').'</p></div>';
			}
		$output[] ='</div>';
		$output[] = '<ul id="sortable">';

		$output[] = '</ul>';
		$output[] = '</div>';
		$output[] = '</div>';
		$output[] = '</div>';
		$output[] = '</div>';

		$output[] = '<textarea style="display:none" name="'.$this->name.'" id="sgtimages">'.trim($this->value).'</textarea>';

		return implode("",$output);

	}

	protected function getImages($id)
	{
		// Build the query for the table list.
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT images'
			. ' FROM #__s7dgallery_albums'
			. ' WHERE id = ' . (int) $id
		);
		
		$result = $db->loadResult();

		/*echo '<div class="im">';
		if(is_array(json_decode($result,true))){

		foreach(json_decode($result) as $k=> $image)
		{	
			echo '<li id="'.$image->id.'">'.$image->name.'<input name="lvid[]" type="checkbox" value="'.$image->id.'"></li>';
		}	
	}
		echo '</div>';*/

		return $result;
	}

	public function getLabel()
	{
        return false;
	}
}
