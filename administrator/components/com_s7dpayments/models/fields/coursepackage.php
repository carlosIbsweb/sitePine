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
class JFormFieldCoursepackage extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'coursepackage';

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
					
					$("#jform_coursePackage").change(function(){
						var arr = [];
						var pega = $("#jform_coursePackage_chzn a");
						var arrs = $.makeArray(pega);

						arrs.each(function(index,el){
							 arr.push($(index).data("option-array-index"));
							
						});
						
						alert(JSON.stringify(arr));
					});

					var modType = $( "#jform_type :checked" ).val();
					if(modType != 1)
					{
						$("#jform_type").parent(".controls").parent(".control-group").next(".control-group").hide();
					}

					$( document ).on("change","#jform_type :checked",function(){
						var modType = $( this ).val();
						if(modType == 0)
						{
							$("#jform_type").parent(".controls").parent(".control-group").next(".control-group").fadeOut("slow");
						}
						if(modType == 1)
						{
							$("#jform_type").parent(".controls").parent(".control-group").next(".control-group").fadeIn("slow");
						}


					});

					var addCat = function(){
						var obj = $(".input_fields_wrap input").serializeJSON();
    					$("#jform_categorias").val(JSON.stringify(obj));
					};

					$( document ).on("hover",".input_fields_wrap input", function(){
						addCat();
					});

					$( document ).on("click",".remove_field", function(){
						addCat();
					});


				});

				$(document).ready(function() {
				    var max_fields      = 10; //maximum input boxes allowed
				    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
				    var add_button      = $(".add_field_button"); //Add button ID
				    
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

	
				            $(wrapper).append(\'<div><input type="text" name="cat[\'+uniqid(false, true)+\'][price]" placeholder="Preço" class="cprice"/><input type="text" name="cat[\'+uniqid(false, true)+\'][categoria]" placeholder="Categoria" class="ccat"/><a href="#" class="remove_field">Remove</a></div>\'); //add input box
				        }
				    });
				    
				    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
				        e.preventDefault(); $(this).parent(\'div\').remove(); x--;
				    })
				});

			});
		';

		$doc->addScriptDeclaration($script);


	$a = explode(",",self::getCoursesCategories());

    $output = '<div class="form-group">';
    $output .= '<select id="ms" multiple="multiple">';


    foreach(self::getCourses() as $its)
    {
    	$cSelected = in_array($its->id,$a) ? 'selected' : '';

  		if($its->id != $_GET['id'] && $its->type != '1')
  		{
  			$output .= '<option value="'.$its->id.'" '.$cSelected.'>'.$its->title. ' <div class="catp">('.self::getCat($its->catid)[0]->title.')</div>'.'</option>';
  		}
  		

    	
    }

    $output .='</select>';
    $output .= '</div>';

    $output .= '<div class="input_fields_wrap">';
    $output .= "<h2>Categorias</h2>";
    $output .= '<button class="add_field_button">Adicionar campo</button>';

  	foreach(self::getCourses() as $items)
  	{
  		if($items->id == $_GET['id'] && is_array(json_decode($items->categorias,true)))
  		{
  			foreach(json_decode($items->categorias) as $arrj)
  			{
  				
  				foreach($arrj as $k=> $item)
  				{
  					if($item->price != '' && $item->categoria != '')
  					{
  						$output .= "<div>";
  						$output .= '<input type="text" name="cat['.$k.'][price]" placeholder="Preço" value="'.$item->price.'" class="cprice" />';
  						$output .= '<input type="text" name="cat['.$k.'][categoria]" placeholder="Categoria" value="'.$item->categoria.'" class="ccat" />';
  						$output .= '<a href="#" class="remove_field">Remove</a>';
						$output .= "</div>";
					}
  				}
	  		}
  		}
  	}
    
	$output .= '</div>';

    echo $output;



		?>


<script src="<?= JUri::base(true).'/components/com_s7dpayments/assets/js/multiple-select.js';?>"></script>
<script src="<?= JUri::base(true).'/components/com_s7dpayments/models/fields/js/jquery.serializejson.js';?>"></script>
<script>
    jQuery(function($) {
        $('#ms').change(function() {
                 $('#jform_package').val($(this).val());
        });
    });
</script>

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
		$query->select($db->quoteName(array('title','id','alias','package','categorias')));
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

	public static function getCat($id = null)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('title','alias')));
		$query->from($db->quoteName('#__s7dpayments_categories'));
		$query->where($db->quoteName('state') . ' = '. $db->quote(1));
		$query->where($db->quoteName('id') . ' = '. $db->quote($id));
		$query->order('id ASC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}
}