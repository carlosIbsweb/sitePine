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
class JFormFieldSvideos extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'svideos';

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
		
		$doc->addScript(JUri::base(true).'/components/com_s7dpayments/models/fields/js/zepto-1.1.6.js');
		

		$script = 
		'
			jQuery(function($){
				$( document ).ready(function(){

					var addCat = function(){
						var obj = $(".dPaula_videos input, .dPaula_videos textarea").serializeJSON();
    					$("#jform_videos").val(JSON.stringify(obj));
					};

					$( document ).on("hover keyup",".dPaula_videos input, .dPaula_videos textarea, .dPaula_videos h3, .dPaula_videos div", function(){
						addCat();
					});
					
					$( document ).on("keyup hover",".dPaula_videos input, .dPaula_videos textarea, .dPaula_videos h3, .dPaula_videos div",function(){
						if($(this).find(".dtitle").val() != "")
						{
							$(this).find("h3").html($(this).find(".dtitle").val());
						}else{
							$(this).find("h3").html("Título vazio");
						}
					});

					$( document ).on("click",".remove_field", function(){
						addCat();
					});


				});

				$(document).ready(function() {
				    var max_fields      = 10; //maximum input boxes allowed
				    var wrapper         = $(".dPaula_videos"); //Fields wrapper
				    var add_button      = $(".add_dPaula_field"); //Add button ID
				    
				    var x = 1; //initlal text box count
				    $(add_button).click(function(e){ //on add input button click
				        e.preventDefault();
				        if(x < max_fields){ //max input box allowed
				            x++; //text box increment

				            function uniqid() {
							    var ts=String(new Date().getTime()), i = 0, out = \'\';
							    for(i=0;i<ts.length;i+=2) {        
							       out+=Number(ts.substr(i, 2)).toString(36);    
							    }
							    return (\'d\'+out);
							}

	
				            $(wrapper).append(\'<div class="new"><h3>Novo vídeo</h3><div class="evt" style="display:block"><label><label><input type="text" name="course[\'+uniqid(false, true)+\'][title]" placeholder="Título" class="dtitle"/></label><input type="text" name="course[\'+uniqid(false, true)+\'][link]" placeholder="Link" class="dlink"/></label><label><textarea name="course[\'+uniqid(false, true)+\'][description]" placeholder="Descrição" class="ddescription"/></label></div><a href="#" class="remove_field">Remove</a></div>\'); //add input box
				        }
				    });
				    
				    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
				        e.preventDefault(); $(this).parent(\'div\').remove(); x--;
				    });

					$( document ).on("click",".dPaula_videos h3", function(){
				    
						if($(this).next(".evt:visible").length == 1){
					        $(this).next(".evt").hide("slow");
					    }
					    else{
					        $(this).next(".evt").show("slow");
					    }
					});

					
				});

			});
		';

		$doc->addScriptDeclaration($script);



    $output .= '<div class="dPaula_videos">';
    $output .= "<h2>Vídeos</h2>";
    $output .= '<button class="add_dPaula_field btn btn-secondary">Adicionar vídeo</button>';

  	foreach(self::getCourses() as $items)
  	{
  		
  		if($items->id == $_GET['id'])
  		{
  			if(is_array(json_decode($items->videos,true)) && count(json_decode($items->videos,true)) > 0)
  			foreach(json_decode($items->videos) as $arrj)
  			{
  				
  				foreach($arrj as $k=> $item)
  				{
  					if($item->title != '')
  					{
  						$output .= "<div>";
  						$output .= "<h3>".$item->title."</h3>";
  						$output .= "<div class=\"evt\" style=\"display:none\">";
  						$output .= '<label><input type="text" name="course['.$k.'][title]" placeholder="Título" value="'.$item->title.'" class="dtitle" /></label>';
  						$output .= '<label><input type="text" name="course['.$k.'][link]" placeholder="Link" value="'.$item->link.'" class="dlink" /></label>';
  						$output .= '<label><textarea name="course['.$k.'][description]" placeholder="Descrição" class="ddescription" >'.$item->description.'</textarea></label>';
  						$output .= '</div>';
  						$output .= '<a href="#" class="remove_field">Remover</a>';
						$output .= "</div>";
					}
  				}
	  		}else{
	  			$sn = '<p>Não há vídeos</p>';
	  		}
  		}
  	}
    
    $output .= $sn;
	$output .= '</div>';

    echo $output;



		?>

<script src="<?= JUri::base(true).'/components/com_s7dpayments/models/fields/js/jquery.serializejson.js';?>"></script>

</body>
</html>


		<?php
	}

	public static function getCourses()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('title','alias','videos','image','price','id','description','videoslink','videosdescription','arquivos','catid','categorias')));
		$query->from($db->quoteName('#__s7dpayments_courses'));
		isset($_GET['courseId']) ? $query->where($db->quoteName('id') . ' = '. $db->quote($_GET['courseId'])) : null;
		$query->where($db->quoteName('state') . ' = '. $db->quote(1));
		$query->order('id ASC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}

	public static function getCoursesCategories()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('title','id','alias','package','categorias','videos')));
		$query->from($db->quoteName('#__s7dpayments_courses'));
		$query->where($db->quoteName('state') . ' = '. $db->quote(1));
		$query->where($db->quoteName('id') . ' = '. $db->quote($_GET['id']));
		$query->order('ordering ASC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		foreach($results as $items)
		{
			$cats = $items->package;
		}

		return $cats;
	}
}